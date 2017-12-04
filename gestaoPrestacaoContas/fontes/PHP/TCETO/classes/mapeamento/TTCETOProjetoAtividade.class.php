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
    * Extensão da Classe de Mapeamento TTCETOProjetoAtividade
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCETOProjetoAtividade.class.php 60910 2014-11-24 16:20:59Z carolina $
    *
    * @ignore
    *
*/

class TTCETOProjetoAtividade extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCETOProjetoAtividade()
    {
        parent::Persistente();
    }
    /**
     * Método para trazer todos os registros de Projeto Atividade, para o TCETO
     * @access Public
     * @param  Object  $rsRecordSet Objeto RecordSet
     * @param  String  $stCondicao  String de condição do SQL (WHERE)
     * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
     * @param  Boolean $boTransacao
     * @return Object  Objeto Erro
    */
    public function recuperaProjetoAtividade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaProjetoAtividade().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaProjetoAtividade()
    {
        $stSql = "
        SELECT (SELECT PJ.cnpj
                  FROM orcamento.entidade
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm
                  JOIN sw_cgm_pessoa_juridica AS PJ
                    ON sw_cgm.numcgm = PJ.numcgm
                 WHERE entidade.exercicio = consulta.exercicio
                   AND entidade.cod_entidade = consulta.cod_entidade)
                AS cod_und_gestora
             , ".$this->getDado('bimestre')." AS bimestre
             , exercicio AS exercicio
             , LPAD(num_acao::VARCHAR,4,'0') AS cod_proj_atividade
             , LPAD(cod_identificador::VARCHAR, 2, '0') AS identificador
             , titulo AS nome
          
          FROM (SELECT despesa.cod_entidade
                     , pao.exercicio
                     , acao.num_acao
                     , cod_identificador
                     , acao_dados.titulo
                  FROM orcamento.pao
            
            INNER JOIN orcamento.pao_ppa_acao
                    ON pao_ppa_acao.num_pao = pao.num_pao
                   AND pao_ppa_acao.exercicio = pao.exercicio
            
            INNER JOIN orcamento.despesa
                    ON despesa.exercicio = pao.exercicio
                   AND despesa.num_pao = pao.num_pao
            
            INNER JOIN ppa.acao
                    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
            
            INNER JOIN ppa.acao_dados
                    ON acao.cod_acao                    = acao_dados.cod_acao
                   AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados

            LEFT JOIN tceto.acao_identificador_acao
                    ON acao_identificador_acao.cod_acao = acao.cod_acao

                 WHERE pao.exercicio = '".$this->getDado('exercicio')."'
                   AND despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
                   
              GROUP BY despesa.cod_entidade
                     , pao.exercicio
                     , acao.num_acao
                     , cod_identificador
                     , acao_dados.titulo
                     
              ORDER BY acao.num_acao ASC
              
               ) AS consulta ";
               
        return $stSql;
    }
}
?>