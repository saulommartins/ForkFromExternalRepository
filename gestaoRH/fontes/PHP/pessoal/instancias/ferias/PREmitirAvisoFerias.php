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
    * Página de Filtro do Relatório Emitir Aviso Férias
    * Data de Criação : 25/05/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30860 $
    $Name$
    $Autor: $
    $Date: 2007-12-17 17:28:49 -0200 (Seg, 17 Dez 2007) $

    * Casos de uso: uc-04.04.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "EmitirAvisoFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$preview = new PreviewBirt(4,22,4);
$preview->setVersaoBirt( '2.5.0' );
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."ferias/FLEmitirAvisoFerias.php");
$preview->setTitulo('Emitir Aviso de Férias');
$preview->setNomeArquivo('avisoferias');

$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stcodEntidade", Sessao::getCodEntidade($boTransacao));

if ($_REQUEST['inCodMes']<=9) {
    $Mes = "0".$_REQUEST['inCodMes'];
} else {
    $Mes = $_REQUEST['inCodMes'];
}
$preview->addParametro("mesCompetencia", $Mes);
$preview->addParametro("anoCompetencia", $_REQUEST['inAno']);

$OrderBy = "";

if ($_REQUEST['boOrdenacaoLotacao']=="t") {
    if ($_REQUEST['stOrdenacaoLotacao']=="A") {
        $OrderBy .= "orgao,";
    } else {
        $OrderBy .= "cod_orgao,";
    }
}

if ($_REQUEST['boOrdenacaoRegime']=="t") {
    if ($_REQUEST['stOrdenacaoRegime']=="A") {
        $OrderBy .= "regime,";
    } else {
        $OrderBy .= "contrato_servidor.cod_regime,";
    }
}

if ($_REQUEST['boOrdenacaoContrato']=="t") {
    if ($_REQUEST['stOrdenacaoContrato']=="A") {
        $OrderBy .= "sw_cgm.nom_cgm,";
    } else {
        $OrderBy .= "contrato.registro,";
    }
}

if ($OrderBy == "") {
    $OrderBy = "null";
} else {
    $OrderBy = substr($OrderBy,0,strlen($OrderBy)-1);
}

$preview->addParametro("OrderBy", $OrderBy);

if (is_array($_REQUEST['inCodSubDivisaoSelecionados']) &&
      count($_REQUEST['inCodSubDivisaoSelecionados'])>0){
      foreach ($_REQUEST['inCodSubDivisaoSelecionados'] as $array) {
          $arrSubDivisao.= $array.",";
      }
      $arrSubDivisao = substr($arrSubDivisao,0,strlen($arrSubDivisao)-1);
      $preview->addParametro("codSubDivisao", $arrSubDivisao);
} else {
      $preview->addParametro("codSubDivisao", "null");
}

if (is_array($_REQUEST['inCodRegimeSelecionados']) &&
      count($_REQUEST['inCodRegimeSelecionados'])>0){
      foreach ($_REQUEST['inCodRegimeSelecionados'] as $array) {
          $arrRegime.= $array.",";
      }
      $arrRegime = substr($arrRegime,0,strlen($arrRegime)-1);
      $preview->addParametro("codRegime", $arrRegime);
} else {
      $preview->addParametro("codRegime", "null");
}

$arContratos = Sessao::read('arContratos');
if(is_array($arContratos) &&
    count($arContratos)>0){
    foreach ($arContratos as $array) {
        $arrContrato.=  $array['inContrato'].",";
    }
  $arrContrato = substr($arrContrato,0,strlen($arrContrato)-1);
  $preview->addParametro( "codMatricula",$arrContrato);
} else {
    $preview->addParametro( "codMatricula","null"               );
}

if(is_array($_REQUEST['inCodLotacaoSelecionados']) &&
    count($_REQUEST['inCodLotacaoSelecionados'])>0){
    foreach ($_REQUEST['inCodLotacaoSelecionados'] as $array) {
        $arrLotacao.= $array.",";
    }
    $arrLotacao = substr($arrLotacao, 0, strlen($arrLotacao)-1);
    $preview->addParametro("codLotacao", $arrLotacao);

} else {
    $preview->addParametro("codLotacao", "null");
}

if(is_array($_REQUEST['inCodLocalSelecionados']) &&
    count($_REQUEST['inCodLocalSelecionados'])>0){
    foreach ($_REQUEST['inCodLocalSelecionados'] as $array) {
        $arrLocal.= $array.",";
    }
    $arrLocal = substr($arrLocal, 0, strlen($arrLocal)-1);
    $preview->addParametro("codLocal", $arrLocal);

} else {
    $preview->addParametro("codLocal", "null");
}
$preview->preview();
?>
