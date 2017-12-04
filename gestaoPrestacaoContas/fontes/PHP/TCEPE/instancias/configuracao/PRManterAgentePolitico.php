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
/*
 * Processamento de Vinculo de Agente Político
 * Data de Criação: 01/10/2014

 * @author Desenvolvedor Diogo Zarpelon <diogo.zarpelon@cnm.org.br>

 * @package URBEM
 * @subpackage

 * @ignore

 $Id: $
 
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPECGMAgentePolitico.class.php';

$stPrograma = "ManterAgentePolitico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTTCEPECGMAgentePolitico = new TTCEPECGMAgentePolitico();

# Inicia o controle de transação
Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTTCEPECGMAgentePolitico );

# Exclui todos os registros da tabela e insere novamente.
$obTTCEPECGMAgentePolitico->excluirTodos();

$arAgentePolitico = Sessao::read('arAgentes');

# Percorre o Array de dados de Agente Politico e insere.
foreach ($arAgentePolitico as $arAgentesSessaoTmp) {
    $obTTCEPECGMAgentePolitico->setDado('exercicio' , Sessao::getExercicio() );
    $obTTCEPECGMAgentePolitico->setDado('cod_entidade' , $arAgentesSessaoTmp['cod_entidade'] );
    $obTTCEPECGMAgentePolitico->setDado('numcgm' , $arAgentesSessaoTmp['num_cgm'] );
    $obTTCEPECGMAgentePolitico->setDado('cod_agente_politico' , $arAgentesSessaoTmp['cod_agente_politico'] );
    $obTTCEPECGMAgentePolitico->inclusao();
}

# Encerra o controle de transação
Sessao::encerraExcecao();

SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");

?>