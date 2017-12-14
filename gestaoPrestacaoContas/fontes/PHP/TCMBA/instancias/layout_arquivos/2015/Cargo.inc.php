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

    * Página de Include Oculta - Exportação Arquivos TCM-BA

    * Data de Criação   : 02/07/2015

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * $Id: $

    
    Numérico (N) =>  Alinhado à direita e preenchido com brancos à esquerda. Caso não seja informado, deverá ser totalmente preenchido com brancos.
    AlfaNumérico (AN) =>  Alinhado à esquerda e preenchido com brancos à direita. Caso não seja informado, deverá ser totalmente preenchido com brancos. 
        Não serão permitidos os seguintes caracteres: aspas simples ('), ponto-vírgula (;), 
        e as seguintes sequências de caracteres: “--", "select", "insert", "update", "delete", "drop", "xp_". 
    Valor (V) =>  Alinhado à direita e preenchido com zeros à esquerda. Caso não seja informado, deverá ser totalmente preenchido com zeros. 
        Sempre com duas casas decimais e sem separador (vírgula ou ponto). 
        Alguns campos de valor permitem valor negativo, neste caso usar o sinal de menos antes do valor.
    Data (D) => Preencher no formato ddmmaaaa, com ano igual ou superior a 2000.
*/ 

    include_once( CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio()."/TTCMBACargo.class.php" );
    
    $arFiltro = Sessao::read('filtroRelatorio');

    list($dia,$mes,$ano) = explode("/", $stDataFinal);
    $stMesAno = $mes.$ano;

    $inCodPeriodoMovimentacao = SistemaLegado::pegaDado("cod_periodo_movimentacao"
                                                        ,"folhapagamento".$stEntidadeRH.".periodo_movimentacao"
                                                        ," WHERE to_char(dt_final, 'mmyyyy') = '".$stMesAno."'"
                                                        , $boTransacao);
    
    $obTTCMBACargo = new TTCMBACargo();
    $obTTCMBACargo->setDado("unidade_gestora",$inCodUnidadeGestora);
    $obTTCMBACargo->recuperaArquivo($rsCargo,"","",$boTransacao);

    $obExportador->roUltimoArquivo->addBloco($rsCargo);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("unidade_gestora");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cargo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_cargo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_cargo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_vagas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vagas_ocupadas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("lei");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_lei");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_vigencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    unset($inCodPeriodoMovimentacao);
    unset($obTTCMBACargo);
    unset($rsCargo);

?>