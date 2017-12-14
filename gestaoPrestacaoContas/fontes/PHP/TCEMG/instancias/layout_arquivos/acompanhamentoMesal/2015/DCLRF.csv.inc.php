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
  * $Date: 2015-04-20 14:12:11 -0300 (Mon, 20 Apr 2015) $
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
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_saldo_atual_concessoes_garantia");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("receita_privatizacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_liquidado_incentivo_contribuinte");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_liquidado_incentivo_instituicao_financeira");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_inscrito_rpnp_incentivo_contribuinte");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_inscrito_rpnp_incentivo_instituicao_financeira");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_compromissado");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_recursos_nao_aplicados");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

} else{
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetDCLRF99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetDCLRF10 = null;
$rsAdminConfiguracao = null;
$rsAdminConfigEntidade = null;
$rsRecordSetDCLRF99 = null;
$obTAdministracaoConfiguracao = null;
$obTAdministracaoConfiguracaoEntidade = null;
$obTTCEMGDCLRF = null;
?>