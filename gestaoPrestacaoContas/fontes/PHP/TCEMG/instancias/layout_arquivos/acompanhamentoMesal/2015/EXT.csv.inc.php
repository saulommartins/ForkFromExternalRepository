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
  * Página de Include Oculta - Exportação Arquivos TCEMG - EXT.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: EXT.csv.inc.php 63553 2015-09-10 14:53:28Z jean $
  * $Date: 2015-09-10 11:53:28 -0300 (Thu, 10 Sep 2015) $
  * $Author: jean $
  * $Rev: 63553 $
  *
*/
/**
* EXT.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGExtraOrcamentarias.class.php";

$rsRecordSetEXT10 = new RecordSet();
$rsRecordSetEXT20 = new RecordSet();
$rsRecordSetEXT21 = new RecordSet();
$rsRecordSetEXT22 = new RecordSet();
$rsRecordSetEXT23 = new RecordSet();
$rsRecordSetEXT24 = new RecordSet();

$obTTCEMGExtraOrcamentarias = new TTCEMGExtraOrcamentarias();
$obTTCEMGExtraOrcamentarias->setDado('exercicio'  , Sessao::getExercicio());
$obTTCEMGExtraOrcamentarias->setDado('entidades'  , $stEntidades);
$obTTCEMGExtraOrcamentarias->setDado('mes'        , $stMes);
$obTTCEMGExtraOrcamentarias->setDado('dt_inicial' , $stDataInicial);
$obTTCEMGExtraOrcamentarias->setDado('dt_final'   , $stDataFinal);

$obTTCEMGExtraOrcamentarias->criaTabelaExtras($rsTabelaExtra,"","",$boTransacao);

//Tipo Registro 10
$obTTCEMGExtraOrcamentarias->recuperaExportacao10($rsRecordSetEXT10);

//Tipo Registro 20
$obTTCEMGExtraOrcamentarias->recuperaExportacao20($rsRecordSetEXT20);

//Tipo Registro 21
$obTTCEMGExtraOrcamentarias->recuperaExportacao21($rsRecordSetEXT21);

//Tipo Registro 22
$obTTCEMGExtraOrcamentarias->recuperaExportacao22($rsRecordSetEXT22);

//Tipo Registro 23
$obTTCEMGExtraOrcamentarias->recuperaExportacao23($rsRecordSetEXT23);

//Tipo Registro 99
$arRecordSetEXTS99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaEXT99 = new RecordSet();
$rsRecuperaEXT99->preenche($arRecordSetEXTS99);

$inCount = 0;

if (count($rsRecordSetEXT10->getElementos()) > 0) {

    $stChave10 = '';

    foreach ($rsRecordSetEXT10->getElementos() as $arEXT10) {        
        $inCount++;
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();

        $$rsBloco->preenche(array($arEXT10));
        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ext");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_lancamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_tipo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desdobra_sub_tipo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_extra_orc");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
    }

}

if (count($rsRecordSetEXT20->getElementos()) > 0) {
    $stChave20 = "";
        foreach ($rsRecordSetEXT20->getElementos() as $arEXT20) {
            $stChave = $arEXT20['cod_ext'];

            if($stChave20 !== $stChave){
                $stChave = $arEXT20['cod_ext'];
                $inCount++;
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arEXT20));

                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ext");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recurso");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_ant");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nat_saldo_anterior_fonte");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nat_saldo_atual_fonte");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);


                if (count($rsRecordSetEXT21->getElementos()) > 0) {
                    foreach ($rsRecordSetEXT21->getElementos() as $arEXT21) {
                        $stChave1 = $arEXT21['cod_ext'];
                        $stChave11 = $arEXT21['cod_reduzido_mov'];

                        if ($stChave === $stChave1) {
                            
                            $inCount++;  
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arEXT21));

                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_mov");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTCER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ext");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recurso");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("categoria");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_lancamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_lancamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                            if (count($rsRecordSetEXT22->getElementos()) > 0) {
                                foreach ($rsRecordSetEXT22->getElementos() as $arEXT22) {
                                    $stChave12 = $arEXT22['cod_reduzido_mov'];
                                    $stChave22 = $arEXT22['cod_reduzido_op'];                              
                                    
                                    if ( $stChave11 === $stChave12 ) {
                                        
                                        $inCount++; 
                                        $rsBloco = 'rsBloco_'.$inCount;
                                        unset($$rsBloco);
                                        $$rsBloco = new RecordSet();
                                        $$rsBloco->preenche(array($arEXT22));

                                        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_mov");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTCER_ESPACOS_DIR");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_op");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_op");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pagamento");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_credor");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento_credor");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_op");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("especificacao_op");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(200);

                                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_responsavel");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

                                        if (count($rsRecordSetEXT23->getElementos()) > 0) {
                                            foreach ($rsRecordSetEXT23->getElementos() as $arEXT23) {
                                                $stChave23 = $arEXT23['cod_reduzido_op'];

                                                if ($stChave22 === $stChave23) {

                                                    $inCount++;
                                                    $rsBloco = 'rsBloco_'.$inCount;
                                                    unset($$rsBloco);
                                                    $$rsBloco = new RecordSet();
                                                    $$rsBloco->preenche(array($arEXT23));

                                                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_op");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_op");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_ctb");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desctipodocumentoop");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_documento");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                                                    
                                                    if (count($rsRecordSetEXT24->getElementos()) > 0) {

                                                        foreach ($rsRecordSetEXT24->getElementos() as $arEXT24) {
                                                            $stChave24 = $arEXT24['cod_reduzido_op'];
                                                            if ($stChave23 === $stChave24) {
                                                                $inCount++;
                                                                $rsBloco = 'rsBloco_'.$inCount;
                                                                unset($$rsBloco);
                                                                $$rsBloco = new RecordSet();
                                                                $$rsBloco->preenche(array($arEXT24));

                                                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                                                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido_op");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_retencao");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_retencao");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_retencao");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
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
                        }
                    }
                }
            }
        }
}else {
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaEXT99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetEXT10 = null;
$rsRecordSetEXT20 = null;
$rsRecordSetEXT21 = null;
$rsRecordSetEXT22 = null;
$rsRecordSetEXT23 = null;
$rsRecordSetEXT24 = null;
$rsRecuperaEXT99  = null;
$obTTCEMGExtraOrcamentarias = null;
?>