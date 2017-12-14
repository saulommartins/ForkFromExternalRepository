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
    * Extensão da Classe de mapeamento
    * Data de Criação: 09/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCETOPPA extends Persistente
{

    function montaRecuperaTodos()
    {
        $stSql = "
                    SELECT *
		    	FROM (
		    	      SELECT LPAD((SELECT PJ.cnpj
                                            FROM orcamento.entidade
                                      INNER JOIN sw_cgm
                                              ON sw_cgm.numcgm = entidade.numcgm
                                      INNER JOIN sw_cgm_pessoa_juridica AS PJ
                                              ON sw_cgm.numcgm = PJ.numcgm
                                           WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                             AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                                ),14,'0') AS cod_und_gestora
  		          , LPAD('".$this->getDado('exercicio')."'::VARCHAR,4,'0') AS exercicio
  		    	  , LPAD(programa_dados.num_orgao::varchar,2,'0') AS cod_orgao
  		    	  , LPAD(programa_dados.num_unidade::varchar,4,'0') AS cod_und_orcamentaria
  		    	  , LPAD(programa.num_programa::varchar,4,'0') AS cod_programa
  		    	  , LPAD(acao.num_acao::varchar,4,'0') AS cod_proj_atividade
  		    	  , COALESCE(Ano1.quantidade,0.00) AS meta_fisica_1ano
  		    	  , COALESCE(Ano2.quantidade,0.00) AS meta_fisica_2ano
  		    	  , COALESCE(Ano3.quantidade,0.00) AS meta_fisica_3ano
  		    	  , COALESCE(Ano4.quantidade,0.00) AS meta_fisica_4ano
  		    	  , COALESCE(((Ano1.quantidade)+(Ano2.quantidade)+(Ano3.quantidade)+(Ano4.quantidade)),0.00) AS meta_fisica_total
  		    	  , COALESCE(Ano1.valor,0.00) AS meta_financeira_1ano
  		    	  , COALESCE(Ano2.valor,0.00) AS meta_financeira_2ano
  		    	  , COALESCE(Ano3.valor,0.00) AS meta_financeira_3ano
  		    	  , COALESCE(Ano4.valor,0.00) AS meta_financeira_4ano
  		    	  , COALESCE(((Ano1.valor)+(Ano2.valor)+(Ano3.valor)+(Ano4.valor)),0.00) AS meta_financeira_total
                          , LPAD(acao_dados.cod_unidade_medida::VARCHAR,2,'0') AS cod_unidade_medida
  		    	    
  		           FROM ppa.programa
		           
  	             INNER JOIN ppa.acao
  		    	     ON programa.cod_programa = acao.cod_programa
		    	 
  		    INNER JOIN ppa.acao_quantidade
  		    	    ON acao.cod_acao = acao_quantidade.cod_acao
                              
                     INNER JOIN ppa.acao_dados
                             ON acao_dados.cod_acao = acao.cod_acao
		    	  
  		      INNER JOIN (
  		    	     SELECT *
		    	       FROM ppa.acao_quantidade AS Ano1
  		    	      WHERE Ano1.exercicio_recurso = ('".$this->getDado('exercicio')."')
  		    	     ) AS Ano1
  		    	  ON Ano1.cod_acao             = acao_quantidade.cod_acao
  		    	 AND Ano1.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
		    	                                     FROM ppa.acao_quantidade as times
		    		                            WHERE times.cod_acao          = Ano1.cod_acao
		    					      AND times.exercicio_recurso = Ano1.exercicio_recurso
		    		                         ORDER BY timestamp_acao_dados DESC LIMIT 1 )
                    
  		      INNER JOIN (
  		    	     SELECT *
		    	       FROM ppa.acao_quantidade AS Ano2
  		    	      WHERE Ano2.exercicio_recurso = ((('".$this->getDado('exercicio')."'::integer)+1)::VARCHAR)
  		    	     ) AS Ano2
  		    	  ON Ano2.cod_acao             = acao_quantidade.cod_acao
  		    	 AND Ano2.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
		    	                                     FROM ppa.acao_quantidade AS times
		    		                            WHERE times.cod_acao          = Ano2.cod_acao
		    					      AND times.exercicio_recurso = Ano2.exercicio_recurso
		    		                         ORDER BY timestamp_acao_dados DESC LIMIT 1 )
                    
  		      INNER JOIN (
  		    	     SELECT *
		    	      FROM ppa.acao_quantidade AS Ano3
  		    	     WHERE Ano3.exercicio_recurso = ((('".$this->getDado('exercicio')."'::integer)+2)::VARCHAR)
  		    	     ) AS Ano3
  		    	  ON Ano3.cod_acao             = acao_quantidade.cod_acao
  		    	 AND Ano3.timestamp_acao_dados = (SELECT times.timestamp_acao_dados
		    	                                    FROM ppa.acao_quantidade AS times
		    		                           WHERE times.cod_acao          = Ano3.cod_acao
		    					     AND times.exercicio_recurso = Ano3.exercicio_recurso
		    		                        ORDER BY timestamp_acao_dados DESC LIMIT 1 )
                    
  		      INNER JOIN (
  		    	     SELECT *
		    	       FROM ppa.acao_quantidade AS Ano4
  		    	      WHERE Ano4.exercicio_recurso = ((('".$this->getDado('exercicio')."'::integer)+3)::VARCHAR)
  		    	     ) AS Ano4
  		    	  ON Ano4.cod_acao             = acao_quantidade.cod_acao
  		    	 AND Ano4.timestamp_acao_dados = ( SELECT times.timestamp_acao_dados
		    	                                     FROM ppa.acao_quantidade AS times
		    		                            WHERE times.cod_acao          = Ano4.cod_acao
		    					      AND times.exercicio_recurso = Ano4.exercicio_recurso
		    		                         ORDER BY timestamp_acao_dados DESC LIMIT 1)
		    	  
		      INNER JOIN ppa.programa_dados
		    	      ON programa_dados.cod_programa = programa.cod_programa
		           
  		           WHERE acao_quantidade.exercicio_recurso    = '".$this->getDado('exercicio')."'
  		    	     AND acao_quantidade.timestamp_acao_dados = Ano1.timestamp_acao_dados
		           
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
                               , acao_dados.cod_unidade_medida
  		         
		         ORDER BY cod_programa
		                , cod_proj_atividade
		    	 
		    ) AS TBL
                    
                    GROUP BY cod_und_gestora
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
                           , cod_unidade_medida ";
        
        return $stSql;
    }

}//FIM CLASSE
