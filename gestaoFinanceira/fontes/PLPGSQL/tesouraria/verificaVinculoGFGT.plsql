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
* $Revision: 23244 $
* $Name$
* $Author: domluc $
* $Date: 2007-06-13 18:36:03 -0300 (Qua, 13 Jun 2007) $
*
* Casos de uso: uc-02.04.33
*/
/*
$Log$
Revision 1.2  2007/06/13 21:36:03  domluc
Alterações para comportar arr. via banco em receitas orcamentarias e/ou extra-orcamentarias

Revision 1.1  2007/03/15 19:02:17  domluc
Caso de Uso 02.04.33

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_verifica_creditos_acrescimos( integer , integer ) RETURNS BOOLEAN AS
$$
DECLARE
    inCodLote           ALIAS FOR $1; -- Lote da Arrecadacao - Gestao Tributaria
    inExercicio         ALIAS FOR $2; -- Exercicio do Lote da Arrecadacao  - Gestao Tributaria e Exercicio do Boletim - Gestao Financeira
    inTeste             integer;
BEGIN
    inTeste := 0;

    select pagamento_lote.cod_lote
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
           inner join monetario.credito
                   on credito.cod_credito = calculo.cod_credito
                  and credito.cod_especie = calculo.cod_especie
                  and credito.cod_genero  = calculo.cod_genero
                  and credito.cod_natureza= calculo.cod_natureza
            left join contabilidade.plano_analitica_credito
                   on plano_analitica_credito.cod_credito = credito.cod_credito
                  and plano_analitica_credito.cod_especie = credito.cod_especie
                  and plano_analitica_credito.cod_genero  = credito.cod_genero
                  and plano_analitica_credito.cod_natureza= credito.cod_natureza
            left join orcamento.receita_credito
                   on receita_credito.cod_credito = credito.cod_credito
                  and receita_credito.cod_especie = credito.cod_especie
                  and receita_credito.cod_genero  = credito.cod_genero
                  and receita_credito.cod_natureza= credito.cod_natureza
     where pagamento_lote.cod_lote  = inCodLote 
       and pagamento_lote.exercicio = inExercicio::VARCHAR 
       and plano_analitica_credito.cod_credito is null
       and receita_credito.cod_credito is null
     limit 1;

IF inTeste > 0 THEN
 return FALSE;
ELSE
    inTeste := null;

      select pagamento_lote.cod_lote
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
             inner join monetario.credito_acrescimo
                     on credito_acrescimo.cod_credito = calculo.cod_credito
                    and credito_acrescimo.cod_especie = calculo.cod_especie
                    and credito_acrescimo.cod_genero  = calculo.cod_genero
                    and credito_acrescimo.cod_natureza= calculo.cod_natureza
              left join contabilidade.plano_analitica_credito_acrescimo
                     on plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                    and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                    and plano_analitica_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                    and plano_analitica_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                    and plano_analitica_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
                    and plano_analitica_credito_acrescimo.cod_tipo= credito_acrescimo.cod_tipo    
            left join orcamento.receita_credito_acrescimo
                   on receita_credito_acrescimo.cod_credito  = credito_acrescimo.cod_credito
                  and receita_credito_acrescimo.cod_especie  = credito_acrescimo.cod_especie
                  and receita_credito_acrescimo.cod_genero   = credito_acrescimo.cod_genero
                  and receita_credito_acrescimo.cod_natureza = credito_acrescimo.cod_natureza
                  and receita_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                  and receita_credito_acrescimo.cod_tipo     = credito_acrescimo.cod_tipo

       where pagamento_lote.cod_lote  = inCodLote    
         and pagamento_lote.exercicio = inExercicio::VARCHAR   
         and plano_analitica_credito_acrescimo.cod_credito is null
         and receita_credito_acrescimo.cod_credito is null
       limit 1;
    IF inTeste > 0 THEN
        return FALSE;
    ELSE
        return TRUE;
    END IF;
END IF;

END;

$$ language 'plpgsql';
