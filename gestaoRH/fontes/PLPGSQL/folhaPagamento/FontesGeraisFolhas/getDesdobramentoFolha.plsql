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

CREATE OR REPLACE FUNCTION getDesdobramentoFolha(INTEGER,VARCHAR,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodConfiguracao           ALIAS FOR $1;
    stDesdobramento             ALIAS FOR $2;
    stEntidade                  ALIAS FOR $3;
    stDescricao                 VARCHAR;
BEGIN
    IF inCodConfiguracao = 1 THEN
        stDescricao = getDesdobramentoSalario(stDesdobramento,stEntidade);
    END IF;
    IF inCodConfiguracao = 2 THEN
        stDescricao = getDesdobramentoFerias(stDesdobramento,stEntidade);
    END IF;
    IF inCodConfiguracao = 3 THEN
        stDescricao = getDesdobramentoDecimo(stDesdobramento,stEntidade);
    END IF;
    IF inCodConfiguracao = 4 THEN
        stDescricao = getDesdobramentoRescisao(stDesdobramento,stEntidade);
    END IF;

    RETURN stDescricao;
END;
$$ LANGUAGE 'plpgsql';
