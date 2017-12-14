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
    * Página Processamento de Relatório Recurso Destinação
    * Data de Criação: 11/02/2009

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Fellipe Esteves dos Santos

    * @package URBEM
    * @subpackage

    * Casos de uso: UC-02.09.15
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';
require_once CAM_GF_PPA_COMPONENTES . 'IPopUpPrograma.class.php';
require_once CAM_GF_PPA_COMPONENTES . 'IPopUpRecurso.class.php';
require_once(CAM_GF_PPA_NEGOCIO."/RPPAHomologarPPA.class.php");
require_once(CAM_GRH_PES_COMPONENTES . 'IFiltroContrato.class.php');
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';

//Instanciando a Classe de Controle e de Visao de Homologar para Trazer o PPA vigente pelo Exercício
$obController = new RPPAHomologarPPA;
$obVisao = new VPPAHomologarPPA($obController);

$rsRecordSet = $obVisao->pesquisaPPANorma($stFiltro);

$inCount = count($rsRecordSet->arElementos);
$inAnoExercicio = Sessao::read("exercicio");

for ($i = 0; $i < $inCount; $i++) {
    $arCampos = $rsRecordSet->arElementos[$i];

    if ($arCampos['ano_inicio'] <= $inAnoExercicio && $inAnoExercicio <= $arCampos['ano_final']) {
        $inCodPPA = $arCampos['cod_ppa'];
    }
}

$stPrograma = "RelatorioRecursoDestinacao";
$pgProc = "PR".$stPrograma.".php";

$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue("encaminhaRelatorioRecursoDestinacao");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("encaminhaRelatorioRecursoDestinacao");

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setCodPPA($inCodPPA);
$obITextBoxSelectPPA->setNull(false);

$obIPopUpPrograma = new IPopUpPrograma($obForm);
$obIPopUpPrograma->setNull(true);

$obIPopUpRecurso = new IPopUpRecurso($obForm);
$obIPopUpRecurso->obInnerRecurso->setRotulo("Destinação do Recurso");
$obIPopUpRecurso->setNull(true);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addTitulo('Relatório demonstrativo compatibilização das fontes com a destinação dos recursos - DR');
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->addComponente($obIPopUpRecurso);
$obFormulario->addComponente($obIPopUpPrograma);
$obFormulario->ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
