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
    * Página de Consulta de Suplementação
    * Data de Criação: 02/06/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * $Id: FMConsultarOrdemPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.05
 *                , uc-02.03.28
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_EMP_NEGOCIO.'REmpenhoOrdemPagamento.class.php';
require_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoOrdemPagamento.class.php';
require_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoRecurso.class.php';
require_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";

//Define o nome dos arquivos PHP
$stPrograma = 'ConsultarOrdemPagamento';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJS   = 'JS'.$stPrograma.'.js';

require $pgJS;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if (empty($stAcao)) {
    $stAcao = 'incluir';
}

$stFiltro = '';
$arFiltro = Sessao::read('filtro');
if ($arFiltro) {
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$obREmpenhoOrdemPagamento->setExercicio                           ($_GET['stExercicio']);
$obREmpenhoOrdemPagamento->setCodigoOrdem                         ($_GET['inCodOrdem']);
$obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_GET['inCodEntidade']);
$obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio        ($_GET['stExercicioEmpenho']);
$obREmpenhoOrdemPagamento->consultar();

//RECUPERA VALORES DA ORDEM DE PAGAMENTO
$inCount              = 0;
$stOrdemPagamento     = $obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$obREmpenhoOrdemPagamento->getExercicio();
$stDtEmissao          = $obREmpenhoOrdemPagamento->getDataEmissao();
$stDtPagamento        = $obREmpenhoOrdemPagamento->getDataPagamento();
$stDtEstorno          = $obREmpenhoOrdemPagamento->getDataEstorno();
$stDtVencimento       = $obREmpenhoOrdemPagamento->getDataVencimento();
$stDtAnulacao         = $obREmpenhoOrdemPagamento->getDataAnulacao();
$stSituacao           = $obREmpenhoOrdemPagamento->getSituacao();
$stPagamentoEstornado = $obREmpenhoOrdemPagamento->getPagamentoEstornado();
$flValorNota          = $obREmpenhoOrdemPagamento->getValorNota();
$flValorNotaOriginal  = $obREmpenhoOrdemPagamento->getValorNotaOriginal();
$flValorNotaAnulacoes = $obREmpenhoOrdemPagamento->getValorNotaAnulacoes();
$flValorPagamento     = $obREmpenhoOrdemPagamento->getValorPagamento();
$stObservacao         = $obREmpenhoOrdemPagamento->getObservacao();

if ($obREmpenhoOrdemPagamento->getRetencao()) {
    $arRetencoes = $obREmpenhoOrdemPagamento->getRetencoes();
    foreach ($arRetencoes as $item) {
        $flValorRetencoes = bcadd($flValorRetencoes, $item['vl_retencao'], 2);
    }
}

switch ($stSituacao) {
case 'Paga':
    $stDtSituacao = $stDtPagamento;
    $stEstorno    = 'Não';
    $stDtEstorno  = '';
    break;
case 'A Pagar':
    if ($stDtEstorno) {
        $stEstorno = 'Sim';
        $stDtSituacao = $stDtEstorno;
    } else {
        $stEstorno    = 'Não';
        $stDtSituacao = '';
    }
    break;
case 'Anulada':
    $stDtSituacao = $stDtAnulacao;
    $stEstorno    = 'Não';
    $stDtEstorno  = '';
    break;
case 'Estornada':
    $stSituacao   = 'À Pagar';
    $stDtSituacao = $stDtEstorno;
    $stEstorno    = 'Sim';
}

$flValorNota = 0;
foreach ($obREmpenhoOrdemPagamento->getNotaLiquidacao() as $obNotaLiquidacao) {
    if ($inCount == 0) {
        $stEntidade       = $obNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCgm();
        $stDesdobramento  = $obNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
        $stDesdobramento .= " - ".$obNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
        $stOrgao          = $obNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
        $stOrgao         .= " - ".$obNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
        $stUnidade        = $obNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
        $stUnidade       .= " - ".$obNotaLiquidacao->roREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
        $stCredor         = $obNotaLiquidacao->roREmpenhoEmpenho->obRCGM->getNomCGM();
        $stDotacao        = $obNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getCodConta();
    }

    $arNota = Sessao::read('arNota');

    $arNota[$inCount]['nr_empenho'] = $obNotaLiquidacao->roREmpenhoEmpenho->getCodEmpenho();
    $arNota[$inCount]['dt_empenho'] = $obNotaLiquidacao->roREmpenhoEmpenho->getDtEmpenho();
    $arNota[$inCount]['nr_nota']    = $obNotaLiquidacao->getCodNota();
    $arNota[$inCount]['dt_nota']    = $obNotaLiquidacao->getDtNota();
    $arNota[$inCount++]['valor']    = $obNotaLiquidacao->getValorTotal();
    Sessao::write('arNota', $arNota);
}

$flValorNota = $obREmpenhoOrdemPagamento->getValorNota();
SistemaLegado::executaFramePrincipal("buscaDado('montaListaLiquidacoes');");
$jsOnload = " executaFuncaoAjax('montaListaContas','&stExercicio=".$_GET['stExercicio']."&inCodOrdem=".$_GET['inCodOrdem']."&stExercicioEmpenho=".$_GET['stExercicioEmpenho']."&stNotas=".$stNotas."&inCodEntidade=".$_GET['inCodEntidade']."'); ";

// Realiza a pesquisa referente a listagem dos cheques da Ordem de Pagamento
$obTEmpenhoOrdemPagamento = new TEmpenhoOrdemPagamento;
$obTEmpenhoOrdemPagamento->setDado('cod_ordem', $_GET['inCodOrdem']);
$obTEmpenhoOrdemPagamento->setDado('exercicio', $_GET['stExercicio']);
$obTEmpenhoOrdemPagamento->setDado('cod_entidade', $_GET['inCodEntidade']);
$obTEmpenhoOrdemPagamento->recuperaListaChequesOrdemPagamento($rsCheques);

/*
 * ***************************************/
//Define COMPONENTES DO FORMULARIO
/****************************************/

//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgList);
$obForm->setTarget('telaPrincipal');

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

// Define objeto Label para Ordem de Pagamento
$obLblOrdem = new Label;
$obLblOrdem->setRotulo('Ordem de Pagamento');
$obLblOrdem->setValue ($stOrdemPagamento);

//Define objeto label para Data de Emissao
$obLblDtEmissao = new Label;
$obLblDtEmissao->setRotulo('Data de Emissão');
$obLblDtEmissao->setValue ($stDtEmissao);

//Define objeto label para Tipo de Suplementação
$obLblEntidade = new Label;
$obLblEntidade->setRotulo('Entidade');
$obLblEntidade->setValue ($stEntidade);

//Define objeto label para Orgao
$obLblOrgao = new Label;
$obLblOrgao->setRotulo('Orgão Orcamentário');
$obLblOrgao->setValue ($stOrgao);

//Define objeto label para Unidade Orcamentaria
$obLblUnidade = new Label;
$obLblUnidade->setRotulo('Unidade Orçamentária');
$obLblUnidade->setValue ($stUnidade);

//Define objeto label para Dotação Orçamentaria
$obLblDotacao = new Label;
$obLblDotacao->setRotulo('Dotação Orçamentária');
$obLblDotacao->setValue ($stDotacao);

//Define objeto label para Desdobramento
$obLblDesdobramento = new Label;
$obLblDesdobramento->setRotulo('Desdobramento');
$obLblDesdobramento->setValue ($stDesdobramento);

$obTOrcamentoRecurso = new TOrcamentoRecurso;
$inCodRecurso = $obNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();

if ($inCodRecurso != null) {
    $stFiltroQuery .= ' WHERE cod_recurso = '.$inCodRecurso;
}

if (Sessao::getExercicio()) {
    $stFiltroQuery .=  " AND exercicio = '".Sessao::getExercicio()."' ";
}

$obErro = $obTOrcamentoRecurso->recuperaTodos($rsLista, $stFiltroQuery);

if (!$obErro->ocorreu()) {
    $stNomRecurso = $rsLista->getCampo('nom_recurso');
}
$stRecurso = $inCodRecurso.' - '.$stNomRecurso;

//Define objeto label para Recurso
$obLblRecurso = new Label;
$obLblRecurso->setRotulo('Recurso');
$obLblRecurso->setValue ($stRecurso);

//Define objeto label para Credor
$obLblCredor = new Label;
$obLblCredor->setRotulo('Credor');
$obLblCredor->setValue ($stCredor);

//Define objeto label para Observacao
$obLblObservacao = new Label;
$obLblObservacao->setRotulo('Descrição da Ordem');
$obLblObservacao->setValue ($stObservacao);

//Define objeto label para Valor Original da Ordem de Pagamento | antes das anulações
$obLblValorOriginal = new Label;
$obLblValorOriginal->setRotulo('Valor Original da OP');
$obLblValorOriginal->setValue (number_format($flValorNotaOriginal, 2, ',', '.'));

//Define objeto label para Valor das Anulações da Ordem de Pagamento
$obLblValorAnulacoes = new Label;
$obLblValorAnulacoes->setRotulo('Total de Anulações');
$obLblValorAnulacoes->setValue (number_format($flValorNotaAnulacoes, 2, ',','.'));

//Define objeto label para Valor da Ordem de Pagamento
$obLblValor = new Label;
$obLblValor->setRotulo('Valor da OP');
$obLblValor->setValue ($flValorNota);

$obLblValorRetencoes = new Label;
$obLblValorRetencoes->setRotulo('Total de Retenções');
$obLblValorRetencoes->setValue (number_format($flValorRetencoes, 2, ',', '.'));

$obLblValorLiquido = new Label;
$obLblValorLiquido->setRotulo('Valor Líquido da OP');
$obLblValorLiquido->setValue (number_format(bcsub(str_replace(',', '.', str_replace('.', '', $flValorNota)), $flValorRetencoes, 2), 2, ',', '.'));

//Define objeto label para Situação
$obLblSituacao = new Label;
$obLblSituacao->setRotulo('Situação');
$obLblSituacao->setValue ($stSituacao);

//Define objeto label para Data da Situação
$obLblDtSituacao = new Label;
$obLblDtSituacao->setRotulo('Data da Situação');
$obLblDtSituacao->setValue ($stDtSituacao);

//Define objeto label para Pagamento Estornado
$obLblEstorno = new Label;
$obLblEstorno->setRotulo('Pagamento Estornado');
$obLblEstorno->setValue ($stPagamentoEstornado);

// Define Objeto Label para Vencimento
$obLblDtVencimento = new Label;
$obLblDtVencimento->setRotulo('Data de Vencimento');
$obLblDtVencimento->setId    ('stDtVencimento');
$obLblDtVencimento->setValue ($stDtVencimento);

if ($arRetencoes) {
    $inCountExt = 0;
    $inCountOrc = 0;
    $stListaExt = '';
    $stListaOrc = '';
    foreach ($arRetencoes as $item) {
        if ($item['tipo'] == 'O') {
            $arTmpRetOrc[$inCountOrc] = $item;
            $inCountOrc++;
        } elseif ($item['tipo'] == 'E') {
            $arTmpRetExt[$inCountExt] = $item;
            $inCountExt++;
        }
    }

    if (isset($arTmpRetOrc)) {
        $rsRecordSetOrc = new RecordSet;
        $rsRecordSetOrc->preenche($arTmpRetOrc);
        $rsRecordSetOrc->addFormatacao('vl_retencao','NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setTitulo('Retenções Orçamentárias');
        $obLista->setRecordSet($rsRecordSetOrc);
        $obLista->setMostraPaginacao(false);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(3);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Conta de Retenção');
        $obLista->ultimoCabecalho->setWidth(65);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor da Retenção');
        $obLista->ultimoCabecalho->setWidth(20);
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('[cod_receita] - [nom_conta_receita]');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('[vl_retencao]');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaOrc = $obLista->getHTML();
    }

    if (isset($arTmpRetExt)) {
        $rsRecordSetExt = new RecordSet;
        $rsRecordSetExt->preenche($arTmpRetExt);
        $rsRecordSetExt->addFormatacao('vl_retencao','NUMERIC_BR');

        $obLista = new Lista;
        $obLista->setTitulo('Retenções Extra-Orçamentárias');
        $obLista->setRecordSet($rsRecordSetExt);
        $obLista->setMostraPaginacao(false);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(3);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Conta de Retenção');
        $obLista->ultimoCabecalho->setWidth(65);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor da Retenção');
        $obLista->ultimoCabecalho->setWidth(20);
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('[cod_plano] - [nom_conta]');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('[vl_retencao]');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaExt = $obLista->getHTML();
    }

    $obSpnRetencoes = new Span;
    $obSpnRetencoes->setId('spnRet');
    $obSpnRetencoes->setValue($stListaOrc.$stListaExt);
}

$obSpnListaCheques = new Span;
$obSpnListaCheques->setId('spnCheques');

$stListaCheque = '';
if ($rsCheques->getNumLinhas() > 0) {
    $obLista = new Lista;
        $obLista->setTitulo('Cheques');
        $obLista->setRecordSet($rsCheques);
        $obLista->setMostraPaginacao(false);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(3);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Agência');
        $obLista->ultimoCabecalho->setWidth(20);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Banco');
        $obLista->ultimoCabecalho->setWidth(25);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Conta Corrente');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Cheque');
        $obLista->ultimoCabecalho->setWidth(8);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Data Emissão');
        $obLista->ultimoCabecalho->setWidth(8);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor');
        $obLista->ultimoCabecalho->setWidth(8);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Status');
        $obLista->ultimoCabecalho->setWidth(8);
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('agencia');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('banco');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('conta_corrente');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('num_cheque');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('data_emissao');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('valor');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('status');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();
        $obLista->montaInnerHTML();
        $stListaCheque = $obLista->getHTML();
}
$obSpnListaCheques->setValue($stListaCheque);

$obSpnListaContas = new Span;
$obSpnListaContas->setId('spnListaContas');

$obSpnListaRegistros = new Span;
$obSpnListaRegistros->setId('spnListaRegistros');

$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnAcao);

$obFormulario->addTitulo    ('Dados da Ordem de Pagamento');
$obFormulario->addComponente($obLblOrdem);
$obFormulario->addComponente($obLblDtEmissao);
$obFormulario->addComponente($obLblEntidade);
$obFormulario->addComponente($obLblOrgao);
$obFormulario->addComponente($obLblUnidade);
$obFormulario->addComponente($obLblDotacao);
$obFormulario->addComponente($obLblDesdobramento);
$obFormulario->addComponente($obLblRecurso);
$obFormulario->addComponente($obLblCredor);
$obFormulario->addComponente($obLblObservacao);

$obFormulario->addTitulo    ('Valores');
$obFormulario->addComponente($obLblValorOriginal);
$obFormulario->addComponente($obLblValorAnulacoes);
$obFormulario->addComponente($obLblValor);
if ($arRetencoes) {
    $obFormulario->addComponente($obLblValorRetencoes);
    $obFormulario->addComponente($obLblValorLiquido);
}
$obFormulario->addComponente($obLblSituacao);
$obFormulario->addComponente($obLblDtSituacao);
$obFormulario->addComponente($obLblEstorno);
$obFormulario->addComponente($obLblDtVencimento);

if ($arRetencoes) {
    $obFormulario->addSpan($obSpnRetencoes);
}
$obFormulario->addSpan($obSpnListaContas);
$obFormulario->addSpan($obSpnListaRegistros);
$obFormulario->addSpan($obSpnListaCheques);

$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setCodDocumento           ($obREmpenhoOrdemPagamento->getCodigoOrdem());
$obMontaAssinaturas->setCodEntidade            ($obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade());
$obMontaAssinaturas->setExercicio              ($obREmpenhoOrdemPagamento->getExercicio());
$obMontaAssinaturas->geraListaLeituraFormulario($obFormulario, 'ordem_pagamento');

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obButtonVoltar = new Button;
$obButtonVoltar->setName ('Voltar');
$obButtonVoltar->setValue('Voltar');
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario->defineBarra(array($obButtonVoltar), 'left', '');
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
