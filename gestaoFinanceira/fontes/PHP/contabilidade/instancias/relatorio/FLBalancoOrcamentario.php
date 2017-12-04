<?php
/**
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 05/05/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: FLAnexo15.php 46609 2012-05-18 13:07:51Z tonismar $

    * Casos de uso: uc-02.02.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$obForm = new Form;
$obForm->setAction( CAM_GF_CONT_INSTANCIAS."relatorio/OCBalancoOrcamentario.php" );
$obForm->setTarget( "telaPrincipal" );

//$obHdnCaminho = new Hidden;
//$obHdnCaminho->setName("stCaminho");
//$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."relatorio/OCBalancoOrcamentario.php" );

$obHdnValidaData = new HiddenEval;
$obHdnValidaData->setName  ( "hdnValidaData" );
$obHdnValidaData->setValue ( $stValidaData  );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
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

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull            (false );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
//$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addHidden    ( $obHdnValidaData,true );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obPeriodo           );
$obFormulario->OK();
$obFormulario->show();
