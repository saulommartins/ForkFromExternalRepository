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
    * Página de Vínculo de Conta Fundeb
    * Data de Criação   : 01/06/2011

    * @author

    * @ignore

    * Casos de uso :

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GPC_STN_MAPEAMENTO."TSTNVinculoFundeb.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "VincularContaFundeb";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obErro = new Erro();
$arContas = Sessao::read('arContas');

$obTSTNVinculoFundeb = new TSTNVinculoFundeb();
$obTSTNVinculoFundeb->setDado('exercicio', Sessao::getExercicio());
$obErro = $obTSTNVinculoFundeb->exclusao();
if ( !$obErro->ocorreu() ) {
    foreach ($arContas as $conta) {
        $obTSTNVinculoFundeb->setDado('cod_plano', $conta['cod_plano']);
        $obTSTNVinculoFundeb->setDado('cod_entidade', $conta['cod_entidade']);
        $obErro = $obTSTNVinculoFundeb->inclusao();
    }
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?inCodEntidade=".$_REQUEST["inCodigoEntidade"],'Contas Fundeb configuradas com Sucesso!',$stAcao,"aviso", Sessao::getId()."&stAcao=".$stAcao, "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_".$stAcao,"erro");
}
