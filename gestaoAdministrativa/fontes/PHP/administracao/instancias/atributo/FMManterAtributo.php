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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 24714 $
$Name$
$Author: domluc $
$Date: 2007-08-13 17:38:25 -0300 (Seg, 13 Ago 2007) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RAdministracaoMenu.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$arSessaoValores = array();
Sessao::write('Valores',array());

$inCount = 0;

$obRAdministracaoMenu = new RAdministracaoMenu;
$obRAtributoDinamico = new RAtributoDinamico;
$rsGestao = new RecordSet;
$rsModulo =  new RecordSet;
$rsCadastro = new RecordSet;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
} elseif ($stAcao == "alterar") {
    $obRAdministracaoGestao = new RAdministracaoGestao;
    $obRAdministracaoGestao->setCodigoGestao( $_GET['inCodGestao'] );
    $obRAdministracaoGestao->consultarGestao();
    $stNomGestao = $obRAdministracaoGestao->getNomeGestao();

    $obRAtributoDinamico->obRModulo->setCodModulo ( $_REQUEST['inCodModulo']   );
    $obRAtributoDinamico->setCodAtributo          ( $_REQUEST['inCodAtributo'] );
    $obRAtributoDinamico->setCodCadastro          ( $_REQUEST['inCodCadastro'] );
    $obRAtributoDinamico->setCodTipo              ( $_REQUEST['inCodTipo']     );

    $obRAtributoDinamico->consultar               ( $rsAtributo );

    $stNomTipoAtributo  = $_REQUEST['stNomTipo'];
    $stNomAtributo      = $obRAtributoDinamico->getNome();
    $stMascara          = $obRAtributoDinamico->getMascara();
    $inCodTipoAtributo  = $obRAtributoDinamico->getCodTipo();
    $boNaoNulo          = $obRAtributoDinamico->getObrigatorio();
    $boAtributoAtivo    = $obRAtributoDinamico->getAtivo();
    $inCodModulo        = $obRAtributoDinamico->obRModulo->getCodModulo();
    $inCodCadastro      = $obRAtributoDinamico->getCodCadastro();
    $stNomCadastro      = $rsAtributo->getCampo( "nom_cadastro" );
    $arRestricoes       = $obRAtributoDinamico->getRegras();
    $stMensagemAuxiliar = $obRAtributoDinamico->getAjuda();
    $arValores = $obRAtributoDinamico->getValores();
    foreach ($arValores as $arValor) {
        if ($inCodTipoAtributo==3 || $inCodTipoAtributo==4) {
            $arSessaoValores[$inCount]['inId']        = ($inCount+1);
            $arSessaoValores[$inCount]['cod_valor']   = $arValor['cod_valor'];
            $arSessaoValores[$inCount]['ativo']       = ($arValor['ativo']=='true') ? 'Sim' : 'Não';
            $arSessaoValores[$inCount]['valor']       = $arValor['valor'];
            $arSessaoValores[$inCount++]['excluir']   = false;
        } else {
            $arSessaoValores[0]['valor']       = $arValor['valor'];
        }
    }
    Sessao::write('Valores',$arSessaoValores);

    $obRAtributoDinamico->obRModulo->setCodModulo( $inCodModulo );
    $obRAtributoDinamico->obRModulo->consultar( $rsModulo );
    $stNomModulo = $rsModulo->getCampo('nom_modulo');

    $stOperacao = $arRestricoes[0]["sinal"];
    $inTxtValorAtributo = $arRestricoes[0]["valor"];

    $inCodAtributoCIM = $arRestricoes[1]["chave"];
    $stOperacaoCondicional = $arRestricoes[1]["sinal"];
    $inTxtValorAtributoValida = $arRestricoes[1]["valor"];

    $stCampoReferencial = $arRestricoes[2]["campo"];
    $stTabelaReferencial = $arRestricoes[2]["tabela"];

    SistemaLegado::executaFramePrincipal("goOculto('MontaValores');");

} elseif ($stAcao == 'incluir' and $_GET['inCodGestao']) {
    $obRAdministracaoGestao = new RAdministracaoGestao;
    $obRAdministracaoGestao->setCodigoGestao( $_GET['inCodGestao'] );
    $obErro = $obRAdministracaoGestao->listarModulos();
    if ( !$obErro->ocorreu() ) {
        $arModulo = array();
        while ( !$obRAdministracaoGestao->rsRModulo->eof() ) {
            $obRModulo = $obRAdministracaoGestao->rsRModulo->getObjeto();
            $arModulo[] = array( 'cod_modulo' => $obRModulo->getCodModulo(),
                               'nom_modulo' => $obRModulo->getNomModulo() );
            $obRAdministracaoGestao->rsRModulo->proximo();
        }
        $rsModulo->preenche( $arModulo );
    }
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->obRModulo->setCodModulo( $_REQUEST["inCodModulo"] );
    $obErro = $obRCadastroDinamico->recuperaCadastros( $rsCadastro );
}

if ($stAcao == 'incluir') {
    if ( Sessao::read('numCgm') == '0' ) {
        $obErro = $obRAdministracaoMenu->listarGestoesPorOrdem();
    } else {
        $obErro = $obRAdministracaoMenu->listarGestoes();
    }
    //Monta o recordset para o preenchimento do combo de gestões
    $rsGestao = new RecordSet;
    if ( !$obErro->ocorreu() ) {
        $arGestao = Array();
        while ( !$obRAdministracaoMenu->rsRAdministracaoGestao->eof() ) {
            $obRGestao = $obRAdministracaoMenu->rsRAdministracaoGestao->getObjeto();
            $arTmpGestao = array( 'cod_gestao' => $obRGestao->getCodigoGestao(),
                                  'nom_gestao' => $obRGestao->getNomeGestao() );
            $arGestao[] = $arTmpGestao;
            $obRAdministracaoMenu->rsRAdministracaoGestao->proximo();
        }
        $rsGestao->preenche( $arGestao );
    }
}

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

$obHdnAcao  = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCodTipo = new Hidden;
$obHdnCodTipo->setName  ( "inCodTipo" );
$obHdnCodTipo->setValue ( $_GET['inCodTipo'] );

$obHdnCodAtributo = new Hidden;
$obHdnCodAtributo->setName  ( "inCodAtributo" );
$obHdnCodAtributo->setValue ( $_GET['inCodAtributo'] );

$obCmbGestao = new Select;
$obCmbGestao->setRotulo        ( "Gestão" );
$obCmbGestao->setName          ( "inCodGestao" );
$obCmbGestao->setValue         ( $_REQUEST['inCodGestao'] );
$obCmbGestao->setStyle         ( "width: 200px");
$obCmbGestao->setCampoID       ( "cod_gestao" );
$obCmbGestao->setCampoDesc     ( "nom_gestao" );
$obCmbGestao->addOption        ( "", "Selecione" );
$obCmbGestao->setNull          ( false );
$obCmbGestao->preencheCombo    ( $rsGestao );
$obCmbGestao->obEvento->SetOnChange("goOculto('MontaModulo');");

$obLblGestao = new Label;
$obLblGestao->setRotulo         ( "Gestão" );
$obLblGestao->setValue          ( $inCodGestao.' - '.$stNomGestao );

$obHdnGestao = new Hidden;
$obHdnGestao->setName  ( "inCodGestao" );
$obHdnGestao->setValue ( $inCodGestao );

$obCmbModulo = new Select;
$obCmbModulo->setRotulo        ( "Módulo" );
$obCmbModulo->setName          ( "inCodModulo" );
$obCmbModulo->setValue         ( $_REQUEST['inCodModulo']);
$obCmbModulo->setStyle         ( "width: 200px");
$obCmbModulo->setCampoID       ( "cod_modulo" );
$obCmbModulo->setCampoDesc     ( "nom_modulo" );
$obCmbModulo->addOption        ( "", "Selecione" );
$obCmbModulo->setNull          ( false );
$obCmbModulo->preencheCombo    ( $rsModulo );
$obCmbModulo->obEvento->SetOnChange("goOculto('MontaCadastro');");

$obLblModulo = new Label;
$obLblModulo->setRotulo         ( "Módulo" );
$obLblModulo->setValue          ($inCodModulo.' - '.$stNomModulo);

$obHdnModulo = new Hidden;
$obHdnModulo->setName  ( "inCodModulo" );
$obHdnModulo->setValue ( $inCodModulo );

$obCmbCadastro = new Select;
$obCmbCadastro->setRotulo        ( "Cadastro" );
$obCmbCadastro->setName          ( "inCodCadastro" );
$obCmbCadastro->setValue         ( $_REQUEST['inCodCadastro']);
$obCmbCadastro->setStyle         ( "width: 200px");
$obCmbCadastro->addOption        ( "", "Selecione" );
$obCmbCadastro->setCampoID       ( "cod_cadastro" );
$obCmbCadastro->setCampoDesc     ( "nom_cadastro" );
$obCmbCadastro->preencheCombo    ( $rsCadastro );
$obCmbCadastro->setNull          ( false );

$obLblCadastro = new Label;
$obLblCadastro->setRotulo         ( "Cadastro" );
$obLblCadastro->setValue          ($inCodCadastro.' - '.$stNomCadastro);

$obHdnCadastro = new Hidden;
$obHdnCadastro->setName  ( "inCodCadastro" );
$obHdnCadastro->setValue ( $inCodCadastro );

$obTxtNomeAtributo = new TextBox;
$obTxtNomeAtributo->setName         ( "stNomAtributo" );
$obTxtNomeAtributo->setRotulo       ( "Nome");
$obTxtNomeAtributo->setSize         ( 80 );
$obTxtNomeAtributo->setMaxLength    ( 80 );
$obTxtNomeAtributo->setNull         ( false );
$obTxtNomeAtributo->setValue        ( $stNomAtributo );

$obTxtMascara = new TextBox;
$obTxtMascara->setName              ( "stMascara" );
$obTxtMascara->setRotulo            ( "Máscara" );
$obTxtMascara->setSize              ( 80 );
$obTxtMascara->setMaxLength         ( 80 );
$obTxtMascara->setValue             ( $stMascara );

$rsTipoAtributo = new RecordSet;
$obRAtributoDinamico->recuperaTodosTipoAtributo( $rsTipoAtributo, "", " ORDER BY nom_tipo " );

$obTxtTipoAtributo = new TextBox;
$obTxtTipoAtributo->setName         ( "inTxtCodTipoAtributo" );
$obTxtTipoAtributo->setInteiro      ( true );
$obTxtTipoAtributo->setRotulo       ( "Tipo" );
$obTxtTipoAtributo->setValue        ( $inCodTipoAtributo );
$obTxtTipoAtributo->obEvento->SetOnChange("goOculto('MontaValores');");

$obCmbTipoAtributo = new Select;
$obCmbTipoAtributo->setName         ( "inCodTipoAtributo" );
$obCmbTipoAtributo->setRotulo       ( "Tipo" );
$obCmbTipoAtributo->setNull         ( false );
$obCmbTipoAtributo->setCampoID      ( "cod_tipo" );
$obCmbTipoAtributo->setCampoDesc    ( "nom_tipo" );
$obCmbTipoAtributo->addOption       ( "", "Selecione" );
$obCmbTipoAtributo->setValue        ( $inCodTipoAtributo );
$obCmbTipoAtributo->preencheCombo   ( $rsTipoAtributo );
$obCmbTipoAtributo->setStyle        ( "width: 200px" );
$obCmbTipoAtributo->obEvento->SetOnChange("goOculto('MontaValores');");

$obLblTipoAtributo = new Label;
$obLblTipoAtributo->setRotulo ( "Tipo" );
$obLblTipoAtributo->setValue  ($inCodTipoAtributo.' - '.$stNomTipoAtributo);
$obHdnCodTipoAtributo = new Hidden;
$obHdnCodTipoAtributo->setName  ( "inCodTipoAtributo" );
$obHdnCodTipoAtributo->setValue ( $inCodTipoAtributo );

$obSpnValores = new Span;
$obSpnValores->setID("spnValores");

$obRdNaoNuloT = new Radio;
$obRdNaoNuloT->setName              ( "boNaoNulo" );
$obRdNaoNuloT->setValue             ( "true" );
$obRdNaoNuloT->setChecked           ( true );
$obRdNaoNuloT->setLabel             ( "Sim" );

$obRdNaoNuloF = new Radio;
$obRdNaoNuloF->setName              ( "boNaoNulo" );
$obRdNaoNuloF->setValue             ( "false" );
$obRdNaoNuloF->setChecked           ( false );
$obRdNaoNuloF->setLabel             ( "Não" );

if ($boNaoNulo == "Não") {
    $obRdNaoNuloT->setChecked( false );
    $obRdNaoNuloF->setChecked( true );
}

$obRdAtivoT = new Radio;
$obRdAtivoT->setRotulo            ( "Atributo Ativo" );
$obRdAtivoT->setName              ( "boAtributoAtivo" );
$obRdAtivoT->setValue             ( "true" );
$obRdAtivoT->setChecked           ( true );
$obRdAtivoT->setLabel             ( "Sim" );

$obRdAtivoF = new Radio;
$obRdAtivoF->setName              ( "boAtributoAtivo" );
$obRdAtivoF->setValue             ( "false" );
$obRdAtivoF->setChecked           ( false );
$obRdAtivoF->setLabel             ( "Não" );

if ($boAtributoAtivo == "f") {
    $obRdAtivoT->setChecked( false );
    $obRdAtivoF->setChecked( true );
}

$obRdIndexavelT = new Radio;
$obRdIndexavelT->setRotulo            ( "Indexável" );
$obRdIndexavelT->setName              ( "boIndexavel" );
$obRdIndexavelT->setValue             ( "true" );
$obRdIndexavelT->setChecked           ( true );
$obRdIndexavelT->setLabel             ( "Sim" );

$obRdIndexavelF = new Radio;
$obRdIndexavelF->setRotulo            ( "Indexável" );
$obRdIndexavelF->setName              ( "boIndexavel" );
$obRdIndexavelF->setValue             ( "false" );
$obRdIndexavelF->setChecked           ( false );
$obRdIndexavelF->setLabel             ( "Não" );

if ($boIndexavel == "t") {
    $obRdIndexavelF->setChecked( false );
    $obRdIndexavelT->setChecked( true );
} else {
    $obRdIndexavelT->setChecked( false );
    $obRdIndexavelF->setChecked( true );
}

$obTxtMensagemAuxiliar = new TextBox;
$obTxtMensagemAuxiliar->setRotulo       ( "Mensagem Auxiliar" );
$obTxtMensagemAuxiliar->setName         ( "stMensagemAuxiliar" );
$obTxtMensagemAuxiliar->setValue        ( $stMensagemAuxiliar );
$obTxtMensagemAuxiliar->setSize         (80);
$obTxtMensagemAuxiliar->setMaxLength    (80);

//DEFINICAO DOS CAMPOS PARA INTEGRIDADE CONDICIONAL
$obCmbOperacao = new Select;
$obCmbOperacao->setName    ( "stOperacao" );
$obCmbOperacao->setValue   ( $stOperacao );
$obCmbOperacao->setStyle   ( "width: 200px" );
$obCmbOperacao->setRotulo  ( "Operação");
$obCmbOperacao->addOption  ( "","Selecione" );
$obCmbOperacao->addOption  ( "=", "=" );
$obCmbOperacao->addOption  ( "!=", "!=" );
$obCmbOperacao->addOption  ( ">", ">" );
$obCmbOperacao->addOption  ( "<", "<" );
$obCmbOperacao->addOption  ( ">=", ">=" );
$obCmbOperacao->addOption  ( "<=", "<=" );

$obTxtValor = new TextBox;
$obTxtValor->setRotulo          ( "Valor" );
$obTxtValor->setTitle           ( "Valor" );
$obTxtValor->setName            ( "inTxtValorAtributo" );
$obTxtValor->setValue           ( $inTxtValorAtributo );
$obTxtValor->setSize            ( 80 );
$obTxtValor->setMaxLength       ( 80 );

$rsAtributo = new RecordSet;
$obRAtributoDinamico->listar( $rsAtributo );

$obTxtAtributoCIM = new TextBox;
$obTxtAtributoCIM->setRotulo        ( "Atributo" );
$obTxtAtributoCIM->setName          ( "inTxtCodAtributoCIM" );
$obTxtAtributoCIM->setValue         ( $inCodAtributoCIM );
$obTxtAtributoCIM->setSize          ( 8 );
$obTxtAtributoCIM->setMaxLength     ( 8 );

$obCmbAtributoCIM = new Select;
$obCmbAtributoCIM->setName          ( "inCodAtributoCIM" );
$obCmbAtributoCIM->setStyle         ( "width: 200px");
$obCmbAtributoCIM->setCampoID       ( "[cod_tipo]-[cod_atributo]" );
$obCmbAtributoCIM->setCampoDesc     ( "nom_atributo" );
$obCmbAtributoCIM->addOption        ( "", "Selecione" );
$obCmbAtributoCIM->setValue         ( $inCodAtributoCIM );
$obCmbAtributoCIM->preencheCombo    ( $rsAtributo );

$obCmbOperacaoCondicional = new Select;
$obCmbOperacaoCondicional->setName    ( "stOperacaoCondicional" );
$obCmbOperacaoCondicional->setStyle   ( "width: 200px" );
$obCmbOperacaoCondicional->setRotulo  ( "Operação");
$obCmbOperacaoCondicional->setValue   ( $stOperacaoCondicional );
$obCmbOperacaoCondicional->addOption  ( "","Selecione" );
$obCmbOperacaoCondicional->addOption  ( "=", "=" );
$obCmbOperacaoCondicional->addOption  ( "!=", "!=" );
$obCmbOperacaoCondicional->addOption  ( ">", ">" );
$obCmbOperacaoCondicional->addOption  ( "<", "<" );
$obCmbOperacaoCondicional->addOption  ( ">=", ">=" );
$obCmbOperacaoCondicional->addOption  ( "<=", "<=" );

$obTxtValorValida = new TextBox;
$obTxtValorValida->setRotulo          ( "Valor" );
$obTxtValorValida->setTitle           ( "Valor" );
$obTxtValorValida->setName            ( "inTxtValorAtributoValida" );
$obTxtValorValida->setValue           ( $inTxtValorAtributoValida );
$obTxtValorValida->setSize            ( 80 );

//DEFINICAO DOS CAMPOS PARA INTEGRIDADE REFERENCIAL
$obTxtCompoReferencial = new TextBox;
$obTxtCompoReferencial->setName     ( "stCampoReferencial" );
$obTxtCompoReferencial->setRotulo   ( "Campo(s)" );
$obTxtCompoReferencial->setValue    ( $stCampoReferencial );
$obTxtCompoReferencial->setSize     ( 80 );
$obTxtCompoReferencial->setMaxLength( 80 );

$obTxtTabelaReferencial = new TextBox;
$obTxtTabelaReferencial->setName     ( "stTabelaReferencial" );
$obTxtTabelaReferencial->setValue    ( $stTabelaReferencial );
$obTxtTabelaReferencial->setRotulo   ( "Tabela" );
$obTxtTabelaReferencial->setSize     ( 80 );
$obTxtTabelaReferencial->setMaxLength( 80 );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;

#$obFormulario->addAba                   ( "Atributo" );
#$obFormulario->addFuncaoAba             ( "HabilitaLayer('');"   );

$obFormulario->addTitulo                ( "Dados para Atributo" );
$obFormulario->setAjuda("uc-01.03.96");
$obFormulario->addForm                  ( $obForm );
$obFormulario->addHidden                ( $obHdnAcao );
$obFormulario->addHidden                ( $obHdnCtrl );
$obFormulario->addHidden                ( $obHdnCodTipo );
$obFormulario->addHidden                ( $obHdnCodAtributo );
if ($stAcao=='incluir') {
    $obFormulario->addComponente            ( $obCmbGestao );
    $obFormulario->addComponente            ( $obCmbModulo );
    $obFormulario->addComponente            ( $obCmbCadastro );
} else {
    $obFormulario->addHidden                ( $obHdnGestao );
    $obFormulario->addComponente            ( $obLblGestao );
    $obFormulario->addHidden                ( $obHdnModulo );
    $obFormulario->addComponente            ( $obLblModulo );
    $obFormulario->addHidden                ( $obHdnCadastro );
    $obFormulario->addComponente            ( $obLblCadastro );
}
if ($stAcao=='alterar') {
    $obFormulario->agrupaComponentes        ( array( $obRdAtivoT , $obRdAtivoF ) );
}
$obFormulario->addComponente            ( $obTxtNomeAtributo );
$obFormulario->addComponente            ( $obTxtMascara );
if ($stAcao=='incluir') {
    $obFormulario->addComponenteComposto    ( $obTxtTipoAtributo, $obCmbTipoAtributo );
} else {
    $obFormulario->addHidden                ( $obHdnCodTipoAtributo );
    $obFormulario->addComponente            ( $obLblTipoAtributo );
}
$obFormulario->addSpan                  ( $obSpnValores );
$obFormulario->abreLinha                ();
$obFormulario->addRotulo                ( "Aceita Nulo", "Aceita Nulo" );
$obFormulario->addCampo                 ( $obRdNaoNuloT, true, false );
$obFormulario->addCampo                 ( $obRdNaoNuloF, false, true );
$obFormulario->fechaLinha               ();
$obFormulario->agrupaComponentes        ( array( $obRdIndexavelT, $obRdIndexavelF ) );
$obFormulario->addComponente            ( $obTxtMensagemAuxiliar );

# Abas não mais necessárias Ticket: #23984
#$obFormulario->addAba                   ( "Integridade Condicional" );
#$obFormulario->addFuncaoAba             ( "HabilitaLayer('');"   );
#$obFormulario->addTitulo                ( "Integridade Condicional" );
#$obFormulario->addComponente            ( $obCmbOperacao );
#$obFormulario->addComponente            ( $obTxtValor );
#$obFormulario->addComponenteComposto    ( $obTxtAtributoCIM, $obCmbAtributoCIM );
#$obFormulario->addComponente            ( $obCmbOperacaoCondicional );
#$obFormulario->addComponente            ( $obTxtValorValida );
#$obFormulario->addAba                   ( "Integridade Referencial" );
#$obFormulario->addFuncaoAba             ( "HabilitaLayer('');"   );
#$obFormulario->addTitulo                ( "Integridade Referencial" );
#$obFormulario->addComponente            ( $obTxtCompoReferencial );
#$obFormulario->addComponente            ( $obTxtTabelaReferencial );

$obFormulario->OK                       ();
$obFormulario->show                     ();

?>
