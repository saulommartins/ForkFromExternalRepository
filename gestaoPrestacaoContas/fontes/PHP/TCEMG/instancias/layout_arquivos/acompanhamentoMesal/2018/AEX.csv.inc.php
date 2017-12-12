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
  * Página de Include Oculta - Exportação Arquivos TCEMG - AEX.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: AEX.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Qua, 15 Abr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* AEX.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGAnulacaoExtraOrcamentaria.class.php";

$rsRecordSetAEX10 = new RecordSet();

$obTTCEMGAnulacaoExtraOrcamentaria = new TTCEMGAnulacaoExtraOrcamentaria();
$obTTCEMGAnulacaoExtraOrcamentaria->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGAnulacaoExtraOrcamentaria->setDado('entidades', $stEntidades);
$obTTCEMGAnulacaoExtraOrcamentaria->setDado('mes', $stMes);

//Tipo Registro 10
$obTTCEMGAnulacaoExtraOrcamentaria->recuperaExportacao10($rsRecordSetAEX10);

//Tipo Registro 99
$arRecordSetAEX99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);
$rsRecordSetAEX99 = new RecordSet();
$rsRecordSetAEX99->preenche($arRecordSetAEX99);

$inCount = 0;

if (count($rsRecordSetAEX10->getElementos()) > 0) {
    foreach ($rsRecordSetAEX10->getElementos() as $arAEX10) {        
        $inCount++;
        $rsBloco = 'rsBloco_'.$inCount;
        unset($$rsBloco);
        $$rsBloco = new RecordSet();
        $$rsBloco->preenche(array($arAEX10));
                
        $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
        $obExportador->roUltimoArquivo->addBloco($$rsBloco);
                
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(02);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_ext");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(15);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_font_recursos");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_op");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_unidade");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(5);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_pagamento");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_anulacao_op");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(22);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_anulacao_op");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulacao_op"); 
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';'); 
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");  
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);
    }
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetAEX99);	
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");	
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');	
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");	
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}	

$rsRecordSetAEX10 = null;
$obTTCEMGAnulacaoExtraOrcamentaria = null;
$rsRecordSetAEX99 = null;

?>