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
 * Formulario de filtro de despesa/receita
 *
 * @category    Urbem
 * @package     LDO
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GF_LDO_NEGOCIO . 'RLDODespesaReceita.class.php';
include_once CAM_GF_LDO_VISAO   . 'VLDODespesaReceita.class.php';
include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPA.class.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCDespesaReceita.php';

include_once 'JSDespesaReceita.js';

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PRDespesaReceita.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia um objeto hidden para o cod_ppa
$obHdnCodPPA = new Hidden;
$obHdnCodPPA->setName ('inCodPPA');
$obHdnCodPPA->setValue($_REQUEST['inCodPPA']);

//Instancia um objeto hidden para o ano
$obHdnAno = new Hidden;
$obHdnAno->setName ('inAno');
$obHdnAno->setValue($_REQUEST['slExercicioLDO']);

$obVLDOTipoReceitaDespesa = new VLDODespesaReceita(new RLDODespesaReceita());

$obSpnArrecadadoLiquidado = new Span();
$obSpnArrecadadoLiquidado->setId('spnArrecadadoLiquidado');

$obSpnPrevistoFixado = new Span();
$obSpnPrevistoFixado->setId('spnPrevistoFixado');

//Instancia um objeto Formulario
$obFormulario = new FormularioAbas    ();
$obFormulario->addForm                ($obForm);
$obFormulario->addHidden              ($obHdnAcao);
$obFormulario->addHidden              ($obHdnCodPPA);
$obFormulario->addHidden              ($obHdnAno);

$obFormulario->addAba                 ('Arrecadado/Liquidado');
$obVLDOTipoReceitaDespesa->geraTabelas($obFormulario,$_REQUEST,'arrecadado');

$obFormulario->addAba             ('Previsto/Fixado');
$obVLDOTipoReceitaDespesa->geraTabelas($obFormulario,$_REQUEST,'previsto');

$obFormulario->addAba             ('Projetado');
$obVLDOTipoReceitaDespesa->geraTabelas($obFormulario,$_REQUEST,'projetado');

$obFormulario->Cancelar           ('FLDespesaReceita.php', true);
$obFormulario->show               ();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
