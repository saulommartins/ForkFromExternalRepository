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
    * Classe de mapeamento da tabela compras.mapa_item_dotacao
    * Data de Criação: 02/12/2008

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela compras.mapa_item_dotacao
  * Data de Criação: 02/12/2008

  * Data de Criação: 02/12/2008

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage Mapeamento
*/

class TComprasMapaItemDotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasMapaItemDotacao()
{
    parent::Persistente();
    $this->setTabela("compras.mapa_item_dotacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_mapa, exercicio_solicitacao, cod_entidade, cod_solicitacao, cod_centro, cod_item, lote, cod_conta, cod_despesa');

    $this->AddCampo('exercicio'             , 'char'    , true  , '4'    , true , true  , 'TComprasMapaItem');
    $this->AddCampo('cod_mapa'              , 'integer' , true  , ''     , true , true  , 'TComprasMapaItem');
    $this->AddCampo('exercicio_solicitacao' , 'char'    , true  , '4'    , true , true  , 'TComprasMapaItem');
    $this->AddCampo('cod_entidade'          , 'integer' , true  , ''     , true , true  , 'TComprasMapaItem');
    $this->AddCampo('cod_solicitacao'       , 'integer' , true  , ''     , true , true  , 'TComprasMapaItem');
    $this->AddCampo('cod_centro'            , 'integer' , true  , ''     , true , true  , 'TComprasMapaItem');
    $this->AddCampo('cod_item'              , 'integer' , true  , ''     , true , true  , 'TComprasMapaItem');
    $this->AddCampo('lote'                  , 'integer' , true  , ''     , true , true  , 'TComprasMapaItem');
    $this->AddCampo('cod_conta'             , 'integer' , true  , ''     , true , true  , 'TComprasSolicitacaoItemDotacao');
    $this->AddCampo('cod_despesa'           , 'integer' , true  , ''     , true , true  , 'TComprasSolicitacaoItemDotacao');
    $this->AddCampo('quantidade'            , 'numeric' , false , '14.4' , false , false , false                    );
    $this->AddCampo('vl_dotacao'            , 'numeric' , false , '14.2' , false , false , false                    );

}

function recuperaItensMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaItensMapa( ). $stFiltro . $stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaItensMapa()
{
    $stSql = "
        SELECT  solicitacao_item.exercicio as exercicio_solicitacao
             ,  solicitacao_item.cod_entidade
             ,  solicitacao_item.cod_solicitacao
             ,  solicitacao_item.complemento
             ,  solicitacao_item_dotacao.cod_item
             ,  solicitacao_item_dotacao.cod_despesa as dotacao
             ,  solicitacao_item_dotacao.cod_conta as conta_despesa
             ,  solicitacao_item_dotacao.cod_centro
             ,  catalogo_item.descricao as item
             ,  unidade_medida.nom_unidade
             ,  unidade_medida.cod_unidade
             ,  centro_custo.descricao as centro_custo
             ,  conta_despesa.descricao as dotacao_nom_conta
             ,  desdobramento.descricao  as nom_conta
             ,  desdobramento.cod_estrutural

             -- quantidades/valores
             ,  solicitacao_item_dotacao.quantidade - coalesce(  anulacao_solicitacao.quantidade  , 0.0000 ) as quantidade_solicitada

             -- quantidade do mapa - anulação
             ,  mapa_item_dotacao.quantidade  - coalesce( anulacao_mapa.quantidade  , 0 ) as quantidade_mapa
             ,  mapa_item_dotacao.exercicio
             ,  mapa_item_dotacao.lote

             --- quantidade atendida para o item neste e em outros mapas
             ,  ( solicitacao_item_dotacao.quantidade -  total_mapas.quantidade + ( mapa_item_dotacao.quantidade - coalesce( anulacao_mapa.quantidade, 0 ) ) ) as quantidade_maxima
             ,  mapa_item_dotacao.vl_dotacao - coalesce ( anulacao_mapa.vl_total, 0 )   as valor_total_mapa

             --- reserva de saldos
             ,  reserva_saldos.cod_reserva
             ,  reserva_saldos.exercicio as exercicio_reserva
             ,  coalesce(reserva_saldos.vl_reserva, 0.00) as vl_reserva
             ,  coalesce((  SELECT sum(lancamento_material.quantidade) as quantidade
                              FROM almoxarifado.estoque_material
                              JOIN almoxarifado.lancamento_material
                                ON ( lancamento_material.cod_item         = estoque_material.cod_item
                               AND   lancamento_material.cod_marca        = estoque_material.cod_marca
                               AND   lancamento_material.cod_almoxarifado = estoque_material.cod_almoxarifado
                               AND   lancamento_material.cod_centro       = estoque_material.cod_centro )
                             WHERE solicitacao_item.cod_item    = estoque_material.cod_item
                               AND solicitacao_item.cod_centro  = estoque_material.cod_centro
                               AND solicitacao.cod_almoxarifado = estoque_material.cod_almoxarifado )
                           , 0.0 ) as quantidade_estoque
             ,  reserva_solicitacao.cod_reserva as cod_reserva_solicitacao
             ,  reserva_solicitacao.exercicio   as exercicio_reserva_solicitacao
             ,  reserva_solicitacao.vl_reserva  as vl_reserva_solicitacao

          FROM  compras.solicitacao_item

    INNER JOIN  compras.solicitacao
            ON  solicitacao.exercicio       = solicitacao_item.exercicio
           AND  solicitacao.cod_entidade    = solicitacao_item.cod_entidade
           AND  solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao

    LEFT JOIN  compras.solicitacao_item_dotacao
            ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
           AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
           AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
           AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
           AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item

    INNER JOIN  almoxarifado.catalogo_item
            ON  solicitacao_item.cod_item = catalogo_item.cod_item

    INNER JOIN  administracao.unidade_medida
            ON  catalogo_item.cod_unidade  = unidade_medida.cod_unidade
           AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza

    INNER JOIN  almoxarifado.centro_custo
            ON  solicitacao_item.cod_centro = centro_custo.cod_centro

     LEFT JOIN  orcamento.despesa
            ON  solicitacao_item_dotacao.exercicio   = despesa.exercicio
           AND  solicitacao_item_dotacao.cod_despesa = despesa.cod_despesa

     LEFT JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta

    ---- buscando o desdobramento
     LEFT JOIN  orcamento.conta_despesa as desdobramento
            ON  desdobramento.exercicio    = solicitacao_item_dotacao.exercicio
           AND  desdobramento.cod_conta    = solicitacao_item_dotacao.cod_conta

    LEFT JOIN  compras.mapa_item_dotacao
            ON  solicitacao_item_dotacao.exercicio       = mapa_item_dotacao.exercicio
           AND  solicitacao_item_dotacao.cod_entidade    = mapa_item_dotacao.cod_entidade
           AND  solicitacao_item_dotacao.cod_solicitacao = mapa_item_dotacao.cod_solicitacao
           AND  solicitacao_item_dotacao.cod_centro      = mapa_item_dotacao.cod_centro
           AND  solicitacao_item_dotacao.cod_item        = mapa_item_dotacao.cod_item
           AND  solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa
           AND  solicitacao_item_dotacao.cod_conta       = mapa_item_dotacao.cod_conta

    INNER JOIN (
                SELECT  mapa_item_dotacao.exercicio
                     ,  mapa_item_dotacao.cod_entidade
                     ,  mapa_item_dotacao.cod_solicitacao
                     ,  mapa_item_dotacao.cod_centro
                     ,  mapa_item_dotacao.cod_item
                     ,  mapa_item_dotacao.cod_despesa
                     ,  sum ( mapa_item_dotacao.quantidade ) as quantidade

                  FROM  compras.mapa_item_dotacao

              GROUP BY mapa_item_dotacao.exercicio
                     , mapa_item_dotacao.cod_entidade
                     , mapa_item_dotacao.cod_solicitacao
                     , mapa_item_dotacao.cod_centro
                     , mapa_item_dotacao.cod_item
                     , mapa_item_dotacao.cod_despesa
               ) as total_mapas
            ON  total_mapas.exercicio       = solicitacao_item_dotacao.exercicio
           AND  total_mapas.cod_entidade    = solicitacao_item_dotacao.cod_entidade
           AND  total_mapas.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
           AND  total_mapas.cod_centro      = solicitacao_item_dotacao.cod_centro
           AND  total_mapas.cod_item        = solicitacao_item_dotacao.cod_item
           AND  total_mapas.cod_despesa     = solicitacao_item_dotacao.cod_despesa

     ---- buscando as anulações
     LEFT JOIN (    SELECT  mapa_item_anulacao.exercicio
                         ,  mapa_item_anulacao.cod_entidade
                         ,  mapa_item_anulacao.cod_solicitacao
                         ,  mapa_item_anulacao.cod_mapa
                         ,  mapa_item_anulacao.cod_centro
                         ,  mapa_item_anulacao.cod_item
                         ,  mapa_item_anulacao.exercicio_solicitacao
                         ,  mapa_item_anulacao.lote
                         ,  mapa_item_anulacao.cod_despesa
                         ,  sum(vl_total) as vl_total
                         ,  sum(quantidade) as quantidade
                      FROM  compras.mapa_item_anulacao
                  GROUP BY  mapa_item_anulacao.exercicio
                         ,  mapa_item_anulacao.cod_entidade
                         ,  mapa_item_anulacao.cod_solicitacao
                         ,  mapa_item_anulacao.cod_mapa
                         ,  mapa_item_anulacao.cod_centro
                         ,  mapa_item_anulacao.cod_item
                         ,  mapa_item_anulacao.exercicio_solicitacao
                         ,  mapa_item_anulacao.lote
                         ,  mapa_item_anulacao.cod_despesa ) as anulacao_mapa
           ON ( anulacao_mapa.exercicio             = mapa_item_dotacao.exercicio
          AND   anulacao_mapa.cod_entidade          = mapa_item_dotacao.cod_entidade
          AND   anulacao_mapa.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
          AND   anulacao_mapa.cod_mapa              = mapa_item_dotacao.cod_mapa
          AND   anulacao_mapa.cod_centro            = mapa_item_dotacao.cod_centro
          AND   anulacao_mapa.cod_item              = mapa_item_dotacao.cod_item
          AND   anulacao_mapa.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
          AND   anulacao_mapa.lote                  = mapa_item_dotacao.lote
          AND   anulacao_mapa.cod_despesa           = mapa_item_dotacao.cod_despesa)

    ---- Anulações de solitações
    LEFT JOIN (  select exercicio
                    , cod_entidade
                    , cod_solicitacao
                    , cod_item
                    , cod_centro
                    , cod_despesa
                    , sum ( quantidade ) as quantidade
                    , sum ( vl_anulacao ) as vl_anulacao
              from compras.solicitacao_item_dotacao_anulacao
              group by exercicio
                      , cod_entidade
                      , cod_solicitacao
                      , cod_item
                      , cod_centro
                      , cod_despesa
              ) as anulacao_solicitacao
           ON  anulacao_solicitacao.exercicio       = solicitacao_item_dotacao.exercicio
          AND  anulacao_solicitacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
          AND  anulacao_solicitacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
          AND  anulacao_solicitacao.cod_centro      = solicitacao_item_dotacao.cod_centro
          AND  anulacao_solicitacao.cod_item        = solicitacao_item_dotacao.cod_item
          AND  anulacao_solicitacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa

    LEFT JOIN (  select solicitacao_homologada_reserva.exercicio
                  , solicitacao_homologada_reserva.cod_entidade
                  , solicitacao_homologada_reserva.cod_solicitacao
                  , solicitacao_homologada_reserva.cod_centro
                  , solicitacao_homologada_reserva.cod_item
                  , reserva_saldos.cod_reserva
                  , reserva_saldos.cod_despesa
                  , reserva_saldos.vl_reserva
              FROM compras.solicitacao_homologada_reserva
              JOIN orcamento.reserva_saldos
                ON ( reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva
               AND   reserva_saldos.exercicio   = solicitacao_homologada_reserva.exercicio
               AND   reserva_saldos.cod_despesa = solicitacao_homologada_reserva.cod_despesa )
             WHERE NOT EXISTS ( select 1
                                  from orcamento.reserva_saldos_anulada
                                 where reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva
                                   and reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio )
             ) as reserva_solicitacao
           ON  reserva_solicitacao.exercicio       = solicitacao_item_dotacao.exercicio
          AND  reserva_solicitacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
          AND  reserva_solicitacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
          AND  reserva_solicitacao.cod_centro      = solicitacao_item_dotacao.cod_centro
          AND  reserva_solicitacao.cod_item        = solicitacao_item_dotacao.cod_item
          AND  reserva_solicitacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa

    --- buscando a reserva de saldos
    LEFT JOIN compras.mapa_item_reserva
           ON ( mapa_item_dotacao.exercicio               = mapa_item_reserva.exercicio_mapa
          AND   mapa_item_dotacao.cod_mapa                = mapa_item_reserva.cod_mapa
          AND   mapa_item_dotacao.exercicio_solicitacao   = mapa_item_reserva.exercicio_solicitacao
          AND   mapa_item_dotacao.cod_entidade            = mapa_item_reserva.cod_entidade
          AND   mapa_item_dotacao.cod_solicitacao         = mapa_item_reserva.cod_solicitacao
          AND   mapa_item_dotacao.cod_centro              = mapa_item_reserva.cod_centro
          AND   mapa_item_dotacao.cod_item                = mapa_item_reserva.cod_item
          AND   mapa_item_dotacao.lote                    = mapa_item_reserva.lote
          AND   mapa_item_dotacao.cod_despesa             = mapa_item_reserva.cod_despesa
          AND   mapa_item_dotacao.cod_conta               = mapa_item_reserva.cod_conta)

    LEFT JOIN  orcamento.reserva_saldos
           ON  mapa_item_reserva.cod_reserva       = reserva_saldos.cod_reserva
          AND  mapa_item_reserva.exercicio_reserva = reserva_saldos.exercicio ";

    if ((Sessao::read('stAcaoTela') != 'anularProcessoLicitatorio')) {
        $stSql.= " where ( mapa_item_dotacao.quantidade  - coalesce( anulacao_mapa.quantidade  , 0 ) ) > 0 ";
    }

        return $stSql;
}

function recuperaQuantidadeMapas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaQuantidadeMapas( ). $stFiltro . $stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaQuantidadeMapas()
{
    $stSql = " select
                       ---- quantidade incluida em mapas
                     ( ( coalesce (
                                   (select  (coalesce(  sum ( mapa_item_dotacao.quantidade ), 0.0) ) as quantidade
                                      from compras.mapa_item_dotacao
                                     where mapa_item_dotacao.exercicio       = solicitacao_item_dotacao.exercicio
                                       and mapa_item_dotacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       and mapa_item_dotacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       and mapa_item_dotacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       and mapa_item_dotacao.cod_item        = solicitacao_item_dotacao.cod_item
                                       and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       and mapa_item_dotacao.cod_conta       = solicitacao_item_dotacao.cod_conta )
                                  , 0.0 ) ) -
                       ---- quantidade anulada do mapa
                       ( coalesce (
                                   (select (coalesce(  sum ( mapa_item_anulacao.quantidade ), 0.0 )) as quantidade
                                      from compras.mapa_item_dotacao
                                   left join compras.mapa_item_anulacao
                                          on ( mapa_item_dotacao.exercicio             = mapa_item_anulacao.exercicio
                                         and   mapa_item_dotacao.cod_mapa              = mapa_item_anulacao.cod_mapa
                                         and   mapa_item_dotacao.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
                                         and   mapa_item_dotacao.cod_entidade          = mapa_item_anulacao.cod_entidade
                                         and   mapa_item_dotacao.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
                                         and   mapa_item_dotacao.cod_centro            = mapa_item_anulacao.cod_centro
                                         and   mapa_item_dotacao.cod_item              = mapa_item_anulacao.cod_item
                                         and   mapa_item_dotacao.lote                  = mapa_item_anulacao.lote
                                         and   mapa_item_dotacao.cod_despesa           = mapa_item_anulacao.cod_despesa
                                         and   mapa_item_dotacao.cod_conta             = mapa_item_anulacao.cod_conta)
                                     where mapa_item_dotacao.exercicio       = solicitacao_item_dotacao.exercicio
                                       and mapa_item_dotacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                       and mapa_item_dotacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                       and mapa_item_dotacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                       and mapa_item_dotacao.cod_item        = solicitacao_item_dotacao.cod_item
                                       and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                       and mapa_item_dotacao.cod_conta       = solicitacao_item_dotacao.cod_conta )
                                  , 0.0 ))) as quantidade_em_mapas

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

                  left join compras.solicitacao_item_dotacao
                         on ( solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                          and solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                          and solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                          and solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                          and solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item )

                  where solicitacao_item.cod_solicitacao is not null   ";

        return $stSql;
    }

    public function recuperaConsultaItemMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaConsultaItemMapa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaConsultaItemMapa()
    {
        $stSql = "
            SELECT  solicitacao_item.exercicio as exercicio_solicitacao
                 ,  solicitacao_item.cod_entidade
                 ,  solicitacao_item.cod_solicitacao
                 ,  solicitacao_item.complemento
                 ,  solicitacao_item_dotacao.cod_item
                 ,  solicitacao_item_dotacao.cod_despesa as dotacao
                 ,  solicitacao_item_dotacao.cod_conta as conta_despesa
                 ,  solicitacao_item_dotacao.cod_centro
                 ,  catalogo_item.descricao as item
                 ,  unidade_medida.nom_unidade
                 ,  unidade_medida.cod_unidade
                 ,  centro_custo.descricao as centro_custo
                 ,  conta_despesa.descricao as dotacao_nom_conta
                 ,  desdobramento.descricao  as nom_conta
                 ,  desdobramento.cod_estrutural
                 -- QUANTIDADE ANULADA
                 , anulacao_mapa.quantidade as quantidade_anulada

                 -- quantidades/valores
                 ,  solicitacao_item_dotacao.quantidade - coalesce(  anulacao_solicitacao.quantidade  , 0.0000 ) as quantidade_solicitada

                 -- quantidade do mapa - anulação
                 ,  mapa_item_dotacao.quantidade  - coalesce( anulacao_mapa.quantidade  , 0 ) as quantidade_mapa
                 ,  mapa_item_dotacao.exercicio
                 ,  mapa_item_dotacao.lote

                 --- quantidade atendida para o item neste e em outros mapas
                 ,  ( solicitacao_item_dotacao.quantidade -  total_mapas.quantidade + ( mapa_item_dotacao.quantidade - coalesce( anulacao_mapa.quantidade, 0 ) ) ) as quantidade_maxima
                 ,  mapa_item_dotacao.vl_dotacao - coalesce ( anulacao_mapa.vl_total, 0 )   as valor_total_mapa

                 --- reserva de saldos
                 ,  reserva_saldos.cod_reserva
                 ,  reserva_saldos.exercicio as exercicio_reserva
                 ,  coalesce(reserva_saldos.vl_reserva, 0.00) as vl_reserva
                 ,  coalesce((  SELECT sum(lancamento_material.quantidade) as quantidade
                                  FROM almoxarifado.estoque_material
                                  JOIN almoxarifado.lancamento_material
                                    ON ( lancamento_material.cod_item         = estoque_material.cod_item
                                   AND   lancamento_material.cod_marca        = estoque_material.cod_marca
                                   AND   lancamento_material.cod_almoxarifado = estoque_material.cod_almoxarifado
                                   AND   lancamento_material.cod_centro       = estoque_material.cod_centro )
                                 WHERE solicitacao_item.cod_item    = estoque_material.cod_item
                                   AND solicitacao_item.cod_centro  = estoque_material.cod_centro
                                   AND solicitacao.cod_almoxarifado = estoque_material.cod_almoxarifado )
                               , 0.0 ) as quantidade_estoque
                 ,  reserva_solicitacao.cod_reserva as cod_reserva_solicitacao
                 ,  reserva_solicitacao.exercicio   as exercicio_reserva_solicitacao
                 ,  reserva_solicitacao.vl_reserva  as vl_reserva_solicitacao

              FROM  compras.solicitacao_item

        INNER JOIN  compras.solicitacao
                ON  solicitacao.exercicio       = solicitacao_item.exercicio
               AND  solicitacao.cod_entidade    = solicitacao_item.cod_entidade
               AND  solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao

        INNER JOIN  compras.solicitacao_item_dotacao
                ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
               AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
               AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
               AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
               AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item

        INNER JOIN  almoxarifado.catalogo_item
                ON  solicitacao_item.cod_item = catalogo_item.cod_item

        INNER JOIN  administracao.unidade_medida
                ON  catalogo_item.cod_unidade  = unidade_medida.cod_unidade
               AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza

        INNER JOIN  almoxarifado.centro_custo
                ON  solicitacao_item.cod_centro = centro_custo.cod_centro

         LEFT JOIN  orcamento.despesa
                ON  solicitacao_item_dotacao.exercicio   = despesa.exercicio
               AND  solicitacao_item_dotacao.cod_despesa = despesa.cod_despesa

         LEFT JOIN  orcamento.conta_despesa
                ON  conta_despesa.exercicio = despesa.exercicio
               AND  conta_despesa.cod_conta = despesa.cod_conta

        ---- buscando o desdobramento
         LEFT JOIN  orcamento.conta_despesa as desdobramento
                ON  desdobramento.exercicio    = solicitacao_item_dotacao.exercicio
               AND  desdobramento.cod_conta    = solicitacao_item_dotacao.cod_conta

        INNER JOIN  compras.mapa_item_dotacao
                ON  solicitacao_item_dotacao.exercicio       = mapa_item_dotacao.exercicio
               AND  solicitacao_item_dotacao.cod_entidade    = mapa_item_dotacao.cod_entidade
               AND  solicitacao_item_dotacao.cod_solicitacao = mapa_item_dotacao.cod_solicitacao
               AND  solicitacao_item_dotacao.cod_centro      = mapa_item_dotacao.cod_centro
               AND  solicitacao_item_dotacao.cod_item        = mapa_item_dotacao.cod_item
               AND  solicitacao_item_dotacao.cod_despesa     = mapa_item_dotacao.cod_despesa
               AND  solicitacao_item_dotacao.cod_conta       = mapa_item_dotacao.cod_conta

        INNER JOIN (
                    SELECT  mapa_item_dotacao.exercicio
                         ,  mapa_item_dotacao.cod_entidade
                         ,  mapa_item_dotacao.cod_solicitacao
                         ,  mapa_item_dotacao.cod_centro
                         ,  mapa_item_dotacao.cod_item
                         ,  mapa_item_dotacao.cod_despesa
                         ,  sum ( mapa_item_dotacao.quantidade ) as quantidade

                      FROM  compras.mapa_item_dotacao

                  GROUP BY mapa_item_dotacao.exercicio
                         , mapa_item_dotacao.cod_entidade
                         , mapa_item_dotacao.cod_solicitacao
                         , mapa_item_dotacao.cod_centro
                         , mapa_item_dotacao.cod_item
                         , mapa_item_dotacao.cod_despesa
                   ) as total_mapas
                ON  total_mapas.exercicio       = solicitacao_item_dotacao.exercicio
               AND  total_mapas.cod_entidade    = solicitacao_item_dotacao.cod_entidade
               AND  total_mapas.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
               AND  total_mapas.cod_centro      = solicitacao_item_dotacao.cod_centro
               AND  total_mapas.cod_item        = solicitacao_item_dotacao.cod_item
               AND  total_mapas.cod_despesa     = solicitacao_item_dotacao.cod_despesa

         ---- buscando as anulações
         LEFT JOIN (    SELECT  mapa_item_anulacao.exercicio
                             ,  mapa_item_anulacao.cod_entidade
                             ,  mapa_item_anulacao.cod_solicitacao
                             ,  mapa_item_anulacao.cod_mapa
                             ,  mapa_item_anulacao.cod_centro
                             ,  mapa_item_anulacao.cod_item
                             ,  mapa_item_anulacao.exercicio_solicitacao
                             ,  mapa_item_anulacao.lote
                             ,  mapa_item_anulacao.cod_despesa
                             ,  sum(vl_total) as vl_total
                             ,  sum(quantidade) as quantidade
                          FROM  compras.mapa_item_anulacao
                      GROUP BY  mapa_item_anulacao.exercicio
                             ,  mapa_item_anulacao.cod_entidade
                             ,  mapa_item_anulacao.cod_solicitacao
                             ,  mapa_item_anulacao.cod_mapa
                             ,  mapa_item_anulacao.cod_centro
                             ,  mapa_item_anulacao.cod_item
                             ,  mapa_item_anulacao.exercicio_solicitacao
                             ,  mapa_item_anulacao.lote
                             ,  mapa_item_anulacao.cod_despesa ) as anulacao_mapa
               ON ( anulacao_mapa.exercicio             = mapa_item_dotacao.exercicio
              AND   anulacao_mapa.cod_entidade          = mapa_item_dotacao.cod_entidade
              AND   anulacao_mapa.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
              AND   anulacao_mapa.cod_mapa              = mapa_item_dotacao.cod_mapa
              AND   anulacao_mapa.cod_centro            = mapa_item_dotacao.cod_centro
              AND   anulacao_mapa.cod_item              = mapa_item_dotacao.cod_item
              AND   anulacao_mapa.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
              AND   anulacao_mapa.lote                  = mapa_item_dotacao.lote
              AND   anulacao_mapa.cod_despesa           = mapa_item_dotacao.cod_despesa)

        ---- Anulações de solitações
        LEFT JOIN (  select exercicio
                        , cod_entidade
                        , cod_solicitacao
                        , cod_item
                        , cod_centro
                        , cod_despesa
                        , sum ( quantidade ) as quantidade
                        , sum ( vl_anulacao ) as vl_anulacao
                  from compras.solicitacao_item_dotacao_anulacao
                  group by exercicio
                          , cod_entidade
                          , cod_solicitacao
                          , cod_item
                          , cod_centro
                          , cod_despesa
                  ) as anulacao_solicitacao
               ON  anulacao_solicitacao.exercicio       = solicitacao_item_dotacao.exercicio
              AND  anulacao_solicitacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
              AND  anulacao_solicitacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
              AND  anulacao_solicitacao.cod_centro      = solicitacao_item_dotacao.cod_centro
              AND  anulacao_solicitacao.cod_item        = solicitacao_item_dotacao.cod_item
              AND  anulacao_solicitacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa

        LEFT JOIN (  select solicitacao_homologada_reserva.exercicio
                      , solicitacao_homologada_reserva.cod_entidade
                      , solicitacao_homologada_reserva.cod_solicitacao
                      , solicitacao_homologada_reserva.cod_centro
                      , solicitacao_homologada_reserva.cod_item
                      , reserva_saldos.cod_reserva
                      , reserva_saldos.cod_despesa
                      , reserva_saldos.vl_reserva
                  FROM compras.solicitacao_homologada_reserva
                  JOIN orcamento.reserva_saldos
                    ON ( reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva
                   AND   reserva_saldos.exercicio   = solicitacao_homologada_reserva.exercicio
                   AND   reserva_saldos.cod_despesa = solicitacao_homologada_reserva.cod_despesa )
                 WHERE NOT EXISTS ( select 1
                                      from orcamento.reserva_saldos_anulada
                                     where reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva
                                       and reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio )
                 ) as reserva_solicitacao
               ON  reserva_solicitacao.exercicio       = solicitacao_item_dotacao.exercicio
              AND  reserva_solicitacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
              AND  reserva_solicitacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
              AND  reserva_solicitacao.cod_centro      = solicitacao_item_dotacao.cod_centro
              AND  reserva_solicitacao.cod_item        = solicitacao_item_dotacao.cod_item
              AND  reserva_solicitacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa

        --- buscando a reserva de saldos
        LEFT JOIN compras.mapa_item_reserva
               ON ( mapa_item_dotacao.exercicio               = mapa_item_reserva.exercicio_mapa
              AND   mapa_item_dotacao.cod_mapa                = mapa_item_reserva.cod_mapa
              AND   mapa_item_dotacao.exercicio_solicitacao   = mapa_item_reserva.exercicio_solicitacao
              AND   mapa_item_dotacao.cod_entidade            = mapa_item_reserva.cod_entidade
              AND   mapa_item_dotacao.cod_solicitacao         = mapa_item_reserva.cod_solicitacao
              AND   mapa_item_dotacao.cod_centro              = mapa_item_reserva.cod_centro
              AND   mapa_item_dotacao.cod_item                = mapa_item_reserva.cod_item
              AND   mapa_item_dotacao.lote                    = mapa_item_reserva.lote
              AND   mapa_item_dotacao.cod_despesa             = mapa_item_reserva.cod_despesa
              AND   mapa_item_dotacao.cod_conta               = mapa_item_reserva.cod_conta)

        LEFT JOIN  orcamento.reserva_saldos
               ON  mapa_item_reserva.cod_reserva       = reserva_saldos.cod_reserva
              AND  mapa_item_reserva.exercicio_reserva = reserva_saldos.exercicio

            WHERE  1=1 ";

        return $stSql;
    }

}
