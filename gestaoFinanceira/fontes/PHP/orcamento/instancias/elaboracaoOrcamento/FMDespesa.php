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
    * Página de Formulario de Inclusao/Alteracao de Fornecedores
    * Data de Criação   : 26/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    * Casos de uso: uc-02.01.06

    $Id: FMDespesa.php 59612 2014-09-02 12:00:51Z gelson $
*/

ob_start();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

if (Sessao::getExercicio() > '2009') {
    header('Location: '.CAM_GF_ORC_INSTANCIAS.'elaboracaoOrcamento/FLDespesaAcao.php?'.Sessao::getId().'&stAcao=incluir');
    exit;
} else {
    ob_flush();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
include_once CAM_GF_ORC_COMPONENTES."MontaDotacaoOrcamentaria.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPreEmpenho.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "Despesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obROrcamentoDespesa        = new ROrcamentoDespesa;
$obREmpenhoPreEmpenho       = new REmpenhoPreEmpenho;
$obMontaDotacaoOrcamentaria = new MontaDotacaoOrcamentaria;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->consultarConfiguracao();

if (Sessao::getExercicio() < '2014') {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetas();
} else {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetasDespesa();
}

if ($inUnidadesMedidasMetas > 0) {
        $inNumeroColunas = (12/$inUnidadesMedidasMetas);
}

if ($stAcao == 'alterar') {
    $obROrcamentoDespesa->setCodDespesa( $_GET['inCodDespesa'] );

    $obErro = $obROrcamentoDespesa->consultar( $rsDespesa );

    if ( !$obErro->ocorreu() ) {
        $arDespesa = $rsDespesa->getElementos();
        $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($rsDespesa->getCampo('cod_recurso'));
        $obErro = $obROrcamentoDespesa->obROrcamentoRecurso->consultar($rsRecurso);
        if ( !$obErro->ocorreu() ) {
            $inCodEntidade   = $rsDespesa->getCampo( 'cod_entidade'      );
            $inCodRecurso    = $rsDespesa->getCampo( 'cod_recurso'       );
            $stDescricaoRecurso = $rsRecurso->getCampo( 'nom_recurso' );
            $stDescRecurso   = $obROrcamentoDespesa->obROrcamentoRecurso->getNome();
            $nuValorOriginal = $rsDespesa->getCampo( 'vl_original'       );
            $nuValorOriginal = number_format( $nuValorOriginal , 2 , ',' , '.' );

            $stDespesa .= $rsDespesa->getCampo( 'num_orgao'     ).".";
            $stDespesa .= $rsDespesa->getCampo( 'num_unidade'   ).".";
            $stDespesa .= $rsDespesa->getCampo( 'cod_funcao'    ).".";
            $stDespesa .= $rsDespesa->getCampo( 'cod_subfuncao' ).".";
            $stDespesa .= $rsDespesa->getCampo( 'cod_programa'  ).".";
            $stDespesa .= $rsDespesa->getCampo( 'num_pao'       );
            $stMascClassDespesa = str_replace( '.' , '' , $_GET['stMascClassDespesa'] );
            $stDespesa = $stDespesa.".".$stMascClassDespesa;
            $arMascDotacao = Mascara::validaMascaraDinamica( $obMontaDotacaoOrcamentaria->getMascara(), $stDespesa );
            $obMontaDotacaoOrcamentaria->setValue( $arMascDotacao[1] );
            /*
             * CONSULTAR METAS
             */
            include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoDespesa.class.php"      );
            include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php" );

            $obRPrevisaoDespesa       = new ROrcamentoPrevisaoDespesa;

            $obRPrevisaoDespesa->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
            $obRPrevisaoDespesa->listarPeriodo($rsListaDespesa2, " cod_despesa = ". $obROrcamentoDespesa->getCodDespesa() . " " );
            $rsListaDespesa2->addFormatacao( 'vl_previsto', 'NUMERIC_BR' );
            $arVlPeriodo = array();
            while ( !$rsListaDespesa2->eof() ) {
               $arVlPeriodo[ $rsListaDespesa2->getCampo('periodo') ] = $rsListaDespesa2->getCampo( 'vl_previsto' );
               $rsListaDespesa2->proximo();
            }
            Sessao::write('arVlPeriodo',$arVlPeriodo);
        }
    }
} elseif ( $stAcao == 'incluir' && isset($_REQUEST['inCodEntidade']) ) {
    $inCodEntidade = $_REQUEST['inCodEntidade'];
}
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName ( "inCodConta" );
$obHdnCodConta->setValue( $inCodConta );

$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName ( "inCodFixacaoDespesa" );
$obHdnCodDespesa->setValue( $_GET['inCodDespesa'] );

$obHdnDescRecurso = new Hidden;
$obHdnDescRecurso->setName ( "stDescRecurso" );
$obHdnDescRecurso->setValue( $stDescRecurso );

$obLblDespesa = new Label;
$obLblDespesa->setRotulo( 'Código Reduzido' );
$obLblDespesa->setValue ( $_GET['inCodDespesa'] );

$obROrcamentoDespesa->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoDespesa->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade , " ORDER BY cod_entidade" );

$obCmbEntidade = new Select;
$obCmbEntidade->setName      ( 'inCodEntidade'   );
$obCmbEntidade->setValue     (  $inCodEntidade   );
$obCmbEntidade->setRotulo    ( 'Entidade'        );
$obCmbEntidade->setTitle     ( 'Selecione a entidade.' );
$obCmbEntidade->setStyle     ( "width: 400px"    );
$obCmbEntidade->setNull      ( false             );
$obCmbEntidade->setCampoId   ( "cod_entidade"    );
$obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbEntidade->addOption              ( "", "Selecione"               );
}

$obCmbEntidade->preencheCombo( $rsEntidade       );
$rsEntidade->setPrimeiroElemento();

if ($stAcao == 'alterar') {
    $obLblEntidade = new Label();
    $obLblEntidade->setRotulo( 'Entidade' );

    $obHdnEntidade = new Hidden();
    $obHdnEntidade->setName ('inCodEntidade');
    $obHdnEntidade->setValue ( $inCodEntidade );

    while (!$rsEntidade->eof()) {
        if ($rsEntidade->getCampo('cod_entidade') == $rsDespesa->getCampo('cod_entidade')) {
            $obLblEntidade->setValue ( $rsEntidade->getCampo('cod_entidade').' - '.$rsEntidade->getCampo('nom_cgm') );
            $obHdnEntidade->setValue ( $rsEntidade->getCampo('cod_entidade') );
            $rsEntidade->setUltimoElemento();
        }
        $rsEntidade->proximo();
    }

    $obLblRecurso = new Label();
    $obLblRecurso->setRotulo( 'Recurso' );
    $obLblRecurso->setValue( $rsDespesa->getCampo('cod_recurso').' - '.$_GET['stDescricaoRecurso']);
}
$obMontaDotacaoOrcamentaria->setName           ('stDotacaoOrcamentaria');
$obMontaDotacaoOrcamentaria->setRotulo         ('Dotação Orcamentaria' );
$obMontaDotacaoOrcamentaria->setActionAnterior ( $pgOcul );
$obMontaDotacaoOrcamentaria->setActionPosterior( $pgProc );
$obMontaDotacaoOrcamentaria->setTarget         ( 'oculto' );
$obMontaDotacaoOrcamentaria->setNull           ( false );

if ($stAcao == 'incluir' && !empty($_GET['stDotacaoOrcamentaria'])) {
    $obMontaDotacaoOrcamentaria->setValue		   ( substr($_GET['stDotacaoOrcamentaria'],0,22) );
    $jsOnload .= "buscaValor('preencheUnidade', '".$obMontaDotacaoOrcamentaria->getActionAnterior()."', '".$obMontaDotacaoOrcamentaria->getActionPosterior()."', '".$obMontaDotacaoOrcamentaria->getTarget()."', '".Sessao::getId()."');";
}

$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Rubrica de Despesa" );
$obBscRubricaDespesa->setTitle                ( "Informe a rubrica de despesa." );
$obBscRubricaDespesa->setNull                 ( false );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa" );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setId       ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setValue    ( '' );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnChange( "preencheComZeros( '".$stMascaraRubrica."', this, 'D' );" );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );
if ( $obRConfiguracaoOrcamento->getFormaExecucaoOrcamento() == "1" )
    $obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ( "montaParametrosGET('mascaraClassificacao');" );
else
    $obBscRubricaDespesa->setValoresBusca( CAM_GF_ORC_POPUPS.'classificacaodespesa/OCClassificacaoDespesa.php?'.Sessao::getId(), $obForm->getName(), '' );

// Monta Rubrica Despesa
$obLblRubricaDespesa = new Label;
$obLblRubricaDespesa->setRotulo( 'Rubrica de Despesa' );
$obLblRubricaDespesa->setValue ( $_GET['stMascClassDespesa'].' - '.$stDescricao );

$obHdnRubricaDespesa = new Hidden;
$obHdnRubricaDespesa->setName ( 'inCodDespesa' );
$obHdnRubricaDespesa->setValue ( $_GET['stMascClassDespesa'] );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

include_once( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setCodRecurso ( $inCodRecurso );
$obIMontaRecursoDestinacao->setDescricaoRecurso ( $stDescRecurso );
$obIMontaRecursoDestinacao->setNull( false );

$obTxtValorDotacao = new Numerico;
$obTxtValorDotacao->setRotulo   ( "Valor da Dotação Orçamentária" );
$obTxtValorDotacao->setTitle    ( "Informe o valor da dotação orçamentária." );
$obTxtValorDotacao->setName     ( "nuValorOriginal" );
$obTxtValorDotacao->setId       ( "nuValorOriginal" );
$obTxtValorDotacao->setValue    ( $nuValorOriginal );
$obTxtValorDotacao->setSize     ( 20 );
$obTxtValorDotacao->setMaxLength( 21 );
$obTxtValorDotacao->setNull     ( false );
$obTxtValorDotacao->obEvento->setOnChange(" montaParametrosGET('mudaValor','', 'sincrono'); ");
$obTxtValorDotacao->obEvento->setOnBlur(" document.getElementById('Ok').focus();");

$obLblValorDotacao = new Label;
$obLblValorDotacao->setRotulo( 'Valor da Dotação Orçamentária' );
$obLblValorDotacao->setValue ( $nuValorOriginal );

$obHdnValorDotacao = new Hidden;
$obHdnValorDotacao->setName ( "nuValorOriginal" );
$obHdnValorDotacao->setId   ( "nuValorOriginal" );
$obHdnValorDotacao->setValue( $nuValorOriginal );

//***************************************
// Preenche combos e campos Inner
//***************************************

if ($stAcao == 'alterar') {
    $js .= "buscaValor( 'preencheInner' ,'".$pgOcul."' , '".$pgProc."' , 'oculto' ,'".Sessao::getId()."');";
    SistemaLegado::executaFramePrincipal($js);
}

/**
 * METAS
 */
$obHdnNumCampos = new Hidden;
$obHdnNumCampos->setName ( "inNumCampos" );
$obHdnNumCampos->setId   ( "inNumCampos" );
$obHdnNumCampos->setValue( $inNumeroColunas );

$arTxtPorcento = array();
$arTxtValor    = array();

// Retorna a data conforme o numero de dias corridos a somar desde o inicio do ano.
function somaDiasUteis($inDiasSomar)
{
    $stData = "31/12/".(Sessao::getExercicio()-1);
    $partes=explode("/",$stData);

    return date("d/m/Y",mktime(0,0,0,$partes[1] ,$partes[0] + $inDiasSomar ,$partes[2]));
}

for ($inCountComponente = 1 ; $inCountComponente <= $inNumeroColunas ; $inCountComponente++) {
    if ($stAcao == 'alterar') {
        switch ($inUnidadesMedidasMetas) {
            case 1: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,30 ,2)), date('d/m/Y')) ? false : true; break;
            case 2: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,60 ,2)), date('d/m/Y')) ? false : true; break;
            case 3: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,90 ,2)), date('d/m/Y')) ? false : true; break;
            case 4: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,120,2)), date('d/m/Y')) ? false : true; break;
            case 6: $boReadOnly = SistemaLegado::comparaDatas(somaDiasUteis(bcmul($inCountComponente,180,2)), date('d/m/Y')) ? false : true; break;
        }
    } else $boReadOnly = false;

    $obHdnMesBloqueado = new Hidden();
    $obHdnMesBloqueado->setName( 'hdnBlock_' . ($inCountComponente) );
    $obHdnMesBloqueado->setId  ( 'hdnBlock_' . ($inCountComponente) );
    $obHdnMesBloqueado->setValue( $boReadOnly );

    $arHdnMesBloqueado[$inCountComponente] = $obHdnMesBloqueado;

    if ($boReadOnly) {
        $obTxtPorcento = new Label();
        $obTxtPorcento->setName ( 'lblvlPorcentagem_' . ($inCountComponente) );
        $obTxtPorcento->setId   ( 'lblvlPorcentagem_' . ($inCountComponente) );
        $obTxtPorcento->setValue( '' );

        $obHdnPorcento = new Hidden();
        $obHdnPorcento->setName ( 'vlPorcentagem_' . ($inCountComponente) );
        $obHdnPorcento->setId   ( 'vlPorcentagem_' . ($inCountComponente) );
        $obHdnPorcento->setValue( '' );

        $arTxtPorcento[$inCountComponente] = $obTxtPorcento;
        $arHdnPorcento[$inCountComponente] = $obHdnPorcento;

    } else {
        $obTxtPorcento = new Porcentagem();
        $obTxtPorcento->setName ( 'vlPorcentagem_' . ($inCountComponente) );
        $obTxtPorcento->setId   ( 'vlPorcentagem_' . ($inCountComponente) );
        $obTxtPorcento->setValue( '' );
        $obTxtPorcento->setReadOnly ( $boReadOnly );
        $obTxtPorcento->obEvento->setOnChange( "montaParametrosGET('mudaValor');" );

        $arTxtPorcento[$inCountComponente] = $obTxtPorcento;
    }

    if ($boReadOnly) {
        $obTxtValor = new Label();
        $obTxtValor->setName ( 'lblvlValor_' . ($inCountComponente) );
        $obTxtValor->setId   ( 'lblvlValor_' . ($inCountComponente) );
        $obTxtValor->setValue( $vlPeriodo );

        $obHdnValor = new Hidden();
        $obHdnValor->setName ( 'vlValor_' . ($inCountComponente) );
        $obHdnValor->setId   ( 'vlValor_' . ($inCountComponente) );
        $obHdnValor->setValue( $vlPeriodo );

        $arTxtValor[$inCountComponente] = $obTxtValor;
        $arHdnValor[$inCountComponente] = $obHdnValor;

    } else {
        $obTxtValor = new Moeda();
        $obTxtValor->setName ( 'vlValor_' . ($inCountComponente) );
        $obTxtValor->setId   ( 'vlValor_' . ($inCountComponente) );
        $obTxtValor->setValue( $vlPeriodo );
        $obTxtValor->setReadOnly ( $boReadOnly );
        $obTxtValor->setSize      ( 13 );
        $obTxtValor->setMaxLength ( 21 );
        $obTxtValor->obEvento->setOnChange( "montaParametrosGET('mudaPorcentagem');" );

        $arTxtValor[$inCountComponente] = $obTxtValor;
    }

}

$obHdnTotalPorcento = new Hidden();
$obHdnTotalPorcento->setName( "TotalPorcento" );
$obHdnTotalPorcento->setId  ( "TotalPorcento" );
$obHdnTotalPorcento->setValue ( '0,00' );
$obHdnTotalPorcento->montaHTML();

$obLblTotalPorcento = new Label();
$obLblTotalPorcento->setName ('lblTotalPorcento' );
$obLblTotalPorcento->setId   ('lblTotalPorcento' );
$obLblTotalPorcento->montaHTML();

$obHdnTotalValor = new Hidden();
$obHdnTotalValor->setName( "TotalValor" );
$obHdnTotalValor->setId  ( "TotalValor" );
$obHdnTotalValor->setValue ( '0,00' );
$obHdnTotalValor->montaHTML();

$obLblTotalValor = new Label();
$obLblTotalValor->setName( "lblTotalValor" );
$obLblTotalValor->setId  ( "lblTotalValor" );
$obLblTotalValor->montaHTML();

$arLista = array();

$arLista[0]['titulo'] = 'Porcentagem';
$arLista[1]['titulo'] = 'Valor';

$arLista[0]['total'] = $obHdnTotalPorcento->getHtml().$obLblTotalPorcento->getHTML();
$arLista[1]['total'] = $obHdnTotalValor->getHtml().$obLblTotalValor->getHTML();

for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    $arHdnMesBloqueado[$i]->montaHTML();
    if ($arHdnPorcento[$i]) {
        $arTxtPorcento[$i]->montaHTML();
        $arHdnPorcento[$i]->montaHTML();
        $arLista[0]['campo_' . $i] = $arTxtPorcento[$i]->getHTML().$arHdnPorcento[$i]->getHTML().$arHdnMesBloqueado[$i]->getHTML();
    } else {
        $arTxtPorcento[$i]->montaHTML();
        $arLista[0]['campo_' . $i] = $arTxtPorcento[$i]->getHTML().$arHdnMesBloqueado[$i]->getHTML();
    }
}
for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    if ($arHdnValor[$i]) {
        $arTxtValor[$i]->montaHTML();
        $arHdnValor[$i]->montaHTML();
        $arLista[1]['campo_' . $i] = $arTxtValor[$i]->getHTML().$arHdnValor[$i]->getHTML();

    } else {
        $arTxtValor[$i]->montaHTML();
        $arLista[1]['campo_' . $i] = $arTxtValor[$i]->getHTML();
    }
}

//inclusao do valor empenhado por periodo
if ($stAcao == 'alterar') {
    //Recupera o valor arrecadado para a receita, dividindo por mes
    include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteDespesa.class.php" );

    $obFBalanceteDespesa = new FOrcamentoBalanceteDespesa();

    $obFBalanceteDespesa->setDado("exercicio",Sessao::getExercicio() );
    $obFBalanceteDespesa->setDado("stFiltro",' AND od.cod_entidade = '.$inCodEntidade.' ');
    $obFBalanceteDespesa->setDado("stEntidade",$inCodEntidade );
    $obFBalanceteDespesa->setDado("stCodEstruturalInicial",'');
    $obFBalanceteDespesa->setDado("stCodEstruturalFinal",'');
    $obFBalanceteDespesa->setDado("stCodReduzidoInicial",$_GET['inCodDespesa'] );
    $obFBalanceteDespesa->setDado("stCodReduzidoFinal",$_GET['inCodDespesa'] );
    $obFBalanceteDespesa->setDado("stControleDetalhado",'');
    $obFBalanceteDespesa->setDado("inNumOrgao", $rsDespesa->getCampo( 'num_orgao' ) );
    $obFBalanceteDespesa->setDado("inNumUnidade", $rsDespesa->getCampo( 'num_unidade' ) );

    $stOrder = "";

    $arLista[2]['titulo'] = 'Empenhado';

    for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
        $obFBalanceteDespesa->setDado("stDataInicial",date('d/m/Y',mktime(0,0,0,($i*(12/$inNumeroColunas)-((12/$inNumeroColunas)-1)),01,Sessao::getExercicio())));
        $obFBalanceteDespesa->setDado("stDataFinal",date('d/m/Y',mktime(0,0,0,$i*(12/$inNumeroColunas)+1,0,Sessao::getExercicio())));

        // Serve para fazer uma verificação interna onde não cria e dropa as tabelas temporarias
        if ($i == 1) {
            $obFBalanceteDespesa->setDado("stVerificaCreateDropTables", 'create');
        } elseif ($i != $inNumeroColunas) {
            $obFBalanceteDespesa->setDado("stVerificaCreateDropTables", 'continue');
        } else {
            $obFBalanceteDespesa->setDado("stVerificaCreateDropTables", 'drop');
        }
        $obFBalanceteDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );

        while ( !$rsRecordSet->eof() ) {
            $inVlTotal += $rsRecordSet->getCampo('empenhado_per')-$rsRecordSet->getCampo('anulado_per');
            $arLista[2]['campo_'.$i] += $rsRecordSet->getCampo('empenhado_per')-$rsRecordSet->getCampo('anulado_per');

            $rsRecordSet->proximo();
        }
        $arLista[2]['campo_'.$i] = number_format($arLista[2]['campo_'.$i],2,',','.');
   }

   $arLista[2]['total'] = number_format($inVlTotal,'2',',','.');
}

$rsLista = new RecordSet();
$rsLista->preenche ( $arLista );

$obLista = new Lista();
$obLista->setRecordSet( $rsLista );
$obLista->setNumeracao( false );
$obLista->setMostraPaginacao( false );
$obLista->setTitulo ( "Registros de Metas de Execução da Despesa" );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Despesa" );
$obLista->ultimoCabecalho->setWidth   ( 5 );
$obLista->ultimoCabecalho->setRowSpan ( 2 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Períodos" );
$obLista->ultimoCabecalho->setWidth   ( 5 );
$obLista->ultimoCabecalho->setColSpan ( $inNumeroColunas + 1 );
$obLista->commitCabecalho();

$bo = true;
for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    $obLista->addCabecalho($bo);
    $obLista->ultimoCabecalho->addConteudo ( $i . "º" );
    $obLista->ultimoCabecalho->setWidth    ( 10 );
    $obLista->commitCabecalho();
    $bo = false;
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo ( "Total" );
$obLista->ultimoCabecalho->setWidth    ( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento( "CSS" );
$obLista->ultimoDado->setClass( 'label' );
$obLista->ultimoDado->setCampo( 'titulo' );
$obLista->commitDado();

for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[campo_".$i."]" );
    $obLista->commitDadoComponente();
}

$obLista->addDado();
$obLista->ultimoDado->setCampo( "total" );
$obLista->commitDadoComponente();

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-02.01.06"             );

$obFormulario->addHidden( $obHdnCtrl                );
$obFormulario->addHidden( $obHdnAcao                );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obFormulario->addHidden( $obHdnCodConta            );
$obFormulario->addHidden( $obHdnDescRecurso         );
$obFormulario->addHidden( $obHdnCodDespesa          );
$obFormulario->addHidden( $obHdnNumCampos           );

$obFormulario->addTitulo( "Dados para Despesa"             );
if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblDespesa            );

    $obREmpenhoPreEmpenho->setExercicio ( Sessao::getExercicio() );
    $obREmpenhoPreEmpenho->setCodEntidade($inCodEntidade);
    $obREmpenhoPreEmpenho->obROrcamentoDespesa->setCodDespesa( $_GET['inCodDespesa'] );
    $obREmpenhoPreEmpenho->consultarExistenciaDespesa();

    if ($obREmpenhoPreEmpenho->getCountDespesaExercicio() != 0) {
        $obFormulario->addComponente( $obLblEntidade );
        $obFormulario->addHidden    ( $obHdnEntidade );
        $obMontaDotacaoOrcamentaria->geraFormulario( $obFormulario,$stAcao,$obREmpenhoPreEmpenho->getCountDespesaExercicio(),$arDespesa);
        $obFormulario->addComponente( $obLblRubricaDespesa   );
        $obFormulario->addHidden    ( $obHdnRubricaDespesa   );
        $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    } else {
        $obFormulario->addComponente( $obCmbEntidade );
        $obMontaDotacaoOrcamentaria->geraFormulario( $obFormulario,$stAcao,$obREmpenhoPreEmpenho->getCountDespesaExercicio(),$arDespesa);
        $obFormulario->addComponente( $obBscRubricaDespesa   );
        $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    }

    $obHdnEntidadeAtual = new hidden();
    $obHdnEntidadeAtual->setName('inHdnEntidadeAtual');
    $obHdnEntidadeAtual->setValue($inCodEntidade);
    $obFormulario->addHidden( $obHdnEntidadeAtual           );

    if ($obREmpenhoPreEmpenho->getCountDespesaExercicio() == 0) {
        $obFormulario->addComponente( $obTxtValorDotacao  );
    } else {
        $jsOnload .= "alertaAviso('@Já existe movimentação nas contas - somente as metas e o recurso podem ser alterados.','form','erro','".Sessao::getId()."');\n";
        $obFormulario->addComponente( $obLblValorDotacao  );
        $obFormulario->addHidden    ( $obHdnValorDotacao );
    }
} else {
    $obFormulario->addComponente( $obCmbEntidade );
    $obMontaDotacaoOrcamentaria->geraFormulario( $obFormulario,$stAcao,$obREmpenhoPreEmpenho->getCountDespesaExercicio(),$arDespesa);
    $obFormulario->addComponente( $obBscRubricaDespesa   );
    $obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
    $obFormulario->addComponente( $obTxtValorDotacao  );
}

$obFormulario->addLista( $obLista );

//Define os botões de ação do formulário
$obBtnOK = new OK;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "limpaForm();" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;

$arBtn = array();
$arBtn[] = $obBtnOK;
$arBtn[] = $obBtnLimpar;
if ($stAcao=='alterar') {
    $obFormulario->Cancelar($stLocation);
} else {
$obFormulario->defineBarra( $arBtn );
}

$obFormulario->show();

if ($stAcao == 'alterar') {
    $jsOnload .= "montaParametrosGET('preencheMetas','','sincrono');";
    $jsOnload .= "montaParametrosGET('mudaPorcentagem','','sincrono');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
