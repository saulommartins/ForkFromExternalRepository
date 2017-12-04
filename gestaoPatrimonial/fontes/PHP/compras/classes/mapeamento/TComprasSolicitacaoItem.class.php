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
 * Classe de mapeamento da tabela compras.solicitacao_item
 * Data de Criação: 30/06/2006

 * @author Analista: Diego Victoria
 * @author Desenvolvedor: Leandro André Zis

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-03.04.01

 $Id: TComprasSolicitacaoItem.class.php 63962 2015-11-11 18:32:34Z franver $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  compras.solicitacao_item
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
  */
class TComprasSolicitacaoItem extends Persistente
{
/**
 * Método Construtor
 * @access Private
 */
function TComprasSolicitacaoItem()
{
    parent::Persistente();
    $this->setTabela("compras.solicitacao_item");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao,cod_centro,cod_item');

    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_entidade','integer',true,'',true,true);
    $this->AddCampo('cod_solicitacao','integer',true,'',true,true);
    $this->AddCampo('cod_centro','integer',true,'',true,true);
    $this->AddCampo('cod_item','integer',true,'',true,true);
    $this->AddCampo('complemento','varchar',true,'200',false,false);
    $this->AddCampo('quantidade','numeric',true,'14,4',false,false);
    $this->AddCampo('vl_total','numeric',true,'14,2',false,false);

}

function montaRecuperaItensSolicitacaoMapaCompras()
{
    if ($this->getDado('inCodSolicitacao'))
        $stFiltro .= "         AND solicitacao_item.cod_solicitacao = ".$this->getDado('inCodSolicitacao')." \n";

    if ($this->getDado('inCodEntidade'))
        $stFiltro .= "         AND solicitacao_item.cod_entidade = ".$this->getDado('inCodEntidade')." \n";

    if ($this->getDado('stExercicio'))
        $stFiltro .= "         AND solicitacao_item.exercicio = '".$this->getDado('stExercicio')."' \n";

    if ($this->getDado('inCodItem'))
        $stFiltro .= "         AND solicitacao_item.cod_item = ".$this->getDado('inCodItem')." \n";

    if ($this->getDado('inCodCentro'))
        $stFiltro .= "         AND solicitaca_item.cod_centro = ".$this->getDado('inCodCentro')." \n";

    $stSql = "select exercicio_solicitacao
                   , cod_entidade
                   , cod_solicitacao
                   , cod_item
                   , item
                   , nom_unidade
                   , quantidade
                   , vl_total
                   , complemento
                   , cod_centro
                   , centro_custo
                   , valor_unitario
                   , qtd_anulada
                   , valor_anulado
                   , quantidade_atendida
                   , vl_total_mapa_item
                   , quantidade_estoque
                   , dotacao
                   , dotacao_nom_conta
                   , conta_despesa
                   , nom_conta
                   , cod_estrutural
                   , vl_reserva
                   , ( quantidade - qtd_anulada - quantidade_atendida ) as quantidade_mapa
                   , ( quantidade - qtd_anulada ) as quantidade_solicitada
                   , cod_reserva
                   , exercicio_reserva
                from (


                  select solicitacao_item.exercicio as exercicio_solicitacao
                       , solicitacao_item.cod_entidade
                       , solicitacao_item.cod_solicitacao
                       , solicitacao_item.cod_item
                       , catalogo_item.descricao as item
                       , unidade_medida.nom_unidade
                       , solicitacao_item.quantidade
                       , solicitacao_item.vl_total
                       , solicitacao_item.complemento
                       , solicitacao_item.cod_centro
                       , centro_custo.descricao as centro_custo
                       , ( solicitacao_item.vl_total / solicitacao_item.quantidade ) as valor_unitario
                       ---- Quantidade Anulada do item
                       ,coalesce(
                                  (select sum ( solicitacao_item_anulacao.quantidade ) as quantidade
                                    from compras.solicitacao_item_anulacao
                                    where solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                      and solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                      and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                      and solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                      and solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                   ), 0.0 )  as qtd_anulada
                       ---- Valor anulado do item
                       , coalesce(
                                  (select sum ( solicitacao_item_anulacao.vl_total   ) as vl_total
                                    from compras.solicitacao_item_anulacao
                                    where solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                      and solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                      and solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                      and solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                      and solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                   ), 0.0 )  as valor_anulado
                       ---- quantidade mapeada subtraida da qtd anulada do mapa ( incluida em mapas )
                       , coalesce (
                                   (select  (coalesce(  sum ( mapa_item.quantidade ), 0.0) ) -
                                            (coalesce(  sum ( mapa_item_anulacao.quantidade ), 0.0 )) as quantidade
                                      from compras.mapa_item
                                   left join compras.mapa_item_anulacao
                                          on ( mapa_item.exercicio             = mapa_item_anulacao.exercicio
                                         and   mapa_item.cod_mapa              = mapa_item_anulacao.cod_mapa
                                         and   mapa_item.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
                                         and   mapa_item.cod_entidade          = mapa_item_anulacao.cod_entidade
                                         and   mapa_item.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
                                         and   mapa_item.cod_centro            = mapa_item_anulacao.cod_centro
                                         and   mapa_item.cod_item              = mapa_item_anulacao.cod_item
                                         and   mapa_item.lote                  = mapa_item_anulacao.lote )
                                     where mapa_item.exercicio       = solicitacao_item.exercicio
                                       and mapa_item.cod_entidade    = solicitacao_item.cod_entidade
                                       and mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao
                                       and mapa_item.cod_centro      = solicitacao_item.cod_centro
                                       and mapa_item.cod_item        = solicitacao_item.cod_item )
                                  , 0.0 ) as quantidade_atendida
                       ---- valor mapeado menos o valor anulado do mapa
                       , coalesce (
                                   (select  (coalesce(  sum ( mapa_item.vl_total ), 0.0) ) -
                                            (coalesce(  sum ( mapa_item_anulacao.vl_total ), 0.0 )) as valor
                                      from compras.mapa_item
                                   left join compras.mapa_item_anulacao
                                          on ( mapa_item.exercicio             = mapa_item_anulacao.exercicio
                                         and   mapa_item.cod_mapa              = mapa_item_anulacao.cod_mapa
                                         and   mapa_item.exercicio_solicitacao = mapa_item_anulacao.exercicio_solicitacao
                                         and   mapa_item.cod_entidade          = mapa_item_anulacao.cod_entidade
                                         and   mapa_item.cod_solicitacao       = mapa_item_anulacao.cod_solicitacao
                                         and   mapa_item.cod_centro            = mapa_item_anulacao.cod_centro
                                         and   mapa_item.cod_item              = mapa_item_anulacao.cod_item
                                         and   mapa_item.lote                  = mapa_item_anulacao.lote )
                                     where mapa_item.exercicio       = solicitacao_item.exercicio
                                       and mapa_item.cod_entidade    = solicitacao_item.cod_entidade
                                       and mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao
                                       and mapa_item.cod_centro      = solicitacao_item.cod_centro
                                       and mapa_item.cod_item        = solicitacao_item.cod_item )
                               , 0.0 ) as vl_total_mapa_item
                       ----- quantidade em estoque
                       ,coalesce( ( SELECT sum(lancamento_material.quantidade) as quantidade
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
                       ---- Reserva de saldo pro item



                       , despesa.cod_despesa     as dotacao
                       , conta_despesa.descricao as dotacao_nom_conta
                       , desdobramento.cod_conta  as conta_despesa
                       , desdobramento.descricao  as nom_conta
                       , desdobramento.cod_estrutural
                       , coalesce(reserva_saldos.vl_reserva, 0.00) as vl_reserva
                       , reserva_saldos.cod_reserva
                       , reserva_saldos.exercicio as exercicio_reserva

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
                  left join compras.solicitacao_homologada_reserva
                         on ( solicitacao_item_dotacao.exercicio       = solicitacao_homologada_reserva.exercicio
                        and   solicitacao_item_dotacao.cod_entidade    = solicitacao_homologada_reserva.cod_entidade
                        and   solicitacao_item_dotacao.cod_solicitacao = solicitacao_homologada_reserva.cod_solicitacao
                        and   solicitacao_item_dotacao.cod_centro      = solicitacao_homologada_reserva.cod_centro
                        and   solicitacao_item_dotacao.cod_item        = solicitacao_homologada_reserva.cod_item )
                  left join orcamento.reserva_saldos
                         on ( solicitacao_homologada_reserva.cod_reserva = reserva_saldos.cod_reserva
                        and   solicitacao_homologada_reserva.exercicio   = reserva_saldos.exercicio )

                  where solicitacao_item.cod_solicitacao is not null
                        $stFiltro
              ) as itens
           --   where ( quantidade - qtd_anulada )>0
           --     and ( quantidade - qtd_anulada - quantidade_atendida)>0";

    return $stSql;
}

/*
    Recupera os Itens que pertencem às solicitações inclusas num mapa de compras
*/
function recuperaItensSolicitacaoMapaCompras(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaItensSolicitacaoMapaCompras();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function recuperaItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaItem().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaItem()
{
    $stSql = " SELECT solicitacao_item.exercicio                   \n";
    $stSql.= "       ,solicitacao_item.cod_entidade                \n";
    $stSql.= "       ,solicitacao_item.cod_solicitacao             \n";
    $stSql.= "       ,solicitacao_item.cod_centro                  \n";
    $stSql.= "       ,solicitacao_item.cod_item                    \n";
    $stSql.= "       ,solicitacao_item.complemento                 \n";
    $stSql.= "       ,solicitacao_item.quantidade                  \n";
    $stSql.= "       ,solicitacao_item.vl_total                    \n";
    $stSql.= "   FROM compras.solicitacao_item                     \n";

    return $stSql;
}

function recuperaSolicitacaoItemMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaSolicitacaoItemMapa().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaSolicitacaoItemMapa()
{
    $stSql  = "     SELECT  mapa_item.exercicio                                                        \n";
    $stSql .= "          ,  mapa_item.exercicio_solicitacao                                            \n";
    $stSql .= "          ,  mapa_item.cod_entidade                                                     \n";
    $stSql .= "          ,  mapa_item.cod_solicitacao                                                  \n";
    $stSql .= "          ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN                       \n";
    $stSql .= "                 COALESCE(SUM(mapa_item.quantidade), 0.00)                              \n";
    $stSql .= "             ELSE                                                                       \n";
    $stSql .= "                 COALESCE(SUM(mapa_item_dotacao.quantidade), 0.0000)                    \n";
    $stSql .= "             END AS quantidade                                                          \n";
    $stSql .= "          ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN                       \n";
    $stSql .= "                 COALESCE(SUM(mapa_item.vl_total),0.00)                                 \n";
    $stSql .= "             ELSE                                                                       \n";
    $stSql .= "                 COALESCE(SUM(mapa_item_dotacao.vl_dotacao),0.00)                       \n";
    $stSql .= "             END AS vl_total                                                            \n";
    $stSql .= "                                        , mapa_item_dotacao.cod_despesa                 \n";
    $stSql .= "       FROM  compras.mapa_item                                                          \n";
    $stSql .= "                                                                                        \n";
    $stSql .= "  LEFT JOIN  compras.mapa_item_dotacao                                                  \n";
    $stSql .= "         ON  mapa_item_dotacao.exercicio             = mapa_item.exercicio              \n";
    $stSql .= "        AND  mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa               \n";
    $stSql .= "        AND  mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao  \n";
    $stSql .= "        AND  mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade           \n";
    $stSql .= "        AND  mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao        \n";
    $stSql .= "        AND  mapa_item_dotacao.cod_centro            = mapa_item.cod_centro             \n";
    $stSql .= "        AND  mapa_item_dotacao.cod_item              = mapa_item.cod_item               \n";
    $stSql .= "        AND  mapa_item_dotacao.lote                  = mapa_item.lote                   \n";

    return $stSql;
}

function recuperaRelacionamentoItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoItem().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRelacionamentoItem()
{
    $stSql = " SELECT solicitacao_item.exercicio                                                                       \n";
    $stSql.= "       ,solicitacao_item.cod_entidade                                                                    \n";
    $stSql.= "       ,solicitacao_item.cod_solicitacao                                                                 \n";
    $stSql.= "       ,solicitacao_item.cod_centro                                                                      \n";
    $stSql.= "       ,solicitacao_item.cod_item                                                                        \n";
    $stSql.= "       ,solicitacao_item.complemento                                                                     \n";
    $stSql.= "       ,solicitacao_item.quantidade                                                                      \n";
    $stSql.= "       ,solicitacao_item_dotacao.quantidade as quantidade_dotacao                                        \n";
    $stSql.= "       ,solicitacao_item.vl_total                                                                        \n";
    $stSql.= "       ,(solicitacao_item.vl_total/solicitacao_item.quantidade) as vl_unitario                           \n";
    $stSql.= "       ,((solicitacao_item.vl_total/solicitacao_item.quantidade)*solicitacao_item_dotacao.quantidade) as vl_total_dotacao                            \n";
    $stSql.= "       ,catalogo_item.descricao as nomitem                                                               \n";
    $stSql.= "       ,centro_custo.descricao  as nomcentroCusto                                                        \n";
    $stSql.= "       ,unidade_medida.nom_unidade                                                                       \n";
    $stSql.= "		 ,conta_despesa.descricao AS nomdespesa															   \n";
    $stSql.= "		 ,conta_despesa.cod_estrutural																	   \n";
    $stSql.= "		 ,conta_despesa.cod_conta 																		   \n";
    $stSql.= "		 ,solicitacao_item_dotacao.cod_despesa															   \n";
    $stSql.= "		 ,solicitacao_item_dotacao.vl_reserva															   \n";
    //$stSql.= "       ,(SELECT solicitacao_item.quantidade - COALESCE(SUM(solicitacao_item_anulacao.quantidade),0.0000) \n";
    $stSql.= "       ,(SELECT COALESCE(SUM(solicitacao_item_anulacao.quantidade),0.0000) \n";
    $stSql.= "           FROM compras.solicitacao_item_anulacao                                                        \n";
    $stSql.= "          WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio                   \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade                \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao             \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro                  \n";
    $stSql.= "        ) AS quantidade_anulada                                                                          \n";
    //$stSql.= "       ,(SELECT solicitacao_item.vl_total - COALESCE(SUM(solicitacao_item_anulacao.vl_total),0.0000)     \n";
    $stSql.= "       ,(SELECT COALESCE(SUM(solicitacao_item_anulacao.vl_total),0.0000)     \n";
    $stSql.= "           FROM compras.solicitacao_item_anulacao                                                        \n";
    $stSql.= "          WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio                   \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade                \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao             \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro                  \n";
    $stSql.= "        ) AS valor_anulada                                                                               \n";
    $stSql.= "   FROM compras.solicitacao_item                                                                         \n";
    $stSql.= "  LEFT JOIN   compras.solicitacao_item_dotacao                                                           \n";
    $stSql.= "         ON   ( solicitacao_item.exercicio       = solicitacao_item_dotacao.exercicio                    \n";
    $stSql.= "        AND     solicitacao_item.cod_entidade    = solicitacao_item_dotacao.cod_entidade                 \n";
    $stSql.= "        AND     solicitacao_item.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao              \n";
    $stSql.= "        AND     solicitacao_item.cod_centro      = solicitacao_item_dotacao.cod_centro                   \n";
    $stSql.= "        AND     solicitacao_item.cod_item        = solicitacao_item_dotacao.cod_item)                    \n";
    $stSql.= " LEFT JOIN                                                                                              \n";
    $stSql.= "              orcamento.conta_despesa                                                                    \n";
    $stSql.= "         ON                                                                                              \n";
    $stSql.= "              ( solicitacao_item_dotacao.exercicio = conta_despesa.exercicio                             \n";
    $stSql.= "        AND     solicitacao_item_dotacao.cod_conta = conta_despesa.cod_conta)                            \n";
    $stSql.= "       ,almoxarifado.catalogo_item                                                                       \n";
    $stSql.= "       ,almoxarifado.centro_custo                                                                        \n";
    $stSql.= "       ,administracao.unidade_medida                                                                     \n";

    return $stSql;

}

function recuperaRelacionamentoItemCentroCusto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoItemCentroCusto().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRelacionamentoItemCentroCusto()
{
    $stSql = " SELECT solicitacao_item.exercicio                                                                       \n";
    $stSql.= "       ,solicitacao_item.cod_entidade                                                                    \n";
    $stSql.= "       ,solicitacao_item.cod_solicitacao                                                                 \n";
    $stSql.= "       ,solicitacao_item.cod_centro                                                                      \n";
    $stSql.= "       ,solicitacao_item.cod_item                                                                        \n";
    $stSql.= "       ,solicitacao_item.complemento                                                                     \n";
    $stSql.= "       ,solicitacao_item.quantidade                                                                      \n";
    //$stSql.= "       ,solicitacao_item_dotacao.quantidade as quantidade_dotacao                                        \n";
    $stSql.= "       ,solicitacao_item.vl_total                                                                        \n";
    $stSql.= "       ,(solicitacao_item.vl_total/solicitacao_item.quantidade) as vl_unitario                           \n";
    //$stSql.= "       ,((solicitacao_item.vl_total/solicitacao_item.quantidade)*solicitacao_item_dotacao.quantidade) as vl_total_dotacao                            \n";
    $stSql.= "       ,catalogo_item.descricao as nomitem                                                               \n";
    $stSql.= "       ,centro_custo.descricao  as nomcentroCusto                                                        \n";
    $stSql.= "       ,unidade_medida.nom_unidade                                                                       \n";
    //$stSql.= "		 ,conta_despesa.descricao AS nomdespesa															   \n";
    //$stSql.= "		 ,conta_despesa.cod_estrutural																	   \n";
    //$stSql.= "		 ,conta_despesa.cod_conta 																		   \n";
    //$stSql.= "		 ,solicitacao_item_dotacao.cod_despesa															   \n";
    //$stSql.= "		 ,solicitacao_item_dotacao.vl_reserva															   \n";
    //$stSql.= "       ,(SELECT solicitacao_item.quantidade - COALESCE(SUM(solicitacao_item_anulacao.quantidade),0.0000) \n";
    $stSql.= "       ,(SELECT COALESCE(SUM(solicitacao_item_anulacao.quantidade),0.0000) \n";
    $stSql.= "           FROM compras.solicitacao_item_anulacao                                                        \n";
    $stSql.= "          WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio                   \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade                \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao             \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro                  \n";
    $stSql.= "        ) AS quantidade_anulada                                                                          \n";
    //$stSql.= "       ,(SELECT solicitacao_item.vl_total - COALESCE(SUM(solicitacao_item_anulacao.vl_total),0.0000)     \n";
    $stSql.= "       ,(SELECT COALESCE(SUM(solicitacao_item_anulacao.vl_total),0.0000)     \n";
    $stSql.= "           FROM compras.solicitacao_item_anulacao                                                        \n";
    $stSql.= "          WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio                   \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade                \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao             \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item                    \n";
    $stSql.= "            AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro                  \n";
    $stSql.= "        ) AS valor_anulada                                                                               \n";
    $stSql.= "   FROM compras.solicitacao_item                                                                         \n";
   /* $stSql.= "  LEFT JOIN   compras.solicitacao_item_dotacao                                                           \n";
    $stSql.= "         ON   ( solicitacao_item.exercicio       = solicitacao_item_dotacao.exercicio                    \n";
    $stSql.= "        AND     solicitacao_item.cod_entidade    = solicitacao_item_dotacao.cod_entidade                 \n";
    $stSql.= "        AND     solicitacao_item.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao              \n";
    $stSql.= "        AND     solicitacao_item.cod_centro      = solicitacao_item_dotacao.cod_centro                   \n";
    $stSql.= "        AND     solicitacao_item.cod_item        = solicitacao_item_dotacao.cod_item)                    \n";
    $stSql.= " LEFT JOIN                                                                                              \n";
    $stSql.= "              orcamento.conta_despesa                                                                    \n";
    $stSql.= "         ON                                                                                              \n";
    $stSql.= "              ( solicitacao_item_dotacao.exercicio = conta_despesa.exercicio                             \n";
    $stSql.= "        AND     solicitacao_item_dotacao.cod_conta = conta_despesa.cod_conta)                            \n";*/
    $stSql.= "       ,almoxarifado.catalogo_item                                                                       \n";
    $stSql.= "       ,almoxarifado.centro_custo                                                                        \n";
    $stSql.= "       ,administracao.unidade_medida                                                                     \n";

    return $stSql;

}

function recuperaRelacionamentoItemDotacao(&$rsRecordSet,   $exercicio, $cod_entidade, $cod_solicitacao)
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoItemDotacao( $exercicio, $cod_entidade, $cod_solicitacao );
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRelacionamentoItemDotacao($exercicio, $cod_entidade, $cod_solicitacao)
{
   $stSql.= "select * from (
            select catalogo_item.descricao_resumida
               ,unidade_medida.nom_unidade
               ,centro_custo.cod_centro
               ,centro_custo.descricao
               --- quantidade do item menos a quantidade anulada
               ,solicitacao_item.quantidade -
                COALESCE((SELECT SUM(solicitacao_item_anulacao.quantidade )
                            FROM compras.solicitacao_item_anulacao
                           WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                             AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                             AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                             AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                             AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                          ),0.00) as quantidade

               --- valor do item menos o valor anulado
               , cast( solicitacao_item.vl_total -
                       COALESCE((SELECT SUM(solicitacao_item_anulacao.vl_total )
                                   FROM compras.solicitacao_item_anulacao
                                  WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                    AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                    AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                    AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                    AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                 ),0.00) as numeric( 14,2 ) )  as vl_total
                --- quantidade do item por centro de custo e dotação menos a quantidade anulada
               ,solicitacao_item_dotacao.quantidade -
                COALESCE((SELECT SUM(solicitacao_item_anulacao.quantidade )
                            FROM compras.solicitacao_item_anulacao
                           WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                             AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                             AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                             AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                             AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                          ),0.00) as quantidade_dotacao

               --- valor do item por centro de custo e dotação menos o valor anulado
               , cast( ((solicitacao_item.vl_total/solicitacao_item.quantidade)*solicitacao_item_dotacao.quantidade) -
                       COALESCE((SELECT SUM(solicitacao_item_anulacao.vl_total )
                                   FROM compras.solicitacao_item_anulacao
                                  WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                    AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                    AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                    AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                    AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                 ),0.00) as numeric( 14,2 ) )  as vl_total_dotacao

               ,solicitacao_item.cod_item
               ,solicitacao_item.complemento
               ,(solicitacao_item.vl_total / solicitacao_item.quantidade)::numeric(14,2) as vl_unitario
               ,despesa.cod_despesa
               ,conta_despesa.descricao AS nomdespesa
               ,conta_despesa.cod_conta
               ,conta_despesa.cod_estrutural as desdobramento
               ,empenho.fn_saldo_dotacao(solicitacao_item_dotacao.exercicio,
                                        solicitacao_item_dotacao.cod_despesa) as saldo
               ,solicitacao_item_dotacao.vl_reserva
               ,solicitacao_item_dotacao.exercicio
               ,orcamento_reserva.vl_reserva_orcamento
          from compras.solicitacao_item
         join almoxarifado.catalogo_item
             ON (catalogo_item.cod_item = solicitacao_item.cod_item)
         join administracao.unidade_medida
            on ( catalogo_item.cod_unidade  = unidade_medida.cod_unidade
            AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza )
         join almoxarifado.centro_custo
            on (  solicitacao_item.cod_centro   = centro_custo.cod_centro )
         left join compras.solicitacao_item_dotacao
            on ( solicitacao_item_dotacao.exercicio            = solicitacao_item.exercicio
             and solicitacao_item_dotacao.cod_entidade         = solicitacao_item.cod_entidade
             and solicitacao_item_dotacao.cod_solicitacao      = solicitacao_item.cod_solicitacao
             and solicitacao_item_dotacao.cod_centro           = solicitacao_item.cod_centro
             and solicitacao_item_dotacao.cod_item             = solicitacao_item.cod_item )
         --- pegando a dotação
         left join orcamento.despesa
            on ( despesa.exercicio   = solicitacao_item_dotacao.exercicio
             and despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa )
         left join ( SELECT
                            sum(coalesce(reserva_saldos.vl_reserva, 0.00)) as vl_reserva_orcamento
                          , cod_despesa
                          , exercicio
                       FROM orcamento.reserva_saldos
                   GROUP BY reserva_saldos.cod_despesa
                          , reserva_saldos.exercicio                                       ) as orcamento_reserva
                ON (     orcamento_reserva.cod_despesa  = despesa.cod_despesa
                     AND orcamento_reserva.exercicio    = despesa.exercicio   )
         --- pegando o desdobramento
         left join orcamento.conta_despesa
                      on (conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
                      and conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta)
   where solicitacao_item.exercicio       = '".$exercicio."'
     and solicitacao_item.cod_entidade    = $cod_entidade
     and solicitacao_item.cod_solicitacao = $cod_solicitacao
    ) as consulta where quantidade > 0 --and vl_total >0              \n";

    return $stSql;

}

function recuperaSolicitacaoItemReserva(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSolicitacaoItemReserva().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSolicitacaoItemReserva()
{
    $stSql = "

            SELECT
                    solicitacao_item.cod_solicitacao,
                    solicitacao_item.exercicio
              FROM
                    compras.solicitacao_item
             WHERE
        NOT EXISTS  (
                        SELECT
                                1
                          FROM
                                compras.solicitacao_homologada_reserva
                         WHERE
                                solicitacao_homologada_reserva.exercicio = solicitacao_item.exercicio
                                AND solicitacao_homologada_reserva.cod_entidade = solicitacao_item.cod_entidade
                                AND solicitacao_homologada_reserva.cod_solicitacao = solicitacao_item.cod_solicitacao
                                AND solicitacao_homologada_reserva.cod_centro = solicitacao_item.cod_centro
                                AND solicitacao_homologada_reserva.cod_item = solicitacao_item.cod_item
                    )
               AND	solicitacao_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')."
               AND	solicitacao_item.cod_entidade = ".$this->getDado('cod_entidade')."
               AND	solicitacao_item.exercicio = '".$this->getDado('exercicio')."'

    ";

    return $stSql;
}

    public function recuperaItemSolicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItemSolicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, "", $boTransacao);

        return $obErro;
    }

    public function montaRecuperaItemSolicitacao()
    {
        $stSql .= "
           SELECT
                   solicitacao_item.cod_item
                ,  solicitacao_item.complemento
                ,  catalogo_item.descricao_resumida
                ,  unidade_medida.nom_unidade
                ,  centro_custo.cod_centro
                ,  centro_custo.descricao
                ,  COALESCE(solicitacao_item.quantidade,0.00) - COALESCE(solicitacao_item_anulacao.quantidade,0.00) AS quantidade_item
                ,  COALESCE(solicitacao_item.vl_total,0.00) - COALESCE(solicitacao_item_anulacao.vl_total,0.00)::numeric(14,2) AS vl_total_item
                ,  CASE WHEN solicitacao_item_dotacao.quantidade IS NULL THEN
                        COALESCE(solicitacao_item.quantidade,0.00) - COALESCE(solicitacao_item_anulacao.quantidade,0.00)
                   ELSE
                        COALESCE(solicitacao_item_dotacao.quantidade,0.00) - COALESCE(solicitacao_item_dotacao_anulacao.quantidade,0.00)
                   END AS quantidade
                ,  CASE WHEN conta_despesa.cod_conta IS NOT NULL THEN
                        COALESCE(solicitacao_item_dotacao.vl_reserva,0.00) - COALESCE(solicitacao_item_dotacao_anulacao.vl_anulacao,0.00)
                    ELSE
                        COALESCE(solicitacao_item.vl_total,0.00) - COALESCE(solicitacao_item_anulacao.vl_total,0.00)
                   END::numeric(14,2) AS vl_total
                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                        COALESCE(solicitacao_item_dotacao_anulacao.quantidade,0.00)
                   ELSE
                        COALESCE(solicitacao_item_anulacao.quantidade,0.00)
                   END::numeric(14,4) AS quantidade_anulada
                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                        COALESCE(solicitacao_item_dotacao_anulacao.vl_anulacao,0.00)
                   ELSE
                        COALESCE(solicitacao_item_anulacao.vl_total,0.00)
                   END::numeric(14,2) AS vl_anulado
                ,  CASE WHEN conta_despesa.cod_conta IS NOT NULL THEN
                        COALESCE((solicitacao_item_dotacao.vl_reserva/solicitacao_item_dotacao.quantidade),0.00)
                   ELSE
                        COALESCE((solicitacao_item.vl_total/solicitacao_item.quantidade),0.00)
                   END::numeric(14,2) AS vl_unitario
                ,  despesa.cod_despesa
                ,  conta_despesa.descricao AS nomdespesa
                ,  conta_despesa.cod_conta
                ,  conta_despesa.cod_estrutural AS desdobramento
                ,  empenho.fn_saldo_dotacao(solicitacao_item_dotacao.exercicio, solicitacao_item_dotacao.cod_despesa) AS saldo
                ,  solicitacao_item_dotacao.vl_reserva
                ,  solicitacao_item_dotacao.exercicio

             FROM  compras.solicitacao_item

       INNER JOIN  almoxarifado.catalogo_item
               ON  catalogo_item.cod_item = solicitacao_item.cod_item

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
              
        LEFT JOIN  (SELECT SUM(solicitacao_item_anulacao.quantidade ) AS quantidade
                         , SUM(solicitacao_item_anulacao.vl_total ) AS vl_total
                         , solicitacao_item_anulacao.cod_item
                         , solicitacao_item_anulacao.exercicio 
                         , solicitacao_item_anulacao.cod_entidade
                         , solicitacao_item_anulacao.cod_solicitacao
                         , solicitacao_item_anulacao.cod_centro
                      FROM compras.solicitacao_item_anulacao
                  GROUP BY solicitacao_item_anulacao.cod_item
                         , solicitacao_item_anulacao.exercicio
                         , solicitacao_item_anulacao.cod_entidade
                         , solicitacao_item_anulacao.cod_solicitacao
                         , solicitacao_item_anulacao.cod_centro
                   ) AS solicitacao_item_anulacao
               ON  solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
              AND  solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
              AND  solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
              AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
              AND  solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro

        LEFT JOIN  (SELECT SUM(solicitacao_item_dotacao_anulacao.quantidade ) AS quantidade
                         , SUM(solicitacao_item_dotacao_anulacao.vl_anulacao ) AS vl_anulacao
                         , solicitacao_item_dotacao_anulacao.cod_item 
                         , solicitacao_item_dotacao_anulacao.exercicio 
                         , solicitacao_item_dotacao_anulacao.cod_entidade
                         , solicitacao_item_dotacao_anulacao.cod_solicitacao
                         , solicitacao_item_dotacao_anulacao.cod_centro 
                         , solicitacao_item_dotacao_anulacao.cod_despesa
                      FROM compras.solicitacao_item_dotacao_anulacao
                  GROUP BY solicitacao_item_dotacao_anulacao.cod_item 
                         , solicitacao_item_dotacao_anulacao.exercicio 
                         , solicitacao_item_dotacao_anulacao.cod_entidade
                         , solicitacao_item_dotacao_anulacao.cod_solicitacao
                         , solicitacao_item_dotacao_anulacao.cod_centro 
                         , solicitacao_item_dotacao_anulacao.cod_despesa
                   ) AS solicitacao_item_dotacao_anulacao      
               ON  solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
              AND  solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
              AND  solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
              AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
              AND  solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
              AND  solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa           

        -- RECUPERA A DOTAÇÃO
        LEFT JOIN  orcamento.despesa
               ON  despesa.exercicio   = solicitacao_item_dotacao.exercicio
              AND  despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

        -- RECUPERA O DESDOBRAMENTO
        LEFT JOIN  orcamento.conta_despesa
               ON  conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
              AND  conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta

            WHERE  solicitacao_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')."
              AND  solicitacao_item.cod_entidade    = ".$this->getDado('cod_entidade')."
              AND  solicitacao_item.exercicio       = '".$this->getDado('exercicio')."'";

        return $stSql;

    }

    # Recupera os dados para a consulta da solicitaçao.
    public function recuperaItensConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItensConsulta().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, "", $boTransacao);

        return $obErro;
    }

    public function montaRecuperaItensConsulta()
    {
        $stSql .= "

           SELECT

                   solicitacao_item.cod_item
                ,  catalogo_item.descricao_resumida
                ,  unidade_medida.nom_unidade
                ,  centro_custo.cod_centro
                ,  centro_custo.descricao

                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                      coalesce(solicitacao_item_dotacao.quantidade, 0.0000)
                   ELSE
                      coalesce(solicitacao_item.quantidade, 0.0000)
                   END                                                                                                     AS qnt_solicitada

                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                      coalesce(solicitacao_item_dotacao_anulacao.quantidade, 0.0000)
                   ELSE
                      coalesce(solicitacao_item_anulacao.quantidade, 0.0000)
                   END                                                                                                     AS qnt_anulada

                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                      coalesce(mapa_item_dotacao.quantidade, 0.0000) - coalesce(mapa_item_anulacao.quantidade, 0.0000)
                   ELSE
                      coalesce(mapa_item.quantidade, 0.0000) - coalesce(mapa_item_anulacao.quantidade, 0.0000)
                   END                                                                                                     AS qnt_mapa

                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                      coalesce(solicitacao_item_dotacao.vl_reserva, 0.00)
                   ELSE
                      coalesce(solicitacao_item.vl_total, 0.00)
                   END                                                                                                     AS vl_solicitado

                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                      coalesce(solicitacao_item_dotacao_anulacao.vl_anulacao, 0.00)
                   ELSE
                      coalesce(solicitacao_item_anulacao.vl_total, 0.00)
                   END                                                                                                     AS vl_anulado

                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                      coalesce(mapa_item_dotacao.vl_dotacao, 0.00) - coalesce(mapa_item_anulacao.vl_total, 0.00)
                   ELSE
                      coalesce(mapa_item.vl_total, 0.00) - coalesce(mapa_item_anulacao.vl_total, 0.00)
                   END                                                                                                     AS vl_mapa

                ,  despesa.cod_despesa           as hint_cod_despesa
                ,  conta_despesa.descricao       as hint_nom_despesa
                ,  conta_despesa.cod_estrutural  as hint_cod_estrutural
                ,  ppa.acao.num_acao             as hint_num_pao
                ,  pao.nom_pao                   as hint_nom_pao
                ,  recurso.cod_recurso           as hint_cod_recurso
                ,  recurso.nom_recurso           as hint_nom_recurso

                , CASE WHEN mapa_item_dotacao.vl_dotacao > 0.00 THEN
                      CASE WHEN (coalesce(mapa_item_dotacao.vl_dotacao, 0.00) - coalesce(mapa_item_anulacao.vl_total, 0.00)) = 0.00 THEN
                         'false'
                      ELSE
                         'true'
                      END
                  ELSE
                     'true'
                  END AS bo_totalizar

             FROM  compras.solicitacao_item
                   JOIN almoxarifado.catalogo_item
                     ON solicitacao_item.cod_item = catalogo_item.cod_item
                   JOIN almoxarifado.centro_custo
                     ON centro_custo.cod_centro = solicitacao_item.cod_centro
                   JOIN administracao.unidade_medida
                     ON catalogo_item.cod_unidade  = unidade_medida.cod_unidade
                    AND catalogo_item.cod_grandeza = unidade_medida.cod_grandeza

                   -- VALORES DOTACAO
                   LEFT JOIN( SELECT solicitacao_item_dotacao.exercicio
                                   , solicitacao_item_dotacao.cod_entidade
                                   , solicitacao_item_dotacao.cod_solicitacao
                                   , solicitacao_item_dotacao.cod_centro
                                   , solicitacao_item_dotacao.cod_item
                                   , solicitacao_item_dotacao.cod_conta
                                   , solicitacao_item_dotacao.cod_despesa
                                   , sum(solicitacao_item_dotacao.vl_reserva) as vl_reserva
                                   , sum(solicitacao_item_dotacao.quantidade) as quantidade
                                FROM compras.solicitacao_item_dotacao
                            GROUP BY solicitacao_item_dotacao.exercicio
                                   , solicitacao_item_dotacao.cod_entidade
                                   , solicitacao_item_dotacao.cod_solicitacao
                                   , solicitacao_item_dotacao.cod_centro
                                   , solicitacao_item_dotacao.cod_item
                                   , solicitacao_item_dotacao.cod_conta
                                   , solicitacao_item_dotacao.cod_despesa ) as solicitacao_item_dotacao
                          ON(     solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                              AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                              AND solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                              AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                              AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item )

                   LEFT JOIN( SELECT solicitacao_item_dotacao_anulacao.exercicio
                                   , solicitacao_item_dotacao_anulacao.cod_entidade
                                   , solicitacao_item_dotacao_anulacao.cod_solicitacao
                                   , solicitacao_item_dotacao_anulacao.cod_centro
                                   , solicitacao_item_dotacao_anulacao.cod_item
                                   , solicitacao_item_dotacao_anulacao.cod_conta
                                   , solicitacao_item_dotacao_anulacao.cod_despesa
                                   , sum(solicitacao_item_dotacao_anulacao.quantidade)    as quantidade
                                   , sum(solicitacao_item_dotacao_anulacao.vl_anulacao)   as vl_anulacao
                                FROM compras.solicitacao_item_dotacao_anulacao
                            GROUP BY solicitacao_item_dotacao_anulacao.exercicio
                                   , solicitacao_item_dotacao_anulacao.cod_entidade
                                   , solicitacao_item_dotacao_anulacao.cod_solicitacao
                                   , solicitacao_item_dotacao_anulacao.cod_centro
                                   , solicitacao_item_dotacao_anulacao.cod_item
                                   , solicitacao_item_dotacao_anulacao.cod_conta
                                   , solicitacao_item_dotacao_anulacao.cod_despesa ) as solicitacao_item_dotacao_anulacao
                          ON(     solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                              AND solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                              AND solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                              AND solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                              AND solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                              AND solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                              AND solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa )

                   LEFT JOIN( SELECT
                                     mapa_item_dotacao.exercicio_solicitacao
                                   , mapa_item_dotacao.cod_entidade
                                   , mapa_item_dotacao.cod_solicitacao
                                   , mapa_item_dotacao.cod_centro
                                   , mapa_item_dotacao.cod_item
                                   , mapa_item_dotacao.cod_conta
                                   , mapa_item_dotacao.cod_despesa
                                   , sum(mapa_item_dotacao.quantidade)    as quantidade
                                   , sum(mapa_item_dotacao.vl_dotacao)    as vl_dotacao
                                FROM compras.mapa_item_dotacao
                            GROUP BY
                                     mapa_item_dotacao.exercicio_solicitacao
                                   , mapa_item_dotacao.cod_entidade
                                   , mapa_item_dotacao.cod_solicitacao
                                   , mapa_item_dotacao.cod_centro
                                   , mapa_item_dotacao.cod_item
                                   , mapa_item_dotacao.cod_conta
                                   , mapa_item_dotacao.cod_despesa ) as mapa_item_dotacao
                          ON(     mapa_item_dotacao.exercicio_solicitacao = solicitacao_item_dotacao.exercicio
                              AND mapa_item_dotacao.cod_entidade          = solicitacao_item_dotacao.cod_entidade
                              AND mapa_item_dotacao.cod_solicitacao       = solicitacao_item_dotacao.cod_solicitacao
                              AND mapa_item_dotacao.cod_centro            = solicitacao_item_dotacao.cod_centro
                              AND mapa_item_dotacao.cod_item              = solicitacao_item_dotacao.cod_item
                              AND mapa_item_dotacao.cod_conta             = solicitacao_item_dotacao.cod_conta
                              AND mapa_item_dotacao.cod_despesa           = solicitacao_item_dotacao.cod_despesa )

                   LEFT JOIN( SELECT
                                     mapa_item_anulacao.exercicio_solicitacao
                                   , mapa_item_anulacao.cod_entidade
                                   , mapa_item_anulacao.cod_solicitacao
                                   , mapa_item_anulacao.cod_centro
                                   , mapa_item_anulacao.cod_item
                                   , mapa_item_anulacao.cod_conta
                                   , mapa_item_anulacao.cod_despesa
                                   , sum(mapa_item_anulacao.quantidade)  as quantidade
                                   , sum(mapa_item_anulacao.vl_total)    as vl_total
                                FROM compras.mapa_item_anulacao
                            GROUP BY
                                     mapa_item_anulacao.exercicio_solicitacao
                                   , mapa_item_anulacao.cod_entidade
                                   , mapa_item_anulacao.cod_solicitacao
                                   , mapa_item_anulacao.cod_centro
                                   , mapa_item_anulacao.cod_item
                                   , mapa_item_anulacao.cod_conta
                                   , mapa_item_anulacao.cod_despesa ) as mapa_item_anulacao
                          ON(
                                  mapa_item_anulacao.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao
                              AND mapa_item_anulacao.cod_entidade           = mapa_item_dotacao.cod_entidade
                              AND mapa_item_anulacao.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao
                              AND mapa_item_anulacao.cod_centro             = mapa_item_dotacao.cod_centro
                              AND mapa_item_anulacao.cod_item               = mapa_item_dotacao.cod_item
                              AND mapa_item_anulacao.cod_conta              = mapa_item_dotacao.cod_conta
                              AND mapa_item_anulacao.cod_despesa            = mapa_item_dotacao.cod_despesa )
                   -- FIM VALORES DOTACAO

                   -- VALORES SEM DOTACAO
                   LEFT JOIN( SELECT solicitacao_item_anulacao.exercicio
                                   , solicitacao_item_anulacao.cod_entidade
                                   , solicitacao_item_anulacao.cod_solicitacao
                                   , solicitacao_item_anulacao.cod_centro
                                   , solicitacao_item_anulacao.cod_item
                                   , sum(solicitacao_item_anulacao.quantidade) as quantidade
                                   , sum(solicitacao_item_anulacao.vl_total)   as vl_total
                                FROM compras.solicitacao_item_anulacao
                            GROUP BY solicitacao_item_anulacao.exercicio
                                   , solicitacao_item_anulacao.cod_entidade
                                   , solicitacao_item_anulacao.cod_solicitacao
                                   , solicitacao_item_anulacao.cod_centro
                                   , solicitacao_item_anulacao.cod_item ) as solicitacao_item_anulacao
                          ON(     solicitacao_item_anulacao.exercicio        = solicitacao_item.exercicio
                              AND solicitacao_item_anulacao.cod_entidade     = solicitacao_item.cod_entidade
                              AND solicitacao_item_anulacao.cod_solicitacao  = solicitacao_item.cod_solicitacao
                              AND solicitacao_item_anulacao.cod_centro       = solicitacao_item.cod_centro
                              AND solicitacao_item_anulacao.cod_item         = solicitacao_item.cod_item  )

                   LEFT JOIN( SELECT mapa_item.exercicio_solicitacao
                                   , mapa_item.cod_entidade
                                   , mapa_item.cod_solicitacao
                                   , mapa_item.cod_centro
                                   , mapa_item.cod_item
                                   , sum(mapa_item.quantidade) as quantidade
                                   , sum(mapa_item.vl_total)   as vl_total
                                FROM compras.mapa_item
                            GROUP BY mapa_item.exercicio_solicitacao
                                   , mapa_item.cod_entidade
                                   , mapa_item.cod_solicitacao
                                   , mapa_item.cod_centro
                                   , mapa_item.cod_item ) as mapa_item
                          ON(     mapa_item.exercicio_solicitacao = solicitacao_item.exercicio
                              AND mapa_item.cod_entidade          = solicitacao_item.cod_entidade
                              AND mapa_item.cod_solicitacao       = solicitacao_item.cod_solicitacao
                              AND mapa_item.cod_centro            = solicitacao_item.cod_centro
                              AND mapa_item.cod_item              = solicitacao_item.cod_item  )
                   -- FIM VALORES SEM DOTACAO

                   -- HINTS
                   LEFT JOIN  orcamento.despesa
                          ON  despesa.exercicio   = solicitacao_item_dotacao.exercicio
                         AND  despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

                   LEFT JOIN  orcamento.conta_despesa
                          ON  conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
                         AND  conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta
                   LEFT JOIN  orcamento.recurso
                          ON  recurso.exercicio   = despesa.exercicio
                         AND  recurso.cod_recurso = despesa.cod_recurso
                   LEFT JOIN  orcamento.pao
                          ON  pao.exercicio = despesa.exercicio
                         AND  pao.num_pao   = despesa.num_pao

                   LEFT JOIN  orcamento.pao_ppa_acao
              ON  pao_ppa_acao.num_pao = orcamento.pao.num_pao
             AND  pao_ppa_acao.exercicio = orcamento.pao.exercicio
       LEFT JOIN  ppa.acao
              ON  ppa.acao.cod_acao = pao_ppa_acao.cod_acao
                   -- FIM HINTS

            WHERE  solicitacao_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')."
              AND  solicitacao_item.cod_entidade    = ".$this->getDado('cod_entidade')."
              AND  solicitacao_item.exercicio       = '".$this->getDado('exercicio')."'";

        return $stSql;
    }

    # Recupera os dados para a consulta da solicitaçao.
    public function recuperaItemConsultaSolicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItemConsultaSolicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, "", $boTransacao);

        return $obErro;
    }

    public function montaRecuperaItemConsultaSolicitacao()
    {
        $stSql .= "
           SELECT
                   solicitacao_item.cod_item
                ,  solicitacao_item.complemento
                ,  catalogo_item.descricao_resumida
                ,  unidade_medida.nom_unidade
                ,  centro_custo.cod_centro
                ,  centro_custo.descricao

                -- QUANTIDADE DO ITEM MENOS A QUANTIDADE ANULADA
                ,  COALESCE(solicitacao_item.quantidade, 0.00) as quantidade_item

                -- VALOR DO ITEM - VALOR DO ITEM ANULADO
                ,  CASE WHEN solicitacao_item_dotacao.vl_reserva IS NULL THEN
                        COALESCE(solicitacao_item.vl_total, 0.00)::numeric(14,2)
                   ELSE
                        COALESCE(solicitacao_item.vl_total, 0.00)::numeric(14,2)
                   END as vl_total_item

                -- QUANTIDADE - QUANTIDADE ANULADA
                ,  CASE WHEN solicitacao_item_dotacao.quantidade IS NULL THEN
                       COALESCE(solicitacao_item.quantidade, 0.00)
                   ELSE
                       COALESCE(solicitacao_item_dotacao.quantidade, 0.00)
                   END as quantidade

                -- VALOR DO ITEM POR CENTRO DE CUSTO E DOTAÇÃO MENOS O VALOR ANULADO
                ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                        COALESCE(solicitacao_item.vl_total, 0.00)
                    ELSE
                        COALESCE(solicitacao_item_dotacao.vl_reserva, 0.00)
                    END as vl_total

                -- QUANTIDADE ANULADA
                ,  CASE WHEN solicitacao_item_dotacao.cod_despesa IS NULL THEN
                        COALESCE(solicitacao_item_anulacao.quantidade, 0.0000)
                    ELSE
                        COALESCE(solicitacao_item_dotacao_anulacao.quantidade, 0.0000)
                    END as quantidade_anulada

                -- VALOR ANULADO
                ,  CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                        ( SELECT  SUM(solicitacao_item_dotacao_anulacao.vl_anulacao)::numeric(14,4)
                            FROM  compras.solicitacao_item_dotacao_anulacao
                           WHERE  solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                             AND  solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                             AND  solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                             AND  solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                             AND  solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                             AND  solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa )
                   ELSE
                        ( SELECT  SUM(solicitacao_item_anulacao.vl_total)::numeric(14,4)
                            FROM  compras.solicitacao_item_anulacao
                           WHERE  solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                             AND  solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                             AND  solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                             AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                             AND  solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro )
                   END AS vl_anulado

                ,  CASE WHEN solicitacao_item.vl_total > 0 THEN
                      (solicitacao_item.vl_total / solicitacao_item.quantidade)::numeric(14,2)
                   END as vl_unitario

                ,  despesa.cod_despesa
                ,  conta_despesa.descricao AS nomdespesa
                ,  conta_despesa.cod_conta
                ,  conta_despesa.cod_estrutural as desdobramento
                ,  empenho.fn_saldo_dotacao(solicitacao_item_dotacao.exercicio, solicitacao_item_dotacao.cod_despesa) as saldo

                ,  solicitacao_item_dotacao.vl_reserva
                ,  solicitacao_item_dotacao.exercicio

                ,  coalesce(mapa_item_anulacao.quantidade, 0.0000) as mapa_item_anulacao_quantidade
                ,  coalesce(mapa_item_anulacao.valor, 0.00) as mapa_item_anulacao_valor

                ,  coalesce( solicitacao_item_dotacao.quantidade - coalesce(solicitacao_item_anulacao.quantidade, 0.0000) , 0.0000 ) as sol_qnt
                ,  coalesce( solicitacao_item_dotacao.vl_reserva - coalesce(solicitacao_item_anulacao.vl_total, 0.00)   , 0.00   ) as sol_vl
                ,  coalesce( mapa_item.vl_total   , 0.00   ) as map_vl
                ,  coalesce( mapa_item.quantidade , 0.0000 ) as map_qnt

                ,  (
                        SELECT  nom_cgm
                          FROM  sw_cgm
                    INNER JOIN  compras.solicitacao
                            ON  solicitacao.exercicio       = solicitacao_item.exercicio
                           AND  solicitacao.cod_entidade    = solicitacao_item.cod_entidade
                           AND  solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                         WHERE  solicitacao.cgm_solicitante = sw_cgm.numcgm
                   ) as nom_cgm

             FROM  compras.solicitacao_item

                   LEFT JOIN(
                              SELECT sum( solicitacao_item_anulacao.quantidade ) as quantidade
                                   , sum( solicitacao_item_anulacao.vl_total   ) as vl_total
                                   , solicitacao_item_anulacao.exercicio
                                   , solicitacao_item_anulacao.cod_entidade
                                   , solicitacao_item_anulacao.cod_solicitacao
                                   , solicitacao_item_anulacao.cod_centro
                                   , solicitacao_item_anulacao.cod_item
                                FROM compras.solicitacao_item_anulacao
                            GROUP BY solicitacao_item_anulacao.exercicio
                                   , solicitacao_item_anulacao.cod_entidade
                                   , solicitacao_item_anulacao.cod_solicitacao
                                   , solicitacao_item_anulacao.cod_centro
                                   , solicitacao_item_anulacao.cod_item ) as solicitacao_item_anulacao
                          ON(     solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio
                              AND solicitacao_item_anulacao.cod_entidade = solicitacao_item.cod_entidade
                              AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                              AND solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro
                              AND solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item      )

    LEFT JOIN(

      SELECT ( coalesce(sum( mapa_item.vl_total   ), 0.00   ) - coalesce(sum( mapa_item_anulacao.vl_total   ), 0.00  )) as vl_total
           , ( coalesce(sum( mapa_item.quantidade ), 0.0000 ) - coalesce(sum( mapa_item_anulacao.quantidade ), 0.0000)) as quantidade
           , mapa_item.exercicio_solicitacao
           , mapa_item.cod_entidade
           , mapa_item.cod_solicitacao
           , mapa_item.cod_centro
           , mapa_item.cod_item
           , mapa_item_dotacao.cod_despesa
        FROM compras.mapa_item
             JOIN compras.mapa_item_dotacao
               ON mapa_item_dotacao.exercicio              = mapa_item.exercicio
              AND mapa_item_dotacao.cod_mapa               = mapa_item.cod_mapa
              AND mapa_item_dotacao.exercicio_solicitacao  = mapa_item.exercicio_solicitacao
              AND mapa_item_dotacao.cod_entidade           = mapa_item.cod_entidade
              AND mapa_item_dotacao.cod_solicitacao        = mapa_item.cod_solicitacao
              AND mapa_item_dotacao.cod_centro             = mapa_item.cod_centro
              AND mapa_item_dotacao.cod_item               = mapa_item.cod_item
              AND mapa_item_dotacao.lote                   = mapa_item.lote

             LEFT JOIN(
                        SELECT sum( vl_total   ) as vl_total
                             , sum( quantidade ) as quantidade
                             , mapa_item_anulacao.exercicio
                             , mapa_item_anulacao.cod_mapa
                             , mapa_item_anulacao.exercicio_solicitacao
                             , mapa_item_anulacao.cod_entidade
                             , mapa_item_anulacao.cod_solicitacao
                             , mapa_item_anulacao.cod_centro
                             , mapa_item_anulacao.cod_item
                             , mapa_item_anulacao.lote
                             , mapa_item_anulacao.cod_conta
                             , mapa_item_anulacao.cod_despesa
                          FROM compras.mapa_item_anulacao
                      GROUP BY mapa_item_anulacao.exercicio
                             , mapa_item_anulacao.cod_mapa
                             , mapa_item_anulacao.exercicio_solicitacao
                             , mapa_item_anulacao.cod_entidade
                             , mapa_item_anulacao.cod_solicitacao
                             , mapa_item_anulacao.cod_centro
                             , mapa_item_anulacao.cod_item
                             , mapa_item_anulacao.lote
                             , mapa_item_anulacao.cod_conta
                             , mapa_item_anulacao.cod_despesa   ) as mapa_item_anulacao
                    ON(     mapa_item_anulacao.exercicio              = mapa_item_dotacao.exercicio
                        AND mapa_item_anulacao.cod_mapa               = mapa_item_dotacao.cod_mapa
                        AND mapa_item_anulacao.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao
                        AND mapa_item_anulacao.cod_entidade           = mapa_item_dotacao.cod_entidade
                        AND mapa_item_anulacao.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao
                        AND mapa_item_anulacao.cod_centro             = mapa_item_dotacao.cod_centro
                        AND mapa_item_anulacao.cod_item               = mapa_item_dotacao.cod_item
                        AND mapa_item_anulacao.lote                   = mapa_item_dotacao.lote
                        AND mapa_item_anulacao.cod_conta              = mapa_item_dotacao.cod_conta
                        AND mapa_item_anulacao.cod_despesa            = mapa_item_dotacao.cod_despesa    )

    GROUP BY mapa_item.exercicio_solicitacao
           , mapa_item.cod_entidade
                                , mapa_item.cod_solicitacao
                                , mapa_item.cod_centro
                                , mapa_item.cod_item
                                , mapa_item_dotacao.cod_despesa
            ) as mapa_item
       ON(     mapa_item.exercicio_solicitacao = solicitacao_item.exercicio
           AND mapa_item.cod_entidade = solicitacao_item.cod_entidade
           AND mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao
           AND mapa_item.cod_centro = solicitacao_item.cod_centro
           AND mapa_item.cod_item = solicitacao_item.cod_item )

       INNER JOIN  almoxarifado.catalogo_item
               ON  catalogo_item.cod_item = solicitacao_item.cod_item

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

        LEFT JOIN( SELECT solicitacao_item_dotacao_anulacao.exercicio
                        , solicitacao_item_dotacao_anulacao.cod_entidade
                        , solicitacao_item_dotacao_anulacao.cod_solicitacao
                        , solicitacao_item_dotacao_anulacao.cod_centro
                        , solicitacao_item_dotacao_anulacao.cod_item
                        , solicitacao_item_dotacao_anulacao.cod_conta
                        , solicitacao_item_dotacao_anulacao.cod_despesa
                        , sum(solicitacao_item_dotacao_anulacao.quantidade)    as quantidade
                        , sum(solicitacao_item_dotacao_anulacao.vl_anulacao)   as vl_anulacao
                     FROM compras.solicitacao_item_dotacao_anulacao
                 GROUP BY solicitacao_item_dotacao_anulacao.exercicio
                        , solicitacao_item_dotacao_anulacao.cod_entidade
                        , solicitacao_item_dotacao_anulacao.cod_solicitacao
                        , solicitacao_item_dotacao_anulacao.cod_centro
                        , solicitacao_item_dotacao_anulacao.cod_item
                        , solicitacao_item_dotacao_anulacao.cod_conta
                        , solicitacao_item_dotacao_anulacao.cod_despesa ) as solicitacao_item_dotacao_anulacao
               ON(     solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                   AND solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                   AND solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                   AND solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                   AND solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                   AND solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                   AND solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa )

        -- RECUPERA AS ANULACOES DO ITEM NO MAPA DE COMPRAS (quantidade)
        LEFT JOIN  ( SELECT sum(mapa_item_anulacao.quantidade) as quantidade
                          , sum(mapa_item_anulacao.vl_total) as valor
                          , mapa_item_anulacao.exercicio_solicitacao
                          , mapa_item_anulacao.cod_entidade
                          , mapa_item_anulacao.cod_solicitacao
                          , mapa_item_anulacao.cod_centro
                          , mapa_item_anulacao.cod_item
                       FROM compras.mapa_item_anulacao
                   GROUP BY mapa_item_anulacao.exercicio_solicitacao
                          , mapa_item_anulacao.cod_entidade
                          , mapa_item_anulacao.cod_solicitacao
                          , mapa_item_anulacao.cod_centro
                          , mapa_item_anulacao.cod_item                ) as mapa_item_anulacao
               ON  (     mapa_item_anulacao.exercicio_solicitacao = solicitacao_item.exercicio
                     AND mapa_item_anulacao.cod_entidade          = solicitacao_item.cod_entidade
                     AND mapa_item_anulacao.cod_solicitacao       = solicitacao_item.cod_solicitacao
                     AND mapa_item_anulacao.cod_centro            = solicitacao_item.cod_centro
                     AND mapa_item_anulacao.cod_item              = solicitacao_item.cod_item        )

        -- RECUPERA A DOTAÇÃO
        LEFT JOIN  orcamento.despesa
               ON  despesa.exercicio   = solicitacao_item_dotacao.exercicio
              AND  despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

        -- RECUPERA O DESDOBRAMENTO
        LEFT JOIN  orcamento.conta_despesa
               ON  conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
              AND  conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta

            WHERE  solicitacao_item.cod_solicitacao = ".$this->getDado('cod_solicitacao')."
              AND  solicitacao_item.cod_entidade    = ".$this->getDado('cod_entidade')."
              AND  solicitacao_item.exercicio       = '".$this->getDado('exercicio')."'";
//              AND  mapa_item_anulacao.quantidade    > 0.00 ";
        return $stSql;

    }

    public function recuperaDadosSolicitacaoEmMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosSolicitacaoEmMapa().$stFiltro.$stOrdem.") as mapas";
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, "", $boTransacao);

        return $obErro;
    }

    public function montaRecuperaDadosSolicitacaoEmMapa()
    {
        $stSQL .= "     SELECT sum(mapas.qt_mapa )           as qt_mapa                                                                   \n";
        $stSQL .= "          , sum(mapas.vl_mapa )           as vl_mapa                                                                   \n";
        $stSQL .= "          , sum(mapas.qt_mapa_anulacao )  as qt_mapa_anulacao                                                          \n";
        $stSQL .= "          , sum(mapas.vl_mapa_anulacao )  as vl_mapa_anulacao                                                          \n";
        $stSQL .= "       FROM (                                                                                                          \n";
        $stSQL .= "          SELECT                                                                                                       \n";
        $stSQL .= "              CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(total.quantidade)                                                                          \n";
        $stSQL .= "                  FROM  compras.mapa_item as total                                                                     \n";
        $stSQL .= "                 WHERE  total.exercicio             = mapa_item_dotacao.exercicio                                      \n";
        $stSQL .= "                   AND  total.cod_mapa              = mapa_item_dotacao.cod_mapa                                       \n";
        $stSQL .= "                   AND  total.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao                          \n";
        $stSQL .= "                   AND  total.cod_entidade          = mapa_item_dotacao.cod_entidade                                   \n";
        $stSQL .= "                   AND  total.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                                \n";
        $stSQL .= "                   AND  total.cod_centro            = mapa_item_dotacao.cod_centro                                     \n";
        $stSQL .= "                   AND  total.cod_item              = mapa_item_dotacao.cod_item                                       \n";
        $stSQL .= "                   AND  total.lote                  = mapa_item_dotacao.lote                                           \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              ELSE                                                                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(total.quantidade)                                                                          \n";
        $stSQL .= "                  FROM  compras.mapa_item_dotacao as total                                                             \n";
        $stSQL .= "                 WHERE  total.exercicio             = mapa_item_dotacao.exercicio                                      \n";
        $stSQL .= "                   AND  total.cod_mapa              = mapa_item_dotacao.cod_mapa                                       \n";
        $stSQL .= "                   AND  total.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao                          \n";
        $stSQL .= "                   AND  total.cod_entidade          = mapa_item_dotacao.cod_entidade                                   \n";
        $stSQL .= "                   AND  total.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                                \n";
        $stSQL .= "                   AND  total.cod_centro            = mapa_item_dotacao.cod_centro                                     \n";
        $stSQL .= "                   AND  total.cod_item              = mapa_item_dotacao.cod_item                                       \n";
        $stSQL .= "                   AND  total.lote                  = mapa_item_dotacao.lote                                           \n";
        $stSQL .= "                   AND  total.cod_conta             = mapa_item_dotacao.cod_conta                                      \n";
        $stSQL .= "                   AND  total.cod_despesa           = mapa_item_dotacao.cod_despesa                                    \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              END AS qt_mapa                                                                                           \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(total.vl_total)                                                                            \n";
        $stSQL .= "                  FROM  compras.mapa_item as total                                                                     \n";
        $stSQL .= "                 WHERE  total.exercicio             = mapa_item_dotacao.exercicio                                      \n";
        $stSQL .= "                   AND  total.cod_mapa              = mapa_item_dotacao.cod_mapa                                       \n";
        $stSQL .= "                   AND  total.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao                          \n";
        $stSQL .= "                   AND  total.cod_entidade          = mapa_item_dotacao.cod_entidade                                   \n";
        $stSQL .= "                   AND  total.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                                \n";
        $stSQL .= "                   AND  total.cod_centro            = mapa_item_dotacao.cod_centro                                     \n";
        $stSQL .= "                   AND  total.cod_item              = mapa_item_dotacao.cod_item                                       \n";
        $stSQL .= "                   AND  total.lote                  = mapa_item_dotacao.lote                                           \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              ELSE                                                                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(total.vl_dotacao)                                                                          \n";
        $stSQL .= "                  FROM  compras.mapa_item_dotacao as total                                                             \n";
        $stSQL .= "                 WHERE  total.exercicio             = mapa_item_dotacao.exercicio                                      \n";
        $stSQL .= "                   AND  total.cod_mapa              = mapa_item_dotacao.cod_mapa                                       \n";
        $stSQL .= "                   AND  total.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao                          \n";
        $stSQL .= "                   AND  total.cod_entidade          = mapa_item_dotacao.cod_entidade                                   \n";
        $stSQL .= "                   AND  total.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                                \n";
        $stSQL .= "                   AND  total.cod_centro            = mapa_item_dotacao.cod_centro                                     \n";
        $stSQL .= "                   AND  total.cod_item              = mapa_item_dotacao.cod_item                                       \n";
        $stSQL .= "                   AND  total.lote                  = mapa_item_dotacao.lote                                           \n";
        $stSQL .= "                   AND  total.cod_conta             = mapa_item_dotacao.cod_conta                                      \n";
        $stSQL .= "                   AND  total.cod_despesa           = mapa_item_dotacao.cod_despesa                                    \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              END AS vl_mapa                                                                                           \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(mapa_item_anulacao.quantidade)                                                             \n";
        $stSQL .= "                  FROM  compras.mapa_item_anulacao                                                                     \n";
        $stSQL .= "                 WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio                         \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao             \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade                      \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                   \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro                        \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote                              \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              ELSE                                                                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(mapa_item_anulacao.quantidade)                                                             \n";
        $stSQL .= "                  FROM  compras.mapa_item_anulacao                                                                     \n";
        $stSQL .= "                 WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio                         \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao             \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade                      \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                   \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro                        \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote                              \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta                         \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa                       \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              END AS qt_mapa_anulacao                                                                                  \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "           ,  CASE WHEN mapa_item_dotacao.cod_despesa IS NULL THEN                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(mapa_item_anulacao.vl_total)                                                               \n";
        $stSQL .= "                  FROM  compras.mapa_item_anulacao                                                                     \n";
        $stSQL .= "                 WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio                         \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao             \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade                      \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                   \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro                        \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote                              \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              ELSE                                                                                                     \n";
        $stSQL .= "              (                                                                                                        \n";
        $stSQL .= "                SELECT  SUM(mapa_item_anulacao.vl_total)                                                               \n";
        $stSQL .= "                  FROM  compras.mapa_item_anulacao                                                                     \n";
        $stSQL .= "                 WHERE  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio                         \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao             \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade                      \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao                   \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro                        \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item                          \n";
        $stSQL .= "                   AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote                              \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta                         \n";
        $stSQL .= "                   AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa                       \n";
        $stSQL .= "              )                                                                                                        \n";
        $stSQL .= "              END AS vl_mapa_anulacao                                                                                  \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "        FROM  compras.mapa_item                                                                                        \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "  INNER JOIN  compras.mapa_solicitacao                                                                                 \n";
        $stSQL .= "          ON  mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio                                             \n";
        $stSQL .= "         AND  mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade                                          \n";
        $stSQL .= "         AND  mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao                                       \n";
        $stSQL .= "         AND  mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa                                              \n";
        $stSQL .= "         AND  mapa_solicitacao.exercicio             = mapa_item.exercicio                                             \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "  INNER JOIN  compras.solicitacao_item                                                                                 \n";
        $stSQL .= "          ON  solicitacao_item.exercicio       = mapa_item.exercicio_solicitacao                                       \n";
        $stSQL .= "         AND  solicitacao_item.cod_entidade    = mapa_item.cod_entidade                                                \n";
        $stSQL .= "         AND  solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao                                             \n";
        $stSQL .= "         AND  solicitacao_item.cod_centro      = mapa_item.cod_centro                                                  \n";
        $stSQL .= "         AND  solicitacao_item.cod_item        = mapa_item.cod_item                                                    \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "   LEFT JOIN  compras.solicitacao_item_dotacao                                                                         \n";
        $stSQL .= "          ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio                                    \n";
        $stSQL .= "         AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade                                 \n";
        $stSQL .= "         AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao                              \n";
        $stSQL .= "         AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro                                   \n";
        $stSQL .= "         AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item                                     \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "   LEFT JOIN  compras.mapa_item_dotacao                                                                                \n";
        $stSQL .= "          ON  mapa_item_dotacao.exercicio_solicitacao = solicitacao_item_dotacao.exercicio                             \n";
        $stSQL .= "         AND  mapa_item_dotacao.cod_entidade          = solicitacao_item_dotacao.cod_entidade                          \n";
        $stSQL .= "         AND  mapa_item_dotacao.cod_solicitacao       = solicitacao_item_dotacao.cod_solicitacao                       \n";
        $stSQL .= "         AND  mapa_item_dotacao.cod_centro            = solicitacao_item_dotacao.cod_centro                            \n";
        $stSQL .= "         AND  mapa_item_dotacao.cod_item              = solicitacao_item_dotacao.cod_item                              \n";
        $stSQL .= "         AND  mapa_item_dotacao.cod_conta             = solicitacao_item_dotacao.cod_conta                             \n";
        $stSQL .= "         AND  mapa_item_dotacao.cod_despesa           = solicitacao_item_dotacao.cod_despesa                           \n";
        $stSQL .= "         AND  mapa_item.exercicio                     = mapa_item_dotacao.exercicio                                    \n";
        $stSQL .= "         AND  mapa_item.cod_mapa                      = mapa_item_dotacao.cod_mapa                                     \n";
        $stSQL .= "         AND  mapa_item.exercicio_solicitacao         = mapa_item_dotacao.exercicio_solicitacao                        \n";
        $stSQL .= "         AND  mapa_item.cod_entidade                  = mapa_item_dotacao.cod_entidade                                 \n";
        $stSQL .= "         AND  mapa_item.cod_solicitacao               = mapa_item_dotacao.cod_solicitacao                              \n";
        $stSQL .= "         AND  mapa_item.cod_centro                    = mapa_item_dotacao.cod_centro                                   \n";
        $stSQL .= "         AND  mapa_item.cod_item                      = mapa_item_dotacao.cod_item                                     \n";
        $stSQL .= "         AND  mapa_item.lote                          = mapa_item_dotacao.lote                                         \n";
        $stSQL .= "                                                                                                                       \n";
        $stSQL .= "       WHERE  1=1                                                                                                      \n";

        return $stSQL;
    }


    public function recuperaRelacionamentoItemHomologacao(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoItemHomologacao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, "", $boTransacao);

        return $obErro;
    }


    public function montaRecuperaRelacionamentoItemHomologacao()
    {
        $stSql = "         SELECT
                                   catalogo_item.descricao_resumida
                                ,  catalogo_item.descricao as descricao_completa
                                ,  unidade_medida.nom_unidade
                                ,  centro_custo.cod_centro
                                ,  centro_custo.descricao
                                ,  solicitacao_item.cod_item
                                ,  solicitacao_item.complemento
                                ,  solicitacao_item.exercicio
                                ,  despesa.cod_despesa
                                ,  conta_despesa.descricao AS nomdespesa
                                ,  conta_despesa.cod_conta
                                ,  conta_despesa.cod_estrutural as desdobramento
                                ,  empenho.fn_saldo_dotacao(solicitacao_item_dotacao.exercicio,
                                                          solicitacao_item_dotacao.cod_despesa) as saldo
                                -- SALDO DA SOLICITAÇÃO
                                , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                                      (coalesce(sum(solicitacao_item_dotacao.vl_reserva), 0.00) - coalesce(sum(solicitacao_item_dotacao_anulacao.vl_anulacao), 0.00))
                                  ELSE
                                      (coalesce(sum(solicitacao_item.vl_total), 0.00) - coalesce(sum(solicitacao_item_anulacao.vl_total), 0.00))
                                  END AS vl_item_solicitacao

                                , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                                      (coalesce(sum(solicitacao_item_dotacao.quantidade), 0.0000) - coalesce(sum(solicitacao_item_dotacao_anulacao.quantidade), 0.0000))
                                  ELSE
                                     (coalesce(sum(solicitacao_item.quantidade), 0.0000) - coalesce(sum(solicitacao_item_anulacao.quantidade), 0.0000))
                                  END AS qnt_item_solicitacao

                                -- RESERVA DE SALDOS
                                , coalesce(sum(reserva.vl_reserva), 0.00) as vl_reserva

                             FROM compras.solicitacao
                       INNER JOIN  compras.solicitacao_item
                               ON  solicitacao_item.exercicio       = solicitacao.exercicio
                              AND  solicitacao_item.cod_entidade    = solicitacao.cod_entidade
                              AND  solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao
                       INNER JOIN  almoxarifado.catalogo_item
                               ON  catalogo_item.cod_item = solicitacao_item.cod_item
                       INNER JOIN  administracao.unidade_medida
                               ON  catalogo_item.cod_unidade  = unidade_medida.cod_unidade
                              AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
                       INNER JOIN  almoxarifado.centro_custo
                               ON  solicitacao_item.cod_centro   = centro_custo.cod_centro
                        LEFT JOIN  compras.solicitacao_item_dotacao
                               ON  solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                              AND  solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                              AND  solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                              AND  solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                              AND  solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item

                        --- pegando a dotação
                        LEFT JOIN  orcamento.despesa
                               ON  despesa.exercicio   = solicitacao_item_dotacao.exercicio
                              AND  despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

                           --- pegando o desdobramento
                        LEFT JOIN  orcamento.conta_despesa
                               ON  conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
                              AND  conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta

                        LEFT JOIN( SELECT solicitacao_item_anulacao.cod_item
                                        , solicitacao_item_anulacao.exercicio
                                        , solicitacao_item_anulacao.cod_entidade
                                        , solicitacao_item_anulacao.cod_solicitacao
                                        , solicitacao_item_anulacao.cod_centro
                                        , sum(solicitacao_item_anulacao.vl_total) as vl_total
                                        , sum(solicitacao_item_anulacao.quantidade) as quantidade
                                     FROM compras.solicitacao_item_anulacao
                                 GROUP BY solicitacao_item_anulacao.cod_item
                                        , solicitacao_item_anulacao.exercicio
                                        , solicitacao_item_anulacao.cod_entidade
                                        , solicitacao_item_anulacao.cod_solicitacao
                                        , solicitacao_item_anulacao.cod_centro        ) as solicitacao_item_anulacao

                               ON solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                              AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                              AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                              AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                              AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro

                        LEFT JOIN( SELECT solicitacao_item_dotacao_anulacao.exercicio
                                        , solicitacao_item_dotacao_anulacao.cod_entidade
                                        , solicitacao_item_dotacao_anulacao.cod_solicitacao
                                        , solicitacao_item_dotacao_anulacao.cod_centro
                                        , solicitacao_item_dotacao_anulacao.cod_item
                                        , solicitacao_item_dotacao_anulacao.cod_conta
                                        , solicitacao_item_dotacao_anulacao.cod_despesa
                                        , sum(solicitacao_item_dotacao_anulacao.vl_anulacao) as vl_anulacao
                                        , sum(solicitacao_item_dotacao_anulacao.quantidade) as quantidade
                                     FROM compras.solicitacao_item_dotacao_anulacao
                                 GROUP BY solicitacao_item_dotacao_anulacao.exercicio
                                        , solicitacao_item_dotacao_anulacao.cod_entidade
                                        , solicitacao_item_dotacao_anulacao.cod_solicitacao
                                        , solicitacao_item_dotacao_anulacao.cod_centro
                                        , solicitacao_item_dotacao_anulacao.cod_item
                                        , solicitacao_item_dotacao_anulacao.cod_conta
                                        , solicitacao_item_dotacao_anulacao.cod_despesa ) as solicitacao_item_dotacao_anulacao
                               ON solicitacao_item_dotacao_anulacao.exercicio        = solicitacao_item_dotacao.exercicio
                              AND solicitacao_item_dotacao_anulacao.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                              AND solicitacao_item_dotacao_anulacao.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                              AND solicitacao_item_dotacao_anulacao.cod_centro       = solicitacao_item_dotacao.cod_centro
                              AND solicitacao_item_dotacao_anulacao.cod_item         = solicitacao_item_dotacao.cod_item
                              AND solicitacao_item_dotacao_anulacao.cod_conta        = solicitacao_item_dotacao.cod_conta
                              AND solicitacao_item_dotacao_anulacao.cod_despesa      = solicitacao_item_dotacao.cod_despesa

                        LEFT JOIN compras.solicitacao_homologada ON solicitacao_homologada.exercicio       = solicitacao.exercicio
                              AND solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
                              AND solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao

                        LEFT JOIN compras.solicitacao_homologada_reserva
                               ON solicitacao_homologada_reserva.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                              AND solicitacao_homologada_reserva.cod_entidade    = solicitacao_homologada.cod_entidade
                              AND solicitacao_homologada_reserva.exercicio       = solicitacao_homologada.exercicio
                              AND solicitacao_homologada_reserva.exercicio       = solicitacao_item_dotacao.exercicio
                              AND solicitacao_homologada_reserva.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                              AND solicitacao_homologada_reserva.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                              AND solicitacao_homologada_reserva.cod_centro      = solicitacao_item_dotacao.cod_centro
                              AND solicitacao_homologada_reserva.cod_item        = solicitacao_item_dotacao.cod_item
                              AND solicitacao_homologada_reserva.cod_conta       = solicitacao_item_dotacao.cod_conta
                              AND solicitacao_homologada_reserva.cod_despesa     = solicitacao_item_dotacao.cod_despesa

                        LEFT JOIN( SELECT cod_reserva
                                        , exercicio
                                        , vl_reserva
                                     FROM
                                          orcamento.reserva_saldos
                                    WHERE
                                          not exists( SELECT 1
                                                        FROM orcamento.reserva_saldos_anulada
                                                       WHERE reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva
                                                         AND reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio    ) ) as reserva
                               ON(     reserva.cod_reserva = solicitacao_homologada_reserva.cod_reserva
                                   AND reserva.exercicio   = solicitacao_homologada_reserva.exercicio   )

                            WHERE  solicitacao.exercicio       = '".$this->getDado('exercicio')."'
                              AND  solicitacao.cod_entidade    = ".$this->getDado('cod_entidade')."
                              AND  solicitacao.cod_solicitacao = ".$this->getDado('cod_solicitacao')."

                         GROUP BY
                                   catalogo_item.descricao_resumida
                                ,  catalogo_item.descricao
                                ,  unidade_medida.nom_unidade
                                ,  centro_custo.cod_centro
                                ,  centro_custo.descricao
                                ,  solicitacao_item.cod_item
                                ,  solicitacao_item.complemento
                                ,  solicitacao_item.exercicio
                                ,  despesa.cod_despesa
                                ,  conta_despesa.descricao
                                ,  conta_despesa.cod_conta
                                ,  conta_despesa.cod_estrutural
                                ,  solicitacao_item_dotacao.cod_despesa
                                ,  solicitacao_item_dotacao.cod_conta
                                ,  solicitacao_item_dotacao.exercicio
       ";

        return $stSql;
    }

}
?>
