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
    * Data de Criação: 13/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 28251 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-27 13:43:36 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/10/17 13:42:13  hboaventura
correção dos arquivos

Revision 1.1  2007/09/18 15:11:04  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioManutencao.class.php");

$stPrograma = "ManterAgendarManutencao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//seta o caminho para a popup de exclusao
$stCaminho = CAM_GP_PAT_INSTANCIAS."manutencao/";

$arFiltro = Sessao::read('filtro');
//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro[$stCampo] = $stValor;
    }
    Sessao::write('pg',($_GET['pg'] ? $_GET['pg'] : 0));
    Sessao::write('pos',($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando',true);
} else {
    Sessao::write('pg',$_GET['pg']);
    Sessao::write('pos',$_GET['pos']);
}

if ($arFiltro) {
    foreach ($arFiltro as $key => $value) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando',true);
Sessao::write('filtro',$arFiltro);

if ($_REQUEST['inCodBem'] != '') {
    $stFiltro .= " AND bem.cod_bem = ".$_REQUEST['inCodBem']." ";
}
if ($_REQUEST['inCodNatureza'] != '') {
    $stFiltro .= " AND bem.cod_natureza = ".$_REQUEST['inCodNatureza']." ";
}
if ($_REQUEST['inCodGrupo'] != '') {
    $stFiltro .= " AND bem.cod_grupo = ".$_REQUEST['inCodGrupo']." ";
}
if ($_REQUEST['inCodEspecie'] != '') {
    $stFiltro .= " AND bem.cod_especie = ".$_REQUEST['inCodEspecie']." ";
}
if ($_REQUEST['inCodEspecie'] != '') {
    $stFiltro .= " AND bem.cod_especie = ".$_REQUEST['inCodEspecie']." ";
}
if ($_REQUEST['stHdnNomBem'] != '') {
    $stFiltro .= " AND bem.descricao ILIKE '".$_REQUEST['stHdnNomBem']."' ";
}
if ($_REQUEST['stPlacaIdentificacao'] == 'nao') {
    $stFiltro .= " AND bem.num_placa IS NULL ";
} elseif ($_REQUEST['stPlacaIdentificacao'] == 'sim') {
    $stFiltro .= " AND bem.num_placa IS NOT NULL ";
}
if ($_REQUEST['stHdnNumeroPlaca'] != '') {
    $stFiltro .= " AND bem.num_placa LIKE '".$_REQUEST['stHdnNumeroPlaca']."' ";
}

if ($stAcao == 'alterar') {
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM patrimonio.manutencao
                                 WHERE manutencao.cod_bem = bem.cod_bem
                              ) ";
}

if ($_REQUEST['stOrdenacao'] == 'codigo') {
    $stOrder = ' ORDER BY  bem.cod_bem ';
} else {
    $stOrder = ' ORDER BY bem.descricao ';
}

if ($stFiltro != '') {
    $stFiltro = ' WHERE '.substr($stFiltro,4);
}

$obTPatrimonioManutencao = new TPatrimonioManutencao();
if ($stAcao == 'incluir') {
    $obTPatrimonioManutencao->recuperaDadosBem( $rsBem, $stFiltro, $stOrder );
} else {
    $stOrder .= ', manutencao.dt_agendamento ';
    $obTPatrimonioManutencao->recuperaBensManutencao( $rsBem, $stFiltro, $stOrder );
}

//instancia uma nova lista
$obLista = new Lista;
$obLista->setAjuda     ('UC-03.01.07');
$stLink .= "&stAcao=".$stAcao;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsBem );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Classificação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número da Placa" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

if ($stAcao == 'alterar') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data de Agendamento" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Descrição" );
$obLista->ultimoCabecalho->setWidth( 55 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_natureza].[cod_grupo].[cod_especie]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_bem" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "num_placa" );
$obLista->commitDado();

if ($stAcao == 'alterar') {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_agendamento" );
    $obLista->commitDado();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo( "descricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

$obLista->ultimaAcao->addCampo( "&inCodBem", "cod_bem");
$obLista->ultimaAcao->addCampo( "&dtAgendamento" , "dt_agendamento" );

$obLista->ultimaAcao->setLink( $stCaminho.$pgForm.'?'.Sessao::getId().$stLink."&pos=".Sessao::read('pos')."&pg=".Sessao::read('pg') );

$obLista->commitAcao();
$obLista->show();
