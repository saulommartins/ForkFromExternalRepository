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
CREATE OR REPLACE FUNCTION tcepb.recupera_servidores(stEntidade VARCHAR, stPeriodoMovimentacao VARCHAR) RETURNS SETOF RECORD AS $$
DECLARE
    stSql           VARCHAR;
    reRegistro      RECORD;
    crCursor        REFCURSOR;
    inNumcgm        INTEGER;
    
BEGIN
    
    stSql := 'SELECT sw_cgm_pessoa_fisica.cpf
                   , sw_cgm_pessoa_fisica.numcgm
                   , sw_cgm_pessoa_fisica.rg
                   , sw_cgm_pessoa_fisica.orgao_emissor
                   , sw_cgm.nom_cgm
                   , CAST(to_char(sw_cgm_pessoa_fisica.dt_nascimento, ''ddmmyyyy'') AS VARCHAR(8)) AS dt_nascimento
                   , sw_cgm_pessoa_fisica.sexo
                   , periodo_movimentacao.cod_periodo_movimentacao
                   , CAST(CASE WHEN COALESCE(servidor_cid.cod_cid, 0) <> 0 THEN
                       ''S''
                          ELSE
                       ''N''
                     END AS CHAR) AS deficiente

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

          INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                  ON registro_evento_periodo.cod_periodo_movimentacao = (
                                                                          SELECT cod_periodo_movimentacao
                                                                            FROM folhapagamento'||stEntidade||'.periodo_movimentacao
                                                                           WHERE TO_CHAR(periodo_movimentacao.dt_final, ''mmyyyy'') = '''||stPeriodoMovimentacao||''' 
                                                                        )
                 AND registro_evento_periodo.cod_contrato = servidor_contrato_servidor.cod_contrato

          INNER JOIN folhapagamento'||stEntidade||'.periodo_movimentacao
                  ON periodo_movimentacao.cod_periodo_movimentacao = registro_evento_periodo.cod_periodo_movimentacao

           LEFT JOIN (
                       SELECT servidor_cid.cod_cid
                            , servidor_cid.cod_servidor
                         FROM pessoal'||stEntidade||'.servidor_cid
                   INNER JOIN (  SELECT servidor_cid.cod_servidor
                                      , max(servidor_cid.timestamp) as timestamp
                                   FROM pessoal'||stEntidade||'.servidor_cid
                                  WHERE TO_CHAR(servidor_cid.timestamp, ''mmyyyy'') <= '''||stPeriodoMovimentacao||'''
                               GROUP BY servidor_cid.cod_servidor) as max_servidor_cid
                           ON max_servidor_cid.cod_servidor = servidor_cid.cod_servidor
                          AND max_servidor_cid.timestamp = servidor_cid.timestamp
                     ) AS servidor_cid
                  ON servidor_cid.cod_servidor = servidor.cod_servidor

               WHERE TO_CHAR(contrato_servidor_nomeacao_posse.dt_admissao, ''mmyyyy'') = '''||stPeriodoMovimentacao||'''
                 
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
                                 WHERE registro_evento_complementar.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                   AND registro_evento_complementar.cod_contrato             = contrato.cod_contrato
          
                                 UNION
          
                              -- Verificando salario
                                SELECT 1
                                  FROM folhapagamento'||stEntidade||'.evento_calculado
                            INNER JOIN folhapagamento'||stEntidade||'.registro_evento_periodo
                                    ON registro_evento_periodo.cod_registro = evento_calculado.cod_registro
                                 WHERE registro_evento_periodo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
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
                                 WHERE registro_evento_ferias.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
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
                                 WHERE registro_evento_decimo.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
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
                                 WHERE registro_evento_rescisao.cod_periodo_movimentacao = periodo_movimentacao.cod_periodo_movimentacao
                                   AND registro_evento_rescisao.cod_contrato             = contrato.cod_contrato
                          )
                      )
                      
                 AND NOT EXISTS (
                        SELECT 1
                          FROM tcepb.servidores
                         WHERE servidores.periodo <> '''||stPeriodoMovimentacao||'''
                           AND servidores.numcgm = sw_cgm_pessoa_fisica.numcgm
                 )
                 
            GROUP BY sw_cgm_pessoa_fisica.cpf
                   , sw_cgm_pessoa_fisica.numcgm
                   , sw_cgm_pessoa_fisica.rg
                   , sw_cgm_pessoa_fisica.orgao_emissor
                   , sw_cgm.nom_cgm
                   , to_char(sw_cgm_pessoa_fisica.dt_nascimento, ''ddmmyyyy'')
                   , sw_cgm_pessoa_fisica.sexo
                   , periodo_movimentacao.cod_periodo_movimentacao
                   , servidor_cid.cod_cid

            ORDER BY sw_cgm.nom_cgm;';
    
    OPEN crCursor FOR EXECUTE stSql;
        FETCH crCursor INTO reRegistro;
            WHILE FOUND LOOP
                
                SELECT numcgm
                  INTO inNumcgm
                  FROM tcepb.servidores
                 WHERE servidores.numcgm = reRegistro.numcgm;
                
                IF (inNumcgm IS NULL) THEN
                    INSERT INTO tcepb.servidores (numcgm, periodo) VALUES (reRegistro.numcgm, stPeriodoMovimentacao);
                END IF;
                
                RETURN NEXT reRegistro;
                
                FETCH crCursor INTO reRegistro;
            END LOOP;
    
    CLOSE crCursor;

END;
$$ LANGUAGE plpgsql;
