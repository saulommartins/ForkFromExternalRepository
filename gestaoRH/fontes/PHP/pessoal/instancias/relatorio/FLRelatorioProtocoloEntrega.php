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
    * Página de Filtro do Relatório de Protocolo de Entrega
    * Data de Criação : 04/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30547 $
    $Name$
    $Autor: $
    $Date: 2007-10-30 12:16:35 -0200 (Ter, 30 Out 2007) $

    * Casos de uso: uc-04.04.47
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalPensao.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioProtocoloEntrega";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRPessoalPensao = new RPessoalPensao();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "telaPrincipal"                                  		);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                               );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();

$obChkSequencia = new CheckBox();
$obChkSequencia->setRotulo("Imprimir sequência?");
$obChkSequencia->setName("boImprimirSequencia");
$obChkSequencia->SetId("boImprimirSequencia");
$obChkSequencia->setValue(true);
$obChkSequencia->setTitle("Selecione a opção para escolher a sequencia inicial.");
$obChkSequencia->setLabel("");
$obChkSequencia->obEvento->setOnChange             ( "montaParametrosGET('imprimirSequencia','boImprimirSequencia');" );

$obRPessoalServidor = new RPessoalServidor();
$obRPessoalServidor->addContratoServidor();
$obRPessoalServidor->roUltimoContratoServidor->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

$obCmbAtributo = new Select();
$obCmbAtributo->setRotulo("Atributo Dinâmico");
$obCmbAtributo->setName("inCodAtributo");
$obCmbAtributo->setTitle("Selecione o atributo dinâmico para filtro.");
$obCmbAtributo->setNull(true);
$obCmbAtributo->setCampoDesc("nom_atributo");
$obCmbAtributo->setCampoId("cod_atributo");
$obCmbAtributo->addOption("","Selecione");
$obCmbAtributo->preencheCombo($rsAtributos);
$obCmbAtributo->obEvento->setOnChange("montaParametrosGET('gerarSpanAtributosDinamicos','inCodAtributo');");

$obSpnAtributo = new Span();
$obSpnAtributo->setId							( "spnAtributo" 										);
$obSpnAtributo->setValue                        ( ""                                                    );

$obChkComplementoTitulo = new CheckBox;
$obChkComplementoTitulo->setRotulo  ( "Complementar título?"     										  );
$obChkComplementoTitulo->setTitle	( "Marque para digitar uma complementação do título do relatório."	  );
$obChkComplementoTitulo->setName    ( "boComplementoTitulo"												  );
$obChkComplementoTitulo->setId   	( "boComplementoTitulo"												  );
$obChkComplementoTitulo->setLabel   ( "" 											  					  );
$obChkComplementoTitulo->obEvento->setOnClick ( "montaParametrosGET('gerarSpanComplementoTitulo', 'boComplementoTitulo');" );

$obSpanSequencia = new Span;
$obSpanSequencia->setId                         ( "spnSequencia"                                        );
$obSpanSequencia->setValue                      ( ""                                                    );

$obSpanComplementoTitulo = new Span;
$obSpanComplementoTitulo->setId					( "spnComplementoTitulo"								);
$obSpanComplementoTitulo->setValue				( ""													);

$obHdnFiltroDinamico = new HiddenEval;
$obHdnFiltroDinamico->setName                   ( "hdnFiltroDinamico"   		                        );
$obHdnFiltroDinamico->setValue                  ( $stEval 												);
$obHdnFiltroDinamico->setId                     ( "hdnFiltroDinamico" 									);

$obBtnLimpar = new Limpar();

$obBtnOk = new OK;
$obBtnOk->obEvento->setOnClick                  ( "montaParametrosGET('OK','',true);"                   );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                              			    );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addTitulo                        ( "Parâmetros para a Consulta"                					    );
$obFormulario->addHidden                        ( $obHdnCtrl                                            			);
$obFormulario->addHidden                        ( $obHdnFiltroDinamico,true                                    	 	);
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addComponente                    ( $obChkSequencia													);
$obFormulario->addSpan                          ( $obSpanSequencia													);
$obFormulario->addTitulo						( "Atributo Dinâmico"												);
$obFormulario->addComponente					( $obCmbAtributo													);
$obFormulario->addSpan                          ( $obSpnAtributo                                                    );
$obFormulario->addComponente					( $obChkComplementoTitulo											);
$obFormulario->addSpan							( $obSpanComplementoTitulo 											);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
