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
 * Arquivo instância para popup de Servidor
 * Data de Criação: 26/10/2012
 *
 *
 * @author Desenvolvedor: Matheus Figueredo
 *
 * $Id: DTRelatorioAuditoriaDetalhes.php 64804 2016-04-04 19:29:47Z michel $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoAuditoria.class.php";

include CAM_FW_LEGADO."funcoesLegado.lib.php";
include CAM_FW_LEGADO."paginacaoLegada.class.php";
include CAM_FW_LEGADO."botoesPdfLegado.class.php";

$rsDetalhes = new RecordSet();

//FILTRO
$arFiltro = array();
if ($request->get("numcgm")) {
    $arFiltro[] = "u.numcgm = ".$request->get("numcgm");
}
if ($request->get("cod_acao")) {
    $arFiltro[] = "a.cod_acao = ".$request->get("cod_acao");
}
if ($request->get("timestamp")) {
    // 17/10/2012 - 10:10:07
    $timestamp = explode("-", $request->get("timestamp"));
    $day = dataToSql($timestamp[0]);
    $time = $timestamp[1];

    $arFiltro[] = "au_d.timestamp::varchar like '".$day."".$time."%'";
}

$stFiltro = implode(" AND ", $arFiltro);

//CONSULTA
$rsRecordSet = null;
$obTAuditoria = new TAuditoria();
$obErro = $obTAuditoria->recuperaAuditoriaDetalhes($rsDetalhes, $stFiltro, ' au_d.cod_detalhe ', false);

if (!$obErro->ocorreu()) {
    $obLista = new Table;
    $obLista->setStyle("margin: 0px; width: 100%;");
    $obLista->setRecordset($rsDetalhes);
    $obLista->setSummary("Detalhes");
    $obLista->Head->addCabecalho('Detalhes', 'E');
    $obLista->Body->addCampo('[valores]');
    $obLista->montaHTML(false, true);

    echo $obLista->getHtml();
}
