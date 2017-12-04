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
* $Id: fn_multa_vigencia_atual.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.3  2007/07/18 20:12:58  cercato
Bug #9698#

Revision 1.2  2006/09/19 15:29:04  domluc
*** empty log message ***

Revision 1.1  2006/09/19 09:38:46  domluc
Novas funções de juro e multa que buscar valor cadastrado para o acrescimo

Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION fn_multa_vigencia_atual(date,date,float,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         FLOAT = 0;
        inDiff          INTEGER;
        nuValorJuro     numeric;
        inUltimoDiaVencimento INTEGER;
        inAnoVencimento INTEGER;
        inMesVencimento INTEGER;

    BEGIN
        -- Calculo de Multa simples     
        -- Para Aplicar Multa tem de ser a partir do mes seguinte
        inUltimoDiaVencimento := extract (day from dtVencimento);
        inAnoVencimento := extract (year from dtVencimento);
        inMesVencimento := extract (month from dtVencimento);
        
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);
        if ( inUltimoDiaVencimento = calculaUltimoDiaMes ( inAnoVencimento , inMesVencimento) ) then
            inDiff := inDiff - 1;
        end if;        

        IF dtVencimento < dtDataCalculo  AND inDiff > 0 THEN                

            -- busca percetual do acrescimo
            select valor
              into nuValorJuro
              from monetario.valor_acrescimo
             where valor_acrescimo.cod_acrescimo = inCodAcrescimo
               and valor_acrescimo.cod_tipo = inCodTipo
               and dtDataCalculo >= valor_acrescimo.inicio_vigencia
          order by valor_acrescimo.inicio_vigencia desc limit 1 ;
            
            flMulta := flCorrigido * ( nuValorJuro / 100);

        END IF;             
    
        RETURN flMulta::numeric(14,2);
    END;
'language 'plpgsql';
           
