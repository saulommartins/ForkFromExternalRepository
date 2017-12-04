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
    * Página de Formulario de Inclusao de Empenho Diverso
    * Data de Criação   : 22/12/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo
    * @author Desenvolvedor Eduardo Martins
    * @author Desenvolvedor Fábio Bertoldi Rodrigues

    * @ignore

    $Id: FMManterEmpenhoDiversos.php 65471 2016-05-24 18:58:44Z michel $

    * Casos de uso: uc-02.03.03
                    uc-02.03.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE.'validaGF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoConfiguracao.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenho.class.php';
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNFundeb.class.php';
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNRoyalties.class.php';
include_once CAM_FW_HTML.'MontaAtributos.class.php';
include_once CAM_GF_ORC_COMPONENTES.'IPopUpDotacaoFiltroClassificacao.class.php';
include_once CAM_GP_LIC_COMPONENTES.'IPopUpContrato.class.php';
require_once CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

//Define o nome dos arquivos PHP
$stPrograma    = 'ManterEmpenho';
$pgFormDiverso = 'FMManterEmpenhoDiversos.php';
$pgProcDiverso = 'PRManterEmpenhoDiversos.php';
$pgFilt        = 'FL'.$stPrograma.'.php';
$pgList        = 'LS'.$stPrograma.'.php';
$pgForm        = 'FM'.$stPrograma.'.php';
$pgProc        = 'PRManterEmpenhoDiversos.php';
$pgOcul        = 'OC'.$stPrograma.'.php';
$pgJS          = 'JS'.$stPrograma.'.js';

SistemaLegado::liberaFrames();

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

include_once ($pgJS);

Sessao::remove('arItens');
Sessao::remove('arBuscaContrato');
Sessao::write('stTituloPagina', 'Gestão Financeira | Empenho | Empenho | Emitir Empenho Diversos');

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    $inCodHistorico = 0;

    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
    $obREmpenhoConfiguracao       = new REmpenhoConfiguracao;
    $obREmpenhoEmpenho            = new REmpenhoEmpenho;

    if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20) {
        $obTTCERNRoyalties            = new TTCERNRoyalties;
        $obTTCERNFundeb               = new TTCERNFundeb;

        $obTTCERNRoyalties->recuperaTodos($rsRoyalties, '', 'codigo');
        $obTTCERNFundeb->recuperaTodos($rsFundeb, '', 'codigo');
    }

    $obREmpenhoConfiguracao->consultar();

    $boLiquidacaoAutomatica = $obREmpenhoConfiguracao->getLiquidacaoAutomatica();

    $rsOrgao = $rsUnidade = new RecordSet ;

    $rsClassificacao = new RecordSet;
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarEntidadeRestos($rsEntidade);

    $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo, " cod_tipo <> 0 ");
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->listar($rsHistorico);
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao, $stOrder);
    $obREmpenhoAutorizacaoEmpenho->listarUnidadeMedida($rsUnidade);
    while (!$rsUnidade->eof()) {
        if ($rsUnidade->getCampo("nom_unidade" ) == 'Unidade') {
            $inCodUnidade = $rsUnidade->getCampo('cod_unidade').'-'.$rsUnidade->getCampo('cod_grandeza').'-'.$rsUnidade->getCampo('nom_unidade');
            $inCodUnidadePadrao = $rsUnidade->getCampo('cod_unidade').'-'.$rsUnidade->getCampo('cod_grandeza').'-'.$rsUnidade->getCampo('nom_unidade');
        }
        $rsUnidade->proximo();
    }
    $rsUnidade->setPrimeiroElemento();

    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento($stFormaExecucao);

    $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());
    $stMascaraRubrica = $obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

    $stDtValidadeInicial = date('d/m')."/".Sessao::getExercicio();
    $stDtVencimento = "31/12/".Sessao::getExercicio();

    $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionados($rsAtributos);

    //*****************************************************//
    // Define COMPONENTES DO FORMULARIO
    //*****************************************************//
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction($pgProcDiverso);
    $obForm->setTarget('oculto');

    //Define o objeto da data do ultimo Empenho cadastrado

    $rsUltimoEmpenho = new RecordSet();
    $dtUltimaDataEmpenho = "01/01/".Sessao::getExercicio();

    $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
    $obREmpenhoEmpenho->listarMaiorData($rsUltimoEmpenho);

    if ($rsUltimoEmpenho->getCampo("dataempenho") != "") {
        $dtUltimaDataEmpenho = $rsUltimoEmpenho->getCampo('dataempenho');
    }

    if ($rsUltimoEmpenho->getCampo("dt_empenho") != "") {
        $dtUltimaDataEmpenho = $rsUltimoEmpenho->getCampo('dt_empenho');
    }

    $obHdnUltimaDataEmpenho = new Hidden;
    $obHdnUltimaDataEmpenho->setName ('dtUltimaDataEmpenho');
    $obHdnUltimaDataEmpenho->setValue($dtUltimaDataEmpenho);

    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ('stAcao');
    $obHdnAcao->setValue($stAcao);

    //Define o Hidden para valor padrao da unidade
    $obHdnUnidadePadrao = new Hidden;
    $obHdnUnidadePadrao->setName ('inCodUnidadePadrao');
    $obHdnUnidadePadrao->setValue($inCodUnidadePadrao);

    //Define o objeto de controle
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName ('stCtrl');
    $obHdnCtrl->setValue('');

    $obHdnNumItem = new Hidden;
    $obHdnNumItem->setName ('hdnNumItem');
    $obHdnNumItem->setValue($hdnNumItem);

    //Define o Hidden de Valor de Reserva
    $obHdnVlReserva = new Hidden;
    $obHdnVlReserva->setId   ('hdnVlReserva');
    $obHdnVlReserva->setName ('nuVlReserva');
    $obHdnVlReserva->setValue(0);

    //Define o Hidden de Valor de Reserva
    $obHdnOrgaoOrcamento = new Hidden;
    $obHdnOrgaoOrcamento->setId   ('hdnOrgaoOrcamento');
    $obHdnOrgaoOrcamento->setName ('hdnOrgaoOrcamento');
    $obHdnOrgaoOrcamento->setValue($inCodOrgao);

    //Define o Hidden de Valor de Reserva
    $obHdnUnidadeOrcamento = new Hidden;
    $obHdnUnidadeOrcamento->setId   ('hdnUnidadeOrcamento');
    $obHdnUnidadeOrcamento->setName ('hdnUnidadeOrcamento');
    $obHdnUnidadeOrcamento->setValue($inCodUnidadeOrcamento);

    // Define objeto HiddenEval para travar o botão OK
    $obHdnTrava = new HiddenEval;
    $obHdnTrava->setName ('hdnValidaData');
    $obHdnTrava->setValue('document.frm.Ok.disabled = true;');

    //Define o Hidden para armazenar o cod despesa antes de abrir a popup para alterar o mesmo
    $obHdnCodDespesa = new Hidden;
    $obHdnCodDespesa->setName ('inCodDespesaAnterior');
    $obHdnCodDespesa->setId   ('inCodDespesaAnterior');
    $obHdnCodDespesa->setValue('');

    //Define o objeto para validacao da data do fornecedor
    $obHdnValidaFornecedor = new Hidden;
    $obHdnValidaFornecedor->setName ('boMsgValidadeFornecedor');
    $obHdnValidaFornecedor->setId   ('boMsgValidadeFornecedor');
    $obHdnValidaFornecedor->setValue('false');

    //Define o objeto para validacao da data do contrato
    $obHdnDtContrato = new Hidden;
    $obHdnDtContrato->setName ('dtContrato');
    $obHdnDtContrato->setId   ('dtContrato');
    $obHdnDtContrato->setValue('');

    // Define Objeto TextBox para Codigo da Entidade
    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setName('inCodEntidade');
    $obTxtCodEntidade->setId  ('inCodEntidade');

    if ($rsEntidade->getNumLinhas()==1) {
        $obTxtCodEntidade->setValue($rsEntidade->getCampo("cod_entidade"));
        $jsOnload = "montaParametrosGET('buscaDtEmpenho', 'inCodEntidade');";
    }

    $obTxtCodEntidade->setRotulo ('Entidade');
    $obTxtCodEntidade->setTitle  ('Selecione a entidade.');
    $obTxtCodEntidade->setInteiro(true);
    $obTxtCodEntidade->setNull   (false);

    // Define Objeto Select para Nome da Entidade
    $obCmbNomEntidade = new Select;
    $obCmbNomEntidade->setName ('stNomEntidade');
    $obCmbNomEntidade->setId   ('stNomEntidade');
    $obCmbNomEntidade->setValue($inCodEntidade);

    if ($rsEntidade->getNumLinhas()>1) {
        $obCmbNomEntidade->addOption            ('', 'Selecione');
        $obCmbNomEntidade->obEvento->setOnChange("limparCampos();montaParametrosGET('buscaDtEmpenho', 'inCodEntidade,inCodContrato,inCodFornecedor,stExercicioContrato');getIMontaAssinaturas();");
        $obTxtCodEntidade->obEvento->setOnChange("limparCampos();montaParametrosGET('buscaDtEmpenho', 'inCodEntidade,inCodContrato,inCodFornecedor,stExercicioContrato');getIMontaAssinaturas();");
    }
    $obCmbNomEntidade->setCampoId   ('cod_entidade');
    $obCmbNomEntidade->setCampoDesc ('nom_cgm');
    $obCmbNomEntidade->setStyle     ('width: 520');
    $obCmbNomEntidade->preencheCombo($rsEntidade);
    $obCmbNomEntidade->setNull      (false);

    $obIPopUpDotacao = new IPopUpDotacaoFiltroClassificacao($obCmbNomEntidade);
    $obIPopUpDotacao->obCampoCod->setName('inCodDespesa');
    $obIPopUpDotacao->obCampoCod->setId  ('inCodDespesa');
    $obIPopUpDotacao->setNull            (false);
    $obIPopUpDotacao->setId              ('stNomDespesa');
    $obIPopUpDotacao->setValue           ($stNomDespesa);
    $obIPopUpDotacao->setTipoBusca       ('autorizacaoEmpenho');
    $obIPopUpDotacao->obCampoCod->obEvento->setOnBlur("if (this.value!=document.frm.inCodDespesaAnterior.value) {document.frm.inCodDespesaAnterior.value=this.value; BloqueiaFrames(true,false);buscaDado('buscaDespesaDiverso');}");

    // Define Objeto Select para Classificacao da Despesa
    $obCmbClassificacao = new Select;
    $obCmbClassificacao->setRotulo    ('Desdobramento');
    $obCmbClassificacao->setTitle     ('Informe a rubrica de despesa.');
    $obCmbClassificacao->setName      ('stCodClassificacao');
    $obCmbClassificacao->setId        ('stCodClassificacao');
    $obCmbClassificacao->setValue     ($stCodClassificacao);
    $obCmbClassificacao->setStyle     ('width: 600');
    $obCmbClassificacao->setNull      (($stFormaExecucao) ? false : true );
    $obCmbClassificacao->setReadOnly  (($stFormaExecucao) ? false : true );
    $obCmbClassificacao->addOption    ('', 'Selecione');
    $obCmbClassificacao->setCampoId   ('cod_estrutural');
    $obCmbClassificacao->setCampoDesc ('cod_estrutural');
    $obCmbClassificacao->preencheCombo($rsClassificacao);

    // Define Objeto Span Para lista de itens
    $obSpanSaldo = new Span;
    $obSpanSaldo->setId('spnSaldoDotacao');

    $obLblOrgaoOrcamento = new Label;
    $obLblOrgaoOrcamento->setRotulo('Órgão Orçamentário');
    $obLblOrgaoOrcamento->setId    ('stOrgaoOrcamento');
    $obLblOrgaoOrcamento->setValue ($stOrgaoOrcamento);

    $obLblUnidadeOrcamento = new Label;
    $obLblUnidadeOrcamento->setRotulo('Unidade Orçamentária');
    $obLblUnidadeOrcamento->setId    ('stUnidadeOrcamento');
    $obLblOrgaoOrcamento->setValue   ($stUnidadeOrcamento);

    // Define Objeto BuscaInner para Fornecedor
    $obBscFornecedor = new BuscaInner;
    $obBscFornecedor->setRotulo('Fornecedor');
    $obBscFornecedor->setTitle ('Informe o fornecedor.');
    $obBscFornecedor->setId    ('stNomFornecedor');
    $obBscFornecedor->setValue ($stNomFornecedor);
    $obBscFornecedor->setNull  (false);
    $obBscFornecedor->obCampoCod->setName     ('inCodFornecedor');
    $obBscFornecedor->obCampoCod->setSize     (10);
    $obBscFornecedor->obCampoCod->setNull     (false);
    $obBscFornecedor->obCampoCod->setMaxLength(8);
    $obBscFornecedor->obCampoCod->setValue    ($inCodFornecedor);
    $obBscFornecedor->obCampoCod->setAlign    ('left');
    $obBscFornecedor->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaFornecedorDiverso', 'inCodFornecedor');
                                                         montaParametrosGET('buscaContrapartida', 'inCodFornecedor, inCodCategoria');
                                                         montaParametrosGET('verificaFornecedor', 'inCodFornecedor, inCodCategoria, inCodContraPartida, stDtEmpenho');");
    $obBscFornecedor->setFuncaoBusca("window.parent.frames['telaPrincipal'].document.frm.inCodFornecedor.focus(); abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");
    $obBscFornecedor->obCampoCod->obEvento->setOnBlur("montaParametrosGET('montaBuscaContrato', 'inCodContrato,inCodEntidade,inCodFornecedor,stExercicioContrato');");

    // Define Objeto Select para Categoria do Empenho
    include_once TEMP.'TEmpenhoCategoriaEmpenho.class.php';

    $rsCategoriaEmpenho = new RecordSet();
    $obCategoriaEmpenho = new TEmpenhoCategoriaEmpenho();

    $obCategoriaEmpenho->recuperaTodos($rsCategoriaEmpenho);

    $obCmbCategoriaEmpenho = new Select;
    $obCmbCategoriaEmpenho->setRotulo    ('Categoria do Empenho');
    $obCmbCategoriaEmpenho->setTitle     ('Informe a categoria do empenho.');
    $obCmbCategoriaEmpenho->setName      ('inCodCategoria');
    $obCmbCategoriaEmpenho->setId        ('inCodCategoria');
    $obCmbCategoriaEmpenho->setNull      (false);
    $obCmbCategoriaEmpenho->setValue     ($stCodCategoria);
    $obCmbCategoriaEmpenho->setStyle     ('width: 250');
    $obCmbCategoriaEmpenho->setCampoId   ('cod_categoria');
    $obCmbCategoriaEmpenho->setCampoDesc ('descricao');
    $obCmbCategoriaEmpenho->preencheCombo($rsCategoriaEmpenho);
    $obCmbCategoriaEmpenho->obEvento->setOnChange("montaParametrosGET('buscaContrapartida', 'inCodFornecedor, inCodCategoria');");

    // Define Objeto Span Para Contrapartida
    $obSpanContrapartida = new Span;
    $obSpanContrapartida->setId('spnContrapartida');

    // Define Objeto TextArea para Descricao
    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setName         ('stDescricao');
    $obTxtDescricao->setId           ('stDescricao');
    $obTxtDescricao->setValue        ($stDescricao);
    $obTxtDescricao->setRotulo       ('Descrição do Empenho');
    $obTxtDescricao->setTitle        ('Informe a descrição do empenho.');
    $obTxtDescricao->setNull         (true);
    $obTxtDescricao->setRows         (6);
    $obTxtDescricao->setCols         (100);
    $obTxtDescricao->setMaxCaracteres(640);

    // Define Objeto Select para Histórico
    $obCmbHistorico = new Select;
    $obCmbHistorico->setName      ('inCodHistorico');
    $obCmbHistorico->setRotulo    ('Histórico Padrão');
    $obCmbHistorico->setTitle     ('Selecione o histórico padrão.');
    $obCmbHistorico->setId        ('inCodHistorico');
    $obCmbHistorico->setValue     ($inCodHistorico);
    $obCmbHistorico->addOption    ('', 'Selecione');
    $obCmbHistorico->setCampoId   ('cod_historico');
    $obCmbHistorico->setCampoDesc ('nom_historico');
    $obCmbHistorico->preencheCombo($rsHistorico);
    $obCmbHistorico->setNull      (true);

    $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());

    if ($inCodUF == 20) {
        $obCmbFundeb = new Select;
        $obCmbFundeb->setName      ('inCodFundeb');
        $obCmbFundeb->setRotulo    ('Fundeb');
        $obCmbFundeb->setTitle     ('Selecione Fundeb.');
        $obCmbFundeb->setId        ('inCodFundeb');
        $obCmbFundeb->setCampoId   ('cod_fundeb');
        $obCmbFundeb->setCampoDesc ('codigo');
        $obCmbFundeb->preencheCombo($rsFundeb);
        $obCmbFundeb->setNull      (false);

        $obCmbRoyalties = new Select;
        $obCmbRoyalties->setName      ('inCodRoyalties');
        $obCmbRoyalties->setRotulo    ('Royalties');
        $obCmbRoyalties->setTitle     ('Selecione Royalties.');
        $obCmbRoyalties->setId        ('inCodRoyalties');
        $obCmbRoyalties->setCampoId   ('cod_royalties');
        $obCmbRoyalties->setCampoDesc ('codigo');
        $obCmbRoyalties->preencheCombo($rsRoyalties);
        $obCmbRoyalties->setNull      (false);
    }

    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
        $obTxtProcessoLicitacao = new TextBox;
        $obTxtProcessoLicitacao->setName            ('stProcessoLicitacao');
        $obTxtProcessoLicitacao->setId              ('stProcessoLicitacao');
        $obTxtProcessoLicitacao->setRotulo          ('Número Processo Licitação');
        $obTxtProcessoLicitacao->setTitle           ('Informe o número do Processo de Licitação.');
        $obTxtProcessoLicitacao->setNull            (true);
        $obTxtProcessoLicitacao->setMaxLength       (8);
        $obTxtProcessoLicitacao->setSize            (8);

        $obTxtExercicioLicitacao = new TextBox;
        $obTxtExercicioLicitacao->setName           ('stExercicioLicitacao');
        $obTxtExercicioLicitacao->setId             ('stExercicioLicitacao');
        $obTxtExercicioLicitacao->setRotulo         ('Ano Processo Licitação');
        $obTxtExercicioLicitacao->setTitle          ('Informe o ano do Processo de Licitação.');
        $obTxtExercicioLicitacao->setInteiro        (true);
        $obTxtExercicioLicitacao->setNull           (true);
        $obTxtExercicioLicitacao->setMaxLength      (4);
        $obTxtExercicioLicitacao->setSize           (4);

        $obTxtProcessoAdministrativo = new TextBox;
        $obTxtProcessoAdministrativo->setName            ('stProcessoAdministrativo');
        $obTxtProcessoAdministrativo->setId              ('stProcessoAdministrativo');
        $obTxtProcessoAdministrativo->setRotulo          ('Número Processo Administrativo');
        $obTxtProcessoAdministrativo->setTitle           ('Informe o número do Processo Administrativo.');
        $obTxtProcessoAdministrativo->setNull            (true);
        $obTxtProcessoAdministrativo->setMaxLength       (20);
        $obTxtProcessoAdministrativo->setSize            (20);
    }

    // Define Objeto TextArea para Descricao do Item
    $obTxtNomItem = new TextArea;
    $obTxtNomItem->setName            ('stNomItem');
    $obTxtNomItem->setId              ('stNomItem');
    $obTxtNomItem->setValue           ($stNomItem);
    $obTxtNomItem->setRotulo          ('* Descrição do Item');
    $obTxtNomItem->setTitle           ('Informe a descrição do item.');
    $obTxtNomItem->setRows            (2);
    $obTxtNomItem->setCols            (100);
    $obTxtNomItem->setMaxCaracteres   (160);
    $obTxtNomItem->obEvento->setOnBlur('proximoFoco(this.value);');

    // Define Objeto TextArea para Complemento
    $obTxtComplemento = new TextArea;
    $obTxtComplemento->setName  ('stComplemento');
    $obTxtComplemento->setId    ('stComplemento');
    $obTxtComplemento->setValue ($stComplemento);
    $obTxtComplemento->setRotulo('Complemento');
    $obTxtComplemento->setTitle ('Informe o complemento.');
    $obTxtComplemento->setRows  (3);
    $obTxtComplemento->setCols  (100);

    $obMarca = new IPopUpMarca($obForm);
    $obMarca->setNull               ( true );
    $obMarca->setRotulo             ( 'Marca' );
    $obMarca->setId                 ( 'stNomeMarca' );
    $obMarca->setName               ( 'stNomeMarca' );
    $obMarca->obCampoCod->setName   ( 'inMarca' );
    $obMarca->obCampoCod->setId     ( 'inMarca' );

    // Define Objeto Numeric para Quantidade
    $obTxtQuantidade = new Numerico;
    $obTxtQuantidade->setName     ('nuQuantidade');
    $obTxtQuantidade->setId       ('nuQuantidade');
    $obTxtQuantidade->setValue    ($nuQuantidade);
    $obTxtQuantidade->setRotulo   ('* Quantidade');
    $obTxtQuantidade->setTitle    ('Informe a quantidade.');
    $obTxtQuantidade->setNegativo (false);
    $obTxtQuantidade->setSize     (23);
    $obTxtQuantidade->setMaxLength(9);
    $obTxtQuantidade->setDecimais (4);
    $obTxtQuantidade->setFormatarNumeroBR  (true);
    $obTxtQuantidade->obEvento->setOnChange('gerarValorTotal(this);');

    // Define Objeto Select para Unidade
    $obCmbUnidade = new Select;
    $obCmbUnidade->setName      ( "inCodUnidade" );
    $obCmbUnidade->setId        ( "inCodUnidade" );
    $obCmbUnidade->setRotulo    ( "* Unidade" );
    $obCmbUnidade->setTitle     ( "Informe a unidade." );
    $obCmbUnidade->setValue     ( $inCodUnidade  );
    $obCmbUnidade->addOption    ( "", "Selecione"  );
    $obCmbUnidade->setCampoId   ( "[cod_unidade]-[cod_grandeza]-[nom_unidade]" );
    $obCmbUnidade->setCampoDesc ( "nom_unidade"  );
    $obCmbUnidade->preencheCombo( $rsUnidade     );

    // Define Objeto Moeda para Valor Unitário
    $obTxtVlUnitario = new ValorUnitario;
    $obTxtVlUnitario->setName              ('nuVlUnitario');
    $obTxtVlUnitario->setId                ('nuVlUnitario');
    $obTxtVlUnitario->setValue             ($nuVlUnitario);
    $obTxtVlUnitario->setRotulo            ('* Valor Unitário');
    $obTxtVlUnitario->setTitle             ('Informe o valor unitário.');
    $obTxtVlUnitario->setDecimais          (4);
    $obTxtVlUnitario->setSize              (21);
    $obTxtVlUnitario->setMaxLength         (10);
    $obTxtVlUnitario->setFormatarNumeroBR  (true);
    $obTxtVlUnitario->obEvento->setOnChange('geraValor(this); gerarValorTotal(this);');

    // Define Objeto Moeda para Valor Unitário
    $obTxtVlTotal = new ValorTotal;
    $obTxtVlTotal->setName              ('nuVlTotal');
    $obTxtVlTotal->setId                ('nuVlTotal');
    $obTxtVlTotal->setValue             ($nuVlTotal);
    $obTxtVlTotal->setRotulo            ('*Valor Total');
    $obTxtVlTotal->setTitle             ('Informe o valor total.');
    $obTxtVlTotal->setReadOnly          (true);
    $obTxtVlTotal->setSize              (21);
    $obTxtVlTotal->setMaxLength         (12);
    $obTxtVlTotal->setFormatarNumeroBR  (true);
    $obTxtVlTotal->obEvento->setOnChange('gerarValorTotal(this);');

    // Define Objeto Button para  Incluir Item
    $obBtnIncluir = new Button;
    $obBtnIncluir->setValue            ('Incluir');
    $obBtnIncluir->obEvento->setOnClick('incluirItem();');
    $obBtnIncluir->setName             ('btnIncluir');
    $obBtnIncluir->setId               ('btnIncluir');

    // Define Objeto Button para Limpar
    $obBtnLimpar = new Button;
    $obBtnLimpar->setValue            ('Limpar');
    $obBtnLimpar->obEvento->setOnClick("limparItem();document.getElementById('stNomItem').focus();");

    // Define Objeto Span Para lista de itens
    $obSpan = new Span;
    $obSpan->setId('spnLista');

    // Define Objeto Label para Valor Total dos Itens
    $obLblVlTotal = new Label;
    $obLblVlTotal->setId    ('nuValorTotal');
    $obLblVlTotal->setRotulo('TOTAL: ');

    // Define Objeto Label para Valor da Reserva
    $obLblReserva = new Label;
    $obLblReserva->setId    ('nuVlReserva');
    $obLblReserva->setRotulo('Valor da reserva: ');

    // Define Objeto TextBox para Codigo do Tipo de Empenho
    $obTxtCodTipo = new TextBox;
    $obTxtCodTipo->setName   ('inCodTipo');
    $obTxtCodTipo->setId     ('inCodTipo');
    $obTxtCodTipo->setValue  ($inCodTipo != '' ? $inCodTipo : 1);
    $obTxtCodTipo->setRotulo ('Tipo de Empenho');
    $obTxtCodTipo->setTitle  ('Selecione o tipo de empenho.');
    $obTxtCodTipo->setInteiro(true);
    $obTxtCodTipo->setNull   (false);

    // Define Objeto Select para Nome do tipo de empenho
    $obCmbNomTipo = new Select;
    $obCmbNomTipo->setName      ('stNomTipo');
    $obCmbNomTipo->setId        ('stNomTipo');
    $obCmbNomTipo->setValue     ($inCodTipo != '' ? $inCodTipo : 1);
    $obCmbNomTipo->setCampoId   ('cod_tipo');
    $obCmbNomTipo->setCampoDesc ('nom_tipo');
    $obCmbNomTipo->preencheCombo($rsTipo);
    $obCmbNomTipo->setNull      (false);

    // Define objeto Data para validade final
    $obDtEmpenho = new Data;
    $obDtEmpenho->setName              ('stDtEmpenho');
    $obDtEmpenho->setId                ('stDtEmpenho');
    $obDtEmpenho->setRotulo            ('Data de Empenho');
    $obDtEmpenho->setTitle             ('Informe a data do empenho.');
    $obDtEmpenho->setNull              (false);
    $obDtEmpenho->obEvento->setOnChange("montaParametrosGET('verificaFornecedor', 'inCodFornecedor, inCodCategoria, inCodContraPartida'); buscaDado('buscaDespesaDiverso')");
    $obDtEmpenho->setLabel             ( TRUE );
    $obDtEmpenho->setValue             ($dtUltimaDataEmpenho);
    $jsOnLoad .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','LiberaDataEmpenho');";

    // Define objeto Data para validade final
    $obDtValidadeFinal = new Data;
    $obDtValidadeFinal->setName              ('stDtVencimento');
    $obDtValidadeFinal->setValue             ($stDtVencimento );
    $obDtValidadeFinal->setRotulo            ('Data de Vencimento');
    $obDtValidadeFinal->setTitle             ('');
    $obDtValidadeFinal->setNull              (false);
    $obDtValidadeFinal->obEvento->setOnChange('validaVencimento();');

    $obMontaAtributos = new MontaAtributos;
    $obMontaAtributos->setTitulo   ('Atributos');
    $obMontaAtributos->setName     ('Atributo_');
    $obMontaAtributos->setRecordSet($rsAtributos);

    $rsAtributos->setPrimeiroElemento();
    while (!$rsAtributos->eof()) {
        $stAtributos .= 'Atributo_'.$rsAtributos->getCampo('cod_atributo').'_'.$rsAtributos->getCampo('cod_cadastro').','.$rsAtributos->getCampo('nao_nulo').','.$rsAtributos->getCampo('nom_atributo').'#';
        $rsAtributos->proximo();
    }

    $obHdnAtributos = new Hidden;
    $obHdnAtributos->setName('HdnAtributos');
    $obHdnAtributos->setValue($stAtributos);

    $rsAtributos->setPrimeiroElemento();

    if ($boLiquidacaoAutomatica=="true") {
        $stLiquidacaoAutomatica = 'SIM';
    } else {
        $stLiquidacaoAutomatica = 'NAO';
    }

    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
        include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOModalidade.php';

        /* Monta combo com modalidades de licitação */
        $obModalidadeLicitacao = new TTCMGOModalidade();
        $obModalidadeLicitacao->recuperaTodos($rsModalidadeLicitacao);

        $obCmbModalidadeLicitacao = new Select;
        $obCmbModalidadeLicitacao->setRotulo ('Modalidade');
        $obCmbModalidadeLicitacao->setName('inModalidadeLicitacao');
        $obCmbModalidadeLicitacao->setId('inModalidadeLicitacao');
        $obCmbModalidadeLicitacao->setStyle('width: 520');
        $obCmbModalidadeLicitacao->setCampoId('cod_modalidade');
        $obCmbModalidadeLicitacao->setCampoDesc('descricao');
        $obCmbModalidadeLicitacao->addOption('', 'Selecione');
        $obCmbModalidadeLicitacao->preencheCombo($rsModalidadeLicitacao);
        $obCmbModalidadeLicitacao->setNull(false);
        $obCmbModalidadeLicitacao->obEvento->setOnChange('verificaModalidade(this);');

        // Define Objeto Span Para lista de itens
        $obSpanFundamentacaoLegal = new Span;
        $obSpanFundamentacaoLegal->setId('spnFundamentacaoLegal');
    }

    //Radio para definicao de tipo Item
    $obRdTipoItemC = new Radio;
    $obRdTipoItemC->setTitle      ( "Selecione o tipo de Item" );
    $obRdTipoItemC->setRotulo     ( "**Item do Almoxarifado" );
    $obRdTipoItemC->setName       ( "stTipoItemRadio" );
    $obRdTipoItemC->setId         ( "stTipoItemRadio1" );
    $obRdTipoItemC->setValue      ( "Catalogo" );
    $obRdTipoItemC->setLabel      ( "Sim" );
    $obRdTipoItemC->obEvento->setOnClick( "habilitaCampos('Catalogo');" );
    $obRdTipoItemC->setChecked( false );

    $obRdTipoItemD = new Radio;
    $obRdTipoItemD->setRotulo   ( "**Item do Almoxarifado" );
    $obRdTipoItemD->setName     ( "stTipoItemRadio" );
    $obRdTipoItemD->setId       ( "stTipoItemRadio2" );
    $obRdTipoItemD->setValue    ( "Descricao" );
    $obRdTipoItemD->setLabel    ( "Não" );
    $obRdTipoItemD->obEvento->setOnClick( "habilitaCampos('Descricao');" );
    $obRdTipoItemD->setChecked( true );

    $obHdnTipoItem = new Hidden;
    $obHdnTipoItem->setName ('stTipoItem');
    $obHdnTipoItem->setValue('Catalogo');

    $arRadios = array( $obRdTipoItemC, $obRdTipoItemD );

    include_once CAM_GP_ALM_COMPONENTES."IMontaItemUnidade.class.php";
    $obMontaItemUnidade = new IMontaItemUnidade($obForm);
    $obMontaItemUnidade->obIPopUpCatalogoItem->setRotulo("*Item");
    $obMontaItemUnidade->obIPopUpCatalogoItem->setNull(true);
    $obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->setId("inCodItem");
    $obMontaItemUnidade->obIPopUpCatalogoItem->obCampoCod->obEvento->setOnBlur("javascript: unidadeItem(this.value);");
    $obMontaItemUnidade->obIPopUpCatalogoItem->setId( 'stNomItemCatalogo' );
    $obMontaItemUnidade->obIPopUpCatalogoItem->setName( 'stNomItemCatalogo' );
    $obMontaItemUnidade->obSpnInformacoesItem->setStyle('visibility:hidden; display:none');

    // Define Objeto SimNao para emitir liquidacao
    $obSimNaoEmitirLiquidacao = new SimNao();
    $obSimNaoEmitirLiquidacao->setRotulo ('Liquidar este empenho após sua emissão');
    $obSimNaoEmitirLiquidacao->setTitle  ('Selecione liquidar este empenho após sua emissão.');
    $obSimNaoEmitirLiquidacao->setName   ('boEmitirLiquidacao');
    $obSimNaoEmitirLiquidacao->setNull   (true);
    $obSimNaoEmitirLiquidacao->setChecked($stLiquidacaoAutomatica);

    include_once CAM_GA_ADM_COMPONENTES.'IMontaAssinaturas.class.php';
    $obMontaAssinaturas = new IMontaAssinaturas(null, 'nota_empenho');
    $obMontaAssinaturas->definePapeisDisponiveis('nota_empenho');
    $obMontaAssinaturas->setOpcaoAssinaturas( false );

    $obContrato = new IPopUpContrato( $obForm );
    $obContrato->obHdnBoFornecedor->setValue(TRUE);
    $obContrato->obBuscaInner->obCampoCod->obEvento->setOnBlur("montaParametrosGET('validaContrato', 'inCodContrato,inCodEntidade,inCodFornecedor,stExercicioContrato');");
    $obContrato->obBuscaInner->setValoresBusca('', '', '');
    $obContrato->obBuscaInner->setFuncaoBusca("montaParametrosGET('montaBuscaContrato', 'inCodContrato,inCodEntidade,inCodFornecedor,stExercicioContrato');".$obContrato->obBuscaInner->getFuncaoBusca());

    //****************************************//
    // Monta FORMULARIO
    //****************************************//
    $obFormulario = new Formulario;
    $obFormulario->addForm  ($obForm);
    $obFormulario->addTitulo('Insira os Dados do Empenho');

    $obFormulario->addHidden($obHdnCtrl);
    $obFormulario->addHidden($obHdnUltimaDataEmpenho);
    $obFormulario->addHidden($obHdnAcao);
    $obFormulario->addHidden($obHdnUnidadeOrcamento);
    $obFormulario->addHidden($obHdnVlReserva);
    $obFormulario->addHidden($obHdnOrgaoOrcamento);
    $obFormulario->addHidden($obHdnUnidadeOrcamento);
    $obFormulario->addHidden($obHdnUnidadePadrao);
    $obFormulario->addHidden($obHdnCodDespesa);
    $obFormulario->addHidden($obHdnNumItem);
    $obFormulario->addHidden($obHdnValidaFornecedor);
    $obFormulario->addHidden($obHdnAtributos );
    $obFormulario->addHidden($obHdnDtContrato);

    $obFormulario->addComponenteComposto($obTxtCodEntidade, $obCmbNomEntidade);
    $obFormulario->addComponente($obDtEmpenho);
    $obFormulario->addComponente($obIPopUpDotacao);
    $obFormulario->addComponente($obCmbClassificacao);
    $obFormulario->addSpan($obSpanSaldo);
    $obFormulario->addComponente($obLblOrgaoOrcamento);
    $obFormulario->addComponente($obLblUnidadeOrcamento);
    $obFormulario->addComponente($obBscFornecedor);

    $obFormulario->addComponente($obCmbCategoriaEmpenho);

    $obFormulario->addSpan($obSpanContrapartida);
    $obFormulario->addComponente($obTxtDescricao);
    $obFormulario->addComponenteComposto($obTxtCodTipo, $obCmbNomTipo);

    $obFormulario->addComponente($obDtValidadeFinal);
    $obFormulario->addComponente($obCmbHistorico);

    if ($inCodUF == 20) {
        $obFormulario->addComponente($obCmbFundeb);
        $obFormulario->addComponente($obCmbRoyalties);
    }

    if ($inCodUF == 9 && Sessao::getExercicio() >= 2012) {
        //informações sobre a licitação
        $obFormulario->addComponente($obTxtProcessoLicitacao);
        $obFormulario->addComponente($obTxtExercicioLicitacao);
        $obFormulario->addComponente($obTxtProcessoAdministrativo);

        $obFormulario->addTitulo('Modalidade TCMGO');
        $obFormulario->addComponente($obCmbModalidadeLicitacao);
        $obFormulario->addSpan($obSpanFundamentacaoLegal);
    }

    $obMontaAtributos->geraFormulario($obFormulario);

    $obFormulario->addTitulo('Contrato');
    $obContrato->geraFormulario($obFormulario);

    $obFormulario->addTitulo('Insira os Ítens do Empenho');
    $obFormulario->addHidden($obHdnTipoItem);
    $obFormulario->agrupaComponentes($arRadios);
    $obMontaItemUnidade->geraFormulario($obFormulario);
    $obFormulario->addComponente($obTxtNomItem);
    $obFormulario->addComponente($obTxtComplemento);
    $obFormulario->addComponente($obMarca);
    $obFormulario->addComponente($obTxtQuantidade);
    $obFormulario->addComponente($obCmbUnidade);
    $obFormulario->addComponente($obTxtVlUnitario);
    $obFormulario->addComponente($obTxtVlTotal);
    $obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
    $obFormulario->addSpan($obSpan);
    $obFormulario->addComponente($obLblVlTotal);

    $obFormulario->addComponente($obSimNaoEmitirLiquidacao);

    $obMontaAssinaturas->geraFormulario($obFormulario);

    $obOk = new Ok(true);
    $obLimpar = new Limpar();
    $obLimpar->obEvento->setOnClick('limparOrdem();');
    $obFormulario->defineBarra(array($obOk, $obLimpar));

    $obFormulario->show();
}

echo ("<script>habilitaCampos('Descricao')</script>");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>