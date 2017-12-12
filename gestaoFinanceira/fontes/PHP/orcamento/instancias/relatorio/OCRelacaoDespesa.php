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
    * Data de Criação   : 12/08/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Marcelo B Paulino

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.18
*/

/*
$Log$
Revision 1.7  2006/11/20 21:37:51  gelson
Bug #7444#
Parte 1

Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRelacaoDespesa.class.php"  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );

$obRegra = new ROrcamentoRelatorioRelacaoDespesa;

$obROrcamentoDespesa        = new ROrcamentoDespesa;

//seta elementos do filtro
$stFiltro = "";

$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');
//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro .= " AND od.cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor.",";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 ) . ")";
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

            $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
            $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obRegra->obROrcamentoUnidade->consultar($rsCombo, $stFiltro,"", $boTransacao );
            while ( !$rsCombo->eof() ) {
                $arNomFiltro['unidade'][$rsCombo->getCampo( 'num_unidade' )] = $rsCombo->getCampo( 'nom_unidade' );
                $rsCombo->proximo();
            }
            Sessao::write('filtroNomRelatorio', $arNomFiltro);
            $rsCombo->setPrimeiroElemento();

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

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        if ($_POST['inCodDespesa'] != '') {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
            $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

            //busca DESCRICAO DA RUBRICA DE DESPESA
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
            if ($stDescricao != "") {
                $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
            } else {
                $null = "&nbsp;";
                $js .= 'f.inCodDespesa.value = "";';
//                $js .= 'f.inCodDespesa.focus();';
                $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
            }
            SistemaLegado::executaFrameOculto( $js );
        } else {
            $null = "&nbsp;";
            $js = 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            echo SistemaLegado::executaFrameOculto($js);
        }

   case 'buscaRecurso':
        $obROrcamentoRecurso = new ROrcamentoRecurso;
        $obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
        $obROrcamentoRecurso->listar( $rsRecurso );
        $obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
        $arMascara = Mascara::validaMascaraDinamica($stMascaraRecurso,$_POST['inCodRecurso']);
        if ( $rsRecurso->getNumLinhas() > -1 and $_POST['inCodRecurso'] ) {
            $js .= 'f.inCodRecurso.value = "'.$arMascara[1].'";';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$rsRecurso->getCampo("nom_recurso").'";';
            $js .= 'f.stDescricaoRecurso.value = "'.$rsRecurso->getCampo("nom_recurso").'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodRecurso.value = "";';
            $js .= 'f.inCodRecurso.focus();';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
            if( $_POST['inCodRecurso'] )
                $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodRecurso'].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    default:

        if ($arFiltro['inCodPao'] != '') {
            //$stFiltro .= ' AND od.num_pao = '.$arFiltro['inCodPao'];
            $stFiltro .= ' AND ppa.acao.num_acao = '.$arFiltro['inCodPao'];
        }
        if ($arFiltro['inCodFuncao'] != '') {
            $stFiltro .= ' AND od.cod_funcao = '.$arFiltro['inCodFuncao'];
        }
        if ($arFiltro['inCodSubFuncao'] != '') {
            $stFiltro .= ' AND od.cod_subfuncao = '.$arFiltro['inCodSubFuncao'];
        }
        if ($arFiltro['inCodPrograma'] != '') {
            //$stFiltro .= ' AND od.cod_programa = '.$arFiltro['inCodPrograma'];
            $stFiltro .= ' AND ppa.programa.num_programa = '.$arFiltro['inCodPrograma'];
        }
        if ($arFiltro['inCodDespesa'] != '') {
            $stFiltro .= " AND orcamento.fn_consulta_class_despesa(ocd.cod_conta, ocd.exercicio, \'9.9.9.9.99.99.99.99.99\') = \'".$arFiltro['inCodDespesa']."\'";
        }

        $obRegra->setFiltro         ( $stFiltro );
        $obRegra->setExercicio      ( Sessao::getExercicio() );
        $obRegra->setTipoOrdenacao  ( $arFiltro['stTipoOrdenacao']);

        $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arFiltro["inNumOrgao"]);
        $obRegra->obROrcamentoUnidade->setNumeroUnidade($arFiltro["inNumUnidade"]);
        $obRegra->obROrcamentoRecurso->setCodRecurso($arFiltro["inCodRecurso"]);
        if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
            $obRegra->obROrcamentoRecurso->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

        $obRegra->obROrcamentoRecurso->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );

        $obRegra->geraRecordSet( $rsRelacaoDespesa );
        Sessao::write('rsRelacaoDespesa',$rsRelacaoDespesa);
        //sessao->transf5 = $rsRelacaoDespesa;
        $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioRelacaoDespesa.php" );
    break;
}

?>
