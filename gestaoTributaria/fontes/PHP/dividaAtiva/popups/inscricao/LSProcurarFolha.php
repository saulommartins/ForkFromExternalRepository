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
  * Página de Lista de Inscricao
  * Data de criação : 29/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSProcurarInscricao.php 29252 2008-04-16 14:25:51Z fabio $

  Caso de uso: uc-05.04.02
**/

/*
$Log$
Revision 1.3  2007/08/08 21:43:14  cercato
correcao no filtro da inscricao.

Revision 1.2  2007/04/04 15:04:00  dibueno
*** empty log message ***

Revision 1.1  2006/09/29 10:45:38  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarFolha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );
$stLink .= "&campoNom=".$_REQUEST['campoNom'];
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&nomForm=".$_REQUEST['nomForm'];

//MANTEM FILTRO E PAGINACAO
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = "";
if ($_REQUEST['inLivro']) {
    $stFiltro .= " \n num_livro  = '".$_REQUEST['inLivro']."' AND ";
}

//-- ( $_REQUEST['stExercicio'] ){
  //  $stFiltro .= " \n exercicio = '".$_REQUEST['stExercicio']."' AND ";
//}

if ($_REQUEST['inPagina']) {
    $stFiltro .= " \n num_folha  = '".$_REQUEST['inPagina']."' AND ";
}

if ( $stFiltro )
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$stOrdem = " GROUP BY num_folha, num_livro ORDER BY num_livro, num_folha ";
$obTDATDividaAtiva = new TDATDividaAtiva;
$obTDATDividaAtiva->recuperaFolhaPopUP( $rsLivro, $stFiltro, $stOrdem );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsLivro );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Livro");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Página" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
/*
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercicio" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
*/
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_livro]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_folha]" );
$obLista->commitDado();

//$obLista->addDado();
//$obLista->ultimoDado->setCampo( "[exercicio]" );
//$obLista->commitDado();

$_REQUEST['stAcao'] = "SELECIONAR";

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
//$obLista->ultimaAcao->addCampo("1","num_livro");
$obLista->ultimaAcao->addCampo("1","num_folha");
$obLista->commitAcao();
$obLista->show();

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnNumLivro = new Hidden;
$obHdnNumLivro->setName     ( "campoNum");
$obHdnNumLivro->setValue    ( $_REQUEST['campoNum'] );

$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ( $obBtnFiltro );

$obFormulario = new Formulario;
$obFormulario->addHidden($obHdnCampoNom);
$obFormulario->addHidden($obHdnNumLivro);
$obFormulario->defineBarra ( $botoes, 'left', '' );
$obFormulario->show();
