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
    * Classe de mapeamento da tabela compras.ordem
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Id$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.ordem
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasOrdemCompra extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TComprasOrdemCompra()
    {
        parent::Persistente();
        $this->setTabela("compras.ordem");

        $this->setCampoCod('cod_ordem');
        $this->setComplementoChave('exercicio, cod_entidade');

        $this->AddCampo('exercicio'         ,'char'     ,true, '4'    ,true,  false );
        $this->AddCampo('cod_entidade'      ,'integer'  ,true, ''     ,true,  true  );
        $this->AddCampo('cod_ordem'         ,'integer'  ,true, ''     ,true,  false );
        $this->AddCampo('exercicio_empenho' ,'char'     ,true, '4'    ,false, true  );
        $this->AddCampo('cod_empenho'       ,'integer'  ,true, ''     ,false, true  );
        $this->AddCampo('observacao'        ,'char'     ,true, '200'  ,false, false );
    }

    public function recuperaFornecedorOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaFornecedorOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaFornecedorOrdemCompra()
    {
        $stSql  ="SELECT                                                 \n";
        $stSql .="       emp.cod_empenho                                 \n";
        $stSql .="      ,pre.cgm_beneficiario                            \n";
        $stSql .="      ,pre.cod_pre_empenho                             \n";
        $stSql .="      ,emp.exercicio                                   \n";
        $stSql .="      ,cgm.nom_cgm                                     \n";
        $stSql .="FROM                                                   \n";
        $stSql .="      empenho.empenho as emp                           \n";
        $stSql .="     ,empenho.pre_empenho as pre                       \n";
        $stSql .="     ,sw_cgm as cgm                                    \n";
        $stSql .="WHERE                                                  \n";
        $stSql .="         emp.cod_pre_empenho = pre.cod_pre_empenho     \n";
        $stSql .="     AND emp.exercicio       = pre.exercicio           \n";
        $stSql .="     AND pre.cgm_beneficiario = cgm.numcgm             \n";

        if ($this->getDado('cod_entidade'))
            $stSql .="     AND emp.cod_entidade = ".$this->getDado('cod_entidade')."\n";

        if ($this->getDado('exercicio'))
            $stSql .="     AND emp.exercicio = '".$this->getDado('exercicio')."'    \n";

        if ($this->getDado('cod_empenho'))
            $stSql .="     AND emp.cod_empenho = ".$this->getDado('cod_empenho')."  \n";

        return $stSql;
    }

    public function recuperaEmpenhoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaEmpenhoLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaEmpenhoLicitacao()
    {
        $stSql .="
                SELECT
                        licitacao.cod_licitacao
                     ,  licitacao.cod_modalidade AS licitacao_cod_modalidade
                     ,  licitacao.cod_entidade AS licitacao_cod_entidade
                     ,  licitacao.exercicio AS licitacao_exercicio
                     ,	licitacao.cod_objeto as licitacao_cod_objeto
                     ,  compra_direta.cod_compra_direta
                     ,  compra_direta.cod_entidade AS compra_direta_cod_entidade
                     ,  compra_direta.cod_modalidade AS compra_direta_cod_modalidade
                     ,  compra_direta.exercicio_entidade AS compra_direta_exercicio
                     ,	compra_direta.cod_objeto as compra_direta_cod_objeto
                     ,  CASE
                            WHEN licitacao.cod_licitacao IS NOT NULL THEN
                                ( SELECT descricao FROM compras.objeto WHERE cod_objeto = licitacao.cod_objeto)::varchar
                               WHEN compra_direta.cod_compra_direta IS NOT NULL THEN
                                ( SELECT descricao FROM compras.objeto WHERE cod_objeto = compra_direta.cod_objeto)::varchar
                            ELSE
                                'Descricao Objeto'::varchar
                        END AS descricao_objeto
                     ,  fornecedor.numcgm
                     ,  fornecedor.nom_cgm
                     ,	empenho.cod_empenho
                FROM
                        empenho.empenho
                INNER JOIN
                        empenho.pre_empenho
                ON      (
                           pre_empenho.exercicio = empenho.exercicio AND
                           pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        )
                INNER JOIN
                        empenho.item_pre_empenho_julgamento
                ON      (
                            item_pre_empenho_julgamento.exercicio       = empenho.exercicio AND
                            item_pre_empenho_julgamento.cod_pre_empenho = empenho.cod_pre_empenho AND
                            item_pre_empenho_julgamento.num_item        = 1
                        )
                INNER JOIN
                        compras.cotacao
                ON      (
                            cotacao.exercicio   = item_pre_empenho_julgamento.exercicio AND
                            cotacao.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                        )
                INNER JOIN
                        compras.mapa_cotacao
                ON      (
                            mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao AND
                            mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                        )
                LEFT JOIN
                        compras.compra_direta
                ON      (
                            compra_direta.cod_mapa       = mapa_cotacao.cod_mapa AND
                            compra_direta.exercicio_mapa = mapa_cotacao.exercicio_mapa
                        )
                LEFT JOIN
                        licitacao.licitacao
                ON      (
                            licitacao.cod_mapa       = mapa_cotacao.cod_mapa AND
                            licitacao.exercicio_mapa = mapa_cotacao.exercicio_mapa
                        )
                INNER JOIN
                        sw_cgm AS fornecedor
                ON      (
                            pre_empenho.cgm_beneficiario = fornecedor.numcgm
                        )
                WHERE
                        empenho.cod_entidade = ".$this->getDado('cod_entidade')." AND
                        empenho.cod_empenho  = ".$this->getDado('cod_empenho')." AND
                        empenho.exercicio    = '".$this->getDado('exercicio')."'
    ";

        return $stSql;

    }

    public function recuperaItensEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensEmpenho().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItensEmpenho()
    {
        $stSql.= "
            SELECT	empenho.cod_empenho
                 ,  pre_empenho.cod_pre_empenho
                 ,  item_pre_empenho.num_item
                 ,  item_pre_empenho.nom_item
                 ,  item_pre_empenho.quantidade
                 ,  item_pre_empenho.exercicio
                 ,  ROUND( ( item_pre_empenho.vl_total - COALESCE(empenho_anulado_item.vl_anulado,0 ) ) / item_pre_empenho.quantidade,2 ) as vl_unitario
                 ,  ( item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0) ) AS oc_saldo
                 ,  ( ROUND( ( item_pre_empenho.vl_total - COALESCE(empenho_anulado_item.vl_anulado,0 ) ) / item_pre_empenho.quantidade,2 ) * ( item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0) ) ) AS oc_vl_total
                 ,  COALESCE(ordem.quantidade,0) AS oc_quantidade_atendido
                 ,  COALESCE(ordem.vl_total,0) AS oc_vl_atendido
              FROM  empenho.empenho
        INNER JOIN  empenho.pre_empenho
                ON  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               AND  pre_empenho.exercicio = empenho.exercicio
        INNER JOIN  empenho.item_pre_empenho
                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
        INNER JOIN  empenho.item_pre_empenho_julgamento
                ON  item_pre_empenho_julgamento.exercicio = item_pre_empenho.exercicio
               AND  item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  item_pre_empenho_julgamento.num_item = item_pre_empenho.num_item
         LEFT JOIN  empenho.empenho_anulado_item
                ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
               AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
        LEFT JOIN  (	SELECT 	SUM( ordem_item.quantidade - COALESCE(ordem_item_anulacao.quantidade,0) ) AS quantidade
                            ,	SUM( ordem_item.vl_total - COALESCE(ordem_item_anulacao.vl_total,0) ) AS vl_total
                            ,  ordem.exercicio_empenho
                            ,  ordem.cod_empenho
                            ,  ordem_item.num_item
                        FROM  compras.ordem
                    INNER JOIN  compras.ordem_item
                            ON  ordem_item.exercicio = ordem.exercicio
                        AND  ordem_item.cod_entidade = ordem.cod_entidade
                        AND  ordem_item.cod_ordem = ordem.cod_ordem
                    LEFT JOIN  compras.ordem_item_anulacao
                            ON  ordem_item_anulacao.exercicio = ordem_item.exercicio
                        AND  ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                        AND  ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                        AND  ordem_item_anulacao.num_item = ordem_item.num_item
                        AND  ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                        WHERE  NOT EXISTS 	(	SELECT 	1
                                                FROM  compras.ordem_anulacao
                                                WHERE  ordem_anulacao.exercicio = ordem.exercicio
                                                AND  ordem_anulacao.cod_entidade = ordem.cod_entidade
                                                AND  ordem_anulacao.cod_ordem = ordem.cod_ordem
                                            )
                    GROUP BY 	ordem.exercicio_empenho, ordem.cod_empenho, ordem_item.num_item

                            ) AS ordem
                ON  ordem.exercicio_empenho = empenho.exercicio
               AND  ordem.cod_empenho = empenho.cod_empenho
               AND  ordem.num_item = item_pre_empenho.num_item
             WHERE  empenho.cod_empenho = ".$this->getDado('cod_empenho')."
               AND  empenho.exercicio = '".$this->getDado('exercicio')."'
               AND  empenho.cod_entidade = ".$this->getDado('cod_entidade')."
               AND  (item_pre_empenho.quantidade - COALESCE(ordem.quantidade,0)) > 0
               AND  NOT EXISTS 	( 	SELECT 	1
                                         FROM  empenho.empenho_anulado
                                        WHERE  empenho_anulado.exercicio = empenho.exercicio
                                          AND  empenho_anulado.cod_entidade = empenho.cod_entidade
                                          AND  empenho_anulado.cod_empenho = empenho.cod_empenho
                                   )
        ";

        return $stSql;
    }

    public function recuperaDetalheItem(&$rsRecordSet, $stFiltro='', $stOrdem='', $boTransacao='')
    {
        return $this->executaRecupera( 'montaRecuperaDetalheItem', $rsRecordSet,$stFiltro,$stOrdem,$boTransacao );
    }

    public function montaRecuperaDetalheItem()
    {
        $stSql = "
            SELECT 	item_pre_empenho.num_item
                 ,  item_pre_empenho.cod_pre_empenho
                 ,	item_pre_empenho.exercicio
                 ,  CASE WHEN ( julgada.descricao is null )
                         THEN empenho_diverso.descricao
                         ELSE julgada.descricao
                    END AS descricao
                 ,  CASE WHEN ( julgada.nom_unidade is null )
                         THEN empenho_diverso.nom_unidade
                         ELSE julgada.nom_unidade
                    END AS nom_unidade
                 ,  CASE WHEN ( julgada.nom_grandeza is null )
                         THEN empenho_diverso.nom_grandeza
                         ELSE julgada.nom_grandeza
                    END AS nom_grandeza
                 ,  CASE WHEN ( julgada.cod_item is null )
                        THEN null
                        ELSE julgada.cod_item
                    END AS cod_item
              FROM  empenho.pre_empenho
        INNER JOIN  empenho.item_pre_empenho
                ON  item_pre_empenho.exercicio = pre_empenho.exercicio
               AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
         LEFT JOIN  (	SELECT  item_pre_empenho_julgamento.exercicio
                             ,  item_pre_empenho_julgamento.cod_pre_empenho
                             ,  item_pre_empenho_julgamento.num_item
                             ,  item_pre_empenho_julgamento.cgm_fornecedor
                             ,  catalogo_item.cod_item
                             ,  catalogo_item.descricao
                             ,  unidade_medida.nom_unidade
                             ,  grandeza.nom_grandeza
                          FROM  empenho.item_pre_empenho_julgamento
                    INNER JOIN  almoxarifado.catalogo_item
                            ON	catalogo_item.cod_item = item_pre_empenho_julgamento.cod_item
                    INNER JOIN  administracao.unidade_medida
                            ON  unidade_medida.cod_unidade = catalogo_item.cod_unidade
                           AND  unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                    INNER JOIN  administracao.grandeza
                            ON  grandeza.cod_grandeza = catalogo_item.cod_grandeza
                    ) AS julgada
                ON  julgada.exercicio = item_pre_empenho.exercicio
               AND  julgada.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  julgada.num_item = item_pre_empenho.num_item
               AND  julgada.cgm_fornecedor = pre_empenho.cgm_beneficiario
         LEFT JOIN  (	SELECT 	item_pre_empenho.exercicio
                             ,  item_pre_empenho.cod_pre_empenho
                             ,  item_pre_empenho.num_item
                             ,  item_pre_empenho.nom_item AS descricao
                             ,  unidade_medida.nom_unidade
                             ,  grandeza.nom_grandeza
                          FROM  empenho.item_pre_empenho
                    INNER JOIN  administracao.unidade_medida
                            ON  unidade_medida.cod_unidade = item_pre_empenho.cod_unidade
                           AND  unidade_medida.cod_grandeza = item_pre_empenho.cod_grandeza
                    INNER JOIN  administracao.grandeza
                            ON  grandeza.cod_grandeza = item_pre_empenho.cod_grandeza
                    ) AS empenho_diverso
                ON  empenho_diverso.exercicio = item_pre_empenho.exercicio
               AND  empenho_diverso.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
               AND  empenho_diverso.num_item = item_pre_empenho.num_item
             WHERE  pre_empenho.exercicio = '".$this->getDado('exercicio')."'
               AND  pre_empenho.cod_pre_empenho = ".$this->getDado('cod_pre_empenho')."
               AND  item_pre_empenho.num_item = ".$this->getDado('num_item')."
        ";

        return $stSql;
    }

    public function recuperaItensOrdemCompra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaItensOrdemCompra",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaItensOrdemCompra()
    {
        $stSql = "
        SELECT ordem.cod_ordem
             , item_pre_empenho.num_item
             , item_pre_empenho.nom_item
             , item_pre_empenho.cod_pre_empenho
             , item_pre_empenho.exercicio
             , item_pre_empenho.quantidade AS qtde_empenhada
             , COALESCE(quantidade_oc.quantidade,0) AS qtde_em_oc
             , (item_pre_empenho.quantidade - COALESCE(quantidade_oc.quantidade,0)) AS qtde_disponivel
             , ROUND(item_pre_empenho.vl_total / item_pre_empenho.quantidade,2) AS vl_unitario
             , ordem_item.quantidade AS qtde_da_oc
             , (ordem_item.quantidade * ROUND(item_pre_empenho.vl_total / item_pre_empenho.quantidade,2)) AS vl_total_item
          FROM compras.ordem
    INNER JOIN empenho.empenho
            ON empenho.cod_empenho = ordem.cod_empenho
           AND empenho.exercicio = ordem.exercicio_empenho
    INNER JOIN empenho.item_pre_empenho
            ON item_pre_empenho.exercicio = empenho.exercicio
           AND item_pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
     LEFT JOIN ( SELECT empenho_anulado_item.exercicio
                      , empenho_anulado_item.cod_pre_empenho
                      , empenho_anulado_item.num_item
                      , SUM(vl_anulado) AS vl_anulado
                   FROM empenho.empenho_anulado_item
               GROUP BY empenho_anulado_item.exercicio
                      , empenho_anulado_item.cod_pre_empenho
                      , empenho_anulado_item.num_item
               ) AS empenho_anulado_item
            ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
           AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND empenho_anulado_item.num_item = item_pre_empenho.num_item
    INNER JOIN ( SELECT ordem_item.exercicio
                      , ordem_item.cod_entidade
                      , ordem_item.cod_ordem
                      , ordem_item.tipo
                      , ordem_item.num_item
                      , (ordem_item.vl_total - ordem_item_anulacao.vl_total) AS vl_total
                      , (ordem_item.quantidade - ordem_item_anulacao.quantidade) AS quantidade
                   FROM compras.ordem_item
              LEFT JOIN compras.ordem_item_anulacao
                     ON ordem_item_anulacao.exercicio = ordem_item.exercicio
                    AND ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                    AND ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                    AND ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                    AND ordem_item_anulacao.num_item = ordem_item.num_item
               ) AS ordem_item
            ON ordem_item.exercicio = ordem.exercicio
           AND ordem_item.cod_entidade = ordem.cod_entidade
           AND ordem_item.cod_ordem = ordem.cod_ordem
           AND ordem_item.tipo = ordem.tipo
           AND ordem_item.num_item = item_pre_empenho.num_item
     LEFT JOIN ( SELECT ordem_item.exercicio
                      , ordem_item.cod_pre_empenho
                      , ordem_item.num_item
                      , SUM(ordem_item.quantidade) AS quantidade
                   FROM compras.ordem_item
                  WHERE NOT EXISTS ( SELECT 1
                                       FROM compras.ordem_item_anulacao
                                      WHERE ordem_item_anulacao.exercicio = ordem_item.exercicio
                                        AND ordem_item_anulacao.cod_entidade = ordem_item.cod_entidade
                                        AND ordem_item_anulacao.cod_ordem = ordem_item.cod_ordem
                                        AND ordem_item_anulacao.num_item = ordem_item.num_item
                                        AND ordem_item_anulacao.cod_pre_empenho = ordem_item.cod_pre_empenho
                                   )
               GROUP BY ordem_item.exercicio
                      , ordem_item.cod_pre_empenho
                      , ordem_item.num_item
               ) AS quantidade_oc
            ON quantidade_oc.exercicio = item_pre_empenho.exercicio
           AND quantidade_oc.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
           AND quantidade_oc.num_item = item_pre_empenho.num_item ";

            if ( $this->getDado('cod_ordem') ) {
                $stFiltro.= ' ordem.cod_ordem = '.$this->getDado('cod_ordem').' AND ';
            }
            if ( $this->getDado('exercicio') ) {
                $stFiltro.= ' ordem.exercicio = '.$this->getDado('exercicio').' AND ';
            }
            if ( $this->getDado('cod_entidade') ) {
                $stFiltro.= ' ordem.cod_entidade = '.$this->getDado('cod_entidade').' AND ';
            }

            if ($stFiltro != '') {
                $stSql.= ' WHERE '.substr( $stFiltro,0,strlen($stFiltro)-4 );
            }

        return $stSql;
    }

    public function recuperaOrdemCompraFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaOrdemCompraFornecedor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaOrdemCompraFornecedor()
    {
        $stSql.= "
                  SELECT  ordem.cod_ordem
                       ,  ordem.exercicio
                       ,  ordem.cod_empenho
                       ,  ordem.cod_entidade
                       ,  ordem.nom_entidade
                       ,  ordem.exercicio_empenho
                       ,  ordem.cgm_beneficiario
                       ,  ordem.nom_cgm
                       ,  TO_CHAR(ordem.timestamp,'dd/mm/yyyy') AS dt_ordem
                    FROM  (	SELECT	pre_empenho.cgm_beneficiario
                                 ,  ordem_item.exercicio
                                 ,	ordem_item.cod_entidade
                                 ,	entidade_cgm.nom_cgm AS nom_entidade
                                 ,  ordem_item.cod_ordem
                                 ,	ordem.cod_empenho
                                 ,	ordem.exercicio_empenho
                                 ,  ordem.timestamp
                                 ,  fornecedor.nom_cgm
                               FROM 	compras.ordem
                        INNER JOIN  compras.ordem_item
                                ON  ordem_item.exercicio = ordem.exercicio
                               AND  ordem_item.cod_entidade = ordem.cod_entidade
                               AND  ordem_item.cod_ordem = ordem.cod_ordem
                        INNER JOIN  orcamento.entidade
                                ON  entidade.exercicio = ordem_item.exercicio
                               AND  entidade.cod_entidade = ordem.cod_entidade
                        INNER JOIN  sw_cgm AS entidade_cgm
                                ON  entidade_cgm.numcgm = entidade.numcgm
                        INNER JOIN  empenho.item_pre_empenho
                                   ON  item_pre_empenho.exercicio = ordem.exercicio_empenho
                                  AND  item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho
                                  AND  item_pre_empenho.num_item = ordem_item.num_item
                           INNER JOIN  empenho.pre_empenho
                                ON  pre_empenho.exercicio = item_pre_empenho.exercicio
                               AND  pre_empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                        INNER JOIN  sw_cgm AS fornecedor
                                   ON  fornecedor.numcgm = pre_empenho.cgm_beneficiario
                             GROUP BY  pre_empenho.cgm_beneficiario
                                 ,  ordem_item.exercicio
                                 ,	ordem_item.cod_entidade
                                 ,	entidade_cgm.nom_cgm
                                 ,  ordem_item.cod_ordem
                                 ,	ordem.cod_empenho
                                 ,	ordem.exercicio_empenho
                                 ,  ordem.timestamp
                                 ,  fornecedor.nom_cgm
                        ) AS ordem
                 WHERE  NOT EXISTS     (    SELECT     1
                                              FROM  compras.ordem_anulacao
                                             WHERE  ordem_anulacao.exercicio = ordem.exercicio
                                               AND  ordem_anulacao.cod_entidade = ordem.cod_entidade
                                               AND  ordem_anulacao.cod_ordem = ordem.cod_ordem
                                        )
        ";
        if ($this->getDado('exercicio'))
            $stSql .="  AND ordem.exercicio = '".$this->getDado('exercicio')."' \n";
        if ($this->getDado('cod_entidade'))
            $stSql .="   AND ordem.cod_entidade IN ( ".$this->getDado('cod_entidade')." ) \n";
        if ($this->getDado('cod_ordem'))
            $stSql .="  AND ordem.cod_ordem = ".$this->getDado('cod_ordem')." \n";

        if ($this->getDado('cod_empenho')) {
            $empenho = explode('/',$this->getDado('cod_empenho'));
            $stSql .=" AND ordem.cod_empenho = ".trim($empenho[0])." \n";
            $stSql .=" AND ordem.exercicio_empenho  = '".trim($empenho[1])."' \n";
        }

        return $stSql;

    }

    public function recuperaCentroCustoPorOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCentroCustoPorOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaCentroCustoPorOrdemCompra()
    {
        $stSql = "
            SELECT DISTINCT centro_custo.cod_centro
                        ,   centro_custo.centro_custo
            FROM compras.ordem
            INNER JOIN  compras.ordem_item
                    ON  ordem.cod_entidade = ordem_item.cod_entidade
                    AND ordem.cod_ordem = ordem_item.cod_ordem
                    AND ordem.exercicio = ordem_item.exercicio

            INNER JOIN  empenho.item_pre_empenho
                    ON  item_pre_empenho.exercicio = ordem.exercicio_empenho
                    AND  item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho

            INNER JOIN (SELECT  item_pre_empenho_julgamento.exercicio
                            ,  item_pre_empenho_julgamento.cod_pre_empenho
                            ,  item_pre_empenho_julgamento.num_item
                            ,  item_pre_empenho_julgamento.cod_item
                            ,  mapa_item.cod_centro
                            ,  centro_custo.descricao AS centro_custo
                         FROM  empenho.item_pre_empenho_julgamento

                        INNER JOIN  compras.julgamento_item
                                ON  julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio_julgamento
                                AND  julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                                AND  julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item
                                AND  julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                                AND  julgamento_item.lote = item_pre_empenho_julgamento.lote

                        INNER JOIN  compras.mapa_cotacao
                                ON  mapa_cotacao.cod_cotacao = julgamento_item.cod_cotacao
                                AND  mapa_cotacao.exercicio_cotacao = julgamento_item.exercicio

                        INNER JOIN  compras.mapa_item
                                ON  mapa_item.exercicio = mapa_cotacao.exercicio_mapa
                                AND  mapa_item.cod_mapa = mapa_cotacao.cod_mapa
                                AND  mapa_item.cod_item = julgamento_item.cod_item

                        INNER JOIN  almoxarifado.centro_custo
                                ON  centro_custo.cod_centro = mapa_item.cod_centro

                        WHERE  julgamento_item.ordem = 1 ) AS centro_custo
                    ON  centro_custo.exercicio = item_pre_empenho.exercicio
                    AND centro_custo.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                    AND centro_custo.num_item = item_pre_empenho.num_item
            WHERE ordem.cod_ordem = ".$this->getDado('cod_ordem');

        return $stSql;
    }

    public function recuperaMarcaPorOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMarcaPorOrdemCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaMarcaPorOrdemCompra()
    {
        $stSql = "
            SELECT DISTINCT centro_custo.cod_marca
                        ,   centro_custo.marca
            FROM compras.ordem
            INNER JOIN  compras.ordem_item
                    ON  ordem.cod_entidade = ordem_item.cod_entidade
                    AND ordem.cod_ordem = ordem_item.cod_ordem
                    AND ordem.exercicio = ordem_item.exercicio

            INNER JOIN  empenho.item_pre_empenho
                    ON  item_pre_empenho.exercicio = ordem.exercicio_empenho
                    AND  item_pre_empenho.cod_pre_empenho = ordem_item.cod_pre_empenho

            INNER JOIN (SELECT  item_pre_empenho_julgamento.exercicio
                            ,  item_pre_empenho_julgamento.cod_pre_empenho
                            ,  item_pre_empenho_julgamento.num_item
                            ,  item_pre_empenho_julgamento.cod_item
                            ,  cotacao_fornecedor_item.cod_marca
                            ,  marca.descricao AS marca

                        FROM  empenho.item_pre_empenho_julgamento

                        INNER JOIN  compras.julgamento_item
                                ON  julgamento_item.exercicio = item_pre_empenho_julgamento.exercicio_julgamento
                                AND  julgamento_item.cod_cotacao = item_pre_empenho_julgamento.cod_cotacao
                                AND  julgamento_item.cod_item = item_pre_empenho_julgamento.cod_item
                                AND  julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                                AND  julgamento_item.lote = item_pre_empenho_julgamento.lote

                        INNER JOIN  compras.cotacao_fornecedor_item
                                ON  julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                                AND  julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                                AND  julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                                AND  julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                                AND  julgamento_item.lote = cotacao_fornecedor_item.lote

                        INNER JOIN  almoxarifado.catalogo_item_marca
                                ON  cotacao_fornecedor_item.cod_item = catalogo_item_marca.cod_item
                                AND cotacao_fornecedor_item.cod_marca = catalogo_item_marca.cod_marca

                        INNER JOIN  almoxarifado.marca
                                ON  marca.cod_marca = catalogo_item_marca.cod_marca

                        WHERE  julgamento_item.ordem = 1 ) AS centro_custo
                    ON  centro_custo.exercicio = item_pre_empenho.exercicio
                    AND centro_custo.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                    AND centro_custo.num_item = item_pre_empenho.num_item
                WHERE ordem.cod_ordem = ".$this->getDado('cod_ordem');

        return $stSql;
    }

}
