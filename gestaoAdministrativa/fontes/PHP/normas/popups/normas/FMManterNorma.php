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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");

$stPrograma = "ManterNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList =  "LSNorma.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma."2.php";
$pgJs   = "JS".$stPrograma.".js";

//MANTEM FILTRO E PAGINACAO
//$stLink .= "&stAcao=".$stAcao;
if ($_GET["pg"] and  $_GET["pos"]) {
    Sessao::write('linkPopUp_pg', $_GET["pg"]);
    Sessao::write('linkPopUp_pos', $_GET["pos"]);
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array(Sessao::read('linkPopUp')) ) {
    $_REQUEST = Sessao::read('linkPopUp');
} else {
    $arLinkPopUp = array();
    foreach ($_REQUEST as $key => $valor) {
        $arLinkPopUp[$key] = $valor;
    }
    Sessao::write('linkPopUp',$arLinkPopUp);
}

include($pgJs);

$rsNorma = $rsTipoNorma = $rsAtributos = new RecordSet;
$obRegra = new RNorma;

$stExercicio = Sessao::getExercicio();

//$obRegra->obRTipoNorma->obRCadastroDinamico->obRModulo->setCodModulo(15);
//$obRegra->obRTipoNorma->obRCadastroDinamico->verificaModulo();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

if ( (empty($stAcao)) || ($stAcao=="incluir")) {
    $js.= "limpaCampos ();";
    Sessao::write("stAcao", $stAcao);
    $obRegra->obRTipoNorma->listar( $rsTipoNorma );

}
    $obLblLink = new Label;
    $obLblLink->setRotulo ( "Arquivo" );
    $obLblLink->setName   ( "stlblLabel" );
    $obLblLink->setValue  ( "&nbsp;");
    $obLblLink->setId     ( "spnlink" );

$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setName ("Atributo_");
$obMontaAtributos->setRecordSet( $rsAtributos );
$obMontaAtributos->recuperaValores();

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( "" );

//Caso inclusão
$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo        ( "Tipo" );
$obTxtTipoNorma->setName          ( "inCodTipoNorma" );
$obTxtTipoNorma->setValue         ( $inCodTipoNorma );
$obTxtTipoNorma->setSize          ( 5 );
$obTxtTipoNorma->setMaxLength     ( 5 );
$obTxtTipoNorma->setInteiro       ( true  );
$obTxtTipoNorma->setNull          ( false );
$obTxtTipoNorma->obEvento->setOnChange("executaFuncaoAjax('MontaAtributos');");
$obTxtTipoNorma->setTitle         ( "Selecione o Tipo" );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo        ( "Tipo" );
$obCmbTipoNorma->setName          ( "stNomeTipoNorma" );
$obCmbTipoNorma->setStyle         ( "width: 200px");
$obCmbTipoNorma->setCampoID       ( "cod_tipo_norma" );
$obCmbTipoNorma->setCampoDesc     ( "nom_tipo_norma" );
$obCmbTipoNorma->addOption        ( "", "Selecione" );
if ( $stAcao == "alteracao" )
    $obCmbTipoNorma->setValue         ( $inCodTipoNorma );
$obCmbTipoNorma->setNull          ( false );
$obCmbTipoNorma->preencheCombo    ( $rsTipoNorma );
$obCmbTipoNorma->obEvento->setOnChange("buscaValor('MontaAtributos');");
$obCmbTipoNorma->setTitle         ( "Selecione o Tipo" );

//Caso alteração
$obLblTipoNorma = new Label;
$obLblTipoNorma->setRotulo        ( "Tipo" );
$obLblTipoNorma->setName          ( "stNomeTipoNorma" );
$obLblTipoNorma->setValue         ( $stNomeTipoNorma );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo        ( "Número da norma" );
$obTxtNorma->setTitle         ( "Informe o número da norma" );
$obTxtNorma->setName          ( "inNumNorma" );
$obTxtNorma->setValue         ( $inNumNorma  );
$obTxtNorma->setSize          ( 6 );
$obTxtNorma->setMaxLength     ( 6 );
$obTxtNorma->setCaracteresAceitos ("[a-zA-Z0-9\-/.]");
$obTxtNorma->setNull          ( false );
$obTxtNorma->obEvento->setOnChange("formataValoresNorma(this);buscaValor('MontaAtributos');");

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo            ( "Exercício" );
$obTxtExercicio->setTitle             ( "Informe o exercício da norma" );
$obTxtExercicio->setName              ( "stExercicio" );
$obTxtExercicio->setValue             ( $stExercicio  );
$obTxtExercicio->setSize              ( 6 );
$obTxtExercicio->setMaxLength         ( 4 );
$obTxtExercicio->setInteiro           ( true  );
$obTxtExercicio->setNull              ( false );
$obTxtExercicio->obEvento->setOnChange("validaExercicio(this)");

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName ( "inCodNorma" );
$obHdnCodNorma->setValue( $inCodNorma  );

$obHdnCodTipoNorma = new Hidden;
$obHdnCodTipoNorma->setName ( "inCodTipoNorma" );
$obHdnCodTipoNorma->setValue( $inCodTipoNorma  );

$obTxtNome = new TextBox;
$obTxtNome->setRotulo        ( "Nome" );
$obTxtNome->setName          ( "stNomeNorma" );
$obTxtNome->setValue         ( $stNomeNorma  );
$obTxtNome->setSize          ( 40 );
$obTxtNome->setMaxLength     ( 40 );
$obTxtNome->setNull          ( false );
$obTxtNome->setTitle         ( "Informe o nome da norma" );

$obTxtDescricao = new TextArea;
$obTxtDescricao->setRotulo        ( "Descrição" );
$obTxtDescricao->setName          ( "stDescricao" );
$obTxtDescricao->setValue         ( $stDescricao  );
$obTxtDescricao->setTitle         ( "Informe a descrição da norma" );

$obTxtData = new Data;
$obTxtData->setRotulo        ( "Data de Publicação" );
$obTxtData->setName          ( "stDataPublicacao" );
$obTxtData->setValue         ( $stDataPublicacao  );
$obTxtData->setNull          ( false );
$obTxtData->setTitle         ( "Informe a data de publicação da norma" );

$obTxtDataAssinatura = new Data;
$obTxtDataAssinatura->setRotulo        ( "Data de Assinatura" );
$obTxtDataAssinatura->setName          ( "stDataAssinatura" );
$obTxtDataAssinatura->setValue         ( $stDataAssinatura  );
$obTxtDataAssinatura->setNull          ( false );
$obTxtDataAssinatura->setTitle         ( "Informe a data de assinatura da norma" );

$obTxtDataTermino = new Data;
$obTxtDataTermino->setRotulo        ( "Data de Término" );
$obTxtDataTermino->setName          ( "stDataTermino" );
$obTxtDataTermino->setValue         ( $stDataTermino  );
$obTxtDataTermino->setTitle         ( "Informe a data de Término da norma" );

$obBtnLink = new FileBox;
$obBtnLink->setNull   ( true                                     );
$obBtnLink->setRotulo ( "Arquivo"                                );
$obBtnLink->setTitle  ( "Informe o caminho do arquivo"           );
$obBtnLink->setName   ( "btnIncluirLink"                         );
$obBtnLink->setId     ( "btnIncluirLink"                         );
$obBtnLink->setSize   ( 35                                       );
$obBtnLink->setValue  ( $btnIncluirLink  );

$obSpan = new Span;
$obSpan->setId ( "spanAtributos" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgProc );
$obForm->setTarget                  ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->setAjuda             ( "UC-01.04.02" );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );
$obFormulario->addHidden            ( $obHdnEval , true);

$obFormulario->addTitulo            ( "Dados da Norma" );
if ($stAcao=='incluir') {
    $obFormulario->addComponenteComposto( $obTxtTipoNorma , $obCmbTipoNorma );
} else {
    $obFormulario->addHidden        ( $obHdnCodNorma      );
    $obFormulario->addHidden        ( $obHdnCodTipoNorma  );
    $obFormulario->addComponente    ( $obLblTipoNorma     );
}
$obFormulario->addComponente        ( $obTxtNorma         );
$obFormulario->addComponente        ( $obTxtExercicio     );
$obFormulario->addComponente        ( $obTxtNome          );
$obFormulario->addComponente        ( $obTxtDescricao     );
$obFormulario->addComponente        ( $obTxtData          );
$obFormulario->addComponente        ( $obTxtDataAssinatura);
$obFormulario->addComponente        ( $obTxtDataTermino   );
$obFormulario->addComponente        ( $obBtnLink          );

$obFormulario->addSpan          ( $obSpan );

$obBtnOk = new OK;
$obBtnLimpar = new Limpar;
$obBtnCancelar = new Cancelar;
$obBtnCancelar->obEvento->setOnClick( "CancelarForm();" );

$obFormulario->defineBarra               (array($obBtnOk, $obBtnLimpar, $obBtnCancelar));

$obFormulario->show                 ();
$obIFrame = new IFrame;
$obIFrame->setName  ("oculto"   );
$obIFrame->setWidth ("100%"     );
$obIFrame->setHeight("0"      );

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem" );
$obIFrame2->setWidth  ( "100%"         );
$obIFrame2->setHeight ( "50"           );
$obIFrame2->show();
$obIFrame->show();

/*include_once($pgJs);

if ( $stAcao == "incluir" )
    $js .= "focusIncluir();";
else
    $js .= "focusAlterar();";

sistemaLegado::executaFrameOculto($js);*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
