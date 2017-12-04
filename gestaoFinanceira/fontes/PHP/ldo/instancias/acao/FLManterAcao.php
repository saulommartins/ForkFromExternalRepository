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
 * Página de Filtro do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Fellipe Esteves dos Santos <fellipe.santos>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

$stPrograma = 'ManterAcao';
$pgFilt     = 'FL' . $stPrograma . '.php';
$pgList     = 'LS' . $stPrograma . '.php';
$pgForm     = 'FM' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma . '.php';
$pgOcul     = 'OC' . $stPrograma . '.php';
$pgJs       = 'JS' . $stPrograma . '.php';

$obForm = new Form();
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Filtro');

$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);
$obFormulario->addHidden($obHdnAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);
$obFormulario->addHidden($obHdnCtrl);

$obLblIntervalo = new Label();
$obLblIntervalo->setValue(' até ');

$obInAcaoIni = new Inteiro();
$obInAcaoIni->setName('inNumAcaoInicio');
$obInAcaoIni->setRotulo('Ação');
$obInAcaoIni->setTitle('Informe o intervalo de Códigos de Ação a consultar.');
$obInAcaoIni->setInteiro(true);
$obInAcaoIni->setSize(9);

$obInAcaoFim= new Inteiro();
$obInAcaoFim->setName('inNumAcaoFim');
$obInAcaoFim->setRotulo('Código Ação');
$obInAcaoFim->setInteiro(true);
$obInAcaoFim->setSize(10);

$arTxtIntervaloAcao = array($obInAcaoIni, $obLblIntervalo, $obInAcaoFim);

$obFormulario->agrupaComponentes($arTxtIntervaloAcao);

$obFormulario->ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
