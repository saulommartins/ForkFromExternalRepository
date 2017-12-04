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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25841 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-05 10:02:21 -0300 (Sex, 05 Out 2007) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/10/05 12:59:35  hboaventura
inclusão dos arquivos

Revision 1.2  2007/09/27 12:57:24  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:04  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once( CAM_FW_HTML."MontaAtributos.class.php"       );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioEspecieAtributo.class.php");
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemAtributoEspecie.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterBem";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'montaPlacaIdentificacao':

        if ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
            $obTxtNumeroPlaca = new TextBox();
            $obTxtNumeroPlaca->setRotulo( 'Número da Placa' );
            $obTxtNumeroPlaca->setTitle( 'Informe o número da placa do bem.' );
            $obTxtNumeroPlaca->setName( 'stNumeroPlaca' );
            $obTxtNumeroPlaca->setNull( true );
            $obTxtNumeroPlaca->setValue( $_REQUEST['stNumPlaca'] );

            $obTipoBuscaNumeroPlaca = new TipoBusca( $obTxtNumeroPlaca );

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTipoBuscaNumeroPlaca );
            $obFormulario->montaInnerHTML();

            $stJs.= "$('spnNumeroPlaca').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $stJs.= "$('spnNumeroPlaca').innerHTML = '';";
        }
        break;
}

echo $stJs;
