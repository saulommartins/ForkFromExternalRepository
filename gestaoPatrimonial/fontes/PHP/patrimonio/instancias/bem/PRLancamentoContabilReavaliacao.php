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
 * Data de Criação: 17/05/2016

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Arthur Cruz

 * @package URBEM
 * @subpackage

 * $Id: PRLancamentoContabilReavaliacao.php 66372 2016-08-19 19:06:35Z michel $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FRAMEWORK."legado/funcoesLegado.lib.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioReavaliacao.class.php";
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoReavaliacao.class.php";

$stPrograma = "LancamentoContabilReavaliacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro;
$obTPatrimonioReavaliacao = new TPatrimonioReavaliacao();
$obTContabilidadeLancamentoReavaliacao = new TContabilidadeLancamentoReavaliacao();

$stAcao              = $request->get('stAcao');
$inCodEntidade       = $request->get('inCodEntidade');
$inExercicio         = $request->get('inExercicio');
$inMesCompetencia    = str_pad($request->get('inCompetencia'),2,'0',STR_PAD_LEFT);
$inAnoMesCompetencia = $inExercicio.$inMesCompetencia;
$inCodBem            = $request->get('inCodBem');

switch ($stAcao) {

    case 'incluir':

    $stFiltro = "WHERE dt_reavaliacao BETWEEN '".$inExercicio."-".$inMesCompetencia."-01'
                                          AND '".sistemaLegado::dataToSql(sistemaLegado::retornaUltimoDiaMes($inMesCompetencia,$inExercicio))."'
                   AND NOT EXISTS ( SELECT 1 
                                      FROM patrimonio.bem_baixado
                                     WHERE bem_baixado.cod_bem = reavaliacao.cod_bem
                                  ) ";
    $obTPatrimonioReavaliacao->recuperaRelacionamento( $rsBemReavaliacao, $stFiltro );

    $stFiltro = "AND TO_CHAR(reavaliacao.dt_reavaliacao,'YYYY') =  '".$inExercicio."'";
    $obErro = $obTPatrimonioReavaliacao->recuperaMinDataReavaliacao($rsMinDataaReavaliada,$stFiltro);

    $obTContabilidadeLancamentoReavaliacao->setDado('exercicio'   , $inExercicio);
    $obTContabilidadeLancamentoReavaliacao->setDado('competencia' , $inMesCompetencia);
    $obTContabilidadeLancamentoReavaliacao->setDado('cod_entidade', $inCodEntidade);
    $obErro = $obTContabilidadeLancamentoReavaliacao->verificaLancamentosAnteriores($rsLancamentosCompetencia);

    $stFiltro = "
                WHERE lancamento_reavaliacao.timestamp = ( SELECT MAX(lancamento_reavaliacao.timestamp) AS timestamp 
                                                             FROM contabilidade.lancamento_reavaliacao
                                                            WHERE lancamento_reavaliacao.exercicio = '".$inExercicio."'
                                                         )
                  AND lancamento_reavaliacao.exercicio = '".$inExercicio."'
                ";

    $obErro = $obTContabilidadeLancamentoReavaliacao->recuperaMaxCompetenciaLancada($rsUltimoLancamento, $stFiltro);

    if (!$obErro->ocorreu()) {
        $stEstorno        = $rsUltimoLancamento->getCampo('estorno');
        $stCompetencia    = $rsUltimoLancamento->getCampo('max_competencia');
        $stExercicio      = $rsUltimoLancamento->getCampo('exercicio');
        $stExercicioCompetencia = $stExercicio.$stCompetencia;
        $stMinCompetencia = $rsMinDataaReavaliada->getCampo('min_competencia');

        $inMesProcessamento = SistemaLegado::pegaConfiguracao('mes_processamento', 9, Sessao::getExercicio(), $boTransacao);

        if( $inMesCompetencia < $inMesProcessamento ){
            // Verifica a competência de Processamento da Contabilidade
            $obErro->setDescricao("Competência do Lançamento de Reavaliação é anterior ao Mês de Processamento da Contabilidade!");
        }

        if(empty($inCodBem) && !$obErro->ocorreu()){
            if( $stEstorno == "t" && $stCompetencia != "" && ($stExercicioCompetencia) < $inAnoMesCompetencia ){
                // Verifica se a competência selecionada foi estornada
                $obErro->setDescricao("A competência ".$rsUltimoLancamento->getCampo('max_competencia_formatada')." foi estornada. Deve ser lançada novamente!");
            }
            elseif ( $stMinCompetencia != "" && $stCompetencia == "" && $inAnoMesCompetencia > $stMinCompetencia ){
                // Verifica se é a primeira vez que ocorre a ação. Tem que existir uma reavaliação para esta competencia, e nenhum lançamento ainda efetuado
                $obErro->setDescricao("A competência selecionada deve ser igual a ".$rsMinDataaReavaliada->getCampo('min_competencia_formatada'));
            }
            elseif ( $stCompetencia != "" && $stEstorno == "f" && ( ($inAnoMesCompetencia < $stExercicioCompetencia ) || ($inAnoMesCompetencia > ($stExercicioCompetencia + 1)) ) ){
                // Verifica se existe se já foi feito algum lançamento. Caso sim, o proximo lançamento não pode ser menor, nem maior que próximo mês a lançar
                $stProximaCompetencia = ($stCompetencia != "12") ? str_pad(($stCompetencia + 1), 2,'0',STR_PAD_LEFT)."/".$stExercicio : $stCompetencia."/".$stExercicio;
                $obErro->setDescricao("A competência selecionada deve ser igual a ".$stProximaCompetencia);
            }
            elseif ( $rsBemReavaliacao->getNumLinhas() <= 0 ){
                // Verifica se não existe nenhuma reavaliação no sistema, só pode efetuar lançamentos se existir reavaliação.
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." sem reavaliação!");
            }
            elseif ( $rsLancamentosCompetencia->getNumLinhas() > 0 && $rsLancamentosCompetencia->getCampo('estorno') == "f"){
                // Verifica se já foi feito lançamento competencia na selecionada, e que não foi estornado.
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." já lançada! Fazer estornos do mês antes de fazer novos lançamentos!");
            }
        }

        if (!$obErro->ocorreu()) {
            // Faz os Lançamento da competência selecionada.
            $obTContabilidadeLancamentoReavaliacao->setDado("exercicio"      , $inExercicio       );
            $obTContabilidadeLancamentoReavaliacao->setDado("competencia"    , $inMesCompetencia  );
            $obTContabilidadeLancamentoReavaliacao->setDado("dt_inicial"     , $inExercicio."-".$inMesCompetencia."-01" );
            $obTContabilidadeLancamentoReavaliacao->setDado("dt_final"       , sistemaLegado::dataToSql(sistemaLegado::retornaUltimoDiaMes($inMesCompetencia,$inExercicio)) );
            $obTContabilidadeLancamentoReavaliacao->setDado("cod_entidade"   , $inCodEntidade     );
            $obTContabilidadeLancamentoReavaliacao->setDado("cod_historico"  , 970                );
            $obTContabilidadeLancamentoReavaliacao->setDado("tipo"           , "R"                );
            $obTContabilidadeLancamentoReavaliacao->setDado("complemento"    , $inMesCompetencia."/".$inExercicio);
            $obTContabilidadeLancamentoReavaliacao->setDado("estorno"        , "false"            );
            $obTContabilidadeLancamentoReavaliacao->setDado("cod_bem"        , $inCodBem          );
            $obErro = $obTContabilidadeLancamentoReavaliacao->insereLancamentoReavaliacao($rsLancamentoDepreciacao,$boTransacao);
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir", "Lançamento de Reavaliações para a competência ".$inMesCompetencia."/".$inExercicio." efetuado com sucesso!", "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }

    break;

    case 'estornar':

    $stFiltro = "
                WHERE lancamento_reavaliacao.timestamp = ( SELECT MAX(lancamento_reavaliacao.timestamp) AS timestamp 
                                                             FROM contabilidade.lancamento_reavaliacao
                                                            WHERE lancamento_reavaliacao.exercicio = '".$inExercicio."'
                                                         )
                  AND lancamento_reavaliacao.exercicio = '".$inExercicio."'
                ";

    $obErro = $obTContabilidadeLancamentoReavaliacao->recuperaMaxCompetenciaLancada($rsUltimoLancamento, $stFiltro);

    $obTContabilidadeLancamentoReavaliacao->setDado('exercicio'   , $inExercicio);
    $obTContabilidadeLancamentoReavaliacao->setDado('competencia' , $inMesCompetencia);
    $obTContabilidadeLancamentoReavaliacao->setDado('cod_entidade', $inCodEntidade);
    $obErro = $obTContabilidadeLancamentoReavaliacao->verificaLancamentosAnteriores($rsLancamentosCompetencia);

    if (!$obErro->ocorreu()) {
        $stEstorno        = $rsUltimoLancamento->getCampo('estorno');
        $stCompetencia    = $rsUltimoLancamento->getCampo('max_competencia');
        $stExercicio      = $rsUltimoLancamento->getCampo('exercicio');
        $stExercicioCompetencia = $stExercicio.$stCompetencia;

        $inMesProcessamento = SistemaLegado::pegaConfiguracao('mes_processamento', 9, Sessao::getExercicio(), $boTransacao);

        if( $inMesCompetencia < $inMesProcessamento ){
            // Verifica a competência de Processamento da Contabilidade
            $obErro->setDescricao("Competência de Estorno de Lançamento de Reavaliação é anterior ao Mês de Processamento da Contabilidade!");
        }

        if(empty($inCodBem) && !$obErro->ocorreu()){
            if ( $rsUltimoLancamento->getNumLinhas() > 0 && $stEstorno == "t" && ( $inAnoMesCompetencia > ($stExercicioCompetencia) || $inAnoMesCompetencia < ($stExercicioCompetencia -1)) ) {
                // Quando já foi feito ao menos um estorno, avisa o usário o mês a ser estornado. Deve seguir a seguencia do maior para o menor lançamento.
                $stProximaCompetencia = ($stCompetencia != "12") ? str_pad(($stCompetencia - 1), 2,'0',STR_PAD_LEFT)."/".$stExercicio : $stCompetencia."/".$stExercicio;
                $obErro->setDescricao("A competência selecionada deve ser igual a ".$stProximaCompetencia);
            }
            elseif ( $rsUltimoLancamento->getNumLinhas() == 1 && $stEstorno == "f" && ($inAnoMesCompetencia > ($stExercicioCompetencia) || $inAnoMesCompetencia <= ($stExercicioCompetencia -1)) ) {
                // Verifica se é o primeiro estorno das competências.
                $obErro->setDescricao("A competência selecionada deve ser igual a ".str_pad($stCompetencia, 2, '0',STR_PAD_LEFT)."/".$stExercicio);
            }
            elseif ( $rsLancamentosCompetencia->getNumLinhas() <= 0 ) {
                // Verifica se a competência selecionada possui algum lançamento.
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." sem lançamentos! Gere lançamento contábil antes de continuar.");
            }
            elseif ($rsLancamentosCompetencia->getNumLinhas() > 0 && $rsLancamentosCompetencia->getCampo('estorno') == "t") {
                // Verifica se já foi feito estorno do lançamento na competencia na selecionada.
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." já estornada! Fazer lançamento contábil da competência antes de fazer novo estorno!");
            }
        }

        if (!$obErro->ocorreu()) {
            $obTContabilidadeLancamentoReavaliacao->setDado("exercicio"      , $inExercicio       );
            $obTContabilidadeLancamentoReavaliacao->setDado("competencia"    , $inMesCompetencia  );
            $obTContabilidadeLancamentoReavaliacao->setDado("dt_inicial"     , $inExercicio."-".$inMesCompetencia."-01" );
            $obTContabilidadeLancamentoReavaliacao->setDado("dt_final"       , sistemaLegado::dataToSql(sistemaLegado::retornaUltimoDiaMes($inMesCompetencia,$inExercicio)) );
            $obTContabilidadeLancamentoReavaliacao->setDado("cod_entidade"   , $inCodEntidade     );
            $obTContabilidadeLancamentoReavaliacao->setDado("cod_historico"  , 971                );
            $obTContabilidadeLancamentoReavaliacao->setDado("tipo"           , "R"                );
            $obTContabilidadeLancamentoReavaliacao->setDado("complemento"    , $inMesCompetencia."/".$inExercicio);
            $obTContabilidadeLancamentoReavaliacao->setDado("estorno"        , "true"             );
            $obTContabilidadeLancamentoReavaliacao->setDado("cod_bem"        , $inCodBem          );
            $obErro = $obTContabilidadeLancamentoReavaliacao->insereLancamentoReavaliacao($rsLancamentoDepreciacao,$boTransacao);
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso($pgFilt."?stAcao=estornar", "Estorno de lançamentos para a competência ".$inMesCompetencia."/".$inExercicio." efetuado com sucesso!", "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }

    break;
}

?>