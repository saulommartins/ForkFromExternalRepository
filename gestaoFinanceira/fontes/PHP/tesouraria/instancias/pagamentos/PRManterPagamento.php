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
    * Página de Processamento para Pagamento do módulo Tesouraria
    * Data de Criação   : 27/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: PRManterPagamento.php 66484 2016-09-02 18:07:47Z franver $

    * Casos de uso: uc-02.04.05
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php";
require_once CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php";
require_once CAM_GF_TES_MAPEAMENTO."TTesourariaPagamento.class.php";
require_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

$stAcao = $request->get('stAcao');

$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

$obAdministracaoConfiguracao = new TAdministracaoConfiguracao();

list( $inCodBoletim , $stDtBoletim ) = explode ( ':' , $request->get('inCodBoletim') );
list($stDia, $stMes, $stAno) = explode( '/', $stDtBoletim );

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9, '', $boTransacao);

$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ', $boTransacao);

if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $stMes) {
    SistemaLegado::exibeAviso(urlencode("Mês do Boletim encerrado!"),"n_incluir","erro");
    SistemaLegado::LiberaFrames();
    exit;
}

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio  ( Sessao::getExercicio() );
$obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
$obRTesourariaBoletim->setDataBoletim( $stDtBoletim  );
$obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade ( $request->get('inCodEntidade') );
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario( $request->get('stTimestampUsuario') );
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal( $request->get('inCodTerminal') );
$obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal( $request->get('stTimestampTerminal') );
$obRTesourariaBoletim->addPagamento();

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
$obRTesourariaConfiguracao->consultarTesouraria($boTransacao);

switch ($stAcao) {
    case 'incluir':

    if ( $stDtBoletim == date( 'd/m/Y' ) ) {
        $stTimestamp = date( 'Y-m-d H:i:s.ms' );
    } else {
        list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
        $stTimestamp = substr($stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms'),0,-1);
    }
    $obRTesourariaBoletim->roUltimoPagamento->setTimestamp( $stTimestamp );
    
    $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE exercicio = '".Sessao::getExercicio()."' and cod_modulo = 2 and parametro = 'cod_uf'","",$boTransacao);
    $inCodUf = $rsAdministracaoConfiguracao->getCampo('valor');
    $stSiglaUf = SistemaLegado::pegaDado("sigla_uf","sw_uf","where cod_uf = ".$inCodUf."", $boTransacao);

    $obErro = new Erro;
    if( $stSiglaUf != "BA" ){
        if ($request->get('inDocTipo')) {
            switch ($request->get('inDocTipo')) {
                case 1 :
                case 2 :
                case 3 :
                case 99:
                    if ( !$request->get('nuDoc') ) {
                        $obErro->setDescricao("O número do documento é obrigatório");
                    }
            }
        }
    }
    if ($request->get('inCodOrdem')) {
        if (SistemaLegado::comparaDatas($request->get('stDtEmissaoOrdem'),$stDtBoletim)) {
            $obErro->setDescricao("A data do pagamento é anterior à data de emissão da OP");
        }
    }
    if($stSiglaUf == "BA" && $inCodUf==5 && !$obErro->ocorreu()){
        if($request->get('inCodTipoPagamento')==''){
            $obErro->setDescricao("Informe o Tipo de Pagamento TCM-BA");
        }
        if($request->get('numDocPagamento')==''){
            $obErro->setDescricao("Informe o Número de Detalhe do Tipo Pagamento TCM-BA");
        }
    }
    
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem( $request->get('inCodOrdem') );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio( $request->get('stExercicioOrdem') );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setDataVencimento( '31/12/'.Sessao::getExercicio() );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setCodPlano( $request->get('inCodPlano') );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setObservacao( $request->get('stObservacoes') );

    // Verifica se já há algum valor prestado contas.
    if ($request->get('nuValorPrestado') > 0) {
        $obErro->setDescricao('Esta OP é de adiantamentos/subvenções e não pode ser paga novamente.');
    }

    $arrNota = Sessao::read('arNota');
    // seta como false a variavel boCheque
    $obRTesourariaBoletim->roUltimoPagamento->boCheque = false;

    if (!$obErro->ocorreu() && is_array( $arrNota ) ) {
        $inCount = 0;
        foreach ($arrNota as $arNota) {
            list( $inCodEmpenho, $stExercicioEmpenho    ) = explode( '/', $arNota['empenho'] );
            list( $inCodNota   , $stExercicioLiquidacao ) = explode( '/', $arNota['liquidacao'] );
            $nuValorPagar       = str_replace(',','.',str_replace('.','', $request->get('nuValorPagar_'.($inCount+1)) ));
            $nuValorOriginal    = str_replace(',','.',str_replace('.','', $request->get('nuValorOriginal_'.($inCount+1)) ));

            if (SistemaLegado::comparaDatas($arNota['dt_liquidacao'],$stDtBoletim)) {
                $obErro->setDescricao("A data do pagamento é anterior à data da liquidaçao");
            } elseif ($nuValorPagar > $nuValorOriginal) {
                $obErro->setDescricao("O valor a pagar da nota ".$inCodNota." não pode ser superior a R$ ".$request->get('nuValorOriginal_'.($inCount+1)).".");
            }

            $arNotaLiquidacao[$inCount]['cod_nota']        = $inCodNota;
            $arNotaLiquidacao[$inCount]['ex_nota' ]        = $stExercicioLiquidacao;
            $arNotaLiquidacao[$inCount]['cod_empeho']      = $inCodEmpenho;
            $arNotaLiquidacao[$inCount]['ex_empenho']      = $stExercicioEmpenho;
            $arNotaLiquidacao[$inCount]['dt_nota']         = $stDtBoletim;
            $arNotaLiquidacao[$inCount]['valor_pagar']     = $request->get('nuValorPagar_'.($inCount+1));
            $arNotaLiquidacao[$inCount]['max_valor_pagar'] = $request->get('nuValorPagamento');

            $nuVlPagar = str_replace(".", "", $request->get('nuValorPagar_'.($inCount+1)));
            $nuVlPagar = str_replace(",", ".", $nuVlPagar);

            $arNotaPaga[$inCount]['cod_nota']     = $inCodNota;
            $arNotaPaga[$inCount]['exercicio']    = $stExercicioLiquidacao;
            $arNotaPaga[$inCount]['cod_entidade'] = $arNota['cod_entidade'];
            $arNotaPaga[$inCount]['cod_empeho']   = $inCodEmpenho;
            $arNotaPaga[$inCount]['ex_empenho']   = $stExercicioEmpenho;
            $arNotaPaga[$inCount]['dt_nota']      = $stDtBoletim;
            $arNotaPaga[$inCount]['vl_pago']      = $nuVlPagar;
            $arNotaPaga[$inCount]['vl_a_pagar']   = $request->get('nuValorPagamento');

            $nuTotalPagamento = $nuTotalPagamento + $nuVlPagar; // Totaliza o valor do pagamento que está sendo feito

            $inCount++;
        }

        if ($nuTotalPagamento == '0.00') {
            $obErro->setDescricao("O valor a pagar deve ser maior que zero.");
        }

        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setNotaLiquidacao( $arNotaLiquidacao );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setValoresPagos( $arNotaPaga );
    }

    if (!$obErro->ocorreu()) {
       $obErro = $obRTesourariaBoletim->roUltimoPagamento->pagar($boTransacao);
    }

    $boRetencao = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();

    if ($obRTesourariaBoletim->roUltimoPagamento->obRTesourariaAutenticacao->getDescricao()) {
        Sessao::write('pagamento',true);
    }
    if ( !$obErro->ocorreu() ) {
        if ($request->get('inCodOrdem')) { // Se não estiver pagando uma op...
           $inCodOrdemPagarOutra = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem();
            $stExercicioOrdemPagarOutra = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getExercicio();
        } else { // .. continua pagando a nota que foi selecionada na lista (a única que foi paga anteriormente )
            $inCodNotaPagarOutra       = $inCodNota;
            $stExercicioNotaPagarOutra = $stExercicioLiquidacao;
        }

        if ($request->get('boPagarOutra')) {
            if ($nuTotalPagamento < $request->get('nuValorPagamento')) {
                $stPagarOutraAuth = "inCodBoletim=".urlencode($request->get('inCodBoletim'))."&inCodEntidade=".$request->get('inCodEntidade')."&stOrdem=".$inCodOrdemPagarOutra."%2F".$stExercicioOrdemPagarOutra."&stNota=".$inCodNotaPagarOutra."%2F".$stExercicioNotaPagarOutra."&pg_volta=../pagamentos/".$pgForm;
                $stPagarOutra = $pgForm."?stAcao=incluir&inCodEntidade=".$request->get('inCodEntidade')."&stOrdem=".$inCodOrdemPagarOutra."%2F".$stExercicioOrdemPagarOutra."&stNota=".$inCodNotaPagarOutra."%2F".$stExercicioNotaPagarOutra."&inCodBoletim=".urlencode($request->get('inCodBoletim'));
            } else {
                $stPagarOutraAuth = "pg_volta=../pagamentos/".$pgList;
                $stPagarOutra = $pgList;
            }
        }

        $inCodOrdem       = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getCodigoOrdem();
        $stExercicioOrdem = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getExercicio();

        //adicionado para que pegue as informações de pagamento que não foram anuladas ainda para gerar após os registros da tabela
        //tcepb.pagamento_origem_recursos_interna
        $arNota = Sessao::read('arNota');
        foreach ($arNota as $arrNota) {
            list( $inCodEmpenho, $stExercicioEmpenho    ) = explode( '/', $arrNota['empenho'] );
            list( $inCodNota   , $stExercicioLiquidacao ) = explode( '/', $arrNota['liquidacao'] );
            $obTTesourariaPagamento = new TTesourariaPagamento;
            $stFiltroPagamentos = " where cod_nota=".$inCodNota;
            $stFiltroPagamentos .="  and exercicio = '".$stExercicioLiquidacao."'
                      AND NOT EXISTS ( SELECT 1 FROM tesouraria.pagamento_estornado
                                                    WHERE pagamento.cod_nota = pagamento_estornado.cod_nota
                                                      AND pagamento.timestamp = pagamento_estornado.timestamp
                                          AND pagamento.exercicio = pagamento_estornado.exercicio
                                          AND pagamento.cod_entidade = pagamento_estornado.cod_entidade )
                      AND NOT EXISTS ( SELECT 1 FROM tcepb.pagamento_origem_recursos_interna
                                               WHERE pagamento_origem_recursos_interna.exercicio = pagamento.exercicio
                                                 AND pagamento_origem_recursos_interna.cod_nota = pagamento.cod_nota
                                                 AND pagamento_origem_recursos_interna.timestamp = pagamento.timestamp
                                                 AND pagamento_origem_recursos_interna.cod_entidade = pagamento.cod_entidade )

                      ";
            $obTTesourariaPagamento->recuperaTodos($rsPagamentos,$stFiltroPagamentos,"",$boTransacao);

            if ($request->get('inCodOrigemRecurso')) {
                $arOrigemRecurso = explode('-', $request->get('inCodOrigemRecurso'));
                require_once CAM_GPC_TPB_MAPEAMENTO."TTPBPagamentoOrigemRecursosInterna.class.php";
                while (!$rsPagamentos->eof()) {
                    $obTTPBPagamentoOrigemRecursosInterna = new TTPBPagamentoOrigemRecursosInterna;
                    $obTTPBPagamentoOrigemRecursosInterna->setDado('cod_entidade', $arrNota['cod_entidade']);
                    $obTTPBPagamentoOrigemRecursosInterna->setDado('exercicio', $stExercicioLiquidacao);
                    $obTTPBPagamentoOrigemRecursosInterna->setDado('cod_nota', $inCodNota);
                    $obTTPBPagamentoOrigemRecursosInterna->setDado('cod_origem_recursos', $arOrigemRecurso[0]);
                    $obTTPBPagamentoOrigemRecursosInterna->setDado('exercicio_origem_recurso', $arOrigemRecurso[1]);
                    $obTTPBPagamentoOrigemRecursosInterna->setDado('timestamp', $rsPagamentos->getCampo('timestamp') );
                    $obErro = $obTTPBPagamentoOrigemRecursosInterna->inclusao($boTransacao);

                    if ($obErro->ocorreu()) {
                      SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                      SistemaLegado::LiberaFrames();
                      break;
                    }
                    $rsPagamentos->proximo();
                }
            }

            //fim do processo de inclusao de origens de recurso

            $obTTesourariaPagamento->recuperaTodos($rsPagamentos, $stFiltroPagamentos,"",$boTransacao);

            if ($request->get('inDocTipo')) {
                ###TCMGO
                $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.exercicio = '".Sessao::getExercicio()."' AND configuracao.parametro = 'seta_tipo_documento_tcmgo'","",$boTransacao);

                if ($rsAdministracaoConfiguracao->getCampo('valor')  == 'true') {
                    require_once CAM_GF_TES_MAPEAMENTO."TTesourariaPagamentoTipoDocumento.class.php";
                    if (!$rsPagamentos->eof()) {
                        $obTTesourariaPagamentoTipoDocumento = new TTesourariaPagamentoTipoDocumento;
                        $obTTesourariaPagamentoTipoDocumento->setDado('cod_entidade', $arrNota['cod_entidade']);
                        $obTTesourariaPagamentoTipoDocumento->setDado('exercicio', $stExercicioLiquidacao);
                        $obTTesourariaPagamentoTipoDocumento->setDado('cod_nota', $inCodNota);
                        $obTTesourariaPagamentoTipoDocumento->setDado('cod_tipo_documento',$request->get('inDocTipo'));
                        $obTTesourariaPagamentoTipoDocumento->setDado('timestamp',$stTimestamp );
                        $obTTesourariaPagamentoTipoDocumento->setDado('num_documento', $request->get('nuDoc') );
                        $obErro = $obTTesourariaPagamentoTipoDocumento->inclusao($boTransacao);
                        if ($obErro->ocorreu()) {
                            SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                            SistemaLegado::LiberaFrames();
                        }
                    }
                }

                ###TCEMG
                $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.exercicio = '".Sessao::getExercicio()."' AND configuracao.parametro = 'seta_tipo_documento_tcemg'","", $boTransacao);

                if ($rsAdministracaoConfiguracao->getCampo('valor')  == 'true') {
                    require_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGPagamentoTipoDocumento.class.php";
                    if (!$rsPagamentos->eof()) {
                        $obTTCEMGPagamentoTipoDocumento = new TTCEMGPagamentoTipoDocumento;
                        $obTTCEMGPagamentoTipoDocumento->setDado('cod_entidade', $arrNota['cod_entidade']);
                        $obTTCEMGPagamentoTipoDocumento->setDado('exercicio', $stExercicioLiquidacao);
                        $obTTCEMGPagamentoTipoDocumento->setDado('cod_nota', $inCodNota);
                        $obTTCEMGPagamentoTipoDocumento->setDado('cod_tipo_documento',$request->get('inDocTipo') );
                        $obTTCEMGPagamentoTipoDocumento->setDado('timestamp',$stTimestamp );
                        $obTTCEMGPagamentoTipoDocumento->setDado('num_documento', $request->get('nuDoc') );
                        $obErro = $obTTCEMGPagamentoTipoDocumento->inclusao($boTransacao);
                        if ($obErro->ocorreu()) {
                            SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                            SistemaLegado::LiberaFrames();
                        }
                    }
                }
                
                ###TCEAL
                $obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE exercicio = '".Sessao::getExercicio()."' and cod_modulo = 2 and parametro = 'cod_uf'","",$boTransacao);
                $inCodUf = $rsAdministracaoConfiguracao->getCampo('valor');
                $stSiglaUf = SistemaLegado::pegaDado("sigla_uf","sw_uf","where cod_uf = ".$inCodUf."", $boTransacao);
                
                if ($stSiglaUf == "AL") {
                    require_once CAM_GPC_TCEAL_MAPEAMENTO."TTCEALPagamentoTipoDocumento.class.php";
                    if (!$rsPagamentos->eof()) {
                        $obTTCEALPagamentoTipoDocumento = new TTCEALPagamentoTipoDocumento;
                        $obTTCEALPagamentoTipoDocumento->setDado('cod_entidade', $arrNota['cod_entidade']);
                        $obTTCEALPagamentoTipoDocumento->setDado('exercicio', $stExercicioLiquidacao);
                        $obTTCEALPagamentoTipoDocumento->setDado('cod_nota', $inCodNota);
                        $obTTCEALPagamentoTipoDocumento->setDado('cod_tipo_documento', $request->get('inDocTipo') );
                        $obTTCEALPagamentoTipoDocumento->setDado('timestamp',$stTimestamp );
                        $obTTCEALPagamentoTipoDocumento->setDado('num_documento', $request->get('nuDoc') );
                        $obErro = $obTTCEALPagamentoTipoDocumento->inclusao($boTransacao);
                        if ($obErro->ocorreu()) {
                            SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                            SistemaLegado::LiberaFrames();
                        }
                    }
                }//fim TCEAL
            }
            
            if ($stSiglaUf == "TO") {
                if ( !$obErro->ocorreu() ) {
                    if ( SistemaLegado::pegaConfiguracao("cod_uf", 2, Sessao::getExercicio(), $boTransacao) == 27 ) {
                        require_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOPagamentoTipoPagamento.class.php";

                        $obTTCETOPagamentoTipoPagamento = new TTCETOPagamentoTipoPagamento;
                        $obTTCETOPagamentoTipoPagamento->setDado('cod_entidade', $arrNota['cod_entidade']);
                        $obTTCETOPagamentoTipoPagamento->setDado('exercicio', $stExercicioLiquidacao);
                        $obTTCETOPagamentoTipoPagamento->setDado('cod_nota', $inCodNota);
                        $obTTCETOPagamentoTipoPagamento->setDado('timestamp',$stTimestamp );
                        $obTTCETOPagamentoTipoPagamento->setDado('cod_tipo_pagamento', $request->get('inCodTipoPagamento') );
                        $obErro = $obTTCETOPagamentoTipoPagamento->inclusao($boTransacao);
                       
                        if ($obErro->ocorreu()) {
                            SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                            SistemaLegado::LiberaFrames();
                        }
                    }
                }
            }

            if ($stSiglaUf == "BA" && $inCodUf==5 && !$obErro->ocorreu() ) {
                include_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBAPagamentoTipoPagamento.class.php';

                $obTTesourariaPagamento = new TTesourariaPagamento;
                $stFiltro = " WHERE cod_nota = ".$inCodNota." AND exercicio = '".$stExercicioLiquidacao."' AND cod_entidade = ".$arrNota['cod_entidade'];
                $obTTesourariaPagamento->recuperaTodos($rsTesourariaPagamento, $stFiltro, '', $boTransacao);
                
                while (!$rsTesourariaPagamento->eof()) {
                    $obTTCMBAPagamentoTipoPagamento = new TTCMBAPagamentoTipoPagamento;
                    $obTTCMBAPagamentoTipoPagamento->setDado('cod_entidade'   , $rsTesourariaPagamento->getCampo('cod_entidade'));
                    $obTTCMBAPagamentoTipoPagamento->setDado('exercicio'      , $stExercicioLiquidacao);
                    $obTTCMBAPagamentoTipoPagamento->setDado('cod_nota'       , $rsTesourariaPagamento->getCampo('cod_nota'));
                    $obTTCMBAPagamentoTipoPagamento->setDado('timestamp'      , $rsTesourariaPagamento->getCampo('timestamp'));
                    $obTTCMBAPagamentoTipoPagamento->setDado('cod_tipo'       , $request->get('inCodTipoPagamento'));
                    $obTTCMBAPagamentoTipoPagamento->setDado('num_documento'  , $request->get('numDocPagamento'));
                    $obErro = $obTTCMBAPagamentoTipoPagamento->inclusao($boTransacao);
    
                    if ($obErro->ocorreu()) {
                        SistemaLegado::exibeAviso(urlencode("Erro ao executar Pagamento de Origem de Recursos Interna (".$obErro->getDescricao().")"),"","erro");
                        SistemaLegado::LiberaFrames();
                    }
                    
                    $rsTesourariaPagamento->proximo();
                }
            }
        }

        # Encerra Transação para validar o commit ou rollback
        $obErro = $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaPagamento );

        if ( $obRTesourariaConfiguracao->getFormaComprovacao() ) {
            SistemaLegado::alertaAviso($pgAutenticacao."?".( $request->get('boPagarOutra') ? $stPagarOutraAuth : "pg_volta=../pagamentos/".$pgList ),"Pagamento Concluído com Sucesso! (OP ".$inCodOrdem."/".$stExercicioOrdem.")","","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso(($request->get('boPagarOutra') ? $stPagarOutra : $pgList),"Pagamento Concluído com Sucesso! (OP ".$inCodOrdem."/".$stExercicioOrdem.")","","aviso", Sessao::getId(), "../");
        }
    } else {
        $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'), $boTransacao);
        SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
        SistemaLegado::LiberaFrames();
    }

    break;
///////////////////////////////////////////////////////////////////////////////


    case 'alterar':
    if ( $stDtBoletim == date( 'd/m/Y' ) ) {
        $obRTesourariaBoletim->roUltimoPagamento->setTimestampEstornado( date( 'Y-m-d H:i:s.ms' ) );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->stTimestampAnulada = date( 'Y-m-d H:i:s.ms' );
        $stDtEstorno = date( 'Ymd' );
    } else {
        list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
        $obRTesourariaBoletim->roUltimoPagamento->setTimestampEstornado( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->stTimestampAnulada = $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms');
        $stDtEstorno = $stAno.$stMes.$stDia;
    }
    $obErro = new Erro;

    $arrNota = Sessao::read('arNota');

    if ( count($arrNota) > 0 ) {
        $inCount = 0;
        $inPos = 0;
        $nuTotalEstorno;
        $stMaiorData = 0;
        $nuTotalEstorno = 0;

        foreach ($arrNota as $arNota) {
            list( $inCodEmpenho, $stExercicioEmpenho    ) = explode( '/', $arNota['empenho'] );
            list( $inCodNota   , $stExercicioLiquidacao ) = explode( '/', $arNota['liquidacao'] );

            $nuValorPagar       = str_replace(',','.',str_replace('.','',$request->get('nuValorPagar_'.($inCount+1))    ));  /// Valor do Estorno
            $nuValorEstornar    = $arNota['vl_estornar'];  // Valor Prestado Contas
            $nuValorOriginal    = str_replace(',','.',str_replace('.','',$request->get('nuValorOriginal_'.($inCount+1)) ));

            if ($nuValorEstornar != $nuValorOriginal) {
                $nuValorOriginal = $nuValorEstornar;
            }
            if ($nuValorPagar > $nuValorOriginal) {
                $obErro->setDescricao("O valor a estornar da nota ".$inCodNota." não pode ser superior a R$ ".number_format($nuValorOriginal,2,',','.').".");
                break;
            }

            if ($nuValorPagar > 0.00) {
                $arNotaPaga[$inPos]['cod_nota']           = $inCodNota;
                $arNotaPaga[$inPos]['exercicio']          = $stExercicioLiquidacao;
                $arNotaPaga[$inPos]['cod_entidade']       = $arNota['cod_entidade'];
                $arNotaPaga[$inPos]['cod_empeho']         = $inCodEmpenho;
                $arNotaPaga[$inPos]['ex_empenho']         = $stExercicioEmpenho;
                $arNotaPaga[$inPos]['dt_nota']            = $stDtBoletim;
                $arNotaPaga[$inPos]['timestamp']          = $arNota['timestamp'];
                $arNotaPaga[$inPos]['vl_estornado']       = $nuValorPagar;
                $arNotaPaga[$inPos]['vl_pago']            = $request->get('nuValorPagamento');
                $arNotaPaga[$inPos]['cod_plano']          = $arNota['cod_plano'];
                $arNotaPaga[$inPos]['cod_plano_retencao'] = $arNota['cod_plano_retencao'];
                $arNotaPaga[$inPos]['exercicio_plano']    = $arNota['exercicio_plano'];
                $nuTotalEstorno = $nuTotalEstorno + $nuValorPagar;
                $inPos++;
                $arData = explode('/',$arNota['dt_pagamento']);
                $stData = $arData[2].$arData[1].$arData[0];
                if ($stData >= $stMaiorData) {
                    $stMaiorData = $stData;
                    $dtMaiorData = $arData[0].'/'.$arData[1].'/'.$arData[2];
                }
            }
            $inCount++;
        }

        if (!$nuTotalEstorno) {
            $nuTotalEstorno = $nuValorPagar;
        }
        if ($nuTotalEstorno > 0.00) {
            if ($stDtEstorno >= $stMaiorData) {
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem   ( $request->get('inCodOrdem')       );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio     ( $request->get('stExercicioOrdem')         );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setValorAnulado  ( number_format( $request->get('nuValorPagamento'), 2, ',', '.' ) );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($request->get('inCodEntidade'));
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setDataPagamento( $dtMaiorData ); // data de pgto mais recente das notas com valor a estornar
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setObservacao( $request->get('stMotivo') );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setValoresPagos( $arNotaPaga );
            } else {
                $obErro->setDescricao('A data do boletim deve ser igual ou superior à data do pagamento mais recente com valor a estornar informado ('.$dtMaiorData.').');
            }
        } else {
            $obErro->setDescricao("O valor a estornar deve ser maior que 0,00.");
        }
    }

    if (!$obErro->ocorreu()) {
        Sessao::setTrataExcecao ( true );
        $obErro = $obRTesourariaBoletim->roUltimoPagamento->estornar( $boTransacao ); 
        Sessao::encerraExcecao();
    }

    $boRetencao = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->getRetencao();
    if ($obRTesourariaBoletim->roUltimoPagamento->obRTesourariaAutenticacao->getDescricao()) {
        Sessao::write('pagamento',true);
    }

    $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'), $boTransacao);

    if ( !$obErro->ocorreu() ) {
        if( $obRTesourariaConfiguracao->getFormaComprovacao() )
            SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../pagamentos/".$pgList."&".Sessao::getId(),"Estorno de Pagamento Concluído com Sucesso! (OP: ".$request->get('inCodOrdem') . "/" . Sessao::getExercicio().")","","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,"Estorno de Pagamento Concluído com Sucesso! (OP: ".$request->get('inCodOrdem') . "/" . Sessao::getExercicio().")","","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"n_estornar","erro");
        SistemaLegado::LiberaFrames();
    }

    break;

}

?>
