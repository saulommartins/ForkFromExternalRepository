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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * Casos de uso: uc-03.05.20

    $Id: FMManterAdjudicacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once ( CAM_GP_LIC_COMPONENTES."IMontaNumeroLicitacao.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdjudicacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

//DEFINE COMPONENTES DO FORMULARIO

$jsOnload = "executaFuncaoAjax('configuracoesIniciais');";

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obMontaLicitacao = new IMontaNumeroLicitacao($obForm, false, 'julgamento');
$obMontaLicitacao->obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange( $obMontaLicitacao->obITextBoxSelectEntidadeGeral->obSelect->obEvento->getOnChange()."executaFuncaoAjax('limpaSpans');" );
$obMontaLicitacao->obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange( $obMontaLicitacao->obITextBoxSelectEntidadeGeral->obTextBox->obEvento->getOnChange()."executaFuncaoAjax('limpaSpans');" );
$obMontaLicitacao->obISelectModalidade->obEvento->setOnChange( $obMontaLicitacao->obISelectModalidade->obEvento->getOnChange()."executaFuncaoAjax('limpaSpans');" );
$obMontaLicitacao->obCmbLicitacao->obEvento->setOnChange( $obMontaLicitacao->obCmbLicitacao->obEvento->getOnChange()."montaParametrosGET('carregaItensBanco');" );
$obMontaLicitacao->setSelecionaAutomaticamenteLicitacao( false );

$obDocumento = new ITextBoxSelectDocumento();
$obDocumento->setCodAcao( Sessao::read('acao') );

$obLblObjeto = new ILabelEditObjeto();
$obLblObjeto->setRotulo( "Objeto" );
$obLblObjeto->setId( "objeto" );

$obSpnItensAdjudicacao = new Span;
$obSpnItensAdjudicacao->setId ( "spnItensAdjudicacao" );

$obSpnClassificacao = new Span;
$obSpnClassificacao->setId ( "spnClassificacao" );

$obDtAdjudicacao = new Data;
$obDtAdjudicacao->setName               ( 'stDtAdjudicacao' );
$obDtAdjudicacao->setId                 ( 'stDtAdjudicacao' );
$obDtAdjudicacao->setRotulo             ( 'Data da Adjudicação' );
$obDtAdjudicacao->setTitle              ( 'Informe a data de Adjudicação.' );
$obDtAdjudicacao->setNull               ( false );

$obHoraAdjudicacao = new Hora;
$obHoraAdjudicacao->setName ( "stHoraAdjudicacao" );
$obHoraAdjudicacao->setId ( "stHoraAdjudicacao" );
$obHoraAdjudicacao->setRotulo( "Hora da Adjudicação" );
$obHoraAdjudicacao->setTitle( "Informe a hora da Adjudicação." );
$obHoraAdjudicacao->setNull ( false );

$obChkGerarTermo = new CheckBox;
$obChkGerarTermo->setRotulo('Gerar Termo de Adjudicação');
$obChkGerarTermo->setId('boGerarTermoAdjudicacao');
$obChkGerarTermo->setName('boGerarTermoAdjudicacao');

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados da Adjudicação" );
$obMontaLicitacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obLblObjeto );
$obFormulario->addComponente( $obDtAdjudicacao );
$obFormulario->addComponente( $obHoraAdjudicacao );
$obFormulario->addSpan( $obSpnItensAdjudicacao );
$obFormulario->addSpan( $obSpnClassificacao );

$obFormulario->addComponente( $obChkGerarTermo );

$obBtnOk = new Ok(true);

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "Limpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "Reset" );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTela')" );

$obFormulario->defineBarra( array ( $obBtnOk , $obBtnLimpar ),"","" );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
