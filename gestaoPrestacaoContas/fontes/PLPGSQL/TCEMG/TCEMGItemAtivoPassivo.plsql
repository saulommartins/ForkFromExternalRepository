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
 * Script de função PLPGSQL
 *
 * URBEM Soluções de Gestão Pública Ltda
 * www.urbem.cnm.org.br
 *
 * Casos de uso: uc-02.02.11
 */

/*

*/

CREATE OR REPLACE FUNCTION tcemg.item_ativo_passivo (varchar, varchar, varchar, varchar) RETURNS SETOF RECORD AS $$

DECLARE
    stExercicio         ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
    stCodEntidades      ALIAS FOR $4;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    reRegistro          RECORD;
    arRetorno           NUMERIC[];

BEGIN
    
    CREATE TEMPORARY TABLE tmp_ativo_passivo AS
        SELECT * 
        FROM contabilidade.fn_rl_balancete_verificacao(stExercicio
                                                        ,'cod_entidade IN  ('||stCodEntidades||') '
                                                        , stDtInicial
                                                        , stDtFinal
                                                        ,'A'::CHAR)
        as retorno
                    ( cod_estrutural varchar                                                    
                    ,nivel integer                                                               
                    ,nom_conta varchar                                                           
                    ,cod_sistema integer                                                         
                    ,indicador_superavit char(12)                                                    
                    ,vl_saldo_anterior numeric                                                   
                    ,vl_saldo_debitos  numeric                                                   
                    ,vl_saldo_creditos numeric                                                   
                    ,vl_saldo_atual    numeric                                                   
                    );

    stSql := '  SELECT 
                    (SELECT vl_saldo_debitos 
                            FROM tmp_ativo_passivo
                            WHERE cod_estrutural like ''1.0.0.0.0.00.00.00.00.00%''
                    ) as valor_acrescimo
                    
                    ,(0.00) as vl_reducao
                    , ''01''::varchar as cod_tipo

            UNION

                SELECT
                    (SELECT vl_saldo_creditos 
                            FROM tmp_ativo_passivo
                            WHERE cod_estrutural like ''2.0.0.0.0.00.00.00.00.00%''
                    ) as valor_acrescimo
                    
                    ,(SELECT vl_saldo_creditos 
                            FROM tmp_ativo_passivo
                            WHERE cod_estrutural like ''1.0.0.0.0.00.00.00.00.00%''
                    ) as vl_reducao
                    , ''02''::varchar as cod_tipo
    ';
    
    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_ativo_passivo;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';