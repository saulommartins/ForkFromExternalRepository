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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 16/01/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
require_once CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php";

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoCargo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

/* Cria a combo onde ficará os tipos de Cargos */
$arTipoCargo[0]['cod_tipo'] = 10;
$arTipoCargo[0]['descricao'] = 'Comissionado';
$arTipoCargo[1]['cod_tipo'] = 20;
$arTipoCargo[1]['descricao'] = 'Efetivo';
$arTipoCargo[2]['cod_tipo'] = 30;
$arTipoCargo[2]['descricao'] = 'Eletivo';
$arTipoCargo[3]['cod_tipo'] = 90;
$arTipoCargo[3]['descricao'] = 'Outros';

$rsTipoCargo = new RecordSet;
$rsTipoCargo->preenche( $arTipoCargo );

$obTipoCargo = new  Select();
$obTipoCargo->setName      ( 'codTipoCargo_[cod_cargo]' );
$obTipoCargo->setId        ( 'codTipoCargo_[cod_cargo]' );
$obTipoCargo->setValue     ( '[tipo]' );
$obTipoCargo->setCampoId   ( 'cod_tipo'  );
$obTipoCargo->setCampoDesc ( 'descricao' );
$obTipoCargo->addOption    ( '', 'Selecione' );
$obTipoCargo->preencheCombo( $rsTipoCargo );

// Faz a busca para cirar a listagem dos cargos
$obTPessoalCargo = new TPessoalCargo();
$obTPessoalCargo->recuperaListagemCargo( $rsTPessoalCargo );

/* Monta a table para a listagem de todos os campos */
$table = new Table   ();
$table->setRecordset  ( $rsTPessoalCargo );
$table->setSummary    ('Configuração de Tipo de Cargo');
//$table->setConditional( true , "#ddd" );

$table->Head->addCabecalho( 'Cargo' , 50  );
$table->Head->addCabecalho( 'Tipo do Cargo' , 50  );

$table->Body->addCampo      ( '[cod_cargo] - [descricao]' , 'E');
$table->Body->addComponente ( $obTipoCargo );

$table->montaHTML();
$stLista = $table->getHTML();

//Define Span para DataGrid
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );
$obSpnLista->setValue ( $stLista );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->addTitulo( "Dados" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );

$obFormulario->addSpan( $obSpnLista );

$obFormulario->defineBarra( array( new Ok(true) ) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
