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
 * Fornulário de Filtro para Relatório Grade de Horários
 * Data de Criação   : 17/10/2008

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
$stPrograma = "GradeHorarios";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$jsOnLoad = "executaFuncaoAjax('onLoad');";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );

$obRdoGradeHorario = new Radio();
$obRdoGradeHorario->setRotulo("Emitir");
$obRdoGradeHorario->setName("stEmitir");
$obRdoGradeHorario->setId("stEmitir");
$obRdoGradeHorario->setLabel("Grade de Horário");
$obRdoGradeHorario->setValue("G");
$obRdoGradeHorario->setChecked(true);
$obRdoGradeHorario->setTitle("Selecione tipo de filtro para emissão.");
$obRdoGradeHorario->obEvento->setOnChange("montaParametrosGET('gerarSpanEmitir','stEmitir');");

$obRdoServidoresGrade = new Radio();
$obRdoServidoresGrade->setRotulo("Emitir");
$obRdoServidoresGrade->setName("stEmitir");
$obRdoServidoresGrade->setId("stEmitir");
$obRdoServidoresGrade->setLabel("Servidores / Grade");
$obRdoServidoresGrade->setValue("S");
$obRdoServidoresGrade->setTitle("Selecione tipo de filtro para emissão.");
$obRdoServidoresGrade->obEvento->setOnChange("montaParametrosGET('gerarSpanEmitir','stEmitir');");

$obSpnEmitir = new Span();
$obSpnEmitir->setId("spnEmitir");

$obHdnEmitir = new HiddenEval();
$obHdnEmitir->setName("hdnEmitir");
$obHdnEmitir->setId("hdnEmitir");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->agrupaComponentes(array($obRdoGradeHorario,$obRdoServidoresGrade));
$obFormulario->addSpan($obSpnEmitir);
$obFormulario->addHidden($obHdnEmitir,true);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
