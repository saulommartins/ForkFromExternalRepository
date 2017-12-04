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
 * Página de Formulário para configuração
 * Data de Criação   : 10/10/2007

  * @author Henrique Boaventura

 * @ignore

 * $Id: OCManterElementoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso : uc-06.04.00
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once TTGO."TTGOElementoTribunal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterElementoDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];
$stJs   = "";

$arElementos = Sessao::read('arElementos');

switch ($stCtrl) {

    case 'montaCombos' :
        $obTTGOElementoTribunal = new TTGOElementoTribunal();

        foreach ($arElementos as $arTemp) {
            $obTTGOElementoTribunal->setDado('cod_estrutural', $arTemp['estrutural'] );
            $obTTGOElementoTribunal->setDado('cod_conta', $arTemp['cod_conta'] );
            $obTTGOElementoTribunal->setDado('exercicio', Sessao::getExercicio() );
            $obTTGOElementoTribunal->recuperaElementoDespesaTribunal( $rsElementosTribunal );

            $inCount = 1;

            while (!$rsElementosTribunal->eof()) {
                $stJs .= "document.frm.inElemento_".$arTemp['estrutural']."[".$inCount."] = new Option('".substr($rsElementosTribunal->getCampo('estrutural'),9).' - '.substr($rsElementosTribunal->getCampo('descricao'),0,50)."','".$rsElementosTribunal->getCampo('estrutural')."','');\n";

                if ($rsElementosTribunal->getCampo('cod_conta') != '') {
                    $stJs .= "jQuery('select[name=inElemento_".$arTemp["estrutural"]."]').find('option[value=\'".$rsElementosTribunal->getCampo('estrutural')."\']').attr('selected','selected'); ";
                }

                $inCount++;

                $rsElementosTribunal->proximo();
            }
        }

    break;
}

echo $stJs;
