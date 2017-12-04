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
  * Página de Formulario
  * Data de Criação: 17/03/2016
  * @author Desenvolvedor: Michel Teixeira
  * $Id: OCRelatorioDividaFlutuante.php 64655 2016-03-18 16:50:13Z michel $
  *
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;
include_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALRelatorioDividaFlutuante.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoSituacaoEmpenho.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioDividaFlutuante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgOculGera = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$boTransacao = new Transacao();
$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "", "", $boTransacao);

foreach ($rsEntidade->getElementos() as $entidades) {
    $arCodEntidades[]  = $entidades['cod_entidade'];
}

sort($arCodEntidades);
$inCodEntidades = implode(",", $arCodEntidades);
$stDataInicial  = $request->get('stDataInicial');
$stDataFinal    = $request->get('stDataFinal');

$obTTCEALRelatorioDividaFlutuante = new TTCEALRelatorioDividaFlutuante();
$obTTCEALRelatorioDividaFlutuante->setDado( 'exercicio'      , Sessao::getExercicio());
$obTTCEALRelatorioDividaFlutuante->setDado( 'cod_entidade'   , $inCodEntidades);
$obTTCEALRelatorioDividaFlutuante->setDado( 'data_inicial'   , $stDataInicial);
$obTTCEALRelatorioDividaFlutuante->setDado( 'data_final'     , $stDataFinal);

//Gerando os records sets
$obTTCEALRelatorioDividaFlutuante->recuperaDepositosDividaFlutuante ($rsDepositoDividaFlutuante , $boTransacao);
$obTTCEALRelatorioDividaFlutuante->recuperaTotaisOrgao              ($rsTotalOrgao              , $boTransacao);
$obTTCEALRelatorioDividaFlutuante->recuperaRestosPagar              ($rsRestosPagar             , $boTransacao);
$obTTCEALRelatorioDividaFlutuante->recuperaBalanceteVerificacao     ($rsBalVerificacao);

$obFEmpenhoSituacaoEmpenho    = new FEmpenhoSituacaoEmpenho;
$obFEmpenhoSituacaoEmpenho->setDado( 'stEntidade'                     , $inCodEntidades);
$obFEmpenhoSituacaoEmpenho->setDado( 'exercicio'                      , Sessao::getExercicio());
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataInicialEmissao'           , $stDataInicial);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataFinalEmissao'             , $stDataFinal);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataInicialAnulacao'          , $stDataInicial);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataFinalAnulacao'            , $stDataFinal);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataInicialLiquidacao'        , $stDataInicial);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataFinalLiquidacao'          , $stDataFinal);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataInicialEstornoLiquidacao' , $stDataInicial);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataFinalEstornoLiquidacao'   , $stDataFinal);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataInicialPagamento'         , $stDataInicial);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataFinalPagamento'           , $stDataFinal);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataInicialEstornoPagamento'  , $stDataInicial);
$obFEmpenhoSituacaoEmpenho->setDado( 'stDataFinalEstornoPagamento'    , $stDataFinal);

$stFiltro = " WHERE ( aliquidar > 0 OR liquidadoapagar > 0 ) ";
$stOrder = "ORDER BY entidade, exercicio";
$obFEmpenhoSituacaoEmpenho->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

$arRestosPagar = $rsRecordSet->getElementos();
$arExercicioAtual = array();

//SOMAR TODOS OS ARRAYS
foreach($arRestosPagar as $restos) {
    $inNumCgmEntidade = SistemaLegado::pegaDado("numcgm"  , "orcamento.entidade" , " WHERE cod_entidade = ".$restos['entidade']);
    $stEntidade       = SistemaLegado::pegaDado("nom_cgm" , "sw_cgm"             , " WHERE numcgm = ".$inNumCgmEntidade);
    
    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['entidade'] = $stEntidade;
    
    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['saldo_anterior_p'] = 0;

    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['inscricao_p'] += $restos['aliquidar'];
    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['inscricao_p'] += $restos['liquidadoapagar'];

    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['restabelicimento_p'] = 0;
    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['baixa_p'] = 0;
    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['cancelamento_p'] = 0;

    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['saldo_atual_p'] += $restos['aliquidar'];
    $arExercicioAtual[$restos['entidade']][$restos['exercicio']]['saldo_atual_p'] += $restos['liquidadoapagar'];
}

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado("exercicio"  , Sessao::getExercicio());
$obTAdministracaoConfiguracao->recuperaMunicipio($rsNomMunicipio);

$arDados['exercicio']               = Sessao::getExercicio();
$arDados['municipio']               = mb_strtoupper($rsNomMunicipio->getCampo('nom_municipio'));
$arDados['data_inicial']            = $stDataInicial;
$arDados['data_final']              = $stDataFinal;
$arDados['total_restos_entidade']   = $arExercicioAtual;

$arDados['restos_pagar']            = $rsRestosPagar;
$arDados['depositos']               = $rsDepositoDividaFlutuante;
$arDados['totais_orgao']            = $rsTotalOrgao;
$arDados['totais_contas_devedoras'] = $rsTotalOrgao;

Sessao::write('arDados', $arDados);
Sessao::write('cod_entidade', $inCodEntidades);
Sessao::write('data_inicial', $stDataInicial);
Sessao::write('data_final'  , $stDataFinal);

SistemaLegado::LiberaFrames(true,true);
$stCaminho = CAM_GPC_TCEAL_RELATORIOS."OCGeraRelatorioDividaFlutuante.php";

SistemaLegado::mudaFramePrincipal($stCaminho);
?>