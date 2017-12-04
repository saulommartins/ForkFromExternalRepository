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
    * Extensão da Classe de Mapeamento TTCETOPrograma
    *
    * Data de Criação: 07/11/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    * $Id: TTCETOPrograma.class.php 60678 2014-11-07 18:06:09Z franver $
    *
    * @ignore
    *
*/

class TTCETOPrograma extends Persistente {
    /**
        * Método Construtor
        * @access Public
    */
    public function TTCETOPrograma()
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
    SELECT (SELECT PJ.cnpj
              FROM orcamento.entidade
              JOIN sw_cgm
                ON sw_cgm.numcgm=entidade.numcgm
              JOIN sw_cgm_pessoa_juridica AS PJ
                ON sw_cgm.numcgm=PJ.numcgm
             WHERE entidade.exercicio=consulta.exercicio
               AND entidade.cod_entidade=consulta.cod_entidade
            ) AS cod_und_gestora
         , exercicio
         , LPAD(cod_programa::varchar,4,'0') AS cod_programa
         , nome
         , objetivo
         , publico_alvo
      FROM (SELECT despesa.exercicio
                 , ppa_programa.num_programa AS cod_programa
                 , SUBSTR(programa_dados.identificacao, 0, 100) AS nome 
                 , SUBSTR(programa_dados.objetivo, 0, 255) AS objetivo 
                 , SUBSTR(programa_dados.publico_alvo, 0, 255) AS publico_alvo
                 , despesa.cod_entidade
              FROM orcamento.programa
              JOIN orcamento.programa_ppa_programa
                ON programa_ppa_programa.exercicio = programa.exercicio
               AND programa_ppa_programa.cod_programa = programa.cod_programa
              JOIN orcamento.despesa
                ON despesa.exercicio = programa.exercicio
               AND despesa.cod_programa = programa.cod_programa
              JOIN ppa.programa AS ppa_programa
                ON ppa_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
              JOIN ppa.programa_dados
                ON programa_dados.cod_programa = ppa_programa.cod_programa
             WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
               AND despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
             GROUP BY despesa.exercicio
                 , despesa.cod_entidade
                 , ppa_programa.num_programa
                 , programa_dados.identificacao
                 , programa_dados.objetivo
                 , programa_dados.publico_alvo
             ORDER BY ppa_programa.num_programa
           ) AS consulta
        ";
        
        return $stSql;
    }
}
?>