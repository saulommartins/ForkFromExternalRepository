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
    * Lista
    * Data de Criação: 07/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: LSManterRegistroEvento.php 65896 2016-06-24 20:14:24Z michel $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEvento";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//MANTEM FILTRO E PAGINACAO
$stLink .= "&stAcao=".$stAcao."&inFiltrar=".$request->get('inFiltrar');
$link = Sessao::read("link");
if ( $request->get('pg') and $request->get('pos') ) {
    $stLink.= "&pg=".$request->get("pg")."&pos=".$request->get("pos");
    $link["pg"]  = $request->get("pg");
    $link["pos"] = $request->get("pos");
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {    
    $request = new Request($link);
} else {
    foreach ($request->getAll() as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}

include_once($pgJs);
$stCaminho = CAM_GRH_FOL_INSTANCIAS."movimentacaoFinanceira/";
$rsLista = new RecordSet;
$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );

$stValoresFiltro = "";
switch ($request->get('inFiltrar')) {
    case 0:
    case 1:
    #case "contrato":
    #case "cgm_contrato":
        if ($request->get('inContrato')) {
            $obTPessoalContrato = new TPessoalContrato;
            $stFiltro = " WHERE registro = ".$request->get('inContrato');
            $obTPessoalContrato->recuperaTodos($rsContrato, $stFiltro);            
            $request->set('stTipoFiltro',"contrato");
            $stValoresFiltro = $rsContrato->getCampo("cod_contrato");
        }
        break;

    case 2:
    #case "reg_sub_car_esp":        
        $request->set("stTipoFiltro","reg_sub_car_esp");        
        $stValoresFiltro  = $request->get('inCodRegime')."#";
        $stValoresFiltro  = $request->get('inCodSubDivisao')."#";
        $stValoresFiltro  = $request->get('inCodCargo')."#";
        $arInCodEspecialidade = $request->get("inCodEspecialidade");
        if ( is_array($arInCodEspecialidade) ) {
            $stValoresFiltro .= $arInCodEspecialidade;
        }
        break;

    case 3:
    #case "reg_sub_fun_esp":        
        $request->set("stTipoFiltro","reg_sub_fun_esp");
        $stValoresFiltro  = $request->get('inCodRegime')."#";
        $stValoresFiltro  = $request->get('inCodSubDivisao')."#";
        $stValoresFiltro  = $request->get('inCodCargo')."#";
        $arInCodEspecialidade = $request->get("inCodEspecialidade");
        if ( is_array($arInCodEspecialidade) ) {
            $stValoresFiltro .= $arInCodEspecialidade;
        }
        break;

    case 4:
    #case "padrao":
        $request->set("stTipoFiltro","padrao");
        $stValoresFiltro = $request->get('inCodPadrao');
        break;

    case 5:
    #case "lotacao":
        $request->set("stTipoFiltro","lotacao");        
        $obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->setCodOrgaoEstruturado( $request->get('inCodLotacao') );
        $obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->listarOrgaoReduzido   ( $rsOrgaoReduzido,"","",$boTransacao );
        $stValoresFiltro = $rsOrgaoReduzido->getCampo("cod_orgao");
    break;

    case 6:
    #case "local":        
        $request->set("stTipoFiltro","local");    
        $stValoresFiltro = $request->get('inCodLocal');
    break;

    case 7:
    #case "evento":
        $request->set("stTipoFiltro","evento");
        $stValoresFiltro = $request->get('inCodigoEvento');
    break;
}

$stTipoFiltro = $request->get('stTipoFiltro');

$obTPessoalContratoServidor = new TPessoalContratoServidor();
$obTPessoalContratoServidor->setDado("stTipoFiltro"     ,$stTipoFiltro );
$obTPessoalContratoServidor->setDado("stValoresFiltro"  ,$stValoresFiltro );
$obTPessoalContratoServidor->setDado("situacao"         ,"'A','P','E'" );
$obTPessoalContratoServidor->recuperaContratosParaRegistroEvento($rsLista);

$obLista = new Lista;
$obLista->setRecordSet          ( $rsLista );
$obLista->setTitulo             ("Matrículas");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Servidor" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lotação" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Situação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 2 );
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
$obLista->ultimaAcao->addCampo( "&inContrato"   , "registro" );
$obLista->ultimaAcao->addCampo( "inNumCGM"      , "numcgm");
$obLista->ultimaAcao->addCampo( "stNomCGM"      , "nom_cgm");
$obLista->ultimaAcao->addCampo( "inCodFuncao"   , "cod_funcao");
$obLista->ultimaAcao->addCampo( "inCodContrato" , "cod_contrato");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink );
$obLista->commitAcao();
$obLista->show();
?>
