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
* Quantidade de Parcelas PAGAS de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: fn_total_parcelas_pagas.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2007/08/16 14:16:58  dibueno
*** empty log message ***


*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_total_parcelas_pagas(integer , char(4)) returns integer as '
declare
    inLancamento    ALIAS FOR $1;
    stExercicio     ALIAS FOR $2;
    inSoma          integer;
    reRecord        record;

begin

    select
        count(pagamento.numeracao)
    into
        inSoma
    from
        arrecadacao.parcela as ap
        INNER JOIN arrecadacao.carne
        ON carne.cod_parcela = ap.cod_parcela
        , arrecadacao.pagamento pagamento
    where
        ap.cod_lancamento = inLancamento
        and pagamento.numeracao = carne.numeracao
        and pagamento.cod_convenio = carne.cod_convenio
        and pagamento.valor > 0;

   return coalesce(inSoma,0);

end;

'language 'plpgsql';
