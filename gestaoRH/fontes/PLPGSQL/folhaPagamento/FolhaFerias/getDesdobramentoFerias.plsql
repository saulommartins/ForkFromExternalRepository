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
--    * Data de Criação: 08/04/2009
--
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 24425 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-07-31 17:59:34 -0300 (Ter, 31 Jul 2007) $
--
--    * Casos de uso: uc-04.05.09
--*/

CREATE OR REPLACE FUNCTION getDesdobramentoFerias(VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stDesdobramento             ALIAS FOR $1;
    stEntidade                  ALIAS FOR $2;
    stDescricao                 VARCHAR;
    stSql                       VARCHAR;
BEGIN
    stSql := ' SELECT descricao
                 FROM folhapagamento'||stEntidade||'.configuracao_desdobramento
                WHERE cod_configuracao = 2
                  AND desdobramento = '||quote_literal(stDesdobramento)||'';

    IF stDesdobramento IS NOT NULL THEN
        stDescricao := selectIntoVarchar(stSql);
    ELSE
        stDescricao := NULL;
    END IF;
    
    RETURN stDescricao;
END;
$$ LANGUAGE 'plpgsql';
