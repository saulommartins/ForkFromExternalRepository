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
  * ESTE ARQUIVO NÃO É MAIS UTILIZADO,
  * A CHAMADA QUE OCRELATORIOBAIXA.PHP FAZIA PARA ESTE ARQUIVO AGORA É EXECUTADA NO PRÓPRIO OCRELATORIOBAIXA.PHP
  * Desenvolvedor: Carlos
  *
  * Página de
  * Data de criação : 31/10/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    Caso de uso: uc-03.01.18
**/

/*
$Log$
Revision 1.9  2006/07/06 14:07:05  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:11:27  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GP_PAT_NEGOCIO."RPatrimonioRelatorioBaixa.class.php");

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF("L");
$rsVazio      = new RecordSet;

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );

$arFiltro = Sessao::read('filtroRelatorio');

$obPDF->setModulo            ( "Relatorio"   );
$obPDF->setTitulo            ( "Bens Baixados" );
$obPDF->setSubTitulo         ( "Período: ". $arFiltro['stDataInicial']." até ". $arFiltro['stDataFinal'] );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsRecordSet = Sessao::read('recordset');

$obPDF->addRecordSet( $rsRecordSet );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho   ( "CÓDIGO DO BEM" , 12,10);
$obPDF->addCabecalho   ( "DESCRIÇÃO DA CONTA" , 50,10);
if ($arFiltro["inCodAtributo"] > 0) {
    $obPDF->addCabecalho   ( $rsRecordSet->getCampo("nom_atributo")    ,10, 10);
}
$obPDF->addCabecalho   ( "AQUISIÇÃO"     ,9, 10);
$obPDF->addCabecalho   ( "VALOR"         ,8, 10);
$obPDF->addCabecalho   ( "BAIXA"      ,12, 10);

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "codigo"            , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "descricao"            , 8 );
$obPDF->setAlinhamento ( "C" );
if ($arFiltro['inCodAtributo'] > 0) {
    $obPDF->addCampo       ( "atributo"    , 8 );
}
$obPDF->addCampo       ( "aquisicao"     , 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo       ( "valor"    , 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "baixa"       , 8 );

$obPDF->show();
