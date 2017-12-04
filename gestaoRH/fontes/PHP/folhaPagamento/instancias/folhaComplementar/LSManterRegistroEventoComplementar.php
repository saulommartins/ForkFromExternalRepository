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
    * Lista Manter Registro de Evento (Folha Complementar)
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );
//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoComplementar";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $_REQUEST["stAcao"] != "" ? $_REQUEST["stAcao"] : $_GET["stAcao"];
//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao."&inFiltrar=".$_REQUEST['inFiltrar'];
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}
include_once($pgJs);
$stCaminho = CAM_GRH_FOL_INSTANCIAS."folhaComplementar/";
$rsLista = new RecordSet;
$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );

$stValoresFiltro = "";
switch ($_REQUEST['inFiltrar']) {
    case 0:
    case 1:
    #case "contrato":
    #case "cgm_contrato":
        if ($_REQUEST['inContrato']) {
            $obTPessoalContrato = new TPessoalContrato;
            $stFiltro = " WHERE registro = ".$_REQUEST['inContrato'];
            $obTPessoalContrato->recuperaTodos($rsContrato, $stFiltro);
            $_REQUEST["stTipoFiltro"] = "contrato";
            $stValoresFiltro = $rsContrato->getCampo("cod_contrato");
        }
        break;

    case 2:
    #case "reg_sub_car_esp":
        $_REQUEST["stTipoFiltro"] = "reg_sub_car_esp";
        $stValoresFiltro  = $_REQUEST["inCodRegime"]."#";
        $stValoresFiltro .= $_REQUEST["inCodSubDivisao"]."#";
        $stValoresFiltro .= $_REQUEST["inCodCargo"]."#";
        if (is_array($_REQUEST["inCodEspecialidade"])) {
            $stValoresFiltro .= $_REQUEST["inCodEspecialidade"];
        }
        break;

    case 3:
    #case "reg_sub_fun_esp":
        $_REQUEST["stTipoFiltro"] = "reg_sub_fun_esp";
        $stValoresFiltro  = $_REQUEST["inCodRegime"]."#";
        $stValoresFiltro .= $_REQUEST["inCodSubDivisao"]."#";
        $stValoresFiltro .= $_REQUEST["inCodCargo"]."#";
        if (is_array($_REQUEST["inCodEspecialidade"])) {
            $stValoresFiltro .= $_REQUEST["inCodEspecialidade"];
        }
        break;

    case 4:
    #case "padrao":
        $_REQUEST["stTipoFiltro"] = "padrao";
        $stValoresFiltro = $_REQUEST['inCodPadrao'];
        break;

    case 5:
    #case "lotacao":
        $_REQUEST["stTipoFiltro"] = "lotacao";
        $obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->setCodOrgaoEstruturado( $_REQUEST['inCodLotacao'] );
        $obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->listarOrgaoReduzido   ( $rsOrgaoReduzido );
        $stValoresFiltro = $rsOrgaoReduzido->getCampo("cod_orgao");
    break;

    case 6:
    #case "local":
        $_REQUEST["stTipoFiltro"] = "local";
        $stValoresFiltro = $_REQUEST['inCodLocal'];
    break;
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
$obTPessoalContratoServidor = new TPessoalContratoServidor();
$obTPessoalContratoServidor->setDado("stTipoFiltro",$_REQUEST["stTipoFiltro"]);
$obTPessoalContratoServidor->setDado("stValoresFiltro",$stValoresFiltro);
$obTPessoalContratoServidor->setDado("situacao","'A','P','E'");
$obTPessoalContratoServidor->recuperaContratosParaRegistroEvento($rsLista);

$obLista = new Lista;
$obLista->setRecordSet          ( $rsLista );
$obLista->setTitulo             ("Matrículas");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula" );
$obLista->ultimoCabecalho->setWidth( 12 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lotação" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Situação" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("RIGHT");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("LEFT");
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("LEFT");
$obLista->ultimoDado->setCampo( "[cod_estrutural] - [descricao_lotacao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "alterar" );
$obLista->ultimaAcao->addCampo( "&inContrato"    , "registro" );
$obLista->ultimaAcao->addCampo( "inNumCGM"      , "numcgm");
$obLista->ultimaAcao->addCampo( "stNomCGM"      , "nom_cgm");
$obLista->ultimaAcao->addCampo( "inCodFuncao"   , "cod_funcao");
$obLista->ultimaAcao->addCampo( "inCodContrato" , "cod_contrato");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();
$obLista->show();
?>
