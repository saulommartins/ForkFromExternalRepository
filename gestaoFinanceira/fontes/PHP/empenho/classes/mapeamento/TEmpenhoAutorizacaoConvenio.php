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

    }
