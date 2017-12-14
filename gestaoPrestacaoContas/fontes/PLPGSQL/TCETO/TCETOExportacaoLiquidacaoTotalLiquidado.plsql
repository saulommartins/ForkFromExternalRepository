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
* $Revision: $
* $Name: $
* $Id: TCETOExportacaoLiquidacaoTotalLiquidado.plsql 60726 2014-11-11 19:48:00Z evandro $
* $Author: $
* $Date: $
*
*/

CREATE OR REPLACE FUNCTION tceto.fn_exportacao_liquidacao_total_liquidado(varchar,integer,integer) RETURNS NUMERIC(14,2) AS $$
DECLARE
    stExercicio     ALIAS FOR $1;
    inCodNota       ALIAS FOR $2;
    inCodEntidade   ALIAS FOR $3;
    nuSoma          NUMERIC(14,2)   := 0.00 ;

BEGIN
    SELECT  coalesce(Sum(eli.vl_total),0.00)
        INTO    nuSoma
    FROM    empenho.nota_liquidacao_item    as eli,
            empenho.nota_liquidacao         as enl

    WHERE enl.exercicio  =   stExercicio
    AND enl.cod_nota     =   inCodNota
    AND enl.cod_entidade =   inCodEntidade
    -- Liga a nota_liquidacao_item
    AND eli.exercicio    =   enl.exercicio
    AND eli.cod_nota     =   enl.cod_nota
    AND eli.cod_entidade =   enl.cod_entidade;

    RETURN nuSoma;
END;
$$ LANGUAGE 'plpgsql';
