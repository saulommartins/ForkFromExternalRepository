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
    * Arquivo filtro - configuração arquivo DDC TCE/MG
    * Data de Criação: 08/03/2014

    * @author Analista:      Sergio Luiz dos Santos
    * @author Desenvolvedor: Arthur Cruz

*/

include_once("../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php");
include_once("../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php");
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundadaOutraOperacaoCredito";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::write('link', '');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
Sessao::write('arDividas', array());

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction($pgForm);
$obForm->setTarget( "telaPrincipal" );

$obEntidadeOrgao = new ITextBoxSelectEntidadeUsuario;
$obEntidadeOrgao->obSelect->setValue($_REQUEST['inCodEntidade']);
$obEntidadeOrgao->obTextBox->setValue($_REQUEST['inCodEntidade']);
$obEntidadeOrgao->setNull ( false );

$obExercicio = new Inteiro();
$obExercicio->setRotulo("Exercício de configurações para Dados da Dívida");
$obExercicio->setTitle("Informe o exercício de vigência das configurações de Dados da Dívida");
$obExercicio->setId("stExercicio");
$obExercicio->setName("stExercicio");
$obExercicio->setValue(Sessao::getExercicio());
$obExercicio->setNull(false);
$obExercicio->setSize(5);
$obExercicio->setMaxLength(4);
$obExercicio->setReadOnly(true);

$obBtnOk     = new Ok();
$obBtnLimpar = new Limpar();
$obBtnLimpar->setName("btnLimpar");
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setTitle("Clique para limpar os dados dos campos.");
$obBtnLimpar->obEvento->setOnClick("document.frm.reset();");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setLarguraRotulo( 30 );
$obFormulario->setLarguraCampo ( 70 );
$obFormulario->addHidden( $obHdnAcao        );
$obFormulario->addHidden( $obHdnCtrl        );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obEntidadeOrgao );
$obFormulario->addComponente( $obExercicio );
$obFormulario->defineBarra( array( $obBtnOk,$obBtnLimpar ));
$obFormulario->show();

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php');
