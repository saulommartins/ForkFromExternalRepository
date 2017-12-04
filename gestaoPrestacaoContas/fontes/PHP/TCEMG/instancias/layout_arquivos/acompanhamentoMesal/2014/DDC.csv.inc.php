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
  * Página de Include Oculta - Exportação Arquivos TCEMG - DDC.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: DDC.csv.inc.php 62299 2015-04-20 17:37:23Z franver $
  * $Date: 2015-04-20 14:37:23 -0300 (Mon, 20 Apr 2015) $
  * $Author: franver $
  * $Rev: 62299 $
  *
*/
/**
* DDC.csv | Autor : Arthur Cruz
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGDDC.class.php";

$rsRecordSetDDC10 = new RecordSet();
$rsRecordSetDDC20 = new RecordSet();
$rsRecordSetDDC30 = new RecordSet();

$obTTCEMGDDC = new TTCEMGDDC();
$obTTCEMGDDC->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEMGDDC->setDado('cod_entidade'  , $stEntidades);
$obTTCEMGDDC->setDado('mes_referencia', $stMes);
 
$obTTCEMGDDC->recuperaDadosMensalDDC10($rsRecordSetDDC10,$boTransacao);
 
$obTTCEMGDDC->recuperaDadosMensalDDC20($rsRecordSetDDC20,$boTransacao);
 
$obTTCEMGDDC->recuperaDadosMensalDDC30($rsRecordSetDDC30,$boTransacao);

//Tipo Registro 99
$arRecordSetDDC99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetDDC99 = new RecordSet();
$rsRecordSetDDC99->preenche($arRecordSetDDC99);

$inCount=0;
if (count($rsRecordSetDDC10->getElementos()) > 0) {
    $stChave10 = '';
    
    foreach ($rsRecordSetDDC10->getElementos() as $arDDC10) {
        
        $inCount++;
        $stChave10 = $arDDC10['cod_orgao'].$arDDC10['nro_lei_autorizacao'].$arDDC10['dt_lei_autorizacao'];
        
        $$rsBloco10 = 'rsBloco10_'.$inCount;
        unset($$rsBloco10);
        $$rsBloco10 = new RecordSet();
        $$rsBloco10->preenche(array($arDDC10));
        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_autorizacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lei_autorizacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao_lei_autorizacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        if (count($rsRecordSetDDC20->getElementos()) > 0) {
            $stChave20 = '';
            
            foreach ($rsRecordSetDDC20->getElementos() as $arDDC20){
                
                //Verifica se registro 20 bate com chave do registro 10
                if ($stChave10 == $arDDC20['cod_orgao'].$arDDC20['nro_lei_autorizacao'].$arDDC20['dt_lei_autorizacao']) {
                    $stChave20 = $arDDC20['cod_orgao'].$arDDC20['nro_contrato_divida'].$arDDC20['dt_assinatura'];
                    
                    $rsBloco20 = 'rsBloco20_'.$inCount;
                    unset($$rsBloco20);
                    $$rsBloco20 = new RecordSet();
                    $$rsBloco20->preenche(array($arDDC20));
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco( $$rsBloco20 );
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato_divida");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("contrato_dec_lei"); 
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_lei_autorizacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lei_autorizacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato_divida");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1000);
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_contrato_divida");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);

                    if (count($rsRecordSetDDC30->getElementos()) > 0) {
                        $stChave30 = '';
                        
                        foreach ($rsRecordSetDDC30->getElementos() as $arDDC30) {
                             
                             //Verifica se registro 20 bate com chave do registro 30
                            if ($stChave20 == $arDDC30['cod_orgao'].$arDDC30['nro_contrato_divida'].$arDDC30['dt_assinatura']) {
                                                                    
                                $stChave30 = $arDDC30['cod_orgao'].$arDDC30['nro_contrato_divida'].$arDDC30['dt_assinatura'].$arDDC30['tipo_lancamento'];
                                
                                $rsBloco30 = 'rsBloco30_'.$inCount;
                                unset($$rsBloco30);
                                $$rsBloco30 = new RecordSet();
                                $$rsBloco30->preenche(array($arDDC30));
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco30 );
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);        
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato_divida");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento"); 
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_credor");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento_credor");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa_cancelamento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_contratacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_amortizacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_cancelamento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_encampacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_atualizacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }
                        }//Fim foreach DDC30
                    }
                }
            }//Fim foreach DDC20
        }
    }//Fim foreach DDC10
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetDDC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetDDC10 = null;
$rsRecordSetDDC20 = null;
$rsRecordSetDDC30 = null;
$obTTCEMGDDC = null;
$rsRecordSetDDC99 = null;
?>