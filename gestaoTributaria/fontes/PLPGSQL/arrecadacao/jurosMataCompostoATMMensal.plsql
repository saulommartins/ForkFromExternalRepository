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
* Calcula Valor de Juros com atualização monetária mensal para Mata
*
* Data de Criação   : 17/06/2010
*
* @author Analista      Fábio Bertoldi
* @author Desenvolvedor Eduardo Paculski Schitz
*
* @package URBEM
*
* $Id:$
*/

CREATE OR REPLACE FUNCTION fn_juros_1_porcento_composto_atm_mensal(date,date,numeric,integer,integer) RETURNS numeric as $$
DECLARE
    dtVencimento        ALIAS FOR $1;
    dtDataCalculo       ALIAS FOR $2;
    nuValor             ALIAS FOR $3;
    inCodAcrescimo      ALIAS FOR $4;
    inCodTipo           ALIAS FOR $5;
    nuRetorno           NUMERIC = 0.00;
    nuAux               NUMERIC = 0.00;
    inDiff              INTEGER;
    inDiasAntes         INTEGER;
    inDiasMes           INTEGER;
    inMes               INTEGER;
    inAno               INTEGER;
    inDiaCalculo        INTEGER;
    inDiaVencimento     INTEGER;
    i                   INTEGER = 0;
    inCount             INTEGER = 0;
    nuTaxaFracao        NUMERIC = 0.0333/100;
    nuTaxa              NUMERIC = 1.0/100;
    ValorComJuros       NUMERIC(14,2) = 0;
    nuValorComAcrescimo NUMERIC(14,2);
    nuValorAtMonetaria  NUMERIC(14,2);
    dtVencimentoTMP     DATE;
    dtCalculoTMP        DATE;
BEGIN

    nuValorAtMonetaria := nuValor;
    IF (dtVencimento < dtDataCalculo ) THEN
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);

        IF (inDiff = 0) THEN
            inDiasAntes := diff_datas_em_dias(dtVencimento,dtDataCalculo);
            IF (inDiasAntes > 0) THEN
                SELECT
                    fn_acrescimo_indice_mata_composto(dtVencimento, dtDataCalculo, nuValor, 3, 1)
                INTO
                    nuValorComAcrescimo;

                WHILE (i < inDiasAntes) LOOP
                    nuAux := nuAux + nuTaxaFracao;
                    i := i +1;
                END LOOP;

                ValorComJuros := ((nuValor + nuValorComAcrescimo) * nuAux);  -- + (nuValor + nuValorComAcrescimo);
            ELSE
                ValorComJuros := 0.00;
            END  IF;

            nuRetorno := ValorComJuros;-- - nuValor;
        ELSE
            -- Retira o último mês, pois este é calculado depois do WHILE final
            inDiff := inDiff - 1;

            inDiaVencimento := EXTRACT(DAY FROM dtVencimento);
            inMes := EXTRACT(MONTH FROM dtVencimento);
            inAno := EXTRACT(YEAR FROM dtVencimento);
            inDiaCalculo := EXTRACT(DAY FROM dtDataCalculo);

            -- Verifica qual o último dia do mes
            inDiasMes := calculaNrDiasAnoMes(inAno, inMes);

            IF (inDiaVencimento = inDiasMes) THEN
                -- Caso o dia de vencimento seja o ultimo dia do mes, não haverá juros neste mês
                inDiaVencimento := 30;
                inDiasAntes := 0;
                ValorComJuros := nuValor;

                dtVencimentoTMP := inAno||'-'||inMes||'-'||01;
                dtCalculoTMP := inAno||'-'||inMes||'-'||01;

                SELECT
                    fn_acrescimo_indice_mata_composto(dtVencimentoTMP, dtCalculoTMP, nuValor, 3, 1)
                INTO
                    nuValorComAcrescimo;

                nuValorAtMonetaria := nuValorAtMonetaria + nuValorComAcrescimo;
                ValorComJuros := nuValorAtMonetaria;

            ELSE
                -- Caso não seja o ultimo dia do mês, calcula o juros dos dias restantes do primeiro mês

                -- inDiasAntes recebe o número de dias restantes dentro do mês de vencimento a partir do dia do vencimento
                inDiasAntes := (inDiasMes - inDiaVencimento);

                -- Calcula a taxa de acordo com o número de dias
                WHILE (i < inDiasAntes) LOOP
                    nuAux := (nuAux + nuTaxaFracao);
                    i := i +1;
                END LOOP;

                dtVencimentoTMP := inAno||'-'||inMes||'-'||01;
                dtCalculoTMP := inAno||'-'||inMes||'-'||01;

                SELECT
                    fn_acrescimo_indice_mata_composto(dtVencimentoTMP, dtCalculoTMP, nuValor, 3, 1)
                INTO
                    nuValorComAcrescimo;

                nuValorAtMonetaria := nuValorAtMonetaria + nuValorComAcrescimo;

                ValorComJuros := (((nuValor + nuValorComAcrescimo) * nuAux) + (nuValor + nuValorComAcrescimo));
            END IF;

            -- JUROS COMPOSTO

            -- Calcula juros dos meses entre o período inicial e final
            WHILE inCount < inDiff LOOP
                inMes := inMes + 1;

                -- Caso chegue no mes 13, vira o ano
                IF (inMes = 13) THEN
                    inMes := 1;
                    inAno := inAno + 1;
                END IF;

                dtVencimentoTMP := inAno||'-'||inMes||'-'||01;
                dtCalculoTMP := inAno||'-'||inMes||'-'||01;

                SELECT
                    fn_acrescimo_indice_mata_composto(dtVencimentoTMP, dtCalculoTMP, nuValorAtMonetaria, 3, 1)
                INTO
                    nuValorComAcrescimo;

                nuValorAtMonetaria := nuValorAtMonetaria + nuValorComAcrescimo;

                ValorComJuros := (((ValorComJuros + nuValorComAcrescimo) * nuTaxa) + (ValorComJuros + nuValorComAcrescimo));

                inCount:=inCount + 1;
            END LOOP;
            -- JUROS COMPOSTO

            inMes := inMes + 1;

            -- Caso chegue no mes 13, vira o ano
            IF (inMes = 13) THEN
                inMes := 1;
                inAno := inAno + 1;
            END IF;

            dtVencimentoTMP := inAno||'-'||inMes||'-'||01;
            dtCalculoTMP := inAno||'-'||inMes||'-'||01;

            -- Calcula o juros do último mês
            SELECT
                fn_acrescimo_indice_mata_composto(dtVencimentoTMP, dtCalculoTMP, nuValorAtMonetaria, 3, 1)
            INTO
                nuValorComAcrescimo;

            nuAux := 0;
                -- Calcula a taxa de acordo com o número de dias
            i := 0;
            WHILE (i < inDiaCalculo) LOOP
                nuAux := (nuAux + nuTaxaFracao);
                i := i +1;
            END LOOP;

            ValorComJuros := (((ValorComJuros + nuValorComAcrescimo) * nuAux) + (ValorComJuros + nuValorComAcrescimo));

            -- Subtrai também o valor total da atualização monetária do período

           SELECT
               fn_acrescimo_indice_mata_composto(dtVencimento, dtDataCalculo, nuValor, 3, 1)
           INTO
               nuValorComAcrescimo;

            nuRetorno := ValorComJuros - nuValor; -- - nuValorComAcrescimo;
        END IF;
    ELSE
        nuRetorno := 0;
    END IF;

    RETURN (nuRetorno)::numeric(14,2);
END;
$$ language 'plpgsql';
