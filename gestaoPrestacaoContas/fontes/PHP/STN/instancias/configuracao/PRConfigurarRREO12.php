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
 * Arquivo de processamento de STN - Configuracao
 *
 * @category    Urbem
 * @package     STN
 * @author      Carlos Adriano   <carlos.silva@cnm.org.br>
 * $Id: PRConfigurarRREO12.php 66695 2016-11-28 20:46:59Z carlos.silva $
 */

//inclui os arquivos necessarios
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GPC_STN_MAPEAMENTO .'TSTNVinculoSaudeRREO12.class.php';

$obTSTNVinculoSaudeRREO12 = new TSTNVinculoSaudeRREO12;

$obErro = new Erro();
foreach ((array) Sessao::read('receitas_del') as $receita) {
    $obTSTNVinculoSaudeRREO12->setDado('cod_receita', $receita['cod_receita']);
    $obTSTNVinculoSaudeRREO12->setDado('exercicio', $receita['exercicio']);
    $obErro = $obTSTNVinculoSaudeRREO12->exclusao($boTransacao);
}

if (!$obErro->ocorreu()) {
    foreach ((array) Sessao::read('receitas') as $receita) {
        if ($receita['new']) {
            $obTSTNVinculoSaudeRREO12->setDado('cod_receita', $receita['cod_receita']);
            $obTSTNVinculoSaudeRREO12->setDado('exercicio', $receita['exercicio']);
            $obErro = $obTSTNVinculoSaudeRREO12->inclusao($boTransacao);
        }
    }
}

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso('FMConfigurarRREO12.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Configuração concluída com sucesso!',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
}
