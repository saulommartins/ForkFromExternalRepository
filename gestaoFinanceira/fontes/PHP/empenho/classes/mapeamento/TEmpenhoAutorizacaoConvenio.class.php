<?php

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
    include_once ( CLA_PERSISTENTE );

    /**
      * Efetua conexão com a tabela  EMPENHO.AUTORIZACAO_CONVENIO
      * @package URBEM
      * @subpackage Mapeamento
    */
    class TEmpenhoAutorizacaoConvenio extends Persistente
    {
	    /**
	     * Método Construtor
	     * @access Private
	     */
	    public function TEmpenhoAutorizacaoConvenio()
	    {
	        parent::Persistente();
	        $this->setTabela('empenho.autorizacao_convenio');

	        $this->setCampoCod('cod_autorizacao');
	        $this->setComplementoChave('exercicio, cod_entidade');

	        $this->AddCampo('exercicio',	   'char',    true, '4', true,  true );
	        $this->AddCampo('cod_entidade',	   'integer', true, '',  false, true );
	        $this->AddCampo('cod_autorizacao', 'integer', true, '',  true,  false);
	        $this->AddCampo('nro_convenio',	   'integer', true, '',  true,  true );
	    }

	    public function recurperaConveniosPorAutorizacao(&$rsRecordSet, $codAutorizacao, $codEntidade, $exercicio)
	    {
	    	$stFiltro  = " WHERE cod_autorizacao = " . $codAutorizacao;
	    	$stFiltro .= "   AND cod_entidade = " . $codEntidade;
	    	$stFiltro .= "   AND exercicio = '" . $exercicio . "'";

	    	return $this->executaRecupera( "montaRecurperaConveniosPorAutorizacao", $rsRecordSet, $stFiltro );
	    }

	    public function montaRecurperaConveniosPorAutorizacao()
	    {
	    	return "
	    		SELECT cod_autorizacao, nro_convenio, cod_entidade, exercicio
	    		  FROM empenho.autorizacao_convenio
	    	";
	    }

	    public function removerRegistrosAntigos($codAutorizacao, $codEntidade, $exercicio)
	    {
    	    $obErro     = new Erro;
		    $obConexao  = new Conexao;
	        
	        return $obConexao->executaDML( 
	        	"DELETE 
	        	   FROM empenho.autorizacao_convenio 
	              WHERE cod_entidade = " . $codEntidade . "
	                AND exercicio = '".$exercicio."'
	                AND cod_autorizacao = ".$codAutorizacao
	        );
	    }
    }
