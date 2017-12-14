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
* Arquivo instância para popup para inserir CBO
* Data de Criação: 13/06/2013
* @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCbo.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "InserirCBO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove( "filtroRelatorio" );
Sessao::remove( "link" );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_GET['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Definição das Caixas de Texto
$obTxtNomeCbo = new TextBox;
$obTxtNomeCbo->setName( "stNomeCbo" );
$obTxtNomeCbo->setRotulo( "Nome CBO" );
$obTxtNomeCbo->setSize( 60 );
$obTxtNomeCbo->setMaxLength( 60 );
$obTxtNomeCbo->setNull( false );

$obTxtNumCbo = new TextBox;
$obTxtNumCbo->setName( "stNumCbo" );
$obTxtNumCbo->setRotulo( "Numero CBO" );
$obTxtNumCbo->setSize( 15 );
$obTxtNumCbo->setMaxLength( 6 );
$obTxtNumCbo->setNull( false );

$obDtInicial = new Data;
$obDtInicial->setName("dtInicial");
$obDtInicial->setRotulo( "Data Inicial" );
$obDtInicial->setNull(false);

$obDtFinal = new Data;
$obDtFinal->setName("dtFinal");
$obDtFinal->setRotulo( "Data Final" );
$obDtFinal->setNull(true);

$obInserir = new Button;
$obInserir->setValue("Inserir");
$obInserir->obEvento->setOnClick("Salvar();");

$obLimpar = new Limpar;

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addTitulo( "Dados para CBO" );
$obFormulario->addComponente( $obTxtNomeCbo );
$obFormulario->addComponente( $obTxtNumCbo );
$obFormulario->addComponente( $obDtInicial );
$obFormulario->addComponente( $obDtFinal );
$obFormulario->defineBarra( array( $obInserir, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
