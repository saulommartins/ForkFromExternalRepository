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

include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoBalanceteReceita.class.php";
include_once CAM_GF_EXP_MAPEAMENTO."FExportacaoLiquidacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php";

$stExercicio = Sessao::getExercicio();

$dtInicial = substr($arFiltroRelatorio['stDataInicial'],3);
$dtFinal   = substr($arFiltroRelatorio['stDataFinal'],3);

$arDataInicial = explode('/',$arFiltroRelatorio['stDataInicial']);
$arDataFinal   = explode('/',$arFiltroRelatorio['stDataFinal']);

if ($arDataInicial[1] == $arDataFinal[1]) {
    $inMesFinal= $arDataFinal[1] + 1;
    $arDataFinal = array($inMesFinal,$arDataInicial[2]);
    $dtFinal = implode('/',$arDataFinal  );
}

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());

foreach ($arEntidades as $inCodEntidade) {
    include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

    if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
        $arEsquemasEntidades[] = $inCodEntidade;
    }
}

foreach ($arEsquemasEntidades as $codEntidade) {

    if ($codEntidadePrefeitura !=$codEntidade) {
        $stEntidade = '_'.$codEntidade;
    } else {
        $stEntidade = '';
    }

    $obTFolha = new TFolhaPagamentoFolhaSituacao();

    $obTFolha->setDado('dt_inicial', $dtInicial);
    $obTFolha->setDado('dt_final', $dtFinal);
    $obTFolha->setDado('entidade', $stEntidade);
    $obTFolha->recuperaFolhaSituacaoPeriodo($rsFolha);

     if ($rsFolha->getNumLinhas() > 0) {

        include_once CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php";

        $obTMapeamento = new TEstagioEstagiarioEstagio();

        foreach ($rsFolha->arElementos as $key => $value) {
            $stArquivo = "rsEstagiarios";
            $stArquivo .= $stEntidade;
            $stArquivo .= $value['cod_periodo_movimentacao'];
            $$stArquivo = new RecordSet();

            $obTMapeamento->setDado('dt_inicial'    , $dtInicial);
            $obTMapeamento->setDado('dt_final'      , $dtFinal);
            $obTMapeamento->setDado('entidade'      , $stEntidade);
            $obTMapeamento->setDado('inCodEntidade' , $codEntidade);
            $obTMapeamento->setDado('exercicio'     , $stExercicio);
            $obTMapeamento->setDado('cod_periodo_movimentacao', $value['cod_periodo_movimentacao']);
            $obTMapeamento->recuperaExportacaoTransparencia( $$stArquivo);

            $obExportador->roUltimoArquivo->addBloco( $$stArquivo);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_entidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_ano");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_MMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_estagio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_renovacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_lotacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_local");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
        }
    }
}

?>
