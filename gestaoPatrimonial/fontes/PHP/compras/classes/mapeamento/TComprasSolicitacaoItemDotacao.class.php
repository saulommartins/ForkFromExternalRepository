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
 * Classe de mapeamento da tabela compras.solicitacao_item_dotacao
 * Data de Criação: 30/06/2006

 * @author Analista: Diego Victoria
 * @author Desenvolvedor: Leandro André Zis

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-03.04.01

 $Id: TComprasSolicitacaoItemDotacao.class.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

 /**
  * Efetua conexão com a tabela  compras.solicitacao_item_dotacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
  */
class TComprasSolicitacaoItemDotacao extends Persistente
{
 /**
  * Método Construtor
  * @access Private
  */
    public function TComprasSolicitacaoItemDotacao()
    {
        parent::Persistente();
        $this->setTabela("compras.solicitacao_item_dotacao");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao,cod_centro,cod_item,cod_despesa');

        $this->AddCampo('exercicio'       , 'char'    , true ,true , '4'    ,true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('cod_entidade'    , 'integer' , true ,true , ''     ,true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('cod_solicitacao' , 'integer' , true ,true , ''     ,true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('cod_centro'      , 'integer' , true ,true , ''     ,true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('cod_item'        , 'integer' , true ,true , ''     ,true  , 'TComprasSolicitacaoItem');
        $this->AddCampo('cod_conta'       , 'integer' , true ,true , ''     ,false , true);
        $this->AddCampo('cod_despesa'     , 'integer' , true ,true , ''     ,false , true);
        $this->AddCampo('vl_reserva'      , 'numeric' , true ,true , '14,2' ,false , false);
        $this->AddCampo('quantidade'      , 'numeric' , true ,true , '14,2' ,false , true);
    }

    /*verifica o saldo da dotação especificada em cod_conta/cod_despesa */
    public function recuperaSaldoDotacao()
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaVerificaSaloDotacao();

        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $rsRecordSet->getCampo( 'saldo' );
    }

    public function montaVerificaSaloDotacao()
    {
        $stSql = "select empenho.fn_saldo_dotacao( '" . $this->getDado( 'exercicio'). "'  , " . $this->getDado( 'cod_despesa' ). " ) as saldo";

        return $stSql;
    }

    public function recuperaDadosDotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosDotacao();
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaDadosDotacao()
    {
        $stSql = " SELECT  *
                     FROM  compras.solicitacao_item_dotacao
                    WHERE  exercicio       = ".$this->getDado('exercicio')."
                      AND  cod_entidade    = ".$this->getDado('cod_entidade')."
                      AND  cod_solicitacao = ".$this->getDado('cod_solicitacao');

        if ($this->getDado('cod_item')) {
            $stSql .= " AND cod_item = ".$this->getDado('cod_item');
        }

        if ($this->getDado('cod_centro')) {
            $stSql .= " AND cod_centro = ".$this->getDado('cod_centro');
        }

        return $stSql;
    }

    public function recuperaRelacionamentoItemCentroCustoDotacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoItemCentroCustoDotacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaRelacionamentoItemCentroCustoDotacao()
    {
        $stSql = " SELECT solicitacao_item.exercicio                                                                       \n";
        $stSql.= "       ,solicitacao_item.cod_entidade                                                                    \n";
        $stSql.= "       ,solicitacao_item.cod_solicitacao                                                                 \n";
        $stSql.= "       ,solicitacao_item.cod_centro                                                                      \n";
        $stSql.= "       ,solicitacao_item.cod_item                                                                        \n";
        $stSql.= "       ,solicitacao_item.complemento                                                                     \n";
        $stSql.= "       ,solicitacao_item.quantidade as quantidade_item                                                                      \n";
        $stSql.= "       ,solicitacao_item_dotacao.quantidade as quantidade                                        \n";
        $stSql.= "       ,solicitacao_item.vl_total as vl_total_item                                                                        \n";
        $stSql.= "       ,(solicitacao_item.vl_total/solicitacao_item.quantidade) as vl_unitario                           \n";
        $stSql.= "       ,((solicitacao_item.vl_total/solicitacao_item.quantidade)*solicitacao_item_dotacao.quantidade) as vl_total                            \n";
        $stSql.= "       ,catalogo_item.descricao as nomitem                                                               \n";
        $stSql.= "       ,centro_custo.descricao  as nomcentroCusto                                                        \n";
        $stSql.= "       ,unidade_medida.nom_unidade                                                                       \n";
        $stSql.= "		 ,conta_despesa.descricao AS nomdespesa															   \n";
        $stSql.= "		 ,conta_despesa.cod_estrutural																	   \n";
        $stSql.= "		 ,conta_despesa.cod_conta 																		   \n";
        $stSql.= "		 ,solicitacao_item_dotacao.cod_despesa															   \n";
        $stSql.= "		 ,solicitacao_item_dotacao.vl_reserva															   \n";
        //$stSql.= "       ,(SELECT solicitacao_item.quantidade - COALESCE(SUM(solicitacao_item_anulacao.quantidade),0.0000) \n";
        $stSql.= "       ,(SELECT COALESCE(SUM(solicitacao_item_dotacao_anulacao.quantidade),0.0000) \n";
        $stSql.= "           FROM compras.solicitacao_item_dotacao_anulacao                                                        \n";
        $stSql.= "          WHERE solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item                    \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio                   \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade                \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao             \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item                    \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro                  \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa                 \n";

        $stSql.= "        ) AS quantidade_anulada                                                                          \n";
        //$stSql.= "       ,(SELECT solicitacao_item.vl_total - COALESCE(SUM(solicitacao_item_anulacao.vl_total),0.0000)     \n";
        $stSql.= "       ,(SELECT COALESCE(SUM(solicitacao_item_dotacao_anulacao.vl_anulacao),0.0000)     \n";
        $stSql.= "           FROM compras.solicitacao_item_dotacao_anulacao                                                        \n";
        $stSql.= "          WHERE solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item                    \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio                   \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade                \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao             \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item                    \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro                  \n";
        $stSql.= "            AND solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa                 \n";

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

    public function recuperaRelacionamentoItemDotacao(&$rsRecordSet,   $exercicio, $cod_entidade, $cod_solicitacao)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoItemDotacao( $exercicio, $cod_entidade, $cod_solicitacao );
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaRelacionamentoItemDotacao($exercicio, $cod_entidade, $cod_solicitacao)
    {
       $stSql.= "
       SELECT  *
         FROM (
             SELECT catalogo_item.descricao_resumida
                  ,  unidade_medida.nom_unidade
                  ,  centro_custo.cod_centro
                  ,  centro_custo.descricao
                  --- quantidade do item menos a quantidade anulada
                  ,  solicitacao_item.quantidade -
                     COALESCE((SELECT SUM(solicitacao_item_anulacao.quantidade )
                                FROM compras.solicitacao_item_anulacao
                               WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                 AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                 AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                 AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                 AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                              ),0.00) as quantidade_item

                  --- valor do item menos o valor anulado
                  ,  cast( solicitacao_item.vl_total -
                           COALESCE((SELECT SUM(solicitacao_item_anulacao.vl_total )
                                       FROM compras.solicitacao_item_anulacao
                                      WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item
                                        AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio
                                        AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade
                                        AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                        AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                     ),0.00) as numeric(14,2)) as vl_total_item

                  --- quantidade do item por centro de custo e dotação menos a quantidade anulada
                  ,  solicitacao_item_dotacao.quantidade -
                     COALESCE((SELECT SUM(solicitacao_item_dotacao_anulacao.quantidade )
                                FROM compras.solicitacao_item_dotacao_anulacao
                               WHERE solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                 AND solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                 AND solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                 AND solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                 AND solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                 AND solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                              ),0.00) as quantidade

                  ,  ( CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                            solicitacao_item_dotacao.vl_reserva -
                                               COALESCE((SELECT SUM(solicitacao_item_dotacao_anulacao.vl_anulacao )
                                                          FROM compras.solicitacao_item_dotacao_anulacao
                                                         WHERE solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                                           AND solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                                           AND solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                                           AND solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                                           AND solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                                           AND solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                                        ),0.00)
                       ELSE
                            solicitacao_item.vl_total
                       END ) as vl_total

                  ,  ( CASE WHEN solicitacao_item_dotacao.cod_conta IS NOT NULL THEN
                            solicitacao_item_dotacao.vl_reserva/solicitacao_item_dotacao.quantidade
                       ELSE
                            solicitacao_item.vl_total/solicitacao_item.quantidade
                       END )::numeric(14,2)   as vl_unitario

                  ,  solicitacao_item.cod_item
                  ,  solicitacao_item.complemento
                  ,  despesa.cod_despesa
                  ,  conta_despesa.descricao AS nomdespesa
                  ,  conta_despesa.cod_conta
                  ,  conta_despesa.cod_estrutural as desdobramento
                  ,  empenho.fn_saldo_dotacao(solicitacao_item_dotacao.exercicio,
                                            solicitacao_item_dotacao.cod_despesa) as saldo
                  ,  solicitacao_item_dotacao.vl_reserva
                  ,  solicitacao_item_dotacao.exercicio
                  ,  orcamento_reserva.vl_reserva_orcamento

               FROM  compras.solicitacao_item

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

          LEFT JOIN ( SELECT
                             sum(coalesce(reserva_saldos.vl_reserva, 0.00)) as vl_reserva_orcamento
                           , cod_despesa
                           , exercicio
                        FROM orcamento.reserva_saldos
                    GROUP BY reserva_saldos.cod_despesa
                           , reserva_saldos.exercicio                                       ) as orcamento_reserva
                 ON (     orcamento_reserva.cod_despesa  = despesa.cod_despesa
                      AND orcamento_reserva.exercicio    = despesa.exercicio   )

             --- pegando o desdobramento
          LEFT JOIN  orcamento.conta_despesa
                 ON  conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
                AND  conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta

              WHERE  solicitacao_item.exercicio       = $exercicio
                AND  solicitacao_item.cod_entidade    = $cod_entidade
                AND  solicitacao_item.cod_solicitacao = $cod_solicitacao
        ) as consulta

        WHERE  1=1
        --AND  quantidade > 0
        --AND  vl_total >0  \n";

        return $stSql;

    }

    /*
        Recupera os Itens que pertencem às solicitações inclusas num mapa de compras
    */
    public function recuperaItensDotacaoSolicitacaoMapaCompras(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaItensDotacaoSolicitacaoMapaCompras();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItensDotacaoSolicitacaoMapaCompras()
    {
        if ($this->getDado('inCodSolicitacao'))
            $stFiltro .= " AND solicitacao_item.cod_solicitacao = ".$this->getDado('inCodSolicitacao')." \n";

        if ($this->getDado('inCodEntidade'))
            $stFiltro .= " AND solicitacao_item.cod_entidade = ".$this->getDado('inCodEntidade')." \n";

        if ($this->getDado('stExercicio'))
            $stFiltro .= " AND solicitacao_item.exercicio = ".$this->getDado('stExercicio')." \n";

        if ($this->getDado('inCodItem'))
            $stFiltro .= " AND solicitacao_item.cod_item = ".$this->getDado('inCodItem')." \n";

        if ($this->getDado('inCodCentro'))
            $stFiltro .= " AND solicitaca_item.cod_centro = ".$this->getDado('inCodCentro')." \n";

        $stSql = "SELECT exercicio_solicitacao
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
                       , (quantidade - qtd_anulada ) - (quantidade_mapa - quantidade_anulada_mapa) as quantidade_maxima
                       , vl_total_mapa_item
                       , quantidade_estoque
                       , dotacao
                       , dotacao_nom_conta
                       , conta_despesa
                       , nom_conta
                       , cod_estrutural
                       , vl_reserva
                       --quantidade atendida em mapas
                       , (quantidade_mapa - quantidade_anulada_mapa ) as quantidade_atendida
                       --quantidade a ser inserida no mapa
                       , ( quantidade - qtd_anulada - quantidade_mapa + quantidade_anulada_mapa ) as quantidade_mapa
                       --quantidade total solicitada
                       , ( quantidade - qtd_anulada  ) as quantidade_solicitada
                       , cod_reserva
                       , exercicio_reserva
                    from (


                      select solicitacao_item.exercicio as exercicio_solicitacao
                           , solicitacao_item.cod_entidade
                           , solicitacao_item.cod_solicitacao
                           , solicitacao_item.cod_item
                           , catalogo_item.descricao as item
                           , unidade_medida.nom_unidade
                           , solicitacao_item.quantidade as quantidade_item
                           , solicitacao_item_dotacao.quantidade
                           , solicitacao_item.vl_total as vl_total_item
                           , ((solicitacao_item.vl_total/solicitacao_item.quantidade)*solicitacao_item_dotacao.quantidade) as vl_total
                           , solicitacao_item.complemento
                           , solicitacao_item.cod_centro
                           , centro_custo.descricao as centro_custo
                           , ( solicitacao_item.vl_total / solicitacao_item.quantidade ) as valor_unitario
                           ---- Quantidade Anulada do item por dota??o
                           ,coalesce(
                                      (select sum ( solicitacao_item_dotacao_anulacao.quantidade ) as quantidade
                                        from compras.solicitacao_item_dotacao_anulacao
                                        where solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                          and solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                          and solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                          and solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                          and solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                          and solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                          and solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                       ), 0.0 )  as qtd_anulada
                           ---- Valor anulado do item por dota??o
                           , coalesce(
                                      (select sum ( solicitacao_item_dotacao_anulacao.vl_anulacao ) as vl_total
                                        from compras.solicitacao_item_dotacao_anulacao
                                        where solicitacao_item_dotacao_anulacao.exercicio       = solicitacao_item_dotacao.exercicio
                                          and solicitacao_item_dotacao_anulacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                          and solicitacao_item_dotacao_anulacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                          and solicitacao_item_dotacao_anulacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                          and solicitacao_item_dotacao_anulacao.cod_item        = solicitacao_item_dotacao.cod_item
                                          and solicitacao_item_dotacao_anulacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                          and solicitacao_item_dotacao_anulacao.cod_conta       = solicitacao_item_dotacao.cod_conta
                                       ), 0.0 )  as valor_anulado
                           ---- quantidade incluida em mapas
                           , coalesce (
                                       (select  (coalesce(  sum ( mapa_item_dotacao.quantidade ), 0.0) ) as quantidade
                                          from compras.mapa_item_dotacao
                                         where mapa_item_dotacao.exercicio       = solicitacao_item_dotacao.exercicio
                                           and mapa_item_dotacao.cod_entidade    = solicitacao_item_dotacao.cod_entidade
                                           and mapa_item_dotacao.cod_solicitacao = solicitacao_item_dotacao.cod_solicitacao
                                           and mapa_item_dotacao.cod_centro      = solicitacao_item_dotacao.cod_centro
                                           and mapa_item_dotacao.cod_item        = solicitacao_item_dotacao.cod_item
                                           and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                                           and mapa_item_dotacao.cod_conta       = solicitacao_item_dotacao.cod_conta )
                                      , 0.0 ) as quantidade_mapa
                           ---- quantidade anulada do mapa
                           , coalesce (
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
                                      , 0.0 ) as quantidade_anulada_mapa
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


                           , solicitacao_item_dotacao.cod_despesa as dotacao
                           , solicitacao_item_dotacao.cod_conta as conta_despesa
                         --  , despesa.cod_despesa     as dotacao
                           , conta_despesa.descricao as dotacao_nom_conta
                         --  , desdobramento.cod_conta  as conta_despesa
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
                            and   solicitacao_item_dotacao.cod_item        = solicitacao_homologada_reserva.cod_item
                            and   solicitacao_item_dotacao.cod_despesa        = solicitacao_homologada_reserva.cod_despesa
                            and   solicitacao_item_dotacao.cod_conta        = solicitacao_homologada_reserva.cod_conta )
                      left join orcamento.reserva_saldos
                             on ( solicitacao_homologada_reserva.cod_reserva = reserva_saldos.cod_reserva
                            and   solicitacao_homologada_reserva.exercicio   = reserva_saldos.exercicio )

                      where solicitacao_item.cod_solicitacao is not null
                            $stFiltro
                  ) as itens

                WHERE  (quantidade - qtd_anulada) > 0
                  AND  (quantidade_mapa + quantidade_anulada_mapa) > 0 ";

        return $stSql;
    }

    public function recuperaSolicitacaoItemMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaSolicitacaoItemMapa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaSolicitacaoItemMapa()
    {
        $stSql.= "      SELECT  mapa_item_dotacao.exercicio                                                                                             \n";
        $stSql.= "           ,  mapa_item_dotacao.exercicio_solicitacao                                                                                 \n";
        $stSql.= "           ,  mapa_item_dotacao.cod_entidade                                                                                          \n";
        $stSql.= "           ,  mapa_item_dotacao.cod_solicitacao                                                                                       \n";
        $stSql.= "           ,  mapa_item_dotacao.cod_item                                                                                              \n";
        $stSql.= "           ,  mapa_item_dotacao.cod_despesa                                                                                           \n";
        $stSql.= "           ,  COALESCE(SUM(mapa_item_dotacao.quantidade) ,0.0000)                                                                     \n";
        $stSql.= "              - COALESCE((  SELECT  SUM(mapa_item_anulacao.quantidade)                                                                \n";
        $stSql.= "                          FROM  compras.mapa_item_anulacao                                                                            \n";
        $stSql.= "                         WHERE  mapa_item_anulacao.exercicio              = mapa_item_dotacao.exercicio                               \n";
        $stSql.= "                           AND  mapa_item_anulacao.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao                   \n";
        $stSql.= "                           AND  mapa_item_anulacao.cod_entidade           = mapa_item_dotacao.cod_entidade                            \n";
        $stSql.= "                           AND  mapa_item_anulacao.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao                         \n";
        $stSql.= "                           AND  mapa_item_anulacao.cod_item               = mapa_item_dotacao.cod_item                                \n";
        $stSql.= "                           AND  mapa_item_anulacao.cod_despesa            = mapa_item_dotacao.cod_despesa) ,0.0000)  AS quantidade    \n";
        $stSql.= "           ,  COALESCE(SUM(mapa_item_dotacao.vl_dotacao),0.00)                                                                        \n";
        $stSql.= "              - COALESCE(( select SUM(mapa_item_anulacao.vl_total)                                                                    \n";
        $stSql.= "                       from compras.mapa_item_anulacao                                                                                \n";
        $stSql.= "                      where mapa_item_anulacao.exercicio              = mapa_item_dotacao.exercicio                                   \n";
        $stSql.= "                        AND mapa_item_anulacao.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao                       \n";
        $stSql.= "                        AND mapa_item_anulacao.cod_entidade           = mapa_item_dotacao.cod_entidade                                \n";
        $stSql.= "                        AND mapa_item_anulacao.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao                             \n";
        $stSql.= "                        AND mapa_item_anulacao.cod_item               = mapa_item_dotacao.cod_item                                    \n";
        $stSql.= "                        AND mapa_item_anulacao.cod_despesa            = mapa_item_dotacao.cod_despesa),0.00)  AS vl_total             \n";
        #$stSql.= "       ,COALESCE(SUM(mapa_item_dotacao.quantidade),0.0000)- COALESCE(SUM(mapa_item_anulacao.quantidade),0.0000)  AS quantidade \n";
        #$stSql.= "       ,COALESCE(SUM(mapa_item_dotacao.vl_dotacao),0.00) - COALESCE(SUM(mapa_item_anulacao.vl_total),0.00)  AS vl_total          \n";
        $stSql.= "        FROM  compras.mapa_item_dotacao                                                                                               \n";
        $stSql.= "                                                                                                                                      \n";
        $stSql.= "   LEFT JOIN  compras.mapa_item_anulacao                                                                                              \n";
        $stSql.= "          ON  mapa_item_anulacao.exercicio              = mapa_item_dotacao.exercicio                                                 \n";
        $stSql.= "         AND  mapa_item_anulacao.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao                                     \n";
        $stSql.= "         AND  mapa_item_anulacao.cod_entidade           = mapa_item_dotacao.cod_entidade                                              \n";
        $stSql.= "         AND  mapa_item_anulacao.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao                                           \n";
        $stSql.= "         AND  mapa_item_anulacao.cod_item               = mapa_item_dotacao.cod_item                                                  \n";
        $stSql.= "         AND  mapa_item_anulacao.cod_despesa            = mapa_item_dotacao.cod_despesa                                               \n";

        return $stSql;
    }

}
?>
