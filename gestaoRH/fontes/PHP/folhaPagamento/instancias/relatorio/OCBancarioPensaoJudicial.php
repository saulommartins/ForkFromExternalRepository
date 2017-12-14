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
    * Página de Filtro do Relatório Bancário de Pensão Judicial
    * Data de Criação : 21/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30840 $
    $Name$
    $Autor: $
    $Date: 2007-09-26 18:57:40 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.05.57
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "CadastroPensaoJudicial";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function limparForm()
{
    Sessao::remove("arContratos");
    $stJs = "ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroComponentes.php?".Sessao::getId()."&stTipoFiltro=contrato','gerarSpan' ); \n";
    $stJs .= "d.frm.stTipoFiltro.value = 'contrato'; 															\n";
    $stJs .= "passaItem('document.frm.inCodBancoSelecionados','document.frm.inCodBancoDisponiveis','tudo'); 	\n";
    $stJs .= "limpaSelect(document.frm.inCodAgenciaSelecionados, 0); 											\n";
    $stJs .= "limpaSelect(document.frm.inCodAgenciaDisponiveis, 0); 											\n";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "limparForm":
        $stJs = limparForm();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
