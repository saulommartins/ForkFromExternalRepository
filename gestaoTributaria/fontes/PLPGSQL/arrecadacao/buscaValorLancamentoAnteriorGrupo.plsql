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
* $Id: buscaValorLancamentoAnteriorGrupo.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.7  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

-- Drop Function devido a alteração do tipo de retorno.
Drop Function arrecadacao.buscaValorLancamentoAnteriorGrupo( integer , integer, char(4) );

CREATE OR REPLACE FUNCTION arrecadacao.buscaValorLancamentoAnteriorGrupo( integer , integer, char(4) ) returns varchar as $$
declare
    inImovel        ALIAS FOR $1;
    inCodGrupo      ALIAS FOR $2;
    stExercicio     ALIAS FOR $3;
    nuValorTmp      numeric;
    inCodLancamento integer;
begin
-- Lancamento do Calculo do Imovel
        select d.valor,d.cod_lancamento
          into nuValorTmp,inCodLancamento
          from
               arrecadacao.imovel_calculo a
               INNER JOIN arrecadacao.calculo as b ON b.cod_calculo = a.cod_calculo
               INNER JOIN arrecadacao.lancamento_calculo c ON c.cod_calculo = a.cod_calculo
               INNER JOIN arrecadacao.lancamento d ON d.cod_lancamento = c.cod_lancamento
               INNER JOIN arrecadacao.calculo_grupo_credito cg ON cg.cod_calculo = c.cod_calculo AND cg.ano_exercicio = b.exercicio
         where 
            cg.cod_grupo = inCodGrupo
            and a.inscricao_municipal = inImovel
            and b.exercicio = stExercicio
        order by c.cod_lancamento desc
            limit 1;

   return nuValorTmp||'-'||inCodLancamento;
end;
$$ language 'plpgsql';
