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
* $Id: fn_acrescimo_infracao_iptu.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.04.00
* Calculo Valor de Juros Especial (Mata)
*/

CREATE OR REPLACE FUNCTION fn_acrescimo_infracao_iptu ( date, date, numeric, integer, integer ) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        nuValor         ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        inCodAcrCorr    INTEGER;
        nuCorrecao      NUMERIC = 0.00;
        nuRetorno       NUMERIC = 0.00;
        inDiff          INTEGER;
        nuJuroCorrente  numeric = 0.0;
        nuJuroTotal     numeric = 0.0;
        inMesInicio     integer;
        inMesFim        integer;
        inAno           integer;
        inTeste         integer;

    BEGIN


        -- Calculo de Juros simples
        SELECT * INTO nuCorrecao
        FROM fn_acrescimo_indice ( dtVencimento, dtDataCalculo, nuValor, 3, 1);

        nuRetorno := ( nuValor + nuCorrecao );
        nuRetorno := ( nuRetorno * 0.5 )::numeric(14,2);

        RETURN nuRetorno::numeric(14,2);

    END;
'language 'plpgsql';
