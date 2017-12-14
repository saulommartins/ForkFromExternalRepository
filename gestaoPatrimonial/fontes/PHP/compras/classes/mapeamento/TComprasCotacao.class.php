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
    * Classe de mapeamento da tabela compras.cotacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.04.04
                    uc-03.05.26

    $Id: TComprasCotacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.cotacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasCotacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasCotacao()
{
    parent::Persistente();
    $this->setTabela("compras.cotacao");

    $this->setCampoCod('cod_cotacao');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'4',true,false);
    $this->AddCampo('cod_cotacao','sequence',true,'',true,false);
    $this->AddCampo('timestamp','timestamp',false,'',false,false);

}

function recuperaQuantidadeItensCotacaoFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montarecuperaQuantidadeItensCotacaoFornecedor ( ) .  $stFiltro .$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montarecuperaQuantidadeItensCotacaoFornecedor()
{
    $stSql = "select mapa_cotacao.cod_mapa
                   , mapa_cotacao.exercicio_mapa
                   , total_itens.*
                   , total_forncedor_itens.*
                from (select cotacao.cod_cotacao
                           , cotacao.exercicio
                           , cotacao_item.lote
                           , count ( cotacao_item.cod_item ) as quantidade_itens
                      from compras.cotacao
                      join compras.cotacao_item
                        on ( cotacao.exercicio   = cotacao_item.exercicio
                       and   cotacao.cod_cotacao = cotacao_item.cod_cotacao )
                      WHERE 1=1
                        -- NÃO PODE LISTAR COTAÇÕES ANULADAS.
                        AND NOT EXISTS
                                    (
                                        SELECT  1
                                          FROM  compras.cotacao_anulada
                                         WHERE  cotacao_anulada.cod_cotacao = cotacao.cod_cotacao
                                           AND  cotacao_anulada.exercicio   = cotacao.exercicio
                                    )
                      group by cotacao.cod_cotacao
                             , cotacao.exercicio
                             , cotacao_item.lote)
                      as total_itens
                 join compras.mapa_cotacao
                   on ( mapa_cotacao.exercicio_cotacao  = total_itens.exercicio
                  and   mapa_cotacao.cod_cotacao        = total_itens.cod_cotacao )

              left join (select cotacao_fornecedor_item.exercicio
                              , cotacao_fornecedor_item.cod_cotacao
                              , cotacao_fornecedor_item.lote
                              , cotacao_fornecedor_item.cgm_fornecedor
                              , count( cotacao_fornecedor_item.cod_item ) as quantidade_itens_fornecedor
                           from compras.cotacao_fornecedor_item
                         group by cotacao_fornecedor_item.exercicio
                              , cotacao_fornecedor_item.cod_cotacao
                              , cotacao_fornecedor_item.lote
                              , cotacao_fornecedor_item.cgm_fornecedor) as total_forncedor_itens
                      on ( total_forncedor_itens.exercicio   = total_itens.exercicio
                     and   total_forncedor_itens.cod_cotacao = total_itens.cod_cotacao
                     and   total_forncedor_itens.lote        = total_itens.lote )                ";

    return $stSql;
}

function recuperaJulgamentoVencedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montarecuperaJulgamentoVencedor( ).$stFiltro .$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montarecuperaJulgamentoVencedor()
{
    $stSql = "
        SELECT
                  julgamento_item.cod_item
                , cotacao_item.quantidade
                , cast (( cotacao_fornecedor_item.vl_cotacao / cotacao_item.quantidade ) as numeric(14,2)) as vl_unitario
                , cotacao_fornecedor_item.vl_cotacao as vl_total
                , julgamento_item.cgm_fornecedor
                , catalogo_item.descricao_resumida
                , catalogo_item.descricao
                , julgamento_item.lote
                , sw_cgm.nom_cgm
                , (     SELECT  complemento
                          FROM  compras.solicitacao_item
                    INNER JOIN  compras.mapa_item
                            ON  solicitacao_item.exercicio       = mapa_item.exercicio
                           AND  solicitacao_item.cod_entidade    = mapa_item.cod_entidade
                           AND  solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
                           AND  solicitacao_item.cod_centro      = mapa_item.cod_centro
                           AND  solicitacao_item.cod_item        = mapa_item.cod_item

                    INNER JOIN  compras.cotacao
                            ON  cotacao_item.cod_cotacao = cotacao.cod_cotacao
                           AND  cotacao_item.exercicio = cotacao.exercicio

                    INNER JOIN  compras.mapa_cotacao
                            ON  mapa_cotacao.cod_cotacao = cotacao.cod_cotacao
                           AND  mapa_cotacao.exercicio_cotacao = cotacao.exercicio

                    INNER JOIN  compras.mapa
                            ON  mapa_cotacao.cod_mapa = mapa.cod_mapa
                           AND  mapa_cotacao.exercicio_cotacao = mapa.exercicio

                         WHERE  mapa_item.cod_mapa  = mapa.cod_mapa
                           AND  mapa_item.exercicio = mapa.exercicio
                           AND  solicitacao_item.complemento <> ''
                      ORDER BY  solicitacao_item.cod_solicitacao DESC
                         LIMIT  1
                   ) as complemento
                , marca.descricao as marca
          FROM  compras.julgamento

    INNER JOIN  compras.julgamento_item
            ON  julgamento_item.cod_cotacao = julgamento.cod_cotacao
           AND  julgamento_item.exercicio   = julgamento.exercicio

    INNER JOIN  almoxarifado.catalogo_item
            ON  catalogo_item.cod_item = julgamento_item.cod_item

    INNER JOIN  compras.cotacao_fornecedor_item
            ON  cotacao_fornecedor_item.cod_item = julgamento_item.cod_item
           AND  cotacao_fornecedor_item.exercicio = julgamento_item.exercicio
           AND  cotacao_fornecedor_item.cod_cotacao = julgamento_item.cod_cotacao
           AND  cotacao_fornecedor_item.cgm_fornecedor = julgamento_item.cgm_fornecedor
           AND  cotacao_fornecedor_item.lote = julgamento_item.lote

    INNER JOIN  compras.cotacao_item
            ON  cotacao_item.cod_cotacao = julgamento_item.cod_cotacao
           AND  cotacao_item.exercicio = julgamento_item.exercicio
           AND  cotacao_item.cod_item = julgamento_item.cod_item
           AND  cotacao_item.lote = julgamento_item.lote

    INNER JOIN  almoxarifado.marca
            ON  marca.cod_marca = cotacao_fornecedor_item.cod_marca

    INNER JOIN  sw_cgm
            ON  sw_cgm.numcgm = julgamento_item.cgm_fornecedor

         -- Retorna somente os ítens dos fornecedores que ganharam.
         WHERE  julgamento_item.ordem = 1 ";

    if ($this->getDado('cod_cotacao'))
        $stSql .= " AND julgamento_item.cod_cotacao = ".$this->getDado('cod_cotacao');

    if ($this->getDado('exercicio'))
       $stSql .= " AND julgamento_item.exercicio = '".$this->getDado('exercicio')."'";

    return $stSql;
}

function verificaUltimoCodCotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaUltimoCodCotacao( ).$stFiltro .$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaVerificaUltimoCodCotacao()
{
    $stSql = "SELECT MAX(cod_cotacao) AS cod_cotacao FROM compras.cotacao
              WHERE 1=1 ";

    return $stSql;
}

function recuperaCotacaoNaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCotacaoNaoAnulada( ).$stFiltro .$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCotacaoNaoAnulada()
{
    $stSql = "SELECT c.timestamp, c.cod_cotacao
                FROM compras.cotacao AS c
          INNER JOIN compras.mapa_cotacao
                  ON c.cod_cotacao = mapa_cotacao.cod_cotacao
                 AND c.exercicio   = mapa_cotacao.exercicio_cotacao
                 AND NOT EXISTS (SELECT *
                                   FROM compras.cotacao_anulada
                                  WHERE cotacao_anulada.cod_cotacao = c.cod_cotacao
                                )
        ";

    return $stSql;
}

//inclusão para ser usada no OC onde não deve demonstrar debug
function inclusaoNoDebug($boTransacao = '')
{
    $obErro     = new Erro;
    $obConexao  = new Transacao;//Conexao;
    if ( !$obErro->ocorreu() ) {
        $stSql = $this->montaInclusao( $boTransacao, $arBlob, $obConexao );
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
