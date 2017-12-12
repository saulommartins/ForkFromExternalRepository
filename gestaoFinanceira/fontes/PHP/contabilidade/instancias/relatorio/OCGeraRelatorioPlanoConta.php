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
    * Data de Criação   : 25/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    * $Id: OCGeraRelatorioPlanoConta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.19
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF(  );

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Plano de Contas" );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( Sessao::read('rsPlanoConta') );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CÓDIGO", 26, 10);
$obPDF->setAlinhamento( "D" );
$obPDF->addCabecalho("COD. REDUZ",14, 10);
$obPDF->setAlinhamento( "C" );
$obPDF->addCabecalho("SC",14, 10);
$obPDF->setAlinhamento( "C" );
$obPDF->addCabecalho("DESCRIÇÃO",60, 10);
$obPDF->addIndentacao("nivel","cod_estrutural","  ");
$obPDF->addIndentacao("nivel","nom_conta","  ");
//$obPDF->addQuebraLinha("nivel",0,5);
$obPDF->addQuebraPagina("pagina",1);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("cod_estrutural", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("cod_plano", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("nom_sistema", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("nom_conta", 8 );

$obPDF->show();
//$obPDF->montaPDF();
//$obPDF->OutPut();
?>
