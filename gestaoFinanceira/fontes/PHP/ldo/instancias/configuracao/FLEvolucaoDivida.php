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
    * Página de filtro da evolução da dívida
    * Data de Criação   : 03/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
include_once CAM_GF_PPA_MAPEAMENTO.'TPPA.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOTipoIndicadores.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCEvolucaoDivida.php';

Sessao::remove('paginando');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('FMEvolucaoDivida.php');

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
$obITextBoxSelectPPA->setHomologado(true);

//Instancia um objeto Select
$obSlExercicioLDO = new Select();
$obSlExercicioLDO->setName    ('slExercicioLDO');
$obSlExercicioLDO->setId      ('slExercicioLDO');
$obSlExercicioLDO->setRotulo  ('Exercício LDO');
$obSlExercicioLDO->setTitle   ('Informe o exercicio da LDO');
$obSlExercicioLDO->setNull    (false);
$obSlExercicioLDO->addOption  ('','Selecione');

$obTLDOTipoIndicadores = new TLDOTipoIndicadores;
$obTLDOTipoIndicadores->recuperaTodos($rsTipoIndicadores);

//Instancia um objeto Select
$obCmbIndicadorSelic = new Select();
$obCmbIndicadorSelic->setName      ('inCodSelic');
$obCmbIndicadorSelic->setId        ('inCodSelic');
$obCmbIndicadorSelic->setRotulo    ('Selic');
$obCmbIndicadorSelic->setTitle     ('Informe o indicador da Selic');
$obCmbIndicadorSelic->setCampoId   ('cod_tipo_indicador');
$obCmbIndicadorSelic->setCampoDesc ('descricao');
$obCmbIndicadorSelic->setNull      (false);
$obCmbIndicadorSelic->addOption    ('','Selecione');
$obCmbIndicadorSelic->preencheCombo($rsTipoIndicadores);

//Instancia um objeto Formulario
$obFormulario = new Formulario  ();
$obFormulario->addForm          ($obForm);
$obFormulario->addHidden        ($obHdnAcao);
$obFormulario->addTitulo        ('Filtro');

$obITextBoxSelectPPA->geraFormulario($obFormulario);
$obFormulario->addComponente    ($obSlExercicioLDO);
$obFormulario->addComponente    ($obCmbIndicadorSelic);

$obFormulario->Ok               ();
$obFormulario->show             ();

if ($obITextBoxSelectPPA->getRecordSet()->getNumLinhas() > 0) {
    $jsOnload = "montaParametrosGET('preencheLDO');";
}

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
