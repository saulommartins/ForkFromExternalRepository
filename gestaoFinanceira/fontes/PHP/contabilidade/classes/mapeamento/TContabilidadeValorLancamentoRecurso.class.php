<?php

	include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
	include_once ( CLA_PERSISTENTE );

	class TContabilidadeValorLancamentoRecurso extends Persistente {

	   /**
	    * Método Construtor
	    * @access private
		*/
		public function TContabilidadeValorLancamentoRecurso() {
		    parent::Persistente();
		    $this->setTabela('contabilidade.valor_lancamento_recurso');

		    $this->setCampoCod('sequencia');
		    $this->setComplementoChave('cod_lote,tipo,exercicio,cod_entidade,sequencia,tipo_valor');

		    $this->AddCampo('cod_lote',		'integer', true, '',      true,   true);
		    $this->AddCampo('tipo',			'char',	   true, '1',     true,   true);
		    $this->AddCampo('sequencia', 	'integer', true, '',      true,  false);
		    $this->AddCampo('exercicio', 	'char',	   true, '04',    true,   true);
		    $this->AddCampo('tipo_valor', 	'char',    true, '1',     true,   true);
		    $this->AddCampo('cod_entidade', 'integer', true, '',   	  true,   true);
		    $this->AddCampo('cod_recurso',  'integer', true, '',   	  false,  true);
		    $this->AddCampo('vl_recurso',   'numeric', true, '14,02', false, false);
		}

		public function recuperaProximaSequencia() {
		    $obErro      = new Erro;
		    $obConexao   = new Conexao;
		    $rsRecordSet = new RecordSet;

		    $stSql  = " SELECT (max(sequencia) + 1) as prox_seq 	  \n";
		    $stSql .= "   FROM contabilidade.valor_lancamento_recurso \n";

		    // $stSql = $this->montaRecuperaProximaSequencia();
		    $this->setDebug( $stSql );

		    return $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
		}

		public function gerarValoresPorRecurso($dadosRetencao, $boTransacao)
		{
		    $obConexao   = new Conexao;
		    $rsRecordSet = new RecordSet;

			$stFiltro  = " lancamento_retencao.exercicio = '".$dadosRetencao['exercicio']."'";
			$stFiltro .= " AND lancamento_retencao.cod_entidade = ".$dadosRetencao['cod_entidade'];
			$stFiltro .= " AND lancamento_retencao.sequencial = ".$dadosRetencao['sequencial'];
			$stFiltro .= " AND lancamento_retencao.cod_ordem = ".$dadosRetencao['cod_ordem'];

			$stSql = $this->montaGerarValoresPorRecurso($stFiltro);

		    $obConexao->executaSQL( $rsRecordSet, "commit;", false );
		    $obConexao->executaSQL( $rsRecordSet, $stSql, false );

		    foreach ($rsRecordSet->getElementos() as $key => $lancamentoRetencao) {
			    
		    	if ($lancamentoRetencao['cod_recurso'] == null || $lancamentoRetencao['cod_recurso'] == '') {
		    		continue;
		    	}

			    $this->setDado('cod_lote',	   $lancamentoRetencao['cod_lote']);
			    $this->setDado('tipo',		   $lancamentoRetencao['tipo']);
			    $this->setDado('sequencia',    $lancamentoRetencao['sequencia']);
			    $this->setDado('exercicio',    $lancamentoRetencao['exercicio']);
			    $this->setDado('tipo_valor',   $lancamentoRetencao['tipo_valor']);
			    $this->setDado('cod_entidade', $lancamentoRetencao['cod_entidade']);
			    $this->setDado('cod_recurso',  $lancamentoRetencao['cod_recurso']);
			    $this->setDado('vl_recurso',   $lancamentoRetencao['vl_lancamento']);

			    $this->inclusao();
		    }
		}

		private function montaGerarValoresPorRecurso($stFiltro)
		{
			return "
				SELECT valor_lancamento.cod_lote, valor_lancamento.tipo, valor_lancamento.sequencia,
				       valor_lancamento.exercicio, valor_lancamento.tipo_valor, valor_lancamento.cod_entidade,
				       despesa.cod_recurso, valor_lancamento.vl_lancamento

				  FROM contabilidade.lancamento_retencao

				  JOIN empenho.ordem_pagamento_retencao
				    ON ordem_pagamento_retencao.cod_ordem = lancamento_retencao.cod_ordem
				   AND ordem_pagamento_retencao.cod_plano = lancamento_retencao.cod_plano
				   AND ordem_pagamento_retencao.exercicio = lancamento_retencao.exercicio
				   AND ordem_pagamento_retencao.sequencial = lancamento_retencao.sequencial
				   AND ordem_pagamento_retencao.cod_entidade = lancamento_retencao.cod_entidade

				  JOIN empenho.ordem_pagamento
				    ON ordem_pagamento.cod_ordem = ordem_pagamento_retencao.cod_ordem
				   AND ordem_pagamento.exercicio = ordem_pagamento_retencao.exercicio
				   AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
				    
				  JOIN empenho.pagamento_liquidacao
				    ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
				   AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
				   AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

				  JOIN empenho.nota_liquidacao
				    ON nota_liquidacao.exercicio = pagamento_liquidacao.exercicio
				   AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
				   AND nota_liquidacao.cod_nota = pagamento_liquidacao.cod_nota

				  JOIN empenho.empenho
				    ON empenho.exercicio = nota_liquidacao.exercicio
				   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
				   AND empenho.cod_empenho = nota_liquidacao.cod_empenho

				  JOIN empenho.pre_empenho
				    ON pre_empenho.exercicio = empenho.exercicio
				   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

				  JOIN empenho.pre_empenho_despesa  
				    ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
				   AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

				  JOIN orcamento.despesa
				    ON despesa.exercicio = pre_empenho_despesa.exercicio
				   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

				  JOIN contabilidade.lancamento
				    ON lancamento.tipo = lancamento_retencao.tipo
				   AND lancamento.cod_lote = lancamento_retencao.cod_lote
				   AND lancamento.exercicio = lancamento_retencao.exercicio
				   AND lancamento.sequencia = lancamento_retencao.sequencia
				   AND lancamento.cod_entidade = lancamento_retencao.cod_entidade

				  JOIN contabilidade.valor_lancamento
				    ON valor_lancamento.tipo = lancamento.tipo
				   AND valor_lancamento.cod_lote = lancamento.cod_lote
				   AND valor_lancamento.exercicio = lancamento.exercicio
				   -- AND valor_lancamento.sequencia = lancamento.sequencia
				   AND valor_lancamento.cod_entidade = lancamento.cod_entidade
			     WHERE " . $stFiltro;
		}
	}

