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
    * Data de Criação   : 02/10/2014

    * @author Analista:
    * @author Desenvolvedor: Jean Silva
    $Id: TTCEPEItemPagamento.class.php 60632 2014-11-04 18:41:10Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPEItemPagamento extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEPEItemPagamento()
    {
        parent::Persistente();
    }


    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaItemPagamento.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaItemPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaItemPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItemPagamento()
    {
        $stSql = " SELECT DISTINCT
                          retorno.exercicio AS ano_empenho,
                          LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria,
                          retorno.empenho AS num_empenho,
                          1 AS num_parcela,
                          nota_liquidacao_paga.vl_pago AS vl_pagamento,
                          regexp_replace(conta_corrente.num_conta_corrente,'[.|-]','','gi') AS num_banco_debito,
                          cheque_emissao_ordem_pagamento.num_cheque AS num_cheque,
                          '' AS doc_debito,
                          '' AS cod_febraban,
                          '' AS cod_agencia,
                          '' AS num_banco_credito,
                          recurso.cod_recurso AS fonte_recursos,
                          plano_banco_tipo_conta_banco.cod_tipo_conta_banco AS tipo_conta,
                          '1'::varchar AS num_sequencial,
                          0 AS reservado_tce
                          
                    FROM empenho.fn_empenho_empenhado_pago_estornado ('" .$this->getDado("exercicio"). "',                             
                                                                      '',
                                                                      '" .$this->getDado("stDataInicial"). "',                                    
                                                                      '" .$this->getDado("stDataFinal"). "',
                                                                      '" .$this->getDado("stEntidade"). "',                                                                                
                                                                      '',
                                                                      '',
                                                                      '',              
                                                                      '',
                                                                      '',                 
                                                                      '',
                                                                      '',                           
                                                                      '',
                                                                      '',                                  
                                                                      '',
                                                                      '',                                   
                                                                      '',
                                                                      '',
                                                                      '',  
                                                                      '',
                                                                      ''
                                                                    ) AS retorno (                           
                                                                                    entidade            integer,                                                                                       
                                                                                    descricao_categoria varchar,                                                                                       
                                                                                    nom_tipo            varchar,                                                                                       
                                                                                    empenho             integer,                                                                                       
                                                                                    exercicio           char(4),                                                                                       
                                                                                    cgm                 integer,                                                                                       
                                                                                    razao_social        varchar,                                                                                       
                                                                                    cod_nota            integer,                                                                                       
                                                                                    data                text,                                                                                          
                                                                                    ordem               integer,                                                                                       
                                                                                    conta               integer,                                                                                       
                                                                                    nome_conta          varchar,                                                                                       
                                                                                    valor               numeric,                                                                                       
                                                                                    valor_estornado     numeric,                                                                                       
                                                                                    valor_liquido       numeric,                                                                                       
                                                                                    descricao           varchar,                                                                                       
                                                                                    recurso             varchar,                                                                                       
                                                                                    despesa             varchar(150)                                                                                   
                                                                                )
                                                                                
                ----- JOIN PARA ACHAR O ORCAMENTO DESPESA ---------
                
                    JOIN empenho.empenho
                      ON empenho.cod_empenho  = retorno.empenho
                     AND empenho.exercicio    = retorno.exercicio
                     AND empenho.cod_entidade = retorno.entidade
                     
                    JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                     
                    JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                     AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     
                    JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio   = pre_empenho_despesa.exercicio
                     
                    JOIN orcamento.recurso
		      ON recurso.exercicio = despesa.exercicio
		     AND recurso.cod_recurso = despesa.cod_recurso
                     
                ----------------------------------------------------------
                
                    JOIN empenho.nota_liquidacao
                      ON nota_liquidacao.cod_empenho  = empenho.cod_empenho
                     AND nota_liquidacao.exercicio    = empenho.exercicio
                     AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                     
                    JOIN empenho.nota_liquidacao_paga
                      ON nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                     AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                     AND nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                     AND to_char(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') = retorno.data
                     AND nota_liquidacao_paga.cod_nota     = retorno.cod_nota
                     
               LEFT JOIN empenho.nota_liquidacao_conta_pagadora
                      ON nota_liquidacao_conta_pagadora.cod_entidade         = nota_liquidacao_paga.cod_entidade
                     AND nota_liquidacao_conta_pagadora.cod_nota             = nota_liquidacao_paga.cod_nota
                     AND nota_liquidacao_conta_pagadora.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                     AND nota_liquidacao_conta_pagadora.timestamp            = nota_liquidacao_paga.timestamp
                     
                -------- JOIN PARA ACHAR O BANCO ---------------------
                
                JOIN contabilidade.plano_banco
                        ON plano_banco.exercicio = retorno.exercicio
                     AND plano_banco.cod_plano = retorno.conta
                
                JOIN monetario.conta_corrente
                       ON conta_corrente.cod_banco             = plano_banco.cod_banco
                      AND conta_corrente.cod_agencia          = plano_banco.cod_agencia
                      AND conta_corrente.cod_conta_corrente   = plano_banco.cod_conta_corrente
               
               LEFT JOIN tcepe.plano_banco_tipo_conta_banco
                      ON plano_banco_tipo_conta_banco.exercicio = plano_banco.exercicio
                     AND plano_banco_tipo_conta_banco.cod_plano = plano_banco.cod_plano
                     
                -----------------------------------------------------
                
                    JOIN empenho.ordem_pagamento
                      ON ordem_pagamento.exercicio    = retorno.exercicio
                     AND ordem_pagamento.cod_entidade = retorno.entidade
                     AND ordem_pagamento.cod_ordem    = retorno.ordem
                     
               LEFT JOIN tesouraria.cheque_emissao_ordem_pagamento
                      ON cheque_emissao_ordem_pagamento.cod_ordem    = ordem_pagamento.cod_ordem
                     AND cheque_emissao_ordem_pagamento.exercicio    = ordem_pagamento.exercicio
                     AND cheque_emissao_ordem_pagamento.cod_entidade = ordem_pagamento.cod_entidade
                     
                    WHERE TO_CHAR(TO_DATE(nota_liquidacao_paga.timestamp::varchar, 'YYYY-MM-DD'),'mmyyyy') = '".$this->getDado("mes").$this->getDado("exercicio")."'
                      AND retorno.exercicio = '" .$this->getDado("exercicio"). "'
                      AND retorno.valor_liquido > 0
                      
                      
                 GROUP BY
                          despesa.num_orgao
                        , despesa.num_unidade
                        , retorno.empenho
                        , retorno.exercicio
                        , retorno.recurso
                        , conta_corrente.num_conta_corrente
                        , nota_liquidacao_conta_pagadora.cod_plano
                        , nota_liquidacao_paga.vl_pago
                        , plano_banco_tipo_conta_banco.cod_tipo_conta_banco
                        , cheque_emissao_ordem_pagamento.num_cheque
                        , recurso.cod_recurso
                        
                 ORDER BY num_empenho
                ";

        return $stSql;
    }

}
?>