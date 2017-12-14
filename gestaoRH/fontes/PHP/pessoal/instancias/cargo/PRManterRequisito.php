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
    * Página de Processamento para Incluir/Alterar Requisitos
    * Data de Criação   :

    * @author Davi Ritter Aroldi

    * @ignore

    * Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRequisito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRequisito";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

$obErro = new Erro;
$obTPessoalRequisito = new TPessoalRequisito;

switch ($stAcao) {
    case 'incluir':
        $obTPessoalRequisito->proximoCod( $inCodRequisito );
        $obTPessoalRequisito->setDado( 'cod_requisito', $inCodRequisito );
        $obTPessoalRequisito->setDado( 'descricao', trim($_REQUEST['stDescricao']) );

        $obErro = $obTPessoalRequisito->inclusao();
        break;
}

if ($obErro->ocorreu()) {
    SistemaLegado::alertaAviso(CAM_GRH_PES_INSTANCIAS."cargo/FMManterRequisito.php", $obErro->getDescricao());
} else {
    $obTPessoalRequisito->recuperaTodos($rsRequisito);

    $stJs = '';
    // foreach ($rsRequisito->arElementos as $requisto) {
    // $stJs .= "jQuery('#inCodRequisitosDisponiveis').append('<option value=\'".$inCodRequisito."\'>' + '".trim($_REQUEST['stDescricao'])."' + '</option>'); \n";
    // $stJs .= "alert(window.parent.window.opener.document.getElementById('inCodRequisitosDisponiveis')); \n";
        $stJs .= "var lengthSelect = window.parent.window.opener.document.getElementById('inCodRequisitosDisponiveis').length; \n";
        $stJs .= "window.parent.window.opener.document.getElementById('inCodRequisitosDisponiveis').options[lengthSelect] = new Option('".addslashes(trim($_REQUEST['stDescricao']))."', ".$inCodRequisito."); \n";
    // }

    $stJs .= "window.parent.window.close(); \n";
    SistemaLegado::executaFrameOculto($stJs);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
