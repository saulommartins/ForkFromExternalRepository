<?php

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CLA_PERSISTENTE );

    /**
      * Efetua conexão com a tabela  EMPENHO.AUTORIZACAO_CONTRATO
      * @package URBEM
      * @subpackage Mapeamento
    */
    class TEmpenhoAutorizacaoContrato extends Persistente
    {
	    /**
	     * Método Construtor
	     * @access Private
	     */
	    public function TEmpenhoAutorizacaoContrato()
	    {
	        $this->setCampoCod('cod_autorizacao');
	        $this->setComplementoChave('exercicio, cod_entidade');

	        parent::Persistente();
	        $this->setTabela('empenho.autorizacao_contrato');

	        $this->setCampoCod('cod_autorizacao');
	        $this->setComplementoChave('exercicio, cod_entidade');

	        $this->AddCampo('exercicio',	   'char',    true, '4', true,  true );
	        $this->AddCampo('cod_entidade',	   'integer', true, '',  false, true );
	        $this->AddCampo('cod_autorizacao', 'integer', true, '',  true,  false);
	        $this->AddCampo('num_contrato',	   'integer', true, '',  true,  true );
	    }

	    public function recurperaContratosPorAutorizacao(&$rsRecordSet, $codAutorizacao, $codEntidade, $exercicio)
	    {
	    	$stFiltro  = " WHERE autorizacao_contrato.cod_autorizacao = " . $codAutorizacao;
	    	$stFiltro .= "   AND autorizacao_contrato.cod_entidade = " . $codEntidade;
	    	$stFiltro .= "   AND autorizacao_contrato.exercicio = '" . $exercicio . "'";

	    	return $this->executaRecupera( "montaRecurperaContratosPorAutorizacao", $rsRecordSet, $stFiltro );
	    }

	    public function montaRecurperaContratosPorAutorizacao()
	    {
	    	return "
	    		SELECT autorizacao_contrato.cod_autorizacao, 
	    			   contrato.num_contrato, 
	    			   autorizacao_contrato.cod_entidade, 
	    			   autorizacao_contrato.exercicio, 
	    			   contrato.objeto

				  FROM empenho.autorizacao_contrato

				  JOIN licitacao.contrato
				    ON contrato.exercicio = autorizacao_contrato.exercicio
				   AND contrato.cod_entidade = autorizacao_contrato.cod_entidade
				   AND contrato.num_contrato = autorizacao_contrato.num_contrato
	    	";
	    }

	    public function removerRegistrosAntigos($codAutorizacao, $codEntidade, $exercicio)
	    {
    	    $obErro     = new Erro;
		    $obConexao  = new Conexao;
	        
	        return $obConexao->executaDML( 
	        	"DELETE 
	        	   FROM empenho.autorizacao_contrato 
	              WHERE cod_entidade = " . $codEntidade . "
	                AND exercicio = '".$exercicio."'
	                AND cod_autorizacao = ".$codAutorizacao
	        );
	    }
    }
