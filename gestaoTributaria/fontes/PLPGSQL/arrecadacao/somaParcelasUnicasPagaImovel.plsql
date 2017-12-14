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
* $Id: somaParcelasUnicasPagaImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.00
*/

/*
$Log$
Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.somaParcelasUnicasPagasImovel(integer , char(4)) returns numeric as '
declare
    inImovel        ALIAS FOR $1;
    stExercicio     ALIAS FOR $2;
    nuSoma          numeric;
    reRecord        record;
    inLancamento    integer; 
begin
-- Lancamento do Calculo do Imovel    
        select cod_lancamento 
          into inLancamento
          from arrecadacao.imovel_calculo a
             , arrecadacao.calculo b
             , arrecadacao.lancamento_calculo c
         where a.cod_calculo = b.cod_calculo
           and b.cod_calculo = c.cod_calculo
           and a.inscricao_municipal = inImovel
           and b.exercicio = stExercicio
      order by cod_lancamento desc
         limit 1;

if ( FOUND ) then 
    CREATE TEMPORARY TABLE tmp_la2c as SELECT * FROM arrecadacao.lancamento_calculo WHERE cod_lancamento = inLancamento;
    CREATE TEMPORARY TABLE tmp_lanc as SELECT * FROM arrecadacao.lancamento WHERE  cod_lancamento = inLancamento;
    -- somente parcelas unicas
    CREATE TEMPORARY TABLE tmp_parc as SELECT * FROM arrecadacao.parcela WHERE  cod_lancamento = inLancamento and nr_parcela = 0;
    CREATE TEMPORARY TABLE tmp_carne as SELECT y.* FROM arrecadacao.parcela x, arrecadacao.carne y WHERE y.cod_parcela = x.cod_parcela and  x.cod_lancamento = inLancamento;
        
    FOR reRecord IN EXECUTE '' select sum(ap.valor) as soma
                                 from tmp_lanc al
                                    , tmp_parc ap
                                    , tmp_carne carne
                                    , arrecadacao.pagamento pagamento
                                where ap.cod_lancamento = al.cod_lancamento
                                  and carne.cod_parcela = ap.cod_parcela
                                  and pagamento.numeracao = carne.numeracao
                                  and pagamento.cod_convenio = carne.cod_convenio
                                  and al.cod_lancamento = ''||inLancamento LOOP
        nuSoma := reRecord.soma;
    END LOOP;
    
    DROP TABLE tmp_la2c;
    DROP TABLE tmp_lanc;
    DROP TABLE tmp_parc;
    DROP TABLE tmp_carne; 
end if;

   return coalesce(nuSoma,0.00);
end;
'language 'plpgsql';
