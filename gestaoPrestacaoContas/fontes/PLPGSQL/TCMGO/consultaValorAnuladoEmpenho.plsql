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
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.1  2007/05/21 19:25:20  domluc
FUncao Esquecida

*/

CREATE OR REPLACE FUNCTION tcmgo.fn_consultar_anulado_empenho( inCodPreEmpenho integer, stExercicio varchar, inMes integer, inAno integer) RETURNS NUMERIC AS '
DECLARE
    nuValorAnulado             NUMERIC := 0.00;
BEGIN
    select coalesce(sum(empenho_anulado_item.vl_anulado),0.00)
      into nuValorAnulado 
      from empenho.empenho_anulado_item 
inner join empenho.item_pre_empenho 
        on empenho_anulado_item.exercicio  = item_pre_empenho.exercicio
       and empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
       and empenho_anulado_item.num_item = item_pre_empenho.num_item

     where item_pre_empenho.cod_pre_empenho = inCodPreEmpenho
       and item_pre_empenho.exercicio = stExercicio  
       and ( ( to_char( empenho_anulado_item.timestamp,''mm'')::int = inMes ) and ( to_char( empenho_anulado_item.timestamp,''yyyy'')::int = inAno  ) );

    RETURN coalesce ( nuValorAnulado , 0.00)::numeric;

END;
'LANGUAGE 'plpgsql';
