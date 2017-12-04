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
    * Página de Formulario de Homologar PPA
    * Data de Criação: 26/09/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.12
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
require_once(CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php");
require_once(CAM_GF_PPA_COMPONENTES."MontaVeiculoPublicitario.class.php");
require_once(CAM_GF_PPA_NEGOCIO."/RPPAHomologarPPA.class.php");
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';

//Instanciando a Classe de Controle e de Visao
$obController = new RPPAHomologarPPA;
$obVisao = new VPPAHomologarPPA($obController);

//Define o nome dos arquivos PHP
$stPrograma = "HomologarPPA";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";

include_once($pgJs);

$stAcao = $request->get('stAcao');

### Campos Hidden ###
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue("cadastrarHomologacao");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("cadastrarHomologacao");

// Variávelde controle para o método calcularDiferencaReceitaDespesa
$obHdnCodPPASelecionado = new Hidden;
$obHdnCodPPASelecionado->setName("inCodPPASelecionado");
$obHdnCodPPASelecionado->setId("inCodPPASelecionado");
$obHdnCodPPASelecionado->setValue("");

### Form ###
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$stFiltro = " ppa.fn_verifica_homologacao(ppa.cod_ppa) = false ";
$rsRecordSet = $obVisao->pesquisaPPAHomologacao($stFiltro);

### ITextBoxSelectPPA ###
$obITextBoxSelectPPA = new ITextBoxSelectPPA($rsRecordSet);
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('montaSpanHomologacao', 'inCodPPA');");
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaParametrosGET('montaSpanHomologacao', 'inCodPPA');");
$obITextBoxSelectPPA->setNull(false);

### Span Homolagação PPA ###
$obSpnHomologacaoPPA = new Span;
$obSpnHomologacaoPPA->setID("spnHomologacaoPPA");

### Norma ###
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
$obFormulario->addHidden($obHdnCodPPASelecionado);
$obFormulario->addHidden($obTxtAnoFinal);
$obFormulario->addHidden($obTxtAnoInicio);

### Homologar PPA ###
$obFormulario->addTitulo('Homologação do PPA');
$obFormulario->addComponente($obITextBoxSelectPPA);

### Dados do Span Homologação do PPA ###
$obFormulario->addSpan($obSpnHomologacaoPPA);

### Título Norma ###
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
