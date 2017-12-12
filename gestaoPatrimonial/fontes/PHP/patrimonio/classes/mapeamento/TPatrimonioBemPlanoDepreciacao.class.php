<?php

/**
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioBemPlanoDepreciacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TPatrimonioBemPlanoDepreciacao()
    {
        parent::Persistente();
        
        $this->setTabela('patrimonio.bem_plano_depreciacao');
        $this->setCampoCod('cod_bem');
        $this->setComplementoChave('timestamp');
        
        $this->AddCampo('cod_bem'   ,'integer'   ,true  ,''  ,true  ,false);
        $this->AddCampo('timestamp' ,'timestamp' ,false ,''  ,false ,false);
        $this->AddCampo('exercicio' ,'integer'   ,true  ,''  ,true  ,false);
        $this->AddCampo('cod_plano' ,'integer'   ,true  ,''  ,true  ,false);
    }
    
    function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaRelacionamento();
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaRelacionamento(){
	$stSql = "  SELECT bem_plano_depreciacao.cod_bem
                         , bem_plano_depreciacao.cod_plano AS cod_plano_depreciacao
                         , bem_plano_depreciacao.exercicio
                         , MAX(bem_plano_depreciacao.timestamp::timestamp) AS timestamp
                         , plano_conta.nom_conta AS nom_conta_depreciacao
                          
                      FROM patrimonio.bem_plano_depreciacao

                 LEFT JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano       = bem_plano_depreciacao.cod_plano
                       AND plano_analitica.exercicio       = bem_plano_depreciacao.exercicio
		       --AND bem_plano_depreciacao.exercicio = '".Sessao::getExercicio()."'

                 LEFT JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                       
                     WHERE bem_plano_depreciacao.cod_bem   = ".$this->getDado('cod_bem')."
		       --AND bem_plano_depreciacao.exercicio = '".Sessao::getExercicio()."'
                  
                  GROUP BY bem_plano_depreciacao.cod_bem
                         , bem_plano_depreciacao.cod_plano
                         , bem_plano_depreciacao.exercicio
                         , plano_conta.nom_conta
                         
                  ORDER BY timestamp DESC ";
	return $stSql;
    }
    
    function recuperaBemPlanoDepreciacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaBemPlanoDepreciacao();
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaBemPlanoDepreciacao(){
	$stSql = " SELECT bem_plano_depreciacao.cod_bem
                         , bem_plano_depreciacao.cod_plano 
                         , bem_plano_depreciacao.exercicio
                         , MAX(bem_plano_depreciacao.timestamp::timestamp) AS timestamp
                         , plano_conta.cod_estrutural
                         , plano_conta.nom_conta AS nom_conta_depreciacao
                         
                      FROM patrimonio.bem_plano_depreciacao
		      
	        INNER JOIN patrimonio.bem
                        ON bem.cod_bem = bem_plano_depreciacao .cod_bem

                INNER JOIN patrimonio.depreciacao
                        ON depreciacao.cod_bem = bem.cod_bem

                 LEFT JOIN contabilidade.plano_analitica
                        ON plano_analitica.cod_plano = bem_plano_depreciacao.cod_plano
                       AND plano_analitica.exercicio = bem_plano_depreciacao.exercicio

                 LEFT JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                         
                     WHERE bem_plano_depreciacao.timestamp::timestamp = ( SELECT MAX(bem_plano.timestamp::timestamp) AS timestamp 
                                                                            FROM patrimonio.bem_plano_depreciacao AS bem_plano
                                       
                                                                           WHERE bem_plano_depreciacao.cod_bem   = bem_plano.cod_bem
									     AND bem_plano_depreciacao.exercicio = bem_plano.exercicio
                                                                             AND bem_plano_depreciacao.exercicio = '".Sessao::getExercicio()."'   
                                        
                                                                        GROUP BY bem_plano.cod_bem
                                                                               , bem_plano.exercicio )
		       AND NOT EXISTS ( SELECT 1 
					  FROM patrimonio.depreciacao_anulada
				         WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
					   AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
					   AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                      )
			    
                       AND depreciacao.cod_bem                     = ".$this->getDado('cod_bem')."
		       AND substring(depreciacao.competencia, 1,4) = '".Sessao::getExercicio()."'   
                       AND bem_plano_depreciacao.exercicio         = '".Sessao::getExercicio()."'   
                       
                  GROUP BY bem_plano_depreciacao.cod_bem
                         , bem_plano_depreciacao.cod_plano
                         , bem_plano_depreciacao.exercicio
                         , plano_conta.cod_estrutural
                         , plano_conta.nom_conta  ";
	return $stSql;
    }

}