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
    * Página de Frame Oculto de Despesas Mensais Fixas
    * Data de Criação   : 01/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-09-01 14:35:03 -0300 (Sex, 01 Set 2006) $

    * Casos de uso: uc-02.03.29
*/

/**

$Log$
Revision 1.1  2006/09/01 17:35:03  tonismar
Manter Despesas Fixas Mensais

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma      = "ManterDespesasMensaisFixas";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

switch ($_REQUEST['stCtrl']) {
    case "buscarLocal":
        include_once(TORG."TOrganogramaLocal.class.php");

        $rsLocal = new RecordSet();
        $obTOrganogramaLocal = new TOrganogramaLocal();
        $obTOrganogramaLocal->setDado( "cod_local", $_REQUEST['inCodLocal'] );
        $obTOrganogramaLocal->recuperaPorChave( $rsLocal );

        if ( $rsLocal->getNumLinhas() >= 1 ) {
            $stJs  = "f.inCodLocal.value = ".$rsLocal->getCampo('cod_local')."\n";
            $stJs .= "d.getElementById('stLocal').innerHTML = '".$rsLocal->getCampo('descricao')."' \n";
        } else {
            $stJs  = "f.inCodLocal.value = '' \n";
            $stJs .= "d.getElementById('stLocal').innerHTML = '&nbsp;'  \n";
        }
    break;
}

echo $stJs;
