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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 13/08/2004

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    * $Id: OCGeraRelatorioHistoricoPadrao.php 60866 2014-11-19 18:02:40Z jean $

    * Casos de uso: uc-02.02.20
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF;

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Históricos Padrões" );
$obPDF->setSubTitulo         ( "Exercício - " . Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsRecordSet = new RecordSet;
$rsRecordSet = Sessao::read('rsHistoricoPadrao');

$obPDF->addRecordSet( $rsRecordSet );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CÓDIGO", 20, 10);
$obPDF->addCabecalho("DESCRICAO", 60, 10);
$obPDF->setAlinhamento( "C" );
$obPDF->addCabecalho("COMPLEMENTO",20, 10);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("cod_historico", 8 );
$obPDF->addCampo("nom_historico", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("complemento", 8 );

$obPDF->show();
?>
