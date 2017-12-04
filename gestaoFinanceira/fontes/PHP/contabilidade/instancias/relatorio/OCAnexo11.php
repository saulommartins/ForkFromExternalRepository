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
    * Página de Formulario Oculto para relatorio de Demonstrativo de Despesa - Anexo 11
    * Data de Criação: 12/05/2005

    * @author Analista: Gelson Wolowski
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Id: OCAnexo11.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioAnexo11.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php" );

$obRRelatorio = new RRelatorio;
$obRegra      = new RContabilidadeRelatorioAnexo11;
$obOrcamentoDespesa   = new ROrcamentoDespesa;

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
$arFiltro = Sessao::read('filtroRelatorio');
if ($arFiltro['inCodEntidade'] != "") {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor." , ";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 2 );
    $stEntidade = $stFiltro;
    $stFiltro = "D.cod_entidade IN  (".$stFiltro.") ";
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}

switch ($_REQUEST['stCtrl']) {
    case "MontaUnidade":
        if ($_REQUEST["inNumOrgao"]) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obOrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
            $obOrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obOrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

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

    default:

        if($arFiltro["inNumOrgao"])
                $stFiltro .= " AND D.num_orgao = ".$arFiltro["inNumOrgao"];
        if($arFiltro["inNumUnidade"])
                $stFiltro .= " AND D.num_unidade = ".$arFiltro["inNumUnidade"];

        $obRegra->setFiltro     ( $stFiltro   );
        $obRegra->setCodEntidade( $stEntidade );
        $obRegra->setExercicio  ( Sessao::getExercicio() );
        $obRegra->setDtInicial  ( $arFiltro['stDataInicial'] );
        $obRegra->setDtFinal    ( $arFiltro['stDataFinal'] );
        $obRegra->setSituacao   ( $arFiltro['stTipoRelatorio'] );
        $obRegra->geraRecordSet( $rsRecordSet );
        Sessao::write('rsRecordSet', $rsRecordSet);
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioAnexo11.php" );
    break;
}

?>
