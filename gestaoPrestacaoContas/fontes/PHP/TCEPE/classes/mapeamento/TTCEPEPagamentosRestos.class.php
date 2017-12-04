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
    * Data de Criação   : 07/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPEPagamentosRestos.class.php 60386 2014-10-16 19:03:08Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEPagamentosRestos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPEPagamentosRestos()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        $stSql = "
               SELECT empenho.cod_empenho AS num_empenho
                    , empenho.exercicio
                    , empenho.cod_entidade
                    , (''||empenho.cod_entidade||empenho.cod_empenho||empenho.exercicio) AS cod_empenho
                    , LPAD((LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),10,'0') AS unidade_orcamentaria
                    , nota_liquidacao_paga.valor
                    , TO_CHAR(TO_DATE(nota_liquidacao_paga.timestamp::varchar, 'YYYY-MM-DD'),'ddmmyyyy') AS dt_pagamento
                    , 0 AS num_parcela
                   
                FROM ( SELECT nlp.vl_pago as valor
                            , nl.exercicio
                            , nl.cod_nota
                            , nl.cod_entidade
                            , nlp.timestamp
                         FROM empenho.nota_liquidacao as nl
                   INNER JOIN empenho.nota_liquidacao_paga as nlp
                           ON nlp.exercicio    = nl.exercicio
                          AND nlp.cod_entidade = nl.cod_entidade
                          AND nlp.cod_nota     = nl.cod_nota
                   INNER JOIN empenho.nota_liquidacao_item as nli
                           ON nl.exercicio    = nli.exercicio
                          AND nl.cod_nota     = nli.cod_nota
                          AND nl.cod_entidade = nli.cod_entidade
                    LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia
                           ON nlia.exercicio       = nli.exercicio
                          AND nlia.cod_nota        = nli.cod_nota
                          AND nlia.num_item        = nli.num_item
                          AND nlia.exercicio_item  = nli.exercicio_item
                          AND nlia.cod_pre_empenho = nli.cod_pre_empenho
                          AND nlia.cod_entidade    = nli.cod_entidade

                    WHERE TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN
                                   TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                               AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                      AND nl.cod_entidade IN (".$this->getDado('cod_entidade').")
                 ORDER BY nl.cod_nota, nl.cod_entidade
                     ) AS nota_liquidacao_paga

            INNER JOIN empenho.nota_liquidacao
                    ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                   AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
            INNER JOIN empenho.empenho
                    ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   AND empenho.cod_empenho = nota_liquidacao.cod_empenho
            INNER JOIN  (  SELECT  pre_empenho.exercicio
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
                    ON despesa.exercicio = empenho.exercicio
                   AND despesa.cod_pre_empenho = empenho.cod_pre_empenho
            INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                    ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                   AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                   AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                   AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
                       
                 WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                   AND empenho.exercicio<'".$this->getDado('exercicio')."' 
              ORDER BY empenho.exercicio,empenho.cod_empenho, empenho.cod_entidade  
        ";
        return $stSql;
    }
}

?>