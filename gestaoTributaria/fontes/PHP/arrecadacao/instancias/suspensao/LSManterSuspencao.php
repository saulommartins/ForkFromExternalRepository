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
   * Pagina de processamento para Suspenção
   * Data de Criação   : 30/10/2006
   * @author Analista: Fabio Bertoldi Rodrigues
   * @author Desenvolvedor: Márson Luís Oliveira de Paula

    * $Id: LSManterSuspencao.php 63839 2015-10-22 18:08:07Z franver $

   * Casos de uso: uc-05.03.08
*/

/*
$Log$
Revision 1.7  2006/11/22 10:05:23  marson
Adição do caso de uso de Suspensão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRSuspensao.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterSuspensao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//include_once( $pgJs );

//DEFINE LISTA
$obSuspensao    = new RARRSuspensao;
$rsLista    = new RecordSet;

//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
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

// CGM
if( $_REQUEST["inCGM"] )
    $obSuspensao->setCGM( $_REQUEST["inCGM"] );
// Grupo de Crédito
if ($_REQUEST["inCodGrupo"])
    $obSuspensao->setCodGrupo($_REQUEST["inCodGrupo"]);
// Crédito
if ($_REQUEST["inCodCredito"])
    $obSuspensao->obRMONCredito->setCodCredito($_REQUEST["inCodCredito"]);
// Código Tipo Suspensao
if ($_REQUEST["inCodigoTipoSuspensao"])
    $obSuspensao->obRARRTipoSuspensao->setCodigoTipoSuspensao($_REQUEST["inCodigoTipoSuspensao"]);

if ($stAcao == "incluir") {
    $obSuspensao->listarSuspensaoLancamento( $rsLista );
} else {
    $obSuspensao->listarSuspensao( $rsLista );
}

if( $rsLista->getNumLinhas() > 0 )
    $rsLista->setPrimeiroElemento();

$stLink = "&stAcao=".$stAcao;

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

if ($stAcao=="incluir") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Lançamento");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Origem da Cobrança" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Inscrição" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Dados Complementares" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

} else {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código ");
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo Suspensão" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

if ($stAcao=="incluir") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_lancamento" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "proprietarios" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "origemcobranca" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inscricao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dados_complementares" );
    $obLista->commitDado();
} else {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_suspensao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_tipo_suspensao] - [descricao]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "proprietarios" );
    $obLista->commitDado();
}

$obLista->addAcao();
// Define ACOEs
if ($stAcao == "incluir") {
    $obLista->ultimaAcao->setAcao( 'suspender' );
    $obLista->ultimaAcao->addCampo("&inCodLancamento" , "cod_lancamento" );
    $obLista->ultimaAcao->addCampo("&stCGM"           , "proprietarios"  );
    $obLista->ultimaAcao->addCampo("&stGrupoCredito"  , "grupocredito"   );
    $obLista->ultimaAcao->addCampo("&stCredito"       , "credito"        );
} else {
    $obLista->ultimaAcao->setAcao( 'alterar' );
    $obLista->ultimaAcao->addCampo("&inCodigoTipoSuspensao" , "cod_tipo_suspensao"      );
}
$obLista->ultimaAcao->setLink($pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );

$obLista->commitAcao();

$obLista->show();

?>
