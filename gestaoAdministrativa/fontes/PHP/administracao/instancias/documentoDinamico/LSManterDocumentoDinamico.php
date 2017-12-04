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
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3574 $
$Name$
$Author: lizandro $
$Date: 2005-12-07 15:23:54 -0200 (Qua, 07 Dez 2005) $

Casos de uso: uc-01.03.99
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RDocumentoDinamicoDocumento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDocumentoDinamico";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stCaminho = "../modulos/configuracao/documentoDinamico/";

$obRDocumentoDinamico = new RDocumentoDinamicoDocumento;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}
//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
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

//MONTA O FILTRO
if ($_REQUEST["inCodModulo"]) {
    $obRDocumentoDinamico->obRModulo->setCodModulo( $_REQUEST["inCodModulo"]);
}

$rsListaDocumentos = new RecordSet;
$obRDocumentoDinamico->listarDocumento($rsListaDocumentos );

$obLista = new Lista;

$obLista->setRecordSet( $rsListaDocumentos );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_documento" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_documento" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodDocumento"    , "cod_documento"      );
$obLista->ultimaAcao->addCampo("&stModulo"          , "cod_modulo"         );
$obLista->ultimaAcao->addCampo("&stDocumento"       , "nom_documento"      );
$obLista->ultimaAcao->addCampo("&stTitulo"          , "titulo"             );
$obLista->ultimaAcao->addCampo("&inMargDir"         , "margem_dir"         );
$obLista->ultimaAcao->addCampo("&inMargEsq"         , "margem_esq"         );
$obLista->ultimaAcao->addCampo("&inMargSup"         , "margem_sup"         );
$obLista->ultimaAcao->addCampo("&inFonte"           , "fonte"              );
$obLista->ultimaAcao->addCampo("&inTamFonte"        , "tamanho_fonte"      );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"descricao");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink);
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();

$obLista->show();
?>
