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

    }
