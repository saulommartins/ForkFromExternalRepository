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
  * Página de Include Oculta - Exportação Arquivos TCEMG - AOC.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: AOC.csv.inc.php 62413 2015-05-05 21:10:22Z arthur $
  * $Date: 2015-05-05 18:10:22 -0300 (Ter, 05 Mai 2015) $
  * $Author: arthur $
  * $Rev: 62413 $
  *
*/
/**
* AOC.csv | Autor : Carlos Adriano Vernieri da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGAOC.class.php";

$rsRecordSetAOC10 = new RecordSet();
$rsRecordSetAOC11 = new RecordSet();
$rsRecordSetAOC12 = new RecordSet();
$rsRecordSetAOC13 = new RecordSet();
$rsRecordSetAOC14 = new RecordSet();

$obTTCEMGAOC = new TTCEMGAOC();
$obTTCEMGAOC->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGAOC->setDado('entidade'  , $stEntidades);
$obTTCEMGAOC->setDado('dt_inicial', $stDataInicial);
$obTTCEMGAOC->setDado('dt_final'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGAOC->recuperaDadosAOC10($rsRecordSetAOC10);

//Tipo Registro 11
$obTTCEMGAOC->recuperaDadosAOC11($rsRecordSetAOC11);

//Tipo Registro 12
$obTTCEMGAOC->recuperaDadosAOC12($rsRecordSetAOC12);

//Tipo Registro 13
$obTTCEMGAOC->recuperaDadosAOC13($rsRecordSetAOC13);

//Tipo Registro 14
$obTTCEMGAOC->recuperaDadosAOC14($rsRecordSetAOC14);

//Tipo Registro 99
$arRecordSetAOC99= array(
    '0' => array(
        'tipo_registro' => '99'
    )
);

$rsRecordSetAOC99 = new RecordSet();
$rsRecordSetAOC99->preenche($arRecordSetAOC99);

$inCount=0;

if (count($rsRecordSetAOC10->getElementos()) > 0) {
    $stChave10 = '';

    foreach ($rsRecordSetAOC10->getElementos() as $arAOC10) {

        if ($stChave10 != $arAOC10['tiporegistro'].$arAOC10['codorgao'].$arAOC10['nrodecreto']) {
            $inCount++;
            $stChave10    = $arAOC10['tiporegistro'].$arAOC10['codorgao'].$arAOC10['nrodecreto'];
            $stChave10_fk = $arAOC10['nrodecreto'];

            $$rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arAOC10));
            
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodecreto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("datadecreto");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        }//if chave AOC10
        //Se houver registros no array
        if (count($rsRecordSetAOC11->getElementos()) > 0) {
            $stChave11 = '';

            foreach ($rsRecordSetAOC11->getElementos() as $arAOC11) {

                if ($stChave10_fk == $arAOC11['nrodecreto']) {
                    $inCount++;

                    $stChave11_fk = $arAOC11['codreduzidodecreto'];
        
                    $$rsBloco11 = 'rsBloco11_'.$inCount;
                    unset($$rsBloco11);
                    $$rsBloco11 = new RecordSet();
                    $$rsBloco11->preenche(array($arAOC11));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco( $$rsBloco11 );
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidodecreto");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodecreto");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipodecretoalteracao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valoraberto");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
        
                    //Se houver registros no array
                    if (count($rsRecordSetAOC12->getElementos()) > 0) {
                        $stChave12 = '';
        
                        foreach ($rsRecordSetAOC12->getElementos() as $arAOC12) {
        
                            if ($stChave11_fk == $arAOC12['codreduzidodecreto']) {
                                $inCount++;
                    
                                $$rsBloco12 = 'rsBloco12_'.$inCount;
                                unset($$rsBloco12);
                                $$rsBloco12 = new RecordSet();
                                $$rsBloco12->preenche(array($arAOC12));
                                
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco12 );
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidodecreto");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroleialteracao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(6);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dataleialteracao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tpleiorigdecreto");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoleialteracao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorabertolei");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }//if chave AOC12
                        }//foreach AOC12
                    }//if count(AOC12)
                    
                    //Se houver registros no array
                    if (count($rsRecordSetAOC13->getElementos()) > 0) {
                        $stChave13 = '';
                        foreach ($rsRecordSetAOC13->getElementos() as $arAOC13) {
                            if ($stChave11_fk == $arAOC13['codreduzidodecreto']) {
                                $inCount++;

                                $$rsBloco13 = 'rsBloco13_'.$inCount;
                                unset($$rsBloco13);
                                $$rsBloco13 = new RecordSet();
                                $$rsBloco13->preenche(array($arAOC13));
                             
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco13 );
                             
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidodecreto");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("origemrecalteracao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorabertoorigem");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }//if chave AOC13
                        }//foreach AOC13
                    }//if count(AOC13)
        
                    if (count($rsRecordSetAOC14->getElementos()) > 0) {
                        $stChave14 = '';
        
                        foreach ($rsRecordSetAOC14->getElementos() as $arAOC14) {

                            if ($stChave11_fk == $arAOC14['codreduzidodecreto']) {
                                $inCount++;
                    
                                $$rsBloco14 = 'rsBloco14_'.$inCount;
                                unset($$rsBloco14);
                                $$rsBloco14 = new RecordSet();
                                $$rsBloco14->preenche(array($arAOC14));
                             
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco14 );
                             
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidodecreto");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("origemrecalteracao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);    

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipoalteracao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfuncao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codsubfuncao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codprograma");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("idacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("idsubacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("naturezadespesa");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfontrecurso");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
        
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlacrescimoreducao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }//if chave AOC14
                        }//foreach AOC14
                    }//if count(AOC14)
                }//if chave AOC11
            }//foreach AOC11
        }//if count(AOC11)  
    }//foreach AOC10
   
//if count(AOC10)
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetAOC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetAOC10 = null;
$rsRecordSetAOC11 = null;
$rsRecordSetAOC12 = null;
$rsRecordSetAOC13 = null;
$rsRecordSetAOC14 = null;
$obTTCEMGAOC      = null;
$rsRecordSetAOC99 = null;

?>