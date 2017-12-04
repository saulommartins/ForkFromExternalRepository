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
    * Data de Criação: 23/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: FLManterItem.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.12
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoItem.class.php' );
include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php");

$stPrograma = "ManterItem";
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

//instancia um textbox para o codigo do item
$obTxtCodItem = new TextBox();
$obTxtCodItem->setName( 'inCodItem' );
$obTxtCodItem->setRotulo( 'Código do Item' );
$obTxtCodItem->setTitle( 'Informe o código do item.' );
$obTxtCodItem->setInteiro( true );

//instancia um textbox para a descricao do item
$obTxtDescricaoItem = new TextBox();
$obTxtDescricaoItem->setName( 'stDescricaoItem' );
$obTxtDescricaoItem->setRotulo( 'Descrição do Item' );
$obTxtDescricaoItem->setTitle( 'Informe a descrição do item.' );
$obTxtDescricaoItem->setSize( 60 );

//instancia um tipo busca para a descricao do item
$obTipoBuscaItem = new TipoBusca( $obTxtDescricaoItem );

//recupera os tipos de item
$obTFrotaTipoItem = new TFrotaTipoItem();
$obTFrotaTipoItem->recuperaTodos( $rsTipoItem );

//instancia um selectmultiplo para o tipo do item
$obCmbTipoItem = new SelectMultiplo();
$obCmbTipoItem->setName   ('inCodTipoItem');
$obCmbTipoItem->setRotulo ( "Tipo" );
$obCmbTipoItem->setNull   ( true );
$obCmbTipoItem->setTitle  ( "Selecione o tipo do item." );

//disponiveis
$obCmbTipoItem->SetNomeLista1 ('inCodTipoItemDisponivel');
$obCmbTipoItem->setCampoId1   ('cod_tipo');
$obCmbTipoItem->setCampoDesc1 ('descricao');
$obCmbTipoItem->SetRecord1    ( $rsTipoItem );

//selecionados
$obCmbTipoItem->SetNomeLista2 ('inCodTipoItemSelecionados');
$obCmbTipoItem->SetRecord2    ( new RecordSet() );

//instancia um select  para o tipo do item
$obSlTipoItem = new Select();
$obSlTipoItem->setName( 'slTipoItem' );
$obSlTipoItem->setRotulo( 'Tipo' );
$obSlTipoItem->setTitle( 'Informe o tipo do item.' );
$obSlTipoItem->setCampoId( 'cod_tipo' );
$obSlTipoItem->setCampoDesc( 'descricao' );
$obSlTipoItem->addOption( '','Selecione' );
$obSlTipoItem->preencheCombo( $rsTipoItem );
$obSlTipoItem->setNull( false );

//monta o formulário
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obFormulario->addTitulo    ( 'Dados do Filtro' );

$obFormulario->addComponente( $obTxtCodItem );
$obFormulario->addComponente( $obTipoBuscaItem );
$obFormulario->addComponente( $obCmbTipoItem );

$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
