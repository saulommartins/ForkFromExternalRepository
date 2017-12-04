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
 * Página de filtro de Manter Compensação da Renúncia de Receita
 * Data de Criação: 23/03/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros>
 * @package GF
 * @subpackage LDO
 * @uc uc-02.10.16 - Manter Compensação da Renúncia de Receita
 */

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$stModulo = 'ManterRenunciaReceita';
$pgProc   = 'PR'.$stModulo.'.php';
$pgJS     = 'JS'.$stModulo.'.php';
$pgOcul   = 'OC'.$stModulo.'.php';
$pgList   = 'LS'.$stModulo.'.php';

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

//Instancia form
$obForm = new Form;
$obForm->setAction( 'LSManterRenunciaReceita.php' );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addTitulo("Dados para filtro");

// Monta o select para buscar o PPA e de acordo com sua escolha preenche a combo de exercício, usando os valores iniciais e finais para
// preencher a combo de exercicios. Isso é feito via JS pelo arquivo JS do programa, sendo chamado pelo método montaExercicio()
$obITextBoxSelectPPA = new ITextBoxSelectPPA;
$obITextBoxSelectPPA->setPreencheUnico(true);
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaParametrosGET('montaExercicio');");
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaParametrosGET('montaExercicio');");
$obFormulario->addComponente($obITextBoxSelectPPA);

// Monta o componente que receberá valores de acordo com o que for selecionado pelo select de PPA
$obSelectExercicio = new Select;
$obSelectExercicio->setId('inAnoLDO');
$obSelectExercicio->setName('inAnoLDO');
$obSelectExercicio->setTitle('Selecione o exercício.');
$obSelectExercicio->setRotulo('Exercício LDO');
$obSelectExercicio->addOption('', 'Selecione');
$obSelectExercicio->setNull(false);
$obSelectExercicio->setStyle('width: 100px');
$obFormulario->addComponente($obSelectExercicio);

$jsOnload = "montaParametrosGET('montaExercicio');";

$obFormulario->ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
