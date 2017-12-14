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
    * Classe de mapeamento do arquivo e exportação ARC
    * Data de Criação   : 29/05/2015

    * @author Analista      Ane Pereira
    * @author Desenvolvedor Arthur Cruz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: TTCEMGExportacaoREC.class.php 62302 2015-04-20 17:54:18Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGExportacaoREC extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGExportacaoREC()
    {
        parent::Persistente();
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método recuperaReceitaExportacao10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaReceitaExportacao10(&$rsRecordSet, $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaExportacao10();
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
    }
    
    function montaRecuperaReceitaExportacao10()
    {
    $stSql = "
        SELECT tipo_registro
         , cod_receita_final AS cod_receita
         , cod_orgao
         , deducao_receita
         , identificador_deducao
         , CASE WHEN SUBSTR(natureza_receita::text, 1, 1) = '9'
            THEN SUBSTR(natureza_receita::text, 2, 8)::integer
            ELSE natureza_receita
            END AS natureza_receita
         , remove_acentos(especificacao) as especificacao
         , CASE WHEN SUBSTR(cod_receita_final::VARCHAR, 1, 1) = '9'
            THEN REPLACE(REPLACE(sum(tabela.vl_previsto)::VARCHAR,'.',','),'-','')
            ELSE REPLACE(sum(tabela.vl_previsto)::VARCHAR,'.',',')
            END AS vl_previsto
          
          FROM (
               SELECT 10::integer AS tipo_registro
            , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                   THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                   ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                     WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
                     THEN '24210101'
                     WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
                     THEN '19319900'
                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                     END
              END AS cod_receita_final
            , configuracao_entidade.valor AS cod_orgao
            , rec.masc_recurso_red AS recurso
            , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                   THEN 1
                   ELSE 2
               END AS deducao_receita
            , valores_identificadores.cod_identificador::integer AS identificador_deducao
            , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                   THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                   ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                     WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
                     THEN '24210101'
                     WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
                     THEN '19319900'
                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                     END
              END AS natureza_receita
            , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                   THEN (SELECT TRIM(o_cr.descricao)
                       FROM orcamento.conta_receita AS o_cr
                      WHERE o_cr.exercicio ='".Sessao::getExercicio()."'
                    AND RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9),15,'0') = REPLACE(o_cr.cod_estrutural,'.',''))
                   ELSE (SELECT TRIM(descricao)
                       FROM orcamento.conta_receita AS o_cr
                      WHERE o_cr.exercicio ='".Sessao::getExercicio()."'
                    AND RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8),14,'0') = REPLACE(o_cr.cod_estrutural,'.',''))
              END AS especificacao
            , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                   THEN detalhamento_receitas.arrecadado_periodo
                   ELSE    ABS(detalhamento_receitas.arrecadado_periodo)
              END AS vl_previsto
             
             FROM orcamento.receita
    
        LEFT JOIN orcamento.recurso('".Sessao::getExercicio()."') AS rec 
               ON rec.cod_recurso = receita.cod_recurso
              AND rec.exercicio   = receita.exercicio                 
             
           INNER JOIN orcamento.conta_receita
               ON conta_receita.cod_conta = receita.cod_conta
              AND conta_receita.exercicio = receita.exercicio
    
           INNER JOIN administracao.configuracao_entidade
               ON configuracao_entidade.cod_entidade = receita.cod_entidade
              AND configuracao_entidade.exercicio    = receita.exercicio
              
           INNER JOIN tcemg.fn_detalhamento_receitas('".Sessao::getExercicio()."','','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."','".$this->getDado('entidades')."','','','','','','','') 
               AS detalhamento_receitas (                      
                            cod_estrutural      varchar,                                           
                            receita             integer,                                           
                            recurso             varchar,                                           
                            descricao           varchar,                                           
                            valor_previsto      numeric,                                           
                            arrecadado_periodo  numeric,                                           
                            arrecadado_ano      numeric,                                           
                            diferenca           numeric                                           
            ) ON detalhamento_receitas.cod_estrutural = conta_receita.cod_estrutural
             AND SUBSTR(detalhamento_receitas.cod_estrutural, 1, 1) != '9'
    
        LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
               ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
              AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
    
        LEFT JOIN tcemg.valores_identificadores
               ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
    
             WHERE receita.exercicio = '".Sessao::getExercicio()."'
               AND receita.cod_entidade IN (".$this->getDado('entidades').")
               AND configuracao_entidade.cod_modulo = 55
               AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
               --AND receita.vl_original <> 0.00
               AND conta_receita.cod_conta NOT IN (384) -- Retirado esta conta devido a erro de cadastro do wallace, sendo cadastrada duas vezes.
               
         GROUP BY  cod_receita_final
             , conta_receita.cod_estrutural
             , conta_receita.descricao
             , cod_orgao
             , identificador_deducao
             , detalhamento_receitas.arrecadado_periodo
             , rec.masc_recurso_red
         
         UNION
    
           SELECT 10::integer AS tipo_registro
            , SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer AS cod_receita_final
            , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
            , rec.masc_recurso_red AS recurso
            , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                   THEN 1
                   ELSE 2
            END AS deducao_receita
            , valores_identificadores.cod_identificador AS indentificador_deducao
            , SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer AS natureza_receita
            , TRIM(conta_receita.descricao) AS especificacao
            , SUM(arrecadacao_receita_dedutora.vl_deducao) AS vl_previsto
    
             FROM orcamento.receita
             
        LEFT JOIN orcamento.recurso('".Sessao::getExercicio()."') as rec 
               ON rec.cod_recurso = receita.cod_recurso
              AND rec.exercicio   = receita.exercicio
    
           INNER JOIN tesouraria.arrecadacao_receita_dedutora
               ON arrecadacao_receita_dedutora.cod_receita_dedutora=receita.cod_receita
              AND arrecadacao_receita_dedutora.exercicio=receita.exercicio
              AND arrecadacao_receita_dedutora.timestamp_arrecadacao::date BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dt_final')."', 'dd/mm/yyyy' )
    
           INNER JOIN administracao.configuracao_entidade
               ON configuracao_entidade.cod_entidade = receita.cod_entidade
              AND configuracao_entidade.exercicio = receita.exercicio
    
           INNER JOIN orcamento.conta_receita
               ON conta_receita.cod_conta = receita.cod_conta
              AND conta_receita.exercicio = receita.exercicio        
    
        LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
               ON receita_indentificadores_peculiar_receita.exercicio = receita.exercicio
              AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
    
        LEFT JOIN tcemg.valores_identificadores
               ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
    
             WHERE receita.exercicio = '".Sessao::getExercicio()."'
               AND receita.cod_entidade IN (".$this->getDado('entidades').")
               AND configuracao_entidade.cod_modulo = 55
               AND configuracao_entidade.parametro = 'tcemg_tipo_orgao_entidade_sicom'
    
         GROUP BY receita.cod_receita
             , receita.exercicio
             , cod_orgao
             , conta_receita.cod_estrutural
             , conta_receita.descricao
             , indentificador_deducao
             , natureza_receita
             , especificacao
             , rec.masc_recurso_red
             
           ) AS tabela
        
           WHERE tabela.vl_previsto<>0.00
        
        GROUP BY tipo_registro
               , cod_orgao
           , deducao_receita
           , identificador_deducao
           , natureza_receita
           , cod_receita
           , especificacao
        
        ORDER BY tabela.natureza_receita ";
    
    return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método recuperaReceitaExportacao11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    function recuperaReceitaExportacao11(&$rsRecordSet, $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReceitaExportacao11();
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
    }
    
    function montaRecuperaReceitaExportacao11()
    {
    $stSql = "
    
        SELECT tipo_registro 
         , cod_receita
         , cod_font_recursos
         , REPLACE(REPLACE(sum(vl_arrecadado_fonte)::VARCHAR,'.',','),'-','') AS vl_arrecadado_fonte
    
           FROM(
            
        SELECT 11 AS tipo_registro
             , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 9)::integer
                   ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                           OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                           OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
                         THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                         WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 24210100
                         THEN '24210101'
                         WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 19319902
                         THEN '19319900'
                         ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                     END
            END AS cod_receita
             , receita.cod_recurso::integer AS cod_font_recursos
             , detalhamento_receitas.arrecadado_periodo AS vl_arrecadado_fonte
          
          FROM orcamento.receita
          
        INNER JOIN orcamento.conta_receita
            ON conta_receita.cod_conta = receita.cod_conta
           AND conta_receita.exercicio = receita.exercicio
           
        INNER JOIN (
                    SELECT cod_estrutural
                 , receita
                 , recurso
                 , descricao
                 , sum(detalhamento.arrecadado_periodo) as arrecadado_periodo
                
              FROM (
                  SELECT * FROM tcemg.fn_detalhamento_receitas('".Sessao::getExercicio()."','','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."','".$this->getDado('entidades')."','','','','','','','')
                      AS detalhamento_receitas(                      
                                cod_estrutural      varchar,                                           
                                receita             integer,                                           
                                recurso             varchar,                                           
                                descricao           varchar,                                           
                                valor_previsto      numeric,                                           
                                arrecadado_periodo  numeric,                                           
                                arrecadado_ano      numeric,                                           
                                diferenca           numeric                                           
                    )
                    WHERE SUBSTR(cod_estrutural, 1, 1) != '9'
        
                UNION 
                
                  SELECT conta_receita.cod_estrutural::varchar AS cod_estrutural
                       , receita.cod_receita AS receita
                       , rec.masc_recurso_red AS recurso
                       , TRIM(conta_receita.descricao)::varchar AS descricao
                       , 0.00::numeric AS valor_previsto
                       , SUM(arrecadacao_receita_dedutora.vl_deducao)::numeric AS arrecadado_periodo
                       , 0.00::numeric AS arrecadado_ano
                       , 0.00::numeric AS diferenca
    
                    FROM orcamento.receita
        
                   LEFT JOIN orcamento.recurso('".Sessao::getExercicio()."') as rec 
                      ON rec.cod_recurso = receita.cod_recurso
                     AND rec.exercicio   = receita.exercicio
                
                  INNER JOIN tesouraria.arrecadacao_receita_dedutora
                      ON arrecadacao_receita_dedutora.cod_receita_dedutora = receita.cod_receita
                     AND arrecadacao_receita_dedutora.exercicio            = receita.exercicio
                     AND arrecadacao_receita_dedutora.timestamp_arrecadacao::date BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dt_final')."', 'dd/mm/yyyy' )
            
                  INNER JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = receita.cod_entidade
                     AND configuracao_entidade.exercicio    = receita.exercicio
            
                  INNER JOIN orcamento.conta_receita
                      ON conta_receita.cod_conta = receita.cod_conta
                     AND conta_receita.exercicio = receita.exercicio

                   LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                      ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
                     AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita
            
                   LEFT JOIN tcemg.valores_identificadores
                      ON valores_identificadores.cod_identificador = receita_indentificadores_peculiar_receita.cod_identificador
            
                   WHERE receita.exercicio    = '".Sessao::getExercicio()."'
                     AND receita.cod_entidade IN (".$this->getDado('entidades').")
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro  = 'tcemg_tipo_orgao_entidade_sicom'
        
                GROUP BY receita.cod_receita
                       , receita.exercicio
                       , cod_estrutural
                       , conta_receita.descricao
                       , rec.masc_recurso_red
                )
               AS detalhamento 
             
             GROUP BY cod_estrutural
                , receita
                , recurso
                , descricao
            ) AS detalhamento_receitas
            ON detalhamento_receitas.cod_estrutural = conta_receita.cod_estrutural
               
         WHERE receita.exercicio    = '".Sessao::getExercicio()."'
           AND receita.cod_entidade IN (".$this->getDado('entidades').")
          GROUP BY receita.cod_receita
             , receita.cod_recurso
             , conta_receita.cod_estrutural
             , detalhamento_receitas.arrecadado_periodo
             
          ORDER BY tipo_registro
             , cod_receita
             , cod_font_recursos
             
             ) AS tabela
    
          GROUP BY tipo_registro 
             , cod_receita
             , cod_font_recursos
           HAVING sum(COALESCE(vl_arrecadado_fonte,0.00)) <> 0.00
          ORDER BY tipo_registro
                 , cod_receita
             , cod_font_recursos ";
    
    return $stSql;
    }
    
    public function __destruct(){}
    
}

?>