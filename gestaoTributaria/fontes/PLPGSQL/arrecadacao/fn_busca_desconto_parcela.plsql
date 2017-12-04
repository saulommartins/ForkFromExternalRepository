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
* $Id: fn_busca_desconto_parcela.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.7  2006/11/13 11:28:06  dibueno
Adicionado coalesce para retornar 2 casas decimais

Revision 1.6  2006/11/06 16:35:11  dibueno
Bug #7351#

Revision 1.5  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION fn_busca_desconto_parcela(int,date) returns numeric as '
declare
    inCodParcela    ALIAS FOR $1;
    dtDataCalculo   ALIAS FOR $2;
    nuRetorno       NUMERIC;
begin
            SELECT coalesce(pd.valor, 0.00) 
              INTO nuRetorno 
              FROM 
                 arrecadacao.parcela_desconto pd 
              INNER JOIN
                arrecadacao.parcela p
             ON pd.cod_parcela = p.cod_parcela                
               AND dtDataCalculo <= arrecadacao.fn_atualiza_data_vencimento( pd.vencimento )
               AND p.cod_parcela = inCodParcela ;
    return coalesce ( nuRetorno, 0.00 );
    --
end;
'language 'plpgsql';
