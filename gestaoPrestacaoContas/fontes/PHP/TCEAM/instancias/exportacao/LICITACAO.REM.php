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
    /*
    include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMItens.class.php" );
    $obTMapeamento = new TTCEAMItens();
    $obTMapeamento->setDado('exercicio'  , Sessao::getExercicio() );
    $obTMapeamento->setDado('inMes'      , $inMes );
    $obTMapeamento->setDado('stEntidades', $stEntidades );
    $obTMapeamento->recuperaItemLicitacaoREM($arRecordSet[$stArquivo]);
    //$obTMapeamento->recuperaLicitacao($arRecordSet[$stArquivo]);
    $obTMapeamento->debug();
    die();
    */
    
    
    include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMLicitacao.class.php';

    $obTTCEAMLicitacao = new TTCEAMLicitacao();

    $obTTCEAMLicitacao->setDado('exercicio'   , Sessao::getExercicio());
    $obTTCEAMLicitacao->setDado('cod_entidade', $stEntidades);
    $obTTCEAMLicitacao->setDado('mes'         , $inMes);
    $obTTCEAMLicitacao->recuperaLicitacao($arRecordSet[$stArquivo]);

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
        $arDados[$inDados]["num_diario_oficial"]   = $dados["num_diario_oficial"];	
        $arDados[$inDados]["dt_publicacao_edital"] = $dados["dt_publicacao_edital"];
        $arDados[$inDados]["compras_modalidade"]   = $dados["compras_modalidade"];
        $arDados[$inDados]["descricao_objeto"]     = mb_convert_encoding($dados["descricao_objeto"], 'UTF-8');
        $arDados[$inDados]["total_previsto"]       = $dados["total_previsto"];
        $arDados[$inDados]["numero_edital"]        = $dados["numero_edital"];
        $arDados[$inDados]["controle_item_lote"]   = $dados["controle_item_lote"];
        $arDados[$inDados]["tipo_licitacao"]       = $dados["tipo_licitacao"];
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
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_diario_oficial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao_edital");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("compras_modalidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_objeto");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(300);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_previsto");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_edital");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
?>