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
* Página de relatório de Fornecedor
* Data de Criação   : 13/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: tiago $
$Date: 2007-06-26 17:19:23 -0300 (Ter, 26 Jun 2007) $

* Casos de uso: uc-04.06.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"             );

$obRRelatorio = new RRelatorio;
$obRRelatorio->setCodigoEntidade   ( Sessao::getCodEntidade($boTransacao) );
$obRRelatorio->setExercicioEntidade( Sessao::getExercicio()        );

$obPDF        = new ListaPDF();

if ( Sessao::read('inTipoBeneficio') == 1 ) {
    $stFiltro = "Vale-Transporte";
}
$obPDF->addFiltro( "Tipo de Benefício:       "    , $stFiltro );

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatório"                  );
$obPDF->setTitulo            ( "Relatório de Fornecedor"    );
$obPDF->setSubTitulo         ( ""                           );
$obPDF->setUsuario           ( Sessao::getUsername()        );
$obPDF->setEnderecoPrefeitura( $arConfiguracao              );

$obPDF->addRecordSet( Sessao::read('transf5') );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ("CGM"      , 13, 10);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ("Fornecedor",80, 10);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ("numcgm"     , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ("nom_cgm" , 8    );

$obPDF->show();
//$obPDF->montaPDF();
//$obPDF->OutPut();
?>
