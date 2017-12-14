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
 * Fornulário para Exportação de Pontos
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
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$arArquivoDownload = Sessao::read('arArquivosDownload');
$stPrograma = "ManterExportacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PRRelatorio".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgDown = "DW".$stPrograma.".php?arq=".$arArquivoDownload[0]['stLink']."&label=".$arArquivoDownload[0]['stNomeArquivo'];
$pgJS   = "JS".$stPrograma.".js";
include_once($pgJS);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "telaPrincipal"                                  );

include_once(CAM_GRH_PON_MAPEAMENTO."TPontoFormatoExportacao.class.php");
$obTPontoFormatoExportacao = new TPontoFormatoExportacao();
$obTPontoFormatoExportacao->servidoresExportador($rsServidoresExportados);

$stTabela1 .= "<table border=0 width=100%>";
$stTabela1 .= "<tr><td align=center width=100%><font size=3><b>Quantidade de Servidores Exportados: ".$rsServidoresExportados->getCampo("contador_contratos")."</b></font></td></tr>";
$stTabela1 .= "</table>";

$obBtnImprimir = new Ok();
$obBtnImprimir->setValue("Imprimir");

$obBtnDownload = new Button();
$obBtnDownload->setValue("Download");
$obBtnDownload->obEvento->setOnClick("download()");

$spnResumo = new Span();
$spnResumo->setValue($stTabela1);

SistemaLegado::LiberaFrames();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addTitulo("Resultado da Exportação de Pontos");
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addSpan($spnResumo);
$obFormulario->defineBarra(array($obBtnImprimir,$obBtnDownload),"center","");
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
