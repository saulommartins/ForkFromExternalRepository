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
include CLA_IAPPLETTERMINAL;
include 'JSManterEmitirCheque.js';

$stAcao = $request->get('stAcao');
$pgOcul = 'OCManterEmitirCheque.php';

Sessao::remove('paginando');

//Instancia um objeto Form
$obForm = new Form();
$obForm->setAction('LSManterEmitirCheque.php');

//Instancia o applet
$obApplet = new IAppletTerminal( $obForm );

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden();
$obHdnAcao->setName    ('stAcao');
$obHdnAcao->setValue   ($stAcao );

//Instancia um select para o tipo de pagamento
$obCmbTipoPagamento = new Select();
$obCmbTipoPagamento->setName    ('stTipoPagamento'                      );
$obCmbTipoPagamento->setId      ('stTipoPagamento'                      );
$obCmbTipoPagamento->setRotulo  ('Tipo de Pagamento'                    );
$obCmbTipoPagamento->setTitle   ('Informe o tipo de pagamento'          );
$obCmbTipoPagamento->addOption  ('', 'Selecione'                        );
$obCmbTipoPagamento->addOption  ('ordem_pagamento', 'Ordem de Pagamento');
$obCmbTipoPagamento->addOption  ('despesa_extra'  , 'Despesa Extra'     );
$obCmbTipoPagamento->addOption  ('transferencia'  , 'Transferência'     );
$obCmbTipoPagamento->setNull    (false                                  );
$obCmbTipoPagamento->obEvento->setOnChange("montaParametrosGET('montaTipoPagamento','stTipoPagamento');");

//Instancia um span para os dados do pagamento
$obSpnTipoPagamento = new Span();
$obSpnTipoPagamento->setId    ('spnTipoPagamento');

//Instancia o botao para o ok
$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick('verificaDados();');

//Instancia o botao para o limpar
$obBtnLimpar = new Limpar();
//$obBtnLimpar->obEvento->setOnClick('verificaDados();');

//Instancia um objeto Formulario
$obFormulario = new Formulario();
$obFormulario->addForm        ($obForm            );
$obFormulario->addHidden      ($obHdnAcao         );
$obFormulario->addHidden      ($obApplet          );
$obFormulario->addTitulo      ('Filtro'           );

$obFormulario->addComponente  ($obCmbTipoPagamento);
$obFormulario->addSpan        ($obSpnTipoPagamento);

$obFormulario->defineBarra    (array($obBtnOk,$obBtnLimpar));

//$obFormulario->Ok             ();
$obFormulario->show           ();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
