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
    * Página de Formulário para Formulario de Arrecadação de Receita Extra Orçamentária
    * Data de Criação   : 14/09/2006
    *
    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Id: FMManterArrecadacaoReceitaExtra.php 60237 2014-10-08 11:41:58Z jean $

    * Casos de uso: uc-02.04.26

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php");
include_once( CAM_GF_ORC_COMPONENTES."IPopUpRecurso.class.php");
include_once ( CAM_GF_EMP_MAPEAMENTO.'TEmpenhoConfiguracao.class.php' );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );
include_once ( CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoTransferencia.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceitaExtra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
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
    include( $pgJs );

    $obTEmpenhoConfiguracao = new TEmpenhoConfiguracao;
    $obTEmpenhoConfiguracao->setDado( 'parametro', 'numero_empenho' );
    $obTEmpenhoConfiguracao->consultar ();
    $tipoNumeracao = $obTEmpenhoConfiguracao->getDado( 'valor' );

    $obForm = new Form;
    $obForm->setAction( $pgProc );
    $obForm->setTarget( "oculto" );

    Sessao::write('inCodPlanoDebito', $request->get('inCodPlanoDebito'));
    Sessao::write('stNomContaDebito', $request->get('stNomContaDebito'));
    Sessao::write('inCodEntidade', $request->get('inCodEntidade'));

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ( "stCtrl" );
    $obHdnCtrl->setValue( $request->get('stCtrl') );

    $obHdnDtRecibo= new Hidden;
    $obHdnDtRecibo->setName ( "stDtRecibo" );
    $obHdnDtRecibo->setId( "stDtRecibo" );
    $obHdnDtRecibo->setValue( $request->get('stDtRecibo') );

    $stHdnValor = "
        if (document.frm.inCodPlanoDebito.value == '') {
             document.frm.nuSaldoContaAnalitica.value = '';
             erro = true;
             mensagem += '@Deve ser informada uma conta de caixa/banco!';
        }
        if (document.frm.inCodPlanoCredito.value == '') {
             erro = true;
             mensagem += '@Deve ser informada uma conta de receita!';
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
        var stVlTransferencia;
        var stVlTransf;
        stVlTransferencia = document.frm.nuValor.value;
        while (stVlTransferencia.indexOf('.')>0) {
            stVlTransferencia = stVlTransferencia.replace('.','');
        }
        stVlTransf = stVlTransferencia.replace(',','.');
        if ( parseFloat(stVlTransf) > parseFloat(document.frm.nuSaldoContaAnalitica.value) ) { ;
            if ( confirm( 'O saldo da conta informada não é suficiente para realizar a arrecadacao.\\n (Saldo da conta: R$ '+document.frm.nuSaldoContaAnaliticaBR.value+')\\n Se efetuar esta arrecadação, o saldo da conta ficará negativo. Deseja continuar?')) {
                erro = false
            } else erro = true;
        } ";

    $stHdnEvalValor = "
        if (document.frm.inCodPlanoCredito) {
            if (document.frm.inCodPlanoCredito.value == '') {
                erro = true;
                mensagem += '@Deve ser informada uma conta de receita!';
            }
        }";

    $obHdnEval = new HiddenEval;
    $obHdnEval->setName( "stEval" );
    $obHdnEval->setValue( $stHdnEvalValor );

    $obHdnVlSaldoContaAnalitica = new Hidden;
    $obHdnVlSaldoContaAnalitica->setName (   "nuSaldoContaAnalitica" );
    $obHdnVlSaldoContaAnalitica->setId   (   "nuSaldoContaAnalitica" );
    $obHdnVlSaldoContaAnalitica->setValue(   $request->get('nuSaldoContaAnalitica') );

    $obHdnVlSaldoContaAnaliticaBR = new Hidden;
    $obHdnVlSaldoContaAnaliticaBR->setName ( "nuSaldoContaAnaliticaBR" );
    $obHdnVlSaldoContaAnaliticaBR->setId   ( "nuSaldoContaAnaliticaBR" );
    $obHdnVlSaldoContaAnaliticaBR->setValue( $request->get('nuSaldoContaAnaliticaBR') );

    //Define Objeto TextBox para Código de Barras
    $obTxtCodBarras = new TextBox();
    $obTxtCodBarras->setTitle     ( 'Informe o Código de Barras. posicionando o leitor sobre o mesmo.' );
    $obTxtCodBarras->setRotulo    ( 'Código de Barras'                                    );
    $obTxtCodBarras->setName      ( 'inCodBarras'                                       );
    $obTxtCodBarras->setId        ( 'inCodBarras'                                       );
    $obTxtCodBarras->setInteiro   ( true                                                  );
    $obTxtCodBarras->setNull      ( true                                                  );
    $obTxtCodBarras->setSize      ( 23                                                    );
    $obTxtCodBarras->setMaxLength ( 19                                                    );
    $obTxtCodBarras->obEvento->setOnChange(" if (this.value.length == 19) ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodBarras='+this.value, 'verificaCodBarras');\n
                                             if (this.value.length < 19) { \n
                                                executaFuncaoAjax('limparCampos');\n
                                                alertaAviso('@Código de barras inválido.','form','erro','".Sessao::getId()."'); \n } ");

    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    $obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio() );
    $obTOrcamentoEntidade->recuperaEntidades( $rsEntidade );
    $inCodEntidade = "";

    if ($rsEntidade->getNumLinhas() == 1) {
        $inCodEntidade = $rsEntidade->getCampo('cod_entidade');
    }

    // Define Objeto Select para Entidade
    $obIEntidade = new ITextBoxSelectEntidadeUsuario();
    $obIEntidade->obTextBox->obEvento->setOnChange( "var x = '".$tipoNumeracao."';\n
                                                     if (document.getElementById('inCodBarras').value=='') {\n
                                                        if (this.value == '') executaFuncaoAjax('limparCampos');\n
                                                        else if ( (x == 'P') || (x == 'G' && $('inCodRecibo').value == '') ) {
                                                           executaFuncaoAjax('limparCampos');
                                                           montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
                                                           executaFuncaoAjax('buscaBoletim','&inCodEntidade='+this.value+'&inCodBoletim=".$request->get('inCodBoletim')."');\n
                                                        }\n
                                                     }\n" );
    $jsOnload = "   if (document.getElementById('inCodEntidade').value != '') {
                     var x = '".$tipoNumeracao."';\n
                     if (frm.inCodBarras.value=='') {\n
                        if ( (x == 'P') || (x == 'G' && frm.inCodRecibo.value == '') ) {
                            montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
                            executaFuncaoAjax('buscaBoletim','&inCodEntidade='+document.getElementById('inCodEntidade').value+'&inCodBoletim=".$request->get('inCodBoletim')."');\n
                        }\n
                     }
                    }\n";
    $obIEntidade->obSelect->obEvento->setOnChange( "var x = '".$tipoNumeracao."';\n
                                                    if (document.getElementById('inCodBarras').value=='') {\n
                                                        if (this.value == '') executaFuncaoAjax('limparCampos'); \n
                                                        else if ( (x == 'P') || (x == 'G' && document.getElementById('inCodRecibo').value == '') ) {
                                                          executaFuncaoAjax('limparCampos');
                                                          montaParametrosGET('montaSpanContas' , 'inCodEntidade');\n
                                                          executaFuncaoAjax('buscaBoletim','&inCodEntidade='+this.value+'&inCodBoletim=".$request->get('inCodBoletim')."');\n
                                                        }\n
                                                    }\n" );
    if(isset($inCodEntidade))
        $obIEntidade->setCodEntidade( $inCodEntidade );

    //Define objeto textbox para o Recibo
    $obTxtCodRecibo = new TextBox();
    $obTxtCodRecibo->setTitle       ( 'Informe o número do recibo.');
    $obTxtCodRecibo->setRotulo      ( 'Nr. Recibo');
    $obTxtCodRecibo->setName        ( 'inCodRecibo' );
    $obTxtCodRecibo->setValue       ( $request->get('inCodRecibo') );
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

    $obSpanBoletim = new Span;
    $obSpanBoletim->setId ( 'spnBoletim' );

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
    $obBscHistorico->setValue                  ( $request->get('stNomHistorico')  );
    $obBscHistorico->setNull                   ( false                        );
    $obBscHistorico->obCampoCod->setName       ( "inCodHistorico"             );
    $obBscHistorico->obCampoCod->setId         ( "inCodHistorico"             );
    $obBscHistorico->obCampoCod->setSize       ( 10                           );
    $obBscHistorico->obCampoCod->setMaxLength  ( 5                            );
    $obBscHistorico->obCampoCod->setValue      ( $request->get('inCodHistorico')  );
    $obBscHistorico->obCampoCod->setAlign      ( "left"                       );
    $obBscHistorico->obImagem->setId           ( "imgHistorico"               );
    $obBscHistorico->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistorico','stNomHistorico','','".Sessao::getId()."','800','550');");
    $obBscHistorico->setValoresBusca           ( CAM_GF_CONT_POPUPS.'historicoPadrao/OCHistoricoPadrao.php?'.Sessao::getId(), $obForm->getName() );

    $obSpanContas = new Span;
    $obSpanContas->setId( "spnContas" );

    // Define Obeto Numerico para valor da arrecadacao
    $obTxtValor = new Numerico();
    $obTxtValor->setRotulo   ("*Valor"                     );
    $obTxtValor->setTitle    ("Informe o valor a arrecadar");
    $obTxtValor->setName     ("nuValor"                    );
    $obTxtValor->setId       ("nuValor"                    );
    $obTxtValor->setNull     (false                        );
    $obTxtValor->setDecimais (2                            );
    $obTxtValor->setNegativo (false                        );
    $obTxtValor->setNull     (true                         );
    $obTxtValor->setSize     (17                           );
    $obTxtValor->setMaxLength(17                           );
    $obTxtValor->setMinValue (0.01                         );

    // Define Objeto TextArea para observações
    $obTxtObs = new TextArea;
    $obTxtObs->setName   ( "stObservacoes" );
    $obTxtObs->setId     ( "stObservacoes" );
    $obTxtObs->setValue  ( $request->get('stObservacoes') );
    $obTxtObs->setRotulo ( "Observações"   );
    $obTxtObs->setTitle  ( "Informe as observações da arrecadação." );
    $obTxtObs->setNull   ( true            );
    $obTxtObs->setRows   ( 2               );
    $obTxtObs->setCols   ( 100             );
    $obTxtObs->setMaxCaracteres    ( 170 );
    
    $inCodUf = SistemaLegado::pegaConfiguracao('cod_uf');
    
    $obSpnTipoTransferencia = new Span;
    $obSpnTipoTransferencia->setId ( "spnTipoTransferencia" );
    
    $obSpnEntidadeTransferidora = new Span;
    $obSpnEntidadeTransferidora->setId ( "spnEntidadeTransferidora" );

    //****************************************//
    //Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm( $obForm );

    $obIAppletTerminal = new IAppletTerminal( $obForm );

    $obFormulario->addTitulo    ( "Dados para Arrecadações"     );
    $obFormulario->addHidden    ( $obHdnAcao                    );
    $obFormulario->addHidden    ( $obHdnCtrl                    );
    $obFormulario->addHidden    ( $obHdnDtRecibo                );
    $obFormulario->addHidden    ( $obIAppletTerminal            );
    $obFormulario->addHidden    ( $obHdnEval, true              );
    $obFormulario->addHidden    ( $obHdnVlSaldoContaAnalitica   );
    $obFormulario->addHidden    ( $obHdnVlSaldoContaAnaliticaBR );
    $obFormulario->addHidden    ( $obHdnTipoRecibo              );
    $obFormulario->addComponente( $obTxtCodBarras               );
    $obFormulario->addComponente( $obIEntidade                  );
    $obFormulario->addComponente( $obTxtCodRecibo               );
    
    $obFormulario->addSpan      ( $obSpanBoletim                );
    $obFormulario->addComponente( $obICredor                    );
    
    $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario  );
    
    $obFormulario->addComponente( $obBscHistorico               );
    $obFormulario->addSpan      ( $obSpanContas                 );
    $obFormulario->addComponente( $obTxtValor                   );
    $obFormulario->addSpan      ( $obSpnEntidadeTransferidora   );
    $obFormulario->addSpan      ( $obSpnTipoTransferencia       );
    $obFormulario->addComponente( $obTxtObs                     );

    Sessao::write('obIEntidade', $obIEntidade);

    $obOk  = new Ok(true);
    $obOk->setId ("Ok");

    $obLimpar = new Button;
    $obLimpar->setValue( "Limpar" );
    $obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparCampos');");

    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
    $obFormulario->show();
    if ($request->get('inCodEntidade')) {
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
