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
    * Página de Formulário do Registro de Evento de Décimo Terceiro
    * Data de Criação: 06/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 10:50:26 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php"                                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once (CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php"                                    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoDecimo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php?".Sessao::getId();
$pgJS       = "JS".$stPrograma.".js";
include_once($pgJS);

$obTPessoalAdidoCedido = new TPessoalAdidoCedido();
$stFiltro = " AND contrato.cod_contrato = ".$_GET['inCodContrato'];
$obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltro);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stLocation  = $pgList.'?'.Sessao::getId()."&stAcao=".$stAcao."&stTipoFiltro=".$_REQUEST['stTipoFiltro'];
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato":
    case "cgm_contrato":
        $stLocation .= "&inContrato=".$_REQUEST['inContrato'];
        break;
    case "cargo":
        $stLocation .= "&inCodRegime=".$_REQUEST['inCodRegime'];
        $stLocation .= "&inCodSubDivisao=".$_REQUEST['inCodSubDivisao'];
        $stLocation .= "&inCodCargo=".$_REQUEST['inCodCargo'];
        $stLocation .= "&inCodEspecialidade=".$_REQUEST['inCodEspecialidade'];
        break;
    case "funcao":
        $stLocation .= "&inCodRegime=".$_REQUEST['inCodRegime'];
        $stLocation .= "&inCodSubDivisao=".$_REQUEST['inCodSubDivisao'];
        $stLocation .= "&inCodEspecialidade=".$_REQUEST['inCodEspecialidade'];
        $stLocation .= "&inCodFuncao=".$_REQUEST['inCodFuncao'];
        break;
    case "lotacao":
        $stLocation .= "&inCodLotacao=".$_REQUEST['inCodLotacao'];
        break;
    case "local":
        $stLocation .= "&inCodLocal=".$_REQUEST['inCodLocal'];
        break;
    case "padrao":
        $stLocation .= "&inCodPadrao=".$_REQUEST['inCodPadrao'];
        break;
}

if( ($rsAdidoCedido->getCampo("tipo_cedencia") == "a" and $rsAdidoCedido->getCampo("indicativo_onus") == "c") or
    ($rsAdidoCedido->getCampo("tipo_cedencia") == "c" and $rsAdidoCedido->getCampo("indicativo_onus") == "e")){
    $stMensagem = "Para tipo de cedência adido/cedido e indicativo de ônus cedente/cessionário não é permitido registro de eventos.";
    $obLblMensagem = new Label;
    $obLblMensagem->setRotulo               ( "Situação"                                                );
    $obLblMensagem->setValue                ( $stMensagem                                               );

    $obBtnVoltar = new Voltar();
    $obBtnVoltar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addComponente($obLblMensagem);
    $obFormulario->defineBarra(array($obBtnVoltar),"","");
    $obFormulario->show();
} else {
    $jsOnload   = "executaFuncaoAjax('processarForm','&inCodContrato=".$_REQUEST['inCodContrato']."');";

    include_once($pgJS);

    $obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

    //DEFINICAO DOS COMPONENTES
    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName                             ( "stAcao"                                                              );
    $obHdnAcao->setValue                            ( $stAcao                                                               );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                                              );
    $obHdnCtrl->setValue                            ( $stCtrl                                                               );

    $obHdnContrato =  new Hidden;
    $obHdnContrato->setName                         ( "inCodContrato"                                                       );
    $obHdnContrato->setValue                        ( $_REQUEST['inCodContrato']                                            );

    $obHdnRegistro =  new Hidden;
    $obHdnRegistro->setName                         ( "inRegistro"                                                          );
    $obHdnRegistro->setValue                        ( $_REQUEST['inRegistro']                                               );

    $obHdnCargo =  new Hidden;
    $obHdnCargo->setName                            ( "inCodCargo"                                                          );
    $obHdnCargo->setValue                           ( $_REQUEST['inCodCargo']                                               );

    $obHdnSubDivisao =  new Hidden;
    $obHdnSubDivisao->setName                       ( "inCodSubDivisao"                                                     );
    $obHdnSubDivisao->setValue                      ( $_REQUEST['inCodSubDivisao']                                          );

    $obHdnEspecialidade =  new Hidden;
    $obHdnEspecialidade->setName                    ( "inCodEspecialidade"                                                  );
    $obHdnEspecialidade->setValue                   ( $_REQUEST['inCodEspecialidade']                                       );

    $obHdnPeriodoMovimentacao =  new Hidden;
    $obHdnPeriodoMovimentacao->setName              ( "inCodPeriodoMovimentacao"                                            );
    $obHdnPeriodoMovimentacao->setValue             ( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao")           );

    $obLblContrato = new Label;
    $obLblContrato->setName                         ( "stContrato"                                                          );
    $obLblContrato->setRotulo                       ( "Matrícula"                                                           );
    $obLblContrato->setValue                        ( $_REQUEST['inRegistro']                                               );

    $obLblCGM = new Label;
    $obLblCGM->setName                              ( "stCGM"                                                               );
    $obLblCGM->setRotulo                            ( "CGM"                                                                 );
    $obLblCGM->setValue                             ( $_REQUEST['inNumCGM'] ."-".$_REQUEST['stNomCGM']                      );

    $obIBscEvento = new IBscEvento;
    $obIBscEvento->obBscInnerEvento->setNullBarra   ( false                                                                 );
    $obIBscEvento->setInformarValorQuantidade       ( true                                                                  );
    $obIBscEvento->setInformarQuantidadeParcelas    ( true                                                                  );
    $obIBscEvento->obBscInnerEvento->obCampoCod->obEvento->setOnChange("");
    $obIBscEvento->obBscInnerEvento->obCampoCod->obEvento->setOnBlur("ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBscEvento.php?".Sessao::getId()."&inCodigoEvento='+this.value+'&stCampoNomEvento=stEvento&stCampoCodEvento=inCodigoEvento', 'preencheDescEvento' ); " );
    
    $obTxtDesdobramento = new TextBox;
    $obTxtDesdobramento->setRotulo                  ( "Desdobramento"                                                       );
    $obTxtDesdobramento->setName                    ( "stDesdobramento"                                                     );
    $obTxtDesdobramento->setId                      ( "stDesdobramento"                                                     );
    $obTxtDesdobramento->setValue                   ( $stDesdobramento                                                      );
    $obTxtDesdobramento->setTitle                   ( "Selecione o desdobramento."                                          );
    $obTxtDesdobramento->setSize                    ( 5                                                                     );
    $obTxtDesdobramento->setMaxLength               ( 5                                                                     );
    $obTxtDesdobramento->setNullBarra               ( false                                                                 );

    $obCmbDesdobramento = new Select;
    $obCmbDesdobramento->setName                    ( "stCmbDesdobramento"                                                  );
    $obCmbDesdobramento->setId                      ( "stCmbDesdobramento"                                                  );
    $obCmbDesdobramento->setValue                   ( $stDesdobramento                                                      );
    $obCmbDesdobramento->setRotulo                  ( "Desdobramento"                                                       );
    $obCmbDesdobramento->setTitle                   ( "Selecione o desdobramento."                                          );
    $obCmbDesdobramento->setNullBarra               ( false                                                                 );
    $obCmbDesdobramento->addOption                  ( "", "Selecione"                                                       );

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado( "cod_modulo", "27"                         );
    $obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio()           );
    $obTAdministracaoConfiguracao->setDado( "parametro" , "mes_calculo_decimo".Sessao::getEntidade()         );
    $obTAdministracaoConfiguracao->recuperaPorChave($rsAdministracao)   ;
    $inMesPagamentoSaldo = $rsAdministracao->getCampo("valor");
    $inMesCompetencia = (int) substr($rsUltimaMovimentacao->getCampo("dt_final"),3,2);
    Sessao::write('inMesCompetencia',$inMesCompetencia);
    switch (true) {
        case $inMesCompetencia == 12:
            $obCmbDesdobramento->addOption          ( "D", "13º Salário"                                                    );
            $obCmbDesdobramento->addOption          ( "C", "Complemento 13º Salario"                                        );
            break;
        case $inMesCompetencia < $inMesPagamentoSaldo:
            $obCmbDesdobramento->addOption          ( "A", "Adiantamento"                                                   );
            break;
        case $inMesCompetencia == $inMesPagamentoSaldo:
            $obCmbDesdobramento->addOption          ( "D", "13º Salário"                                                    );
            break;
    }
    $obCmbDesdobramento->setStyle                   ( "width: 250px"                                                        );

    $arIncluirAlterar = array($obIBscEvento->obBscInnerEvento,
                              $obIBscEvento->obTxtValor,
                              $obIBscEvento->obTxtQuantidade,
                              $obIBscEvento->obTxtQuantidadeParcelas,
                              $obIBscEvento->obSpnDadosEvento,
                              $obTxtDesdobramento,
                              $obCmbDesdobramento);

    Sessao::write('arIncluirAlterar',$arIncluirAlterar);

    $obSpnEventosCadastrados = new Span;
    $obSpnEventosCadastrados->setId                 ( "spnEventosCadastrados"                                               );

    $obSpnEventosBase = new Span;
    $obSpnEventosBase->setId                        ( "spnEventosBase"                                                      );

    $obSpnBotoes = new Span;
    $obSpnBotoes->setId                             ( "spnBotoes"                                                           );

    $obHdnEvalBotoes =  new HiddenEval;
    $obHdnEvalBotoes->setName                       ( "hdnEvalBotoes"                                                       );
    $obHdnEvalBotoes->setValue                      ( $hdnEvalBotoes                                                        );

    $obHdnOkRetorno = new hidden();
    $obHdnOkRetorno->setName("stOkRetorno");

    $obBtnOkFiltro = new Ok;
    $obBtnOkFiltro->setName("okFiltro");
    $obBtnOkFiltro->setValue("OK/Filtro");
    $obBtnOkFiltro->obEvento->setOnClick("salvarOkFiltro();");

    $obBtnOkLista = new Ok;
    $obBtnOkLista->setName("okLista");
    $obBtnOkLista->setValue("OK/Lista");
    $obBtnOkLista->obEvento->setOnClick("salvarOkLista();");

    $obBtnCancelar = new Button;
    $obBtnCancelar->setName                         ( 'cancelar'                                            );
    $obBtnCancelar->setValue                        ( 'Cancelar'                                            );
    $obBtnCancelar->obEvento->setOnClick            ( "Cancelar('".$stLocation."');"                        );

    //DEFINICAO DO FORM
    $obForm = new Form;
    $obForm->setAction                              ( $pgProc                                                               );
    $obForm->setTarget                              ( "oculto"                                                              );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new FormularioAbas;
    $obFormulario->addForm                          ( $obForm                                                               );
    $obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
    $obFormulario->addHidden                        ( $obHdnAcao                                                            );
    $obFormulario->addHidden                        ( $obHdnCtrl                                                            );
    $obFormulario->addHidden                        ( $obHdnContrato                                                        );
    $obFormulario->addHidden                        ( $obHdnCargo                                                           );
    $obFormulario->addHidden                        ( $obHdnSubDivisao                                                      );
    $obFormulario->addHidden                        ( $obHdnEspecialidade                                                   );
    $obFormulario->addHidden                        ( $obHdnRegistro                                                        );
    $obFormulario->addHidden                        ( $obHdnPeriodoMovimentacao                                             );
    $obFormulario->addTitulo                        ( "Dados da Matrícula do Servidor"                                       );
    $obFormulario->addComponente                    ( $obLblContrato                                                        );
    $obFormulario->addComponente                    ( $obLblCGM                                                             );
    $obFormulario->addAba                           ( "Eventos"                                                             );
    $obIBscEvento->geraFormulario                   ( $obFormulario                                                         );
    $obFormulario->addComponenteComposto            ( $obTxtDesdobramento,$obCmbDesdobramento                               );
    $obFormulario->addSpan                          ( $obSpnBotoes                                                          );
    $obFormulario->addHidden                        ( $obHdnEvalBotoes,true                                                 );
    $obFormulario->addSpan                          ( $obSpnEventosCadastrados                                              );
    $obFormulario->addAba                           ( "Base"                                                                );
    $obFormulario->addSpan                          ( $obSpnEventosBase                                                     );
    $obFormulario->addHidden($obHdnOkRetorno);
    $obFormulario->defineBarra(array($obBtnOkFiltro,$obBtnOkLista,$obBtnCancelar));
    $obFormulario->show();
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
