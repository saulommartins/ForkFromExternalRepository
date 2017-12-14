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
    * Arquivo de Lista da Entrada por Ordem de Compra
    * Data de Criação: 12/07/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    $Id: LSMovimentacaoOrdemCompra.php 62703 2015-06-10 13:29:57Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasOrdem.class.php";
include_once TCOM.'TComprasNotaFiscalFornecedor.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoOrdemCompra";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$stLink = '&stAcao='.$stAcao;

if ($_REQUEST['stExercicio']) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
    Sessao::write('filtro', $filtro);
} else {
    $filtro = Sessao::read('filtro');
    if ($filtro) {
        foreach ($filtro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando', true);
}

$obTComprasOrdemCompra = new TComprasOrdem();
$obTComprasOrdemCompra->setDado('tipo','C');
$stFiltro = "";

$obTComprasNotaFiscalFornecedor = new TComprasNotaFiscalFornecedor();

if ($_REQUEST['stExercicio']) {
    $obTComprasOrdemCompra->setDado('exercicio',$_REQUEST['stExercicio']);
    $obTComprasNotaFiscalFornecedor->setDado ( 'exercicio' , $_REQUEST['stExercicio']);
}

if ($_REQUEST['inOrdemCompra']) {
    $obTComprasOrdemCompra->setDado('cod_ordem',$_REQUEST['inOrdemCompra']);
}

if ($_REQUEST['inCodEntidade']) {
    $obTComprasOrdemCompra->setDado('cod_entidade',implode(',',$_REQUEST['inCodEntidade']));
    $obTComprasNotaFiscalFornecedor->setDado ( 'cod_entidade' , implode(',',$_REQUEST['inCodEntidade']));
}

if ($_REQUEST['inCGM']) {
    $stFiltro .= ' AND ordem.cgm_beneficiario = '.$_REQUEST['inCGM'].' ';
}

$stOrdem = "\n ORDER BY ordem.cod_ordem, ordem.exercicio";

$obTComprasOrdemCompra->recuperaOrdemCompraFornecedor( $rsOrdemCompra, $stFiltro, $stOrdem );

// Só lista as ordens de compra que não foram atendidas completamente.
$inCount = 0;

$obLista = new Lista;
$obLista->obPaginacao->setFiltro('&stLink='.$stLink);

$obLista->setRecordSet ( $rsOrdemCompra );
//$obLista->setTitulo             ("Registros");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "OC" );
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Autorização" );
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Empenho" );
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_ordem]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "cod_autorizacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio_empenho]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_entidade] - [nom_entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_beneficiario] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_ordem" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Selecionar' );
$obLista->ultimaAcao->addCampo( "&inOrdemCompra" , "cod_ordem" );
$obLista->ultimaAcao->addCampo( "&stEntidade"    , "[cod_entidade] - [nom_entidade]" );
$obLista->ultimaAcao->addCampo( "&stDtOrdem"     , "dt_ordem" );
$obLista->ultimaAcao->addCampo( "&stFornecedor"  , "[cgm_beneficiario] - [nom_cgm]" );
$obLista->ultimaAcao->addCampo( "&stExercicio"   , "exercicio");
$obLista->ultimaAcao->addCampo( "&nuVlTotal"     , "[vl_total]");
$obLista->ultimaAcao->addCampo( "&inCodEntidade" , "[cod_entidade]");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>
