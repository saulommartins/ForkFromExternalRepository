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
    * Pagina de Formulario de Inclusao/Alteracao de CREDITO

    * Data de Criacao: 22/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMManterCredito.php 63509 2015-09-04 14:32:22Z michel $

    *Casos de uso: uc-05.05.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_MON_NEGOCIO.'RMONCredito.class.php';
include_once CAM_GT_MON_NEGOCIO.'RMONCarteira.class.php';
include_once CAM_GT_MON_NEGOCIO.'RMONConvenio.class.php';
include_once CAM_GT_MON_COMPONENTES.'IPopUpAcrescimo.class.php';

$obRMONCredito =  new RMONCredito;
$rsCarteira = new RecordSet();

Sessao::write( "acrescimos", array() );
Sessao::write( "listaFundamentacao", array() );

$stMascaraCalculo = "99.9.999";

$obBscDesoneracao = new BuscaInner;
$obBscDesoneracao->setNull             ( false );
$obBscDesoneracao->setTitle            ( "Regra que será utilizada para conceder desoneração aos lançamentos." );
$obBscDesoneracao->setRotulo           ( "Regra p/ Desoneração" );
$obBscDesoneracao->setId               ( "stFormula"  );
$obBscDesoneracao->setNull ( true );

if ($request->get("stNomFuncao")) {
    $obBscDesoneracao->setValue ( sprintf( "%03d - %s", $request->get("inCodFuncao"), $request->get("stNomFuncao") ) );
}

$obBscDesoneracao->obCampoCod->setName ( "inCodigoFormula" );

if ($request->get("inCodFuncao")) {
    $stCodDes = sprintf( "%02d.%d.%03d", $request->get("inCodModulo"), $request->get("inCodBiblioteca"), $request->get("inCodFuncao") );
}

$obBscDesoneracao->obCampoCod->setValue( $stCodDes  );
$obBscDesoneracao->obCampoCod->setInteiro ( true );
$obBscDesoneracao->obCampoCod->setNull ( true );
$obBscDesoneracao->obCampoCod->setSize (  9   );
$obBscDesoneracao->obCampoCod->obEvento->setOnChange( "buscaValor('buscaFuncao');" );
$obBscDesoneracao->setFuncaoBusca      (  "abrePopUp('".CAM_GA_ADM_POPUPS."funcao/FLBuscarFuncao.php?".Sessao::getId()."&stCodModulo=25&stCodBiblioteca=2&','frm','inCodigoFormula','stFormula','','".Sessao::getId()."','800','550');" );
$obBscDesoneracao->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraCalculo."', this, event);");
$obBscDesoneracao->obCampoCod->setMinLength ( strlen($stMascaraCalculo) );

if ($request->get('stAcao') == 'alterar') {
    $obRMONCredito->setCodCredito   ( $request->get('inCodCredito') );
    $obRMONCredito->setCodNatureza  ( $request->get('inCodNatureza') );
    $obRMONCredito->setCodGenero    ( $request->get('inCodGenero') );
    $obRMONCredito->ListarGeneroNatureza($rsGenero);
    $obRMONCredito->ListarEspecie       ($rsEspecie);

    $rsConvenio = new Recordset();
    if($request->get("inCodConvenio")){
        $obRMONConvenio = new RMONConvenio;
        $obRMONConvenio->setCodigoConvenio( $request->get("inCodConvenio") );
        $obRMONConvenio->listarConvenio( $rsConvenio );
    }
    $inNumConvenio = $rsConvenio->getCampo( "num_convenio" );

    $obErro = $obRMONCredito->buscaMoedaCredito( $rsRecordSetA, $boTransacao );
    if ( !$obErro->ocorreu () ) {
        $timestampMoeda = $rsRecordSetA->getCampo("timestamp");
    }
    $obErro = $obRMONCredito->buscaIndicadorCredito( $rsRecordSetB, $boTransacao );
    if ( !$obErro->ocorreu () ) {
        $timestampIndicador = $rsRecordSetB->getCampo("timestamp");
    }

    if ($timestampMoeda > $timestampIndicador) {
        $boIndexacao = "moeda";
        $inCodIndexacao = $rsRecordSetA->getCampo("cod_moeda");
        $stNomeIndexacao = $rsRecordSetA->getCampo("descricao_plural");
    } else {
        $boIndexacao = "indicador";
        $inCodIndexacao = $rsRecordSetA->getCampo("cod_indicador");
        $stNomeIndexacao = $rsRecordSetA->getCampo("descricao");
    }

} else {
    $rsGenero = new recordSet();
    $rsEspecie = new recordSet();
    $rsNormaCredito = new recordSet();
}

//Define a funcao do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCredito";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

/***********************************************/

include_once ( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $request->get('stCtrl') );

$obHdnCodCredito = new Hidden;
$obHdnCodCredito->setName  ('inCodCredito');
$obHdnCodCredito->setValue ( $request->get('inCodCredito') );

$obHdnCodConta = new Hidden;
$obHdnCodConta->setName  ('inCodConta');
$obHdnCodConta->setValue ( $request->get('inCodConta') );

$obHdnSimboloMoeda = new Hidden;
$obHdnSimboloMoeda->setName ('inSimboloMoeda');

$obHdnAbreviatura = new Hidden;
$obHdnAbreviatura->setName ('inAbreviatura');

//------------------------------------------ PARAMETOS PARA O CAMPO DE INDEXACAO
$obHdnCodIndexacao = new Hidden;
$obHdnCodIndexacao->setName  ('inCodIndexacao');
$obHdnCodIndexacao->setValue ( $inCodIndexacao );

$obHdnNomeIndexacao = new Hidden;
$obHdnNomeIndexacao->setName  ('stNomeIndexacao');
$obHdnNomeIndexacao->setValue ( $stNomeIndexacao );

$obTxtCodAcrescimo = new TextBox;
$obTxtCodAcrescimo->setRotulo  ( 'Código');
$obTxtCodAcrescimo->setTitle   ( 'Código do Acréscimo');
$obTxtCodAcrescimo->setName    ( 'inCodAcrescimo');
$obTxtCodAcrescimo->setValue   ( $request->get("inCodAcrescimo") );
$obTxtCodAcrescimo->setInteiro ( false );
$obTxtCodAcrescimo->setSize    ( 10 );
$obTxtCodAcrescimo->setMaxLength ( 10 );
$obTxtCodAcrescimo->setNull    ( false );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo  ( 'Descrição');
$obTxtDescricao->setTitle   ( 'Descrição do crédito');
$obTxtDescricao->setName    ( 'stDescricao');
$obTxtDescricao->setValue   ( $request->get("stDescricao") );
$obTxtDescricao->setInteiro ( false );
$obTxtDescricao->setSize    ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull    ( false );

if ($stAcao == 'alterar') {
    $obHdnCodNatureza = new Hidden;
    $obHdnCodNatureza->setName  ('inCodNatureza');
    $obHdnCodNatureza->setValue ( $request->get('inCodNatureza') );

    $obHdnCodGenero = new Hidden;
    $obHdnCodGenero->setName  ('inCodGenero');
    $obHdnCodGenero->setValue ( $request->get('inCodGenero') );

    $obHdnCodEspecie = new Hidden;
    $obHdnCodEspecie->setName  ('inCodEspecie');
    $obHdnCodEspecie->setValue ( $request->get('inCodEspecie') );

    $obLblCodNatureza = new Label;
    $obLblCodNatureza->setName   ( 'LabelCodNatureza' );
    $obLblCodNatureza->setTitle  ( 'Natureza' );
    $obLblCodNatureza->setRotulo ( 'Natureza' );
    $obLblCodNatureza->setValue  ( $request->get('inCodNatureza') . ' - ' . $request->get('stNomNatureza') );

    $obLblCodGenero = new Label;
    $obLblCodGenero->setName   ( 'LabelCodGenero' );
    $obLblCodGenero->setTitle  ( 'Gênero' );
    $obLblCodGenero->setRotulo ( 'Gênero' );
    $obLblCodGenero->setValue  ( $request->get('inCodGenero') . ' - ' . $request->get('stNomGenero') );

    $obLblCodEspecie = new Label;
    $obLblCodEspecie->setName   ( 'LabelCodEspecie' );
    $obLblCodEspecie->setTitle  ( 'Espécie' );
    $obLblCodEspecie->setRotulo ( 'Espécie' );
    $obLblCodEspecie->setValue  ( $request->get('inCodEspecie') . ' - ' . $request->get('stNomEspecie') );
} else {
    $obTxtCodNatureza = new TextBox;
    $obTxtCodNatureza->setRotulo  ( 'Natureza ');
    $obTxtCodNatureza->setTitle   ( 'Natureza do Crédito');
    $obTxtCodNatureza->setName    ( 'inCodNatureza');
    $obTxtCodNatureza->setValue   ( $request->get("inCodNatureza") );
    $obTxtCodNatureza->setInteiro ( true );
    $obTxtCodNatureza->setSize    ( 10 );
    $obTxtCodNatureza->setMaxLength ( 10 );
    $obTxtCodNatureza->setNull    ( false );
    $obTxtCodNatureza->obEvento->setOnChange   ( "preencheGenero('');" );

    $obTxtCodGenero = new TextBox;
    $obTxtCodGenero->setRotulo  ( 'Gênero');
    $obTxtCodGenero->setTitle   ( 'Gênero do Crédito');
    $obTxtCodGenero->setName    ( 'inCodGenero');
    $obTxtCodGenero->setValue   ( $request->get("inCodGenero") );
    $obTxtCodGenero->setInteiro ( true );
    $obTxtCodGenero->setSize    ( 10 );
    $obTxtCodGenero->setMaxLength ( 10 );
    $obTxtCodGenero->setNull    ( false );
    $obTxtCodGenero->obEvento->setOnChange   ( "preencheEspecie('');" );

    $obTxtCodEspecie = new TextBox;
    $obTxtCodEspecie->setRotulo  ( 'Espécie');
    $obTxtCodEspecie->setTitle   ( 'Espécie do Crédito');
    $obTxtCodEspecie->setName    ( 'inCodEspecie');
    $obTxtCodEspecie->setValue   ( $request->get("inCodEspecie") );
    $obTxtCodEspecie->setInteiro ( true );
    $obTxtCodEspecie->setSize    ( 10 );
    $obTxtCodEspecie->setMaxLength ( 10 );
    $obTxtCodEspecie->setNull    ( false );

    //------------------------------------------------- COMBOS
    $obRMONCredito->ListarNatureza  ( $rsNatureza );

    $obCmbNatureza = new Select;
    $obCmbNatureza->setRotulo               ( "Natureza"    );
    $obCmbNatureza->setTitle                ( "Natureza do crédito"    );
    $obCmbNatureza->setName                 ( "cmbNatureza"              );
    $obCmbNatureza->addOption               ( "", "Selecione"        );
    $obCmbNatureza->setValue                ( $request->get('inCodNatureza') );
    $obCmbNatureza->setCampoId              ( "cod_natureza"             );
    $obCmbNatureza->setCampoDesc            ( "nom_natureza"             );
    $obCmbNatureza->preencheCombo           ( $rsNatureza                );
    $obCmbNatureza->setNull                 ( false                  );
    $obCmbNatureza->setStyle                ( "width: 220px"         );
    $obCmbNatureza->obEvento->setOnChange   ( "preencheGenero('');" );

    $obCmbGenero = new Select;
    $obCmbGenero->setRotulo       ( "Genero"    );
    $obCmbGenero->setTitle        ( "Genero do crédito"    );
    $obCmbGenero->setName         ( "cmbGenero"              );
    $obCmbGenero->addOption       ( "", "Selecione"        );
    $obCmbGenero->setValue        ( $request->get('inCodGenero') );
    $obCmbGenero->setCampoId      ( "cod_genero"             );
    $obCmbGenero->setCampoDesc    ( "nom_genero"             );
    $obCmbGenero->preencheCombo   ( $rsGenero                );
    $obCmbGenero->setNull         ( false                  );
    $obCmbGenero->setStyle        ( "width: 220px"         );
    $obCmbGenero->obEvento->setOnChange   ( "preencheEspecie('');" );

    $obCmbEspecie = new Select;
    $obCmbEspecie->setRotulo       ( "Espécie"    );
    $obCmbEspecie->setTitle        ( "Espécie do crédito"    );
    $obCmbEspecie->setName         ( "cmbEspecie"              );
    $obCmbEspecie->addOption       ( "", "Selecione"        );
    $obCmbEspecie->setValue        ( $request->get('inCodEspecie') );
    $obCmbEspecie->setCampoId      ( "cod_especie"             );
    $obCmbEspecie->setCampoDesc    ( "nom_especie"             );
    $obCmbEspecie->preencheCombo   ( $rsEspecie                );
    $obCmbEspecie->setNull         ( false                  );
    $obCmbEspecie->setStyle        ( "width: 220px"         );

//--------------------------------------------------------------------//
}

//----------------------------------------------------------//
$obBscConvenio = new BuscaInner;
$obBscConvenio->setRotulo ( "Convênio" );
$obBscConvenio->setTitle  ( "Convênio no qual o crédito estará vinculado." );
$obBscConvenio->obCampoCod->setName   ( "inNumConvenio" );
$obBscConvenio->obCampoCod->setValue  ( $inNumConvenio );
$obBscConvenio->obCampoCod->obEvento->setOnChange("buscaValor('buscaConvenio');");
$obBscConvenio->setFuncaoBusca (
"abrePopUp('".CAM_GT_MON_POPUPS."convenio/FLProcurarConvenio.php','frm','inNumConvenio','campoInner2','','".Sessao::getId()."','800','550');" );
$obBscConvenio->setNull ( false );
$obBscConvenio->obCampoCod->setId( "inNumConvenio" );
$obBscConvenio->setMonitorarCampoCod( true );

$rsConta = new RecordSet;

$obCmbCC = new Select;
$obCmbCC->setRotulo       ( "Conta Corrente" );
$obCmbCC->setTitle        ( "Conta corrente do convênio à qual o crédito estará vinculado." );
$obCmbCC->setName         ( "cmbContaCorrente" );
$obCmbCC->addOption       ( "", "Selecione" );
$obCmbCC->setValue        ( $request->get('inCodConta') );
$obCmbCC->setCampoId      ( "[cod_conta]-[cod_banco]-[cod_agencia]" );
$obCmbCC->setCampoDesc    ( "nom_conta" );
$obCmbCC->preencheCombo   ( $rsConta );
$obCmbCC->setNull         ( false );
$obCmbCC->setStyle        ( "width: 220px" );

$obCmbCarteira = new Select;
$obCmbCarteira->setRotulo       ( "Carteira" );
$obCmbCarteira->setTitle        ( " Carteira do convênio no qual o crédito estará vinculado." );
$obCmbCarteira->setName         ( "cmbCarteira" );
$obCmbCarteira->addOption       ( "", "Selecione" );
$obCmbCarteira->setValue        ( $request->get('inCodCarteira') );
$obCmbCarteira->setCampoId      ( "cod_carteira" );
$obCmbCarteira->setCampoDesc    ( "nom_carteira" );
$obCmbCarteira->preencheCombo   ( $rsCarteira );
$obCmbCarteira->setNull         ( true );
$obCmbCarteira->setStyle        ( "width: 220px" );

$obLblCodCredito = new Label;
$obLblCodCredito->setName   ( 'LabelCodCredito' );
$obLblCodCredito->setTitle  ( 'Código' );
$obLblCodCredito->setRotulo ( 'Código do Crédito' );
$obLblCodCredito->setValue  ( $request->get('inCodCredito') );

$obBscNorma= new BuscaInner;
$obBscNorma->setRotulo ( "*Norma" );
$obBscNorma->setTitle  ( "Fundamentação Legal que normaliza o crédito"  );
$obBscNorma->setId     ( "stNorma"  );
$obBscNorma->obCampoCod->setName   ( "inCodNorma" );
$obBscNorma->obCampoCod->obEvento->setOnChange("buscaValor('buscaNorma');");
$obBscNorma->setFuncaoBusca ( "abrePopUp('".CAM_GA_ADM_POPUPS."../../normas/popups/normas/FLNorma.php','frm','inCodNorma','stNorma','todos','".Sessao::getId()."','800','550');" );
//-------------------------------------------------------- BOTOES
$obBtnIncluirFundamentacao = new Button;
$obBtnIncluirFundamentacao->setName              ( "btnIncluirAcrescimo" );
$obBtnIncluirFundamentacao->setValue             ( "Incluir"             );
$obBtnIncluirFundamentacao->setTipo              ( "button"              );
$obBtnIncluirFundamentacao->obEvento->setOnClick ( "montaParametrosGET('IncluirFundamentacao','inCodNorma,dtVigenciaInicio');" );
$obBtnIncluirFundamentacao->setDisabled          ( false                 );

$obBtnLimparFundamentacao = new Button;
$obBtnLimparFundamentacao->setName               ( "btnLimparAcrescimo"  );
$obBtnLimparFundamentacao->setValue              ( "Limpar"              );
$obBtnLimparFundamentacao->setTipo               ( "button"              );
$obBtnLimparFundamentacao->obEvento->setOnClick  ( "montaParametrosGET('LimparFundamentacao');"  );
$obBtnLimparFundamentacao->setDisabled           ( false                 );

$botoesSpanFundamentacao = array ( $obBtnIncluirFundamentacao , $obBtnLimparFundamentacao );

$obSpnListaFundamentacao = new Span;
$obSpnListaFundamentacao->setID("spnFund");

$obDtVigenciaInicio  = new Data;
$obDtVigenciaInicio->setName ( "dtVigenciaInicio" );
$obDtVigenciaInicio->setRotulo ( "*Vigência" );
$obDtVigenciaInicio->setTitle ( "Data de vigência da fundamentação." );
$obDtVigenciaInicio->setMaxLength ( 20 );
$obDtVigenciaInicio->setSize ( 10 );
$obDtVigenciaInicio->setNull ( true );
//-------------------------------------------------------- BOTOES
$obBtnOK = new Ok;

$obBtnLimpar = new Limpar;
$obBtnLimpar->obEvento->setOnClick  ( "Limpar();" );

$botoesSpanOK = array ( $obBtnOK , $obBtnLimpar );
//-------------------------------------------------------- BOTOES
$obBtnIncluirAcrescimo = new Button;
$obBtnIncluirAcrescimo->setName              ( "btnIncluirAcrescimo" );
$obBtnIncluirAcrescimo->setValue             ( "Incluir"             );
$obBtnIncluirAcrescimo->setTipo              ( "button"              );
$obBtnIncluirAcrescimo->obEvento->setOnClick ( "incluirAcrescimo();" );
$obBtnIncluirAcrescimo->setDisabled          ( false                 );

$obBtnLimparAcrescimo = new Button;
$obBtnLimparAcrescimo->setName               ( "btnLimparAcrescimo"  );
$obBtnLimparAcrescimo->setValue              ( "Limpar"              );
$obBtnLimparAcrescimo->setTipo               ( "button"              );
$obBtnLimparAcrescimo->obEvento->setOnClick  ( "buscaValor('limparAcrescimo');"  );
$obBtnLimparAcrescimo->setDisabled           ( false                 );

$botoesSpanAcrescimo = array ( $obBtnIncluirAcrescimo , $obBtnLimparAcrescimo );

$obSpnListaAcrescimo = new Span;
$obSpnListaAcrescimo->setID("spnListaAcrescimo");

$obSpnIndexacao= new Span;
$obSpnIndexacao->setID("spnIndexacao");

$obRdbIndexacaoIndicador = new Radio;
$obRdbIndexacaoIndicador->setRotulo   ( "Indexação" );
$obRdbIndexacaoIndicador->setName     ( "boIndexacao" );
$obRdbIndexacaoIndicador->setValue    ( "Indicador Economico" );
$obRdbIndexacaoIndicador->setLabel    ( "Indicador Econômico" );
$obRdbIndexacaoIndicador->setNull     ( false );
if ( $boIndexacao == "indicador")
    $obRdbIndexacaoIndicador->setChecked  ( true );

$obRdbIndexacaoIndicador->setTitle    ( "Define se o crédito será indexado pelo Indicador Econômico ou pela Moeda" );
$obRdbIndexacaoIndicador->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');" );

$obRdbIndexacaoMoeda = new Radio;
$obRdbIndexacaoMoeda->setRotulo       ( "Indexação" );
$obRdbIndexacaoMoeda->setName         ( "boIndexacao"   );
$obRdbIndexacaoMoeda->setValue        ( "Moeda" );
$obRdbIndexacaoMoeda->setLabel        ( "Moeda" );
$obRdbIndexacaoMoeda->setNull         ( false   );
if ( $boIndexacao == "moeda")
    $obRdbIndexacaoMoeda->setChecked  ( true );

$obRdbIndexacaoMoeda->setTitle        ( "Define se o crédito será indexado pelo Indicador Econômico ou pela Moeda" );
$obRdbIndexacaoMoeda->obEvento->setOnChange ( "buscaValor('BuscaIndexacao');");

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ("oculto" );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm   ( $obForm );
$obFormulario->setAjuda  ( "UC-05.05.10" );
$obFormulario->addTitulo ('Dados para Crédito');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addHidden ( $obHdnSimboloMoeda );
$obFormulario->addHidden ( $obHdnAbreviatura );

if ($stAcao == "alterar") {
    $obFormulario->addHidden    ( $obHdnCodCredito );
    $obFormulario->addHidden    ( $obHdnCodIndexacao );
    $obFormulario->addHidden    ( $obHdnNomeIndexacao );
    $obFormulario->addHidden    ( $obHdnCodNatureza );
    $obFormulario->addHidden    ( $obHdnCodGenero );
    $obFormulario->addHidden    ( $obHdnCodEspecie );
    $obFormulario->addHidden    ( $obHdnCodConta );

    $obFormulario->addComponente ( $obLblCodCredito );
    $obFormulario->addComponente ( $obLblCodNatureza );
    $obFormulario->addComponente ( $obLblCodGenero );
    $obFormulario->addComponente ( $obLblCodEspecie );
} else {
    $obFormulario->addComponenteComposto  ( $obTxtCodNatureza, $obCmbNatureza );
    $obFormulario->addComponenteComposto  ( $obTxtCodGenero, $obCmbGenero );
    $obFormulario->addComponenteComposto  ( $obTxtCodEspecie, $obCmbEspecie );
}

$obFormulario->addComponente ( $obTxtDescricao );
$obFormulario->addComponente ( $obBscDesoneracao );
$obFormulario->addComponente ( $obBscConvenio );
$obFormulario->addComponente ( $obCmbCC );
$obFormulario->addComponente ( $obCmbCarteira );
//------------- SPAN
$obFormulario->addComponenteComposto ( $obRdbIndexacaoIndicador, $obRdbIndexacaoMoeda );
$obFormulario->addSpan               ( $obSpnIndexacao    );
//--------------
$obFormulario->addTitulo ( 'Fundamentação Legal' );
$obFormulario->addComponente ( $obBscNorma );
$obFormulario->addComponente ( $obDtVigenciaInicio );
$obFormulario->defineBarra   ( $botoesSpanFundamentacao, 'left', '' );
$obFormulario->addSpan       ( $obSpnListaFundamentacao );

$obFormulario->addTitulo ( 'Acréscimos Legais' );

$obPopUpAcrescimo = new IPopUpAcrescimo;
$obPopUpAcrescimo->setNull ( true );
$obPopUpAcrescimo->setRotulo ( "*Acréscimo" );
$obPopUpAcrescimo->setTitle  ( "Acréscimo legal que compõe o crédito"  );
$obPopUpAcrescimo->setCodAcrescimo( $inCodAcrescimo );
$obPopUpAcrescimo->geraFormulario( $obFormulario );

$obFormulario->defineBarra   ( $botoesSpanAcrescimo,'left','' );
$obFormulario->addSpan       ( $obSpnListaAcrescimo     );

if ($stAcao == "incluir") {
    $obFormulario->defineBarra   ( $botoesSpanOK,'left','' );
} else {
    $obFormulario->cancelar();
}

$obFormulario->show();

if ($stAcao == 'alterar') { //funcao para buscar as indexacoes e os acrescimos do credito
     $js = "buscaValor('BuscaDados')";
}

sistemaLegado::executaFrameOculto($js);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
