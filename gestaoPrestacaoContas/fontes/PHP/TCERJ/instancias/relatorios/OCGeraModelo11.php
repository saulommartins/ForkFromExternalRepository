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
*/

/*
$Log$
Revision 1.2  2006/07/21 13:35:28  rodrigo
Correções nos casos de uso no codigo

Revision 1.1  2006/07/21 13:22:57  rodrigo
Caso de uso 06.02.11

Revision 1.1  2006/07/18 16:25:51  rodrigo
Caso de Uso #06.02.12

Revision 1.5  2006/07/06 14:03:02  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:09:53  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorioAgata.class.php" );

$stInFiltro = "";
$stFiltro = "";

$obAgata = new RRelatorioAgata( "../../../../../../gestaoPrestacaoContas/fontes/AGT/TCRJ/RLModelo11.agt" );
//if ( count(//sessao->filtro['inNumCGM']) > 0 ) {
//    foreach (//sessao->filtro['inNumCGM'] as $key => $valor) {
//       $stInFiltro .= $valor.',';
//    }
//    $stInFiltro = substr($stInFiltro, 0,(strlen($stInFiltro)-1));
//
//    $stFiltro = ' and entidade.numcgm in ('.$stInFiltro.')';
//}

$aux = $obAgata->getSQLWhere(0);

$stFiltro = $aux.$stFiltro;

$obAgata->setSQLWhere( $stFiltro, 0);

$obAgata->setParameter( '$dtData' , date("d").'/'.date("m").'/'.Sessao::getExercicio());

$ok = $obAgata->generateDocument();
if (!$ok) {
    echo $obAgata->getError();
} else {
    $obAgata->fileDialog();
}
