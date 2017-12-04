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
* Casos de uso: uc-02.05.11
*/

/*
$Log$
Revision 1.6  2006/07/05 20:37:50  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION tcers.fn_somatorio_contabil_receita(VARCHAR, VARCHAR, VARCHAR)  RETURNS numeric(14,2) AS '
DECLARE
    stCodEstrutural             ALIAS FOR $1;
    stTipo                      ALIAS FOR $2;
    stTipoValorDespesa          ALIAS FOR $3;

    stSql                       VARCHAR   := '''';
    nuSoma                      NUMERIC   := 0;

    crCursor                    REFCURSOR;

BEGIN
   IF (stTipo=1) THEN
            stSql := ''
               SELECT   (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_credito_receita
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%''''
                ) + (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_debito_receita
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%''''
                )'';

    ELSE
            stSql := ''
               SELECT   (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_credito_receita
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%'''' AND tipo=''''A''''
                ) + (
                   SELECT   coalesce(sum(valor),0.00)
                   FROM
                        tmp_debito_receita
                   WHERE
                       cod_estrutural like '''''' || stCodEstrutural || ''%'''' AND tipo=''''A''''
                )'';
    END IF;

    OPEN crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuSoma;
    CLOSE crCursor;


    RETURN nuSoma;
END;
' LANGUAGE 'plpgsql';
