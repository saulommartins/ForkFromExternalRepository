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
    * Página de Formulario de Inclusao/Alteracao de Convenio

    * Data de Criação   : 03/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.04

*/

/*
$Log$
Revision 1.18  2007/02/07 15:57:26  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.17  2006/09/15 14:57:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenio";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );
$obRMONConvenio = new RMONConvenio;
$obIMontaAgencia = new IMontaAgencia;

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnNumBanco =  new Hidden;
$obHdnNumBanco->setId     ( "inCodBanco" );
$obHdnNumBanco->setName   ( "inCodBancoTxt" );
$obHdnNumBanco->setValue  ( $_REQUEST["inNumBanco"]  );

$obHdnNumAgencia = new Hidden;
$obHdnNumAgencia->setId      ( "stNumAgenciaTxt"        );
$obHdnNumAgencia->setName    ( "stNumAgencia"           );
$obHdnNumAgencia->setValue   ( $_REQUEST["inNumAgencia"]);

$obHdnCodConvenio = new Hidden;
$obHdnCodConvenio->setName    ( "inCodConvenio"           );
$obHdnCodConvenio->setValue   ( $_REQUEST["inCodConvenio"]);

$obHdnNumConvenio = new Hidden;
$obHdnNumConvenio->setName    ( "inNumConvenio"           );
$obHdnNumConvenio->setValue   ( $_REQUEST["inNumConvenio"]);

$obHdnCodTipo = new Hidden;
$obHdnCodTipo->setName    ( "inCodTipo"           );
$obHdnCodTipo->setValue   ( $_REQUEST["inCodTipoConvenio"]);

$obHdnCedente = new Hidden;
$obHdnCedente->setName    ( "flCedente"           );
$obHdnCedente->setValue   ( $_REQUEST["flCedente"]);

$obLblBanco = new Label ;
$obLblBanco->setRotulo    ( "Banco"                             );
$obLblBanco->setName      ( "labelBanco"                        );
$obLblBanco->setValue     ( $_REQUEST["inNumBanco"]." - ".$_REQUEST["stNomBanco"] );

$obLblAgencia = new Label ;
$obLblAgencia->setRotulo    ( "Agencia"                             );
$obLblAgencia->setName      ( "labelAgencia"                        );
$obLblAgencia->setValue     ( $_REQUEST["inNumAgencia"]." - ".$_REQUEST["stNomAgencia"]  );

$obLblConvenio = new Label ;
$obLblConvenio->setRotulo    ( "Tipo de Convênio"                             );
$obLblConvenio->setName      ( "labelConvenio"                        );
$obLblConvenio->setValue     ( $_REQUEST["inCodTipoConvenio"]." - ".$_REQUEST["stNomTipoConvenio"]  );

$obLblNumConvenio = new Label ;
$obLblNumConvenio->setRotulo    ( "Número do Convênio"                   );
$obLblNumConvenio->setName      ( "labelNumConvenio"                     );
$obLblNumConvenio->setValue     ( $_REQUEST["inNumConvenio"]  );

$obLblCedente = new Label ;
$obLblCedente->setRotulo    ( "Cedente"                   );
$obLblCedente->setName      ( "labelCedente"                     );
$obLblCedente->setValue     ( $_REQUEST["flCedente"]  );

$obLblCCorrente = new Label ;
$obLblCCorrente->setRotulo    ( "Conta Corrente"     );
$obLblCCorrente->setName      ( "labelCCorrente"     );
$obLblCCorrente->setValue     ( $_REQUEST["stNumeroConta"] );

$obTxtNumConvenio = new TextBox;
$obTxtNumConvenio->setRotulo        ( "Número do Convênio"                   );
$obTxtNumConvenio->setTitle         ( "Número do Convênio" );
$obTxtNumConvenio->setName          ( "inNumConvenio"             );
$obTxtNumConvenio->setValue         ( $_REQUEST["inNumConvenio"] );
$obTxtNumConvenio->setSize          ( 10                                            );
$obTxtNumConvenio->setMaxLength     ( 10                                            );
$obTxtNumConvenio->setNull          ( false                                         );
$obTxtNumConvenio->setInteiro       ( true                                          );
$obTxtNumConvenio->obEvento->setOnChange ( "buscaValor('buscaConvenioBanco');"  );

$obTxtCedente = new TextBox;
$obTxtCedente->setRotulo        ( "Cedente"                             );
$obTxtCedente->setTitle         ( "Cedente a ser utilizado no convênio" );
$obTxtCedente->setName          ( "flCedente"                       );
$obTxtCedente->setValue         ( $_REQUEST["flCedente"]            );
$obTxtCedente->setSize          ( 20                                );
$obTxtCedente->setMaxLength     ( 20                                );
$obTxtCedente->setNull          ( true                         );
$obTxtCedente->setInteiro       ( true                         );
//$obTxtCedente->setNegativo      ( false                        );
//$obTxtCedente->setNaoZero       ( true                         );

if ($_REQUEST['flTaxaBancaria']) {
    $tmpTaxa = str_replace(".",",", $_REQUEST['flTaxaBancaria']);
}

$obTxtTaxaBancaria = new Numerico;
$obTxtTaxaBancaria->setRotulo        ( "Taxa Bancária"                     );
$obTxtTaxaBancaria->setTitle         ( "Taxa Bancária Cobrada no Convênio" );
$obTxtTaxaBancaria->setName          ( "flTaxaBancaria"             );
$obTxtTaxaBancaria->setValue         ( $tmpTaxa                     );
$obTxtTaxaBancaria->setSize          ( 10                           );
$obTxtTaxaBancaria->setMaxLength     ( 10                           );
$obTxtTaxaBancaria->setNull          ( true                         );
$obTxtTaxaBancaria->setFloat         ( true                         );
$obTxtTaxaBancaria->setDecimais      ( 2 );
$obTxtTaxaBancaria->obEvento->setOnChange  ( "buscaValor('VerificaTaxa');" );
$obTxtTaxaBancaria->setNegativo      ( true                     );

$obTxtConta = new TextBox ;
$obTxtConta->setRotulo    ( "Conta Corrente"                         );
$obTxtConta->setName      ( "stNumeroConta"                          );
$obTxtConta->setValue     ( $_REQUEST["stNumeroConta"]               );
$obTxtConta->setTitle     ( "Número da conta corrente            "   );
$obTxtConta->setSize      ( 20                                       );
$obTxtConta->setMaxLength ( 20                                       );
$obTxtConta->setNull      ( true                                     );

$obTxtNumVariacao = new TextBox;
$obTxtNumVariacao->setRotulo        ( "Variação" );
$obTxtNumVariacao->setTitle         ( "Número da Variação" );
$obTxtNumVariacao->setName          ( "inNumVariacao" );
$obTxtNumVariacao->setNull          ( true );
$obTxtNumVariacao->setInteiro       ( true );

$obRMONConvenio->ListarTipoConvenio ( $rsTipoConvenio );

$obCmbTipoConvenio = new Select;
$obCmbTipoConvenio->setRotulo ('Tipo de Convênio');
$obCmbTipoConvenio->setTitle ('Tipo de Convênio');
$obCmbTipoConvenio->setName          ( "cmbTipoConvenio"          );
$obCmbTipoConvenio->addOption        ( "", "Selecione"            );
$obCmbTipoConvenio->setValue         ( $_REQUEST['inCodTipoConvenio'] );
$obCmbTipoConvenio->setCampoId       ( "cod_tipo"             );
$obCmbTipoConvenio->setCampoDesc     ( "nom_tipo"             );
$obCmbTipoConvenio->preencheCombo    ( $rsTipoConvenio  );
$obCmbTipoConvenio->setNull          ( false                    );
$obCmbTipoConvenio->setStyle         ( "width: 220px"           );

$obBscConta = new BuscaInner;
$obBscConta->setRotulo ( "*Conta Corrente" );
$obBscConta->setTitle  ( "Conta Corrente que faz parte do convênio"  );
$obBscConta->setId     ( "stNumConta"  );
$obBscConta->setNull   ( true  );
$obBscConta->obCampoCod->setName   ( "inNumConta" );
$obBscConta->obCampoCod->setValue  ( $_REQUEST["inNumConta"] );
$obBscConta->obCampoCod->setSize ( 20 );
$obBscConta->obCampoCod->setMaxLength ( 20 );
$obBscConta->obCampoCod->setInteiro(false);
$obBscConta->obCampoCod->obEvento->setOnChange("buscaValor('buscaConta');");
$obBscConta->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."contaCorrente/FLProcurarConta.php','frm','inNumConta','stConta','todos','".Sessao::getId()."','800','550');" );

//-------------------------------------------------------- BOTOES
$obBtnIncluirConta = new Button;
$obBtnIncluirConta->setName              ( "btnIncluirConta" );
$obBtnIncluirConta->setValue             ( "Incluir"             );
$obBtnIncluirConta->setTipo              ( "button"              );
$obBtnIncluirConta->obEvento->setOnClick ( "incluirConta();" );
$obBtnIncluirConta->setDisabled          ( false                 );

$obBtnLimparConta = new Button;
$obBtnLimparConta->setName               ( "btnLimparConta"  );
$obBtnLimparConta->setValue              ( "Limpar"              );
$obBtnLimparConta->setTipo               ( "button"              );
$obBtnLimparConta->obEvento->setOnClick  ( "limparContas();"  );
$obBtnLimparConta->setDisabled           ( false                 );

$botoesSpanConta = array ( $obBtnIncluirConta , $obBtnLimparConta );

$obSpnListaConta = new Span;
$obSpnListaConta->setID("spnListaConta");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget ("oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->setAjuda      ( "UC-05.05.04" );
$obFormulario->addTitulo     ( "Dados para Convênio" );

$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );

if ($_REQUEST['stAcao'] == "alterar") {
    $obFormulario->addHidden     ( $obHdnNumBanco       );
    $obFormulario->addHidden     ( $obHdnNumAgencia     );
    $obFormulario->addHidden     ( $obHdnCodConvenio    );
    $obFormulario->addHidden     ( $obHdnNumConvenio    );
    $obFormulario->addHidden     ( $obHdnCodTipo        );
    $obFormulario->addHidden     ( $obHdnCedente        );
    $obFormulario->addComponente ( $obLblBanco          );
    $obFormulario->addComponente ( $obLblAgencia        );
    $obFormulario->addComponente ( $obLblConvenio       );
    $obFormulario->addComponente ( $obLblNumConvenio    );
    $obFormulario->addComponente ( $obLblCedente        );
}

if ($_REQUEST['stAcao'] == "incluir") {
    $obIMontaAgencia->geraFormulario( $obFormulario );
    $obFormulario->addComponente ( $obCmbTipoConvenio );
    $obFormulario->addComponente ( $obTxtNumConvenio);
    $obFormulario->addComponente ( $obTxtCedente );
}

$obFormulario->addComponente ( $obTxtTaxaBancaria);

$obFormulario->addTitulo ( 'Contas Correntes' );
$obFormulario->addComponente ( $obBscConta );
$obFormulario->addComponente ( $obTxtNumVariacao );
$obFormulario->defineBarra   ( $botoesSpanConta,'left','' );
$obFormulario->addSpan       ( $obSpnListaConta     );

if ($_REQUEST['stAcao'] == "incluir") {
    $obFormulario->ok();
} else {
    $link = Sessao::read( "link" );
    $obFormulario->Cancelar ($pgList.'&pg='.$link["pg"].'&pos='.$link["pos"]);
}

$obFormulario->show();
Sessao::write( 'contas', array() );

if ($_REQUEST['stAcao'] == "alterar") {
    $js = "buscaValor('recuperaContas');";
    sistemaLegado::executaFrameOculto($js);
}

$stJs .= 'f.inNumBanco.focus();';
sistemaLegado::executaFrameOculto ( $stJs );
