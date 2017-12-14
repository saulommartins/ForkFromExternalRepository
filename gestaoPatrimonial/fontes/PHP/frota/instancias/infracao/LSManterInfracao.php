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
 * Data de Criação: 29/11/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: LSManterManutencao.php 53500 2012-11-08 19:08:34Z silvia $

 * Casos de uso: uc-03.02.14

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaInfracao.class.php";

$stPrograma = "ManterInfracao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_FRO_INSTANCIAS."infracao/";

$stSql = '';

if ($_REQUEST['inCodVeiculo'] != '') {
    $stSql.= " infracao.cod_veiculo =".$_REQUEST['inCodVeiculo'];
}

if ($_REQUEST['inCodMotorista'] != '') {
    $stSql.= " infracao.cgm_motorista =".$_REQUEST['inCodMotorista'];
}

if ($_REQUEST['stAutoInfracao'] != '') {
    $stSql.= " infracao.auto_infracao = '".$_REQUEST['stAutoInfracao']."'";
}

if ($_REQUEST['dtInfracao'] != '') {
    $stSql.= " infracao.data_infracao = '".SistemaLegado::dataToSql($_REQUEST['stAutoInfracao'])."'";
}

if ($_REQUEST['inCodInfracao'] != '') {
    $stSql.= " infracao.cod_infracao = ".$_REQUEST['inCodInfracao'];
}

if ($stSql != '') {
    $stFiltro = " WHERE ".$stSql;
}

//recupera os dados do banco de acordo com o filtro
$obTFrotaInfracao = new TFrotaInfracao();
$obTFrotaInfracao->recuperaInfracao( $rsFrotaInfracao, $stFiltro, " ORDER BY data_infracao DESC " );

//instancia uma nova lista
$obLista = new Lista;
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsFrotaInfracao );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Auto Infração" );
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 4 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Gravidade" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Motorista" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Veículo" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CENTRO" );
$obLista->ultimoDado->setCampo( "auto_infracao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_infracao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[gravidade] - [pontos]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_motorista] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "nom_modelo" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&id", "id" );
if ($stAcao == 'alterar') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
} elseif ($stAcao == 'excluir') {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();

$obLista->show();

?>
