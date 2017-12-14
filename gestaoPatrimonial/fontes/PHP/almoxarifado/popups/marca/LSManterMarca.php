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
    * Página de Listagem da Marca
    * Data de Criação   : 05/05/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso :uc-03.03.03

    $Id: LSManterMarca.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoMarca.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterMarca";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

  $stFncJavaScript  = " function insereMarca(num,nom) {  \n";
  $stFncJavaScript .= " var sNum;                  \n";
  $stFncJavaScript .= " var sNom;                  \n";
  $stFncJavaScript .= " sNum = num;                \n";
  $stFncJavaScript .= " sNom = nom;                \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".value = sNum; \n";
  $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"  ].".focus(); \n";
  $stFncJavaScript .= " window.close();            \n";
  $stFncJavaScript .= " }                          \n";

$stCaminho = CAM_GP_ALM_INSTANCIAS."marca/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}

$obRegra = new RAlmoxarifadoMarca;
$rsLista = new RecordSet;
$stLink = isset($stLink) ? $stLink : null;
if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ( $request->get('inCodigo') ) {
    $obRegra->setCodigo( $_REQUEST['inCodigo'] );
    $stLink .= '&inCodigo='.$_REQUEST['inCodigo'];
}
if ( $request->get('stHdnDescricao') ) {
    $obRegra->setDescricao( $_REQUEST['stHdnDescricao'] );
    $stLink .= '&stHdnDescricao='.$_REQUEST['stHdnDescricao'];
}

$stLink .= "&stAcao=".$stAcao;

if ( $request->get("pg") and $request->get("pos") ) {
    #sessao->link["pg"]  = $_GET["pg"];
    #sessao->link["pos"] = $_GET["pos"];
    $link["pg"]  = $request->get("pg");
    $link["pos"]  = $request->get("pos");
    Sessao::write('link', $link);
} elseif ( is_array(Sessao::read('link')) ) {
    $_GET = Sessao::read('link');
    $_REQUEST = Sessao::read('link');
    Sessao::write('link', '');
} else {
    foreach ($_REQUEST as $key => $valor) {
        #sessao->link[$key] = $valor;
        $link[$key] = $valor;
    }
    Sessao::write('link', $link);
}

$stOrder = " ORDER BY lower(descricao) ";
$obRegra->listar($rsLista, $stOrder );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Marcas cadastradas");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 70 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_marca" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink  ( "JavaScript:insereMarca();" );
$obLista->ultimaAcao->addCampo ("1","cod_marca");
$obLista->ultimaAcao->addCampo ("2","descricao");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();

?>
