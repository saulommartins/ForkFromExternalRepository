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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.5  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRazaoCredor.class.php"  );

$obRegra            = new REmpenhoRelatorioRazaoCredor;

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$arFiltro = Sessao::read('filtroRelatorio');

//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

if ( $rsTotalEntidades->getNumLinhas() == $inCount ) {
   $arFiltro['relatorio'] = "(Consolidado)";
} else {
   $arFiltro['relatorio'] = "";
}

Sessao::write('filtroRelatorio', $arFiltro);
switch ($_REQUEST['stCtrl']) {
    case "MontaUnidade":
        if ($_REQUEST["inNumOrgao"]) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsCombo, $stFiltro,"", $boTransacao );

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
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'f.inCodDespesa.focus();';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    default:
        $obRegra->setCodEntidade            ( $stEntidade );
        $obRegra->setExercicio              ( $arFiltro['stExercicio'] );

        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arFiltro['inNumOrgao']);
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade($arFiltro['inNumUnidade'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodEstrutural( $arFiltro['inCodDespesa'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $arFiltro['inCodRecurso'] );
        if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao']);

        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( $arFiltro['inCGM'] );

        $obRegra->geraRecordSet( $rsRazaoCredor );
        Sessao::write('rsRecordSet', $rsRazaoCredor);
        $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioRazaoCredor.php" );
    break;

    case 'buscaFornecedor':
        if ($_POST["inCGM"] != "") {
            $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( $_POST["inCGM"] );
            $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCGM.value = "";';
                $js .= 'f.inCGM.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_POST["inCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

}

?>
