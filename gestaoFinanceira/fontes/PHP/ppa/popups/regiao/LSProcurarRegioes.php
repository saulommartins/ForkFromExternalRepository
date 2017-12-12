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
    * Página de Listagem de Procura de Regioes
    * Data de Criação   : 21/06/2004

    * @author Desenvolvedor: Marcio Medeiros

    * @ignore

    * Casos de uso: uc-02.09.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_PPA_MAPEAMENTO."TPPARegiao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma 	= "ProcurarRegioes";
$pgFilt 				= "FL".$stPrograma.".php?" . Sessao::getId();
$pgList 			= "LS".$stPrograma.".php";
$pgOcul 			= "OC".$stPrograma.".php";
$pgJs   			= "JS".$stPrograma.".php";

include_once($pgJs);

$stCaminho   = CAM_GF_PPA_INSTANCIAS."regioes/";

$obTPPARegiao = new TPPARegiao;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

// Definicao dos objetos hidden
$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST[ 'campoNum' ] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST[ 'campoNom' ] );

if ($_REQUEST['inCodigo']) {
    $obRegra->setCodigo( $_REQUEST['inCodigo'] );
    $stLink .= '&inCodigo='.$_REQUEST['inCodigo'];
}

if ($_REQUEST['stNome']) {
    $stFiltro.= " regiao.nome ilike '%".$_REQUEST['stNome']."%' and ";
    $stLink .= '&stNome='.$_REQUEST['stNome'];
}

if ($stFiltro) {
    $stFiltro = ' where '.substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
}

$stOrder = " order by regiao.cod_regiao";

$obTPPARegiao->recuperaRegioes( $rsLista, $stFiltro, $stOrder );

$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");// pk
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome ");
$obLista->ultimoCabecalho->setWidth( 75 );
$obLista->commitCabecalho();

// Cabeçalho para a ação
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_regiao" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nome" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink("JavaScript:insereRegiao()");
$obLista->ultimaAcao->addCampo('1',	'cod_regiao');
$obLista->ultimaAcao->addCampo('2',	'nome');
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario();

$obBtnCancelar = new Button();
$obBtnCancelar->setName( 'cancelar' );
$obBtnCancelar->setValue( 'Cancelar' );
$obBtnCancelar->obEvento->setOnClick( "window.close();" );

$obBtnFiltro = new Button();
$obBtnFiltro->setName( 'filtro' );
$obBtnFiltro->setValue( 'Filtro' );
$obBtnFiltro->obEvento->setOnClick( "Javascript:history.back(-1);" );

$obFormulario->defineBarra( array( $obBtnCancelar,$obBtnFiltro ) , '', '' );
$obFormulario->addHidden($obHdnForm);
$obFormulario->addHidden($obHdnCampoNum);
$obFormulario->addHidden($obHdnCampoNom);

$obFormulario->show();

?>
