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

   /**
    * Página de Listagem de Mapa de Compras
    * Data de Criação: 19/03/2007

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * Casos de uso: uc-03.04.33

    $Id: LSManterCompraDireta.php 63408 2015-08-25 17:10:37Z lisiane $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_MAPEAMENTO. 'TComprasCompraDireta.class.php';

$stPrograma = "ManterCompraDireta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
$pgFormConsultar = "FMConsultaCompraDireta.php";
$pgFormPublicar = "FMManterPublicacaoCompraDireta.php";

$arFiltro = Sessao::read('filtro');
//filtros
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
    Sessao::write('filtro', $arFiltro);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

if (is_array($arFiltro)) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

if ( isset($_REQUEST['stMapaCompras'] )) {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link' , $link);
} else {
    $_REQUEST = Sessao::read('link');
}
Sessao::write('inCodHomologada',$_REQUEST['inCodHomologada']);

$stAcao = $_REQUEST["stAcao"] != "" ? $_REQUEST["stAcao"] : Sessao::read("stAcao");

if ($stAcao == 'alterar') {
    $stFiltro .= " NOT EXISTS (    SELECT  1
                                      FROM  compras.mapa_cotacao
                                     WHERE  mapa_cotacao.cod_mapa = compra_direta.cod_mapa
                                       AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
                              ) AND
                   NOT EXISTS (    SELECT  1
                                     FROM  compras.compra_direta_anulacao
                                    WHERE  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                                      AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                      AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                                      AND  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                              ) AND ";
} elseif ($stAcao == 'anular') {
       $stFiltro .= " NOT EXISTS (
                                SELECT  1
                                  FROM  compras.compra_direta_anulacao
                                 WHERE  compra_direta.cod_compra_direta = compra_direta_anulacao.cod_compra_direta
                                   AND  compra_direta.cod_entidade = compra_direta_anulacao.cod_entidade
                                   AND  compra_direta.exercicio_entidade = compra_direta_anulacao.exercicio_entidade
                                   AND  compra_direta.cod_modalidade = compra_direta_anulacao.cod_modalidade
                            )   AND ";
} elseif ($stAcao == 'publicar') {
       $stFiltro .= ' NOT EXISTS( SELECT 1
                                    FROM compras.compra_direta_anulacao
                                   WHERE compra_direta.cod_compra_direta = compra_direta_anulacao.cod_compra_direta
                                     AND compra_direta.cod_entidade = compra_direta_anulacao.cod_entidade
                                     AND compra_direta.exercicio_entidade = compra_direta_anulacao.exercicio_entidade
                                     AND compra_direta.cod_modalidade = compra_direta_anulacao.cod_modalidade) AND ';
}
if($stAcao == 'publicar') {
    if ( count($_REQUEST['inCodEntidade']) > 0 ) {
        $stFiltro .= " compra_direta.cod_entidade in (".implode(',', $_REQUEST['inCodEntidade']).") AND ";
    }
}elseif ($_REQUEST['inCodEntidade']) {
    $stFiltro .= " compra_direta.cod_entidade = ".$_REQUEST['inCodEntidade']." AND ";
}

$stFiltro .= " compra_direta.exercicio_entidade = '".Sessao::getExercicio()."' AND ";

if ($_REQUEST['inCodModalidade']) {
    $stFiltro .= " compra_direta.cod_modalidade = '".$_REQUEST['inCodModalidade']."' AND ";
}

if ($_REQUEST['inCompraDireta']) {
    $stFiltro .= " compra_direta.cod_compra_direta = ".$_REQUEST['inCompraDireta']." AND ";
}

if ($_REQUEST['stMapaCompras']) {

    $arMapa = explode( '/' ,  $_REQUEST['stMapaCompras']  );

    $stFiltro .= " compra_direta.cod_mapa = ".$arMapa[0]. " AND ";
}

if ($_REQUEST['inPeriodicidade']!="") {
    if ($_REQUEST['stDtInicial'] != '') {
        $dtDataInicial = $_REQUEST["stDtInicial"];
        $dtDataFinal   = $_REQUEST["stDtFinal"];

        $stFiltro .= " TO_DATE(compra_direta.timestamp::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$dtDataInicial."','dd/mm/yyyy') AND ";
        $stFiltro .= " TO_DATE('".$dtDataFinal."','dd/mm/yyyy') AND ";
    }
}
if ($_REQUEST['inCodHomologada'] == 2) {
    $stFiltro .=  " homologadas.homologado = 't' AND ";
} else if ($_REQUEST['inCodHomologada'] == 3) {
    $stFiltro .=  " (homologadas.homologado = 'f' OR 
                          NOT EXISTS( SELECT 1 FROM compras.homologacao 
                                              WHERE homologacao.cod_compra_direta = compra_direta.cod_compra_direta
                                                  AND homologacao.cod_entidade = compra_direta.cod_entidade
                                                  AND homologacao.exercicio_compra_direta = compra_direta.exercicio_entidade
                                                  AND homologacao.cod_modalidade = compra_direta.cod_modalidade) 
                            ) AND ";
}
if($stAcao == 'publicar' and $_REQUEST['stChaveProcesso']) {
    $arProcesso = explode('/', $_REQUEST['stChaveProcesso']);
    $stFiltro .= " compra_direta_processo.cod_processo = ".$arProcesso[0]." AND compra_direta_processo.exercicio_processo = '".$arProcesso[1]."' AND ";
}
if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4);
}

$stOrder = "
        ORDER BY    compra_direta.cod_entidade
               ,    compra_direta.timestamp DESC
               ,    compra_direta.cod_compra_direta ASC ";

$obTCompraDireta = new TComprasCompraDireta;
$obTCompraDireta->recuperaCompraDireta( $rsCompraDireta, $stFiltro, $stOrder );

$obLista = new Lista;

$obLista->setRecordSet($rsCompraDireta);

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

if($stAcao == 'reemitir')
    $obLista->ultimaAcao->setAcao( 'relatorio' );
else
    $obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo("&inCodCompraDireta" , "cod_compra_direta"   );
$obLista->ultimaAcao->addCampo("&inCodEntidade", "cod_entidade"  );
$obLista->ultimaAcao->addCampo("&inCodModalidade", "cod_modalidade"  );
$obLista->ultimaAcao->addCampo("&stDtEmissao", "data"  );
$obLista->ultimaAcao->addCampo("&inCodMapa", "cod_mapa"  );
$obLista->ultimaAcao->addCampo("&stExercicioMapa", "exercicio_mapa"  );
$obLista->ultimaAcao->addCampo("&bolHomologado", "homologado"  );
if($stAcao == 'publicar'){
    $obLista->ultimaAcao->addCampo("&stModalidadeDescricao" , "modalidade"   );
    $obLista->ultimaAcao->addCampo("&stNomeEntidade", "entidade"  );
    $obLista->ultimaAcao->addCampo("&entidade_exercicio", "entidade_exercicio"  );
}

if($stAcao == 'reemitir')
    $obLista->ultimaAcao->setLink( $pgProc."?stAcao=$stAcao&".Sessao::getId().$stLink );
elseif($stAcao == 'consultar')
    $obLista->ultimaAcao->setLink( $pgFormConsultar."?stAcao=$stAcao&".Sessao::getId().$stLink );
elseif($stAcao == 'publicar')
    $obLista->ultimaAcao->setLink( $pgFormPublicar."?stAcao=$stAcao&".Sessao::getId().$stLink );
else
    $obLista->ultimaAcao->setLink( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );

$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
