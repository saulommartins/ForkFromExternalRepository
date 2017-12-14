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
    Página de formulário Inclução da Localização
    Data de criação : 27/01/2006

    * @author Analista      : Diego
    * @author Desenvolvedor : Rodrigo D. Schreiner

    * @ignore

    * Casos de uso: uc-03.03.14

    $Id: FMManterLocalizacao.php 61639 2015-02-19 13:05:36Z diogo.zarpelon $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO ."RAlmoxarifadoLocalizacao.class.php";
include_once CAM_GP_ALM_NEGOCIO ."RAlmoxarifadoAlmoxarifado.class.php";
include_once CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php";
include_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";

$stPrograma = "ManterLocalizacao";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$inCodLocalizacao = $request->get('inCodLocalizacao');
$stLocalizacao = $request->get('stLocalizacao');

include_once($pgJs);

$inCount = 0;

$obAlmoxarifadoLocalizacao = new RAlmoxarifadoLocalizacao();
$rsAlmoxarifado            = new Recordset;

$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCgm( Sessao::read('numCgm') );
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->listarPermissao( $rsAlmoxarifado,"",true);
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->consultar();
$codAlmoxarifadoPadrao = $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->getCodigo();

$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->setCodigo( $_REQUEST['inCodAlmoxarifado'] );
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->consultar();
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->obRCGMAlmoxarifado->getNomCGM()."*";

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

$obHdnEval = new HiddenEval;
$obHdnEval->setName("stEval");
$obHdnEval->setValue("");

$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$stLocalizacao = $_REQUEST['stLocalizacao'];

$obHdnLocalizacao = new Hidden;
$obHdnLocalizacao->setName("HdnLocalizacao");
$obHdnLocalizacao->setValue(($stAcao == "alterar") ? $stLocalizacao : null);
$obHdnLocalizacao->setNull(false);

$obHdnCodLocalizacao = new Hidden;
$obHdnCodLocalizacao->setName("inCodLocalizacao");
$obHdnCodLocalizacao->setValue(($stAcao == "alterar") ? $inCodLocalizacao : null);

$obHdnNomeUnidade = new Hidden;
$obHdnNomeUnidade->setName("HdnNomUnidade");
$obHdnNomeUnidade->setValue("");

$obHdnNomeItem = new Hidden;
$obHdnNomeItem->setName("HdnNomItem");
$obHdnNomeItem->setValue("");

$obHdnNomeMarca = new Hidden;
$obHdnNomeMarca->setName("HdnNomMarca");
$obHdnNomeMarca->setValue("");

if ($stAcao != "alterar") {
    $obCmbCodAlmoxarifado = new Select();
    $obCmbCodAlmoxarifado->setRotulo            ("Almoxarifado"                        );
    $obCmbCodAlmoxarifado->setTitle             ("Selecione os almoxarifados.");
    $obCmbCodAlmoxarifado->setName              ("inCodAlmoxarifado"                   );
    $obCmbCodAlmoxarifado->setId                ("inCodAlmoxarifado"                   );
    $obCmbCodAlmoxarifado->setNull              (false                                 );
    $obCmbCodAlmoxarifado->setCampoID           ("codigo"                              );
    $obCmbCodAlmoxarifado->addOption            ("","Selecione"                        );
    $obCmbCodAlmoxarifado->obEvento->setOnChange("goOculto('FMontaLocalizacao',false);");
    $obCmbCodAlmoxarifado->setCampoDesc         ("[codigo] - [nom_a]");
    $obCmbCodAlmoxarifado->preencheCombo        ($rsAlmoxarifado                       );
    $obCmbCodAlmoxarifado->setValue             ($codAlmoxarifadoPadrao                );
} else {
    $inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];
    $obCmbCodAlmoxarifado = new Hidden;
    $obCmbCodAlmoxarifado->setName("inCodAlmoxarifado");
    $obCmbCodAlmoxarifado->setId  ("inCodAlmoxarifado");
    $obCmbCodAlmoxarifado->setValue($inCodAlmoxarifado);

    $obLblLocalizacao = new Label;
    $obLblLocalizacao->setRotulo("Almoxarifado");
    $obLblLocalizacao->setValue ($inCodAlmoxarifado." - ".$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->obRCGMAlmoxarifado->getNomCGM());

    Sessao::write('inCodAlmoxarifado', $inCodAlmoxarifado);
    Sessao::write('inNomAlmoxarifado', $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarifado->obRCGMAlmoxarifado->getNomCGM());
}

if ( (!($codAlmoxarifadoPadrao == "")) || ($stAcao == "alterar") ) {
    $js = "goOculto( 'FMontaLocalizacao', false );";
    SistemaLegado::executaFrameOculto($js);
}

$obSpnListaLocalizacao = new Span;
$obSpnListaLocalizacao->setId("spnListaLocalizacao");

$obBscItem = new IMontaItemUnidade( $obForm );
$obBscItem->obIPopUpCatalogoItem->setServico( false );
$obBscItem->obIPopUpCatalogoItem->setObrigatorioBarra (true);

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setNull(true);
$obBscMarca->setRotulo("Marca");
$obBscMarca->setObrigatorioBarra(true);
$obBscMarca->setTitle("Informe a marca do item.");
$obBtnIncluir = new Button;
$obBtnIncluir->setValue            ( "Incluir"                      );
$obBtnIncluir->obEvento->setOnClick( "goOculto('IncluirItem',true);");

$obSpnListaValores = new Span;
$obSpnListaValores->setID("spnListaValores");

$obBtnLimpar = new Button;
$obBtnLimpar->setValue            ( "Limpar"          );
$obBtnLimpar->obEvento->setOnClick( 'LimparDadosItem();');

$obFormulario = new Formulario;

Sessao::write('arValores', array());

$obFormulario->addTitulo("Dados da Localização" );
$obFormulario->addForm  ($obForm                );
$obFormulario->setAjuda ("UC-03.03.14");

if ($stAcao != "alterar") {
    $obFormulario->addComponente($obCmbCodAlmoxarifado);
} else {
    $obFormulario->addHidden($obCmbCodAlmoxarifado);
    $obFormulario->addComponente($obLblLocalizacao);
}

$obFormulario->addSpan      ($obSpnListaLocalizacao );
$obFormulario->addTitulo    ("Dados do Item"        );
$obBscItem->geraFormulario($obFormulario);
$obFormulario->addComponente($obBscMarca            );
$obFormulario->defineBarra  (array($obBtnIncluir, $obBtnLimpar), "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;");
$obFormulario->addSpan      ($obSpnListaValores     );
$obFormulario->addHidden    ($obHdnCtrl             );
$obFormulario->addHidden    ($obHdnEval  , true     );
$obFormulario->addHidden    ($obHdnAcao             );
$obFormulario->addHidden    ($obHdnLocalizacao      );
$obFormulario->addHidden    ($obHdnCodLocalizacao   );
$obFormulario->addHidden    ($obHdnNomeItem         );
$obFormulario->addHidden    ($obHdnNomeMarca        );
$obFormulario->addHidden    ($obHdnNomeUnidade      );

if ($stAcao == "incluir") {
    $obBtnOk = new Ok;

    $obBtnLimparGeral = new Button;
    $obBtnLimparGeral->setValue( "Limpar" );
    $obBtnLimparGeral->obEvento->setOnClick( 'LimpaTela();');

    $obFormulario->defineBarra( array( $obBtnOk, $obBtnLimparGeral) );
} else {
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao;
    $obFormulario->Cancelar( $stLocation );
}

$obFormulario->show();

?>