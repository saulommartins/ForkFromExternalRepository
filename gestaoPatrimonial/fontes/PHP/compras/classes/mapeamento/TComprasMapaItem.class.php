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
  * Classe de mapeamento da tabela compras.mapa_item
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento

  * Casos de uso: uc-03.04.05
                  uc-03.05.26

  $Id: TComprasMapaItem.class.php 63865 2015-10-27 13:55:57Z franver $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  compras.mapa_item
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasMapaItem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TComprasMapaItem()
    {
        parent::Persistente();
        $this->setTabela("compras.mapa_item");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_mapa,exercicio_solicitacao,cod_entidade,cod_solicitacao,cod_centro,cod_item,lote');

        $this->AddCampo('exercicio'             , 'char'    , true  , '4'    , true  , true  , 'TComprasMapaSolicitacao');
        $this->AddCampo('cod_mapa'              , 'integer' , true  , ''     , true  , true  , 'TComprasMapaSolicitacao');
        $this->AddCampo('exercicio_solicitacao' , 'char'    , true  , '4'    , true  , true  , 'TComprasMapaSolicitacao');
        $this->AddCampo('cod_entidade'          , 'integer' , true  , ''     , true  , true  , 'TComprasMapaSolicitacao');
        $this->AddCampo('cod_solicitacao'       , 'integer' , true  , ''     , true  , true  , 'TComprasMapaSolicitacao');
        $this->AddCampo('cod_centro'            , 'integer' , true  , ''     , true  , true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('cod_item'              , 'integer' , true  , ''     , true  , true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('lote'                  , 'integer' , true  , ''     , true  , true  , false                    );
        $this->AddCampo('quantidade'            , 'numeric' , false , '14.4' , false , false , false                    );
        $this->AddCampo('vl_total'              , 'numeric' , false , '14.2' , false , false , false                    );
    }

    public function recuperaValorTotal(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValorTotal().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaValorTotal()
    {
        $stSql  = "    SELECT  mapa_item.cod_mapa                                                                 \n";
        $stSql .= "         ,  mapa_item.exercicio                                                                \n";
        $stSql .= "         ,  SUM(mapa_item.vl_total) - COALESCE(SUM(mapa_item_anulacao.vl_total),0) as vl_total \n";
        $stSql .= "                                                                                               \n";
        $stSql .= "      FROM  compras.mapa_item                                                                  \n";
        $stSql .= "                                                                                               \n";
        $stSql .= " LEFT JOIN  compras.mapa_item_anulacao                                                         \n";
        $stSql .= "        ON  mapa_item_anulacao.exercicio             = mapa_item.exercicio                     \n";
        $stSql .= "       AND  mapa_item_anulacao.cod_mapa              = mapa_item.cod_mapa                      \n";
        $stSql .= "       AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao         \n";
        $stSql .= "       AND  mapa_item_anulacao.cod_entidade          = mapa_item.cod_entidade                  \n";
        $stSql .= "       AND  mapa_item_anulacao.cod_solicitacao       = mapa_item.cod_solicitacao               \n";
        $stSql .= "       AND  mapa_item_anulacao.cod_centro            = mapa_item.cod_centro                    \n";
        $stSql .= "       AND  mapa_item_anulacao.cod_item              = mapa_item.cod_item                      \n";
        $stSql .= "       AND  mapa_item_anulacao.lote                  = mapa_item.lote                          \n";
        $stSql .= "                                                                                               \n";
        $stSql .= "     WHERE  mapa_item.cod_mapa IS NOT NULL                                                     \n";

        if ($this->getDado('cod_mapa')) {
            $stSql .=" AND  mapa_item.cod_mapa = ".$this->getDado('cod_mapa')." \n";
        }

        if ($this->getDado('exercicio')) {
            $stSql .=" AND  mapa_item.exercicio = '".$this->getDado('exercicio')."' \n";
        }

        $stSql .= "  GROUP BY  mapa_item.cod_mapa                 \n";
        $stSql .= "         ,  mapa_item.exercicio                \n";

        return $stSql;
    }

    public function recuperaItensProposta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensProposta().$stFiltro.$stOrdem.$stGroup;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItensProposta()
    {
       $stSql = "
           SELECT  mapa.cod_mapa
                ,  mapa.exercicio
                ,  mapa_item.cod_item
                ,  mapa_item.quantidade
                ,  mapa_item.vl_total
                ,  mapa_item.lote
                ,  ( mapa_item.vl_total::numeric  / coalesce(mapa_item.quantidade,1.0)::numeric )::numeric(14,2) as valor_referencia
                ,  0.00 as valor_unitario
                ,  0.00 as valor_total
                ,  catalogo_item.descricao_resumida
                ,  '' as data_validade
                ,  '' as cod_marca
                ,  '' as desc_marca

             FROM  compras.mapa

       INNER JOIN  compras.mapa_item
               ON  mapa_item.cod_mapa = mapa.cod_mapa
              AND  mapa_item.exercicio = mapa.exercicio

       INNER JOIN  almoxarifado.catalogo_item
               ON  catalogo_item.cod_item = mapa_item.cod_item";

        if ($this->getDado('cod_mapa') && $this->getDado('exercicio')) {
            $stSql .= " WHERE  mapa.cod_mapa  = ".$this->getDado('cod_mapa')."  \n";
            $stSql .= "   AND  mapa.exercicio = '".$this->getDado('exercicio')."' \n";
        }

        return $stSql;
    }

    public function recuperaItensPropostaAgrupado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensPropostaAgrupados().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItensPropostaAgrupados()
    {
       $stSql = "    select mapa_itens.cod_mapa
                     , mapa_itens.exercicio
                     , mapa_itens.cod_item
                     , mapa_itens.lote
                     , TRIM(mapa_itens.descricao_resumida) as descricao_resumida
                     , TRIM(mapa_itens.descricao) as descricao
                     , 0.00 as valor_unitario
                     , 0.00 as valor_total
                     , 0.00 as valor_referencia
                     , '' as data_validade
                     , '' as cod_marca
                     , '' as desc_marca
                     , mapa_itens.quantidade - coalesce ( mapa_item_anulacao.quantidade, 0.0000 ) as quantidade
                     , mapa_itens.vl_total   - coalesce ( mapa_item_anulacao.vl_total  , 0.00 ) as vl_total

                  from (
                       select mapa.cod_mapa
                             , mapa.exercicio
                             , mapa_item.cod_item
                             , mapa_item.lote
                             , catalogo_item.descricao_resumida
                             , catalogo_item.descricao
                             , sum( coalesce( mapa_item.quantidade, 0.0000) ) as quantidade
                             , sum( coalesce( mapa_item.vl_total, 0.00) )as vl_total

                          from compras.mapa

                         inner join compras.mapa_item
                            on mapa_item.cod_mapa  = mapa.cod_mapa
                           and mapa_item.exercicio = mapa.exercicio

                          JOIN compras.mapa_solicitacao
                            ON mapa_solicitacao.exercicio             = mapa_item.exercicio
                           AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                           AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                           AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                           AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao

                         inner join almoxarifado.catalogo_item
                            on catalogo_item.cod_item = mapa_item.cod_item

                         Group by mapa.cod_mapa
                                     , mapa.exercicio
                                     , mapa_item.cod_item
                                     , mapa_item.lote
                                     , catalogo_item.descricao_resumida
                                     , catalogo_item.descricao

                         ) as mapa_itens

                    ----- buscando as possiveis anulações
                    left join ( select mapa_item_anulacao.cod_mapa
                                   , mapa_item_anulacao.exercicio
                                   , mapa_item_anulacao.cod_item
                                   , mapa_item_anulacao.lote
                                   , sum ( mapa_item_anulacao.vl_total   ) as vl_total
                                   , sum ( mapa_item_anulacao.quantidade ) as quantidade
                                from compras.mapa_item_anulacao
                              group by cod_mapa
                                     , exercicio
                                     , cod_item
                                     , lote ) as mapa_item_anulacao
                         on ( mapa_itens.cod_mapa  = mapa_item_anulacao.cod_mapa
                        and   mapa_itens.exercicio = mapa_item_anulacao.exercicio
                        and   mapa_itens.cod_item  = mapa_item_anulacao.cod_item
                        and   mapa_itens.lote      = mapa_item_anulacao.lote

                            )
                where mapa_itens.quantidade - coalesce ( mapa_item_anulacao.quantidade, 0 ) > 0  \n";
        if ( $this->getDado('cod_mapa') && $this->getDado('exercicio') ) {
            $stSql .= "    and mapa_itens.cod_mapa = "   . $this->getDado('cod_mapa')   ."\n";
            $stSql .= "    and mapa_itens.exercicio = '" . $this->getDado('exercicio') . "'\n";
        }
            $stSql .= "    ORDER BY mapa_itens.cod_item";

        return $stSql;
    }

    public function recuperaItensEdital(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $this->setDado('stFiltroOrdem',$stFiltro.$stOrdem);
        $stSql = $this->montaRecuperaItensEdital();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItensEdital()
    {
       $stSql = " select mapa.cod_mapa
                , mapa.exercicio
                , mapa_item.cod_item
                , mapa_item.cod_entidade
                , sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0) as quantidade
                , sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0) as vl_total
                , mapa_item.lote
                , ( sum(mapa_item.vl_total)::numeric  / coalesce(sum(mapa_item.quantidade),1.0)::numeric )::numeric(14,2) as valor_referencia
                , 0.00 as valor_unitario
                , 0.00 as valor_total
                , catalogo_item.descricao_resumida
                , catalogo_item.descricao
                , unidade_medida.nom_unidade
             from compras.mapa
       inner join compras.mapa_item
               on mapa_item.cod_mapa = mapa.cod_mapa
              and mapa_item.exercicio = mapa.exercicio
        LEFT JOIN compras.mapa_item_anulacao
               ON mapa_item_anulacao.exercicio             = mapa_item.exercicio
              AND mapa_item_anulacao.cod_mapa              = mapa_item.cod_mapa
              AND mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
              AND mapa_item_anulacao.cod_entidade          = mapa_item.cod_entidade
              AND mapa_item_anulacao.cod_solicitacao       = mapa_item.cod_solicitacao
              AND mapa_item_anulacao.cod_centro            = mapa_item.cod_centro
              AND mapa_item_anulacao.cod_item              = mapa_item.cod_item
              AND mapa_item_anulacao.lote                  = mapa_item.lote
       inner join almoxarifado.catalogo_item
               on catalogo_item.cod_item = mapa_item.cod_item
       inner join administracao.unidade_medida
               on catalogo_item.cod_unidade  = unidade_medida.cod_unidade
              and catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
    ".$this->getDado('stFiltroOrdem')."
       group by mapa.cod_mapa
               ,mapa.exercicio
               ,mapa_item.cod_item
               ,mapa_item.cod_entidade
               ,mapa_item.lote
               ,catalogo_item.descricao_resumida
               ,catalogo_item.descricao
               ,unidade_medida.nom_unidade
    ";

        return $stSql;
    }

    public function recuperaFiltroItensEdital()
    {
        if ( $this->getDado('cod_mapa') && $this->getDado('exercicio') ) {
            $stFiltro .= " where mapa.cod_mapa = " . $this->getDado('cod_mapa') . " ";
            $stFiltro .= "   and mapa.exercicio = " . $this->getDado('exercicio');
        }

        return $stFiltro;
    }

    public function recuperaItensMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensMapa( ). $stFiltro . $stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItensMapa()
    {
        $stSql = "
    select solicitacao_item.exercicio as exercicio_solicitacao
         , solicitacao_item.cod_entidade
         , solicitacao_item.cod_solicitacao
         , solicitacao_item.cod_item
         , catalogo_item.descricao as item
         , unidade_medida.nom_unidade
         , unidade_medida.cod_unidade
         , solicitacao_item.complemento
         , solicitacao_item.cod_centro
         , centro_custo.descricao as centro_custo
         , despesa.cod_despesa     as dotacao
         , conta_despesa.descricao as dotacao_nom_conta
         , desdobramento.cod_conta  as conta_despesa
         , desdobramento.descricao  as nom_conta
         , desdobramento.cod_estrutural
         --- quantidades/valores
         , solicitacao_item.quantidade as quantidade_solicitada
          ----- quantidade do mapa - anulação
         , mapa_item.quantidade  - coalesce( anulacao_mapa.quantidade  , 0 ) as quantidade_mapa
         , mapa_item.exercicio
         , mapa_item.lote
         --- quantidade atendida para o item neste e em outros mapas
        , ( solicitacao_item.quantidade -  total_mapas.quantidade + ( mapa_item.quantidade - coalesce( anulacao_mapa.quantidade, 0 ) ) ) as quantidade_maxima
         --- reserva de saldos
         , reserva_saldos.cod_reserva
         , reserva_saldos.exercicio as exercicio_reserva
         , coalesce(reserva_saldos.vl_reserva, 0.00) as vl_reserva
         , coalesce( (  SELECT sum(lancamento_material.quantidade) as quantidade
                          FROM almoxarifado.estoque_material
                          JOIN almoxarifado.lancamento_material
                            on ( lancamento_material.cod_item         = estoque_material.cod_item
                           AND   lancamento_material.cod_marca        = estoque_material.cod_marca
                           AND   lancamento_material.cod_almoxarifado = estoque_material.cod_almoxarifado
                           AND   lancamento_material.cod_centro       = estoque_material.cod_centro )
                         where solicitacao_item.cod_item    = estoque_material.cod_item
                           AND solicitacao_item.cod_centro  = estoque_material.cod_centro
                           AND solicitacao.cod_almoxarifado = estoque_material.cod_almoxarifado )
                       , 0.0 ) as quantidade_estoque
          , mapa_item.vl_total - coalesce ( anulacao_mapa.vl_total, 0 )   as valor_total_mapa
          , reserva_solicitacao.cod_reserva as cod_reserva_solicitacao
          , reserva_solicitacao.exercicio   as exercicio_reserva_solicitacao
          , reserva_solicitacao.vl_reserva  as vl_reserva_solicitacao
      from compras.solicitacao_item
      join compras.solicitacao
        on ( solicitacao.exercicio       = solicitacao_item.exercicio
       and   solicitacao.cod_entidade    = solicitacao_item.cod_entidade
       and   solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao)
      join almoxarifado.catalogo_item
        on ( solicitacao_item.cod_item = catalogo_item.cod_item )
      join administracao.unidade_medida
        on ( catalogo_item.cod_unidade  = unidade_medida.cod_unidade
       and   catalogo_item.cod_grandeza = unidade_medida.cod_grandeza )
      join almoxarifado.centro_custo
        on ( solicitacao_item.cod_centro = centro_custo.cod_centro )
      join compras.mapa_item
        on ( solicitacao_item.exercicio       = mapa_item.exercicio
       and   solicitacao_item.cod_entidade    = mapa_item.cod_entidade
       and   solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
       and   solicitacao_item.cod_centro      = mapa_item.cod_centro
       and   solicitacao_item.cod_item        = mapa_item.cod_item )
      join (select mapa_item.exercicio
                 , mapa_item.cod_entidade
                 , mapa_item.cod_solicitacao
                 , mapa_item.cod_centro
                 , mapa_item.cod_item
                 , sum ( mapa_item.quantidade ) as quantidade
            from compras.mapa_item
            group by mapa_item.exercicio
                   , mapa_item.cod_entidade
                   , mapa_item.cod_solicitacao
                   , mapa_item.cod_centro
                   , mapa_item.cod_item
           ) as total_mapas

        on ( total_mapas.exercicio       = solicitacao_item.exercicio
       and   total_mapas.cod_entidade    = solicitacao_item.cod_entidade
       and   total_mapas.cod_solicitacao = solicitacao_item.cod_solicitacao
       and   total_mapas.cod_centro      = solicitacao_item.cod_centro
       and   total_mapas.cod_item        = solicitacao_item.cod_item )

     left join (  select solicitacao_homologada_reserva.exercicio
                         , solicitacao_homologada_reserva.cod_entidade
                         , solicitacao_homologada_reserva.cod_solicitacao
                         , solicitacao_homologada_reserva.cod_centro
                         , solicitacao_homologada_reserva.cod_item
                         , reserva_saldos.cod_reserva
                         , reserva_saldos.cod_despesa
                         , reserva_saldos.vl_reserva
                      from compras.solicitacao_homologada_reserva
                      join orcamento.reserva_saldos
                        on ( reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva
                       and   reserva_saldos.exercicio   = solicitacao_homologada_reserva.exercicio )
                      where not exists ( select 1
                                           from orcamento.reserva_saldos_anulada
                                          where reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva
                                            and reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio )
                  ) as reserva_solicitacao
              on ( reserva_solicitacao.exercicio       = solicitacao_item.exercicio
             and   reserva_solicitacao.cod_entidade    = solicitacao_item.cod_entidade
             and   reserva_solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao
             and   reserva_solicitacao.cod_centro      = solicitacao_item.cod_centro
             and   reserva_solicitacao.cod_item        = solicitacao_item.cod_item  )

       left join compras.solicitacao_item_dotacao
              on ( solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
               and solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
               and solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
               and solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
               and solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item )
       --- buscando a dotacao
       left join orcamento.despesa
              on ( solicitacao_item_dotacao.exercicio   = despesa.exercicio
             and   solicitacao_item_dotacao.cod_despesa = despesa.cod_despesa )
       left join orcamento.conta_despesa
              on ( conta_despesa.exercicio    = despesa.exercicio
             AND   conta_despesa.cod_conta    = despesa.cod_conta )
       ---- buscando o desdobramento
       left join orcamento.conta_despesa as desdobramento
       on (    desdobramento.exercicio    = solicitacao_item_dotacao.exercicio
           AND desdobramento.cod_conta    = solicitacao_item_dotacao.cod_conta )

       ---- buscando a reserva de saldos
       left join compras.mapa_item_reserva
                   on ( mapa_item.exercicio               = mapa_item_reserva.exercicio_mapa
                  and   mapa_item.cod_mapa                = mapa_item_reserva.cod_mapa
                  and   mapa_item.exercicio_solicitacao   = mapa_item_reserva.exercicio_solicitacao
                  and   mapa_item.cod_entidade            = mapa_item_reserva.cod_entidade
                  and   mapa_item.cod_solicitacao         = mapa_item_reserva.cod_solicitacao
                  and   mapa_item.cod_centro              = mapa_item_reserva.cod_centro
                  and   mapa_item.cod_item                = mapa_item_reserva.cod_item
                  and   mapa_item.lote                    = mapa_item_reserva.lote
                        )
       left join orcamento.reserva_saldos
              on (mapa_item_reserva.cod_reserva       = reserva_saldos.cod_reserva
             and  mapa_item_reserva.exercicio_reserva = reserva_saldos.exercicio )
       ---- buscando as anulações
       left join ( select mapa_item_anulacao.exercicio
                        , mapa_item_anulacao.cod_entidade
                        , mapa_item_anulacao.cod_solicitacao
                        , mapa_item_anulacao.cod_mapa
                        , mapa_item_anulacao.cod_centro
                        , mapa_item_anulacao.cod_item
                        , mapa_item_anulacao.exercicio_solicitacao
                        , mapa_item_anulacao.lote
                        , sum( vl_total ) as vl_total
                        , sum ( quantidade ) as quantidade
                     from compras.mapa_item_anulacao
                  group by mapa_item_anulacao.exercicio
                         , mapa_item_anulacao.cod_entidade
                         , mapa_item_anulacao.cod_solicitacao
                         , mapa_item_anulacao.cod_mapa
                         , mapa_item_anulacao.cod_centro
                         , mapa_item_anulacao.cod_item
                         , mapa_item_anulacao.exercicio_solicitacao
                         , mapa_item_anulacao.lote ) as anulacao_mapa
              on ( anulacao_mapa.exercicio             = mapa_item.exercicio
             and   anulacao_mapa.cod_entidade          = mapa_item.cod_entidade
             and   anulacao_mapa.cod_solicitacao       = mapa_item.cod_solicitacao
             and   anulacao_mapa.cod_mapa              = mapa_item.cod_mapa
             and   anulacao_mapa.cod_centro            = mapa_item.cod_centro
             and   anulacao_mapa.cod_item              = mapa_item.cod_item
             and   anulacao_mapa.exercicio_solicitacao = mapa_item.exercicio_solicitacao
             and   anulacao_mapa.lote                  = mapa_item.lote  )";
             if (Sessao::read('stAcaoTela') != 'anularProcessoLicitatorio') {
                 $stSql.= " where ( mapa_item.quantidade  - coalesce( anulacao_mapa.quantidade  , 0 ) ) > 0 ";
             }

            return $stSql;
    }

    public function recuperaItensCompraDireta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensCompraDireta().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }
    public function montaRecuperaItensCompraDireta()
    {
       $stSql = " select mapa.cod_mapa
                , mapa.exercicio
                , mapa_item.exercicio_solicitacao
                , mapa_item.cod_solicitacao
                , mapa_item.cod_centro
                , mapa_item.cod_item
                , mapa_item.lote
                , unidade_medida.nom_unidade
                , (SELECT   descricao
                     FROM   almoxarifado.centro_custo
                    WHERE   centro_custo.cod_centro = mapa_item.cod_centro
                  ) AS centro_custo_descricao
                , mapa.cod_objeto
                , (SELECT   descricao
                     FROM   compras.objeto
                    WHERE   cod_objeto = mapa.cod_objeto
                  ) AS objeto_descricao
                , trim(catalogo_item.descricao_resumida) as descricao_resumida
                , trim(catalogo_item.descricao) as descricao_completa
                , trim(solicitacao_item.complemento) as complemento
                , sum( mapa_item.quantidade)	 as quantidade
                , sum( ( mapa_item.vl_total / mapa_item.quantidade )::numeric(14,2) ) as valor_unitario
                , sum( mapa_item.vl_total ) as valor_total
                , ( SELECT  sum(mapa_item_anulacao.vl_total)
                      FROM  compras.mapa_item_anulacao
                     WHERE  mapa_item_anulacao.cod_mapa = mapa.cod_mapa
                       AND  mapa_item_anulacao.exercicio = mapa.exercicio
                       AND  mapa_item_anulacao.cod_item = mapa_item.cod_item
                       AND  mapa_item_anulacao.cod_centro = mapa_item.cod_centro
                  ) as vl_total_anulado
                , sum( mapa_item.quantidade) - COALESCE (( SELECT  sum(mapa_item_anulacao.quantidade)
                      FROM  compras.mapa_item_anulacao
                     WHERE  mapa_item_anulacao.cod_mapa = mapa.cod_mapa
                       AND  mapa_item_anulacao.exercicio = mapa.exercicio
                       AND  mapa_item_anulacao.cod_item = mapa_item.cod_item
                       AND  mapa_item_anulacao.cod_centro = mapa_item.cod_centro
                  ),0) as quantidade_real
                , sum( mapa_item.vl_total ) - coalesce(( SELECT  sum(mapa_item_anulacao.vl_total)
                      FROM  compras.mapa_item_anulacao
                     WHERE  mapa_item_anulacao.cod_mapa = mapa.cod_mapa
                       AND  mapa_item_anulacao.exercicio = mapa.exercicio
                       AND  mapa_item_anulacao.cod_item = mapa_item.cod_item
                       AND  mapa_item_anulacao.cod_centro = mapa_item.cod_centro
                  ),0) as valor_total_real
             from compras.mapa
       inner join compras.mapa_item
               on mapa_item.cod_mapa = mapa.cod_mapa
              and mapa_item.exercicio = mapa.exercicio
       inner join compras.solicitacao_item
               on solicitacao_item.exercicio = mapa_item.exercicio
              and solicitacao_item.cod_entidade  = mapa_item.cod_entidade
              and solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
              and solicitacao_item.cod_centro = mapa_item.cod_centro
              and solicitacao_item.cod_item = mapa_item.cod_item
       inner join almoxarifado.catalogo_item
               on catalogo_item.cod_item = mapa_item.cod_item
       inner join administracao.unidade_medida
               on unidade_medida.cod_unidade = catalogo_item.cod_unidade
              and unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
               ";
        if ( $this->getDado('cod_mapa') && $this->getDado('exercicio') ) {
            $stSql .= " where mapa.cod_mapa = " . $this->getDado('cod_mapa') . " ";
            $stSql .= "   and mapa.exercicio = " . $this->getDado('exercicio')."::varchar";
        }

        $stSql .= " group by mapa.cod_mapa, mapa.exercicio, mapa_item.exercicio_solicitacao, mapa_item.cod_solicitacao, mapa_item.lote, mapa_item.cod_item, mapa_item.cod_centro, mapa.cod_objeto, objeto_descricao, descricao_resumida, descricao_completa, solicitacao_item.complemento, unidade_medida.nom_unidade \n";
        $stSql .= " order by descricao_completa ";

        return $stSql;
    }

    /*
     * Método que retorna o valor médio dos ítens do mapa (valor de referência).
     *
     */

    public function recuperaValorReferenciaItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValorReferenciaItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaValorReferenciaItem()
    {
        $stSql = "
            SELECT
                    CAST
                    (
                     ((sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) / (sum(mapa_item.quantidade) - coalesce(sum(mapa_item_anulacao.quantidade),0))) as numeric(14,2)
                    ) as vl_referencia

              FROM  compras.mapa_item

        INNER JOIN  compras.mapa
                ON  mapa.cod_mapa  = mapa_item.cod_mapa
               AND  mapa.exercicio = mapa_item.exercicio

         LEFT JOIN  compras.mapa_item_anulacao
                ON  mapa_item_anulacao.exercicio             = mapa_item.exercicio
               AND  mapa_item_anulacao.cod_entidade          = mapa_item.cod_entidade
               AND  mapa_item_anulacao.cod_solicitacao       = mapa_item.cod_solicitacao
               AND  mapa_item_anulacao.cod_mapa              = mapa_item.cod_mapa
               AND  mapa_item_anulacao.cod_centro            = mapa_item.cod_centro
               AND  mapa_item_anulacao.cod_item              = mapa_item.cod_item
               AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
               AND  mapa_item_anulacao.lote                  = mapa_item.lote

             WHERE  1=1 ".
               ($this->getDado('cod_mapa')  ? " AND mapa_item.cod_mapa  = ".$this->getDado('cod_mapa')  : "").
               ($this->getDado('exercicio') ? " AND mapa_item.exercicio = ".$this->getDado('exercicio')."::VARCHAR" : "").
               ($this->getDado('lote')      ? " AND mapa_item.lote      = ".$this->getDado('lote')      : "").
               ($this->getDado('cod_item')  ? " AND mapa_item.cod_item  = ".$this->getDado('cod_item')  : "")."

          GROUP BY  mapa_item.cod_item";

        return $stSql;
    }

    /*
     * Método que retorna o valor médio dos ítens do mapa (valor de referência).
     *
     */

    public function recuperaValorReferenciaLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValorReferenciaLote().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaValorReferenciaLote()
    {
        $stSql = "
            SELECT
              CAST
              (
               ((sum(mapa_item.vl_total) - coalesce(sum(mapa_item_anulacao.vl_total),0)) ) as numeric(14,2)
              ) as vl_referencia

              FROM  compras.mapa_item

        INNER JOIN  compras.mapa
                ON  mapa.cod_mapa  = mapa_item.cod_mapa
               AND  mapa.exercicio = mapa_item.exercicio

         LEFT JOIN  compras.mapa_item_anulacao
                ON  mapa_item_anulacao.exercicio             = mapa_item.exercicio
               AND  mapa_item_anulacao.cod_entidade          = mapa_item.cod_entidade
               AND  mapa_item_anulacao.cod_solicitacao       = mapa_item.cod_solicitacao
               AND  mapa_item_anulacao.cod_mapa              = mapa_item.cod_mapa
               AND  mapa_item_anulacao.cod_centro            = mapa_item.cod_centro
               AND  mapa_item_anulacao.cod_item              = mapa_item.cod_item
               AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
               AND  mapa_item_anulacao.lote                  = mapa_item.lote

             WHERE  1=1 ".
               ($this->getDado('cod_mapa')  ? " AND mapa_item.cod_mapa  = ".$this->getDado('cod_mapa')  : "").
               ($this->getDado('exercicio') ? " AND mapa_item.exercicio = ".$this->getDado('exercicio')."::VARCHAR" : "''").
               ($this->getDado('lote')      ? " AND mapa_item.lote      = ".$this->getDado('lote')      : "").
               ($this->getDado('cod_item')  ? " AND mapa_item.cod_item  = ".$this->getDado('cod_item')  : "")."

          GROUP BY  mapa_item.cod_item";

        return $stSql;
    }

    public function recuperaNomeItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaNomeItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

            return $obErro;
    }

    public function montaRecuperaNomeItem()
    {
        $stSql = "
                SELECT catalogo_item.descricao_resumida AS nome
                FROM  almoxarifado.catalogo_item
                WHERE  catalogo_item.cod_item  =".($this->getDado('cod_item') ? $this->getDado('cod_item') : "0");

                return $stSql;
        }

    public function recuperaComplementoItemMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaComplementoItemMapa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    }

    public function montaRecuperaComplementoItemMapa()
    {
        $stSql = "SELECT solicitacao_item.complemento
                    FROM compras.mapa_item

              INNER JOIN compras.solicitacao_item
                      on (    solicitacao_item.exercicio = mapa_item.exercicio
                          and solicitacao_item.cod_entidade = mapa_item.cod_entidade
                          and solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
                          and solicitacao_item.cod_centro = mapa_item.cod_centro
                          and solicitacao_item.cod_item = mapa_item.cod_item
                         )

               WHERE 1=1 ".
               ($this->getDado('cod_mapa')     ? " AND mapa_item.cod_mapa     = ".$this->getDado('cod_mapa')     : "").
               ($this->getDado('exercicio')    ? " AND mapa_item.exercicio    = '".$this->getDado('exercicio')."'"   : "").
               ($this->getDado('lote')         ? " AND mapa_item.lote         = ".$this->getDado('lote')         : "").
               ($this->getDado('cod_item')     ? " AND mapa_item.cod_item     = ".$this->getDado('cod_item')     : "").
               ($this->getDado('cod_entidade') ? " AND mapa_item.cod_entidade = ".$this->getDado('cod_entidade') : "")."

               GROUP BY mapa_item.cod_item
                      , solicitacao_item.complemento";

        return $stSql;
    }

    public function recuperaItemSolicitacaoMapa(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaItemSolicitacaoMapa();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaItemSolicitacaoMapa()
    {
        $stSql = "
                  SELECT
                          cod_solicitacao
                       ,  exercicio_solicitacao
                       ,  cod_entidade
                       ,  cod_item
                       ,  nom_item
                       ,  nom_unidade
                       ,  complemento
                       ,  cod_centro
                       ,  centro_custo
                       ,  CASE WHEN vl_total IS NOT NULL THEN vl_total
                          ELSE vl_total_item
                          END as vl_total
                       ,  valor_unitario
                       ,  valor_anulado
                       ,  COALESCE(valor_unitario, 0.00) * (COALESCE(quantidade_mapa, 0.0000) - COALESCE(quantidade_mapa_anulada, 0.0000)) as valor_total_mapa
                       ,  quantidade
                       ,  quantidade_anulada
                       ,  quantidade_estoque
                       ,  quantidade_mapa
                       ,  quantidade_mapa_anulada
                       ,  quantidade_em_mapas
                       ,  quantidade_anulada_em_mapas
                       ,  ((quantidade - quantidade_anulada) - (quantidade_mapa - quantidade_mapa_anulada)) as quantidade_disponivel
                       ,  (quantidade_mapa - quantidade_mapa_anulada) as quantidade_maxima
                       ,  (quantidade - quantidade_anulada) as quantidade_solicitada
                       --,  (quantidade_mapa - quantidade_mapa_anulada) as quantidade_atendida
                       --,  ((quantidade - quantidade_anulada) - (quantidade_mapa - quantidade_mapa_anulada)) as quantidade_mapa
                       ,  vl_total_mapa_item
                       ,  dotacao
                       ,  dotacao_nom_conta
                       ,  conta_despesa
                       ,  nom_conta
                       ,  cod_estrutural
                       ,  vl_reserva
                       ,  cod_reserva
                       ,  exercicio_reserva
                       ,  cod_conta
                       ,  cod_despesa
                       ,  lote
                       ,  cod_reserva_solicitacao
                       ,  exercicio_reserva_solicitacao
                       ,  vl_reserva_solicitacao

                    FROM (

                      SELECT  solicitacao_item.exercicio as exercicio_solicitacao
                           ,  solicitacao_item.cod_entidade
                           ,  solicitacao_item.cod_solicitacao
                           ,  solicitacao_item.cod_item
                           ,  solicitacao_item.quantidade as quantidade_item

                           ,  catalogo_item.descricao as nom_item
                           ,  unidade_medida.nom_unidade

                           ,  solicitacao_item.vl_total as vl_total_item
                           ,  ((solicitacao_item.vl_total/solicitacao_item.quantidade)*solicitacao_item_dotacao.quantidade) as vl_total
                           ,  solicitacao_item.complemento
                           ,  solicitacao_item.cod_centro
                           ,  centro_custo.descricao as centro_custo

                           -- VALOR UNITARIO DO ITEM NA SOLICITAÇÃO OU MAPA.
                           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NOT NULL THEN
                                   (mapa_item_dotacao.vl_dotacao / mapa_item_dotacao.quantidade)
                                   WHEN solicitacao_item_dotacao IS NOT NULL THEN
                                   (solicitacao_item_dotacao.vl_reserva / solicitacao_item_dotacao.quantidade)
                                   ELSE
                                   (solicitacao_item.vl_total / solicitacao_item.quantidade)
                              END AS valor_unitario

                           -- ,  (solicitacao_item.vl_total / solicitacao_item.quantidade) as valor_unitario

                           -- QUANTIDADE DO ITEM NA SOLICITAÇÃO.
                           ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                                  COALESCE(solicitacao_item.quantidade, 0.00)
                              ELSE
                                  COALESCE(solicitacao_item_dotacao.quantidade, 0.00)
                              END AS quantidade

                           -- QUANTIDADE ANULADA DO ITEM NA SOLICITAÇÃO
                           ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                                (
                                  SELECT  COALESCE(SUM(solicitacao_item_anulacao.quantidade), 0.00) as quantidade
                                    FROM  compras.solicitacao_item_anulacao
                                   WHERE  solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                     AND  solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                     AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                     AND  solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                     AND  solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                )
                              ELSE
                                (
                                  SELECT  COALESCE(SUM(solicitacao_item_dotacao_anulacao.quantidade), 0.00) as quantidade
                                    FROM  compras.solicitacao_item_dotacao_anulacao
                                   WHERE  solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                     AND  solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                     AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                     AND  solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                     AND  solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                     AND  solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                     AND  solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                )
                              END AS quantidade_anulada

                           -- VALOR ANULADO DO ITEM NA SOLICITAÇÃO
                           ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                                (
                                    SELECT  COALESCE(SUM(solicitacao_item_anulacao.vl_total), 0.00) as vl_total
                                      FROM  compras.solicitacao_item_anulacao
                                     WHERE  solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                       AND  solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                       AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                       AND  solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                       AND  solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                )
                              ELSE
                                (
                                    SELECT  COALESCE(SUM(solicitacao_item_dotacao_anulacao.vl_anulacao), 0.00) as vl_total
                                      FROM  compras.solicitacao_item_dotacao_anulacao
                                     WHERE  solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                       AND  solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       AND  solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       AND  solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                       AND  solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       AND  solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                )
                              END AS valor_anulado

                           -- QUANTIDADE DO ITEM NO MAPA
                           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NOT NULL THEN
                                (
                                    SELECT  (COALESCE(SUM(mapa_item_dotacao.quantidade), 0.00)) as quantidade
                                      FROM  compras.mapa_item_dotacao
                                     WHERE  mapa_item_dotacao.exercicio       = solicitacao_item_dotacao.exercicio
                                       AND  mapa_item_dotacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       AND  mapa_item_dotacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       AND  mapa_item_dotacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       AND  mapa_item_dotacao.cod_item        = solicitacao_item_dotacao.cod_item
                                       AND  mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       AND  mapa_item_dotacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                       AND  mapa_item_dotacao.cod_mapa        = mapa_solicitacao.cod_mapa
                                       AND  mapa_item_dotacao.exercicio       = mapa_solicitacao.exercicio
                                )
                              WHEN solicitacao_item_dotacao.cod_despesa IS NOT NULL THEN
                                (
                                    SELECT  (COALESCE(SUM(SID.quantidade), 0.00)) as quantidade
                                      FROM  compras.solicitacao_item_dotacao AS SID
                                     WHERE  SID.exercicio       = solicitacao_item_dotacao.exercicio
                                       AND  SID.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       AND  SID.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       AND  SID.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       AND  SID.cod_item        = solicitacao_item_dotacao.cod_item
                                       AND  SID.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       AND  SID.cod_conta       = solicitacao_item_dotacao.cod_conta
                                )
                              ELSE
                                (
                                    SELECT  (COALESCE(SUM(mapa_item.quantidade), 0.00)) as quantidade
                                      FROM  compras.mapa_item
                                     WHERE  mapa_item.exercicio       = solicitacao_item.exercicio
                                       AND  mapa_item.cod_entidade    = solicitacao_item.cod_entidade
                                       AND  mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao
                                       AND  mapa_item.cod_centro      = solicitacao_item.cod_centro
                                       AND  mapa_item.cod_item        = solicitacao_item.cod_item
                                       AND  mapa_item.cod_mapa        = mapa_solicitacao.cod_mapa
                                       AND  mapa_item.exercicio       = mapa_solicitacao.exercicio
                                )
                              END AS quantidade_mapa

                           -- QUANTIDADE ANULADA DO MAPA
                           ,  (
                                    SELECT  (COALESCE(SUM(mapa_item_anulacao.quantidade), 0.00)) as quantidade

                                      FROM  compras.mapa_item_anulacao

                                INNER JOIN  compras.mapa_item_dotacao
                                        ON  mapa_item_dotacao.exercicio             = mapa_item_anulacao.exercicio
                                       AND  mapa_item_dotacao.cod_mapa              = mapa_item_anulacao.cod_mapa
                                       AND  mapa_item_dotacao.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
                                       AND  mapa_item_dotacao.cod_entidade          = mapa_item_anulacao.cod_entidade
                                       AND  mapa_item_dotacao.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
                                       AND  mapa_item_dotacao.cod_centro            = mapa_item_anulacao.cod_centro
                                       AND  mapa_item_dotacao.cod_item              = mapa_item_anulacao.cod_item
                                       AND  mapa_item_dotacao.lote                  = mapa_item_anulacao.lote
                                       AND  mapa_item_dotacao.cod_despesa           = mapa_item_anulacao.cod_despesa
                                       AND  mapa_item_dotacao.cod_conta             = mapa_item_anulacao.cod_conta

                                     WHERE  mapa_item_dotacao.exercicio             = solicitacao_item_dotacao.exercicio
                                       AND  mapa_item_dotacao.cod_entidade          = solicitacao_item_dotacao.cod_entidade
                                       AND  mapa_item_dotacao.cod_solicitacao       = solicitacao_item_dotacao.cod_solicitacao
                                       AND  mapa_item_dotacao.cod_centro            = solicitacao_item_dotacao.cod_centro
                                       AND  mapa_item_dotacao.cod_item              = solicitacao_item_dotacao.cod_item
                                       AND  mapa_item_dotacao.cod_despesa           = solicitacao_item_dotacao.cod_despesa
                                       AND  mapa_item_dotacao.cod_conta             = solicitacao_item_dotacao.cod_conta
                                       AND  mapa_item_dotacao.cod_mapa              = mapa_solicitacao.cod_mapa
                                       AND  mapa_item_dotacao.exercicio             = mapa_solicitacao.exercicio
                              ) as quantidade_mapa_anulada

                           -- QUANTIDADE DO ITEM EM OUTROS MAPAS
                           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN
                                (
                                    SELECT  (COALESCE(SUM(mapa_item.quantidade), 0.00)) as quantidade
                                      FROM  compras.mapa_item
                                     WHERE  mapa_item.exercicio       = solicitacao_item.exercicio
                                       AND  mapa_item.cod_entidade    = solicitacao_item.cod_entidade
                                       AND  mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao
                                       AND  mapa_item.cod_centro      = solicitacao_item.cod_centro
                                       AND  mapa_item.cod_item        = solicitacao_item.cod_item
                                       AND  mapa_item.cod_mapa        <> mapa_solicitacao.cod_mapa
                                       AND  mapa_item.exercicio       = mapa_solicitacao.exercicio
                                )
                              ELSE
                                (
                                    SELECT  (COALESCE(SUM(mapa_item_dotacao.quantidade), 0.00)) as quantidade
                                      FROM  compras.mapa_item_dotacao
                                     WHERE  mapa_item_dotacao.exercicio       = solicitacao_item_dotacao.exercicio
                                       AND  mapa_item_dotacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       AND  mapa_item_dotacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       AND  mapa_item_dotacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       AND  mapa_item_dotacao.cod_item        = solicitacao_item_dotacao.cod_item
                                       AND  mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       AND  mapa_item_dotacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                       AND  mapa_item_dotacao.cod_mapa        <> mapa_solicitacao.cod_mapa
                                       AND  mapa_item_dotacao.exercicio       = mapa_solicitacao.exercicio
                                )
                              END AS quantidade_em_mapas

                          -- QUANTIDADE ANULADA EM OUTROS MAPAS
                           ,  (
                                    SELECT  (COALESCE(SUM(mapa_item_anulacao.quantidade), 0.00)) as quantidade
                                      FROM  compras.mapa_item_anulacao
                                INNER JOIN  compras.mapa_item_dotacao
                                        ON  mapa_item_dotacao.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
                                       AND  mapa_item_dotacao.cod_entidade          = mapa_item_anulacao.cod_entidade
                                       AND  mapa_item_dotacao.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
                                       AND  mapa_item_dotacao.cod_centro            = mapa_item_anulacao.cod_centro
                                       AND  mapa_item_dotacao.cod_item              = mapa_item_anulacao.cod_item
                                       AND  mapa_item_dotacao.lote                  = mapa_item_anulacao.lote
                                       AND  mapa_item_dotacao.cod_despesa           = mapa_item_anulacao.cod_despesa
                                       AND  mapa_item_dotacao.cod_conta             = mapa_item_anulacao.cod_conta
                                     WHERE  mapa_item_dotacao.exercicio             = solicitacao_item_dotacao.exercicio
                                       AND  mapa_item_dotacao.cod_entidade          = solicitacao_item_dotacao.cod_entidade
                                       AND  mapa_item_dotacao.cod_solicitacao       = solicitacao_item_dotacao.cod_solicitacao
                                       AND  mapa_item_dotacao.cod_centro            = solicitacao_item_dotacao.cod_centro
                                       AND  mapa_item_dotacao.cod_item              = solicitacao_item_dotacao.cod_item
                                       AND  mapa_item_dotacao.cod_despesa           = solicitacao_item_dotacao.cod_despesa
                                       AND  mapa_item_dotacao.cod_conta             = solicitacao_item_dotacao.cod_conta
                              ) as quantidade_anulada_em_mapas

                           -- VALOR MAPEADO MENOS O VALOR ANULADO DO MAPA
                           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NOT NULL THEN
                                COALESCE (( 	SELECT  (COALESCE(SUM(mapa_item.vl_total ), 0.00)) -
                                                        (COALESCE(SUM(mapa_item_anulacao.vl_total), 0.00)) as valor
                                                      FROM  compras.mapa_item
                                                     LEFT JOIN  compras.mapa_item_anulacao
                                                        ON  mapa_item.exercicio             = mapa_item_anulacao.exercicio
                                                       AND  mapa_item.cod_mapa              = mapa_item_anulacao.cod_mapa
                                                       AND  mapa_item.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
                                                       AND  mapa_item.cod_entidade          = mapa_item_anulacao.cod_entidade
                                                       AND  mapa_item.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
                                                       AND  mapa_item.cod_centro            = mapa_item_anulacao.cod_centro
                                                       AND  mapa_item.cod_item              = mapa_item_anulacao.cod_item
                                                       AND  mapa_item.lote                  = mapa_item_anulacao.lote
                                                     WHERE  mapa_item.exercicio             = solicitacao_item.exercicio
                                                       AND  mapa_item.cod_entidade          = solicitacao_item.cod_entidade
                                                       AND  mapa_item.cod_solicitacao       = solicitacao_item.cod_solicitacao
                                                       AND  mapa_item.cod_centro            = solicitacao_item.cod_centro
                                                       AND  mapa_item.cod_item              = solicitacao_item.cod_item )
                                        , 0.0 ) 
                              ELSE 
                                (
                                    SELECT  (COALESCE(SUM(SID.vl_reserva), 0.00)) as valor
                                      FROM  compras.solicitacao_item_dotacao AS SID
                                     WHERE  SID.exercicio       = solicitacao_item_dotacao.exercicio
                                       AND  SID.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       AND  SID.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       AND  SID.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       AND  SID.cod_item        = solicitacao_item_dotacao.cod_item
                                       AND  SID.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       AND  SID.cod_conta       = solicitacao_item_dotacao.cod_conta
                                )
                              END AS vl_total_mapa_item

                           -- QUANTIDADE EM ESTOQUE
                           ,  (     SELECT  COALESCE(SUM(lancamento_material.quantidade), 0.0) as quantidade
                                      FROM  almoxarifado.estoque_material
                                INNER JOIN  almoxarifado.lancamento_material
                                        ON  lancamento_material.cod_item         = estoque_material.cod_item
                                       AND  lancamento_material.cod_marca        = estoque_material.cod_marca
                                       AND  lancamento_material.cod_almoxarifado = estoque_material.cod_almoxarifado
                                       AND  lancamento_material.cod_centro       = estoque_material.cod_centro
                                     WHERE  solicitacao_item.cod_item            = estoque_material.cod_item
                                       AND  solicitacao_item.cod_centro          = estoque_material.cod_centro
                                       AND  solicitacao.cod_almoxarifado         = estoque_material.cod_almoxarifado
                              ) AS quantidade_estoque

                           -- RECUPERA O COD_DESPESA
                           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN
                                solicitacao_item_dotacao.cod_despesa
                              ELSE
                                mapa_item_dotacao.cod_despesa
                              END AS dotacao

                           -- RECUPERA O COD_CONTA
                           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN
                                solicitacao_item_dotacao.cod_conta
                              ELSE
                                mapa_item_dotacao.cod_conta
                              END AS conta_despesa

                           ,  conta_despesa.descricao as dotacao_nom_conta
                           ,  desdobramento.descricao  as nom_conta
                           ,  desdobramento.cod_estrutural
                           ,  coalesce(reserva_saldos.vl_reserva, 0.00) as vl_reserva
                           ,  reserva_saldos.cod_reserva
                           ,  reserva_saldos.exercicio as exercicio_reserva
                           ,  desdobramento.cod_conta as cod_conta
                           ,  despesa.cod_despesa as cod_despesa
                           ,  mapa_item.lote

                              -- RESERVAS DA SOLICITAÇÃO DE COMPRAS
                           ,  solicitacao_homologada_reserva.cod_reserva as cod_reserva_solicitacao
                           ,  solicitacao_homologada_reserva.exercicio   as exercicio_reserva_solicitacao
                           ,  (
                                    SELECT  vl_reserva
                                      FROM  orcamento.reserva_saldos
                                     WHERE  reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva
                                       AND  reserva_saldos.exercicio   = solicitacao_homologada_reserva.exercicio
                              ) as vl_reserva_solicitacao

                       FROM  compras.mapa

                 INNER JOIN  compras.mapa_solicitacao
                         ON  mapa_solicitacao.exercicio = mapa.exercicio
                        AND  mapa_solicitacao.cod_mapa  = mapa.cod_mapa

                 INNER JOIN  compras.mapa_item
                         ON  mapa_item.exercicio             = mapa_solicitacao.exercicio
                        AND  mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                        AND  mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                        AND  mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                        AND  mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao

                   LEFT JOIN compras.mapa_item_dotacao
                          ON mapa_item.exercicio             = mapa_item_dotacao.exercicio
                         AND mapa_item.cod_mapa              = mapa_item_dotacao.cod_mapa
                         AND mapa_item.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                         AND mapa_item.cod_entidade          = mapa_item_dotacao.cod_entidade
                         AND mapa_item.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                         AND mapa_item.cod_centro            = mapa_item_dotacao.cod_centro
                         AND mapa_item.cod_item              = mapa_item_dotacao.cod_item
                         AND mapa_item.lote                  = mapa_item_dotacao.lote

                 INNER JOIN compras.solicitacao_item
                         ON solicitacao_item.exercicio       =  mapa_item.exercicio
                        AND solicitacao_item.cod_entidade    =  mapa_item.cod_entidade
                        AND solicitacao_item.cod_solicitacao =  mapa_item.cod_solicitacao
                        AND solicitacao_item.cod_centro      =  mapa_item.cod_centro
                        AND solicitacao_item.cod_item        =  mapa_item.cod_item

                   LEFT JOIN compras.solicitacao_item_dotacao
                          ON solicitacao_item.exercicio       = solicitacao_item_dotacao.exercicio
                         AND solicitacao_item.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                         AND solicitacao_item.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                         AND solicitacao_item.cod_centro      = solicitacao_item_dotacao.cod_centro
                         AND solicitacao_item.cod_item        = solicitacao_item_dotacao.cod_item
                         AND mapa_item_dotacao.cod_conta      = solicitacao_item_dotacao.cod_conta
                         AND mapa_item_dotacao.cod_despesa    = solicitacao_item_dotacao.cod_despesa

                 INNER JOIN  compras.solicitacao
                         ON  solicitacao.exercicio       = solicitacao_item.exercicio
                        AND  solicitacao.cod_entidade    = solicitacao_item.cod_entidade
                        AND  solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao

                 INNER JOIN  almoxarifado.catalogo_item
                         ON  solicitacao_item.cod_item = catalogo_item.cod_item

                 INNER JOIN  administracao.unidade_medida
                         ON  catalogo_item.cod_unidade  = unidade_medida.cod_unidade
                        AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza

                 INNER JOIN  almoxarifado.centro_custo
                         ON  solicitacao_item.cod_centro = centro_custo.cod_centro

                  LEFT JOIN  compras.solicitacao_homologada_reserva
                         ON  solicitacao_homologada_reserva.exercicio       = solicitacao_item_dotacao.exercicio
                        AND  solicitacao_homologada_reserva.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                        AND  solicitacao_homologada_reserva.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                        AND  solicitacao_homologada_reserva.cod_centro      = solicitacao_item_dotacao.cod_centro
                        AND  solicitacao_homologada_reserva.cod_item        = solicitacao_item_dotacao.cod_item
                        AND  solicitacao_homologada_reserva.cod_conta       = solicitacao_item_dotacao.cod_conta
                        AND  solicitacao_homologada_reserva.cod_despesa     = solicitacao_item_dotacao.cod_despesa

                         --  BUSCANDO A DOTACAO
                  LEFT JOIN  orcamento.despesa
                         ON  (			mapa_item_dotacao.exercicio   = despesa.exercicio
                        		AND  	mapa_item_dotacao.cod_despesa = despesa.cod_despesa
                             )
                         OR  (			solicitacao_item_dotacao.exercicio   = despesa.exercicio
                        		AND  	solicitacao_item_dotacao.cod_despesa = despesa.cod_despesa
                             )

                  LEFT JOIN  orcamento.conta_despesa
                         ON  conta_despesa.exercicio = despesa.exercicio
                        AND  conta_despesa.cod_conta = despesa.cod_conta

                         --  BUSCANDO O DESDOBRAMENTO
                  LEFT JOIN  orcamento.conta_despesa as desdobramento
                         ON  (			mapa_item_dotacao.exercicio = desdobramento.exercicio
                        		AND  	mapa_item_dotacao.cod_conta = desdobramento.cod_conta
                             )
                         OR  (			solicitacao_item_dotacao.exercicio = desdobramento.exercicio
                        		AND  	solicitacao_item_dotacao.cod_conta = desdobramento.cod_conta
                             )

                         --  BUSCANDO A RESERVA DE SALDOS
                  LEFT JOIN  compras.mapa_item_reserva
                         ON  mapa_item_reserva.exercicio_mapa        = mapa_item_dotacao.exercicio
                        AND  mapa_item_reserva.cod_mapa              = mapa_item_dotacao.cod_mapa
                        AND  mapa_item_reserva.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                        AND  mapa_item_reserva.cod_entidade          = mapa_item_dotacao.cod_entidade
                        AND  mapa_item_reserva.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                        AND  mapa_item_reserva.cod_centro            = mapa_item_dotacao.cod_centro
                        AND  mapa_item_reserva.cod_item              = mapa_item_dotacao.cod_item
                        AND  mapa_item_reserva.lote                  = mapa_item_dotacao.lote
                        AND  mapa_item_reserva.cod_despesa           = mapa_item_dotacao.cod_despesa
                        AND  mapa_item_reserva.cod_conta             = mapa_item_dotacao.cod_conta

                  LEFT JOIN  orcamento.reserva_saldos
                         ON  (			mapa_item_reserva.cod_reserva       = reserva_saldos.cod_reserva
                        		AND  	mapa_item_reserva.exercicio_reserva = reserva_saldos.exercicio
                             )
                         OR  (			mapa_item_reserva.cod_reserva IS NULL
                        		AND  	solicitacao_homologada_reserva.cod_reserva = reserva_saldos.cod_reserva
                        		AND  	solicitacao_homologada_reserva.exercicio   = reserva_saldos.exercicio
                             )

                      WHERE  1=1 ";

                if ($this->getDado('cod_solicitacao'))
                    $stSql .= " AND  solicitacao_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')." \n";

                if ($this->getDado('cod_entidade'))
                    $stSql .= " AND  solicitacao_item.cod_entidade = ".$this->getDado('cod_entidade')." \n";

                if ($this->getDado('exercicio_solicitacao'))
                    $stSql .= " AND  solicitacao_item.exercicio = '".$this->getDado('exercicio_solicitacao')."' \n";

                if ($this->getDado('cod_item'))
                    $stSql .= " AND  solicitacao_item.cod_item = ".$this->getDado('cod_item')." \n";

                if ($this->getDado('cod_centro'))
                    $stSql .= " AND  solicitaca_item.cod_centro = ".$this->getDado('cod_centro')." \n";

                if ($this->getDado('cod_mapa'))
                    $stSql .= " AND  mapa_solicitacao.cod_mapa = ".$this->getDado('cod_mapa')." \n";

                if ($this->getDado('exercicio_mapa'))
                    $stSql .= " AND  mapa_solicitacao.exercicio = '".$this->getDado('exercicio_mapa')."' \n";

                $stSql .= "
                  ) as itens

             WHERE  1=1
                --  TESTE QUE VERIFICA SE EXISTE SALDO (QTDE) PARA O ITEM ENTRAR NO MAPA
               AND  (quantidade - quantidade_anulada) - (quantidade_em_mapas - quantidade_anulada_em_mapas) > 0";

                # -- (quantidade - qtd_anulada) > 0
                # --  AND  (quantidade_mapa + quantidade_anulada_mapa) > 0 ";

        return $stSql;
    }

    # Recupera a quantidade do item de determinada solicitação que possa
    # estar em outro Mapa de Compras.
    public function recuperaQtdeAtendidaEmMapas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaQtdeAtendidaEmMapas().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaQtdeAtendidaEmMapas()
    {
        $stSql  = "
            SELECT
                   CASE WHEN SUM(mapa_item_dotacao.quantidade) IS NOT NULL THEN
                        COALESCE(SUM(mapa_item_dotacao.quantidade), 0.0000) -
                        COALESCE(SUM(
                                     ( SELECT  SUM(mapa_item_anulacao.quantidade)
                                         FROM  compras.mapa_item_anulacao
                                        WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                                          AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                                          AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                                          AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                                          AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                                          AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                                          AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                                          AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                                          AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                                          AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa )
                                  ), 0.0000)
                   ELSE
                        COALESCE(SUM(mapa_item.quantidade), 0.0000) -
                        COALESCE(SUM(
                                     ( SELECT  SUM(mapa_item_anulacao.quantidade)
                                         FROM  compras.mapa_item_anulacao
                                        WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                                          AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                                          AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                                          AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                                          AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                                          AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                                          AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                                          AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                                          AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                                          AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa )
                                  ), 0.0000)
                   END AS qtde_atendida

                ,  COALESCE(SUM(mapa_item_dotacao.quantidade), 0.0000) - COALESCE(SUM(
                        ( SELECT  SUM(mapa_item_anulacao.quantidade)
                            FROM  compras.mapa_item_anulacao
                           WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                             AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                             AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                             AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                             AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                             AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                             AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                             AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                             AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                             AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa )
                     ), 0.0000) AS qtde_em_mapas

                ,   SUM
                    (
                        ( SELECT  SUM(mapa_item_anulacao.quantidade)
                            FROM  compras.mapa_item_anulacao
                           WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                             AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                             AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                             AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                             AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                             AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                             AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                             AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                             AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta
                             AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa )
                   ) as qtde_anulado_em_mapas

             FROM  compras.mapa_item

       INNER JOIN  compras.mapa_solicitacao
               ON  mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio
              AND  mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
              AND  mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
              AND  mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
              AND  mapa_solicitacao.exercicio             = mapa_item.exercicio

        LEFT JOIN  compras.mapa_item_dotacao
               ON  mapa_item_dotacao.exercicio             = mapa_item.exercicio
              AND  mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
              AND  mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
              AND  mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
              AND  mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
              AND  mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
              AND  mapa_item_dotacao.cod_item              = mapa_item.cod_item
              AND  mapa_item_dotacao.lote                  = mapa_item.lote

            WHERE  1=1";

        return $stSql;

    }

    # Método utilizado ao incluir uma Solicitação de Compra em um novo Mapa.
    public function recuperaIncluirSolicitacaoMapa(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaIncluirSolicitacaoMapa();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaIncluirSolicitacaoMapa()
    {
        $stSql = "
                SELECT  *
                  FROM
                  (
                      SELECT
                           -- DADOS DA SOLICITAÇÃO
                              solicitacao_item.cod_solicitacao
                           ,  solicitacao_item.exercicio as exercicio_solicitacao
                           ,  solicitacao_item.cod_entidade

                           -- DADOS DO ITEM
                           ,  solicitacao_item.cod_item
                           ,  catalogo_item.descricao as nom_item
                           ,  unidade_medida.nom_unidade
                           ,  solicitacao_item.complemento
                           ,  solicitacao_item.cod_centro
                           ,  centro_custo.descricao as centro_custo

                           -- VALOR UNITARIO DO ITEM NA SOLICITAÇÃO OU MAPA.
                           ,  COALESCE(solicitacao_item_dotacao.vl_reserva, 0.00) as vl_total
                           ,  CASE WHEN solicitacao_item_dotacao IS NOT NULL THEN
                                   COALESCE((solicitacao_item_dotacao.vl_reserva / solicitacao_item_dotacao.quantidade), 0.00)::numeric(14,4)
                                   ELSE
                                   COALESCE((solicitacao_item.vl_total / solicitacao_item.quantidade), 0.00)::numeric(14,4)
                              END AS valor_unitario

                           -- VALOR ANULADO DO ITEM NA SOLICITAÇÃO
                           ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                                (
                                    SELECT  COALESCE(SUM(solicitacao_item_anulacao.vl_total), 0.00) as vl_total
                                      FROM  compras.solicitacao_item_anulacao
                                     WHERE  solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                       AND  solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                       AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                       AND  solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                       AND  solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                )
                              ELSE
                                (
                                    SELECT  COALESCE(SUM(solicitacao_item_dotacao_anulacao.vl_anulacao), 0.00) as vl_total
                                      FROM  compras.solicitacao_item_dotacao_anulacao
                                     WHERE  solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                       AND  solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       AND  solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       AND  solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                       AND  solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       AND  solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                )
                              END AS valor_anulado

                           -- QUANTIDADE DO ITEM NA SOLICITAÇÃO.
                           ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                                  COALESCE(solicitacao_item.quantidade, 0.00)
                              ELSE
                                  COALESCE(solicitacao_item_dotacao.quantidade, 0.00)
                              END AS quantidade

                           -- QUANTIDADE ANULADA DO ITEM NA SOLICITAÇÃO
                           ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                                (
                                  SELECT  COALESCE(SUM(solicitacao_item_anulacao.quantidade), 0.00) as quantidade
                                    FROM  compras.solicitacao_item_anulacao
                                   WHERE  solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                     AND  solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                     AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                     AND  solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                     AND  solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                )
                              ELSE
                                (
                                  SELECT  COALESCE(SUM(solicitacao_item_dotacao_anulacao.quantidade), 0.00) as quantidade
                                    FROM  compras.solicitacao_item_dotacao_anulacao
                                   WHERE  solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                     AND  solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                     AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                     AND  solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                     AND  solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                     AND  solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                     AND  solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                )
                              END AS quantidade_anulada

                           -- QUANTIDADE EM ESTOQUE DO ITEM
                           ,  (     SELECT  COALESCE(SUM(lancamento_material.quantidade), 0.0) as quantidade
                                      FROM  almoxarifado.estoque_material
                                INNER JOIN  almoxarifado.lancamento_material
                                        ON  lancamento_material.cod_item         = estoque_material.cod_item
                                       AND  lancamento_material.cod_marca        = estoque_material.cod_marca
                                       AND  lancamento_material.cod_almoxarifado = estoque_material.cod_almoxarifado
                                       AND  lancamento_material.cod_centro       = estoque_material.cod_centro
                                     WHERE  solicitacao_item.cod_item            = estoque_material.cod_item
                                       AND  solicitacao_item.cod_centro          = estoque_material.cod_centro
                                       AND  solicitacao.cod_almoxarifado         = estoque_material.cod_almoxarifado
                              ) AS quantidade_estoque

                           -- DADOS DA RESERVA
                           ,  reserva_saldos.cod_reserva
                           ,  coalesce(reserva_saldos.vl_reserva, 0.00) as vl_reserva
                           ,  reserva_saldos.exercicio as exercicio_reserva

                           -- DADOS DA DOTAÇÃO
                           ,  despesa.cod_despesa as cod_despesa
                           ,  conta_despesa.descricao as dotacao_nom_conta
                           ,  desdobramento.cod_conta as cod_conta
                           ,  desdobramento.cod_estrutural
                           ,  desdobramento.descricao  as nom_conta

                       FROM  compras.solicitacao_item

                 INNER JOIN  compras.solicitacao
                         ON  solicitacao.exercicio       = solicitacao_item.exercicio
                        AND  solicitacao.cod_entidade    = solicitacao_item.cod_entidade
                        AND  solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao

                 INNER JOIN  almoxarifado.catalogo_item
                         ON  solicitacao_item.cod_item = catalogo_item.cod_item

                 INNER JOIN  administracao.unidade_medida
                         ON  catalogo_item.cod_unidade  = unidade_medida.cod_unidade
                        AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza

                 INNER JOIN  almoxarifado.centro_custo
                         ON  solicitacao_item.cod_centro = centro_custo.cod_centro

                  LEFT JOIN  compras.solicitacao_item_dotacao
                         ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                        AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                        AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                        AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                        AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item

                         --  BUSCANDO A RESERVA DE SALDOS
                  LEFT JOIN  compras.solicitacao_homologada_reserva
                         ON  solicitacao_item_dotacao.exercicio       = solicitacao_homologada_reserva.exercicio
                        AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_homologada_reserva.cod_entidade
                        AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_homologada_reserva.cod_solicitacao
                        AND  solicitacao_item_dotacao.cod_centro      = solicitacao_homologada_reserva.cod_centro
                        AND  solicitacao_item_dotacao.cod_item        = solicitacao_homologada_reserva.cod_item
                        AND  solicitacao_item_dotacao.cod_despesa     = solicitacao_homologada_reserva.cod_despesa
                        AND  solicitacao_item_dotacao.cod_conta       = solicitacao_homologada_reserva.cod_conta

                         --  BUSCANDO A DOTACAO
                  LEFT JOIN  orcamento.despesa
                         ON  solicitacao_item_dotacao.exercicio   = despesa.exercicio
                        AND  solicitacao_item_dotacao.cod_despesa = despesa.cod_despesa

                         --  BUSCANDO O DESDOBRAMENTO
                  LEFT JOIN  orcamento.conta_despesa
                         ON  conta_despesa.exercicio = despesa.exercicio
                        AND  conta_despesa.cod_conta = despesa.cod_conta

                  LEFT JOIN  orcamento.conta_despesa as desdobramento
                         ON  desdobramento.exercicio = solicitacao_item_dotacao.exercicio
                        AND  desdobramento.cod_conta = solicitacao_item_dotacao.cod_conta

                  LEFT JOIN  orcamento.reserva_saldos
                         ON  solicitacao_homologada_reserva.cod_reserva = reserva_saldos.cod_reserva
                        AND  solicitacao_homologada_reserva.exercicio   = reserva_saldos.exercicio

                      WHERE  1=1 ";

                if ($this->getDado('cod_solicitacao'))
                    $stSql .= " AND  solicitacao_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')." \n";

                if ($this->getDado('cod_entidade'))
                    $stSql .= " AND  solicitacao_item.cod_entidade = ".$this->getDado('cod_entidade')." \n";

                if ($this->getDado('exercicio_solicitacao'))
                    $stSql .= " AND  solicitacao_item.exercicio = '".$this->getDado('exercicio_solicitacao')."' \n";

                if ($this->getDado('cod_item'))
                    $stSql .= " AND  solicitacao_item.cod_item = ".$this->getDado('cod_item')." \n";

                if ($this->getDado('cod_centro'))
                    $stSql .= " AND  solicitaca_item.cod_centro = ".$this->getDado('cod_centro')." \n";

                $stSql .= "

                 ) as solicitacao_item

             WHERE  1=1

                --  LISTA SOMENTE ITENS COM SALDO DA SOLICITAÇÃO (NÃO ANULADOS)
               AND  (quantidade - quantidade_anulada) > 0";

        return $stSql;
    }

}

?>
