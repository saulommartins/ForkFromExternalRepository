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
    * Página de lista de Licitações para Autorização de Empenho Parcial
    * Data de Criação   : 25/09/2015

    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: LSManterAutorizacaoParcial.php 63841 2015-10-22 19:14:30Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoLicitacao.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoHomologacao.class.php';

$stPrograma = "ManterAutorizacaoParcial";
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

$stFiltros .= " AND licitacao.exercicio = '".Sessao::getExercicio()."'                                                                          \n";

if($request->get('inCodEntidade')) 
    $stFiltros .= " AND entidade.cod_entidade = ".$request->get('inCodEntidade')."                                                              \n";

if($request->get('inCodModalidade'))
    $stFiltros .= " AND licitacao.cod_modalidade = ".$request->get('inCodModalidade')."                                                         \n";

if($request->get('inCodigoLicitacao'))
    $stFiltros .= " AND licitacao.cod_licitacao = ".$request->get('inCodigoLicitacao')."                                                        \n";

if($request->get('stDtInicial'))
    $stFiltros .= " AND to_date( licitacao.timestamp::VARCHAR, 'yyyy/mm/dd' ) >= to_date ( '".$request->get('stDtInicial')."' , 'dd/mm/yyyy' )  \n";

if($request->get('stDtFinal'))
    $stFiltros .= " AND to_date( licitacao.timestamp::VARCHAR, 'yyyy/mm/dd' ) <= to_date ( '".$request->get('stDtFinal')."', 'dd/mm/yyyy' )     \n";

if($request->get('inCodMapa'))
    $stFiltros .= " AND mapa_cotacao.cod_mapa = ".$request->get('inCodMapa')."                                                                  \n";

$obTLicitacaoHomolocacao = new TLicitacaoHomologacao;
$obTLicitacaoHomolocacao->recuperaCotacoesParaEmpenhoParcial( $rsCotacoes, $stFiltros );

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
