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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 4275 $
$Name$
$Author: cassiano $
$Date: 2005-12-23 11:25:24 -0200 (Sex, 23 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stPrograma = "PopupAtribuicaoFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once($pgJs);

$rsFuncao = $rsVariavel     = new RecordSet;
$obRegra                    = new RFuncao;
$arVariaveis                = array();

//Indentifica a tipagem da variável inicialmente informada.
$arVariaveisTipo = Sessao::read('arVariaveisTipo');
foreach ($arVariaveisTipo as $campo => $valor) {
    if ( $arVariaveisTipo[$campo]['stNomeVariavel'] == str_replace("-","",$_REQUEST['stVariavelInicial']) ) {
        $stTipoVariavel = $arVariaveisTipo[$campo]['stTipoVariavel'];
    }
}

//Recupera as funções de mesmo tipo da variável informada
$obRegra->obRTipoPrimitivo->setNomeTipo( $stTipoVariavel );
$obRegra->listar( $rsFuncao );

//Armazena as funções presentes na atribuição, pois estavam dando problema na verificação.
#sessao->transf3['Funcoes'] = array();
Sessao::write('arFuncoes',array());
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty($stAcao)||$stAcao=="incluir" ) {
    $stAcao = "incluir";

} elseif ($stAcao) {
    $arPosicao  = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
    $arFuncao = Sessao::read('Funcao');
    $stConteudo = $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'];

    if ( substr($stConteudo, 0, 6) == 'SELECT') {
        $stVarInicial = substr($stConteudo, strpos($stConteudo, '#')+1, strlen($stConteudo));
        $stVarInicial = '-'.substr($stVarInicial, 0, strpos($stVarInicial, ' '));
        $_REQUEST['stVariavelInicial'] = $stVarInicial;

        $stFuncao     = substr($stConteudo, strpos($stConteudo, '"')+1, strlen($stConteudo));
        $stParametros = substr($stFuncao, strpos($stFuncao, '[')+1, strlen($stFuncao));
        $stParametros = substr($stParametros, 0, strpos($stParametros, ']'));
        $stFuncao     = substr($stFuncao, 0, strpos($stFuncao, '"'));

        $stParametros = str_replace('::varchar', '', $stParametros);
        $stParametros = str_replace(',"INTEIRO"','', $stParametros);
        $stParametros = str_replace(',"TEXTO"','', $stParametros);
        $stParametros = str_replace(',"BOOLEANO"','', $stParametros);
        $stParametros = str_replace(',"NUMERICO"','', $stParametros);
        $stParametros = str_replace(',"DATA"','', $stParametros);

    } else {
        $stFuncao       = substr($stConteudo, strpos($stConteudo,'<-')+2, (strlen($stConteudo)-(strpos($stConteudo,'<-')+2) ) );
        $stFuncao       = substr($stFuncao, 0, strpos($stFuncao,'(') );
        $stFuncaoTxt    = $stFuncao = trim($stFuncao);
        $stParametros   = substr($stConteudo    , strpos($stConteudo,'(')+1, strlen($stConteudo) );
        $stParametros   = substr($stParametros  , 0, strrpos($stParametros,');') );

    }
    $arPar          = explode(',',$stParametros);

    for ($inCount=0; $inCount<count($arPar); $inCount++) {
        $arParametros[$inCount] = str_replace('\"','X,X,X,X',trim($arPar[$inCount]) );
        $arParametros[$inCount] = str_replace('"','',$arParametros[$inCount] );
        $arParametros[$inCount] = str_replace('X,X,X,X','\"',$arParametros[$inCount] );
    }

    #sessao->transf3['arParametros'] = $arParametros;
    Sessao::write('arParametros',$arParametros);
    SistemaLegado::executaIFrameOculto("buscaDado('MontaParametrosFuncao');");
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obLblVariavel = new Label;
$obLblVariavel->setRotulo("Variável");
$obLblVariavel->setValue (str_replace("-","#",$_REQUEST['stVariavelInicial']));

$obTxtFuncao = new TextBox;
$obTxtFuncao->setRotulo        ( "Função" );
$obTxtFuncao->setName          ( "stFuncaoTxt" );
$obTxtFuncao->setValue         ( $stFuncaoTxt  );
$obTxtFuncao->setSize          ( 38 );
$obTxtFuncao->setMaxLength     ( 150 );
$obTxtFuncao->setNull          ( false );
$obTxtFuncao->obEvento->setOnChange("PreencheComboFuncao(this);buscaDado('MontaParametrosFuncao');");

$obCmbFuncao = new Select;
$obCmbFuncao->setRotulo        ( "Função" );
$obCmbFuncao->setName          ( "stFuncao" );
$obCmbFuncao->setStyle         ( "width: 280px");
$obCmbFuncao->setCampoID       ( "nom_funcao" );
$obCmbFuncao->setCampoDesc     ( "nom_funcao" );
$obCmbFuncao->addOption        ( "", "Selecione" );
$obCmbFuncao->setValue         ( $stFuncao   );
$obCmbFuncao->setNull          ( false );
$obCmbFuncao->preencheCombo    ( $rsFuncao );
$obCmbFuncao->obEvento->setOnChange("document.frm.stFuncaoTxt.value=document.frm.stFuncao.value;buscaDado('MontaParametrosFuncao');");

$obSpnFuncao = new Span;
$obSpnFuncao->setId ( "spnFuncao" );

$obHdnFuncaoEval = new Hidden;
$obHdnFuncaoEval->setName( "stFuncaoEval" );
$obHdnFuncaoEval->setValue( "" );

$obBtnAdicionarFuncao = new Button;
$obBtnAdicionarFuncao->setName ( "btnAdicionarFuncao" );
$obBtnAdicionarFuncao->setValue( "Adicionar" );
$obBtnAdicionarFuncao->obEvento->setOnClick ( "return AdicionaFuncao('MontaFuncao');" );

$obBtnOk = new Button;
$obBtnOk->setName( "btnOk" );
$obBtnOk->setValue( "Ok" );
$obBtnOk->obEvento->setOnClick ( "Ok();" );

$obBtnCancelar = new Button;
$obBtnCancelar->setName( "btnCancelar" );
$obBtnCancelar->setValue( "Cancelar" );
$obBtnCancelar->obEvento->setOnClick ( "buscaDado('Fechar');" );

//$obBtnTrataErros = new Button;
//$obBtnTrataErros->setName("btnTrataErros");
//$obBtnTrataErros->setValue("Tratar Erros");
//$obBtnTrataErros->obEvento->setOnClick("buscaDado('TratarErros');");

$obHdnVariavelInicial = new Hidden;
$obHdnVariavelInicial->setName ("stVariavelInicial");
$obHdnVariavelInicial->setValue( str_replace("-","#",$_REQUEST['stVariavelInicial']) );

$obHdnTipoVariavel = new Hidden;
$obHdnTipoVariavel->setName ( "stTipoVariavel" );
$obHdnTipoVariavel->setValue( $stTipoVariavel );

$obHdnCondicao = new Hidden;
$obHdnCondicao->setName ("hdnCondicao");
$obHdnCondicao->setId   ("hdnCondicao");

$obHdnPosicao = new Hidden;
$obHdnPosicao->setName( "stPosicao" );
$obHdnPosicao->setValue( $_REQUEST['stPosicao'] );

$obHdnValidaParametros = new Hidden;
$obHdnValidaParametros->setName ( "stValidaParametros" );
$obHdnValidaParametros->setValue( "" );

$obHdnParametros = new Hidden;

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                   );
$obFormulario->addHidden            ( $obHdnAcao                );
$obFormulario->addHidden            ( $obHdnCtrl                );
$obFormulario->addHidden            ( $obHdnCondicao            );
$obFormulario->addHidden            ( $obHdnTipoVariavel        );
$obFormulario->addHidden            ( $obHdnVariavelInicial     );
$obFormulario->addHidden            ( $obHdnPosicao             );
$obFormulario->addHidden            ( $obHdnFuncaoEval          );
$obFormulario->addHidden            ( $obHdnValidaParametros    );
for ($inCount=0; $inCount<count($arParametros); $inCount++) {
    $obHdnParametros->setName ( "stParametro_".$stFuncao."_" . ($inCount+1) );
    $obHdnParametros->setValue( $arParametros[$inCount] );
    $obFormulario->addHidden  ( $obHdnParametros );
}

$obFormulario->addTitulo            ( "Dados para atribuição de função" );
$obFormulario->addComponente        ( $obLblVariavel );
$obFormulario->agrupaComponentes    ( array( $obTxtFuncao, $obCmbFuncao ) );
$obFormulario->addSpan              ( $obSpnFuncao );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "field" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnOk       );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnCancelar );
//if ($stAcao != 'incluir') {
//    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnTrataErros );
//}
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->addIFrameOculto("oculto");
$obFormulario->obIFrame->setWidth("100%");
$obFormulario->obIFrame->setHeight("0");
$obFormulario->show                 ();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
//$obIFrame->setSrc("../../../includes/mensagem.php?".Sessao::getId());
$obIFrame->setHeight("50");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
