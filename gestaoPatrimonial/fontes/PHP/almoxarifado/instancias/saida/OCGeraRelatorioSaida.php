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
    * Página de geração do recordSet para o Relatório Metas de Execução da Despesa
    * Data de Criação   : 04/12/2006

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Alessandro La-Rocca Silveira

    * @ignore

    * Casos de uso: uc-03.03.11

    $Id: OCGeraRelatorioSaida.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$obBirtPreview = new PreviewBirt( 3, 29, 4);

// Versão NOVA
$obBirtPreview->setVersaoBirt( '2.5.0' );

$obBirtPreview->setTitulo     ( 'Relatório de Saída por Requisição' );

$obBirtPreview->addParametro( 'prExercicio', Sessao::getExercicio() );

$obBirtPreview->addParametro( 'prCodAlmoxarifado', $_REQUEST['inCodAlmoxarifado'] );

$obBirtPreview->addParametro( 'prNumLancamento', $_REQUEST['inNumLancamento'] );

$obBirtPreview->addParametro( 'prCodRequisicao', $_REQUEST['inCodRequisicao'] );

if ($_REQUEST['stExercicioRequisicao'] != '') {
    $obBirtPreview->addParametro( 'prExercicioReemissao', $_REQUEST['stExercicioRequisicao'] );
} elseif ($_REQUEST['exercicioReemissao'] != '') {
    $obBirtPreview->addParametro( 'prExercicioReemissao', $_REQUEST['exercicioReemissao'] );
} else {
    $obBirtPreview->addParametro( 'prExercicioReemissao', '' );
}

$obBirtPreview->preview();

?>
