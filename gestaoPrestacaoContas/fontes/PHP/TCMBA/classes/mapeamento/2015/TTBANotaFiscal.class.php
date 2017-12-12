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
    * Data de Criação: 07/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63459 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:20:03  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/08/09 01:05:49  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 07/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBANotaFiscal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
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
    $stSql = "  SELECT DISTINCT
                        1 AS tipo_registro
                        , ".$this->getDado("unidade_gestora")." AS unidade_gestora
                        , LPAD(autorizacao_empenho.num_unidade::varchar,2,'0') || LPAD(autorizacao_empenho.num_orgao::varchar,2,'0') AS unidade_orcamentaria
                        , empenho.cod_empenho as num_empenho
                        , TO_CHAR(nota_liquidacao_paga.timestamp,'dd/mm/yyyy') as data_pagamento_empenho
                        , nota_fiscal_liquidacao.nro_nota
                        ,'".Sessao::getExercicio()."' as ano
                        , nota_fiscal_liquidacao.nro_serie
                        , nota_fiscal_liquidacao.nro_subserie
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 
                                   sw_cgm_pessoa_fisica.cpf        
                               WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN 
                                   sw_cgm_pessoa_juridica.cnpj       
                               ELSE 
                                     ''
                        END AS cpf_cnpj
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 
                                   sw_cgm.nom_cgm
                               WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN 
                                   sw_cgm_pessoa_juridica.nom_fantasia
                               ELSE 
                                     ''
                        END AS nome_emitente
                        , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN 
                                        1 
                                    ELSE 
                                        2                                      
                         END AS tipo_emitente
                        , nota_fiscal_liquidacao.data_emissao as data_nota
                        , nota_fiscal_liquidacao.vl_nota as valor_nota
                        , '' as reservado_tcm
                        , nota_fiscal_liquidacao.descricao as descricao_nota
                        , '".$this->getDado('competencia')."' as competencia
                        , autorizacao_empenho.num_orgao as cod_orgao
                        , empenho.cod_empenho as num_subempenho
                
                 FROM tcmba.nota_fiscal_liquidacao

           INNER JOIN empenho.nota_liquidacao
                   ON nota_liquidacao.cod_nota     = nota_fiscal_liquidacao.cod_nota_liquidacao
                  AND nota_liquidacao.exercicio    = nota_fiscal_liquidacao.exercicio_liquidacao
                  AND nota_liquidacao.cod_entidade = nota_fiscal_liquidacao.cod_entidade

           INNER JOIN empenho.nota_liquidacao_paga
                   ON nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                  AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                  AND nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota

           INNER JOIN empenho.empenho
                   ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                  AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                  AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

           INNER JOIN empenho.pre_empenho
                   ON pre_empenho.exercicio = empenho.exercicio
                  AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

           INNER JOIN empenho.pre_empenho_despesa
                   ON pre_empenho.exercicio = pre_empenho_despesa.exercicio                
                  AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

           INNER JOIN orcamento.conta_despesa
                   ON pre_empenho_despesa.exercicio = conta_despesa.exercicio                
                  AND pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
           
           INNER JOIN sw_cgm
                   ON pre_empenho.cgm_beneficiario = sw_cgm.numcgm

            LEFT JOIN sw_cgm_pessoa_fisica
                   ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

            LEFT JOIN sw_cgm_pessoa_juridica
                   ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

          INNER JOIN empenho.empenho_autorizacao
                  ON empenho.exercicio    = empenho_autorizacao.exercicio
                 AND empenho.cod_entidade = empenho_autorizacao.cod_entidade
                 AND empenho.cod_empenho  = empenho_autorizacao.cod_empenho
                 
         INNER JOIN empenho.autorizacao_empenho
                 ON autorizacao_empenho.exercicio       = empenho_autorizacao.exercicio
                AND autorizacao_empenho.cod_entidade    = empenho_autorizacao.cod_entidade
                AND autorizacao_empenho.cod_autorizacao = empenho_autorizacao.cod_autorizacao

              WHERE nota_fiscal_liquidacao.data_emissao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                AND nota_fiscal_liquidacao.cod_entidade IN (".$this->getDado('entidades').")
    ";

    return $stSql;
}

}
