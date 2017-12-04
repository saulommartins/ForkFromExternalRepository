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
    * Página de Include Oculta - Exportação Arquivos
    *
    * Data de Criação: 03/03/2011
    *
    *
    * @author: Tonismar R. Bernardo
    * @ignore
    *
*/
include_once( CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMOrcamento.class.php" );

$obTMapeamento = new TTCEAMOrcamento();
$rsRecordSet   = new RecordSet;
$arTemp        = array();

$obTMapeamento->setDado( 'cod_modulo', Sessao::read('modulo') );
$obTMapeamento->setDado( 'cod_entidade', $stEntidades );

$obTMapeamento->setDado( 'parametro', 'data_aprovacao_loa' );
$obTMapeamento->recuperaParametro( $rsRecordSet );

while ( !$rsRecordSet->eof() ) {
    list($ano, $mes, $dia) = explode('-', $rsRecordSet->getCampo( 'data_aprovacao_loa' ));
    $rsRecordSet->setCampo( 'data_aprovacao_loa', $ano.$mes.$dia );

    $arTemp[$rsRecordSet->getCorrente()-1]['data_aprovacao_loa'] = $rsRecordSet->getCampo('data_aprovacao_loa');
    $rsRecordSet->proximo();
}

$obTMapeamento->setDado( 'parametro', 'numero_loa' );
$obTMapeamento->recuperaParametro( $rsRecordSet );

while ( !$rsRecordSet->eof() ) {
    $arTemp[$rsRecordSet->getCorrente()-1]['numero_loa'] = $rsRecordSet->getCampo( 'numero_loa' );
    $arTemp[$rsRecordSet->getCorrente()-1]['exercicio'] = Sessao::getExercicio();
    $rsRecordSet->proximo();
}

$obTMapeamento->setDado( 'parametro', 'data_aprovacao_ldo' );
$obTMapeamento->recuperaParametro( $rsRecordSet );

while ( !$rsRecordSet->eof() ) {
    list($ano, $mes, $dia) = explode('-', $rsRecordSet->getCampo( 'data_aprovacao_ldo' ));
    $rsRecordSet->setCampo( 'data_aprovacao_ldo', $ano.$mes.$dia );

    $arTemp[$rsRecordSet->getCorrente()-1]['data_aprovacao_ldo'] = $rsRecordSet->getCampo( 'data_aprovacao_ldo' );
    $rsRecordSet->proximo();
}

$obTMapeamento->setDado( 'parametro', 'numero_ldo' );
$obTMapeamento->recuperaParametro( $rsRecordSet );

while ( !$rsRecordSet->eof() ) {
    $arTemp[$rsRecordSet->getCorrente()-1]['numero_ldo'] = $rsRecordSet->getCampo( 'numero_ldo' );
    $rsRecordSet->proximo();
}

$obTMapeamento->setDado( 'parametro', 'data_aprovacao_qdd' );
$obTMapeamento->recuperaParametro( $rsRecordSet );

while ( !$rsRecordSet->eof() ) {
    list($ano,$mes, $dia) = explode('-', $rsRecordSet->getCampo( 'data_aprovacao_qdd' ));
    $rsRecordSet->setCampo( 'data_aprovacao_qdd', $ano.$mes.$dia );

    $arTemp[$rsRecordSet->getCorrente()-1]['data_aprovacao_qdd'] = $rsRecordSet->getCampo( 'data_aprovacao_qdd' );
    $rsRecordSet->proximo();
}

$obTMapeamento->setDado( 'parametro', 'numero_qdd' );
$obTMapeamento->recuperaParametro( $rsRecordSet );

while ( !$rsRecordSet->eof() ) {
    $arTemp[$rsRecordSet->getCorrente()-1]['numero_qdd'] = $rsRecordSet->getCampo( 'numero_qdd' );
    $rsRecordSet->proximo();
}

$rsRecordSet->preenche($arTemp);

$obExportador->roUltimoArquivo->addBloco($rsRecordSet);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('[]');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('exercicio');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('data_aprovacao_loa');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('DATA_YYYYMMDD');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('numero_loa');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('data_aprovacao_ldo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('DATA_YYYYMMDD');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('numero_ldo');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('data_aprovacao_qdd');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('DATA_YYYYMMDD');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

$obExportador->roUltimoArquivo->roUltimoBloco->addColuna('numero_qdd');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
?>
