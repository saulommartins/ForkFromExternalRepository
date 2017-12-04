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
    * Relatório Cusmonizável
    * Data de Criação   : 29/12/2004

    * @author Desenvolvedor  ???

    * @ignore

    $Revision: 12234 $
    $Name$
    $Autor: $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.01.09
*/

/*
$Log$
Revision 1.7  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.6  2006/07/06 12:11:28  diego

*/

//include_once "../../classes/sessao.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/sessaoLegado.class.php';

//$stLocation = "../../modul../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/RCustomizavel.php?".Sessao::getId()."&acao=824&stAcao=&modulo=6&funcionalidade=28";
$stLocation = "../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/relatorios/RCustomizavel.php?".Sessao::getId()."&acao=824&stAcao=&modulo=6&funcionalidade=28";

header("Location: ". $stLocation);
?>
