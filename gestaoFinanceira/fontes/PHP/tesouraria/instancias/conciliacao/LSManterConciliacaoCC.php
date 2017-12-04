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
    * Data de Criação   : 22/08/2014

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    * $Id: LSManterConciliacaoCC.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaConciliacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConciliacaoCC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

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
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $_REQUEST['pg']);
    Sessao::write('pos', $_REQUEST['pos']);
}
$_REQUEST['inMes'             ] = $arFiltro['inMes'             ];
$_REQUEST['stDataInicial'     ] = $arFiltro['stDataInicial'     ];
$_REQUEST['stDataFinal'       ] = $arFiltro['stDataFinal'       ];
$_REQUEST['inNumeroConta'     ] = $arFiltro['inNumeroConta'     ];

if ($arFiltro['inCodigoEntidadesSelecionadas']&&!is_array($arFiltro['inCodigoEntidadesSelecionadas'])) {
    $arFiltro['inCodigoEntidadesSelecionadas'] = explode(",", $arFiltro['inCodigoEntidadesSelecionadas']);
    
    $_REQUEST['inCodigoEntidadesSelecionadas']= $arFiltro['inCodigoEntidadesSelecionadas'];
}

Sessao::write('filtro', $arFiltro);

if ($_REQUEST['inCodigoEntidadesSelecionadas']) {
    foreach ($_REQUEST['inCodigoEntidadesSelecionadas'] as $value) {
        $stCodEntidade .= $value . " , ";
    }
}
$stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);

$obRTesourariaConciliacao->obRMONContaCorrente->setNumeroConta ( $_REQUEST[ 'inNumeroConta' ] );
$obRTesourariaConciliacao->obRMONContaCorrente->setNumeroBanco ( $_REQUEST[ 'inNumBanco' ] );
$obRTesourariaConciliacao->obRMONContaCorrente->setNumeroAgencia ( $_REQUEST[ 'inNumAgencia' ] );
$obRTesourariaConciliacao->obRMONContaCorrente->boVinculoPlanoBanco = true;
$obRTesourariaConciliacao->obRMONContaCorrente->inCodEntidadeVinculo = $stCodEntidade;
$obRTesourariaConciliacao->obRMONContaCorrente->listarContaCorrenteConciliacao($rsLista);

$obLista = new Lista;

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Conta Corrente");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Banco");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Agência");
$obLista->ultimoCabecalho->setWidth( 25 );
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
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_conta_corrente" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_banco] - [nom_banco]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_agencia] - [nom_agencia]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( "Conciliar" );
$obLista->ultimaAcao->addCampo( "&stCC"                 , "num_conta_corrente"              );
$obLista->ultimaAcao->addCampo( "inCodEntidade"         , "cod_entidade"                    );
$obLista->ultimaAcao->addCampo( "stNomEntidade"         , "nom_entidade"                    );
$obLista->ultimaAcao->addCampo( "stExercicio"           , "exercicio"                       );
$obLista->ultimaAcao->addCampo( "stNomeBanco"           , "[num_banco] - [nom_banco]"       );
$obLista->ultimaAcao->addCampo( "stNomeAgencia"         , "[num_agencia] - [nom_agencia] ');");
$obLista->ultimaAcao->setLink ( "javascript:Conciliar('".$pgForm."?stAcao=".$_REQUEST["stAcao"]."&".Sessao::getId()."&stDtExtrato=".$_REQUEST['stDtExtrato']);
$obLista->commitAcao();

$obLista->show();

include_once( $pgJs );
?>
