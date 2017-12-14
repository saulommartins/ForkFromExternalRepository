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
    * Data de Criação   : 10/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoVinculoEmpregaticio.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterConfiguracaoVinculoEmpregaticio';
$pgFilt    = 'FL'.$stPrograma.'.php';
$pgList    = 'LS'.$stPrograma.'.php';
$pgForm    = 'FM'.$stPrograma.'.php';
$pgProc    = 'PR'.$stPrograma.'.php';
$pgOcul    = 'OC'.$stPrograma.'.php';

Sessao::setTrataExcecao( true );

$obTTCEALConfiguracaoVinculoEmpregaticio = new TTCEALConfiguracaoVinculoEmpregaticio();

$obTTCEALConfiguracaoVinculoEmpregaticio->setDado('exercicio', Sessao::getExercicio());
$obTTCEALConfiguracaoVinculoEmpregaticio->setDado('cod_entidade', Sessao::read('vinculo_empregaticio_cod_entidade'));
$obTTCEALConfiguracaoVinculoEmpregaticio->excluirRegistros();

foreach ($_REQUEST as $stKey => $stValue) {
    if (strpos($stKey,'cmbCargo') !== false AND $stValue != '') {
        $arRetencao = explode('_',$stKey);

        $obTTCEALConfiguracaoVinculoEmpregaticio->setDado('exercicio', Sessao::getExercicio());
        $obTTCEALConfiguracaoVinculoEmpregaticio->setDado('cod_entidade', Sessao::read('vinculo_empregaticio_cod_entidade'));
        $obTTCEALConfiguracaoVinculoEmpregaticio->setDado('cod_tipo_cargo_tce',$stValue);
        $obTTCEALConfiguracaoVinculoEmpregaticio->setDado('cod_sub_divisao',$arRetencao[1]);
        $obErro = $obTTCEALConfiguracaoVinculoEmpregaticio->inclusao();
    }
}

if (!$obErro->ocorreu) {
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
} else {
    sistemaLegado::exibeAviso($obErro->getDescricao() ,"n_incluir","erro");
}

Sessao::encerraExcecao();
