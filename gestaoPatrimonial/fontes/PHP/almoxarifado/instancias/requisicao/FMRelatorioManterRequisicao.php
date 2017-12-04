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
    * Página de processamento para Requisição
    * Data de criação : 29/01/2007

    * @author Analista: Gelson
    * @author Programador: Tonismar R. Bernardo

    * @ignore

    $Revision: 28218 $
    $Name$
    $Author: luiz $
    $Date: 2008-02-27 07:59:54 -0300 (Qua, 27 Fev 2008) $

    Caso de uso: uc-03.03.10
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obBirtPreview = new PreviewBirt( 3, 29, 2 );
$obBirtPreview->setVersaoBirt( '2.5.0');
$obBirtPreview->setTitulo     ( 'Relatório de Requisição' );
$obBirtPreview->addParametro  ( "prCodRequisicao" , $_REQUEST['inCodRequisicao'] );
$obBirtPreview->addParametro  ( "prCodAlmoxarifado" , $_REQUEST['inCodAlmoxarifado'] );
if (!isset($_REQUEST['stExercicio'])) {
    $obBirtPreview->addParametro  ( "stExercicio" , Sessao::getExercicio() );
} else {
    $obBirtPreview->addParametro  ( "stExercicio" , $_REQUEST['stExercicio']);
}
$obBirtPreview->addParametro  ( "cod_acao" , Sessao::read('acao'));
$obBirtPreview->preview();
