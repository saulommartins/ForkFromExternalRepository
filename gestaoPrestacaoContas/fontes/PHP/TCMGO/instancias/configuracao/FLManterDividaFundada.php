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

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obEntidade = new ITextBoxSelectEntidadeUsuario;
$obEntidade->setNull ( false );

$obIntExercicio = new Inteiro();
$obIntExercicio->setRotulo      ( "Exercício de configurações para Dados da Dívida Consolidada." );
$obIntExercicio->setTitle       ( "Informe o exercício de vigência das configurações de Dados da Dívida Consolidada." );
$obIntExercicio->setId          ( "inExercicio" );
$obIntExercicio->setName        ( "inExercicio" );
$obIntExercicio->setValue       ( Sessao::getExercicio() );
$obIntExercicio->setNull        ( false );
$obIntExercicio->setSize        ( 5 );
$obIntExercicio->setMaxLength   ( 4 );

$obRadIncluir = new Radio();
$obRadIncluir->setRotulo('Ação');
$obRadIncluir->setId    ('stControleAcao');
$obRadIncluir->setName  ('stControleAcao');
$obRadIncluir->setValue ('incluir');
$obRadIncluir->setLabel ('Incluir');

$obRadAlterar = new Radio();
$obRadAlterar->setId    ('stControleAcao');
$obRadAlterar->setName  ('stControleAcao');
$obRadAlterar->setValue ('alterar');
$obRadAlterar->setLabel ('Alterar');
$obRadAlterar->setChecked   (true);

$arRadControleAcao = array($obRadIncluir, $obRadAlterar);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm           ( $obForm );
$obFormulario->addHidden         ( $obHdnCtrl );
$obFormulario->addHidden         ( $obHdnAcao );
$obFormulario->addTitulo         ( "Dados para filtro" );
$obFormulario->addComponente     ( $obEntidade );
$obFormulario->addComponente     ( $obIntExercicio );
$obFormulario->agrupaComponentes ( $arRadControleAcao );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>