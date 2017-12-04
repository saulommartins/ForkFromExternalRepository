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
    * $Id: TTCEMGCAIXA.class.php 64261 2015-12-23 12:21:21Z lisiane $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGCAIXA extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaCAIXA101(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCAIXA101",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCAIXA101()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
                 SELECT  '10' as tipo_registro
                            , plano_banco.cod_entidade as cod_orgao
                            , cod_estrutural
                            , SUM(c.vl_saldo_anterior ) as vl_saldo_inicial 
                            , SUM(c.vl_saldo_atual) as vl_saldo_final
                            , indicador_superavit        
                    FROM  ( 
                              SELECT cod_estrutural                                                                       
                                        , nivel                                                                                
                                        , nom_conta                                                                            
                                        , cod_sistema                                                                          
                                        , indicador_superavit                                                                  
                                        , vl_saldo_anterior                                                                
                                        , vl_saldo_debitos                                                                 
                                        , vl_saldo_creditos                                                                
                                        , vl_saldo_atual                                                                   
                                   FROM                                                                                        
                                     contabilidade.fn_rl_balancete_verificacao('".$this->getDado('exercicio')."',' cod_entidade IN  (".$this->getDado('entidades')." ) ','".$this->getDado('dtInicio')."','".$this->getDado('dtFim')."','A'::CHAR)
                                       as retorno( cod_estrutural varchar                                                      
                                                  ,nivel integer                                                               
                                                  ,nom_conta varchar                                                           
                                                  ,cod_sistema integer                                                         
                                                  ,indicador_superavit char(12)                                                
                                                  ,vl_saldo_anterior numeric                                                   
                                                  ,vl_saldo_debitos  numeric                                                   
                                                  ,vl_saldo_creditos numeric                                                   
                                                  ,vl_saldo_atual    numeric                                                   
                                                  )                                                                           
                                  WHERE cod_estrutural like '1.1.1.%'
                             ) as c
              INNER JOIN  contabilidade.plano_conta
                      ON  plano_conta.cod_estrutural = c.cod_estrutural
                     AND  plano_conta.exercicio = '".$this->getDado('exercicio')."'
                     AND  plano_conta.indicador_superavit = c.indicador_superavit                     
              INNER JOIN  contabilidade.plano_analitica
                      ON  plano_analitica.cod_conta = plano_conta.cod_conta
                     AND  plano_analitica.exercicio = plano_banco.exercicio
              INNER JOIN contabilidade.plano_banco
                      ON plano_banco.cod_plano = plano_analitica.plano_banco
                     AND plano_banco.exercicio =   plano_analitica.exercicio 
           
                GROUP BY  tipo_registro
                        , c.cod_estrutural
                        , cod_orgao
                ";
        return $stSql;
    }
    
    public function recuperaCAIXA10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCAIXA10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCAIXA10()
    {
        $stSql = "
              SELECT 10 AS tipo_registro
                   , LPAD('".$this->getDado("cod_orgao")."',4,'0') AS cod_orgao
                   , SUM(conta.vl_saldo_anterior) AS vl_saldo_inicial
                   , SUM(conta.vl_saldo_atual) AS vl_saldo_final
                FROM (
                      SELECT cod_estrutural
                           , nivel
                           , nom_conta
                           , cod_sistema
                           , indicador_superavit
                           , vl_saldo_anterior
                           , vl_saldo_debitos
                           , vl_saldo_creditos
                           , vl_saldo_atual
                        FROM contabilidade.fn_rl_balancete_verificacao( '".$this->getDado('exercicio')."'
                                                                      , ' cod_entidade IN  (".$this->getDado('entidades').") '
                                                                      , '".$this->getDado('dtInicio')."'
                                                                      , '".$this->getDado('dtFim')."'
                                                                      , 'S'::CHAR
                                                                      )
                          AS retorno ( cod_estrutural varchar
                                     , nivel integer
                                     , nom_conta varchar
                                     , cod_sistema integer
                                     , indicador_superavit char(12)
                                     , vl_saldo_anterior numeric
                                     , vl_saldo_debitos  numeric
                                     , vl_saldo_creditos numeric
                                     , vl_saldo_atual    numeric
                                   )
                           WHERE cod_estrutural like '1.1.1.1.1.01%'
                        ) as conta
                GROUP BY cod_orgao, tipo_registro
        ";
        return $stSql;
    }
    
     public function recuperaCAIXA11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCAIXA11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCAIXA11()
    {    
        $stSql = "
        
 SELECT tipo_registro                                     
                                    , replace(tabela.cod_estrutural,'.','') AS cod_estrutural                                     
                                    , cod_fonte_caixa
                                    , 0.00::NUMERIC(14,2) AS vl_saldo_inicial_fonte
                                    , 0.00::NUMERIC(14,2) AS vl_saldo_final_fonte
                    FROM ( SELECT '11'::int  AS  tipo_registro                                 
                                , plano_conta.cod_estrutural                                
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5)
                                         OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3)
                                         OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4)
                                       THEN (
                                             SELECT plano_recurso.cod_recurso 
                                               FROM contabilidade.plano_conta     
                                         INNER JOIN contabilidade.plano_analitica 
                                                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                AND plano_conta.exercicio = plano_analitica.exercicio 
                                         INNER JOIN contabilidade.plano_recurso
                                                 ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                                AND plano_recurso.exercicio = plano_analitica.exercicio
                                              WHERE plano_analitica.cod_plano = transferencia.cod_plano_credito
                                                AND plano_analitica.exercicio = transferencia.exercicio
                                            )::VARCHAR
                                       ELSE plano_recurso.cod_recurso::VARCHAR
                                    END::VARCHAR AS cod_fonte_caixa
                                , COALESCE(SUM(COALESCE(valor_lancamento.vl_lancamento,0.00)),0.00) as valor_entr_saida
                            FROM contabilidade.conta_debito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.cod_lote = conta_debito.cod_lote
                             AND valor_lancamento.tipo = conta_debito.tipo
                             AND valor_lancamento.sequencia = conta_debito.sequencia
                             AND valor_lancamento.exercicio = conta_debito.exercicio
                             AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                             AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.sequencia = valor_lancamento.sequencia
                             AND lancamento.cod_lote = valor_lancamento.cod_lote
                             AND lancamento.tipo = valor_lancamento.tipo
                             AND lancamento.exercicio = valor_lancamento.exercicio
                             AND lancamento.cod_entidade = valor_lancamento.cod_entidade

                       LEFT JOIN contabilidade.lancamento_empenho
                              ON lancamento_empenho.exercicio    = lancamento.exercicio
                             AND lancamento_empenho.tipo         = lancamento.tipo
                             AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                             AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                             AND lancamento_empenho.sequencia    = lancamento.sequencia

                      INNER JOIN contabilidade.lote
                              ON lote.cod_lote = lancamento.cod_lote
                             AND lote.exercicio = lancamento.exercicio
                             AND lote.tipo = lancamento.tipo
                             AND lote.cod_entidade = lancamento.cod_entidade
                             AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo != 'I'

                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = conta_debito.cod_plano
                             AND plano_analitica.exercicio = conta_debito.exercicio

                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio  

                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio                       

                       LEFT JOIN tesouraria.transferencia 
                              ON transferencia.exercicio = lote.exercicio
                             AND transferencia.cod_entidade = lote.cod_entidade
                             AND transferencia.tipo = lote.tipo
                             AND transferencia.cod_lote = lote.cod_lote
                       LEFT JOIN tesouraria.transferencia_estornada 
                              ON transferencia_estornada.exercicio = lote.exercicio
                             AND transferencia_estornada.cod_entidade = lote.cod_entidade
                             AND transferencia_estornada.tipo = lote.tipo
                             AND transferencia_estornada.cod_lote_estorno = lote.cod_lote

                           WHERE conta_debito.exercicio =  '".$this->getDado('exercicio')."'
                             AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")  
                             AND plano_conta.cod_estrutural like  '1.1.1.1.1.01%'

                        GROUP BY tipo_registro                                
                               , plano_conta.cod_estrutural                                 
                               , cod_fonte_caixa

              UNION
                          SELECT '11'::int  AS  tipo_registro                                
                               , plano_conta.cod_estrutural                              
                               , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5)
                                        OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3)
                                        OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4)
                                      THEN (
                                            SELECT plano_recurso.cod_recurso 
                                              FROM contabilidade.plano_conta     
                                        INNER JOIN contabilidade.plano_analitica 
                                                ON plano_conta.cod_conta = plano_analitica.cod_conta
                                               AND plano_conta.exercicio = plano_analitica.exercicio 
                                        INNER JOIN contabilidade.plano_recurso
                                                ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                               AND plano_recurso.exercicio = plano_analitica.exercicio
                                             WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                               AND plano_analitica.exercicio = transferencia.exercicio
                                           )::VARCHAR
                                      ELSE plano_recurso.cod_recurso::VARCHAR
                                  END::VARCHAR AS cod_fonte_caixa
                               , COALESCE(SUM(COALESCE(valor_lancamento.vl_lancamento,0.00)),0.00) as valor_entr_saida
                            FROM contabilidade.conta_credito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.cod_lote = conta_credito.cod_lote
                             AND valor_lancamento.tipo = conta_credito.tipo
                             AND valor_lancamento.sequencia = conta_credito.sequencia
                             AND valor_lancamento.exercicio = conta_credito.exercicio
                             AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                             AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.sequencia = valor_lancamento.sequencia
                             AND lancamento.cod_lote = valor_lancamento.cod_lote
                             AND lancamento.tipo = valor_lancamento.tipo
                             AND lancamento.exercicio = valor_lancamento.exercicio
                             AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                      INNER JOIN contabilidade.lote
                              ON lote.cod_lote = lancamento.cod_lote
                             AND lote.exercicio = lancamento.exercicio
                             AND lote.tipo = lancamento.tipo
                             AND lote.cod_entidade = lancamento.cod_entidade
                             AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo != 'I'
                             AND lote.cod_entidade IN (".$this->getDado('entidades').")  
                       LEFT JOIN contabilidade.lancamento_empenho
                              ON lancamento_empenho.exercicio    = lancamento.exercicio
                             AND lancamento_empenho.tipo         = lancamento.tipo
                             AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                             AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                             AND lancamento_empenho.sequencia    = lancamento.sequencia
                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = conta_credito.cod_plano
                             AND plano_analitica.exercicio = conta_credito.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio  
                      INNER JOIN contabilidade.plano_banco
                              ON plano_banco.cod_plano = conta_credito.cod_plano
                             AND plano_banco.exercicio = conta_credito.exercicio
                             AND plano_banco.cod_entidade IN  (".$this->getDado('entidades').")  
                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio                      

                       LEFT JOIN tesouraria.transferencia 
                              ON transferencia.exercicio = lote.exercicio
                             AND transferencia.cod_entidade = lote.cod_entidade
                             AND transferencia.tipo = lote.tipo
                             AND transferencia.cod_lote = lote.cod_lote
                       LEFT JOIN tesouraria.transferencia_estornada 
                              ON transferencia_estornada.exercicio = lote.exercicio
                             AND transferencia_estornada.cod_entidade = lote.cod_entidade
                             AND transferencia_estornada.tipo = lote.tipo
                             AND transferencia_estornada.cod_lote_estorno = lote.cod_lote

                           WHERE conta_credito.exercicio = '".$this->getDado('exercicio')."'
                             AND conta_credito.cod_entidade IN (".$this->getDado('entidades').")  
                             AND plano_conta.cod_estrutural like  '1.1.1.1.1.01%'

                        GROUP BY tipo_registro                                
                               , plano_conta.cod_estrutural                                 
                               , cod_fonte_caixa
                    ) as tabela
                    
            GROUP BY tipo_registro
                   , cod_estrutural
                   , cod_fonte_caixa

                    ";

        return $stSql;
    }
    
    public function recuperaCAIXA12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCAIXA12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCAIXA12()
    {
        $stSql = " SELECT tipo_registro
                        
                        , CASE WHEN tipo_entr_saida = '09'
                               THEN TRIM((exercicio||tipo_movimentacao::VARCHAR||'09'||cod_fonte_caixa::VARCHAR))
                               WHEN TRIM(cod_ctb_transf) = '' AND cod_fonte_ctb_transf IS NULL AND tipo_entr_saida = '06'
                               THEN TRIM((exercicio||tipo_movimentacao::VARCHAR||'09'||cod_fonte_caixa::VARCHAR))
                               ELSE TRIM((exercicio||tipo_movimentacao::VARCHAR||tipo_entr_saida||cod_ctb_transf||COALESCE(cod_fonte_ctb_transf,'')))
                           END AS cod_reduzido

                        , cod_fonte_caixa
                        , tipo_movimentacao
                        , CASE WHEN cod_fonte_ctb_transf <> cod_fonte_caixa AND tipo_entr_saida = '03'
                                 OR cod_fonte_ctb_transf <> cod_fonte_caixa AND tipo_entr_saida = '04'
                               THEN tipo_entr_saida
                               ELSE tipo_entr_saida
                           END AS tipo_entrada_saida
                        
                        , CASE WHEN cod_fonte_ctb_transf <> cod_fonte_caixa AND tipo_entr_saida = '03'
                                 OR cod_fonte_ctb_transf <> cod_fonte_caixa AND tipo_entr_saida = '04'
                               THEN TRIM(descr_movimentacao)
                               ELSE TRIM(descr_movimentacao)
                           END AS descricao_movimentacao
                        
                        , SUM(valor_entr_saida) AS valor_entr_saida
                        , cod_orgao
                        , exercicio
                        , CASE WHEN tipo_entr_saida = '09'
                               THEN ' '
                               ELSE cod_ctb_transf
                           END AS cod_ctb_transfe

                        , CASE WHEN tipo_entr_saida = '09'
                               THEN NULL
                               ELSE cod_fonte_ctb_transf
                           END AS cod_fonte_ctb_transfe
                 
                    FROM ( SELECT '12'::int  AS  tipo_registro
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
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
                                            )
                                       ELSE plano_recurso.cod_recurso
                                    END::VARCHAR AS cod_fonte_caixa
                                , conta_debito.tipo 
                                , CASE  WHEN conta_debito.tipo_valor = 'D' THEN
                                            1::integer
                                        WHEN conta_debito.tipo_valor = 'C' THEN
                                            2::integer
                                  END AS tipo_movimentacao
                                , CASE  WHEN lote.tipo = 'A'
                                        THEN '01'
                                        WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 5 AND conta_debito.tipo_valor = 'D' THEN '03'
                                        WHEN lancamento_empenho.estorno = true OR (conta_debito.cod_lote = transferencia_estornada.cod_lote_estorno) THEN '08'
                                        WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 2 AND conta_debito.tipo_valor = 'D' THEN '10' 
                                        WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 1 AND conta_debito.tipo_valor = 'C' THEN '10' 
                                  END AS tipo_entr_saida
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 2 AND conta_debito.tipo_valor = 'D') THEN 'OUTROS'
                                       WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 1 AND conta_debito.tipo_valor = 'C') THEN 'OUTROS'
                                  END AS descr_movimentacao
                                , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as valor_entr_saida
                                --, replace(plano_conta.cod_estrutural,'.','') AS cod_estrutural 
                                , conta_debito.cod_entidade AS cod_orgao
                                , plano_analitica.exercicio
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                            cod_ctb_transferencia.cod_ctb_anterior::VARCHAR
                                       ELSE ' ' 
                                  END AS cod_ctb_transf   
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
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
                            FROM contabilidade.conta_debito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.cod_lote = conta_debito.cod_lote
                             AND valor_lancamento.tipo = conta_debito.tipo
                             AND valor_lancamento.sequencia = conta_debito.sequencia
                             AND valor_lancamento.exercicio = conta_debito.exercicio
                             AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                             AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.sequencia = valor_lancamento.sequencia
                             AND lancamento.cod_lote = valor_lancamento.cod_lote
                             AND lancamento.tipo = valor_lancamento.tipo
                             AND lancamento.exercicio = valor_lancamento.exercicio
                             AND lancamento.cod_entidade = valor_lancamento.cod_entidade

                       LEFT JOIN contabilidade.lancamento_empenho
                              ON lancamento_empenho.exercicio    = lancamento.exercicio
                             AND lancamento_empenho.tipo         = lancamento.tipo
                             AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                             AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                             AND lancamento_empenho.sequencia    = lancamento.sequencia

                      INNER JOIN contabilidade.lote
                              ON lote.cod_lote = lancamento.cod_lote
                             AND lote.exercicio = lancamento.exercicio
                             AND lote.tipo = lancamento.tipo
                             AND lote.cod_entidade = lancamento.cod_entidade
                             AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo != 'I'

                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = conta_debito.cod_plano
                             AND plano_analitica.exercicio = conta_debito.exercicio

                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio  

                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio

                      INNER JOIN contabilidade.plano_banco
                              ON plano_banco.cod_plano = conta_debito.cod_plano
                             AND plano_banco.exercicio = conta_debito.exercicio
                             AND plano_banco.cod_entidade IN  (".$this->getDado('entidades').")           

                       LEFT JOIN tesouraria.transferencia 
                              ON transferencia.exercicio = lote.exercicio
                             AND transferencia.cod_entidade = lote.cod_entidade
                             AND transferencia.tipo = lote.tipo
                             AND transferencia.cod_lote = lote.cod_lote

                       LEFT JOIN tesouraria.transferencia_estornada 
                              ON transferencia_estornada.exercicio = lote.exercicio
                             AND transferencia_estornada.cod_entidade = lote.cod_entidade
                             AND transferencia_estornada.tipo = lote.tipo
                             AND transferencia_estornada.cod_lote_estorno = lote.cod_lote  
                        
                       LEFT JOIN ( SELECT conta_debito.cod_lote
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
                                      AND plano_analitica.exercicio = conta_debito.exercicio
                                LEFT JOIN tcemg.conta_bancaria
                                       ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                                      AND conta_bancaria.exercicio = plano_analitica.exercicio
                                   WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
                                     AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")
                                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                     AND conta_debito.tipo         = 'T'
                                ) AS cod_ctb_transferencia
                              ON cod_ctb_transferencia.exercicio = conta_debito.exercicio                           
                             AND cod_ctb_transferencia.sequencia = conta_debito.sequencia
                             AND cod_ctb_transferencia.cod_lote = conta_debito.cod_lote
                             AND cod_ctb_transferencia.tipo = conta_debito.tipo
                             AND cod_ctb_transferencia.cod_plano_debito = conta_debito.cod_plano
                        
                           WHERE conta_debito.exercicio =  '".$this->getDado('exercicio')."'
                             AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")  
                             AND plano_conta.cod_estrutural like  '1.1.1.1.1.01%'

                        GROUP BY tipo_registro
                               , cod_fonte_caixa
                               , conta_debito.tipo 
                               , tipo_movimentacao
                               , tipo_entr_saida
                               , descr_movimentacao
                               , cod_orgao
                               , plano_analitica.exercicio
                               , cod_ctb_transf   
                               , cod_fonte_ctb_transf  

              UNION
                          SELECT '12'::int  AS  tipo_registro
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                            (   SELECT plano_recurso.cod_recurso 
                                                  FROM contabilidade.plano_conta     
                                            INNER JOIN contabilidade.plano_analitica 
                                                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                   AND plano_conta.exercicio = plano_analitica.exercicio 
                                            INNER JOIN contabilidade.plano_recurso
                                                    ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                                   AND plano_recurso.exercicio = plano_analitica.exercicio
                                                WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                                   AND plano_analitica.exercicio = transferencia.exercicio
                                            )
                                       ELSE plano_recurso.cod_recurso
                                    END::VARCHAR AS cod_fonte_caixa
                                , conta_credito.tipo 
                                , CASE WHEN conta_credito.tipo_valor = 'D' THEN
                                         1::integer
                                         WHEN conta_credito.tipo_valor = 'C' THEN
                                         2::integer
                                  END AS tipo_movimentacao
                                , CASE WHEN lote.tipo = 'A' AND lancamento_receita.estorno != 't'
                                       THEN '01'
                               WHEN lote.tipo ='T' AND transferencia.cod_tipo =5 AND conta_credito.tipo_valor = 'D' THEN '03'
                               WHEN lote.tipo ='T' AND transferencia.cod_tipo =5 AND conta_credito.tipo_valor = 'C' THEN '04'
                                       WHEN lote.tipo ='P' AND conta_credito.tipo_valor = 'C'   THEN '06'
                                       WHEN lancamento_empenho.estorno = true
                                         OR (conta_credito.cod_lote = transferencia_estornada.cod_lote_estorno)
                                         OR (lancamento_receita.estorno = 't') THEN '08'
                                       WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 2 AND conta_credito.tipo_valor = 'D' THEN '10' 
                                       WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 1 AND conta_credito.tipo_valor = 'C' THEN '06' 
                                  END AS tipo_entr_saida
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 2 AND conta_credito.tipo_valor = 'D') THEN 'OUTROS'
                                       --WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 1 AND conta_credito.tipo_valor = 'C') THEN 'OUTROS'
                                  END AS descr_movimentacao
                                , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) * -1 as valor_entr_saida
                                --, replace(plano_conta.cod_estrutural,'.','') AS cod_estrutural
                                , conta_credito.cod_entidade AS cod_orgao
                                , plano_analitica.exercicio
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                            cod_ctb_transferencia.cod_ctb_anterior::VARCHAR
                                      ELSE ' ' 
                                  END AS cod_ctb_transf   
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5 AND conta_credito.tipo_valor = 'C') OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                    (   SELECT plano_recurso.cod_recurso 
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
                            FROM contabilidade.conta_credito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.cod_lote = conta_credito.cod_lote
                             AND valor_lancamento.tipo = conta_credito.tipo
                             AND valor_lancamento.sequencia = conta_credito.sequencia
                             AND valor_lancamento.exercicio = conta_credito.exercicio
                             AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                             AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.sequencia = valor_lancamento.sequencia
                             AND lancamento.cod_lote = valor_lancamento.cod_lote
                             AND lancamento.tipo = valor_lancamento.tipo
                             AND lancamento.exercicio = valor_lancamento.exercicio
                             AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                      INNER JOIN contabilidade.lote
                              ON lote.cod_lote = lancamento.cod_lote
                             AND lote.exercicio = lancamento.exercicio
                             AND lote.tipo = lancamento.tipo
                             AND lote.cod_entidade = lancamento.cod_entidade
                             AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo != 'I'
                             AND lote.cod_entidade IN (".$this->getDado('entidades').")  
                       LEFT JOIN contabilidade.lancamento_empenho
                              ON lancamento_empenho.exercicio    = lancamento.exercicio
                             AND lancamento_empenho.tipo         = lancamento.tipo
                             AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                             AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                             AND lancamento_empenho.sequencia    = lancamento.sequencia
                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = conta_credito.cod_plano
                             AND plano_analitica.exercicio = conta_credito.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio  
                      INNER JOIN contabilidade.plano_banco
                              ON plano_banco.cod_plano = conta_credito.cod_plano
                             AND plano_banco.exercicio = conta_credito.exercicio
                             AND plano_banco.cod_entidade IN  (".$this->getDado('entidades').")  
                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio
                       LEFT JOIN tesouraria.transferencia 
                              ON transferencia.exercicio = lote.exercicio
                             AND transferencia.cod_entidade = lote.cod_entidade
                             AND transferencia.tipo = lote.tipo
                             AND transferencia.cod_lote = lote.cod_lote
                       LEFT JOIN tesouraria.transferencia_estornada 
                              ON transferencia_estornada.exercicio = lote.exercicio
                             AND transferencia_estornada.cod_entidade = lote.cod_entidade
                             AND transferencia_estornada.tipo = lote.tipo
                             AND transferencia_estornada.cod_lote_estorno = lote.cod_lote  
                       LEFT JOIN contabilidade.lancamento_receita
                              ON lancamento_receita.exercicio = lancamento.exercicio
                             AND lancamento_receita.sequencia= lancamento.sequencia 
                             AND lancamento_receita.cod_lote = lancamento.cod_lote
                             AND lancamento_receita.tipo = lancamento.tipo
                             AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                       LEFT JOIN ( SELECT conta_credito.cod_lote
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
                                      AND conta_credito.tipo         = lo.tipo
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
                                      AND plano_analitica.exercicio = conta_credito.exercicio
                                LEFT JOIN tcemg.conta_bancaria
                                       ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                                      AND conta_bancaria.exercicio = plano_analitica.exercicio
                                   WHERE conta_credito.exercicio = '".$this->getDado('exercicio')."'
                                     AND conta_credito.cod_entidade IN (".$this->getDado('entidades').")
                                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                     AND conta_credito.tipo         = 'T'
                                ) AS cod_ctb_transferencia
                              ON cod_ctb_transferencia.exercicio = conta_credito.exercicio                           
                             AND cod_ctb_transferencia.sequencia = conta_credito.sequencia
                             AND cod_ctb_transferencia.cod_lote  = conta_credito.cod_lote
                             AND cod_ctb_transferencia.tipo = conta_credito.tipo
                             AND cod_ctb_transferencia.cod_plano_credito = conta_credito.cod_plano
                       
                           WHERE conta_credito.exercicio = '".$this->getDado('exercicio')."'
                             AND conta_credito.cod_entidade IN (".$this->getDado('entidades').")  
                             AND plano_conta.cod_estrutural like  '1.1.1.1.1.01%'
                             AND ( transferencia.cod_tipo <> 1 OR transferencia.cod_tipo IS NULL)

                        GROUP BY tipo_registro
                               , cod_fonte_caixa
                               , conta_credito.tipo 
                               , tipo_movimentacao
                               , tipo_entr_saida
                               , descr_movimentacao
                               , cod_orgao
                               , plano_analitica.exercicio
                               , cod_ctb_transf   
                               , cod_fonte_ctb_transf
                               
                               
                               
                               
                               
              UNION
              
                        SELECT tipo_registro
               , cod_fonte_caixa
               , tipo 
               , tipo_movimentacao
               , '06'::VARCHAR AS tipo_entr_saida
               , NULL AS descr_movimentacao
               , COALESCE(SUM(valor_entr_saida),0.00) as valor_entr_saida
               , cod_orgao
               , exercicio
               , ' '::VARCHAR AS cod_ctb_transf   
               , NULL AS cod_fonte_ctb_transf
                                
            FROM (
                          SELECT '12'::int  AS  tipo_registro
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5)
                                         OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3)
                                         OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4)
                                         OR (lote.tipo = 'T' AND transferencia.cod_tipo = 1)
                                         OR (lote.tipo = 'T' AND transferencia.cod_tipo = 0)
                                         -- OR (lote.tipo ='P' AND conta_credito.tipo_valor = 'C')
                                       THEN
                                            (   SELECT plano_recurso.cod_recurso 
                                                  FROM contabilidade.plano_conta     
                                            INNER JOIN contabilidade.plano_analitica 
                                                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                   AND plano_conta.exercicio = plano_analitica.exercicio 
                                            INNER JOIN contabilidade.plano_recurso
                                                    ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                                   AND plano_recurso.exercicio = plano_analitica.exercicio
                                                WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                                   AND plano_analitica.exercicio = transferencia.exercicio
                                            )
                                       ELSE plano_recurso.cod_recurso
                                    END::VARCHAR AS cod_fonte_caixa
                                    , transferencia.cod_tipo
                                    , transferencia.cod_plano_debito
                                , conta_credito.tipo
                                , lote.cod_lote
                                , CASE WHEN conta_credito.tipo_valor = 'D' THEN
                                         1::integer
                                         WHEN conta_credito.tipo_valor = 'C' THEN
                                         2::integer
                                  END AS tipo_movimentacao
                                , CASE WHEN lote.tipo = 'A' AND lancamento_receita.estorno != 't'
                                       THEN '01'
                                       
                                       WHEN lote.tipo ='T' AND transferencia.cod_tipo =5 AND conta_credito.tipo_valor = 'D' THEN '03'
                                       WHEN lote.tipo ='T' AND transferencia.cod_tipo =5 AND conta_credito.tipo_valor = 'C' THEN '04'
                                       
                                       WHEN lote.tipo ='P' AND conta_credito.tipo_valor = 'C'   THEN '06'

                                       WHEN lancamento_empenho.estorno = true
                                         OR (conta_credito.cod_lote = transferencia_estornada.cod_lote_estorno)
                                         OR (lancamento_receita.estorno = 't') THEN '08'
                                       WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 1 THEN '06' 
                                  END AS tipo_entr_saida
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 2 AND conta_credito.tipo_valor = 'D') THEN 'OUTROS'
                                       --WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 1 AND conta_credito.tipo_valor = 'C') THEN 'OUTROS'
                                  END AS descr_movimentacao
                                , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as valor_entr_saida
                                --, replace(plano_conta.cod_estrutural,'.','') AS cod_estrutural
                                , conta_credito.cod_entidade AS cod_orgao
                                , plano_analitica.exercicio
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                            cod_ctb_transferencia.cod_ctb_anterior::VARCHAR
                                      ELSE ' ' 
                                  END AS cod_ctb_transf   
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
                                    (   SELECT plano_recurso.cod_recurso 
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
                            FROM contabilidade.conta_credito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.cod_lote = conta_credito.cod_lote
                             AND valor_lancamento.tipo = conta_credito.tipo
                             AND valor_lancamento.sequencia = conta_credito.sequencia
                             AND valor_lancamento.exercicio = conta_credito.exercicio
                             AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                             AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.sequencia = valor_lancamento.sequencia
                             AND lancamento.cod_lote = valor_lancamento.cod_lote
                             AND lancamento.tipo = valor_lancamento.tipo
                             AND lancamento.exercicio = valor_lancamento.exercicio
                             AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                      INNER JOIN contabilidade.lote
                              ON lote.cod_lote = lancamento.cod_lote
                             AND lote.exercicio = lancamento.exercicio
                             AND lote.tipo = lancamento.tipo
                             AND lote.cod_entidade = lancamento.cod_entidade
                             AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo != 'I'
                             AND lote.cod_entidade IN (".$this->getDado('entidades').")  
                       LEFT JOIN contabilidade.lancamento_empenho
                              ON lancamento_empenho.exercicio    = lancamento.exercicio
                             AND lancamento_empenho.tipo         = lancamento.tipo
                             AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                             AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                             AND lancamento_empenho.sequencia    = lancamento.sequencia
                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = conta_credito.cod_plano
                             AND plano_analitica.exercicio = conta_credito.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio  
                      INNER JOIN contabilidade.plano_banco
                              ON plano_banco.cod_plano = conta_credito.cod_plano
                             AND plano_banco.exercicio = conta_credito.exercicio
                             AND plano_banco.cod_entidade IN  (".$this->getDado('entidades').")  
                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio
                       LEFT JOIN tesouraria.transferencia 
                              ON transferencia.exercicio = lote.exercicio
                             AND transferencia.cod_entidade = lote.cod_entidade
                             AND transferencia.tipo = lote.tipo
                             AND transferencia.cod_lote = lote.cod_lote
                       LEFT JOIN tesouraria.transferencia_estornada 
                              ON transferencia_estornada.exercicio = lote.exercicio
                             AND transferencia_estornada.cod_entidade = lote.cod_entidade
                             AND transferencia_estornada.tipo = lote.tipo
                             AND transferencia_estornada.cod_lote_estorno = lote.cod_lote  
                       LEFT JOIN contabilidade.lancamento_receita
                              ON lancamento_receita.exercicio = lancamento.exercicio
                             AND lancamento_receita.sequencia= lancamento.sequencia 
                             AND lancamento_receita.cod_lote = lancamento.cod_lote
                             AND lancamento_receita.tipo = lancamento.tipo
                             AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                       LEFT JOIN ( SELECT conta_credito.cod_lote
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
                                      AND conta_credito.tipo         = lo.tipo
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
                                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                     AND conta_credito.tipo         = 'T'
                                ) AS cod_ctb_transferencia
                              ON cod_ctb_transferencia.exercicio = conta_credito.exercicio                           
                             AND cod_ctb_transferencia.sequencia = conta_credito.sequencia
                             AND cod_ctb_transferencia.cod_lote  = conta_credito.cod_lote
                             AND cod_ctb_transferencia.tipo = conta_credito.tipo
                             AND cod_ctb_transferencia.cod_plano_credito = conta_credito.cod_plano
                       
                           WHERE conta_credito.exercicio = '".$this->getDado('exercicio')."'
                             AND conta_credito.cod_entidade IN (".$this->getDado('entidades').")  
                             AND plano_conta.cod_estrutural like  '1.1.1.1.1.01%'
                             AND lote.tipo = 'T'
                             AND transferencia.cod_tipo = 5

                        GROUP BY tipo_registro
                               , cod_fonte_caixa
                               , transferencia.cod_tipo
                               ,transferencia.cod_plano_debito
                               , conta_credito.tipo
                               , lote.cod_lote
                               , tipo_movimentacao
                               , tipo_entr_saida
                               , descr_movimentacao
                               , cod_orgao
                               , plano_analitica.exercicio
                               , cod_ctb_transf   
                               , cod_fonte_ctb_transf
                               --ORDER BY lote.cod_lote
                               
UNION
                          SELECT '12'::int  AS  tipo_registro
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 3) OR (lote.tipo = 'T' AND transferencia.cod_tipo = 4) THEN
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
                                            )
                                       ELSE plano_recurso.cod_recurso
                                    END::VARCHAR AS cod_fonte_caixa
                                
                                , transferencia.cod_tipo
                                , transferencia.cod_plano_credito
                                , conta_debito.tipo
                                , lote.cod_lote
                                
                                
                                , 2::integer AS tipo_movimentacao
                                , CASE WHEN lote.tipo = 'T' AND transferencia.cod_tipo = 5 AND conta_debito.tipo_valor = 'D'
                                       THEN '04'
                                   END AS tipo_entr_saida
                                , NULL AS descr_movimentacao
                                , COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as valor_entr_saida
                                , conta_debito.cod_entidade AS cod_orgao
                                , plano_analitica.exercicio
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) THEN
                                            cod_ctb_transferencia.cod_ctb_anterior::VARCHAR
                                      ELSE ' ' 
                                  END AS cod_ctb_transf   
                                , CASE WHEN (lote.tipo = 'T' AND transferencia.cod_tipo = 5) THEN
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
                            FROM contabilidade.conta_debito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.cod_lote = conta_debito.cod_lote
                             AND valor_lancamento.tipo = conta_debito.tipo
                             AND valor_lancamento.sequencia = conta_debito.sequencia
                             AND valor_lancamento.exercicio = conta_debito.exercicio
                             AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                             AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.sequencia = valor_lancamento.sequencia
                             AND lancamento.cod_lote = valor_lancamento.cod_lote
                             AND lancamento.tipo = valor_lancamento.tipo
                             AND lancamento.exercicio = valor_lancamento.exercicio
                             AND lancamento.cod_entidade = valor_lancamento.cod_entidade

                       LEFT JOIN contabilidade.lancamento_empenho
                              ON lancamento_empenho.exercicio    = lancamento.exercicio
                             AND lancamento_empenho.tipo         = lancamento.tipo
                             AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                             AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                             AND lancamento_empenho.sequencia    = lancamento.sequencia

                      INNER JOIN contabilidade.lote
                              ON lote.cod_lote = lancamento.cod_lote
                             AND lote.exercicio = lancamento.exercicio
                             AND lote.tipo = lancamento.tipo
                             AND lote.cod_entidade = lancamento.cod_entidade
                             AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                             AND lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo != 'I'

                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = conta_debito.cod_plano
                             AND plano_analitica.exercicio = conta_debito.exercicio

                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio  

                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio

                      INNER JOIN contabilidade.plano_banco
                              ON plano_banco.cod_plano = conta_debito.cod_plano
                             AND plano_banco.exercicio = conta_debito.exercicio
                             AND plano_banco.cod_entidade IN  (".$this->getDado('entidades').")           

                       LEFT JOIN tesouraria.transferencia 
                              ON transferencia.exercicio = lote.exercicio
                             AND transferencia.cod_entidade = lote.cod_entidade
                             AND transferencia.tipo = lote.tipo
                             AND transferencia.cod_lote = lote.cod_lote

                       LEFT JOIN tesouraria.transferencia_estornada 
                              ON transferencia_estornada.exercicio = lote.exercicio
                             AND transferencia_estornada.cod_entidade = lote.cod_entidade
                             AND transferencia_estornada.tipo = lote.tipo
                             AND transferencia_estornada.cod_lote_estorno = lote.cod_lote  
                        
                       LEFT JOIN ( SELECT conta_debito.cod_lote
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
                              ON cod_ctb_transferencia.exercicio = conta_debito.exercicio                           
                             AND cod_ctb_transferencia.sequencia = conta_debito.sequencia
                             AND cod_ctb_transferencia.cod_lote = conta_debito.cod_lote
                             AND cod_ctb_transferencia.tipo = conta_debito.tipo
                             AND cod_ctb_transferencia.cod_plano_debito = conta_debito.cod_plano
                        
                           WHERE conta_debito.exercicio =  '".$this->getDado('exercicio')."'
                             AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")  
                             AND plano_conta.cod_estrutural like  '1.1.1.1.1.01%'
                             AND lote.tipo = 'T'
                             AND transferencia.cod_tipo = 5
                             AND conta_debito.tipo_valor = 'D'

                        GROUP BY tipo_registro
                               , cod_fonte_caixa
                                , transferencia.cod_tipo
                               ,transferencia.cod_plano_credito
                               , conta_debito.tipo
                               , lote.cod_lote
                               , tipo_movimentacao
                               , tipo_entr_saida
                               , descr_movimentacao
                               , cod_orgao
                               , plano_analitica.exercicio
                               , cod_ctb_transf   

                               , cod_fonte_ctb_transf
                               
                               
                               ) AS foo
                               
               GROUP BY tipo_registro
               , cod_fonte_caixa
               , tipo 
               , tipo_movimentacao
               , tipo_entr_saida
               , descr_movimentacao
               , cod_orgao
               , exercicio
               , cod_ctb_transf   
               , cod_fonte_ctb_transf
               
                    ) as tabela
                    
            GROUP BY tipo_registro
                   , cod_reduzido
                   , cod_fonte_caixa
                   , tipo_movimentacao
                   , tipo_entrada_saida
                   , descricao_movimentacao
                   , cod_orgao
                   , exercicio
                   , cod_ctb_transfe
                   , cod_fonte_ctb_transfe
                   
        ";
        return $stSql;
    }
    
     public function recuperaCAIXA13(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCAIXA13",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCAIXA13()
    {
       
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
              SELECT c.tipo_registro
                   , exercicio||tipo_movimentacao::VARCHAR||tipo_entr_saida as cod_reduzido
                   , c.e_deducao_de_receita
                   , c.identificador_deducao
                   , c.natureza_receita
                   , c.cod_orgao 
                   , ABS(c.vlr_receita_cont) AS vlr_receita_cont
                   , c.cod_estrutural
                FROM ( SELECT '13'::int  AS  tipo_registro
                            , CASE WHEN (substr(conta_receita.cod_estrutural,1,1) = '9') THEN '1'::INTEGER
                                   ELSE '2'::INTEGER
                              END AS e_deducao_de_receita
                            , CASE WHEN receita_indentificadores_peculiar_receita.cod_identificador::TEXT is null THEN ' '
                                   ELSE  receita_indentificadores_peculiar_receita.cod_identificador::TEXT
                               END AS identificador_deducao
                            , SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8) AS natureza_receita
                            , SUM(vl.vl_lancamento) as vlr_receita_cont
                            , plano_banco.cod_entidade as cod_orgao            
                            , REPLACE(pc.cod_estrutural, '.', '') as cod_estrutural
                            , lo.tipo
                            , CASE WHEN conta_debito.tipo_valor = 'D' THEN 1::integer
                                   WHEN conta_debito.tipo_valor = 'C' THEN 2::integer
                               END AS tipo_movimentacao
                            , CASE WHEN lo.tipo = 'A' THEN '01'
                              END AS tipo_entr_saida
                            , pa.exercicio

                         FROM contabilidade.plano_conta AS pc
                   INNER JOIN contabilidade.plano_analitica AS pa
                           ON pc.cod_conta = pa.cod_conta
                          AND pc.exercicio = pa.exercicio 
                 
                   INNER JOIN contabilidade.plano_banco
                           ON plano_banco.cod_plano = pa.cod_plano
                          AND plano_banco.exercicio = pa.exercicio 
                
                   INNER JOIN monetario.agencia
                           ON agencia.cod_banco   = plano_banco.cod_banco
                          AND agencia.cod_agencia = plano_banco.cod_agencia
                     
                   INNER JOIN monetario.banco
                           ON banco.cod_banco = plano_banco.cod_banco
                           
                    LEFT JOIN tcemg.conta_bancaria
                   ON conta_bancaria.cod_conta = pc.cod_conta
                      AND conta_bancaria.exercicio = pc.exercicio

                   INNER JOIN contabilidade.conta_debito
                           ON pa.cod_plano = conta_debito.cod_plano
                          AND pa.exercicio = conta_debito.exercicio
                   
                   INNER JOIN contabilidade.valor_lancamento AS vl
                           ON conta_debito.cod_lote     = vl.cod_lote
                          AND conta_debito.tipo         = vl.tipo
                          AND conta_debito.sequencia    = vl.sequencia
                          AND conta_debito.exercicio    = vl.exercicio
                          AND conta_debito.tipo_valor   = vl.tipo_valor
                          AND conta_debito.cod_entidade = vl.cod_entidade
                   
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
                          AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_conta 
                   
                        WHERE pc.exercicio = '".$this->getDado('exercicio')."'
                          AND plano_banco.cod_entidade IN (".$this->getDado('entidades').") 
                          AND lo.dt_lote BETWEEN TO_DATE ('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                          AND pc.cod_estrutural like  '1.1.1.1.1.01%'

                     GROUP BY 2,3,4,6,7,8,9,11
                    ) AS c
              
             GROUP BY c.tipo_registro
                    , c.cod_estrutural
            , c.e_deducao_de_receita
            , c.identificador_deducao
            , c.natureza_receita
            , c.cod_orgao 
            , c.tipo
            , c.vlr_receita_cont
            , tipo_movimentacao
            , tipo_entr_saida
            , exercicio 
        
             ORDER BY cod_reduzido";
     return $stSql;
    }
    public function __destruct(){}

}

?>