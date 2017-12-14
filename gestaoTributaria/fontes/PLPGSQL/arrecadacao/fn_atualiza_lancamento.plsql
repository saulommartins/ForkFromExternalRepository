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
* $Id: fn_atualiza_lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.10
* Caso de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_atualiza_lancamento(INTEGER,VARCHAR,NUMERIC)  RETURNS BOOLEAN AS '

DECLARE
    inCodLancamento   ALIAS FOR $1;
    stNumeracao       ALIAS FOR $2;
    nuValor           ALIAS FOR $3;

    inCodCalculo      INTEGER;
    inCodCredito      INTEGER;
    inCodNatureza     INTEGER;
    inCodGenero       INTEGER;
    inCodEspecie      INTEGER;
    inCodParcela      INTEGER;
    stExercicio       VARCHAR;
    nuValorLancamento NUMERIC := 0;
    nuValorCalculo    NUMERIC := 0;
    nuValorParcela    NUMERIC := 0;
    stSql             VARCHAR := '''';
    boRetorno         BOOLEAN;

BEGIN

    -- atualiza valor do lancamento
    SELECT valor INTO nuValorLancamento FROM arrecadacao.lancamento WHERE cod_lancamento=inCodLancamento;
    nuValorLancamento := nuValorLancamento + nuValor;
    UPDATE arrecadacao.lancamento SET valor=nuValorLancamento WHERE cod_lancamento=inCodLancamento;

    -- atualiza valor do calculo
    SELECT
        c.cod_calculo,
        c.cod_credito,
        c.cod_natureza,
        c.cod_genero,
        c.cod_especie,
        c.exercicio,
        c.valor
    INTO
        inCodCalculo,
        inCodCredito,
        inCodNatureza,
        inCodGenero,
        inCodEspecie,
        stExercicio,
        nuValorCalculo
    FROM
        arrecadacao.calculo c,
        arrecadacao.lancamento_calculo lc
    WHERE
        lc.cod_calculo=c.cod_calculo AND
        lc.cod_lancamento=inCodLancamento;

    UPDATE
        arrecadacao.calculo SET valor=nuValorLancamento
    WHERE
        cod_calculo=inCodCalculo AND
        cod_credito=inCodCredito AND
        cod_natureza=inCodNatureza AND
        cod_genero=inCodGenero AND
        cod_especie=inCodEspecie AND
        exercicio=stExercicio;

    -- atualiza valor da parcela
    SELECT cod_parcela INTO inCodParcela FROM arrecadacao.carne WHERE numeracao=stNumeracao;
    UPDATE arrecadacao.parcela SET valor=nuValor WHERE cod_parcela=inCodParcela;

    return boRetorno;
END;

' LANGUAGE 'plpgsql';
