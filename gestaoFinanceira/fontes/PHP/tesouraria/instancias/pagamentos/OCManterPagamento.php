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
    * Paginae Oculta para funcionalidade Manter Pagamento
    * Data de Criação   : 26/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCManterPagamento.php 63464 2015-08-31 17:30:39Z michel $

    $Revision: 31732 $
    $Name$
    $Author: luciano $
    $Date: 2007-08-27 16:06:27 -0300 (Seg, 27 Ago 2007) $

    * Casos de uso: uc-02.04.05,uc-02.03.28

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoBanco.class.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaBoletim.class.php';
include_once CAM_GF_TES_NEGOCIO.'RTesourariaSaldoTesouraria.class.php';
include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaChequeEmissaoOrdemPagamento.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALPagamentoTipoDocumento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();

function montaLista($arRecordSet, $boExecuta = true)
{
    global $_REQUEST;

    if ($_REQUEST['boAdiantamento']) {
        $boAdiantamento = true;
    } else {
        $boAdiantamento = false;
    }

    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao( 'vl_total', 'NUMERIC_BR' );
    $rsLista->addFormatacao( 'vl_estornar', 'NUMERIC_BR' );

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Dt. Empenho");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Liquidação");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Dt. Liquid.");
    $obLista->ultimoCabecalho->setWidth( 17 );
    $obLista->commitCabecalho();
    if ($_REQUEST['stAcao'] == "incluir") {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor a Pagar");
        $obLista->ultimoCabecalho->setWidth( 27 );
        $obLista->commitCabecalho();
    } elseif ($_REQUEST['stAcao'] == "alterar") {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Conta");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Dt. Pagamento");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Pago");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor a Estornar");
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();

    }
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("");
    $obLista->ultimoCabecalho->setWidth( 1 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "empenho" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_empenho" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "liquidacao" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_liquidacao" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obTxtValor = new Numerico;
    $obTxtValor->setName     ( "nuValorPagar_[num_item]" );
    $obTxtValor->setAlign    ( "RIGHT");
    $obTxtValor->setTitle    ( "" );
    $obTxtValor->setMaxLength( 19 );
    $obTxtValor->setSize     ( 20 );
    $obTxtValor->setNegativo ( false );
    $obTxtValor->setNull     ( false );
    if (Sessao::read('arCheque')) {
        $obTxtValor->setLabel(true);
    }
    if (Sessao::read('retencao') || $boAdiantamento) {
        $obTxtValor->setReadOnly ( true );
    }

    if ($_REQUEST['stAcao'] == "incluir") {
        $obTxtValor->setValue    ( "vl_total" );
    }

    if ($_REQUEST['stAcao'] == "alterar") {

        $obTxtValor->setValue ( "vl_estornar" );

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_plano" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dt_pagamento" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_total" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

    }

    $obLista->addDadoComponente( $obTxtValor );
    $obLista->ultimoDado->setAlinhamento( "DIREITA" );
    $obLista->commitDadoComponente();

    $obHdnValorOriginal = new Hidden();
    $obHdnValorOriginal->setName( "nuValorOriginal_[num_item]" );
    $obHdnValorOriginal->setValue( "vl_total" );

    $obLista->addDadoComponente( $obHdnValorOriginal );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDadoComponente();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if (Sessao::read('retencao')) {
        if($_REQUEST['stAcao'] == 'incluir')
            $stJS = "alertaAviso('Esta OP possui retenções: O Pagamento não poderá ser parcial.','','','".Sessao::getId()."');";
        else if ($_REQUEST['stAcao'] == 'alterar')
            $stJS = "alertaAviso('Esta OP possui retenções: O Estorno não poderá ser parcial.','','','".Sessao::getId()."');";
    }

    if ($boAdiantamento) {
        if($_REQUEST['stAcao'] == 'incluir')
            $stJS = "alertaAviso('Esta OP é de adiantamentos/subvenções: O Pagamento não poderá ser parcial.','','','".Sessao::getId()."');";
        else if ($_REQUEST['stAcao'] == 'alterar')
            $stJS = "alertaAviso('Esta OP é de adiantamentos/subvenções: O Estorno não poderá ser parcial.','','','".Sessao::getId()."');";
    }

    if ($boExecuta) {
        if (isset($stJS)) {
            SistemaLegado::executaFrameOculto($stJS);
        }
        SistemaLegado::executaFrameOculto("d.getElementById('spnItem').innerHTML = '".$stHTML."';");
    } else {
        return $stHTML;
    }
}

switch ($stCtrl) {
    case 'alteraBoletim':
        $obRTesourariaBoletim = new RTesourariaBoletim();
        list( $inCodBoletim , $stDataBoletim ) = explode( ':' , $_REQUEST['inCodBoletim'] );
        $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obErro = $obRTesourariaBoletim->listarBoletimAberto ( $rsBoletimAberto );

        if ( !$obErro->ocorreu() && $rsBoletimAberto->getNumLinhas() == 1 ) {
            $inCodigoBoletim = $rsBoletimAberto->getCampo( 'cod_boletim' ). ":" . $rsBoletimAberto->getCampo( 'dt_boletim' ).":".$rsBoletimAberto->getCampo( 'exercicio' ).":".$rsBoletimAberto->getCampo('cod_entidade');
            $stJs  = "f.inCodBoletim.value = '" . $inCodigoBoletim . "';\r\n";
            $stJs .= "f.stDtBoletim.value = '" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        } else {
            $stJs  = "f.inCodBoletim.value = '';\r\n";
            $stJs .= "f.stDtBoletim.value = '';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        }
        exit;
    break;
    case 'montaItem':
        $arNota = Sessao::read('arNota');
        montaLista( $arNota );
    break;
    case 'montaDocumento':
        switch ($_POST['inDocTipo']) {
            case 1:
            case 2:
            case 3:
            case 99:
                $obTxtValor = new TextBox;
                $obTxtValor->setName     ( "nuDoc"   );
                $obTxtValor->setId       ( "nuDoc"   );
                $obTxtValor->setValue    ( $nuDoc    );
                $obTxtValor->setRotulo   ( "Numero"     );
                $obTxtValor->setTitle    ( "Informe o Número." );
                $obTxtValor->setDecimais ( 0                );
                $obTxtValor->setinteiro (true );
                $obTxtValor->setNull     ( false             );
                $obTxtValor->setSize     ( 15               );
                $obTxtValor->setMaxLength( 15               );

                $obForm = new Formulario();
                $obForm->addComponente($obTxtValor);
                $obForm->montaInnerHTML();
                $stHTML = $obForm->getHtml();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\'","\\'",$stHTML );

                SistemaLegado::executaFrameOculto("d.getElementById('spnDocumento').innerHTML = '".$stHTML."';");           break;
           case  4:
                SistemaLegado::executaFrameOculto("d.getElementById('spnDocumento').innerHTML = '';");
           break;
           case  5:
               $nuDoc = '999999999999999';
               $obHdnNumDoc = new Hidden;
               $obHdnNumDoc->setName  ( "nuDoc" );
               $obHdnNumDoc->setValue ( $nuDoc );

               $obForm = new Formulario();
               $obForm->addHidden($obHdnNumDoc);
               $obForm->montaInnerHTML();
               $stHTML = $obForm->getHtml();

               $stHTML = str_replace( "\n" ,"" ,$stHTML );
               $stHTML = str_replace( "  " ,"" ,$stHTML );
               $stHTML = str_replace( "'","\\'",$stHTML );
               $stHTML = str_replace( "\\\'","\\'",$stHTML );

               SistemaLegado::executaFrameOculto("d.getElementById('spnDocumento').innerHTML = '".$stHTML."';");
           break;
       }
    break;
##########
    case 'montaDocumentoTcemg':
        switch ($_POST['inDocTipo']) {
            case 2:
            case 3:
            case 4:
            case 99:
                $obTxtValor = new TextBox;
                $obTxtValor->setName     ( "nuDoc"  );
                $obTxtValor->setId       ( "nuDoc"  );
                $obTxtValor->setValue    ( $nuDoc   );
                $obTxtValor->setRotulo   ( "Numero" );
                $obTxtValor->setTitle    ( "Informe o Número." );
                $obTxtValor->setDecimais ( 0 );
                $obTxtValor->setinteiro  ( true );
                $obTxtValor->setNull     ( false );
                $obTxtValor->setSize     ( 15 );
                $obTxtValor->setMaxLength( 15 );

                $obForm = new Formulario();
                $obForm->addComponente($obTxtValor);
                $obForm->montaInnerHTML();
                $stHTML = $obForm->getHtml();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\'","\\'",$stHTML );

                SistemaLegado::executaFrameOculto("d.getElementById('spnDocumento').innerHTML = '".$stHTML."';");
            break;
            case 1:
                $idCodOrdem       = $_REQUEST['inCodOrdem'];
                $stExercicioOrdem = $_REQUEST['stExercicioOrdem'];
                $inCodEntidade    = $_REQUEST['inCodEntidade'];
                $rsRecordset      = new RecordSet;

                if (($idCodOrdem != '') && ($stExercicioOrdem != '')) {
                    $obTTesourariaChequeEmissaoOrdemPagamento = new TTesourariaChequeEmissaoOrdemPagamento;
                    $obTTesourariaChequeEmissaoOrdemPagamento->setDado('cod_ordem', $idCodOrdem);
                    $obTTesourariaChequeEmissaoOrdemPagamento->setDado('exercicio', $stExercicioOrdem);
                    $obTTesourariaChequeEmissaoOrdemPagamento->recuperaTodos($rsRecordset, " WHERE cod_ordem=".$idCodOrdem." AND exercicio='".$stExercicioOrdem."' AND cod_entidade =".$inCodEntidade);
                }

                if ($rsRecordset->inNumLinhas < 0) {
                    $inNumCheque = '';
                } else {
                    $inNumCheque = $rsRecordset->getCampo('num_cheque');
                }

                $obTxtValor = new TextBox;
                $obTxtValor->setName     ( "nuDoc" );
                $obTxtValor->setId       ( "nuDoc" );
                $obTxtValor->setValue    ( $inNumCheque );
                $obTxtValor->setRotulo   ( "Numero" );
                $obTxtValor->setTitle    ( "Informe o Número." );
                $obTxtValor->setDecimais ( 0 );
                $obTxtValor->setinteiro  ( true );
                $obTxtValor->setNull     ( false );
                $obTxtValor->setSize     ( 15 );
                $obTxtValor->setMaxLength( 15 );

                $obForm = new Formulario();
                $obForm->addComponente($obTxtValor);
                $obForm->montaInnerHTML();
                $stHTML = $obForm->getHtml();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\'","\\'",$stHTML );

                SistemaLegado::executaFrameOculto("d.getElementById('spnDocumento').innerHTML = '".$stHTML."';");
            break;
        }
    break;
##########
    case 'verificaFornecedor':

        list( $inCodBoletim , $stDataBoletim ) = explode( ':' , $_REQUEST['inCodBoletim'] );

        if ($_REQUEST['boAdiantamento'] && $stDataBoletim) {
            include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php");
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado('exercicio',Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado('numcgm',$_REQUEST['inNumCGM']);
            $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida',$_REQUEST['inCodContrapartida']);
            $obTEmpenhoResponsavelAdiantamento->consultaEmpenhosFornecedor($rsVerificaEmpenho);

            if ($rsVerificaEmpenho->getNumLinhas() > 0) {
                while (!$rsVerificaEmpenho->eof()) {
                    if (SistemaLegado::comparaDatas($stDataBoletim,$rsVerificaEmpenho->getCampo('dt_prazo_prestacao'))) {
                           $boPendente++;
                    }
                    $rsVerificaEmpenho->Proximo();
                }
                if ($boPendente) {
                    echo " alertaAviso('@O responsável por adiantamento possui prestação de contas pendentes.','form','erro','".Sessao::getId()."'); ";
                } else {
                    echo " alertaAviso('','','','".Sessao::getId()."'); ";
                }
            }
        }

    break;

    case 'preencheCampos':

    if (trim(strlen($_POST['inCodBarrasOP'])) == 20) {

        $obRTesourariaBoletim = new RTesourariaBoletim();

        $inOrdemPagamento = substr($_POST['inCodBarrasOP'],8,6);
        $stExercicio      = "20". substr($_POST['inCodBarrasOP'],14,2);
        $stEntidade       = substr($_POST['inCodBarrasOP'],16,3);

        $obRTesourariaBoletim->setExercicio( $stExercicio );
        $obRTesourariaBoletim->addPagamento();

        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->addNotaLiquidacao();
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $stEntidade );

        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdemInicial( $inOrdemPagamento );
        $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoOrdemPagamento->setCodigoOrdemFinal( $inOrdemPagamento );

        $obRTesourariaBoletim->obRTesourariaConfiguracao->consultarTesouraria();
        if( $obRTesourariaBoletim->obRTesourariaConfiguracao->getFormaComprovacao() != 1 )
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->setNaoListarNota( true );

        if ($stAcao == 'incluir') {
            $obRTesourariaBoletim->roUltimoPagamento->obREmpenhoPagamentoLiquidacao->listarLiquidacaoNaoPaga( $rsDadosPagamento );
        } elseif ($stAcao == 'alterar') {
            $obRTesourariaBoletim->roUltimoPagamento->listarPagamentos( $rsDadosPagamento );
        }

        $exercicio_empenho = explode("/",$rsDadosPagamento->getCampo("empenho"));
        $ordem   = explode("/",$rsDadosPagamento->getCampo("ordem"));

        if ($rsDadosPagamento->getNumLinhas() == -1) {
            $inCodBarrasOP = "";
        } else {
            $inCodBarrasOP = $_POST['inCodBarrasOP'];
        }
        SistemaLegado::executaFrameOculto("
                                            for (var i=0; i<f.inCodEntidadeDisponivel.length; i++) {
                                                if (f.inCodEntidadeDisponivel.options[i].value == '".$rsDadosPagamento->getCampo("cod_entidade")."') {
                                                    f.inCodEntidadeDisponivel.options[i].selected = true;
                                                } else {
                                                    f.inCodEntidadeDisponivel.options[i].selected = false;
                                                }
                                            }
                                            passaItem(f.inCodEntidadeDisponivel,f.inCodEntidade,'selecao');
                                            f.stExercicioEmpenho.value  = '".$exercicio_empenho[1]."'
                                            f.inCodOrdemInicial.value   = '".$ordem[0]."';
                                            f.inCodOrdemFinal.value     = '".$ordem[0]."';
                                            f.inCodBarrasOP.value       = '".$inCodBarrasOP."';
                                         ");
    } else {

        SistemaLegado::executaFrameOculto(" f.inCodBarrasOP.value  = '';" );
    }

    break;

    case 'saldoConta':
        if ($_POST['inCodPlano']) {
            $obRTesourariaSaldoTesouraria = new RTesourariaSaldoTesouraria();
            $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
            $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->setCodPlano ( $_POST['inCodPlano'] );

            $obRTesourariaSaldoTesouraria->obRContabilidadePlanoBanco->listarPlanoContaPagamento( $rsRecordSet );

            if ( $rsRecordSet->getNumLinhas() > 0 ) {
                if ( $_REQUEST['inCodEntidade'] == $rsRecordSet->getCampo("cod_entidade") ) {
                    $stDescricao = $rsRecordSet->getCampo("nom_conta");
                    $obRTesourariaSaldoTesouraria->consultarSaldoTesouraria( $nuVlSaldoContaBanco );

                    $nuVlTotal = 0;
                    $arNota = Sessao::read('arNota');
                    if ( is_array( $arNota ) ) {
                        for ( $x = 1; $x <= count($arNota); $x++ ) {
                            $nuVlPagar = str_replace(".", "", $_REQUEST["nuValorPagar_".$x]);
                            $nuVlPagar = str_replace(",", ".",$nuVlPagar);
                            $nuVlTotal = bcadd( $nuVlTotal, $nuVlPagar, 4 );
                        }
                    }
                    $stJs  = "f.nuSaldoContaBanco.value='".$nuVlSaldoContaBanco."';";
                    $stJs .= "f.nuSaldoContaBancoBR.value='".number_format($nuVlSaldoContaBanco,'2',',','.')."';";

                    $stJs .= "d.getElementById( 'stNomConta' ).innerHTML='".$stDescricao."';";
                    if ($nuVlTotal > $nuVlSaldoContaBanco) {
                        $stJs .= "alertaAviso('O saldo da conta informada é inferior a do pagamento! Se continuar o saldo da conta ficará negativo.','','erro','".Sessao::getId()."'); \n";
                    }
                } else {
                    $stJs .= "f.inCodPlano.value = '';";
                    $stJs .= "d.getElementById( 'stNomConta' ).innerHTML='&nbsp;';";
                    $stJs .= "alertaAviso('A entidade da conta é diferente da entidade informada! (".$_POST['inCodPlano']." - ".$rsRecordSet->getCampo('nom_conta').")','','erro','".Sessao::getId()."'); \n";
                }
            } else {
                $stJs .= "f.inCodPlano.value = '';";
                $stJs .= "d.getElementById( 'stNomConta' ).innerHTML='&nbsp;';";
                $stJs .= "alertaAviso('A conta informada não existe! (".$_POST['inCodPlano'].")','','erro','".Sessao::getId()."'); \n";
            }
        } else $stJs = "d.getElementById( 'stNomConta' ).innerHTML='&nbsp;';";
        SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
    break;

    ###TCEAL
    case 'montaDocumentoTCEAL':
        switch ($_POST['inDocTipo']) {
            case 1:
            case 2:
            case 99:
                $inCodNota              = $_REQUEST['inCodNota'];
                $stExercicioLiquidacao  = $_REQUEST['stExercicioLiquidacao'];
                $inCodEntidade          = $_REQUEST['inCodEntidade'];
                
                $rsRecordset = new RecordSet;

                if (($inCodNota != '') && ($stExercicioLiquidacao != '')) {
                    $obTTCEALPagamentoTipoDocumento = new TTCEALPagamentoTipoDocumento;
                    $obTTCEALPagamentoTipoDocumento->setDado('cod_nota' , $inCodNota);
                    $obTTCEALPagamentoTipoDocumento->setDado('exercicio', $stExercicioLiquidacao);
                    $obTTCEALPagamentoTipoDocumento->setDado('cod_entidade', $inCodEntidade);
                    $obTTCEALPagamentoTipoDocumento->recuperaCheque($rsRecordset, "" , "" , $boTransacao);
                }

                if ($rsRecordset->inNumLinhas < 0) {
                    $inNumCheque = '';
                    
                } else {
                    $inNumCheque = $rsRecordset->getCampo('num_documento');
                }

                SistemaLegado::executaFrameOculto("jq('#nuDoc').val(".$inNumCheque.");");
            break;
        }
    break;

    ###TCMBA
    case 'montaDocumentoTCMBA':
        $inNumCheque = '';
        if ($_REQUEST['inCodTipoPagamento']==1) {
            $idCodOrdem       = $_REQUEST['inCodOrdem'];
            $stExercicioOrdem = $_REQUEST['stExercicioOrdem'];
            $inCodEntidade    = $_REQUEST['inCodEntidade'];
            $rsRecordset      = new RecordSet;

            if (($idCodOrdem != '') && ($stExercicioOrdem != '')) {
                $obTTesourariaChequeEmissaoOrdemPagamento = new TTesourariaChequeEmissaoOrdemPagamento;
                $obTTesourariaChequeEmissaoOrdemPagamento->setDado('cod_ordem', $idCodOrdem);
                $obTTesourariaChequeEmissaoOrdemPagamento->setDado('exercicio', $stExercicioOrdem);
                $obTTesourariaChequeEmissaoOrdemPagamento->recuperaTodos($rsRecordset, " WHERE cod_ordem=".$idCodOrdem." AND exercicio='".$stExercicioOrdem."' AND cod_entidade =".$inCodEntidade);
            }

            if ($rsRecordset->inNumLinhas > 0) {
                $inNumCheque = $rsRecordset->getCampo('num_cheque');
            }
        }
        SistemaLegado::executaFrameOculto("jq_('#numDocPagamento').val('".$inNumCheque."');");
    break;
}
