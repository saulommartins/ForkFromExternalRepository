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
* $Id: fn_juros_vigencia_atual.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.1  2006/09/19 09:38:46  domluc
Novas funções de juro e multa que buscar valor cadastrado para o acrescimo

Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION fn_juros_vigencia_atual(date,date,float,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flJuros         NUMERIC = 0.00;
        flRetorno       NUMERIC = 0.00;
        inAnoVencimento INTEGER;
        inAnoCalculo    INTEGER;
        inDiff          INTEGER;
        nuJuros         numeric = 0.00;

    BEGIN
       -- Calculo de Juros simples                                                                            
        
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);

        IF ( inDiff > 0 ) THEN
            select valor
              into nuJuros
              from monetario.valor_acrescimo
             where valor_acrescimo.cod_acrescimo = inCodAcrescimo
               and valor_acrescimo.cod_tipo = inCodTipo
               and dtDataCalculo between valor_acrescimo.inicio_vigencia and now()::date
          order by valor_acrescimo.inicio_vigencia desc limit 1 ;

            flJuros        := nuJuros * inDiff; 
            flRetorno      := flCorrigido * ( flJuros / 100 ); 

        END IF;

        RETURN flRetorno::numeric(14,2);
    END;
'language 'plpgsql';
           
