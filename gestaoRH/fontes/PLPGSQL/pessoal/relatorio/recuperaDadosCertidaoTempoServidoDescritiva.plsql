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
CREATE OR REPLACE FUNCTION recuperaDadosCertidaoTempoServidoDescritiva(VARCHAR,VARCHAR,INTEGER,BOOLEAN,VARCHAR,VARCHAR,INTEGER,DATE,DATE) RETURNS SETOF colunasDadosCertidaoTempoServidoDescritiva AS $$
DECLARE
    stTipoFiltro        ALIAS FOR $1;
    stCodigos           ALIAS FOR $2;
    inCodAtributo       ALIAS FOR $3;
    boArray             ALIAS FOR $4;
    stEntidade          ALIAS FOR $5;
    stExercicio         ALIAS FOR $6;
    inCodEntidade       ALIAS FOR $7;
    dtPeriodoInicial    ALIAS FOR $8;
    dtPeriodoFinal      ALIAS FOR $9;
    dtPeriodoInicialAux DATE;
    dtPeriodoFinalAux   DATE;
    stSql               VARCHAR;
    stSqlAux            VARCHAR;
    stEmissao           VARCHAR;
    stNomEntidade       VARCHAR;
    stNomeCidade        VARCHAR;
    stDataRescicao      VARCHAR;
    stEfetividade       VARCHAR:='';
    arMes               VARCHAR[];
    inDias              INTEGER;
    inTotal             INTEGER:=0;
    dtRescisao          DATE;
    reRegistro          RECORD;
    reEfetividade       RECORD;
    rwRetorno           colunasDadosCertidaoTempoServidoDescritiva%ROWTYPE;
BEGIN
    stSql := 'SELECT sw_cgm.nom_cgm
                   , contrato.registro
                   , contrato.cod_contrato
                   , regime.descricao as regime
                   , (CASE regime.cod_regime
                        WHEN 2 THEN ''nomeado''
                        WHEN 1 THEN ''admitido'' 
                      END) as regime_formatado
                    , (CASE regime.cod_regime
                        WHEN 2 THEN to_char(dt_nomeacao,''dd/mm/yyyy'')
                        WHEN 1 THEN to_char(dt_admissao,''dd/mm/yyyy'')
                      END) as dt_regime
                   , cargo.descricao as cargo
                   , recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) as lotacao
                   , (CASE (SELECT valor FROM administracao.configuracao WHERE parametro = ''dtContagemInicial'' AND cod_modulo = 22  AND exercicio = '|| quote_literal(stExercicio) ||')
                         WHEN ''dtAdmissao'' THEN contrato_servidor_nomeacao_posse.dt_admissao
                         WHEN ''dtPosse''    THEN contrato_servidor_nomeacao_posse.dt_posse
                         WHEN ''dtNomeacao'' THEN contrato_servidor_nomeacao_posse.dt_nomeacao
                      END ) as dt_contagem_tempo
                FROM pessoal'|| stEntidade ||'.contrato
                   , pessoal'|| stEntidade ||'.contrato_servidor
                   , pessoal'|| stEntidade ||'.contrato_servidor_orgao
                   , (SELECT cod_contrato
                           , max(timestamp) as timestamp
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                    GROUP BY cod_contrato) as max_contrato_servidor_orgao
                  , pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                  , (SELECT cod_contrato
                          , max(timestamp) as timestamp
                      FROM pessoal'|| stEntidade ||'.contrato_servidor_nomeacao_posse
                        GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse
                  , organograma.orgao
                  , pessoal'|| stEntidade ||'.cargo
                  , pessoal'|| stEntidade ||'.regime
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
   AND contrato_servidor.cod_regime = regime.cod_regime
   AND contrato_servidor.cod_cargo = cargo.cod_cargo
   AND contrato.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato
   AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp

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
        ORDER BY lotacao
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

    --Busca nome cidade
    stSqlAux :='SELECT sw_municipio.nom_municipio
                 FROM (SELECT configuracao.valor as cod_municipio
                         FROM administracao.configuracao
                        WHERE parametro = ''cod_municipio''
                          AND exercicio = '|| quote_literal(stExercicio) ||') as municipio
                    , (SELECT configuracao.valor as cod_uf
                         FROM administracao.configuracao
                        WHERE parametro = ''cod_uf''
                          AND exercicio = '|| quote_literal(stExercicio) ||') as uf
                   , sw_municipio
               WHERE municipio.cod_municipio::integer = sw_municipio.cod_municipio
                 AND uf.cod_uf::integer = sw_municipio.cod_uf';

    stNomeCidade := selectIntoVarchar(stSqlAux);

    --Busca a Entidade
    stSqlAux :='SELECT nom_cgm
                 FROM orcamento.entidade                   
                    , sw_cgm                               
                WHERE entidade.numcgm = sw_cgm.numcgm      
                  AND entidade.cod_entidade = '|| inCodEntidade ||'
                  AND entidade.exercicio = '|| quote_literal(stExercicio) ||' ';

    stNomEntidade := selectIntoVarchar(stSqlAux);
    stEmissao := selectIntoVarchar('SELECT publico.fn_data_extenso(now()::date)');
   
    FOR reRegistro IN  EXECUTE stSql LOOP
        rwRetorno.nom_cgm              := reRegistro.nom_cgm;
        rwRetorno.registro             := reRegistro.registro;
        rwRetorno.regime               := reRegistro.regime;
        rwRetorno.regime_formatado     := reRegistro.regime_formatado;
        rwRetorno.cargo                := reRegistro.cargo;
        rwRetorno.dt_regime            := reRegistro.dt_regime;
        rwRetorno.nom_cgm_entidade     := stNomEntidade;     
        rwRetorno.dt_emissao           := stEmissao;
        rwRetorno.nome_cidade          := stNomeCidade;
        rwRetorno.lotacao              := reRegistro.lotacao;

        -- Valida periodo inicial e final, para ver se existe resciï¿½ï¿½o e pegar o periodo correto de contagem inicial

        stSqlAux := 'SELECT dt_rescisao
                       FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                      WHERE cod_contrato = '|| reRegistro.cod_contrato;

        dtRescisao := selectIntoVarchar(stSqlAux)::DATE;
    
        IF dtRescisao IS NOT NULL THEN
            dtPeriodoFinalAux := dtRescisao;
        ELSE
            dtPeriodoFinalAux := dtPeriodoFinal;
        END IF;

        IF reRegistro.dt_contagem_tempo > dtPeriodoInicial THEN
            dtPeriodoInicialAux := reRegistro.dt_contagem_tempo;
        ELSE
            dtPeriodoInicialAux := dtPeriodoInicial;
        END IF;
                                                                                                              
        --Busca a previdencia do contrato
        stSqlAux = '  SELECT previdencia_previdencia.descricao
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                  INNER JOIN ( SELECT cod_contrato
                                    , max(timestamp) as timestamp
                                 FROM pessoal'|| stEntidade ||'.contrato_servidor_previdencia
                             GROUP BY cod_contrato) as max_contrato_servidor_previdencia
                          ON contrato_servidor_previdencia.cod_contrato = max_contrato_servidor_previdencia.cod_contrato
                         AND contrato_servidor_previdencia.timestamp = max_contrato_servidor_previdencia.timestamp
                  INNER JOIN folhapagamento'|| stEntidade ||'.previdencia_previdencia
                          ON previdencia_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                  INNER JOIN ( SELECT cod_previdencia
                                    , max(timestamp) as timestamp
                                 FROM folhapagamento'|| stEntidade ||'.previdencia_previdencia
                             GROUP BY cod_previdencia) as max_previdencia_previdencia
                          ON previdencia_previdencia.cod_previdencia = max_previdencia_previdencia.cod_previdencia
                         AND previdencia_previdencia.timestamp = max_previdencia_previdencia.timestamp
                       WHERE bo_excluido = false
                         AND previdencia_previdencia.tipo_previdencia = ''o''
                         AND contrato_servidor_previdencia.cod_contrato = '|| reRegistro.cod_contrato;

        rwRetorno.previdencia := selectIntoVarchar(stSqlAux);

        -- Busca exoneração_rescição
        stSqlAux = 'SELECT to_char(dt_rescisao, ''dd/mm/yyyy'') as dt_rescisao
                      FROM pessoal'|| stEntidade ||'.contrato_servidor_caso_causa
                     WHERE cod_contrato = '|| reRegistro.cod_contrato; 

        stDataRescicao := COALESCE(selectIntoVarchar(stSqlAux),'');
        IF trim(stDataRescicao) != '' THEN
            IF reRegistro.regime = 'CLT' THEN
                rwRetorno.exonerado_rescindido := ', rescindido em '|| trim(stDataRescicao);
            ELSE
                rwRetorno.exonerado_rescindido := ', exonerado em '|| trim(stDataRescicao);
            END IF;
        ELSE
            rwRetorno.exonerado_rescindido := '';
        END IF;

        -- Busca Efetividade
        stSql := 'SELECT * FROM recuperaDadosGradeEfetividade('|| reRegistro.cod_contrato ||', '|| quote_literal(stExercicio) ||', '|| quote_literal(dtPeriodoInicialAux) ||','|| quote_literal(dtPeriodoFinalAux) ||', '|| quote_literal(stEntidade) ||')';

        stEfetividade := '';
        inTotal := 0;
        FOR reEfetividade IN execute stSql LOOP
            stEfetividade := stEfetividade  ||'No ano de '|| reEfetividade.ano ||', foi efetivo ';
            IF reEfetividade.jan != '-' THEN        
                arMes := string_to_array(reEfetividade.jan,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Janeiro, ';
            END IF;
            IF reEfetividade.fev != '-' THEN        
                arMes := string_to_array(reEfetividade.fev,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Fevereiro, ';
            END IF;
            IF reEfetividade.mar != '-' THEN        
                arMes := string_to_array(reEfetividade.mar,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Março, ';
            END IF;
            IF reEfetividade.abr != '-' THEN        
                arMes := string_to_array(reEfetividade.abr,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Abril, ';
            END IF;
            IF reEfetividade.mai != '-' THEN        
                arMes := string_to_array(reEfetividade.mai,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Maio, ';
            END IF;
            IF reEfetividade.jun != '-' THEN        
                arMes := string_to_array(reEfetividade.jun,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Junho, ';
            END IF;
            IF reEfetividade.jul != '-' THEN        
                arMes := string_to_array(reEfetividade.jul,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Julho, ';
            END IF;
            IF reEfetividade.ago != '-' THEN        
                arMes := string_to_array(reEfetividade.ago,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Agosto, ';
            END IF;
            IF reEfetividade.set != '-' THEN        
                arMes := string_to_array(reEfetividade.set,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Setembro, ';
            END IF;
            IF reEfetividade.out != '-' THEN        
                arMes := string_to_array(reEfetividade.out,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Outubro, ';
            END IF;
            IF reEfetividade.nov != '-' THEN        
                arMes := string_to_array(reEfetividade.nov,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Novembro, ';
            END IF;
            IF reEfetividade.dez != '-' THEN
                arMes := string_to_array(reEfetividade.dez,' ');
                inDias := replace(arMes[1],'E','')::INTEGER;        
                inTotal := inTotal + inDias;
                stEfetividade := stEfetividade ||inDias ||' dias em Dezembro, ';
            END IF;
        END LOOP;
        
        stEfetividade := stEfetividade || ' TOTALIZANDO '|| inTotal ||' dias ('|| replace(publico.fn_extenso(inTotal),' REAIS','') ||' DIAS)';
        rwRetorno.efetividade := stEfetividade;

        RETURN NEXT rwRetorno;
    END LOOP;
  
END;
$$ language 'plpgsql';
