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
    * Página de Consulta de Empenho
    * Data de Criação   : 16/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: FMConsultarEmpenho.php 65433 2016-05-20 17:57:39Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoPagamentoLiquidacao.class.php";
include_once CAM_FW_HTML."MontaAtributos.class.php";
include_once CAM_GF_PPA_MAPEAMENTO."TPPAAcao.class.php";
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao', 'incluir');

$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarUnidadeMedida( $rsUnidade );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

Sessao::remove('arItens');

$obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato;
$stFiltro  = "   AND e.exercicio    = '".$request->get('stExercicioEmpenho')."'";
$stFiltro .= "   AND e.cod_entidade =  ".$request->get('inCodEntidade');
$stFiltro .= "   AND e.cod_empenho  =  ".$request->get('inCodEmpenho');
$obTEmpenhoEmpenhoContrato->recuperaRelacionamentoEmpenhoContrato($rsEmpenhoContrato, $stFiltro, "");

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodEmpenho( $request->get('inCodEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setExercicio( $request->get('stExercicioEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->setCodAutorizacao( $request->get('inCodAutorizacao') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->setCodPreEmpenho( $request->get('inCodPreEmpenho') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $request->get('inCodEntidade') );

if (( $request->get('boImplantado') =='t' ) || ( Sessao::getExercicio() !== $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getExercicio() ) ) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarRestosAPagar();
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listarRestosAPagar( $rsSaldos );
} else {
    $obREmpenhoEmpenhoAutorizacao->consultar();
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->listar( $rsSaldos );
}

$stDotacao = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
$stDespesa = $rsSaldos->getCampo( "cod_despesa" );

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarValorItem();

$nuValorSaldoAnterior = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getVlSaldoAnterior();
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$obREmpenhoOrdemPagamento->setExercicio( $request->get('stExercicioEmpenho') );
$obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setCodEmpenho( $request->get('inCodEmpenho') );
$obREmpenhoOrdemPagamento->listarValorPago( $rsValorPago );

$inNumUnidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
$stNomUnidade = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
$inNumOrgao   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
$stNomOrgao   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->setCodDespesa( $rsSaldos->getCampo( "cod_despesa" ) );

if ($request->get('boImplantado') != 't') {
    $stFiltro = "";

    $obErro = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->consultar( $rsDespesa );

    if ( !$obErro->ocorreu() ) {
        $obErro = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->listarDespesaDotacao( $rsDotacao );
        if ( !$obErro->ocorreu() ) {
            $stDotacao = $rsDotacao->getCampo( 'dotacao' );
        }
    }

    if ( !$obErro->ocorreu() ) {
        $nuValorOriginal = $rsDespesa->getCampo( 'vl_original' );
    }

    include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php";
    $obTOrcamentoRecurso = new TOrcamentoRecurso;

    $inCodRecursoTemp = $rsDespesa->getCampo( "cod_recurso" );

    if ($inCodRecursoTemp) {
        $stFiltro .= " WHERE cod_recurso = ".$inCodRecursoTemp;
    }

    if (Sessao::getExercicio()) {
        if ( strlen($stFiltro) < 1 ) {
            $stFiltro .= " WHERE exercicio = '".Sessao::getExercicio()."'";
        } else {
            $stFiltro .= " AND exercicio = '".Sessao::getExercicio()."'";
        }
    }

    $obErro = $obTOrcamentoRecurso->recuperaTodos( $rsLista, $stFiltro);
    if ( !$obErro->ocorreu() ) {
        $stDescricaoRecurso = $rsLista->getCampo( 'nom_recurso' );
        $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setNome( $rsLista->getCampo( 'nom_recurso' ) );
    }
}

$flValorPago     = number_format( $rsValorPago->getCampo( "valor_pago" ), 2,',','.');
$flSaldoAnterior = number_format($nuValorOriginal, 2,',','.');
//Recuperação de valores!!!
$flEmpenhado        = $rsSaldos->getCampo( "vl_empenhado"         ) - $rsSaldos->getCampo( "vl_empenhado_anulado" );
$flEmpenhado        = number_format( $flEmpenhado ,2,',','.');
$flLiquidado        = $rsSaldos->getCampo( "vl_liquidado"         );
$flLiquidadoAnulado = $rsSaldos->getCampo( "vl_liquidado_anulado" );
$flLiquidado        = number_format( $flLiquidado - $flLiquidadoAnulado ,2,',','.');
//---
$dtDataFinal     = $rsSaldos->getCampo( "dt_vencimento" );
$nuVlPago        = $rsSaldos->getCampo( "vl_pago" );
$nuVlPagoAnulado = $rsSaldos->getCampo( "vl_pago_anulado" );
$nuVlTotalPago   = $nuVlPago - $nuVlPagoAnulado;
$nuVlTotalPago   = number_format( $nuVlTotalPago, 2, ',', '.' );

$nuSaldoDisponivel = $rsSaldos->getCampo( "vl_dotacao" ) != '' ? number_format($rsSaldos->getCampo( "vl_dotacao" ), 2, ',', '.') : 0;

$inCodContrapartida = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodContrapartida();
$stNomContrapartida = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getNomContrapartida();
$stNomCategoria     = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getNomCategoria();
$stNomEmpenho       = stripslashes($obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getDescricao());
$stNomEntidade      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->getNomCGM();
$inCodTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
$stNomTipo          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getNomTipo();
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
$stRecurso          = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();
$inCodRecurso       = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->getCodRecurso();
$flDotacaoInicial   = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getValorOriginal();
$flDotacaoInicial   = number_format($flDotacaoInicial,2,',','.');
$stCodPrograma      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoPrograma->getCodPrograma();
$stNomPrograma      = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoPrograma->getDescricao();

if($stDescricaoRecurso == ""){
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setExercicio($request->get('stExercicio'));
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar($rsLista);
    $stDescricaoRecurso = $rsLista->getCampo('nom_recurso');
}

$obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoPrograma->listar($rsNumprograma);

if ($rsNumprograma->getNumLinhas() > 0) {
    foreach ($rsNumprograma->getElementos() as $indice => $valor) {
        if ($valor['exercicio'] == $request->get('stExercicio')) {
            $inNumPrograma = intval($valor['num_programa']);
            $stNomPrograma = $valor['descricao'];
            break;
        } else if ($valor['exercicio'] == Sessao::getExercicio()) {
            $inNumPrograma = intval($valor['num_programa']);
            $stNomPrograma = $valor['descricao'];
        }
    }
}

if (!isset($inNumPrograma)) {
    $inNumPrograma = '';
}

$inCodPao           = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNumeroProjeto();
$stNomPao           = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoProjetoAtividade->getNome();
if($request->get('inCodAutorizacao'))
    $stAutorizacao = $request->get('inCodAutorizacao')." / ".$request->get('stExercicioEmpenho');

if ($inCodDespesa) {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->consultarSaldoDotacao();
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultarSaldoAnterior();
}
$nuSaldoDotacao   = number_format($obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->obROrcamentoDespesa->getSaldoDotacao(),2,',','.');;
$nuSaldoAnterior  = $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getSaldoAnterior();

$flLiquidado        = 0;
$flEmpenhado        = 0;
$flEmpenhadoAnulado = 0;

$arItens = array();
foreach ($arItemPreEmpenho as $inCount => $obItemPreEmpenho) {
    $arItens[$inCount]['num_item']     = $obItemPreEmpenho->getNumItem();
    if($obItemPreEmpenho->getCodItemPreEmp()!=''){
        $arItens[$inCount]['nom_item'] = $obItemPreEmpenho->getCodItemPreEmp()." - ".stripslashes($obItemPreEmpenho->getNomItem());
    }else{
        $arItens[$inCount]['nom_item'] = stripslashes($obItemPreEmpenho->getNomItem());
    }

    $inCodMarca = $obItemPreEmpenho->getCodigoMarca();
    if (!empty($inCodMarca)) {
        $stDescricaoItemMarca = SistemaLegado::pegaDado('descricao', 'almoxarifado.marca', " WHERE cod_marca = ".$inCodMarca, $boTransacao);
        $arItens[$inCount]['nom_item'] .= " (Marca: ".$inCodMarca." - ".$stDescricaoItemMarca.")";
    }

    $arItens[$inCount]['complemento']  = stripslashes($obItemPreEmpenho->getComplemento());
    $arItens[$inCount]['quantidade']   = $obItemPreEmpenho->getQuantidade();
    $arItens[$inCount]['cod_unidade']  = $obItemPreEmpenho->obRUnidadeMedida->getCodUnidade();
    $arItens[$inCount]['cod_grandeza'] = $obItemPreEmpenho->obRUnidadeMedida->obRGrandeza->getCodGrandeza();
    $arItens[$inCount]['nom_unidade']  = $obItemPreEmpenho->getNomUnidade();
    $arItens[$inCount]['vl_total']     = $obItemPreEmpenho->getValorTotal() - $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $arItens[$inCount]['vl_liquidado'] = $obItemPreEmpenho->getValorLiquidado() - $obItemPreEmpenho->getValorLiquidadoAnulado();
    $arItens[$inCount]['vl_unitario']  = bcdiv( $obItemPreEmpenho->getValorTotal(), $obItemPreEmpenho->getQuantidade(),2 );

    $flLiquidado += $obItemPreEmpenho->getValorLiquidado() - $obItemPreEmpenho->getValorLiquidadoAnulado();
    $flEmpenhado += $obItemPreEmpenho->getValorTotal();
    $flEmpenhadoAnulado += $obItemPreEmpenho->getValorEmpenhadoAnulado();
    $nuVTotal = $nuVTotal + $obItemPreEmpenho->getValorTotal();
}
Sessao::write('arItens', $arItens);
$flLiquidado        = number_format( $flLiquidado        ,2,',','.');
$flEmpenhado        = number_format( $flEmpenhado        ,2,',','.');
$flEmpenhadoAnulado = number_format( $flEmpenhadoAnulado ,2,',','.');

SistemaLegado::executaFramePrincipal("buscaDado('montaListaItemPreEmpenho');");
$arChaveAtributo =  array( "cod_pre_empenho" => $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->getCodPreEmpenho(),
                           "exercicio"       => $request->get('stExercicioEmpenho')         );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos     );

$stFiltro = '';
if ( Sessao::read('filtro') ) {
    $arFiltro = Sessao::read('filtro');
    $stFiltro = '';
    foreach ($arFiltro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            $stValorAux = '';
            foreach ($stValor as $index => $value) {
                if ($stValor[$index+1] != '') {
                    $stValorAux = $stValorAux.$value.',';
                } else {
                    $stValorAux = $stValorAux.$value;
                }
            }
            $stValor = $stValorAux;
        }
        $stFiltro .= "&".$stCampo."=".@urlencode( $stValor );
    }
    $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
}

if ($request->get('boImplantado') != 't') {
    $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho->consultaSaldoAnterior( $nuSaldoDisponivel );
    $stDespesa = $inCodDespesa.' - '.$stNomDespesa;
}

$stOrgao   = empty($stNomOrgao) ? $inNumOrgao : $inNumOrgao.' - '.$stNomOrgao;
$stUnidade = empty($stNomUnidade) ? $inNumUnidade : $inNumUnidade.' - '.$stNomUnidade;

if ($inNumPrograma != '') {
    $stLblPrograma = empty($stNomPrograma) ? $inNumPrograma : $inNumPrograma.' - '.$stNomPrograma;
} else {
    $stLblPrograma = $inNumPrograma;
}

$nuSaldoAnterior = number_format($nuValorSaldoAnterior,2,',','.');;
$nuSaldoDisponivel = number_format($nuSaldoDisponivel,2,',','.');
    
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20 && !$boEmpenhoComplementar) {
    include_once CAM_GPC_TCERN_MAPEAMENTO."TTCERNFundeb.class.php";
    $stFiltroTCERN = " WHERE cod_empenho = ".$request->get('inCodEmpenho')."
                         AND cod_entidade = ".$request->get('inCodEntidade')."
                         AND exercicio = '".$request->get('stExercicioEmpenho')."' ";
    $obTTCERNFundeb = new TTCERNFundeb;
    $obTTCERNFundeb->recuperaRelacionamento($rsFundeb, $stFiltroTCERN);

    include_once CAM_GPC_TCERN_MAPEAMENTO."TTCERNRoyalties.class.php";
    $obTTCERNRoyalties = new TTCERNRoyalties;
    $obTTCERNRoyalties->recuperaRelacionamento($rsRoyalties, $stFiltroTCERN);
}

/*
* Verifica se o Empenho é complementar
*/

$boEmpenhoComplementar = false;

include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoComplementar.class.php";
$obTEmpenhoEmpenhoComplementar = new TEmpenhoEmpenhoComplementar();
$obTEmpenhoEmpenhoComplementar->setDado( 'cod_empenho' , $request->get('inCodEmpenho') );
$obTEmpenhoEmpenhoComplementar->setDado( 'cod_entidade', $request->get('inCodEntidade') );
$obTEmpenhoEmpenhoComplementar->setDado( 'exercicio'   , $request->get('stExercicioEmpenho') );
$obErro = $obTEmpenhoEmpenhoComplementar->recuperaPorChave( $rsEmpenhoComplementar, '' );

if ($rsEmpenhoComplementar->inNumLinhas > 0) {
    $boEmpenhoComplementar = true;
    $inCodEmpenhoOriginal = $rsEmpenhoComplementar->getCampo('cod_empenho_original');
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

// Define objeto Hidden para Codigo da Autorizacao
$obHdnCodAutorizacao = new Hidden;
$obHdnCodAutorizacao->setName ( "inCodAutorizacao" );
$obHdnCodAutorizacao->setValue( $inCodAutorizacao );

// Define objeto Hidden para Codigo da Pre Empenho
$obHdnCodPreEmpenho = new Hidden;
$obHdnCodPreEmpenho->setName ( "inCodPreEmpenho" );
$obHdnCodPreEmpenho->setValue( $inCodPreEmpenho );

// Define objeto Hidden para Codigo da Entidade
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName ( "inCodEntidade" );
$obHdnCodEntidade->setValue( $inCodEntidade );
$obHdnCodEntidade->setId   ( "inCodEntidade" );

// Define objeto Hidden para Codigo da Reserva
$obHdnCodReserva = new Hidden;
$obHdnCodReserva->setName  ( "inCodReserva" );
$obHdnCodReserva->setValue ( $inCodReserva  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName  ( "stCodClassificacao" );
$obHdnCodClassificacao->setValue ( $stCodClassificacao  );

// Define Objeto Label para Fornecedor
$obHdnCodFornecedor = new Hidden;
$obHdnCodFornecedor->setName     ( "inCodFornecedor" );
$obHdnCodFornecedor->setValue    ( $inCodFornecedor  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodDespesa = new Hidden;
$obHdnCodDespesa->setName  ( "inCodDespesa" );
$obHdnCodDespesa->setValue ( $inCodDespesa  );

// Define objeto Hidden para Codigo da Classificacao
$obHdnCodHistorico = new Hidden;
$obHdnCodHistorico->setName  ( "inCodHistorico" );
$obHdnCodHistorico->setValue ( $inCodHistorico  );

$inCodEmpenho = $request->get('inCodEmpenho');
$inCodEntidade = $request->get('inCodEntidade');

// Define objeto Label para Empenho
$obLblEmpenho = new Label;
$obLblEmpenho->setRotulo( "N° Empenho" );
$obLblEmpenho->setValue ( $inCodEmpenho.' / '.$request->get('stExercicioEmpenho'));

// Define objeto Label para Tipo de Empenho
$obLblTipoEmpenho = new Label;
$obLblTipoEmpenho->setRotulo( "Tipo de Empenho" );
$obLblTipoEmpenho->setValue ( $stNomTipo );

// Define objeto Label para Data de Empenho
$obLblDataEmpenho = new Label;
$obLblDataEmpenho->setRotulo( "Data de Empenho" );
$obLblDataEmpenho->setValue ( $stDtEmpenho );

//Define objeto label entidade
$obLblEntidade = new Label;
$obLblEntidade->setRotulo( "Entidade"                             );
$obLblEntidade->setId    ( "stNomeEntidade"                        );
$obLblEntidade->setValue ( $inCodEntidade.' - '.$stNomEntidade     );

$obLblDotacao = new Label;
$obLblDotacao->setRotulo( "Dotação Orçamentária"    );
$obLblDotacao->setId    ( "stDotacao"               );
$obLblDotacao->setValue ( $stDotacao );

$obLblCdgReduzido = new Label;
$obLblCdgReduzido->setRotulo( "Código Reduzido"     );
$obLblCdgReduzido->setId    ( "stNomDespesa"        );
$obLblCdgReduzido->setValue ( $stDespesa );

$obLblDesdobramento = new Label;
$obLblDesdobramento->setRotulo( "Desdobramento"       );
$obLblDesdobramento->setId    ( "stNomClassificacao"  );
$obLblDesdobramento->setValue ( empty($stNomClassificacao) ? $stCodClassificacao : $stCodClassificacao.' - '.$stNomClassificacao );

$obLblOrgao = new Label;
$obLblOrgao->setRotulo( "Orgão Orçamentário"              );
$obLblOrgao->setId    ( "inCodOrgao"                     );
$obLblOrgao->setValue ( $stOrgao );

$obLblUnidade = new Label;
$obLblUnidade->setRotulo( "Unidade Orçamentária"                );
$obLblUnidade->setId    ( "inCodUnidade"                       );
$obLblUnidade->setValue ( $stUnidade );

// Define objeto Label para PAO
$obLblPAO = new Label;
$obLblPAO->setRotulo( 'PAO' );
$obLblPAO->setId    ( 'pao' );
$obLblPAO->setValue ( empty($stNomPao) ? $inCodPao: $inCodPao.' - '.$stNomPao );

$obLblPrograma = new Label;
$obLblPrograma->setRotulo( "Programa"   );
$obLblPrograma->setId    ( "stPrograma" );
$obLblPrograma->setValue ( $stLblPrograma );

$obLblFornecedor = new Label;
$obLblFornecedor->setRotulo( "Fornecedor"       );
$obLblFornecedor->setId    ( "stNomFornecedor" );
$obLblFornecedor->setValue ( $inCodFornecedor.' - '.$stNomFornecedor  );

$obLblContrapartida = new Label;
$obLblContrapartida->setRotulo( "Contrapartida"       );
$obLblContrapartida->setId    ( "stContrapartida" );
$obLblContrapartida->setValue ( $inCodContrapartida.' - '.$stNomContrapartida );

$obLblAutorizacao = new Label;
$obLblAutorizacao->setRotulo( "Autorização"       );
$obLblAutorizacao->setId    ( "inCodAutorizacao" );
$obLblAutorizacao->setValue ( $stAutorizacao );

$obLblCategoria = new Label;
$obLblCategoria->setRotulo( "Categoria do Empenho" );
$obLblCategoria->setId    ( "stNomCategoria"       );
$obLblCategoria->setValue ( $stNomCategoria        );

$obLblDescricao = new Label;
$obLblDescricao->setRotulo( "Descrição do Empenho" );
$obLblDescricao->setId    ( "stNomEmpenho"              );
$obLblDescricao->setValue ( $stNomEmpenho               );

$flValorEmpenhado = $rsSaldos->getCampo( "vl_empenhado" ) - $rsSaldos->getCampo( "vl_empenhado_anulado" );

$obLblSaldoDotacao = new Label;
$obLblSaldoDotacao->setRotulo( "Saldo Dotação"  );
$obLblSaldoDotacao->setId    ( "flSaldoDotacao " );
$obLblSaldoDotacao->setValue ( number_format($nuValorSaldoAnterior - $flValorEmpenhado,2,',','.' ) );

$obLblVencimento = new Label;
$obLblVencimento->setRotulo( "Data de Vencimento" );
$obLblVencimento->setId    ( "dtDataVencimento"   );
$obLblVencimento->setValue ( $stDtVencimento    );

$obLblHistorico = new Label;
$obLblHistorico->setRotulo( "Histórico"   );
$obLblHistorico->setId    ( "stNomHistorico" );
$obLblHistorico->setValue ( $inCodHistorico." - ".$stNomHistorico  );

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->setCodEmpenho              ( $request->get('inCodEmpenho')  );
$obREmpenhoEmpenho->setCodEntidade             ( $request->get('inCodEntidade') );
$obREmpenhoEmpenho->setExercicio               ( $request->get('stExercicio')   );
$obREmpenhoEmpenho->setBoEmpenhoCompraLicitacao( true );
$obREmpenhoEmpenho->setCodModalidadeCompra     ( $request->get('inCodModalidadeCompra') );
$obREmpenhoEmpenho->setCompraInicial           ( $request->get('inCompraInicial') );
$obREmpenhoEmpenho->setCompraFinal             ( $request->get('inCompraFinal') );
$obREmpenhoEmpenho->setCodModalidadeLicitacao  ( $request->get('inCodModalidadeLicitacao') );
$obREmpenhoEmpenho->setLicitacaoInicial        ( $request->get('inLicitacaoInicial') );
$obREmpenhoEmpenho->setLicitacaoFinal          ( $request->get('inLicitacaoFinal') );
$obREmpenhoEmpenho->listarConsultaEmpenho( $rsListaModalidade );

$obLblCodCompraDireta = new Label;
$obLblCodCompraDireta->setRotulo( "Compra Direta"      );
$obLblCodCompraDireta->setId    ( "inCodCompraDireta"  );
$obLblCodCompraDireta->setValue ( $rsListaModalidade->getCampo('cod_compra_direta') );

$obLblModalidadeCompraDireta = new Label;
$obLblModalidadeCompraDireta->setRotulo( "Modalidade da Compra Direta" );
$obLblModalidadeCompraDireta->setId    ( "inModalidadeCompraDireta"    );
$obLblModalidadeCompraDireta->setValue ( $rsListaModalidade->getCampo('compra_cod_modalidade').' - '.$rsListaModalidade->getCampo('compra_modalidade') );

$obLblCodLicitacao = new Label;
$obLblCodLicitacao->setRotulo( "Licitação"            );
$obLblCodLicitacao->setId    ( "inCodLicitacao"       );
$obLblCodLicitacao->setValue ( $rsListaModalidade->getCampo('cod_licitacao').' / '.$rsListaModalidade->getCampo('exercicio') );

$obLblModalidadeLicitacao = new Label;
$obLblModalidadeLicitacao->setRotulo( "Modalidade da Licitação" );
$obLblModalidadeLicitacao->setId    ( "inModalidadeLicitacao"   );
$obLblModalidadeLicitacao->setValue ( $rsListaModalidade->getCampo('licitacao_cod_modalidade').' - '.$rsListaModalidade->getCampo('licitacao_modalidade') );

if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20 && !$boEmpenhoComplementar) {
    $obLblFundeb = new Label;
    $obLblFundeb->setRotulo( "Fundeb" );
    $obLblFundeb->setId    ( "stCodFundeb"   );
    $obLblFundeb->setValue ( $rsFundeb->getCampo('codigo') );

    $obLblRoyalties = new Label;
    $obLblRoyalties->setRotulo( "Royalties"   );
    $obLblRoyalties->setId    ( "stCodRoyalties" );
    $obLblRoyalties->setValue ( $rsRoyalties->getCampo('codigo') );
}

if ($boEmpenhoComplementar) {
    $obLblFlagComplementar = new Label;
    $obLblFlagComplementar->setRotulo( "Empenho Complementar"   );
    $obLblFlagComplementar->setId    ( "stEmpenhoComplementar" );
    $obLblFlagComplementar->setValue ( "Sim" );

    $obLblNroEmpenhoComplementar = new Label;
    $obLblNroEmpenhoComplementar->setRotulo( "Empenho Original"   );
    $obLblNroEmpenhoComplementar->setId    ( "inCodEmpenhoOriginal" );
    $obLblNroEmpenhoComplementar->setValue ( $inCodEmpenhoOriginal );
}

// Atributos Dinamicos
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo     ( "Atributos"  );
$obMontaAtributos->setName       ( "Atributo_"  );
$obMontaAtributos->setRecordSet  ( $rsAtributos );
$obMontaAtributos->setLabel      ( true );

if ($rsEmpenhoContrato->getNumLinhas() > 0) {
    $obLblContrato = new Label;
    $obLblContrato->setRotulo( "Número do Contrato"  );
    $obLblContrato->setId    ( "inCodContrato" );
    $obLblContrato->setValue ( $rsEmpenhoContrato->getCampo('num_contrato').'/'.$rsEmpenhoContrato->getCampo('exercicio_contrato')  );
}

// Saldos
$obLblDotacaoInicial = new Label;
$obLblDotacaoInicial->setRotulo( "Valor Orçado"  );
$obLblDotacaoInicial->setId    ( "flSaldoAnterior" );
$obLblDotacaoInicial->setValue ( $flSaldoAnterior   );

$obLblSaldoAnterior = new Label;
$obLblSaldoAnterior->setRotulo( "Saldo Anterior"  );
$obLblSaldoAnterior->setId    ( "flSaldoAnterior " );
$obLblSaldoAnterior->setValue ( $nuSaldoAnterior  );

$obLblSaldoDisponivel = new Label;
$obLblSaldoDisponivel->setRotulo( "Saldo Disponível"  );
$obLblSaldoDisponivel->setId    ( "flDotacaoInicial " );
$obLblSaldoDisponivel->setValue ( number_format($nuValorSaldoAnterior - $flValorEmpenhado,2,',','.' )  );

$obLblEmpenhado = new Label;
$obLblEmpenhado->setRotulo( "Valor Empenhado"   );
$obLblEmpenhado->setId    ( "flEmpenhado"       );
$obLblEmpenhado->setValue ( $flEmpenhado        );

// Define Objeto BuscaInner para Anulações
$obLblEmpenhadoAnulado = new BuscaInner;
$obLblEmpenhadoAnulado->setRotulo ( "Anulações Empenho"      );
$obLblEmpenhadoAnulado->setId     ( "flEmpenhoAnulado"       );
$obLblEmpenhadoAnulado->setValue  ( $flEmpenhadoAnulado        );
$obLblEmpenhadoAnulado->obCampoCod->setName ( "flEmpenhoAnulado" );
$obLblEmpenhadoAnulado->obCampoCod->setMinLength( 0 );
$obLblEmpenhadoAnulado->obCampoCod->setMaxLength( 0 );
$obLblEmpenhadoAnulado->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/LSConsultaEmpenhoAnulado.php','frm','','','consultaEmpenho&inCodEntidade=".$inCodEntidade."&inCodEmpenho=".$inCodEmpenho."&stExercicio=".$request->get('stExercicioEmpenho')."','".Sessao::getId()."','800','450');");

// Define Objeto BuscaInner para Liquidações
$obLblVlLiquidado = new BuscaInner;
$obLblVlLiquidado->setRotulo ( "Liquidações"      );
$obLblVlLiquidado->setId     ( "nuVLiquidado"     );
$obLblVlLiquidado->setValue  ( $flLiquidado       );
$obLblVlLiquidado->obCampoCod->setName ( "nuVLiquidado" );
$obLblVlLiquidado->obCampoCod->setMinLength( 0 );
$obLblVlLiquidado->obCampoCod->setMaxLength( 0 );
$obLblVlLiquidado->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/LSConsultaEmpenhoLiquidado.php','frm','','','consultaEmpenho&inCodEntidade=".$inCodEntidade."&inCodEmpenho=".$inCodEmpenho."&stExercicio=".$request->get('stExercicioEmpenho')."','".Sessao::getId()."','800','450');");

// Define Objeto BuscaInner para Pagamentos
$obLblPago = new BuscaInner;
$obLblPago->setRotulo ( "Pagamentos"       );
$obLblPago->setId     ( "flPago"     );
$obLblPago->setValue  ( $nuVlTotalPago       );
$obLblPago->obCampoCod->setName ( "flPago" );
$obLblPago->obCampoCod->setMinLength( 0 );
$obLblPago->obCampoCod->setMaxLength( 0 );
$obLblPago->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/LSConsultaEmpenhoPago.php','frm','','','consultaEmpenho&inCodEntidade=".$inCodEntidade."&inCodEmpenho=".$inCodEmpenho."&stExercicio=".$request->get('stExercicioEmpenho')."','".Sessao::getId()."','800','450');");

include_once CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php";
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setCodRecurso ( $inCodRecurso );
$obIMontaRecursoDestinacao->setDescricaoRecurso ( $stDescricaoRecurso );
$obIMontaRecursoDestinacao->setLabel ( true );

$pgProx = CAM_FW_POPUPS."relatorio/OCRelatorio.php";
$stLink .= "&stCaminho=".CAM_GF_EMP_INSTANCIAS."empenho/OCRelatorioRazaoEmpenho.php";
$stLink .= "&inCodEmpenho=".$inCodEmpenho."&stExercicio=".$request->get('stExercicioEmpenho')."&inCodEntidade=".$request->get('inCodEntidade');

$obLnknRazao = new Link;
$obLnknRazao->setRotulo ("Razão do Empenho" );
$obLnknRazao->setValue  ("Visualizar"       );
$obLnknRazao->setTarget ("oculto"           );
$obLnknRazao->setHref   ( $pgProx."?".Sessao::getId().$stLink );

$obLblDataFinal = new Label;
$obLblDataFinal->setRotulo( "Data Validade Final" );
$obLblDataFinal->setId    ( "dtDataFinal"         );
$obLblDataFinal->setValue ( $dtDataFinal          );

$obSpnLista = new Span;
$obSpnLista->setId ( "spnLista" );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm                    );
$obFormulario->addTitulo( "Dados do empenho"       );
$obFormulario->addHidden( $obHdnCtrl               );
$obFormulario->addHidden( $obHdnAcao               );
$obFormulario->addHidden( $obHdnCodAutorizacao     );
$obFormulario->addHidden( $obHdnCodPreEmpenho      );
$obFormulario->addHidden( $obHdnCodEntidade        );
$obFormulario->addHidden( $obHdnCodReserva         );
$obFormulario->addHidden( $obHdnCodFornecedor      );
$obFormulario->addHidden( $obHdnCodDespesa         );
$obFormulario->addHidden( $obHdnCodHistorico       );

$obFormulario->addComponente( $obLblEmpenho        );
$obFormulario->addComponente( $obLblTipoEmpenho    );
$obFormulario->addComponente( $obLblDataEmpenho    );
$obFormulario->addComponente( $obLblEntidade       );
$obFormulario->addComponente( $obLblOrgao          );
$obFormulario->addComponente( $obLblUnidade        );
$obFormulario->addComponente( $obLblDotacao        );
$obFormulario->addComponente( $obLblCdgReduzido    );
$obFormulario->addComponente( $obLblDesdobramento  );
$obFormulario->addComponente( $obLblPAO            );
$obFormulario->addComponente( $obLblPrograma       );
$obFormulario->addComponente( $obLblCategoria      );
if($inCodContrapartida)
    $obFormulario->addComponente( $obLblContrapartida );
$obFormulario->addComponente( $obLblDescricao      );
$obFormulario->addComponente( $obLblSaldoDotacao   );
$obFormulario->addComponente( $obLblFornecedor     );
$obFormulario->addComponente( $obLblAutorizacao    );
$obFormulario->addComponente( $obLblVencimento     );
$obFormulario->addComponente( $obLblHistorico      );

if ($rsListaModalidade->getCampo('cod_compra_direta') != '') {
    $obFormulario->addComponente( $obLblCodCompraDireta        );
    $obFormulario->addComponente( $obLblModalidadeCompraDireta );
}

if ($rsListaModalidade->getCampo('cod_licitacao') != '') {
    $obFormulario->addComponente( $obLblCodLicitacao        );
    $obFormulario->addComponente( $obLblModalidadeLicitacao );
}

if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == 20 && !$boEmpenhoComplementar) {
    $obFormulario->addComponente( $obLblFundeb         );
    $obFormulario->addComponente( $obLblRoyalties      );
}

if ($boEmpenhoComplementar) {
    $obFormulario->addComponente( $obLblFlagComplementar );
    $obFormulario->addComponente( $obLblNroEmpenhoComplementar );
}

$obMontaAtributos->geraFormulario( $obFormulario   );

if ($rsEmpenhoContrato->getNumLinhas() > 0) {
    $obFormulario->addTitulo( "Contrato"           );
    $obFormulario->addComponente( $obLblContrato   );
}

$obFormulario->addTitulo( "Saldos"                 );
$obFormulario->addComponente( $obLblDotacaoInicial );
$obFormulario->addComponente( $obLblSaldoAnterior  );
$obFormulario->addComponente( $obLblSaldoDisponivel);
$obFormulario->addComponente( $obLblEmpenhado      );
$obFormulario->addComponente( $obLblEmpenhadoAnulado );
$obFormulario->addComponente( $obLblVlLiquidado    );
$obFormulario->addComponente( $obLblPago           );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obLnknRazao          );
$obFormulario->addTitulo( "Ítens do empenho"       );
$obFormulario->addSpan  ( $obSpnLista              );

$obFormulario->addComponente( $obLblDataFinal      );

include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setCodDocumento( $request->get('inCodEmpenho') );
$obMontaAssinaturas->setCodEntidade( $request->get('inCodEntidade') );
$obMontaAssinaturas->setExercicio( $request->get('stExercicioEmpenho') );
$obMontaAssinaturas->geraListaLeituraFormulario( $obFormulario, 'nota_empenho' );

$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );
$obFormulario->show();

?>
