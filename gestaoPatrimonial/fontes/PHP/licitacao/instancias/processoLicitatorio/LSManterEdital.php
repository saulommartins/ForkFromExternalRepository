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
    * Pagina de Lista para Alterar Edital
    * Data de Criação   : 23/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: LSManterEdital.php 62651 2015-05-28 19:58:48Z jean $

    * Casos de uso: uc-03.05.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC."TLicitacaoEdital.class.php";

$stPrograma = "ManterEdital";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$pgFormPublicar = "FMManterPublicacaoEdital.php";
$pgFormAnular   = "FM".$stPrograma."Anular.php";

$obTLicitacaoEdital = new TLicitacaoEdital();

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

if (is_array(Sessao::read('link'))) {
    $_REQUEST = Sessao::read('link');
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
}

if ($_REQUEST['stExercicioLicitacao']) {
    $exercicioLicitacao = "'".$_REQUEST['stExercicioLicitacao']."'";
    $obTLicitacaoEdital->setDado( 'exercicio_licitacao', $exercicioLicitacao );
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

$stOrder = "
            ORDER BY
                    le.exercicio DESC,
                    le.num_edital,
                    ll.exercicio DESC,
                    ll.cod_entidade,
                    ll.cod_licitacao,
                    ll.cod_modalidade
";

$stFiltro = "
            -- O Edital não pode estar anulado.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.edital_anulado
                                 WHERE  edital_anulado.num_edital = le.num_edital
                                   AND 	edital_anulado.exercicio = le.exercicio
                            )

            -- A Licitação não pode estar anulada.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.licitacao_anulada
                                 WHERE	licitacao_anulada.cod_licitacao  = ll.cod_licitacao
                                   AND  licitacao_anulada.cod_modalidade = ll.cod_modalidade
                                   AND  licitacao_anulada.cod_entidade   = ll.cod_entidade
                                   AND  licitacao_anulada.exercicio      = ll.exercicio
                            ) ";

if ($stAcao == 'anular') {
    $stFiltro.= " AND NOT EXISTS( SELECT 1
                                    from licitacao.homologacao as lh
                                   where ll.cod_licitacao = lh.cod_licitacao
                                     and ll.cod_modalidade = lh.cod_modalidade
                                     and ll.cod_entidade = lh.cod_entidade
                                     and ll.exercicio = lh.exercicio_licitacao
                                )";
}

$obTLicitacaoEdital->recuperaListaEdital( $rsEdital,$stFiltro,$stOrder );

$obLista = new Lista;

$stLink .= "&stAcao=".$stAcao;

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
$obLista->ultimoDado->setCampo( "[num_edital]/[exercicio]" );
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
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inNumEdital"          , "num_edital" );
$obLista->ultimaAcao->addCampo("&stExercicio"          , "exercicio"  );
$obLista->ultimaAcao->addCampo("&stNumEdital"          , "[num_edital]/[exercicio]" );
$obLista->ultimaAcao->addCampo("&stExercicioLicitacao" , "exercicio");
$obLista->ultimaAcao->addCampo("&inCodEntidade"        , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inCodModalidade"      , "cod_modalidade");
$obLista->ultimaAcao->addCampo("&inCodLicitacao"       , "cod_licitacao");
$obLista->ultimaAcao->addCampo("&dtEntrega"            , "dt_entrega_propostas");
$obLista->ultimaAcao->addCampo("&qtdDiasValidade"      , "qtd_dias_validade");
$obLista->ultimaAcao->addCampo("&dtValidade"           , "dt_validade_proposta");
$obLista->ultimaAcao->addCampo("&stHoraEntrega"        , "hora_entrega_propostas");
$obLista->ultimaAcao->addCampo("&stLocalEntrega"       , "local_entrega_propostas");
$obLista->ultimaAcao->addCampo("&dtAbertura"           , "dt_abertura_propostas");
$obLista->ultimaAcao->addCampo("&stHoraAbertura"       , "local_abertura_propostas ");
$obLista->ultimaAcao->addCampo("&stLocalAbertura"      , "hora_abertura_propostas");
$obLista->ultimaAcao->addCampo("&txtCodPagamento"      , "condicoes_pagamento");

if ($stAcao == "anular") {
   $obLista->ultimaAcao->setLink( $stCaminho.$pgFormAnular."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?stAcao=".$stAcao.Sessao::getId().$stLink );
} elseif ($stAcao == 'publicar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormPublicar."?stAcao=".$stAcao.Sessao::getId().$stLink );
} else {
   $obLista->ultimaAcao->setLink( $pgProc."?".Sessao::getId().$stLink."&boGerarDocumento=S" );
}

$obLista->setAjuda("UC-03.05.16");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
