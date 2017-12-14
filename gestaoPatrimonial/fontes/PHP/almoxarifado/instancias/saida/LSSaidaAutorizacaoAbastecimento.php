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
 * Página de lista
 * Data de Criação: 04/01/2006

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Diego Barbosa Victoria

 * @ignore

 * Casos de uso: uc-03.03.11

 $Id: LSMovimentacaoRequisicao.php 37339 2009-01-16 11:41:18Z melo $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoAutorizacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "SaidaAutorizacaoAbastecimento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$stLink = '&stAcao='.$stAcao;

$arrayFiltroSelecionados = Sessao::read("filtro");

if (count($arrayFiltroSelecionados) == 0) {
    foreach ($_REQUEST as $key => $value) {
        $filtro[$key] = $value;
    }
    Sessao::write('filtro', $filtro);
} else {
    $arrayFiltro = Sessao::read("filtro");

    if (is_array($arrayFiltro)) {
        foreach ($arrayFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
}

if ($request->get('pg') != '') {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
} else {
    $_GET['pg'] = Sessao::read('pg');
    $_GET['pos'] = Sessao::read('pos');
}

Sessao::write('paginando' , true);

$dtDataInicial = $dtDataFinal = '';

//montando filtro
if (!empty($_REQUEST['inCodAlmoxarifado'])  ) {
    $arCodAlmoxarifados = $_REQUEST['inCodAlmoxarifado'] ;
}

if ($_REQUEST['stDataInicial']) {
    $dtDataInicial = $_REQUEST['stDataInicial'];
    $dtDataFinal   = $_REQUEST['stDataFinal'];
}

$stFiltro = "
AND  NOT EXISTS (
            SELECT  1
            FROM    almoxarifado.lancamento_autorizacao as lau
            WHERE   lau.exercicio       = aut.exercicio
            AND     lau.cod_autorizacao = aut.cod_autorizacao
        )
";

$stFiltro .= "  AND NOT EXISTS( SELECT 1
                                  FROM frota.efetivacao, frota.manutencao
                                 WHERE efetivacao.cod_autorizacao = aut.cod_autorizacao
                                   AND efetivacao.exercicio_autorizacao = aut.exercicio
                                   AND efetivacao.cod_manutencao = manutencao.cod_manutencao
                                   AND efetivacao.exercicio_manutencao = manutencao.exercicio
                                )  ";

if ($_REQUEST['inCodVeiculo']) {
    $stFiltro .= " AND vei.cod_veiculo=".$_REQUEST['inCodVeiculo'];
}

$rsLista = new RecordSet;

$obTLancamentoAutorizacao = new TAlmoxarifadoLancamentoAutorizacao();
$obTLancamentoAutorizacao->setDado('exercicio'      , $_REQUEST['stExercicio'] );
$obTLancamentoAutorizacao->setDado('cod_autorizacao', $_REQUEST['inCodAutorizacao'] );
$obTLancamentoAutorizacao->setDado('stDataInicial'  , $dtDataInicial );
$obTLancamentoAutorizacao->setDado('stDataFinal'    , $dtDataFinal );
$obTLancamentoAutorizacao->setDado('stFiltro', $stFiltro );
$obTLancamentoAutorizacao->recuperaRelacionamento($rsLista);

$obLista = new Lista;

$obLista->setRecordSet ( $rsLista );
$obLista->setTitulo    ("Registros");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Autorização" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Combustível" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Veículo" );
$obLista->ultimoCabecalho->setWidth( 33 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_autorizacao]/[exercicio]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_autorizacao" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[descricao_resumida]" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[nom_marca]-[nom_modelo]" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Selecionar' );
$obLista->ultimaAcao->addCampo( "&stExercicio",     "exercicio");
$obLista->ultimaAcao->addCampo( "&inCodAutorizacao","cod_autorizacao");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>
