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
    * Página de Formulario de Homologar LDO
    * Data de Criação: 27/07/2009

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
require_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
require_once CAM_GF_PPA_COMPONENTES.'MontaVeiculoPublicitario.class.php';
require_once CAM_GF_LDO_COMPONENTES.'ISelectLDO.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "HomologarLDO";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

require $pgJs;

$stAcao = $request->get('stAcao');

// Campos Hidden
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue("cadastrarHomologacao");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("cadastrarHomologacao");

// Form
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//ITextBoxSelectPPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('preencheLDO', 'inCodPPA');");
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaParametrosGET('preencheLDO', 'inCodPPA');");
$obITextBoxSelectPPA->setHomologado(true);
$obITextBoxSelectPPA->setNull(false);

//ISelectLDO
$obISelectLDO = new ISelectLDO();
$obISelectLDO->setId($obISelectLDO->getName());
$obISelectLDO->setNull(false);
$obISelectLDO->obEvento->setOnChange("montaParametrosGET('montaSpanHomologacao', 'inCodPPA,inAnoLDO');");

// Span Homolagação PPA
$obSpnHomologacaoLDO = new Span;
$obSpnHomologacaoLDO->setID  ("spnHomologacaoLDO");

// Norma
$obIPopUpNorma = new IPopUpNorma();
$obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
$obIPopUpNorma->obInnerNorma->setRotulo("Número da Norma");
$obIPopUpNorma->obLblDataNorma->setRotulo( "Data da Norma" );
$obIPopUpNorma->obLblDataPublicacao->setRotulo( "Data da Publicação da Norma" );
$obIPopUpNorma->setExibeDataNorma(true);
$obIPopUpNorma->setExibeDataPublicacao(true);

$obTxtAnoInicio = new Hidden;
$obTxtAnoInicio->setName('stAnoInicio');
$obTxtAnoInicio->setId('stAnoInicio');

$obTxtAnoFinal = new Hidden;
$obTxtAnoFinal->setName('stAnoFinal');
$obTxtAnoFinal->setId('stAnoFinal');

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obTxtAnoFinal);
$obFormulario->addHidden($obTxtAnoInicio);

// Homologar PPA
$obFormulario->addTitulo('Homologação do PPA');
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->addComponente($obISelectLDO);

// Dados do Span Homologação do PPA
$obFormulario->addSpan($obSpnHomologacaoLDO);

// Título Norma
$obFormulario->addTitulo('Norma');
$obIPopUpNorma->geraFormulario($obFormulario);

$obBtnOk = new OK(true);
$obBtnOk->obEvento->setOnClick('validarFormulario()');

$obBtnLimpar = new Button;
$obBtnLimpar->setName('Limpar');
$obBtnLimpar->setValue('Limpar');
$obBtnLimpar->obEvento->setOnClick('limpaFormulario()');

$obFormulario->defineBarra(array($obBtnOk , $obBtnLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
