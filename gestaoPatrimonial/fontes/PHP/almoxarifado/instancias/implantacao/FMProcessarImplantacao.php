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
    * Página de Formulário para Processar Implantação
    * Data de Criação   : 08/06/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-03.03.16

    $Id: FMProcessarImplantacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IMontaItemUnidadeTipo.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
include_once(CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php");
include_once '../../../../../../config.php';

$stPrograma = "ProcessarImplantacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::remove('itens');
Sessao::remove('lotes');
Sessao::write('itens',array());
Sessao::write('lotes',array());

$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( $stCtrl  );

$obHdnIdItem = new Hidden;
$obHdnIdItem->setName   ( "inIdItem" );
$obHdnIdItem->setValue ( ''  );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo ( "Exercício"   );
$obLblExercicio->setId     ( "stExercicio" );
$obLblExercicio->setValue  ( Sessao::getExercicio() );

$obTxtExercicio = new Exercicio;

$obSpnAlmoxarifado = new Span;
$obSpnAlmoxarifado->setId ( 'spnAlmoxarifado' );

$obItemUnidadeTipo = new IMontaItemUnidadeTipo($obForm);
$obItemUnidadeTipo->obIMontaItemUnidade->obIPopUpCatalogoItem->setServico( false );
$obItemUnidadeTipo->obIMontaItemUnidade->obIPopUpCatalogoItem->setVerificacaoMovimentacaoItem(false);
$obItemUnidadeTipo->obIMontaItemUnidade->obIPopUpCatalogoItem->setTipoNaoInformado( true );
$obItemUnidadeTipo->obIMontaItemUnidade->obIPopUpCatalogoItem->setNull(false);
$obItemUnidadeTipo->obIMontaItemUnidade->obIPopUpCatalogoItem->setPreencheTipoNaoInformado( true );
$obItemUnidadeTipo->obIMontaItemUnidade->setPreencheUnidadeNaoInformada( true );
$obItemUnidadeTipo->obIMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur("montaParametrosGET('montaFormLotes');montaParametrosGET('montaSpnAtributos');");

$obSpnAtributos = new Span;
$obSpnAtributos->setId( 'spnAtributos' );

$obMarca = new IPopUpMarca($obForm);
$obMarca->setTitle("Informe a marca do item.");
$obMarca->setNull( true );
$obMarca->setNull(false);

$obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
$obCentroCusto->obCampoCod->setSize( 10 );
$obCentroCusto->setNull ( false );

$obSpnFormLotes = new Span;
$obSpnFormLotes->setId("spnFormLotes");

$obQuantidade = new Quantidade;
$obQuantidade->setRotulo ( 'Quantidade' );
$obQuantidade->setSize( 15 );
$obQuantidade->setNull(false);

$obValorTotal = new ValorTotal;
$obValorTotal->setRotulo( 'Valor Total de Mercado' );
$obValorTotal->setSize( 15 );
$obValorTotal->setNull(false);

$obFormulario =  new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ("UC-03.03.16");
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Dados da Implantação" );
$obFormulario->addComponente( $obLblExercicio );
$obFormulario->addSpan( $obSpnAlmoxarifado );
$obFormulario->addTitulo( "Dados do Item" );
$obFormulario->addHidden( $obHdnIdItem );
$obItemUnidadeTipo->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obMarca );
$obFormulario->addComponente( $obCentroCusto );
$obFormulario->addSpan      ( $obSpnFormLotes );
$obFormulario->addSpan      ( $obSpnAtributos );
$obFormulario->addTitulo( "Saldos" );
$obFormulario->addComponente( $obQuantidade );
$obFormulario->addComponente( $obValorTotal );
$obFormulario->Ok();
$obFormulario->Show();

$jsOnLoad = "executaFuncaoAjax('montaCampoAlmoxarifado');\n";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
