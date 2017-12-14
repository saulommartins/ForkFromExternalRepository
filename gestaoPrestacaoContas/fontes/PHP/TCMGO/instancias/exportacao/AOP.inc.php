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

    $Id: AOP.inc.php 60941 2014-11-25 18:13:25Z franver $

    * Casos de uso: uc-06.04.00
*/

    include_once( CAM_GPC_TGO_MAPEAMENTO."TTGOAOP.class.php" );
    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $obTMapeamento = new TTGOAOP();
    $obTMapeamento->setDado('exercicio'   , Sessao::getExercicio() );
    $obTMapeamento->setDado('dtInicio'    , $arFiltroRelatorio['stDataInicial'] );
    $obTMapeamento->setDado('dtFim'   	  , $arFiltroRelatorio['stDataFinal']   );
    $obTMapeamento->setDado('dtInicioAnt' , SistemaLegado::somaOuSubtraiData($arFiltroRelatorio['stDataInicial'],false,1,'month')  );
    $obTMapeamento->setDado('dtFimAnt'    , SistemaLegado::somaOuSubtraiData($arFiltroRelatorio['stDataInicial'],false,1,'day')   );
    $obTMapeamento->setDado('cod_entidade', $stEntidades );
    $obTMapeamento->recuperaAnulacaoOrdemPagamento( $rsAnulacaoOrdemPag );

    if (Sessao::getExercicio() > 2011) {
        $obTMapeamento->recuperaLiquidacaoOrdemPagamento( $rsLiquidacaoOrdemPag );
        $obTMapeamento->recuperaFinanceiraOrdemPagamento( $rsFinanceiraOrdemPag );
        $obTMapeamento->recuperaRecursoOrdemPagamento( $rsRecursoOrdemPag );
        $obTMapeamento->recuperaRetencaoOrdemPagamento( $rsRetencaoOrdemPag );
        
		foreach($rsFinanceiraOrdemPag->getElementos() AS $arFinanceiraOrdemPag) {
			if ( $arFinanceiraOrdemPag["banco"] == 999 AND $arFinanceiraOrdemPag["agencia"] == 999999 AND $arFinanceiraOrdemPag["conta_corrente"] == 999999999999 AND $arFinanceiraOrdemPag["nrdocumento"] == 999999999999999 ) {
				$vlTotalCaixa = $vlTotalCaixa + $arFinanceiraOrdemPag["vldocumento"];
			}
		}
        
    }

    $i = 0;

    if (Sessao::getExercicio() > '2008') {
        //REGISTRO 10
        foreach ($rsAnulacaoOrdemPag->arElementos as $arAnulacaoOrdemPag) {
            $chave = $arAnulacaoOrdemPag['codprograma'].
                     $arAnulacaoOrdemPag['codunidade'].
                     $arAnulacaoOrdemPag['codfuncao'].
                     $arAnulacaoOrdemPag['codsubfuncao'].
                     $arAnulacaoOrdemPag['naturezaacao'].
                     $arAnulacaoOrdemPag['nroprojativ'].
                     $arAnulacaoOrdemPag['elementodespesa'].
                     $arAnulacaoOrdemPag['subelemento'].
                     $arAnulacaoOrdemPag['dotorigp2001'].
                     $arAnulacaoOrdemPag['nroempenho'].
                     $arAnulacaoOrdemPag['nroop'].
                     $arAnulacaoOrdemPag['nranulacaoop'];

            $arAnulacaoOrdemPag['numero_sequencial'] = ++$i;

            $rsBloco = 'rsBloco_'.$i;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arAnulacaoOrdemPag));

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

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtanulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nranulacaoop");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

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

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanuladoop");
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
            
            if (Sessao::getExercicio() >= 2014) {
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrextraorcamentaria");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
            }
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

            if (Sessao::getExercicio() > 2011) {
                //REGISTRO 11
                foreach ($rsLiquidacaoOrdemPag->arElementos as $arLiquidacaoOrdemPag) {
                    $chaveLiquidacao = $arLiquidacaoOrdemPag['codprograma'].
                                       $arLiquidacaoOrdemPag['codunidade'].
                                       $arLiquidacaoOrdemPag['codfuncao'].
                                       $arLiquidacaoOrdemPag['codsubfuncao'].
                                       $arLiquidacaoOrdemPag['naturezaacao'].
                                       $arLiquidacaoOrdemPag['nroprojativ'].
                                       $arLiquidacaoOrdemPag['elementodespesa'].
                                       $arLiquidacaoOrdemPag['subelemento'].
                                       $arLiquidacaoOrdemPag['dotorigp2001'].
                                       $arLiquidacaoOrdemPag['nroempenho'].
                                       $arLiquidacaoOrdemPag['nroop'].
                                       $arLiquidacaoOrdemPag['nranulacaoop'];

                    if ($chave === $chaveLiquidacao) {
                        $arLiquidacaoOrdemPag['numero_sequencial'] = ++$i;

                        $rsBloco = 'rsBloco_'.$i;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arLiquidacaoOrdemPag));

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

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtanulacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nranulacaoop");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrliquidacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtliquidacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanulacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        if( Sessao::getExercicio() < 2014 ) {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(281);
                        } else {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(287);
                        }
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    }
                }

                //REGISTRO 12
                foreach ($rsFinanceiraOrdemPag->arElementos as $arFinanceiraOrdemPag) {
                    $chaveFinanceira = $arFinanceiraOrdemPag['codprograma'].     
                                       $arFinanceiraOrdemPag['codunidade'].
                                       $arFinanceiraOrdemPag['codfuncao'].
                                       $arFinanceiraOrdemPag['codsubfuncao'].
                                       $arFinanceiraOrdemPag['naturezaacao'].
                                       $arFinanceiraOrdemPag['nroprojativ'].
                                       $arFinanceiraOrdemPag['elementodespesa'].
                                       $arFinanceiraOrdemPag['subelemento'].
                                       $arFinanceiraOrdemPag['dotorigp2001'].
                                       $arFinanceiraOrdemPag['nroempenho'].
                                       $arFinanceiraOrdemPag['nroop'].
                                       $arFinanceiraOrdemPag['nranulacaoop'];

                    $chaveFinanceiraRecurso = $chaveFinanceira.
                                              $arFinanceiraOrdemPag['codunidadefinanceira'].
                                              $arFinanceiraOrdemPag['banco'].
                                              $arFinanceiraOrdemPag['agencia'].
                                              $arFinanceiraOrdemPag['conta_corrente'].
                                              $arFinanceiraOrdemPag['digiverif'].
                                              $arFinanceiraOrdemPag['tipoconta'].
                                              $arFinanceiraOrdemPag['nrodocumento'];

                    if ($chave === $chaveFinanceira) {
                        $boVerificaVlZero = true;
                        if ( $arFinanceiraOrdemPag["valor_total"] != '' ) {
                            $arFinanceiraOrdemPag['vldocumento'] = $arFinanceiraOrdemPag["valor_total"];
                        }
                        if ( $arFinanceiraOrdemPag["banco"] == 999 AND $arFinanceiraOrdemPag["agencia"] == 999999 AND $arFinanceiraOrdemPag["conta_corrente"] == 999999999999 AND $arFinanceiraOrdemPag["nrodocumento"] == 999999999999999 ) {
                            
                            
                            $vlAnuladoContaCAIXA = $arFinanceiraOrdemPag['vlanulacao'] - $arFinanceiraOrdemPag['vl_retencao'];
                            //$vlAnuladoContaCAIXA = $arFinanceiraOrdemPag['vl_retencao'];

                            if ( $vlAnuladoContaCAIXA < 0) {
                                $vlAnuladoContaCAIXA = ($vlAnuladoContaCAIXA*-1);
                            }
                            
                            if ($arFinanceiraOrdemPag['vlanulacao'] == $arFinanceiraOrdemPag['vl_retencao']) {
                                $boVerificaVlZero = false;
                            }
                            $arFinanceiraOrdemPag['vlanulacao'] = $vlAnuladoContaCAIXA;
                            $vlValorAnuladoFR = $vlAnuladoContaCAIXA;
                            $arFinanceiraOrdemPag['vldocumento'] = $arFinanceiraOrdemPag['total_pago'];
					    }
                        
                        if ($boVerificaVlZero == true) {
                        $arFinanceiraOrdemPag['numero_sequencial'] = ++$i;

                        $rsBloco = 'rsBloco_'.$i;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arFinanceiraOrdemPag));

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

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtanulacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nranulacaoop");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadefinanceira");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digiverif");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoconta");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodocumento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipodocumento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vldocumento");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtemissao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanulacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                        if ( Sessao::getExercicio() < 2014 ){
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(233);
                        } else {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(239);
                        }

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

                        //REGISTRO 13
                        foreach ($rsRecursoOrdemPag->arElementos as $arRecursoOrdemPag) {
                            $chaveRecurso = $arRecursoOrdemPag['codprograma'].
                                            $arRecursoOrdemPag['codunidade'].
                                            $arRecursoOrdemPag['codfuncao'].
                                            $arRecursoOrdemPag['codsubfuncao'].
                                            $arRecursoOrdemPag['naturezaacao'].
                                            $arRecursoOrdemPag['nroprojativ'].
                                            $arRecursoOrdemPag['elementodespesa'].
                                            $arRecursoOrdemPag['subelemento'].
                                            $arRecursoOrdemPag['dotorigp2001'].
                                            $arRecursoOrdemPag['nroempenho'].
                                            $arRecursoOrdemPag['nroop'].
                                            $arRecursoOrdemPag['nranulacaoop'];
                                            
                            $chaveRecurso = $chaveRecurso.
                                            $arRecursoOrdemPag['codunidadefinanceira'].
                                            $arRecursoOrdemPag['banco'].
                                            $arRecursoOrdemPag['agencia'].
                                            $arRecursoOrdemPag['conta_corrente'].
                                            $arRecursoOrdemPag['digiverif'].
                                            $arRecursoOrdemPag['tipoconta'].
                                            $arRecursoOrdemPag['nrodocumento'];


                            if ($chaveFinanceiraRecurso === $chaveRecurso) {
                                if ( $arRecursoOrdemPag["banco"] == 999 AND $arRecursoOrdemPag["agencia"] == 999999 AND $arRecursoOrdemPag["conta_corrente"] == 999999999999 AND $arRecursoOrdemPag["nrodocumento"] == 999999999999999 ) {
                                    $arRecursoOrdemPag['vlanulacaofr'] = $vlValorAnuladoFR;
					            }
                                
                                $arRecursoOrdemPag['numero_sequencial'] = ++$i;

                                $rsBloco = 'rsBloco_'.$i;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arRecursoOrdemPag));

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

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtanulacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nranulacaoop");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadefinanceira");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digiverif");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoconta");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodocumento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfonterecurso");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanulacaofr");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);

                                if ( Sessao::getExercicio() < 2014 ){
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
                                } else {
                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(256);
                                }

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                            }
                        }
                        }
                    }
                }

                //REGISTRO 14
                foreach ($rsRetencaoOrdemPag->arElementos as $arRetencaoOrdemPag) {
                    $chaveRetencao = $arRetencaoOrdemPag['codprograma'].
                                     $arRetencaoOrdemPag['codunidade'].
                                     $arRetencaoOrdemPag['codfuncao'].
                                     $arRetencaoOrdemPag['codsubfuncao'].
                                     $arRetencaoOrdemPag['naturezaacao'].
                                     $arRetencaoOrdemPag['nroprojativ'].
                                     $arRetencaoOrdemPag['elementodespesa'].
                                     $arRetencaoOrdemPag['subelemento'].
                                     $arRetencaoOrdemPag['dotorigp2001'].
                                     $arRetencaoOrdemPag['nroempenho'].
                                     $arRetencaoOrdemPag['nroop'].
                                     $arRetencaoOrdemPag['nranulacaoop'];

                    if ($chave === $chaveRetencao) {
                        $arRetencaoOrdemPag['numero_sequencial'] = ++$i;

                        $rsBloco = 'rsBloco_'.$i;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arRetencaoOrdemPag));

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

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtanulacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(08);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nranulacaoop");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporetencao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanulacaoretencao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                        
                        if (Sessao::getExercicio() >= 2014) {
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrextraorcamentaria");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                        }

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(293);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
                    }
                }
            }
        }
    } else {
        $i = 0;
    }

    $arTemp[0] = array( 'tipo_registro'=> '99', 'espacador'=> '', 'numero_sequencial' => $i+1 );

    $arRecordSet[$stArquivo] = new RecordSet();
    $arRecordSet[$stArquivo]->preenche( $arTemp );

    $obExportador->roUltimoArquivo->addBloco($arRecordSet[$stArquivo]);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    if (Sessao::getExercicio() >= 2014) {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(383);
    } else {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espacador");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(377);        
    }
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
