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
/*
    * Página de lista
    * Data de Criação: 27/03/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage

    * @ignore

    * Caso de uso: uc-03.03.32

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ReemitirSaida";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$stLink = '&stAcao='.$stAcao;

if ($_REQUEST['stAno']) {
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

    Sessao::write("paginando",true);
}

$stFiltro .= "WHERE natureza_lancamento.tipo_natureza='S' \n";

if ( is_array ( $_REQUEST['inCodNaturezaSelecionados'] ) ) {
    $natureza = implode( $_REQUEST['inCodNaturezaSelecionados'] , ' , ' );
}

if ( isset($natureza) ) {
    $stFiltro .= "  AND natureza_lancamento.cod_natureza IN ( $natureza )             \n";
}

if ($_REQUEST['inNumSaida'] != "") {
    $stFiltro .= "  AND natureza_lancamento.num_lancamento = ".$_REQUEST["inNumSaida"]. "\n";
}

if ($_REQUEST['inPeriodicidade'] != "") {
    if ($_REQUEST['stDataInicial']) {
        $dtDataInicial = $_REQUEST["stDataInicial"];
        $dtDataFinal   = $_REQUEST["stDataFinal"];

        $stFiltro .= "  AND TO_DATE(timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')   \n";
        $stFiltro .= "  AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                   \n";
    }
}

$stGrupo = " GROUP BY natureza_lancamento.TIMESTAMP
                    , natureza_lancamento.num_lancamento
                    , natureza.cod_natureza
                    , natureza.descricao
                    , natureza_lancamento.exercicio_lancamento
                    , lancamento_requisicao.exercicio  \n";

$stOrdem = "ORDER BY natureza.descricao, natureza_lancamento.num_lancamento  DESC";

$obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento();
$obTAlmoxarifadoNaturezaLancamento->recuperaDadosReemissao($rsDados, $stFiltro, $stGrupo, $stOrdem);

$obLista = new Lista;
$obLista->setAjuda('UC-03.03.32');
$obLista->obPaginacao->setFiltro('&stLink='.$stLink);

$obLista->setRecordSet( $rsDados );
$obLista->setTitulo             ("Registros");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Natureza" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Saída" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "data" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("E");
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_lancamento" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'Selecionar' );

$obLista->ultimaAcao->addCampo("&inNumLancamento"      , "num_lancamento"       );
$obLista->ultimaAcao->addCampo("&inCodNatureza"        , "cod_natureza"         );
$obLista->ultimaAcao->addCampo("&stExercicio"          , "exercicio" );
$obLista->ultimaAcao->addCampo("&stExercicioLancamento", "exercicio_lancamento");
$obLista->ultimaAcao->setLink ( $pgProc."?".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
