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
    * Página de Filtro do Relatório de IRRF
    * Data de Criação : 07/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30840 $
    $Name$
    $Autor: $
    $Date: 2008-01-07 12:05:54 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-04.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                          );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                       );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                           );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploBanco.class.php"                                );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploAgencia.class.php"                              );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioIRRF";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

###########################LIMPA SPANS#####################################

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    return $stJs;
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanAtivosAposentados()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setTodos();

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'todos':
            $obFormulario->addTitulo("Todos");
            break;
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $stEval = $obFormulario->getInnerJavaScript();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnTipoFiltroExtra.value = '$stEval';\n";

    return $stJs;

}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setCGMMatriculaPensionista();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setGrupoLotacao();

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    //$obIFiltroComponentes->getOnload($stJs);
    return $stJs;
}

###########################UTILS##########################

function limparFormulario()
{
    ;

    $stJs  = "d.getElementById('spnTipoFolha').innerHTML = '';											\n";
    $stJs .= "d.frm.inCodConfiguracao.value = 1;														\n";
    $stJs .= "d.frm.stConfiguracao.value = 1;															\n";
    $stJs .= "d.frm.stOrdenacao.value = 'A';															\n";
    $stJs .= "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case "gerarSpanAtivosAposentados":
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case "gerarSpanPensionistas":
        $stJs .= gerarSpanPensionistas();
        break;
    case "limparSpans":
        $stJs .= limparSpans();
        break;
    case "limparFormulario":
        $stJs = limparFormulario();
        break;
}

if ($stJs) {
    echo $stJs;
}
?>
