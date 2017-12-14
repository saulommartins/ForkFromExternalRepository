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
    * Página de Lista do Manter Cadastro Pensionista
    * Data de Criação: 16/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30894 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-19 10:24:55 -0300 (Ter, 19 Jun 2007) $

    * Casos de uso: uc-04.04.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionista.class.php"                           );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPensionista";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_PES_INSTANCIAS."pensionista/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read("link");
$stLink = Sessao::getId()."&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($arLink) ) {
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
}
Sessao::write("link",$arLink);

switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm."?".$stLink; break;
    case 'excluir': $pgProx = $pgProc."?".$stLink; break;
    DEFAULT       : $pgProx = $pgForm."?".$stLink;
}

$stLink  = "&stAcao=$stAcao";
$stLink .= "&inContratoPensionista=".$_REQUEST['inContratoPensionista'];
$stLink .= "&inNumCGMServidor=".$_REQUEST['inNumCGMServidor'];
$stLink .= "&inContratoServidor=".$_REQUEST['inContratoServidor'];
$stLink .= "&inCGM=".$_REQUEST['inCGM'];

$obTPessoalContratoPensionista = new TPessoalContratoPensionista;
$obTPessoalContrato            = new TPessoalContrato;
if ($_REQUEST['inContratoPensionista'] != "") {
    $stFiltro = " WHERE registro = ".$_REQUEST['inContratoPensionista'];
    $obTPessoalContrato->recuperaTodos( $rsContrato,$stFiltro );
    if ( $rsContrato->getNumLinhas() == 1 ) {
        $stFiltroLista .= " AND contrato_pensionista.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
    } else {
        $stFiltroLista .= " AND contrato_pensionista.cod_contrato IS NULL";
    }
}
$stFiltroLista .= ( $_REQUEST['inCGM'] != "" ) ? " AND pensionista.numcgm = ".$_REQUEST['inCGM'] : "";
if ($_REQUEST['inContratoServidor'] != "") {
    $stFiltro = " WHERE registro = ".$_REQUEST['inContratoServidor'];
    $obTPessoalContrato->recuperaTodos( $rsContrato,$stFiltro );
    if ( $rsContrato->getNumLinhas() == 1 ) {
        $stFiltroLista .= " AND pensionista.cod_contrato_cedente = ".$rsContrato->getCampo("cod_contrato");
    } else {
        $stFiltroLista .= " AND pensionista.cod_contrato_cedente IS NULL ";
    }
}
$stFiltroLista .= ( $_REQUEST['inNumCGMServidor'] != "" ) ? " AND sw_cgm_servidor.numcgm = ".$_REQUEST['inNumCGMServidor'] : "";
$obTPessoalContratoPensionista->recuperaPensionistas($rsLista,$stFiltroLista,"nom_cgm_pensionista");
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$stTitulo = ' </div></td></tr><tr><td colspan="6" class="alt_dados">Matrículas';
$obLista->setTitulo('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula do Pensionista" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Pensionista" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula do Servidor" );
$obLista->ultimoCabecalho->setWidth( 7 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro_pensionista" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm_pensionista]-[nom_cgm_pensionista]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro_servidor" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm_servidor]-[nom_cgm_servidor]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodPensionista"            , "cod_pensionista" );
$obLista->ultimaAcao->addCampo( "&inCGM"                       , "numcgm_pensionista");
$obLista->ultimaAcao->addCampo( "&inCGMServidor"               , "numcgm_servidor");
$obLista->ultimaAcao->addCampo( "&stNomCGM"                    , "nom_cgm_pensionista");
$obLista->ultimaAcao->addCampo( "&stNomCGMServidor"            , "nom_cgm_servidor");
$obLista->ultimaAcao->addCampo( "&inRegistroPensionista"       , "registro_pensionista");
$obLista->ultimaAcao->addCampo( "&inCodContratoPensionista"    , "cod_contrato");
$obLista->ultimaAcao->addCampo( "&inRegistroServidor"          , "registro_servidor");
$obLista->ultimaAcao->addCampo( "&inCodContratoServidor"       , "cod_contrato_cedente");
if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "&stDescQuestao"      , "[registro_pensionista] - [nom_cgm_pensionista]");
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProx."?".Sessao::getId().$stLink );
} else {
    $obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
}
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
