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
 * Página de Listagem de Mapa de Compras
 * Data de Criação   : 19/09/2006

 * @author Analista: Cleisson Barbosa
 * @author Desenvolvedor: Bruce Cruz de Sena

 * @ignore

 * Casos de uso: uc-03.04.05

 $Id: LSManterMapaCompras.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php';

$stPrograma = "ManterMapaCompras";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgCons     = "FM".$stPrograma."Consulta.php";

if (isset($_REQUEST['txtCodSolicitacao'])) {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
} else {
    $_REQUEST = Sessao::read('link');
}

$stAcao = $request->get('stAcao');
$stLink.= "&stAcao=".$stAcao;

//// montando o filtro
$arFiltro = array();

/// filtro por entidade
if ($request->get('inCodEntidade')) {

    $stEntidade = implode( $_REQUEST['inCodEntidade'] , ',' ) ;

    $arFiltro[] = " exists ( select * from compras.mapa_solicitacao
                               join orcamento.entidade
                                 on ( entidade.cod_entidade = mapa_solicitacao.cod_entidade )
                              where mapa_solicitacao.exercicio = mapa.exercicio
                                and mapa_solicitacao.cod_mapa  = mapa.cod_mapa
                                and entidade.cod_entidade in ( $stEntidade ) ) ";
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
      $dtDataFinal   = $_REQUEST["stDtFinal"]  ;
      $arFiltro[] = "to_char ( mapa.timestamp, 'dd/mm/yyyy' ) >=  '$dtDataInicial' AND to_char( mapa.timestamp, 'dd/mm/yyyy' ) <= '$dtDataFinal' ";
}

/// filtro por item
if ($_REQUEST['inCodItem']) {
    $arFiltro[] = 'mapa.cod_mapa in ( select cod_mapa from compras.mapa_item where cod_item = ' . $_REQUEST['inCodItem'] . ')';
}

/// filtro por dotação e/ou centro de custo
if (($_REQUEST['inCodDotacao']) || ($_REQUEST['inCodCentroCusto'])) {
    $stCondicao = " mapa.cod_mapa in
                    (
                          SELECT  cod_mapa
                            FROM  compras.mapa_item

                      INNER JOIN  compras.solicitacao_item
                              ON  solicitacao_item.exercicio       = mapa_item.exercicio
                             AND  solicitacao_item.cod_entidade    = mapa_item.cod_entidade
                             AND  solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
                             AND  solicitacao_item.cod_centro      = mapa_item.cod_centro
                             AND  solicitacao_item.cod_item        = mapa_item.cod_item

                      INNER JOIN  compras.solicitacao_item_dotacao
                              ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                             AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                             AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                             AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item
                             AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro

                   WHERE  NOT cod_mapa IS NULL ";

    if ($_REQUEST['inCodDotacao']) {
        $stCondicao .= ' AND solicitacao_item_dotacao.cod_despesa = '.$_REQUEST['inCodDotacao'];
    }

    if ($_REQUEST['inCodCentroCusto']) {
        $stCondicao .= ' AND solicitacao_item.cod_centro = '.$_REQUEST['inCodCentroCusto'];
    }

    $arFiltro[] = $stCondicao. ' )';
}

# Filtro utilizado para não listar mapas que já estejam totalmente anulados.
if ($stAcao == "alterar") {
    $arFiltro[] = "    (
                         SELECT  COALESCE(SUM(vl_total), 0.00)
                           FROM  compras.mapa_item
                          WHERE  mapa.exercicio = mapa_item.exercicio
                            AND  mapa.cod_mapa  = mapa_item.cod_mapa
                       ) -
                       (
                         SELECT  COALESCE(SUM(vl_total), 0.00)
                           FROM  compras.mapa_item_anulacao
                          WHERE  mapa_item_anulacao.cod_mapa  = mapa.cod_mapa
                            AND  mapa_item_anulacao.exercicio = mapa.exercicio
                       ) > 0 ";
}

if ($_REQUEST['txtCodMapa']) {
    $arFiltro[] = " mapa.cod_mapa = ". $_REQUEST['txtCodMapa'];
}

# Verifica se o campo stAno, se o campo estiver vázil ele adiciona o Exercício atual
if ($request->get('stAno')) {
    $arFiltro[] = " mapa.exercicio = '". $_REQUEST['stAno'] . "' ";
} else {
    $arFiltro[] = " mapa.exercicio = '". Sessao::getExercicio() . "' ";
}

if (count($arFiltro) > 0) {
    $stFiltro =  implode($arFiltro, "\n and ");
    if ($stAcao == 'anular') {
        $stFiltro = "\n AND $stFiltro";
    } else {
        $stFiltro = "\n WHERE $stFiltro";
    }
}

$obTComprasMapa = new TComprasMapa;
$stOrder = " mapa.cod_mapa DESC ";

if (($stAcao == 'anular')) {
    $obTComprasMapa->recuperaMapasAnulacao($rsMapas,$stFiltro, " order by $stOrder " );
} else {

    if (($stAcao == 'excluir') || ($stAcao == 'alterar')) {
        //// complementando o filtro pra pegar apenas mapas que podem ser excluidos
        $stFiltroExcluir = " NOT EXISTS
                             (
                                SELECT  1
                                  FROM  compras.mapa_cotacao
                                 WHERE  mapa_cotacao.exercicio_mapa = mapa.exercicio
                                   AND  mapa_cotacao.cod_mapa       = mapa.cod_mapa
                             )

                         AND NOT EXISTS
                             (
                                SELECT  1
                                  FROM  licitacao.licitacao
                                 WHERE  licitacao.exercicio_mapa = mapa.exercicio
                                   AND  licitacao.cod_mapa       = mapa.cod_mapa
                             )

                         AND NOT EXISTS
                             (
                                SELECT  1
                                  FROM  compras.compra_direta
                                 WHERE  compra_direta.cod_mapa       = mapa.cod_mapa
                                   AND  compra_direta.exercicio_mapa = mapa.exercicio
                             ) ";

        if ($stAcao == 'excluir') {

            # Não pode permitir a exclusão de Mapas que estejam sendo utilizados em Compra Direta ou Licitação.
            $stFiltroExcluir .= "  AND  NOT EXISTS
                                        (
                                            SELECT  1
                                              FROM  compras.mapa_solicitacao_anulacao
                                             WHERE  mapa_solicitacao_anulacao.exercicio = mapa.exercicio
                                               AND  mapa_solicitacao_anulacao.cod_mapa  = mapa.cod_mapa
                                        )

                                   AND  NOT EXISTS
                                        (
                                            SELECT  1
                                              FROM  compras.compra_direta
                                             WHERE  compra_direta.cod_mapa       = mapa.cod_mapa
                                               AND  compra_direta.exercicio_mapa = mapa.exercicio
                                        )

                                   AND  NOT EXISTS
                                        (
                                            SELECT  1
                                              FROM  licitacao.licitacao
                                             WHERE  licitacao.cod_mapa       = mapa.cod_mapa
                                               AND  licitacao.exercicio_mapa = mapa.exercicio
                                        ) ";
        }

        $stFiltro .= $stFiltro ? ' AND ' . $stFiltroExcluir : ' WHERE ' . $stFiltroExcluir;
    }

    $obTComprasMapa->recuperaTodos( $rsMapas, $stFiltro, $stOrder );
}

$rsMapas->addFormatacao ( 'valor_total', 'NUMERIC_BR' );

$obLista = new Lista;
$obLista->setRecordSet( $rsMapas );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Mapa');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Objeto');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor Total');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_mapa]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_objeto] - [descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "valor_total" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&cod_mapa" , "cod_mapa"   );
$obLista->ultimaAcao->addCampo("&exercicio", "exercicio"  );

if ($stAcao == 'excluir') {
    # Monta a descrição que será exibida na pop-up de exclusão.
    $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"[cod_mapa]/[exercicio]");
    $obLista->ultimaAcao->setLink( CAM_GP_COM_INSTANCIAS.'mapaCompras/'.$pgProc."?stAcao=$stAcao&".Sessao::getId().$stLink );
} else {
    if ($stAcao == 'consultar') {
        $obLista->ultimaAcao->setLink( $pgCons."?stAcao=$stAcao&".Sessao::getId().$stLink );
    } else {
        $obLista->ultimaAcao->setLink( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );
    }
}

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
