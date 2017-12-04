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
    * Página de lista para Validar simulação de cálculos
    * Data de Criação   : 21/10/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: André Machado

    * @ignore

    * $Id: LSLancarTransferencia.php 32939 2008-09-03 21:14:50Z domluc $

    * Casos de uso: uc-05.03.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ValidarCalculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js" ;
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PRManterCalculo.php?".$stAcao;

include_once($pgJs);
/**
    *Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/

$stAcao = 'validar';//$_REQUEST["stAcao"];

$stLink = "";

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$stAcao;
//$stLink .= "&stTipo=".$_REQUEST["stTipo"];
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

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obRARRCalculo = new RARRCalculo();

if ($_REQUEST['inCodGrupo']) {
    $argrupo   = explode( '/', $_REQUEST['inCodGrupo'] );
    $stFiltro  = " AND cod_grupo = ".$argrupo[0];
    $stFiltro .= " AND exercicio = '".$argrupo[1]."'";
}

$obRARRCalculo->listarGruposSimulacao($rsGrupos, $stFiltro);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsGrupos );

$obLista->setTitulo ("Lista de Grupos com cálculos simulados");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Grupo" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código/Exercício" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Validar" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

// campos codigo e logradouro sao montados no SQL
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_grupo]/[ano_exercicio]" );
$obLista->commitDado();

$obChkIncluir = new Checkbox;
$obChkIncluir->setName                        ( "boIncluir");
$obChkIncluir->setChecked                     ( false         );
$obChkIncluir->setValue                       ( "[cod_grupo]/[ano_exercicio]" );

$obLista->addDadoComponente                    ( $obChkIncluir );
$obLista->ultimoDado->setAlinhamento           ( 'CENTRO' );
$obLista->commitDadoComponente                 ();

//$obLista->show();

$obLista->montaHtml();
$obListaSpan = new Span;
$obListaSpan->setId( 'listaspn' );
$obListaSpan->setvalue( $obLista->getHtml() );

$obForm = new Form();
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addSpan   ( $obListaSpan );
$obFormulario->setAjuda  ( "UC-05.01.09" );
$obFormulario->Ok();
$obFormulario->show();

?>
