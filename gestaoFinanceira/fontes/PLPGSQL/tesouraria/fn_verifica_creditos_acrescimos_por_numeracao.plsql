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
* Lucas Stephanou 26/06/2007
*
* $Revision: 24322 $
* $Name$
* $Author: domluc $
* $Date: 2007-07-26 21:17:25 -0300 (Qui, 26 Jul 2007) $
*
* Casos de uso: uc-02.04.33
*/
/*
$Log$
Revision 1.1  2007/07/27 00:17:25  domluc
*** empty log message ***

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_verifica_creditos_acrescimos_por_numeracao(varchar,integer) RETURNS BOOLEAN AS '
DECLARE
    stNumeracao         ALIAS FOR $1;
    inExercicio         ALIAS FOR $2;
    inTeste             integer;
BEGIN
    inTeste := 0;

    -- verifica se existe
    select carne.cod_convenio
      into inTeste
      from arrecadacao.carne
     where carne.numeracao = stNumeracao limit 1;

    if NOT FOUND then
        return false;
    end if;

    inTeste := null;

    select calculo.cod_calculo
      into inTeste
      from arrecadacao.carne
           inner join arrecadacao.parcela 
                   on carne.cod_parcela          = parcela.cod_parcela
           inner join arrecadacao.lancamento
                   on lancamento.cod_lancamento = parcela.cod_lancamento 
           inner join arrecadacao.lancamento_calculo
                   on lancamento_calculo.cod_lancamento = lancamento.cod_lancamento 
           inner join arrecadacao.calculo
                   on calculo.cod_calculo = lancamento_calculo.cod_calculo
           inner join monetario.credito
                   on credito.cod_credito = calculo.cod_credito
                  and credito.cod_especie = calculo.cod_especie
                  and credito.cod_genero  = calculo.cod_genero
                  and credito.cod_natureza= calculo.cod_natureza
           inner join contabilidade.plano_analitica_credito
                   on plano_analitica_credito.cod_credito = credito.cod_credito
                  and plano_analitica_credito.cod_especie = credito.cod_especie
                  and plano_analitica_credito.cod_genero  = credito.cod_genero
                  and plano_analitica_credito.cod_natureza= credito.cod_natureza
     where carne.numeracao  = stNumeracao
       and plano_analitica_credito.exercicio = inExercicio
     limit 1;


IF NOT FOUND THEN
 return FALSE;
ELSE
    inTeste := null;
        select calculo.cod_calculo
      into inTeste
      from arrecadacao.carne
           inner join arrecadacao.parcela
                   on carne.cod_parcela          = parcela.cod_parcela
           inner join arrecadacao.lancamento
                   on lancamento.cod_lancamento = parcela.cod_lancamento
           inner join arrecadacao.lancamento_calculo
                   on lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
           inner join arrecadacao.calculo
                   on calculo.cod_calculo = lancamento_calculo.cod_calculo
           inner join monetario.credito
                   on credito.cod_credito = calculo.cod_credito
                  and credito.cod_especie = calculo.cod_especie
                  and credito.cod_genero  = calculo.cod_genero
                  and credito.cod_natureza= calculo.cod_natureza
           inner join orcamento.receita_credito
                   on receita_credito.cod_credito = credito.cod_credito
                  and receita_credito.cod_especie = credito.cod_especie
                  and receita_credito.cod_genero  = credito.cod_genero
                  and receita_credito.cod_natureza= credito.cod_natureza
     where carne.numeracao  = stNumeracao
       and receita_credito.exercicio = inExercicio
     limit 1;

    IF NOT FOUND THEN
     return FALSE;
    ELSE
        inTeste := null;
    
          select calculo.cod_calculo
            into inTeste
            from arrecadacao.carne
                 inner join arrecadacao.parcela
                         on carne.cod_parcela          = parcela.cod_parcela
                 inner join arrecadacao.lancamento
                         on lancamento.cod_lancamento = parcela.cod_lancamento
                 inner join arrecadacao.lancamento_calculo
                         on lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
                 inner join arrecadacao.calculo
                         on calculo.cod_calculo = lancamento_calculo.cod_calculo
                 inner join monetario.credito_acrescimo
                         on credito_acrescimo.cod_credito = calculo.cod_credito
                        and credito_acrescimo.cod_especie = calculo.cod_especie
                        and credito_acrescimo.cod_genero  = calculo.cod_genero
                        and credito_acrescimo.cod_natureza= calculo.cod_natureza
                 inner join contabilidade.plano_analitica_credito_acrescimo
                         on plano_analitica_credito_acrescimo.cod_credito = credito_acrescimo.cod_credito
                        and plano_analitica_credito_acrescimo.cod_especie = credito_acrescimo.cod_especie
                        and plano_analitica_credito_acrescimo.cod_genero  = credito_acrescimo.cod_genero
                        and plano_analitica_credito_acrescimo.cod_natureza= credito_acrescimo.cod_natureza
                        and plano_analitica_credito_acrescimo.cod_acrescimo = credito_acrescimo.cod_acrescimo
                        and plano_analitica_credito_acrescimo.cod_tipo= credito_acrescimo.cod_tipo        
           where carne.numeracao = stNumeracao    
             and plano_analitica_credito_acrescimo.exercicio = inExercicio
           limit 1;

        IF NOT FOUND THEN
            return FALSE;
        ELSE

              select calculo.cod_calculo
                into inTeste
                from arrecadacao.carne
                     inner join arrecadacao.parcela
                             on carne.cod_parcela          = parcela.cod_parcela
                     inner join arrecadacao.lancamento
                             on lancamento.cod_lancamento = parcela.cod_lancamento
                     inner join arrecadacao.lancamento_calculo
                             on lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
                     inner join arrecadacao.calculo
                             on calculo.cod_calculo = lancamento_calculo.cod_calculo
                     inner join monetario.credito_acrescimo
                             on credito_acrescimo.cod_credito = calculo.cod_credito
                            and credito_acrescimo.cod_especie = calculo.cod_especie
                            and credito_acrescimo.cod_genero  = calculo.cod_genero
                            and credito_acrescimo.cod_natureza= calculo.cod_natureza
                     inner join orcamento.receita_credito_acrescimo
                             on receita_credito_acrescimo.cod_credito  = credito_acrescimo.cod_credito
                            and receita_credito_acrescimo.cod_especie  = credito_acrescimo.cod_especie
                            and receita_credito_acrescimo.cod_genero   = credito_acrescimo.cod_genero
                            and receita_credito_acrescimo.cod_natureza = credito_acrescimo.cod_natureza
                            and receita_credito_acrescimo.cod_acrescimo= credito_acrescimo.cod_acrescimo
                            and receita_credito_acrescimo.cod_tipo     = credito_acrescimo.cod_tipo
               where carne.numeracao = stNumeracao
                 and receita_credito_acrescimo.exercicio = inExercicio
               limit 1;


            IF NOT FOUND THEN
                return FALSE;
            ELSE
                return TRUE;
            END IF;

        END IF;

    END IF;

END IF;

END;

'language 'plpgsql';
