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
    * Data de Criação: 15/04/2008

    * @author Analista: Gelson W
    * @author Desenvolvedor: Luiz Felipe P Teixeira
    * @ignore

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBRetencaoResto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBRetencaoResto()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function montaRecuperaTodos()
{

    $stSql = "                                                                                                                       
        SELECT empenho.exercicio AS exercicio_empenho
             , LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria
             , empenho.cod_empenho
             , tc.numero_pagamento_empenho( nota_liquidacao_paga.exercicio ,nota_liquidacao_paga.cod_entidade ,nota_liquidacao_paga.cod_nota ,nota_liquidacao_paga.timestamp) AS num_parcela
             , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
             , 5 AS tipo_retencao

          FROM empenho.nota_liquidacao_paga
  
    INNER JOIN ( 
                 SELECT exercicio
                      , cod_nota
                      , cod_entidade
                      , MAX(TIMESTAMP) AS TIMESTAMP
                   FROM empenho.nota_liquidacao_paga
               GROUP BY exercicio
                       , cod_nota
                       , cod_entidade

               ) AS max_paga
	      ON max_paga.exercicio    = nota_liquidacao_paga.exercicio
	     AND max_paga.cod_nota     = nota_liquidacao_paga.cod_nota
	     AND max_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
	     AND max_paga.timestamp    = nota_liquidacao_paga.timestamp

       LEFT JOIN ( SELECT nota_liquidacao_paga_anulada.*
                     FROM empenho.nota_liquidacao_paga_anulada

               INNER JOIN ( 
                            SELECT exercicio
                                 , cod_nota
                                 , cod_entidade
                                 , MAX(timestamp_anulada) AS timestamp_anulada
                              FROM empenho.nota_liquidacao_paga_anulada
                          GROUP BY exercicio
                                  , cod_nota
                                  , cod_entidade
                          ) AS max_anulada
                         ON max_anulada.exercicio    =  nota_liquidacao_paga_anulada.exercicio
                        AND max_anulada.cod_nota     =  nota_liquidacao_paga_anulada.cod_nota
                        AND max_anulada.cod_entidade =  nota_liquidacao_paga_anulada.cod_entidade
                        
                 ) AS nota_liquidacao_paga_anulada
                ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
	       AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
	       AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
	       AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
	       AND nota_liquidacao_paga_anulada.vl_anulado IS NULL


	INNER JOIN empenho.nota_liquidacao
	        ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
	       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
	       AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

        INNER JOIN empenho.empenho
	        ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
	       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
	       AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

	INNER JOIN empenho.pre_empenho
	        ON pre_empenho.exercicio       = empenho.exercicio
	       AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

	INNER JOIN empenho.pre_empenho_despesa
	        ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
	       AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

	INNER JOIN orcamento.despesa
	        ON despesa.exercicio   = pre_empenho_despesa.exercicio
	       AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

	INNER JOIN empenho.pagamento_liquidacao
	        ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
	       AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
	       AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota

	INNER JOIN empenho.ordem_pagamento
	        ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
	       AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
	       AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem

	INNER JOIN empenho.ordem_pagamento_retencao
	        ON ordem_pagamento_retencao.exercicio    = ordem_pagamento.exercicio
	       AND ordem_pagamento_retencao.cod_entidade = ordem_pagamento.cod_entidade
	       AND ordem_pagamento_retencao.cod_ordem    = ordem_pagamento.cod_ordem

	     WHERE 1 = 1
               AND nota_liquidacao_paga.timestamp BETWEEN TO_DATE('01/".$this->getDado('inMes')."/".$this->getDado('exercicio')."', 'DD/MM/YYYY')::TIMESTAMP AND (TO_DATE('01/".$this->getDado('inMes')."/".$this->getDado('exercicio')."', 'DD/MM/YYYY') + INTERVAL '1 MONTH')
               AND TO_CHAR(ordem_pagamento.dt_emissao, 'YYYY') <= '".$this->getDado('exercicio')."' ";
               
        if ( $this->getDado('stEntidades') ) {
            $stSql .= " AND ordem_pagamento_retencao.cod_entidade IN (".$this->getDado('stEntidades').") ";
        }

    $stSql .= "              
          GROUP BY exercicio_empenho
                 , unidade_orcamentaria
                 , empenho.cod_empenho
                 , num_parcela
                 , tipo_retencao
                 , empenho.exercicio ";
                                                                                                                         
    return $stSql;
}
}
