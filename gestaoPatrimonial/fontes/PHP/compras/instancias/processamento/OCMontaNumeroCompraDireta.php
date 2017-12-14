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
    * Arquivo de popup de busca de Item do catálogo
    * Data de Criação: 19/10/2006

    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * Casos de uso:

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( TCOM.'TComprasCompraDireta.class.php' );
include_once(CAM_GP_COM_COMPONENTES.'IMontaNumeroCompraDireta.class.php');

$obTCompraDireta = new TComprasCompraDireta();

$stJs = "";

function preencheCompraDireta($rsRecordSet, $stComponente)
{
    $stJs = "limpaSelect(f.".$stComponente.",1);\n";
    $obIMontaNumeroCompraDireta = Sessao::read('IMontaNumeroCompraDireta');
    if ( $rsRecordSet->getNumLinhas() == 1 && $obIMontaNumeroCompraDireta->getSelecionaAutomaticamenteCompraDireta() ) {
        $selected = 'selected';
    } else {
        $selected = '';
    }

    while ( !$rsRecordSet->eof() ) {
        $stJs .= "f.".$stComponente."[".$rsRecordSet->getCorrente()."] = new Option('".$rsRecordSet->getCampo('cod_compra_direta')."','".$rsRecordSet->getCampo('cod_compra_direta')."','".$selected."');\n";
        $rsRecordSet->proximo();
    }

    return $stJs;
}

function limpaSelect()
{
    $stJs = "f.stExercicioCompraDireta.value = '".Sessao::getExercicio()."';\n";
    $stJs.= "f.inCodEntidade.value = '';\n";
    $stJs.= "f.stNomEntidade.value = '';\n";
    $stJs.= "f.inCodModalidade.value = 0;\n";
    $stJs.= "f.inCodCompraDireta.value = '';\n";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "MontaUnidade":
        include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
        if ($_REQUEST["inNumOrgao"]) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            
            $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
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
        echo $stJs;
    break;
    
    case 'carregaModalidade':
        if ($_REQUEST['stExercicioCompraDireta'] && $_REQUEST['inCodEntidade']) {
        } else {
            $stJs = "f.inCodModalidade.value = '';\n";
            $stJs.= "f.inCodCompraDireta.value = '';\n";
            $stJs.= "limpaSelect(f.inCodCompraDireta,1);\n";
        }
    break;

    case 'carregaCompraDireta':
    case 'carregaCompraDiretaContrato':

        if ($_REQUEST['stExercicioCompraDireta'] && $_REQUEST['inCodEntidade'] && $_REQUEST['inCodModalidade']) {
            $obTCompraDireta->setDado( 'exercicio_entidade' , $_REQUEST['stExercicioCompraDireta'] );
            $obTCompraDireta->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
            $obTCompraDireta->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );

            $obTCompraDireta->recuperaCompraDiretaContratoCombo( $rsCompraDireta );

            if ( $rsCompraDireta->getNumLinhas() > 0 ) {
                $stJs.= preencheCompraDireta( $rsCompraDireta, 'inCodCompraDireta' );
            } else {
                $stJs.= "f.inCodCompraDireta.selectedIndex =  0;\n";
                $stJs.= "limpaSelect(f.inCodCompraDireta,1);\n";
            }
        } else {
            $stJs = "f.inCodCompraDireta.value = '';\n";
            $stJs.= "limpaSelect(f.inCodCompraDireta,1);\n";
        }
    break;

    case 'carregaDadosCompraDireta':
        if ($_REQUEST['stExercicioCompraDireta'] && $_REQUEST['inCodEntidade'] && $_REQUEST['inCodModalidade'] && $_REQUEST['inCodCompraDireta']) {
            $obTCompraDireta->setDado( 'exercicio' , $_REQUEST['stExercicioCompraDireta'] );
            $obTCompraDireta->setDado( 'cod_entidade', $_REQUEST['inCodEntidade'] );
            $obTCompraDireta->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
            $obTCompraDireta->setDado( 'cod_compra_direta', $_REQUEST['inCodCompraDireta'] );
            $obTCompraDireta->recuperaCompraDireta( $rsProcesso );

            if ( $rsProcesso->getNumLinhas() > 0 ) {
                $stJs .= "f.hdnDtCompraDireta.value = '".$rsProcesso->getCampo( 'dt_compra_direta' )."'\n";
            }
        } else {
            $stJs.= "f.inCodCompraDireta.value = 0;\n";
        }
    break;
}
echo $stJs;
?>
