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
    * PÃ¡gina de Processamento
    * Data de CriaÃ§Ã£o   : 15/04/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * Casos de uso: uc-06.03.00

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBObras.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoCategoriaObra.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoFonteObras.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoObra.class.php");
include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBTipoSituacao.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterObras";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

Sessao::setTrataExcecao ( true );

$obTTPBObras  = new TTPBObras();
Sessao::getTransacao()->setMapeamento( $obTTPBObras );

switch ($_REQUEST['stAcao']) {
  case "incluir":
    $obTTPBObras->setDado('num_obra'            , $_REQUEST['inNumero']);
    $obTTPBObras->setDado('exercicio'           , Sessao::getExercicio());
    $obTTPBObras->setDado('dt_cadastro'         , $_REQUEST['stDataCadastro'] );
    $obTTPBObras->setDado('patrimonio'          , $_REQUEST['boPatrimonio'] );
    $obTTPBObras->setDado('localidade'          , $_REQUEST['stLocalidade'] );
    $obTTPBObras->setDado('descricao'           , $_REQUEST['stDescricao'] );
    $obTTPBObras->setDado('cod_tipo_obra'       , $_REQUEST['inCodTipoObra'] );
    $obTTPBObras->setDado('cod_tipo_categoria'  , $_REQUEST['inCodCategoriaObra'] );
    $obTTPBObras->setDado('cod_tipo_fonte'      , $_REQUEST['inCodFonteObra'] );
    $obTTPBObras->setDado('mes_ano_estimado_fim', str_pad($_REQUEST['inMesEstimado'],2,"0", STR_PAD_LEFT).$_REQUEST['inAnoEstimado'] );
    $obTTPBObras->setDado('dt_inicio'           , $_REQUEST['stDataInicio'] );
    $obTTPBObras->setDado('dt_conclusao'        , $_REQUEST['stDataConclusao'] );
    $obTTPBObras->setDado('dt_recebimento'      , $_REQUEST['stDataRecebimento'] );
    $obTTPBObras->setDado('cod_tipo_situacao'   , $_REQUEST['inCodSituacao'] );
    
    $nuVlOrcado = '0.00';
    if($_REQUEST['vlOrcado']!=null)
      $nuVlOrcado = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlOrcado']));

    $obTTPBObras->setDado('vl_obra', $nuVlOrcado );
    
    $obTTPBObras->recuperaPorChave( $rsVerifica );
    if (!$rsVerifica->eof()) {
        SistemaLegado::exibeAviso("Obra - ".$_REQUEST['inNumero']." já cadastrada.");
        break;
    }
    $obTTPBObras->inclusao();

    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Obra - ".$_REQUEST['inNumero'],"incluir","incluir_n", Sessao::getId(), "../");
  break;

  case "alterar":
    $obTTPBObras->setDado('num_obra'            , $_REQUEST['inNumero']);
    $obTTPBObras->setDado('exercicio'           , $_REQUEST['exercicio']);
    $obTTPBObras->setDado('dt_cadastro'         , $_REQUEST['stDataCadastro'] );
    $obTTPBObras->setDado('patrimonio'          , $_REQUEST['boPatrimonio'] );
    $obTTPBObras->setDado('localidade'          , $_REQUEST['stLocalidade'] );
    $obTTPBObras->setDado('descricao'           , $_REQUEST['stDescricao'] );
    $obTTPBObras->setDado('cod_tipo_obra'       , $_REQUEST['inCodTipoObra'] );
    $obTTPBObras->setDado('cod_tipo_categoria'  , $_REQUEST['inCodCategoriaObra'] );
    $obTTPBObras->setDado('cod_tipo_fonte'      , $_REQUEST['inCodFonteObra'] );
    $obTTPBObras->setDado('mes_ano_estimado_fim', str_pad($_REQUEST['inMesEstimado'],2,"0", STR_PAD_LEFT).$_REQUEST['inAnoEstimado'] );
    $obTTPBObras->setDado('dt_inicio'           , $_REQUEST['stDataInicio'] );
    $obTTPBObras->setDado('dt_conclusao'        , $_REQUEST['stDataConclusao'] );
    $obTTPBObras->setDado('dt_recebimento'      , $_REQUEST['stDataRecebimento']);
    $obTTPBObras->setDado('cod_tipo_situacao'   , $_REQUEST['inCodSituacao'] );
    
    $nuVlOrcado = '0.00';
    
    if($_REQUEST['vlOrcado'] != null){
      $nuVlOrcado = str_replace(',', '.', str_replace('.', '', $_REQUEST['vlOrcado']));
    }

    $obTTPBObras->setDado('vl_obra', $nuVlOrcado );
    $obTTPBObras->alteracao();
    
    SistemaLegado::alertaAviso($pgList,"Obra - ".$_REQUEST['inNumero'],"alterar","aviso", Sessao::getId(), "../");
  break;

  case "excluir":
    include_once(CAM_GPC_TPB_MAPEAMENTO."TTPBEmpenhoObras.class.php");
    $obEmpenhoObras = new TTPBEmpenhoObras();
    $stFiltro = ' WHERE num_obra = '.$_REQUEST['num_obra']." AND exercicio_empenho = '".$_REQUEST['exercicio']."'";
    $obEmpenhoObras->recuperaTodos( $rsTemp, $stFiltro);
    
    if ( $rsTemp->getNumLinhas() > -1) {
        SistemaLegado::alertaAviso($pgList,"Obra - ".$_REQUEST['num_obra']." não pode ser excluída por estar relacionada com empenho!","n_excluir","alerta", Sessao::getId(), "../");
    } else {
      $obTTPBObras->setDado('num_obra',$_REQUEST['num_obra']);
      $obTTPBObras->setDado('exercicio',$_REQUEST['exercicio']);
      $obErro = new Erro();
      $obErro = $obTTPBObras->exclusao();
      SistemaLegado::alertaAviso($pgList,"Obra - ".$_REQUEST['num_obra'],"excluir","aviso", Sessao::getId(), "../");
    }

  break;
}

Sessao::encerraExcecao();
?>
