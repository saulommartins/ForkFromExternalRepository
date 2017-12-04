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
* $Revision: 13028 $
* $Name$
* $Author: diego $
* $Date: 2006-07-20 11:25:24 -0300 (Qui, 20 Jul 2006) $
*
* Casos de uso: uc-03.03.05
*/

/*
$Log$
Revision 1.3  2006/07/20 14:25:24  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:41  diego


*/

CREATE OR REPLACE FUNCTION almoxarifado.fn_consulta_classificacao(INTEGER, INTEGER)
RETURNS VARCHAR AS $$
DECLARE
    reRecord            RECORD;
    stOut               VARCHAR := '';
    stSql               VARCHAR := '';
    inCodCatalogo       ALIAS FOR $1;
    inCodClassificacao  ALIAS FOR $2;

BEGIN

    stSql := '
        SELECT
             cla.cod_nivel
            ,pos.mascara
        FROM
             almoxarifado.catalogo_niveis         as pos
            ,almoxarifado.classificacao_nivel     as cla
        WHERE   cla.cod_catalogo       = pos.cod_catalogo
        AND     cla.nivel              = pos.nivel
        AND     cla.cod_catalogo       = '||inCodCatalogo||'
        AND     cla.cod_classificacao  = '||inCodClassificacao||'
        ORDER BY cla.nivel
        ';

    FOR reRecord IN EXECUTE stSql LOOP
    stOut := stOut||'.'||sw_fn_mascara_dinamica ( ( case when reRecord.mascara = '' then '0' else reRecord.mascara end ) ,
                cast(reRecord.cod_nivel as VARCHAR));
    END LOOP;

    stOut := SUBSTR(stOut,2,LENGTH(stOut));

    RETURN stOut;

END;
$$
language 'plpgsql';
