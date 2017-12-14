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
  * Página de Lista de Modalidade
  * Data de criação : 22/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: LSManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.04.07
**/

/*
$Log$
Revision 1.6  2007/09/25 19:32:33  cercato
filtro por tipo de modalidade.

Revision 1.5  2007/07/27 15:01:14  cercato
Bug#9767#

Revision 1.4  2007/04/16 20:48:42  cercato
Bug #9109#

Revision 1.3  2007/02/09 18:29:47  cercato
correcao da lista de modalidade

Revision 1.2  2006/09/26 10:01:05  cercato
correcao do filtro de busca por descricao.

Revision 1.1  2006/09/25 14:56:20  cercato
implementacao dos formularios de acordo com interface abstrata.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );

$stLink = Sessao::read('stLink');
$link = Sessao::read('link');

//Define o nome dos arquivos PHP
$stPrograma = "ManterModalidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgForm2= "FM".$stPrograma."Divida.php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = "../../../../../../gestaoTributaria/fontes/PHP/dividaAtiva/instancias/modalidade/";

include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'   : $pgProx = $pgForm2; break;
    case 'excluir'   : $pgProx = $pgProc; break;
    DEFAULT          : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
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

Sessao::write('link'  , $link);
Sessao::write('stLink', $stLink);

//MONTAGEM DO FILTRO
$stFiltro = ' dm.ativa = true AND ';
if ($_REQUEST['inCodigo']) {
    $stFiltro .= " \n dm.cod_modalidade = '".$_REQUEST['inCodigo']."' AND ";
}

if ($_REQUEST['stDescricao']) {
    $stFiltro .= " \n dm.descricao LIKE '%".$_REQUEST['stDescricao']."%' AND ";
}

if ($_REQUEST["cmbTipo"]) {
    $stFiltro .= " \n dmv.cod_tipo_modalidade = ".$_REQUEST["cmbTipo"]." AND ";
}

$stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );

$obTDATModalidade = new TDATModalidade;
$obTDATModalidade->recuperaListaModalidade( $rsModalidade, $stFiltro, " ORDER BY dm.cod_modalidade " );

$obLista = new Lista;
$obLista->setRecordSet( $rsModalidade );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 14 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_modalidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao_tipo_modalidade" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $_REQUEST['stAcao'] );

$obLista->ultimaAcao->addCampo( "&inCodModalidade", "cod_modalidade" );
$obLista->ultimaAcao->addCampo( "&stDescricao", "descricao" );
$obLista->ultimaAcao->addCampo( "&inCodFormaInscricao", "cod_forma_inscricao" );
$obLista->ultimaAcao->addCampo( "&stVigenciaInicial", "vigencia_inicial" );
$obLista->ultimaAcao->addCampo( "&stVigenciaFinal", "vigencia_final" );
$obLista->ultimaAcao->addCampo( "&inCodFuncao", "cod_funcao" );
$obLista->ultimaAcao->addCampo( "&inCodBiblioteca", "cod_biblioteca" );
$obLista->ultimaAcao->addCampo( "&inCodModulo", "cod_modulo" );
$obLista->ultimaAcao->addCampo( "&inCodNorma", "cod_norma" );
$obLista->ultimaAcao->addCampo( "&inCodTipoModalidade", "cod_tipo_modalidade" );
$obLista->ultimaAcao->addCampo( "&stTimeStamp", "timestamp" );
$obLista->ultimaAcao->addCampo( "&stTipoModalidade", "descricao_tipo_modalidade" );
$obLista->ultimaAcao->addCampo( "stDescQuestao", "[cod_modalidade] - [descricao]");

if ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
