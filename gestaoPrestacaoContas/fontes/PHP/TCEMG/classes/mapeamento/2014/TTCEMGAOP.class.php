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

    $Id: TTCEMGAOP.class.php 62269 2015-04-15 18:28:39Z franver $

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGAOP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGAOP()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOP10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOP10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOP10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
                                            
    public function montaRecuperaDadosAOP10()
    {
        $stSql  = "  SELECT * FROM ( SELECT   '10' AS tiporegistro
                                            , RPAD(ordem_pagamento.cod_ordem||TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'hh24mmss'),15,'0') AS codreduzido
                                            , LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = empenho.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0') AS codorgao
                                            , CASE WHEN  (pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho and pre_empenho.implantado = 't') THEN
                                                    CASE WHEN ( uniorcam.num_orgao_atual IS NOT NULL) THEN
                                                            LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                                    ELSE
                                                            LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                                    END
                                                ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')
                                              END AS codunidadesub
                                            , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyymmddhh24mm')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop
                                            , TO_CHAR(nota_liquidacao_paga.timestamp,'ddmmyyyy') AS dtpagamento
                                            , TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'yyyymmddhh24mmss')||LPAD(ordem_pagamento.cod_ordem::VARCHAR, 8, '0') AS nroanulacaoop
                                            , TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'ddmmyyyy') AS dtanulacaoop
                                            , CASE WHEN nota_liquidacao_paga_anulada.observacao = '' THEN
                                                    'Anulação de Pagamento'
                                                ELSE 
                                                    nota_liquidacao_paga_anulada.observacao
                                              END AS justificativaanulacao
                                            , REPLACE(COALESCE(pagamento_liquidacao.vl_pagamento,0)::varchar, '.',',') AS vlanulacaoop
                                
                                        FROM empenho.nota_liquidacao_paga
                
                                  INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                          ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                                         AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                                         AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                                         AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
                
                                  INNER JOIN (SELECT exercicio
                                                    , cod_entidade
                                                    , cod_nota
                                                    , SUM(COALESCE(vl_anulado,0)) AS vl_anulado
                                                    , MAX(timestamp) AS timestamp
                                                    , MAX(timestamp_anulada) AS timestamp_anulada
                                                    , observacao
                                                FROM empenho.nota_liquidacao_paga_anulada
                                                GROUP BY exercicio
                                                        , cod_entidade
                                                        , cod_nota
                                                        , observacao
                                                        , timestamp
                                            ) AS nota_liquidacao_paga_anulada
                                          ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                         AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                         AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                         AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
                
                                  INNER JOIN empenho.pagamento_liquidacao
                                          ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                                         AND pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio
                
                                  INNER JOIN empenho.ordem_pagamento
                                          ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
                                         AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                         AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem
                
                                  INNER JOIN empenho.nota_liquidacao
                                          ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                         AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                         AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                
                                  INNER JOIN empenho.empenho
                                          ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                         AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                         AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                
                                  INNER JOIN empenho.pre_empenho
                                          ON pre_empenho.exercicio       = empenho.exercicio
                                         AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                                   LEFT JOIN empenho.restos_pre_empenho
                                          ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                         AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                             
                                   LEFT JOIN tcemg.uniorcam
                                          ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                                         AND uniorcam.exercicio = restos_pre_empenho.exercicio
                                         AND uniorcam.num_orgao_atual IS NOT NULL

                                   LEFT JOIN empenho.pre_empenho_despesa
                                          ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                                         AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
				   
                                   LEFT JOIN orcamento.despesa
                                          ON despesa.exercicio    = pre_empenho_despesa.exercicio
                                         AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                
                                      WHERE ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
                                        AND ordem_pagamento.cod_entidade IN (".$this->getDado('entidade').")
                                        AND (TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada, 'yyyy')) = '".$this->getDado('exercicio')."'
                                        AND TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                        
                             GROUP BY tiporegistro
                                    , codreduzido
                                    , codorgao
                                    , codunidadesub
                                    , nroop
                                    , dtpagamento
                                    , nroanulacaoop
                                    , dtanulacaoop
                                    , justificativaanulacao
                                    , vlanulacaoop
                                    , codreduzido
                                    , codunidadesub              
                        ) AS registros";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOP11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOP11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOP11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOP11()
    {
     
        $stSql  = "    
            SELECT  '11' AS tiporegistro
                    , RPAD(ordem_pagamento.cod_ordem||TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'hh24mmss'),15,'0') AS codreduzido
                    , CASE WHEN (pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho ) THEN
                                    CASE WHEN (pre_empenho.implantado = 't')  THEN
                                            '3'
                                    ELSE
                                            '4'
                                    END
                        WHEN substr(conta_despesa.cod_estrutural, 1, 3) = '4.6' THEN
                            '2'
                        ELSE
                            '1'
                      END AS tipopagamento
                    , empenho.cod_empenho AS nroempenho
                    , to_char(empenho.dt_empenho,'ddmmyyyy') AS dtempenho
                    , TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."',
                                                     empenho.cod_entidade,
                                                     nota_liquidacao.cod_nota,
                                                     nota_liquidacao.exercicio_empenho,
                                                     empenho.cod_empenho
                                                    ) AS nroliquidacao
                    , to_char(nota_liquidacao.dt_liquidacao,'ddmmyyyy') AS dtliquidacao 
                    , CASE WHEN  (pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho and pre_empenho.implantado = 't') THEN
                                restos_pre_empenho.recurso
                        ELSE
                                recurso.cod_fonte::INTEGER
                        END AS codfontrecurso
                    , REPLACE(COALESCE(pagamento_liquidacao.vl_pagamento,0)::varchar, '.',',') AS vlanulacaofonte  
                    , '' AS codorgao
                    , '' AS codunidadesub
                    , TO_CHAR(pagamento_liquidacao_nota_liquidacao_paga.timestamp,'yyyymmddhh24mm')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop
                    , TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'yyyymmddhh24mmss')||LPAD(ordem_pagamento.cod_ordem::VARCHAR, 8, '0') AS nroanulacaoop
    
               FROM empenho.nota_liquidacao_paga
    
         INNER JOIN empenho.nota_liquidacao
                 ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
    
         INNER JOIN (SELECT exercicio
                           , cod_entidade
                           , cod_nota
                           , SUM(COALESCE(vl_anulado,0)) AS vl_anulado
                           , MAX(timestamp) AS timestamp
                           , MAX(timestamp_anulada) AS timestamp_anulada
                           , observacao
                      FROM empenho.nota_liquidacao_paga_anulada
                  GROUP BY exercicio
                           , cod_entidade
                            , cod_nota
                            , observacao
                            , timestamp
                ) AS nota_liquidacao_paga_anulada
                  ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                 AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                 AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                 AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
    
         INNER JOIN empenho.empenho
                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
    
         INNER JOIN empenho.pre_empenho
                 ON pre_empenho.exercicio       = empenho.exercicio
               AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

          LEFT JOIN empenho.restos_pre_empenho
                 ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
               AND restos_pre_empenho.exercicio = pre_empenho.exercicio
   
          LEFT JOIN tcemg.uniorcam
                 ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                AND uniorcam.exercicio = restos_pre_empenho.exercicio
                AND uniorcam.num_orgao_atual IS NOT NULL

          LEFT JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

          LEFT JOIN orcamento.despesa
                 ON despesa.exercicio    = pre_empenho_despesa.exercicio
                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                
          LEFT JOIN orcamento.recurso
                 ON recurso.exercicio   = despesa.exercicio
                AND recurso.cod_recurso = despesa.cod_recurso

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
    
              WHERE (to_char(nota_liquidacao_paga_anulada.timestamp_anulada, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                AND TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                AND ordem_pagamento.cod_entidade IN (".$this->getDado('entidade').")
    
           GROUP BY tiporegistro
                   , codreduzido
                   , tipopagamento
                   , nroempenho
                   , dtempenho
                   , nroliquidacao
                   , dtliquidacao
                   , codfontrecurso
                   , vlanulacaofonte
                   , codorgao
                   , codunidadesub
                   , nroop
                   , nroanulacaoop
                   ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>