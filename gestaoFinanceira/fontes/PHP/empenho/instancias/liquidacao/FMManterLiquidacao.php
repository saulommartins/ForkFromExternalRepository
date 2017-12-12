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
    * Página de Formulario de Inclusao/Alteracao de Empenho
    * Data de Criação   : 06/12/2004

    * @author Analista Jorge B. Ribarr
    * @author Desenvolvedor Anderson R. M. Buzo

    * @ignore

    $Id: FMManterLiquidacao.php 65471 2016-05-24 18:58:44Z michel $

    * Casos de uso: uc-02.03.04, uc-02.03.05
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
include_once CAM_GT_ARR_NEGOCIO.'RARRCarne.class.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write('stTituloPagina', 'Gestão Financeira | Empenho | Liquidação | Liquidar Empenho');

if ( $request->get('pgProxEmpenho') )
    $pgProx = CAM_GF_EMP_INSTANCIAS."empenho/".$request->get('pgProxEmpenho');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

$obREmpenhoConfiguracao       = new REmpenhoConfiguracao;
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
$obREmpenhoNotaLiquidacao     = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho );

$obREmpenhoConfiguracao->consultar();
$boDataVencimento = $obREmpenhoConfiguracao->getDataVencimento();
$boOPAutomatica   = $obREmpenhoConfiguracao->getOPAutomatica();

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $request->get('dtExercicioEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( $request->get('dtExercicioEmpenho')  );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarUnidadeMedida( $rsUnidade );

$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho( $request->get('inCodEmpenho') );
$obREmpenhoNotaLiquidacao->setExercicio( Sessao::getExercicio() );
$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( $request->get('dtExercicioEmpenho') );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

$arItensReserva = Sessao::read('arItens');
Sessao::remove('arItens');

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $request->get('inCodEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );
if ( $request->get('boImplantado') == 't' ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarRestosAPagar();
} else {
    $obREmpenhoEmpenhoAutorizacao->consultar();
}
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarValorItem();

$boAdiantamento = false;
$inCodCategoria = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodCategoria();
if ($inCodCategoria == '2' || $inCodCategoria == '3') {
    $boAdiantamento = true;
}

$stNomEmpenho  = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDescricao();
$stNomEntidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
$inNumUnidade  = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade  = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$inCodDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stNomDespesa       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
$stCodClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
$stNomClassificacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();
$inCodFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->getNumCGM();
$stNomFornecedor    = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRCGM->getnomCGM();
$stDtVencimento     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtVencimento();
$inCodHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getCodHistorico();
$stNomHistorico     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->getNomHistorico();
$stDtEmpenho        = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDtEmpenho();
$arItemPreEmpenho   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getItemPreEmpenho();

$stConfiguracaoUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());

$stCodClassIncorporacao = substr(str_replace(".", "", $stCodClassificacao), 0, 6); // XXXXXX
if (!Sessao::getExercicio() > '2012') {
    $arIncorpPatrimonial = array("449051", "449052", "449061", "449092", "459061", "459161");
    $arAmortizacao = array("4690");
} else {
    $arIncorpPatrimonial = array();
    $arAmortizacao = array();
}

$boCriaContasDebCred = false;
if (in_array(trim($stCodClassIncorporacao), $arIncorpPatrimonial) or in_array(trim(substr($stCodClassIncorporacao, 0, 4)), $arAmortizacao)) {
    $boCriaContasDebCred = true;
    $inCodContaCredPatrimon = null;
    $stNumContaCredPatrimon = "";
    $inCodContaDebPatrimon = null;
    $stNumContaDebPatrimon = "";
}

$arItens = array();
$nuVTotal = 0;

foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $arItens[$inCount]['num_item']     = $obItemPreEmpenho->getNumItem();
    $arItens[$inCount]['nom_item']     = $obItemPreEmpenho->getNomItem();
    $arItens[$inCount]['complemento']  = $obItemPreEmpenho->getComplemento();
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
    $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
    $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal() - $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $arItens[$inCount]['vl_unitario']  = bcdiv( $obItemPreEmpenho->getValorTotal(), $obItemPreEmpenho->getQuantidade(),2 );
    $arItens[$inCount]['vl_empenhado_anulado']  = $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $arItens[$inCount]['vl_liquidado']          = $obItemPreEmpenho->getValorLiquidado();
    $arItens[$inCount]['vl_liquidado_anulado']  = $obItemPreEmpenho->getValorLiquidadoAnulado();
    $arItens[$inCount]['vl_liquidado_real']     = bcsub( $obItemPreEmpenho->getValorLiquidado(), $obItemPreEmpenho->getValorLiquidadoAnulado() ,2);

    $nuValorEmpenhadoReal = bcsub( $obItemPreEmpenho->getValorTotal(), $obItemPreEmpenho->getValorEmpenhadoAnulado() ,2);
    $nuValorLiquidadoReal = bcsub( $obItemPreEmpenho->getValorLiquidado(), $obItemPreEmpenho->getValorLiquidadoAnulado() ,2);
    $nuValorALiquidar     = bcsub( $nuValorEmpenhadoReal, $nuValorLiquidadoReal,2 );
    $arItens[$inCount]['vl_a_liquidar'] = $nuValorALiquidar;
    $nuVTotal = $nuVTotal + $obItemPreEmpenho->getValorTotal();
}

if (count($arItens) > 0) {
    Sessao::write('FiltroItens', $arItens);
} else {
    Sessao::write('FiltroItens', $arItensReserva);
}

$obTOrcamentoRecurso = new TOrcamentoRecurso;

$inCodRecurso = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();

$stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' ";

if ($inCodRecurso != '')
    $stFiltro .= " AND cod_recurso = ".$inCodRecurso;

$obErro = $obTOrcamentoRecurso->recuperaTodos( $rsLista, $stFiltro);

if ( !$obErro->ocorreu() )
    $stNomRecurso = $rsLista->getCampo( 'nom_recurso' );

$arChaveAtributo = array( "cod_pre_empenho" => $request->get("inCodPreEmpenho"),
                          "exercicio"       => $request->get('dtExercicioEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

// Atributos Dinamicos - LIQUIDACAO
$obREmpenhoNotaLiquidacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosLiquidacao );

$arAtributos = $rsAtributos->getElementos();
$arAtributosLiquidacao  = $rsAtributosLiquidacao->getElementos();

$inExercicio = Sessao::read('exercicio');

$arTmp = array();
$arAtributosLiquidacao = array();

if ($inExercicio > 2008) {
    while (!$rsAtributosLiquidacao->eof()) {
        if ($rsAtributosLiquidacao->getCampo('nom_atributo') != 'TipoCredor') {
            $arTmp['cod_cadastro']      = $rsAtributosLiquidacao->getCampo('cod_cadastro');
            $arTmp['cod_atributo']      = $rsAtributosLiquidacao->getCampo('cod_atributo');
            $arTmp['ativo']             = $rsAtributosLiquidacao->getCampo('ativo');
            $arTmp['nao_nulo']          = $rsAtributosLiquidacao->getCampo('nao_nulo');
            $arTmp['nom_atributo']      = $rsAtributosLiquidacao->getCampo('nom_atributo');
            $arTmp['valor_padrao']      = $rsAtributosLiquidacao->getCampo('valor_padrao');
            $arTmp['valor_padrao_desc'] = $rsAtributosLiquidacao->getCampo('valor_padrao_desc');
            $arTmp['valor_desc']        = $rsAtributosLiquidacao->getCampo('valor_desc');
            $arTmp['ajuda']             = $rsAtributosLiquidacao->getCampo('ajuda');
            $arTmp['mascara']           = $rsAtributosLiquidacao->getCampo('mascara');
            $arTmp['nom_tipo']          = $rsAtributosLiquidacao->getCampo('nom_tipo');
            $arTmp['cod_tipo']          = $rsAtributosLiquidacao->getCampo('cod_tipo');
            $arAtributosLiquidacao[] = $arTmp;
        }
        $rsAtributosLiquidacao->proximo();
    }
} else
    $arAtributosLiquidacao = $rsAtributosLiquidacao->getElementos();

$inCount=0;
while ( $inCount < count($arAtributos) ) {
    $stValorAtributo = $arAtributos[$inCount]["valor"];
    if ($stValorAtributo) {
        $stNomeAtributo = $arAtributos[$inCount]["nom_atributo"];
        $inCount2=0;
        while ( $inCount2 < count($arAtributosLiquidacao)) {
            if ($stNomeAtributo == $arAtributosLiquidacao[$inCount2]["nom_atributo"]) {
                $arAtributosLiquidacao[$inCount2]["valor"] = $stValorAtributo;
            }
            $inCount2++;
        }
    }
    $inCount++;
}

$rsAtributosLiquidacao->preenche($arAtributosLiquidacao);

$obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setDtEmpenho($stDtEmpenho);
$obREmpenhoNotaLiquidacao->listarMaiorData($rsMaiorData);
$obREmpenhoNotaLiquidacao->listarMaiorDataAnulacaoEmpenho($rsMaiorDataAnulacao);

if ($rsMaiorDataAnulacao->getCampo('dataanulacao') != '') {
    if(SistemaLegado::comparaDatas($rsMaiorDataAnulacao->getCampo('dataanulacao'), $rsMaiorData->getCampo('data_liquidacao')))
        $stDtLiquidacao = $rsMaiorDataAnulacao->getCampo('dataanulacao');
    else
        $stDtLiquidacao = $rsMaiorData->getCampo('data_liquidacao');

} elseif ($rsMaiorData)
    $stDtLiquidacao = $rsMaiorData->getCampo('data_liquidacao');

if ($request->get('boImplantado') != 't') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDotacao );
    $stDespesa = $inCodDespesa.' - '.$stNomDespesa;
    $stOrgao   = $inNumOrgao.' - '.$stNomOrgao;
    $stUnidade = $inNumUnidade.' - '.$stNomUnidade;
} else {
    $stDespesa = $inCodDespesa;
    $stOrgao   = $inNumOrgao;
    $stUnidade = $inNumUnidade;
}
$nuSaldoDotacao = number_format( $nuSaldoDotacao, 2, ',', '.');

if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = "";
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            $stFiltro .= "&".$stCampo."=".urlencode( implode(',', $stValor) );
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

Sessao::remove('assinaturas');

$obMontaAssinaturas = new IMontaAssinaturas(null, 'nota_liquidacao');
$obMontaAssinaturas->definePapeisDisponiveis('nota_liquidacao');
$obMontaAssinaturas->setOpcaoAssinaturas( false );

$inCodEmpenho    = $request->get('inCodEmpenho');
$inCodPreEmpenho = $request->get('inCodPreEmpenho');
$inCodEntidade   = $request->get('inCodEntidade');

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

// Define objeto Hidden para Codigo da Empenho
$obHdnCodEmpenho = new Hidden;
$obHdnCodEmpenho->setName ( "inCodEmpenho" );
$obHdnCodEmpenho->setValue( $inCodEmpenho );

// Define objeto Hidden para boAdiatamento
$obHdnAdiantamento = new Hidden;
$obHdnAdiantamento->setName ( "boAdiantamento" );
$obHdnAdiantamento->setValue( $boAdiantamento  );

// Define objeto Hidden para Codigo da Empenho
$obHdnDtEmpenho = new Hidden;
$obHdnDtEmpenho->setName ( "stDtEmpenho" );
$obHdnDtEmpenho->setValue( $stDtEmpenho );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodPreEmpenho = new Hidden;
$obHdnCodPreEmpenho->setName ( "inCodPreEmpenho" );
$obHdnCodPreEmpenho->setValue( $inCodPreEmpenho );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setId ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );

// Define objeto Hidden para Exercicio Empenho
$obHdnExercicioEmpenho = new Hidden;
$obHdnExercicioEmpenho->setName ( "dtExercicioEmpenho" );
$obHdnExercicioEmpenho->setValue( $request->get("dtExercicioEmpenho") );

// Define Objeto Hidden para Codigo do Orgão Orçamentário
$obHdnNumOrgao = new Hidden;
$obHdnNumOrgao->setName   ( "inNumOrgao" );
$obHdnNumOrgao->setValue  ( $inNumOrgao  );

// Define Objeto Hidden para Codigo da Despesa
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName   ( "inCodDespesa" );
$obHdnCodDespesa->setValue  ( $inCodDespesa  );

// Define Objeto Hidden para codigo estrutural do elemento despesa
$obHdnDesdobramento = new Hidden;
$obHdnDesdobramento->setName   ( "inCodContaContabilFinanc" );
$obHdnDesdobramento->setValue  ( $stCodClassificacao );

// Define Objeto Hidden para codigo estrutural do elemento despesa
$obHdnCriaContasCredDeb = new Hidden;
$obHdnCriaContasCredDeb->setName ("boCriaContasDebCred");
$obHdnCriaContasCredDeb->setId   ("boCriaContasDebCred");
$obHdnCriaContasCredDeb->setValue($boCriaContasDebCred );

// Verifica se o credor possui débitos com a Prefeitura
$obRARRCarne = new RARRCarne;
$obRARRCarne->obRARRParcela->roRARRLancamento->obRCgm->setNumCgm($inCodFornecedor);

$obRARRCarne->listarCarneConsulta( $rsLista );
$boExisteDebitoContribuinte = false;
while (!$rsLista->eof()) {
    $stSituacaoLancamento = $rsLista->getCampo('situacao_lancamento');
    if ($stSituacaoLancamento == 'Ativo') {
        $boExisteDebitoContribuinte = true;
    }
    $rsLista->proximo();
}

// Define Objeto Hidden para validação dos débitos do contribuinte
$obHdnDebitoContribuinte = new Hidden;
$obHdnDebitoContribuinte->setName ("boExisteDebitoContribuinte");
$obHdnDebitoContribuinte->setId   ("boExisteDebitoContribuinte");
$obHdnDebitoContribuinte->setValue  ($boExisteDebitoContribuinte);

if ( $request->get('stEmitirEmpenho') ) {
    // Define Objeto Hidden para informar que a ultima acao foi a inclusao de um empenho
    $obHdnEmitirEmpenho = new Hidden;
    $obHdnEmitirEmpenho->setName   ( "stEmitirEmpenho" );
    $obHdnEmitirEmpenho->setValue  ( $request->get('stEmitirEmpenho') );

    // Define Objeto Hidden para setar a acao da pagina do empenho
    $stAcaoEmpenho = $request->get('stAcaoEmpenho');
    $obHdnAcaoEmp = new Hidden;
    $obHdnAcaoEmp->setName   ( "stAcaoEmpenho" );
    $obHdnAcaoEmp->setValue  ( $stAcaoEmpenho  );

    // Define Objeto Hidden para setar a pagina para direcionamento do empenho
    $obHdnPgProxEmpenho = new Hidden;
    $obHdnPgProxEmpenho->setName   ( "pgProxEmpenho" );
    $obHdnPgProxEmpenho->setValue  ( $pgProx         );

    $obHdnPgDespesasFixas = new Hidden;
    $obHdnPgDespesasFixas->setName ("pgDespesasFixas" );
    $obHdnPgDespesasFixas->setValue ( $request->get('pgDespesasFixas') );

    // Define Objeto Hidden para setar a acao do empenho
    $acaoEmpenho = ($request->get('acaoEmpenho')!='') ? $request->get('acaoEmpenho'): 822;
    $obHdnAcaoEmpenho = new Hidden;
    $obHdnAcaoEmpenho->setName   ( "acaoEmpenho" );
    $obHdnAcaoEmpenho->setValue  ( $acaoEmpenho  );

    // Define Objeto Hidden para setar o modulo do empenho
    $moduloEmpenho = $request->get('moduloEmpenho');
    $obHdnModuloEmpenho = new Hidden;
    $obHdnModuloEmpenho->setName   ( "moduloEmpenho" );
    $obHdnModuloEmpenho->setValue  ( $moduloEmpenho  );

    // Define Objeto Hidden para setar a funcionalidade do empenho
    $funcionalidadeEmpenho = $request->get('funcionalidadeEmpenho');
    $obHdnFuncionalidadeEmpenho = new Hidden;
    $obHdnFuncionalidadeEmpenho->setName   ( "funcionalidadeEmpenho" );
    $obHdnFuncionalidadeEmpenho->setValue  ( $funcionalidadeEmpenho  );
}

// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "N° Empenho" );
$obLblEmpenho->setValue ( $inCodEmpenho );

// Define objeto Label para Entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade" );
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade );

//Define o objeto Label para Descrição do Empenho
$obLblNomEmpenho = new Label;
$obLblNomEmpenho->setId     ( "stNomEmpenho"         );
$obLblNomEmpenho->setRotulo ( "Descrição do Empenho" );
$obLblNomEmpenho->setValue  ( $stNomEmpenho          );

// Define Objeto Label para Tipo de Empenho
$obLblTipo = new Label;
$obLblTipo->setId     ( "inCodTipo"                 );
$obLblTipo->setValue  ( $inCodTipo.' - '.$stNomTipo );
$obLblTipo->setRotulo ( "Tipo de Empenho"           );

// Define Objeto Label para Despesa
$obLblDespesa = new Label;
$obLblDespesa->setRotulo ( "Dotação Orçamentária" );
$obLblDespesa->setId     ( "stNomDespesa" );
$obLblDespesa->setValue  ( $stDespesa );

// Define Objeto Label para Classificacao da Despesa
$obLblClassificacao = new Label;
$obLblClassificacao->setRotulo ( "Desdobramento" );
$obLblClassificacao->setId     ( "stNomClassificacao" );
$obLblClassificacao->setValue  ( $stCodClassificacao.' - '.$stNomClassificacao );

// Define Objeto Label para Recurso
$obLblRecurso = new Label;
$obLblRecurso->setRotulo ( "Recurso" );
$obLblRecurso->setId     ( "stNomRecurso" );
$obLblRecurso->setValue  ( substr($inCodRecurso,0,6).' - '.$stNomRecurso );

// Define Objeto Label para Saldo da Dotação
$obLblSaldoDotacao = new Label;
$obLblSaldoDotacao->setRotulo ( "Saldo Dotação" );
$obLblSaldoDotacao->setId     ( "nuSaldoDotacao" );
$obLblSaldoDotacao->setValue  ( $nuSaldoDotacao );

// Define Objeto Label para Codigo do Orgão Orçamentário
$obLblCodOrgao = new Label;
$obLblCodOrgao->setRotulo ( "Orgão Orçamentário" );
$obLblCodOrgao->setId     ( "inCodOrgao" );
$obLblCodOrgao->setValue  ( $stOrgao );

// Define Objeto Label para Codigo do Unidade Orçamentária
$obLblCodUnidade = new Label;
$obLblCodUnidade->setRotulo ( "Unidade Orçamentária" );
$obLblCodUnidade->setId     ( "inCodUnidade" );
$obLblCodUnidade->setValue  ( $stUnidade );

// Define Objeto Label para Fornecedor
$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo ( "Credor" );
$obLblFornecedor->setId     ( "stNomFornecedor" );
$obLblFornecedor->setValue  ( $inCodFornecedor.' - '.$stNomFornecedor  );

// Define Objeto Label para Vencimento
$obLblDtVencimento = new Label;
$obLblDtVencimento->setRotulo ( "Data de Vencimento" );
$obLblDtVencimento->setId     ( "stDtVencimento" );
$obLblDtVencimento->setName   ( "stDtVencimento" );
$obLblDtVencimento->setValue  ( $stDtVencimento  );

// Define Objeto Label para Histórico
$obLblHistorico = new Label;
$obLblHistorico->setRotulo    ( "Histórico"      );
$obLblHistorico->setId        ( "stNomHistorico" );
$obLblHistorico->setValue     ( $inCodHistorico.' - '.$stNomHistorico  );

// Atributos Dinamicos - EMPENHO
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true         );

$obMontaAtributosLiquidacao = new MontaAtributos;
$obMontaAtributosLiquidacao->setTitulo     ( "Atributos"            );
$obMontaAtributosLiquidacao->setName       ( "Atributo_Liq"         );
$obMontaAtributosLiquidacao->setRecordSet  ( $rsAtributosLiquidacao );
$obMontaAtributosLiquidacao->setSortLabel  ( true                   );

if ($boCriaContasDebCred) {
    // Define Objeto TextBox para Codigo do Historico Padrao
    $obBscHistorico = new BuscaInner;
    $obBscHistorico->setRotulo ( "Histórico" );
    $obBscHistorico->setTitle ( "Informe o histórico." );
    $obBscHistorico->setNulL ( true  );
    $obBscHistorico->setId ( "stNomHistoricoPatrimon" );
    $obBscHistorico->setValue( $stNomHistoricoPatrimon );
    $obBscHistorico->obCampoCod->setName ( "inCodHistoricoPatrimon" );
    $obBscHistorico->obCampoCod->setSize ( 10 );
    $obBscHistorico->obCampoCod->setMaxLength( 5 );
    $obBscHistorico->obCampoCod->setValue ( $inCodHistoricoPatrimon );
    $obBscHistorico->obCampoCod->setAlign ("left");
    $obBscHistorico->obCampoCod->obEvento->setOnBlur("buscaDado('buscaHistorico');");
    $obBscHistorico->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistoricoPatrimon','stNomHistoricoPatrimon','','".Sessao::getId()."','800','550');");

    // Define Objeto TextBox para Complemento
    $obTxtComplemento = new TextArea;
    $obTxtComplemento->setName   ( "stComplemento" );
    $obTxtComplemento->setId     ( "stComplemento" );
    $obTxtComplemento->setValue  ( $stComplemento  );
    $obTxtComplemento->setRotulo ( "Complemento" );
    $obTxtComplemento->setTitle  ( "Informe o complemento." );
    $obTxtComplemento->setNull   ( true );
    $obTxtComplemento->setRows   ( 3 );

    $stGrupoConta = (in_array($stCodClassIncorporacao, $arIncorpPatrimonial)) ? "_incorp" : "_amort";

    include_once CAM_GF_CONT_COMPONENTES."IPopUpContaAnalitica.class.php";

    $obBscContaDebito = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect );
    $obBscContaDebito->setID ( 'innerContaDebito' );
    $obBscContaDebito->setNulL ( true  );
    $obBscContaDebito->setName ( 'innerContaDebito' );
    $obBscContaDebito->obCampoCod->setName ( "inCodContaDebito" );
    $obBscContaDebito->setRotulo ( 'Conta Débito' );
    $obBscContaDebito->setTitle ( 'Informe a conta a Debitar para fins de Incorporação Patrimonial' );
    $obBscContaDebito->setTipoBusca ( 'emp_conta_debito' . $stGrupoConta );

    $obBscContaCredito = new IPopUpContaAnalitica ( $obEntidadeUsuario->obSelect );
    $obBscContaCredito->setID ( 'innerContaCredito' );
    $obBscContaCredito->setNulL ( true  );
    $obBscContaCredito->setName ( 'innerContaCredito' );
    $obBscContaCredito->obCampoCod->setName ( "inCodContaCredito" );
    $obBscContaCredito->setRotulo ( 'Conta Crédito' );
    $obBscContaCredito->setTitle ( 'Informe a conta a Creditar para fins de Incorporação Patrimonial' );
    $obBscContaCredito->setTipoBusca ( 'emp_conta_credito' . $stGrupoConta );
}

// Define Objeto Span Para lista de itens
$obSpan = new Span;
$obSpan->setId( "spnLista" );

$obSpanTipoDocumento = new Span;
$obSpanTipoDocumento->setId('spnTipoDocumento');

// Define Objeto Label para Valor Total  do Empenho
$obLblVlTotalEmp = new Label;
$obLblVlTotalEmp->setId( "nuValorTotalEmp" );
$obLblVlTotalEmp->setRotulo( "Total do Empenho" );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnVlTotalEmpenho = new Hidden;
$obHdnVlTotalEmpenho->setName ( "vlTotalEmpenho" );
$obHdnVlTotalEmpenho->setValue( $vlTotalEmpenho );

$obHdnVlTotalLiquidado = new Hidden;
$obHdnVlTotalLiquidado->setName ( "vlTotalLiquidado" );

$obHdnVlTotalLiquidadoAnulado = new Hidden;
$obHdnVlTotalLiquidadoAnulado->setName ( "vlTotalLiquidadoAnulado" );

// Define Objeto Label para Valor Total dos Itens
$obLblVlTotalSaldo = new Label;
$obLblVlTotalSaldo->setId( "nuValorTotalSaldo" );
$obLblVlTotalSaldo->setRotulo( "Saldo a Liquidar" );

// Define Objeto Label para o Saldo dos Itens do Empenho
$obLblVlTotal = new Label;
$obLblVlTotal->setId( "nuValorTotal" );
$obLblVlTotal->setRotulo( "Total a Liquidar" );

// Define Objeto Label para Valor Total Anulado
$obLblVlAnulado = new Label;
$obLblVlAnulado->setId( "nuVlAnulado" );
$obLblVlAnulado->setRotulo( "Valor Anulado" );

// Define Objeto Label para Valor Total Liquidado
$obLblVlLiquidado = new Label;
$obLblVlLiquidado->setId( "nuVlLiquidado" );
$obLblVlLiquidado->setRotulo( "Valor Liquidado" );

$obAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obAdministracaoConfiguracao->recuperaTodos($rsAdministracaoConfiguracao, " WHERE configuracao.parametro = 'seta_tipo_documento_liq_tceam' AND exercicio = '".Sessao::getExercicio()."'");
$boMostrarCombo = $rsAdministracaoConfiguracao->getCampo('valor');

// Verifica quais estados estão selecionados para saber quais dados incluir, se for necessário, nas combos de tipo de documento.

Sessao::write('inUf', $stConfiguracaoUf);

switch ($stConfiguracaoUf) {
    case '2':
        $obComboEstados = 'true';
        include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALTipoDocumento.class.php';
        $obTTCEALTipoDocumento = new TTCEALTipoDocumento;
        $obTTCEALTipoDocumento->recuperaTodos($rsTipoDocumento);
        Sessao::write('tipoEstado', 'AL');
    break;
    case '3':
        if ($boMostrarCombo == 'true') {
            $obComboEstados = 'true';
            include_once CAM_GPC_TCEAM_NEGOCIO.'RTCEAMTipoDocumento.class.php';
            $obRTCEAMTipoDocumento = new RTCEAMTipoDocumento;
            $obRTCEAMTipoDocumento->recuperaTipoDocumento($rsTipoDocumento);
            Sessao::write('tipoEstado', 'AM');
        }
    break;
    case '5':
        $obTxtNumeroNF = new TextBox;
        $obTxtNumeroNF->setName     ( 'stNumeroNF' );
        $obTxtNumeroNF->setiD       ( 'stNumeroNF' );
        $obTxtNumeroNF->setRotulo   ( 'Número da Nota Fiscal' );
        $obTxtNumeroNF->setTitle    ( 'Informe o Número da Nota Fiscal' );
        $obTxtNumeroNF->setSize     ( 10 );
        $obTxtNumeroNF->setMaxLength( 10 );
        $obTxtNumeroNF->setNull     ( true );

        $obExercicioNotaFiscal = new Exercicio();
        $obExercicioNotaFiscal->setRotulo        ( "Ano" );
        $obExercicioNotaFiscal->setTitle         ( "Ano. Ex.: 2015" );
        $obExercicioNotaFiscal->setName          ( "stAnoNotaFiscal" );
        $obExercicioNotaFiscal->setId            ( "stAnoNotaFiscal" );
        $obExercicioNotaFiscal->setInteiro       ( true );
        $obExercicioNotaFiscal->setSize          ( 3 );
        $obExercicioNotaFiscal->setMaxLength     ( 4 );
        $obExercicioNotaFiscal->setDefinicao     ( "exercicio" );
        $obExercicioNotaFiscal->setNull          ( true );
        $obExercicioNotaFiscal->setValue         ( Sessao::getExercicio() );

        $obTxtSerieNF = new TextBox;
        $obTxtSerieNF->setName      ( 'stSerieNF' );
        $obTxtSerieNF->setId        ( 'stSerieNF' );
        $obTxtSerieNF->setRotulo    ( 'Série da Nota Fiscal' );
        $obTxtSerieNF->setTitle     ( 'Informe a Série da Nota Fiscal' );
        $obTxtSerieNF->setSize      ( 2 );
        $obTxtSerieNF->setMaxLength ( 3 );
        $obTxtSerieNF->setNull      ( true );

        $obTxtSubSerieNF = new TextBox;
        $obTxtSubSerieNF->setName       ( 'stSubSerieNF' );
        $obTxtSubSerieNF->setId         ( 'stSubSerieNF' );
        $obTxtSubSerieNF->setRotulo     ( 'SubSérie da Nota Fiscal' );
        $obTxtSubSerieNF->setTitle      ( 'Informe a SubSérie da Nota Fiscal' );
        $obTxtSubSerieNF->setSize       ( 2 );
        $obTxtSubSerieNF->setMaxLength  ( 3 );
        $obTxtSubSerieNF->setNull       ( true );

        $obDtEmissaoNF = new Data;
        $obDtEmissaoNF->setName     ( 'stDtEmissaoNF' );
        $obDtEmissaoNF->setId       ( 'stDtEmissaoNF' );
        $obDtEmissaoNF->setRotulo   ( 'Data da Emissão da Nota Fiscal' );
        $obDtEmissaoNF->setTitle    ( 'Informe a Data da Emissão da Nota Fiscal' );
        $obDtEmissaoNF->setSize     ( 7 );
        $obDtEmissaoNF->setNull     ( true );
        $obDtEmissaoNF->obEvento->setOnBlur( "validaDataEmissaoNF();" );

        $obNuValorNota = new Numerico();
        $obNuValorNota->setName     ( "nuValorNotaFiscal" );
        $obNuValorNota->setId       ( "nuValorNotaFiscal" );
        $obNuValorNota->setRotulo   ( 'Valor da Nota Fiscal' );
        $obNuValorNota->setTitle    ( 'Informe o Valor da Nota Fiscal' );
        $obNuValorNota->setSize     ( 12 );
        $obNuValorNota->setMaxLength( 16 );
        $obNuValorNota->setValue    ( '0,00' );
        $obNuValorNota->setNegativo ( false );

        $obTxtObjetoNF = new TextBox;
        $obTxtObjetoNF->setName      ( 'stObjetoNF' );
        $obTxtObjetoNF->setId        ( 'stObjetoNF' );
        $obTxtObjetoNF->setRotulo    ( 'Descrição do objeto da Nota Fiscal' );
        $obTxtObjetoNF->setTitle     ( 'Informe a descricao do objeto da Nota Fiscal' );
        $obTxtObjetoNF->setSize      ( 130 );
        $obTxtObjetoNF->setMaxLength ( 150 );
        $obTxtObjetoNF->setNull      ( true );

        $obTxtUFDocumento = new TextBox;
        $obTxtUFDocumento->setName      ( 'stUFUnidadeFederacao' );
        $obTxtUFDocumento->setId        ( 'stUFUnidadeFederacao' );
        $obTxtUFDocumento->setRotulo    ( 'Unidade de Federação do documento' );
        $obTxtUFDocumento->setTitle     ( 'Informe a Unidade de Federação do documento' );
        $obTxtUFDocumento->setSize      ( 2 );
        $obTxtUFDocumento->setMaxLength ( 2 );
        $obTxtUFDocumento->setNull      ( true );
    break;
    case '11':
        $obHdnVlVlTotalDoctoFiscal = new Hidden;
        $obHdnVlVlTotalDoctoFiscal->setName  ( "nuTotalNf" );
        $obHdnVlVlTotalDoctoFiscal->setId    ( "nuTotalNf" );

        //Radio para definicao de Inclusão de Documento Fiscal
        $obRdIncluirNFS = new Radio;
        $obRdIncluirNFS->setRotulo  ( "*Incluir Documento Fiscal" );
        $obRdIncluirNFS->setName    ( "stIncluirNF"  );
        $obRdIncluirNFS->setId      ( "stIncluirNF1" );
        $obRdIncluirNFS->setValue   ( "Sim" );
        $obRdIncluirNFS->setLabel   ( "Sim" );
        $obRdIncluirNFS->obEvento->setOnClick( "montaParametrosGET('montaNF', 'stIncluirNF');" );
        $obRdIncluirNFS->setChecked ( false );

        $obRdIncluirNFN = new Radio;
        $obRdIncluirNFN->setRotulo  ( "*Incluir Documento Fiscal" );
        $obRdIncluirNFN->setName    ( "stIncluirNF"    );
        $obRdIncluirNFN->setId      ( "stIncluirNF2"   );
        $obRdIncluirNFN->setValue   ( "Não" );
        $obRdIncluirNFN->setLabel   ( "Não" );
        $obRdIncluirNFN->obEvento->setOnClick( "montaParametrosGET('montaNF', 'stIncluirNF');" );
        $obRdIncluirNFN->setChecked ( true );

        $arRadiosNF = array( $obRdIncluirNFS, $obRdIncluirNFN );

        $obSpnNF = new Span();
        $obSpnNF->setId( 'spnNF' );
    break;
    case '20':
        $obTxtNumeroNF = new TextBox;
        $obTxtNumeroNF->setName( 'stNumeroNF' );
        $obTxtNumeroNF->setRotulo( 'Número da NF' );
        $obTxtNumeroNF->setTitle( 'Informe o Número da NF' );
        $obTxtNumeroNF->setSize( 12 );
        $obTxtNumeroNF->setMaxLength( 12 );
        $obTxtNumeroNF->setNull( true );

        $obTxtSerieNF = new TextBox;
        $obTxtSerieNF->setName( 'stSerieNF' );
        $obTxtSerieNF->setRotulo( 'Série da NF' );
        $obTxtSerieNF->setTitle( 'Informe a Série da NF' );
        $obTxtSerieNF->setSize( 12 );
        $obTxtSerieNF->setMaxLength( 12 );
        $obTxtSerieNF->setNull( true );

        $obDtEmissaoNF = new Data;
        $obDtEmissaoNF->setName( 'stDtEmissaoNF' );
        $obDtEmissaoNF->setRotulo( 'Data da Emissão da NF' );
        $obDtEmissaoNF->setTitle( 'Informe a Data da Emissão da NF' );
        $obDtEmissaoNF->setNull( true );
        $obDtEmissaoNF->obEvento->setOnBlur( "validaDataEmissaoNF();" );

        $obTxtCodValidacaoNF = new TextBox;
        $obTxtCodValidacaoNF->setName( 'stCodValidacaoNF' );
        $obTxtCodValidacaoNF->setRotulo( 'Código de Validação da NF' );
        $obTxtCodValidacaoNF->setTitle( 'Informe o Código de Validação da NF' );
        $obTxtCodValidacaoNF->setSize( 50 );
        $obTxtCodValidacaoNF->setMaxLength( 50 );
        $obTxtCodValidacaoNF->setNull( true );

        $obTxtModeloNF = new TextBox;
        $obTxtModeloNF->setName( 'stModeloNF' );
        $obTxtModeloNF->setRotulo( 'Modelo da NF' );
        $obTxtModeloNF->setTitle( 'Informe o Modelo da NF' );
        $obTxtModeloNF->setSize( 3 );
        $obTxtModeloNF->setMaxLength( 3 );
        $obTxtModeloNF->setNull( true );
    break;
    case '16':
        $obComboEstados = 'true';
        include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPETipoDocumento.class.php';
        $obTTCEPETipoDocumento = new TTCEPETipoDocumento;
        $obTTCEPETipoDocumento->recuperaTodos($rsTipoDocumento);
        Sessao::write('tipoEstado', 'PE');
    break;
    case '27':
        $obComboEstados = 'true';
        include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOTipoDocumento.class.php';
        $obTTCETOTipoDocumento = new TTCETOTipoDocumento;
        $obTTCETOTipoDocumento->recuperaTodos($rsTipoDocumento);
        Sessao::write('tipoEstado', 'TO');
    break;
    case '23':
        $obHdnVlVlTotalDoctoFiscal = new Hidden;
        $obHdnVlVlTotalDoctoFiscal->setName  ( "nuTotalNf" );
        $obHdnVlVlTotalDoctoFiscal->setId    ( "nuTotalNf" );

        //Radio para definicao de Inclusão de Documento Fiscal
        $obRdIncluirNFS = new Radio;
        $obRdIncluirNFS->setRotulo  ( "*Incluir Documento Fiscal" );
        $obRdIncluirNFS->setName    ( "stIncluirNF"  );
        $obRdIncluirNFS->setId      ( "stIncluirNF1" );
        $obRdIncluirNFS->setValue   ( "Sim" );
        $obRdIncluirNFS->setLabel   ( "Sim" );
        $obRdIncluirNFS->obEvento->setOnClick( "montaParametrosGET('montaNF', 'stIncluirNF');" );
        $obRdIncluirNFS->setChecked ( false );

        $obRdIncluirNFN = new Radio;
        $obRdIncluirNFN->setRotulo  ( "*Incluir Documento Fiscal" );
        $obRdIncluirNFN->setName    ( "stIncluirNF"    );
        $obRdIncluirNFN->setId      ( "stIncluirNF2"   );
        $obRdIncluirNFN->setValue   ( "Não" );
        $obRdIncluirNFN->setLabel   ( "Não" );
        $obRdIncluirNFN->obEvento->setOnClick( "montaParametrosGET('montaNF', 'stIncluirNF');" );
        $obRdIncluirNFN->setChecked ( true );

        $arRadiosNF = array( $obRdIncluirNFS, $obRdIncluirNFN );

        $obSpnNF = new Span();
        $obSpnNF->setId( 'spnNF' );
    break;
}

// Prepara o combo do Tipo de Documento para os estados que devem possuir esse campo.
if ($obComboEstados == 'true') {
    $obTxtTipoDocumento = new TextBox;
    $obTxtTipoDocumento->setRotulo   ('Tipo de Documento');
    $obTxtTipoDocumento->setTitle    ('Informe o Tipo do Documento');
    $obTxtTipoDocumento->setName     ('inCodTipoDocumentoTxt');
    $obTxtTipoDocumento->setValue    ('');
    $obTxtTipoDocumento->setSize     (4);
    $obTxtTipoDocumento->setMaxLength(3);
    $obTxtTipoDocumento->setInteiro  (true);
    $obTxtTipoDocumento->setNull     (false);
    $obTxtTipoDocumento->obEvento->setOnChange("montaParametrosGET('alteraCamposTipoDocumento');");

    $obCboTipoDocumento = new Select;
    $obCboTipoDocumento->setName      ('inCodTipoDocumento');
    $obCboTipoDocumento->setId        ('inCodTipoDocumento');
    $obCboTipoDocumento->setTitle     ('Informe o Tipo do Documento.');
    $obCboTipoDocumento->setRotulo    ('Tipo de Documento');
    $obCboTipoDocumento->setCampoDesc ('descricao');
    $obCboTipoDocumento->setCampoId   ('cod_tipo');
    $obCboTipoDocumento->addOption    ('', 'Selecione');
    $obCboTipoDocumento->preencheCombo($rsTipoDocumento);
    $obCboTipoDocumento->setNull      (false);
    $obCboTipoDocumento->obEvento->setOnChange("montaParametrosGET('alteraCamposTipoDocumento');");
}

// Define objeto Label para Data do Empenho
$obLblDtEmpenho = new Label;
$obLblDtEmpenho->setId       ( "stDtEmpenho" );
$obLblDtEmpenho->setValue    ( $stDtEmpenho );
$obLblDtEmpenho->setRotulo   ( "Data de Emissão" );

//Define o objeto data para a Data validade final
$obDataValidadeFinal = new Data;
$obDataValidadeFinal->setName   ( "dtValidadeFinal" );
$obDataValidadeFinal->setRotulo ( "Data de Vencimento" );
$obDataValidadeFinal->setTitle  ( "Informe a data de vencimento." );
$obDataValidadeFinal->setNull   ( false );
$obDataValidadeFinal->obEvento->setOnBlur( "validaDataVencimento();" );
if($boDataVencimento=="true")
    $obDataValidadeFinal->setValue  ( "31/12/".Sessao::getExercicio() );
else
    $obDataValidadeFinal->setValue  ( "");

if (!$stDtLiquidacao) {
    $arDtEmpenho = explode('/',$stDtEmpenho);
    if ($arDtEmpenho[2] < date('Y')) {
        $stDtLiquidacao = date('d/m/Y');
    } else
        $stDtLiquidacao = $stDtEmpenho;
}

$obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
$obTAdministracaoConfiguracaoEntidade->setDado("exercicio"    , Sessao::getExercicio());
$obTAdministracaoConfiguracaoEntidade->setDado("cod_modulo"   , 10);
$obTAdministracaoConfiguracaoEntidade->setDado("cod_entidade" , $request->get('inCodEntidade'));
$obTAdministracaoConfiguracaoEntidade->setDado("parametro"    , "data_fixa_liquidacao");
$obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsConfiguracao);
$stDtFixaLiquidacao = trim($rsConfiguracao->getCampo('valor'));

// Define objeto Data para validade final
$obDtLiquidacao = new Data;
$obDtLiquidacao->setName     ( "stDtLiquidacao" );
$obDtLiquidacao->setRotulo   ( 'Data de Liquidação' );
$obDtLiquidacao->setTitle    ( 'Informe a data de liquidação.' );
$obDtLiquidacao->setNull     ( false );
$obDtLiquidacao->obEvento->setOnBlur( "validaDataLiquidacao();" );
if( $stDtFixaLiquidacao != ''){
    $obDtLiquidacao->setValue ( $stDtFixaLiquidacao );
    $obDtLiquidacao->setLabel ( TRUE );
}else
    $obDtLiquidacao->setValue ( $stDtLiquidacao );

// Define objeto TextArea para Motivo da Anulação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId       ( "stObservacao" );
$obTxtObservacao->setName     ( "stObservacao" );
$obTxtObservacao->setRotulo   ( "Observação"   );
$obTxtObservacao->setTitle    ( "Informe a observação." );
$obTxtObservacao->setCols     ( 100            );
$obTxtObservacao->setRows     ( 3              );
$obTxtObservacao->setMaxCaracteres( 640        );

$obIFrame = new IFrame;
$obIFrame->setName("telaListaItemPreEmpenho");
$obIFrame->setWidth("0");
$obIFrame->setHeight("0");
$obIFrame->show();

if($boOPAutomatica=="true")
    $stOPAutomatica = "SIM";
else
    $stOPAutomatica = "NAO";

// Define Objeto SimNao para emitir OP
$obSimNaoEmitirOP = new SimNao();
$obSimNaoEmitirOP->setRotulo ( "Emitir OP desta Liquidação após sua emissão" );
$obSimNaoEmitirOP->setName   ( 'boEmitirOP'              );
$obSimNaoEmitirOP->setNull   ( true                      );
$obSimNaoEmitirOP->setChecked( $stOPAutomatica           );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados do empenho" );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCodEmpenho     );
$obFormulario->addHidden( $obHdnAdiantamento   );
$obFormulario->addHidden( $obHdnDtEmpenho      );
$obFormulario->addHidden( $obHdnCodPreEmpenho  );
$obFormulario->addHidden( $obHdnCodEntidade    );
$obFormulario->addHidden( $obHdnExercicioEmpenho );
$obFormulario->addHidden( $obHdnNumOrgao );
$obFormulario->addHidden( $obHdnCodDespesa );
$obFormulario->addHidden( $obHdnDesdobramento );
$obFormulario->addHidden( $obHdnVlTotalEmpenho );
$obFormulario->addHidden( $obHdnVlTotalLiquidadoAnulado );
$obFormulario->addHidden( $obHdnVlTotalLiquidado );
$obFormulario->addHidden($obHdnDebitoContribuinte);
$obFormulario->addHidden($obHdnCriaContasCredDeb);

if ( $request->get('stEmitirEmpenho') ) {
    $obFormulario->addHidden( $obHdnEmitirEmpenho );
    $obFormulario->addHidden( $obHdnAcaoEmp );
    $obFormulario->addHidden( $obHdnPgProxEmpenho );
    $obFormulario->addHidden( $obHdnAcaoEmpenho );
    $obFormulario->addHidden( $obHdnPgDespesasFixas );
    $obFormulario->addHidden( $obHdnModuloEmpenho );
    $obFormulario->addHidden( $obHdnFuncionalidadeEmpenho );
}

$obFormulario->addComponente( $obLblEmpenho       );
$obFormulario->addComponente( $obLblEntidade      );
$obFormulario->addComponente( $obLblDespesa       );
$obFormulario->addComponente( $obLblClassificacao );
$obFormulario->addComponente( $obLblRecurso       );
$obFormulario->addComponente( $obLblSaldoDotacao  );
$obFormulario->addComponente( $obLblCodOrgao      );
$obFormulario->addComponente( $obLblCodUnidade    );
$obFormulario->addComponente( $obLblFornecedor    );
$obFormulario->addComponente( $obLblNomEmpenho    );
$obFormulario->addComponente( $obLblDtEmpenho     );
$obFormulario->addComponente( $obLblDtVencimento  );
$obFormulario->addComponente( $obLblTipo          );
$obFormulario->addComponente( $obLblHistorico     );

$obMontaAtributos->geraFormulario ( $obFormulario );

if ($boCriaContasDebCred) {
    $obFormulario->addTitulo( "Dados dos lançamentos contábeis" );
    $obFormulario->addTitulo( "Incorporação Patrimonial/Amortização" );
    $obFormulario->addComponente( $obBscContaDebito );
    $obFormulario->addComponente( $obBscContaCredito );
    $obFormulario->addComponente( $obBscHistorico );
    $obFormulario->addComponente( $obTxtComplemento );
}

$obFormulario->addTitulo( "Itens do empenho" );
$obFormulario->addSpan( $obSpan );
$obFormulario->addComponente( $obLblVlTotalEmp );
$obFormulario->addComponente( $obLblVlTotal );
$obFormulario->addComponente( $obLblVlTotalSaldo );

$obFormulario->addTitulo( "Liquidação" );
$obFormulario->addComponente( $obDtLiquidacao );
$obFormulario->addComponente( $obDataValidadeFinal );
$obFormulario->addComponente( $obTxtObservacao );

switch ($stConfiguracaoUf) {
    case '5':
        $obFormulario->addTitulo( "Notas Fiscais" );
        $obFormulario->addComponente( $obTxtNumeroNF );
        $obFormulario->addComponente( $obExercicioNotaFiscal );
        $obFormulario->addComponente( $obTxtSerieNF );
        $obFormulario->addComponente( $obTxtSubSerieNF );
        $obFormulario->addComponente( $obDtEmissaoNF );
        $obFormulario->addComponente( $obNuValorNota );
        $obFormulario->addComponente( $obTxtObjetoNF );
        $obFormulario->addComponente( $obTxtUFDocumento );
    break;
    //se for prefeitura de Minas Gerais, inclui as informações de documento fiscal
    case '11':
        $obFormulario->addTitulo('Documento Fiscal');
        $obFormulario->addHidden        ( $obHdnVlVlTotalDoctoFiscal );
        $obFormulario->agrupaComponentes( $arRadiosNF );
        $obFormulario->addSpan          ( $obSpnNF );
    break;
    case '20':
        $obFormulario->addComponente( $obTxtNumeroNF );
        $obFormulario->addComponente( $obTxtSerieNF );
        $obFormulario->addComponente( $obDtEmissaoNF );
        $obFormulario->addComponente( $obTxtCodValidacaoNF );
        $obFormulario->addComponente( $obTxtModeloNF );
    break;
    case '23':
        $obFormulario->addTitulo('Documento Fiscal');
        $obFormulario->addHidden        ( $obHdnVlVlTotalDoctoFiscal );
        $obFormulario->agrupaComponentes( $arRadiosNF );
        $obFormulario->addSpan          ( $obSpnNF );
    break;
}

if ($obComboEstados == 'true') {
    $obFormulario->addTitulo('Tipo de Documento');
    $obFormulario->addComponenteComposto($obTxtTipoDocumento, $obCboTipoDocumento);
    $obFormulario->addSpan($obSpanTipoDocumento);
}

$obMontaAtributosLiquidacao->geraFormulario ( $obFormulario );

if ( $request->get('stEmitirEmpenho') ) {
    if ($request->get('pgDespesasFixas', '') != '')
        $stLocation =  CAM_GF_EMP_INSTANCIAS."empenho/FMManterDespesasMensaisFixas.php?".Sessao::getId().'&stAcao='.$stAcaoEmpenho.'&acao='.$acaoEmpenho.'&modulo='.$moduloEmpenho.'&funcionalidade='.$funcionalidadeEmpenho;
    else
        $stLocation = $pgProx.'?'.Sessao::getId().'&stAcao='.$stAcaoEmpenho.'&acao='.$acaoEmpenho.'&modulo='.$moduloEmpenho.'&funcionalidade='.$funcionalidadeEmpenho;
} else
    $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obFormulario->addComponente( $obSimNaoEmitirOP );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obOk  = new Ok;
$obOk->setDisabled( true );
$obOk->obEvento->setOnClick("Salvar();");

$obCancelar  = new Cancelar;
$obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

$obFormulario->defineBarra( array( $obOk, $obCancelar ) );

$obFormulario->show();

include_once ($pgOcul);
include_once ($pgJS);

$js = "buscaDado('montaListaItemPreEmpenho');";
SistemaLegado::executaiFrameOculto($js);

SistemaLegado::liberaFrames();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>