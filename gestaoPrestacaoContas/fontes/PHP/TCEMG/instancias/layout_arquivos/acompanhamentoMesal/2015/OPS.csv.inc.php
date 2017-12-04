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
  * Página de Include Oculta - Exportação Arquivos TCEMG - OPS.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OPS.csv.inc.php 66179 2016-07-26 18:42:17Z franver $
  * $Date: 2016-07-26 15:42:17 -0300 (Tue, 26 Jul 2016) $
  * $Author: franver $
  * $Rev: 66179 $
  *  
*/
/**
* OPS.csv | Autor : Carlos Adriano Silva 
*/
require_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGOPS.class.php";
$rsRecordSetOPS10 = new RecordSet();
$rsRecordSetOPS11 = new RecordSet();
$rsRecordSetOPS12 = new RecordSet();
$rsRecordSetOPS13 = new RecordSet();

$obTTCEMGOPS = new TTCEMGOPS();
$obTTCEMGOPS->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGOPS->setDado('entidade'  , $stEntidades);
$obTTCEMGOPS->setDado('dt_inicial', $stDataInicial);
$obTTCEMGOPS->setDado('dt_final'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGOPS->recuperaDadosOPS10($rsRecordSetOPS10);
//Tipo Registro 11
$obTTCEMGOPS->recuperaDadosOPS11($rsRecordSetOPS11);
//Tipo Registro 12
$obTTCEMGOPS->recuperaDadosOPS12($rsRecordSetOPS12);
//Tipo Registro 13
// O Registro 13, não será informado pois será visto posteriormente. Nesse momento será mandado a mesma coisa ue foi mandado nas remessas anteriores. Conforme conversado com o Valtair
//$obTTCEMGOPS->recuperaDadosOPS13($rsRecordSetOPS13);

//Tipo Registro 99
$arRecordSetOPS99 = array(
    '0' => array(
        'tipo_registro' => '99'
    )
);

$rsRecordSetOPS99 = new RecordSet();
$rsRecordSetOPS99->preenche($arRecordSetOPS99);

$inCount=0;

if (count($rsRecordSetOPS10->getElementos()) > 0) {
    foreach ($rsRecordSetOPS10->getElementos() as $arOPS10) {
        $stChave10Pagamento = $arOPS10['codunidadesub'].$arOPS10['nroop'];
        SistemaLegado::removeAcentosSimbolos($arOPS10['especificacaoop']);
        
        $inCount++;
        
        $$rsBloco10 = 'rsBloco10_'.$inCount;
        unset($$rsBloco10);
        $$rsBloco10 = new RecordSet();
        $$rsBloco10->preenche(array($arOPS10));
        
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

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtpagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacaoop");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(200);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpfresppgto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        //Se houver registros no array
        if (count($rsRecordSetOPS11->getElementos()) > 0) {
            //Percorre array de registros
            foreach ($rsRecordSetOPS11->getElementos() as $arOPS11) {
                $stChave11Pagamento = $arOPS11['codunidadesub'].$arOPS11['nroop'];
                //Verifica se registro 11 bate com chave do registro 10

                if ($stChave10Pagamento === $stChave11Pagamento) {

                    $rsBloco11 = 'rsBloco11_'.$inCount;
                    unset($$rsBloco11);
                    $$rsBloco11 = new RecordSet();
                    $$rsBloco11->preenche(array($arOPS11));
                    
                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                    $obExportador->roUltimoArquivo->addBloco( $$rsBloco11 );
                    
                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidoop");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadesub");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroop");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtpagamento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipopagamento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroempenho");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtempenho");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nroliquidacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtliquidacao");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfontrecursos");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valorfonte");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipodocumentocredor");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodocumento");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codorgaoempop");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidadeempop");
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

                    //Se houver registros no array
                    if (count($rsRecordSetOPS12->getElementos()) > 0) {
                        //Percorre array de registros
                        foreach ($rsRecordSetOPS12->getElementos() as $arOPS12) {
                            $stChave12Emissao = $arOPS12['codreduzidoop'];
                            //Verifica se registro 12 bate com chave do registro 10
                            if ($arOPS11['codreduzidoop'] === $stChave12Emissao) {
                                $rsBloco12 = 'rsBloco12_'.$inCount;
                                unset($$rsBloco12);
                                
                                /*
                                Se o campo tipoDocumentoOP estiver preenchido como “05 – Dinheiro”:
                                1.3.1. O campo nrDocumento, codCTB e codFonteCTB não deve estar informados.
                                */
                                if ($arOPS12['tipodocumentoop'] == '05') {
                                    $arOPS12['nrodocumento']    = '';
                                    $arOPS12['codctb']          = '';
                                }
            
                                $$rsBloco12 = new RecordSet();
                                $$rsBloco12->preenche(array($arOPS12));
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco12 );
                            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidoop");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipodocumentoop");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nrodocumento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codctb");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codfontectb");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_tipo_documento_op");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dtemissao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vldocumento");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }
                        }
                    }
                    
                    //Se houver registros no array
                    if (count($rsRecordSetOPS13->getElementos()) > 0) {
                       //Percorre array de registros
                       foreach ($rsRecordSetOPS13->getElementos() as $arOPS13) {
                            $stChave13 = $arOPS13['codreduzidoop'];
                    
                            //Verifica se registro 13 bate com chave do registro 10
                            if ($arOPS11['codreduzidoop'] === $stChave13) {
                                $rsBloco13 = 'rsBloco13_'.$inCount;
                                unset($$rsBloco13);
                                $$rsBloco13 = new RecordSet();
                                $$rsBloco13->preenche(array($arOPS13));
                                
                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco( $$rsBloco13 );
                                
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporegistro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codreduzidoop");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tiporetencao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricaoretencao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
                    
                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlretencao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            }
                        }
                    }
                }
            }
        }
    }// Fim do foreach principal
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetOPS99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetOPS10 = null;
$rsRecordSetOPS11 = null;
$rsRecordSetOPS12 = null;
$rsRecordSetOPS13 = null;
$rsRecordSetOPS99 = null;
$obTTCEMGOPS      = null;
?>