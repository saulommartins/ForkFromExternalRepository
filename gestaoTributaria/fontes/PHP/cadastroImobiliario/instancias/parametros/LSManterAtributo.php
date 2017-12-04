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
    * Página de lista para a configuração de atributos dinâmicos
    * Data de Criação   : 04/03/2004

    * @author Cassiano de Vasconcelos Ferreira

    * @ignore

    * $Id: LSManterAtributo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.01
*/

/*
$Log$
Revision 1.6  2006/09/18 10:31:08  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once(CAM_MAPEAMENTO."TAtributoCIM.class.php");
include_once(CAM_MAPEAMENTO."TRestricaoIntegridade.class.php");
include_once(CAM_MAPEAMENTO."TTipoAtributo.class.php");
include_once(CAM_MAPEAMENTO."TAtributoRestricaoIntegridade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

//DEFINE O CAMINHO DOS ARQUIVOS DO MODULO A PARTIR DO DIRETORIO POPUPS
$stCaminho = "../modulos/CIM/parametros/";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

$obTAtributoCIM = new TAtributoCIM;
$obTAtributoCIM->recuperaRelacionamento( $rsAtributoCIM , "", " ORDER BY COD_TIPO, COD_ATRIBUTO, NOM_ATRIBUTO ");
$obLista = new Lista;

$obLista->setRecordSet( $rsAtributoCIM );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome atributo" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_atributo_tipo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_atributo" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo("&inCodAtributo","cod_atributo");
    $obLista->ultimaAcao->addCampo("inCodTipo","cod_tipo");
    $obLista->ultimaAcao->addCampo("stDescQuestao","nom_atributo");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"] );
} else {
    $obLista->ultimaAcao->addCampo("&inCodAtributo","cod_atributo");
    $obLista->ultimaAcao->addCampo("inCodTipo","cod_tipo");
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"] );
}
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.01.01" );
$obFormulario->show();

?>
