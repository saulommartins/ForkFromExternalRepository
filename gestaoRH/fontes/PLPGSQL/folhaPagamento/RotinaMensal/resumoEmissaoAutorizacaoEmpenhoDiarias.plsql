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
/**
    * Resumo para Emissão das Autorizações de Empenho
    * Data de Criação: 02/09/2008
    
    
    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza
    
    * @ignore
    
    $Revision: 26143 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-17 10:37:19 -0200 (Qua, 17 Out 2007) $
    
*/
CREATE OR REPLACE FUNCTION resumoEmissaoAutorizacaoEmpenhoDiarias(varchar,varchar,varchar,varchar) RETURNS SETOF colunasResumoDiarias AS $$
DECLARE
    stTipoFiltro                        ALIAS FOR $1;
    stCodigos                           ALIAS FOR $2;
    stExercicio                         ALIAS FOR $3;
    stEntidadeParametro                 ALIAS FOR $4;
    stEntidade                          VARCHAR:='';
    stSql                               VARCHAR;
    stCodEstrutural                     VARCHAR;
    stCodEstruturalUltimo               VARCHAR:='';
    stCodEstruturalOriginal             VARCHAR:='';
    stDescricaoOriginal                 VARCHAR:='';
    stMascaraDespesa                    VARCHAR;
    stLLA                               VARCHAR;
    stFornecedor                        VARCHAR;
    stOrgao                             VARCHAR;
    stUnidade                           VARCHAR;
    stComparacao1                       VARCHAR:='';
    stComparacao2                       VARCHAR:='';
    arComparacao                        VARCHAR[];
    arCodigos                           VARCHAR[];
    stExercicioDespesa                  VARCHAR:='';
    reRegistro                          RECORD; 
    reDespesa                           RECORD;   
    reContaDespesa                      RECORD;  
    inCodDespesa                        VARCHAR;
    inNumPAO                            VARCHAR;
    boInserir                           BOOLEAN:=FALSE;
    crCursor                            REFCURSOR;
    crDespesa                           REFCURSOR;
    inCountLotacao                      INTEGER;
    inCountLocal                        INTEGER;
    inCountAtributo                     INTEGER;
    inCodConta                          INTEGER;
    rwColunasResumo                     colunasResumoDiarias%ROWTYPE;
    inCodAtributo                       INTEGER;
    stTimestampFechamentoPeriodo        VARCHAR;
    reConfiguracao                      RECORD;
    inCodPeriodoMovimentacao            INTEGER;
BEGIN

    stEntidade := criarBufferEntidade(stEntidadeParametro);
    
    inCodPeriodoMovimentacao := selectIntoInteger('SELECT max(cod_periodo_movimentacao)
                                                     FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                    WHERE to_char(dt_final, ''yyyy'') = '|| quote_literal(stExercicio));
    
    stTimestampFechamentoPeriodo := ultimoTimestampPeriodoMovimentacao(inCodPeriodoMovimentacao,stEntidadeParametro);
    
    ----------------------------------------------------
    --Busca Configuração para a autorizacao de empenho                                              
    ----------------------------------------------------                       

    stSql := '
          SELECT ultima_vigencia_competencia.vigencia as dt_vigencia
               , to_char(ultima_vigencia_competencia.vigencia,''dd/mm/yyyy'') as vigencia
               , to_char(ultima_vigencia_competencia.vigencia,''yyyy'') as exercicio
               , ultima_vigencia_competencia.cod_periodo_movimentacao
               , (SELECT max(timestamp)
                    FROM (SELECT max(timestamp) as timestamp
                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho
                           WHERE vigencia = ultima_vigencia_competencia.vigencia
                           UNION
                          SELECT max(timestamp) as timestamp
                            FROM folhapagamento'|| stEntidade ||'.configuracao_autorizacao_empenho
                           WHERE vigencia = ultima_vigencia_competencia.vigencia
                           UNION
                          SELECT max(timestamp) as timestamp
                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla
                           WHERE vigencia = ultima_vigencia_competencia.vigencia
                         ) as max_timestamp_vigencia
                 ) as timestamp
            FROM (SELECT DISTINCT max(vigencia) as vigencia
                       , ( SELECT cod_periodo_movimentacao
                             FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                            WHERE vigencia BETWEEN dt_inicial AND dt_final
                         ) as cod_periodo_movimentacao
                    FROM ( SELECT vigencia
                             FROM folhapagamento'|| stEntidade ||'.configuracao_empenho
                            UNION
                           SELECT vigencia
                             FROM folhapagamento'|| stEntidade ||'.configuracao_autorizacao_empenho
                            UNION
                           SELECT vigencia
                             FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla
                         ) as configuracoes_empenho
                GROUP BY cod_periodo_movimentacao
                 ) as ultima_vigencia_competencia
           WHERE ultima_vigencia_competencia.vigencia <= (SELECT dt_final
                                                            FROM folhapagamento'|| stEntidade ||'.periodo_movimentacao
                                                           WHERE cod_periodo_movimentacao = '|| inCodPeriodoMovimentacao ||')
             AND to_char(ultima_vigencia_competencia.vigencia,''yyyy'') = '|| quote_literal(stExercicio) ||'
        ORDER BY dt_vigencia DESC LIMIT 1
    ';

    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reConfiguracao;
    CLOSE crCursor;    
    
    stMascaraDespesa := selectIntoVarchar('SELECT valor
                                             FROM administracao.configuracao
                                            WHERE parametro = ''masc_class_despesa'' AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||' ');
    
    inCountLotacao  := selectIntoInteger('SELECT count(*) 
                                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_lotacao
                                           WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                             AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                            
    inCountLocal    := selectIntoInteger('SELECT count(*) 
                                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_local
                                           WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                             AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                                            
    inCountAtributo := selectIntoInteger('SELECT count(*) 
                                            FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo
                                           WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                             AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');

    stSql := '
              SELECT diaria.cod_diaria
                   , diaria.timestamp
                   , diaria.motivo
                   , contrato.*
                   , sw_cgm.nom_cgm
                   , sw_cgm.numcgm
                   , diaria.dt_inicio
                   , diaria.dt_termino
                   , diaria.vl_total as valor
                   , diaria.quantidade
                   , tipo_diaria_despesa.cod_conta
                   , tipo_diaria_despesa.exercicio
                   , norma.num_norma
                   , norma.exercicio as exercicio_norma
                   , (SELECT descricao FROM pessoal'|| stEntidade ||'.cargo WHERE cod_cargo = contrato_servidor.cod_cargo) as cargo
    ';
                     
    IF inCountLotacao >= 1 THEN
        stSql := stSql || '
                   , contrato_servidor_orgao.cod_orgao
                   , (SELECT orgao||''-''||recuperaDescricaoOrgao(contrato_servidor_orgao.cod_orgao,('|| quote_literal(stExercicio ||'-01-01') ||')::date) FROM organograma.vw_orgao_nivel WHERE cod_orgao = contrato_servidor_orgao.cod_orgao) as lla
        ';
    END IF;       
    IF inCountLocal >= 1 THEN
        stSql := stSql || ' , contrato_servidor_local.cod_local
                            , (SELECT descricao FROM organograma.local WHERE cod_local = contrato_servidor_local.cod_local) as lla ';
    END IF;
    IF inCountAtributo >= 1 THEN
        stSql := stSql || ' , atributo.valor,atributo.cod_atributo
                            , atributo.valor as lla ';
    END IF;        

    stSql := stSql || '
                FROM diarias'|| stEntidade ||'.diaria
          INNER JOIN (SELECT cod_diaria
                           , cod_contrato
                           , max(timestamp) as timestamp
                        FROM diarias'|| stEntidade ||'.diaria
                    GROUP BY cod_diaria
                           , cod_contrato
                     ) as max_diaria
                  ON diaria.cod_contrato = max_diaria.cod_contrato
                 AND diaria.cod_diaria   = max_diaria.cod_diaria
                 AND diaria.timestamp    = max_diaria.timestamp
           LEFT JOIN diarias'|| stEntidade ||'.diaria_empenho 
                  ON diaria_empenho.cod_diaria   = diaria.cod_diaria
                 AND diaria_empenho.cod_contrato = diaria.cod_contrato
                 AND diaria_empenho.timestamp    = diaria.timestamp
          INNER JOIN diarias'|| stEntidade ||'.tipo_diaria
                  ON diaria.cod_tipo       = tipo_diaria.cod_tipo  
                 AND diaria.timestamp_tipo = tipo_diaria.timestamp
           LEFT JOIN diarias'|| stEntidade ||'.tipo_diaria_despesa
                  ON tipo_diaria.cod_tipo  = tipo_diaria_despesa.cod_tipo
                 AND tipo_diaria.timestamp = tipo_diaria_despesa.timestamp
          INNER JOIN pessoal'|| stEntidade ||'.contrato
                  ON diaria.cod_contrato = contrato.cod_contrato
          INNER JOIN pessoal'|| stEntidade ||'.contrato_servidor
                  ON contrato.cod_contrato = contrato_servidor.cod_contrato
          INNER JOIN pessoal'|| stEntidade ||'.servidor_contrato_servidor
                  ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato
          INNER JOIN pessoal'|| stEntidade ||'.servidor
                  ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor
           LEFT JOIN (SELECT contrato_servidor_orgao.cod_orgao
                           , contrato_servidor_orgao.cod_contrato
                        FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao
                  INNER JOIN (SELECT cso.cod_contrato
                                   , max(cso.timestamp) as timestamp
                                FROM pessoal'|| stEntidade ||'.contrato_servidor_orgao AS cso
                               WHERE cso.timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                            GROUP BY cso.cod_contrato
                             ) as max_contrato_servidor_orgao
                          ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato
                         AND max_contrato_servidor_orgao.timestamp    = contrato_servidor_orgao.timestamp
                     ) AS contrato_servidor_orgao
                  ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato
          INNER JOIN sw_cgm
                  ON servidor.numcgm = sw_cgm.numcgm
          INNER JOIN normas.norma
                  ON diaria.cod_norma = norma.cod_norma
    ';
         
    IF inCountLocal >= 1 THEN
        stSql := stSql || 'INNER JOIN ( SELECT contrato_servidor_local.cod_contrato
                                             , contrato_servidor_local.cod_local
                                          FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                             , (   SELECT cod_contrato
                                                        , max(timestamp) as timestamp
                                                     FROM pessoal'|| stEntidade ||'.contrato_servidor_local
                                                    WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                 GROUP BY cod_contrato
                                               ) as max_contrato_servidor_local
                                         WHERE contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato
                                           AND contrato_servidor_local.timestamp    = max_contrato_servidor_local.timestamp
                                      ) as contrato_servidor_local
                                   ON (contrato.cod_contrato = contrato_servidor_local.cod_contrato)';
    END IF;
    
    IF inCountAtributo >= 1 THEN
    
        inCodAtributo   := selectIntoInteger('SELECT cod_atributo 
                                                FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo 
                                               WHERE exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                 AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||'
                                               LIMIT 1');
        
        stSql := stSql || '     INNER JOIN (SELECT atributo_contrato_servidor_valor.*
                                              FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                                
                                                 , (  SELECT cod_contrato                                                                            
                                                           , cod_atributo                                                                            
                                                           , max(timestamp) as timestamp                                                             
                                                        FROM pessoal'|| stEntidade ||'.atributo_contrato_servidor_valor                      
                                                       WHERE timestamp <= '|| quote_literal(stTimestampFechamentoPeriodo) ||'
                                                    GROUP BY cod_contrato                                                                          
                                                           , cod_atributo) as max_atributo_contrato_servidor_valor
                                                 , folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo
                                                 , folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo_valor
                                     WHERE atributo_contrato_servidor_valor.cod_contrato = max_atributo_contrato_servidor_valor.cod_contrato
                                       AND atributo_contrato_servidor_valor.timestamp    = max_atributo_contrato_servidor_valor.timestamp      
                                       AND atributo_contrato_servidor_valor.cod_atributo = max_atributo_contrato_servidor_valor.cod_atributo              
                                       AND atributo_contrato_servidor_valor.cod_atributo = '|| inCodAtributo ||'
                                       
                                       AND atributo_contrato_servidor_valor.cod_modulo = configuracao_empenho_lla_atributo.cod_modulo
                                       AND atributo_contrato_servidor_valor.cod_atributo = configuracao_empenho_lla_atributo.cod_atributo
                                       AND atributo_contrato_servidor_valor.cod_cadastro = configuracao_empenho_lla_atributo.cod_cadastro
                                       
                                       AND configuracao_empenho_lla_atributo.cod_modulo = configuracao_empenho_lla_atributo_valor.cod_modulo
                                       AND configuracao_empenho_lla_atributo.cod_atributo = configuracao_empenho_lla_atributo_valor.cod_atributo
                                       AND configuracao_empenho_lla_atributo.cod_cadastro = configuracao_empenho_lla_atributo_valor.cod_cadastro
                                       AND configuracao_empenho_lla_atributo.exercicio = configuracao_empenho_lla_atributo_valor.exercicio
                                       AND configuracao_empenho_lla_atributo.timestamp = configuracao_empenho_lla_atributo_valor.timestamp
                                       
                                       AND configuracao_empenho_lla_atributo.exercicio = '|| (reConfiguracao.exercicio) ||'
                                       AND configuracao_empenho_lla_atributo.timestamp = '|| (reConfiguracao.timestamp) ||'
                                       AND atributo_contrato_servidor_valor.valor = configuracao_empenho_lla_atributo_valor.valor
                                           ) as atributo
                                        ON (contrato.cod_contrato = atributo.cod_contrato)';
    END IF;
         
    stSql := stSql || '
               WHERE diaria_empenho.cod_diaria IS NULL
    ';       

    IF stTipoFiltro = 'contrato' or stTipoFiltro = 'cgm_contrato' THEN
        stSql := stSql || ' AND contrato.cod_contrato IN ('|| stCodigos ||')';
    END IF;       
    IF stTipoFiltro = 'lotacao' THEN
        stSql := stSql || ' AND contrato_servidor_orgao.cod_orgao IN ('|| stCodigos ||')';
    END IF;           

    IF stTipoFiltro = 'periodo' THEN
        arCodigos := string_to_array(stCodigos,'#');
        stSql := stSql || ' AND (diaria.dt_inicio  between '|| quote_literal(to_date(arCodigos[1],'dd/mm/yyyy')) ||' AND '|| quote_literal(to_date(arCodigos[2],'dd/mm/yyyy')) ||' ';
        stSql := stSql || '  OR  diaria.dt_termino between '|| quote_literal(to_date(arCodigos[1],'dd/mm/yyyy')) ||' AND '|| quote_literal(to_date(arCodigos[2],'dd/mm/yyyy')) ||' )';
    END IF;               


    stSql := stSql || '
            GROUP BY diaria.cod_diaria
                   , diaria.timestamp
                   , diaria.motivo
                   , contrato.cod_contrato
                   , sw_cgm.nom_cgm
                   , sw_cgm.numcgm
                   , diaria.dt_inicio
                   , diaria.dt_termino
                   , diaria.vl_total
                   , diaria.quantidade
                   , tipo_diaria_despesa.cod_conta
                   , tipo_diaria_despesa.exercicio
                   , norma.num_norma
                   , norma.exercicio
                   , cargo';
    
    IF inCountLotacao >= 1 THEN
        stSql := stSql || ' , contrato_servidor_orgao.cod_orgao ';
    END IF;       
    IF inCountLocal >= 1 THEN
        stSql := stSql || ' , contrato_servidor_local.cod_local ';
    END IF;
    IF inCountAtributo >= 1 THEN
        stSql := stSql || ' , atributo.valor,atributo.cod_atributo ';
    END IF;        
    
    stSql := stSql || '
                   , lla
            ORDER BY dt_inicio
                   , dt_termino
    ';
    
    FOR reRegistro IN EXECUTE stSql LOOP
        stOrgao                 := '';
        stUnidade               := '';
        inCodDespesa            := ''; 
        stCodEstruturalOriginal := '';
        stCodEstruturalUltimo   := '';
        stDescricaoOriginal     := '';
        inNumPAO                := '';
        stLLA                   := '';
        stFornecedor            := reRegistro.registro ||'-'|| reRegistro.nom_cgm;


        inCodConta         := reRegistro.cod_conta;
        stExercicioDespesa := reRegistro.exercicio;
        
        IF inCountLotacao >= 1 THEN
            inNumPAO := selectIntoInteger('SELECT num_pao
                                             FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_lotacao
                                            WHERE cod_orgao = '|| reRegistro.cod_orgao ||'
                                              AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                              AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
                        
        END IF;                         
        IF inCountLocal >= 1 THEN
            inNumPAO := selectIntoInteger('SELECT num_pao                           
                                             FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_local
                                            WHERE cod_local = '|| reRegistro.cod_local ||'
                                              AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                              AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
        END IF;             
        IF inCountAtributo >= 1 THEN
            IF reRegistro.valor IS NOT NULL THEN
                inNumPAO := selectIntoInteger('SELECT num_pao
                                                 FROM folhapagamento'|| stEntidade ||'.configuracao_empenho_lla_atributo_valor                           
                                                WHERE cod_atributo = '|| reRegistro.cod_atributo ||'
                                                 AND valor     = '|| quote_literal(reRegistro.valor)         ||'
                                                 AND exercicio = '|| quote_literal(reConfiguracao.exercicio) ||'
                                                 AND timestamp = '|| quote_literal(reConfiguracao.timestamp) ||' ');
            END IF;
        END IF;                             

        stSql := 'SELECT cod_estrutural
                    FROM orcamento.conta_despesa
                   WHERE cod_conta = '|| inCodConta ||'
                     AND exercicio = '|| quote_literal(stExercicioDespesa) ||' ';
        stCodEstrutural := selectIntoVarchar(stSql);
        stCodEstruturalOriginal := stCodEstrutural;
        
        IF inNumPAO IS NOT NULL AND inNumPAO != '' THEN
            stSql := 'SELECT cod_despesa 
                        FROM orcamento.despesa
                           , orcamento.conta_despesa
                       WHERE despesa.cod_conta = conta_despesa.cod_conta
                         AND despesa.exercicio = conta_despesa.exercicio                     
                         AND despesa.num_pao = '|| inNumPAO ||'
                         AND despesa.cod_conta = '|| inCodConta ||'
                         AND despesa.exercicio = '|| quote_literal(stExercicioDespesa) ||' ';
            
            inCodDespesa := selectIntoInteger(stSql);
            
            IF inCodDespesa IS NULL THEN
                WHILE inCodDespesa IS NULL AND stCodEstrutural != stCodEstruturalUltimo LOOP               
                    stCodEstruturalUltimo := stCodEstrutural;
                    stCodEstrutural := fn_conta_mae(stCodEstrutural);
                    stCodEstrutural := publico.fn_mascara_completa(stMascaraDespesa,stCodEstrutural);
                    stSql := 'SELECT cod_despesa 
                                FROM orcamento.despesa
                                   , orcamento.conta_despesa
                               WHERE despesa.cod_conta = conta_despesa.cod_conta
                                 AND despesa.exercicio = conta_despesa.exercicio                     
                                 AND despesa.num_pao = '|| inNumPAO ||'
                                 AND conta_despesa.cod_estrutural = '|| quote_literal(stCodEstrutural) ||'
                                 AND despesa.exercicio = '|| quote_literal(stExercicioDespesa) ||' ';
                    inCodDespesa := selectIntoInteger(stSql);

                END LOOP;
                stCodEstruturalOriginal := stCodEstrutural;
            END IF;
            
            IF inCodDespesa IS NOT NULL THEN               
                stSql := 'SELECT despesa.num_orgao
                               , orgao.nom_orgao
                               , unidade.nom_unidade
                               , despesa.num_unidade
                            FROM orcamento.despesa
                               , orcamento.unidade
                               , orcamento.orgao
                           WHERE despesa.exercicio = unidade.exercicio
                             AND despesa.num_unidade = unidade.num_unidade
                             AND despesa.num_orgao = unidade.num_orgao
                             AND unidade.exercicio = orgao.exercicio
                             AND unidade.num_orgao = orgao.num_orgao
                             AND cod_despesa = '|| inCodDespesa ||'
                             AND despesa.exercicio = '|| quote_literal(stExercicio) ||'
                             AND num_pao = '|| inNumPAO;

                OPEN crDespesa FOR EXECUTE stSql;
                    FETCH crDespesa INTO reDespesa;
                CLOSE crDespesa;

                stOrgao   := reDespesa.num_orgao ||'-'|| reDespesa.nom_orgao;
                stUnidade := reDespesa.num_unidade ||'-'|| reDespesa.nom_unidade;
                
            END IF;
        END IF;

        IF stOrgao IS NULL THEN
            stOrgao := '';
        END IF;
        IF stUnidade IS NULL THEN
            stUnidade := '';
        END IF;
        IF inCodDespesa IS NULL THEN
            inCodDespesa := '';
        END IF;        
        IF stCodEstruturalOriginal IS NULL THEN
            stCodEstruturalOriginal := '';
        END IF; 
        IF inNumPAO IS NULL THEN
            inNumPAO := '';
        END IF;    
        IF stFornecedor IS NULL THEN
            stFornecedor := '';
        END IF;                 
                
        stSql := 'SELECT descricao
                    FROM orcamento.conta_despesa
                   WHERE cod_estrutural = '|| quote_literal(stCodEstruturalOriginal) ||' ';                                        
        stDescricaoOriginal := selectIntoVarchar(stSql);   

        rwColunasResumo.orgao              := stOrgao;
        rwColunasResumo.unidade            := stUnidade;
        rwColunasResumo.red_dotacao        := inCodDespesa; 
        rwColunasResumo.rubrica_despesa    := stCodEstruturalOriginal;
        rwColunasResumo.descricao_despesa  := stDescricaoOriginal;
        rwColunasResumo.num_pao            := inNumPAO;
        rwColunasResumo.numcgm             := reRegistro.numcgm;
        rwColunasResumo.fornecedor         := stFornecedor;
        rwColunasResumo.periodo            := to_char(reRegistro.dt_inicio,'dd/mm/yyyy') ||' a '|| to_char(reRegistro.dt_termino,'dd/mm/yyyy');
        rwColunasResumo.ato                := reRegistro.num_norma ||'/'|| reRegistro.exercicio_norma;
        rwColunasResumo.cargo              := reRegistro.cargo;
        rwColunasResumo.valor              := reRegistro.valor;                 
        rwColunasResumo.quantidade         := reRegistro.quantidade;                 
        rwColunasResumo.cod_diaria         := reRegistro.cod_diaria;                 
        rwColunasResumo.cod_contrato       := reRegistro.cod_contrato;                 
        rwColunasResumo.timestamp          := reRegistro.timestamp;                 
        rwColunasResumo.motivo_viagem      := reRegistro.motivo;
        rwColunasResumo.cod_conta          := inCodConta;
        boInserir     := FALSE;
        RETURN NEXT rwColunasResumo;                    
    END LOOP;

    RETURN;
END
$$ LANGUAGE 'plpgsql';