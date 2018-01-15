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
include_once CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeGeral.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO . 'TTCEMGCampoContaCorrente.class.php';

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

$obTipoRegistro= new Hidden;
$obTipoRegistro->setName("tipoRegistro");
$obTipoRegistro->setId("tipoRegistro");

$obCodArquivo = new Hidden;
$obCodArquivo->setName("codArquivo");
$obCodArquivo->setId("codArquivo");

$obSeqArquivo = new Hidden;
$obSeqArquivo->setName("seqArquivo");
$obSeqArquivo->setId("seqArquivo");

$obSpnContas = new Span();
$obSpnContas->setId('spnContas');

$obSpnContas2 = new Span();
$obSpnContas2->setId('spnContas2');

$obNomeArquivo = new Select();
$obNomeArquivo->setRotulo("Nome do Arquivo");
$obNomeArquivo->setTitle("Informe o nome do arquivo a qual o campo pertence");
$obNomeArquivo->setId('stNomeArquivo');
$obNomeArquivo->setNull(false);
$obNomeArquivo->setName('stNomeArquivo');
$obNomeArquivo->addOption('BO', 'BO');
$obNomeArquivo->addOption('BF', 'BF');
$obNomeArquivo->addOption('BP', 'BP');
$obNomeArquivo->addOption('DVP', 'DVP');
$obNomeArquivo->addOption('DFC', 'DFC');
$obNomeArquivo->addOption('RPSD', 'RPSD');
$obNomeArquivo->obEvento->setOnChange("limpaCampos(); limpaSpan();");

$obNomeCampo = new BuscaInner;
$obNomeCampo->setRotulo("Nome do Campo");
$obNomeCampo->setTitle("Informe o nome do campo para alterar suas contas");
$obNomeCampo->setId("stCampo");
$obNomeCampo->setName("stCampo");
$obNomeCampo->setNull(false);
$obNomeCampo->obCampoCod->setSize(30);
$obNomeCampo->obCampoCod->setName("inCodCampo");
$obNomeCampo->obCampoCod->setId("inCodCampo");
$obNomeCampo->obCampoCod->setNull(true);
$obNomeCampo->obCampoCod->setAlign("left");
$obNomeCampo->obCampoCod->obEvento->setOnBlur("1");
$obNomeCampo->setFuncaoBusca("abrePopUpDcasp('" . CAM_GF_CONT_POPUPS . "camposDcasp/FLCampos.php', 'frm', 'inCodCampo', 'stCampo', 'tipoRegistro', 'codArquivo', 'seqArquivo', jQuery('#stNomeArquivo').val(), '" . Sessao::getId() . "&inCodIniEstrutural=1,2,5,6&tipoBusca2=extmmaa', '800', '550');");

$obTxtDescGrupo = new TextBox;
$obTxtDescGrupo->setRotulo("Grupo");
$obTxtDescGrupo->setTitle("Informe apenas os 2 primeiros números da conta. Ex.: (1.2)");
$obTxtDescGrupo->setId("inDescGrupo");
$obTxtDescGrupo->setName("inDescGrupo");
$obTxtDescGrupo->setSize(5);
$obTxtDescGrupo->setMaxLength(5);
$obTxtDescGrupo->setMascara('9.9.');
$obTxtDescGrupo->setNull(false);

$obBtnBuscar = new Button();
$obBtnBuscar->setId('btnBuscar');
$obBtnBuscar->setValue('Buscar');
$obBtnBuscar->obEvento->setOnClick("if (validaCampos()) {montaParametrosGET('montaListagem');}");

$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addTitulo("Configuração DCASP por campo");
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obTipoRegistro);
$obFormulario->addHidden($obCodArquivo);
$obFormulario->addHidden($obSeqArquivo);
$obFormulario->addComponente($obNomeArquivo);
$obFormulario->addComponente($obNomeCampo);
$obFormulario->addComponente($obTxtDescGrupo);
$obFormulario->addComponente($obBtnBuscar);
$obFormulario->addSpan($obSpnContas);
$obFormulario->addSpan($obSpnContas2);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
