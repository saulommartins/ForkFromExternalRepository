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
    * Página de Formulário do Exportação Remessa BESC
    * Data de Criação: 04/12/2006

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FMExportarRemessaBESC.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.08.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBESC";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

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
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

$stTabela1 .= "<table border=0 width=100%>";
$stTabela1 .= "<tr><td align=center colspan=2 width=100%><font size=3><b>Resumo Remessa BESC</b></font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Competência:</font></td>";

if ($_GET['stSituacao'] == 'd') {
        ///////// COMPETENCIA ATUAL
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);
        $arFinalCompetenciaAtual   = explode("/",$rsUltimaMovimentacao->getCampo('dt_final'));
        $stCompetencia = $arFinalCompetenciaAtual[1]."/".$arFinalCompetenciaAtual[2];
} else {
        $stCompetencia  = ( $_GET['inCodMes'] >= 10 )? $_GET['inCodMes'] : "0".$_GET['inCodMes'];
        $stCompetencia .= "/".$_GET['inAno'];
}

$stTabela1 .= "<td align=left width=50%><font size=-1>".$stCompetencia."</font></td></tr>";

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

if ($_GET["stSituacao"]) {
    switch ($_GET["stSituacao"]) {
        case "ativos";
            $stCadastro = "Ativos";
            break;
        case "aposentados";
            $stCadastro = "Aposentados";
            break;
        case "pensionistas";
            $stCadastro = "Pensionistas";
            break;
        case "estagiarios";
            $stCadastro = "Estagiários";
            break;
        case "rescindidos";
            $stCadastro = "Rescindidos";
            break;
    case "todos";
            $stCadastro = "Todos";
            break;
    case "pensao_judicial";
            $stCadastro = "Pensão Judicial";
            break;
    }
}

if ($_GET["inTipoMovimento"] != "") {
    switch ($_GET["inTipoMovimento"]) {
        case 0;
            $stTipoMovimento = "0 - Inclusão de Lançamento Novo";
            break;
        case 5;
            $stTipoMovimento = "5 - Alteração de Lançamento";
            break;
    }
}

$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Cadastro:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$stCadastro."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Data da Geração do Arquivo:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['dtGeracaoArquivo']."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Data do Pagamento:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['dtPagamento']."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Tipo de Movimento:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$stTipoMovimento."</font></td></tr>";
if ($_GET['nuValorLiquidoInicial'] and $_GET['nuValorLiquidoFinal']) {
    $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Valores Líquidos de:</font></td>";
    $stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['nuValorLiquidoInicial']." até ".$_GET['nuValorLiquidoFinal']."</font></td></tr>";
}
if ($_GET['nuPercentualPagar']) {
    $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Percentual à Pagar do Líquido:</font></td>";
    $stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['nuPercentualPagar']." %</font></td></tr>";
}
$stTabela1 .= "</table>";

$arArquivosDownload = Sessao::read('arArquivosDownload');

$arLista = array();
$arArquivoDadosExtras = Sessao::read("arArquivoDadosExtras");
foreach ($arArquivosDownload as $arArquivo) {
    $stNomeArquivo = $arArquivo['stNomeArquivo'];
    $stLink = $pgDown."?arq=".$arArquivo['stLink']."&label=".$stNomeArquivo;
    $obLink = new Link;
    $obLink->setValue($stNomeArquivo);
    $obLink->setHref($stLink);
    $obLink->montaHtml();

    $arTemp["arquivo"]      = $obLink->getHtml();
    $arTemp["seq"]          = $arArquivoDadosExtras[$stNomeArquivo]["seq"];
    $arTemp["descricao"]    = $arArquivoDadosExtras[$stNomeArquivo]["descricao"];
    $arTemp["qtd"]          = $arArquivoDadosExtras[$stNomeArquivo]["qtd"];
    $arTemp["total"]        = $arArquivoDadosExtras[$stNomeArquivo]["total"];
    $arLista[] = $arTemp;
}
$rsLista = new Recordset;
$rsLista->preenche($arLista);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setMostraPaginacao( false );
$obLista->setNumeracao(false);
$obLista->setRotuloSomatorio("Total Geral");
$arValor[] = 'qtd,&nbsp;,c,3';
$arValor[] = 'total,&nbsp;,r,4';
$obLista->setTotalizaMultiplo($arValor);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Arquivo Gerado");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Seq");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Qte de Registros");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Total do Arquivo");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "arquivo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[seq]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[qtd]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[total]" );
$obLista->commitDado();

$obLista->montaHTML();
$stHtml = $obLista->getHTML();
$stHtml = str_replace("\n","",$stHtml);
$stHtml = str_replace("  ","",$stHtml);
$stHtml = str_replace("'","\\'",$stHtml);

$stTabela1 .= $stHtml;
$stTabela1 .= "</center>";

$spnResumo = new Span();
$spnResumo->setValue($stTabela1);

SistemaLegado::LiberaFrames();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addSpan($spnResumo);
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
