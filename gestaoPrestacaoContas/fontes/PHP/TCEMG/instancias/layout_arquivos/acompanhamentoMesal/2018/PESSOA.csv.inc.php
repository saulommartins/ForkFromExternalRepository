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
  * Página de Include Oculta - Exportação Arquivos TCEMG - PESSOA.csv
  * Data de Criação: 29/08/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: PESSOA.csv.inc.php 62269 2015-04-15 18:28:39Z franver $
  * $Date: 2015-04-15 15:28:39 -0300 (Qua, 15 Abr 2015) $
  * $Author: franver $
  * $Rev: 62269 $
  *
*/
/**
* PESSOA.csv | Autor : Arthur Cruz
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGArquivoMensalPessoa.class.php";

$rsRecordSetPESSOA10 = new RecordSet();

$obTTCEMGArquivoMensalPessoa = new TTCEMGArquivoMensalPessoa();
# Quando o mês for Janeiro deve enviar todo o cadastro conforme o chamado #21123
if ($stMes == '01') {
    $obTTCEMGArquivoMensalPessoa->setDado('dtInicio' , '01/01/'.Sessao::getExercicio() );
    $obTTCEMGArquivoMensalPessoa->setDado('dtFim'    , '31/01/'.Sessao::getExercicio() );
    $obTTCEMGArquivoMensalPessoa->setDado('mes'      , $stMes );
} else { 
    $obTTCEMGArquivoMensalPessoa->setDado('dtInicio' , $stDataInicial );
    $obTTCEMGArquivoMensalPessoa->setDado('dtFim'    , $stDataFinal );
    $obTTCEMGArquivoMensalPessoa->setDado('mes'      , $stMes );
}
$obTTCEMGArquivoMensalPessoa->criaTabelaPessoas($rsTabelaPessoas,"","",$boTransacao);
$obTTCEMGArquivoMensalPessoa->recuperaDadosExportacaoMensal($rsRecordSetPESSOA10);

//Tipo Registro 99
$arRecordSetPESSOA99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaPESSOA99 = new RecordSet();
$rsRecuperaPESSOA99->preenche($arRecordSetPESSOA99);

if (count($rsRecordSetPESSOA10->getElementos()) > 0) {
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsRecordSetPESSOA10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_documento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_documento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_razao_social");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(120);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_cadastro");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("justificativa_alteracao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(100);

} else {      
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaPESSOA99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}
$rsRecordSetPESSOA10 = null;
$obTTCEMGArquivoMensalPessoa = null;
$rsRecuperaPESSOA99 = null;

?>