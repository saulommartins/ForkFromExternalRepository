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
/* Página de Formulário do Cadastro de Padrões
 *
 * Data de Criação : 04/03/2009

 * @author Analista : Dagiane
 * @author Desenvolvedor : Rafael Garbin

 * @package URBEM
 * @subpackage

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

echo "<html>
        <body bgcolor='#E6E6E6' leftmargin=0 topmargin=0>"; //Como não tem o cabelho, deve abrir com o echo

$stPrograma = "ManterPadrao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

Sessao::write("stOrigem", "CARGO"); // Váriavel lida no cadastro do padrão

$obIFrame = new IFrame;
$obIFrame->setName("oculto");

$obIFrame1 = new IFrame;
$obIFrame1->setName   ("telaPrincipal");
$obIFrame1->setWidth  ("100%"         );
$obIFrame1->setHeight ("95%"          );
$obIFrame1->setSrc    (CAM_GRH_FOL_INSTANCIAS."padrao/FMManterPadrao.php");

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "5%"          );

$obIFrame->show();
$obIFrame1->show();
$obIFrame2->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
