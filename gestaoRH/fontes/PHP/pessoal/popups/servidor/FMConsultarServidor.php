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
    * Página de Formulário para cadastro de servdor
    * Data de criação : 23/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Programador: Diego Lemos de Souza

    * @ignore

    $Id: FMConsultarServidor.php 66023 2016-07-08 15:01:19Z michel $

    * Casos de uso: uc-04.04.07
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stLink  = "?inCodContrato=".$request->get('inCodContrato');
$stLink .= "&inRegistro=".$request->get('inRegistro');

$obIFrame = new IFrame;
$obIFrame->setName("oculto");

$obIFrame1 = new IFrame;
$obIFrame1->setName   ("telaPrincipal");
$obIFrame1->setWidth  ("100%"         );
$obIFrame1->setHeight ("95%"          );
$obIFrame1->setSrc    (CAM_GRH_PES_POPUPS."servidor/FMConsultarServidorAbaServidor.php".$stLink);

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem");
$obIFrame2->setWidth  ( "100%"        );
$obIFrame2->setHeight ( "5%"          );

$obIFrame->show();
$obIFrame1->show();
$obIFrame2->show();

?>
