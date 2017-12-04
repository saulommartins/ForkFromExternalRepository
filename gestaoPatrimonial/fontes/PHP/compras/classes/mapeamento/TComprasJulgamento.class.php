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
    * Classe de mapeamento da tabela compras.julgamento
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 28737 $
    $Name$
    $Author: diogo.zarpelon $
    $Date: 2008-03-25 15:47:35 -0300 (Ter, 25 Mar 2008) $

    * Casos de uso: uc-03.04.06
                    uc-03.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TComprasJulgamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasJulgamento()
{
    parent::Persistente();
    $this->setTabela("compras.julgamento");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_cotacao');

    $this->AddCampo('exercicio','char',true,'4',true,true,'TComprasCotacao');
    $this->AddCampo('cod_cotacao','integer',true,true,'',true,'TComprasCotacao');
    $this->AddCampo('timestamp','timestamp',true,true,'',false,false);
    $this->AddCampo('observacao','varchar',false,'200',false,false,false);

}

function recuperaTotalCotacaoFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTotalCotacaoFornecedor  ($stFiltro) .$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTotalCotacaoFornecedor($stFiltro = '')
{
    $stSql = "  select fornecedor.cgm_fornecedor
                     , fornecedor.vl_minimo_nf
                     , sum ( cotacao_fornecedor_item.vl_cotacao ) as vl_total_cotacao
                  from compras.julgamento
                  join compras.julgamento_item
                    on ( julgamento.cod_cotacao = julgamento_item.cod_cotacao
                   and   julgamento.exercicio   = julgamento_item.exercicio )
                  join compras.cotacao_fornecedor_item
                    on ( julgamento_item.exercicio       = cotacao_fornecedor_item.exercicio
                   and   julgamento_item.cod_cotacao     = cotacao_fornecedor_item.cod_cotacao
                   and   julgamento_item.cod_item        = cotacao_fornecedor_item.cod_item
                   and   julgamento_item.cgm_fornecedor  = cotacao_fornecedor_item.cgm_fornecedor
                   and   julgamento_item.lote            = cotacao_fornecedor_item.lote            )
                  join compras.fornecedor
                    on (cotacao_fornecedor_item.cgm_fornecedor = fornecedor.cgm_fornecedor )
                   $stFiltro
                group by fornecedor.cgm_fornecedor
                       , vl_minimo_nf

                ";

        return $stSql;
}

function recuperaDataJulgamento(&$rsRecordSet, $stFiltro , $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDataJulgamento  ($stFiltro) .$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDataJulgamento($stFiltro)
{
    $stSql = " SELECT
                    exercicio ,
                    cod_cotacao ,
                    TO_CHAR(timestamp,'dd/mm/yyyy') as data,
                    observacao
                FROM
                    compras.julgamento ";

    return $stSql.$stFiltro;
}

function recuperaJulgamentoAutorizacao(&$rsRecordSet, $stFiltro, $stGroupBy = "", $stOrder = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaJulgamentoAutorizacao().$stFiltro.$stGroupBy.$stOrder;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaJulgamentoAutorizacao()
{
    $stSql  = "  select                                                                                               \n";
    $stSql .= "          julgamento.exercicio                                                                         \n";
    $stSql .= "       ,  julgamento.cod_cotacao                                                                       \n";
    $stSql .= "       ,  julgamento.observacao                                                                        \n";
    $stSql .= "       ,  autorizacao_empenho.cod_autorizacao                                                          \n";
    $stSql .= "       ,  autorizacao_empenho.exercicio       as autorizacao_exercicio                                 \n";
    $stSql .= "       ,  autorizacao_anulada.dt_anulacao                                                              \n";
    $stSql .= "       ,  autorizacao_anulada.motivo                                                                   \n";
    $stSql .= "    from                                                                                               \n";
    $stSql .= "          compras.julgamento                                                                           \n";
    $stSql .= "          join compras.julgamento_item                                                                 \n";
    $stSql .= "            on julgamento.exercicio    = julgamento_item.exercicio                                     \n";
    $stSql .= "           and julgamento.cod_cotacao  = julgamento_item.cod_cotacao                                   \n";
    $stSql .= "          join empenho.item_pre_empenho_julgamento                                                     \n";
    $stSql .= "            on julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio                  \n";
    $stSql .= "           and julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao                \n";
    $stSql .= "           and julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item                   \n";
    $stSql .= "           and julgamento_item.lote           = item_pre_empenho_julgamento.lote                       \n";
    $stSql .= "           and julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor             \n";
    $stSql .= "          join empenho.item_pre_empenho                                                                \n";
    $stSql .= "            on item_pre_empenho_julgamento.cod_pre_empenho = item_pre_empenho.cod_pre_empenho          \n";
    $stSql .= "           and item_pre_empenho_julgamento.exercicio       = item_pre_empenho.exercicio                \n";
    $stSql .= "           and item_pre_empenho_julgamento.num_item        = item_pre_empenho.num_item                 \n";
    $stSql .= "          join empenho.pre_empenho                                                                     \n";
    $stSql .= "            on item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho                          \n";
    $stSql .= "           and item_pre_empenho.exercicio       = pre_empenho.exercicio                                \n";
    $stSql .= "          join empenho.autorizacao_empenho                                                             \n";
    $stSql .= "            on pre_empenho.exercicio       = autorizacao_empenho.exercicio                             \n";
    $stSql .= "           and pre_empenho.cod_pre_empenho = autorizacao_empenho.cod_pre_empenho                       \n";
    $stSql .= "          left join empenho.autorizacao_anulada                                                        \n";
    $stSql .= "                 on autorizacao_empenho.exercicio       = autorizacao_anulada.exercicio                \n";
    $stSql .= "                and autorizacao_empenho.cod_entidade    = autorizacao_anulada.cod_entidade             \n";
    $stSql .= "                and autorizacao_empenho.cod_autorizacao = autorizacao_anulada.cod_autorizacao          \n";

    return $stSql;
}

function recuperaPorCotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPorCotacao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPorCotacao()
{
    $stSql = " SELECT exercicio ,
              cod_cotacao ,
              TO_CHAR(timestamp,'dd/mm/yyyy') as data,
              timestamp,
              observacao
        FROM  compras.julgamento
        WHERE cod_cotacao = ".$this->getDado('cod_cotacao')."
          AND exercicio   = ".$this->getDado('exercicio')."::VARCHAR";

    return $stSql;
}

}
