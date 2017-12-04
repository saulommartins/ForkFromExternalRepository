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

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCEMGAnulacaoExtraOrcamentaria extends TOrcamentoContaReceita
{
    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "
                    SELECT tipo_registro
                    , cod_ext
                    , cod_font_recursos
                    , nro_op
                    , cod_unidade
                    , dt_pagamento
                    , nro_anulacao_op
                    , dt_anulacao_op
                    , vl_anulacao_op
                    
                    FROM (
                            SELECT
                                    10 AS tipo_registro
                                  , LPAD(TTE.cod_lote||''||TTE.cod_entidade||TTE.exercicio||(TTE.cod_lote_estorno+1),15,'0') AS cod_reduzido_aex                            
                                  , tcemg.seq_num_op_extra(TT.exercicio,TT.cod_entidade,1,TT.cod_lote)::varchar||balancete_extmmaa.cod_plano||TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS nro_op
                                  , TO_CHAR(TT.dt_autenticacao, 'ddmmyyyy') AS dt_pagamento
                                  , balancete_extmmaa.cod_plano||''||TT.cod_lote||lancamento.sequencia AS nro_anulacao_op
                                  , TO_CHAR(TTE.dt_autenticacao,'ddmmyyyy') AS dt_anulacao_op
                                  , CASE WHEN valor_lancamento.vl_lancamento < 0 THEN REPLACE((valor_lancamento.vl_lancamento * -1)::TEXT, '.', ',')
                                         ELSE REPLACE(valor_lancamento.vl_lancamento::TEXT, '.', ',')
                                  END AS vl_anulacao_op
                                  , CASE WHEN (balancete_extmmaa.tipo_lancamento = 1)
                                            THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      ELSE plano_analitica.cod_plano::VARCHAR
                                                 END
                                         WHEN (balancete_extmmaa.tipo_lancamento = 4)
                                            THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      ELSE plano_analitica.cod_plano::VARCHAR
                                                 END
                                         ELSE plano_analitica.cod_plano::VARCHAR
                                  END AS cod_ext
                                  , LPAD((LPAD(configuracao_entidade.valor::VARCHAR,2, '0')||LPAD(configuracao_entidade.cod_entidade::VARCHAR,2, '0')), 5, '0') AS cod_unidade 
                                  , plano_recurso.cod_recurso AS cod_font_recursos
                            
                              FROM tesouraria.transferencia AS TT
                    
                        INNER JOIN tesouraria.transferencia_estornada AS TTE
                                ON TTE.cod_lote=TT.cod_lote
                               AND TTE.exercicio=TT.exercicio
                               AND TTE.tipo=TT.tipo
                               AND TTE.cod_entidade=TT.cod_entidade
                    
                        INNER JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_plano = TT.cod_plano_debito
                               AND plano_analitica.exercicio = TT.exercicio
                    
                        INNER JOIN tcemg.balancete_extmmaa
                                ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                               AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                    
                        INNER JOIN contabilidade.plano_conta
                                ON plano_analitica.exercicio = plano_conta.exercicio
                               AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                        INNER JOIN contabilidade.lote
                                ON TT.exercicio = lote.exercicio
                               AND TT.cod_entidade = lote.cod_entidade
                               AND TT.tipo = lote.tipo
                               AND TT.cod_lote = lote.cod_lote
                    
                        INNER JOIN contabilidade.conta_debito
                                ON plano_analitica.exercicio = conta_debito.exercicio
                               AND plano_analitica.cod_plano = conta_debito.cod_plano 
                               AND TT.cod_lote = conta_debito.cod_lote     
                    
                        INNER JOIN contabilidade.valor_lancamento
                                ON conta_debito.exercicio = valor_lancamento.exercicio
                               AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                               AND conta_debito.tipo = valor_lancamento.tipo
                               AND conta_debito.cod_lote = valor_lancamento.cod_lote
                               AND conta_debito.sequencia = valor_lancamento.sequencia
                               AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
                               AND conta_debito.tipo <> 'I'
                    
                        INNER JOIN contabilidade.lancamento
                                ON lancamento.exercicio=valor_lancamento.exercicio
                               AND lancamento.cod_entidade=valor_lancamento.cod_entidade
                               AND lancamento.tipo=valor_lancamento.tipo
                               AND lancamento.cod_lote=valor_lancamento.cod_lote
                               AND lancamento.sequencia=valor_lancamento.sequencia

                        INNER JOIN administracao.configuracao_entidade
                                ON configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."
                               AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                               AND configuracao_entidade.cod_modulo = 55
                               AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                         LEFT JOIN contabilidade.plano_recurso
                                ON plano_recurso.exercicio = TT.exercicio
                               AND plano_recurso.cod_plano = TT.cod_plano_credito
                    
                             WHERE balancete_extmmaa.exercicio  = '". $this->getDado('exercicio')."'
                               AND TTE.dt_autenticacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                               AND TT.cod_entidade IN (".$this->getDado ( 'entidades' ).")
                               AND TT.cod_tipo IN (1)
                    
                             UNION
                    
                            SELECT
                                    10 AS tipo_registro
                                  , LPAD(TTE.cod_lote||''||TTE.cod_entidade||TTE.exercicio||(TTE.cod_lote_estorno+1),15,'0') AS cod_reduzido_aex
                                  , tcemg.seq_num_op_extra(TT.exercicio,TT.cod_entidade,2,TT.cod_lote)::varchar||balancete_extmmaa.cod_plano||TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS nro_op
                                  , TO_CHAR(TT.dt_autenticacao, 'ddmmyyyy') AS dt_pagamento
                                  , balancete_extmmaa.cod_plano||''||TT.cod_lote||lancamento.sequencia AS nro_anulacao_op
                                  , TO_CHAR(TTE.dt_autenticacao,'ddmmyyyy') AS dt_anulacao_op
                                  , CASE WHEN valor_lancamento.vl_lancamento < 0 THEN REPLACE((valor_lancamento.vl_lancamento * -1)::TEXT, '.', ',')
                                         ELSE REPLACE(valor_lancamento.vl_lancamento::TEXT, '.', ',')
                                  END AS vl_anulacao_op
                                  , CASE WHEN (balancete_extmmaa.tipo_lancamento = 1)
                                            THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      ELSE plano_analitica.cod_plano::VARCHAR
                                                 END
                                         WHEN (balancete_extmmaa.tipo_lancamento = 4)
                                            THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                        THEN LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0')||LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                      ELSE plano_analitica.cod_plano::VARCHAR
                                                 END
                                         ELSE plano_analitica.cod_plano::VARCHAR
                                  END AS cod_ext
                                  , LPAD((LPAD(configuracao_entidade.valor::VARCHAR,2, '0')||LPAD(configuracao_entidade.cod_entidade::VARCHAR,2, '0')), 5, '0') AS cod_unidade 
                                  , plano_recurso.cod_recurso AS cod_font_recursos
                            
                              FROM tesouraria.transferencia AS TT
                    
                        INNER JOIN tesouraria.transferencia_estornada AS TTE
                                ON TTE.cod_lote=TT.cod_lote
                               AND TTE.exercicio=TT.exercicio
                               AND TTE.tipo=TT.tipo
                               AND TTE.cod_entidade=TT.cod_entidade
                    
                        INNER JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_plano = TT.cod_plano_credito
                               AND plano_analitica.exercicio = TT.exercicio
                    
                        INNER JOIN tcemg.balancete_extmmaa
                                ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                               AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                    
                        INNER JOIN contabilidade.plano_conta
                                ON plano_analitica.exercicio = plano_conta.exercicio
                               AND plano_analitica.cod_conta = plano_conta.cod_conta
                    
                        INNER JOIN contabilidade.lote
                                ON TT.exercicio = lote.exercicio
                               AND TT.cod_entidade = lote.cod_entidade
                               AND TT.tipo = lote.tipo
                               AND TT.cod_lote = lote.cod_lote
                    
                        INNER JOIN contabilidade.conta_credito
                                ON plano_analitica.exercicio = conta_credito.exercicio
                               AND plano_analitica.cod_plano = conta_credito.cod_plano 
                               AND TT.cod_lote = conta_credito.cod_lote
                    
                        INNER JOIN contabilidade.valor_lancamento
                                ON conta_credito.exercicio = valor_lancamento.exercicio
                               AND conta_credito.cod_entidade = valor_lancamento.cod_entidade
                               AND conta_credito.tipo = valor_lancamento.tipo
                               AND conta_credito.cod_lote = valor_lancamento.cod_lote
                               AND conta_credito.sequencia = valor_lancamento.sequencia
                               AND conta_credito.tipo_valor = valor_lancamento.tipo_valor
                               AND conta_credito.tipo <> 'I'
                    
                        INNER JOIN contabilidade.lancamento
                                ON lancamento.exercicio=valor_lancamento.exercicio
                               AND lancamento.cod_entidade=valor_lancamento.cod_entidade
                               AND lancamento.tipo=valor_lancamento.tipo
                               AND lancamento.cod_lote=valor_lancamento.cod_lote
                               AND lancamento.sequencia=valor_lancamento.sequencia

                        INNER JOIN administracao.configuracao_entidade
                                ON configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."
                               AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                               AND configuracao_entidade.cod_modulo = 55
                               AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                         LEFT JOIN contabilidade.plano_recurso
                                ON plano_recurso.exercicio = TT.exercicio
                               AND plano_recurso.cod_plano = TT.cod_plano_debito
                    
                             WHERE balancete_extmmaa.exercicio  = '". $this->getDado('exercicio')."'
                               AND TTE.dt_autenticacao BETWEEN TO_DATE( '01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || '".$this->getDado('mes')."' || '-' || '01','yyyy-mm-dd'))
                               AND TT.cod_entidade IN (".$this->getDado ( 'entidades' ).")
                               AND TT.cod_tipo IN (1)
                        ) AS resultado
                    
                GROUP BY tipo_registro
                       , cod_ext
                       , nro_op
                       , dt_pagamento
                       , nro_anulacao_op
                       , dt_anulacao_op
                       , vl_anulacao_op
                       , cod_unidade
                       , cod_font_recursos
                    
                ORDER BY cod_ext
                ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
