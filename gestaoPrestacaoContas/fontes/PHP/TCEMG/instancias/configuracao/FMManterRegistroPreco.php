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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 11/03/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGItemRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosOrgao.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosOrgaoItem.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGEmpenhoRegistroPrecos.class.php";
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';
include_once CAM_GF_PPA_COMPONENTES.'MontaOrgaoUnidade.class.php';
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosLicitacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once ($pgJs);
include_once ($pgOcul);

$stAcao = $request->get('stAcao');

Sessao::write('arOrgaos'                        , array());
Sessao::write('arOrgaosRemovido'                , array());
Sessao::write('arOrgaoItemQuantitativos'        , array());
Sessao::write('arOrgaoItemQuantitativosRemovido', array());
Sessao::write('arItens'                         , array());
Sessao::write('arItensRemovido'                 , array());
Sessao::write('arEmpenhos'                      , array());
Sessao::write('arEmpenhosRemovidos'             , array());

if ($stAcao == 'alterar' && ($request->get('inNroRegistroPrecos') != '' && $request->get('stExercicioRegistroPrecos') != '')) {
    $rsRegistroPrecos = new RecordSet();
    $obTTCEMGRegistroPrecos = new TTCEMGRegistroPrecos();
    $obTTCEMGRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
    $obTTCEMGRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
    $obTTCEMGRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
    $obTTCEMGRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
    $obTTCEMGRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
    $obTTCEMGRegistroPrecos->recuperaProcesso($rsRegistroPrecos);

    $inCodEntidade                = $rsRegistroPrecos->getCampo('cod_entidade');
    $stExercicioRegistroPreco     = $rsRegistroPrecos->getCampo('exercicio_registro_precos');
    $stCodigoRegistroPrecos       = $rsRegistroPrecos->getCampo('codigo_registro_precos');
    $dtAberturaRegistroPrecos     = $rsRegistroPrecos->getCampo('data_abertura_registro_precos');
    $inNumGerenciador             = $rsRegistroPrecos->getCampo('numcgm_gerenciador');
    $stNomGerenciador             = $rsRegistroPrecos->getCampo('nomcgm_gerenciador');
    $stExercicioProcessoLicitacao = $rsRegistroPrecos->getCampo('exercicio_licitacao');
    $inCodigoModalidadeLicitacao  = $rsRegistroPrecos->getCampo('codigo_modalidade_licitacao');
    $stCodigoProcessoLicitacao    = $rsRegistroPrecos->getCampo('numero_processo_licitacao');
    $inNumeroModalidade           = $rsRegistroPrecos->getCampo('numero_modalidade');
    $dtAtaRegistroPreco           = $rsRegistroPrecos->getCampo('data_ata_registro_preco');
    $dtAtaRegistroPrecoValidade   = $rsRegistroPrecos->getCampo('data_ata_registro_preco_validade');
    $txtObjeto                    = $rsRegistroPrecos->getCampo('objeto');
    $inNumCGMResponsavel          = $rsRegistroPrecos->getCampo('numcgm_responsavel');
    $stNomCGMResponsavel          = $rsRegistroPrecos->getCampo('nomcgm_responsavel');
    $inDescontoTabela             = $rsRegistroPrecos->getCampo('desconto_tabela');
    $inProcessoLote               = $rsRegistroPrecos->getCampo('processo_lote');
    $boTipoRegistroPrecos         = $rsRegistroPrecos->getCampo('interno');
    
    $obTTCEMGRegistroPrecosLicitacao = new TTCEMGRegistroPrecosLicitacao();
    $obTTCEMGRegistroPrecosLicitacao->setDado('cod_entidade'            , $request->get('inCodEntidade'));
    $obTTCEMGRegistroPrecosLicitacao->setDado('numero_registro_precos'  , $request->get('inNroRegistroPrecos'));
    $obTTCEMGRegistroPrecosLicitacao->setDado('exercicio'               , $request->get('stExercicioRegistroPrecos'));
    $obTTCEMGRegistroPrecosLicitacao->setDado('interno'                 , $request->get('boInterno'));
    $obTTCEMGRegistroPrecosLicitacao->setDado('numcgm_gerenciador'      , $request->get('numcgmGerenciador'));
    $obTTCEMGRegistroPrecosLicitacao->recuperaPorChave($rsRegistroPrecosLicitacao);
    
    $stExercicioLicitacao   = $rsRegistroPrecosLicitacao->getCampo('exercicio_licitacao');
    $inCodModalidade        = $rsRegistroPrecosLicitacao->getCampo('cod_modalidade');
    $inCodLicitacao         = $rsRegistroPrecosLicitacao->getCampo('cod_licitacao');
    $inCodLicitacao         = ($inCodLicitacao!='') ? $inCodLicitacao.'/'.$stExercicioLicitacao : NULL;
    
    # Monta array Sessao para armazenar os itens.
    $obTTCEMGRegistroPrecosOrgao = new TTCEMGRegistroPrecosOrgao();
    $obTTCEMGRegistroPrecosOrgao->setDado('numero_registro_precos'   , $request->get('inNroRegistroPrecos'));
    $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_registro_precos', $request->get('stExercicioRegistroPrecos'));
    $obTTCEMGRegistroPrecosOrgao->setDado('cod_entidade'             , $request->get('inCodEntidade'));
    $obTTCEMGRegistroPrecosOrgao->setDado('interno'                  , $request->get('boInterno'));
    $obTTCEMGRegistroPrecosOrgao->setDado('numcgm_gerenciador'       , $request->get('numcgmGerenciador'));
    
    $obTTCEMGRegistroPrecosOrgao->recuperaProcessoOrgao($rsOrgao);

    $arOrgaos = array();
    $inCount = 0;
    
    # Carrega os orgãos para alteração
    while (!($rsOrgao->eof())) {
        $arOrgaos[$inCount]['stExercicioOrgao']          = $rsOrgao->getCampo('exercicio_unidade');
        $arOrgaos[$inCount]['stUnidadeOrcamentaria']     = str_pad($rsOrgao->getCampo('num_orgao'), 2, "0", STR_PAD_LEFT).".".str_pad($rsOrgao->getCampo('num_unidade'), 2, "0", STR_PAD_LEFT);
        $arOrgaos[$inCount]['stMontaCodOrgaoM']          = SistemaLegado::pegaDado("nom_orgao", "orcamento.orgao", "WHERE exercicio ='".$rsOrgao->getCampo('exercicio_unidade')."' AND num_orgao = ".$rsOrgao->getCampo('num_orgao'));
        $arOrgaos[$inCount]['stMontaCodUnidadeM']        = SistemaLegado::pegaDado("nom_unidade", "orcamento.unidade", "WHERE exercicio ='".$rsOrgao->getCampo('exercicio_unidade')."' AND num_orgao = ".$rsOrgao->getCampo('num_orgao')." AND num_unidade = ".$rsOrgao->getCampo('num_unidade'));
        $arOrgaos[$inCount]['inMontaCodOrgaoM']          = $rsOrgao->getCampo('num_orgao');
        $arOrgaos[$inCount]['inMontaCodUnidadeM']        = $rsOrgao->getCampo('num_unidade');
        $arOrgaos[$inCount]['inNaturezaProcedimento']    = ($rsOrgao->getCampo('participante') == 't') ? 1 : 2;
        $arOrgaos[$inCount]['inOrgaoGerenciador']        = ($rsOrgao->getCampo('gerenciador') == 't') ? 1 : 2;
        $arOrgaos[$inCount]['stOrgaoGerenciador']        = ($rsOrgao->getCampo('gerenciador') == 't') ? "Sim":"Não";
        $arOrgaos[$inCount]['stNaturezaProcedimento']    = ($rsOrgao->getCampo('participante') == 't') ? "Órgão Participante":"Órgão Não Participante";
        $arOrgaos[$inCount]['stCodigoProcessoAdesao']    = ($rsOrgao->getCampo('numero_processo_adesao')!=NULL&&$rsOrgao->getCampo('exercicio_adesao')!=NULL) ? $rsOrgao->getCampo('numero_processo_adesao') : '';
        $arOrgaos[$inCount]['stExercicioProcessoAdesao'] = ($rsOrgao->getCampo('numero_processo_adesao')!=NULL&&$rsOrgao->getCampo('exercicio_adesao')!=NULL) ? $rsOrgao->getCampo('exercicio_adesao') : '';
        $arOrgaos[$inCount]['dtAdesao']                  = $rsOrgao->getCampo('dt_adesao');
        $arOrgaos[$inCount]['dtPublicacaoAvisoIntencao'] = $rsOrgao->getCampo('dt_publicacao_aviso_intencao');
        $arOrgaos[$inCount]['inResponsavel']             = $rsOrgao->getCampo('numcgm_responsavel');
        $arOrgaos[$inCount]['stNomResponsavel']          = $rsOrgao->getCampo('nomcgm_responsavel');
        $arOrgaos[$inCount]['inStNomResponsavel']        = $rsOrgao->getCampo('st_cgm_responsavel'); 
        $arOrgaos[$inCount]['inId'] = ($inCount + 1);

        if($rsOrgao->getCampo('numcgm_responsavel')=='')
            $boResponsavelOrgao = true;

        $inCount++;
        $rsOrgao->proximo();
    }
    Sessao::write('arOrgaos', $arOrgaos);
    
    # Monta array Sessao para armazenar os itens.
    $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
    $obTTCEMGItemRegistroPrecos->setDado('numero_registro_precos', $request->get('inNroRegistroPrecos'));
    $obTTCEMGItemRegistroPrecos->setDado('exercicio'             , $request->get('stExercicioRegistroPrecos'));
    $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'          , $request->get('inCodEntidade'));
    $obTTCEMGItemRegistroPrecos->setDado('interno'               , $request->get('boInterno'));
    $obTTCEMGItemRegistroPrecos->setDado('numcgm_gerenciador'    , $request->get('numcgmGerenciador'));
    
    $obTTCEMGItemRegistroPrecos->recuperaListaItem($rsItem);
    
    $arItens = array();
    $inCount = 0;
    
    # Carrega os itens para alteração
    while (!($rsItem->eof())) {
        $arItens[$inCount]['inCodItem']                = $rsItem->getCampo('cod_item');
        $arItens[$inCount]['inNumItemLote']            = $rsItem->getCampo('num_item');
        $arItens[$inCount]['inOrdemClassifFornecedor'] = $rsItem->getCampo('ordem_classificacao_fornecedor');
        $arItens[$inCount]['stNomItem']                = $rsItem->getCampo('descricao_resumida');
        $arItens[$inCount]['stNomUnidade']             = $rsItem->getCampo('nom_unidade');
        $arItens[$inCount]['nuVlReferencia']           = number_format($rsItem->getCampo('vl_cotacao_preco_unitario'), 4, ',', '.');
        $arItens[$inCount]['nuQuantidade']             = number_format($rsItem->getCampo('quantidade_cotacao'), 4, ',', '.');
        $arItens[$inCount]['nuVlTotal']                = number_format(($rsItem->getCampo('vl_cotacao_preco_unitario') * $rsItem->getCampo('quantidade_cotacao')), 4, ',', '.');
        $arItens[$inCount]['dtCotacao']                = $rsItem->getCampo('data_cotacao');
        $arItens[$inCount]['stCodigoLote']             = ($rsItem->getCampo('cod_lote') != 0) ? $rsItem->getCampo('cod_lote') : '0';
        $arItens[$inCount]['nuPercentualLote']         = $rsItem->getCampo('percentual_desconto_lote');
        $arItens[$inCount]['txtDescricaoLote']         = $rsItem->getCampo('descricao_lote');
        $arItens[$inCount]['nuVlUnitario']             = number_format($rsItem->getCampo('preco_unitario'), 4, ',', '.');
        $arItens[$inCount]['nuQtdeLicitada']           = number_format($rsItem->getCampo('quantidade_licitada'), 4, ',', '.');
        $arItens[$inCount]['nuQtdeAderida']            = number_format($rsItem->getCampo('quantidade_aderida'), 4, ',', '.');
        $arItens[$inCount]['nuPercentualItem']         = $rsItem->getCampo('percentual_desconto');
        $arItens[$inCount]['inNumCGMVencedor']         = $rsItem->getCampo('numcgm_vencedor');
        $arItens[$inCount]['stNomCGMVencedor']         = $rsItem->getCampo('nomcgm_vencedor');
        
        $arItens[$inCount]['inId'] = ($inCount + 1);

        $inCount++;
        $rsItem->proximo();
    }
    
    # Monta array Sessao para armazenar os Quantitativos.
    $obTTCEMGRegistroPrecosOrgaoItem = new TTCEMGRegistroPrecosOrgaoItem();
    $obTTCEMGRegistroPrecosOrgaoItem->setDado('numero_registro_precos'   , $request->get('inNroRegistroPrecos'));
    $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_registro_precos', $request->get('stExercicioRegistroPrecos'));
    $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_entidade'             , $request->get('inCodEntidade'));
    $obTTCEMGRegistroPrecosOrgaoItem->setDado('interno'                  , $request->get('boInterno'));
    $obTTCEMGRegistroPrecosOrgaoItem->setDado('numcgm_gerenciador'       , $request->get('numcgmGerenciador'));
    $obTTCEMGRegistroPrecosOrgaoItem->recuperaPorChave($rsQuantitativo);
    
    $arQuantitativos = array();
    $arQuantitativosItemTemp = array();
    $inCount = 0;
    $rsQuantitativo->ordena('num_item');
    while ( !($rsQuantitativo->eof()) ) {
        $arQuantitativos[$inCount]['stExercicioOrgao'] = $rsQuantitativo->getCampo('exercicio_unidade');
        $arQuantitativos[$inCount]['inCodOrgaoQ']      = $rsQuantitativo->getCampo('num_orgao');
        $arQuantitativos[$inCount]['stNomOrgaoQ']      = SistemaLegado::pegaDado("nom_orgao", "orcamento.orgao", "WHERE exercicio ='".$rsQuantitativo->getCampo('exercicio_unidade')."' AND num_orgao = ".$rsQuantitativo->getCampo('num_orgao'));
        $arQuantitativos[$inCount]['inCodUnidadeQ']    = $rsQuantitativo->getCampo('num_unidade');
        $arQuantitativos[$inCount]['stNomUnidadeQ']    = SistemaLegado::pegaDado("nom_unidade", "orcamento.unidade", "WHERE exercicio ='".$rsQuantitativo->getCampo('exercicio_unidade')."' AND num_orgao = ".$rsQuantitativo->getCampo('num_orgao')." AND num_unidade = ".$rsQuantitativo->getCampo('num_unidade'));
        $arQuantitativos[$inCount]['inCodLoteQ']       = ($rsQuantitativo->getCampo('cod_lote') != 0) ? $rsQuantitativo->getCampo('cod_lote') : '0';
        $arQuantitativos[$inCount]['inNumItemQ']       = $rsQuantitativo->getCampo('num_item');
        $arQuantitativos[$inCount]['inCodItemQ']       = $rsQuantitativo->getCampo('cod_item');
        $arQuantitativos[$inCount]['stNomItemQ']       = SistemaLegado::pegaDado("descricao_resumida", "almoxarifado.catalogo_item", "where cod_item = ".$rsQuantitativo->getCampo('cod_item'));
        $arQuantitativos[$inCount]['inCodFornecedorQ'] = $rsQuantitativo->getCampo('cgm_fornecedor');
        $arQuantitativos[$inCount]['stNomFornecedorQ'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$rsQuantitativo->getCampo('cgm_fornecedor'));
        $arQuantitativos[$inCount]['nuQtdeOrgao']      = number_format($rsQuantitativo->getCampo('quantidade'), 4, ',', '.');
        $arQuantitativos[$inCount]['inId']             = ($inCount + 1);

        if(isset($arQuantitativosItemTemp[$rsQuantitativo->getCampo('num_item').'_'.$arQuantitativos[$inCount]['inCodLoteQ']]))
            $arQuantitativosItemTemp[$rsQuantitativo->getCampo('num_item').'_'.$arQuantitativos[$inCount]['inCodLoteQ']]=$arQuantitativosItemTemp[$rsQuantitativo->getCampo('num_item').'_'.$arQuantitativos[$inCount]['inCodLoteQ']]+$rsQuantitativo->getCampo('quantidade');
        else
            $arQuantitativosItemTemp[$rsQuantitativo->getCampo('num_item').'_'.$arQuantitativos[$inCount]['inCodLoteQ']]=$rsQuantitativo->getCampo('quantidade');

        $inCount++;
        $rsQuantitativo->proximo();
    }

    foreach($arItens as $key => $value) {
        if(isset($arQuantitativosItemTemp[$value['inNumItemLote'].'_'.$value['stCodigoLote']]))
            $arItens[$key]['nuQtdeAderida'] = number_format($arQuantitativosItemTemp[$value['inNumItemLote'].'_'.$value['stCodigoLote']], 4, ',', '.');
        else
            $arItens[$key]['nuQtdeAderida'] = '0,0000';
    }

    Sessao::write('arItens', $arItens);
    Sessao::write('arOrgaoItemQuantitativos', $arQuantitativos);
    
    $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
    $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_registro_precos'   , $request->get('inNroRegistroPrecos'));
    $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio'                , $request->get('stExercicioRegistroPrecos'));
    $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
    $obTTCEMGEmpenhoRegistroPrecos->setDado('interno'                  , $request->get('boInterno'));
    $obTTCEMGEmpenhoRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('numcgmGerenciador'));
    $obTTCEMGEmpenhoRegistroPrecos->recuperaPorChave($rsEmpenho);

    $arEmpenhos = array();
    $inCount = 0;

    $obErro = new Erro();
    $obTEmpenhoEmpenho = new TEmpenhoEmpenho();
    # Carrega os empenhos para alteração
    while (!($rsEmpenho->eof())) {
        if ( $rsEmpenho->getCampo('exercicio_empenho') == Sessao::getExercicio() ) {
            $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor";
            $obTEmpenhoEmpenho->setDado( "tribunal", "TCEMG");
            $obTEmpenhoEmpenho->setDado( "exercicio", $rsEmpenho->getCampo('exercicio_empenho') );
            $stFiltro  = " AND tabela.exercicio = '".$rsEmpenho->getCampo('exercicio_empenho')."' ";
            $stFiltro .= " AND tabela.cod_entidade IN (".$rsEmpenho->getCampo('cod_entidade')." ) ";
            $stFiltro .= " AND tabela.cod_empenho = ".$rsEmpenho->getCampo('cod_empenho')." ";

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
            $stOrder = ($stOrder) ? $stOrder : "tabela.cod_empenho";
            $obErro = $obTEmpenhoEmpenho->recuperaConsultaEmpenho( $rsLista, $stFiltro, $stOrder, $boTransacao );
        } else {
            $stOrder = "tabela.cod_entidade, tabela.cod_empenho, tabela.nom_fornecedor";
            $obTEmpenhoEmpenho->setDado( "tribunal", "TCEMG");
            $obTEmpenhoEmpenho->setDado( "exercicio", $rsEmpenho->getCampo('exercicio_empenho') );
            $stFiltro  = " AND tabela.exercicio = '".$rsEmpenho->getCampo('exercicio_empenho')."' ";
            $stFiltro .= " AND tabela.cod_entidade IN (".$rsEmpenho->getCampo('cod_entidade')." ) ";
            $stFiltro .= " AND tabela.cod_empenho = ".$rsEmpenho->getCampo('cod_empenho')." ";

            $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 4, strlen($stFiltro)) : "";
            $stOrder  = ($stOrder) ? $stOrder : "tabela.cod_empenho";
            $obErro   = $obTEmpenhoEmpenho->recuperaRestosConsultaEmpenho( $rsLista, $stFiltro, $stOrder, $boTransacao );
        }

        $arEmpenhos[$inCount]['cod_entidade']   = $rsLista->getCampo('cod_entidade');
        $arEmpenhos[$inCount]['exercicio']      = $rsLista->getCampo('exercicio');
        $arEmpenhos[$inCount]['cod_empenho']    = $rsLista->getCampo('cod_empenho');
        $arEmpenhos[$inCount]['nom_fornecedor'] = $rsLista->getCampo('nom_fornecedor');
        $arEmpenhos[$inCount]['vl_empenhado']   = $rsLista->getCampo('vl_empenhado');
        $arEmpenhos[$inCount]['dt_empenho']     = $rsLista->getCampo('dt_empenho');
        $arEmpenhos[$inCount]['inId'] = ($inCount + 1);

        $inCount++;
        $rsEmpenho->proximo();
    }

    Sessao::write('arEmpenhos', $arEmpenhos);
}

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setId   ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName( "stExercicio" );
$obHdnExercicio->setId( "stExercicio" );
$obHdnExercicio->setValue( Sessao::getExercicio() );

$obHdnResponsavelOrgao = new Hidden;
$obHdnResponsavelOrgao->setName( "boResponsavelOrgao" );
$obHdnResponsavelOrgao->setId( "boResponsavelOrgao" );
if($boResponsavelOrgao)
    $obHdnResponsavelOrgao->setValue( 'false' );
else
    $obHdnResponsavelOrgao->setValue( 'true' );

# Entidade Principal
$obITextBoxSelectEntidade = new ITextBoxSelectEntidadeGeral();
$obITextBoxSelectEntidade->obTextBox->setId('inCodEntidade');
$obITextBoxSelectEntidade->obTextBox->setName('inCodEntidade');
$obITextBoxSelectEntidade->obSelect->setName('stNomEntidade');
$obITextBoxSelectEntidade->obSelect->setId('stNomEntidade');
$obITextBoxSelectEntidade->setObrigatorio(true);
$obITextBoxSelectEntidade->setCodEntidade($inCodEntidade);
$obITextBoxSelectEntidade->obTextBox->obEvento->setOnChange("jQuery('#stCodigoProcesso').val('');ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value, 'carregaLicitacao');");
$obITextBoxSelectEntidade->obSelect->obEvento->setOnChange("jQuery('#stCodigoProcesso').val('');ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value, 'carregaLicitacao');");

if ($stAcao == 'alterar')
    $obITextBoxSelectEntidade->setLabel(true);

$obTxtExercicioRegistroPreco = new TextBox();
$obTxtExercicioRegistroPreco->setName('stExercicioRegistroPreco');
$obTxtExercicioRegistroPreco->setId('stExercicioRegistroPreco');
$obTxtExercicioRegistroPreco->setRotulo('Exercício');
$obTxtExercicioRegistroPreco->setMaxLength(4);
$obTxtExercicioRegistroPreco->setSize(5);
$obTxtExercicioRegistroPreco->setNull(false);
$obTxtExercicioRegistroPreco->setInteiro(true);
$obTxtExercicioRegistroPreco->setValue( ($stExercicioRegistroPreco != "") ? $stExercicioRegistroPreco : Sessao::getExercicio() );
$obTxtExercicioRegistroPreco->obEvento->setOnChange("jQuery('#stCodigoProcesso').val('');ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioProcessoLicitacao='+frm.stExercicioProcessoLicitacao.value+'&stExercicioRegistroPreco='+frm.stExercicioRegistroPreco.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value, 'verificaExercicioLicitacao');");

if ($stAcao == 'alterar')
    $obTxtExercicioRegistroPreco->setLabel(true);

$obTxtCodigoProcesso = new TextBox();
$obTxtCodigoProcesso->setName('stCodigoProcesso');
$obTxtCodigoProcesso->setId('stCodigoProcesso');
$obTxtCodigoProcesso->setRotulo('Nro. do Processo de Registro de Preços');
$obTxtCodigoProcesso->setTitle('Número do processo de Registro de Preços.');
$obTxtCodigoProcesso->setMaxLength(12);
$obTxtCodigoProcesso->setNull(false);
$obTxtCodigoProcesso->setInteiro(true);
$obTxtCodigoProcesso->setValue( $stCodigoRegistroPrecos );
$obTxtCodigoProcesso->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodEntidade='+jQuery('#inCodEntidade').val()+'&stNumProcesso='+jQuery('#stCodigoProcesso').val()+'&stExercicioRegistroPreco='+jQuery('#stExercicioRegistroPreco').val(),'validaNroProcesso');");
if ($stAcao == 'alterar')
    $obTxtCodigoProcesso->setLabel(true);

if ( $stAcao == 'incluir' ) {
    $obRdTipoRegistroPrecosSim = new Radio();
    $obRdTipoRegistroPrecosSim->setRotulo("Tipo de Registro de Preço.");
    $obRdTipoRegistroPrecosSim->setId("boTipoRegPreco");
    $obRdTipoRegistroPrecosSim->setName("boTipoRegPreco");
    $obRdTipoRegistroPrecosSim->setLabel("Interno");
    $obRdTipoRegistroPrecosSim->setValue("true");
    $obRdTipoRegistroPrecosSim->setNull(false);
    
    $obRdTipoRegistroPrecosNao = new Radio();
    $obRdTipoRegistroPrecosNao->setId("boTipoRegPreco");
    $obRdTipoRegistroPrecosNao->setName("boTipoRegPreco");
    $obRdTipoRegistroPrecosNao->setLabel("Externo");
    $obRdTipoRegistroPrecosNao->setValue("false");
    $obRdTipoRegistroPrecosNao->setNull(false);
} else {
    $obHdnTipoRegistroPrecos = new Hidden();
    $obHdnTipoRegistroPrecos->setId('boTipoRegPreco');
    $obHdnTipoRegistroPrecos->setName('boTipoRegPreco');
    $obHdnTipoRegistroPrecos->setValue($boTipoRegistroPrecos);
    
    $obLblTipoRegistroPrecos = new Label();
    $obLblTipoRegistroPrecos->setRotulo("Tipo de Registro de Preço.");
    $obLblTipoRegistroPrecos->setId('stLblRegPreco');
    $obLblTipoRegistroPrecos->setName('stLblRegPreco');
    $obLblTipoRegistroPrecos->setValue( $boTipoRegistroPrecos ? '&nbsp;Interno' : '&nbsp;Externo');
}
$obDtAbertura = new Data(); 
$obDtAbertura->setName('dtAberturaProcesso');
$obDtAbertura->setId('dtAberturaProcesso');
$obDtAbertura->setTitle('Data de abertura do processo de registro de preços.');
$obDtAbertura->setRotulo('Data de abertura do processo');
$obDtAbertura->setNull(false);
$obDtAbertura->setValue( $dtAberturaRegistroPrecos );

$obBscOrgaoGerenciador = new BuscaInner;
$obBscOrgaoGerenciador->setRotulo( "CGM do Orgão Gerenciador" );
$obBscOrgaoGerenciador->setTitle( "Informe o código CGM do Orgão Gerenciador" );
$obBscOrgaoGerenciador->setNull( false );
$obBscOrgaoGerenciador->setId( "inNomOrgaoGerenciador" );
$obBscOrgaoGerenciador->setValue($stNomGerenciador);
$obBscOrgaoGerenciador->obCampoCod->setName("inNumOrgaoGerenciador");
$obBscOrgaoGerenciador->obCampoCod->setId("inNumOrgaoGerenciador");
$obBscOrgaoGerenciador->obCampoCod->setValue( $inNumGerenciador );
$stParametrosAbrePopUp = Sessao::getId()."&stCtrl=buscaOrgaoGerenciador&stTabelaVinculo=sw_cgm_pessoa_juridica&stCampoVinculo=numcgm";
$stParametrosOnBlur = "&stTabelaVinculo=sw_cgm_pessoa_juridica&stCampoVinculo=numcgm&inNumOrgaoGerenciador='+this.value";
$obBscOrgaoGerenciador->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId().$stParametrosOnBlur.",'buscaOrgaoGerenciador');");
$obBscOrgaoGerenciador->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumOrgaoGerenciador','inNomOrgaoGerenciador','orgaoGerenciador','".$stParametrosAbrePopUp."','800','550');" );

if ($stAcao == 'alterar')
    $obBscOrgaoGerenciador->setLabel(true);

$obTxtNumeroProcessoLicitacao = new TextBox();
$obTxtNumeroProcessoLicitacao->setName     ( 'stNroProcessoLicitacao'   );
$obTxtNumeroProcessoLicitacao->setId       ( 'stNroProcessoLicitacao'   );
$obTxtNumeroProcessoLicitacao->setRotulo   ( 'Nro. do Processo de Licitação');
$obTxtNumeroProcessoLicitacao->setTitle    ( 'Número sequencial do processo cadastrado no órgão gerenciador do registro de preços por exercício.' );
$obTxtNumeroProcessoLicitacao->setMaxLength(12);
$obTxtNumeroProcessoLicitacao->setCaracteresAceitos ( '[0-9.]' );
$obTxtNumeroProcessoLicitacao->setNull(false);
$obTxtNumeroProcessoLicitacao->setValue( $stCodigoProcessoLicitacao );

$obTxtExercicioProcessoLicitacao = new TextBox();
$obTxtExercicioProcessoLicitacao->setName('stExercicioProcessoLicitacao');
$obTxtExercicioProcessoLicitacao->setId('stExercicioProcessoLicitacao');
$obTxtExercicioProcessoLicitacao->setRotulo('Exercício do Processo de Licitação');
$obTxtExercicioProcessoLicitacao->setMaxLength(4);
$obTxtExercicioProcessoLicitacao->setSize(5);
$obTxtExercicioProcessoLicitacao->setNull(false);
$obTxtExercicioProcessoLicitacao->setValue( ($stExercicioProcessoLicitacao != "") ? $stExercicioProcessoLicitacao : Sessao::getExercicio() );
$obTxtExercicioProcessoLicitacao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioProcessoLicitacao='+frm.stExercicioProcessoLicitacao.value+'&stExercicioRegistroPreco='+frm.stExercicioRegistroPreco.value, 'verificaExercicioLicitacao');");

$obRadioCodigoModalidadeLicitacaoConcorrencia = new Radio();
$obRadioCodigoModalidadeLicitacaoConcorrencia->setRotulo('Modalidade da Licitação');
$obRadioCodigoModalidadeLicitacaoConcorrencia->setTitle('Somente os Municípios com população inferior a cinquenta mil habitantes devem preencher este campo.');
$obRadioCodigoModalidadeLicitacaoConcorrencia->setName('inCodModalidadeLicitacao');
$obRadioCodigoModalidadeLicitacaoConcorrencia->setLabel("Concorrência");
$obRadioCodigoModalidadeLicitacaoConcorrencia->setValue("1");
$obRadioCodigoModalidadeLicitacaoConcorrencia->setNull(false);
$obRadioCodigoModalidadeLicitacaoConcorrencia->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioProcessoLicitacao=' +frm.stExercicioProcessoLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$inCodLicitacao."', 'carregaLicitacao');");

if($inCodigoModalidadeLicitacao == 3)
    $inCodigoModalidadeLicitacao = 1;
elseif($inCodigoModalidadeLicitacao == 6 || $inCodigoModalidadeLicitacao == 7)
    $inCodigoModalidadeLicitacao = 2;    

if ( $inCodigoModalidadeLicitacao == 1)
    $obRadioCodigoModalidadeLicitacaoConcorrencia->setChecked(true);

$obRadioCodigoModalidadeLicitacaoPregao = new Radio;
$obRadioCodigoModalidadeLicitacaoPregao->setName('inCodModalidadeLicitacao');
$obRadioCodigoModalidadeLicitacaoPregao->setLabel("Pregão");
$obRadioCodigoModalidadeLicitacaoPregao->setValue("2");
$obRadioCodigoModalidadeLicitacaoPregao->setNull(false);
$obRadioCodigoModalidadeLicitacaoPregao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioProcessoLicitacao=' +frm.stExercicioProcessoLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodModalidade='+frm.inCodModalidade.value+'&stAcao=".$stAcao."&inCodLicitacao=".$inCodLicitacao."', 'carregaLicitacao');");

if ( $inCodigoModalidadeLicitacao == 2)
    $obRadioCodigoModalidadeLicitacaoPregao->setChecked(true);

$obTxtNumeroModalidade = new TextBox();
$obTxtNumeroModalidade->setName('stNroModalidade');
$obTxtNumeroModalidade->setId('stNroModalidade');
$obTxtNumeroModalidade->setRotulo('Nro. da Modalidade');
$obTxtNumeroModalidade->setTitle('Número sequencial da Modalidade por exercício.');
$obTxtNumeroModalidade->setMaxLength(10);
$obTxtNumeroModalidade->setNull(false);
$obTxtNumeroModalidade->setValue( $inNumeroModalidade );

$obDtAtaRegistroPreco = new Data(); 
$obDtAtaRegistroPreco->setName('dtAtaRegistroPreco');
$obDtAtaRegistroPreco->setId('dtAtaRegistroPreco');
$obDtAtaRegistroPreco->setTitle('Data da Ata do Registro de Preços');
$obDtAtaRegistroPreco->setRotulo('Data da Ata');
$obDtAtaRegistroPreco->setNull(false);
$obDtAtaRegistroPreco->setValue( $dtAtaRegistroPreco );

$obDtValidadeAtaRegistroPreco = new Data(); 
$obDtValidadeAtaRegistroPreco->setName('dtValidadeAtaRegistroPreco');
$obDtValidadeAtaRegistroPreco->setId('dtValidadeAtaRegistroPreco');
$obDtValidadeAtaRegistroPreco->setTitle('Data de Validade da Ata do Registro de Preços.');
$obDtValidadeAtaRegistroPreco->setRotulo('Data de Validade da Ata');
$obDtValidadeAtaRegistroPreco->setNull(false);
$obDtValidadeAtaRegistroPreco->setValue( $dtAtaRegistroPrecoValidade );

$obTxtAreaObjetoAdesao = new TextArea();
$obTxtAreaObjetoAdesao->setName('txtAreaObjeto');
$obTxtAreaObjetoAdesao->setId('txtAreaObjeto');
$obTxtAreaObjetoAdesao->setRotulo('Objeto');
$obTxtAreaObjetoAdesao->setTitle('Objeto do Registro de Preço.');
$obTxtAreaObjetoAdesao->setMaxCaracteres(500);
$obTxtAreaObjetoAdesao->setNull(false);
$obTxtAreaObjetoAdesao->setValue( $txtObjeto );

$obBscCGMResponsavel = new BuscaInner;
$obBscCGMResponsavel->setRotulo( "CGM Responsável pelo Detalhamento" );
$obBscCGMResponsavel->setTitle( "Informe o código do CGM responsável pelo processo" );
$obBscCGMResponsavel->setNull( false );
$obBscCGMResponsavel->setId( "inNomCGMResponsavel" );
$obBscCGMResponsavel->setValue( $stNomCGMResponsavel );
$obBscCGMResponsavel->obCampoCod->setName("inNumCGMResponsavel");
$obBscCGMResponsavel->obCampoCod->setId("inNumCGMResponsavel");
$obBscCGMResponsavel->obCampoCod->setValue( $inNumCGMResponsavel );
$obBscCGMResponsavel->obCampoCod->obEvento->setOnBlur("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inNumCGMResponsavel='+this.value,'buscaCGMResponsavel');");
$obBscCGMResponsavel->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCGMResponsavel','inNomCGMResponsavel','fisica','".Sessao::getId()."&stCtrl=buscaCGMResponsavel','800','550');" );

if ( $stAcao == 'incluir' ) {
    $obDescontoTabelaSim = new Radio();
    $obDescontoTabelaSim->setName('inDescontoTabela');
    $obDescontoTabelaSim->setId('inDescontoTabelaSim');
    $obDescontoTabelaSim->setRotulo('Desconto Tabela');
    $obDescontoTabelaSim->setTitle('Informar se foi utilizado como critério de adjudicação a oferta de desconto sobre tabela de preços praticados no mercado.');
    $obDescontoTabelaSim->setLabel('Sim');
    $obDescontoTabelaSim->setValue(1);
    $obDescontoTabelaSim->setNull(false);
    if ( $inDescontoTabela == 1)
        $obDescontoTabelaSim->setChecked(true);    

    $obDescontoTabelaSim->obEvento->setOnClick("if (jQuery('#nuPercentualLote')) { jQuery('#nuPercentualLote').removeAttr('disabled'); }");
    
    $obDescontoTabelaNao = new Radio();
    $obDescontoTabelaNao->setName('inDescontoTabela');
    $obDescontoTabelaNao->setId('inDescontoTabelaNao');
    $obDescontoTabelaNao->setLabel('Não');
    $obDescontoTabelaNao->setValue(2);
    $obDescontoTabelaNao->setNull(false);
    if ( $inDescontoTabela == 2)
        $obDescontoTabelaNao->setChecked(true);    

    $obDescontoTabelaNao->obEvento->setOnClick("jQuery('#nuPercentualLote').attr('disabled', 'disabled'); jQuery('#nuPercentualItem').attr('disabled', 'disabled');");
    
    $obProcessoPorLoteSim = new Radio();
    $obProcessoPorLoteSim->setName('inProcessoPorLote');
    $obProcessoPorLoteSim->setId('inProcessoPorLoteSim');
    $obProcessoPorLoteSim->setRotulo('Processo por Lote');
    $obProcessoPorLoteSim->setTitle('Informar se o processo foi realizado por lote.');
    $obProcessoPorLoteSim->setLabel('Sim');
    $obProcessoPorLoteSim->setValue('1');
    $obProcessoPorLoteSim->setNull(false);
    if ( $inProcessoLote == 1)
        $obProcessoPorLoteSim->setChecked(true);    

    $obProcessoPorLoteSim->obEvento->setOnClick("montaParametrosGET('montaFormLote', 'stCtrl'); jQuery('#nuPercentualItem').attr('disabled', 'disabled'); ");
    
    $obProcessoPorLoteNao = new Radio();
    $obProcessoPorLoteNao->setName('inProcessoPorLote');
    $obProcessoPorLoteNao->setId('inProcessoPorLoteNao');
    $obProcessoPorLoteNao->setLabel('Não');
    $obProcessoPorLoteNao->setValue('2');
    $obProcessoPorLoteNao->setNull(false);
    if ( $inProcessoLote == 2)
        $obProcessoPorLoteNao->setChecked(true);    

    $obProcessoPorLoteNao->obEvento->setOnClick("jQuery('#spnLote').html('');\n jQuery('#spnLoteQuantitativo').html('');\n if (jQuery('#inDescontoTabela:checked').val() == 1) { jQuery('#nuPercentualItem').removeAttr('disabled'); }");
} else {
    # Caso o Registro de Preço já tenha desconto em tabela, não poderá ser alterado.
    $obHdnDescontoTabela = new Hidden;
    $obHdnDescontoTabela->setName  ( "inDescontoTabela" );
    $obHdnDescontoTabela->setId    ( "inDescontoTabela" );
    $obHdnDescontoTabela->setValue ( $inDescontoTabela  );
    
    $obLblDescontoTabela = new Label;
    $obLblDescontoTabela->setName   ( "stLabelDescontoTabela" );
    $obLblDescontoTabela->setId     ( "stLabelDescontoTabela" );
    $obLblDescontoTabela->setRotulo ( "Desconto Tabela"  );
    $obLblDescontoTabela->setValue  ( ($inDescontoTabela == 1) ? "&nbsp;Sim" : "&nbsp;Não" );

    # Caso o Registro de Preço seja por lote e já tenha item vinculado, não poderá ser alterado.
    $obHdnProcessoPorLote = new Hidden;
    $obHdnProcessoPorLote->setName  ( "inProcessoPorLote" );
    $obHdnProcessoPorLote->setId    ( "inProcessoPorLote" );
    $obHdnProcessoPorLote->setValue ( $inProcessoLote  );
    
    $obLblProcessoPorLote = new Label;
    $obLblProcessoPorLote->setName   ( "stLabelProcessoPorLote" );
    $obLblProcessoPorLote->setId     ( "stLabelProcessoPorLote" );
    $obLblProcessoPorLote->setRotulo ( "Processo por Lote"  );
    $obLblProcessoPorLote->setValue  ( ($inProcessoLote == 1) ? "&nbsp;Sim" : "&nbsp;Não" );
}

//Consulta para Buscar Modalidades Licitação
$obComprasModalidade = new TComprasModalidade();
$rsModalidadeLicit = new RecordSet;
$stFiltro = " WHERE cod_modalidade IN (3,6,7)  ";
$stOrdem  = " ORDER BY cod_modalidade, descricao ";
$obComprasModalidade->recuperaTodos($rsModalidadeLicit, $stFiltro, $stOrdem);

//Montando Licitação Urbem
$obTxtExercicioLicitacao = new TextBox();
$obTxtExercicioLicitacao->setName       ( 'stExercicioLicitacao' );
$obTxtExercicioLicitacao->setId         ( 'stExercicioLicitacao' );
$obTxtExercicioLicitacao->setRotulo     ( 'Exercício' );
$obTxtExercicioLicitacao->setMaxLength  ( 4 );
$obTxtExercicioLicitacao->setSize       ( 5 );
$obTxtExercicioLicitacao->setNull       ( true );
$obTxtExercicioLicitacao->setValue      ( ($stExercicioLicitacao != "") ? $stExercicioLicitacao : Sessao::getExercicio() );
$obTxtExercicioLicitacao->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioRegistroPreco='+frm.stExercicioRegistroPreco.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value+'&stAcao=".$stAcao."&inCodLicitacao=".$inCodLicitacao."', 'verificaExercicioLicitacao');");

$obISelectModalidade = new Select();
$obISelectModalidade->setName       ( 'inCodModalidade' );
$obISelectModalidade->setId         ( 'inCodModalidade' );
$obISelectModalidade->setRotulo     ( 'Modalidade' );
$obISelectModalidade->setTitle      ( 'Selecione a Modalidade.' );
$obISelectModalidade->setCampoID    ( 'cod_modalidade' );
$obISelectModalidade->setValue      ( $inCodModalidade );
$obISelectModalidade->setCampoDesc  ( '[cod_modalidade] - [descricao]' );
$obISelectModalidade->addOption     ( '','Selecione' );
$obISelectModalidade->setNull       ( true );
$obISelectModalidade->preencheCombo ( $rsModalidadeLicit );
$obISelectModalidade->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stExercicioRegistroPreco='+frm.stExercicioRegistroPreco.value+'&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade='+frm.inCodModalidade.value+'&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value+'&stAcao=".$stAcao."&inCodLicitacao=".$inCodLicitacao."', 'carregaLicitacao');");

$obISelectLicitacao = new Select();
$obISelectLicitacao->setName    ( 'inCodLicitacao' );
$obISelectLicitacao->setId      ( 'inCodLicitacao' );
$obISelectLicitacao->setRotulo  ( 'Licitação' );
$obISelectLicitacao->setTitle   ( 'Selecione a Licitação.' );
$obISelectLicitacao->addOption  ( '','Selecione' );
$obISelectLicitacao->setNull    ( true );
$obISelectLicitacao->setValue   ( $inCodLicitacao );

# Inclui formulário de Orgãos
include_once 'FMManterRegistroPrecoOrgaos.php';

# Inclui formulário de itens
include_once 'FMManterRegistroPrecoItem.php';

# Incluir formulaŕio de Quantitativos por Orgão
include_once 'FMManterRegistroPrecoQuantitativos.php';

# Inclui formulário de empenhos
include_once 'FMManterRegistroPrecoEmpenho.php';

$obFormulario = new FormularioAbas;
$obFormulario->addForm( $obForm );

# Elementos da Aba Detalhes
$obFormulario->addAba ( "Detalhamento" );
$obFormulario->addTitulo( "Registro de Preços" );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnExercicio );
$obFormulario->addHidden( $obHdnResponsavelOrgao );
$obFormulario->addComponente( $obITextBoxSelectEntidade );
$obFormulario->addComponente( $obTxtExercicioRegistroPreco );
$obFormulario->addComponente( $obTxtCodigoProcesso );
if ( $stAcao == "incluir") {
    $obFormulario->agrupaComponentes (array($obRdTipoRegistroPrecosSim,$obRdTipoRegistroPrecosNao));
} else {
    $obFormulario->addHidden( $obHdnTipoRegistroPrecos );
    $obFormulario->addComponente( $obLblTipoRegistroPrecos );
}
$obFormulario->addComponente( $obDtAbertura );
$obFormulario->addComponente( $obBscOrgaoGerenciador );
$obFormulario->addComponente( $obTxtExercicioProcessoLicitacao );
$obFormulario->agrupaComponentes (array($obRadioCodigoModalidadeLicitacaoConcorrencia, $obRadioCodigoModalidadeLicitacaoPregao));
$obFormulario->addComponente( $obTxtNumeroModalidade );
$obFormulario->addComponente( $obTxtNumeroProcessoLicitacao );
$obFormulario->addComponente( $obDtAtaRegistroPreco );
$obFormulario->addComponente( $obDtValidadeAtaRegistroPreco );
$obFormulario->addComponente( $obTxtAreaObjetoAdesao );
$obFormulario->addComponente( $obBscCGMResponsavel );
if ($stAcao == "incluir") {
    $obFormulario->agrupaComponentes( array($obDescontoTabelaSim, $obDescontoTabelaNao) );
    $obFormulario->agrupaComponentes( array($obProcessoPorLoteSim, $obProcessoPorLoteNao) );
} else {
    $obFormulario->addHidden( $obHdnDescontoTabela );
    $obFormulario->addHidden( $obHdnProcessoPorLote );
    $obFormulario->addComponente ( $obLblDescontoTabela ) ;
    $obFormulario->addComponente ( $obLblProcessoPorLote ) ;
}

$obFormulario->addTitulo( "Licitação" );
$obFormulario->addComponente ( $obTxtExercicioLicitacao );
$obFormulario->addComponente ( $obISelectModalidade );
$obFormulario->addComponente ( $obISelectLicitacao );

# Elementos da Aba Orgãos
$obFormulario->addAba("Orgãos");
$obFormulario->addTitulo("Orgãos Participantes ou Não do Registro de Preço.");
$obFormulario->addHidden($obHdnIdOrgao);
$obFormulario->addComponente($obTxtExercicio);
$obIMontaUnidadeOrcamentaria->geraFormulario( $obFormulario );
$obFormulario->agrupaComponentes (array($obRdoGerenciadorSim,$obRdoGerenciadorNao));
$obFormulario->agrupaComponentes (array($obRadioNaturezaProcedimentoParticipante, $obRadioNaturezaProcedimentoNaoParticipante));
$obFormulario->addComponente( $obIPopUpCGMResponsavel );
$obFormulario->addComponente($obTxtCodigoProcessoAdesao);
$obFormulario->addComponente($obTxtExercicioProcessoAdesao);
$obFormulario->addComponente($obDtAdesao);
$obFormulario->addComponente($obDtPublicacaoAvisoIntencao);
$obFormulario->agrupaComponentes( array($obBtnSalvarOrgao, $obBtnLimparOrgao) );
$obFormulario->addSpan($obSpanListaOrgao);

# Elementos da Aba Itens
$obFormulario->addAba  ( "Itens" );
$obFormulario->addSpan ( $obSpnLote );
$obFormulario->addTitulo( "Dados do Item" );
$obFormulario->addHidden($obHdnIdItem);
$obFormulario->addComponente( $obDtContacao );
$obFormulario->addSpan ( $obSpnItemBuscaInner );
$obFormulario->addComponente($obIntNumItem);
$obMontaQuantidadeValores->geraFormulario($obFormulario);
$obFormulario->addComponente( $obVlrPrecoUnitario );
$obFormulario->addComponente( $obIntQtdeLicitada );
$obFormulario->addComponente( $obIntQtdeAderida );
$obFormulario->addComponente( $obLblSaldoItem );
$obFormulario->addComponente( $obVlrPercentualItem );
$obFormulario->addComponente( $obBscCGMVencedor );
$obFormulario->addComponente( $obIntOrdemClassificacao );
$obFormulario->addSpan ( $obSpnItem );
$obFormulario->agrupaComponentes( array($obBtnSalvar, $obBtnLimpar) );
$obFormulario->addSpan( $obSpanListaItem );

# Elementos da Aba Quantitativos por Orgão
$obFormulario->addAba  ( "Quantitativos por Orgão");
$obFormulario->addTitulo( "Quantitativos por Orgão" );
$obFormulario->addHidden( $obHdnQtdeFornecida );
$obFormulario->addHidden( $obHdnCodItem );
$obFormulario->addHidden( $obHdnIdItemQ );
$obFormulario->addComponente( $obTxtExercicioQ );
$obFormulario->addComponente( $obSlcOrgao);
$obFormulario->addSpan( $obSpnOrgao);
$obFormulario->addSpan( $obSpnLoteQuantitativo );
$obFormulario->addComponente( $obSlcItem );
$obFormulario->addComponente( $obSlcFornecedor );
$obFormulario->addComponente( $obLblQtdePermitidaFornecedor );
$obFormulario->addComponente( $obQuantidadeOrgao );
$obFormulario->addComponente( $obLblQtdeAderidaQ );
$obFormulario->addComponente( $obLblSaldoItemQ );
$obFormulario->agrupaComponentes( array($obBtnSalvarQuantitativo, $obBtnLimparQuantitativo) );

$obFormulario->addSpan ( $spnQuantitativoOrgao );
# Elementos da Aba Empenhos
$obFormulario->addAba  ( "Empenhos ");
$obFormulario->addTitulo( "Dados do Empenho" );
$obFormulario->addComponente( $obTxtExercicioEmpenho );
$obFormulario->addComponente( $obBscEmpenho );
$obFormulario->addComponente( $obBtnIncluirEmpenho );
$obFormulario->addSpan ( $obSpnEmpenhos );

$obOk = new Ok();
$obOk->obEvento->setOnClick("ValidaRegistroPreco();");

$obLimpar = new Limpar;
$obLimpar->obEvento->setOnClick( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."', 'LimparForm');");

$obCancelar  = new Cancelar();
$stLink  = '&stAcao='.$request->get('stAcao').'&inCodEntidade='.$request->get('inCodEntidade').'&stExercicioRegistroPreco='.$request->get('stExercicioRegistroPrecos');
$stLink .= "&stCodigoProcesso=".$request->get('stCodigoProcesso')."&inCodModalidade=".$request->get('inCodModalidade')."&inCodLicitacao=".$request->get('inCodLicitacao');
$stLink .= "&stExercicioEmpenho=".$request->get('stExercicioEmpenho')."&numEmpenho=".$request->get('numEmpenho');
$obCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().$stLink."','telaPrincipal');");

if ($stAcao == 'alterar') {
    $obFormulario->defineBarra( array( $obOk, $obCancelar ) );
}else{
    $obFormulario->defineBarra( array( $obOk, $obLimpar ) );
}

$obFormulario->show();

if ($stAcao == 'alterar') {
    $stJs .= "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stAcao=".$stAcao."&stExercicioLicitacao='+frm.stExercicioLicitacao.value+'&inCodEntidade='+frm.inCodEntidade.value+'&inCodModalidade=".$inCodModalidade."&inCodLicitacao=".$inCodLicitacao."&inCodModalidadeLicitacao='+frm.inCodModalidadeLicitacao.value, 'carregaLicitacao');";
    $stJs .= preencheComboOrgaoAbaQuantitativo();
    
    $boLote = false;
    if ($inProcessoLote == 1){
        $stJs .= montaFormLote();
        $boLote = true;
    }
    $stJs .= preencheLoteOuNumItemAbaQuantitativo($boLote);
    $stJs .= montaListaOrgaos();
    $stJs .= montaListaItens();
    $stJs .= montaListaOrgaoItemQuantitativos();
    $stJs .= montaListaEmpenho();
}

SistemaLegado::executaFrameOculto($stJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>