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
    * Relatório de posição financeira por Natureza
    * Data de Criação   : 08/04/2003

    * @author Desenvolvedor  Ricardo Lopes de Alencar

    * @ignore

    * Casos de uso: uc-03.01.09

    $Id:$

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obRelatorio = new PreviewBirt(3,6,15);
$obRelatorio->setVersaoBirt('2.5.0');

$obRelatorio->addParametro('stExercicio'     , $_REQUEST['exercicio']);
$obRelatorio->addParametro('inCodBemInicial' , $_REQUEST['codInicial']);
$obRelatorio->addParametro('inCodBemFinal'   , $_REQUEST['codFinal']);
$obRelatorio->addParametro('inCodEntidade'   , $_REQUEST['codEntidade']);
$obRelatorio->addParametro('inCodNatureza'   , $_REQUEST['codNatureza']);

$obRelatorio->preview();

?>
