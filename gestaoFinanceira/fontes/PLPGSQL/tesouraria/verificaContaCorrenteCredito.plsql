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
* Lucas Stephanou 10/03/2007
*
* $Revision: 24421 $
* $Name$
* $Author: domluc $
* $Date: 2007-07-31 16:51:02 -0300 (Ter, 31 Jul 2007) $
*
* Casos de uso: uc-02.04.33
*/
/*
$Log$
Revision 1.3  2007/07/31 19:51:02  domluc
Correção em Variavel de REtorno

Revision 1.2  2007/07/31 19:49:53  domluc
Correção em Variavel de REtorno

Revision 1.1  2007/07/25 15:49:24  domluc
Arr Carne

Revision 1.2  2007/06/13 21:36:03  domluc
Alterações para comportar arr. via banco em receitas orcamentarias e/ou extra-orcamentarias

Revision 1.1  2007/03/15 19:02:17  domluc
Caso de Uso 02.04.33

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_verifica_conta_corrente_credito( integer, integer) RETURNS BOOLEAN AS $$
DECLARE
    inCodLote	ALIAS FOR $1; 
    inExercicio	ALIAS FOR $2;
    inTeste	integer;

BEGIN
    inTeste := 0;
    select plano_banco.cod_plano
      into inTeste
      from arrecadacao.pagamento_lote
inner join arrecadacao.pagamento
        on pagamento.numeracao            = pagamento_lote.numeracao
       and pagamento.cod_convenio         = pagamento_lote.cod_convenio
       and pagamento.ocorrencia_pagamento = pagamento_lote.ocorrencia_pagamento
inner join arrecadacao.pagamento_calculo
        on pagamento_calculo.numeracao            = pagamento.numeracao
       and pagamento_calculo.cod_convenio         = pagamento.cod_convenio
       and pagamento_calculo.ocorrencia_pagamento = pagamento.ocorrencia_pagamento
inner join arrecadacao.calculo
        on calculo.cod_calculo = pagamento_calculo.cod_calculo
 left join monetario.credito_conta_corrente
        on credito_conta_corrente.cod_credito = calculo.cod_credito
       and credito_conta_corrente.cod_especie = calculo.cod_especie
       and credito_conta_corrente.cod_genero  = calculo.cod_genero
       and credito_conta_corrente.cod_natureza= calculo.cod_natureza
 left join monetario.conta_corrente_convenio
        on credito_conta_corrente.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
       and credito_conta_corrente.cod_agencia = conta_corrente_convenio.cod_agencia
       and credito_conta_corrente.cod_banco = conta_corrente_convenio.cod_banco
       and credito_conta_corrente.cod_convenio = conta_corrente_convenio.cod_convenio
 left join monetario.conta_corrente
        on conta_corrente.cod_conta_corrente = conta_corrente_convenio.cod_conta_corrente
       and conta_corrente.cod_agencia = conta_corrente_convenio.cod_agencia
       and conta_corrente.cod_banco = conta_corrente_convenio.cod_banco
 left join contabilidade.plano_banco
        on conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
       and conta_corrente.cod_agencia = plano_banco.cod_agencia
       and conta_corrente.cod_banco = plano_banco.cod_banco
       and plano_banco.exercicio = inExercicio::VARCHAR
     where pagamento_lote.cod_lote  = inCodLote
       and pagamento_lote.exercicio = inExercicio::VARCHAR
    limit 1;

    IF inTeste = 0 OR inTeste IS NULL THEN
        return FALSE;
    ELSE
        return TRUE;
    END IF;
END;
$$ language 'plpgsql';
