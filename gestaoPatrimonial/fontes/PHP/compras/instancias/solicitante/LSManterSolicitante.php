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
    * Arquivo de lista para consulta de Solicitantes
    * Data de Criação: 11/02/2008

    * @author Analista: Gelson W
    * @author Luiz Felipe Prestes Teixeira

    * Casos de uso: uc-03.04.34

    $Id: LSManterSolicitante.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_COM_MAPEAMENTO."TComprasSolicitante.class.php");

$stPrograma = "ManterSolicitante";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_COM_INSTANCIAS."solicitante/";

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        #sessao->transf4['filtro'][$stCampo] = $stValor;
        $filtro[$stCampo] = $stValor;
    }
    #sessao->transf4['pg']  = $_GET['pg'] ? $_GET['pg'] : 0;
    #sessao->transf4['pos'] = $_GET['pos']? $_GET['pos'] : 0;
    #sessao->transf4['paginando'] = true;
    $pg  = $_GET['pg'] ? $_GET['pg']  : 0;
    $pos = $_GET['pos']? $_GET['pos'] : 0;
    $paginando = true;
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

Sessao::write('pg' , $pg);
Sessao::write('pos' , $pos);
Sessao::write('filtro' , $filtro);

if ( Sessao::read('filtro') ) {
    foreach ( Sessao::read('filtro') as $key => $value ) {
        $_REQUEST[$key] = $value;
    }
}

#sessao->transf4['paginando'] = true;
Sessao::write('paginando' , true);

//seta os cgm_solicitante
if ($_REQUEST['inCodCGMSolicitante'] != '') {
    $stFiltro .= " AND solicitante.solicitante = ".$_REQUEST['inCodCGMSolicitante'];
}

//seta o status
if ($_REQUEST['boAtivo'] == 'true') {
    $stFiltro .= " AND solicitante.ativo = true ";
}

if ($_REQUEST['boAtivo'] == 'false') {
    $stFiltro .= " AND solicitante.ativo = false";
}

$stOrderBy = " ORDER BY nom_cgm";

//recupera os dados do banco de acordo com o filtro
$obTComprasSolicitante = new TComprasSolicitante();
$obTComprasSolicitante->recuperaSolicitantes( $rsSolicitante,$stFiltro,$stOrderBy);

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('uc-03.04.34');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsSolicitante );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "CGM" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Solicitante" );
$obLista->ultimoCabecalho->setWidth( 45 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Status" );
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "solicitante" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo( "ativo" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodCGMSolicitante", "solicitante" );
$obLista->ultimaAcao->addCampo( "&boAtivo" , "ativo" );
$obLista->ultimaAcao->addCampo( "&stNomCGMSolicitante" , "nom_cgm" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[solicitante] - [nom_cgm]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
