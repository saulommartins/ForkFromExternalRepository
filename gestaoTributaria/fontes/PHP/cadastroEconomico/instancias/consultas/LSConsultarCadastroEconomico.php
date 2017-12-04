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
    * Página de Lista para consulta de Inscrição Econômica
    * Data de Criação: 16/09/2005

    * @author Marcelo B. Paulino

    * @ignore

    * $Id: LSConsultarCadastroEconomico.php 63376 2015-08-21 18:55:42Z arthur $

    * Casos de uso: uc-05.02.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php";

//Define o nome dos arquivos PHP
$stCadastroEcon = "FMConsultarCadastroEconomico.php";
$stRespTecnico  = "FMConsultarResponsaveis.php";
$stAtividades   = "FMConsultarAtividades.php";
$stLicencas     = "FMConsultarLicenca.php";
$pgJS           = "JSConsultarCadastroEconomico.js";
include_once ( $pgJS );

$stCaminho = CAM_GT_ECONOMICO."PHP/cadastroEconomio/instancias/consultas/";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
$link = Sessao::read( "link" );
if ($_GET["pg"] and  $_GET["pos"]) {
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
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;

//SETA ELEMENTOS DO FILTRO PARA EFETUAR A BUSCA...
if ($_REQUEST['inInscricaoEconomica']) {
    $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica'] );
}
if ($_REQUEST['stCNPJ']) {
    $stCNPJ = str_replace( "/" , "" , $_REQUEST['stCNPJ'] );
    $stCNPJ = str_replace( "." , "" , $stCNPJ             );
    $stCNPJ = str_replace( "-" , "" , $stCNPJ             );
    $obRCEMInscricaoEconomica->obRCGMPessoaJuridica->setCNPJ( $stCNPJ );
}
if ($_REQUEST['stCPF']) {
    $stCPF = str_replace( "." , "" , $_REQUEST['stCPF'] );
    $stCPF = str_replace( "-" , "" , $stCPF             );
    $obRCEMInscricaoEconomica->obRCGMPessoaFisica->setCPF( $stCPF );
}
if ($_REQUEST['stNomeRazaoSocial']) {
    $obRCEMInscricaoEconomica->obRCGM->setNomCGM( $_REQUEST['stNomeRazaoSocial'] );
}
if ($_REQUEST['stChaveAtividade']) {
    $obRCEMInscricaoEconomica->obRCEMAtividade->setValorComposto( $_REQUEST['stChaveAtividade'] );
}
if ($_REQUEST['inCodigoSocio']) {
    $obRCEMInscricaoEconomica->obRCEMSociedade->obRCGM->setNumCGM ( $_REQUEST['inCodigoSocio'] );
}
if ($_REQUEST['inCodigoDomicilio']) {
    $obRCEMInscricaoEconomica->setDomicilioFiscal( $_REQUEST[ 'inCodigoDomicilio'] );
}
if ($_REQUEST['inCodigoNatureza']) {
    $obRCEMInscricaoEconomica->obRCEMNaturezaJuridica->setCodigoNatureza( $_REQUEST['inCodigoNatureza'] );
}

if ($_REQUEST['inNumCGM']) {
    $obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST['inNumCGM'] );
}

if ($_REQUEST['stLicenca']) {
    $temp = explode( "/", $_REQUEST['stLicenca'] );
    $obRCEMInscricaoEconomica->setCodLicenca( $temp[0] );
    $obRCEMInscricaoEconomica->setLicencaExercicio( $temp[1] );
}
$obRCEMInscricaoEconomica->listarInscricaoConsulta( $rsListaInscricao );

$obLista = new Lista;
$obLista->setRecordSet( $rsListaInscricao );
$obLista->setTitulo ("Registros de Inscrição Econômica");
$obLista->setId('lista');

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Inscrição econômica" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome / Razão Social" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Atividade" );
$obLista->ultimoCabecalho->setWidth( 32 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Situação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 13 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "inscricao_economica" );
$obLista->ultimoDado->setAlinhamento( "DIREITA" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_estrutural] [nom_atividade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$stAcao = "Empresa";
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao" , "inscricao_economica"                 );
$obLista->ultimaAcao->setLink  ( $stCadastroEcon."?".Sessao::getId().$stLink."&stAcao=$stAcao" );
$obLista->commitAcao();

$obLista->addAcao();
$stAcao = "Responsavel";
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao"          , "inscricao_economica"        );
$obLista->ultimaAcao->addCampo ( "&inCGMRespContabil"       , "resp_contabil_cgm"          );
$obLista->ultimaAcao->addCampo ( "&inProfissaoRespContabil" , "resp_contabil_profissao"    );
$obLista->ultimaAcao->setLink  ( $stRespTecnico."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
$obLista->commitAcao();

$obLista->addAcao();
$stAcao = "Atividade";
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao", "inscricao_economica"                );
$obLista->ultimaAcao->setLink  ( $stAtividades."?".Sessao::getId().$stLink."&stAcao=$stAcao" );
$obLista->commitAcao();

$obLista->addAcao();
$stAcao = "Licenca";
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->addCampo ( "&inCodInscricao" , "inscricao_economica"             );
$obLista->ultimaAcao->setLink  ( $stLicencas."?".Sessao::getId().$stLink."&stAcao=$stAcao" );
$obLista->commitAcao();

$obLista->addAcao();
$stAcao = "relatorio";
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink  ( "javascript:relatorio();" );
$obLista->ultimaAcao->addCampo ("1","[inscricao_economica]");
$obLista->ultimaAcao->addCampo ("2","situacao");
$obLista->commitAcao();

$obLista->show();

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CEM_INSTANCIAS."relatorios/OCCadastroEconomico.php" );

$obFormulario = new Formulario;
$obFormulario->addHidden ( $obHdnCaminho );
$obFormulario->setAjuda  ( "UC-05.02.21" );
$obFormulario->show();

?>
