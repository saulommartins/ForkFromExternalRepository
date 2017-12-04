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
  * Página de Include Oculta - Exportação Arquivos TCMGO - DDA
  * Data de Criação: 05/02/2015
  * @author Analista:      Ane Caroline
  * @author Desenvolvedor: Evandro Melos
  * @ignore
  * $Id: DDA.inc.php 66265 2016-08-04 12:30:41Z evandro $
*/

include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio()."/TTCMGODDA.class.php";
include_once CAM_GPC_TGO_MAPEAMENTO.Sessao::getExercicio()."/TTCMGOConfiguracaoUnidadeGestora.class.php";

$obTTCMGOConfiguracaoUnidadeGestora = new TTCMGOConfiguracaoUnidadeGestora();
$obTTCMGOConfiguracaoUnidadeGestora->setDado('cod_entidade',Sessao::getCodEntidade($boTransacao));
$obTTCMGOConfiguracaoUnidadeGestora->recuperaUnidadeGestora($rsUnidadeGestora, "", "", $boTransacao);

$obTTCMGODDA = new TTCMGODDA();
$obTTCMGODDA->setDado('exercicio'           , Sessao::getExercicio() );
$obTTCMGODDA->setDado('nome_unidade_gestora', $rsUnidadeGestora      );
$obTTCMGODDA->recuperaArquivoExportacao10($rsRegistro10, "", "", $boTransacao);

$inCount = 0;

if (!$rsRegistro10->eof()) {
    foreach ($rsRegistro10->arElementos as $stChave){
        
        $stChave['numero_sequencial'] = ++$inCount;

        $rsBloco10 = 'rsBloco10_'.$inCount;
        unset($$rsBloco10);
        $$rsBloco10 = new RecordSet();
        $$rsBloco10->preenche(array($stChave));
    
        $obExportador->roUltimoArquivo->addBloco($$rsBloco10);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numr_insc_divida_ativa");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_unidade_gestora");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 100 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_devedor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_cnpj_devedor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 14 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_devedor");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 100 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_divida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inscricao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 8 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numr_proc_admin");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 15 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao_cobranca");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 1 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_original_divida");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_ant_principal_atualizado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_ant_juros_atualizado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_anterior");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_insc_princ_atual_periodo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_insc_juros_atual_periodo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_atualiz_monet_principal");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_juros_periodo");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_abatimento_principal");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_abatimento_juros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_baixa_rec_principal");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_baixa_rec_juros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_baixa_canc_principal");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_baixa_canc_juros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_ajuste_exe_ant_principal");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_ajuste_exe_ant_juros");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_principal_atualizado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_juros_atualizado");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_saldo_atual");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 13 );
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
    }
}

$arRegistro99 = array();
$arRegistro99['tipo_registro'] = '99';
$arRegistro99['numero_sequencial'] = ++$inCount;

$$rsBloco99 = new RecordSet();
$$rsBloco99->preenche(array($arRegistro99));

$obExportador->roUltimoArquivo->addBloco($$rsBloco99);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 2 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 504 );

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo( 6 );
  
unset($arRegistro99);
unset($$rsBloco99);
unset($rsUnidadeGestora);
unset($obTTCMGODDA);
unset($rsRegistro10);

?>