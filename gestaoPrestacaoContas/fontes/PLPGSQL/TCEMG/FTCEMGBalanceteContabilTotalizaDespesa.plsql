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

    $Id: FTCEMGBalanceteContabilTotalizaDespesa.plsql 62872 2015-07-01 20:16:55Z franver $

* $Revision: 62872 $
* $Name$
* $Author: franver $
* $Date: 2015-07-01 17:16:55 -0300 (Wed, 01 Jul 2015) $
*
* Casos de uso: uc-02.02.22
*/
CREATE OR REPLACE FUNCTION tcemg.fn_balancete_contabil_totaliza_despesa(VARCHAR, INTEGER, INTEGER, INTEGER, INTEGER,  INTEGER, INTEGER, INTEGER, VARCHAR) RETURNS numeric[] AS $$
DECLARE
    stMascaraReduzida   ALIAS FOR $1;
    inNumOrgao          ALIAS FOR $2;
    inNumUnidade        ALIAS FOR $3;
    inCodFuncao         ALIAS FOR $4;
    inCodSubFuncao      ALIAS FOR $5;
    inNumPrograma       ALIAS FOR $6;
    inNumAcao           ALIAS FOR $7;
    inCodRecurso        ALIAS FOR $8;
    stNaturezaDespesa   ALIAS FOR $9;
    stSql               VARCHAR   := '';
    stSqlComplemento    VARCHAR   := '';
    nuSaldoAnterior     NUMERIC   := 0;
    nuDebito            NUMERIC   := 0;
    nuCredito           NUMERIC   := 0;
    nuSaldoAtual        NUMERIC   := 0;
    arRetorno           NUMERIC[] := array[0];
    crCursor            REFCURSOR;


BEGIN

--    --Totaliza o saldo anterior
    stSql := 'SELECT SUM( vl_lancamento ) as soma
                FROM tmp_totaliza
               WHERE cod_estrutural LIKE ''' || stMascaraReduzida || '%''
                 AND num_orgao        = '||inNumOrgao||'
                 AND num_unidade      = '||inNumUnidade||'
                 AND cod_funcao       = '||inCodFuncao||'
                 AND cod_subfuncao    = '||inCodSubFuncao||'
                 AND num_programa     = '||inNumPrograma||'
                 AND num_acao         = '||inNumAcao||'
                 AND cod_recurso      = '||inCodRecurso||'
                 AND natureza_despesa LIKE '''||stNaturezaDespesa||'''
             ';

    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuSaldoAnterior;
    CLOSE crCursor;

    --Totaliza a conta débito
    stSql := 'SELECT SUM( vl_lancamento ) as soma
                FROM tmp_totaliza_debito
               WHERE cod_estrutural LIKE ''' || stMascaraReduzida || '%''
                 AND num_orgao        = '||inNumOrgao||'
                 AND num_unidade      = '||inNumUnidade||'
                 AND cod_funcao       = '||inCodFuncao||'
                 AND cod_subfuncao    = '||inCodSubFuncao||'
                 AND num_programa     = '||inNumPrograma||'
                 AND num_acao         = '||inNumAcao||'
                 AND cod_recurso      = '||inCodRecurso||'
                 AND natureza_despesa LIKE '''||stNaturezaDespesa||'''
             ';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuDebito;
    CLOSE crCursor;

    --Totaliza a conta crédito
    stSql := 'SELECT SUM( vl_lancamento ) as soma
                FROM tmp_totaliza_credito
               WHERE cod_estrutural LIKE '''||stMascaraReduzida||'%''
                 AND num_orgao        = '||inNumOrgao||'
                 AND num_unidade      = '||inNumUnidade||'
                 AND cod_funcao       = '||inCodFuncao||'
                 AND cod_subfuncao    = '||inCodSubFuncao||'
                 AND num_programa     = '||inNumPrograma||'
                 AND num_acao         = '||inNumAcao||'
                 AND cod_recurso      = '||inCodRecurso||'
                 AND natureza_despesa LIKE '''||stNaturezaDespesa||'''
             ';

    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuCredito;
    CLOSE crCursor;

    nuSaldoAnterior := coalesce(nuSaldoAnterior,0.00);
    nuCredito       := coalesce(nuCredito,0.00);
    nuDebito        := coalesce(nuDebito,0.00);

    --Totaliza Saldo Atual
    nuSaldoAtual    := ( nuSaldoAnterior + nuDebito ) + nuCredito;
    nuSaldoAtual    := coalesce(nuSaldoAtual,0.00);

    --Preenche array de retorno
    arRetorno[1] := nuSaldoAnterior;
    arRetorno[2] := nuDebito;
    arRetorno[3] := nuCredito;
    arRetorno[4] := nuSaldoAtual;

    RETURN arRetorno;
END;
$$ language 'plpgsql';
