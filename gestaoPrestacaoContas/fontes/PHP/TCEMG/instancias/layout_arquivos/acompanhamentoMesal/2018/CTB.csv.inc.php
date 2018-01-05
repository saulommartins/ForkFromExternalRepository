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
  * Página de Include Oculta - Exportação Arquivos TCEMG - LQD.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Lisiane Morais
  *
  * @ignore
  * $Id: CTB.csv.inc.php 64257 2015-12-22 16:26:04Z evandro $
  * $Date:$
  * $Author:$
  * $Rev:$
  *
*/
/**
* CTB.csv | Autor : Carolina Schwaab Marcal
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGCTB.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoCTB.class.php";

$rsRecordSet10 = new RecordSet();
$rsRecordSet20 = new RecordSet();
$rsRecordSet21 = new RecordSet();
$rsRecordSet22 = new RecordSet();

$obTTCEMGCTB = new TTCEMGCTB();
$obTTCEMGCTB->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGCTB->setDado('entidades', $stEntidades);
$obTTCEMGCTB->setDado('mes', $stMes);
$obTTCEMGCTB->setDado('dtInicio', $stDataInicial);
$obTTCEMGCTB->setDado('dtFim',   $stDataFinal);

//Tipo Registro 10
$obTTCEMGCTB->recuperaContasBancarias10($rsRecordSet10);
//Tipo Registro 99
$arRecordSetCTB99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSet99 = new RecordSet();
$rsRecordSet99->preenche($arRecordSetCTB99);

$inCount=0;    
if (count($rsRecordSet10->arElementos) > 0) {
    $stChave10 = '';
    $inSequencia = 0;
    foreach ($rsRecordSet10->arElementos as $arCTB10) {
        $inCount++;
        $boAchou = false;
        $boRegistroNaoEnviado = true;
        
        if( $stChave10 <> $arCTB10['cod_ctb'].$arCTB10['tipo_conta'].$arCTB10['cod_ctb_view']){
            $stChave10 = $arCTB10['cod_ctb'].$arCTB10['tipo_conta'].$arCTB10['cod_ctb_view'];
            
            //VERIFICA SE O REGISTRO 10 VAI SER ENVIADO
            if($arCTB10['ano']!=''&&$arCTB10['mes']!=''){
                if($arCTB10['ano']!=Sessao::getExercicio()||$arCTB10['mes']!=$stMes){
                    $boRegistroNaoEnviado = false;
                }
            }

            //VERIFICA SE O REGISTRO 10 VAI SER CADASTRADO
            $obTTCEMGArquivoCTB = new TTCEMGArquivoCTB();
            
            $rsValidaCTBView = new RecordSet();
            
            $obTTCEMGArquivoCTB->setDado('cod_ctb_view' , $arCTB10['cod_ctb_view']);
            $obTTCEMGArquivoCTB->recuperaPorChave( $rsValidaCTBView, $boTransacao );
            
            if ( $rsValidaCTBView->getNumLinhas() < 0 ) {
                $obTTCEMGArquivoCTB->setDado('ano'          , Sessao::getExercicio());
                $obTTCEMGArquivoCTB->setDado('mes'          , $stMes);
                $obTTCEMGArquivoCTB->setDado('cod_ctb'      , $arCTB10['cod_ctb']);
                $obTTCEMGArquivoCTB->setDado('cod_orgao'    , $arCTB10['cod_orgao']);
                $obTTCEMGArquivoCTB->setDado('tipo_conta'   , $arCTB10['tipo_conta']);
                if($arCTB10['tipo_aplicacao']!=''){
                    $obTTCEMGArquivoCTB->setDado('tipo_aplicacao', $arCTB10['tipo_aplicacao']);
                }
            
                $obTTCEMGArquivoCTB->inclusao($boTransacao);
            }

            //SE O REGISTRO 10 JA FOI ENVIADO NOS MESES ANTERIORES, ENVIA A PARTIR REG20  
            if($boRegistroNaoEnviado) {
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arCTB10));
                
                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_agencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_bancaria");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("digito_verificador_conta_bancaria");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_aplicacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_seq_aplicacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_conta_bancaria");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_convenio");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_convenio");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_assinatura_convenio");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);
            }
        }
    }
}

 //Tipo Registro 20
$obTTCEMGCTB->recuperaContasBancarias20($rsRecordSet20);
//Tipo Registro 21
$obTTCEMGCTB->recuperaContasBancarias21($rsRecordSet21);
//Tipo Registro 22
$obTTCEMGCTB->recuperaContasBancarias22($rsRecordSet22);

if (count($rsRecordSet20->getNumLinhas()) > 0) {
    $stChaveComp20 = '';
    foreach ($rsRecordSet20->arElementos as $arCTB20) {
        $inCount++;
        if(!($stChaveComp20===$arCTB20['cod_ctb'].$arCTB20['cod_fonte_recursos'])) {
            $stChaveComp20  = $arCTB20['cod_ctb'].$arCTB20['cod_fonte_recursos'];
            $stChaveAux20   = $arCTB20['cod_ctb'].$arCTB20['cod_fonte_recursos'];
            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arCTB20));
                    
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recursos");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_inicial_fonte");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_final_fonte");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

            if (count($rsRecordSet21->arElementos) > 0) {
                $stChave21= '';
                            
                foreach ($rsRecordSet21->arElementos as $arCTB21) {
                    $stChave21 = $arCTB21['cod_ctb'].$arCTB21['cod_fonte_recursos'];
                        
                    $inSequencia++;
                    $inCodSequencial = str_pad($inSequencia,4,"0",STR_PAD_LEFT);
                    $arCTB21['cod_reduzido_mov'] = Sessao::getExercicio().$arFiltro['stMes'].$inCodSequencial;
    
                    if ($stChave21 === $stChaveAux20 ) {                                    
                        $inCount++;
                        if ($arCTB21['cod_ctb'] != $arCTB21['cod_ctb_transf']) {
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arCTB21));
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recursos");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_mov");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_movimentacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_entr_saida");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_entr_saida");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb_transf");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
                        
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_ctb_transf");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                              
                            $stChaveComp21 = $stChave21.$arCTB21['tipo_entr_saida'].$arCTB21['tipo_movimentacao'] ;
                            if($arCTB21['tipo_entr_saida'] == '01' OR $arCTB21['tipo_entr_saida'] == '02' OR $arCTB21['tipo_entr_saida'] == '03' OR $arCTB21['tipo_entr_saida'] == '04' OR $arCTB21['tipo_entr_saida'] == '15' OR $arCTB21['tipo_entr_saida'] == '16'){
                                if (count($rsRecordSet22->arElementos) > 0) {
                                    $stChaveComp22 = '';
                                        
                                    foreach ($rsRecordSet22->arElementos as $arCTB22) {
                                        $stChave22 = $arCTB22['cod_ctb'].$arCTB22['cod_fonte_recursos'].$arCTB22['tipo_entr_saida'].$arCTB22['tipo_movimentacao'];
                                        if($stChave22 === $stChaveComp21 AND $arCTB22['tipo_entr_saida'] != '99' ) {
                                            $arCTB22['cod_reduzido_mov'] = $arCTB21['cod_reduzido_mov'];
                                            $inCount++;
                                            $rsBloco = 'rsBloco_'.$inCount;
                                            unset($$rsBloco);
                                            $$rsBloco = new RecordSet();
                                            $$rsBloco->preenche(array($arCTB22));
                                            
                                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                            $obExportador->roUltimoArquivo->addBloco($$rsBloco); 
                        
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_mov");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                        
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("e_deducao_de_receita");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador_deducao");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
                        
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                        
                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlr_receita_cont");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);            
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //}
    } // end foreach Registro20
} // end if count Registro20
    
    

if($rsRecordSet20->getNumLinhas() < 0) {
        $obExportador->roUltimoArquivo->addBloco($rsRecordSet99);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSet10      = null;
$rsRecordSet20      = null;
$rsRecordSet21      = null;
$rsRecordSet22      = null;
$rsRecordSet99      = null;
$obTTCEMGCTB        = null;
$obTTCEMGArquivoCTB = null;
?>