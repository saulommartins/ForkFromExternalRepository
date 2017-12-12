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
    * Página de Filtro de lista Participante Licitação
    * Data de Criação: 02/03/2014

    * @author Analista: Gelson Wolowski
    * @author Desenvolvedor: Arthur Cruz

    * @ignore

    * Casos de uso: uc-03.05.16

    $Id: FLManterEdital.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TLIC."TLicitacaoParticipante.class.php");

$stPrograma = "ManterHabilitacaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTLicitacaoParticipante = new TLicitacaoParticipante();

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

if (is_array(Sessao::read('link'))) {
    $_REQUEST = Sessao::read('link');
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
}

if ($_REQUEST['stExercicioLicitacao']) {
    $obTLicitacaoParticipante->setDado( 'exercicio', $_REQUEST['stExercicioLicitacao'] );
}

if ( count($_REQUEST['inCodEntidade']) > 0 ) {
    $obTLicitacaoParticipante->setDado( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
}

if ($_REQUEST['inCodModalidade']) {
    $obTLicitacaoParticipante->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
}

if ($_REQUEST['inCodLicitacao']) {
    $obTLicitacaoParticipante->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
}

if ($_REQUEST['stChaveProcesso']) {
    $arProcesso = explode('/', $_REQUEST['stChaveProcesso']);
    $obTLicitacaoParticipante->setDado( 'cod_processo', intval($arProcesso[0]) );
}

if ($_REQUEST['numEdital']) {
    $arEdital = explode('/',$_REQUEST['numEdital']);
    $obTLicitacaoParticipante->setDado( 'num_edital', $arEdital[0] );
}

if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode('/', $_REQUEST['stMapaCompras'] );
    $obTLicitacaoParticipante->setDado( 'cod_mapa', $arMapa[0] );
}

if ($_REQUEST['inCodTipoLicitacao']) {
    $obTLicitacaoParticipante->setDado( 'cod_tipo_licitacao', $_REQUEST['inCodTipoLicitacao'] );
}

if ($_REQUEST['inCodCriterio']) {
    $obTLicitacaoParticipante->setDado( 'cod_criterio', $_REQUEST['inCodCriterio'] );
}

if ($_REQUEST['inCodTipoObjeto']) {
    $obTLicitacaoParticipante->setDado( 'cod_tipo_objeto', $_REQUEST['inCodTipoObjeto'] );
}

if ($_REQUEST['stObjeto']) {
    $obTLicitacaoParticipante->setDado( 'cod_objeto', $_REQUEST['stObjeto'] );
}

if ($_REQUEST['inCodComissao']) {
    $obTLicitacaoParticipante->setDado( 'cod_comissao', $_REQUEST['inCodComissao'] );
}

if ($request->get('inCGM')) {
    $obTLicitacaoParticipante->setDado( 'cgm_fornecedor', $request->get('inCGM') );
}

$obTLicitacaoParticipante->recuperaParticipanteLicitacaoHabilitacaoLista($rsLicitacaoParticipante);

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLicitacaoParticipante );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Licitação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_licitacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [nom_entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_processo]/[exercicio_processo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [descricao]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inNumEdital"          , "num_edital" );
$obLista->ultimaAcao->addCampo("&stExercicio"          , "exercicio"  );
$obLista->ultimaAcao->addCampo("&stNumEdital"          , "[num_edital]/[exercicio]" );
$obLista->ultimaAcao->addCampo("&stExercicioLicitacao" , "exercicio");
$obLista->ultimaAcao->addCampo("&inCodEntidade"        , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inCodModalidade"      , "cod_modalidade");
$obLista->ultimaAcao->addCampo("&inCodLicitacao"       , "cod_licitacao");
$obLista->ultimaAcao->addCampo("&stNomEntidade"        , "nom_entidade");
$obLista->ultimaAcao->addCampo("&stNomModalidade"      , "descricao");

$obLista->ultimaAcao->setLink( $pgForm."?stAcao=".$stAcao.Sessao::getId().$stLink );
    
$obLista->setAjuda("UC-03.05.16");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>