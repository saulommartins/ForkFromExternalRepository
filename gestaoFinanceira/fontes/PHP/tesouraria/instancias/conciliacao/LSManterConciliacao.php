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
    * Página de Listagem de Terminais
    * Data de Criação   : 06/02/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson Barboza

    * @ignore

    * $Id: LSManterConciliacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRTesourariaConciliacao = new RTesourariaConciliacao;
$rsLista = new RecordSet();
$arFiltro = Sessao::read('filtro');
if ( !Sessao::read('paginando') ) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $_REQUEST['pg'] ? $_REQUEST['pg'] : 0);
    Sessao::write('pos', $_REQUEST['pos']? $_REQUEST['pos'] : 0);
    Sessao::write('paginando', true);
} else {
    Sessao::write('pg', $_REQUEST['pg']);
    Sessao::write('pos', $_REQUEST['pos']);
}
$_REQUEST['inMes'             ] = $arFiltro['inMes'             ];
$_REQUEST['stDataInicial'     ] = $arFiltro['stDataInicial'     ];
$_REQUEST['stDataFinal'       ] = $arFiltro['stDataFinal'       ];
$_REQUEST['inCodPlanoInicial' ] = $arFiltro['inCodPlanoInicial' ];
$_REQUEST['inCodPlanoFinal'   ] = $arFiltro['inCodPlanoFinal'   ];
$_REQUEST['boAgrupar'         ] = $arFiltro['boAgrupar'         ];
$_REQUEST['inCodigoEntidadesSelecionadas' ] = $arFiltro['inCodigoEntidadesSelecionadas' ];

Sessao::write('filtro', $arFiltro);

if ($_REQUEST['inCodigoEntidadesSelecionadas']) {
    foreach ($_REQUEST['inCodigoEntidadesSelecionadas'] as $value) {
        $stCodEntidade .= $value . " , ";
    }
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setExercicio ( Sessao::getExercicio() );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlanoInicial( $_REQUEST['inCodPlanoInicial'] );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->setCodPlanoFinal  ( $_REQUEST['inCodPlanoFinal'] );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade );
$obRTesourariaConciliacao->obRContabilidadePlanoBanco->listarContasBancosAConciliar($rsLista);
$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Reduzido");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome da Conta");
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_plano" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_conta" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( "Conciliar" );
$obLista->ultimaAcao->addCampo( "&stExercicio"          , "exercicio"       );
$obLista->ultimaAcao->addCampo( "inCodEntidade"         , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "inCodPlano"            , "cod_plano"       );
$obLista->ultimaAcao->addCampo( "stNomConta"            , "nom_conta"       );
$obLista->ultimaAcao->addCampo( "stNomEntidade"         , "nom_cgm"         );
$obLista->ultimaAcao->setLink( $pgForm."?stAcao=".$_REQUEST["stAcao"]."&".Sessao::getId()."&stDtExtrato=".$_REQUEST['stDtExtrato']."&boAgrupar=".$_REQUEST['boAgrupar'] );
$obLista->commitAcao();

$obLista->show();

?>
