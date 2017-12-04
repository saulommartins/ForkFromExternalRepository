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
    * Página de Formulário para Conciliação Bancária
    * Data de Criação   : 06/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barbozainclude_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBATipoConciliacao.class.php";

    * @ignore

    * $Id: FMManterConciliacao.php 65436 2016-05-20 20:27:14Z carlos.silva $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";
include_once CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php";
include_once CAM_GPC_TCMBA_MAPEAMENTO."/TTCMBATipoConciliacao.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('arMovimentacao');
Sessao::remove('arMovimentacaoAux');
Sessao::remove('arMovimentacaoPendencia');
Sessao::remove('arMovimentacaoPendenciaAux');
Sessao::remove('arMovimentacaoManual');
Sessao::remove('arMovimentacaoPendenciaListagem');
Sessao::remove('arPendenciasMarcadas');
Sessao::remove('arMovimentacaoAuxSessao');

$arFiltro = Sessao::read('filtro');

$mes = $arFiltro['inMes'];
$meses =  array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
$obRTesourariaConfiguracao->consultarTesouraria();
Sessao::write('boAgrupar', $_REQUEST['boAgrupar']);
Sessao::write('stDtInicial',$arFiltro['stDataInicial']);
Sessao::write('stDtFinal',$_REQUEST['stDtExtrato']);
$obRTesourariaConciliacao = new RTesourariaConciliacao();
$obRTesourariaConciliacao->setDataInicial   ($arFiltro['stDataInicial']);
$obRTesourariaConciliacao->setDataFinal     ($_REQUEST['stDtExtrato'] );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio($_REQUEST['stExercicio']);
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlano($_REQUEST['inCodPlano']);
$obRTesourariaConciliacao->setMes           (intval($arFiltro['inMes']));
$obRTesourariaConciliacao->listar           ($rsRecordSet);

$obRTesourariaConciliacao->obRTesourariaAssinatura->setExercicio($_REQUEST['stExercicio']);
$obRTesourariaConciliacao->obRTesourariaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
$obRTesourariaConciliacao->obRTesourariaAssinatura->setTipo('CO');

$obRTesourariaConciliacao->obRTesourariaAssinatura->listar($rsRecordSetAssinatura);

$obRTesourariaConciliacao->listarMovimentacao($rsLista);
$obRTesourariaConciliacao->listarMovimentacaoPendente($rsListaPendencia);

$obRTesourariaConciliacao->addLancamentoManual();
$obRTesourariaConciliacao->roUltimoLancamentoManual->listar( $rsListaManual );

$obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio'] );
$obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setCodPlano ( $_REQUEST['inCodPlano']  );
$obRTesourariaConciliacao->obRTesourariaSaldoTesouraria->consultarSaldoTesouraria( $nuSaldoTesouraria, '01/01/'.$_REQUEST['stExercicio'], $_REQUEST['stDtExtrato'] );

$arMovimentacaoAuxSessao = array();
$arMovimentacaoAuxSessao = ( !$rsLista->eof() ) ? $rsLista->getElementos() : array();
sort($arMovimentacaoAuxSessao);
Sessao::write('arMovimentacaoAuxSessao', $arMovimentacaoAuxSessao);
unset( $rsLista );

$inCount = 0;
for ( $x = 0; $x<count($arMovimentacaoAuxSessao); $x++ ) {
    $arMovimentacaoAuxSessao[$x]['indices'] = $x;
    if ( $obRTesourariaConfiguracao->getOcultarMovimentacoes() ) {
        if ($arMovimentacaoAuxSessao[$x]['conciliar']) {
            continue;
        } else {
            foreach ($arMovimentacaoAuxSessao[$x] AS $key => $value) {
                $arMovimentacaoAux[$inCount][$key] = $value;
            }
            $inCount++;
        }
    }
    
    if (!$arMovimentacaoAuxSessao[$x]['conciliar']) {
        $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, $arMovimentacaoAuxSessao[$x]['vl_lancamento'], 4);
    } else {
        if (substr($arMovimentacaoAuxSessao[$x]['dt_conciliacao'],3,2) != $mes) {
            $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoAuxSessao[$x]['vl_lancamento']), 4 );
        }
    }
}

$arMovimentacaoSessao = array();
$arMovimentacaoSessao = ( $obRTesourariaConfiguracao->getOcultarMovimentacoes() ) ? $arMovimentacaoAux : $arMovimentacaoAuxSessao;

$rsListaPendencia->addFormatacao('vl_lancamento','NUMERIC_BR');
$rsListaPendencia->setPrimeiroElemento();
$arMovimentacaoPendenciaAuxSessao = array();
$arMovimentacaoPendenciaAuxSessao = ( !$rsListaPendencia->eof() ) ? $rsListaPendencia->getElementos() : array();

unset( $arMovimentacaoAux );
unset( $rsListaPendencia );

$inCount = 0;
$arPendenciaListagemAux = array();
for ( $x = 0; $x<count( $arMovimentacaoPendenciaAuxSessao ); $x++ ) {
    $arMovimentacaoPendenciaAuxSessao[$x]['indices'] = $x;

    // Se a ordem é vazia, então é porque é uma pendencia vinda das novas movimentações
    if (trim($arMovimentacaoPendenciaAuxSessao[$x]['ordem']) == "") {
        // tipo == C (entrada) | tipo == D (saida)
        if ($arMovimentacaoPendenciaAuxSessao[$x]['tipo_valor'] == 'C') {
            $stChave = 'entradaTesouraria';
        } else {
            $stChave = 'saidaTesouraria';
        }
    // se não é uma movimentacao corrente do mes passado
    } else {
        if ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento'] < 0) {
            $stChave = 'entradaBanco';
        } else {
            $stChave = 'saidaBanco';
        }
    }

    $inCount = count($arPendenciaListagemAux[$stChave]);
    foreach ($arMovimentacaoPendenciaAuxSessao[$x] AS $key => $value) {
        $arPendenciaAux[$inCount][$key] = $value;
        $arPendenciaListagemAux[$stChave][$inCount][$key] = $value;
    }

    if (!$arMovimentacaoPendenciaAuxSessao[$x]['conciliar']) {
        $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento']*(-1)), 4 );
    } else {
        if (substr($arMovimentacaoPendenciaAuxSessao[$x]['dt_conciliacao'],3,2) != $mes) {
            $nuSaldoContabilConciliado = bcadd( $nuSaldoContabilConciliado, ($arMovimentacaoPendenciaAuxSessao[$x]['vl_lancamento']*(-1)), 4 );
        }
    }
}

$arMovimentacaoPendenciaSessao = ( is_array($arPendenciaAux) ) ? $arPendenciaAux : array();

$rsListaManual->addFormatacao('vl_lancamento', 'NUMERIC_BR');
$arMovimentacaoManualSessao = array();
$arMovimentacaoManualSessao = ( !$rsListaManual->eof() ) ? $rsListaManual->getElementos() : array();
for ( $x = 0; $x<count( $arMovimentacaoManualSessao ); $x++ ) {
    $inSequencia = $arMovimentacaoManualSessao[$x]['sequencia'] - 1;
    $arMovimentacaoManualSessao[$x]['id'] = 'M'.$inSequencia;
    if ($arMovimentacaoManualSessao[$x]['conciliado'] == 't') {
        $arMovimentacaoManualSessao[$x]['conciliar'] = true;
        if (substr($arMovimentacaoManualSessao[$x]['dt_conciliacao'],3,2) != $mes) {
            $nuSaldoContabilConciliado = bcsub( $nuSaldoContabilConciliado, $arMovimentacaoManualSessao[$x]['vl_lancamento'], 4 );
        }
    } else {
        $arMovimentacaoManualSessao[$x]['conciliar'] = false;
        $nuSaldoContabilConciliado = bcsub( $nuSaldoContabilConciliado, $arMovimentacaoManualSessao[$x]['vl_lancamento'], 4 );
    }
}

Sessao::write('arMovimentacao', $arMovimentacao);
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

sort($arMovimentacaoSessao);
sort($arMovimentacaoPendenciaSessao);

// Agrupa bordero e receita
if ($_REQUEST['boAgrupar']) {
    $inCount = 0;
    for ( $x = 0 ; $x < count( $arMovimentacaoSessao ); $x++ ) {
        foreach ($arMovimentacaoSessao[$x] as $key => $value) {
            $arMovAgrupada[$inCount][$key] = $value;
        }
        // Agrupa bordero
        if ($arMovimentacaoSessao[$x]['cod_bordero']) {
            $inCodBordero = $arMovimentacaoSessao[$x]['cod_bordero'];
            $nuVlMovConciliada = 0;
            $nuVlMovNConciliada = 0;
            $stIndiceMovCinciliada  = "";
            $stIndiceMovNCinciliada = "";
            while ($arMovimentacaoSessao[$x]['cod_bordero'] == $inCodBordero) {
                if ($arMovimentacaoSessao[$x]['conciliar']) {
                    $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacaoSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovCinciliada .= $arMovimentacaoSessao[$x]['indices'].",";
                } else {
                    $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacaoSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovNCinciliada .= $arMovimentacaoSessao[$x]['indices'].",";
                }
                $x++;
            }
            $x--;
            if ($nuVlMovConciliada != 0) {
                if( $nuVlMovConciliada >= 0 )
                    $stDescricao = "Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                else
                    $stDescricao = "Estorno de Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = true;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                $inCount++;
            }
            if ($nuVlMovNConciliada != 0) {
                foreach ($arMovimentacaoSessao[$x] as $key => $value) {
                    $arMovAgrupada[$inCount][$key] = $value;
                }
                if( $nuVlNMovConciliada >= 0 )
                    $stDescricao = "Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                else
                    $stDescricao = "Estorno de Pagamento de Empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = false;
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                $inCount++;
            }
            $inCount--;
        }
        // Agrupa Arrecadacao
        if ($arMovimentacaoSessao[$x]['cod_receita']) {
            $inCodReceita = $arMovimentacaoSessao[$x]['cod_receita'];
            $nuVlMovConciliada  = 0;
            $nuVlMovNConciliada = 0;
            $stIndiceMovCinciliada  = "";
            $stIndiceMovNCinciliada = "";
            while ($arMovimentacaoSessao[$x]['cod_receita'] == $inCodReceita) {
                if ($arMovimentacaoSessao[$x]['conciliar']) {
                    $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacaoSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovCinciliada .= $arMovimentacaoSessao[$x]['indices'].",";
                } else {
                    $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacaoSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovNCinciliada .= $arMovimentacaoSessao[$x]['indices'].",";
                }
                $x++;
            }
            $x--;
            if ($nuVlMovConciliada != 0) {
                if( $nuVlMovConciliada >= 0 )
                    $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                else
                    $stDescricao  = "Estorno de Arrecadação da receita ".$inCodReceita."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = true;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                $inCount++;
            }
            if ($nuVlMovNConciliada != 0) {
                foreach ($arMovimentacaoSessao[$x] as $key => $value) {
                    $arMovAgrupada[$inCount][$key] = $value;
                }
                if( $nuVlMovNConciliada >= 0 )
                    $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                else
                    $stDescricao  = "Estorno de Arrecadação da receita ".$inCodReceita."-".$arMovimentacaoSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = false;
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                $inCount++;
            }
            $inCount--;
        }
    $inCount++;
    }
//    sessao->transf3['arMovimentacaoAux'] = sessao->transf3['arMovimentacao'];
    $arMovimentacaoSessao = $arMovAgrupada;
}

$arMovAgrupada = array();
// Agrupa bordero e receita das pendencias
if ($_REQUEST['boAgrupar']) {
    $inCount = 0;
    for ( $x = 0 ; $x < count( $arMovimentacaoPendenciaSessao ); $x++ ) {
        foreach ($arMovimentacaoPendenciaSessao[$x] as $key => $value) {
            $arMovAgrupada[$inCount][$key] = $value;
        }
        // Agrupa bordero
        if ($arMovimentacaoPendenciaSessao[$x]['cod_bordero']) {
            $inCodBordero = $arMovimentacaoPendenciaSessao[$x]['cod_bordero'];
            $stDescricao = "Pagamento de empenho(s) conforme Borderô nr. ".$inCodBordero."-".$arMovimentacaoPendenciaSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
            $arMovAgrupada[$inCount]['descricao'] = $stDescricao;
            $nuVlMovConciliada = 0;
            $nuVlMovNConciliada = 0;
            $stIndiceMovCinciliada  = "";
            $stIndiceMovNCinciliada = "";
            while ($arMovimentacaoPendenciaSessao[$x]['cod_bordero'] == $inCodBordero) {
                if ($arMovimentacaoPendenciaSessao[$x]['conciliar']) {
                    $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacaoPendenciaSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovCinciliada .= $arMovimentacaoPendenciaSessao[$x]['indices'].",";
                } else {
                    $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacaoPendenciaSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovNCinciliada .= $arMovimentacaoPendenciaSessao[$x]['indices'].",";
                }
                $x++;
            }
            $x--;

            if ($nuVlMovConciliada) {
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = true;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                $inCount++;
            }
            if ($nuVlMovNConciliada) {
                foreach ($arMovimentacaoPendenciaSessao[$x] as $key => $value) {
                    $arMovAgrupada[$inCount][$key] = $value;
                }
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = false;
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                $inCount++;
            }
            $inCount--;
        }
        // Agrupa Arrecadacao
        if ($arMovimentacaoPendenciaSessao[$x]['cod_receita']) {
            $inCodReceita = $arMovimentacaoPendenciaSessao[$x]['cod_receita'];
            $stDescricao  = "Arrecadação da receita ".$inCodReceita."-".$arMovimentacaoPendenciaSessao[$x]['cod_entidade']."/".Sessao::getExercicio();
            $arMovAgrupada[$inCount]['descricao'] = $stDescricao;
            $nuVlMovConciliada  = 0;
            $nuVlMovNConciliada = 0;
            $stIndiceMovCinciliada  = "";
            $stIndiceMovNCinciliada = "";
            while ($arMovimentacaoPendenciaSessao[$x]['cod_receita'] == $inCodReceita) {
                if ($arMovimentacaoPendenciaSessao[$x]['conciliar']) {
                    $nuVlMovConciliada = bcadd( $nuVlMovConciliada, $arMovimentacaoPendenciaSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovCinciliada .= $arMovimentacaoPendenciaSessao[$x]['indices'].",";
                } else {
                    $nuVlMovNConciliada = bcadd( $nuVlMovNConciliada, $arMovimentacaoPendenciaSessao[$x]['vl_lancamento'], 4 );
                    $stIndiceMovNCinciliada .= $arMovimentacaoPendenciaSessao[$x]['indices'].",";
                }
                $x++;
            }
            $x--;
            if ($nuVlMovConciliada != 0) {
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = true;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovCinciliada,0,strlen($stIndiceMovCinciliada)-1);
                $inCount++;
            }
            if ($nuVlMovNConciliada != 0) {
                foreach ($arMovimentacaoPendenciaSessao[$x] as $key => $value) {
                    $arMovAgrupada[$inCount][$key] = $value;
                }
                $arMovAgrupada[$inCount]['vl_lancamento'] = $nuVlMovNConciliada;
                $arMovAgrupada[$inCount]['conciliar']     = false;
                $arMovAgrupada[$inCount]['descricao']     = $stDescricao;
                $arMovAgrupada[$inCount]['indices']       = substr($stIndiceMovNCinciliada,0,strlen($stIndiceMovNCinciliada)-1);
                $inCount++;
            }
            $inCount--;
        }
    $inCount++;
    }
//    sessao->transf3['arMovimentacaoPendenciaAux'] = sessao->transf3['arMovimentacaoPendencia'];
    $arMovimentacaoPendenciaSessao = $arMovAgrupada;
}

$arCabecalhos[] = 'entradaBanco';
$arCabecalhos[] = 'entradaTesouraria';
$arCabecalhos[] = 'saidaBanco';
$arCabecalhos[] = 'saidaTesouraria';

$arLista = array();
foreach ($arCabecalhos as $stChave) {
    if ($arPendenciaListagemAux[$stChave]) {

        $inCount = 0;
        $inLinha = 0;
        // Verifica em qual numero deve começar a linha das tabelas para os ids dos valores.
        // Os cases não estão com break de propósito, pois se a chave for saidaTesouraria, o valor da linha tem que ser a soma do total de todos
        // juntos, se for saidaBanco, somente a soma da linha dele e da entradaTesouraria.
        switch ($stChave) {
        case 'saidaTesouraria':
            $inLinha += count($arPendenciaListagemAux['saidaBanco']);
        case 'saidaBanco':
            $inLinha += count($arPendenciaListagemAux['entradaTesouraria']);
        case 'entradaTesouraria':
            $inLinha += count($arPendenciaListagemAux['entradaBanco']);
        }

        foreach ($arPendenciaListagemAux[$stChave] as $key => $array) {
            foreach ($array as $stCampo => $stValor) {
                $arLista[$stChave][$inCount][$stCampo] = $stValor;
            }
            $arLista[$stChave][$inCount]['linha'] = ($inLinha+1);
            if ($array['tipo'] == 'P' and trim($array['observacao'])) {
                if(!strstr($array['observacao'], "Borderô"))
                    $arLista[$stChave][$inCount]['descricao'] = $array['descricao']." - ".$array['observacao'];
            }
            $inCount++;
            $inLinha++;
        }
    }
}
$arPendenciaListagemAux = $arLista;

$inLinha = 1;
foreach ($arPendenciaListagemAux as $arTipoPendencias) {
    foreach ($arTipoPendencias as $stChave => $arDados) {
        if ($arDados['conciliar'] == true) {
            $arPendenciasMarcadas['boPendencia_'.$arDados['tipo'].'-'.$arDados['sequencia'].'_'.$inLinha] = true;
        }
        $inLinha++;
    }
}

Sessao::write('arMovimentacao', $arMovimentacaoSessao);
Sessao::write('arMovimentacaoAux', $arMovimentacaoAuxSessao);
Sessao::write('arMovimentacaoPendencia', $arMovimentacaoPendenciaSessao);
Sessao::write('arMovimentacaoPendenciaAux', $arMovimentacaoPendenciaAuxSessao);
Sessao::write('arMovimentacaoManual', $arMovimentacaoManualSessao);
Sessao::write('arMovimentacaoPendenciaListagem', $arPendenciaListagemAux);
Sessao::write('arPendenciasMarcadas', $arPendenciasMarcadas);

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setId( "stExercicio" );
$obHdnExercicio->setValue( $_REQUEST['stExercicio'] );

$obHdnTimestampConciliacao = new Hidden;
$obHdnTimestampConciliacao->setName( "stTimestampConciliacao" );
$obHdnTimestampConciliacao->setValue( $rsRecordSet->getCampo("timestamp") );

// Define Objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo  ( "Entidade"                             );
$obLblEntidade->setId      ( "inCodEntidade"                        );
$obLblEntidade->setValue   ( $_REQUEST['inCodEntidade']." - " . $_REQUEST['stNomEntidade']  );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "inCodEntidade" );
$obHdnEntidade->setValue( $_REQUEST['inCodEntidade'] );

// Define Objeto Label para Conta
$obLblConta = new Label;
$obLblConta->setRotulo  ( "Conta"                          );
$obLblConta->setId      ( "inCodConta"                  );
$obLblConta->setValue   ( $_REQUEST['inCodPlano']." - " . $_REQUEST['stNomConta']  );

$obHdnConta = new Hidden;
$obHdnConta->setName( "inCodPlano" );
$obHdnConta->setValue( $_REQUEST['inCodPlano'] );

// Define objeto Data para a Data do Extrato
$obDtExtrato = new Data;
$obDtExtrato->setName     ( "stDtExtrato"                   );
$obDtExtrato->setValue    ( $_REQUEST['stDtExtrato']        );
$obDtExtrato->setRotulo   ( "Data Extrato"                  );
$obDtExtrato->setTitle    ( 'Informe a data do Extrato.'    );
$obDtExtrato->setNull     ( false                           );
$obDtExtrato->obEvento->setOnChange( "buscaDado('saldoTesouraria');" );

// Define Obeto Moeda para Saldo do Extrato
$obTxtSaldoExtrato = new Moeda();
$obTxtSaldoExtrato->setRotulo   ( "Saldo do Extrato"                    );
$obTxtSaldoExtrato->setTitle    ( "Digite o Saldo do Extrato da Conta." );
$obTxtSaldoExtrato->setName     ( "nuSaldoExtrato"                      );
$obTxtSaldoExtrato->setId       ( "nuSaldoExtrato"                      );
$obTxtSaldoExtrato->setDecimais ( 2                                     );
$obTxtSaldoExtrato->setNegativo ( false                                 );
$obTxtSaldoExtrato->setNull     ( false                                 );
$obTxtSaldoExtrato->setSize     ( 14                                    );
$obTxtSaldoExtrato->setMaxLength( 14                                    );
$obTxtSaldoExtrato->setMinValue ( 0.00                                  );
$obTxtSaldoExtrato->obEvento->setOnChange( "calculaSaldo();"              );
$obTxtSaldoExtrato->setNegativo( true                            );
if ($rsRecordSet->getNumLinhas() > -1) {
    $obTxtSaldoExtrato->setValue( number_format($rsRecordSet->getCampo("vl_extrato"),2,',','.') );
} else {
    $obTxtSaldoExtrato->setValue( '0.00' );
}

// Define Objeto Label para mês de conciliação
$obLblMesConciliacao = new Label;
$obLblMesConciliacao->setRotulo  ( "Mês de Conciliação"         );
$obLblMesConciliacao->setId      ( "stMesConciliacao"           );
$obLblMesConciliacao->setValue   ( $mes . " - " . $meses[intval($mes)]  );

$obHdnMes = new Hidden;
$obHdnMes->setName( "inMes" );
$obHdnMes->setId( "inMes" );
$obHdnMes->setValue( $mes );

$obHdnSaldoTesouraria = new Hidden;
$obHdnSaldoTesouraria->setName( "nuSaldoTesouraria" );
$obHdnSaldoTesouraria->setId( "nuSaldoTesouraria" );
$obHdnSaldoTesouraria->setValue( $nuSaldoTesouraria );

// Define Objeto Label para saldo contabil
$obLblSaldoContabil = new Label;
$obLblSaldoContabil->setRotulo  ( "Saldo da Tesouraria" );
$obLblSaldoContabil->setId      ( "nuLblSaldoTesouraria"   );
$obLblSaldoContabil->setValue   ( number_format($nuSaldoTesouraria,2,',','.') );

// Define Objeto Hdn para saldo contabil conciliado
$obHdnSaldoContabilConciliado = new Hidden;
$obHdnSaldoContabilConciliado->setId      ( "nuSaldoContabilConciliado" );
$obHdnSaldoContabilConciliado->setName    ( "nuSaldoContabilConciliado" );
$obHdnSaldoContabilConciliado->setValue   ( number_format($nuSaldoContabilConciliado,2,',','.')  );

$nuDiferencaConciliar = number_format(bcsub(bcsub($rsRecordSet->getCampo("vl_extrato"),$nuSaldoTesouraria,4),$nuSaldoContabilConciliado,4),2,',','.');

// Define Objeto Label para saldo contabil a conciliar
$obLblSaldoConciliado = new Label;
$obLblSaldoConciliado->setRotulo   ( "Saldo Conciliado"     );
$obLblSaldoConciliado->setId       ( "nuLblSaldoConciliado" );
$obLblSaldoConciliado->setValue    ( number_format(bcsub($nuSaldoTesouraria,$nuSaldoContabilConciliado,4),2,',','.') );

// Define Objeto Span Para lista de movimentações
$obSpanPendente = new Span;
$obSpanPendente->setId( "spnMovimentacaoPendente" );

// Define objeto Data para a Data da movimentação
$stMsg = "A data digitada não pertence ao mês da conciliação";
$obDtMovimentacao = new Data;
$obDtMovimentacao->setName     ( "stDtMovimentacao"                 );
$obDtMovimentacao->setValue    ( $_REQUEST['stDtExtrato']           );
$obDtMovimentacao->setRotulo   ( "*Data"                            );
$obDtMovimentacao->setTitle    ( 'Informe a Data da Movimentação.'  );
$obDtMovimentacao->setNull     ( true                               );
$obDtMovimentacao->obEvento->setOnChange( "if (document.frm.inMes.value!=this.value.substr(3,2)) {this.value='';alertaAviso('".$stMsg."','aviso','','".Sessao::getId()."');}" );

// Define Obeto Moeda para valor da arrecadacao
$obTxtValor = new Moeda();
$obTxtValor->setRotulo   ( "*Valor"                          );
$obTxtValor->setTitle    ( "Informe o Valor da Movimentação.");
$obTxtValor->setName     ( "nuValor"                         );
$obTxtValor->setId       ( "nuValor"                         );
$obTxtValor->setNull     ( false                             );
$obTxtValor->setDecimais ( 2                                 );
$obTxtValor->setNegativo ( false                             );
$obTxtValor->setNull     ( true                              );
$obTxtValor->setSize     ( 14                                );
$obTxtValor->setMaxLength( 14                                );
$obTxtValor->setMinValue ( 0.00                              );

//Radios de tipo de movimento
$obRdTipoMovimentacaoC = new Radio;
$obRdTipoMovimentacaoC->setRotulo ( "*Tipo de Movimento"    );
$obRdTipoMovimentacaoC->setName   ( "stTipoMovimento"       );
$obRdTipoMovimentacaoC->setChecked( true                    );
$obRdTipoMovimentacaoC->setValue  ( "C"                     );
$obRdTipoMovimentacaoC->setLabel  ( "Entradas"              );
$obRdTipoMovimentacaoC->setNull   ( true                    );
$obRdTipoMovimentacaoC->setId     ( "typeC"                 );

$obRdTipoMovimentacaoD = new Radio;
$obRdTipoMovimentacaoD->setName   ( "stTipoMovimento"   );
$obRdTipoMovimentacaoD->setValue  ( "D"                 );
$obRdTipoMovimentacaoD->setLabel  ( "Saídas"            );
$obRdTipoMovimentacaoD->setId     ( "typeD"             );

// Define Objeto TextArea para descrição
$obTxtDescricao = new TextArea;
$obTxtDescricao->setName   ( "stDescricao"      );
$obTxtDescricao->setId     ( "stDescricao"      );
$obTxtDescricao->setRotulo ( "*Descrição"       );
$obTxtDescricao->setTitle  ( "Digite a Observação Relativa à este Recebimento.");
$obTxtDescricao->setNull   ( true               );
$obTxtDescricao->setRows   ( 2                  );
$obTxtDescricao->setCols   ( 100                );

if(SistemaLegado::isTCMBA($boTransacao)) {
    $obTTCMBATipoConciliacao = new TTCMBATipoConciliacao();
    $obTTCMBATipoConciliacao->recuperaTodos( $rsTipoConciliacao, ' ORDER BY cod_tipo_conciliacao ' );
    
    $obSelectTipoConciliacao = new Select();
    $obSelectTipoConciliacao->setName   ( "inCodTipoConciliacao" );
    $obSelectTipoConciliacao->setId   ( "inCodTipoConciliacao" );
    $obSelectTipoConciliacao->setRotulo ( "*Tipo de conciliação" );
    $obSelectTipoConciliacao->addOption ( "","Selecione" );
    $obSelectTipoConciliacao->setCampoID( "cod_tipo_conciliacao" );
    $obSelectTipoConciliacao->setCampoDesc( "descricao" );
    $obSelectTipoConciliacao->preencheCombo( $rsTipoConciliacao );
    $obSelectTipoConciliacao->setStyle  ( "width:180px;" );
    $obSelectTipoConciliacao->setValue  ( "[cod_tipo_conciliacao]" );
}


// Define Objeto Span Para lista de movimentações
$obSpanMovimentacao = new Span;
$obSpanMovimentacao->setId( "spnMovimentacao" );

// Define Objeto Span Para lista de movimentações
$obSpanMovimentacaoManual = new Span;
$obSpanMovimentacaoManual->setId( "spnMovimentacaoManual" );
$obSpanMovimentacaoManual->setValue ( "" );

// Define Objeto Button para incluir movimentação
$obBtnIncluir = new Button;
$obBtnIncluir->setValue( "Incluir " );
$obBtnIncluir->setName( "btIncluir" );
$obBtnIncluir->obEvento->setOnClick( "incluirMovimentacao();");

// Define Objeto Button para alterar movimentação
$obBtnAlterar = new Button;
$obBtnAlterar->setValue( "Alterar " );
$obBtnAlterar->setName( "btAlterar" );
$obBtnAlterar->setDisabled( true );
$obBtnAlterar->obEvento->setOnClick( "incluirMovimentacao()");

// Define Objeto Button para Limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limparMovimentacao();");

// Define objeto BuscaInner para cgm
$obBscCGM1 = new BuscaInner();
$obBscCGM1->setRotulo                 ( "CGM"            );
$obBscCGM1->setTitle                  ( "Informe o CGM que deseja pesquisar."   );
$obBscCGM1->setId                     ( "stNomCgm1"       );
$obBscCGM1->setValue                  ( $rsRecordSetAssinatura->getCampo("nom_cgm")       );
$obBscCGM1->setNull                   ( true              );
$obBscCGM1->obCampoCod->setName       ( "inNumCgm1"       );
$obBscCGM1->obCampoCod->setSize       ( 10                );
$obBscCGM1->obCampoCod->setMaxLength  ( 8                 );
$obBscCGM1->obCampoCod->setValue      ( $rsRecordSetAssinatura->getCampo("numcgm")        );
$obBscCGM1->obCampoCod->setAlign      ( "left"            );
$obBscCGM1->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm1','stNomCgm1','fisica','".Sessao::getId()."','800','550');");
$obBscCGM1->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() ,'fisica');

// Define objeto TextBox para Matrícula
$obTxtMatricula1 = new TextBox();
$obTxtMatricula1->setRotulo     ( 'Número de Matrícula'  );
$obTxtMatricula1->setTitle      ( 'Informe o Número de Matrícula referente ao CGM pesquisado.'    );
$obTxtMatricula1->setName       ( 'inMatricula1'         );
$obTxtMatricula1->setValue      ( $rsRecordSetAssinatura->getCampo("num_matricula")      );
$obTxtMatricula1->setSize       ( 20                     );
$obTxtMatricula1->setMaxLength  ( 20                     );
$obTxtMatricula1->setNull       ( true                   );
$obTxtMatricula1->setInteiro    ( true                   );

// Define objeto TextBox para cargo
$obTxtCargo1 = new TextBox();
$obTxtCargo1->setRotulo     ( 'Cargo'       );
$obTxtCargo1->setTitle      ( 'Informe o Cargo referente ao CGM pesquisado.'    );
$obTxtCargo1->setName       ( 'stCargo1'    );
$obTxtCargo1->setValue      ( $rsRecordSetAssinatura->getCampo("cargo")     );
$obTxtCargo1->setSize       ( 75            );
$obTxtCargo1->setMaxLength  ( 1000          );
$obTxtCargo1->setNull       ( true          );

$rsRecordSetAssinatura->proximo();

// Define objeto BuscaInner para cgm
$obBscCGM2 = new BuscaInner();
$obBscCGM2->setRotulo                 ( "CGM"               );
$obBscCGM2->setTitle                  ( "Informe o CGM que deseja pesquisar."               );
$obBscCGM2->setId                     ( "stNomCgm2"         );
$obBscCGM2->setValue                  ( $rsRecordSetAssinatura->getCampo("nom_cgm")          );
$obBscCGM2->setNull                   ( true                );
$obBscCGM2->obCampoCod->setName       ( "inNumCgm2"         );
$obBscCGM2->obCampoCod->setSize       ( 10                  );
$obBscCGM2->obCampoCod->setMaxLength  ( 8                   );
$obBscCGM2->obCampoCod->setValue      ( $rsRecordSetAssinatura->getCampo("numcgm")          );
$obBscCGM2->obCampoCod->setAlign      ( "left"              );
$obBscCGM2->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm2','stNomCgm2','fisica','".Sessao::getId()."','800','550');");
$obBscCGM2->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() ,'fisica');

// Define objeto TextBox para Matrícula
$obTxtMatricula2 = new TextBox();
$obTxtMatricula2->setRotulo     ( 'Número de Matrícula'  );
$obTxtMatricula2->setTitle      ( 'Informe o Número de Matrícula referente ao CGM pesquisado.'    );
$obTxtMatricula2->setName       ( 'inMatricula2'         );
$obTxtMatricula2->setValue      ( $rsRecordSetAssinatura->getCampo("num_matricula")         );
$obTxtMatricula2->setSize       ( 20                     );
$obTxtMatricula2->setMaxLength  ( 20                     );
$obTxtMatricula2->setNull       ( true                   );
$obTxtMatricula2->setInteiro    ( true                   );

// Define objeto TextBox para cargo
$obTxtCargo2 = new TextBox();
$obTxtCargo2->setRotulo     ( 'Cargo'       );
$obTxtCargo2->setTitle      ( 'Informe o Cargo referente ao CGM pesquisado.'    );
$obTxtCargo2->setName       ( 'stCargo2'    );
$obTxtCargo2->setValue      ( $rsRecordSetAssinatura->getCampo("cargo")     );
$obTxtCargo2->setSize       ( 75            );
$obTxtCargo2->setMaxLength  ( 1000          );
$obTxtCargo2->setNull       ( true          );

$rsRecordSetAssinatura->proximo();

// Define objeto BuscaInner para cgm
$obBscCGM3 = new BuscaInner();
$obBscCGM3->setRotulo                 ( "CGM"               );
$obBscCGM3->setTitle                  ( "Informe o CGM que deseja pesquisar."               );
$obBscCGM3->setId                     ( "stNomCgm3"         );
$obBscCGM3->setValue                  ( $rsRecordSetAssinatura->getCampo("nom_cgm")          );
$obBscCGM3->setNull                   ( true                );
$obBscCGM3->obCampoCod->setName       ( "inNumCgm3"         );
$obBscCGM3->obCampoCod->setSize       ( 10                  );
$obBscCGM3->obCampoCod->setMaxLength  ( 8                   );
$obBscCGM3->obCampoCod->setValue      ( $rsRecordSetAssinatura->getCampo("numcgm")          );
$obBscCGM3->obCampoCod->setAlign      ( "left"              );
$obBscCGM3->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm3','stNomCgm3','fisica','".Sessao::getId()."','800','550');");
$obBscCGM3->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() ,'fisica');

// Define objeto TextBox para Matrícula
$obTxtMatricula3 = new TextBox();
$obTxtMatricula3->setRotulo     ( 'Número de Matrícula'  );
$obTxtMatricula3->setTitle      ( 'Informe o Número de Matrícula referente ao CGM pesquisado.'    );
$obTxtMatricula3->setName       ( 'inMatricula3'         );
$obTxtMatricula3->setValue      ( $rsRecordSetAssinatura->getCampo("num_matricula")          );
$obTxtMatricula3->setSize       ( 20                     );
$obTxtMatricula3->setMaxLength  ( 20                     );
$obTxtMatricula3->setNull       ( true                   );
$obTxtMatricula3->setInteiro    ( true                   );

// Define objeto TextBox para cargo
$obTxtCargo3 = new TextBox();
$obTxtCargo3->setRotulo     ( 'Cargo'       );
$obTxtCargo3->setTitle      ( 'Informe o Cargo referente ao CGM pesquisado.'    );
$obTxtCargo3->setName       ( 'stCargo3'    );
$obTxtCargo3->setValue      ( $rsRecordSetAssinatura->getCampo("cargo")     );
$obTxtCargo3->setSize       ( 75            );
$obTxtCargo3->setMaxLength  ( 1000          );
$obTxtCargo3->setNull       ( true          );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm       ( $obForm                      );
$obFormulario->addHidden     ( $obHdnAcao                   );
$obFormulario->addHidden     ( $obHdnCtrl                   );
$obFormulario->addHidden     ( $obHdnExercicio              );
$obFormulario->addHidden     ( $obHdnTimestampConciliacao   );
$obFormulario->addHidden     ( $obHdnSaldoTesouraria        );

$obFormulario->addAba        ( "Principal"                  );
$obFormulario->addTitulo     ( "Dados para Conciliação Bancária" );
$obFormulario->addComponente ( $obLblEntidade               );
$obFormulario->addHidden     ( $obHdnEntidade               );
$obFormulario->addComponente ( $obLblConta                  );
$obFormulario->addHidden     ( $obHdnConta                  );
$obFormulario->addComponente ( $obDtExtrato                 );
$obFormulario->addComponente ( $obTxtSaldoExtrato           );
$obFormulario->addComponente ( $obLblMesConciliacao         );
$obFormulario->addHidden     ( $obHdnMes                    );
$obFormulario->addComponente ( $obLblSaldoContabil          );
$obFormulario->addHidden     ( $obHdnSaldoContabilConciliado);
$obFormulario->addComponente ( $obLblSaldoConciliado        );
$obFormulario->addSpan       ( $obSpanPendente              );
$obFormulario->addTitulo     ( "Assinaturas" );
$obFormulario->addComponente ( $obBscCGM1                   );
$obFormulario->addComponente ( $obTxtMatricula1             );
$obFormulario->addComponente ( $obTxtCargo1                 );
$obFormulario->addComponente ( $obBscCGM2                   );
$obFormulario->addComponente ( $obTxtMatricula2             );
$obFormulario->addComponente ( $obTxtCargo2                 );
$obFormulario->addComponente ( $obBscCGM3                   );
$obFormulario->addComponente ( $obTxtMatricula3             );
$obFormulario->addComponente ( $obTxtCargo3                 );

$obFormulario->addAba        ( "Movimentações Correntes"    );
$obFormulario->addTitulo     ( "Dados para Conciliação Bancária" );
$obFormulario->addSpan       ( $obSpanMovimentacao          );

$obFormulario->addAba        ( "Novas Movimentações"        );
$obFormulario->addTitulo     ( "Dados para Movimentação de Conciliação Bancária" );
$obFormulario->addComponente ( $obDtMovimentacao            );
$obFormulario->addComponente ( $obTxtValor                  );
$obFormulario->agrupaComponentes( array( $obRdTipoMovimentacaoC, $obRdTipoMovimentacaoD ) );
$obFormulario->addComponente ( $obTxtDescricao              );

if(SistemaLegado::isTCMBA($boTransacao)) {
    $obFormulario->addComponente ( $obSelectTipoConciliacao );

}
$obFormulario->agrupaComponentes ( array( $obBtnIncluir, $obBtnAlterar, $obBtnLimpar ) );
$obFormulario->addSpan       ( $obSpanMovimentacaoManual    );

foreach ($_REQUEST as $key => $value) {
    $stFiltro .= "&".$key."=".$value;
}
$stLocation = $pgList.'?'.Sessao::getId().$stFiltro;
$obFormulario->Cancelar( $stLocation );

if($stFiltro)
    Sessao::write('voltaBusca',$stLocation );
else
    Sessao::write('voltaBusca',"");

$obFormulario->show();

$jsOnload = 'montaParametrosGET("montaListaMovimentacao","stExercicio,inMes", true);';

if(SistemaLegado::isTCMBA($boTransacao)) {
    $jsOnload.= 'jq(document).ready(function(){
        jq.each(jq(".boConciliar"), function (i, checkbox) {
            if(jq(checkbox).attr("checked")) {
                var name = "idTipoConciliacao" + jq(checkbox).attr("name").substr(11, 100);
                jq("select[name*="+ name +"]").attr("disabled", true);
            }
        });
        
        jq(".boConciliar").change(function(){
            if(jq(this).attr("checked")) {
                var name = "idTipoConciliacao" + jq(this).attr("name").substr(11, 100);
                jq("select[name*="+ name +"]").val("");
                jq("select[name*="+ name +"]").attr("disabled", true);
            }
            
            if(!jq(this).attr("checked")) {
                var name = "idTipoConciliacao" + jq(this).attr("name").substr(11, 100);
                jq("select[name*="+ name +"]").val("");
                jq("select[name*="+ name +"]").attr("disabled", false);
            }
        });
    }); ';
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';