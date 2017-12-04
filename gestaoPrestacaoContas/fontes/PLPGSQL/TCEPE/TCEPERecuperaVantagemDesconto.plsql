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

CREATE OR REPLACE FUNCTION tcepe.recupera_vantagem_desconto (VARCHAR,VARCHAR,INTEGER,INTEGER,VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stDataInicial                           ALIAS FOR $1;
    stDataFinal                             ALIAS FOR $2;
    inCodPeriodoMovimentacao                ALIAS FOR $3;
    inCodComplementar                       ALIAS FOR $4;
    stEntidade                              ALIAS FOR $5;
    stSql                                   VARCHAR := '';
    stSqlAux                                VARCHAR := '';
    reRegistro                              RECORD;
    reRegistroAux                           RECORD;
    dtFinal                                     VARCHAR[];
    
BEGIN
dtFinal = string_to_array(stDataFinal,'/' );

    stSql := ' CREATE TEMPORARY TABLE tmp_tabela AS

 SELECT       0 AS reservado_tce
                     , ''''::VARCHAR AS justificativa
                     ,  ' || quote_literal(dtFinal[2]) || ' AS mes_folha
                     ,  ' || quote_literal(dtFinal[3]) || ' AS ano_folha
                     , contratos.cod_contrato 
                     , contratos.cod_servidor
                     , contratos.cpf
                     , contratos.cod_cargo
                     , contratos.matricula
                     , folhas.cod_operacao
                     , folhas.cod_evento
                     , folhas.codigo
                     , folhas.sequencia
                     , folhas.tipo_folha
                     , folhas.desdobramento
                     , sum(folhas.valor) as valor
                  FROM (     SELECT sw_cgm_pessoa_fisica.cpf
                                  , contrato.cod_contrato
                                  , contrato.registro AS matricula
                                  , cargo.cod_cargo                                  
                                  , servidor.cod_servidor
                               FROM pessoal.servidor
                         INNER JOIN sw_cgm
                                 ON sw_cgm.numcgm = servidor.numcgm
                         INNER JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                         INNER JOIN pessoal.servidor_contrato_servidor
                                 ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor 
                         INNER JOIN pessoal.contrato_servidor
                                 ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato 
                         INNER JOIN pessoal.contrato
                                 ON contrato_servidor.cod_contrato = contrato.cod_contrato 

                         INNER JOIN pessoal.cargo
                                 ON cargo.cod_cargo = contrato_servidor.cod_cargo
                                 
                              UNION          
                        
                             SELECT sw_cgm_pessoa_fisica.cpf
                                  , contrato.cod_contrato
                                  , contrato.registro AS matricula
                                  , cargo.cod_cargo
                                  , 0 as cod_servidor
                               FROM pessoal.pensionista
                         INNER JOIN sw_cgm
                                 ON pensionista.numcgm = sw_cgm.numcgm
                         INNER JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                         INNER JOIN pessoal.contrato_pensionista
                                 ON contrato_pensionista.cod_pensionista      = pensionista.cod_pensionista
                                AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente
                         INNER JOIN pessoal.contrato                                                                                             
                                 ON contrato_pensionista.cod_contrato = contrato.cod_contrato
                         INNER JOIN pessoal.contrato_servidor
                                 ON contrato_servidor.cod_contrato = contrato_pensionista.cod_contrato_cedente
                         INNER JOIN pessoal.cargo
                                 ON cargo.cod_cargo = contrato_servidor.cod_cargo
                       ) AS contratos
            INNER JOIN (
                          SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 0 as tipo_folha 
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao        
                          , desdobramento
                       FROM recuperarEventosCalculados(1, '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                      
                      UNION ALL SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 0 as tipo_folha 
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                          ,  desdobramento
                       FROM recuperarEventosCalculados(2,  '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                      
                      UNION ALL SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 1 as tipo_folha
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                           , desdobramento
                       FROM recuperarEventosCalculados(3,  '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                      
                      UNION ALL SELECT cod_contrato
                          , cod_evento
                          , codigo
                          , sequencia
                          , valor
                          , 0 as tipo_folha
                          , CASE WHEN natureza = ''D'' THEN
                                1
                            ELSE
                                0 
                            END as cod_operacao
                           , desdobramento
                       FROM recuperarEventosCalculados(4,  '|| inCodPeriodoMovimentacao ||', 0, 0, '|| quote_literal(stEntidade) ||', '''')
                      WHERE natureza IN (''P'', ''D'')
                       ) as folhas
                    ON contratos.cod_contrato = folhas.cod_contrato
              GROUP BY reservado_tce
                     , justificativa
                     ,  mes_folha
                     ,  ano_folha
                     , contratos.cod_contrato 
                     , contratos.cod_servidor
                     , contratos.cpf
                     , contratos.cod_cargo
                     , contratos.matricula
                     , folhas.cod_operacao
                     , folhas.cod_evento
                     , folhas.codigo
                     , folhas.sequencia
                     , folhas.tipo_folha
                     , folhas.desdobramento
              ORDER BY contratos.cpf
                     , contratos.cod_contrato
                     , contratos.cod_cargo
                     , folhas.tipo_folha
                     , folhas.cod_operacao
                     , folhas.sequencia';     
    EXECUTE stSql;

    stSql := 'SELECT * FROM tmp_tabela';
    
 
    
    stSql := 'SELECT *
                FROM (
                        SELECT
                                 tmp_tabela.reservado_tce
                               , tmp_tabela.matricula
                               , tmp_tabela.cod_cargo
                               , tmp_tabela.cpf
                               , tmp_tabela.mes_folha
                               , tmp_tabela.ano_folha
                               , tmp_tabela.justificativa
                               ,  tmp_tabela.tipo_folha
                                , tmp_tabela.valor  AS vl_vantdesc
                                , tmp_tabela.cod_evento AS cod_vantdesc                             
                               
                        FROM tmp_tabela                       
               
                         
                        GROUP BY tmp_tabela.reservado_tce,
                                 tmp_tabela.matricula,
                                 tmp_tabela.cod_cargo,
                                 tmp_tabela.cpf,
                                 tmp_tabela.mes_folha,
                                 tmp_tabela.ano_folha,
                                 tmp_tabela.justificativa,
                                 tmp_tabela.cod_evento,
                                 tmp_tabela.valor,
                                 tmp_tabela.tipo_folha
                             
                                 
                ) AS retorno
                
            ORDER BY retorno.matricula
            ';
            
            
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;
    
    DROP TABLE tmp_tabela;
  
    
RETURN;
END;

$$LANGUAGE 'plpgsql';