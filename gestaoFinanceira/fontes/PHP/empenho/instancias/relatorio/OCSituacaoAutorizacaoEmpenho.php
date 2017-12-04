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
    * Página de Filtro de Relatório Situação de Autorizações de Empenho
    * Data de Criação   : 12/10/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: cako $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso : uc-02.03.34
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoAutorizacaoEmpenho.class.php"  );

$obRegra = new REmpenhoRelatorioSituacaoAutorizacaoEmpenho;

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
        if ($_REQUEST["inCodOrgao"]) {
            $stCombo  = "inCodUnidade";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

            include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php");
            $obROrcamentoUnidadeOrcamentaria = new ROrcamentoUnidadeOrcamentaria();
            $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            $obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
            $obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

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
    break;

    default:
        $obRegra->setCodEntidade( $stEntidade   );
        $obRegra->obREmpenhoEmpenho->setExercicio               ( $arFiltro['inExercicio']   );

        $obRegra->obREmpenhoEmpenho->setDtEmpenhoInicial        ( $arFiltro['stDataInicial'] );
        $obRegra->obREmpenhoEmpenho->setDtEmpenhoFinal          ( $arFiltro['stDataFinal']   );

        $stDataInicial = "01/01/".$arFiltro['inExercicio'];

        $obRegra->setCodAutorizacao                             ( $arFiltro['inNumAutorizacao'] );
        $obRegra->setDataInicialAnulacao                        ( $stDataInicial );
        $obRegra->setDataFinalAnulacao                          ( $arFiltro['stDataSituacao'] );
        $obRegra->setDataInicialLiquidacao                      ( $stDataInicial );
        $obRegra->setDataFinalLiquidacao                        ( $arFiltro['stDataSituacao'] );
        $obRegra->setDataInicialEstornoLiquidacao               ( $stDataInicial );
        $obRegra->setDataFinalEstornoLiquidacao                 ( $arFiltro['stDataSituacao'] );
        $obRegra->setDataInicialPagamento                       ( $stDataInicial );
        $obRegra->setDataFinalPagamento                         ( $arFiltro['stDataSituacao'] );
        $obRegra->setDataInicialEstornoPagamento                ( $stDataInicial );
        $obRegra->setDataFinalEstornoPagamento                  ( $arFiltro['stDataSituacao'] );

        $obRegra->obREmpenhoEmpenho->setCodEmpenhoInicial       ( $arFiltro['inCodEmpenhoInicial']            );
        $obRegra->obREmpenhoEmpenho->setCodEmpenhoFinal         ( $arFiltro['inCodEmpenhoFinal']              );
        $obRegra->obREmpenhoEmpenho->setCodDespesa              ( $arFiltro['inCodDotacao']                   );
        $obRegra->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodEstrutural( $arFiltro['inCodDespesa'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $arFiltro['inNumOrgao'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade( $arFiltro['inNumUnidade'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $arFiltro['inCodRecurso'] );
        if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
            $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );

        $obRegra->setOrdenacao                                  ( $arFiltro['inOrdenacao']    );
        $obRegra->obREmpenhoEmpenho->obRCGM->setNumCGM          ( $arFiltro['inCodCredor']    );
        $obRegra->setSituacao                                   ( $arFiltro['inSituacao']     );
        $obRegra->setCentroCusto                                ( $arFiltro['inCentroCusto']  );

        $obRegra->geraRecordSet( $rsSituacaoEmpenho );
        Sessao::write('rsRecordSet', $rsSituacaoEmpenho);
        $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioSituacaoAutorizacaoEmpenho.php" );
    break;

}
echo $stJs;
?>
