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
    * Página processamento ocuto Pessoal ServidorP
    * Data de Criação   : 14/12/2004
    *

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Id: OCManterServidorAbaDocumentacao.php 66017 2016-07-07 17:31:31Z michel $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once 'OCManterServidorAbaContrato.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJS                = "JS".$stPrograma.".js";

function validaDataCadPis()
{
    if ($_POST['dtCadastroPis'] != "" and $_POST['dtDataNascimento'] != "") {
        if ( SistemaLegado::comparaDatas($_POST['dtDataNascimento'],$_POST['dtCadastroPis']) ) {
            $stJs .= "f.dtCadastroPis.value = '';       \n";
            $stJs .= "alertaAviso('Data de Emissão (".$_POST['dtCadastroPis'].") não pode ser anterior a Data de Nascimento (".$_POST['dtDataNascimento'].")', 'form', 'erro', '".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function validaNumeroPis()
{
    $stJs = "";

    if (trim($_POST['stPisPasep'])!="" and !checkPIS($_POST['stPisPasep'], false)) {
        $stJs .= "f.stPisPasep.value = '';       \n";
        $stJs .= "alertaAviso('Campo PIS/PASEP da guia Documentação é inválido(".$_POST['stPisPasep'].")', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function validaCTPS()
{
    $obErro = new erro;
    if ($_POST['inNumeroCTPS'] == "") {
        $obErro->setDescricao("Campo Número da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stSerieCTPS'] == "" ) {
        $obErro->setDescricao("Campo Série da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['dtDataCTPS'] == "" ) {
        $obErro->setDescricao("Campo Data de Emissão da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stOrgaoExpedidorCTPS'] == "" ) {
        $obErro->setDescricao("Campo Órgão Expedidor da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stSiglaUF'] == "" ) {
        $obErro->setDescricao("Campo UF da guia Documentação inválido!()");
    }

    return $obErro;
}

function incluirCTPS()
{
    $obErro = validaCTPS();
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet = new Recordset;
        $arCTPS      = ( is_array( Sessao::read('CTPS') ) ) ? Sessao::read('CTPS') : array();
        $rsRecordSet->preenche( $arCTPS );
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if ($inUltimoId < 0 or $inUltimoId === "") {
            $inProxId = 0;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $ultimaDataIncluida = $rsRecordSet->getCampo("dtDataCTPS");
        if ( SistemaLegado::comparaDatas($ultimaDataIncluida,$_REQUEST["dtDataCTPS"]) && (count (Sessao::read("CTPS")) > 0 )) {
            $obErro->setDescricao("A data informada deve ser maior que o da última data cadastrada.");
        }
    }
    if ( !$obErro->ocorreu() and is_array(Sessao::read('CTPS')) ) {
        foreach (Sessao::read('CTPS') as $arCTPS) {
            if( trim($arCTPS['inNumeroCTPS'])         == trim($_POST['inNumeroCTPS']) and
                trim($arCTPS['stSerieCTPS'])          == trim($_POST['stSerieCTPS'])){
                $obErro->setDescricao("Esses dados de CTPS já estão inseridos na lista de CTPS.");
                break;
            }
        }
    }
    if (!$obErro->ocorreu()) {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
        $rsUF = new RecordSet();
        $obTUF = new TUF();
        $stFiltro = " WHERE sw_uf.cod_uf=".$_POST['stSiglaUF'];
        $obTUF->recuperaTodos($rsUF, $stFiltro);
        if ( !$rsUF->eof()  ) {
            $sigla = $rsUF->getCampo("sigla_uf");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $arCTPSs = Sessao::read("CTPS");
        $arElementos['inId']                 = $inProxId;
        $arElementos['inNumeroCTPS']         = $_POST['inNumeroCTPS'];
        $arElementos['stOrgaoExpedidorCTPS'] = $_POST['stOrgaoExpedidorCTPS'];
        $arElementos['stSerieCTPS']          = $_POST['stSerieCTPS'];
        $arElementos['dtDataCTPS']           = $_POST['dtDataCTPS'];
        $arElementos['stSiglaUF'] 		     = $sigla;
        $arElementos['inCodUF']              = $_POST['stSiglaUF'];
        $arCTPSs[]            = $arElementos;
        Sessao::write("CTPS",$arCTPSs);

        $stJs .= listarCTPS();

    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function alterarCTPS()
{
    $obErro = validaCTPS();

    if (!$obErro->ocorreu()) {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
        $rsUF = new RecordSet();
        $obTUF = new TUF();
        $stFiltro = " WHERE sw_uf.cod_uf=".$_POST['stSiglaUF'];
        $obTUF->recuperaTodos($rsUF, $stFiltro);
        if ( !$rsUF->eof()  ) {
            $sigla = $rsUF->getCampo("sigla_uf");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet = new Recordset;
        $arCTPS      = ( is_array( Sessao::read('CTPS') ) ) ? Sessao::read('CTPS') : array();
        $rsRecordSet->preenche( $arCTPS );
        $rsRecordSet->setUltimoElemento();
        $ultimaDataIncluida = $rsRecordSet->getCampo("dtDataCTPS");
        if ( SistemaLegado::comparaDatas($ultimaDataIncluida,$_REQUEST["dtDataCTPS"]) && (count (Sessao::read("CTPS")) > 0 )) {
            $obErro->setDescricao("A data informada deve ser maior que o da última data cadastrada.");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $arCTPSs = Sessao::read("CTPS");
        $arElementos['inId']                 = Sessao::read('inId');
        $arElementos['inNumeroCTPS']         = $_POST['inNumeroCTPS'];
        $arElementos['stOrgaoExpedidorCTPS'] = $_POST['stOrgaoExpedidorCTPS'];
        $arElementos['stSerieCTPS']          = $_POST['stSerieCTPS'];
        $arElementos['dtDataCTPS']           = $_POST['dtDataCTPS'];
        $arElementos['stSiglaUF'] 		     = $sigla;
        $arElementos['inCodUF']              = $_POST['stSiglaUF'];
        $arCTPSs[Sessao::read('inId')] = $arElementos;
        Sessao::write("CTPS",$arCTPSs);

        $stJs .= listarCTPS();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirCTPS()
{
    $id = $_GET['inLinha'];
    $_id = 0;
    $arCTPSs = Sessao::read("CTPS");
    while ( list( $arId ) = each( $arCTPSs ) ) {
        if ($arCTPSs[$arId]["inId"] != $id) {

            $arElementos['inId']                 = $_id;
            $arElementos['inNumeroCTPS']         = $arCTPSs[$arId]['inNumeroCTPS'];
            $arElementos['stOrgaoExpedidorCTPS'] = $arCTPSs[$arId]['stOrgaoExpedidorCTPS'];
            $arElementos['stSerieCTPS']          = $arCTPSs[$arId]['stSerieCTPS'];
            $arElementos['dtDataCTPS']           = $arCTPSs[$arId]['dtDataCTPS'];
            $arElementos['stSiglaUF']            = $arCTPSs[$arId]['stSiglaUF'];
            $arElementos['inCodUF']              = $arCTPSs[$arId]['inCodUF'];
            $arTMP[] = $arElementos;
            $_id++;
        }
    }
    Sessao::write('CTPS',$arTMP);

    $stJs .= listarCTPS();

    return $stJs;
}

function limparCTPS()
{
    $stJs  = "f.inNumeroCTPS.value                      = '';\n";
    $stJs .= "f.stOrgaoExpedidorCTPS.value              = '';\n";
    $stJs .= "f.stSerieCTPS.value                       = '';\n";
    $stJs .= "f.dtDataCTPS.value                        = '';\n";
    $stJs .= "f.stSiglaUF.value                         = '';\n";

    return $stJs;
}

function listarCTPS()
{
    $rsRecordSet = new Recordset;
    $arCTPS      = ( is_array( Sessao::read('CTPS') ) ) ? Sessao::read('CTPS') : array();
    $rsRecordSet->preenche( $arCTPS );
    $stHtml = "";
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Dados de CTPS" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Número" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Série" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data emissão" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Orgão expedidor" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "UF" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNumeroCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stSerieCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dtDataCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stOrgaoExpedidorCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stSiglaUF" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao(true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('montaAlterarCTPS',2);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao(true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirCTPS',2);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('btnAlterar').disabled = true;               \n";
    $stJs .= "d.getElementById('btnIncluir').disabled = false;              \n";
    $stJs .= "d.getElementById('spnCTPS').innerHTML   = '".$stHtml."';";
    $stJs .= limparCTPS();

    return $stJs;

}

function montaAlterarCTPS()
{
    $id = $_GET['inLinha'];
    $arCTPSs = Sessao::read('CTPS');
    while ( list( $arId ) = each( $arCTPSs ) ) {

        if ($arCTPSs[$arId]['inId'] == $id) {
            $numero = trim($arCTPSs[$arId]['inNumeroCTPS']);
            $orgao  = trim($arCTPSs[$arId]['stOrgaoExpedidorCTPS']);
            $serie  = trim($arCTPSs[$arId]['stSerieCTPS']);
            $data   = trim($arCTPSs[$arId]['dtDataCTPS']);
            $uf     = trim($arCTPSs[$arId]['inCodUF']);

            $stJs .= "f.inNumeroCTPS.value = '$numero';";
            $stJs .= "f.stOrgaoExpedidorCTPS.value = '$orgao';";
            $stJs .= "f.stSerieCTPS.value = '$serie';";
            $stJs .= "f.dtDataCTPS.value = '$data';";
            $stJs .= "f.stSiglaUF.value = '$uf';";
            $stJs .= "d.getElementById('btnAlterar').disabled = false;      \n";
            $stJs .= "d.getElementById('btnIncluir').disabled = true;      \n";

            Sessao::write('inId',$id);
       }
    }

    return $stJs;
}

function listarAlterarCTPS()
{
    GLOBAL $inCodServidor;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addRPessoalCTPS();
    $stMensagem = false;
    $obRPessoalServidor->setCodServidor( $inCodServidor );
    $obRPessoalServidor->roRPessoalCTPS->listarCTPS( $rsCTPS, $boTransacao );

    $arCTPS = array();
    $inId = 0;
    while ( !$rsCTPS->eof()  ) {
        $arTemp["inId"]                 = $inId;
        $arTemp["inNumeroCTPS"]         = trim($rsCTPS->getCampo("numero"));
        $arTemp["stOrgaoExpedidorCTPS"] = $rsCTPS->getCampo("orgao_expedidor");
        $arTemp["stSerieCTPS"]          = $rsCTPS->getCampo("serie");
        $arTemp["dtDataCTPS"]           = $rsCTPS->getCampo("dt_emissao");
        $arTemp["stSiglaUF"]            = $rsCTPS->getCampo("sigla");
        $arTemp["inCodUF"]              = $rsCTPS->getCampo("uf_expedicao");

        $arCTPS[]                       = $arTemp;
        $inId++;
        $rsCTPS->proximo();
    }

    Sessao::write('CTPS',$arCTPS);

    $stJs .= listarCTPS();

    return $stJs;
}

function validaDataEmissaoCTPS()
{
    $stValida = comparaComDataNascimento("dtDataCTPS","Data de Emissão");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        if ( $_POST['dtDataCTPS'] != "" and SistemaLegado::comparaDatas($_POST['dtDataNascimento'],$_POST['dtDataCTPS']) ) {
            $stJs .= "f.dtDataCTPS.value = '';  \n";
            $stJs .= "alertaAviso('Data de Emissão (".$_POST['dtDataCTPS'].") não pode ser anterior a Data de Nascimento (".$_POST['dtDataNascimento'].")', 'form', 'erro', '".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function checkPIS($pis, $checkZero=true)
{
    $pis = trim(preg_replace('/[^0-9]/', '', $pis));

    if ($pis === "00000000000" && $checkZero == false) {
        return true;
    }

    if (strlen($pis) != 11 || intval($pis) == 0) {
        return false;
    }

    for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
        $d += $pis[$c] * $p;
    }

    return ($pis[10] == (((10 * $d) % 11) % 10));
}

function limparArqDigital()
{
    $stJs  = "f.inTipoDocDigital.value = '';    \n";
    $stJs .= "f.stArqDigital.value = '';        \n";

    return $stJs;
}

function montaListaArqDigital()
{
    $rsRecordSet = new Recordset;
    $arArquivosDocumentos = ( is_array( Sessao::read('arArquivosDocumentos') ) ) ? Sessao::read('arArquivosDocumentos') : array();
    $arArqDoc = array();
    foreach($arArquivosDocumentos AS $chave => $arquivo){
        if($arquivo['boExcluido']=='FALSE')
            $arArqDoc[] = $arquivo;
    }

    $rsRecordSet->preenche( $arArqDoc );
    $stHtml = "";
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Cópias digitais de documentos" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo de documento" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Arquivo" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stTipoArqDocDigital" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "name" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('abrirArqDigital', 2);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao(true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirArqDigital', 2);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs .= "d.getElementById('spnListaArqDigital').innerHTML = '".$stHtml."';";
    $stJs .= limparArqDigital();

    return $stJs;
}

switch ($request->get("stCtrl")) {
    case "validaNumeroPis":
        $stJs .= validaNumeroPis();
        break;
    case "validaDataCadPis":
        $stJs .= validaDataCadPis();
        break;
    case "incluirCTPS":
        $stJs .= incluirCTPS();
        break;
    case "alterarCTPS":
        $stJs .= alterarCTPS();
        break;
    case "excluirCTPS":
        $stJs .= excluirCTPS();
        break;
    case "limparCTPS":
        $stJs .= limparCTPS();
        break;
    case "montaAlterarCTPS":
        $stJs .= montaAlterarCTPS();
        break;
    case "validaDataEmissaoCTPS":
        $stJs .= validaDataEmissaoCTPS();
        break;
    case "incluirArqDigital":
        $arArquivosDocumentos = Sessao::read("arArquivosDocumentos");

        $stErro = null;

        $inTipoDocDigital = $request->get('inTipoDocDigital');
        if (empty($inTipoDocDigital))
            $stErro = "Selecione o Tipo de documento!";
        else
            $stTipoArqDocDigital = SistemaLegado::pegaDado("descricao","pessoal".Sessao::getEntidade().".tipo_documento_digital","WHERE cod_tipo = ".$inTipoDocDigital);

        if(!$stErro){
            foreach($arArquivosDocumentos AS $chave => $arquivo){
                if($arquivo['inTipoDocDigital'] == $inTipoDocDigital && $arquivo['boExcluido'] == 'FALSE'){
                    $stErro = $stTipoArqDocDigital." já possui arquivo digital vinculado!";
                    break;
                }
            }
        }

        if(!$stErro){
            $stName = $_FILES["stArqDigital"]["name"];
            if(empty($stName))
                $stErro = "Selecione o Arquivo do documento!";
            else{
                $stEntidade = Sessao::getEntidade();
                $stEntidade = (empty($stEntidade)) ? $stEntidade : $stEntidade.'_';

                $stNameArq = $request->get('inNumCGM').$stEntidade.'_'.date('YmdHis').'_'.$stName;
            }
        }

        if(!$stErro){
            if ($_FILES["stArqDigital"]["error"] > 0) {
                if ($_FILES["stArqDigital"]["error"] == 1 )
                    $stErro = "Arquivo ultrapassa o valor maxímo de ".ini_get("upload_max_filesize");
                else
                    $stErro = "Erro no upload do arquivo.";
            }
        }

        $stDirTMP = CAM_GRH_PESSOAL."tmp/";
        $stDirANEXO = CAM_GRH_PESSOAL."anexos/";

        if(!$stErro){
            if (!is_writable($stDirTMP)) {
                $stErro = " O diretório ".CAM_GRH_PESSOAL."tmp não possui permissão de escrita!";
            }
        }

        if(!$stErro){
            switch($_FILES['stArqDigital']['type']){
                #DOC
                case 'application/msword':
                #DOCX
                case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                #ODT
                case 'application/vnd.oasis.opendocument.text':
                #PDF
                case 'application/pdf':
                #PNG
                case 'image/png':
                #JPG/JPEG
                case 'image/jpeg':
                #GIF
                case 'image/gif':
                    $boType = 'TRUE';
                break;
                default:
                    $stErro = 'Tipo de Arquivo Inválido!';
                break;
            }
        }

        if(!$stErro){
            $stArquivoTMP = $stDirTMP.$stNameArq;
            $stArquivoANEXO = $stDirANEXO.$stNameArq;

            if(!move_uploaded_file($_FILES["stArqDigital"]["tmp_name"],$stArquivoTMP)){
                $stErro = "Erro no upload do arquivo.";
            }
        }

        if ($stErro) {
            $stJs  = "alertaAviso('".urlencode("Erro ao Incluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        } else {
            chmod($stArquivoTMP,0777);

            $arArquivosUpload['inTipoDocDigital']    = $inTipoDocDigital;
            $arArquivosUpload['stTipoArqDocDigital'] = $stTipoArqDocDigital;
            $arArquivosUpload['stArquivo']           = $stArquivoANEXO;
            $arArquivosUpload['arquivo_digital']     = $stNameArq;
            $arArquivosUpload['name']                = $stName;
            $arArquivosUpload['inId']                = count($arArquivosDocumentos);
            $arArquivosUpload['boCopiado']           = 'FALSE';
            $arArquivosUpload['tmp_name']            = $stArquivoTMP;
            $arArquivosUpload['boExcluido']          = 'FALSE';

            $arArquivosDocumentos[] = $arArquivosUpload;

            Sessao::write("arArquivosDocumentos", $arArquivosDocumentos);

            $stJs  = montaListaArqDigital();
            $stJs .= limparArqDigital();
        }
    break;
    case "excluirArqDigital":
        $stErro = null;
        $inCount = 0;

        $arTemp = array();
        $arArquivosDocumentos = Sessao::read("arArquivosDocumentos");
        foreach($arArquivosDocumentos AS $chave => $arquivo){
            if($arquivo['inId'] != $request->get('inLinha')){
                $arquivo['inId'] = $inCount;
                $arTemp[] = $arquivo;

                $inCount++;
            }else{
                if($arquivo['boCopiado']=='TRUE'){
                    $arquivo['inId'] = $inCount;
                    $arquivo['boExcluido'] = 'TRUE';
                    $arTemp[] = $arquivo;

                    $inCount++;
                }else{
                    $stArquivo = $arquivo['tmp_name'];

                    if (file_exists($stArquivo)) {
                        if(!unlink($stArquivo)){
                            $stErro = $arquivo['name']." não excluído!";
                            break;
                        }
                    }
                }
            }
        }

        if ($stErro) {
            $stJs  = "alertaAviso('".urlencode("Erro ao Excluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        }else{
            Sessao::write("arArquivosDocumentos",$arTemp);

            $stJs = montaListaArqDigital();
        }
    break;
    case "limparArqDigital":
        $stJs = limparArqDigital();
    break;
    case "abrirArqDigital":
        $arArquivosDocumentos = Sessao::read("arArquivosDocumentos");

        foreach($arArquivosDocumentos AS $chave => $arquivo){
            if($arquivo['inId'] == $request->get('inLinha')){
                if($arquivo['boCopiado'] == 'FALSE'){
                    $stArquivo = $arquivo['tmp_name'];
                    $stNomArq = $arquivo['name'];
                }else{
                    $stArquivo = $arquivo['stArquivo'];
                    $stNomArq = $arquivo['name'];
                }

                break;
            }
        }

        if($stNomArq){
            header('Content-Description: File Transfer');
            header('Content-Type: application/force-download');
            header('Content-Length: '.filesize($stArquivo));
            header('Content-Disposition: attachment; filename='.$stNomArq);
            readfile($stArquivo);
        }else
            $stJs = "alertaAviso('Erro ao abrir o Arquivo Digital!','unica','erro','".Sessao::getId()."');";
    break;
    case "montaListaArqDigital":
        $stJs = montaListaArqDigital();
    break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
