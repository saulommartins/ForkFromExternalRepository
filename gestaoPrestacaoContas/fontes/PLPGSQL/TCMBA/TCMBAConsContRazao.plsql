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
* $Revision: 17524 $
* $Name$
* $Author: cako $
* $Date: 2006-11-09 13:34:45 -0200 (Qui, 09 Nov 2006) $
*
* Casos de uso: uc-02.02.22
                uc-02.08.07
*/

/*
$Log$
Revision 1.14  2006/11/09 15:34:45  cako
Bug #6787#

Revision 1.13  2006/10/27 17:28:23  cako
Bug #6787#

Revision 1.12  2006/07/18 20:02:10  eduardo
Bug #6556#

Revision 1.11  2006/07/14 17:58:30  andre.almeida
Bug #6556#

Alterado scripts de NOT IN para NOT EXISTS.

Revision 1.10  2006/07/05 20:37:31  cleisson
Adicionada tag Log aos arquivos

*/

/*
CREATE TYPE tcmba.type_conscontrazao AS (
    cod_estrutural  VARCHAR,
    deb_ex_ant      NUMERIC,
    deb_mov_ant     NUMERIC,
    deb_mes         NUMERIC,
    deb_mov         NUMERIC,
    cred_ex_ant     NUMERIC,
    cred_mov_ant    NUMERIC,
    cred_mes        NUMERIC,
    cred_mov        NUMERIC,
    deb_ex          NUMERIC,
    cred_ex         NUMERIC
);
*/

CREATE OR REPLACE FUNCTION tcmba.fn_conscontrazao(VARCHAR, VARCHAR, VARCHAR, VARCHAR, VARCHAR) RETURNS SETOF tcmba.type_conscontrazao AS $$ 
DECLARE
    stExercicio         ALIAS FOR $1;
    stEntidades         ALIAS FOR $2;
    stDtInicial         ALIAS FOR $3;
    stDtFinal           ALIAS FOR $4;
    stDtFinalMesAnt     ALIAS FOR $5;
    stExercicioAnt      VARCHAR := '';
    stSql               VARCHAR := '';
    reRegistro          RECORD;
    reRegistroAux       RECORD;

BEGIN

  stExercicioAnt := stExercicio::INTEGER - 1;

    -- PEGA OS DADOS NO MÊS DO ARQUIVO
    stSql := '
            CREATE TEMPORARY TABLE tmp_balancete_mes AS (

                SELECT *

                  FROM contabilidade.fn_rl_balancete_verificacao(' || quote_literal(stExercicio) || ',
                                                                 '' cod_entidade IN (' || stEntidades || ') '',
                                                                 ' || quote_literal(stDtInicial) || ',
                                                                 ' || quote_literal(stDtFinal) || ',
                                                                 ''A''
                                                                ) AS retorno
                        (
                           cod_estrutural       VARCHAR
                         , nivel                INTEGER
                         , nom_conta            VARCHAR
                         , cod_sistema          INTEGER
                         , indicador_superavit  CHAR(12)
                         , saldo_anterior       NUMERIC
                         , saldo_debitos        NUMERIC
                         , saldo_creditos       NUMERIC
                         , saldo_atual          NUMERIC
                        )
                      
             ORDER BY retorno.cod_estrutural
            )
          ';

    EXECUTE stSql;

    -- PEGA OS DADOS DO INÍCIO DO ANO ATÉ O MES ANTERIOR DO MÊS DO ARQUIVO
    stSql := '
            CREATE TEMPORARY TABLE tmp_balancete_mes_ant AS (

                SELECT *

                  FROM contabilidade.fn_rl_balancete_verificacao(' || quote_literal(stExercicio) || ',
                                                                 '' cod_entidade IN (' || stEntidades || ') '',
                                                                 ''01/01/' || stExercicio || ''',
                                                                 ' || quote_literal(stDtFinalMesAnt) || ',
                                                                 ''A''
                                                                ) AS retorno
                        (
                           cod_estrutural       VARCHAR
                         , nivel                INTEGER
                         , nom_conta            VARCHAR
                         , cod_sistema          INTEGER
                         , indicador_superavit  CHAR(12)
                         , saldo_anterior       NUMERIC
                         , saldo_debitos        NUMERIC
                         , saldo_creditos       NUMERIC
                         , saldo_atual          NUMERIC
                        )
                      
             ORDER BY retorno.cod_estrutural
            )
          ';

    EXECUTE stSql;

    stSql := '
              SELECT
                      cod_estrutural
                    , CASE WHEN saldo_ex_ant > 0.00 THEN saldo_ex_ant
                                                    ELSE 0.00
                    END AS deb_ex_ant
                    , deb_mov_ant
                    , deb_mes
                    , deb_mov
                    , CASE WHEN saldo_ex_ant < 0.00 THEN saldo_ex_ant
                                                    ELSE 0.00
                    END AS cred_ex_ant
                    , cred_mov_ant
                    , cred_mes
                    , cred_mov
                    , CASE WHEN (saldo_ex) > 0.00 THEN saldo_ex
                                                  ELSE 0.00
                    END AS deb_ex
                    , CASE WHEN (saldo_ex) < 0.00 THEN saldo_ex
                                                  ELSE 0.00
                    END AS cred_ex

                FROM
                    (
                      SELECT
                            cod_estrutural
                          , SUM(saldo_ex_ant) AS saldo_ex_ant
                          , SUM(deb_mov_ant) AS deb_mov_ant
                          , SUM(deb_mes) AS deb_mes
                          , (SUM(deb_mov_ant) + SUM(deb_mes)) AS deb_mov
                          , SUM(cred_mov_ant) AS cred_mov_ant
                          , SUM(cred_mes) AS cred_mes
                          , (SUM(cred_mov_ant) + SUM(cred_mes)) AS cred_mov
                          , SUM(saldo_ex) AS saldo_ex

                        FROM
                            (
                              SELECT
                                      cod_estrutural
                                    , saldo_anterior AS saldo_ex_ant
                                    , 0.00 AS deb_mov_ant
                                    , COALESCE(saldo_debitos,0.00) AS deb_mes
                                    , 0.00 AS cred_mov_ant
                                    , COALESCE(saldo_creditos,0.00) AS cred_mes
                                    , saldo_atual AS saldo_ex

                                FROM tmp_balancete_mes

                           UNION ALL

                              SELECT
                                      cod_estrutural
                                    , 0.00 AS saldo_ex_ant
                                    , COALESCE(saldo_debitos,0.00) AS deb_mov_ant
                                    , 0.00 AS deb_mes
                                    , COALESCE(saldo_creditos,0.00) AS cred_mov_ant
                                    , 0.00 AS cred_mes
                                    , 0.00 AS saldo_ex

                                FROM tmp_balancete_mes_ant

                            ORDER BY cod_estrutural
                            ) AS retorno
                    GROUP BY cod_estrutural
                    ORDER BY cod_estrutural
                    ) AS tabela
          ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN NEXT reRegistro;
    END LOOP;

    DROP TABLE tmp_balancete_mes;
    DROP TABLE tmp_balancete_mes_ant;

    RETURN;
END;
$$ LANGUAGE 'plpgsql';
