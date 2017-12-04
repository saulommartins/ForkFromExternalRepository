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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeGestora.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeGestoraResponsavel.class.php");
include_once( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNNaturezaJuridica.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNFuncaoGestor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeGestora";
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
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;
Sessao::write('arResponsavel', array());

if ($inCodigo) {
    $stLocation .= "&inCodigo=$inCodigo";
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
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

$obTGestora = new TTCERNUnidadeGestora;
$obTGestora->recuperaRelacionamento($rsGestora);

$obLblEntidade = new Label;
$obLblEntidade->setRotulo   ("Entidade");
$obLblEntidade->setName     ("stNomeEntidade");
$obLblEntidade->setId       ("stNomeEntidade");
$obLblEntidade->setValue    ($rsGestora->getCampo('nom_cgm'));

$obLblCodEntidade = new Label;
$obLblCodEntidade->setRotulo   ("Código da Entidade");
$obLblCodEntidade->setName     ("stEntidade");
$obLblCodEntidade->setId       ("stEntidade");
$obLblCodEntidade->setValue    ($rsGestora->getCampo('cod_entidade'));

$obHdnCgmUnidade = new Hidden;
$obHdnCgmUnidade->setName ("hdnCgmUnidade");
$obHdnCgmUnidade->setId   ("hdnCgmUnidade");
$obHdnCgmUnidade->setValue($rsGestora->getCampo('numcgm'));

$obHdnIdUnidade = new Hidden;
$obHdnIdUnidade->setName ("hdnIdUnidade");
$obHdnIdUnidade->setId   ("hdnIdUnidade");
$obHdnIdUnidade->setValue($rsGestora->getCampo('id'));

/*$obLblOrgao = new Label;
$obLblOrgao->setRotulo   ("Código do Órgão - TCE");
$obLblOrgao->setName     ("stOrgao");
$obLblOrgao->setId       ("stOrgao");
$obLblOrgao->setValue    ($rsGestora->getCampo('cod_orgao'));*/

$obTxtInstitucional = new TextBox;
$obTxtInstitucional->setRotulo   ("Codigo Institucional");
$obTxtInstitucional->setName     ("stInstitucional");
$obTxtInstitucional->setId       ("stInstitucional");
$obTxtInstitucional->setSize     (10);
$obTxtInstitucional->setMaxLength(10);
$obTxtInstitucional->setInteiro  (true);
$obTxtInstitucional->setNull     (false);
$obTxtInstitucional->setValue    ($rsGestora->getCampo('cod_institucional'));

$obCmbPersonalidade = new Select;
$obCmbPersonalidade->setName                  ("stPersonalidade");
$obCmbPersonalidade->setId                    ("stPersonalidade");
$obCmbPersonalidade->setValue                 ($rsGestora->getCampo('personalidade'));
$obCmbPersonalidade->setRotulo                ("Personalidade");
$obCmbPersonalidade->setTitle                 ("Informe a Personalidade");
$obCmbPersonalidade->setNull                  (false);
$obCmbPersonalidade->addOption                ("", "Selecione");
$obCmbPersonalidade->addOption                (1, "Direito Público");
$obCmbPersonalidade->addOption                (2, "Direito Privado");
$obCmbPersonalidade->setStyle                 ("width: 110px");

$obCmbAdministracao = new Select;
$obCmbAdministracao->setName                  ("stAdministracao");
$obCmbAdministracao->setId                    ("stAdministracao");
$obCmbAdministracao->setValue                 ($rsGestora->getCampo('administracao'));
$obCmbAdministracao->setRotulo                ("Administração");
$obCmbAdministracao->setTitle                 ("Informe a Administração");
$obCmbAdministracao->setNull                  (false);
$obCmbAdministracao->addOption                ("", "Selecione" );
$obCmbAdministracao->addOption                (1, "Administração Direta");
$obCmbAdministracao->addOption                (2, "Administração Indireta");
$obCmbAdministracao->setStyle                 ("width: 150px");

$obTNatureza = new TTCERNNaturezaJuridica;
$obTNatureza->recuperaTodos($rsNatureza, "", " ORDER BY descricao");
$obCmbNatureza = new Select;
$obCmbNatureza->setName                  ("stNatureza");
$obCmbNatureza->setId                    ("stNatureza");
$obCmbNatureza->setValue                 ($rsGestora->getCampo('cod_natureza'));
$obCmbNatureza->setRotulo                ("Natureza");
$obCmbNatureza->setTitle                 ("Informe a natureza");
$obCmbNatureza->setNull                  (false);
$obCmbNatureza->setCampoId               ("[cod_natureza]");
$obCmbNatureza->setCampoDesc             ("descricao");
$obCmbNatureza->addOption                ("", "Selecione");
$obCmbNatureza->preencheCombo            ($rsNatureza);
$obCmbNatureza->setStyle                 ("width: 153px");

$obCmbSituacao = new Select;
$obCmbSituacao->setName                  ("stSituacao");
$obCmbSituacao->setId                    ("stSituacao");
$obCmbSituacao->setValue                 ($rsGestora->getCampo('situacao'));
$obCmbSituacao->setRotulo                ("Situacao");
$obCmbSituacao->setTitle                 ("Informe a Situacao");
$obCmbSituacao->setNull                  (false);
$obCmbSituacao->addOption                ("", "Selecione");
$obCmbSituacao->addOption                (1, "Ativa");
$obCmbSituacao->addOption                (2, "Inativa");
$obCmbSituacao->setStyle                 ("width: 90px");

//Componente para inserir a Norma
$obTipoNormaNorma = new IBuscaInnerNorma(false,false);
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obSelect->setDisabled(true);
$obTipoNormaNorma->obITextBoxSelectTipoNorma->obTextBox->setReadOnly(true);
$obTipoNormaNorma->obBscNorma->setRotulo("Norma Regulamentadora");

$obHdnCodNorma = new Hidden();
$obHdnCodNorma->setId ('hdnCodNorma');
$obHdnCodNorma->setName ('hdnCodNorma');
$obHdnCodNorma->setValue ($rsGestora->getCampo('cod_norma'));

$jsOnLoad = "montaParametrosGET( 'preencheDados', 'hdnCodNorma' );";

// FORMULARIO para Responsável pela Unidade

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setTipo             ( "fisica" );
$obIPopUpCGM->setTitle            ( "Informe o CGM relacionado ao Responsável da Unidade." );
$obIPopUpCGM->setObrigatorioBarra ( true );
$obIPopUpCGM->setNull             ( true );
//$obIPopUpCGM->setName             ( 'stNomCGM' );
//$obIPopUpCGM->setId             ( 'stNomCGM' );
$obIPopUpCGM->obCampoCod->setName( 'inNumCGM' );
$obIPopUpCGM->obCampoCod->setId  ( 'inNumCGM' );

$obSpnTipoMembro = new Span;
$obSpnTipoMembro->setID ( 'spnTipoMembro' );

$obHdnNomCGM = new Hidden();
$obHdnNomCGM->setName('hdnNomCGM');

$obHdnId = new Hidden();
$obHdnId->setId ('hdnId');
$obHdnId->setName('hdnId');

$obTxtCargo = new TextBox;
$obTxtCargo->setRotulo   ("Cargo");
$obTxtCargo->setName     ("stCargo");
$obTxtCargo->setId       ("stCargo");
$obTxtCargo->setSize     (30);
$obTxtCargo->setMaxLength(30);
$obTxtCargo->setInteiro  (false);
$obTxtCargo->setNull     (true);

$obTFuncaoResponsavel = new TTCERNFuncaoGestor;
$obTFuncaoResponsavel->recuperaTodos($rsFuncao);
$obCmbFuncao = new Select;
$obCmbFuncao->setName                  ("stFuncao");
$obCmbFuncao->setId                    ("stFuncao");
$obCmbFuncao->setRotulo                ("Função");
$obCmbFuncao->setTitle                 ("Informe a Função do Responsavel");
$obCmbFuncao->setNull                  (true);
$obCmbFuncao->setCampoId               ("[cod_funcao]");
$obCmbFuncao->setCampoDesc             ("descricao");
$obCmbFuncao->addOption                ("", "Selecione");
$obCmbFuncao->preencheCombo            ($rsFuncao);
$obCmbFuncao->setStyle                 ("width: 205px");

$obDtIniFuncao = new Data;
$obDtIniFuncao->setRotulo   ("Data de Início da Função");
$obDtIniFuncao->setName     ("stDtInicio");
$obDtIniFuncao->setId       ("stDtInicio");
$obDtIniFuncao->setSize     (8);
$obDtIniFuncao->setMaxLength(10);

$obDtFimFuncao = new Data;
$obDtFimFuncao->setRotulo   ("Data de Término da Função");
$obDtFimFuncao->setName     ("stDtFim");
$obDtFimFuncao->setId       ("stDtFim");
$obDtFimFuncao->setSize     (8);
$obDtFimFuncao->setMaxLength(10);

$obBtOk = new Button();
$obBtOk->setValue( 'Incluir' );
$obBtOk->setId( 'btIncluir' );
$obBtOk->obEvento->setOnCLick( "montaParametrosGET( 'incluiResponsavel', 'inNumCGM,stNomCGM,stCargo,stFuncao,stDtInicio,stDtFim' );" );

$obBtLimpar = new Button();
$obBtLimpar->setValue( 'Limpar' );
$obBtLimpar->obEvento->setOnClick( "limparResponsavel();");

$obSpnGestor = new Span();
$obSpnGestor->setId( 'spnResponsavel' );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCgmUnidade );
$obFormulario->addHidden( $obHdnIdUnidade );

$obFormulario->addTitulo( "Unidade Gestora" );
$obFormulario->addComponente( $obLblEntidade );
$obFormulario->addComponente( $obLblCodEntidade );
//$obFormulario->addComponente( $obLblOrgao );
$obFormulario->addComponente( $obTxtInstitucional );
$obFormulario->addComponente( $obCmbPersonalidade );
$obFormulario->addComponente( $obCmbAdministracao );
$obFormulario->addComponente( $obCmbNatureza );
$obFormulario->addComponente( $obCmbSituacao );
$obTipoNormaNorma->geraFormulario($obFormulario);
$obFormulario->addHidden( $obHdnCodNorma );

$obFormulario->addTitulo( "Responsável" );
$obFormulario->addComponente( $obIPopUpCGM );
$obFormulario->addHidden( $obHdnNomCGM );
$obFormulario->addComponente( $obTxtCargo );
$obFormulario->addComponente( $obCmbFuncao );
$obFormulario->addComponente( $obDtIniFuncao );
$obFormulario->addComponente( $obDtFimFuncao );

$obFormulario->addHidden( $obHdnId );
$obFormulario->defineBarra  ( array( $obBtOk, $obBtLimpar ) );
$obFormulario->addSpan( $obSpnGestor );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
