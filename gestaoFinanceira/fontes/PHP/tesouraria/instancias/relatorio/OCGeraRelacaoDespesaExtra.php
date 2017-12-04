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
include_once (CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");

$preview = new PreviewBirt(2,30,4);
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt('2.5.0');
$arEntidades = '';

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

if ($_REQUEST['stTipoRelatorio'] != '') {
    $preview->addParametro( 'tipo_relatorio', $_REQUEST['stTipoRelatorio'] );
} else {
    $preview->addParametro( 'tipo_relatorio', '' );
}

if (Sessao::getExercicio() > '2012') {
    $filtroExtras = " AND (
           ( cpcd.cod_estrutural like '1.1.2.%' AND cpc.cod_estrutural like '1.1.2.%' ) OR
           ( cpcd.cod_estrutural like '1.1.3.%' AND cpc.cod_estrutural like '1.1.3.%' ) OR
           ( cpcd.cod_estrutural like '1.2.1.%' AND cpc.cod_estrutural like '1.2.1.%' ) OR
           ( cpcd.cod_estrutural like '2.1.1.%' AND cpc.cod_estrutural like '2.1.1.%' ) OR
           ( cpcd.cod_estrutural like '2.1.2.%' AND cpc.cod_estrutural like '2.1.2.%' ) OR
           ( cpcd.cod_estrutural like '2.1.9.%' AND cpc.cod_estrutural like '2.1.9.%' ) OR
           ( cpcd.cod_estrutural like '2.2.1.%' AND cpc.cod_estrutural like '2.2.1.%' ) OR
           ( cpcd.cod_estrutural like '2.2.2.%' AND cpc.cod_estrutural like '2.2.2.%' )
         ) ";
    $preview->addParametro( 'filtro_extras', strip_tags($filtroExtras) );
} else {
    $preview->addParametro( 'filtro_extras', '' );
}

$preview->preview();
?>
