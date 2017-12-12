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
    * Data de Criação: 15/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63289 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 15/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBALicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::__construct();
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
    $stSql = " SELECT 1 AS tipo_registro
                     ,licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS cod_licitacao
                     ,".$this->getDado("unidade_gestora")." AS unidade_gestora
                     ,TO_CHAR(publicacao_edital.data_publicacao,'dd/mm/yyyy') AS data_licitacao      
                     ,sw_cgm.nom_cgm as nom_cgm_imprensa      
                     ,objeto.descricao as descricao_objeto      
                     ,CASE WHEN licitacao.cod_modalidade = 1 THEN 5       --convite
                           WHEN licitacao.cod_modalidade = 2 THEN 10      --tomada de precos
                           WHEN licitacao.cod_modalidade = 3 THEN 1       --Concorrência
                           WHEN licitacao.cod_modalidade = 5 THEN 4       --concurso
                           WHEN licitacao.cod_modalidade = 4 THEN 7       --leilao
                           WHEN licitacao.cod_modalidade = 6 THEN 14       --pregao presencial
                           WHEN licitacao.cod_modalidade = 7 THEN 15       --pregao eletronico
                           WHEN licitacao.cod_modalidade = 11 THEN 17      --tomada de registro precos
                      END AS modalidade      
                     ,edital.num_edital      
                     ,TO_CHAR(licitacao.timestamp,'yyyymm') AS competencia      
                     ,licitacao.vl_cotado AS vl_estimado    
                     , CASE WHEN licitacao.cod_regime = 1 THEN 7
                            WHEN licitacao.cod_regime = 2 THEN 5
                            WHEN licitacao.cod_regime = 3 THEN 8  
                            WHEN licitacao.cod_regime = 4 THEN 6
                            WHEN licitacao.cod_regime = 5 THEN 4
                       ELSE 9 END AS regime_execucao      
                     , CASE WHEN licitacao.cod_criterio = 1 THEN
                            CASE WHEN licitacao.cod_tipo_licitacao = 1 THEN 3 --ITEM 
                                 WHEN licitacao.cod_tipo_licitacao = 2 THEN 9 --LOTE
                                 WHEN licitacao.cod_tipo_licitacao = 3 THEN 2 --GLOBAL
                            END
                            WHEN licitacao.cod_criterio = 2 THEN 4
                            WHEN licitacao.cod_criterio = 3 THEN 7
                            WHEN licitacao.cod_criterio = 4 THEN 1
                            END AS tipo_licitacao    
                     ,CASE WHEN edital.dt_aprovacao_juridico IS NOT NULL THEN 1 ELSE 2 END AS juridico      
                     ,TO_CHAR(homologacao.timestamp,'dd/mm/yyyy') AS data_homologacao      
                     ,TO_CHAR(edital.dt_abertura_propostas,'dd/mm/yyyy') AS data_propostas
                     ,'' AS orgao_internacional

                  FROM licitacao.licitacao

            INNER JOIN licitacao.edital
                    ON edital.cod_licitacao       = licitacao.cod_licitacao
                   AND edital.cod_modalidade      = licitacao.cod_modalidade
                   AND edital.cod_entidade        = licitacao.cod_entidade
                   AND edital.exercicio_licitacao = licitacao.exercicio

            INNER JOIN licitacao.publicacao_edital
                    ON publicacao_edital.num_edital = edital.num_edital
                   AND publicacao_edital.exercicio  = edital.exercicio

            INNER JOIN licitacao.veiculos_publicidade
                    ON veiculos_publicidade.numcgm = publicacao_edital.numcgm

            INNER JOIN sw_cgm
                    ON sw_cgm.numcgm = veiculos_publicidade.numcgm

            INNER JOIN compras.objeto
                    ON objeto.cod_objeto = licitacao.cod_objeto

            INNER JOIN licitacao.cotacao_licitacao
                    ON cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                   AND cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                   AND cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
                   AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio

            INNER JOIN licitacao.adjudicacao
                    ON adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                   AND adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                   AND adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                   AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                   AND adjudicacao.lote                = cotacao_licitacao.lote
                   AND adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                   AND adjudicacao.cod_item            = cotacao_licitacao.cod_item
                   AND adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao
                   AND adjudicacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor

            INNER JOIN licitacao.homologacao
                    ON homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                   AND homologacao.cod_entidade        = adjudicacao.cod_entidade
                   AND homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                   AND homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                   AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                   AND homologacao.cod_item            = adjudicacao.cod_item
                   AND homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                   AND homologacao.lote                = adjudicacao.lote
                   AND homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                   AND homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor

                 WHERE licitacao.cod_modalidade NOT IN (8,9)
                   AND licitacao.exercicio    = '".$this->getDado('exercicio')."'
                   AND licitacao.cod_entidade IN (".$this->getDado('entidade').")
                   AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_fim')."','dd/mm/yyyy')

                GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16
                ";

    return $stSql;
}

}

?>