

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
 * Data de Criação: 14/02/2012

 * @author Desenvolvedor: Jean Felipe da Silva

 * @package URBEM
 * @subpackage Mapeamento

 $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTRNAnexo28 extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaHeader(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaHeader",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaHeader()
    {
        $stSql = "
        			SELECT
        					'0' AS tipo_registro
        				  , 'ANEXO28' AS nom_arquivo
        				  , '".$this->getDado('exercicio')."0".$this->getDado('inBimestre')."' AS bimestre
        				  , 'O' AS tipo_arquivo
        				  , to_char(CURRENT_DATE,'dd/mm/yyyy') AS dt_arquivo
        				  , substr(CAST(CURRENT_TIME AS text),1,8) AS hr_arquivo
        				  , configuracao_entidade.valor AS cod_orgao
                	, sw_cgm.nom_cgm AS nom_orgao

                	 FROM administracao.configuracao_entidade

            		 JOIN orcamento.entidade
            		   ON entidade.exercicio = configuracao_entidade.exercicio
            		  AND entidade.cod_entidade = configuracao_entidade.cod_entidade

            		 JOIN sw_cgm
              		   ON sw_cgm.numcgm = entidade.numcgm

            		WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
              		  AND configuracao_entidade.cod_entidade = ( SELECT valor::INTEGER
                                                           		   FROM administracao.configuracao
                                                          		  WHERE parametro = 'cod_entidade_prefeitura'
                                                            		AND exercicio = '".$this->getDado('exercicio')."'
                                                            	)
              		  AND configuracao_entidade.cod_modulo = 49
              		  AND configuracao_entidade.parametro = 'cod_orgao_tce'";
        return $stSql;
    }

    public function recuperaRegistro1(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro1",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro1()
    {
	$stSql = "
	     SELECT '1' AS tipo_registro
		      , bem_processo.cod_processo AS processo_origem
		      , sw_cgm_pessoa_juridica.cnpj AS cnpj
		      , sw_cgm.nom_cgm AS nome_contratado
		      , TO_CHAR(bem.dt_aquisicao, 'dd/mm/yyyy') AS dt_aquisicao
		      , bem.vl_bem AS vl_aquisicao
			  , veiculo.cod_veiculo
           FROM frota.veiculo
     INNER JOIN frota.veiculo_propriedade
    	     ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
    	    AND veiculo_propriedade.proprio = TRUE
    	    AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
    							 FROM frota.veiculo_propriedade as vp
    							WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
    						     )
     INNER JOIN frota.proprio
		     ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
		    AND proprio.timestamp   = veiculo_propriedade.timestamp   
     INNER JOIN patrimonio.bem
             ON bem.cod_bem = proprio.cod_bem
     INNER JOIN patrimonio.bem_comprado
    		 ON bem_comprado.cod_bem = bem.cod_bem
     INNER JOIN patrimonio.bem_processo
    		 ON bem_processo.cod_bem = bem.cod_bem
     INNER JOIN sw_cgm
    		 ON bem.numcgm = sw_cgm.numcgm
     INNER JOIN sw_cgm_pessoa_juridica
    		 ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
      LEFT JOIN frota.veiculo_cessao
             ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
    	  WHERE bem_comprado.exercicio = '".$this->getDado('exercicio')."'
    		AND bem_comprado.cod_entidade IN (".$this->getDado('inCodEntidade').")
    		AND TO_CHAR(bem.dt_aquisicao, 'dd/mm/yyyy') BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."'
            AND veiculo_cessao.cod_veiculo IS NULL ";
        return $stSql;
    }

    public function recuperaRegistro2(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro2",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro2()
    {
	$stSql = " 
		SELECT  '2' AS tipo_registro
		     , veiculo_locacao.cod_processo AS processo_origem
		     , sw_cgm_pessoa_juridica.cnpj AS cnpj
		     , sw_cgm.nom_cgm AS nome_locatario
		     , TO_CHAR(veiculo_locacao.dt_contrato , 'dd/mm/yyyy') AS dt_contrato
		     , TO_CHAR(veiculo_locacao.dt_inicio   , 'dd/mm/yyyy') AS ini_locacao
		     , TO_CHAR(veiculo_locacao.dt_termino  , 'dd/mm/yyyy') AS fim_locacao
		     , veiculo_locacao.vl_locacao AS vl_locacao
	         , veiculo.cod_veiculo
		  FROM frota.veiculo
	INNER JOIN frota.veiculo_propriedade
	        ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
	       AND veiculo_propriedade.proprio     = FALSE
	       AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
							 FROM frota.veiculo_propriedade as vp
							WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
						     )
    INNER JOIN frota.terceiros
		    ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
		   AND terceiros.timestamp   = veiculo_propriedade.timestamp
    INNER JOIN frota.veiculo_locacao
		    ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
	INNER JOIN sw_cgm_pessoa_juridica
		    ON sw_cgm_pessoa_juridica.numcgm = veiculo_locacao.cgm_locatario
    INNER JOIN sw_cgm 
	    	ON sw_cgm.numcgm = veiculo_locacao.cgm_locatario
	 LEFT JOIN frota.veiculo_cessao
            ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
	     WHERE veiculo_locacao.exercicio = '".$this->getDado('exercicio')."'
		   AND veiculo_locacao.cod_entidade IN (".$this->getDado('inCodEntidade').")
		   AND ( TO_DATE( TO_CHAR(veiculo_locacao.dt_inicio , 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy') 
              OR TO_DATE( TO_CHAR(veiculo_locacao.dt_termino, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy')  )
		   AND veiculo_cessao.cod_veiculo IS NULL ";
        return $stSql;
    }

    public function recuperaRegistro3(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro3",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro3()
    {
	$stSql = "
		SELECT '3' AS tipo_registro
		     , veiculo_cessao.cod_processo AS processo_origem
		     , sw_cgm_pessoa_juridica.cnpj AS cnpj_cedente
		     , sw_cgm.nom_cgm AS orgao_cedente
		     , TO_CHAR(veiculo_cessao.dt_inicio   , 'dd/mm/yyyy') AS ini_cessao
             , TO_CHAR(veiculo_cessao.dt_termino  , 'dd/mm/yyyy') AS fim_cessao
			 , veiculo.cod_veiculo
		  FROM frota.veiculo
	INNER JOIN frota.veiculo_propriedade
		    ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
		   AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
							  FROM frota.veiculo_propriedade as vp
							 WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
						      )
    INNER JOIN ( SELECT cod_veiculo
                      , bem_comprado.cod_entidade
                   FROM frota.proprio
             INNER JOIN patrimonio.bem
                     ON bem.cod_bem = proprio.cod_bem
             INNER JOIN patrimonio.bem_comprado
                     ON bem_comprado.cod_bem = bem.cod_bem  
                  WHERE proprio.timestamp = ( SELECT MAX(vp.timestamp)
                                                FROM frota.proprio as vp
                                               WHERE vp.cod_veiculo = proprio.cod_veiculo)       
                  UNION 

                 SELECT veiculo_locacao.cod_veiculo
                      , veiculo_locacao.cod_entidade
                   FROM frota.terceiros
             INNER JOIN frota.veiculo_locacao
                     ON veiculo_locacao.cod_veiculo = terceiros.cod_veiculo
                  WHERE terceiros.timestamp = ( SELECT MAX(ft.timestamp)
                                                  FROM frota.terceiros as ft
                                                 WHERE ft.cod_veiculo = terceiros.cod_veiculo)       
             ) AS dados_veiculo
            ON dados_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo							  
    INNER JOIN frota.veiculo_cessao
    		ON veiculo_cessao.cod_veiculo = dados_veiculo.cod_veiculo
    INNER JOIN sw_cgm_pessoa_juridica
	    	ON sw_cgm_pessoa_juridica.numcgm = veiculo_cessao.cgm_cedente
	INNER JOIN sw_cgm 
	   	    ON sw_cgm.numcgm = veiculo_cessao.cgm_cedente
	 	 WHERE  dados_veiculo.cod_entidade IN (".$this->getDado('inCodEntidade').")
		   AND ( TO_DATE( TO_CHAR(veiculo_cessao.dt_inicio , 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy') 
              OR TO_DATE( TO_CHAR(veiculo_cessao.dt_termino, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy')  )
		  ";
        return $stSql;
    }

    public function recuperaRegistro4(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro4",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro4()
    {
        $stSql = " SELECT distinct '4' AS tipo_registro
			            , CASE WHEN veiculo_aquisicao.cod_veiculo IS NOT NULL THEN 'Aquisição'
				               WHEN veiculo_locacao.cod_veiculo IS NOT NULL THEN 'locação'
				               WHEN veiculo_cessao.cod_veiculo IS NOT NULL THEN 'cessão'
			              END AS situacao
                        , CASE WHEN veiculo_aquisicao.cod_veiculo IS NOT NULL THEN veiculo_aquisicao.cod_processo
				               WHEN veiculo_locacao.cod_veiculo IS NOT NULL THEN veiculo_locacao.cod_processo
				               WHEN veiculo_cessao.cod_veiculo IS NOT NULL THEN veiculo_cessao.cod_processo
			              END AS processo_origem
			            , especie_veiculo_tce.nom_especie_tce AS id_especie
			            , tipo_veiculo_tce.nom_tipo_tce AS id_tipo
			            , marca.nom_marca||' '||modelo.nom_modelo AS id_marca
			            , veiculo.ano_fabricacao AS ano_fabricacao
			            , veiculo.placa AS placa
			            , veiculo.num_certificado AS renavam
			            , combustivel.nom_combustivel AS id_combustivel
			            , veiculo.capacidade_tanque AS tanque
			            , categoria_veiculo_tce.nom_categoria AS id_categoria
						, veiculo.cod_veiculo
		             FROM frota.veiculo
	           INNER JOIN frota.veiculo_propriedade
		               ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
		              AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
					 		                                  FROM frota.veiculo_propriedade as vp
						                                     WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
						                                  )
               INNER JOIN frota.veiculo_combustivel
                       ON veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo
               INNER JOIN frota.combustivel
                       ON combustivel.cod_combustivel = veiculo_combustivel.cod_combustivel
               INNER JOIN frota.marca
                       ON marca.cod_marca = veiculo.cod_marca
               INNER JOIN frota.modelo
                       ON modelo.cod_modelo = veiculo.cod_modelo
                      AND modelo.cod_marca = veiculo.cod_marca
               INNER JOIN frota.tipo_veiculo
                       ON tipo_veiculo.cod_tipo = veiculo.cod_tipo_veiculo
               INNER JOIN tcern.tipo_veiculo_vinculo 
                       ON tipo_veiculo_vinculo.cod_tipo = tipo_veiculo.cod_tipo
               INNER JOIN tcern.especie_veiculo_tce
                       ON especie_veiculo_tce.cod_especie_tce = tipo_veiculo_vinculo.cod_especie_tce
               INNER JOIN tcern.tipo_veiculo_tce
                       ON tipo_veiculo_tce.cod_tipo_tce = tipo_veiculo_vinculo.cod_tipo_tce
               INNER JOIN tcern.veiculo_categoria_vinculo
                       ON veiculo_categoria_vinculo.cod_veiculo = veiculo.cod_veiculo
               INNER JOIN tcern.categoria_veiculo_tce
                       ON categoria_veiculo_tce.cod_categoria = veiculo_categoria_vinculo.cod_categoria
                LEFT JOIN  frota.proprio
		               ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
		              AND proprio.timestamp   = veiculo_propriedade.timestamp   
		        LEFT JOIN patrimonio.bem
		               ON bem.cod_bem = proprio.cod_bem
   	            LEFT JOIN ( SELECT proprio.cod_veiculo
                                 , bem_processo.cod_processo
                              FROM frota.proprio
			            INNER JOIN patrimonio.bem
			  	                ON bem.cod_bem = proprio.cod_bem
			            INNER JOIN patrimonio.bem_comprado
  			  	                ON bem_comprado.cod_bem = bem.cod_bem
			            INNER JOIN patrimonio.bem_processo
           				        ON bem_processo.cod_bem = bem.cod_bem
						 LEFT JOIN frota.veiculo_cessao
                                ON veiculo_cessao.cod_veiculo = proprio.cod_veiculo
            			     WHERE bem_comprado.exercicio = '".$this->getDado('exercicio')."'
				               AND bem_comprado.cod_entidade IN (".$this->getDado('inCodEntidade').")
				               AND TO_CHAR(bem.dt_aquisicao, 'dd/mm/yyyy') BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."'
							   AND veiculo_cessao.cod_veiculo IS NULL
                        ) AS veiculo_aquisicao
                       ON veiculo_aquisicao.cod_veiculo = veiculo.cod_veiculo
                LEFT JOIN ( SELECT veiculo.cod_veiculo
                                 , veiculo_locacao.cod_processo
                              FROM frota.veiculo
			            INNER JOIN frota.veiculo_propriedade
				                ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
			                   AND veiculo_propriedade.proprio     = FALSE
			            INNER JOIN frota.terceiros
				                ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
				               AND terceiros.timestamp   = veiculo_propriedade.timestamp
			            INNER JOIN frota.veiculo_locacao 
				                ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
						 LEFT JOIN frota.veiculo_cessao
                                ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
			                 WHERE veiculo_locacao.exercicio = '".$this->getDado('exercicio')."'
				               AND veiculo_locacao.cod_entidade IN (".$this->getDado('inCodEntidade').")
				               AND ( TO_DATE( TO_CHAR(veiculo_locacao.dt_inicio , 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy') 
                                  OR TO_DATE( TO_CHAR(veiculo_locacao.dt_termino, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy')  )
							   AND veiculo_cessao.cod_veiculo IS NULL
                        ) AS veiculo_locacao
                       ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
                LEFT JOIN ( SELECT veiculo.cod_veiculo
		                         , veiculo_cessao.cod_processo
                              FROM frota.veiculo
 		                INNER JOIN frota.veiculo_propriedade
			                    ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
					    INNER JOIN ( SELECT cod_veiculo
                                          , bem_comprado.cod_entidade
                                       FROM frota.proprio
                                 INNER JOIN patrimonio.bem
                                         ON bem.cod_bem = proprio.cod_bem
                                 INNER JOIN patrimonio.bem_comprado
                                         ON bem_comprado.cod_bem = bem.cod_bem  
                                      WHERE proprio.timestamp = ( SELECT MAX(vp.timestamp)
                                                                    FROM frota.proprio as vp
                                                                   WHERE vp.cod_veiculo = proprio.cod_veiculo)       
                                      UNION 
                                
                                     SELECT veiculo_locacao.cod_veiculo
                                          , veiculo_locacao.cod_entidade
                                       FROM frota.terceiros
                                 INNER JOIN frota.veiculo_locacao
                                         ON veiculo_locacao.cod_veiculo = terceiros.cod_veiculo
                                      WHERE terceiros.timestamp = ( SELECT MAX(ft.timestamp)
                                                                      FROM frota.terceiros as ft
                                                                     WHERE ft.cod_veiculo = terceiros.cod_veiculo)       
                                ) AS dados_veiculo
					           ON dados_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo			
		               INNER JOIN frota.veiculo_cessao
			                   ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
	                        WHERE  dados_veiculo.cod_entidade IN (".$this->getDado('inCodEntidade').")
		                      AND ( TO_DATE( TO_CHAR(veiculo_cessao.dt_inicio , 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy') 
                                 OR TO_DATE( TO_CHAR(veiculo_cessao.dt_termino, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."' , 'dd/mm/yyyy')  )
                    ) AS veiculo_cessao
                   ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo ";
	    
        return $stSql;
    }

}

?>