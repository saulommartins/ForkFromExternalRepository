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
    * Página de Oculto para Relatório de Ïtens
    * Data de Criação   : 25/01/2006

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-03.03.20

    $Id: OCItensEstoque.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php");
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogoItem.class.php");
include_once(CAM_FW_PDF."RRelatorio.class.php" );

$stCtrl = $_REQUEST['stCtrl'];

$obRAlmoxarifadoPermissaoCentroDeCustos = new RAlmoxarifadoPermissaoCentroDeCustos;
$obRAlmoxarifadoCatalogoItem = new RAlmoxarifadoCatalogoItem;

$obRRelatorio = new RRelatorio;

if ( count($_REQUEST['inCodAlmoxarifadoSelecionado']) > 0 ) {
    foreach ($_REQUEST['inCodAlmoxarifadoSelecionado'] as $key => $valor) {
       $stInFiltro .= $valor.',';
    }
    $stFiltro .= ' and almoxarifado.lancamento_material.cod_almoxarifado in ('.$stInFiltro.')';
}

if ($_REQUEST['inCodCatalogo']) {
    $stFiltro .= ' and almoxarifado.catalogo_classificacao.cod_catalogo = '.$_REQUEST['inCodCatalogo'];
}

Sessao::write('transf5', $stFiltro);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioItens.php" );
