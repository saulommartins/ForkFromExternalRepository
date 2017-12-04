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
 * Formulário de Vínculo das Contas Bancárias com a Fonte Pagadora
 * Data de Criação   : 18/02/2009

 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TPB_MAPEAMENTO.'TTPBPlanoConta.class.php';
include_once CAM_GPC_TPB_NEGOCIO.'RTCEPBTipoOrigemRecurso.class.php';
include_once(CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );

$stPrograma = "ManterFontePagadoraContaBancaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include($pgJs);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Referente a  pagar sessão caso haja algum erro.
Sessao::remove('arContas');
Sessao::remove('arValoresBanco');
Sessao::remove('inValorTipo');

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obTTPBPlanoConta = new TTPBPlanoConta();
$obTTPBPlanoConta->setDado('stExercicio', Sessao::getExercicio() );
$obTTPBPlanoConta->recuperaContaBancariaFontePagadoraContas($rsContasBancarias);

$obRTPBTipoOrigemRecurso = new RTCEPBTipoOrigemRecurso;
$obRTPBTipoOrigemRecurso->recuperaOrigemRecurso($rsOrigemRecurso);

$obCboFontePagadora = new Select();
$obCboFontePagadora->setName             ("cmbFontePagadora");
$obCboFontePagadora->setId               ("cmbFontePagadora");
$obCboFontePagadora->setValue            ("[cod_tipo]-[exercicio]");
$obCboFontePagadora->addOption           ("","Selecione");
$obCboFontePagadora->setRotulo           ("*Fonte Pagadora");
$obCboFontePagadora->setCampoId          ("[cod_tipo]-[descricao]");
$obCboFontePagadora->setCampodesc        ("[cod_tipo]-[descricao]");
$obCboFontePagadora->setTitle            ('Informe a Fonte Pagadora.');
$obCboFontePagadora->preencheCombo       ($rsOrigemRecurso);
$obCboFontePagadora->obEvento->setOnChange ("executaFuncaoAjax('buscaContasFontePagadora&'+this.name+'='+this.value);");

$rsContasSelecionados = new RecordSet;

$obCmbContas = new SelectMultiplo();
$obCmbContas->setName  ( 'arContasSelecionadas' );
$obCmbContas->setRotulo( "Contas" );
$obCmbContas->setNull  ( true );
$obCmbContas->setObrigatorioBarra (true);
$obCmbContas->setTitle ( 'Contas Disponiveis' );

// lista de CONTAS disponiveis
$obCmbContas->SetNomeLista1( 'arCodContasDisponiveis' );
$obCmbContas->setCampoId1  ( '[cod_conta_corrente]' );
$obCmbContas->setCampoDesc1( '[num_conta_corrente]-[nom_conta]' );
$obCmbContas->SetRecord1   ( $rsContasBancarias  );

// lista de CONTAS selecionados
$obCmbContas->SetNomeLista2( 'arContasSelecionadas' );
$obCmbContas->setCampoId2  ( '[cod_conta_corrente]' );
$obCmbContas->setCampoDesc2( '[num_conta_corrente]-[nom_conta]' );
$obCmbContas->SetRecord2   ( $rsContasSelecionados );

// Define Objeto Button para Incluir Item na lista
$obBtnIncluir = new Button;
$obBtnIncluir->setValue            ( "Incluir"          );
$obBtnIncluir->obEvento->setOnClick( "montaParametrosGET('incluirContaLista','cmbFontePagadora,arContasSelecionadas');" );

// Define Objeto Button para Limpar  Item na lista
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparContasLista');" );

$obSpnLista = new Span;
$obSpnLista->setId  ( 'spnLista' );

$obOk  = new Ok;
$obOk->setId ("btnOk");

$obLimpar = new Button;
$obLimpar->setValue ( "Limpar" );
$obLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparTudo');" );

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm              ($obForm);
$obFormulario->addHidden            ($obHdnAcao);
$obFormulario->addComponente        ($obCboFontePagadora);
$obFormulario->addComponente        ($obCmbContas);
$obFormulario->agrupaComponentes    (array( $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->addSpan              ($obSpnLista);

$obFormulario->defineBarra( array( $obOk,$obLimpar ) );

$obFormulario->show();

$jsOnLoad = "executaFuncaoAjax('contasExistentes');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
