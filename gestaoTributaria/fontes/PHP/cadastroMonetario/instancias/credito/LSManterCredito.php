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
    * Página de Formulario de Inclusao/Alteracao de CREDITOS

    * Data de Criação   : 23/12/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: LSManterCredito.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.05.10

*/

/*
$Log$
Revision 1.17  2007/08/14 19:59:11  vitor
Ajustes em: Tesouraria :: Configuração :: Classificar Receitas

Revision 1.15  2007/02/22 13:17:31  rodrigo
Bug #8423#

Revision 1.14  2007/02/08 10:37:40  cercato
alteracoes para o credito trabalhar com conta corrente.

Revision 1.13  2006/09/15 14:57:49  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCredito";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$stCaminho   = CAM_GT_MON_INSTANCIAS."credito/";

$obRMONCredito = new RMONCredito;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}
//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'baixar'   : $pgProx = $pgFormBaixar; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( 'linkCredito' );
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

Sessao::write('stLink', $stLink);
Sessao::write('linkCredito', $link);

//MONTA O FILTRO
if ($_REQUEST["inCodCredito"]) {
    $obRMONCredito->setCodCredito( $_REQUEST['inCodCredito'] );
}
if ($_REQUEST["stDescricao"]) {
    $obRMONCredito->setDescricao( $_REQUEST["stDescricao"] );
}
if ($_REQUEST["inCodEspecie"]) {
    $obRMONCredito->setCodEspecie( $_REQUEST["inCodEspecie"] );
}

$stLink .= "&stAcao=".$_REQUEST['stAcao'];
$obRMONCredito->listarCreditos($rsLista);

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Registros de Créditos");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Descrição");
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Espécie");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_credito" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_credito" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_especie] - [nom_especie]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );
$obLista->ultimaAcao->addCampo("&inCodCredito", "cod_credito" );
$obLista->ultimaAcao->addCampo("&stDescricao",  "descricao_credito");
$obLista->ultimaAcao->addCampo("&inCodEspecie", "cod_especie" );
$obLista->ultimaAcao->addCampo("&stNomEspecie", "nom_especie" );
$obLista->ultimaAcao->addCampo("&inCodNatureza", "cod_natureza" );
$obLista->ultimaAcao->addCampo("&stNomNatureza", "nom_natureza" );
$obLista->ultimaAcao->addCampo("&inCodGenero", "cod_genero" );
$obLista->ultimaAcao->addCampo("&stNomGenero", "nom_genero" );
$obLista->ultimaAcao->addCampo("&inCodConvenio", "cod_convenio" );
$obLista->ultimaAcao->addCampo("&inCodConta", "cod_conta_corrente" );
$obLista->ultimaAcao->addCampo("&inCodGrupo", "cod_grupo" );
$obLista->ultimaAcao->addCampo("&inCodModulo", "cod_modulo" );
$obLista->ultimaAcao->addCampo("&inCodFuncao", "cod_funcao" );
$obLista->ultimaAcao->addCampo("&inCodBiblioteca", "cod_biblioteca" );
$obLista->ultimaAcao->addCampo("&stNomFuncao", "nom_funcao" );
$obLista->ultimaAcao->addCampo("&stDescQuestao","[cod_credito]-[descricao_credito]");

if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.05.10" );
$obFormulario->show();
