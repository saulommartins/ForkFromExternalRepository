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
  * Página de Lista de Prorrogar/Revogar Desoneracao
  * Data de criação : 05/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSProrrogarDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.4  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.3  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ProrrogarDesoneracao";
$pgForm = "FM".$stPrograma.".php";

$obRARRDesoneracao = new RARRDesoneracao;

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/instancias/desoneracao/";

//$stCaminho = "../modulos/arrecadacao/desoneracao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$link = Sessao::read( "link" );
//MANTEM FILTRO E PAGINACAO
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
//MONTAGEM DO FILTRO
if ($_REQUEST['inCodCGM']) {
    $obRARRDesoneracao->obRCGM->setNumCGM( $_REQUEST['inCodCGM'] );
}
if ($_REQUEST['inCodigoDesoneracao']) {
    $obRARRDesoneracao->setCodigo( $_REQUEST['inCodigoDesoneracao'] );
}
if ($_REQUEST['stAcao'] == 'revogar') {
    $obRARRDesoneracao->setRevogavel( 't' );
}
if ($_REQUEST['stAcao'] == 'prorrogar') {
    $obRARRDesoneracao->setProrrogavel( 't' );
}

$obRARRDesoneracao->listarDesoneracaoCredito2( $rsDesoneracao );

if ($_REQUEST['stAcao'] == 'revogar') {
    $dtdiaHOJE = date ("d/m/Y");
    $arDiaHOJE = explode("/", $dtdiaHOJE);
    $arDados = array();
    $arDesoneracao = $rsDesoneracao->getElementos();
    $inX = 0;
    $inY = 0;
    while ( !$rsDesoneracao->Eof() ) {
        $dtRevogacao = $arDesoneracao[$inY]["data_revogacao"];
        if ($dtRevogacao) {
            $arRevogacao = explode("/", $dtRevogacao);
            if ($arDiaHOJE[0].$arDiaHOJE[1].$arDiaHOJE[2] < $arRevogacao[0].$arRevogacao[1].$arRevogacao[2]) {
                $arDados[$inX] = $arDesoneracao[$inY];
                $inX++;
            }
        } else {
            $arDados[$inX] = $arDesoneracao[$inY];
            $inX++;
        }

        $rsDesoneracao->proximo();
        $inY++;
    }

    $rsDesoneracao->preenche( $arDados );
    $rsDesoneracao->setPrimeiroElemento();
}

$obLista = new Lista;
$obLista->setRecordSet( $rsDesoneracao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Desoneração" );
$obLista->ultimoCabecalho->setWidth( 14 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Crédito" );
$obLista->ultimoCabecalho->setWidth( 14 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Contribuinte" );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_desoneracao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_tipo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_credito" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigoDesoneracao"  , "cod_desoneracao" );
$obLista->ultimaAcao->addCampo("&inNumCGM"             , "numcgm" );
$obLista->ultimaAcao->addCampo("&inOcorrencia"         , "ocorrencia" );
$obLista->ultimaAcao->addCampo("&dtExpiracao"          , "data_expiracao" );
$obLista->ultimaAcao->addCampo("&dtConcessao"          , "data_concessao" );
$obLista->ultimaAcao->addCampo("&dtProrrogacao"        , "data_prorrogacao" );
$obLista->ultimaAcao->addCampo("&dtRevogacao"          , "data_revogacao"       );
$obLista->ultimaAcao->addCampo("&stCGM"                , "[numcgm] - [nom_cgm]"   );
$obLista->ultimaAcao->addCampo("&stDesoneracao"        , "[cod_desoneracao] - [cod_tipo_desoneracao] - [descricao_credito]"   );

$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
