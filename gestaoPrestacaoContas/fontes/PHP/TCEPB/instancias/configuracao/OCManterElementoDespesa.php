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
    * Página Oculta para configuração
    * Data de Criação   : 25/04/2008

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.03.00

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once(TTPB."TTPBElementoTribunal.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterElementoDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ?  $_REQUEST['stCtrl'] : $_REQUEST['stCtrl'];
$stJs = '';

switch ($stCtrl) {
    case 'montaCombos' :
        $obTTPBElementoTribunal = new TTPBElementoTribunal();
        $arElementos = Sessao::read('elementos');
        foreach ($arElementos AS $arTemp) {
            $obTTPBElementoTribunal->setDado('cod_estrutural', $arTemp['estrutural'] );
            $obTTPBElementoTribunal->setDado('cod_conta', $arTemp['cod_conta'] );
            $obTTPBElementoTribunal->setDado('exercicio', Sessao::getExercicio() );
            $obTTPBElementoTribunal->recuperaElementoDespesaTribunal( $rsElementosTribunal );
            $inCount = 1;
            while ( !$rsElementosTribunal->eof() ) {
                $stSelected = ( $rsElementosTribunal->getCampo('cod_conta') != '' ) ? 'selected' : '' ;
                $stJs .= "document.frm.inElemento_".$arTemp['estrutural']."[".$inCount."] = new Option('".$rsElementosTribunal->getCampo('estrutural')." - " . $rsElementosTribunal->getCampo('descricao') . "','".$rsElementosTribunal->getCampo('estrutural')."','".$stSelected."');\n";
                $inCount++;
                $rsElementosTribunal->proximo();
            }
        }
        break;
}
echo $stJs;
