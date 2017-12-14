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
    * Oculto
    * Data de Criação: 07/11/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    $Revision: 19027 $
    $Name$
    $Author: cassiano $
    $Date: 2007-01-02 10:28:50 -0200 (Ter, 02 Jan 2007) $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
// Includes de classes utilizadas pelo componente
require_once(CAM_FW_COMPONENTES."Table/Table.class.php");
require_once(CAM_FW_COMPONENTES."Table/TableTree.class.php");
// Includes de classes utilizadas em tabelas
include_once( CAM_GP_PAT_COMPONENTES."ISelectSituacaoBem.class.php" );
include_once( CAM_GA_ORGAN_COMPONENTES."IMontaOrganogramaLocal.class.php" );
include_once( CAM_GA_ORGAN_COMPONENTES."ISelectOrganogramaOrgao.class.php" );

$inLine     = $_GET['inLine'];
$stTableId  = $_GET['table_id'];

switch ($_GET['stCtrl']) {
    case 'montaPaging':
        //wlog($_GET);
        $table = Sessao::read('TableSession');
        $table->Paging->setPaginaAtual( $inLine );
        $table->montaHTML(true);
        $stHtml = $table->getHtml();
        echo "document.getElementById('".$stTableId."').innerHTML = '$stHtml';\n";
        Sessao::write('TableSession',$table);
    break;
}
?>
