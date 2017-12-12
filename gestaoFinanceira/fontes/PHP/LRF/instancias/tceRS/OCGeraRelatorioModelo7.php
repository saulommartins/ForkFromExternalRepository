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
* Oculto para modelo 7 do modulo LRF
* Data de Criação: 09/08/2005

* @author Analista: Diego Barbosa
* @author Desenvolvedor: Anderson R. M. Buzo

* @ignore

$Revision: 30668 $
$Name$
$Author: cako $
$Date: 2006-10-27 16:37:56 -0300 (Sex, 27 Out 2006) $

* Casos de uso: uc-02.05.09, uc-02.01.35
*/

/*
$Log$
Revision 1.8  2006/10/27 19:37:33  cako
Bug #6773#

Revision 1.7  2006/08/25 17:50:22  fernando
Bug #6773#

Revision 1.6  2006/07/05 20:45:22  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"       );
include_once( CAM_GF_LRF_NEGOCIO."RLRFRelatorioModelo7.class.php"  );

$obRegra      = new RLRFRelatorioModelo7;
$obRRelatorio = new RRelatorio();
$obPDF        = new ListaFormPDF( 'L' );

// Adicionar logo nos relatorios
if ( count( $sessao->filtro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $sessao->filtro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->recuperaCabecalho ( $arConfiguracao          );

$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

foreach ($sessao->filtro['inCodEntidade'] as $inCodEntidade) {
    $arEntidades[] = $sessao->nomFiltro['nom_entidade'][$inCodEntidade];
}

$obPDF->addFiltro( 'Entidades relacionadas', $arEntidades                                            );
$obPDF->addFiltro( 'Mês'                   , $sessao->nomFiltro['nom_mes'][$sessao->filtro['inMes']] );

$rsVazio = new RecordSet;

$sessao->transf5->addFormatacao( "liquidado_rp"         , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "liquidado"            , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "lq_adicao_exclusao"   , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "lq_ajustado"          , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "total_liq_ajust"      , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "a_liquidar_rp"        , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "a_liquidar"           , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "n_lq_adicao_exclusao" , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "n_lq_ajustado"        , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "total_n_liq_ajust"    , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "saldo"                , "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "saldo_adicao_exclusao", "NUMERIC_BR" );
$sessao->transf5->addFormatacao( "total_saldo_ajust"    , "NUMERIC_BR" );

$obPDF->addRecordSet($rsVazio);
$obPDF->setAlinhamento ( "C" );
$obPDF->setAlturaCabecalho( 8 );
if (Sessao::read('modulo') != 8)
    $obPDF->addCabecalho("Modelo 7 - Demonstrativo dos Restos a Pagar", 100, 12 );
$obPDF->addCampo( '', 8 );

$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho( 8 );
$obPDF->setAlinhamento ( "C" );
if (Sessao::read('modulo') != 8)
    $obPDF->addCabecalho('LC Federal nº 101/2000, art. 54 e alínea "b" do inciso III', 100, 12 );
$obPDF->addCampo( '', 8 );

$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho( 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCabecalho("Em R$", 100,  8 );
$obPDF->addCampo( '', 8 );

$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho( 6 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( ""                              ,  7, 10, '', '', 'TRL' , '205,206,205' );
$obPDF->addCabecalho( "RESTOS A PAGAR PROCESSADOS"    , 36, 10, '', '', 'TRLB' );
$obPDF->addCabecalho( "RESTOS A PAGAR NÃO PROCESSADOS", 36, 10, '', '', 'TRLB' );
$obPDF->addCabecalho( ""                              , 21, 10, '', '', 'TRL' , '205,206,205' );
$obPDF->addCampo( '', 8 );
$obPDF->addCampo( '', 8 );

$obPDF->addRecordSet($rsVazio);
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho( 5 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "Código do Recurso"           ,  7,  8, '', '', 'LR'  , '205,206,205' );
$obPDF->addCabecalho( "Exercícios Anteriores"       ,  7,  8, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "\nExercício Atual"           , 22,  8, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "\nTOTAL"                     ,  7,  8, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "Exercícios Anteriores"       ,  7,  8, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "\nExercício Atual"           , 22,  8, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "\nTOTAL"                     ,  7,  8, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "Disponibilidade Financeira\n ", 21,  8, '', '', 'LRB', '205,206,205' );
$obPDF->addCampo( '', 8 );

$obPDF->addRecordSet( $sessao->transf5 );
$obPDF->setQuebraPaginaLista( false );
$obPDF->setAlturaCabecalho( 5 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho( "\n "               ,  7, 7, '', '', 'LRB' , '205,206,205' );
$obPDF->addCabecalho( "\nContabil"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nContabil"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "Adição/ Exclusão"  ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nAjustado"        ,  8, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nAjustado"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nContabil"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nContabil"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "Adição/ Exclusão"  ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nAjustado"        ,  8, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nAjustado"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nContabil"        ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "Adição/ Exclusão"  ,  7, 7, '', '', 'TLRB', '205,206,205' );
$obPDF->addCabecalho( "\nAjustado"        ,  7, 7, '', '', 'TLRB', '205,206,205' );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo ( "recurso"              , 6, '', '', 'TLRB' );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo ( "liquidado_rp"         , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "liquidado"            , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "lq_adicao_exclusao"   , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "lq_ajustado"          , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "total_liq_ajust"      , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "a_liquidar_rp"        , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "a_liquidar"           , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "n_lq_adicao_exclusao" , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "n_lq_ajustado"        , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "total_n_liq_ajust"    , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "saldo"                , 6, '', '', 'TLRB' );
$obPDF->addCampo ( "saldo_adicao_exclusao", 6, '', '', 'TLRB' );
$obPDF->addCampo ( "total_saldo_ajust"    , 6, '', '', 'TLRB' );

$obPDF->show();
?>
