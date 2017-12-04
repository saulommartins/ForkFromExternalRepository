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
--    * Data de Criação: 23/12/2008
--
--
--    * @author Analista: Dagiane Vieira
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 25971 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $
--
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION deletarEventoCalculadoDependente(INTEGER,INTEGER,VARCHAR,VARCHAR) RETURNS BOOLEAN as $$
DECLARE
    inCodRegistro               ALIAS FOR $1;
    inCodEvento                 ALIAS FOR $2;
    stDesdobramento             ALIAS FOR $3;
    stTimestamp                 ALIAS FOR $4;
    stSql                       VARCHAR;
    stEntidade                  VARCHAR;
    stTipoFolha                 VARCHAR;
    boRetorno                   BOOLEAN;
BEGIN
    stEntidade      := recuperarBufferTexto('stEntidade');
    stTipoFolha     := recuperarBufferTexto('stTipoFolha');
    IF stTipoFolha = 'S' THEN
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_calculado_dependente
                   WHERE cod_registro = '|| inCodRegistro;
    END IF;
    IF stTipoFolha = 'C' THEN
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_complementar_calculado_dependente
                   WHERE cod_registro = '|| inCodRegistro ||'
                     AND cod_evento = '|| inCodEvento ||'                
                     AND cod_configuracao = '|| stDesdobramento::integer ||'
                     AND timestamp_registro = '|| quote_literal(stTimestamp) ||' ';
    END IF;
    IF stTipoFolha = 'F' THEN
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_ferias_calculado_dependente
                   WHERE cod_registro = '|| inCodRegistro ||'
                     AND cod_evento = '|| inCodEvento ||'                
                     AND desdobramento = '|| quote_literal(stDesdobramento) ||'
                     AND timestamp_registro = '|| quote_literal(stTimestamp) ||' ';
    END IF;
    IF stTipoFolha = 'R' THEN
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_rescisao_calculado_dependente
                   WHERE cod_registro = '|| inCodRegistro ||'
                     AND cod_evento = '|| inCodEvento ||'                
                     AND desdobramento = '|| quote_literal(stDesdobramento) ||'
                     AND timestamp_registro = '|| quote_literal(stTimestamp) ||' ';
    END IF;
    IF stTipoFolha = 'D' THEN
        stSql := 'DELETE FROM folhapagamento'|| stEntidade ||'.evento_decimo_calculado_dependente
                   WHERE cod_registro = '|| inCodRegistro ||'
                     AND cod_evento = '|| inCodEvento ||'                
                     AND desdobramento = '|| quote_literal(stDesdobramento) ||'
                     AND timestamp_registro = '|| quote_literal(stTimestamp) ||' ';
    END IF;
    EXECUTE stSql;
    RETURN true;
END;
$$ LANGUAGE 'plpgsql';
