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
    * Ficha Patrimonial
    * Data de Criação   : ???

    * @author Desenvolvedor  Lucas Stephanou

    * @ignore

    $Revision: 16313 $
    $Name$
    $Autor: $
    $Date: 2006-10-03 12:45:20 -0300 (Ter, 03 Out 2006) $

    * Casos de uso: uc-03.01.21
*/

/*
$Log$
Revision 1.3  2006/10/03 15:45:20  gelson
Correção do caso de uso.

Revision 1.2  2006/10/03 15:07:46  domluc
Corrigido caso de uso

Revision 1.1  2006/09/28 08:37:57  domluc
Caso de Uso 03.01.21

Revision 1.16  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.15  2006/07/06 12:11:28  diego

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
$stLink = "";
foreach ($_GET as $stVariavel => $stValor) {
    $stLink .= $stVariavel."=".$stValor."&";
}
$stLink = substr( $stLink , 0, strlen( $stLink) - 1 );
header("Location: ../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/instancias/relatorio/FLListaPatrimonial.php?".$stLink);
?>
