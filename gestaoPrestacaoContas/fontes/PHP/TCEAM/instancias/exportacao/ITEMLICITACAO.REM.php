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
    * Página de Include Oculta - Exportação Arquivos

    * Data de Criação   : 29/03/2011

    * @author Desenvolvedor: Davi Ritter Aroldi
    * @ignore
*/
    include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMItens.class.php" );
    $obTMapeamento = new TTCEAMItens();
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('inMes'      , $inMes );
    $obTMapeamento->setDado('stEntidades', $stEntidades );
    $obTMapeamento->recuperaItemLicitacaoREM($arRecordSet[$stArquivo]);
    
    //gerando sequencial
    $cont = 1;
    $arRecordSet[$stArquivo]->setPrimeiroElemento();
    $numHomologacao = $arRecordSet[$stArquivo]->getCampo('processo_licitatorio');

    while (!$arRecordSet[$stArquivo]->eof()) {

        if ($numHomologacao == $arRecordSet[$stArquivo]->getCampo('processo_licitatorio')) {
            $arRecordSet[$stArquivo]->setCampo('sequencial', $cont);
        } else {
            $cont = 1;
            $numHomologacao = $arRecordSet[$stArquivo]->getCampo('processo_licitatorio');
            $arRecordSet[$stArquivo]->setCampo('sequencial', $cont);
        }

        $cont++;

        $numHomologacao = $arRecordSet[$stArquivo]->getCampo('processo_licitatorio');

        $arRecordSet[$stArquivo]->proximo();
    }
  
    $inDados =0;
    foreach($arRecordSet[$stArquivo]->getElementos() as $dados){

        $arDados[$inDados]["processo_licitatorio"] = $dados["processo_licitatorio"];
        $arDados[$inDados]["descricao"] =  utf8_decode($dados["descricao"]);	
        $arDados[$inDados]["quantidade_itens"] = $dados["quantidade_itens"];
        $arDados[$inDados]["data_homologacao"] =  $dados["data_homologacao"];
        $arDados[$inDados]["data_publicacao_homologacao"] =  $dados["data_publicacao_homologacao"];
        $arDados[$inDados]["unidade_medida"] =   utf8_decode($dados["unidade_medida"]);
        $arDados[$inDados]["status_item"] =  $dados["status_item"];
        $arDados[$inDados]["controle_item_lote"] =  $dados["controle_item_lote"];
        $arDados[$inDados]["sequencial"] =  $dados["sequencial"];
        $inDados++;
    }    
 
    unset($rsBloco);
    $rsBloco = new RecordSet();
    $rsBloco->preenche($arDados);

    $obExportador->roUltimoArquivo->addBloco($rsBloco);
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("processo_licitatorio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(300);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_itens");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_homologacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_publicacao_homologacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_medida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("status_item");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("controle_item_lote");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
