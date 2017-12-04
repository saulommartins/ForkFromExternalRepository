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
    * Página Oculta de Cargo
    * Data de Criação   : 05/01/2006

    * @author Analista: Vandre Miguel Ramos
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Caso de uso: uc-04.05.27

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

function verificaSequencia()
{
    if ($_POST['inSequencia']) {
        include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoSequencia.class.php"  );
        $obSequencia = new RFolhaPagamentoSequencia;
        $obErro = new Erro;
        $obSequencia->setSequencia( $_POST['inSequencia'] );
        $obErro = $obSequencia->verificaSequencia($boTransacao );
        if ( $obErro->ocorreu() ) {
            sistemaLegado::exibeAviso($obErro->getDescricao() ," " ," " );
        }
    }
}

switch ($stCtrl) {
    case "verificaSequencia":
        $stJs = verificaSequencia();
    break;
}

if($stJs)
   sistemaLegado::executaFrameOculto($stJs);
?>
