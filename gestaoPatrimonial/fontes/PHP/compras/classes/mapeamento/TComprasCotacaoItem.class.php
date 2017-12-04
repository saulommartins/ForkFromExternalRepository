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
    * Classe de mapeamento da tabela compras.cotacao_item
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.05.25
                    uc-03.05.26

    $Id: TComprasCotacaoItem.class.php 63865 2015-10-27 13:55:57Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.cotacao_item
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasCotacaoItem extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasCotacaoItem()
{
    parent::Persistente();
    $this->setTabela("compras.cotacao_item");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_cotacao,cod_item');

    $this->AddCampo('exercicio'  ,   'CHAR', true,   '4', true, 'TComprasCotacao');
    $this->AddCampo('cod_cotacao','INTEGER', true,    '', true, 'TComprasCotacao');
    $this->AddCampo('lote'       ,'INTEGER', true,    '', true, false);
    $this->AddCampo('cod_item'   ,'INTEGER', true,    '', true, 'TAlmoxarifadoCatalogoItem');
    $this->AddCampo('quantidade' ,'NUMERIC', true,'14,4',false, false);

}

function montaRecuperaRelacionamento()
{
    $stSql = "
              select catalogo_item.descricao_resumida
                   , catalogo_item.descricao as descricao_completa
                   , cotacao_item.lote
                   , cotacao_item.quantidade
                   , cotacao_item.cod_item
                   , cotacao_item.cod_cotacao
                   , cotacao_item.exercicio
                   --- valor médio de referencia
                   , cast(   (  ( select sum ( vl_total )
                         from compras.mapa_item
                        where mapa_item.cod_mapa = mapa_cotacao.cod_mapa
                          and mapa_item.cod_item = cotacao_item.cod_item   ) / cotacao_item.quantidade )  as numeric( 14,2) )  as vl_total



                from compras.cotacao_item
                join almoxarifado.catalogo_item
                  on ( cotacao_item.cod_item = catalogo_item.cod_item )
                join compras.cotacao
                  on ( cotacao_item.exercicio   = cotacao.exercicio
                 and   cotacao_item.cod_cotacao = cotacao.cod_cotacao )
                join compras.mapa_cotacao
                  on (  cotacao.exercicio     = mapa_cotacao.exercicio_cotacao
                 and    cotacao.cod_cotacao   = mapa_cotacao.cod_cotacao )
                ";

   return $stSql;
}

function recuperaLotes(&$rsRecordSet,  $inCodMapa, $stExercicio , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLotes ( $inCodMapa, $stExercicio   ).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaLotes($inCodMapa, $stExercicio)
{
    $stSql = "
              select cotacao_item.exercicio
                   , cotacao_item.cod_cotacao
                   , cotacao_item.lote
                   , count( cotacao_item.cod_item ) as numero_itens
                   , sum ( cotacao_item.quantidade ) as quantidade
                   --,(sum ( mapa_item.vl_total)::numeric / sum ( cotacao_item.quantidade)::numeric )::numeric(14,2)  as valor_referencia
                from compras.cotacao_item
                join compras.mapa_cotacao
                  on ( mapa_cotacao.exercicio_cotacao = cotacao_item.exercicio
                 and   mapa_cotacao.cod_cotacao = cotacao_item.cod_cotacao )
          INNER JOIN  compras.cotacao
                  ON  cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                 AND  cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
                left join compras.mapa_item
                    on (cotacao_item.lote       = mapa_item.lote
                    and cotacao_item.cod_item   = mapa_item.cod_item
                    and cotacao_item.quantidade = mapa_item.quantidade )
                where mapa_cotacao.cod_mapa       = $inCodMapa
                  and mapa_cotacao.exercicio_mapa = '$stExercicio'

                -- NÃO PODE LISTAR COTAÇÕES ANULADAS.
                AND NOT EXISTS
                            (
                                SELECT  1
                                  FROM  compras.cotacao_anulada
                                 WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                   AND  cotacao_anulada.exercicio   = cotacao.exercicio
                            )

              group by cotacao_item.exercicio
                   , cotacao_item.cod_cotacao
                   , cotacao_item.lote
              order by cotacao_item.lote

                          ";

    return $stSql;
}

function recuperaFornecedoresPorLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFornecedoresPorLote ().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaFornecedoresPorLote()
{
    $stSql = " select cotacao_item.lote
                    , fornecedor.cgm_fornecedor
                    , sw_cgm.nom_cgm
                    , fornecedor.tipo
                    , sum ( cotacao_fornecedor_item.vl_cotacao ) as vl_total
                    , case when ( count( cotacao_fornecedor_item_desclassificacao.cod_item ) > 0
                               or count( inativado.cgm_fornecedor ) > 0 )
                           then 'desclassificado'
                           else 'classificado'
                      end as status
                    , CASE WHEN (julgamento.timestamp IS NOT NULL) THEN
                        'true'
                      ELSE
                        'false'
                      END as julgado
                    , julgamento_item.justificativa
                    , julgamento_item.ordem
                 from compras.cotacao_item

                 join compras.cotacao_fornecedor_item
                   on ( cotacao_fornecedor_item.exercicio    = cotacao_item.exercicio
                  and   cotacao_fornecedor_item.cod_cotacao = cotacao_item.cod_cotacao
                  and   cotacao_fornecedor_item.cod_item     = cotacao_item.cod_item
                  and   cotacao_fornecedor_item.lote         = cotacao_item.lote )

                 join compras.fornecedor
                   on ( fornecedor.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor )

                 join sw_cgm
                   on ( fornecedor.cgm_fornecedor = sw_cgm.numcgm )

            LEFT JOIN  compras.julgamento
                   ON  cotacao_fornecedor_item.exercicio      = julgamento.exercicio
                  AND  cotacao_fornecedor_item.cod_cotacao    = julgamento.cod_cotacao

            LEFT JOIN compras.julgamento_item
                   ON (
                        cotacao_fornecedor_item.exercicio      = julgamento_item.exercicio
                  AND   cotacao_fornecedor_item.cod_cotacao    = julgamento_item.cod_cotacao
                  AND   cotacao_fornecedor_item.cod_item       = julgamento_item.cod_item
                  AND   cotacao_fornecedor_item.lote           = julgamento_item.lote
                  AND   cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
                      )

                 left
                 join compras.cotacao_fornecedor_item_desclassificacao
                   on ( cotacao_fornecedor_item.cgm_fornecedor =  cotacao_fornecedor_item_desclassificacao.cgm_fornecedor
                  and   cotacao_fornecedor_item.cod_item       =  cotacao_fornecedor_item_desclassificacao.cod_item
                  and   cotacao_fornecedor_item.cod_cotacao    =  cotacao_fornecedor_item_desclassificacao.cod_cotacao
                  and   cotacao_fornecedor_item.exercicio      =  cotacao_fornecedor_item_desclassificacao.exercicio
                  and   cotacao_fornecedor_item.lote           =  cotacao_fornecedor_item_desclassificacao.lote )

                 left
                  join  ( select *
                            from compras.fornecedor_inativacao
                           where fornecedor_inativacao.timestamp_fim is null ) as inativado
                    on ( inativado.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor )
                 where cotacao_item.exercicio   = '" .$this->getDado( 'exercicio' ) . "'
                   and cotacao_item.cod_cotacao = " . $this->getDado( 'cod_cotacao' )."
                   and cotacao_item.lote        = " . $this->getDado( 'lote'        )."
                group by cotacao_item.lote
                       , fornecedor.cgm_fornecedor
                       , julgamento_item.ordem
                       , sw_cgm.nom_cgm
                       , julgamento_item.justificativa
                       , julgamento.timestamp
                       , fornecedor.tipo
              ";

    return $stSql;

}

function recuperaUltimosItensCotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimosItensCotacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaUltimosItensCotacao()
{
    $stSql = "SELECT * FROM compras.cotacao_item
              WHERE 1=1";

    return $stSql;

}

//inclusão para ser usada no OC onde não deve demonstrar debug
function inclusaoNoDebug($boTransacao = '')
{
    $obErro     = new Erro;
    $obConexao  = new Transacao;//Conexao;
    //$this->setAuditoria( new TAuditoria() );

    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaInclusao( $boTransacao, $arBlob, $obConexao );
        // $this->setDebug( 'inclusao' );
        if ($arBlob["qtd_blob"]) {
            $boTranFalse = false;
            if ( !Sessao::getTrataExcecao() && !$boTransacao) {
                $boTransacao = true;
                $boTranFalse = true;
            }

            $obErro = $obConexao->__executaDML( $stSql, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                for ($inX=0; $inX<$arBlob["qtd_blob"]; $inX++) {
                    $obConexao->gravaBlob( $arBlob["blob_oid"][$inX], $arBlob["blob"][$inX] );
                }

                if ($boTranFalse) {
                    $obConexao->fechaTransacao( $boTranFalse, $boTransacao, $obErro );
                }
            }
        }else
            $obErro = $obConexao->__executaDML( $stSql, $boTransacao );
    }

    return $obErro;
}

}
