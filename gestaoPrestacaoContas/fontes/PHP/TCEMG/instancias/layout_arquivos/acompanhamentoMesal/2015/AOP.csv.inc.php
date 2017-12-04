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
  * Página de Include Oculta - Exportação Arquivos TCEMG - AOP.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: AOP.csv.inc.php 66183 2016-07-27 13:01:29Z franver $
  * $Date: 2016-07-27 10:01:29 -0300 (Wed, 27 Jul 2016) $
  * $Author: franver $
  * $Rev: 66183 $
  *
*/
/**
* AOP.csv | Autor : Carlos Adriano Vernieri da Silva
*/
require_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGAOP.class.php";

$rsRecordSetAOP10 = new RecordSet();
$rsRecordSetAOP11 = new RecordSet();

$obTTCEMGAOP = new TTCEMGAOP();
$obTTCEMGAOP->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGAOP->setDado('entidade'  , $stEntidades);
$obTTCEMGAOP->setDado('dt_inicial', $stDataInicial);
$obTTCEMGAOP->setDado('dt_final'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGAOP->recuperaDadosAOP10($rsRecordSetAOP10);

//Tipo Registro 11
$obTTCEMGAOP->recuperaDadosAOP11($rsRecordSetAOP11);

//Tipo Registro 99
$arRecordSetAOP99= array(
    '0' => array(
        'tipo_registro' => '99'
    )
);
$rsRecordSetAOP99 = new RecordSet();
$rsRecordSetAOP99->preenche($arRecordSetAOP99);
    
$inCount=0;
    
if (count($rsRecordSetAOP10->getElementos()) > 0) {
    $stChave10 = '';
       
    foreach ($rsRecordSetAOP10->getElementos() as $arAOP10) {
       
        if ($stChave10 !== $arAOP10['tiporegistro'].$arAOP10['codreduzido'].$arAOP10['codorgao'].$arAOP10['codunidadesub'].$arAOP10['nroop'].$arAOP10['dtpagamento'].$arAOP10['nroanulacaoop']) {
            $stChave10 = $arAOP10['tiporegistro'].$arAOP10['codreduzido'].$arAOP10['codorgao'].$arAOP10['codunidadesub'].$arAOP10['nroop'].$arAOP10['dtpagamento'].$arAOP10['nroanulacaoop'];
            $inCount++;
                
            $$rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arAOP10));
                
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzido");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtpagamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroanulacaoop");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtanulacaoop");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativaanulacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanulacaoop");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                          
            //Se houver registros no array
            if (count($rsRecordSetAOP11->getElementos()) > 0) {
                $stChave11 = '';
                
                //Percorre array de registros   
                foreach ($rsRecordSetAOP11->getElementos() as $arAOP11) {
    
                    //Verifica se registro 11 bate com chave do registro 10
                    if ($arAOP10['codreduzido'] === $arAOP11['codreduzido']) {
                        //Chave única do registro 11
                        if ($stChave11 !== $arAOP11['tiporegistro'].$arAOP11['codreduzido'].$arAOP11['tipopagamento'].$arAOP11['nroempenho'].$arAOP11['dtempenho'].$arAOP11['nroliquidacao'].$arAOP11['dtliquidacao'].$arAOP11['codfontrecurso']) {
                            $stChave11 = $arAOP11['tiporegistro'].$arAOP11['codreduzido'].$arAOP11['tipopagamento'].$arAOP11['nroempenho'].$arAOP11['dtempenho'].$arAOP11['nroliquidacao'].$arAOP11['dtliquidacao'].$arAOP11['codfontrecurso'];
                            
                            $rsBloco11 = 'rsBloco11_'.$inCount;
                            unset($$rsBloco11);
                            $$rsBloco11 = new RecordSet();
                            $$rsBloco11->preenche(array($arAOP11));
                            
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco( $$rsBloco11 );
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzido");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipopagamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtempenho");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroliquidacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtliquidacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfontrecurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlanulacaofonte");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);     
                        }
                    }
                } 
            }
        }
        $inCount++;
    }// Fim do foreach principal
    
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetAOP99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetAOP10 = null;
$rsRecordSetAOP11 = null;
$obTTCEMGAOP      = null;
$rsRecordSetAOP99 = null;

?>