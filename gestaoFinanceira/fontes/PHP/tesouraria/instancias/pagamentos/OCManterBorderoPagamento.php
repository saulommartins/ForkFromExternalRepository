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

    * Pagina Oculta para funcionalidade Bordero Pagamento
    * Data de Criação   : 09/01/2006
    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto
    $Revision: 30835 $
    $Name: $
    $Author: cako $
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $
    $Id: OCManterBorderoPagamento.php 62712 2015-06-11 15:00:29Z evandro $
    * Casos de uso: uc-02.04.20,uc-02.03.28

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBorderoPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$obRMONAgencia = new RMONAgencia;
$obRMONConta = new RMONContaCorrente;

$rsBanco = new RecordSet;
$rsAgencia = new RecordSet;

function montaListaDiverso($arRecordSet , $boExecuta = true)
{
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );

    if ( !$rsLista->eof() ) {

        $nuVlTotal = 0;
        foreach ($arRecordSet as $arItens) {

            $inValor = str_replace(".","",$arItens['inValor']);
            $flValor = str_replace(",",".",$inValor);
            $nuVlTotal = bcadd( $nuVlTotal, $flValor, 4 );
        }

        $obLista = new Lista;
        $obLista->setTitulo( "Registros" );
        $obLista->setMostraPaginacao( false );
        $rsLista->addFormatacao( 'inValor', 'NUMERIC_BR');
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Entidade" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "OP" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Empenhos" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Credor" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Líquido a Pagar" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "(X)" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inCodigoEntidade" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stOrdemPagamento" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stEmpenho" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stCredor" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inValor" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirOrdemPagamento();" );
        $obLista->ultimaAcao->addCampo( "1", "num_item" );
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."'; \n
                                           f.nuVlTotalBordero.value = '".$nuVlTotal."'; \n
                                          ");
    } else {

        SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = ''");
    }

}

function mostraSpanOrdemPagamento()
{
    global $obRMONAgencia;
    global $obRMONConta;
    global $rsBanco;
    global $rsAgencia;

    if ($_REQUEST['inCodConta']) {
        $obRMONConta->obRMONAgencia->obRMONBanco->listarBanco($rsBanco);

        $arItens = Sessao::read('arItens');
        $inCount = count($arItens);
        if ($inCount > 0) {
            for ($i=0; $i < $inCount; $i++) {
                if ( ($arItens[$i]['inNumOrdemPagamentoCredor'] ) != $_REQUEST['inNumOrdemPagamento'] ) {
                    $boTemOrdem = false;
                } else {
                    $boTemOrdem = true;
                    break;
                }
            }
        } else {
            $boTemOrdem = false;
        }
        if (!$boTemOrdem) {

            if ($_REQUEST['inCodigoEntidade'] && $_REQUEST['inNumOrdemPagamento']) {

                $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
                $obREmpenhoOrdemPagamento->setCodigoOrdem( $_REQUEST['inNumOrdemPagamento'] );
                $obREmpenhoOrdemPagamento->setExercicio( $_REQUEST['stExercicio'] );
                $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidade'] );

                    //$obREmpenhoOrdemPagamento->listarDadosPagamentoBordero( $rsLista , $boTransacao);
                    
                    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoRecurso.class.php"          );
                    $obTContabilidadePlanoRecurso          = new TContabilidadePlanoRecurso;
                    $obTContabilidadePlanoRecurso->setDado('exercicio', $_REQUEST["stExercicio"] );
                    $obTContabilidadePlanoRecurso->setDado('cod_plano', $_REQUEST['inCodConta'] );
                    $obTContabilidadePlanoRecurso->recuperaPorChave($rsRecurso,"","",$boTransacao);

                    $obREmpenhoOrdemPagamento->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($rsRecurso->getCampo('cod_recurso'));
                    $obREmpenhoOrdemPagamento->listarDadosPagamentoBorderoContaRecurso( $rsLista, $boTransacao);

                    //recuperaDadosPagamentoBorderoContaRecurso
                    
                    if ( $rsLista->getNumLinhas() > 0 ) {
                        $obREmpenhoOrdemPagamento->consultar();

                        include_once ( CAM_GF_CONT_NEGOCIO.      "RContabilidadePlanoBanco.class.php"              );
                        $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();
                        $obRContabilidadePlanoBanco->setCodPlano( $_REQUEST['inCodConta']   );
                        $obRContabilidadePlanoBanco->setExercicio( $_REQUEST['stExercicio'] );
                        $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidade'] );
                        $obRContabilidadePlanoBanco->consultarRecurso();
                        $inCodRecurso = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getCodRecurso();
                        $stTipoRecurso = $obRContabilidadePlanoBanco->obROrcamentoRecurso->getTipo();
                        $obErro = new Erro;
                        if ($stTipoRecurso == "V") {
                           if ( $inCodRecurso != $obREmpenhoOrdemPagamento->roUltimaNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso()) {
                            $obErro->setDescricao( 'A OP deve ter o mesmo recurso que a Conta informada.' );
                           }
                        }
                        if (!$obErro->ocorreu()) {
                            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->setNumCGM( $rsLista->getCampo("cgm_beneficiario") );
                            $obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->listar( $rsCgm , '', $boTransacao);

                            $obRTesourariaBoletim = new RTesourariaBoletim;
                            $obRTesourariaBoletim->addBordero();
                            $obRTesourariaBoletim->roUltimoBordero->addTransacaoPagamento();
                            $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obRCGM->setNumCGM($rsLista->getCampo("cgm_beneficiario"));
                            $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obREmpenhoPagamentoLiquidacao->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obRCGM->setNumCGM($rsLista->getCampo("cgm_beneficiario"));
                            $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->consultarDadosBancariosCGM($boTransacao);

                            if ($obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->obRMONBanco->getNumBanco()) {
                                $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->listarAgencia( $rsAgencia );
                            }

                            $arEmp = str_replace("<br>","  ",trim($rsLista->getCampo("nota_empenho")));
                            $arEmpenhos = explode("   ",$arEmp);

                            for ($x=0; $x < sizeof($arEmpenhos); $x++) {
                                if (($x % 2) == 1 ) {
                                    if ( trim($arEmpenhos[$x]) != "" ) {
                                        $empenhos .= $arEmpenhos[$x] . "<br>";
                                    }
                                }
                            }
                            $empenhos = substr($empenhos, 0, strlen($empenhos)-4);

                            $obHdnCPFCNPJ = new Hidden;
                            $obHdnCPFCNPJ->setName( "stCPF/CNPJ" );
                            $obHdnCPFCNPJ->setValue( $rsCgm->getCampo("cpf")."".$rsCgm->getCampo("cnpj") );

                            $obHdnOrdem = new Hidden;
                            $obHdnOrdem->setName( "stOrdemPagamento" );
                            $obHdnOrdem->setValue( str_pad($rsLista->getCampo("cod_ordem"), 6, "0",STR_PAD_LEFT)."/".$rsLista->getCampo("exercicio") );

                            $obHdnExercicioOrdem = new Hidden;
                            $obHdnExercicioOrdem->setName( "stExercicioOrdem" );
                            $obHdnExercicioOrdem->setValue( $rsLista->getCampo("exercicio") );

                            $obHdnDtEmissaoOrdem = new Hidden;
                            $obHdnDtEmissaoOrdem->setName( "stDtEmissaoOrdem" );
                            $obHdnDtEmissaoOrdem->setValue( $rsLista->getCampo("dt_emissao") );

                            $obHdnExercicioEmpenho = new Hidden;
                            $obHdnExercicioEmpenho->setName( "stExercicioEmpenho" );
                            $obHdnExercicioEmpenho->setValue( $rsLista->getCampo("exercicio_empenho") );

                            $obHdnEmpenho = new Hidden;
                            $obHdnEmpenho->setName( "stEmpenho" );
                            $obHdnEmpenho->setValue( $empenhos );

                            $obHdnCodCredor = new Hidden;
                            $obHdnCodCredor->setName( "inCodCredor" );
                            $obHdnCodCredor->setValue( $rsLista->getCampo("cgm_beneficiario") );

                            $obHdnNomCredor = new Hidden;
                            $obHdnNomCredor->setName( "stNomCredor" );
                            $obHdnNomCredor->setValue( $rsLista->getCampo("beneficiario") );

                            $obHdnCodBancoCredor =  new Hidden;
                            $obHdnCodBancoCredor->setName   ( "inCodBancoCredor" );
                            $obHdnCodBancoCredor->setValue  ( $rsAgencia->getCampo("cod_banco") );

                            $obHdnCodAgenciaCredor = new Hidden;
                            $obHdnCodAgenciaCredor->setName    ( "inCodAgenciaCredor"           );
                            $obHdnCodAgenciaCredor->setValue   ( $rsAgencia->getCampo("cod_agencia") );

                            $obHdnNumOrdemPagamento = new Hidden;
                            $obHdnNumOrdemPagamento->setName    ( "inNumOrdemPagamentoCredor"         );
                            $obHdnNumOrdemPagamento->setValue   ( $_REQUEST["inNumOrdemPagamento"] );

                            $inValorOP = bcsub($rsLista->getCampo("valor_pagamento"),$rsLista->getCampo('vl_anulado'),2);

                            /* Retenções */
                            if ($obREmpenhoOrdemPagamento->getRetencao()) {
                                foreach ( $obREmpenhoOrdemPagamento->getRetencoes() as $arRetencoes ) {
                                    $nuVlRetencoes = bcadd($arRetencoes['vl_retencao'],$nuVlRetencoes,2);
                                }
                               // Define Objeto Numeric para valor das retenções
                                $obLblValorRet = new Label();
                                $obLblValorRet->setRotulo( "Valor Retenções"     );
                                $obLblValorRet->setId    ( "inValorRet"   );
                                $obLblValorRet->setValue ( number_format($nuVlRetencoes,2,',','.') );

                               // Valor liquido da OP
                                $obLblValorLiqOp = new Label();
                                $obLblValorLiqOp->setRotulo( "Valor Líquido da OP" );
                                $obLblValorLiqOp->setValue ( number_format(bcsub($inValorOP,$nuVlRetencoes,2),2,',','.') );

                            } else {
                                $nuVlRetencoes = 0.00;

                                // Valor liquido da OP
                                $obLblValorLiqOp = new Label();
                                $obLblValorLiqOp->setRotulo( "Valor Líquido da OP" );
                                $obLblValorLiqOp->setValue ( number_format(bcsub($inValorOP,$rsLista->getCampo("vl_pago_nota"),2),2,',','.') );
                            }

                            $obHdnValor = new Hidden;
                            $obHdnValor->setName( "inValor" );
                            $obHdnValor->setValue( bcsub(bcsub(bcsub($rsLista->getCampo("valor_pagamento"),$rsLista->getCampo('vl_anulado'),2),$rsLista->getCampo('vl_pago_nota'),2),$nuVlRetencoes,2));

                            $rsLista->addFormatacao( 'valor_pagamento', 'NUMERIC_BR' );
                            $rsLista->addFormatacao( 'vl_pago_nota', 'NUMERIC_BR' );

                            // Define Objeto Numeric para valor pags
                            $obLblValorPago = new Label();
                            $obLblValorPago->setRotulo( "Valor Pago"     );
                            $obLblValorPago->setId    ( "inValorPago"   );
                            $obLblValorPago->setValue ( $rsLista->getCampo("vl_pago_nota") );

                            // Define Objeto Numeric para valor
                            $obLblValor = new Label();
                            $obLblValor->setRotulo( "Valor"     );
                            $obLblValor->setId    ( "inValor"   );
                            $obLblValor->setValue ( number_format($inValorOP,2,',','.') );

                            // Define Objeto Label para Valor Geral
                            $obLblCredor = new Label();
                            $obLblCredor->setRotulo( "Credor"        );
                            $obLblCredor->setId    ( "stNomCredor"   );
                            $obLblCredor->setValue ( $rsLista->getCampo("cgm_beneficiario") ." - ". $rsLista->getCampo("beneficiario") );

                            $obTxtBanco = new TextBox;
                            $obTxtBanco->setRotulo        ( "*Banco"                           );
                            $obTxtBanco->setTitle         ( ""                                 );
                            $obTxtBanco->setName          ( "inNumBancoCredor"                 );
                            $obTxtBanco->setValue         ( $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->obRMONBanco->getNumBanco()   );
                            $obTxtBanco->setSize          ( 10                                 );
                            $obTxtBanco->setMaxLength     ( 6                                  );
                            $obTxtBanco->setNull          ( true                               );
                            $obTxtBanco->setInteiro       ( true                               );
                            $obTxtBanco->obEvento->setOnChange ( "preencheAgenciaCredor('');" );

                            $obCmbBanco = new Select;
                            $obCmbBanco->setName          ( "cmbBancoCredor"               );
                            $obCmbBanco->addOption        ( "", "Selecione"                );
                            $obCmbBanco->setValue         ( $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->obRMONBanco->getNumBanco() );
                            $obCmbBanco->setCampoId       ( "num_banco"                    );
                            $obCmbBanco->setCampoDesc     ( "nom_banco"                    );
                            $obCmbBanco->preencheCombo    ( $rsBanco                       );
                            $obCmbBanco->setNull          ( true                           );
                            $obCmbBanco->setStyle         ( "width: 220px"                 );
                            $obCmbBanco->obEvento->setOnChange ( "preencheAgenciaCredor('');" );

                            $obTxtAgencia = new TextBox;
                            $obTxtAgencia->setRotulo        ( "*Agencia"                                    );
                            $obTxtAgencia->setTitle         ( "" );
                            $obTxtAgencia->setName          ( "inNumAgenciaCredor"                          );
                            $obTxtAgencia->setValue         ( $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->getNumAgencia() );
                            $obTxtAgencia->setSize          ( 10                                            );
                            $obTxtAgencia->setMaxLength     ( 6                                             );
                            $obTxtAgencia->setNull          ( true                                          );
                            $obTxtAgencia->setInteiro       ( true                                          );
                            $obTxtAgencia->obEvento->setOnChange ( "preencheCamposCodigosCredor('');" );

                            $obCmbAgencia = new Select;
                            $obCmbAgencia->setName          ( "cmbAgenciaCredor"             );
                            $obCmbAgencia->addOption        ( "", "Selecione"                );
                            $obCmbAgencia->setValue         ( $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->obRMONAgencia->getNumAgencia()  );
                            $obCmbAgencia->setCampoId       ( "num_agencia"                  );
                            $obCmbAgencia->setCampoDesc     ( "nom_agencia"                  );
                            $obCmbAgencia->preencheCombo    ( $rsAgencia                     );
                            $obCmbAgencia->setNull          ( true                           );
                            $obCmbAgencia->setStyle         ( "width: 220px"                 );
                            $obCmbAgencia->obEvento->setOnChange ( "preencheCamposCodigosCredor('');" );

                            $obTxtContaCorrente = new TextBox ;
                            $obTxtContaCorrente->setRotulo    ( "*Conta Corrente"                        );
                            $obTxtContaCorrente->setName      ( "stNumeroContaCredor"                    );
                            $obTxtContaCorrente->setValue     ( $obRTesourariaBoletim->roUltimoBordero->roUltimaTransacaoPagamento->getContaCorrente() );
                            $obTxtContaCorrente->setTitle     ( ""                                       );
                            $obTxtContaCorrente->setSize      ( 20                                       );
                            $obTxtContaCorrente->setMaxLength ( 20                                       );
                            $obTxtContaCorrente->setNull      ( true                                     );

                            if (Sessao::getExercicio() <= '2014' ){
                                $obTxtNrNFDocumento = new TextBox ;
                                $obTxtNrNFDocumento->setRotulo    ( "Nr. NF / Documento"                     );
                                $obTxtNrNFDocumento->setName      ( "stNrNFDocumento"                        );
                                $obTxtNrNFDocumento->setValue     ( $stNrNFDocumento                         );
                                $obTxtNrNFDocumento->setSize      ( 20                                       );
                                $obTxtNrNFDocumento->setMaxLength ( 100                                      );
                                $obTxtNrNFDocumento->setNull      ( true                                     );
                            }

                            // Define Objeto TextArea para observações
                            $obTxtObs = new TextArea;
                            $obTxtObs->setName   ( "stObservacao"                                   );
                            $obTxtObs->setId     ( "stObservacao"                                   );
                            $obTxtObs->setValue  ( $stObservacao                                    );
                            $obTxtObs->setRotulo ( "*Observação"                                    );
                            $obTxtObs->setTitle  ( "Informe as observações do borderô"              );
                            $obTxtObs->setNull   ( true                                             );
                            $obTxtObs->setRows   ( 2                                                );
                            $obTxtObs->setCols   ( 100                                              );

                            $obFormulario = new Formulario;
                            $obFormulario->addHidden     ( $obHdnCPFCNPJ        );
                            $obFormulario->addHidden     ( $obHdnOrdem          );
                            $obFormulario->addHidden     ( $obHdnExercicioOrdem );
                            $obFormulario->addHidden     ( $obHdnExercicioEmpenho );
                            $obFormulario->addHidden     ( $obHdnEmpenho        );
                            $obFormulario->addHidden     ( $obHdnCodCredor      );
                            $obFormulario->addHidden     ( $obHdnNomCredor      );
                            $obFormulario->addHidden     ( $obHdnValor          );
                            $obFormulario->addHidden     ( $obHdnCodBancoCredor    );
                            $obFormulario->addHidden     ( $obHdnCodAgenciaCredor  );
                            $obFormulario->addHidden     ( $obHdnNumOrdemPagamento );
                            $obFormulario->addHidden     ( $obHdnDtEmissaoOrdem    );

                            $obFormulario->addComponente ( $obLblValor  );
                            if ($obREmpenhoOrdemPagamento->getRetencao()) {
                                $obFormulario->addComponente ($obLblValorRet    );
                            } else  $obFormulario->addComponente ( $obLblValorPago  );
                            $obFormulario->addComponente ( $obLblValorLiqOp );
                            $obFormulario->addComponente ( $obLblCredor );
                            $obFormulario->addComponenteComposto    ( $obTxtBanco,$obCmbBanco     );
                            $obFormulario->addComponenteComposto    ( $obTxtAgencia,$obCmbAgencia );
                            $obFormulario->addComponente ( $obTxtContaCorrente );
                            
                            if (Sessao::getExercicio() <= '2014' )
                                $obFormulario->addComponente ( $obTxtNrNFDocumento );
                            
                            $obFormulario->addComponente ( $obTxtObs           );

                            $obFormulario->montaInnerHTML ();
                            $stHTML = $obFormulario->getHTML ();

                            SistemaLegado::executaFrameOculto(" d.frm.inCodigoEntidade.value = '".$rsLista->getCampo("cod_entidade")."';
                                                                d.frm.inNumOrdemPagamento.value = '".$_REQUEST['inNumOrdemPagamento']."'; \n
                                                                d.getElementById('stNumOrdemPagamento').innerHTML = '".$rsLista->getCampo("beneficiario")."'; \n
                                                                d.getElementById('spnOrdem').innerHTML = '".$stHTML."';
                                                             ");
                            SistemaLegado::exibeAviso("","","");
                        } else {
                            SistemaLegado::exibeAviso($obErro->getDescricao(),"","erro");
                            SistemaLegado::executaFrameOculto( "d.frm.inNumOrdemPagamento.value = '';");
                        }
                    } else {
                        SistemaLegado::executaFrameOculto(" d.frm.stTipoTransacao.value = '2';           \n
                                                            d.frm.inNumOrdemPagamento.value  = '';      \n
                                                            d.getElementById('stNumOrdemPagamento').innerHTML = '&nbsp;';\n
                                                            d.getElementById('spnOrdem').innerHTML = '';\n
                                                         ");
                        SistemaLegado::exibeAviso("Ordem de Pagamento paga ou inválida (".$_REQUEST['inNumOrdemPagamento'].")","","erro");

                    }
            } else {
                SistemaLegado::executaFrameOculto(" d.frm.stTipoTransacao.value = '2';           \n
                                                    d.frm.inNumOrdemPagamento.value  = '';      \n
                                                    d.getElementById('stNumOrdemPagamento').innerHTML = '&nbsp;';\n
                                                    d.getElementById('spnOrdem').innerHTML = '';\n
                                                 ");
            }
        } else {
            SistemaLegado::executaFrameOculto(" d.frm.stTipoTransacao.value = '2';           \n
                                                d.frm.inNumOrdemPagamento.value  = '';      \n
                                                d.getElementById('stNumOrdemPagamento').innerHTML = '&nbsp;';\n
                                                d.getElementById('spnOrdem').innerHTML = '';\n
                                             ");
            SistemaLegado::exibeAviso("Ordem de Pagamento já inclusa!()","","erro");
        }
    } else {
        SistemaLegado::exibeAviso("Informe a Conta Pagadora.","","erro");
        SistemaLegado::executaFrameOculto( "d.frm.inNumOrdemPagamento.value = ''; d.getElementById('stNumOrdemPagamento').innerHTML = '&nbsp;'; ");
    }
}

if ($_REQUEST['mostraSpanOrdemPagamento']) {
    mostraSpanOrdemPagamento($_REQUEST);
} else {
    switch ($_REQUEST["stCtrl"]) {

        case 'saldoConta':
            if ($_POST['inCodConta']) {

                $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;
                $obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoBanco->setCodPlano ( $_POST['inCodConta'] );
                $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidade'] );
                $obRContabilidadePlanoBanco->listarContasBancos($rsContaBanco);

                if ($rsContaBanco->getCampo("nom_conta")) {
                    $stDescricao = $rsContaBanco->getCampo("nom_conta");
                    $stJs  = "d.getElementById( 'stConta' ).innerHTML='".$stDescricao."'; \n";
                    $stJs .= "f.stConta.value = '".$stDescricao."'; \n";
                    $obRContabilidadePlanoBanco->consultarSaldoBanco( $nuVlSaldoContaBanco );
                    $stJs .= "f.nuSaldoContaBanco.value   = '".$nuVlSaldoContaBanco."'; \n";
                    $stJs .= "f.nuSaldoContaBancoBR.value = '".number_format($nuVlSaldoContaBanco, "2", ",", ".")."'; \n";
                } else {
                    SistemaLegado::exibeAviso("Conta inválida.","","erro");
                    $stJs  = "d.getElementById( 'stConta' ).innerHTML = '&nbsp;'; \n";
                    $stJs .= "f.inCodConta.value = ''; \n";
                    $stJs .= "f.stConta.value = ''; \n";
                    $stJs .= "f.nuSaldoContaBanco.value   = ''; \n";
                    $stJs .= "f.nuSaldoContaBancoBR.value = ''; \n";
                }
            } else {
                $stJs  = "d.getElementById( 'stConta' ).innerHTML = '&nbsp;'; \n";
            }
            SistemaLegado::executaFrameOculto( $stJs );
        break;

        case 'incluiOrdemPagamento':

            $arItens = Sessao::read('arItens');

            $inCount = count($arItens);
            if ( $inCount < 50 ) {
                $arItens[$inCount]['num_item']                     = $inCount+1;
                $arItens[$inCount]['inCodigoEntidade']             = $_POST['inCodigoEntidade'];
                $arItens[$inCount]['stOrdemPagamento']             = $_POST['stOrdemPagamento'];
                $arItens[$inCount]['stEmpenho']                    = $_POST['stEmpenho'];
                $arItens[$inCount]['inCodCredor']                  = $_POST['inCodCredor'];
                $arItens[$inCount]['stCredor']                     = $_POST['inCodCredor'] ." - ". $_POST['stNomCredor'];
                $arItens[$inCount]['inValor' ]                     = $_POST['inValor'];
                $arItens[$inCount]['inNumBancoCredor']             = $_POST['inNumBancoCredor'];
                $arItens[$inCount]['inNumAgenciaCredor']           = $_POST['inNumAgenciaCredor'];
                $arItens[$inCount]['stNumeroContaCredor']          = $_POST['stNumeroContaCredor'];
                $arItens[$inCount]['stObservacao']                 = $_POST['stObservacao'];
                $arItens[$inCount]['inCodBancoCredor']             = $_POST['inCodBancoCredor'];
                $arItens[$inCount]['inCodAgenciaCredor']           = $_POST['inCodAgenciaCredor'];
                $arItens[$inCount]['stTipoTransacaoCredor']        = $_POST['stTipoTransacao'];
                $arItens[$inCount]['inNumOrdemPagamentoCredor']    = $_POST['inNumOrdemPagamentoCredor'];
                $arItens[$inCount]['stCPF/CNPJ']                   = $_POST['stCPF/CNPJ'];
                
                if (Sessao::getExercicio() <= '2014' )
                    $arItens[$inCount]['stNrNFDocumento']              = $_POST['stNrNFDocumento'];
                
                $arItens[$inCount]['stExercicioOrdem']             = $_POST['stExercicioOrdem'];
                $arItens[$inCount]['stExercicioEmpenho']           = $_POST['stExercicioEmpenho'];
                $arItens[$inCount]['stDtEmissaoOrdem']             = $_POST['stDtEmissaoOrdem'];
    
                Sessao::write('arItens',$arItens);
    
                $stHTML = montaListaDiverso(Sessao::read('arItens'));
            }else{
                SistemaLegado::exibeAviso("Maximo de 50 OPs por borderô.","","erro");
            }
            if ( count(Sessao::read('arItens')) == 1 ) {
                SistemaLegado::executaFrameOculto(" f.inCodEntidade.disabled = true;" );
            }
        break;

        case 'mostraSpanOrdem':
            mostraSpanOrdemPagamento($_REQUEST);
        break;

        case "preencheCamposCodigosCredor":

            $NumAgencia = $_REQUEST['inNumAgenciaCredor'];
            $obRMONAgencia->setNumAgencia ( $NumAgencia );
            $obRMONAgencia->consultarAgencia ( $rsListaAgencia );

            $CodBanco   = $rsListaAgencia->getCampo ("cod_banco");
            $CodAgencia = $rsListaAgencia->getCampo ("cod_agencia");

            $js .= "f.inCodBancoCredor.value='". $CodBanco ."'; \n";
            $js .= "f.inCodAgenciaCredor.value='". $CodAgencia ."'; \n";
            SistemaLegado::executaFrameOculto($js);
        break;

        case "preencheAgenciaCredor":

            $js .= "f.inNumAgenciaCredor.value=''; \n";
            $js .= "limpaSelect(f.cmbAgenciaCredor,1); \n";
            $js .= "f.cmbAgenciaCredor[0] = new Option('Selecione','', 'selected');\n";

            if ($_REQUEST['inNumBancoCredor']) {

                $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumBancoCredor"] );
                $obRMONAgencia->listarAgencia( $rsAgencia );

                $inContador = 1;

                while ( !$rsAgencia->eof() ) {
                    $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
                    $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );

                    $js .= "f.inCodAgenciaCredor.value='".$inCodAgencia."'; \n";
                    $js .= "f.cmbAgenciaCredor.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
                    $inContador++;
                    $rsAgencia->proximo();
                }
            }
            if ($_REQUEST["stLimpar"] == "limpar") {
                $js .= "f.inNumAgenciaCredor.value='".$_REQUEST["inNumAgenciaCredor"]."'; \n";
                $js .= "f.cmbAgenciaCredor.options[".$_REQUEST["inNumAgenciaCredor"]."].selected = true; \n";
            }
            SistemaLegado::executaFrameOculto($js);
        break;

        case 'excluirOrdemPagamento':

            $arItens = array();
            $inCount = 0;
            foreach ( Sessao::read('arItens') as $value ) {
                if ( ($value['num_item'] ) != $_GET['inNumItem'] ) {

                    $arItens[$inCount]['num_item']                     = $inCount + 1;
                    $arItens[$inCount]['inCodigoEntidade']             = $value['inCodigoEntidade'];
                    $arItens[$inCount]['stOrdemPagamento']             = $value['stOrdemPagamento'];
                    $arItens[$inCount]['stEmpenho']                    = $value['stEmpenho'];
                    $arItens[$inCount]['inCodCredor']                  = $value['inCodCredor'];
                    $arItens[$inCount]['stCredor']                     = $value['stCredor'];
                    $arItens[$inCount]['inValor' ]                     = $value['inValor' ];
                    $arItens[$inCount]['inNumBancoCredor']             = $value['inNumBancoCredor'];
                    $arItens[$inCount]['inNumAgenciaCredor']           = $value['inNumAgenciaCredor'];
                    $arItens[$inCount]['stNumeroContaCredor']          = $value['stNumeroContaCredor'];
                    $arItens[$inCount]['stObservacao']                 = $value['stObservacao'];
                    $arItens[$inCount]['inCodBancoCredor']             = $value['inCodBancoCredor'];
                    $arItens[$inCount]['inCodAgenciaCredor']           = $value['inCodAgenciaCredor'];
                    $arItens[$inCount]['stTipoTransacaoCredor']        = $value['stTipoTransacaoCredor'];
                    $arItens[$inCount]['inNumOrdemPagamentoCredor']    = $value['inNumOrdemPagamentoCredor'];
                    $arItens[$inCount]['stCPF/CNPJ']                   = $value['stCPF/CNPJ'];
                    
                    if (Sessao::getExercicio() <= '2014' )
                        $arItens[$inCount]['stNrNFDocumento']              = $value['stNrNFDocumento'];
                    
                    $arItens[$inCount]['stExercicioOrdem']             = $value['stExercicioOrdem'];
                    $arItens[$inCount]['stExercicioEmpenho']           = $value['stExercicioEmpenho'];
                    $arItens[$inCount]['stDtEmissaoOrdem']             = $value['stDtEmissaoOrdem'];

                    $inCount++;
                }
            }
            Sessao::write('arItens',$arItens);

            montaListaDiverso( Sessao::read('arItens') );

            if ( count(Sessao::read('arItens')) == 0 ) {
                SistemaLegado::executaFrameOculto(" f.inCodEntidade.disabled = false; ");
            }
        break;
    case 'alteraBoletim':
        $obRTesourariaBoletim = new RTesourariaBoletim();
        list( $inCodBoletim , $stDataBoletim ) = explode ( ':' , $_REQUEST['inCodBoletim'] );
        $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obErro = $obRTesourariaBoletim->listarBoletimAberto ( $rsBoletimAberto );

        if ( !$obErro->ocorreu() && $rsBoletimAberto->getNumLinhas() == 1 ) {
            $stJs  = "f.inCodBoletim.value = '" . $rsBoletimAberto->getCampo( 'cod_boletim' ) . "';\r\n";
            $stJs .= "f.stDtBoletim.value = '" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        } else {
            $stJs  = "f.inCodBoletim.value = '';\r\n";
            $stJs .= "f.stDtBoletim.value = '';\r\n";
            SistemaLegado::executaFrameOculto( "LiberaFrames(true,false);".$stJs );
        }

    break;
       case 'mostraSpanBoletim':

            if ($_REQUEST['inCodEntidade']) {

                $obRTesourariaBoletim = new RTesourariaBoletim;
                $obRTesourariaBoletim->setExercicio( $_REQUEST['stExercicio'] );
                $obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
                $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );

                $obRTesourariaBoletim->addBordero();
                $obRTesourariaBoletim->roUltimoBordero->addAssinatura();
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setExercicio( $_REQUEST['stExercicio'] );
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setTipo('BR');
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->setEntidades( $_REQUEST['inCodEntidade'] );
                $obRTesourariaBoletim->roUltimoBordero->roUltimaAssinatura->listar( $rsAssinatura );

                if ( $rsAssinatura->getNumLinhas() > 0 ) {

                    for ($x=1; $x<=$rsAssinatura->getNumLinhas(); $x++) {

                        $js .= "f.inNumAssinante_".$x.".value = '".$rsAssinatura->getCampo("numcgm")."'; \n";
                        $js .= "d.getElementById('stNomAssinante_".$x."').innerHTML = '".$rsAssinatura->getCampo("nom_cgm")."'; \n";
                        $js .= "f.stNomAssinante_".$x.".value = '".$rsAssinatura->getCampo("nom_cgm")."'; \n";
                        $js .= "f.inNumMatricula_".$x.".value = '".$rsAssinatura->getCampo("num_matricula")."'; \n";
                        $js .= "f.stCargo_".$x.".value = '".$rsAssinatura->getCampo("cargo")."'; \n";

                        $rsAssinatura->proximo();
                    }
                }
                //Define o objeto INNER para armazenar a Conta Banco
                $obBscConta = new BuscaInner;
                $obBscConta->setRotulo( "Conta" );
                $obBscConta->setTitle( "Informe a Conta" );
                $obBscConta->setNull( false );
                $obBscConta->setId( "stConta" );
                $obBscConta->setValue( '' );
                $obBscConta->obCampoCod->setName("inCodConta");
                $obBscConta->obCampoCod->setId("inCodConta");
                $obBscConta->obCampoCod->setValue( "" );
                $obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','inCodConta','stConta','banco&inCodEntidade='+document.frm.inCodigoEntidade.value,'".Sessao::getId()."','800','550');" );
                $obBscConta->obCampoCod->obEvento->setOnChange("return false;");
                $obBscConta->obCampoCod->obEvento->setOnBlur( "buscaDado('saldoConta');" );

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obBscConta );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $js .= "d.getElementById('spnContaBanco').innerHTML = '".$stHTML."' \n";

                /*
                *  Este BuscaInner da Ordem de Pagamento foi implementado
                *  especificamente para o Boderô de Pagamento, o valor do
                *  metodo setValoresBusca foi mudado para  OCManterBorderoPagamento,
                *  pois se fez necessário essa alteração para que o span da listagem
                *  de Ordem de Pagamento fosse preenchida de acordo com o numero de
                *  OP informado.
                */
                //Define o objeto INNER para buscar a Ordem de Pagamento
                $obBscOrdemPagamento = new BuscaInner;
                $obBscOrdemPagamento->setRotulo( "*Nr. Ordem de Pagamento" );
                $obBscOrdemPagamento->setTitle( "Informe o número da Ordem de Pagamento que deseja incluir" );
                $obBscOrdemPagamento->setNull( true );
                $obBscOrdemPagamento->setId( "stNumOrdemPagamento" );
                $obBscOrdemPagamento->setValue( '' );
                $obBscOrdemPagamento->obCampoCod->setName("inNumOrdemPagamento");
                $obBscOrdemPagamento->obCampoCod->setValue( "" );
                $obBscOrdemPagamento->setFuncaoBusca ( "abrePopUp('".CAM_GF_EMP_POPUPS."ordemPagamento/FLOrdemPagamento.php','frm','inNumOrdemPagamento','stNumOrdemPagamento','&inCodPlano='+document.frm.inCodConta.value+'&inCodEntidade='+document.frm.inCodigoEntidade.value+'&tipoBusca=bordero_recurso','".Sessao::getId()."','800','550','ordem_pagamento');" );
                $obBscOrdemPagamento->setValoresBusca( 'OCManterBorderoPagamento.php?'.Sessao::getId().'&mostraSpanOrdemPagamento=1', "frm", '' );

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obBscOrdemPagamento );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                $js .= "d.getElementById('spnBscOrdemPagamento').innerHTML = '".$stHTML."' \n";

                $obErro = $obRTesourariaBoletim->buscarCodigoBoletim( $inCodBoletimAberto, $stDtBoletimAberto );
                if ( $obErro->ocorreu() ) {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"","erro");
                    if (!$inCodBoletimAberto) {
                        $js .= "f.Ok.disabled = true; \n";
                    } else {
                        $js .= "f.Ok.disabled = false; \n";
                    }
                } else {
                    $js .= "f.Ok.disabled = false; \n";
                }
                require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
                $obISelectBoletim = new ISelectBoletim;
                $obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']  );
                $obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
                $obISelectBoletim->obEvento->setOnChange ( "buscaDado('alteraBoletim');");

                $obFormulario = new Formulario;
                $obFormulario->addComponente ( $obISelectBoletim );

                $obFormulario->montaInnerHTML ();
                $stHTML = $obFormulario->getHTML ();

                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
                $stHTML = str_replace( "\\\\'","\\'",$stHTML );

                SistemaLegado::executaFrameOculto(" f.inCodigoEntidade.value = '".$_REQUEST['inCodEntidade']."'; \n
                                                        d.getElementById('spnBoletim').innerHTML = '".$stHTML."';" . $js
                                                      );
            } else {

                SistemaLegado::executaFrameOculto("d.getElementById('spnBoletim').innerHTML           = ''; \n
                                                   d.getElementById('spnContaBanco').innerHTML        = ''; \n
                                                   d.getElementById('spnBscOrdemPagamento').innerHTML = ''; \n
                                                   f.Ok.disabled = false;                                   \n
                                                  ");
            }
        break;

    }
}
?>
