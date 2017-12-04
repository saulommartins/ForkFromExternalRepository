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
  * Página de Formulario de Configuração de Termos de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: FMManterConfiguracaoParcSubvOSCIP.php 64116 2015-12-03 19:33:49Z evandro $
  * $Revision: 64116 $
  * $Author: evandro $
  * $Date: 2015-12-03 17:33:49 -0200 (Thu, 03 Dec 2015) $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
require_once CAM_GF_ORC_COMPONENTES."ILabelEntidade.class.php";
require_once CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoParcSubvOSCIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;

$stCtrl = $request->get("stCtrl");
$stAcao = $request->get("stAcao");
$stExercicioProcesso = $request->get('stExercicioProcesso', Sessao::getExercicio());

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setId   ("stAcao");
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setId   ("stCtrl");
$obHdnCtrl->setValue($stCtrl);

$obHdnNumeroProcessoAnterior = new Hidden;
$obHdnNumeroProcessoAnterior->setName ("stHdnNumeroProcessoAnterior");
$obHdnNumeroProcessoAnterior->setId   ("stHdnNumeroProcessoAnterior");

$obILabelEntidade = new ILabelEntidade($obFrom);
$obILabelEntidade->setExercicio( $stExercicioProcesso );
$obILabelEntidade->setMostraCodigo( true );
$obILabelEntidade->setCodEntidade( $request->get('inCodEntidade') );

/*
 * Cadastro dos Termos de Parceria/Subvenção/OSCIP
 **/
$obTxtExercicioProcesso = new TextBox;
$obTxtExercicioProcesso->setRotulo   ( "Exercício do Processo");
$obTxtExercicioProcesso->setTitle    ( "Informe o exercício do Processo."	);
$obTxtExercicioProcesso->setName     ( "stExercicioProcesso" );
$obTxtExercicioProcesso->setId       ( "stExercicioProcesso" );
$obTxtExercicioProcesso->setValue    ( $stExercicioProcesso );
$obTxtExercicioProcesso->setInteiro  ( false );
$obTxtExercicioProcesso->setNull     ( false );
$obTxtExercicioProcesso->setMaxLength( 4 );
$obTxtExercicioProcesso->setSize     ( 5 );

$obHdnExercicioProcesso = new hidden;
$obHdnExercicioProcesso->setName  ("hdnExercicioProcesso");
$obHdnExercicioProcesso->setId    ("hdnExercicioProcesso");
$obHdnExercicioProcesso->setValue ($stExercicioProcesso);

$obTxtNumeroProcesso = new TextBox();
$obTxtNumeroProcesso->setRotulo   ("Número Processo");
$obTxtNumeroProcesso->setTitle    ("Informe o número do processo da Subvenção, OSCIP e Termo de Parceria");
$obTxtNumeroProcesso->setId       ('stNumeroProcesso');
$obTxtNumeroProcesso->setName     ('stNumeroProcesso');
$obTxtNumeroProcesso->setNull     (false);
$obTxtNumeroProcesso->setMaxLength(16);
$obTxtNumeroProcesso->setSize     (16);
$obTxtNumeroProcesso->obEvento->setOnBlur(" montaParametrosGET('validaTermoParceria');");

$obDtAssinatura = new Data();
$obDtAssinatura->setRotulo("Data Assinatura");
$obDtAssinatura->setName  ('stDtAssinatura');
$obDtAssinatura->setId    ('stDtAssinatura');
$obDtAssinatura->setNull  (false);
$obDtAssinatura->obEvento->setOnBlur(" if( this.value != ''){ jQuery('#stDtPublicacao').removeProp('disabled'); jQuery('#stDtPublicacao').focus(); } else { jQuery('#stDtPublicacao').prop('disabled', true);}");

$obDtPublicacao = new Data();
$obDtPublicacao->setRotulo  ( "Data Publicação" );
$obDtPublicacao->setName    ( 'stDtPublicacao' );
$obDtPublicacao->setId      ( 'stDtPublicacao' );
$obDtPublicacao->setNull    ( false );
$obDtPublicacao->setDisabled( true );
$obDtPublicacao->obEvento->setOnBlur (" if( this.value != '' && jQuery('stDtAssinatura').val() != '') { montaParametrosGET('validaPeriodicidade', 'stDtAssinatura,stDtPublicacao'); } \n");

$obTxtImprensaOficial = new TextBox();
$obTxtImprensaOficial->setRotulo   ("Imprensa Oficial");
$obTxtImprensaOficial->setName     ("stImprensaOficial");
$obTxtImprensaOficial->setId       ("stImprensaOficial");
$obTxtImprensaOficial->setNull     (false);
$obTxtImprensaOficial->setMaxLength(50);
$obTxtImprensaOficial->setSize     (42);

$obDtInicioTermo = new Data();
$obDtInicioTermo->setRotulo( "Data Início Termo" );
$obDtInicioTermo->setTitle ( "Informe a data de início da vigência do termo." );
$obDtInicioTermo->setName  ( 'stDtInicioTermo' );
$obDtInicioTermo->setId    ( 'stDtInicioTermo' );
$obDtInicioTermo->setNull  ( false );
$obDtInicioTermo->obEvento->setOnBlur(" if( this.value != ''){ jQuery('#stDtTerminoTermo').removeProp('disabled'); jQuery('#stDtTerminoTermo').focus(); } else { jQuery('#stDtTerminoTermo').prop('disabled', true);}");

$obDtTerminoTermo = new Data();
$obDtTerminoTermo->setRotulo  ( "Data Término Termo" );
$obDtTerminoTermo->setTitle   ( "Informe a data do término da vigência do termo." );
$obDtTerminoTermo->setName    ( 'stDtTerminoTermo' );
$obDtTerminoTermo->setId      ( 'stDtTerminoTermo' );
$obDtTerminoTermo->setNull    ( false );
$obDtTerminoTermo->setDisabled( true );
$obDtTerminoTermo->obEvento->setOnBlur(" if(this.value != '' && jQuery('stDtInicioTermo').val() != '') { montaParametrosGET('validaPeriodicidade', 'stDtInicioTermo,stDtTerminoTermo'); } \n");

//CGM fornecedor
$obBscCGMParceria = new IPopUpCGMVinculado($obForm);
$obBscCGMParceria->setTabelaVinculo   ('sw_cgm_pessoa_juridica');
$obBscCGMParceria->setCampoVinculo    ('numcgm');
$obBscCGMParceria->setRotulo          ('CGM da OSCIP/Termo Parceria');
$obBscCGMParceria->setName            ('stNomParceria');
$obBscCGMParceria->setId              ('stNomParceria');
$obBscCGMParceria->obCampoCod->setName("inCGMParceria");
$obBscCGMParceria->obCampoCod->setId  ("inCGMParceria");
$obBscCGMParceria->obCampoCod->setNull(false);
$obBscCGMParceria->setNull            (false);

$obTxtObjeto = new TextArea();
$obTxtObjeto->setRotulo       ("Objeto");
$obTxtObjeto->setName         ("txtObjeto");
$obTxtObjeto->setId           ("txtObjeto");
$obTxtObjeto->setNull         (false);
$obTxtObjeto->setMaxCaracteres(400);

$obTxtProcessoMJ = new TextBox();
$obTxtProcessoMJ->setRotulo   ("Processo da OSCIP no M. Justiça");
$obTxtProcessoMJ->setTitle    ("Informe o número do processo de concessão do título de OSCIP pelo Ministério da Justiça.");
$obTxtProcessoMJ->setName     ("stProcessoMJ");
$obTxtProcessoMJ->setId       ("stProcessoMJ");
$obTxtProcessoMJ->setMaxLength(36);
$obTxtProcessoMJ->setSize     (30);

$obDtProcessoMJ = new Data();
$obDtProcessoMJ->setRotulo("Data Processo no M. Justiça");
$obDtProcessoMJ->setName  ("dtProcessoMJ");
$obDtProcessoMJ->setId    ("dtProcessoMJ");

$obDtPublicacaoMJ = new Data();
$obDtPublicacaoMJ->setRotulo("Data Publicacao no M. Justiça");
$obDtPublicacaoMJ->setName  ("dtPublicacaoMJ");
$obDtPublicacaoMJ->setId    ("dtPublicacaoMJ");

/*
 *
 * Cadastro das Licitações
 **/
$obTxtProcessoLicitatorio = new TextBox();
$obTxtProcessoLicitatorio->setRotulo      ("Processo Licitatório");
$obTxtProcessoLicitatorio->setTitle       ("Preencher, quando for o caso, o Processo Licitatório feito para escolha da OSCIP parceria no termo sendo registrado.");
$obTxtProcessoLicitatorio->setName        ("stProcessoLicitatorio");
$obTxtProcessoLicitatorio->setId          ("stProcessoLicitatorio");
$obTxtProcessoLicitatorio->setMaxLength   (36);
$obTxtProcessoLicitatorio->setSize        (36);
$obTxtProcessoLicitatorio->setAlfaNumerico(true);

$obTxtProcessoDispensa = new TextBox();
$obTxtProcessoDispensa->setRotulo      ("Processo de dispensa");
$obTxtProcessoDispensa->setTitle       ("Preencher, quando for o caso, o Processo de Dispensa feito para a escolha da OSCIP parceria no termo sendo registrado.");
$obTxtProcessoDispensa->setName        ("stProcessoDispensa");
$obTxtProcessoDispensa->setId          ("stProcessoDispensa");
$obTxtProcessoDispensa->setMaxLength   (16);
$obTxtProcessoDispensa->setSize        (16);
$obTxtProcessoDispensa->setAlfaNumerico(true);
/*****************************
 * Cadastro dos Recursos Financeiros
 *****************************
 */
$obVlParceiroPublico = new Moeda();
$obVlParceiroPublico->setRotulo   ("Valor do Parceiro");
$obVlParceiroPublico->setTitle    ("Informe o valor dos recursos financeiros sob a responsabilidade do parceiro.");
$obVlParceiroPublico->setName     ("vlParceiroPublico");
$obVlParceiroPublico->setId       ("vlParceiroPublico");
$obVlParceiroPublico->setMaxLength(21);
$obVlParceiroPublico->setSize     (21);

$obVlParceiroOSCIP = new Moeda();
$obVlParceiroOSCIP->setRotulo   ("Valor da Subvenção/OSCIP /Termo Parceria");
$obVlParceiroOSCIP->setTitle    ("Informe o valor dos recursos financeiros sob a responsabilidade da OSCIP.");
$obVlParceiroOSCIP->setName     ("vlParceiroOSCIP");
$obVlParceiroOSCIP->setId       ("vlParceiroOSCIP");
$obVlParceiroOSCIP->setMaxLength(21);
$obVlParceiroOSCIP->setSize     (21);

/******************************
 * Cadastro das Dotações para os Termos de Parceria/Subvenção/OSCIP
 ******************************
 **/
// Define Objeto BuscaInner para Despesa
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo               ("Dotação Orçamentária");
$obBscDespesa->setTitle                ("Dotação referente aos recursos financeiros sob a responsabilidade do parceiro público, a dotação a ser usada para pagamento das despesas do termo.");
$obBscDespesa->setId                   ("stNomDespesa");
$obBscDespesa->setNullBarra            (false);
$obBscDespesa->setObrigatorioBarra     (true);
$obBscDespesa->obCampoCod->setName     ("inCodDespesa");
$obBscDespesa->obCampoCod->setSize     (10);
$obBscDespesa->obCampoCod->setMaxLength(5);
$obBscDespesa->obCampoCod->setAlign    ("left");
$obBscDespesa->setFuncaoBusca          ("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','','".Sessao::getId()."','800','550');");
$obBscDespesa->setValoresBusca         ( CAM_GF_ORC_POPUPS."despesa/OCDespesa.php?".Sessao::getId(), $obForm->getName(), '');

$obSpnDotacoes = new Span();
$obSpnDotacoes->setId("spnDotacoes");

$obSpnListaTermosCadastrados = new Span();
$obSpnListaTermosCadastrados->setId('spnListaTermosCadastrados');

$obBtnOK = new OK(true);
$obBtnLimpar = new Button();
$obBtnLimpar->setName("btnLimpar");
$obBtnLimpar->setId("btnLimpar");
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->obEvento->setOnClick("LimparFormulario(); \n");

$arBarraOkLimpar = array($obBtnOK,$obBtnLimpar);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnExercicioProcesso );
$obFormulario->addHidden     ( $obHdnNumeroProcessoAnterior );
$obFormulario->addTitulo     ( "Dados para Termos de Parceria/Subvenção/OSCIP" );
$obILabelEntidade->geraFormulario( $obFormulario );
$obFormulario->addComponente ( $obTxtExercicioProcesso );
$obFormulario->addComponente ( $obTxtNumeroProcesso );
$obFormulario->addComponente ( $obDtAssinatura );
$obFormulario->addComponente ( $obDtPublicacao );
$obFormulario->addComponente ( $obTxtImprensaOficial );
$obFormulario->addComponente ( $obDtInicioTermo );
$obFormulario->addComponente ( $obDtTerminoTermo );
$obFormulario->addComponente ( $obBscCGMParceria );
$obFormulario->addComponente ( $obTxtObjeto );
$obFormulario->addComponente ( $obTxtProcessoMJ );
$obFormulario->addComponente ( $obDtProcessoMJ );
$obFormulario->addComponente ( $obDtPublicacaoMJ );
$obFormulario->addTitulo     ( "Dados para Configuração de Licitações" );
$obFormulario->addComponente ( $obTxtProcessoLicitatorio );
$obFormulario->addComponente ( $obTxtProcessoDispensa );
$obFormulario->addTitulo     ( "Dados para Configuração de Recursos Financeiros" );
$obFormulario->addComponente ( $obVlParceiroPublico );
$obFormulario->addComponente ( $obVlParceiroOSCIP );
$obFormulario->addTitulo     ( "Dados para Configuração de Dotações" );
$obFormulario->addComponente ( $obBscDespesa );
$obFormulario->Incluir       ( 'Dotacoes', array( $obBscDespesa,$obBscDespesa->obCampoCod ), true, false, '',true);
$obFormulario->addSpan       ( $obSpnDotacoes );
$obFormulario->defineBarra   ( $arBarraOkLimpar );
$obFormulario->addSpan       ( $obSpnListaTermosCadastrados );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>