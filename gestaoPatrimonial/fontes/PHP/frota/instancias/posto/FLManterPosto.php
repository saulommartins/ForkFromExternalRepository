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
    * Data de Criação: 16/11/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage

    $Id: FLManterTipoVeiculo.php 32939 2008-09-03 21:14:50Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_COMPONENTES.'IPopUpPosto.class.php' );

$stPrograma = "ManterPosto";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//cria um novo formulario
$obForm = new Form;
$obForm->setAction ($pgList);

//Cria o hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//cria a acao de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl" );
$obHdnCtrl->setValue("");

$obBscPosto = new IPopUpPosto($obForm);
$obBscPosto->setNull ( true );

$obRdInternoSim = new Radio();
$obRdInternoSim->setName('boInterno');
$obRdInternoSim->setId( 'boInternoSim' );
$obRdInternoSim->setValue( 't' );
$obRdInternoSim->setRotulo( 'Interno' );
$obRdInternoSim->setTitle( 'Informe se o cgm é interno.' );
$obRdInternoSim->setLabel( 'Sim' );
$obRdInternoSim->setNull( true );

$obRdInternoNao = new Radio();
$obRdInternoNao->setName('boInterno');
$obRdInternoNao->setId( 'boInternoNao' );
$obRdInternoNao->setValue( 'f' );
$obRdInternoNao->setRotulo( 'Interno' );
$obRdInternoNao->setTitle( 'Informe se o cgm é interno.' );
$obRdInternoNao->setLabel( 'Não' );
$obRdInternoNao->setNull( true );

$obRdInternoTodos = new Radio();
$obRdInternoTodos->setName('boInterno');
$obRdInternoTodos->setId( 'boInternoTodos' );
$obRdInternoTodos->setValue( false );
$obRdInternoTodos->setRotulo( 'Interno' );
$obRdInternoTodos->setTitle( 'Informe se o cgm é interno.' );
$obRdInternoTodos->setLabel( 'Todos' );
$obRdInternoTodos->setNull( true );
$obRdInternoTodos->setChecked( true );

$obRdAtivoSim = new Radio();
$obRdAtivoSim->setName('boAtivo');
$obRdAtivoSim->setValue( 't' );
$obRdAtivoSim->setRotulo( 'Ativo' );
$obRdAtivoSim->setTitle( 'Informe se o posto é ativo.' );
$obRdAtivoSim->setLabel( 'Sim' );
$obRdAtivoSim->setChecked( true );
$obRdAtivoSim->setNull( true );

$obRdAtivoNao = new Radio();
$obRdAtivoNao->setName('boAtivo');
$obRdAtivoNao->setValue( 'f' );
$obRdAtivoNao->setRotulo( 'Exige Prefixo' );
$obRdAtivoNao->setTitle( 'Informe se o posto é ativo.' );
$obRdAtivoNao->setLabel( 'Não' );
$obRdAtivoNao->setNull( true );

$obRdAtivoTodos = new Radio();
$obRdAtivoTodos->setName('boAtivo');
$obRdAtivoTodos->setValue( false );
$obRdAtivoTodos->setRotulo( 'Exige Prefixo' );
$obRdAtivoTodos->setTitle( 'Informe se o posto é ativo.' );
$obRdAtivoTodos->setLabel( 'Todos' );
$obRdAtivoTodos->setNull( true );
$obRdAtivoTodos->setChecked( true );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.02');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addTitulo    ( "Dados para o Filtro" );
$obFormulario->addComponente( $obBscPosto );
$obFormulario->agrupaComponentes( array( $obRdInternoSim, $obRdInternoNao, $obRdInternoTodos ) );
$obFormulario->agrupaComponentes( array( $obRdAtivoSim, $obRdAtivoNao, $obRdAtivoTodos ) );
$obFormulario->OK();
$obFormulario->show();
