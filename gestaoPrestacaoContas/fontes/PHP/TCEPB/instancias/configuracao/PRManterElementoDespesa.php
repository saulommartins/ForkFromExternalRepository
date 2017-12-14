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
    * Página de Processamento para configuração
    * Data de Criação   : 25/04/2008

    * @author Tonismar Régis Bernardo

    * @ignore

    * Casos de uso : uc-06.03.00

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTPB."TTPBElementoTribunal.class.php");
include_once(TTPB."TTPBElementoDePara.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterElementoDespesa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTPBElementoTribunal = new TTPBElementoTribunal();
$obTTPBElementoDePara = new TTPBElementoDePara();

Sessao::setTrataExcecao ( true );
Sessao::getTransacao()->setMapeamento( $obTTPBElementoTribunal );
Sessao::getTransacao()->setMapeamento( $obTTPBElementoDePara );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($_REQUEST['stAcao']) {

    case 'incluir' :
        $obTTPBElementoDePara->setDado('exercicio',Sessao::getExercicio());
        $obTTPBElementoDePara->exclusao();
        $arElementos = Sessao::read('elementos');
        foreach ($arElementos as $arTemp) {
            if ($_REQUEST['inElemento_'.$arTemp['estrutural']] != '') {
                $obTTPBElementoDePara->setDado('cod_conta' ,$arTemp['cod_conta']);
                $obTTPBElementoDePara->setDado('exercicio' ,Sessao::getExercicio());
                $obTTPBElementoDePara->setDado('estrutural',$_REQUEST['inElemento_'.$arTemp['estrutural']] );
                $obTTPBElementoDePara->inclusao();
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao",".", "incluir","aviso", Sessao::getId(), "../");
        break;
}

Sessao::encerraExcecao();
?>
