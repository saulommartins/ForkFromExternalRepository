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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"                       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"         );

//Define o nome dos arquivos PHP
$stPrograma = "Receita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$inCodRecurso = isset($inCodRecurso) ? $inCodRecurso : null;
$nuValorOriginal = isset($nuValorOriginal) ? $nuValorOriginal : 0;
$boCreditoTributario = isset($boCreditoTributario) ? $boCreditoTributario : false;
$jsOnload = isset($jsOnload) ? $jsOnload : null;
$inCodContaCreditoTributario = null;
$obROrcamentoReceita = new ROrcamentoReceita;
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();

if (Sessao::getExercicio() < '2014') {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetas();
} else {
    $inUnidadesMedidasMetas = $obRConfiguracaoOrcamento->getUnidadeMedidaMetasReceita();
}

if ($inUnidadesMedidasMetas > 0) {
        $inNumeroColunas = (12/$inUnidadesMedidasMetas);
}
$stDescricaoRecurso = $request->get('stDescricaoRecurso');

if ($stAcao == 'alterar') {

    include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoReceita.class.php");
    $obRContablidadeLancamentoReceita   = new RContabilidadeLancamentoReceita;
    $obRContablidadeLancamentoReceita->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio ( Sessao::getExercicio() );
    $obRContablidadeLancamentoReceita->consultarExistenciaReceita();

    if ( $obRContablidadeLancamentoReceita->getCountReceitaExercicio() > 0) {
        $jsOnload .= "alertaAviso('@Já existe movimentação nas contas - somente as metas , a característica peculiar e o recurso poderão ser alterados.','form','erro','".Sessao::getId()."');\n";
        $boArrecadado = true;
    }

    $obROrcamentoReceita->setCodReceita( $_GET['inCodReceita'] );
    $obErro = $obROrcamentoReceita->consultar( $rsReceita );
    
    if ( !$obErro->ocorreu() ) {
        $inCodEntidade   = $rsReceita->getCampo( 'cod_entidade'      );
        $inCodRecurso    = $rsReceita->getCampo( 'cod_recurso'       );
        $nuValorOriginal = $rsReceita->getCampo( 'vl_original'       );
        $boCreditoTributario = $rsReceita->getCampo('credito_tributario') == "t" ? "S" : "N";
        $nuValorOriginal = number_format( $nuValorOriginal , 2 , ',' , '.' );

        if ($boCreditoTributario == "S") {
            include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoReceitaCreditoTributario.class.php';

            $obTOrcamentoReceitaCreditoTributario = new TOrcamentoReceitaCreditoTributario;
            $obTOrcamentoReceitaCreditoTributario->setDado('cod_receita', $_GET['inCodReceita']);
            $obTOrcamentoReceitaCreditoTributario->setDado('exercicio'  , Sessao::getExercicio());

            $obTOrcamentoReceitaCreditoTributario->recuperaPorChave($rsContaCreditoTributario, $boTransacao);
            if ($rsContaCreditoTributario->getNumLinhas() > 0) {
                $inCodContaCreditoTributario = $rsContaCreditoTributario->getCampo('cod_conta');
            }
        }

        /*
         * CONSULTAR METAS
         */
        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoReceita.class.php"      );
        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrevisaoOrcamentaria.class.php" );

        $obRPrevisaoReceita       = new ROrcamentoPrevisaoReceita;

        $obRPrevisaoReceita->obROrcamentoReceita->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
        $obRPrevisaoReceita->listarPeriodo($rsListaReceita2, " cod_receita = ". $obROrcamentoReceita->getCodReceita() . " " );
        $rsListaReceita2->addFormatacao( 'vl_periodo', 'NUMERIC_BR' );
        $arVlPeriodo = array();
        while ( !$rsListaReceita2->eof() ) {
           $arVlPeriodo[ $rsListaReceita2->getCampo('periodo') ] = $rsListaReceita2->getCampo( 'vl_periodo' );
           $rsListaReceita2->proximo();
        }
        Sessao::write('arVlPeriodo',$arVlPeriodo);
    }
} elseif ($stAcao == "incluir") {
    $nuValorOriginal = '0,00';
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

$obHdnCodReceita = new Hidden;
$obHdnCodReceita->setName ( "inCodFixacaoReceita" );
$obHdnCodReceita->setValue( $request->get('inCodReceita') );

$obHdnDescRecurso = new Hidden;
$obHdnDescRecurso->setName ( "stDescricaoRecurso" );
$obHdnDescRecurso->setValue( $stDescricaoRecurso );

$obHdnCodEstrutural = new Hidden();
$obHdnCodEstrutural->setName ( "inCodEstrutural" );
$obHdnCodEstrutural->setValue( $request->get('stMascClassReceita') );

$obHdnCodContaCreditoTributario = new Hidden();
$obHdnCodContaCreditoTributario->setName ("inCodContaCreditoTributario");
$obHdnCodContaCreditoTributario->setValue($inCodContaCreditoTributario);

$obLblReceita = new Label;
$obLblReceita->setRotulo( 'Código Reduzido' );
$obLblReceita->setValue ($request->get('inCodReceita') );

$inCodEntidade = isset($inCodEntidade) ? $inCodEntidade : $request->get('inCodEntidade');
$boArrecadado  = isset($boArrecadado)  ? $boArrecadado  : false;

if ($boArrecadado) {
    include_once ( CAM_GF_ORC_COMPONENTES."ILabelEntidade.class.php" );
    $obILabelEntidade = new ILabelEntidade( $obForm );
    $obILabelEntidade->setMostraCodigo( true );
    $obILabelEntidade->setCodEntidade( $inCodEntidade );

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName ('inCodEntidade');
    $obHdnCodEntidade->setValue ( $inCodEntidade );
} else {
    include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
    $obIEntidade = new ITextBoxSelectEntidadeUsuario(true);
    $obIEntidade->setNull( false );
    if($inCodEntidade)
        $obIEntidade->setCodEntidade( $inCodEntidade );
}

$stMascaraRubrica = $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaMascara();

if ($boArrecadado) {
    include_once( CAM_GF_ORC_COMPONENTES."ILabelReceitaRecurso.class.php");
    // Define Objeto ILabelReceitaRecurso para label da receita
    $obILabelReceitaRecurso = new ILabelReceitaRecurso( $obForm );
    $obILabelReceitaRecurso->setMostraMascaraClass ( true );
    $obILabelReceitaRecurso->setCodReceita( $_REQUEST['inCodReceita'] );
} else {
    $obBscRubricaReceita = new BuscaInner;
    $obBscRubricaReceita->setRotulo               ( "Classificação de Receita" );
    $obBscRubricaReceita->setTitle                ( "Informe a rubrica de receita." );
    $obBscRubricaReceita->setNulL                 ( false );
    $obBscRubricaReceita->setId                   ( "stDescricaoReceita" );
    $obBscRubricaReceita->setValue                ( $request->get('stDescricao') );
    $obBscRubricaReceita->obCampoCod->setName     ( "inCodReceita" );
    $obBscRubricaReceita->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
    $obBscRubricaReceita->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
    $obBscRubricaReceita->obCampoCod->setValue    ( $request->get('stMascClassReceita') );
    $obBscRubricaReceita->obCampoCod->setAlign    ("left");
    $obBscRubricaReceita->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
    $obBscRubricaReceita->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
    $obBscRubricaReceita->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaoreceita/FLClassificacaoReceita.php','frm','inCodReceita','stDescricaoReceita','','".Sessao::getId()."','800','550');" );
    $obBscRubricaReceita->setValoresBusca( CAM_GF_ORC_POPUPS.'classificacaoreceita/OCClassificacaoReceita.php?'.Sessao::getId(), $obForm->getName(), 'buscaAnalitica' );
    $obBscRubricaReceita->obCampoCod->obEvento->setOnBlur("montaParametrosGET('buscaAnalitica');");
}

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

include_once( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setCodRecurso ( $inCodRecurso );
$obIMontaRecursoDestinacao->setDescricaoRecurso ( $stDescricaoRecurso );
$obIMontaRecursoDestinacao->setNull( false );

$obTxtValorDotacao = new Numerico;
$obTxtValorDotacao->setRotulo   ( "Valor de Previsão da Receita" );
$obTxtValorDotacao->setTitle    ( "Informe o valor de previsão da receita." );
$obTxtValorDotacao->setName     ( "nuValorOriginal" );
$obTxtValorDotacao->setId       ( "nuValorOriginal" );
$obTxtValorDotacao->setValue    ( $nuValorOriginal );
$obTxtValorDotacao->setSize     ( 28 );
$obTxtValorDotacao->setMaxLength( 21 );
$obTxtValorDotacao->setDecimais ( 2 );
$obTxtValorDotacao->setNull     ( false );
$obTxtValorDotacao->setNegativo ( false );
$obTxtValorDotacao->obEvento->setOnChange(" montaParametrosGET('mudaValor','', 'sincrono'); ");

$obLblValorDotacao = new Label;
$obLblValorDotacao->setRotulo( 'Valor de Previsão da Receita' );
$obLblValorDotacao->setValue ( $nuValorOriginal );

$obHdnValorDotacao = new Hidden;
$obHdnValorDotacao->setName ( "nuValorOriginal" );
$obHdnValorDotacao->setId   ( "nuValorOriginal" );
$obHdnValorDotacao->setValue( $nuValorOriginal );

$obRdnCreditoTributario = new SimNao;
$obRdnCreditoTributario->setRotulo ( 'Crédito Tributário');
$obRdnCreditoTributario->setName   ( "boCreditoTributario" );
$obRdnCreditoTributario->setTitle  ( "Informe se esta receita é um crédito tributário histórico" );
$obRdnCreditoTributario->setChecked( $boCreditoTributario );
$obRdnCreditoTributario->obRadioSim->obEvento->setOnClick ( "executaFuncaoAjax('montaContaCreditoTributario'); ");
$obRdnCreditoTributario->obRadioNao->obEvento->setOnClick ( "executaFuncaoAjax('desmontaContaCreditoTributario'); ");

$obSpnContaCreditoTributario = new Span;
$obSpnContaCreditoTributario->setID("spnContaCreditoTributario");

//***************************************
// Preenche combos e campos Inner
//***************************************

if ($stAcao == 'alterar' && !$boArrecadado) {
    $js .= "buscaValor( 'preencheInner' ,'".$pgOcul."' , '".$pgProc."','oculto' , '".Sessao::getId()."' );";
    SistemaLegado::executaFramePrincipal($js);
}

$obHdnNumCampos = new Hidden;
$obHdnNumCampos->setName ( "inNumCampos" );
$obHdnNumCampos->setId   ( "inNumCampos" );
$obHdnNumCampos->setValue( $inNumeroColunas );

$arTxtPorcento = array();
$arTxtValor    = array();
$vlPeriodo     = isset($vlPeriodo) ? $vlPeriodo : 0;

for ($inCountComponente = 1 ; $inCountComponente <= $inNumeroColunas ; $inCountComponente++) {
    $obTxtPorcento = new Porcentagem();
    $obTxtPorcento->setName ( 'vlPorcentagem_' . ($inCountComponente) );
    $obTxtPorcento->setId   ( 'vlPorcentagem_' . ($inCountComponente) );
    $obTxtPorcento->setValue( '' );
    $obTxtPorcento->obEvento->setOnChange( "montaParametrosGET('mudaValor','','sincrono');" );

    $arTxtPorcento[$inCountComponente] = $obTxtPorcento;

    $obTxtValor = new Moeda();
    $obTxtValor->setName ( 'vlValor_' . ($inCountComponente) );
    $obTxtValor->setId   ( 'vlValor_' . ($inCountComponente) );
    $obTxtValor->setValue( number_format($vlPeriodo,2,',','.') );
    $obTxtValor->setSize      ( 13 );
    $obTxtValor->setMaxLength ( 21 );
    $obTxtValor->setNegativo  ( false);
    $obTxtValor->obEvento->setOnChange( "montaParametrosGET('mudaPorcentagem','','sincrono');" );

    $arTxtValor[$inCountComponente] = $obTxtValor;
}

$obTxtTotalPorcento = new Porcentagem();
$obTxtTotalPorcento->setName( "TotalPorcento" );
$obTxtTotalPorcento->setId  ( "TotalPorcento" );
$obTxtTotalPorcento->setReadOnly( true );
$obTxtTotalPorcento->setValue ( '0,00' );
$obTxtTotalPorcento->montaHTML();

$obTxtTotalValor = new Moeda();
$obTxtTotalValor->setName( "TotalValor" );
$obTxtTotalValor->setId  ( "TotalValor" );
$obTxtTotalValor->setReadOnly( true );
$obTxtTotalValor->setValue ( '0,00' );
$obTxtTotalValor->setSize     ( 28 );
$obTxtTotalValor->setMaxLength( 21 );
$obTxtTotalValor->setDecimais ( 2 );
$obTxtTotalValor->montaHTML();

$arLista = array();

$arLista[0]['titulo'] = 'Porcentagem';
$arLista[1]['titulo'] = 'Valor';

$arLista[0]['total'] = $obTxtTotalPorcento->getHtml();
$arLista[1]['total'] = $obTxtTotalValor->getHtml();

for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    $arTxtPorcento[$i]->montaHTML();
    $arLista[0]['campo_' . $i] = $arTxtPorcento[$i]->getHTML();
}
for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
    $arTxtValor[$i]->montaHTML();
    $arLista[1]['campo_' . $i] = $arTxtValor[$i]->getHTML();
}

$stFiltro = isset($stFiltro) ? $stFiltro : null;
$stOrder = isset($stOrder) ? $stOrder : null;
//inclusao do valor arrecadado por periodo
if ($stAcao == 'alterar') {
    //Recupera o valor arrecadado para a receita, dividindo por mes
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );

    $obTOrcamentoReceita = new TOrcamentoReceita();
    $obTOrcamentoReceita->setDado('exercicio',Sessao::getExercicio());
    $obTOrcamentoReceita->setDado('cod_entidade',$inCodEntidade);
    $obTOrcamentoReceita->setDado('cod_estrutural',$_GET['stMascClassReceita']);

    $arLista[2]['titulo'] = 'Arrecadado';

    $inVlTotal = isset($inVlTotal) ? $inVlTotal : 0;

    for ($i = 1; $i <= $inNumeroColunas ; $i ++) {
        $obTOrcamentoReceita->setDado('dt_inicial',date('d/m/Y',mktime(0,0,0,($i*(12/$inNumeroColunas)-((12/$inNumeroColunas)-1)),01,Sessao::getExercicio())));
        $obTOrcamentoReceita->setDado('dt_final',date('d/m/Y',mktime(0,0,0,$i*(12/$inNumeroColunas)+1,0,Sessao::getExercicio())));

        $obTOrcamentoReceita->recuperaLancamentosReceita( $rsRecordSet, $stFiltro, $stOrder );

        $inVlTotal += $rsRecordSet->getCampo('vl_periodo');
        $arLista[2]['campo_'.$i] = number_format((float) $rsRecordSet->getCampo('vl_periodo') ,'2',',','.');
   }

   $arLista[2]['total'] = number_format($inVlTotal,'2',',','.');

}

$rsLista = new RecordSet();
$rsLista->preenche ( $arLista );

$obLista = new Lista();
$obLista->setRecordSet( $rsLista );
$obLista->setNumeracao( false );
$obLista->setMostraPaginacao( false );
$obLista->setTitulo ( "Registros de Metas de Arrecadação de Receita" );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Receita" );
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
    $obLista->commitDado();
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
$obFormulario->addHidden( $obHdnDescRecurso         );
$obFormulario->addHidden( $obHdnCodReceita          );
$obFormulario->addHidden( $obHdnCodEstrutural       );
$obFormulario->addHidden( $obHdnNumCampos           );
$obFormulario->addHidden( $obHdnCodContaCreditoTributario );

$obFormulario->addTitulo( "Dados para Receita"      );
if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblReceita     );
    if ($boArrecadado) {
        $obFormulario->addComponente( $obILabelEntidade );
        $obFormulario->addHidden    ( $obHdnCodEntidade );
        $obFormulario->addComponente( $obILabelReceitaRecurso );
        $obFormulario->addHidden    ( $obHdnCodReceita  );
    } else {
    $obFormulario->addComponente( $obIEntidade        );
    $obFormulario->addComponente( $obBscRubricaReceita  );
    }

} else {
    $obFormulario->addComponente( $obIEntidade        );
    $obFormulario->addComponente( $obBscRubricaReceita  );
}
    $obIMontaRecursoDestinacao->geraFormulario( $obFormulario );

if ($stAcao == 'alterar') {
    if ($boArrecadado) {
        $obFormulario->addComponente( $obLblValorDotacao  );
        $obFormulario->addHidden    ( $obHdnValorDotacao  );
    } else {
        $obFormulario->addComponente( $obTxtValorDotacao  );
    }
} else {
    $obFormulario->addComponente( $obTxtValorDotacao  );
}
$obFormulario->addComponente($obRdnCreditoTributario);
$obFormulario->addSpan($obSpnContaCreditoTributario);
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

if ($boCreditoTributario == "S") {
    $jsOnload .= "montaParametrosGET('montaContaCreditoTributario','','sincrono');";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>