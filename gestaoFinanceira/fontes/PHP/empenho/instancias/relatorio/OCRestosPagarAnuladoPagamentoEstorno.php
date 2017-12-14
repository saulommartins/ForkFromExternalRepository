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
    * Página do Oculto do Relatório Restos a Pagar Anulado, Pagamentos ou Estorno
    * Data de Criação   : 08/09/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage empenho
    * @ignore relatorio

    * $Id:$

    * Casos de uso : uc-02.03.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obROrcamentoEntidade->listarUsuariosEntidade($rsTotalEntidades, " ORDER BY cod_entidade");
unset($obROrcamentoEntidade);

$stJs = "";
switch ($_REQUEST['stCtrl']) {

case "MontaOrgao":
    $stHTML = "";
    if ($_REQUEST["inExercicio"]) {
        $obTxtOrgao = new TextBox;
        $obTxtOrgao->setRotulo   ("Órgão");
        $obTxtOrgao->setTitle    ("Informe o órgão para filtro");
        $obTxtOrgao->setName     ("inCodOrgaoTxt");
        $obTxtOrgao->setId       ("inCodOrgaoTxt");
        $obTxtOrgao->setValue    ("");
        $obTxtOrgao->setSize     (6);
        $obTxtOrgao->setMaxLength(3);
        $obTxtOrgao->setInteiro  (true);

        $obTxtUnidade = new TextBox;
        $obTxtUnidade->setRotulo   ("Unidade");
        $obTxtUnidade->setTitle    ("Informe a unidade para filtro");
        $obTxtUnidade->setName     ("inCodUnidadeTxt");
        $obTxtUnidade->setId       ("inCodUnidadeTxt");
        $obTxtUnidade->setValue    ("");
        $obTxtUnidade->setSize     (6);
        $obTxtUnidade->setMaxLength(3);
        $obTxtUnidade->setInteiro  (true);

        if ($_REQUEST["inExercicio"] > '2004') {

            $obTxtOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');");

            include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
            $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio($_REQUEST["inExercicio"]);
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->listar($rsCombo);

            $obCmbOrgao = new Select;
            $obCmbOrgao->setRotulo            ("Órgão");
            $obCmbOrgao->setName              ("inCodOrgao");
            $obCmbOrgao->setId                ("inCodOrgao");
            $obCmbOrgao->setValue             ("");
            $obCmbOrgao->setStyle             ("width: 200px");
            $obCmbOrgao->setCampoID           ("num_orgao");
            $obCmbOrgao->setCampoDesc         ("nom_orgao");
            $obCmbOrgao->addOption            ('', 'Selecione');
            $obCmbOrgao->preencheCombo        ($rsCombo);
            $obCmbOrgao->obEvento->setOnChange("montaParametrosGET('MontaUnidade');" );

            $obCmbUnidade= new Select;
            $obCmbUnidade->setRotulo   ("Unidade");
            $obCmbUnidade->setName     ("inCodUnidade");
            $obCmbUnidade->setId       ("inCodUnidade");
            $obCmbUnidade->setValue    ("");
            $obCmbUnidade->setStyle    ("width: 200px");
            $obCmbUnidade->setCampoID  ("cod_unidade");
            $obCmbUnidade->setCampoDesc("descricao");
            $obCmbUnidade->addOption   ('', 'Selecione');

            $obFormulario = new Formulario;
            $obFormulario->addComponenteComposto($obTxtOrgao, $obCmbOrgao);
            $obFormulario->addComponenteComposto($obTxtUnidade, $obCmbUnidade);

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
        } else {
            $obFormulario = new Formulario;
            $obFormulario->addComponente($obTxtOrgao);
            $obFormulario->addComponente($obTxtUnidade);

            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
        }
    }

    $stJs .= "jq('#spnOrgaoUnidade').html('".$stHTML."');";
    break;

case "MontaUnidade":
    $stJs .= "limpaSelect(f.inCodUnidade,0); \n";
    $stJs .= "jq('#inCodUnidadeTxt').value = ''; \n";
    $stJs .= "jq('#inCodUnidade').append( new Option('Selecione','', 'selected')) ;\n";

    if ($_REQUEST["inCodOrgao"]) {
        include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
        $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio($_REQUEST["inExercicio"]);
        $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("num_unidade");
            $stDesc = $rsCombo->getCampo("nom_unidade");
            $stJs .= "jq('#inCodUnidade').append( new Option('".$rsCombo->getCampo("nom_unidade")."','".$rsCombo->getCampo("num_unidade")."' )); \n";
            $rsCombo->proximo();
        }
    }
    break;

case "mascaraClassificacao":
    //monta mascara da RUBRICA DE DESPESA
    if ($_REQUEST['inCodDespesa'] != "") {
        $arMascClassificacao = Mascara::validaMascaraDinamica($_REQUEST['stMascClassificacao'], $_REQUEST['inCodDespesa']);
        $stJs .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara              ($_REQUEST['stMascClassificacao']);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao    ($arMascClassificacao[1]);
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa($stDescricao);
        if ($stDescricao != "") {
            $stJs .= 'jq("#stDescricaoDespesa").html("'.$stDescricao.'");';
        } else {
            $null = "&nbsp;";
            $stJs .= 'jq("#inCodDespesa").value = "";';
            $stJs .= 'jq("#inCodDespesa").focus();';
            $stJs .= 'jq("#stDescricaoDespesa").html("'.$null.'");';
            $stJs .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
    } else {
        $stJs .= 'jq("#inCodDespesa").value = "";';
        $stJs .= 'jq("#stDescricaoDespesa").html("&nbsp;");';
    }
    break;

case 'buscaFornecedor':
    if ($_REQUEST["inCodFornecedor"] != "") {
        $RCGM = new RCGM;
        $RCGM->setNumCGM($_REQUEST["inCodFornecedor"]);
        $RCGM->listar($dadosCGM);
        $nomFornecedor = $dadosCGM->getCampo("nom_cgm");
        if (!$nomFornecedor) {
            $stJs .= 'jq("#inCodFornecedor").value = "";';
            $stJs .= 'jq("#stNomFornecedor").html("&nbsp;");';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'jq("#stNomFornecedor").html("'.$nomFornecedor.'");';
        }
    } else {
        $stJs .= 'jq("#stNomFornecedor").html("&nbsp;");';
    }
    break;

    default:
        if(Sessao::getExercicio()>2015){
            include_once CAM_FW_PDF."RRelatorio.class.php";

            $obRRelatorio = new RRelatorio;
            $obRRelatorio->executaFrameOculto( "OCGeraRelatorioRestosPagarAnuladoPagamentoEstorno.php" );
        }
    break;
}

echo $stJs;

?>
