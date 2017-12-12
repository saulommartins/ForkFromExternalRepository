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
    * Data de Criação: 26/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: LSManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO."TFrotaAutorizacao.class.php");

$stPrograma = "ManterAutorizacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."autorizacao/";

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
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
} else {
    Sessao::write('filtro' , $filtro);
}

Sessao::write('paginando' , true);

//seta os filtros
if ($stAcao == 'alterar' OR $stAcao == 'excluir') {
    $stFiltro = "
        NOT EXISTS ( SELECT 1
                       FROM frota.efetivacao
                      WHERE efetivacao.cod_autorizacao = autorizacao.cod_autorizacao
                        AND efetivacao.exercicio_autorizacao = autorizacao.exercicio
                        AND NOT EXISTS ( SELECT 1
                                           FROM frota.manutencao_anulacao
                                          WHERE manutencao_anulacao.exercicio = efetivacao.exercicio_manutencao
                                            AND manutencao_anulacao.cod_manutencao = efetivacao.cod_manutencao
                                        )
                   ) AND   ";
}

//seta o cod_autorizacao
if ($_REQUEST['inCodAutorizacao'] != '') {
    $stFiltro .= " autorizacao.cod_autorizacao = ".$_REQUEST['inCodAutorizacao']." AND   ";
}

//seta o exercicio
$stFiltro .= " autorizacao.exercicio = '".Sessao::getExercicio()."' AND   ";

//seta o cod_veiculo
if ($_REQUEST['inCodVeiculo'] != '') {
    $stFiltro .= " autorizacao.cod_veiculo = ".$_REQUEST['inCodVeiculo']." AND   ";
}

//seta a placa
if ($_REQUEST['stNumPlaca'] != '') {
    $stFiltro .= " veiculo.placa = '".str_replace('-','',$_REQUEST['stNumPlaca'])."' AND   ";
}

//seta a placa
if ($_REQUEST['stPrefixo'] != '') {
    $stFiltro .= " veiculo.prefixo = '".$_REQUEST['stPrefixo']."' AND   ";
}

//seta a periodicidade
if ($_REQUEST['stDataInicial'] != '') {
    $stFiltro .= " TO_DATE(autorizacao.timestamp::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$_REQUEST['stDataInicial']."','dd/mm/yyyy') AND TO_DATE('".$_REQUEST['stDataFinal']."','dd/mm/yyyy') AND   ";
}

if ($_REQUEST['inCodEntidade'] != '') {
    $stFiltro .= " EXISTS ( SELECT veiculo_propriedade.cod_veiculo
                                 , MAX(veiculo_propriedade.timestamp) AS timestamp
                              FROM frota.veiculo_propriedade
                        INNER JOIN frota.proprio
                                ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                               AND proprio.timestamp = veiculo_propriedade.timestamp
                        LEFT JOIN patrimonio.bem_comprado
                                ON bem_comprado.cod_bem = proprio.cod_bem
                             WHERE veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                               AND bem_comprado.cod_entidade IN ( ".implode(',',$_REQUEST['inCodEntidade'])." )
                          GROUP BY veiculo_propriedade.cod_veiculo)       ";
}

if ($stFiltro) {
    $stFiltro = ' WHERE '.substr($stFiltro,0,-6);
}

//recupera os dados do banco de acordo com o filtro
$obTFrotaAutorizacao = new TFrotaAutorizacao();
$obTFrotaAutorizacao->recuperaRelacionamento( $rsAutorizacao, $stFiltro, ' ORDER BY timestamp DESC' );

//instancia uma nova lista
$obLista = new Lista;
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsAutorizacao );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Autorização" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Combustável" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Veículo" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_autorizacao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo( "dt_autorizacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nom_combustivel" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[nom_marca] - [nom_modelo] - [nom_tipo]" );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == 'reemitir') {
    $obLista->ultimaAcao->setAcao( 'selecionar' );
} else {
    $obLista->ultimaAcao->setAcao( $stAcao );
}

$obLista->ultimaAcao->addCampo( "&inCodAutorizacao", "cod_autorizacao" );
$obLista->ultimaAcao->addCampo( "&stExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao" , "[cod_autorizacao]/[exercicio]" );

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} else {
    $stLink .= '&boVias='.$_REQUEST['boVias'];
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();
