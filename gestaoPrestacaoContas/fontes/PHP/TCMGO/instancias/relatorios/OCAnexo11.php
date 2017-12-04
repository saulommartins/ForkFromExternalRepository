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
    * Pagina oculta do Demonstrativo de Despesa - Anexo 11

    * Data de Criação: 19/02/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    $Id:$

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';
include_once '../../classes/negocio/RTGORelatorioAnexo11.class.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoDespesa.class.php';

$obRRelatorio = new RRelatorio;
$obRegra      = new RTGORelatorioAnexo11;
$obOrcamentoDespesa   = new ROrcamentoDespesa;

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

//seta elementos do filtro
$stFiltro = '';
$stEntidade = '';

//seta elementos do filtro para ENTIDADE
if ($arFiltroRelatorio['inCodEntidade'] != '') {
    $stEntidade = implode(',', $arFiltroRelatorio['inCodEntidade']);
} else {
    $stEntidade .= $arFiltroRelatorio['stTodasEntidades'];
}

switch ($_REQUEST['stCtrl']) {
    case 'MontaUnidade':
        if ($_REQUEST['inNumOrgao']) {
            $stCombo  = 'inNumUnidade';
            $stComboTxt  = 'inNumUnidadeTxt';
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obOrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
            $obOrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obOrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->consultar($rsCombo, $stFiltro,'', $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo('num_unidade');
                $stDesc = $rsCombo->getCampo('nom_unidade');
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

        if($arFiltroRelatorio['inNumOrgao'])
                $stFiltro .= " AND D.num_orgao = ".$arFiltroRelatorio["inNumOrgao"];
        if($arFiltroRelatorio["inNumUnidade"])
                $stFiltro .= " AND D.num_unidade = ".$arFiltroRelatorio["inNumUnidade"];

        $obRegra->setFiltro     ($stFiltro);
        $obRegra->setCodEntidade($stEntidade);
        $obRegra->setExercicio  (Sessao::getExercicio());
        $obRegra->setDtInicial  ($arFiltroRelatorio['stDataInicial']);
        $obRegra->setDtFinal    ($arFiltroRelatorio['stDataFinal']);
        $obRegra->setSituacao   ($arFiltroRelatorio['stTipoRelatorio']);
        $obRegra->geraRecordSet($rsRecordSet );
        Sessao::write('rsRecordSet', $rsRecordSet);
        $obRRelatorio->executaFrameOculto('OCGeraRelatorioAnexo11.php');
    break;
}

?>
