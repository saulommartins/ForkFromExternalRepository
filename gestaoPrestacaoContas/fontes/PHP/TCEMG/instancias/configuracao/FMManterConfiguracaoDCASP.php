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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES . "ITextBoxSelectEntidadeGeral.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDCASP";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgProc = "PR" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgJs = "JS" . $stPrograma . ".js";

include_once ($pgJs);

$rsEntidades = new RecordSet();
$boTransacao = new Transacao();

$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");
$obForm->setName('frm');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setId("");

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');

$obNomeArquivo = new Select();
$obNomeArquivo->setRotulo("Nome do Arquivo");
$obNomeArquivo->setId('stTipoExportacao');
$obNomeArquivo->setNull(false);
$obNomeArquivo->setName('stTipoExportacao');
$obNomeArquivo->addOption('BO', 'BO');
$obNomeArquivo->addOption('BF', 'BF');
$obNomeArquivo->addOption('BP', 'BP');
$obNomeArquivo->addOption('DVP', 'DVP');
$obNomeArquivo->addOption('DFC', 'DFC');
$obNomeArquivo->addOption('RPSD', 'RPSD');
$obNomeArquivo->obEvento->setOnChange("document.getElementById('inMes').value = '';");

$obNomeCampo = new BuscaInner;
$obNomeCampo->setRotulo("Nome do Campo");
$obNomeCampo->setTitle("Informe o nome do campo para alterar suas contas");
$obNomeCampo->setId("stCampo");
$obNomeCampo->setName("stCampo");
$obNomeCampo->setValue($stCampo);
$obNomeCampo->setNull(false);
$obNomeCampo->obCampoCod->setName("inCodCampo");
$obNomeCampo->obCampoCod->setId("inCodCampo");
$obNomeCampo->obCampoCod->setNull(true);
$obNomeCampo->obCampoCod->setValue($inCodCampo);
$obNomeCampo->obCampoCod->setAlign("left");
$obNomeCampo->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaEstrutural', 'inClassificacao,inCodCampo', 'true');");
$obNomeCampo->setFuncaoBusca("abrePopUp('" . CAM_GF_CONT_POPUPS . "camposDcasp/FLCampos.php', 'frm', 'inCodCampo', 'stCampo', 'conta_analitica_estrutural', '" . Sessao::getId() . "&inCodIniEstrutural=1,2,5,6&tipoBusca2=extmmaa', '800', '550');");

$obTxtDescGrupo = new TextBox;
$obTxtDescGrupo->setRotulo("Grupo");
$obTxtDescGrupo->setTitle("Informe apenas os 2 primeiros números da conta. Ex.: (1.2)");
$obTxtDescGrupo->setName("inDescGrupo");
$obTxtDescGrupo->setSize(5);
$obTxtDescGrupo->setMaxLength(5);
$obTxtDescGrupo->setMascara('9.9.');
$obTxtDescGrupo->setNull(false);
// $obTxtDescGrupo->setValue($inDescGrupo);

$obBtnBuscar = new Button();
$obBtnBuscar->setId('btnBuscar');
$obBtnBuscar->setValue('Buscar');
// $obBtnOk->obEvento->setOnClick("montaParametrosGET('incluirConta','inClassificacao,inCodConta','true');");


// $obITextBoxSelectEntidadeGeral = new ITextBoxSelectEntidadeGeral();
// $obITextBoxSelectEntidadeGeral->setNull(false);
// $obITextBoxSelectEntidadeGeral->obSelect->obEvento->setOnChange("limpaSpan();");
// $obITextBoxSelectEntidadeGeral->obTextBox->obEvento->setOnChange("limpaSpan();");
//
// $obPeriodoMes = new Mes;
// $obPeriodoMes->obMes->setId('inMes');
// $obPeriodoMes->setExercicio(Sessao::getExercicio());
// $obPeriodoMes->setNull(false);
// $obPeriodoMes->obMes->obEvento->setOnChange ("if (validaCampos()) {montaParametrosGET('montaSpanCodigos');}");


//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Considerações por arquivo");
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addComponente($obNomeArquivo);
$obFormulario->addComponente($obNomeCampo);
$obFormulario->addComponente($obTxtDescGrupo);
$obFormulario->addComponente($obBtnBuscar);

// $obFormulario->addComponente($obITextBoxSelectEntidadeGeral);
// $obFormulario->addComponente($obPeriodoMes);
$obFormulario->addSpan($obSpnCodigos);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
