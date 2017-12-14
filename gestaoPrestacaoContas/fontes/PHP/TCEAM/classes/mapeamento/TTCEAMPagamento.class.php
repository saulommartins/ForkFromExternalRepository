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
    * Data de Criacão: 22/03/2011

    * @author Desenvolvedor: Matheus Figueredo

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMPagamento extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMPagamento()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaPagamentos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaPagamentos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPagamentos()
    {
        $stSql = " SELECT *
        FROM (
        SELECT DISTINCT(empenho.cod_empenho)::varchar AS cod_empenho                                                                        -- Código do empenho
             , empenho.exercicio                                                    AS ano                          -- Ano do empenho para a despesa
             , lpad(despesa.num_orgao::varchar, 4, '0')||lpad(despesa.num_unidade::varchar, 2, '0')   AS cod_unidade_orcamentaria     -- Unidado Orçamentária
             , (pagamento.pago - pagamento.estornado)                               AS valor_pagamento              --Valor do pagamento
             , to_char(pagamento.timestamp, 'YYYYMMDD')                             AS data_pagamento_vencimento    -- Data de pagamento
             , coalesce(lpad(debito.banco,4,'0'), '')                               AS cod_banco_pagamento          -- Código do Banco onde ocorrerá o débito
             , LPAD(REPLACE(REPLACE(LOWER(debito.agencia),'-',''),'x','0'),6,'0')   AS cod_agencia_pagamento
             , coalesce(debito.conta, '')                                           AS conta_pagamento              -- Número da conta corrente que será debitada
             , debito.cod_entidade                                                  AS entidade_c1
             , coalesce(lpad(debito2.banco,4,'0'), '')                              AS cod_banco_pagamento2         -- Código do Banco (2) onde ocorrerá o débito
             , LPAD(REPLACE(REPLACE(LOWER(debito2.agencia),'-',''),'x','0'),6,'0')  AS cod_agencia_pagamento2
             , coalesce(debito2.conta, '')                                          AS conta_pagamento2             -- Número da conta corrente (2) que será debitada
             , debito2.cod_entidade                                                 AS entidade_c2
             , coalesce(lpad(debito3.banco,4,'0'), '')                              AS cod_banco_pagamento3         -- Código do Banco (3) onde ocorrerá o débito
             , LPAD(REPLACE(REPLACE(LOWER(debito3.agencia),'-',''),'x','0'),6,'0')  AS cod_agencia_pagamento3
             , coalesce(debito3.conta, '')                                          AS conta_pagamento3             -- Número da conta corrente (3) que será debitada
             , debito3.cod_entidade                                                 AS entidade_c3
             , ''                                                                   AS cod_banco_recebimento        -- Código do Banco onde ocorrerá o crédito
             , ''                                                                   AS cod_agencia_recebimento      -- Código da Agência onde ocorrerá o crédito
             , ''                                                                   AS conta_recebimento            -- Número da conta corrente que será creditada
             , to_char(pagamento.dt_vencimento, 'YYYYMMDD')                         AS data_exigibilidade           -- Data da exigibilidade do pagamento da despesa
             , ''                                                                   AS data_justificativa           -- Data da publicação da Justificativa da quebra de Ordem Cronológica
             , 0                                                                    AS cod_empenho_incorporado
          FROM empenho.empenho
    INNER JOIN empenho.pre_empenho
            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
           AND pre_empenho.exercicio       = empenho.exercicio
    INNER JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
    INNER JOIN orcamento.despesa
            ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           AND despesa.exercicio   = pre_empenho_despesa.exercicio
          JOIN (
                   SELECT interna.cod_empenho
                        , interna.exercicio
                        , interna.cod_entidade
                        , interna.dt_vencimento
                        , SUM(interna.pago)         AS pago
                        , SUM(interna.estornado)    AS estornado
                        , interna.timestamp
                     FROM (
                                SELECT empenho.cod_empenho
                                     , empenho.exercicio
                                     , empenho.cod_entidade
                                     , nota_liquidacao.dt_vencimento
                                     , SUM(coalesce(nota_liquidacao_paga.vl_pago , 0.00))           AS pago
                                     , SUM(coalesce(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS estornado
                                     , nota_liquidacao_paga.timestamp::date                         AS timestamp
                                     , nota_liquidacao_paga_anulada.timestamp_anulada::date         AS ts_anulado
                                  FROM empenho.empenho
                            INNER JOIN empenho.nota_liquidacao
                                    ON nota_liquidacao.exercicio_empenho    = empenho.exercicio
                                   AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                                   AND nota_liquidacao.cod_empenho  = empenho.cod_empenho
                            INNER JOIN empenho.nota_liquidacao_paga
                                    ON nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                                   AND nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                                   AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                             LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                    ON nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                   AND nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                   AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
                                   AND EXTRACT(month FROM nota_liquidacao_paga_anulada.timestamp_anulada::date) =  '".$this->getDado('inMes')."'
                                 WHERE to_char(nota_liquidacao_paga.timestamp, 'mm') =  '".$this->getDado('inMes')."'
                                   AND empenho.cod_entidade IN   (".$this->getDado('stEntidades').")
                                   AND empenho.exercicio = '".$this->getDado('exercicio')."'
                              GROUP BY empenho.cod_empenho
                                     , empenho.exercicio
                                     , empenho.cod_entidade
                                     , nota_liquidacao.dt_vencimento
                                     , nota_liquidacao_paga.timestamp
                                     , nota_liquidacao_paga_anulada.timestamp_anulada
                          ) AS interna
                 GROUP BY interna.cod_empenho
                        , interna.exercicio
                        , interna.cod_entidade
                        , interna.dt_vencimento
                        , interna.timestamp
               ) as pagamento
            ON pagamento.cod_empenho  = empenho.cod_empenho
           AND pagamento.exercicio    = empenho.exercicio
           AND pagamento.cod_entidade = empenho.cod_entidade
     LEFT JOIN (
                     SELECT *
                       FROM fn_contas_pagamento('".$this->getDado('stEntidades')."', '".$this->getDado('exercicio')."')
                      WHERE row_number   = 1
               ) as debito
            ON debito.cod_empenho  = empenho.cod_empenho
           AND debito.cod_entidade = empenho.cod_entidade
           AND debito.exercicio    = empenho.exercicio
     LEFT JOIN (
                     SELECT *
                       FROM fn_contas_pagamento('".$this->getDado('stEntidades')."', '".$this->getDado('exercicio')."')
                      WHERE row_number   = 2
               ) as debito2
            ON debito2.cod_empenho  = empenho.cod_empenho
           AND debito2.cod_entidade = empenho.cod_entidade
           AND debito2.exercicio    = empenho.exercicio
     LEFT JOIN (
                     SELECT *
                       FROM fn_contas_pagamento('".$this->getDado('stEntidades')."', '".$this->getDado('exercicio')."')
                      WHERE row_number   = 3
               ) as debito3
            ON debito3.cod_empenho  = empenho.cod_empenho
           AND debito3.cod_entidade = empenho.cod_entidade
           AND debito3.exercicio    = empenho.exercicio
         WHERE (pagamento.pago - pagamento.estornado) > 0
           AND empenho.exercicio   = '".$this->getDado('exercicio')."'
           AND empenho.cod_entidade IN   (".$this->getDado('stEntidades').") ";

 $stSql .= "  GROUP BY empenho.exercicio
             , despesa.num_orgao
             , despesa.num_unidade
             , pagamento.timestamp
             , empenho.cod_empenho
             , valor_pagamento
             , cod_banco_pagamento
             , cod_agencia_pagamento
             , conta_pagamento
             , entidade_c1
             , cod_banco_pagamento2
             , cod_agencia_pagamento2
             , conta_pagamento2
             , entidade_c2
             , cod_banco_pagamento3
             , cod_agencia_pagamento3
             , conta_pagamento3
             , entidade_c3
             , cod_banco_recebimento
             , cod_banco_recebimento
             , conta_recebimento
             , data_exigibilidade
             , data_justificativa \n";

      if ($this->getDado('boIncorporarEmpenhos')) {
        $stSql .= " UNION
        SELECT DISTINCT(empenho_incorporacao.descricao)                      AS cod_empenho
             , empenho.exercicio                                                    AS ano
             , lpad(despesa.num_orgao::varchar, 4, '0')||lpad(despesa.num_unidade::varchar, 2, '0')   AS cod_unidade_orcamentaria
             , SUM(pagamento.pago - pagamento.estornado)                            AS valor_pagamento
             , to_char((empenho.exercicio||'-12-31')::date, 'YYYYMMDD')             AS data_pagamento_vencimento
             , coalesce(lpad(debito.banco,4,'0'), '')                               AS cod_banco_pagamento
             , LPAD(REPLACE(REPLACE(LOWER(debito.agencia),'-',''),'x','0'),6,'0')   AS cod_agencia_pagamento
             , coalesce(debito.conta, '')                                           AS conta_pagamento
             , debito.cod_entidade                                                  AS entidade_c1
             , coalesce(lpad(debito2.banco,4,'0'), '')                              AS cod_banco_pagamento2
             , LPAD(REPLACE(REPLACE(LOWER(debito2.agencia),'-',''),'x','0'),6,'0')  AS cod_agencia_pagamento2
             , coalesce(debito2.conta, '')                                          AS conta_pagamento2
             , debito2.cod_entidade                                                 AS entidade_c2
             , coalesce(lpad(debito3.banco,4,'0'), '')                              AS cod_banco_pagamento3
             , LPAD(REPLACE(REPLACE(LOWER(debito3.agencia),'-',''),'x','0'),6,'0')  AS cod_agencia_pagamento3
             , coalesce(debito3.conta, '')                                          AS conta_pagamento3
             , debito3.cod_entidade                                                 AS entidade_c3
             , ''                                                                   AS cod_banco_recebimento
             , ''                                                                   AS cod_agencia_recebimento
             , ''                                                                   AS conta_recebimento
             , to_char((empenho.exercicio||'-12-31')::date, 'YYYYMMDD')             AS data_exigibilidade
             , ''                                                                   AS data_justificativa
             , empenho_incorporacao.cod_empenho_incorporado
          FROM empenho.empenho
    INNER JOIN empenho.pre_empenho
            ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
           AND pre_empenho.exercicio       = empenho.exercicio
    INNER JOIN empenho.pre_empenho_despesa
            ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
    INNER JOIN orcamento.despesa
            ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
           AND despesa.exercicio   = pre_empenho_despesa.exercicio
    INNER JOIN tceam.empenho_incorporacao
            ON empenho.cod_empenho  = empenho_incorporacao.cod_empenho
           AND empenho.cod_entidade = empenho_incorporacao.cod_entidade
           AND empenho.exercicio    = empenho_incorporacao.exercicio
          JOIN (
                   SELECT interna.cod_empenho
                        , interna.exercicio
                        , interna.cod_entidade
                        , interna.dt_vencimento
                        , SUM(interna.pago)         AS pago
                        , SUM(interna.estornado)    AS estornado
                        , interna.timestamp
                     FROM (
                                SELECT empenho.cod_empenho
                                     , empenho.exercicio
                                     , empenho.cod_entidade
                                     , nota_liquidacao.dt_vencimento
                                     , SUM(coalesce(nota_liquidacao_paga.vl_pago , 0.00))           AS pago
                                     , SUM(coalesce(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS estornado
                                     , nota_liquidacao_paga.timestamp::date                         AS timestamp
                                     , nota_liquidacao_paga_anulada.timestamp_anulada::date         AS ts_anulado
                                  FROM empenho.empenho
                            INNER JOIN empenho.nota_liquidacao
                                    ON nota_liquidacao.exercicio_empenho    = empenho.exercicio
                                   AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                                   AND nota_liquidacao.cod_empenho  = empenho.cod_empenho
                            INNER JOIN empenho.nota_liquidacao_paga
                                    ON nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                                   AND nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                                   AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                             LEFT JOIN empenho.nota_liquidacao_paga_anulada
                                    ON nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                   AND nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                   AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                   AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
                                 WHERE empenho.cod_entidade IN   (".$this->getDado('stCodEntidadesIncorporadas').")
                                   AND empenho.exercicio = '".$this->getDado('exercicio')."'
                              GROUP BY empenho.cod_empenho
                                     , empenho.exercicio
                                     , empenho.cod_entidade
                                     , nota_liquidacao.dt_vencimento
                                     , nota_liquidacao_paga.timestamp
                                     , nota_liquidacao_paga_anulada.timestamp_anulada
                          ) AS interna
                 GROUP BY interna.cod_empenho
                        , interna.exercicio
                        , interna.cod_entidade
                        , interna.dt_vencimento
                        , interna.timestamp
               ) as pagamento
            ON pagamento.cod_empenho  = empenho.cod_empenho
           AND pagamento.exercicio    = empenho.exercicio
           AND pagamento.cod_entidade = empenho.cod_entidade
     LEFT JOIN (
                     SELECT *
                       FROM fn_contas_pagamento('".$this->getDado('stCodEntidadesIncorporadas')."', '".$this->getDado('exercicio')."')
                      WHERE row_number   = 1
               ) as debito
            ON debito.cod_empenho  = empenho.cod_empenho
           AND debito.cod_entidade = empenho.cod_entidade
           AND debito.exercicio    = empenho.exercicio
     LEFT JOIN (
                     SELECT *
                       FROM fn_contas_pagamento('".$this->getDado('stCodEntidadesIncorporadas')."', '".$this->getDado('exercicio')."')
                      WHERE row_number   = 2
               ) as debito2
            ON debito2.cod_empenho  = empenho.cod_empenho
           AND debito2.cod_entidade = empenho.cod_entidade
           AND debito2.exercicio    = empenho.exercicio
     LEFT JOIN (
                     SELECT *
                       FROM fn_contas_pagamento('".$this->getDado('stCodEntidadesIncorporadas')."', '".$this->getDado('exercicio')."')
                      WHERE row_number   = 3
               ) as debito3
            ON debito3.cod_empenho  = empenho.cod_empenho
           AND debito3.cod_entidade = empenho.cod_entidade
           AND debito3.exercicio    = empenho.exercicio
         WHERE (pagamento.pago - pagamento.estornado) > 0
           AND empenho.exercicio   = '".$this->getDado('exercicio')."'
           AND empenho.cod_entidade IN   (".$this->getDado('stCodEntidadesIncorporadas').")
      GROUP BY empenho.exercicio
             , despesa.num_orgao
             , despesa.num_unidade
             , data_pagamento_vencimento
             , empenho_incorporacao.cod_empenho_incorporado
             , empenho_incorporacao.descricao
             , cod_banco_pagamento
             , cod_agencia_pagamento
             , conta_pagamento
             , entidade_c1
             , cod_banco_pagamento2
             , cod_agencia_pagamento2
             , conta_pagamento2
             , entidade_c2
             , cod_banco_pagamento3
             , cod_agencia_pagamento3
             , conta_pagamento3
             , entidade_c3
             , cod_banco_recebimento
             , cod_banco_recebimento
             , conta_recebimento
             , data_exigibilidade
             , data_justificativa
        ";
      }

      $stSql .= " ) as tabela
        ORDER BY cod_empenho_incorporado
               , substr(cod_empenho, position('.' IN cod_empenho)+1)::integer";

        return $stSql;

    }
}
