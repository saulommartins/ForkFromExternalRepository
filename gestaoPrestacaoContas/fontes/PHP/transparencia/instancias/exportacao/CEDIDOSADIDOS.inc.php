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

    * @ignore
*/

include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TTransparenciaCedidosAdidos.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

$arDataInicial = explode('/',$arFiltroRelatorio['stDataInicial']);
$inMesInicial  = $arDataInicial[1];
$inAnoInicial  = $arDataInicial[2];

$arDataFinal = explode('/',$arFiltroRelatorio['stDataFinal']);
$inMesFinal  = $arDataFinal[1];
$inAnoFinal  = $arDataFinal[2];

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mesInicial", $inMesInicial);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("anoInicial", $inAnoInicial);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mesFinal", $inMesFinal);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("anoFinal", $inAnoFinal);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaIntervaloPeriodosMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

$arEntidades = explode(',',$stEntidades);

$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, $stAno);

foreach ($arEntidades as $inCodEntidade) {
    include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

    if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
        $arEsquemasEntidades[] = $inCodEntidade;
    }
}

foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodoMovimentacao ) {

    foreach ($arEsquemasEntidades as $codEntidade) {

        if ($codEntidadePrefeitura !=$codEntidade) {
            $stEntidade = '_'.$codEntidade;
        } else {
            $stEntidade = '';
        }

        $stArquivo = "rsCedidosAdidos";
        $stArquivo .= $codEntidade;
        $stArquivo .= $arPeriodoMovimentacao['cod_periodo_movimentacao'];
        $$stArquivo = new RecordSet();

        $obTMapeamento = new TTransparenciaCedidosAdidos();

        $obTMapeamento->setDado('exercicio'   , $stAno );
        $obTMapeamento->setDado('stEntidade'  , $stEntidade );
        $obTMapeamento->setDado('inCodPeriodoMovimentacao', $arPeriodoMovimentacao['cod_periodo_movimentacao'] );
        $obTMapeamento->setDado('codEntidade' , $codEntidade);
        $obTMapeamento->recuperaCedidosAdidos($$stArquivo);

        $obExportador->roUltimoArquivo->addBloco($$stArquivo);
        $obExportador->roUltimoArquivo->setTipoDocumento('transparencia');

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_entidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_ano");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula_servidor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ato_cedencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_cedencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicativo_onus");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao_cedente_cessionario");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_convenio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("local");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
    }

}

?>
