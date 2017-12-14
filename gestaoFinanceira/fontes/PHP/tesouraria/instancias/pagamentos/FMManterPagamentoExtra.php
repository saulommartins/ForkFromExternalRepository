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
    * Página de Formulário para Pagamento de Despesas Extra Orçamentárias
    * Data de Criação   : 23/08/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze
    *
    * @ignore
    *
    * $Id: FMManterPagamentoExtra.php 66484 2016-09-02 18:07:47Z franver $
    *
    * Casos de uso: uc-02.04.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php");
include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoConfiguracao.class.php' );
include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoTransferencia.class.php' );
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterPagamentoExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.exercicio = '".Sessao::getExercicio()."' AND configuracao.parametro = 'seta_tipo_documento_tcemg'");
$boMostrarComboTipoDocTcemg  = $rsAdministracaoConfiguracao->getCampo('valor');

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    Sessao::remove('arCheques');

    $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
    $obTEmpenhoConfiguracao->setDado( 'parametro', 'numero_empenho' );
    $obTEmpenhoConfiguracao->consultar ();
    $tipoNumeracao = $obTEmpenhoConfiguracao->getDado( 'valor' );

    $inCodHistorico = $request->get('inCodHistorico');
    $stNomHistorico = $request->get('stNomHistorico');
    Sessao::write('inCodPlanoCredito', $request->get('inCodPlanoCredito'));
    Sessao::write('stNomContaCredito', $request->get('stNomContaCredito'));
    Sessao::write('inCodEntidade'    , $request->get('inCodEntidade'));
    
    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );
    
    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( $stCtrl );

    $obHdnDtRecibo= new Hidden;
    $obHdnDtRecibo->setName ( "stDtRecibo" );
    $obHdnDtRecibo->setId( "stDtRecibo" );
    $obHdnDtRecibo->setValue( $stDtRecibo );
    
    $obHdnBoTipoDocTCEMG = new Hidden;
    $obHdnBoTipoDocTCEMG->setName ( "boTipoDocTCEMG" );
    $obHdnBoTipoDocTCEMG->setValue( $boMostrarComboTipoDocTcemg );

    $stHdnValor = "
        if (!document.frm.inCodBoletim) {
            erro = true;
            mensagem += '@Selecione um boletim.';
        } else {
            if (document.frm.inCodBoletim.value == '') {
                erro = true;
                mensagem += '@Selecione um boletim.';
            }
        }
        if (document.frm.inCodPlanoCredito.value == '') {
             document.frm.nuSaldoContaAnalitica.value = '';
             erro = true;
             mensagem += '@Deve ser informada uma conta de caixa/banco!';
        }
        if (document.frm.inCodPlanoDebito.value == '') {
             erro = true;
             mensagem += '@Deve ser informada uma conta de despesa!';
        }
        var stValor;
        stValor = document.frm.nuValor.value;
        while (stValor.indexOf('.')>0) {
            stValor = stValor.replace('.','');
        }
        stValor = stValor.replace(',','.');
        if (stValor <= 0) {
             erro = true;
             mensagem += '@Campo Valor deve ser maior que 0,00!';
        }
        ";

    $obHdnEval = new HiddenEval;
    $obHdnEval->setName( "stEval" );
    $obHdnEval->setValue( $stHdnValor );

    $obHdnVlSaldoContaAnalitica = new Hidden;
    $obHdnVlSaldoContaAnalitica->setName (   "nuSaldoContaAnalitica" );
    $obHdnVlSaldoContaAnalitica->setId   (   "nuSaldoContaAnalitica" );
    $obHdnVlSaldoContaAnalitica->setValue(   $nuSaldoContaAnalitica  );

    $obHdnVlSaldoContaAnaliticaBR = new Hidden;
    $obHdnVlSaldoContaAnaliticaBR->setName ( "nuSaldoContaAnaliticaBR" );
    $obHdnVlSaldoContaAnaliticaBR->setId   ( "nuSaldoContaAnaliticaBR" );
    $obHdnVlSaldoContaAnaliticaBR->setValue( $nuSaldoContaAnaliticaBR  );

    //Define Objeto TextBox para Código de Barras
    $obTxtCodBarras = new TextBox();
    $obTxtCodBarras->setTitle     ( 'Informe o Código de Barras. posicionando o leitor sobre o mesmo.' );
    $obTxtCodBarras->setRotulo    ( 'Código de Barras'                                    );
    $obTxtCodBarras->setName      ( 'inCodBarras'                                       );
    $obTxtCodBarras->setId        ( 'inCodBarras'                                       );
    $obTxtCodBarras->setInteiro   ( true                                                  );
    $obTxtCodBarras->setNull      ( true                                                  );
    $obTxtCodBarras->setSize      ( 23                                                    );
    $obTxtCodBarras->setMaxLength ( 20                                                    );
    $obTxtCodBarras->obEvento->setOnChange(" if (this.value.length == 20) ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBarras='+this.value, 'verificaCodBarras');\n
                                             if (this.value.length < 20) { \n
                                                executaFuncaoAjax('limparCampos');\n
                                                alertaAviso('@Código de barras inválido.','form','erro','".Sessao::getId()."'); \n } ");

    // Define Objeto Select para Entidade
    $obIEntidade = new ITextBoxSelectEntidadeUsuario();
    $obIEntidade->setRotulo ( "*Entidade");
    $obIEntidade->obTextBox->obEvento->setOnChange( "var x = '".$tipoNumeracao."';\n
                                                     if (frm.inCodBarras.value=='') {\n
                                                     executaFuncaoAjax('limparCampos');\n
                                                        if ( (x == 'P') || (x == 'G' && frm.inCodRecibo.value == '') ) {
                                                            executaFuncaoAjax('limparCampos');
                                                            if (this.value != '') {
                                                                montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
                                                            }
                                                            //executaFuncaoAjax('buscaBoletim','&inCodEntidade='+this.value+'&inCodBoletim=".$_REQUEST['inCodBoletim']."');\n
                                                            executaFuncaoAjax('buscaBoletim','&inCodEntidade='+this.value);\n
                                                        }\n
                                                     }\n" );

    $jsOnload = "   if (document.getElementById('inCodEntidade').value != '') {
                     var x = '".$tipoNumeracao."';\n
                     if (frm.inCodBarras.value=='') {\n
                        if ( (x == 'P') || (x == 'G' && frm.inCodRecibo.value == '') ) {
                            montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
                            executaFuncaoAjax('buscaBoletim','&inCodEntidade='+document.getElementById('inCodEntidade').value+'&inCodBoletim=".$_REQUEST['inCodBoletim']."');\n
                        }\n
                    }
                 }\n";

    $obIEntidade->obSelect->obEvento->setOnChange( "var x = '".$tipoNumeracao."';\n
                                                    if (frm.inCodBarras.value=='') {\n
                                                        executaFuncaoAjax('limparCampos'); \n
                                                        if ( (x == 'P') || (x == 'G' && frm.inCodRecibo.value == '') ) {
                                                          executaFuncaoAjax('limparCampos');
                                                          if (this.value != '') {
                                                               montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
                                                           }
                                                          //executaFuncaoAjax('buscaBoletim','&inCodEntidade='+this.value+'&inCodBoletim=".$_REQUEST['inCodBoletim']."');\n
                                                          executaFuncaoAjax('buscaBoletim','&inCodEntidade='+this.value);\n
                                                        }\n
                                                    }\n" );

    //Define objeto textbox para o Recibo
    $obTxtCodRecibo = new TextBox();
    $obTxtCodRecibo->setTitle       ( 'Informe o número do Recibo.');
    $obTxtCodRecibo->setRotulo      ( 'Nr. Recibo');
    $obTxtCodRecibo->setName        ( 'inCodRecibo' );
    $obTxtCodRecibo->setValue       ( $inCodRecibo  );
    $obTxtCodRecibo->setId          ( 'inCodRecibo' );
    $obTxtCodRecibo->setInteiro     ( true );
    $obTxtCodRecibo->setSize        ( 15 );
    $obTxtCodRecibo->setMaxLength   ( 10 );
    $obTxtCodRecibo->obEvento->setOnChange("
    if ((this.value != '') && (jq('#inCodEntidade').val() != '')) {
        montaParametrosGET('buscaDadosRecibo', 'inCodRecibo, inCodEntidade');
    } else {
        executaFuncaoAjax('limparCampos');
        jq('#inCodRecibo').val('');
    }");

    $obHdnTipoRecibo = new Hidden;
    $obHdnTipoRecibo->setName  ( 'stTipoRecibo');
    $obHdnTipoRecibo->setId    ( 'stTipoRecibo');

    $obSpnBoletim = new Span;
    $obSpnBoletim->setId( 'spnBoletim');

    $obHdnDtBoletim = new Hidden;
    $obHdnDtBoletim->setName  ( 'stDtBoletim');
    $obHdnDtBoletim->setId    ( 'stDtBoletim');
    $obHdnDtBoletim->setValue ( $_REQUEST['stDtBoletim'] );
    
    // Define objeto BuscaInner para cgm
    $obICredor = new IPopUpCredor($obForm);
    $obICredor->obCampoCod->setId ( "inCodCredor" );
    $obICredor->obImagem->setId   ( "imgCredor" );
    $obICredor->setNull ( true );

    include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
    $obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
    $obIMontaRecursoDestinacao->setFiltro ( true );

    // Define Objeto para busca do histórico
    $obBscHistorico = new BuscaInner();
    $obBscHistorico->setRotulo                 ( "Histórico Padrão"           );
    $obBscHistorico->setTitle                  ( "Informe o histórico padrão.");
    $obBscHistorico->setId                     ( "stNomHistorico"             );
    $obBscHistorico->setValue                  ( $stNomHistorico              );
    $obBscHistorico->setNull                   ( false                        );
    $obBscHistorico->obCampoCod->setName       ( "inCodHistorico"             );
    $obBscHistorico->obCampoCod->setId         ( "inCodHistorico"             );
    $obBscHistorico->obCampoCod->setSize       ( 10                           );
    $obBscHistorico->obCampoCod->setMaxLength  ( 5                            );
    $obBscHistorico->obCampoCod->setValue      ( $inCodHistorico              );
    $obBscHistorico->obCampoCod->setAlign      ( "left"                       );
    $obBscHistorico->obImagem->setId           ( "imgHistorico"               );
    $obBscHistorico->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistorico','stNomHistorico','','".Sessao::getId()."','800','550');");
    $obBscHistorico->setValoresBusca           ( CAM_GF_CONT_POPUPS.'historicoPadrao/OCHistoricoPadrao.php?'.Sessao::getId(), $obForm->getName() );

    $obSpanContas = new Span;
    $obSpanContas->setId( "spnContas" );

    // Define Obeto Numerico para valor da arrecadacao
    $obTxtValor = new Numerico();
    $obTxtValor->setRotulo   ("*Valor"                 );
    $obTxtValor->setTitle    ("Informe o valor a pagar");
    $obTxtValor->setName     ("nuValor"                );
    $obTxtValor->setId       ("nuValor"                );
    $obTxtValor->setNull     (false                    );
    $obTxtValor->setDecimais (2                        );
    $obTxtValor->setNegativo (false                    );
    $obTxtValor->setNull     (true                     );
    $obTxtValor->setSize     (17                       );
    $obTxtValor->setMaxLength(17                       );
    $obTxtValor->setMinValue (0.01                     );
    
    if ($boMostrarComboTipoDocTcemg  == 'true' AND Sessao::getExercicio() >= '2016' ) {
        require_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoTipoDocumentoTcemgInterna.class.php';
        $obTEmpenhoTipoDocumentoTcemgInterna = new TEmpenhoTipoDocumentoTcemgInterna;
        $obTEmpenhoTipoDocumentoTcemgInterna->recuperaTodos($rstipoDocumento);
    
        $obCboDocTipo = new Select;
        $obCboDocTipo->setName('inCodDocTipo');
        $obCboDocTipo->setId('inCodDocTipo');
        $obCboDocTipo->setRotulo('Tipo de Documento');
        $obCboDocTipo->setCampoDesc('[cod_tipo] - [descricao]');
        $obCboDocTipo->setCampoId('cod_tipo');
        $obCboDocTipo->addOption('', 'Selecione');
        $obCboDocTipo->preencheCombo( $rstipoDocumento );
        $obCboDocTipo->setNull(false);
        $obCboDocTipo->obEvento->setOnChange("montaParametrosGET('montaDocumento')");
        
        $obSpnNroDocumento = new Span;
        $obSpnNroDocumento->setId( 'spnNroDocumento');
    }
     // Define o objeto para o tipo de pagamento
    if (SistemaLegado::isAL()) {
        $obTipoPagamento = new Select;
        $obTipoPagamento->setRotulo( "Tipo de Pagamento" );
        $obTipoPagamento->setName( "cmbTipoPagamento" );
        $obTipoPagamento->addOption( "", "Selecione" );
        $obTipoPagamento->addOption( "1", "Ordem Bancária" );
        $obTipoPagamento->addOption( "2", "Cheque" );
        $obTipoPagamento->setNull( false );
        $obTipoPagamento->setStyle( "width: 220px" );
        $obTipoPagamento->obEvento->setOnChange(" montaParametrosGET('montaDescricaoTipoPagamento', 'cmbTipoPagamento'); ");

        $obSpanTipoPagamento = new Span;
        $obSpanTipoPagamento->setId( "spnTipoPagamento" );
    }

    //Busca cod_uf para verificar se é o estado de Tocantins 27
    $inCodUf = SistemaLegado::pegaConfiguracao("cod_uf", 2, Sessao::getExercicio(), $boTransacao);    
    //Disponibilizar na tela de Pagamento Extra na Tesouraria o campo Tipo Pagamento para atender exigências do Tribunal de Tocantins.
    if ( $inCodUf == 27 ) {
        include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOTipoPagamento.class.php";
        $obTTCETOTipoPagamento = new TTCETOTipoPagamento();
        $obTTCETOTipoPagamento->recuperaTodos($rsTipoPagamento,"","",$boTransacao);

        // Define o objeto para o tipo de pagamento
        $obTipoPagamento = new Select;
        $obTipoPagamento->setRotulo    ( "Tipo de Pagamento" );
        $obTipoPagamento->setName      ( "inCodTipoPagamento" );
        $obTipoPagamento->setCampoId   ( 'cod_tipo'      );
        $obTipoPagamento->setCampoDesc ( '[cod_tipo] - [descricao]' );
        $obTipoPagamento->addOption    ( "", "Selecione" );
        $obTipoPagamento->setNull      ( false );
        $obTipoPagamento->setStyle     ( "width: 220px" );        
        $obTipoPagamento->preencheCombo($rsTipoPagamento);
    }

    // Define Objeto TextArea para observações
    $obTxtObs = new TextArea;
    $obTxtObs->setName   ( "stObservacoes" );
    $obTxtObs->setId     ( "stObservacoes" );
    $obTxtObs->setValue  ( $stObservacoes  );
    $obTxtObs->setRotulo ( "Observações"   );
    $obTxtObs->setTitle  ( "Informe as observações do pagamento." );
    $obTxtObs->setNull   ( true            );
    $obTxtObs->setRows   ( 2               );
    $obTxtObs->setCols   ( 100             );
    $obTxtObs->setMaxCaracteres    ( 170 );

    //Instancia um span para os cheques que possam estar vinculados a um recibo extra
    $obSpnCheques = new Span();
    $obSpnCheques->setId    ('spnCheques');
    
    $obSpnTipoTransferencia = new Span();
    $obSpnTipoTransferencia->setId    ('spnTipoTransferencia');

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obIAppletTerminal = new IAppletTerminal( $obForm );

    $obFormulario->addTitulo    ( "Dados para Pagamentos Extras"     );
    $obFormulario->addHidden    ( $obHdnAcao                    );
    $obFormulario->addHidden    ( $obHdnCtrl                    );
    $obFormulario->addHidden    ( $obIAppletTerminal            );
    $obFormulario->addHidden    ( $obHdnEval, true              );
    $obFormulario->addHidden    ( $obHdnVlSaldoContaAnalitica   );
    $obFormulario->addHidden    ( $obHdnVlSaldoContaAnaliticaBR );
    $obFormulario->addHidden    ( $obHdnTipoRecibo              );
    $obFormulario->addHidden    ( $obHdnDtRecibo                );
    $obFormulario->addHidden    ( $obHdnBoTipoDocTCEMG                );
    
    $obFormulario->addComponente( $obTxtCodBarras               );

    $obFormulario->addComponente( $obIEntidade                  );
    $obFormulario->addComponente( $obTxtCodRecibo               );

    $obFormulario->addSpan      ( $obSpnBoletim                 );
    $obFormulario->addHidden    ($obHdnDtBoletim);
    $obFormulario->addComponente( $obICredor                    );

    if ( !(SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio(), $boTransacao ) == 11 && SistemaLegado::pegaConfiguracao('cod_municipio', 2, Sessao::getExercicio(), $boTransacao ) == 79 && SistemaLegado::comparaDatas($stDataFinalAno, $stDataAtual, true)))
    $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    $obFormulario->addComponente( $obBscHistorico               );
    $obFormulario->addSpan      ( $obSpanContas                 );
    $obFormulario->addComponente( $obTxtValor                   );
    if ($boMostrarComboTipoDocTcemg  == 'true' AND Sessao::getExercicio() >= '2016' ) {
        $obFormulario->addComponente( $obCboDocTipo         );
        $obFormulario->addSpan( $obSpnNroDocumento          );
    }
    
    $obFormulario->addSpan( $obSpnTipoTransferencia             );
    //ALAGOAS
    if (SistemaLegado::isAL()) {
        $obFormulario->addComponente( $obTipoPagamento          );
        $obFormulario->addSpan      ( $obSpanTipoPagamento      );
    }
    //TOCANTINS
    if ( $inCodUf == 27 )
        $obFormulario->addComponente( $obTipoPagamento          );

    $obFormulario->addComponente( $obTxtObs                     );

    $obFormulario->addSpan      ($obSpnCheques                  );

    Sessao::write('obIEntidade', $obIEntidade);
    
    $obOk  = new Ok(true);
    $obOk->setId ("Ok");
    $obOk->obEvento->setOnClick("
        var stVlTransferencia;
        var stVlTransf;
        stVlTransferencia = document.frm.nuValor.value;
        while (stVlTransferencia.indexOf('.')>0) {
            stVlTransferencia = stVlTransferencia.replace('.','');
        }
        stVlTransf = stVlTransferencia.replace(',','.');
        var erro = false;
        if ( parseFloat(stVlTransf) > parseFloat(document.frm.nuSaldoContaAnalitica.value) ) { ;
            if ( confirm( 'O saldo da conta informada não é suficiente para realizar o pagamento.\\n (Saldo da conta: R$ '+document.frm.nuSaldoContaAnaliticaBR.value+')\\n Se efetuar este pagamento, o saldo da conta ficará negativo. Deseja continuar?')) {
                erro = false
            } else erro = true;
        }
        if ( erro == false && Valida() ) {
            //document.frm.Ok.disabled = true;
            BloqueiaFrames(true,false);
            Salvar();
            
        }");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    //$obLimpar->obEvento->setOnClick( "frm.reset(); frm.inCodBarras.focus(); document.frm.Ok.disabled = false;");
    $obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparCampos');");

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
    $obFormulario->show();
    if ($_REQUEST['inCodEntidade']) {
        $jsOnload .= " document.getElementById('inCodEntidade').value = '".$_REQUEST['inCodEntidade']."';
                       document.getElementById('stNomEntidade').value = '".$_REQUEST['stNomEntidade']."';
                       document.getElementById('stNomHistorico').value = '".$_REQUEST['stNomHistorico']."';
                       executaFuncaoAjax('buscaBoletim','&inCodEntidade=".$_REQUEST['inCodEntidade']."&inCodBoletim=".$_REQUEST['inCodBoletim']."');\n
                       montaParametrosGET('montaSpanContas');
       ";
    }
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
