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
* script de funcao PLSQL
* 
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br

* $Revision: 23095 $
* $Name$
* $Autor: MArcia $
* Date: 2006/04/19 10:50:00 $
*
* Caso de uso: uc-04.05.14
*
* Objetivo: recebe o valor da base, verifica o valor total dos vales e retorna 
* apenas o valor a descontar.
*/




CREATE OR REPLACE FUNCTION pega1ValorDescontoValeTransporte(numeric) RETURNS numeric as '

DECLARE
    nuValorBase               ALIAS FOR $1;
    inCodContrato             INTEGER;
    stDataFinalCompetencia    VARCHAR;
    dtDataFinalCompetencia    VARCHAR;

    nuValorDosVales           NUMERIC := 0.00;

    nuPercentualDesconto      NUMERIC := 0.00;
    nuValorDesconto           NUMERIC := 0.00;

    stSql                     VARCHAR := '''';
    reRegistro                RECORD;
    stTipoBeneficio           VARCHAR := ''v'';

stEntidade VARCHAR := recuperarBufferTexto(''stEntidade'');
 BEGIN


    inCodContrato := recuperarBufferInteiro(''inCodContrato'');
    inCodContrato := recuperaContratoServidorPensionista(inCodContrato);
    stDataFinalCompetencia := recuperarBufferTexto(''stDataFinalCompetencia'');

    dtDataFinalCompetencia := to_date(substr( stDataFinalCompetencia ,1,10),''yyyy-mm-dd'');

    nuValorDosVales := pega0valortotaldevalestransporteporcontratonadata( inCodContrato, stDataFinalCompetencia);
    IF nuValorDosVales > 0 THEN

        stSql := '' select
                  COALESCE( percentual_desconto,0) as percentual_desconto

              FROM beneficio''||stEntidade||''.vigencia as v

              LEFT OUTER JOIN beneficio''||stEntidade||''.faixa_desconto as fd
                ON fd.cod_vigencia  = v.cod_vigencia
               AND ''|| nuValorBase ||'' between  fd.vl_inicial AND fd.vl_final

             WHERE v.vigencia  <= ''''''||dtDataFinalCompetencia||''''''
               AND v.tipo = ''''''||stTipoBeneficio||''''''

              ORDER BY v.vigencia desc

              LIMIT 1
            '' ;

        FOR reRegistro IN  EXECUTE stSql
        LOOP

            IF reRegistro.percentual_desconto is null  THEN
                nuPercentualDesconto := 0.00;
            ELSE
                nuPercentualDesconto := reRegistro.percentual_desconto;

                nuValorDesconto := arredondar( (nuValorBase * nuPercentualDesconto / 100) ,2);

                IF nuValorDesconto > nuValorDosVales THEN

                     nuValorDesconto = nuValorDosVales;

                END IF;

            END IF;

        END LOOP;

    END IF;

    RETURN nuValorDesconto;
END;
' LANGUAGE 'plpgsql';

