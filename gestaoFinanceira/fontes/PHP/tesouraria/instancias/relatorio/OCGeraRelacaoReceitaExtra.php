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
    * Data de Criação   : 31/17/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-02.04.37
*/

/*
$Log$
Revision 1.2  2007/08/23 12:55:43  hboaventura
Bug#9937#, Bug#9938#, Bug#9940#

Revision 1.1  2007/08/08 14:07:33  hboaventura
uc_02-04-37

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(2,30,2);
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt('2.5.0');

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

//seta as o código estrutural das receitas
if ($_REQUEST['inCodPlanoInicial'] != '' AND $_REQUEST['inCodPlanoFinal'] != '') {
    $preview->addParametro( "cod_plano", " BETWEEN ".$_REQUEST['inCodPlanoInicial']." AND ".$_REQUEST['inCodPlanoFinal'] );
} elseif ($_REQUEST['inCodPlanoFinal'] == '' AND $_REQUEST['inCodPlanoInicial'] != '') {
    $preview->addParametro( "cod_plano", " >= '".$_REQUEST['inCodPlanoInicial']."' ");
} elseif ($_REQUEST['inCodPlanoFinal'] != '' AND $_REQUEST['inCodPlanoInicial'] == '') {
    $preview->addParametro( "cod_plano", " <= '".$_REQUEST['inCodPlanoFinal']."' ");
} else {
    $preview->addParametro( "cod_plano", '');
}

//seta o cod_plano
if ($_REQUEST['inContaBancoInicial'] != '' AND $_REQUEST['inContaBancoFinal'] != '') {
    $preview->addParametro( "conta_banco", " BETWEEN ".$_REQUEST['inContaBancoInicial']." AND ".$_REQUEST['inContaBancoFinal'] );
} elseif ($_REQUEST['inContaBancoInicial'] == '' AND $_REQUEST['inContaBancoFinal'] != '') {
    $preview->addParametro( "conta_banco", " <= ".$_REQUEST['inContaBancoFinal'] );
} elseif ($_REQUEST['inContaBancoInicial'] != '' AND $_REQUEST['inContaBancoFinal'] == '') {
    $preview->addParametro( "conta_banco", " >= ".$_REQUEST['inContaBancoInicial'] );
} else {
    $preview->addParametro( "conta_banco", "" );
}

if ($_REQUEST['inCodRecurso'] != '') {
    $preview->addParametro( 'recurso', $_REQUEST['inCodRecurso'] );
} else {
    $preview->addParametro( 'recurso', '' );
}

if( $_REQUEST['inCodUso']<>NULL && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'] )
     $preview->addParametro( 'destinacaorecurso', $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );
else $preview->addParametro( 'destinacaorecurso', '');

if ( $_REQUEST['inCodDetalhamento'] )
     $preview->addParametro( 'cod_detalhamento', $_REQUEST['inCodDetalhamento'] );
else $preview->addParametro( 'cod_detalhamento', '' );

if ($_REQUEST['stTipoRelatorio'] != '') {
    $preview->addParametro( 'tipo_relatorio', $_REQUEST['stTipoRelatorio'] );
} else {
    $preview->addParametro( 'tipo_relatorio', '' );
}

$preview->preview();
?>
