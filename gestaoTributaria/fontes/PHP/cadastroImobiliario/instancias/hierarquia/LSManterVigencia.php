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
    * Página de lista para o cadastro de vigências
    * Data de Criação   : 28/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: LSManterVigencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.02
*/

/*
$Log$
Revision 1.7  2006/09/18 10:30:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterVigencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GT_IMOBILIARIO."instancias/hierarquia/";
//$stCaminho = "../modulos/cadastroImobiliario/hierarquia/";

 //Define arquivos PHP para cada acao
 switch ($_REQUEST['stAcao']) {
     case 'alterar'   : $pgProx = $pgForm; break;
     case 'excluir'   : $pgProx = $pgProc; break;
     DEFAULT          : $pgProx = $pgForm;
 }

 //MANTEM FILTRO E PAGINACAO
 $stLink .= "&stAcao=".$_REQUEST['stAcao'];
 if ($_GET["pg"] and  $_GET["pos"]) {
     $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
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

Sessao::write('link', $link);
Sessao::write('stLink', $stLink);

$obRegra = new RCIMNivel;

$rsLista = new RecordSet;
$obRegra->listarVigencias( $rsLista );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data de Início" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_vigencia" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dtinicio" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo( "&inCodigoVigencia" , "cod_vigencia" );
$obLista->ultimaAcao->addCampo( "&dtDataInicio" , "dtinicio" );
if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"[cod_vigencia]-[dtinicio]");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.02" );
$obFormulario->show();

?>
