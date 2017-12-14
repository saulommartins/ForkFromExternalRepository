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
 * Formulario de filtro de acoes para validacao
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

require '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once CAM_GF_LDO_NEGOCIO.'RLDOValidarAcao.class.php';
require_once CAM_GF_LDO_VISAO.'VLDOValidarAcao.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCValidarAcao.php';
require_once 'JSValidarAcao.js';

//Recupera os dados da acao
$obModel = new RLDOValidarAcao();
$obView  = new VLDOValidarAcao($obModel);
$obView->getAcao($rsAcao, $_REQUEST);

$_REQUEST['stTimestamp']  = $rsAcao->getCampo('timestamp');
$_REQUEST['stTitulo']     = $rsAcao->getCampo('titulo');

$rsAcao->addFormatacao('quantidade'      ,'NUMERIC_BR');
$rsAcao->addFormatacao('valor'           ,'NUMERIC_BR');
$rsAcao->addFormatacao('total'           ,'NUMERIC_BR');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PRValidarAcao.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setId   ('stAcao');
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnTimestamp = new Hidden();
$obHdnTimestamp->setName ('stTimestamp');
$obHdnTimestamp->setValue($rsAcao->getCampo('timestamp'));

$obHdnSaldoDisponivel = new Hidden();
$obHdnSaldoDisponivel->setName ('flSaldoDisponivel');
$obHdnSaldoDisponivel->setValue($rsAcao->getCampo('saldo_disponivel'));

$obHdnAno = new Hidden();
$obHdnAno->setName ('inAno');
$obHdnAno->setValue($rsAcao->getCampo('ano'));

$obHdnPPA = new Hidden();
$obHdnPPA->setName('inCodPPA');
$obHdnPPA->setValue($rsAcao->getCampo('cod_ppa'));

$obHdnCodAcao = new Hidden();
$obHdnCodAcao->setName('inCodAcao');
$obHdnCodAcao->setValue($rsAcao->getCampo('cod_acao'));

$obHdnNumAcao = new Hidden();
$obHdnNumAcao->setName('inNumAcao');
$obHdnNumAcao->setValue($rsAcao->getCampo('num_acao'));

$obHdnExercicio = new Hidden();
$obHdnExercicio->setName('stExercicio');
$obHdnExercicio->setValue($_REQUEST['stExercicio']);

if ($_REQUEST['stAcao'] == 'excluir') {
    $obHdnInCodRecurso = new Hidden();
    $obHdnInCodRecurso->setId  ('inCodRecurso');
    $obHdnInCodRecurso->setName('inCodRecurso');

    $obHdnStExercicioRecurso = new Hidden();
    $obHdnStExercicioRecurso->setId  ('stExercicioRecurso');
    $obHdnStExercicioRecurso->setName('stExercicioRecurso');
}

//Instancia um objeto TextBoxSelect
$obTextBoxSelectAcao = new TextBoxSelect;
$obTextBoxSelectAcao->setRotulo              ('Ação');
$obTextBoxSelectAcao->setName                ('inCodAcao');
$obTextBoxSelectAcao->obTextBox->setName     ('inCodAcaoTxt');
$obTextBoxSelectAcao->obTextBox->setId       ('inCodAcaoTxt');
$obTextBoxSelectAcao->obTextBox->setValue    ($rsAcao->getCampo('cod_acao'));
$obTextBoxSelectAcao->obSelect->setName      ('inCodAcao');
$obTextBoxSelectAcao->obSelect->setId        ('inCodAcao');
$obTextBoxSelectAcao->obSelect->addOption    ($rsAcao->getCampo('cod_acao'), $rsAcao->getCampo('titulo'));
$obTextBoxSelectAcao->obSelect->setValue     ($rsAcao->getCampo('cod_acao'));
$obTextBoxSelectAcao->obSelect->setDependente(true);
$obTextBoxSelectAcao->setLabel               (true);

$obSpnListaRecursos = new Span;
$obSpnListaRecursos->setId('spnListaRecursos');
if ($_REQUEST['stAcao'] != 'excluir') {
    $obSpnListaRecursos->setValue($obView->montaListagemRecursos($_REQUEST));
} else {
    $obSpnListaRecursos->setValue($obView->montaListagemRecursosExcluir($_REQUEST));
}

//Instancia um objeto Formulario
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnPPA);
$obFormulario->addHidden($obHdnCodAcao);
$obFormulario->addHidden($obHdnNumAcao);
$obFormulario->addHidden($obHdnExercicio);
$obFormulario->addHidden($obHdnAno);
$obFormulario->addHidden($obHdnTimestamp);
$obFormulario->addHidden($obHdnSaldoDisponivel);
if ($_REQUEST['stAcao'] == 'excluir') {
    $obFormulario->addHidden($obHdnInCodRecurso);
    $obFormulario->addHidden($obHdnStExercicioRecurso);
}
$obFormulario->addSpan  ($obSpnListaRecursos);

$obFormulario->Cancelar('LSValidarAcao.php?stAcao='.$stAcao.'&pg='.$_REQUEST['pg']);
$obFormulario->show();

require '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
