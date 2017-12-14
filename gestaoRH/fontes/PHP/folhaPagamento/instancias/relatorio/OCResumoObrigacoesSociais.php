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
    * Oculto
    * Data de Criação: 10/08/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: alex $
    $Date: 2007-12-17 10:07:04 -0200 (Seg, 17 Dez 2007) $

    * Casos de uso: uc-04.05.64
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ResumoObrigacoesSociais";
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
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setRegimeSubDivisao();
    $obIFiltroComponentes->setAtributoServidor();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoRegimeSubDivisao();
    $obIFiltroComponentes->setGrupoAtributoServidor();

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'A':
                $obFormulario->addTitulo("Ativos");
            break;
        case 'P'://aposentados
                $obFormulario->addTitulo("Aposentados");
            break;
        case 'R':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

    return $stJs;

}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoPensionista();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoAtributoPensionista();

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

    return $stJs;
}

###########################UTILS##########################

function submeter()
{
    $obErro = new Erro();

    if ($_GET["stSituacao"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cadastro inválido!()");
    }

    if (($_GET["stSituacao"] !== "") && !$obErro->ocorreu() ) {
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro do Ativos/Aposentados/Rescindidos inválido!()");
                break;
            case "contrato":
            case "cgm_contrato":
            case "contrato_rescindido":
            case "cgm_contrato_rescindido":
                if ( count(Sessao::read("arContratos")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                }
                break;
            case "contrato_pensionista":
            case "cgm_contrato_pensionista":
                if ( count(Sessao::read("arPensionistas")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                }
                break;
            case "atributo_servidor_grupo":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Ativos/Aposentados inválido!()");
                }
                break;
            case "atributo_pensionista_grupo":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Pensionista inválido!()");
                }
                break;
        }
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function limparForm()
{
    $stJs .= "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

    return $stJs;
}

#################################################

switch ($_REQUEST['stCtrl']) {
    case "gerarSpanAtivosAposentados":
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case "gerarSpanPensionistas":
        $stJs .= gerarSpanPensionistas();
        break;
    case "limparSpans":
        $stJs .= limparSpans();
        break;
    case "limparForm":
        $stJs .= limparForm();
        break;
    case "submeter":
        $stJs .= submeter();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>
