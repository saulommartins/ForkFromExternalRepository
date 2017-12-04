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
    * Página de Formulário do Consultar Registro de Evento de Férias
    * Data de Criação: 17/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: souzadl $
    $Date: 2006-10-26 09:00:46 -0300 (Qui, 26 Out 2006) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarRegistroEventoRescisao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php?".Sessao::getId();
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "executaFuncaoAjax('processarForm','&inRegistro=".$_REQUEST['inContrato']."&inCodMes=".$_REQUEST['inCodMes']."&inAno=".$_REQUEST['inAno']."');";

$stAcao      = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);
$arMeses = array(1 =>"Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
$stCGM = ( $_REQUEST['hdnCGM'] != "" ) ? $_REQUEST['hdnCGM'] : $_REQUEST['inNumCGM'] ."-".$_REQUEST['inCampoInner'];
if ($stCGM == "-") {
    $obRPessoalContrato = new RPessoalContrato;
    $obRPessoalContrato->listarCgmDoRegistro($rsContrato,$_REQUEST['inContrato']);
    $stCGM = $rsContrato->getCampo('numcgm')."-".$rsContrato->getCampo('nom_cgm');
}

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php"                                 );
$obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao;
$stFiltro = " AND  registro = ".$_GET['inContrato'];
$obTFolhaPagamentoRegistroEventoRescisao->recuperaContratosDoFiltro($rsLista,$stFiltro);

if ( $rsLista->getNumLinhas() < 0 ) {
    $obTFolhaPagamentoRegistroEventoRescisao->setDado("registro_pensionista",$_GET['inContrato']);
    $obTFolhaPagamentoRegistroEventoRescisao->recuperaRescisaoContratoPensionista($rsLista,$stFiltro);
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obLblCompetencia = new Label;
$obLblCompetencia->setName                      ( "stCompetencia"                                                       );
$obLblCompetencia->setRotulo                    ( "Competência"                                                         );
$obLblCompetencia->setValue                     ( $arMeses[$_REQUEST['inCodMes']]."/".$_REQUEST['inAno']                );

$obLblContrato = new Label;
$obLblContrato->setName                         ( "stContrato"                                                          );
$obLblContrato->setRotulo                       ( "Matrícula"                                                           );
$obLblContrato->setValue                        ( $_REQUEST['inContrato']                                               );

$obLblCGM = new Label;
$obLblCGM->setName                              ( "stCGM"                                                               );
$obLblCGM->setRotulo                            ( "CGM"                                                                 );
$obLblCGM->setValue                             ( $stCGM                                                                );

$obLblDataRescisao = new Label;
$obLblDataRescisao->setName                     ( "stDataRescisao"                                                      );
$obLblDataRescisao->setRotulo                   ( "Data Rescisão"                                                       );
$obLblDataRescisao->setValue                    ( $rsLista->getCampo("dt_rescisao")                                     );

$obLblCausa = new Label;
$obLblCausa->setName                            ( "stCausa"                                                             );
$obLblCausa->setRotulo                          ( "Causa"                                                               );
$obLblCausa->setValue                           ( $rsLista->getCampo("num_causa")."-".$rsLista->getCampo("descricao_causa"));

$obSpnEventosCadastrados = new Span;
$obSpnEventosCadastrados->setId                 ( "spnEventosCadastrados"                                               );

$obSpnEventosBase = new Span;
$obSpnEventosBase->setId                        ( "spnEventosBase"                                                      );

$obBtnFechar = new Button;
$obBtnFechar->setName                           ( "btnFechar" );
$obBtnFechar->setValue                          ( "Fechar"    );
$obBtnFechar->setTipo                           ( "button"    );
$obBtnFechar->obEvento->setOnClick              ( "window.parent.window.close();"  );

Sessao::write('stTipoFolha',"Rescisao");
$stLink    = CAM_FW_POPUPS."relatorio/OCRelatorio.php?".Sessao::getId()."&stCaminho=".CAM_GRH_FOL_INSTANCIAS."relatorio/OCRelatorioEventoPorContrato.php";
$stFiltros = "&stOpcaoFiltro=contrato&inContrato=".$_GET['inContrato']."&inCodMes=".$_GET['inCodMes']."&inAno=".$_GET['inAno'];

$obBtnImprimir = new Button;
$obBtnImprimir->setName                         ( "btnImprimir"      );
$obBtnImprimir->setValue                        ( "Imprimir"         );
$obBtnImprimir->setTipo                         ( "button"           );
$obBtnImprimir->obEvento->setOnClick            ( "window.parent.window.frames['oculto'].location='".$stLink.$stFiltros."';" );

$botoesForm = array ( $obBtnFechar , $obBtnImprimir );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Dados da Matrícula do Servidor"                                       );
$obFormulario->addComponente                    ( $obLblCompetencia                                                     );
$obFormulario->addComponente                    ( $obLblContrato                                                        );
$obFormulario->addComponente                    ( $obLblCGM                                                             );
$obFormulario->addComponente                    ( $obLblDataRescisao );
$obFormulario->addComponente                    ( $obLblCausa );
$obFormulario->addTitulo                        ( "Dados dos Eventos"                                                   );
$obFormulario->addSpan                          ( $obSpnEventosCadastrados                                              );
$obFormulario->addSpan                          ( $obSpnEventosBase                                                     );
$obFormulario->defineBarra ( $botoesForm );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
