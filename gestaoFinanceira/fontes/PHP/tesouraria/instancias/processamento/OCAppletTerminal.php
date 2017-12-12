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
    * Paginae Oculta para funcionalidade Manter Pagamento
    * Data de Criação   : 26/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.18,uc-02.04.04,uc-02.04.05,uc-02.04.08,uc-02.04.17

*/

/*
$Log$
Revision 1.12  2006/07/05 20:39:33  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AppletTerminal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {

    case 'validaTerminal':
        $obRTesourariaBoletim = new RTesourariaBoletim();
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodVerificador( $_REQUEST['stHashMac'] );
        $obErro = $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->listarUsuariosAtivosTerminalAtivo( $rsUsuarioTerminal );
        if ($rsUsuarioTerminal->getNumLinhas() > -1 || Sessao::read('numCgm') == 0) {
            $inCodTerminal       = ($rsUsuarioTerminal->getCampo('cod_terminal')) ? $rsUsuarioTerminal->getCampo('cod_terminal') : 0;
            Sessao::write('inCodTerminal', $inCodTerminal );
            $stTimestampTerminal = ( $rsUsuarioTerminal->getCampo( 'timestamp_terminal' ) ) ? $rsUsuarioTerminal->getCampo( 'timestamp_terminal' ) : ' ';
            $stTimestampUsuario  = ( $rsUsuarioTerminal->getCampo( 'timestamp_usuario'  ) ) ? $rsUsuarioTerminal->getCampo( 'timestamp_usuario'  ) : ' ';
            $stXML  = "<?xml version='1.0' standalone='yes'?>                              \n";
            $stXML .= "  <terminal>                                                        \n";
            $stXML .= "     <cod_terminal>".$inCodTerminal."</cod_terminal>                \n";
            $stXML .= "     <timestamp_terminal>".$stTimestampTerminal."</timestamp_terminal>  \n";
            $stXML .= "     <timestamp_usuario>".$stTimestampUsuario."</timestamp_usuario> \n";
            $stXML .= "  </terminal>                                                        \n";
        } else {
            $stXML  = "<?xml version='1.0' standalone='yes'?>                              \n";
            $stXML .= "<terminal><cod_terminal>invalid</cod_terminal></terminal>";
        }
        header('Content-Type: text/html');
        echo $stXML;
    break;

}

?>
