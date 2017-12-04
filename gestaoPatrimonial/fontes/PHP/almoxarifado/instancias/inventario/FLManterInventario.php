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
    * Página de Formulario de Manter Inventario
    * Data de Criação: 25/10/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id:$

    * Casos de uso: uc-03.03.15
*/

/*
    $Log:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once( CAM_GP_ALM_COMPONENTES."ISelectMultiploAlmoxarifadoAlmoxarife.class.php" );
include_once (CAM_GP_ALM_COMPONENTES."ITextBoxSelectCatalogo.class.php" );
include_once ( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php" );
include_once ( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php" );
include_once ( CAM_GP_ALM_COMPONENTES."IPopUpCentroCustoUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInventario";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setId    ( "stAcao"            );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo ( "Exercício"   );
$obTxtExercicio->setName   ( "stExercicio" );
$obTxtExercicio->setValue  ( Sessao::getExercicio() );
$obTxtExercicio->setSize ( 5 );
$obTxtExercicio->setMaxLength ( 4 );
$obTxtExercicio->setTitle ( "Informe o exercício." );

$obSelectAlmoxarifado = new ISelectMultiploAlmoxarifadoAlmoxarife;

$obTxtCodInventario = new TextBox;
$obTxtCodInventario->setRotulo ( "Código do Inventário"   );
$obTxtCodInventario->setName   ( "inCodInventario" );
$obTxtCodInventario->setTitle  ( "Informe o código do inventário."   );

$obDataInventario = new Data();
$obDataInventario->setName  ( 'dtDataInventario'   );
$obDataInventario->setId    ( 'dtDataInventario'   );
$obDataInventario->setRotulo( 'Data do Inventário' );
$obDataInventario->setTitle ( 'Informe a data do inventário.' );

$obTxtObservacao = new TextBox;
$obTxtObservacao->setRotulo    ( "Observação"           );
$obTxtObservacao->setTitle     ( "Informe a observação" );
$obTxtObservacao->setName      ( "stObservacao"         );
$obTxtObservacao->setId        ( "stObservacao"         );
$obTxtObservacao->setValue     ( isset($stObservacao) ? $stObservacao : null);
$obTxtObservacao->setSize      ( 50                     );
$obTxtObservacao->setMaxLength ( 160                    );
$obCmbTipoBusca = new TipoBusca($obTxtObservacao);

$jsMontaClassificacao = "montaParametrosGET('montaClassificacaoFiltro');";
$obSelectCatalogo = new ITextBoxSelectCatalogo();
$obSelectCatalogo->setNaoPermiteManutencao(true);
$obSelectCatalogo->obTextBox->obEvento->setOnChange($jsMontaClassificacao);
$obSelectCatalogo->obSelect->obEvento->setOnChange($jsMontaClassificacao);

$spnClassificacao = new Span();
$spnClassificacao->setId( 'spnClassificacao' );

$obIPopUpItem = new IPopUpItem($obForm);
$obIPopUpItem->setObrigatorio(false);
$obIPopUpItem->setRetornaUnidade(false);
$obIPopUpItem->setServico(false);

$obMarca = new IPopUpMarca($obForm);
$obMarca->setTitle("Informe a marca do item.");
$obMarca->obCampoCod->setId('inCodMarca');
$obMarca->setNull (true);
$obMarca->setObrigatorioBarra(false);

$obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
$obCentroCusto->setNull(true);
$obCentroCusto->obCampoCod->setId('inCodCentroCusto');
$obCentroCusto->setObrigatorioBarra(false);

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addHidden    ( $obHdnAcao      );
$obFormulario->addComponente( $obTxtExercicio );
$obFormulario->addComponente( $obSelectAlmoxarifado );
$obFormulario->addComponente( $obTxtCodInventario );
$obFormulario->addComponente( $obDataInventario );
$obFormulario->addComponente( $obCmbTipoBusca );
$obFormulario->addComponente( $obSelectCatalogo );
$obFormulario->addSpan( $spnClassificacao );
$obFormulario->addComponente( $obIPopUpItem );
$obFormulario->addComponente( $obMarca );
$obFormulario->addComponente( $obCentroCusto );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
