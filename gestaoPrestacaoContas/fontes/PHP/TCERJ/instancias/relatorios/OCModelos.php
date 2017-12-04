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
    * Página de Oculto para Relatório de modelos
    * Data de Criação   : 25/01/2006

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
Revision 1.5  2006/07/18 16:25:51  rodrigo
Caso de Uso #06.02.12

Revision 1.4  2006/07/06 13:52:24  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:42:06  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_FW_PDF."RRelatorio.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$pgGera = $_GET['pgGera'] ?  $_GET['pgGera'] : $_POST['pgGera'];

$obRRelatorio = new RRelatorio;
$obRRelatorio->executaFrameOculto( $pgGera );
