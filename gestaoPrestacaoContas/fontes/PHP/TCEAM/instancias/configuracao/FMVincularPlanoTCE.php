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
/*
    * Formulário de Vinculo do Plano de Contas ao TCE
    * Data de Criação   : 21/03/2011

    * @author: Eduardo Paculski Schitz

    * @ignore
    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES . '/Table/Table.class.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMVinculoElencoPlanoContas.class.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMElencoContasTCE.class.php';

$stPrograma = 'VincularPlanoTCE';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;

$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao );

$obTTCEAMVinculoElencoPlanoContas = new TTCEAMVinculoElencoPlanoContas;
$obTTCEAMVinculoElencoPlanoContas->setDado('exercicio', Sessao::getExercicio());
$obTTCEAMVinculoElencoPlanoContas->setDado('mes', $_REQUEST['inMes']);
$obTTCEAMVinculoElencoPlanoContas->recuperaVinculoPlanoContas($rsEstrutural);

//recupera os tipos de retencao
$obTTCEAMElencoContasTCE = new TTCEAMElencoContasTCE();
$obTTCEAMElencoContasTCE->setDado('exercicio', Sessao::getExercicio());
$obTTCEAMElencoContasTCE->recuperaTodos($rsElenco);

//cria um select com as contas do Elenco de contas do TCE
$obCmbElenco = new Select;
$obCmbElenco->setId        ('slElenco_[cod_plano]_[exercicio]');
$obCmbElenco->setName      ('slElenco_[cod_plano]_[exercicio]');
$obCmbElenco->setCampoId   ('[cod_elenco]_[exercicio]');
$obCmbElenco->setCampoDesc ('[cod_conta_tce] - [descricao]');
$obCmbElenco->addOption    ('','Selecione');
$obCmbElenco->preencheCombo($rsElenco);
$obCmbElenco->setValue     ('[cod_elenco]_[exercicio]');

//cria uma table para demonstrar os valores para o vinculo
$obTable = new Table;
$obTable->setRecordset($rsEstrutural);
//$obTable->setConditional(true);
$obTable->addLineNumber(true);

$obTable->Head->addCabecalho('Cod. Reduzido', 5);
$obTable->Head->addCabecalho('Estrutural', 10);
$obTable->Head->addCabecalho('Descrição', 45);
$obTable->Head->addCabecalho('Elenco Contas TCE', 40);

$obTable->Body->addCampo('[cod_plano]', 'C');
$obTable->Body->addCampo('[cod_estrutural]', 'C');
$obTable->Body->addCampo('[nom_conta]', 'E');
$obTable->Body->addCampo($obCmbElenco, 'E');

$obTable->montaHTML(true);
$stHTML = $obTable->getHtml();

$obSpnLista = new Span();
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addSpan  ($obSpnLista);
$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
