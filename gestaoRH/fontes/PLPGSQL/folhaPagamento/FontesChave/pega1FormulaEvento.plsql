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
--    * Data de Criação: 00/00/0000
--
--
--    * @author Analista: Vandré Miguel Ramos
--    * @author Desenvolvedor: Diego Lemos de Souza
--
--    * @package URBEM
--    * @subpackage
--
--    $Revision: 23095 $
--    $Name$
--    $Author: souzadl $
--    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $
--
--    * Casos de uso: uc-04.05.09
--    * Casos de uso: uc-04.05.10
--*/

CREATE OR REPLACE FUNCTION pega1FormulaEvento(INTEGER,INTEGER,INTEGER,INTEGER,INTEGER,VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    inCodEvento         ALIAS FOR $1;
    inCodConfiguracao   ALIAS FOR $2;
    inCodSubDivisao     ALIAS FOR $3;
    inCodCargo          ALIAS FOR $4;
    inCodEspecialidade  ALIAS FOR $5;
    stNaturezaParametro ALIAS FOR $6;
    stFormulaEvento     VARCHAR;
    stNatureza          VARCHAR;
BEGIN
    stNatureza := criarBufferTexto('stNatureza',stNaturezaParametro);
    stFormulaEvento := pegaFormulaEvento(inCodEvento,inCodConfiguracao,inCodSubDivisao,inCodCargo,inCodEspecialidade);
    RETURN stFormulaEvento;
END;
$$ LANGUAGE 'plpgsql';

