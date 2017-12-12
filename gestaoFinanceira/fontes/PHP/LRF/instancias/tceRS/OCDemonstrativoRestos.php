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
    * Classe Oculta para o Modelo 7
    * Data de Criação   : 03/08/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso uc-02.05.09

*/

/*
$Log$
Revision 1.5  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../bibliotecas/mascaras.lib.php';
include_once( CAM_GF_LRF_NEGOCIO."RRelatorio.class.php"        );
include_once( CAM_GF_LRF_NEGOCIO."RLRFModelo7.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "DemonstrativoRestos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "CO".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRLRFModelo7 = new RLRFModelo7();
$obRRelatorio  = new RRelatorio();

switch ($stCtrl) {
    default:
        if ( is_array( $sessao->filtro['inCodEntidade'] ) ) {
            foreach ($sessao->filtro['inCodEntidade'] as $inCodEntidade) {
                $stEntidade .= $inCodEntidade.',';
            }
            $stEntidade = substr( $stEntidade, 0, strlen( $stEntidade )-1 );
        }
        $obRLRFModelo7->setExercicio  ( Sessao::getExercicio()       );
        $obRLRFModelo7->setCodEntidade( $stEntidade              );
        $obRLRFModelo7->setMes        ( $sessao->filtro['inMes'] );

        $obErro = $obRLRFModelo7->geraRecordSet( $rsRecordSet );
        $sessao->transf5 = $rsRecordSet;

        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioModelo7.php");
    break;
}
?>
