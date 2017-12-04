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
    * PÃ¡gina de Processamento
    * Data de CriaÃ§Ã£o   : 25/01/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura
    * @ignore

    $Id: PRManterElementoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TTGO."TTGOElementoTribunal.class.php");
include_once(TTGO."TTGOElementoDePara.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterElementoDespesa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTGOElementoTribunal = new TTGOElementoTribunal();
$obTTGOElementoDePara = new TTGOElementoDePara();

Sessao::setTrataExcecao ( true );
Sessao::getTransacao()->setMapeamento( $obTTGOElementoTribunal );
Sessao::getTransacao()->setMapeamento( $obTTGOElementoDePara );

$stAcao = $request->get('stAcao');
$arElementos = Sessao::read('arElementos');

switch ($_REQUEST['stAcao']) {

    case 'incluir' :
        $obTTGOElementoDePara->setDado('exercicio',Sessao::getExercicio());
        $obTTGOElementoDePara->exclusao();
        foreach ($arElementos as $arTemp) {
            if ($_REQUEST['inElemento_'.$arTemp['estrutural']] != '') {
                $obTTGOElementoDePara->setDado('cod_conta' ,$arTemp['cod_conta']);
                $obTTGOElementoDePara->setDado('exercicio' ,Sessao::getExercicio());
                $obTTGOElementoDePara->setDado('estrutural',$_REQUEST['inElemento_'.$arTemp['estrutural']] );
                $obTTGOElementoDePara->inclusao();
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao",".", "incluir","aviso", Sessao::getId(), "../");
        /*se{
            sistemaLegado::exibeAviso(urlencode('É necessário cadastrar pelo um gestor!'),"n_incluir","erro");
        }*/
        break;
}

Sessao::encerraExcecao();
?>
