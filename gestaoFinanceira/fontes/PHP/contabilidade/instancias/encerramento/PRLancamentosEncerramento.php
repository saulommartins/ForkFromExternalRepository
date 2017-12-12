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
 * Página de Processamento para Gerar Restos A Pagar
 * Data de Criação   : 21/12/2005

 * @author Analista: Muriel
 * @author Desenvolvedor: Cleisson Barboza

 * @ignore

 * Casos de uso: uc-02.02.31

 $Id: PRLancamentosEncerramento.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_NEGOCIO."RConfiguracaoConfiguracao.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."FContabilidadeEncerramento.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "LancamentosEncerramento";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$rsContas = $rsSaldo = new recordSet();
$obErro = new Erro;

$obRConfiguracaoConfiguracao = new RConfiguracaoConfiguracao;
$obFContabilidadeEncerramento = new FContabilidadeEncerramento;
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;

$stAcao = $request->get("stAcao");

switch (true) {
    case $stAcao == "incluir" && Sessao::getExercicio() < 2013:
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            foreach ($_POST["inCodEntidade"] as $inCodEntidade) {
                $obFContabilidadeEncerramento->setDado("stExercicio",Sessao::getExercicio());
                $obFContabilidadeEncerramento->setDado("inCodEntidade",$inCodEntidade);
                /*
                Verifica antes de iniciar quais os lançamentos que ja foram feitos para a entidade
                */
                $obErro = $obFContabilidadeEncerramento->fezEncerramentoReceita( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouReceita = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoDespesa( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouDespesa = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoVariacoesPatri( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouVariacoes = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoOrcamentario( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouOrcamentario = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoResultadoApurado( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouResultadoApurado = $rsRecordSet->getCampo("fez");

                /*
                    Lança Receita se ainda não foi lançada e se foi selecionada a opção
                */
                if ((!$obErro->ocorreu()) && ($_POST["boTodos"] or $_POST["boReceita"]) && ($lancouReceita=='f')) {
                      $obErro = $obFContabilidadeEncerramento->gerarEncerramentoReceita( $rsRecordset, $boTransacao );
                      $lancouReceita = 't';
                }

                /*
                    Lança Despesa se ainda não foi lançada e se foi selecionada a opção, somente se a receita ja foi lançada
                */
                if ((!$obErro->ocorreu()) && ($_POST["boTodos"] or $_POST["boDespesa"]) && ($lancouDespesa=='f')) {
                   if ($lancouReceita=='t') {
                       $obErro = $obFContabilidadeEncerramento->gerarEncerramentoDespesa( $rsRecordSet, $boTransacao );
                       $lancouDespesa = 't';
                   } else {
                       $obErro->setDescricao("É necessario realizar os lançamentos de Receita para a Entidade $inCodEntidade.");
                   }
                }

                /*
                    Lança Variações Patrimoniais se ainda não foi lançada e se foi selecionada a opção, somente se a despesa ja foi lançada
                */
                if ((!$obErro->ocorreu()) && ($_POST["boTodos"] or $_POST["boVariacoes"]) && ($lancouVariacoes=='f')) {
                   if ($lancouDespesa=='t') {
                       $obErro = $obFContabilidadeEncerramento->gerarEncerramentoVariacoes( $rsRecordset, $boTransacao );
                       $lancouVariacoes='t';
                   } else {
                       $obErro->setDescricao("É necessario realizar os lançamentos de Despesa para a Entidade $inCodEntidade.");
                   }
                }

                /*
                    Lança Orçamentaria se ainda não foi lançada e se foi selecionada a opção, somente se as variações patrimoniais ja foram lançadas
                */
                if ((!$obErro->ocorreu()) && ($_POST["boTodos"] or $_POST["boOrcamentario"]) && ($lancouOrcamentario=='f')) {
                   if ($lancouVariacoes=='t') {
                       $obErro = $obFContabilidadeEncerramento->gerarEncerramentoOrcamentario( $rsRecordSet, $boTransacao );
                       $lancouOrcamentario='t';
                   } else {
                       $obErro->setDescricao("É necessario realizar os lançamentos de Variações Patrimoniais para a Entidade $inCodEntidade.");
                   }
                }

                /*
                    Lança Resultados apurados se ainda não foi lançado e se foi selecionada a opção, somente se as orçamentarias ja foram lançadas
                */
                if ((!$obErro->ocorreu()) && ($_POST["boTodos"] or $_POST["boResultadoApurado"]) && ($lancouResultadoApurado=='f')) {
                   if ($lancouOrcamentario=='t') {
                       $obErro = $obFContabilidadeEncerramento->gerarEncerramentoResultadoApurado( $rsRecordSet, $boTransacao );
                   } else {
                       $obErro->setDescricao("É necessario realizar os lançamentos Orçamentários para a Entidade $inCodEntidade.");
                   }
                }

            }
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRConfiguracaoConfiguracao->obTConfiguracao );
        echo "<script type='text/javascript'>LiberaFrames(true,false);</script>";
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId()."&stAcao=".$stAcao,Sessao::getExercicio() , "incluir", "aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;

    /*
     * Estes lançamento começam e funcionar a partir de 2013
     */
    case $stAcao == "incluir" && Sessao::getExercicio() > 2012:
        $obTContabilidadeEncerramentoMes->setDado( "situacao", "F" );
        $obTContabilidadeEncerramentoMes->setDado( "mes", 12 );
        $obTContabilidadeEncerramentoMes->setDado( "exercicio", Sessao::getExercicio() );
        $obTContabilidadeEncerramentoMes->recuperaEncerramentoMes( $rsMesEncerrado );

        //caso o mês contábil de dezembro esteja fechado, não gera lançamentos
        if ($rsMesEncerrado->getNumLinhas() > 0) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode("Não é possível efetuar os lançamentos pois o mês de dezembro está fechado!"),"n_incluir","aviso");
            exit;
        }

        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {

            foreach ($_POST["inCodEntidade"] as $inCodEntidade) {
                $obFContabilidadeEncerramento->setDado("stExercicio",Sessao::getExercicio());
                $obFContabilidadeEncerramento->setDado("inCodEntidade",$inCodEntidade);
                /*
                Verifica antes de iniciar quais os lançamentos que ja foram feitos para a entidade
                */
                $obErro = $obFContabilidadeEncerramento->fezEncerramentoVariacoesPatrimoniais2013( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouVariacoes = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoOrcamentario2013( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouOrcamentario = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoControle2013( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouControle = $rsRecordSet->getCampo("fez");

                /*
                    Lança Variações Patrimoniais se ainda não foi lançada e se foi selecionada a opção
                */
                if ((!$obErro->ocorreu()) && (isset($_POST["boTodos"]) || isset($_POST["boVariacoes"]))) {
                    if ($lancouVariacoes=='f') {
                        $obErro = $obFContabilidadeEncerramento->gerarEncerramentoVariacoes2013( $rsRecordset, $boTransacao );
                    } else {
                        $obErro->setDescricao('Os lançamentos de Variações Patrimoniais já foram realizados!');
                    }
                }

                /*
                    Lança Orçamentaria se ainda não foi lançada e se foi selecionada a opção
                */

                if ((!$obErro->ocorreu()) && (isset($_POST["boTodos"]) || isset($_POST["boOrcamentario"]))) {
                    if ($lancouOrcamentario=='f') {
                        $obErro = $obFContabilidadeEncerramento->gerarEncerramentoOrcamentario2013( $rsRecordSet, $boTransacao );
                    } else {
                        $obErro->setDescricao('Os lançamentos Orçamentários já foram realizados!');
                    }
                }

                /*
                    Lança Controle se ainda não foi lançada e se foi selecionada a opção
                */
                if ((!$obErro->ocorreu()) && (isset($_POST["boTodos"]) || isset($_POST["boControle"]))) {
                    if ($lancouControle=='f') {
                        $obErro = $obFContabilidadeEncerramento->gerarEncerramentoControle2013( $rsRecordSet, $boTransacao );
                    } else {
                        $obErro->setDescricao('Os lançamentos de Controle já foram realizados!');
                    }
                }
            }
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRConfiguracaoConfiguracao->obTConfiguracao );
        SistemaLegado::LiberaFrames(true,false);
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId()."&stAcao=".$stAcao,Sessao::getExercicio() , "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;

    case $stAcao == "excluir" && Sessao::getExercicio() < 2013:
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            foreach ($_POST["inCodEntidade"] as $inCodEntidade) {
                $obFContabilidadeEncerramento->setDado("stExercicio",Sessao::getExercicio());
                $obFContabilidadeEncerramento->setDado("inCodEntidade",$inCodEntidade);
                /*
                Verifica antes de iniciar quais os lançamentos que ja foram feitos para a entidade
                */
                $obErro = $obFContabilidadeEncerramento->fezEncerramentoReceita( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouReceita = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoDespesa( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouDespesa = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoVariacoesPatri( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouVariacoes = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoOrcamentario( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouOrcamentario = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoResultadoApurado( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouResultadoApurado = $rsRecordSet->getCampo("fez");

                /*
                    Exclui todos os lançamentos que ainda não foram excluidos
                */

                if ((!$obErro->ocorreu()) && ($lancouReceita=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoReceita( $rsRecordset, $boTransacao );
                }
                if ((!$obErro->ocorreu()) && ($lancouDespesa=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoDespesa( $rsRecordset, $boTransacao );
                }
                if ((!$obErro->ocorreu()) && ($lancouVariacoes=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoVariacoesPatri( $rsRecordset, $boTransacao );
                }
                if ((!$obErro->ocorreu()) && ($lancouOrcamentario=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoOrcamentario( $rsRecordset, $boTransacao );
                }
                if ((!$obErro->ocorreu()) && ($lancouResultadoApurado=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoResultadoApurado( $rsRecordset, $boTransacao );
                }
            }
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRConfiguracaoConfiguracao->obTConfiguracao );
        echo "<script type='text/javascript'>LiberaFrames(true,false);</script>";
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId()."&stAcao=".$stAcao, Sessao::getExercicio() , "excluir", "aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");

    break;
    case $stAcao == "excluir" && Sessao::getExercicio() > 2012:
        $obTransacao = new Transacao;
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            foreach ($_POST["inCodEntidade"] as $inCodEntidade) {
                $obFContabilidadeEncerramento->setDado("stExercicio",Sessao::getExercicio());
                $obFContabilidadeEncerramento->setDado("inCodEntidade",$inCodEntidade);
                /*
                Verifica antes de iniciar quais os lançamentos que ja foram feitos para a entidade
                */
                $obErro = $obFContabilidadeEncerramento->fezEncerramentoVariacoesPatrimoniais2013( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouVariacoes = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoOrcamentario2013( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouOrcamentario = $rsRecordSet->getCampo("fez");

                $obErro = $obFContabilidadeEncerramento->fezEncerramentoControle2013( $rsRecordSet, $boTransacao );
                if(!$obErro->ocorreu()) $lancouControle = $rsRecordSet->getCampo("fez");

                /*
                    Exclui todos os lançamentos que ainda não foram excluidos
                */

                if ((!$obErro->ocorreu()) && ($lancouVariacoes=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoVariacoesPatrimoniais2013( $rsRecordset, $boTransacao );
                }
                if ((!$obErro->ocorreu()) && ($lancouOrcamentario=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoVariacoesOrcamentario2013( $rsRecordset, $boTransacao );
                }
                if ((!$obErro->ocorreu()) && ($lancouControle=='t')) {
                      $obErro = $obFContabilidadeEncerramento->excluiEncerramentoControle2013( $rsRecordset, $boTransacao );
                }
            }
        }
        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRConfiguracaoConfiguracao->obTConfiguracao );
        SistemaLegado::LiberaFrames(true,false);
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId()."&stAcao=".$stAcao, Sessao::getExercicio() , "excluir", "aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");

    break;

}

?>
