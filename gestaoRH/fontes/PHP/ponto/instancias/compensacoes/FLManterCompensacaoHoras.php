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
/*
    * Formulário para cadastro de compensações de horas
    * Data de Criação   : 03/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "ManterCompensacaoHoras";
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
$obForm->setAction      ( $pgList );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setGeral(false);

$obPeriodoFalta = new Periodo();
$obPeriodoFalta->setRotulo("Dia da Falta");
$obPeriodoFalta->obDataInicial->setName("dtFaltaInicial");
$obPeriodoFalta->obDataInicial->setId("dtFaltaInicial");
$obPeriodoFalta->obDataFinal->setName("dtFaltaFinal");
$obPeriodoFalta->obDataFinal->setId("dtFaltaFinal");
$obPeriodoFalta->setTitle("Informe o dia da falta para compensação.");

$obDtCompensacao = new Periodo();
$obDtCompensacao->setRotulo("Dia da Compensação");
$obDtCompensacao->obDataInicial->setName("dtCompensacaoInicial");
$obDtCompensacao->obDataInicial->setName("dtCompensacaoInicial");
$obDtCompensacao->obDataFinal->setName("dtCompensacaoFinal");
$obDtCompensacao->obDataFinal->setName("dtCompensacaoFinal");
$obDtCompensacao->setTitle("Informe o dia da compensação.");

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addForm								( $obForm 																);
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addTitulo("Dados da Compensação de Horas");
$obFormulario->addComponente($obPeriodoFalta);
$obFormulario->addComponente($obDtCompensacao);
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
