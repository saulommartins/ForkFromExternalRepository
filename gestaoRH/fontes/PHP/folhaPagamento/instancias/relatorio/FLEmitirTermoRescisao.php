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
    * Página de Filtro do Emitir Termo Rescisao
    * Data de Criação: 01/11/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-12-13 10:39:58 -0200 (Qui, 13 Dez 2007) $

    * Casos de uso: uc-04.05.39
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);

//Define o nome dos arquivos PHP
$stPrograma = "EmitirTermoRescisao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                          ( "stCaminho"                                               			);
$obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgProc  							);

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRescisao();

$obRadioOrdenacaoAlfabetica = new Radio();
$obRadioOrdenacaoAlfabetica->setRotulo("Ordenação");
$obRadioOrdenacaoAlfabetica->setName("stOrdenacao");
$obRadioOrdenacaoAlfabetica->setValue("alfabetica");
$obRadioOrdenacaoAlfabetica->setLabel("Alfabética");
$obRadioOrdenacaoAlfabetica->setTitle("Selecione o tipo de ordenação para a emissão dos termos de rescisão.");
$obRadioOrdenacaoAlfabetica->setChecked(true);

$obRadioOrdenacaoNumerica = new Radio();
$obRadioOrdenacaoNumerica->setRotulo("Ordenação");
$obRadioOrdenacaoNumerica->setName("stOrdenacao");
$obRadioOrdenacaoNumerica->setValue("numerica");
$obRadioOrdenacaoNumerica->setLabel("Numérica");
$obRadioOrdenacaoNumerica->setTitle("Selecione o tipo de ordenação para a emissão dos termos de rescisão.");

$obBtnOk = new Ok();

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limparFormulario', '', true);" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget("oculto");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnCaminho                                             			);
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obIFiltroCompetencia->geraFormulario			( $obFormulario															);
$obIFiltroComponentes->geraFormulario			( $obFormulario															);
$obFormulario->agrupaComponentes				( array($obRadioOrdenacaoAlfabetica,$obRadioOrdenacaoNumerica)			);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
