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
/* fn_insere_depara_orgaos_existentes
 *
 * Data de Criação : 19/04/2009


 * @author Analista : Gelson Wolowski Gonçalves
 * @author Desenvolvedor : Fábio Bertoldi

 * @package URBEM
 * @subpackage

 * $Id:  $
 */
CREATE OR REPLACE FUNCTION organograma.fn_insere_de_para_orgaos_existentes( stTabela         VARCHAR
                                                                          ) RETURNS          INTEGER AS $$
DECLARE
    stSql               VARCHAR;
    inCodOrganograma    INTEGER;
    inCount             INTEGER;
    stTabelaRH          VARCHAR[];
BEGIN
    stTabelaRH := string_to_array(stTabela, '.');

    SELECT cod_organograma
      INTO inCodOrganograma
      FROM organograma.organograma
     WHERE ativo = TRUE
         ;

    IF stTabela = 'frota.terceiros_historico' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao                                  AS oorg
                                      INNER JOIN (
                                                        SELECT fth.*
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
                                                 )                                                  AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao            AS org
                                                   LEFT JOIN organograma.de_para_orgao    AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                                  AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabela = 'patrimonio.historico_bem' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao                                  AS oorg
                                      INNER JOIN (
                                                        SELECT phb.*
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
                                                 )                                                  AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao            AS org
                                                   LEFT JOIN organograma.de_para_orgao    AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                                  AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabela = 'sw_andamento' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao                                  AS oorg
                                      INNER JOIN (
                                                        SELECT swa.*
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
                                                 )                                                  AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao            AS org
                                                   LEFT JOIN organograma.de_para_orgao    AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                                  AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'contrato_pensionista_orgao' OR stTabelaRH[2] = 'contrato_servidor_orgao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao                                  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'               AS tbl
                                                    INNER JOIN (
                                                                    SELECT DISTINCT ON (cod_contrato)
                                                                           cod_contrato
                                                                         , MAX(timestamp) AS timestamp
                                                                      FROM '|| stTabela ||'
                                                                  GROUP BY cod_contrato
                                                               )                         AS temp
                                                            ON tbl.cod_contrato = temp.cod_contrato
                                                           AND tbl.timestamp    = temp.timestamp
                                                 )                                                  AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao            AS org
                                                   LEFT JOIN organograma.de_para_orgao    AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                                  AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'configuracao_empenho_lotacao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'             AS tbl
                                                    INNER JOIN (
                                                                     SELECT tbl.exercicio
                                                                          , tbl.cod_configuracao
                                                                          , tbl.sequencia
                                                                          , tbl.timestamp
                                                                       FROM '|| stTabela ||'                                AS tbl
                                                                 INNER JOIN '|| stTabelaRH[1] ||'.configuracao_empenho      AS tblo
                                                                         ON tblo.exercicio        = tbl.exercicio
                                                                        AND tblo.cod_configuracao = tbl.cod_configuracao
                                                                        AND tblo.sequencia        = tbl.sequencia
                                                                        AND tblo.timestamp        = tbl.timestamp
                                                                        AND tblo.vigencia         >= (
                                                                                                       SELECT MAX(vigencia)
                                                                                                         FROM '|| stTabelaRH[1] ||'.configuracao_empenho
                                                                                                        WHERE vigencia <= now()::date
                                                                                                     )
                                                               )                            AS temp
                                                            ON tbl.exercicio        = temp.exercicio
                                                           AND tbl.cod_configuracao = temp.cod_configuracao
                                                           AND tbl.sequencia        = temp.sequencia
                                                           AND tbl.timestamp        = temp.timestamp
                                                 )                                          AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao              AS org
                                                   LEFT JOIN organograma.de_para_orgao      AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                          AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'configuracao_empenho_lla_lotacao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'             AS tbl
                                                    INNER JOIN (
                                                                     SELECT tbl.exercicio
                                                                          , tbl.cod_configuracao_lla
                                                                          , tbl.timestamp
                                                                       FROM '|| stTabela ||'                                AS tbl
                                                                 INNER JOIN '|| stTabelaRH[1] ||'.configuracao_empenho_lla  AS tblo
                                                                         ON tblo.exercicio            = tbl.exercicio
                                                                        AND tblo.cod_configuracao_lla = tbl.cod_configuracao_lla
                                                                        AND tblo.timestamp            = tbl.timestamp
                                                                        AND tblo.vigencia             >= (
                                                                                                           SELECT MAX(vigencia)
                                                                                                             FROM '|| stTabelaRH[1] ||'.configuracao_empenho_lla
                                                                                                            WHERE vigencia <= now()::date
                                                                                                         )
                                                               )                            AS temp
                                                            ON tbl.exercicio            = temp.exercicio
                                                           AND tbl.cod_configuracao_lla = temp.cod_configuracao_lla
                                                           AND tbl.timestamp            = temp.timestamp
                                                 )                                          AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao              AS org
                                                   LEFT JOIN organograma.de_para_orgao      AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                          AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'configuracao_banpara_orgao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'             AS tbl
                                                    INNER JOIN (
                                                                     SELECT tbl.cod_empresa
                                                                          , tbl.num_orgao_banpara
                                                                          , tbl.timestamp
                                                                       FROM '|| stTabela ||'                                AS tbl
                                                                 INNER JOIN '|| stTabelaRH[1] ||'.configuracao_banpara      AS tblo
                                                                         ON tblo.cod_empresa          = tbl.cod_empresa
                                                                        AND tblo.num_orgao_banpara    = tbl.num_orgao_banpara
                                                                        AND tblo.timestamp            = tbl.timestamp
                                                                        AND tblo.vigencia             >= (
                                                                                                           SELECT MAX(vigencia)
                                                                                                             FROM '|| stTabelaRH[1] ||'.configuracao_banpara
                                                                                                            WHERE vigencia <= now()::date
                                                                                                         )
                                                               )                            AS temp
                                                            ON tbl.cod_empresa       = temp.cod_empresa
                                                           AND tbl.num_orgao_banpara = temp.num_orgao_banpara
                                                           AND tbl.timestamp         = temp.timestamp
                                                 )                                          AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao              AS org
                                                   LEFT JOIN organograma.de_para_orgao      AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                          AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'configuracao_bb_orgao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'             AS tbl
                                                    INNER JOIN (
                                                                     SELECT tbl.cod_convenio
                                                                          , tbl.cod_banco
                                                                          , tbl.cod_agencia
                                                                          , tbl.cod_conta_corrente
                                                                          , tbl.timestamp
                                                                       FROM '|| stTabela ||'                                AS tbl
                                                                 INNER JOIN '|| stTabelaRH[1] ||'.configuracao_bb_conta     AS tblo
                                                                         ON tblo.cod_convenio         = tbl.cod_convenio
                                                                        AND tblo.cod_banco            = tbl.cod_banco
                                                                        AND tblo.cod_agencia          = tbl.cod_agencia
                                                                        AND tblo.cod_conta_corrente   = tbl.cod_conta_corrente
                                                                        AND tblo.timestamp            = tbl.timestamp
                                                                        AND tblo.vigencia             >= (
                                                                                                           SELECT MAX(vigencia)
                                                                                                             FROM '|| stTabelaRH[1] ||'.configuracao_bb_conta
                                                                                                            WHERE vigencia <= now()::date
                                                                                                         )
                                                               )                            AS temp
                                                            ON tbl.cod_convenio       = temp.cod_convenio
                                                           AND tbl.cod_banco          = temp.cod_banco
                                                           AND tbl.cod_agencia        = temp.cod_agencia
                                                           AND tbl.cod_conta_corrente = temp.cod_conta_corrente
                                                           AND tbl.timestamp          = temp.timestamp
                                                 )                                          AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao              AS org
                                                   LEFT JOIN organograma.de_para_orgao      AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                          AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'configuracao_besc_orgao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'             AS tbl
                                                    INNER JOIN (
                                                                     SELECT tbl.cod_convenio
                                                                          , tbl.cod_banco
                                                                          , tbl.cod_agencia
                                                                          , tbl.cod_conta_corrente
                                                                          , tbl.timestamp
                                                                       FROM '|| stTabela ||'                                AS tbl
                                                                 INNER JOIN '|| stTabelaRH[1] ||'.configuracao_besc_conta   AS tblo
                                                                         ON tblo.cod_convenio         = tbl.cod_convenio
                                                                        AND tblo.cod_banco            = tbl.cod_banco
                                                                        AND tblo.cod_agencia          = tbl.cod_agencia
                                                                        AND tblo.cod_conta_corrente   = tbl.cod_conta_corrente
                                                                        AND tblo.timestamp            = tbl.timestamp
                                                                        AND tblo.vigencia             >= (
                                                                                                           SELECT MAX(vigencia)
                                                                                                             FROM '|| stTabelaRH[1] ||'.configuracao_besc_conta
                                                                                                            WHERE vigencia <= now()::date
                                                                                                         )
                                                               )                            AS temp
                                                            ON tbl.cod_convenio       = temp.cod_convenio
                                                           AND tbl.cod_banco          = temp.cod_banco
                                                           AND tbl.cod_agencia        = temp.cod_agencia
                                                           AND tbl.cod_conta_corrente = temp.cod_conta_corrente
                                                           AND tbl.timestamp          = temp.timestamp
                                                 )                                          AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao              AS org
                                                   LEFT JOIN organograma.de_para_orgao      AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                          AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSIF stTabelaRH[2] = 'configuracao_banrisul_orgao' THEN
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao  AS oorg
                                      INNER JOIN (
                                                        SELECT tbl.*
                                                          FROM '|| stTabela ||'             AS tbl
                                                    INNER JOIN (
                                                                     SELECT tbl.cod_convenio
                                                                          , tbl.cod_banco
                                                                          , tbl.cod_agencia
                                                                          , tbl.cod_conta_corrente
                                                                          , tbl.timestamp
                                                                       FROM '|| stTabela ||'                                    AS tbl
                                                                 INNER JOIN '|| stTabelaRH[1] ||'.configuracao_banrisul_conta   AS tblo
                                                                         ON tblo.cod_convenio         = tbl.cod_convenio
                                                                        AND tblo.cod_banco            = tbl.cod_banco
                                                                        AND tblo.cod_agencia          = tbl.cod_agencia
                                                                        AND tblo.cod_conta_corrente   = tbl.cod_conta_corrente
                                                                        AND tblo.timestamp            = tbl.timestamp
                                                                        AND tblo.vigencia             >= (
                                                                                                           SELECT MAX(vigencia)
                                                                                                             FROM '|| stTabelaRH[1] ||'.configuracao_banrisul_conta
                                                                                                            WHERE vigencia <= now()::date
                                                                                                         )
                                                               )                            AS temp
                                                            ON tbl.cod_convenio       = temp.cod_convenio
                                                           AND tbl.cod_banco          = temp.cod_banco
                                                           AND tbl.cod_agencia        = temp.cod_agencia
                                                           AND tbl.cod_conta_corrente = temp.cod_conta_corrente
                                                           AND tbl.timestamp          = temp.timestamp
                                                 )                                          AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao              AS org
                                                   LEFT JOIN organograma.de_para_orgao      AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                          AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    ELSE
        stSql := '          INSERT
                              INTO organograma.de_para_orgao
                                 ( cod_orgao
                                 , cod_organograma
                                 )
                            SELECT DISTINCT ON (cod_orgao
                                   , cod_organograma
                                 ) oorg.cod_orgao
                                 , oniv.cod_organograma
                              FROM organograma.orgao                AS oorg
                        INNER JOIN organograma.orgao_nivel          AS oniv
                                ON oniv.cod_orgao = oorg.cod_orgao
                        INNER JOIN (      SELECT oorg.cod_orgao
                                            FROM organograma.orgao                                  AS oorg
                                      INNER JOIN '|| stTabela ||'                                   AS tabela
                                              ON oorg.cod_orgao = tabela.cod_orgao
                                      RIGHT JOIN (
                                                      SELECT org.cod_orgao
                                                        FROM organograma.orgao            AS org
                                                   LEFT JOIN organograma.de_para_orgao    AS dpo
                                                          ON dpo.cod_orgao = org.cod_orgao
                                                       WHERE dpo.cod_orgao IS NULL
                                                 )                                                  AS RESTO
                                              ON oorg.cod_orgao        = RESTO.cod_orgao
                                   )                                AS oexi
                                ON oexi.cod_orgao = oorg.cod_orgao
                             WHERE oniv.cod_organograma = '|| inCodOrganograma ||'
                                 ;
                 ';
    END IF;

    EXECUTE stSql;

    GET DIAGNOSTICS inCount = ROW_COUNT;
    RETURN inCount;
END;
$$ LANGUAGE 'plpgsql';
