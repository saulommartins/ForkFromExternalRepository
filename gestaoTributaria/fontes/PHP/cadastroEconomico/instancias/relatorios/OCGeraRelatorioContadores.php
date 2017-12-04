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
    * Data de Criação   : 28/04/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: OCGeraRelatorioContadores.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.14

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:30  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_FW_PDF."ListaPDF.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Cadastro Econômico - Relatórios"   );
$obPDF->setTitulo            ( "Contadores" );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( Sessao::read( "sessao_transf5" ) );
$arFiltroSessao = Sessao::read( "filtroRelatorio" );

if ($arFiltroSessao["stTipoRelatorio"] == "analitico") {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "CONTADOR"        ,25, 10 );
    $obPDF->addCabecalho   ( "REGISTRO"        ,10, 10 );
    $obPDF->addCabecalho   ( "ENDEREÇO"        ,25, 10 );
    $obPDF->addCabecalho   ( "TELEFONE"        ,10, 10 );
    $obPDF->addCabecalho   ( "INS. ECONÔMICA"  ,15, 10 );
    $obPDF->addCabecalho   ( "ENDEREÇO"        ,15, 10 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "contador"            , 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "num_registro"        , 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "endereco"            , 8 );
    $obPDF->addCampo       ( "fone_comercial"      , 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "inscricao_economica" , 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "endereco_cadastro"   , 8 );
} else {
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCabecalho   ( "CONTADOR"        ,25, 10 );
    $obPDF->addCabecalho   ( "REGISTRO"        ,10, 10 );
    $obPDF->addCabecalho   ( "ENDEREÇO"        ,25, 10 );
    $obPDF->addCabecalho   ( "TELEFONE"        ,10, 10 );

    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "contador"            , 8 );
    $obPDF->setAlinhamento ( "C" );
    $obPDF->addCampo       ( "num_registro"        , 8 );
    $obPDF->setAlinhamento ( "L" );
    $obPDF->addCampo       ( "endereco"            , 8 );
    $obPDF->addCampo       ( "fone_comercial"      , 8 );
}

$obPDF->show();
?>
