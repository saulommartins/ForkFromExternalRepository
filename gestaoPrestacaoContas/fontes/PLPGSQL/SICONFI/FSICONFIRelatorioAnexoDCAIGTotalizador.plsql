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
 * Script de função PLPGSQL - Relatório STN - RREO - Anexo 9
 *
 * URBEM Soluções de Gestão Pública Ltda
 * www.urbem.cnm.org.br
 *
 * Casos de uso: uc-06.01.10
 * 
 * $Id: FSICONFIRelatorioAnexoDCAIGTotalizador.plsql 62933 2015-07-09 14:18:16Z franver $
 */
CREATE OR REPLACE FUNCTION siconfi.fn_relatorio_anexo_dca_ig_totalizador(INTEGER,INTEGER) RETURNS NUMERIC[] AS $$
DECLARE
    inCodFuncao                  ALIAS FOR $1;
    inCodSubFuncao               ALIAS FOR $2;
    stSql                        VARCHAR := '';
    stSqlComplemento             VARCHAR := '';
    nuRPNaoProcessadosPagos      NUMERIC := 0.00;
    nuRPNaoProcessadosCancelados NUMERIC := 0.00;
    nuRPProcessadosPagos         NUMERIC := 0.00;
    nuRPProcessadosCancelados    NUMERIC := 0.00;
    arRetorno                    NUMERIC[] := array[0];
    crCursor                     REFCURSOR;

BEGIN
    
    IF inCodSubFuncao <> 0 THEN
        stSqlComplemento := ' AND subfuncao = '||inCodSubFuncao;
    ELSE
        stSqlComplemento := '';
    END IF;
    --Totaliza o valor Restos A Pagar Não processados Pagos
    stSql := 'SELECT SUM(vl_total)
                FROM tmp_nao_processados_pago
               WHERE estrutural <> ''91''
                 AND funcao = '||inCodFuncao||'
             ';
    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuRPNaoProcessadosPagos;
    CLOSE crCursor;

    --Totaliza o valor Restos A Pagar Não processados Cancelados
    stSql := 'SELECT SUM(vl_total)
                FROM tmp_nao_processados_cancelado
               WHERE estrutural <> ''91''
                 AND funcao = '||inCodFuncao||'
             ';
    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuRPNaoProcessadosCancelados;
    CLOSE crCursor;

    --Totaliza o valor Restos A Pagar processados Pagos
    stSql := 'SELECT SUM(vl_total)
                FROM tmp_processados_pago
               WHERE estrutural <> ''91''
                 AND funcao = '||inCodFuncao||'
             ';
    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuRPProcessadosPagos;
    CLOSE crCursor;

    --Totaliza o valor Restos A Pagar processados Cancelados
    stSql := 'SELECT SUM(vl_total)
                FROM tmp_processados_cancelado
               WHERE estrutural <> ''91''
                 AND funcao = '||inCodFuncao||'
             ';
    OPEN  crCursor FOR EXECUTE stSql || stSqlComplemento;
    FETCH crCursor INTO nuRPProcessadosCancelados;
    CLOSE crCursor;

    nuRPNaoProcessadosPagos      := COALESCE(nuRPNaoProcessadosPagos,0.00);
    nuRPNaoProcessadosCancelados := COALESCE(nuRPNaoProcessadosCancelados,0.00);
    nuRPProcessadosPagos         := COALESCE(nuRPProcessadosPagos,0.00);
    nuRPProcessadosCancelados    := COALESCE(nuRPProcessadosCancelados,0.00);

    --Preenche array de retorno
    arRetorno[1] := nuRPNaoProcessadosPagos;
    arRetorno[2] := nuRPNaoProcessadosCancelados;
    arRetorno[3] := nuRPProcessadosPagos;
    arRetorno[4] := nuRPProcessadosCancelados;

    RETURN arRetorno;

END;
$$ language 'plpgsql';
