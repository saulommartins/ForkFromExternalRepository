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
    * Classe de mapeamento da tabela TTCEMG
    * Data de Criação: 26/02/2014

    * @author Analista: Valtair
    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGOPS extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGOPS()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosOPS10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosOPS10()
    {
        $stSql  = " SELECT    '10' AS tiporegistro
                             , LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = empenho.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0')::VARCHAR AS codorgao
                             , CASE WHEN  (pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho and pre_empenho.implantado = 't') THEN
					                           CASE WHEN ( uniorcam.num_orgao_atual IS NOT NULL) THEN
						                              LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')::VARCHAR
					                           ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')::VARCHAR
					                           END
				                        ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')::VARCHAR
				                      END AS codunidadesub
                             , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyymmddhh24mm')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop
                             , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'ddmmyyyy') AS dtpagamento
                             , sum(nota_liquidacao_paga.vl_pago) AS vlop
                             , CASE WHEN ordem_pagamento.observacao <> ''
                                    THEN trim(regexp_replace(sem_acentos(ordem_pagamento.observacao), '[º|°]', '', 'gi'))
                                    ELSE 'pagamento OP' || ordem_pagamento.cod_ordem::varchar
                             END AS especificacaoop
                             , (SELECT cpf
                                  FROM sw_cgm_pessoa_fisica
                                     WHERE sw_cgm_pessoa_fisica.numcgm = (SELECT numcgm
																		    FROM administracao.auditoria
																		   WHERE auditoria.objeto = (SELECT objeto
																									   FROM administracao.auditoria
																									  WHERE objeto = (SELECT '".chr(34)."cod_ordem".chr(34)." => ".chr(34)."'||op2.cod_ordem||'".chr(34).",".chr(34)."cod_entidade".chr(34)." => ".chr(34)."'||op2.cod_entidade||'".chr(34).",".chr(34)."exercicio".chr(34)." => ".chr(34)."'||op2.exercicio||'".chr(34)."'
																										                FROM empenho.ordem_pagamento AS op2
																													   WHERE op2.exercicio = ordem_pagamento.exercicio
																														 AND op2.cod_entidade   = ordem_pagamento.cod_entidade
																														 AND op2.cod_ordem      = ordem_pagamento.cod_ordem limit 1
																													  )
																									)
																		   --AND auditoria.cod_acao = 816
                                                                             AND substr(timestamp::varchar,1,4) = '".$this->getDado('exercicio')."'
																		  )
                            ) AS cpfresppgto

                    FROM  empenho.nota_liquidacao as nl

                    JOIN empenho.nota_liquidacao_paga
                         ON nota_liquidacao_paga.exercicio    = nl.exercicio
                        AND nota_liquidacao_paga.cod_entidade = nl.cod_entidade
                        AND nota_liquidacao_paga.cod_nota     = nl.cod_nota

                    LEFT JOIN empenho.nota_liquidacao_paga_anulada
                         ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                        AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                        AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp
                    

                    JOIN empenho.nota_liquidacao_conta_pagadora
                         ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                        AND nota_liquidacao_conta_pagadora.cod_entidade         = nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao_conta_pagadora.cod_nota             = nota_liquidacao_paga.cod_nota
                        AND nota_liquidacao_conta_pagadora.timestamp            = nota_liquidacao_paga.timestamp

                    JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                         ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                        AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                        AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

                    JOIN empenho.pagamento_liquidacao
                         ON pagamento_liquidacao.exercicio              = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                        AND pagamento_liquidacao.cod_entidade           = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                        AND pagamento_liquidacao.cod_ordem              = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                        AND pagamento_liquidacao.exercicio_liquidacao   = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                        AND pagamento_liquidacao.cod_nota               = pagamento_liquidacao_nota_liquidacao_paga.cod_nota

                    JOIN empenho.empenho
                         ON empenho.exercicio    = nl.exercicio_empenho
                        AND empenho.cod_entidade = nl.cod_entidade
                        AND empenho.cod_empenho  = nl.cod_empenho

                    JOIN empenho.pre_empenho
                         ON pre_empenho.exercicio       = empenho.exercicio
                        AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                   
                    LEFT JOIN empenho.restos_pre_empenho
                         ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                          
                    LEFT JOIN sw_cgm
                         ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                    LEFT JOIN sw_cgm_pessoa_fisica
                        ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario

                    LEFT JOIN sw_cgm_pessoa_juridica
                        ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario

                    LEFT JOIN empenho.pre_empenho_despesa
                         ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                        AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                                   
                    LEFT JOIN orcamento.despesa
                         ON despesa.exercicio   = pre_empenho_despesa.exercicio
                        AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            
                    LEFT JOIN tcemg.uniorcam
                         ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                        AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao
                        AND uniorcam.exercicio   = restos_pre_empenho.exercicio    
                        AND uniorcam.num_orgao_atual IS NOT NULL                            
                            
                    JOIN empenho.ordem_pagamento
                         ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                        AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                        AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
                             
                    LEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
                                        , ordem_pagamento_retencao.cod_entidade
                                        , ordem_pagamento_retencao.exercicio
                                        , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                                FROM empenho.ordem_pagamento_retencao
                                JOIN contabilidade.plano_analitica
                                     ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                    AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                                JOIN contabilidade.plano_conta
                                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                                    AND plano_conta.exercicio = plano_analitica.exercicio
                                WHERE SUBSTR(plano_conta.cod_estrutural, 1, 1) <> '4'
                                GROUP BY ordem_pagamento_retencao.cod_ordem
                                        , ordem_pagamento_retencao.cod_entidade
                                        , ordem_pagamento_retencao.exercicio
                    ) AS vl_retencao_orcamentaria
                         ON vl_retencao_orcamentaria.cod_ordem    = ordem_pagamento.cod_ordem
                        AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                        AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio
                                
                    WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                    AND TO_DATE(nota_liquidacao_paga.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                    AND ordem_pagamento.cod_entidade IN (".$this->getDado('entidade').")
                    --AND nota_liquidacao_paga_anulada.timestamp_anulada IS NULL
                    GROUP BY tiporegistro
                            , codorgao
                            , codunidadesub
                            , nroop
                            , dtpagamento
                            , especificacaoop
                            , cpfresppgto
                            
                    ORDER BY   nroop";
        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosOPS11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );                
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosOPS11()
    {
        $stSql = "SELECT    '11' AS tiporegistro
                           , LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = empenho.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0')::VARCHAR AS codorgao
                           , CASE WHEN  (pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho and pre_empenho.implantado = 't') THEN
					                        CASE WHEN ( uniorcam.num_orgao_atual IS NOT NULL) THEN
						                          LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')::VARCHAR
					                        ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')::VARCHAR
					                   END
				                ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')::VARCHAR
				            END AS codunidadesub
                           , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyymmddhh24mm')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop
                           , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'ddmmyyyy') AS dtpagamento
                           , empenho.cod_empenho AS nroempenho
                           , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dtempenho
                           , pagamento_liquidacao.cod_ordem AS codreduzidoop
                           , CASE WHEN resultado_pagamento.pagamento = '3' OR resultado_pagamento.pagamento = '4'
                                  THEN CASE WHEN TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')."
                                       THEN '3'
                                       WHEN TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')."
                                       THEN '4'
                                       ELSE resultado_pagamento.pagamento
                                   END
                                  ELSE resultado_pagamento.pagamento
                              END AS tipopagamento
                           , TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."'
                                                           ,empenho.cod_entidade
                                                           ,nota_liquidacao.cod_nota
                                                           ,nota_liquidacao.exercicio_empenho
                                                           ,empenho.cod_empenho
                            ) AS nroliquidacao
                           , resultado_pagamento.recurso as codfontrecursos
                           , TO_CHAR(nota_liquidacao.dt_liquidacao,'ddmmyyyy') AS dtliquidacao                           
                           , sum(nota_liquidacao_paga.vl_pago) AS valorfonte                            
                           , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL 
                                    THEN 
                                            '1'
                                    ELSE 
                                            '2'
                                            
                            END::VARCHAR AS tipodocumentocredor
                           , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                    THEN
                                        sw_cgm_pessoa_fisica.cpf 
                                    else
                                        case when sw_cgm_pessoa_juridica.cnpj IS NOT NULL 
                                            THEN
                                                sw_cgm_pessoa_juridica.cnpj
                                            ELSE
                                                ( SELECT cnpj 
                                                FROM sw_cgm_pessoa_juridica 
                                                WHERE numcgm = (SELECT numcgm 
                                                                FROM orcamento.entidade 
                                                                WHERE exercicio = '".$this->getDado('exercicio')."'
                                                                AND cod_entidade = ".$this->getDado('entidade')."))::VARCHAR
                                        END                           
                            END AS nrodocumento

                            FROM  empenho.nota_liquidacao as nl

                            JOIN empenho.nota_liquidacao_paga
                                 ON nota_liquidacao_paga.exercicio    = nl.exercicio
                                AND nota_liquidacao_paga.cod_entidade = nl.cod_entidade
                                AND nota_liquidacao_paga.cod_nota     = nl.cod_nota

                            LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                 ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

                            INNER JOIN empenho.nota_liquidacao
                                    ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                   AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                   AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

                            JOIN empenho.nota_liquidacao_conta_pagadora
                                     ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                    AND nota_liquidacao_conta_pagadora.cod_entidade         = nota_liquidacao_paga.cod_entidade
                                    AND nota_liquidacao_conta_pagadora.cod_nota             = nota_liquidacao_paga.cod_nota
                                    AND nota_liquidacao_conta_pagadora.timestamp            = nota_liquidacao_paga.timestamp

                             JOIN contabilidade.plano_analitica
                                     ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
                                    AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio
                         
                             JOIN contabilidade.plano_recurso
                                     ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                    AND plano_recurso.exercicio = plano_analitica.exercicio

                            INNER JOIN empenho.empenho
                                    ON empenho.exercicio    = nl.exercicio_empenho
                                   AND empenho.cod_entidade = nl.cod_entidade
                                   AND empenho.cod_empenho  = nl.cod_empenho

                            INNER JOIN empenho.pre_empenho
                                    ON pre_empenho.exercicio       = empenho.exercicio
                                   AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                   
                             LEFT JOIN empenho.restos_pre_empenho
                                    ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                   AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                                   
                             LEFT JOIN tcemg.uniorcam
				                 ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                                 AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao
				                 AND uniorcam.exercicio = restos_pre_empenho.exercicio
				                 AND uniorcam.num_orgao_atual IS NOT NULL
                                    
                             LEFT JOIN sw_cgm
                                    ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                             LEFT JOIN sw_cgm_pessoa_fisica
                                    ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario

                             LEFT JOIN sw_cgm_pessoa_juridica
                                    ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario

                            LEFT JOIN empenho.pre_empenho_despesa
                                    ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                                   AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                                   
                            LEFT JOIN orcamento.despesa
                                    ON despesa.exercicio   = pre_empenho_despesa.exercicio
                                   AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                            LEFT JOIN orcamento.conta_despesa
                                    ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                   AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

			                 JOIN (SELECT restos_pre_empenho.cod_pre_empenho
                                            , restos_pre_empenho.exercicio
					                       , restos_pre_empenho.recurso
                                            , CASE WHEN pre_empenho.implantado = 't'
                                                   THEN '3'
                                                   ELSE '4'
                                            END AS pagamento
                                         FROM empenho.restos_pre_empenho
                                         JOIN empenho.pre_empenho
                                             ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                                            AND pre_empenho.exercicio       = restos_pre_empenho.exercicio
                                           
                                        UNION
                                        
                                       SELECT pre_empenho_despesa.cod_pre_empenho
                                            , pre_empenho_despesa.exercicio
					                        , recurso.cod_recurso AS recurso
                                            , CASE WHEN substr(conta_despesa.cod_estrutural, 1, 3) = '4.6'
                                                   THEN '1'
                                                   ELSE '2'
                                            END AS pagamento
                                         FROM orcamento.conta_despesa
                                         JOIN empenho.pre_empenho_despesa
                                           ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                          AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
					                    JOIN orcamento.despesa
   				    	                    ON despesa.exercicio   = pre_empenho_despesa.exercicio
                                  	         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
					                   JOIN orcamento.recurso
 					                          ON recurso.exercicio   = despesa.exercicio
                                  	         AND recurso.cod_recurso = despesa.cod_recurso
                                    ) AS resultado_pagamento
                                  ON resultado_pagamento.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 AND resultado_pagamento.exercicio = pre_empenho.exercicio
                                   
                            INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                    ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                                   AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                                   AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                                   AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

                            INNER JOIN empenho.pagamento_liquidacao
                                    ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao    = pagamento_liquidacao.exercicio_liquidacao
                                   AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                                   AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
                                   AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
                                    AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio

                            INNER JOIN empenho.ordem_pagamento
                                     ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                    AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                    AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

                             LEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
                                                , ordem_pagamento_retencao.cod_entidade
                                                , ordem_pagamento_retencao.exercicio
                                                , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                                            FROM empenho.ordem_pagamento_retencao
                                            JOIN contabilidade.plano_analitica
                                              ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                             AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                                            JOIN contabilidade.plano_conta
                                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                                             AND plano_conta.exercicio = plano_analitica.exercicio
                                           WHERE SUBSTR(plano_conta.cod_estrutural, 1, 1) <> '4'
                                        GROUP BY ordem_pagamento_retencao.cod_ordem
                                                , ordem_pagamento_retencao.cod_entidade
                                                , ordem_pagamento_retencao.exercicio
                                    ) AS vl_retencao_orcamentaria
                                 ON vl_retencao_orcamentaria.cod_ordem    = ordem_pagamento.cod_ordem
                                AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                                AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio
                                
                         WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                           AND TO_DATE(nota_liquidacao_paga.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                           AND ordem_pagamento.cod_entidade IN (".$this->getDado('entidade').")
                           --AND nota_liquidacao_paga_anulada.timestamp_anulada IS NULL
                               GROUP BY   tiporegistro
                                           , codorgao
                                           , codunidadesub
                                           , nroop
                                           , dtpagamento
                                           , nroempenho
                                           , dtempenho
                                           , codreduzidoop
                                           , tipopagamento
                                           , nroliquidacao
                                           , codfontrecursos
                                           , dtliquidacao                                           
                                           , tipodocumentocredor
                                           , nrodocumento
                                           , uniorcam.num_orgao
                                           , uniorcam.num_unidade
                                           , restos_pre_empenho.num_unidade
                               ORDER BY   nroop
                ";
        return $stSql;
    }

        /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaDadosOPS12( &$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "" ){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );                
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }    
    
    function montaRecuperaDadosOPS12(){
        
        $stSql  = "SELECT    '12' AS tiporegistro
                             , LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = empenho.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0')::VARCHAR AS codorgao
                             , CASE WHEN  (pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho and pre_empenho.implantado = 't') THEN
                    CASE WHEN ( uniorcam.num_orgao_atual IS NOT NULL) THEN
                            LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')::VARCHAR
                         ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')::VARCHAR
                    END
                     ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')::VARCHAR
                END AS codunidadesub
                             , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyymmddhh24mm')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop
                             , CASE WHEN plano_conta.cod_estrutural like '1.1.1.1.1.01%' THEN
                                                '05'
                                        ELSE
                                            CASE WHEN pagamento_tipo_documento.cod_tipo_documento IS NOT NULL THEN
                                                    pagamento_tipo_documento.cod_tipo_documento::varchar
                                            ELSE
                                                    '99'
                                            END
                             END AS tipodocumentoop
                             , CASE WHEN pagamento_tipo_documento.num_documento IS NULL THEN 
                                                '0000'
                                        ELSE
                                                pagamento_tipo_documento.num_documento 
                             END AS nrodocumento
                             , conta_bancaria.cod_ctb_anterior AS codctb
                             , CASE WHEN pagamento_tipo_documento.cod_tipo_documento = 99 THEN 
                                            (SELECT td.descricao FROM tcemg.tipo_documento AS td WHERE td.cod_tipo = pagamento_tipo_documento.cod_tipo_documento)
									ELSE	' '
                             END AS desc_tipo_documento_op
                             , plano_recurso.cod_recurso AS codfontectb
                             , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'ddmmyyyy') AS dtemissao
                             , nota_liquidacao_paga.vl_pago AS vldocumento
                             , pagamento_liquidacao.cod_ordem AS codreduzidoop
                               
                            FROM  empenho.nota_liquidacao as nl

                            JOIN empenho.nota_liquidacao_paga
                                 ON nota_liquidacao_paga.exercicio    = nl.exercicio
                                AND nota_liquidacao_paga.cod_entidade = nl.cod_entidade
                                AND nota_liquidacao_paga.cod_nota     = nl.cod_nota

                            LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                 ON nota_liquidacao_paga_anulada.exercicio       = nota_liquidacao_paga.exercicio
                                AND nota_liquidacao_paga_anulada.cod_nota       = nota_liquidacao_paga.cod_nota
                                AND nota_liquidacao_paga_anulada.cod_entidade   = nota_liquidacao_paga.cod_entidade
                                AND nota_liquidacao_paga_anulada.timestamp      = nota_liquidacao_paga.timestamp

                            LEFT JOIN tcemg.pagamento_tipo_documento
                                       ON pagamento_tipo_documento.cod_nota     = nota_liquidacao_paga.cod_nota
                                      AND pagamento_tipo_documento.exercicio    = nota_liquidacao_paga.exercicio
                                      AND pagamento_tipo_documento.timestamp    = nota_liquidacao_paga.timestamp
                                      AND pagamento_tipo_documento.cod_entidade = nota_liquidacao_paga.cod_entidade
                            
                            LEFT JOIN tcemg.tipo_documento
                                        ON tipo_documento.cod_tipo = pagamento_tipo_documento.cod_tipo_documento

                             INNER JOIN empenho.nota_liquidacao_conta_pagadora
                                     ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                    AND nota_liquidacao_conta_pagadora.cod_entidade         = nota_liquidacao_paga.cod_entidade
                                    AND nota_liquidacao_conta_pagadora.cod_nota             = nota_liquidacao_paga.cod_nota
                                    AND nota_liquidacao_conta_pagadora.timestamp            = nota_liquidacao_paga.timestamp

                             INNER JOIN contabilidade.plano_analitica
                                     ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
                                    AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio
                         
                             INNER JOIN contabilidade.plano_recurso
                                     ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                    AND plano_recurso.exercicio = plano_analitica.exercicio
                                    
                             INNER JOIN contabilidade.plano_conta 
                                     ON plano_analitica.cod_conta = plano_conta.cod_conta
                                    AND plano_analitica.exercicio = plano_conta.exercicio
                             
                             LEFT JOIN tcemg.conta_bancaria
                                    ON conta_bancaria.cod_conta = plano_conta.cod_conta
                                   AND conta_bancaria.exercicio = plano_conta.exercicio
                       
                             INNER JOIN empenho.empenho
                                     ON empenho.exercicio    = nl.exercicio_empenho
                                    AND empenho.cod_entidade = nl.cod_entidade
                                    AND empenho.cod_empenho  = nl.cod_empenho
                                 
                             INNER JOIN empenho.pre_empenho
                                     ON pre_empenho.exercicio       = empenho.exercicio
                                    AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                    
                              LEFT JOIN empenho.restos_pre_empenho
                                     ON restos_pre_empenho.exercicio       = pre_empenho.exercicio
                                    AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 
                              LEFT JOIN sw_cgm
                                     ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                             
                              LEFT JOIN sw_cgm_pessoa_fisica
                                     ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario
                             
                              LEFT JOIN sw_cgm_pessoa_juridica
                                     ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario
                                     
                              LEFT JOIN empenho.pre_empenho_despesa
                                     ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                                    AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                                 
                              LEFT JOIN orcamento.despesa
                                     ON despesa.exercicio   = pre_empenho_despesa.exercicio
                                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                         
                              LEFT JOIN orcamento.recurso
                                     ON recurso.exercicio   = despesa.exercicio
                                    AND recurso.cod_recurso = despesa.cod_recurso
                         
                              LEFT JOIN tcemg.uniorcam
                                     ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                                    AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao    
                                    AND uniorcam.exercicio   = restos_pre_empenho.exercicio
                                    AND uniorcam.num_orgao_atual IS NOT NULL      
                                 
                              LEFT JOIN orcamento.conta_despesa
                                     ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                    AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta 
                                 
                             INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                     ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                                    AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                                    AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                                    AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
                                
                             INNER JOIN empenho.pagamento_liquidacao
                                     ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota
                                    AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                                    AND pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio
                             
                             INNER JOIN empenho.ordem_pagamento
                                     ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                    AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                    AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
                             
                              LEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
                                                     , ordem_pagamento_retencao.cod_entidade
                                                     , ordem_pagamento_retencao.exercicio
                                                     , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                                            FROM empenho.ordem_pagamento_retencao
                                            JOIN contabilidade.plano_analitica
                                              ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                             AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                                            JOIN contabilidade.plano_conta
                                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                                             AND plano_conta.exercicio = plano_analitica.exercicio
                                           WHERE SUBSTR(plano_conta.cod_estrutural, 1, 1) <> '4'
                                        GROUP BY   ordem_pagamento_retencao.cod_ordem
                                                 , ordem_pagamento_retencao.cod_entidade
                                                 , ordem_pagamento_retencao.exercicio
                                        ) AS vl_retencao_orcamentaria
                                             ON vl_retencao_orcamentaria.cod_ordem    = ordem_pagamento.cod_ordem
                                             AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                                             AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio
                                             
                                  WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                                    AND TO_DATE(nota_liquidacao_paga.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                    AND ordem_pagamento.cod_entidade IN (".$this->getDado('entidade').")
                                    --AND nota_liquidacao_paga_anulada.timestamp_anulada IS NULL
                               
                               /*GROUP BY  tiporegistro
                                        , codorgao
                                        , codunidadesub
                                        , nroop
                                        , tipodocumentoop
                                        , codreduzidoop
                                        , nrodocumento
                                        , codctb
                                        , desc_tipo_documento_op
                                        , codfontectb
                                        , dtemissao
                                        , vldocumento
                                */
                                 ORDER BY  nroOp";

        return $stSql;
    }
    
     /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosOPS13.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaDadosOPS13( &$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "" ){
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosOPS13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        return $obErro;
    }    
    
    function montaRecuperaDadosOPS13(){
        
        $stSql  = "SELECT  '13' AS tiporegistro
                           , LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = empenho.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0')::VARCHAR AS codorgao
                           , LPAD(lpad(uniorcam.num_orgao::VARCHAR, 1, '0')||LPAD(uniorcam.num_unidade::VARCHAR, 2, '0'),5,'0')::VARCHAR AS codunidadesub
                           , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyymmddhh24mm')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop
                           , pagamento_liquidacao.cod_ordem AS codreduzidoop
                           , nota_liquidacao_paga.cod_tipo_documento AS tiporetencao
                           , '' AS descricaoretencao
                           , '' AS vlretencao
                           
                    FROM ( SELECT vl_total as valor
                                  , nl.exercicio
                                  , nl.cod_nota
                                  , nl.cod_entidade 
                                  , nlp.timestamp
                                  , pagamento_tipo_documento.num_documento
                                  , pagamento_tipo_documento.cod_tipo_documento
                            FROM empenho.nota_liquidacao as nl
                                           
                      INNER JOIN empenho.nota_liquidacao_paga as nlp
                              ON nlp.exercicio    = nl.exercicio
                             AND nlp.cod_entidade = nl.cod_entidade
                             AND nlp.cod_nota     = nl.cod_nota
                
                      INNER JOIN tcemg.pagamento_tipo_documento
                              ON pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                             AND pagamento_tipo_documento.exercicio    = nlp.exercicio
                             AND pagamento_tipo_documento.timestamp    = nlp.timestamp
                             AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade
                                 
                      INNER JOIN empenho.nota_liquidacao_item as nli
                              ON nl.exercicio    = nli.exercicio
                             AND nl.cod_nota     = nli.cod_nota
                             AND nl.cod_entidade = nli.cod_entidade
                                 
                       LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia
                              ON nlia.exercicio       = nli.exercicio
                             AND nlia.cod_nota        = nli.cod_nota
                             AND nlia.num_item        = nli.num_item
                             AND nlia.exercicio_item  =  nli.exercicio_item
                             AND nlia.cod_pre_empenho =  nli.cod_pre_empenho
                             AND nlia.cod_entidade    = nli.cod_entidade
                                        
                           WHERE TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') 
                    ) AS nota_liquidacao_paga
                                        
                    INNER JOIN empenho.nota_liquidacao
                            ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                           AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                           AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                 
                    INNER JOIN empenho.nota_liquidacao_conta_pagadora
                            ON nota_liquidacao_conta_pagadora.exercicio    = nota_liquidacao_paga.exercicio
                           AND nota_liquidacao_conta_pagadora.cod_entidade = nota_liquidacao_paga.cod_entidade
                           AND nota_liquidacao_conta_pagadora.cod_nota     = nota_liquidacao_paga.cod_nota
                 
                    INNER JOIN contabilidade.plano_analitica
                            ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
                           AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio
                 
                    INNER JOIN contabilidade.plano_recurso
                            ON plano_recurso.cod_plano = plano_analitica.cod_plano
                           AND plano_recurso.exercicio = plano_analitica.exercicio
                       
                    INNER JOIN empenho.empenho
                            ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                           AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                        
                    INNER JOIN empenho.pre_empenho
                            ON pre_empenho.exercicio       = empenho.exercicio
                           AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        
                     LEFT JOIN sw_cgm
                            ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                    
                     LEFT JOIN sw_cgm_pessoa_fisica
                            ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario
                    
                     LEFT JOIN sw_cgm_pessoa_juridica
                            ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario
                            
                    INNER JOIN empenho.pre_empenho_despesa
                            ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                           AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                        
                    INNER JOIN orcamento.despesa
                            ON despesa.exercicio   = pre_empenho_despesa.exercicio
                           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                 
                    INNER JOIN orcamento.recurso
                            ON recurso.exercicio   = despesa.exercicio
                           AND recurso.cod_recurso = despesa.cod_recurso
                 
                    INNER JOIN orcamento.unidade
                            ON unidade.exercicio   = despesa.exercicio
                           AND unidade.num_unidade = despesa.num_unidade
                           AND unidade.num_orgao   = despesa.num_orgao
                 
                    INNER JOIN tcemg.uniorcam
                        ON uniorcam.num_unidade = unidade.num_unidade
                       AND uniorcam.num_orgao   = unidade.num_orgao 
                       AND uniorcam.exercicio   = unidade.exercicio
                 
                    INNER JOIN orcamento.orgao
                            ON orgao.exercicio = unidade.exercicio
                           AND orgao.num_orgao = unidade.num_orgao
                        
                    INNER JOIN orcamento.conta_despesa
                        ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                        AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta 
                                
                    INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                            ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                           AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                           AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                           AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
                                
                    INNER JOIN empenho.pagamento_liquidacao
                            ON pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                           AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                           AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
                           AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
                           AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio
                             
                    INNER JOIN empenho.ordem_pagamento
                            ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
                           AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                           AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem
                         
                    INNER JOIN empenho.ordem_pagamento_retencao
                            ON ordem_pagamento_retencao.exercicio    = ordem_pagamento.exercicio
                           AND ordem_pagamento_retencao.cod_entidade = ordem_pagamento.cod_entidade
                           AND ordem_pagamento_retencao.cod_ordem    = ordem_pagamento.cod_ordem
                         
                    INNER JOIN tcemg.balancete_extmmaa
                            ON balancete_extmmaa.exercicio = plano_analitica.exercicio
                           AND balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                           AND balancete_extmmaa.tipo_lancamento = 1 /* Tipo Fixo pois configuração EXT apenas a opção Depósitos e Consignações contemplam os subtipos da documentação */
                         
                    LEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
                                       , ordem_pagamento_retencao.cod_entidade
                                       , ordem_pagamento_retencao.exercicio
                                       , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                                  FROM empenho.ordem_pagamento_retencao
                                  JOIN contabilidade.plano_analitica
                                    ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                                   AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                                  JOIN contabilidade.plano_conta
                                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                                   AND plano_conta.exercicio = plano_analitica.exercicio
                                 WHERE SUBSTR(plano_conta.cod_estrutural, 1, 1) <> '4'
                              GROUP BY ordem_pagamento_retencao.cod_ordem
                                       , ordem_pagamento_retencao.cod_entidade
                                       , ordem_pagamento_retencao.exercicio
                    ) AS vl_retencao_orcamentaria
                        ON vl_retencao_orcamentaria.cod_ordem    = ordem_pagamento.cod_ordem
                       AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                       AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio
                     WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                       AND TO_DATE(ordem_pagamento.dt_emissao::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                       AND ordem_pagamento.cod_entidade IN (".$this->getDado('entidade').")
                                    
                  GROUP BY   tiporegistro
                           , codorgao
                           , codunidadesub
                           , nroop
                           , codreduzidoop
                           , tiporetencao
                           , descricaoretencao
                           --, vlretencao
                                 
                  ORDER BY codReduzidoOP";
                  
        return $stSql;
    }
    
    public function __destruct(){}

}