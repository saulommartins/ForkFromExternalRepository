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

    * Data de Criação   : 18/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    $Id: OPS.inc.php 66621 2016-10-11 13:09:49Z franver $

    * Casos de uso: uc-06.04.00
*/

    include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio()."/TTGOOPS.class.php";

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $arData = explode('/', $arFiltroRelatorio['stDataFinal']);

    $obTMapeamento = new TTGOOPS();
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('dtInicio'   , $arFiltroRelatorio['stDataInicial'] );
    $obTMapeamento->setDado('dtFim'        , $arFiltroRelatorio['stDataFinal']   );
    $obTMapeamento->setDado('cod_entidade',$stEntidades );
    $obTMapeamento->setDado('mes'        , $arData[1]);
    $obTMapeamento->recuperaOrdemPagamento( $rsOrdemPagamento ); 

    $vlTotalCaixa = 0;

    $obTMapeamento->recuperaOrdemPagamentoLiquidacoes($rsOrdemPagamentoLiquidacoes);
    $obTMapeamento->recuperaOrdemPagamentoMovimentacao2009($rsOrdemPagamentoMovimentacao);
    $obTMapeamento->recuperaOrdemPagamentoFonteRecursos2016($rsOrdemPagamentoFonteRecursos);
    $obTMapeamento->recuperaOrdemPagamentoRetencao($rsOrdemPagamentoRetencao);

    foreach($rsOrdemPagamentoMovimentacao->getElementos() AS $arMovimentacao) {
        if ( $arMovimentacao["banco"] == 999 AND $arMovimentacao["agencia"] == 999999 AND $arMovimentacao["contacorrente"] == 999999999999 AND $arMovimentacao["nrdocumento"] == 999999999999999 ) {
            $vlTotalCaixa = $vlTotalCaixa + $arMovimentacao["vldocumento"];
        }
    }

    $inCount = 0;
    $opAntiga = '';
    foreach ($rsOrdemPagamento->arElementos as $arOrdemPagamento) {
        //loop do tipo 10
        $arOrdemPagamento['numero_sequencial'] = ++$inCount;

        $vl_retencao = $arOrdemPagamento['vlretencao'];
        if ($vl_retencao <> '') {
            $vl_retencao = (float) str_replace(',','.',$vl_retencao);
        } else {
            $vl_retencao = 0;
        }

        $stChave2 = $arOrdemPagamento['codprograma'].
                    $arOrdemPagamento['codunidade'].
                    $arOrdemPagamento['codfuncao'].
                    $arOrdemPagamento['codsubfuncao'].
                    $arOrdemPagamento['naturezaacao'].
                    $arOrdemPagamento['nroprojativ'].
                    $arOrdemPagamento['elementodespesa'].
                    $arOrdemPagamento['subelemento'].
                    $arOrdemPagamento['dotorigp2001'].
                    $arOrdemPagamento['nroempenho'].
                    $arOrdemPagamento['nroop'].
                    $arOrdemPagamento['nrdocumento'];

        $stChave = $arOrdemPagamento['codprograma'].
                   $arOrdemPagamento['codunidade'].
                   $arOrdemPagamento['codfuncao'].
                   $arOrdemPagamento['codsubfuncao'].
                   $arOrdemPagamento['naturezaacao'].
                   $arOrdemPagamento['nroprojativ'].
                   $arOrdemPagamento['elementodespesa'].
                   $arOrdemPagamento['subelemento'].
                   $arOrdemPagamento['dotorigp2001'].
                   $arOrdemPagamento['nroempenho'].
                   $arOrdemPagamento['nroop'];

        $stChaveSemDocumentoLiquidacao =    $arOrdemPagamento['codprograma'].
                                            $arOrdemPagamento['codunidade'].
                                            $arOrdemPagamento['codfuncao'].
                                            $arOrdemPagamento['codsubfuncao'].
                                            $arOrdemPagamento['naturezaacao'].
                                            $arOrdemPagamento['nroprojativ'].
                                            $arOrdemPagamento['elementodespesa'].
                                            $arOrdemPagamento['subelemento'].
                                            $arOrdemPagamento['dotorigp2001'].
                                            $arOrdemPagamento['nroempenho'].
                                            $arOrdemPagamento['nroop'];

        $stChaveSemDocumento = $stChave;
        $inCodOrdem = $arOrdemPagamento['nroop'];
        $stChave.= $arOrdemPagamento['nrdocumento'];

        $especificacao = $arOrdemPagamento['especificacaoop'];
        SistemaLegado::removeAcentosSimbolos($especificacao);
        $arOrdemPagamento['especificacaoop'] = $especificacao;

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arOrdemPagamento));

        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        $obExportador->roUltimoArquivo->setTipoDocumento('TCM_GO');
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezaacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroprojativ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotorigp2001");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtinscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtemissao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nomecredor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipocredor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpfcnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacaoop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(200);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_resp_op");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_resp_op");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrextraorcamentaria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

        if ( $arOPMovimentacao["banco"] == 999 AND $arOPMovimentacao["agencia"] == 999999 AND $arOPMovimentacao["contacorrente"] == 999999999999 AND $arOPMovimentacao["nrdocumento"] == 999999999999999 ) {
            $arOPMovimentacao["vldocumento"] = $vlTotalCaixa;
        }
        //loop do tipo 11 -- maior que 2010
        foreach ($rsOrdemPagamentoLiquidacoes->arElementos as $arOPLiquidacoes) {
            $stChaveLiquidacoes = $arOPLiquidacoes['codprograma'].
                                  $arOPLiquidacoes['codunidade'].
                                  $arOPLiquidacoes['codfuncao'].
                                  $arOPLiquidacoes['codsubfuncao'].
                                  $arOPLiquidacoes['naturezaacao'].
                                  $arOPLiquidacoes['nroprojativ'].
                                  $arOPLiquidacoes['elementodespesa'].
                                  $arOPLiquidacoes['subelemento'].
                                  $arOPLiquidacoes['dotorigp2001'].
                                  $arOPLiquidacoes['nroempenho'].
                                  $arOPLiquidacoes['nroop'];

            if ($stChaveSemDocumentoLiquidacao === $stChaveLiquidacoes) {
                $arOPLiquidacoes['numero_sequencial'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arOPLiquidacoes));
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezaacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroprojativ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotorigp2001");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numeroliquidacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquidacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlliquidacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlop");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(322);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }

        //loop do tipo até 2010 = 11, maior que 2010 = 12
        $boEncontrado = false;
        foreach ($rsOrdemPagamentoMovimentacao->arElementos as $arOPMovimentacao) {
            $arcomplemento = explode(' ', $arOPMovimentacao['nrdocumento']);

            foreach ($arcomplemento as $key2=>$temp2) {
                $stcomplemento = $arcomplemento[$key2];
            }

            $arOPMovimentacao['nrdocumento'] = $stcomplemento;

            $stChaveMovimentacao =  $arOPMovimentacao['codprograma'].
                                    $arOPMovimentacao['codunidade'].
                                    $arOPMovimentacao['codfuncao'].
                                    $arOPMovimentacao['codsubfuncao'].
                                    $arOPMovimentacao['naturezaacao'].
                                    $arOPMovimentacao['nroprojativ'].
                                    $arOPMovimentacao['elementodespesa'].
                                    $arOPMovimentacao['subelemento'].
                                    $arOPMovimentacao['dotorigp2001'].
                                    $arOPMovimentacao['nroempenho'].
                                    $arOPMovimentacao['nroop'];

            $stChave13 = $stChaveMovimentacao.$arOPMovimentacao['banco'].$arOPMovimentacao['agencia'].$arOPMovimentacao['contacorrente'];

            if ($stChaveSemDocumentoLiquidacao === $stChaveMovimentacao) {
                if ($arOPMovimentacao['valor_total'] == '') {
                    $arOPMovimentacao['valor_total'] = $arOPMovimentacao['vldocumento'];
                }
                if ( $arOPMovimentacao["banco"] == 999 AND $arOPMovimentacao["agencia"] == 999999 AND $arOPMovimentacao["contacorrente"] == 999999999999 AND $arOPMovimentacao["nrdocumento"] == 999999999999999 ) {
                    if ( $arOPMovimentacao['vl_associado'] != '' ) {
                        $arOPMovimentacao['vldocumento'] = $arOPMovimentacao['vl_associado'];
                        $vlFonteRecursoContaCAIXA = $arOPMovimentacao['vl_associado'];
                    } else {
                        $vlFonteRecursoContaCAIXA = $arOPMovimentacao['vldocumento'];
                    }
                    
                    $arOPMovimentacao['valor_total'] = $vlTotalCaixa;
                }
                $boEncontrado = true;
                $arOPMovimentacao['numero_sequencial'] = ++$inCount;
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arOPMovimentacao));
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezaacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroprojativ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotorigp2001");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contacorrente");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contacorrentedigverif");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrdocumento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_doc");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_total");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtemissao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vldocumento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(287);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $boEncontrado13 = false;
                foreach ($rsOrdemPagamentoFonteRecursos->arElementos as $arOPFonteRecursos) {
                    $arcomplemento = explode(' ', $arOPFonteRecursos['nrdocumento']);
                    foreach ($arcomplemento as $key2=>$temp2) {
                        $stcomplemento = $arcomplemento[$key2];
                    }
                    $arOPFonteRecursos['nrdocumento'] = $stcomplemento;
                    $stChaveFonteRecursos = $arOPFonteRecursos['codprograma'].
                                $arOPFonteRecursos['codunidade'].
                                $arOPFonteRecursos['codfuncao'].
                                $arOPFonteRecursos['codsubfuncao'].
                                $arOPFonteRecursos['naturezaacao'].
                                $arOPFonteRecursos['nroprojativ'].
                                $arOPFonteRecursos['elementodespesa'].
                                $arOPFonteRecursos['subelemento'].
                                $arOPFonteRecursos['dotorigp2001'].
                                $arOPFonteRecursos['nroempenho'].
                                $arOPFonteRecursos['nroop'];

                    $stChaveFonteRecursos .= $arOPFonteRecursos['banco'].$arOPFonteRecursos['agencia'].$arOPFonteRecursos['contacorrente']; //.$arOPFonteRecursos['vl_retencao'];

                    if ($stChave13 === $stChaveFonteRecursos) {
                        if ( $arOPFonteRecursos["banco"] == 999 AND $arOPFonteRecursos["agencia"] == 999999 AND $arOPFonteRecursos["contacorrente"] == 999999999999 AND $arOPFonteRecursos["nrdocumento"] == 999999999999999 ) {
                            $arOPFonteRecursos['vl_retencao'] = $vlFonteRecursoContaCAIXA;
                        }

                        $boEncontrado13 = true;
                        $arOPFonteRecursos['numero_sequencial'] = ++$inCount;

                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arOPFonteRecursos));
                        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezaacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroprojativ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotorigp2001");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade"); // codUnidadeFinanceira
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contacorrente");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contacorrentedigverif");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta"); //tipo_conta
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrdocumento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfonterecurso");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_retencao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(304);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    }
                }
            }
        }

        //loop do tipo até 2011 = 12, maior que 2010 = 14
        foreach ($rsOrdemPagamentoRetencao->arElementos as $arOPRetencao) {
            $stChaveRetencao = $arOPRetencao['codprograma'].
                       $arOPRetencao['codunidade'].
                       $arOPRetencao['codfuncao'].
                       $arOPRetencao['codsubfuncao'].
                       $arOPRetencao['naturezaacao'].
                       $arOPRetencao['nroprojativ'].
                       $arOPRetencao['elementodespesa'].
                       $arOPRetencao['subelemento'].
                       $arOPRetencao['dotorigp2001'].
                       $arOPRetencao['nroempenho'].
                       $arOPRetencao['nroop'];

            if ($stChaveSemDocumento === $stChaveRetencao && $inCodOrdem === $arOPRetencao['nroop']) {
                $arOPRetencao['numero_sequencial'] = ++$inCount;

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arOPRetencao));
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(04);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezaacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroprojativ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("elementodespesa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("subelemento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dotorigp2001");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_retencao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrextraorcamentaria");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlretencao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(291);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
        }
        $opAntiga = $stChaveSemDocumentoLiquidacao;
    }
    $arTemp = array();
    $arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_sequencial' => $inCount+1 );

    $arRecordSet[$stArquivo] = new RecordSet();
    $arRecordSet[$stArquivo]->preenche( $arTemp );

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(420);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
