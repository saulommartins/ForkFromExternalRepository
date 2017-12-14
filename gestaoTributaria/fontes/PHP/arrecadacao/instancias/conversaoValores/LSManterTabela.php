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
    * Página de Lista de Tablela de Conversão
    * Data de Criacao: 11/09/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Vitor Hugo
    * @ignore

    * $Id: LSManterTabela.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.23
*/

/*
$Log$
Revision 1.1  2007/09/13 13:37:37  vitor
uc-05.03.23

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_MAPEAMENTO."TARRTabelaConversao.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterTabela";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/instancias/conversaoValores/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$rsLista    = new RecordSet;
$link = Sessao::read( "link" );
//MANTEM FILTRO E PAGINACAO
if ($_REQUEST["pg"] and  $_REQUEST["pos"]) {
    $link["pg"]  = $_REQUEST["pg"];
    $link["pos"] = $_REQUEST["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
    $_GET["pg"]  = $_REQUEST["pg"];
    $_GET["pos"] = $_REQUEST["pos"];
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write( "link", $link );

// filtrando
if ($_REQUEST["inCodTabela"])
    $stCondicao  = " cod_tabela = ".$_REQUEST['inCodTabela']." AND ";
if ($_REQUEST["stDescricao"])
    $stCondicao .= " nome_tabela ilike '%".$_REQUEST['stDescricao']."%' AND ";
if ($_REQUEST["stExercicio"])
    $stCondicao .= " exercicio = '".$_REQUEST['stExercicio']."' AND ";

    $stCondicao = ($stCondicao) ? " WHERE " . substr($stCondicao, 0, strlen($stCondicao)-4) : "";

    $rsListaTabelaConversao = new RecordSet();

    $obTabelaConversao = new TARRTabelaConversao();
    $obTabelaConversao->recuperaListaTabelaConversao( $rsListaTabelaConversao, $stCondicao ,' ORDER BY cod_tabela, exercicio' );

$stLink = $_REQUEST["stLink"];
//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsListaTabelaConversao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercicio" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_tabela"  );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nome_tabela" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio"   );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodTabela"     , "cod_tabela"     );
    $obLista->ultimaAcao->addCampo("&stDescricao"     , "nome_tabela"     );
    $obLista->ultimaAcao->addCampo("&stExercicio"     , "exercicio" );
    $obLista->ultimaAcao->addCampo("&inCodModulo"     , "cod_modulo" );
    $obLista->ultimaAcao->addCampo("&stParametro1"    , "parametro_1"    );
    $obLista->ultimaAcao->addCampo("&stParametro2"    , "parametro_2"    );
    $obLista->ultimaAcao->addCampo("&stParametro3"    , "parametro_3"    );
    $obLista->ultimaAcao->addCampo("&stParametro4"    , "parametro_4"    );
    $obLista->ultimaAcao->setLink($pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
} elseif ($stAcao == "excluir") {
    $obLista->ultimaAcao->setAcao( $stAcao );

    $obLista->ultimaAcao->addCampo("&inCodTabela"     , "cod_tabela"     );
    $obLista->ultimaAcao->addCampo("&stDescricao"     , "nome_tabela"     );
    $obLista->ultimaAcao->addCampo("&stExercicio"     , "exercicio" );
    $obLista->ultimaAcao->addCampo("&inCodModulo"     , "cod_modulo" );
    $obLista->ultimaAcao->addCampo("&stParametro1"    , "parametro_1"    );
    $obLista->ultimaAcao->addCampo("&stParametro2"    , "parametro_2"    );
    $obLista->ultimaAcao->addCampo("&stParametro3"    , "parametro_3"    );
    $obLista->ultimaAcao->addCampo("&stParametro4"    , "parametro_4"    );
    $obLista->ultimaAcao->addCampo("&stDescQuestao"   , "[nome_tabela] - [exercicio]" );
    $obLista->ultimaAcao->setLink($stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao."&stCtrl=excluirGrupo" );
}
$obLista->commitAcao();
$obLista->show();
?>
