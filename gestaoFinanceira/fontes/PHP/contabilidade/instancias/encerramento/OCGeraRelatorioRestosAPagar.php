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
    * Pagina oculta para gerar relatorio
    * Data de Criação   : 06/10/2004

    * @author Desenvolvedor: Anderson Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: OCGeraRelatorioAnexo10.php 35926 2008-11-25 10:56:57Z eduardoschitz $

    * Casos de uso: uc-02.02.07
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$arDados      = Sessao::Read('argeral');

$rsRecordSet  = new RecordSet;

$obRRelatorio->setCodigoEntidade( $arDados['entidade'][0] );
$obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$obPDF->setEnderecoPrefeitura( $arConfiguracao );
//totalizador
$arTotal['entidade'] = '';
$arTotal['cod_recurso'] = '';
$arTotal['nom_recurso'] = 'Total';

foreach ($arDados as $key => $arTemp) {
    $arTotal['saldo_atual']   = $arTotal['saldo_atual']   + $arTemp['saldo_atual'];
    $arTotal['liquidados_nao_pagos']       = $arTotal['liquidados_nao_pagos']       + $arTemp['liquidados_nao_pagos'];
    $arTotal['empenhados_nao_liquidados']  = $arTotal['empenhados_nao_liquidados']  + $arTemp['empenhados_nao_liquidados'];
    $arTotal['rp_processados']             = $arTotal['rp_processados']             + $arTemp['rp_processados'];
    $arTotal['rp_nao_processados']         = $arTotal['rp_nao_processados']         + $arTemp['rp_nao_processados'];
    $arTotal['total']           = $arTotal['total']           + $arTemp['total'];

}

$arDados[] = $arTotal;

$rsRecordSet->preenche($arDados);

$obPDF->setAcao                  ( "Relatório de Restos a pagar por recurso" );
$obPDF->setUsuario               ( Sessao::getUsername()                                );

$rsRecordSet->addFormatacao("saldo_atual"   , "NUMERIC_BR");
$rsRecordSet->addFormatacao("rp_processados"        , "NUMERIC_BR");
$rsRecordSet->addFormatacao("rp_nao_processados"        , "NUMERIC_BR");
$rsRecordSet->addFormatacao("liquidados_nao_pagos"       , "NUMERIC_BR");
$rsRecordSet->addFormatacao("empenhados_nao_liquidados" , "NUMERIC_BR");
$rsRecordSet->addFormatacao("total"           , "NUMERIC_BR");

$obPDF->addRecordSet            ($rsRecordSet );
// Monta cabecalho dos dados
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ("Entidade"    ,8, 10 );
$obPDF->addCabecalho   ("Recurso"  ,26, 10 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho   ("Saldo 31/12/".Sessao::read('exercicio').""  ,10, 10 );
$obPDF->addCabecalho   ("Restos Proc."  ,10, 10 );
$obPDF->addCabecalho   ("Restos Nao Proc."  ,10, 10 );
$obPDF->addCabecalho   ("À Liquidar"  ,10, 10 );
$obPDF->addCabecalho   ("Liquidado à Pagar" ,10, 10 );
$obPDF->addCabecalho   ("Saldo com Inscrição" ,10, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("entidade" , 8 );
$obPDF->addCampo       ("[cod_recurso] - [nom_recurso]"     , 8 );
$obPDF->setAlinhamento ( "R" );

$obPDF->addCampo       ("saldo_atual"     , 8 );
$obPDF->addCampo       ("rp_processados"       , 8 );
$obPDF->addCampo       ("rp_nao_processados"       , 8 );
$obPDF->addCampo       ("liquidados_nao_pagos"       , 8 );
$obPDF->addCampo       ("empenhados_nao_liquidados"      , 8 );
$obPDF->addCampo       ("total"      , 8 );

$obPDF->show();
?>
