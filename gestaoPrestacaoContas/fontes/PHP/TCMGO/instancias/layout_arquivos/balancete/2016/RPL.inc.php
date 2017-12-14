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
    * Página de Include Oculta - RESPONSÁVEIS PELA LICITAÇÃO

    * Data de Criação   : 28/02/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore
    * $Id: RPL.inc.php 65190 2016-04-29 19:36:51Z michel $

*/
include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio()."/TTCMGOResponsavelLicitacao.class.php";

$rsRecordSetRESPLIC10 = new RecordSet();
$rsRecordSetRESPLIC20 = new RecordSet();

$obTTCMGOResponsavelLicitacao = new TTCMGOResponsavelLicitacao();
$obTTCMGOResponsavelLicitacao->setDado('exercicio'  , Sessao::getExercicio());
$obTTCMGOResponsavelLicitacao->setDado('entidades'  , $stEntidades);
$obTTCMGOResponsavelLicitacao->setDado('dt_inicial' , $stDataInicial);
$obTTCMGOResponsavelLicitacao->setDado('dt_final'   , $stDataFinal);
$obTTCMGOResponsavelLicitacao->setDado('mes', $inMes);

//Tipo Registro 10
$obTTCMGOResponsavelLicitacao->recuperaResponsaveisLicitacao($rsRecordSetRESPLIC10);

//Tipo Registro 20
$obTTCMGOResponsavelLicitacao->recuperaComissaoLicitacao($rsRecordSetRESPLIC20);

//REGISTRO 10   
$inCount = 0;
if ( count($rsRecordSetRESPLIC10->getElementos()) > 0 ) {
    $stChave10 = '';
    $stChave = '';
    
    foreach ( $rsRecordSetRESPLIC10->getElementos() as $arRESPLIC10 ) {
        
        $auxiliarStChave10=$arRESPLIC10['tipo_registro'].$arRESPLIC10['cod_orgao'].$arRESPLIC10['codunidade'].$arRESPLIC10['exercicio_licitacao'].$arRESPLIC10['num_processo_licitatorio'].$arRESPLIC10['tipo_responsabilidade'];
        
        if ( !($stChave10 === $auxiliarStChave10)){
            $inCount++; 
            $stChave10 = $arRESPLIC10['tipo_registro'].$arRESPLIC10['cod_orgao'].$arRESPLIC10['codunidade'].$arRESPLIC10['exercicio_licitacao'].$arRESPLIC10['num_processo_licitatorio'].$arRESPLIC10['tipo_responsabilidade'];
            $stChave = $arRESPLIC10['num_processo_licitatorio'];
            $stChaveAuxiliar =  $arRESPLIC10['num_processo_licitatorio'].$arRESPLIC10['codunidade'];
            $arRESPLIC10['nro_sequencial'] = $inCount;
            
            $rsBloco = 'rsBloco_'.$inCount;
            unset($$rsBloco);
            $$rsBloco = new RecordSet();
            $$rsBloco->preenche(array($arRESPLIC10));
                        
            $obExportador->roUltimoArquivo->addBloco($$rsBloco);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
          
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codunidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_processo_licitatorio");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_responsabilidade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cargo_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logra_res_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("setor_logra_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade_logra_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_cidade_logra_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep_logra_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone_responsavel");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("escolaridade");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);

            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

        }
    }

    //REGISTRO 20    
    if ( count($rsRecordSetRESPLIC20->getElementos()) > 0 ) {
        $stChave20 = '';
        
        foreach ( $rsRecordSetRESPLIC20->getElementos() as $arRESPLIC20 ) {
            
            $stChaveElemento = $arRESPLIC20['nro_processo_licitatorio'].$arRESPLIC20['cod_unidade'];
            
            if ( $stChaveElemento == $stChaveAuxiliar ) {
                $inCount++;                
                $stChave20 = $arRESPLIC20['nro_processo_licitatorio'].$arRESPLIC20['cod_unidade'];
                $arRESPLIC20['nro_sequencial'] = $inCount;
                $rsBloco = 'rsBloco_'.$inCount;
                unset($$rsBloco);
                $$rsBloco = new RecordSet();
                $$rsBloco->preenche(array($arRESPLIC20));
                                      
                $obExportador->roUltimoArquivo->addBloco($$rsBloco);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio_licitacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_processo_licitatorio");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_comissao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_atribuicao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_membro_comissao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_ato_momeacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_ato_nomeacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_ato_nomeacao");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inicio_vigencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("final_vigencia");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_membro_com_lic");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);  
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_cargo");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("logra_res_membro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("setor_logra_membro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cidade_logra_membro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf_cidade_lograMembro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep_logra_membro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone_membro");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(80);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("escolaridade");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
                
                $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
                $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
            }
        }
    }
    
}

//Tipo Registro 99
$arRecordSetRESPLIC99 = array(
    '0' => array(
        'tipo_registro' => '99',
        'brancos'       => '',
        'nro_sequencial'=> $inCount+1
    )
);
$rsRecordSetRESPLIC99 = new RecordSet();
$rsRecordSetRESPLIC99->preenche($arRecordSetRESPLIC99);

$obExportador->roUltimoArquivo->addBloco($rsRecordSetRESPLIC99);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");    
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");    
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(389);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);    

$rsRecordSetRESPLIC10         = null;
$rsRecordSetRESPLIC20         = null;
$obTTCMGOResponsavelLicitacao = null;
$rsRecordSetRESPLIC99         = null;

?>