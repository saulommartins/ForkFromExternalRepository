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
    * Arquivo de Formulário para exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: FMExportarCAGED.php 30829 2008-07-07 19:59:54Z alex $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$arArquivoDownload = Sessao::read('arArquivosDownload');

$stPrograma = "ExportarCAGED";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgProcRel  = "PRRelatorioCAGED.php";
$pgDown     = "DW".$stPrograma.".php?arq=".$arArquivoDownload[0]['stLink']."&label=".$arArquivoDownload[0]['stNomeArquivo'];
$pgJS       = "JS".$stPrograma.".js";
include_once($pgJS);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

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
$obForm->setAction                              ( $pgProcRel                                                            );
$obForm->setTarget                              ( "oculto"                                                              );

$stCompetencia  = ( $_GET['inCodMes'] >= 10 )? $_GET['inCodMes'] : "0".$_GET['inCodMes'];
$stCompetencia .= "/".$_GET['inAno'];
Sessao::write("stCompetencia", $stCompetencia);

$stTipoArquivo = ($_GET["stTipoEmissao"] == "movimento") ? "Movimentação Mensal" : "Acerto";
Sessao::write("stTipoArquivo", $stTipoArquivo);

$stTabela1 .= "<center>";
$stTabela1 .= "<table border=0 width=80%>";
$stTabela1 .= "<tr><td align=center colspan=4 width=100%><font size=3><b>Totais Arquivo CAGED</b></font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Competência:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".$stCompetencia."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Tipo de Arquivo:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".$stTipoArquivo."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Total de Servidores no Primeiro Dia:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTotalPrimeiroDia")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Total de Servidores no Último Dia:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".(Sessao::read("inTotalPrimeiroDia")+Sessao::read("inTotalMovimentacoes")-Sessao::read("inTotalDesligamentosMes"))."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1><b>Total de Admissões no Mês:</b></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1><b>".Sessao::read("inTotalMovimentacoes")."</b></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1><b>Total de Desligamentos no Mês:</b></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1><b>".Sessao::read("inTotalDesligamentosMes")."</b></font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Contrato de Prazo Determinado:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inContratoPrazoDeterminado")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Aposentado:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inAposentado")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Primeiro Emprego:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inPrimeiroEmprego")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Dispensa a Pedido (Espontâneo):</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inDispensaPedido")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Reemprego:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inReemprego")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Dispensa por Justa Causa:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inDispensaJustaCausa")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Reintegração:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inReintegracao")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Dispensa sem Justa Causa:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inDispensaSemJustaCausa")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1>Transferência de Entrada:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTranferenciaEntrada")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Morte:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inMorte")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Término de Contrato:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTerminoContrato")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Transferência de Saída:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTranferenciaSaida")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>Fim do Contrato por Prazo Determinado:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTerminoContrato")."</font></td></tr>";

$stTabela1 .= "<tr><td align=left width=100% colspan=4><font size=-1><b>Atenção: O usuário só deve enviar um e não mais que um arquivo para o MTE, para isso pode utilizar a unificação de arquivos (mensal e acerto) presente no programa do CAGED (ACI Windows).</b></font></td></tr>";

$stTabela1 .= "</table>";
$stTabela1 .= "</center>";

$spnResumo = new Span();
$spnResumo->setValue($stTabela1);

$obBtnImprimir = new Ok();
$obBtnImprimir->setValue("Imprimir");

$obBtnDownload = new Button();
$obBtnDownload->setValue("Download");
$obBtnDownload->obEvento->setOnClick("download()");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addSpan($spnResumo);
$obFormulario->defineBarra(array($obBtnImprimir,$obBtnDownload),"","");
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
