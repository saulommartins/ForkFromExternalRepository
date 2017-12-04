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
    * Página de Listagem de IPopUpMapaCompras
    * Data de Criação   : 23/10/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: LSBuscaMapaCompras.php 63859 2015-10-26 17:39:34Z franver $

    * Casos de uso: uc-03.04.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php'                                         );

$stAcao = $request->get("stAcao");

$stPrograma = "BuscaMapaCompras";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?" .Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

if ( isset($_REQUEST['txtCodSolicitacao'] )) {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
} else {
    $_REQUEST = Sessao::read('link');
}

$stLink = isset($stLink) ? $stLink : null;

$stLink .= "&stAcao=".$stAcao;
$stLink .= "&campoNom=".$request->get("campoNom");
$stLink .= "&campoNum=".$request->get("campoNum");
$stLink .= "&nomForm=".$request->get("nomForm");

//// montando o filtro
$arFiltro = array();

/// filtro por entidade
if ( $request->get('inNumCGM') ) {

    $arFiltro[] = "exists ( select 1
                      from compras.mapa_solicitacao
                      join orcamento.entidade
                        on ( entidade.cod_entidade = mapa_solicitacao.cod_entidade
                       and   entidade.exercicio    = mapa_solicitacao.exercicio_solicitacao  )
                     where  entidade.numcgm in (" .  implode( $_REQUEST['inNumCGM'] , ',' )     .  " )
                       and mapa_solicitacao.exercicio = mapa.exercicio
                       and mapa_solicitacao.cod_mapa  = mapa.cod_mapa
                       and entidade.exercicio = '".Sessao::getExercicio() . "'  )
                 ";

}
/// filtro por codigo solicitacao
if ($_REQUEST['txtCodSolicitacao']) {
    $arFiltro[] = 'exists ( select * from compras.mapa_solicitacao
                             where mapa_solicitacao.exercicio = mapa.exercicio
                               and mapa_solicitacao.cod_mapa  = mapa.cod_mapa
                               and mapa_solicitacao.cod_solicitacao = ' .$_REQUEST['txtCodSolicitacao'] . ')';
}
//// filtro por Objeto
if ($_REQUEST['stObjeto']) {
    $arFiltro[] = 'mapa.cod_objeto = ' . $_REQUEST['stObjeto'];
}

/// filtro por datas
if ($_REQUEST['stDtInicial']) {
      $dtDataInicial = $_REQUEST["stDtInicial"];
      $dtDataFinal   = $_REQUEST["stDtFinal"];
      $arFiltro[] = " ( mapa.timestamp::date >=  to_date('$dtDataInicial','dd/mm/yyyyy') AND  mapa.timestamp::date <= to_date('$dtDataFinal','dd/mm/yyyy') ) ";
}

/// filtro por item
if ($_REQUEST['inCodItem']) {
    $arFiltro[] = 'mapa.cod_mapa in ( select cod_mapa from compras.mapa_item where cod_item = ' . $_REQUEST['inCodItem'] . ')';
}

/// filtro por dotação e/ou centro de custo
if (($_REQUEST['inCodDotacao'])or ($_REQUEST['inCodCentroCusto'])) {
    $stCondicao = " mapa.cod_mapa in (   select cod_mapa
                    from compras.mapa_item
                    join compras.solicitacao_item
                        on (solicitacao_item.exercicio       = mapa_item.exercicio
                        and solicitacao_item.cod_entidade    = mapa_item.cod_entidade
                        and solicitacao_item.cod_solicitacao   = mapa_item.cod_solicitacao
                        and solicitacao_item.cod_centro      = mapa_item.cod_centro
                        and solicitacao_item.cod_item        = mapa_item.cod_item)
                    join compras.solicitacao_item_dotacao
                        on ( solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                        and  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                        and  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                        and  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
                        and  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro)
                    where not cod_mapa is null  ";

     if ($_REQUEST['inCodDotacao']) {
            $stCondicao .= ' and solicitacao_item_dotacao.cod_despesa = '. $_REQUEST['inCodDotacao'];
     }
     if ($_REQUEST['inCodCentroCusto']) {
            $stCondicao .= ' and solicitacao_item.cod_centro = '.$_REQUEST['inCodCentroCusto'];
     }
     $arFiltro[] = $stCondicao. ' )';
}
if ( $request->get('txtCodMapa') ) {
    $arFiltro[] = " mapa.cod_mapa = ". $_REQUEST['txtCodMapa'];
}

if ($_REQUEST['stExercicioMapa']) {
     $arFiltro[] = " mapa.exercicio = '".$_REQUEST['stExercicioMapa']. "'";
}

if ($_REQUEST['boAutEmp']) {
     $arFiltro[] = "not exists( SELECT
                                       cotacao.cod_cotacao
                                     , cotacao.exercicio
                                     , max(cotacao.timestamp) as timestamp
                                  FROM
                                       compras.cotacao
                                       INNER JOIN empenho.item_pre_empenho_julgamento
                                               ON item_pre_empenho_julgamento.cod_cotacao = cotacao.cod_cotacao
                                              AND item_pre_empenho_julgamento.exercicio   = cotacao.exercicio
                                       INNER JOIN empenho.item_pre_empenho
                                               ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                                              AND item_pre_empenho.exercicio       = item_pre_empenho_julgamento.exercicio
                                              AND item_pre_empenho.num_item        = item_pre_empenho_julgamento.num_item
                                       INNER JOIN empenho.pre_empenho
                                               ON item_pre_empenho.exercicio = pre_empenho.exercicio
                                              AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                       INNER JOIN empenho.autorizacao_empenho
                                               ON autorizacao_empenho.exercicio       = pre_empenho.exercicio
                                              AND autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                       INNER JOIN compras.mapa_cotacao
                                               ON mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                                              AND mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                                 WHERE
                                       mapa_cotacao.exercicio_mapa = mapa.exercicio
                                   AND mapa_cotacao.cod_mapa       = mapa.cod_mapa
                              GROUP BY
                                       cotacao.exercicio
                                     , cotacao.cod_cotacao ) ";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = isset($stFiltro) ? $stFiltro : null;
    $stFiltro.= implode ( $arFiltro, "\n and " );
}

$stOrdem = " mapa.exercicio,
             mapa.cod_mapa
            ";

$rsMapas = new RecordSet;

$obTComprasMapa = new TComprasMapa;

switch ($_REQUEST['stTipoBusca']) {

    case 'verificaMapaComprasDireta':
        if ( $stFiltro ) $stFiltro = " and  $stFiltro ";
            $obTComprasMapa->recuperaMapaProcessoLicitatorio( $rsMapas, $stFiltro, $stOrdem );
        break;

    case 'processoLicitatorio':
        if ( $stFiltro ) $stFiltro = " and  $stFiltro ";
            $obTComprasMapa->recuperaMapaSemReservaProcessoLicitatorio( $rsMapas, $stFiltro, $stOrdem );
        break;

    case "manterContrato":
    
        $stFiltroContrato .= "
            EXISTS (SELECT 1
                    FROM licitacao.licitacao
    
                    INNER JOIN licitacao.contrato_licitacao
                            ON contrato_licitacao.cod_licitacao = licitacao.cod_licitacao
                        AND contrato_licitacao.cod_modalidade = licitacao.cod_modalidade
                        AND contrato_licitacao.cod_entidade = licitacao.cod_entidade
                        AND contrato_licitacao.exercicio = licitacao.exercicio
    
                    INNER JOIN licitacao.contrato
                            ON contrato_licitacao.num_contrato = contrato.num_contrato
                        AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                        AND contrato_licitacao.exercicio = contrato.exercicio
    
                    WHERE licitacao.cod_mapa = mapa.cod_mapa
                    AND licitacao.exercicio_mapa = mapa.exercicio)
    
            AND NOT EXISTS (
                         SELECT 1
                         FROM licitacao.licitacao
    
                    INNER JOIN licitacao.contrato_licitacao
                            ON contrato_licitacao.cod_licitacao = licitacao.cod_licitacao
                        AND contrato_licitacao.cod_modalidade = licitacao.cod_modalidade
                        AND contrato_licitacao.cod_entidade = licitacao.cod_entidade
                        AND contrato_licitacao.exercicio = licitacao.exercicio
    
                    INNER JOIN licitacao.contrato
                            ON contrato_licitacao.num_contrato = contrato.num_contrato
                        AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                        AND contrato_licitacao.exercicio = contrato.exercicio
    
                        INNER JOIN licitacao.rescisao_contrato
                                ON contrato.exercicio = rescisao_contrato.exercicio_contrato
                               AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                               AND contrato.num_contrato = rescisao_contrato.num_contrato
    
                         WHERE licitacao.cod_mapa = mapa.cod_mapa
                            AND licitacao.exercicio_mapa = mapa.exercicio
    
                        )
        ";
        $stFiltro = $stFiltro != "" ? "where".$stFiltro."\nAND ".$stFiltroContrato : "where".$stFiltroContrato;
        $obTComprasMapa->recuperaTodos ( $rsMapas, $stFiltro, $stOrdem );
    break;

    case 'manterContratoCompraDireta':

        $stFiltroContrato .= "
            EXISTS (SELECT 1
                    FROM compras.compra_direta

                    INNER JOIN licitacao.contrato_compra_direta
                            ON contrato_compra_direta.cod_compra_direta = compra_direta.cod_compra_direta
                        AND contrato_compra_direta.cod_modalidade = compra_direta.cod_modalidade
                        AND contrato_compra_direta.cod_entidade = compra_direta.cod_entidade
                        AND contrato_compra_direta.exercicio = compra_direta.exercicio_entidade

                    INNER JOIN licitacao.contrato
                            ON contrato_compra_direta.num_contrato = contrato.num_contrato
                        AND contrato_compra_direta.cod_entidade = contrato.cod_entidade
                        AND contrato_compra_direta.exercicio = contrato.exercicio

                    WHERE compra_direta.cod_mapa = mapa.cod_mapa
                      AND compra_direta.exercicio_mapa = mapa.exercicio)

            AND NOT EXISTS (
                         SELECT 1
                           FROM compras.compra_direta

                     INNER JOIN licitacao.contrato_compra_direta
                             ON contrato_compra_direta.cod_compra_direta = compra_direta.cod_compra_direta
                            AND contrato_compra_direta.cod_modalidade = compra_direta.cod_modalidade
                            AND contrato_compra_direta.cod_entidade = compra_direta.cod_entidade
                            AND contrato_compra_direta.exercicio = compra_direta.exercicio_entidade

                     INNER JOIN licitacao.contrato
                             ON contrato_compra_direta.num_contrato = contrato.num_contrato
                            AND contrato_compra_direta.cod_entidade = contrato.cod_entidade
                            AND contrato_compra_direta.exercicio = contrato.exercicio

                     INNER JOIN licitacao.rescisao_contrato
                             ON contrato.exercicio = rescisao_contrato.exercicio_contrato
                            AND contrato.cod_entidade = rescisao_contrato.cod_entidade
                            AND contrato.num_contrato = rescisao_contrato.num_contrato

                          WHERE compra_direta.cod_mapa = mapa.cod_mapa
                            AND compra_direta.exercicio_mapa = mapa.exercicio
                        )

        AND NOT EXISTS (
                         SELECT 1
                           FROM compras.compra_direta

                     INNER JOIN licitacao.contrato_compra_direta
                             ON contrato_compra_direta.cod_compra_direta = compra_direta.cod_compra_direta
                            AND contrato_compra_direta.cod_modalidade = compra_direta.cod_modalidade
                            AND contrato_compra_direta.cod_entidade = compra_direta.cod_entidade
                            AND contrato_compra_direta.exercicio = compra_direta.exercicio_entidade

                     INNER JOIN licitacao.contrato
                             ON contrato_compra_direta.num_contrato = contrato.num_contrato
                            AND contrato_compra_direta.cod_entidade = contrato.cod_entidade
                            AND contrato_compra_direta.exercicio = contrato.exercicio

                     INNER JOIN licitacao.contrato_anulado
                             ON contrato.exercicio = contrato_anulado.exercicio
                            AND contrato.cod_entidade = contrato_anulado.cod_entidade
                            AND contrato.num_contrato = contrato_anulado.num_contrato

                          WHERE compra_direta.cod_mapa = mapa.cod_mapa
                            AND compra_direta.exercicio_mapa = mapa.exercicio
                        )
            ";
        $stFiltro = $stFiltro != "" ? "where".$stFiltro."\nAND ".$stFiltroContrato : "where".$stFiltroContrato;
        $obTComprasMapa->recuperaTodos ( $rsMapas, $stFiltro, $stOrdem );

    break;

default:
    $stFiltro = isset($stFiltro) ? "where $stFiltro" : null;
    $obTComprasMapa->recuperaTodos ( $rsMapas, $stFiltro, $stOrdem );
    break;
}

//// monta listagem
$obLista = new Lista;
$obLista->setRecordSet( $rsMapas );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Exercício');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Mapa');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_mapa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "data" );
$obLista->commitDado();

$stAcao = "SELECIONAR";

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:Insere();" );
$obLista->ultimaAcao->addCampo("1", "[cod_mapa]/[exercicio]" );
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
