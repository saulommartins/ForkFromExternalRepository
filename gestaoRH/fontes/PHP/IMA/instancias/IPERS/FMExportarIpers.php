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
    * Página de Formulário do Exportação Ipers
    * Data de Criação: 25/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FMExportarIpers.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$arArquivoDownload = Sessao::read('arArquivosDownload');

//Define o nome dos arquivos PHP
$stPrograma = "ExportarIpers";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PRRelatorioIpers.php";
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

$stCompetencia  = ( $_GET['inCodMes'] >= 10 )? $_GET['inCodMes'] : "0".$_GET['inCodMes'];
$stCompetencia .= "/".$_GET['inAno'];
Sessao::write("stCompetencia",$stCompetencia);

$stTabela1 .= "<center><table border=1  width=65%>";
$stTabela1 .= "<tr><td align=center colspan=3 width=45%><font size=3><b>Arquivo IPE/RS</b></font></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Competência:</font></td>";
$stTabela1 .= "<td align=left width=40% colspan=2><font size=-1>".$stCompetencia."</font></td></tr>";

if ($_GET['inCodConfiguracao'] != '') {
    $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Tipo de Cálculo:</font></td>";
    switch ($_GET['inCodConfiguracao']) {
        case 0:
            $stTipoFolha = "Complementar";
            break;
        case 1:
            $stTipoFolha = "Salário";
            break;
        case 2:
            $stTipoFolha = "Férias";
            break;
        case 3:
            $stTipoFolha = "13° Salário";
            break;
        case 4:
            $stTipoFolha = "Rescisão";
            break;
    }
    $stTabela1 .= "<td align=left width=50%><font size=-1>".$stTipoFolha."</font></td></tr>";

    if ($_GET['inCodConfiguracao'] == 3) {
        include_once ( CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGetDesdobramentoFolha.class.php" );
        $obFFolhaPagamentoGetDesdobramentoFolha = new FFolhaPagamentoGetDesdobramentoFolha();
        $obFFolhaPagamentoGetDesdobramentoFolha->setDado("inCodConfiguracao", $_GET['inCodConfiguracao']);
        $obFFolhaPagamentoGetDesdobramentoFolha->setDado("stDesdobramento"  , $_GET['stDesdobramento']);
        $obFFolhaPagamentoGetDesdobramentoFolha->recuperaGetDesdobramentoFolha($rsDesdobramento);

        $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Desdobramento:</font></td>";
        $stTabela1 .= "<td align=left width=50%><font size=-1>".$rsDesdobramento->getCampo("desdobramento")."</font></td></tr>";
    }

    if ($_GET['inCodConfiguracao'] == 0) {
        $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Folha Complementar:</font></td>";
        $stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['inCodComplementar']."</font></td></tr>";
    }

}

$stTabela1 .= "<tr><td align=right width=25%><font size=-1>Tipo de Emissão:</font></td>";

switch ($_REQUEST['inCodTipoEmissao']) {
    case 1 :
        $stDescTipoEmissao = "Manutenção";
    break;
    case 2 :
        $stDescTipoEmissao = "Acerto de Manutenção";
    break;
    case 3 :
        $stDescTipoEmissao = "Inclusão";
    break;
    case 4 :
        $stDescTipoEmissao = "Acerto de Inclusão";
    break;
}
Sessao::write("stDescTipoEmissao",$stDescTipoEmissao);
$stTabela1 .= "<td align=left width=40% colspan=2><font size=-1>".$stDescTipoEmissao."</font></td></tr>";

$stTabela1 .= "<tr><td align=right width=65% colspan=3></td></tr>";

$stTabela1 .= "<tr><td align=right width=25%><font size=-1><b>Total de Servidores:</b></font></td>";
$stTabela1 .= "<td align=left width=40% colspan=2><font size=-1>".Sessao::read("inQuantRegistros")." </font></td></tr>";

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
