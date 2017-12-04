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
    * Data de Criação   : 15/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Michel Teixeira
    $Id: TTCEPERetencaoRestos.class.php 60607 2014-11-03 17:41:56Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPERetencaoRestos extends Persistente
{
    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPERetencaoRestos()
    {
        parent::Persistente();
    }

    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaRetencaoRestos.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaRetencaoRestos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRetencaoRestos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRetencaoRestos()
    {
        $stSql = "  SELECT retencao.exercicio_empenho
                         , retencao.unidade_orcamentaria
                         , retencao.cod_empenho
                         , SUM(retencao.vl_retencao) AS vl_retencao
                         , retencao.tipo_retencao
                         , retencao.situacao
                         , 1 AS num_parcela
                    FROM(
                        SELECT  
                                empenho.exercicio AS exercicio_empenho
                                , LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria
                                , empenho.cod_empenho 
                                , nota_liquidacao_paga.vl_pago AS vl_retencao
                                , plano_analitica_tipo_retencao.cod_tipo AS tipo_retencao
                                , CASE WHEN COALESCE(PL.vl_pagamento,0.00) - COALESCE(OPLA.vl_anulado,0.00) = 0.00 
                                            THEN 'ANULADA'
                                        WHEN (COALESCE(nota_liquidacao_paga.vl_pago,0.00) - COALESCE(NLPA.vl_anulado,0.00)) = 0.00 
                                            THEN 'A PAGAR'
                                        WHEN (COALESCE(nota_liquidacao_paga.vl_pago,0.00) - COALESCE(NLPA.vl_anulado,0.00)) > 0.00 
                                            THEN 'PAGA'
                                 END AS situacao

                        FROM empenho.pre_empenho

                    JOIN empenho.empenho
                         ON empenho.exercicio = pre_empenho.exercicio
                        AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      
                    JOIN ( SELECT  pre_empenho.exercicio
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
                         ) AS despesa
                         ON despesa.exercicio = empenho.exercicio
                        AND despesa.cod_pre_empenho = empenho.cod_pre_empenho

                    JOIN empenho.nota_liquidacao
                         ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                        AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                        AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                       
                    JOIN empenho.nota_liquidacao_paga 
                         ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                        AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
        
                    LEFT JOIN empenho.nota_liquidacao_paga_anulada AS NLPA
                         ON NLPA.exercicio    = nota_liquidacao_paga.exercicio
                        AND NLPA.cod_nota     = nota_liquidacao_paga.cod_nota
                        AND NLPA.cod_entidade = nota_liquidacao_paga.cod_entidade
                        AND NLPA.timestamp    = nota_liquidacao_paga.timestamp
                       
                    JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                         ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                        AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                        AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                        AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
        
                    JOIN empenho.pagamento_liquidacao AS PL
                         ON PL.exercicio            = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                        AND PL.cod_entidade         = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                        AND PL.cod_ordem            = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                        AND PL.exercicio_liquidacao = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                        AND PL.cod_nota             = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
        
                    LEFT JOIN empenho.ordem_pagamento_liquidacao_anulada AS OPLA
                         ON OPLA.exercicio            = PL.exercicio
                        AND OPLA.cod_entidade         = PL.cod_entidade
                        AND OPLA.cod_ordem            = PL.cod_ordem
                        AND OPLA.exercicio_liquidacao = PL.exercicio_liquidacao
                        AND OPLA.cod_nota             = PL.cod_nota
                       
                    JOIN empenho.ordem_pagamento_retencao
                         ON ordem_pagamento_retencao.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                        AND ordem_pagamento_retencao.cod_ordem    = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                        AND ordem_pagamento_retencao.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                        AND ordem_pagamento_retencao.vl_retencao  = nota_liquidacao_paga.vl_pago
                       
                    JOIN tcepe.plano_analitica_tipo_retencao
                         ON plano_analitica_tipo_retencao.exercicio = ordem_pagamento_retencao.exercicio
                        AND plano_analitica_tipo_retencao.cod_plano = ordem_pagamento_retencao.cod_plano
                      
                    WHERE empenho.exercicio    < '".$this->getDado('exercicio')."'
                    AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                    AND TO_CHAR(nota_liquidacao_paga.timestamp,'mmyyyy') = '".$this->getDado('mes').$this->getDado('exercicio')."'
                       
                    GROUP BY  empenho.exercicio
                            , despesa.num_orgao
                            , despesa.num_unidade
                            , empenho.cod_empenho
                            , nota_liquidacao_paga.vl_pago
                            , plano_analitica_tipo_retencao.cod_tipo
                            , PL.vl_pagamento
                            , OPLA.vl_anulado
                            , ordem_pagamento_retencao.vl_retencao 
                            , NLPA.vl_anulado
                            , nota_liquidacao_paga.exercicio
                            , nota_liquidacao_paga.cod_entidade
                            , nota_liquidacao_paga.cod_nota
                            , nota_liquidacao_paga.timestamp
                       
                    ORDER BY empenho.exercicio
                            , empenho.cod_empenho
                            , TO_CHAR(nota_liquidacao_paga.timestamp,'ddmmyyyy')
            ) AS retencao
            
            WHERE retencao.situacao='PAGA'
            
            GROUP BY retencao.exercicio_empenho
                    , retencao.unidade_orcamentaria
                    , retencao.cod_empenho
                    , retencao.tipo_retencao
                    , retencao.situacao
         
            ORDER BY exercicio_empenho, cod_empenho
        ";

        return $stSql;
    }

}
?>