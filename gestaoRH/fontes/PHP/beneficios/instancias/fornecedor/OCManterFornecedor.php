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
* Página de Oculto de Empresa
* Data de Criação   :  07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"               );

$obRCGMPessoaJuridica = new RCGMPessoaJuridica;

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ManterFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

switch ($_POST["stCtrl"]) {
    case "buscaCGM":
        if ($_POST["inNumCGM"] != "") {
            $obRCGMPessoaJuridica->setNumCGM ( $_POST["inNumCGM"] );
            $stWhere = " numcgm = ".$obRCGMPessoaJuridica->getNumCGM();
            $null = "&nbsp;";
            $obRCGMPessoaJuridica->consultarCGM($rsCgm, $stWhere);
            $inNumLinhas = $rsCgm->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $js .= 'f.inNumCGM.value = "";';
                $js .= 'f.inNumCGM.focus();';
                $js .= 'd.getElementById("campoInner").innerHTML = "'.$null.'";';
                $js .= "sistemaLegado::alertaAviso('@Valor inválido. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
                $js .= 'd.getElementById("campoInner").innerHTML = "'.$stNomCgm.'";';
            }
            sistemaLegado::executaFrameOculto($js);
        }
    break;

}
?>
