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
    * Página de Filtro de Nota de Transferência
    * Data de Criação   : 16/05/2006

    * @author Analista      : Diego Barbosa Victoria
    * @author Desenvolvedor : Rodrigo

    * @ignore

    * Casos de uso: uc-03.03.08
*/

/*
$Log$
Revision 1.11  2007/09/13 14:50:40  leandro.zis
Ticket#10091#

Revision 1.10  2007/08/06 19:02:21  leandro.zis
Corrigido nota de transferencia

Revision 1.9  2007/07/19 21:44:46  leandro.zis
Bug #9612#, Bug #9604#, Bug #9601#, Bug #9482#, Bug #9614#

Revision 1.8  2007/06/26 20:56:45  bruce
Bug#9482#

Revision 1.7  2006/07/20 21:14:00  fernando
alteração na padronização dos UC

Revision 1.6  2006/07/19 11:44:20  fernando
Inclusão do  Ajuda.

Revision 1.5  2006/07/11 14:45:45  fernando
tamanho do rótulo de ação na lista.

Revision 1.4  2006/07/10 19:26:45  fernando
Retirado o rótulo do cabeçalho Ação da lista.

Revision 1.3  2006/07/06 14:02:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:09:53  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoPedidoTransferencia.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgAnular = "FMAnularNotaTransferencia.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_ALM_INSTANCIAS."notaTransferencia/";

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
    case 'anular':
        $pgProx = $pgAnular; break;
    case 'consultar':
        $pgProx = $pgForm; break;
}

$stFiltro = Sessao::read('stFiltroNotaTransferencia');

if (!is_array($stFiltro)) {
    $stFiltro = $_REQUEST;
    Sessao::write('stFiltroNotaTransferencia', $_REQUEST);
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
   $stLink .= "&pg=".$_GET["pg"];
   $stLink .= "&pos=".$_GET["pos"];
   $link["pg"]  = $_GET["pg"];
   $link["pos"] = $_GET["pos"];
   Sessao::write('link', $link);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if (is_array(Sessao::read('link'))) {
//    $stFiltro = Sessao::read('link');
} else {
    foreach ($stFiltro as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write('link', $link);
}

$obTAlmoxarifadoPedidoTransferencia = new TAlmoxarifadoPedidoTransferencia;

//montando filtro
if ($stFiltro['stExercicio']) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'execicio', $stFiltro['stExercicio'] );
}
if ( count($stFiltro['inCodAlmoxarifadoOrigem']) > 0 ) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'cod_almoxarifado_origem', $stFiltro['inCodAlmoxarifadoOrigem'] );
}

if ($stFiltro['inCodAlmoxarifadoDestino']) {

    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'cod_almoxarifado_destino',$stFiltro['inCodAlmoxarifadoDestino'] );
}

if ($stFiltro['inCodTransferencia']) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'cod_transferencia', $stFiltro['inCodTransferencia'] );
}

if ($stFiltro['stHdnObservacao']) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'observacao', $stFiltro['stHdnObservacao'] );
}

if ($stFiltro['inCodItem']) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'cod_item', $stFiltro['inCodItem'] );
}

if ($stFiltro['inCodMarca']) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'cod_marca', $stFiltro['inCodMarca'] );
}

if ($stFiltro['inCodCentroCusto']) {
    $obTAlmoxarifadoPedidoTransferencia->setDado ( 'cod_centro', $stFiltro['inCodCentroCusto'] );
}

$stFiltro = "";
$stLink   = "";

$obTAlmoxarifadoPedidoTransferencia->recuperaTransferencias($rsLista);

//$obTAlmoxarifadoPedidoTransferencia->debug();

$obLista = new Lista;

$obLista->obPaginacao->setFiltro("&stLink=".$stLink);

$obLista->setRecordSet($rsLista);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth(8);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código");
$obLista->ultimoCabecalho->setWidth(8);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Almoxarifado Origem");
$obLista->ultimoCabecalho->setWidth(55);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Almoxarifado Destino");
$obLista->ultimoCabecalho->setWidth(55);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo("exercicio");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo("cod_transferencia");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo("[cod_almoxarifado_origem] - [nom_almoxarifado_origem]");
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo("[cod_almoxarifado_destino] - [nom_almoxarifado_destino]");
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao($stAcao);
$obLista->ultimaAcao->addCampo("&stExercicio", "exercicio");
$obLista->ultimaAcao->addCampo("&stDescQuestao", "cod_transferencia" );
$obLista->ultimaAcao->addCampo("&inCodTransferencia", "cod_transferencia");

$obLista->ultimaAcao->setLink($pgProx."?".Sessao::getId().$stLink."&stAcao=".$stAcao);
$obLista->commitAcao();
//$obLista->setAjuda             ("UC-03.03.08");
$obLista->Show();
