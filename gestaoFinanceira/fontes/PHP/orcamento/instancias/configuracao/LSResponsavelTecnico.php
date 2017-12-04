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
    * Página de listagem Responsável Técnico
    * Data de Criação   : 15/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2008-03-31 15:00:22 -0300 (Seg, 31 Mar 2008) $

    * Casos de uso: uc-02.01.01
*/

/*
$Log$
Revision 1.5  2006/07/05 20:42:45  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php"   );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ResponsavelTecnico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GF_ORC_INSTANCIAS ."configuracao/";

/**
    * Instância o OBJETO da regra de negócios RResponsavelTecnico
*/
$obRResponsavel = new RCEMResponsavelTecnico;

/**
    * Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

/**
    * Define arquivos PHP para cada acao
*/
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'baixar' : $pgProx = $pgBaix; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

/*
    * Monta sessao com os valores do filtro
*/
$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

if ($_REQUEST['inCodigoProfissao']) {
    $obRResponsavel->obRProfissao->setCodigoProfissao( $_REQUEST['inCodigoProfissao'] );
    $stLink .= '&inCodigoProfissao='.$_REQUEST['inCodigoProfissao'];
}
if ($_REQUEST['inCodigoRegistro']) {
    $obRResponsavel->setNumRegistro( $_REQUEST['inCodigoRegistro'] );
    $stLink .= '&inCodigoRegistro='.$_REQUEST['inCodigoRegistro'];
}
if ($_REQUEST['inCodigoUF']) {
    $obRResponsavel->setCodigoUf( $_REQUEST['inCodigoUF'] );
    $stLink .= '&inCodigoUF='.$_REQUEST['inCodigoUF'];
}
if ($_REQUEST['inNumCGM']) {
    $obRResponsavel->setNumCgm( $_REQUEST['inNumCGM'] );
    $stLink .= '&inNumCGM='.$_REQUEST['inNumCGM'];
}
$stLink .= "&stAcao=".$stAcao;

if ($_GET["pg"] and  $_GET["pos"]) {
    $stLinkPagina = "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
}

$obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );

/**
    * Instância o OBJETO Lista
*/
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsListaResponsavel );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("CGM");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Profissão" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Registro" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_profissao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_registro] - [num_registro] - [nom_uf]" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
//$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->addCampo("&inCodigoCGM","numcgm");
$obLista->ultimaAcao->addCampo("&inCodigoProfissao","cod_profissao");
$obLista->ultimaAcao->addCampo("&stNomeProfissao","nom_profissao");
$obLista->ultimaAcao->addCampo("&stNomeRegistro","nom_registro");
$obLista->ultimaAcao->addCampo("&stNomeUF","nom_uf");
$obLista->ultimaAcao->addCampo("&stDescQuestao","nom_cgm");

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink.$stLinkPagina );
}
$obLista->commitAcao();
$obLista->show();
?>
