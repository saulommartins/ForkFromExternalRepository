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
    * Página de Processamento Configurar Lançamentos de Receita
    * Data de Criaão   : 21/10/2011

    * @author Analista: Tonismar Bernardo
    * @author Desenvolvedor: Davi Aroldi

    * @ignore

    $Id: PRConfigurarLancamentosReceita.php 66481 2016-09-01 20:15:15Z michel $

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoReceita.class.php" );

$stPrograma = "ConfigurarLancamentosReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

if (empty($_REQUEST['codContaReceitaLista'])) {
    $stErro = "Selecione uma Receita na Lista para vincular às contas!";
}

if ($stErro) {
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stErro,"n_incluir","erro", Sessao::getId(), "../");
    exit;
}

$obErro = new Erro;

$inCodContaCredito = $_REQUEST['stLancamentoCreditoReceita'];
$inCodContaCredito = (empty($inCodContaCredito) && $_REQUEST['boArrecadacao'] == 'TRUE') ? $_REQUEST['inCodContaCredito'] : $inCodContaCredito;

$obTContabilidadeConfiguracaoLancamentoReceita = new TContabilidadeConfiguracaoLancamentoReceita;
$obTContabilidadeConfiguracaoLancamentoReceita->setDado('cod_conta_receita', $_REQUEST['codContaReceitaLista']);
$obTContabilidadeConfiguracaoLancamentoReceita->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeConfiguracaoLancamentoReceita->setDado('cod_conta', $inCodContaCredito);
$obTContabilidadeConfiguracaoLancamentoReceita->setDado('estorno', 'true');
$obErro = $obTContabilidadeConfiguracaoLancamentoReceita->salvar();

//salva o lancamento inverso
$obTContabilidadeConfiguracaoLancamentoReceita->setDado('estorno', 'false');
$obErro = $obTContabilidadeConfiguracaoLancamentoReceita->salvar();

if ($obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,$stErro,"n_incluir","erro", Sessao::getId(), "../");
} else {
    SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao, 'Configuração realizada com sucesso!',"aviso","aviso", Sessao::getId(), "../");
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
