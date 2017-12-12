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
* $Revision: 24263 $
* $Name$
* $Author: domluc $
* $Date: 2007-07-25 12:49:24 -0300 (Qua, 25 Jul 2007) $
*
* Casos de uso: uc-02.04.04
*/
/*
$Log$
Revision 1.1  2007/07/25 15:49:24  domluc
Arr Carne

*/

CREATE OR REPLACE FUNCTION tesouraria.fn_verifica_classificacao_receitas_por_numeracao( varchar, int ) RETURNS BOOLEAN AS $$
DECLARE
    stNumeracao         ALIAS FOR $1; 
    inExercicio         ALIAS FOR $2; 
    inTeste             integer;
    stSql               varchar;
    reRecord            record;
    flag                boolean;
BEGIN

    stSql := '
           select calculo.cod_credito
                , calculo.cod_especie
                , calculo.cod_genero
                , calculo.cod_natureza
             from arrecadacao.carne
                  inner join arrecadacao.parcela
                          on parcela.cod_parcela = carne.cod_parcela
                  inner join arrecadacao.lancamento
                          on lancamento.cod_lancamento = parcela.cod_lancamento 
                  inner join arrecadacao.lancamento_calculo
                          on lancamento_calculo.cod_lancamento = lancamento.cod_lancamento 
                  inner join arrecadacao.calculo
                          on calculo.cod_calculo = lancamento_calculo.cod_calculo             
            where carne.numeracao = ' || stNumeracao || '
    ';

    for reRecord in execute stSql
    loop
        select tesouraria.fn_verifica_credito(  reRecord.cod_credito
                                              , reRecord.cod_especie 
                                              , reRecord.cod_genero
                                              , reRecord.cod_natureza )
          into flag;
        if flag = false then
            return false;
        end if;                             
    end loop;

    return flag;

END;

$$ language 'plpgsql';
