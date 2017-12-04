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
    * Pagina de processamento para Grupos de Credito
    * Data de Criação   : 25/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Texeira Stephanou

    * $Id: LSManterGrupo.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.7  2007/03/07 21:52:54  rodrigo
Bug #8439#

Revision 1.6  2006/10/19 18:01:16  cercato
correcao da formatacao da lista do grupo de creditos e da lista de creditos por grupo.

Revision 1.5  2006/09/15 11:10:42  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterGrupo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/instancias/grupoCreditos/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRegra    = new RARRGrupo;
$rsLista    = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
if ($_REQUEST["pg"] and  $_REQUEST["pos"]) {
    $link["pg"]  = $_REQUEST["pg"];
    $link["pos"] = $_REQUEST["pos"];
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
// filtrando
if ($_REQUEST["inCodGrupo"])
    $obRegra->setCodGrupo($_REQUEST["inCodGrupo"]);
if ($_REQUEST["stDescricao"])
    $obRegra->setDescricao($_REQUEST["stDescricao"]);
if ($_REQUEST["stExercicio"])
    $obRegra->setExercicio($_REQUEST["stExercicio"]);

$stMascara = "";
$obRegra->RecuperaMascaraGrupoCredito( $stMascara );
$inTamanhoMascara = strlen( $stMascara );
($stAcao=="excluir") ? $obRegra->listarCalculoGrupos( $rsLista ) : $obRegra->listarGrupos( $rsLista );
$arDados = $rsLista->getElementos();
for ( $inX=0; $inX<$rsLista->getNumLinhas(); $inX++ ) {
    $arDados[$inX]["cod_grupo_exercicio"] = sprintf("%0".$inTamanhoMascara."d/%d", $arDados[$inX]["cod_grupo"], $arDados[$inX]["ano_exercicio"]);
}

if ( $rsLista->getNumLinhas() > 0 ) {
    $rsLista->preenche( $arDados );
    $rsLista->setPrimeiroElemento();
}

$stLink .= "&stAcao=".$stAcao;

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 65 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_grupo_exercicio" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao"       );
$obLista->commitDado();
// Define ACOEs
$obLista->addAcao();
if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodGrupo"      , "cod_grupo"     );
    $obLista->ultimaAcao->addCampo("&stDescricao"     , "descricao"     );
    $obLista->ultimaAcao->addCampo("&stExercicio"     , "ano_exercicio" );
    $obLista->ultimaAcao->addCampo("&inCodigoModulo"  , "cod_modulo"    );
    $obLista->ultimaAcao->addCampo("&inCodDes"        , "cod_des"    );
    $obLista->ultimaAcao->addCampo("&stDesDesc"       , "func_des"    );
    $obLista->ultimaAcao->setLink($pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
} elseif ($stAcao == "excluir") {
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodGrupo"      , "cod_grupo"     );
    $obLista->ultimaAcao->addCampo("&stDescricao"     , "descricao"     );
    $obLista->ultimaAcao->addCampo("&stExercicio"     , "ano_exercicio" );
    $obLista->ultimaAcao->addCampo("&boCalendario"    , "calendario"    );
    $obLista->ultimaAcao->addCampo("&stDescQuestao"   , "[cod_grupo] - [descricao]/[ano_exercicio]" );
    $obLista->ultimaAcao->setLink($stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao."&stCtrl=excluirGrupo" );
}

$obLista->commitAcao();

$obLista->show();

?>
