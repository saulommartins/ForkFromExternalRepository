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
    * Data de Criação   : 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso : uc-02.03.07
*/

/*
$Log$
Revision 1.7  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhoPagar.class.php"                          );
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"                          );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                                      );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                                      );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"                                 );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhadoPagoLiquidado.class.php"                );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                                                   );

$obRegra = new REmpenhoRelatorioEmpenhoPagar;
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');

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

if ($rsTotalEntidades->getNumLinhas() == $inCount) {
   $arFiltro['relatorio'] = "(Consolidado)";
} else {
   $arFiltro['relatorio'] = "";
}

switch ($_REQUEST['stCtrl']) {
case 'buscaFornecedorDiverso':
    if ($_POST["inCodFornecedor"] != "") {
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM($_POST["inCodFornecedor"] );
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->listar($rsCGM);
        $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
        if (!$stNomFornecedor) {
            $js .= 'f.inCodFornecedor.value = "";';
            $js .= 'f.inCodFornecedor.focus();';
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
        }
    } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

case "MontaUnidade":

    $obROrcamentoDespesa        = new ROrcamentoDespesa;
    $obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

    $obREntidade                = new ROrcamentoEntidade;
    $obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
    $obREntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );
    $obRRelatorio                       = new RRelatorio;
    $obREmpenhoEmpenhadoPagoLiquidado   = new REmpenhoRelatorioEmpenhadoPagoLiquidado;
    $obROrcamentoUnidadeOrcamentaria    = new ROrcamentoUnidadeOrcamentaria;
    $obROrcamentoRecurso                = new ROrcamentoRecurso;
    $obROrcamentoClassificacaoDespesa   = new ROrcamentoClassificacaoDespesa;

        if ($_REQUEST["inCodOrgao"]) {
            $stCombo  = "inCodUnidade";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

            $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            $obROrcamentoUnidadeOrcamentaria->listar($rsCombo);
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

}
