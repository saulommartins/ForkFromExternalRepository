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

    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 10/02/2014

    * @category    Urbem
    * @package     TCE/MG
    * @author      Carolina Schwaab Marcal
    * $Id: TTCEMGCTB.class.php 62269 2015-04-15 18:28:39Z franver $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGCTB extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaContasBancarias10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancarias10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasBancarias10()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
                 SELECT '10' AS tipo_registro
                      ,	cod_ctb
                      , tipo_conta||regexp_replace((conta), '[-|,|.|x]', '', 'gi') AS cod_ctb_view 
                      , cod_orgao
                      , banco
                      , agencia 
                      , digito_verificador_agencia
                      , digito_verificador_conta_bancaria
                      , conta_bancaria
                      , retorno.conta_corrente
                      , tipo_conta
                      , tipo_aplicacao
                      , ''::VARCHAR nro_seq_aplicacao
                      , desc_conta_bancaria
                      , CASE WHEN (convenio_plano_banco.num_convenio <> NULL) THEN 1
                             ELSE 2
                        END AS conta_convenio
                      , convenio_plano_banco.num_convenio
                      , convenio_plano_banco.dt_assinatura
                  FROM tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as retorno
                                                        (  cod_conta                         INTEGER
					                  ,tipo_aplicacao                    VARCHAR
					                  ,cod_ctb                           INTEGER
					                  ,tipo_conta                        INTEGER
					                  ,exercicio                         CHAR(4)  
					                  ,conta                             TEXT                         
                                                          ,conta_bancaria                    TEXT  
                                                          ,conta_corrente                    TEXT                                                         
                                                          ,cod_orgao                         INTEGER                                                        
                                                          ,banco                             VARCHAR                                                        
                                                          ,agencia             		     TEXT                                                        
                                                          ,digito_verificador_agencia        TEXT                                                        
                                                          ,digito_verificador_conta_bancaria TEXT                                                        
                                                          ,desc_conta_bancaria               VARCHAR
                                                          --,cod_plano                         INTEGER   
                                                        )
                                                        
                       INNER JOIN contabilidade.plano_analitica
                               ON retorno.cod_conta = plano_analitica.cod_conta
                              AND retorno.exercicio = plano_analitica.exercicio
                              
                       INNER JOIN contabilidade.plano_banco
                               ON plano_analitica.cod_plano = plano_banco.cod_plano
                              AND plano_analitica.exercicio = plano_banco.exercicio
                              
             LEFT JOIN tcemg.convenio_plano_banco 
                    ON convenio_plano_banco.cod_plano = plano_banco.cod_plano 
                   AND convenio_plano_banco.exercicio = '".$this->getDado('exercicio')."'
              GROUP BY cod_ctb
                     , cod_ctb_view
                     , cod_orgao,banco
                     , agencia
                     , digito_verificador_agencia
                     , digito_verificador_conta_bancaria
                     , conta_bancaria,retorno.conta_corrente
                     , tipo_conta,tipo_aplicacao
                     , desc_conta_bancaria
                     , conta_convenio,num_convenio
                     , dt_assinatura
              ORDER BY cod_ctb";
        return $stSql;
    }
    
    public function recuperaContasBancarias20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancarias20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasBancarias20()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
                   SELECT c.tipo_registro
                        , c.cod_ctb
                        , c.cod_ctb_view
                        , tipo_aplicacao
                        , c.tipo_conta 
                        , c.cod_orgao
                        , c.cod_recurso as cod_fonte_recursos
                        , SUM(c.vl_saldo_inicial_fonte) as vl_saldo_inicial_fonte 
                        , SUM(c.vl_saldo_final_fonte) as vl_saldo_final_fonte
                        , c.movimentacao
                     FROM (SELECT '20'::VARCHAR AS tipo_registro
                                , cod_ctb
                                , tipo_conta||regexp_replace((conta), '[-|,|.|x]', '', 'gi') AS cod_ctb_view 
                                , tipo_aplicacao
                                , tipo_conta
                                , cod_orgao
                                , plano_recurso.cod_recurso
                                , plano_analitica.cod_plano
                                , plano_analitica.exercicio
                                , saldo_inicial.saldo_anterior AS vl_saldo_inicial_fonte
                                , saldo_inicial.saldo_atual AS vl_saldo_final_fonte
                                , CASE WHEN (vl_lancamento IS NOT NULL) THEN 1 END AS movimentacao  
                                , conta
                             FROM tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as retorno
                                           (  cod_conta                          INTEGER
					    , tipo_aplicacao                    VARCHAR
					    , cod_ctb                           INTEGER
					    , tipo_conta                        INTEGER
					    , exercicio                         CHAR(4)  
					    , conta                             TEXT                         
                                            , conta_bancaria                    TEXT  
                                            , conta_corrente                    TEXT                                                         
                                            , cod_orgao                         INTEGER                                                        
                                            , banco                             VARCHAR                                                        
                                            , agencia             		TEXT                                                        
                                            , digito_verificador_agencia        TEXT                                                        
                                            , digito_verificador_conta_bancaria TEXT                                                        
                                            , desc_conta_bancaria               VARCHAR
                                            --, cod_plano                         INTEGER  
                                           )
                       INNER JOIN contabilidade.plano_analitica
                               ON retorno.cod_conta = plano_analitica.cod_conta
                              AND retorno.exercicio = plano_analitica.exercicio
                              
                       INNER JOIN contabilidade.plano_banco
                               ON plano_analitica.cod_plano = plano_banco.cod_plano
                              AND plano_analitica.exercicio = plano_banco.exercicio
                              --AND retorno.cod_plano = plano_banco.cod_plano
                              
                        LEFT JOIN  tcemg.convenio_plano_banco 
                               ON  convenio_plano_banco.cod_plano = plano_banco.cod_plano 
                              AND  convenio_plano_banco.exercicio = plano_banco.exercicio
                       INNER JOIN  contabilidade.plano_recurso
                               ON  plano_analitica.cod_plano = plano_recurso.cod_plano
                              AND  plano_analitica.exercicio = plano_recurso.exercicio
                        LEFT JOIN ( SELECT cod_plano
                                         , exercicio
                                         , cod_lote
                                         , tipo
                                         , sequencia
                                         , tipo_valor
                                         , cod_entidade
                                         , '1'::VARCHAR AS tipo_movimentacao 
                                      FROM contabilidade.conta_debito
                                 UNION ALL
                                    SELECT cod_plano
                                         , exercicio
                                         , cod_lote
                                         , tipo
                                         , sequencia
                                         , tipo_valor
                                         , cod_entidade
                                         , '2'::VARCHAR AS tipo_movimentacao 
                                      FROM contabilidade.conta_credito
                                ) AS conta_debito_credito
                               ON plano_analitica.cod_plano = conta_debito_credito.cod_plano
                              AND plano_analitica.exercicio = conta_debito_credito.exercicio
                        LEFT JOIN contabilidade.valor_lancamento AS vl
                               ON conta_debito_credito.cod_lote     = vl.cod_lote
                              AND conta_debito_credito.tipo         = vl.tipo
                              AND conta_debito_credito.sequencia    = vl.sequencia
                              AND conta_debito_credito.exercicio    = vl.exercicio
                              AND conta_debito_credito.tipo_valor   = vl.tipo_valor
                              AND conta_debito_credito.cod_entidade = vl.cod_entidade
                        LEFT JOIN contabilidade.lancamento
                               ON lancamento.exercicio    = vl.exercicio
                              AND lancamento.cod_entidade = vl.cod_entidade
                              AND lancamento.tipo         = vl.tipo
                              AND lancamento.cod_lote     = vl.cod_lote
                              AND lancamento.sequencia    = vl.sequencia
                        LEFT JOIN contabilidade.lancamento_receita
                               ON lancamento_receita.exercicio    = lancamento.exercicio
                              AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                              AND lancamento_receita.tipo         = lancamento.tipo
                              AND lancamento_receita.cod_lote     = lancamento.cod_lote
                              AND lancamento_receita.sequencia    = lancamento.sequencia
                        LEFT JOIN contabilidade.lote AS lo
                               ON vl.cod_lote     = lo.cod_lote
                              AND vl.exercicio    = lo.exercicio
                              AND vl.cod_entidade = lo.cod_entidade
                              AND  lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                              AND  lo.exercicio = '".$this->getDado('exercicio')."'
                       INNER JOIN ( SELECT *
                                         , ((vl_debito - vl_credito) + saldo_anterior) as saldo_atual                                                                                     
                                      FROM tesouraria.fn_relatorio_demostrativo_saldos( '".$this->getDado('exercicio')."',           
                                                                                        '".$this->getDado('entidades')."',           
                                                                                        '".$this->getDado('dtInicio')."',            
                                                                                        '".$this->getDado('dtFim')."',               
                                                                                        '',   
                                                                                        '',      
                                                                                        '',     
                                                                                        '',        
                                                                                        '',            
                                                                                        'S',          
                                                                                        '',     
                                                                                        '',       
                                                                                        'true'   
                                                                   ) as retorno( exercicio          VARCHAR                                                        
                                                                            ,cod_estrutural     VARCHAR                                                        
                                                                            ,cod_plano          INTEGER                                                        
                                                                            ,nom_conta          VARCHAR                                                        
                                                                            ,saldo_anterior     NUMERIC                                                        
                                                                            ,vl_credito         NUMERIC                                                        
                                                                            ,vl_debito          NUMERIC                                                        
                                                                            ,cod_recurso        INTEGER                                                        
                                                                            ,nom_recurso        VARCHAR                                                        
                                                                   )
                                ) AS saldo_inicial
                               ON saldo_inicial.exercicio = plano_analitica.exercicio
                              AND saldo_inicial.cod_plano = plano_analitica.cod_plano
                              AND saldo_inicial.cod_recurso = plano_recurso.cod_recurso
                            WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
                              AND  plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                         GROUP BY cod_ctb
                                , cod_ctb_view
                                , cod_orgao
                                , tipo_conta
                                , tipo_aplicacao 
                                , plano_recurso.cod_recurso
                                , plano_analitica.cod_plano
                                , plano_analitica.exercicio
                                , movimentacao
                                , saldo_inicial.saldo_anterior
                                , saldo_inicial.saldo_atual
                                , conta
                         ORDER BY cod_ctb
                        ) AS c
                --    WHERE movimentacao = 1   
                 GROUP BY c.tipo_registro
                        , c.cod_ctb
                        , c.cod_ctb_view
                        , c.tipo_aplicacao
                        , c.tipo_conta 
                        , c.cod_orgao
                        , c.cod_recurso
                        , c.conta
                        , c.movimentacao    
                 ORDER BY c.cod_ctb  
                 ";
        return $stSql;
    }
    
    public function recuperaContasBancarias21(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancarias21",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasBancarias21()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
                  SELECT a.tipo_registro
                       , '' AS cod_reduzido_mov
                       , a.cod_orgao 
                       , a.cod_ctb
                       , LPAD(a.tipo_conta||a.cod_ctb||a.tipo_entr_saida||a.tipo_movimentacao||a.cod_fonte_recursos||LPAD(COALESCE(a.cod_fonte_ctb_transf::VARCHAR,'0'),3,'0'),17,'0') AS cod_ctb_view
                       , a.cod_fonte_recursos
                       , a.exercicio
                       , a.tipo_movimentacao
                       , a.tipo_entr_saida
                       , a.cod_ctb_transf AS cod_ctb_transf
                       , a.tipo_conta||a.cod_ctb AS cod_ctb_transf_view
                       , a.cod_fonte_ctb_transf
                       , a.tipo_conta  AS tipo_conta
                       , SUM(a.valor_entr_saida) AS valor_entr_saida

                  FROM (
                       
                        SELECT
                            '21'::int  AS  tipo_registro
                             , SUM(vl.vl_lancamento ) AS valor_entr_saida
                             , plano_banco.cod_entidade as cod_orgao
                             , conta_bancaria.cod_ctb
                             , conta_bancaria.tipo_aplicacao
                             , conta_bancaria.tipo_conta::VARCHAR
                             , pa.exercicio 
                             , plano_recurso.cod_recurso AS cod_fonte_recursos
                             , '1'::VARCHAR AS tipo_movimentacao 
                             , CASE
                                  WHEN lo.tipo = 'A' AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9' AND lancamento_receita.estorno = false
                                        AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325' THEN '01'
                                  WHEN lo.tipo = 'A' AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1)= '9' 
                                        AND lancamento_receita.estorno = false AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'  
                                        AND  (RDE.vl_estornado IS NULL ) AND ((ARR.vl_arrecadacao IS NULL ) OR (AR.devolucao = true)) THEN '02'
                                  WHEN lo.tipo = 'A' AND lancamento_receita.estorno = true AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9' THEN '03'
                                  WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) = '1325' THEN '04'
                                  WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 5 THEN '05'
                                  WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 4 THEN '07'
                                  WHEN lo.tipo = 'P' AND lancamento_receita.estorno = false THEN '08'
                                  WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 3 THEN '09'
                                  WHEN lo.tipo = 'P' AND lancamento_receita.estorno = true THEN '10'
                                  WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 2
                                                     AND transferencia_estornada.cod_lote_estorno IS NOT NULL
                                                     AND ( SELECT CASE WHEN COUNT(plano_conta.*) > 0 THEN TRUE ELSE FALSE END
                                                             FROM contabilidade.plano_conta     
                                                       INNER JOIN contabilidade.plano_analitica 
                                                               ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                              AND plano_conta.exercicio = plano_analitica.exercicio 
                                                            WHERE plano_analitica.cod_plano = transferencia.cod_plano_credito
                                                              AND plano_analitica.exercicio = transferencia.exercicio
                                                              AND (    SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451100000'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220101'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220102'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220103'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220104'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220199'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451300000' ) 
                                                         ) THEN '13'

                             WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 2
                                                AND ( SELECT CASE WHEN COUNT(plano_conta.*) > 0 THEN TRUE ELSE FALSE END
                                                             FROM contabilidade.plano_conta     
                                                       INNER JOIN contabilidade.plano_analitica 
                                                               ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                              AND plano_conta.exercicio = plano_analitica.exercicio 
                                                            WHERE plano_analitica.cod_plano = transferencia.cod_plano_credito
                                                              AND plano_analitica.exercicio = transferencia.exercicio
                                                              AND (    SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451100000'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220101'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220102'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220103'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220104'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220199'
                                                                    OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451300000' ) 
                                                    ) THEN '12'
                            
                              WHEN lo.tipo = 'A'AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9' 
                                    AND ( (RDE.vl_estornado IS NOT NULL) OR (ARR.vl_arrecadacao IS NOT NULL AND AR.devolucao = false) ) THEN '16'                              
                              ELSE '99'
                              END AS tipo_entr_saida 

                           , CASE WHEN (lo.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                  ( cod_ctb_transferencia.cod_ctb_anterior
                             ) END::VARCHAR AS cod_ctb_transf         

                           , CASE WHEN (lo.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                             (   SELECT plano_recurso.cod_recurso 
                                   FROM contabilidade.plano_conta     
                             INNER JOIN contabilidade.plano_analitica 
                                     ON plano_conta.cod_conta = plano_analitica.cod_conta
                                    AND plano_conta.exercicio = plano_analitica.exercicio 
                             INNER JOIN contabilidade.plano_recurso
                                     ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                    AND plano_recurso.exercicio = plano_analitica.exercicio
                                  WHERE plano_analitica.cod_plano = transferencia.cod_plano_credito
                                    AND plano_analitica.exercicio = transferencia.exercicio
                             ) END::VARCHAR AS cod_fonte_ctb_transf 
                    
                        FROM contabilidade.plano_conta AS pc
                    
                INNER JOIN contabilidade.plano_analitica AS pa
                        ON pc.cod_conta = pa.cod_conta
                       AND pc.exercicio = pa.exercicio 
                    
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = pa.cod_plano
                       AND plano_banco.exercicio = pa.exercicio 
                    
                 LEFT JOIN tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as conta_bancaria
                                           (  cod_conta                          INTEGER
					    , tipo_aplicacao                    VARCHAR
					    , cod_ctb                           INTEGER
					    , tipo_conta                        INTEGER
					    , exercicio                         CHAR(4)  
					    , conta                             TEXT                         
                                            , conta_bancaria                    TEXT  
                                            , conta_corrente                    TEXT                                                         
                                            , cod_orgao                         INTEGER                                                        
                                            , banco                             VARCHAR                                                        
                                            , agencia             		TEXT                                                        
                                            , digito_verificador_agencia        TEXT                                                        
                                            , digito_verificador_conta_bancaria TEXT                                                        
                                            , desc_conta_bancaria               VARCHAR
                                            --, cod_plano                         INTEGER  
                                           )
                       ON conta_bancaria.cod_conta = pc.cod_conta
                      AND conta_bancaria.exercicio = pc.exercicio
                      --AND conta_bancaria.cod_plano = plano_banco.cod_plano
                          
                INNER JOIN contabilidade.conta_debito AS cd
                        ON pa.cod_plano = cd.cod_plano
                       AND pa.exercicio = cd.exercicio

                INNER JOIN contabilidade.valor_lancamento AS vl
                        ON cd.cod_lote     = vl.cod_lote
                       AND cd.tipo         = vl.tipo
                       AND cd.sequencia    = vl.sequencia
                       AND cd.exercicio    = vl.exercicio
                       AND cd.tipo_valor   = vl.tipo_valor
                       AND cd.cod_entidade = vl.cod_entidade
                 
                INNER JOIN contabilidade.lancamento
                        ON lancamento.exercicio    = vl.exercicio
                       AND lancamento.cod_entidade = vl.cod_entidade
                       AND lancamento.tipo         = vl.tipo
                       AND lancamento.cod_lote     = vl.cod_lote
                       AND lancamento.sequencia    = vl.sequencia

                 LEFT JOIN contabilidade.lancamento_receita
                        ON lancamento_receita.exercicio    = lancamento.exercicio
                       AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                       AND lancamento_receita.tipo         = lancamento.tipo
                       AND lancamento_receita.cod_lote     = lancamento.cod_lote
                       AND lancamento_receita.sequencia    = lancamento.sequencia

                INNER JOIN contabilidade.lote AS lo
                        ON vl.cod_lote     = lo.cod_lote
                       AND vl.tipo         = lo.tipo
                       AND vl.exercicio    = lo.exercicio
                       AND vl.cod_entidade = lo.cod_entidade
                       AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                       AND lo.exercicio = '".$this->getDado('exercicio')."'

                 LEFT JOIN orcamento.receita
                        ON receita.cod_receita = lancamento_receita.cod_receita
                       AND receita.exercicio   = lancamento_receita.exercicio

                 LEFT JOIN contabilidade.plano_recurso
                        ON pa.cod_plano = plano_recurso.cod_plano
                       AND pa.exercicio = plano_recurso.exercicio
                
                 LEFT JOIN orcamento.recurso
                        ON recurso.exercicio   = plano_recurso.exercicio
                       AND recurso.cod_recurso = plano_recurso.cod_recurso
                
                 LEFT JOIN contabilidade.lancamento_empenho
                        ON lancamento_empenho.exercicio    = lancamento.exercicio
                       AND lancamento_empenho.tipo         = lancamento.tipo
                       AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                       AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                       AND lancamento_empenho.sequencia    = lancamento.sequencia
 
                 LEFT JOIN orcamento.conta_receita
                        ON conta_receita.exercicio = receita.exercicio
                       AND conta_receita.cod_conta = receita.cod_conta 
                            
                 LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                        ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
                       AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_conta 
                    
                 LEFT JOIN tesouraria.transferencia
                        ON transferencia.cod_lote     = lo.cod_lote
                       AND transferencia.tipo         = lo.tipo
                       AND transferencia.exercicio    = lo.exercicio
                       AND transferencia.cod_entidade = lo.cod_entidade

                 LEFT JOIN tesouraria.transferencia_estornada
                        ON transferencia_estornada.cod_lote     = lo.cod_lote
                       AND transferencia_estornada.tipo         = lo.tipo
                       AND transferencia_estornada.exercicio    = lo.exercicio
                       AND transferencia_estornada.cod_entidade = lo.cod_entidade
                       
                 LEFT JOIN (
                            SELECT conta_debito.cod_lote
				 , conta_debito.tipo
				 , conta_debito.exercicio
				 , conta_debito.cod_entidade
				 , CASE WHEN (conta_bancaria.cod_ctb_anterior is null) THEN transferencia.cod_plano_credito
                                        Else conta_bancaria.cod_ctb_anterior
                                        END AS cod_ctb_anterior
				 , transferencia.cod_plano_credito
				 , transferencia.cod_plano_debito
				 , conta_debito.sequencia
                              FROM contabilidade.conta_debito
			INNER JOIN contabilidade.lote AS lo
				ON conta_debito.cod_lote     = lo.cod_lote
			       AND conta_debito.tipo         = lo.tipo
			       AND conta_debito.exercicio    = lo.exercicio
			       AND conta_debito.cod_entidade = lo.cod_entidade
			INNER JOIN tesouraria.transferencia
				ON transferencia.cod_plano_debito = conta_debito.cod_plano
				AND lo.cod_lote = transferencia.cod_lote
				AND transferencia.cod_entidade = lo.cod_entidade
				AND transferencia.tipo = 'T'
                                AND transferencia.exercicio = conta_debito.exercicio
			INNER JOIN contabilidade.plano_analitica
				ON plano_analitica.cod_plano = transferencia.cod_plano_credito
				AND plano_analitica.natureza_saldo = 'D'
                                AND plano_analitica.exercicio = conta_debito.exercicio
			LEFT JOIN tcemg.conta_bancaria
				ON conta_bancaria.cod_conta = plano_analitica.cod_conta
				AND conta_bancaria.exercicio = plano_analitica.exercicio
			WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
				AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")
                                AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
				AND conta_debito.tipo         = 'T'
                ) AS cod_ctb_transferencia
                        ON cod_ctb_transferencia.exercicio = cd.exercicio                             
                       AND cod_ctb_transferencia.sequencia = cd.sequencia
                       AND cod_ctb_transferencia.cod_lote = cd.cod_lote
                       AND cod_ctb_transferencia.tipo = cd.tipo
                       AND cod_ctb_transferencia.cod_plano_debito = cd.cod_plano
                       
                 LEFT JOIN tesouraria.arrecadacao_receita_dedutora AS RD
			ON RD.cod_receita_dedutora = lancamento_receita.cod_receita
		       AND RD.vl_deducao = vl.vl_lancamento
                       AND TO_DATE(RD.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') = lo.dt_lote

		 LEFT JOIN tesouraria.arrecadacao_receita_dedutora_estornada AS RDE
		        ON RDE.cod_receita_dedutora = RD.cod_receita_dedutora
		       AND RDE.cod_arrecadacao = RD.cod_arrecadacao
                
                 LEFT JOIN tesouraria.arrecadacao_receita AS ARR
                        ON ARR.cod_receita = RD.cod_receita_dedutora
                       AND TO_DATE(ARR.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') = lo.dt_lote

                 LEFT JOIN tesouraria.arrecadacao AS AR
                        ON AR.cod_arrecadacao = ARR.cod_arrecadacao 
                       AND AR.timestamp_arrecadacao = ARR.timestamp_arrecadacao
                        
                     WHERE pc.exercicio   = '".$this->getDado('exercicio')."' 
                       AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                       AND SUBSTR(REPLACE(pc.cod_estrutural, '.', ''), 1, 7) <> '1111101'
                       AND vl.tipo <> 'I'                       
                  GROUP BY tipo_registro
                         , cod_ctb
		         , conta_bancaria.tipo_aplicacao
			 , tipo_conta
                         , plano_banco.cod_entidade
                         , plano_recurso.cod_recurso
                         , pa.exercicio
                         , tipo_movimentacao
                         , tipo_entr_saida
                         , cod_ctb_transf
                         , cod_fonte_ctb_transf
                         , conta_receita.cod_estrutural
                    ) AS a
                    
                      GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13
                    UNION
                    
                       SELECT b.tipo_registro
                            , '' AS cod_reduzido_mov
                            , b.cod_orgao 
                            , b.cod_ctb
                            , LPAD(b.tipo_conta||b.cod_ctb||b.tipo_entr_saida||b.tipo_movimentacao||b.cod_fonte_recursos||LPAD(COALESCE(b.cod_fonte_ctb_transf::VARCHAR,'0'),3,'0'),17,'0')  AS cod_ctb_view
                           
                            , b.cod_fonte_recursos
                            , b.exercicio
                            , b.tipo_movimentacao
                            , b.tipo_entr_saida
                            , b.cod_ctb_transf AS cod_ctb_transf
                            , b.tipo_conta||b.cod_ctb AS cod_ctb_transf_view
                            , b.cod_fonte_ctb_transf
                            , b.tipo_conta AS tipo_conta
                           , SUM(b.valor_entr_saida) AS valor_entr_saida
                       FROM (
                   
                    SELECT
                            '21'::int  AS  tipo_registro
                          , SUM(vl.vl_lancamento) * -1 as valor_entr_saida
                          , plano_banco.cod_entidade as cod_orgao
                          , conta_bancaria.cod_ctb
                          , conta_bancaria.tipo_aplicacao
                          , conta_bancaria.tipo_conta::VARCHAR
                          , pa.exercicio
                          , plano_recurso.cod_recurso as cod_fonte_recursos
                          , '2'::VARCHAR AS tipo_movimentacao
                          , CASE 
                             WHEN lo.tipo = 'A' AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9' AND lancamento_receita.estorno = false AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325' THEN '01'
                             WHEN lo.tipo = 'A' AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1)= '9' 
                                  AND lancamento_receita.estorno = false AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'  
                                  AND  (RDE.vl_estornado IS NULL ) AND ((ARR.vl_arrecadacao IS NULL ) OR (AR.devolucao = true)) THEN '02'
                             WHEN lo.tipo = 'A' AND lancamento_receita.estorno = true AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9' THEN '03'
                             WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) = '1325' THEN '04'
                             WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 5 THEN '06'
                             WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 4 THEN '07'
                             WHEN lo.tipo = 'P' AND lancamento_empenho.estorno = false THEN '08'
                             WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 3 THEN '09'
                             WHEN lo.tipo = 'P' AND lancamento_empenho.estorno = true THEN '10'
                             WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 1
                                                AND transferencia_estornada.cod_lote_estorno IS NOT NULL
                                                AND ( SELECT CASE WHEN COUNT(plano_conta.*) > 0 THEN TRUE
                                                                  ELSE FALSE
                                                              END
                                                        FROM contabilidade.plano_conta     
                                                  INNER JOIN contabilidade.plano_analitica 
                                                          ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                         AND plano_conta.exercicio = plano_analitica.exercicio 
                                                       WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                                         AND plano_analitica.exercicio = transferencia.exercicio
                                                         AND (    SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351100000'
                                                               OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220101'
                                                               OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220102'
                                                               OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220103'
                                                               OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220104'
                                                               OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220199'
                                                               OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351300000' ) 
                                                         ) THEN '12'
                             WHEN lo.tipo = 'T' AND transferencia.cod_tipo = 1
                                                AND ( SELECT CASE WHEN COUNT(plano_conta.*) > 0 THEN TRUE
                                                                  ELSE FALSE
                                                             END
                                                        FROM contabilidade.plano_conta     
                                                  INNER JOIN contabilidade.plano_analitica 
                                                          ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                         AND plano_conta.exercicio = plano_analitica.exercicio 
                                                       WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                                         AND plano_analitica.exercicio = transferencia.exercicio
                                                         AND (    SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351100000'
                                                              OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220101'
                                                              OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220102'
                                                              OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220103'
                                                              OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220104'
                                                              OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220199'
                                                              OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351300000' ) 
                                                    ) THEN '13'    
                            WHEN lo.tipo = 'A'AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9' 
		 		 AND ( (RDE.vl_estornado IS NOT NULL) OR (ARR.vl_arrecadacao IS NOT NULL AND AR.devolucao = false) ) THEN '16' 
                             ELSE '99'
                            END AS tipo_entr_saida

                           , CASE WHEN (lo.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3) THEN
                                  (  cod_ctb_transferencia.cod_ctb_anterior
                              ) END::VARCHAR AS cod_ctb_transf

                           , CASE WHEN (lo.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4) OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3) THEN
                                  (  SELECT plano_recurso.cod_recurso 
                                       FROM contabilidade.plano_conta     
                                 INNER JOIN contabilidade.plano_analitica 
                                         ON plano_conta.cod_conta = plano_analitica.cod_conta
                                        AND plano_conta.exercicio = plano_analitica.exercicio 
                                 INNER JOIN contabilidade.plano_recurso
                                         ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                        AND plano_recurso.exercicio = plano_analitica.exercicio 
                                      WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                        AND plano_analitica.exercicio = transferencia.exercicio
                              ) END::VARCHAR AS cod_fonte_ctb_transf 
                
                      FROM contabilidade.plano_conta AS pc
                
                INNER JOIN contabilidade.plano_analitica AS pa
                        ON pc.cod_conta = pa.cod_conta
                       AND pc.exercicio = pa.exercicio 
                
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = pa.cod_plano
                       AND plano_banco.exercicio = pa.exercicio 
                
                 LEFT JOIN tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as conta_bancaria
                                           (  cod_conta                          INTEGER
					    , tipo_aplicacao                    VARCHAR
					    , cod_ctb                           INTEGER
					    , tipo_conta                        INTEGER
					    , exercicio                         CHAR(4)  
					    , conta                             TEXT                         
                                            , conta_bancaria                    TEXT  
                                            , conta_corrente                    TEXT                                                         
                                            , cod_orgao                         INTEGER                                                        
                                            , banco                             VARCHAR                                                        
                                            , agencia             		TEXT                                                        
                                            , digito_verificador_agencia        TEXT                                                        
                                            , digito_verificador_conta_bancaria TEXT                                                        
                                            , desc_conta_bancaria               VARCHAR
                                            --, cod_plano                         INTEGER  
                                           )
                       ON conta_bancaria.cod_conta = pc.cod_conta
                      AND conta_bancaria.exercicio = pc.exercicio
                      --AND conta_bancaria.cod_plano = plano_banco.cod_plano
                      
                INNER JOIN contabilidade.conta_credito AS cc
                        ON pa.cod_plano = cc.cod_plano
                       AND pa.exercicio = cc.exercicio
                
                INNER JOIN contabilidade.valor_lancamento AS vl
                        ON cc.cod_lote     = vl.cod_lote
                       AND cc.tipo         = vl.tipo
                       AND cc.sequencia    = vl.sequencia
                       AND cc.exercicio    = vl.exercicio
                       AND cc.tipo_valor   = vl.tipo_valor
                       AND cc.cod_entidade = vl.cod_entidade
                
                INNER JOIN contabilidade.lancamento
                        ON lancamento.exercicio    = vl.exercicio
                       AND lancamento.cod_entidade = vl.cod_entidade
                       AND lancamento.tipo         = vl.tipo
                       AND lancamento.cod_lote     = vl.cod_lote
                       AND lancamento.sequencia    = vl.sequencia

                 LEFT JOIN contabilidade.lancamento_receita
                        ON lancamento_receita.exercicio    = lancamento.exercicio
                       AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                       AND lancamento_receita.tipo         = lancamento.tipo
                       AND lancamento_receita.cod_lote     = lancamento.cod_lote
                       AND lancamento_receita.sequencia    = lancamento.sequencia

                INNER JOIN contabilidade.lote AS lo
                        ON vl.cod_lote     = lo.cod_lote
                       AND vl.tipo         = lo.tipo
                       AND vl.exercicio    = lo.exercicio
                       AND vl.cod_entidade = lo.cod_entidade
                       AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                       AND lo.exercicio = '".$this->getDado('exercicio')."'

                 LEFT JOIN orcamento.receita
                        ON receita.cod_receita = lancamento_receita.cod_receita
                       AND receita.exercicio   = lancamento_receita.exercicio

                 LEFT JOIN contabilidade.plano_recurso
                        ON pa.cod_plano = plano_recurso.cod_plano
                       AND pa.exercicio = plano_recurso.exercicio
                
                 LEFT JOIN orcamento.recurso
                        ON recurso.exercicio   = plano_recurso.exercicio
                       AND recurso.cod_recurso = plano_recurso.cod_recurso
                
                 LEFT JOIN contabilidade.lancamento_empenho
                        ON lancamento_empenho.exercicio    = lancamento.exercicio
                       AND lancamento_empenho.tipo         = lancamento.tipo
                       AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                       AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                       AND lancamento_empenho.sequencia    = lancamento.sequencia
                
                 LEFT JOIN orcamento.conta_receita
                        ON conta_receita.exercicio = receita.exercicio
                       AND conta_receita.cod_conta = receita.cod_conta 
                        
                 LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                        ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
                       AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_conta 
                
                 LEFT JOIN tesouraria.transferencia
                        ON transferencia.cod_lote     = lo.cod_lote
                       AND transferencia.tipo         = lo.tipo
                       AND transferencia.exercicio    = lo.exercicio
                       AND transferencia.cod_entidade = lo.cod_entidade

                 LEFT JOIN tesouraria.transferencia_estornada
                        ON transferencia_estornada.cod_lote     = lo.cod_lote
                       AND transferencia_estornada.tipo         = lo.tipo
                       AND transferencia_estornada.exercicio    = lo.exercicio
                       AND transferencia_estornada.cod_entidade = lo.cod_entidade
                
                 LEFT JOIN (
                            SELECT conta_credito.cod_lote
                                 , conta_credito.tipo
                                 , conta_credito.exercicio
                                 , conta_credito.cod_entidade
                                 , CASE WHEN (conta_bancaria.cod_ctb_anterior is null) THEN transferencia.cod_plano_debito
                                        Else conta_bancaria.cod_ctb_anterior
                                        END AS cod_ctb_anterior
                                 , transferencia.cod_plano_credito
                                 , transferencia.cod_plano_debito
                                 , conta_credito.sequencia
                              FROM contabilidade.conta_credito
			INNER JOIN contabilidade.lote AS lo
				ON conta_credito.cod_lote     = lo.cod_lote
                               AND  lo.tipo = 'T'
                               AND conta_credito.exercicio    = lo.exercicio
                               AND conta_credito.cod_entidade = lo.cod_entidade
			INNER JOIN tesouraria.transferencia
				ON transferencia.cod_plano_credito = conta_credito.cod_plano
			       AND lo.cod_lote = transferencia.cod_lote
			       AND transferencia.cod_entidade = lo.cod_entidade
			       AND transferencia.tipo = 'T'
                               AND transferencia.exercicio = conta_credito.exercicio
			INNER JOIN contabilidade.plano_analitica
				ON plano_analitica.cod_plano = transferencia.cod_plano_debito
			       AND plano_analitica.natureza_saldo = 'D'
                               AND plano_analitica.exercicio = conta_credito.exercicio
			 LEFT JOIN tcemg.conta_bancaria
				ON conta_bancaria.cod_conta = plano_analitica.cod_conta
			       AND conta_bancaria.exercicio = plano_analitica.exercicio
			     WHERE conta_credito.exercicio = '".$this->getDado('exercicio')."' 
			       AND conta_credito.cod_entidade IN (".$this->getDado('entidades').")
                               AND  lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                               AND conta_credito.tipo         = 'T'
                        )AS cod_ctb_transferencia
                        ON cod_ctb_transferencia.exercicio = cc.exercicio                             
                       AND cod_ctb_transferencia.sequencia = cc.sequencia
                       AND cod_ctb_transferencia.cod_lote = cc.cod_lote
                       AND cod_ctb_transferencia.tipo = cc.tipo
                       AND cod_ctb_transferencia.cod_plano_credito = cc.cod_plano 
                 
                 LEFT JOIN tesouraria.arrecadacao_receita_dedutora AS RD
                        ON RD.cod_receita_dedutora = lancamento_receita.cod_receita
                       AND RD.vl_deducao = vl.vl_lancamento
                       AND TO_DATE(RD.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') = lo.dt_lote

                 LEFT JOIN tesouraria.arrecadacao_receita_dedutora_estornada AS RDE
                        ON RDE.cod_receita_dedutora = RD.cod_receita_dedutora
                       AND RDE.cod_arrecadacao = RD.cod_arrecadacao
               
                 LEFT JOIN tesouraria.arrecadacao_receita AS ARR
                        ON ARR.cod_receita = RD.cod_receita_dedutora
                       AND TO_DATE(ARR.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') = lo.dt_lote

                 LEFT JOIN tesouraria.arrecadacao AS AR
                        ON AR.cod_arrecadacao = ARR.cod_arrecadacao 
                       AND AR.timestamp_arrecadacao = ARR.timestamp_arrecadacao
                 
                     WHERE pc.exercicio   = '".$this->getDado('exercicio')."' 
                       AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                       AND SUBSTR(REPLACE(pc.cod_estrutural, '.', ''), 1, 7) <> '1111101'
                       AND vl.tipo <> 'I'
                       
                  GROUP BY tipo_registro
                         , cod_ctb
		         , conta_bancaria.tipo_aplicacao
		         , tipo_conta
                         , plano_banco.cod_entidade
                         , plano_recurso.cod_recurso
                         , pa.exercicio
                         , tipo_entr_saida
                         , cod_ctb_transf
                         , cod_fonte_ctb_transf
                ) AS b
         GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13
         ORDER BY  cod_ctb,tipo_entr_saida, tipo_movimentacao
        ";
        return $stSql;
    }

    public function recuperaContasBancarias22(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancarias22",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasBancarias22()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "SELECT    c.tipo_registro
              , '' AS cod_reduzido_mov
              , c.e_deducao_de_receita
              , c.identificador_deducao
              , c.natureza_receita
              , c.cod_ctb
              , LPAD(c.cod_ctb||c.tipo_entr_saida||c.tipo_movimentacao||c.cod_fonte_recursos||'000',17,'0')  AS cod_ctb_view
              , c.tipo_aplicacao
              , c.tipo_conta
              , c.tipo
              , tipo_entr_saida
              , c.tipo_movimentacao
              , c.cod_fonte_recursos
              , ABS(SUM(c.vlr_receita_cont)) AS vlr_receita_cont
           FROM ( SELECT '22'::int  AS  tipo_registro
                       , replace(pc.cod_estrutural,'.','') as cod_reduzido_mov
                       , CASE WHEN (substr(conta_receita.cod_estrutural,1,1) = '9') THEN
                          '1'::INTEGER
                         ELSE 
                          '2'::INTEGER
                         END AS e_deducao_de_receita
                       , receita_indentificadores_peculiar_receita.cod_identificador as identificador_deducao
                       , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 2, 8)::integer
                           ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240101
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17240102
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER = 17219903
                                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                                    
                                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                                 END
                           END AS natureza_receita
                       , SUM(vl.vl_lancamento) as vlr_receita_cont
                       , conta_bancaria.cod_ctb
		       , conta_bancaria.tipo_aplicacao
                       , conta_bancaria.tipo_conta::VARCHAR
                       , lo.tipo
                       , CASE WHEN lo.tipo = 'A' AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9'
                                    AND lancamento_receita.estorno = false AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325' THEN '01'
                               WHEN lo.tipo = 'A' AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9'
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'
                                    AND  (RDE.vl_estornado IS NULL) AND ((ARR.vl_arrecadacao IS NULL ) OR (AR.devolucao = true)) THEN '02'
                               WHEN lo.tipo = 'A' AND lancamento_receita.estorno = true AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9' THEN '03'
                               WHEN lo.tipo = 'A'AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9' 
					AND ( (RDE.vl_estornado IS NOT NULL) OR (ARR.vl_arrecadacao IS NOT NULL AND AR.devolucao = false) ) THEN '16'                       
                               ELSE '99'
                           END AS tipo_entr_saida 
                      , conta_debito_credito.tipo_movimentacao
                      , plano_recurso.cod_recurso AS cod_fonte_recursos
                    FROM contabilidade.plano_conta AS pc
              INNER JOIN contabilidade.plano_analitica AS pa
                      ON pc.cod_conta = pa.cod_conta
                     AND pc.exercicio = pa.exercicio 
              INNER JOIN contabilidade.plano_banco
                      ON plano_banco.cod_plano = pa.cod_plano
                     AND plano_banco.exercicio = pa.exercicio 
               LEFT JOIN tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as conta_bancaria
                                           (  cod_conta                          INTEGER
					    , tipo_aplicacao                    VARCHAR
					    , cod_ctb                           INTEGER
					    , tipo_conta                        INTEGER
					    , exercicio                         CHAR(4)  
					    , conta                             TEXT                         
                                            , conta_bancaria                    TEXT  
                                            , conta_corrente                    TEXT                                                         
                                            , cod_orgao                         INTEGER                                                        
                                            , banco                             VARCHAR                                                        
                                            , agencia             		TEXT                                                        
                                            , digito_verificador_agencia        TEXT                                                        
                                            , digito_verificador_conta_bancaria TEXT                                                        
                                            , desc_conta_bancaria               VARCHAR
                                            --, cod_plano                         INTEGER  
                                           )
                      ON conta_bancaria.cod_conta = pc.cod_conta
                     AND conta_bancaria.exercicio = pc.exercicio
                     --AND conta_bancaria.cod_plano = plano_banco.cod_plano
              INNER JOIN (
                           SELECT cod_plano
                                , exercicio
                                , cod_lote
                                , tipo
                                , sequencia
                                , tipo_valor
                                , cod_entidade
                                , '1'::VARCHAR AS tipo_movimentacao 
                             FROM contabilidade.conta_debito

                        UNION ALL

                           SELECT cod_plano
                                , exercicio
                                , cod_lote
                                , tipo
                                , sequencia
                                , tipo_valor
                                , cod_entidade
                                , '2'::VARCHAR AS tipo_movimentacao 
                             FROM contabilidade.conta_credito
                       ) AS conta_debito_credito
                      ON pa.cod_plano = conta_debito_credito.cod_plano
                     AND pa.exercicio = conta_debito_credito.exercicio
              INNER JOIN contabilidade.valor_lancamento AS vl
                      ON conta_debito_credito.cod_lote     = vl.cod_lote
                     AND conta_debito_credito.tipo         = vl.tipo
                     AND conta_debito_credito.sequencia    = vl.sequencia
                     AND conta_debito_credito.exercicio    = vl.exercicio
                     AND conta_debito_credito.tipo_valor   = vl.tipo_valor
                     AND conta_debito_credito.cod_entidade = vl.cod_entidade
              INNER JOIN contabilidade.lancamento
                      ON lancamento.exercicio    = vl.exercicio
                     AND lancamento.cod_entidade = vl.cod_entidade
                     AND lancamento.tipo         = vl.tipo
                     AND lancamento.cod_lote     = vl.cod_lote
                     AND lancamento.sequencia    = vl.sequencia
               LEFT JOIN contabilidade.lancamento_receita
                      ON lancamento_receita.exercicio    = lancamento.exercicio
                     AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                     AND lancamento_receita.tipo         = lancamento.tipo
                     AND lancamento_receita.cod_lote     = lancamento.cod_lote
                     AND lancamento_receita.sequencia    = lancamento.sequencia
              INNER JOIN contabilidade.lote AS lo
                      ON vl.cod_lote     = lo.cod_lote
                     AND vl.exercicio    = lo.exercicio
                     AND vl.cod_entidade = lo.cod_entidade
                     AND lo.tipo = 'A'
               LEFT JOIN orcamento.receita
                      ON receita.cod_receita = lancamento_receita.cod_receita
                     AND receita.exercicio   = lancamento_receita.exercicio
               LEFT JOIN contabilidade.plano_recurso
                      ON pa.cod_plano = plano_recurso.cod_plano
                     AND pa.exercicio = plano_recurso.exercicio
               LEFT JOIN orcamento.recurso
                      ON recurso.exercicio   = plano_recurso.exercicio
                     AND recurso.cod_recurso = plano_recurso.cod_recurso
               LEFT JOIN contabilidade.lancamento_empenho
                      ON lancamento_empenho.exercicio    = lancamento.exercicio
                     AND lancamento_empenho.tipo         = lancamento.tipo
                     AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                     AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                     AND lancamento_empenho.sequencia    = lancamento.sequencia
               LEFT JOIN orcamento.conta_receita
                      ON conta_receita.exercicio = receita.exercicio
                     AND conta_receita.cod_conta = receita.cod_conta 
               LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                      ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
                     AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_receita 
               LEFT JOIN tesouraria.arrecadacao_receita_dedutora AS RD
                      ON RD.cod_receita_dedutora = lancamento_receita.cod_receita
                     AND RD.vl_deducao = vl.vl_lancamento
                     AND TO_DATE(RD.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') = lo.dt_lote
               LEFT JOIN tesouraria.arrecadacao_receita_dedutora_estornada AS RDE
                      ON RDE.cod_receita_dedutora = RD.cod_receita_dedutora
                     AND RDE.cod_arrecadacao = RD.cod_arrecadacao
               LEFT JOIN tesouraria.arrecadacao_receita AS ARR
                      ON ARR.cod_receita = RD.cod_receita_dedutora
                     AND TO_DATE(ARR.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') = lo.dt_lote
               LEFT JOIN tesouraria.arrecadacao AS AR
                      ON AR.cod_arrecadacao = ARR.cod_arrecadacao 
                     AND AR.timestamp_arrecadacao = ARR.timestamp_arrecadacao
            
                   WHERE pc.exercicio = '".$this->getDado('exercicio')."'
                     AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                
                GROUP BY 2,3,4,5,7,8,9,10,11,12,13
              ) AS c
          WHERE tipo_entr_saida != '99'
       GROUP BY c.tipo_registro
              , c.cod_ctb
	      , c.tipo_aplicacao
	      , c.tipo_conta
	      , c.e_deducao_de_receita
	      , c.identificador_deducao
	      , c.natureza_receita
	      , c.tipo
              , tipo_entr_saida
              , c.tipo_movimentacao
              , c.cod_fonte_recursos

       ORDER BY c.cod_ctb, c.natureza_receita";
    return $stSql;
    }
    public function __destruct(){}

}
?>