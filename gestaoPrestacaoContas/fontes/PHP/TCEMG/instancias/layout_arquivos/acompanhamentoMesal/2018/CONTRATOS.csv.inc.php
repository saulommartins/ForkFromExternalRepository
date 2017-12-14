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
  * Página de Include Oculta - Exportação Arquivos TCEMG - CONTRATOS.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: CONTRATOS.csv.inc.php 65268 2016-05-09 15:06:10Z michel $
  *
*/
/**
* CONTRATOS.csv | Autor : Michel Teixeira
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGCONTRATOS.class.php";

$rsRecordSetCONTRATOS10 = new RecordSet();
$rsRecordSetCONTRATOS11 = new RecordSet();
$rsRecordSetCONTRATOS12 = new RecordSet();
$rsRecordSetCONTRATOS13 = new RecordSet();
$rsRecordSetCONTRATOS20 = new RecordSet();
$rsRecordSetCONTRATOS21 = new RecordSet();
$rsRecordSetCONTRATOS30 = new RecordSet();
$rsRecordSetCONTRATOS40 = new RecordSet();

$obTTCEMGCONTRATOS = new TTCEMGCONTRATOS();
$obTTCEMGCONTRATOS->setDado('exercicio' , Sessao::getExercicio());
$obTTCEMGCONTRATOS->setDado('entidade'  , $stEntidades);
$obTTCEMGCONTRATOS->setDado('dt_inicial', $stDataInicial);
$obTTCEMGCONTRATOS->setDado('dt_final'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGCONTRATOS->recuperaContrato10($rsRecordSetCONTRATOS10);

//Tipo Registro 11
$obTTCEMGCONTRATOS->recuperaContrato11($rsRecordSetCONTRATOS11);

//Tipo Registro 12
$obTTCEMGCONTRATOS->recuperaContrato12($rsRecordSetCONTRATOS12);

//Tipo Registro 13
$obTTCEMGCONTRATOS->recuperaContrato13($rsRecordSetCONTRATOS13);

//Tipo Registro 20
$obTTCEMGCONTRATOS->recuperaContrato20($rsRecordSetCONTRATOS20);

//Tipo Registro 21
$obTTCEMGCONTRATOS->recuperaContrato21($rsRecordSetCONTRATOS21);

//Tipo Registro 30
$obTTCEMGCONTRATOS->recuperaContrato30($rsRecordSetCONTRATOS30);

//Tipo Registro 40
$obTTCEMGCONTRATOS->recuperaContrato40($rsRecordSetCONTRATOS40);

//Tipo Registro 99
$arRecordSetCONTRATOS99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetCONTRATOS99 = new RecordSet();
$rsRecordSetCONTRATOS99->preenche($arRecordSetCONTRATOS99);

$boGera99 = TRUE;

if (count($rsRecordSetCONTRATOS10->getElementos()) > 0) {
    $boGera99 = FALSE;
    $inCount = 0;
    foreach ($rsRecordSetCONTRATOS10->getElementos() as $arContrato10) {
        $inCount++;
        $stChave10 = $arContrato10['cod_contrato'];

        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arContrato10));

        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cont_dec_licitacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao_resp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub_resp");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_processo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_objeto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(500);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_instrumento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicio_vigencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final_vigencia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_contratado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_fornecimento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("forma_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prazo_execucao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("multa_rescisoria");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("multa_inadimplemento");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_garantia");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_signatario_contratante");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("veiculo_divulgacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

        if (count($rsRecordSetCONTRATOS11->getElementos()) > 0) {

            foreach ($rsRecordSetCONTRATOS11->getElementos() as $arContrato11) {
                $stChave11 = $arContrato11['cod_contrato'];
                //Se a Natureza do Objeto for Concessão ou Permissão este registro não deve ser informado.
                if ( $arContrato10['natureza_objeto'] !== 4 OR $arContrato10['natureza_objeto'] !== 5) {
                    if ($stChave10 === $stChave11) {
                        $inCount++;
                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arContrato11));

                        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_contrato");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade_item");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_unitario_item");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                        if (count($rsRecordSetCONTRATOS12->getElementos()) > 0) {

                            foreach ($rsRecordSetCONTRATOS12->getElementos() as $arContrato12) {
                                $stChave12 = $arContrato12['cod_contrato'];

                                if ($stChave11 === $stChave12) {
                                    $inCount++;
                                    $rsBloco = 'rsBloco_'.$inCount;
                                    unset($$rsBloco);
                                    $$rsBloco = new RecordSet();
                                    $$rsBloco->preenche(array($arContrato12));

                                    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                    $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_contrato");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_sub_funcao");
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

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("id_sub_acao");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_despesa");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

                                    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recursos");
                                    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
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
                }
            }
        }

        if (count($rsRecordSetCONTRATOS13->getElementos()) > 0) {

           foreach ($rsRecordSetCONTRATOS13->getElementos() as $arContrato13) {
              $stChave13 = $arContrato13['cod_contrato'];

              if ($stChave10 === $stChave13) {
                 $inCount++;
                 $rsBloco = 'rsBloco_'.$inCount;
                 unset($$rsBloco);
                 $$rsBloco = new RecordSet();
                 $$rsBloco->preenche(array($arContrato13));

                 $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                 $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_contrato");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                 $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_representante_legal");
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                 $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                 $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
              }
           }
        }
    }
}

if (count($rsRecordSetCONTRATOS20->getElementos()) > 0) {
    $boGera99 = FALSE;
    foreach ($rsRecordSetCONTRATOS20->getElementos() as $arContrato20) {
        $stChave20 = $arContrato20['cod_contrato'];

        $inCount++;
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arContrato20));

        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_aditivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_aditivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_assinatura_aditivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_alteracao_valor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_termo_aditivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao_alteracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nova_data_termino");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_aditivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_publicacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("veiculo_divulgacao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

        if (count($rsRecordSetCONTRATOS21->getElementos()) > 0) {

            foreach ($rsRecordSetCONTRATOS21->getElementos() as $arContrato21) {
                $stChave21 = $arContrato21['cod_contrato'];
                //Se a Natureza do Objeto for Concessão ou Permissão este registro não deve ser informado.
                if ( $arContrato21['tipo_objeto'] !== 3 OR $arContrato21['tipo_objeto'] !== 5 ) {
                    //Somente se o tipo de aditivo for “09,10,11 ou 14 "
                    if ($arContrato21['tipo_termo_aditivo'] == 9 OR $arContrato21['tipo_termo_aditivo'] == 10 OR $arContrato21['tipo_termo_aditivo'] == 11 OR $arContrato21['tipo_termo_aditivo'] == 14) {
                        if ($stChave20 === $stChave21) {
                            $inCount++;
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arContrato21));

                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_aditivo");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_alteracao_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quantidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_unitario");
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

if (count($rsRecordSetCONTRATOS30->getElementos()) > 0) {
    $boGera99 = FALSE;
    foreach ($rsRecordSetCONTRATOS30->getElementos() as $arContrato30) {
        $stChave30 = $arContrato30['cod_contrato'];

        $inCount++;
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arContrato30));

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

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura_contrato_original");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_apostila");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_apostila");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_apostila");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_alteracao_apostila");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(250);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_apostila");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    }
}

if (count($rsRecordSetCONTRATOS40->getElementos()) > 0) {
    $boGera99 = FALSE;
    foreach ($rsRecordSetCONTRATOS40->getElementos() as $arContrato40) {
        $stChave40 = $arContrato40['cod_contrato'];

        $inCount++;
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arContrato40));

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

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade_sub");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_assinatura_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_rescisao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_cancelamento_contrato");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    }
}

if($boGera99) {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetCONTRATOS99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$rsRecordSetCONTRATOS10 = null;
$rsRecordSetCONTRATOS11 = null;
$rsRecordSetCONTRATOS12 = null;
$rsRecordSetCONTRATOS13 = null;
$rsRecordSetCONTRATOS20 = null;
$rsRecordSetCONTRATOS21 = null;
$rsRecordSetCONTRATOS30 = null;
$rsRecordSetCONTRATOS40 = null;
$obTTCEMGCONTRATOS      = null;
$rsRecordSetCONTRATOS99 = null;

?>