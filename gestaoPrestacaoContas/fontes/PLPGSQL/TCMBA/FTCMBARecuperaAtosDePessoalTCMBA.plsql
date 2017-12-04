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
/* recuperaAtosDePessoalTCMBA
 * Data de Criação : 07/10/2015
 * @author Analista : Dagiane Vieira
 * @author Desenvolvedor : Evandro Melos
 * $Id:$
*/

CREATE OR REPLACE FUNCTION tcmba.recuperaAtosDePessoalTCMBA(INTEGER, VARCHAR, VARCHAR) RETURNS SETOF RECORD as $$
DECLARE    
    inCodPeriodoMovimentacao ALIAS FOR $1;
    stCompetencia            ALIAS FOR $2;
    stEntidade               ALIAS FOR $3;
    stSQL                    VARCHAR:='';
    reRecord                 RECORD;
BEGIN
    
    --REGRA TAMBEM PODE SER CONFERIDA NO TICKET #22976
    --TCMBA - Exportação - Informes Mensais - Pessoal.txt
    
    /*
        1) Para tipos de atos de Pessoal referente a admissão - Cadastro de servidor / admissões 
        Buscar pessoal.contrato_servidor_nomeacao_posse.dt_admissao compreendidas no mes do filtro 
        e pessoal.contrato_servidor.cod_regime e cod_sub_divisao cfe tipos abaixo:
        Preencher com Tipo de Ato de Pessoal:
        1 - Admissão para cargo efetivo => TCM - BA :: Configuração :: Relacionar Tipo de Cargo como "tipo de cargo"="cargo efetivo" e "Tipo de Regime" = Estatutario 
        2 - Nomeação para cargo comissionado => TCM - BA :: Configuração :: Relacionar Tipo de Cargo como "tipo de cargo"="cargo comissionado" e "tipo de cargo=Agente político" 
        3 - Admissão para emprego público => TCM - BA :: Configuração :: Relacionar Tipo de Cargo como "tipo de cargo"="cargo efetivo" e "Tipo de Regime" = C.L.T
        8 - Contratação por prazo determinado (Inicial) => TCM - BA :: Configuração :: Relacionar Tipo de Cargo como "tipo de cargo"="temporário" 
    */
    stSQL := 'CREATE TEMPORARY TABLE tmp_atos_admissao AS
            SELECT * FROM(
            SELECT  
                contrato_servidor.cod_contrato
                ,CASE WHEN tipo_cargo_tce.cod_tipo_cargo_tce = 2 AND tipo_regime_tce.cod_tipo_regime_tce = 2 
                    THEN 1
                    ELSE
                        CASE WHEN tipo_cargo_tce.cod_tipo_cargo_tce = 1 AND tipo_regime_tce.cod_tipo_regime_tce = 4 
                            THEN 2
                            ELSE
                                CASE WHEN tipo_cargo_tce.cod_tipo_cargo_tce = 2 AND tipo_regime_tce.cod_tipo_regime_tce = 1 
                                    THEN 3
                                    ELSE
                                        CASE WHEN tipo_cargo_tce.cod_tipo_cargo_tce = 4 
                                            THEN 8
                                        END -- END 8
                                END -- END 3
                        END -- END 2
                END AS tipo_ato
            FROM pessoal'||stEntidade||'.contrato_servidor

      INNER JOIN pessoal'||stEntidade||'.contrato
              ON contrato.cod_contrato = contrato_servidor.cod_contrato

      INNER JOIN pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
              ON contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor.cod_contrato
              AND TO_CHAR(contrato_servidor_nomeacao_posse.dt_admissao,''yyyymm'') = '''||stCompetencia||'''
      
      INNER JOIN (SELECT contrato_servidor_nomeacao_posse.cod_contrato
                         , max(contrato_servidor_nomeacao_posse.timestamp) as timestamp
                      FROM pessoal'||stEntidade||'.contrato_servidor_nomeacao_posse
                      WHERE contrato_servidor_nomeacao_posse.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
                      GROUP BY contrato_servidor_nomeacao_posse.cod_contrato
                  ) as max_contrato_servidor_nomeacao_posse
              ON max_contrato_servidor_nomeacao_posse.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato
             AND max_contrato_servidor_nomeacao_posse.timestamp = contrato_servidor_nomeacao_posse.timestamp

    INNER JOIN pessoal'||stEntidade||'.sub_divisao
            ON sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
 
     LEFT JOIN pessoal'||stEntidade||'.de_para_tipo_cargo_tcmba
            ON de_para_tipo_cargo_tcmba.cod_sub_divisao = sub_divisao.cod_sub_divisao

     LEFT JOIN tcmba.tipo_cargo_tce
            ON tipo_cargo_tce.cod_tipo_cargo_tce = de_para_tipo_cargo_tcmba.cod_tipo_cargo_tce

     LEFT JOIN tcmba.tipo_regime_tce
            ON tipo_regime_tce.cod_tipo_regime_tce = de_para_tipo_cargo_tcmba.cod_tipo_regime_tce
    ) as foo
        WHERE tipo_ato IS NOT NULL
        ORDER BY cod_contrato
    ';

    EXECUTE stSQL;

    /*
    2) Para tipos de atos de Pessoal referente a Rescisões:
    => Buscar pessoal.contrato_servidor_caso_causa.dt_rescisao compreendidos no mes do filtro e relacionando cod_caso_causa a pessoal.caso_causa e quando pessoal.causa_rescisao.num_causa for:
    Preencher com Tipo de Ato de Pessoal:    
    4  - Aposentadoria - RGPS   (somente para num_causa => 70 a 79 e regime previdencia = rgps folhapagamento.previdencia.cod_regime_previdencia = 1 relacionado a pessoal.contrato_servidor_previdencia)
    36 - Aposentadoria - RPPS   (somente para num_causa => 70 a 79 e regime previdencia = rpps folhapagamento.previdencia.cod_regime_previdencia = 2 relacionado a pessoal.contrato_servidor_previdencia)
    13 - Exoneração/Demissão de cargo ou emprego público  (somente para num_causa in 10,11,20,21)
    14 - Falecimento (somente para num_causa in 60,62,64)
    24 - Contratação por prazo determinado (Término Contrato)  => somente para num_causa = 12
    */
    stSQL :=' CREATE TEMPORARY TABLE tmp_atos_rescisao AS
                SELECT * FROM(
                SELECT 
                    contrato_servidor.cod_contrato
                    ,CASE WHEN (causa_rescisao.num_causa BETWEEN 70 AND 79) AND previdencia.cod_regime_previdencia = 1 
                    THEN 
                        4
                    ELSE
                        CASE WHEN (causa_rescisao.num_causa BETWEEN 70 AND 79) AND previdencia.cod_regime_previdencia = 2 
                        THEN
                            36
                        ELSE
                            CASE WHEN (causa_rescisao.num_causa=10)
                                    OR (causa_rescisao.num_causa=11) 
                                    OR (causa_rescisao.num_causa=20) 
                                    OR (causa_rescisao.num_causa=21) 
                            THEN 
                                13
                            ELSE
                                CASE WHEN (causa_rescisao.num_causa=60) 
                                        OR (causa_rescisao.num_causa=62) 
                                        OR (causa_rescisao.num_causa=64) 
                                THEN 
                                    14
                                ELSE
                                    CASE WHEN causa_rescisao.num_causa = 12 
                                    THEN 
                                        24
                                    END --24
                                END -- 14
                            END -- 13
                        END -- END 36
                    END AS tipo_ato
               FROM pessoal'||stEntidade||'.contrato_servidor
        
         INNER JOIN(SELECT contrato_servidor_previdencia.*
                      FROM pessoal'||stEntidade||'.contrato_servidor_previdencia
                      INNER JOIN(SELECT max.cod_contrato
                                        ,max.cod_previdencia
                                        ,MAX(timestamp) as timestamp
                                    FROM pessoal'||stEntidade||'.contrato_servidor_previdencia as max
                                      WHERE max.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
                                    GROUP BY 1,2
                                ) AS max_contratro_servidor_previdencia
                         ON max_contratro_servidor_previdencia.cod_contrato = contrato_servidor_previdencia.cod_contrato
                        AND max_contratro_servidor_previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
                        AND max_contratro_servidor_previdencia.timestamp = contrato_servidor_previdencia.timestamp            
                  ) AS contrato_servidor_previdencia
                 ON contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
                AND contrato_servidor_previdencia.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
        
         INNER JOIN folhapagamento'||stEntidade||'.previdencia
                 ON previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
        
         LEFT JOIN pessoal'||stEntidade||'.contrato_servidor_caso_causa 
                ON contrato_servidor_caso_causa.cod_contrato = contrato_servidor_previdencia.cod_contrato
         
         LEFT JOIN pessoal'||stEntidade||'.caso_causa
                ON caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa
        
         LEFT JOIN pessoal'||stEntidade||'.causa_rescisao
                ON causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao
            
            WHERE contrato_servidor_caso_causa.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
              AND TO_CHAR(contrato_servidor_caso_causa.dt_rescisao,''yyyymm'') = '''||stCompetencia||'''
        ) as foo
        WHERE tipo_ato IS NOT NULL
        ORDER BY cod_contrato
    ';

    EXECUTE stSQL;

    /*
    3) Para tipos de atos de Pessoal referente a Pensões:
    => Buscar pessoal.contrato_pensionista.dt_inicio_beneficio e dt_fim_beneficio estiver compreendido no mes do filtro
    Preencher com Tipo de Ato de Pessoal:
    15 - Pensão - RGPS => folhapagamento.previdencia.cod_regime_previdencia = 1 relacionado a pessoal.contrato_servidor_previdencia do pessoal.contrato_pensionista.cod_contrato_cedente 
    35 - Pensão - RPPS => folhapagamento.previdencia.cod_regime_previdencia = 2 relacionado a pessoal.contrato_servidor_previdencia do pessoal.contrato_pensionista.cod_contrato_cedente 
    41 - Término de Pensão => quando pessoal.contrato_pensionista.dt_encerramento ocorrer no mes do filtro
    */
    stSQL := ' CREATE TEMPORARY TABLE tmp_atos_pensao AS
                SELECT * FROM (
                    SELECT 
                        contrato_pensionista.cod_contrato
                        ,CASE previdencia.cod_regime_previdencia
                            WHEN 1 THEN 15
                            WHEN 2 THEN 35
                            ELSE
                                CASE WHEN TO_CHAR(contrato_pensionista.dt_encerramento,''yyyymm'') = '''||stCompetencia||'''
                                THEN
                                    41
                                END
                        END AS tipo_ato
                   FROM pessoal'||stEntidade||'.contrato_pensionista
             INNER JOIN (SELECT contrato_pensionista_previdencia.*
                          FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia
                          INNER JOIN(SELECT max.cod_contrato
                                            ,max.cod_previdencia
                                            ,MAX(timestamp) as timestamp
                                        FROM pessoal'||stEntidade||'.contrato_pensionista_previdencia as max                                        
                                          WHERE max.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
                                        GROUP BY 1,2
                                    ) AS max_contrato_pensionista_previdencia
                             ON max_contrato_pensionista_previdencia.cod_contrato = contrato_pensionista_previdencia.cod_contrato
                            AND max_contrato_pensionista_previdencia.cod_previdencia = contrato_pensionista_previdencia.cod_previdencia
                            AND max_contrato_pensionista_previdencia.timestamp = contrato_pensionista_previdencia.timestamp            
                        ) AS contrato_pensionista_previdencia
                     ON contrato_pensionista_previdencia.cod_contrato = contrato_pensionista.cod_contrato
                    AND contrato_pensionista_previdencia.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
             
             INNER JOIN folhapagamento'||stEntidade||'.previdencia
                     ON previdencia.cod_previdencia = contrato_pensionista_previdencia.cod_previdencia
        
                  WHERE TO_CHAR(contrato_pensionista.dt_inicio_beneficio,''yyyymm'') = '''||stCompetencia||'''
                     OR TO_CHAR(contrato_pensionista.dt_encerramento,''yyyymm'') = '''||stCompetencia||'''
        ) as foo
        WHERE tipo_ato IS NOT NULL
        ORDER BY cod_contrato
    ';

    EXECUTE stSQL;
    
    /*
    4) Para tipos de atos de Pessoal referente a Cedidos/Adidos:
    => pessoal.adido_cedido.dt_inicial e dt_final estiver compreendido no mes do filtro
    Preencher com Tipo de Ato de Pessoal:
    22     Cessão de outro Órgão/Entidade  => e pessoal.adido_cedido.tipo_cedencia in (1,2)
    7     Cessão para outro Órgão/Entidade => e pessoal.adido_cedido.tipo_cedencia in (3,4)
    26     Retorno / Devolução de Servidor Cedido => pessoal.adido_cedido.dt_final estiver no mes do filtro
    */
    stSQL := ' CREATE TEMPORARY TABLE tmp_atos_cedidos AS
            SELECT * FROM(
                SELECT
                    adido_cedido.cod_contrato
                    ,CASE adido_cedido.tipo_cedencia
                        WHEN ''a'' THEN 22
                        WHEN ''c'' THEN 7
                        ELSE
                            CASE WHEN TO_CHAR(adido_cedido.dt_final,''yyyymm'') = '''||stCompetencia||'''
                            THEN 
                                26
                            END
                    END AS tipo_ato                    
                FROM (SELECT adido_cedido.*
                        FROM pessoal'||stEntidade||'.adido_cedido
                        INNER JOIN (SELECT  max.cod_contrato 
                                            , max.cod_norma
                                            ,MAX(max.timestamp) as timestamp
                                      FROM pessoal'||stEntidade||'.adido_cedido as max
                                      WHERE max.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
                                      GROUP BY 1,2
                            ) as max_adido_cedido
                        ON max_adido_cedido.cod_contrato = adido_cedido.cod_contrato
                       AND max_adido_cedido.cod_norma = adido_cedido.cod_norma
                       AND max_adido_cedido.timestamp = adido_cedido.timestamp
                    ) as adido_cedido
                        
            WHERE TO_CHAR(adido_cedido.dt_inicial,''yyyymm'') = '''||stCompetencia||'''
               OR TO_CHAR(adido_cedido.dt_final,''yyyymm'') = '''||stCompetencia||'''
              
        ) as foo
        WHERE tipo_ato IS NOT NULL
        ORDER BY cod_contrato
    ';

    EXECUTE stSQL;

    /*
    5) Para tipos de atos de Pessoal referente a assentamentos de afastamento:
    => Se "periodo_inicial" (de pessoal.assentamento_gerado) estiver compreendido no mes/ano do filtro e cod_assentamento (de pessoal.assentamento_gerado e
    pessoal.assentamento_gerado_contrato_servidor) relacionar-se o cod_assentamento com motivo e :
    Preencher com Tipo de Ato de Pessoal:
    51 - Auxílio Doença - previdência própria   (para assentamento.cod_motivo = 5 e regime previd = rpps )
    50 - Auxílio Doença - RGPS                  (para assentamento.cod_motivo = 5 e regime_previd = rgps)
    54 - Licença Maternidade - RGPS             (para assentamento.cod_motivo = 7 e regime_previd = rgps)
    55 - Licença maternindade - Previdência Própria (para assentamento.cod_motivo = 7 e regime_previd = rpps)
    53 - Licença Prêmio                         (para assentamento.cod_motivo = 9 )
    */
    stSQL := ' CREATE TEMPORARY TABLE tmp_ato_afastamento AS
            SELECT * FROM(
                SELECT  
                        assentamento_gerado_contrato_servidor.cod_contrato
                        ,CASE WHEN assentamento_assentamento.cod_motivo = 5 
                            THEN 
                                51
                            ELSE
                                CASE WHEN assentamento_assentamento.cod_motivo = 7 
                                THEN 
                                    54
                                ELSE
                                    CASE WHEN assentamento_assentamento.cod_motivo = 9 
                                    THEN 
                                        59
                                    END -- 59
                                END -- 54                            
                            END AS tipo_ato -- 50
                FROM (SELECT assentamento_gerado.*
                        FROM pessoal'||stEntidade||'.assentamento_gerado
                        INNER JOIN(SELECT MAX(max_assentamento_gerado.timestamp) as timestamp
                                        , max_assentamento_gerado.cod_assentamento_gerado                   
                                     FROM pessoal'||stEntidade||'.assentamento_gerado as max_assentamento_gerado
                                     WHERE max_assentamento_gerado.timestamp <= (ultimoTimestampPeriodoMovimentacao('||inCodPeriodoMovimentacao||','''||stEntidade||'''))::timestamp
                                     GROUP BY max_assentamento_gerado.cod_assentamento_gerado
                                  ) as max_assentamento_gerado
                             ON max_assentamento_gerado.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                            AND max_assentamento_gerado.timestamp = assentamento_gerado.timestamp
                        )as assentamento_gerado
            
            INNER JOIN pessoal'||stEntidade||'.assentamento_assentamento
                    ON assentamento_assentamento.cod_assentamento = assentamento_gerado.cod_assentamento
                    
            INNER JOIN pessoal'||stEntidade||'.assentamento_gerado_contrato_servidor
                    ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
                
                WHERE TO_CHAR(assentamento_gerado.periodo_inicial,''yyyymm'') <= '''||stCompetencia||'''
                  AND ((TO_CHAR(assentamento_gerado.periodo_final,''yyyymm'') >= '''||stCompetencia||''') OR assentamento_gerado.periodo_final is null)
            ) as resultado
                WHERE tipo_ato is not null
                ORDER BY cod_contrato
    ';

    EXECUTE stSQL;
    
    stSQL := ' 
            SELECT * FROM tmp_atos_admissao

            UNION

            SELECT * FROM tmp_atos_rescisao

            UNION
            
            SELECT * FROM tmp_atos_pensao

            UNION
            
            SELECT * FROM tmp_atos_cedidos

            UNION

            SELECT * FROM tmp_ato_afastamento
        ';

    FOR reRecord IN EXECUTE stSQL
    LOOP
        RETURN NEXT reRecord;
    END LOOP;

    DROP TABLE tmp_atos_admissao;
    DROP TABLE tmp_atos_rescisao;
    DROP TABLE tmp_atos_pensao;
    DROP TABLE tmp_atos_cedidos;
    DROP TABLE tmp_ato_afastamento;

END;
$$ LANGUAGE 'plpgsql';