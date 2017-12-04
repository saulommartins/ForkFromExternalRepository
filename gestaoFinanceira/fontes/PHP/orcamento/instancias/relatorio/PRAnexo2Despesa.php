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
    * Página oculta pra pré-processar o relatorio
    * Data de Criação   : 25/09/2004

    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Revision: 32061 $
    $Name$
    $Author: rodrigosoares $
    $Date:

    * Casos de uso: uc-02.01.11
*/

/*
$Log$
Revision 1.7  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2Despesa.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2DespesaBalanco.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2DespesaOrgao.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2DespesaOrgaoBalanco.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2DespesaUnidade.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2DespesaUnidadeBalanco.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2CategoriaEconomica.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioAnexo2CategoriaEconomicaBalanco.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoOrgaoOrcamentario.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"         );

$obRRelatorio = new RRelatorio;

$arFiltro = Sessao::read('filtroRelatorio');
$stTipoRelatorio = trim( $arFiltro['stTipoRelatorio']);
$stSituacao = $arFiltro['stSituacao'];
$stDataInicial = $arFiltro['stDataInicial'];
$stDataFinal = $arFiltro['stDataFinal'];
$stDemonstrarValores = $arFiltro['stDemonstrarValores'];
$arNomFiltro = Sessao::read('filtroNomRelatorio');

Sessao::write('filtroNomRelatorio',$arNomFiltro);
Sessao::write('stSituacao',$stSituacao);
Sessao::write('stDataInicial',$stDataInicial);
Sessao::write('stDataFinal',$stDataFinal);

if (is_array($arFiltro['inCodEntidade'])) {
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
            $stEntidades .= $valor.",";
    }
} else {
    $stEntidades = $arFiltro['inCodEntidade'];
}
$stEntidades = trim(substr( $stEntidades, 0, strlen($stEntidades) - 1 ));
if ($stTipoRelatorio == 'resumo') {
    if ($stDemonstrarValores == 'orcamento') {
        $obRegra = new ROrcamentoRelatorioAnexo2Despesa;
    } elseif ($stDemonstrarValores == 'balanco') {
        $obRegra = new ROrcamentoRelatorioAnexo2DespesaBalanco;
    }
} elseif ($stTipoRelatorio == 'orgao') {
    if ($stDemonstrarValores == 'orcamento') {
        $obRegra = new ROrcamentoRelatorioAnexo2DespesaOrgao;
    } elseif ($stDemonstrarValores == 'balanco') {
        $obRegra = new ROrcamentoRelatorioAnexo2DespesaOrgaoBalanco;
    }

} elseif ($stTipoRelatorio == 'orgao_unidade') {
    if ($stDemonstrarValores == 'orcamento') {
        $obRegra = new ROrcamentoRelatorioAnexo2DespesaUnidade;
    } elseif ($stDemonstrarValores == 'balanco') {
        $obRegra = new ROrcamentoRelatorioAnexo2DespesaUnidadeBalanco;
    }
} elseif ($stTipoRelatorio == 'unidade_categoria_economica') {
    if ($stDemonstrarValores == 'orcamento') {
        $obRegra = new ROrcamentoRelatorioAnexo2CategoriaEconomica;
    } elseif ($stDemonstrarValores == 'balanco') {
        $obRegra = new ROrcamentoRelatorioAnexo2CategoriaEconomicaBalanco;
    }
}

$stFiltro = "";
//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $stFiltro .= " AND cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor.",";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 ) . ")";
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}

//seta elementos do filtro para ORGAO E/OU UNIDADE

if ($arFiltro['stDotacaoOrcamentaria'] != "") {
    $arCodigos = explode (".", $arFiltro["stDotacaoOrcamentaria"]);
    $inCodOrgao = (int) $arCodigos[0];
    $inCodUnidade = (int) $arCodigos[1];

    if ($inCodOrgao) {
        $inNumOrgao = explode("-",$arFiltro['inCodOrgao']);
        $obROrcamentoOrgaoOrcamentario = new ROrcamentoOrgaoOrcamentario;

        $obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inNumOrgao[1]);
        $obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
        $obROrcamentoOrgaoOrcamentario->listar($rsOrgao);
        $arFiltro["stNomOrgao"] = $rsOrgao->getCampo("num_orgao")." - ".$rsOrgao->getCampo("nom_orgao");
        $stFiltro .= " AND num_orgao = ".$inCodOrgao." ";
        $obRegra->setOrgao ( $inCodOrgao );
    }

    if ($inCodUnidade) {
        $obROrcamentoUnidadeOrcamentaria = new ROrcamentoUnidadeOrcamentaria;

        $obROrcamentoUnidadeOrcamentaria->setNumeroUnidade($inCodUnidade);
        $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inNumOrgao[1]);
        $obROrcamentoUnidadeOrcamentaria->setExercicio(Sessao::getExercicio());
        $obROrcamentoUnidadeOrcamentaria->listar($rsUnidade);
        $arFiltro["stNomUnidade"] = $rsUnidade->getCampo("num_unidade")." - ". $rsUnidade->getCampo("nom_unidade");
        $stFiltro .= " AND num_unidade = ".$inCodUnidade." ";
        $obRegra->setUnidade ( $inCodUnidade );
   }
}

if ($stFiltro != "") {
    $obRegra->setFiltro( $stFiltro );
}

$obRegra->setExercicio     ( Sessao::getExercicio() );

$arFiltro['stDemonstrarValores'] = $stDemonstrarValores;
Sessao::write('filtroRelatorio',$arFiltro);

if ($stTipoRelatorio == 'resumo') {
    if ($stDemonstrarValores == 'balanco') {
        $obRegra->setDataInicial( $stDataInicial);
        $obRegra->setDataFinal  ( $stDataFinal  );
        $obRegra->setSituacao   ( $stSituacao   );
        $obRegra->setEntidades  ( $stEntidades   );
        $obRegra->setExercicio  ( Sessao::getExercicio() );
    }

    $obRegra->geraRecordSet( $rsAnexo2, $rsAnexo2Cabecalho, $rsAnexo2Resumo );

     Sessao::write('rsAnexo2',$rsAnexo2);
     Sessao::write('rsAnexo2Cabecalho',$rsAnexo2Cabecalho);
     Sessao::write('rsAnexo2Resumo',$rsAnexo2Resumo);

    $stOculto = "OCGeraRelatorioAnexo2Despesa.php";

} elseif ($stTipoRelatorio == 'orgao') {
    if ($stDemonstrarValores == 'balanco') {
        $obRegra->setDataInicial( $stDataInicial);
        $obRegra->setDataFinal  ( $stDataFinal  );
        $obRegra->setSituacao   ( $stSituacao   );
        $obRegra->setEntidades  ( $stEntidades   );
        $obRegra->setExercicio  ( Sessao::getExercicio() );
    }

    $obRegra->geraRecordSet( $arAnexo2, $rsTotal );

    Sessao::write('rsAnexo2',$arAnexo2);
    Sessao::write('rsTotal',$rsTotal);

    $stOculto = "OCGeraRelatorioAnexo2DespesaOrgao.php";
} elseif ($stTipoRelatorio == 'orgao_unidade') {
    if ($stDemonstrarValores == 'balanco') {
        $obRegra->setDataInicial( $stDataInicial);
        $obRegra->setDataFinal  ( $stDataFinal  );
        $obRegra->setSituacao   ( $stSituacao   );
        $obRegra->setEntidades  ( $stEntidades   );
        $obRegra->setExercicio  ( Sessao::getExercicio() );
    }
    $obRegra->geraRecordSet( $arAnexo2, $rsTotal );

    Sessao::write('rsAnexo2',$arAnexo2);
    Sessao::write('rsTotal',$rsTotal);

    $stOculto = "OCGeraRelatorioAnexo2DespesaUnidade.php";
} elseif ($stTipoRelatorio == 'unidade_categoria_economica') {
    if ($stDemonstrarValores == 'balanco') {
        $obRegra->setDataInicial( $arFiltro['stDataInicial']);
        $obRegra->setDataFinal  ( $arFiltro['stDataFinal']  );
        $obRegra->setSituacao   ( $arFiltro['stSituacao']   );
        $obRegra->setEntidades  ( $stEntidades   );
        $obRegra->setExercicio  ( Sessao::getExercicio() );
    }
    $obRegra->geraRecordSet( $rsCategoriaEconomica3 , 3 );
    $obRegra->geraRecordSet( $rsCategoriaEconomica4 , 4 );
    $obRegra->geraRecordSet( $rsCategoriaEconomica9 , 9 );
    $obRegra->geraRecordSet( $rsCategoriaEconomica7 , 7 );

    Sessao::write('rsAnexo2',$rsCategoriaEconomica3->getElementos());
    Sessao::write('rsTotal',$rsCategoriaEconomica4->getElementos());

    $arCategoriaEconomica3 = $rsCategoriaEconomica3->getElementos();
    $arCategoriaEconomica4 = $rsCategoriaEconomica4->getElementos();

    $arTotalizadores[0][0] = 'descricao';
    $arTotalizadores[0][1] = 'DESPESAS CORRENTES';
    $arTotalizadores[0][2] = 'DESPESAS DE CAPITAL';
    $arTotalizadores[0][3] = 'TOTAL GERAL';

    for ($inCount=0; $inCount<count($arCategoriaEconomica3[1][0]); $inCount++) {
        $arTotalizadores[1][0][$inCount]['descricao'] = $arCategoriaEconomica3[1][0][$inCount]['descricao'];
        $arTotalizadores[1][0][$inCount]['DESPESAS CORRENTES']  = $arCategoriaEconomica3[1][0][$inCount]['TOTAL'];
        $arTotalizadores[1][0][$inCount]['DESPESAS DE CAPITAL'] = $arCategoriaEconomica4[1][0][$inCount]['TOTAL'];
        $arTotalizadores[1][0][$inCount]['TOTAL GERAL']         = $arCategoriaEconomica3[1][0][$inCount]['TOTAL'] + $arCategoriaEconomica4[1][0][$inCount]['TOTAL'];

        $arTotalizadores[2][0]['descricao']            = 'T O T A L ...........';
        $arTotalizadores[2][0]['DESPESAS CORRENTES']  += $arCategoriaEconomica3[1][0][$inCount]['TOTAL'];
        $arTotalizadores[2][0]['DESPESAS DE CAPITAL'] += $arCategoriaEconomica4[1][0][$inCount]['TOTAL'];
        $arTotalizadores[2][0]['TOTAL GERAL']          = $arTotalizadores[2][0]['DESPESAS CORRENTES'] + $arTotalizadores[2][0]['DESPESAS DE CAPITAL'];

 }
    Sessao::write('arTotalizadores',$arTotalizadores);

    $arTotalGeral[0]['descricao'] = "T O T A L   G E R A L ...........";
    $arTotalGeral[0]['valor'] = $arTotalizadores[2][0]['TOTAL GERAL'] + $rsCategoriaEconomica9->getCampo('valor') + $rsCategoriaEconomica7->getCampo('valor');

    $rsTotalGeral = new RecordSet;
    $rsTotalGeral->preenche( $arTotalGeral );
    $rsTotalGeral->addFormatacao('valor','NUMERIC_BR');

    $rsCategoriaEconomica9->addFormatacao('valor','NUMERIC_BR');
    $rsCategoriaEconomica7->addFormatacao('valor','NUMERIC_BR');

    Sessao::write('rsCategoriaEconomica9',$rsCategoriaEconomica9);
    Sessao::write('rsTotalGeral',$rsTotalGeral);
    Sessao::write('rsCategoriaEconomica7',$rsCategoriaEconomica7);

    $stOculto = "OCGeraRelatorioAnexo2CategoriaEconomica.php";
}

$obRRelatorio->executaFrameOculto( $stOculto );
?>
