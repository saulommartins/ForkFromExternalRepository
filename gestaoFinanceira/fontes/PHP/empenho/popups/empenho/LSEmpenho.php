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
    * Página de Listagem para popup de Empenho
    * Data de Criação   : 30/03/2005

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: LSEmpenho.php 60868 2014-11-19 18:18:01Z evandro $

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.03.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Empenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$pgCons = $pgFilt;

include_once( $pgJS );

if ( empty($obRempenho) ) {
    $obRempenho = "";
}

$obRegra = new REmpenhoOrdemPagamento( $obRempenho );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'baixar'   : $pgProx = $pgBaix; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'prorrogar': $pgProx = $pgCons; break;
    case 'consultar': $pgProx = $pgCons; break;
    DEFAULT         : $pgProx = $pgForm;
}

$arLinkPopUp = Sessao::read('linkPopUp');
//Monta sessao com os valores do filtro
if ( is_array($arLinkPopUp) ) {
    $_REQUEST = $arLinkPopUp;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp', $arLinkPopUp);
}

switch ($_REQUEST['tipoBusca']) {

    case 'empenhoComplementar':
        include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
        $obREmpenhoEmpenho = new REmpenhoEmpenho;
        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"] );
        $obREmpenhoEmpenho->setExercicio  ( Sessao::getExercicio() );
        $obREmpenhoEmpenho->setCodEmpenhoInicial ( $_REQUEST["inCodEmpenhoInicial"]    );
        $obREmpenhoEmpenho->setCodEmpenhoFinal ( $_REQUEST["inCodEmpenhoFinal"]    );
        $obREmpenhoEmpenho->setSituacao   ( 5                               );
        $obREmpenhoEmpenho->stDtEmpenhoInicial = $_REQUEST['stDtInicial'];
        $obREmpenhoEmpenho->stDtEmpenhoFinal   = $_REQUEST['stDtFinal'];
        $obREmpenhoEmpenho->listar($rsLista);
        $stBeneficiario = 'nom_fornecedor';
    break;

    case 'obra_tcmgo':

        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado( 'cod_entidade'        , $_REQUEST["inCodigoEntidade"   ] );
        $obTEmpenhoEmpenho->setDado( 'exercicio'           , $_REQUEST["stExercicio"        ] );
        $obTEmpenhoEmpenho->setDado( 'cod_empenho_ini'     , $_REQUEST['inCodEmpenhoInicial'] );
        $obTEmpenhoEmpenho->setDado( 'cod_empenho_fim'     , $_REQUEST['inCodEmpenhoFinal'  ] );
        $obTEmpenhoEmpenho->setDado( 'dt_empenho_ini'      , $_REQUEST['stDtInicial'        ] );
        $obTEmpenhoEmpenho->setDado( 'dt_empenho_fim'      , $_REQUEST['stDtFinal'          ] );
        $obTEmpenhoEmpenho->setDado( 'cod_estrutural'      , '4.4.9.0.51'                     );
        $obTEmpenhoEmpenho->recuperaEmpenhoObra ($rsLista);
        $stBeneficiario = '';

        $stBeneficiario = 'nom_fornecedor';

    break;

    case 'empenhoNotaFiscal':

        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenho.class.php';
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado('cod_entidade'   , $_REQUEST['inCodEntidade']);
        $obTEmpenhoEmpenho->setDado('exercicio'      , $_REQUEST['stExercicioEmpenho']);
        $obTEmpenhoEmpenho->setDado('cod_empenho_ini', $_REQUEST['inCodEmpenhoInicial']);
        $obTEmpenhoEmpenho->setDado('cod_empenho_fim', $_REQUEST['inCodEmpenhoFinal']);
        $obTEmpenhoEmpenho->setDado('dt_emissao'     , $_REQUEST['stDtInicial']);
        $obTEmpenhoEmpenho->setDado('dt_final'       , $_REQUEST['stDtFinal']);
        $obTEmpenhoEmpenho->recuperaEmpenhoLiquidacaoNotaFiscal ($rsLista);

        $stBeneficiario = 'credor';

    break;

    case "buscaEmpenhoNota":

            $rsLista = new RecordSet;

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'exercicio'    , $_REQUEST['stExercicioEmpenho']     );
            $obTEmpenhoEmpenho->setDado( 'cod_empenho'  , $_REQUEST['numEmpenho']      );
            $obTEmpenhoEmpenho->setDado( 'dt_emissao'   , $_REQUEST['stDtInicial']            );
            $obTEmpenhoEmpenho->setDado( 'dt_final'     , $_REQUEST['stDtFinal']              );
            $obTEmpenhoEmpenho->recuperaEmpenhoBuscaInner($rsLista);
            $stBeneficiario = '';

            $stBeneficiario = 'credor';

    break;

    default:
       if ( empty($stLink) ) {
            $stLink = "";
       }

       if ($_REQUEST['stExercicioEmpenho']) {
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['stExercicioEmpenho'] );
            $stLink .= '&stExercicioEmpenho='.$_REQUEST['stExercicioEmpenho'];
        }
        if ($_REQUEST['inCodEntidade']) {
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']);
            $stLink .= '&inCodigoEntidade='.$_REQUEST['inCodEntidade'];
        }
        if ($_REQUEST['inCodEmpenhoInicial']) {
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );
            $stLink .= '&inCodEmpenhoInicial='.$_REQUEST['inCodEmpenhoInicial'];
        }
        if ($_REQUEST['inCodEmpenhoFinal']) {
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );
            $stLink .= '&inCodEmpenhoFinal='.$_REQUEST['inCodEmpenhoFinal'];
        }
        if ($_REQUEST['stDtInicial']) {
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setDtEmpenhoInicial( $_REQUEST['stDtInicial'] );
            $stLink .= '&stDtInicial='.$_REQUEST['stDtInicial'];
        }
        if ($_REQUEST['stDtFinal']) {
            $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setDtEmpenhoFinal( $_REQUEST['stDtFinal'] );
            $stLink .= '&stDtFinal='.$_REQUEST['stDtFinal'];
        }

        $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado );
        if ($boImplantado) {
            $obRegra->obREmpenhoNotaLiquidacao->listarNotasDisponiveisImplantadas( $rsLista );
        } else {
            $obRegra->obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsLista );
        }
        $stBeneficiario = 'beneficiario';
    break;

    case 'buscaTodosEmpenhos':
        include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenho.class.php';
        $obREmpenhoEmpenho = new REmpenhoEmpenho;

        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade ( TRIM($_REQUEST["inCodEntidade"]));
        $obREmpenhoEmpenho->setExercicio                            ( $_REQUEST["stExercicioEmpenho"] );

        if ($_REQUEST['stDtInicial'])
            $obREmpenhoEmpenho->setDtEmpenhoInicial( $_REQUEST['stDtInicial'] );

        if ($_REQUEST['stDtFinal'])
            $obREmpenhoEmpenho->setDtEmpenhoFinal( $_REQUEST['stDtFinal'] );

        if ($_REQUEST['inCodEmpenhoInicial'])
            $obREmpenhoEmpenho->setCodEmpenhoInicial( $_REQUEST['inCodEmpenhoInicial'] );

        if ($_REQUEST['inCodEmpenhoFinal']) 
            $obREmpenhoEmpenho->setCodEmpenhoFinal( $_REQUEST['inCodEmpenhoFinal'] );

        $obRegra->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado );
        if ($boImplantado) {
            
        }else{

        }
        $obREmpenhoEmpenho->listarEmpenhosPopUp( $rsLista, $stOrder, $boTransacao );
        $stBeneficiario = 'nom_fornecedor';

    break;
}

isset($stLink) ? $stLink : ($stLink = '');

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&nomForm=".$_REQUEST['nomForm'];
$stLink .= "&campoNum=".$_REQUEST['campoNum'];
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&tipoBusca=".$_REQUEST['tipoBusca'];

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Empenho");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Empenho");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Fornecedor");
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();
if ($_REQUEST['tipoBusca'] != 'buscaTodosEmpenhos') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Recurso");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio_empenho]" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_empenho" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( $stBeneficiario );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
if ($_REQUEST['tipoBusca'] != 'buscaTodosEmpenhos') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_recurso" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
}

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );

if ($_REQUEST['tipoBusca'] == 'obra_tcmgo') {
    $obLista->ultimaAcao->setLink( "JavaScript:insereEmpenhoExercicio();" );
    $obLista->ultimaAcao->addCampo("1","cod_empenho"       );
    $obLista->ultimaAcao->addCampo("2", $stBeneficiario    );
    $obLista->ultimaAcao->addCampo("3","exercicio_empenho" );
} else {
    $obLista->ultimaAcao->setLink( "JavaScript:insere();" );
    $obLista->ultimaAcao->addCampo("1","cod_empenho");
    $obLista->ultimaAcao->addCampo("2", $stBeneficiario);
}

$obLista->commitAcao();

$obLista->show();

?>
