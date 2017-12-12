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

* Classe de mapeamento da tabela T_EXPORTACAO_MOVIMENTO_CONTABIL
* Data de Criação: 17/11/2014

* @author Analista: Sergio Luiz dos Santos
* @author Desenvolvedor: Carolina Schwaab Marcal

* @package URBEM
* @subpackage Mapeamento

$Revision:  $
$Name$
$Author: $

*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TExportacaoMovimentoContabil extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TExportacaoMovimentoContabil()
{   
    parent::Persistente();
    $this->setTabela('contabilidade.valor_lancamento');
    
    $this->AddCampo('cod_lote','integer',true,'',true,true);
    $this->AddCampo('tipo','char',true,'1',true,true);
    $this->AddCampo('sequencia','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('tipo_valor','char',true,'01',true,false);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('vl_lancamento','numeric',true,'14,02',false,false);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
   
    $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosExportacao()
{
    $stSql  = " SELECT  tabela.*              
                                , '".$this->getDado('bimestre')."' AS bimestre                                             
                                , replace(pc.cod_estrutural,'.', '') as cod_estrutural                                         
                        FROM (                                                             
                                        SELECT l.cod_lote                                             
                                                  , l.sequencia                                   
                                                  , l.tipo
                                                  , l.cod_historico
                                                  , l.exercicio
                                                  , l.cod_entidade                                       
                                                  , CASE WHEN l.complemento IS NULL THEN 
                                                        hc.nom_historico
                                                    ELSE
                                                       hc.nom_historico||' '||l.complemento 
                                                    END
                                                    ||CASE WHEN (tret.cod_recibo_extra IS NOT NULL) OR (tret2.cod_recibo_extra IS NOT NULL) OR (ret.cod_recibo_extra IS NOT NULL) THEN 
                                                        ' - Recibo: '||coalesce(cast(tret.cod_recibo_extra as varchar),'')||coalesce(cast(ret.cod_recibo_extra as varchar),'')  
                                                    ELSE
                                                        ''                                                 
                                                    END                                                         
                                                    ||CASE WHEN (tt.observacao IS NOT NULL) OR (tte.observacao IS NOT NULL) OR (tarrec.observacao IS NOT NULL) THEN 
                                                        ' - '||coalesce(tt.observacao, '')||coalesce(tte.observacao,'')                 
                                                    ELSE ''                                                
                                                    END                                                        
                                                    AS historico                                             
                                                  , CASE WHEN (tret.cod_recibo_extra is null ) THEN        
                                                           tret2.cod_recibo_extra                      
                                                     ELSE                                              
                                                           tret.cod_recibo_extra                       
                                                    END AS cod_recibo_extra                               
                                                  , CASE WHEN vl.tipo_valor = 'C' AND vl.vl_lancamento  < 0 THEN
                                                            vl.vl_lancamento*(-1)
                                                     ELSE 
                                                            vl.vl_lancamento
                                                     END AS vl_lancamento
                                                  , vl.tipo_valor                                      
                                                                                     
                                                  , lo.dt_lote       
                                                  , CASE WHEN cc.cod_plano is not null THEN 
                                                        cc.cod_plano   
                                                    ELSE cd.cod_plano                                 
                                                    END AS cod_plano        
                                                   , (    SELECT PJ.cnpj
                                                            FROM orcamento.entidade
                                                            JOIN sw_cgm
                                                              ON sw_cgm.numcgm=entidade.numcgm
                                                            JOIN sw_cgm_pessoa_juridica AS PJ
                                                              ON sw_cgm.numcgm=PJ.numcgm
                                                       WHERE entidade.exercicio='".$this->getDado('stExercicio')."'
                                                            AND entidade.cod_entidade= l.cod_entidade
                                                         ) AS cod_und_gestora
                                          FROM  contabilidade.lancamento AS l                
                                                  , contabilidade.lote AS lo                 
                                                  , contabilidade.historico_contabil AS hc                 
                                                  , orcamento.entidade               as en                 
                                                  , sw_cgm                           as cgm           
                                                  , contabilidade.valor_lancamento   AS vl                    
                                                  
                                     LEFT JOIN contabilidade.conta_credito AS cc                       
                                              ON cc.cod_lote     = vl.cod_lote       
                                            AND cc.tipo         = vl.tipo           
                                            AND cc.sequencia    = vl.sequencia      
                                            AND cc.exercicio    = vl.exercicio      
                                            AND cc.tipo_valor   = vl.tipo_valor     
                                            AND cc.cod_entidade = vl.cod_entidade                  
                                                                                                       
                                     LEFT JOIN contabilidade.conta_debito AS cd                        
                                              ON cd.cod_lote     = vl.cod_lote       
                                            AND cd.tipo         = vl.tipo           
                                            AND cd.sequencia    = vl.sequencia      
                                            AND cd.exercicio    = vl.exercicio      
                                            AND cd.tipo_valor   = vl.tipo_valor     
                                            AND cd.cod_entidade = vl.cod_entidade         
                                         
                                    LEFT JOIN tesouraria.transferencia AS tt                          
                                             ON tt.cod_lote     = vl.cod_lote       
                                           AND tt.tipo         = vl.tipo           
                                           AND tt.exercicio    = vl.exercicio      
                                           AND tt.cod_entidade = vl.cod_entidade               
                                                
                                   LEFT JOIN tesouraria.transferencia_estornada  AS tte             
                                             ON  tte.cod_lote_estorno  = vl.cod_lote 
                                           AND  tte.tipo = vl.tipo           
                                           AND  tte.exercicio= vl.exercicio      
                                           AND  tte.cod_entidade= vl.cod_entidade         
                                                 
                                    LEFT JOIN tesouraria.recibo_extra_transferencia AS ret       
                                             ON ret.cod_lote = vl.cod_lote       
                                           AND ret.tipo= vl.tipo           
                                           AND ret.exercicio= vl.exercicio      
                                           AND ret.cod_entidade= vl.cod_entidade       
                                                    
                                   LEFT JOIN (  SELECT tbl.exercicio                                      
                                                                , tbl.cod_entidade                                          
                                                                , tbll.tipo                                                  
                                                                , tbll.cod_lote                                              
                                                                , ta.observacao                                             
                                                        FROM tesouraria.boletim_liberado AS tbl  
                                                        
                                                  LEFT JOIN tesouraria.arrecadacao AS ta                           
                                                            ON  ta.exercicio    = tbl.exercicio                    
                                                          AND ta.cod_entidade = tbl.cod_entidade                 
                                                          AND ta.cod_boletim      = tbl.cod_boletim                    
                                                          
                                                          JOIN tesouraria.boletim_liberado_lote as tbll               
                                                            ON tbll.cod_boletim = tbl.cod_boletim                      
                                                          AND tbll.cod_entidade = tbl.cod_entidade               
                                                          AND tbll.exercicio = tbll.exercicio                        
                                                          AND tbll.timestamp_liberado = tbll.timestamp_liberado      
                                                          AND tbll.timestamp_fechamento = tbll.timestamp_fechamento  
                                                          
                                                   WHERE ta.exercicio    = tbl.exercicio                           
                                                        AND ta.cod_entidade = tbl.cod_entidade                    
                                                        AND ta.cod_boletim  = tbl.cod_boletim                     
                                                     ) AS tarrec                                                       
                                             ON tarrec.cod_lote= vl.cod_lote    
                                           AND tarrec.tipo= vl.tipo        
                                           AND tarrec.exercicio= vl.exercicio   
                                           AND tarrec.cod_entidade = vl.cod_entidade                         
                                                                                                               
                                    LEFT JOIN (SELECT  tesouraria.transferencia_estornada.cod_lote_estorno               
                                                                , tesouraria.transferencia_estornada.tipo                           
                                                                , tesouraria.transferencia_estornada.exercicio                      
                                                                , tesouraria.transferencia_estornada.cod_entidade                  
                                                                , tesouraria.recibo_extra_transferencia.cod_recibo_extra             
                                                        FROM tesouraria.transferencia_estornada                                 
                                                   LEFT JOIN tesouraria.recibo_extra_transferencia 
                                                            ON tesouraria.recibo_extra_transferencia.exercicio = tesouraria.transferencia_estornada.exercicio 
                                                          AND tesouraria.recibo_extra_transferencia.cod_entidade = tesouraria.transferencia_estornada.cod_entidade  
                                                          AND tesouraria.recibo_extra_transferencia.cod_lote = tesouraria.transferencia_estornada.cod_lote                        
                                                                                                                                 
                                                      WHERE  tesouraria.transferencia_estornada.exercicio = '".$this->getDado("stExercicio")."'  
                                                          AND  tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado("inCodEntidade")." ) 
                                                 GROUP BY  tesouraria.transferencia_estornada.cod_lote_estorno               
                                                                , tesouraria.transferencia_estornada.tipo                         
                                                                , tesouraria.transferencia_estornada.exercicio                 
                                                                , tesouraria.transferencia_estornada.cod_entidade                 
                                                                , tesouraria.recibo_extra_transferencia.cod_recibo_extra      

                                                 ) AS tret                                                               
                                              ON tret.cod_lote_estorno  = vl.cod_lote    
                                            AND tret.tipo  = vl.tipo        
                                            AND tret.exercicio= vl.exercicio   
                                            AND tret.cod_entidade = vl.cod_entidade                                
                                                                                                                              
                                    LEFT JOIN ( SELECT  tesouraria.transferencia_estornada.cod_lote
                                                                , tesouraria.transferencia_estornada.tipo                      
                                                                , tesouraria.transferencia_estornada.exercicio                     
                                                                , tesouraria.transferencia_estornada.cod_entidade                 
                                                                , tesouraria.recibo_extra_transferencia.cod_recibo_extra             
                                                        FROM  tesouraria.transferencia_estornada                                 
                                                  LEFT JOIN tesouraria.recibo_extra_transferencia 
                                                            ON tesouraria.recibo_extra_transferencia.exercicio =  tesouraria.transferencia_estornada.exercicio 
                                                          AND tesouraria.recibo_extra_transferencia.cod_entidade = tesouraria.transferencia_estornada.cod_entidade 
                                                          AND tesouraria.recibo_extra_transferencia.cod_lote = tesouraria.transferencia_estornada.cod_lote                        
                                                                                                                          
                                                     WHERE tesouraria.transferencia_estornada.exercicio = '".$this->getDado("stExercicio")."'  
                                                          AND tesouraria.transferencia_estornada.cod_entidade IN (".$this->getDado("inCodEntidade")." ) 
                                                 GROUP BY tesouraria.transferencia_estornada.cod_lote                  
                                                                , tesouraria.transferencia_estornada.tipo                  
                                                                , tesouraria.transferencia_estornada.exercicio                
                                                                , tesouraria.transferencia_estornada.cod_entidade              
                                                                , tesouraria.recibo_extra_transferencia.cod_recibo_extra        

                                                      ) AS tret2                                                              
                                             ON tret2.cod_lote = vl.cod_lote     
                                           AND tret2.tipo = vl.tipo         
                                           AND tret2.exercicio = vl.exercicio    
                                           AND tret2.cod_entidade = vl.cod_entidade                                    
                                                                                                          
                            WHERE vl.cod_lote= l.cod_lote        
                                 AND vl.tipo = l.tipo            
                                 AND vl.sequencia= l.sequencia       
                                 AND vl.exercicio  = l.exercicio       
                                 AND vl.cod_entidade  = l.cod_entidade    
                                 AND lo.cod_lote   = l.cod_lote        
                                 AND lo.exercicio  = l.exercicio       
                                 AND lo.tipo  = l.tipo            
                                 AND lo.cod_entidade  = l.cod_entidade    
                                 AND hc.cod_historico = l.cod_historico   
                                 AND hc.exercicio = l.exercicio       
                                 AND en.cod_entidade  = l.cod_entidade    
                                 AND en.exercicio  = l.exercicio       
                                 AND cgm.numcgm   = en.numcgm                           
                                AND  l.exercicio = '".$this->getDado("stExercicio")."' 
                                AND  lo.dt_lote >= TO_DATE('".$this->getDado("dtInicial")."'::varchar,'dd/mm/yyyy' ) 
                                AND  lo.dt_lote <= TO_DATE('".$this->getDado("dtFinal")."'::varchar,'dd/mm/yyyy') 
                                AND  l.cod_entidade IN  (".$this->getDado("inCodEntidade")." )     
                       ORDER BY  to_date(dt_lote::varchar,'yyyy-mm-dd')
                                     , l.cod_lote                                            
                                     , l.cod_entidade                                        
                                     , l.tipo                                               
                                     , l.sequencia ASC                                       
                                     , vl.vl_lancamento DESC                                 
                                     , vl.tipo_valor DESC     
                                     , cod_und_gestora
                                ) AS tabela                                                   
                                , contabilidade.plano_analitica AS pa                           
                                , contabilidade.plano_conta     AS pc  
                            
                     WHERE  tabela.cod_plano = pa.cod_plano     
                          AND tabela.exercicio = pa.exercicio     
                          AND pa.cod_conta = pc.cod_conta     
                          AND pa.exercicio = pc.exercicio    
                          
                 GROUP BY tabela.cod_lote                                          
                                , tabela.sequencia                                    
                                , tabela.tipo                                        
                                , tabela.cod_historico                               
                                , tabela.historico                                
                                , tabela.exercicio                                  
                                , tabela.cod_entidade                                
                                , tabela.cod_recibo_extra                          
                                , tabela.vl_lancamento                               
                                , tabela.tipo_valor     
                                , tabela.dt_lote                                      
                                , tabela.cod_plano                                   
                                , pc.cod_estrutural   
                                , tabela.cod_und_gestora
                 ORDER BY  cod_entidade
                                , cod_lote
                                , dt_lote, tipo
                                , sequencia
                                , tipo_valor DESC 
                         ";                                               

    return $stSql;
}

}
