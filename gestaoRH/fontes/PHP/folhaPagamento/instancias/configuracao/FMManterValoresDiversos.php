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
    * Arquivo de Formulário
    * Data de Criação: 31/10/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.05.22

    $Id: FMManterValoresDiversos.php 61416 2015-01-15 13:21:21Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoValorDiversos.class.php");

$stPrograma = "ManterValoresDiversos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $_REQUEST["stAcao"]                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obBtnOk = new Ok;
$obBtnOk->setName									( "btnOk" 																);
$obBtnOk->setTitle									( "Clique para gravar as informações." 									);
//$obBtnOk->obEvento->setOnClick						( "montaParametrosGET('submeter', '', true);						   ");

$obBtnLimpar = new Limpar;
$obBtnLimpar->setName								( "btnLimpar" 															);
$obBtnLimpar->setTitle								( "Clique para limpar os dados dos campos." 							);
$obBtnLimpar->obEvento->setOnClick("");

$obIntCodigo = new Inteiro();
$obIntCodigo->setRotulo("Código");
$obIntCodigo->setTitle("Informe um código para identificação do valor.");
$obIntCodigo->setName("inCodigo");
$obIntCodigo->setNull(false);
$obIntCodigo->setValue($_GET["inCodigo"]);;
$obIntCodigo->setMaxLength(10);

$obTxtDescricao = new TextBox();
$obTxtDescricao->setRotulo("Descrição");
$obTxtDescricao->setTitle("Informe uma descrição para o valor.");
$obTxtDescricao->setName("stDescricao");
$obTxtDescricao->setNull(false);
$obTxtDescricao->setValue($_GET["stDescricao"]);
$obTxtDescricao->setMaxLength(60);
$obTxtDescricao->setSize(60);

$obNumValor = new Numerico();
$obNumValor->setRotulo("Valor");
$obNumValor->setTitle("Informe o valor do código do diverso.");
$obNumValor->setName("nuValor");
$obNumValor->setNull(false);
$obNumValor->setDecimais(4);
$obNumValor->setValue($_GET["nuValor"]);

$obTxtVigencia = new Data;
$obTxtVigencia->setName                              ( "dataVigencia"                                                );
$obTxtVigencia->setTitle                             ( "Informe a data da vigência."                               );
$obTxtVigencia->setNull                              ( false                                                       );
$obTxtVigencia->setRotulo                            ( "Vigência"                                                  );
$obTxtVigencia->setValue                             ( $_GET["dataVigencia"]                                                );

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addForm								( $obForm 																);
$obFormulario->addComponente($obIntCodigo);
$obFormulario->addComponente($obTxtDescricao);
$obFormulario->addComponente($obNumValor);
$obFormulario->addComponente($obTxtVigencia);
$obFormulario->defineBarra  					    ( array( $obBtnOk, $obBtnLimpar ) 										);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
