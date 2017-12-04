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
    * Página Oculta - Exportação Arquivos GPC

    * Data de Criação   : 19/11/2012

    * @author Analista: Gelson
    * @author Desenvolvedor: Jean

    * @ignore

    $Id:$

    * Casos de uso:
*/

include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TTransparenciaRemuneracao.class.php";

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$arDataInicial = explode('/',$arFiltroRelatorio['stDataInicial']);
$inMesInicial = $arDataInicial[1];
$inAnoInicial = $arDataInicial[2];
$inDiaInicial = $arDataInicial[0];

$arDataFinal = explode('/',$arFiltroRelatorio['stDataFinal']);
$inMesFinal = $arDataFinal[1];
$inAnoFinal = $arDataFinal[2];
$inDiaFinal = $arDataFinal[0];

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mesInicial" , $inMesInicial);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("anoInicial" , $inAnoInicial);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("diaInicial" , $inDiaInicial);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mesFinal"   , $inMesFinal);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("anoFinal"   , $inAnoFinal);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("diaFinal"   , $inDiaFinal);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaIntervaloPeriodosMovimentacaoDaSituacao($rsPeriodoMovimentacao, "", " ORDER BY periodo_movimentacao.cod_periodo_movimentacao ASC ");

foreach ($arEntidades as $codEntidade) {
    include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$codEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);
    $codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());

    if ($rsEsquemas->getNumLinhas() < 0) {
        $boEsquema = false;
        if ($codEntidade == $codEntidadePrefeitura) {
            $boEsquema = true;
            $stEntidade = '';
        }
    } else {
        $boEsquema = true;
        if ($codEntidade == $codEntidadePrefeitura) {
            $stEntidade = '';
        } else {
            $stEntidade = '_'.$codEntidade;
        }
    }

    if ($boEsquema == true) {
        foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {
            $arDataCompetencia = explode('-',$arPeriodoMovimentacao['dt_final']);
            $stDtCompetencia   = (string) ($arDataCompetencia[1].'/'.$arDataCompetencia[0]);

            $obTMapeamento = new TTransparenciaRemuneracao();
            $obTMapeamento->setDado('dt_final'     , $stDtCompetencia );
            $obTMapeamento->setDado('cod_entidade' , $codEntidade );
            $obTMapeamento->setDado('exercicio'    , Sessao::getExercicio() );
            $obTMapeamento->setDado('st_entidade'  , $stEntidade );
            $obTMapeamento->setDado('cod_periodo_movimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao'] );
            $obTMapeamento->recuperaRemuneracao($rsRemuneracao);
            $arRemuneracao = $rsRemuneracao->arElementos;

            $rsArquivo = "rsRemuneracao";
            $rsArquivo .= $stEntidade;
            $rsArquivo .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
            $$rsArquivo = new RecordSet();

            $$rsArquivo->preenche($arRemuneracao);

            $obExportador->roUltimoArquivo->addBloco($$rsArquivo);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mesano");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cgm");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_bruta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("redutor_teto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_natalina");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_ferias");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_outras");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("deducoes_irrf");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("deducoes_obrigatorias");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("demais_deducoes");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_apos_deducoes");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_familia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jetons");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("verbas");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
        }
    }
}

// Limpa tabela temporária temp_transparencia_remuneracao para evitar erros de chave na hora de deletar o periodo_movimentacao
//$obTMapeamento->limpaTabelaTemporaria();

?>
