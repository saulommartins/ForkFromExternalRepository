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
* $Id: fn_acrescimos.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.4  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE function fn_acrescimos(date, date, float)
    returns varchar as '

    declare
        
        vencimento      alias for $1;
        data_hoje       alias for $2;
        corrigido       alias for $3;
        juros           float = 0;
        multa           float = 0;
        ano_vencimento  integer;
        ano_data_hoje   integer;
        retorno     varchar;

    begin
    
    -- Calculo de Juros simples
    
        ano_vencimento  := extract(year from vencimento);
        ano_data_hoje       := extract(year from data_hoje);
        if vencimento < data_hoje then
            if ano_data_hoje > ano_vencimento then
                juros := juros + (((ano_data_hoje - 1) - ano_vencimento) * 12);
                juros := juros + extract(month from data_hoje);
                if (ano_vencimento + 1)  = ano_data_hoje then
                    juros := juros + (13 - extract(month from (vencimento + 1)));
                end if;
            else
                juros := juros + ((extract(month from data_hoje)+1) - extract(month from (vencimento+1)));
            end if;
        end if;
        juros := juros * 1; 
        

    -- Calculo de Multa simples     

       if vencimento < data_hoje then
           multa := 2;
               end if;
    
    -- Retorno
      
        retorno := to_char(juros, ''9990.9999'')||'', ''||to_char((juros / 100) * corrigido, ''9999999990.99'')||'', ''||to_char(multa, ''9990.9999'')||'', ''||to_char((multa / 100) * corrigido, ''9999999990.99'');

        return retorno;

    end;

' language 'plpgsql';
