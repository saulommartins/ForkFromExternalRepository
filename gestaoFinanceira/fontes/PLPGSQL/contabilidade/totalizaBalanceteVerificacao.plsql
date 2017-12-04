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

    $Id: totalizaBalanceteVerificacao.plsql 59612 2014-09-02 12:00:51Z gelson $

* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.02.22
*/
CREATE OR REPLACE FUNCTION contabilidade.fn_totaliza_balancete_verificacao(VARCHAR, VARCHAR, VARCHAR) RETURNS numeric[] AS $$
DECLARE
    stMascaraReduzida   ALIAS FOR $1;
    stDtInicial         ALIAS FOR $2;
    stDtFinal           ALIAS FOR $3;
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
    stSql := 'SELECT
                SUM( vl_lancamento ) as soma
                FROM    tmp_totaliza
                WHERE   cod_estrutural LIKE ''' || stMascaraReduzida || '%''
             ';

    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuSaldoAnterior;
    CLOSE crCursor;

    --Totaliza a conta débito
    stSql := 'SELECT
                SUM( vl_lancamento ) as soma
                FROM    tmp_totaliza_debito
                WHERE   cod_estrutural LIKE ''' || stMascaraReduzida || '%''
             ';
    OPEN  crCursor FOR EXECUTE stSql;
    FETCH crCursor INTO nuDebito;
    CLOSE crCursor;

    --Totaliza a conta crédito
    stSql := 'SELECT
                SUM( vl_lancamento ) as soma
                FROM    tmp_totaliza_credito
                WHERE   cod_estrutural LIKE ''' || stMascaraReduzida || '%''
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
