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
    * Página de filtro do relatório
    * Data de Criação   : 30/07/2008

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Eduardo Schitz

    $Id: OCGeraObrasServicos.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(6,42,1);
$preview->setTitulo('Obras e Serviços');
$preview->setVersaoBirt( '2.5.0' );

//seta a entidade
if (count($_REQUEST['inCodigoEntidadesSelecionadas'])>0) {
    foreach ($_REQUEST['inCodigoEntidadesSelecionadas'] as $array) {
        $arEntidades.=  $array.", ";
    }
    $arEntidades = substr( $arEntidades, 0, strlen($arEntidades)-2 );
    $preview->addParametro( "entidade",$arEntidades );
} else {
    $preview->addParametro( "entidade","" );
}

//seta a data
$preview->addParametro( "data_ini",$_REQUEST['stDataInicial']);
$preview->addParametro( "data_fim",$_REQUEST['stDataFinal']);

$preview->addParametro( "exercicio",Sessao::getExercicio());

//seta as o código estrutural das despesas
if ($_REQUEST['stCodEstruturalInicial'] != '' AND $_REQUEST['stCodEstruturalFinal'] != '') {
    $preview->addParametro( "cod_estrutural", " BETWEEN '".$_REQUEST['stCodEstruturalInicial']."' AND '".$_REQUEST['stCodEstruturalFinal'] ."'" );
} elseif ($_REQUEST['stCodEstruturalFinal'] == '' AND $_REQUEST['stCodEstruturalInicial'] != '') {
    $preview->addParametro( "cod_estrutural", " >= '".$_REQUEST['stCodEstruturalInicial']."' ");
} elseif ($_REQUEST['stCodEstruturalFinal'] != '' AND $_REQUEST['stCodEstruturalInicial'] == '') {
    $preview->addParametro( "cod_estrutural", " <= '".$_REQUEST['stCodEstruturalFinal']."' ");
} else {
    $preview->addParametro( "cod_estrutural", '');
}

//seta o cod_empenho
if ($_REQUEST['inCodEmpenhoInicial'] != '' AND $_REQUEST['inCodEmpenhoFinal'] != '') {
    $preview->addParametro( "cod_empenho", " BETWEEN ".$_REQUEST['inCodEmpenhoInicial']." AND ".$_REQUEST['inCodEmpenhoFinal'] );
} elseif ($_REQUEST['inCodEmpenhoInicial'] == '' AND $_REQUEST['inCodEmpenhoFinal'] != '') {
    $preview->addParametro( "cod_empenho", " <= ".$_REQUEST['inCodEmpenhoFinal'] );
} elseif ($_REQUEST['inCodEmpenhoInicial'] != '' AND $_REQUEST['inCodEmpenhoFinal'] == '') {
    $preview->addParametro( "cod_empenho", " >= ".$_REQUEST['inCodEmpenhoInicial'] );
} else {
    $preview->addParametro( "cod_empenho", "" );
}

//seta o cod_obra
if ($_REQUEST['inCodObraInicial'] != '' AND $_REQUEST['inCodObraFinal'] != '') {
    $preview->addParametro( "cod_obra", " BETWEEN ".$_REQUEST['inCodObraInicial']." AND ".$_REQUEST['inCodObraFinal'] );
} elseif ($_REQUEST['inCodObraInicial'] == '' AND $_REQUEST['inCodObraFinal'] != '') {
    $preview->addParametro( "cod_obra", " <= ".$_REQUEST['inCodObraFinal'] );
} elseif ($_REQUEST['inCodObraInicial'] != '' AND $_REQUEST['inCodObraFinal'] == '') {
    $preview->addParametro( "cod_obra", " >= ".$_REQUEST['inCodObraInicial'] );
} else {
    $preview->addParametro( "cod_obra", "" );
}

$preview->preview();
?>
