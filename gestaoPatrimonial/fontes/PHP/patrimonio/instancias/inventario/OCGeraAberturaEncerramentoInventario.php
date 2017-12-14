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
 * Data de Criação: 14/10/2009

 * @author Analista: 	  Gelson W. Gonçalves
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 $Id:$

 * Casos de uso:

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

if ($_REQUEST['stAcao'] == 'encerramento') {
    $preview = new PreviewBirt(3,6,8);
    $preview->setNomeArquivo('encerramentoInventario');
    $stDataHoje     = SistemaLegado::dataExtenso(date('Y-m-d'));
    $stDataCabecalho = $stDataHoje;
} else {
    $preview = new PreviewBirt(3,6,7);
    $preview->setNomeArquivo('aberturaInventario');
    $stDataHoje = SistemaLegado::dataExtenso( SistemaLegado::dataToSql( $_REQUEST['stDataInicial'] ) );
    $stDataCabecalho = $_REQUEST['stDataInicial'];
}

$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório do Birt');

$inCodMunicipio = SistemaLegado::pegaConfiguracao('cod_municipio' , '2', $_REQUEST['stExercicio']);
$inCodUf        = SistemaLegado::pegaConfiguracao('cod_uf'        , '2', $_REQUEST['stExercicio']);
$nomeMunicipio  = SistemaLegado::pegaDado('nom_municipio','sw_municipio','where cod_municipio='.$inCodMunicipio.' and cod_uf='.$inCodUf);

$preview->addParametro("exercicio_inventario" , $_REQUEST['stExercicio']);
$preview->addParametro("id_inventario"        , $_REQUEST['inIdInventario']);
$preview->addParametro("nomMunicipio"         , $nomeMunicipio);
$preview->addParametro("dataHoje"             , $stDataHoje);
$preview->addParametro("dataCabecalho"        , $stDataCabecalho);

$preview->preview();

?>
