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
 * Página de Lista
 * Data de Criação: 09/10/2009

 * @author Analista: 	  Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @ignore

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioInventario.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";

$pgGeraAberturaEncerramento = "OCGeraAberturaEncerramentoInventario.php";
$pgGeraRelatorioInventario = "OCGeraRelatorioInventario.php";

$stCaminho = CAM_GP_PAT_INSTANCIAS."inventario/";
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&stAcao=$stAcao";

$obTInventario = new TPatrimonioInventario;

if (is_numeric($_POST['inCodInventario'])) {
    $obTInventario->setDado('id_inventario' , $_POST['inCodInventario']);
}

if (is_numeric($_POST['stExercicio'])) {
    $stFiltro = " AND inventario.exercicio = '".$_POST['stExercicio']."'";
}

$stOrder = " ORDER BY inventario.id_inventario DESC";

switch ($stAcao) {

    case 'encerramento':
        $obTInventario->setDado('processado' , 'true');
    break;

    case 'alterar'   :
    case 'excluir'   :
    case 'processar' :
        $obTInventario->setDado('processado' , 'false');
    break;

}

$obTInventario->recuperaInventarios($rsInventarios, $stFiltro, $stOrder);

$obLista = new Lista;
$obLista->setTitulo("Lista de Inventário");
$obLista->setMostraPaginacao(true);
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsInventarios );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Exercício" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Inicial" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Final" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("exercicio");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("id_inventario");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("[data_inicio]");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("&nbsp; [data_fim]");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

switch ($stAcao) {

    case 'incluir':
        $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
    break;

    case 'alterar':
        $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
    break;

    case 'excluir':
        $obLista->ultimaAcao->addCampo("&stDescQuestao"  ,"[id_inventario]/[exercicio]");
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
    break;

    case 'abertura':
        $obLista->ultimaAcao->setLink( $stCaminho.$pgGeraAberturaEncerramento."?".Sessao::getId().$stLink );
    break;

    case 'inventario':
        $obLista->ultimaAcao->setAcao( 'inventário' );
        $obLista->ultimaAcao->setLink( $stCaminho.$pgGeraRelatorioInventario."?".Sessao::getId().$stLink );
    break;

    case 'encerramento':
        $obLista->ultimaAcao->setLink( $stCaminho.$pgGeraAberturaEncerramento."?".Sessao::getId().$stLink );
    break;

    case 'processar':
        $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink );
    break;
}

$obLista->ultimaAcao->addCampo("&stExercicio"    , "exercicio");
$obLista->ultimaAcao->addCampo("&inIdInventario" , "id_inventario");
$obLista->ultimaAcao->addCampo("&stDataInicial"  , "data_inicio");
$obLista->ultimaAcao->addCampo("&stDataFinal"    , "data_fim");
$obLista->ultimaAcao->addCampo("&stObservacao"   , "observacao");
$obLista->commitAcao();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
