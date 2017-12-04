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
* Página de Formulário Oculto Documento
* Data de Criação   : 12/04/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Vandré Miguel Ramos

* @package URBEM
* @subpackage

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.01.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_CON_NEGOCIO."RConcursoCandidato.class.php" 		);
include_once( CAM_GRH_CON_NEGOCIO."RConfiguracaoConcurso.class.php" 	);
include_once( CAM_FW_PDF."RRelatorio.class.php"   );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"          	);

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRCandidato = new RConcursoCandidato;

// Acoes por pagina
switch ($stCtrl) {
    case "buscaCGM":
        if ($_POST["inNumCGM"] != "") {
            $obRCGM = new RCGMPessoaFisica;
            $obRCGM->setNumCGM( $_POST["inNumCGM"] );
            $obRCGM->consultarCGM( $rsCGM );
            $null = "&nbsp;";

            if ( $rsCGM->getNumLinhas() <= 0) {
                $js .= 'f.inNumCGM.value = "";';
                $js .= 'f.inNumCGM.focus();';
                $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$rsCGM->getCampo("nom_cgm").'";';
            }
        } else {
            $js .= 'd.getElementById("nom_cgm").innerHTML = "&nbsp;";';
        }
    break;
}
if ($js) {
   SistemaLegado::executaFrameOculto($js);
   $js = '';
} else {
  $obRRelatorio  = new RRelatorio;
  $obRRelatorio->executaFrameOculto( "OCGeraEmitirDocumento.php" );
}

?>
