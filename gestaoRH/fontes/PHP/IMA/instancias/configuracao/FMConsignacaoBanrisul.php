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
    * Página de Formulário do IMA Configuração Consignação Banrisul
    * Data de Criação: 09/06/2008

    * @author Alex Cardoso

    * Casos de uso: uc-04.08.27

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_COMPONENTES."ISelectMultiploEvento.class.php"                                );

//Define o nome dos arquivos PHP
$stPrograma = "ConsignacaoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "executaFuncaoAjax('preencherDados');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

//Definicao dos componentes
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obISelectMultiploEventoRemuneracao = new ISelectMultiploEvento();
$obISelectMultiploEventoRemuneracao->setName("inCodEventoRemuneracao");
$obISelectMultiploEventoRemuneracao->SetNomeLista1("inCodEventoDisponiveisRemuneracao");
$obISelectMultiploEventoRemuneracao->SetNomeLista2("inCodEventoSelecionadosRemuneracao");
$obISelectMultiploEventoRemuneracao->setRotulo("Eventos Remuneração");
$obISelectMultiploEventoRemuneracao->setNull(false);
$obISelectMultiploEventoRemuneracao->setProventos();
$obISelectMultiploEventoRemuneracao->setBases();
$obISelectMultiploEventoRemuneracao->montarEventosDisponiveis();
$obISelectMultiploEventoRemuneracao->setTitle("Selecione os eventos que compõe a base da remuneração (proventos) para fins de consignação.");

$obISelectMultiploEventoLiquido = new ISelectMultiploEvento();
$obISelectMultiploEventoLiquido->setName("inCodEventoLiquido");
$obISelectMultiploEventoLiquido->SetNomeLista1("inCodEventoDisponiveisLiquido");
$obISelectMultiploEventoLiquido->SetNomeLista2("inCodEventoSelecionadosLiquido");
$obISelectMultiploEventoLiquido->setRotulo("Eventos Líquido");
$obISelectMultiploEventoLiquido->setNull(false);
$obISelectMultiploEventoLiquido->setDescontos();
$obISelectMultiploEventoLiquido->montarEventosDisponiveis();
$obISelectMultiploEventoLiquido->setTitle("Selecione os eventos que compõe a base do líquido (descontos) que serão considerados no cálculo para fins de consignação.");

/****************************************
* Monta FORMULARIO
****************************************/
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden             ( $obHdnAcao                            											);
$obFormulario->addHidden             ( $obHdnCtrl                                                                       );
$obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" 				);
$obFormulario->addTitulo             ( "Consignação Banrisul" 											                );
$obFormulario->addTitulo             ( "Base da Remuneração"         											        );
$obFormulario->addComponente         ( $obISelectMultiploEventoRemuneracao 											    );
$obFormulario->addTitulo             ( "Base do Líquido"         											            );
$obFormulario->addComponente         ( $obISelectMultiploEventoLiquido 		      									    );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
