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
    * Página de Oculto do Exportação Remessa Banrisul
    * Data de Criação: 10/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.08.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroPensionista.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMPensionista.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "CreditoBanrisul";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

###########################LIMPA SPANS#####################################

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    #Ativos / Aposentados / Pensionistas
    $stJs .= gerarSpanComplementar(false);

    return $stJs;
}

###########################LIMPA SPANS#####################################

function gerarSpan()
{
    switch ($_GET["stSituacao"]) {
        case "pensionistas"://pensionistas

            return gerarSpanPensionistas();
            break;
        default:
            return gerarSpanGeral();
            break;
    }
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanGeral()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();

    if ($_GET["stSituacao"] != 'todos') {
       $obIFiltroComponentes->setMatricula();
       $obIFiltroComponentes->setCGMMatricula();
       $obIFiltroComponentes->setLocal();
       $obIFiltroComponentes->setLotacao();
       $obIFiltroComponentes->setAtributoServidor();
    }

    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'todos':
                $obFormulario->addTitulo("Todos");
                $obIFiltroComponentes->setTodos();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanComplementar(true);

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
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoPensionista();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanComplementar(true, true);

    return $stJs;
}

###########################ATIVOS E APOSENTADOS E PENSIONISTAS #####################################

function gerarSpanComplementar($boGerar = true)
{
    if ($boGerar) {
        include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
        $obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

        $obFormulario = new Formulario();
        $obIFiltroCompetencia->geraFormulario($obFormulario);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAtivosAposentadosPensionistas').innerHTML = '$stHtml';\n";

    return $stJs;
}

###########################UTILS##########################

function submeter()
{
    $obErro = new Erro();

    if ($_GET["stSituacao"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cadastro inválido!()");
    }

    if ($_GET['stSituacao'] == 'ativos' or
        $_GET['stSituacao'] == 'aposentados' or
        $_GET['stSituacao'] == 'pensionistas' or
        $_GET['stSituacao'] == 'todos') {
        if ( empty($_GET["inCodMes"]) ) {
              $obErro->setDescricao($obErro->getDescricao()."@Campo Mês da Competência inválido!()");
        } else {
            if ( empty($_GET["inAno"]) ) {
                $obErro->setDescricao($obErro->getDescricao()."@Campo Ano da Competência inválido!()");
            }
        }
    }

    if ($_GET["stSituacao"] == 'ativos' or
        $_GET["stSituacao"] == 'aposentados' or
        $_GET["stSituacao"] == 'pensionistas' or
        $_GET["stSituacao"] == 'todos') {
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro inválido!()");
                break;
            case "contrato":
            case "contrato_rescisao":
            case "cgm_contrato":
            case "cgm_contrato_rescisao":
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
            case "atributo_servidor":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Ativos/Aposentados inválido!()");
                }
                break;
            case "atributo_pensionista":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Pensionista inválido!()");
                }
                break;
        }
    }

    $rsConfiguracaoRemuneracao = Sessao::read("rsConfiguracaoRemuneracao");
    if ($rsConfiguracaoRemuneracao->getNumLinhas() == -1) {
        $obErro->setDescricao($obErro->getDescricao()."@Não existem eventos de remuneração configurados!()");
    }

    $rsConfiguracaoLiquido = Sessao::read("rsConfiguracaoLiquido");
    if ($rsConfiguracaoLiquido->getNumLinhas() == -1) {
        $obErro->setDescricao($obErro->getDescricao()."@Não existem eventos de líquido configurados!()");
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "parent.frames[2].Salvar(); BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

function limparForm()
{
    $stJs .= "montaParametrosGET('gerarSpan','stSituacao');";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpan":
        $stJs .= gerarSpan();
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
