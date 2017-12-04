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
  * Página de Include Oculta - Exportação Arquivos TCEMG - CONSID.csv
  * Data de Criação: 29/08/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: CONSID.csv.inc.php 62857 2015-06-30 13:53:56Z franver $
  * $Date: 2015-06-30 10:53:56 -0300 (Tue, 30 Jun 2015) $
  * $Author: franver $
  * $Rev: 62857 $
  *
*/
/**
* CONSID.csv | Autor : Lisiane Morais
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConsideracaoArquivo.class.php";

$rsRecuperaConsid10 = new RecordSet();

$obTTCEMGConsideracaoArquivo = new TTCEMGConsideracaoArquivo();
$obTTCEMGConsideracaoArquivo->setDado('exercicio'   , Sessao::getExercicio());
$obTTCEMGConsideracaoArquivo->setDado('entidade'    , $stEntidades);
$obTTCEMGConsideracaoArquivo->setDado('mes'         , $stMes);
$obTTCEMGConsideracaoArquivo->setDado('modulo_sicom','mensal');
//10 – Considerações
$obTTCEMGConsideracaoArquivo->recuperaConsid($rsRecuperaConsid10);

//Tipo Registro 99 – Declaração de inexistência de informações
$arRecordSetCONSID99 = array(
    '0' => array(
        'tipo_registro' => '99',
    )
);

$rsRecuperaConsid99 = new RecordSet();
$rsRecuperaConsid99->preenche($arRecordSetCONSID99);

if (count($rsRecuperaConsid10->getElementos()) > 0) {
    $obExportador->roUltimoArquivo->setTipoDocumento('TCE_MG');
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaConsid10);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    if ( Sessao::getExercicio() >= "2015" ) {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
    } else {
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    }
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("consideracoes");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(3000);
    
} else {
    $obExportador->roUltimoArquivo->addBloco($rsRecuperaConsid99);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
}

$obTTCEMGConsideracaoArquivo = null;
$rsRecuperaConsid10 = null;
$rsRecuperaConsid99 = null;

?>