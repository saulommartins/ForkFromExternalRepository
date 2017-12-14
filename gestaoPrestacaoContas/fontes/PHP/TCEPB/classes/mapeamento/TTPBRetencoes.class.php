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
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 60025 $
    $Name$
    $Author: michel $
    $Date: 2014-09-25 15:12:10 -0300 (Thu, 25 Sep 2014) $
    
    $Id: TTPBRetencoes.class.php 60025 2014-09-25 18:12:10Z michel $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBRetencoes extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBRetencoes()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

// //Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
// function montaRecuperaTodos()
// {
//     $stSql = "
//                 SELECT *
//                   FROM tcepb.fn_relatorio_retencoes( '" . $this->getDado('exercicio') . "' ,'".$this->getDado('inMes')."' ,'".$this->getDado('stEntidades')."' )
//                                                     AS tbl
//                                                    (  exercicio_empenho      VARCHAR
//                                                     , unidade_orcamentaria   VARCHAR
//                                                     , cod_empenho            INTEGER
//                                                     , num_parcela            INTEGER
//                                                     , vl_retencao            DECIMAL
//                                                     , tipo_retencao          INTEGER
//                                                     , tipo_lancamento        INTEGER
//                                                     , total_op               NUMERIC
//                                                     , vl_pago                NUMERIC
//                                                     , situacao               TEXT   ) ";

//     return $stSql;
// }



function recuperaRelatorioRetencoes(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stSql  = $this->montaRecuperaRelatorioRetencoes().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
}


function montaRecuperaRelatorioRetencoes()
{
    $stSql = "SELECT * FROM(
                SELECT CAST(empenho.exercicio AS VARCHAR) AS exercicio_empenho
                         , CAST(LPAD(despesa.num_orgao::VARCHAR, 2, '0') || LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS VARCHAR) AS unidade_orcamentaria
                         , CAST(empenho.cod_empenho AS INTEGER) AS cod_empenho
                         , CAST(tc.numero_pagamento_empenho( nota_liquidacao_paga.exercicio, nota_liquidacao_paga.cod_entidade, nota_liquidacao_paga.cod_nota, nota_liquidacao_paga.timestamp) AS INTEGER) AS num_parcela
                         , CAST(nota_liquidacao_paga.vl_pago AS DECIMAL)  AS vl_retencao
                         , CAST(plano_analitica_tipo_retencao.cod_tipo AS INTEGER) AS tipo_retencao
                         , CAST(1 AS INTEGER) AS tipo_lancamento                         
                         , CASE WHEN COALESCE(PL.vl_pagamento,0.00) - COALESCE(OPLA.vl_anulado,0.00) = 0.00 THEN
                                'ANULADA'
                           WHEN (COALESCE(nota_liquidacao_paga.vl_pago,0.00) - COALESCE(NLPA.vl_anulado,0.00)) = 0.00 THEN
                                'A PAGAR'
                           WHEN (COALESCE(nota_liquidacao_paga.vl_pago,0.00) - COALESCE(NLPA.vl_anulado,0.00)) > 0.00 THEN
                                'PAGA'
                           END AS situacao
                         
                      FROM orcamento.despesa
                      
                INNER JOIN empenho.pre_empenho_despesa
                        ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                       AND despesa.exercicio   = pre_empenho_despesa.exercicio
                       
                INNER JOIN empenho.empenho
                        ON pre_empenho_despesa.exercicio       = empenho.exercicio
                       AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                       
                INNER JOIN empenho.nota_liquidacao
                        ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                       AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                       AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                       
                INNER JOIN empenho.nota_liquidacao_paga 
                        ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                       AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                       AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
        
                 LEFT JOIN empenho.nota_liquidacao_paga_anulada AS NLPA
                        ON NLPA.exercicio   = nota_liquidacao_paga.exercicio
                       AND NLPA.cod_nota    = nota_liquidacao_paga.cod_nota
                       AND NLPA.cod_entidade    = nota_liquidacao_paga.cod_entidade
                       AND NLPA.timestamp   = nota_liquidacao_paga.timestamp
                       
                INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                        ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                       AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                       AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                       AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
        
                INNER JOIN empenho.pagamento_liquidacao AS PL
                        ON PL.exercicio         = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                       AND PL.cod_entidade      = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                       AND PL.cod_ordem         = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                       AND PL.exercicio_liquidacao  = pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao
                       AND PL.cod_nota          = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
        
                 LEFT JOIN empenho.ordem_pagamento_liquidacao_anulada AS OPLA
                        ON OPLA.exercicio              = PL.exercicio
                       AND OPLA.cod_entidade           = PL.cod_entidade
                       AND OPLA.cod_ordem              = PL.cod_ordem
                       AND OPLA.exercicio_liquidacao = PL.exercicio_liquidacao
                       AND OPLA.cod_nota                 = PL.cod_nota
                       
                 JOIN empenho.ordem_pagamento_retencao
                        ON ordem_pagamento_retencao.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                       AND ordem_pagamento_retencao.cod_ordem    = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
                       AND ordem_pagamento_retencao.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                       
                 JOIN tcepb.plano_analitica_tipo_retencao
                       ON plano_analitica_tipo_retencao.exercicio = ordem_pagamento_retencao.exercicio
                      AND plano_analitica_tipo_retencao.cod_plano = ordem_pagamento_retencao.cod_plano
                      
                     WHERE empenho.exercicio    = '".$this->getDado('exercicio')."'
                       AND empenho.cod_entidade IN (".$this->getDado('stEntidades').")
                       AND TO_CHAR(nota_liquidacao_paga.timestamp,'mm') = '".$this->getDado('inMes')."'
                       
                  GROUP BY empenho.exercicio
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
              AND num_parcela > 1
            
         GROUP BY retencao.exercicio_empenho
                , retencao.unidade_orcamentaria
                , retencao.cod_empenho
                , retencao.num_parcela
                , retencao.vl_retencao
                , retencao.tipo_retencao
                , retencao.tipo_lancamento
                , retencao.situacao
         
         ORDER BY exercicio_empenho, cod_empenho, num_parcela
            ";

    return $stSql;
}

}
