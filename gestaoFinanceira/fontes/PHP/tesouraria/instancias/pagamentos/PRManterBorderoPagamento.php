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
    * Data de Criação   : 10/01/2006

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 30835 $
    $Name$
    $Author: cako $
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"      );
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterBorderoPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgAutenticacao = "../autenticacao/FMManterAutenticacao.php";

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
$obRTesourariaConfiguracao->consultarTesouraria();

switch ($stAcao) {
    case 'incluir':
        include_once ( CAM_FW_BANCO_DADOS    ."Transacao.class.php"            );
        $obTransacao = new Transacao();
        $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        $obErro = new Erro;

        list ( $inCodBoletim , $stDtBoletim , $stExercicioBoletim , $inCodEntidadeBoletim ) =  explode ( ':' , $_REQUEST['inCodBoletim']);

        $obRTesourariaBoletim = new RTesourariaBoletim;

        $obRTesourariaBoletim->setCodBoletim($inCodBoletim);
        $obRTesourariaBoletim->setExercicio($_REQUEST['stExercicio']);
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST['inCodTerminal']);
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST['stTimestampTerminal']);
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->setNumCgm(Sessao::read('numCgm'));
        $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST['stTimestampUsuario']);
        $obRTesourariaBoletim->setDataBoletim($stDtBoletim);

        $obRTesourariaBoletim->addBordero();

        $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade'] );
        $obRTesourariaBoletim->roUltimoBordero->obRContabilidadePlanoBanco->setCodPlano($_REQUEST['inCodConta'] );
        $obRTesourariaBoletim->roUltimoBordero->roRTesourariaBoletim->setCodBoletim($inCodBoletim );
        $obRTesourariaBoletim->roUltimoBordero->setExercicio($_REQUEST['stExercicio'] );
        $obRTesourariaBoletim->roUltimoBordero->roRTesourariaBoletim->setExercicio( $stExercicioBoletim );
        $obRTesourariaBoletim->roUltimoBordero->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setCodTerminal($_REQUEST['inCodTerminal']);
        $obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm') );
        $obRTesourariaBoletim->roUltimoBordero->obRTesourariaUsuarioTerminal->roRTesourariaTerminal->setTimestampTerminal($_REQUEST['stTimestampTerminal'] );
        $obRTesourariaBoletim->roUltimoBordero->obRTesourariaUsuarioTerminal->setTimestampUsuario($_REQUEST['stTimestampUsuario'] );

        $obRTesourariaBoletim->roUltimoBordero->buscaProximoCodigo( $boTransacao );

        $inCodBordero = $obRTesourariaBoletim->roUltimoBordero->inCodBordero;

        $arrItens = Sessao::read('arItens');

        if ( count( $arrItens) > 0 ) {

            foreach ($arrItens as $arItens) {
                if ( $stDtBoletim == date( 'd/m/Y' ) ) {
                    $obRTesourariaBoletim->roUltimoBordero->setTimestampBordero( date( 'Y-m-d H:i:s.ms' ) );
                } else {
                    list( $stDia, $stMes, $stAno ) = explode( '/', $stDtBoletim );
                    $obRTesourariaBoletim->roUltimoBordero->setTimestampBordero( $stAno.'-'.$stMes.'-'.$stDia.' '.date('H:i:s.ms') );
                }

                $historico = "Pagamento de empenho(s) conforme Borderô nr. ".$arItens['inCodigoEntidade']."-".$inCodBordero."/".$_REQUEST['stExercicio'];

                $obRTesourariaBoletim->roUltimoBordero->addTransacaoPagamento();
                $obRTesourariaBoletim->addPagamento();
                $obRTesourariaBoletim->roUltimoPagamento->setTimestamp( $obRTesourariaBoletim->roUltimoBordero->getTimestampBordero() );

                if (SistemaLegado::comparaDatas($arItens['stDtEmissaoOrdem'],$stDtBoletim)) {
                    $obErro->setDescricao("A data do pagamento é anterior à data de emissão da OP");
                }
                $stCodOrdem .= $arItens['inNumOrdemPagamentoCredor'].",";
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdem   ( $arItens['inNumOrdemPagamentoCredor']        );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setExercicio     ( $arItens['stExercicioOrdem']          );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setDataVencimento( '31/12/'.Sessao::getExercicio() );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($arItens['inCodigoEntidade']);
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setCodPlano ( $_REQUEST['inCodConta'] );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio()   );
                $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setObservacao( $historico );
                $obErro = $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->listarItensPagamento ( $rsRecordSet );
                
                if (!$obErro->ocorreu()) {
                    $inCount = 0;
                    $arNotas = $rsRecordSet->getElementos();
                    foreach ($arNotas as $arNota) {
                        $inCodEmpenho           = $arNota['cod_empenho'];
                        $inCodNota              = $arNota['cod_nota'];
                        $nuValorPagar           = $arNota['vl_pagamento'];
                        $stExercicioLiquidacao  = $arNota['ex_nota'];
                        $stExercicioEmpenho     = $arNota['ex_empenho'];
                        $nuValorPagar           = $arNota['vl_pagamento'];

                        if (SistemaLegado::comparaDatas($arNota['dt_nota'],$stDtBoletim,true)) {
                            $obErro->setDescricao("A data do pagamento é anterior à data da liquidaçao");
                        }

                        $arNotaLiquidacao[$inCount]['cod_nota']        = $inCodNota;
                        $arNotaLiquidacao[$inCount]['ex_nota' ]        = $stExercicioLiquidacao;
                        $arNotaLiquidacao[$inCount]['cod_empeho']      = $inCodEmpenho;
                        $arNotaLiquidacao[$inCount]['ex_empenho']      = $stExercicioEmpenho;
                        $arNotaLiquidacao[$inCount]['dt_nota']         = $stDtBoletim;
                        $arNotaLiquidacao[$inCount]['valor_pagar']     = $nuValorPagar;
                        $arNotaLiquidacao[$inCount]['max_valor_pagar'] = bcadd($nuValorPagar,$arNota['vl_pago'],2);

                        $arNotaPaga[$inCount]['cod_nota']     = $inCodNota;
                        $arNotaPaga[$inCount]['exercicio']    = $stExercicioLiquidacao;
                        $arNotaPaga[$inCount]['cod_entidade'] = $arNota['cod_entidade'];
                        $arNotaPaga[$inCount]['cod_empeho']   = $inCodEmpenho;
                        $arNotaPaga[$inCount]['ex_empenho']   = $stExercicioEmpenho;
                        $arNotaPaga[$inCount]['dt_nota']      = $stDtBoletim;
                        $arNotaPaga[$inCount]['vl_pago']      = $nuValorPagar;
                        $arNotaPaga[$inCount]['vl_a_pagar']   = $nuValorPagar + $arNota['vl_pago'];
                        $inCount++;
                    }
                    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setNotaLiquidacao( $arNotaLiquidacao );
                    $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setValoresPagos( $arNotaPaga );
                }
                if (!$obErro->ocorreu()) {

                    $obErro = $obRTesourariaBoletim->roUltimoPagamento->pagar( $boTransacao );

                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->setCodOrdem ($arItens['inNumOrdemPagamentoCredor']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->obROrcamentoEntidade->setCodigoEntidade($arItens['inCodigoEntidade']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->roRTesourariaBordero->setExercicio($_REQUEST['stExercicio']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->setTipo($arItens['stTipoTransacaoCredor']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->obRMONBanco->setCodBanco($arItens['inCodBancoCredor']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->setCodAgencia($arItens['inCodAgenciaCredor']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->setContaCorrente($arItens['stNumeroContaCredor']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->setNumDocumento($arItens['stNrNFDocumento']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->setDescricao($arItens['stObservacao']);
                    $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setValorPagamento($arItens['inValor']);
                }
            }
        }

        $obRTesourariaBoletim->roUltimoBordero->addAssinatura();

        $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio($_REQUEST['stExercicio']);
        $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');

        for ($x=1; $x<=3; $x++) {

            if ($_REQUEST["inNumAssinante_".$x]) {

                $obRTesourariaBoletim->roUltimoBordero->addAssinatura();

                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->obRCGM->setNumCGM($_REQUEST["inNumAssinante_".$x]);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio($_REQUEST['stExercicio']);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setCargo($_REQUEST["stCargo_".$x]);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setNumMatricula($_REQUEST["inNumMatricula_".$x]);
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setSituacao(true);

            }
        }
        if (!$obErro->ocorreu()) {
            $obErro = $obRTesourariaBoletim->roUltimoBordero->incluirTransacaoPagamentoAssinatura( $boTransacao );
        }

        if($obRTesourariaBoletim->roUltimoBordero->obRTesourariaAutenticacao->getDescricao())
            Sessao::write('stDescricao', $obRTesourariaBoletim->roUltimoBordero->obRTesourariaAutenticacao->getDescricao());

        $nomAcao = SistemaLegado::pegaDado("nom_acao","administracao.acao"," where cod_acao = ".Sessao::read('acao'));
        if ( !$obErro->ocorreu() ) {

            if( $obRTesourariaConfiguracao->getFormaComprovacao() )
                SistemaLegado::alertaAviso($pgAutenticacao."?pg_volta=../pagamentos/".$pgForm."?".Sessao::getId()."&stAcao=incluir", "Ação ".$nomAcao." concluída com sucesso! (".$obRTesourariaBoletim->roUltimoBordero->getCodBordero() ."/". Sessao::getExercicio().")","","aviso", Sessao::getId(), "../");
            else
                SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir", "Ação ".$nomAcao." concluída com sucesso! (".$obRTesourariaBoletim->roUltimoBordero->getCodBordero() ."/". Sessao::getExercicio().")","","aviso", Sessao::getId(), "../");

            $stCodOrdem = substr($stCodOrdem,0,strlen($stCodOrdem)-1);
            
	    $stCaminho = CAM_GF_TES_INSTANCIAS."pagamentos/OCRelatorioBordero.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho;
            $stCampos .= "&inCodBordero=".$obRTesourariaBoletim->roUltimoBordero->getCodBordero();
            $stCampos .= "&stExercicio=".$obRTesourariaBoletim->roUltimoBordero->getExercicio();
            $stCampos .= "&inCodEntidade=".$obRTesourariaBoletim->roUltimoBordero->obROrcamentoEntidade->getCodigoEntidade();
            $stCampos .= "&stTipoBordero=P";
            $stCampos .= "&stCodOrdem=".$stCodOrdem;
            
            // Passagem de parametros para a geração do relatório conforme o exercicio logado
            if ( Sessao::getExercicio() >= '2015' )
                Sessao::write('relatorioBordero', $_REQUEST);
                
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaBorderoPagamento );
            
        } else {
            SistemaLegado::exibeAviso(urlencode("Erro ao executar ação: ".$nomAcao." (".$obErro->getDescricao().")"),"","erro");
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
        }

    break;

    case 'alterar':

    break;
}

?>