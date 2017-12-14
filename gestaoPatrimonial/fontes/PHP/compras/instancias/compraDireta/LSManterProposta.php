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

    $Id: LSManterProposta.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasCompraDireta.class.php'                                 );

$stPrograma = "ManterProsposta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMManterManutencaoProposta.php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_LIC_INSTANCIAS."processoLicitatorio/";

$stAcao = $request->get('stAcao');
$link = Sessao::read('link');
$stLink = "&stAcao=".$stAcao;

if ( $request->get("pg") and  $request->get("pos") ) {
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('link' , $link);
$stFiltro = isset($stFiltro) ? $stFiltro : null;

if ($stAcao != 'reemitir' && $stAcao != '') {
    $stFiltro .= "
             NOT EXISTS  (  SELECT  1
                              FROM  compras.compra_direta_anulacao
                             WHERE  compra_direta_anulacao.cod_modalidade = compra_direta.cod_modalidade
                               AND  compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                               AND  compra_direta_anulacao.cod_entidade = compra_direta.cod_entidade
                               AND  compra_direta_anulacao.cod_compra_direta = compra_direta.cod_compra_direta
                         )
             AND (
                    NOT EXISTS  (
                                SELECT  1
                                  FROM  compras.mapa_cotacao
                            INNER JOIN  compras.julgamento
                                    ON  julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                                   AND  julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                                 WHERE  mapa_cotacao.cod_mapa = compra_direta.cod_mapa
                                   AND  mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
                                   AND  NOT EXISTS (
                                                    SELECT  1
                                                      FROM  compras.cotacao_anulada
                                                     WHERE  cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                                       AND  cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                                                   )
                                )
                 )
            AND ";
} else {
  $stFiltro .= " NOT EXISTS (   SELECT  1
                              FROM  compras.cotacao_anulada
                              WHERE  cotacao_anulada.cod_cotacao = mapa_cot.cod_cotacao
                              AND  cotacao_anulada.exercicio = mapa_cot.exercicio_cotacao
                          )
            AND compra_direta.exercicio_entidade = '".Sessao::getExercicio()."' AND ";
}

if ($_REQUEST['inCodEntidade']) {
    $stFiltro .= " compra_direta.cod_entidade = ".$_REQUEST['inCodEntidade']." AND ";
}

if ($_REQUEST['inCodModalidade']) {
    $stFiltro .= " compra_direta.cod_modalidade = ".$_REQUEST['inCodModalidade']." AND ";
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
        $stFiltro .= " TO_DATE('".$dtDataFinal."','dd/mm/yyyy')                                   AND ";
    }
}

$stFiltro .= " compra_direta.exercicio_entidade = '".Sessao::getExercicio()."' AND ";

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,0,strlen($stFiltro)-4);
}

$stOrder = "
        ORDER BY    compra_direta.cod_entidade
               ,    compra_direta.timestamp DESC
               ,    compra_direta.cod_compra_direta ASC
";

$obTCompraDireta = new TComprasCompraDireta();
if ($stAcao == 'reemitirCompra') {
    $obTCompraDireta->setDado('reemitirCompra', true);
}
$obTCompraDireta->recuperaCompraDireta( $rsCompraDireta, $stFiltro, $stOrder );

$obLista = new Lista();

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
$obLista->ultimaAcao->setAcao( 'selecionar' );
$obLista->ultimaAcao->addCampo("&inCodMapa" , "cod_mapa");
$obLista->ultimaAcao->addCampo("&stExercicioMapa" , "exercicio_mapa");
$obLista->ultimaAcao->addCampo("&inCodTipoLicitacao" , "cod_tipo_licitacao");
$obLista->ultimaAcao->addCampo("&stExercicio", "exercicio_mapa");
$obLista->ultimaAcao->addCampo("&inCodEntidade", "cod_entidade");
$obLista->ultimaAcao->addCampo("&inCodModalidade", "cod_modalidade");
$obLista->ultimaAcao->addCampo("&stExercicioEntidade", "entidade_exercicio");
$obLista->ultimaAcao->addCampo("&inCompraDireta", "cod_compra_direta");

if ($_REQUEST['stAcao'] != 'reemitir' && $_REQUEST['stAcao'] != '') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&stAcao=dispensaLicitacao" );
} else {
    $obLista->ultimaAcao->setAcao( 'imprimir' );
    $obLista->ultimaAcao->setLink( $stCaminho."PRManterManutencaoProposta.php"."?".Sessao::getId().$stLink."&stAcao=reemitirCompra" );
}

$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
