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
    * Página de Formulário do Exportação SEFIP
    * Data de Criação: 15/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 08:32:58 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.08.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$arArquivoDownload = Sessao::read('arArquivosDownload');

$stPrograma = "ExportarSEFIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
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
$obForm->setAction                              ( CAM_FW_POPUPS."relatorio/OCRelatorio.php"                 );
$obForm->setTarget                              ( "oculto"                                                  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                          ( "stCaminho"                                               );
$obHdnCaminho->setValue                         ( CAM_GRH_IMA_INSTANCIAS."sefip/PRRelatorioSEFIP.php");

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMARecolhimento.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidencia.class.php");
$obTIMARecolhimento = new TIMARecolhimento();
$obTIMARecolhimento->setDado("cod_recolhimento",$_GET["inCodRecolhimentoTxt"]);
$obTIMARecolhimento->recuperaPorChave($rsRecolhimento);

$stRecolhimento = $_GET["inCodRecolhimentoTxt"]." - ".$rsRecolhimento->getCampo("descricao");
Sessao::write("stRecolhimento", $stRecolhimento);

$stCompetencia13 = (Sessao::read("boCompetencia13")) ? "Sim" : "Não";
Sessao::write("stCompetencia13", $stCompetencia13);
$obTFolhaPagamentoPrevidencia = new TFolhaPagamentoPrevidencia();
$stFiltroPrevidencia = " WHERE cod_regime_previdencia = 1";
$obTFolhaPagamentoPrevidencia->recuperaTodos($rsPrevidencia,$stFiltroPrevidencia);

$obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia();
$stFiltroPrevidencia = " AND previdencia_previdencia.cod_previdencia = ".$rsPrevidencia->getCampo("cod_previdencia");
$obTFolhaPagamentoPrevidenciaPrevidencia->recuperaRelacionamento($rsPrevidenciaPrevidencia,$stFiltroPrevidencia);
$nuValorPatronal = ((Sessao::read("nuBasePrevidenciaS13")+Sessao::read("nuBasePrevidencia13"))*($rsPrevidenciaPrevidencia->getCampo("aliquota")+Sessao::read("aliquota_rat")))/100 + Sessao::read("nuDescontoPrevidenciaS13DemaisOcor") - Sessao::read("nuSalarioFamilia") - Sessao::read("nuTotalSalarioMaternidade");
if ($nuValorPatronal < 0) {
    $nuValorPatronal = 0;
}
Sessao::write("nuValorPatronal", $nuValorPatronal);

$stCompetencia  = ( $_GET['inCodMes'] >= 10 )? $_GET['inCodMes'] : "0".$_GET['inCodMes'];
$stCompetencia .= "/".$_GET['inAno'];
Sessao::write("stCompetencia", $stCompetencia);

$stTabela1 .= "<table border=0  width=100%>";
$stTabela1 .= "<tr><td align=center colspan=4 width=100%><font size=3><b>Totais Arquivos SEFIP</b></font></td></tr>";
$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Competência:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".$stCompetencia."</font></td>";
$stTabela1 .= "<td align=left colspan=2 width=50%><font size=-1>Quantidade de Afastamentos Listados nos Arquivos:</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Competência 13:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".$stCompetencia13."</font></td>";
$stTabela1 .= "<td align=right width=25%><font size=-1>Doença + 15 dias:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inDoenca15Dias")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Código do Recolhimento:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".$stRecolhimento."</font></td>";
$stTabela1 .= "<td align=right width=25%><font size=-1>Acidente Trabalho:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inAcidenteTrabalho")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%></td>";
$stTabela1 .= "<td align=left width=25%></td>";
$stTabela1 .= "<td align=right width=25%><font size=-1>Licença Maternidade:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inLicencaMaternidade")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1><b>Servidores Listados nos Arquivos:</b></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTotalServidores")."</font></td>";
$stTabela1 .= "<td align=right width=25%><font size=-1>Movimentação Definitiva (Rescisões):</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inRescisoes")."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Base Previdência s/13°:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuBasePrevidenciaS13"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Desc. Previdência s/13° Ocorr. 05 e Maternidade:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuDescontoPrevidenciaS13"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Desc. Previdência s/13° Demais Servidores:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuDescontoPrevidenciaS13DemaisOcor"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Base Previdência 13°:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuBasePrevidencia13"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Salário Maternidade:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuTotalSalarioMaternidade"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Salário Família:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuSalarioFamilia"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Valor Patronal:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format($nuValorPatronal,2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Base FGTS:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuBaseFGTS"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Base FGTS 13° Salário:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".number_format(Sessao::read("nuBaseFGTS13"),2,',','.')."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Total Servidores Listados nos Arquivos:</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1>".Sessao::read("inTotalServidoresArquivo")."</font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td>";
$stTabela1 .= "<td align=left width=25%><font size=-1></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1><b>Obs:</b></font></td>";
$stTabela1 .= "<td align=left width=25% colspan=3><font size=-1><b>Foi gerado o seguinte arquivo para download: SEFIP.zip. Os arquivos compactados neste arquivo zip deverão ser importados individualmente (renomear) para o programa da SEFIP.</b></font></td>";
$stTabela1 .= "</tr>";

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
$obFormulario->addHidden                        ( $obHdnCaminho                                             );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addSpan($spnResumo);
$obFormulario->defineBarra(array($obBtnImprimir,$obBtnDownload),"","");
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
