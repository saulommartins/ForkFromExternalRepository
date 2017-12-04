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

    * Página de Formulario de Manter Homologacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * Casos de uso: uc-03.05.21

    $Id: FMManterHomologacao.php 45605 2011-07-14 20:04:27Z davi.aroldi $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GP_COM_COMPONENTES."IMontaNumeroCompraDireta.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterHomologacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

//DEFINE COMPONENTES DO FORMULARIO

$jsOnload = "executaFuncaoAjax('configuracoesIniciais');";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obMontaCompraDireta = new IMontaNumeroCompraDireta($obForm, false);
$obMontaCompraDireta->obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange( $obMontaCompraDireta->obITextBoxSelectEntidadeGeral->obSelect->obEvento->getOnChange()."executaFuncaoAjax('limpaSpans');" );
$obMontaCompraDireta->obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange( $obMontaCompraDireta->obITextBoxSelectEntidadeGeral->obTextBox->obEvento->getOnChange()."executaFuncaoAjax('limpaSpans');" );
$obMontaCompraDireta->obISelectModalidade->obEvento->setOnChange( $obMontaCompraDireta->obISelectModalidade->obEvento->getOnChange()."executaFuncaoAjax('limpaSpans');" );
$obMontaCompraDireta->obCmbCompraDireta->obEvento->setOnChange( $obMontaCompraDireta->obCmbCompraDireta->obEvento->getOnChange()."montaParametrosGET('carregaItensBanco');" );
$obMontaCompraDireta->setSelecionaAutomaticamenteCompraDireta( false );

$obDocumento = new ITextBoxSelectDocumento();
$obDocumento->setCodAcao( Sessao::read('acao') );

$obLblObjeto = new ILabelEditObjeto();
$obLblObjeto->setRotulo( "Objeto" );
$obLblObjeto->setId( "objeto" );

$obSpnItensHomologacao = new Span;
$obSpnItensHomologacao->setId ( "spnItensHomologacao" );

$obSpnHomologacao = new Span;
$obSpnHomologacao->setId ( "spnHomologacao" );

$obSpnAutorizacao = new Span;
$obSpnAutorizacao->setId ( "spnAutorizacaoEmpenho" );

$obChkGerarTermo = new CheckBox;
$obChkGerarTermo->setRotulo('Gerar Termo de Homologação');
$obChkGerarTermo->setName('boGerarTermoHomologacao');

$obDtHomologacao = new Data;
$obDtHomologacao->setName               ( 'stDtHomologacao' );
$obDtHomologacao->setId                 ( 'stDtHomologacao' );
$obDtHomologacao->setRotulo             ( 'Data da Homologação' );
$obDtHomologacao->setTitle              ( 'Informe a data de Homologação.' );
$obDtHomologacao->setNull               ( false );

$obHoraHomologacao = new Hora;
$obHoraHomologacao->setName ( "stHoraHomologacao" );
$obHoraHomologacao->setId ( "stHoraHomologacao" );
$obHoraHomologacao->setRotulo( "Hora da Homologação" );
$obHoraHomologacao->setTitle( "Informe a hora da Homologação." );
$obHoraHomologacao->setNull ( false );

$obTxtJustificativa = new TextArea;
$obTxtJustificativa->setName   ( 'stJustificativa' );
$obTxtJustificativa->setId     ( 'stJustificativa' );
//$obTxtJustificativa->setValue  ( $stJustificativa  );
$obTxtJustificativa->setRotulo ( "Justificativa"   );
$obTxtJustificativa->setTitle  ( "Justificativa e fundamentação legal para contratação mediante dispensa ou inexigibilidade." );
$obTxtJustificativa->setNull   ( true              );
$obTxtJustificativa->setRows   ( 2                 );
$obTxtJustificativa->setCols   ( 100               );
$obTxtJustificativa->setMaxCaracteres( 250         );

$obTxtRazao = new TextArea;
$obTxtRazao->setName   ( 'stRazao' );
$obTxtRazao->setId     ( 'stRazao' );
//$obTxtRazao->setValue  ( $stRazao  );
$obTxtRazao->setRotulo ( "Razão"   );
$obTxtRazao->setTitle  ( "Razão da escolha do fornecedor ou executante." );
$obTxtRazao->setNull   ( true              );
$obTxtRazao->setRows   ( 2                 );
$obTxtRazao->setCols   ( 100               );
$obTxtRazao->setMaxCaracteres( 250         );

$obTxtFundamentacao = new TextArea;
$obTxtFundamentacao->setName   ( 'stFundamentacao' );
$obTxtFundamentacao->setId     ( 'stFundamentacao' );
$obTxtFundamentacao->setRotulo ( "Fundamentação Legal"   );
$obTxtFundamentacao->setTitle  ( "Informe a Fundamentação Legal." );
$obTxtFundamentacao->setNull   ( false              );
$obTxtFundamentacao->setRows   ( 2                 );
$obTxtFundamentacao->setCols   ( 100               );
$obTxtFundamentacao->setMaxCaracteres( 250         );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados da Homologação" );
$obMontaCompraDireta->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obLblObjeto );
$obFormulario->addComponente( $obDtHomologacao );
$obFormulario->addComponente( $obHoraHomologacao );
$obFormulario->addComponente( $obTxtJustificativa );
$obFormulario->addComponente( $obTxtRazao );
$obFormulario->addComponente( $obTxtFundamentacao );
$obFormulario->addSpan( $obSpnItensHomologacao );
$obFormulario->addSpan( $obSpnHomologacao );
$obFormulario->addSpan( $obSpnAutorizacao );
$obFormulario->addComponente( $obChkGerarTermo );

$obBtnOk = new Ok;

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTela')" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
