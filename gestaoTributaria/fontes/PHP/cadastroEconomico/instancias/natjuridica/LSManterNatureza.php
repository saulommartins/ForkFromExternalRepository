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
    * Página de Listagem de Itens
    * Data de Criação   : 11/04/2005

    * Data de Criação   : 13/04/2005

    * @author Fernando Zank Correa Evangelista
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: LSManterNatureza.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.02.08

*/

/*
$Log$
Revision 1.8  2007/02/27 13:48:57  cassiano
Bug #8434#

Revision 1.7  2006/09/15 14:33:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterNatureza";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgFormBaixar = "FM".$stPrograma."Baixar.php";
$stCaminho   = CAM_GT_CEM_INSTANCIAS."natjuridica/";

$obRCEMNatureza = new RCEMNaturezaJuridica;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'baixar'   : $pgProx = $pgFormBaixar; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$stAcao;
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

Sessao::write( "link", $link );

//MONTA O FILTRO
if ($_REQUEST["inCodigoNatureza"]) {
    //RETIRA O HIFEN DA MASCARA DO CODIGO NATUREZA
    $arTmpCodigo = explode("-", $_REQUEST['inCodigoNatureza']);
    $obRCEMNatureza->setCodigoNatureza( $arTmpCodigo[0].$arTmpCodigo[1] );
}

if ($_REQUEST["stNomeNatureza"]) {
    //RETIRA O PONTO FINAL DO VALOR COMPOSTO CASO EXISTA
    $obRCEMNatureza->setNomeNatureza( $_REQUEST["stNomeNatureza"] );
}

$stLink .= "&stAcao=".$stAcao;
if ($stAcao == "baixar") {
    $obRCEMNatureza->listarNaturezaJuridica($rsLista,"",true);
} else {
    $obRCEMNatureza->listarNaturezaJuridica($rsLista);
}

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Registros de Natureza Jurídica");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome ");
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_natureza" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_natureza" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&stNomeNatureza",       "nom_natureza"        );
$obLista->ultimaAcao->addCampo("&inCodigoNatureza",       "cod_natureza"        );
$obLista->ultimaAcao->addCampo("&stDescQuestao","[cod_natureza]-[nom_natureza]");
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.02.08" );
$obFormulario->show();

?>
