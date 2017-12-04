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
  * Página de Include Oculta - Exportação Arquivos TCEMG - TEREM.csv
  * Data de Criação: 14/03/2016

  * @author Analista:      Dagiane
  * @author Desenvolvedor: Jean
  *
  * @ignore
  * $Id: TEREM.inc.php 65902 2016-06-28 17:07:30Z evandro $
  * $Date: 2016-06-28 14:07:30 -0300 (Tue, 28 Jun 2016) $
  * $Author: evandro $
  * $Rev: 65902 $
  *
*/
/**
* TEREM.csv | Autor : Jean
*/
require_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGTEREM.class.php";


foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    if ( $inCodEntidade == $inCodEntidadePrefeitura ) {
        Sessao::setEntidade('');
    }else{        
        Sessao::setEntidade($inCodEntidade);
    }

    $rsRecordSet = new RecordSet();
    $obTTCEMGTEREM = new TTCEMGTEREM();
    $obTTCEMGTEREM->setDado('unidade_gestora', $inCodUnidadeGestora);
    $obTTCEMGTEREM->setDado('entidades'      , $inCodEntidade);
    $obTTCEMGTEREM->setDado('exercicio'      , $stExercicioFiltro );
    $obTTCEMGTEREM->setDado('mes'            , $inMes);
    
    //Tipo Registro 10
    $obTTCEMGTEREM->recuperaDados($rsRecordSet);
    
    //10 – Cadastro de Teto Remuneratório 
    if ( count($rsRecordSet->getElementos()) > 0 ) {
        $obExportador->roUltimoArquivo->addBloco($rsRecordSet);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vlparateto");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("VALOR_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(15);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_cadastro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicial");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("just_alteracao");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(250);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    } else {
        //Tipo Registro 99
        //99 - Declaração de Inexistência de Informações
        $arRecord99[] = array( 'tipo_registro' => '99' );
        
        $rsRecord99 = new RecordSet();
        $rsRecord99->preenche($arRecord99);
    
        $obExportador->roUltimoArquivo->addBloco($rsRecord99);
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');   
    }

}

?>