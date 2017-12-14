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
    * Pagina de formulário para Incluir Penalidade a Fornecedor
    * Data de Criação   : 10/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.28

    $Id: FMManterPenalidadeFornecedor.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GP_COM_COMPONENTES."IPopUpFornecedor.class.php" );
include_once ( CAM_GA_PROT_COMPONENTES."IPopUpProcesso.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPenalidadeFornecedor";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

include_once ( $pgJS );

Sessao::write('arPen' , array());

$stAcao            = $_REQUEST['stAcao'];
$stCtrl            = $_REQUEST['stCtrl'];
$id                = $_REQUEST['id'];
$stAcaoSessao      = $_REQUEST['stAcaoSessao'];
$stNumCertificacao = $_REQUEST['stNumCertificacao'];
$inCertificacao    = $_REQUEST['inCertificacao'];
$stExercicio       = $_REQUEST['stExercicio'];

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obForm = new Form();
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden();
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnNumCertificacao = new Hidden();
$obHdnNumCertificacao->setName ( 'inCertificacao' );
$obHdnNumCertificacao->setId   ( 'inCertificacao' );
$obHdnNumCertificacao->setValue( $inCertificacao );

$obHdnAcaoSessao = new Hidden();
$obHdnAcaoSessao->setName( "stAcaoSessao" );
$obHdnAcaoSessao->setValue( $stAcaoSessao );

$obLblNumCertificacao = new Label();
$obLblNumCertificacao->setId    ( 'stNumCertificacao' );
$obLblNumCertificacao->setValue ( $stNumCertificacao == '' ? '&nbsp;' : $stNumCertificacao );
$obLblNumCertificacao->setRotulo( 'Número da Certificação' );

$obHdnId = new Hidden();
$obHdnId->setName( "id" );
$obHdnId->setValue( $id );

$obHdnExercicio = new Hidden();
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setValue( $stExercicio );

if ($stAcao == 'alterar') {
    $obLblFornecedor = new Label();
    $obLblFornecedor->setName( 'stFornecedor' );
    $obLblFornecedor->setValue( $_REQUEST['inCodFornecedor'].'-'.$_REQUEST['stNomFornecedor'] );
    $obLblFornecedor->setRotulo( 'Fornecedor' );

    $obHdnFornecedor = new Hidden();
    $obHdnFornecedor->setName( 'inCodFornecedor' );
    $obHdnFornecedor->setValue( $_REQUEST['inCodFornecedor'] );

    $jsOnload  = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodFornecedor=".$_REQUEST['inCodFornecedor']."','listarCertificacao');\n";
    $jsOnload .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodFornecedor=".$_REQUEST['inCodFornecedor']."&inCertificacao=".$_REQUEST['inCertificacao']. "&stExercicio=".$_REQUEST['stExercicio']. "&stCodDocumento=".$_REQUEST['stCodDocumento']." &inCodTipoDocumento=".$_REQUEST['inCodTipoDocumento']."','montaAlteracao');";
} else {
    $obFornecedor = new IPopUpFornecedor($obForm);
    $obFornecedor->setId ( "stNomFornecedor" );
    $obFornecedor->setTitle( "Selecione o Fornecedor." );
    $obFornecedor->setNull( false );
    $obFornecedor->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodFornecedor='+this.value,'listarCertificacao');");
    $obFornecedor->setTipoConsulta ( 'certificados' );
    $obFornecedor->obCampoCod->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodFornecedor='+this.value,'listarCertificacao');");
}

$obLblDataRegistro = new Label();
$obLblDataRegistro->setId( 'dtDataRegistro' );
$obLblDataRegistro->setValue( $dtDataRegistro == '' ? '&nbsp;' : $dtDataRegistro );
$obLblDataRegistro->setRotulo( 'Data do Registro' );

$obLblDataVigencia = new Label();
$obLblDataVigencia->setId( 'dtDataVigencia' );
$obLblDataVigencia->setValue( $dtDataVigencia == '' ? '&nbsp;' : $dtDataVigencia );
$obLblDataVigencia->setRotulo( 'Data da Vigência' );

Sessao::getExercicio();

include_once( TLIC."TLicitacaoPenalidade.class.php");
$obTLicitacaoDocumento = new TLICitacaoPenalidade;
$obTLicitacaoDocumento->recuperaTodos( $rsPenalidade );

$obTxtPenalidade = new TextBox;
$obTxtPenalidade->setName  ( "inPenalidade" );
$obTxtPenalidade->setRotulo( "Penalidade" );
$obTxtPenalidade->setTitle ( "Selecione a Penalidade." );
$obTxtPenalidade->setValue ( $inPenalidadeTxt );
$obTxtPenalidade->setObrigatorioBarra( true );

$obCmbPenalidade = new Select;
$obCmbPenalidade->setName      ( "stPenalidade"            );
$obCmbPenalidade->setValue     ( $inPenalidade             );
$obCmbPenalidade->setRotulo    ( "Penalidade"              );
$obCmbPenalidade->setTitle     ( "Selecione a Penalidade." );
$obCmbPenalidade->setId        ( "stPenalidade"            );
$obCmbPenalidade->setCampoID   ( 'cod_penalidade'          );
$obCmbPenalidade->setCampoDesc ( 'descricao'               );
$obCmbPenalidade->addOption    ( "", "Selecione"           );
$obCmbPenalidade->preencheCombo( $rsPenalidade             );
$obCmbPenalidade->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inPenalidade='+frm.inPenalidade.value,'mostraValor');" );
$obCmbPenalidade->setObrigatorioBarra( true );

$obSpnValor = new Span();
$obSpnValor->setId( 'spnValor' );

$obProtocolo = new IPopUpProcesso($obForm);
$obProtocolo->setRotulo( '**'.$obProtocolo->obCampoCod->getRotulo() );
$obProtocolo->obCampoCod->setObrigatorioBarra( true );
$obProtocolo->setValidar( true );

$obDataPublicacao = new Data();
$obDataPublicacao->setName ( "dtDataPublicacao" );
$obDataPublicacao->setValue( $dtDataPublicacao );
$obDataPublicacao->setRotulo( "Data de Publicação" );
$obDataPublicacao->setTitle( "Informe a Data de Publicação da Penalidade." );
$obDataPublicacao->setObrigatorioBarra( true );

$obDataValidade = new Data();
$obDataValidade->setName ( "dtDataValidade" );
$obDataValidade->setValue( $dtDataValidade );
$obDataValidade->setRotulo( "Data de Validade" );
$obDataValidade->setTitle( "Informe a Data de Validade da Penalidade." );
$obDataValidade->setObrigatorioBarra( true );

$obTxtObservacao = new TextArea();
$obTxtObservacao->setName  ( "stObservacao" );
$obTxtObservacao->setRotulo( "Observações" );
$obTxtObservacao->setTitle ( "Digite observações pertinentes a este registro." );
$obTxtObservacao->setCols  ( 40 );
$obTxtObservacao->setRows  ( 5 );
$obTxtObservacao->setValue ( $stObservacao );

$obSpnPenalidade = new Span();
$obSpnPenalidade->setId( 'spnPenalidade' );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnNumCertificacao );
$obFormulario->addHidden( $obHdnAcaoSessao );
$obFormulario->addHidden( $obHdnId );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addTitulo( 'Dados para Registro Cadastral/Certificação do Fornecedor/Conveniado' );

if ($stAcao == 'alterar') {
    $obFormulario->addComponente( $obLblFornecedor );
    $obFormulario->addHidden( $obHdnFornecedor );
} else {
    $obFormulario->addComponente( $obFornecedor );
}

$obFormulario->addComponente( $obLblNumCertificacao       );
$obFormulario->addComponente( $obLblDataRegistro          );
$obFormulario->addComponente( $obLblDataVigencia          );
$obFormulario->addTitulo( 'Dados das Penalidades Aplicadas' );
$obFormulario->addComponenteComposto( $obTxtPenalidade, $obCmbPenalidade );
$obFormulario->addSpan( $obSpnValor );
$obFormulario->addComponente( $obProtocolo );
$obFormulario->addComponente( $obDataPublicacao );
$obFormulario->addComponente( $obDataValidade );
$obFormulario->addComponente( $obTxtObservacao );
$obFormulario->Incluir('Penalidade',array($obTxtPenalidade, $obCmbPenalidade, $obProtocolo->obCampoCod, $obDataPublicacao, $obDataValidade));
$obFormulario->addSpan( $obSpnPenalidade );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
