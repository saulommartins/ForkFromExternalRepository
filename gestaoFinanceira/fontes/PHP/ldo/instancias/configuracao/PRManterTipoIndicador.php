<?php
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
?>
<?php
/**
 * Página de formulário de Cadastro de Tipo de Indicador
 * Data de Criação: 09/01/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Analista     : Tonismar Regis Bernardo     <tonismar.bernardo@cnm.org.br>
 * @author Desenvolvedor: Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @package gestaoFinanceira
 * @subpackage ldo
 * @uc uc-02.10.02
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_LDO_VISAO.'VLDOManterTipoIndicador.class.php';

if ($_REQUEST['stAcao'] != 'excluir') {
    list($_REQUEST['inCodUnidade'], $_REQUEST['inCodGrandeza']) = explode('-', $_REQUEST['inCodUnidadeMedida']);
}
VLDOManterTipoIndicador::recuperarInstancia()->executarAcao($_REQUEST);
