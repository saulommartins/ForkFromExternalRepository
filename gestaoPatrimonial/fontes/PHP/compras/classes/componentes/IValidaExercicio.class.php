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
    * Arquivo de validação do exercício
    * Data de Criação: 05/03/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 27924 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-02-08 15:19:40 -0200 (Sex, 08 Fev 2008) $

    * Casos de uso: uc-03.04.01

*/

/*
$Log$
Revision 1.1  2007/03/05 19:49:12  hboaventura
Componente criado para validar se o exercicio da sessão é o mesmo do sistema

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
$stExercicioAtual = date('Y');
$stExercicioSessao = Sessao::getExercicio();

if ($stExercicioAtual >  $stExercicioSessao) {
    header( "location:".CAM_GP_COM_COMPONENTES."validadeGP.php?".Sessao::getId()."&exercicio=".$stExercicioSessao );
}

?>
