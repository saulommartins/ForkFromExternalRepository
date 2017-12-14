

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
  * Página de Include Oculta - Exportação Arquivos TCEMG  FOLHA PAGAMENTO - FLPO.csv
  * Data de Criação: 23/03/2016
  * @author Analista:      Dagiane Vieira
  * @author Desenvolvedor: Evandro Melos
  * $Id: FLPGO.inc.php 66549 2016-09-21 13:15:28Z evandro $
  *
*/

include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoFolhaPagamento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$stFiltroPeriodo = " AND dt_final BETWEEN TO_DATE('".$arDatasInicialFinal['stDtInicial']."', 'dd/mm/yyyy') AND TO_DATE('".$arDatasInicialFinal['stDtFinal']."', 'dd/mm/yyyy')";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);

if ( $rsPeriodoMovimentacao->getNumLinhas() > 0 ) {

    $stDataInicialPeriodo = $arDatasInicialFinal['stDtInicial'];
    $stDataInicialPeriodo = SistemaLegado::dataToSql($stDataInicialPeriodo);
    $stDataFinalPeriodo = $arDatasInicialFinal['stDtFinal'];
    $stDataFinalPeriodo = SistemaLegado::dataToSql($stDataFinalPeriodo);
    
    $arFiltro = Sessao::read('filtroRelatorio');
    
    foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
        if ( $inCodEntidade == $inCodEntidadePrefeitura ) {
            Sessao::setEntidade('');
        }else{        
            Sessao::setEntidade($inCodEntidade);
        }
    
        $obTTCEMGArquivoFolhaPagamento = new TTCEMGArquivoFolhaPagamento();
        $obTTCEMGArquivoFolhaPagamento->setDado('exercicio'                , $stExercicioFiltro );
        $obTTCEMGArquivoFolhaPagamento->setDado('data_inicial_periodo'     , $stDataInicialPeriodo );
        $obTTCEMGArquivoFolhaPagamento->setDado('data_final_periodo'       , $stDataFinalPeriodo );
        $obTTCEMGArquivoFolhaPagamento->setDado('cod_periodo_movimentacao' , $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') );
        $obTTCEMGArquivoFolhaPagamento->setDado('cod_entidade'             , $inCodEntidade );
        $obTTCEMGArquivoFolhaPagamento->setDado('mes'                      , $inMes );
        
        $obTTCEMGArquivoFolhaPagamento->recuperaDadosExportacaoFolhaPagamento10($rsRecordSet10,$boTransacao);
        
        $obTTCEMGArquivoFolhaPagamento->recuperaDadosExportacaoFolhaPagamento11($rsRecordSet11,$boTransacao);
        
        $obTTCEMGArquivoFolhaPagamento->recuperaDadosExportacaoFolhaPagamento12($rsRecordSet12,$boTransacao);
        
        if ( $rsRecordSet10->getNumLinhas() > 0) {
            foreach ($rsRecordSet10->getElementos() as $arFolha10) {
                $inCount++;
                $stChave10 = $arFolha10['num_cpf'].$arFolha10['cod_reduzido_pessoa'];
        
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arFolha10));
        
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cpf");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_pessoa");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("regime");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pagamento");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao_servidor_pensionista");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_situacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(150);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_concessao_aposentadoria_pensao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(120);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_sigla_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(150);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("requisito_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_cessao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_lotacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_horas_semanais");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_efetivacao_exercicio_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_exclusao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_remuneracao_bruto");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(17);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_saldo_liquido");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_remuneracao_liquida");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(17);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_obrigacoes");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(17);
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                //REGISTRO 11
                $stChave11 = '';
                $stChave11Unica = '';
        
                foreach ($rsRecordSet11->getElementos() as $arFolha11) {
                    $inCount++;
                    $stChave11 = $arFolha11['num_cpf'].$arFolha11['cod_reduzido_pessoa'];
                    if ($stChave10 === $stChave11) {
                        //Registro unico 11
                        $stChave11Aux = $arFolha11['num_cpf'].$arFolha11['cod_reduzido_pessoa'].$arFolha11['tipo_remuneracao'];
                        //Caso a chave seja igual ignorar pq pode ir mais de um tipo_remuneracao se ele for 99
                        if ($stChave11Unica !== $stChave11Aux || ($stChave11Unica == $stChave11Aux && $arFolha11['tipo_remuneracao'] == 99) ||  ($arFolha11['tipo_remuneracao'] == 9) ) {
                            $stChave11Unica = $stChave11Aux;
        
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arFolha11));
        
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cpf");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
           
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_pessoa");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_remuneracao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_outros");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(150);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_remuneracao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(17);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        }
                    }
                }//foreach 11
                        
                //REGISTRO 12
                $stChave12 = '';
                $stChave12Unica = '';
            
                foreach ($rsRecordSet12->getElementos() as $arFolha12) {                        
                    $inCount++;
                    $stChave12 = $arFolha12['num_cpf'].$arFolha12['cod_reduzido_pessoa'];
                    if ($stChave10 === $stChave12) {
                        //Registro unico 12
                        $stChave12Aux = $arFolha12['num_cpf'].$arFolha12['cod_reduzido_pessoa'].$arFolha12['tipo_desconto'];
                        //Caso a chave seja igual ignorar pq pode ir mais de um tipo_desconto se ele for 99
                        if ($stChave12Unica !== $stChave12Aux || ($stChave12Unica == $stChave12Aux && $arFolha12['tipo_desconto'] == 99) ) {
                            $stChave12Unica = $stChave12Aux;
            
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arFolha12));
                        
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cpf");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_pessoa");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                         
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_desconto");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_desconto");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(17);
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        }
                    }
                }//foreach 12
                
            }//foreach 10
        
        } else {
        
            //Tipo Registro 99
            $arRecuperaFLPO99[] = array('tipo_registro' => '99');
        
            $rsRecuperaFLPO99 = new RecordSet();
            $rsRecuperaFLPO99->preenche($arRecuperaFLPO99);
        
            $obExportador->roUltimoArquivo->addBloco($rsRecuperaFLPO99);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        }
    }//END FOREACH

}else{
        //Tipo Registro 99
        $arRecuperaFLPO99[] = array('tipo_registro' => '99');
    
        $rsRecuperaFLPO99 = new RecordSet();
        $rsRecuperaFLPO99->preenche($arRecuperaFLPO99);
    
        $obExportador->roUltimoArquivo->addBloco($rsRecuperaFLPO99);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}//END IF


?>