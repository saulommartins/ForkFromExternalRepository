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

$Revision: 5624 $
$Name$
$Author: lizandro $
$Date: 2006-01-26 17:48:55 -0200 (Qui, 26 Jan 2006) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stPrograma = "PopupAtribuicaoSimples";
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

//Armazena as funções presentes na atribuição, pois estavam dando problema na verificação.

$variaveisTipo = Sessao::read('VariaveisTipo');

$parametrosTipo = Sessao::read('ParametrosTipo');

Sessao::remove('Condicao');
//Indentifica a tipagem da variável inicialmente informada.
foreach ($variaveisTipo as $campo => $valor) {
    if ( $variaveisTipo[$campo]['stNomeVariavel'] == str_replace("-","",$_REQUEST['stVariavelInicial']) ) {
        $stTipoVariavel = $variaveisTipo[$campo]['stTipoVariavel'];
    }
}

//Variaveis
for ($inCount=0; $inCount<count($variaveisTipo); $inCount++) {
    switch ($stTipoVariavel) {
        case "INTEIRO":
        case "BOOLEANO":
            if ($variaveisTipo[$inCount]['stTipoVariavel']==$stTipoVariavel) {
                $arVariaveis[] = $variaveisTipo[$inCount];
            }
        break;
        case "NUMERICO":
            if ($variaveisTipo[$inCount]['stTipoVariavel']=="INTEIRO" || $variaveisTipo[$inCount]['stTipoVariavel']=="NUMERICO") {
                $arVariaveis[] = $variaveisTipo[$inCount];
            }
        break;
        case "DATA":
            if ($variaveisTipo[$inCount]['stTipoVariavel']=="DATA") {
                $arVariaveis[] = $variaveisTipo[$inCount];
            }
        break;
        default:
            $arVariaveis[] = $variaveisTipo[$inCount];
        break;
    }
}
//Parametros
for ($inCount=0; $inCount<count($parametrosTipo); $inCount++) {
    $arTmp['stTipoVariavel'] = $parametrosTipo[$inCount]['stTipoParametro'];
    $arTmp['stNomeVariavel'] = $parametrosTipo[$inCount]['stNomeParametro'];
    $arVariaveis[] = $arTmp;
    //switch ($stTipoVariavel) {
    //    case "INTEIRO":
    //    case "BOOLEANO":
    //        if ($arTmp['stTipoVariavel']==$stTipoVariavel) {
    //            $arVariaveis[] = $arTmp;
    //        }
    //    break;
    //    case "NUMERICO":
    //        if ($arTmp['stTipoVariavel']=="INTEIRO" || $arTmp['stTipoVariavel']=="NUMERICO") {
    //            $arVariaveis[] = $arTmp;
    //        }
    //    break;
    //    default:
    //        $arVariaveis[] = $arTmp;
    //    break;
    //}
}

$rsVariavel->preenche( $arVariaveis );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty($stAcao)||$stAcao=="incluir" ) {
    $stAcao = "incluir";

} elseif ($stAcao) {

    $arPosicao  = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
    $arFuncao = Sessao::read('Funcao');
    $stConteudo = $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'];

    if (strstr($stConteudo,'"')) {
        $stHtml = substr($stConteudo,strpos($stConteudo,'<-')+4, strlen($stConteudo)-(strpos($stConteudo,'<-')+6) );
    } else
        $stHtml = substr($stConteudo,strpos($stConteudo,'<-')+3, strlen($stConteudo)-(strpos($stConteudo,'<-')+4) );

    $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
    $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";

    $stCondicaoSessao = explode("&nbsp;",$stHtml);

    Sessao::write('Condicao',$stCondicaoSessao);

    SistemaLegado::executaIFrameOculto($js);
}
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

if ($stTipoVariavel=="DATA") {
    $obTxtValor = new Data;
} else {
$obTxtValor = new TextBox;
}
$obTxtValor->setRotulo        ( "Valor / Variável" );
$obTxtValor->setName          ( "stValor" );
$obTxtValor->setValue         ( $stValor  );
$obTxtValor->setSize          ( 30 );
$obTxtValor->setMaxLength     ( 200 );
$obTxtValor->setNull          ( false );
$obTxtValor->obEvento->setOnChange("document.frm.stVariavel.options[0].selected=true;");
if($stTipoVariavel=="INTEIRO")
    $obTxtValor->setInteiro   ( true );
elseif ($stTipoVariavel=="NUMERICO") {
    $obTxtValor->setMaxLength(14);
    $obTxtValor->obEvento->setOnKeyPress("return tfloatPonto(this, event);");
}

$obCmbVariavel = new Select;
$obCmbVariavel->setRotulo        ( "Valor / Variável" );
$obCmbVariavel->setName          ( "stVariavel" );
$obCmbVariavel->setStyle         ( "width: 200px");
$obCmbVariavel->setCampoID       ( "-[stNomeVariavel]" );
$obCmbVariavel->setCampoDesc     ( "#[stNomeVariavel]" );
$obCmbVariavel->addOption        ( "", "Selecione" );
if ($stTipoVariavel=="BOOLEANO") {
    $obCmbVariavel->addOption        ( "VERDADEIRO", "VERDADEIRO" );
    $obCmbVariavel->addOption        ( "FALSO"     , "FALSO" );
}
if ($stTipoVariavel=="TEXTO") {
    $obCmbVariavel->addOption        ( "VAZIO"        , "VAZIO" );
}
$obCmbVariavel->setValue         ( $stVariavel );
$obCmbVariavel->setNull          ( false );
$obCmbVariavel->preencheCombo    ( $rsVariavel );
$obCmbVariavel->obEvento->setOnChange("document.frm.stValor.value='';");

$obBtnAdicionarValorVariavel = new Button;
$obBtnAdicionarValorVariavel->setName ( "btnAdicionarValorVariavel" );
$obBtnAdicionarValorVariavel->setValue( "Adicionar" );
$obBtnAdicionarValorVariavel->obEvento->setOnClick ( "return AdicionaValorVariavel('MontaCondicao');" );

$obBtnLimparValorVariavel = new Button;
$obBtnLimparValorVariavel->setName( "btnLimparValorVariavel" );
$obBtnLimparValorVariavel->setValue( "Limpar" );
$obBtnLimparValorVariavel->obEvento->setOnClick ( "LimpaValorVariavel();" );

$obBtnParDir = new Button;
$obBtnParDir->setName( "btnParDir" );
$obBtnParDir->setValue( "(" );
$obBtnParDir->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnParEsq = new Button;
$obBtnParEsq->setName( "btnParEsq" );
$obBtnParEsq->setValue( ")" );
$obBtnParEsq->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnSoma = new Button;
$obBtnSoma->setName( "btnSoma" );
$obBtnSoma->setValue( "+" );
$obBtnSoma->obEvento->setOnClick ( "Adiciona('Soma');" );

$obBtnSubtracao = new Button;
$obBtnSubtracao->setName( "btnSubtracao" );
$obBtnSubtracao->setValue( "-" );
$obBtnSubtracao->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnMultiplicacao = new Button;
$obBtnMultiplicacao->setName( "btnMultiplicacao" );
$obBtnMultiplicacao->setValue( "*" );
$obBtnMultiplicacao->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnDivisao = new Button;
$obBtnDivisao->setName( "btnDivisao" );
$obBtnDivisao->setValue( "/" );
$obBtnDivisao->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnExponenciacao = new Button;
$obBtnExponenciacao->setName( "btnExponenciacao" );
$obBtnExponenciacao->setValue( "^" );
$obBtnExponenciacao->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnDEL = new Button;
$obBtnDEL->setName( "btnDEL" );
$obBtnDEL->setValue( "DEL" );
$obBtnDEL->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnOk = new Button;
$obBtnOk->setName( "btnOk" );
$obBtnOk->setValue( "Ok" );
$obBtnOk->obEvento->setOnClick ( "Ok();" );

$obBtnCancelar = new Button;
$obBtnCancelar->setName( "btnCancelar" );
$obBtnCancelar->setValue( "Cancelar" );
$obBtnCancelar->obEvento->setOnClick ( "buscaDado('Fechar');" );

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

$obFormulario->addTitulo            ( "Dados para atribuição" );
if($stTipoVariavel=="BOOLEANO")
    $obFormulario->addComponente        ( $obCmbVariavel );
else
    $obFormulario->agrupaComponentes    ( array( $obTxtValor, $obCmbVariavel ) );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldcenter" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnAdicionarValorVariavel );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnLimparValorVariavel    );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->addTitulo            ( "Dados para atribuição" );
$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldcenter" );
if ($stTipoVariavel!="DATA") {
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnParDir        );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnParEsq        );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnSoma          );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnSubtracao     );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnMultiplicacao );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnDivisao       );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnExponenciacao );
}
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnDEL           );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan(2);

$obTabela = new Tabela;
$obTabela->setWidth( 100 );
$obTabela->addLinha();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "label" );
$obTabela->ultimaLinha->ultimaCelula->setWidth ( 10 );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( $obHdnVariavelInicial->getValue() );
$obTabela->ultimaLinha->commitCelula();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "labelcenter" );
$obTabela->ultimaLinha->ultimaCelula->setWidth ( 5 );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;<-&nbsp;" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "fakefield" );
$obTabela->ultimaLinha->ultimaCelula->setWidth ( 35 );
$obTabela->ultimaLinha->ultimaCelula->setId( "idCondicao" );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->commitLinha();

$obFormulario->ultimaLinha->ultimaCelula->addTabela( $obTabela );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "field" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnOk       );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnCancelar );
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
