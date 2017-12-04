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
  * Página de Include Oculta - Exportação Arquivos TCEMG - DISPENSA.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: DISPENSA.csv.inc.php 64740 2016-03-29 17:21:56Z franver $
  * $Date: 2016-03-29 14:21:56 -0300 (Tue, 29 Mar 2016) $
  * $Author: franver $
  * $Rev: 64740 $
  *
*/
/**
* DISPENSA.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGDispensaInexigibilidade.class.php";

$rsRecordSetDISPENSA10 = new RecordSet();
$rsRecordSetDISPENSA11 = new RecordSet();
$rsRecordSetDISPENSA12 = new RecordSet();
$rsRecordSetDISPENSA13 = new RecordSet();
$rsRecordSetDISPENSA14 = new RecordSet();
$rsRecordSetDISPENSA15 = new RecordSet();
$rsRecordSetDISPENSA16 = new RecordSet();
$rsRecordSetDISPENSA17 = new RecordSet();
$rsRecordSetDISPENSA18 = new RecordSet();

$obTTCEMGDispensaInexigibilidade = new TTCEMGDispensaInexigibilidade();
$obTTCEMGDispensaInexigibilidade->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGDispensaInexigibilidade->setDado('entidades' , $stEntidades);
$obTTCEMGDispensaInexigibilidade->setDado('dt_inicial', $stDataInicial);
$obTTCEMGDispensaInexigibilidade->setDado('dt_final'  , $stDataFinal);
$obTTCEMGDispensaInexigibilidade->setDado('mes'       , $stMes);

//Tipo Registro 10
$obTTCEMGDispensaInexigibilidade->recuperaExportacao10($rsRecordSetDISPENSA10);
////Tipo Registro 11
$obTTCEMGDispensaInexigibilidade->recuperaExportacao11($rsRecordSetDISPENSA11);
////Tipo Registro 12
$obTTCEMGDispensaInexigibilidade->recuperaExportacao12($rsRecordSetDISPENSA12);
////Tipo Registro 13
$obTTCEMGDispensaInexigibilidade->recuperaExportacao13($rsRecordSetDISPENSA13);
////Tipo Registro 14
$obTTCEMGDispensaInexigibilidade->recuperaExportacao14($rsRecordSetDISPENSA14);
////Tipo Registro 15
$obTTCEMGDispensaInexigibilidade->recuperaExportacao15($rsRecordSetDISPENSA15);
////Tipo Registro 16
$obTTCEMGDispensaInexigibilidade->recuperaExportacao16($rsRecordSetDISPENSA16);
////Tipo Registro 17
$obTTCEMGDispensaInexigibilidade->recuperaExportacao17($rsRecordSetDISPENSA17);
////Tipo Registro 18
//$obTTCEMGDispensaInexigibilidade->recuperaExportacao18($rsRecordSetDISPENSA18);

$inCount=0;

if( $rsRecordSetDISPENSA10->getNumLinhas() > 0 ) {
    $stChave10 = '';
    foreach ($rsRecordSetDISPENSA10->getElementos() as $arDispensa10) {
        if( $arDispensa10['natureza_objeto']!= 99  AND $stChave10!=$arDispensa10['cod_unidade_resp'].$arDispensa10['cod_orgao_resp'].$arDispensa10['exercicio_processo'].$arDispensa10['num_processo'].$arDispensa10['tipo_processo']) {
            $stChave10 = $arDispensa10['cod_unidade_resp'].$arDispensa10['cod_orgao_resp'].$arDispensa10['exercicio_processo'].$arDispensa10['num_processo'].$arDispensa10['tipo_processo'];

            $$rsBloco = 'rsBloco_'.$inCount++;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arDispensa10));
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_abertura");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_objeto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("razao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao_termo_ratificacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("veiculo_publicacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("processo_por_lote");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

            if ( $arDispensa10['processo_por_lote'] == 1 ){
                if( $rsRecordSetDISPENSA11->getNumLinhas() > 0 ) {
                    $stChave11 = '';
                    foreach ($rsRecordSetDISPENSA11->getElementos() as $arDispensa11){
                        if($stChave10 == $arDispensa11['cod_unidade_resp'].$arDispensa11['cod_orgao_resp'].$arDispensa11['exercicio_processo'].$arDispensa11['num_processo'].$arDispensa11['tipo_processo']){
                            if( $stChave11 != $arDispensa11['cod_unidade_resp'].$arDispensa11['cod_orgao_resp'].$arDispensa11['exercicio_processo'].$arDispensa11['num_processo'].$arDispensa11['tipo_processo'].$arDispensa11['num_lote'] ){
                                $stChave11 = $arDispensa11['cod_unidade_resp'].$arDispensa11['cod_orgao_resp'].$arDispensa11['exercicio_processo'].$arDispensa11['num_processo'].$arDispensa11['tipo_processo'].$arDispensa11['num_lote'];

                                $rsBloco = 'rsBloco_'.$inCount++;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arDispensa11));
                         
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                         
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                         
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                         
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_lote");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);

                            }
                        }//VAlida se o regitro faz parte do regitro 10
                    }//foreach para passar por todo registro 10
                }// VALidade se possui registro do tipo 11
            }// VALIDA se o processo é por lote
            
            if( $rsRecordSetDISPENSA12->getNumLinhas() > 0 ) {
                $stChave12 = '';
                foreach ($rsRecordSetDISPENSA12->getElementos() as $arDispensa12) {
                    if($stChave10 == $arDispensa12['cod_unidade_resp'].$arDispensa12['cod_orgao_resp'].$arDispensa12['exercicio_processo'].$arDispensa12['num_processo'].$arDispensa12['tipo_processo']){
                        if( $stChave12 != $arDispensa12['cod_unidade_resp'].$arDispensa12['cod_orgao_resp'].$arDispensa12['exercicio_processo'].$arDispensa12['num_processo'].$arDispensa12['tipo_processo'].$arDispensa12['cod_item'] ){
                            $stChave12 = $arDispensa12['cod_unidade_resp'].$arDispensa12['cod_orgao_resp'].$arDispensa12['exercicio_processo'].$arDispensa12['num_processo'].$arDispensa12['tipo_processo'].$arDispensa12['cod_item'];

                            $rsBloco = 'rsBloco_'.$inCount++;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arDispensa12));
                     
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

                        }
                    }
                }//foreach para passar por todo registro 12
            }// VALidade se possui registro do tipo 12
            
            if ( $arDispensa10['processo_por_lote'] == 1 ){
                if( $rsRecordSetDISPENSA13->getNumLinhas() > 0 ) {
                    $stChave13 = '';
                    foreach ($rsRecordSetDISPENSA13->getElementos() as $arDispensa13){
                        if($stChave10 == $arDispensa13['cod_unidade_resp'].$arDispensa13['cod_orgao_resp'].$arDispensa13['exercicio_processo'].$arDispensa13['num_processo'].$arDispensa13['tipo_processo']){
                            if( $stChave13 != $arDispensa13['cod_unidade_resp'].$arDispensa13['cod_orgao_resp'].$arDispensa13['exercicio_processo'].$arDispensa13['num_processo'].$arDispensa13['tipo_processo'].$arDispensa13['num_lote'].$arDispensa13['cod_item'] ){
                                $stChave13 = $arDispensa13['cod_unidade_resp'].$arDispensa13['cod_orgao_resp'].$arDispensa13['exercicio_processo'].$arDispensa13['num_processo'].$arDispensa13['tipo_processo'].$arDispensa13['num_lote'].$arDispensa13['cod_item'];
                                $rsBloco = 'rsBloco_'.$inCount++;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arDispensa13));
                                
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            }
                        }
                    }
                }
            }// VALIDA se o processo é por lote
            
            if( $rsRecordSetDISPENSA14->getNumLinhas() > 0 ) {
                $stChave14 = '';
                foreach ($rsRecordSetDISPENSA14->getElementos() as $arDispensa14) {
                    if($stChave10 == $arDispensa14['cod_unidade_resp'].$arDispensa14['cod_orgao_resp'].$arDispensa14['exercicio_processo'].$arDispensa14['num_processo'].$arDispensa14['tipo_processo']){
                        if( $stChave14 != $arDispensa14['cod_unidade_resp'].$arDispensa14['cod_orgao_resp'].$arDispensa14['exercicio_processo'].$arDispensa14['num_processo'].$arDispensa14['tipo_processo'].$arDispensa14['tipo_resp'].$arDispensa14['num_cpf_resp'] ){
                            $stChave14 = $arDispensa14['cod_unidade_resp'].$arDispensa14['cod_orgao_resp'].$arDispensa14['exercicio_processo'].$arDispensa14['num_processo'].$arDispensa14['tipo_processo'].$arDispensa14['tipo_resp'].$arDispensa14['num_cpf_resp'];
                            $rsBloco = 'rsBloco_'.$inCount++;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arDispensa14));
                            
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                               
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                               
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
             
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cpf_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
                        }
                    }
                }
            }

            if ( $arDispensa10['processo_por_lote'] == 1 ){
                if( $rsRecordSetDISPENSA15->getNumLinhas() > 0 ) {
                    $stChave15 = '';
                    foreach ($rsRecordSetDISPENSA15->getElementos() as $arDispensa15){
                        if($stChave10 == $arDispensa15['cod_unidade_resp'].$arDispensa15['cod_orgao_resp'].$arDispensa15['exercicio_processo'].$arDispensa15['num_processo'].$arDispensa15['tipo_processo']){
                            if( $stChave15 != $arDispensa15['cod_unidade_resp'].$arDispensa15['cod_orgao_resp'].$arDispensa15['exercicio_processo'].$arDispensa15['num_processo'].$arDispensa15['tipo_processo'].$arDispensa15['num_lote'].$arDispensa15['cod_item'] ){
                                $stChave15 = $arDispensa15['cod_unidade_resp'].$arDispensa15['cod_orgao_resp'].$arDispensa15['exercicio_processo'].$arDispensa15['num_processo'].$arDispensa15['tipo_processo'].$arDispensa15['num_lote'].$arDispensa15['cod_item'];
                                $rsBloco = 'rsBloco_'.$inCount++;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arDispensa15));
                                
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cot_precos_unitario");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                            }
                        }
                    }
                }
            }

            if( $rsRecordSetDISPENSA16->getNumLinhas() > 0 ) {
                $stChave16 = '';
                foreach ($rsRecordSetDISPENSA16->getElementos() as $arDispensa16) {
                    if($stChave10 == $arDispensa16['cod_unidade_resp'].$arDispensa16['cod_orgao_resp'].$arDispensa16['exercicio_processo'].$arDispensa16['num_processo'].$arDispensa16['tipo_processo']){
                        if( $stChave16 != $arDispensa16['cod_unidade_resp'].$arDispensa16['cod_orgao_resp'].$arDispensa16['exercicio_processo'].$arDispensa16['num_processo'].$arDispensa16['tipo_processo'].$arDispensa16['cod_orgao'].$arDispensa16['cod_subunidade'].$arDispensa16['cod_funcao'].$arDispensa16['cod_subfuncao'].$arDispensa16['cod_programa'].$arDispensa16['id_acao'].$arDispensa16['id_sub_acao'].$arDispensa16['natureza_despesa'].$arDispensa16['cod_font_recurso'] ){
                            $stChave16 = $arDispensa16['cod_unidade_resp'].$arDispensa16['cod_orgao_resp'].$arDispensa16['exercicio_processo'].$arDispensa16['num_processo'].$arDispensa16['tipo_processo'].$arDispensa16['cod_orgao'].$arDispensa16['cod_subunidade'].$arDispensa16['cod_funcao'].$arDispensa16['cod_subfuncao'].$arDispensa16['cod_programa'].$arDispensa16['id_acao'].$arDispensa16['id_sub_acao'].$arDispensa16['natureza_despesa'].$arDispensa16['cod_font_recurso'];
                            $rsBloco = 'rsBloco_'.$inCount++;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arDispensa16));
                            
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                                     
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subunidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_subfuncao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_programa");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("id_acao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("id_subacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_despesa");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_recurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                        }
                    }
                }
            }

            if( $rsRecordSetDISPENSA17->getNumLinhas() > 0 ) {
                $stChave17 = '';
                foreach ($rsRecordSetDISPENSA17->getElementos() as $arDispensa17) {
                    if($stChave10 == $arDispensa17['cod_unidade_resp'].$arDispensa17['cod_orgao_resp'].$arDispensa17['exercicio_processo'].$arDispensa17['num_processo'].$arDispensa17['tipo_processo']){
                        if( $stChave17 != $arDispensa17['cod_unidade_resp'].$arDispensa17['cod_orgao_resp'].$arDispensa17['exercicio_processo'].$arDispensa17['num_processo'].$arDispensa17['tipo_processo'].$arDispensa17['num_lote'].$arDispensa17['cod_item'].$arDispensa17['tipo_documento'].$arDispensa17['num_documento'] ){
                            $stChave17 = $arDispensa17['cod_unidade_resp'].$arDispensa17['cod_orgao_resp'].$arDispensa17['exercicio_processo'].$arDispensa17['num_processo'].$arDispensa17['tipo_processo'].$arDispensa17['num_lote'].$arDispensa17['cod_item'].$arDispensa17['tipo_documento'].$arDispensa17['num_documento'];

                            $rsBloco = 'rsBloco_'.$inCount++;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arDispensa17));
                    
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco );
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_resp");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_inss");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_inss");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_inss");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_fgts");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_fgts");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_fgts");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cndt");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                        }
                    }
                }
            }
            

            
        }// Valida chaves se estão repetidas e se a natureza é do tipo 
    }//foreach para passar por todo registro 10
}// Valida se possui registro de tipo 10

if( $rsRecordSetDISPENSA10->getNumLinhas() == 0 ){
    //Tipo Registro 99
    $arRecordSetDISPENSA99 = array(
        '0' => array(
            'tipo_registro' => '99',
        )
    );

    $rsRecordSetDISPENSA99 = new RecordSet();
    $rsRecordSetDISPENSA99->preenche($arRecordSetDISPENSA99);

    $obExportador->roUltimoArquivo->addBloco($rsRecordSetDISPENSA99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetDISPENSA10 =null;
$rsRecordSetDISPENSA11 =null;
$rsRecordSetDISPENSA12 =null;
$rsRecordSetDISPENSA13 =null;
$rsRecordSetDISPENSA14 =null;
$rsRecordSetDISPENSA15 =null;
$rsRecordSetDISPENSA16 =null;
$rsRecordSetDISPENSA17 =null;
$rsRecordSetDISPENSA18 =null;
$rsRecordSetDISPENSA99 =null;
$obTTCEMGDispensaInexigibilidade = null;
?>