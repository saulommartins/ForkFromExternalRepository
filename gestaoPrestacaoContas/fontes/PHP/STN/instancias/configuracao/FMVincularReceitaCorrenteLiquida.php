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
 * Formulario de Vinculo de Receita Corrente Liquida
 *
 * @category    Urbem
 * @package     STN
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';

$stAcao = $request->get('stAcao');

$pgOcul = 'OCVincularReceitaCorrenteLiquida.php';

Sessao::remove('arPeriodo');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('PRVincularReceitaCorrenteLiquida.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia um objeto Data
$obDtImplantacao = new Data();
$obDtImplantacao->setRotulo('Data de Implantação');
$obDtImplantacao->setTitle ('Informe a data de implantação');
$obDtImplantacao->setName  ('stDataImplantacao');
$obDtImplantacao->setId    ('stDataImplantacao');
$obDtImplantacao->setNull  (false);
$stJs = "ajaxJavaScript('" . $pgOcul . "?stDataImplantacao='+this.value+'&stTitle=Dados da Receita Corrente Líquida','montaFormRCL');";
$obDtImplantacao->obEvento->setOnChange($stJs);

//Instancia um span para o formulario
$obSpnFormAux = new Span();
$obSpnFormAux->setId('spnFormAux');

//Instancia um span para a lista
$obSpnLista = new Span();
$obSpnLista->setId('spnLista');

//Instancia um Ok
$obOk = new Ok;

//Instancia um Limpar
$obLimpar = new Limpar();
$obLimpar->obEvento->setOnClick("LimparForm();");

//Instancia um objeto Formulario
$obFormulario = new Formulario       ();
$obFormulario->addForm               ($obForm   );
$obFormulario->addHidden             ($obHdnAcao);

$obFormulario->addTitulo             ('Dados da Implantação');
$obFormulario->addComponente         ($obDtImplantacao);
$obFormulario->addSpan               ($obSpnFormAux);
$obFormulario->addSpan               ($obSpnLista);

$obFormulario->defineBarra           (array($obOk,$obLimpar));
$obFormulario->show                  ();

$jsOnload = "montaParametrosGET('verificaDataImplantacao');";

include 'JSVincularReceitaCorrenteLiquida.js';
include CAM_FW_INCLUDE . 'rodape.inc.php';
?>
