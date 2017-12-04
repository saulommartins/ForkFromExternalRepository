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
* $Id: multaAlagoinhas.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.4  2007/08/06 15:54:23  dibueno
*** empty log message ***

Revision 1.3  2007/08/01 18:10:33  fabio
funções para acréscimos de Alagoinhas


*/

/*
    Função de Calculo de Multa para Alagoinhas/BA
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_alagoinhas(date,date,float,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         NUMERIC;
        inDiff          INTEGER;
        inDias          INTEGER;
        i               INTEGER;
        nuTaxaFracao    NUMERIC = 0.17/100;
        nuTaxa          NUMERIC = 5.0/100;
        nuAux           NUMERIC = 0.00;
        ValorComMulta   NUMERIC;

    BEGIN

        -- recupera diferença em dias das datas
        inDias := diff_datas_em_dias( dtVencimento, dtDataCalculo );

        if ( inDias < 30 ) then

            i :=  0;
            while ( i < inDias ) loop
                nuAux := nuAux + nuTaxaFracao ;
                i := i +1;
            end loop;

            ValorComMulta := flCorrigido * nuAux;

            IF ( ValorComMulta = 0 ) THEN
                flMulta := 0;
            ELSE
                flMulta := ValorComMulta ;
            END IF;

        else

            ValorComMulta := (flCorrigido * nuTaxa);
            flMulta := ValorComMulta ;

        end if;

        RETURN flMulta::numeric(14,2);

    END;

'language 'plpgsql';
