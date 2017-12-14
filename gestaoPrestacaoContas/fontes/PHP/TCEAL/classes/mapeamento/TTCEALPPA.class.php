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
    * Extensão da Classe de Mapeamento TTCEALPPA
    *
    * Data de Criação: 30/05/2014
    *
    * @author: Arthur Cruz
    *
*/
class TTCEALPPA extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALPPA()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montarecuperaPPA.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaPPA(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montarecuperaPPA().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montarecuperaPPA()
    {
        $stSql  = "	SELECT cod_und_gestora
			      , codigo_ua
			      , exercicio 
			      , cod_orgao
			      , cod_und_orcamentaria
			      , cod_programa
			      , cod_proj_atividade
			      , meta_fisica_1Ano
			      , meta_fisica_2Ano
			      , meta_fisica_3Ano
			      , meta_fisica_4Ano
			      , meta_fisica_total
			      , meta_financeira_1Ano
			      , meta_financeira_2Ano
			      , meta_financeira_3Ano
			      , meta_financeira_4Ano
			      , meta_financeira_total
			FROM (
			      SELECT (SELECT PJ.cnpj
                                        FROM orcamento.entidade
                                  INNER JOIN sw_cgm
                                          ON sw_cgm.numcgm = entidade.numcgm
                                  INNER JOIN sw_cgm_pessoa_juridica AS PJ
                                          ON sw_cgm.numcgm = PJ.numcgm
                                       WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                         AND entidade.cod_entidade = ".$this->getDado('und_gestora')." )
                         AS cod_und_gestora
  		               , LPAD ((SELECT valor
  		                     FROM administracao.configuracao_entidade
  		                    WHERE exercicio    = '".$this->getDado('exercicio')."'
  		                      AND cod_entidade = ".$this->getDado('und_gestora')."
  		                      AND cod_modulo   = 62
  		                      AND parametro    = 'tceal_configuracao_unidade_autonoma'),4,'0')
			             AS codigo_ua
    		           ,'".$this->getDado('exercicio')."' AS exercicio
  			           , LPAD(programa_dados.num_orgao::varchar,2,'0') AS cod_orgao
  			           , LPAD(programa_dados.num_unidade::varchar,4,'0') AS cod_und_orcamentaria
  			           , LPAD(programa.num_programa::varchar,4,'0') AS cod_programa
  			           , LPAD(acao.num_acao::varchar,4,'0') AS cod_proj_atividade
  			           , Ano1.quantidade AS meta_fisica_1Ano
  			           , Ano2.quantidade AS meta_fisica_2Ano
  			           , Ano3.quantidade AS meta_fisica_3Ano
  			           , Ano4.quantidade AS meta_fisica_4Ano
  			           , ((Ano1.quantidade)+(Ano2.quantidade)+(Ano3.quantidade)+(Ano4.quantidade)) AS meta_fisica_total
  			           , Ano1.valor AS meta_financeira_1Ano
  			           , Ano2.valor AS meta_financeira_2Ano
  			           , Ano3.valor AS meta_financeira_3Ano
  			           , Ano4.valor AS meta_financeira_4Ano
  			           , ((Ano1.valor)+(Ano2.valor)+(Ano3.valor)+(Ano4.valor)) AS meta_financeira_total 
  			    
  		            FROM ppa.programa
			   
			  INNER JOIN orcamento.programa_ppa_programa 
                      ON programa_ppa_programa.cod_programa_ppa = programa.cod_programa

              INNER JOIN orcamento.programa AS orcamento_programa
                      ON programa_ppa_programa.cod_programa = orcamento_programa.cod_programa
                     
              INNER JOIN orcamento.despesa 
                      ON despesa.exercicio = orcamento_programa.exercicio
                     AND despesa.cod_programa = orcamento_programa.cod_programa
                     AND despesa.cod_entidade = ".$this->getDado('und_gestora')."
		       
  	          INNER JOIN ppa.acao
  			          ON programa.cod_programa = acao.cod_programa
			 
  		      INNER JOIN ppa.acao_quantidade
  			          ON acao.cod_acao = acao_quantidade.cod_acao
			  
  		      INNER JOIN (
  			              SELECT *
			                FROM ppa.acao_quantidade AS Ano1
  			               WHERE Ano1.exercicio_recurso = (SELECT ano_inicio from ppa.ppa WHERE '2016' between ano_inicio AND ano_final)
  			            ) AS Ano1
  			          ON Ano1.cod_acao             = acao_quantidade.cod_acao
  			         AND Ano1.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
			                                             FROM ppa.acao_quantidade as times
			         	                               WHERE times.cod_acao = Ano1.cod_acao
			         				                     AND times.exercicio_recurso = Ano1.exercicio_recurso
			         	                            ORDER BY timestamp_acao_dados DESC LIMIT 1 )

  		      INNER JOIN (
  			              SELECT *
			                FROM ppa.acao_quantidade AS Ano2
  			               WHERE Ano2.exercicio_recurso = ((((SELECT ano_inicio from ppa.ppa WHERE '2016' between ano_inicio AND ano_final)::integer)+1)::VARCHAR)
  			            ) AS Ano2
  			          ON Ano2.cod_acao             = acao_quantidade.cod_acao
  			         AND Ano2.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
			                                             FROM ppa.acao_quantidade AS times
				                                        WHERE times.cod_acao = Ano2.cod_acao
							                              AND times.exercicio_recurso = Ano2.exercicio_recurso
				                                     ORDER BY timestamp_acao_dados DESC LIMIT 1 )

  		      INNER JOIN (
  			              SELECT *
			                FROM ppa.acao_quantidade AS Ano3
  			               WHERE Ano3.exercicio_recurso = ((((SELECT ano_inicio from ppa.ppa WHERE '2016' between ano_inicio AND ano_final)::integer)+2)::VARCHAR)
  			            ) AS Ano3
  			          ON Ano3.cod_acao = acao_quantidade.cod_acao
  			         AND Ano3.timestamp_acao_dados = (SELECT times.timestamp_acao_dados
			                                            FROM ppa.acao_quantidade AS times
				                                       WHERE times.cod_acao = Ano3.cod_acao
							                             AND times.exercicio_recurso = Ano3.exercicio_recurso
				                                    ORDER BY timestamp_acao_dados DESC LIMIT 1 )

  		      INNER JOIN (
  			              SELECT *
			                FROM ppa.acao_quantidade AS Ano4
  			               WHERE Ano4.exercicio_recurso = ((((SELECT ano_inicio from ppa.ppa WHERE '2016' between ano_inicio AND ano_final)::integer)+3)::VARCHAR)
  			            ) AS Ano4
  			          ON Ano4.cod_acao             = acao_quantidade.cod_acao
  			         AND Ano4.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
			                                             FROM ppa.acao_quantidade AS times
			         	                                WHERE times.cod_acao = Ano4.cod_acao
			         				                      AND times.exercicio_recurso = Ano4.exercicio_recurso
			         	                             ORDER BY timestamp_acao_dados DESC LIMIT 1)
			  
		      INNER JOIN ppa.programa_dados
			          ON programa_dados.cod_programa = programa.cod_programa
		       
  		           WHERE acao_quantidade.exercicio_recurso    = '".$this->getDado('exercicio')."'
  			         AND acao_quantidade.timestamp_acao_dados = Ano1.timestamp_acao_dados
                     AND programa_dados.num_orgao = (SELECT * 
                                                      FROM tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."'
                                                                                       , ".$this->getDado('und_gestora')."
                                                                                       , 'orgao')
                                                    )
                     AND programa_dados.num_unidade = (SELECT * 
                                                       FROM tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."'
                                                                                        , ".$this->getDado('und_gestora')."
                                                                                        , 'unidade')
                                                       )
		       
  		        GROUP BY programa.cod_programa 
  			           , cod_proj_atividade
  			           , acao_quantidade.ano
  			           , acao_quantidade.cod_recurso
  			           , acao_quantidade.valor
  			           , acao_quantidade.quantidade
  			           , programa_dados.num_orgao
  			           , programa_dados.num_unidade
  			           , Ano1.quantidade
  			           , Ano2.quantidade
  			           , Ano3.quantidade
  			           , Ano4.quantidade
  			           , Ano1.valor
  			           , Ano2.valor
  			           , Ano3.valor
  			           , Ano4.valor
  		     
		        ORDER BY cod_programa
		               , cod_proj_atividade
				) AS TBL";
		 
        return $stSql;
    }
}

?>
