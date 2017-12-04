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
    * Página de Formulario de Emissao de Nota Avulsa

    * Data de Criação   : 23/06/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: $

    *Casos de uso: uc-05.03.22

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCadastroEconomicoCalculo.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_OOPARSER."tbs_class.php" );
include_once ( CAM_OOPARSER."tbsooo_class.php" );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaAvulsa";
$pgFilt        = "FL".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->consultar();

$obConexao = new Conexao;
if ($obRARRConfiguracao->getNotaAvulsa() == "sim") {
    $stSql = "select valor from arrecadacao.lancamento where cod_lancamento = ".($_REQUEST["inCodLancamentoNotaAvul"]-1);
} else {
    $stSql = "select *
                from arrecadacao.lancamento_calculo
                left join arrecadacao.calculo
                  on arrecadacao.calculo.cod_calculo = arrecadacao.lancamento_calculo.cod_calculo
               where arrecadacao.lancamento_calculo.cod_lancamento = ".($_REQUEST["inCodLancamento"])."
                 and arrecadacao.calculo.cod_credito||'.'||arrecadacao.calculo.cod_natureza||'.'||arrecadacao.calculo.cod_genero||'.'||arrecadacao.calculo.cod_especie <> '99.1.2.1'  ";

}
$obConexao->executaSQL($rsINSS, $stSql, $boTransacao );

if ($_REQUEST["inCodParcelaNotaAvul"]) {
    $stFiltro = " ece.inscricao_economica = ".$_REQUEST["inscricao_economica"]." and ap.cod_parcela = '".$_REQUEST["inCodParcelaNotaAvul"]."' AND ( ac.cod_credito = 99 and ac.cod_genero = 2 and ac.cod_especie = 1 and ac.cod_natureza = 1 ) ";
} else {
    $inCodLancamento = $_REQUEST["inCodLancamentoNotaAvul"];
    if (SistemaLegado::is_manaquiri()) {
        $inCodLancamento = $_REQUEST["inCodLancamento"];
    }
    $stFiltro = " ece.inscricao_economica = ".$_REQUEST["inscricao_economica"]." and al.cod_lancamento = '".$inCodLancamento."' AND ( ac.cod_credito = 99 and ac.cod_genero = 2 and ac.cod_especie = 1 and ac.cod_natureza = 1 ) ";
}

$obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo;
$obTARRCadastroEconomicoCalculo->recuperaArquivoNotaAvulsa( $rsDocumento );
$obTARRCadastroEconomicoCalculo->recuperaDadosNotaAvulsa( $rsDados, $stFiltro );

$arTempDados = $rsDados->getElementos();
$flValorISS = 0.00;
$flTotalNF = 0.00;
for ( $inX=0; $inX<count( $arTempDados ); $inX++ ) {
    //$flValorISS += ($arTempDados[$inX]["valor_serv_total"] * $arTempDados[$inX]["aliq_serv"]) / 100;
    $flTotalNF += $arTempDados[$inX]["valor_serv_total"];
    $arTempDados[$inX]["valor_serv_total"] = number_format( $arTempDados[$inX]["valor_serv_total"], 2, ',', '.' );
    $arTempDados[$inX]["valor_serv"] = number_format( $arTempDados[$inX]["valor_serv"], 2, ',', '.' );
    $arTempDados[$inX]["aliq_serv"] = number_format( $arTempDados[$inX]["aliq_serv"], 2, ',', '.' );
    $arTempDados[$inX]["desc_serv"] = substr( $arTempDados[$inX]["desc_serv"], 0 );
}

$arTempDados[0]["nom_usuario"] = Sessao::read( "numCgm" )." - ".Sessao::read( "nomCgm" );
//$arTempDados[0]["iss"] = number_format( $flValorISS, 2, ',', '.' );
$arTempDados[0]["iss"] = number_format( $rsINSS->getCampo('valor'), 2, ',', '.' );
$arTempDados[0]["total_nf"] = number_format( $flTotalNF, 2, ',', '.' );

/*
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
$arChaveAtributoInscricao =  array( "inscricao_economica" => $_REQUEST["inscricao_economica"] );
$obRCEMInscricaoEconomica->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoInscricao );
$obRCEMInscricaoEconomica->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
*/

// instantiate a TBS OOo class
$OOParser = new clsTinyButStrongOOo;

// setting the object
$OOParser->SetZipBinary('zip');
$OOParser->SetUnzipBinary('unzip');
$OOParser->SetProcessDir('/tmp');
$OOParser->SetDataCharset('UTF8');
$OOParser->NoErr;

$stDocumento = '/tmp/';
$OOParser->_process_path = $stDocumento; //nome do arquivo pra salva

// create a new openoffice document from the template with an unique id
if ( $obRARRConfiguracao->getQtdViasNotaAvulsa() == 4 ) {
    $arDoc = explode( ".", $rsDocumento->getCampo( "nome_arquivo_swx" ) );
    $stDoc = $arDoc[0]."4vias.".$arDoc[1];
} else {
    $stDoc = $rsDocumento->getCampo( "nome_arquivo_swx" );
}

$OOParser->NewDocFromTpl( CAM_GT_ARR_MODELOS.$stDoc ); //arquivo do openof
$OOParser->LoadXmlFromDoc('content.xml');

$OOParser->MergeBlock( 'DC', $arTempDados);
$OOParser->MergeBlock( 'DCS', $arTempDados);
$OOParser->MergeBlock( 'DCS2', $arTempDados);
$OOParser->MergeBlock( 'DCS3', $arTempDados);
$OOParser->MergeBlock( 'DCS4', $arTempDados);

$OOParser->SaveXmlToDoc();

$OOParser->LoadXmlFromDoc('styles.xml');
$OOParser->SaveXmlToDoc();

$arDadosArquivos[0]["nome_arquivo_tmp"] = $OOParser->GetPathnameDoc();
$arDadosArquivos[0]["nome_arquivo"] = $rsDocumento->getCampo( "nome_documento" );

Sessao::write( "dados", $arDadosArquivos );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addTitulo     ( "Documentos para Download" );

$inX=0;

$stDownLoadName = "stArq".$inX;
$stLblDownLoadName = "stLBArq".$inX;
$stBtnDownLoadName = "stBtnArq".$inX;

$obLabelDownLoad = new Label;
$obLabelDownLoad->setValue ( $arDadosArquivos[$inX]["nome_arquivo"] );
$obLabelDownLoad->setName   ( $stLblDownLoadName );

$obBtnDownLoad = new Button;
$obBtnDownLoad->setName               ( $stBtnDownLoadName );
$obBtnDownLoad->setValue              ( "Download" );
$obBtnDownLoad->setTipo               ( "button" );
$obBtnDownLoad->obEvento->setOnClick  ( "buscaValor('Download')" );
$obBtnDownLoad->setDisabled           ( false );

$obFormulario->defineBarra ( array( $obLabelDownLoad, $obBtnDownLoad ), 'left', '' );

$obFormulario->show();
