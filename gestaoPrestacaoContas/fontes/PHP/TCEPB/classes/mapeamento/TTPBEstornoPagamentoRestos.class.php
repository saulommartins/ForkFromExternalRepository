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
/*
 * Arquivo de mapeamento da consulta do arquivo EstornoPagamentoRestos
 * Data de Criação   : 06/02/2009

 * @author Analista      Tonismar Regis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBEstornoPagamentoRestos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBEstornoPagamentoRestos()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
  $stSql = "
    SELECT * FROM (
            SELECT nota_liquidacao.exercicio_empenho AS ano_empenho
                 , lpad(despesa.num_orgao::text,2,'0') || lpad(despesa.num_unidade::text,2,'0') || '' as unidade_orcamentaria
                 , nota_liquidacao.cod_empenho AS num_empenho
                 , tc.numero_pagamento_empenho( nota_liquidacao_paga_anulada.exercicio, nota_liquidacao_paga_anulada.cod_entidade, nota_liquidacao_paga_anulada.cod_nota, nota_liquidacao_paga_anulada.timestamp) AS num_parcela
                 , TO_CHAR(nota_liquidacao_paga_anulada.timestamp,'dd/mm/yyyy') AS data_estorno
                 , nota_liquidacao_paga_anulada.observacao AS motivo
                 , 'S' AS liquidada
                 , nota_liquidacao_paga_anulada.vl_anulado AS valor_estornado

              FROM (
              SELECT pagamento.*
                        FROM contabilidade.pagamento
                       WHERE NOT EXISTS ( SELECT 1
                                            FROM contabilidade.pagamento_estorno
                                           WHERE pagamento_estorno.exercicio = pagamento.exercicio
                                             AND pagamento_estorno.cod_entidade = pagamento.cod_entidade
                                             AND pagamento_estorno.cod_lote = pagamento.cod_lote
                                             AND pagamento_estorno.tipo = pagamento.tipo
                                             AND pagamento_estorno.sequencia = pagamento.sequencia
                                        )
                    ) AS pagamento

        INNER JOIN empenho.nota_liquidacao_paga
                ON nota_liquidacao_paga.exercicio = pagamento.exercicio_liquidacao
               AND nota_liquidacao_paga.cod_entidade = pagamento.cod_entidade
               AND nota_liquidacao_paga.cod_nota = pagamento.cod_nota
               AND nota_liquidacao_paga.timestamp = pagamento.timestamp

        INNER JOIN empenho.nota_liquidacao
                ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
               AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
               AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota

        INNER JOIN empenho.nota_liquidacao_paga_anulada
                ON nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
               AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
               AND nota_liquidacao_paga_anulada.\"timestamp\" = nota_liquidacao_paga.\"timestamp\"
               AND nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio

        INNER JOIN empenho.empenho
                ON empenho.cod_entidade = nota_liquidacao.cod_entidade
               AND empenho.cod_empenho = nota_liquidacao.cod_empenho
               AND empenho.exercicio = nota_liquidacao.exercicio_empenho
        INNER JOIN ( SELECT pre_empenho.exercicio
                          , pre_empenho.cod_pre_empenho
                          , CASE WHEN ( pre_empenho.implantado = true )
                                  THEN restos_pre_empenho.num_orgao
                                  ELSE despesa.num_orgao
                             END as num_orgao
                          , CASE WHEN ( pre_empenho.implantado = true )
                                  THEN restos_pre_empenho.num_unidade
                                  ELSE despesa.num_unidade
                            END as num_unidade
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
                   ) as despesa
                ON despesa.exercicio = empenho.exercicio
               AND despesa.cod_pre_empenho = empenho.cod_pre_empenho

        INNER JOIN contabilidade.lancamento_empenho
                ON lancamento_empenho.cod_lote = pagamento.cod_lote
               AND lancamento_empenho.tipo = pagamento.tipo
               AND lancamento_empenho.sequencia = pagamento.sequencia
               AND lancamento_empenho.exercicio = pagamento.exercicio
               AND lancamento_empenho.cod_entidade = pagamento.cod_entidade
        INNER JOIN contabilidade.lancamento
                ON lancamento.sequencia = lancamento_empenho.sequencia
               AND lancamento.cod_lote = lancamento_empenho.cod_lote
               AND lancamento.tipo = lancamento_empenho.tipo
               AND lancamento.exercicio = lancamento_empenho.exercicio
               AND lancamento.cod_entidade = lancamento_empenho.cod_entidade
        INNER JOIN contabilidade.valor_lancamento
                ON valor_lancamento.cod_lote = lancamento.cod_lote
               AND valor_lancamento.tipo = lancamento.tipo
               AND valor_lancamento.sequencia = lancamento.sequencia
               AND valor_lancamento.exercicio = lancamento.exercicio
               AND valor_lancamento.cod_entidade = lancamento.cod_entidade

             WHERE  nota_liquidacao.exercicio_empenho < '".$this->getDado('exercicio')."'
               AND  valor_lancamento.exercicio = '".$this->getDado('exercicio')."'
               AND  valor_lancamento.tipo_valor = 'D'
    ";
    if ( $this->getDado('stEntidades') ) {
        $stSql .= " AND valor_lancamento.cod_entidade in (".$this->getDado('stEntidades').") \n";
    }
    if ( $this->getDado('inMes') ) {
         $stSql .= " AND to_char(nota_liquidacao_paga_anulada.timestamp,'mm') = '".$this->getDado('inMes')."' \n";
    }
    $stSql .= "         ) AS estornos                                     \n";
    $stSql .= " ORDER BY estornos.num_empenho                             \n";
    $stSql .= "        , estornos.num_parcela                             \n";

    return $stSql;
}
}
