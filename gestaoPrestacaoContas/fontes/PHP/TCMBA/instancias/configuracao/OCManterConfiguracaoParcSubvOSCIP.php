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
  * Página Oculta de Configuração de Termos de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: OCManterConfiguracaoParcSubvOSCIP.php 64116 2015-12-03 19:33:49Z evandro $
  * $Revision: 64116 $
  * $Author: evandro $
  * $Date: 2015-12-03 17:33:49 -0200 (Thu, 03 Dec 2015) $
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoDespesa.class.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceria.class.php';
require_once CAM_GPC_TCMBA_MAPEAMENTO.'TTCMBATermoParceriaDotacao.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoParcSubvOSCIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stCtrl = $request->get('stCtrl');

function consultaTermoParceria()
{
    $arTermoParcerias = Sessao::remove('arTermoParcerias');

    $rsTermoParcerias = new RecordSet();
    
    $obTTCMBATermoParceria = new TTCMBATermoParceria();
    $obTTCMBATermoParceria->setDado('exercicio'   , $_REQUEST['stExercicioTermo']   );
    $obTTCMBATermoParceria->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTTCMBATermoParceria->recuperaPorChave($rsTermoParcerias, $boTransacao);

    $rsTermoParcerias->ordena('nro_processo', "ASC", SORT_STRING);
    $rsTermoParcerias->addFormatacao("vl_parceiro_publico", "NUMERIC_BR");
    $rsTermoParcerias->addFormatacao("vl_termo_parceria"  , "NUMERIC_BR");

    while(!$rsTermoParcerias->eof())
    {
        $inNovo = ($rsTermoParcerias->getCorrente()-1);
        $arTermoParcerias[$inNovo]["inId"]                 = $inNovo;
        $arTermoParcerias[$inNovo]["exercicio"]            = $rsTermoParcerias->getCampo("exercicio");
        $arTermoParcerias[$inNovo]["cod_entidade"]         = $rsTermoParcerias->getCampo("cod_entidade");
        $arTermoParcerias[$inNovo]["nro_processo"]         = $rsTermoParcerias->getCampo("nro_processo");
        $arTermoParcerias[$inNovo]["dt_assinatura"]        = $rsTermoParcerias->getCampo("dt_assinatura");
        $arTermoParcerias[$inNovo]["dt_publicacao"]        = $rsTermoParcerias->getCampo("dt_publicacao");
        $arTermoParcerias[$inNovo]["imprensa_oficial"]     = $rsTermoParcerias->getCampo("imprensa_oficial");
        $arTermoParcerias[$inNovo]["dt_inicio"]            = $rsTermoParcerias->getCampo("dt_inicio");
        $arTermoParcerias[$inNovo]["dt_termino"]           = $rsTermoParcerias->getCampo("dt_termino");
        $arTermoParcerias[$inNovo]["numcgm"]               = $rsTermoParcerias->getCampo("numcgm");
        $arTermoParcerias[$inNovo]["objeto"]               = $rsTermoParcerias->getCampo("objeto");
        $arTermoParcerias[$inNovo]["nro_processo_mj"]      = $rsTermoParcerias->getCampo("nro_processo_mj");
        $arTermoParcerias[$inNovo]["dt_processo_mj"]       = $rsTermoParcerias->getCampo("dt_processo_mj");
        $arTermoParcerias[$inNovo]["dt_publicacao_mj"]     = $rsTermoParcerias->getCampo("dt_publicacao_mj");
        $arTermoParcerias[$inNovo]["processo_licitatorio"] = $rsTermoParcerias->getCampo("processo_licitatorio");
        $arTermoParcerias[$inNovo]["processo_dispensa"]    = $rsTermoParcerias->getCampo("processo_dispensa");
        $arTermoParcerias[$inNovo]["vl_parceiro_publico"]  = $rsTermoParcerias->getCampo("vl_parceiro_publico");
        $arTermoParcerias[$inNovo]["vl_termo_parceria"]    = $rsTermoParcerias->getCampo("vl_termo_parceria");
        
        $rsTermoParcerias->proximo();
    }
    $rsTermoParcerias->setPrimeiroElemento();
    
    Sessao::write('arTermoParcerias', $arTermoParcerias);
    
    $stJs .= geraSpanTermoParceria();
    return $stJs;
}

function alterarTermoParceria()
{
    $arTermoParcerias = Sessao::read("arTermoParcerias");
    $rsTermoParceriaDotacoes = new RecordSet();

    if(!is_array($arTermoParcerias))
        $arTermoParcerias = array();

    foreach($arTermoParcerias AS $arTermoParceiro) {
        if($arTermoParceiro["inId"] == $_REQUEST["inId"]){
            $stJs .= "jQuery('#stExercicioTermo').val(\"".$arTermoParceiro["exercicio"]."\"); \n ";
            $stJs .= "jQuery('#stExercicioProcesso').val(\"".$arTermoParceiro["exercicio"]."\"); \n ";
            $stJs .= "jQuery('#stExercicioProcesso').prop('disabled', true); \n ";
            $stJs .= "jQuery('#hdnExercicioProcesso').val(\"".$arTermoParceiro["exercicio"]."\"); \n ";
            $stJs .= "jQuery('#stNumeroProcesso').val(\"".$arTermoParceiro["nro_processo"]."\"); \n ";
            $stJs .= "jQuery('#stHdnNumeroProcessoAnterior').val(\"".$arTermoParceiro["nro_processo"]."\"); \n ";
            $stJs .= "jQuery('#stDtAssinatura').val(\"".$arTermoParceiro["dt_assinatura"]."\"); \n ";
            $stJs .= "jQuery('#stDtPublicacao').removeProp('disabled'); \n ";
            $stJs .= "jQuery('#stDtPublicacao').val(\"".$arTermoParceiro["dt_publicacao"]."\"); \n ";
            $stJs .= "jQuery('#stImprensaOficial').val(\"".$arTermoParceiro["imprensa_oficial"]."\"); \n ";
            $stJs .= "jQuery('#stDtInicioTermo').val(\"".$arTermoParceiro["dt_inicio"]."\"); \n ";
            $stJs .= "jQuery('#stDtTerminoTermo').removeProp('disabled'); \n ";
            $stJs .= "jQuery('#stDtTerminoTermo').val(\"".$arTermoParceiro["dt_termino"]."\"); \n ";
            $stJs .= "jQuery('#inCGMParceria').focus(); \n ";
            $stJs .= "jQuery('#inCGMParceria').val(\"".$arTermoParceiro["numcgm"]."\"); \n ";
            $stJs .= "jQuery('#txtObjeto').focus(); \n ";
            $stJs .= "jQuery('#txtObjeto').val(\"".$arTermoParceiro["objeto"]."\"); \n ";
            $stJs .= "jQuery('#stProcessoMJ').val(\"".$arTermoParceiro["nro_processo_mj"]."\"); \n ";
            $stJs .= "jQuery('#dtProcessoMJ').val(\"".$arTermoParceiro["dt_processo_mj"]."\"); \n ";
            $stJs .= "jQuery('#dtPublicacaoMJ').val(\"".$arTermoParceiro["dt_publicacao_mj"]."\"); \n ";
            $stJs .= "jQuery('#stProcessoLicitatorio').val(\"".$arTermoParceiro["processo_licitatorio"]."\"); \n ";
            $stJs .= "jQuery('#stProcessoDispensa').val(\"".$arTermoParceiro["processo_dispensa"]."\"); \n ";
            $stJs .= "jQuery('#vlParceiroPublico').val(\"".$arTermoParceiro["vl_parceiro_publico"]."\"); \n ";
            $stJs .= "jQuery('#vlParceiroOSCIP').val(\"".$arTermoParceiro["vl_termo_parceria"]."\"); \n ";
            $stJs .= "jQuery('#Ok').prop('value','Alterar'); \n ";
            $stJs .= "jQuery('#stAcao').val('configurar'); \n ";
            
            $obTTCMBATermoParceriaDotacao = new TTCMBATermoParceriaDotacao();
            $obTTCMBATermoParceriaDotacao->setDado('exercicio'   , $arTermoParceiro["exercicio"]    );
            $obTTCMBATermoParceriaDotacao->setDado('cod_entidade', $arTermoParceiro["cod_entidade"]);
            $obTTCMBATermoParceriaDotacao->setDado('nro_processo', $arTermoParceiro["nro_processo"] );
            $obTTCMBATermoParceriaDotacao->recuperaPorChave($rsTermoParceriaDotacoes, $boTransacao);
            
            $arDotacoes = array();
            $rsDespesa = new RecordSet();
            
            foreach($rsTermoParceriaDotacoes->getElementos() AS $arDotacao){
                $stCondicao  = " AND O.cod_despesa = ".$arDotacao["cod_despesa"];
                $stCondicao .= " AND O.exercicio   = '".$arDotacao["exercicio_despesa"]."' ";
                
                $stOrdem = " ORDER BY dotacao";
                
                $obTOrcamentoDespesa = new TOrcamentoDespesa();
                $obTOrcamentoDespesa->setDado('exercicio', $arDotacao["exercicio_despesa"]);
                $obTOrcamentoDespesa->recuperaDespesaDotacao($rsDespesa, $stCondicao, $stOrdem, $boTransacao);
                
                $inNovo = count($arDotacoes);
                $arDotacoes[$inNovo]['inId']              = $inNovo;
                $arDotacoes[$inNovo]['exercicio_despesa'] = $arDotacao["exercicio_despesa"];
                $arDotacoes[$inNovo]['cod_despesa']       = $rsDespesa->getCampo('cod_despesa');
                $arDotacoes[$inNovo]['nom_despesa']       = $rsDespesa->getCampo('descricao');
                $arDotacoes[$inNovo]['dotacao']           = $rsDespesa->getCampo('dotacao');
            }
            
            Sessao::write("arDotacoes", $arDotacoes);
            $stJs .= gerarSpanDotacoes();    
        }
    }
    
    return $stJs;
}

function geraSpanTermoParceria()
{
    $rsRecordSet = new recordset();
    $rsRecordSet->preenche(Sessao::read("arTermoParcerias"));

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Termos de Parceria" );
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Número do Processo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Assinatura" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Publicação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Imprensa Oficial" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Início" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Término" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[nro_processo]/[exercicio]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dt_assinatura]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dt_publicacao]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[imprensa_oficial]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dt_inicio]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dt_termino]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarTermoParceria');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "excluir" );
    $obLista->ultimaAcao->setFuncaoAjax( false );
    $obLista->ultimaAcao->setLink( CAM_GPC_TCMBA_INSTANCIAS."configuracao/PRManterConfiguracaoParcSubvOSCIP.php?" );
    $obLista->ultimaAcao->addCampo("inId"  ,"inId");
    $obLista->ultimaAcao->addCampo("inCodEntidade"  ,"cod_entidade");
    $obLista->ultimaAcao->addCampo("stAcao=excluirTermoParceria&1","");
    $obLista->ultimaAcao->addCampo("frameDestino=oculto&2","");
    $obLista->commitAcao();

    $obLista->montaInnerHtml();
    $stHtml = $obLista->getHTML();
    $stJs .= "jQuery('#spnListaTermosCadastrados').html('".$stHtml."');";

    return $stJs;
}

function excluirDotacoes()
{
    $arDotacoes = Sessao::read("arDotacoes");

    $arTemp = array();
    foreach ($arDotacoes as $arDotacao) {
        if ($arDotacao["inId"] != $_GET["inId"]) {
            $arTemp[] = $arDotacao;
        }
    }
    Sessao::write('arDotacoes',$arTemp);
    $stJs .= gerarSpanDotacoes();

    return $stJs;
}

function incluirDotacoes()
{
    $stJs = processarDotacoes("incluir");

    return $stJs;
}

function processarDotacoes($stAcao)
{
    $arDotacoes = Sessao::read("arDotacoes");
 
    if (!is_array($arDotacoes))
        $arDotacoes = array();
        
    if( validaInclusaoListaDotacoes($arDotacoes) ) {
        $stCondicao  = " AND O.cod_despesa = ".$_REQUEST['inCodDespesa'];
        $stCondicao .= " AND O.exercicio   = '".Sessao::getExercicio()."' ";
        
        $stOrdem = " ORDER BY dotacao";
        
        $obTOrcamentoDespesa = new TOrcamentoDespesa();
        $obTOrcamentoDespesa->setDado('exercicio', Sessao::getExercicio());
        $obTOrcamentoDespesa->recuperaDespesaDotacao($rsDespesa, $stCondicao, $stOrdem, $boTransacao);
        
        $inNovo = count($arDotacoes);
        $arDotacoes[$inNovo]['inId']              = $inNovo;
        $arDotacoes[$inNovo]['exercicio_despesa'] = Sessao::getExercicio();
        $arDotacoes[$inNovo]['cod_despesa']       = $rsDespesa->getCampo('cod_despesa');
        $arDotacoes[$inNovo]['nom_despesa']       = $rsDespesa->getCampo('descricao');
        $arDotacoes[$inNovo]['dotacao']           = $rsDespesa->getCampo('dotacao');
        
        Sessao::write("arDotacoes", $arDotacoes);
        $stJs .= gerarSpanDotacoes();
    } else {
        $stJs .= "alertaAviso('Essa dotação já foi incluida na lista.','form','erro','".Sessao::getId()."');\n";
    }
    return $stJs;
}

function validaInclusaoListaDotacoes($arDotacoes)
{
    foreach ($arDotacoes as $key => $value) {
        if ( $_REQUEST['inCodDespesa'] == $value['cod_despesa'] ) {
            return false;
        }
    }
    return true;
}

function gerarSpanDotacoes()
{
    $rsRecordSet = new recordset();
    $rsRecordSet->preenche(Sessao::read("arDotacoes"));

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Dotações de despesas do Termo de Parceria" );
    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Dotação / Descrição" );
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Ação" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[dotacao] - [nom_despesa]");
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirDotacoes');");
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaInnerHtml();
    $stHtml = $obLista->getHTML();
    $stJs .= "jQuery('#spnDotacoes').html('".$stHtml."');";

    return $stJs;
}

function validaPeriodicidade( $stDtInicio, $stDtTermino, $stCampoDataTermino) {
    if($stCampoDataTermino == 'stDtPublicacao') {
        $boMaiorIgual = false;
    } else {
        $boMaiorIgual = true;
    }
    
    if(SistemaLegado::comparaDatas($stDtInicio,$stDtTermino, $boMaiorIgual)){
        $stJs .= "jQuery(\"#".$stCampoDataTermino."\").val(''); \n";
        $stJs .= "jQuery(\"#".$stCampoDataTermino."\").focus(); \n";
        if($stCampoDataTermino == 'stDtPublicacao') {
            $stJs .= "alertaAviso('Data de Publicação, deve ser maior ou igual que a Data de Assinatura (".$stDtInicio.").','form','aviso','".Sessao::getId()."');\n";
        } else {
            $stJs .= "alertaAviso('Data de Término, deve ser maior que a Data de Início (".$stDtInicio.").','form','aviso','".Sessao::getId()."');\n";
        }
    }
    return $stJs;
}

function validaTermoParceria(){
    $arTermoParcerias = Sessao::read("arTermoParcerias");
    if(!is_array($arTermoParcerias))
        $arTermoParcerias = array();
    foreach($arTermoParcerias AS $arTermoParceria){
        if($arTermoParceria['exercicio'] == $_REQUEST['stExercicioProcesso'] && $arTermoParceria['nro_processo'] == $_REQUEST['stNumeroProcesso']){
            $stJs .= "jQuery(\"#stNumeroProcesso\").val(''); \n";
            $stJs .= "jQuery(\"#stNumeroProcesso\").focus(); \n";
            $stJs .= "alertaAviso('O Termo de Parceria (".$arTermoParceria['nro_processo']."/".$arTermoParceria['exercicio']."), já foi cadastrado.','form','aviso','".Sessao::getId()."');\n";
        }
    }
    
    return $stJs;
}

switch ($stCtrl) {
    case "consultaTermoParceria":
        $stJs .= consultaTermoParceria();
        break;
    case "alterarTermoParceria":
        $stJs .= alterarTermoParceria();
        break;
    case "incluirDotacoes":
        $stJs .= incluirDotacoes();
        break;
    case "excluirDotacoes":
        $stJs .= excluirDotacoes();
        break;
    case "validaPeriodicidade":
        if(array_key_exists('stDtInicioTermo',$request->getAll()) && array_key_exists('stDtTerminoTermo',$request->getAll())) {
            $stDataInicio  = $request->get('stDtInicioTermo');
            $stDataTermino = $request->get('stDtTerminoTermo');
            $stCampoDataTermino = 'stDtTerminoTermo';
        } else if(array_key_exists('stDtAssinatura',$request->getAll()) && array_key_exists('stDtPublicacao',$request->getAll())) {
            $stDataInicio  = $request->get('stDtAssinatura');
            $stDataTermino = $request->get('stDtPublicacao');
            $stCampoDataTermino = 'stDtPublicacao';
        }
        $stJs .= validaPeriodicidade($stDataInicio, $stDataTermino, $stCampoDataTermino);
        break;
    case "validaTermoParceria":
        $stJs .= validaTermoParceria();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>