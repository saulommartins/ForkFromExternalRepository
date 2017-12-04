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
    * Data de criação : 04/11/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.01.09

    $Id: OCGeraFichaPatrimonial.php 66009 2016-07-07 13:32:43Z lisiane $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obRelatorio = new PreviewBirt(3, 6, 14);
$obRelatorio->setVersaoBirt('2.5.0');

$obRelatorio->addParametro('inCodBemInicial' , $request->get('inCodBemInicial'));
$obRelatorio->addParametro('inCodBemFinal'   , $request->get('inCodBemFinal'));
$obRelatorio->addParametro('inCodOrgao'      , $request->get('hdnUltimoOrgaoSelecionado'));
$obRelatorio->addParametro('inCodLocal'      , $request->get('inCodLocal'));
$obRelatorio->addParametro('inCodNatureza'   , $request->get('inCodNatureza'));
$obRelatorio->addParametro('inCodGrupo'      , $request->get('inCodGrupo'));
$obRelatorio->addParametro('inCodEspecie'    , $request->get('inCodEspecie'));
$obRelatorio->addParametro('inCodEntidade'   , $request->get('inCodEntidade'));

$obRelatorio->addParametro('boQuebraPagina'  , $request->get('boQuebraPagina'));
$obRelatorio->addParametro('stTipoRelatorio' , $request->get('stTipoRelatorio'));
$obRelatorio->addParametro('stHistorico'     , $request->get('stHistorico'));

$obRelatorio->addParametro('stDataInicial'                , $request->get('stDataInicial'));
$obRelatorio->addParametro('stDataFinal'                  , $request->get('stDataFinal'));
$obRelatorio->addParametro('stPeriodoInicialIncorporacao' , $request->get('stPeriodoInicialIncorporacao'));
$obRelatorio->addParametro('stPeriodoFinalIncorporacao'   , $request->get('stPeriodoFinalIncorporacao'));
$obRelatorio->addParametro('stDepreciacoes'               , $request->get('stDepreciacoes'));

$obRelatorio->preview();

?>