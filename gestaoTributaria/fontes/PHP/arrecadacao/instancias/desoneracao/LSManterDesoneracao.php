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
  * Página de Lista de Desoneracao
  * Data de criação : 01/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: LSManterDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.7  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.6  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRDesoneracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/instancias/desoneracao/";

//$stCaminho = "../modulos/arrecadacao/desoneracao/";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'incluir'   : $pgProx = $pgForm; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
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
//MONTAGEM DO FILTRO
$stFiltro = "";
if ($_REQUEST['inCodigoDesoneracao']) {
    $stFiltro .= " DE.cod_desoneracao = ".$_REQUEST['inCodigoDesoneracao']." AND ";
}

if ($_REQUEST['inCodigoTipo']) {
    $stFiltro .= " DE.cod_tipo_desoneracao = ".$_REQUEST['inCodigoTipo']." AND ";
}

if ($_REQUEST['inCodigoCredito']) {
    $arDadosCompostos = explode( ".", $_REQUEST['inCodigoCredito'] );
    $stFiltro .= " CR.cod_credito = ".$arDadosCompostos[0]." AND ";
    $stFiltro .= " CR.cod_especie = ".$arDadosCompostos[1]." AND ";
    $stFiltro .= " CR.cod_genero = ".$arDadosCompostos[2]." AND ";
    $stFiltro .= " CR.cod_natureza = ".$arDadosCompostos[3]." AND ";
}

if ($stFiltro) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, -4 );
}

$obTARRDesoneracao = new TARRDesoneracao;
$obTARRDesoneracao->recuperaDesoneracaoLS( $rsDesoneracao, $stFiltro, " ORDER BY DE.cod_desoneracao " );

$obLista = new Lista;

$obLista->setRecordSet( $rsDesoneracao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Desoneração" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Crédito" );
$obLista->ultimoCabecalho->setWidth( 25 );
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

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodigoDesoneracao"  , "cod_desoneracao"      );
$obLista->ultimaAcao->addCampo("&stDescQuestao"        , "cod_desoneracao"      );
$obLista->ultimaAcao->addCampo("&inCodigoCredito"      , "cod_credito"          );
$obLista->ultimaAcao->addCampo("&inCodigoNatureza"     , "cod_natureza"         );
$obLista->ultimaAcao->addCampo("&inCodigoGenero"       , "cod_genero"           );
$obLista->ultimaAcao->addCampo("&inCodigoEspecie"      , "cod_especie"          );
$obLista->ultimaAcao->addCampo("&stCredito"            , "descricao_credito"    );
$obLista->ultimaAcao->addCampo("&stTipo"               , "descricao_tipo"       );
$obLista->ultimaAcao->addCampo("&inCodigoTipo"         , "cod_tipo_desoneracao" );
$obLista->ultimaAcao->addCampo("&dtInicio"             , "data_inicio"          );
$obLista->ultimaAcao->addCampo("&dtTermino"            , "data_termino"         );
$obLista->ultimaAcao->addCampo("&dtExpiracao"          , "data_expiracao"       );
$obLista->ultimaAcao->addCampo("&inCodigoFundamentacao", "fundamentacao_legal"  );
$obLista->ultimaAcao->addCampo("&inCodigoFormula"      , "cod_funcao"           );
$obLista->ultimaAcao->addCampo("&boRevogavel"          , "revogavel"            );
$obLista->ultimaAcao->addCampo("&boProrrogavel"        , "prorrogavel"          );

$obLista->ultimaAcao->addCampo("&inCodigoBiblioteca"   , "cod_biblioteca"       );
$obLista->ultimaAcao->addCampo("&inCodigoModulo"       , "cod_modulo"           );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
