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

    $Id: FContabilidadeApuracaoSuperavitDeficitTotalizaRecurso.plsql 66680 2016-11-09 13:01:02Z franver $

* $Revision: 66680 $
* $Name$
* $Author: franver $
* $Date: 2016-11-09 11:01:02 -0200 (Wed, 09 Nov 2016) $
*
* Casos de uso: uc-02.02.22
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_rl_apuracao_superavit_defict_totaliza_recurso(INTEGER) RETURNS numeric[] AS $$
DECLARE
    inCodRecurso        ALIAS FOR $1;
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
    stSql := 'SELECT SUM(COALESCE(vl_lancamento, 0.00)) as soma
                FROM tmp_totaliza
               WHERE cod_recurso = '||inCodRecurso||'
             ';

    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuSaldoAnterior;
    CLOSE crCursor;

    --Totaliza a conta débito
    stSql := 'SELECT SUM(COALESCE(vl_lancamento,0.00)) as soma
                FROM tmp_totaliza_debito
               WHERE cod_recurso = '||inCodRecurso||'
             ';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuDebito;
    CLOSE crCursor;

    --Totaliza a conta crédito
    stSql := 'SELECT SUM(COALESCE(vl_lancamento,0.00) ) as soma
                FROM tmp_totaliza_credito
               WHERE cod_recurso = '||inCodRecurso||'
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
