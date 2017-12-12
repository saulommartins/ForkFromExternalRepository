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

class TTCEMGAOC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGAOC()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOC10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOC10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOC10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOC10()
    {
        $stSql  = " SELECT
                            tiporegistro
                            ,tabela.cod_entidade AS codorgao
                            ,tabela.num_norma AS nrodecreto
                            ,tabela.data AS datadecreto                            
                        FROM (                                                                      
                           SELECT   
                                '10'::text AS tiporegistro
                                ,CASE WHEN EXTRACT(MONTH FROM suplementacao.dt_suplementacao) <> EXTRACT(MONTH FROM norma.dt_assinatura) THEN
                                            TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy')
                                      ELSE	TO_CHAR(norma.dt_assinatura,'ddmmyyyy')
                                 END AS data
                                ,norma.nom_norma||' '||norma.num_norma||'/'||norma.exercicio   as fundamentacao
                                ,norma.num_norma
                                ,tipo_transferencia.nom_tipo as tipo_suplementacao
                                ,tipo_transferencia.cod_tipo                                 
                                ,suplementacao_suplementada.valor
                                ,despesa.cod_entidade                                       
                                ,CASE                                                               
                                   WHEN suplementacao_anulada.cod_suplementacao IS NOT NULL THEN 'anulada'          
                                   ELSE 'valida'                                                   
                                END as situacao
                                                                                    
                            FROM orcamento.suplementacao
                            
                        LEFT JOIN orcamento.suplementacao_anulada
                              ON suplementacao.exercicio          = suplementacao_anulada.exercicio                         
                             AND suplementacao.cod_suplementacao  = suplementacao_anulada.cod_suplementacao                 
                                                                
                        JOIN contabilidade.tipo_transferencia
                               ON tipo_transferencia.cod_tipo  = suplementacao.cod_tipo                              
                              AND tipo_transferencia.exercicio = suplementacao.exercicio                                                                     
                      
                        JOIN orcamento.suplementacao_suplementada                                                                       
                              ON suplementacao.cod_suplementacao  = suplementacao_suplementada.cod_suplementacao                     
                             AND suplementacao.exercicio          = suplementacao_suplementada.exercicio                             
                      
                        JOIN orcamento.despesa                                                                    
                              ON suplementacao_suplementada.cod_despesa = despesa.cod_despesa                           
                             AND suplementacao_suplementada.exercicio   = despesa.exercicio                             
                      
                        JOIN contabilidade.transferencia_despesa                                   
                              ON transferencia_despesa.cod_tipo          = suplementacao.cod_tipo
                             AND transferencia_despesa.cod_suplementacao = suplementacao.cod_suplementacao
                             AND transferencia_despesa.exercicio         = suplementacao.exercicio
                             AND transferencia_despesa.cod_tipo          <> 16
                        
                        JOIN normas.norma                                                          
                             ON suplementacao.cod_norma = norma.cod_norma
                        
                        LEFT JOIN tcemg.norma_detalhe
                                ON norma_detalhe.cod_norma = norma.cod_norma

                        WHERE suplementacao.exercicio          = '".$this->getDado('exercicio')."'        
                        AND suplementacao.dt_suplementacao >= to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')                  
                        AND suplementacao.dt_suplementacao <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')
                        AND transferencia_despesa.cod_entidade IN (".$this->getDado('entidade').")
                    
                    ) AS tabela                                                                
                      
                    WHERE 
                        tabela.situacao = 'valida'                      
                    GROUP BY                                                                   
                        tiporegistro,
                        codorgao,
                        nrodecreto,
                        datadecreto
                    ORDER BY                                                                   
                        tabela.num_norma";
        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOC11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOC11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOC11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOC11()
    {
        $stSql  = "SELECT
                        '11' AS tiporegistro,
                        LPAD(tabela.num_norma||tabela.cod_tipo, 8,'0' ) as codreduzidodecreto,
                        tabela.num_norma AS nrodecreto,
                        
                        CASE WHEN tabela.tipo_lei_alteracao_orcamentaria = 5 THEN '11'
                             WHEN tabela.cod_tipo = 1 THEN '01'
                             WHEN tabela.cod_tipo = 2 THEN '01'
                             WHEN tabela.cod_tipo = 3 THEN '01'
                             WHEN tabela.cod_tipo = 4 THEN '01'
                             WHEN tabela.cod_tipo = 5 THEN '01'
                             WHEN tabela.cod_tipo = 6 THEN '02'
                             WHEN tabela.cod_tipo = 7 THEN '02'
                             WHEN tabela.cod_tipo = 8 THEN '02'
                             WHEN tabela.cod_tipo = 9 THEN '02'
                             WHEN tabela.cod_tipo = 10 THEN '02'
                             WHEN tabela.cod_tipo = 11 THEN '04'
                             WHEN tabela.cod_tipo = 12 THEN '10'
                             WHEN tabela.cod_tipo = 13 THEN '08'
                             WHEN tabela.cod_tipo = 14 THEN '09'
                        END AS tipodecretoalteracao,
                        
                        SUM(coalesce(tabela.valor, 0.00)) AS valoraberto
                        
                   FROM (                                                                      
                       SELECT   
                            CASE WHEN EXTRACT(MONTH FROM suplementacao.dt_suplementacao) <> EXTRACT(MONTH FROM norma.dt_assinatura) THEN
                                        TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy')
                                 ELSE	TO_CHAR(norma.dt_assinatura,'ddmmyyyy')
                            END AS data,
                            norma.num_norma,
                            tipo_transferencia.nom_tipo as tipo_suplementacao,
                            norma_detalhe.tipo_lei_alteracao_orcamentaria,
                            suplementacao.cod_tipo,
                            suplementacao_suplementada.valor,
                            despesa.cod_entidade,
                            suplementacao.cod_suplementacao,
                            suplementacao.exercicio,
                            CASE WHEN suplementacao_anulada.cod_suplementacao IS NOT NULL
                                THEN 'anulada'
                                ELSE 'valida'
                            END as situacao
                                                                                
                        FROM orcamento.suplementacao 
                        
                   LEFT JOIN orcamento.suplementacao_anulada
                          ON suplementacao.exercicio          = suplementacao_anulada.exercicio                         
                         AND suplementacao.cod_suplementacao  = suplementacao_anulada.cod_suplementacao                        
                            
                   INNER JOIN contabilidade.tipo_transferencia
                           ON tipo_transferencia.cod_tipo  = suplementacao.cod_tipo                              
                          AND tipo_transferencia.exercicio = suplementacao.exercicio                                                                   
                  
                  INNER JOIN orcamento.suplementacao_suplementada                                                                       
                          ON suplementacao.cod_suplementacao  = suplementacao_suplementada.cod_suplementacao                     
                         AND suplementacao.exercicio          = suplementacao_suplementada.exercicio                             
                  
                  INNER JOIN orcamento.despesa                                                                    
                          ON suplementacao_suplementada.cod_despesa = despesa.cod_despesa                           
                         AND suplementacao_suplementada.exercicio   = despesa.exercicio                             
                  
                  INNER JOIN contabilidade.transferencia_despesa                                   
                         ON transferencia_despesa.cod_tipo          = suplementacao.cod_tipo
                        AND transferencia_despesa.cod_suplementacao = suplementacao.cod_suplementacao
                        AND transferencia_despesa.exercicio         = suplementacao.exercicio
                        AND transferencia_despesa.cod_tipo          <> 16   
                  
                  INNER JOIN normas.norma                                                          
                         ON suplementacao.cod_norma = norma.cod_norma
                         
                   LEFT JOIN tcemg.norma_detalhe
                         ON norma_detalhe.cod_norma = norma.cod_norma
                  
                       WHERE suplementacao.exercicio         = '".$this->getDado('exercicio')."'        
                         AND suplementacao.dt_suplementacao >= to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')                  
                         AND suplementacao.dt_suplementacao <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')                    
                         AND transferencia_despesa.cod_entidade IN (".$this->getDado('entidade').")
                    ) AS tabela
                  
                   WHERE
                       tabela.situacao = 'valida'                      
                    GROUP BY                                                                                                                            
                        tiporegistro,
                        codreduzidodecreto,
                        nrodecreto,
                        tipodecretoalteracao
                    ORDER BY                                                                   
                        tabela.num_norma";
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOC12.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOC12(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOC12().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOC12()
    {
            $stSql  = " SELECT
                            '12' AS tiporegistro
                            ,LPAD(tabela.num_norma||tabela.cod_tipo, 8,'0' ) as codreduzidodecreto
                            ,( SELECT DISTINCT atributo_norma_valor.valor
                                            FROM normas.atributo_norma_valor
                                      INNER JOIN normas.atributo_tipo_norma
                                              ON atributo_tipo_norma.cod_tipo_norma = atributo_norma_valor.cod_tipo_norma
                                             AND atributo_tipo_norma.cod_modulo     = atributo_norma_valor.cod_modulo
                                             AND atributo_tipo_norma.cod_cadastro   = atributo_norma_valor.cod_cadastro
                                             AND atributo_tipo_norma.cod_atributo   = atributo_norma_valor.cod_atributo
                                      INNER JOIN administracao.atributo_dinamico
                                              ON atributo_dinamico.cod_modulo     = atributo_tipo_norma.cod_modulo    
                                             AND atributo_dinamico.cod_cadastro   = atributo_tipo_norma.cod_cadastro  
                                             AND atributo_dinamico.cod_atributo   = atributo_tipo_norma.cod_atributo  
                                           WHERE atributo_tipo_norma.ativo = TRUE
                                             AND atributo_dinamico.cod_atributo = 103
                                             AND atributo_norma_valor.cod_norma = tabela.cod_norma
                                             AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                                      FROM normas.atributo_norma_valor AS anv 
                                                                                     WHERE cod_norma = atributo_norma_valor.cod_norma 
                                                                                       AND cod_cadastro = atributo_norma_valor.cod_cadastro 
                                                                                       AND cod_modulo =  atributo_norma_valor.cod_modulo 
                                                                                       AND cod_atributo =  atributo_norma_valor.cod_atributo
                                                                                       AND atributo_dinamico.cod_atributo = 103 )
                            ) AS nroleialteracao
                            ,REPLACE((SELECT valor FROM normas.atributo_norma_valor WHERE cod_norma = tabela.cod_norma AND cod_atributo = 104 ORDER BY timestamp DESC LIMIT 1), '/', '') AS dataleialteracao
                            , SUBSTR(descricao,0,4) as tpleiorigdecreto
                            , tipo_lei_alteracao_orcamentaria as tipoleialteracao
                            , SUM(valor) as valorabertolei
                        FROM (                                                                      
                           SELECT
                                CASE WHEN EXTRACT(MONTH FROM suplementacao.dt_suplementacao) <> EXTRACT(MONTH FROM norma.dt_assinatura) THEN
                                            TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy')
                                     ELSE	TO_CHAR(norma.dt_assinatura,'ddmmyyyy')
                                 END AS data
                                ,norma.num_norma
                                ,norma.cod_norma
                                ,tipo_transferencia.nom_tipo as tipo_suplementacao
                                ,tipo_transferencia.cod_tipo                                 
                                ,suplementacao_suplementada.valor
                                ,despesa.cod_entidade
                                ,suplementacao.cod_suplementacao
                                ,suplementacao.exercicio
                                ,CASE                                                               
                                   WHEN suplementacao_anulada.cod_suplementacao IS NOT NULL THEN 'anulada'          
                                   ELSE 'valida'                                                   
                                END as situacao
                                ,tipo_lei_origem_decreto.descricao
                                ,norma_detalhe.tipo_lei_alteracao_orcamentaria
                                                                                    
                            FROM orcamento.suplementacao 
                            
                        LEFT JOIN orcamento.suplementacao_anulada
                              ON suplementacao.exercicio          = suplementacao_anulada.exercicio                         
                             AND suplementacao.cod_suplementacao  = suplementacao_anulada.cod_suplementacao                        
                                
                        JOIN contabilidade.tipo_transferencia
                               ON tipo_transferencia.cod_tipo  = suplementacao.cod_tipo                              
                              AND tipo_transferencia.exercicio = suplementacao.exercicio                             
                      
                        JOIN orcamento.suplementacao_suplementada                                                                       
                              ON suplementacao.cod_suplementacao  = suplementacao_suplementada.cod_suplementacao                     
                             AND suplementacao.exercicio          = suplementacao_suplementada.exercicio                             
                      
                        JOIN orcamento.despesa                                                                    
                              ON suplementacao_suplementada.cod_despesa = despesa.cod_despesa                           
                             AND suplementacao_suplementada.exercicio   = despesa.exercicio                             
                      
                        JOIN contabilidade.transferencia_despesa                                   
                             ON transferencia_despesa.cod_tipo          = suplementacao.cod_tipo
                            AND transferencia_despesa.cod_suplementacao = suplementacao.cod_suplementacao
                            AND transferencia_despesa.exercicio         = suplementacao.exercicio
                            AND transferencia_despesa.cod_tipo          <> 16                   
                      
                        JOIN normas.norma                                                          
                             ON suplementacao.cod_norma = norma.cod_norma
                      
                        JOIN tcemg.norma_detalhe
                            ON norma_detalhe.cod_norma = norma.cod_norma
    
                   LEFT JOIN tcemg.tipo_lei_origem_decreto
                          ON  tipo_lei_origem_decreto.cod_tipo_lei = norma_detalhe.tipo_lei_origem_decreto
    
                        WHERE suplementacao.exercicio          = '".$this->getDado('exercicio')."'        
                        AND suplementacao.dt_suplementacao >= to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')                  
                        AND suplementacao.dt_suplementacao <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')                    
                        AND transferencia_despesa.cod_entidade IN (".$this->getDado('entidade').")
                    ) AS tabela
                      
                    WHERE
                        tabela.situacao = 'valida'                      
                    GROUP BY
                        tabela.num_norma
                        ,nroleialteracao
                        ,dataleialteracao
                        ,codreduzidodecreto
                        ,descricao 
                        ,tipo_lei_alteracao_orcamentaria                     
                    ORDER BY                                                                   
                        tabela.num_norma";

        return $stSql;
    }
    
/**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOC13.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOC13(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOC13().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOC13()
    {
        $stSql  = "SELECT                  
                        '13' AS tiporegistro,
                        LPAD(tabela.num_norma||tabela.cod_tipo, 8,'0' ) as codreduzidodecreto,
                        
                        CASE WHEN tabela.cod_tipo = 1 THEN '03'
                             WHEN tabela.cod_tipo = 2 THEN '04'
                             WHEN tabela.cod_tipo = 4 THEN '02'
                             WHEN tabela.cod_tipo = 5 THEN '01'
                             WHEN tabela.cod_tipo = 6 THEN '03'
                             WHEN tabela.cod_tipo = 7 THEN '04'
                             WHEN tabela.cod_tipo = 9 THEN '02'
                             WHEN tabela.cod_tipo = 10 THEN '01'
                             WHEN tabela.cod_tipo = 11 THEN '98' 
                             WHEN tabela.cod_tipo = 12 THEN '98'
                             WHEN tabela.cod_tipo = 13 THEN '98'
                             WHEN tabela.cod_tipo = 14 THEN '98'
                             WHEN tabela.cod_tipo = 15 THEN '98'
                        END AS origemrecalteracao,
                        
                        SUM(coalesce(tabela.valor,0.00)) AS valorabertoorigem
                          
                   FROM (                                                                      
                       SELECT
                            CASE WHEN EXTRACT(MONTH FROM suplementacao.dt_suplementacao) <> EXTRACT(MONTH FROM norma.dt_assinatura) THEN
                                        TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy')
                                 ELSE	TO_CHAR(norma.dt_assinatura,'ddmmyyyy')
                            END AS data,                
                            norma.num_norma,
                            norma.cod_norma,
                            tipo_transferencia.nom_tipo as tipo_suplementacao,
                            suplementacao.cod_tipo,                                                      
                            suplementacao_suplementada.valor,                      
                            despesa.cod_entidade,
                            suplementacao.cod_suplementacao, 
                            suplementacao.exercicio,
                            CASE                                                               
                               WHEN suplementacao_anulada.cod_suplementacao IS NOT NULL THEN 'anulada'          
                               ELSE 'valida'                                                   
                            END as situacao
                                                                                
                        FROM orcamento.suplementacao 
                        
                   LEFT JOIN orcamento.suplementacao_anulada
                          ON suplementacao.exercicio          = suplementacao_anulada.exercicio                         
                         AND suplementacao.cod_suplementacao  = suplementacao_anulada.cod_suplementacao                        
                            
                   INNER JOIN contabilidade.tipo_transferencia
                           ON tipo_transferencia.cod_tipo  = suplementacao.cod_tipo                              
                          AND tipo_transferencia.exercicio = suplementacao.exercicio                             
                  
                  INNER JOIN orcamento.suplementacao_suplementada                                                                       
                          ON suplementacao.cod_suplementacao  = suplementacao_suplementada.cod_suplementacao                     
                         AND suplementacao.exercicio          = suplementacao_suplementada.exercicio                             
                  
                  INNER JOIN orcamento.despesa                                                                    
                          ON suplementacao_suplementada.cod_despesa = despesa.cod_despesa                           
                         AND suplementacao_suplementada.exercicio   = despesa.exercicio                             
                  
                  INNER JOIN contabilidade.transferencia_despesa                                   
                         ON transferencia_despesa.cod_tipo          = suplementacao.cod_tipo
                        AND transferencia_despesa.cod_suplementacao = suplementacao.cod_suplementacao
                        AND transferencia_despesa.exercicio         = suplementacao.exercicio
                        AND transferencia_despesa.cod_tipo          <> 16                    
                  
                  INNER JOIN normas.norma                                                          
                         ON suplementacao.cod_norma = norma.cod_norma
                  
                       WHERE suplementacao.exercicio          = '".$this->getDado('exercicio')."'        
                         AND suplementacao.dt_suplementacao >= to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')                  
                         AND suplementacao.dt_suplementacao <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')                    
                         AND transferencia_despesa.cod_entidade IN (".$this->getDado('entidade').")
                    ) AS tabela
                  
                   WHERE
                       tabela.situacao = 'valida'                      
                    GROUP BY                                                                   
                        tabela.num_norma
                      , origemrecalteracao
                      , codreduzidodecreto
                    ORDER BY                                                                   
                        tabela.num_norma";
        return $stSql;
    }
    
/**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOC14.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOC14(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOC14().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOC14()
    {
        $stSql  = " SELECT
                            tabela.dt_suplementacao
                            ,'14' AS tiporegistro
                            ,LPAD(tabela.num_norma||tabela.cod_tipo, 8,'0' ) as codreduzidodecreto
                            , origemrecalteracao as origemrecalteracao
                            ,LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = tabela.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0') AS codorgao
                            ,LPAD(lpad(tabela.num_orgao::VARCHAR, 2, '0')||LPAD(tabela.num_unidade::VARCHAR, 2, '0'),5,'0') AS codunidadesub
                            ,tabela.cod_funcao AS codfuncao
                            ,tabela.cod_subfuncao AS codsubfuncao
                            ,tabela.cod_programa AS codprograma
                            ,tabela.cod_acao AS idacao
                            ,'' AS idsubacao
                            ,tabela.cod_recurso AS codfontrecurso
                            , CASE WHEN SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6) = '339009' OR SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6) = '339005'
                                    THEN
                                        '319005'
                                    ELSE
                                    CASE WHEN SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6) = '339013'
                                        THEN
                                            '319013'
                                        ELSE
                                            SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6)::VARCHAR
                                    END
                            END AS naturezadespesa
                            ,'1' AS tipoalteracao
                            ,SUM(tabela.vl_suplementada) AS vlacrescimoreducao
                          
                   FROM (                                                                      
                       SELECT
                             CASE WHEN EXTRACT(MONTH FROM suplementacao.dt_suplementacao) <> EXTRACT(MONTH FROM norma.dt_assinatura) THEN
                                        TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy')
                                  ELSE	TO_CHAR(norma.dt_assinatura,'ddmmyyyy')
                             END AS data
                            ,norma.nom_norma||' '||norma.num_norma||'/'||norma.exercicio as fundamentacao
                            ,norma.num_norma
                            ,norma.cod_norma
                            ,tipo_transferencia.nom_tipo as tipo_suplementacao
                            ,tipo_transferencia.cod_tipo
                            ,despesa.cod_entidade
                            ,suplementacao.cod_suplementacao
                            ,suplementacao.exercicio
                            ,suplementacao.dt_suplementacao
                            ,CASE WHEN suplementacao.cod_tipo = 1 THEN '03'
                               WHEN suplementacao.cod_tipo = 2 THEN '04'
                               WHEN suplementacao.cod_tipo = 4 THEN '02'
                               WHEN suplementacao.cod_tipo = 5 THEN '01'
                               WHEN suplementacao.cod_tipo = 6 THEN '03'
                               WHEN suplementacao.cod_tipo = 7 THEN '04'
                               WHEN suplementacao.cod_tipo = 9 THEN '02'
                               WHEN suplementacao.cod_tipo = 10 THEN '01'
                               WHEN suplementacao.cod_tipo = 11 THEN '98' 
                               WHEN suplementacao.cod_tipo = 12 THEN '98'
                               WHEN suplementacao.cod_tipo = 13 THEN '98'
                               WHEN suplementacao.cod_tipo = 14 THEN '98'
                               WHEN suplementacao.cod_tipo = 15 THEN '98'
                            END AS origemrecalteracao
                            ,uniorcam.num_orgao
                            ,uniorcam.num_unidade
                            ,funcao.cod_funcao
                            ,subfuncao.cod_subfuncao
                            ,ppa.programa.num_programa AS cod_programa
                            ,ppa.acao.num_acao AS cod_acao
                            ,recurso.cod_recurso
                            ,conta_despesa.cod_estrutural
                            ,suplementacao_suplementada.valor AS vl_suplementada
                            ,CASE WHEN suplementacao_anulada.cod_suplementacao IS NOT NULL THEN 
                                        'anulada'
                                    ELSE 
                                        'valida'
                             END as situacao
                        FROM orcamento.suplementacao 
                        
                   LEFT JOIN orcamento.suplementacao_anulada
                          ON suplementacao.exercicio          = suplementacao_anulada.exercicio                         
                         AND suplementacao.cod_suplementacao  = suplementacao_anulada.cod_suplementacao                        
                  
                   INNER JOIN contabilidade.tipo_transferencia
                           ON tipo_transferencia.cod_tipo  = suplementacao.cod_tipo
                          AND tipo_transferencia.exercicio = suplementacao.exercicio
                  
                  INNER JOIN orcamento.suplementacao_suplementada                                                                       
                          ON suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                         AND suplementacao_suplementada.exercicio         = suplementacao.exercicio
                  
                  INNER JOIN orcamento.despesa                                                                    
                          ON despesa.cod_despesa = suplementacao_suplementada.cod_despesa
                         AND despesa.exercicio   = suplementacao_suplementada.exercicio
                  
                  INNER JOIN orcamento.conta_despesa
                          ON conta_despesa.exercicio = despesa.exercicio
                         AND conta_despesa.cod_conta = despesa.cod_conta
                  
                  INNER JOIN contabilidade.transferencia_despesa                                   
                          ON transferencia_despesa.cod_tipo          = suplementacao.cod_tipo
                         AND transferencia_despesa.cod_suplementacao = suplementacao.cod_suplementacao
                         AND transferencia_despesa.exercicio         = suplementacao.exercicio
                         AND transferencia_despesa.cod_tipo  <> 16
                  
                  INNER JOIN normas.norma                                                          
                         ON suplementacao.cod_norma = norma.cod_norma
                  
                  INNER JOIN orcamento.unidade
                          ON unidade.exercicio   = despesa.exercicio
                         AND unidade.num_unidade = despesa.num_unidade
                         AND unidade.num_orgao   = despesa.num_orgao
                  
                  INNER JOIN tcemg.uniorcam
                          ON uniorcam.num_unidade = unidade.num_unidade
                         AND uniorcam.num_orgao   = unidade.num_orgao
                         AND uniorcam.exercicio   = unidade.exercicio
                  
                  INNER JOIN orcamento.funcao
                          ON funcao.exercicio  = despesa.exercicio
                         AND funcao.cod_funcao = despesa.cod_funcao
                  
                  INNER JOIN orcamento.subfuncao
                          ON subfuncao.exercicio     = despesa.exercicio
                         AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
                  
                  INNER JOIN orcamento.programa
                          ON orcamento.programa.exercicio    = despesa.exercicio
                         AND orcamento.programa.cod_programa = despesa.cod_programa

                  INNER JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.cod_programa = orcamento.programa.cod_programa
                         AND programa_ppa_programa.exercicio    = orcamento.programa.exercicio

                  INNER JOIN ppa.programa
                          ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                  
                  INNER JOIN orcamento.pao
                          ON despesa.num_pao   = pao.num_pao
                         AND despesa.exercicio = pao.exercicio 

                 INNER JOIN orcamento.pao_ppa_acao 
                         ON pao_ppa_acao.num_pao   = pao.num_pao
                        AND pao_ppa_acao.exercicio = pao.exercicio 
                        
                  INNER JOIN ppa.acao
                          ON acao.cod_acao = pao_ppa_acao.cod_acao
                  
                  INNER JOIN orcamento.recurso
                          ON recurso.exercicio   = despesa.exercicio
                         AND recurso.cod_recurso = despesa.cod_recurso
                  
                       WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'        
                         AND suplementacao.dt_suplementacao >= to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')                  
                         AND suplementacao.dt_suplementacao <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')                    
                         AND despesa.cod_entidade IN (".$this->getDado('entidade').")
                         
                    ) AS tabela
                  
                   WHERE
                       tabela.situacao = 'valida'
                    GROUP BY                                                                   
                        dt_suplementacao
                        ,codreduzidodecreto
                        ,origemrecAlteracao
                        ,num_norma
                        ,codorgao        
                        ,codunidadesub
                        ,codfuncao
                        ,codsubfuncao
                        ,codprograma
                        ,idacao
                        ,idsubacao
                        ,codfontrecurso
                        ,naturezadespesa
                        ,tipoalteracao
                        ,situacao
                  
                  UNION
                  
                       SELECT                  
                        tabela.dt_suplementacao
                        ,'14' AS tiporegistro
                        ,LPAD(tabela.num_norma||tabela.cod_tipo, 8,'0' ) as codreduzidodecreto
                        , origemrecalteracao as origemrecalteracao
                        ,LPAD((SELECT valor FROM administracao.configuracao_entidade WHERE exercicio = '".$this->getDado('exercicio')."' AND cod_entidade = tabela.cod_entidade AND parametro = 'tcemg_codigo_orgao_entidade_sicom'), 2, '0') AS codorgao
                        ,LPAD(lpad(tabela.num_orgao::VARCHAR, 2, '0')||LPAD(tabela.num_unidade::VARCHAR, 2, '0'),5,'0') AS codunidadesub
                        ,tabela.cod_funcao AS codfuncao
                        ,tabela.cod_subfuncao AS codsubfuncao
                        ,tabela.cod_programa AS codprograma
                        ,tabela.cod_acao AS idacao
                        ,'' AS idsubacao
                        ,tabela.cod_recurso AS codfontrecurso
                        , CASE WHEN SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6) = '339009' OR SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6) = '339005'
                                    THEN
                                        '319005'
                                    ELSE
                                    CASE WHEN SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6) = '339013'
                                        THEN
                                            '319013'
                                        ELSE
                                            SUBSTR(REPLACE(tabela.cod_estrutural, '.', ''), 1, 6)::VARCHAR
                                    END
                            END AS naturezadespesa
                        ,'2' AS tipoalteracao
                        ,SUM(tabela.vl_reducao) AS vlacrescimoreducao
                           
                   FROM (                                                                      
                       SELECT
                             CASE WHEN EXTRACT(MONTH FROM suplementacao.dt_suplementacao) <> EXTRACT(MONTH FROM norma.dt_assinatura) THEN
                                        TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy')
                                  ELSE	TO_CHAR(norma.dt_assinatura,'ddmmyyyy')
                             END AS data
                            ,norma.nom_norma||' '||norma.num_norma||'/'||norma.exercicio as fundamentacao
                            ,norma.num_norma
                            ,norma.cod_norma
                            ,tipo_transferencia.nom_tipo as tipo_suplementacao
                            ,tipo_transferencia.cod_tipo
                            ,despesa.cod_entidade
                            ,suplementacao.cod_suplementacao
                            ,suplementacao.exercicio
                            ,suplementacao.dt_suplementacao
                            ,CASE WHEN suplementacao.cod_tipo = 1 THEN '03'
                               WHEN suplementacao.cod_tipo = 2 THEN '04'
                               WHEN suplementacao.cod_tipo = 4 THEN '02'
                               WHEN suplementacao.cod_tipo = 5 THEN '01'
                               WHEN suplementacao.cod_tipo = 6 THEN '03'
                               WHEN suplementacao.cod_tipo = 7 THEN '04'
                               WHEN suplementacao.cod_tipo = 9 THEN '02'
                               WHEN suplementacao.cod_tipo = 10 THEN '01'
                               WHEN suplementacao.cod_tipo = 11 THEN '98' 
                               WHEN suplementacao.cod_tipo = 12 THEN '98'
                               WHEN suplementacao.cod_tipo = 13 THEN '98'
                               WHEN suplementacao.cod_tipo = 14 THEN '98'
                               WHEN suplementacao.cod_tipo = 15 THEN '98'
                            END AS origemrecalteracao
                            ,uniorcam.num_orgao
                            ,uniorcam.num_unidade
                            ,funcao.cod_funcao
                            ,subfuncao.cod_subfuncao
                            ,ppa.programa.num_programa AS cod_programa
                            ,ppa.acao.num_acao AS cod_acao
                            ,recurso.cod_recurso
                            ,conta_despesa.cod_estrutural
                            ,suplementacao_reducao.valor AS vl_reducao
                            ,despesa.cod_despesa                  
                            ,CASE WHEN suplementacao_anulada.cod_suplementacao IS NOT NULL THEN 
                                        'anulada'
                                    ELSE 
                                        'valida'
                             END as situacao
                                                                                
                        FROM orcamento.suplementacao 
                        
                   LEFT JOIN orcamento.suplementacao_anulada
                          ON suplementacao.exercicio          = suplementacao_anulada.exercicio                         
                         AND suplementacao.cod_suplementacao  = suplementacao_anulada.cod_suplementacao                        
                  
                   INNER JOIN contabilidade.tipo_transferencia
                           ON tipo_transferencia.cod_tipo  = suplementacao.cod_tipo
                          AND tipo_transferencia.exercicio = suplementacao.exercicio
                  
                  INNER JOIN orcamento.suplementacao_reducao                                                                       
                          ON suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                         AND suplementacao_reducao.exercicio         = suplementacao.exercicio
                  
                  INNER JOIN orcamento.despesa                                                                    
                          ON despesa.cod_despesa = suplementacao_reducao.cod_despesa
                         AND despesa.exercicio   = suplementacao_reducao.exercicio
                  
                  INNER JOIN orcamento.conta_despesa
                          ON conta_despesa.exercicio = despesa.exercicio
                         AND conta_despesa.cod_conta = despesa.cod_conta
                  
                  INNER JOIN contabilidade.transferencia_despesa                                   
                          ON transferencia_despesa.cod_tipo          = suplementacao.cod_tipo
                         AND transferencia_despesa.cod_suplementacao = suplementacao.cod_suplementacao
                         AND transferencia_despesa.exercicio         = suplementacao.exercicio
                         AND transferencia_despesa.cod_tipo          <> 16
                  
                  INNER JOIN normas.norma                                                          
                         ON suplementacao.cod_norma = norma.cod_norma
                  
                  INNER JOIN orcamento.unidade
                          ON unidade.exercicio   = despesa.exercicio
                         AND unidade.num_unidade = despesa.num_unidade
                         AND unidade.num_orgao   = despesa.num_orgao
                  
                  INNER JOIN tcemg.uniorcam
                          ON uniorcam.num_unidade = unidade.num_unidade
                         AND uniorcam.num_orgao   = unidade.num_orgao
                         AND uniorcam.exercicio   = unidade.exercicio
                  
                  INNER JOIN orcamento.funcao
                          ON funcao.exercicio  = despesa.exercicio
                         AND funcao.cod_funcao = despesa.cod_funcao
                  
                  INNER JOIN orcamento.subfuncao
                          ON subfuncao.exercicio     = despesa.exercicio
                         AND subfuncao.cod_subfuncao = despesa.cod_subfuncao
                  
                  INNER JOIN orcamento.programa
                          ON programa.exercicio    = despesa.exercicio
                         AND programa.cod_programa = despesa.cod_programa
                         
                  INNER JOIN orcamento.programa_ppa_programa
                          ON programa_ppa_programa.exercicio    = despesa.exercicio
                         AND programa_ppa_programa.cod_programa = despesa.cod_programa    

                  INNER JOIN ppa.programa 
                         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa               
                  
                  INNER JOIN orcamento.despesa_acao
                          ON despesa_acao.exercicio_despesa  = despesa.exercicio
                         AND despesa_acao.cod_despesa        = despesa.cod_despesa       
                   
                 INNER JOIN orcamento.pao
                         ON despesa.num_pao   = pao.num_pao
                        AND despesa.exercicio = pao.exercicio 

                 INNER JOIN orcamento.pao_ppa_acao 
                         ON pao_ppa_acao.num_pao   = pao.num_pao
                        AND pao_ppa_acao.exercicio = pao.exercicio 
                        
                 INNER JOIN ppa.acao
                         ON acao.cod_acao = pao_ppa_acao.cod_acao
                          
                  INNER JOIN orcamento.recurso
                          ON recurso.exercicio   = despesa.exercicio
                         AND recurso.cod_recurso = despesa.cod_recurso
                  
                       WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'        
                         AND suplementacao.dt_suplementacao >= to_date('".$this->getDado('dt_inicial')."','dd/mm/yyyy')                  
                         AND suplementacao.dt_suplementacao <= to_date('".$this->getDado('dt_final')."','dd/mm/yyyy')                    
                         AND despesa.cod_entidade IN (".$this->getDado('entidade').")
                         
                    ) AS tabela
                  
                   WHERE
                       tabela.situacao = 'valida'
                    GROUP BY                                                                   
                        dt_suplementacao
                        ,codreduzidodecreto
                        ,origemrecAlteracao
                        ,num_norma
                        ,codorgao        
                        ,codunidadesub
                        ,codfuncao
                        ,codsubfuncao
                        ,codprograma
                        ,idacao
                        ,idsubacao
                        ,codfontrecurso
                        ,naturezadespesa
                        ,tipoalteracao
                        ,situacao";
        return $stSql;
    }

    public function __destruct(){}

}
?>
