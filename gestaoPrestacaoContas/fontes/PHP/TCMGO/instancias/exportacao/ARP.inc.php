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
    * Página de Include Oculta - Adesão a registro de Preços

    * Data de Criação   : 28/02/2014

    * @author Analista:      Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore
    * $Id: ARP.inc.php 59612 2014-09-02 12:00:51Z gelson $
    * $Rev: 59612 $
    * $Author: gelson $
    * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

*/

$arrayDado = array (
    'tipo_registro'  => '99',
    'brancos'        => '',
    'nro_sequencial' => '1'
);

$recordSet[$stArquivo] = new RecordSet();
$recordSet[$stArquivo]->preenche( array($arrayDado) );

$obExportador->roUltimoArquivo->addBloco($recordSet[$stArquivo]);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
if (Sessao::getExercicio() > 2013) {
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(670);
} else {
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(430);
}

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nro_sequencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
