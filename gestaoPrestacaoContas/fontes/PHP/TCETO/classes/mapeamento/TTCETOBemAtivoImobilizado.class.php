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

    * Extensão da Classe de Mapeamento TTCETOBemAtivoImobilizado
    *
    * Data de Criação: 13/11/2014
    *
    * @author: Lisiane Morais
    *
    * $Id:$
    *
    * @ignore
    *
*/
class TTCETOBemAtivoImobilizado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCETOBemAtivoImobilizado()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
  
   public function recuperaBemAtivoImobilizado(&$rsRecordSet,$stFiltro="",$stOrder=" ",$boTransacao="")
  {
         $stSql = "
                SELECT cod_und_gestora
                     , bimestre
                     , exercicio
                     , cod_orgao
                     , cod_und_orcamentaria
                     , num_bem
                     , RPAD(descricao,255,'') AS descricao
                     , LPAD(num_empenho,13,'0') AS num_empenho
                     , data_inscricao
                     , valor_bem
                     , setor
                     , num_tombamento
                     , LPAD(conta_contabil,17,'0') as conta_contabil
                     , LPAD(estado_bem::VARCHAR, 2 , '0') AS estado_bem
                     , LPAD(alteracao_bem_ativo_imobilizado::VARCHAR, 2 , '0') AS alteracao_bem_ativo_imobilizado
                     , percentual
              
                  FROM ( SELECT  (SELECT PJ.cnpj 
                                    FROM orcamento.entidade 
                              INNER JOIN sw_cgm 
                                      ON sw_cgm.numcgm = entidade.numcgm
                              INNER JOIN sw_cgm_pessoa_juridica AS PJ 
                                      ON PJ.numcgm = sw_cgm.numcgm
                                   WHERE entidade.exercicio    = '".$this->getDado('exercicio')."' 
                                     AND entidade.cod_entidade = ".$this->getDado('inCodEntidade')." 
                               ) AS cod_und_gestora
                            , ".$this->getDado('bimestre')." AS bimestre
                            , '".$this->getDado('exercicio')."' AS exercicio
                            , LPAD(bem_comprado.num_orgao::VARCHAR,2,'0') AS cod_orgao
                            , LPAD(bem_comprado.num_unidade::VARCHAR,4,'0') AS cod_und_orcamentaria
                            , bem.cod_bem AS num_bem
                            , bem.descricao
                            , bem.num_placa AS num_tombamento
                            , CASE WHEN bem_comprado.cod_empenho::VARCHAR  <> '' THEN
                                        TO_CHAR(dt_empenho,'yyyymm')||bem_comprado.cod_empenho::VARCHAR
                                    ELSE 
                                        ''::VARCHAR
                              END AS num_empenho
                            , TO_CHAR(bem.dt_aquisicao,'dd/mm/yyyy') AS data_inscricao 
			    , bem.vl_bem AS valor_bem
			    , local.descricao AS setor
			    , REPLACE(estrutural.cod_estrutural, '.','') AS  conta_contabil
                            , historico_bem.cod_situacao AS estado_bem
			    , CASE 
			         WHEN (depreciacao.cod_bem = bem_comprado.cod_bem) THEN 2
			         WHEN (bem.dt_incorporacao BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                       AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy') ) THEN 5
			         WHEN (reavaliacao.cod_bem = bem_comprado.cod_bem) THEN 6 
			         WHEN (bem_baixado.cod_bem = bem_comprado.cod_bem AND tipo_baixa = 1 OR tipo_baixa = 2) THEN 10
			         WHEN (bem_baixado.cod_bem = bem_comprado.cod_bem AND tipo_baixa != 1 OR tipo_baixa != 2) THEN 11
			         ELSE 01
			     END AS alteracao_bem_ativo_imobilizado
			    , CASE 
			         WHEN (depreciacao.cod_bem = bem_comprado.cod_bem) THEN depreciacao.quota_utilizada
			      END AS percentual
                        FROM patrimonio.bem
                  INNER JOIN patrimonio.bem_comprado
                          ON bem_comprado.cod_bem      = bem.cod_bem
                   LEFT JOIN tceal.bem_comprado_tipo_documento_fiscal
                          ON bem_comprado_tipo_documento_fiscal.cod_bem = bem_comprado.cod_bem
                  INNER JOIN patrimonio.historico_bem
                          ON historico_bem.cod_bem = bem.cod_bem	
                         AND historico_bem.timestamp = (Select max(timestamp) from patrimonio.historico_bem as HB where cod_bem = bem.cod_bem )
                  INNER JOIN organograma.local
                          ON historico_bem.cod_local = local.cod_local
                   LEFT JOIN empenho.empenho
                          ON bem_comprado.cod_empenho = empenho.cod_empenho
                         AND bem_comprado.cod_entidade = empenho.cod_entidade
                         AND bem_comprado.exercicio = empenho.exercicio

                   LEFT JOIN ( SELECT bem_comprado.cod_bem
			            , bem_comprado.exercicio
			            , bem_comprado.exercicio AS bem_plano_exercicio
			            , CASE WHEN bem_plano_analitica.cod_plano IS NOT NULL
			            		THEN bem_plano_analitica.cod_plano
			            	ELSE grupo_plano_analitica.cod_plano
			              END AS cod_plano
			            , CASE WHEN bem_plano_analitica.cod_plano IS NOT NULL
			            		THEN bem_plano_analitica.cod_estrutural
			            	ELSE grupo_plano_analitica.cod_estrutural
			            END AS cod_estrutural
		                FROM patrimonio.bem_comprado         
                           LEFT JOIN ( SELECT bem_plano_analitica.cod_bem
                                            , bem_plano_analitica.cod_plano 
                                            , bem_plano_analitica.exercicio
                                            , MAX(bem_plano_analitica.timestamp::timestamp) AS timestamp
			                    , plano_conta.cod_estrutural
                                            , plano_conta.nom_conta AS nom_conta_depreciacao
                                         FROM patrimonio.bem_plano_analitica
                                    LEFT JOIN contabilidade.plano_analitica
                                           ON plano_analitica.cod_plano = bem_plano_analitica.cod_plano
                                          AND plano_analitica.exercicio = bem_plano_analitica.exercicio
                                    LEFT JOIN contabilidade.plano_conta
                                           ON plano_conta.cod_conta = plano_analitica.cod_conta
                                          AND plano_conta.exercicio = plano_analitica.exercicio
                                     GROUP BY bem_plano_analitica.cod_bem
                                            , bem_plano_analitica.cod_plano
                                            , bem_plano_analitica.exercicio
                                            , plano_conta.cod_estrutural
                                            , plano_conta.nom_conta 
                                     ORDER BY timestamp DESC
                                    )AS bem_plano_analitica
                                  ON bem_plano_analitica.cod_bem   = bem_comprado.cod_bem
                                  
                           LEFT JOIN ( SELECT grupo_plano_analitica.cod_plano
	                                    , cod_bem
	                                    , plano_conta.cod_estrutural
                                            , plano_conta.nom_conta 
                    		         FROM patrimonio.grupo_plano_analitica
                                   INNER JOIN patrimonio.grupo
                                           ON grupo.cod_natureza = grupo_plano_analitica.cod_natureza
                                          AND grupo.cod_grupo    = grupo_plano_analitica.cod_grupo
                                   INNER JOIN patrimonio.especie
                                           ON especie.cod_grupo    = grupo.cod_grupo
                                          AND especie.cod_natureza = grupo.cod_natureza
                                   INNER JOIN patrimonio.bem
                                           ON bem.cod_especie  = especie.cod_especie
                                          AND bem.cod_grupo    = especie.cod_grupo
                                          AND bem.cod_natureza = especie.cod_natureza
                                   LEFT JOIN contabilidade.plano_analitica
                                          ON plano_analitica.cod_plano = grupo_plano_analitica.cod_plano
                                         AND plano_analitica.exercicio = grupo_plano_analitica.exercicio
                                  LEFT JOIN contabilidade.plano_conta
                                         ON plano_conta.cod_conta = plano_analitica.cod_conta
                                        AND plano_conta.exercicio = plano_analitica.exercicio
                                   
                                   GROUP BY cod_bem
                                          , grupo_plano_analitica.cod_plano
                                          , grupo_plano_analitica.exercicio
                                          , plano_conta.cod_estrutural
                                          , plano_conta.nom_conta        
                                    ) AS grupo_plano_analitica
                                  ON grupo_plano_analitica.cod_bem = bem_comprado.cod_bem
                               WHERE 1 = 1 
                            ) AS estrutural
                          ON estrutural.cod_bem = bem_comprado.cod_bem
                                
                   LEFT JOIN (SELECT *
			        FROM patrimonio.depreciacao
                               WHERE depreciacao.dt_depreciacao BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                            AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                 AND depreciacao.cod_depreciacao = (SELECT max(cod_depreciacao) 
                                                                      FROM patrimonio.depreciacao AS PR 
                                                                     WHERE PR.dt_depreciacao BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                       AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy'))
                                                            AND NOT EXISTS ( SELECT 1 
                                                                               FROM patrimonio.depreciacao_anulada
                                                                              WHERE depreciacao_anulada.cod_depreciacao = depreciacao.cod_depreciacao
                                                                                AND depreciacao_anulada.cod_bem         = depreciacao.cod_bem
                                                                                AND depreciacao_anulada.timestamp       = depreciacao.timestamp
                                                                           )	
                                                                    
                            ) AS depreciacao
                          ON depreciacao.cod_bem = bem.cod_bem
                   LEFT JOIN (SELECT *
                                FROM patrimonio.reavaliacao
                               WHERE reavaliacao.dt_reavaliacao BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                       AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                 AND reavaliacao.cod_reavaliacao = (SELECT max(cod_reavaliacao) 
                                                                      FROM patrimonio.reavaliacao AS PR 
                                                                     WHERE PR.dt_reavaliacao BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                       AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy') ) 
                            ) AS reavaliacao
                          ON reavaliacao.cod_bem = bem.cod_bem
                          
                   LEFT JOIN patrimonio.bem_baixado
                          ON bem_baixado.cod_bem = bem.cod_bem
                         AND bem_baixado.dt_baixa BETWEEN to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                         
                       WHERE bem_comprado.cod_entidade = 2
                         AND bem.dt_aquisicao < to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                ) AS tabela
              
         ORDER BY num_bem ";
        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
     }
}
?>
