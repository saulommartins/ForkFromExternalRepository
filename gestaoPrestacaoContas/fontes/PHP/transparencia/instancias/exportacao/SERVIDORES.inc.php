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
  * Data de Criação   : 16/11/2012

  * @author Analista: Gelson
  * @author Desenvolvedor: Carolina
  $Id: $
  * @ignore
  */

include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TTransparenciaServidor.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$arDataInicial = explode('/',$arFiltroRelatorio['stDataInicial']);
$inMesInicial  = $arDataInicial[1];
$inAnoInicial  = $arDataInicial[2];

$arDataFinal = explode('/',$arFiltroRelatorio['stDataFinal']);
$inMesFinal  = $arDataFinal[1];
$inAnoFinal  = $arDataFinal[2];
$arEntidades = explode(',',$stEntidades);
$Cont=0;

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());

for ($i=0;$i<(count($arEntidades));$i++) {
    if ($codEntidadePrefeitura == $arEntidades[$i]) {
        $filtroEsquemasEntidades[$Cont] = $arEntidades[$i];
        $Cont++;
    } else {
        include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
        $obTEntidade = new TEntidade();
        $stFiltro = " WHERE nspname = 'pessoal_".$arEntidades[$i]."'";
        $obTEntidade->recuperaEsquemasCriados($rsEsquema, $stFiltro);

        if ($rsEsquema->getNumLinhas() > 0) {
            $filtroEsquemasEntidades[$Cont] = $arEntidades[$i];
            $Cont++;
        }
    }
}

foreach ($filtroEsquemasEntidades as $inCodEntidade) {
    include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'folhapagamento_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquemas,$stFiltro);

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mesInicial" , $inMesInicial);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("anoInicial" , $inAnoInicial);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("mesFinal"   , $inMesFinal);
    $obTFolhaPagamentoPeriodoMovimentacao->setDado("anoFinal"   , $inAnoFinal);

    if ($codEntidadePrefeitura == $inCodEntidade) {
        $obTFolhaPagamentoPeriodoMovimentacao->setTabela("folhapagamento.periodo_movimentacao");
    } elseif ($rsEsquemas->getElementos() > 0) {
        $obTFolhaPagamentoPeriodoMovimentacao->setTabela("folhapagamento_".$inCodEntidade.".periodo_movimentacao");
    }

    $obTFolhaPagamentoPeriodoMovimentacao->recuperaIntervaloPeriodosMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

    foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {

        $stArquivo = "rsServidor";
        $stArquivo .= $stEntidade;
        $stArquivo .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
        $$stArquivo = new RecordSet();

        $obTMapeamento = new TTransparenciaServidor;
        $obTMapeamento->setDado('inCompetencia', $arPeriodoMovimentacao['cod_periodo_movimentacao'] );
        $obTMapeamento->setDado('inCodEntidade', $inCodEntidade );
        $obTMapeamento->setDado('stEntidades  ', $stEntidade );
        $obTMapeamento->recuperaServidor($$stArquivo);

        $obExportador->roUltimoArquivo->addBloco($$stArquivo);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mesano");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_admissao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ato_nomeacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_rescisao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_causa_rescisao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_regime_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_sub_divisao_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_especialidade_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_padrao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_mensais");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lotacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_lotacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_local");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

    }
}

?>
