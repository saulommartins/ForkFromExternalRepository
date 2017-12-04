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
    * Extensão da Classe de Mapeamento TTCEALPrograma
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCEALPrograma.class.php 64731 2016-03-23 20:32:26Z arthur $
    *
    * @ignore
    *
*/

class TTCEALPrograma extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCEALPrograma()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCEAL
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
    public function recuperaPrograma(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPrograma().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaPrograma()
    {
        $stSql = "
            SELECT ( SELECT PJ.cnpj
                       FROM orcamento.entidade
                 INNER JOIN sw_cgm
                         ON sw_cgm.numcgm = entidade.numcgm
                 INNER JOIN sw_cgm_pessoa_juridica AS PJ
                         ON sw_cgm.numcgm = PJ.numcgm
                      WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                        AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                    ) AS cod_und_gestora
                 , ( SELECT LPAD(COALESCE(valor, '0'),4,'0') AS valor
                       FROM administracao.configuracao_entidade
                      WHERE configuracao_entidade.cod_modulo   = 62
                        AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                        AND configuracao_entidade.parametro like 'tceal_configuracao_unidade_autonoma'
                        AND configuracao_entidade.cod_entidade =  ".$this->getDado('cod_entidade')."
                      ) AS codigo_ua
                 , ".$this->getDado('exercicio')." AS exercicio
                 , cod_programa
                 , nome
                 , objetivo
                 , publico_alvo
            
            FROM (
	               SELECT LPAD( programa.num_programa::VARCHAR, 4, '0000' ) AS cod_programa
                        , SUBSTR( programa_dados.identificacao, 0, 100 ) AS nome 
                        , SUBSTR( programa_dados.objetivo, 0, 255 ) AS objetivo 
			            , SUBSTR ( programa_dados.publico_alvo, 0, 255 ) AS publico_alvo

                    FROM ppa.programa
                        
              INNER JOIN ppa.programa_dados
                      ON programa_dados.timestamp_programa_dados = programa.ultimo_timestamp_programa_dados
                     AND programa_dados.cod_programa             = programa.cod_programa

              INNER JOIN ppa.tipo_programa
                      ON tipo_programa.cod_tipo_programa = programa_dados.cod_tipo_programa

              INNER JOIN orcamento.programa_ppa_programa
                      ON programa_ppa_programa.cod_programa_ppa = programa.cod_programa 

              INNER JOIN orcamento.programa AS orcamento_programa
                      ON programa_ppa_programa.exercicio    = orcamento_programa.exercicio
                     AND programa_ppa_programa.cod_programa = orcamento_programa.cod_programa

                GROUP BY programa.num_programa
                       , programa_dados.identificacao
                       , programa_dados.objetivo
                       , programa_dados.publico_alvo

              ) AS consulta ";
        
        return $stSql;
    }
}
?>