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
 * Data de Criação: 05/02/2009

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

 * Casos de uso: uc-03.02.20

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaUtilizacao.class.php";

$stPrograma = "ManterUtilizacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."veiculo/";

//seta o filtro na sessao e vice-versa
if (is_array( Sessao::read('filtro'))) {
    foreach ( Sessao::read('filtro') as $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
} else {
    Sessao::write('filtro', $_REQUEST);
}

if ($_REQUEST['pg'] != '') {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
} else {
    $_GET['pg'] = Sessao::read('pg');
    $_GET['pos'] = Sessao::read('pos');
}

Sessao::write('paginando' , true);

$stComplementoLink = "";

//seta o codigo do veiculo
if ($_REQUEST['inCodVeiculo'] != '') {
    $stFiltro .= " AND veiculo.cod_veiculo = ".$_REQUEST['inCodVeiculo']." ";
    $stComplementoLink .= "&inCodVeiculo=".$_REQUEST['inCodVeiculo'];
}

//seta a marca do veiculo
if ($_REQUEST['inCodMarca'] != '') {
    $stFiltro .= " AND veiculo.cod_marca = ".$_REQUEST['inCodMarca']." ";
    $stComplementoLink .= "&inCodMarca=".$_REQUEST['inCodMarca'];
}
//seta o modelo do veiculo
if ($_REQUEST['inCodModelo'] != '') {
    $stFiltro .= " AND veiculo.cod_modelo = ".$_REQUEST['inCodModelo']." ";
    $stComplementoLink .= "&inCodModelo=".$_REQUEST['inCodModelo'];
}
//seta o tipo do veiculo
if ($_REQUEST['slTipoVeiculo'] != '') {
    $stFiltro .= " AND veiculo.cod_tipo_veiculo = ".$_REQUEST['slTipoVeiculo']." ";
    $stComplementoLink .= "&slTipoVeiculo=".$_REQUEST['slTipoVeiculo'];
}
//seta o prefixo
if ($_REQUEST['stPrefixo'] != '') {
    $stFiltro .= " AND veiculo.prefixo = '".$_REQUEST['stPrefixo']."' ";
    $stComplementoLink .= "&stPrefixo=".$_REQUEST['stPrefixo'];
}
//seta o numero da placa
if ($_REQUEST['stNumPlaca'] != '') {
    $stFiltro .= " AND SUBSTR(VEICULO.PLACA,1,3) || '-' || SUBSTR(VEICULO.PLACA,4,4) ILIKE '%".$_REQUEST['stNumPlaca']."%' ";
    $stComplementoLink .= "&stNumPlaca=".$_REQUEST['stNumPlaca'];
}
//seta o responsavel
if ($_REQUEST['inCodResponsavel'] != '') {
    $stFiltro .= " AND veiculo.cod_responsavel = ".$_REQUEST['inCodResponsavel']." ";
    $stComplementoLink .= "&inCodResponsavel=".$_REQUEST['inCodResponsavel'];
}

// verifica e entidade a que pertence os veiculos
if ($_REQUEST['inCodEntidade'] != '') {
    $stFiltro .= " AND
              EXISTS ( SELECT veiculo_propriedade.cod_veiculo
                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                         FROM frota.veiculo_propriedade
                   INNER JOIN frota.proprio
                       ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND proprio.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN patrimonio.bem_comprado
                       ON bem_comprado.cod_bem = proprio.cod_bem
                        WHERE veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND bem_comprado.cod_entidade IN ( ".implode(',', $_REQUEST['inCodEntidade'])." )
                     GROUP BY veiculo_propriedade.cod_veiculo 
                      ) OR (
                  EXISTS ( SELECT veiculo_locacao.cod_veiculo
                             FROM frota.veiculo_locacao
                       INNER JOIN frota.veiculo
                               ON veiculo.cod_veiculo = veiculo_locacao.cod_veiculo
                              AND veiculo_locacao.cod_entidade IN ( ".implode(',', $_REQUEST['inCodEntidade'])." )
                     GROUP BY veiculo_locacao.cod_veiculo 
               ))
                           
                           ";
    foreach ($_REQUEST['inCodEntidade'] as $chave => $dadosEntidade) {
        $stComplementoLink .= "&inCodEntidade[".$chave."]=".$dadosEntidade;
    }
}

// verifica o combustivel dos veiculos
if ($_REQUEST['inCodCombustivelSelecionados'] != '') {
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM frota.veiculo_combustivel
                                 WHERE veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo
                                   AND veiculo_combustivel.cod_combustivel IN ( ".implode(',',$_REQUEST['inCodCombustivelSelecionados'])." )
                              ) ";
    foreach ($_REQUEST['inCodCombustivelSelecionados'] as $chave => $dadosCombustivel) {
        $stComplementoLink .= "&inCodCombustivelSelecionados[".$chave."]=".$dadosCombustivel;
    }
}

//seta a origem
if ($_REQUEST['stOrigem'] == 'proprio') {
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM frota.proprio
                                 WHERE proprio.cod_veiculo = veiculo.cod_veiculo
                              ) ";
    $stComplementoLink .= "&stOrigem=".$_REQUEST['stOrigem'];
}
if ($_REQUEST['stOrigem'] == 'terceiros') {
     $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM frota.terceiros
                                 WHERE terceiros.cod_veiculo = veiculo.cod_veiculo
                              ) ";
    $stComplementoLink .= "&stOrigem=".$_REQUEST['stOrigem'];
}

if ($stFiltro) {
    $stFiltro = ' WHERE '.substr($stFiltro,4);
}

//seta a ordenacao
switch ($_REQUEST['stOrdenacao']) {

    case 'codigo' :
        $stOrder = ' ORDER BY veiculo.cod_veiculo, UTILIZACAO.DT_SAIDA, UTILIZACAO.HR_SAIDA ';
        break;
    case 'dataAscendente' :
        $stOrder = ' ORDER BY UTILIZACAO.DT_SAIDA ASC, UTILIZACAO.HR_SAIDA ASC, veiculo.cod_veiculo ASC ';
        break;
    case 'dataDescendente' :
        $stOrder = ' ORDER BY UTILIZACAO.DT_SAIDA DESC, UTILIZACAO.HR_SAIDA DESC, veiculo.cod_veiculo DESC ';
        break;
    case 'horaAscendente' :
        $stOrder = ' ORDER BY UTILIZACAO.DT_SAIDA ASC, UTILIZACAO.HR_SAIDA ASC ';
        break;
    case 'horaDescendente' :
        $stOrder = ' ORDER BY UTILIZACAO.DT_SAIDA DESC, UTILIZACAO.HR_SAIDA DESC ';
        break;
    $stComplementoLink .= "&stOrdenacao=".$_REQUEST['stOrdenacao'];
}

//recupera os dados do banco de acordo com o filtro
$obTFrotaVeiculo = new TFrotaUtilizacao();
$obTFrotaVeiculo->recuperaListaUtilizacaoVeiculo( $rsVeiculo, $stFiltro, $stOrder );

//instancia uma nova lista
$obLista = new Lista;

$stLink .= "&stAcao=excluir".$stComplementoLink;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsVeiculo );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Marca" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modelo" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Placa" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Saída" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Hora" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "KM" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Retorno" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Hora" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "KM" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "cod_veiculo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_marca" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_modelo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "placa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_saida" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "hr_saida" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "km_saida" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_retorno" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "hr_retorno" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "km_retorno" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( 'excluir' );
$obLista->ultimaAcao->addCampo( "&inCodVeiculo", "cod_veiculo" );
$obLista->ultimaAcao->addCampo( "&stDataSaida", "dt_saida" );
$obLista->ultimaAcao->addCampo( "&stHoraSaida", "hr_saida" );
$obLista->ultimaAcao->addCampo( "&stDataRetorno", "dt_retorno" );
$obLista->ultimaAcao->addCampo( "&stHoraRetorno", "hr_retorno" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao", "Veículo: [cod_veiculo]" );

$obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();

?>
