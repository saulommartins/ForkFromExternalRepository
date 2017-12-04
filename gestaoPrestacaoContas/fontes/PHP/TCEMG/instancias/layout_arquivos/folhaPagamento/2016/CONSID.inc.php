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
  * @author Analista:      Dagiane Vieira
  * @author Desenvolvedor: Jean da Silva
  *
  * $Id: CONSID.inc.php 65902 2016-06-28 17:07:30Z evandro $
*/
/**
* CONSID.csv | Autor : Jean da Silva
*/
include_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGConsideracaoArquivo.class.php";

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    if ( $inCodEntidade == $inCodEntidadePrefeitura ) {
        Sessao::setEntidade('');
    }else{        
        Sessao::setEntidade($inCodEntidade);
    }

    $rsConsid10 = new RecordSet();
    
    $obTTCEMGConsideracaoArquivo = new TTCEMGConsideracaoArquivo();
    $obTTCEMGConsideracaoArquivo->setDado('exercicio'    , Sessao::getExercicio());
    $obTTCEMGConsideracaoArquivo->setDado('entidade'     , $inCodEntidade);
    $obTTCEMGConsideracaoArquivo->setDado('mes'          , $arFiltro['inMes']);
    $obTTCEMGConsideracaoArquivo->setDado('modulo_sicom' ,'folha');
    $obTTCEMGConsideracaoArquivo->setDado('tipo'         ,'folhapagamento');
    
    //10 – Considerações
    $obTTCEMGConsideracaoArquivo->recuperaConsid($rsConsid10);
    
    if (count($rsConsid10->getElementos()) > 0) {
        $obExportador->roUltimoArquivo->addBloco($rsConsid10);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_arquivo");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(20);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("consideracoes");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoMaximo(4000);
        
    } else {
        //Tipo Registro 99 – Declaração de inexistência de informações
        $arConsid99 = array(
            '0' => array(
                'tipo_registro' => '99',
            )
        );
        
        $rsConsid99 = new RecordSet();
        $rsConsid99->preenche($arConsid99);
        
        $obExportador->roUltimoArquivo->addBloco($rsConsid99);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    }
    
    $obTTCEMGCONSID = null;
    $rsConsid10 = null;
    $rsConsid99 = null;

}



?>