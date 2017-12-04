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
 * Formulario para o vinculo de impressora de cheques com o terminal
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaImpressoraCheque.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCVincularImpressoraCheque.php';

$stPrograma = "VincularImpressoraCheque";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia um label para o usuário
$obLblUsuario = new Label();
$obLblUsuario->setRotulo ('Usuário');
$obLblUsuario->setValue  (Sessao::read('numCgm') . ' - ' . Sessao::read('nomCgm'));

//Recupera as impressoras do sistema
$obRTesourariaImpressoraCheque = new RTesourariaImpressoraCheque();
$obRTesourariaImpressoraCheque->listImpressorasSistema ($rsImpressoras);

//Instancia um select para as impressoras
$obCmbImpressora = new Select  ();
$obCmbImpressora->setRotulo    ('Impressora'     );
$obCmbImpressora->setName      ('inCodImpressora');
$obCmbImpressora->setId        ('inCodImpressora');
$obCmbImpressora->setValue     ($obRTesourariaImpressoraCheque->stFilaImpressao);
$obCmbImpressora->setStyle     ('width: 200px'   );
$obCmbImpressora->setCampoId   ('cod_impressora' );
$obCmbImpressora->setCampoDesc ('nom_impressora' );
$obCmbImpressora->addOption    ('', 'Selecione'  );
$obCmbImpressora->preencheCombo($rsImpressoras   );
$obCmbImpressora->setNull      (false            );

//Instancia um objeto Formulario
$obFormulario = new Formulario();
$obFormulario->addForm        ($obForm           );

$obFormulario->addHidden      ($obHdnAcao        );

$obFormulario->addTitulo      ('Dados do Cheque' );
$obFormulario->addComponente  ($obLblUsuario     );
$obFormulario->addComponente  ($obCmbImpressora  );

$obFormulario->Ok             ();
$obFormulario->show           ();

//$jsOnload = "montaParametrosGET('selecionaImpressora');";

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
