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
  * Data de Criação: 04/09/2013
  * @author Analista:      Gelson W. Gonçalves 
  * @author Desenvolvedor: Arthur Cruz
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_FRAMEWORK."legado/funcoesLegado.lib.php"      		      );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"                      );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoDepreciacao.class.php" );
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacao.class.php"		      );

$stPrograma = "LancamentoContabilDepreciacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro	= new Erro;
$obTPatrimonioDepreciacao	= new TPatrimonioDepreciacao;
$obTContabilidadeLancamentoDepreciacao  = new TContabilidadeLancamentoDepreciacao;

$stAcao                       = $request->get('stAcao');
$inCodEntidade 		      = $request->get('inCodEntidade');
$inExercicio		      = $request->get('inExercicio');
$inCodContaDepreciacao	      = $request->get('inCodContaDepreciacao');
$stDescricaoContaDepreciacao  = $request->get('stDescricaoContaDepreciacao');
$inMesCompetencia             = str_pad($request->get('inCompetencia'),2,'0',STR_PAD_LEFT);
$inAnoMesCompetencia          = $inExercicio.$inMesCompetencia;

switch ($stAcao) {

    case 'incluir':

        $obTPatrimonioDepreciacao->setDado('competencia', $inExercicio.$inMesCompetencia);
        $obErro = $obTPatrimonioDepreciacao->recuperaDepreciacao($rsPatrimonioDepreciacao);

        $stFiltro = "AND TO_CHAR(TO_DATE(competencia, 'YYYYMM'),'YYYY') = '".$inExercicio."'";
        $obErro = $obTPatrimonioDepreciacao->recuperaMinCompetenciaDepreciada($rsMinCompetenciaDepreciada,$stFiltro);
        
        $stFiltro = " WHERE lancamento_depreciacao.timestamp = ( SELECT MAX(lancamento_depreciacao.timestamp) AS timestamp 
							         FROM contabilidade.lancamento_depreciacao
							       )
                       AND lancamento_depreciacao.exercicio = '".$inExercicio."'";
        $obErro = $obTContabilidadeLancamentoDepreciacao->recuperaMaxCompetenciaLancada($rsUltimoLancamento, $stFiltro);
        
        $stFiltro = " WHERE lancamento_depreciacao.timestamp = ( SELECT MAX(lancamento_depreciacao.timestamp) AS timestamp 
                                                                   FROM contabilidade.lancamento_depreciacao
                                                                  WHERE competencia  = '".$inExercicio.$inMesCompetencia."'
                                                                    AND cod_entidade = ".$inCodEntidade."
                                                                    AND exercicio    = '".$inExercicio."'
						               )
                        AND lancamento_depreciacao.exercicio = '".$inExercicio."'";
        $obErro = $obTContabilidadeLancamentoDepreciacao->verificaDepreciacoesAnteriores($rsLancamentosCompetencia,$stFiltro);
        
        if (!$obErro->ocorreu()) {
            
           // Verifica se a competência selecionada
           if($rsUltimoLancamento->getCampo('estorno') == "t" && $rsUltimoLancamento->getCampo('max_competencia') != "" && $rsUltimoLancamento->getCampo('max_competencia') != $inAnoMesCompetencia){
                $obErro->setDescricao("A competência ".$rsUltimoLancamento->getCampo('max_competencia_formatada')." foi estornada. Deve ser lançada novamente!");
                
           // Verifica se é a primeira vez que ocorre a ação. Tem que existir uma depreciação para esta competencia, e nenhum lançamento ainda efetuado
           }elseif ($rsMinCompetenciaDepreciada->getCampo('min_competencia') != "" && $rsUltimoLancamento->getCampo('max_competencia') == "" && $inAnoMesCompetencia != $rsMinCompetenciaDepreciada->getCampo('min_competencia')){
                $obErro->setDescricao("A competência selecionada deve ser igual a ".$rsMinCompetenciaDepreciada->getCampo('min_competencia_formatada'));
            
            // Verifica se existe se já foi feito algum lançamento. Caso sim, o proximo lançamento não pode ser menor, nem maior que próximo mês a lançar
            } elseif ($rsUltimoLancamento->getCampo('max_competencia') != "" && ($inAnoMesCompetencia < $rsUltimoLancamento->getCampo('max_competencia')  || $inAnoMesCompetencia > ($rsUltimoLancamento->getCampo('max_competencia') + 1))) {
		$stProximaCompetencia = ($rsUltimoLancamento->getCampo('max_competencia') != $inExercicio."12") ? substr(($rsUltimoLancamento->getCampo('max_competencia') + 1), 4, 6)."/".substr($rsUltimoLancamento->getCampo('max_competencia'),0,4) : substr($rsUltimoLancamento->getCampo('max_competencia'), 4, 6)."/".substr($rsUltimoLancamento->getCampo('max_competencia'),0,4);
                $obErro->setDescricao("A competência selecionada deve ser igual a ".$stProximaCompetencia);
                
            // Verifica se não existe nenhuma depreciação no sistema, só pode efetuar lançamentos se existir depreciação.
            } elseif ( $rsPatrimonioDepreciacao->getNumLinhas() <= 0 ){
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." sem depreciação! Faça a depreciação automática antes de continuar.");               
            
            // Verifica se já foi feito lançamento competencia na selecionada, e que não foi estornado.
            } elseif ($rsLancamentosCompetencia->getNumLinhas() > 0 && $rsLancamentosCompetencia->getCampo('estorno') == "f") {
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." já lançada! Fazer estornos do mês antes de fazer novos lançamentos!");
            
            } else {
                $obTContabilidadeLancamentoDepreciacao->setDado("exercicio"	     , $inExercicio       );
                $obTContabilidadeLancamentoDepreciacao->setDado("competencia"    , $inMesCompetencia  );
                $obTContabilidadeLancamentoDepreciacao->setDado("cod_entidade"   , $inCodEntidade     );
                $obTContabilidadeLancamentoDepreciacao->setDado("cod_historico"  , 962                );
                $obTContabilidadeLancamentoDepreciacao->setDado("tipo"	         , "D"                );
                $obTContabilidadeLancamentoDepreciacao->setDado("complemento"    , $inMesCompetencia."/".$inExercicio);
                $obTContabilidadeLancamentoDepreciacao->setDado("estorno"        , "false"            );
                $obErro = $obTContabilidadeLancamentoDepreciacao->insereLancamentoDepreciacao($rsLancamentoDepreciacao,$boTransacao);
            }
            
            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=incluir", "Lançamento de depreciações para a competência ".$inMesCompetencia."/".$inExercicio." efetuado com sucesso!", "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                SistemaLegado::LiberaFrames();
            }
            
        }else{
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            SistemaLegado::LiberaFrames();
        }
        
    break;

    case 'estornar':
    
        $stFiltro = " WHERE competencia  = '".$inExercicio.$inMesCompetencia."'
                        AND cod_entidade = ".$inCodEntidade."
                        AND exercicio    = '".$inExercicio."'";
        $obErro = $obTContabilidadeLancamentoDepreciacao->verificaDepreciacoesAnteriores($rsLancamentoCompetenciaAtual,$stFiltro);
                
        $stFiltro = " WHERE lancamento_depreciacao.timestamp = ( SELECT MAX(lancamento_depreciacao.timestamp) AS timestamp 
                                                                   FROM contabilidade.lancamento_depreciacao
                                                                  WHERE competencia  = '".$inExercicio.$inMesCompetencia."'
                                                                    AND cod_entidade = ".$inCodEntidade."
                                                                    AND exercicio    = '".$inExercicio."'
						               )
                        AND lancamento_depreciacao.exercicio = '".$inExercicio."'";
        $obErro = $obTContabilidadeLancamentoDepreciacao->verificaDepreciacoesAnteriores($rsLancamentosCompetencia,$stFiltro);
        
        $stFiltroMax = " WHERE lancamento_depreciacao.timestamp = ( SELECT MAX(lancamento_depreciacao.timestamp) AS timestamp 
							              FROM contabilidade.lancamento_depreciacao

							        INNER JOIN patrimonio.depreciacao
								        ON lancamento_depreciacao.cod_depreciacao       = depreciacao.cod_depreciacao
								       AND lancamento_depreciacao.cod_bem               = depreciacao.cod_bem               
								       AND lancamento_depreciacao.timestamp_depreciacao = depreciacao.timestamp    
								      
                                                                 LEFT JOIN patrimonio.depreciacao_anulada
									ON depreciacao.cod_bem         = depreciacao_anulada.cod_bem
								       AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
								       AND depreciacao.timestamp       = depreciacao_anulada.timestamp
									 
                                                                     WHERE depreciacao_anulada.cod_depreciacao IS NULL
							          )
                           AND lancamento_depreciacao.exercicio = '".$inExercicio."'";
        $obErro = $obTContabilidadeLancamentoDepreciacao->recuperaMaxCompetenciaLancada($rsUltimoLancamento,$stFiltroMax);
        
        if (!$obErro->ocorreu()) {
           
            // Quando já foi feito ao menos um estorno, avisa o usário o mês a ser estornado. Deve seguir a seguencia do maior para o menor lançamento.
            if ( $rsUltimoLancamento->getNumLinhas() > 0 && $rsUltimoLancamento->getCampo('estorno') == "t" && ( $inAnoMesCompetencia > $rsUltimoLancamento->getCampo('max_competencia') || $inAnoMesCompetencia < ($rsUltimoLancamento->getCampo('max_competencia') -1))) {
				$stProximaCompetencia = ($rsUltimoLancamento->getCampo('max_competencia') != $inExercicio."01") ? substr(($rsUltimoLancamento->getCampo('max_competencia') - 1), 4, 6)."/".substr($rsUltimoLancamento->getCampo('max_competencia'),0,4) : substr($rsUltimoLancamento->getCampo('max_competencia'), 4, 6)."/".substr($rsUltimoLancamento->getCampo('max_competencia'),0,4);
                $obErro->setDescricao("A competência selecionada deve ser igual a ".$stProximaCompetencia);
                
            // Verifica se é o primeiro estorno das competências.
            } elseif ( $rsUltimoLancamento->getNumLinhas() == 1 && $rsUltimoLancamento->getCampo('estorno') == "f" && ($inAnoMesCompetencia > $rsUltimoLancamento->getCampo('max_competencia') || $inAnoMesCompetencia <= ($rsUltimoLancamento->getCampo('max_competencia') -1)) ) {    
                $obErro->setDescricao("A competência selecionada deve ser igual a ".substr($rsUltimoLancamento->getCampo('max_competencia'), 4, 6)."/".substr($rsUltimoLancamento->getCampo('max_competencia'),0,4) );
            
            // Verifica se a competência selecionada possui algum lançamento.
            } elseif ( $rsLancamentoCompetenciaAtual->getNumLinhas() <= 0 ) {
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." sem lançamentos! Gere lançamento contábil antes de continuar.");               
            
            // Verifica se já foi feito estorno do lançamento na competencia na selecionada.
            } elseif ($rsLancamentosCompetencia->getNumLinhas() > 0 && $rsLancamentosCompetencia->getCampo('estorno') == "t") {
                $obErro->setDescricao("Competência ".$inMesCompetencia."/".$inExercicio." já estornada! Fazer lançamento contábil da competência antes de fazer novo estorno!");
            
            // Faz os estorno da competência selecionada.
            } else {
               
                $obTContabilidadeLancamentoDepreciacao->setDado("exercicio"	 , $inExercicio       );
                $obTContabilidadeLancamentoDepreciacao->setDado("competencia"    , $inMesCompetencia  );
                $obTContabilidadeLancamentoDepreciacao->setDado("cod_entidade"   , $inCodEntidade     );
                $obTContabilidadeLancamentoDepreciacao->setDado("cod_historico"  , 963                );
                $obTContabilidadeLancamentoDepreciacao->setDado("tipo"	         , "D"                );
                $obTContabilidadeLancamentoDepreciacao->setDado("complemento"    , $inMesCompetencia."/".$inExercicio);
                $obTContabilidadeLancamentoDepreciacao->setDado("estorno"        , "true"             );
                $obErro = $obTContabilidadeLancamentoDepreciacao->insereLancamentoDepreciacao($rsLancamentoDepreciacao,$boTransacao);
               
            }
            
            if (!$obErro->ocorreu()) {
                SistemaLegado::alertaAviso($pgFilt."?stAcao=estornar", "Estorno de lançamentos para a competência ".$inMesCompetencia."/".$inExercicio." efetuado com sucesso!", "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
				SistemaLegado::LiberaFrames();
            }
            
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
			SistemaLegado::LiberaFrames();
        }
    
    break;
}

?>