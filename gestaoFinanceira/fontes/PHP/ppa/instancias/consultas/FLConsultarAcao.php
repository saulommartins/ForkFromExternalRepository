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
 * Página de Formulário para Filtragem de Ação.
 * Data de Criacao: 04/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id$

 * Casos de uso: uc-02.09.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GF_PPA_COMPONENTES . 'IPopUpPrograma.class.php';

$stAcao = $request->get('stAcao');

# Define o nome dos arquivos PHP
$stPrograma = 'ConsultarAcao';
$pgFilt     = 'FL' . $stPrograma . '.php';
$pgList     = 'LS' . $stPrograma . '.php';
$pgForm     = 'FM' . $stPrograma . '.php';
$pgProc     = 'PR' . $stPrograma . '.php';
$pgOcul     = 'OC' . $stPrograma . '.php';
$pgJs       = 'JS' . $stPrograma . '.php';

# Define form
$obForm = new Form();
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal');

# Define campos escondidos
$obHdnAcao = new Hidden();
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName('stCtrl');
$obHdnCtrl->setValue($stCtrl);

# Define o formulário e acrescenta todos os componentes
$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addTitulo('Dados para Filtro');
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

# Define popup de programa
$obIPopUpPrograma = new IPopUpPrograma($obForm);
$obIPopUpPrograma->geraFormulario($obFormulario);

# Define label de intervalo.
$obLblIntervalo = new Label();
$obLblIntervalo->setValue(' até ');

# Define intervalo inicial da ação
$obTxtAcaoInicio = new TextBox();
$obTxtAcaoInicio->setName('inCodAcaoInicio');
$obTxtAcaoInicio->setRotulo('Código Ação');
$obTxtAcaoInicio->setTitle('Informe o intervalo de Códigos de Ação a consultar.');
$obTxtAcaoInicio->setInteiro(true);

# Define intervalo final da ação
$obTxtAcaoFim= new TextBox();
$obTxtAcaoFim->setName('inCodAcaoFim');
$obTxtAcaoFim->setRotulo('Código Ação');
$obTxtAcaoFim->setInteiro(true);

$arTxtIntervaloAcao = array($obTxtAcaoInicio, $obLblIntervalo, $obTxtAcaoFim);
$obFormulario->agrupaComponentes($arTxtIntervaloAcao);

$obFormulario->ok();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
