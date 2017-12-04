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
CREATE OR REPLACE FUNCTION tcepb.recupera_matriculas(stEntidade VARCHAR, stPeriodoMovimentacao VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stSql           VARCHAR;
    reRegistro      RECORD;
    crCursor        REFCURSOR;
    inNumcgm        INTEGER;
    
BEGIN
    
    stSql := '
        SELECT cpf
             , numcgm
             , cod_cargo
             , matricula
             , CAST(MAX(dt_admissao) AS VARCHAR) AS dt_admissao
          FROM (
              SELECT sw_cgm_pessoa_fisica.cpf
                   , sw_cgm.numcgm
                   , CASE WHEN periodo_movimentacao.dt_final IS NOT NULL THEN
                            CASE WHEN tcepb.fn_retorna_assentamento(contrato.cod_contrato, to_char(periodo_movimentacao.dt_final, ''mmyyyy''), '''') = 7 then
                                9999
                              ELSE
                                contrato_servidor.cod_cargo
                            END
                     ELSE
                        contrato_servidor.cod_cargo
                     END AS cod_cargo
                   , contrato.registro AS matricula
                   , CAST(to_char(contrato_servidor_nomeacao_posse.dt_admissao, ''ddmmyyyy'') AS VARCHAR(8)) AS dt_admissao

                FROM sw_cgm

          INNER JOIN sw_cgm_pessoa_fisica
                  ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

          INNER JOIN pessoal'||stEntidade||'.servidor
                  ON servidor.numcgm = sw_cgm.numcgm

          INNER JOIN pessoal'||stEntidade||'.servidor_contrato_servidor
                  ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

          INNER JOIN pessoal'||stEntidade||'.contrato
                  ON servidor_contrato_servidor.cod_contrato = contrato.cod_contrato
          
          INNER JOIN pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                  ON contrato_servidor_nomeacao_posse.cod_contrato = contrato.cod_contrato

          INNER JOIN pessoal'||stEntidade||'.contrato_servidor
                  ON contrato_servidor.cod_contrato = servidor_contrato_servidor.cod_contrato

           LEFT JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                  ON registro_evento_periodo.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                 AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato

           LEFT JOIN folhapagamento'||stEntidade||'.periodo_movimentacao
                  ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao

               WHERE (TO_CHAR(contrato_servidor_nomeacao_posse.dt_admissao, ''mmyyyy'') = '''||stPeriodoMovimentacao||'''
                 
                 AND EXISTS (
                          ( 
                              -- Verificando complementar
                                SELECT 1
                                  FROM folhapagamento'||stEntidade||'.evento_complementar_calculado
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento_complementar
                                    ON registro_evento_complementar.cod_registro     = evento_complementar_calculado.cod_registro
                                   AND registro_evento_complementar.cod_evento       = evento_complementar_calculado.cod_evento
                                   AND registro_evento_complementar.cod_configuracao = evento_complementar_calculado.cod_configuracao
                                   AND registro_evento_complementar.timestamp        = evento_complementar_calculado.timestamp_registro
                                 WHERE registro_evento_complementar.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                                   AND registro_evento_complementar.cod_contrato             = contrato.cod_contrato
          
                                 UNION
          
                              -- Verificando salario
                                SELECT 1
                                  FROM folhapagamento'||stEntidade||'.evento_calculado
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                    ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                 WHERE registro_evento_periodo.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                                   AND registro_evento_periodo.cod_contrato             = contrato.cod_contrato
          
                                 UNION
          
                              -- Verificando férias
                                SELECT 1
                                  FROM folhapagamento'||stEntidade||'.evento_ferias_calculado
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento_ferias
                                    ON registro_evento_ferias.cod_registro  = evento_ferias_calculado.cod_registro
                                   AND registro_evento_ferias.cod_evento    = evento_ferias_calculado.cod_evento
                                   AND registro_evento_ferias.desdobramento = evento_ferias_calculado.desdobramento
                                   AND registro_evento_ferias.timestamp     = evento_ferias_calculado.timestamp_registro
                                 WHERE registro_evento_ferias.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                                   AND registro_evento_ferias.cod_contrato             = contrato.cod_contrato
          
                                 UNION
          
                               -- Verificando décimo
                                SELECT 1
                                  FROM folhapagamento'||stEntidade||'.evento_decimo_calculado
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento_decimo
                                    ON registro_evento_decimo.cod_registro  = evento_decimo_calculado.cod_registro
                                   AND registro_evento_decimo.cod_evento    = evento_decimo_calculado.cod_evento
                                   AND registro_evento_decimo.desdobramento = evento_decimo_calculado.desdobramento
                                   AND registro_evento_decimo.timestamp     = evento_decimo_calculado.timestamp_registro
                                 WHERE registro_evento_decimo.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                                   AND registro_evento_decimo.cod_contrato             = contrato.cod_contrato
                                  
                                 UNION
          
                               -- Verificando rescisão
                                SELECT 1
                                  FROM folhapagamento'||stEntidade||'.evento_rescisao_calculado
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento_rescisao
                                    ON registro_evento_rescisao.cod_registro  = evento_rescisao_calculado.cod_registro
                                   AND registro_evento_rescisao.cod_evento    = evento_rescisao_calculado.cod_evento
                                   AND registro_evento_rescisao.desdobramento = evento_rescisao_calculado.desdobramento
                                   AND registro_evento_rescisao.timestamp     = evento_rescisao_calculado.timestamp_registro
                                 WHERE registro_evento_rescisao.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                                   AND registro_evento_rescisao.cod_contrato             = contrato.cod_contrato
                          )
                      )
                      
           ) OR (EXISTS ( SELECT 1
                            FROM tcepb.servidores
                           WHERE servidores.numcgm = sw_cgm_pessoa_fisica.numcgm
                      ) AND NOT EXISTS (
                          SELECT 1
                            FROM tcepb.matriculas
                           WHERE matriculas.periodo <> '''||stPeriodoMovimentacao||'''
                             AND matriculas.numcgm = sw_cgm.numcgm
                      )
                  AND CAST(TO_CHAR(contrato_servidor_nomeacao_posse.dt_admissao, ''yyyymm'') AS INTEGER) <= CAST(SUBSTR('''||stPeriodoMovimentacao||''', 3, 4)||SUBSTR('''||stPeriodoMovimentacao||''', 1, 2) AS INTEGER)
                )

            GROUP BY sw_cgm_pessoa_fisica.cpf
                   , sw_cgm.nom_cgm
                   , sw_cgm.numcgm
                   , contrato_servidor.cod_cargo
                   , contrato.registro
                   , contrato.cod_contrato
                   , to_char(contrato_servidor_nomeacao_posse.dt_admissao, ''ddmmyyyy'')
                   , periodo_movimentacao.dt_final

            ORDER BY sw_cgm.nom_cgm
        ) AS tabela
      GROUP BY cpf
             , numcgm
             , cod_cargo
             , matricula ';
    
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reRegistro;
            WHILE FOUND LOOP

                SELECT numcgm
                  INTO inNumcgm
                  FROM tcepb.matriculas
                 WHERE matriculas.numcgm = reRegistro.numcgm;

                IF (inNumcgm IS NULL) THEN
                    INSERT INTO tcepb.matriculas (numcgm, periodo) VALUES (reRegistro.numcgm, stPeriodoMovimentacao);
                END IF;

                RETURN NEXT reRegistro;
                
                FETCH crCursor INTO reRegistro;
            END LOOP;
    
    CLOSE crCursor;

END;
$$ LANGUAGE plpgsql;
