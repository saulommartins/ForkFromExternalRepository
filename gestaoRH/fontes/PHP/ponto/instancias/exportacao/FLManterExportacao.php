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
 * Fornulário de Filtro para Exportação de Pontos
 * Data de Criação   : 22/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

//Define o nome dos arquivos PHP
$stPrograma = "ManterExportacao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );
$obForm->setTarget("oculto");

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoExportacao.class.php");

$stOrdem = " descricao ";
$obTPontoFormatoExportacao = new TPontoFormatoExportacao();
$obTPontoFormatoExportacao->recuperaTodos($rsFormatoExportacao, $stFiltro="", $stOrdem);

$obCmbFormatoExportacao = new Select();
$obCmbFormatoExportacao->setTitle      ( "Selecione a configuração do formato de exportação."  );
$obCmbFormatoExportacao->setRotulo     ( "Formato de Exportação"                                                    );
$obCmbFormatoExportacao->setName       ( "inCodFormato"                                                             );
$obCmbFormatoExportacao->setId         ( "inCodFormato"                                                             );
$obCmbFormatoExportacao->addOption     ( "", "Selecione"                                                            );
$obCmbFormatoExportacao->setCampoDesc  ( "descricao"                                                                );
$obCmbFormatoExportacao->setCampoId    ( "cod_formato"                                                              );
$obCmbFormatoExportacao->setStyle      ( "width: 200px" );
$obCmbFormatoExportacao->preencheCombo ( $rsFormatoExportacao                                                       );
$obCmbFormatoExportacao->setValue      ( $inCodCampo                                                                );
$obCmbFormatoExportacao->setNull       ( false                                                                      );

$dtInicial = "01".date("/m/Y");
$dtFinal = strftime ("%d",mktime (0,0,0,date("m")+1,0,date("Y"))).date("/m/Y");

$obPeriodoExportar = new Periodo();
$obPeriodoExportar->setRotulo ( "Período a Exportar"                                                                         );
$obPeriodoExportar->setTitle  ( "Informa o período dos lançamentos do relógio ponto que deverão ser gerados no arquivo para importação na folha de pagamento.");
$obPeriodoExportar->obDataInicial->setValue($dtInicial);
$obPeriodoExportar->obDataFinal->setValue($dtFinal);
$obPeriodoExportar->setNull   ( false );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php")    ;
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setFiltroPadrao("geral");

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("BloqueiaFrames(true,false);parent.frames[2].Salvar();");

$obBtnLimpar = new Limpar();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addComponente($obCmbFormatoExportacao);
$obFormulario->addComponente($obPeriodoExportar);
$obFormulario->addTitulo("Seleção de Filtro");
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
