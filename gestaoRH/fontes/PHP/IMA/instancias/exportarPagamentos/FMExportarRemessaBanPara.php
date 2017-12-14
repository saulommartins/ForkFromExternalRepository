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
    * Página de Formulário do Exportação Remessa Banpara
    * Data de Criação: 10/04/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FMExportarRemessaBanPara.php 30829 2008-07-07 19:59:54Z alex $

    * Casos de uso: uc-04.08.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBanPara";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao  = $_REQUEST["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction    ( $pgProc  );
$obForm->setTarget    ( "oculto" );

$stTabela1 .= "<table border=0 width=100%>";
$stTabela1 .= "<tr><td align=center colspan=2 width=100%><font size=3><b>Resumo Remessa BanPará</b></font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Competência:</font></td>";

if ($_GET['stSituacao'] == 'estagiarios') {
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

if ($_REQUEST['inCodEmpresa']) {
    $rsArquivos       	   = new RecordSet();
    $rsConfiguracaoBanpara = Sessao::read("rsContas");

    if (is_array(Sessao::read('inCodOrgaosSelecionados'))) {
        $stCodOrgaosSelecionados = implode(',', Sessao::read('inCodOrgaosSelecionados'));
    }

    $arArquivosDownload = Sessao::read('arArquivosDownload');
    if ($rsConfiguracaoBanpara->getNumLinhas() > 0) {
        while (!$rsConfiguracaoBanpara->eof()) {
            $stOrgaos .= $rsConfiguracaoBanpara->getCampo('num_orgao_banpara')." - ".$rsConfiguracaoBanpara->getCampo('descricao')."<br>";

            if (is_array($arArquivosDownload)) {
                foreach ($arArquivosDownload as $arquivoDownload) {
                    if (strpos($arquivoDownload['stNomeArquivo'], "BanPara_Cad".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara'))  !== false ||
                        strpos($arquivoDownload['stNomeArquivo'], "BanPara_Cred".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara')) !== false ) {

                       $arDadosArquivosRemessaBanPara = Sessao::read('dadosArquivosRemessaBanPara');

                       $arArquivos = array();
                       $arArquivos['orgao']     = $rsConfiguracaoBanpara->getCampo('num_orgao_banpara')." - ".$rsConfiguracaoBanpara->getCampo('descricao');
                       $arArquivos['registros'] = $arDadosArquivosRemessaBanPara[ $arquivoDownload['stNomeArquivo'] ]['nuRegistros'];

                       if (strpos($arquivoDownload['stNomeArquivo'], "BanPara_Cred".$rsConfiguracaoBanpara->getCampo('num_orgao_banpara')) !== false) {
                           $arArquivos['valor'] = number_format($arDadosArquivosRemessaBanPara[ $arquivoDownload['stNomeArquivo'] ]['nuVlrLancamentoTotal'],2,',','.');
                           $arArquivos['tipo']  = "Crédito";
                       } else {
                           $arArquivos['valor'] = "N/A";
                           $arArquivos['tipo']  = "Cadastro";
                       }
                       $arArquivos['download'] = "<a href='".$pgDown."?arq=".$arquivoDownload['stLink']."&label=".$arquivoDownload['stNomeArquivo']."'>".$arquivoDownload['stNomeArquivo']."</a>";
                       $rsArquivos->add($arArquivos);
                    }
                }
            }
            $rsConfiguracaoBanpara->proximo();
        }
    }
}

$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Cadastro:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$stCadastro."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Código da Empresa:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$stCodEmpresa."</font></td></tr>";
$stTabela1 .= "<tr valign=top><td align=right width=50%><font size=-1>Órgãos Selecionados para o aquivo:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$stOrgaos."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Data da Geração do Arquivo:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['dtGeracaoArquivo']."</font></td></tr>";
$stTabela1 .= "<tr><td align=right width=50%><font size=-1>Data do Pagamento:</font></td>";
$stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['dtPagamento']."</font></td></tr>";
if ($_GET['nuValorLiquidoInicial'] and $_GET['nuValorLiquidoFinal']) {
    $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Valores Líquidos de:</font></td>";
    $stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['nuValorLiquidoInicial']." até ".$_GET['nuValorLiquidoFinal']."</font></td></tr>";
}
if ($_GET['nuPercentualPagar']) {
    $stTabela1 .= "<tr><td align=right width=50%><font size=-1>Percentual à Pagar do Líquido:</font></td>";
    $stTabela1 .= "<td align=left width=50%><font size=-1>".$_GET['nuPercentualPagar']." %</font></td></tr>";
}
$stTabela1 .= "</table>";

$obLista = new Lista;
$obLista->setTitulo("Download de Arquivos");
$obLista->setMostraPaginacao(false);
$obLista->setRecordSet( $rsArquivos );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Órgão");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo de Arquivo");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Quant. Registros" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor Total");
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Download");
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "orgao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "tipo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "registros" );
$obLista->ultimoDado->setAlinhamento('RIGHT');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "valor" );
$obLista->ultimoDado->setAlinhamento('RIGHT');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "download" );
$obLista->commitDado();
$obLista->montaHTML();

$stTabela1 .= $obLista->getHTML();
$stTabela1 .= "</center>";

$spnResumo = new Span();
$spnResumo->setValue($stTabela1);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addSpan	 ( $spnResumo );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
