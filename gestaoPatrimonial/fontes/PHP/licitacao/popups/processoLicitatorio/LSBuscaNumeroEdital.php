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
    * Pagina de Lista para IPopUpEdital
    * Data de Criação   : 25/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.16

    $Id: LSBuscaNumeroEdital.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( TLIC."TLicitacaoEdital.class.php" );

$stPrograma = "BuscaNumeroEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTLicitacaoEdital = new TLicitacaoEdital();

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$arFiltro = Sessao::read('filtro');

if ($_REQUEST['stExercicioLicitacao']) {
    foreach ($_REQUEST as $key => $value) {
        $arFiltro[$key] = $value;
    }
} else {
    if ($arFiltro) {
        foreach ($arFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

include_once ( $pgJs );

Sessao::write('filtro', $arFiltro);

$stLink = "&stAcao=".$stAcao;

if ($_REQUEST['stExercicioLicitacao']) {
    $obTLicitacaoEdital->setDado( 'exercicio_licitacao', "'".$_REQUEST['stExercicioLicitacao']."'" );
}

if ( count($_REQUEST['inCodEntidade']) > 0 ) {
    $obTLicitacaoEdital->setDado( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
}

if ($_REQUEST['inCodModalidade']) {
    $obTLicitacaoEdital->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
}

if ($_REQUEST['inCodLicitacao']) {
    $obTLicitacaoEdital->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
}

if ($_REQUEST['stChaveProcesso']) {
    $arProcesso = explode('/', $_REQUEST['stChaveProcesso']);
    $obTLicitacaoEdital->setDado( 'cod_processo', intval($arProcesso[0]) );
}

if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode('/', $_REQUEST['stMapaCompras'] );
    $obTLicitacaoEdital->setDado( 'cod_mapa', $arMapa[0] );
}

if ($_REQUEST['inCodTipoLicitacao']) {
    $obTLicitacaoEdital->setDado( 'cod_tipo_licitacao', $_REQUEST['stMapaCompras'] );
}

if ($_REQUEST['inCodCriterio']) {
    $obTLicitacaoEdital->setDado( 'cod_criterio', $_REQUEST['inCodCriterio'] );
}

if ($_REQUEST['stObjeto']) {
    $obTLicitacaoEdital->setDado( 'cod_objeto', $_REQUEST['stObjeto'] );
}

if ($_REQUEST['inCodComissao']) {
    $obTLicitacaoEdital->setDado( 'cod_comissao', $_REQUEST['inCodComissao'] );
}
if ($_REQUEST['tipoBusca'] != 'publicacaoEdital') {
    $stFiltro = "
            AND NOT EXISTS (
                                SELECT  1
                                  FROM  Licitacao.edital_anulado
                                 WHERE  le.num_edital = edital_anulado.num_edital
                                   AND  le.exercicio = edital_anulado.exercicio
                           )
        ";
}

if ($stAcao == 'anular') {
    $stFiltro .= " AND EXISTS             \n";
} else {
    $stFiltro .= " AND NOT EXISTS         \n";
}

$stFiltro .= "
                       (    SELECT  1
                              FROM  licitacao.edital_suspenso
                             WHERE  le.num_edital = edital_suspenso.num_edital
                               AND  le.exercicio = edital_suspenso.exercicio
                       )
    ";

$stFiltro.= Sessao::read('filtroAdicionalSqlEditais');

$obTLicitacaoEdital->recuperaListaEdital( $rsEdital, $stFiltro);

$rsEdital->setCampo( 'cod_processo', str_pad($rsEdital->getCampo( 'cod_processo' ), 5, "0", STR_PAD_LEFT) );

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsEdital );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Licitação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número do Edital" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo Administrativo" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "num_licitacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "num_edital" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_processo]/[exercicio_processo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [descricao]" );
$obLista->commitDado();

$stAcao = "SELECIONAR";

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1", "[num_edital]/[exercicio]" );
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
