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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: somaPagamentosLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00 , uc-02.04.33                 
*/

/*
$Log$
Revision 1.1  2007/03/15 16:15:42  domluc
Caso de Uso 02.04.33

*/

CREATE OR REPLACE FUNCTION arrecadacao.somaPagamentosLote(integer,integer) returns numeric as $$
declare
    inCodLote       ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    nuSoma          numeric := 0.00;
begin

    select coalesce(sum(pagamento.valor),0.00) as valor
      into nuSoma
      from arrecadacao.lote
           inner join arrecadacao.pagamento_lote
                   on pagamento_lote.cod_lote = lote.cod_lote 
                  and pagamento_lote.exercicio = lote.exercicio
           inner join arrecadacao.pagamento 
                   on pagamento.numeracao = pagamento_lote.numeracao
                  and pagamento.ocorrencia_pagamento = pagamento_lote.ocorrencia_pagamento
                  and pagamento.cod_convenio = pagamento_lote.cod_convenio 
     where lote.cod_lote  = inCodLote 
       and lote.exercicio  = inExercicio::VARCHAR ;

   return nuSoma::numeric(14,2);
end;
$$ language 'plpgsql';
