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

    * @author Analista     : Diego Barbosa
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * Casos de uso: uc-03.04.01

    $Id: LSManterSolicitacaoCompra.php 65105 2016-04-25 19:30:38Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php"                                    );
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php"									  );

$stCaminho = CAM_GP_COM_INSTANCIAS."solicitacaoCompra/";

$stPrograma = "ManterSolicitacaoCompra";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgCons = "FM".$stPrograma."Consulta.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgFormAnulacao = "FMManterAnulacaoSolicitacaoCompra.php";
$pgRel  = 'FMRelatorio'.$stPrograma.'.php';

if ( isset($_REQUEST['stSolicitacao'] )) {
    foreach ($_REQUEST as $key => $valor) {
        #sessao->link[$key] = $valor;
        $link[$key] = $valor;
    }
    Sessao::write('link', $link);
} else {
    $_REQUEST = Sessao::read('link');
}

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

$obRegra = new TComprasSolicitacao();
$obRegra->setDado('exercicio',Sessao::getExercicio());
$stFiltro = "";
$stLink   = "";

$rsLista = new RecordSet;
$rsSaldo = new RecordSet;

$paginando = Sessao::read('paginando');
$filtro = Sessao::read('filtro');

//filtros
if (!$paginando) {
    foreach ($_POST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
    $pg  = $_GET['pg']  ? $_GET['pg']  : 0;
    $pos = $_GET['pos'] ? $_GET['pos'] : 0;
    $paginando = true;
} else {
    $pg  = $_GET['pg'];
    $pos = $_GET['pos'];
}

if ($filtro) {
    foreach ($filtro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
} else {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
}

$paginando = true;

Sessao::write( 'pg'        , $pg  );
Sessao::write( 'pos'       , $pos );
Sessao::write( 'paginando' , true );

if ($stAcao=="alterar") {
    $stFiltro.="     AND solicitacao.exercicio = '".Sessao::getExercicio()."'                                       \n";
    $stFiltro.="     AND (NOT EXISTS( SELECT 1                                                                  	\n";
    $stFiltro.="                       FROM compras.solicitacao_homologada                                    		\n";
    $stFiltro.="                      WHERE solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao  	\n";
    $stFiltro.="                        AND solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade    	\n";
    $stFiltro.="                        AND solicitacao_homologada.exercicio       = solicitacao.exercicio      ) 	\n";
    $stFiltro.="				AND (SELECT COUNT(1)																\n";
    $stFiltro.="					   FROM compras.solicitacao_item 												\n";
    $stFiltro.="					  WHERE solicitacao_item.exercicio = solicitacao.exercicio 						\n";
    $stFiltro.="					  	AND solicitacao_item.cod_entidade = solicitacao.cod_entidade				\n";
    $stFiltro.="					  	AND solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao)			\n";
    $stFiltro.="			    <>																					\n";
    $stFiltro.="			    	(SELECT COUNT(1)																\n";
    $stFiltro.="			    	   FROM compras.solicitacao_item_anulacao										\n";
    $stFiltro.="			     	  WHERE solicitacao_item_anulacao.exercicio = solicitacao.exercicio				\n";
    $stFiltro.="					  	AND solicitacao_item_anulacao.cod_entidade = solicitacao.cod_entidade		\n";
    $stFiltro.="					  	AND solicitacao_item_anulacao.cod_solicitacao = solicitacao.cod_solicitacao)\n";
    $stFiltro.="OR EXISTS ( SELECT 1
                                    FROM compras.solicitacao_homologada_anulacao
                                   WHERE solicitacao_homologada_anulacao.cod_entidade = solicitacao.cod_entidade
                                     AND solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                     AND solicitacao_homologada_anulacao.exercicio = solicitacao.exercicio
                               ))";
}

if ( is_array ( $filtro['inCodEntidade'] ) ) {
    $entidade = implode( $filtro['inCodEntidade'] , ' , ' );
}

if ( isset($entidade) ) {
    if ($stAcao == "consultar" OR $stAcao == 'reemitir') {
        $stFiltro .= " AND entidade.cod_entidade       IN ( $entidade )             \n";
    } else {
        $stFiltro .= " AND solicitacao.cod_entidade IN ( ".$entidade." ) ";
    }
}

if ($filtro['stSolicitacao'] != "") {
    $stFiltro .= " AND solicitacao.cod_solicitacao = ".$_REQUEST["stSolicitacao"]. "\n";
}

if ($filtro['stObjeto'] != "") {
    $stFiltro .= " AND solicitacao.cod_objeto = ".$_REQUEST["stObjeto"]." \n";
}

if ($filtro['inCodItem'] != "") {
    $stFiltro .="
                    AND EXISTS (
                                SELECT
                                        1
                                FROM
                                        compras.solicitacao_item
                                WHERE
                                        solicitacao_item.exercicio = solicitacao.exercicio
                                        AND solicitacao_item.cod_entidade = solicitacao.cod_entidade
                                        AND solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao
                                        AND solicitacao_item.cod_item = ".$_REQUEST['inCodItem']."
                                )
    ";

}

if ($filtro['inCodCentroCusto']!="") {
    $stFiltro .="
                    AND EXISTS (
                                SELECT
                                        1
                                FROM
                                        compras.solicitacao_item
                                WHERE
                                        solicitacao_item.exercicio = solicitacao.exercicio
                                        AND solicitacao_item.cod_entidade = solicitacao.cod_entidade
                                        AND solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao
                                        AND solicitacao_item.cod_centro = ".$_REQUEST['inCodCentroCusto']."
                                )
    ";
}

if ($filtro['inCodDotacao'] != "") {
    $stFiltro.="     AND EXISTS( SELECT 1                                                                       \n";
    $stFiltro.="                   FROM compras.solicitacao_item_dotacao                                        \n";
    $stFiltro.="                  WHERE solicitacao_item_dotacao.cod_solicitacao = solicitacao.cod_solicitacao  \n";
    $stFiltro.="                    AND solicitacao_item_dotacao.cod_entidade    = solicitacao.cod_entidade     \n";
    $stFiltro.="                    AND solicitacao_item_dotacao.cod_despesa     = ".$_REQUEST['inCodDotacao']."\n";
    $stFiltro.="                    AND solicitacao_item_dotacao.exercicio       = solicitacao.exercicio    )   \n";
}
if ($filtro['inPeriodicidade'] != "") {
    if ($_REQUEST['stDataInicial']) {
        $dtDataInicial = $_REQUEST["stDataInicial"];
        $dtDataFinal   = $_REQUEST["stDataFinal"];

        $stFiltro .= "  AND TO_DATE(solicitacao.timestamp::varchar,'yyyy-mm-dd') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy')   \n";
        $stFiltro .= "  AND TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                   \n";
    }
}

if ($stAcao == "consultar" OR $stAcao == 'reemitir') {
    $stOrdem = " 	GROUP BY solicitacao.exercicio 					           \n";
    $stOrdem.= " 			,solicitacao.cod_entidade 				           \n";
    $stOrdem.= " 			,solicitacao.cod_solicitacao 			           \n";
    $stOrdem.= " 			,solicitacao.timestamp 					           \n";
    $stOrdem.= " 			,sw_cgm.nom_cgm 					               \n";
    $stOrdem.= " 			,solicitante.nom_cgm 					           \n";
    $stOrdem.= " 	ORDER BY solicitacao.cod_entidade,                         \n";
    $stOrdem.= " 	         TO_DATE(solicitacao.timestamp::varchar,'yyyy-mm-dd') DESC, \n";
    $stOrdem.= "             solicitacao.cod_solicitacao DESC                  \n";
    $obRegra->recuperaRelacionamento( $rsLista ,$stFiltro,$stOrdem );
} elseif ($stAcao == "anular") {
    $stFiltro.="     AND solicitacao.exercicio = '".Sessao::getExercicio()."' \n";
    $stOrdem.= " 	ORDER BY solicitacao.cod_entidade, TO_DATE(solicitacao.timestamp::varchar,'yyyy-mm-dd') DESC, solicitacao.cod_solicitacao DESC 		";
    $obRegra->recuperaSolicitacoesNaoAtendidasAnular( $rsLista ,$stFiltro,$stOrdem );
} else {
    $stOrdem.= " 	ORDER BY solicitacao.cod_entidade, TO_DATE(solicitacao.timestamp::varchar,'yyyy-mm-dd') DESC, solicitacao.cod_solicitacao DESC 		";
    $obRegra->recuperaSolicitacoesNaoAtendidasNaoHomologadas( $rsLista ,$stFiltro,$stOrdem );
}

$obLista = new Lista;
$obLista->setAjuda('UC-03.04.01');

$stLink .= '&inCodigo='.$rsLista->getCampo['cod_solicitacao'];
$stLink .= "&stAcao=".$stAcao;

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
if ($stAcao == 'reemitir') {
    $obLista->ultimaAcao->setAcao( 'relatorio' );
} else {
    $obLista->ultimaAcao->setAcao( $stAcao );
}

$obLista->ultimaAcao->addCampo( "&cod_solicitacao"  , "cod_solicitacao" );
$obLista->ultimaAcao->addCampo( "cod_entidade"      , "cod_entidade"    );
$obLista->ultimaAcao->addCampo( "stDescQuestao"     , "cod_solicitacao" );
$obLista->ultimaAcao->addCampo( "exercicio"  	    , "exercicio"       );
$obLista->ultimaAcao->addCampo( "dtSolicitacao"     , "data"            );
$obLista->ultimaAcao->addCampo( "stHoraSolicitacao" , "hora"            );
$obLista->ultimaAcao->addCampo( "boRegistroPreco"   , "registro_precos" );

if ($stAcao == "consultar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgCons."?".Sessao::getId().$stLink );
} elseif ($stAcao == "anular") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgFormAnulacao."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&pos=".$pos."&pg=".$_REQUEST['pg'] );
} elseif ($stAcao == 'reemitir') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgRel.'?'.Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
