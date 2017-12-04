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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRGrupoCredito.class.php" );

$obErro = new Erro;

//Define o nome dos arquivos PHP
$stPrograma = "ManterCopiarGrupo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
    case "preencheGrupo":
        $stJs = "limpaSelect( f.cmbGrupos, 1 ); \n";
        $stJs .= "f.cmbGrupos[0] = new Option('Selecione', '', 'selected');\n";
        if ($_GET["inExercicio"]) {
            $stFiltro = " WHERE ano_exercicio = '".$_GET["inExercicio"]."'";
            $obTARRGrupoCredito = new TARRGrupoCredito;
            $obTARRGrupoCredito->recuperaTodos( $rsListaTabelas, $stFiltro );
            $inContador = 1;
            if ( !$rsListaTabelas->Eof() ) {
                $stJs .= "f.cmbGrupos.options[$inContador] = new Option('Todos','-666'); \n";
                $inContador++;
            }

            while ( !$rsListaTabelas->eof() ) {
                $stJs .= "f.cmbGrupos.options[$inContador] = new Option('".$rsListaTabelas->getCampo("cod_grupo")." - ".$rsListaTabelas->getCampo("descricao")."','".$rsListaTabelas->getCampo("cod_grupo")."'); \n";
                $rsListaTabelas->proximo();
                $inContador++;
            }
        }

    echo $stJs;
    break;
}
?>
