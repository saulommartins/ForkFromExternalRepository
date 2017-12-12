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
    * Página de Filtro para relatorico de Contas
    * Data de Criação   : 25/11/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    * $Id: FLLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

Sessao::write('rsLancamento', '');

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
$rsRecordset = new RecordSet;
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$stOrdem = "ORDER BY cod_entidade";
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCLancamento.php" );

// Define Objeto Data para data do lote
$obDtLote = new Data;
$obDtLote->setName  ( "stDtLote" );
$obDtLote->setRotulo( "Data do Lote" );
$obDtLote->setTitle ( "Informe a data do lote." );

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull        (false );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

// define objeto TextBox para codigo do lote
$obTxtCodLote = new TextBox;
$obTxtCodLote->setName   ( "inCodLote" );
$obTxtCodLote->setInteiro( true );
$obTxtCodLote->setRotulo ( "Código do Lote" );
$obTxtCodLote->setTitle  ( " Informe o código do lote." );

// define objeto TextBox para nome do lote
$obTxtNomLote = new TextBox;
$obTxtNomLote->setName   ( "stNomLote" );
$obTxtNomLote->setRotulo ( "Nome do Lote" );
$obTxtNomLote->setTitle  ( "Informe o nome do lote." );
$obTxtNomLote->setSize     ( 80 );
$obTxtNomLote->setMaxLength( 80 );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione a(s) entidade(s)." );
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

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.21');
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCaminho );
//$obFormulario->addHidden        ( $obHdnValidaData,true );

$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades );
$obFormulario->addComponente( $obPeriodo );
$obFormulario->addComponente( $obDtLote       );
$obFormulario->addComponente( $obTxtCodLote   );
$obFormulario->addComponente( $obTxtNomLote   );
$obFormulario->OK();
$obFormulario->show();

//include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php");
?>
