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
* Valor de um lancamento!
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaValorLancamentoAnterior.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.4  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.buscaValorLancamentoAnterior(integer , char(4)) returns numeric as '
declare
    inImovel        ALIAS FOR $1;
    stExercicio     ALIAS FOR $2;
    nuValor         numeric;
begin
-- Lancamento do Calculo do Imovel    
        select d.valor 
          into nuValor     
          from arrecadacao.imovel_calculo a
            INNER JOIN arrecadacao.calculo as b ON b.cod_calculo = a.cod_calculo
            INNER JOIN arrecadacao.lancamento_calculo c ON c.cod_calculo = a.cod_calculo
            INNER JOIN arrecadacao.lancamento d ON d.cod_lancamento = c.cod_lancamento
         where 
           a.inscricao_municipal = inImovel
           and b.exercicio = stExercicio
      order by c.cod_lancamento desc
         limit 1;

   return nuValor;
end;
'language 'plpgsql';
