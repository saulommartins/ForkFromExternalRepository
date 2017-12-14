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
    * Página de geração de relatório
    * Data de criação : 10/05/2012

    Caso de uso: uc-03.01.09

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obRelatorio = new PreviewBirt(5, 25, 6);
$obRelatorio->setVersaoBirt('2.5.0');

$codigoGrupo= array();

$codigoGrupo = explode("/",$_REQUEST['inCodGrupo']);

$obRelatorio->addParametro('inCodImovel'     , $_REQUEST['inCodImovel']);
$obRelatorio->addParametro('HdninCodImovel'  , $_REQUEST['HdninCodImovel']);
$obRelatorio->addParametro('stImovel'        , $_REQUEST['stImovel']);

$obRelatorio->addParametro('stExercico'      , $_REQUEST['stExercicio']);

$obRelatorio->addParametro('inCGM'           , $_REQUEST['inCGM']);
$obRelatorio->addParametro('HdninCGM'        , $_REQUEST['HdninCGM']);
$obRelatorio->addParametro('stNomCGM'        , $_REQUEST['stNomCGM']);

$obRelatorio->addParametro('inCodGrupo'      , $codigoGrupo[0]);
$obRelatorio->addParametro('HdninCodGrupo'   , $_REQUEST['HdninCodGrupo']);
$obRelatorio->addParametro('stGrupo'         , $_REQUEST['stGrupo']);

$obRelatorio->preview();

?>
