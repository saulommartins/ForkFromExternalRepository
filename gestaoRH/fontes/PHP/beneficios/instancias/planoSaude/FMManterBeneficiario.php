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
    * Página de Formulário para configuração
    * Data de Criação   : 11/01/2011

    * @author Carlos Adriano

    * @ignore
    *
    * $Id: FMManterConfiguracaoUnidadeOrcamentaria.php 45121 2011-01-27 19:52:49Z silvia $

    * Casos de uso : uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioBeneficiario.class.php");
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioModalidadeConvenioMedico.class.php");
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioTipoConvenioMedico.class.php");
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"      );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php" );
include_once (CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php");
include_once (CAM_GA_CGM_MAPEAMENTO."TGrauParentesco.class.php");
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

//Esvazia array que acumula lista de dívidas
Sessao::remove('arBeneficiario');

$stPrograma = "ManterBeneficiario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include($pgJs);
$stAcao = $request->get('stAcao');

if (empty($stAcao)) {
    $stAcao = "alterar";
}

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obHdnAcao = new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName("stCtrl");
$obHdnCtrl->setValue("");

$obHdnId = new Hidden();
$obHdnId->setId('hdnId');
$obHdnId->setName('hdnId');
$obHdnId->setValue('');

$obCGM = new IPopUpCGMVinculado($obForm);
$obCGM->setTabelaVinculo('sw_cgm');
$obCGM->setCampoVinculo('numcgm');
$obCGM->setNomeVinculo('Beneficiário');
$obCGM->setRotulo('*Beneficiário');
$obCGM->setName('stCGMBeneficiario');
$obCGM->setId('stCGMBeneficiario');
$obCGM->obCampoCod->setName('inCGMBeneficiario');
$obCGM->obCampoCod->setId('inCGMBeneficiario');
$obCGM->setNull(true);
$obCGM->setTipo("fisica");

$obTBeneficioModalidadeConvenioMedico = new TBeneficioModalidadeConvenioMedico();
$obTBeneficioModalidadeConvenioMedico->recuperaTodos($rsBeneficioModalidadeConvenioMedico);

$obCGMFornecedor = new IPopUpCGMVinculado($obForm);
$obCGMFornecedor->setTabelaVinculo('compras.fornecedor');
$obCGMFornecedor->setCampoVinculo('cgm_fornecedor');
$obCGMFornecedor->setNomeVinculo('Fornecedor do plano');
$obCGMFornecedor->setRotulo('*Fornecedor do plano');
$obCGMFornecedor->setName('stCGMFornecedor');
$obCGMFornecedor->setId('stCGMFornecedor');
$obCGMFornecedor->obCampoCod->setName('inCGMFornecedor');
$obCGMFornecedor->obCampoCod->setId('inCGMFornecedor');
$obCGMFornecedor->setNull(true);

$obTBeneficioModalidadeConvenioMedico = new TBeneficioModalidadeConvenioMedico();
$obTBeneficioModalidadeConvenioMedico->recuperaTodos($rsBeneficioModalidadeConvenioMedico);

$obCmbBeneficioModalidadeConvenioMedico = new Select();
$obCmbBeneficioModalidadeConvenioMedico->setRotulo('*Modalidade do convênio médico');
$obCmbBeneficioModalidadeConvenioMedico->setTitle('Selecione a modalidade');
$obCmbBeneficioModalidadeConvenioMedico->setName('inModalidade');
$obCmbBeneficioModalidadeConvenioMedico->setId('inModalidade');
$obCmbBeneficioModalidadeConvenioMedico->addOption('', 'Selecione');
$obCmbBeneficioModalidadeConvenioMedico->setCampoId('cod_modalidade');
$obCmbBeneficioModalidadeConvenioMedico->setCampoDesc('descricao');
$obCmbBeneficioModalidadeConvenioMedico->setStyle('width: 300');
$obCmbBeneficioModalidadeConvenioMedico->preencheCombo($rsBeneficioModalidadeConvenioMedico);

$obTBeneficioTipoConvenioMedico = new TBeneficioTipoConvenioMedico();
$obTBeneficioTipoConvenioMedico->recuperaTodos($rsBeneficioTipoConvenioMedico);

$obCmbBeneficioTipoConvenioMedico = new Select();
$obCmbBeneficioTipoConvenioMedico->setRotulo('*Tipo de convênio médico');
$obCmbBeneficioTipoConvenioMedico->setTitle('Selecione a tipo');
$obCmbBeneficioTipoConvenioMedico->setName('inTipo');
$obCmbBeneficioTipoConvenioMedico->setId('inTipo');
$obCmbBeneficioTipoConvenioMedico->addOption('', 'Selecione');
$obCmbBeneficioTipoConvenioMedico->setCampoId('cod_tipo_convenio');
$obCmbBeneficioTipoConvenioMedico->setCampoDesc('descricao');
$obCmbBeneficioTipoConvenioMedico->setStyle('width: 300');
$obCmbBeneficioTipoConvenioMedico->preencheCombo($rsBeneficioTipoConvenioMedico);

$obTGrauParentesco = new TGrauParentesco();
$obTGrauParentesco->recuperaTodos($rsGrauParentesco);

$obCmbGrauParentesco = new Select();
$obCmbGrauParentesco->setRotulo('*Grau de parentesco');
$obCmbGrauParentesco->setTitle('Selecione a o grau de parentesco');
$obCmbGrauParentesco->setName('inGrauParentesco');
$obCmbGrauParentesco->setId('inGrauParentesco');
$obCmbGrauParentesco->addOption('', 'Selecione');
$obCmbGrauParentesco->setCampoId('cod_grau');
$obCmbGrauParentesco->setCampoDesc('nom_grau');
$obCmbGrauParentesco->setStyle('width: 300');
$obCmbGrauParentesco->preencheCombo($rsGrauParentesco);

$obTxtCodigoUsuario = new Inteiro();
$obTxtCodigoUsuario->setRotulo('*Código de Usuário no Plano de Saúde');
$obTxtCodigoUsuario->setName('inCodUsuario');
$obTxtCodigoUsuario->setId('inCodUsuario');
$obTxtCodigoUsuario->setSize(10);
$obTxtCodigoUsuario->setMaxLength(10);

$obTxtDtInicioBeneficio = new Data();
$obTxtDtInicioBeneficio->setName('dtInicioBeneficio');
$obTxtDtInicioBeneficio->setId('dtInicioBeneficio');
$obTxtDtInicioBeneficio->setRotulo('*Início do benefício');
$obTxtDtInicioBeneficio->setSize(10);
$obTxtDtInicioBeneficio->setMaxLength(10);

$obTxtDtFimBeneficio = new Data();
$obTxtDtFimBeneficio->setName('dtFimBeneficio');
$obTxtDtFimBeneficio->setId('dtFimBeneficio');
$obTxtDtFimBeneficio->setRotulo('Término do benefício');
$obTxtDtFimBeneficio->setSize(10);
$obTxtDtFimBeneficio->setMaxLength(10);

$obTxtVlDesconto = new Moeda;
$obTxtVlDesconto->setName('vlDesconto');
$obTxtVlDesconto->setId('vlDesconto');
$obTxtVlDesconto->setValue('');
$obTxtVlDesconto->setRotulo('*Valor do desconto');
$obTxtVlDesconto->setTitle('Informe o valor do desconto');
$obTxtVlDesconto->setSize(10);
$obTxtVlDesconto->setMaxLength(10);

$obSpanBeneficiario = new Span;
$obSpanBeneficiario->setId('spnBeneficiario');

$obBtOk = new Button();
$obBtOk->setValue('Incluir');
$obBtOk->setId('btIncluir');
$obBtOk->obEvento->setOnCLick("montaParametrosGET('incluiBeneficiario', 'hdnId,inContrato,inCGMBeneficiario,inCGMFornecedor,inModalidade,inTipo,inCodUsuario,inGrauParentesco,dtInicioBeneficio,dtFimBeneficio,vlDesconto');");

$obBtLimpar = new Button();
$obBtLimpar->setValue('Limpar');
$obBtLimpar->obEvento->setOnClick("limparBeneficiario();");

/****
 ****  Montagem do formulário
 ****/

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo('Titular do Plano de Saúde');

$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnId);

$obIFiltroComponentes = new IFiltroContrato();
$obIFiltroComponentes->geraFormulario($obFormulario);
$onBlur = $obIFiltroComponentes->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur()." buscaBeneficiarios(this);";
$obIFiltroComponentes->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur($onBlur);

$obFormulario->addTitulo('Beneficiários do Plano de Saúde');
$obFormulario->addComponente($obCGM);
$obFormulario->addComponente($obCGMFornecedor);
$obFormulario->addComponente($obCmbBeneficioModalidadeConvenioMedico);
$obFormulario->addComponente($obCmbBeneficioTipoConvenioMedico);
$obFormulario->addComponente($obTxtCodigoUsuario);
$obFormulario->addComponente($obCmbGrauParentesco);
$obFormulario->addComponente($obTxtDtInicioBeneficio);
$obFormulario->addComponente($obTxtDtFimBeneficio);
$obFormulario->addComponente($obTxtVlDesconto);

$obFormulario->defineBarra(array($obBtOk, $obBtLimpar));
$obFormulario->addSpan($obSpanBeneficiario);

$obOk = new Ok();
$obLimpar = new Limpar();
$obFormulario->defineBarra(array($obOk, $obLimpar));

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
