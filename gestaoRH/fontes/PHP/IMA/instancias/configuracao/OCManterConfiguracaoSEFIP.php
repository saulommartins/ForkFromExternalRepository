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
    * Página de Oculto do IMA Configuração
    * Data de Criação: 20/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * Casos de uso: uc-04.08.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSEFIP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function gerarSpanInscricaoFornecedor()
{
    $obFormulario = new Formulario();
    switch ($_GET["inTipoInscricao"]) {
        case 1:
            $stNomCgm = "&nbsp;";
            if ( Sessao::read("inscricao_fornecedor") != "" or Sessao::read("inscricao_fornecedor") != 0 ) {
                include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
                $obTCGMPessoaJuridica =  new TCGMPessoaJuridica();

                $obTCGMPessoaJuridica->setDado("numcgm", Sessao::read("inscricao_fornecedor"));
                $obTCGMPessoaJuridica->recuperaPorChave($rsCgm);
                $stCgm = $rsCgm->getCampo("numcgm");

                include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
                $obTCGM =  new TCGM();
                $obTCGM->setDado("numcgm", Sessao::read("inscricao_fornecedor"));
                $obTCGM->recuperaPorChave($rsCgm);
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
            }
            include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
            $obForm = Sessao::read("obForm");
            $obIPopUpCGM = new IPopUpCGM($obForm);
            $obIPopUpCGM->setTipo('juridica');
            $obIPopUpCGM->obCampoCod->setValue($stCgm);
            $obIPopUpCGM->setRotulo("CGM Fornecedor da Folha");
            $obIPopUpCGM->setTitle("Informe o CGM do fornecedor da folha com tipo de inscrição igual a CNPJ.");
            $obFormulario->addComponente($obIPopUpCGM);

            $stNomeCgm = "d.getElementById('".$obIPopUpCGM->getId()."').innerHTML = '$stNomCgm';\n";
            break;
        case 2:
            $obTxtCEI = new TextBox();
            $obTxtCEI->setRotulo          ( "CEI" );
            $obTxtCEI->setName            ( "inCGM"	);
            $obTxtCEI->setTitle           ( "Informe o CEI do fornecedor." );
            $obTxtCEI->setSize            ( 14 );
            $obTxtCEI->setMaxLength       ( 14 );
            $obTxtCEI->setNull			 ( false );
            $obTxtCEI->setValue(Sessao::read("inscricao_fornecedor"));

            $obFormulario->addComponente($obTxtCEI);
            break;
        case 3:
            $stNomCgm = "&nbsp;";
            if ( Sessao::read("inscricao_fornecedor") != "" or Sessao::read("inscricao_fornecedor") != 0 ) {
                include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
                $obTCGMPessoaFisica =  new TCGMPessoaFisica();

                $obTCGMPessoaFisica->setDado("numcgm",Sessao::read("inscricao_fornecedor"));
                $obTCGMPessoaFisica->recuperaPorChave($rsCgm);
                $stCgm = $rsCgm->getCampo("numcgm");

                include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
                $obTCGM =  new TCGM();
                $obTCGM->setDado("numcgm",Sessao::read("inscricao_fornecedor"));
                $obTCGM->recuperaPorChave($rsCgm);
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
            }

            include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php");
            $obForm = Sessao::read("obForm");
            $obIPopUpCGM = new IPopUpCGM($obForm);
            $obIPopUpCGM->setTipo('fisica');
            $obIPopUpCGM->obCampoCod->setValue($stCgm);
            $obIPopUpCGM->setRotulo("CGM Fornecedor da Folha");
            $obIPopUpCGM->setTitle("Informe o CGM do fornecedor da folha com tipo de inscrição igual a CPF.");
            $obFormulario->addComponente($obIPopUpCGM);

            $stNomeCgm = "d.getElementById('".$obIPopUpCGM->getId()."').innerHTML = '$stNomCgm';\n";
            break;
    }
    $obFormulario->montaInnerHTML();

    $stJs  = "d.getElementById('spnTipoInscricao').innerHTML = '".$obFormulario->getHTML()."'\n";
    $stJs .= $stNomeCgm;

    return $stJs;
}

function preencherForm()
{
    $_GET["inTipoInscricao"] = Sessao::read("inTipoInscricao");
    $stJs = gerarSpanInscricaoFornecedor();
    $stJs .= gerarSpanModalidadeAlterar();

    return $stJs;
}

function submeter()
{
    //SistemaLegado::BloqueiaFrames(true,false);

    $obErro = new Erro();

    switch ($_REQUEST["inTipoInscricao"]) {
        case 1:
            if ($_REQUEST["inCGM"] == "") {
                $obErro->setDescricao("Campo CGM inválido!");
            } else {
                include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
                $obTCGMPessoaJuridica =  new TCGMPessoaJuridica();
                $obTCGMPessoaJuridica->setDado("numcgm",$_GET["inCGM"]);
                $obTCGMPessoaJuridica->recuperaPorChave($rsCgm);

                if ( $rsCgm->getCampo("cnpj") == "" ) {
                    $obErro->setDescricao("O CGM ".$_GET["inCGM"]." não possui um cnpj cadastrado!");
                }
            }
        break;

        case 2:
            if ($_REQUEST["inCGM"] == "") {
                $obErro->setDescricao("Campo CEI inválido!");
            }
        break;

        case 3:
            if ($_REQUEST["inCGM"] == "") {
                $obErro->setDescricao("Campo CGM inválido!");
            } else {
                include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
                $obTCGMPessoaFisica =  new TCGMPessoaFisica();
                $obTCGMPessoaFisica->setDado("numcgm",$_GET["inCGM"]);
                $obTCGMPessoaFisica->recuperaPorChave($rsCgm);

                if ( $rsCgm->getCampo("cpf") == "" ) {
                    $obErro->setDescricao("O CGM ".$_GET["inCGM"]." não possui um cpf cadastrado!");
                }
            }
        break;
    }

    if ( !$obErro->ocorreu() ) {
        $stJs .= "BloqueiaFrames(true,false);\nparent.frames[2].Salvar();\n";
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    return $stJs;
}

function limpar()
{
    $stJs .= "d.getElementById('stCnae').innerHTML = '&nbsp;';\n";
    $stJs .= "f.inCodCnae.value = '';\n";
    $stJs .= "f.inCodCentralizacao.value = '';\n";
    $stJs .= "f.inCodFPAS.value = '';\n";
    $stJs .= "f.inCodPagamentoGPS.value = '';\n";
    $stJs .= "f.inTipoInscricao.value = '1';\n";
    $_GET["inTipoInscricao"] = 1;
    $stJs .= gerarSpanInscricaoFornecedor();

    return $stJs;
}

function buscaCnae()
{
    $rsCnae = new RecordSet();
    if ($_GET["inCodCnae"]) {
        include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php"         );
        $obTCEMCnaeFiscal = new TCEMCnaeFiscal();
        $stFiltro = " WHERE cod_estrutural = '".$_GET["inCodCnae"]."'";
        $obTCEMCnaeFiscal->recuperaTodos($rsCnae,$stFiltro);
    }
    if ( $rsCnae->getNumLinhas() == -1 ) {
        $stJs .= 'f.inCodCnae.value = "";';
        $stJs .= 'f.inCodCnae.focus();';
        $stJs .= "f.HdninCodCnae.value = '';";
        $stJs .= 'd.getElementById("stCnae").innerHTML = "&nbsp;";';
        $stJs .= "alertaAviso('campo Código CNAE Fiscal inválido!(".$_GET['inCodCnae'].").','form','erro','".Sessao::getId()."');\n";
    } else {
        $stCnae        = $rsCnae->getCampo ("nom_atividade");
        $inCodigoCnae  = $rsCnae->getCampo ("cod_cnae");
        $stJs .= "f.HdninCodCnae.value = '$inCodigoCnae';";
        $stJs .= "d.getElementById('stCnae').innerHTML = '$stCnae'";
    }

    return $stJs;
}

function incluirModalidade()
{
    $obErro = new Erro;
    if (count(Sessao::read("arModalidades")) > 0) {
        $arCategorias = array();
    } else {
        $arCategorias = $_GET["inCodCategoriaSelecionados"];
    }
    $stMensagemErro = "";
    foreach (Sessao::read("arModalidades") as $arModalidade) {
        if ($arModalidade["inSefip"] == $_GET["inCodModalidadeRecolhimento"]) {
            $obErro->setDescricao("O Tipo de Modalidade selecionado já encontra-se na lista.");
            break;
        }
        foreach ($_GET["inCodCategoriaSelecionados"] as $inCodCategoria) {
            if (in_array($inCodCategoria,$arModalidade["categorias"])) {
                $stMensagemErro = "@Uma ou mais categoria selecionadas já foram inseridas para outra modalidade.";
            } else {
                $arCategorias[] = $inCodCategoria;
            }
        }
    }
    if ($stMensagemErro != "") {
        //$obErro->setDescricao($obErro->getDescricao().$stMensagemErro);
    }
    if (count($arCategorias) === 0) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Categoria da Sefip inválido ou as todas as Categorias selecionadas já foram inseridas em outra modalidade!()");
    }
    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAModalidadeRecolhimento.class.php");
        $obTIMAModalidadeRecolhimento = new TIMAModalidadeRecolhimento();
        $stFiltro = " WHERE sefip = '".trim($_GET["inCodModalidadeRecolhimento"])."'";
        $obTIMAModalidadeRecolhimento->recuperaTodos($rsModalidadeRecolhimento,$stFiltro);

        $arModalidades = Sessao::read('arModalidades');

        $arModalidade["inSefip"] = $_GET["inCodModalidadeRecolhimento"];
        $arModalidade["inCodModalidadeRecolhimento"] = $rsModalidadeRecolhimento->getCampo("cod_modalidade");
        $arModalidade["stModalidadeRecolhimento"] = $rsModalidadeRecolhimento->getCampo("descricao");
        $arModalidade["categorias"] = $arCategorias;

        $arModalidades[] = $arModalidade;
        Sessao::write('arModalidades', $arModalidades);

        $stJs = gerarSpanModalidade();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarModalidade()
{
    $obErro = new Erro;
    if (count(Sessao::read("arModalidades")) > 0) {
        if (count(Sessao::read("arModalidades")) == 1) {
            $arCategorias = $_GET["inCodCategoriaSelecionados"];
        } else {
            $arCategorias = array();
        }
    } else {
        $arCategorias = $_GET["inCodCategoriaSelecionados"];
    }
    foreach (Sessao::read("arModalidades") as $inIndex=>$arModalidade) {
        if ($arModalidade["inSefip"] === $_GET["inCodModalidadeRecolhimento"]) {
            $inIndexAlterar = $inIndex;
        }
        if ($arModalidade["inSefip"] !== $_GET["inCodModalidadeRecolhimento"]) {
            foreach ($_GET["inCodCategoriaSelecionados"] as $inCodCategoria) {
                if (!in_array($inCodCategoria,$arModalidade["categorias"])) {
                    $arCategorias[] = $inCodCategoria;
                }
            }
        }
    }
    if (count($arCategorias) === 0) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Categoria da Sefip inválido ou as todas as Categorias selecionadas já foram inseridas em outra modalidade!()");
    }
    if (!$obErro->ocorreu()) {
        include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAModalidadeRecolhimento.class.php");
        $obTIMAModalidadeRecolhimento = new TIMAModalidadeRecolhimento();
        $stFiltro = " WHERE sefip = '".trim($_GET["inCodModalidadeRecolhimento"])."'";
        $obTIMAModalidadeRecolhimento->recuperaTodos($rsModalidadeRecolhimento,$stFiltro);

        $arModalidades = Sessao::read('arModalidades');

        $arModalidade["inSefip"] = $_GET["inCodModalidadeRecolhimento"];
        $arModalidade["inCodModalidadeRecolhimento"] = $rsModalidadeRecolhimento->getCampo("cod_modalidade");
        $arModalidade["stModalidadeRecolhimento"] = $rsModalidadeRecolhimento->getCampo("descricao");
        $arModalidade["categorias"] = $arCategorias;

        $arModalidades[$inIndexAlterar] = $arModalidade;
        Sessao::write('arModalidades', $arModalidades);

        $stJs = gerarSpanModalidade();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }
    $stJs .= "f.btLimparModalidade.disabled = false;\n";
    $stJs .= "f.inCodModalidadeRecolhimento.disabled = false;\n";
    $stJs .= "f.inCodModalidadeRecolhimentoTxt.readOnly = false;\n";

    return $stJs;
}

function excluirModalidade()
{
    $arTemp = array();
    foreach (Sessao::read("arModalidades") as $inIndex=>$arModalidade) {
        if ($arModalidade["inSefip"] !== $_GET["inSefip"]) {
            $arTemp[] = $arModalidade;
        }
    }
    Sessao::write('arModalidades', $arTemp);
    $stJs = gerarSpanModalidade();

    return $stJs;
}

function montaAlterarModalidade()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCategoria.class.php");
    $obTPessoalCategoria = new TPessoalCategoria();

    foreach (Sessao::read("arModalidades") as $arModalidade) {
        if ($arModalidade["inSefip"] === $_GET["inSefip"]) {
            $stJs  = "f.inCodModalidadeRecolhimento.value = '".$arModalidade["inSefip"]."';\n";
            $stJs .= "f.inCodModalidadeRecolhimentoTxt.value = '".$arModalidade["inSefip"]."';\n";
            $stJs .= "limpaSelect(f.inCodCategoriaSelecionados,0);\n";
            foreach ($arModalidade["categorias"] as $inIndex=>$inCodCategoria) {
                $obTPessoalCategoria->setDado("cod_categoria",$inCodCategoria);
                $obTPessoalCategoria->recuperaPorChave($rsCategoria);
                $stJs .= "f.inCodCategoriaSelecionados[".$inIndex."] = new Option('".$rsCategoria->getCampo("cod_categoria")."-".$rsCategoria->getCampo("descricao")."','".$inCodCategoria."','');\n";
                $stJs .= "inIndex = 0;\n";
                $stJs .= "while (f.inCodCategoriaDisponiveis.options[inIndex]) {\n";
                $stJs .= "    if (f.inCodCategoriaDisponiveis.options[inIndex].value == '".$inCodCategoria."') {\n";
                $stJs .= "        f.inCodCategoriaDisponiveis.options[inIndex] = null;\n";
                $stJs .= "    }\n";
                $stJs .= "    inIndex++;\n";
                $stJs .= "}\n";
            }
        }
    }
    $stJs .= "f.btAlterarModalidade.disabled = false;\n";
    $stJs .= "f.btIncluirModalidade.disabled = true;\n";
    $stJs .= "f.btLimparModalidade.disabled = true;\n";
    $stJs .= "f.inCodModalidadeRecolhimento.disabled = true;\n";
    $stJs .= "f.inCodModalidadeRecolhimentoTxt.readOnly = true;\n";

    return $stJs;
}

function gerarSpanModalidadeAlterar()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCategoria.class.php");
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACategoriaSefip.class.php");
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAModalidadeRecolhimento.class.php");
    $obTIMAModalidadeRecolhimento = new TIMAModalidadeRecolhimento();
    $obTIMAModalidadeRecolhimento->recuperaTodos($rsModalidadeRecolhimento);

    $obTPessoalCategoria = new TPessoalCategoria();
    $obTPessoalCategoria->recuperaTodos($rsCategoria);
    $obTIMACategoriaSefip = new TIMACategoriaSefip();
    $arModalidades = array();
    while (!$rsModalidadeRecolhimento->eof()) {
        $arCategorias = array();
        $stFiltro = " WHERE cod_modalidade = ".$rsModalidadeRecolhimento->getCampo("cod_modalidade");
        $obTIMACategoriaSefip->recuperaTodos($rsCategoriaSefip,$stFiltro);
        while (!$rsCategoriaSefip->eof()) {
            $arCategorias[] = $rsCategoriaSefip->getCampo("cod_categoria");
            $rsCategoriaSefip->proximo();
        }
        if (count($arCategorias) > 0) {
            $arModalidades = Sessao::read('arModalidades');

            $arModalidade["inSefip"] = $rsModalidadeRecolhimento->getCampo("sefip");
            $arModalidade["inCodModalidadeRecolhimento"] = $rsModalidadeRecolhimento->getCampo("cod_modalidade");
            $arModalidade["stModalidadeRecolhimento"] = $rsModalidadeRecolhimento->getCampo("descricao");
            $arModalidade["categorias"] = $arCategorias;

            $arModalidades[] = $arModalidade;
            Sessao::write("arModalidades", $arModalidades);
        }
        $rsModalidadeRecolhimento->proximo();
    }
    $stJs = gerarSpanModalidade();

    return $stJs;
}

function gerarSpanModalidade()
{
    $rsModalidades = new recordset();
    $rsModalidades->preenche(Sessao::read("arModalidades"));

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Configurações de Modalidade");
    $obLista->setRecordSet( $rsModalidades );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Modalidade");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "inSefip" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "stModalidadeRecolhimento" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('montaAlterarModalidade');");
    $obLista->ultimaAcao->addCampo("1","inSefip");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirModalidade');");
    $obLista->ultimaAcao->addCampo("1","inSefip");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnModalidadeRecolhimento').innerHTML = '".$stHtml."';   \n";

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "gerarSpanInscricaoFornecedor":
        Sessao::write("inscricao_fornecedor", "");
        $stJs .= gerarSpanInscricaoFornecedor();
        break;
    case "preencherForm":
        $stJs = preencherForm();
        break;
    case "submeter":
        $stJs = submeter();
        break;
    case "limpar":
        $stJs = limpar();
        break;
    case "buscaCnae":
        $stJs = buscaCnae();
        break;
    case "incluirModalidade";
        $stJs = incluirModalidade();
        break;
    case "alterarModalidade";
        $stJs = alterarModalidade();
        break;
    case "excluirModalidade";
        $stJs = excluirModalidade();
        break;
    case "montaAlterarModalidade":
        $stJs = montaAlterarModalidade();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>
