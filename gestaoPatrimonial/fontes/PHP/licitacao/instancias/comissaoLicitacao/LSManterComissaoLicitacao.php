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
    * Pagina de formulário para Cadastro de Comissão de licitação
    * Data de Criação   : 28/08/2006
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: LSManterComissaoLicitacao.php 61017 2014-11-28 18:14:03Z carlos.silva $

    * Casos de uso: uc-03.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoTipoComissao.class.php'                                );
include_once ( CAM_GP_LIC_MAPEAMENTO.'TLicitacaoComissao.class.php'                                    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterComissaoLicitacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$arFiltro = Sessao::read('filtro');
//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos',($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$_GET['pg']);
    Sessao::write('pos',$_GET['pos']);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

$obComissao = new TLicitacaoComissao;
$arFiltro = array();

if ($_REQUEST['txtCodComissao']) {
    $arFiltro[] = 'comissao.cod_comissao = ' . $_REQUEST['txtCodComissao'];
}
if ($_REQUEST['stFinalidade']) {
    $arFiltro[] = 'comissao.cod_tipo_comissao = ' . $_REQUEST['stFinalidade'];
}
if (trim($_REQUEST['inCodNorma']) != '') {
    $arFiltro[] = 'comissao.cod_norma = '. $_REQUEST['inCodNorma'];
}

$arFiltroMembro = array();
if ($_REQUEST['inCGM']) {
    $arFiltroMembro[] = 'comissao_membros.numcgm = '.$_REQUEST['inCGM'];
}

if ($_REQUEST['stTipoMembro']) {
    $arFiltroMembro[] = 'comissao_membros.cod_tipo_membro = ' . $_REQUEST['stTipoMembro'];
}

if (trim($_REQUEST['inCodNormaMembro']) != '') {
    $arFiltroMembro[] = 'comissao_membros.cod_norma = ' . $_REQUEST['inCodNormaMembro'];
}

if ($_REQUEST['stSituacao'] == '2') {
    $arFiltroMembro[] = ' ativo = true ';
} elseif ($_REQUEST['stSituacao'] == '3') {
    $arFiltroMembro[] = ' ativo = false ';
}

$stFiltro = '';

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = ' where '.implode( $arFiltro, ' and ' );
}

if ( count( $arFiltroMembro ) > 0 ) {
    $stFiltroMembro = 'comissao.cod_comissao in ( select comissao_membros.cod_comissao from licitacao.comissao_membros where ' .
                       implode( $arFiltroMembro, ' and ') . ' )';
}

if ($stFiltroMembro) {
    if ($stFiltro) {
        $stFiltro = $stFiltro . ' and ' . $stFiltroMembro;
    } else {
        $stFiltro = "where $stFiltroMembro ";
    }
}

$stOrder = " ORDER BY comissao.cod_comissao ";

$obComissao->recuperaRelacionamento( $rsComissoes, $stFiltro, $stOrder );

///// Montando a listagem de comissãoes
$obLista = new Lista;

$obLista->setRecordSet( $rsComissoes );

$obLista->setTitulo('Registros');
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Cód Comissão" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Finalidade Comissão" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nr. Ato Designação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Designação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Término Vigência" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Presidente" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

//if ($_REQUEST['stSituacao'] == 1) {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Status" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
//}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

if ($stAcao == 'consultar') {

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo( "&cod_comissao",  "cod_comissao"            );
    $obLista->ultimaAcao->addCampo( "cod_tipo_comissao", "cod_tipo_comissao"    );
    $obLista->ultimaAcao->addCampo( "cod_norma"        , "cod_norma"            );
    $obLista->ultimaAcao->setLink ( $pgForm ."?stAcao=".$stAcao."&".Sessao::getId() );
    $obLista->commitAcao();
} else {

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( 'alterar' );
    $obLista->ultimaAcao->addCampo( "&cod_comissao"    , "cod_comissao"      );
    $obLista->ultimaAcao->addCampo( "cod_tipo_comissao", "cod_tipo_comissao" );
    $obLista->ultimaAcao->addCampo( "cod_norma"        , "cod_norma"         );

    $obLista->ultimaAcao->setLink( $pgForm ."?stAcao=alterar&".Sessao::getId() );
    $obLista->commitAcao();

    $stCaminho   = CAM_GP_LIC_INSTANCIAS . 'comissaoLicitacao/' ;
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao ( 'ativoinativo' );
    $obLista->ultimaAcao->addCampo( "&cod_comissao",  "cod_comissao" );
    $obLista->ultimaAcao->addCampo( "cod_norma"        , "cod_norma" );
    $obLista->ultimaAcao->addCampo( "stDescQuestao",  "cod_comissao" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc ."?stAcao=excluir&".Sessao::getId() );
    $obLista->commitAcao();

}

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_comissao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "finalidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_norma]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_publicacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_termino" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "presidente" );
$obLista->commitDado();

//if ($_REQUEST['stSituacao'] == 1) {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "status" );
    $obLista->commitDado();
//}

$obLista->show();

?>
