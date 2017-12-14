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
 * Formulario de filtro de acoes para validacao
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';

$stAcao = $request->get('stAcao');

$stPrograma = 'ValidarAcao';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

include_once $pgJs;
Sessao::remove('paginando');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('LSValidarAcao.php');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia um objeto do componente ITextBoxSelectPPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->setPreencheUnico(true);
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaParametrosGET('preencheLDO');");
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('preencheLDO');");

//Instancia um objeto Select
$obSlExercicioLDO = new Select();
$obSlExercicioLDO->setName    ('slExercicioLDO');
$obSlExercicioLDO->setId      ('slExercicioLDO');
$obSlExercicioLDO->setRotulo  ('Exercício LDO');
$obSlExercicioLDO->setTitle   ('Informe o exercicio da LDO');
$obSlExercicioLDO->setNull    (false);
$obSlExercicioLDO->addOption  ('', 'Selecione');

$obIPopUpPrograma = new BuscaInner                ($obForm);
$obIPopUpPrograma->setRotulo                      ('Programa');
$obIPopUpPrograma->setTitle                       ('Informe o programa.');
$obIPopUpPrograma->setId                          ('stNomPrograma');
$obIPopUpPrograma->obCampoCod->setId              ('inCodPrograma');
$obIPopUpPrograma->obCampoCod->setName            ('inCodPrograma');
$obIPopUpPrograma->obCampoCod->setSize            (10);
$obIPopUpPrograma->obCampoCod->setMaxLength       (9);
$obIPopUpPrograma->obCampoCod->setAlign           ('left');
$obIPopUpPrograma->obCampoCod->setMascara         ('9999');
$obIPopUpPrograma->obCampoCod->setPreencheComZeros('E');
$stFuncaoBusca = "
    abrePopUp('".CAM_GF_PPA_POPUPS."programa/FLProcurarPrograma.php','".$obForm->getName()."','".$obIPopUpPrograma->obCampoCod->getName()."','" . $obIPopUpPrograma->getId() . "','&inCodPPA='+jq('#inCodPPATxt').val()+'&','".Sessao::getId()."','800','550');
";
$obIPopUpPrograma->setFuncaoBusca($stFuncaoBusca);
$stOnChange = "
    ajaxJavaScriptSincrono('".CAM_GF_PPA_POPUPS.'programa/OCProcurarPrograma.php?'.Sessao::getId()."&stNomCampoCod=".$obIPopUpPrograma->obCampoCod->getName()."&stIdCampoDesc=".$obIPopUpPrograma->getId()."&stNomForm=".$obForm->getName()."&inCodPPA='+jq('#inCodPPATxt').val()+'&inNumPrograma='+this.value, 'buscaPrograma');
";
$obIPopUpPrograma->obCampoCod->obEvento->setOnBlur($stOnChange);
$obIPopUpPrograma->setNull(true);

//Instancia um label de intervalo.
$obLblIntervalo = new Label();
$obLblIntervalo->setValue(' até ');

//Instancia um textbox para o intervalo inicial da ação
$obTxtAcaoInicio = new TextBox();
$obTxtAcaoInicio->setName('inNumAcaoInicio');
$obTxtAcaoInicio->setRotulo('Ação');
$obTxtAcaoInicio->setTitle('Informe o intervalo de Códigos de Ação.');
$obTxtAcaoInicio->setInteiro(true);
$obTxtAcaoInicio->setMascara('9999');
$obTxtAcaoInicio->setPreencheComZeros('E');

//Instancia um textbox para o intervalo final da ação
$obTxtAcaoFim= new TextBox();
$obTxtAcaoFim->setName('inNumAcaoFim');
$obTxtAcaoFim->setRotulo('Ação');
$obTxtAcaoFim->setInteiro(true);
$obTxtAcaoFim->setMascara('9999');
$obTxtAcaoFim->setPreencheComZeros('E');

//Instancia um objeto Formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addTitulo        ('Filtro');

$obFormulario->addComponente    ($obITextBoxSelectPPA);
$obFormulario->addComponente    ($obSlExercicioLDO);
$obFormulario->addComponente    ($obIPopUpPrograma);
$obFormulario->agrupaComponentes(array($obTxtAcaoInicio,$obLblIntervalo,$obTxtAcaoFim));

$obFormulario->Ok               ();
$obFormulario->show             ();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
