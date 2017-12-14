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
    * Data de Criação: 09/10/2014
    * @author Analista: 
    * @author Desenvolvedor: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEPERetencao extends Persistente
{

    function montaRecuperaTodos()
    {
        $stSql = "  SELECT 
                            retencao.exercicio_empenho
                            , LPAD(retencao.num_orgao::VARCHAR, 2, '0') || LPAD(retencao.num_unidade::VARCHAR, 2, '0') AS unidade_orcamentaria
                            , retencao.cod_empenho         
                            , 1 AS num_parcela
                            , SUM(retencao.vl_retencao) AS vl_retencao
                            , retencao.tipo_retencao
                    FROM (
                            SELECT  empenho.exercicio                               AS exercicio_empenho
                                    , empenho.cod_empenho
                                    , ordem_pagamento_retencao.vl_retencao
                                    , nota_liquidacao.exercicio                       AS exercicio_nota_liquidacao
                                    , nota_liquidacao.cod_entidade
                                    , nota_liquidacao.cod_nota
                                    , despesa.num_orgao
                                    , despesa.num_unidade
                                    , plano_analitica_tipo_retencao.cod_tipo          AS tipo_retencao
                                    , plano_conta.cod_estrutural
                                    , plano_analitica.cod_plano
                                    
                            FROM orcamento.despesa
                            
                            JOIN empenho.pre_empenho_despesa
                                 ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                AND despesa.exercicio   = pre_empenho_despesa.exercicio
                                
                            JOIN empenho.empenho
                                 ON pre_empenho_despesa.exercicio       = empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
                                
                            JOIN empenho.nota_liquidacao
                                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                
                            JOIN empenho.pagamento_liquidacao
                                 ON pagamento_liquidacao.exercicio            = nota_liquidacao.exercicio
                                AND pagamento_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade
                                AND pagamento_liquidacao.cod_nota             = nota_liquidacao.cod_nota
                                
                            JOIN empenho.ordem_pagamento
                                 ON ordem_pagamento.exercicio    = pagamento_liquidacao.exercicio
                                AND ordem_pagamento.cod_entidade = pagamento_liquidacao.cod_entidade
                                AND ordem_pagamento.cod_ordem    = pagamento_liquidacao.cod_ordem
                                
                            LEFT JOIN empenho.ordem_pagamento_liquidacao_anulada
                                 ON ordem_pagamento_liquidacao_anulada.exercicio            = pagamento_liquidacao.exercicio
                                AND ordem_pagamento_liquidacao_anulada.cod_entidade         = pagamento_liquidacao.cod_entidade
                                AND ordem_pagamento_liquidacao_anulada.cod_ordem            = pagamento_liquidacao.cod_ordem
                                AND ordem_pagamento_liquidacao_anulada.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                                AND ordem_pagamento_liquidacao_anulada.cod_nota             = pagamento_liquidacao.cod_nota
                                
                            JOIN empenho.ordem_pagamento_retencao
                                 ON ordem_pagamento_retencao.exercicio    = ordem_pagamento.exercicio
                                AND ordem_pagamento_retencao.cod_ordem    = ordem_pagamento.cod_ordem
                                AND ordem_pagamento_retencao.cod_entidade = ordem_pagamento.cod_entidade
                                
                            JOIN contabilidade.plano_analitica
                                 ON plano_analitica.cod_plano = ordem_pagamento_retencao.cod_plano
                                AND plano_analitica.exercicio =  ordem_pagamento_retencao.exercicio
                                
                            JOIN contabilidade.plano_conta
                                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                                AND plano_conta.exercicio = plano_analitica.exercicio
                                
                            LEFT JOIN tcepe.plano_analitica_tipo_retencao
                                 ON plano_analitica_tipo_retencao.exercicio = plano_analitica.exercicio
                                AND plano_analitica_tipo_retencao.cod_plano = plano_analitica.cod_plano
                                
                            WHERE empenho.exercicio    = '".$this->getDado('exercicio')."'
                            AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                            AND SUBSTR(cod_estrutural,1,1) IN ('1','2')
                            AND ordem_pagamento.dt_emissao BETWEEN to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') AND to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
                    
                            GROUP BY empenho.exercicio
                                    , empenho.cod_empenho
                                    , ordem_pagamento_retencao.cod_ordem
                                    , ordem_pagamento_retencao.cod_plano
                                    , ordem_pagamento_retencao.vl_retencao
                                    , nota_liquidacao.exercicio
                                    , nota_liquidacao.cod_entidade
                                    , nota_liquidacao.cod_nota
                                    , despesa.num_orgao
                                    , despesa.num_unidade
                                    , plano_analitica_tipo_retencao.cod_tipo
                                    , pagamento_liquidacao.vl_pagamento
                                    , ordem_pagamento_liquidacao_anulada.vl_anulado
                                    , plano_conta.cod_estrutural
                                    , plano_analitica.cod_plano
                            ORDER BY empenho.exercicio
                                    , empenho.cod_empenho
                    ) AS retencao
                    
                    GROUP BY retencao.exercicio_empenho
                           , retencao.num_orgao
                           , retencao.num_unidade
                           , retencao.cod_empenho
                           , retencao.tipo_retencao        
                            
                    ORDER BY exercicio_empenho
                           , cod_empenho
        ";
        
        return $stSql;
    }

}//FIM CLASSE
