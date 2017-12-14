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
  * Página de Include Oculta - Exportação Arquivos TCEMG - HABLIC.csv
  * Data de Criação: 02/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: HABLIC.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Wed, 15 Apr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* HABLIC.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGHabilitacaoLicitacao.class.php";

$rsRecordSetHABLIC10 = new RecordSet();
$rsRecordSetHABLIC11 = new RecordSet();
$rsRecordSetHABLIC20 = new RecordSet();

$obTTCEMGHabilitacaoLicitacao = new TTCEMGHabilitacaoLicitacao();
$obTTCEMGHabilitacaoLicitacao->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGHabilitacaoLicitacao->setDado('entidades',$stEntidades);
$obTTCEMGHabilitacaoLicitacao->setDado('mes', $stMes);
$obTTCEMGHabilitacaoLicitacao->setDado('dataInicial', $stDataInicial);
$obTTCEMGHabilitacaoLicitacao->setDado('dataFinal'  , $stDataFinal);

//Tipo Registro 10
$obTTCEMGHabilitacaoLicitacao->recuperaExportacao10($rsRecordSetHABLIC10);

//Tipo Registro 11
$obTTCEMGHabilitacaoLicitacao->recuperaExportacao11($rsRecordSetHABLIC11);

//Tipo Registro 20
$obTTCEMGHabilitacaoLicitacao->recuperaExportacao20($rsRecordSetHABLIC20);

//Tipo Registro 99
$arRecordSetHABLIC99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecordSetHABLIC99 = new RecordSet();
$rsRecordSetHABLIC99->preenche($arRecordSetHABLIC99);
 
$boChave = false;
//10
if ( count($rsRecordSetHABLIC10->getElementos()) > 0) {
    $boChave = true;
    foreach ($rsRecordSetHABLIC10->getElementos() as $arHABLIC10) {
        $inCount++;
        if($arHABLIC10['natureza_objeto']!=99){
            $stChave10 = $arHABLIC10['cod_orgao'].$arHABLIC10['cod_unidade'].$arHABLIC10['exercicio_licitacao'].$arHABLIC10['num_processo_licitatorio'].$arHABLIC10['num_documento'];
             
            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arHABLIC10));
            
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("objeto_social");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(2000);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("orgao_resp_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_registro_cvm");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_registro_cvm");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_inss");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_fgts");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_habilitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("presenca_licitantes");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("renuncia_recurso");
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            //11
            $stChave11 = '';
            $stChave11Unica = '';
            foreach ($rsRecordSetHABLIC11->getElementos() as $arHABLIC11) {
                $inCount++;
                $stChave11 = $arHABLIC11['cod_orgao'].$arHABLIC11['cod_unidade'].$arHABLIC11['exercicio_licitacao'].$arHABLIC11['num_processo_licitatorio'].$arHABLIC11['cnpj_empresa_hablic'];                
                if ($stChave10 === $stChave11){
                    //Registro Unico do registro 11
                    $stChave11Aux = $arHABLIC11['cod_orgao']
                                    .$arHABLIC11['cod_unidade']
                                    .$arHABLIC11['exercicio_licitacao']
                                    .$arHABLIC11['num_processo_licitatorio']
                                    .$arHABLIC11['tipo_documento_socio']
                                    .$arHABLIC11['num_documento_socio']
                                    .$arHABLIC11['tipo_participacao'];    
                    if ($stChave11Unica !== $stChave11Aux) {
                        $stChave11Unica = $stChave11Aux;

                        $rsBloco = 'rsBloco_'.$inCount;
                        unset($$rsBloco);
                        $$rsBloco = new RecordSet();
                        $$rsBloco->preenche(array($arHABLIC11));
                    
                        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                    
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_cnpj");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_empresa_hablic");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento_socio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento_socio");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                        
                        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_participacao");
                        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                    }    
                }
            }

            if (count($rsRecordSetHABLIC20->getElementos()) > 0) {
                $boChave = true;
                foreach ($rsRecordSetHABLIC20->getElementos() as $arHABLIC20) {
                    $inCount++;
                    $stChave20 = $arHABLIC20['cod_orgao'].$arHABLIC20['cod_unidade'].$arHABLIC20['exercicio_licitacao'].$arHABLIC20['num_processo_licitatorio'];
                    if($stChave11===$stChave20){
       
                        if(!($stChaveAux20===$arHABLIC20['cod_orgao'].$arHABLIC20['cod_unidade'].$arHABLIC20['exercicio_licitacao'].$arHABLIC20['num_processo_licitatorio'].$arHABLIC20['num_documento'])) {
                            $stChaveAux20===$arHABLIC20['cod_orgao'].$arHABLIC20['cod_unidade'].$arHABLIC20['exercicio_licitacao'].$arHABLIC20['num_processo_licitatorio'].$arHABLIC20['num_documento'];
                            $rsBloco = 'rsBloco_'.$inCount;
                            unset($$rsBloco);
                            $$rsBloco = new RecordSet();
                            $$rsBloco->preenche(array($arHABLIC20));
                            
                            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
                            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(12);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_documento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_credenciamento");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_lote");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_item");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_inscricao_estadual");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_inscricao_estadual");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_inss");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_inss");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_inss");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_certidao_regularidade_fgts");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_certidao_regularidade_fgts");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_certidao_regularidade_fgts");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_cndt");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(30);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_emissao_cndt");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                            
                            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_validade_cndt");
                            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                        }
                    }
                }
            }
        }//IF COD NATUREZA 99
    }
}
if ($boChave != true){
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetHABLIC99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetHABLIC10 = null;
$rsRecordSetHABLIC11 = null;
$rsRecordSetHABLIC20 = null;
$rsRecordSetHABLIC99 = null;
$obTTCEMGHabilitacaoLicitacao = null;
?>