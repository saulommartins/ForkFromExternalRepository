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
  * Página de Lista de Convênio
  * Data de criação : 08/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

    * $Id: LSProcurarConvenio.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.04
**/

/*
$Log$
Revision 1.9  2006/09/18 08:47:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GT_MON_NEGOCIO."RMONConvenio.class.php");
include_once(CAM_GT_MON_NEGOCIO."RMONBanco.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRMONConvenio = new RMONConvenio;
$obRMONBanco    = new RMONBanco;

$stFiltro = "";

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&campoNum=".$_REQUEST["campoNum"];
$stLink .= "&campoNom=".$_REQUEST["campoNom"];
$stLink .= "&nomForm= ".$_REQUEST["nomForm"];

//MANTEM FILTRO E PAGINACAO
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink .= "&pg=".$_GET["pg"];
    $stLink .= "&pos=".$_GET["pos"];
}
// FILTRAGEM

if ($_REQUEST["inNumBanco"]) {
    $obRMONConvenio->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
    $stLink .= "&inCodBanco=".$_REQUEST["inNumBanco"];
}

if ($_REQUEST["cmbTipo"]) {
    $obRMONConvenio->setTipoConvenio( $_REQUEST["cmbTipo"] );
    $stLink .= "&inCodTipo=".$_REQUEST["cmbTipo"];
}

if ($_REQUEST["inNumConvenio"]) {
    $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
    $stLink .= "&inNumConvenio=".$_REQUEST['inNumConvenio'];
}

$obRMONConvenio->listarConvenioBanco( $rsConvenios );

$stLink .= "&stAcao=".$stAcao;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro( $stLink );
$obLista->setRecordSet( $rsConvenios );
$obLista->setTitulo("Registros de Convênios");
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Banco ");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Agência ");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo de Convênio ");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Convênio ");
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_banco] [nom_banco]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_agencia" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_convenio" );
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->commitDado();

$obLista->addAcao();
$stAcao = "SELECIONAR";
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true                   );
$obLista->ultimaAcao->setLink  ( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo ("1","num_convenio"      );
//$obLista->ultimaAcao->addCampo ("2","cod_banco"         );

$obLista->commitAcao();
$obLista->show();

//DEFINIÇÃO DE COMPONENTES
$obBtnFiltro = new Button;
$obBtnFiltro->setName              ( "btnFiltrar" );
$obBtnFiltro->setValue             ( "Filtrar"    );
$obBtnFiltro->setTipo              ( "button"     );
$obBtnFiltro->obEvento->setOnClick ( "filtrar();" );
$obBtnFiltro->setDisabled          ( false        );

$botoes = array ($obBtnFiltro);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->defineBarra ( $botoes, 'left','' );
$obFormulario->show();
