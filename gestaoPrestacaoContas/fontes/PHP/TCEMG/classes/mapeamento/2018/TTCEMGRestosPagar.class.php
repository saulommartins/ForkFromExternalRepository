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

class TTCEMGRestosPagar extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGRestosPagar()
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
      //VALIDACAO PARA TRAZER SO OS RESTOS A PAGAR SE O MES FOR JANEIRO
      if ($this->getDado('mes') == 1) {
        $stSql = "
                    SELECT 10 AS tipo_registro
                         , rp.cod_empenho::VARCHAR||rp.cod_entidade::VARCHAR||rp.exercicio::VARCHAR AS cod_reduzido
                         , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                         , CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                     THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                ELSE CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN LPAD(orgao.num_unidade::VARCHAR,5,'0')
                                          ELSE LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
                                     END
                           END AS cod_unidade
                         , CASE WHEN cnpj_prefeitura.valor = '18301002000186' AND pre_empenho.implantado = TRUE THEN LPAD(orgao.num_unidade::VARCHAR,5,'0')
                                ELSE LPAD(LPAD(orgao.num_orgao::VARCHAR,2,'0')||LPAD(orgao.num_unidade::VARCHAR,2,'0'),5,'0') 
                                END AS cod_unidade_orig
                         , rp.cod_empenho AS num_empenho
                         , TO_CHAR (empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
                         , empenho.exercicio as exercicio_empenho
                         , CASE WHEN restos_pre_empenho.cod_pre_empenho IS NOT NULL THEN
                                         CASE WHEN empenho.exercicio > '2012' THEN ''
                                              ELSE LPAD(restos_pre_empenho.cod_funcao::VARCHAR,2,'0')||LPAD(restos_pre_empenho.cod_subfuncao::VARCHAR,3,'0')||LPAD(restos_pre_empenho.cod_programa::VARCHAR,4,'0')||LPAD(restos_pre_empenho.num_pao::VARCHAR,4,'0')||LPAD(restos_pre_empenho.cod_estrutural,8,'0')
                                         END    
                           END AS dot_orig
                         , REPLACE(SUM(rp.empenhado)::varchar,'.',',')       AS vl_original
                         , REPLACE(SUM(rp.liquidadoapagar)::varchar,'.',',') AS vl_saldo_ant_proc
                         , REPLACE(SUM(rp.aliquidar)::varchar,'.',',')       AS vl_saldo_ant_nao
                      FROM empenho.fn_relatorio_resumo_execucao_restos_pagar   ( '".$this->getDado('exercicio')."'
                                                                               , '".$this->getDado('entidades')."'
                                                                               , '01/01/".$this->getDado('exercicio')."'
                                                                               , '01/01/".$this->getDado('exercicio')."'
                                                                               , ''
                                                                               , ''
                                                                               , 0
                                                                               , 0
                                                                               ) AS rp
                                                                               ( cod_empenho                INTEGER,
                                                                                 cod_entidade               INTEGER,
                                                                                 exercicio                  VARCHAR,
                                                                                 credor                     VARCHAR,
                                                                                 emissao                    TEXT,
                                                                                 vencimento                 TEXT,
                                                                                 empenhado                  NUMERIC(14,2),
                                                                                 aliquidar                  NUMERIC(14,2),
                                                                                 liquidadoapagar            NUMERIC(14,2),
                                                                                 anulado                    NUMERIC(14,2),
                                                                                 liquidado                  NUMERIC(14,2),
                                                                                 pagamento                  NUMERIC(14,2),
                                                                                 empenhado_saldo            NUMERIC(14,2),
                                                                                 aliquidar_saldo            NUMERIC(14,2),
                                                                                 liquidadoapagar_saldo      NUMERIC(14,2)
                                                                               )


              INNER JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = rp.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

              INNER JOIN empenho.empenho
                      ON empenho.exercicio = rp.exercicio
                     AND empenho.cod_entidade = rp.cod_entidade
                     AND empenho.cod_empenho = rp.cod_empenho

              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

               LEFT JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND restos_pre_empenho.exercicio = pre_empenho.exercicio

               LEFT JOIN tcemg.uniorcam
                      ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao 
                     AND uniorcam.exercicio   = restos_pre_empenho.exercicio
                     AND uniorcam.num_orgao_atual IS NOT NULL

               LEFT JOIN ( SELECT * FROM (
                                            SELECT  despesa.num_orgao
                                                    , despesa.num_unidade
                                                    , despesa.exercicio
                                                    , pre_empenho_despesa.cod_pre_empenho
                                            FROM empenho.pre_empenho_despesa
                                            JOIN orcamento.despesa
                                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                            JOIN orcamento.conta_despesa
                                              ON conta_despesa.exercicio = despesa.exercicio
                                             AND conta_despesa.cod_conta = despesa.cod_conta
                                            UNION
                                            SELECT  restos_pre_empenho.num_orgao
                                                    , restos_pre_empenho.num_unidade
                                                    , restos_pre_empenho.exercicio
                                                    , restos_pre_empenho.cod_pre_empenho
                                            FROM empenho.restos_pre_empenho
                                        ) as tbl
                                GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho
                        ) AS orgao
                      ON orgao.exercicio = pre_empenho.exercicio
                     AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN ( SELECT *
                             FROM administracao.configuracao
                            WHERE configuracao.exercicio = '".$this->getDado('exercicio')."'
                              AND configuracao.cod_modulo = 2
                              AND configuracao.parametro = 'cnpj'
                        ) AS cnpj_prefeitura
                      ON cnpj_prefeitura.exercicio = '".$this->getDado('exercicio')."'


                  GROUP BY cod_reduzido
                         , cod_orgao
                         , cod_unidade
                         , cod_unidade_orig
                         , num_empenho
                         , empenho.dt_empenho
                         , exercicio_empenho
                         , dot_orig
              ";

      }
      return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
      //VALIDACAO PARA TRAZER SO OS RESTOS A PAGAR SE O MES FOR JANEIRO
      if ($this->getDado('mes') == 1) {
        $stSql = "
                    SELECT 11 AS tipo_registro
                         , rp.cod_empenho::VARCHAR||rp.cod_entidade::VARCHAR||rp.exercicio::VARCHAR AS cod_reduzido
                         , CASE WHEN pre_empenho.implantado = TRUE THEN restos_pre_empenho.recurso::VARCHAR
                                   ELSE orgao.cod_fonte
                              END AS cod_fonte_recurso
                         , REPLACE(SUM(rp.empenhado)::varchar,'.',',')       AS vl_fonte
                         , REPLACE(SUM(rp.liquidadoapagar)::varchar,'.',',') AS vl_saldo_ant_proc
                         , REPLACE(SUM(rp.aliquidar)::varchar,'.',',')       AS vl_saldo_ant_nao


                      FROM empenho.fn_relatorio_resumo_execucao_restos_pagar   ( '".$this->getDado('exercicio')."'
                                                                               , '".$this->getDado('entidades')."'
                                                                               , '01/01/".$this->getDado('exercicio')."'
                                                                               , '01/01/".$this->getDado('exercicio')."'
                                                                               , ''
                                                                               , ''
                                                                               , 0
                                                                               , 0
                                                                               ) AS rp
                                                                               ( cod_empenho                INTEGER,
                                                                                 cod_entidade               INTEGER,
                                                                                 exercicio                  VARCHAR,
                                                                                 credor                     VARCHAR,
                                                                                 emissao                    TEXT,
                                                                                 vencimento                 TEXT,
                                                                                 empenhado                  NUMERIC(14,2),
                                                                                 aliquidar                  NUMERIC(14,2),
                                                                                 liquidadoapagar            NUMERIC(14,2),
                                                                                 anulado                    NUMERIC(14,2),
                                                                                 liquidado                  NUMERIC(14,2),
                                                                                 pagamento                  NUMERIC(14,2),
                                                                                 empenhado_saldo            NUMERIC(14,2),
                                                                                 aliquidar_saldo            NUMERIC(14,2),
                                                                                 liquidadoapagar_saldo      NUMERIC(14,2)
                                                                               )


              INNER JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = rp.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

              INNER JOIN empenho.empenho
                      ON empenho.exercicio = rp.exercicio
                     AND empenho.cod_entidade = rp.cod_entidade
                     AND empenho.cod_empenho = rp.cod_empenho

              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

               LEFT JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND restos_pre_empenho.exercicio = pre_empenho.exercicio

               LEFT JOIN tcemg.uniorcam
                      ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao 
                     AND uniorcam.exercicio   = restos_pre_empenho.exercicio
                     AND uniorcam.num_orgao_atual IS NOT NULL

               LEFT JOIN ( SELECT * FROM (
                                            SELECT  despesa.num_orgao
                                                    , despesa.num_unidade
                                                    , despesa.exercicio
                                                    , pre_empenho_despesa.cod_pre_empenho
                                                    , recurso.cod_fonte
                                            FROM empenho.pre_empenho_despesa
                                            JOIN orcamento.despesa
                                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                            JOIN orcamento.recurso
                                              ON recurso.exercicio = despesa.exercicio
                                             AND recurso.cod_recurso = despesa.cod_recurso
                                            JOIN orcamento.conta_despesa
                                              ON conta_despesa.exercicio = despesa.exercicio
                                             AND conta_despesa.cod_conta = despesa.cod_conta
                                            UNION
                                            SELECT  restos_pre_empenho.num_orgao
                                                    , restos_pre_empenho.num_unidade
                                                    , restos_pre_empenho.exercicio
                                                    , restos_pre_empenho.cod_pre_empenho
                                                    , restos_pre_empenho.recurso::VARCHAR AS cod_fonte
                                            FROM empenho.restos_pre_empenho
                                        ) as tbl
                                GROUP BY num_orgao, exercicio, num_unidade, cod_pre_empenho, cod_fonte
                        ) AS orgao
                      ON orgao.exercicio = pre_empenho.exercicio
                     AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

               LEFT JOIN ( SELECT *
                             FROM administracao.configuracao
                            WHERE configuracao.exercicio = '".$this->getDado('exercicio')."'
                              AND configuracao.cod_modulo = 2
                              AND configuracao.parametro = 'cnpj'
                        ) AS cnpj_prefeitura
                      ON cnpj_prefeitura.exercicio = '".$this->getDado('exercicio')."'
                GROUP BY cod_reduzido
                       , cod_fonte_recurso
                ORDER BY cod_reduzido
          ";
      
      }

      return $stSql;
    }

    public function recuperaExportacao12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao12()
    {
        $stSql = "
                    SELECT
                              12 AS tipo_registro
                            , empenho.cod_empenho::VARCHAR||empenho.cod_entidade::VARCHAR||empenho.exercicio::VARCHAR AS cod_reduzido
                            , CASE WHEN trim(sw_pais.nom_pais) <> 'Brasil' THEN 3
                                   WHEN sw_cgm_pessoa_fisica.cpf <> '' THEN 1
                                   WHEN sw_cgm_pessoa_juridica.cnpj <> '' THEN 2
                                   ELSE 1
                              END AS tipo_documento
                            , CASE WHEN sw_cgm_pessoa_fisica.cpf <> '' THEN sw_cgm_pessoa_fisica.cpf
                                   WHEN sw_cgm_pessoa_juridica.cnpj <> '' THEN sw_cgm_pessoa_juridica.cnpj
                                   ELSE '00000000000000'
                              END AS num_documento
                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                      ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = empenho.exercicio

                    JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                     AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                     AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                    LEFT JOIN (  SELECT pre_empenho.exercicio
                                 , pre_empenho.cod_pre_empenho
                                 , CASE WHEN ( pre_empenho.implantado = true )
                                        THEN restos_pre_empenho.num_orgao
                                        ELSE despesa.num_orgao
                                   END AS num_orgao
                                 , CASE WHEN ( pre_empenho.implantado = true )
                                        THEN restos_pre_empenho.num_unidade
                                        ELSE despesa.num_unidade
                                   END AS num_unidade
                                 , conta_despesa.cod_estrutural
                                 , CASE WHEN SUBSTR(conta_despesa.cod_estrutural, 9, 2) IN ('01','03','04','05','09','11','16','48','94')
                                        THEN true
                                   WHEN ''||SUBSTR(conta_despesa.cod_estrutural, 9, 2)||SUBSTR(conta_despesa.cod_estrutural, 12, 2) IN ('3626','3628','3699')
                                        THEN true
                                   WHEN SUBSTR(conta_despesa.cod_estrutural, 0, 14) IN ('3.1.9.0.92.01','3.1.9.0.92.02','3.1.7.1.92.01','3.1.7.1.92.02','3.1.9.1.92.01','3.1.9.1.92.02','3.3.9.0.36.07')
                                        THEN true
                                        ELSE false
                                   END AS folha
                              FROM empenho.pre_empenho
                         LEFT JOIN empenho.restos_pre_empenho
                                ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                               AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN empenho.pre_empenho_despesa
                                ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                               AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN orcamento.despesa
                                ON despesa.exercicio = pre_empenho_despesa.exercicio
                               AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              JOIN orcamento.conta_despesa
                                ON (    conta_despesa.exercicio = despesa.exercicio
                                    AND conta_despesa.cod_conta = despesa.cod_conta
                                   )
                                OR (    replace( conta_despesa.cod_estrutural, '.', '') = restos_pre_empenho.cod_estrutural
                                    AND conta_despesa.exercicio = restos_pre_empenho.exercicio
                                   )
                         ) AS orgao
                      ON orgao.exercicio = pre_empenho.exercicio
                     AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN sw_cgm
                      ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                    JOIN sw_pais
                      ON sw_pais.cod_pais = sw_cgm.cod_pais

               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

               LEFT JOIN (       SELECT *
                                    , COALESCE((rp.valor_processado_exercicios_anteriores + rp.valor_processado_exercicio_anterior),0.00) AS valor_processado_nao_pago
                                 FROM tcemg.fn_restos_pagar    (
                                                                '".$this->getDado('exercicio')."',
                                                                '".$this->getDado('entidades')."',
                                                                ".$this->getDado('mes')."
                                                            ) as rp (
                                                                cod_empenho INTEGER, cod_entidade INTEGER, exercicio CHARACTER(4), valor_processado_exercicios_anteriores NUMERIC,
                                                                valor_processado_exercicio_anterior NUMERIC, valor_processado_cancelado NUMERIC, valor_processado_pago NUMERIC,
                                                                valor_nao_processado_exercicios_anteriores NUMERIC, valor_nao_processado_exercicio_anterior NUMERIC,
                                                                valor_nao_processado_cancelado NUMERIC, valor_nao_processado_pago NUMERIC
                                                            )
                        ) AS restos_pagar
                      ON restos_pagar.cod_empenho = empenho.cod_empenho
                     AND restos_pagar.cod_entidade = empenho.cod_entidade
                     AND restos_pagar.exercicio = empenho.exercicio
                     
               LEFT JOIN ( SELECT retorno.entidade AS cod_entidade
                                , retorno.empenho AS cod_empenho 
                                , retorno.exercicio  
                                , sum(retorno.valor) AS vl_pago_exercicio_atual                                               
                             FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
                                ( ''                      
                                , ''                      
                                , '01/01/".$this->getDado('exercicio')."'
                                , '31/12/".$this->getDado('exercicio')."'
                                , '".$this->getDado('entidades')."'
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , ''
                                , '1'
                                , ''
                                , ''
                                , ''
                                , ''
                                ) AS retorno(      
                                     entidade            integer,                             
                                     empenho             integer,                             
                                     exercicio           char(4),                             
                                     credor              varchar,                             
                                     cod_estrutural      varchar,                             
                                     cod_nota            integer,                             
                                     data                text,                                
                                     conta               integer,                             
                                     banco               varchar,                             
                                     valor               numeric                              
                                     )                                     
                         GROUP BY retorno.entidade
                                , retorno.empenho  
                                , retorno.exercicio 
                        ) AS pagamento_restos                        
                      ON pagamento_restos.cod_empenho = empenho.cod_empenho
                     AND pagamento_restos.cod_entidade = empenho.cod_entidade
                     AND pagamento_restos.exercicio = empenho.exercicio


                   WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                     AND (
                             (pagamento_restos.vl_pago_exercicio_atual > 0.00)
                         OR    (restos_pagar.valor_processado_nao_pago > 0.00)
                         OR    (restos_pagar.valor_nao_processado_exercicio_anterior > 0.00)
                         )
                     AND pre_empenho.exercicio < '2013'
                     AND empenho.exercicio < '2013'
                     AND (sw_cgm_pessoa_fisica.cpf <> '' OR sw_cgm_pessoa_juridica.cnpj <> '')
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
                  SELECT 20 AS tipo_registro
                       , cod_reduzido
                       , cod_orgao
                       , cod_unidade
                       , cod_unidade_orig
                       , num_empenho
                       , exercicio_empenho
                       , dt_empenho
                       , tipo_restos_pagar
                       , tipo_movimento
                       , dt_movimentacao
                       , dot_orig
                       , SUM(COALESCE(vl_movimentacao,0.00)) AS vl_movimentacao
                       , cod_orgao_encamp_atrib
                       , cod_unidade_encamp_atrib
                       , justificativa
                       , ato_cancelamento
                       , dt_cancelamento
                    FROM (
                          SELECT LPAD(empenho.exercicio, 4, '0')::VARCHAR||LPAD(empenho.cod_entidade::VARCHAR, 2, '0')::VARCHAR||LPAD(2::VARCHAR, 2, '0')::VARCHAR||LPAD(empenho.cod_empenho::VARCHAR, 7, '0')::VARCHAR AS cod_reduzido
                               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      THEN CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                                THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                                ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                            END
                                      ELSE LPAD((LPAD(despesa_empenho.num_orgao::VARCHAR, 2, '0')||LPAD(despesa_empenho.num_unidade::VARCHAR, 2, '0')), 5, '0')
                                  END AS cod_unidade
                               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho AND pre_empenho.implantado = TRUE
                                      THEN LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                      ELSE LPAD((LPAD(despesa_empenho.num_orgao::VARCHAR, 2, '0')||LPAD(despesa_empenho.num_unidade::VARCHAR, 2, '0')), 5, '0')
                                  END AS cod_unidade_orig
                               , empenho.cod_empenho AS num_empenho
                               , empenho.exercicio AS exercicio_empenho
                               , TO_CHAR(empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
                               , 2 AS tipo_restos_pagar
                               , 1 tipo_movimento
                               , TO_CHAR(empenho_anulado.timestamp, 'ddmmyyyy') AS dt_movimentacao
                               , '' AS dot_orig
                               , SUM(COALESCE(empenho_anulado.vl_anulado,0.00)) AS vl_movimentacao
                               , '' AS cod_orgao_encamp_atrib
                               , '' AS cod_unidade_encamp_atrib
                               , 'ANULACAO EMPENHO DE RESTOS NAO PROCESSADO'::VARCHAR AS justificativa
                               , 'DECRETO 55'::VARCHAR AS ato_cancelamento
                               , TO_CHAR(empenho_anulado.timestamp, 'ddmmyyyy') AS dt_cancelamento

                            FROM (
                                  SELECT empenho_anulado.exercicio
                                       , empenho_anulado.cod_entidade
                                       , empenho_anulado.cod_empenho
                                       , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                                       , empenho_anulado.timestamp
                                       , empenho_anulado.motivo
                                    FROM empenho.empenho_anulado_item
                              INNER JOIN empenho.empenho_anulado
                                      ON empenho_anulado_item.exercicio    = empenho_anulado.exercicio
                                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                     AND empenho_anulado_item.cod_empenho  = empenho_anulado.cod_empenho
                                     AND empenho_anulado_item.timestamp    = empenho_anulado.timestamp
                                   WHERE empenho_anulado.exercicio < '".$this->getDado('exercicio')."'
                                     AND empenho_anulado.cod_entidade IN (".$this->getDado('entidades').")
                                     AND empenho_anulado.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd')
                                                                             AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
                                GROUP BY empenho_anulado.exercicio
                                       , empenho_anulado.cod_empenho
                                       , empenho_anulado.cod_entidade
                                       , empenho_anulado.timestamp
                                       , empenho_anulado.motivo
                                 ) AS empenho_anulado
                      INNER JOIN empenho.empenho
                              ON empenho_anulado.exercicio    = empenho.exercicio
                             AND empenho_anulado.cod_entidade = empenho.cod_entidade
                             AND empenho_anulado.cod_empenho  = empenho.cod_empenho
   
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio       = empenho.exercicio

                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                             AND configuracao_entidade.exercicio = empenho.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                       LEFT JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio

                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL

                       LEFT JOIN (
                                  SELECT despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , pre_empenho_despesa.exercicio AS exercicio_empenho_despesa
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                                GROUP BY despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , exercicio_empenho_despesa
                                 ) AS despesa_empenho
                              ON despesa_empenho.cod_pre_empenho           = pre_empenho.cod_pre_empenho
                             AND despesa_empenho.exercicio_empenho_despesa = pre_empenho.exercicio
                             AND despesa_empenho.cod_entidade              = empenho.cod_entidade

                           WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                        GROUP BY cod_reduzido
                               , cod_orgao
                               , cod_unidade
                               , cod_unidade_orig
                               , num_empenho
                               , exercicio_empenho
                               , dt_empenho
                               , dt_movimentacao
                               , dt_cancelamento
                               , tipo_restos_pagar
                       UNION ALL
                          SELECT LPAD(empenho.exercicio, 4, '0')::VARCHAR||LPAD(empenho.cod_entidade::VARCHAR, 2, '0')::VARCHAR||LPAD(1::VARCHAR, 2, '0')::VARCHAR||LPAD(empenho.cod_empenho::VARCHAR, 7, '0')::VARCHAR AS cod_reduzido
                               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      THEN CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                                THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                                ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                            END
                                      ELSE LPAD((LPAD(despesa_empenho.num_orgao::VARCHAR, 2, '0')||LPAD(despesa_empenho.num_unidade::VARCHAR, 2, '0')), 5, '0')
                                  END AS cod_unidade
                               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho AND pre_empenho.implantado = TRUE
                                      THEN LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                      ELSE LPAD((LPAD(despesa_empenho.num_orgao::VARCHAR, 2, '0')||LPAD(despesa_empenho.num_unidade::VARCHAR, 2, '0')), 5, '0')
                                  END AS cod_unidade_orig
                               , empenho.cod_empenho AS num_empenho
                               , empenho.exercicio AS exercicio_empenho
                               , TO_CHAR(empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
                               , 1 AS tipo_restos_pagar
                               , 1 tipo_movimento
                               , TO_CHAR(liquidacao_anulado.timestamp, 'ddmmyyyy') AS dt_movimentacao
                               , '' AS dot_orig
                               , SUM(COALESCE(liquidacao_anulado.vl_anulado,0.00)) AS vl_movimentacao
                               , '' AS cod_orgao_encamp_atrib
                               , '' AS cod_unidade_encamp_atrib
                               , 'ANULACAO EMPENHO DE RESTOS PROCESSADO'::VARCHAR AS justificativa
                               , 'DECRETO 55'::VARCHAR AS ato_cancelamento
                               , TO_CHAR(liquidacao_anulado.timestamp, 'ddmmyyyy') AS dt_cancelamento

                            FROM (
                                  SELECT nota_liquidacao.exercicio_empenho AS exercicio_empenho_liquidacao
                                       , nota_liquidacao.cod_entidade
                                       , nota_liquidacao.cod_empenho
                                       , SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) AS vl_anulado
                                       , nota_liquidacao_item_anulado.timestamp
                                    FROM empenho.nota_liquidacao_item_anulado
                    
                              INNER JOIN empenho.nota_liquidacao_item
                                      ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                                     AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                                     AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                                     AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                                     AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                                     AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                                     
                              INNER JOIN empenho.nota_liquidacao
                                      ON nota_liquidacao.exercicio    = nota_liquidacao_item_anulado.exercicio
                                     AND nota_liquidacao.cod_nota     = nota_liquidacao_item_anulado.cod_nota
                                     AND nota_liquidacao.cod_entidade = nota_liquidacao_item_anulado.cod_entidade
                                     
                                   WHERE nota_liquidacao.exercicio < '".$this->getDado('exercicio')."'
                                     AND nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('entidades').")
                                     AND nota_liquidacao_item_anulado.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd')
                                                                                          AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
                                GROUP BY exercicio_empenho_liquidacao
                                       , nota_liquidacao.cod_entidade
                                       , nota_liquidacao.cod_empenho
                                       , nota_liquidacao_item_anulado.timestamp
                                 ) AS liquidacao_anulado
                      INNER JOIN empenho.empenho
                              ON liquidacao_anulado.exercicio_empenho_liquidacao = empenho.exercicio
                             AND liquidacao_anulado.cod_entidade                 = empenho.cod_entidade
                             AND liquidacao_anulado.cod_empenho                  = empenho.cod_empenho
   
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio       = empenho.exercicio

                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                             AND configuracao_entidade.exercicio = empenho.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                       LEFT JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio

                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL

                       LEFT JOIN (
                                  SELECT despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , pre_empenho_despesa.exercicio AS exercicio_empenho_despesa
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                                GROUP BY despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , exercicio_empenho_despesa
                                 ) AS despesa_empenho
                              ON despesa_empenho.cod_pre_empenho           = pre_empenho.cod_pre_empenho
                             AND despesa_empenho.exercicio_empenho_despesa = pre_empenho.exercicio
                             AND despesa_empenho.cod_entidade              = empenho.cod_entidade

                           WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                        GROUP BY cod_reduzido
                               , cod_orgao
                               , cod_unidade
                               , cod_unidade_orig
                               , num_empenho
                               , exercicio_empenho
                               , dt_empenho
                               , dt_movimentacao
                               , dt_cancelamento
                               , tipo_restos_pagar

                            ) AS tabela
                   GROUP BY cod_reduzido
                          , cod_orgao
                          , cod_unidade
                          , cod_unidade_orig
                          , num_empenho
                          , exercicio_empenho
                          , dt_empenho
                          , tipo_restos_pagar
                          , tipo_movimento
                          , dt_movimentacao
                          , dot_orig
                          , cod_orgao_encamp_atrib
                          , cod_unidade_encamp_atrib
                          , justificativa
                          , ato_cancelamento
                          , dt_cancelamento
                   ORDER BY num_empenho
        ";
        
        return $stSql;
    }

    public function recuperaExportacao21(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao21",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao21()
    {
        $stSql = "
                  SELECT 21 AS tipo_registro
                       , cod_reduzido
                       , cod_fonte_recurso
                       , SUM(COALESCE(vl_movimentacao,0.00)) AS vl_movimentacao_fonte
                    FROM (
                          SELECT LPAD(empenho.exercicio, 4, '0')::VARCHAR||LPAD(empenho.cod_entidade::VARCHAR, 2, '0')::VARCHAR||LPAD(2::VARCHAR, 2, '0')::VARCHAR||LPAD(empenho.cod_empenho::VARCHAR, 7, '0')::VARCHAR AS cod_reduzido
                               , CASE WHEN pre_empenho.implantado = TRUE
                                      THEN restos_pre_empenho.recurso::VARCHAR
                                      ELSE despesa_empenho.cod_fonte
                                  END AS cod_fonte_recurso
                               , SUM(COALESCE(empenho_anulado.vl_anulado,0.00)) AS vl_movimentacao
                            FROM (
                                  SELECT empenho_anulado.exercicio
                                       , empenho_anulado.cod_entidade
                                       , empenho_anulado.cod_empenho
                                       , SUM(COALESCE(empenho_anulado_item.vl_anulado,0.00)) AS vl_anulado
                                       , empenho_anulado.timestamp
                                       , empenho_anulado.motivo
                                    FROM empenho.empenho_anulado_item
                              INNER JOIN empenho.empenho_anulado
                                      ON empenho_anulado_item.exercicio    = empenho_anulado.exercicio
                                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                     AND empenho_anulado_item.cod_empenho  = empenho_anulado.cod_empenho
                                     AND empenho_anulado_item.timestamp    = empenho_anulado.timestamp
                                   WHERE empenho_anulado.exercicio < '".$this->getDado('exercicio')."'
                                     AND empenho_anulado.cod_entidade IN (".$this->getDado('entidades').")
                                     AND empenho_anulado.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd')
                                                                             AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
                                GROUP BY empenho_anulado.exercicio
                                       , empenho_anulado.cod_empenho
                                       , empenho_anulado.cod_entidade
                                       , empenho_anulado.timestamp
                                       , empenho_anulado.motivo
                                 ) AS empenho_anulado
                      INNER JOIN empenho.empenho
                              ON empenho_anulado.exercicio    = empenho.exercicio
                             AND empenho_anulado.cod_entidade = empenho.cod_entidade
                             AND empenho_anulado.cod_empenho  = empenho.cod_empenho
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio       = empenho.exercicio
                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                             AND configuracao_entidade.exercicio = empenho.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                       LEFT JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL
                       LEFT JOIN (
                                  SELECT despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , pre_empenho_despesa.exercicio AS exercicio_empenho_despesa
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                               LEFT JOIN orcamento.recurso
                                      ON recurso.exercicio   = despesa.exercicio
                                     AND recurso.cod_recurso = despesa.cod_recurso

                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                                GROUP BY despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , exercicio_empenho_despesa
                                 ) AS despesa_empenho
                              ON despesa_empenho.cod_pre_empenho           = pre_empenho.cod_pre_empenho
                             AND despesa_empenho.exercicio_empenho_despesa = pre_empenho.exercicio
                             AND despesa_empenho.cod_entidade              = empenho.cod_entidade

                           WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                        GROUP BY cod_reduzido
                               , cod_fonte_recurso
                       UNION ALL
                          SELECT LPAD(empenho.exercicio, 4, '0')::VARCHAR||LPAD(empenho.cod_entidade::VARCHAR, 2, '0')::VARCHAR||LPAD(2::VARCHAR, 2, '0')::VARCHAR||LPAD(empenho.cod_empenho::VARCHAR, 7, '0')::VARCHAR AS cod_reduzido
                               , CASE WHEN pre_empenho.implantado = TRUE
                                      THEN restos_pre_empenho.recurso::VARCHAR
                                      ELSE despesa_empenho.cod_fonte
                                  END AS cod_fonte_recurso
                               , SUM(COALESCE(liquidacao_anulado.vl_anulado,0.00)) AS vl_movimentacao

                            FROM (
                                  SELECT nota_liquidacao.exercicio_empenho AS exercicio_empenho_liquidacao
                                       , nota_liquidacao.cod_entidade
                                       , nota_liquidacao.cod_empenho
                                       , SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) AS vl_anulado
                                       , nota_liquidacao_item_anulado.timestamp
                                    FROM empenho.nota_liquidacao_item_anulado
                    
                              INNER JOIN empenho.nota_liquidacao_item
                                      ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                                     AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                                     AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                                     AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                                     AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                                     AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                                     
                              INNER JOIN empenho.nota_liquidacao
                                      ON nota_liquidacao.exercicio    = nota_liquidacao_item_anulado.exercicio
                                     AND nota_liquidacao.cod_nota     = nota_liquidacao_item_anulado.cod_nota
                                     AND nota_liquidacao.cod_entidade = nota_liquidacao_item_anulado.cod_entidade
                                     
                                   WHERE nota_liquidacao.exercicio < '".$this->getDado('exercicio')."'
                                     AND nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('entidades').")
                                     AND nota_liquidacao_item_anulado.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd')
                                                                                          AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
                                GROUP BY exercicio_empenho_liquidacao
                                       , nota_liquidacao.cod_entidade
                                       , nota_liquidacao.cod_empenho
                                       , nota_liquidacao_item_anulado.timestamp
                                 ) AS liquidacao_anulado
                      INNER JOIN empenho.empenho
                              ON liquidacao_anulado.exercicio_empenho_liquidacao = empenho.exercicio
                             AND liquidacao_anulado.cod_entidade                 = empenho.cod_entidade
                             AND liquidacao_anulado.cod_empenho                  = empenho.cod_empenho
   
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio       = empenho.exercicio

                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                             AND configuracao_entidade.exercicio = empenho.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

                       LEFT JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio

                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL

                       LEFT JOIN (
                                  SELECT despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , pre_empenho_despesa.exercicio AS exercicio_empenho_despesa
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                               LEFT JOIN orcamento.recurso
                                      ON recurso.exercicio   = despesa.exercicio
                                     AND recurso.cod_recurso = despesa.cod_recurso
                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                                GROUP BY despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , exercicio_empenho_despesa
                                 ) AS despesa_empenho
                              ON despesa_empenho.cod_pre_empenho           = pre_empenho.cod_pre_empenho
                             AND despesa_empenho.exercicio_empenho_despesa = pre_empenho.exercicio
                             AND despesa_empenho.cod_entidade              = empenho.cod_entidade

                           WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                        GROUP BY cod_reduzido
                               , cod_fonte_recurso

                         ) AS tabela
                GROUP BY cod_reduzido
                       , cod_fonte_recurso
                ORDER BY cod_reduzido
        ";
        return $stSql;
    }

    public function recuperaExportacao22(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao22",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao22()
    {
        $stSql = "
                  SELECT 22 AS tipo_registro
                       , cod_reduzido
                       , tipo_documento
                       , num_documento
                    FROM (
                          SELECT LPAD(empenho.exercicio, 4, '0')::VARCHAR||LPAD(empenho.cod_entidade::VARCHAR, 2, '0')::VARCHAR||LPAD(2::VARCHAR, 2, '0')::VARCHAR||LPAD(empenho.cod_empenho::VARCHAR, 7, '0')::VARCHAR AS cod_reduzido
                               , CASE WHEN TRIM(sw_cgm_pessoa_fisica.cpf) <> '' OR sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                      THEN 1
                                      WHEN TRIM(sw_cgm_pessoa_juridica.cnpj) <> '' OR sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                      THEN 2
                                      ELSE 3
                                  END AS tipo_documento
                               , CASE WHEN TRIM(sw_cgm_pessoa_fisica.cpf) <> '' OR sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                      THEN sw_cgm_pessoa_fisica.cpf
                                      WHEN TRIM(sw_cgm_pessoa_juridica.cnpj) <> '' OR sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                      THEN sw_cgm_pessoa_juridica.cnpj
                                      ELSE '00000000000000'
                                  END AS num_documento
                            FROM (
                                  SELECT empenho_anulado.exercicio
                                       , empenho_anulado.cod_entidade
                                       , empenho_anulado.cod_empenho
                                       , SUM(empenho_anulado_item.vl_anulado) AS vl_anulado
                                       , empenho_anulado.timestamp
                                       , empenho_anulado.motivo
                                    FROM empenho.empenho_anulado_item
                              INNER JOIN empenho.empenho_anulado
                                      ON empenho_anulado_item.exercicio    = empenho_anulado.exercicio
                                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                     AND empenho_anulado_item.cod_empenho  = empenho_anulado.cod_empenho
                                     AND empenho_anulado_item.timestamp    = empenho_anulado.timestamp
                                   WHERE empenho_anulado.exercicio < '".$this->getDado('exercicio')."'
                                     AND empenho_anulado.cod_entidade IN (".$this->getDado('entidades').")
                                     AND empenho_anulado.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd')
                                                                             AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
                                GROUP BY empenho_anulado.exercicio
                                       , empenho_anulado.cod_empenho
                                       , empenho_anulado.cod_entidade
                                       , empenho_anulado.timestamp
                                       , empenho_anulado.motivo
                                 ) AS empenho_anulado
                      INNER JOIN empenho.empenho
                              ON empenho_anulado.exercicio    = empenho.exercicio
                             AND empenho_anulado.cod_entidade = empenho.cod_entidade
                             AND empenho_anulado.cod_empenho  = empenho.cod_empenho
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio       = empenho.exercicio

                      INNER JOIN sw_cgm
                              ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                       LEFT JOIN sw_cgm_pessoa_fisica
                              ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                       LEFT JOIN sw_cgm_pessoa_juridica
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                             AND configuracao_entidade.exercicio = empenho.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                       LEFT JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL
                       LEFT JOIN (
                                  SELECT despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , pre_empenho_despesa.exercicio AS exercicio_empenho_despesa
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                               LEFT JOIN orcamento.recurso
                                      ON recurso.exercicio   = despesa.exercicio
                                     AND recurso.cod_recurso = despesa.cod_recurso

                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                                GROUP BY despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , exercicio_empenho_despesa
                                 ) AS despesa_empenho
                              ON despesa_empenho.cod_pre_empenho           = pre_empenho.cod_pre_empenho
                             AND despesa_empenho.exercicio_empenho_despesa = pre_empenho.exercicio
                             AND despesa_empenho.cod_entidade              = empenho.cod_entidade
                           WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                        GROUP BY cod_reduzido
                               , tipo_documento
                               , num_documento
                       UNION ALL
                          SELECT LPAD(empenho.exercicio, 4, '0')::VARCHAR||LPAD(empenho.cod_entidade::VARCHAR, 2, '0')::VARCHAR||LPAD(2::VARCHAR, 2, '0')::VARCHAR||LPAD(empenho.cod_empenho::VARCHAR, 7, '0')::VARCHAR AS cod_reduzido
                               , CASE WHEN TRIM(sw_cgm_pessoa_fisica.cpf) <> '' OR sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                      THEN 1
                                      WHEN TRIM(sw_cgm_pessoa_juridica.cnpj) <> '' OR sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                      THEN 2
                                      ELSE 3
                                  END AS tipo_documento
                               , CASE WHEN TRIM(sw_cgm_pessoa_fisica.cpf) <> '' OR sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                      THEN sw_cgm_pessoa_fisica.cpf
                                      WHEN TRIM(sw_cgm_pessoa_juridica.cnpj) <> '' OR sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                      THEN sw_cgm_pessoa_juridica.cnpj
                                      ELSE '00000000000000'
                                  END AS num_documento
                            FROM (
                                  SELECT nota_liquidacao.exercicio_empenho AS exercicio_empenho_liquidacao
                                       , nota_liquidacao.cod_entidade
                                       , nota_liquidacao.cod_empenho
                                       , SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0.00)) AS vl_anulado
                                       , nota_liquidacao_item_anulado.timestamp
                                    FROM empenho.nota_liquidacao_item_anulado
                              INNER JOIN empenho.nota_liquidacao_item
                                      ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                                     AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                                     AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                                     AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                                     AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                                     AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                              INNER JOIN empenho.nota_liquidacao
                                      ON nota_liquidacao.exercicio    = nota_liquidacao_item_anulado.exercicio
                                     AND nota_liquidacao.cod_nota     = nota_liquidacao_item_anulado.cod_nota
                                     AND nota_liquidacao.cod_entidade = nota_liquidacao_item_anulado.cod_entidade
                                   WHERE nota_liquidacao.exercicio < '".$this->getDado('exercicio')."'
                                     AND nota_liquidacao_item_anulado.cod_entidade IN (".$this->getDado('entidades').")
                                     AND nota_liquidacao_item_anulado.timestamp::date BETWEEN TO_DATE('".$this->getDado('dt_inicial')."', 'yyyy-mm-dd')
                                                                                          AND TO_DATE('".$this->getDado('dt_final')."', 'yyyy-mm-dd')
                                GROUP BY exercicio_empenho_liquidacao
                                       , nota_liquidacao.cod_entidade
                                       , nota_liquidacao.cod_empenho
                                       , nota_liquidacao_item_anulado.timestamp
                                 ) AS liquidacao_anulado
                      INNER JOIN empenho.empenho
                              ON liquidacao_anulado.exercicio_empenho_liquidacao = empenho.exercicio
                             AND liquidacao_anulado.cod_entidade                 = empenho.cod_entidade
                             AND liquidacao_anulado.cod_empenho                  = empenho.cod_empenho
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio       = empenho.exercicio
                      INNER JOIN sw_cgm
                              ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario
                       LEFT JOIN sw_cgm_pessoa_fisica
                              ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                       LEFT JOIN sw_cgm_pessoa_juridica
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                       LEFT JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
                             AND configuracao_entidade.exercicio = empenho.exercicio
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                       LEFT JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio
                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL
                       LEFT JOIN (
                                  SELECT despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , pre_empenho_despesa.exercicio AS exercicio_empenho_despesa
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                               LEFT JOIN orcamento.recurso
                                      ON recurso.exercicio   = despesa.exercicio
                                     AND recurso.cod_recurso = despesa.cod_recurso
                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                                GROUP BY despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_entidade
                                       , despesa.exercicio
                                       , recurso.cod_fonte
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , exercicio_empenho_despesa
                                 ) AS despesa_empenho
                              ON despesa_empenho.cod_pre_empenho           = pre_empenho.cod_pre_empenho
                             AND despesa_empenho.exercicio_empenho_despesa = pre_empenho.exercicio
                             AND despesa_empenho.cod_entidade              = empenho.cod_entidade
                           WHERE empenho.cod_entidade IN (".$this->getDado('entidades').")
                        GROUP BY cod_reduzido
                               , tipo_documento
                               , num_documento
                         ) AS tabela
                GROUP BY cod_reduzido
                       , tipo_documento
                       , num_documento
                ORDER BY cod_reduzido
        ";

        return $stSql;
    }

        function recuperaDadosEntidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
        {
        return $this->executaRecupera("montaRecuperaDadosEntidade",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function __destruct(){}

}
