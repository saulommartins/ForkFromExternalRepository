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
    * Lista para Tipo Baixa (tipo pagamento) de arrecadação
    * Data de Criação   : 10/05/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixera Stephanou
    * @package URBEM
    * @subpackage Arrecadação

    * $Id: LSManterTipoBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.09
*/

/*
$Log$
Revision 1.7  2006/09/15 11:19:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoPagamento.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterTipoBaixa";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stCaminho = CAM_GT_ARR_INSTANCIAS."tipoBaixaManual/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//DEFINE LISTA
$obRegra    = new RARRTipoPagamento;
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
$obRegra->listarTipoPagamento( $rsLista );

$stLink = "&stAcao=".$stAcao;

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
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição Resumida" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_tipo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo"       );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_resumido"       );
$obLista->commitDado();

// Define ACOEs
$obLista->addAcao();
if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo"   , "cod_tipo"     );
    $obLista->ultimaAcao->addCampo("&stNomeTipo"     , "nom_tipo"     );
    $obLista->ultimaAcao->addCampo("&stNomeResumido" , "nom_resumido" );
    $obLista->ultimaAcao->addCampo("&boPagamento"    , "pagamento"    );
    $obLista->ultimaAcao->setLink($pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
} elseif ($stAcao == "excluir") {
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoTipo" , "cod_tipo"               );
    $obLista->ultimaAcao->addCampo("&stNomeTipo"   , "nom_tipo"               );
    $obLista->ultimaAcao->addCampo("&stDescQuestao","[cod_tipo] - [nom_tipo]" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
}

$obLista->commitAcao();

$obLista->show();

?>
