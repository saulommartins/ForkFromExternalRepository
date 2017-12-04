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
    * Filtro
    * Data de Criação: 07/02/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.41

    $Id: FLConsultarFichaFinanceira.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarFichaFinanceira";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
$jsOnload = "buscaValor('habilitaSpan');";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stTipoCalculo = $_POST["stTipoCalculo"] ? $_POST["stTipoCalculo"] : $_GET["stTipoCalculo"];

include_once($pgJS);
//include_once($pgOcul);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnTipoCalculo =  new Hidden;
$obHdnTipoCalculo->setName                     ( "stTipoCalculo"                                             );
$obHdnTipoCalculo->setValue                    ( $stTipoCalculo                                              );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

$obTextoComplementar =  new Hidden;
$obTextoComplementar->setName                             ( "stTextoComplementar"                                              );
$obTextoComplementar->setValue                            ( $stTextoComplementar                                               );
$obTextoComplementar->setId                               ( "stTextoComplementar"                                              );

$obHdnTextoComplementar =  new Hidden;
$obHdnTextoComplementar->setName                             ( "hdnTextoComplementar"                                              );
$obHdnTextoComplementar->setValue                            ( $hdnTextoComplementar                                               );
$obHdnTextoComplementar->setId                               ( "hdnTextoComplementar"                                              );

$obRdoContrato = new Radio;
$obRdoContrato->setName                         ( "stOpcao"                                             );
$obRdoContrato->setTitle                        ( "Selecione a opção para a consulta."                  );
$obRdoContrato->setRotulo                       ( "Opções"                                              );
$obRdoContrato->setLabel                        ( "Matrícula"                                           );
$obRdoContrato->setValue                        ( "contrato"                                            );
$obRdoContrato->obEvento->setOnChange           ( "buscaValor('habilitaSpan');"                         );
$obRdoContrato->setChecked                      ( $stOpcao == 'contrato' || !$stOpcao                   );

$obRdoCgmContrato = new Radio;
$obRdoCgmContrato->setName                      ( "stOpcao"                                             );
$obRdoCgmContrato->setTitle                     ( "Selecione a opção para a consulta."                  );
$obRdoCgmContrato->setRotulo                    ( "Opções"                                              );
$obRdoCgmContrato->setLabel                     ( "CGM/Matrícula"                                       );
$obRdoCgmContrato->setValue                     ( "cgm_contrato"                                        );
$obRdoCgmContrato->obEvento->setOnChange        ( "buscaValor('habilitaSpan');"                         );
$obRdoCgmContrato->setChecked                   ( $stOpcao == 'cgm_contrato'                            );

$obRdoEvento = new Radio;
$obRdoEvento->setName                           ( "stOpcao"                                             );
$obRdoEvento->setTitle                          ( "Selecione a opção para a consulta."                  );
$obRdoEvento->setRotulo                         ( "Opções"                                              );
$obRdoEvento->setLabel                          ( "Evento"                                              );
$obRdoEvento->setValue                          ( "evento"                                              );
$obRdoEvento->obEvento->setOnChange             ( "buscaValor('habilitaSpan');"                         );
$obRdoEvento->setChecked                        ( $stOpcao == 'evento'                                  );

$obChkFiltrar = new Checkbox;
$obChkFiltrar->setName                          ( "boFiltrarFolhaComplementar"                          );
$obChkFiltrar->setTitle                         ( "Selecione o tipo de cálculo."                        );
$obChkFiltrar->setValue                         ( true                                                  );
$obChkFiltrar->setRotulo                        ( "Filtrar por Folha Complementar"                      );
$obChkFiltrar->obEvento->setOnChange            ( "buscaValor('habilitaSpan2');"                        );

$obIFiltroCompetencia = new IFiltroCompetencia(true, "", true);
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange( "buscaValor('habilitaSpan2');"                  );
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange( "buscaValor('habilitaSpan2');"                  );

//Define objeto SPAN
$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spnSpan1"                                            );

$obSpnSpan3 = new Span;
$obSpnSpan3->setId                              ( "spnSpan3"                                            );

$obSpnSpan5 = new Span;
$obSpnSpan5->setId                              ( "spnSpan5"                                            );

$obSpnBotaoImprimir = new Span;
$obSpnBotaoImprimir->setId                      ( "spnSpanBotaoImprimir"                                            );

$obSpnSpanDadosInformativos = new Span;
$obSpnSpanDadosInformativos->setId              ( "spnSpanDadosInformativos"                            );

$obSpnDesdobramento = new Span;
$obSpnDesdobramento->setId                      ( "spnDesdobramento"                                    );

$obBtnVisualizar = new Button;
$obBtnVisualizar->setName                       ( "btnVisualizar"                                       );
$obBtnVisualizar->setValue                      ( "Visualizar"                                          );
$obBtnVisualizar->setTipo                       ( "button"                                              );
$obBtnVisualizar->obEvento->setOnClick          ( "buscaValor('visualizar');"                           );

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                              );
$obBtnLimpar->setTipo                           ( "button"                                              );
$obBtnLimpar->obEvento->setOnClick              ( "buscaValor('limpar');"                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                          ( $pgProc             );
$obForm->setTarget                          ( "oculto"            );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addHidden                        ( $obTextoComplementar                                  );
$obFormulario->addHidden                        ( $obHdnTextoComplementar                               );
$obFormulario->addHidden                        ( $obHdnTipoCalculo                                     );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo                        ( "Parâmetros para a Consulta"                          );
$obFormulario->agrupaComponentes                ( array($obRdoContrato,$obRdoCgmContrato,$obRdoEvento)  );
$obFormulario->addSpan                          ( $obSpnSpan1                                           );
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                         );
$obFormulario->addComponente                    ( $obChkFiltrar                                         );
$obFormulario->addSpan                          ( $obSpnSpan3                                           );
$obFormulario->agrupaComponentes                ( array($obBtnVisualizar,$obBtnLimpar)                  );
$obFormulario->addSpan                          ( $obSpnSpanDadosInformativos                           );
$obFormulario->addSpan                          ( $obSpnDesdobramento                                   );
$obFormulario->addSpan                          ( $obSpnSpan5                                           );
$obFormulario->addSpan                          ( $obSpnBotaoImprimir                                           );

$obFormulario->show();

// processarFiltro(true);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
