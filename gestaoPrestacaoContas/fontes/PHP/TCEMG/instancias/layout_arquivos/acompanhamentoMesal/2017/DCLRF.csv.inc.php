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
  * Página de Include Oculta - Exportação Arquivos TCEMG - DCLRF.csv
  * Data de Criação: 01/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: DCLRF.csv.inc.php 62297 2015-04-20 17:12:11Z franver $
  * $Date: 2015-04-20 14:12:11 -0300 (Seg, 20 Abr 2015) $
  * $Author: franver $
  * $Rev: 62297 $
  *
*/
/**
* DCLRF.csv | Autor : Carlos Adriano
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGDCLRF.class.php";
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php';

$rsRecordSetDCLRF10 = new RecordSet();
$rsAdminConfiguracao = new Recordset();
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

//10 - TIPO REGISTRO
$obTTCEMGDCLRF = new TTCEMGDCLRF();
$obTTCEMGDCLRF->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGDCLRF->setDado('mes_referencia',$stMes);
$obTTCEMGDCLRF->setDado('cod_orgao',$rsAdminConfigEntidade->getCampo('valor'));
$obTTCEMGDCLRF->setDado('tipo_registro', '10');
$obTTCEMGDCLRF->recuperaValoresArquivoDCLRF($rsRecordSetDCLRF10);

//Tipo Registro 99 – Declaração de inexistência de informações
$arRecordSetDCLRF99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);
$rsRecordSetDCLRF99 = new RecordSet();
$rsRecordSetDCLRF99->preenche($arRecordSetDCLRF99);

if (count($rsRecordSetDCLRF10->getElementos()) > 0) {
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetDCLRF10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_saldo_atual_concessoes_garantia");//externa
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_saldo_atual_concessoes_garantia_interna");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_saldo_atual_contra_concessoes_garantia_interna");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_saldo_atual_contra_concessoes_garantia_externa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("medidas_corretivas");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4000);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("receita_privatizacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_liquidado_incentivo_contribuinte");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_liquidado_incentivo_instituicao_financeira");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_inscrito_rpnp_incentivo_contribuinte");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_inscrito_rpnp_incentivo_instituicao_financeira");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_compromissado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_recursos_nao_aplicados");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
 
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("publicacao_relatorio_lrf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_publicacao_relatorio_lrf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(8);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bimestre");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meta_bimestral");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("medida_adotada");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4000);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    //Gera registro 20 somente no mês de dezembro
    if($stMes == 12) {
        $obTTCEMGDCLRF = new TTCEMGDCLRF();
        $obTTCEMGDCLRF->setDado('exercicio',Sessao::getExercicio());
        $obTTCEMGDCLRF->setDado('mes_referencia',$stMes);
        $obTTCEMGDCLRF->setDado('cod_orgao',$rsAdminConfigEntidade->getCampo('valor'));
        $obTTCEMGDCLRF->setDado('tipo_registro', '20');
        $obTTCEMGDCLRF->recuperaValoresArquivoDCLRF($rsRecordSetDCLRF20);
                
        if (count($rsRecordSetDCLRF20->getElementos()) > 0) {
            $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
            $obExportador->roUltimoArquivo->addBloco($rsRecordSetDCLRF20);
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cont_op_credito");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("desc_cont_op_credito");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1000);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("realiz_op_credito");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_realiz_op_credito_capta");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_realiz_op_credito_receb");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
            
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_realiz_op_credito_assun_dir");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        
            $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_realiz_op_credito_assun_obg");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
            $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(1);
            $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        }
    }
    
} else{
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetDCLRF99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
}

$rsRecordSetDCLRF10 = null;
$rsRecordSetDCLRF20 = null;
$rsAdminConfiguracao = null;
$rsAdminConfigEntidade = null;
$rsRecordSetDCLRF99 = null;
$obTAdministracaoConfiguracao = null;
$obTAdministracaoConfiguracaoEntidade = null;
$obTTCEMGDCLRF = null;

?>