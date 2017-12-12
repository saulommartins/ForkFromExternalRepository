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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4642 $
$Name$
$Author: cassiano $
$Date: 2006-01-04 10:33:50 -0200 (Qua, 04 Jan 2006) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgBaix = "FMBaixarEdificio.php";

//$stCaminho = "../modulos/configuracao/atributo/";
$stCaminho = CAM_GA."PHP/administracao/instancias/atributo/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('link_pg',$_GET["pg"]);
    Sessao::write('link_pos',$_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
} else {
    $arLink = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('link',$arLink);
}

if ($stAcao == "alterar") {
    $pgProx = $pgForm;
} elseif ($stAcao == "baixar") {
    $pgProx = $pgBaix;
} else {
    $pgProx = $pgProc;
}

$pgProx = $pgProx."?".Sessao::getId()."&stAcao=".$stAcao;

$obRAtributoDinamico = new RAtributoDinamico;
$obRAtributoDinamico->obRModulo->setCodModulo ( $_REQUEST['inCodModulo']   );
$obRAtributoDinamico->setCodCadastro          ( $_REQUEST['inCodCadastro'] );
$obRAtributoDinamico->listar( $rsLista, " ORDER BY cod_atributo, nom_atributo" );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ativo" );
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_atributo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_atributo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "stativo" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&inCodModulo",  "cod_modulo"  );
    $obLista->ultimaAcao->addCampo("&inCodAtributo","cod_atributo");
    $obLista->ultimaAcao->addCampo("&inCodCadastro","cod_cadastro");
    $obLista->ultimaAcao->addCampo("stNomAtributo","nom_atributo");
    $obLista->ultimaAcao->addCampo("inCodTipo","cod_tipo");
    $obLista->ultimaAcao->addCampo("stDescQuestao","nom_atributo");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx );
} else {
    $obLista->ultimaAcao->addCampo("&inCodGestao=".$_REQUEST['inCodGestao']."&", "" );
    $obLista->ultimaAcao->addCampo("&inCodModulo",  "cod_modulo"  );
    $obLista->ultimaAcao->addCampo("&inCodAtributo","cod_atributo");
    $obLista->ultimaAcao->addCampo("&inCodCadastro","cod_cadastro");
    $obLista->ultimaAcao->addCampo("&stNomTipo",    "nom_tipo"    );
    $obLista->ultimaAcao->addCampo("inCodTipo","cod_tipo");
    $obLista->ultimaAcao->setLink( $pgProx );
}
$obLista->commitAcao();
$obLista->show();
?>
