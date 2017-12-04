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
* $Id: fn_rl_cr_valor_pago_multa.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.13
*/

/*
$Log$
Revision 1.6  2007/04/02 14:14:05  dibueno
'raise' removido

Revision 1.5  2007/03/08 21:20:07  dibueno
Melhorias no relatório SINTETICO

Revision 1.4  2007/03/08 13:57:16  dibueno
Valor correção no relatorio sintético

Revision 1.3  2006/12/05 09:52:39  cercato
Bug #7737#

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_rl_cr_valor_pago_multa ( integer, integer, integer, integer, integer, varchar )  RETURNS NUMERIC(14,2) AS '
DECLARE
    inCodCredito    ALIAS FOR $1;
    inCodNatureza   ALIAS FOR $2;
    inCodGenero     ALIAS FOR $3;
    inCodEspecie    ALIAS FOR $4;
    inExercicio     ALIAS FOR $5;
    stLancamentos   ALIAS FOR $6;
    nuResultado     NUMERIC;
    reRecord        RECORD;
    stFiltroAux     VARCHAR := null;
BEGIN

    IF stLancamentos is not null THEN
        stFiltroAux := '' AND alc.cod_lancamento in (''|| stLancamentos ||'') '';
    END IF;

                    
    SELECT 
        sum(APA.valor)
    INTO
        nuResultado
    FROM
        arrecadacao.pagamento_acrescimo as APA
        INNER JOIN arrecadacao.calculo as ac
        ON ac.cod_calculo = APA.cod_calculo
        INNER JOIN arrecadacao.lancamento_calculo as alc
        ON alc.cod_calculo = ac.cod_calculo
    WHERE
        APA.cod_tipo = 3
        AND ac.cod_credito = inCodCredito
        AND ac.cod_natureza = inCodNatureza
        AND ac.cod_genero = inCodGenero
        AND ac.cod_especie = inCodEspecie
        AND ac.exercicio = inExercicio
        ||stFiltroAux
    ;

    return coalesce (nuResultado, 0.00 );

END;
' LANGUAGE 'plpgsql';
