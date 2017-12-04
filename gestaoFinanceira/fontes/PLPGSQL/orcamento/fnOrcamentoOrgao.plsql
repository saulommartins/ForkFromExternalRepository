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
CREATE OR REPLACE FUNCTION orcamento.fn_orgao(stCodOrgao VARCHAR) RETURNS SETOF RECORD AS
$$

DECLARE
    stSql              VARCHAR;
    reRegistro          RECORD;

BEGIN
    stSql := '
        SELECT  publico.fn_mascara_dinamica(' || quote_literal('999') || ',orgao.num_orgao::varchar)::integer as num_orgao,
                orgao.nom_orgao::varchar as nom_orgao
          FROM  orcamento.orgao
         WHERE  orcamento.orgao.num_orgao IN ( '||stCodOrgao||' )
      GROUP BY  publico.fn_mascara_dinamica(' || quote_literal('999') || ',orgao.num_orgao::varchar),
                orgao.nom_orgao
    ';

    FOR reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    RETURN;

END;

$$language 'plpgsql';

