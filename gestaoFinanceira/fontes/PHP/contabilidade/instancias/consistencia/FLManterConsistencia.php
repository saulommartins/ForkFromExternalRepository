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
    * Data de Criação   : 24/05/2006

    * @author Analista Cleisson Barboza
    * @author Desenvolvedor Cleisson da Silva Barboza

    * @ignore

    * $Id: FLManterConsistencia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";

//sessao->transf5 = "";
Sessao::write('arRecordSet', array());
$rsRecordset = new RecordSet;

$stOrdem = "ORDER BY cod_entidade";
$obROrcamentoEntidade = new ROrcamentoEntidade;
$obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_CONT_INSTANCIAS."consistencia/OCManterConsistencia.php" );

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   (  Sessao::getExercicio() );
$obPeriodo->setNull        (false );
$obPeriodo->setValidaExercicio ( true );
$obPeriodo->setValue           ( 4);

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

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCaminho );

$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades );
$obFormulario->addComponente( $obPeriodo );
$obFormulario->OK();
$obFormulario->show();

?>
