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
    * Lista para Popup Responsavel Tecnico
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * $Id: LSProcurarResponsavel.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.11  2006/09/15 13:50:37  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"     );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"                 );
include_once ( CAM_GA_CSE_NEGOCIO."RConselho.class.php"                  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"           );
include_once ( CAM_GA_ADM_NEGOCIO."RAdministracaoUF.class.php"           );

//Define o nome dos arquivos PHP
$stPrograma    = "ProcurarResponsavel";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

$stCaminho = CAM_GT_CEM_INSTANCIAS."resptecnico/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

// INSTANCIA REGRAS UTILIZADAS

$obRConselho                = new RConselho                 ;
$obRCEMResponsavelTecnico   = new RCEMResponsavelTecnico    ;
$obRProfissao               = new RProfissao                ;
$obRUF                      = new RUF                       ;

//MANTEM FILTRO E PAGINACAO
$stLink .= "&nomForm=".$_REQUEST["nomForm"];
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&inCodProfissao=".$_REQUEST["inCodProfissao"];
$stLink .= "&stProfissao=".$_REQUEST["stNomProfissao"];
$stLink .= "&stAcao=".$_REQUEST['stAcao'];

$link = Sessao::read( "link" );

if ($_GET["pg"] and  $_GET["pos"]) {
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

//DEFINICAO DO FILTRO PARA CONSULTA
//$stLink = "";

if ($_REQUEST["inCodigoProfissao"]) {
    $obRCEMResponsavelTecnico->obRProfissao->setCodigoProfissao( $_REQUEST["inCodigoProfissao"] );
    $stLink .= "&inCodigoProfissao=".$_REQUEST["inCodigoProfissao"];
}else
if ( Sessao::read( "arProfissoes" ) ) {
    $obRCEMResponsavelTecnico->setProfissoes( Sessao::read( "arProfissoes" ) );
}

if ($_REQUEST["inNumCGM"]) {
    $obRCEMResponsavelTecnico->setNumCgm( $_REQUEST["inNumCGM"] );
    $stLink .= "&inNumCGM=".$_REQUEST["inNumCGM"];
}
if ($_REQUEST["inNomCGM"]) {
    $obRCEMResponsavelTecnico->setNomCgm( $_REQUEST["inNomCGM"] );
    $stLink .= "&inNomCGM=".$_REQUEST["inNomCGM"];
}
if ($_REQUEST["stRegistro"]) {
    $obRCEMResponsavelTecnico->setCodigoProfissao( $_REQUEST["stRegistro"] );
    $stLink .= "&stRegistro=".$_REQUEST["stRegistro"];
}
if ($_REQUEST["inCodigoUf"]) {
    $obRCEMResponsavelTecnico->setCodigoUF( $_REQUEST["inCodigoUf"] );
    $stLink .= "&inCodigoUf=".$_REQUEST["inCodigoUf"];
}
$obRCEMResponsavelTecnico->listarResponsavelTecnico($rsRespTecnico);

$stLink .= "&stAcao=".$_REQUEST['stAcao'];

Sessao::write('stLink', $stLink);
//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsRespTecnico );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Profissão");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Registro");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_profissao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_registro] [num_registro] [sigla_uf] " );
$obLista->commitDado();

$obLista->addAcao();
$_REQUEST['stAcao'] = 'SELECIONAR';
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1","numcgm");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->ultimaAcao->addCampo("3","nom_profissao");
$obLista->ultimaAcao->addCampo("4","cod_profissao");
$obLista->ultimaAcao->addCampo("5","sequencia");
$obLista->commitAcao();

$obLista->show();

$obHdnNomForm = new Hidden;
$obHdnNomForm->setName( "nomForm" );
$obHdnNomForm->setValue( $_REQUEST["nomForm"] );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST["campoNom"] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST["campoNum"] );

$obHdnProfissao = new Hidden;
$obHdnProfissao->setName ( "stProfissao" );
$obHdnProfissao->setValue( $_REQUEST['stProfissao'] );

$obHdnSequencia = new Hidden;
$obHdnSequencia->setName ( "inSequencia" );
$obHdnSequencia->setValue( $_REQUEST['inSequencia']  );

$obHdnCodProfissao = new Hidden;
$obHdnCodProfissao->setName ( "inCodProfissao" );
$obHdnCodProfissao->setValue( $_REQUEST['inCodProfissao']  );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden( $obHdnNomForm );
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnSequencia);
$obFormulario->addHidden($obHdnProfissao);
$obFormulario->addHidden($obHdnCodProfissao);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();

?>
