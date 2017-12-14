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
    * Data de Criação   : 02/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    $Id: EXT.inc.php 65220 2016-05-03 21:30:22Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio().'/TTCMGOExtraOrcamentarias.class.php';
include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio().'/TTCMGOArquivoEXT.class.php';

$arFiltroRelatorio = Sessao::read('filtroRelatorio');
$arData = explode('/', $arFiltroRelatorio['stDataFinal']);

$obTMapeamento = new TTCMGOExtraOrcamentarias;
$obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
$obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
$obTMapeamento->setDado('dtFim'   	 , $arFiltroRelatorio['stDataFinal']   );
$obTMapeamento->setDado('mes'        , $arData[1]);
$obTMapeamento->setDado('stEntidades', $stEntidades );
$obTMapeamento->recuperaTodos($rsExtraOrcamentarias);

$obTMapeamento->recuperaMovimentacaoFinanceira($rsMovimentacaoFinanceira);
$obTMapeamento->recuperaDetalhamentoFonteRecurso($rsDetalhamentoFonteRecurso);

$inCount = 0;
$inCodConta = 1;
$inCodContaAnterior = 0;

$inSequencial = SistemaLegado::pegaDado("sequencial", "tcmgo.arquivo_ext", "WHERE exercicio = '".Sessao::getExercicio()."' ORDER BY sequencial DESC LIMIT 1") + 1;

//tipo10
foreach ($rsExtraOrcamentarias->arElementos as $arExtraOrcamentarias) {
    $stChave = $arExtraOrcamentarias['orgao'].$arExtraOrcamentarias['categoria'].$arExtraOrcamentarias['tipo_lancamento'].$arExtraOrcamentarias['sub_tipo_lancamento'].$arExtraOrcamentarias['desdobra_subtipo'];
    $stChave .= $arExtraOrcamentarias['nro_extra_orcamentaria'];

    if ($arExtraOrcamentarias['sequencial'] == "") {
        if ($inCodContaAnterior == 0) {
            $inCodContaAnterior = $arExtraOrcamentarias['nro_extra_orcamentaria'];
            $arExtraOrcamentarias['sequencial'] = $inSequencial;

            $obTTCMGOArquivoEXT = new TTCMGOArquivoEXT;
            $obTTCMGOArquivoEXT->setDado('cod_plano' , $arExtraOrcamentarias['nro_extra_orcamentaria']);
            $obTTCMGOArquivoEXT->setDado('exercicio' , Sessao::getExercicio());
            $obTTCMGOArquivoEXT->setDado('mes'       , $arData[1]);
            $obTTCMGOArquivoEXT->setDado('sequencial', $inSequencial);

            $obTTCMGOArquivoEXT->inclusao();
        } else {
            if ($inCodContaAnterior != $arExtraOrcamentarias['nro_extra_orcamentaria']) {
                $inSequencial++;

                $obTTCMGOArquivoEXT = new TTCMGOArquivoEXT;
                $obTTCMGOArquivoEXT->setDado('cod_plano' , $arExtraOrcamentarias['nro_extra_orcamentaria']);
                $obTTCMGOArquivoEXT->setDado('exercicio' , Sessao::getExercicio());
                $obTTCMGOArquivoEXT->setDado('mes'       , $arData[1]);
                $obTTCMGOArquivoEXT->setDado('sequencial', $inSequencial);

                $obTTCMGOArquivoEXT->inclusao();
            }

            $inCodContaAnterior = $arExtraOrcamentarias['nro_extra_orcamentaria'];
            $arExtraOrcamentarias['sequencial'] = $inSequencial;
        }
    }

    $arExtraOrcamentarias['numero_registro'] = ++$inCount;

    $rsBloco = 'rsBloco_'.$inCount;
    unset($$rsBloco);
    $$rsBloco = new RecordSet();
    $$rsBloco->preenche(array($arExtraOrcamentarias));

    $obExportador->roUltimoArquivo->addBloco( $$rsBloco );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");//receita nao tem orgao
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo_lancamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_subtipo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_conta");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 50 );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_lancamento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    /* REGISTRO 11 MOVIMENTAÇÃO FINANCEIRA */
    foreach ($rsMovimentacaoFinanceira->arElementos as $arMovimentacaoFinanceira) {
        $stChaveElemento11 = $arMovimentacaoFinanceira['orgao'].$arMovimentacaoFinanceira['categoria'].$arMovimentacaoFinanceira['tipo_lancamento'].$arMovimentacaoFinanceira['sub_tipo_lancamento'].$arMovimentacaoFinanceira['desdobra_subtipo'];
        $stChaveElemento11 .= $arMovimentacaoFinanceira['nro_extra_orcamentaria'];

        if ($stChave == $stChaveElemento11) {
            $arMovimentacaoFinanceira['sequencial'] = $arExtraOrcamentarias['sequencial'];

            $stChaveElemento11 .= $arMovimentacaoFinanceira['banco'].$arMovimentacaoFinanceira['agencia'].$arMovimentacaoFinanceira['conta_corrente'].$arMovimentacaoFinanceira['digito'];

            $arMovimentacaoFinanceira['numero_registro'] = ++$inCount;

            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arMovimentacaoFinanceira));

            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");//receita nao tem orgao
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo_lancamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_subtipo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 4 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 12 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 26 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        }

        /* TIPO REGISTRO 12 -- DETALHAMENTOS DAS FONTES DE RECURSOS*/
        $i = 0;
        foreach ($rsDetalhamentoFonteRecurso->arElementos as $arDetalhamentoFonteRecurso) {
            $stChaveElemento12 = $arDetalhamentoFonteRecurso['orgao'].$arDetalhamentoFonteRecurso['categoria'].$arDetalhamentoFonteRecurso['tipo_lancamento'].$arDetalhamentoFonteRecurso['sub_tipo_lancamento'].$arDetalhamentoFonteRecurso['desdobra_subtipo'].$arDetalhamentoFonteRecurso['nro_extra_orcamentaria'].$arDetalhamentoFonteRecurso['banco'].$arDetalhamentoFonteRecurso['agencia'].$arDetalhamentoFonteRecurso['conta_corrente'].$arDetalhamentoFonteRecurso['digito'];

            if ($stChaveElemento11 === $stChaveElemento12) {
                $arDetalhamentoFonteRecurso['sequencial'] = $arExtraOrcamentarias['sequencial'];
                $arDetalhamentoFonteRecurso['numero_registro'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arDetalhamentoFonteRecurso));

                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao");//receita nao tem orgao
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo_lancamento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_subtipo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 3 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 4 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 12 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 20 );

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }
            $i++;
        }
    }
}

//// totais

$arRegistro = array();
$arRegistro[0][ 'tipo_registro' ] = 99 ;

/// Saldo Exrcicio Anterior Caixa
$arRegistro[0]['vlSaldoExercAntCaixa'] =
         $obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                  and plano_conta.cod_estrutural like '1.1.1.1.1%'
                                                  and plano_conta.exercicio = '".Sessao::getExercicio()."' "
                                            );

/// Saldo Exercicio anterior banco exercicio anterior( apenas recursos livres )
$arRegistro[0]['vlSaldoExercAntBanco'] =
         number_format($obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                                and plano_conta.cod_estrutural like '1.1.1.1.2%'
                                                                and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                         " ),2,'.','');

///// Saldo vinculado em conta bancaria exercicio anterior
$arRegistro[0]['vlSaldoExercAntVinculado'] =
         number_format($obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                                and plano_conta.cod_estrutural like '1.1.5%'
                                                                and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                                                     " )
                        +
                       $obTMapeamento->recuperaTotalConta ( " where valor_lancamento.tipo = 'I'
                                                                and plano_conta.cod_estrutural like '1.1.1.1.3%'
                                                                and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                        " ),2,'.','');

$arRegistro[0]['vlSaldoMesSegCaixa'] =
         $obTMapeamento->recuperaTotalConta ( " --where valor_lancamento.tipo <> 'I'
                                                    and plano_conta.cod_estrutural like '1.1.1.1.1%'
                                                    and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                    and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                    and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                          " );

$arRegistro[0]['vlSaldoMesSegBanco'] =
        number_format($obTMapeamento->recuperaTotalConta ( " --where valor_lancamento.tipo <> 'I'
                                                                 and plano_conta.cod_estrutural like '1.1.1.1.2%'
                                                                 and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                                 and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                                 and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                                      " )
                                            ,2,'.','') ;

///// Saldo vinculado em conta bancaria exercicio anterior
$arRegistro[0]['vlSaldoMesSegVinculado'] =
          $obTMapeamento->recuperaTotalConta ( " where plano_conta.cod_estrutural like '1.1.1.1.3%'
                                                   and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                   and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                   and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                           " )
           +
          $obTMapeamento->recuperaTotalConta ( " where plano_conta.cod_estrutural like '1.1.5%'
                                                   and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                   and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                   and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                          " )
           +
          $obTMapeamento->recuperaTotalConta ( " where plano_conta.cod_estrutural like '1.1.1.1.4%'
                                                   and plano_conta.exercicio = '".Sessao::getExercicio()."'
                                                   and lote.dt_lote  >= to_date( '01/01/".Sessao::getExercicio()."', 'dd/mm/yyyy' )
                                                   and lote.dt_lote  < to_date( '".$arFiltroRelatorio['stDataFinal']."', 'dd/mm/yyyy' ) + 1
                                          " );

$arRegistro[0]['brancos'] = ' ';
$arRegistro[0]['numero_registro'] = $inCount+1;

$rsBloco = 'rsBloco_'.$inCount;
unset($$rsBloco);
$$rsBloco = new RecordSet();
$$rsBloco->preenche($arRegistro);

$obExportador->roUltimoArquivo->addBloco( $$rsBloco );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(82);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
