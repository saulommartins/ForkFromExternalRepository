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
    * Página de processamento para calculo
    * Data de criação : 05/12/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Marcelo Boezzio Paulino

    * $Id: PRManterBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.11
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                            );
include_once( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php");
include_once( CAM_GT_ARR_NEGOCIO."RARRCarneConsolidacao.class.php");

$stAcao = $_REQUEST['stAcao'];

//Define o nome dos arquivos PHP
$stPrograma    = "ManterBaixaManual";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$obErro          = new Erro;
$obRARRPagamento = new RARRPagamento;
$obRARRConfiguracao = new RARRConfiguracao;

switch ($stAcao) {
    case "incluir":
        $rsRecordSet = new RecordSet;
        $obRARRCarneConsolidacao = new RARRCarneConsolidacao;
        $obRARRCarneConsolidacao->setCodConvenio($_REQUEST['inCodConvenio']);
        $obRARRCarneConsolidacao->setNumeracaoConsolidacao($_REQUEST['inNumeracao']);
        $obErro = $obRARRCarneConsolidacao->listarNumeracaoCarne($rsRecordSet);

        $arNumeracoes = array();
        if ($rsRecordSet->getNumLinhas() > 0) {
            $arNumeracoes = $rsRecordSet->getElementos();
        } else {
            $arNumeracoes[0]['numeracao'] = $_REQUEST['inNumeracao'];
            $arNumeracoes[0]['cod_convenio'] = $_REQUEST['inCodConvenio'];
        }

        if (!$obErro->ocorreu()) {
            foreach ($arNumeracoes as $arNumeracao) {
                $arTipo = explode( "-", $_REQUEST["stTipo"] );
                if ( ( $_REQUEST['nuValorPagamento'] == "" ) && ($arTipo[1] != 'f' ) ) {
                    SistemaLegado::exibeAviso(urlencode( "Campo Valor Inválido!"),"n_erro","erro",Sessao::getId(), "../" );
                    exit;
                }

                if ($arNumeracao["cod_convenio"] == -1) {
                    $arDtVenc = explode( "/", $_REQUEST["dtVencimento"] );
                    $arDtPag = explode( "/", $_REQUEST["dtPagamento"] );
                    if ($arDtPag[2].$arDtPag[1].$arDtPag[0] > $arDtVenc[2].$arDtVenc[1].$arDtVenc[0]) {
                        $arMes = array (31,28,31,30, 31,30,31,31,30,31,30,31);
                        //tentando pagar apos vencimento
                        $obRARRConfiguracao->consultar();
                        $inQtdDiasVenc = $obRARRConfiguracao->getBaixaManualDAVencida();
                        $m = $arDtVenc[1];
                        $d = $arDtVenc[0];
                        $y = $arDtVenc[2];

                        for ($inX=0; $inX<=$inQtdDiasVenc; $inX++) {
                            if ($d+1 > $arMes[$m-1]) {
                                $d = 1;
                                if ($m+1 > 12) {
                                    $m = 1;
                                    $y++;
                                }else
                                    $m++;
                            }else
                                $d++;
                        }

                        $boPrimeira = true;
                        while ( !checkdate($m,$d,$y) ) {
                            if ($boPrimeira) {
                                $boPrimeira = false;
                                $d = 1;
                                if ($m+1 > 12) {
                                    $m = 1;
                                    $y++;
                                }else
                                    $m++;
                            } else {
                                $d++;
                            }
                        }

                        $arDtVenc[1] = sprintf( "%02d", $m );
                        $arDtVenc[0] = sprintf( "%02d", $d );
                        $arDtVenc[2] = $y;

                        if ($arDtPag[2].$arDtPag[1].$arDtPag[0] > $arDtVenc[2].$arDtVenc[1].$arDtVenc[0]) {
                            SistemaLegado::exibeAviso("Não é possível executar a baixa de um carne da dívida ativa vencido!","n_erro","erro",Sessao::getId(), "../" );
                            exit;
                        }
                    }
                }

                $nuValorPagamento = str_replace( ".", "", $_REQUEST['nuValorPagamento'] );
                $nuValorPagamento = str_replace( ",", ".", $nuValorPagamento );
                $obRARRPagamento->setDataPagamento                  ( $_REQUEST['dtPagamento']  );
                $obRARRPagamento->setObservacao                     ( $_REQUEST['stObservacao'] );
                $obRARRPagamento->setValorPagamento                 ( $nuValorPagamento         );
                $obRARRPagamento->obRARRCarne->setNumeracao         ( $arNumeracao['numeracao']  );
                $obRARRPagamento->obRARRCarne->setExercicio         ( $_REQUEST['stExercicio']  );
                $obRARRPagamento->obRARRCarne->obRMONConvenio->setCodigoConvenio( $arNumeracao['cod_convenio'] );
                $arTipoPagamento = explode( "-" , $_REQUEST['stTipo'] );
                $obRARRPagamento->obRARRTipoPagamento->setCodigoTipo( $arTipoPagamento[0] );
                $obRARRPagamento->obRARRTipoPagamento->setPagamento ( $arTipoPagamento[1] );
                if ($arTipoPagamento[1] == 'f') {
                    $obRARRPagamento->setValorPagamento( 0 );
                }else
                    if ( ( $_REQUEST['inCodConvenio'] == -1 ) && ( $nuValorPagamento < $_REQUEST["nuValorTotal"] ) ) {
                        SistemaLegado::exibeAviso("Não é possível executar a baixa de um carne da dívida ativa com valor a menor!","n_erro","erro",Sessao::getId(), "../" );
                        exit;
                    }

                 // buscar cod_agencia e cod_banco
                $obRMONAgencia = new RMONAgencia();
                $obRMONAgencia->setNumAgencia            ( $_REQUEST['inNumAgencia'] );
                $obRMONAgencia->obRMONBanco->setNumBanco ( $_REQUEST['inNumBanco']   );
                $obRMONAgencia->listarAgencia($rsAgencia);

                $obRARRPagamento->obRMONAgencia->setCodAgencia      ( $rsAgencia->getCampo( 'cod_agencia') );
                $obRARRPagamento->obRMONBanco->setCodBanco          ( $rsAgencia->getCampo( 'cod_banco'  ) );

                if ($_REQUEST['inProcesso']) {
                    $arProcesso = explode( "/" , $_REQUEST['inProcesso'] );
                    $obRARRPagamento->obRProcesso->setCodigoProcesso( $arProcesso[0] );
                    $obRARRPagamento->obRProcesso->setExercicio( $arProcesso[1] );
                }

                // if ($_REQUEST['boFechaBaixa']) {
                    $boFecha = TRUE;
                    $dtdiaHOJE = date ("d-m-Y");
                    $obRARRPagamento->setDataLote( $dtdiaHOJE );
                    $obRARRPagamento->setExercicio( Sessao::getExercicio() );
                // }else
                //    $boFecha = FALSE;

                $obErro = $obRARRPagamento->efetuarPagamentoManual('', FALSE, $boFecha, $inTotal);
                if ( $obErro->ocorreu() ) {
                    SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
                }
            }
            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso( $pgList."?stAcao=incluir","Numeração: ".$arNumeracao['numeracao'],"incluir","aviso", Sessao::getId(), "../" );
            }
        } else {
            SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
        }
        break;

    case "fechar":
        $dtdiaHOJE = date ("d-m-Y");
        $obRARRPagamento->setDataLote( $dtdiaHOJE );
        if (!$_REQUEST['inTodosBancos']) {
            if ($_REQUEST['inNumbanco'] == "") {
                SistemaLegado::exibeAviso("Campo 'Banco' vazio!", "n_erro", "erro", Sessao::getId(), "../" );
                exit();
            }else
            if ($_REQUEST['inNumAgencia'] == "") {
                SistemaLegado::exibeAviso("Campo 'Agência' vazio!", "n_erro", "erro", Sessao::getId(), "../" );
                exit();
            }

            $obRMONAgencia = new RMONAgencia();
            $obRMONAgencia->setNumAgencia            ( $_REQUEST['inNumAgencia'] );
            $obRMONAgencia->obRMONBanco->setNumBanco ( $_REQUEST['inNumBanco']   );
            $obRMONAgencia->listarAgencia($rsAgencia);

            $obRARRPagamento->obRMONAgencia->setCodAgencia              ( $rsAgencia->getCampo( 'cod_agencia') );
            $obRARRPagamento->obRMONBanco->setCodBanco                  ( $rsAgencia->getCampo( 'cod_banco'  ) );

            $obRARRPagamento->setExercicio( Sessao::getExercicio() );

//            $obRARRPagamento->listarPagamentosManuaisAFechar( $rsLista );

            $obErro = $obRARRPagamento->efetuarFechamentoManual();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso("LSResumoFechamentoBaixaManual.php?stAcao=fechar", "Fechamento" ,"incluir","aviso", Sessao::getId(), "../" );

/*                $stMsg = " Lote: ".$obRARRPagamento->inCodLote;
                $stMsg.= " Banco: ".$rsAgencia->getCampo('num_banco')." - ".$rsAgencia->getCampo('nom_banco');
                $stMsg.= " Agência: ".$rsAgencia->getCampo('num_agencia')." - ".$rsAgencia->getCampo('nom_agencia');

                SistemaLegado::alertaAviso( "FMResumoFechamentoBaixaManual.php?stAcao=incluir&cod_lote=".$obRARRPagamento->inCodLote."&exercicio=".Sessao::getExercicio()."&pagamento=".$rsLista->getCampo("pagamento"), $stMsg, "incluir", "aviso", Sessao::getId(), "../" );
*/
            } else {
                SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
            }
        } else {
            $obRARRPagamento->setExercicio( Sessao::getExercicio());
            $obErro = $obRARRPagamento->efetuarFechamentoManualTodosBancos();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso("LSResumoFechamentoBaixaManual.php?stAcao=fechar", "Fechamento" ,"incluir","aviso", Sessao::getId(), "../" );
            } else {
                SistemaLegado::exibeAviso(urlencode( $obErro->getDescricao()),"n_erro","erro",Sessao::getId(), "../" );
            }
        }
    break;
}
