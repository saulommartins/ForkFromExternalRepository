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
    * Pagina de formulário para Incluir Cadastro/Certificação
    * Data de Criação   : 27/09/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.13

    $Id: FMManterCertificacao.php 64452 2016-02-23 21:03:45Z arthur $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_COM_COMPONENTES.'IPopUpFornecedor.class.php';
include_once CAM_GA_ADM_COMPONENTES.'ITextBoxSelectDocumento.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GP_COM_COMPONENTES.'ISelectModalidade.class.php';
include_once TLIC.'TLicitacaoDocumento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCertificacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

include_once ( $pgJS );

$stAcao            = $request->get('stAcao');
$inNumCertificacao = $request->get('inNumCertificacao');
$id                = $request->get('id');
$stAcaoSessao      = $request->get('stAcaoSessao');
$hdnObservacao     = $request->get('stObservacao');
$stCtrl            = $request->get('stCtrl');

Sessao::remove('arDocs');
Sessao::write('arDocs' , array());

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

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
$obHdnNumCertificacao->setName ( "inNumCertificacao" );
$obHdnNumCertificacao->setValue( $inNumCertificacao  );

$obHdnAcaoSessao = new Hidden();
$obHdnAcaoSessao->setName( "stAcaoSessao" );
$obHdnAcaoSessao->setValue( $stAcaoSessao );

$obHdnId = new Hidden();
$obHdnId->setName( "id" );
$obHdnId->setValue( $id );

$obHdnObservacao = new Hidden();
$obHdnObservacao->setName( "hdnObservacao" );
$obHdnObservacao->setValue( $hdnObservacao );

if ($stAcao == 'alterar') {
    $obHdnExercicio = new Hidden();
    $obHdnExercicio->setName ( "stHdnExercicio" );
    $obHdnExercicio->setValue( $request->get('stExercicio') );

    $obHdnNomFornecedor = new Hidden();
    $obHdnNomFornecedor->setName ("stHdnNomFornecedor");
    $obHdnNomFornecedor->setValue( $request->get('stNomFornecedor') );

    $obHdnCodFornecedor = new Hidden();
    $obHdnCodFornecedor->setName ("inHdnCodFornecedor");
    $obHdnCodFornecedor->setValue( $request->get('inCodFornecedor') );

    $obHdnDataRegistro = new Hidden();
    $obHdnDataRegistro->setName( "dtHdnDataRegistro" );
    $obHdnDataRegistro->setValue( $request->get('dtDataRegistro') );

    $obHdnDataVigencia = new Hidden();
    $obHdnDataVigencia->setName( "dtHdnDataVigencia" );
    $obHdnDataVigencia->setValue( $request->get('dtDataVigencia') );
}

$obLblNumCertificacao = new Label();
$obLblNumCertificacao->setId( 'inNumCertificacao' );
$obLblNumCertificacao->setValue( $request->get('inNumCertificacao').'/'.$request->get('stExercicio') );
$obLblNumCertificacao->setRotulo( 'Número da Certificação' );

if (( $stAcao == 'alterar' ) || ( $stAcao == 'consultar' )) {
    $obLblFornecedor = new Label();
    $obLblFornecedor->setId( 'inCodFornecedor' );
    $obLblFornecedor->setValue( $request->get('inCodFornecedor').'-'.$request->get('stNomFornecedor') );
    $obLblFornecedor->setRotulo( 'Fornecedor' );
} else {
    $obFornecedor = new IPopUpFornecedor($obForm);
    $obFornecedor->setId   ( "stNomFornecedor" );
    $obFornecedor->setTitle( "Selecione o Fornecedor." );
    $obFornecedor->setNull ( false );
    $obFornecedor->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&inCodLicitacao='+frm.inCodLicitacao.value+'&inCodFornecedor='+this.value, 'validaFornecedorLicitacao');" );
}

if( ( $stAcao == 'incluir' ) || ( $stAcao == 'alterar' ) ){
    $rsLicitacao = new RecordSet();
    
    $obExercicio = new Exercicio();
    $obExercicio->setName( 'stExercicioLicitacao' );
    $obExercicio->setId  ( 'stExercicioLicitacao' );
    $obExercicio->setNull( false );
    $obExercicio->setValue ( $request->get('stExercicioLicitacao') ? $request->get('stExercicioLicitacao') : Sessao::getExercicio() );
    if($stAcao == 'alterar')
        $obExercicio->setLabel(true);

    $obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
    $obITextBoxSelectEntidadeUsuario->obSelect->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+this.value, 'carregaModalidade');");
    $obITextBoxSelectEntidadeUsuario->obTextBox->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+this.value, 'carregaModalidade');");
    $obITextBoxSelectEntidadeUsuario->setNull( false );
    $obITextBoxSelectEntidadeUsuario->setCodEntidade($request->get('inCodEntidade') ? $request->get('inCodEntidade') : '');
  
    $obISelectModalidade = new ISelectModalidade();
    $obISelectModalidade->setNull( false );
    $obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?". Sessao::getId(). "&inCodLicitacao='+frm.inCodLicitacao.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&numLicitacao='+document.getElementById('hdnNumLicitacao').value+'&stFiltro=', 'carregaLicitacao');");    
    $obISelectModalidade->setValue($request->get('inCodModalidade') ? $request->get('inCodModalidade') : '');    
    
    //Carrega as informações na alteração
    if ( $stAcao == "alterar" ){
        $jsOnload .= " executaFuncaoAjax('carregaLicitacao', '&inCodLicitacao=".$request->get('inCodLicitacao')."&stExercicioLicitacao=".$request->get('stExercicioLicitacao')."&inCodEntidade=".$request->get('inCodEntidade')."&inCodModalidade=".$request->get('inCodModalidade')."&stFiltraLicitacao=false&numLicitacao=".$request->get('inCodLicitacao')."&stFiltro=');";
    }
    
    $obCmbLicitacao = new Select();
    $obCmbLicitacao->setRotulo    ( 'Licitação'        );
    $obCmbLicitacao->setTitle     ( 'Selecione a Licitação.' );
    $obCmbLicitacao->setId        ( 'inCodLicitacao'   );
    $obCmbLicitacao->setName      ( 'inCodLicitacao'   );
    $obCmbLicitacao->setCampoID   ( 'cod_licitacao'    );
    $obCmbLicitacao->setCampoDesc ( 'cod_licitacao'    );
    $obCmbLicitacao->addOption    ( '','Selecione'     );
    $obCmbLicitacao->preencheCombo( $rsLicitacao       );        
    $obCmbLicitacao->setNull      ( false );
        
    $obHdnNumLicitacao = new Hidden();
    $obHdnNumLicitacao->setName ( 'hdnNumLicitacao' );
    $obHdnNumLicitacao->setId   ( 'hdnNumLicitacao' );
    $obHdnNumLicitacao->setValue( $request->get('inCodLicitacao') ? $request->get('inCodLicitacao') : '' );
}

if (( $stAcao == 'alterar' ) || ( $stAcao == 'consultar' )) {
    $obLblDataRegistro = new Label();
    $obLblDataRegistro->setId( 'dtDataRegistro' );
    $obLblDataRegistro->setValue( $request->get('dtDataRegistro') );
    $obLblDataRegistro->setRotulo( 'Data do Registro' );
} else {
    $obDataRegistro = new Data();
    $obDataRegistro->setName ( "dtDataRegistro" );
    $obDataRegistro->setValue( date('d/m/Y') );
    $obDataRegistro->setRotulo( "Data do Registro" );
    $obDataRegistro->setNull( false );
    $obDataRegistro->setTitle( "Informe a Data do Registro e Certificação do Fornecedor." );
}

if (( $stAcao == 'alterar' ) || ( $stAcao == 'consultar' )) {
    $obLblDataVigencia = new Label();
    $obLblDataVigencia->setId( 'dtDataVigencia' );
    $obLblDataVigencia->setValue( $request->get('dtDataVigencia') );
    $obLblDataVigencia->setRotulo( 'Data da Vigência' );
} else {
    $dtDataVigencia  = date('d').'/';
    $dtDataVigencia .= date('m').'/';
    $dtDataVigencia .= date('Y')+1;

    $obDataVigencia = new Data();
    $obDataVigencia->setName ( "dtDataVigencia" );
    $obDataVigencia->setValue( $dtDataVigencia );
    $obDataVigencia->setRotulo( "Data da Vigência" );
    $obDataVigencia->setNull( false );
    $obDataVigencia->setTitle( "Informe a Data de Validade do Registro e Certificação do Fornecedor." );
}

if ($stAcao == 'consultar') {
    
    $obLblExercicio = new Label();
    $obLblExercicio->setId( 'stExercicioLicitacao' );
    $obLblExercicio->setName( 'stExercicioLicitacao' );
    $obLblExercicio->setValue( $request->get('stExercicioLicitacao') );
    $obLblExercicio->setRotulo( 'Exercício' );
    
    $obLblEntidade = new Label();
    $obLblEntidade->setId( 'stEntidade' );
    $obLblEntidade->setName( 'stEntidade' );
    $obLblEntidade->setValue( $request->get('stEntidade') );
    $obLblEntidade->setRotulo( 'Entidade' );
    
    $obLblModalidade = new Label();
    $obLblModalidade->setId  ( 'stModalidade' );
    $obLblModalidade->setName( 'stModalidade' );
    $obLblModalidade->setValue( $request->get('inCodModalidade') );
    $obLblModalidade->setRotulo( 'Modalidade' );
    
    $obLblLicitacao = new Label();
    $obLblLicitacao->setId  ( 'stLicitacao' );
    $obLblLicitacao->setName( 'stLicitacao' );
    $obLblLicitacao->setValue( $request->get('inCodLicitacao') );
    $obLblLicitacao->setRotulo( 'Licitação' );
    
    $obLblObservacao = new Label();
    $obLblObservacao->setId( 'stObservacao' );
    $obLblObservacao->setValue( $request->get('stObservacao') );
    $obLblObservacao->setRotulo( 'Observações' );
    
} else {
    $obTxtObservacao = new TextArea;
    $obTxtObservacao->setName  ( "stObservacao" );
    $obTxtObservacao->setRotulo( "Observações" );
    $obTxtObservacao->setTitle ( "Digite observações pertinentes a este registro." );
    $obTxtObservacao->setCols  ( 40 );
    $obTxtObservacao->setRows  ( 5 );
    $obTxtObservacao->setValue ( $hdnObservacao );
}

$obTLicitacaoDocumento = new TLICitacaoDocumento;
$obTLicitacaoDocumento->recuperaTodos( $rsDocumento );

$obTxtDocumento = new TextBox;
$obTxtDocumento->setName  ( "inDocumento" );
$obTxtDocumento->setRotulo( "Documento" );
$obTxtDocumento->setTitle ( "Selecione o documento." );
$obTxtDocumento->setObrigatorioBarra( true );

$obCmbDocumento = new Select;
$obCmbDocumento->setName      ( "stDocumento" );
$obCmbDocumento->setRotulo    ( "Documento" );
$obCmbDocumento->setTitle     ( "Selecione o documento." );
$obCmbDocumento->setId        ( "stDocumento" );
$obCmbDocumento->setCampoID   ( 'cod_documento' );
$obCmbDocumento->setCampoDesc ( 'nom_documento' );
$obCmbDocumento->addOption    ( "", "Selecione" );
$obCmbDocumento->preencheCombo( $rsDocumento );
$obCmbDocumento->obEvento->setOnChange(" ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inDocumento='+frm.inDocumento.value,'montaAtributos');" );
$obCmbDocumento->setObrigatorioBarra( true );

$obTxtNumDocumento = new TextBox;
$obTxtNumDocumento->setName  ( "inNumDocumento" );
$obTxtNumDocumento->setRotulo( "Número do Documento" );
$obTxtNumDocumento->setTitle ( "Informe o número do documento." );
$obTxtNumDocumento->setMaxLength(30);
$obTxtNumDocumento->setObrigatorioBarra( true );

$obDataEmissao = new Data();
$obDataEmissao->setName('stDataEmissao');
$obDataEmissao->setId('stDataEmissao');
$obDataEmissao->setRotulo('**Data de Emissão');
$obDataEmissao->setValue($request->get('stDataEmissao'));
$obDataEmissao->setNull ( true );
$obDataEmissao->obEvento->setOnChange("bloqueiaDesbloqueiaCampos(this);formataDiasValidosDocumento();");

$obDataValidade = new Data();
$obDataValidade->setName ( "stDataValidade" );
$obDataValidade->setId ( "stDataValidade" );
$obDataValidade->setValue( $request->get('stDataValidade') );
$obDataValidade->setRotulo( "**Data de Validade" );
$obDataValidade->setTitle( "Informe a Data de Validade do Documento." );
$obDataValidade->obEvento->setOnChange("if (verificaData(this)) { if (validaData(this)) { formataDiasValidosDocumento(); } } else { jQuery(this).val(''); jQuery('#inNumDiasValido').val(''); }");
$obDataValidade->setNull( true );

$obTxtNumDiasVcto = new TextBox;
$obTxtNumDiasVcto->setName  ( "inNumDiasValido" );
$obTxtNumDiasVcto->setId  ( "inNumDiasValido" );
$obTxtNumDiasVcto->setRotulo( "Dias para Vencimento" );
$obTxtNumDiasVcto->setTitle ( "Informe o número de dias para o vencimento do documento." );
$obTxtNumDiasVcto->setValue ( $request->get('inNumDiasValido') );
$obTxtNumDiasVcto->setMaxLength(4);
$obTxtNumDiasVcto->setInteiro(true);
$obTxtNumDiasVcto->setObrigatorioBarra( false );

$obTxtNumDiasVcto->obEvento->setOnBlur('formataDataValidaDocumento()');

$obSpnAtributos = new Span();
$obSpnAtributos->setId( 'spnAtributos' );

$obSpnDocumentos = new Span();
$obSpnDocumentos->setId( 'spnDocumentos' );

if (( $stAcao == "alterar" ) || ( $stAcao == "consultar" )) {
    $jsOnload .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stNomFornecedor=".$request->get('stNomFornecedor'). "&inCodFornecedor=".$request->get('inCodFornecedor')."&inNumCertificacao=".$request->get('inNumCertificacao')." &stExercicio=".$request->get('stExercicio')."&stAcao=".$stAcao."','montaAlteracao');\n";
}

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnNumCertificacao );
$obFormulario->addHidden( $obHdnAcaoSessao );
$obFormulario->addHidden( $obHdnId );
$obFormulario->addHidden( $obHdnObservacao );

if (( $stAcao == 'incluir' ) || ( $stAcao == 'alterar' )) {
    $obFormulario->addComponente( $obExercicio    );
    $obFormulario->addComponente( $obITextBoxSelectEntidadeUsuario );
    $obFormulario->addComponente( $obISelectModalidade );
    $obFormulario->addComponente( $obCmbLicitacao );
    $obFormulario->addHidden( $obHdnNumLicitacao  );
}

if ($stAcao == 'alterar') {
    $obFormulario->addHidden( $obHdnExercicio );
    $obFormulario->addHidden( $obHdnNomFornecedor );
    $obFormulario->addHidden( $obHdnCodFornecedor );
    $obFormulario->addHidden( $obHdnDataRegistro  );
    $obFormulario->addHidden( $obHdnDataVigencia  );
    $obFormulario->addComponente( $obLblNumCertificacao );
}

if ($stAcao == 'consultar') {
    $obFormulario->addComponente( $obLblExercicio );
    $obFormulario->addComponente( $obLblEntidade );
    $obFormulario->addComponente( $obLblModalidade );
    $obFormulario->addComponente( $obLblLicitacao );
}

if ( $stAcao == 'alterar' || $stAcao == 'consultar' ) {

$obFormulario->addComponente( $obLblFornecedor   );
$obFormulario->addComponente( $obLblDataRegistro );
$obFormulario->addComponente( $obLblDataVigencia );
    
} else {

$obFormulario->addComponente( $obFornecedor   );
$obFormulario->addComponente( $obDataRegistro );
$obFormulario->addComponente( $obDataVigencia );
   
}

$obFormulario->addComponente( $stAcao == 'consultar' ? $obLblObservacao : $obTxtObservacao );

if ($stAcao != 'consultar') {
    $obFormulario->addTitulo( "Dados dos Documentos Exigidos" );
    $obFormulario->addComponenteComposto( $obTxtDocumento, $obCmbDocumento );
    $obFormulario->addComponente( $obTxtNumDocumento );
    $obFormulario->addComponente    ( $obDataEmissao     );
    $obFormulario->addComponente    ( $obTxtNumDiasVcto);
    $obFormulario->addComponente    ( $obDataValidade  );

    $obFormulario->addSpan( $obSpnAtributos );
    $obFormulario->Incluir( 'Documento', array( $obTxtDocumento, $obCmbDocumento, $obTxtNumDocumento, $obDataEmissao, $obDataValidade,$obTxtNumDiasVcto) );
}

$obFormulario->addTitulo( 'Documentos Exigidos' );
$obFormulario->addSpan( $obSpnDocumentos );

if ($stAcao == 'consultar') {
    $stLocation = $pgList.'?'.Sessao::getId().$stLink;
    $obBtnCancelar = new Cancelar;
    $obBtnCancelar->setValue ( 'Voltar' );
    $obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
    $obFormulario->defineBarra( array( $obBtnCancelar  ), '', '' );
} else {
    if ($stAcao == 'alterar') {
        $obBtnOk = new Ok;
        $obBtnOk->setId( 'Ok');

        $stLocation = $pgList.'?'.Sessao::getId().$stLink;
        $obBtnCancelar = new Cancelar;
        
        $obBtnCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
        $obFormulario->defineBarra( array( $obBtnOk, $obBtnCancelar  ), '', '' );
    } else {
        $obFormulario->Ok();
    }
}
$obFormulario->Show();

if ($request->get('stAcao') == 'consultar') {
    include_once( $pgJS );
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>