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
* $Author: $
* $Date: $
* $Id: TCETOExportacaoLiquidacaoTotalPaga.plsql 60815 2014-11-17 18:07:36Z evandro $
*/

CREATE OR REPLACE FUNCTION tceto.fn_exportacao_liquidacao_total_pago(varchar,integer,integer,varchar) RETURNS NUMERIC(14,2)  AS $$
DECLARE
    stExercicio      ALIAS FOR $1;
    inCodNota        ALIAS FOR $2;
    inCodEntidade    ALIAS FOR $3;
    stExercicioAtual ALIAS FOR $4;
    nuSoma           NUMERIC(14,2) := 0.00 ;

BEGIN
    SELECT  coalesce(Sum(enlp.vl_pago),0.00)
    INTO    nuSoma
    FROM    empenho.nota_liquidacao_paga    as enlp,
            empenho.nota_liquidacao         as enl
    WHERE   enl.exercicio            =   stExercicio
        AND enl.cod_nota             =   inCodNota
        AND enl.cod_entidade         =   inCodEntidade
        AND	to_date(enlp.timestamp::varchar,'yyyy-mm-dd') <= to_date('31/12/'||(to_number(stExercicioAtual,'9999') ),'dd/mm/yyyy')

        -- Nota Liquidacao Paga
        AND enlp.cod_entidade       =   enl.cod_entidade
        AND enlp.cod_nota           =   enl.cod_nota
        AND enlp.exercicio          =   enl.exercicio;

    RETURN nuSoma;
END;
$$ LANGUAGE 'plpgsql';
