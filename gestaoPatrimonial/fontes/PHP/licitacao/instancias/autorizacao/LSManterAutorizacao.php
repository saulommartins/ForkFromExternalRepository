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
    * Página de lista do fornecedor
    * Data de Criação   : 10/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

     $Id: LSManterAutorizacao.php 63584 2015-09-14 13:11:00Z michel $

    * Casos de uso: uc-uc-03.05.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php"  );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php");

$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//filtros
$arFiltro = Sessao::read('filtro');

$pg  = $request->get('pg', 0);
$pos = $request->get('pos', 0);

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg', $pg);
    Sessao::write('pos', $pos);
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$pg);
    Sessao::write('pos',$pos);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

$request = new Request($_REQUEST);

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

///////// montando filtros

$stFiltros .= " AND licitacao.exercicio = '" . Sessao::getExercicio() . "'";

if ($request->get('inCodEntidade')) {
    $stFiltros .= " AND entidade.cod_entidade = ".$request->get('inCodEntidade');
}

if ($request->get('inCodModalidade')) {
    $stFiltros .= " AND licitacao.cod_modalidade = ".$request->get('inCodModalidade');
}

if ($request->get('inCodigoLicitacao')) {
    $stFiltros .= " AND licitacao.cod_licitacao = ".$request->get('inCodigoLicitacao');
}

if ($request->get('stDtInicial')) {
    $stFiltros .= " AND to_date( licitacao.timestamp::VARCHAR, 'yyyy/mm/dd' ) >= to_date ( '".$request->get('stDtInicial')."' , 'dd/mm/yyyy' )     ";
}

if ($request->get('stDtFinal')) {
    $stFiltros .= " AND to_date( licitacao.timestamp::VARCHAR, 'yyyy/mm/dd' ) <=  to_date ( '".$request->get('stDtFinal')."', 'dd/mm/yyyy' )   ";
}

if ($request->get('inCodMapa')) {
    $stFiltros .= " AND mapa_cotacao.cod_mapa = ".$request->get('inCodMapa');
}

$stFiltros .= "
        AND NOT EXISTS ( SELECT 1
                           FROM licitacao.homologacao
                          WHERE NOT homologacao.homologado
                            AND (NOT EXISTS (SELECT 1
                                               FROM licitacao.homologacao_anulada
                                              WHERE homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                                                AND homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                                                AND homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                                                AND homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                                                AND homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                                                AND homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                                                AND homologacao_anulada.lote                = homologacao.lote
                                                AND homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                                                AND homologacao_anulada.cod_item            = homologacao.cod_item
                                                AND homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                                                AND homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor )
                                )
                            AND homologacao.cod_cotacao       = mapa_cotacao.cod_cotacao
                            AND homologacao.exercicio_cotacao = mapa_cotacao.exercicio_cotacao)

            -- A Licitação não pode estar anulada.
        AND NOT EXISTS ( SELECT	1
                           FROM licitacao.licitacao_anulada
                          WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                            AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                            AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                            AND licitacao_anulada.exercicio      = licitacao.exercicio
                       )

            -- Validação para não existir cotação anulada.
        AND NOT EXISTS ( SELECT 1
                           FROM compras.cotacao_anulada
                          WHERE cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                            AND cotacao_anulada.exercicio   = mapa_cotacao.exercicio_cotacao
                              )

        AND NOT EXISTS ( SELECT 1
                           FROM licitacao.edital_suspenso
                     INNER JOIN licitacao.edital
                             ON edital_suspenso.num_edital = edital.num_edital
                            AND edital_suspenso.exercicio = edital.exercicio
                     INNER JOIN licitacao.licitacao ll
                             ON ll.cod_licitacao = edital.cod_licitacao
                            AND ll.cod_modalidade = edital.cod_modalidade
                            AND ll.cod_entidade = edital.cod_entidade
                            AND ll.exercicio = edital.exercicio
                          WHERE ll.cod_licitacao = licitacao.cod_licitacao
                            AND ll.cod_modalidade = licitacao.cod_modalidade
                            AND ll.cod_entidade = licitacao.cod_entidade
                            AND ll.exercicio = licitacao.exercicio
                       )

        -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
        AND CASE WHEN licitacao.cod_modalidade in (1,2,3,4,5,6,7,10,11)
                 THEN EXISTS ( SELECT 1
                                 FROM licitacao.edital
                                WHERE edital.cod_licitacao = licitacao.cod_licitacao
                                  AND edital.cod_modalidade = licitacao.cod_modalidade
                                  AND edital.cod_entidade = licitacao.cod_entidade
                                  AND edital.exercicio = licitacao.exercicio
                             )
                 -- Para as modalidades 8,9 é facultativo possuir um edital
                 WHEN licitacao.cod_modalidade in (8,9)
                 THEN EXISTS ( SELECT 1
                                 FROM licitacao.edital
                                WHERE edital.cod_licitacao = licitacao.cod_licitacao
                                  AND edital.cod_modalidade = licitacao.cod_modalidade
                                  AND edital.cod_entidade = licitacao.cod_entidade
                                  AND edital.exercicio = licitacao.exercicio
                             )
                   OR NOT EXISTS ( SELECT 1
                                     FROM licitacao.edital
                                    WHERE edital.cod_licitacao = licitacao.cod_licitacao
                                      AND edital.cod_modalidade = licitacao.cod_modalidade
                                      AND edital.cod_entidade = licitacao.cod_entidade
                                      AND edital.exercicio = licitacao.exercicio
                             )
                  END AND EXISTS(SELECT mp.exercicio
                                      , mp.cod_mapa
                                      , mp.cod_objeto
                                      , mp.timestamp
                                      , mp.cod_tipo_licitacao
                                      , solicitacao.registro_precos
                                   FROM compras.mapa AS mp
                             INNER JOIN compras.mapa_solicitacao
                                     ON mapa_solicitacao.exercicio = mp.exercicio
                                    AND mapa_solicitacao.cod_mapa  = mp.cod_mapa
                             INNER JOIN compras.solicitacao_homologada
                                     ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                                    AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                                    AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                             INNER JOIN compras.solicitacao
                                     ON solicitacao.exercicio       = solicitacao_homologada.exercicio
                                    AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                                    AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                                  WHERE mp.cod_mapa = mapa_cotacao.cod_mapa
                                    AND mp.exercicio = mapa_cotacao.exercicio_mapa
                               GROUP BY mp.exercicio
                                      , mp.cod_mapa
                                      , mp.cod_objeto
                                      , mp.timestamp
                                      , mp.cod_tipo_licitacao
                                      , solicitacao.registro_precos)
                 \n " ;

$obTLicitacaoHomolocacao = new TLicitacaoHomologacao;
$obTLicitacaoHomolocacao->recuperaCotacoesParaEmpenho( $rsCotacoes, $stFiltros );

$obLista = new Lista();

$obLista->setRecordSet( $rsCotacoes );

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
$obLista->ultimoCabecalho->addConteudo('Cod. Licitação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Licitação');
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
$obLista->ultimoDado->setCampo( "[cod_licitacao]" );
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
$obLista->ultimaAcao->setAcao( 'selecionar' );
$obLista->ultimaAcao->addCampo( "&inCodCotacao"       , "cod_cotacao"    );
$obLista->ultimaAcao->addCampo( "&inCodEntidade"      , "cod_entidade"   );
$obLista->ultimaAcao->addCampo( "&inCodLicitacao"     , "cod_licitacao"  );
$obLista->ultimaAcao->addCampo( "&inCodModalidade"    , "cod_modalidade" );
$obLista->ultimaAcao->setLink( $pgForm."?stAcao=".$stAcao."&".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();
