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
  * Página de Include Oculta - Exportação Arquivos TCEMG - RESPINF.csv
  * Data de Criação: 11/03/2016

  * @author Analista:      Dagiane
  * @author Desenvolvedor: Arthur Cruz
  *
  * @ignore
  * $Id: RESPINF.inc.php 65902 2016-06-28 17:07:30Z evandro $
  * $Date: 2016-06-28 14:07:30 -0300 (Tue, 28 Jun 2016) $
  * $Author: evandro $
  * $Rev: 65902 $
  *
*/
/**
* RESPINF.csv | Autor : Arthur Cruz
*/
require_once CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio()."/TTCEMGRESPINF.class.php";

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    if ( $inCodEntidade == $inCodEntidadePrefeitura ) {
        Sessao::setEntidade('');
    }else{        
        Sessao::setEntidade($inCodEntidade);
    }

    $rsRecordSet = new RecordSet();
    $obTTCEMGRESPINF = new TTCEMGRESPINF();
    $obTTCEMGRESPINF->setDado('entidades' , $inCodEntidade);
    $obTTCEMGRESPINF->setDado('dt_inicial', $arDatasInicialFinal["stDtInicial"]);
    $obTTCEMGRESPINF->setDado('dt_final'  , $arDatasInicialFinal["stDtFinal"]);
    $obTTCEMGRESPINF->recuperaDados($rsRecordSet);
    
    if ( $rsRecordSet->getNumLinhas() > 0 ) {
        $obExportador->roUltimoArquivo->addBloco($rsRecordSet);
        
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_inicio");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
        $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_final");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_DDMMYYYY");
        $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
        $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    }

}

?>