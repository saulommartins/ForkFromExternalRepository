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
    * Página de Formulario de alteracao documento

    * Data de Criação   : 25/07/2008

    * @author Analista      : Heleno Santos
    * @author Desenvolvedor      : Jânio Eduardo
    * @ignore

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_NEGOCIO."RFISDocumento.class.php"                                               );
include_once( CAM_GT_FISCALIZACAO."classes/visao/VFISManterDocumento.class.php"          );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) { $stAcao = "alterar"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$stCaminho   = CAM_GT_FIS_INSTANCIAS."documento/";

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'excluir' : $pgProx = $pgProc; break;
    DEFAULT        : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO* @author Analista      : Jânio Eduardo
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write( 'link', $link );
}

$link = Sessao::read( 'link' );
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write( 'link', $link );
}

$obRegra = new RFISDocumento();
$obVisao = new VFISManterDocumento( $obRegra );

# Filtros da pesquisa
$obRsDocumento = $obVisao->listarDocumento( $_REQUEST );
$obRsDocumento->addFormatacao("nom_documento","STRIPSLASHES");

$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Lista de Documentos' );

$obLista->setRecordSet($obRsDocumento);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 5        );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Fiscalização" );
$obLista->ultimoCabecalho->setWidth   ( 20       );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Documento" );
$obLista->ultimoCabecalho->setWidth   ( 70     );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ativo" );
$obLista->ultimoCabecalho->setWidth   ( 10         );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 10       );
$obLista->commitCabecalho();

////dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA"     );
$obLista->ultimoDado->setCampo      ( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo      ( "nom_documento"  );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo      ( "ativo"    );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao );

$obLista->ultimaAcao->addCampo( "&cod_documento"   ,"cod_documento"               );
$obLista->ultimaAcao->addCampo( "&cod_tipo_fiscalizacao"   ,"cod_tipo_fiscalizacao"       );
$obLista->ultimaAcao->addCampo( "&descricao_fiscalizacao"   ,"descricao"       );
$obLista->ultimaAcao->addCampo( "&uso_interno"   ,"uso_interno"       );
$obLista->ultimaAcao->addCampo( "&nom_documento" ,"nom_documento"             );
$obLista->ultimaAcao->addCampo( "&ativo"        ,"ativo"    );
$obLista->ultimaAcao->addCampo( "&stDescQuestao","[cod_documento] - [nom_documento]" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink            );
}
$obLista->commitAcao();
$obLista->show();
