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
* Soma Parcelas pagas de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: somaParcelasUnicasPagaLancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.somaParcelasUnicasPagasLancamento(integer , char(4)) returns numeric as $$
declare
    inLancamento    ALIAS FOR $1;
    stExercicio     ALIAS FOR $2;
    nuSomaUnica          numeric;
    reRecord        record;

begin

    -- somente parcelas normais
    CREATE TEMPORARY TABLE tmp_parc_unica as SELECT * FROM arrecadacao.parcela WHERE  cod_lancamento = inLancamento and nr_parcela = 0;
    CREATE TEMPORARY TABLE tmp_carne_unica as SELECT y.* FROM arrecadacao.parcela x, arrecadacao.carne y WHERE y.cod_parcela = x.cod_parcela and  x.cod_lancamento = inLancamento;

    select
        sum(ap.valor)
    into
        nuSomaUnica
    from
          tmp_parc_unica ap
        , tmp_carne_unica carne
        , arrecadacao.pagamento pagamento
    where
            ap.cod_lancamento = inLancamento
        and carne.cod_parcela = ap.cod_parcela
        and pagamento.numeracao = carne.numeracao
        and pagamento.cod_convenio = carne.cod_convenio
        and pagamento.valor > 0;

    DROP TABLE tmp_parc_unica;
    DROP TABLE tmp_carne_unica;

   return coalesce(nuSomaUnica,0.00);

end;

$$ language 'plpgsql';
