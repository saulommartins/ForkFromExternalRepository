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

    $Id: FMManterFuncao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RBiblioteca.class.php");

$stPrograma = "ManterFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once($pgJs);

$rsTipo  = new RecordSet;
$obRegra = new RFuncao;
$obRBiblioteca = new RBiblioteca( new RModulo );

$obRegra->obRTipoPrimitivo->listar( $rsTipo );

//Variável de sessão utilizada no frame oculto, inicializada nesta página
Sessao::write('ParametrosTipo', array());
Sessao::write('VariaveisTipo',array());
Sessao::write('Condicao',array());

$arFuncao = array();

$arFuncao['Nome'] = $arFuncao['Retorno'] = $arFuncao['RetornoVar']	= "";
$arFuncao['Parametro'] = $arFuncao['Variavel'] = $arFuncao['Corpo'] = array();

Sessao::write('Funcao',$arFuncao);

$inCodFuncao  = $_REQUEST['inCodFuncao'];
$stNomeFuncao = $_REQUEST['stNomeFuncao'];

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_REQUEST["stAcao"];
if ( empty($stAcao)||$stAcao=="incluir" ) {
    $stAcao = "incluir";
    $obRBiblioteca->listarBibliotecasPorResponsavel( $rsBiblioteca );
} elseif ($stAcao) {
    $rsBiblioteca = new RecordSet;
    $obRegra->obRBiblioteca->roRModulo->setCodModulo( $_REQUEST['inCodModulo'] );
    $obRegra->obRBiblioteca->setCodigoBiblioteca( $_REQUEST['inCodBiblioteca'] );
    $obRegra->obRBiblioteca->consultarBiblioteca( $rsBiblioteca );
    $obRegra->setCodFuncao( $_REQUEST['inCodFuncao'] );
    $obRegra->consultar();

    $stRetornoTxt = $obRegra->obRTipoPrimitivo->getNomeTipo();
    $stRetorno = $obRegra->obRTipoPrimitivo->getNomeTipo();

    $stNomeFuncao = $obRegra->getNomeFuncao();
    $stComentario = $obRegra->getComentario();

    $arFuncao['Nome']      = $stNomeFuncao;
    $arFuncao['Retorno']   = $stRetorno;
    $obRegra->obRVariavel->setCodModulo    ( $_REQUEST['inCodModulo'] );
    $obRegra->obRVariavel->setCodBiblioteca( $_REQUEST['inCodBiblioteca'] );
    $obRegra->obRVariavel->setCodFuncao( $obRegra->getCodFuncao() );
    $obRegra->obRVariavel->listar( $rsVariavel );
    $inCount = 0;

    $variaveisTipoTemp = Sessao::read('VariaveisTipo');

    while ( !$rsVariavel->eof() ) {
        $arVariavel['inId']            = $inCount++;
        $arVariavel['stNomeVariavel']  = $rsVariavel->getCampo("nom_variavel");
        $arVariavel['stTipoVariavel']  = $rsVariavel->getCampo("nom_tipo");
        $arVariavel['stValorVariavel'] = $rsVariavel->getCampo("valor_inicial");
        $stVariavel = $arVariavel['stNomeVariavel'].' '.$arVariavel['stTipoVariavel'];
        switch ($arVariavel['stTipoVariavel']) {
            case "TEXTO":
                $stVariavel .= ' <- ';
                $arVariavel['stValorVariavel'] = str_replace('"','',$arVariavel['stValorVariavel']);
                $stVariavel .= '"'.$arVariavel['stValorVariavel'].'"';
            break;
            case "DATA":
                $data = explode ( '-', $arVariavel['stValorVariavel'] );
                $ano = $data[0];
                $mes = $data[1];
                $dia = $data[2];

                if ($data[0] != '') {
                  $_POST['stValorVariavel'] = $ano."-".$mes."-".$dia;
                  $stVariavel .= ' <- "'.$_POST['stValorVariavel'].'"';
                }

            break;
            default:
                $stTmpVariavel = (string) $arVariavel['stValorVariavel'];
                if(strlen( $stTmpVariavel ) > 0 ) $stVariavel .= ' <- ';
                $stVariavel .= $arVariavel['stValorVariavel'];
                break;
        }
        $arFuncao['Variavel'][] = $stVariavel;
        $variaveisTipoTemp[] = $arVariavel;
        $rsVariavel->proximo();
    }

    $variaveisTipoTemp = Sessao::write('VariaveisTipo',$variaveisTipoTemp);

    $obRegra->obRVariavel->setParametro(true);
    $obRegra->obRVariavel->listar( $rsParametro );
    $inCount = 0;
    $ParametrosTipoTemp = Sessao::read('ParametrosTipo');

    while ( !$rsParametro->eof() ) {
        $arParametro['inId']             = $inCount++;
        $arParametro['stNomeParametro']  = $rsParametro->getCampo("nom_variavel");
        $arParametro['stTipoParametro']  = $rsParametro->getCampo("nom_tipo");

        $arFuncao['Parametro'][] = $arParametro['stNomeParametro'].':'.$arParametro['stTipoParametro'];

        $ParametrosTipoTemp[] = $arParametro;

        $rsParametro->proximo();
    }
    Sessao::write('ParametrosTipo',$ParametrosTipoTemp);

    $obRegra->listarCorpoFuncao( $rsCorpo );
    while ( !$rsCorpo->eof() ) {
        $arCorpo['Nivel']    = $rsCorpo->getCampo("nivel");
        $arCorpo['Conteudo'] = $rsCorpo->getCampo("linha");
        if ( substr($arCorpo['Conteudo'],0,7)=='RETORNA' ) {
            $arFuncao['RetornoVar'] = trim(substr($arCorpo['Conteudo'],7,strlen($arCorpo['Conteudo'])));
        } else {
            $arFuncao['Corpo'][] = $arCorpo;
        }
        $rsCorpo->proximo();
    }
    # escreve array da funcao de volta na sessao
    Sessao::write('Funcao',$arFuncao);
    $stJs = "goOculto('preencheInner');";
}

$stCorpoLN = $obRegra->montaCorpoFuncao();
$stCorpoPL = $obRegra->ln2pl();
SistemaLegado::executaFramePrincipal($stJs."d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Cria o hidden da pagina acessada
$obHdnPg = new Hidden;
$obHdnPg->setName ("pg");
$obHdnPg->setValue($_REQUEST['pg']);

//Cria o hidden da posicao acessada
$obHdnPos = new Hidden;
$obHdnPos->setName ("pos");
$obHdnPos->setValue($_REQUEST['pos']);

//ABA FUNÇÃO
    $obCmbBiblioteca = new Select;
    $obCmbBiblioteca->setRotulo        ( "Biblioteca" );
    $obCmbBiblioteca->setName          ( "stChaveBiblioteca" );
    $obCmbBiblioteca->setStyle         ( "width: 200px");
    $obCmbBiblioteca->setCampoID       ( "[cod_modulo]-[cod_biblioteca]" );
    $obCmbBiblioteca->setCampoDesc     ( "nom_biblioteca" );
    $obCmbBiblioteca->addOption        ( "", "Selecione" );
//    $obCmbBiblioteca->setValue         ( $stRetorno );
    $obCmbBiblioteca->setNull          ( false );
    $obCmbBiblioteca->preencheCombo    ( $rsBiblioteca );

    $obTxtNomeFuncao = new TextBox;
    $obTxtNomeFuncao->setRotulo        ( "Nome" );
    $obTxtNomeFuncao->setName          ( "stNomeFuncao" );
    $obTxtNomeFuncao->setValue         ( $stNomeFuncao  );
    $obTxtNomeFuncao->setSize          ( 60 );
    $obTxtNomeFuncao->setMaxLength     ( 60 );
    $obTxtNomeFuncao->setNull          ( false );
    $obTxtNomeFuncao->obEvento->setOnChange("goOculto('NomeFuncao');");
    $obTxtNomeFuncao->obEvento->setOnKeyPress("return validaNome(this, event);");

    $obHdnChaveBiblioteca = new Hidden;
    $obHdnChaveBiblioteca->setName( 'stChaveBiblioteca' );
    if ( $stAcao == 'incluir' )
        $obHdnChaveBiblioteca->setValue( $obRBiblioteca->roRModulo->getCodModulo().'-'.$obRBiblioteca->getCodigoBiblioteca() );
    else
        $obHdnChaveBiblioteca->setValue( $obRegra->obRBiblioteca->roRModulo->getCodModulo().'-'.$obRegra->obRBiblioteca->getCodigoBiblioteca() );

    $obLblNomeBiblioteca = new Label;
    $obLblNomeBiblioteca->setRotulo( 'Biblioteca' );
    if ( $stAcao == 'incluir' )
        $obLblNomeBiblioteca->setValue (  $obRBiblioteca->getNomeBiblioteca() );
    else
        $obLblNomeBiblioteca->setValue (  $obRegra->obRBiblioteca->getNomeBiblioteca() );

    $obHdnCodFuncao = new Hidden;
    $obHdnCodFuncao->setName ("inCodFuncao");
    $obHdnCodFuncao->setValue($inCodFuncao );

    $obLblNomeFuncao = new Label;
    $obLblNomeFuncao->setRotulo("Nome");
    $obLblNomeFuncao->setValue ($stNomeFuncao);

    $obTxtRetorno = new TextBox;
    $obTxtRetorno->setRotulo        ( "Retorno" );
    $obTxtRetorno->setName          ( "stRetornoTxt");
    $obTxtRetorno->setValue         ( $stRetornoTxt );
    $obTxtRetorno->setSize          ( 10 );
    $obTxtRetorno->setMaxLength     ( 10 );
    $obTxtRetorno->setToUpperCase   ( true );
    $obTxtRetorno->setNull          ( false );
    $obTxtRetorno->obEvento->setOnChange("goOculto('RetornoFuncao');");

    $obCmbRetorno = new Select;
    $obCmbRetorno->setRotulo        ( "Retorno" );
    $obCmbRetorno->setName          ( "stRetorno" );
    $obCmbRetorno->setStyle         ( "width: 200px");
    $obCmbRetorno->setCampoID       ( "nom_tipo" );
    $obCmbRetorno->setCampoDesc     ( "nom_tipo" );
    $obCmbRetorno->addOption        ( "", "Selecione" );
    $obCmbRetorno->setValue         ( $stRetorno );
    $obCmbRetorno->setNull          ( false );
    $obCmbRetorno->preencheCombo    ( $rsTipo );
    $obCmbRetorno->obEvento->setOnChange("goOculto('RetornoFuncao');");
    $rsTipo->setPrimeiroElemento();

    $obLblRetorno = new Label;
    $obLblRetorno->setRotulo("Retorno");
    $obLblRetorno->setValue ($stRetorno);

    $obTxtComentario = new TextArea;
    $obTxtComentario->setRotulo        ( "Comentário" );
    $obTxtComentario->setName          ( "stComentario");
    $obTxtComentario->setValue         ( $stComentario );
    $obTxtComentario->setCols          ( 150 );
    $obTxtComentario->setRows          ( 10 );

//ABA PARÃMETROS
    $obTxtNomeParametro = new TextBox;
    $obTxtNomeParametro->setRotulo        ( "*Nome " );
    $obTxtNomeParametro->setName          ( "stNomeParametro" );
    $obTxtNomeParametro->setValue         ( $stNomeParametro  );
    $obTxtNomeParametro->setSize          ( 60 );
    $obTxtNomeParametro->setMaxLength     ( 60 );
    $obTxtNomeParametro->obEvento->setOnKeyPress("return validaNome(this, event);");

    $obTxtTipoParametro = new TextBox;
    $obTxtTipoParametro->setRotulo        ( "*Tipo" );
    $obTxtTipoParametro->setName          ( "stTipoParametroTxt");
    $obTxtTipoParametro->setValue         ( $stTipoParametroTxt );
    $obTxtTipoParametro->setSize          ( 10 );
    $obTxtTipoParametro->setMaxLength     ( 10 );
    $obTxtTipoParametro->setToUpperCase   ( true );

    $obCmbTipoParametro = new Select;
    $obCmbTipoParametro->setRotulo        ( "*Tipo" );
    $obCmbTipoParametro->setName          ( "stTipoParametro" );
    $obCmbTipoParametro->setStyle         ( "width: 200px");
    $obCmbTipoParametro->setCampoID       ( "nom_tipo" );
    $obCmbTipoParametro->setCampoDesc     ( "nom_tipo" );
    $obCmbTipoParametro->addOption        ( "", "Selecione" );
    $obCmbTipoParametro->setValue         ( $stTipoParametro );
    $obCmbTipoParametro->preencheCombo    ( $rsTipo );

    $obBtnAdicionarParametro = new Button;
    $obBtnAdicionarParametro->setName ( "btnAdicionarParametro" );
    $obBtnAdicionarParametro->setValue( "Adicionar" );
    $obBtnAdicionarParametro->setTipo ( "button" );
    $obBtnAdicionarParametro->obEvento->setOnClick ( "return AdicionaParametro('MontaParametrosTipo');" );

    $obBtnLimparParametro = new Button;
    $obBtnLimparParametro->setName( "btnLimparParametro" );
    $obBtnLimparParametro->setValue( "Limpar" );
    $obBtnLimparParametro->setTipo( "button" );
    $obBtnLimparParametro->obEvento->setOnClick ( "limpaParametros();" );

    $obSpnListaParametro = new Span;
    $obSpnListaParametro->setId ( "spnListaParametros" );

//ABA VARIÁVEIS
    $obTxtNomeVariavel = new TextBox;
    $obTxtNomeVariavel->setRotulo        ( "*Nome  " );
    $obTxtNomeVariavel->setTitle         ( "Nome da Variável  ");
    $obTxtNomeVariavel->setName          ( "stNomeVariavel" );
    $obTxtNomeVariavel->setValue         ( $stNomeVariavel  );
    $obTxtNomeVariavel->setSize          ( 30 );
    $obTxtNomeVariavel->setMaxLength     ( 30 );
    $obTxtNomeVariavel->obEvento->setOnKeyPress("return validaNome(this, event);");

    $obTxtTipoVariavel = new TextBox;
    $obTxtTipoVariavel->setRotulo        ( "*Tipo  " );
    $obTxtTipoVariavel->setName          ( "stTipoVariavelTxt");
    $obTxtTipoVariavel->setValue         ( $stTipoVariavelTxt );
    $obTxtTipoVariavel->setSize          ( 10 );
    $obTxtTipoVariavel->setMaxLength     ( 10 );
    $obTxtTipoVariavel->setToUpperCase   ( true );
    $obTxtTipoVariavel->obEvento->setOnChange("goOculto('montaValorVariavel');");

    $rsTipo->setPrimeiroElemento();

    $obCmbTipoVariavel = new Select;
    $obCmbTipoVariavel->setRotulo        ( "*Tipo  " );
    $obCmbTipoVariavel->setName          ( "stTipoVariavel" );
    $obCmbTipoVariavel->setStyle         ( "width: 200px");
    $obCmbTipoVariavel->setCampoID       ( "nom_tipo" );
    $obCmbTipoVariavel->setCampoDesc     ( "nom_tipo" );
    $obCmbTipoVariavel->addOption        ( "", "Selecione" );
    $obCmbTipoVariavel->setValue         ( $stTipoVariavel );
    $obCmbTipoVariavel->preencheCombo    ( $rsTipo );
    $obCmbTipoVariavel->obEvento->setOnChange("goOculto('montaValorVariavel');");

    $obSpnValorVariavel = new Span;
    $obSpnValorVariavel->setId ( "spnValorVariavel" );

/*    $obTxtValorVariavel = new TextBox;
    $obTxtValorVariavel->setRotulo        ( "Valor  " );
    $obTxtValorVariavel->setName          ( "stValorVariavel" );
    $obTxtValorVariavel->setValue         ( $stValorVariavel  );
    $obTxtValorVariavel->setSize          ( 60 );*/

    $obBtnAdicionarVariavel = new Button;
    $obBtnAdicionarVariavel->setName ( "btnAdicionarVariavel" );
    $obBtnAdicionarVariavel->setValue( "Adicionar" );
    $obBtnAdicionarVariavel->obEvento->setOnClick ( "return AdicionaVariavel('MontaVariaveisTipo');" );

    $obBtnLimparVariavel = new Button;
    $obBtnLimparVariavel->setName( "btnLimparVariavel" );
    $obBtnLimparVariavel->setValue( "Limpar" );
    $obBtnLimparVariavel->obEvento->setOnClick ( "limpaParametros();" );

    $obSpnListaVariavel = new Span;
    $obSpnListaVariavel->setId ( "spnListaVariaveis" );

    //ABA CORPO - LINGUAGEM NATURAL
    $obLnkAtribuicao = new Link;
    $obLnkAtribuicao->setValue( "Atribuição" );
    $obLnkAtribuicao->setHref ( "JavaScript:AbrePopUp('FLPopupAtribuicao');" );

    $obLnkCondicao = new Link;
    $obLnkCondicao->setValue( "Condição" );
    $obLnkCondicao->setHref ( "JavaScript:AbrePopUp('FLPopupCondicao');" );

    $obLnkLaco = new Link;
    $obLnkLaco->setValue( "Laço" );
    $obLnkLaco->setHref ( "JavaScript:AbrePopUp('FMPopupLaco');" );

    $obLnkRetorno = new Link;
    $obLnkRetorno->setValue( "Retorno" );
    $obLnkRetorno->setHref ( "JavaScript:AbrePopUp('FMPopupRetorno');" );

    $obSpnCorpoLN = new Span;
    $obSpnCorpoLN->setId ( "spnCorpoLN" );

    $obSpnCorpoPL = new Span;
    $obSpnCorpoPL->setId ( "spnCorpoPL" );

    $obSpnMsgSalva = new Span;
    $obSpnMsgSalva->setId ( "spnMsgSalva" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obFormulario = new FormularioAbas;
$obFormularioCorpo = new FormularioAbas;

$obFormulario->addForm		( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnPg );
$obFormulario->addHidden    ( $obHdnPos );

$obFormulario->addAba    	("Assinatura");
$obFormulario->addTitulo    ( "Dados para função" );

if ($stAcao=="incluir") {
    $obFormulario->addComponente        ( $obCmbBiblioteca );
    $obFormulario->addComponente        ( $obTxtNomeFuncao );
    $obFormulario->addComponenteComposto( $obTxtRetorno , $obCmbRetorno );
} else {
    $obFormulario->addComponente        ( $obLblNomeBiblioteca );
    $obFormulario->addComponente        ( $obLblNomeFuncao );
    $obFormulario->addHidden            ( $obHdnCodFuncao  );
    $obFormulario->addHidden            ( $obHdnChaveBiblioteca );
    $obFormulario->addComponente        ( $obLblRetorno    );
}
$obFormulario->addComponente        ( $obTxtComentario );

$obFormulario->addAba("Parâmetros",true);

// Restringe apenas para inlusão
if ($stAcao=="incluir") {

    $obFormulario->addTitulo            ( "Dados para parâmetro" );

    $obFormulario->addComponente        ( $obTxtNomeParametro );
    $obFormulario->addComponenteComposto( $obTxtTipoParametro , $obCmbTipoParametro );

    $obFormulario->addLinha();
    $obFormulario->ultimaLinha->addCelula();
    $obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
    $obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldcenter" );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnAdicionarParametro );
    $obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnLimparParametro    );
    $obFormulario->ultimaLinha->commitCelula();
    $obFormulario->commitLinha();
}

$obFormulario->addSpan              ( $obSpnListaParametro );

$obFormulario->addAba("Variáveis");
$obFormulario->addTitulo            ( "Dados para Variável" );
$obFormulario->addComponente        ( $obTxtNomeVariavel );
$obFormulario->addComponenteComposto( $obTxtTipoVariavel , $obCmbTipoVariavel );
//$obFormulario->addComponente        ( $obTxtValorVariavel );
$obFormulario->addSpan              ( $obSpnValorVariavel );

$obFormulario->addLinha();
$obFormulario->ultimaLinha->addCelula();
$obFormulario->ultimaLinha->ultimaCelula->setColSpan( 2 );
$obFormulario->ultimaLinha->ultimaCelula->setClass( "fieldcenter" );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnAdicionarVariavel );
$obFormulario->ultimaLinha->ultimaCelula->addComponente( $obBtnLimparVariavel    );
$obFormulario->ultimaLinha->commitCelula();
$obFormulario->commitLinha();

/* BOTOES */ /************************************/
$obBtnSalvar = new Button;
$obBtnSalvar->setName( "btnSalvar" );
$obBtnSalvar->setValue( "Salvar" );
$obBtnSalvar->setTitle("Salva função as modificações sem sair!");
$obBtnSalvar->obEvento->setOnClick ( "Salvar_Fica('salva_fica');" );

$obBtnOk = new Button;
$obBtnOk->setName   ( "btnOk" );
$obBtnOk->setValue  ( "Ok" );
$obBtnOk->setTitle  ("Salva função e volta para lista!!");
$obBtnOk->obEvento->setOnClick ("Salvar();");

$obBtnLimpar = new Reset;
$obBtnLimpar->setValue ( "Limpar" );
$obBtnLimpar->setTitle ("Limpa!");

    /* FIM DOS BOTOES *******************************/

    $obFormulario->addSpan              ( $obSpnListaVariavel );

    $obFormulario->addAba("Corpo");
    $obFormulario->addTitulo            ( "Corpo da Função" );

    $obFormularioCorpo->addAba    ("Linguagem Natural");
    $obFormularioCorpo->addLinha();
    $obFormularioCorpo->ultimaLinha->addCelula();

    $obTabela = new Tabela;
    $obTabela->setWidth( 50 );
    $obTabela->addLinha();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "botao" );
    $obTabela->ultimaLinha->ultimaCelula->addComponente( $obLnkAtribuicao );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "botao" );
    $obTabela->ultimaLinha->ultimaCelula->addComponente( $obLnkCondicao );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "botao" );
    $obTabela->ultimaLinha->ultimaCelula->addComponente( $obLnkLaco );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "botao" );
    $obTabela->ultimaLinha->ultimaCelula->addComponente( $obLnkRetorno );
    $obTabela->ultimaLinha->commitCelula();
    $obTabela->commitLinha();

    $obFormularioCorpo->ultimaLinha->ultimaCelula->addTabela( $obTabela );
    $obFormularioCorpo->ultimaLinha->commitCelula();
    $obFormularioCorpo->commitLinha();
    $obFormularioCorpo->addSpan( $obSpnCorpoLN );

    $obFormularioCorpo->addAba   ("Linguagem PLpgSQL");
    $obFormularioCorpo->addSpan  ( $obSpnCorpoPL );
    $obFormularioCorpo->addAba   ();

    $obFormulario->addFormularioAbas( $obFormularioCorpo );
    $obFormulario->addAba();

if ($stAcao == 'alterar') {
    $obFormulario->defineBarra( array( $obBtnOk,$obBtnSalvar, $obBtnLimpar ) );
    $obFormulario->addSpan($obSpnMsgSalva);
} else {
    $obFormulario->OK();
}

    $obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
