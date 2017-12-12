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
    * Página de Formulário Oculto de Consultar Empenho
    * Data de Criação   : 24/09/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: OCConsultarEmpenho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

$obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoPreEmpenho->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet , $boExecuta = true)
{
    $arRecordSetAux = $arRecordSet;
    foreach ($arRecordSetAux as $inChave => $arValor) {
        if (trim($arValor['complemento']) == "") {
            $arRecordSet[$inChave]['possui_complemento'] = 'f';
        } else {
            $arRecordSet[$inChave]['possui_complemento'] = 't';
        }
    }
    unset($arRecordSetAux);

    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao( "vl_total", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_empenhado_anulado", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_liquidado_anulado", "NUMERIC_BR" );
    $rsLista->addFormatacao( "vl_liquidado", "NUMERIC_BR" );

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
    $table = new TableTree;
    $table->setRecordset( $rsLista );

    $table->setArquivo( CAM_GF_EMP_INSTANCIAS . 'empenho/OCConsultarEmpenho.php');
    $table->setParametros( array() );
    $table->setComplementoParametros( "stCtrl=detalharEmpenho");

    // Defina o título da tabela
    $table->setSummary( 'Registros' );

    $table->addCondicionalTree( 'possui_complemento' , 't' );

    // lista zebrada
    ////$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Descrição' , 60  );
    $table->Head->addCabecalho( 'Empenhado' , 10  );
    $table->Head->addCabecalho( 'Liquidado' , 10  );

    $table->Body->addCampo( 'nom_item', 'E' );
    $table->Body->addCampo( 'vl_total', 'D' );
    $table->Body->addCampo( 'vl_liquidado', 'D' );
    $table->montaHTML( true );
    $stHTML = $table->getHtml();

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."';");
    } else {
        return $stHTML;
    }
}

switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista( Sessao::read('arItens') );
    break;

case 'detalharEmpenho':
    // recebe a linha que está sendo detalhada
    $arLinha = explode("_", $_REQUEST['linha_table_tree']);
    $arItens = Sessao::read('arItens');
    echo $arItens[($arLinha[4]-1)]['complemento'];

    break;

case 'buscaFornecedorDiverso':
    if ($_POST["inCodFornecedor"] != "") {
        $obREmpenhoPreEmpenho->obRCGM->setNumCGM( $_POST["inCodFornecedor"] );
        $obREmpenhoPreEmpenho->obRCGM->listar( $rsCGM );
        $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
        if (!$stNomFornecedor) {
            $js .= 'f.inCodFornecedor.value = "";';
            $js .= 'f.inCodFornecedor.focus();';
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
        }
    } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

case "MontaUnidade":
    if ($_REQUEST["inNumOrgao"]) {
        $stCombo  = "inNumUnidade";
        $stComboTxt  = "inNumUnidadeTxt";
        $stJs .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stComboTxt.value=''; \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        $obREmpenhoPreEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
        $obREmpenhoPreEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsCombo, $stFiltro,"", $boTransacao );

        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("num_unidade");
            $stDesc = $rsCombo->getCampo("nom_unidade");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
    }
    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

case "mascaraClassificacao":
    //monta mascara da RUBRICA DE DESPESA
    $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
    $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

    //busca DESCRICAO DA RUBRICA DE DESPESA
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
    $obREmpenhoPreEmpenho->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
    if ($stDescricao != "") {
        $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
    } else {
        $null = "&nbsp;";
        $js .= 'f.inCodDespesa.value = "";';
        $js .= 'f.inCodDespesa.focus();';
        $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
        $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
    }
    SistemaLegado::executaFrameOculto( $js );
    break;
case 'buscaDotacao':
        $obREmpenhoPreEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDotacao"] );
        $obREmpenhoPreEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obREmpenhoPreEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        if (!$stNomDespesa) {
            $js .= 'f.inCodDotacao.value = "";';
            $js .= 'f.inCodDotacao.focus();';
            $js .= 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_POST["inCodDotacao"].")','form','erro','".Sessao::getId()."');";
        } else {
            $stNomDespesa = $rsDespesa->getCampo( "descricao" );
            $js .= 'd.getElementById("stNomDotacao").innerHTML = "'.$stNomDespesa.'";';
        }

    SistemaLegado::executaFrameOculto($js);
    break;

}
