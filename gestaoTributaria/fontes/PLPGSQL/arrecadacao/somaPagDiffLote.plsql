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
* $Id: somaPagDiffLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.somaPagDiffLote(integer,integer) returns numeric as '
declare
    inCodLote       ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    stSql           varchar;
    reRecord        record;
    nuSoma          numeric := 0.00;
begin
stSql := ''  select sum(paga.valor) as soma
               from arrecadacao.pagamento_diferenca paga
                  , arrecadacao.pagamento pag
                  , arrecadacao.pagamento_lote plote
                  , arrecadacao.lote lote
              where pag.numeracao = paga.numeracao
                and pag.ocorrencia_pagamento = paga.ocorrencia_pagamento
                and pag.cod_convenio = paga.cod_convenio
      
                and plote.numeracao = pag.numeracao
                and plote.ocorrencia_pagamento = pag.ocorrencia_pagamento
                and plote.cod_convenio = pag.cod_convenio
      
                and lote.cod_lote = plote.cod_lote
                and lote.exercicio = plote.exercicio
                and lote.cod_lote= ''||inCodLote||''
                and lote.exercicio= ''||inExercicio||''
         '';
    for reRecord in execute stSql loop
        nuSoma := nuSoma + reRecord.soma;
    end loop;

   return nuSoma::numeric(14,2);
end;
'language 'plpgsql';
