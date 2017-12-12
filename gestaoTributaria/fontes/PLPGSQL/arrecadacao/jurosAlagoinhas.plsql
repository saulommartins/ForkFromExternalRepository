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
* $Id: jurosAlagoinhas.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
* Calculo Valor de Juros para Alagoinhas
*/ 

/*
$Log$
Revision 1.5  2007/08/24 15:19:51  dibueno
Correções para contagem de meses

Revision 1.4  2007/08/06 15:54:23  dibueno
*** empty log message ***

Revision 1.3  2007/08/01 18:10:33  fabio
funções para acréscimos de Alagoinhas


*/

/*
    Função de Calculo de Juros para Alagoinhas/BA
*/
CREATE OR REPLACE FUNCTION fn_juros_alagoinhas(date,date,numeric,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        nuValor         ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        nuRetorno       NUMERIC = 0.00;
        nuAux           NUMERIC = 0.00;
        inDiff          INTEGER;
        inDias          INTEGER;
        i               INTEGER;
        inDiaInicio     INTEGER;
        inDiaFim        INTEGER;
        --nuTaxaFracao    NUMERIC = 0.033/100;
        nuTaxa          NUMERIC = 1.0/100;
        ValorComJuros   NUMERIC;

    BEGIN
       -- Calculo de Juros simples                                                                            
        
        inDiff := diff_datas_em_meses(dtVencimento,dtDataCalculo);


        if ( inDiff > 0 ) then

        inDiaInicio := split_part ( dtVencimento    , ''-'', 3);
        inDiaFim    := split_part ( dtDataCalculo   , ''-'', 3);

        if ( inDiaInicio > inDiaFim ) THEN
            inDiff := inDiff - 1;
        END IF;


            i :=  0; 
            while ( i < inDiff ) loop
                nuAux := nuAux + nuTaxa ;
                i := i +1;
            end loop;

            ValorComJuros := nuValor * nuAux;

            IF ( ValorComJuros = 0 ) THEN
                nuRetorno := 0;
            ELSE
                nuRetorno := ValorComJuros ;
            END IF;

        else

            nuRetorno := 0.00;

        end if;

        RETURN (nuRetorno)::numeric(14,2);
    END;
'language 'plpgsql';
