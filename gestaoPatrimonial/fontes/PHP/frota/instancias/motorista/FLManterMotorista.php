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
    * Data de Criação: 08/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FLManterMotorista.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.11
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCategoriaHabilitacao.class.php' );

$stPrograma = "ManterMotorista";
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

//instancia um textbox para o cgm do motorista
$obInCGMMotorista = new TextBox();
$obInCGMMotorista->setName( 'inCodMotorista' );
$obInCGMMotorista->setRotulo( 'CGM do Motorista' );
$obInCGMMotorista->setTitle( 'Informe o número do CGM do motorista.' );
$obInCGMMotorista->setInteiro( true );

//instancia um textbox para a descricao do motorista
$obTxtNomMotorista = new TextBox();
$obTxtNomMotorista->setName( 'stNomMotorista' );
$obTxtNomMotorista->setRotulo( 'Nome do Motorista' );
$obTxtNomMotorista->setTitle( 'Informe o nome do motorista' );
$obTxtNomMotorista->setSize( 40 );

//instancia um tipobusca para a descricao do motorista
$obTipoBuscaMotorista = new TipoBusca( $obTxtNomMotorista );

//instancia um componente periodicidade para a validade da carteira
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo( 'Data de Validade CNH' );
$obPeriodicidade->setTitle( 'Informe a data de validade da habilitação.' );

//recupera os as categorias de habilitacao
$obTFrotaCategoriaHabilitacao = new TFrotaCategoriaHabilitacao();
$obTFrotaCategoriaHabilitacao->recuperaTodos( $rsCategoriaHabilitacao, ' ORDER BY nom_categoria ' );

//instancia um selectmultiplo para a habilitacao exigida
$obCmbHabilitacao = new SelectMultiplo();
$obCmbHabilitacao->setName   ('inCodHabilitacao');
$obCmbHabilitacao->setRotulo ( "Categoria" );
$obCmbHabilitacao->setNull   ( true );
$obCmbHabilitacao->setTitle  ( "Selecione a categoria de habilitação." );

//disponiveis
$obCmbHabilitacao->SetNomeLista1 ('inCodHabilitacaoDisponivel');
$obCmbHabilitacao->setCampoId1   ('cod_categoria');
$obCmbHabilitacao->setCampoDesc1 ('nom_categoria');
$obCmbHabilitacao->SetRecord1    ( $rsCategoriaHabilitacao );

//selecionados
$obCmbHabilitacao->SetNomeLista2 ('inCodAtributosSelecionados');
$obCmbHabilitacao->SetRecord2    ( new RecordSet() );

//instancia um radio para todos
$obRdStatusTodos = new Radio();
$obRdStatusTodos->setName( 'boStatus' );
$obRdStatusTodos->setId( 'boStatusTodos' );
$obRdStatusTodos->setRotulo( 'Status' );
$obRdStatusTodos->setTitle( 'Selecione o status do motorista.' );
$obRdStatusTodos->setLabel( 'Todos' );
$obRdStatusTodos->setValue( 'todos' );

//instancia um radio para ativo
$obRdStatusAtivo = new Radio();
$obRdStatusAtivo->setName( 'boStatus' );
$obRdStatusAtivo->setId( 'boStatusAtivo' );
$obRdStatusAtivo->setRotulo( 'Status' );
$obRdStatusAtivo->setTitle( 'Selecione o status do motorista.' );
$obRdStatusAtivo->setLabel( 'Ativo' );
$obRdStatusAtivo->setValue( 'ativo' );

//instancia um radio para ativo
$obRdStatusInativo = new Radio();
$obRdStatusInativo->setName( 'boStatus' );
$obRdStatusInativo->setId( 'boStatusInativo' );
$obRdStatusInativo->setRotulo( 'Status' );
$obRdStatusInativo->setTitle( 'Selecione o status do motorista.' );
$obRdStatusInativo->setLabel( 'Inativo' );
$obRdStatusInativo->setValue( 'inativo' );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->setAjuda('uc-03.02.11');
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Motorista' );

$obFormulario->addComponente( $obInCGMMotorista );
$obFormulario->addComponente( $obTipoBuscaMotorista );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obCmbHabilitacao );
$obFormulario->agrupaComponentes( array( $obRdStatusTodos, $obRdStatusAtivo, $obRdStatusInativo ) );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
