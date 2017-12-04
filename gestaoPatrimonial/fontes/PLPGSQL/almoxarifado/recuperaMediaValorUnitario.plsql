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
/** fn_recupera_valor_unitario_estoque
  * 
  * Data de Criação: 13/11/2008
  
  
  * @author Analista : Gelson Wolowski
  * @author Desenvolvedor : Diogo Zarpelon
  
  * @package URBEM
  * @subpackage 
  
  $Id:$
 
 **/
 CREATE OR REPLACE FUNCTION fn_recupera_valor_unitario_estoque (INTEGER, INTEGER) RETURNS NUMERIC AS $$

DECLARE
    inCodItem      ALIAS FOR $1;
    inCodCatalogo  ALIAS FOR $2;
    nuVlUnitario   NUMERIC;
    reRegistro     RECORD;
    stSQL          VARCHAR;
BEGIN
    stSQL := '
            SELECT CASE WHEN SUM(quantidade) <> 0 THEN
                        COALESCE(SUM(valor_mercado),0) / COALESCE(SUM(quantidade),0)
                    ELSE 0
                    END AS valor_unitario
              FROM almoxarifado.lancamento_material

        INNER JOIN almoxarifado.catalogo_item
                ON catalogo_item.cod_item = lancamento_material.cod_item

             WHERE 1=1';

        IF inCodCatalogo IS NOT NULL THEN
            stSQL := stSQL || ' AND  catalogo_item.cod_catalogo = '||inCodCatalogo||' ';
        END IF;

        IF inCodItem IS NOT NULL THEN
            stSQL := stSQL || ' AND  lancamento_material.cod_item = '||inCodItem||' ';
        END IF;

        stSQL := stSQL || ' GROUP BY  lancamento_material.cod_item ';

    FOR reRegistro IN EXECUTE stSQL LOOP
        nuVlUnitario := reRegistro.valor_unitario;
    END LOOP;

    RETURN nuVlUnitario;
END;
$$ LANGUAGE 'plpgsql';
