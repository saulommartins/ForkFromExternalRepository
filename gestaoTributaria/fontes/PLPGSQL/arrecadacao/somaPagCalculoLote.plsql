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
* $Id: somaPagCalculoLote.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.somaPagCalculoLote(integer,integer,integer,integer,integer,integer) returns numeric as '
declare
    inCodLote       ALIAS FOR $1;
    inExercicio     ALIAS FOR $2;
    inCodCredito    ALIAS FOR $3;
    inCodEspecie    ALIAS FOR $4;
    inCodGenero     ALIAS FOR $5;
    inCodNatureza   ALIAS FOR $6;
    stSql           varchar;
    reRecord        record;
    nuSoma          numeric := 0.00;
begin
stSql := '' select sum(pagc.valor) as soma
              from arrecadacao.calculo c
                  , arrecadacao.pagamento_calculo pagc
                  , arrecadacao.pagamento pag
                  , arrecadacao.pagamento_lote plote
                  , arrecadacao.lote lote
                  , monetario.credito mc
              where mc.cod_credito = c.cod_credito
                and mc.cod_especie = c.cod_especie
                and mc.cod_genero = c.cod_genero
                and mc.cod_natureza = c.cod_natureza
      
                and pagc.cod_calculo = c.cod_calculo
                and pag.numeracao = pagc.numeracao
                and pag.ocorrencia_pagamento = pagc.ocorrencia_pagamento
                and pag.cod_convenio = pagc.cod_convenio
      
                and plote.numeracao = pag.numeracao
                and plote.ocorrencia_pagamento = pag.ocorrencia_pagamento
                and plote.cod_convenio = pag.cod_convenio
      
                and lote.cod_lote = plote.cod_lote
                and lote.exercicio = plote.exercicio
                and lote.cod_lote= ''||inCodLote||''
                and lote.exercicio= ''||inExercicio||''
                and lote.cod_lote= ''||inCodLote||''
                and lote.exercicio= ''||inExercicio||''
                and c.cod_credito  = ''||inCodCredito||''
                and c.cod_especie  = ''||inCodEspecie||''
                and c.cod_genero   = ''||inCodGenero||''
                and c.cod_natureza = ''||inCodNatureza||''

           group by c.cod_credito
                  , c.cod_especie
                  , c.cod_genero
                  , c.cod_natureza
                  , mc.descricao_credito'';
    for reRecord in execute stSql loop
        nuSoma := nuSoma + reRecord.soma;
    end loop;

   return nuSoma::numeric(14,2);
end;
'language 'plpgsql';
