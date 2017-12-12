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
    * $Id: TTCEMGCTB.class.php 63927 2015-11-09 16:25:18Z lisiane $

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
                       , arquivo_ctb.ano
                       , arquivo_ctb.mes 
                       , retorno.cod_ctb
                       , retorno.tipo_conta||regexp_replace((retorno.conta), '[-|,|.|x]', '', 'gi') AS cod_ctb_view 
                       , retorno.cod_orgao
                       , retorno.banco
                       , retorno.agencia 
                       , retorno.digito_verificador_agencia
                       , retorno.digito_verificador_conta_bancaria
                       , retorno.conta_bancaria
                       , retorno.conta_corrente
                       , retorno.tipo_conta
                       , retorno.tipo_aplicacao
                       , ''::VARCHAR nro_seq_aplicacao
                       , retorno.desc_conta_bancaria
                       , plano_banco.cod_plano
                       , CASE WHEN (convenio_plano_banco.num_convenio <> NULL)
                              THEN 1
                              ELSE 2
                          END AS conta_convenio
                       , convenio_plano_banco.num_convenio
                       , convenio_plano_banco.dt_assinatura
                    FROM contabilidade.plano_banco
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = plano_banco.cod_plano
                     AND plano_analitica.exercicio = plano_banco.exercicio
              INNER JOIN contabilidade.plano_conta
                      ON plano_analitica.cod_conta = plano_conta.cod_conta
                     AND plano_analitica.exercicio = plano_conta.exercicio
               LEFT JOIN tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as retorno
                                                        (  cod_conta                         INTEGER
                                                        ,  tipo_aplicacao                    VARCHAR
                                                        ,  cod_ctb                           INTEGER
                                                        ,  tipo_conta                        INTEGER
                                                        ,  exercicio                         CHAR(4)
                                                        ,  conta                             TEXT
                                                        ,  conta_bancaria                    TEXT
                                                        ,  conta_corrente                    TEXT
                                                        ,  cod_orgao                         INTEGER
                                                        ,  banco                             VARCHAR
                                                        ,  agencia                           TEXT
                                                        ,  digito_verificador_agencia        TEXT
                                                        ,  digito_verificador_conta_bancaria TEXT
                                                        ,  desc_conta_bancaria               VARCHAR
                                                        )
                                                        
                      ON retorno.cod_conta = plano_analitica.cod_conta
                     AND retorno.exercicio = plano_analitica.exercicio

               LEFT JOIN tesouraria.fn_relatorio_demostrativo_saldos ( '".$this->getDado('exercicio')."'
                                                                     , '".$this->getDado('entidades')."'
                                                                     , '".$this->getDado('dtInicio')."'
                                                                     , '".$this->getDado('dtFim')."'
                                                                     , ''
                                                                     , ''
                                                                     , ''
                                                                     , ''
                                                                     , ''
                                                                     , 'S'
                                                                     , ''
                                                                     , ''
                                                                     , 'true'   
                                                                     )
                                                                  AS saldo_inicial
                                                                     ( exercicio         VARCHAR
                                                                     ,cod_estrutural     VARCHAR
                                                                     ,cod_plano          INTEGER
                                                                     ,nom_conta          VARCHAR
                                                                     ,saldo_anterior     NUMERIC
                                                                     ,vl_credito         NUMERIC
                                                                     ,vl_debito          NUMERIC
                                                                     ,cod_recurso        INTEGER
                                                                     ,nom_recurso        VARCHAR
                                                                     )
                      ON saldo_inicial.cod_plano = plano_analitica.cod_plano
                     AND saldo_inicial.exercicio = plano_analitica.exercicio

               LEFT JOIN tcemg.convenio_plano_banco 
                      ON convenio_plano_banco.cod_plano = plano_analitica.cod_plano 
                     AND convenio_plano_banco.exercicio = plano_analitica.exercicio

               LEFT JOIN tcemg.arquivo_ctb
                    --  ON arquivo_ctb.cod_ctb = retorno.cod_ctb
                    ON arquivo_ctb.cod_ctb_view = retorno.tipo_conta||regexp_replace((retorno.conta), '[-|,|.|x]', '', 'gi')
                    
                   WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                     AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                     AND plano_banco.cod_plano NOT IN (3274)
                     AND ((ano IS NULL AND mes IS NULL)
                      OR (ano = '".$this->getDado('exercicio')."' AND mes = ".$inMes."))
                     AND ( saldo_anterior = 0.00 AND vl_credito = 0.00 AND vl_debito = 0.00) IS FALSE
                     AND saldo_inicial.cod_plano IS NOT NULL

               GROUP BY tipo_registro
                      , arquivo_ctb.ano
                      , arquivo_ctb.mes 
                      , retorno.cod_ctb
                      , cod_ctb_view 
                      , retorno.cod_orgao
                      , retorno.banco
                      , retorno.agencia 
                      , retorno.digito_verificador_agencia
                      , retorno.digito_verificador_conta_bancaria
                      , retorno.conta_bancaria
                      , retorno.conta_corrente
                      , retorno.tipo_conta
                      , retorno.tipo_aplicacao
                      , nro_seq_aplicacao
                      , retorno.desc_conta_bancaria
                      , conta_convenio
                      , retorno.conta
                      , convenio_plano_banco.num_convenio
                      , convenio_plano_banco.dt_assinatura
                      , plano_banco.cod_plano
              ORDER BY retorno.cod_ctb 
        ";
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
                        , c.cod_orgao
                        , c.codigo_ctb AS cod_ctb
                        , c.cod_recurso as cod_fonte_recursos
                        , c.tipo_conta
                        , SUM(c.vl_saldo_inicial_fonte) as vl_saldo_inicial_fonte 
                        , SUM(c.vl_saldo_final_fonte) as vl_saldo_final_fonte
                    FROM (
                          SELECT '20'::VARCHAR AS tipo_registro
                               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao

                               , CASE WHEN contasCTB.cod_ctb IS NOT NULL
                                      THEN contasCTB.cod_ctb
                                      ELSE conta_bancaria.cod_ctb_anterior
                                  END AS codigo_ctb

                               , plano_recurso.cod_recurso
                               , contasCTB.tipo_conta
                               , saldo_inicial.saldo_anterior AS vl_saldo_inicial_fonte
                               , ((saldo_inicial.vl_debito - saldo_inicial.vl_credito) + saldo_inicial.saldo_anterior) AS vl_saldo_final_fonte
                            FROM contabilidade.plano_banco
                      INNER JOIN contabilidade.plano_analitica
                              ON plano_analitica.cod_plano = plano_banco.cod_plano
                             AND plano_analitica.exercicio = plano_banco.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio
                      INNER JOIN contabilidade.plano_recurso
                              ON plano_analitica.cod_plano = plano_recurso.cod_plano
                             AND plano_analitica.exercicio = plano_recurso.exercicio
                        
                       LEFT JOIN tcemg.conta_bancaria
                              ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                             AND conta_bancaria.exercicio = plano_analitica.exercicio
                       LEFT JOIN tesouraria.fn_relatorio_demostrativo_saldos ( '".$this->getDado('exercicio')."'
                                                                             , '".$this->getDado('entidades')."'
                                                                             , '".$this->getDado('dtInicio')."'
                                                                             , '".$this->getDado('dtFim')."'
                                                                             , ''
                                                                             , ''
                                                                             , ''
                                                                             , ''
                                                                             , ''
                                                                             , 'S'
                                                                             , ''
                                                                             , ''
                                                                             , 'true'   
                                                                             )
                                                                          AS saldo_inicial
                                                                             ( exercicio         VARCHAR
                                                                             ,cod_estrutural     VARCHAR
                                                                             ,cod_plano          INTEGER
                                                                             ,nom_conta          VARCHAR
                                                                             ,saldo_anterior     NUMERIC
                                                                             ,vl_credito         NUMERIC
                                                                             ,vl_debito          NUMERIC
                                                                             ,cod_recurso        INTEGER
                                                                             ,nom_recurso        VARCHAR
                                                                             )
                              ON saldo_inicial.cod_plano = plano_analitica.cod_plano
                             AND saldo_inicial.exercicio = plano_analitica.exercicio
                             
                       LEFT JOIN (
                                  SELECT cod_conta
                                       , cod_ctb
                                       , exercicio
                                       , tipo_conta
                                       , tipo_aplicacao
                                    FROM tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as conta_bancaria
                                                    (
                                                        cod_conta                         INTEGER
                                                      , tipo_aplicacao                    VARCHAR
                                                      , cod_ctb                           INTEGER
                                                      , tipo_conta                        INTEGER
                                                      , exercicio                         CHAR(4)  
                                                      , conta                             TEXT                         
                                                      , conta_bancaria                    TEXT  
                                                      , conta_corrente                    TEXT                                                         
                                                      , cod_orgao                         INTEGER                                                        
                                                      , banco                             VARCHAR                                                        
                                                      , agencia                           TEXT                                                        
                                                      , digito_verificador_agencia        TEXT                                                        
                                                      , digito_verificador_conta_bancaria TEXT                                                        
                                                      , desc_conta_bancaria               VARCHAR
                                                    )
                                GROUP BY cod_conta
                                       , cod_ctb
                                       , exercicio
                                       , tipo_conta
                                       , tipo_aplicacao
                                 ) AS contasCTB
                              ON contasCTB.cod_conta = plano_analitica.cod_conta
                             AND contasCTB.exercicio = plano_analitica.exercicio
                             
                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = plano_banco.cod_entidade
                             AND configuracao_entidade.exercicio = plano_banco.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                           WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                             AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                             --AND ( saldo_anterior = 0.00 AND vl_credito = 0.00 AND vl_debito = 0.00) IS FALSE
                             --AND saldo_inicial.cod_plano IS NOT NULL
                             --AND (saldo_anterior =0.00 AND vl_credito = 0.00 AND vl_debito = 0.00) = FALSE
                             AND contasCTB.cod_ctb IN (SELECT arquivo_ctb.cod_ctb FROM tcemg.arquivo_ctb WHERE arquivo_ctb.cod_ctb = contasCTB.cod_ctb GROUP BY arquivo_ctb.cod_ctb)
                         ORDER BY contasCTB.cod_ctb
                        ) AS c
                 GROUP BY c.tipo_registro
                       , c.cod_orgao
                       , cod_ctb
                       , cod_fonte_recursos
                       , c.tipo_conta
                 ORDER BY cod_ctb
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

             SELECT tipo_registro
                   , cod_ctb
                   , cod_fonte_recursos
                   , cod_reduzido_mov
                   , tipo_movimentacao
                   , CASE WHEN tipo_entrada_saida = '06'
                            OR tipo_entrada_saida = '05'
                            OR tipo_entrada_saida = '07'
                            OR tipo_entrada_saida = '09'
                          THEN CASE WHEN cod_fonte_ctb_transfe::INTEGER <> cod_fonte_recursos
                                    THEN '99'
                                    ELSE tipo_entrada_saida
                                END
                          ELSE tipo_entrada_saida
                      END AS tipo_entr_saida
                   
                   , CASE WHEN tipo_entrada_saida = '06'
                            OR tipo_entrada_saida = '05'
                            OR tipo_entrada_saida = '07'
                            OR tipo_entrada_saida = '09'
                          THEN CASE WHEN cod_fonte_ctb_transfe::INTEGER <> cod_fonte_recursos
                                    THEN NULL
                                    ELSE cod_ctb_transfe
                                END
                          ELSE cod_ctb_transfe
                      END AS cod_ctb_transf
                   
                   , CASE WHEN tipo_entrada_saida = '06'
                            OR tipo_entrada_saida = '05'
                            OR tipo_entrada_saida = '07'
                            OR tipo_entrada_saida = '09'
                          THEN CASE WHEN cod_fonte_ctb_transfe::INTEGER <> cod_fonte_recursos
                                    THEN NULL
                                    ELSE cod_fonte_ctb_transfe
                                END
                          WHEN tipo_entrada_saida = '18'
                          THEN cod_fonte_recursos::VARCHAR
                          WHEN tipo_entrada_saida = '99'
                          THEN NULL
                          ELSE cod_fonte_ctb_transfe
                      END AS cod_fonte_ctb_transf
                   , SUM(foo.valor_entr_saida) AS valor_entr_saida
  FROM (

              SELECT a.tipo_registro
                   , a.codigo_ctb AS cod_ctb
                   , a.cod_fonte_recursos
                   , '' AS cod_reduzido_mov
                   , a.tipo_movimentacao
                   , a.tipo_entr_saida AS tipo_entrada_saida
                   , a.cod_ctb_transf AS cod_ctb_transfe
                   , a.cod_fonte_ctb_transf AS cod_fonte_ctb_transfe
                   , SUM(a.valor_entr_saida) AS valor_entr_saida
                FROM (
                      SELECT '21'::int  AS  tipo_registro
                           , SUM(vl.vl_lancamento ) AS valor_entr_saida
                           , plano_banco.cod_entidade as cod_orgao
                           , pa.cod_plano
                           , transferencia.cod_plano_credito

                           , CASE WHEN conta_bancaria.cod_ctb IS NOT NULL
                                  THEN conta_bancaria.cod_ctb
                                  ELSE codigo_bancario.cod_ctb_anterior
                              END AS codigo_ctb
                           , conta_bancaria.tipo_aplicacao
                           , conta_bancaria.tipo_conta::VARCHAR
                           , pa.exercicio 
                           , plano_recurso.cod_recurso AS cod_fonte_recursos
                           , '1'::VARCHAR AS tipo_movimentacao 
                           , CASE WHEN lo.tipo = 'A'
                                   AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9'
                                   AND lancamento_receita.estorno = false
                                   AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'
                                  THEN '01'

                                  WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) = '1325'
                                  THEN '04'
                              
                                  WHEN lo.tipo = 'T'
                                   AND transferencia.cod_tipo = 5
                                  THEN CASE WHEN SUBSTR(cod_ctb_transferencia.estrutural_ctb_transfer, 1, 7) = '1111101'
                                            THEN '18'
                                            ELSE '05'
                                        END

                                  WHEN lo.tipo = 'T'
                                   AND transferencia.cod_tipo = 4
                                  THEN '07'

                                  WHEN lo.tipo = 'T'
                                   AND transferencia.cod_tipo = 3
                                  THEN '09'
                                  WHEN lo.tipo = 'P'
                                   AND lancamento_receita.estorno = true
                                  THEN '10'

                                  WHEN lo.tipo = 'T'
                                   AND transferencia.cod_tipo = 2
                                   AND (
                                        SELECT CASE WHEN COUNT(plano_conta.*) > 0
                                                    THEN TRUE
                                                    ELSE FALSE
                                                END
                                          FROM contabilidade.plano_conta     
                                    INNER JOIN contabilidade.plano_analitica 
                                            ON plano_conta.cod_conta = plano_analitica.cod_conta
                                           AND plano_conta.exercicio = plano_analitica.exercicio 
                                         WHERE plano_analitica.cod_plano = transferencia.cod_plano_credito
                                           AND plano_analitica.exercicio = transferencia.exercicio
                                           AND (SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451100000'
                                             OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220101'
                                             OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220102'
                                             OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220103'
                                             OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220104'
                                             OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451220199'
                                             OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '451300000'
                                               ) 
                                       )
                                      THEN '12'
                                  WHEN lo.tipo = 'A'
                                   AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9' 
                                  THEN '16'

                                  WHEN lo.tipo = 'P'
                                   AND timestamp_anulada IS NOT NULL
                                  THEN '17'
                                  ELSE '99'
                              END AS tipo_entr_saida 
                           , CASE WHEN ((lo.tipo = 'T' AND transferencia.cod_tipo = 5)
                                     OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3)
                                     OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4)
                                    )
                                   AND SUBSTR(cod_ctb_transferencia.estrutural_ctb_transfer, 1, 7) <> '1111101'
                                  THEN ( cod_ctb_transferencia.cod_ctb_anterior )
                              END::VARCHAR AS cod_ctb_transf         

                           , CASE WHEN ((lo.tipo = 'T' AND transferencia.cod_tipo = 5)
                                     OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3)
                                     OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4)
                                       )
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
                                        )
                                   ELSE (
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
                                        )
                                END::VARCHAR AS cod_fonte_ctb_transf
                        FROM contabilidade.plano_conta AS pc   
                  INNER JOIN contabilidade.plano_analitica AS pa
                          ON pc.cod_conta = pa.cod_conta
                         AND pc.exercicio = pa.exercicio 
                  INNER JOIN contabilidade.plano_banco
                          ON plano_banco.cod_plano = pa.cod_plano
                         AND plano_banco.exercicio = pa.exercicio 
                  INNER JOIN contabilidade.plano_recurso
                          ON pa.cod_plano = plano_recurso.cod_plano
                         AND pa.exercicio = plano_recurso.exercicio

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
  
                  INNER JOIN contabilidade.lote AS lo
                          ON vl.cod_lote     = lo.cod_lote
                         AND vl.tipo         = lo.tipo
                         AND vl.exercicio    = lo.exercicio
                         AND vl.cod_entidade = lo.cod_entidade
                         AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                         AND lo.exercicio = '".$this->getDado('exercicio')."'

                   LEFT JOIN tcemg.conta_bancaria AS codigo_bancario
                          ON codigo_bancario.cod_conta = pa.cod_conta
                         AND codigo_bancario.exercicio = pa.exercicio

                   LEFT JOIN tesouraria.transferencia
                          ON transferencia.cod_lote     = lo.cod_lote
                         AND transferencia.tipo         = lo.tipo
                         AND transferencia.exercicio    = lo.exercicio
                         AND transferencia.cod_entidade = lo.cod_entidade
                   LEFT JOIN tesouraria.transferencia_estornada
                          ON transferencia_estornada.cod_lote_estorno     = lo.cod_lote
                         AND transferencia_estornada.tipo         = lo.tipo
                         AND transferencia_estornada.exercicio    = lo.exercicio
                         AND transferencia_estornada.cod_entidade = lo.cod_entidade
                         
                         AND transferencia_estornada.cod_lote     = transferencia.cod_lote
                         AND transferencia_estornada.tipo         = transferencia.tipo
                         AND transferencia_estornada.exercicio    = transferencia.exercicio
                         AND transferencia_estornada.cod_entidade = transferencia.cod_entidade

                   LEFT JOIN (SELECT cod_conta
                                   , cod_ctb
                                   , exercicio
                                   , tipo_conta
                                   , tipo_aplicacao
                                FROM tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as contas_bancaria
                                                (
                                                    cod_conta                         INTEGER
                                                  , tipo_aplicacao                    VARCHAR
                                                  , cod_ctb                           INTEGER
                                                  , tipo_conta                        INTEGER
                                                  , exercicio                         CHAR(4)  
                                                  , conta                             TEXT                         
                                                  , conta_bancaria                    TEXT  
                                                  , conta_corrente                    TEXT                                                         
                                                  , cod_orgao                         INTEGER                                                        
                                                  , banco                             VARCHAR                                                        
                                                  , agencia                           TEXT                                                        
                                                  , digito_verificador_agencia        TEXT                                                        
                                                  , digito_verificador_conta_bancaria TEXT                                                        
                                                  , desc_conta_bancaria               VARCHAR
                                                )
                            GROUP BY cod_conta
                                   , cod_ctb
                                   , exercicio
                                   , tipo_conta
                                   , tipo_aplicacao
                             ) AS conta_bancaria
                          ON conta_bancaria.cod_conta = pc.cod_conta
                         AND conta_bancaria.exercicio = pc.exercicio

                   LEFT JOIN contabilidade.lancamento_receita
                          ON lancamento_receita.exercicio    = lancamento.exercicio
                         AND lancamento_receita.cod_entidade = lancamento.cod_entidade
                         AND lancamento_receita.tipo         = lancamento.tipo
                         AND lancamento_receita.cod_lote     = lancamento.cod_lote
                         AND lancamento_receita.sequencia    = lancamento.sequencia
                   LEFT JOIN orcamento.receita
                          ON receita.cod_receita = lancamento_receita.cod_receita
                         AND receita.exercicio   = lancamento_receita.exercicio
                   LEFT JOIN orcamento.recurso
                          ON recurso.exercicio   = plano_recurso.exercicio
                         AND recurso.cod_recurso = plano_recurso.cod_recurso
                   LEFT JOIN contabilidade.lancamento_empenho
                          ON lancamento_empenho.exercicio    = lancamento.exercicio
                         AND lancamento_empenho.tipo         = lancamento.tipo
                         AND lancamento_empenho.cod_entidade = lancamento.cod_entidade
                         AND lancamento_empenho.cod_lote     = lancamento.cod_lote
                         AND lancamento_empenho.sequencia    = lancamento.sequencia

                   LEFT JOIN contabilidade.pagamento
                          ON lancamento_empenho.exercicio    = pagamento.exercicio
                         AND lancamento_empenho.tipo         = pagamento.tipo
                         AND lancamento_empenho.cod_entidade = pagamento.cod_entidade
                         AND lancamento_empenho.cod_lote     = pagamento.cod_lote
                         AND lancamento_empenho.sequencia    = pagamento.sequencia

                   LEFT JOIN contabilidade.pagamento_estorno
                          ON pagamento_estorno.exercicio    = pagamento.exercicio
                         AND pagamento_estorno.tipo         = pagamento.tipo
                         AND pagamento_estorno.cod_entidade = pagamento.cod_entidade
                         AND pagamento_estorno.cod_lote     = pagamento.cod_lote
                         AND pagamento_estorno.sequencia    = pagamento.sequencia


                   LEFT JOIN orcamento.conta_receita
                          ON conta_receita.exercicio = receita.exercicio
                         AND conta_receita.cod_conta = receita.cod_conta 
                   LEFT JOIN tcemg.receita_indentificadores_peculiar_receita
                          ON receita_indentificadores_peculiar_receita.exercicio   = receita.exercicio
                         AND receita_indentificadores_peculiar_receita.cod_receita = receita.cod_conta 

                   LEFT JOIN (
                            SELECT conta_debito.cod_lote
                                , conta_debito.tipo
                                , conta_debito.exercicio
                                , conta_debito.cod_entidade
                                , transferencia.cod_plano_debito
                                , transferencia.cod_plano_credito
                                , CASE WHEN (conta_bancaria.cod_ctb_anterior is null)
                                       THEN transferencia.cod_plano_credito
                                       Else conta_bancaria.cod_ctb_anterior
                                   END AS cod_ctb_anterior
                                , conta_debito.sequencia
                                , REPLACE(pc.cod_estrutural,'.', '') AS estrutural_ctb_transfer
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
                       INNER JOIN contabilidade.plano_conta AS pc
                               ON pc.cod_conta = plano_analitica.cod_conta
                              AND pc.exercicio = plano_analitica.exercicio
                            WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
                              AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")
                              AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                              AND conta_debito.tipo         = 'T'
                        ORDER BY cod_ctb_anterior
                               , conta_debito.cod_lote
                            ) AS cod_ctb_transferencia
                          ON cod_ctb_transferencia.exercicio = cd.exercicio
                         AND cod_ctb_transferencia.sequencia = cd.sequencia
                         AND cod_ctb_transferencia.cod_lote = cd.cod_lote
                         AND cod_ctb_transferencia.tipo = cd.tipo
                         AND cod_ctb_transferencia.cod_plano_debito = cd.cod_plano

                       WHERE pc.exercicio   = '".$this->getDado('exercicio')."'
                         AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                         AND vl.tipo <> 'I'                       
                    GROUP BY tipo_registro
                           , plano_banco.cod_entidade
                           , pa.cod_plano
                           , tipo_movimentacao
                           , transferencia.cod_plano_credito
                           , codigo_ctb
                           , conta_bancaria.cod_ctb
                           , cod_fonte_recursos
                           , conta_bancaria.tipo_aplicacao
                           , tipo_conta
                           , pa.exercicio
                           , tipo_entr_saida
                           , cod_ctb_transf
                           , cod_fonte_ctb_transf
                        ) AS a
            GROUP BY a.tipo_registro
                   , cod_ctb
                   , a.cod_fonte_recursos
                   , cod_reduzido_mov
                   , a.tipo_movimentacao
                   , tipo_entrada_saida
                   , cod_ctb_transfe
                   , cod_fonte_ctb_transfe



                    UNION
                   SELECT b.tipo_registro
                        , b.codigo_ctb AS cod_ctb
                        , b.cod_fonte_recursos
                        , '' AS cod_reduzido_mov
                        , b.tipo_movimentacao
                        , b.tipo_entr_saida AS tipo_entrada_saida
                        , b.cod_ctb_transf AS cod_ctb_transfe
                        , b.cod_fonte_ctb_transf AS cod_fonte_ctb_transfe
                        , SUM(b.valor_entr_saida) AS valor_entr_saida
                    FROM (
                        SELECT '21'::int  AS  tipo_registro
                            , SUM(vl.vl_lancamento) * -1 as valor_entr_saida

                           , pa.cod_plano
                           , transferencia.cod_plano_debito

                            , plano_banco.cod_entidade as cod_orgao
                           , CASE WHEN conta_bancaria.cod_ctb IS NOT NULL
                                  THEN conta_bancaria.cod_ctb
                                  ELSE codigo_bancario.cod_ctb_anterior
                              END AS codigo_ctb
                            , conta_bancaria.tipo_aplicacao
                            , conta_bancaria.tipo_conta::VARCHAR
                            , pa.exercicio
                            , plano_recurso.cod_recurso as cod_fonte_recursos
                            , '2'::VARCHAR AS tipo_movimentacao
                            , CASE 
                                WHEN lo.tipo = 'A'
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9'
                                    AND lancamento_receita.estorno = true
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'
                                    --AND RDE.vl_estornado IS NOT NULL
                                    AND ( (ARR.vl_arrecadacao IS NULL) OR (AR.devolucao = true) )
                                    THEN '02'
                                WHEN lo.tipo = 'A'
                                    AND lancamento_receita.estorno = true
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9'
                                    THEN '03'
                                WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) = '1325'
                                    THEN '04'
                                WHEN lo.tipo = 'T'
                                    AND transferencia.cod_tipo = 5
                                    AND SUBSTR(cod_ctb_transferencia.estrutural_ctb_transfer, 1, 7) <> '1111101'
                                    THEN '06'
                                WHEN lo.tipo = 'T'
                                    AND transferencia.cod_tipo = 4
                                    THEN '07'
                                WHEN lo.tipo = 'P'
                                    AND lancamento_empenho.estorno = false
                                    THEN '08'
                                WHEN lo.tipo = 'T'
                                    AND transferencia.cod_tipo = 3
                                    THEN '09'
                                WHEN lo.tipo = 'P'
                                    AND lancamento_empenho.estorno = true
                                    THEN '10'
                                WHEN lo.tipo = 'T'
                                    AND transferencia.cod_tipo = 5
                                    AND SUBSTR(cod_ctb_transferencia.estrutural_ctb_transfer, 1, 7) = '1111101'
                                    THEN '11'
                                WHEN lo.tipo = 'T'
                                    AND transferencia.cod_tipo = 1
                                    AND (
                                        SELECT CASE 
                                                WHEN COUNT(plano_conta.*) > 0
                                                    THEN TRUE
                                                ELSE FALSE
                                                END
                                        FROM contabilidade.plano_conta
                                        INNER JOIN contabilidade.plano_analitica
                                            ON plano_conta.cod_conta = plano_analitica.cod_conta
                                                AND plano_conta.exercicio = plano_analitica.exercicio
                                        WHERE plano_analitica.cod_plano = transferencia.cod_plano_debito
                                            AND plano_analitica.exercicio = transferencia.exercicio
                                            AND (
                                                SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351100000'
                                                OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220101'
                                                OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220102'
                                                OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220103'
                                                OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220104'
                                                OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351220199'
                                                OR SUBSTR(REPLACE(plano_conta.cod_estrutural, '.', ''), 1, 9) = '351300000'
                                                )
                                        )
                                    THEN '13'
                                WHEN lo.tipo = 'A'
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9'
                                    AND ( 
                                        (RDE.vl_estornado IS NOT NULL)
                                        OR ( ARR.vl_arrecadacao IS NOT NULL AND AR.devolucao = false )
                                        )
                                    THEN '16'
                                ELSE '99'
                                END AS tipo_entr_saida
                            , CASE 
                                WHEN (
                                        ( lo.tipo = 'T' AND transferencia.cod_tipo = 5 )
                                        OR ( lo.tipo = 'T' AND transferencia.cod_tipo = 4 )
                                        OR ( lo.tipo = 'T' AND transferencia.cod_tipo = 3 )
                                    )
                                    AND SUBSTR(cod_ctb_transferencia.estrutural_ctb_transfer, 1, 7) <> '1111101'
                                    THEN (cod_ctb_transferencia.cod_ctb_anterior)
                                END::VARCHAR AS cod_ctb_transf
                            , CASE
                                WHEN (
                                        ( lo.tipo = 'T' AND transferencia.cod_tipo = 5)
                                        OR (lo.tipo = 'T' AND transferencia.cod_tipo = 3)
                                        OR (lo.tipo = 'T' AND transferencia.cod_tipo = 4)
                                    )
                                    AND SUBSTR(cod_ctb_transferencia.estrutural_ctb_transfer, 1, 7) <> '1111101'
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
                                        )
                                    ELSE (
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
                                        )

                                END::VARCHAR AS cod_fonte_ctb_transf 
                        FROM contabilidade.plano_conta AS pc
                  INNER JOIN contabilidade.plano_analitica AS pa
                          ON pc.cod_conta = pa.cod_conta
                         AND pc.exercicio = pa.exercicio 
                  INNER JOIN contabilidade.plano_banco
                          ON plano_banco.cod_plano = pa.cod_plano
                         AND plano_banco.exercicio = pa.exercicio
                         
                   LEFT JOIN tcemg.conta_bancaria AS codigo_bancario
                          ON codigo_bancario.cod_conta = pa.cod_conta
                         AND codigo_bancario.exercicio = pa.exercicio
                         
                   LEFT JOIN tcemg.contasCTB('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."') as conta_bancaria
                                            (
                                                cod_conta                         INTEGER
                                              , tipo_aplicacao                    VARCHAR
                                              , cod_ctb                           INTEGER
                                              , tipo_conta                        INTEGER
                                              , exercicio                         CHAR(4)  
                                              , conta                             TEXT                         
                                              , conta_bancaria                    TEXT  
                                              , conta_corrente                    TEXT                                                         
                                              , cod_orgao                         INTEGER                                                        
                                              , banco                             VARCHAR                                                        
                                              , agencia                           TEXT                                                        
                                              , digito_verificador_agencia        TEXT                                                        
                                              , digito_verificador_conta_bancaria TEXT                                                        
                                              , desc_conta_bancaria               VARCHAR
                                            )
                          ON conta_bancaria.cod_conta = pc.cod_conta
                         AND conta_bancaria.exercicio = pc.exercicio
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
                                     , CASE
                                        WHEN (conta_bancaria.cod_ctb_anterior is null)
                                            THEN transferencia.cod_plano_debito
                                        Else conta_bancaria.cod_ctb_anterior
                                        END AS cod_ctb_anterior
                                     , transferencia.cod_plano_credito
                                     , transferencia.cod_plano_debito
                                     , conta_credito.sequencia
                                     , REPLACE(pc.cod_estrutural,'.', '') AS estrutural_ctb_transfer
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
                                   AND plano_analitica.exercicio = conta_credito.exercicio
                             LEFT JOIN tcemg.conta_bancaria
                                    ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                                   AND conta_bancaria.exercicio = plano_analitica.exercicio
                            INNER JOIN contabilidade.plano_conta AS pc
                                    ON pc.cod_conta = plano_analitica.cod_conta
                                   AND pc.exercicio = plano_analitica.exercicio
                            WHERE conta_credito.exercicio = '".$this->getDado('exercicio')."'
                              AND conta_credito.cod_entidade IN (".$this->getDado('entidades').")
                              AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                   AND conta_credito.tipo         = 'T'
                            ) AS cod_ctb_transferencia
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
                         AND vl.tipo <> 'I'
                    GROUP BY tipo_registro
                           , plano_banco.cod_entidade
                           , pa.cod_plano
                           , transferencia.cod_plano_debito
                           , codigo_ctb
                           , conta_bancaria.tipo_aplicacao
                           , tipo_conta
                           , cod_fonte_recursos
                           , pa.exercicio
                           , tipo_entr_saida
                           , cod_ctb_transf
                           , cod_fonte_ctb_transf
                        ) AS b
                 GROUP BY b.tipo_registro
                        , cod_ctb
                        , b.cod_fonte_recursos
                        , cod_reduzido_mov
                        , b.tipo_movimentacao
                        , tipo_entrada_saida
                        , cod_ctb_transfe
                        , cod_fonte_ctb_transfe
                      ) AS foo
                      
               GROUP BY tipo_registro
                   , cod_ctb
                   , cod_fonte_recursos
                   , cod_reduzido_mov
                   , tipo_movimentacao
                   , tipo_entr_saida
                   , cod_ctb_transf
                   , cod_fonte_ctb_transf
        ORDER BY cod_ctb
                   
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
        $stSql = "
        
                   SELECT c.tipo_registro
                , '' AS cod_reduzido_mov
                , c.e_deducao_de_receita
                , c.identificador_deducao
                , c.natureza_receita
                , c.codigo_ctb AS cod_ctb
                , tipo_entr_saida
                , c.tipo_movimentacao
                , c.cod_fonte_recursos
                , ABS(SUM(c.vlr_receita_cont)) AS vlr_receita_cont
           FROM (
                  SELECT '22'::int  AS  tipo_registro
                       , CASE WHEN (substr(conta_receita.cod_estrutural,1,1) = '9') THEN
                          '1'::INTEGER
                         ELSE 
                          '2'::INTEGER
                         END AS e_deducao_de_receita
                       , receita_indentificadores_peculiar_receita.cod_identificador as identificador_deducao
                       , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                           THEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 2, 8)::integer
                           ELSE CASE WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6)::INTEGER = 172401
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6)::INTEGER = 172199
                                       OR SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6)::INTEGER = 193199
                                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 6), 8, '0')::INTEGER
                                    
                                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 8)::INTEGER
                                 END
                           END AS natureza_receita
                       , SUM(vl.vl_lancamento) as vlr_receita_cont
                       , CASE WHEN conta_bancaria.cod_ctb IS NOT NULL
                              THEN conta_bancaria.cod_ctb
                              ELSE pa.cod_plano
                          END AS codigo_ctb
                       , CASE WHEN lo.tipo = 'A'
                                   AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9'
                                   AND lancamento_receita.estorno = false
                                   AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'
                                  THEN '01'
                             WHEN lo.tipo = 'A'
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9'
                                    AND lancamento_receita.estorno = true
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 4) <> '1325'
                                    THEN '02'
                                WHEN lo.tipo = 'A'
                                    AND lancamento_receita.estorno = true
                                    AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) <> '9'
                                    THEN '03'
                                  WHEN lo.tipo = 'A'
                                   AND SUBSTR(REPLACE(conta_receita.cod_estrutural, '.', ''), 1, 1) = '9' 
                                  THEN '16'

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
                                            , agencia                     TEXT                                                        
                                            , digito_verificador_agencia        TEXT                                                        
                                            , digito_verificador_conta_bancaria TEXT                                                        
                                            , desc_conta_bancaria               VARCHAR
                                           )
                      ON conta_bancaria.cod_conta = pc.cod_conta
                     AND conta_bancaria.exercicio = pc.exercicio
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
                     AND vl.tipo = lo.tipo
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

                   WHERE pc.exercicio = '".$this->getDado('exercicio')."'
                     AND plano_banco.cod_entidade IN (".$this->getDado('entidades').")
                     AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                
                
                GROUP BY tipo_registro
                       , e_deducao_de_receita
                       , identificador_deducao
                       , natureza_receita
                       , codigo_ctb
                       , tipo_entr_saida
                       , tipo_movimentacao
                       , cod_fonte_recursos
              ) AS c
          WHERE tipo_entr_saida != '99'
            AND codigo_ctb NOT IN (3274)

       GROUP BY c.tipo_registro
                , cod_reduzido_mov
                , c.e_deducao_de_receita
                , c.identificador_deducao
                , c.natureza_receita
                , cod_ctb
                , tipo_entr_saida
                , c.tipo_movimentacao
                , c.cod_fonte_recursos
       ORDER BY cod_ctb 

        ";
    return $stSql;
    }
    public function __destruct(){}

}
?>