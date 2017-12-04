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

    * 
    * Data de Criação   : 06/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEAtualizacaoOrcamentaria.class.php 60693 2014-11-10 16:52:34Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEAtualizacaoOrcamentaria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEAtualizacaoOrcamentaria()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "SELECT despesa.exercicio
                       , LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , programa.num_programa
                       , acao.num_acao
                       , CASE WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 1 ) THEN 1 --PROJETOS
                              WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 2 ) THEN 2 --ATIVIDADE
                              WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 3 ) THEN 9 --OPERACOES ESPECIAIS
                         END AS tipo_acao   
  
                       , despesa.cod_recurso
                       , vinculo_tipo_norma.cod_tipo_norma as tipo_norma                                              
                       , norma.num_norma
                       , LPAD(tabela_suplementacao.ctt_cod_tipo::VARCHAR,2,'0')  as tipo_alteracao_orcamentaria                                  
                       , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),1,1) as categoria_economica
                       , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),2,1) as natureza
                       , SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),3,2) as modalidade                    
                       , SUBSTR(orcamento.recuperaEstruturalDespesa(despesa.cod_conta, despesa.exercicio, 6, FALSE, FALSE),5,2) AS cod_elemento_despesa  
                       , REPLACE(tabela_suplementacao.valor::VARCHAR,'-','') AS vl_saldo_atual
                       
                    FROM (
                              SELECT OS.exercicio                                                         
                                   , OS.cod_suplementacao                                                 
                                   , OS.cod_tipo                                                          
                                   , CTT.nom_tipo      
                                   , CASE WHEN OSS.tipo = 'OSS' THEN
                                            CASE WHEN CTT.cod_tipo = 16 THEN
                                                    11::varchar
                                                 WHEN CTT.cod_tipo = 1 THEN   
                                                    4::varchar
                                                 WHEN CTT.cod_tipo = 2 THEN   
                                                    1::varchar
                                                 WHEN CTT.cod_tipo = 4 THEN   
                                                    3::varchar
                                                 WHEN CTT.cod_tipo = 5 THEN   
                                                    2::varchar
                                                 WHEN CTT.cod_tipo = 6 THEN   
                                                    8::varchar
                                                 WHEN CTT.cod_tipo = 7 THEN   
                                                    6::varchar
                                                 WHEN CTT.cod_tipo = 8 THEN   
                                                    14::varchar
                                                 WHEN CTT.cod_tipo = 9 THEN   
                                                    9::varchar
                                                 WHEN CTT.cod_tipo = 10 THEN   
                                                    7::varchar
                                                 END
                                          WHEN OSS.tipo = 'OSR' THEN
                                            11::varchar
                                     END as ctt_cod_tipo                                         
                                   , OS.cod_norma                                                         
                                   , OS.motivo                                                            
                                   , OSS.cod_entidade                                               
                                   , TO_CHAR( OS.dt_suplementacao, 'dd/mm/yyyy' ) AS dt_suplementacao
                                   , OSS.valor
                                   , OSS.cod_despesa
                            
                            FROM orcamento.suplementacao          AS OS
                            
                            JOIN ( SELECT OSS.exercicio                                            
                               , OSS.cod_suplementacao                                    
                               , MAX( OSS.cod_despesa ) AS cod_despesa                    
                               , MAX( RECURSO.cod_recurso )AS cod_recurso                
                               , sum( OSS.valor ) AS valor                                
                               , OD.cod_entidade 
                               , 'OSS' AS tipo                                        
                               , OD.num_orgao                                                                                                          
                                 ||'.'||OD.num_unidade                                                                                                     
                                 ||'.'||OD.cod_funcao                                                                                                      
                                 ||'.'||OD.cod_subfuncao                                                                                                   
                                 ||'.'||ppa.programa.num_programa                                                                                                    
                                 ||'.'||ppa.acao.num_acao                                                                                                         
                                 ||'.'||replace(ocd.cod_estrutural,'.','')  AS dotacao 
                                         FROM orcamento.suplementacao_suplementada AS OSS                
                                INNER JOIN orcamento.despesa                    AS OD   
                                             ON OSS.cod_despesa = OD.cod_despesa 
                                           AND OSS.exercicio   = OD.exercicio                           
                               INNER  JOIN orcamento.recurso('2014')            AS RECURSO
                                             ON OD.cod_recurso  = RECURSO.cod_recurso                   
                                           AND OD.exercicio    = RECURSO.exercicio
                                INNER JOIN orcamento.programa_ppa_programa                                                  
                                             ON programa_ppa_programa.cod_programa = OD.cod_programa                                    
                                           AND programa_ppa_programa.exercicio   = OD.exercicio                                    
                                 INNER JOIN ppa.programa                                                            
                                             ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa                                
                                INNER JOIN orcamento.pao_ppa_acao                                                    
                                             ON pao_ppa_acao.num_pao = OD.num_pao                                            
                                           AND pao_ppa_acao.exercicio = OD.exercicio                                            
                                INNER JOIN ppa.acao                                                             
                                             ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                                            
                                INNER JOIN orcamento.conta_despesa AS ocd
                                             ON ocd.exercicio =  OD.exercicio
                                            AND ocd.cod_conta =  OD.cod_conta 
                                  GROUP BY OSS.exercicio                                          
                               , OSS.cod_suplementacao                                  
                               , RECURSO.cod_recurso                                    
                               , OD.cod_entidade
                               , dotacao
                                        
                               UNION ALL
							     
                                   SELECT OSR.exercicio                                            
                             , OSR.cod_suplementacao                                    
                             , MAX( OSR.cod_despesa ) AS cod_despesa
                             , null::integer AS cod_recurso                    
                             , sum( OSR.valor ) AS valor                                
                             , OD.cod_entidade
                             , 'OSR' AS tipo
                             , OD.num_orgao                                                                                                          
                               ||'.'||OD.num_unidade                                                                                                     
                               ||'.'||OD.cod_funcao                                                                                                      
                               ||'.'||OD.cod_subfuncao                                                                                                   
                               ||'.'||ppa.programa.num_programa                                                                                                    
                               ||'.'||ppa.acao.num_acao                                                                                                         
                               ||'.'||replace(ocd.cod_estrutural,'.','')  AS dotacao 
                                     FROM orcamento.suplementacao_reducao AS OSR
                            INNER JOIN orcamento.despesa               AS OD                 
                                         ON OSR.cod_despesa = OD.cod_despesa                           
                                       AND OSR.exercicio   = OD.exercicio
                            INNER JOIN orcamento.programa_ppa_programa                                                  
                                        ON programa_ppa_programa.cod_programa = OD.cod_programa                                    
                                      AND programa_ppa_programa.exercicio   = OD.exercicio                                    
                          INNER JOIN ppa.programa                                                            
                                       ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa                                
                          INNER JOIN orcamento.pao_ppa_acao                                                    
                                       ON pao_ppa_acao.num_pao = OD.num_pao                                            
                                     AND pao_ppa_acao.exercicio = OD.exercicio                                            
                          INNER JOIN ppa.acao                                                             
                                       ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                                            
                          INNER JOIN orcamento.conta_despesa AS ocd
                                       ON ocd.exercicio =  OD.exercicio
                                     AND ocd.cod_conta =  OD.cod_conta
                                 GROUP BY OSR.exercicio                                          
                               , OSR.cod_suplementacao                                  
                               , OD.cod_entidade
                               , dotacao
                                 ORDER BY exercicio                                          
                              , cod_suplementacao
                               ) AS OSS
                              ON OS.exercicio         = OSS.exercicio                           
                             AND OS.cod_suplementacao = OSS.cod_suplementacao
                             
                       LEFT JOIN orcamento.suplementacao_anulada AS OSA                        
                              ON OS.cod_suplementacao = OSA.cod_suplementacao_anulacao                  
                             AND OS.exercicio         = OSA.exercicio
                             
                       LEFT JOIN contabilidade.tipo_transferencia     AS CTT                   
                              ON OS.cod_tipo          = CTT.cod_tipo                           
                             AND OS.exercicio         = CTT.exercicio
                             
                       LEFT JOIN contabilidade.transferencia_despesa  AS CTD                   
                              ON OS.cod_tipo          = CTD.cod_tipo                           
                             AND OS.exercicio         = CTD.exercicio                          
                             AND OS.cod_suplementacao = CTD.cod_suplementacao
                             
                           WHERE OSA.cod_suplementacao is null                                 
                             AND OS.exercicio = '".$this->getDado('exercicio')."'
                             AND OS.dt_suplementacao BETWEEN TO_DATE( '".$this->getDado('dt_inicial')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dt_final')."', 'dd/mm/yyyy' )
                             AND OSS.cod_entidade IN ( ".$this->getDado('cod_entidade')." )
                             
                        GROUP BY OS.exercicio,OS.cod_suplementacao,OS.cod_tipo,CTT.nom_tipo, CTT.cod_tipo, OS.cod_norma,OS.motivo,
                                 OS.dt_suplementacao,OSS.cod_entidade, OSS.cod_despesa,OSS.tipo,OSS.valor
                                
                        ORDER BY OS.cod_suplementacao,OS.cod_tipo,OS.dt_suplementacao
                       
                    ) AS tabela_suplementacao
                    
                    JOIN orcamento.despesa
                      ON tabela_suplementacao.exercicio   = despesa.exercicio 
                     AND tabela_suplementacao.cod_despesa = despesa.cod_despesa
                     
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa       = despesa.cod_despesa
                        
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                         
                    JOIN ppa.programa
                      ON programa.cod_programa = despesa.cod_programa
                         
                    JOIN orcamento.conta_despesa
                      ON conta_despesa.exercicio = despesa.exercicio
                     AND conta_despesa.cod_conta = despesa.cod_conta
                     
                    JOIN normas.norma
                      ON norma.cod_norma = tabela_suplementacao.cod_norma
                     
               LEFT JOIN normas.tipo_norma
                      ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma
                     
               LEFT JOIN tcepe.vinculo_tipo_norma
                      ON vinculo_tipo_norma.cod_tipo_norma = tipo_norma.cod_tipo_norma

                   WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                     AND despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
                     AND NOT EXISTS(SELECT 1 FROM orcamento.suplementacao_anulada
                                     WHERE suplementacao_anulada.cod_suplementacao = tabela_suplementacao.cod_suplementacao
                                        and suplementacao_anulada.exercicio = tabela_suplementacao.exercicio  )

       
                GROUP BY despesa.exercicio
                       , programa.num_programa,despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , acao.num_acao
                       , despesa.cod_recurso                                 
                       , conta_despesa.cod_estrutural
                       , tabela_suplementacao.ctt_cod_tipo
                       , tabela_suplementacao.valor
                       , despesa.num_orgao 
                       , despesa.num_unidade
                       , tipo_norma                                              
                       , norma.num_norma
                       , despesa.cod_conta
                       , despesa.exercicio
        ";
        
    
        
        return $stSql;
    }
}

?>