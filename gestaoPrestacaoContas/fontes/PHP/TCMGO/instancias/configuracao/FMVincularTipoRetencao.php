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
    * Formulário de Vinculo de Tipo de Retencao
    * Data de Criação   : 06/04/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Henrique Boaventura

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once TTGO . 'TTCMGODeParaTipoRetencao.class.php';
include_once TTGO . 'TTCMGOTipoRetencao.class.php';
include_once CAM_FW_COMPONENTES . '/Table/Table.class.php';

$stPrograma = "VincularTipoRetencao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction                  ($pgProc );
$obForm->setTarget                  ("oculto");

$obHdnAcao = new Hidden;

$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao );

//recupera os estruturais das retencoes
$obTTCMGODeParaTipoRetencao = new TTCMGODeParaTipoRetencao();
$obTTCMGODeParaTipoRetencao->setDado('exercicio',Sessao::getExercicio());
$obTTCMGODeParaTipoRetencao->listRetencaoReceita($rsEstrutural);

//recupera os tipos de retencao
$obTTCMGOTipoRetencao = new TTCMGOTipoRetencao();
$obTTCMGOTipoRetencao->setDado('exercicio',Sessao::getExercicio());
$obTTCMGOTipoRetencao->recuperaTodos($rsTipo);

//cria um select com os tipos de retencao
$obSlTipoRetencao = new Select  ();
$obSlTipoRetencao->setId        ('slRetencao_[cod_plano]_[exercicio]');
$obSlTipoRetencao->setName      ('slRetencao_[cod_plano]_[exercicio]');
$obSlTipoRetencao->setCampoId   ('[cod_tipo]_[exercicio]');
$obSlTipoRetencao->setCampoDesc ('[descricao]');
$obSlTipoRetencao->addOption    ('','Selecione');
$obSlTipoRetencao->preencheCombo($rsTipo);
$obSlTipoRetencao->setValue     ('[cod_tipo]_[exercicio]');

//cria uma table para demonstrar os valores para o vinculo
$obTable = new Table;
$obTable->setRecordset($rsEstrutural);
//$obTable->setConditional(true);
$obTable->addLineNumber(true);

$obTable->Head->addCabecalho('Cod. Reduzido', 10);
$obTable->Head->addCabecalho('Estrutural', 15);
$obTable->Head->addCabecalho('Descrição', 50);
$obTable->Head->addCabecalho('Tipo de Retenção', 15);

$obTable->Body->addCampo('[cod_receita]', 'C');
$obTable->Body->addCampo('[cod_estrutural]', 'C');
$obTable->Body->addCampo('[nom_conta]', 'E');
$obTable->Body->addCampo($obSlTipoRetencao, 'E');

$obTable->montaHTML(true);
$stHTML = $obTable->getHtml();

$obSpnLista = new Span();
$obSpnLista->setId('spnLista');
$obSpnLista->setValue($stHTML);

$obFormulario = new Formulario();
$obFormulario->addForm        ($obForm);

$obFormulario->addHidden      ($obHdnAcao);
$obFormulario->addSpan        ($obSpnLista);
$obFormulario->Ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
