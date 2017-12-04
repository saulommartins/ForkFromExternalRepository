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
    * Arquivo paga Filtro do relatorio de Cadastro de Imoveis
    * Data de Criação: 07/02/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    $Id: FLCondominios.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.27
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once( CAM_GT_CIM_COMPONENTES."IPopUpCondominioIntervalo.class.php" );
include_once 'JSCondominios.js';

$obIPopUpCondominioIntervalo = new IPopUpCondominioIntervalo;

$obRRegra = new RCadastroDinamico;
$rsAtributoCad4 = $rsAtributosCad4 = new RecordSet;
$obRRegra->setCodCadastro('4');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoCad4 );
$rsAtributoCad2 = $rsAtributosCad2 = new RecordSet;
$obRRegra->setCodCadastro('2');
$obRRegra->recuperaAtributosSelecionados ( $rsAtributoCad2 );

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GT_CIM_INSTANCIAS."relatorios/OCCondominios.php" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"] );

$obTxtNomCondominio = new TextBox;
$obTxtNomCondominio->setNull( true );
$obTxtNomCondominio->setName( 'stNomCondominio' );
$obTxtNomCondominio->setRotulo ( 'Nome do Condomínio' );
$obTxtNomCondominio->setTitle ( 'Nome do condomínio' );
$obTxtNomCondominio->setSize      ( "80" ) ;
$obTxtNomCondominio->setMaxLength ( "256" ) ;

$obCmbAtributosCad2 = new SelectMultiplo();
$obCmbAtributosCad2->setName  ('inCodAtributos2');
$obCmbAtributosCad2->setRotulo( "Atributos" );
$obCmbAtributosCad2->setNull  ( true );
$obCmbAtributosCad2->setTitle ( "Selecione os atributos a serem exibidos no relatório" );

// lista de atributos disponiveis
$obCmbAtributosCad2->SetNomeLista1('inCodAtributosDisponiveis2');
$obCmbAtributosCad2->setCampoId1  ('cod_atributo');
$obCmbAtributosCad2->setCampoDesc1('nom_atributo');
$obCmbAtributosCad2->SetRecord1   ( $rsAtributoCad2 );

// lista de atributos selecionados
$obCmbAtributosCad2->SetNomeLista2('inCodAtributosSelecionados2');
$obCmbAtributosCad2->setCampoId2  ('cod_atributo');
$obCmbAtributosCad2->setCampoDesc2('nom_atributo');
$obCmbAtributosCad2->SetRecord2   ( $rsAtributosCad2 );

$obCmbAtributosCad4 = new SelectMultiplo();
$obCmbAtributosCad4->setName  ('inCodAtributos4');
$obCmbAtributosCad4->setRotulo( "Atributos" );
$obCmbAtributosCad4->setNull  ( true );
$obCmbAtributosCad4->setTitle ( "Selecione os atributos a serem exibidos no relatório" );

// lista de atributos disponiveis
$obCmbAtributosCad4->SetNomeLista1('inCodAtributosDisponiveis4');
$obCmbAtributosCad4->setCampoId1  ('cod_atributo');
$obCmbAtributosCad4->setCampoDesc1('nom_atributo');
$obCmbAtributosCad4->SetRecord1   ( $rsAtributoCad4 );

// lista de atributos selecionados
$obCmbAtributosCad4->SetNomeLista2('inCodAtributosSelecionados4');
$obCmbAtributosCad4->setCampoId2  ('cod_atributo');
$obCmbAtributosCad4->setCampoDesc2('nom_atributo');
$obCmbAtributosCad4->SetRecord2   ( $rsAtributosCad4 );

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( "UC-05.01.23" );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addTitulo( "Dados para filtro" );
$obIPopUpCondominioIntervalo->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obTxtNomCondominio );
$obFormulario->addComponente( $obCmbAtributosCad2 );
$obFormulario->addComponente( $obCmbAtributosCad4 );

$obBtnOK = new OK;
$obBtnOK->obEvento->setOnClick( "submeteFiltro()" );
$onBtnLimpar = new Limpar;

$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );

$obFormulario->show();

?>
