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
    * Data de Criação: 13/10/2014
    * @author Analista: 
    * @author Desenvolvedor:
    *
    $Id: TTCEPEItemPagamentosRestos.class.php 60579 2014-10-31 12:56:40Z michel $
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEPEItemPagamentosRestos extends Persistente
{

    function montaRecuperaTodos()
    {
        $stSql ="   SELECT
                              nota_liquidacao.exercicio_empenho AS ano_empenho
                            , lpad(despesa.num_orgao::VARCHAR,2,'0') || lpad(despesa.num_unidade::VARCHAR,2,'0') || '' as unidade_orcamentaria
                            , nota_liquidacao.cod_empenho AS num_empenho
                            , tc.numero_pagamento_empenho( nota_liquidacao_paga.exercicio, nota_liquidacao_paga.cod_entidade, nota_liquidacao_paga.cod_nota, nota_liquidacao_paga.timestamp) AS num_parcela
                            , nota_liquidacao_paga.vl_pago AS valor_pagamento
                            , lpad(regexp_replace(debito.conta_corrente,'[.|-]','','gi'),12,'0') as conta_bancaria_debito
                            , substring(substr(nota_liquidacao_paga.observacao, length(nota_liquidacao_paga.observacao)-5,6), 'Y*([0-9]{1,6})') as num_cheque
                            , ''::VARCHAR AS num_doc_deb_automatico            
                            , ''::VARCHAR as cod_banco_credito
                            , ''::VARCHAR as cod_agencia_credito             
                            , ''::VARCHAR as conta_bancaria_credito
                            , despesa.cod_fonte
                            , debito.cod_tipo_conta_banco
                            , pagamento.sequencia
                    FROM
                    (
                        SELECT pagamento.*
                            FROM contabilidade.pagamento
                        WHERE  NOT EXISTS ( SELECT  1
                                                FROM contabilidade.pagamento_estorno
                                            WHERE pagamento_estorno.exercicio  = pagamento.exercicio
                                            AND pagamento_estorno.cod_entidade = pagamento.cod_entidade
                                            AND pagamento_estorno.cod_lote     = pagamento.cod_lote
                                            AND pagamento_estorno.tipo         = pagamento.tipo
                                            AND pagamento_estorno.sequencia    = pagamento.sequencia
                                        )
                    ) AS pagamento

                    JOIN empenho.nota_liquidacao_paga
                         ON nota_liquidacao_paga.exercicio    = pagamento.exercicio_liquidacao
                        AND nota_liquidacao_paga.cod_entidade = pagamento.cod_entidade
                        AND nota_liquidacao_paga.cod_nota     = pagamento.cod_nota
                        AND nota_liquidacao_paga.timestamp    = pagamento.timestamp
                    
                    JOIN empenho.nota_liquidacao
                         ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                        AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

                    JOIN  empenho.empenho
                         ON empenho.cod_entidade = nota_liquidacao.cod_entidade
                        AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                        AND empenho.exercicio    = nota_liquidacao.exercicio_empenho
                    
                    JOIN  ( SELECT  pre_empenho.exercicio
                                    , pre_empenho.cod_pre_empenho
                                    , CASE WHEN ( pre_empenho.implantado = true ) THEN 
                                            restos_pre_empenho.num_orgao
                                        ELSE 
                                            despesa.num_orgao
                                    END as num_orgao
                                    , CASE WHEN ( pre_empenho.implantado = true ) THEN 
                                            restos_pre_empenho.num_unidade
                                        ELSE 
                                            despesa.num_unidade
                                    END as num_unidade
                                    , codigo_fonte_recurso.cod_fonte
                            FROM  empenho.pre_empenho
                            LEFT JOIN  empenho.restos_pre_empenho
                                 ON  restos_pre_empenho.exercicio = pre_empenho.exercicio
                                AND  restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                            LEFT JOIN  empenho.pre_empenho_despesa
                                 ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                                AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                            LEFT JOIN  orcamento.despesa
                                 ON  despesa.exercicio = pre_empenho_despesa.exercicio
                                AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                            LEFT JOIN orcamento.recurso
                                 ON recurso.exercicio    = despesa.exercicio
                                AND recurso.cod_recurso = despesa.cod_recurso
                            LEFT JOIN tcepe.codigo_fonte_recurso
                                 ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                                AND codigo_fonte_recurso.exercicio  = recurso.exercicio
                    ) as despesa
                         ON  despesa.exercicio = empenho.exercicio
                        AND  despesa.cod_pre_empenho = empenho.cod_pre_empenho

                    JOIN  contabilidade.lancamento_empenho
                         ON  lancamento_empenho.cod_lote     = pagamento.cod_lote
                        AND  lancamento_empenho.tipo         = pagamento.tipo
                        AND  lancamento_empenho.sequencia    = pagamento.sequencia
                        AND  lancamento_empenho.exercicio    = pagamento.exercicio
                        AND  lancamento_empenho.cod_entidade = pagamento.cod_entidade
                    JOIN  contabilidade.lancamento
                         ON  lancamento.sequencia    = lancamento_empenho.sequencia
                        AND  lancamento.cod_lote     = lancamento_empenho.cod_lote
                        AND  lancamento.tipo         = lancamento_empenho.tipo
                        AND  lancamento.exercicio    = lancamento_empenho.exercicio
                        AND  lancamento.cod_entidade = lancamento_empenho.cod_entidade
                    JOIN  contabilidade.valor_lancamento
                         ON  valor_lancamento.cod_lote     = lancamento.cod_lote
                        AND  valor_lancamento.tipo         = lancamento.tipo
                        AND  valor_lancamento.sequencia    = lancamento.sequencia
                        AND  valor_lancamento.exercicio    = lancamento.exercicio
                        AND  valor_lancamento.cod_entidade = lancamento.cod_entidade
     
                    LEFT JOIN(  SELECT  conta_credito.cod_plano
                                        , conta_credito.cod_lote
                                        , conta_credito.sequencia
                                        , conta_credito.exercicio
                                        , conta_credito.tipo
                                        , conta_credito.cod_entidade
                                        , conta_credito.tipo_valor
                                        , plano_banco.conta_corrente
                                        , plano_banco.cod_conta_corrente
                                        , plano_banco_tipo_conta_banco.cod_tipo_conta_banco
                                FROM  contabilidade.plano_analitica
                                LEFT JOIN  contabilidade.plano_banco
                                     ON  plano_banco.cod_plano = plano_analitica.cod_plano
                                    AND  plano_banco.exercicio = plano_analitica.exercicio
                                LEFT JOIN tcepe.plano_banco_tipo_conta_banco
                                     ON plano_banco_tipo_conta_banco.exercicio  = plano_banco.exercicio
                                    AND plano_banco_tipo_conta_banco.cod_plano  = plano_banco.cod_plano
                                JOIN  contabilidade.conta_credito
                                     ON  conta_credito.cod_plano = plano_analitica.cod_plano
                                    AND  conta_credito.exercicio = plano_analitica.exercicio
                    ) as debito
                         ON  debito.cod_lote     = valor_lancamento.cod_lote
                        AND  debito.tipo         = valor_lancamento.tipo
                        AND  debito.sequencia    = valor_lancamento.sequencia
                        AND  debito.exercicio    = valor_lancamento.exercicio
                        AND  debito.cod_entidade = valor_lancamento.cod_entidade
                        AND  debito.tipo_valor   = 'C'

                    WHERE  nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
                      AND  valor_lancamento.exercicio = '".$this->getDado('exercicio')."'
                      AND  valor_lancamento.tipo_valor = 'D'
                      AND  valor_lancamento.cod_entidade in (".$this->getDado('cod_entidade').")                
                      AND  to_char(nota_liquidacao_paga.timestamp,'mm') = '".$this->getDado('mes')."'
                    
                 ORDER BY  nota_liquidacao.exercicio_empenho
                        ,  nota_liquidacao.cod_empenho
                        ,  num_parcela
        ";

        return $stSql;

    }

}
