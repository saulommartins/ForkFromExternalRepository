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
* Página de relatório de Vale-Transporte
* Data de Criação   : 14/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30922 $
$Name$
$Author: tiago $
$Date: 2007-06-26 17:19:23 -0300 (Ter, 26 Jun 2007) $

* Casos de uso: uc-04.06.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"             );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                   );

$obRRelatorio = new RRelatorio;
$obRRelatorio->setCodigoEntidade   ( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio()        );

$obRCGM       = new RCGM;
$obPDF        = new ListaPDF('L');

//Filtro do fornecedor
$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

if ($arSessaoFiltroRelatorio['inCodFornecedor'] != 0) {
    $obRCGM->setNumCGM( $arSessaoFiltroRelatorio['inCodFornecedor'] );
    $obRCGM->consultar( $rsCGM );
    $stFiltro = $obRCGM->getNomCGM();
    $obPDF->addFiltro( "Fornecedor:       "    , $stFiltro );
} else {
    $stFiltro = "Todos";
    $obPDF->addFiltro( "Fornecedor:       "    , $stFiltro );
}

//Filtro da Periodicidade
if ($arSessaoFiltroRelatorio['stDataInicial'] and $arSessaoFiltroRelatorio['stDataFinal']) {
    if ($arSessaoFiltroRelatorio['inPeriodicidade'] == 1) {
        $stFiltro = $arSessaoFiltroRelatorio['stDataInicial'];
        $obPDF->addFiltro( "Periodicidade:       "    , $stFiltro );
    } else {
        $stFiltro = $arSessaoFiltroRelatorio['stDataInicial'] ." até ". $arSessaoFiltroRelatorio['stDataFinal'];
        $obPDF->addFiltro( "Periodicidade:       "    , $stFiltro );

    }
} elseif ($arSessaoFiltroRelatorio['stDataInicial'] and !$arSessaoFiltroRelatorio['stDataFinal']) {
    $stFiltro = "A partir de: ".$arSessaoFiltroRelatorio['stDataInicial'];
    $obPDF->addFiltro( "Periodicidade:       "    , $stFiltro );
} elseif (!$arSessaoFiltroRelatorio['stDataInicial'] and $arSessaoFiltroRelatorio['stDataFinal']) {
    $stFiltro = "Até: ".$arSessaoFiltroRelatorio['stDataFinal'];
    $obPDF->addFiltro( "Periodicidade:       "    , $stFiltro );
}

//Filtro da Ordem
if ($arSessaoFiltroRelatorio['inCodOrdem'] == 1) {
    $stFiltro = "Data";
    $obPDF->addFiltro( "Ordem:       "    , $stFiltro );
} elseif ($arSessaoFiltroRelatorio['inCodOrdem'] == 2) {
    $stFiltro = "Nome de Fornecedor";
    $obPDF->addFiltro( "Ordem:       "    , $stFiltro );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatório"                  );
$obPDF->setTitulo            ( "Registros de Vale-Transporte");
$obPDF->setSubTitulo         ( ""                           );
$obPDF->setUsuario           ( Sessao::getUsername()        );
$obPDF->setEnderecoPrefeitura( $arConfiguracao              );

$obPDF->addRecordSet( Sessao::read('transf5') );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ("CGM"           ,13, 10);
$obPDF->addCabecalho   ("Fornecedor"    ,30, 10);
$obPDF->addCabecalho   ("Origem"        ,15, 10);
$obPDF->addCabecalho   ("Destino"       ,15, 10);
$obPDF->addCabecalho   ("Vigencia"      ,10, 10);
$obPDF->addCabecalho   ("Custo Unitário",10, 10);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("numcgm"     , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("nom_cgm"    , 8 );
$obPDF->addCampo       ("origem"     , 8 );
$obPDF->addCampo       ("destino"    , 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("vigencia"   , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ("custo"      , 8 );

$obPDF->show();
//$obPDF->montaPDF();
//$obPDF->OutPut();
?>
