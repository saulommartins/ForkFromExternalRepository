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
    * Data de Criação: 05/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCGeraRelatorioVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,7,3);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioVeiculo');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

if ($_REQUEST['inCodVeiculo'] != '') {
    $preview->addParametro( 'inCodVeiculo', $_REQUEST['inCodVeiculo'] );
} else {
    $preview->addParametro( 'inCodVeiculo', null );
}
if ($_REQUEST['stNumPlaca'] != '') {
    $preview->addParametro( 'stPlaca', str_replace('-','',$_REQUEST['stNumPlaca']) );
} else {
    $preview->addParametro( 'stPlaca', null );
}
if ($_REQUEST['stPrefixo'] != '') {
    $preview->addParametro( 'stPrefixo', $_REQUEST['stPrefixo'] );
} else {
    $preview->addParametro( 'stPrefixo', null );
}

if ($_REQUEST['inCodEntidade'] != '') {
    $preview->addParametro( 'inCodEntidade', implode(',',$_REQUEST['inCodEntidade']) );
} else {
    $preview->addParametro( 'inCodEntidade', null );
}

$preview->addParametro( 'ordenacao', $_REQUEST['inCodOrdenacao'] );
$preview->addParametro( 'baixado', $_REQUEST['inCodVeiculoBaixado'] );

$preview->preview();
