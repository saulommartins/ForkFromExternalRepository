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
    * Página de redirecionamento para a configuração de atributos dinâmicos
    * Data de Criação   : 18/05/2004

    * @author Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMManterAtributo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.01
*/

/*
$Log$
Revision 1.3  2006/09/15 11:02:28  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/Framework.inc.php';
include_once(CLA_SESSAO);
;
foreach($_GET as $key=>$value)
    $stUrl .= "&$key=$value";

$stPagina = '../../configuracao/configuracao/FMManterAtributo.php?'.Sessao::getId()."$stUrl";
header("Location: ".$stPagina);
exit;

?>
