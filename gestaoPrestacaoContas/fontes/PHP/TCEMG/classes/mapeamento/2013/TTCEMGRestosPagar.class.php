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
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: LUCAS STEPHANOU

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGORestosPagar.class.php 56934 2014-01-08 19:46:44Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGRestosPagar extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGRestosPagar()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
      //VALIDACAO PARA TRAZER SO OS RESTOS A PAGAR SE O MES FOR JANEIRO
      if ($this->getDado('mes') == 1) {
        $stSql = "
                    SELECT
                              10 AS tipo_registro
                            , empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
                            , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                            , CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
										THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                   ELSE CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN LPAD(orgao.num_unidade::VARCHAR,5,'0')
											 ELSE LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
										END
							  END AS cod_unidade
                            , CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN LPAD(orgao.num_unidade::VARCHAR,5,'0')
                                   ELSE LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
                                   END AS cod_unidade_orig
                            , empenho.cod_empenho AS num_empenho
                            , TO_CHAR (empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
                            , empenho.exercicio as exercicio_empenho
                            , CASE WHEN restos_pre_empenho.cod_pre_empenho IS NOT NULL THEN
											CASE WHEN empenho.exercicio > '2012' THEN ''
												 ELSE LPAD(restos_pre_empenho.cod_funcao::VARCHAR,2,'0')||LPAD(restos_pre_empenho.cod_subfuncao::VARCHAR,3,'0')||LPAD(restos_pre_empenho.cod_programa::VARCHAR,4,'0')||LPAD(restos_pre_empenho.num_pao::VARCHAR,4,'0')||LPAD(restos_pre_empenho.cod_estrutural,8,'0')
											END	
                              END AS dot_orig
                            , REPLACE((SELECT COALESCE(SUM(item_pre_empenho.vl_total),0.00) FROM empenho.item_pre_empenho
                                WHERE item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho AND item_pre_empenho.exercicio = pre_empenho.exercicio)::varchar,'.',',') AS vl_original
                            , REPLACE(restos_pagar.valor_processado_nao_pago::varchar,'.',',') AS vl_saldo_ant_proc
                            , REPLACE(COALESCE((restos_pagar.valor_nao_processado_exercicio_anterior),0.00)::varchar,'.',',') AS vl_saldo_ant_nao

                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

                    LEFT JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                    
                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                    JOIN ( SELECT * FROM (
                                            SELECT  despesa.num_orgao
                                                    , despesa.num_unidade
                                                    , despesa.exercicio
                                                    , pre_empenho_despesa.cod_pre_empenho
                                            FROM empenho.pre_empenho_despesa
                                            JOIN orcamento.despesa
                                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                            JOIN orcamento.conta_despesa
                                              ON conta_despesa.exercicio = despesa.exercicio
                                             AND conta_despesa.cod_conta = despesa.cod_conta
                                            UNION
                                            SELECT  restos_pre_empenho.num_orgao
                                                    , restos_pre_empenho.num_unidade
                                                    , restos_pre_empenho.exercicio
                                                    , restos_pre_empenho.cod_pre_empenho
                                            FROM empenho.restos_pre_empenho
                                        ) as tbl
                                GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
                        ) AS orgao
                      ON orgao.exercicio = pre_empenho.exercicio
                     AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN (SELECT
                                  despesa.*
                                , conta_despesa.cod_estrutural
                                , pre_empenho_despesa.cod_pre_empenho
                            FROM empenho.pre_empenho_despesa
                            JOIN orcamento.despesa
                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = despesa.exercicio
                             AND conta_despesa.cod_conta = despesa.cod_conta
                        )   AS  despesa
                      ON despesa.exercicio = pre_empenho.exercicio
                     AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN tcemg.uniorcam
                      ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao 
                     AND uniorcam.exercicio   = restos_pre_empenho.exercicio
                     AND uniorcam.num_orgao_atual IS NOT NULL
		    
		     
               LEFT JOIN (SELECT *
                               , COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
							FROM tcemg.fn_restos_pagar	(
															'".$this->getDado('exercicio')."',
															'".$this->getDado('entidades')."',
															".$this->getDado('mes')."
														) as rp (
															cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
															valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
															valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
															valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
														)
                        ) AS restos_pagar
                      ON restos_pagar.cod_empenho = empenho.cod_empenho
                     AND restos_pagar.cod_entidade = empenho.cod_entidade
                     AND restos_pagar.exercicio = empenho.exercicio
                     
               LEFT JOIN ( SELECT *
							 FROM administracao.configuracao
                            WHERE configuracao.exercicio = '".($this->getDado('exercicio'))."'
                              AND configuracao.cod_modulo = 2
                              AND configuracao.parametro = 'cnpj'
                        ) AS cnpj_prefeitura
                      ON cnpj_prefeitura.exercicio = '".($this->getDado('exercicio'))."'
					  
			   LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
								, retorno.empenho AS cod_empenho 
								, retorno.exercicio  
								, sum(retorno.valor) AS vl_pago_exercicio_atual                                               
							 FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
								( ''                      
								, ''                      
								, '01/01/".$this->getDado('exercicio')."'
								, '31/12/".$this->getDado('exercicio')."'
								, '".$this->getDado('entidades')."'
								, ''
								, ''
								, ''
								, ''
								, ''
								, ''
								, ''
								, '1'
								, ''
								, ''
								, ''
								, ''
								) AS retorno(      
									 entidade            integer,                             
									 empenho             integer,                             
									 exercicio           char(4),                             
									 credor              varchar,                             
									 cod_estrutural      varchar,                             
									 cod_nota            integer,                             
									 data                text,                                
									 conta               integer,                             
									 banco               varchar,                             
									 valor               numeric                              
									 )									 
						 GROUP BY retorno.entidade
								, retorno.empenho  
								, retorno.exercicio 
                        ) AS pagamento_restos                        
                      ON pagamento_restos.cod_empenho = empenho.cod_empenho
                     AND pagamento_restos.cod_entidade = empenho.cod_entidade
                     AND pagamento_restos.exercicio = empenho.exercicio

				   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                     AND (	(pre_empenho.implantado = true AND pre_empenho.exercicio = '".($this->getDado('exercicio')-1)."' )
						 OR	(pagamento_restos.vl_pago_exercicio_atual > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
						 OR	(restos_pagar.valor_processado_nao_pago > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
						 OR	(restos_pagar.valor_nao_processado_exercicio_anterior > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
						 )
              ";

      }
      return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
      //VALIDACAO PARA TRAZER SO OS RESTOS A PAGAR SE O MES FOR JANEIRO
      if ($this->getDado('mes') == 1) {
        $stSql = "
                    SELECT
                              11 AS tipo_registro
                            , empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
                            , CASE WHEN pre_empenho.implantado = TRUE THEN restos_pre_empenho.recurso::VARCHAR
                                   ELSE recurso.cod_fonte
                              END AS cod_fonte_recurso
                            , REPLACE((SELECT COALESCE(SUM(item_pre_empenho.vl_total),0.00)
										 FROM empenho.item_pre_empenho
										WHERE item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
										  AND item_pre_empenho.exercicio = pre_empenho.exercicio)::varchar,'.',',')
							  AS vl_fonte
                            , REPLACE(restos_pagar.valor_processado_nao_pago::varchar,'.',',') AS vl_saldo_ant_proc
                            , REPLACE(COALESCE((restos_pagar.valor_nao_processado_exercicio_anterior),0.00)::varchar,'.',',') AS vl_saldo_ant_nao

                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

               LEFT JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                     AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                    JOIN ( SELECT * FROM (
                                            SELECT  despesa.num_orgao
                                                    , despesa.num_unidade
                                                    , despesa.exercicio
                                                    , pre_empenho_despesa.cod_pre_empenho
                                            FROM empenho.pre_empenho_despesa
                                            JOIN orcamento.despesa
                                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                            JOIN orcamento.conta_despesa
                                              ON conta_despesa.exercicio = despesa.exercicio
                                             AND conta_despesa.cod_conta = despesa.cod_conta
                                            UNION
                                            SELECT  restos_pre_empenho.num_orgao
                                                    , restos_pre_empenho.num_unidade
                                                    , restos_pre_empenho.exercicio
                                                    , restos_pre_empenho.cod_pre_empenho
                                            FROM empenho.restos_pre_empenho
                                        ) as tbl
                                GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
                        ) AS orgao
                      ON orgao.exercicio = pre_empenho.exercicio
                     AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN (SELECT
                                    despesa.*
                                    , conta_despesa.cod_estrutural
                                    , pre_empenho_despesa.cod_pre_empenho
                            FROM empenho.pre_empenho_despesa
                            JOIN orcamento.despesa
                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = despesa.exercicio
                             AND conta_despesa.cod_conta = despesa.cod_conta
                        ) AS  despesa
                      ON despesa.exercicio = pre_empenho.exercicio
                     AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    LEFT JOIN orcamento.recurso
                      ON recurso.exercicio = despesa.exercicio
                     AND recurso.cod_recurso = despesa.cod_recurso

                    LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

                    LEFT JOIN (SELECT *
									, COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
								 FROM tcemg.fn_restos_pagar	(
                                                                '".$this->getDado('exercicio')."',
                                                                '".$this->getDado('entidades')."',
                                                                ".$this->getDado('mes')."
                                                            ) as rp (
                                                                cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
                                                                valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
                                                                valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
                                                                valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
                                                            )
                        ) AS restos_pagar
                      ON restos_pagar.cod_empenho = empenho.cod_empenho
                     AND restos_pagar.cod_entidade = empenho.cod_entidade
                     AND restos_pagar.exercicio = empenho.exercicio
					 
					LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
								, retorno.empenho AS cod_empenho 
								, retorno.exercicio  
								, sum(retorno.valor) AS vl_pago_exercicio_atual                                               
							 FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
								( ''                      
								, ''                      
								, '01/01/".$this->getDado('exercicio')."'
								, '31/12/".$this->getDado('exercicio')."'
								, '".$this->getDado('entidades')."'
								, ''
								, ''
								, ''
								, ''
								, ''
								, ''
								, ''
								, '1'
								, ''
								, ''
								, ''
								, ''
								) AS retorno(      
									 entidade            integer,                             
									 empenho             integer,                             
									 exercicio           char(4),                             
									 credor              varchar,                             
									 cod_estrutural      varchar,                             
									 cod_nota            integer,                             
									 data                text,                                
									 conta               integer,                             
									 banco               varchar,                             
									 valor               numeric                              
									 )									 
						 GROUP BY retorno.entidade
								, retorno.empenho  
								, retorno.exercicio 
                        ) AS pagamento_restos                        
                      ON pagamento_restos.cod_empenho = empenho.cod_empenho
                     AND pagamento_restos.cod_entidade = empenho.cod_entidade
                     AND pagamento_restos.exercicio = empenho.exercicio

                   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                     AND (	(pre_empenho.implantado = true AND pre_empenho.exercicio = '".($this->getDado('exercicio')-1)."' )
						 OR	(pagamento_restos.vl_pago_exercicio_atual > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
						 OR	(restos_pagar.valor_processado_nao_pago > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
						 OR	(restos_pagar.valor_nao_processado_exercicio_anterior > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
						 )
          ";
      
      }

      return $stSql;
    }

    public function recuperaExportacao12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao12()
    {
        $stSql = "
                    SELECT
                              12 AS tipo_registro
                            , empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
                            , CASE WHEN trim(sw_pais.nom_pais) <> 'Brasil' THEN 3
                                   WHEN sw_cgm_pessoa_fisica.cpf <> '' THEN 1
                                   WHEN sw_cgm_pessoa_juridica.cnpj <> '' THEN 2
                                   ELSE 1
                              END AS tipo_documento
                            , CASE WHEN sw_cgm_pessoa_fisica.cpf <> '' THEN sw_cgm_pessoa_fisica.cpf
                                   WHEN sw_cgm_pessoa_juridica.cnpj <> '' THEN sw_cgm_pessoa_juridica.cnpj
                                   ELSE '00000000000000'
                              END AS num_documento
                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

                    JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                     AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                    LEFT JOIN (  SELECT pre_empenho.exercicio
                                 , pre_empenho.cod_pre_empenho
                                 , CASE WHEN ( pre_empenho.implantado = true )
                                        THEN restos_pre_empenho.num_orgao
                                        ELSE despesa.num_orgao
                                   END AS num_orgao
                                 , CASE WHEN ( pre_empenho.implantado = true )
                                        THEN restos_pre_empenho.num_unidade
                                        ELSE despesa.num_unidade
                                   END AS num_unidade
                                 , conta_despesa.cod_estrutural
                                 , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 9, 2) IN ('01','03','04','05','09','11','16','48','94')
                                        THEN true
								   WHEN ''||SUBSTR(conta_despesa.cod_estrutural, 9, 2)||SUBSTR(conta_despesa.cod_estrutural, 12, 2) IN ('3626','3628','3699')
										THEN true
								   WHEN SUBSTR(conta_despesa.cod_estrutural, 0, 14) IN ('3.1.9.0.92.01','3.1.9.0.92.02','3.1.7.1.92.01','3.1.7.1.92.02','3.1.9.1.92.01','3.1.9.1.92.02','3.3.9.0.36.07')
										THEN true
										ELSE false
								   END AS folha
                              FROM empenho.pre_empenho
                         LEFT JOIN empenho.restos_pre_empenho
                                ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                               AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN empenho.pre_empenho_despesa
                                ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                               AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN orcamento.despesa
                                ON despesa.exercicio = pre_empenho_despesa.exercicio
                               AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              JOIN orcamento.conta_despesa
								ON (    conta_despesa.exercicio = despesa.exercicio
									AND conta_despesa.cod_conta = despesa.cod_conta
								   )
								OR (    replace( conta_despesa.cod_estrutural, '.', '') = restos_pre_empenho.cod_estrutural
                                    AND conta_despesa.exercicio = restos_pre_empenho.exercicio
                                   )
						 ) AS orgao
					  ON orgao.exercicio = pre_empenho.exercicio
					 AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN sw_cgm
                      ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                    JOIN sw_pais
                      ON sw_pais.cod_pais = sw_cgm.cod_pais

               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

               LEFT JOIN (	   SELECT *
									, COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
								 FROM tcemg.fn_restos_pagar	(
                                                                '".$this->getDado('exercicio')."',
                                                                '".$this->getDado('entidades')."',
                                                                ".$this->getDado('mes')."
                                                            ) as rp (
                                                                cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
                                                                valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
                                                                valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
                                                                valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
                                                            )
                        ) AS restos_pagar
                      ON restos_pagar.cod_empenho = empenho.cod_empenho
                     AND restos_pagar.cod_entidade = empenho.cod_entidade
                     AND restos_pagar.exercicio = empenho.exercicio
					 
		       LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
								, retorno.empenho AS cod_empenho 
								, retorno.exercicio  
								, sum(retorno.valor) AS vl_pago_exercicio_atual                                               
							 FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
								( ''                      
								, ''                      
								, '01/01/".$this->getDado('exercicio')."'
								, '31/12/".$this->getDado('exercicio')."'
								, '".$this->getDado('entidades')."'
								, ''
								, ''
								, ''
								, ''
								, ''
								, ''
								, ''
								, '1'
								, ''
								, ''
								, ''
								, ''
								) AS retorno(      
									 entidade            integer,                             
									 empenho             integer,                             
									 exercicio           char(4),                             
									 credor              varchar,                             
									 cod_estrutural      varchar,                             
									 cod_nota            integer,                             
									 data                text,                                
									 conta               integer,                             
									 banco               varchar,                             
									 valor               numeric                              
									 )									 
						 GROUP BY retorno.entidade
								, retorno.empenho  
								, retorno.exercicio 
                        ) AS pagamento_restos                        
                      ON pagamento_restos.cod_empenho = empenho.cod_empenho
                     AND pagamento_restos.cod_entidade = empenho.cod_entidade
                     AND pagamento_restos.exercicio = empenho.exercicio


                   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
					 AND (
						 	(pagamento_restos.vl_pago_exercicio_atual > 0.00)
						 OR	(restos_pagar.valor_processado_nao_pago > 0.00)
						 OR	(restos_pagar.valor_nao_processado_exercicio_anterior > 0.00)
						 )
					 AND pre_empenho.exercicio < '2013'
					 AND empenho.exercicio < '2013'
					 AND (sw_cgm_pessoa_fisica.cpf <> '' OR sw_cgm_pessoa_juridica.cnpj <> '')
        ";

        return $stSql;
    }

    public function recuperaExportacao20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao20()
    {
        $stSql = "
                     SELECT
                            20 AS tipo_registro
                          , cod_reduzido
                          , cod_orgao
						  , cod_unidade
                          , cod_unidade_orig
                          , num_empenho
                          , exercicio_empenho
                          , dt_empenho
                          , tipo_restos_pagar
                          , tipo_movimento
                          , dt_movimentacao
                          , dot_orig
                          , REPLACE(SUM(vl_movimentacao)::varchar,'.',',') AS vl_movimentacao
                          , cod_orgao_encamp_atrib
                          , cod_unidade_sub_encamp_atrib
                          , justificativa
                          , ato_cancelamento
                          , data_ato_cancelamento
					   FROM	(
						 SELECT
                                empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR||substring(replace(replace(empenho_anulado.timestamp::VARCHAR,'-',''),' ',''),5,6) AS cod_reduzido
                              , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                              , CASE WHEN uniorcam.num_orgao_atual IS NOT NULL THEN
												LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
									 ELSE CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN
													LPAD(orgao.num_unidade::VARCHAR,5,'0')
											   ELSE
													LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
										  END
								END AS cod_unidade
							  , CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN
												LPAD(orgao.num_unidade::VARCHAR,5,'0')
									 ELSE
												LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
								END AS cod_unidade_orig
                              , empenho.cod_empenho AS num_empenho
                              , empenho.exercicio AS exercicio_empenho
                              , TO_CHAR(empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
                              , 1 AS tipo_restos_pagar
                              , 1 tipo_movimento
                              , TO_CHAR(empenho_anulado.timestamp, 'ddmmyyyy') AS dt_movimentacao
                              , '' AS dot_orig
                              , empenho_anulado.vl_anulado AS vl_movimentacao
                              , '' AS cod_orgao_encamp_atrib
                              , '' AS cod_unidade_sub_encamp_atrib
                              , empenho_anulado.motivo AS justificativa
                              , '' ato_cancelamento
                              , '' AS data_ato_cancelamento

						   FROM empenho.empenho
   
						   JOIN empenho.pre_empenho
							 ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
							AND pre_empenho.exercicio = empenho.exercicio
   
					  LEFT JOIN empenho.restos_pre_empenho
							 ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
							AND restos_pre_empenho.exercicio = pre_empenho.exercicio
							 
						   JOIN administracao.configuracao_entidade
							 ON configuracao_entidade.cod_entidade = empenho.cod_entidade
							AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
							AND configuracao_entidade.cod_modulo = 55
							AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
   
						   JOIN ( SELECT * FROM (
												   SELECT  despesa.num_orgao
														   , despesa.num_unidade
														   , despesa.exercicio
														   , pre_empenho_despesa.cod_pre_empenho
													 FROM empenho.pre_empenho_despesa
											   INNER JOIN orcamento.despesa
													   ON despesa.exercicio = pre_empenho_despesa.exercicio
													  AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
											   INNER JOIN orcamento.conta_despesa
													   ON conta_despesa.exercicio = despesa.exercicio
													  AND conta_despesa.cod_conta = despesa.cod_conta
												   UNION
												   SELECT  restos_pre_empenho.num_orgao
														   , restos_pre_empenho.num_unidade
														   , restos_pre_empenho.exercicio
														   , restos_pre_empenho.cod_pre_empenho
													 FROM empenho.restos_pre_empenho
											   ) as tbl
									   GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
							    ) AS orgao
							 ON orgao.exercicio = pre_empenho.exercicio
						    AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho
   
					  LEFT JOIN tcemg.uniorcam
							 ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
						    AND uniorcam.exercicio = restos_pre_empenho.exercicio
						    AND uniorcam.num_orgao_atual IS NOT NULL
   
						   JOIN ( SELECT empenho_anulado_item.exercicio
									   , empenho_anulado_item.cod_empenho
									   , empenho_anulado_item.cod_entidade
									   , empenho_anulado_item.vl_anulado
									   , empenho_anulado_item.timestamp
									   , empenho_anulado.motivo
									FROM empenho.empenho_anulado_item
									JOIN empenho.empenho_anulado
									  ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
									 AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
									 AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
									 AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
								   WHERE empenho_anulado_item.exercicio < '".$this->getDado('exercicio')."'
								     AND empenho_anulado_item.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd') AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
								) AS empenho_anulado
							 ON empenho_anulado.exercicio = empenho.exercicio
							AND empenho_anulado.cod_entidade = empenho.cod_entidade
							AND empenho_anulado.cod_empenho = empenho.cod_empenho
   
					  LEFT JOIN ( SELECT *
									   , COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
									FROM tcemg.fn_restos_pagar (
																	'".$this->getDado('exercicio')."',
																	'".$this->getDado('entidades')."',
																	".$this->getDado('mes')."
															   ) as rp (
																	cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
																	valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
																	valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
																	valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
															   )
							    ) AS restos_pagar
							 ON restos_pagar.cod_empenho = empenho.cod_empenho
							AND restos_pagar.cod_entidade = empenho.cod_entidade
							AND restos_pagar.exercicio = empenho.exercicio
							
					  LEFT JOIN ( SELECT * 
									FROM administracao.configuracao
								   WHERE configuracao.exercicio = '".$this->getDado('exercicio')."'
									 AND configuracao.cod_modulo = 2
									 AND configuracao.parametro = 'cnpj'
								) AS cnpj_prefeitura
							 ON cnpj_prefeitura.exercicio = '".$this->getDado('exercicio')."'
							  
					  LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
									   , retorno.empenho AS cod_empenho 
									   , retorno.exercicio  
									   , sum(retorno.valor) AS vl_pago_exercicio_atual                                               
									FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
									   ( ''                      
									   , ''                      
									   , '01/01/".$this->getDado('exercicio')."'
									   , '31/12/".$this->getDado('exercicio')."'
									   , '".$this->getDado('entidades')."'
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , '1'
									   , ''
									   , ''
									   , ''
									   , ''
									   ) AS retorno(      
											entidade            integer,                             
											empenho             integer,                             
											exercicio           char(4),                             
											credor              varchar,                             
											cod_estrutural      varchar,                             
											cod_nota            integer,                             
											data                text,                                
											conta               integer,                             
											banco               varchar,                             
											valor               numeric                              
											)									 
								GROUP BY retorno.entidade
									   , retorno.empenho  
									   , retorno.exercicio 
							    ) AS pagamento_restos                        
							 ON pagamento_restos.cod_empenho = empenho.cod_empenho
							AND pagamento_restos.cod_entidade = empenho.cod_entidade
							AND pagamento_restos.exercicio = empenho.exercicio
							  
							  
						  WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
							AND (	(pre_empenho.implantado = true AND pre_empenho.exercicio = '".($this->getDado('exercicio')-1)."' )
								OR	(pagamento_restos.vl_pago_exercicio_atual > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								OR	(restos_pagar.valor_processado_nao_pago > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								OR	(restos_pagar.valor_nao_processado_exercicio_anterior > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								)
							AND (	valor_processado_cancelado  > 0.00 )
   
						  UNION
   
						 SELECT
                                empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
							  , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
							  , CASE WHEN uniorcam.num_orgao_atual IS NOT NULL THEN
												LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
									 ELSE CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN
													LPAD(orgao.num_unidade::VARCHAR,5,'0')
											   ELSE
													LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
										  END
								END AS cod_unidade
							  , CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN
												LPAD(orgao.num_unidade::VARCHAR,5,'0')
									 ELSE
												LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
								END AS cod_unidade_orig
							  , empenho.cod_empenho AS num_empenho
							  , empenho.exercicio AS exercicio_empenho
							  , TO_CHAR(empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
							  , 2 AS tipo_restos_pagar
							  , 1 tipo_movimento
							  , TO_CHAR(empenho_anulado.timestamp, 'ddmmyyyy') AS dt_movimentacao
							  , '' AS dot_orig
							  , empenho_anulado.vl_anulado AS vl_movimentacao
							  , '' AS cod_orgao_encamp_atrib
							  , '' AS cod_unidade_sub_encamp_atrib
							  , empenho_anulado.motivo AS justificativa
							  , '' ato_cancelamento
							  , '' AS data_ato_cancelamento
   
						   FROM empenho.empenho
   
						   JOIN empenho.pre_empenho
							 ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
							AND pre_empenho.exercicio = empenho.exercicio
   
					  LEFT JOIN empenho.restos_pre_empenho
							 ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
							AND restos_pre_empenho.exercicio = pre_empenho.exercicio
							 
						   JOIN administracao.configuracao_entidade
							 ON configuracao_entidade.cod_entidade = empenho.cod_entidade
							AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
							AND configuracao_entidade.cod_modulo = 55
							AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
   
						   JOIN ( SELECT * FROM (
												   SELECT  despesa.num_orgao
														   , despesa.num_unidade
														   , despesa.exercicio
														   , pre_empenho_despesa.cod_pre_empenho
													 FROM empenho.pre_empenho_despesa
											   INNER JOIN orcamento.despesa
													   ON despesa.exercicio = pre_empenho_despesa.exercicio
													  AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
											   INNER JOIN orcamento.conta_despesa
													   ON conta_despesa.exercicio = despesa.exercicio
													  AND conta_despesa.cod_conta = despesa.cod_conta
												   UNION
												   SELECT  restos_pre_empenho.num_orgao
														   , restos_pre_empenho.num_unidade
														   , restos_pre_empenho.exercicio
														   , restos_pre_empenho.cod_pre_empenho
													 FROM empenho.restos_pre_empenho
											    ) as tbl
									   GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
							    ) AS orgao
							 ON orgao.exercicio = pre_empenho.exercicio
							AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho
   
					  LEFT JOIN tcemg.uniorcam
							 ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
							AND uniorcam.exercicio = restos_pre_empenho.exercicio
							AND uniorcam.num_orgao_atual IS NOT NULL
   
						   JOIN ( SELECT empenho_anulado_item.exercicio
									   , empenho_anulado_item.cod_empenho
									   , empenho_anulado_item.cod_entidade
									   , empenho_anulado_item.vl_anulado
									   , empenho_anulado_item.timestamp
									   , empenho_anulado.motivo
									FROM empenho.empenho_anulado_item
									JOIN empenho.empenho_anulado
									  ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
									 AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
									 AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
									 AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
								   WHERE empenho_anulado_item.exercicio < '".$this->getDado('exercicio')."'
									 AND empenho_anulado_item.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd') AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
								) AS empenho_anulado
							 ON empenho_anulado.exercicio = empenho.exercicio
							AND empenho_anulado.cod_entidade = empenho.cod_entidade
							AND empenho_anulado.cod_empenho = empenho.cod_empenho
   
					  LEFT JOIN ( SELECT *
									   , COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
									FROM tcemg.fn_restos_pagar (
																	'".$this->getDado('exercicio')."',
																	'".$this->getDado('entidades')."',
																	".$this->getDado('mes')."
															   ) as rp (
																	cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
																	valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
																	valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
																	valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
															   )
							    ) AS restos_pagar
							 ON restos_pagar.cod_empenho = empenho.cod_empenho
							AND restos_pagar.cod_entidade = empenho.cod_entidade
							AND restos_pagar.exercicio = empenho.exercicio
   
					  LEFT JOIN ( SELECT * 
									FROM administracao.configuracao
								   WHERE configuracao.exercicio = '".$this->getDado('exercicio')."'
									 AND configuracao.cod_modulo = 2
									 AND configuracao.parametro = 'cnpj'
								) AS cnpj_prefeitura
							 ON cnpj_prefeitura.exercicio = '".$this->getDado('exercicio')."'
							  
					  LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
									   , retorno.empenho AS cod_empenho 
									   , retorno.exercicio  
									   , sum(retorno.valor) AS vl_pago_exercicio_atual                                               
									FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
									   ( ''                      
									   , ''                      
									   , '01/01/".$this->getDado('exercicio')."'
									   , '31/12/".$this->getDado('exercicio')."'
									   , '".$this->getDado('entidades')."'
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , '1'
									   , ''
									   , ''
									   , ''
									   , ''
									   ) AS retorno(      
											entidade            integer,                             
											empenho             integer,                             
											exercicio           char(4),                             
											credor              varchar,                             
											cod_estrutural      varchar,                             
											cod_nota            integer,                             
											data                text,                                
											conta               integer,                             
											banco               varchar,                             
											valor               numeric                              
											)									 
								GROUP BY retorno.entidade
									   , retorno.empenho  
									   , retorno.exercicio 
							    ) AS pagamento_restos                        
							 ON pagamento_restos.cod_empenho = empenho.cod_empenho
							AND pagamento_restos.cod_entidade = empenho.cod_entidade
							AND pagamento_restos.exercicio = empenho.exercicio
							  
						  WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
							AND (	(pre_empenho.implantado = true AND pre_empenho.exercicio = '".($this->getDado('exercicio')-1)."' )
								OR	(pagamento_restos.vl_pago_exercicio_atual > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								OR	(restos_pagar.valor_processado_nao_pago > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								OR	(restos_pagar.valor_nao_processado_exercicio_anterior > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								)
							AND (	valor_nao_processado_cancelado > 0.00 )
							) AS tabela
				   GROUP BY cod_reduzido
		    			  , cod_orgao
		    			  , cod_unidade
		    			  , cod_unidade_orig
		    			  , num_empenho
		    			  , exercicio_empenho
		    			  , dt_empenho
		    			  , tipo_restos_pagar
		    			  , tipo_movimento
		    			  , dt_movimentacao
		    			  , dot_orig
		    			  , cod_orgao_encamp_atrib
		    			  , cod_unidade_sub_encamp_atrib
		    			  , justificativa
		    			  , ato_cancelamento
		    			  , data_ato_cancelamento
				   ORDER BY num_empenho
        ";
        
        return $stSql;
    }

    public function recuperaExportacao21(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao21",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao21()
    {
        $stSql = "
                    SELECT tipo_registro
					     , cod_reduzido
						 , cod_fonte_recurso
						 , REPLACE(SUM(vl_movimentacao_fonte)::varchar,'.',',') AS vl_movimentacao_fonte
						 , cod_empenho
						 , exercicio
						 , dt_movimentacao
					  FROM (
								SELECT
									21 AS tipo_registro
									, empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
									, CASE WHEN pre_empenho.implantado = TRUE THEN restos_pre_empenho.recurso::VARCHAR
										   ELSE recurso.cod_fonte
									  END AS cod_fonte_recurso
									, empenho_anulado.vl_anulado AS vl_movimentacao_fonte
									, empenho.cod_empenho
									, empenho.exercicio
									, TO_CHAR(empenho_anulado.timestamp, 'ddmmyyyy') AS dt_movimentacao
		
							FROM empenho.empenho
		
							JOIN empenho.pre_empenho
							  ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
							 AND pre_empenho.exercicio = empenho.exercicio
		
							JOIN administracao.configuracao_entidade
							  ON configuracao_entidade.cod_entidade = empenho.cod_entidade
							 AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
							 AND configuracao_entidade.cod_modulo = 55
							 AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
		
							JOIN ( SELECT * FROM (
													SELECT  despesa.num_orgao
															, despesa.num_unidade
															, despesa.exercicio
															, pre_empenho_despesa.cod_pre_empenho
													FROM empenho.pre_empenho_despesa
													JOIN orcamento.despesa
													  ON despesa.exercicio = pre_empenho_despesa.exercicio
													 AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
													JOIN orcamento.conta_despesa
													  ON conta_despesa.exercicio = despesa.exercicio
													 AND conta_despesa.cod_conta = despesa.cod_conta
													UNION
													SELECT  restos_pre_empenho.num_orgao
															, restos_pre_empenho.num_unidade
															, restos_pre_empenho.exercicio
															, restos_pre_empenho.cod_pre_empenho
													FROM empenho.restos_pre_empenho
												) as tbl
										GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
								) AS orgao
							  ON orgao.exercicio = pre_empenho.exercicio
							 AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho
		
							JOIN tcemg.uniorcam
							  ON uniorcam.num_orgao = orgao.num_orgao
							 AND uniorcam.num_unidade = orgao.num_unidade
							 AND uniorcam.exercicio = orgao.exercicio
		
							JOIN ( SELECT empenho_anulado_item.exercicio
									   , empenho_anulado_item.cod_empenho
									   , empenho_anulado_item.cod_entidade
									   , empenho_anulado_item.vl_anulado
									   , empenho_anulado_item.timestamp
									   , empenho_anulado.motivo
									FROM empenho.empenho_anulado_item
									JOIN empenho.empenho_anulado
									  ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
									 AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
									 AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
									 AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
								   WHERE empenho_anulado_item.exercicio < '".$this->getDado('exercicio')."'
								     AND empenho_anulado_item.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd') AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
								 ) AS empenho_anulado
							  ON empenho_anulado.exercicio = empenho.exercicio
							 AND empenho_anulado.cod_entidade = empenho.cod_entidade
							 AND empenho_anulado.cod_empenho = empenho.cod_empenho
		
					   LEFT JOIN empenho.restos_pre_empenho 
							  ON restos_pre_empenho.exercicio = pre_empenho.exercicio
							 AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
							 
					   LEFT JOIN (SELECT
                                         despesa.*
                                       , conta_despesa.cod_estrutural
									   , pre_empenho_despesa.cod_pre_empenho
									FROM empenho.pre_empenho_despesa
									JOIN orcamento.despesa
									  ON despesa.exercicio = pre_empenho_despesa.exercicio
									 AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
									JOIN orcamento.conta_despesa
									  ON conta_despesa.exercicio = despesa.exercicio
									 AND conta_despesa.cod_conta = despesa.cod_conta
								 ) AS despesa
							  ON despesa.exercicio = pre_empenho.exercicio
							 AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

					   LEFT JOIN orcamento.recurso
							  ON recurso.exercicio = despesa.exercicio
							 AND recurso.cod_recurso = despesa.cod_recurso

					   LEFT JOIN empenho.pre_empenho_despesa
							  ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
							 AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
		
					   LEFT JOIN ( SELECT *
									   , COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
									FROM tcemg.fn_restos_pagar (
																	'".$this->getDado('exercicio')."',
																	'".$this->getDado('entidades')."',
																	".$this->getDado('mes')."
															   ) as rp (
																	cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
																	valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
																	valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
																	valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
															   )
							     ) AS restos_pagar
							  ON restos_pagar.cod_empenho = empenho.cod_empenho
							 AND restos_pagar.cod_entidade = empenho.cod_entidade
							 AND restos_pagar.exercicio = empenho.exercicio
					
					   LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
									   , retorno.empenho AS cod_empenho 
									   , retorno.exercicio  
									   , sum(retorno.valor) AS vl_pago_exercicio_atual                                               
									FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
									   ( ''                      
									   , ''                      
									   , '01/01/".$this->getDado('exercicio')."'
									   , '31/12/".$this->getDado('exercicio')."'
									   , '".$this->getDado('entidades')."'
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , ''
									   , '1'
									   , ''
									   , ''
									   , ''
									   , ''
									   ) AS retorno(      
											entidade            integer,                             
											empenho             integer,                             
											exercicio           char(4),                             
											credor              varchar,                             
											cod_estrutural      varchar,                             
											cod_nota            integer,                             
											data                text,                                
											conta               integer,                             
											banco               varchar,                             
											valor               numeric                              
											)									 
								GROUP BY retorno.entidade
									   , retorno.empenho  
									   , retorno.exercicio 
							     ) AS pagamento_restos                        
							  ON pagamento_restos.cod_empenho = empenho.cod_empenho
							 AND pagamento_restos.cod_entidade = empenho.cod_entidade
							 AND pagamento_restos.exercicio = empenho.exercicio
		
						   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
						     AND (	(pre_empenho.implantado = true AND pre_empenho.exercicio = '".($this->getDado('exercicio')-1)."' )
								OR	(pagamento_restos.vl_pago_exercicio_atual > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								OR	(restos_pagar.valor_processado_nao_pago > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								OR	(restos_pagar.valor_nao_processado_exercicio_anterior > 0.00 AND pre_empenho.exercicio <= '".($this->getDado('exercicio')-1)."')
								)
							AND (	(valor_processado_cancelado  > 0.00)
								OR	(valor_nao_processado_cancelado  > 0.00)		    
								)
						   ) AS tabela
				  GROUP BY tipo_registro
				         , cod_reduzido
						 , cod_fonte_recurso
						 , cod_empenho
						 , exercicio
						 , dt_movimentacao
        ";

        return $stSql;
    }

    public function recuperaExportacao22(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao22",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao22()
    {
        $stSql = "
                    SELECT
                            22 AS tipo_registro
                            , empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
                            , CASE WHEN sw_pais.nom_pais = 'Brasil'
                                    THEN 3
                                    ELSE CASE WHEN sw_cgm_pessoa_fisica.cpf <> ''
                                                THEN 1
                                                ELSE CASE WHEN sw_cgm_pessoa_juridica.cnpj <> ''
                                                            THEN 2
                                                            ELSE 1
                                                    END
                                        END
                            END AS tipo_documento
                            , CASE WHEN sw_cgm_pessoa_fisica.cpf <> ''
                                                THEN sw_cgm_pessoa_fisica.cpf
                                                ELSE CASE WHEN sw_cgm_pessoa_juridica.cnpj <> ''
                                                            THEN sw_cgm_pessoa_juridica.cnpj
                                                            ELSE '00000000000000'
                                                    END
                            END AS num_documento

                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                    JOIN ( SELECT * FROM (
                                            SELECT  despesa.num_orgao
                                                    , despesa.num_unidade
                                                    , despesa.exercicio
                                                    , pre_empenho_despesa.cod_pre_empenho
                                            FROM empenho.pre_empenho_despesa
                                            JOIN orcamento.despesa
                                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                            JOIN orcamento.conta_despesa
                                              ON conta_despesa.exercicio = despesa.exercicio
                                             AND conta_despesa.cod_conta = despesa.cod_conta
                                            UNION
                                            SELECT  restos_pre_empenho.num_orgao
                                                    , restos_pre_empenho.num_unidade
                                                    , restos_pre_empenho.exercicio
                                                    , restos_pre_empenho.cod_pre_empenho
                                            FROM empenho.restos_pre_empenho
                                        ) as tbl
                                GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
                                ORDER BY tbl.exercicio, tbl.num_orgao
                        ) AS orgao
                      ON orgao.exercicio = pre_empenho.exercicio
                     AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN empenho.empenho_anulado
                      ON empenho_anulado.exercicio = empenho.exercicio
                     AND empenho_anulado.cod_entidade = empenho.cod_entidade
                     AND empenho_anulado.cod_empenho = empenho.cod_empenho

                    JOIN empenho.empenho_anulado_item
                      ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                     AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho

                    JOIN sw_cgm
                      ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                    JOIN sw_pais
                      ON sw_pais.cod_pais = sw_cgm.cod_pais

               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                    JOIN (
                            SELECT  cod_empenho, cod_entidade, exercicio,
                                    SUM(nao_processados_exercicios_anteriores) AS nao_processados_exercicios_anteriores,
                                    SUM(nao_processados_exercicio_anterior) AS nao_processados_exercicio_anterior,
                                    SUM(nao_processados_cancelado) AS nao_processados_cancelado,
                                    SUM(nao_processados_pago) AS nao_processados_pago,
                                    SUM(nao_processados_liquidado) AS nao_processados_liquidado,
                                    SUM(processados_exercicios_anteriores) AS processados_exercicios_anteriores,
                                    SUM(processados_exercicio_anterior) AS processados_exercicio_anterior,
                                    SUM(processados_cancelado) AS processados_cancelado,
                                    SUM(processados_pago) AS processados_pago
                            FROM (
                                    SELECT  cod_empenho, cod_entidade, exercicio, nao_processados_exercicios_anteriores, nao_processados_exercicio_anterior,
                                            nao_processados_cancelado, nao_processados_pago, nao_processados_liquidado, 0.00 AS processados_exercicios_anteriores,
                                            0.00 AS processados_exercicio_anterior, 0.00 AS processados_cancelado, 0.00 AS processados_pago
                                    FROM tcemg.restos_pagar_nao_processado_movimentacao(
                                                                                        '".$this->getDado('exercicio')."',
                                                                                        '".$this->getDado('entidades')."',
                                                                                        ".$this->getDado('mes')."
                                                                                    ) as rp (
                                                                                                cod_empenho integer,
                                                                                                cod_entidade integer,
                                                                                                exercicio varchar,
                                                                                                nao_processados_exercicios_anteriores numeric,
                                                                                                nao_processados_exercicio_anterior numeric,
                                                                                                nao_processados_cancelado numeric,
                                                                                                nao_processados_pago numeric,
                                                                                                nao_processados_liquidado numeric
                                                                                            )
                                    UNION
                                    SELECT  cod_empenho, cod_entidade, exercicio, 0.00 AS nao_processados_exercicios_anteriores, 0.00 AS nao_processados_exercicio_anterior,
                                            0.00 AS nao_processados_cancelado, 0.00 AS nao_processados_pago, 0.00 AS nao_processados_liquidado, processados_exercicios_anteriores,
                                            processados_exercicio_anterior, processados_cancelado, processados_pago
                                    FROM tcemg.restos_pagar_nao_processado_movimentacao(
                                                                                        '".$this->getDado('exercicio')."',
                                                                                        '".$this->getDado('entidades')."',
                                                                                        ".$this->getDado('mes')."
                                                                                    ) as rp (
                                                                                                cod_empenho integer,
                                                                                                cod_entidade integer,
                                                                                                exercicio varchar,
                                                                                                processados_exercicios_anteriores numeric,
                                                                                                processados_exercicio_anterior numeric,
                                                                                                processados_cancelado numeric,
                                                                                                processados_pago numeric,
                                                                                                nao_processados_liquidado numeric
                                                                                            )
                                ) AS restos
                            GROUP BY cod_empenho, cod_entidade, exercicio
                            ORDER BY cod_empenho, cod_entidade, exercicio
                        ) AS restos_pagar
                      ON restos_pagar.cod_empenho = empenho.cod_empenho
                     AND restos_pagar.cod_entidade = empenho.cod_entidade
                     AND restos_pagar.exercicio = empenho.exercicio

                   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
        ";

        return $stSql;
    }

        function recuperaDadosEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
        {
        return $this->executaRecupera("montaRecuperaDadosEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function __destruct(){}

}
