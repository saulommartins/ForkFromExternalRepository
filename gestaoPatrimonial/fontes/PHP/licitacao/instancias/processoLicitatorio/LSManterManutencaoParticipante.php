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
    * Página de Filtro de lista Participante Licitação
    * Data de Criação: 02/03/2014

    * @author Analista: Gelson Wolowski
    * @author Desenvolvedor: Arthur Cruz

    * @ignore

    * Casos de uso: uc-03.05.16

    $Id: FLManterEdital.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TLIC.'TLicitacaoLicitacao.class.php';

$stPrograma = "ManterManutencaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTLicitacaoLicitacao = new TLicitacaoLicitacao;

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
    $obTLicitacaoLicitacao->setDado( 'exercicio', $_REQUEST['stExercicioLicitacao'] );
}

if ( count($_REQUEST['inCodEntidade']) > 0 ) {
    $obTLicitacaoLicitacao->setDado( 'cod_entidade', implode(',', $_REQUEST['inCodEntidade']) );
}

if ($_REQUEST['inCodModalidade']) {
    $obTLicitacaoLicitacao->setDado( 'cod_modalidade', $_REQUEST['inCodModalidade'] );
}

if ($_REQUEST['inCodLicitacao']) {
    $obTLicitacaoLicitacao->setDado( 'cod_licitacao', $_REQUEST['inCodLicitacao'] );
}

if ($_REQUEST['stChaveProcesso']) {
    $arProcesso = explode('/', $_REQUEST['stChaveProcesso']);
    $obTLicitacaoLicitacao->setDado( 'cod_processo', intval($arProcesso[0]) );
}

if ($_REQUEST['numEdital']) {
    $arEdital = explode('/',$_REQUEST['numEdital']);
    $obTLicitacaoLicitacao->setDado( 'num_edital', $arEdital[0] );
}

if ($_REQUEST['stMapaCompras']) {
    $arMapa = explode('/', $_REQUEST['stMapaCompras'] );
    $obTLicitacaoLicitacao->setDado( 'cod_mapa', $arMapa[0] );
}

if ($_REQUEST['inCodTipoLicitacao']) {
    $obTLicitacaoLicitacao->setDado( 'cod_tipo_licitacao', $_REQUEST['inCodTipoLicitacao'] );
}

if ($_REQUEST['inCodCriterio']) {
    $obTLicitacaoLicitacao->setDado( 'cod_criterio', $_REQUEST['inCodCriterio'] );
}

if ($_REQUEST['inCodTipoObjeto']) {
    $obTLicitacaoLicitacao->setDado( 'cod_tipo_objeto', $_REQUEST['inCodTipoObjeto'] );
}

if ($_REQUEST['stObjeto']) {
    $obTLicitacaoLicitacao->setDado( 'cod_objeto', $_REQUEST['stObjeto'] );
}

if ($_REQUEST['inCodComissao']) {
    $obTLicitacaoLicitacao->setDado( 'cod_comissao', $_REQUEST['inCodComissao'] );
}

$stFiltro = "
            -- A Licitação não pode estar anulada.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.licitacao_anulada
                                 WHERE	licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                   AND  licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                   AND  licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                   AND  licitacao_anulada.exercicio      = licitacao.exercicio
                            )
            -- O Edital não pode estar anulado.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.edital_anulado
                                 WHERE  edital_anulado.num_edital = edital.num_edital
                                   AND 	edital_anulado.exercicio  = edital.exercicio
                            )
            
            -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
            AND CASE WHEN licitacao.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                    
                    edital.cod_licitacao IS NOT NULL
               AND edital.cod_modalidade IS NOT NULL
               AND edital.cod_entidade   IS NOT NULL 
               AND edital.exercicio      IS NOT NULL 

              -- Para as modalidades 8,9 é facultativo possuir um edital
              WHEN licitacao.cod_modalidade in (8,9) THEN
                    
                    edital.cod_licitacao  IS NULL
                 OR edital.cod_modalidade IS NULL
                 OR edital.cod_entidade   IS NULL 
                 OR edital.exercicio      IS NULL 

	         OR edital.cod_licitacao  IS NOT NULL
	         OR edital.cod_modalidade IS NOT NULL
	         OR edital.cod_entidade   IS NOT NULL 
	         OR edital.exercicio      IS NOT NULL 
            END  \n ";

$stOrder = " ORDER BY licitacao.exercicio DESC
                    , licitacao.cod_entidade
                    , licitacao.cod_licitacao
                    , licitacao.cod_modalidade ";

$obTLicitacaoLicitacao->recuperaManutencaoParticipanteLicitacao( $rsLicitacaoParticipante,$stFiltro,$stOrder );

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLicitacaoParticipante );

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

$obLista->ultimaAcao->setLink( $pgForm."?stAcao=".$stAcao.Sessao::getId().$stLink );
    
$obLista->setAjuda("UC-03.05.16");
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>