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
    * Data de Criacao: 12/08/2008

    * @author Analista      : Heleno Menezes dos Santos
    * @author Desenvolvedor : Jânio Eduardo Vasconcellos de Magalhães

    * @package URBEM
    * @subpackage

    * @ignore

    *Casos de uso:

    $Id:$
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once( CAM_GT_FIS_NEGOCIO."RFISManterLevantamento.class.php" );
require_once( CAM_GT_FIS_NEGOCIO."RFISProcessoFiscal.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISManterLevantamento.class.php" );
require_once( CAM_GT_FIS_VISAO."VFISProcessoFiscal.class.php" );

//Instanciando a Classe de Controle e de Visao
$obController = new RFISManterLevantamento;
$obVisao = new VFISManterLevantamento( $obController );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$inTipoFiscalizacao = $_GET['inTipoFiscalizacao'] ?  $_GET['inTipoFiscalizacao'] : $_POST['inTipoFiscalizacao'];

//Define o nome dos arquivos PHP
switch ($_REQUEST['bt_faturamento']) {
    case 'servico':
        $stPrograma = "ManterServico";
    break;

    case 'nota':
        $stPrograma = "ManterNota";
    break;

    default:
        $stPrograma = "ManterRetido";
    break;
}

$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".php";
$stCaminho   = CAM_GT_FIS_INSTANCIAS."processoFiscal/";

$pgFMPopup  = "FMManterLevantamento.php";

include_once 'JSManterLevantamento.php';
//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'alterar' :
        $pgProx = $pgForm;
    break;
    case 'excluir' :
        $pgProx = $pgProc;
    break;
    default        :
        //$pgProx = $pgOcul;
        $pgProx = $pgForm;
    break;
}

//MANTEM FILTRO E PAGINACAO
//$stCtrl = "verificaDocumentos";
//MANTEM FILTRO E PAGINACAO

$stUrl = $pgForm;

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

#mantem a ação
$stAcao =  $_REQUEST['stAcao'];
$stLink .= "&stAcao=".$stAcao;
$obControllerProcessoFiscal = new RFISProcessoFiscal;
$obVisaoProcessoFiscal = new VFISProcessoFiscal($obControllerProcessoFiscal);
$rsFiscal = $obVisaoProcessoFiscal->getFiscalLogado();

if ($rsFiscal->getCampo('administrador') == 't') {
      $_REQUEST['numcgm']='';
}
//Filtros da pesquisa.
$where = $obVisao->filtrosProcessoFiscal( $_REQUEST );

$rsProcessoFiscal = $obVisao->recuperarListaProcessoFiscalEconomica( $where );
$stTipoInscricao = "Inscrição Econômica";

$count = count($rsProcessoFiscal->arElementos);
for ($i = 0; $i < $count; $i++) {
    foreach ($rsProcessoFiscal->arElementos[$i] as $ch => $vlr) {
        if ($ch == 'status') {

            if ($vlr != "" || $vlr > 0) {
                $rsProcessoFiscal->arElementos[$i]['link'] = $pgFMPopup;
            } else {
                $rsProcessoFiscal->arElementos[$i]['link'] = $pgForm;
            }
        }
    }
    $rsProcessoFiscal->arElementos[$i]['url'] = $stUrl;
}

//Lista de Processos
$obLista = new Lista;
$obLista->setMostraPaginacao( true );
$obLista->setTitulo( 'Registros de Processo Fiscal' );

$obLista->setRecordSet( $rsProcessoFiscal );

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
$obLista->ultimoCabecalho->addConteudo( "Ação" );
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
$obLista->ultimaAcao->setAcao  ( "cadastrar");
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink  ( "javascript:verificaDocumento();" );
$obLista->ultimaAcao->addCampo( "inStatus" ,"status" );
$obLista->ultimaAcao->addCampo( "inTipoFiscalizacao" ,"cod_tipo" );
$obLista->ultimaAcao->addCampo( "inCodProcesso" ,"cod_processo" );
$obLista->ultimaAcao->addCampo( "inInscricao" ,"inscricao" );
$obLista->ultimaAcao->addCampo( "inCodFiscal" ,"cod_fiscal" );
$obLista->ultimaAcao->addCampo( "inCodAtividade" ,"cod_atividade" );
$obLista->ultimaAcao->addCampo( "inCodModalidade" ,"cod_modalidade" );
$obLista->ultimaAcao->addCampo( "inNomModalidade" ,"nom_modalidade" );
$obLista->ultimaAcao->addCampo( "inNomAtividade" ,"nom_atividade" );
$obLista->ultimaAcao->addCampo( "inInicio" ,"periodo_inicio" );
$obLista->ultimaAcao->addCampo( "inTermino" ,"periodo_termino" );
$obLista->ultimaAcao->addCampo( "stLink" ,"link" );
$obLista->ultimaAcao->addCampo( "stUrl" ,"url" );

$obLista->commitAcao();
$obLista->show();

//Para corrigir o Cache do Navegador
unset( $inTipoFiscalizacao );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
