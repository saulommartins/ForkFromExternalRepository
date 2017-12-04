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
    * Página de Filtro para relatorico de Razão
    * Data de Criação   : 29/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: FLRazao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php" );
include_once ( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpEstruturalPlano.class.php" );
include_once ( CAM_GF_CONT_COMPONENTES."IIntervaloPopUpContaAnalitica.class.php" );

//sessao->transf5 = "";
Sessao::write('arRazao', array());

$rsRecordset = new RecordSet;
$obRContabilidadePlanoBanco = new RContabilidadePlanoBanco;

$obRContabilidadePlanoBanco->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$stOrdem = "ORDER BY cod_entidade";
$obRContabilidadePlanoBanco->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

Sessao::write('arEntidade', $rsEntidades);

$obRContabilidadePlanoBanco->recuperaMascaraConta( $stMascara );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( CAM_GF_CONT_INSTANCIAS."relatorio/OCGeraRelatorioRazao.php" );
$obForm->setTarget( "telaPrincipal" );

/*$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCRazao.php" );*/

// VALIDA INTERVALO DE DATAS
//$stValidaData = "if (''+document.frm.stDtInicial.value.substr(6,4)+document.frm.stDtInicial.value.substr(3,2)+document.frm.stDtInicial.value.substr(0,2) >  ''+document.frm.stDtFinal.value.substr(6,4)+document.frm.stDtFinal.value.substr(3,2)+document.frm.stDtFinal.value.substr(0,2)) { erro = true; mensagem += '@Data Final deve ser maior que Data Inicial!';} ";
//$stValidaData .= "";
//$stValidaData .= "if (''+document.frm.stDtInicial.value.substr(6,4)+document.frm.stDtInicial.value.substr(3,2)+document.frm.stDtInicial.value.substr(0,2) <=  ''+document.frm.stDtFinal.value.substr(6,4)+document.frm.stDtFinal.value.substr(3,2)+document.frm.stDtFinal.value.substr(0,2)) { erro = false; alertaAviso('Processando ...','form','erro','".Sessao::getId()."'); document.frm.Ok.disabled = true; } ";
//$stValidaData .= "";

//$obHdnValida = new HiddenEval;
//$obHdnValida->setName("hdnValidaData");
//$obHdnValida->setValue( $stValidaData );

// define objeto Data
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull        (  false              );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

// Define intervalo de codigo estrutural de contas
$obIIntervaloPopUpEstruturalPlano = new IIntervaloPopUpEstruturalPlano;

// Define intervalo de codigo reduzidos de contas
$obIIntervaloPopUpContaAnalitica = new IIntervaloPopUpContaAnalitica;

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "" );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

// Define Objeto SimNao para emitir relatorio com ou sem movimentacao de contas
$obSimNaoMovimentacaoConta = new SimNao();
$obSimNaoMovimentacaoConta->setRotulo ( "Imprimir contas sem movimentação" );
$obSimNaoMovimentacaoConta->setName   ( 'boMovimentacaoConta'      );
$obSimNaoMovimentacaoConta->setNull   ( true                       );
$obSimNaoMovimentacaoConta->setChecked( 'NAO'                      );

$obSimNaoHistoricoCompleto = new SimNao();
$obSimNaoHistoricoCompleto->setRotulo ( "Histórico Completo" );
$obSimNaoHistoricoCompleto->setName   ( 'boHistoricoCompleto'      );
$obSimNaoHistoricoCompleto->setNull   ( true                       );
$obSimNaoHistoricoCompleto->setChecked( 'NAO'                      );

// Define Objeto SimNao para emitir relatorio com ou sem quebra de pagina por conta
$obSimNaoQuebraPaginaConta = new SimNao();
$obSimNaoQuebraPaginaConta->setRotulo ( "Quebrar Página por Conta" );
$obSimNaoQuebraPaginaConta->setName   ( 'boQuebraPaginaConta'      );
$obSimNaoQuebraPaginaConta->setNull   ( true                       );
$obSimNaoQuebraPaginaConta->setChecked( 'NAO'                      );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.27');
$obFormulario->addForm( $obForm );
//$obFormulario->addHidden( $obHdnCaminho );
//$obFormulario->addHidden( $obHdnValida, true );

$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades     );
$obFormulario->addComponente( $obPeriodo          );
$obFormulario->addComponente( $obIIntervaloPopUpEstruturalPlano );
$obFormulario->addComponente( $obIIntervaloPopUpContaAnalitica );
$obFormulario->addComponente( $obSimNaoMovimentacaoConta );
$obFormulario->addComponente( $obSimNaoHistoricoCompleto );
$obFormulario->addComponente( $obSimNaoQuebraPaginaConta );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
