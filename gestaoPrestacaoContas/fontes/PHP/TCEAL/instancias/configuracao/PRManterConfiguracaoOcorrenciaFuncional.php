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
    * Data de Criação   : 17/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GRH_PES_MAPEAMENTO.'TPessoalAssentamentoAssentamento.class.php');
include_once (CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoOcorrenciaFuncional.class.php');
include_once (CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoOcorrenciaFuncionalAssentamento.class.php');

$stPrograma = "ManterConfiguracaoOcorrenciaFuncional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao ( true );
$obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento = new TTCEALConfiguracaoOcorrenciaFuncionalAssentamento();
Sessao::getTransacao()->setMapeamento( $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento );

$obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento = new TTCEALConfiguracaoOcorrenciaFuncionalAssentamento();
$obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('exercicio', Sessao::getExercicio());
$obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('cod_entidade', Sessao::read('cod_entidade'));
$obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->exclusao();

$arOcorrenciasSessao = Sessao::read('arOcorrencias');

if ( is_array($arOcorrenciasSessao) ){
    foreach ($arOcorrenciasSessao as $arOcorrenciasSessaoTmp) {
        foreach ($arOcorrenciasSessaoTmp["assentamentos"] as $arAssentamentoSelecionado) {
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('exercicio',Sessao::getExercicio());
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('cod_entidade', Sessao::read('cod_entidade'));
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('cod_ocorrencia', $arOcorrenciasSessaoTmp["cod_ocorrencia"]);
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->setDado('cod_assentamento', $arAssentamentoSelecionado["cod_assentamento"]);
            $obTTCEALConfiguracaoOcorrenciaFuncionalAssentamento->inclusao();
        }
    }
}

SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"Configuração ","incluir","incluir_n", Sessao::getId(), "../");
Sessao::encerraExcecao();

?>