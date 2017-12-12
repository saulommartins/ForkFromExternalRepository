
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
    * Data de Criação: 02/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63248 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

/*
$Log$
Revision 1.2  2007/10/02 18:17:17  hboaventura
inclusão do caso de uso uc-06.05.00

Revision 1.1  2007/08/09 01:05:49  diego
Primeira versão.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 02/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAItemLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAItemLicitacao()
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
    $stSql = " SELECT   licitacao.exercicio 
                       ,licitacao.exercicio||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS cod_licitacao
                       ,mapa_item.cod_item
                       ,catalogo_item.descricao
                       ,TO_CHAR(homologacao.timestamp,'dd/mm/yyyy') AS data_homologacao     
                       ,TO_CHAR(homologacao.timestamp,'yyyymm') AS competencia     
                       ,COALESCE(mapa_item.quantidade,0.00) AS qtd_licitacao     
                       ,COALESCE(mapa_item_anulacao.qtd_anulacao,0.00) AS qtd_anulacao     
                       ,COALESCE(mapa_item.quantidade,0.00) - COALESCE(mapa_item_anulacao.qtd_anulacao,0.00) AS qtd_saldo     
                       ,unidade_medida.simbolo
                       ,1 AS tipo_registro
                       , ".$this->getDado('unidade_gestora')." AS unidade_gestora

                FROM compras.mapa_item

          INNER JOIN compras.mapa
                  ON mapa.cod_mapa = mapa_item.cod_mapa
                 AND mapa.exercicio = mapa_item.exercicio

           LEFT JOIN (
                        SELECT mapa_item_anulacao.exercicio     
                              ,mapa_item_anulacao.cod_mapa     
                              ,mapa_item_anulacao.exercicio_solicitacao     
                              ,mapa_item_anulacao.cod_entidade     
                              ,mapa_item_anulacao.cod_solicitacao     
                              ,mapa_item_anulacao.cod_centro     
                              ,mapa_item_anulacao.cod_item     
                              ,mapa_item_anulacao.lote
                              ,COALESCE(SUM(mapa_item_anulacao.quantidade),0.00) AS qtd_anulacao     
                          FROM compras.mapa_item_anulacao
                          WHERE mapa_item_anulacao.exercicio='".$this->getDado('exercicio')."'
                            ";

        if (trim($this->getDado('entidades'))) {
            $stSql .= " AND mapa_item_anulacao.cod_entidade IN (".$this->getDado('entidades').")
                      ";
        }

        $stSql .= "
                          GROUP BY mapa_item_anulacao.exercicio
                                 , mapa_item_anulacao.cod_mapa
                                 , mapa_item_anulacao.exercicio_solicitacao
                                 , mapa_item_anulacao.cod_entidade
                                 , mapa_item_anulacao.cod_solicitacao
                                 , mapa_item_anulacao.cod_centro
                                 , mapa_item_anulacao.cod_item
                                 , mapa_item_anulacao.lote     
                    ) AS mapa_item_anulacao
                 ON mapa_item_anulacao.exercicio = mapa_item.exercicio     
                AND mapa_item_anulacao.cod_mapa = mapa_item.cod_mapa     
                AND mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao     
                AND mapa_item_anulacao.cod_entidade = mapa_item.cod_entidade     
                AND mapa_item_anulacao.cod_solicitacao = mapa_item.cod_solicitacao     
                AND mapa_item_anulacao.cod_centro = mapa_item.cod_centro     
                AND mapa_item_anulacao.cod_item = mapa_item.cod_item     
                AND mapa_item_anulacao.lote = mapa_item.lote

         INNER JOIN licitacao.licitacao
                 ON licitacao.exercicio_mapa = mapa.exercicio
                AND licitacao.cod_mapa = mapa.cod_mapa

         INNER JOIN licitacao.cotacao_licitacao
                 ON cotacao_licitacao.cod_licitacao = licitacao.cod_licitacao
                AND cotacao_licitacao.cod_modalidade = licitacao.cod_modalidade
                AND cotacao_licitacao.cod_entidade = licitacao.cod_entidade
                AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio

         INNER JOIN licitacao.adjudicacao
                 ON adjudicacao.cod_licitacao = cotacao_licitacao.cod_licitacao
                AND adjudicacao.cod_modalidade = cotacao_licitacao.cod_modalidade
                AND adjudicacao.cod_entidade = cotacao_licitacao.cod_entidade
                AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                AND adjudicacao.lote = cotacao_licitacao.lote
                AND adjudicacao.cod_cotacao = cotacao_licitacao.cod_cotacao
                AND adjudicacao.cod_item = cotacao_licitacao.cod_item
                AND adjudicacao.exercicio_cotacao = cotacao_licitacao.exercicio_cotacao
                AND adjudicacao.cgm_fornecedor = cotacao_licitacao.cgm_fornecedor

         INNER JOIN licitacao.homologacao
                 ON homologacao.num_adjudicacao = adjudicacao.num_adjudicacao
                AND homologacao.cod_entidade = adjudicacao.cod_entidade
                AND homologacao.cod_modalidade = adjudicacao.cod_modalidade
                AND homologacao.cod_licitacao = adjudicacao.cod_licitacao
                AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                AND homologacao.cod_item = adjudicacao.cod_item
                AND homologacao.cod_cotacao = adjudicacao.cod_cotacao
                AND homologacao.lote = adjudicacao.lote
                AND homologacao.exercicio_cotacao = adjudicacao.exercicio_cotacao
                AND homologacao.cgm_fornecedor = adjudicacao.cgm_fornecedor

         INNER JOIN almoxarifado.catalogo_item
                 ON catalogo_item.cod_item = mapa_item.cod_item

         INNER JOIN administracao.unidade_medida
                 ON catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
                AND catalogo_item.cod_unidade = unidade_medida.cod_unidade

              WHERE ";

        if (trim($this->getDado('entidades'))) {
            $stSql .= " licitacao.cod_entidade IN (".$this->getDado('entidades').")
                    AND ";              
        }

        $stSql .= " licitacao.exercicio = '".$this->getDado('exercicio')."'
                AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                          AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                AND licitacao.cod_modalidade NOT IN (8,9)

            GROUP BY licitacao.exercicio     
                    ,licitacao.cod_licitacao     
                    ,mapa_item.cod_item     
                    ,catalogo_item.cod_item     
                    ,catalogo_item.descricao     
                    ,data_homologacao    
                    ,competencia
                    ,unidade_medida.simbolo
                    ,licitacao.cod_entidade
                    ,licitacao.cod_modalidade
                    ,qtd_licitacao
                    ,qtd_anulacao
                    ,qtd_saldo

            ORDER BY licitacao.exercicio     
                    ,licitacao.cod_licitacao     
                    ,mapa_item.cod_item     
                    ,catalogo_item.cod_item     
                    ,catalogo_item.descricao     
                    ,data_homologacao
                    ,competencia
                    ,unidade_medida.simbolo
                    
        ";

    return $stSql;
}

}
