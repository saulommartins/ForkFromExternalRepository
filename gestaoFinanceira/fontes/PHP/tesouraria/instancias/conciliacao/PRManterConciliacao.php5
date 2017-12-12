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
    * Data de Criação   : 13/02/2006

    
    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore
    
    $Revision: 12203 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.19
*/

/*
$Log$
Revision 1.2  2006/07/05 20:41:18  cleisson
Adicionada tag Log aos arquivos

*/

include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php"             );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php"     );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

if($stAcao=="incluir"){
    $obRTesourariaConciliacao = new RTesourariaConciliacao();
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio'] );
    $obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRTesourariaConciliacao->setMes(intval($_REQUEST['inMes']));
    $obRTesourariaConciliacao->setDataExtrato( $_REQUEST['stDtExtrato'] );
    $obRTesourariaConciliacao->setValorExtrato( $_REQUEST['nuSaldoExtrato'] );
    $obRTesourariaConciliacao->setTimestampConciliacao( $_REQUEST['stTimestampConciliacao'] );

    if( count( Sessao::read('arMovimentacaoAux') ) > 0 ) {
        foreach( Sessao::read('arMovimentacaoAux') as $key => $arMovimentacaoAux ) {
            $arConciliar['boConciliar_'.($arMovimentacaoAux['indices']+1)] = ($arMovimentacaoAux['conciliar'] ) ? 'on' : '';
        }
        foreach( Sessao::read('arMovimentacao') as $key => $arMovimentacao ) {
            $arIndice = explode( ',', $arMovimentacao['indices'] );
            foreach( $arIndice as $inIndice ) {
                $arConciliar["boConciliar_".($inIndice+1)] = $_REQUEST["boConciliar_".$arMovimentacao['id']."_".($key+1)];
            }
        }
    }
    if( count( Sessao::read('arMovimentacaoPendenciaAux') ) > 0 ) {
        foreach( Sessao::read('arMovimentacaoPendenciaAux') as $key => $arMovimentacaoAux ) {
            $arConciliar['boPendencia_'.($arMovimentacaoAux['indices']+1)] = ($arMovimentacaoAux['conciliar'] ) ? 'on' : '';
        }
        foreach( Sessao::read('arMovimentacaoPendencia') as $key => $arMovimentacao ) {
            $arIndice = explode( ',', $arMovimentacao['indices'] );
            foreach( $arIndice as $inIndice ) {
                $arConciliar["boPendencia_".($inIndice+1)] = $_REQUEST["boPendencia_".$arMovimentacao['id']."_".($key+1)];
            }
        }
    }

    $arMovimentacaoFinal = Sessao::read('arMovimentacaoAux');
    $arMovimentacaoPendenciaFinal = Sessao::read('arMovimentacaoPendenciaAux');

    if( !is_array($arMovimentacaoFinal) ) $arMovimentacaoFinal = array();
    if( !is_array($arMovimentacaoPendenciaFinal) ) $arMovimentacaoPendenciaFinal = array();

    $inCount=0;
    foreach( $arMovimentacaoFinal as $key=>$arMovimentacao ) {
        if( $arConciliar["boConciliar_".($key+1)]=="on" ) {
            if( $arMovimentacao['tipo'] == 'A' ) {
                $stTipoValor = ( strstr( $arMovimentacao['descricao'], "Estorno de Arrecadação" ) ) ? 'C' : 'D';
                $obRTesourariaConciliacao->addLancamentoArrecadacao();
                $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->setCodPlano ( $arMovimentacao['cod_plano'] );
                $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio']     );
                $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->setCodArrecadacao      ( $arMovimentacao['cod_arrecadacao']       );
                $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->setTimestampArrecadacao( $arMovimentacao['timestamp_arrecadacao'] );
                $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->setTipoValor( $stTipoValor            );
                $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->setTipo     ( $arMovimentacao['tipo_arrecadacao'] );
            } else {
                $obRTesourariaConciliacao->addLancamentoContabil();
                $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($arMovimentacao['cod_lote']);
                $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($arMovimentacao['tipo']       );
                $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia($arMovimentacao['sequencia']                   ); 
                $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $arMovimentacao['cod_entidade']  );                                                
                $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->setTipoValor( $arMovimentacao['tipo_valor']                                            );
            }
        }
    }
    foreach( $arMovimentacaoPendenciaFinal as $key=>$arMovimentacao ) {
        if( $arMovimentacao['tipo_movimentacao'] == 'A' ) {
            if($arConciliar["boPendencia_".($key+1)]=="on" ){
                if( $arMovimentacao['tipo'] == 'A' ) {
                    $stTipoValor = ( strstr( $arMovimentacao['descricao'], "Estorno de Arrecadação" ) ) ? 'C' : 'D';
                    $obRTesourariaConciliacao->addLancamentoArrecadacao();
                    $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->setCodPlano ( $arMovimentacao['cod_plano'] );
                    $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio']     );
                    $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->setCodArrecadacao      ( $arMovimentacao['cod_arrecadacao']       );
                    $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->obRTesourariaArrecadacao->setTimestampArrecadacao( $arMovimentacao['timestamp_arrecadacao'] );
                    $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->setTipoValor( $stTipoValor            );
                    $obRTesourariaConciliacao->roUltimoLancamentoArrecadacao->setTipo     ( $arMovimentacao['tipo_arrecadacao'] );
                } else {
                    $obRTesourariaConciliacao->addLancamentoContabil();
                    $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote($arMovimentacao['cod_lote']);
                    $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                    $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo($arMovimentacao['tipo']       );
                    $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia($arMovimentacao['sequencia']                   ); 
                    $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $arMovimentacao['cod_entidade']  );                                                
                    $obRTesourariaConciliacao->roUltimoLancamentoContabil->obRContabilidadeLancamentoValor->setTipoValor( $arMovimentacao['tipo_valor']                                            );
                }
            }
        }elseif( $arMovimentacao['tipo_movimentacao'] == 'M' ) {
            if( $arConciliar["boPendencia_".($key+1)]=="on") {
                $obRTesourariaConciliacaoAux = new RTesourariaConciliacao();
                $obRTesourariaConciliacaoAux->obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodPlano'] );
                $obRTesourariaConciliacaoAux->obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio'] );
                $obRTesourariaConciliacaoAux->setMes(intval(substr($arMovimentacao['dt_lancamento'],3,2)));
                $obRTesourariaConciliacaoAux->addLancamentoManual();
                $obRTesourariaConciliacaoAux->roUltimoLancamentoManual->setSequencia($arMovimentacao['sequencia']);
                $obRTesourariaConciliacaoAux->roUltimoLancamentoManual->excluir();
            }
        }
    }

    $inCount=0;
    foreach( Sessao::read('arMovimentacaoManual') as $key=>$arMovimentacao ) {
        if($_REQUEST["boManual_".$arMovimentacao['id']."_".($key+1)]!="on" ) { 
            $obRTesourariaConciliacao->addLancamentoManual();
            $obRTesourariaConciliacao->roUltimoLancamentoManual->setDataLancamento($_REQUEST['stDtMovimentacao']);
            $obRTesourariaConciliacao->roUltimoLancamentoManual->setTipoValor($arMovimentacao['tipo_valor']);
            $obRTesourariaConciliacao->roUltimoLancamentoManual->setValorLancamento($arMovimentacao['vl_lancamento']);
            $obRTesourariaConciliacao->roUltimoLancamentoManual->setDescricao($arMovimentacao['descricao']);
        }
    }

    for ($x=1; $x<=3; $x++) {
        if ($_REQUEST["inNumCgm".$x]) {
            $obRTesourariaConciliacao->addAssinatura();
            $obRTesourariaConciliacao->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
            $obRTesourariaConciliacao->roUltimaAssinatura->obRCGM->setNumCGM($_REQUEST["inNumCgm".$x]);
            $obRTesourariaConciliacao->roUltimaAssinatura->setExercicio($_REQUEST['stExercicio']);
            $obRTesourariaConciliacao->roUltimaAssinatura->setTipo('CO');
            $obRTesourariaConciliacao->roUltimaAssinatura->setCargo($_REQUEST["stCargo".$x]);
            $obRTesourariaConciliacao->roUltimaAssinatura->setNumMatricula($_REQUEST["inMatricula".$x]);
            $obRTesourariaConciliacao->roUltimaAssinatura->setSituacao(true);
        }
    }

    $obErro = $obRTesourariaConciliacao->salvar( $boTransacao );
    if ( !$obErro->ocorreu() ){
        SistemaLegado::alertaAviso($pgFilt,'','incluir','aviso',Sessao::getId(),'../');
        $stCaminho = CAM_GF_TES_INSTANCIAS."conciliacao/OCRelatorioConciliacao.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodPlano=".$_REQUEST['inCodPlano']."&stExercicio=".$_REQUEST['stExercicio'];
        $stCampos .= "&inMes=".$_REQUEST['inMes']."&inCodEntidade=".$_REQUEST['inCodEntidade']."&nuSaldoTesouraria=".$_REQUEST['nuSaldoTesouraria'];
        $stCampos .= "&boAgrupar=".Sessao::read('boAgrupar');
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );

    }else{
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
}

?>
