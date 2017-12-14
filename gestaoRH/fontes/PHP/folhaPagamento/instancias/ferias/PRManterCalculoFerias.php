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
    * Página de Processamento do Calculo de Férias
    * Data de Criação: 06/07/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.19

    $Id: PRManterCalculoFerias.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoFerias";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php");
$obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
$obRFolhaPagamentoCalcularFolhas->setTipoFiltro($_REQUEST['stTipoFiltro']);
switch ($_REQUEST['stTipoFiltro']) {
    case 'contrato':
    case 'cgm_contrato':
        $obRFolhaPagamentoCalcularFolhas->setCodigos(Sessao::read('arContratos'));
        break;
    case 'lotacao':
        $obRFolhaPagamentoCalcularFolhas->setCodigos($_POST['inCodLotacaoSelecionados']);
        break;
}
$obRFolhaPagamentoCalcularFolhas->setRecalcular(Sessao::read("rsRecalcular"));
$obRFolhaPagamentoCalcularFolhas->setCalcularFerias();
$obRFolhaPagamentoCalcularFolhas->calcularFolha();
?>
