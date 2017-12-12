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
    * Data de Criação: 10/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: LSManterMotorista.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaMotorista.class.php");

$stPrograma = "ManterMotorista";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."motorista/";

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        #sessao->transf4['filtro'][$stCampo] = $stValor;
        $filtro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg']  : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

if ( Sessao::read('filtro') ) {
    foreach ( Sessao::read('filtro') as $key => $value ) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando' , true);

//seta os filtros
if ($stAcao == 'excluir') {
    $stFiltro .= "\n AND NOT EXISTS ( SELECT 1
                                      FROM frota.utilizacao
                                      WHERE utilizacao.cgm_motorista = motorista.cgm_motorista
                                    ) ";
}

//seta os cgm_motorista
if ($_REQUEST['inCodMotorista'] != '') {
    $stFiltro .= "\n AND motorista.cgm_motorista = ".$_REQUEST['inCodMotorista']." ";
}

//seta o nome do motorista
if ($_REQUEST['stHdnNomMotorista'] != '') {
    $stFiltro .= "\n AND sw_cgm.nom_cgm ILIKE '".$_REQUEST['stHdnNomMotorista']."' ";
}

//seta a periodicidade
if ($_REQUEST['stDataInicial'] != '') {
    $stFiltro .= "\n AND sw_cgm_pessoa_fisica.dt_validade_cnh BETWEEN TO_DATE('".$_REQUEST['stDataInicial']."','dd/mm/yyyy') AND TO_DATE( '".$_REQUEST['stDataFinal']."','dd/mm/yyyy') ";
}

//seta a categoria cnh
if ($_REQUEST['inCodAtributosSelecionados'] != '') {
    $stFiltro .= "\n AND sw_cgm_pessoa_fisica.cod_categoria_cnh IN (".implode(',',$_REQUEST['inCodAtributosSelecionados']).")  ";
}

//seta o status
if ($_REQUEST['boStatus'] == 'ativo') {
    $stFiltro .= "\n AND motorista.ativo = true ";
}

if ($_REQUEST['boStatus'] == 'inativo') {
    $stFiltro .= "\n AND motorista.ativo = false ";
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE 1=1 '.$stFiltro;
    //$stFiltro = ' WHERE '.substr($stFiltro,0,-6);
}

//recupera os dados do banco de acordo com o filtro
$obTFrotaMotorista = new TFrotaMotorista();
$obTFrotaMotorista->recuperaMotoristaSintetico( $rsMotorista, $stFiltro,' ORDER BY nom_motorista '  );

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('uc-03.02.11');

$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsMotorista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Motorista" );
$obLista->ultimoCabecalho->setWidth( 80 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ativo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_motorista] - [nom_motorista]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo( "ativo_desc" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodMotorista", "cgm_motorista" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[cgm_motorista] - [nom_motorista]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&pg=".$_REQUEST['pg']."&pos=".Sessao::read('pos') );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
