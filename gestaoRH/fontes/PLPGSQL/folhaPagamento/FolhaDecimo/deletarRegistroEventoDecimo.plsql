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
--/**
--    * Função PLSQL
--    * Data de Criação: 25/10/2006
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Vandré Miguel Ramos
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23101 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-06 10:17:40 -0300 (Qua, 06 Jun 2007) $
--
--    * Casos de uso: uc-04.05.11
--*/

CREATE OR REPLACE FUNCTION deletarRegistroEventoDecimo(INTEGER,INTEGER,VARCHAR) RETURNS BOOLEAN as $$

DECLARE
    inCodContrato               ALIAS FOR $1;
    inCodPeriodoMovimentacao    ALIAS FOR $2;
    stDesdobramento             ALIAS FOR $3;
    stSql                       VARCHAR := '';
    stEntidade VARCHAR := recuperarBufferTexto('stEntidade');
BEGIN

  stSql := '  DELETE FROM folhapagamento'||stEntidade||'.ultimo_registro_evento_decimo
        WHERE cod_registro IN (SELECT cod_registro
                                 FROM folhapagamento'||stEntidade||'.registro_evento_decimo
                                WHERE cod_contrato = '||inCodContrato||'
                                  AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                  AND desdobramento            = '''||stDesdobramento||''' )';
  EXECUTE stSql;

  stSql := 'DELETE FROM folhapagamento'||stEntidade||'.registro_evento_decimo
                                WHERE cod_contrato = '||inCodContrato||'
                                  AND cod_periodo_movimentacao = '||inCodPeriodoMovimentacao||'
                                  AND desdobramento            = '''||stDesdobramento||''' ';
  EXECUTE stSql;

  RETURN true;
END;
$$LANGUAGE 'plpgsql';
