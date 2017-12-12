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
    * Data de Criação: 22/07/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63250 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

class TTBAMovimentoContabil extends TContabilidadePlanoConta
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::__construct();
}

public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaDadosTribunal()
{
    $stSql = " SELECT 1 AS tipo_registro
                    , ".$this->getDado('inCodGestora')." AS unidade_gestora
                    , exercicio                                         
                    , cod_estrutural                                              
                    , tipo_mov                                                    
                    , competencia                                                         
                    , ABS(SUM(valor_credito)) AS vl_credito                       
                    , ABS(SUM(valor_debito))  AS vl_debito                        
                    , ROW_NUMBER() OVER (ORDER BY cod_estrutural) AS nu_sequencial_tc 
                 FROM (
                 
                       SELECT plano_conta.exercicio                                        
                            , plano_conta.cod_estrutural                                   
                            , CASE WHEN valor_lancamento.tipo = 'I'
                                   THEN 1
                                   ELSE 3
                              END AS tipo_mov 
                            , TO_CHAR(lote.dt_lote,'YYYYMM') AS competencia
                            , SUM(valor_lancamento.vl_lancamento) AS valor_credito              
                            , 0.00 AS valor_debito                                
                     
                        FROM contabilidade.plano_conta
                      
                  INNER JOIN contabilidade.plano_analitica
                          ON plano_conta.exercicio = plano_analitica.exercicio                       
                         AND plano_conta.cod_conta = plano_analitica.cod_conta                       
                        
                 INNER JOIN contabilidade.conta_credito
                         ON plano_analitica.exercicio = conta_credito.exercicio                       
                        AND plano_analitica.cod_plano = conta_credito.cod_plano                       
                        
                 INNER JOIN contabilidade.valor_lancamento
                         ON conta_credito.exercicio    = valor_lancamento.exercicio                       
                        AND conta_credito.cod_entidade = valor_lancamento.cod_entidade                    
                        AND conta_credito.cod_lote     = valor_lancamento.cod_lote                        
                        AND conta_credito.tipo         = valor_lancamento.tipo                            
                        AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor                      
                        AND conta_credito.sequencia    = valor_lancamento.sequencia                       
                        
                 INNER JOIN contabilidade.lote
                         ON valor_lancamento.exercicio    = lote.exercicio                       
                        AND valor_lancamento.cod_entidade = lote.cod_entidade                    
                        AND valor_lancamento.tipo         = lote.tipo                            
                        AND valor_lancamento.cod_lote     = lote.cod_lote                        
                        
                      WHERE plano_conta.exercicio = '".$this->getDado('stExercicio')."'  
                        AND valor_lancamento.cod_entidade IN (".$this->getDado('stEntidades').")
                        AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('DtInicio')."', 'DD/MM/YYYY') 
                                             AND TO_DATE('".$this->getDado('DtFim')."', 'DD/MM/YYYY')
                   
                   GROUP BY plano_conta.exercicio
                          , plano_conta.cod_estrutural
                          , valor_lancamento.tipo
                          , TO_CHAR(lote.dt_lote , 'YYYYMM')
            
                UNION                                                                      
            
                    SELECT plano_conta.exercicio                                                      
                         , plano_conta.cod_estrutural                                                 
                         , CASE WHEN valor_lancamento.tipo = 'I'
                                THEN 1
                                ELSE 3
                           END AS tipo_mov               
                         , TO_CHAR(lote.dt_lote,'YYYYMM') AS competencia
                         , 0.00 AS valor_credito                                             
                         , SUM(valor_lancamento.vl_lancamento) AS valor_debito                             
                     
                     FROM  contabilidade.plano_conta
            
                     
                INNER JOIN contabilidade.plano_analitica
                        ON plano_conta.exercicio    = plano_analitica.exercicio                                     
                       AND plano_conta.cod_conta    = plano_analitica.cod_conta                                     
                       
                INNER JOIN contabilidade.conta_debito
                        ON plano_analitica.exercicio    = conta_debito.exercicio                                     
                       AND plano_analitica.cod_plano    = conta_debito.cod_plano                                     
                       
                INNER JOIN contabilidade.valor_lancamento
                        ON conta_debito.exercicio    = valor_lancamento.exercicio                                     
                       AND conta_debito.cod_entidade = valor_lancamento.cod_entidade                                  
                       AND conta_debito.cod_lote     = valor_lancamento.cod_lote                                      
                       AND conta_debito.tipo         = valor_lancamento.tipo                                          
                       AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor                                    
                       AND conta_debito.sequencia    = valor_lancamento.sequencia                                     
                       
                INNER JOIN contabilidade.lote
                        ON valor_lancamento.exercicio    = lote.exercicio                                     
                       AND valor_lancamento.cod_entidade = lote.cod_entidade                                  
                       AND valor_lancamento.tipo         = lote.tipo                                          
                       AND valor_lancamento.cod_lote     = lote.cod_lote                                      
                       
                     WHERE plano_conta.exercicio = '".$this->getDado('stExercicio')."' 
                       AND valor_lancamento.cod_entidade IN (".$this->getDado('stEntidades').") 
                       AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('DtInicio')."', 'DD/MM/YYYY') 
                                            AND TO_DATE('".$this->getDado('DtFim')."', 'DD/MM/YYYY')
                  
                  GROUP BY plano_conta.exercicio
                         , plano_conta.cod_estrutural
                         , valor_lancamento.tipo
                         , TO_CHAR(lote.dt_lote,'YYYYMM')
                         
                ) AS tabela                                                                    
                             
            GROUP BY exercicio
                   , cod_estrutural
                   , tipo_mov
                   , competencia                              
            ORDER BY exercicio
                   , cod_estrutural
                   , tipo_mov
                   , competencia ";

    return $stSql;
}

}

?>