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
    * Página de Lista do Registro de Evento de Rescisão
    * Data de Criação: 16/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-04-10 13:11:20 -0300 (Ter, 10 Abr 2007) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php"                   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoRescisao";
$pgForm = "FM".$stPrograma.".php";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCaminho = CAM_GRH_FOL_INSTANCIAS."Rescisao/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}
switch ($stAcao) {
    case 'alterar': $pgProx = $pgForm; break;
    case 'excluir': $pgProx = $pgProc; break;
    DEFAULT       : $pgProx = $pgForm;
}
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
$stLink  = "&stAcao=$stAcao";
$stLink .= "&stTipoFiltro=".$_REQUEST['stTipoFiltro'];
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato":
    case "cgm_contrato":
        if ($_REQUEST['inContrato']) {
            $stLink .= "&inContrato=".$_REQUEST['inContrato'];
            $inRegistro = $_REQUEST['inContrato'];
            $stFiltro = " AND  registro = $inRegistro";
        }
    break;
    case "cargo":
        $stLink .= "&inCodRegime=".$_REQUEST['inCodRegime'];
        $stLink .= "&inCodSubDivisao=".$_REQUEST['inCodSubDivisao'];
        $stLink .= "&inCodCargo=".$_REQUEST['inCodCargo'];
        $stLink .= "&inCodEspecialidade=".$_REQUEST['inCodEspecialidade'];
        $stFiltro = " AND contrato_servidor.cod_cargo = ".$_REQUEST['inCodCargo'] ;
        if ($_REQUEST['inCodEspecialidade'] != "") {
            $stFiltro .= " AND contrato_servidor_especialidade_cargo.cod_especialidade = ".$_REQUEST['inCodEspecialidade'] ;
        }
    break;
    case "funcao":
        $stLink .= "&inCodRegime=".$_REQUEST['inCodRegime'];
        $stLink .= "&inCodSubDivisao=".$_REQUEST['inCodSubDivisao'];
        $stLink .= "&inCodEspecialidade=".$_REQUEST['inCodEspecialidade'];
        $stLink .= "&inCodFuncao=".$_REQUEST['inCodFuncao'];
        $stFiltro = " AND contrato_servidor_funcao.cod_cargo = ".$_REQUEST['inCodFuncao'] ;
        if ($_REQUEST['inCodEspecialidade'] != "") {
            $stFiltro .= " AND contrato_servidor_especialidade_funcao.cod_especialidade = ".$_REQUEST['inCodEspecialidade'] ;
        }
    break;
    case "lotacao":
        $stLink .= "&inCodLotacao=".$_REQUEST['inCodLotacao'];
        $stFiltro = " AND vw_orgao_nivel.orgao = '".$_REQUEST['inCodLotacao']."'";
    break;
    case "local":
        $stLink .= "&inCodLocal=".$_REQUEST['inCodLocal'];
        $stFiltro = " AND contrato_servidor_local.cod_local = ".$_REQUEST['inCodLocal'];
    break;
    case "padrao":
        $stLink .= "&inCodPadrao=".$_REQUEST['inCodPadrao'];
        $stFiltro = " AND contrato_servidor_padrao.cod_padrao = ".$_REQUEST['inCodPadrao'];
    break;
}

$rsLista = new Recordset;
$obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao;
$obTFolhaPagamentoRegistroEventoRescisao->recuperaContratosDoFiltro($rsLista,$stFiltro);

if ($rsLista->getNumLinhas() < 0 ) {
    $obTFolhaPagamentoRegistroEventoRescisao->recuperaRescisaoContratoPensionista($rsLista,$stFiltro);
}

$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo("Matrículas");

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Matrícula" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Lotação" );
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Situação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cod_estrutural]-[descricao_lotacao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodContrato"          , "cod_contrato" );
$obLista->ultimaAcao->addCampo( "&inCodCargo"             , "cod_cargo"    );
$obLista->ultimaAcao->addCampo( "&inCodSubDivisao"        , "cod_sub_divisao");
$obLista->ultimaAcao->addCampo( "&inCodEspecialidade"     , "cod_especialidade");
$obLista->ultimaAcao->addCampo( "&inRegistro"             , "registro"     );
$obLista->ultimaAcao->addCampo( "&inNumCGM"               , "numcgm"       );
$obLista->ultimaAcao->addCampo( "&stNomCGM"               , "nom_cgm"      );
$obLista->ultimaAcao->addCampo( "&stDescricaoCausa"       , "descricao_causa"      );
$obLista->ultimaAcao->addCampo( "&inNumCausa"             , "num_causa"      );
$obLista->ultimaAcao->addCampo( "&dtRescisao"             , "dt_rescisao"      );
$obLista->ultimaAcao->setLink( $pgProx."?".Sessao::getId().$stLink );
$obLista->commitAcao();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
