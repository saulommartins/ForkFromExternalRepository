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
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPEPagamentos.class.php 60218 2014-10-07 16:28:42Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEPagamentos extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEPagamentos()
    {
        parent::Persistente();
    }

    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaPagamento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPagamento()
    {
        $stSql = "  SELECT empenho.cod_empenho AS num_empenho
			             , empenho.exercicio
			             , empenho.cod_entidade
                         , (''||empenho.cod_entidade||empenho.cod_empenho||empenho.exercicio) AS cod_empenho
			             , LPAD((LPAD(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),10,'0') AS unidade_orcamentaria
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
                                        TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy')
                                    AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
				           AND nl.cod_entidade IN (".$this->getDado('stEntidades').")
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
                 INNER JOIN empenho.pre_empenho
                         ON pre_empenho.exercicio       = empenho.exercicio
                        AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                 INNER JOIN empenho.pre_empenho_despesa
                         ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                        AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                 INNER JOIN orcamento.despesa
                         ON despesa.exercicio   = pre_empenho_despesa.exercicio
                        AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                 INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                         ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                        AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                        AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
                            
                      WHERE empenho.cod_entidade IN (".$this->getDado('stEntidades').")
                        AND empenho.exercicio='".$this->getDado('stExercicio')."' 
                   ORDER BY empenho.cod_empenho, empenho.cod_entidade                  
                ";
        return $stSql;
    }

}
?>