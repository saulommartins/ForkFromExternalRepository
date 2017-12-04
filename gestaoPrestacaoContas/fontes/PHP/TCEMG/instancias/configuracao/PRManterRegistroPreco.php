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
  * Página de processamento para Configurar IDE
  * Data de criação : 07/01/2014
  * 
  * @author Analista:    Eduardo Paculski Schitz
  * @author Programador: Franver Sarmento de Moraes
  * 
  * @ignore
  * 
  * $Id: PRManterRegistroPreco.php 63765 2015-10-07 18:51:47Z michel $
  * $Date: 2015-10-07 15:51:47 -0300 (Wed, 07 Oct 2015) $
  * $Author: michel $
  * $Rev: 63765 $
  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosOrgao.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGItemRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGEmpenhoRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGLoteRegistroPrecos.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosOrgaoItem.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecosLicitacao.class.php";

// Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao', 'incluir');

$obTTCEMGRegistroPrecos = new TTCEMGRegistroPrecos();

$obErro = new Erro;
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($stAcao) {

    case 'excluir' :
        $obTTCEMGRegistroPrecosOrgaoItem = new TTCEMGRegistroPrecosOrgaoItem();
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('numero_registro_precos'    , $request->get('inNroRegistroPrecos'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_registro_precos' , $request->get('stExercicioRegistroPrecos'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_entidade'              , $request->get('inCodEntidade'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('interno'                   , $request->get('boInterno'));
        $obTTCEMGRegistroPrecosOrgaoItem->setDado('numcgm_gerenciador'        , $request->get('numcgmGerenciador'));
        $obErro = $obTTCEMGRegistroPrecosOrgaoItem->exclusao($boTransacao);
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
            $obTTCEMGItemRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
            $obTTCEMGItemRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
            $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGItemRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
            $obTTCEMGItemRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
            $obErro = $obTTCEMGItemRegistroPrecos->exclusao($boTransacao);
        }
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGRegistroPrecosOrgao = new TTCEMGRegistroPrecosOrgao();
            $obTTCEMGRegistroPrecosOrgao->setDado('numero_registro_precos'   , $request->get('inNroRegistroPrecos'));
            $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_registro_precos', $request->get('stExercicioRegistroPrecos'));
            $obTTCEMGRegistroPrecosOrgao->setDado('cod_entidade'             , $request->get('inCodEntidade'));
            $obTTCEMGRegistroPrecosOrgao->setDado('interno'                  , $request->get('boInterno'));
            $obTTCEMGRegistroPrecosOrgao->setDado('numcgm_gerenciador'       , $request->get('numcgmGerenciador'));
            $obErro = $obTTCEMGRegistroPrecosOrgao->exclusao($boTransacao);
        }
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
            $obTTCEMGLoteRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
            $obTTCEMGLoteRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
            $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGLoteRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
            $obTTCEMGLoteRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
            $obErro = $obTTCEMGLoteRegistroPrecos->exclusao($boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
            $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
            $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
            $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGEmpenhoRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
            $obTTCEMGEmpenhoRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
            $obErro = $obTTCEMGEmpenhoRegistroPrecos->exclusao($boTransacao);
        }
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGRegistroPrecosLicitacao = new TTCEMGRegistroPrecosLicitacao();
            $obTTCEMGRegistroPrecosLicitacao->setDado('cod_entidade'            , $request->get('inCodEntidade'));
            $obTTCEMGRegistroPrecosLicitacao->setDado('numero_registro_precos'  , $request->get('inNroRegistroPrecos'));
            $obTTCEMGRegistroPrecosLicitacao->setDado('exercicio'               , $request->get('stExercicioRegistroPrecos'));
            $obTTCEMGRegistroPrecosLicitacao->setDado('interno'                 , $request->get('boInterno'));
            $obTTCEMGRegistroPrecosLicitacao->setDado('numcgm_gerenciador'      , $request->get('numcgmGerenciador'));
            $obErro = $obTTCEMGRegistroPrecosLicitacao->exclusao($boTransacao);
        }
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGRegistroPrecos = new TTCEMGRegistroPrecos();
            $obTTCEMGRegistroPrecos->setDado('numero_registro_precos' , $request->get('inNroRegistroPrecos'));
            $obTTCEMGRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPrecos'));
            $obTTCEMGRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGRegistroPrecos->setDado('interno'                , $request->get('boInterno'));
            $obTTCEMGRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('numcgmGerenciador'));
            $obErro = $obTTCEMGRegistroPrecos->exclusao($boTransacao);
        }

        if (!$obErro->ocorreu()) {
            $stMsg = 'Registro de Preço - '.$request->get('inNroRegistroPrecos')."/".$request->get('stExercicioRegistroPrecos');
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&inCodEntidade=".$request->get('inCodEntidade')."&stAcao=".$stAcao,$stMsg,"excluir","excluir", Sessao::getId(), "../");
        }
    break;

    default:
        $rsRegistroPrecos = new RecordSet();

        $arItens = Sessao::read('arItens');
        $arEmpenhos = Sessao::read('arEmpenhos');
        $arOrgaos = Sessao::read('arOrgaos');
        $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
        
        if (!$obErro->ocorreu()) {
            $obTTCEMGRegistroPrecos->setDado('numero_registro_precos' , $request->get('stCodigoProcesso'));
            $obTTCEMGRegistroPrecos->setDado('exercicio'              , $request->get('stExercicioRegistroPreco'));
            $obTTCEMGRegistroPrecos->setDado('cod_entidade'           , $request->get('inCodEntidade'));
            $obTTCEMGRegistroPrecos->setDado('interno'                , $request->get('boTipoRegPreco'));
            $obTTCEMGRegistroPrecos->setDado('numcgm_gerenciador'     , $request->get('inNumOrgaoGerenciador'));
            $obErro = $obTTCEMGRegistroPrecos->recuperaPorChave( $rsRegistroPrecos, $boTransacao );

            if (!$obErro->ocorreu()) {
                $obTTCEMGRegistroPrecos->setDado('data_abertura_registro_precos'    , $request->get('dtAberturaProcesso'));
                $obTTCEMGRegistroPrecos->setDado('exercicio_licitacao'              , $request->get('stExercicioProcessoLicitacao'));
                $obTTCEMGRegistroPrecos->setDado('numero_processo_licitacao'        , $request->get('stNroProcessoLicitacao'));
                $obTTCEMGRegistroPrecos->setDado('codigo_modalidade_licitacao'      , $request->get('inCodModalidadeLicitacao'));
                $obTTCEMGRegistroPrecos->setDado('numero_modalidade'                , $request->get('stNroModalidade'));
                $obTTCEMGRegistroPrecos->setDado('data_ata_registro_preco'          , $request->get('dtAtaRegistroPreco'));
                $obTTCEMGRegistroPrecos->setDado('data_ata_registro_preco_validade' , $request->get('dtValidadeAtaRegistroPreco'));
                $obTTCEMGRegistroPrecos->setDado('objeto'                           , $request->get('txtAreaObjeto'));
                $obTTCEMGRegistroPrecos->setDado('cgm_responsavel'                  , $request->get('inNumCGMResponsavel'));
                $obTTCEMGRegistroPrecos->setDado('desconto_tabela'                  , $request->get('inDescontoTabela'));
                $obTTCEMGRegistroPrecos->setDado('processo_lote'                    , $request->get('inProcessoPorLote'));
    
                if ($rsRegistroPrecos->getNumLinhas() < 0) {
                    $obErro = $obTTCEMGRegistroPrecos->inclusao($boTransacao);
                } else {
                    $obErro = $obTTCEMGRegistroPrecos->alteracao($boTransacao);
                }
            }

            if (!$obErro->ocorreu()) {
                $obTTCEMGRegistroPrecosLicitacao = new TTCEMGRegistroPrecosLicitacao();
                $obTTCEMGRegistroPrecosLicitacao->setDado('cod_entidade'            , $request->get('inCodEntidade'));
                $obTTCEMGRegistroPrecosLicitacao->setDado('numero_registro_precos'  , $request->get('stCodigoProcesso'));
                $obTTCEMGRegistroPrecosLicitacao->setDado('exercicio'               , $request->get('stExercicioRegistroPreco'));
                $obTTCEMGRegistroPrecosLicitacao->setDado('interno'                 , $request->get('boTipoRegPreco'));
                $obTTCEMGRegistroPrecosLicitacao->setDado('numcgm_gerenciador'      , $request->get('inNumOrgaoGerenciador'));

                $obErro = $obTTCEMGRegistroPrecosLicitacao->exclusao($boTransacao);

                if (!$obErro->ocorreu()) {
                    if(($request->get('inCodLicitacao')!='')&&($request->get('inCodModalidade')!='')&&($request->get('inCodEntidade')!='')&&($request->get('stExercicioLicitacao')!='')){
                        $inCodLicitacao = explode('/',$request->get('inCodLicitacao'));
                        $obTTCEMGRegistroPrecosLicitacao->setDado('cod_licitacao'           , $inCodLicitacao[0]);
                        $obTTCEMGRegistroPrecosLicitacao->setDado('cod_modalidade'          , $request->get('inCodModalidade'));
                        $obTTCEMGRegistroPrecosLicitacao->setDado('cod_entidade_licitacao'  , $request->get('inCodEntidade'));
                        $obTTCEMGRegistroPrecosLicitacao->setDado('exercicio_licitacao'     , $request->get('stExercicioLicitacao'));

                        $obErro = $obTTCEMGRegistroPrecosLicitacao->inclusao($boTransacao);
                    }
                }
            }

            # Exclui sempre e inclui se necessário, lote e item.
            if (!$obErro->ocorreu()) {
                # Exclui todos os Quantitativos por Orgãos para o tipo de registro de preço.
                $obTTCEMGRegistroPrecosOrgaoItem = new TTCEMGRegistroPrecosOrgaoItem();
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('numero_registro_precos'   , $request->get('stCodigoProcesso'));
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_registro_precos', $request->get('stExercicioRegistroPreco'));
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('interno'                  , $request->get('boTipoRegPreco'));
                $obTTCEMGRegistroPrecosOrgaoItem->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                $obErro = $obTTCEMGRegistroPrecosOrgaoItem->exclusao($boTransacao);

                if (!$obErro->ocorreu()) {
                    # Exclui todos os Orgãos e as Unidades para o tipo de registro de preço.
                    $obTTCEMGRegistroPrecosOrgao = new TTCEMGRegistroPrecosOrgao();
                    $obTTCEMGRegistroPrecosOrgao->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                    $obTTCEMGRegistroPrecosOrgao->setDado('numero_registro_precos'   , $request->get('stCodigoProcesso'));
                    $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_registro_precos', $request->get('stExercicioRegistroPreco'));
                    $obTTCEMGRegistroPrecosOrgao->setDado('interno'                  , $request->get('boTipoRegPreco'));
                    $obTTCEMGRegistroPrecosOrgao->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                    $obErro = $obTTCEMGRegistroPrecosOrgao->exclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    # Exclui todos os Itens para o tipo de registro de preço.
                    $obTTCEMGItemRegistroPrecos = new TTCEMGItemRegistroPrecos();
                    $obTTCEMGItemRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                    $obTTCEMGItemRegistroPrecos->setDado('numero_registro_precos'   , $request->get('stCodigoProcesso'));
                    $obTTCEMGItemRegistroPrecos->setDado('exercicio'                , $request->get('stExercicioRegistroPreco'));
                    $obTTCEMGItemRegistroPrecos->setDado('interno'                  , $request->get('boTipoRegPreco'));
                    $obTTCEMGItemRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                    $obErro = $obTTCEMGItemRegistroPrecos->exclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    # Exclui todos os Empenhos para o tipo de registro de preço.
                    $obTTCEMGEmpenhoRegistroPrecos = new TTCEMGEmpenhoRegistroPrecos();
                    $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                    $obTTCEMGEmpenhoRegistroPrecos->setDado('numero_registro_precos'   , $request->get('stCodigoProcesso'));
                    $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio'                , $request->get('stExercicioRegistroPreco'));
                    $obTTCEMGEmpenhoRegistroPrecos->setDado('interno'                  , $request->get('boTipoRegPreco'));
                    $obTTCEMGEmpenhoRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                    $obErro = $obTTCEMGEmpenhoRegistroPrecos->exclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    # Exclui todos os Lotes para o tipo de registro de preço.
                    $obTTCEMGLoteRegistroPrecos = new TTCEMGLoteRegistroPrecos();
                    $obTTCEMGLoteRegistroPrecos->setDado('cod_entidade'             , $request->get('inCodEntidade'));
                    $obTTCEMGLoteRegistroPrecos->setDado('numero_registro_precos'   , $request->get('stCodigoProcesso'));
                    $obTTCEMGLoteRegistroPrecos->setDado('exercicio'                , $request->get('stExercicioRegistroPreco'));
                    $obTTCEMGLoteRegistroPrecos->setDado('interno'                  , $request->get('boTipoRegPreco'));
                    $obTTCEMGLoteRegistroPrecos->setDado('numcgm_gerenciador'       , $request->get('inNumOrgaoGerenciador'));
                    $obErro = $obTTCEMGLoteRegistroPrecos->exclusao($boTransacao);
                }

                if (!$obErro->ocorreu()) {
                    if (is_array($arOrgaos) && count($arOrgaos) > 0) {
                        foreach( $arOrgaos as $arOrgao ){
                            if($arOrgao['inResponsavel']!=''){
                                $arUnidadeOrcamentaria = explode('.',$arOrgao['stUnidadeOrcamentaria']);

                                $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_unidade'           ,$arOrgao['stExercicioOrgao']);
                                $obTTCEMGRegistroPrecosOrgao->setDado('num_orgao'                   ,(int)$arUnidadeOrcamentaria[0]);
                                $obTTCEMGRegistroPrecosOrgao->setDado('num_unidade'                 ,(int)$arUnidadeOrcamentaria[1]);
                                $obTTCEMGRegistroPrecosOrgao->setDado('participante'                ,($arOrgao['inNaturezaProcedimento'] == 1) ? true : false);
                                $obTTCEMGRegistroPrecosOrgao->setDado('numero_processo_adesao'      ,$arOrgao['stCodigoProcessoAdesao']);
                                $obTTCEMGRegistroPrecosOrgao->setDado('exercicio_adesao'            ,$arOrgao['stExercicioProcessoAdesao']);
                                $obTTCEMGRegistroPrecosOrgao->setDado('dt_publicacao_aviso_intencao',$arOrgao['dtPublicacaoAvisoIntencao']);
                                $obTTCEMGRegistroPrecosOrgao->setDado('dt_adesao'                   ,$arOrgao['dtAdesao']);
                                $obTTCEMGRegistroPrecosOrgao->setDado('gerenciador'                 ,($arOrgao['inOrgaoGerenciador'] == 1)? true : false );
                                $obTTCEMGRegistroPrecosOrgao->setDado('cgm_aprovacao'               ,$arOrgao['inResponsavel']);

                                $obErro = $obTTCEMGRegistroPrecosOrgao->inclusao($boTransacao);
                            }else{
                                $obErro->setDescricao('Informe o CGM do Responsável pela Aprovação do Orgão: '.$arOrgao['stMontaCodOrgaoM'].' - '.$arOrgao['stMontaCodUnidadeM']);
                            }

                            if ($obErro->ocorreu())
                                break;
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
                    if (is_array($arItens) && count($arItens) > 0) {
                        $boProcessoPorLote = $request->get('inProcessoPorLote');
                        $inDescontoTabela  = $request->get('inDescontoTabela');

                        foreach ($arItens as $item) {
                            # Cadastro de Lote quando necessário
                            $inCodLote = ((!empty($item['stCodigoLote']) && $item['stCodigoLote'] != 0) ? $item['stCodigoLote'] : 0);
                            $txtDescricaoLote = (!empty($item['txtDescricaoLote']) ? $item['txtDescricaoLote'] : '');

                            $obTTCEMGLoteRegistroPrecos->setDado('cod_lote' , $inCodLote);
                            $obErro =  $obTTCEMGLoteRegistroPrecos->recuperaPorChave( $rsLote, $boTransacao );
                            
                            if (!$obErro->ocorreu()) {
                                $obTTCEMGLoteRegistroPrecos->setDado('descricao_lote' , $txtDescricaoLote);

                                $nuPercentualLote = ($boProcessoPorLote == true) ? $item['nuPercentualLote'] : 0;
                                $obTTCEMGLoteRegistroPrecos->setDado('percentual_desconto_lote' , $nuPercentualLote);

                                if ($rsLote->getNumLinhas() > 0) {
                                    $obErro = $obTTCEMGLoteRegistroPrecos->alteracao($boTransacao);
                                } else {
                                    $obErro = $obTTCEMGLoteRegistroPrecos->inclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu()) {
                                    if ($inDescontoTabela == 2 || ($inDescontoTabela == 1 && $boProcessoPorLote == 1)) {
                                        $nuPercentualItem = 0;
                                    } else {
                                        $nuPercentualItem = $item['nuPercentualItem'];
                                    }

                                    # Cadastro dos Itens do Registro de Preço, vinculação ao lote
                                    $obTTCEMGItemRegistroPrecos->setDado('cod_lote'                       , $inCodLote);
                                    $obTTCEMGItemRegistroPrecos->setDado('cod_item'                       , $item['inCodItem']);
                                    $obTTCEMGItemRegistroPrecos->setDado('cgm_vencedor'                   , $item['inNumCGMVencedor']);
                                    $obTTCEMGItemRegistroPrecos->setDado('num_item'                       , $item['inNumItemLote']);
                                    $obTTCEMGItemRegistroPrecos->setDado('data_cotacao'                   , $item['dtCotacao']);
                                    $obTTCEMGItemRegistroPrecos->setDado('vl_cotacao_preco_unitario'      , $item['nuVlReferencia']);
                                    $obTTCEMGItemRegistroPrecos->setDado('quantidade_cotacao'             , $item['nuQuantidade']);
                                    $obTTCEMGItemRegistroPrecos->setDado('preco_unitario'                 , $item['nuVlUnitario']);
                                    $obTTCEMGItemRegistroPrecos->setDado('quantidade_licitada'            , $item['nuQtdeLicitada']);
                                    $obTTCEMGItemRegistroPrecos->setDado('quantidade_aderida'             , $item['nuQtdeAderida']);
                                    $obTTCEMGItemRegistroPrecos->setDado('percentual_desconto'            , $nuPercentualItem);
                                    $obTTCEMGItemRegistroPrecos->setDado('cgm_fornecedor'                 , $item['inNumCGMVencedor']);
                                    $obTTCEMGItemRegistroPrecos->setDado('ordem_classificacao_fornecedor' , $item['inOrdemClassifFornecedor']);
                                    $obErro = $obTTCEMGItemRegistroPrecos->inclusao($boTransacao);

                                    if (!$obErro->ocorreu() && $item['nuQtdeAderida'] > $item['nuQtdeLicitada']) {
                                        $boItemAderidaSuperior = true;
                                    }
                                }

                            }

                            if ($obErro->ocorreu())
                                break;
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
                    if (is_array($arOrgaoItemQuantitativos) && count($arOrgaoItemQuantitativos) > 0) {
                        foreach ( $arOrgaoItemQuantitativos as $arOrgaoItemQuantitativo ) {
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('exercicio_unidade',$arOrgaoItemQuantitativo['stExercicioOrgao']);
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('num_orgao'        ,$arOrgaoItemQuantitativo['inCodOrgaoQ']);
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('num_unidade'      ,$arOrgaoItemQuantitativo['inCodUnidadeQ']);
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_lote'         ,$arOrgaoItemQuantitativo['inCodLoteQ']);
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('cod_item'         ,$arOrgaoItemQuantitativo['inCodItemQ']);
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('cgm_fornecedor'   ,$arOrgaoItemQuantitativo['inCodFornecedorQ']);
                            $obTTCEMGRegistroPrecosOrgaoItem->setDado('quantidade'       ,$arOrgaoItemQuantitativo['nuQtdeOrgao']);
                            $obErro = $obTTCEMGRegistroPrecosOrgaoItem->inclusao($boTransacao);

                            if ($obErro->ocorreu())
                                break;
                        }
                    }
                }

                if (!$obErro->ocorreu()) {
                    if (is_array($arEmpenhos) && count($arEmpenhos) > 0) {
                        foreach ($arEmpenhos as $empenho) {
                            # Cadastro dos Empenhos do Registro de Preço
                            $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_empenho'          , $empenho['cod_empenho']);
                            $obTTCEMGEmpenhoRegistroPrecos->setDado('exercicio_empenho'    , $empenho['exercicio']);
                            $obTTCEMGEmpenhoRegistroPrecos->setDado('cod_entidade_empenho' , $empenho['cod_entidade']);
                            $obErro = $obTTCEMGEmpenhoRegistroPrecos->inclusao($boTransacao);

                            if ($obErro->ocorreu())
                                break;
                        }
                    }
                }
            }

            if (!$obErro->ocorreu()) {
                # Limpa o array de empenhos que está na sessão.
                Sessao::remove('arEmpenhos');
                Sessao::remove('arItens');
                Sessao::remove('arOrgaos');
                Sessao::remove('arOrgaoItemQuantitativos');

                $stMsg = "";
                if($boItemAderidaSuperior)
                    $stMsg = ". Itens foram incluidos/alterados com Quantidade Aderida superior a Quantidade Licitada.";

                $stMsg = 'Registro de Preço - '.$request->get('stCodigoProcesso').'/'.$request->get('stExercicioRegistroPreco').$stMsg;

                SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,$stMsg,"incluir","aviso", Sessao::getId(), "../");
            }
        }

    break;

}

if ($obErro->ocorreu())
    SistemaLegado::exibeAviso($obErro->getDescricao(),"n_".$stAcao,"erro");

SistemaLegado::liberaFrames(true,true);
$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCEMGRegistroPrecos );

?>