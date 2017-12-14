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
 * Data de Criação: 13/10/2009

 * @author Analista: 	  Gelson Wolowski
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @ignore

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioInventarioHistoricoBem.class.php";

$obTPatrimonioInventarioHistoricoBem = new TPatrimonioInventarioHistoricoBem;

if (is_numeric($_REQUEST['inIdInventario'])) {
    $obTPatrimonioInventarioHistoricoBem->setDado('id_inventario' , $_REQUEST['inIdInventario']);
    $stLink .= "&inIdInventario=".$_REQUEST['inIdInventario'];
}

if (isset($_REQUEST['stExercicio'])) {
    $obTPatrimonioInventarioHistoricoBem->setDado('exercicio' , $_REQUEST['stExercicio']);
    $stLink .= "&stExercicio='".$_REQUEST['stExercicio']."'";
}

if (is_numeric($_REQUEST['inCodOrgao'])) {
    $obTPatrimonioInventarioHistoricoBem->setDado('cod_orgao' , $_REQUEST['inCodOrgao']);
    $stLink .= "&inCodOrgao=".$_REQUEST['inCodOrgao'];
}

if (is_numeric($_REQUEST['inCodLocal'])) {
    $obTPatrimonioInventarioHistoricoBem->setDado('cod_local' , $_REQUEST['inCodLocal']);
    $stLink .= "&inCodLocal=".$_REQUEST['inCodLocal'];
}

$obTPatrimonioInventarioHistoricoBem->recuperaBemHistoricoInventario($rsBemHistoricoInventario, $stFiltro, $stOrder);

$obLista = new Lista;
$obLista->setTitulo("Histórico Atual do Bem no Patrimônio");
$obLista->setMostraPaginacao(true);
$obLista->obPaginacao->setFiltro("&stLink=".$stLink);
$obLista->setRecordSet($rsBemHistoricoInventario);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Bem");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Órgão Atual");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Local Atual");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação Atual");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Novo Órgão");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Novo Local");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nova Situação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Modificado pelo Inventário");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo("cod_bem");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_bem");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_orgao");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_local");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_situacao");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_orgao_novo");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_local_novo");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("nom_situacao_novo");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo("modificado");
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->commitDado();

$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
