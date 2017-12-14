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
    * Página de Filtro Conceder Remissão

    * Data de Criação: 03/05/2010

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Eduardo Paculski Schitz
    * @ignore

    * $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_NORMAS_CLASSES.'componentes/IPopUpNorma.class.php';
include_once CAM_GA_ADM_COMPONENTES.'IPopUpFuncao.class.php';
include_once CAM_GT_ARR_COMPONENTES.'MontaGrupoCredito.class.php';
include_once CAM_GT_DAT_NEGOCIO.'RDATConfiguracao.class.php';
include_once CAM_GT_CEM_COMPONENTES.'IPopUpEmpresaIntervalo.class.php';
include_once CAM_GT_CIM_COMPONENTES.'IPopUpImovelIntervalo.class.php';

if (empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = 'incluir';
}

//Define o nome dos arquivos PHP
$stPrograma = 'ConcederRemissao';
$pgForm     = 'FM'.$stPrograma.'.php';
$pgProc     = 'PR'.$stPrograma.'.php';
$pgOcul     = 'OC'.$stPrograma.'.php?'.Sessao::getId();
$pgJs       = 'JS'.$stPrograma.'.js';

$obRadioTipoCredito = new Radio;
$obRadioTipoCredito->setName   ('boTipoLancamentoManual');
$obRadioTipoCredito->setTitle  ('Efetuar Lançamento');
$obRadioTipoCredito->setRotulo ('Efetuar Lançamento por ');
$obRadioTipoCredito->setValue  ('credito');
$obRadioTipoCredito->setLabel  ('Crédito');
$obRadioTipoCredito->setNull   (true);
$obRadioTipoCredito->setChecked(true);

$obRadioTipoGrupoCredito = new Radio;
$obRadioTipoGrupoCredito->setName ('boTipoLancamentoManual');
$obRadioTipoGrupoCredito->setValue('grupo_credito');
$obRadioTipoGrupoCredito->setLabel('Grupo de Crédito');
$obRadioTipoGrupoCredito->setNull (true);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($_REQUEST['stAcao']);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($_REQUEST['stCtrl']);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction($pgForm);
$obForm->settarget('telaPrincipal');

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addHidden            ($obHdnCtrl);
$obFormulario->addComponenteComposto($obRadioTipoCredito, $obRadioTipoGrupoCredito);
$obFormulario->ok();
$obFormulario->show();
