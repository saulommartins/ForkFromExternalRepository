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
    * Página de processamento oculto para o relatório de consulta de Imóvel
    * Data de Criação   : 21/06/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCGeraRelatorioImovelConsulta.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.5  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../includes/Constante.inc.php';
//include_once("../../../includes/tabelas.inc.php"    );
include_once( CAM_INCLUDES."IncludeClasses.inc.php" );
include_once( CAM_GT_CIM_NEGOCIO."RRelatorio.class.php"      );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

$obRRelatorio->setExercicio     ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao    );

$obPDF->setModulo            ( "Relatorio"                       );
$obPDF->setTitulo            ( "Consulta de Imóvel"              );
$obPDF->setSubTitulo         ( "Exercicio - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername()                 );
$obPDF->setEnderecoPrefeitura( $arConfiguracao                   );

//RELATÓRIO DE LOTE - DADOS DO LOTE
$obPDF->addRecordSet( Sessao::read('lote') );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Dados do Lote" ,20, 10 );
$obPDF->addCabecalho   ( ""              ,80, 10 );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "rotulo", 8 );
$obPDF->addCampo       ( "valor" , 8 );

//RELATÓRIO DE LOTE - LISTA DE CONFRONTAÇÕES
$obPDF->addRecordSet( Sessao::read( 'lote_confrontacoes' ) );
$obPDF->setQuebraPaginaLista( false );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "Ponto Cardeal" ,15, 10 );
$obPDF->addCabecalho   ( "Tipo"          ,15, 10 );
$obPDF->addCabecalho   ( "Descrição"     ,50, 10 );
$obPDF->addCabecalho   ( "Extensão"      ,10, 10 );
$obPDF->addCabecalho   ( "Testada"       ,10, 10 );

$obPDF->setAlinhamento ( "L"                       );
$obPDF->addCampo       ( "stNomePontoCardeal"  , 8 );
$obPDF->addCampo       ( "stLsTipoConfrotacao" , 8 );
$obPDF->addCampo       ( "stDescricao"         , 8 );
$obPDF->setAlinhamento ( "R"                       );
$obPDF->addCampo       ( "flExtensao"          , 8 );
$obPDF->setAlinhamento ( "C"                       );
$obPDF->addCampo       ( "stTestada"           , 8 );

$obPDF->show();
?>
