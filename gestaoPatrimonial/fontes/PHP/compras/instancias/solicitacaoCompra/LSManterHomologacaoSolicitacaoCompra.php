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
 * Tela do formulário de listagem da Solicitação de compra
 * Data de Criação   : 24/09/2006

 * @author Analista     : Cleisson
 * @author Desenvolvedor: Bruce Cruz de Sena

 $Id: LSManterHomologacaoSolicitacaoCompra.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";

$stPrograma = "ManterHomologacaoSolicitacaoCompra";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

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

$obTComprasSolicitacao = new TComprasSolicitacao;

$stFiltro = $stLink = "";

$stLink .= '&inCodigo='.$_REQUEST['inCodigo'];
$stLink .= "&stAcao=".$stAcao;

$rsLista = new RecordSet;

for ( $x = 0; $x < count($_REQUEST['inCodEntidade']); $x++ ) {
    $entidade.= ",".$_REQUEST['inCodEntidade'][$x];
}

if ($stAcao == 'incluir') {
        $stFiltro .= "AND solicitacao.exercicio = '".Sessao::getExercicio()."' \n";
        $stFiltro .= "AND (NOT EXISTS ( SELECT 1
                                          FROM compras.solicitacao_homologada
                                         WHERE solicitacao_homologada.cod_entidade = solicitacao.cod_entidade
                                           AND solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
                                           AND solicitacao_homologada.exercicio = solicitacao.exercicio
                                      )
                            OR EXISTS ( SELECT 1
                                 FROM compras.solicitacao_homologada_anulacao
                                WHERE solicitacao_homologada_anulacao.cod_entidade = solicitacao.cod_entidade
                                  AND solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                  AND solicitacao_homologada_anulacao.exercicio = solicitacao.exercicio
                          ))";
} else {
    $stFiltro .= " AND solicitacao.exercicio = '".Sessao::getExercicio()."' \n";
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM compras.solicitacao_homologada
                                 WHERE solicitacao_homologada.cod_entidade = solicitacao.cod_entidade
                                   AND solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
                                   AND solicitacao_homologada.exercicio = solicitacao.exercicio
                              )";
}

//// filtrando as homogações que ainda não foram pro mapa
if ($stAcao == 'anular') {

    if ($_REQUEST["stSolicitacao"] == "") {
        $stFiltro .= "
               AND  ((( SELECT coalesce(SUM(mapa_item_anulacao.quantidade),0)
                         FROM compras.mapa_item_anulacao
                        WHERE mapa_item_anulacao.exercicio_solicitacao = solicitacao.exercicio
                          AND mapa_item_anulacao.cod_entidade  = solicitacao.cod_entidade
                          AND mapa_item_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                      )
                     -
                     ( SELECT coalesce(SUM(mapa_item.quantidade),0)
                          FROM compras.mapa_item
                         WHERE mapa_item.exercicio_solicitacao = solicitacao.exercicio
                           AND mapa_item.cod_entidade  = solicitacao.cod_entidade
                           AND mapa_item.cod_solicitacao = solicitacao.cod_solicitacao
                     )
                     ) = 0
                     OR
                     ( SELECT coalesce(SUM(mapa_item.quantidade),0)
                         FROM compras.mapa_item
                        WHERE mapa_item.exercicio_solicitacao = solicitacao.exercicio
                          AND mapa_item.cod_entidade  = solicitacao.cod_entidade
                          AND mapa_item.cod_solicitacao = solicitacao.cod_solicitacao
                     ) = 0

                    )

    AND NOT EXISTS  (
                        SELECT  1
                          FROM  compras.solicitacao_homologada_anulacao
                         WHERE  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                           AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao.cod_entidade
                           AND  solicitacao_homologada_anulacao.exercicio       = solicitacao.exercicio
                    )

        AND EXISTS  (
                        SELECT  1
                          FROM  compras.solicitacao_homologada
                         WHERE  solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
                           AND  solicitacao_homologada.cod_entidade      = solicitacao.cod_entidade
                           AND  solicitacao_homologada.exercicio         = solicitacao.exercicio
                    )";

    if ($_REQUEST["inCodEntidade"]) {
        $stFiltro .= " AND  entidade.cod_entidade IN (".substr($entidade,1).") \n";
    }

    if ($_REQUEST["stSolicitacao"]) {
        $stFiltro .= " AND  solicitacao.cod_solicitacao = ".$_REQUEST["stSolicitacao"]. "\n";
    }

    if ($_REQUEST["stObjeto"]) {
        $stFiltro .= " AND  solicitacao.cod_objeto = ".$_REQUEST["stObjeto"]." \n";
    }

    $stTmpFiltroItem = "
                    SELECT  1
                      FROM  compras.solicitacao_item
                     WHERE  solicitacao_item.exercicio       = solicitacao.exercicio
                       AND  solicitacao_item.cod_entidade    = solicitacao.cod_entidade
                       AND  solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao ";

    if ($_REQUEST["inCodItem"]) {
        $stTmpFiltroItem .= "\n AND  solicitacao_item.cod_item = " . $_REQUEST["inCodItem"];
        $stFiltroItens  = $stTmpFiltroItem;
    }

    if ($_REQUEST['inCodDotacao']) {
        $stFiltro.="     AND EXISTS( SELECT 1                                                                       \n";
        $stFiltro.="                   FROM compras.solicitacao_item_dotacao                                        \n";
        $stFiltro.="                  WHERE solicitacao_item_dotacao.cod_solicitacao = solicitacao.cod_solicitacao  \n";
        $stFiltro.="                    AND solicitacao_item_dotacao.cod_entidade    = solicitacao.cod_entidade     \n";
        $stFiltro.="                    AND solicitacao_item_dotacao.cod_despesa     = ".$_REQUEST['inCodDotacao']."\n";
        $stFiltro.="                    AND solicitacao_item_dotacao.exercicio       = solicitacao.exercicio    )   \n";
    }

    if ($_REQUEST["inCodCentroCusto"]) {
        $stTmpFiltroItem .= "\n AND  solicitacao_item.cod_centro = ".$_REQUEST["inCodCentroCusto"];
        $stFiltroItens  = $stTmpFiltroItem;
    }

    /// filtro por datas
    if ($_REQUEST['stDataInicial']) {
        $dtDataInicial = $_REQUEST["stDataInicial"];
        $dtDataFinal   = $_REQUEST["stDataFinal"]  ;

        $stFiltro .= "  AND  TO_DATE(solicitacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')   \n";
        $stFiltro .= "  AND  TO_DATE('".$dtDataFinal."','dd/mm/yyyy')        \n";

        //$stFiltro .= " and to_char ( solicitacao.timestamp, 'dd/mm/yyyy' ) >=  '$dtDataInicial' AND to_char( solicitacao.timestamp, 'dd/mm/yyyy' ) <= '$dtDataFinal' ";
    }

    if ($stFiltroItens) {
        $stFiltro .=  "\n AND EXISTS  ( $stFiltroItens ) ";
    }

    if ($filtro["inPeriodicidade"] == "4") {
        if ($filtro["stDataInicial"]) {
            $dtDataInicial = $filtro["stDataInicial"];
            $dtDataFinal   = $filtro["stDataFinal"];

            $stFiltro .= "  AND  solicitacao.timestamp BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')       \n";
            $stFiltro .= "  AND  TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                       \n";
        }
    }

    $stFiltro .= "UNION
                    SELECT  solicitacao.exercicio
                         ,  solicitacao.cod_entidade
                         ,  solicitacao.cod_solicitacao
                         ,  TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data
                         ,  solicitacao.timestamp
                         ,  solicitacao.cod_objeto
                         ,  sw_cgm.nom_cgm
                         ,  total_itens.quantidade
                         ,  total_itens.vl_total
                         ,  total_anulacoes.quantidade as quantidade_anulada
                         ,  total_anulacoes.vl_total  as vl_anulado

                      FROM  compras.solicitacao

                INNER JOIN  orcamento.entidade
                        ON  (solicitacao.cod_entidade = entidade.cod_entidade
                       AND   solicitacao.exercicio    = entidade.exercicio )

                INNER JOIN  sw_cgm
                        ON  (entidade.numcgm = sw_cgm.numcgm )

                      ---- consulta para totalizar os itens
                INNER JOIN  (
                                SELECT  solicitacao_item.exercicio
                                     ,  solicitacao_item.cod_entidade
                                     ,  solicitacao_item.cod_solicitacao
                                     ,  sum (solicitacao_item.quantidade) as quantidade
                                     ,  sum (solicitacao_item.vl_total) as vl_total

                                  FROM  compras.solicitacao_item

                              GROUP BY  solicitacao_item.exercicio
                                     ,  solicitacao_item.cod_entidade
                                     ,  solicitacao_item.cod_solicitacao
                            ) as total_itens
                        ON  ( solicitacao.exercicio       = total_itens.exercicio
                       AND  solicitacao.cod_entidade    = total_itens.cod_entidade
                       AND  solicitacao.cod_solicitacao = total_itens.cod_solicitacao  )
                      ---- consulta para totalizar as anulações
                 LEFT JOIN  (
                                SELECT  solicitacao_item_anulacao.exercicio
                                     ,  solicitacao_item_anulacao.cod_entidade
                                     ,  solicitacao_item_anulacao.cod_solicitacao
                                     ,  sum (solicitacao_item_anulacao.quantidade ) as quantidade
                                     ,  sum (solicitacao_item_anulacao.vl_total   ) as vl_total

                                  FROM  compras.solicitacao_item_anulacao

                              GROUP BY  solicitacao_item_anulacao.exercicio
                                     ,  solicitacao_item_anulacao.cod_entidade
                                     ,  solicitacao_item_anulacao.cod_solicitacao
                            ) as total_anulacoes
                        ON  (  solicitacao.exercicio       = total_anulacoes.exercicio
                       AND     solicitacao.cod_entidade    = total_anulacoes.cod_entidade
                       AND     solicitacao.cod_solicitacao = total_anulacoes.cod_solicitacao)

                     WHERE  1=1

            AND NOT EXISTS  (
                                SELECT  1
                                  FROM  compras.mapa_solicitacao
                                 WHERE  mapa_solicitacao.cod_solicitacao = solicitacao.cod_solicitacao
                                   AND  mapa_solicitacao.cod_entidade    = solicitacao.cod_entidade
                                   AND  mapa_solicitacao.exercicio       = solicitacao.exercicio
                            )

            AND NOT EXISTS  (
                                SELECT  1
                                  FROM  compras.solicitacao_homologada_anulacao
                                 WHERE  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                   AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao.cod_entidade
                                   AND  solicitacao_homologada_anulacao.exercicio       = solicitacao.exercicio
                            )

                AND EXISTS  (
                                SELECT  1
                                  FROM  compras.solicitacao_homologada
                                 WHERE  solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
                                   AND  solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
                                   AND  solicitacao_homologada.exercicio       = solicitacao.exercicio
                            )

                       AND  compras.solicitacao.exercicio = '".Sessao::getExercicio()."' \n";
    } else {
        $stFiltro .= "
            AND NOT EXISTS  (
                                SELECT  1
                                  FROM  compras.solicitacao_homologada_anulacao
                                 WHERE  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                   AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao.cod_entidade
                                   AND  solicitacao_homologada_anulacao.exercicio       = solicitacao.exercicio
                            )";
    }
}
# Final Anular.

if ($_REQUEST["inCodEntidade"]) {
    $stFiltro .= " AND entidade.cod_entidade IN (".substr($entidade,1).") \n";
}

if ($_REQUEST["stSolicitacao"]) {
    $stFiltro .= " AND solicitacao.cod_solicitacao = ".$_REQUEST["stSolicitacao"]. "\n";
}

if ($_REQUEST["stObjeto"]) {
    $stFiltro .= " AND solicitacao.cod_objeto = ".$_REQUEST["stObjeto"]." \n";
}

$stTmpFiltroItem = "select 1
                  from compras.solicitacao_item
                 where solicitacao_item.exercicio       = solicitacao.exercicio
                   and solicitacao_item.cod_entidade    = solicitacao.cod_entidade
                   and solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao";

if ($_REQUEST["inCodItem"]) {
    $stTmpFiltroItem .= "\n and solicitacao_item.cod_item = " . $_REQUEST["inCodItem"];
    $stFiltroItens  = $stTmpFiltroItem;
}

if ($_REQUEST['inCodDotacao']) {
    $stFiltro.="     AND EXISTS( SELECT 1                                                                       \n";
    $stFiltro.="                   FROM compras.solicitacao_item_dotacao                                        \n";
    $stFiltro.="                  WHERE solicitacao_item_dotacao.cod_solicitacao = solicitacao.cod_solicitacao  \n";
    $stFiltro.="                    AND solicitacao_item_dotacao.cod_entidade    = solicitacao.cod_entidade     \n";
    $stFiltro.="                    AND solicitacao_item_dotacao.cod_despesa     = ".$_REQUEST['inCodDotacao']."\n";
    $stFiltro.="                    AND solicitacao_item_dotacao.exercicio       = solicitacao.exercicio    )   \n";
}

if ($_REQUEST["inCodCentroCusto"]) {
    $stTmpFiltroItem .= "\n and solicitacao_item.cod_centro = ".$_REQUEST["inCodCentroCusto"];
    $stFiltroItens  = $stTmpFiltroItem;
}

/// filtro por datas
if ($_REQUEST['stDataInicial']) {
    $dtDataInicial = $_REQUEST["stDataInicial"];
    $dtDataFinal   = $_REQUEST["stDataFinal"]  ;

    $stFiltro .= "  AND TO_DATE(solicitacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')   \n";
    $stFiltro .= "  AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy')        \n";
}

if ($stFiltroItens) {
    $stFiltro .=  "\n and exists ( $stFiltroItens ) ";
}

if ($filtro["inPeriodicidade"] == "4") {
    if ($filtro["stDataInicial"]) {
        $dtDataInicial = $filtro["stDataInicial"];
        $dtDataFinal   = $filtro["stDataFinal"];

        $stFiltro .= "  AND solicitacao.timestamp BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')       \n";
        $stFiltro .= "  AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                       \n";
    }
}

$stOrdem.= " ORDER BY cod_solicitacao DESC, timestamp, cod_entidade ASC  \n";

$obTComprasSolicitacao->recuperaSolicitacoesSaldos ($rsLista ,$stFiltro,$stOrdem);

$obLista = new Lista;
$obLista->setAjuda('UC-03.04.02');
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Solicitação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 60 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 28 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_solicitacao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "[cod_entidade] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "data" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&cod_solicitacao", "cod_solicitacao"   );
$obLista->ultimaAcao->addCampo("&cod_entidade"   , "cod_entidade"      );
$obLista->ultimaAcao->addCampo("&exercicio"      , "exercicio"         );
$obLista->ultimaAcao->addCampo("&cod_objeto"     , "cod_objeto"        );

if ($stAcao == "anular") {
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'incluir') {
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
