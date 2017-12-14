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
 * Página de Processamento Oculto para popup de Penalidade
 * Data de Criação: 11/08/2008

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: OCPenalidade.php 64421 2016-02-19 12:14:17Z fabio $

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_FIS_MAPEAMENTO . "TFISPenalidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Penalidade";
$pgFilt = "FL" . $stPrograma . ".php";
$pgList = "LS" . $stPrograma . ".php";
$pgForm = "FM" . $stPrograma . ".php";
$pgOcul = "OC" . $stPrograma . ".php";
$pgJS   = "JS" . $stPrograma . ".js";

switch ($_REQUEST["stCtrl"]) {
    case "buscaPenalidade":
        $obPenalidade = new TFISPenalidade();
        $rsPenalidade = new RecordSet();
        $obPenalidade->setDado( 'cod_penalidade', $_REQUEST['inCodPenalidade'] );
        if ($_REQUEST["tipoBusca"]) {
            $obPenalidade->setDado( 'cod_tipo_penalidade', $_REQUEST['tipoBusca'] );
        }

        $obPenalidade->recuperaPorChave( $rsPenalidade );
        if (! $rsPenalidade->eof() ) {
            $js = "window.document.getElementById('stPenalidade').innerHTML = '" . $rsPenalidade->getCampo( 'nom_penalidade' ) . "';";
        } else {
            $js  = "window.document.frm.inCodPenalidade.value = '';";
            $js .= "window.document.getElementById('stPenalidade').innerHTML = '&nbsp;';";
            $js .= "alertaAviso('@Código da Penalidade inválido (" . $_REQUEST['inCodPenalidade'] . ").','form','erro','" . Sessao::getId() . "');";
        }

        echo $js;
        break;
}
