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
Revision 1.1  2007/05/21 19:24:54  domluc
FUnção Esquecida

*/

CREATE OR REPLACE FUNCTION tcmgo.fn_consultar_pagamento_empenho( inCodEmpenho integer, inCodEntidade integer, stExercicio varchar, inMes integer, inAno integer) RETURNS NUMERIC AS '
DECLARE
    nuValorPago                NUMERIC := 0.00;
    nuValorAnulado             NUMERIC := 0.00;
BEGIN

-- valor pago
    select sum ( coalesce(nota_liquidacao_paga.vl_pago,0.00) )
      into nuValorPago
      from empenho.nota_liquidacao_paga 
           inner join empenho.nota_liquidacao
                   on nota_liquidacao_paga.cod_nota      = nota_liquidacao.cod_nota
                  and nota_liquidacao_paga.cod_entidade  = nota_liquidacao.cod_entidade
     where nota_liquidacao.cod_entidade      = inCodEmpenho 
       and nota_liquidacao.cod_empenho       = inCodEntidade
       and ( ( to_char( nota_liquidacao_paga.timestamp,''mm'')::int = inMes ) and ( to_char( nota_liquidacao_paga.timestamp,''yyyy'')::int = inAno  ) );

-- valor anulado     
    select sum( coalesce(nota_liquidacao_paga_anulada.vl_anulado ,0.00) )
      into nuValorAnulado
     from empenho.nota_liquidacao_paga_anulada 
          inner join empenho.nota_liquidacao_paga
                  on nota_liquidacao_paga_anulada.exercicio     = nota_liquidacao_paga.exercicio
                 and nota_liquidacao_paga_anulada.cod_nota      = nota_liquidacao_paga.cod_nota
                 and nota_liquidacao_paga_anulada.cod_entidade  = nota_liquidacao_paga.cod_entidade
                 and nota_liquidacao_paga_anulada.timestamp     = nota_liquidacao_paga.timestamp
          inner join empenho.nota_liquidacao
                  on nota_liquidacao_paga.cod_nota      = nota_liquidacao.cod_nota
                 and nota_liquidacao_paga.cod_entidade  = nota_liquidacao.cod_entidade

     where nota_liquidacao.exercicio_empenho = stExercicio  
       and nota_liquidacao.cod_entidade      = inCodEmpenho 
       and nota_liquidacao.cod_empenho       = inCodEntidade
       and ( ( to_char( nota_liquidacao_paga_anulada.timestamp_anulada,''mm'')::int = inMes ) and ( to_char( nota_liquidacao_paga_anulada.timestamp_anulada,''yyyy'')::int = inAno  ) );
    
    RETURN coalesce ( (nuValorPago - nuValorAnulado ) , 0.00)::numeric;

END;
'LANGUAGE 'plpgsql';
