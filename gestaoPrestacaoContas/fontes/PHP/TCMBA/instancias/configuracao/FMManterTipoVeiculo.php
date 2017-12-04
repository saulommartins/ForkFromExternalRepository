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
    * Página de Formulario de Vinculo entre a marca do URBEM e a do SIGA
    * Data de Criação: 18/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: FMManterTipoVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoVeiculo.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaTipoVeiculo.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Remove o que estiver setado na sessao
Sessao::remove('arTipoVeiculo');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

/*****************************************************
 Recupera dados do banco
*****************************************************/
//Recupera os tipos de veiculo do TCM
$obTTipoVeiculo = new TTBATipoVeiculo();
$obTTipoVeiculo->recuperaTodos($rsTipoVeiculoTcm);

//Recupera os tipos de veiculo do Urbem
$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
$obTFrotaTipoVeiculo->recuperaTodos($rsTipoVeiculoSw);

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define um combo para os tipos de veiculo do tcm
$obCmbTipoVeiculoTcm = new Select();
$obCmbTipoVeiculoTcm->setName('inCodTipoVeiculoTcm');
$obCmbTipoVeiculoTcm->setId('inCodTipoVeiculoTcm');
$obCmbTipoVeiculoTcm->setRotulo('Tipo Veículo - TCM');
$obCmbTipoVeiculoTcm->setTitle('Informe o tipo do tcm.');
$obCmbTipoVeiculoTcm->addOption('','Selecione');
$obCmbTipoVeiculoTcm->setCampoId('cod_tipo_tcm');
$obCmbTipoVeiculoTcm->setCampoDesc('descricao');
$obCmbTipoVeiculoTcm->preencheCombo($rsTipoVeiculoTcm);
$obCmbTipoVeiculoTcm->setNull(true);
$obCmbTipoVeiculoTcm->setObrigatorioBarra(true);

//Define um select multiplo para os tipos de veiculo do urbem
$obISelectMultiploTipoVeiculoSw = new SelectMultiplo();
$obISelectMultiploTipoVeiculoSw->setName('inCodTipoVeiculoSw');
$obISelectMultiploTipoVeiculoSw->setRotulo('Tipo Veículo - Sistema');
$obISelectMultiploTipoVeiculoSw->setTitle('Selecione o tipo de veículo do urbem.');
$obISelectMultiploTipoVeiculoSw->setNull(true);
$obISelectMultiploTipoVeiculoSw->setObrigatorioBarra(true);
//Seta os tipos de veiculo disponiveis
$obISelectMultiploTipoVeiculoSw->setNomeLista1('inCodTipoVeiculoSwDisponivel');
$obISelectMultiploTipoVeiculoSw->setCampoId1('cod_tipo');
$obISelectMultiploTipoVeiculoSw->setCampoDesc1('nom_tipo');
$obISelectMultiploTipoVeiculoSw->setRecord1($rsTipoVeiculoSw);
//Seta os tipos de veiculo selecionados
$obISelectMultiploTipoVeiculoSw->setNomeLista2('inCodTipoVeiculoSwSelecionados');
$obISelectMultiploTipoVeiculoSw->setCampoId2('');
$obISelectMultiploTipoVeiculoSw->setCampoDesc2('');
$obISelectMultiploTipoVeiculoSw->setRecord2(new RecordSet());

//Define Objeto Button para Incluir o tipo de veiculo
$obBtnIncluirTipoVeiculo = new Button;
$obBtnIncluirTipoVeiculo->setValue             ( "Incluir"                                   );
$obBtnIncluirTipoVeiculo->setId                ( "incluiTipoVeiculo"                         );
$obBtnIncluirTipoVeiculo->obEvento->setOnClick ( "montaParametrosGET('incluirTipoVeiculo','inCodTipoVeiculoTcm,inCodTipoVeiculoSwSelecionados');" );

//Define Objeto Button para Limpar o tipo Veiculo
$obBtnLimparTipoVeiculo = new Button;
$obBtnLimparTipoVeiculo->setValue             ( "Limpar"          );
$obBtnLimparTipoVeiculo->obEvento->setOnClick ( "LimparFormularioTipoVeiculo();" );

//Define Span para DataGrid
$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );
$obSpnLista->setValue ( $stLista );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Dados");

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);

$obFormulario->addComponente($obCmbTipoVeiculoTcm);
$obFormulario->addComponente($obISelectMultiploTipoVeiculoSw);
$obFormulario->defineBarra(array($obBtnIncluirTipoVeiculo,$obBtnLimparTipoVeiculo));

$obFormulario->addSpan($obSpnLista);

$obFormulario->Ok();

$obFormulario->show();

//Caso ja exista dados na base, carrega a lista
$jsOnLoad = "montaParametrosGET('montaLista');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
