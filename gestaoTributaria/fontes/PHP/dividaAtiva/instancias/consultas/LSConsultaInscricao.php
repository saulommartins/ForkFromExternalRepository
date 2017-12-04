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
  * Página de Lista da Consulta de Divida Ativa
  * Data de criação : 13/02/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSConsultaInscricao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.04
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpModalidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "consultar";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$_REQUEST['stAcao'];
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
$link = Sessao::read( 'link' );
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = '';

if ($_REQUEST["inNrParcelamento"] != "") {
    $arParcelamento = explode( "/", $_REQUEST["inNrParcelamento"] );
    $stFiltro .= " ddp.numero_parcelamento = ".$arParcelamento[0]." AND ddp.exercicio = '".$arParcelamento[1]."' AND ";
}

if ($_REQUEST['inCGM'] != "") {
    $stFiltro .= " \n dda.numcgm_contribuinte = '".$_REQUEST['inCGM']."' AND ";
}

if ($_REQUEST['inCodInscricaoInicial'] != "") {
    $arCodInscricaoInicial =  explode('/', $_REQUEST['inCodInscricaoInicial'] );
    $inCodInscricaoInicial = $arCodInscricaoInicial[0];
    $inExercicioInicial    = $arCodInscricaoInicial[1];
}
if ($_REQUEST['inCodInscricaoFinal'] != "") {
    $arCodInscricaoFinal =  explode('/', $_REQUEST['inCodInscricaoFinal'] );
    $inCodInscricaoFinal   = $arCodInscricaoFinal[0];
    $inExercicioFinal      = $arCodInscricaoFinal[1];
}

 //Filtro Inscrição
if ( ( $inCodInscricaoInicial != "") && ( $inCodInscricaoFinal != ""  ) ) {
    $stFiltro = " dda.cod_inscricao between ".$inCodInscricaoInicial." AND ".$inCodInscricaoFinal." AND ";
    $stFiltro .= " dda.exercicio between '".$inExercicioInicial."' AND '".$inExercicioFinal."' AND ";
} elseif ( ( $inCodInscricaoInicial != "") && ( $inCodInscricaoFinal == ""  ) ) {
    $stFiltro .= " dda.cod_inscricao = ".$inCodInscricaoInicial." AND ";
    $stFiltro .= " dda.exercicio = '".$inExercicioInicial."' AND ";
} elseif ( ( $inCodInscricaoInicial == "") and ( $inCodInscricaoFinal != ""  ) ) {
     $stFiltro .= " dda.cod_inscricao = ".$inCodInscricaoFinal." AND ";
     $stFiltro .= " dda.exercicio = '".$inExercicioFinal."' AND ";
}
/*********************************************************************/
//Filtro Livro

if ($_REQUEST['inLivroFolhaInicial']  !="" && $_REQUEST['inLivroFolhaFinal'] != "") {
    $stFiltro .= " dda.num_livro between ".$_REQUEST['inLivroFolhaInicial']." AND ".$_REQUEST['inLivroFolhaFinal']." AND ";
} elseif ($_REQUEST['inLivroFolhaInicial']  != "" && $_REQUEST['inLivroFolhaFinal'] == "") {
    $stFiltro .= " dda.num_livro = ".$inLivroInicial." AND ";
} elseif ($_REQUEST['inLivroFolhaInicial'] == "" && $_REQUEST['inLivroFolhaFinal'] != "") {
    $stFiltro .= " dda.num_livro = ".$_REQUEST['inLivroFolhaFinal']." AND ";
}
/********************************************************************/
//Filtro Folha

if ($_REQUEST['inFolhaInicial'] != "" && $_REQUEST['inFolhaFinal'] != "") {
    $stFiltro .= " dda.num_folha between ".$_REQUEST['inFolhaInicial']." AND ".$_REQUEST['inFolhaFinal']." AND ";
} elseif ($_REQUEST['inFolhaInicial'] != "" && $_REQUEST['inFolhaFinal']== "") {
    $stFiltro .= " dda.num_folha = ".$_REQUEST['inFolhaInicial']." AND ";
} elseif ($_REQUEST['inFolhaInicial'] == "" && $_REQUEST['inFolhaFinal'] != "") {
    $stFiltro .= " dda.num_folha = ".$_REQUEST['inFolhaFinal']." AND ";
}
/******************************************************************/
//Filtro Codigo Imovel
if ($_REQUEST['inCodImovelInicial'] != "" && $_REQUEST['inCodImovelFinal'] != "") {
    $stFiltro .= " \n dda.inscricao_municipal BETWEEN ".$_REQUEST['inCodImovelInicial']." AND ".$_REQUEST['inCodImovelFinal']." AND ";
} elseif ($_REQUEST['inCodImovelInicial'] != "" && $_REQUEST['inCodImovelFinal'] =="") {
    $stFiltro .= " \n dda.inscricao_municipal = ".$_REQUEST['inCodImovelInicial']." AND ";
} elseif ($_REQUEST['inCodImovelInicial'] =="" && $_REQUEST['inCodImovelFinal'] != "") {
    $stFiltro .= " \n dda.inscricao_municipal = ".$_REQUEST['inCodImovelFinal']." AND ";
}

/******************************************************************/
//Filtro Inscricao Economica
if ($_REQUEST['inNumInscricaoEconomicaInicial'] != "" && $_REQUEST['inNumInscricaoEconomicaFinal'] !="") {
    $stFiltro .= " \n dda.inscricao_economica BETWEEN ".$_REQUEST['inNumInscricaoEconomicaInicial']." AND ".$_REQUEST['inNumInscricaoEconomicaFinal']." AND ";
} elseif ($_REQUEST['inNumInscricaoEconomicaInicial'] != "" && $_REQUEST['inNumInscricaoEconomicaFinal'] =="") {
    $stFiltro .= " \n dda.inscricao_economica = ".$_REQUEST['inNumInscricaoEconomicaInicial']." AND ";
} elseif ($_REQUEST['inNumInscricaoEconomicaInicial'] == "" && $_REQUEST['inNumInscricaoEconomicaFinal'] != "") {
    $stFiltro .= " \n dda.inscricao_economica = ".$_REQUEST['inNumInscricaoEconomicaFinal']." AND ";
}

if ($stFiltro != "") {
    $stFiltro =     " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 )."                       \n";
    $stFiltro .=    " ORDER BY dda.exercicio, dda.cod_inscricao                                     \n";
}

$obTDATDividaAtiva = new TDATDividaAtiva;
$obTDATDividaAtiva->ListaConsultaDivida2 ( $rsListaDivida, $stFiltro );

while ( !$rsListaDivida->eof() ) {
    if ( $rsListaDivida->getCampo("situacao") == "Sem cobrança" ) {
        //verificando se a inscricao em divida está paga (com parcelas pagas e canceladas
        $stFiltro = " \n AND dda.cod_inscricao = ".$rsListaDivida->getCampo("cod_inscricao");
        $stFiltro .= " AND dda.exercicio = '".$rsListaDivida->getCampo("exercicio")."'";

        $stFilto1 = " WHERE divida_parcelamento.cod_inscricao =  ".$rsListaDivida->getCampo("cod_inscricao")." AND divida_parcelamento.exercicio =  '".$rsListaDivida->getCampo("exercicio")."'" ;

        $obTDATDividaAtiva->recuperaListaCobrancaDetalhe ( $rsListaDividaCobrar, $stFiltro, $stFilto1  );

        if ( $rsListaDividaCobrar->eof() ) {
            $rsListaDivida->setCampo("situacao", "Paga" );
        }
    }

    if ( !in_array($rsListaDivida->getCampo("situacao"),array('Cobrança Judicial','Paga','Aberta') ) ) {
        $rsListaDivida->setCampo("dt_vencimento_parcela", $rsListaDivida->getCampo("dt_inscricao_divida") );
    }

    if ( $rsListaDivida->getCampo("credito") )
        $rsListaDivida->setCampo("credito", str_replace(";", "<BR>", $rsListaDivida->getCampo("credito") ) );

    $rsListaDivida->proximo();
}

$rsListaDivida->setPrimeiroElemento();

$obLista = new Lista;
$obLista->setRecordSet( $rsListaDivida );
$obLista->setTitulo( "Registros de Inscrição" );
$obLista->setMostraPaginacao(false);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição/Ano" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Livro/Folha" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Cobrança/Ano" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contribuinte" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Origem");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Modalidade");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 14 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ( "CENTRO"                 );
$obLista->ultimoDado->setCampo( "[cod_inscricao]/[exercicio]"   );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO"                  );
$obLista->ultimoDado->setCampo      ( "[num_livro]/[num_folha]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ( "CENTRO"                 );
$obLista->ultimoDado->setCampo( "[max_cobranca]"   );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm_contribuinte]-[nom_cgm_contribuinte]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ( "CENTRO"                 );
$obLista->ultimoDado->setCampo( "credito" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [modalidade_descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento ( "CENTRO"                 );
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo( "&stSituacao"       , "situacao" );
$obLista->ultimaAcao->addCampo( "&stCredito"        , "credito" );
$obLista->ultimaAcao->addCampo( "&stMotivo"         , "motivo");
$obLista->ultimaAcao->addCampo( "&inCodModalidade"  , "cod_modalidade" );
$obLista->ultimaAcao->addCampo( "&stModalidadeDesc" , "modalidade_descricao" );
$obLista->ultimaAcao->addCampo( "&inNumParcelamento", "num_parcelamento" );
$obLista->ultimaAcao->addCampo( "&inNumCGMContrib"  , "numcgm_contribuinte" );
$obLista->ultimaAcao->addCampo( "&inNomCGMContrib"  , "nom_cgm_contribuinte" );
$obLista->ultimaAcao->addCampo( "&inNumCGMAutorid"  , "numcgm_autoridade" );
$obLista->ultimaAcao->addCampo( "&inNomCGMAutorid"  , "nom_cgm_autoridade" );
$obLista->ultimaAcao->addCampo( "&inCodInscricao"   , "cod_inscricao" );
$obLista->ultimaAcao->addCampo( "&inExercicio"      , "exercicio" );
$obLista->ultimaAcao->addCampo( "&stDataInscDiv"    , "dt_inscricao_divida" );
$obLista->ultimaAcao->addCampo( "&stDataBase", "dt_vencimento_parcela" );
$obLista->ultimaAcao->addCampo( "&inInscMunic"  , "inscricao_municipal" );
$obLista->ultimaAcao->addCampo( "&inInscEcon", "inscricao_economica" );
$obLista->ultimaAcao->addCampo( "&inCodProcesso", "cod_processo" );
$obLista->ultimaAcao->addCampo( "&inExercicioProcesso", "ano_exercicio" );
$obLista->ultimaAcao->addCampo( "&inNumCgmCancelada", "numcgm_cancelada" );
$obLista->ultimaAcao->addCampo( "&stNomCgmCancelada", "usuario_cancelada" );
$obLista->ultimaAcao->addCampo( "&dtCancelada", "data_cancelada" );
$obLista->ultimaAcao->addCampo( "&stRemissaoNorma", "remissao_norma" );
$obLista->ultimaAcao->addCampo( "&inRemissaoCodNorma", "remissao_cod_norma" );

$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "documentos" );
$obLista->ultimaAcao->addCampo( "&stSituacao", "situacao" );
$obLista->ultimaAcao->addCampo( "&stCredito", "credito" );
$obLista->ultimaAcao->addCampo( "&inCodModalidade", "cod_modalidade" );
$obLista->ultimaAcao->addCampo( "&stModalidadeDesc", "modalidade_descricao" );
$obLista->ultimaAcao->addCampo( "&inNumParcelamento", "num_parcelamento" );
$obLista->ultimaAcao->addCampo( "&inNumCGMContrib", "numcgm_contribuinte" );
$obLista->ultimaAcao->addCampo( "&inNomCGMContrib", "nom_cgm_contribuinte" );
$obLista->ultimaAcao->addCampo( "&inNumCGMAutorid", "numcgm_autoridade" );
$obLista->ultimaAcao->addCampo( "&inNomCGMAutorid", "nom_cgm_autoridade" );
$obLista->ultimaAcao->addCampo( "&inCodInscricao", "cod_inscricao" );
$obLista->ultimaAcao->addCampo( "&inExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "&stDataInscDiv", "dt_inscricao_divida" );
$obLista->ultimaAcao->addCampo( "&stDataBase", "dt_vencimento_parcela" );
$obLista->ultimaAcao->addCampo( "&inInscMunic", "inscricao_municipal" );
$obLista->ultimaAcao->addCampo( "&inInscEcon", "inscricao_economica" );
$obLista->ultimaAcao->addCampo( "&inCodProcesso", "cod_processo" );
$obLista->ultimaAcao->addCampo( "&inExercicioProcesso", "ano_exercicio" );
$obLista->ultimaAcao->addCampo( "&inNumCgmCancelada", "numcgm_cancelada" );
$obLista->ultimaAcao->addCampo( "&stNomCgmCancelada", "usuario_cancelada" );
$obLista->ultimaAcao->addCampo( "&dtCancelada", "data_cancelada" );
$obLista->ultimaAcao->setLink( "FMConsultaInscricaoDocumento.php?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
