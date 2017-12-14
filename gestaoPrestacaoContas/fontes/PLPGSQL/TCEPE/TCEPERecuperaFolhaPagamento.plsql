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

CREATE OR REPLACE FUNCTION tcepe.recupera_folha_pagamento (VARCHAR,VARCHAR,INTEGER,VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stDataInicial                           ALIAS FOR $1;
    stDataFinal                             ALIAS FOR $2;
    inCodPeriodoMovimentacao                ALIAS FOR $3;
    stEntidade                              ALIAS FOR $4;
    stSql                                   VARCHAR := '';
    stSqlAux                                VARCHAR := '';
    reRegistro                              RECORD;
    reRegistroAux                           RECORD;
    
BEGIN

    stSql := ' CREATE TEMPORARY TABLE tmp_tabela AS
                
                SELECT
                         0 AS reservado_tce,
                          contrato.registro AS matricula,
                          tabela_servidor_pensionista.cod_cargo,
                          sw_cgm_pessoa_fisica.cpf,
                          TO_CHAR(periodo_movimentacao.dt_final, ''mm'') AS mes_folha,
                          TO_CHAR(periodo_movimentacao.dt_final, ''yyyy'') AS ano_folha,
                          contrato.cod_contrato,
                          fonte_recurso.cod_fonte as cod_fonte_recurso,
                          contrato_servidor_salario.horas_semanais::INTEGER,
                          evento.cod_evento 
                          
                    FROM pessoal.contrato
                    
                    JOIN ( SELECT contrato_servidor.cod_contrato, contrato_servidor.cod_cargo, servidor.numcgm
                             FROM pessoal.contrato_servidor
			     JOIN pessoal.servidor_contrato_servidor
 			       ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
                             JOIN pessoal.servidor
                               ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
                               
                            UNION
                            
                           SELECT contrato_pensionista.cod_contrato, contrato_pensionista.cod_contrato_cedente AS cod_cargo, pensionista.numcgm
                             FROM pessoal.contrato_pensionista
                             JOIN pessoal.pensionista
                               ON pensionista.cod_pensionista = contrato_pensionista.cod_pensionista
                              AND pensionista.cod_contrato_cedente = contrato_pensionista.cod_contrato_cedente
                            
                        ) AS tabela_servidor_pensionista
                      ON tabela_servidor_pensionista.cod_contrato = contrato.cod_contrato
                      
               LEFT JOIN ( SELECT cod_contrato
                                , exercicio
                                , cod_entidade
                                , cod_fonte
                             FROM tcepe.fonte_recurso_lotacao
                        LEFT JOIN( SELECT * 
                                     FROM ultimo_contrato_servidor_orgao('|| quote_literal(stEntidade) ||','|| inCodPeriodoMovimentacao ||')
                                 ) AS servidor_orgao
                               ON fonte_recurso_lotacao.cod_orgao = servidor_orgao.cod_orgao

                        UNION ALL

                           SELECT cod_contrato
                                , exercicio
                                , cod_entidade
                                , cod_fonte 
                             FROM tcepe.fonte_recurso_local
                        LEFT JOIN ( SELECT * 
                             FROM ultimo_contrato_servidor_local('|| quote_literal(stEntidade) ||','|| inCodPeriodoMovimentacao ||')
                                ) AS servidor_local
                               ON fonte_recurso_local.cod_local = servidor_local.cod_local
                         ) AS fonte_recurso
                      ON fonte_recurso.cod_contrato = contrato.cod_contrato

                      
               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = tabela_servidor_pensionista.numcgm
                     
                    JOIN folhapagamento.contrato_servidor_periodo    -- join para achar o periodo movimentacao
                      ON contrato_servidor_periodo.cod_contrato = contrato.cod_contrato
                      
                    JOIN folhapagamento.periodo_movimentacao
                      ON periodo_movimentacao.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                      
                    JOIN folhapagamento.registro_evento_periodo
                      ON registro_evento_periodo.cod_periodo_movimentacao = contrato_servidor_periodo.cod_periodo_movimentacao
                     AND registro_evento_periodo.cod_contrato = contrato_servidor_periodo.cod_contrato
                     
                    JOIN folhapagamento.registro_evento
                      ON registro_evento.cod_registro = registro_evento_periodo.cod_registro
                      
                    JOIN folhapagamento.evento
                      ON evento.cod_evento = registro_evento.cod_evento
                     
               LEFT JOIN pessoal.contrato_servidor_salario 
                      ON contrato_servidor_salario.cod_contrato = contrato.cod_contrato
                     AND contrato_servidor_salario.timestamp = (SELECT MAX(timestamp) FROM pessoal.contrato_servidor_salario AS CSS 
										     WHERE CSS.cod_contrato = contrato_servidor_salario.cod_contrato )
                                                                                    
                    WHERE TO_CHAR(periodo_movimentacao.dt_inicial, ''dd/mm/yyyy'') = ' || quote_literal(stDataInicial) || '
                      AND TO_CHAR(periodo_movimentacao.dt_final, ''dd/mm/yyyy'') = ' || quote_literal(stDataFinal) || '
                      
                 GROUP BY reservado_tce,
                          matricula,
                          tabela_servidor_pensionista.cod_cargo,
                          sw_cgm_pessoa_fisica.cpf,
                          mes_folha,
                          ano_folha,
                          contrato.cod_contrato,
                          fonte_recurso.cod_fonte,
                          contrato_servidor_salario.horas_semanais,
                          evento.cod_evento 
                          
                 ORDER BY matricula
            ';
            
    EXECUTE stSql;
    
    stSql := 'SELECT * FROM tmp_tabela';
    
    stSqlAux := ' CREATE TEMPORARY TABLE tmp_eventos AS
                        SELECT retorno.cod_evento::INTEGER AS cod_evento,
                               SUM(retorno.valor::NUMERIC(14,2)) AS valor,
                               retorno.tipo_evento::VARCHAR AS tipo_evento,
                               retorno.cod_contrato::INTEGER AS cod_contrato,
                               retorno.desdobramento,
                               SUM(retorno.valor_previdencia::NUMERIC(14,2)) AS valor_previdencia,
                               SUM(retorno.valor_irrf::NUMERIC(14,2)) AS valor_irrf
                          FROM tcepe.recupera_evento_previdencia_irrf ( ' || inCodPeriodoMovimentacao || ',
                                                                        ' || quote_literal('B') || ',
                                                                        ' || quote_literal(stEntidade) ||'
                                                                ) AS retorno
                                                                (
                                                                  valor          NUMERIC,
                                                                  quantidade     NUMERIC(15,2),
                                                                  codigo         CHARACTER(5),
                                                                  cod_evento     INTEGER,
                                                                  descricao      CHARACTER(80),
                                                                  natureza       CHARACTER(1),
                                                                  desdobramento  BPCHAR,
                                                                  tipo_evento    TEXT,
                                                                  cod_contrato   INTEGER,
                                                                  valor_previdencia NUMERIC,
                                                                  valor_irrf     NUMERIC
                                                                )
                     GROUP BY cod_evento,tipo_evento,cod_contrato,desdobramento
                     ORDER BY cod_contrato  
                ';
     
    EXECUTE stSqlAux;

    stSql := 'SELECT    reservado_tce,
                        matricula,
                        cod_cargo,
                        cpf,
                        mes_folha,
                        ano_folha,
                        cod_fonte_recurso,
                        horas_semanais,
                        tipo_folha,
                        COALESCE(SUM(valor_previdencia),0.00) AS valor_previdencia,
                        COALESCE(SUM(valor_irrf),0.00) AS valor_irrf
                FROM (
                        SELECT
                                tmp_tabela.reservado_tce,
                                tmp_tabela.matricula,
                                tmp_tabela.cod_cargo,
                                tmp_tabela.cpf,
                                tmp_tabela.mes_folha,
                                tmp_tabela.ano_folha,
                                tmp_tabela.cod_fonte_recurso,
                                tmp_tabela.horas_semanais,
                                CASE WHEN tmp_eventos.tipo_evento = ''complementar_calculado'' AND tmp_eventos.valor IS NOT NULL THEN 2
                                     WHEN tmp_eventos.tipo_evento = ''decimo_calculado''   AND tmp_eventos.valor IS NOT NULL THEN 1
                                     WHEN tmp_eventos.tipo_evento = ''calculado''          AND tmp_eventos.valor IS NOT NULL THEN CASE WHEN tmp_eventos.desdobramento = ''I'' THEN 1 ELSE 0 END
                                     WHEN tmp_eventos.tipo_evento = ''rescisao_calculado'' AND tmp_eventos.valor IS NOT NULL THEN CASE WHEN tmp_eventos.desdobramento = ''D'' THEN 1 ELSE 0 END
                                     WHEN tmp_eventos.tipo_evento = ''ferias_calculado''   AND tmp_eventos.valor IS NOT NULL THEN 0
                                END AS tipo_folha,
                                tmp_eventos.valor_previdencia,
                                tmp_eventos.valor_irrf
                                
                        FROM tmp_tabela
                        
                        JOIN tmp_eventos
                          ON tmp_eventos.cod_evento = tmp_tabela.cod_evento
                         AND tmp_eventos.cod_contrato = tmp_tabela.cod_contrato
                         
                        GROUP BY tmp_tabela.reservado_tce,
                                 tmp_tabela.matricula,
                                 tmp_tabela.cod_cargo,
                                 tmp_tabela.cpf,
                                 tmp_tabela.mes_folha,
                                 tmp_tabela.ano_folha,
                                 tmp_tabela.cod_fonte_recurso,
                                 tmp_tabela.horas_semanais,
                                 tmp_eventos.valor_previdencia,
                                 tmp_eventos.valor_irrf,
                                 tmp_eventos.tipo_evento,
                                 tmp_eventos.valor,
                                 tmp_eventos.desdobramento
                ) AS retorno
                GROUP BY
                            reservado_tce,
                            matricula,
                            cod_cargo,
                            cpf,
                            mes_folha,
                            ano_folha,
                            cod_fonte_recurso,
                            horas_semanais,
                            tipo_folha
                
            ORDER BY retorno.matricula
            ';
            
            
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_tabela;
    DROP TABLE tmp_eventos;
    
RETURN;
END;

$$LANGUAGE 'plpgsql';