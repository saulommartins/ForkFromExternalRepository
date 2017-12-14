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
    * Página Formulário - Parâmetros do Arquivo RDEXTRA.
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: $

    * Casos de uso: uc-02.08.04, uc-02.08.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterContratosLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS); 

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case "configuracoesIniciais":
        configuracoesIniciais();
        $stJs .= montaSpnLista();
    break;
    case "incluirContrato":
        $stJs .= incluirContratos();
        $stJs .= montaSpnLista();
    break;
    case "alterarContrato":
        $stJs .= alterarContrato( $_REQUEST['inId'] );
        $stJs .= montaSpnLista();
    break;
    case "excluirContrato":
        $stJs .= excluirContrato( $_GET['inId'] );
        $stJs .= montaSpnLista();
    break;
    case "carregaContrato":
        $stJs .= carregaLista( $_GET['inId'] );
    break;
}

function incluirContratos()
{
    include_once( CAM_GPC_TCERS_MAPEAMENTO."TTCERSContratosLiquidacao.class.php"  );
    $boValida = true;
    $arSessaoLista = Sessao::read('sessaoLista');

    foreach ($arSessaoLista as $arLista) {
        if ($arLista['stAno'] == $_REQUEST['stAno'] && $arLista['inLiquidacao'] == $_REQUEST['inLiquidacao']) {
            $boValida = false;
            $stJs .= "alertaAviso('Esses dados já foram informados!','form','erro','".Sessao::getId()."');\n";
        }
    }

    if ($boValida) {
        $inId = count($arSessaoLista);
 
        $arSessaoLista[$inId]['inId']          = $inId;
        $arSessaoLista[$inId]['inContrato']    = $_REQUEST['inContrato'];
        $arSessaoLista[$inId]['inLiquidacao']  = $_REQUEST['inLiquidacao'];
        $arSessaoLista[$inId]['inContratoTCE'] = $_REQUEST['inContratoTCE'];
        $arSessaoLista[$inId]['stAno']         = $_REQUEST['stAno'];
    }
    Sessao::write('sessaoLista',$arSessaoLista);

    return $stJs;
}

function montaSpnLista()
{
    $arLista = Sessao::read('sessaoLista');
    $rsLista = new RecordSet;
    $rsLista->preenche($arLista);

    $obLista = new Lista;
    $obLista->setTitulo( "Lista de Contratos na Liquidação" );
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Contrato");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Liquidação");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Contrato TCE");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Exercício");
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inContrato" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inLiquidacao" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inContratoTCE" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stAno" );
    $obLista->ultimoDado->setAlinhamento( "CENTRO" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado( 'carregaContrato' );" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax(true);
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado( 'excluirContrato' );" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnLista').innerHTML = '".$stHtml."';\n limpaFormularioContratos();";

    return $stJs;
}

function carregaLista($inIdCarregar)
{
    $arSessaoLista = Sessao::read('sessaoLista');

    $inId          = $arSessaoLista[$inIdCarregar]['inId'];
    $inContrato    = $arSessaoLista[$inIdCarregar]['inContrato'];
    $inLiquidacao  = $arSessaoLista[$inIdCarregar]['inLiquidacao'];
    $inContratoTCE = $arSessaoLista[$inIdCarregar]['inContratoTCE'];
    $stAno         = $arSessaoLista[$inIdCarregar]['stAno'];

    $stJs.= "jq_('#inId').val(".$inId.");";
    $stJs.= "jq_('#inContrato').val(".$inContrato.");";
    $stJs.= "jq_('#inLiquidacao').val(".$inLiquidacao.");";
    $stJs.= "jq_('#inContratoTCE').val(".$inContratoTCE.");";
    $stJs.= "jq_('#stAno').val(".$stAno.");";

    $stJs.= "jq_('#btnIncluir').prop('disabled', true);";
    $stJs.= "jq_('#btnAlterar').prop('disabled', false);";

    return $stJs;
}

function alterarContrato($inIdAlterar)
{
    $arListaSessao = Sessao::read('sessaoLista');
    Sessao::remove('sessaoLista');

    $inId = 0;

    foreach ($arListaSessao as $arLista => $dados) {
        if ($dados['inId'] == $inIdAlterar) {
            $arListaSessao[$arLista]['inId']          = $dados['inId'];
            $arListaSessao[$arLista]['inContrato']    = $_REQUEST['inContrato'];
            $arListaSessao[$arLista]['inLiquidacao']  = $_REQUEST['inLiquidacao'];
            $arListaSessao[$arLista]['inContratoTCE'] = $_REQUEST['inContratoTCE'];
            $arListaSessao[$arLista]['stAno']         = $_REQUEST['stAno'];
        }
    }

    Sessao::write('sessaoLista',$arListaSessao);

    return $stJs;
}

function excluirContrato($inIdExcluir)
{
    $arLista = array();
    $inId = 0;

    $arListaSessao = Sessao::read('sessaoLista');
    Sessao::remove('sessaoLista');

    foreach ($arListaSessao as $arDado ) {
        if ($arDado['inId'] != $inIdExcluir) {
            $arLista[$inId]['inId']          = $inId;
            $arLista[$inId]['inContrato']    = $arDado['inContrato'];
            $arLista[$inId]['inLiquidacao']  = $arDado['inLiquidacao'];
            $arLista[$inId]['inContratoTCE'] = $arDado['inContratoTCE'];
            $arLista[$inId]['stAno']         = $arDado['stAno'];
            $inId++;
        }
    }

    Sessao::write('sessaoLista',$arLista);

    return $stJs;
}

function configuracoesIniciais()
{
    include_once( CAM_GPC_TCERS_MAPEAMENTO."TTCERSContratosLiquidacao.class.php"  );

    $arSessaoLista = array();
    $inId = 0;

    $obTTCERSContratosLiquidacao = new TTCERSContratosLiquidacao;
    $obTTCERSContratosLiquidacao->recuperaTodos($rsRecordSet, "", " ORDER BY cod_liquidacao, cod_contrato");

    while ( !$rsRecordSet->eof() ) {
        $arSessaoLista[$inId]['inId']          = $inId;
        $arSessaoLista[$inId]['inContrato']    = $rsRecordSet->getCampo('cod_contrato');
        $arSessaoLista[$inId]['inLiquidacao']  = $rsRecordSet->getCampo('cod_liquidacao');
        $arSessaoLista[$inId]['inContratoTCE'] = $rsRecordSet->getCampo('cod_contrato_tce');
        $arSessaoLista[$inId]['stAno']         = $rsRecordSet->getCampo('exercicio');

        $rsRecordSet->proximo();
        $inId++;
    }

    Sessao::write('sessaoLista',$arSessaoLista);
}


if (isset($stJs)) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
