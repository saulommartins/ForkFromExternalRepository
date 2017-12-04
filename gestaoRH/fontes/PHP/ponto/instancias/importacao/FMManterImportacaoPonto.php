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
    * Formulário
    * Data de Criação: 07/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.04

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );

SistemaLegado::LiberaFrames();
$stPrograma = "ManterImportacaoPonto";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgProcRel  = "PRRelatorioImportacaoPonto.php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao   = $_REQUEST["stAcao"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$inTotalServidoresImportados     = Sessao::read("inTotalServidoresImportados");
$inTotalServidoresImportadosErro = Sessao::read("inTotalServidoresImportadosErro");

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obForm = new Form;
$obForm->setAction ( $pgProcRel );
$obForm->setTarget ( "telaPrincipal" );

$stTabela .= "\n <center>";
$stTabela .= "\n    <table border=0 width=100%>";
$stTabela .= "\n       <tr>";
$stTabela .= "\n           <td align=center colspan=2 width=100%><font size=3><b>Quantidade de Servidores Importados: ".$inTotalServidoresImportados."</b></font></td>";
$stTabela .= "\n        </tr>";
$stTabela .= "\n        <tr>";
$stTabela .= "\n             <td align=center colspan=2 width=100%><font size=3><b>Quantidade de Linhas com Erros: ".$inTotalServidoresImportadosErro."</b></font></td>";
$stTabela .= "\n        </tr>";
$stTabela .= "\n    </table>";
$stTabela .= "\n </center>";

$spnResumo = new Span();
$spnResumo->setValue($stTabela);

$obBtnImprimir = new Ok();
$obBtnImprimir->setValue ("Relatório");
$obBtnImprimir->setStyle ("width:150px;");
$obBtnImprimir->setId    ("boImprimirRelatorio");
$obBtnImprimir->obEvento->setOnClick("jQuery('#stTipoRelatorio').val('relatorio'); Salvar();");

$obBtnImprimir2 = new Ok();
$obBtnImprimir2->setValue("Relatório de Erros");
$obBtnImprimir2->setStyle("width:190px;");
$obBtnImprimir2->setId   ("boImprimirRelatorioErros");
$obBtnImprimir2->obEvento->setOnClick("jQuery('#stTipoRelatorio').val('relatorioErros'); Salvar();");

$obHdnTipoRelatorio = new Hidden();
$obHdnTipoRelatorio->setName("stTipoRelatorio");
$obHdnTipoRelatorio->setId  ("stTipoRelatorio");

$obFormulario = new Formulario();
$obFormulario->addForm     ( $obForm                                                            );
$obFormulario->addTitulo   ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"   );
$obFormulario->addHidden   ( $obHdnAcao                                                         );
$obFormulario->addHidden   ( $obHdnCtrl                                                         );
$obFormulario->addSpan     ( $spnResumo                                                         );
$obFormulario->defineBarra (array($obBtnImprimir,$obBtnImprimir2), "center", ""                 );
$obFormulario->addHidden ($obHdnTipoRelatorio);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
