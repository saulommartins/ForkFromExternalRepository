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
    * Página de Processamento de Anulação de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31087 $
    $Name$
    $Autor:$
    $Date: 2008-01-04 12:27:45 -0200 (Sex, 04 Jan 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.15
*/

/*
$Log$
Revision 1.10  2007/02/22 23:47:26  cleisson
Bug #7905#

Revision 1.9  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AnularEmpenho";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

$obTransacao = new Transacao();
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obAtributos = new MontaAtributos;
$obAtributos->setName      ( "Atributo_" );
$obAtributos->recuperaVetor( $arChave    );

//Trecho de código do filtro
$stFiltro = '';
if ($stAcao != 'incluir') {
    if ( Sessao::read('filtro') ) {
        $arFiltro = Sessao::read('filtro');
        $stFiltro = '';
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
        }
        $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
    }
}

//valida a utilização da rotina de encerramento do mês contábil
$arDtAutorizacao = explode('/', $_POST['stDtAnulacao']);
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9, $boTransacao);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ', $boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
    SistemaLegado::LiberaFrames(true,False);
    SistemaLegado::exibeAviso(urlencode("Mês da Anulação encerrado!"),"n_incluir","erro");
    exit;
}

switch ($stAcao) {
    case "anular":
    SistemaLegado::LiberaFrames(true,False);
    Sessao::setTrataExcecao(true);
    $obErro = new Erro;
    foreach ($_POST as $stChave => $stValor) {
        if ( strstr( $stChave, "nuValor" ) ) {
            $stValor = str_replace('.','',$stValor);
            $stValor = str_replace(',','.',$stValor);
            if ($stValor > 0) {
                list($var,$inNumItem,$inSeq) = explode( '_',$stChave );
                $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->addItemPreEmpenho();
                $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->roUltimoItemPreEmpenho->setNumItem( $inNumItem );
                $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->roUltimoItemPreEmpenho->setValorEmpenhadoAnulado( $stValor );
            }
        }
    }
    //ATRIBUTOS
    foreach ($arChave as $key=>$value) {
        $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
        $inCodAtributo = $arChaves[0];
        if ( is_array($value) ) {
            $value = implode(",",$value);
        }
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
    }

    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $_POST['inCodPreEmpenho'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $_POST['inCodEmpenho'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtEmpenho( $_POST['stDtEmpenho'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $_POST['inCodDespesa'] );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicioEmissao( Sessao::getExercicio() );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setMotivo( $_POST['stMotivo'] );
    include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );
    $obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao($obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho);
    $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
    $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho($_REQUEST['inCodEmpenho']);
    $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio ($_REQUEST['stDtExercicioEmpenho']);
    $obREmpenhoNotaLiquidacao->listarMaiorDataAnulacaoEmpenho($rsMaiorData, '', $boTransacao);

    if ( !$rsMaiorData->getCampo("dataanulacao") ) {
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade'] );
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( Sessao::getExercicio());
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarMaiorDataAnulacao( $rsMaiorData, '', $boTransacao );
    }

    if ( $rsMaiorData->getCampo("dataanulacao") && strlen($rsMaiorData->getCampo("dataanulacao")) > 0 ) {
        $anoUltimaAnulacao = (int) substr($rsMaiorData->getCampo("dataanulacao"), 6, strlen($rsMaiorData->getCampo("dataanulacao")));
        if ($anoUltimaAnulacao > Sessao::getExercicio()) {
            $stMaiorData = '31/12/' . Sessao::getExercicio();
        } elseif ($anoUltimaAnulacao < Sessao::getExercicio()) {
            $stMaiorData = '01/01/' . Sessao::getExercicio();
        } else {
            $stMaiorData = $rsMaiorData->getCampo("dataanulacao");
        }
    } else {
        $stMaiorData = '01/01/'.Sessao::getExercicio();
    }

    //if (SistemaLegado::comparaDatas($rsMaiorData->getCampo("dataanulacao"),$_POST['stDtAnulacao'])) {
    if (SistemaLegado::comparaDatas($stMaiorData, $_POST['stDtAnulacao'])) {
        //$obErro->setDescricao("A data de anulação deve ser maior ou igual a ". $rsMaiorData->getCampo("dataanulacao"));
        $obErro->setDescricao("A data de anulação deve ser maior ou igual a ". $stMaiorData);
    }
   
    if ( !$obErro->ocorreu() ) {
        if (isset($_REQUEST['inRestos'])) {
            Sessao::write('inRestos', $_REQUEST['inRestos']);
            //Verifica se as contas estão configuradas, se nao estiver nao conclui a anulação
            if ( $_REQUEST['inRestos'] == 0) {
                $arContasLancamentoAnulacao = array('6.3.1.1','6.3.1.9.1');
            }elseif ($_REQUEST['inRestos'] == 1){
                $arContasLancamentoAnulacao = array('6.3.1.1','6.3.1.9.1', '4.6.4.0.1.00' );
            }elseif ($_REQUEST['inRestos'] == 2){
                $arContasLancamentoAnulacao = array('4.6.4.0.1.00' , '6.3.2.1', '6.3.2.9.9' );
            }
            foreach($arContasLancamentoAnulacao AS $arConta ){
                include_once ( CAM_GF_EMP_MAPEAMENTO     ."FEmpenhoEmpenhoEstornoRestosAPagar.class.php" );
                $obFEmpenhoEmpenhoEstornoRestosAPagar =  new FEmpenhoEmpenhoEstornoRestosAPagar;
                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'exercicio'        , Sessao::getExercicio() );
                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'cod_estrutural'   , $arConta );
                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'exercicio_empenho', $_REQUEST['stDtExercicioEmpenho'] );
                $obFEmpenhoEmpenhoEstornoRestosAPagar->setDado( 'cod_empenho_resto', $_POST['inCodEmpenho'] );
                $obFEmpenhoEmpenhoEstornoRestosAPagar->verificaConta($rsRecordset, $boTransacao);
                if($rsRecordset->getNumLinhas() < 0){
                    $obFEmpenhoEmpenhoEstornoRestosAPagar->buscaContaComMascara($rsRecordset, $boTransacao);
                    $obErro->setDescricao("A conta ".$rsRecordset->getCampo('fn_mascara_completa')." não é analítica ou não está cadastrada!");
                }
            }
        }
        if ( !$obErro->ocorreu() ) {
            $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setDtAnulacao( $_POST['stDtAnulacao'] );
            $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $_POST['stDtExercicioEmpenho'] );
            $obErro = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->anular($boTransacao);
        }
    }

    $obTransacao->fechaTransacao($boFlagTransacao,$boTransacao,$obErro,$obREmpenhoEmpenhoAutorizacao->obTEmpenhoEmpenhoAutorizacao);

    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId().$stFiltro, $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho()."/".$_POST['stDtExercicioEmpenho'], "incluir", "aviso", Sessao::getId(), "../");
        $stCaminho = CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioEmpenhoOrcamentarioAnulado.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodEmpenho=".$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodEmpenho(). "&inCodEntidade=" .$_POST['inCodEntidade']."&stDtExercicioEmpenho=".$_POST['stDtExercicioEmpenho']."&boImplantado=".$_POST['boImplantado']."&timestamp=".$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtAnulacao();
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
    SistemaLegado::LiberaFrames(true,False);
    
    Sessao::encerraExcecao();
    break;
}
?>
