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
    * Data de Criacao: 25/07/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once( CAM_GT_FIS_NEGOCIO."RFISProcessoFiscal.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISProcessoFiscal.class.php" );

require_once( CAM_GT_FIS_NEGOCIO."RFISIniciarProcessoFiscal.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISIniciarProcessoFiscal.class.php" );

//Instanciando a   Classe de Controle e de Visao
$obController = new RFISProcessoFiscal;
$obVisao = new VFISProcessoFiscal( $obController );

$obControllerIniciarProcessoFiscal = new RFISIniciarProcessoFiscal;
$obVisaoIniciarProcessoFiscal = new VFISIniciarProcessoFiscal( $obControllerIniciarProcessoFiscal );

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterProcesso";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";

$stCaminho   = CAM_GT_FIS_INSTANCIAS."processoFiscal/";

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' :
        $pgProx = $pgForm;
    break;

    case 'cancelar' :
        $pgProx = "FMJustificativaCancelamento.php";
    break;

    case 'encerrar' :
        $pgProx = "FMEncerrarProcesso.php";
    break;
    case 'notificar' :
        $pgProx = "FMNotificarProcesso.php";
    break;
}

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $sessao->link["pg"]  = $_GET["pg"];
    $sessao->link["pos"] = $_GET["pos"];
}

$link = Sessao::read( 'link' );
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write( 'link', $link );
}

$stCondicao = "";
$stGroupBy = "";

$rsFiscal = $obVisao->getFiscalLogado();

if ($rsFiscal->getCampo('administrador') == 'f') {
    $stCondicao = " AND fc.numcgm = " .Sessao::read('numCgm');
}
if ($_REQUEST['inCodProcesso']) {
    $stCondicao.='AND pf.cod_processo = '.$_REQUEST['inCodProcesso'];

}
//Filtros da pesquisa
if ($stAcao == 'cancelar') {
    $where = " fif.dt_inicio is null AND pfc.cod_processo is null AND ftf.cod_processo is null ";
    $where.= $stCondicao;
    $where.= " GROUP BY pf.cod_processo, tf.cod_tipo, tf.descricao, fc.cod_fiscal, fc.numcgm";
} elseif ($stAcao == 'encerrar') {
    $where = " fif.dt_inicio is not null AND pfc.cod_processo is null AND ftf.cod_processo is null ";
    $where.= $stCondicao;
    $where.= " GROUP BY pf.cod_processo, tf.cod_tipo, tf.descricao, fc.cod_fiscal, fc.numcgm";
} else {
    $where = " fif.cod_processo is null  AND  pfc.cod_processo is null ";
    $where.= $stCondicao;
    $where.= " GROUP BY pf.cod_processo, tf.cod_tipo, tf.descricao, fc.cod_fiscal, fc.numcgm";
}

//Switch que monta a pesquisa de acordo com o Tipo de Fiscalização
switch ($_REQUEST['inTipoFiscalizacao']) {
    case 1:
        $rsRecordSet = $obVisaoIniciarProcessoFiscal->recuperarListaProcessoFiscalEconomica( $where.",pfe.inscricao_economica" );
        $stTipoInscricao = "Inscrição Econômica";
    break;
    case 2:
        $rsRecordSet = $obVisaoIniciarProcessoFiscal->recuperarListaProcessoFiscalObra( $where.",pfo.inscricao_municipal" );
        $stTipoInscricao = "Inscrição Municipal";
    break;
    default:
        $condicao = $where.",pfe.inscricao_economica"." # "." where ".$where.",pfo.inscricao_municipal";
        $rsRecordSet = $obVisaoIniciarProcessoFiscal->recuperarListaProcessoFiscaEconomicaObra($condicao);
        $stTipoInscricao = "Inscrição Economica/Municipal";
    break;
}

if (!$rsRecordSet->Eof()) {
    $obInfracoes = $obVisao->InfracoesProcesso($_REQUEST['inCodProcesso']);
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

if ($stAcao == "cancelar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} elseif ($stAcao == "encerrar") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink            );
}

$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
