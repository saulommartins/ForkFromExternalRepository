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

  * Página de Include Oculta - Exportação Arquivos TCEMG - CAIXA.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: CAIXA.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Wed, 15 Apr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* CAIXA.csv | Autor : Carolina Schwaab Marcal
*/
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGCAIXA.class.php";

$rsRecordSetCAIXA10 = new RecordSet();
$rsRecordSetCAIXA11 = new RecordSet();
$rsRecordSetCAIXA12 = new RecordSet();
$rsAdminConfiguracao = new RecordSet();
$rsAdminConfigEntidade = new RecordSet();

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado('exercicio', Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado('parametro', 'cod_entidade_prefeitura');
$obTAdministracaoConfiguracao->setDado('cod_modulo', 8);
$obTAdministracaoConfiguracao->recuperaPorChave($rsAdminConfiguracao, $boTransacao);

$obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
$obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo', 55);
$obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade', $rsAdminConfiguracao->getCampo('valor'));
$obTAdministracaoConfiguracaoEntidade->setDado('parametro', 'tcemg_codigo_orgao_entidade_sicom');
$obTAdministracaoConfiguracaoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTAdministracaoConfiguracaoEntidade->recuperaPorChave($rsAdminConfigEntidade);

$obTTCEMGCAIXA = new TTCEMGCAIXA();
$obTTCEMGCAIXA->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGCAIXA->setDado('entidades',$stEntidades);
$obTTCEMGCAIXA->setDado('dtInicio' ,$stDataInicial);
$obTTCEMGCAIXA->setDado('dtFim'    ,$stDataFinal);
$obTTCEMGCAIXA->setDado('cod_orgao',$rsAdminConfigEntidade->getCampo('valor'));

//10 - TIPO REGISTRO
$obTTCEMGCAIXA->recuperaCAIXA10($rsRecordSetCAIXA10);

//11 - TIPO REGISTRO
$obTTCEMGCAIXA->recuperaCAIXA11($rsRecordSetCAIXA11);

//12 - TIPO REGISTRO
$obTTCEMGCAIXA->recuperaCAIXA12($rsRecordSetCAIXA12);

//13 - TIPO REGISTRO - PARA EXERCICIO A PARTIR DE 2015
$obTTCEMGCAIXA->recuperaCAIXA13($rsRecordSetCAIXA13);

//99 - TIPO REGISTRO
$arRecordSetCAIXA99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetCAIXA99 = new RecordSet();
$rsRecordSetCAIXA99->preenche($arRecordSetCAIXA99);


if (Sessao::getExercicio() < 2015) {
        if (count($rsRecordSetCAIXA10->getElementos()) > 0) {
        $stChave10 = '';
        $stChaveComp10 = '';
        foreach ($rsRecordSetCAIXA10->getElementos() as $arCAIXA10) {
            $stChaveComp10 = $arCAIXA10['cod_orgao'];
            if ($stChave10 <> $arCAIXA10['tipo_registro'].$arCAIXA10['cod_orgao']) {
                $inCount++;

                $stChave10 = $arCAIXA10['tipo_registro'].$arCAIXA10['cod_orgao'];

                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arCAIXA10));

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

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_inicial");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_final");
                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                if (count($rsRecordSetCAIXA11->getElementos()) > 0) {
                    $stChave11 = '';
                    $stChaveComp11 = '';
                    foreach ($rsRecordSetCAIXA11->getElementos() as $arCAIXA11) {
                        $stChaveComp11 = $arCAIXA11['cod_reduzido'];

                            if ($stChave11 <> $arCAIXA11['tipo_registro'].$arCAIXA11['tipo_movimentacao'].$arCAIXA11['tipo_entr_saida'].$arCAIXA11['cod_ctb_transf'].$arCAIXA11['cod_fonte_ctb_transf']) {

                                $inCount++;

                                $stChave11 = $arCAIXA11['tipo_registro'].$arCAIXA11['tipo_movimentacao'].$arCAIXA11['tipo_entr_saida'].$arCAIXA11['cod_ctb_transf'].$arCAIXA11['cod_fonte_ctb_transf'];

                                $rsBloco = 'rsBloco_'.$inCount;
                                unset($$rsBloco);
                                $$rsBloco = new RecordSet();
                                $$rsBloco->preenche(array($arCAIXA11));

                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
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

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descr_movimentacao");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_entr_saida");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb_transf");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);

                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_ctb_transf");
                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ESPACOS_DIR");
                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                                if (count($rsRecordSetCAIXA12->getElementos()) > 0) {
                                    $stChave12 = '';
                                    $stChaveComp12 = '';
                                    foreach ($rsRecordSetCAIXA12->getElementos() as $arCAIXA12) {
                                        $stChaveComp12 = $arCAIXA12['cod_reduzido'];

                                        if($stChaveComp11 == $stChaveComp12){
                                            if (!($stChave12 === $arCAIXA12['tipo_registro'].$arCAIXA12['cod_reduzido'].$arCAIXA12['e_deducao_de_receita'].$arCAIXA12['identificador_deducao'].$arCAIXA12['cod_fonte_ctb_transf'].$arCAIXA12['natureza_receita'])) {
                                                $inCount++;

                                                $stChave12 = $arCAIXA12['tipo_registro'].$arCAIXA12['cod_reduzido'].$arCAIXA12['e_deducao_de_receita'].$arCAIXA12['identificador_deducao'].$arCAIXA12['cod_fonte_ctb_transf'].$arCAIXA12['natureza_receita'];

                                                $rsBloco = 'rsBloco_'.$inCount;
                                                unset($$rsBloco);
                                                $$rsBloco = new RecordSet();
                                                $$rsBloco->preenche(array($arCAIXA12));

                                                $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("e_deducao_de_receita");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador_deducao");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita");
                                                $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
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
        } else {
            $obExportador->roUltimoArquivo->addBloco($rsRecordSetCAIXA99);
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        }

} else if (Sessao::getExercicio() >= 2015)  {
    if (count($rsRecordSetCAIXA10->getElementos()) > 0) {
    $stChave10 = '';
    $stChaveComp10 = '';
    foreach ($rsRecordSetCAIXA10->getElementos() as $arCAIXA10) {
        $stChaveComp10 = $arCAIXA10['cod_orgao'];
        if ($stChave10 <> $arCAIXA10['tipo_registro'].$arCAIXA10['cod_orgao']) {
            $inCount++;
            
            $stChave10 = $arCAIXA10['tipo_registro'].$arCAIXA10['cod_orgao'];

            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arCAIXA10));
            
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

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_inicial");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_final");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

            if (count($rsRecordSetCAIXA11->getElementos()) > 0) {
                $stChave11 = '';
                $stChaveComp11 = '';
                foreach ($rsRecordSetCAIXA11->getElementos() as $arCAIXA11) {
                    $stChaveComp11 = $arCAIXA11['cod_reduzido'];
                    
                        if ($stChave11 <> $arCAIXA11['tipo_registro'].$arCAIXA11['tipo_movimentacao'].$arCAIXA11['tipo_entr_saida'].$arCAIXA11['cod_ctb_transf'].$arCAIXA11['cod_fonte_ctb_transf']) {

                            $inCount++;

                            $stChave11 = $arCAIXA11['tipo_registro'].$arCAIXA11['tipo_movimentacao'].$arCAIXA11['tipo_entr_saida'].$arCAIXA11['cod_ctb_transf'].$arCAIXA11['cod_fonte_ctb_transf'];
                    
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arCAIXA11));
                            
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_caixa");
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

                            if (count($rsRecordSetCAIXA12->getElementos()) > 0) {
                                $stChave12 = '';
                                $stChaveComp12 = '';
                                foreach ($rsRecordSetCAIXA12->getElementos() as $arCAIXA12) {
                                    $stChaveComp12 = $arCAIXA11['cod_reduzido'];

                                        if ($stChave12 <> $arCAIXA12['tipo_registro'].$arCAIXA12['tipo_movimentacao'].$arCAIXA12['tipo_entr_saida'].$arCAIXA12['cod_ctb_transf'].$arCAIXA12['cod_fonte_ctb_transf']) {

                                            $inCount++;

                                            $stChave12 = $arCAIXA12['tipo_registro'].$arCAIXA12['tipo_movimentacao'].$arCAIXA12['tipo_entr_saida'].$arCAIXA12['cod_ctb_transf'].$arCAIXA12['cod_fonte_ctb_transf'];

                                            $rsBloco = 'rsBloco_'.$inCount;
                                            unset($$rsBloco);
                                            $$rsBloco = new RecordSet();
                                            $$rsBloco->preenche(array($arCAIXA12));

                                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                                            
                                             $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_caixa");
                                             $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                             $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_movimentacao");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_entr_saida");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("descr_movimentacao");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(50);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_entr_saida");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ctb_transf");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);

                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte_ctb_transf");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CHARACTER_ESPACOS_DIR");
                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
                                            
                                             if (count($rsRecordSetCAIXA13->getElementos()) > 0) {
                                                $stChave13 = '';
                                                $stChaveComp13 = '';
                                                foreach ($rsRecordSetCAIXA13->getElementos() as $arCAIXA13) {
                                                    $stChaveComp13 = $arCAIXA13['cod_reduzido'];

                                                    if($stChaveComp12 == $stChaveComp13){
                                                        if (!($stChave13 === $arCAIXA13['tipo_registro'].$arCAIXA13['cod_reduzido'].$arCAIXA13['e_deducao_de_receita'].$arCAIXA13['identificador_deducao'].$arCAIXA13['cod_fonte_ctb_transf'].$arCAIXA13['natureza_receita'])) {
                                                            $inCount++;

                                                            $stChave13 = $arCAIXA13['tipo_registro'].$arCAIXA13['cod_reduzido'].$arCAIXA13['e_deducao_de_receita'].$arCAIXA13['identificador_deducao'].$arCAIXA13['cod_fonte_ctb_transf'].$arCAIXA13['natureza_receita'];

                                                            $rsBloco = 'rsBloco_'.$inCount;
                                                            unset($$rsBloco);
                                                            $$rsBloco = new RecordSet();
                                                            $$rsBloco->preenche(array($arCAIXA13));

                                                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                                                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);                                     

                                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_reduzido");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

                                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("e_deducao_de_receita");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("identificador_deducao");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                                                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_receita");
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                                                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
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
                }
            }
        }
    } else {
        $obExportador->roUltimoArquivo->addBloco($rsRecordSetCAIXA99);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    }
} 


$rsRecordSetCAIXA10 = null;
$rsRecordSetCAIXA11 = null;
$rsRecordSetCAIXA12 = null;
$rsRecordSetCAIXA13 = null;
$rsAdminConfiguracao = null;
$rsAdminConfigEntidade = null;
$obTAdministracaoConfiguracao = null;
$obTAdministracaoConfiguracaoEntidade = null;
$obTTCEMGCAIXA = null;
$rsRecordSetCAIXA99 = null;

?>