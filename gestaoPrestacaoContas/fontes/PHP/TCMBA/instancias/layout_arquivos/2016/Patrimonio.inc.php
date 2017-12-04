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
    * Exportação de Arquivos TCMBA
    * Data de Criação : 17/02/2016   
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Jean Felipe da Silva
*/

    include_once( CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBAPatrimonio.class.php" );
    
    $obTTCMBAPatrimonio = new TTCMBAPatrimonio();
    $obTTCMBAPatrimonio->setDado('exercicio',Sessao::getExercicio());
    $obTTCMBAPatrimonio->setDado('cod_entidade',$stEntidades);
    $obTTCMBAPatrimonio->setDado('dt_inicio', $arFiltro['stDataInicial']);
    $obTTCMBAPatrimonio->setDado('dt_fim'   , $arFiltro['stDataFinal']);
    $obTTCMBAPatrimonio->setDado('inCodGestora', $inCodUnidadeGestora );

    if ($stTipoPeriodicidade == "aquisicao" || $stTipoPeriodicidade == "") {
        $obTTCMBAPatrimonio->setDado('tipoPeriodo', "dt_aquisicao" );
    } else {
        $obTTCMBAPatrimonio->setDado('tipoPeriodo', "dt_incorporacao" );
    }
    $obTTCMBAPatrimonio->recuperaDadosTribunal($rsPatrimonio);
    
    $inCount = 1;
    $inLimite = 4998;

    $arNewPatrimonio = array();
    $arRSPatrimonio = array();

    if ($rsPatrimonio->getNumLinhas() > $inLimite) {
        foreach ($rsPatrimonio->arElementos as $arElementos) {
            $arNewPatrimonio[] = $arElementos;
            if (($inCount % $inLimite) == 0 ) {
                $rsNewPatrimonio = new RecordSet();
                $rsNewPatrimonio->preenche($arNewPatrimonio);
                $arRSPatrimonio[] = $rsNewPatrimonio;
                $arNewPatrimonio = array();
            }
            $inCount++;
        }
        if (count($arNewPatrimonio) > 0) {
            $rsNewPatrimonio = new RecordSet();
            $rsNewPatrimonio->preenche($arNewPatrimonio);
            $arRSPatrimonio[] = $rsNewPatrimonio;
        }
    } else {
        $arRSPatrimonio[] = $rsPatrimonio;
    }

    foreach ($arRSPatrimonio as $inKey => $rsNewPatrimonio) {

        if ($rsPatrimonio->getNumLinhas() > $inLimite) {
            $arArquivo = explode('.',$stArquivo);
            
            $obExportador->addArquivo($arArquivo[0] . ($inKey + 1) . '.' . $arArquivo[1]);
            $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
        }

        $obExportador->roUltimoArquivo->addBloco($rsNewPatrimonio);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tombo_bem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_bem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("anterior_siga");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_bem");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("funcionario_responsavel");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_aquisicao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_baixa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        unset($rsNewPatrimonio);
    }

    unset($obTTBAPatrimonio);
    unset($rsPatrimonio);
    
?>