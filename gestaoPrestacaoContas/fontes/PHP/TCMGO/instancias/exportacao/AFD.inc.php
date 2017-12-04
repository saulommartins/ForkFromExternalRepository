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
    * Página de Include Oculta - Exportação Arquivos GF

    * Data de Criação   : 27/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Vitor Hugo

    * @ignore

    $Revision: 62622 $
    $Name$
    $Author: carlos.silva $
    $Date: 2015-05-25 16:52:18 -0300 (Mon, 25 May 2015) $

    * Casos de uso: uc-06.04.00
*/
/*
$Log$
Revision 1.3  2007/05/21 21:04:13  bruce
corrigido a formatação dos numeros negativos

Revision 1.2  2007/05/18 14:55:25  bruce
*** empty log message ***

Revision 1.1  2007/05/08 14:49:18  bruce
*** empty log message ***

*/

    include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOAFD.class.php" );
    $obTTCMGOAFD = new TTCMGOAFD();
    $obTTCMGOAFD->setDado('exercicio', Sessao::getExercicio());
    $obTTCMGOAFD->setDado('cod_entidade', $stEntidades );
    $obTTCMGOAFD->recuperaContasBancarias($rsRecordSetAFD10);
    $obTTCMGOAFD->recuperaContasBancariasFonteRecurso($rsRecordSetAFD11);
    
    $inCount = 0;
    
    if (count($rsRecordSetAFD10->getElementos()) > 0) {
        $stChave10 = '';
        
        //Registro 10    
        foreach ($rsRecordSetAFD10->getElementos() as $arAFD10) {
            
            $inCount++;
            $stChave10 = $arAFD10['cod_orgao'].$arAFD10['banco'].$arAFD10['agencia'].$arAFD10['conta_corrente'].$arAFD10['conta_corrente_dv'].$arAFD10['tipo_documento'];
            $arAFD10['numero_sequencial'] = $inCount;

            $$rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arAFD10));
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );
    
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(03);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente_dv");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(01);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_entradas");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saidas");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_final");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(26);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);

            // Registro 11    
            if (count($rsRecordSetAFD11->getElementos()) > 0) {
                $stChave11 = '';
                
                foreach ($rsRecordSetAFD11->getElementos() as $arAFD11){
                    
                    if ($stChave10 == $arAFD11['cod_orgao'].$arAFD11['banco'].$arAFD11['agencia'].$arAFD11['conta_corrente'].$arAFD11['conta_corrente_dv'].$arAFD11['tipo_documento']) {
                        $stChave11 = $arAFD11['cod_orgao'].$arAFD11['banco'].$arAFD11['agencia'].$arAFD11['conta_corrente'].$arAFD11['conta_corrente_dv'].$arAFD11['tipo_documento'].$arAFD11['cod_fonte_recurso'];
                        $inCount++;
                        
                        $arAFD11['numero_sequencial'] = $inCount;
                        
                        $rsBloco11 = 'rsBloco11_'.$inCount;
                        unset($$rsBloco11);
                        $$rsBloco11 = new RecordSet();
                        $$rsBloco11->preenche(array($arAFD11));
                        $obExportador->roUltimoArquivo->addBloco( $$rsBloco11 );
        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("banco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("agencia");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("conta_corrente_dv");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_conta");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_recurso");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_inicial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_entradas");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saidas");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                            
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("saldo_final");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(13);
                            
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
                    }
                }// Fim foreach registro 11
            }
        }// Fim foreach registro 10
    }

    //Tipo Registro 99
    $arRecordSetAFD99 = array(
        '0' => array(
            'tipo_registro' => '99',
            'brancos' => '',
            'numero_sequencial' => $inCount+1,
        )
    );
    
    $rsRecordSetAFD99 = new RecordSet();
    $rsRecordSetAFD99->preenche($arRecordSetAFD99);
        
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetAFD99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(104);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(06);
    
    $rsRecordSetAFD10  = null;
    $rsRecordSetAFD11  = null;
    $rsRecordSetAFD99  = null;
        
?>