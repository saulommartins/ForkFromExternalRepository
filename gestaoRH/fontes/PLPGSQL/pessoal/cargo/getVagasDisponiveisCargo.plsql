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
 * PL para retorno de número de vagas disponíveis para o cargo
 * Data de Criação   : 05/11/2008


 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza
 
 * @package URBEM
 * @subpackage 

 * @ignore # só use se FOR paginas que o cliente visualiza, se FOR mapeamento ou classe de negocio não se usa

 $Id:$
 */ 
CREATE OR REPLACE FUNCTION getVagasDisponiveisCargo(INTEGER, INTEGER, INTEGER, INTEGER, BOOLEAN, VARCHAR) RETURNS INTEGER AS $$
DECLARE
    inCodRegime                 ALIAS FOR $1;
    inCodSubDivisao             ALIAS FOR $2;
    inCodCargo                  ALIAS FOR $3;
    inCodPeriodoMovimentacao    ALIAS FOR $4;
    boLiberaVagaMesRescisao     ALIAS FOR $5;
    stEntidade                  ALIAS FOR $6;
    inContador                  INTEGER;
BEGIN
    inContador := getVagasCadastradasCargo(inCodRegime, inCodSubDivisao, inCodCargo, inCodPeriodoMovimentacao, stEntidade) - getVagasOcupadasCargo(inCodRegime, inCodSubDivisao, inCodCargo, inCodPeriodoMovimentacao, boLiberaVagaMesRescisao, stEntidade);
    RETURN inContador;
END;
$$ LANGUAGE 'plpgsql';
