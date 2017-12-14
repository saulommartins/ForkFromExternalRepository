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
    * Página de Filtro do Exportar Arquivo Sefip
    * Data de Criação: 12/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 08:32:58 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.08.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarSEFIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "montaParametrosGET('processarFiltro');";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
Sessao::write('link', '');
Sessao::write('arContratos2', array());

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

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

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("montaParametrosGET('processarCompetencia','inCodMes,inAno');");
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange($obIFiltroCompetencia->obTxtAno->obEvento->getOnChange()."montaParametrosGET('processarCompetencia','inCodMes,inAno');");

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLotacaoSubNivel(true);
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setTodos();

$obSpnCompetencia13 = new Span();
$obSpnCompetencia13->setId("spnCompetencia13");

$obCmbTipoRemessa = new Select;
$obCmbTipoRemessa->setRotulo                        ( "Tipo de Remessa"                          );
$obCmbTipoRemessa->setTitle                         ( "Selecione o tipo de remessa do arquivo."  );
$obCmbTipoRemessa->setName                          ( "inTipoRemessa"                            );
$obCmbTipoRemessa->setValue(1);
$obCmbTipoRemessa->setStyle                         ( "width: 200px"                             );
$obCmbTipoRemessa->addOption                        ( "1","GFIP"                                 );
$obCmbTipoRemessa->addOption                        ( "2","DERF"                                 );

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMARecolhimento.class.php");
$obTIMARecolhimento = new TIMARecolhimento();
$obTIMARecolhimento->recuperaTodos($rsRecolhimento);

$obTxtCodRecolhimento = new TextBox;
$obTxtCodRecolhimento->setRotulo              ( "Código de Recolhimento"               );
$obTxtCodRecolhimento->setTitle               ( "Selecione o código de recolhimento da sefip." );
$obTxtCodRecolhimento->setName                ( "inCodRecolhimentoTxt"                 );
$obTxtCodRecolhimento->setValue               ( 115                                    );
$obTxtCodRecolhimento->setSize                ( 6                                      );
$obTxtCodRecolhimento->setMaxLength           ( 3                                      );
$obTxtCodRecolhimento->setInteiro             ( true                                   );
$obTxtCodRecolhimento->setSize                ( 10                                     );

$obCmbCodRecolhimento = new Select;
$obCmbCodRecolhimento->setRotulo              ( "Código de Recolhimento");
$obCmbCodRecolhimento->setName                ( "inCodRecolhimento"     );
$obCmbCodRecolhimento->setValue               ( 115                     );
$obCmbCodRecolhimento->setStyle               ( "width: 450px"          );
$obCmbCodRecolhimento->setCampoID             ( "cod_recolhimento"      );
$obCmbCodRecolhimento->setCampoDesc           ( "descricao"             );
$obCmbCodRecolhimento->preencheCombo          ( $rsRecolhimento         );

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAIndicadorRecolhimento.class.php");
$obTIMAIndicadorRecolhimento = new TIMAIndicadorRecolhimento();
$obTIMAIndicadorRecolhimento->recuperaTodos($rsIndicadorRecolhimento);

$obTxtCodIndicadorRecolhimento = new TextBox;
$obTxtCodIndicadorRecolhimento->setRotulo              ( "Indicador de Recolhimento"    );
$obTxtCodIndicadorRecolhimento->setTitle               ( "Informe se o recolhimento do FGTS será no prazo ou sem atraso." );
$obTxtCodIndicadorRecolhimento->setName                ( "inCodIndicadorRecolhimentoTxt");
$obTxtCodIndicadorRecolhimento->setValue               ( 1                              );
$obTxtCodIndicadorRecolhimento->setSize                ( 6                              );
$obTxtCodIndicadorRecolhimento->setMaxLength           ( 3                              );
$obTxtCodIndicadorRecolhimento->setInteiro             ( true                           );
$obTxtCodIndicadorRecolhimento->setSize               ( 10    );
$obTxtCodIndicadorRecolhimento->obEvento->setOnChange("montaParametrosGET('geraSpanDataRecolhimentoFGTS','inCodIndicadorRecolhimento');");

$obCmbCodIndicadorRecolhimento = new Select;
$obCmbCodIndicadorRecolhimento->setRotulo              ( "Indicador de Recolhimento" );
$obCmbCodIndicadorRecolhimento->setName                ( "inCodIndicadorRecolhimento");
$obCmbCodIndicadorRecolhimento->setValue               ( 1                           );
$obCmbCodIndicadorRecolhimento->setStyle               ( "width: 200px"              );
$obCmbCodIndicadorRecolhimento->setCampoID             ( "cod_indicador"             );
$obCmbCodIndicadorRecolhimento->setCampoDesc           ( "descricao"                 );
$obCmbCodIndicadorRecolhimento->addOption("","Selecione");
$obCmbCodIndicadorRecolhimento->obEvento->setOnChange("montaParametrosGET('geraSpanDataRecolhimentoFGTS','inCodIndicadorRecolhimento');");
$obCmbCodIndicadorRecolhimento->preencheCombo          ( $rsIndicadorRecolhimento    );

$obSpnDataRecolhimento = new Span();
$obSpnDataRecolhimento->setId("spnDataRecolhimento");

$rsIndicadorRecolhimento->setPrimeiroElemento();
$obTxtCodIndicadorRecolhimentoPrevidencia = new TextBox;
$obTxtCodIndicadorRecolhimentoPrevidencia->setRotulo              ( "Indicador de Recolhimento da Previdência" );
$obTxtCodIndicadorRecolhimentoPrevidencia->setTitle               ( "Informe se o recolhimento da previdência será no prazo ou em atrazo." );
$obTxtCodIndicadorRecolhimentoPrevidencia->setName                ( "inCodIndicadorRecolhimentoPrevidenciaTxt" );
$obTxtCodIndicadorRecolhimentoPrevidencia->setValue               ( 1                                          );
$obTxtCodIndicadorRecolhimentoPrevidencia->setSize                ( 6                                          );
$obTxtCodIndicadorRecolhimentoPrevidencia->setMaxLength           ( 3                                          );
$obTxtCodIndicadorRecolhimentoPrevidencia->setInteiro             ( true                                       );
$obTxtCodIndicadorRecolhimentoPrevidencia->setSize                ( 10                                         );
$obTxtCodIndicadorRecolhimentoPrevidencia->obEvento->setOnChange  ("montaParametrosGET('geraSpanDataRecolhimentoPrevidencia','inCodIndicadorRecolhimentoPrevidencia');");

$obCmbCodIndicadorRecolhimentoPrevidencia = new Select;
$obCmbCodIndicadorRecolhimentoPrevidencia->setRotulo              ( "Indicador de Recolhimento da Previdência");
$obCmbCodIndicadorRecolhimentoPrevidencia->setName                ( "inCodIndicadorRecolhimentoPrevidencia"     );
$obCmbCodIndicadorRecolhimentoPrevidencia->setValue               ( 1                                           );
$obCmbCodIndicadorRecolhimentoPrevidencia->setStyle               ( "width: 200px"                              );
$obCmbCodIndicadorRecolhimentoPrevidencia->setCampoID             ( "cod_indicador"                             );
$obCmbCodIndicadorRecolhimentoPrevidencia->setCampoDesc           ( "descricao"                                 );
$obCmbCodIndicadorRecolhimentoPrevidencia->addOption              ( "", "Selecione"                             );
$obCmbCodIndicadorRecolhimentoPrevidencia->obEvento->setOnChange  ("montaParametrosGET('geraSpanDataRecolhimentoPrevidencia','inCodIndicadorRecolhimentoPrevidencia');");
$obCmbCodIndicadorRecolhimentoPrevidencia->preencheCombo          ( $rsIndicadorRecolhimento                    );

$obSpnDataRecolhimentoPrevidencia = new Span();
$obSpnDataRecolhimentoPrevidencia->setId("spnDataRecolhimentoPrevidencia");

$obLblCnaeFiscal = new Label();
$obLblCnaeFiscal->setRotulo("CNAE Fiscal");
$obLblCnaeFiscal->setId("cnae_fiscal");
$obLblCnaeFiscal->setValue("");

$obHdnCnaeFiscal = new hidden();
$obHdnCnaeFiscal->setName("cnae_fiscal");
$obHdnCnaeFiscal->setValue("");

$obLblCodCentralizacao = new Label();
$obLblCodCentralizacao->setRotulo("Código de Centralização");
$obLblCodCentralizacao->setId("centralizacao");
$obLblCodCentralizacao->setValue("");

$obHdnCodCentralizacao = new hidden();
$obHdnCodCentralizacao->setName("centralizacao");
$obHdnCodCentralizacao->setValue("");

$obLblFpas = new Label();
$obLblFpas->setRotulo("FPAS");
$obLblFpas->setId("fpas");
$obLblFpas->setValue("");

$obHdnFpas = new hidden();
$obHdnFpas->setName("fpas");
$obHdnFpas->setValue("");

$obLblGPS = new Label();
$obLblGPS->setRotulo("Código de Pagamento GPS");
$obLblGPS->setId("gps");
$obLblGPS->setValue("");

$obHdnGPS = new hidden();
$obHdnGPS->setName("gps");
$obHdnGPS->setValue("");

$obSpanArrecadouFGTS = new Span();
$obSpanArrecadouFGTS->setId("spnArrecadouFGTS");

$obSpanMatriculasRescindidas = new Span();
$obSpanMatriculasRescindidas->setId("spnMatriculasRescindidas");

$obChkSefipRetificadora = new Checkbox();
$obChkSefipRetificadora->setRotulo("Sefip Retificadora?");
$obChkSefipRetificadora->setName("boSefipRetificadora");
$obChkSefipRetificadora->setValue("sim");
$obChkSefipRetificadora->setLabel("Sim");
$obChkSefipRetificadora->setTitle("Selecionar para emitir a sefip retificadora. Nesse caso, deverá ser emitido todo o arquivo da sefip (com todos os servidores do arquivo original), porém apenas para as matrículas que sofrem alteração de valores, identificá-las no campo específico abaixo");

$obSpnSefipRetificadora = new Span();
$obSpnSefipRetificadora->setId("spnSefipRetificadora");

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "montaParametrosGET('limparFiltro','',true);"                         );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setTarget("oculto");
$obForm->setAction                              ( $pgProc                                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $stTitulo ,"right"                                                    );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnEval,true                                                       );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                               );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpnCompetencia13);
$obFormulario->addComponente($obCmbTipoRemessa);
$obFormulario->addComponenteComposto($obTxtCodRecolhimento,$obCmbCodRecolhimento);
$obFormulario->addComponenteComposto($obTxtCodIndicadorRecolhimento,$obCmbCodIndicadorRecolhimento);
$obFormulario->addSpan($obSpnDataRecolhimento);
$obFormulario->addComponenteComposto($obTxtCodIndicadorRecolhimentoPrevidencia,$obCmbCodIndicadorRecolhimentoPrevidencia);
$obFormulario->addSpan($obSpnDataRecolhimentoPrevidencia);
$obFormulario->addComponente($obLblCnaeFiscal);
$obFormulario->addComponente($obLblCodCentralizacao);
$obFormulario->addComponente($obLblFpas);
$obFormulario->addComponente($obLblGPS);
$obFormulario->addHidden($obHdnCnaeFiscal);
$obFormulario->addHidden($obHdnCodCentralizacao);
$obFormulario->addHidden($obHdnFpas);
$obFormulario->addHidden($obHdnGPS);
$obFormulario->addSpan($obSpanArrecadouFGTS);
$obFormulario->addSpan($obSpanMatriculasRescindidas);
$obFormulario->addComponente($obChkSefipRetificadora);
$obFormulario->addSpan($obSpnSefipRetificadora);
$obFormulario->defineBarra( array($obBtnOk,$obBtnLimpar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
