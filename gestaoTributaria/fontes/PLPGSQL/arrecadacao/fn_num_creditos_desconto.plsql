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
* $Id: fn_num_creditos_desconto.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.4  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_num_creditos_desconto( int,int, int )  RETURNS INTEGER AS '
DECLARE
    inCodLancamento ALIAS FOR $1;   
    inCodParcela    ALIAS FOR $2;
    inExercicio         ALIAS FOR $3;
    inRetorno       INTEGER;
BEGIN
                SELECT count(*) as num_creditos 
                  INTO inRetorno 
                  FROM arrecadacao.parcela ap
                     , arrecadacao.lancamento al
                     , arrecadacao.lancamento_calculo alc
                     , arrecadacao.calculo calc
            INNER JOIN arrecadacao.calculo_grupo_credito acgc
                    ON acgc.cod_calculo = calc.cod_calculo
            INNER JOIN arrecadacao.grupo_credito agc
                    ON agc.cod_grupo = acgc.cod_grupo
                    AND agc.ano_exercicio = acgc.ano_exercicio
            INNER JOIN arrecadacao.credito_grupo acg
                    ON acg.cod_grupo = agc.cod_grupo 
                   AND acg.cod_credito = calc.cod_credito
                   AND acg.cod_especie = calc.cod_especie
                   AND acg.cod_genero  = calc.cod_genero
                   AND acg.cod_natureza= calc.cod_natureza   
                 WHERE ap.cod_lancamento = al.cod_lancamento
                   AND alc.cod_lancamento = al.cod_lancamento
                   AND calc.cod_calculo = alc.cod_calculo
                   AND al.cod_lancamento = inCodLancamento 
                   AND ap.cod_parcela = inCodParcela
                   AND acg.desconto = true
                   AND agc.ano_exercicio = inExercicio;
    
    RETURN inRetorno;
END;
' LANGUAGE 'plpgsql';
