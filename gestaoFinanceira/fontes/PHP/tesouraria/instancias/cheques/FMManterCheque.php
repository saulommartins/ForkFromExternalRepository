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
 * Formulario de Inclusao de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GT_MON_COMPONENTES . 'IMontaAgenciaConta.class.php';
include CLA_IAPPLETTERMINAL;

$stAcao = $request->get('stAcao');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PRManterCheque.php');
$obForm->setTarget('oculto');

//Instancia o applet
$obApplet = new IAppletTerminal( $obForm );

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia o componente IMontaAgenciaConta
$obIMontaAgenciaConta = new IMontaAgenciaConta();
$obIMontaAgenciaConta->boVinculoPlanoBanco = true;

//Instancia um objeto TextBox
$obTxtNumeroCheque = new TextBox();
$obTxtNumeroCheque->setName   ('stNumeroCheque'            );
$obTxtNumeroCheque->setId     ('stNumeroCheque'            );
$obTxtNumeroCheque->setRotulo ('Número do Cheque'          );
$obTxtNumeroCheque->setTitle  ('Informe o número do cheque');
$obTxtNumeroCheque->setInteiro(true                        );
$obTxtNumeroCheque->setNull   (false                       );

//Instancia um objeto Formulario
$obFormulario = new Formulario       ();
$obFormulario->addForm               ($obForm           );
$obFormulario->addHidden             ($obApplet         );

$obFormulario->addHidden             ($obHdnAcao        );

$obFormulario->addTitulo             ('Dados do Cheque' );

$obIMontaAgenciaConta->geraFormulario($obFormulario     );
$obFormulario->addComponente         ($obTxtNumeroCheque);

$obFormulario->Ok                    ();
$obFormulario->show                  ();

?>
