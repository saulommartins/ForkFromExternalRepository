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
    * Página de Geração de Relatório de Ïtens
    * Data de Criação   : 27/01/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.02.11
                     uc-06.02.12
                     uc-06.02.13
                     uc-06.02.15
                     uc-06.02.17
                     uc-06.02.18
*/

/*
$Log$
Revision 1.5  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:42:06  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorioAgata.class.php" );
include_once( TADM."TAdministracaoModeloArquivosDocumento.class.php");

$stInFiltro = "";
$stFiltroPageHeader = "";
$stFiltroDetails    = "";

$obAgata = new RRelatorioAgata(1);
//$obAgata->setOutputPath('/home/zank/tmp.swx');
//if ( count(//sessao->filtro['inNumCGM']) > 0 ) {
//    foreach (//sessao->filtro['inNumCGM'] as $key => $valor) {
//       $stInFiltro .= $valor.',';
//    }
//    $stInFiltro = substr($stInFiltro, 0,(strlen($stInFiltro)-1));
//
//    $stFiltro = ' and entidade.numcgm in ('.$stInFiltro.')';
//}
$filtroPageHeader = $obAgata->getSQLWhere(0).$stFiltroPageHeader;
$filtroDetails    = $obAgata->getSQLWhere(1).$stFiltroDetails;

//echo("<pre><hr>");print_r($obAgata->getSQLWhere(0));echo("</pre><hr>");die();

$obAgata->setSQLWhere( $filtroPageHeader, 0 );

$obAgata->setSQLWhere( $filtroDetails, 1 );

$obAgata->Header();

$ok = $obAgata->parseOpenOffice();
if (!$ok) {
    echo $obAgata->getError();
} else {
    $obAgata->fileDialog();
}
