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
    * Página de Formulário do Exportação TCMBA
    * Data de Criação: 18/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-29 13:03:47 -0300 (Qua, 29 Ago 2007) $

    * Casos de uso: uc-04.08.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$arArquivoDownload = Sessao::read('arArquivosDownload');

$stPrograma = "ExportarTCMBA";
$pgFilt     = "FL".$stPrograma.".php";
//$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PRRelatorioTCMBA.php";
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
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "telaPrincipal"                                  );

$stCompetencia13 = (Sessao::read("boCompetencia13")) ? "Sim" : "Não";
Sessao::write("stCompetencia13", $stCompetencia13);

$stCompetencia  = ( $_GET['inCodMes'] >= 10 )? $_GET['inCodMes'] : "0".$_GET['inCodMes'];
$stCompetencia .= "/".$_GET['inAno'];
Sessao::write("stCompetencia", $stCompetencia);

$stTabela1 .= "<center><table border=1  width=65%>";
$stTabela1 .= "<tr><td align=center colspan=3 width=65%><font size=3><b>Resumo dos Totais Arquivo TCM/BA</b></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Competência:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".$stCompetencia."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Competência 13:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".$stCompetencia13."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Tipo de Envio:</font></td>";

switch ($_REQUEST['inTipoEnvio']) {
    case 1 :
        $stTipoEnvio = "Inclusão";
    break;
    case 2 :
        $stTipoEnvio = "Substituição";
    break;
}
Sessao::write("inTipoEnvio", $stTipoEnvio);
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".$stTipoEnvio."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=65% colspan=3></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1><b>Total de Servidores:</b></font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalServidores")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Cargo Efetivo:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalEfetivo")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Celetistas:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalCeletista")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Cargo em Comissão:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalComissao")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Trabalhador Temporário:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalTemporario")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Agente Político:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalPolitico")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Aposentados:</font></td>";
$stTabela1 .= "<td align=right width=40% colspan=2><font size=-1>".Sessao::read("inTotalAposentado")." </font></td></tr>";

$stTabela1 .= "<tr><td align=right width=65% colspan=3></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Totais da Competência</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>Admissões</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>Exclusões</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Cargo Efetivo:</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalAdmissaoEfetivo")."</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalDemissaoEfetivo")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Celetistas:</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalAdmissaoCeletista")."</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalDemissaoCeletista")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Cargo em Comissão</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalAdmissaoCargoComissao")."</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalDemissaoCargoComissao")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Trabalhador Temporário</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalAdmissaoTemporario")."</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalDemissaoTemporario")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Agente Político</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalAdmissaoPolitico")."</font></td>";
$stTabela1 .= "<td align=right width=20%><font size=-1>".Sessao::read("inTotalDemissaoPolitico")."</font></td></tr>";

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
