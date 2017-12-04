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
    * Classe de mapeamento da tabela FN_EXPORTACAO_LIQUIDACAO
    * Data de Criação: 24/01/2005

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 64433 $
    $Name$
    $Author: jean $
    $Date: 2016-02-22 16:21:35 -0300 (Mon, 22 Feb 2016) $

    * Casos de uso: uc-02.08.01
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.8  2006/07/05 20:45:59  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoLiquidacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoLiquidacao()
{
    parent::Persistente();
    $this->setTabela('tcers.fn_exportacao_liquidacao');
}

function montaRecuperaDadosExportacao()
{
    $stSql  = "
                SELECT

                *,
                CASE WHEN resultado.cod_contrato IS NOT NULL THEN 'S'
                     WHEN resultado.cod_estrutural LIKE ('3190%') THEN 'X'
                     ELSE 'N'
                END AS existe_contrato

                FROM (

                SELECT                                                               
                    lpad(tabela.exercicio,4,'0') as exercicio,                      
                    lpad(tabela.cod_empenho::varchar,7,'0') as cod_empenho,         
                    lpad(tabela.cod_entidade::varchar,2,'0') as cod_entidade,       
                    tabela.cod_nota,                                                
                    to_char(tabela.data_pagamento,'dd/mm/yyyy') as data_pagamento,  
                    replace(tabela.valor_liquidacao::varchar,'.','') as valor_liquidacao,
                    tabela.sinal_valor,                                              
                    tabela.observacao,                                               
                    tabela.ordem,                                                    
                    ' ' as codigo_operacao,
                    contratos_liquidacao.cod_contrato_tce,
                    contratos_liquidacao.cod_contrato,
                    contratos_liquidacao.exercicio AS exercicio_contrato,
                    despesa.cod_estrutural
                FROM                                                                 
                 ".$this->getTabela()."('".$this->getDado("stExercicio")     ."',    
                                        '".$this->getDado("dtInicial")       ."',    
                                        '".$this->getDado("dtFinal")         ."',    
                                        '".$this->getDado("stCodEntidades")  ."',    
                                        '".$this->getDado("stFiltro")        ."')    
                AS tabela              (   exercicio char(4),                        
                                           cod_empenho integer,                      
                                           cod_entidade integer,                     
                                           cod_nota integer,                         
                                           data_pagamento date,                      
                                           valor_liquidacao numeric,                 
                                           sinal_valor text,                         
                                           observacao varchar,                       
                                           ordem integer,                            
                                           oid oid)                                  
               INNER JOIN empenho.empenho
                       ON empenho.exercicio = tabela.exercicio
                      AND empenho.cod_entidade = tabela.cod_entidade
                      AND empenho.cod_empenho = tabela.cod_empenho
               INNER JOIN empenho.pre_empenho
                       ON pre_empenho.exercicio = empenho.exercicio
                      AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               INNER JOIN ( SELECT pre_empenho.exercicio
                                 , pre_empenho.cod_pre_empenho
                                 , CASE WHEN ( pre_empenho.implantado = true )
                                        THEN restos_pre_empenho.cod_estrutural
                                        ELSE replace(conta_despesa.cod_estrutural, '.', '')
                                   END as cod_estrutural
                              FROM empenho.pre_empenho
                         LEFT JOIN empenho.restos_pre_empenho
                                ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                               AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN empenho.pre_empenho_despesa
                                ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                               AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         LEFT JOIN orcamento.conta_despesa
                                ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                               AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                          ) AS despesa
                       ON despesa.exercicio = empenho.exercicio
                      AND despesa.cod_pre_empenho = empenho.cod_pre_empenho
                LEFT JOIN tcers.contratos_liquidacao
                       ON contratos_liquidacao.cod_liquidacao = tabela.cod_nota
                      AND contratos_liquidacao.exercicio = TO_CHAR(tabela.data_pagamento, 'yyyy')
                ) as resultado
                ORDER BY resultado.exercicio, resultado.cod_entidade, resultado.cod_empenho, resultado.ordem
            ";
    return $stSql;
}

function montaRecuperaDadosExportacao2016()
{
    $stSql  = "
                SELECT
                        *
                      , CASE WHEN resultado.cod_contrato IS NOT NULL THEN 'S'
                             WHEN resultado.cod_estrutural LIKE ('3190%') THEN 'X'
                             ELSE 'N'
                      END AS existe_contrato

                  FROM (
                        SELECT                                                               
                                LPAD(tabela.exercicio,4,'0') AS exercicio
                              , LPAD(tabela.cod_empenho::VARCHAR,7,'0') AS cod_empenho
                              , LPAD(tabela.cod_entidade::VARCHAR,2,'0') AS cod_entidade
                              , tabela.cod_nota
                              , TO_CHAR(tabela.data_pagamento,'dd/mm/yyyy') AS data_pagamento
                              , REPLACE(tabela.valor_liquidacao::VARCHAR,'.','') AS valor_liquidacao
                              , tabela.sinal_valor
                              , tabela.observacao
                              , tabela.ordem
                              , ' ' AS codigo_operacao
                              , contratos_liquidacao.cod_contrato_tce
                              , contratos_liquidacao.cod_contrato
                              , contratos_liquidacao.exercicio AS exercicio_contrato
                              , despesa.cod_estrutural
                              , CASE WHEN nota_fiscal.cod_nota IS NOT NULL THEN 'S' ELSE 'N' END AS existe_nf
                              , nota_fiscal.nro_nota AS num_nota
                              , nota_fiscal.nro_serie AS num_serie

                          FROM                                                                 
                                ".$this->getTabela()."('".$this->getDado("stExercicio")     ."',    
                                                       '".$this->getDado("dtInicial")       ."',    
                                                       '".$this->getDado("dtFinal")         ."',    
                                                       '".$this->getDado("stCodEntidades")  ."',    
                                                       '".$this->getDado("stFiltro")        ."'
                                                      )    
                            AS tabela ( exercicio char(4)
                                      , cod_empenho integer
                                      , cod_entidade integer
                                      , cod_nota integer
                                      , data_pagamento date
                                      , valor_liquidacao numeric
                                      , sinal_valor text
                                      , observacao varchar
                                      , ordem integer
                                      , oid oid
                                      )
                    INNER JOIN empenho.empenho
                            ON empenho.exercicio = tabela.exercicio
                           AND empenho.cod_entidade = tabela.cod_entidade
                           AND empenho.cod_empenho = tabela.cod_empenho

                    INNER JOIN empenho.pre_empenho
                            ON pre_empenho.exercicio = empenho.exercicio
                           AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                    INNER JOIN (  SELECT
                                          pre_empenho.exercicio
                                        , pre_empenho.cod_pre_empenho
                                        , CASE WHEN ( pre_empenho.implantado = true )
                                               THEN restos_pre_empenho.cod_estrutural
                                               ELSE replace(conta_despesa.cod_estrutural, '.', '')
                                        END as cod_estrutural

                                    FROM empenho.pre_empenho

                               LEFT JOIN empenho.restos_pre_empenho
                                      ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                                     AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                               LEFT JOIN empenho.pre_empenho_despesa
                                      ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                                     AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                               LEFT JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                               ) AS despesa
                            ON despesa.exercicio = empenho.exercicio
                           AND despesa.cod_pre_empenho = empenho.cod_pre_empenho

                     LEFT JOIN tcers.nota_fiscal
                            ON nota_fiscal.cod_nota = tabela.cod_nota
                           AND nota_fiscal.exercicio = TO_CHAR(tabela.data_pagamento, 'yyyy')
                           AND nota_fiscal.cod_entidade = tabela.cod_entidade

                     LEFT JOIN tcers.contratos_liquidacao
                            ON contratos_liquidacao.cod_liquidacao = tabela.cod_nota
                           AND contratos_liquidacao.exercicio = TO_CHAR(tabela.data_pagamento, 'yyyy')
                        ) as resultado
                ORDER BY resultado.exercicio, resultado.cod_entidade, resultado.cod_empenho, resultado.ordem
            ";
    return $stSql;
}

/**
    * Executa funcao fn_exportacao_liquidacao no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem)) {
      $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }

    if (Sessao::getExercicio() > '2015') {
      $stSql = $this->montaRecuperaDadosExportacao2016().$stCondicao.$stOrdem;
    } else {
      $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
    }

    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
