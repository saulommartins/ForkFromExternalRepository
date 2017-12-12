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
    * Página de Formulario de Inclusao/Alteracao
    * Data de Criação   : 20/11/2006

    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 32095 $
    $Name$
    $Autor: $
    $Date: 2007-10-23 15:35:44 -0200 (Ter, 23 Out 2007) $

    $Id: FLDespesa2.php 60900 2014-11-21 17:39:56Z michel $

    * Casos de uso: uc-02.01.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrograma.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Despesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

//Recupera Mascara da Classificao de Despesa
$stMascClassificacaoDespesa = $obROrcamentoClassificacaoDespesa->recuperaMascara();

//Definições das funções de formulário
$stFncJavaScript  = " function buscaValor() { \n";
$stFncJavaScript .= "     document.frm.target = 'oculto'; \n";
$stFncJavaScript .= "     document.frm.stCtrl.value = 'mascaraClassificacao2'; \n";
$stFncJavaScript .= "     document.frm.action = '".$pgOcul."?".Sessao::getId()."'; \n";
$stFncJavaScript .= "     document.frm.submit(); \n";
$stFncJavaScript .= "     document.frm.target = ''; \n";
$stFncJavaScript .= "     document.frm.action = '".$pgList."?".Sessao::getId()."'; \n";
$stFncJavaScript .= " } \n";

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtroPopUp');
Sessao::remove('link');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
//$obForm->setTarget( "telaPrincipal" );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "tipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $request->get('stAcao') );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $request->get('stCtrl') );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST['nomForm'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST['campoNum'] );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST['campoNom'] );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName( "inCodEntidade" );

if (is_array($_REQUEST["inCodEntidade"])) {
    $obHdnEntidade->setValue( implode(',', $_REQUEST["inCodEntidade"]) );
} else {
    $obHdnEntidade->setValue( $_REQUEST["inCodEntidade"] );
}

Sessao::write('inCodEntidade',$_REQUEST['inCodEntidade']);

$obHdnCentroCusto = new Hidden;
$obHdnCentroCusto->setName("inCodCentroCusto");
$obHdnCentroCusto->setValue( $_REQUEST["inCodCentroCusto"] );

Sessao::write('inCodCentroCusto',$_REQUEST['inCodCentroCusto']);

//Monta combo para seleção de ORGÃO ORCAMENTARIO
$obROrcamentoOrgao = new ROrcamentoOrgaoOrcamentario;
$obROrcamentoOrgao->listar( $rsOrgao, "ORDER BY num_orgao" );

$obCmbOrgao = new TextBoxSelect;
$obCmbOrgao->setName      ( 'inNumOrgao'          );
$obCmbOrgao->setRotulo    ( 'Orgão'               );
$obCmbOrgao->setNull ( true );
$obCmbOrgao->obSelect->setStyle     ( "width: 400px"        );
$obCmbOrgao->setTitle ( "Selecione o orgão orçamentário." );
$obCmbOrgao->obTextBox->setId       ( 'inNumOrgaoTxt'        );
$obCmbOrgao->obTextBox->setName     ( 'inNumOrgaoTxt'        );
$obCmbOrgao->obTextBox->obEvento->setOnChange("montaParametrosGET('buscaValoresUnidade');" );
$obCmbOrgao->obSelect->setId        ( 'inNumOrgao'        );
$obCmbOrgao->obSelect->setName      ( 'inNumOrgao'        );
$obCmbOrgao->obSelect->setCampoId   ( "num_orgao"   );
$obCmbOrgao->obSelect->setCampoDesc ( "nom_orgao" );
$obCmbOrgao->obSelect->addOption    ( "", "Selecione"       );
$obCmbOrgao->obSelect->obEvento->setOnChange("montaParametrosGET('buscaValoresUnidade');" );
$obCmbOrgao->obSelect->preencheCombo( $rsOrgao );

//Monta combo para seleção de UNIDADE ORCAMENTARIA
$obCmbUnidade = new TextBoxSelect;
$obCmbUnidade->setTitle ( "Selecione a unidade orçamentária." );
$obCmbUnidade->setName      ( 'inNumUnidade'          );
$obCmbUnidade->setValue     ( ''                      );
$obCmbUnidade->setRotulo    ( 'Unidade'               );
$obCmbUnidade->obSelect->setStyle     ( "width: 400px"          );
$obCmbUnidade->obTextBox->setId       ( 'inNumUnidadeTxt'        );
$obCmbUnidade->obTextBox->setName     ( 'inNumUnidadeTxt'        );
$obCmbUnidade->obSelect->setId        ( 'inNumUnidade'        );
$obCmbUnidade->obSelect->setName      ( 'inNumUnidade'        );
//$obCmbUnidade->obSelect->setCampoId   ( "num_unidade"           );
//$obCmbUnidade->obSelect->setCampoDesc ( "[num_orgao].[num_unidade] - [nom_nom_unidade]" );
$obCmbUnidade->obSelect->addOption    ( "", "Selecione"         );

//Monta combo para seleção de PROGRAMA
$obROrcamentoPrograma = new ROrcamentoPrograma;
$obROrcamentoPrograma->listarSemMascara( $rsPrograma,  " ORDER BY cod_programa" );
$obCmbPrograma = new TextBoxSelect;
$obCmbPrograma->setTitle ( "Selecione o programa." );
$obCmbPrograma->setName      ( 'inCodPrograma'        );
$obCmbPrograma->setValue     ( ''                     );
$obCmbPrograma->setRotulo    ( 'Programa'             );
$obCmbPrograma->obTextBox->setId       ( 'inCodProgramaTxt'        );
$obCmbPrograma->obTextBox->setName     ( 'inCodProgramaTxt'        );
$obCmbPrograma->obSelect->setId        ( 'inCodPrograma'        );
$obCmbPrograma->obSelect->setName      ( 'inCodPrograma'        );
$obCmbPrograma->obSelect->setStyle     ( "width: 400px"         );
$obCmbPrograma->obSelect->setCampoId   ( "cod_programa"         );
$obCmbPrograma->obSelect->setCampoDesc ( "descricao"            );
$obCmbPrograma->obSelect->addOption    ( "", "Selecione"        );
$obCmbPrograma->obSelect->preencheCombo( $rsPrograma );

//Monta combo para seleção de PROJETO, ATIVIDADE OU OPERAÇÕES ('sw_pao')
$obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
$obROrcamentoProjetoAtividade->listarSemMascara( $rsPAO, "ORDER BY num_pao" );
$obCmbPAO = new TextBoxSelect;
$obCmbPAO->setTitle ( "Selecione o PAO." );
$obCmbPAO->setName      ( 'inCodPAO'                        );
$obCmbPAO->setValue     ( ''            );
$obCmbPAO->setRotulo    ( 'Projeto, Atividade ou Operações' );
$obCmbPAO->obSelect->setStyle     ( "width: 400px"                    );
$obCmbPAO->obTextBox->setId       ( 'inCodPAOTxt'        );
$obCmbPAO->obTextBox->setName     ( 'inCodPAOTxt'        );
$obCmbPAO->obSelect->setId        ( 'inCodPAO'        );
$obCmbPAO->obSelect->setName      ( 'inCodPAO'        );
$obCmbPAO->obSelect->setCampoId   ( "num_acao"                        );
$obCmbPAO->obSelect->setCampoDesc ( "nom_pao"                         );
$obCmbPAO->obSelect->addOption    ( "", "Selecione"                   );
$obCmbPAO->obSelect->preencheCombo( $rsPAO );

$obTxtElementoDespesa = new TextBox;
$obTxtElementoDespesa->setRotulo   ( "Elemento de Despesa" );
$obTxtElementoDespesa->setTitle    ( "Informe o elemento de despesa para filtro" );
$obTxtElementoDespesa->setName     ( "stMascClassificacaoDespesa" );
$obTxtElementoDespesa->setSize     ( strlen($stMascClassificacaoDespesa) );
$obTxtElementoDespesa->setMaxLength( strlen($stMascClassificacaoDespesa) );
$obTxtElementoDespesa->setValue    ( '' );
$obTxtElementoDespesa->setAlign    ("left");
$obTxtElementoDespesa->obEvento->setOnFocus("selecionaValorCampo( this );");
$obTxtElementoDespesa->obEvento->setOnKeyUp("mascaraDinamico('".$stMascClassificacaoDespesa."', this, event);");
$obTxtElementoDespesa->obEvento->setOnBlur ("if (this.value!='') { montaParametrosGET('mascaraClassificacao2'); }");

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascClassificacaoDespesa );

//Define o objeto TEXT para armazenar a DESCRICAO DO ORGAO
$obTxtDescDespesa = new TextBox;
$obTxtDescDespesa->setName     ( "stDescricao" );
$obTxtDescDespesa->setRotulo   ( "Descrição" );
$obTxtDescDespesa->setSize     ( 80 );
$obTxtDescDespesa->setMaxLength( 80 );
$obTxtDescDespesa->setNull     ( true );
$obTxtDescDespesa->setTitle    ( 'Informe uma descrição' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );
$obFormulario->addHidden( $obHdnEntidade          );
$obFormulario->addHidden( $obHdnCentroCusto       );
$obFormulario->addHidden( $obHdnForm              );
$obFormulario->addHidden( $obHdnCampoNum          );
$obFormulario->addHidden( $obHdnCampoNom          );

$obFormulario->addTitulo( "Dados para filtro de Despesa" );
$obFormulario->addComponente( $obCmbOrgao );
$obFormulario->addComponente( $obCmbUnidade );
$obFormulario->addComponente( $obCmbPrograma );
$obFormulario->addComponente( $obCmbPAO );
$obFormulario->addComponente( $obTxtElementoDespesa );
$obFormulario->addHidden    ( $obHdnMascClassificacao );
//$obFormulario->addComponente( $obTxtCodDespesa   );
//$obFormulario->addComponente( $obTxtCodClassificacao   );
$obFormulario->addComponente( $obTxtDescDespesa  );
$obFormulario->addHidden( $obHdnTipoBusca         );
$obFormulario->addIFrameOculto("oculto");

$obFormulario->OK();
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("10%");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
