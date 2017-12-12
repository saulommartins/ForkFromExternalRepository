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
    * Página de Listagem de Plano Conta
    * Data de Criação   : 13/07/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: OCPlanoConta.php 65901 2016-06-28 14:22:28Z michel $

    * Casos de uso: uc-02.02.02,uc-02.04.09
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";
include_once CAM_GF_CONT_NEGOCIO."RContabilidadePlanoBanco.class.php";

$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;

$stCtrl = $request->get('stCtrl');
$stCtrl = (empty($stCtrl)) ? $_GET['stCtrl'] : $stCtrl;

switch( $stCtrl ) {
    case 'buscaPopup':
        $stNomCampoCod = $request->get('stNomCampoCod');
        $inCodCampoCod = $request->get($stNomCampoCod);
        $stTipoBusca   = $request->get('stTipoBusca');
        $inCodEntidade = $request->get('inCodEntidade');

        if ($inCodCampoCod != "") {
            if ($stTipoBusca == "banco") {
                $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;

                $obRContabilidadePlanoContaBanco->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
                if ($inCodEntidade)
                    $obRContabilidadePlanoContaBanco->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                $obErro = $obRContabilidadePlanoContaBanco->consultar();

                $codAgencia = $obRContabilidadePlanoContaBanco->obRMONAgencia->getCodAgencia();
                if ($codAgencia <> "") {
                    if($inCodEntidade AND ($obRContabilidadePlanoContaBanco->obROrcamentoEntidade->getCodigoEntidade() <> $inCodEntidade))
                        SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getNomConta()." - Entidade diferente da informada"),"n_incluir","erro");
                    else
                        $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
                } else
                    SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getCodPlano()." - Não é uma Conta de Banco"),"n_incluir","erro");            
            } elseif ($stTipoBusca == 'tes_pagamento') {
                include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php";
                $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;

                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%'  ";
                    $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.4.%' ))  ";
                    $stFiltro .= "\n   ) AND ";
                } else {
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' )  ";
                    $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.5.%' ) AND ";
                }
                $stFiltro .= "\n   pa.cod_plano = " .$inCodCampoCod . " AND ";

                if ($inCodEntidade)
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$inCodEntidade.") AND ";
                
                $stFiltro = " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4);
                $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';

                $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

                # Quando não encontra nenhum valor no campo.
                if ( $rsLista->getNumLinhas() <= 0 ) {
                    SistemaLegado::executaFrameOculto("jQuery('#inCodPlano', window.parent.frames['telaPrincipal'].document.frm).val('');");
                    SistemaLegado::executaFrameOculto("jQuery('#stNomConta', window.parent.frames['telaPrincipal'].document.frm).html('&nbsp;');");
                    SistemaLegado::exibeAviso(urlencode( $inCodCampoCod )." - Não é uma Conta de Banco","n_incluir","erro");
                } else
                    $stDescricao = $rsLista->getCampo ( 'nom_conta' );
            } elseif ($stTipoBusca == 'tes_pagamento_arrecadacao') {
                include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoAnalitica.class.php";
                $obTContabilidadePlanoAnalitica = new TContabilidadePlanoAnalitica;

                $stFiltro  = "\n pa.cod_plano is not null AND ";
                $stFiltro .= "\n pc.exercicio = '" . Sessao::getExercicio() . "' AND ";
                $stFiltro .= "\n(( pb.cod_banco is not null AND ";
                if ( Sessao::getExercicio() > '2012' ) {
                    $stFiltro .= "\n   ( pc.cod_estrutural like '1.1.1.%'  ";
                    $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.4.%' ))  ";
                    $stFiltro .= "\n   ) AND ";
                } else {
                    $stFiltro .= "\n   pc.cod_estrutural like '1.1.1.%' )  ";
                    $stFiltro .= "\n   OR pc.cod_estrutural like '1.1.5.%' ) AND ";
                }
                $stFiltro .= "\n   pa.cod_plano = " .$inCodCampoCod. " AND ";
                
                if ($inCodEntidade)
                    $stFiltro .= "\n   pb.cod_entidade in ( ".$inCodEntidade.") AND ";

                $stFiltro = " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4);
                $stOrder = ( $stOrder ) ?  $stOrder : 'cod_estrutural';

                $obErro = $obTContabilidadePlanoAnalitica->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

                # Quando não encontra nenhum valor no campo.
                if ( $rsLista->getNumLinhas() <= 0 ) {
                    SistemaLegado::executaFrameOculto("jQuery('#inCodPlano', window.parent.frames['telaPrincipal'].document.frm).val('');");
                    SistemaLegado::executaFrameOculto("jQuery('#stNomConta', window.parent.frames['telaPrincipal'].document.frm).html('&nbsp;');");
                    SistemaLegado::exibeAviso(urlencode( $inCodCampoCod )." - Não é uma Conta de Banco","n_incluir","erro");
                } else
                    $stDescricao = $rsLista->getCampo ( 'nom_conta' );
            } elseif ($stTipoBusca == "bordero_transf") {
                if ($request->get('stTipoTransacao') == "6") {
                    $obRContabilidadePlanoContaBanco = new RContabilidadePlanoBanco;
                    $obRContabilidadePlanoContaBanco->setCodPlano( $inCodCampoCod );
                    $obRContabilidadePlanoContaBanco->setExercicio( Sessao::getExercicio() );
                    if ($inCodEntidade)
                        $obRContabilidadePlanoContaBanco->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                    $obErro = $obRContabilidadePlanoContaBanco->consultar();

                    $codAgencia = $obRContabilidadePlanoContaBanco->obRMONAgencia->getCodAgencia();
                    if ($codAgencia <> "") {
                        if($inCodEntidade AND ($obRContabilidadePlanoContaBanco->obROrcamentoEntidade->getCodigoEntidade() <> $inCodEntidade))
                            SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getNomConta()." - Entidade diferente da informada"),"n_incluir","erro");
                        else
                            $stDescricao = $obRContabilidadePlanoContaBanco->getNomConta();
                    } else
                        SistemaLegado::exibeAviso(urlencode($obRContabilidadePlanoContaBanco->getCodPlano()." - Não é uma Conta de Banco"),"n_incluir","erro");
                } elseif ($request->get('stTipoTransacao') == "7") {
                    if ($inCodEntidade)
                        $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                    $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                    $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                    $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaTransferenciaEntidadeDiferente($rsRecordSet);
                    $stDescricao = $rsRecordSet->getCampo("nom_conta");
                } elseif ($request->get('stTipoTransacao') == "8") {
                    if ($inCodEntidade)
                        $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                    $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                    $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                    $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaConsignacao($rsRecordSet);
                    $stDescricao = $rsRecordSet->getCampo("nom_conta");
                }
            } elseif ($stTipoBusca == 'orcamento_extra') {
                if ($request->get('stTipoReceita') == 'orcamentaria')
                    $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '4' );
                elseif ($request->get('stTipoReceita') == 'extra')
                    $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '1.1.2' );
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );

                if ($request->get('tipoBusca2') == 'receitas_primarias') {
                    $obRContabilidadePlanoContaAnalitica->boFiltraReceitasPrimarias=true;
                    $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnalitica( $rsRecordSet );
                } else
                    $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoConta( $rsRecordSet );

                if ( substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 1 ) == '4' or substr( $rsRecordSet->getCampo( 'cod_estrutural' ), 0, 5 ) == '1.1.2' )
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
                else
                    SistemaLegado::exibeAviso( "Esta conta não é Extra-Orçamentaria.","","erro" );
            } elseif ($stTipoBusca == 'tes_transf') {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaTransferencia( $rsRecordSet );
                if (!$obErro->ocorreu())
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            } elseif ($stTipoBusca == 'tes_pag') {
                $obRContabilidadePlanoBanco = new RContabilidadePlanoBanco();
                $obRContabilidadePlanoBanco->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoBanco->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoBanco->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                $obErro = $obRContabilidadePlanoBanco->listarPlanoContaPagamento( $rsRecordSet );
                if (!$obErro->ocorreu())
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            } elseif ($stTipoBusca == 'tes_arrec') {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoContaAnalitica->obROrcamentoEntidade->setCodigoEntidade( $inCodEntidade );
                $obErro = $obRContabilidadePlanoContaAnalitica->listarPlanoContaArrecadacao( $rsRecordSet );
                if (!$obErro->ocorreu())
                    $stDescricao = $rsRecordSet->getCampo( 'nom_conta' );
            } elseif ($stTipoBusca == 'estrutural') {
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoContaAnalitica->consultar();
                $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
            } elseif ($stTipoBusca == 'contaSinteticaAtivoPermanente') {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaAtivoPermanente);

                if ($rsSinteticaAtivoPermanente->getNumLinhas() > 0)
                    $stDescricao = $rsSinteticaAtivoPermanente->getCampo('nom_conta');
            } elseif ($stTipoBusca == 'contaContabilDepreciacaoAcumulada') {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '1.2.3.8.1' );
                $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaDepreciacaoAcumulada);
                if ($rsSinteticaDepreciacaoAcumulada->getNumLinhas() > 0)
                    $stDescricao = $rsSinteticaDepreciacaoAcumulada->getCampo('nom_conta');
            } elseif ($stTipoBusca == 'contaContabilBaixaBem') {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '3' );
                $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaDepreciacao);

                if ($rsSinteticaDepreciacao->getNumLinhas() > 0)
                    $stDescricao = $rsSinteticaDepreciacao->getCampo('nom_conta');
            } elseif ($stTipoBusca == 'contaContabilAlienacao') {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );

                if ($request->get('tipoConta') == "VPAAlienacao" )
                    $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '4.6.2.2.1' );
                else
                    $obRContabilidadePlanoContaAnalitica->setCodEstrutural( '3.6.2.2.1' );

                $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaDepreciacao);

                if ($rsSinteticaDepreciacao->getNumLinhas() > 0)
                    $stDescricao = $rsSinteticaDepreciacao->getCampo('nom_conta');  
            } elseif ($stTipoBusca == 'contaContabilReavaliacao') {
                $stCodEstruturalDepreciacao = "";

                if($request->get('inCodNatureza') == 1)
                    $stCodEstruturalDepreciacao = '4.6.1.1.1.01';
                elseif($request->get('inCodNatureza') == 2)
                    $stCodEstruturalDepreciacao = '4.6.1.1.1.02';

                if($stCodEstruturalDepreciacao != ''){
                    $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                    $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                    $obRContabilidadePlanoContaAnalitica->setCodEstrutural( $stCodEstruturalDepreciacao );
                    $obErro = $obRContabilidadePlanoContaAnalitica->listarContaAnaliticaAtivoPermanente($rsSinteticaReavaliacao);
                    if ($rsSinteticaReavaliacao->getNumLinhas() > 0)
                        $stDescricao = $rsSinteticaReavaliacao->getCampo('nom_conta');
                    else{
                        SistemaLegado::executaFrameOculto("jq_('#".$stNomCampoCod."').val('');");
                        SistemaLegado::executaFrameOculto("jq_('#".$request->get('stIdCampoDesc')."').html('&nbsp;');");
                    }
                }else{
                    SistemaLegado::executaFrameOculto("jq_('#".$stNomCampoCod."').val('');");
                    SistemaLegado::executaFrameOculto("jq_('#".$request->get('stIdCampoDesc')."').html('&nbsp;');");
                    SistemaLegado::exibeAviso("Somente Grupo de Natureza 1 - Bens Móveis ou Natureza 2 - Bens Imóveis, podem ter Conta Contábil de Reavaliação.","n_incluir","erro");
                    break;
                }
            } else {
                $obRContabilidadePlanoContaAnalitica->setCodPlano( $inCodCampoCod );
                $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
                $obRContabilidadePlanoContaAnalitica->consultar();
                $stDescricao = $obRContabilidadePlanoContaAnalitica->getNomConta();
            }

            if (empty($stDescricao))
                SistemaLegado::exibeAviso("Esta conta não é válida!","n_incluir","aviso");
            else
                SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$stNomCampoCod."', '".$request->get('stIdCampoDesc')."', '".$request->get('stNomForm')."', '".$stDescricao."')");
        } else {
            $stDescricao = "";
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$stNomCampoCod."', '".$request->get('stIdCampoDesc')."', '".$request->get('stNomForm')."', '".$stDescricao."')");    
        }
    break;

}
