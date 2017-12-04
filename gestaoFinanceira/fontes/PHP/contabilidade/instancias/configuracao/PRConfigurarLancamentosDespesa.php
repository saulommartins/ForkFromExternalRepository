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
    * Página de Processamento Configurar Lançamentos de Despesa
    * Data de Criaão   : 21/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @ignore

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoCredito.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoDebito.class.php" );

$stPrograma = "ConfigurarLancamentosDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
if ($stAcao) {
    $stAcao = 'configurar';
}

if (empty($_REQUEST['codContaDespesaLista'])) {
    $stErro = "Selecione uma despesa na lista para vincular às contas!";
}

if (!empty($_REQUEST['stLancamentoDebitoAlmoxarifado']) xor !empty($_REQUEST['stLancamentoCreditoAlmoxarifado'])) {
    $stErro = "Necessário selecionar as Contas de Débito e Crédito na aba Almoxarifado!";
}

if ($stErro) {
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stErro,"n_incluir","erro", Sessao::getId(), "../");
    exit;
}

$obErro = new Erro;

################################### CONFIGURA OS LANCAMENTOS
if (!empty($_REQUEST['stLancamentoCreditoLiquidacao']) && !empty($_REQUEST['stLancamentoDebitoLiquidacao'])) {
    //////////////////////// Cadastro da configuração do lançamento de Liquidacao Debito -- Aba Liquidacao
    $obTNovaContabilidadeConfiguracaoLancamentoDebito = new TContabilidadeConfiguracaoLancamentoDebito;
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'cod_conta_despesa', $_REQUEST['codContaDespesaLista'] );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'exercicio', Sessao::getExercicio() );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'escrituracao', 'analitica' );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'tipo', 'liquidacao' );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'estorno', 'false' );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'rpps', 'false' );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'cod_conta', $_REQUEST['stLancamentoDebitoLiquidacao'] );
    $obErro = $obTNovaContabilidadeConfiguracaoLancamentoDebito->salvar();

    // Cadastra o lancamento invertido
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'estorno', 'true' );
    $obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'cod_conta', $_REQUEST['stLancamentoCreditoLiquidacao'] );
    $obErro = $obTNovaContabilidadeConfiguracaoLancamentoDebito->salvar();

    //////////////////////// Cadastro da configuração do lançamento de Liquidacao Crédito -- Aba Liquidacao
    $obTNovaContabilidadeConfiguracaoLancamentoCredito = new TContabilidadeConfiguracaoLancamentoCredito;
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'cod_conta_despesa', $_REQUEST['codContaDespesaLista'] );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'exercicio', Sessao::getExercicio() );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'escrituracao', 'analitica' );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'tipo', 'liquidacao' );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'estorno', 'false' );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'rpps', 'false' );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'cod_conta', $_REQUEST['stLancamentoCreditoLiquidacao'] );
    $obErro = $obTNovaContabilidadeConfiguracaoLancamentoCredito->salvar();

    // Cadastra o lancamento invertido
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'estorno', 'true' );
    $obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'cod_conta', $_REQUEST['stLancamentoDebitoLiquidacao'] );
    $obErro = $obTNovaContabilidadeConfiguracaoLancamentoCredito->salvar();
}

//////////////////////// Cadastro da configuração do lançamento de Almoxarifado Debito -- Aba Almoxarifado
$obTNovaContabilidadeConfiguracaoLancamentoDebito = new TContabilidadeConfiguracaoLancamentoDebito;
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'cod_conta_despesa', $_REQUEST['codContaDespesaLista'] );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'exercicio', Sessao::getExercicio() );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'escrituracao', 'analitica' );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'tipo', 'almoxarifado' );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'estorno', 'false' );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'rpps', 'false' );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'cod_conta', $_REQUEST['stLancamentoDebitoAlmoxarifado'] );
$obErro = $obTNovaContabilidadeConfiguracaoLancamentoDebito->salvar();

// Cadastra o lancamento invertido
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'estorno', 'true' );
$obTNovaContabilidadeConfiguracaoLancamentoDebito->setDado( 'cod_conta', $_REQUEST['stLancamentoCreditoAlmoxarifado'] );
$obErro = $obTNovaContabilidadeConfiguracaoLancamentoDebito->salvar();

//////////////////////// Cadastro da configuração do lançamento de Almoxarifado Crédito -- Aba Almoxarifado
$obTNovaContabilidadeConfiguracaoLancamentoCredito = new TContabilidadeConfiguracaoLancamentoCredito;
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'cod_conta_despesa', $_REQUEST['codContaDespesaLista'] );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'exercicio', Sessao::getExercicio() );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'escrituracao', 'analitica' );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'tipo', 'almoxarifado' );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'estorno', 'false' );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'rpps', 'false' );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'cod_conta', $_REQUEST['stLancamentoCreditoAlmoxarifado'] );
$obErro = $obTNovaContabilidadeConfiguracaoLancamentoCredito->salvar();

// Cadastra o lancamento invertido
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'estorno', 'true' );
$obTNovaContabilidadeConfiguracaoLancamentoCredito->setDado( 'cod_conta', $_REQUEST['stLancamentoDebitoAlmoxarifado'] );
$obErro = $obTNovaContabilidadeConfiguracaoLancamentoCredito->salvar();

SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, 'Configuração realizada com sucesso!',"aviso","aviso", Sessao::getId(), "../");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
