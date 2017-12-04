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
 * Data de Criação: 10/09/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: LSManterVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.02.06
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php";
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaUtilizacaoRetorno.class.php";

$stPrograma      = "ManterVeiculo";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgFormBaixa     = "FMManterBaixarVeiculo.php";
$pgFormRetirar   = "FMManterRetirarVeiculo.php";
$pgFormConsultar = "FMManterConsultarVeiculo.php";
$pgProc          = "PR".$stPrograma.".php";
$pgProcBaixa     = "PRManterBaixarVeiculo.php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."veiculo/";

if (is_array( Sessao::read('filtro'))) {
    foreach ( Sessao::read('filtro') as $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
} else {
    Sessao::write('filtro', $_REQUEST);
}

if ($request->get('pg') != '') {
    Sessao::write('pg'  , $request->get('pg'));
    Sessao::write('pos' , $request->get('pos'));
} else {
    $request->get('pg', Sessao::read('pg'));
    $request->get('pos',Sessao::read('pos'));
}

Sessao::write('paginando' , true);

# Inicializa o filtro da consulta.
$stFiltro = "";

if ($stAcao != 'consultar' && $stAcao != 'exc_baixa') {
    $stFiltro .= " AND NOT EXISTS
                      (
                        SELECT  1
                          FROM  frota.veiculo_baixado
                         WHERE  veiculo_baixado.cod_veiculo = veiculo.cod_veiculo
                      )";
} elseif ($stAcao == 'exc_baixa') {
    $stFiltro .= " AND EXISTS
                      (
                        SELECT  1
                          FROM  frota.veiculo_baixado
                         WHERE  veiculo_baixado.cod_veiculo = veiculo.cod_veiculo
                      )";
}
$stComplementoLink = "";
# Filtro por codigo do veiculo.
if (is_numeric($_REQUEST['inCodVeiculo']) && isset($_REQUEST['inCodVeiculo'])) {
    $stFiltro .= " AND veiculo.cod_veiculo = ".$_REQUEST['inCodVeiculo']." ";
    $stComplementoLink .= "&inCodVeiculo=".$_REQUEST['inCodVeiculo'];
}

# Filtro por marca do veiculo.
if (is_numeric($_REQUEST['inCodMarca']) && isset($_REQUEST['inCodMarca'])) {
    $stFiltro .= " AND veiculo.cod_marca = ".$_REQUEST['inCodMarca']." ";
    $stComplementoLink .= "&inCodMarca=".$_REQUEST['inCodMarca'];
}

# Filtro por modelo do veiculo.
if (is_numeric($_REQUEST['inCodModelo']) && isset($_REQUEST['inCodModelo'])) {
    $stFiltro .= " AND veiculo.cod_modelo = ".$_REQUEST['inCodModelo']." ";
    $stComplementoLink .= "&inCodModelo=".$_REQUEST['inCodModelo'];
}

# Filtro por tipo do veiculo.
if (is_numeric($_REQUEST['slTipoVeiculo']) && isset($_REQUEST['slTipoVeiculo'])) {
    $stFiltro .= " AND veiculo.cod_tipo_veiculo = ".$_REQUEST['slTipoVeiculo']." ";
    $stComplementoLink .= "&slTipoVeiculo=".$_REQUEST['slTipoVeiculo'];
}

# Filtro por prefixo.
if ($_REQUEST['stPrefixo'] != '') {
    $stFiltro .= " AND veiculo.prefixo = '".$_REQUEST['stPrefixo']."' ";
    $stComplementoLink .= "&stPrefixo=".$_REQUEST['stPrefixo'];
}

# Filtro por número da placa.
if ($_REQUEST['stNumPlaca'] != '') {
    $stFiltro .= " AND SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) ILIKE '%".$_REQUEST['stNumPlaca']."%' ";
    $stComplementoLink .= "&stNumPlaca=".$_REQUEST['stNumPlaca'];
}

# Filtro por combustível.
if ($request->get('inCodCombustivelSelecionados') != '') {
    $stFiltro .= " AND EXISTS
                       (
                            SELECT 1
                              FROM frota.veiculo_combustivel
                             WHERE veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo
                               AND veiculo_combustivel.cod_combustivel IN ( ".implode(',',$_REQUEST['inCodCombustivelSelecionados'])." )
                       ) ";

    foreach ($_REQUEST['inCodCombustivelSelecionados'] as $chave => $dadosCombustivel) {
        $stComplementoLink .= "&inCodCombustivelSelecionados[".$chave."]=".$dadosCombustivel;
    }
}

//seta a origem
if ($request->get('stOrigem') == 'proprio') {
    $stFiltro .= " AND EXISTS
                       (
                            SELECT 1
                              FROM frota.proprio
                             WHERE proprio.cod_veiculo = veiculo.cod_veiculo
                       ) ";
}

if ($request->get('stOrigem') == 'terceiros') {
    $stFiltro .= " AND EXISTS
                       (
                            SELECT 1
                              FROM frota.terceiros
                             WHERE terceiros.cod_veiculo = veiculo.cod_veiculo
                       ) ";
}

//filtro por responsavel
 if ($_REQUEST['inCodResponsavel']) {
    $stFiltro .= " AND
                       (
                          EXISTS( SELECT 1
                                    FROM frota.proprio
                                         JOIN patrimonio.bem
                                           ON ( bem.cod_bem = proprio.cod_bem )
                                         JOIN ( SELECT bem_responsavel.cod_bem
                                                     , bem_responsavel.numcgm
                                                     , max(timestamp)
                                                  FROM patrimonio.bem_responsavel
                                              GROUP BY bem_responsavel.cod_bem
                                                     , bem_responsavel.numcgm       ) as bem_responsavel
                                           ON (     bem_responsavel.cod_bem = bem.cod_bem
                                                AND bem_responsavel.numcgm  = ".$_REQUEST['inCodResponsavel']." )
                                   WHERE proprio.cod_veiculo = veiculo.cod_veiculo )
                          OR

                          EXISTS ( SELECT veiculo_terceiros_responsavel.cod_veiculo
                                        , veiculo_terceiros_responsavel.numcgm
                                        , max(timestamp)
                                     FROM frota.veiculo_terceiros_responsavel
                                    WHERE veiculo_terceiros_responsavel.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_terceiros_responsavel.numcgm = ".$_REQUEST['inCodResponsavel']."
                                 GROUP BY veiculo_terceiros_responsavel.cod_veiculo
                                        , veiculo_terceiros_responsavel.numcgm ) ) ";
}

if ($request->get('stOrigem')) {
    $stComplementoLink .= "&stOrigem=".$_REQUEST['stOrigem'];
}

if ($stAcao == 'excluir') {
    $stFiltro .= " AND NOT EXISTS ( SELECT 1
                                      FROM frota.utilizacao
                                     WHERE utilizacao.cod_veiculo = veiculo.cod_veiculo
                                  )

                   AND NOT EXISTS ( SELECT 1
                                      FROM frota.manutencao
                                     WHERE manutencao.cod_veiculo = veiculo.cod_veiculo
                                  )

                   AND NOT EXISTS ( SELECT 1
                                      FROM frota.veiculo_documento_empenho
                                     WHERE veiculo_documento_empenho.cod_veiculo = veiculo.cod_veiculo
                                  ) ";
} elseif ($stAcao == 'baixar' || $stAcao == 'retirar') {
    $stFiltro .= " AND NOT EXISTS
                       (
                            SELECT  1
                              FROM  frota.utilizacao
                         LEFT JOIN  frota.utilizacao_retorno
                                ON  utilizacao_retorno.cod_veiculo = veiculo.cod_veiculo
                               AND  utilizacao_retorno.dt_saida = utilizacao.dt_saida
                               AND  utilizacao_retorno.hr_saida = utilizacao.hr_saida
                             WHERE  utilizacao.cod_veiculo = veiculo.cod_veiculo
                               AND  utilizacao_retorno.dt_retorno IS NULL
                       ) ";
}

if ($request->get('inCodEntidade') != '') {
    if ($_REQUEST['stOrigem'] == 'proprio') {
        $stFiltro .= " AND EXISTS
                           (
                                    SELECT  veiculo_propriedade.cod_veiculo
                                         ,  MAX(veiculo_propriedade.timestamp) AS timestamp
                                      FROM  frota.veiculo_propriedade
                                INNER JOIN  frota.proprio
                                        ON  proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                       AND  proprio.timestamp = veiculo_propriedade.timestamp
                                INNER JOIN  patrimonio.bem_comprado
                                        ON  bem_comprado.cod_bem = proprio.cod_bem
                                     WHERE  veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                       AND  bem_comprado.cod_entidade IN ( ".implode(',',$_REQUEST['inCodEntidade'])." )
                                  GROUP BY  veiculo_propriedade.cod_veiculo
                           ) ";

        foreach ($_REQUEST['inCodEntidade'] as $chave => $dadosEntidade) {
            $stComplementoLink .= "&inCodEntidade[".$chave."]=".$dadosEntidade;
        }
    }
}

# Seta a ordenação da consulta.
if (!empty($_REQUEST['stOrdenacao'])) {

    $stOrder = ' ORDER BY ';

    switch ($_REQUEST['stOrdenacao']) {
        case 'codigo' : $stOrder .= ' veiculo.cod_veiculo ';                   break;
        case 'placa'  : $stOrder .= ' veiculo.placa ';                         break;
        case 'marca'  : $stOrder .= ' marca.nom_marca, veiculo.cod_modelo ';   break;
        case 'modelo' : $stOrder .= ' modelo.nom_modelo, veiculo.cod_marca ';  break;

        default: $stOrder = '';  break;
    }
}

$stFiltro = isset($stFiltro) ? $stFiltro : null;
$stSql = isset($stSql) ? $stSql : null;

$stFiltro .= $stSql;

if ($stAcao == 'retornar') {
    if ($_REQUEST['stDataInicial'] != '' && $_REQUEST["stDataFinal"] != '') {
        $stFiltro .= " AND TO_DATE(utilizacao.dt_saida,'yyyy-mm-dd') BETWEEN TO_DATE('".$_REQUEST["stDataInicial"]."','dd/mm/yyyy')   \n";
        $stFiltro .= " AND TO_DATE('".$_REQUEST["stDataFinal"]."','dd/mm/yyyy')";
    }

    $obTFrotaVeiculoSemRetorno = new TFrotaUtilizacaoRetorno;
    $obTFrotaVeiculoSemRetorno->recuperaVeiculoSemRetorno($rsVeiculo, $stFiltro, $stOrder);
} else {
    //recupera os dados do banco de acordo com o filtro
    $obTFrotaVeiculo = new TFrotaVeiculo;
    $obTFrotaVeiculo->recuperaVeiculoSintetico($rsVeiculo, $stFiltro, $stOrder);
}

//instancia uma nova lista
$obLista = new Lista;

$stLink = isset($stLink) ? $stLink : null;
$stLink .= "&stAcao=".$stAcao.$stComplementoLink;

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
$obLista->ultimoCabecalho->addConteudo( "Placa" );
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

if ($stAcao == 'retornar') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Saída" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Hora Saída" );
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "cod_veiculo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "placa_masc" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_marca" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_modelo" );
$obLista->commitDado();

if ($stAcao == 'retornar') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_saida" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "hr_saida" );
    $obLista->commitDado();
}

$obLista->addAcao();

if ($stAcao == 'exc_baixa') {
    $obLista->ultimaAcao->setAcao('excluir');
} else {
    $obLista->ultimaAcao->setAcao($stAcao);
}

$obLista->ultimaAcao->addCampo("&inCodVeiculo"  , "cod_veiculo");
$obLista->ultimaAcao->addCampo("&stDescQuestao" , "[cod_veiculo]");

if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'baixar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormBaixa."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'exc_baixa') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProcBaixa."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'retirar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormRetirar."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'consultar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormConsultar."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'retornar') {
    $obLista->ultimaAcao->addCampo( "&inCodVeiculo", "cod_veiculo" );
    $obLista->ultimaAcao->addCampo( "&stDataSaida", "dt_saida" );
    $obLista->ultimaAcao->addCampo( "&stHoraSaida", "hr_saida" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormRetirar."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc.'?'.Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

?>
