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
    * Classe de mapeamento da tabela ALMOXARIFADO.CATALOGO_ITEM
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.03.06

    $Id: TAlmoxarifadoCatalogoItem.class.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.CATALOGO_ITEM
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoCatalogoItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoCatalogoItem()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.catalogo_item');

    $this->setCampoCod('cod_item');
    $this->setComplementoChave('');

    $this->AddCampo('cod_item','integer',true,'',true,false);
    $this->AddCampo('cod_catalogo','integer',true,'',false,true);
    $this->AddCampo('cod_classificacao','integer',true,'',false,true);
    $this->AddCampo('cod_tipo','integer',true,'',false,true);
    $this->AddCampo('cod_unidade','integer',true,'',false,true);
    $this->AddCampo('cod_grandeza','integer',true,'',false,true);
    $this->AddCampo('descricao','varchar',false,'1500',false,false);
    $this->AddCampo('descricao_resumida','varchar',false,'100',false,false);
    $this->AddCampo('ativo','boolean',false,'',false,false);

}

function montaRecuperaRelacionamentoAlmoxarifado()
{
    $stSql  = "SELECT                                                \n";
    $stSql .= "     ac.cod_catalogo,                                 \n";
    $stSql .= "     ac.descricao as desc_catalogo,                   \n";
    $stSql .= "     acc.cod_estrutural,                              \n";
    $stSql .= "     aci.cod_item,                                    \n";
    $stSql .= "     ati.cod_tipo,                                    \n";
    $stSql .= "     ati.descricao as desc_tipo,                      \n";
    $stSql .= "     aci.descricao,                                   \n";
    $stSql .= "     aum.cod_unidade,                                 \n";
    $stSql .= "     aum.cod_grandeza,                                \n";
    $stSql .= "     aum.nom_unidade,                                 \n";
    $stSql .= "FROM                                                  \n";
    $stSql .= "    almoxarifado.catalogo ac,                         \n";
    $stSql .= "    almoxarifado.lancamento_material as spfc ,        \n";
    $stSql .= "    almoxarifado.catalogo_classificacao acc,          \n";
    $stSql .= "    almoxarifado.tipo_item ati,                       \n";
    $stSql .= "    administracao.unidade_medida as aum,              \n";
    $stSql .= "    almoxarifado.catalogo_item aci LEFT OUTER JOIN    \n";
    $stSql .= "    almoxarifado.atributo_catalogo_classificacao_item_valor aacciv ON ( \n";
    $stSql .= "    aacciv.cod_item = aci.cod_item AND                \n";
    $stSql .= "    aacciv.cod_classificacao = aci.cod_classificacao AND \n";
    $stSql .= "    aacciv.cod_catalogo = aci.cod_catalogo )              \n";
    $stSql .= "WHERE                                                 \n";
    $stSql .= "    aum.cod_unidade = aci.cod_unidade AND             \n";
    $stSql .= "    ac.cod_catalogo = aci.cod_catalogo AND            \n";
    $stSql .= "    acc.cod_classificacao = aci.cod_classificacao AND \n";
    $stSql .= "    acc.cod_catalogo = aci.cod_catalogo AND           \n";
    $stSql .= "    ati.cod_tipo = aci.cod_tipo AND                   \n";
    $stSql .= "    aci.cod_grandeza = aum.cod_grandeza               \n";
    $stSql .= "    aci.cod_item = spfc.cod_item                      \n";

    return $stSql;

}

function recuperaRelacionamentoAlmoxarifado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoAlmoxarifado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                \n";
    $stSql .= "     ac.cod_catalogo,                                 \n";
    $stSql .= "     ac.descricao as desc_catalogo,                   \n";
    $stSql .= "     acc.cod_estrutural,                              \n";
    $stSql .= "     aci.cod_item,                                    \n";
    $stSql .= "     ati.cod_tipo,                                    \n";
    $stSql .= "     ati.descricao as desc_tipo,                      \n";
    $stSql .= "     aci.descricao,                                   \n";
    $stSql .= "     aum.cod_unidade,                                 \n";
    $stSql .= "     aum.cod_grandeza,                                \n";
    $stSql .= "     aum.nom_unidade                                  \n";
    $stSql .= "FROM                                                  \n";
    $stSql .= "    almoxarifado.catalogo ac,                         \n";
    $stSql .= "    almoxarifado.catalogo_classificacao acc,          \n";
    $stSql .= "    almoxarifado.tipo_item ati,                       \n";
    $stSql .= "    administracao.unidade_medida as aum,              \n";
    $stSql .= "    almoxarifado.catalogo_item aci LEFT OUTER JOIN    \n";
    $stSql .= "    almoxarifado.atributo_catalogo_classificacao_item_valor aacciv ON ( \n";
    $stSql .= "    aacciv.cod_item = aci.cod_item AND                \n";
    $stSql .= "    aacciv.cod_classificacao = aci.cod_classificacao AND \n";
    $stSql .= "    aacciv.cod_catalogo = aci.cod_catalogo )              \n";
    $stSql .= "WHERE                                                 \n";
    $stSql .= "    aum.cod_unidade = aci.cod_unidade AND             \n";
    $stSql .= "    ac.cod_catalogo = aci.cod_catalogo AND            \n";
    $stSql .= "    acc.cod_classificacao = aci.cod_classificacao AND \n";
    $stSql .= "    acc.cod_catalogo = aci.cod_catalogo AND           \n";
    $stSql .= "    ati.cod_tipo = aci.cod_tipo AND                   \n";
    $stSql .= "    aci.cod_grandeza = aum.cod_grandeza               \n";

    return $stSql;

}

function recuperaDescricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDescricao().$stFiltro.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaDescricao()
{
    $stSql  = "SELECT                                                \n";
    $stSql .= "     catalogo_item.descricao_resumida ,                \n";
    $stSql .= "     catalogo_item.descricao                         \n";
    $stSql .= "FROM                                                  \n";
    $stSql .= "    almoxarifado.catalogo_item                        \n";
    $stSql .= "WHERE                                                 \n";
    $stSql .= "    (LOWER(catalogo_item.descricao_resumida) =  '".strtolower($this->getDado('descricao_resumida'))."' OR \n";
    $stSql .= "    LOWER(catalogo_item.descricao) = '".strtolower($this->getDado('descricao'))."') AND\n";
    $stSql .= "    catalogo_item.cod_classificacao = '".$this->getDado('cod_classificacao')."' \n";

    return $stSql;

}

function recuperaRelacionamentoComSaldo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaRelacionamentoComSaldo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaRelacionamentoComSaldo()
{
    $stSql = "        SELECT ac.cod_catalogo                                                        \n";
    $stSql .= "         , ac.descricao as desc_catalogo                                             \n";
    $stSql .= "         , aci.cod_item                                                              \n";
    $stSql .= "         , aci.descricao                                                             \n";
    $stSql .= "         , acc.cod_estrutural                                                        \n";
    $stSql .= "         , aum.nom_unidade                                                           \n";
    $stSql .= "         , ati.descricao as desc_tipo                                                \n";
    $stSql .= "    FROM ( SELECT lancamento_material.cod_item                                       \n";
    $stSql .= "             , lancamento_material.cod_almoxarifado                                  \n";
    $stSql .= "             , sum(lancamento_material.quantidade) as saldo                          \n";
    $stSql .= "          FROM almoxarifado.lancamento_material                                      \n";
    $stSql .= "      GROUP BY lancamento_material.cod_item                                          \n";
    $stSql .= "                   , lancamento_material.cod_almoxarifado) as spfc                   \n";
    $stSql .= " INNER JOIN almoxarifado.catalogo_item as aci                                        \n";
    $stSql .= "         ON spfc.cod_item = aci.cod_item                                             \n";
    $stSql .= " INNER JOIN administracao.unidade_medida as aum                                      \n";
    $stSql .= "         ON aum.cod_grandeza = aci.cod_grandeza                                      \n";
    $stSql .= "        AND aum.cod_unidade = aci.cod_unidade                                        \n";
    $stSql .= " INNER JOIN almoxarifado.tipo_item as ati                                            \n";
    $stSql .= "         ON ati.cod_tipo = aci.cod_tipo                                              \n";
    $stSql .= " INNER JOIN almoxarifado.catalogo  as ac                                             \n";
    $stSql .= "         ON ac.cod_catalogo = aci.cod_catalogo                                       \n";
    $stSql .= " INNER JOIN almoxarifado.catalogo_classificacao  as acc                              \n";
    $stSql .= "         ON acc.cod_catalogo = aci.cod_catalogo                                      \n";
    $stSql .= "        AND acc.cod_classificacao = aci.cod_classificacao                            \n";
    $stSql .= "  LEFT JOIN almoxarifado.atributo_catalogo_classificacao_item_valor as aacciv        \n";
    $stSql .= "         ON aacciv.cod_item = aci.cod_item                                           \n";
    $stSql .= "        AND aacciv.cod_classificacao = aci.cod_classificacao                         \n";
    $stSql .= "        AND aacciv.cod_catalogo = aci.cod_catalogo                                   \n";
    $stSql .= "      WHERE spfc.saldo > 0                                                           \n";

    return $stSql;

}

function recuperaPorChaveComSaldo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPorChaveComSaldo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaPorChaveComSaldo()
{
    $stSql .= " SELECT                                 \n";
    $stSql .= "     aci.cod_item ,                         \n";
    $stSql .= "     cod_catalogo ,                     \n";
    $stSql .= "     cod_classificacao ,                \n";
    $stSql .= "     cod_tipo ,                         \n";
    $stSql .= "     cod_unidade ,                      \n";
    $stSql .= "     cod_grandeza ,                     \n";
    $stSql .= "     descricao ,                        \n";
    $stSql .= "     descricao_resumida ,               \n";
    $stSql .= "     ativo                              \n";
    $stSql .= " FROM                                   \n";
    $stSql .= "     almoxarifado.catalogo_item     aci, \n";
    $stSql .= "     almoxarifado.estoque_material  aem \n";
    $stSql .= " WHERE                                  \n";
    $stSql .= "     aci.cod_item = aem.cod_item and    \n";
    $stSql .= "     aci.cod_item = ".$this->getDado('cod_item')." \n";
    $stSql .= " GROUP BY                               \n";
    $stSql .= "     aci.cod_item ,                     \n";
    $stSql .= "     cod_catalogo ,                     \n";
    $stSql .= "     cod_classificacao ,                \n";
    $stSql .= "     cod_tipo ,                         \n";
    $stSql .= "     cod_unidade ,                      \n";
    $stSql .= "     cod_grandeza ,                     \n";
    $stSql .= "     descricao ,                        \n";
    $stSql .= "     descricao_resumida ,               \n";
    $stSql .= "     ativo                              \n";

    return $stSql;
}

function verificaTipoItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaTipoItem().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaVerificaTipoItem()
{
    $stSql = " SELECT tipo_item.cod_tipo, tipo_item.descricao
                 from almoxarifado.catalogo_item
                    , almoxarifado.tipo_item
                where cod_item =".$this->getDado('cod_item')."
                  and catalogo_item.cod_tipo = tipo_item.cod_tipo";

    return $stSql;
}

function recuperaItensComSaldoPorCentroCusto(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaItensComSaldoPorCentroCusto",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItensComSaldoPorCentroCusto()
{
    $stSql = "
          SELECT lancamento_material.cod_item
               , lancamento_material.cod_almoxarifado
               , catalogo_item.descricao_resumida
               , lancamento_material.cod_centro
               , centro_custo.descricao as descricao_centro
               , sum(lancamento_material.quantidade) as saldo
               , marca.descricao
            from almoxarifado.lancamento_material
            join almoxarifado.catalogo_item
              on catalogo_item.cod_item = lancamento_material.cod_item
            join almoxarifado.centro_custo
              on centro_custo.cod_centro = lancamento_material.cod_centro
           inner join almoxarifado.marca
              on marca.cod_marca = lancamento_material.cod_marca
           where catalogo_item.ativo = true
    ";

    if ( $this->getDado('cod_almoxarifado') ) {
        $stSqlFiltro .= " and lancamento_material.cod_almoxarifado = ".$this->getDado('cod_almoxarifado');
    }
    if ( $this->getDado('cod_item') ) {
        $stSqlFiltro .= " and lancamento_material.cod_item = ".$this->getDado('cod_item');
    }
    if ( $this->getDado('cod_marca') ) {
        $stSqlFiltro .= " and lancamento_material.cod_marca = ".$this->getDado('cod_marca');
    }
    $stSqlFiltro = " and ".substr($stSqlFiltro,4,strlen($stSqlFiltro)-4);

    $stSqlGroupBy = "
        group by lancamento_material.cod_almoxarifado
               , lancamento_material.cod_item
               , lancamento_material.cod_centro
               , catalogo_item.descricao_resumida
               , centro_custo.descricao
               , marca.descricao

          HAVING ( sum(lancamento_material.quantidade) > 0) ";

    return $stSql.$stSqlFiltro.$stSqlGroupBy;
}

// Para não danificar outras rotinas, este método foi duplicado a partir do recuperaItensComSaldoPorCentroCusto, pois apareceram itens com
// saldo negativo no inventario que devem ser ajustados pelo usuário.
// Foi retirado o having, trazendo assim todos itens do estoque, seja com saldo zegativo, zerado ou positivo.
function recuperaItensComSaldoPorCentroCustoInventario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaItensComSaldoPorCentroCustoInventario",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItensComSaldoPorCentroCustoInventario()
{
    $stSql = "
          SELECT lancamento_material.cod_item
               , lancamento_material.cod_almoxarifado
               , catalogo_item.descricao_resumida
               , lancamento_material.cod_centro
               , centro_custo.descricao as descricao_centro
               , marca.descricao as desc_marca
               , sum(lancamento_material.quantidade) as saldo
            from almoxarifado.lancamento_material
            join almoxarifado.catalogo_item
              on catalogo_item.cod_item = lancamento_material.cod_item
            join almoxarifado.centro_custo
              on centro_custo.cod_centro = lancamento_material.cod_centro
      inner join almoxarifado.marca
              on marca.cod_marca = lancamento_material.cod_marca

           where catalogo_item.ativo = true
    ";

    if ( $this->getDado('cod_almoxarifado') ) {
        $stSqlFiltro .= " and lancamento_material.cod_almoxarifado = ".$this->getDado('cod_almoxarifado');
    }
    if ( $this->getDado('cod_item') ) {
        $stSqlFiltro .= " and lancamento_material.cod_item = ".$this->getDado('cod_item');
    }
    if ( $this->getDado('cod_marca') ) {
        $stSqlFiltro .= " and lancamento_material.cod_marca = ".$this->getDado('cod_marca');
    }
    $stSqlFiltro = " and ".substr($stSqlFiltro,4,strlen($stSqlFiltro)-4);

    $stSqlGroupBy = "
        group by lancamento_material.cod_almoxarifado
               , lancamento_material.cod_item
               , lancamento_material.cod_centro
               , catalogo_item.descricao_resumida
               , centro_custo.descricao
               , marca.descricao";

    return $stSql.$stSqlFiltro.$stSqlGroupBy;
}

function recuperaItensComSaldoPorAlmoxarifado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
     return $this->executaRecupera("montaRecuperaItensComSaldoPorAlmoxarifado",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaItensComSaldoPorAlmoxarifado()
{
    $stSql = "
                SELECT lancamento.cod_almoxarifado
                     , catalogo_classificacao.cod_estrutural
                     , catalogo_item.cod_item
                     , catalogo_item.cod_catalogo
                     , catalogo_item.descricao_resumida
                     , catalogo_item.descricao as descricao_item
                     , unidade_medida.nom_unidade
                     , marca.cod_marca
                     , marca.descricao as descricao_marca
                     , lancamento.saldo
                     , lancamento.cod_centro
                     , centro_custo.descricao as descricao_centro
                from almoxarifado.catalogo_item
                join almoxarifado.catalogo_classificacao
                  on catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao
                join administracao.unidade_medida
                  on unidade_medida.cod_grandeza = catalogo_item.cod_grandeza
                 and unidade_medida.cod_unidade = catalogo_item.cod_unidade
                join almoxarifado.catalogo_item_marca
                  on catalogo_item_marca.cod_item = catalogo_item.cod_item
                join almoxarifado.marca
                  on marca.cod_marca = catalogo_item_marca.cod_marca
                join (
                        SELECT lancamento_material.cod_almoxarifado
                             , lancamento_material.cod_item
                             , lancamento_material.cod_marca
                             , lancamento_material.cod_centro
                             , sum(lancamento_material.quantidade) as saldo
                          from almoxarifado.lancamento_material
                      group by lancamento_material.cod_almoxarifado
                             , lancamento_material.cod_item
                             , lancamento_material.cod_marca
                             , lancamento_material.cod_centro
                     ) as lancamento
                  on lancamento.cod_item = catalogo_item.cod_item
          inner join almoxarifado.centro_custo
                  on centro_custo.cod_centro = lancamento.cod_centro
                 and lancamento.cod_marca = catalogo_item_marca.cod_marca
                where lancamento.saldo > 0
                and catalogo_item.ativo = true
    ";

    if ( $this->getDado('cod_almoxarifado') ) {
        $stSqlFiltro .= " and lancamento.cod_almoxarifado = ".$this->getDado('cod_almoxarifado');
    }
    if ( $this->getDado('cod_estrutural') ) {
        $stSqlFiltro .= " and catalogo_classificacao.cod_estrutural like '".$this->getDado('cod_estrutural')."%' ";
    }
    if ( $this->getDado('cod_catalogo') ) {
        $stSqlFiltro .= " and catalogo_item.cod_catalogo = ".$this->getDado('cod_catalogo');
    }

    return $stSql.$stSqlFiltro;
}

    public function recuperaValorItemUltimaCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaValorItemUltimaCompra().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaValorItemUltimaCompra()
    {
        $stSql = "SELECT
                            CAST
                            (
                                COALESCE((item_pre_empenho.vl_total / item_pre_empenho.quantidade),0) as numeric(14,2)
                            ) as vl_unitario_ultima_compra

                    FROM    empenho.item_pre_empenho_julgamento
                          , empenho.item_pre_empenho
                          , empenho.pre_empenho
                          , empenho.empenho

                   WHERE  item_pre_empenho_julgamento.cod_item        = ".$this->getDado('cod_item')."
                     AND  item_pre_empenho_julgamento.exercicio       = '".$this->getDado('exercicio')."'
                     AND  item_pre_empenho_julgamento.num_item        = item_pre_empenho.num_item
                     AND  item_pre_empenho_julgamento.exercicio       = item_pre_empenho.exercicio
                     AND  item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                     AND  item_pre_empenho.exercicio                  = empenho.exercicio
                     AND  item_pre_empenho.cod_pre_empenho            = empenho.cod_pre_empenho
                     AND  pre_empenho.cod_pre_empenho                 = item_pre_empenho.cod_pre_empenho
                     AND  pre_empenho.exercicio                       = item_pre_empenho.exercicio
                     AND  empenho.cod_pre_empenho                     = pre_empenho.cod_pre_empenho
                     AND  empenho.exercicio                           = pre_empenho.exercicio

          AND NOT EXISTS
                       (
                            SELECT  1
                              FROM  empenho.empenho_anulado_item
                             WHERE  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                               AND  empenho_anulado_item.exercicio       = item_pre_empenho.exercicio
                               AND  empenho_anulado_item.num_item        = item_pre_empenho.num_item
                       )

                ORDER BY  empenho.cod_empenho DESC limit 1";

        return $stSql;
    }

    # Método criado por necessidade do Frota na manutenção de Item.
    public function recuperaItemPorClassificacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItemPorClassificacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaItemPorClassificacao()
    {

        $stSql  = "     SELECT  catalogo_item.*                                                             \n";
        $stSql .= "          ,  catalogo_classificacao.*                                                    \n";
        $stSql .= "                                                                                         \n";
        $stSql .= "       FROM  almoxarifado.catalogo_item                                                  \n";
        $stSql .= "                                                                                         \n";
        $stSql .= " INNER JOIN  almoxarifado.catalogo_classificacao                                         \n";
        $stSql .= "         ON  catalogo_classificacao.cod_classificacao = catalogo_item.cod_classificacao  \n";
        $stSql .= "        AND  catalogo_classificacao.cod_catalogo      = catalogo_item.cod_catalogo       \n";
        $stSql .= "                                                                                         \n";
        $stSql .= "      WHERE  1=1                                                                         \n";

        if ($this->getDado('cod_estrutural')) {
            $stSql .= "    AND  catalogo_classificacao.cod_estrutural ILIKE '".$this->getDado('cod_estrutural')."%' \n";
        }

        if ($this->getDado('cod_catalogo')) {
            $stSql .= "    AND  catalogo_classificacao.cod_catalogo = ".$this->getDado('cod_catalogo')."    \n";
        }

        return $stSql;
    }

}
?>
