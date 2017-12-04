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
 * Formulario de Emissão de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';
include CAM_FW_COMPONENTES . 'Table/Table.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCManterEmitirCheque.php';

//Instancia o model e o controller
$obModel      = new RTesourariaCheque();
$obController = new CTesourariaCheque($obModel);

$arCheque = Sessao::read('arCheque');

//Span para a table de cheques
$obSpnCheques = new Span();
$obSpnCheques->setId('spnCheques');
$obSpnCheques->setValue($obController->buildListaChequeEmissao($arCheque,'Lista de cheques para impressão','imprimir'));

//Botao de voltar
$obBtVoltar = new Button();
$obBtVoltar->setName ('btVoltar');
$obBtVoltar->setId   ('btVoltar');
$obBtVoltar->setValue('Voltar'  );
$obBtVoltar->obEvento->setOnClick("Cancelar('LSManterEmitirCheque.php?stAcao=" . $stAcao . "&" . Sessao::getId() . "','telaPrincipal');");

$obFormulario = new Formulario();
$obFormulario->addSpan        ($obSpnCheques);
$obFormulario->defineBarra    (array($obBtVoltar));

$obFormulario->show();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
