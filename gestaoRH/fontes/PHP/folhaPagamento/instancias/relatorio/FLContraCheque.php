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
    * Página de Filtro do Contra-Cheque
    * Data de Criação: 29/09/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.30

    $Id: FLContraCheque.php 60142 2014-10-01 19:20:52Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"										);

//Define o nome dos arquivos PHP
$stPrograma = "ContraCheque";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgGera		= "OCGeraRelatorio".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao,inAno,inCodMes');";

Sessao::remove("filtro");
Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php");
$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao();
$obRFolhaPagamentoConfiguracao->consultar();
Sessao::write("stImpressao",$obRFolhaPagamentoConfiguracao->getImpressao());

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnEval =  new HiddenEval;
$obHdnEval->setName                             ( "stEval"                                                              );
$obHdnEval->setId                               ( "stEval"                                                              );
$obHdnEval->setValue                            ( $stEval                                                               );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                          ( "stCaminho"                                               );
$obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgProc  );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");

$obChkAtivos = new Radio();
$obChkAtivos->setName("stSituacao");
$obChkAtivos->setRotulo("Cadastro");
$obChkAtivos->setLabel("Ativos");
$obChkAtivos->setValue("ativos");
$obChkAtivos->setNull(false);
$obChkAtivos->setChecked(true);
$obChkAtivos->setTitle("Selecione o tipo de cadastro para emissão do contracheque.");
$obChkAtivos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao,inAno,inCodMes');");

$obChkAposentados = new Radio();
$obChkAposentados->setName("stSituacao");
$obChkAposentados->setLabel("Aposentados");
$obChkAposentados->setValue("aposentados");
$obChkAposentados->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao,inAno,inCodMes');");

$obChkPensionistas = new Radio();
$obChkPensionistas->setName("stSituacao");
$obChkPensionistas->setLabel("Pensionistas");
$obChkPensionistas->setValue("pensionistas");
$obChkPensionistas->obEvento->setOnChange("montaParametrosGET('gerarSpanPensionistas','stSituacao,inAno,inCodMes');");

$obChkRescindidos = new Radio();
$obChkRescindidos->setName("stSituacao");
$obChkRescindidos->setLabel("Rescindidos");
$obChkRescindidos->setValue("rescindidos");
$obChkRescindidos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao,inAno,inCodMes');");

$obChkTodos = new Radio();
$obChkTodos->setName("stSituacao");
$obChkTodos->setLabel("Todos");
$obChkTodos->setValue("todos");
$obChkTodos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao,inAno,inCodMes');");

$arChkCadastro = array($obChkAtivos,$obChkRescindidos,$obChkAposentados,$obChkPensionistas,$obChkTodos);

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obIFiltroTipoFolha = new IFiltroTipoFolha();
$obIFiltroTipoFolha->setMostraDesdobramento( true,"D" );
$obIFiltroTipoFolha->setValorPadrao("1");

$obSpnMensagem = new Span;
$obSpnMensagem->setid( "spnMensagem" );

$obIFiltroCompetencia = new IFiltroCompetencia();
$obIFiltroCompetencia->obSeletorAno->obTxtAno->obEvento->setOnChange("montaParametrosGET('atualizaCompetencia','inAno,inCodMes');");
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("montaParametrosGET('atualizaCompetencia','inAno,inCodMes');");

$obTxtMensagem = new TextArea();
$obTxtMensagem->setName("stMensagem");
$obTxtMensagem->setRotulo("Mensagem");
$obTxtMensagem->setTitle("Digite a mensagem para impressão no contra-cheque.");
$obTxtMensagem->setMaxCaracteres(240);

$obChkMensagem = new CheckBox();
$obChkMensagem->setRotulo("Mensagem Padrão para Aniversariantes");
$obChkMensagem->setName("boMensagemAniversariante");
$obChkMensagem->setId("boMensagemAniversariante");
$obChkMensagem->setValue("off");
$obChkMensagem->setTitle("Clique para apresentar a mensagem padrão para aniversariantes.");
$obChkMensagem->obEvento->setOnClick("montaParametrosGET('gerarSpanMensagem','boMensagemAniversariante');");

$obChkDuplicar = new CheckBox();
$obChkDuplicar->setRotulo("Emitir Cópia");
$obChkDuplicar->setName("boDuplicar");
$obChkDuplicar->setValue("true");
$obChkDuplicar->setTitle("Clique para emitir uma cópia do contracheque.");

$obRdoOrdemAlfabetica = new Radio;
$obRdoOrdemAlfabetica->setName( "stOrdenacao" );
$obRdoOrdemAlfabetica->setTitle( "Selecione o tipo de ordenação para os contracheques." );
$obRdoOrdemAlfabetica->setRotulo( "Ordenação" );
$obRdoOrdemAlfabetica->setLabel( "Alfabética" );
$obRdoOrdemAlfabetica->setValue( "alfabetica" );
$obRdoOrdemAlfabetica->setChecked( true );

$obRdoOrdemNumerica = new Radio;
$obRdoOrdemNumerica->setName( "stOrdenacao" );
$obRdoOrdemNumerica->setTitle( "Selecione o tipo de ordenação para os contracheques." );
$obRdoOrdemNumerica->setRotulo( "Ordenação" );
$obRdoOrdemNumerica->setLabel( "Numérica" );
$obRdoOrdemNumerica->setValue( "numerica" );

$obIContratoDigitoVerificador = new IContratoDigitoVerificador();
$obIContratoDigitoVerificador->setRotulo("Matrícula Reemissão");
$obIContratoDigitoVerificador->setTitle("Informe a matrícula a partir do qual deve ser reemitido o relatório.");
$obIContratoDigitoVerificador->setPagFiltro(true);
$obIContratoDigitoVerificador->setExtender("Reemissao");

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limparFormulario', '', true);"                    );

// //DEFINICAO DO FORM
 $obForm = new Form;
 if (Sessao::read("stImpressao") == 'laser') {
     $obForm->setAction                          ( $pgGera                         	);
     $obForm->setTarget("telaPrincipal");
 } else {
     $obForm->setAction                          ( $pgProc                                                           	);
     $obForm->setTarget("oculto");
 }

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnEval,true                                                       );
$obFormulario->addHidden                        ( $obHdnCaminho                                             			);
$obFormulario->addHidden                        ( $obHdnTipoFiltroExtra,true							);
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obFormulario->agrupaComponentes($arChkCadastro);
$obIFiltroCompetencia->geraFormulario			( $obFormulario															);
$obIFiltroTipoFolha->geraFormulario	        	( $obFormulario															);
$obFormulario->addSpan($obSpnCadastro);
$obFormulario->addComponente					( $obTxtMensagem														);
$obFormulario->addComponente					( $obChkMensagem														);
$obFormulario->addSpan							( $obSpnMensagem														);
$obFormulario->addComponente					( $obChkDuplicar														);
$obFormulario->agrupaComponentes				( array($obRdoOrdemAlfabetica,$obRdoOrdemNumerica)						);
$obIContratoDigitoVerificador->geraFormulario	( $obFormulario															);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
