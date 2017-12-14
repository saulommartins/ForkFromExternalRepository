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
    * Página de Formulário da Configuração de documentos da divida ativa
    * Data de Criação   : 24/11/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgForm = "FM".$stPrograma."Documento.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$stAcao = $request->get('stAcao');
if (!$stAcao) {
    $stAcao = "documento";
}

//DEFINICAO DOS COMPONENTES
$obForm  = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );

$obChkUtilizarMensagem = new Checkbox;
$obChkUtilizarMensagem->setName ( "boMsg" );
$obChkUtilizarMensagem->setId ( "boMsg" );
$obChkUtilizarMensagem->setChecked( true );
$obChkUtilizarMensagem->obEvento->setOnChange( "ControleMsg();");
$obChkUtilizarMensagem->montaHTML();

$obTxtMensagem = new TextArea;
$obTxtMensagem->setName ( "stMensagem" );
$obTxtMensagem->setId ( "stMensagem" );
$obTxtMensagem->setRotulo ( $obChkUtilizarMensagem->getHTML()."Mensagem" );
$obTxtMensagem->setTitle ( "Mensagem." );
$obTxtMensagem->setNull ( true );
$obTxtMensagem->setCols ( 80 );
$obTxtMensagem->setRows ( 5 );
$obTxtMensagem->setMaxCaracteres(3000);
$obTxtMensagem->setStyle ( "width: 540px" );

$obCmbDocumento = new Select;
$obCmbDocumento->setName ( "stDocumento" );
$obCmbDocumento->setRotulo ( "Documento" );
$obCmbDocumento->setTitle ( "Documento" );
$obCmbDocumento->addOption ( "", "Selecione" );
$obCmbDocumento->addOption ( "1", "Certidão de Dívida Ativa" );
$obCmbDocumento->addOption ( "2", "Termo de Inscricao de Dívida Ativa" );
$obCmbDocumento->addOption ( "3", "Memorial de Cálculo da Dívida Ativa" );
$obCmbDocumento->addOption ( "4", "Termo Consolidação" );
$obCmbDocumento->addOption ( "5", "Termo de Parcelamento" );
$obCmbDocumento->addOption ( "6", "Notificação de Dívida Ativa" );
$obCmbDocumento->setStyle ( "width: 300px" );
$obCmbDocumento->obEvento->setOnChange ( "buscaValor('ajustaDocumentos');" );
$obCmbDocumento->setNull ( false );

$obTxtSecretaria = new TextBox;
$obTxtSecretaria->setRotulo  ( 'Secretaria');
$obTxtSecretaria->setTitle   ( 'Informar secretaria.');
$obTxtSecretaria->setName    ( 'stSecretaria');
$obTxtSecretaria->setId      ( 'stSecretaria');
$obTxtSecretaria->setSize    ( 80 );
$obTxtSecretaria->setMaxLength ( 80 );
$obTxtSecretaria->setValue   ( $stSecretaria );
$obTxtSecretaria->setNull    ( false );

$obTxtSetorArrecadacao = new TextBox;
$obTxtSetorArrecadacao->setRotulo  ( 'Setor de Arrecadação');
$obTxtSetorArrecadacao->setTitle   ( 'Informar setor de arrecadação.');
$obTxtSetorArrecadacao->setName    ( 'stSetorArrecadacao');
$obTxtSetorArrecadacao->setId      ( 'stSetorArrecadacao');
$obTxtSetorArrecadacao->setSize    ( 80 );
$obTxtSetorArrecadacao->setMaxLength ( 80 );
$obTxtSetorArrecadacao->setValue   ( $stSetorArrecadacao );
$obTxtSetorArrecadacao->setNull    ( false );

$obTxtCoordenador = new TextArea;
$obTxtCoordenador->setRotulo  ( 'Responsável1');
$obTxtCoordenador->setTitle   ( 'Informar responsável.');
$obTxtCoordenador->setName    ( 'stCoordenador');
$obTxtCoordenador->setId      ( 'stCoordenador');
$obTxtCoordenador->setCols ( 80 );
$obTxtCoordenador->setRows ( 5 );
$obTxtCoordenador->setValue   ( $stCoordenador );
$obTxtCoordenador->setNull    ( false );

$obChkUtilizarResponsavel2 = new Checkbox;
$obChkUtilizarResponsavel2->setName ( "boResp2" );
$obChkUtilizarResponsavel2->setId ( "boResp2" );
$obChkUtilizarResponsavel2->obEvento->setOnChange( "ControleResp2();");
$obChkUtilizarResponsavel2->setChecked( true );
$obChkUtilizarResponsavel2->montaHTML();

$obTxtChefeDepartamento = new TextArea;
$obTxtChefeDepartamento->setRotulo ( $obChkUtilizarResponsavel2->getHTML().'Responsável2' );
$obTxtChefeDepartamento->setTitle ( 'Informar responsável.');
$obTxtChefeDepartamento->setName ( 'stChefeDepartamento');
$obTxtChefeDepartamento->setId ( 'stChefeDepartamento');
$obTxtChefeDepartamento->setCols ( 80 );
$obTxtChefeDepartamento->setRows ( 5 );
$obTxtChefeDepartamento->setValue ( $stChefeDepartamento );
$obTxtChefeDepartamento->setNull ( true );

$obSpnDocumentos = new Span;
$obSpnDocumentos->setID("spnDocumento");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addTitulo ( "Dados para Configuração de Documentos" );
$obFormulario->addComponente( $obCmbDocumento );
$obFormulario->addComponente( $obTxtSecretaria );
$obFormulario->addComponente( $obTxtSetorArrecadacao );
$obFormulario->addComponente( $obTxtCoordenador );
$obFormulario->addComponente( $obTxtChefeDepartamento );
$obFormulario->addComponente( $obTxtMensagem );
$obFormulario->addSpan( $obSpnDocumentos );
$obFormulario->ok();
$obFormulario->show();

?>
