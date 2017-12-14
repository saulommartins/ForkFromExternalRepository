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
 * Página de Formulario para importar PPA
 * Data de Criação: 01/10/2008

 * @author Analista: Heleno Menezes dos Santos
 * @author Desenvolvedor: Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @ignore

 $Id: FMImportarPPA.php 36336 2008-12-08 21:35:52Z pedro.medeiros $

 * Casos de uso: UC-02.09.12
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';

include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';
include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';
include_once CAM_GF_PPA_VISAO . 'VPPAManterPPA.class.php';

# Instanciando a Classe de Controle e de Visao
$obController = new RPPAManterPPA;
$obVisao = new VPPAManterPPA($obController);

# Encontra PPA anterior.
$inExercicio = Sessao::getExercicio();
$inAnterior  = $inExercicio + ($inExercicio % 4) - (($inExercicio % 2) ? 4 : 2);

$stFiltro    = "ppa.cod_ppa IS NOT NULL AND ppa.ano_inicio = '$inAnterior' ORDER BY ppa.ano_inicio";
$rsPPA       = $obVisao->pesquisaPPAImportacao($stFiltro);

# Define o nome dos arquivos PHP
$stPrograma = 'ManterPPA';
$pgOcul     = 'OC' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma . '.php';

$stAcao = $request->get('stAcao');

# Campos Hidden
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("importar");

# Define Form
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

# Define objeto de formulário
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

# Define componente de seleção de PPA.
$obITextBoxSelectPPA = new ITextBoxSelectPPA($rsPPA);
$obITextBoxSelectPPA->setRotulo('PPA');
$obITextBoxSelectPPA->setNull(false);

# Inclui componente de seleção de PPA.
$obFormulario->addTitulo('Importar PPA');
$obFormulario->addComponente($obITextBoxSelectPPA);

$obFormulario->ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
