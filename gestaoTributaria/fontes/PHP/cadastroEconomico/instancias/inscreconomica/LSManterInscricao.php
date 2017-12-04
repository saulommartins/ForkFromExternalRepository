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
    * Página de Lista para cadastro de Inscrição Econômica
    * Data de Criação   : 11/01/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: LSManterInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php"      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php"           );

//Define o nome dos arquivos PHP
$stPrograma  = "ManterInscricao";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormAlt   = "FM".$stPrograma."Alt.php";

$pgFormConvFD = "FM".$stPrograma."ConvFD.php"; //converter empresa de Fato para Direito

$pgFormBaixa = "FM".$stPrograma."Baixa.php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgDefAtiv   = "FMDefinirAtividades.php";
$pgDefResp   = "FMDefinirResponsaveis.php";
$pgDefElem   = "FMDefinirElementos.php";
$pgAltSoc    = "FMAlterarSociedade.php";
$pgAltAtiv   = "FMAlterarAtividade.php";
$pgNatAlt    = "FM".$stPrograma."NaturezaAlteracao.php";
$pgAtvAlt    = "FM".$stPrograma."AtividadeAlteracao.php";
$pgDomAlt    = "FM".$stPrograma."DomicilioAlteracao.php";
$pgHorAlt    = "FM".$stPrograma."HorarioAlteracao.php";
$pgJS        = "JS".$stPrograma.".js";

include_once ($pgJS);

Sessao::write( "horarios", array() );
Sessao::write( "Atividades", array() );
Sessao::write( "elementos", array() );
Sessao::write( "responsaveis", array() );

$stCaminho = CAM_GT_CEM_INSTANCIAS . "inscreconomica/";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

/**
    * Define arquivos PHP para cada ação
*/

switch ($stAcao) {
    case 'alterar'   : $pgProx = $pgFormAlt; break;
    case 'converter' : $pgProx = $pgFormConvFD; break; //converter empresa de Fato em empresa de Direito
    case 'baixar'    : $pgProx = $pgFormBaixa; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    case 'historico' : $pgProx = $pgFormCaracteristica; break;
    case 'deoficio'  : $pgProx = $pgFormBaixa; break;
    case 'reativar'  : $pgProx = $pgFormBaixa; break;
    case 'def_ativ'  : $pgProx = $pgDefAtiv; break;
    case 'def_resp'  : $pgProx = $pgDefResp; break;
    case 'def_elem'  : $pgProx = $pgDefElem; break;
    case 'natureza'  : $pgProx = $pgNatAlt; break;
    case 'domicilio' : $pgProx = $pgDomAlt; break;
    case 'horario'   : $pgProx = $pgHorAlt; break;
    case 'elemento'  : $pgProx = $pgDefElem; break;
    case 'sociedade' : $pgProx = $pgAltSoc; break;
    case 'atividade' : $pgProx = $pgAltAtiv; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {
    $stLink .= "&pg=".$_GET["pg"];
    $stLink .= "&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}
Sessao::write( "link", $link );
if (( $_REQUEST[ 'stAcao' ] == 'natureza' ) || ( $_REQUEST[ 'stAcao' ] == 'sociedade' )) {
    $obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;
} else {
    $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
}

if ($stAcao != 'converter' and $_REQUEST['inNumCGM']) {
    $obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST[ 'inNumCGM' ] );
    $obRCEMInscricaoEconomica->obRCGMPessoaJuridica->setNumCGM( $_REQUEST[ 'inNumCGM' ] );
}

if ($_REQUEST[ 'inInscricaoEconomica' ] || $_REQUEST[ 'inInscricao' ]) {
    $inscricao = $_REQUEST[ 'inInscricaoEconomica' ] ? $_REQUEST[ 'inInscricaoEconomica' ] : $_REQUEST[ 'inInscricao' ];
    $obRCEMInscricaoEconomica->setInscricaoEconomica( $inscricao );
}

if ($stAcao == 'domicilio') {
    $obRCEMInscricaoEconomica->setTipoListagem ('domicilio');
}

if ($stAcao=="reativar") {
    $obRCEMInscricaoEconomica->listarInscricaoBaixa( $rsListaInscricao );
} elseif ($stAcao=='converter') {
    if ($_REQUEST['inNumCGM']) {
        $obRCEMInscricaoEconomica->obRCGMPessoaFisica->setNumCGM($_REQUEST['inNumCGM']);
    }
    $obRCEMInscricaoEconomica->listarInscricaoConversao($rsListaInscricao);
} else {
    $obRCEMInscricaoEconomica->listarInscricao( $rsListaInscricao );
}

$obLista = new Lista;
$obLista->setRecordSet( $rsListaInscricao );
$obLista->setTitulo ("Registros de Inscrição Econômica");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição Econômica" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodigoEnquadramento", "enquadramento"       );
$obLista->ultimaAcao->addCampo( "&inInscricaoEconomica" , "inscricao_economica" );
$obLista->ultimaAcao->addCampo( "&inCGM"                , "numcgm"              );
$obLista->ultimaAcao->addCampo( "&stCGM"                , "nom_cgm"             );
$obLista->ultimaAcao->addCampo( "&stDescQuestao"        , "[inscricao_economica] - [nom_cgm]" );
$obLista->ultimaAcao->addCampo( "&stDtAbertura"         , "dt_abertura"         );
if ($stAcao=="reativar") {
   $obLista->ultimaAcao->addCampo( "&stDtTermino"       , "dt_baixa"            );
}

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.10" );
$obFormulario->show();

?>
