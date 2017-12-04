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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOExtraOrcamentarias.class.php 56934 2014-01-08 19:46:44Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCEMGExtraOrcamentarias extends TOrcamentoContaReceita
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOExtraOrcamentarias()
    {
        parent::TOrcamentoContaReceita();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function criaTabelaExtras(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {        
        return $this->executaRecupera("montaCriaTabelaExtras",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaCriaTabelaExtras()
    {
        $stSql = "  INSERT INTO tcemg.arquivo_ext
                        SELECT DISTINCT
                                balancete_extmmaa.cod_plano
                                ,'".$this->getDado('exercicio')."' as exercicio
                                ,'".$this->getDado('mes')."' as mes
                        FROM tcemg.balancete_extmmaa
                   LEFT JOIN tcemg.arquivo_ext
                          ON balancete_extmmaa.cod_plano = arquivo_ext.cod_plano ";
                          
        if ($this->getDado('exercicio') == '2016' ) {
            $stSql.= " AND arquivo_ext.exercicio = balancete_extmmaa.exercicio
                     WHERE balancete_extmmaa.exercicio = '2016'
                       AND arquivo_ext.cod_plano IS NULL ";
        } else {
            $stSql.= " WHERE arquivo_ext.cod_plano IS NULL ";    
        }

        return $stSql;
    }

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
      return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
      $stSql = "  SELECT  tipo_registro
                        , cod_plano AS cod_ext 
                        , cod_orgao
                        , tipo_lancamento
                        , sub_tipo
                        , desdobra_sub_tipo                            
                        , desc_extra_orc
                        , '".$this->getDado('mes')."' as mes
                        , '".$this->getDado('exercicio')."' as ano

                    FROM (
                          SELECT 10 AS tipo_registro
                               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                               , LPAD(balancete_extmmaa.tipo_lancamento::VARCHAR,2,'0') as tipo_lancamento
                               , LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0') AS sub_tipo
                               , CASE WHEN (balancete_extmmaa.tipo_lancamento = 1)
                                          THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                      THEN LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                    WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                      THEN LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                    WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                                      THEN LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                    WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                                      THEN LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                    ELSE ''
                                              END
                                        WHEN (balancete_extmmaa.tipo_lancamento = 4)
                                          THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                      THEN LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                    WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                      THEN LPAD(balancete_extmmaa.sub_tipo_lancamento::VARCHAR,4,'0')
                                                    ELSE ''
                                              END
                                      ELSE ''
                               END AS desdobra_sub_tipo                                                            
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
                               END AS cod_plano
                               , CASE WHEN (balancete_extmmaa.tipo_lancamento = 1)
                                        THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                    THEN 'INSS'
                                                  WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                    THEN 'RPPS'
                                                  WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                                    THEN 'IRRF'
                                                  WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                                    THEN 'ISSQN'
                                                  ELSE remove_acentos(plano_conta.nom_conta)
                                            END
                                      WHEN (balancete_extmmaa.tipo_lancamento = 4)
                                        THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 1
                                                    THEN 'Repasse à Câmara'
                                                  WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                                    THEN 'Devolução de numerário para a prefeitura'
                                                  ELSE remove_acentos(plano_conta.nom_conta)
                                            END
                                      ELSE remove_acentos(plano_conta.nom_conta)
                               END AS desc_extra_orc

                            FROM contabilidade.plano_analitica 

                       LEFT JOIN tcemg.balancete_extmmaa
                              ON plano_analitica.cod_plano = balancete_extmmaa.cod_plano
                             AND plano_analitica.exercicio = balancete_extmmaa.exercicio

                      INNER JOIN contabilidade.plano_conta
                              ON plano_analitica.exercicio = plano_conta.exercicio
                             AND plano_analitica.cod_conta = plano_conta.cod_conta

                      INNER JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade IN (".$this->getDado('entidades').")
                             AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                
                      INNER JOIN tcemg.arquivo_ext
                              ON plano_analitica.cod_plano = arquivo_ext.cod_plano
                             AND plano_analitica.exercicio = arquivo_ext.exercicio

                           WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                             AND arquivo_ext.mes = '".$this->getDado('mes')."'

                        ) AS resultado
                GROUP BY tipo_registro
                       , cod_orgao
                       , tipo_lancamento
                       , sub_tipo
                       , desdobra_sub_tipo
                       , cod_plano
                       , desc_extra_orc

                ORDER BY  cod_plano
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
              SELECT tipo_registro
                   , cod_orgao
                   , cod_ext
                   , natureza_conta
                   , cod_font_recurso
                   , ABS(vl_saldo_ant) AS vl_saldo_ant
                   , ABS(vl_saldo_atual) AS vl_saldo_atual
                   , CASE WHEN natureza_anterior != ' '
                          THEN natureza_anterior
                          --ELSE CASE WHEN vl_saldo_ant < 0.00
                          --      THEN 'D'
                          --      ELSE 'C'
                          --END
                     	  ELSE CASE 
	             	  			WHEN natureza_conta = 'D' AND vl_saldo_ant < 0.00 THEN 'C'
	                            WHEN vl_saldo_ant < 0.00 THEN 'D'
	             	   			ELSE 'C'
	                      END
			END AS nat_saldo_anterior_fonte
                   , CASE WHEN natureza_atual != ' '
                          THEN natureza_atual
                          --ELSE CASE WHEN vl_saldo_atual < 0.00
                          --      THEN 'D'
                          --      ELSE 'C'
                          -- END
						  ELSE CASE 
						        WHEN natureza_conta = 'D' and vl_saldo_atual < 0.00 THEN 'C'
	                            WHEN vl_saldo_atual < 0.00 THEN 'D'
	             	   			ELSE 'C'
	                      END
                      END AS nat_saldo_atual_fonte
                   , ABS(vl_saldo_debitos) AS total_debitos
                   , ABS(vl_saldo_creditos) AS total_creditos
                FROM (
                      SELECT tipo_registro
                           , cod_orgao
                           , cod_ext
                           , cod_unidade
                           , tipo_lancamento
                           , sub_tipo
                           , (                           	
                           		select pla.natureza_saldo from contabilidade.plano_analitica pla where pla.exercicio = '".$this->getDado('exercicio')."' and pla.cod_plano = cod_ext::numeric
                           	 ) as natureza_conta
                           , cod_recurso AS cod_font_recurso
                           , SUM(vl_saldo_anterior) AS vl_saldo_ant
                           , SUM(vl_saldo_atual) AS vl_saldo_atual
                           , SUM(vl_saldo_debitos) AS vl_saldo_debitos
                           , SUM(vl_saldo_creditos) AS vl_saldo_creditos
                           , nat_saldo_anterior_fonte AS natureza_anterior
                           , nat_saldo_atual_fonte AS natureza_atual
                        FROM tcemg.fn_arquivo_ext_registro20( '".$this->getDado('exercicio')."'
                                                            , 'cod_entidade IN (".$this->getDado('entidades').")'
                                                            , '".$this->getDado('dt_inicial')."'
                                                            , '".$this->getDado('dt_final')."'
                                                            , 'A'::CHAR )
                          AS retorno( cod_estrutural             VARCHAR
                                    , tipo_registro              INTEGER
                                    , cod_orgao                  VARCHAR
                                              , cod_unidade                TEXT
                                    , tipo_lancamento            TEXT
                                    , sub_tipo                   TEXT
                                    , cod_ext                    VARCHAR
                                    , cod_recurso                INTEGER
                                    , vl_saldo_anterior          NUMERIC
                                    , vl_saldo_debitos           NUMERIC
                                    , vl_saldo_creditos          NUMERIC
                                    , vl_saldo_atual             NUMERIC
                                    , nat_saldo_anterior_fonte   CHAR
                                    , nat_saldo_atual_fonte      CHAR
                                    )
                    GROUP BY tipo_registro
                           , cod_orgao
                           , cod_unidade
                           , tipo_lancamento
                           , sub_tipo
                           , cod_ext
                           , cod_font_recurso
                           , nat_saldo_anterior_fonte
                           , nat_saldo_atual_fonte
                     ) AS registro20
        ";     
        return $stSql;
    }

    public function recuperaExportacao30(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao30",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao30()
    {
        $stSql = "
                  SELECT 30 AS tipo_registro
                       , LPAD((LPAD(configuracao_entidade.valor::VARCHAR,2, '0')||LPAD(configuracao_entidade.cod_entidade::VARCHAR,2, '0')), 5, '0') AS cod_unidade_sub 
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
                       , CASE WHEN transferencia.cod_tipo = 1 AND transferencia.cod_plano_credito = 3274
                                   THEN COALESCE((
                                         SELECT plano_recurso.cod_recurso
                                           FROM contabilidade.plano_conta     
                                     INNER JOIN contabilidade.plano_analitica 
                                             ON plano_conta.cod_conta = plano_analitica.cod_conta
                                            AND plano_conta.exercicio = plano_analitica.exercicio 
                                     INNER JOIN contabilidade.plano_recurso
                                             ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                            AND plano_recurso.exercicio = plano_analitica.exercicio
                                          WHERE plano_analitica.cod_plano = cod_ctb_caixa.caixa_credito
                                            AND plano_analitica.exercicio = transferencia.exercicio
                                            ),'100')
                                   ELSE CASE WHEN plano_recurso.cod_recurso IS NOT NULL
                                             THEN COALESCE(plano_recurso.cod_recurso,'100')
                                             ELSE COALESCE(plano_recurso.cod_recurso,'100')
                                         END
                               END AS cod_font_recurso
                       , COALESCE(lote.vl_lancamento,0.00) AS vl_op
                       , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,transferencia.cod_tipo,transferencia.cod_lote)::varchar AS cod_reduzido_op
                       , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,transferencia.cod_tipo,transferencia.cod_lote)::varchar||COALESCE(balancete_extmmaa.cod_plano, '0')::VARCHAR||TO_CHAR(lote.dt_lote, 'ddmmyyyy')::VARCHAR AS num_op
                       , COALESCE(documento.nro_documento::VARCHAR,(SELECT cnpj 
                                                                      FROM sw_cgm_pessoa_juridica 
                                                                     WHERE numcgm = (SELECT numcgm 
                                                                                       FROM orcamento.entidade 
                                                                                      WHERE exercicio = transferencia.exercicio 
                                                                                        AND cod_entidade = transferencia.cod_entidade
                                                                                    )
                                                                   ) 
                         ) AS num_documento_credor
                       , remove_acentos(plano_conta.nom_conta) AS especificacao_op
                       , cpfrespop.cpf AS cpf_responsavel
                       , COALESCE(documento.tipo,2) AS tipo_documento_credor
                       , TO_CHAR(lote.dt_lote, 'ddmmyyyy') AS dt_pagamento
                       , lote.cod_lote
                    FROM (
                          SELECT lote.cod_lote
                               , lote.dt_lote
                               , lote.exercicio
                               , conta_debito.cod_plano
                               , lote.tipo
                               , lote.cod_entidade
                               , valor_lancamento.vl_lancamento
                            FROM contabilidade.lote
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.exercicio    = lote.exercicio
                             AND lancamento.cod_entidade = lote.cod_entidade
                             AND lancamento.tipo         = lote.tipo
                             AND lancamento.cod_lote     = lote.cod_lote
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.exercicio    = lancamento.exercicio
                             AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                             AND valor_lancamento.tipo         = lancamento.tipo
                             AND valor_lancamento.cod_lote     = lancamento.cod_lote
                             AND valor_lancamento.sequencia    = lancamento.sequencia
                             AND valor_lancamento.tipo_valor = 'D'
                      INNER JOIN contabilidade.conta_debito
                              ON conta_debito.exercicio = valor_lancamento.exercicio
                             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                             AND conta_debito.tipo = valor_lancamento.tipo
                             AND conta_debito.cod_lote = valor_lancamento.cod_lote
                             AND conta_debito.sequencia = valor_lancamento.sequencia
                             AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
                             AND valor_lancamento.tipo = 'T'
                           WHERE lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo = 'T'
                             AND lote.cod_entidade IN (".$this->getDado('entidades').")
                             AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
                                                  AND TO_DATE ('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                        ORDER BY lote.cod_lote
                         ) AS lote
               LEFT JOIN tesouraria.transferencia
                      ON lote.exercicio     = transferencia.exercicio
                     AND lote.tipo          = transferencia.tipo
                     AND lote.cod_entidade  = transferencia.cod_entidade 
                     AND lote.cod_lote      = transferencia.cod_lote
                     AND lote.cod_plano     = transferencia.cod_plano_debito
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                     AND plano_analitica.exercicio = transferencia.exercicio
              INNER JOIN contabilidade.plano_conta
                      ON plano_analitica.exercicio = plano_conta.exercicio
                     AND plano_analitica.cod_conta = plano_conta.cod_conta
               LEFT JOIN tcemg.balancete_extmmaa
                      ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                     AND balancete_extmmaa.exercicio = plano_analitica.exercicio
               LEFT JOIN tcemg.contabilidade_lote_transferencia AS de_para_lote
                      ON de_para_lote.exercicio_credito    = lote.exercicio
                     AND de_para_lote.cod_entidade_credito = lote.cod_entidade
                     AND de_para_lote.tipo_credito         = lote.tipo
                     AND de_para_lote.cod_lote_credito     = lote.cod_lote
               LEFT JOIN (
                          SELECT conta_debito.tipo
                               , transferencia.cod_tipo
                               , conta_debito.exercicio
                               , conta_debito.cod_entidade
                               , CASE WHEN (conta_bancaria.cod_ctb_anterior is null)
                                      THEN transferencia.cod_plano_credito
                                      Else conta_bancaria.cod_ctb_anterior
                                  END AS cod_ctb_anterior
                               , transferencia.cod_plano_credito AS caixa_credito
                               , transferencia.cod_plano_debito  AS caixa_debito
                               , valor_lancamento.vl_lancamento
                               , lo.cod_lote
                               , lo.dt_lote
                            FROM contabilidade.conta_debito
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.exercicio    = conta_debito.exercicio
                             AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                             AND valor_lancamento.tipo         = conta_debito.tipo
                             AND valor_lancamento.cod_lote     = conta_debito.cod_lote
                             AND valor_lancamento.sequencia    = conta_debito.sequencia
                             AND valor_lancamento.tipo_valor   = conta_debito.tipo_valor
                             AND valor_lancamento.tipo_valor = 'D'
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
                             --AND plano_analitica.natureza_saldo = 'D'
                             AND plano_analitica.exercicio = conta_debito.exercicio
                       LEFT JOIN tcemg.conta_bancaria
                              ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                             AND conta_bancaria.exercicio = plano_analitica.exercicio
                           WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
                             AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")
                             AND lo.dt_lote BETWEEN TO_DATE ('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND TO_DATE ('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                             AND conta_debito.tipo      = 'T'
                             AND transferencia.cod_tipo = 5
                             AND conta_debito.cod_plano = 3274
                        GROUP BY conta_debito.tipo
                               , transferencia.cod_tipo
                               , conta_debito.exercicio
                               , conta_debito.cod_entidade
                               , transferencia.cod_plano_credito
                               , transferencia.cod_plano_debito
                               , valor_lancamento.vl_lancamento
                               , lo.cod_lote
                               , lo.dt_lote
                               , cod_ctb_anterior
                         ) AS cod_ctb_caixa
                      ON de_para_lote.exercicio_debito    = cod_ctb_caixa.exercicio
                     AND de_para_lote.cod_entidade_debito = cod_ctb_caixa.cod_entidade
                     AND de_para_lote.tipo_debito         = cod_ctb_caixa.tipo
                     AND de_para_lote.cod_lote_debito     = cod_ctb_caixa.cod_lote
              INNER JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
               LEFT JOIN (
                          SELECT cpf
                               , uniorcam.exercicio
                            FROM sw_cgm_pessoa_fisica
                      INNER JOIN tcemg.uniorcam
                              ON uniorcam.cgm_ordenador = sw_cgm_pessoa_fisica.numcgm 
                             AND uniorcam.num_orgao = 02
                             AND uniorcam.num_unidade = 01
                         ) AS cpfrespop
                      ON cpfrespop.exercicio = transferencia.exercicio
               LEFT JOIN tesouraria.recibo_extra_transferencia AS RET
                      ON RET.exercicio     = plano_analitica.exercicio
                     AND RET.cod_lote      = transferencia.cod_lote
                     AND RET.tipo          = transferencia.tipo
                     AND RET.cod_entidade  = transferencia.cod_entidade
               LEFT JOIN tesouraria.recibo_extra AS RE
                      ON RE.exercicio               = RET.exercicio
                     AND RE.cod_entidade          = RET.cod_entidade
                     AND RE.cod_recibo_extra     = RET.cod_recibo_extra
                     AND RE.tipo_recibo           = 'D'
               LEFT JOIN tesouraria.recibo_extra_credor AS REC
                      ON REC.exercicio        = RE.exercicio
                     AND REC.cod_entidade     = RE.cod_entidade
                     AND REC.cod_recibo_extra = RE.cod_recibo_extra 
                     AND REC.tipo_recibo      = 'D'
               LEFT JOIN (
                          SELECT sw_cgm.numcgm
                               , CASE WHEN (sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm) THEN 1
                                      WHEN (sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm) THEN 2    
                                      WHEN (sw_cgm.cod_pais != sw_pais.cod_pais) THEN 3
                                  END AS tipo
                               , CASE WHEN (sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm) THEN sw_cgm_pessoa_fisica.cpf
                                      WHEN (sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm) THEN sw_cgm_pessoa_juridica.cnpj    
                                      WHEN (sw_cgm.cod_pais != sw_pais.cod_pais) THEN 0000000000::TEXT
                                  END AS nro_documento
                            FROM sw_cgm
                      INNER JOIN sw_pais
                              ON sw_pais.cod_pais = sw_cgm.cod_pais
                       LEFT JOIN sw_cgm_pessoa_fisica
                              ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                       LEFT JOIN sw_cgm_pessoa_juridica 
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                         ) AS documento
                      ON documento.numcgm = REC.numcgm
               LEFT JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = plano_analitica.exercicio
                     AND plano_recurso.cod_plano = plano_analitica.cod_plano
                   WHERE transferencia.exercicio = '".$this->getDado('exercicio')."'
                     AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                     AND transferencia.cod_tipo IN (1)
                GROUP BY tipo_registro
                       , cod_unidade_sub 
                       , cod_ext
                       , cod_font_recurso
                       , vl_op
                       , cod_reduzido_op
                       , num_op
                       , num_documento_credor
                       , remove_acentos(plano_conta.nom_conta)
                       , cpf_responsavel
                       , tipo_documento_credor
                       , dt_pagamento
                       , lote.cod_lote
                ORDER BY cod_ext
            ";
        return $stSql;
    }

    public function recuperaExportacao31(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao31",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao31()
    {
        $stSql = "
                  SELECT 31 AS tipo_registro
                       , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,transferencia.cod_tipo,transferencia.cod_lote)::varchar AS cod_reduzido_op
                       , CASE WHEN transferencia_tipo_documento.cod_tipo_documento IS NULL
                               AND SUBSTR(cod_ctb_transferencia.cod_estrutural, 1, 7) <> '1111101'
                              THEN '99'
                              WHEN SUBSTR(cod_ctb_transferencia.cod_estrutural, 1, 7) = '1111101'
                              THEN '05'
                              ELSE transferencia_tipo_documento.cod_tipo_documento::VARCHAR
                          END AS tipo_documento_op                            
                       , transferencia_tipo_documento.num_documento AS num_documento
                            , CASE WHEN SUBSTR(cod_ctb_transferencia.cod_estrutural, 1, 7) = '1111101'
                                   THEN ''
                                   ELSE CASE WHEN cod_ctb_transferencia.cod_ctb_anterior IS NULL
                                             THEN transferencia.cod_plano_credito::VARCHAR
                                             ELSE cod_ctb_transferencia.cod_ctb_anterior::varchar
                                         END
                              END AS cod_ctb   
                            , CASE WHEN transferencia.cod_plano_credito = 3274
                                   THEN (
                                         SELECT COALESCE(plano_recurso.cod_recurso, '100')
                                           FROM contabilidade.plano_conta     
                                     INNER JOIN contabilidade.plano_analitica 
                                             ON plano_conta.cod_conta = plano_analitica.cod_conta
                                            AND plano_conta.exercicio = plano_analitica.exercicio 
                                     INNER JOIN contabilidade.plano_recurso
                                             ON plano_recurso.cod_plano = plano_analitica.cod_plano
                                            AND plano_recurso.exercicio = plano_analitica.exercicio
                                          WHERE plano_analitica.cod_plano = cod_ctb_caixa.caixa_credito
                                            AND plano_analitica.exercicio = transferencia.exercicio
                                            )
                                   ELSE CASE WHEN cod_ctb_transferencia.cod_recurso IS NOT NULL
                                             THEN COALESCE(cod_ctb_transferencia.cod_recurso, '100')
                                             ELSE COALESCE(plano_recurso.cod_recurso,'100')
                                         END
                               END AS cod_fonte_ctb
                             , CASE WHEN transferencia_tipo_documento.cod_tipo_documento = 99 AND SUBSTR(cod_ctb_transferencia.cod_estrutural, 1, 7) <> '1111101' THEN 'Outros'
                                   WHEN transferencia_tipo_documento.cod_tipo_documento IS NULL AND SUBSTR(cod_ctb_transferencia.cod_estrutural, 1, 7) <> '1111101' THEN 'Outros'
                                   WHEN SUBSTR(cod_ctb_transferencia.cod_estrutural, 1, 7) = '1111101' THEN ''
                                   ELSE tipo_documento.descricao
                              END AS desctipodocumentoop
                            , TO_CHAR(transferencia.dt_autenticacao, 'ddmmyyyy') AS dt_emissao
                            , COALESCE(lote.vl_lancamento, 0.00) AS vl_documento

                    FROM (
                          SELECT lote.cod_lote
                               , lote.dt_lote
                               , lote.exercicio
                               , conta_debito.cod_plano
                               , lote.tipo
                               , lote.cod_entidade
                               , valor_lancamento.vl_lancamento
                            FROM contabilidade.lote
                      INNER JOIN contabilidade.lancamento
                              ON lancamento.exercicio    = lote.exercicio
                             AND lancamento.cod_entidade = lote.cod_entidade
                             AND lancamento.tipo         = lote.tipo
                             AND lancamento.cod_lote     = lote.cod_lote
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.exercicio    = lancamento.exercicio
                             AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                             AND valor_lancamento.tipo         = lancamento.tipo
                             AND valor_lancamento.cod_lote     = lancamento.cod_lote
                             AND valor_lancamento.sequencia    = lancamento.sequencia
                             AND valor_lancamento.tipo_valor = 'D'
                      INNER JOIN contabilidade.conta_debito
                              ON conta_debito.exercicio = valor_lancamento.exercicio
                             AND conta_debito.cod_entidade = valor_lancamento.cod_entidade
                             AND conta_debito.tipo = valor_lancamento.tipo
                             AND conta_debito.cod_lote = valor_lancamento.cod_lote
                             AND conta_debito.sequencia = valor_lancamento.sequencia
                             AND conta_debito.tipo_valor = valor_lancamento.tipo_valor
                             AND valor_lancamento.tipo = 'T'
                           WHERE lote.exercicio = '".$this->getDado('exercicio')."'
                             AND lote.tipo = 'T'
                             AND lote.cod_entidade IN (".$this->getDado('entidades').")
                             AND lote.dt_lote BETWEEN TO_DATE ('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
                                                  AND TO_DATE ('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                        ORDER BY lote.cod_lote
                         ) AS lote
              INNER JOIN tesouraria.transferencia
                      ON lote.exercicio     = transferencia.exercicio
                     AND lote.tipo          = transferencia.tipo
                     AND lote.cod_entidade  = transferencia.cod_entidade 
                     AND lote.cod_lote      = transferencia.cod_lote
                     AND lote.cod_plano     = transferencia.cod_plano_debito
                     
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                     AND plano_analitica.exercicio = transferencia.exercicio
             
               LEFT JOIN tcemg.balancete_extmmaa
                      ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                     AND balancete_extmmaa.exercicio = plano_analitica.exercicio
                        
               LEFT JOIN tcemg.transferencia_tipo_documento
                      ON transferencia_tipo_documento.exercicio    = lote.exercicio
                     AND transferencia_tipo_documento.cod_entidade = lote.cod_entidade
                     AND transferencia_tipo_documento.tipo         = lote.tipo
                     AND transferencia_tipo_documento.cod_lote     = lote.cod_lote
                     
               LEFT JOIN tcemg.tipo_documento
                      ON tipo_documento.cod_tipo = transferencia_tipo_documento.cod_tipo_documento


               LEFT JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = plano_analitica.exercicio
                     AND plano_recurso.cod_plano = plano_analitica.cod_plano
                        
                 LEFT JOIN (
                            SELECT conta_debito.cod_lote
                                       , conta_debito.tipo
                                       , conta_debito.exercicio
                                       , conta_debito.cod_entidade
                                 , plano_recurso.cod_recurso
                                       , conta_bancaria.cod_ctb_anterior
                                       , transferencia.cod_plano_credito
                                       , transferencia.cod_plano_debito
                                       , conta_debito.sequencia
                                 , REPLACE(plano_conta.cod_estrutural , '.', '') as cod_estrutural 

                              FROM contabilidade.conta_debito
                                
                        INNER JOIN contabilidade.lote AS lo 
                                ON conta_debito.cod_lote     = lo.cod_lote
                                   AND conta_debito.tipo         = lo.tipo
                                   AND conta_debito.exercicio    = lo.exercicio
                                   AND conta_debito.cod_entidade = lo.cod_entidade
                                
                        INNER JOIN tesouraria.transferencia
                                      ON transferencia.cod_plano_debito = conta_debito.cod_plano
                                   AND lo.cod_lote                = transferencia.cod_lote
                                   AND transferencia.exercicio    = lo.exercicio
                                   AND transferencia.cod_entidade = lo.cod_entidade
                                   AND transferencia.tipo         = 'T'
                                   AND transferencia.cod_tipo     = 1
                
                        INNER JOIN contabilidade.plano_analitica
                                ON plano_analitica.cod_plano      = transferencia.cod_plano_credito
                               AND plano_analitica.natureza_saldo = 'D'
                               AND plano_analitica.exercicio      = conta_debito.exercicio
                                    
                        INNER JOIN contabilidade.plano_conta
                                ON plano_analitica.exercicio = plano_conta.exercicio
                               AND plano_analitica.cod_conta = plano_conta.cod_conta

                         LEFT JOIN contabilidade.plano_recurso
                                ON plano_recurso.exercicio = plano_analitica.exercicio
                               AND plano_recurso.cod_plano = plano_analitica.cod_plano
                        
                         LEFT JOIN tcemg.conta_bancaria
                                      ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                                   AND conta_bancaria.exercicio = plano_analitica.exercicio
                     
                             WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
                               AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")
                               AND lo.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
                                                  AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                               AND lo.exercicio = '".$this->getDado('exercicio')."'
                                   AND conta_debito.tipo = 'T'
                           ) AS cod_ctb_transferencia
                        ON cod_ctb_transferencia.exercicio        = lote.exercicio                             
                       AND cod_ctb_transferencia.cod_lote         = lote.cod_lote
                       AND cod_ctb_transferencia.tipo             = lote.tipo
                       AND cod_ctb_transferencia.cod_plano_debito = lote.cod_plano


                     
               LEFT JOIN tcemg.contabilidade_lote_transferencia AS de_para_lote
                      ON de_para_lote.exercicio_credito    = lote.exercicio
                     AND de_para_lote.cod_entidade_credito = lote.cod_entidade
                     AND de_para_lote.tipo_credito         = lote.tipo
                     AND de_para_lote.cod_lote_credito     = lote.cod_lote

                   LEFT JOIN (
                              SELECT conta_debito.tipo
                                   , transferencia.cod_tipo
                                        , conta_debito.exercicio
                                        , conta_debito.cod_entidade
                                       , CASE WHEN (conta_bancaria.cod_ctb_anterior is null) THEN transferencia.cod_plano_credito
                                                                Else conta_bancaria.cod_ctb_anterior
                                                                END AS cod_ctb_anterior
                                        , transferencia.cod_plano_credito AS caixa_credito
                                        , transferencia.cod_plano_debito  AS caixa_debito
                                        , valor_lancamento.vl_lancamento
                                        , lo.cod_lote
                                        , lo.dt_lote

                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.exercicio    = conta_debito.exercicio
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                                      AND valor_lancamento.tipo         = conta_debito.tipo
                                      AND valor_lancamento.cod_lote     = conta_debito.cod_lote
                                      AND valor_lancamento.sequencia    = conta_debito.sequencia
                                      AND valor_lancamento.tipo_valor   = conta_debito.tipo_valor
                                      AND valor_lancamento.tipo_valor = 'D'

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
                                      --AND plano_analitica.natureza_saldo = 'D'
                                      AND plano_analitica.exercicio = conta_debito.exercicio
                                LEFT JOIN tcemg.conta_bancaria
                                       ON conta_bancaria.cod_conta = plano_analitica.cod_conta
                                      AND conta_bancaria.exercicio = plano_analitica.exercicio
                                   WHERE conta_debito.exercicio = '".$this->getDado('exercicio')."'
                                     AND conta_debito.cod_entidade IN (".$this->getDado('entidades').")
                                     AND lo.dt_lote BETWEEN TO_DATE ('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND TO_DATE ('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                                     AND conta_debito.tipo      = 'T'
                                     AND transferencia.cod_tipo = 5
                                     AND conta_debito.cod_plano = 3274
                                GROUP BY conta_debito.tipo
                                       , transferencia.cod_tipo
                                       , conta_debito.exercicio
                                       , conta_debito.cod_entidade
                                       , transferencia.cod_plano_credito
                                       , transferencia.cod_plano_debito
                                       , valor_lancamento.vl_lancamento
                                       , lo.cod_lote
                                       , lo.dt_lote
                                       , cod_ctb_anterior
                             ) AS cod_ctb_caixa
                          ON de_para_lote.exercicio_debito    = cod_ctb_caixa.exercicio
                         AND de_para_lote.cod_entidade_debito = cod_ctb_caixa.cod_entidade
                         AND de_para_lote.tipo_debito         = cod_ctb_caixa.tipo
                         AND de_para_lote.cod_lote_debito     = cod_ctb_caixa.cod_lote

                     WHERE balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."'
                       AND transferencia.cod_entidade IN (".$this->getDado('entidades').")
                       AND transferencia.cod_tipo = 1
                
                  GROUP BY tipo_registro
                         , tipo_documento_op
                         , num_documento
                         , cod_reduzido_op
                         , cod_ctb
                         , cod_fonte_ctb
                         , desctipodocumentoop
                         , dt_emissao
                         , vl_documento
        
                  ORDER BY cod_reduzido_op
        ";
        return $stSql;
    }
    
    public function recuperaExportacao32(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao32",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao32()
    {
        $stSql = "
                  SELECT
                          32 AS tipo_registro
                        , tcemg.seq_num_op_extra(transferencia.exercicio,transferencia.cod_entidade,1,transferencia.cod_lote)::varchar AS cod_reduzido_op
                        , LPAD(balancete_extmmaa.tipo_lancamento::varchar,4,'0') AS tipo_retencao
                        , CASE WHEN balancete_extmmaa.tipo_lancamento <> 1
                                    AND balancete_extmmaa.tipo_lancamento <> 2
                                    AND balancete_extmmaa.tipo_lancamento <> 3
                                    AND balancete_extmmaa.tipo_lancamento <> 4
                                    THEN remove_acentos(plano_conta.nom_conta)
                               ELSE ' '
                        END AS descricao_retencao
                        , COALESCE(lote.vl_lancamento,0.00) AS vl_retencao

                    FROM tesouraria.transferencia
                
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                     AND plano_analitica.exercicio = transferencia.exercicio
                 
                  INNER JOIN (
                          SELECT lote.cod_lote
                               , lote.dt_lote
                               , lote.exercicio
                               , conta_debito.cod_plano
                               , lote.tipo
                               , lote.cod_entidade
                               , valor_lancamento.vl_lancamento
                         
                            FROM contabilidade.lote
                    
                      INNER JOIN contabilidade.valor_lancamento
                              ON valor_lancamento.exercicio     = lote.exercicio
                             AND valor_lancamento.cod_entidade  = lote.cod_entidade
                             AND valor_lancamento.tipo          = lote.tipo
                             AND valor_lancamento.cod_lote      = lote.cod_lote
                             AND valor_lancamento.tipo_valor    = 'D'
                     
                      INNER JOIN contabilidade.conta_debito
                              ON conta_debito.exercicio     = valor_lancamento.exercicio
                             AND conta_debito.cod_entidade  = valor_lancamento.cod_entidade
                             AND conta_debito.tipo          = valor_lancamento.tipo
                             AND conta_debito.cod_lote      = valor_lancamento.cod_lote
                             AND conta_debito.sequencia     = valor_lancamento.sequencia
                             AND valor_lancamento.tipo      = 'T'
                     
                           WHERE lote.exercicio = '".$this->getDado( 'exercicio' ). "'
                             AND lote.tipo = 'T'
                             AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
                                                  AND TO_DATE('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                        GROUP BY 1,2,3,4,5,6,7

                        ORDER BY lote.cod_lote
                             ) AS lote
                      ON lote.exercicio = plano_analitica.exercicio
                     AND lote.cod_plano = plano_analitica.cod_plano
                     AND lote.tipo = transferencia.tipo
                     AND lote.cod_entidade =  transferencia.cod_entidade 
                     AND lote.cod_lote = transferencia.cod_lote
             
              INNER JOIN tcemg.balancete_extmmaa
                      ON balancete_extmmaa.cod_plano = plano_analitica.cod_plano
                     AND balancete_extmmaa.exercicio = plano_analitica.exercicio
             
              INNER JOIN contabilidade.plano_conta
                      ON plano_analitica.exercicio = plano_conta.exercicio
                     AND plano_analitica.cod_conta = plano_conta.cod_conta
             
              INNER JOIN contabilidade.conta_debito
                      ON plano_analitica.exercicio = conta_debito.exercicio
                     AND plano_analitica.cod_plano = conta_debito.cod_plano
             
                 LEFT JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = balancete_extmmaa.exercicio
                     AND plano_recurso.cod_plano = balancete_extmmaa.cod_plano
                      
                   WHERE balancete_extmmaa.exercicio =  '".$this->getDado( 'exercicio' ). "'
                         AND balancete_extmmaa.tipo_lancamento = 1
                         AND transferencia.cod_entidade IN (".$this->getDado('entidades').")

                GROUP BY 1,2,3,4, 5
            
                ORDER BY cod_reduzido_op
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
