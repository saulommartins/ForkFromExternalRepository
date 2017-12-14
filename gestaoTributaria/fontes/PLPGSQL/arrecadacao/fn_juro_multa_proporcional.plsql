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
* $Id: fn_juro_multa_proporcional.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.4  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION fn_juro_multa_proporcional(date,date,float) RETURNS varchar as '

    DECLARE
        dtVencimento        ALIAS FOR $1;
        dtDataCalculo       ALIAS FOR $2;
        flValorComAcrescimo ALIAS FOR $3;

        -- valores calculados para retorno
        flValorParcela   FLOAT = 0;
        flValorJuro      FLOAT = 0;
        flValorMulta     FLOAT = 0;

        -- valores percentuais
        flMulta          FLOAT = 0;
        flJuro           FLOAT = 0;
        flPercentJuroMulta FLOAT = 100;

        inDiff          INTEGER;
        stRetorno       VARCHAR;

    BEGIN

        -- diferenca de meses entre a data de vencimento e data de pagamento
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);

        IF inDiff < 0 THEN

            flValorParcela := flValorComAcrescimo;
            flValorMulta := 0;
            flValorJuro := 0;

        ELSE

            -- calcula multa
            IF dtVencimento < dtDataCalculo  THEN
                IF ( inDiff = 0 ) THEN
                    flMulta := 5;
                ELSIF ( inDiff = 1 ) THEN
                    flMulta := 10;
                ELSIF ( inDiff = 2 ) THEN
                    flMulta := 15;
                ELSE
                    flMulta := 20;
                END IF;
            END IF;

            -- calcula juro
            flJuro := inDiff * 1;

            flPercentJuroMulta := flPercentJuroMulta + flJuro + flMulta;

            -- calcula valor original da parcela
            flValorParcela := ( flValorComAcrescimo * 100 ) / flPercentJuroMulta;
            flValorParcela := flValorParcela::numeric(14,2);

            -- calcula valor de juro e multa
            flValorJuro := ( flValorParcela * flJuro ) / 100;
            flValorJuro := flValorJuro::numeric(14,2);

            flValorMulta := ( flValorParcela * flMulta ) / 100;
            flValorMulta := flValorMulta::numeric(14,2);

        END IF;

        stRetorno := flValorParcela||''-''||flValorMulta||''-''||flValorJuro;
        RETURN stRetorno;
    END;
'language 'plpgsql';

