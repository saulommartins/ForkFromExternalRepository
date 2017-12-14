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
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: calculaDesoneracao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos d uso: uc-05.03.04
*/

/*
$Log$
Revision 1.3  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION calculaDesoneracao(INTEGER,INTEGER,NUMERIC) RETURNS NUMERIC AS '

DECLARE
    inCodCredito      ALIAS FOR $1;
    inNumCGM          ALIAS FOR $2;
    nuValorCalculo    ALIAS FOR $3;
    nuValorDesonerado NUMERIC;
    stNomFuncao       VARCHAR;
    stNomFuncaoExec   VARCHAR;

BEGIN
    -- Verifica se o CGM informado possui desoneracao concedida
    -- Recupera a o nome da Funcao de desoneracao cadastrada para o CREDITO informado
    SELECT
        ADMF.nom_funcao
    INTO
        stNomFuncao
    FROM
        administracao.funcao    ADMF,
        arrecadacao.desoneracao ARRD,
        arrecadacao.desonerado  ARRDO
    WHERE
            ADMF.cod_funcao      = ARRD.cod_funcao
        AND ADMF.cod_modulo      = ARRD.cod_modulo
        AND ADMF.cod_biblioteca  = ARRD.cod_biblioteca
        AND ARRD.cod_desoneracao = ARRDO.cod_desoneracao
        AND ARRD.cod_credito     = inCodCredito
        AND ARRDO.numcgm         = inNumCGM;

    IF stNomFuncao IS NOT NULL THEN
        stNomFuncaoExec := stNomFuncao;
        EXECUTE ''SELECT ''||stNomFuncaoExec||''(''||nuValorCalculo::numeric||'')'' INTO nuValorDesonerado;
    ELSE
        return nuValorCalculo;
    END IF;

    return nuValorDesonerado;
END;

' LANGUAGE 'plpgsql';
