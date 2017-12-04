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
    * 
    * Data de Criação   : 08/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEDespesaExtra.class.php 60317 2014-10-13 19:48:24Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEDespesaExtra extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEDespesaExtra()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT *
                    FROM (  SELECT 
                                      REPLACE(plano_debito.cod_estrutural,'.','') as cod_estrutural
                                    , plano_debito.tipo AS tipo_registro
                                    , ABS(SUM(COALESCE(plano_debito.vl_pago,0.00))) AS valor
                                    , ".$this->getDado('unidade_gestora')." as unidade_gestora
                                    , plano_debito.cod_fonte as cod_fonte_recurso

                            FROM (
                                    ---------------------------------------------
                                    --                   PAGAMENTOS EXTRA
                                    ---------------------------------------------
                                    SELECT 
                                              plano_conta.cod_estrutural
                                            , transferencia.timestamp_transferencia
                                            , transferencia.cod_entidade
                                            , SUM(coalesce(transferencia.valor,0.00)) as vl_pago
                                            , '1' AS tipo
                                            , plano_analitica_relacionamento.cod_relacionamento
                                            , codigo_fonte_recurso.cod_fonte
                                    FROM tesouraria.transferencia
                                    
                                    -- BUSCA CONTA DESPESA
                                    JOIN contabilidade.plano_analitica
                                         ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                                        AND transferencia.exercicio        = plano_analitica.exercicio                      
                                    
                                    LEFT JOIN  tcepe.plano_analitica_relacionamento
                                         ON plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                                        AND plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                                        AND plano_analitica_relacionamento.tipo = 'D'
                                    
                                    JOIN contabilidade.plano_conta
                                         ON plano_analitica.cod_conta = plano_conta.cod_conta
                                        AND plano_analitica.exercicio = plano_conta.exercicio

                                    LEFT JOIN contabilidade.plano_recurso
                                         ON plano_recurso.exercicio = plano_analitica.exercicio
                                        AND plano_recurso.cod_plano = plano_analitica.cod_plano
                                    
                                    LEFT JOIN orcamento.recurso
                                         ON recurso.exercicio   = plano_recurso.exercicio
                                        AND recurso.cod_recurso = plano_recurso.cod_recurso
                                    
                                    LEFT JOIN tcepe.codigo_fonte_recurso
                                         ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                                        AND codigo_fonte_recurso.exercicio   = recurso.exercicio
            
                                    WHERE transferencia.cod_tipo = 1
                                    AND TO_CHAR(transferencia.timestamp_transferencia,'mm') = '".$this->getDado('mes')."'
                                    AND TO_CHAR(transferencia.timestamp_transferencia,'yyyy') = '".$this->getDado('exercicio')."'
                        
                                    GROUP BY transferencia.exercicio
                                            , transferencia.timestamp_transferencia
                                            , transferencia.cod_entidade
                                            , plano_conta.cod_estrutural
                                            , plano_analitica_relacionamento.cod_relacionamento
                                            , codigo_fonte_recurso.cod_fonte

                                UNION ALL

                                    ---------------------------------------------
                                    --       ESTORNOS DE PAGAMENTOS EXTRA
                                    ---------------------------------------------
                                    SELECT 
                                              plano_conta.cod_estrutural
                                            , transferencia_estornada.timestamp_estornada
                                            , transferencia.cod_entidade
                                            , SUM(coalesce(transferencia_estornada.valor,0.00)) * (-1) AS vl_pago
                                            , '2' AS tipo
                                            , plano_analitica_relacionamento.cod_relacionamento
                                            , codigo_fonte_recurso.cod_fonte
                                    FROM tesouraria.transferencia
                                    
                                    JOIN tesouraria.transferencia_estornada
                                         ON transferencia_estornada.cod_entidade    = transferencia.cod_entidade
                                        AND transferencia_estornada.tipo            = transferencia.tipo
                                        AND transferencia_estornada.exercicio       = transferencia.exercicio
                                        AND transferencia_estornada.cod_lote        = transferencia.cod_lote

                                    -- BUSCA CONTA DESPESA
                                    JOIN contabilidade.plano_analitica
                                         ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                                        AND transferencia.exercicio        = plano_analitica.exercicio
                                    
                                    LEFT JOIN  tcepe.plano_analitica_relacionamento
                                         ON plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                                        AND plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                                        AND plano_analitica_relacionamento.tipo = 'D'
                                    
                                    JOIN contabilidade.plano_conta
                                         ON plano_analitica.cod_conta = plano_conta.cod_conta
                                        AND plano_analitica.exercicio = plano_conta.exercicio

                                    LEFT JOIN contabilidade.plano_recurso
                                         ON plano_recurso.exercicio = plano_analitica.exercicio
                                        AND plano_recurso.cod_plano = plano_analitica.cod_plano
            
                                    LEFT JOIN orcamento.recurso
                                         ON recurso.exercicio   = plano_recurso.exercicio
                                        AND recurso.cod_recurso = plano_recurso.cod_recurso
            
                                    LEFT JOIN tcepe.codigo_fonte_recurso
                                         ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                                        AND codigo_fonte_recurso.exercicio   = recurso.exercicio

                                    WHERE transferencia.cod_tipo = 1
                                    AND TO_CHAR(transferencia.timestamp_transferencia,'mm') = '".$this->getDado('mes')."'
                                    AND TO_CHAR(transferencia.timestamp_transferencia,'yyyy') = '".$this->getDado('exercicio')."'

                                    GROUP BY transferencia_estornada.timestamp_estornada
                                            , transferencia.cod_entidade
                                            , plano_conta.cod_estrutural
                                            , plano_analitica_relacionamento.cod_relacionamento
                                            , codigo_fonte_recurso.cod_fonte

                                UNION ALL

                                    ---------------------------------------------
                                    --                  PAGAMENTOS RESTOS
                                    ---------------------------------------------
                                    SELECT 
                                              plano_conta.cod_estrutural
                                            , plano.timestamp
                                            , plano.cod_entidade
                                            , vl_pago
                                            , '1' AS tipo
                                            , plano_analitica_relacionamento.cod_relacionamento
                                            , codigo_fonte_recurso.cod_fonte
                                    FROM (
                                            SELECT 
                                                    tesouraria.pagamento.exercicio_plano as exercicio
                                                    , SUM(COALESCE(nota_liquidacao_paga.vl_pago,0.00)) AS vl_pago
                                                    , tesouraria.pagamento.cod_entidade
                                                    , tesouraria.pagamento.timestamp
                                                    , contabilidade.fn_recupera_conta_lancamento( contabilidade.pagamento.exercicio
                                                                                                , contabilidade.pagamento.cod_entidade
                                                                                                , contabilidade.pagamento.cod_lote
                                                                                                , contabilidade.pagamento.tipo
                                                                                                , contabilidade.pagamento.sequencia
                                                                                                , 'D'
                                                    ) as cod_plano_debito
                                            FROM tesouraria.pagamento
                                            JOIN empenho.nota_liquidacao_paga
                                                 ON nota_liquidacao_paga.cod_nota     = tesouraria.pagamento.cod_nota
                                                AND nota_liquidacao_paga.cod_entidade = tesouraria.pagamento.cod_entidade
                                                AND nota_liquidacao_paga.exercicio    = tesouraria.pagamento.exercicio
                                                AND nota_liquidacao_paga.timestamp    = tesouraria.pagamento.timestamp
                                            JOIN empenho.nota_liquidacao
                                                 ON nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
                                                AND nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                                AND nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
                                            JOIN contabilidade.pagamento
                                                 ON contabilidade.pagamento.cod_entidade         = nota_liquidacao_paga.cod_entidade
                                                AND contabilidade.pagamento.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                                AND contabilidade.pagamento.cod_nota             = nota_liquidacao_paga.cod_nota
                                                AND contabilidade.pagamento.timestamp            = nota_liquidacao_paga.timestamp
                                            
                                            WHERE TO_CHAR(tesouraria.pagamento.timestamp,'mm') = '".$this->getDado('mes')."'
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
                                    
                                    JOIN contabilidade.plano_analitica
                                         ON plano.cod_plano_debito   = plano_analitica.cod_plano
                                        AND plano.exercicio          = plano_analitica.exercicio
                                    
                                    LEFT JOIN  tcepe.plano_analitica_relacionamento
                                         ON plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                                        AND plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                                        AND plano_analitica_relacionamento.tipo = 'D'
                                    
                                    JOIN contabilidade.plano_conta
                                         ON plano_analitica.cod_conta = plano_conta.cod_conta
                                        AND plano_analitica.exercicio = plano_conta.exercicio

                                    LEFT JOIN contabilidade.plano_recurso
                                         ON plano_recurso.exercicio = plano_analitica.exercicio
                                        AND plano_recurso.cod_plano = plano_analitica.cod_plano
                                    
                                    LEFT JOIN orcamento.recurso
                                         ON recurso.exercicio   = plano_recurso.exercicio
                                        AND recurso.cod_recurso = plano_recurso.cod_recurso
                                    
                                    LEFT JOIN tcepe.codigo_fonte_recurso
                                         ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                                        AND codigo_fonte_recurso.exercicio   = recurso.exercicio

                                UNION ALL

                                    ---------------------------------------------
                                    --       ESTORNOS DE PAGAMENTOS RESTOS
                                    ---------------------------------------------
                                    SELECT 
                                              plano_conta.cod_estrutural
                                            , plano.timestamp
                                            , plano.cod_entidade
                                            , vl_pago
                                            , '2' AS tipo
                                            , plano_analitica_relacionamento.cod_relacionamento
                                            , codigo_fonte_recurso.cod_fonte
                                    FROM (
                                            SELECT 
                                                    tesouraria.pagamento.exercicio_plano as exercicio
                                                    , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0.00)) AS vl_pago
                                                    , tesouraria.pagamento.cod_entidade
                                                    , tesouraria.pagamento.timestamp
                                                    , contabilidade.fn_recupera_conta_lancamento( contabilidade.pagamento.exercicio
                                                                                                , contabilidade.pagamento.cod_entidade
                                                                                                , contabilidade.pagamento.cod_lote
                                                                                                , contabilidade.pagamento.tipo
                                                                                                , contabilidade.pagamento.sequencia
                                                                                                , 'D'
                                                    ) as cod_plano_debito
                                            FROM tesouraria.pagamento
                                            JOIN tesouraria.pagamento_estornado
                                                 ON pagamento.exercicio    = pagamento_estornado.exercicio
                                                AND pagamento.cod_entidade = pagamento_estornado.cod_entidade
                                                AND pagamento.cod_nota     = pagamento_estornado.cod_nota
                                                AND pagamento.timestamp    = pagamento_estornado.timestamp
                                            JOIN empenho.nota_liquidacao_paga_anulada
                                                ON nota_liquidacao_paga_anulada.cod_nota     = tesouraria.pagamento.cod_nota
                                               AND nota_liquidacao_paga_anulada.cod_entidade = tesouraria.pagamento.cod_entidade
                                               AND nota_liquidacao_paga_anulada.exercicio    = tesouraria.pagamento.exercicio
                                               AND nota_liquidacao_paga_anulada.timestamp    = tesouraria.pagamento.timestamp
                                            JOIN empenho.nota_liquidacao
                                                ON nota_liquidacao.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
                                               AND nota_liquidacao.exercicio    = nota_liquidacao_paga_anulada.exercicio
                                               AND nota_liquidacao.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
                                               AND nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
                                            JOIN contabilidade.pagamento
                                                ON contabilidade.pagamento.cod_entidade         = nota_liquidacao_paga_anulada.cod_entidade
                                               AND contabilidade.pagamento.exercicio_liquidacao = nota_liquidacao_paga_anulada.exercicio
                                               AND contabilidade.pagamento.cod_nota             = nota_liquidacao_paga_anulada.cod_nota
                                               AND contabilidade.pagamento.timestamp            = nota_liquidacao_paga_anulada.timestamp
                                     
                                            WHERE TO_CHAR(tesouraria.pagamento.timestamp,'mm') = '".$this->getDado('mes')."'
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

                                    JOIN contabilidade.plano_analitica
                                        ON plano.cod_plano_debito   = plano_analitica.cod_plano
                                       AND plano.exercicio          = plano_analitica.exercicio

                                    LEFT JOIN  tcepe.plano_analitica_relacionamento
                                         ON plano_analitica.cod_plano = plano_analitica_relacionamento.cod_plano
                                        AND plano_analitica.exercicio = plano_analitica_relacionamento.exercicio
                                        AND plano_analitica_relacionamento.tipo = 'D'

                                    JOIN contabilidade.plano_conta
                                         ON plano_analitica.cod_conta = plano_conta.cod_conta
                                        AND plano_analitica.exercicio = plano_conta.exercicio

                                    LEFT JOIN contabilidade.plano_recurso
                                         ON plano_recurso.exercicio = plano_analitica.exercicio
                                        AND plano_recurso.cod_plano = plano_analitica.cod_plano
                                    
                                    LEFT JOIN orcamento.recurso
                                         ON recurso.exercicio   = plano_recurso.exercicio
                                        AND recurso.cod_recurso = plano_recurso.cod_recurso
                                    
                                    LEFT JOIN tcepe.codigo_fonte_recurso
                                         ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                                        AND codigo_fonte_recurso.exercicio   = recurso.exercicio

                            ) as plano_debito

                        WHERE plano_debito.cod_entidade = ".$this->getDado('cod_entidade')."
                        AND substr(cod_estrutural,1,1) = '2'
                        GROUP BY  plano_debito.tipo
                                , plano_debito.cod_fonte 
                                , plano_debito.cod_estrutural
                    ) as tabela
              
        ";
        return $stSql;
    }
}

?>