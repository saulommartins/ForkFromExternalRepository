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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoBalanceteDespRecExtra.class.php' );

Sessao::setTrataExcecao ( true );

$stAcao = $request->get('stAcao');
$arContas = Sessao::read('arContas');

if (count($arContas) < 1) {
    SistemaLegado::exibeAviso(urlencode("Não há nenhuma conta inserida"),"n_incluir","erro");
    Sessao::encerraExcecao();
    die;
}

$obTTCEALConfiguracaoBalanceteDespRecExtra = new TTCEALConfiguracaoBalanceteDespRecExtra();
$obTTCEALConfiguracaoBalanceteDespRecExtra->excluirTodos();

foreach ($arContas as $arAux) {
    $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'exercicio', Sessao::getExercicio() );
    $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'cod_plano', $arAux['cod_plano'] );
    $obTTCEALConfiguracaoBalanceteDespRecExtra->setDado( 'classificacao', substr($arAux['classificacao'], 0, 2));
    $obTTCEALConfiguracaoBalanceteDespRecExtra->inclusao();
}

Sessao::encerraExcecao();

SistemaLegado::alertaAviso('FMManterConfiguracaoDespRecExtra.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
