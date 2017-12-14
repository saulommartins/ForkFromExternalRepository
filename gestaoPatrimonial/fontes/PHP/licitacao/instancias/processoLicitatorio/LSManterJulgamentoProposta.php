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
    * Página de Filtro de fornecedor
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: LSManterJulgamentoProposta.php 62339 2015-04-24 20:31:35Z arthur $

    * Casos de uso: uc-03.05.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( TLIC."TLicitacaoEdital.class.php" );

$stPrograma = "ManterJulgamentoProposta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTLicitacaoEdital = new TLicitacaoEdital();

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $request->get("stAcao");

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

//MANTEM FILTRO E PAGINACAO
$stLink = "&stAcao=".$stAcao;
if ( isset($_GET["pg"]) and  isset($_GET["pos"]) ) {
    Sessao::write("pg"  , $_GET["pg"]);
    Sessao::write("pos" , $_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('link')) ) {
    $_REQUEST = Sessao::read('link');
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write("link", $link);
}

if ($_REQUEST['stExercicioLicitacao']) {
    $obTLicitacaoEdital->setDado( 'exercicio_licitacao', $_REQUEST['stExercicioLicitacao'] );
}

if ( count($_REQUEST['inCodEntidade']) > 0 ) {
    $obTLicitacaoEdital->setDado( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
}

if ($_REQUEST['inCodModalidade']) {
    $obTLicitacaoEdital->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
}

if ($_REQUEST['inCodLicitacao']) {
    $obTLicitacaoEdital->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
}

if ($_REQUEST['stChaveProcesso']) {
    $arProcesso = explode('/', $_REQUEST['stChaveProcesso']);
    $obTLicitacaoEdital->setDado( 'cod_processo', intval($arProcesso[0]) );
}

if ($_REQUEST['numEdital']) {
    $arEdital = explode('/',$_REQUEST['numEdital']);
    $obTLicitacaoEdital->setDado( 'num_edital', $arEdital[0] );
}
if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode('/', $_REQUEST['stMapaCompras'] );
    $obTLicitacaoEdital->setDado( 'cod_mapa', $arMapa[0] );
}

if ($_REQUEST['inCodTipoLicitacao']) {
    $obTLicitacaoEdital->setDado( 'cod_tipo_licitacao', $_REQUEST['inCodTipoLicitacao'] );
}

if ($_REQUEST['inCodCriterio']) {
    $obTLicitacaoEdital->setDado( 'cod_criterio', $_REQUEST['inCodCriterio'] );
}

if ($_REQUEST['inCodTipoObjeto']) {
    $obTLicitacaoEdital->setDado( 'cod_tipo_objeto', $_REQUEST['inCodTipoObjeto'] );
}

if ($_REQUEST['stObjeto']) {
    $obTLicitacaoEdital->setDado( 'cod_objeto', $_REQUEST['stObjeto'] );
}

if ($_REQUEST['inCodComissao']) {
    $obTLicitacaoEdital->setDado( 'cod_comissao', $_REQUEST['inCodComissao'] );
}

$stFiltro = "  ----- este filtro serve para exlcuir da listagem os mapas que forem por lote ou global e tenha fornecedores que não cotaram todos os itens de um lote para o qual fizeram proposta
              and ( mapa.cod_tipo_licitacao = 1 or  not exists ( SELECT lotes.*
                                                                   FROM ( SELECT cotacao_item.exercicio
                                                                               , cotacao_item.cod_cotacao
                                                                               , cotacao_item.lote
                                                                               , count ( cotacao_item.cod_item ) as qtd_itens
                                                                            FROM compras.cotacao_item
                                                                        GROUP BY cotacao_item.exercicio
                                                                               , cotacao_item.cod_cotacao
                                                                               , cotacao_item.lote
                                                                        ) AS lotes
                                                                     JOIN ( SELECT cotacao_fornecedor_item.exercicio
                                                                                 , cotacao_fornecedor_item.cod_cotacao
                                                                                 , cotacao_fornecedor_item.lote
                                                                                 , cotacao_fornecedor_item.cgm_fornecedor
                                                                                 , count ( cotacao_fornecedor_item.cod_item
                                                                          ) AS qtd_itens
                                                                       FROM compras.cotacao_fornecedor_item
                                                                   GROUP BY cotacao_fornecedor_item.exercicio
                                                                          , cotacao_fornecedor_item.cod_cotacao
                                                                          , cotacao_fornecedor_item.lote
                                                                          , cotacao_fornecedor_item.cgm_fornecedor
                                                                    ) AS fornecedor_lotes
                                                                     ON lotes.exercicio   = fornecedor_lotes.exercicio
                                                                    AND lotes.cod_cotacao = fornecedor_lotes.cod_cotacao
                                                                    AND lotes.lote        = fornecedor_lotes.lote 
                                                                  WHERE lotes.qtd_itens > fornecedor_lotes.qtd_itens
                                                                    AND lotes.cod_cotacao = mapa_cotacao.cod_cotacao
                                                                    AND lotes.exercicio   = mapa_cotacao.exercicio_cotacao )  )";
if ($_REQUEST['stAcao'] != 'reemitir') {
    // filtro para excluir da listagem os julgamentos já adjudicados
    $stFiltro.= "    AND NOT EXISTS ( SELECT 1
                                        FROM licitacao.adjudicacao
                                       WHERE adjudicacao.cod_licitacao       = ll.cod_licitacao
                                         AND adjudicacao.exercicio_licitacao = ll.exercicio
                                         AND adjudicacao.cod_modalidade      = ll.cod_modalidade
                                         AND adjudicacao.cod_entidade        = ll.cod_entidade
                                         AND adjudicacao.adjudicado          = true
                                    )";
}

          //  -- Não pode pegar a cotação que já esteja anulada, nem julgar o que já foi excluido.
    $stFiltro.= "AND NOT EXISTS
                (
                    SELECT  1
                      FROM  compras.cotacao_anulada
                     WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                )";

if ($_REQUEST['stAcao'] != 'reemitir') {
        //Retirando licitacoes que ja foram anuladas da listagem de manutencoes de proposta
        $stFiltro.= "   AND NOT EXISTS (  SELECT 1
                                            FROM licitacao.licitacao_anulada
                                           WHERE ll.cod_licitacao = licitacao_anulada.cod_licitacao
                                             AND ll.cod_modalidade = licitacao_anulada.cod_modalidade
                                             AND ll.cod_entidade = licitacao_anulada.cod_entidade
                                             AND ll.exercicio = licitacao_anulada.exercicio
                                        )";
}

if (($_REQUEST['stAcao'] != 'excluir') && ($_REQUEST['stAcao'] != 'reemitir')) {
    $stFiltro.= "AND (
                           NOT EXISTS  (
                                          SELECT  1
                                            FROM  compras.mapa_cotacao
                                      INNER JOIN  compras.julgamento
                                              ON  julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                                             AND  julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                                           WHERE  mapa_cotacao.cod_mapa = ll.cod_mapa
                                             AND  mapa_cotacao.exercicio_mapa = ll.exercicio_mapa
                                             AND  NOT EXISTS (
                                                                  SELECT  1
                                                                    FROM  compras.cotacao_anulada
                                                                   WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                                     AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                                       )
                        )
                    )";
}

if (($_REQUEST['stAcao'] == 'excluir') || ($_REQUEST['stAcao'] == 'reemitir')) {
    $stFiltro.= "AND (
                        EXISTS  (
                                     SELECT  1
                                       FROM  compras.mapa_cotacao
                                 INNER JOIN  compras.julgamento
                                         ON  julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                                        AND  julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                                      WHERE  mapa_cotacao.cod_mapa = ll.cod_mapa
                                        AND  mapa_cotacao.exercicio_mapa = ll.exercicio_mapa";

    if ($_REQUEST['stAcao'] == 'excluir') {
            $stFiltro.= "                   AND  NOT EXISTS (
                                                             SELECT  1
                                                               FROM  compras.cotacao_anulada
                                                              WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                                AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                                                            )";
    }
    $stFiltro.= "               )
                    )";
}

$stOrder = "
            ORDER BY
                    le.exercicio DESC,
                    le.num_edital,
                    ll.exercicio DESC,
                    ll.cod_entidade,
                    ll.cod_licitacao,
                    ll.cod_modalidade
";

$obTLicitacaoEdital->setDado( 'acao', $stAcao );

$obTLicitacaoEdital->recuperaLicitacaoDocumentosParticipanteHabilitar( $rsEdital,$stFiltro,$stOrder );

$rsEdital->setCampo( 'cod_processo', str_pad($rsEdital->getCampo( 'cod_processo' ), 5, "0", STR_PAD_LEFT), true );

$obLista = new Lista();

$rsEdital->setPrimeiroElemento();

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsEdital );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Licitação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Edital" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "num_licitacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_entidade] - [entidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[num_edital_lista]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_processo]/[exercicio_processo]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_modalidade] - [descricao]" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->addCampo("&inCodMapa"          , "cod_mapa"          );
$obLista->ultimaAcao->addCampo("&stExercicio"        , "exercicio"         );
$obLista->ultimaAcao->addCampo("&inExercicioCotacao" , "exercicio_cotacao" );
$obLista->ultimaAcao->addCampo("&inCodCotacao"       , "cod_cotacao"       );
$obLista->ultimaAcao->addCampo("&inCodLicitacao"     , "num_licitacao"     );
$obLista->ultimaAcao->addCampo("&inCodModalidade"    , "cod_modalidade"    );
$obLista->ultimaAcao->addCampo("&inCodTipoObjeto"    , "cod_tipo_objeto"   );
$obLista->ultimaAcao->addCampo("&inCodEntidade"      , "cod_entidade"      );

if ($stAcao == 'reemitir') {
    $obLista->ultimaAcao->setAcao( 'selecionar' );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
} else {
    if ( strstr($stAcao,'excluir') ) {
        $obLista->ultimaAcao->setAcao( 'excluir' );
        $obLista->ultimaAcao->addCampo("&inNumEdital" , "num_edital" );
        $obLista->ultimaAcao->addCampo("stDescQuestao"  ,"Edital [num_edital]");
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?stAcao=$stAcao&".Sessao::getId().$stLink."&stTipoJulgamento=licitacao" );
    } else {
        $obLista->ultimaAcao->setAcao( 'selecionar' );
        $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
    }
}

$obLista->setAjuda("UC-03.05.16");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
