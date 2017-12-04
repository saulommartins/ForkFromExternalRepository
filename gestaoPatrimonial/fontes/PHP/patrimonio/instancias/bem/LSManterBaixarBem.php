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
  * Data de Criação: 27/09/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Henrique Boaventura

  * @package URBEM
  * @subpackage

  * Casos de uso: uc-03.01.06

  $Id: LSManterBaixarBem.php 64857 2016-04-07 19:41:03Z arthur $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBemBaixado.class.php";

$stPrograma = "ManterBaixarBem";
$pgFilt		= "FL".$stPrograma.".php";
$pgList		= "LS".$stPrograma.".php";
$pgForm		= "FM".$stPrograma.".php";
$pgProc		= "PR".$stPrograma.".php";
$pgOcul		= "OC".$stPrograma.".php";
$pgJs		= "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');

# Seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."bem/";

$arFiltro = Sessao::read('filtro');

# Seta o filtro na sessao e vice-versa
if (!Sessao::read('paginando')) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($request->get('pg') ? $request->get('pg') : 0));
    Sessao::write('pos',($request->get('pos') ? $request->get('pos') : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg' ,$request->get('pg'));
    Sessao::write('pos',$request->get('pos'));
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $request->set($key,$value);
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

if ($request->get('inCodBemInicio') != '' && $request->get('inCodBemFinal') == '') {
    $stFiltro .= " bem_baixado.cod_bem = ".$request->get('inCodBemInicio')." AND ";
}

if ($request->get('inCodBemInicio') != '' && $request->get('inCodBemFinal') != '') {
    $stFiltro .= " bem_baixado.cod_bem BETWEEN ".$request->get('inCodBemInicio')." AND ".$request->get('inCodBemFinal')." AND ";
}

if ($request->get('stDataInicial') != '' && $request->get('stDataFinal')) {
    $stFiltro .= " bem_baixado.dt_baixa BETWEEN TO_DATE('".$request->get('stDataInicial')."','DD/MM/YYYY') AND TO_DATE('".$request->get('stDataFinal')."','DD/MM/YYYY') AND ";
}

if ($request->get('inTipoBaixa') != '' ) {
    $stFiltro .= " bem_baixado.tipo_baixa = ".$request->get('inTipoBaixa')." AND ";
}

if ($stFiltro != '') {
    $stFiltro = " WHERE ".substr($stFiltro,0,-4);
}

$stGrupo = " \n GROUP BY bem_baixado.dt_baixa
                       , bem_baixado.motivo
                       , bem_baixado.tipo_baixa 
                       , descricao_baixa
                       , somatorio_bem_baixado.valor_atualizado ";

$stOrder = " \n ORDER BY bem_baixado.dt_baixa
                       , bem_baixado.tipo_baixa ";

$obTPatrimonioBemBaixado = new TPatrimonioBemBaixado();
$obTPatrimonioBemBaixado->recuperaBemBaixadoResumo( $rsBem, $stFiltro, $stGrupo, $stOrder);

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda('UC-03.01.06');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsBem );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data da Baixa" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Baixa" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Motivo" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor do Lançamento" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_baixa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "descricao_baixa" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "motivo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor_atualizado" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "consultar" );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "javascript:consultarBensBaixados();" );
$obLista->ultimaAcao->addCampo("1", "cod_tipo");
$obLista->ultimaAcao->addCampo("2", "dt_baixa");
$obLista->ultimaAcao->addCampo("3", "motivo");
$obLista->commitAcao();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodTipoBaixa" , "tipo_baixa" );
$obLista->ultimaAcao->addCampo( "&stDataBaixa"    , "dt_baixa"   );
$obLista->ultimaAcao->addCampo( "&stMotivo"       , "motivo" );
$obLista->ultimaAcao->addCampo( "&stDescQuestao"  , "motivo" );
$obLista->ultimaAcao->setLink($stCaminho.$pgProc.'?'.Sessao::getId().$stLink);
$obLista->commitAcao();

$obLista->show();

?>