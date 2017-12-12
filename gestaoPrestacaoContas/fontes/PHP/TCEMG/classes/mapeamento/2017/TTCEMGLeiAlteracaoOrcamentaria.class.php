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

class TTCEMGLeiAlteracaoOrcamentaria extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGLeiAlteracaoOrcamentaria()
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
        $stSql = "
                    SELECT 10 AS tipo_registro
                         , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                         , norma.num_norma AS nro_lei_alteracao
                         , TO_CHAR(suplementacao.dt_suplementacao,'ddmmyyyy') AS dt_lei_alteracao

                      FROM orcamento.suplementacao

                 LEFT JOIN orcamento.suplementacao_suplementada
                        ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                       AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao

                 LEFT JOIN orcamento.suplementacao_reducao
                        ON suplementacao_reducao.exercicio = suplementacao.exercicio
                       AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao

                INNER JOIN orcamento.despesa
                        ON despesa.exercicio = suplementacao_suplementada.exercicio
                       AND despesa.cod_despesa = suplementacao_suplementada.cod_despesa

                INNER JOIN normas.norma
                        ON norma.cod_norma = suplementacao.cod_norma

                INNER JOIN administracao.configuracao_entidade
                        ON configuracao_entidade.cod_entidade  = despesa.cod_entidade
                       AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                       AND configuracao_entidade.cod_modulo = 55
                       AND configuracao_entidade.exercicio = despesa.exercicio

                INNER JOIN tcemg.norma_detalhe
                        ON norma_detalhe.cod_norma = norma.cod_norma
                       AND norma_detalhe.tipo_lei_origem_decreto = 3 

                     WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
                       AND despesa.cod_entidade IN (".$this->getDado('entidades').")
                       AND suplementacao.dt_suplementacao BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                       AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                       AND NOT EXISTS
                           (
                            SELECT 1
                              FROM orcamento.suplementacao_anulada
                             WHERE (
                                         suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                    OR
                                         suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                   )
                               AND suplementacao_anulada.exercicio = suplementacao.exercicio
                           )

                  GROUP BY nro_lei_alteracao, dt_lei_alteracao, cod_orgao, tipo_registro
                  ORDER BY dt_lei_alteracao, nro_lei_alteracao
        ";
        return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = " SELECT tabela.tipo_registro
                        , tabela.nro_lei_alteracao 
                        , tabela.tipo_lei_alteracao
                        , tabela.vl_autorizacao_alteracao
                        , CASE WHEN tabela.artigo_lei_alteracao IS NOT NULL THEN
                                        regexp_replace(remove_acentos(REPLACE((REPLACE(REPLACE(tabela.artigo_lei_alteracao,Chr(39), ''), Chr(10), '')), Chr(13), ' ')),'[º|°]', '', 'gi')
                          END AS artigo_lei_alteracao
                        , CASE WHEN tabela.descricao_artigo IS NOT NULL THEN
                                        TRIM(regexp_replace(remove_acentos(REPLACE(REPLACE(REPLACE(REPLACE(tabela.descricao_artigo,Chr(39), ''), Chr(10), ''), Chr(13), ' '), Chr(9), ' ')),'[º|°|%|§]', '', 'gi'))
                          END AS descricao_artigo
                    FROM (
                            SELECT 11 AS tipo_registro
                                 , norma.num_norma AS nro_lei_alteracao
                                 , tipo_lei_alteracao_orcamentaria.cod_tipo_lei AS tipo_lei_alteracao          
                                 , CASE WHEN tipo_transferencia.cod_tipo = 15 THEN LPAD('0,00', 14, '0')
                                        WHEN tipo_transferencia.cod_tipo = 16 THEN LPAD('0,00', 14, '0')
                                        ELSE LPAD(REPLACE(SUM(coalesce( suplementacao_suplementada.valor, 0.00 ))::varchar,'.',','), 14,'0')
                                   END AS vl_autorizacao_alteracao
                                 , ( SELECT atributo_norma_valor.valor
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
                                        AND atributo_dinamico.nom_atributo ILIKE 'Artigo da Lei'
                                        AND atributo_norma_valor.cod_norma = norma.cod_norma
                                        AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                                 FROM normas.atributo_norma_valor AS anv 
                                                                                WHERE cod_norma = atributo_norma_valor.cod_norma
                                                                                  AND cod_cadastro = atributo_norma_valor.cod_cadastro
                                                                                  AND cod_modulo =  atributo_norma_valor.cod_modulo
                                                                                  AND cod_atributo =  atributo_norma_valor.cod_atributo
                                                                              )
                                   ) AS artigo_lei_alteracao
                                 , COALESCE ( (   SELECT atributo_norma_valor.valor
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
                                                     AND atributo_dinamico.cod_atributo = 5004
                                                     AND atributo_norma_valor.cod_norma = norma.cod_norma
                                                     AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                                              FROM normas.atributo_norma_valor AS anv 
                                                                                             WHERE cod_norma = atributo_norma_valor.cod_norma
                                                                                               AND cod_cadastro = atributo_norma_valor.cod_cadastro
                                                                                               AND cod_modulo =  atributo_norma_valor.cod_modulo
                                                                                               AND cod_atributo =  atributo_norma_valor.cod_atributo
                                                                                           ) 
                                              ) , ' ') AS descricao_artigo
                              FROM orcamento.suplementacao

                        INNER JOIN normas.norma
                                ON norma.cod_norma = suplementacao.cod_norma
                              
                         LEFT JOIN tcemg.norma_artigo
                                ON norma_artigo.cod_norma = norma.cod_norma
                              
                         LEFT JOIN ( SELECT OSS.exercicio                                            
                                           , OSS.cod_suplementacao                                    
                                           , MAX( OSS.cod_despesa ) as cod_despesa                    
                                           , MAX( RECURSO.cod_recurso ) as cod_recurso                
                                           , sum( OSS.valor ) as valor                                
                                           , OD.cod_entidade
                                        FROM orcamento.suplementacao_suplementada AS OSS                
                                           , orcamento.despesa                    AS OD                 
                                           , orcamento.recurso('".$this->getDado('exercicio')."')  AS RECURSO
                                       WHERE OSS.cod_despesa = OD.cod_despesa                        
                                         AND OSS.exercicio   = OD.exercicio                          
                                         AND OD.cod_recurso  = RECURSO.cod_recurso                   
                                         AND OD.exercicio    = RECURSO.exercicio                     
                                    GROUP BY OSS.exercicio                                          
                                           , OSS.cod_suplementacao                                  
                                           , RECURSO.cod_recurso                                    
                                           , OD.cod_entidade                                          
                                    ORDER BY OSS.exercicio                                          
                                           , OSS.cod_suplementacao                                  
                                           , RECURSO.cod_recurso                                    
                                   ) AS suplementacao_suplementada
                                ON suplementacao.exercicio         = suplementacao_suplementada.exercicio                           
                               AND suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
                             
                         LEFT JOIN ( SELECT OSR.exercicio                                            
                                           , OSR.cod_suplementacao                                    
                                           , MAX( OSR.cod_despesa ) as cod_despesa                    
                                           , sum( OSR.valor ) AS valor                                
                                           , OD.cod_entidade                                          
                                        FROM orcamento.suplementacao_reducao AS OSR                     
                                  INNER JOIN orcamento.despesa AS OD                 
                                          ON OSR.cod_despesa = OD.cod_despesa                           
                                         AND OSR.exercicio   = OD.exercicio                             
                                    GROUP BY OSR.exercicio                                          
                                           , OSR.cod_suplementacao                                  
                                           , OD.cod_entidade                                          
                                    ORDER BY OSR.exercicio                                          
                                           , OSR.cod_suplementacao                                  
                                   ) AS suplementacao_reducao
                                ON suplementacao.exercicio         = suplementacao_reducao.exercicio                           
                               AND suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao 
                         
                         LEFT JOIN contabilidade.tipo_transferencia                   
                                ON suplementacao.cod_tipo          = tipo_transferencia.cod_tipo                           
                               AND suplementacao.exercicio         = tipo_transferencia.exercicio
                         
                         LEFT JOIN contabilidade.transferencia_despesa                 
                                ON suplementacao.cod_tipo          = transferencia_despesa.cod_tipo                           
                               AND suplementacao.exercicio         = transferencia_despesa.exercicio                          
                               AND suplementacao.cod_suplementacao = transferencia_despesa.cod_suplementacao
                  
                        INNER JOIN tcemg.norma_detalhe
                                ON norma_detalhe.cod_norma                 = norma.cod_norma
                               AND norma_detalhe.tipo_lei_origem_decreto   = 3 
                            
                         LEFT JOIN tcemg.tipo_lei_alteracao_orcamentaria
                                ON tipo_lei_alteracao_orcamentaria.cod_tipo_lei = norma_detalhe.tipo_lei_alteracao_orcamentaria
                            
                             WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
                               AND (suplementacao_suplementada.cod_entidade IN (".$this->getDado('entidades').") OR suplementacao_reducao.cod_entidade IN (".$this->getDado('entidades')."))
                               AND suplementacao.dt_suplementacao BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                               AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                               AND suplementacao.cod_tipo in (1,2,3,4,5,6,7,8,9,10,11,12,13,14)
                               AND NOT EXISTS
                                   (
                                    SELECT 1
                                      FROM orcamento.suplementacao_anulada
                                     WHERE (
                                                suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                            OR
                                                suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                           )
                                       AND suplementacao_anulada.exercicio = suplementacao.exercicio
                                   )
                           
                          GROUP BY nro_lei_alteracao
                                 , tipo_lei_alteracao
                                 , tipo_transferencia.cod_tipo
                                 , norma.cod_norma
                                 
                          ORDER BY nro_lei_alteracao
                                 , tipo_lei_alteracao
                         ) AS tabela
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
                           '20' AS tipo_registro
                         , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                         , COALESCE ( (   SELECT DISTINCT atributo_norma_valor.valor
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
                                             AND atributo_norma_valor.cod_norma = norma.cod_norma
                                             AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                                      FROM normas.atributo_norma_valor AS anv 
                                                                                     WHERE cod_norma = atributo_norma_valor.cod_norma 
                                                                                       AND cod_cadastro = atributo_norma_valor.cod_cadastro 
                                                                                       AND cod_modulo =  atributo_norma_valor.cod_modulo 
                                                                                       AND cod_atributo =  atributo_norma_valor.cod_atributo
                                                                                       AND atributo_dinamico.cod_atributo = 103 ) 
                                       ) , ' ')  AS nro_lei_alter_orcam
                         , ( SELECT DISTINCT REPLACE(atributo_norma_valor.valor,'/','') as valor
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
                                AND atributo_dinamico.cod_atributo = 104
                                AND atributo_norma_valor.cod_norma = norma.cod_norma
                                AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                         FROM normas.atributo_norma_valor AS anv 
                                                                        WHERE cod_norma = atributo_norma_valor.cod_norma 
                                                                          AND cod_cadastro = atributo_norma_valor.cod_cadastro 
                                                                          AND cod_modulo =  atributo_norma_valor.cod_modulo 
                                                                          AND cod_atributo =  atributo_norma_valor.cod_atributo
                                                                          AND atributo_dinamico.cod_atributo = 104 ) 
                           ) AS dt_lei_alter_orcam
                            
                      FROM orcamento.suplementacao

                 LEFT JOIN orcamento.suplementacao_suplementada
                        ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                       AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                     
                 LEFT JOIN orcamento.suplementacao_reducao
                        ON suplementacao_reducao.exercicio = suplementacao.exercicio
                       AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                     
                INNER JOIN orcamento.despesa
                        ON despesa.exercicio = suplementacao_suplementada.exercicio
                       AND despesa.cod_despesa = suplementacao_suplementada.cod_despesa
                     
                INNER JOIN normas.norma
                        ON norma.cod_norma = suplementacao.cod_norma
                        
                INNER JOIN normas.atributo_norma_valor
                        ON atributo_norma_valor.cod_norma = norma.cod_norma
                    
                INNER JOIN normas.atributo_tipo_norma
                        ON atributo_tipo_norma.cod_tipo_norma = atributo_norma_valor.cod_tipo_norma
                       AND atributo_tipo_norma.cod_modulo     = atributo_norma_valor.cod_modulo
                       AND atributo_tipo_norma.cod_cadastro   = atributo_norma_valor.cod_cadastro
                       AND atributo_tipo_norma.cod_atributo   = atributo_norma_valor.cod_atributo
                    
                INNER JOIN administracao.atributo_dinamico
                        ON atributo_dinamico.cod_modulo     = atributo_tipo_norma.cod_modulo    
                       AND atributo_dinamico.cod_cadastro   = atributo_tipo_norma.cod_cadastro  
                       AND atributo_dinamico.cod_atributo   = atributo_tipo_norma.cod_atributo  
                    
                INNER JOIN administracao.configuracao_entidade
                        ON configuracao_entidade.cod_entidade  = despesa.cod_entidade
                       AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                       AND configuracao_entidade.cod_modulo = 55
                       AND configuracao_entidade.exercicio = despesa.exercicio
                     
                INNER JOIN tcemg.norma_detalhe
                        ON norma_detalhe.cod_norma                 = norma.cod_norma
                       AND norma_detalhe.tipo_lei_origem_decreto   = 3 
                     
                     WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
                       AND despesa.cod_entidade IN (".$this->getDado('entidades').")
                       AND suplementacao.dt_suplementacao BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                       AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                       AND atributo_tipo_norma.ativo = TRUE
                       AND TRIM(atributo_dinamico.nom_atributo) ILIKE 'Altera Percentual Suplementações'
                       AND atributo_norma_valor.valor = '1'
                       AND NOT EXISTS
                            (
                             SELECT 1
                               FROM orcamento.suplementacao_anulada
                              WHERE (
                                         suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                     OR
                                         suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                    )
                                AND suplementacao_anulada.exercicio = suplementacao.exercicio
                            )

                  GROUP BY tipo_registro, cod_orgao, nro_lei_alter_orcam, dt_lei_alter_orcam
                  ORDER BY nro_lei_alter_orcam, dt_lei_alter_orcam, cod_orgao ";
        
        return $stSql;
    }

    public function recuperaExportacao21(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao21",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao21()
    {
        $stSql = "
        SELECT DISTINCT tabela.tipo_registro
             , nro_lei_alter_orcam
             , CASE WHEN tabela.artigo_lei_alter_orcamento IS NOT NULL THEN
                            regexp_replace(remove_acentos(REPLACE((REPLACE(REPLACE(tabela.artigo_lei_alter_orcamento,Chr(39), ''), Chr(10), '')), Chr(13), ' ')),'[º|°]', '', 'gi')
               END AS artigo_lei_alter_orcamento
             , CASE WHEN tabela.descricao_artigo IS NOT NULL THEN
                            TRIM(regexp_replace(remove_acentos(REPLACE(REPLACE(REPLACE(REPLACE(tabela.descricao_artigo,Chr(39), ''), Chr(10), ''), Chr(13), ' '), Chr(9), ' ')),'[º|°|%|§]', '', 'gi'))
               END AS descricao_artigo
             , tabela.novo_percentual
             , tabela.tipo_autorizacao::VARCHAR
          FROM (
                SELECT '21' AS tipo_registro
                     , COALESCE (( SELECT DISTINCT atributo_norma_valor.valor
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
                                      AND atributo_norma_valor.cod_norma = norma.cod_norma
                                      AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                               FROM normas.atributo_norma_valor AS anv 
                                                                              WHERE cod_norma = atributo_norma_valor.cod_norma 
                                                                                AND cod_cadastro = atributo_norma_valor.cod_cadastro 
                                                                                AND cod_modulo =  atributo_norma_valor.cod_modulo 
                                                                                AND cod_atributo =  atributo_norma_valor.cod_atributo
                                                                                AND atributo_dinamico.cod_atributo = 103 ) 
                                 ) , ' ') AS nro_lei_alter_orcam
                     , ( SELECT atributo_norma_valor.valor
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
                            AND TRIM(atributo_dinamico.nom_atributo) ILIKE 'Artigo da Lei'
                            AND atributo_norma_valor.cod_norma = norma.cod_norma
                            AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                     FROM normas.atributo_norma_valor AS anv 
                                                                    WHERE cod_norma = atributo_norma_valor.cod_norma
                                                                      AND cod_cadastro = atributo_norma_valor.cod_cadastro 
                                                                      AND cod_modulo =  atributo_norma_valor.cod_modulo
                                                                      AND cod_atributo =  atributo_norma_valor.cod_atributo ) 
                       ) AS artigo_lei_alter_orcamento
                     , COALESCE (( SELECT atributo_norma_valor.valor
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
                                      AND atributo_dinamico.cod_atributo = 5004
                                      AND atributo_norma_valor.cod_norma = norma.cod_norma
                                      AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                               FROM normas.atributo_norma_valor AS anv
                                                                              WHERE cod_norma = atributo_norma_valor.cod_norma
                                                                                AND cod_cadastro = atributo_norma_valor.cod_cadastro
                                                                                AND cod_modulo =  atributo_norma_valor.cod_modulo
                                                                                AND cod_atributo =  atributo_norma_valor.cod_atributo ) 
                                 ), ' ' ) AS descricao_artigo
                     , COALESCE (( SELECT atributo_norma_valor.valor AS valor
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
                                      AND TRIM(atributo_dinamico.nom_atributo) ILIKE 'Novo Percentual Suplementações'
                                      AND atributo_norma_valor.cod_norma = norma.cod_norma
                                      AND atributo_norma_valor.timestamp = ( SELECT MAX(timestamp)
                                                                               FROM normas.atributo_norma_valor AS anv
                                                                              WHERE cod_norma = atributo_norma_valor.cod_norma
                                                                                AND cod_cadastro = atributo_norma_valor.cod_cadastro
                                                                                AND cod_modulo =  atributo_norma_valor.cod_modulo
                                                                                AND cod_atributo =  atributo_norma_valor.cod_atributo ) 
                                 ), '0.00') AS novo_percentual 
                     , CASE WHEN suplementacao.cod_tipo IN (1,2,3,4,5) THEN
                                     1
                            ELSE
                                     2
                       END AS tipo_autorizacao
                
                  FROM orcamento.suplementacao
                  
             LEFT JOIN orcamento.suplementacao_suplementada
                    ON suplementacao_suplementada.exercicio = suplementacao.exercicio
                   AND suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                   
             LEFT JOIN orcamento.suplementacao_reducao
                    ON suplementacao_reducao.exercicio = suplementacao.exercicio
                   AND suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                   
            INNER JOIN orcamento.despesa
                    ON despesa.exercicio = suplementacao_suplementada.exercicio
                   AND despesa.cod_despesa = suplementacao_suplementada.cod_despesa
                   
            INNER JOIN normas.norma
                    ON norma.cod_norma = suplementacao.cod_norma
                   AND suplementacao.cod_suplementacao = (select max(oc.cod_suplementacao) from orcamento.suplementacao as oc where oc.cod_norma = norma.cod_norma AND oc.exercicio = suplementacao.exercicio)
                
            INNER JOIN normas.atributo_norma_valor
                    ON atributo_norma_valor.cod_norma = norma.cod_norma
                
            INNER JOIN normas.atributo_tipo_norma
                    ON atributo_tipo_norma.cod_tipo_norma = atributo_norma_valor.cod_tipo_norma
                   AND atributo_tipo_norma.cod_modulo     = atributo_norma_valor.cod_modulo
                   AND atributo_tipo_norma.cod_cadastro   = atributo_norma_valor.cod_cadastro
                   AND atributo_tipo_norma.cod_atributo   = atributo_norma_valor.cod_atributo
                
            INNER JOIN administracao.atributo_dinamico
                    ON atributo_dinamico.cod_modulo     = atributo_tipo_norma.cod_modulo    
                   AND atributo_dinamico.cod_cadastro   = atributo_tipo_norma.cod_cadastro  
                   AND atributo_dinamico.cod_atributo   = atributo_tipo_norma.cod_atributo  
                
            INNER JOIN administracao.configuracao_entidade
                    ON configuracao_entidade.cod_entidade  = despesa.cod_entidade
                   AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                   AND configuracao_entidade.cod_modulo = 55
                   AND configuracao_entidade.exercicio = despesa.exercicio
                   
            INNER JOIN tcemg.norma_detalhe
                    ON norma_detalhe.cod_norma                 = norma.cod_norma
                   AND norma_detalhe.tipo_lei_origem_decreto   = 3 
             
                 WHERE suplementacao.exercicio = '".$this->getDado('exercicio')."'
                   AND despesa.cod_entidade IN (".$this->getDado('entidades').")
                   AND suplementacao.dt_suplementacao BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                   AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                   AND atributo_tipo_norma.ativo = TRUE
                   AND TRIM(atributo_dinamico.nom_atributo) ILIKE 'Altera Percentual Suplementações'
                   AND TRIM(atributo_norma_valor.valor) = '1'
                   AND NOT EXISTS
                        (
                         SELECT 1
                           FROM orcamento.suplementacao_anulada
                          WHERE (
                                     suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 OR
                                     suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                )
                            AND suplementacao_anulada.exercicio = suplementacao.exercicio
                        )
                                   
              GROUP BY tipo_registro,  nro_lei_alter_orcam, norma.cod_norma, suplementacao.cod_tipo
               ) AS tabela
      GROUP BY 1,2,3,4,5,6
            ";
        
        return $stSql;
    }
    
    public function __destruct(){}

}
