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
* $Id: multaMata2006.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2007/09/11 18:52:47  fabio
tornando o nome da função mais claro em função do seu cálculo para futura referência

Revision 1.1  2006/12/20 10:46:17  fabio
funcoes para buscat o valor do imposto durante calculo de iptu, e nova funcao de multa p/ mata (2006 ->)

Revision 1.1  2006/11/17 16:42:55  domluc
Funções para Calculo de Multa em Mata de Sao Joao,
add funcao para diff em dias entre datas

Revision 1.6  2006/09/19 09:39:30  domluc
Adequação ao numero de parametros

Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

/*
    Função de Calculo de Multa para Mata de São João/BA
     - Dado data de vencimento e de calculo, a função deve retornor o percentual a ser aplicado
*/

CREATE OR REPLACE FUNCTION fn_multa_2006(date,date,float,integer,integer) RETURNS numeric as '

    DECLARE
        dtVencimento    ALIAS FOR $1;
        dtDataCalculo   ALIAS FOR $2;
        flCorrigido     ALIAS FOR $3;
        inCodAcrescimo  ALIAS FOR $4;
        inCodTipo       ALIAS FOR $5;
        flMulta         NUMERIC;
        inDiff          INTEGER;    
    BEGIN
        -- recupera diferença em dias das datas
        inDiff := diff_datas_em_dias(dtVencimento,dtDataCalculo);
        IF dtVencimento <= dtDataCalculo  THEN                
             
            IF ( inDiff > 0 and inDiff <= 30 ) THEN
                flMulta := ( flCorrigido * 5) / 100 ; -- 1 até 30 dias, aplica 5%
            ELSIF ( inDiff >=31  and inDiff <=90 ) THEN
                flMulta := ( flCorrigido * 10) / 100 ; -- 31 até 60 dias , aplica 10 %
            ELSIF ( inDiff >= 91 ) THEN
                flMulta := ( flCorrigido * 15) / 100 ; -- maior que 60 dias, aplica 15%
            ELSE
                flMulta := 0.00;
            END IF;
        END IF;             
    
        RETURN flMulta::numeric(14,2);
    END;
'language 'plpgsql';
           
