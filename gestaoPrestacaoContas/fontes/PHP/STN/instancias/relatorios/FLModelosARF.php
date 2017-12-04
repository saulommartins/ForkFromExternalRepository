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
    * Filtro de Relatórios de Modelos ARF
    * Data de Criação: 11/06/2009

    * @author Analista      Tonismar Régis Bernardo     <tonismar.bernardo@cnm.org.br>
    * @author Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>

    * @package    URBEM
    * @subpackage STN

    $Id:$
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_PPA_COMPONENTES.'ITextBoxSelectPPA.class.php';
require 'JSModelosARF.js';

// Monta o select para buscar o PPA e de acordo com sua escolha preenche a combo de exercício, usando os valores iniciais e finais para
// preencher a combo de exercicios. Isso é feito via JS pelo arquivo JS do programa, sendo chamado pelo método montaExercicio()
$obITextBoxSelectPPA = new ITextBoxSelectPPA;
$obITextBoxSelectPPA->setPreencheUnico(true);
$obITextBoxSelectPPA->setNull(false);
$obITextBoxSelectPPA->obTextBox->obEvento->setOnChange("montaExercicio();");
$obITextBoxSelectPPA->obSelect->obEvento->setOnChange("montaExercicio();");

// Monta o componente que receberá valores de acordo com o que for selecionado pelo select de PPA
$obSelectExercicio = new Select;
$obSelectExercicio->setId('stExercicio');
$obSelectExercicio->setName('stExercicio');
$obSelectExercicio->setTitle('Selecione o exercício.');
$obSelectExercicio->setRotulo('Exercício LDO');
$obSelectExercicio->addOption('', 'Selecione');
$obSelectExercicio->setNull(false);
$obSelectExercicio->setStyle('width: 100px');

// A ação segue um padrão de que o nome do arquivo venha a ser usado com base na ação do programa
// Dessa forma não é necessário criar um switch e precisar alterar cada vez a tela de filtro
$obForm = new Form;
$obForm->setTarget('telaPrincipal');
$obForm->setAction('OCGeraARF'.ucfirst($_REQUEST['stAcao']).'.php');

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para o filtro');
$obITextBoxSelectPPA->geraFormulario($obFormulario);
$obFormulario->addComponente($obSelectExercicio);
$obFormulario->Ok();
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
