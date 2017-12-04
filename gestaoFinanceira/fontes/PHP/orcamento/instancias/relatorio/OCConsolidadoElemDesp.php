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
    * Página Oculta de Filtro de Pesquisa
    * Data de Criação   : 02/03/2005

    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.23
*/

/*
$Log$
Revision 1.8  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioConsolidadoElemDesp.class.php"  );
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                     );
include_once 'JSConsolidadoElemDesp.js';

$obROrcamentoClassificacaoDespesa   = new ROrcamentoClassificacaoDespesa;
$obRRelatorio                       = new RRelatorio;
$obROrcamentoConsolidadoElemDesp    = new ROrcamentoRelatorioConsolidadoElemDesp;
$obROrcamentoRecurso = new ROrcamentoRecurso;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );

$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');
$stCtrl = $request->get('stCtrl');

function montaUnidade()
{
   return  $stJs;
}

switch ($stCtrl) {
    case "montaOrgao":
        $stJs .= montaMensal();
        $stJs .= montaOrgao();
   break;

    case "montaUnidade":

        if ($_REQUEST["inCodOrgao"]) {
              $stCombo  = "inCodUnidade";
              $stComboTxt  = "inCodUnidadeTxt";
              $stJs .= "limpaSelect(f.$stCombo,0); \n";
              $stJs .= "f.$stComboTxt.value=''; \n";
              $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

           $obROrcamentoConsolidadoElemDesp    = new ROrcamentoRelatorioConsolidadoElemDesp;

              $obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgao"]);
              $obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
              $obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

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

    break;

    case "montaUnidadeFinal":

        if ($_REQUEST["inCodOrgaoFinal"]) {
             $stComboFinal  = "inCodUnidadeFinal";
             $stComboFinalTxt  = "inCodUnidadeFinalTxt";
             $stJs .= "limpaSelect(f.$stComboFinal,0); \n";
             $stJs .= "f.$stComboFinalTxt.value=''; \n";
             $stJs .= "f.$stComboFinal.options[0] = new Option('Selecione','', 'selected');\n";

             $obROrcamentoConsolidadoElemDesp    = new ROrcamentoRelatorioConsolidadoElemDesp;

             $obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inCodOrgaoFinal"]);
             $obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
             $obROrcamentoConsolidadoElemDesp->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, $stFiltro,"", $boTransacao );

              while ( !$rsCombo->eof() ) {
                  $arNomFiltro['unidadeFinal'][$rsCombo->getCampo( 'num_unidade' )] = $rsCombo->getCampo( 'nom_unidade' );
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
                 $stJs .= "f.$stComboFinal.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                 $rsCombo->proximo();
             }
         }
         $stJs .= $js;

    break;
    case 'buscaRecurso':

        $obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
        $obROrcamentoRecurso->listar( $rsRecurso );
        $obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
        $arMascara = Mascara::validaMascaraDinamica($stMascaraRecurso,$_POST['inCodRecurso']);
        if ( $rsRecurso->getNumLinhas() > -1 and $_POST['inCodRecurso'] ) {
            $js .= 'f.inCodRecurso.value = "'.$arMascara[1].'";';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$rsRecurso->getCampo("nom_recurso").'";';
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
           $arFiltro['relatorio'] = "Consolidado";
        } else {
           $arFiltro['relatorio'] = "";
        }
        $stFiltro = "";

        if ($arFiltro['inCodRecurso'] != "") {
            $stFiltro .= " AND od.cod_recurso = " . $arFiltro['inCodRecurso'];
        }

        $obROrcamentoConsolidadoElemDesp    = new ROrcamentoRelatorioConsolidadoElemDesp;
        $obROrcamentoConsolidadoElemDesp->setFiltro             ( $stFiltro );
        $obROrcamentoConsolidadoElemDesp->setCodEntidade        ( $stEntidade );
        $obROrcamentoConsolidadoElemDesp->setExercicio          ( Sessao::getExercicio() );
        $obROrcamentoConsolidadoElemDesp->setCodOrgaoInicial    ( $arFiltro['inCodOrgao'] );
        $obROrcamentoConsolidadoElemDesp->setCodOrgaoFinal      ( $arFiltro['inCodOrgaoFinal'] );
        $obROrcamentoConsolidadoElemDesp->setCodUnidadeInicial  ( $arFiltro['inCodUnidade'] );
        $obROrcamentoConsolidadoElemDesp->setCodUnidadeFinal    ( $arFiltro['inCodUnidadeFinal'] );
        $obROrcamentoConsolidadoElemDesp->setCodFuncao          ( $arFiltro['inCodFuncao'] );
        $obROrcamentoConsolidadoElemDesp->setCodSubfuncao       ( $arFiltro['inCodSubFuncao'] );
        $obROrcamentoConsolidadoElemDesp->setDataInicial        ( $arFiltro['stDataInicial']);
        $obROrcamentoConsolidadoElemDesp->setDataFinal          ( $arFiltro['stDataFinal'] );
        $obROrcamentoConsolidadoElemDesp->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );
        if ($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao']) {
            $obROrcamentoConsolidadoElemDesp->setDestinacaoRecurso( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );
        }

        $obROrcamentoConsolidadoElemDesp->geraRecordSetRelatorio( $rsConsolidado,"",$arFiltro['inTipo'] );
        Sessao::write('rsConsolidado',$rsConsolidado);

        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioConsolidadoElemDesp.php");
    break;
}

if($stJs)
   SistemaLegado::executaFrameOculto($stJs);

?>
