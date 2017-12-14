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
    * Arquivo de Oculto
    * Data de Criação: 25/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: OCManterConfiguracaoRais.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.12
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

function preencherFormAlterar()
{
    include_once(CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php");
    $obTCGMCGM = new TCGMCGM();
    $obTCGMCGM->setDado("numcgm",Sessao::read("inCGM"));
    $obTCGMCGM->recuperaPorChave($rsCGM);

    $_GET["inTipoInscricao"] = Sessao::read("inTipoInscricao");
    $stJs .= "d.getElementById('stNomCGM').innerHTML = '".$rsCGM->getCampo("nom_cgm")."';\n";
    $stJs .= "f.inCGM.value = '".$rsCGM->getCampo("numcgm")."';\n"  ;
    if (Sessao::read("boCNPJ") == "true") {
        $_GET["boCNPJ"] = Sessao::read("boCNPJ");
        $stJs .= gerarSpanCEI();
        $stJs .= "f.inCei.value = '".Sessao::read("inCei")."';\n";
        $stJs .= "f.inPrefixo.value = '".Sessao::read("inPrefixo")."';\n";
    }
    $stJs .= preencherComposicaoRemuneracaoRais();
    $stJs .= preencherEventosHorasExtras();

    return $stJs;
}

function preencherComposicaoRemuneracaoRais()
{
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAEventoComposicaoRemuneracao.class.php"                           );
    $obTIMAEventoComposicaoRemuneracao = new TIMAEventoComposicaoRemuneracao();
    $stFiltro = " AND exercicio = '".Sessao::read("inExercicio")."'";
    $obTIMAEventoComposicaoRemuneracao->recuperaRelacionamento($rsEventosComposicaoRemuneracao,$stFiltro," descricao");

    $stJs .= montaJavaScriptComboEventos($rsEventosComposicaoRemuneracao);

    return $stJs;
}

function preencherEventosHorasExtras()
{
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAEventoHorasExtras.class.php"                           );
    $obTIMAEventoHorasExtras = new TIMAEventoHorasExtras();
    $stFiltro = " AND exercicio = '".Sessao::read("inExercicio")."'";
    $obTIMAEventoHorasExtras->recuperaRelacionamento($rsEventosHorasExtras,$stFiltro," descricao");

    $stJs .= montaJavaScriptComboEventos($rsEventosHorasExtras,2);

    return $stJs;
}

function montaJavaScriptComboEventos($rsEventosGravados,$inCodCombo="")
{
    $stJs .= "limpaSelect(f.inCodEventoSelecionados".$inCodCombo.",0);\n";
    $stJs .= "limpaSelect(f.inCodEventoDisponiveis".$inCodCombo.",0);\n";

    $inIndex = 0;
    $stCodEventos = "";
    while (!$rsEventosGravados->eof()) {
        $stJs .= "f.inCodEventoSelecionados".$inCodCombo."[".$inIndex."] = new Option('".$rsEventosGravados->getCampo("codigo")."-".trim($rsEventosGravados->getCampo("descricao"))."','".$rsEventosGravados->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventosGravados->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventosGravados->proximo();
    }
    $stCodEventos = substr($stCodEventos,0,strlen($stCodEventos)-1);

    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro  = " WHERE (natureza = 'P' OR natureza = 'D' OR natureza = 'B')";
    if ($stCodEventos!="") {
        $stFiltro .= "   AND cod_evento NOT IN (".$stCodEventos.")";
    }
    $obTFolhaPagamentoEvento->recuperaTodos($rsEventos,$stFiltro," descricao");
    $inIndex = 0;
    while (!$rsEventos->eof()) {
        $stJs .= "f.inCodEventoDisponiveis".$inCodCombo."[".$inIndex."] = new Option('".$rsEventos->getCampo("codigo")."-".trim($rsEventos->getCampo("descricao"))."','".$rsEventos->getCampo("cod_evento")."','');\n";
        $stCodEventos .= $rsEventos->getCampo("cod_evento").",";
        $inIndex++;
        $rsEventos->proximo();
    }

    return $stJs;
}

function gerarSpanCEI()
{
    if ($_GET["boCNPJ"] == "true") {
        $obTxtCEI = new Inteiro();
        $obTxtCEI->setRotulo("Número CEI");
        $obTxtCEI->setName("inCei");
        $obTxtCEI->setTitle("Informe o número do CEI.");
        $obTxtCEI->setNull(false);
        $obTxtCEI->setSize(13);
        $obTxtCEI->setMaxLength(12);

        $obTxtPrefixo = new Inteiro();
        $obTxtPrefixo->setRotulo("Prefixo");
        $obTxtPrefixo->setName("inPrefixo");
        $obTxtPrefixo->setTitle("Informe o prefixo da entidade observando a seguinte instrução da RAIS: declarar os trabalhadores da empresa (matriz ou filial), iniciando a declaração  pela inscrição do CNPJ , prefixo 00, deixando o  campo CEI vinculado em branco. Declarar os trabalhadores da obra (canteiro) pelo CEI correspondente aquela obra  (utilizando o prefixo 01 para a primeira obra, 02 para a segunda obra e assim por diante)  e informar o  CNPJ da empresa para caracterizar a vinculação.");
        $obTxtPrefixo->setNull(false);
        $obTxtPrefixo->setSize(3);
        $obTxtPrefixo->setMaxLength(2);
        $obTxtPrefixo->setValue(01);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obTxtCEI);
        $obFormulario->addComponente($obTxtPrefixo);
        $obFormulario->montaInnerHTML();
        $obFormulario->obJavaScript->montaJavaScript();
        $stHtml = $obFormulario->getHTML();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    }

    $stJs  = "d.getElementById('spnCNPJ').innerHTML = '".$stHtml."'\n";
    $stJs .= "f.hdnCNPJ.value = '".$stEval."'\n;";

    return $stJs;
}

function limpar()
{
    $stJs .= "f.reset();\n";
    $stJs .= gerarSpanCGMResponsavel();

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherFormAlterar":
        $stJs = preencherFormAlterar();
        break;
    case "gerarSpanCEI":
        $stJs = gerarSpanCEI();
        break;
    case "limpar":
        $stJs = limpar();
        break;

}
if ($stJs) {
    echo $stJs;
}

?>
