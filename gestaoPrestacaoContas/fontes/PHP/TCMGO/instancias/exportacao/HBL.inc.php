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
    * Página de Include Oculta - HABILITAÇÃO DA LICITAÇÃO

    * Data de Criação   : 28/02/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore
    * $Id: HBL.inc.php 64696 2016-03-22 18:23:17Z franver $
    * $Rev: 64696 $
    * $Author: franver $
    * $Date: 2016-03-22 15:23:17 -0300 (Tue, 22 Mar 2016) $

*/
include_once CAM_GPC_TGO_MAPEAMENTO."TTCMGOHabilitacaoLicitacao.class.php";

$rsRecordSetHBL10 = new RecordSet();
$rsRecordSetHBL11 = new RecordSet();
$rsRecordSetHBL20 = new RecordSet();

$obTTCMGOHabilitacaoLicitacao = new TTCMGOHabilitacaoLicitacao();
$obTTCMGOHabilitacaoLicitacao->setDado('exercicio'  , Sessao::getExercicio());
$obTTCMGOHabilitacaoLicitacao->setDado('entidades'  , $stEntidades);
$obTTCMGOHabilitacaoLicitacao->setDado('mes'        , $inMes);
$obTTCMGOHabilitacaoLicitacao->setDado('dataInicial', $stDataInicial);
$obTTCMGOHabilitacaoLicitacao->setDado('dataFinal'  , $stDataFinal);
//Tipo Registro 10
$obTTCMGOHabilitacaoLicitacao->recuperaExportacao10($rsRecordSetHBL10);
//Tipo Registro 11
$obTTCMGOHabilitacaoLicitacao->recuperaExportacao11($rsRecordSetHBL11);
//Tipo Registro 20
$obTTCMGOHabilitacaoLicitacao->recuperaExportacao20($rsRecordSetHBL20);
//Tipo Registro 99
$arRecordSetHBL99 = array(
    '0' => array(
        'tipo_registro' => '99',
        'brancos' => '',
    )
);

$rsRecordSetHBL99 = new RecordSet();
$rsRecordSetHBL99->preenche($arRecordSetHBL99);

$inCount = 0;
if ( $rsRecordSetHBL10->getNumLinhas() > 0 ) {
    $stChave10 = '';
    //Registro 10    
    foreach ($rsRecordSetHBL10->getElementos() as $arHBL10) {
        
        $stChave10Aux11 = $arHBL10['cod_orgao'].$arHBL10['cod_unidade'].$arHBL10['exercicio_licitacao'].$arHBL10['num_processo_licitatorio'];

        $stChave10Aux = $arHBL10['cod_orgao'].$arHBL10['cod_unidade'].$arHBL10['exercicio_licitacao'].$arHBL10['num_processo_licitatorio'].$arHBL10['tipo_documento'].$arHBL10['num_documento'];
        
        if ( $stChave10 != $stChave10Aux) {
            $inCount++;
            $stChave10 = $arHBL10['cod_orgao'].$arHBL10['cod_unidade'].$arHBL10['exercicio_licitacao'].$arHBL10['num_processo_licitatorio'].$arHBL10['tipo_documento'].$arHBL10['num_documento'];
        
            $$rsBloco10 = 'rsBloco10_'.$inCount;
            unset($$rsBloco10);
            $$rsBloco10 = new RecordSet();
            $$rsBloco10->preenche(array($arHBL10));
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco10 );

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_razao_social");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_social");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(500);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao_resp_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_registro_cvm");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_registro_cvm");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_habilitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("presenca_licitantes");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("renuncia_recurso");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

            // Registro 11
            if ( $rsRecordSetHBL11->getNumLinhas() > 0 && $arHBL10['tipo_documento'] = 2) {
                $stChave11 = '';
            
                foreach ($rsRecordSetHBL11->getElementos() as $arHBL11){
                    $stChave11Aux = $arHBL11['cod_orgao'].$arHBL11['cod_unidade'].$arHBL11['exercicio_licitacao'].$arHBL11['num_processo_licitatorio'];
                    if ($stChave10Aux11 == $stChave11Aux) {
                        $stChave11 = $arHBL11['cod_orgao'].$arHBL11['cod_unidade'].$arHBL11['exercicio_licitacao'].$arHBL11['num_processo_licitatorio'];
                    
                        $rsBloco11 = 'rsBloco11_'.$inCount;
                        unset($$rsBloco11);
                        $$rsBloco11 = new RecordSet();
                        $$rsBloco11->preenche(array($arHBL11));
                        $obExportador->roUltimoArquivo->addBloco( $$rsBloco11 );
        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro"); 
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cnpj");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_socio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento_socio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_participacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_socio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espaco_branco");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(753);
                    }
                }
            }//Fim if registro 11 
        }
    }//Fim foreach 10
}
    
if ( $rsRecordSetHBL20->getNumLinhas() > 0 ) {
    $stChave20 = '';

    foreach ($rsRecordSetHBL20->getElementos() as $arHBL20){
        $stChave20Aux = $arHBL20['cod_orgao'].$arHBL20['cod_unidade'].$arHBL20['exercicio_licitacao'].$arHBL20['num_processo_licitatorio'].$arHBL20['tipo_documento'].$arHBL20['num_documento'].$arHBL20['num_lote'].$arHBL20['num_item'];

        if ($stChave20 != $stChave20Aux) {
            $stChave20 = $stChave20Aux;

            $rsBloco20 = 'rsBloco20_'.$inCount;
            unset($$rsBloco20);
            $$rsBloco20 = new RecordSet();
            $$rsBloco20->preenche(array($arHBL20));
            $obExportador->roUltimoArquivo->addBloco( $$rsBloco20 );
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_credenciamento");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_item");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_razao_social");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("espaco_branco");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(642);
        }
    }
}

$obExportador->roUltimoArquivo->addBloco($rsRecordSetHBL99);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(901);

$rsRecordSetHBL10  = null;
$rsRecordSetHBL11  = null;
$rsRecordSetHBL20  = null;
$rsRecordSetHBL99  = null;

?>