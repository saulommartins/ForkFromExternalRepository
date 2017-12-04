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
  * Página Oculta de Configuração de Prorrogação de Termos de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: OCManterConfiguracaoProrrogacaoTermo.php 63861 2015-10-26 18:03:16Z franver $
  * $Revision: 63861 $
  * $Author: franver $
  * $Date: 2015-10-26 16:03:16 -0200 (Mon, 26 Oct 2015) $
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceria.class.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceriaProrrogacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoProrrogacaoTermo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

function preencheTermos() {
    $rsTermoParcerias = new RecordSet();
    
    $obTTCMBATermoParceria = new TTCMBATermoParceria();
    $obTTCMBATermoParceria->setDado('exercicio'   , $_REQUEST['stExercicioTermo']   );
    $obTTCMBATermoParceria->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTTCMBATermoParceria->recuperaPorChave($rsTermoParcerias, $boTransacao);

    $rsTermoParcerias->ordena('nro_processo', "ASC", SORT_STRING);

    $stJs .= " jQuery('#stNumeroProcesso').empty().append(new Option('Selecione','') ); \n";
    while(!$rsTermoParcerias->eof()){
        $stJs .= " jQuery('#stNumeroProcesso').append(new Option(\"".trim($rsTermoParcerias->getCampo('nro_processo'))."\",\"".trim($rsTermoParcerias->getCampo('nro_processo'))."\") ); ";
        $rsTermoParcerias->proximo();
    }
    $rsTermoParcerias->setPrimeiroElemento();
    
    return $stJs;
}

function buscaProrrogacoes() {
    $arProrrogacoes = array();
    $rsProrrogacao = new RecordSet();

    $obTTCMBATermoParceriaProrrogacao = new TTCMBATermoParceriaProrrogacao();
    $obTTCMBATermoParceriaProrrogacao->setDado('exercicio'   , $_REQUEST['stExercicioProcesso'] );
    $obTTCMBATermoParceriaProrrogacao->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTTCMBATermoParceriaProrrogacao->setDado('nro_processo', $_REQUEST["stNumeroProcesso"] );
    $obTTCMBATermoParceriaProrrogacao->recuperaPorChave($rsProrrogacao, $boTransacao);
    
    $rsProrrogacao->addFormatacao('vl_prorrogacao', 'NUMERIC_BR');
    $rsProrrogacao->ordena('nro_processo', 'ASC', SORT_STRING);
    
    while(!$rsProrrogacao->eof()){
        $inNovo = ($rsProrrogacao->getCorrente() - 1 );
        $arProrrogacoes[$inNovo]['inId']                    = $inNovo;
        $arProrrogacoes[$inNovo]['numeroTermoAditivo']      = $rsProrrogacao->getCampo('nro_termo_aditivo');
        $arProrrogacoes[$inNovo]['exercicioTermoAditivo']   = $rsProrrogacao->getCampo('exercicio_aditivo');
        $arProrrogacoes[$inNovo]['dataProrrogacao']         = $rsProrrogacao->getCampo('dt_prorrogacao');
        $arProrrogacoes[$inNovo]['dataPublicacao']          = $rsProrrogacao->getCampo('dt_publicacao');
        $arProrrogacoes[$inNovo]['imprensaOficial']         = $rsProrrogacao->getCampo('imprensa_oficial');
        $arProrrogacoes[$inNovo]['boIndicadorAdimplemento'] = $rsProrrogacao->getCampo('indicador_adimplemento');
        $arProrrogacoes[$inNovo]['indicadorAdimplemento']   = ($rsProrrogacao->getCampo('indicador_adimplemento') == 't')?'Sim':'Não';
        $arProrrogacoes[$inNovo]['dataInicio']              = $rsProrrogacao->getCampo('dt_inicio');
        $arProrrogacoes[$inNovo]['dataTermino']             = $rsProrrogacao->getCampo('dt_termino');
        $arProrrogacoes[$inNovo]['valorProrrogacao']        = $rsProrrogacao->getCampo('vl_prorrogacao');
        
        $rsProrrogacao->proximo();
    }

    Sessao::write("arProrrogacoes", $arProrrogacoes);
    $stJs .= gerarSpanProrrogacoes();
    $stJs .= buscaDadosTermo();
    
    return $stJs;
}

function buscaDadosTermo(){
    $rsTermoParcerias = new RecordSet();
    
    $obTTCMBATermoParceria = new TTCMBATermoParceria();
    $obTTCMBATermoParceria->setDado('exercicio'   , $_REQUEST['stExercicioProcesso']);
    $obTTCMBATermoParceria->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTTCMBATermoParceria->setDado('nro_processo', $_REQUEST["stNumeroProcesso"]);
    $obTTCMBATermoParceria->recuperaPorChave($rsTermoParcerias, $boTransacao);

    if($rsTermoParcerias->getNumLinhas() == 1 ){
        while(!$rsTermoParcerias->eof()){
            $stJs .= " jQuery('#stDataInicio').html(\"".$rsTermoParcerias->getCampo('dt_inicio')."\"); \n";
            $stJs .= " jQuery('#stDataTermino').html(\"".$rsTermoParcerias->getCampo('dt_termino')."\"); \n";
            $stJs .= " jQuery('#stDtTerminoTermo').val(\"".$rsTermoParcerias->getCampo('dt_termino')."\"); \n";
            $stJs .= " jQuery('#stObjeto').html(\"".$rsTermoParcerias->getCampo('objeto')."\"); \n";
            $rsTermoParcerias->proximo();
        }
    }
    $rsTermoParcerias->setPrimeiroElemento();
    return $stJs;
}

function incluirProrrogacoes()
{
    $arProrrogacoes = Sessao::read("arProrrogacoes");
 
    if (!is_array($arProrrogacoes))
        $arProrrogacoes = array();
        
    if( validaInclusaoListaProrrogacao($arProrrogacoes) ) {
        $inNovo = count($arProrrogacoes);
        $arProrrogacoes[$inNovo]['inId']                    = $inNovo;
        $arProrrogacoes[$inNovo]['numeroTermoAditivo']      = trim($_REQUEST['stNumeroAditivo']);
        $arProrrogacoes[$inNovo]['exercicioTermoAditivo']   = $_REQUEST['stExercicioProrrogacao'];
        $arProrrogacoes[$inNovo]['dataProrrogacao']         = $_REQUEST['dtProrrogacao'];
        $arProrrogacoes[$inNovo]['dataPublicacao']          = $_REQUEST['dtPublicacaoProrrogacao'];
        $arProrrogacoes[$inNovo]['imprensaOficial']         = $_REQUEST['stImprensaOficialProrrogacao'];
        $arProrrogacoes[$inNovo]['boIndicadorAdimplemento'] = $_REQUEST['boIndicadorAdimplemento'];
        $arProrrogacoes[$inNovo]['indicadorAdimplemento']   = ($_REQUEST['boIndicadorAdimplemento'] == 't')?'Sim':'Não';
        $arProrrogacoes[$inNovo]['dataInicio']              = $_REQUEST['dtInicioProrrogacao'];
        $arProrrogacoes[$inNovo]['dataTermino']             = $_REQUEST['dtTerminoProrrogacao'];
        $arProrrogacoes[$inNovo]['valorProrrogacao']        = $_REQUEST['vlProrrogacao'];
        Sessao::write("arProrrogacoes", $arProrrogacoes);
        $stJs .= gerarSpanProrrogacoes();
    } else {
        $stJs .= "alertaAviso('Essa Prorrogação já foi incluida na lista.','aviso','aviso','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function carregarProrrogacoes()
{
    $arProrrogacoes = Sessao::read("arProrrogacoes");
    $inId = $_REQUEST["inId"];

    foreach ($arProrrogacoes as $chave => $dados) {
        if (trim($inId) == trim($dados["inId"])) {
            $stJs .= " jQuery('#stNumeroAditivo').val('".$dados["numeroTermoAditivo"]."');              \n";
            $stJs .= " jQuery('#dtProrrogacao').val('".$dados["dataProrrogacao"]."');                   \n";
            $stJs .= " jQuery('#dtPublicacaoProrrogacao').val('".$dados["dataPublicacao"]."');          \n";
            $stJs .= " jQuery('#stImprensaOficialProrrogacao').val('".$dados["imprensaOficial"]."');   \n";
            $stJs .= " jQuery('input[name=\"boIndicadorAdimplemento\"][value=\"".$dados['boIndicadorAdimplemento']."\"]').prop('checked',true); \n";
            $stJs .= " jQuery('#dtInicioProrrogacao').val('".$dados["dataInicio"]."');                  \n";
            $stJs .= " jQuery('#dtTerminoProrrogacao').val('".$dados["dataTermino"]."');               \n";
            $stJs .= " jQuery('#vlProrrogacao').val('".$dados["valorProrrogacao"]."');                 \n";
            $stJs .= " jQuery('#inId').val('".$dados["inId"]."');                                       \n";
        }
    }
    $stJs .= " jQuery('#btIncluirProrrogacoes').prop('disabled', 'disabled'); \n";
    $stJs .= " jQuery('#btAlterarProrrogacoes').removeProp('disabled');       \n";
    $stJs .= " jQuery('#btAlterarProrrogacoes').attr('onClick', ' montaParametrosGET(\'alterarProrrogacoes\'); ' );";
    
    return $stJs;
}

function validaInclusaoListaProrrogacao($arProrrogacoes)
{
    foreach ($arProrrogacoes as $key => $value) {
        if ( trim($_REQUEST['stNumeroAditivo']) == $value['numeroTermoAditivo'] && trim($_REQUEST['stExercicioProrrogacao']) == $value['exercicioTermoAditivo'] ) {
            return false;
        }
    }
    return true;
}

function alterarProrrogacoes()
{
    $inId = $_REQUEST["inId"];
    $arProrrogacoes = Sessao::read('arProrrogacoes');

    if ( validaInclusaoListaProrrogacao($arProrrogacoes) ) {
        foreach ($arProrrogacoes as $campo => $valor) {
            if (trim($valor["inId"]) == trim($inId)) {
                $arProrrogacoes[$campo]['numeroTermoAditivo']      = $_REQUEST['stNumeroAditivo'];
                $arProrrogacoes[$campo]['dataProrrogacao']         = $_REQUEST['dtProrrogacao'];
                $arProrrogacoes[$campo]['dataPublicacao']          = $_REQUEST['dtPublicacaoProrrogacao'];
                $arProrrogacoes[$campo]['imprensaOficial']         = $_REQUEST['stImprensaOficialProrrogacao'];
                $arProrrogacoes[$campo]['boIndicadorAdimplemento'] = $_REQUEST['boIndicadorAdimplemento'];
                $arProrrogacoes[$campo]['indicadorAdimplemento']   = ($_REQUEST['boIndicadorAdimplemento'] == 't')?'Sim':'Não';
                $arProrrogacoes[$campo]['dataInicio']              = $_REQUEST['dtInicioProrrogacao'];
                $arProrrogacoes[$campo]['dataTermino']             = $_REQUEST['dtTerminoProrrogacao'];
                $arProrrogacoes[$campo]['valorProrrogacao']        = $_REQUEST['vlProrrogacao'];
            }
        }
        Sessao::write('arProrrogacoes', $arProrrogacoes);
        $stJs .= gerarSpanProrrogacoes();
        $stJs .= " limpaFormularioProrrogacoes(); \n";
    } else {
        $stJs .= "alertaAviso('Essa Prorrogação já foi incluida na lista!','aviso','aviso','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirProrrogacoes()
{
    $arProrrogacoes = Sessao::read("arProrrogacoes");

    $arTemp = array();
    foreach ($arProrrogacoes as $arProrrogacao) {
        if ($arProrrogacao["inId"] != $_GET["inId"]) {
            $arProrrogacao["inId"] = count($arTemp);
            $arTemp[] = $arProrrogacao;
        }
    }
    Sessao::write('arProrrogacoes',$arTemp);
    $stJs .= gerarSpanProrrogacoes();

    return $stJs;
}

function gerarSpanProrrogacoes()
{
    $rsRecordSet = new recordset();
    $rsRecordSet->preenche(Sessao::read("arProrrogacoes"));

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Prorrogações do Termo de Parceria" );
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "N° Termo Aditivo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data da Prorrogação" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data da Publicação" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Imprensa oficial" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Indicador de adimplemento" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data do Início" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data do Término" );
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Prorrogação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[numeroTermoAditivo]/[exercicioTermoAditivo]");
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dataProrrogacao]");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dataPublicacao]");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[imprensaOficial]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[indicadorAdimplemento]");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dataInicio]");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dataTermino]");
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[valorProrrogacao]");
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('carregarProrrogacoes');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirProrrogacoes');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaInnerHtml();
    $stHtml = $obLista->getHTML();
    $stJs .= "jQuery('#spnProrrogacoes').html('".$stHtml."');";

    return $stJs;
}

function validaDataProrrogacao()
{
    $stDataInicioProrrogacao = $_REQUEST['dtInicioProrrogacao']; 
    $stDataTerminoTermo      = $_REQUEST['stDtTerminoTermo'];

    if(SistemaLegado::comparaDatas($stDataTerminoTermo, $stDataInicioProrrogacao, true)){
        $stJs .= " jQuery('#dtInicioProrrogacao').val(''); \n";
        $stJs .= " jQuery('#dtInicioProrrogacao').focus(); \n";
        $stJs .= "alertaAviso('Data de Início da prorrogação, deve ser maior que a data de termino(".$stDataTerminoTermo.") do Termo de Parceria.','form','aviso','".Sessao::getId()."');\n";
    } else {
        $arProrrogacoes = Sessao::read("arProrrogacoes");
        if(!is_array($arProrrogacoes)){
            $arProrrogacoes = array();
        }
        $boValidaData = false;
        foreach($arProrrogacoes AS $arProrrogacao){
            if(SistemaLegado::comparaDatas($arProrrogacao["dataTermino"],$stDataInicioProrrogacao, true)){
                $boValidaData = true;
                $stTermoProrrogacao = $arProrrogacao["numeroTermoAditivo"];
                $stDataTerminoProrrogacao = $arProrrogacao["dataTermino"];
            }
        }
        if($boValidaData){
            $stJs .= " jQuery('#dtInicioProrrogacao').val(''); \n";
            $stJs .= " jQuery('#dtInicioProrrogacao').focus(); \n";
            $stJs .= " alertaAviso('Data de Início da prorrogação, deve ser maior que a data de termino(".$stDataTerminoProrrogacao.") do Termo de Prorrogação(".$stTermoProrrogacao.").','form','aviso','".Sessao::getId()."');\n";
            $boValidaData = false;
        }
    }
    
    return $stJs;
}

function validaPeriodicidade( $stDtInicio, $stDtTermino, $stCampoDataTermino) {
    
    if(SistemaLegado::comparaDatas($stDtInicio,$stDtTermino, $boMaiorIgual)){
        $stJs .= "jQuery(\"#".$stCampoDataTermino."\").val(''); \n";
        $stJs .= "jQuery(\"#".$stCampoDataTermino."\").focus(); \n";
        $stJs .= "alertaAviso('Data de Término, deve ser maior que a Data de Início (".$stDtInicio.").','form','aviso','".Sessao::getId()."');\n";
    }
    return $stJs;
}

switch ($stCtrl) {
    case "preencheTermos":
        $stJs .= preencheTermos();
        break;
    case "carregarProrrogacoes":
        $stJs .= carregarProrrogacoes();
        break;
    case "incluirProrrogacoes":
        $stJs .= incluirProrrogacoes();
        break;
    case "alterarProrrogacoes":
        $stJs .= alterarProrrogacoes();
        break;
    case "excluirProrrogacoes":
        $stJs .= excluirProrrogacoes();
        break;
    case "validaDataProrrogacao":
        $stJs .= validaDataProrrogacao();
        break;
    case "buscaProrrogacoes":
        $stJs .= buscaProrrogacoes();
        break;
    case "validaPeriodicidade":
        if(array_key_exists('dtInicioProrrogacao',$request->getAll()) && array_key_exists('dtTerminoProrrogacao',$request->getAll())) {
            $stDataInicio  = $request->get('dtInicioProrrogacao');
            $stDataTermino = $request->get('dtTerminoProrrogacao');
            $stCampoDataTermino = 'dtTerminoProrrogacao';
        } 
        $stJs .= validaPeriodicidade($stDataInicio, $stDataTermino, $stCampoDataTermino);
        break;
}
if ($stJs) {
    echo $stJs;
}
?>