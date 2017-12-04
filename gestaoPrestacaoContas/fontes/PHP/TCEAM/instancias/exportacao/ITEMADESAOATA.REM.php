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
    $obTMapeamento->recuperaItemAdesaoAtaREM($arRecordSet[$stArquivo]);
    
    //gerando sequencial
    $cont = 1;
    $arRecordSet[$stArquivo]->setPrimeiroElemento();
    $numHomologacao = $arRecordSet[$stArquivo]->getCampo('processo');

    while (!$arRecordSet[$stArquivo]->eof()) {

        if ($numHomologacao == $arRecordSet[$stArquivo]->getCampo('processo')) {
            $arRecordSet[$stArquivo]->setCampo('sequencial', $cont);
        } else {
            $cont = 1;
            $numHomologacao = $arRecordSet[$stArquivo]->getCampo('processo');
            $arRecordSet[$stArquivo]->setCampo('sequencial', $cont);
        }

        $cont++;

        $numHomologacao = $arRecordSet[$stArquivo]->getCampo('processo');

        $arRecordSet[$stArquivo]->proximo();
    }
  
    $inDados =0;
    foreach($arRecordSet[$stArquivo]->getElementos() as $dados){

        $arDados[$inDados]["processo"] = $dados["processo"];
        $arDados[$inDados]["num_ata"] =  $dados["num_ata"];	
        $arDados[$inDados]["quantidade"] = $dados["quantidade"];
        $arDados[$inDados]["sequencial"] =  $dados["sequencial"];
        $arDados[$inDados]["valor"] =  $dados["valor"];
        $arDados[$inDados]["unidade_medida"] =   mb_convert_encoding($dados["unidade_medida"], 'UTF-8');
        $arDados[$inDados]["descricao"] =  mb_convert_encoding($dados["descricao"], 'UTF-8');
        $arDados[$inDados]["controle_item_lote"] =  $dados["controle_item_lote"];
        $inDados++;
    }    
 
    unset($rsBloco);
    $rsBloco = new RecordSet();
    $rsBloco->preenche($arDados);

    $obExportador->roUltimoArquivo->addBloco($rsBloco);
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_AM');
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("processo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_ata");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(18);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_medida");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(300);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("controle_item_lote");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
