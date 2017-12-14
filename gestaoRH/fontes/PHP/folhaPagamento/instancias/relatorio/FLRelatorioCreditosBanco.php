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
    * Filtro de Relatório de Créditos por Banco
    * Data de Criação: 26/08/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30547 $
    $Name$
    $Author: rgarbin $
    $Date: 2008-02-12 11:57:24 -0200 (Ter, 12 Fev 2008) $

    * Casos de uso: uc-04.05.52
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                  	);
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploBanco.class.php"                                	);
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploAgencia.class.php"                              	);
include_once ( CAM_GRH_FOL_NEGOCIO    ."RFolhaPagamentoFolhaSituacao.class.php"                        	);
include_once ( CAM_GRH_FOL_NEGOCIO    ."RFolhaPagamentoPeriodoMovimentacao.class.php"                  	);
include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLocal.class.php'                                	);
include_once ( CAM_GRH_PES_COMPONENTES.'ISelectMultiploLotacao.class.php'                              	);
include_once ( CAM_GRH_FOL_MAPEAMENTO .'TFolhaPagamentoTipoFolha.class.php'                            	);
include_once ( CAM_GRH_FOL_MAPEAMENTO .'TFolhaPagamentoComplementar.class.php'                         	);
include_once ( CAM_GT_MON_COMPONENTES ."IMontaAgencia.class.php"                                     	);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"									 	);

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioCreditosBanco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma."Filtro.php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction   		( $pgProc  );
$obForm->setTarget   		( "telaPrincipal" );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName              ( "stCtrl"                                                  );
$obHdnCtrl->setValue             ( $stCtrl                                                   );

/// filtro de competencia
$obIFiltroCompetencia = new IFiltroCompetencia();
$obIFiltroCompetencia->obCmbMes->obEvento->setOnChange("montaParametrosGET('gerarSpanComplementar','inCodMes,inAno');");
$obIFiltroCompetencia->obTxtAno->obEvento->setOnChange("montaParametrosGET('gerarSpanComplementar','inCodMes,inAno');");

$obIFiltroTipoFolha = new IFiltroTipoFolha();
$obIFiltroTipoFolha->setValorPadrao("1");

$obISelectMultiploLocal   = new ISelectMultiploLocal;
$obISelectMultiploLotacao = new ISelectMultiploLotacao;

$obTipoFolha = new TFolhaPagamentoTipoFolha;
$obTipoFolha->recuperaTodos ( $rsTipoFolha );

$obSelBanco   = new ISelectMultiploBanco;
$obSelAgencia = new ISelectMultiploAgencia;

$obChkAtivos = new Radio();
$obChkAtivos->setName("stSituacao");
$obChkAtivos->setRotulo("Cadastro");
$obChkAtivos->setLabel("Ativos");
$obChkAtivos->setValue("ativos");
$obChkAtivos->setNull(false);
$obChkAtivos->setChecked(true);
$obChkAtivos->setTitle("Selecione o tipo de cadastro para emissï¿½o do comprovante de rendimentos");

$obChkAposentados = new Radio();
$obChkAposentados->setName("stSituacao");
$obChkAposentados->setLabel("Aposentados");
$obChkAposentados->setValue("aposentados");

$obChkPensionistas = new Radio();
$obChkPensionistas->setName("stSituacao");
$obChkPensionistas->setLabel("Pensionistas");
$obChkPensionistas->setValue("pensionistas");

$obChkRescindidos = new Radio();
$obChkRescindidos->setName("stSituacao");
$obChkRescindidos->setLabel("Rescindidos");
$obChkRescindidos->setValue("rescindidos");

$obChkTodos = new Radio();
$obChkTodos->setName("stSituacao");
$obChkTodos->setLabel("Todos");
$obChkTodos->setValue("todos");

$arChkCadastro = array($obChkAtivos,$obChkRescindidos,$obChkAposentados,$obChkPensionistas,$obChkTodos);

//agrupamento
$obChkAgruparBanco = new CheckBox;
$obChkAgruparBanco->setName		  				( "boTotalBanco"							  						);
$obChkAgruparBanco->setRotulo	  				( "Banco" 								  	  						);
$obChkAgruparBanco->setTitle	  				( "Selecione os dados a serem agrupados." 	  						);
$obChkAgruparBanco->setLabel					( "Agrupar"															);
$obChkAgruparBanco->obEvento->setOnClick		( "habilitaCheck('boQuebraBanco')"									);

$obChkAgrupaLotacao = new CheckBox;
$obChkAgrupaLotacao->setName         			( "boTotalLotacao"                            						);
$obChkAgrupaLotacao->setRotulo       			( "Lotação"                     									);
$obChkAgrupaLotacao->setTitle        			( "Selecione os dados a serem agrupados."      						);
$obChkAgrupaLotacao->setlabel        			( 'Agrupar'                                   						);
$obChkAgrupaLotacao->obEvento->setOnClick		( "habilitaCheck('boQuebraLotacao')"								);

$obChkAgrupaLocal = new CheckBox;
$obChkAgrupaLocal->setName         				( "boTotalLocal"                              						);
$obChkAgrupaLocal->setRotulo       				( "Local"                     				  						);
$obChkAgrupaLocal->setTitle       				( "Selecione os dados a serem agrupados."      						);
$obChkAgrupaLocal->setlabel        				( 'Agrupar'                                   						);
$obChkAgrupaLocal->obEvento->setOnClick			( "habilitaCheck('boQuebraLocal')" 		  							);

$obChkAgrupaAgencia = new CheckBox;
$obChkAgrupaAgencia->setName         				( "boTotalAgencia"                              				);
$obChkAgrupaAgencia->setRotulo       				( "Agência"                     				  				);
$obChkAgrupaAgencia->setTitle       				( "Selecione os dados a serem agrupados."      					);
$obChkAgrupaAgencia->setlabel        				( 'Agrupar'                                   					);
$obChkAgrupaAgencia->obEvento->setOnClick			( "habilitaCheck('boQuebraAgencia')" 		  					);

//quebras de páginas
$obChkQuebraBanco = new CheckBox;
$obChkQuebraBanco->setName 		  				( "boQuebraBanco"  													);
$obChkQuebraBanco->setRotulo	  				( "Quebrar página" 													);
$obChkQuebraBanco->setTitle		  				( "Quebrar página" 													);
$obChkQuebraBanco->setlabel		  				( "Quebrar página" 													);
$obChkQuebraBanco->setId						("boQuebraBanco"													);
$obChkQuebraBanco->setDisabled					( true 																);

$obChkQuebraLotacao = new CheckBox;
$obChkQuebraLotacao->setName 		  			( "boQuebraLotacao" 												);
$obChkQuebraLotacao->setRotulo		  			( "Quebrar página" 													);
$obChkQuebraLotacao->setTitle		  			( "Quebrar página"													);
$obChkQuebraLotacao->setlabel		  			( "Quebrar página"													);
$obChkQuebraLotacao->setId						("boQuebraLotacao"													);
$obChkQuebraLotacao->setDisabled				( true 																);

$obChkQuebraLocal = new CheckBox;
$obChkQuebraLocal->setName 		  				( "boQuebraLocal" 													);
$obChkQuebraLocal->setRotulo	  				( "Quebrar página" 													);
$obChkQuebraLocal->setTitle		  				( "Quebrar página"													);
$obChkQuebraLocal->setlabel		  				( "Quebrar página"													);
$obChkQuebraLocal->setId						("boQuebraLocal"													);
$obChkQuebraLocal->setDisabled					( true																);

$obChkQuebraAgencia = new CheckBox;
$obChkQuebraAgencia->setName 		  			( "boQuebraAgencia" 												);
$obChkQuebraAgencia->setRotulo	  				( "Quebrar página" 													);
$obChkQuebraAgencia->setTitle		  			( "Quebrar página"													);
$obChkQuebraAgencia->setlabel		  			( "Quebrar página"													);
$obChkQuebraAgencia->setId						("boQuebraAgencia"													);
$obChkQuebraAgencia->setDisabled				( true																);

$obBtnOk = new Ok();

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick    			( "montaParametrosGET('limparForm');"         						);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  		( $obForm                                                           );
$obFormulario->addHidden                		( $obHdnCtrl                                                        );
$obFormulario->addTitulo                		( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addTitulo                		( 'Seleção do Filtro'                                               );
$obFormulario->agrupaComponentes                ( $arChkCadastro                                                    );
$obIFiltroCompetencia->geraFormulario   		( $obFormulario                                                     );
$obIFiltroTipoFolha->geraFormulario				( $obFormulario														);
$obFormulario->addComponente            		( $obISelectMultiploLotacao                                         );
$obFormulario->addComponente            		( $obISelectMultiploLocal                                           );
$obFormulario->addTitulo                		( 'Informações Bancárias'                                           );
$obFormulario->addComponente            		( $obSelBanco                                                       );
$obFormulario->addComponente            		( $obSelAgencia                                                     );
$obFormulario->addTitulo                		( 'Totalizadores'                                                   );
$obFormulario->agrupaComponentes   				( array($obChkAgrupaLotacao, $obChkQuebraLotacao)					);
$obFormulario->agrupaComponentes				( array($obChkAgrupaLocal, $obChkQuebraLocal)						);
$obFormulario->agrupaComponentes				( array($obChkAgrupaAgencia, $obChkQuebraAgencia)					);
$obFormulario->defineBarra              		( array($obBtnOk,$obBtnLimpar)                                      );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
