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
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentaria.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentariaResponsavel.class.php");
include_once( CAM_GA_NORMAS_COMPONENTES."IBuscaInnerNorma.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNNaturezaJuridica.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNFuncaoGestor.class.php" );
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
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

list($num_unidade, $num_orgao) = explode("/", $_REQUEST['stUnidade']);

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

$obTUnidade = new TOrcamentoUnidade;
$obTUnidade->recuperaTodos($rsUnidade, " WHERE num_unidade = ".$num_unidade." AND num_orgao = ".$num_orgao." AND exercicio = '".Sessao::getExercicio()."'");

$obTUnidadeOrcamentaria = new TTCERNUnidadeOrcamentaria;
$obTUnidadeOrcamentaria->recuperaRelacionamento($rsOrcamentaria, " WHERE unidade.num_unidade = ".$num_unidade." AND unidade.num_orgao = ".$num_orgao." AND unidade.exercicio = '".Sessao::getExercicio()."'");

$obLblUnidade = new Label;
$obLblUnidade->setRotulo   ("Unidade");
$obLblUnidade->setName     ("stNomeUnidade");
$obLblUnidade->setId       ("stNomeUnidade");
$obLblUnidade->setValue    ($rsUnidade->getCampo('nom_unidade'));

$obHdnNumUnidade = new Hidden;
$obHdnNumUnidade->setName ("hdnNumUnidade");
$obHdnNumUnidade->setId   ("hdnNumUnidade");
$obHdnNumUnidade->setValue($rsUnidade->getCampo('num_unidade'));

$obHdnNumOrgao = new Hidden;
$obHdnNumOrgao->setName ("hdnNumOrgao");
$obHdnNumOrgao->setId   ("hdnNumOrgao");
$obHdnNumOrgao->setValue($rsUnidade->getCampo('num_orgao'));

$obHdnIdOrcamentaria = new Hidden;
$obHdnIdOrcamentaria->setName ("HdnIdOrcamentaria");
$obHdnIdOrcamentaria->setId   ("HdnIdOrcamentaria");
$obHdnIdOrcamentaria->setValue($rsOrcamentaria->getCampo('id'));

$obIPopUpCGMUnidade = new IPopUpCGM($obForm);
$obIPopUpCGMUnidade->setTipo             ( "juridica" );
$obIPopUpCGMUnidade->setTitle            ( "Informe o CGM relacionado a Unidade Orçamentária." );
$obIPopUpCGMUnidade->setObrigatorioBarra ( true );
$obIPopUpCGMUnidade->setNull             ( true );
$obIPopUpCGMUnidade->setRotulo           ("CGM da Unidade");
$obIPopUpCGMUnidade->setName             ( 'stCGMUnidade' );
$obIPopUpCGMUnidade->setId               ( 'stCGMUnidade' );
$obIPopUpCGMUnidade->obCampoCod->setName( 'inNumCGMUnidade' );
$obIPopUpCGMUnidade->obCampoCod->setId  ( 'inNumCGMUnidade' );
$obIPopUpCGMUnidade->obCampoCod->setValue  ( $rsOrcamentaria->getCampo('numcgm') );
$obIPopUpCGMUnidade->setValue  ( $rsOrcamentaria->getCampo('nom_cgm') );

$obSpnTipoMembroUnidade = new Span;
$obSpnTipoMembroUnidade->setID ( 'spnTipoMembroUnidade' );

$obHdnIdGestora = new Hidden();
$obHdnIdGestora->setName('hdnIdGestora');
$obHdnIdGestora->setId('hdnIdGestora');
$obHdnIdGestora->setValue($_REQUEST['hdnIdGestora']);

$obHdnIdUnidade = new Hidden();
$obHdnIdUnidade->setId ('hdnIdUnidade');
$obHdnIdUnidade->setName('hdnIdUnidade');

$obTxtInstitucional = new TextBox;
$obTxtInstitucional->setRotulo   ("Codigo Institucional");
$obTxtInstitucional->setName     ("stInstitucional");
$obTxtInstitucional->setId       ("stInstitucional");
$obTxtInstitucional->setSize     (10);
$obTxtInstitucional->setMaxLength(10);
$obTxtInstitucional->setInteiro  (true);
$obTxtInstitucional->setNull     (false);
$obTxtInstitucional->obEvento->setOnBlur( "if (this.value != '') { montaParametrosGET('buscaCodInstitucional'); }" );
$obTxtInstitucional->setValue    ($rsOrcamentaria->getCampo('cod_institucional'));

$obCmbSituacao = new Select;
$obCmbSituacao->setName                  ("stSituacao");
$obCmbSituacao->setId                    ("stSituacao");
$obCmbSituacao->setValue                 ($rsOrcamentaria->getCampo('situacao'));
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
$obHdnCodNorma->setValue ($rsOrcamentaria->getCampo('cod_norma'));

$jsOnLoad = "montaParametrosGET( 'preencheDados', 'hdnCodNorma,hdnNumUnidade,hdnNumOrgao' );";

// FORMULARIO para Responsável pela Unidade

$obIPopUpCGM = new IPopUpCGM($obForm);
$obIPopUpCGM->setTipo             ( "fisica" );
$obIPopUpCGM->setTitle            ( "Informe o CGM relacionado ao Responsável da Unidade." );
$obIPopUpCGM->setObrigatorioBarra ( true );
$obIPopUpCGM->setNull             ( true );
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

f//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnNumUnidade );
$obFormulario->addHidden( $obHdnNumOrgao );
$obFormulario->addHidden( $obHdnIdOrcamentaria );
$obFormulario->addHidden( $obHdnIdGestora );
$obFormulario->addHidden( $obHdnIdUnidade );
$obFormulario->addSpan( $obSpnTipoMembroUnidade );

$obFormulario->addTitulo( "Unidade Orçamentária" );
$obFormulario->addComponente( $obLblUnidade );
$obFormulario->addComponente( $obIPopUpCGMUnidade );
$obFormulario->addComponente( $obTxtInstitucional );
$obFormulario->addComponente( $obCmbSituacao );

$obTipoNormaNorma->geraFormulario($obFormulario);
$obFormulario->addHidden( $obHdnCodNorma );

$obFormulario->addTitulo( "Responsável" );
$obFormulario->addComponente( $obIPopUpCGM );

$obFormulario->addComponente( $obTxtCargo );
$obFormulario->addComponente( $obCmbFuncao );
$obFormulario->addComponente( $obDtIniFuncao );
$obFormulario->addComponente( $obDtFimFuncao );

$obFormulario->addHidden( $obHdnNomCGM );
$obFormulario->addHidden( $obSpnTipoMembro );
$obFormulario->addHidden( $obHdnId );

$obFormulario->defineBarra  ( array( $obBtOk, $obBtLimpar ) );
$obFormulario->addSpan( $obSpnGestor );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
