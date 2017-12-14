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

    * Pacote de configuração do TCMBA - Limites Para Alteração de Créditos Adicionais
    * Data de Criação   : 11/09/2015
    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos
    * 
    * $id:$
    
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO ."TTCMBALimiteAlteracaoCredito.class.php";
//include_once CAM_GPC_TCMBA_MAPEAMENTO ."TTCMBATipoAlteracaoOrcamentaria.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLimiteAlteracaoCreditoAdicional";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obErro = new Erro();
$obTransacao = new Transacao();
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$arDados = Sessao::read('arDados');

$obTTCMBALimiteAlteracaoCredito = new TTCMBALimiteAlteracaoCredito();
$obTTCMBALimiteAlteracaoCredito->setDado('exercicio', Sessao::getExercicio() );
$obErro = $obTTCMBALimiteAlteracaoCredito->exclusao( $boTransacao );

if (!$obErro->ocorreu() ) {
    foreach ($arDados as $key => $value) {
        $obTTCMBALimiteAlteracaoCredito->setDado('exercicio'         , $value['exercicio']          );
        $obTTCMBALimiteAlteracaoCredito->setDado('cod_entidade'      , $value['cod_entidade']       );
        $obTTCMBALimiteAlteracaoCredito->setDado('cod_norma'         , $value['cod_norma']          );
        $obTTCMBALimiteAlteracaoCredito->setDado('cod_tipo_alteracao', $value['cod_tipo_alteracao'] );
        $obTTCMBALimiteAlteracaoCredito->setDado('valor_alteracao'   , $value['valor_alteracao']    );
        $obErro = $obTTCMBALimiteAlteracaoCredito->inclusao( $boTransacao );
    }
}

if ( !$obErro->ocorreu() ) {
    $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTTCMBALimiteAlteracaoCredito);
    SistemaLegado::alertaAviso($pgFilt,"Dados incluidos!", "incluir", "aviso", Sessao::getId(), "../");
} else {
    $obTransacao->encerraTransacao();
    SistemaLegado::exibeAviso("Verifique os dados antes de incluir","n_incluir","erro");
    SistemaLegado::LiberaFrames(true,true);
}


?>