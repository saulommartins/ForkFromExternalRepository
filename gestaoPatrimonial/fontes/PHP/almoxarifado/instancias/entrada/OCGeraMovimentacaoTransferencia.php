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
    * Data de Criação   : 23/03/2009

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obBirtPreview = new PreviewBirt( 3, 29, 12);

$obBirtPreview->setVersaoBirt( '2.5.0' );

$obBirtPreview->setTitulo     ( 'Relatório de Entrada por Tranferência' );

$obBirtPreview->addParametro( 'prNumLancamento', $_REQUEST['inNumLancamento'] );

if ($_REQUEST['exercicioReemissao'] != '') {
    $obBirtPreview->addParametro  ( "prExercicioReemissao" , $_REQUEST['exercicioReemissao'] );
} else {
    $obBirtPreview->addParametro  ( "prExercicioReemissao" , '' );
}

if ($_REQUEST['inNomNatureza'] != '') {
    $obBirtPreview->addParametro  ( "prNomNatureza" , $_REQUEST['inNomNatureza'] );
} else {
    $obBirtPreview->addParametro  ( "prNomNatureza" , '' );
}

$obBirtPreview->preview();

?>
