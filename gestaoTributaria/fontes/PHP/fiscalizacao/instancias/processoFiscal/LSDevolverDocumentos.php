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
    * Página que Lista os Processos Fiscais
    * Data de Criacao: 29/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Aldo Jean Soares Silva

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISDevolverDocumentos.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISDevolverDocumentos.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RFISDevolverDocumentos;
$obVisao = new VFISDevolverDocumentos( $obController );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

//Define o nome dos arquivos PHP
$stPrograma = "DevolverDocumentos";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

$stCaminho  = CAM_GT_FIS_INSTANCIAS."processoFiscal/";

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' : $pgProx = $pgForm; break;
    case 'excluir' : $pgProx = $pgProc; break;
    default        : $pgProx = $pgForm; break;
}

//MANTEM FILTRO E PAGINACAO
//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$link = Sessao::read( 'link' );

if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write( 'link', $link );
}

//Filtros da pesquisa.
$where = $obVisao->filtrosDocumentos( $_POST );
//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($_REQUEST['inTipoFiscalizacao']) {
    case 1:
        $rsRecordSet = $obVisao->recuperarListaInicioFiscalizacaoEconomica( $where );
        $stTipoInscricao = "Inscrição Econômica";
    break;

    case 2:
        $rsRecordSet = $obVisao->recuperarListaInicioFiscalizacaoObra( $where );
        $stTipoInscricao = "Inscrição Municipal";
    break;

    default:
        $rsRecordSet = $obVisao->recuperarListaInicioFiscalizacaoEconomicaObra( $where );
        $stTipoInscricao = "Inscrição Economica/Municipal";
    break;
}

//Lista de Processos
$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Registros de Processo Fiscal' );
$obLista->setRecordSet( $rsRecordSet );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 5        );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Tipo de Fiscalização" );
$obLista->ultimoCabecalho->setWidth   ( 20     );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Processo Fiscal" );
$obLista->ultimoCabecalho->setWidth   ( 20         );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( $stTipoInscricao );
$obLista->ultimoCabecalho->setWidth   ( 20         );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
$obLista->ultimoCabecalho->setWidth   ( 10       );
$obLista->commitCabecalho();
////dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo      ( "[cod_tipo] - [descricao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo      ( "cod_processo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
$obLista->ultimoDado->setCampo      ( "inscricao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao  ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inTipoFiscalizacao" ,"cod_tipo" );
$obLista->ultimaAcao->addCampo( "inCodProcesso" ,"cod_processo" );
$obLista->ultimaAcao->addCampo( "inInscricao" ,"inscricao" );
$obLista->ultimaAcao->addCampo( "inCodFiscal" ,"cod_fiscal" );

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink            );
}
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
