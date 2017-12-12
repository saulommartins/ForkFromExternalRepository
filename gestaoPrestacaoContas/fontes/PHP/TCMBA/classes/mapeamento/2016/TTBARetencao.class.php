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
    * Data de Criação: 12/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63882 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/08/15 00:21:55  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 12/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBARetencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql = "   SELECT 1 AS tipo_registro
                      , ".$this->getDado('unidade_gestora')." AS unidade_gestora
		      , despesa.num_unidade AS unidade_orcamentaria
		      , empenho.cod_empenho AS num_empenho   
                      , TO_CHAR(nota_liquidacao_paga.timestamp,'YYYY') AS ano    
                      , plano_conta.exercicio AS ano_criacao   
                      , TO_CHAR(nota_liquidacao_paga.timestamp,'DDMMYYYY') as dt_pagamento_empenho
                      , REPLACE(plano_conta.cod_estrutural,'.','') AS conta_contabil
                      , COALESCE(SUM(ordem_pagamento_retencao.vl_retencao),0.00) AS vl_retencao
                      , ".$this->getDado('exercicio').$this->getDado('mes')."::VARCHAR AS competencia
                      , despesa.num_orgao AS cod_orgao    
                      , empenho.cod_empenho AS num_subempenho
                      
                 FROM empenho.empenho

            INNER JOIN empenho.pre_empenho
                    ON empenho.exercicio       = pre_empenho.exercicio    
                   AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho    
                    
            INNER JOIN empenho.nota_liquidacao 
                    ON empenho.exercicio    = nota_liquidacao.exercicio_empenho    
                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade    
                   AND empenho.cod_empenho  = nota_liquidacao.cod_empenho    
                     
            INNER JOIN empenho.pagamento_liquidacao
                    ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio    
                   AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade    
                   AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota    
            
            INNER JOIN empenho.ordem_pagamento
                    ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio    
                   AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade    
                   AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem    
            
            INNER JOIN empenho.ordem_pagamento_retencao
                    ON ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio    
                   AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade    
                   AND ordem_pagamento.cod_ordem    = ordem_pagamento_retencao.cod_ordem    
            
            INNER JOIN contabilidade.plano_analitica
                    ON ordem_pagamento_retencao.exercicio = plano_analitica.exercicio    
                   AND ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano    
            
            INNER JOIN contabilidade.plano_conta
                    ON plano_analitica.exercicio = plano_conta.exercicio    
                   AND plano_analitica.cod_conta = plano_conta.cod_conta    
            
            INNER JOIN empenho.nota_liquidacao_paga
                    ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio    
                   AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade    
                   AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
				   
-- Ao retirar esta linha os registros bem duplicados porque são lançados 2 na nota_liquidacao_paga. O registro de pagamento e o de retenção, assim para trazer apenas um registro de retenção é necessário igualar a retenção ao valor da nota_liquidacao
				   AND nota_liquidacao_paga.vl_pago = ordem_pagamento_retencao.vl_retencao
            
            INNER JOIN empenho.pre_empenho_despesa
                    ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio    
                   AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho    
            
            INNER JOIN orcamento.despesa
                    ON pre_empenho_despesa.exercicio   = despesa.exercicio    
                   AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa    
                     
                 WHERE TO_CHAR(nota_liquidacao_paga.timestamp,'YYYY')  = '".$this->getDado('exercicio')."'
                   AND TO_DATE(nota_liquidacao_paga.timestamp::VARCHAR, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY')
                                                                                          AND TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY')
                   AND nota_liquidacao.cod_entidade IN (".$this->getDado('entidades').")

              GROUP BY despesa.num_orgao
                      , ano
                      , ano_criacao
                      , despesa.num_unidade
                      , empenho.cod_empenho
                      , dt_pagamento_empenho    
                      , conta_contabil

              ORDER BY num_empenho
                     , dt_pagamento_empenho ";
    
    return $stSql;
}

}

?>