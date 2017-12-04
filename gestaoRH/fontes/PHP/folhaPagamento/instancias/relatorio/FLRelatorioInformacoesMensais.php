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
    * Página de Filtro do Relatório Informações Mensais
    * Data de Criação: 26/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: alex $
    $Date: 2008-03-10 14:10:50 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.05.58
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioInformacoesMensais";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once($pgJS);

Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
Sessao::write("arContratos",array());

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

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

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoAtributoServidor();
$obIFiltroComponentes->setFiltroPadrao("contrato");
$obIFiltroComponentes->getOnload($jsOnload);

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );
$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
$stFiltro = " AND (natureza = 'P' OR natureza = 'D') ";
$obTFolhaPagamentoEvento->recuperaEventos($rsEventos,$stFiltro);

$obCmbEvento = new SelectMultiplo();
$obCmbEvento->setName                           ( 'inCodEvento'                                             );
$obCmbEvento->setRotulo                         ( "Eventos"                                                 );
$obCmbEvento->setTitle                          ( "Selecione os eventos que pertencerão a grade de informações." );
$obCmbEvento->SetNomeLista1                     ( 'inCodEventoDisponiveis'                                  );
$obCmbEvento->setCampoId1                       ( '[cod_evento]'                                            );
$obCmbEvento->setCampoDesc1                     ( '[codigo]-[descricao]'                                    );
$obCmbEvento->setStyle1                         ( "width: 300px"                                            );
$obCmbEvento->SetRecord1                        ( $rsEventos                                                );
$obCmbEvento->SetNomeLista2                     ( 'inCodEventoSelecionados'                                 );
$obCmbEvento->setCampoId2                       ( '[cod_evento]'                                            );
$obCmbEvento->setCampoDesc2                     ( '[codigo]-[descricao]'                                    );
$obCmbEvento->setStyle2                         ( "width: 300px"                                            );
$obCmbEvento->SetRecord2                        ( new recordset                                             );
$obCmbEvento->obSelect1->setSize                ( 5                                                         );
$obCmbEvento->obSelect2->setSize                ( 5                                                         );
$stOnClick = "validaQuantidadeEventos();";
$obCmbEvento->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
$obCmbEvento->obSelect1->obEvento->setOnDblClick( $stOnClick );
$obCmbEvento->obSelect2->obEvento->setOnDblClick( $stOnClick );

$obSpnFiltroAgrupar = new Span();
$obSpnFiltroAgrupar->setId("spnFiltroAgrupar");

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "montaParametrosGET('limparFormulario', '', true);"                   );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "telaPrincipal"                                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obIFiltroComponentes->geraFormulario			( $obFormulario															);
$obFormulario->addTitulo("Seleção de Eventos para Grade de Relatório");
$obFormulario->addComponente($obCmbEvento);
$obFormulario->addSpan($obSpnFiltroAgrupar);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
