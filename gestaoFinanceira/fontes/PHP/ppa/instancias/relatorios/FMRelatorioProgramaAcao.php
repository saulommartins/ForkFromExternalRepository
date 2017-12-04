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
 * Página de Formulario que filtra de Relatórios de Programas X Ações
 * Data de Criação: 09/02/2009
 *
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * @package URBEM
 * @subpackage
 *
 * @uc UC-02.09.14
 *
 * $Id $
 *
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';
require_once(CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
require_once(CAM_GF_PPA_NEGOCIO."/RPPAHomologarPPA.class.php");
require_once(CAM_GRH_PES_COMPONENTES . 'IFiltroContrato.class.php');
require_once '../../../../../../gestaoFinanceira/fontes/PHP/ppa/classes/visao/VPPAHomologarPPA.class.php';
include_once 'JSRelatorioAcao.php';

//Instanciando a Classe de Controle e de Visao de Homologar para Trazer o PPA vigente pelo Exercício
$obController = new RPPAHomologarPPA;
$obVisao = new VPPAHomologarPPA($obController);

$rsRecordSet = $obVisao->pesquisaPPANorma($stFiltro);

$inCount = count($rsRecordSet->arElementos);
$inAnoExercicio = substr($_SESSION['exercicio'], 5,-2);

for ($i = 0; $i < $inCount; $i++) {
    $arCampos = $rsRecordSet->arElementos[$i];

    if ($arCampos['ano_inicio'] <= $inAnoExercicio && $inAnoExercicio <= $arCampos['ano_final']) {
        $inCodPPA = $arCampos['cod_ppa'];
    }
}

$stEntidades = $obVisao->montarEntidades();
$obHdnEntidade = new Hidden;
$obHdnEntidade->setName("inCodEntidade");
$obHdnEntidade->setId("inCodEntidade");
$obHdnEntidade->setValue($stEntidades);

$obHdnAssinatura = new Hidden;
$obHdnAssinatura->setName("boAssinaturas");
$obHdnAssinatura->setId("boAssinaturas");
$obHdnAssinatura->setValue('n');

$pgProc = 'PRRelatorioProgramaAcao.php';
$stAcao = $request->get('stAcao');

$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue("encaminhaRelatorioProgramaAcao");

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("encaminhaRelatorioProgramaAcao");

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setCodPPA($inCodPPA);
$obITextBoxSelectPPA->setNull(false);

$obIniPrograma = new Inteiro();
$obIniPrograma->setTitle('Programa');
$obIniPrograma->setRotulo('Programa');
$obIniPrograma->setId('inNumProgramaIni');
$obIniPrograma->setName('inNumProgramaIni');
$obIniPrograma->setMaxLength(4);

$obLabelPrograma = new Label();
$obLabelPrograma->setValue('até');

$obFimPrograma = new Inteiro();
$obFimPrograma->setId('inNumProgramaFim');
$obFimPrograma->setName('inNumProgramaFim');
$obFimPrograma->setMaxLength(4);

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnEntidade);
$obFormulario->addHidden($obHdnAssinatura);
$obFormulario->addTitulo('Anexo de Programa Diagnóstico, Diretrizes, Objetivos, e Metas da Administração');
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->agrupaComponentes(array($obIniPrograma, $obLabelPrograma, $obFimPrograma));

$obMontaAssinaturas = new IMontaAssinaturas();
$obMontaAssinaturas->obRadioAssinaturasSim->obEvento->setOnClick("validaSessao('s');");
$obMontaAssinaturas->obRadioAssinaturasNao->obEvento->setOnClick("validaSessao('n');");

$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
