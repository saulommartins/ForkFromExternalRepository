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
 * Página de Configuração de Empréstimos do Banrisul
 * Data de Criação   : 06/09/2009

 * @author Analista      Dagiane
 * @author Desenvolvedor Cassiano de Vasconcellos Ferreira

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GRH_FOL_COMPONENTES.'IBuscaInnerEvento.class.php';
include_once CAM_GRH_IMA_MAPEAMENTO.'TIMAConfiguracaoBanrisulEmprestimo.class.php';

$stPrograma = "ManterConfiguracaoEmprestimoBanrisul";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTIMAConfiguracaoBanrisulEmprestimo = new TIMAConfiguracaoBanrisulEmprestimo;
$obTIMAConfiguracaoBanrisulEmprestimo->recuperaTodos($rsConfiguracao);

/*
 * Titulo do formulário
 */
include_once CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php';
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

/*
 * Componentes do formulário
 */
$obBscInnerEvento = new IBuscaInnerEvento;
$obBscInnerEvento->setRotulo('Evento');
$obBscInnerEvento->setId('stIdEvento');
$obBscInnerEvento->setTitle('Informe o código do evento de empréstimo Banrisul. Deve ser do tipo variável e controle de parcelas');
$obBscInnerEvento->setNull(false);

$obBscInnerEvento->obCampoCod->setName('inCodigoEvento');
$obBscInnerEvento->obCampoCod->setId('inCodigoEvento');
$obBscInnerEvento->setNaturezasDesconto();
$obBscInnerEvento->setEventoSistema(false);
$obBscInnerEvento->setNaturezaChecked("D");
$obBscInnerEvento->setTipo('V');
$obBscInnerEvento->montaOnChange();
$obBscInnerEvento->montaPopUp();

$obIBscEventoDesconto = new IBuscaInnerEvento();
$obIBscEventoDesconto->setRotulo("Evento Automático de Desconto do IPE/RS");
$obIBscEventoDesconto->setId("stEventoDescontoIPERS");
$obIBscEventoDesconto->setTitle("Informe o evento automático de desconto do IPE, cadastrado previamente no cadastro de eventos.");
$obIBscEventoDesconto->setNull(false);
$obIBscEventoDesconto->obCampoCod->setName("inCodigoEventoDescontoIPERS");
$obIBscEventoDesconto->obCampoCod->setId("inCodigoEventoDescontoIPERS");
$obIBscEventoDesconto->setNaturezasDesconto();
$obIBscEventoDesconto->setEventoSistema(true);
$obIBscEventoDesconto->setNaturezaChecked("D");
$obIBscEventoDesconto->montaOnChange();
$obIBscEventoDesconto->montaPopUp();

$jsOnload = "executaFuncaoAjax('preencherInnerEvento','&inCodEvento=".$rsConfiguracao->getCampo('cod_evento')."');";

$obBtnOk = new Ok;
$obBtnOk->setTitle('Clique para armazenar os dados no banco de dados.');

$obbtnCancelar = new Cancelar;
$obbtnCancelar->setTitle('Clique para cancelar as alterações.');
$obbtnCancelar->obEvento->setonClick("jQuery('#frm')[0].reset();$jsOnload");

/*
 * definição do Form
 */
$obForm = new Form;
$obForm->setAction($pgProc);

$obFormulario= new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo($stTitulo, 'right');
$obFormulario->addTitulo('Configuração Empréstimos Banrisul');
$obFormulario->addBusca($obBscInnerEvento);
$obFormulario->defineBarra(array($obBtnOk,$obbtnCancelar));
$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
