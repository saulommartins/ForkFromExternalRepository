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
 * Classe de regra de configuracao do tcmgo
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TGO_NEGOCIO .'RTGOConfiguracao.class.php';

$pgProc = "PRManterCombustivel.php";
$pgOcul = "OCManterCombustivel.php";

$stAcao = $request->get("stAcao");

//Instancia um form
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Instancia o hidden para a acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Instancia o hidden para o ctrl
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Recupera os tipos de combustivel
$obRTGOConfiguracao = new RTGOConfiguracao ();
$obRTGOConfiguracao->listTipoCombustivelTCM($rsTipoCombustivel);

//Instancia um select para o tipo de combustivel do TCM
$obSlTipoCombustivelTCM = new Select  ();
$obSlTipoCombustivelTCM->setRotulo    ('Tipo de Combustível do TCM');
$obSlTipoCombustivelTCM->setTitle     ('Selecione o tipo de combustível do TCM.');
$obSlTipoCombustivelTCM->setName      ('inCodTipoCombustivelTCM');
$obSlTipoCombustivelTCM->setId        ('inCodTipoCombustivelTCM');
$obSlTipoCombustivelTCM->addOption    ('','Selecione');
$obSlTipoCombustivelTCM->setCampoId   ('cod_tipo');
$obSlTipoCombustivelTCM->setCampoDesc ('descricao');
$obSlTipoCombustivelTCM->preencheCombo($rsTipoCombustivel);
$obSlTipoCombustivelTCM->setNull      (false);
$obSlTipoCombustivelTCM->obEvento->setOnChange("montaParametrosGET('preencheCombustivelTCM');");

//Instancia um select para os combustiveis do TCM
$obSlCombustivelTCM = new Select  ();
$obSlCombustivelTCM->setRotulo    ('Combustível do TCM');
$obSlCombustivelTCM->setTitle     ('Selecione o combustível do TCM');
$obSlCombustivelTCM->setName      ('inCodCombustivelTCM');
$obSlCombustivelTCM->setId        ('inCodCombustivelTCM');
$obSlCombustivelTCM->addOption    ('','Selecione');
$obSlCombustivelTCM->setNull      (false);
$obSlCombustivelTCM->obEvento->setOnChange("montaParametrosGET('preencheCombustivel');");

//Instancia um select multiplo para os combustiveis do SW
$obSlMultiploCombustivel = new SelectMultiplo();
$obSlMultiploCombustivel->setName            ('arCodCombustivelSW');
$obSlMultiploCombustivel->setRotulo          ('Combustível do Urbem');
$obSlMultiploCombustivel->setTitle           ('Selecione os combustiveis do Urbem');
$obSlMultiploCombustivel->setNull            (true);

$rsCombustivelDisponivel  = new RecordSet();
$rsCombustivelSelecionado = new RecordSet();

$obSlMultiploCombustivel->setNomeLista1('arCodCombustivelSWDisponivel');
$obSlMultiploCombustivel->setCampoId1  ('cod_item');
$obSlMultiploCombustivel->setCampoDesc1('descricao');
$obSlMultiploCombustivel->setRecord1   ($rsCombustivelDisponivel);

$obSlMultiploCombustivel->setNomeLista2('arCodCombustivelSWSelecionado');
$obSlMultiploCombustivel->setCampoId2  ('');
$obSlMultiploCombustivel->setCampoDesc2('');
$obSlMultiploCombustivel->setRecord2   ($rsCombustivelDisponivel);

//Instancia um formulario
$obFormulario = new Formulario();
$obFormulario->setForm        ($obForm);
$obFormulario->addHidden      ($obHdnAcao);
$obFormulario->addHidden      ($obHdnCtrl);
$obFormulario->addTitulo      ('Dados do Combustível');

$obFormulario->addComponente  ($obSlTipoCombustivelTCM);
$obFormulario->addComponente  ($obSlCombustivelTCM);
$obFormulario->addComponente  ($obSlMultiploCombustivel);

$obFormulario->Ok();

$obFormulario->show();
include 'JSManterCombustivel.js';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>