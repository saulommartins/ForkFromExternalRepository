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
  * Pagina Oculta para Carteira
  * Data de criacao : 02/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Diego Bueno Coelho

    * $Id: OCManterCarteira.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.05
**/

/*
$Log$
Revision 1.4  2006/09/15 14:57:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCarteira";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FM".$stPrograma.".php";
$pgFormCredito   = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

/*
        FIM DAS FUNÇÕES
*/

switch ($_REQUEST ["stCtrl"]) {

    case "buscaConvenio":

    if ($_REQUEST['inNumConvenio']) {

        include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php"           );
        $obRMONConvenio = new RMONConvenio ;

        $obRMONConvenio->setNumeroConvenio ( $_REQUEST['inNumConvenio'] );
        $obRMONConvenio->listarConvenio( $rsConvenios );

        if ( $rsConvenios->getNumLinhas() < 1 ) {

            $stJs .= "f.inNumConvenio.value ='';\n";
            $stJs .= "f.inNumConvenio.focus();\n";
            $stJs .= "alertaAviso('@Convênio informado não existe. (".$_REQUEST["inNumConvenio"].")','form','erro','".Sessao::getId()."');";

        } else {

           $tmpCod = $rsConvenios->getCampo('cod_convenio');
           $stJs = "f.inCodConvenio.value='".$tmpCod."'; \n";

        }
    }
    break;

}
SistemaLegado::executaFrameOculto($stJs);
?>
