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

    * $Id: OCGeraRelatorioMotorista.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,7,2);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioMotorista');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

if ($_REQUEST['inCodMotorista'] != '') {
    $preview->addParametro( 'inCGMMotorista', $_REQUEST['inCodMotorista'] );
} else {
    $preview->addParametro( 'inCGMMotorista', null );
}

if ($_REQUEST['stDataInicial'] != '') {
    $preview->addParametro( 'stDataInicio', $_REQUEST['stDataInicial'] );
    $preview->addParametro( 'stDataFim', $_REQUEST['stDataFinal'] );
} else {
    $preview->addParametro( 'stDataInicio', null );
    $preview->addParametro( 'stDataFim', null );
}

//$preview->addParametro( 'cgm_usuario',Sessao::read('numCgm') );

$preview->preview();
