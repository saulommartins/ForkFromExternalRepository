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
* Página de oculto relatório de Vale-Transporte
* Data de Criação   : 14/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                 );
include_once( CAM_GRH_BEN_NEGOCIO."RRelatorioBeneficioValeTransporte.class.php"  );

$obRRelatorio                           = new RRelatorio;
$obRRelatorioBeneficioValeTransporte    = new RRelatorioBeneficioValeTransporte;
$rsRecordset                            = new Recordset;

$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

if ($arSessaoFiltroRelatorio['inCodFornecedor']) {
    $stFiltro .= " AND fornecedor.cgm_fornecedor = ".$arSessaoFiltroRelatorio['inCodFornecedor'];
}
if ($arSessaoFiltroRelatorio['inPeriodicidade'] and $arSessaoFiltroRelatorio['stDataInicial'] and $arSessaoFiltroRelatorio['stDataFinal']) {
    $stFiltro .= " AND custo.inicio_vigencia between to_date('".$arSessaoFiltroRelatorio['stDataInicial']."','dd/mm/yyyy') AND to_date('".$arSessaoFiltroRelatorio['stDataFinal']."','dd/mm/yyyy')";
} elseif ($arSessaoFiltroRelatorio['inPeriodicidade'] and $arSessaoFiltroRelatorio['stDataInicial'] and !$arSessaoFiltroRelatorio['stDataFinal']) {
    $stFiltro .= " AND custo.inicio_vigencia > to_date('".$arSessaoFiltroRelatorio['stDataInicial']."','dd/mm/yyyy')";
} elseif ($arSessaoFiltroRelatorio['inPeriodicidade'] and !$arSessaoFiltroRelatorio['stDataInicial'] and $arSessaoFiltroRelatorio['stDataFinal']) {
    $stFiltro .= " AND custo.inicio_vigencia < to_date('".$arSessaoFiltroRelatorio['stDataFinal']."','dd/mm/yyyy')";
}
if ($arSessaoFiltroRelatorio['inCodOrdem'] == 1) {
    $stOrdem .= " ORDER BY vigencia";
} else {
    $stOrdem .= " ORDER BY nom_cgm ";
}

$obRRelatorioBeneficioValeTransporte->geraRecordSet( $rsRecordset, $stFiltro, $stOrdem );

Sessao::write('transf5', $rsRecordset);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioValeTransporte.php" );
?>
