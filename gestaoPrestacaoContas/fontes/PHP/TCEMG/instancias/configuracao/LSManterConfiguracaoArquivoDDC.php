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
/*
    * Arquivo filtro - configuração arquivo DDC TCE/MG
    * Data de Criação: 08/03/2014

    * @author Analista:      Sergio Luiz dos Santos
    * @author Desenvolvedor: Arthur Cruz

*/

include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php");
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoDDC.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoArquivoDDC";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include($pgJs);

$stCaminho = CAM_GPC_TCEMG_INSTANCIAS."configuracao/";

$obForm = new Form;
$obForm->setAction  ( $pgProc );
$obForm->setTarget  ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( "" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read("link");

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

$obTTCEMGConfiguracaoDDC = new TTCEMGConfiguracaoDDC();
$obTTCEMGConfiguracaoDDC->setDado('exercicio',$request->get('inExercicio'));
$obTTCEMGConfiguracaoDDC->setDado('mes_referencia',$request->get('inMes'));
$obTTCEMGConfiguracaoDDC->setDado('cod_entidade',$request->get('inCodEntidade'));
$obTTCEMGConfiguracaoDDC->recuperaDadosDDC($rsDadosDDC," ORDER BY configuracao_ddc.cod_norma ");

$obTOrcamentoEntidade = New TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', $request->get('inExercicio'));
$obTOrcamentoEntidade->setDado('cod_entiadade', $request->get('inCodEntidade'));
$obTOrcamentoEntidade->recuperaEntidades($rsEntidades, " AND e.cod_entidade = ".$request->get('inCodEntidade'));

$stEntidade = $request->get('inCodEntidade')." - ".$rsEntidades->getCampo('nom_cgm');

if($rsDadosDDC->getNumLinhas() <= 0){
    $stCaminho = $pgForm."?inCodEntidade=".$request->get('inCodEntidade')."&inExercicio=".$request->get('inExercicio')."&inMes=".$request->get('inMes');
    SistemaLegado::alertaAviso($stCaminho, "", "", "aviso", Sessao::getId(), "../");
}

foreach ($rsDadosDDC->arElementos as $inDadosDDC => $stDadosDDC) {
    
    $obNorma = new RNorma;
    $obNorma->setCodNorma( $rsDadosDDC->arElementos[$inDadosDDC]['nro_lei_autorizacao'] );
    $obNorma->listarDecreto( $rsNorma );
    
    $rsDadosDDC->arElementos[$inDadosDDC]['entidade'] = $stEntidade;
    $rsDadosDDC->arElementos[$inDadosDDC]['nro_lei_autorizacao_extenso'] = $rsNorma->getCampo('cod_norma')." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
    
    switch($request->get('inMes')){
        case '1':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Janeiro"; break;
        case '2':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Fevereiro"; break;
        case '3':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Março"; break;
        case '4':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Abril"; break; 
        case '5':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Maio"; break;
        case '6':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Junho"; break;
        case '7':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Julho"; break;
        case '8':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Agosto"; break;
        case '9':  $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Setembro"; break;
        case '10': $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Outubro"; break;
        case '11': $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Novembro"; break;
        case '12': $rsDadosDDC->arElementos[$inDadosDDC]['mes_extenso'] = "Dezembro"; break;
    }
}

$obLista = new Lista;
$obLista->setTitulo( "Dívidas" );
    
$obLista->setRecordSet( $rsDadosDDC );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("Exercício");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("Mês");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo( "Lei de autorização" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo( "Nº do contrato" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->ultimoCabecalho->addConteudo( "Data de assinatura" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "mes_extenso" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "entidade" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nro_lei_autorizacao_extenso" );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nro_contrato_divida" );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "dt_assinatura" );
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( "ALTERAR" );
$obLista->ultimaAcao->addCampo("&inExercicio"        , "exercicio");
$obLista->ultimaAcao->addCampo("&inMes"              , "mes_referencia");
$obLista->ultimaAcao->addCampo("&inCodEntidade"      , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inNroLeiAutorizacao", "nro_lei_autorizacao");
$obLista->ultimaAcao->addCampo("&inNroContrato"      , "nro_contrato_divida");
$obLista->ultimaAcao->setLink($pgForm."?".Sessao::getId()."&stAcao=alterar");
$obLista->commitAcao();
    
$obLista->addAcao();
$obLista->ultimaAcao->setAcao ( "excluir" );
$obLista->ultimaAcao->addCampo("&inExercicio"        , "exercicio");
$obLista->ultimaAcao->addCampo("&inMes"              , "mes_referencia");
$obLista->ultimaAcao->addCampo("&inCodEntidade"      , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inNroContrato"      , "nro_contrato_divida");
$obLista->ultimaAcao->addCampo("&stDescQuestao"      , "nro_contrato_divida");
$obLista->ultimaAcao->setLink($stCaminho.$pgProc."?".Sessao::getId()."&stAcao=excluir".$stLink);
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;

$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btnIncluir"   );
$obBtnIncluir->setValue             ( "Incluir Novo contrato de dívida" );
$obBtnIncluir->setTipo              ( "button"       );
$obBtnIncluir->obEvento->setOnClick ( "incluir(".$request->get('inExercicio').",".$request->get('inMes').",".$request->get('inCodEntidade').");"   );

$obBtnFiltro = new Button;
$obBtnFiltro->setName               ( 'filtro'                                       );
$obBtnFiltro->setValue              ( 'Filtro'                                       );
$obBtnFiltro->obEvento->setOnClick  ( "Cancelar('".$pgFilt."','telaPrincipal');"     );

$obFormulario->defineBarra          ( array( $obBtnIncluir,$obBtnFiltro ) , '', ''   );
$obFormulario->show();

?>
