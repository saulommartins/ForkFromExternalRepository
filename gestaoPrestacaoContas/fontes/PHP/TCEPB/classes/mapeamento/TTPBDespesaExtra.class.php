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
    * Data de Criação: 02/03/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBDespesaExtra extends Persistente
{
function montaRecuperaTodos()
{
    $stSql  = "
        SELECT *
          FROM ( SELECT RPAD(REPLACE(plano_debito.cod_estrutural,'.',''),15,'0') AS estrutural
                      , plano_debito.tipo AS tipo_registro
                      , plano_debito.cod_entidade
                      , REPLACE(ABS(SUM(COALESCE(plano_debito.vl_pago,0.00)))::VARCHAR,'.',',') AS valor
                      , TO_CHAR(plano_debito.timestamp_transferencia,'mmyyyy') AS dt_criacao
                   FROM (
                    ---------------------------------------------
                    --                   PAGAMENTOS EXTRA
                    ---------------------------------------------
                          SELECT plano_conta.cod_estrutural
                               , transferencia.timestamp_transferencia
                               , transferencia.cod_entidade
                               , SUM(coalesce(transferencia.valor,0.00)) as vl_pago
                               , '1' AS tipo
                            FROM tesouraria.transferencia
                    -- BUSCA CONTA DESPESA
                      INNER JOIN contabilidade.plano_analitica
                              ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                             AND transferencia.exercicio        = plano_analitica.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_analitica.cod_conta = plano_conta.cod_conta
                             AND plano_analitica.exercicio = plano_conta.exercicio
                           WHERE transferencia.cod_tipo = 1
                             AND TO_CHAR(transferencia.timestamp_transferencia,'mm') = '".$this->getDado('inMes')."'
                             AND TO_CHAR(transferencia.timestamp_transferencia,'yyyy') = '".$this->getDado('exercicio')."'
                        GROUP BY transferencia.exercicio
                               , transferencia.timestamp_transferencia
                               , transferencia.cod_entidade
                               , plano_conta.cod_estrutural

                       UNION ALL

                    ---------------------------------------------
                    --       ESTORNOS DE PAGAMENTOS EXTRA
                    ---------------------------------------------

                          SELECT plano_conta.cod_estrutural
                               , transferencia_estornada.timestamp_estornada
                               , transferencia.cod_entidade
                               , SUM(coalesce(transferencia_estornada.valor,0.00)) * (-1) AS vl_pago
                               , '2' AS tipo
                            FROM tesouraria.transferencia
                      INNER JOIN tesouraria.transferencia_estornada
                              ON transferencia_estornada.cod_entidade    = transferencia.cod_entidade
                             AND transferencia_estornada.tipo            = transferencia.tipo
                             AND transferencia_estornada.exercicio       = transferencia.exercicio
                             AND transferencia_estornada.cod_lote        = transferencia.cod_lote

                      -- BUSCA CONTA DESPESA
                      INNER JOIN contabilidade.plano_analitica
                              ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                             AND transferencia.exercicio        = plano_analitica.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_analitica.cod_conta = plano_conta.cod_conta
                             AND plano_analitica.exercicio = plano_conta.exercicio

                           WHERE transferencia.cod_tipo = 1
                             AND TO_CHAR(transferencia.timestamp_transferencia,'mm') = '".$this->getDado('inMes')."'
                             AND TO_CHAR(transferencia.timestamp_transferencia,'yyyy') = '".$this->getDado('exercicio')."'

                        GROUP BY transferencia_estornada.timestamp_estornada
                               , transferencia.cod_entidade
                               , plano_conta.cod_estrutural

                       UNION ALL

                    ---------------------------------------------
                    --                  PAGAMENTOS RESTOS
                    ---------------------------------------------
                          SELECT plano_conta.cod_estrutural
                               , plano.timestamp
                               , plano.cod_entidade
                               , vl_pago
                               , '1' AS tipo
                            FROM (
                                   SELECT tesouraria.pagamento.exercicio_plano as exercicio
                                        , SUM(COALESCE(nota_liquidacao_paga.vl_pago,0.00)) AS vl_pago
                                        , tesouraria.pagamento.cod_entidade
                                        , tesouraria.pagamento.timestamp
                                        , contabilidade.fn_recupera_conta_lancamento( contabilidade.pagamento.exercicio
                                                                                    , contabilidade.pagamento.cod_entidade
                                                                                    , contabilidade.pagamento.cod_lote
                                                                                    , contabilidade.pagamento.tipo
                                                                                    , contabilidade.pagamento.sequencia
                                                                                    , 'D') as cod_plano_debito
                                      FROM tesouraria.pagamento
                                INNER JOIN empenho.nota_liquidacao_paga
                                        ON nota_liquidacao_paga.cod_nota     = tesouraria.pagamento.cod_nota
                                       AND nota_liquidacao_paga.cod_entidade = tesouraria.pagamento.cod_entidade
                                       AND nota_liquidacao_paga.exercicio    = tesouraria.pagamento.exercicio
                                       AND nota_liquidacao_paga.timestamp    = tesouraria.pagamento.timestamp
                                INNER JOIN empenho.nota_liquidacao
                                        ON nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                                       AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                       AND nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
                                INNER JOIN contabilidade.pagamento
                                        ON contabilidade.pagamento.cod_entidade         = nota_liquidacao_paga.cod_entidade
                                       AND contabilidade.pagamento.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                       AND contabilidade.pagamento.cod_nota             = nota_liquidacao_paga.cod_nota
                                       AND contabilidade.pagamento.timestamp            = nota_liquidacao_paga.timestamp
                                     WHERE TO_CHAR(tesouraria.pagamento.timestamp,'mm') = '".$this->getDado('inMes')."'
                                       AND TO_CHAR(tesouraria.pagamento.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                                  GROUP BY tesouraria.pagamento.exercicio_plano
                                         , tesouraria.pagamento.cod_entidade
                                         , tesouraria.pagamento.timestamp
                                         , contabilidade.pagamento.exercicio
                                         , contabilidade.pagamento.cod_entidade
                                         , contabilidade.pagamento.tipo
                                         , contabilidade.pagamento.sequencia
                                         , contabilidade.pagamento.cod_lote
                                 ) as plano
                        INNER JOIN contabilidade.plano_analitica
                                ON plano.cod_plano_debito   = plano_analitica.cod_plano
                               AND plano.exercicio          = plano_analitica.exercicio
                        INNER JOIN contabilidade.plano_conta
                                ON plano_analitica.cod_conta = plano_conta.cod_conta
                               AND plano_analitica.exercicio = plano_conta.exercicio
                               AND plano_conta.cod_estrutural LIKE '2.1.2.1.1%'

                        UNION ALL

                    ---------------------------------------------
                    --       ESTORNOS DE PAGAMENTOS RESTOS
                    ---------------------------------------------
                          SELECT plano_conta.cod_estrutural
                               , plano.timestamp
                               , plano.cod_entidade
                               , vl_pago
                               , '1' AS tipo
                            FROM (
                                   SELECT tesouraria.pagamento.exercicio_plano as exercicio
                                        , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_pago
                                        , tesouraria.pagamento.cod_entidade
                                        , tesouraria.pagamento.timestamp
                                        , contabilidade.fn_recupera_conta_lancamento( contabilidade.pagamento.exercicio
                                                                                    , contabilidade.pagamento.cod_entidade
                                                                                    , contabilidade.pagamento.cod_lote
                                                                                    , contabilidade.pagamento.tipo
                                                                                    , contabilidade.pagamento.sequencia
                                                                                    , 'D') as cod_plano_debito
                                      FROM tesouraria.pagamento
                                INNER JOIN tesouraria.pagamento_estornado
                                        ON pagamento.exercicio    = pagamento_estornado.exercicio
                                       AND pagamento.cod_entidade = pagamento_estornado.cod_entidade
                                       AND pagamento.cod_nota     = pagamento_estornado.cod_nota
                                       AND pagamento.timestamp    = pagamento_estornado.timestamp
                                INNER JOIN empenho.nota_liquidacao_paga_anulada
                                        ON nota_liquidacao_paga_anulada.cod_nota     = tesouraria.pagamento.cod_nota
                                       AND nota_liquidacao_paga_anulada.cod_entidade = tesouraria.pagamento.cod_entidade
                                       AND nota_liquidacao_paga_anulada.exercicio    = tesouraria.pagamento.exercicio
                                       AND nota_liquidacao_paga_anulada.timestamp    = tesouraria.pagamento.timestamp
                                INNER JOIN empenho.nota_liquidacao
                                        ON nota_liquidacao.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
                                       AND nota_liquidacao.exercicio    = nota_liquidacao_paga_anulada.exercicio
                                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
                                       AND nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
                                INNER JOIN contabilidade.pagamento
                                        ON contabilidade.pagamento.cod_entidade         = nota_liquidacao_paga_anulada.cod_entidade
                                       AND contabilidade.pagamento.exercicio_liquidacao = nota_liquidacao_paga_anulada.exercicio
                                       AND contabilidade.pagamento.cod_nota             = nota_liquidacao_paga_anulada.cod_nota
                                       AND contabilidade.pagamento.timestamp            = nota_liquidacao_paga_anulada.timestamp
                                     WHERE TO_CHAR(tesouraria.pagamento.timestamp,'mm') = '".$this->getDado('inMes')."'
                                       AND TO_CHAR(tesouraria.pagamento.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                                  GROUP BY tesouraria.pagamento.exercicio_plano
                                         , tesouraria.pagamento.cod_entidade
                                         , tesouraria.pagamento.timestamp
                                         , contabilidade.pagamento.exercicio
                                         , contabilidade.pagamento.cod_entidade
                                         , contabilidade.pagamento.tipo
                                         , contabilidade.pagamento.sequencia
                                         , contabilidade.pagamento.cod_lote
                                 ) as plano
                        INNER JOIN contabilidade.plano_analitica
                                ON plano.cod_plano_debito   = plano_analitica.cod_plano
                               AND plano.exercicio          = plano_analitica.exercicio
                        INNER JOIN contabilidade.plano_conta
                                ON plano_analitica.cod_conta = plano_conta.cod_conta
                               AND plano_analitica.exercicio = plano_conta.exercicio
                               AND plano_conta.cod_estrutural LIKE '2.1.2.1.1%'
                        ) as plano_debito
                 GROUP BY plano_debito.cod_estrutural
                        , tipo
                        , TO_CHAR(plano_debito.timestamp_transferencia,'mmyyyy')
                        , cod_entidade
                ) as tabela
            WHERE cod_entidade = ".$this->getDado('stEntidades')."
              AND SUBSTR(estrutural,1,3) <> '512'";

        return $stSql;
}
}
