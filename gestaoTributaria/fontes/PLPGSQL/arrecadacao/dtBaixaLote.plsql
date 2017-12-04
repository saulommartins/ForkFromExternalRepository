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
* $Id: dtBaixaLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.dtBaixaLote(integer,integer) returns date as '
declare
    inCodLote       ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    dtBaixa         date;
begin
             select pag.data_baixa 
               into dtBaixa
               from arrecadacao.pagamento pag INNER JOIN
                  ( select * from arrecadacao.pagamento_lote where cod_lote = inCodLote limit 1) as plote
                  ON plote.numeracao = pag.numeracao
                and plote.ocorrencia_pagamento = pag.ocorrencia_pagamento
                and plote.cod_convenio = pag.cod_convenio
                INNER JOIN ( select * from arrecadacao.lote where cod_lote = inCodLote limit 1) as lote
                ON lote.cod_lote = plote.cod_lote
                and lote.exercicio = plote.exercicio
              where 
                lote.cod_lote=  inCodLote
                and lote.exercicio= inExercicio::varchar
              limit 1;

   return dtBaixa;
end;
'language 'plpgsql';
