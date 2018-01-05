<?php

    class TTCEMGArquivoFundos extends Persistente
    {
        public $obConexao;

        /**
         * Método Construtor
         * @access public
         */
        public function TTCEMGArquivoFundos()
        {
            parent::Persistente();
            $this->obConexao = new Conexao;
        }    

        public function recuperaRegistro($tipoRegistro, RecordSet &$rsRecordSet)
        {
            $metodo = "montaRecuperaRegistro" . $tipoRegistro;
            $stSql = $this->{$metodo}();

            $this->setDebug( $stSql );
            return $this->obConexao->executaSQL( $rsRecordSet, $stSql );
        }
        
        private function montaRecuperaRegistro10()
        {
            return "
                SELECT 10 as tipo_registro, 
                       fundo.cod_fundo,
                       NULLIF(
                          regexp_replace(
                            CASE WHEN fundo.cnpj IS NOT NULL AND fundo.cnpj != ''
                                 THEN fundo.cnpj::VARCHAR
                                 ELSE sw_cgm_pessoa_juridica.cnpj
                             END
                          , '\D','','g'),
                       '')::numeric AS cnpj,
                       fundo.descricao, 
                       fundo.contabilidade_centralizada,
                       CASE WHEN fundo.cod_entidade = 5
                            THEN fundo.plano::VARCHAR
                            ELSE '' 
                        END AS plano

                  FROM contabilidade.fundo
                  
                  JOIN orcamento.entidade
                    ON entidade.cod_entidade = fundo.cod_entidade
                   AND entidade.exercicio = fundo.exercicio

                  LEFT JOIN sw_cgm 
                    ON sw_cgm.numcgm = entidade.numcgm

                  LEFT JOIN sw_cgm_pessoa_juridica 
                    ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                 WHERE fundo.exercicio = '".$this->getDado('exercicio')."'
                   AND situacao = 1";
        }

        private function montaRecuperaRegistro11()
        {
            return "
                 SELECT 11 as tipo_registro, 
                        fundo.cod_fundo, 
                        LPAD(fundo.cod_orgao::VARCHAR, 5, '0') AS cod_unidade,
                        LPAD(LPAD(fundo.cod_orgao::VARCHAR, 2, '0') || LPAD(fundo.cod_unidade::VARCHAR, 2, '0'),5,'0')::VARCHAR AS cod_sub_unidade
                   FROM contabilidade.fundo
                  WHERE exercicio = '".$this->getDado('exercicio')."'
                    AND situacao = 1";
        }

        private function montaRecuperaRegistro20()
        {
            return "
                 SELECT 20 AS tipo_registro, 
                        fundo.cod_fundo, 
                        to_char(fundo.data_extincao, 'ddmmyyyy') AS data_extincao
                   FROM contabilidade.fundo
                  WHERE exercicio = '".$this->getDado('exercicio')."'
                    AND situacao = 0";
        }
    }
?>