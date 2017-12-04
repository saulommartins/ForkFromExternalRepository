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
    * Data de Criação: 25/04/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBPagamentosRestos.class.php 59935 2014-09-22 19:08:49Z michel $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBPagamentosRestos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBPagamentosRestos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
  $stSql = "
        SELECT  nota_liquidacao.exercicio_empenho AS ano_empenho
             ,  lpad(despesa.num_orgao::VARCHAR,2,'0') || lpad(despesa.num_unidade::VARCHAR,2,'0') || '' as unidade_orcamentaria
             ,  nota_liquidacao.cod_empenho AS num_empenho
             ,  tc.numero_pagamento_empenho( nota_liquidacao_paga.exercicio, nota_liquidacao_paga.cod_entidade, nota_liquidacao_paga.cod_nota, nota_liquidacao_paga.timestamp) AS num_parcela
             ,  TO_CHAR(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') AS data_pagamento
             ,  nota_liquidacao_paga.vl_pago AS valor_pagamento

            ,lpad(CASE WHEN substr(trim(UPPER(nota_liquidacao_paga.observacao)),1,2) = 'CH'
                         THEN substring(substr(nota_liquidacao_paga.observacao, length(nota_liquidacao_paga.observacao)-5,6), 'Y*([0-9]{1,6})')
                         ELSE '0' END,6,'0') as num_cheque

             ,  '' AS num_doc_deb_automatico
             ,  CASE WHEN ( debito.conta_corrente is null )
                     THEN LPAD('0',12,'0')
                     ELSE replace(replace(debito.conta_corrente, '.', ''), '-', '')
                END as conta_bancaria_debito
             ,  CASE WHEN ( credito.num_banco is null )
                     THEN LPAD('0',3,'0')
                     ELSE credito.num_banco
                END as cod_banco_credito
             ,  CASE WHEN ( credito.num_agencia is null )
                     THEN LPAD('0',6,'0')
                     ELSE replace(replace(credito.num_agencia, '.',''), '-', '')
                END as cod_agencia_credito
             ,  CASE WHEN ( credito.conta_corrente is null )
                     THEN LPAD('0',12,'0')
                     ELSE replace(replace(credito.conta_corrente, '.',''), '-', '')
                END as conta_bancaria_credito
             ,  pagamento_origem_recursos_interna.cod_origem_recursos as origem_recurso
          FROM
          (

          SELECT  pagamento.*
                    FROM  contabilidade.pagamento
                   WHERE  NOT EXISTS ( SELECT  1
                                         FROM  contabilidade.pagamento_estorno
                                        WHERE  pagamento_estorno.exercicio = pagamento.exercicio
                                          AND  pagamento_estorno.cod_entidade = pagamento.cod_entidade
                                          AND  pagamento_estorno.cod_lote = pagamento.cod_lote
                                          AND  pagamento_estorno.tipo = pagamento.tipo
                                          AND  pagamento_estorno.sequencia = pagamento.sequencia
                                     )
                ) AS pagamento

    INNER JOIN  empenho.nota_liquidacao_paga
            ON  nota_liquidacao_paga.exercicio = pagamento.exercicio_liquidacao
           AND  nota_liquidacao_paga.cod_entidade = pagamento.cod_entidade

           AND  nota_liquidacao_paga.cod_nota = pagamento.cod_nota
           AND  nota_liquidacao_paga.timestamp = pagamento.timestamp
    INNER JOIN  empenho.nota_liquidacao
            ON  nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
           AND  nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
           AND  nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota

    INNER JOIN  empenho.empenho
            ON  empenho.cod_entidade = nota_liquidacao.cod_entidade
           AND  empenho.cod_empenho = nota_liquidacao.cod_empenho
           AND  empenho.exercicio = nota_liquidacao.exercicio_empenho
    INNER JOIN  ( SELECT  pre_empenho.exercicio
                       ,  pre_empenho.cod_pre_empenho
                       ,  CASE WHEN ( pre_empenho.implantado = true )
                               THEN restos_pre_empenho.num_orgao
                               ELSE despesa.num_orgao
                          END as num_orgao
                       ,  CASE WHEN ( pre_empenho.implantado = true )
                               THEN restos_pre_empenho.num_unidade
                               ELSE despesa.num_unidade
                          END as num_unidade
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
                ) as despesa
            ON  despesa.exercicio = empenho.exercicio
           AND  despesa.cod_pre_empenho = empenho.cod_pre_empenho

    INNER JOIN  contabilidade.lancamento_empenho
            ON  lancamento_empenho.cod_lote = pagamento.cod_lote
           AND  lancamento_empenho.tipo = pagamento.tipo
           AND  lancamento_empenho.sequencia = pagamento.sequencia
           AND  lancamento_empenho.exercicio = pagamento.exercicio
           AND  lancamento_empenho.cod_entidade = pagamento.cod_entidade
    INNER JOIN  contabilidade.lancamento
            ON  lancamento.sequencia = lancamento_empenho.sequencia
           AND  lancamento.cod_lote = lancamento_empenho.cod_lote
           AND  lancamento.tipo = lancamento_empenho.tipo
           AND  lancamento.exercicio = lancamento_empenho.exercicio
           AND  lancamento.cod_entidade = lancamento_empenho.cod_entidade
    INNER JOIN  contabilidade.valor_lancamento
            ON  valor_lancamento.cod_lote = lancamento.cod_lote
           AND  valor_lancamento.tipo = lancamento.tipo
           AND  valor_lancamento.sequencia = lancamento.sequencia
           AND  valor_lancamento.exercicio = lancamento.exercicio
           AND  valor_lancamento.cod_entidade = lancamento.cod_entidade
     LEFT JOIN  (   SELECT  conta_debito.cod_plano
                         ,  conta_debito.cod_lote
                         ,  conta_debito.sequencia
                         ,  conta_debito.exercicio
                         ,  conta_debito.tipo
                         ,  conta_debito.cod_entidade
                         ,  conta_debito.tipo_valor
                         ,  plano_banco.conta_corrente
                         ,  plano_banco.cod_conta_corrente
                      FROM  contabilidade.plano_analitica
                 LEFT JOIN  contabilidade.plano_banco
                        ON  plano_banco.cod_plano = plano_analitica.cod_plano
                       AND  plano_banco.exercicio = plano_analitica.exercicio
                INNER JOIN  contabilidade.conta_debito
                        ON  conta_debito.cod_plano = plano_analitica.cod_plano
                       AND  conta_debito.exercicio = plano_analitica.exercicio
                ) as debito
            ON  debito.cod_lote = valor_lancamento.cod_lote
           AND  debito.tipo = valor_lancamento.tipo
           AND  debito.sequencia = valor_lancamento.sequencia
           AND  debito.exercicio = valor_lancamento.exercicio
           AND  debito.cod_entidade = valor_lancamento.cod_entidade
           AND  debito.tipo_valor = 'D'
     LEFT JOIN  (   SELECT  conta_credito.cod_plano
                         ,  conta_credito.cod_lote
                         ,  conta_credito.sequencia
                         ,  conta_credito.exercicio
                         ,  conta_credito.tipo
                         ,  conta_credito.cod_entidade
                         ,  conta_credito.tipo_valor
                         ,  plano_banco.conta_corrente
                         ,  banco.num_banco
                         ,  agencia.num_agencia
                      FROM  contabilidade.plano_analitica
                 LEFT JOIN  contabilidade.plano_banco
                        ON  plano_banco.cod_plano = plano_analitica.cod_plano
                       AND  plano_banco.exercicio = plano_analitica.exercicio
                INNER JOIN  contabilidade.conta_credito
                        ON  conta_credito.cod_plano = plano_analitica.cod_plano
                       AND  conta_credito.exercicio = plano_analitica.exercicio
                INNER JOIN  monetario.agencia
                        ON  agencia.cod_agencia = plano_banco.cod_agencia
                       AND  agencia.cod_banco = plano_banco.cod_banco
                INNER JOIN  monetario.banco
                        ON  banco.cod_banco = plano_banco.cod_banco
                ) AS credito
            ON  credito.cod_lote = valor_lancamento.cod_lote
           AND  credito.tipo = valor_lancamento.tipo
           AND  credito.sequencia = valor_lancamento.sequencia
           AND  credito.exercicio = valor_lancamento.exercicio
           AND  credito.cod_entidade = valor_lancamento.cod_entidade
           AND  credito.tipo_valor = 'C'

     LEFT JOIN  tcepb.pagamento_origem_recursos_interna
            ON  pagamento_origem_recursos_interna.cod_entidade = pagamento.cod_entidade
           AND  pagamento_origem_recursos_interna.exercicio = pagamento.exercicio_liquidacao
           AND  pagamento_origem_recursos_interna.timestamp = pagamento.timestamp
           AND  pagamento_origem_recursos_interna.cod_nota = pagamento.cod_nota

         WHERE  nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
           AND  valor_lancamento.exercicio = '".$this->getDado('exercicio')."'
           AND  valor_lancamento.tipo_valor = 'D'
    ";
    if ( $this->getDado('stEntidades') ) {
      $stSql .= " AND   valor_lancamento.cod_entidade in (".$this->getDado('stEntidades').")                    \n";
    }
    if ( $this->getDado('inMes') ) {
      $stSql .= " AND   to_char(nota_liquidacao_paga.timestamp,'mm') = '".$this->getDado('inMes')."'            \n";
    }

    return $stSql;
}
}
