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
    * Página Oculta para Relatório Resumo Execução de Restos a Pagar
    * Data de Criação   : 24/02/2016

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    * @ignore

    $Id: OCResumoExecucaoRP.php 65308 2016-05-11 20:00:27Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stJs = "";

switch ($request->get('stCtrl')) {
    default:
        include_once '../../../../../../config.php';
        include_once CAM_GF_EMP_MAPEAMENTO."FEmpenhoRelatorioResumoExecucaoRP.class.php";
        include_once CAM_FW_PDF."RRelatorio.class.php";

        $arFiltro = Sessao::read('filtroRelatorio');
        $request = new Request($arFiltro);

        $stDataInicial      = $request->get('stDataInicial');
        $stDataFinal        = $request->get('stDataFinal');
        $inCodEntidades     = $request->get('inCodEntidade');
        $stExercicioEmpenho = $request->get('inExercicio');
        $inCGM              = $request->get('inCGM');
        $inOrgao            = ($request->get('inCodOrgao') == "") ? 0 : $request->get('inCodOrgao');
        $inUnidade          = ($request->get('inCodUnidade') == "") ? 0 : $request->get('inCodUnidade');

        $stExercicio = Sessao::getExercicio();

        $inCodEntidades = implode(',', $inCodEntidades);

        $obFEmpenhoRelatorioResumoExecucaoRP = new FEmpenhoRelatorioResumoExecucaoRP();
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'exercicio'         , $stExercicio);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'cod_entidade'      , $inCodEntidades);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'dt_inicial'        , $stDataInicial);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'dt_final'          , $stDataFinal);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'exercicio_empenho' , $stExercicioEmpenho);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'cgm_credor'        , $inCGM);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'inOrgao'           , $inOrgao);
        $obFEmpenhoRelatorioResumoExecucaoRP->setDado( 'inUnidade'         , $inUnidade);

        //Array Filtro
        $inCount = 0;
        $arFiltro = array();
        $arFiltro[$inCount]['titulo'] = 'Exercício';
        $arFiltro[$inCount]['valor']  = $stExercicio;
        $inCount++;

        $arFiltro[$inCount]['titulo'] = 'Entidades';
        $arFiltro[$inCount]['valor']  = $inCodEntidades;

        $inCount++;

        $arFiltro[$inCount]['titulo'] = 'Data Inicial';
        $arFiltro[$inCount]['valor']  = $stDataInicial;

        $inCount++;

        $arFiltro[$inCount]['titulo'] = 'Data Final';
        $arFiltro[$inCount]['valor']  = $stDataFinal;

        if($inOrgao > 0){
            $inCount++;

            $stWhere  = " where exercicio='".Sessao::getExercicio()."'";
            $stWhere .= " and num_orgao = ".$inOrgao;

            $stNomOrgao = SistemaLegado::pegaDado('nom_orgao', 'orcamento.orgao', $stWhere);

            $arFiltro[$inCount]['titulo'] = 'Órgão';
            $arFiltro[$inCount]['valor']  = $inOrgao.' - '.$stNomOrgao;

            if($inUnidade > 0){
                $inCount++;

                $stWhere  = " where exercicio='".Sessao::getExercicio()."'";
                $stWhere .= " and num_unidade = ".$inUnidade;
                $stWhere .= " and num_orgao = ".$inOrgao;

                $stNomUnidade = SistemaLegado::pegaDado('nom_unidade', 'orcamento.unidade', $stWhere);

                $arFiltro[$inCount]['titulo'] = 'Unidade';
                $arFiltro[$inCount]['valor']  = $inUnidade.' - '.$stNomUnidade;
            }
        }

        $assinaturas = Sessao::read('assinaturas');
        if ( count($assinaturas['selecionadas']) > 0 ) {
            include_once CAM_FW_PDF."RAssinaturas.class.php";
            $obRAssinaturas = new RAssinaturas;
            $obRAssinaturas->setArAssinaturas( $assinaturas['selecionadas'] );
            $rsAssinaturas = $obRAssinaturas->getArAssinaturas();

            foreach ($rsAssinaturas as $key => $assinatura) {
                $arAssinaturas[] = $rsAssinaturas[$key]->getElementos();
            }
        }

        //Gerando os records sets
        $obFEmpenhoRelatorioResumoExecucaoRP->recuperaTodos($rsExecucaoRP);
        $arExecucaoRP = $rsExecucaoRP->getElementos();

        //SOMAR TODOS OS ARRAYS
        $arTotalExercicioEntidade = array();
        $arTotalExercicio         = array();
        $arTotal                  = array();
        foreach($arExecucaoRP as $restos) {
            $inCodEntidade = $restos['cod_entidade'];
            $stNomEntidade = $restos['nom_entidade'];
            $stIdEntidade = $inCodEntidade." - ".$stNomEntidade;

            $inCountEntidade = count($arTotalExercicio[$stIdEntidade]);
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['exercicio']              = $restos['exercicio'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['empenhado']              = $restos['empenhado'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['aliquidar']              = $restos['aliquidar'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['liquidadoapagar']        = $restos['liquidadoapagar'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['anulado']                = $restos['anulado'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['liquidado']              = $restos['liquidado'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['pagamento']              = $restos['pagamento'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['empenhado_saldo']        = $restos['empenhado_saldo'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['aliquidar_saldo']        = $restos['aliquidar_saldo'];
            $arTotalExercicio[$stIdEntidade][$inCountEntidade]['liquidadoapagar_saldo']  = $restos['liquidadoapagar_saldo'];

            $arTotalExercicioEntidade[$stIdEntidade]['empenhado']              += $restos['empenhado'];
            $arTotalExercicioEntidade[$stIdEntidade]['aliquidar']              += $restos['aliquidar'];
            $arTotalExercicioEntidade[$stIdEntidade]['liquidadoapagar']        += $restos['liquidadoapagar'];
            $arTotalExercicioEntidade[$stIdEntidade]['anulado']                += $restos['anulado'];
            $arTotalExercicioEntidade[$stIdEntidade]['liquidado']              += $restos['liquidado'];
            $arTotalExercicioEntidade[$stIdEntidade]['pagamento']              += $restos['pagamento'];
            $arTotalExercicioEntidade[$stIdEntidade]['empenhado_saldo']        += $restos['empenhado_saldo'];
            $arTotalExercicioEntidade[$stIdEntidade]['aliquidar_saldo']        += $restos['aliquidar_saldo'];
            $arTotalExercicioEntidade[$stIdEntidade]['liquidadoapagar_saldo']  += $restos['liquidadoapagar_saldo'];

            $arTotal[0]['empenhado']             += $restos['empenhado'];
            $arTotal[0]['aliquidar']             += $restos['aliquidar'];
            $arTotal[0]['liquidadoapagar']       += $restos['liquidadoapagar'];
            $arTotal[0]['anulado']               += $restos['anulado'];
            $arTotal[0]['liquidado']             += $restos['liquidado'];
            $arTotal[0]['pagamento']             += $restos['pagamento'];
            $arTotal[0]['empenhado_saldo']       += $restos['empenhado_saldo'];
            $arTotal[0]['aliquidar_saldo']       += $restos['aliquidar_saldo'];
            $arTotal[0]['liquidadoapagar_saldo'] += $restos['liquidadoapagar_saldo'];
        }

        $arDados['exercicio']                = $stExercicio;
        $arDados['stDataInicial']            = $stDataInicial;
        $arDados['stDataFinal']              = $stDataFinal;
        $arDados['inCodEntidade']            = $inCodEntidades;

        ksort($arTotalExercicio);
        $arDados['total_exercicio']          = $arTotalExercicio;
        $arDados['total_exercicio_entidade'] = $arTotalExercicioEntidade;
        $arDados['total']                    = $arTotal;
        $arDados['arAssinaturas']            = $arAssinaturas;
        if($rsAssinaturas)
            $arDados['rsAssinaturas']        = $rsAssinaturas;
        $arDados['filtro']                   = $arFiltro;

        Sessao::write('arDados', $arDados);

        $obRRelatorio = new RRelatorio;
        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioResumoExecucaoRP.php" );

    break;

    case "MontaUnidade":
        $stJs .= "limpaSelect(f.inCodUnidade,0); \n";
        $stJs .= "jq('#inCodUnidadeTxt').val(''); \n";
        $stJs .= "jq('#inCodUnidade').append( new Option('Selecione','', 'selected')) ;\n";

        if ($request->get('inCodOrgao', '') != '') {
            include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRPAnuLiqEstLiq.class.php";
            $obREmpenhoRPAnuLiqEstLiq = new REmpenhoRelatorioRPAnuLiqEstLiq;
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($request->get('inCodOrgao'));
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
            $obREmpenhoRPAnuLiqEstLiq->obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo, "", "", $boTransacao );

            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                $stJs .= "jq('#inCodUnidade').append( new Option(\"".$rsCombo->getCampo("nom_unidade")."\",\"".$rsCombo->getCampo("num_unidade")."\" )); \n";
                $rsCombo->proximo();
            }
        }
    break;
}

echo $stJs;

?>
