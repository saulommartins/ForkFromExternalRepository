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
    * Página de Relatório RGF Anexo1
    * Data de Criação   : 25/08/2011

    * @author Davi Ritter Aroldi

    * @ignore

    * Casos de uso : uc-06.01.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

$obErro = new Erro();

$preview = new PreviewBirt(6,57,4);
$preview->setTitulo('Demonstrativo de Restos a Pagar');
$preview->setVersaoBirt( '2.5.0' );
//$preview->setExportaExcel( true );

$stIncluirAssinaturas = $_REQUEST['stIncluirAssinaturas'];
if ($stIncluirAssinaturas == 'nao') {
    $stIncluirAssinaturas = 'não';
} else {
    $stIncluirAssinaturas = 'sim';
}
$preview->addParametro('incluir_assinaturas', $stIncluirAssinaturas);

$preview->addParametro( 'entidades', implode(',',$_REQUEST['inCodEntidade']) );
$preview->addParametro( 'exercicio_resto', Sessao::getExercicio() );
$preview->addParametro( 'data_inicial', $_REQUEST['stDataInicial'] );
$preview->addParametro( 'data_final', $_REQUEST['stDataFinal'] );
$preview->addParametro( 'ordenacao', empty($_REQUEST['inOrdenacao']) ? 1 : $_REQUEST['inOrdenacao'] );

$preview->addAssinaturas(Sessao::read('assinaturas'));

if ( !$obErro->ocorreu() ) {
    $preview->preview();
} else {
    SistemaLegado::alertaAviso($pgFiltro."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao']."", $obErro->getDescricao(),"","aviso", Sessao::getId(), "../");
}
