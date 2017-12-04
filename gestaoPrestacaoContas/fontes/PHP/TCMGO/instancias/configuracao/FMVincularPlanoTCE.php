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
    * Data de Criação   : 09/05/2013

    * @author: Eduardo Paculski Schitz

    * @ignore
    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES . '/Table/Table.class.php';
include_once TTGO.'TTCMGORecuperaPlanoConfiguracaoTCM.class.php';
include_once TTGO.'TTCMGOVinculoPlanoContas.class.php';
include_once TTGO.'TTCMGOElencoContasTCE.class.php';

$stPrograma = 'VincularPlanoTCE';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

include_once $pgJs;

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnMes = new Hidden;
$obHdnMes->setName ("inMes");
$obHdnMes->setValue($_REQUEST['inMes']);

$obHdnGrupo = new Hidden;
$obHdnGrupo->setName ("stGrupo");
$obHdnGrupo->setValue($_REQUEST['stGrupo']);

$obTTCMGORecuperaPlanoConfiguracaoTCM = new TTCMGORecuperaPlanoConfiguracaoTCM;
$obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('exercicio'   , Sessao::getExercicio());
$obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('entidades'   , $_REQUEST['inCodEntidade']);
$obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('data_inicial', '01/'.$_REQUEST['inMes'].'/'.Sessao::getExercicio());
$obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('data_final'  , SistemaLegado::retornaUltimoDiaMes($_REQUEST['inMes'], Sessao::getExercicio()));
$obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('grupo'       , $_REQUEST['stGrupo']);
$obTTCMGORecuperaPlanoConfiguracaoTCM->recuperaTodos($rsEstrutural);

//recupera os tipos de retencao
$obTTCMGOElencoContasTCE = new TTCMGOElencoContasTCE();
$obTTCMGOElencoContasTCE->setDado('exercicio'     , Sessao::getExercicio());
$obTTCMGOElencoContasTCE->setDado('cod_estrutural', $_REQUEST['stGrupo']);
$obTTCMGOElencoContasTCE->recuperaTodos($rsElenco);

//cria um select com as contas do Elenco de contas do TCE
$obCmbElenco = new Select;
$obCmbElenco->setId        ('slPlano_[cod_conta]_[exercicio]');
$obCmbElenco->setName      ('slPlano_[cod_conta]_[exercicio]');
$obCmbElenco->setCampoId   ('[cod_plano_tcmgo]_[exercicio]');
$obCmbElenco->setCampoDesc ('[estrutural] - [titulo]');
$obCmbElenco->addOption    ('','Selecione');
$obCmbElenco->preencheCombo($rsElenco);
$obCmbElenco->setValue     ('[cod_plano_tcmgo]_[exercicio]');

//cria uma table para demonstrar os valores para o vinculo
$obTable = new Table;
$obTable->setRecordset($rsEstrutural);
$obTable->addLineNumber(true);

$obTable->Head->addCabecalho('Cod. Reduzido', 5);
$obTable->Head->addCabecalho('Estrutural', 10);
$obTable->Head->addCabecalho('Descrição', 45);
$obTable->Head->addCabecalho('Obrig.', 1);
$obTable->Head->addCabecalho('Elenco Contas TCE', 39);

$obTable->Body->addCampo('[cod_plano]', 'C');
$obTable->Body->addCampo('[cod_estrutural]', 'C');
$obTable->Body->addCampo('[nom_conta]', 'E');
$obTable->Body->addCampo('[desc_obrigatorio]', 'D');
$obTable->Body->addCampo($obCmbElenco, 'E');

$obTable->montaHTML(true);
$stHTML = $obTable->getHtml();

$obSpnLista = new Span();
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

$obOk      = new Ok();
$obOk->obEvento->setOnClick("if(validaCampos()){ Salvar(); }");
$obLimpar  = new Limpar;

$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnMes);
$obFormulario->addHidden($obHdnGrupo);
$obFormulario->addSpan  ($obSpnLista);
$obFormulario->defineBarra( array( $obOk, $obLimpar ) );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
