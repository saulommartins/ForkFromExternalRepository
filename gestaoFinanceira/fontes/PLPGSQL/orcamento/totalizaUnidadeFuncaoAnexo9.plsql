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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.01.16
*/

/*
$Log$
Revision 1.3  2006/07/05 20:38:05  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION orcamento.fn_totaliza_orgao_funcao(integer,integer,varchar,varchar) RETURNS numeric(14,2) AS '
DECLARE
    inNumOrgao          ALIAS FOR $1;
    inCodFuncao         ALIAS FOR $2;
    stTabela1           ALIAS FOR $3;
    stTabela2           ALIAS FOR $4;
    stSql               VARCHAR   := '''';
    nuSoma              NUMERIC   := 0;
    crCursor            REFCURSOR;

BEGIN

IF (stTabela1 = '''' AND stTabela2 = '''') THEN
    stSql := ''SELECT
                sum( vl_original ) as soma
                FROM    tmp_despesa
                WHERE   num_orgao  = '' || inNumOrgao  || ''
                AND     cod_funcao = '' || inCodFuncao || ''
                '';
ELSE
    stSql := ''SELECT
                coalesce(t1.valor,0.00) - coalesce(t2.valor,0.00) as soma
                FROM    '' || stTabela1 ||'' as t1
                        LEFT JOIN '' || stTabela2 ||'' as t2 ON (
                                t2.num_orgao  = t1.num_orgao 
                            AND t2.cod_funcao = t1.cod_funcao
                        )
                WHERE   t1.num_orgao  = '' || inNumOrgao  || ''
                AND     t1.cod_funcao = '' || inCodFuncao || ''
                '';

END IF;
    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;

    RETURN nuSoma;
END;
'language 'plpgsql';
