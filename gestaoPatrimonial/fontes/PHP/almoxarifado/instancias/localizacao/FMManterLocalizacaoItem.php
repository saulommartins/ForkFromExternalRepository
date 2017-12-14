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
 Data de criação : 31/03/2006

  * @author Analista      : Diego
  * @author Desenvolvedor : Rodrigo D. Schreiner

  * @ignore

  * Casos de uso: uc-03.03.14
**/

/*
$Log$
Revision 1.13  2006/10/03 09:45:30  andre.almeida
Bug #7025#

Revision 1.12  2006/07/27 14:36:53  fernando
Validação do campo marca.

Revision 1.11  2006/07/20 21:13:00  fernando
alteração na padronização dos UC

Revision 1.10  2006/07/19 11:44:09  fernando
Inclusão do  Ajuda.

Revision 1.9  2006/07/18 17:48:40  fernando
alteração de hints

Revision 1.8  2006/07/07 18:38:04  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:53  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO ."RAlmoxarifadoLocalizacao.class.php"                               );
include_once(CAM_GP_ALM_NEGOCIO ."RAlmoxarifadoAlmoxarifado.class.php"                              );
include_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php"                                        );
include_once( CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php");

$stPrograma = "ManterLocalizacao";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$inCount = 0;

$obAlmoxarifadoLocalizacao = new RAlmoxarifadoLocalizacao();
$rsAlmoxarifado            = new Recordset;

$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->obRCGMAlmoxarife->obRCGM->setNumCgm(Sessao::read('numCgm'));
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->listarPermissao($rsAlmoxarifado,"",true);
$obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->consultar();
$codAlmoxarifadoPadrao = $obAlmoxarifadoLocalizacao->obRAlmoxarifadoAlmoxarife->obAlmoxarifadoPadrao->getCodigo();

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

$obHdnVal = new Hidden;
$obHdnVal->setName("stVal");
$obHdnVal->setValue("");

$obHdnVal = new Hidden;
$obHdnVal->setName("stAcao");
//$obHdnVal->setValue($stAcao);
$obHdnVal->setValue("alterarItens");

$obHdnLocalizacao = new Hidden;
$obHdnLocalizacao->setName("HdnLocalizacao");
$obHdnLocalizacao->setValue("");

$obHdnNomeUnidade = new Hidden;
$obHdnNomeUnidade->setName("HdnNomUnidade");
$obHdnNomeUnidade->setValue("");

$obHdnNomeItem = new Hidden;
$obHdnNomeItem->setName("HdnNomItem");
$obHdnNomeItem->setValue("");

$obHdnNomeMarca = new Hidden;
$obHdnNomeMarca->setName("HdnNomMarca");
$obHdnNomeMarca->setValue("");

$obCmbCodAlmoxarifado = new Select();
$obCmbCodAlmoxarifado->setRotulo            ("Almoxarifado"                  );
$obCmbCodAlmoxarifado->setTitle             ("Selecione o almoxarifado.");
$obCmbCodAlmoxarifado->setName              ("inCodAlmoxarifado"             );
$obCmbCodAlmoxarifado->setNull              (false                           );
$obCmbCodAlmoxarifado->setCampoID           ("codigo"                        );
$obCmbCodAlmoxarifado->addOption            ("","Selecione"                  );
$obCmbCodAlmoxarifado->obEvento->setOnChange("goOculto('Localizacao',false);");
$obCmbCodAlmoxarifado->setCampoDesc         ("[codigo] - [nom_a]"            );
$obCmbCodAlmoxarifado->preencheCombo        ($rsAlmoxarifado                 );
$obCmbCodAlmoxarifado->setValue             ($codAlmoxarifadoPadrao          );

if (!($codAlmoxarifadoPadrao == "")) {
 $js = "goOculto('Localizacao',false);";
 SistemaLegado::executaFrameOculto($js);
}

$obCmbCodLocalizacao = new Select();
$obCmbCodLocalizacao->setRotulo ("Localização"                   );
$obCmbCodLocalizacao->setTitle  ("Selecione a localização do item.");
$obCmbCodLocalizacao->setName   ("inCodLocalizacao"              );
$obCmbCodLocalizacao->setNull   (false                           );
$obCmbCodLocalizacao->addOption ("","Selecione"                  );

$obBscItem = new IMontaItemUnidade($obForm);
$obBscItem->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnChange("goOcultoProcessamento('Localizacao','Item');");
$obBscItem->obIPopUpCatalogoItem->setServico( false );
$obBscItem->obIPopUpCatalogoItem->setNull(false);

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setNull(false);
$obBscMarca->setRotulo("Marca");
$obBscMarca->setTitle("Informe a marca do item.");
$obBscMarca->obCampoCod->obEvento->setOnChange("goOcultoProcessamento('Localizacao','Marca');");

$obBtnIncluir = new Button;
$obBtnIncluir->setValue            ( "Incluir"                       );
$obBtnIncluir->obEvento->setOnClick( "goOculto('IncluirItem',false);");

$obSpnListaValores = new Span;
$obSpnListaValores->setID("spnListaValores");

$obBtnLimpar = new Button;
$obBtnLimpar->setValue            ( "Limpar"          );
$obBtnLimpar->obEvento->setOnClick( 'LimparDotacao();');

$obFormulario = new Formulario;

$obFormulario->addForm      ($obForm              );
$obFormulario->setAjuda     ("UC-03.03.14");
$obFormulario->addTitulo    ("Dados do Item"      );
$obBscItem->geraFormulario($obFormulario);
$obFormulario->addComponente($obBscMarca          );
$obFormulario->addComponente($obCmbCodAlmoxarifado);
$obFormulario->addComponente($obCmbCodLocalizacao );
$obFormulario->addSpan      ($obSpnListaValores   );
$obFormulario->addHidden    ($obHdnCtrl           );
$obFormulario->addHidden    ($obHdnVal            );
$obFormulario->addHidden    ($obHdnLocalizacao    );
$obFormulario->addHidden    ($obHdnNomeItem       );
$obFormulario->addHidden    ($obHdnNomeMarca      );
$obFormulario->addHidden    ($obHdnNomeUnidade    );

$obFormulario->OK();

$obFormulario->show();

?>
