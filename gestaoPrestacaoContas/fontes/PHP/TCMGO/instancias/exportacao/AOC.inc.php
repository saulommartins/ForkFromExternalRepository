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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 27/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @ignore

    $Id: AOC.inc.php 62672 2015-06-03 13:34:20Z evandro $

    * Casos de uso: uc-06.04.00
*/

    include_once( CAM_GPC_TGO_MAPEAMENTO."TTGOAOC.class.php" );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $obTMapeamento = new TTGOAOC();
    $obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
    $obTMapeamento->setDado('dtFim'   	 , $arFiltroRelatorio['stDataFinal']   );
    $obTMapeamento->setDado('stEntidades', $stEntidades );

    $obTMapeamento->recuperaAlteracoesOrcamentarias( $rsAlteracoes );
    $obTMapeamento->recuperaAlteracoesOrcamentariasPorElementoDespesa( $rsAlteracoesElemento );
    $obTMapeamento->recuperaAlteracoesOrcamentariasPorRecurso( $rsAlteracoesRecurso );
    $obTMapeamento->recuperaRegistroLeiSuplementacao( $rsAlteracoesSuplementacao );
    $obTMapeamento->recuperaRegistroLeiCreditoEspecial( $rsAlteracoesCredito );
    $obTMapeamento->recuperaRegistroAlteracaoPPA($rsAlteracoesPPA);
    $obTMapeamento->recuperaRegistroDecretosAberturaCreditosAdicionais($rsDecretosAberturaCreditosAdicionais);

    $inCount = 0;
    //tipo10
    foreach ($rsAlteracoes->arElementos as $arAlteracoes) {

        $arAlteracoes['nro_sequencial'] = ++$inCount;
        $stChave = $arAlteracoes['num_programa'].$arAlteracoes['num_orgao'].$arAlteracoes['num_unidade'].$arAlteracoes['cod_funcao'].$arAlteracoes['cod_subfuncao'].$arAlteracoes['nat_acao'].$arAlteracoes['num_seqproj'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arAlteracoes));

        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_programa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nat_acao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_seqproj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(35);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        //tipo 11
        foreach ($rsAlteracoesElemento->arElementos as $arAlteracoesElemento) {
            $stChaveElemento = $arAlteracoesElemento['num_programa'].$arAlteracoesElemento['num_orgao'].$arAlteracoesElemento['num_unidade'].$arAlteracoesElemento['cod_funcao'].$arAlteracoesElemento['cod_subfuncao'].$arAlteracoesElemento['nat_acao'].$arAlteracoesElemento['num_seqproj'];
            $stChaveElementoRecurso = $stChaveElemento.$arAlteracoesElemento['cod_estrutural'].$arAlteracoesElemento['dt_suplementacao'].$arAlteracoesElemento['cod_suplementacao'].$arAlteracoesElemento['tipo_alteracao'];
            if ($stChave == $stChaveElemento) {
                $arAlteracoesElemento['nro_sequencial'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arAlteracoesElemento));

                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_programa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nat_acao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_seqproj");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_suplementacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_suplementacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_alteracao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_alteracao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                //tipo 12
                foreach ($rsAlteracoesRecurso->arElementos as $arAlteracoesRecurso) {
                    $stChaveRecurso = $arAlteracoesRecurso['num_programa'].$arAlteracoesRecurso['num_orgao'].$arAlteracoesRecurso['num_unidade'].$arAlteracoesRecurso['cod_funcao'].$arAlteracoesRecurso['cod_subfuncao'].$arAlteracoesRecurso['nat_acao'].$arAlteracoesRecurso['num_seqproj'].$arAlteracoesRecurso['cod_estrutural'].$arAlteracoesRecurso['dt_suplementacao'].$arAlteracoesRecurso['cod_suplementacao'].$arAlteracoesRecurso['tipo_alteracao'];
                    if ($stChaveRecurso == $stChaveElementoRecurso) {
                        $arAlteracoesRecurso['nro_sequencial'] = ++$inCount;

                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arAlteracoesRecurso));

                        $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_programa");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nat_acao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_seqproj");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_suplementacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_suplementacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_alteracao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_alteracao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    }
                }
            }
        }
    }

    $stFiltro = " where exercicio = '".Sessao::getExercicio()."' and parametro='cod_entidade_prefeitura' and cod_modulo = 8 and valor IN ('".$stEntidades."') ";

    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);

    if ($inCodEntidade != "") {
        $i = 0;
        foreach ($rsAlteracoesSuplementacao->arElementos as $arAux) {
            $rsAlteracoesSuplementacao->arElementos[$i]['nro_sequencial'] = $inCount + 1;
            $i++;
            $inCount++;
        }

        //tipo90
        $obExportador->roUltimoArquivo->addBloco( $rsAlteracoesSuplementacao );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_norma");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(48);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $i = 0;
        foreach ($rsAlteracoesCredito->arElementos as $arAux) {
            $rsAlteracoesCredito->arElementos[$i]['nro_sequencial'] = ++$inCount;
            $i++;
        }

        //tipo91
        $obExportador->roUltimoArquivo->addBloco( $rsAlteracoesCredito );
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_norma");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(48);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $i = 0;
        foreach ($rsAlteracoesPPA->arElementos as $arAux) {
            $rsAlteracoesPPA->arElementos[$i]['nro_sequencial'] = ++$inCount;
            $i++;
        }

        //tipo93
        $obExportador->roUltimoArquivo->addBloco($rsAlteracoesPPA);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_norma");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(64);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $i = 0;
        foreach ($rsDecretosAberturaCreditosAdicionais->arElementos as $arAux) {
            $rsDecretosAberturaCreditosAdicionais->arElementos[$i]['nro_sequencial'] = ++$inCount;
            $i++;
        }

        //tipo94
        $obExportador->roUltimoArquivo->addBloco($rsDecretosAberturaCreditosAdicionais);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_norma");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_credito");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
    }

    //tipo99
    $arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_sequencial' => $inCount+1 );

    $arRecordSet[$stArquivo] = new RecordSet();
    $arRecordSet[$stArquivo]->preenche( $arTemp );

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    if (Sessao::getExercicio() < 2011 ) {
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(75);
    } else {
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(78);
    }

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
