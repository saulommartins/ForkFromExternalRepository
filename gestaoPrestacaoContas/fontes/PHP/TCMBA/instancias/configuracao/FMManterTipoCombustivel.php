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
    * Página de Formulario de Vinculo entre o tipo de combustivel do URBEM e a do SIGA
    * Data de Criação: 20/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: FMManterTipoCombustivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoCombustivel.class.php" );
include_once( CAM_GP_FRO_MAPEAMENTO.'TFrotaCombustivel.class.php');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoCombustivel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Remove o que estiver setado na sessao
Sessao::remove('arTipoCombustivel');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

/*****************************************************
 Recupera dados do banco
*****************************************************/
//Recupera os tipos de combustivel do TCM
$obTTipoCombustivel = new TTBATipoCombustivel();
$obTTipoCombustivel->recuperaTodos($rsTipoCombustivelTcm);

//Recupera os tipos de combustivel do Urbem
$obTFrotaCombustivel = new TFrotaCombustivel();
$obTFrotaCombustivel->recuperaTodos($rsTipoCombustivelSw);

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

//Define um combo para os tipos de combustivel do tcm
$obCmbTipoCombustivelTcm = new Select();
$obCmbTipoCombustivelTcm->setName('inCodTipoCombustivelTcm');
$obCmbTipoCombustivelTcm->setId('inCodTipoCombustivelTcm');
$obCmbTipoCombustivelTcm->setRotulo('Tipo Combustível - TCM');
$obCmbTipoCombustivelTcm->setTitle('Informe o tipo de combustível do tcm.');
$obCmbTipoCombustivelTcm->addOption('','Selecione');
$obCmbTipoCombustivelTcm->setCampoId('cod_tipo_tcm');
$obCmbTipoCombustivelTcm->setCampoDesc('descricao');
$obCmbTipoCombustivelTcm->preencheCombo($rsTipoCombustivelTcm);
$obCmbTipoCombustivelTcm->setNull(true);
$obCmbTipoCombustivelTcm->setObrigatorioBarra(true);

//Define um select multiplo para os tipos de combustivel do urbem
$obISelectMultiploTipoCombustivelSw = new SelectMultiplo();
$obISelectMultiploTipoCombustivelSw->setName('inCodTipoCombustivelSw');
$obISelectMultiploTipoCombustivelSw->setRotulo('Tipo Combustível - Sistema');
$obISelectMultiploTipoCombustivelSw->setTitle('Selecione o tipo de combustível do urbem.');
$obISelectMultiploTipoCombustivelSw->setNull(true);
$obISelectMultiploTipoCombustivelSw->setObrigatorioBarra(true);
//Seta os tipos de combustivel disponiveis
$obISelectMultiploTipoCombustivelSw->setNomeLista1('inCodTipoCombustivelSwDisponivel');
$obISelectMultiploTipoCombustivelSw->setCampoId1('cod_combustivel');
$obISelectMultiploTipoCombustivelSw->setCampoDesc1('nom_combustivel');
$obISelectMultiploTipoCombustivelSw->setRecord1($rsTipoCombustivelSw);
//Seta os tipos de combustivel selecionados
$obISelectMultiploTipoCombustivelSw->setNomeLista2('inCodTipoCombustivelSwSelecionados');
$obISelectMultiploTipoCombustivelSw->setCampoId2('');
$obISelectMultiploTipoCombustivelSw->setCampoDesc2('');
$obISelectMultiploTipoCombustivelSw->setRecord2(new RecordSet());

//Define Objeto Button para Incluir o tipo de combustivel
$obBtnIncluirTipoCombustivel = new Button;
$obBtnIncluirTipoCombustivel->setValue             ( "Incluir"                                   );
$obBtnIncluirTipoCombustivel->setId                ( "incluiTipoCombustivel"                     );
$obBtnIncluirTipoCombustivel->obEvento->setOnClick ( "montaParametrosGET('incluirTipoCombustivel','inCodTipoCombustivelTcm,inCodTipoCombustivelSwSelecionados');" );

//Define Objeto Button para Limpar o tipo combustivel
$obBtnLimparTipoCombustivel = new Button;
$obBtnLimparTipoCombustivel->setValue             ( "Limpar"          );
$obBtnLimparTipoCombustivel->obEvento->setOnClick ( "LimparFormularioTipoCombustivel();" );

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

$obFormulario->addComponente($obCmbTipoCombustivelTcm);
$obFormulario->addComponente($obISelectMultiploTipoCombustivelSw);
$obFormulario->defineBarra(array($obBtnIncluirTipoCombustivel,$obBtnLimparTipoCombustivel));

$obFormulario->addSpan($obSpnLista);

$obFormulario->Ok();

$obFormulario->show();

//Caso ja exista dados na base, carrega a lista
$jsOnLoad = "montaParametrosGET('montaLista');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
