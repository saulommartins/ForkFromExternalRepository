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

$Revision: 3347 $
$Name$
$Author: pablo $
$Date: 2005-12-05 11:05:04 -0200 (Seg, 05 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stPrograma = "PopupCondicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once($pgJs);

$rsVariavel  = new RecordSet;
$obRegra     = new RFuncao;

$parametrosTipo = Sessao::read('ParametrosTipo');
$arVariaveis    = Sessao::read('VariaveisTipo');

for ($inCount=0; $inCount<count($parametrosTipo); $inCount++) {
    $arVariaveisTmp['stNomeVariavel'] = $parametrosTipo[$inCount]['stNomeParametro'];
    $arVariaveisTmp['stTipoVariavel'] = $parametrosTipo[$inCount]['stTipoParametro'];
    $arVariaveis[] = $arVariaveisTmp;
}
$rsVariavel->preenche( $arVariaveis );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty($stAcao)||$stAcao=="incluir" ) {
    $stAcao = "incluir";
    Sessao::remove('Atribuicao');
    Sessao::remove('Condicao');

} elseif ($stAcao) {
    $arPosicao  = explode("-",$_REQUEST['stPosicao']); //Primeira posição: Indice numérico - Segunda posição: Nível
    $arFuncao = Sessao::read('Funcao');
    $stConteudo = $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'];

    $stHtml = substr(ltrim($stConteudo),2, strlen(rtrim($stConteudo))-8 );
    Sessao::write('Condicao',explode(" ",$stHtml));
    $js  = "d.getElementById('idCondicao').innerHTML = '".$stHtml."';";
    $js .= "d.getElementById('hdnCondicao').value = '".$stHtml."';";
    SistemaLegado::executaIFrameOculto($js);

    for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
        if (substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,5)=='SENAO') {
            $_POST['rdbCondicao']=3;
        }
        if (substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,5)=='FIMSE') {
            break;
        }
    }

}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnPosicao = new Hidden;
$obHdnPosicao->setName( "stPosicao" );
$obHdnPosicao->setValue( $_REQUEST['stPosicao'] );

$obHdnCondicao = new Hidden;
$obHdnCondicao->setName ("hdnCondicao");
$obHdnCondicao->setId   ("hdnCondicao");

$obRdbCondicao = new Hidden;
$obRdbCondicao->setName( "rdbCondicao" );
$obRdbCondicao->setValue( $_POST['rdbCondicao'] );

$obTxtValor = new TextBox;
$obTxtValor->setRotulo        ( "Valor / Variável" );
$obTxtValor->setName          ( "stValor" );
$obTxtValor->setValue         ( $stValor  );
$obTxtValor->setSize          ( 30 );
$obTxtValor->setMaxLength     ( 200 );
$obTxtValor->setNull          ( false );
$obTxtValor->obEvento->setOnChange("document.frm.stVariavel.options[0].selected=true;");

$obCmbVariavel = new Select;
$obCmbVariavel->setRotulo        ( "Valor / Variável" );
$obCmbVariavel->setName          ( "stVariavel" );
$obCmbVariavel->setStyle         ( "width: 200px");
$obCmbVariavel->setCampoID       ( "-[stNomeVariavel]" );
$obCmbVariavel->setCampoDesc     ( "#[stNomeVariavel]" );
$obCmbVariavel->addOption        ( "", "Selecione" );
$obCmbVariavel->addOption        ( "VERDADEIRO", "VERDADEIRO" );
$obCmbVariavel->addOption        ( "FALSO"     , "FALSO" );
$obCmbVariavel->addOption        ( "NULO"      , "NULO" );
$obCmbVariavel->addOption        ( "VAZIO"     , "VAZIO" );
$obCmbVariavel->setValue         ( $stVariavel );
$obCmbVariavel->setNull          ( false );
$obCmbVariavel->preencheCombo    ( $rsVariavel );
$obCmbVariavel->obEvento->setOnChange("document.frm.stValor.value='';");

$obBtnAdicionar = new Button;
$obBtnAdicionar->setName ( "btnAdicionar" );
$obBtnAdicionar->setValue( "Adicionar" );
$obBtnAdicionar->obEvento->setOnClick ( "return AdicionaValorVariavel('MontaCondicao');" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "LimpaValorVariavel();" );

$obBtnParDir = new Button;
$obBtnParDir->setName( "btnParDir" );
$obBtnParDir->setValue( "(" );
$obBtnParDir->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnParEsq = new Button;
$obBtnParEsq->setName( "btnParEsq" );
$obBtnParEsq->setValue( ")" );
$obBtnParEsq->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnE = new Button;
$obBtnE->setName( "btnE" );
$obBtnE->setValue( "E" );
$obBtnE->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnOU = new Button;
$obBtnOU->setName( "btnOU" );
$obBtnOU->setValue( "OU" );
$obBtnOU->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnIgual = new Button;
$obBtnIgual->setName( "btnIgual" );
$obBtnIgual->setValue( "=" );
$obBtnIgual->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnDiferente = new Button;
$obBtnDiferente->setName( "btnDiferente" );
$obBtnDiferente->setValue( "!=" );
$obBtnDiferente->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnMaior = new Button;
$obBtnMaior->setName( "btnMaior" );
$obBtnMaior->setValue( ">" );
$obBtnMaior->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnMaiorIgual = new Button;
$obBtnMaiorIgual->setName( "btnMaiorIgual" );
$obBtnMaiorIgual->setValue( ">=" );
$obBtnMaiorIgual->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnMenor = new Button;
$obBtnMenor->setName( "btnMenor" );
$obBtnMenor->setValue( "<" );
$obBtnMenor->obEvento->setOnClick ( "Adiciona(this.value);" );

$obBtnMenorIgual = new Button;
$obBtnMenorIgual->setName( "btnMenorIgual" );
$obBtnMenorIgual->setValue( "<=" );
$obBtnMenorIgual->obEvento->setOnClick ( "Adiciona(this.value);" );

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
$obBtnCancelar->obEvento->setOnClick ( "window.close();" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnCondicao );
$obFormulario->addHidden            ( $obHdnPosicao );
$obFormulario->addHidden            ( $obRdbCondicao );

$obFormulario->addTitulo            ( "Dados para condição" );
$obFormulario->agrupaComponentes    ( array( $obTxtValor, $obCmbVariavel ) );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldcenter" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnAdicionar );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnLimpar    );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldcenter" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnParDir     );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnParEsq     );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnE          );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnOU         );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnIgual      );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnDiferente  );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnMaior      );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnMaiorIgual );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnMenor      );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnMenorIgual );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnDEL        );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

$obFormulario->addTitulo            ( "Dados para condição" );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan(2);

$obTabela = new Tabela;
$obTabela->setWidth( 100 );
$obTabela->addLinha();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "label" );
$obTabela->ultimaLinha->ultimaCelula->setWidth ( 20 );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "SE&nbsp;" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "fakefield" );
$obTabela->ultimaLinha->ultimaCelula->setWidth ( 35 );
$obTabela->ultimaLinha->ultimaCelula->setId( "idCondicao" );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "labelleft" );
$obTabela->ultimaLinha->ultimaCelula->setWidth ( 20 );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;ENTAO" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->commitLinha();

if ($_POST['rdbCondicao']==3) {
    //Caso na tela de filtro seja selecionada esta opção
    $obTabela->addLinha();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass ( "label" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( "SENAO" );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass ( "label" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass ( "labelleft" );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->commitLinha();
}
$obTabela->addLinha();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "label" );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "FIMSE" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "label" );
$obTabela->ultimaLinha->ultimaCelula->addConteudo( "&nbsp;" );
$obTabela->ultimaLinha->commitCelula();
$obTabela->ultimaLinha->addCelula();
$obTabela->ultimaLinha->ultimaCelula->setClass ( "labelleft" );
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
$obFormulario->obIFrame->setHeight("50");

$obFormulario->show                 ();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
//$obIFrame->setSrc("../../../includes/mensagem.php?".Sessao::getId());
$obIFrame->setHeight("50");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
