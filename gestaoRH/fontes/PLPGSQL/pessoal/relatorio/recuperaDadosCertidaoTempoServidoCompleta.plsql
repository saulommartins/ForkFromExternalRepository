/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
 * PL recuperaDadosCertidaoTempoServidoCompleta
 * Data de Criação   : 08/09/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 $Id:$
 */

CREATE OR REPLACE FUNCTION recuperaDadosCertidaoTempoServidoCompleta(varchar,varchar,integer,boolean,varchar) returns SETOF colunasDadosCertidaoTempoServidoCompleta AS $$
DECLARE
    stTipoFiltro        ALIAS FOR $1;
    stCodigos           ALIAS FOR $2;
    inCodAtributo       ALIAS FOR $3;
    boArray             ALIAS FOR $4;
    stEntidade          ALIAS FOR $5;
    stSql               VARCHAR;
    stExercicio         VARCHAR;
    reRegistro          RECORD;
    reCursor            RECORD;
    crCursor            REFCURSOR;
    rwRetorno           colunasDadosCertidaoTempoServidoCompleta%ROWTYPE;
BEGIN
    SELECT max(valor) as exercicio
      into stExercicio
      FROM administracao.configuracao 
     WHERE parametro = 'ano_exercicio';

    stSql := '
SELECT contrato.*
     , sw_cgm.nom_cgm
     , to_char(sw_cgm_pessoa_fisica.dt_nascimento, ''dd/mm/yyyy'')  as dt_nascimento
     , case when sw_cgm_pessoa_fisica.sexo = ''f'' THEN ''Feminino''
       ELSE ''Masculino'' end as sexo
     , sw_cgm_pessoa_fisica.rg
     , sw_cgm_pessoa_fisica.dt_emissao_rg
     , sw_cgm_pessoa_fisica.orgao_emissor as orgao_emissor_rg
     , sw_cgm_pessoa_fisica.servidor_pis_pasep
     , publico.mascara_cpf_cnpj(sw_cgm_pessoa_fisica.cpf,''cpf'') as cpf
     , (SELECT nacionalidade FROM sw_pais WHERE cod_pais = sw_cgm_pessoa_fisica.cod_nacionalidade) as nacionalidade     
     , (SELECT descricao FROM sw_escolaridade WHERE cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade) as escolaridade
     , servidor.cod_servidor
     , servidor.nome_pai
     , servidor.nome_mae
     , servidor.nr_titulo_eleitor
     , servidor.zona_titulo
     , servidor.secao_titulo
     , (SELECT nom_estado FROM cse.estado_civil WHERE cod_estado = servidor.cod_estado_civil) as nom_estado
     , (SELECT nom_municipio FROM sw_municipio WHERE cod_municipio = servidor.cod_municipio AND cod_uf = servidor.cod_uf) as nom_municipio
     , (SELECT sigla_uf FROM sw_uf WHERE cod_uf = servidor.cod_uf) as sigla_uf     
     , norma.exercicio
     , norma.num_norma
     , norma.nom_norma
     , contrato_servidor.cod_tipo_admissao
     , (SELECT descricao FROM pessoal'|| stEntidade ||'.tipo_admissao WHERE cod_tipo_admissao = contrato_servidor.cod_tipo_admissao) as tipo_admissao     
     , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) as cargo
     , (SELECT descricao FROM pessoal'|| stEntidade ||'.regime WHERE cod_regime = contrato_servidor.cod_regime) as regime
     , (SELECT descricao FROM pessoal'|| stEntidade ||'.sub_divisao WHERE cod_sub_divisao = contrato_servidor.cod_sub_divisao) as sub_divisao
     , contrato_servidor_orgao.cod_orgao
     , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) as descricao_orgao
  FROM pessoal'|| stEntidade ||'.contrato
     , pessoal'|| stEntidade ||'.contrato_servidor
     , pessoal'|| stEntidade ||'.contrato_servidor_orgao
     , (SELECT cod_contrato
             , max(timestamp) as timestamp
          FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
        GROUP BY cod_contrato) as max_contrato_servidor_orgao
     , organograma.orgao
     , pessoal'|| stEntidade ||'.servidor_contrato_servidor
     , pessoal'|| stEntidade ||'.servidor
     , sw_cgm
     , sw_cgm_pessoa_fisica     
     , normas.norma';
     
    IF stTipoFiltro = 'local' THEN     
        stSql := stSql || '     
         , pessoal'|| stEntidade ||'.contrato_servidor_local
         , (SELECT cod_contrato
                  , max(timestamp) as timestamp
               FROM pessoal'|| stEntidade ||'.contrato_servidor_local
            GROUP BY cod_contrato) as max_contrato_servidor_local
         , organograma.local';
    END IF;
    IF stTipoFiltro = 'atributo_servidor' THEN
        stSql := stSql || '     
        , pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
        , (SELECT cod_contrato
                , cod_atributo
                , max(timestamp) as timestamp                
             FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor
           GROUP BY cod_contrato
                  , cod_atributo) as max_atributo_contrato_servidor_valor ';
        IF boArray is true THEN                          
            stSql := stSql || ' , administracao.atributo_valor_padrao ';
        END IF;
    END IF;
     
    stSql := stSql || '     
 WHERE contrato.cod_contrato = servidor_contrato_servidor.cod_contrato
   AND servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
   AND contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato
   AND contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato
   AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp
   AND contrato_servidor_orgao.cod_orgao = orgao.cod_orgao
   AND servidor.numcgm = sw_cgm.numcgm
   AND servidor.numcgm = sw_cgm_pessoa_fisica.numcgm
   AND contrato_servidor.cod_norma = norma.cod_norma
   AND NOT EXISTS (   SELECT 1                                                                                   
                        FROM pessoal'|| stEntidade ||'.aposentadoria                                     
                       WHERE aposentadoria.cod_contrato = servidor_contrato_servidor.cod_contrato                                  
                         AND NOT EXISTS (SELECT 1                                                                
                                           FROM pessoal'|| stEntidade ||'.aposentadoria_excluida          
                                          WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato 
                                            AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp))';
   
    IF stTipoFiltro = 'geral' THEN   
        stSql := stSql || ' ORDER BY nom_cgm';
    END IF;
   
    IF stTipoFiltro = 'contrato_todos' or stTipoFiltro = 'cgm_contrato_todos' THEN
        stSql := stSql || '  AND contrato.cod_contrato IN ('|| stCodigos ||')';       
    END IF;
    IF stTipoFiltro = 'lotacao' THEN
        stSql := stSql || '  AND contrato_servidor_orgao.cod_orgao IN ('|| stCodigos ||')
        ORDER BY descricao_orgao
               , nom_cgm ';       
    END IF;
    IF stTipoFiltro = 'local' THEN
        stSql := stSql || ' AND contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato
                            AND contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                            AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp    
                            AND contrato_servidor_local.cod_local IN ('|| stCodigos ||')
                            AND contrato_servidor_local.cod_local = local.cod_local
        ORDER BY local.descricao
               , nom_cgm';       
    END IF;    
    IF stTipoFiltro = 'atributo_servidor' THEN
        stSql := stSql || ' AND contrato_servidor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato
                            AND atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
                            AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo
                            AND atributo_contrato_servidor_valor.timestamp = max_atributo_contrato_servidor_valor.timestamp    
                            AND atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo;
        IF boArray is true THEN
            stSql := stSql || ' AND atributo_contrato_servidor_valor.valor IN ('|| stCodigos ||')
                                AND atributo_contrato_servidor_valor.cod_modulo = atributo_valor_padrao.cod_modulo
                                AND atributo_contrato_servidor_valor.cod_cadastro = atributo_valor_padrao.cod_cadastro
                                AND atributo_contrato_servidor_valor.cod_atributo = atributo_valor_padrao.cod_atributo
                                AND atributo_contrato_servidor_valor.valor = atributo_valor_padrao.cod_valor
            ORDER BY atributo_valor_padrao.valor_padrao
                   , nom_cgm
            ';           
        ELSE
            stSql := stSql || ' AND atributo_contrato_servidor_valor.valor = '|| quote_literal(stCodigos) ||'
            ORDER BY atributo_contrato_servidor_valor.valor
                   , nom_cgm
            ';                       
        END IF;                                                              
    END IF;    
   
   
    FOR reRegistro IN  EXECUTE stSql LOOP   
        rwRetorno.cod_contrato         := reRegistro.cod_contrato              ;
        rwRetorno.registro             := reRegistro.registro                  ;
        rwRetorno.nom_cgm              := reRegistro.nom_cgm                   ;
        rwRetorno.dt_nascimento        := reRegistro.dt_nascimento             ;
        rwRetorno.sexo                 := reRegistro.sexo                      ;
        rwRetorno.rg                   := reRegistro.rg                        ;
        rwRetorno.dt_emissao_rg        := reRegistro.dt_emissao_rg             ;
        rwRetorno.orgao_emissor_rg     := reRegistro.orgao_emissor_rg          ;
        rwRetorno.servidor_pis_pasep   := reRegistro.servidor_pis_pasep        ;
        rwRetorno.cpf                  := reRegistro.cpf                       ;
        rwRetorno.nacionalidade        := reRegistro.nacionalidade             ;
        rwRetorno.escolaridade         := reRegistro.escolaridade              ;
        rwRetorno.nome_pai             := reRegistro.nome_pai                  ;
        rwRetorno.nome_mae             := reRegistro.nome_mae                  ;
        rwRetorno.nr_titulo_eleitor    := reRegistro.nr_titulo_eleitor         ;
        rwRetorno.zona_titulo          := reRegistro.zona_titulo               ;
        rwRetorno.secao_titulo         := reRegistro.secao_titulo              ;
        rwRetorno.nom_estado           := reRegistro.nom_estado                ;
        rwRetorno.nom_municipio        := reRegistro.nom_municipio             ;
        rwRetorno.exercicio            := reRegistro.exercicio                 ;
        rwRetorno.num_norma            := reRegistro.num_norma                 ;
        rwRetorno.nom_norma            := reRegistro.nom_norma                 ;
        rwRetorno.cod_tipo_admissao    := reRegistro.cod_tipo_admissao         ;
        rwRetorno.tipo_admissao        := reRegistro.tipo_admissao             ;
        rwRetorno.cargo                := reRegistro.cargo                     ;
        rwRetorno.regime               := reRegistro.regime                    ;
        rwRetorno.sub_divisao          := reRegistro.sub_divisao               ;        
        rwRetorno.sigla_uf             := reRegistro.sigla_uf                  ;        
        rwRetorno.descricao_orgao      := reRegistro.descricao_orgao           ;        
                
        stSql := 'SELECT cid.sigla as sigla_cid
                       , cid.descricao as descricao_cid
                    FROM pessoal'|| stEntidade ||'.servidor_cid
                       , (SELECT cod_servidor
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.servidor_cid
                          GROUP BY cod_servidor) as max_servidor_cid
                       , pessoal'|| stEntidade ||'.cid
                   WHERE servidor_cid.cod_servidor = max_servidor_cid.cod_servidor
                     AND servidor_cid.timestamp = max_servidor_cid.timestamp
                     AND servidor_cid.cod_cid = cid.cod_cid
                     AND servidor_cid.cod_servidor = '|| reRegistro.cod_servidor ;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;                
        rwRetorno.sigla_cid            := reCursor.sigla_cid                 ;
        rwRetorno.descricao_cid        := reCursor.descricao_cid             ;

        stSql := 'SELECT ctps.numero
                       , ctps.serie
                       , ctps.orgao_expedidor
                       , to_char(ctps.dt_emissao,''dd/mm/yyyy'') as dt_emissao
                       , (SELECT sigla_uf FROM sw_uf WHERE cod_uf = ctps.uf_expedicao) as sigla_uf_ctps
                       , servidor_ctps.cod_servidor
                    FROM pessoal'|| stEntidade ||'.servidor_ctps
                       , (SELECT cod_servidor
                               , max(cod_ctps) as cod_ctps
                            FROM pessoal'|| stEntidade ||'.servidor_ctps
                          GROUP BY cod_servidor) as max_servidor_ctps
                       , pessoal'|| stEntidade ||'.ctps
                   WHERE servidor_ctps.cod_ctps = ctps.cod_ctps
                     AND servidor_ctps.cod_ctps = max_servidor_ctps.cod_ctps
                     AND servidor_ctps.cod_servidor = max_servidor_ctps.cod_servidor
                     AND servidor_ctps.cod_servidor = '|| reRegistro.cod_servidor ;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;
        rwRetorno.numero               := reCursor.numero                    ;
        rwRetorno.serie                := reCursor.serie                     ;
        rwRetorno.orgao_expedidor      := reCursor.orgao_expedidor           ;
        rwRetorno.dt_emissao           := reCursor.dt_emissao                ;
        rwRetorno.sigla_uf_ctps        := reCursor.sigla_uf_ctps             ;

        stSql := 'SELECT to_char(dt_pis_pasep,''dd/mm/yyyy'') as dt_pis_pasep
                    FROM pessoal'|| stEntidade ||'.servidor_pis_pasep
                       , (SELECT cod_servidor
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.servidor_pis_pasep
                          GROUP BY cod_servidor) as max_servidor_pis_pasep
                   WHERE servidor_pis_pasep.cod_servidor = max_servidor_pis_pasep.cod_servidor
                     AND servidor_pis_pasep.timestamp = max_servidor_pis_pasep.timestamp         
                     AND servidor_pis_pasep.cod_servidor = '|| reRegistro.cod_servidor;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;
        rwRetorno.dt_pis_pasep         := reCursor.dt_pis_pasep              ;

        stSql := 'SELECT servidor_reservista.*
                    FROM pessoal'|| stEntidade ||'.servidor_reservista
                   WHERE cod_servidor = '|| reRegistro.cod_servidor;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;  
        rwRetorno.nr_carteira_res      := reCursor.nr_carteira_res           ;
        rwRetorno.cat_reservista       := reCursor.cat_reservista            ;
        rwRetorno.origem_reservista    := reCursor.origem_reservista         ;

        stSql := 'SELECT to_char(dt_posse,''dd/mm/yyyy'') as dt_posse
                       , to_char(dt_nomeacao,''dd/mm/yyyy'') as dt_nomeacao
                       , to_char(dt_admissao,''dd/mm/yyyy'') as dt_admissao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                          GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                   WHERE contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
                     AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp         
                     AND contrato_servidor_nomeacao_posse.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;        
        rwRetorno.dt_nomeacao          := reCursor.dt_nomeacao               ;
        rwRetorno.dt_posse             := reCursor.dt_posse                  ;
        rwRetorno.dt_admissao          := reCursor.dt_admissao               ;

        
        stSql := 'SELECT num_ocorrencia
                       , descricao as ocorrencia
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_ocorrencia
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_ocorrencia
                          GROUP BY cod_contrato) as max_contrato_servidor_ocorrencia
                       , pessoal'|| stEntidade ||'.ocorrencia
                   WHERE contrato_servidor_ocorrencia.cod_contrato = max_contrato_servidor_ocorrencia.cod_contrato
                     AND contrato_servidor_ocorrencia.timestamp = max_contrato_servidor_ocorrencia.timestamp         
                     AND contrato_servidor_ocorrencia.cod_ocorrencia = ocorrencia.cod_ocorrencia
                     AND contrato_servidor_ocorrencia.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;        
        rwRetorno.num_ocorrencia       := reCursor.num_ocorrencia            ;
        rwRetorno.ocorrencia           := reCursor.ocorrencia                ;
        
        stSql := 'SELECT descricao as especialidade
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_cargo
                       , pessoal'|| stEntidade ||'.especialidade
                   WHERE contrato_servidor_especialidade_cargo.cod_especialidade = especialidade.cod_especialidade
                     AND contrato_servidor_especialidade_cargo.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;           
        rwRetorno.especialidade        := reCursor.especialidade             ;

        stSql := 'SELECT descricao as funcao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_funcao
                          GROUP BY cod_contrato) as max_contrato_servidor_funcao
                       , pessoal'|| stEntidade ||'.cargo
                   WHERE contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato
                     AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp         
                     AND contrato_servidor_funcao.cod_cargo = cargo.cod_cargo
                     AND contrato_servidor_funcao.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;
        rwRetorno.funcao               := reCursor.funcao                    ;

        stSql := 'SELECT descricao as regime_funcao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_regime_funcao
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_regime_funcao
                          GROUP BY cod_contrato) as max_contrato_servidor_regime_funcao
                       , pessoal'|| stEntidade ||'.regime
                   WHERE contrato_servidor_regime_funcao.cod_contrato = max_contrato_servidor_regime_funcao.cod_contrato
                     AND contrato_servidor_regime_funcao.timestamp = max_contrato_servidor_regime_funcao.timestamp         
                     AND contrato_servidor_regime_funcao.cod_regime = regime.cod_regime
                     AND contrato_servidor_regime_funcao.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;
        rwRetorno.regime_funcao        := reCursor.regime_funcao             ;

        stSql := 'SELECT descricao as sub_divisao_funcao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_sub_divisao_funcao
                          GROUP BY cod_contrato) as max_contrato_servidor_sub_divisao_funcao
                       , pessoal'|| stEntidade ||'.sub_divisao
                   WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_contrato_servidor_sub_divisao_funcao.cod_contrato
                     AND contrato_servidor_sub_divisao_funcao.timestamp = max_contrato_servidor_sub_divisao_funcao.timestamp         
                     AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = sub_divisao.cod_sub_divisao
                     AND contrato_servidor_sub_divisao_funcao.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;
        rwRetorno.sub_divisao_funcao   := reCursor.sub_divisao_funcao        ;

        stSql := 'SELECT descricao as especialidade_funcao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_especialidade_funcao
                          GROUP BY cod_contrato) as max_contrato_servidor_especialidade_funcao
                       , pessoal'|| stEntidade ||'.especialidade
                   WHERE contrato_servidor_especialidade_funcao.cod_contrato = max_contrato_servidor_especialidade_funcao.cod_contrato
                     AND contrato_servidor_especialidade_funcao.timestamp = max_contrato_servidor_especialidade_funcao.timestamp         
                     AND contrato_servidor_especialidade_funcao.cod_especialidade = especialidade.cod_especialidade
                     AND contrato_servidor_especialidade_funcao.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;   
        rwRetorno.especialidade_funcao := reCursor.especialidade_funcao      ;

        stSql := 'SELECT salario                       
                       , horas_mensais
                       , horas_semanais
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_salario
                          GROUP BY cod_contrato) as max_contrato_servidor_salario
                   WHERE contrato_servidor_salario.cod_contrato = max_contrato_servidor_salario.cod_contrato
                     AND contrato_servidor_salario.timestamp = max_contrato_servidor_salario.timestamp         
                     AND contrato_servidor_salario.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;         
        rwRetorno.horas_mensais        := reCursor.horas_mensais             ;
        rwRetorno.horas_semanais       := reCursor.horas_semanais            ;
        rwRetorno.salario              := reCursor.salario                   ;

        stSql := 'SELECT descricao as padrao                       
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_padrao
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_padrao
                          GROUP BY cod_contrato) as max_contrato_servidor_padrao
                       , folhapagamento'|| stEntidade ||'.padrao
                   WHERE contrato_servidor_padrao.cod_contrato = max_contrato_servidor_padrao.cod_contrato
                     AND contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp         
                     AND contrato_servidor_padrao.cod_padrao = padrao.cod_padrao
                     AND contrato_servidor_padrao.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;         
        rwRetorno.padrao               := reCursor.padrao                    ;

        stSql := '
        SELECT nivel_padrao_nivel.descricao AS progressao
          FROM pessoal'|| stEntidade ||'.contrato_servidor_nivel_padrao
          JOIN folhapagamento'|| stEntidade ||'.nivel_padrao
            ON nivel_padrao.cod_nivel_padrao = contrato_servidor_nivel_padrao.cod_nivel_padrao
          JOIN folhapagamento'|| stEntidade ||'.nivel_padrao_nivel
            ON nivel_padrao_nivel.cod_nivel_padrao = nivel_padrao.cod_nivel_padrao
             , (  SELECT cod_contrato                                    
                       , max(timestamp) as timestamp                     
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_nivel_padrao          
                GROUP BY cod_contrato) as max_nivel_padrao
         WHERE contrato_servidor_nivel_padrao.cod_contrato = max_nivel_padrao.cod_contrato 
           AND contrato_servidor_nivel_padrao.timestamp    = max_nivel_padrao.timestamp
           AND contrato_servidor_nivel_padrao.cod_contrato = '|| reRegistro.cod_contrato;

        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;         
        rwRetorno.progressao := reCursor.progressao;
        
        stSql := 'SELECT organograma.fn_consulta_orgao(orgao_nivel.cod_organograma, orgao_nivel.cod_orgao) AS orgao
                    FROM organograma.orgao_nivel
                   WHERE orgao_nivel.cod_orgao = '|| reRegistro.cod_orgao;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor; 
        rwRetorno.orgao                := reCursor.orgao                     ;
                
        stSql := 'SELECT descricao as local                       
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                       , (SELECT cod_contrato
                               , max(timestamp) as timestamp
                            FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                          GROUP BY cod_contrato) as max_contrato_servidor_local
                       , organograma.local
                   WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                     AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp         
                     AND contrato_servidor_local.cod_local = local.cod_local
                     AND contrato_servidor_local.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;         
        rwRetorno.local                := reCursor.local                     ;   
        
        stSql := 'SELECT to_char(dt_rescisao,''dd/mm/yyyy'') as dt_rescisao
                       , num_causa
                       , causa_rescisao.descricao
                    FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                       , pessoal'|| stEntidade ||'.caso_causa
                       , pessoal'|| stEntidade ||'.causa_rescisao
                   WHERE contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa
                     AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao
                     AND contrato_servidor_caso_causa.cod_contrato = '|| reRegistro.cod_contrato;
        OPEN crCursor FOR EXECUTE stSql;
            FETCH crCursor INTO reCursor;
        CLOSE crCursor;         
        rwRetorno.dt_rescisao                := reCursor.dt_rescisao               ;           
        rwRetorno.num_causa                  := reCursor.num_causa                 ;           
        rwRetorno.descricao_causa            := reCursor.descricao                 ;           
        RETURN NEXT rwRetorno;
    END LOOP;
END;
$$ language 'plpgsql';   
