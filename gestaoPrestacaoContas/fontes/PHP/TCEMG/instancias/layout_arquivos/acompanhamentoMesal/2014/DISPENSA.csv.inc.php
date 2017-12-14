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
  * $Id: DISPENSA.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Wed, 15 Apr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
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
$obTTCEMGDispensaInexigibilidade->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGDispensaInexigibilidade->setDado('entidades', $stEntidades);
$obTTCEMGDispensaInexigibilidade->setDado('dt_inicial', $stDataInicial);
$obTTCEMGDispensaInexigibilidade->setDado('dt_final', $stDataFinal);
$obTTCEMGDispensaInexigibilidade->setDado('mes', $stMes);

//Tipo Registro 10
$obTTCEMGDispensaInexigibilidade->recuperaExportacao10($rsRecordSetDISPENSA10);

//Tipo Registro 11
$obTTCEMGDispensaInexigibilidade->recuperaExportacao11($rsRecordSetDISPENSA11);

//Tipo Registro 12
$obTTCEMGDispensaInexigibilidade->recuperaExportacao12($rsRecordSetDISPENSA12);

//Tipo Registro 13
$obTTCEMGDispensaInexigibilidade->recuperaExportacao13($rsRecordSetDISPENSA13);

//Tipo Registro 14
$obTTCEMGDispensaInexigibilidade->recuperaExportacao14($rsRecordSetDISPENSA14);

//Tipo Registro 15
$obTTCEMGDispensaInexigibilidade->recuperaExportacao15($rsRecordSetDISPENSA15);

//Tipo Registro 16
$obTTCEMGDispensaInexigibilidade->recuperaExportacao16($rsRecordSetDISPENSA16);

//Tipo Registro 17
$obTTCEMGDispensaInexigibilidade->recuperaExportacao17($rsRecordSetDISPENSA17);

//Tipo Registro 18
$obTTCEMGDispensaInexigibilidade->recuperaExportacao18($rsRecordSetDISPENSA18);

//Tipo Registro 99
$arRecordSetDISPENSA99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetDISPENSA99 = new RecordSet();
$rsRecordSetDISPENSA99->preenche($arRecordSetDISPENSA99);


$inCount=0;
$inCountRegistro10 = count($rsRecordSetDISPENSA10->getElementos());
if (count($rsRecordSetDISPENSA10->getElementos()) > 0) {
    $stChave10 = '';

    foreach ($rsRecordSetDISPENSA10->getElementos() as $arDispensa10) {
        $aux16=true;
        $inCount++;
         
        $stNumProcLic = $arDispensa10['exercicio_processo'];
        if( $arDispensa10['natureza_objeto']!= 99  AND $stChave10<>$arDispensa10['num_processo']) {
            $stChave10 = $arDispensa10['num_processo'];
            $$rsBloco = 'rsBloco_'.$inCount;
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
            
            if (count($rsRecordSetDISPENSA11->getElementos()) > 0) {
                $stChave11 = '';
                
                foreach ($rsRecordSetDISPENSA11->getElementos() as $arDispensa11){
                    $stChave11 = $arDispensa11['num_processo'];
                    
                    if ($stChave10 === $stChave11) {
                        if ( $arDispensa10['processo_por_lote']==1){
                            $rsBloco = 'rsBloco_'.$inCount;
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
                        if (count($rsRecordSetDISPENSA12->getElementos()) > 0) {
                            $stChave12 = '';
                            
                            foreach ($rsRecordSetDISPENSA12->getElementos() as $arDispensa12) {
                                $stChave12 = $arDispensa12['cod_orgao_resp']
                                            .$arDispensa12['cod_unidade_resp']
                                            .$arDispensa12['exercicio_processo']
                                            .$arDispensa12['num_processo']
                                            .$arDispensa12['tipo_processo'];
                                $stNumProcLic12=$arDispensa12['num_processo'];
                           
                                if ( $stChave11 === $stNumProcLic12 ) {
                              
                                    if ( !($stChaveAuxiliar12===($stChave12.$arDispensa12['cod_item'])) ){
                                        $stChaveAuxiliar12 = $stChave12.$arDispensa12['cod_item'];
                                        $rsBloco = 'rsBloco_'.$inCount;
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
                                        //
                                        $stNumProcLic12=$arDispensa12['num_processo'].$arDispensa12['cod_item'];
                                        if (count($rsRecordSetDISPENSA13->getElementos()) > 0) {
                                            $stChave13 = '';
                                       
                                            foreach ($rsRecordSetDISPENSA13->getElementos() as $arDispensa13) {
                                                $stChave13 = $arDispensa13['cod_orgao_resp'].$arDispensa13['cod_unidade_resp']
                                                            .$arDispensa13['exercicio_processo'].$arDispensa13['num_processo']
                                                            .$arDispensa13['tipo_processo'];
                                                $stNumProcLic13 = $arDispensa13['num_processo'].$arDispensa13['cod_item'];
                                                if ($stNumProcLic12 === $stNumProcLic13) {
                                                    if( !($stChaveAuxiliar13===($stChave13.$arDispensa13['num_lote'].$arDispensa13['cod_item']) ) ){
                                             
                                                        if ( $arDispensa10['processo_por_lote']==1){
                                                            $stChaveAuxiliar13 =  $stChave13.$arDispensa13['num_lote'].$arDispensa13['cod_item'];   
                                                            $rsBloco = 'rsBloco_'.$inCount;
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
                                                        $stNumProcLic13 = $arDispensa13['num_processo'];
                                                        if (count($rsRecordSetDISPENSA14->getElementos()) > 0) {
                                                            $stChave14 = '';
                                                   
                                                            foreach ($rsRecordSetDISPENSA14->getElementos() as $arDispensa14) {
                                                                $stChave14 = $arDispensa14['cod_orgao_resp'].$arDispensa14['cod_unidade_resp'].$arDispensa14['exercicio_processo'].$arDispensa14['num_processo'].$arDispensa14['tipo_processo'];
                                                                $stNumProcLic14 = $arDispensa14['num_processo'];
                                                                if ($stNumProcLic13 === $stNumProcLic14) {
                                                                    if(!($stChaveAuxiliar14===($stChave14.$arDispensa14['tipo_resp'].$arDispensa14['num_cpf_resp']))){
                                                                        $stChaveAuxiliar14 =  $stChave14.$arDispensa14['tipo_resp'].$arDispensa14['num_cpf_resp'];    
                                                                        $rsBloco = 'rsBloco_'.$inCount;
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
                                                                    $stNumProcLic14 =  $arDispensa14['num_processo'].$arDispensa13['cod_item'];
                                                                    if (count($rsRecordSetDISPENSA15->getElementos()) > 0) {
                                                                        $stChave15 = '';
                                                               
                                                                        foreach ($rsRecordSetDISPENSA15->getElementos() as $arDispensa15) {
                                                                            $stChave15 = $arDispensa15['cod_orgao_resp'].$arDispensa15['cod_unidade_resp']
                                                                                        .$arDispensa15['exercicio_processo'].$arDispensa15['num_processo']
                                                                                        .$arDispensa15['tipo_processo'];
                                                                            $stNumProcLic15 = $arDispensa15['num_processo'].$arDispensa15['cod_item'];
                                                                                     
                                                                            if ($stNumProcLic14 === $stNumProcLic15) {
                                                                                if (!($stChaveAuxiliar15===($stChave15.$arDispensa15['num_lote'].$arDispensa15['cod_item']))){
                                                                                    $stChaveAuxiliar15 = $stChave15.$arDispensa15['num_lote'].$arDispensa15['cod_item'];   
                                                                                    $rsBloco = 'rsBloco_'.$inCount;
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
                                                                                    
                                                                                    $stNumProcLic15 = $arDispensa15['num_processo'];
                                                                                    if (count($rsRecordSetDISPENSA16->getElementos()) > 0) {
                                                                                        $stChave16 = '';
                                                                                             
                                                                                        foreach ($rsRecordSetDISPENSA16->getElementos() as $arDispensa16) {
                                                                                            $stChave16 = $arDispensa16['cod_orgao_resp'].$arDispensa16['cod_unidade_resp']
                                                                                                        .$arDispensa16['exercicio_processo'].$arDispensa16['num_processo']
                                                                                                        .$arDispensa16['tipo_processo'];
                                                                                            $stNumProcLic16 = $arDispensa16['num_processo'];
                                                                                            
                                                                                            if ($stNumProcLic15 === $stNumProcLic16 ) {
                                                                                                if (!($stChaveAuxiliar16===($stChave16.$arDispensa16['cod_orgao']
                                                                                                                           .$arDispensa16['cod_subunidade']
                                                                                                                           .$arDispensa16['cod_funcao']
                                                                                                                           .$arDispensa16['cod_subfuncao']
                                                                                                                           .$arDispensa16['cod_programa']
                                                                                                                           .$arDispensa16['id_acao']
                                                                                                                           .$arDispensa16['id_subacao']
                                                                                                                           .$arDispensa16['natureza_despesa']
                                                                                                                           .$arDispensa16['cod_font_recursos']))
                                                                                                      and $aux16===true) {
                                                                                                    $stChaveAuxiliar16 = $stChave16
                                                                                                                        .$arDispensa16['cod_orgao']
                                                                                                                        .$arDispensa16['cod_subunidade']
                                                                                                                        .$arDispensa16['cod_funcao']
                                                                                                                        .$arDispensa16['cod_subfuncao']
                                                                                                                        .$arDispensa16['cod_programa']
                                                                                                                        .$arDispensa16['id_acao']
                                                                                                                        .$arDispensa16['id_subacao']
                                                                                                                        .$arDispensa16['natureza_despesa']
                                                                                                                        .$arDispensa16['cod_font_recursos'];
                                                                              
                                                                                                    $aux16 = false;
                                                                                                    $rsBloco = 'rsBloco_'.$inCount;
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
                                                                        
                                                                                                $stNumProcLic16 = $arDispensa16['num_processo'].$arDispensa15['cod_item'];
                                                                                                if (count($rsRecordSetDISPENSA17->getElementos()) > 0) {
                                                                                                    $stChave17 = '';
                                                                                                                
                                                                                                    foreach ($rsRecordSetDISPENSA17->getElementos() as $arDispensa17) {
                                                                                                        $stChave17 = $arDispensa17['cod_orgao_resp']
                                                                                                                    .$arDispensa17['cod_unidade_resp']
                                                                                                                    .$arDispensa17['exercicio_processo']
                                                                                                                    .$arDispensa17['num_processo']
                                                                                                                    .$arDispensa17['tipo_processo'];
                                                                                                        $stNumProcLic17 = $arDispensa17['num_processo'].$arDispensa17['cod_item'];
                                                                                          
                                                                                                        if ($stNumProcLic16 === $stNumProcLic17) {
                                                                                                            if (!($stChaveAuxiliar17===($stChave17.$arDispensa17['tipo_documento']
                                                                                                                                       .$arDispensa17['num_documento']
                                                                                                                                       .$arDispensa17['num_lote']
                                                                                                                                       .$arDispensa17['cod_item']))){
                                                                                                                $stChaveAuxiliar17 =  $stChave17.$arDispensa17['tipo_documento']
                                                                                                                                     .$arDispensa17['num_documento']
                                                                                                                                     .$arDispensa17['num_lote']
                                                                                                                                     .$arDispensa17['cod_item'];   
                                                                                                                $rsBloco = 'rsBloco_'.$inCount;
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
                                                                                                                
                                                                                                                $stNumProcLic17 = $arDispensa17['num_processo'].$arDispensa17['cod_item'];
                                                                                                                /*if (count($rsRecordSetDISPENSA18->getElementos()) > 0) {
                                                                                                                    $stChave18 = '';
                                                                                                                
                                                                                                                    foreach ($rsRecordSetDISPENSA18->getElementos() as $arDispensa18) {
                                                                                                                        $stChave18 = $arDispensa18['cod_orgao_resp'].$arDispensa18['cod_unidade_resp'].$arDispensa18['exercicio_processo'].$arDispensa18['num_processo'].$arDispensa18['tipo_processo'];
                                                                                                                        $stNumProcLic18 = $arDispensa18['num_processo'].$arDispensa18['cod_item'];
                                                                                                                        
                                                                                                                        if ($stNumProcLic17 === $stNumProcLic18) {
                                                                                                                            if(!($stChaveAuxiliar18===($stChave18.$arDispensa18['tipo_documento'].$arDispensa18['num_documento'].$arDispensa18['num_lote'].$arDispensa18['cod_item']))){
                                                                                                                                $stChaveAuxiliar18 = $stChave18.$arDispensa18['tipo_documento'].$arDispensa18['num_documento'].$arDispensa18['num_lote'].$arDispensa18['cod_item'];   
                                                                                                                                $rsBloco = 'rsBloco_'.$inCount;
                                                                                                                                unset($$rsBloco);
                                                                                                                                $$rsBloco = new RecordSet();
                                                                                                                                $$rsBloco->preenche(array($arDispensa18));
                                                                                                                
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
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
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
                                                                                                                
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_credenciamento");
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                                                                                                                                
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                                                                                                                                
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQW");
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                                                                                                                
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
                                                                                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
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
                                                                                                                            } // compara o tipo_documento, num_documento, num_lote e cod_item
                                                                                                                        } // compara chave do registro 17 com o do registro 18
                                                                                                                    } // foreach registro 18
                                                                                                                }*/ // verifica se existe registros no registro 18
                                                                                                            } // compara o tipo_documento, num_documento, num_lote e cod_item
                                                                                                        } // compara chave do registro 16 com o do registro 17
                                                                                                    } // foreach registro 17
                                                                                                } // verifica se existe registros no registro 17
                                                                                            } // compara chave do registro 15 com o do registro 16
                                                                                        } // foreach registro 16
                                                                                    } // verifica se existe registros no registro 16
                                                                                } // compara o num_lote e cod_item
                                                                            } // compara chave do registro 14 com o do registro 15
                                                                        } // foreach registro 15
                                                                    } // verifica se existe registros no registro 15
                                                                } // compara chave do registro 13 com o do registro 14
                                                            } // foreach registro 14
                                                        } // verifica se existe registros no registro 14
                                                    } // compara o num_lote e cod_item
                                                } // compara chave do registro 12 com o do registro 13
                                            } // foreach registro 13
                                        } // verifica se existe registros no registro 13
                                    } // compara o cod_item
                                } // compara chave do registro 11 com o do registro 12
                            } // foreach registro 12
                        } // verifica se existe registros no registro 12
                    } // compara chave do registro 10 com o do registro 11
                } // foreach registro 11
            } // verifica se existe registros no registro 11
        } else { // comparação do registro 10 com a notureza
            $inCountRegistro10 = $inCountRegistro10 - 1;
        } // se não vier nenhum registro vai diminuindo o contador.
    } // foreach registro 10
}
if( $inCountRegistro10 == 0 ){
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