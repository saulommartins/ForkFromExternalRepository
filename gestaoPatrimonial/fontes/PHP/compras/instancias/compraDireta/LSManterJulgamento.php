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
    * Página de Formulário para julgamento de propastas para mapas dispensados de licitação
    * Data de Criação   :  17/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * Casos de uso: uc-03.05.26, 03.04.31

    $Id: LSManterJulgamento.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasCompraDireta.class.php'                                );

$stPrograma = "ManterJulgamentoProposta";
$pgFilt = "FL".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";

$pgForm = "FMManterJulgamentoProposta.php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

if ( isset($_REQUEST['stMapaCompras'] )) {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link', $link);
} else {
    $_REQUEST = Sessao::read('link');
}

$stAcao = $request->get('stAcao');
$stFiltro .= "
             NOT EXISTS  (  SELECT  1
                              FROM  compras.compra_direta_anulacao
                             WHERE  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                               AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                               AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                               AND  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                         )

            ----- este filtro serve para exlcuir da listagem os mapas que forem por lote ou global e tenha fornecedores que não cotaram todos os itens de um lote para o qual fizeram proposta
             AND ( mapa.cod_tipo_licitacao = 1 OR NOT EXISTS ( SELECT lotes.*
                                                                 FROM ( select cotacao_item.exercicio
                                                                               , cotacao_item.cod_cotacao
                                                                               , cotacao_item.lote
                                                                               , count ( cotacao_item.cod_item ) as qtd_itens
                                                                            from compras.cotacao_item
                                                                          group by cotacao_item.exercicio
                                                                               , cotacao_item.cod_cotacao
                                                                               , cotacao_item.lote ) as lotes
                                                                   join ( select cotacao_fornecedor_item.exercicio
                                                                               , cotacao_fornecedor_item.cod_cotacao
                                                                               , cotacao_fornecedor_item.lote
                                                                               , cotacao_fornecedor_item.cgm_fornecedor
                                                                               , count ( cotacao_fornecedor_item.cod_item ) as qtd_itens
                                                                            from compras.cotacao_fornecedor_item
                                                                          group by cotacao_fornecedor_item.exercicio
                                                                               ,   cotacao_fornecedor_item.cod_cotacao
                                                                               ,   cotacao_fornecedor_item.lote
                                                                               , cotacao_fornecedor_item.cgm_fornecedor ) as fornecedor_lotes
                                                                     on ( lotes.exercicio   = fornecedor_lotes.exercicio
                                                                    and   lotes.cod_cotacao = fornecedor_lotes.cod_cotacao
                                                                    and   lotes.lote        = fornecedor_lotes.lote    )
                                                                  where lotes.qtd_itens > fornecedor_lotes.qtd_itens
                                                                    and lotes.cod_cotacao = mapa_cot.cod_cotacao
                                                                    and lotes.exercicio   = mapa_cot.exercicio_cotacao )  )                  ";

if ($stAcao != "reemitir") {
    $stFiltro .= "
             AND NOT EXISTS
                        (
                            select 1
                              from compras.mapa_cotacao
                              join compras.cotacao
                                on ( mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                               and   mapa_cotacao.exercicio_cotacao = cotacao.exercicio )
                              join empenho.item_pre_empenho_julgamento
                                on ( cotacao.exercicio   = item_pre_empenho_julgamento.exercicio_julgamento
                               and   cotacao.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao )
                             where mapa.cod_mapa = mapa_cotacao.cod_mapa
                               and mapa.exercicio = mapa_cotacao.exercicio_mapa
                        )";
}

    $stFiltro .= "
             AND NOT EXISTS
                        (
                            SELECT  1
                              FROM  compras.cotacao_anulada
                             WHERE  mapa_cot.cod_cotacao       = cotacao_anulada.cod_cotacao
                               AND  mapa_cot.exercicio_cotacao = cotacao_anulada.exercicio
                        ) ";

if ($stAcao == "reemitir") {

    $stFiltro .= "
               AND EXISTS
                        (
                            SELECT  1
                              FROM  compras.julgamento
                             WHERE  julgamento.cod_cotacao = mapa_cot.cod_cotacao
                               AND  julgamento.exercicio   = mapa_cot.exercicio_cotacao
                        ) ";

}

if ($_REQUEST['inCodEntidade']) {
    $stFiltro .= " AND compra_direta.cod_entidade = ".$_REQUEST['inCodEntidade'];
}

if ($_REQUEST['inCodModalidade']) {
    $stFiltro .= " AND compra_direta.cod_modalidade = ".$_REQUEST['inCodModalidade'];
}

if ($_REQUEST['inCompraDireta']) {
    $stFiltro .= " AND compra_direta.cod_compra_direta = ".$_REQUEST['inCompraDireta'];
}

if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode('/', $_REQUEST['stMapaCompras'] );
    $stFiltro .= " AND compra_direta.cod_mapa = ".$arMapa[0];
}

if ($_REQUEST['stIncluirAssinaturas']) {
    Sessao::write('stIncluirAssinaturas', $_REQUEST['stIncluirAssinaturas']);
}

$stFiltro .= "AND compra_direta.exercicio_entidade = '".Sessao::getExercicio()."'";

if ($_REQUEST['inPeriodicidade']!="") {
    if ($_REQUEST['stDtInicial'] != '') {
        $dtDataInicial = $_REQUEST["stDtInicial"];
        $dtDataFinal   = $_REQUEST["stDtFinal"];

        $stFiltro .= " AND TO_DATE(compra_direta.timestamp::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy') ";
        $stFiltro .= " AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy') ";
    }
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,0,strlen($stFiltro) );
}

$stOrder = "
        ORDER BY    compra_direta.cod_entidade
               ,    compra_direta.timestamp DESC
               ,    compra_direta.cod_compra_direta ASC
";

$obTCompraDireta = new TComprasCompraDireta();
$obTCompraDireta->setDado('julgamento', 'true');
$obTCompraDireta->recuperaCompraDireta( $rsCompraDireta, $stFiltro, $stOrder );

$arAux = array();
while ( !$rsCompraDireta->eof() ) {
    $boInclui = false;
    if ( $rsCompraDireta->getCampo( 'cod_tipo_licitacao' ) == 1 ) {
        $stFiltro = "

            WHERE
                EXISTS  (
                              SELECT 1
                                FROM compras.cotacao_fornecedor_item
                          INNER JOIN compras.cotacao_item
                                  ON cotacao_item.exercicio = cotacao_fornecedor_item.exercicio
                                 AND cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                                 AND cotacao_item.cod_item = cotacao_fornecedor_item.cod_item
                                 AND cotacao_item.lote = cotacao_fornecedor_item.lote";

        $stFiltroHomologado = "";
        // Condição: para que possa ser excluido deve ter sido julgado
        if ($stAcao == 'excluir') {

            $stFiltro .= " INNER JOIN compras.julgamento_item
                                  ON cotacao_fornecedor_item.exercicio       = julgamento_item.exercicio
                                 AND cotacao_fornecedor_item.cod_cotacao     = julgamento_item.cod_cotacao
                                 AND cotacao_fornecedor_item.cod_item        = julgamento_item.cod_item
                                 AND cotacao_fornecedor_item.cgm_fornecedor  = julgamento_item.cgm_fornecedor
                                 AND cotacao_fornecedor_item.lote            = julgamento_item.lote";

            $stFiltroHomologado = " AND NOT EXISTS  (
                                                        SELECT 1
                                                          FROM compras.homologacao
                                                         WHERE homologacao.exercicio_cotacao = julgamento_item.exercicio
                                                           AND homologacao.cod_cotacao       = julgamento_item.cod_cotacao
                                                           AND homologacao.cod_item          = julgamento_item.cod_item
                                                           AND homologacao.cgm_fornecedor    = julgamento_item.cgm_fornecedor
                                                           AND homologacao.lote              = julgamento_item.lote
                                                    ) ";
        }

        $stFiltro .="      WHERE cotacao_fornecedor_item.cod_cotacao = mapa_cot.cod_cotacao
                             AND cotacao_fornecedor_item.exercicio = mapa_cot.exercicio_cotacao";
        $stFiltro .= $stFiltroHomologado;

        // Condição: Para poder julgar uma proposta a mesma não pode ter sido julgada ja
        if (($stAcao != 'excluir' ) && ($stAcao != "reemitir")) {
            $stFiltro .= "   AND NOT EXISTS(
                                             SELECT 1
                                               FROM compras.julgamento_item
                                              WHERE cotacao_fornecedor_item.exercicio       = julgamento_item.exercicio
                                                AND cotacao_fornecedor_item.cod_cotacao     = julgamento_item.cod_cotacao
                                                AND cotacao_fornecedor_item.cod_item        = julgamento_item.cod_item
                                                AND cotacao_fornecedor_item.cgm_fornecedor  = julgamento_item.cgm_fornecedor
                                                AND cotacao_fornecedor_item.lote            = julgamento_item.lote
                                            )";
        }

        $stFiltro .= " )

                  AND NOT EXISTS
                    (
                        SELECT  1
                          FROM  compras.cotacao_anulada
                         WHERE  mapa_cot.cod_cotacao       = cotacao_anulada.cod_cotacao
                           AND  mapa_cot.exercicio_cotacao = cotacao_anulada.exercicio
                    )
                  AND compra_direta.cod_compra_direta = ".$rsCompraDireta->getCampo('cod_compra_direta')."
                  AND compra_direta.cod_entidade = ".$rsCompraDireta->getCampo('cod_entidade')."
                  AND compra_direta.cod_modalidade = ".$rsCompraDireta->getCampo('cod_modalidade')."";

        $stFiltro .= " AND compra_direta.exercicio_entidade = '".Sessao::getExercicio()."'";

        $obTCompraDireta->recuperaCompraDireta( $rsAux, $stFiltro );

        if ( $rsAux->getNumLinhas() > 0 ) {
            $boInclui = true;
        }
    } else {
        include_once ( CAM_GP_COM_MAPEAMENTO.'TComprasCotacao.class.php' );
        $stFiltro = " where quantidade_itens = quantidade_itens_fornecedor
                        and mapa_cotacao.cod_mapa = ".$rsCompraDireta->getCampo('cod_mapa')."
                        and mapa_cotacao.exercicio_mapa = '".$rsCompraDireta->getCampo('exercicio_mapa')."'  " ;
        $obTComprasCotacao = new TComprasCotacao;
        $obTComprasCotacao->recuperaQuantidadeItensCotacaoFornecedor( $rsQuanti , $stFiltro );
        if ( $rsQuanti->getNumLinhas() > 0 ) {
            $boInclui = true;
        }
    }

    if ($boInclui) {
        $arAux[] = $rsCompraDireta->arElementos[$rsCompraDireta->getCorrente()-1];
    }
    $rsCompraDireta->proximo();
}

$obLista = new Lista();

$rsCompraDireta->preenche( $arAux );
$rsCompraDireta->setPrimeiroElemento();
$obLista->setRecordSet( $rsCompraDireta );

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Entidade');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Modalidade');
$obLista->ultimoCabecalho->setWidth(25);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Cod. Compra Direta');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Mapa');
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [modalidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_compra_direta]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[data]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[cod_mapa]/[exercicio_mapa]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->addCampo( "&inCodMapa"          , "cod_mapa"          );
$obLista->ultimaAcao->addCampo( "&stExercicio"        , "exercicio_mapa"    );
$obLista->ultimaAcao->addCampo( "&inExercicioCotacao" , "exercicio_cotacao" );
$obLista->ultimaAcao->addCampo( "&inCodCotacao"       , "cod_cotacao"       );
$obLista->ultimaAcao->addCampo( "&stDataEmissao"      , "data"              );
$obLista->ultimaAcao->addCampo( "&inCodCompraDireta"  , "cod_compra_direta" );
$obLista->ultimaAcao->addCampo( "&inCodModalidade"    , "cod_modalidade"    );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"      , "cod_entidade"      );

if ($stAcao == 'reemitir') {
    $obLista->ultimaAcao->setAcao( 'selecionar' );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=".$_REQUEST['stAcao'] );
} else {

    if ($stAcao =='excluir') {
        $obLista->ultimaAcao->setAcao( $stAcao );
        $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"Compra Direta [cod_compra_direta]");
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?stAcao=dispensaLicitacao".($stAcao=='excluir'?'excluir':'')."&".Sessao::getId().$stLink."&stTipoJulgamento=compra_direta" );
    } else {
        $obLista->ultimaAcao->setAcao( 'selecionar' );
        $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?stAcao=dispensaLicitacao".($stAcao=='excluir'?'excluir':'')."&".Sessao::getId().$stLink );
    }
}
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
