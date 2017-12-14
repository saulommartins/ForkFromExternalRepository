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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 22/08/2014

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    * $Id: PRManterConciliacaoCC.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacaoCC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//Função para separar a Array recebida, por Cod_Plano.
function TratarArray($arTratamento = array(), $inCodPlano, $subListagem=false)
{
    $arTratado = array();
    $inCount=0;
    
    if(!$subListagem){
        if(is_array($arTratamento)&&count($arTratamento)>0){
            foreach ($arTratamento as $key => $campo) {
                if($arTratamento[$key]['cod_plano']==$inCodPlano){
                    $arTratado[$inCount]=$campo;
                    if($arTratado[$inCount]['indices'])
                        $arTratado[$inCount]['indices']=$inCount;
                    $inCount++;
                }
            }
        }
    }else{
        if(is_array($arTratamento)&&count($arTratamento)>0){
            foreach ($arTratamento as $stChave => $arListagem) {
                foreach ($arListagem as $key => $campo) {
                    $arTratado[$stChave][$inCodPlano][$inCount]=$campo;
                    if($arTratado[$stChave][$inCodPlano][$inCount]['indices'])
                        $arTratado[$stChave][$inCodPlano][$inCount]['indices']=$inCount;
                    $inCount++;
                }
            }
        }
    }
    
    return $arTratado;
}

$filtroAux                          = Sessao::read( 'filtroAux'                         );
$arTimes                            = Sessao::read( 'arTimes'                           );
$arMovimentacao                     = Sessao::read( 'arMovimentacao'                    );
$arMovimentacaoAux                  = Sessao::read( 'arMovimentacaoAux'                 );
$arMovimentacaoPendencia            = Sessao::read( 'arMovimentacaoPendencia'           );
$arMovimentacaoPendenciaAux         = Sessao::read( 'arMovimentacaoPendenciaAux'        );
$arMovimentacaoManual               = Sessao::read( 'arMovimentacaoManual'              );
$arMovimentacaoPendenciaListagem    = Sessao::read( 'arMovimentacaoPendenciaListagem'   );
$arPendenciasMarcadas               = Sessao::read( 'arPendenciasMarcadas'              );

$arMovimentacaoTratado                  = array();
$arMovimentacaoAuxTratado               = array();
$arMovimentacaoPendenciaTratado         = array();
$arMovimentacaoPendenciaAuxTratado      = array();
$arMovimentacaoManualTratado            = array();
$arMovimentacaoPendenciaListagemTratado = array();

for($i=0;$i<count($filtroAux['arCodPlano']);$i++){
    $inCodPlano = $filtroAux['arCodPlano'][$i]['inCodPlano'];
    
    $arMovimentacaoTratado[$inCodPlano]                     = TratarArray($arMovimentacao                   , $inCodPlano, false);
    $arMovimentacaoAuxTratado[$inCodPlano]                  = TratarArray($arMovimentacaoAux                , $inCodPlano, false);
    $arMovimentacaoPendenciaTratado[$inCodPlano]            = TratarArray($arMovimentacaoPendencia          , $inCodPlano, false);
    $arMovimentacaoPendenciaAuxTratado[$inCodPlano]         = TratarArray($arMovimentacaoPendenciaAux       , $inCodPlano, false);
    $arMovimentacaoManualTratado[$inCodPlano]               = TratarArray($arMovimentacaoManual             , $inCodPlano, false);
    $arMovimentacaoPendenciaListagemTratado                 = TratarArray($arMovimentacaoPendenciaListagem  , $inCodPlano, true );
}

$arMovimentacao                     = $arMovimentacaoTratado;
$arMovimentacaoAux                  = $arMovimentacaoAuxTratado;
$arMovimentacaoPendencia            = $arMovimentacaoPendenciaTratado;
$arMovimentacaoPendenciaAux         = $arMovimentacaoPendenciaAuxTratado;
$arMovimentacaoManual               = $arMovimentacaoManualTratado;
$arMovimentacaoPendenciaListagem    = $arMovimentacaoPendenciaListagemTratado;

if (count($arPendenciasMarcadas) > 0) {
    foreach ($arPendenciasMarcadas as $stChave => $inValor) {
        $_REQUEST[$stChave] = 'on';
    }
}

for($i=0;$i<count($filtroAux['arCodPlano']);$i++){
    $inCodPlano = $filtroAux['arCodPlano'][$i]['inCodPlano'];
    $inCount=1;

    if ( count( $arMovimentacaoAux[$inCodPlano] ) > 0 ) {
        foreach ($arMovimentacaoAux[$inCodPlano] as $key => $value) {
            $arConciliar[$inCodPlano]['boConciliar_'.$inCount] = ($value['conciliar'] ) ? 'on' : NULL;
            $dt_conciliacao=explode('/',$value['dt_conciliacao']);
            if($arMovimentacaoAux[$inCodPlano][$inCount-1]['conciliar']==true&&!is_null($dt_conciliacao[1])&&$dt_conciliacao[1]!=$_REQUEST['inMes']){
                $arConciliar[$inCodPlano]['boConciliar_'.$inCount] = NULL;
            }

            $inCount++;
        }

        foreach ($arMovimentacao[$inCodPlano] as $key => $value) {
            $arIndice = explode( ',', $value['indices'] );
            if(is_array($arIndice)&&count($arIndice)>1){
                foreach ($arIndice as $inIndice) {
                    $arConciliar[$inCodPlano]["boConciliar_".$inCount] = ( isset($_REQUEST["boConciliar_".$value['id']."_".($inIndice+1)]) ) ? 'on' : NULL;
                    $inCount++;
                }
            }
        }
    }

    $arMovimentacaoPendenciaAuxSessao = $arMovimentacaoPendenciaAux;
    if ( is_array( $arMovimentacaoPendenciaAuxSessao[$inCodPlano] ) ) {
        foreach ($arMovimentacaoPendenciaAuxSessao[$inCodPlano] as $key => $value) {
            $arDataConciliacao = explode('/',$value['dt_conciliacao']);
            $stMesConciliacao = $arDataConciliacao[1];
            $arConciliar[$inCodPlano]['boPendencia_'.($value['indices']+1)] = ($value['conciliar'] AND ((integer) $stMesConciliacao == (integer) $_REQUEST['inMes'] OR $value['dt_conciliacao'] == '')) ? 'on' : '';
        }
    }
    
    $arMovimentacaoManualSessao = $arMovimentacaoManual;
    if ( is_array( $arMovimentacaoManualSessao[$inCodPlano] ) ) {
        foreach ($arMovimentacaoManualSessao[$inCodPlano] as $key => $value) {
            $arConciliar[$inCodPlano]['boManual_'.($value['indices']+1)] = ($value['conciliar'] ) ? 'on' : '';
        }
        foreach ($arMovimentacaoManualSessao[$inCodPlano] as $key => $value) {
            $arIndice = explode( ',', $value['indices'] );
            if(is_array($arIndice)&&count($arIndice)>1){
                foreach ($arIndice as $inIndice) {
                    $arConciliar[$inCodPlano]["boManual_".($inIndice+1)] = ( isset($_REQUEST["boManual_".$value['id']."_".($value['indices']+1)]) ) ? 'on' : '';
                }
            }
        }
    }
}

if ($stAcao == "incluir") {
    for($i=0;$i<count($filtroAux['arCodPlano']);$i++){
        $inCodPlano = $filtroAux['arCodPlano'][$i]['inCodPlano'];

        $obRTesourariaConciliacao = new RTesourariaConciliacao();
        $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano  ( $inCodPlano               );
        $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio ( $_REQUEST['stExercicio']  );
        $obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obRTesourariaConciliacao->setMes                   ( intval($_REQUEST['inMes'])                );
        $obRTesourariaConciliacao->setDataExtrato           ( $_REQUEST['stDtExtrato']                  );
        $obRTesourariaConciliacao->setValorExtrato          ( $_REQUEST['nuSaldoExtrato']               );
        $obRTesourariaConciliacao->setTimestampConciliacao  ( $arTimes[$inCodPlano]                     );
    
        $obRTesourariaConciliacao->setMovimentacao        ( $arMovimentacaoAux[$inCodPlano]             );
        $obRTesourariaConciliacao->setMovimentacaoPendente( $arMovimentacaoPendenciaAux[$inCodPlano]    );
        $obRTesourariaConciliacao->setMovimentacaoManual  ( $arMovimentacaoManual[$inCodPlano]          );

        for ($x=1; $x<=3; $x++) {
            if ($_REQUEST["inNumCgm".$x]) {
                $obRTesourariaConciliacao->addAssinatura();
                $obRTesourariaConciliacao->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
                $obRTesourariaConciliacao->roUltimaAssinatura->obRCGM->setNumCGM( $_REQUEST["inNumCgm".$x]      );
                $obRTesourariaConciliacao->roUltimaAssinatura->setExercicio     ( $_REQUEST['stExercicio']      );
                $obRTesourariaConciliacao->roUltimaAssinatura->setTipo          ( 'CO'                          );
                $obRTesourariaConciliacao->roUltimaAssinatura->setCargo         ( $_REQUEST["stCargo".$x]       );
                $obRTesourariaConciliacao->roUltimaAssinatura->setNumMatricula  ( $_REQUEST["inMatricula".$x]   );
                $obRTesourariaConciliacao->roUltimaAssinatura->setSituacao      ( true                          );
            }
        }

        $obErro = $obRTesourariaConciliacao->salvarMovimentacoes( $arConciliar[$inCodPlano] );
    }

    if (!$obErro->ocorreu()) {
        $filtro = Sessao::read('filtro');
        $filtro['arCodPlano'] = $filtroAux['arCodPlano'];
        Sessao::write('filtroGeraRel', $filtro);

        $arvoltaBusca = Sessao::read('voltaBusca');

        if($arvoltaBusca!="")
            SistemaLegado::alertaAviso($arvoltaBusca,'Conta Corrente '.$_REQUEST['stCC'],'incluir','aviso',Sessao::getId(),'../');
        else
            SistemaLegado::alertaAviso($pgFilt,'Conta Corrente '.$_REQUEST['stCC'],'incluir','aviso',Sessao::getId(),'../');

        $stCaminho = CAM_GF_TES_INSTANCIAS."conciliacao/OCRelatorioConciliacaoCC.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodPlano=".$_REQUEST['inCodPlano']."&stExercicio=".$_REQUEST['stExercicio'];
        $stCampos .= "&inMes=".$_REQUEST['inMes']."&inCodEntidade=".$_REQUEST['inCodEntidade']."&nuSaldoTesouraria=".$_REQUEST['nuSaldoTesouraria'];
        $stCampos .= "&boAgrupar=".Sessao::read('boAgrupar');
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
}

?>
