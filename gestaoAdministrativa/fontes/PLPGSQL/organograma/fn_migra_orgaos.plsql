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
/* fn_migra_orgaos
 *
 * Data de Criação : 07/04/2009


 * @author Analista : Gelson Wolowski Gonçalves
 * @author Desenvolvedor : Fábio Bertoldi

 * @package URBEM
 * @subpackage

 * $Id: fn_migra_orgaos.plsql 66135 2016-07-20 20:11:38Z michel $
 */

CREATE OR REPLACE FUNCTION organograma.fn_migra_orgaos( inNumCgm        INTEGER
                                                      ) RETURNS         BOOLEAN AS $$
DECLARE

    stSqlUpdate     VARCHAR;
    stSqlEnt        VARCHAR;
    reRecordEnt     RECORD;

    stSqlRH         VARCHAR;
    reRecordRH      RECORD;

    tsTimestampIMA  TIMESTAMP;
    stSqlConta      VARCHAR;
    reRecordConta   RECORD;

    stSqlMAX        VARCHAR;
    reRecordMAX     RECORD;

    boAbortar       BOOLEAN := FALSE;
    inCountExec     INTEGER := 0;
    inCountTotal    INTEGER := 0;

BEGIN

    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 19
        AND exercicio  = (
                            SELECT MAX(exercicio)
                              FROM administracao.configuracao
                             WHERE parametro = 'migra_orgao'
                         )
        AND parametro  = 'migra_orgao'
        AND valor      = 'true';

    IF FOUND THEN

        SELECT organograma.fn_insere_de_para_orgaos_existentes('administracao.comunicado'                         ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('administracao.usuario'                            ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('administracao.impressora'                         ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('frota.terceiros_historico'                        ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('patrimonio.historico_bem'                         ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('estagio.estagiario_estagio'                       ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento.configuracao_empenho_lotacao'      ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento.configuracao_empenho_lla_lotacao'  ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_banpara_orgao'                   ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_bb_orgao'                        ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_besc_orgao'                      ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ima.configuracao_banrisul_orgao'                  ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal.contrato_pensionista_orgao'               ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal.contrato_servidor_orgao'                  ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('ponto.configuracao_lotacao'                       ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;
        SELECT organograma.fn_insere_de_para_orgaos_existentes('sw_andamento'                                     ) INTO inCountExec;
        inCountTotal := inCountTotal + inCountExec;

        stSqlEnt := '  SELECT DISTINCT cod_entidade
                         FROM administracao.entidade_rh
                        WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                                  FROM administracao.configuracao
                                                 WHERE exercicio = (
                                                                      SELECT MAX(exercicio)
                                                                        FROM administracao.configuracao
                                                                       WHERE parametro = ''migra_orgao''
                                                                   )
                                                   AND parametro = ''cod_entidade_prefeitura''
                                              )
                            ;
                    ';
    
        FOR reRecordEnt IN EXECUTE stSqlEnt LOOP

            SELECT organograma.fn_insere_de_para_orgaos_existentes('estagio_'       || reRecordEnt.cod_entidade ||'.estagiario_estagio'               ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao'     ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao' ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao'       ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecordEnt.cod_entidade ||'.configuracao_bb_orgao'            ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecordEnt.cod_entidade ||'.configuracao_besc_orgao'          ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('ima_'           || reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao'      ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal_'       || reRecordEnt.cod_entidade ||'.contrato_pensionista_orgao'       ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('pessoal_'       || reRecordEnt.cod_entidade ||'.contrato_servidor_orgao'          ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
            SELECT organograma.fn_insere_de_para_orgaos_existentes('ponto_'         || reRecordEnt.cod_entidade ||'.configuracao_lotacao'             ) INTO inCountExec;
            inCountTotal := inCountTotal + inCountExec;
 
        END LOOP;

        IF inCountTotal > 0 THEN

            UPDATE administracao.configuracao
               SET valor     = 'false'
             WHERE parametro = 'migra_orgao';

            boAbortar := TRUE;

        END IF;

        IF boAbortar = FALSE THEN

            -- UPDATE ADMINISTRACAO.COMUNICADO
            UPDATE administracao.comunicado
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE comunicado.cod_orgao = de_para_orgao.cod_orgao;

            -- UPDATE ADMINISTRACAO.USUARIO
            UPDATE administracao.usuario
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE usuario.cod_orgao = de_para_orgao.cod_orgao;

            -- UPDATE ADMINISTRACAO.IMPRESSORA
            UPDATE administracao.impressora
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE impressora.cod_orgao = de_para_orgao.cod_orgao;

            -- UPDATE FROTA.TERCEIROS_HISTORICO
            stSqlMAX := '       SELECT fth.*
                                  FROM frota.terceiros_historico AS fth
                            INNER JOIN (
                                            SELECT DISTINCT ON (cod_veiculo)
                                                   cod_veiculo
                                                 , MAX(timestamp)   AS timestamp
                                              FROM frota.terceiros_historico
                                          GROUP BY cod_veiculo
                                       )                         AS temp
                                    ON fth.cod_veiculo = temp.cod_veiculo
                                   AND fth.timestamp   = temp.timestamp
                                     ;
                        ';
            FOR reRecordMAX IN EXECUTE stSqlMAX LOOP
                UPDATE frota.terceiros_historico
                   SET cod_orgao = de_para_orgao.cod_orgao_new
                  FROM organograma.de_para_orgao
                 WHERE cod_veiculo = reRecordMAX.cod_veiculo
                   AND timestamp   = reRecordMAX.timestamp
                   AND terceiros_historico.cod_orgao = de_para_orgao.cod_orgao;
            END LOOP;

            -- UPDATE PATRIMONIO.HISTORICO_BEM
            stSqlMAX := '       SELECT phb.*
                                  FROM patrimonio.historico_bem AS phb
                            INNER JOIN (
                                            SELECT DISTINCT ON (cod_bem)
                                                   cod_bem
                                                 , MAX(timestamp)   AS timestamp
                                              FROM patrimonio.historico_bem
                                          GROUP BY cod_bem
                                       )                         AS temp
                                    ON phb.cod_bem   = temp.cod_bem
                                   AND phb.timestamp = temp.timestamp
                                     ;
                        ';
            FOR reRecordMAX IN EXECUTE stSqlMAX LOOP
                UPDATE patrimonio.historico_bem
                   SET cod_orgao = de_para_orgao.cod_orgao_new
                  FROM organograma.de_para_orgao
                 WHERE cod_bem = reRecordMAX.cod_bem
                   AND timestamp   = reRecordMAX.timestamp
                   AND historico_bem.cod_orgao = de_para_orgao.cod_orgao;
            END LOOP;

            -- UPDATE ESTAGIO.ESTAGIARIO_ESTAGIO
            UPDATE estagio.estagiario_estagio
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE estagiario_estagio.cod_orgao = de_para_orgao.cod_orgao;

            -- UPDATE FOLHAPAGAMENTO.CONFIGURACAO_EMPENHO_LOTACAO
            stSqlConta := '
                              SELECT cod_configuracao
                                   , exercicio
                                   , sequencia
                                   , MAX(timestamp) AS timestamp
                                FROM folhapagamento.configuracao_empenho
                               WHERE vigencia >= (
                                                   SELECT MAX(vigencia)
                                                     FROM folhapagamento.configuracao_empenho
                                                    WHERE vigencia <= now()::date
                                                 )
                            GROUP BY cod_configuracao
                                   , exercicio
                                   , sequencia
                                   ;
                          ';
            FOR reRecordConta IN EXECUTE stSqlConta LOOP

                            SELECT MAX(timestamp) AS timestamp
                              INTO tsTimestampIMA
                              FROM folhapagamento.configuracao_empenho
                             WHERE vigencia = (
                                                SELECT MAX(vigencia)
                                                  FROM folhapagamento.configuracao_empenho
                                                 WHERE vigencia        <= now()::date
                                                   AND cod_configuracao = reRecordConta.cod_configuracao
                                                   AND exercicio        = reRecordConta.exercicio
                                                   AND sequencia        = reRecordConta.sequencia
                                              )
                               AND cod_configuracao = reRecordConta.cod_configuracao
                               AND exercicio        = reRecordConta.exercicio
                               AND sequencia        = reRecordConta.sequencia
                                 ;

                            ALTER TABLE folhapagamento.configuracao_empenho_lotacao DROP CONSTRAINT pk_configuracao_empenho_lotacao;

                            INSERT
                              INTO folhapagamento.configuracao_empenho
                                 ( exercicio_pao
                                 , cod_configuracao
                                 , exercicio_despesa
                                 , cod_despesa
                                 , num_pao
                                 , exercicio
                                 , sequencia
                                 , timestamp
                                 , vigencia
                                 )
                            SELECT exercicio_pao
                                 , cod_configuracao
                                 , exercicio_despesa
                                 , cod_despesa
                                 , num_pao
                                 , exercicio
                                 , sequencia
                                 , now()::timestamp(3)
                                 , (
                                     SELECT dt_inicial
                                       FROM folhapagamento.periodo_movimentacao
                                      WHERE cod_periodo_movimentacao = (
                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                           FROM folhapagamento.periodo_movimentacao
                                                                       )
                                   )
                              FROM folhapagamento.configuracao_empenho
                             WHERE timestamp        = tsTimestampIMA
                               AND cod_configuracao = reRecordConta.cod_configuracao
                               AND exercicio        = reRecordConta.exercicio
                               AND sequencia        = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_lotacao
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_orgao
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_orgao
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_lotacao
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_local
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_local
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_local
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_local
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_atributo
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_atributo
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_atributo_valor
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , sequencia
                                 , valor
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , sequencia
                                 , valor
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_atributo_valor
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_situacao
                                 ( cod_configuracao
                                 , exercicio
                                 , situacao
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , situacao
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_situacao
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_conta_despesa
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_conta
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_conta
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_conta_despesa
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_subdivisao
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_sub_divisao
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_sub_divisao
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_subdivisao
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_evento
                                 ( cod_configuracao
                                 , exercicio
                                 , cod_evento
                                 , sequencia
                                 , timestamp
                                 )
                            SELECT cod_configuracao
                                 , exercicio
                                 , cod_evento
                                 , sequencia
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_evento
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_configuracao  = reRecordConta.cod_configuracao
                               AND exercicio         = reRecordConta.exercicio
                               AND sequencia         = reRecordConta.sequencia
                                 ;

                            UPDATE folhapagamento.configuracao_empenho_lotacao
                               SET cod_orgao = de_para_orgao.cod_orgao_new
                              FROM organograma.de_para_orgao
                             WHERE configuracao_empenho_lotacao.cod_orgao  = de_para_orgao.cod_orgao
                               AND configuracao_empenho_lotacao.timestamp IN (
                                                                               SELECT timestamp
                                                                                 FROM folhapagamento.configuracao_empenho
                                                                                WHERE vigencia > (
                                                                                                   SELECT vigencia
                                                                                                     FROM folhapagamento.configuracao_empenho
                                                                                                    WHERE timestamp        = tsTimestampIMA
                                                                                                      AND cod_configuracao = reRecordConta.cod_configuracao
                                                                                                      AND exercicio        = reRecordConta.exercicio
                                                                                                      AND sequencia        = reRecordConta.sequencia
                                                                                                 )
                                                                                  AND cod_configuracao = reRecordConta.cod_configuracao
                                                                                  AND exercicio        = reRecordConta.exercicio
                                                                                  AND sequencia        = reRecordConta.sequencia
                                                                             )
                               AND configuracao_empenho_lotacao.cod_configuracao = reRecordConta.cod_configuracao
                               AND configuracao_empenho_lotacao.exercicio        = reRecordConta.exercicio
                               AND configuracao_empenho_lotacao.sequencia        = reRecordConta.sequencia
                                 ;

                            stSqlRH := '   SELECT cod_configuracao
                                                , cod_orgao
                                                , exercicio
                                                , sequencia
                                                , timestamp
                                             FROM folhapagamento.configuracao_empenho_lotacao
                                            WHERE timestamp IN ( -- >= now()::timestamp(3)
                                                                 SELECT timestamp
                                                                   FROM folhapagamento.configuracao_empenho
                                                                  WHERE vigencia > (
                                                                                     SELECT vigencia
                                                                                       FROM folhapagamento.configuracao_empenho
                                                                                      WHERE timestamp        = '|| quote_literal(tsTimestampIMA)  ||'
                                                                                        AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                                        AND exercicio        = '|| quote_literal(reRecordConta.exercicio)        ||'
                                                                                        AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                                   )
                                                                    AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                    AND exercicio        = '|| quote_literal(reRecordConta.exercicio)        ||'
                                                                    AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                )
                                              AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                              AND exercicio        = '|| quote_literal(reRecordConta.exercicio)        ||'
                                              AND sequencia        = '|| reRecordConta.sequencia        ||'
                                         GROUP BY cod_configuracao
                                                , cod_orgao
                                                , exercicio
                                                , sequencia
                                                , timestamp
                                                ;
                                       ';
                            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                DELETE
                                  FROM folhapagamento.configuracao_empenho_lotacao
                                 WHERE cod_configuracao = reRecordRH.cod_configuracao
                                   AND cod_orgao        = reRecordRH.cod_orgao
                                   AND exercicio        = reRecordRH.exercicio
                                   AND sequencia        = reRecordRH.sequencia
                                   AND timestamp        = reRecordRH.timestamp
                                     ;
                                INSERT
                                  INTO folhapagamento.configuracao_empenho_lotacao
                                     ( cod_configuracao
                                     , cod_orgao
                                     , exercicio
                                     , sequencia
                                     , timestamp
                                     )
                                VALUES
                                     ( reRecordRH.cod_configuracao
                                     , reRecordRH.cod_orgao
                                     , reRecordRH.exercicio
                                     , reRecordRH.sequencia
                                     , reRecordRH.timestamp
                                     );
                            END LOOP;
                            ALTER TABLE folhapagamento.configuracao_empenho_lotacao ADD CONSTRAINT pk_configuracao_empenho_lotacao PRIMARY KEY (exercicio, cod_configuracao, sequencia, timestamp, cod_orgao);

            END LOOP;

            -- UPDATE FOLHAPAGAMENTO.CONFIGURACAO_EMPENHO_LLA_LOTACAO
            stSqlConta := '
                              SELECT cod_configuracao_lla
                                   , exercicio
                                   , MAX(timestamp) AS timestamp
                                FROM folhapagamento.configuracao_empenho_lla
                               WHERE vigencia >= (
                                                   SELECT MAX(vigencia)
                                                     FROM folhapagamento.configuracao_empenho_lla
                                                    WHERE vigencia <= now()::date
                                                 )
                            GROUP BY cod_configuracao_lla
                                   , exercicio
                                   ;
                          ';
            FOR reRecordConta IN EXECUTE stSqlConta LOOP

                            SELECT MAX(timestamp) AS timestamp
                              INTO tsTimestampIMA
                              FROM folhapagamento.configuracao_empenho_lla
                             WHERE vigencia = (
                                                SELECT MAX(vigencia)
                                                  FROM folhapagamento.configuracao_empenho_lla
                                                 WHERE vigencia            <= now()::date
                                                   AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                                                   AND exercicio            = reRecordConta.exercicio
                                              )
                               AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND exercicio            = reRecordConta.exercicio
                                 ;

                            ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao DROP CONSTRAINT pk_configuracao_empenho_lla_lotacao;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_lla
                                 ( cod_configuracao_lla
                                 , exercicio
                                 , timestamp
                                 , vigencia
                                 )
                            SELECT cod_configuracao_lla
                                 , exercicio
                                 , now()::timestamp(3)
                                 , (
                                     SELECT dt_inicial
                                       FROM folhapagamento.periodo_movimentacao
                                      WHERE cod_periodo_movimentacao = (
                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                           FROM folhapagamento.periodo_movimentacao
                                                                       )
                                   )
                              FROM folhapagamento.configuracao_empenho_lla
                             WHERE timestamp            = tsTimestampIMA
                               AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND exercicio            = reRecordConta.exercicio
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_lla_lotacao
                                 ( cod_configuracao_lla
                                 , exercicio
                                 , num_pao
                                 , cod_orgao
                                 , timestamp
                                 )
                            SELECT cod_configuracao_lla
                                 , exercicio
                                 , num_pao
                                 , cod_orgao
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_lla_lotacao
                             WHERE timestamp            = tsTimestampIMA
                               AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND exercicio            = reRecordConta.exercicio
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_lla_local
                                 ( cod_configuracao_lla
                                 , exercicio
                                 , num_pao
                                 , cod_local
                                 , timestamp
                                 )
                            SELECT cod_configuracao_lla
                                 , exercicio
                                 , num_pao
                                 , cod_local
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_lla_local
                             WHERE timestamp            = tsTimestampIMA
                               AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND exercicio            = reRecordConta.exercicio
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_lla_atributo
                                 ( cod_configuracao_lla
                                 , exercicio
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , timestamp
                                 )
                            SELECT cod_configuracao_lla
                                 , exercicio
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_lla_atributo
                             WHERE timestamp            = tsTimestampIMA
                               AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND exercicio            = reRecordConta.exercicio
                                 ;

                            INSERT
                              INTO folhapagamento.configuracao_empenho_lla_atributo_valor
                                 ( cod_configuracao_lla
                                 , exercicio
                                 , num_pao
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , valor
                                 , timestamp
                                 )
                            SELECT cod_configuracao_lla
                                 , exercicio
                                 , num_pao
                                 , cod_atributo
                                 , cod_modulo
                                 , cod_cadastro
                                 , valor
                                 , now()::timestamp(3)
                              FROM folhapagamento.configuracao_empenho_lla_atributo_valor
                             WHERE timestamp            = tsTimestampIMA
                               AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND exercicio            = reRecordConta.exercicio
                                 ;

                            UPDATE folhapagamento.configuracao_empenho_lla_lotacao
                               SET cod_orgao = de_para_orgao.cod_orgao_new
                              FROM organograma.de_para_orgao
                             WHERE configuracao_empenho_lla_lotacao.cod_orgao  = de_para_orgao.cod_orgao
                               AND configuracao_empenho_lla_lotacao.timestamp IN (
                                                                                   SELECT timestamp
                                                                                     FROM folhapagamento.configuracao_empenho_lla
                                                                                    WHERE vigencia > (
                                                                                                       SELECT vigencia
                                                                                                         FROM folhapagamento.configuracao_empenho_lla
                                                                                                        WHERE timestamp            = tsTimestampIMA
                                                                                                          AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                                                                                                          AND exercicio            = reRecordConta.exercicio
                                                                                                     )
                                                                                      AND cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                                                                                      AND exercicio            = reRecordConta.exercicio
                                                                                 )
                               AND configuracao_empenho_lla_lotacao.cod_configuracao_lla = reRecordConta.cod_configuracao_lla
                               AND configuracao_empenho_lla_lotacao.exercicio            = reRecordConta.exercicio
                                 ;

                            stSqlRH := '   SELECT cod_configuracao_lla
                                                , cod_orgao
                                                , exercicio
                                                , num_pao
                                                , timestamp
                                             FROM folhapagamento.configuracao_empenho_lla_lotacao
                                            WHERE timestamp IN (
                                                                 SELECT timestamp
                                                                   FROM folhapagamento.configuracao_empenho_lla
                                                                  WHERE vigencia > (
                                                                                     SELECT vigencia
                                                                                       FROM folhapagamento.configuracao_empenho_lla
                                                                                      WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                                                        AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                                        AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                   )
                                                                    AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                    AND exercicio            = '|| quote_literal(reRecordConta.exercicio)  ||'
                                                                )
                                              AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                              AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                         GROUP BY cod_configuracao_lla
                                                , cod_orgao
                                                , exercicio
                                                , num_pao
                                                , timestamp
                                                ;
                                       ';
                            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                DELETE
                                  FROM folhapagamento.configuracao_empenho_lla_lotacao
                                 WHERE cod_configuracao_lla = reRecordRH.cod_configuracao_lla
                                   AND cod_orgao            = reRecordRH.cod_orgao
                                   AND exercicio            = reRecordRH.exercicio
                                   AND num_pao              = reRecordRH.num_pao
                                   AND timestamp            = reRecordRH.timestamp
                                     ;
                                INSERT
                                  INTO folhapagamento.configuracao_empenho_lla_lotacao
                                     ( cod_configuracao_lla
                                     , cod_orgao
                                     , exercicio
                                     , num_pao
                                     , timestamp
                                     )
                                VALUES
                                     ( reRecordRH.cod_configuracao_lla
                                     , reRecordRH.cod_orgao
                                     , reRecordRH.exercicio
                                     , reRecordRH.num_pao
                                     , reRecordRH.timestamp
                                     );
                            END LOOP;
                            ALTER TABLE folhapagamento.configuracao_empenho_lla_lotacao ADD CONSTRAINT pk_configuracao_empenho_lla_lotacao PRIMARY KEY (cod_orgao, exercicio, cod_configuracao_lla, timestamp);

            END LOOP;

            -- UPDATE IMA.CONFIGURACAO_BANPARA_ORGAO
            stSqlConta := '
                              SELECT cod_empresa
                                   , num_orgao_banpara
                                   , MAX(timestamp) AS timestamp
                                FROM ima.configuracao_banpara
                               WHERE vigencia >= (
                                                   SELECT MAX(vigencia)
                                                     FROM ima.configuracao_banpara
                                                    WHERE vigencia <= now()::date
                                                 )
                            GROUP BY cod_empresa
                                   , num_orgao_banpara
                                   ;
                          ';
            FOR reRecordConta IN EXECUTE stSqlConta LOOP

                            SELECT MAX(timestamp) AS timestamp
                              INTO tsTimestampIMA
                              FROM ima.configuracao_banpara
                             WHERE vigencia = (
                                                SELECT MAX(vigencia)
                                                  FROM ima.configuracao_banpara
                                                 WHERE vigencia         <= now()::date
                                                   AND cod_empresa       = reRecordConta.cod_empresa
                                                   AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                              )
                               AND cod_empresa       = reRecordConta.cod_empresa
                               AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                 ;

                            ALTER TABLE ima.configuracao_banpara_orgao DROP CONSTRAINT pk_configuracao_banpara_orgao;

                            INSERT
                              INTO ima.configuracao_banpara
                                 ( cod_empresa
                                 , num_orgao_banpara
                                 , descricao
                                 , timestamp
                                 , vigencia
                                 )
                            SELECT cod_empresa
                                 , num_orgao_banpara
                                 , descricao || ' MIGRA ORGANOGRAMA'
                                 , now()::timestamp(3)
                                 , (
                                     SELECT dt_inicial
                                       FROM folhapagamento.periodo_movimentacao
                                      WHERE cod_periodo_movimentacao = (
                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                           FROM folhapagamento.periodo_movimentacao
                                                                       )
                                   )
                              FROM ima.configuracao_banpara
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_empresa       = reRecordConta.cod_empresa
                               AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                 ;

                            INSERT
                              INTO ima.configuracao_banpara_orgao
                                 ( cod_empresa
                                 , cod_orgao
                                 , num_orgao_banpara
                                 , timestamp
                                 )
                            SELECT cod_empresa
                                 , cod_orgao
                                 , num_orgao_banpara
                                 , now()::timestamp(3)
                              FROM ima.configuracao_banpara_orgao
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_empresa       = reRecordConta.cod_empresa
                               AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                 ;

                            INSERT
                              INTO ima.configuracao_banpara_local
                                 ( cod_empresa
                                 , num_orgao_banpara
                                 , cod_local
                                 , timestamp
                                 )
                            SELECT cod_empresa
                                 , num_orgao_banpara
                                 , cod_local
                                 , now()::timestamp(3)
                              FROM ima.configuracao_banpara_local
                             WHERE timestamp         = tsTimestampIMA
                               AND cod_empresa       = reRecordConta.cod_empresa
                               AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                 ;

                            UPDATE ima.configuracao_banpara_orgao
                               SET cod_orgao = de_para_orgao.cod_orgao_new
                              FROM organograma.de_para_orgao
                             WHERE configuracao_banpara_orgao.cod_orgao  = de_para_orgao.cod_orgao
                               AND configuracao_banpara_orgao.timestamp IN (
                                                                             SELECT timestamp
                                                                               FROM ima.configuracao_banpara
                                                                              WHERE vigencia > (
                                                                                                 SELECT vigencia
                                                                                                   FROM ima.configuracao_banpara
                                                                                                  WHERE timestamp         = tsTimestampIMA
                                                                                                    AND cod_empresa       = reRecordConta.cod_empresa
                                                                                                    AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                                                                               )
                                                                                AND cod_empresa       = reRecordConta.cod_empresa
                                                                                AND num_orgao_banpara = reRecordConta.num_orgao_banpara
                                                                           )
                               AND configuracao_banpara_orgao.cod_empresa       = reRecordConta.cod_empresa
                               AND configuracao_banpara_orgao.num_orgao_banpara = reRecordConta.num_orgao_banpara
                                 ;

                            stSqlRH := '   SELECT cod_empresa
                                                , cod_orgao
                                                , num_orgao_banpara
                                                , timestamp
                                             FROM ima.configuracao_banpara_orgao
                                            WHERE timestamp IN (
                                                                 SELECT timestamp
                                                                   FROM ima.configuracao_banpara
                                                                  WHERE vigencia > (
                                                                                     SELECT vigencia
                                                                                       FROM ima.configuracao_banpara
                                                                                      WHERE timestamp         = '|| quote_literal(tsTimestampIMA)   ||'
                                                                                        AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                                        AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                                   )
                                                                    AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                    AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                )
                                              AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                              AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                         GROUP BY cod_empresa
                                                , cod_orgao
                                                , num_orgao_banpara
                                                , timestamp
                                                ;
                                       ';
                            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                DELETE
                                  FROM ima.configuracao_banpara_orgao
                                 WHERE cod_empresa       = reRecordRH.cod_empresa
                                   AND cod_orgao         = reRecordRH.cod_orgao
                                   AND num_orgao_banpara = reRecordRH.num_orgao_banpara
                                   AND timestamp         = reRecordRH.timestamp
                                     ;
                                INSERT
                                  INTO ima.configuracao_banpara_orgao
                                     ( cod_empresa
                                     , cod_orgao
                                     , num_orgao_banpara
                                     , timestamp
                                     )
                                VALUES
                                     ( reRecordRH.cod_empresa
                                     , reRecordRH.cod_orgao
                                     , reRecordRH.num_orgao_banpara
                                     , reRecordRH.timestamp
                                     );
                            END LOOP;
                            ALTER TABLE ima.configuracao_banpara_orgao ADD CONSTRAINT pk_configuracao_banpara_orgao PRIMARY KEY (cod_empresa, num_orgao_banpara, cod_orgao, timestamp);

            END LOOP;

            -- UPDATE IMA.CONFIGURACAO_BB_ORGAO
            stSqlConta := '
                              SELECT cod_convenio
                                   , cod_banco
                                   , cod_agencia
                                   , cod_conta_corrente
                                   , MAX(timestamp) AS timestamp
                                FROM ima.configuracao_bb_conta
                               WHERE vigencia >= (
                                                   SELECT MAX(vigencia)
                                                     FROM ima.configuracao_bb_conta
                                                    WHERE vigencia <= now()::date
                                                 )
                            GROUP BY cod_convenio
                                   , cod_banco
                                   , cod_agencia
                                   , cod_conta_corrente
                                   ;
                          ';
            FOR reRecordConta IN EXECUTE stSqlConta LOOP

                            SELECT MAX(timestamp) AS timestamp
                              INTO tsTimestampIMA
                              FROM ima.configuracao_bb_conta
                             WHERE vigencia = (
                                                SELECT MAX(vigencia)
                                                  FROM ima.configuracao_bb_conta
                                                 WHERE vigencia <= now()::date
                                                   AND cod_convenio       = reRecordConta.cod_convenio
                                                   AND cod_banco          = reRecordConta.cod_banco
                                                   AND cod_agencia        = reRecordConta.cod_agencia
                                                   AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                              )
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            ALTER TABLE ima.configuracao_bb_orgao DROP CONSTRAINT pk_configuracao_bb_orgao;

                            INSERT
                              INTO ima.configuracao_bb_conta
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , descricao
                                 , timestamp
                                 , vigencia
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , descricao || ' MIGRA ORGANOGRAMA'
                                 , now()::timestamp(3)
                                 , (
                                     SELECT dt_inicial
                                       FROM folhapagamento.periodo_movimentacao
                                      WHERE cod_periodo_movimentacao = (
                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                           FROM folhapagamento.periodo_movimentacao
                                                                       )
                                   )
                              FROM ima.configuracao_bb_conta
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            INSERT
                              INTO ima.configuracao_bb_orgao
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , timestamp
                                 , cod_orgao
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , now()::timestamp(3)
                                 , cod_orgao
                              FROM ima.configuracao_bb_orgao
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            INSERT
                              INTO ima.configuracao_bb_local
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , timestamp
                                 , cod_local
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , now()::timestamp(3)
                                 , cod_local
                              FROM ima.configuracao_bb_local
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            UPDATE ima.configuracao_bb_orgao
                               SET cod_orgao = de_para_orgao.cod_orgao_new
                              FROM organograma.de_para_orgao
                             WHERE configuracao_bb_orgao.cod_orgao  = de_para_orgao.cod_orgao
                               AND configuracao_bb_orgao.timestamp IN (
                                                                        SELECT timestamp
                                                                          FROM ima.configuracao_bb_conta
                                                                         WHERE vigencia > (
                                                                                            SELECT vigencia
                                                                                              FROM ima.configuracao_bb_conta
                                                                                             WHERE timestamp = tsTimestampIMA
                                                                                               AND cod_convenio       = reRecordConta.cod_convenio
                                                                                               AND cod_banco          = reRecordConta.cod_banco
                                                                                               AND cod_agencia        = reRecordConta.cod_agencia
                                                                                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                                                                          )
                                                                           AND cod_convenio       = reRecordConta.cod_convenio
                                                                           AND cod_banco          = reRecordConta.cod_banco
                                                                           AND cod_agencia        = reRecordConta.cod_agencia
                                                                           AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                                                       )
                               AND configuracao_bb_orgao.cod_convenio       = reRecordConta.cod_convenio
                               AND configuracao_bb_orgao.cod_banco          = reRecordConta.cod_banco
                               AND configuracao_bb_orgao.cod_agencia        = reRecordConta.cod_agencia
                               AND configuracao_bb_orgao.cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            stSqlRH := '   SELECT cod_convenio
                                                , cod_banco
                                                , cod_agencia
                                                , cod_conta_corrente
                                                , timestamp
                                                , cod_orgao
                                             FROM ima.configuracao_bb_orgao
                                            WHERE timestamp IN (
                                                                 SELECT timestamp
                                                                   FROM ima.configuracao_bb_conta
                                                                  WHERE vigencia > (
                                                                                     SELECT vigencia
                                                                                       FROM ima.configuracao_bb_conta
                                                                                      WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                        AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                        AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                        AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                        AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                   )
                                                                    AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                    AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                    AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                    AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                )
                                              AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                              AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                              AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                              AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                         GROUP BY cod_convenio
                                                , cod_banco
                                                , cod_agencia
                                                , cod_conta_corrente
                                                , timestamp
                                                , cod_orgao
                                                ;
                                       ';
                            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                DELETE
                                  FROM ima.configuracao_bb_orgao
                                 WHERE cod_convenio       = reRecordRH.cod_convenio
                                   AND cod_banco          = reRecordRH.cod_banco
                                   AND cod_agencia        = reRecordRH.cod_agencia
                                   AND cod_conta_corrente = reRecordRH.cod_conta_corrente
                                   AND timestamp          = reRecordRH.timestamp
                                   AND cod_orgao          = reRecordRH.cod_orgao
                                     ;
                                INSERT
                                  INTO ima.configuracao_bb_orgao
                                     ( cod_convenio
                                     , cod_banco
                                     , cod_agencia
                                     , cod_conta_corrente
                                     , timestamp
                                     , cod_orgao
                                     )
                                VALUES
                                     ( reRecordRH.cod_convenio
                                     , reRecordRH.cod_banco
                                     , reRecordRH.cod_agencia
                                     , reRecordRH.cod_conta_corrente
                                     , reRecordRH.timestamp
                                     , reRecordRH.cod_orgao
                                     );
                            END LOOP;
                            ALTER TABLE ima.configuracao_bb_orgao ADD CONSTRAINT pk_configuracao_bb_orgao PRIMARY KEY (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao, timestamp);

            END LOOP;

            -- UPDATE IMA.CONFIGURACAO_BESC_ORGAO
            stSqlConta := '
                              SELECT cod_convenio
                                   , cod_banco
                                   , cod_agencia
                                   , cod_conta_corrente
                                   , MAX(timestamp) AS timestamp
                                FROM ima.configuracao_besc_conta
                               WHERE vigencia >= (
                                                   SELECT MAX(vigencia)
                                                     FROM ima.configuracao_besc_conta
                                                    WHERE vigencia <= now()::date
                                                 )
                            GROUP BY cod_convenio
                                   , cod_banco
                                   , cod_agencia
                                   , cod_conta_corrente
                                   ;
                          ';
            FOR reRecordConta IN EXECUTE stSqlConta LOOP

                            SELECT MAX(timestamp) AS timestamp
                              INTO tsTimestampIMA
                              FROM ima.configuracao_besc_conta
                             WHERE vigencia = (
                                                SELECT MAX(vigencia)
                                                  FROM ima.configuracao_besc_conta
                                                 WHERE vigencia <= now()::date
                                                   AND cod_convenio       = reRecordConta.cod_convenio
                                                   AND cod_banco          = reRecordConta.cod_banco
                                                   AND cod_agencia        = reRecordConta.cod_agencia
                                                   AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                              ) 
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            ALTER TABLE ima.configuracao_besc_orgao DROP CONSTRAINT pk_configuracao_besc_orgao;

                            INSERT
                              INTO ima.configuracao_besc_conta
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , descricao
                                 , timestamp
                                 , vigencia
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , descricao || ' MIGRA ORGANOGRAMA'
                                 , now()::timestamp(3)
                                 , (
                                     SELECT dt_inicial
                                       FROM folhapagamento.periodo_movimentacao
                                      WHERE cod_periodo_movimentacao = (
                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                           FROM folhapagamento.periodo_movimentacao
                                                                       )
                                   )
                              FROM ima.configuracao_besc_conta
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            INSERT
                              INTO ima.configuracao_besc_orgao
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , timestamp
                                 , cod_orgao
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , now()::timestamp(3)
                                 , cod_orgao
                              FROM ima.configuracao_besc_orgao
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            INSERT
                              INTO ima.configuracao_besc_local
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , timestamp
                                 , cod_local
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , now()::timestamp(3)
                                 , cod_local
                              FROM ima.configuracao_besc_local
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            UPDATE ima.configuracao_besc_orgao
                               SET cod_orgao = de_para_orgao.cod_orgao_new
                              FROM organograma.de_para_orgao
                             WHERE configuracao_besc_orgao.cod_orgao  = de_para_orgao.cod_orgao
                               AND configuracao_besc_orgao.timestamp IN (
                                                                          SELECT timestamp
                                                                            FROM ima.configuracao_besc_conta
                                                                           WHERE vigencia > (
                                                                                              SELECT vigencia
                                                                                                FROM ima.configuracao_besc_conta
                                                                                               WHERE timestamp          = tsTimestampIMA
                                                                                                 AND cod_convenio       = reRecordConta.cod_convenio
                                                                                                 AND cod_banco          = reRecordConta.cod_banco
                                                                                                 AND cod_agencia        = reRecordConta.cod_agencia
                                                                                                 AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                                                                            )
                                                                             AND cod_convenio       = reRecordConta.cod_convenio
                                                                             AND cod_banco          = reRecordConta.cod_banco
                                                                             AND cod_agencia        = reRecordConta.cod_agencia
                                                                             AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                                                         )
                               AND configuracao_besc_orgao.cod_convenio       = reRecordConta.cod_convenio
                               AND configuracao_besc_orgao.cod_banco          = reRecordConta.cod_banco
                               AND configuracao_besc_orgao.cod_agencia        = reRecordConta.cod_agencia
                               AND configuracao_besc_orgao.cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            stSqlRH := '   SELECT cod_convenio
                                                , cod_banco
                                                , cod_agencia
                                                , cod_conta_corrente
                                                , timestamp
                                                , cod_orgao
                                             FROM ima.configuracao_besc_orgao
                                            WHERE timestamp IN (
                                                                 SELECT timestamp
                                                                   FROM ima.configuracao_besc_conta
                                                                  WHERE vigencia > (
                                                                                     SELECT vigencia
                                                                                       FROM ima.configuracao_besc_conta
                                                                                      WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                        AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                        AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                        AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                        AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                   )
                                                                    AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                    AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                    AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                    AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                )
                                              AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                              AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                              AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                              AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                         GROUP BY cod_convenio
                                                , cod_banco
                                                , cod_agencia
                                                , cod_conta_corrente
                                                , timestamp
                                                , cod_orgao
                                                ;
                                       ';
                            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                DELETE
                                  FROM ima.configuracao_besc_orgao
                                 WHERE cod_convenio       = reRecordRH.cod_convenio
                                   AND cod_banco          = reRecordRH.cod_banco
                                   AND cod_agencia        = reRecordRH.cod_agencia
                                   AND cod_conta_corrente = reRecordRH.cod_conta_corrente
                                   AND timestamp          = reRecordRH.timestamp
                                   AND cod_orgao          = reRecordRH.cod_orgao
                                     ;
                                INSERT
                                  INTO ima.configuracao_besc_orgao
                                     ( cod_convenio
                                     , cod_banco
                                     , cod_agencia
                                     , cod_conta_corrente
                                     , timestamp
                                     , cod_orgao
                                     )
                                VALUES
                                     ( reRecordRH.cod_convenio
                                     , reRecordRH.cod_banco
                                     , reRecordRH.cod_agencia
                                     , reRecordRH.cod_conta_corrente
                                     , reRecordRH.timestamp
                                     , reRecordRH.cod_orgao
                                     );
                            END LOOP;
                            ALTER TABLE ima.configuracao_besc_orgao ADD CONSTRAINT pk_configuracao_besc_orgao PRIMARY KEY (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao, timestamp);

            END LOOP;

            -- UPDATE IMA.CONFIGURACAO_BANRISUL_ORGAO
            stSqlConta := '
                              SELECT cod_convenio
                                   , cod_banco
                                   , cod_agencia
                                   , cod_conta_corrente
                                   , MAX(timestamp) AS timestamp
                                FROM ima.configuracao_banrisul_conta
                               WHERE vigencia >= (
                                                   SELECT MAX(vigencia)
                                                     FROM ima.configuracao_banrisul_conta
                                                    WHERE vigencia <= now()::date
                                                 )
                            GROUP BY cod_convenio
                                   , cod_banco
                                   , cod_agencia
                                   , cod_conta_corrente
                                   ;
                          ';
            FOR reRecordConta IN EXECUTE stSqlConta LOOP

                            SELECT MAX(timestamp) AS timestamp
                              INTO tsTimestampIMA
                              FROM ima.configuracao_banrisul_conta
                             WHERE vigencia = (
                                                SELECT MAX(vigencia)
                                                  FROM ima.configuracao_banrisul_conta
                                                 WHERE vigencia <= now()::date
                                                   AND cod_convenio       = reRecordConta.cod_convenio
                                                   AND cod_banco          = reRecordConta.cod_banco
                                                   AND cod_agencia        = reRecordConta.cod_agencia
                                                   AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                              )
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            ALTER TABLE ima.configuracao_banrisul_orgao DROP CONSTRAINT pk_configuracao_banrisul_orgao;

                            INSERT
                              INTO ima.configuracao_banrisul_conta
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , descricao
                                 , timestamp
                                 , vigencia
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , descricao || ' MIGRA ORGANOGRAMA'
                                 , now()::timestamp(3)
                                 , (
                                     SELECT dt_inicial
                                       FROM folhapagamento.periodo_movimentacao
                                      WHERE cod_periodo_movimentacao = (
                                                                         SELECT MAX(cod_periodo_movimentacao)
                                                                           FROM folhapagamento.periodo_movimentacao
                                                                       )
                                   )
                              FROM ima.configuracao_banrisul_conta
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            INSERT
                              INTO ima.configuracao_banrisul_orgao
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , timestamp
                                 , cod_orgao
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , now()::timestamp(3)
                                 , cod_orgao
                              FROM ima.configuracao_banrisul_orgao
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            INSERT
                              INTO ima.configuracao_banrisul_local
                                 ( cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , timestamp
                                 , cod_local
                                 )
                            SELECT cod_convenio
                                 , cod_banco
                                 , cod_agencia
                                 , cod_conta_corrente
                                 , now()::timestamp(3)
                                 , cod_local
                              FROM ima.configuracao_banrisul_local
                             WHERE timestamp          = tsTimestampIMA
                               AND cod_convenio       = reRecordConta.cod_convenio
                               AND cod_banco          = reRecordConta.cod_banco
                               AND cod_agencia        = reRecordConta.cod_agencia
                               AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            UPDATE ima.configuracao_banrisul_orgao
                               SET cod_orgao = de_para_orgao.cod_orgao_new
                              FROM organograma.de_para_orgao
                             WHERE configuracao_banrisul_orgao.cod_orgao  = de_para_orgao.cod_orgao
                               AND configuracao_banrisul_orgao.timestamp IN (
                                                                              SELECT timestamp
                                                                                FROM ima.configuracao_banrisul_conta
                                                                               WHERE vigencia > (
                                                                                                  SELECT vigencia
                                                                                                    FROM ima.configuracao_banrisul_conta
                                                                                                   WHERE timestamp          = tsTimestampIMA
                                                                                                     AND cod_convenio       = reRecordConta.cod_convenio
                                                                                                     AND cod_banco          = reRecordConta.cod_banco
                                                                                                     AND cod_agencia        = reRecordConta.cod_agencia
                                                                                                     AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                                                                                )
                                                                                 AND cod_convenio       = reRecordConta.cod_convenio
                                                                                 AND cod_banco          = reRecordConta.cod_banco
                                                                                 AND cod_agencia        = reRecordConta.cod_agencia
                                                                                 AND cod_conta_corrente = reRecordConta.cod_conta_corrente
                                                                             )
                               AND configuracao_banrisul_orgao.cod_convenio       = reRecordConta.cod_convenio
                               AND configuracao_banrisul_orgao.cod_banco          = reRecordConta.cod_banco
                               AND configuracao_banrisul_orgao.cod_agencia        = reRecordConta.cod_agencia
                               AND configuracao_banrisul_orgao.cod_conta_corrente = reRecordConta.cod_conta_corrente
                                 ;

                            stSqlRH := '   SELECT cod_convenio
                                                , cod_banco
                                                , cod_agencia
                                                , cod_conta_corrente
                                                , timestamp
                                                , cod_orgao
                                             FROM ima.configuracao_banrisul_orgao
                                            WHERE timestamp IN (
                                                                 SELECT timestamp
                                                                   FROM ima.configuracao_banrisul_conta
                                                                  WHERE vigencia > (
                                                                                     SELECT vigencia
                                                                                       FROM ima.configuracao_banrisul_conta
                                                                                      WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                        AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                        AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                        AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                        AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                   )
                                                                    AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                    AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                    AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                    AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                ) 
                                              AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                              AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                              AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                              AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                         GROUP BY cod_convenio
                                                , cod_banco
                                                , cod_agencia
                                                , cod_conta_corrente
                                                , timestamp
                                                , cod_orgao
                                                ;
                                       ';
                            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                DELETE
                                  FROM ima.configuracao_banrisul_orgao
                                 WHERE cod_convenio       = reRecordRH.cod_convenio
                                   AND cod_banco          = reRecordRH.cod_banco
                                   AND cod_agencia        = reRecordRH.cod_agencia
                                   AND cod_conta_corrente = reRecordRH.cod_conta_corrente
                                   AND timestamp          = reRecordRH.timestamp
                                   AND cod_orgao          = reRecordRH.cod_orgao
                                     ;
                                INSERT
                                  INTO ima.configuracao_banrisul_orgao
                                     ( cod_convenio
                                     , cod_banco
                                     , cod_agencia
                                     , cod_conta_corrente
                                     , timestamp
                                     , cod_orgao
                                     )
                                VALUES
                                     ( reRecordRH.cod_convenio
                                     , reRecordRH.cod_banco
                                     , reRecordRH.cod_agencia
                                     , reRecordRH.cod_conta_corrente
                                     , reRecordRH.timestamp
                                     , reRecordRH.cod_orgao
                                     );
                            END LOOP;
                            ALTER TABLE ima.configuracao_banrisul_orgao ADD CONSTRAINT pk_configuracao_banrisul_orgao PRIMARY KEY (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao, timestamp);

            END LOOP;

            -- UPDATE PESSOAL.CONTRATO_PENSIONISTA_ORGAO
            stSqlRH := '     SELECT ppen.*
                               FROM pessoal.contrato_pensionista_orgao  AS ppen
                         INNER JOIN (
                                         SELECT DISTINCT ON (cod_contrato)
                                                cod_contrato
                                              , MAX (timestamp)  AS timestamp
                                           FROM pessoal.contrato_pensionista_orgao
                                       GROUP BY cod_contrato
                                    )                                AS temp
                                 ON ppen.cod_contrato = temp.cod_contrato
                                AND ppen.timestamp    = temp.timestamp
                                  ;
                       ';
            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                INSERT
                  INTO pessoal.contrato_pensionista_orgao
                     ( cod_contrato
                     , cod_orgao
                     )
                VALUES
                     ( reRecordRH.cod_contrato
                     , ( SELECT cod_orgao_new
                           FROM organograma.de_para_orgao
                          WHERE cod_orgao = reRecordRH.cod_orgao
                       )
                     );
            END LOOP;

            -- UPDATE PESSOAL.CONTRATO_SERVIDOR_ORGAO
            stSqlRH := '     SELECT pcon.*
                               FROM pessoal.contrato_servidor_orgao  AS pcon
                         INNER JOIN (
                                         SELECT DISTINCT ON (cod_contrato)
                                                cod_contrato
                                              , MAX (timestamp)  AS timestamp
                                           FROM pessoal.contrato_servidor_orgao
                                       GROUP BY cod_contrato
                                    )                                AS temp
                                 ON pcon.cod_contrato = temp.cod_contrato
                                AND pcon.timestamp    = temp.timestamp
                                  ;
                       ';
            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                INSERT
                  INTO pessoal.contrato_servidor_orgao
                     ( cod_contrato
                     , cod_orgao
                     )
                VALUES
                     ( reRecordRH.cod_contrato
                     , ( SELECT cod_orgao_new
                           FROM organograma.de_para_orgao
                          WHERE cod_orgao = reRecordRH.cod_orgao 
                       )
                     );
            END LOOP;

            -- UPDATE PONTO.CONFIGURACAO_LOTACAO
            ALTER TABLE ponto.configuracao_lotacao DROP CONSTRAINT pk_configuracao_lotacao;
            UPDATE ponto.configuracao_lotacao
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE configuracao_lotacao.cod_orgao = de_para_orgao.cod_orgao;
            stSqlRH := '   SELECT cod_configuracao
                                , timestamp
                                , cod_orgao
                             FROM ponto.configuracao_lotacao
                         GROUP BY cod_configuracao
                                , timestamp
                                , cod_orgao
                                ;
                       ';
            FOR reRecordRH IN EXECUTE stSqlRH LOOP
                DELETE
                  FROM ponto.configuracao_lotacao
                 WHERE cod_configuracao = reRecordRH.cod_configuracao
                   AND timestamp        = reRecordRH.timestamp       
                   AND cod_orgao        = reRecordRH.cod_orgao
                     ;
                INSERT
                  INTO ponto.configuracao_lotacao
                     ( cod_configuracao
                     , timestamp       
                     , cod_orgao       
                     )
                VALUES
                     ( reRecordRH.cod_configuracao
                     , reRecordRH.timestamp       
                     , reRecordRH.cod_orgao       
                     );
            END LOOP;
            ALTER TABLE ponto.configuracao_lotacao ADD CONSTRAINT pk_configuracao_lotacao PRIMARY KEY (cod_configuracao, timestamp, cod_orgao);

            -- UPDATE SW_ANDAMENTO
            DROP TRIGGER tr_atualiza_ultimo_andamento ON sw_andamento;
            DROP TRIGGER tr_exclui_ultimo_andamento   ON sw_andamento;

            stSqlMAX := '       SELECT swa.*
                                  FROM sw_andamento              AS swa
                            INNER JOIN (
                                            SELECT DISTINCT ON (ano_exercicio, cod_processo)
                                                   ano_exercicio
                                                 , cod_processo
                                                 , MAX(cod_andamento) AS cod_andamento
                                              FROM sw_andamento
                                          GROUP BY ano_exercicio
                                                 , cod_processo
                                       )                         AS temp
                                    ON swa.ano_exercicio = temp.ano_exercicio
                                   AND swa.cod_processo  = temp.cod_processo
                                   AND swa.cod_andamento = temp.cod_andamento
                                     ;
                        ';
            FOR reRecordMAX IN EXECUTE stSqlMAX LOOP
                UPDATE sw_andamento
                   SET cod_orgao = de_para_orgao.cod_orgao_new
                  FROM organograma.de_para_orgao
                 WHERE ano_exercicio = reRecordMAX.ano_exercicio
                   AND cod_processo  = reRecordMAX.cod_processo
                   AND cod_andamento = reRecordMAX.cod_andamento
                   AND sw_andamento.cod_orgao = de_para_orgao.cod_orgao;
            END LOOP;

            -- UPDATE SW_ANDAMENTO_PADRAO
            ALTER TABLE sw_andamento_padrao DROP CONSTRAINT pk_andamento_padrao;
            UPDATE sw_andamento_padrao
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE sw_andamento_padrao.cod_orgao = de_para_orgao.cod_orgao;
            ALTER TABLE sw_andamento_padrao ADD CONSTRAINT pk_andamento_padrao PRIMARY KEY (num_passagens, cod_classificacao, cod_assunto, cod_orgao, ordem);

            -- UPDATE SW_ULTIMO_ANDAMENTO
            UPDATE sw_ultimo_andamento
               SET cod_orgao = de_para_orgao.cod_orgao_new
              FROM organograma.de_para_orgao
             WHERE sw_ultimo_andamento.cod_orgao = de_para_orgao.cod_orgao;

            CREATE TRIGGER tr_atualiza_ultimo_andamento AFTER INSERT OR UPDATE ON sw_andamento FOR EACH ROW EXECUTE PROCEDURE fn_atualiza_ultimo_andamento();
            CREATE TRIGGER tr_exclui_ultimo_andamento BEFORE DELETE ON sw_andamento FOR EACH ROW EXECUTE PROCEDURE fn_exclui_ultimo_andamento();


            -- TRECHO DINAMICO RH
            stSqlEnt := '  SELECT DISTINCT cod_entidade
                             FROM administracao.entidade_rh
                            WHERE cod_entidade <> ( SELECT CAST(valor as integer)
                                                      FROM administracao.configuracao
                                                     WHERE exercicio = (
                                                                         SELECT MAX(exercicio)
                                                                           FROM administracao.configuracao
                                                                          WHERE parametro = ''migra_orgao''
                                                                       )
                                                       AND parametro = ''cod_entidade_prefeitura''
                                                  )
                                ;
                        ';
    
            FOR reRecordEnt IN EXECUTE stSqlEnt LOOP

                -- UPDATE ESTAGIO.ESTAGIARIO_ESTAGIO
                stSqlUpdate := '    UPDATE estagio_'|| reRecordEnt.cod_entidade ||'.estagiario_estagio
                                       SET cod_orgao = de_para_orgao.cod_orgao_new
                                      FROM organograma.de_para_orgao
                                     WHERE estagiario_estagio.cod_orgao = de_para_orgao.cod_orgao
                                         ;
                               ';
                EXECUTE stSqlUpdate;

                -- UPDATE FOLHAPAGAMENTO.CONFIGURACAO_EMPENHO_LOTACAO
                stSqlConta := '
                                  SELECT cod_configuracao
                                       , exercicio
                                       , sequencia
                                       , MAX(timestamp) AS timestamp
                                    FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                   WHERE vigencia >= (
                                                       SELECT MAX(vigencia)
                                                         FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                        WHERE vigencia <= now()::date
                                                     )
                                GROUP BY cod_configuracao
                                       , exercicio
                                       , sequencia
                                       ;
                              ';

                FOR reRecordConta IN EXECUTE stSqlConta LOOP

                                stSqlUpdate := '
                                                UPDATE administracao.configuracao
                                                   SET valor = (
                                                                 SELECT MAX(timestamp) AS timestamp
                                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                                  WHERE vigencia = (
                                                                                     SELECT MAX(vigencia)
                                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                                                      WHERE vigencia        <= now()::date
                                                                                        AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                                        AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                        AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                                   )
                                                                    AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                    AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                    AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                               )
                                                 WHERE cod_modulo = 19
                                                   AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                                   AND parametro  = ''timestamp_vigencia_RH''
                                                     ;
                                           ';
                                EXECUTE stSqlUpdate;

                                SELECT valor::TIMESTAMP
                                  INTO tsTimestampIMA
                                  FROM administracao.configuracao
                                 WHERE cod_modulo = 19
                                   AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                   AND parametro  = 'timestamp_vigencia_RH'
                                     ;

                                stSqlUpdate := 'ALTER TABLE folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao DROP CONSTRAINT pk_configuracao_empenho_lotacao;';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                      ( exercicio_pao
                                                      , cod_configuracao
                                                      , exercicio_despesa
                                                      , cod_despesa
                                                      , num_pao
                                                      , exercicio
                                                      , sequencia
                                                      , timestamp
                                                      , vigencia
                                                      )
                                                 SELECT exercicio_pao
                                                      , cod_configuracao
                                                      , exercicio_despesa
                                                      , cod_despesa
                                                      , num_pao
                                                      , exercicio
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                      , (
                                                          SELECT dt_inicial
                                                            FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                           WHERE cod_periodo_movimentacao = (
                                                                                              SELECT MAX(cod_periodo_movimentacao)
                                                                                                FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                                                            )
                                                        )
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                  WHERE timestamp        = '|| quote_literal(tsTimestampIMA) ||'
                                                    AND cod_configuracao = '|| reRecordConta.cod_configuracao  ||'
                                                    AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia        = '|| reRecordConta.sequencia         ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_orgao
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_orgao
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA) ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao  ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia         ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_local
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_local
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_local
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_local
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA)  ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia        ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_atributo
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_atributo
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA) ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao  ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia         ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_atributo_valor
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , sequencia
                                                      , valor
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , sequencia
                                                      , valor
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_atributo_valor
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA) ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao  ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia         ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_situacao
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , situacao
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , situacao
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_situacao
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA)  ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia        ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_conta_despesa
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_conta
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_conta
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_conta_despesa
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA)  ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia        ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_subdivisao
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_sub_divisao
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_sub_divisao
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_subdivisao
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA)  ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia        ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;
                     
                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_evento
                                                      ( cod_configuracao
                                                      , exercicio
                                                      , cod_evento
                                                      , sequencia
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao
                                                      , exercicio
                                                      , cod_evento
                                                      , sequencia
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_evento
                                                  WHERE timestamp         = '|| quote_literal(tsTimestampIMA)  ||'
                                                    AND cod_configuracao  = '|| reRecordConta.cod_configuracao ||'
                                                    AND exercicio         = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND sequencia         = '|| reRecordConta.sequencia        ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 UPDATE folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao
                                                    SET cod_orgao = de_para_orgao.cod_orgao_new
                                                   FROM organograma.de_para_orgao
                                                  WHERE configuracao_empenho_lotacao.cod_orgao  = de_para_orgao.cod_orgao
                                                    AND configuracao_empenho_lotacao.timestamp IN (
                                                                                                    SELECT timestamp
                                                                                                      FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                                                                     WHERE vigencia > (
                                                                                                                        SELECT vigencia
                                                                                                                          FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                                                                                         WHERE timestamp        = '|| quote_literal(tsTimestampIMA)  ||'
                                                                                                                           AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                                                                           AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                                                           AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                                                                      )
                                                                                                       AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                                                       AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                                       AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                                                  )
                                                    AND configuracao_empenho_lotacao.cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                    AND configuracao_empenho_lotacao.exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                    AND configuracao_empenho_lotacao.sequencia        = '|| reRecordConta.sequencia        ||'
                                                                         ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlRH := '
                                               SELECT cod_configuracao
                                                    , cod_orgao
                                                    , exercicio
                                                    , sequencia
                                                    , timestamp
                                                 FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao
                                                WHERE timestamp IN (
                                                                     SELECT timestamp
                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                                      WHERE vigencia > (
                                                                                         SELECT vigencia
                                                                                           FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho
                                                                                          WHERE timestamp        = '|| quote_literal(tsTimestampIMA)  ||'
                                                                                            AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                                            AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                            AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                                       )
                                                                        AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                                        AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                        AND sequencia        = '|| reRecordConta.sequencia        ||'
                                                                    )
                                                  AND cod_configuracao = '|| reRecordConta.cod_configuracao ||'
                                                  AND exercicio        = '|| quote_literal(reRecordConta.exercicio) ||'
                                                  AND sequencia        = '|| reRecordConta.sequencia        ||'
                                             GROUP BY cod_configuracao
                                                    , cod_orgao
                                                    , exercicio
                                                    , sequencia
                                                    , timestamp
                                                    ;
                                           ';
                                FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                    stSqlUpdate := '    DELETE
                                                          FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao
                                                         WHERE cod_configuracao = '|| reRecordRH.cod_configuracao ||'
                                                           AND cod_orgao        = '|| reRecordRH.cod_orgao        ||'
                                                           AND exercicio        = '|| quote_literal(reRecordRH.exercicio) ||'
                                                           AND sequencia        = '|| reRecordRH.sequencia        ||'
                                                           AND timestamp        = '|| quote_literal(reRecordRH.timestamp) ||'
                                                             ;
                                                   ';
                                    EXECUTE stSqlUpdate;
                                    stSqlUpdate := '    INSERT
                                                          INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao
                                                             ( cod_configuracao
                                                             , cod_orgao
                                                             , exercicio
                                                             , sequencia
                                                             , timestamp
                                                             )
                                                        VALUES
                                                             ( '|| reRecordRH.cod_configuracao ||'
                                                             , '|| reRecordRH.cod_orgao        ||'
                                                             , '|| quote_literal(reRecordRH.exercicio) ||'
                                                             , '|| reRecordRH.sequencia        ||'
                                                             , '|| quote_literal(reRecordRH.timestamp) ||'
                                                             );
                                                   ';
                                    EXECUTE stSqlUpdate;
                                END LOOP;

                                stSqlUpdate := 'ALTER TABLE folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lotacao ADD CONSTRAINT pk_configuracao_empenho_lotacao PRIMARY KEY (exercicio, cod_configuracao, sequencia, timestamp, cod_orgao);';
                                EXECUTE stSqlUpdate;

                END LOOP;

                -- UPDATE FOLHAPAGAMENTO.CONFIGURACAO_EMPENHO_LLA_LOTACAO
                stSqlConta := '
                                  SELECT cod_configuracao_lla
                                       , exercicio
                                       , MAX(timestamp) AS timestamp
                                    FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                   WHERE vigencia >= (
                                                       SELECT MAX(vigencia)
                                                         FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                        WHERE vigencia <= now()::date
                                                     )
                                GROUP BY cod_configuracao_lla
                                       , exercicio
                                       ;
                              ';

                FOR reRecordConta IN EXECUTE stSqlConta LOOP

                                stSqlUpdate := '
                                                UPDATE administracao.configuracao
                                                   SET valor = (
                                                                 SELECT MAX(timestamp) AS timestamp
                                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                                  WHERE vigencia = (
                                                                                     SELECT MAX(vigencia)
                                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                                                      WHERE vigencia            <= now()::date
                                                                                        AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                                        AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                   )
                                                                    AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                    AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                               )
                                                 WHERE cod_modulo = 19
                                                   AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                                   AND parametro  = ''timestamp_vigencia_RH''
                                                     ;
                                           ';
                                EXECUTE stSqlUpdate;

                                SELECT valor::TIMESTAMP
                                  INTO tsTimestampIMA
                                  FROM administracao.configuracao
                                 WHERE cod_modulo = 19
                                   AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                   AND parametro  = 'timestamp_vigencia_RH'
                                     ;

                                stSqlUpdate := 'ALTER TABLE folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao DROP CONSTRAINT pk_configuracao_empenho_lla_lotacao;';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                    INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                       ( cod_configuracao_lla
                                                       , exercicio
                                                       , timestamp
                                                       , vigencia
                                                       )
                                                  SELECT cod_configuracao_lla
                                                       , exercicio
                                                       , now()::timestamp(3)
                                                       , (
                                                           SELECT dt_inicial
                                                             FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                            WHERE cod_periodo_movimentacao = (
                                                                                               SELECT MAX(cod_periodo_movimentacao)
                                                                                                 FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                                                             )
                                                         )
                                                    FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                   WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                     AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                     AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                       ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao
                                                      ( cod_configuracao_lla
                                                      , exercicio
                                                      , num_pao
                                                      , cod_orgao
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao_lla
                                                      , exercicio
                                                      , num_pao
                                                      , cod_orgao
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao
                                                  WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                    AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                    AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_local
                                                      ( cod_configuracao_lla
                                                      , exercicio
                                                      , num_pao
                                                      , cod_local
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao_lla
                                                      , exercicio
                                                      , num_pao
                                                      , cod_local
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_local
                                                  WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                    AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                    AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_atributo
                                                      ( cod_configuracao_lla
                                                      , exercicio
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao_lla
                                                      , exercicio
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_atributo
                                                  WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                    AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                    AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 INSERT
                                                   INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_atributo_valor
                                                      ( cod_configuracao_lla
                                                      , exercicio
                                                      , num_pao
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , valor
                                                      , timestamp
                                                      )
                                                 SELECT cod_configuracao_lla
                                                      , exercicio
                                                      , num_pao
                                                      , cod_atributo
                                                      , cod_modulo
                                                      , cod_cadastro
                                                      , valor
                                                      , now()::timestamp(3)
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_atributo_valor
                                                  WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                    AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                    AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                      ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                 UPDATE folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao
                                                    SET cod_orgao = de_para_orgao.cod_orgao_new
                                                   FROM organograma.de_para_orgao
                                                  WHERE configuracao_empenho_lla_lotacao.cod_orgao  = de_para_orgao.cod_orgao
                                                    AND configuracao_empenho_lla_lotacao.timestamp IN (
                                                                                                        SELECT timestamp
                                                                                                          FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                                                                         WHERE vigencia > (
                                                                                                                            SELECT vigencia
                                                                                                                              FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                                                                                             WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                                                                                               AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                                                                               AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                                                          )
                                                                                                           AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                                                           AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                                      )
                                                    AND configuracao_empenho_lla_lotacao.cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                    AND configuracao_empenho_lla_lotacao.exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                      ;

                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlRH := '   SELECT cod_configuracao_lla
                                                    , cod_orgao
                                                    , exercicio
                                                    , num_pao
                                                    , timestamp
                                                 FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao
                                                WHERE timestamp IN (
                                                                     SELECT timestamp
                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                                      WHERE vigencia > (
                                                                                         SELECT vigencia
                                                                                           FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla
                                                                                          WHERE timestamp            = '|| quote_literal(tsTimestampIMA)      ||'
                                                                                            AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                                            AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                                       )
                                                                        AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                                        AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                                                    )
                                                  AND cod_configuracao_lla = '|| reRecordConta.cod_configuracao_lla ||'
                                                  AND exercicio            = '|| quote_literal(reRecordConta.exercicio) ||'
                                             GROUP BY cod_configuracao_lla
                                                    , cod_orgao
                                                    , exercicio
                                                    , num_pao
                                                    , timestamp
                                                    ;
                                           ';
                                FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                    stSqlUpdate := '
                                                     DELETE
                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao
                                                      WHERE cod_configuracao_lla = '|| reRecordRH.cod_configuracao_lla ||'
                                                        AND cod_orgao            = '|| reRecordRH.cod_orgao            ||'
                                                        AND exercicio            = '|| quote_literal(reRecordRH.exercicio) ||'
                                                        AND num_pao              = '|| reRecordRH.num_pao              ||'
                                                        AND timestamp            = '|| quote_literal(reRecordRH.timestamp) ||'
                                                          ;
                                                   ';
                                    EXECUTE stSqlUpdate;
                                    stSqlUpdate := '
                                                     INSERT
                                                       INTO folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao
                                                          ( cod_configuracao_lla
                                                          , cod_orgao
                                                          , exercicio
                                                          , num_pao
                                                          , timestamp
                                                          )
                                                     VALUES
                                                          ( '|| reRecordRH.cod_configuracao_lla ||'
                                                          , '|| reRecordRH.cod_orgao            ||'
                                                          , '|| quote_literal(reRecordRH.exercicio) ||'
                                                          , '|| reRecordRH.num_pao              ||'
                                                          , '|| quote_literal(reRecordRH.timestamp) ||'
                                                          );
                                                   ';
                                    EXECUTE stSqlUpdate;
                                END LOOP;

                                stSqlUpdate := 'ALTER TABLE folhapagamento_'|| reRecordEnt.cod_entidade ||'.configuracao_empenho_lla_lotacao ADD CONSTRAINT pk_configuracao_empenho_lla_lotacao PRIMARY KEY (cod_orgao, exercicio, cod_configuracao_lla, timestamp);';
                                EXECUTE stSqlUpdate;

                END LOOP;

                -- UPDATE IMA.CONFIGURACAO_BANPARA_ORGAO
                stSqlConta := '
                                  SELECT cod_empresa
                                       , num_orgao_banpara
                                       , MAX(timestamp) AS timestamp
                                    FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                   WHERE vigencia >= (
                                                       SELECT MAX(vigencia)
                                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                        WHERE vigencia <= now()::date
                                                     )
                                GROUP BY cod_empresa
                                       , num_orgao_banpara
                                       ;
                              ';
                FOR reRecordConta IN EXECUTE stSqlConta LOOP

                                stSqlUpdate := '
                                                UPDATE administracao.configuracao
                                                   SET valor = (
                                                                    SELECT MAX(timestamp) AS timestamp
                                                                      FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                                     WHERE vigencia = (
                                                                                        SELECT MAX(vigencia)
                                                                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                                                         WHERE vigencia         <= now()::date
                                                                                           AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                                           AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                                      )
                                                                       AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                       AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                               )
                                                 WHERE cod_modulo = 19
                                                   AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                                   AND parametro  = ''timestamp_vigencia_RH''
                                                     ;
                                           ';
                                EXECUTE stSqlUpdate;

                                SELECT valor::TIMESTAMP
                                  INTO tsTimestampIMA
                                  FROM administracao.configuracao
                                 WHERE cod_modulo = 19
                                   AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                   AND parametro  = 'timestamp_vigencia_RH'
                                     ;

                                stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.CONFIGURACAO_BANPARA_ORGAO DROP CONSTRAINT pk_configuracao_banpara_orgao;';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                INSERT
                                                  INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                     ( cod_empresa
                                                     , num_orgao_banpara
                                                     , descricao
                                                     , timestamp
                                                     , vigencia
                                                     )
                                                SELECT cod_empresa
                                                     , num_orgao_banpara
                                                     , descricao || '' MIGRA ORGANOGRAMA''
                                                     , now()::timestamp(3)
                                                     , (
                                                         SELECT dt_inicial
                                                           FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                          WHERE cod_periodo_movimentacao = (
                                                                                             SELECT MAX(cod_periodo_movimentacao)
                                                                                               FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                                                           )
                                                       )
                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                 WHERE timestamp         = '|| quote_literal(tsTimestampIMA)   ||'
                                                   AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                   AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                     ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                INSERT
                                                  INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao
                                                     ( cod_empresa
                                                     , cod_orgao
                                                     , num_orgao_banpara
                                                     , timestamp
                                                     )
                                                SELECT cod_empresa
                                                     , cod_orgao
                                                     , num_orgao_banpara
                                                     , now()::timestamp(3)
                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao
                                                 WHERE timestamp         = '|| quote_literal(tsTimestampIMA)   ||'
                                                   AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                   AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                     ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '
                                                INSERT
                                                  INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_local
                                                     ( cod_empresa
                                                     , num_orgao_banpara
                                                     , cod_local
                                                     , timestamp
                                                     )
                                                SELECT cod_empresa
                                                     , num_orgao_banpara
                                                     , cod_local
                                                     , now()::timestamp(3)
                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_local
                                                 WHERE timestamp         = '|| quote_literal(tsTimestampIMA)   ||'
                                                   AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                   AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                     ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlUpdate := '    UPDATE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao
                                                       SET cod_orgao = de_para_orgao.cod_orgao_new
                                                      FROM organograma.de_para_orgao
                                                     WHERE configuracao_banpara_orgao.cod_orgao = de_para_orgao.cod_orgao
                                                       AND configuracao_banpara_orgao.timestamp IN (
                                                                                                     SELECT timestamp
                                                                                                       FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                                                                      WHERE vigencia > (
                                                                                                                         SELECT vigencia
                                                                                                                           FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                                                                                          WHERE timestamp         = '|| quote_literal(tsTimestampIMA)   ||'
                                                                                                                            AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                                                                            AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                                                                       )
                                                                                                        AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                                                        AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                                                   )
                                                       AND configuracao_banpara_orgao.cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                       AND configuracao_banpara_orgao.num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                         ;
                                               ';
                                EXECUTE stSqlUpdate;

                                stSqlRH := '   SELECT cod_empresa
                                                    , cod_orgao
                                                    , num_orgao_banpara
                                                    , timestamp
                                                 FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao
                                                WHERE timestamp IN (
                                                                     SELECT timestamp
                                                                       FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                                      WHERE vigencia > (
                                                                                         SELECT vigencia
                                                                                           FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara
                                                                                          WHERE timestamp         = '|| quote_literal(tsTimestampIMA)   ||'
                                                                                            AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                                            AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                                       )
                                                                        AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                                        AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                                                    )
                                                  AND cod_empresa       = '|| reRecordConta.cod_empresa       ||'
                                                  AND num_orgao_banpara = '|| reRecordConta.num_orgao_banpara ||'
                                             GROUP BY cod_empresa
                                                    , cod_orgao
                                                    , num_orgao_banpara
                                                    , timestamp
                                                    ;
                                           ';
                                FOR reRecordRH IN EXECUTE stSqlRH LOOP
                                    stSqlUpdate := '    DELETE
                                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao
                                                         WHERE cod_empresa       = '|| reRecordRH.cod_empresa              ||'
                                                           AND cod_orgao         = '|| reRecordRH.cod_orgao                ||'
                                                           AND num_orgao_banpara = '|| reRecordRH.num_orgao_banpara        ||'
                                                           AND timestamp         = '|| quote_literal(reRecordRH.timestamp) ||'
                                                             ;
                                                   ';
                                    EXECUTE stSqlUpdate;
                                    stSqlUpdate := '    INSERT
                                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao
                                                             ( cod_empresa
                                                             , cod_orgao
                                                             , num_orgao_banpara
                                                             , timestamp
                                                             )
                                                        VALUES
                                                             ( '|| reRecordRH.cod_empresa              ||'
                                                             , '|| reRecordRH.cod_orgao                ||'
                                                             , '|| reRecordRH.num_orgao_banpara        ||'
                                                             , '|| quote_literal(reRecordRH.timestamp) ||'
                                                             );
                                                   ';
                                    EXECUTE stSqlUpdate;
                                END LOOP;

                                stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banpara_orgao ADD CONSTRAINT pk_configuracao_banpara_orgao PRIMARY KEY (cod_empresa, num_orgao_banpara, cod_orgao, timestamp);';
                                EXECUTE stSqlUpdate;
                END LOOP;

                -- UPDATE IMA.CONFIGURACAO_BB_ORGAO
                stSqlConta := '
                                  SELECT cod_convenio
                                       , cod_banco
                                       , cod_agencia
                                       , cod_conta_corrente
                                       , MAX(timestamp) AS timestamp
                                    FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                   WHERE vigencia >= (
                                                       SELECT MAX(vigencia)
                                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                        WHERE vigencia <= now()::date
                                                     )
                                GROUP BY cod_convenio
                                       , cod_banco
                                       , cod_agencia
                                       , cod_conta_corrente
                                       ;
                              ';
                FOR reRecordConta IN EXECUTE stSqlConta LOOP

                        stSqlUpdate := '
                                        UPDATE administracao.configuracao
                                           SET valor = (
                                                            SELECT MAX(timestamp) AS timestamp
                                                              FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                             WHERE vigencia = (
                                                                                SELECT MAX(vigencia)
                                                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                                                 WHERE vigencia          <= now()::date
                                                                                   AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                   AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                   AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                   AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                              )
                                                               AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                               AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                               AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                               AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                       )
                                         WHERE cod_modulo = 19
                                           AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                           AND parametro  = ''timestamp_vigencia_RH''
                                             ;
                                   ';
                        EXECUTE stSqlUpdate;

                        SELECT valor::TIMESTAMP
                          INTO tsTimestampIMA
                          FROM administracao.configuracao
                         WHERE cod_modulo = 19
                           AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                           AND parametro  = 'timestamp_vigencia_RH'
                             ;

                        stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao DROP CONSTRAINT pk_configuracao_bb_orgao;';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , descricao
                                             , timestamp
                                             , vigencia
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , descricao || '' MIGRA ORGANOGRAMA''
                                             , now()::timestamp(3)
                                             , (
                                                 SELECT dt_inicial
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                  WHERE cod_periodo_movimentacao = (
                                                                                     SELECT MAX(cod_periodo_movimentacao)
                                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                                                   )
                                               )
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , timestamp
                                             , cod_orgao
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , now()::timestamp(3)
                                             , cod_orgao
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_local
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , timestamp
                                             , cod_local
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , now()::timestamp(3)
                                             , cod_local
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_local
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '    UPDATE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao
                                               SET cod_orgao = de_para_orgao.cod_orgao_new
                                              FROM organograma.de_para_orgao
                                             WHERE configuracao_bb_orgao.cod_orgao  = de_para_orgao.cod_orgao
                                               AND configuracao_bb_orgao.timestamp IN (
                                                                                        SELECT timestamp
                                                                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                                                         WHERE vigencia > (
                                                                                                            SELECT vigencia
                                                                                                              FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                                                                             WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                                               AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                                               AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                                               AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                                               AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                                          )
                                                                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                      )
                                               AND configuracao_bb_orgao.cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                               AND configuracao_bb_orgao.cod_banco          = '|| reRecordConta.cod_banco          ||'
                                               AND configuracao_bb_orgao.cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                               AND configuracao_bb_orgao.cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                 ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlRH := '   SELECT cod_convenio
                                            , cod_banco
                                            , cod_agencia
                                            , cod_conta_corrente
                                            , timestamp
                                            , cod_orgao
                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao
                                        WHERE timestamp IN ( -->= now()::timestamp(3)
                                                             SELECT timestamp
                                                               FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                              WHERE vigencia > (
                                                                                 SELECT vigencia
                                                                                   FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_conta
                                                                                  WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                    AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                    AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                    AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                    AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                               )
                                                                AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                            )
                                          AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                          AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                          AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                          AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                     GROUP BY cod_convenio
                                            , cod_banco
                                            , cod_agencia
                                            , cod_conta_corrente
                                            , timestamp
                                            , cod_orgao
                                            ;
                                   ';
                        FOR reRecordRH IN EXECUTE stSqlRH LOOP
                            stSqlUpdate := '    DELETE
                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao
                                                 WHERE cod_convenio       = '|| reRecordRH.cod_convenio             ||'
                                                   AND cod_banco          = '|| reRecordRH.cod_banco                ||'
                                                   AND cod_agencia        = '|| reRecordRH.cod_agencia              ||'
                                                   AND cod_conta_corrente = '|| reRecordRH.cod_conta_corrente       ||'
                                                   AND timestamp          = '|| quote_literal(reRecordRH.timestamp) ||'
                                                   AND cod_orgao          = '|| reRecordRH.cod_orgao                ||'
                                                     ;
                                           ';
                            EXECUTE stSqlUpdate;
                            stSqlUpdate := '    INSERT
                                                  INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao
                                                     (  cod_convenio
                                                     , cod_banco
                                                     , cod_agencia
                                                     , cod_conta_corrente
                                                     , timestamp
                                                     , cod_orgao
                                                     )
                                                VALUES
                                                     ( '|| reRecordRH.cod_convenio             ||'
                                                     , '|| reRecordRH.cod_banco                ||'
                                                     , '|| reRecordRH.cod_agencia              ||'
                                                     , '|| reRecordRH.cod_conta_corrente       ||'
                                                     , '|| quote_literal(reRecordRH.timestamp) ||'
                                                     , '|| reRecordRH.cod_orgao                ||'
                                                     );
                                           ';
                            EXECUTE stSqlUpdate;
                        END LOOP;

                        stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_bb_orgao ADD CONSTRAINT pk_configuracao_bb_orgao PRIMARY KEY (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao, timestamp);';
                        EXECUTE stSqlUpdate;

                END LOOP;

                -- UPDATE IMA.CONFIGURACAO_BESC_ORGAO
                stSqlConta := '
                                  SELECT cod_convenio
                                       , cod_banco
                                       , cod_agencia
                                       , cod_conta_corrente
                                       , MAX(timestamp) AS timestamp
                                    FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                   WHERE vigencia >= (
                                                       SELECT MAX(vigencia)
                                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                        WHERE vigencia <= now()::date
                                                     )
                                GROUP BY cod_convenio
                                       , cod_banco
                                       , cod_agencia
                                       , cod_conta_corrente
                                       ;
                              ';
                FOR reRecordConta IN EXECUTE stSqlConta LOOP

                        stSqlUpdate := '
                                        UPDATE administracao.configuracao
                                           SET valor = (
                                                            SELECT MAX(timestamp) AS timestamp
                                                              FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                             WHERE vigencia = (
                                                                                SELECT MAX(vigencia)
                                                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                                                 WHERE vigencia          <= now()::date
                                                                                   AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                   AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                   AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                   AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                              )
                                                               AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                               AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                               AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                               AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                       )
                                         WHERE cod_modulo = 19
                                           AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                           AND parametro  = ''timestamp_vigencia_RH''
                                             ;
                                   ';
                        EXECUTE stSqlUpdate;

                        SELECT valor::TIMESTAMP
                          INTO tsTimestampIMA
                          FROM administracao.configuracao
                         WHERE cod_modulo = 19
                           AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                           AND parametro  = 'timestamp_vigencia_RH'
                             ;

                        stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao DROP CONSTRAINT pk_configuracao_besc_orgao;';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , descricao
                                             , timestamp
                                             , vigencia
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , descricao || '' MIGRA ORGANOGRAMA''
                                             , now()::timestamp(3)
                                             , (
                                                 SELECT dt_inicial
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                  WHERE cod_periodo_movimentacao = (
                                                                                     SELECT MAX(cod_periodo_movimentacao)
                                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                                                   )
                                               )
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , timestamp
                                             , cod_orgao
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , now()::timestamp(3)
                                             , cod_orgao
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_local
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , timestamp
                                             , cod_local
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , now()::timestamp(3)
                                             , cod_local
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_local
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '    UPDATE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao
                                               SET cod_orgao = de_para_orgao.cod_orgao_new
                                              FROM organograma.de_para_orgao
                                             WHERE configuracao_besc_orgao.cod_orgao  = de_para_orgao.cod_orgao
                                               AND configuracao_besc_orgao.timestamp IN (
                                                                                          SELECT timestamp
                                                                                            FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                                                           WHERE vigencia > (
                                                                                                              SELECT vigencia
                                                                                                                FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                                                                               WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                                                 AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                                                 AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                                                 AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                                                 AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                                            )
                                                                                             AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                             AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                             AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                             AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                        )
                                               AND configuracao_besc_orgao.cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                               AND configuracao_besc_orgao.cod_banco          = '|| reRecordConta.cod_banco          ||'
                                               AND configuracao_besc_orgao.cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                               AND configuracao_besc_orgao.cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                 ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlRH := '   SELECT cod_convenio
                                            , cod_banco
                                            , cod_agencia
                                            , cod_conta_corrente
                                            , timestamp
                                            , cod_orgao
                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao
                                        WHERE timestamp IN (
                                                             SELECT timestamp
                                                               FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                              WHERE vigencia > (
                                                                                 SELECT vigencia
                                                                                   FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_conta
                                                                                  WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                    AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                    AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                    AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                    AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                               )
                                                                AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                            )
                                          AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                          AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                          AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                          AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                     GROUP BY cod_convenio
                                            , cod_banco
                                            , cod_agencia
                                            , cod_conta_corrente
                                            , timestamp
                                            , cod_orgao
                                            ;
                                   ';
                        FOR reRecordRH IN EXECUTE stSqlRH LOOP
                            stSqlUpdate := '    DELETE
                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao
                                                 WHERE cod_convenio       = '|| reRecordRH.cod_convenio             ||'
                                                   AND cod_banco          = '|| reRecordRH.cod_banco                ||'
                                                   AND cod_agencia        = '|| reRecordRH.cod_agencia              ||'
                                                   AND cod_conta_corrente = '|| reRecordRH.cod_conta_corrente       ||'
                                                   AND timestamp          = '|| quote_literal(reRecordRH.timestamp) ||'
                                                   AND cod_orgao          = '|| reRecordRH.cod_orgao                ||'
                                                     ;
                                           ';
                            EXECUTE stSqlUpdate;
                            stSqlUpdate := '    INSERT
                                                  INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao
                                                     (  cod_convenio
                                                     , cod_banco
                                                     , cod_agencia
                                                     , cod_conta_corrente
                                                     , timestamp
                                                     , cod_orgao
                                                     )
                                                VALUES
                                                     ( '|| reRecordRH.cod_convenio             ||'
                                                     , '|| reRecordRH.cod_banco                ||'
                                                     , '|| reRecordRH.cod_agencia              ||'
                                                     , '|| reRecordRH.cod_conta_corrente       ||'
                                                     , '|| quote_literal(reRecordRH.timestamp) ||'
                                                     , '|| reRecordRH.cod_orgao                ||'
                                                     );
                                           ';
                            EXECUTE stSqlUpdate;
                        END LOOP;

                        stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_besc_orgao ADD CONSTRAINT pk_configuracao_besc_orgao PRIMARY KEY (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao, timestamp);';
                        EXECUTE stSqlUpdate;

                END LOOP;

                -- UPDATE IMA.CONFIGURACAO_BANRISUL_ORGAO
                stSqlConta := '
                                  SELECT cod_convenio
                                       , cod_banco
                                       , cod_agencia
                                       , cod_conta_corrente
                                       , MAX(timestamp) AS timestamp
                                    FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                   WHERE vigencia >= (
                                                       SELECT MAX(vigencia)
                                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                        WHERE vigencia <= now()::date
                                                     )
                                GROUP BY cod_convenio
                                       , cod_banco
                                       , cod_agencia
                                       , cod_conta_corrente
                                       ;
                              ';
                FOR reRecordConta IN EXECUTE stSqlConta LOOP

                        stSqlUpdate := '
                                        UPDATE administracao.configuracao
                                           SET valor = (
                                                            SELECT MAX(timestamp) AS timestamp
                                                              FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                             WHERE vigencia = (
                                                                                SELECT MAX(vigencia)
                                                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                                                 WHERE vigencia          <= now()::date
                                                                                   AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                   AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                   AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                   AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                              )
                                                               AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                               AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                               AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                               AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                       )
                                         WHERE cod_modulo = 19
                                           AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                                           AND parametro  = ''timestamp_vigencia_RH''
                                             ;
                                   ';

                        EXECUTE stSqlUpdate;

                        SELECT valor::TIMESTAMP
                          INTO tsTimestampIMA
                          FROM administracao.configuracao
                         WHERE cod_modulo = 19
                           AND exercicio  = EXTRACT(year FROM now())::VARCHAR
                           AND parametro  = 'timestamp_vigencia_RH'
                             ;

                        stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao DROP CONSTRAINT pk_configuracao_banrisul_orgao;';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , descricao
                                             , timestamp
                                             , vigencia
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , descricao || '' MIGRA ORGANOGRAMA''
                                             , now()::timestamp(3)
                                             , (
                                                 SELECT dt_inicial
                                                   FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                  WHERE cod_periodo_movimentacao = (
                                                                                     SELECT MAX(cod_periodo_movimentacao)
                                                                                       FROM folhapagamento_'|| reRecordEnt.cod_entidade ||'.periodo_movimentacao
                                                                                   )
                                               )
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , timestamp
                                             , cod_orgao
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , now()::timestamp(3)
                                             , cod_orgao
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '
                                        INSERT
                                          INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_local
                                             ( cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , timestamp
                                             , cod_local
                                             )
                                        SELECT cod_convenio
                                             , cod_banco
                                             , cod_agencia
                                             , cod_conta_corrente
                                             , now()::timestamp(3)
                                             , cod_local
                                          FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_local
                                         WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                           AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                           AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                           AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                           AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                             ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlUpdate := '    UPDATE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao
                                               SET cod_orgao = de_para_orgao.cod_orgao_new
                                              FROM organograma.de_para_orgao
                                             WHERE configuracao_banrisul_orgao.cod_orgao  = de_para_orgao.cod_orgao
                                               AND configuracao_banrisul_orgao.timestamp IN (
                                                                                              SELECT timestamp
                                                                                                FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                                                               WHERE vigencia > (
                                                                                                                  SELECT vigencia
                                                                                                                    FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                                                                                   WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                                                     AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                                                     AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                                                     AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                                                     AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                                                )
                                                                                                 AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                                 AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                                 AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                                 AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                                            )
                                               AND configuracao_banrisul_orgao.cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                               AND configuracao_banrisul_orgao.cod_banco          = '|| reRecordConta.cod_banco          ||'
                                               AND configuracao_banrisul_orgao.cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                               AND configuracao_banrisul_orgao.cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                 ;
                                       ';
                        EXECUTE stSqlUpdate;

                        stSqlRH := '   SELECT cod_convenio
                                            , cod_banco
                                            , cod_agencia
                                            , cod_conta_corrente
                                            , timestamp
                                            , cod_orgao
                                         FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao
                                        WHERE timestamp IN (
                                                             SELECT timestamp
                                                               FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                              WHERE vigencia > (
                                                                                 SELECT vigencia
                                                                                   FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_conta
                                                                                  WHERE timestamp          = '|| quote_literal(tsTimestampIMA)    ||'
                                                                                    AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                                    AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                                    AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                                    AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                                               )
                                                                AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                                                AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                                                AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                                                AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                                           )
                                          AND cod_convenio       = '|| reRecordConta.cod_convenio       ||'
                                          AND cod_banco          = '|| reRecordConta.cod_banco          ||'
                                          AND cod_agencia        = '|| reRecordConta.cod_agencia        ||'
                                          AND cod_conta_corrente = '|| reRecordConta.cod_conta_corrente ||'
                                     GROUP BY cod_convenio
                                            , cod_banco
                                            , cod_agencia
                                            , cod_conta_corrente
                                            , timestamp
                                            , cod_orgao
                                            ;
                                   ';
                        FOR reRecordRH IN EXECUTE stSqlRH LOOP
                            stSqlUpdate := '    DELETE
                                                  FROM ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao
                                                 WHERE cod_convenio       = '|| reRecordRH.cod_convenio             ||'
                                                   AND cod_banco          = '|| reRecordRH.cod_banco                ||'
                                                   AND cod_agencia        = '|| reRecordRH.cod_agencia              ||'
                                                   AND cod_conta_corrente = '|| reRecordRH.cod_conta_corrente       ||'
                                                   AND timestamp          = '|| quote_literal(reRecordRH.timestamp) ||'
                                                   AND cod_orgao          = '|| reRecordRH.cod_orgao                ||'
                                                     ;
                                           ';
                            EXECUTE stSqlUpdate;
                            stSqlUpdate := '    INSERT
                                                  INTO ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao
                                                     (  cod_convenio
                                                     , cod_banco
                                                     , cod_agencia
                                                     , cod_conta_corrente
                                                     , timestamp
                                                     , cod_orgao
                                                     )
                                                VALUES
                                                     ( '|| reRecordRH.cod_convenio             ||'
                                                     , '|| reRecordRH.cod_banco                ||'
                                                     , '|| reRecordRH.cod_agencia              ||'
                                                     , '|| reRecordRH.cod_conta_corrente       ||'
                                                     , '|| quote_literal(reRecordRH.timestamp) ||'
                                                     , '|| reRecordRH.cod_orgao                ||'
                                                     );
                                           ';
                            EXECUTE stSqlUpdate;
                        END LOOP;

                        stSqlUpdate := 'ALTER TABLE ima_'|| reRecordEnt.cod_entidade ||'.configuracao_banrisul_orgao ADD CONSTRAINT pk_configuracao_banrisul_orgao PRIMARY KEY (cod_convenio, cod_banco, cod_agencia, cod_conta_corrente, cod_orgao, timestamp);';
                        EXECUTE stSqlUpdate;

                END LOOP;

                -- UPDATE PESSOAL.CONTRATO_PENSIONISTA_ORGAO
                stSqlRH := '     SELECT ppen.*
                                   FROM pessoal_'|| reRecordEnt.cod_entidade ||'.contrato_pensionista_orgao  AS ppen
                             INNER JOIN (
                                             SELECT DISTINCT ON (cod_contrato)
                                                    cod_contrato
                                                  , MAX (timestamp)  AS timestamp
                                               FROM pessoal_'|| reRecordEnt.cod_entidade ||'.contrato_pensionista_orgao
                                           GROUP BY cod_contrato
                                        )                                AS temp
                                     ON ppen.cod_contrato = temp.cod_contrato
                                    AND ppen.timestamp    = temp.timestamp
                                      ;
                           ';
                FOR reRecordRH IN EXECUTE stSqlRH LOOP
                    stSqlUpdate := '    INSERT
                                          INTO pessoal_'|| reRecordEnt.cod_entidade ||'.contrato_pensionista_orgao
                                             ( cod_contrato
                                             , cod_orgao
                                             )
                                        VALUES
                                             ( '|| reRecordRH.cod_contrato ||'
                                             , ( SELECT cod_orgao_new
                                                   FROM organograma.de_para_orgao
                                                  WHERE cod_orgao = '|| reRecordRH.cod_orgao ||'
                                               )
                                             );
                                   ';
                    EXECUTE stSqlUpdate;
                END LOOP;

                -- UPDATE PESSOAL.CONTRATO_SERVIDOR_ORGAO
                stSqlRH := '     SELECT pser.*
                                   FROM pessoal_'|| reRecordEnt.cod_entidade ||'.contrato_servidor_orgao  AS pser
                             INNER JOIN (
                                             SELECT DISTINCT ON (cod_contrato)
                                                    cod_contrato
                                                  , MAX (timestamp)  AS timestamp
                                               FROM pessoal_'|| reRecordEnt.cod_entidade ||'.contrato_servidor_orgao
                                           GROUP BY cod_contrato
                                        )                                AS temp
                                     ON pser.cod_contrato = temp.cod_contrato
                                    AND pser.timestamp    = temp.timestamp
                                      ;
                           ';
                FOR reRecordRH IN EXECUTE stSqlRH LOOP
                    stSqlUpdate := '    INSERT
                                          INTO pessoal_'|| reRecordEnt.cod_entidade ||'.contrato_servidor_orgao
                                             ( cod_contrato
                                             , cod_orgao
                                             )
                                        VALUES
                                             ( '|| reRecordRH.cod_contrato ||'
                                             , ( SELECT cod_orgao_new
                                                   FROM organograma.de_para_orgao
                                                  WHERE cod_orgao = '|| reRecordRH.cod_orgao ||'
                                               )
                                             );
                                   ';
                    EXECUTE stSqlUpdate;
                END LOOP;

                -- UPDATE PONTO.CONFIGURACAO_LOTACAO
                stSqlUpdate := 'ALTER TABLE ponto_'|| reRecordEnt.cod_entidade ||'.configuracao_lotacao DROP CONSTRAINT pk_configuracao_lotacao;';
                EXECUTE stSqlUpdate;
                stSqlUpdate := '    UPDATE ponto_'|| reRecordEnt.cod_entidade ||'.configuracao_lotacao
                                       SET cod_orgao = de_para_orgao.cod_orgao_new
                                      FROM organograma.de_para_orgao
                                     WHERE configuracao_lotacao.cod_orgao = de_para_orgao.cod_orgao
                                         ;
                               ';
                EXECUTE stSqlUpdate;
                        stSqlRH := '   SELECT cod_configuracao
                                            , timestamp
                                            , cod_orgao
                                         FROM ponto_'|| reRecordEnt.cod_entidade ||'.configuracao_lotacao
                                     GROUP BY cod_configuracao
                                            , timestamp
                                            , cod_orgao
                                            ;
                                   ';
                        FOR reRecordRH IN EXECUTE stSqlRH LOOP
                            stSqlUpdate := '    DELETE
                                                  FROM ponto_'|| reRecordEnt.cod_entidade ||'.configuracao_lotacao
                                                 WHERE cod_configuracao = '|| reRecordRH.cod_configuracao ||'
                                                   AND timestamp        = '|| quote_literal(reRecordRH.timestamp) ||'
                                                   AND cod_orgao        = '|| reRecordRH.cod_orgao        ||'
                                                     ;
                                           ';
                            EXECUTE stSqlUpdate;
                            stSqlUpdate := '    INSERT
                                                  INTO ponto_'|| reRecordEnt.cod_entidade ||'.configuracao_lotacao
                                                     ( cod_configuracao
                                                     , timestamp
                                                     , cod_orgao
                                                     )
                                                VALUES
                                                     ( '|| reRecordRH.cod_configuracao ||'
                                                     , '|| quote_literal(reRecordRH.timestamp) ||'
                                                     , '|| reRecordRH.cod_orgao        ||'
                                                     );
                                           ';
                            EXECUTE stSqlUpdate;
                        END LOOP;
                stSqlUpdate := 'ALTER TABLE ponto_'|| reRecordEnt.cod_entidade ||'.configuracao_lotacao ADD CONSTRAINT pk_configuracao_lotacao PRIMARY KEY (cod_configuracao, timestamp, cod_orgao);';
                EXECUTE stSqlUpdate;

            END LOOP;


            UPDATE administracao.configuracao
               SET valor     = 'false'
             WHERE exercicio = (
                                 SELECT MAX(exercicio)
                                   FROM administracao.configuracao
                                  WHERE parametro = 'migra_orgao'
                               )
               AND parametro = 'migra_orgao';

            INSERT
              INTO organograma.de_para_orgao_historico
            SELECT now()::timestamp(3)    AS timestamp
                 , cod_orgao
                 , cod_organograma
                 , cod_orgao_new
                 , inNumCgm             AS numcgm
              FROM organograma.de_para_orgao;

            DELETE
              FROM organograma.de_para_orgao;

            RETURN TRUE;

        ELSE

            RETURN TRUE;

        END IF;

    ELSE

        RAISE EXCEPTION 'De-Para de Órgãos ainda não foi configurado!';

    END IF;

END;
$$ LANGUAGE 'plpgsql';

