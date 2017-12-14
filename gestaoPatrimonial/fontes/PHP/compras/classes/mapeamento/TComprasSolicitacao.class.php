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
  * Classe de mapeamento da tabela compras.solicitacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento

  * Casos de uso: uc-03.04.01

  $Id: TComprasSolicitacao.class.php 65105 2016-04-25 19:30:38Z jean $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  compras.solicitacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasSolicitacao extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TComprasSolicitacao()
    {
        parent::Persistente();
        $this->setTabela("compras.solicitacao");

        $this->setCampoCod('cod_solicitacao');
        $this->setComplementoChave('exercicio,cod_entidade');

        $this->AddCampo('exercicio','char',true,true,'4',true,'TOrcamentoEntidade');
        $this->AddCampo('cod_entidade','integer',true,true,'',true,'TOrcamentoEntidade');
        $this->AddCampo('cod_solicitacao','sequence',true,true,'',true,false);
        $this->AddCampo('cod_almoxarifado','integer',true,true,'',false,'TAlmoxarifadoAlmoxarifado');
        $this->AddCampo('cgm_solicitante','integer',true,true,'',false,'TCGM','numcgm');
        $this->AddCampo('cgm_requisitante','integer',true,true,'',false,'TAdministracaoUsuario','numcgm');
        $this->AddCampo('cod_objeto','integer',true,true,'',false,'TComprasObjeto');
        $this->AddCampo('observacao','text',true,true,'',false,false);
        $this->AddCampo('prazo_entrega','integer',true,true,'',false,false);
        $this->AddCampo('timestamp','timestamp',true,true,'',false,false);
        $this->AddCampo('registro_precos','boolean',true,'',false,false);
    }

    /*
     * Recupera as informações das solicitações que podem ser inclusas num mapa de compras
     */
    public function recuperaSolicitacoesMapaCompras(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaSolicitacoesMapaCompras();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSolicitacoesMapaCompras()
    {
        $stSql .= " select solicitacao.exercicio                                                                                             \n";
        $stSql .= "      , solicitacao.cod_entidade                                                                                          \n";
        $stSql .= "      , sw_cgm.nom_cgm as nom_entidade                                                                                    \n";
        $stSql .= "      , solicitacao.cod_solicitacao                                                                                       \n";
        $stSql .= "      , to_char(solicitacao.timestamp,'dd/mm/yyyy') as data                                                               \n";
        $stSql .= "      , solicitacao.timestamp as timestamp_solicitacao                                                                    \n";
        $stSql .= "      --- total da solicitacao                                                                                            \n";
        $stSql .= "      , ( select sum(vl_total) as vl_total                                                                                \n";
        $stSql .= "            from compras.solicitacao_item                                                                                 \n";
        $stSql .= "           where solicitacao_item.exercicio       = solicitacao.exercicio                                                 \n";
        $stSql .= "             and solicitacao_item.cod_entidade    = solicitacao.cod_entidade                                              \n";
        $stSql .= "             and solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao )                                         \n";
        $stSql .= "         - coalesce (                                                                                                     \n";
        $stSql .= "                      (select sum(vl_total)                                                                               \n";
        $stSql .= "                          from compras.solicitacao_item_anulacao                                                          \n";
        $stSql .= "                         where solicitacao_item_anulacao.exercicio       = solicitacao.exercicio                          \n";
        $stSql .= "                           and solicitacao_item_anulacao.cod_entidade    = solicitacao.cod_entidade                       \n";
        $stSql .= "                           and solicitacao_item_anulacao.cod_solicitacao = solicitacao.cod_solicitacao ), 0 ) as valor_total \n";
        $stSql .= "   --- total em mapa                                                                                                      \n";
        $stSql .= " , ( select COALESCE(SUM(mapa_item.vl_total),0.00)                                                                        \n";
        $stSql .= "     from compras.mapa_item                                                                                               \n";
        $stSql .= "    where mapa_item.exercicio       = solicitacao.exercicio                                                               \n";
        $stSql .= "      and mapa_item.cod_entidade    = solicitacao.cod_entidade                                                            \n";
        $stSql .= "      and mapa_item.cod_solicitacao = solicitacao.cod_solicitacao )                                                       \n";
        $stSql .= "    -                                                                                                                     \n";
        $stSql .= "    ( select COALESCE(SUM(mapa_item_anulacao.vl_total),0.00)                                                              \n";
        $stSql .= "     from compras.mapa_item_anulacao                                                                                      \n";
        $stSql .= "     where mapa_item_anulacao.exercicio       = solicitacao.exercicio                                                     \n";
        $stSql .= "       and mapa_item_anulacao.cod_entidade    = solicitacao.cod_entidade                                                  \n";
        $stSql .= "       and mapa_item_anulacao.cod_solicitacao = solicitacao.cod_solicitacao ) as total_mapas                              \n";
        $stSql .= " , ( select COALESCE(SUM(mapa_item_anulacao.vl_total),0.00)                                                               \n";
        $stSql .= "       from compras.mapa_item_anulacao                                                                                    \n";
        $stSql .= "  where mapa_item_anulacao.exercicio_solicitacao = solicitacao.exercicio                                                  \n";
        $stSql .= "    and mapa_item_anulacao.cod_solicitacao       = solicitacao.cod_solicitacao                                            \n";
        $stSql .= "    and mapa_item_anulacao.cod_entidade          = solicitacao.cod_entidade ) as total_anulado                            \n";
        $stSql .= "   from compras.solicitacao                                                                                               \n";
        $stSql .= "   join orcamento.entidade                                                                                                \n";
        $stSql .= "     on ( solicitacao.cod_entidade = entidade.cod_entidade                                                                \n";
        $stSql .= "    and   solicitacao.exercicio    = entidade.exercicio   )                                                               \n";
        $stSql .= "   join sw_cgm                                                                                                            \n";
        $stSql .= "     on ( entidade.numcgm = sw_cgm.numcgm )                                                                               \n";
        $stSql .= "   join compras.solicitacao_homologada                                                                                    \n";
        $stSql .= "     on ( solicitacao_homologada.exercicio       = solicitacao.exercicio                                                  \n";
        $stSql .= "    and  solicitacao_homologada.cod_entidade     = solicitacao.cod_entidade                                               \n";
        $stSql .= "    and  solicitacao_homologada.cod_solicitacao  = solicitacao.cod_solicitacao )                                          \n";
        $stSql .= "   where solicitacao.cod_solicitacao is not null                                                                          \n";
        if($this->getDado('stExercicio'))
            $stSql .= "     AND solicitacao.exercicio = '".$this->getDado('stExercicio')."'     \n";
        if($this->getDado('inCodSolicitacao'))
            $stSql .= "     AND solicitacao.cod_solicitacao = ".$this->getDado('inCodSolicitacao')."    \n";
        if($this->getDado('inCodEntidade'))
            $stSql .= "     AND solicitacao.cod_entidade = ".$this->getDado('inCodEntidade')."          \n";

        $stSql .= " ORDER BY solicitacao.cod_solicitacao                                             \n";

        return $stSql;
    }

    public function recuperaRelacionamentoSolicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoSolicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoSolicitacao()
    {
        $stSql = " SELECT solicitacao.exercicio                                                 \n";
        $stSql.= "       ,solicitacao.cod_entidade                                              \n";
        $stSql.= "       ,solicitacao.cod_solicitacao                                           \n";
        $stSql.= "       ,TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data                   \n";
        $stSql.= "       ,solicitante.nom_cgm                                                   \n";
        $stSql.= "       ,solicitante.nom_cgm AS solicitante                                    \n";
        $stSql.= "   FROM compras.solicitacao                                                   \n";
        $stSql.= "  INNER JOIN sw_cgm as solicitante                                            \n";
        $stSql.= "          ON (solicitante.numcgm = compras.solicitacao.cgm_solicitante)       \n";
        $stSql.= "       ,compras.solicitacao_item                                              \n";
        $stSql.= "       ,orcamento.entidade                                                    \n";
        $stSql.= "  WHERE solicitacao.cod_entidade    = entidade.cod_entidade                   \n";
        $stSql.= "    AND solicitacao.exercicio       = entidade.exercicio                      \n";
        $stSql.= "    AND solicitacao.exercicio       = '".$this->getDado('exercicio')."'       \n";
        if ( $this->getDado('cod_entidade') ) {
            $stSql.= "    AND solicitacao.cod_entidade    = '".$this->getDado('cod_entidade')."'       \n";
        }
        if ( $this->getDado('cod_solicitacao') ) {
            $stSql.= "    AND solicitacao.cod_solicitacao = '".$this->getDado('cod_solicitacao')."'       \n";
        }
        $stSql.= "    AND solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao        \n";
        $stSql.= "    AND solicitacao.cod_entidade    = solicitacao_item.cod_entidade           \n";
        $stSql.= "    AND solicitacao.exercicio       = solicitacao_item.exercicio              \n";

        return $stSql;
    }

    public function recuperaRelacionamentoSaldo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoSaldo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoSaldo()
    {
        $stSql =" SELECT solicitacao_item.cod_solicitacao                                                               \n";
        $stSql.="       ,solicitacao_item.cod_entidade                                                                  \n";
        $stSql.="       ,solicitacao_item.vl_total                                                                      \n";
        $stSql.="       ,COALESCE((SELECT SUM(solicitacao_item_anulacao.vl_total) AS vl_total                           \n";
        $stSql.="                    FROM compras.solicitacao_item_anulacao                                             \n";
        $stSql.="                   WHERE solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item         \n";
        $stSql.="                     AND solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio        \n";
        $stSql.="                     AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade     \n";
        $stSql.="                     AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao  \n";
        $stSql.="                     AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro       \n";
        $stSql.="                ),0.00)AS vl_total_anulada                                                             \n";
        $stSql.="       ,COALESCE((SELECT SUM(mapa_item.vl_total) AS vl_total                                           \n";
        $stSql.="                    FROM compras.mapa_item                                                             \n";
        $stSql.="                   WHERE mapa_item.cod_item        = solicitacao_item.cod_item                         \n";
        $stSql.="                     AND mapa_item.exercicio       = solicitacao_item.exercicio                        \n";
        $stSql.="                     AND mapa_item.cod_entidade    = solicitacao_item.cod_entidade                     \n";
        $stSql.="                     AND mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao                  \n";
        $stSql.="                     AND mapa_item.cod_centro      = solicitacao_item.cod_centro                       \n";
        $stSql.="                ),0.00)AS vl_total_reservado                                                           \n";
        $stSql.="  FROM compras.solicitacao_item                                                                        \n";
        $stSql.="      ,orcamento.entidade                                                                              \n";
        $stSql.="      ,sw_cgm                                                                                          \n";
        $stSql.=" WHERE solicitacao_item.exercicio       = '".Sessao::getExercicio()."'                                     \n";
        $stSql.="   AND entidade.numcgm                  = sw_cgm.numcgm                                                \n";
        $stSql.="   AND entidade.exercicio               = solicitacao_item.exercicio                                   \n";
        $stSql.="   AND entidade.cod_entidade            = solicitacao_item.cod_entidade                                \n";

        return $stSql;
    }

    public function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "    SELECT   solicitacao.exercicio                                             \n";
        $stSql.= "          ,  solicitacao.cod_entidade                                          \n";
        $stSql.= "          ,  solicitacao.cod_solicitacao                                       \n";
        $stSql.= "          ,  TO_CHAR(solicitacao.timestamp,'hh24:mi:ss') as hora               \n";
        $stSql.= "          ,  TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data               \n";
        $stSql.= "          ,  sw_cgm.nom_cgm                                                    \n";
        $stSql.= "          ,  solicitante.nom_cgm AS solicitante                                \n";
        $stSql.= "          ,  solicitacao.registro_precos                                       \n";
        $stSql.= "       FROM  compras.solicitacao                                               \n";
        $stSql.= "  LEFT JOIN  sw_cgm as solicitante                                             \n";
        $stSql.= "         ON  (solicitante.numcgm = compras.solicitacao.cgm_solicitante)        \n";
        $stSql.= "          ,  compras.solicitacao_item                                          \n";
        $stSql.= "          ,  orcamento.entidade                                                \n";
        $stSql.= "          ,  sw_cgm                                                            \n";
        $stSql.= "      WHERE  solicitacao.cod_entidade    = entidade.cod_entidade               \n";
        $stSql.= "        AND  solicitacao.exercicio       = entidade.exercicio                  \n";
        //$stSql.= "    AND solicitacao.exercicio       = '".Sessao::getExercicio()."'           \n";
        $stSql.= "        AND  solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao    \n";
        $stSql.= "        AND  solicitacao.cod_entidade    = solicitacao_item.cod_entidade       \n";
        $stSql.= "        AND  solicitacao.exercicio       = solicitacao_item.exercicio          \n";
        $stSql.= "        AND  entidade.numcgm             = sw_cgm.numcgm                       \n";

        return $stSql;
    }

    public function recuperaSolicitacoesSaldos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSolicitacoesSaldos() . $stFiltro . $stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function recuperaSolicitacoesNaoAtendidas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSolicitacoesNaoAtendidas() . $stFiltro . $stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function recuperaSolicitacoesNaoAtendidasNaoHomologadas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSolicitacoesNaoAtendidasNaoHomologadas().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaSolicitacoesNaoAtendidasNaoHomologadas()
    {
        $stSql = "
                select * from (
                               SELECT  solicitacao.exercicio
                                      ,solicitacao.cod_solicitacao
                                      ,TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data
                                      ,solicitacao.cod_objeto
                                      ,solicitacao.cod_entidade
                                      ,sw_cgm.nom_cgm
                                      ,sw_cgm.numcgm
                                      ,solicitacao.timestamp
                                      ,(
                                          (
                                              SELECT
                                                  sum(quantidade)
                                              FROM
                                                  compras.solicitacao_item
                                              WHERE
                                                    compras.solicitacao.cod_solicitacao = compras.solicitacao_item.cod_solicitacao
                                              AND compras.solicitacao.cod_entidade    = compras.solicitacao_item.cod_entidade
                                              AND compras.solicitacao.exercicio       = compras.solicitacao_item.exercicio
                                          )
                                          -
                                           (
                                               SELECT
                                                   coalesce(sum(quantidade), 0.0)
                                               FROM
                                                   compras.solicitacao_item_anulacao
                                               WHERE
                                                     compras.solicitacao.cod_solicitacao = compras.solicitacao_item_anulacao.cod_solicitacao
                                              AND compras.solicitacao.cod_entidade    = compras.solicitacao_item_anulacao.cod_entidade
                                              AND compras.solicitacao.exercicio       = compras.solicitacao_item_anulacao.exercicio
                                           )
                                             -
                                           (
                                               SELECT
                                                   coalesce(sum(quantidade), 0.0)
                                               FROM
                                                   compras.mapa_item
                                               WHERE
                                                     compras.solicitacao.cod_solicitacao = compras.mapa_item.cod_solicitacao
                                              AND compras.solicitacao.cod_entidade    = compras.mapa_item.cod_entidade
                                              AND compras.solicitacao.exercicio       = compras.mapa_item.exercicio_solicitacao
                                        )
                                         +
                                        (
                                               SELECT
                                                   coalesce(sum(quantidade), 0.0)
                                               FROM
                                                   compras.mapa_item_anulacao
                                               WHERE
                                                     compras.solicitacao.cod_solicitacao = compras.mapa_item_anulacao.cod_solicitacao
                                                 AND compras.solicitacao.cod_entidade    = compras.mapa_item_anulacao.cod_entidade
                                                 AND compras.solicitacao.exercicio       = compras.mapa_item_anulacao.exercicio_solicitacao
                                        )
                                       ) as qtd_saldo
                                      --,(
                                      --	(
                                      --		SELECT
                                      --			sum(vl_total)
                                      --		FROM
                                      --			compras.solicitacao_item
                                      --		WHERE
                                      --			compras.solicitacao.cod_solicitacao = compras.solicitacao_item.cod_solicitacao
                                      --	)
                                      --	-
                                      -- 	(
                                      -- 		SELECT
                                      -- 			coalesce(sum(vl_total), 0.0)
                                      -- 		FROM
                                      -- 			compras.solicitacao_item_anulacao
                                      -- 		WHERE
                                      -- 			compras.solicitacao.cod_solicitacao = compras.solicitacao_item_anulacao.cod_solicitacao
                                      -- 	)
                                      --	-
                                      -- 	(
                                      -- 		SELECT
                                      -- 			coalesce(sum(vl_total), 0.0)
                                      -- 		FROM
                                      -- 			compras.mapa_item
                                      -- 		WHERE
                                      -- 			compras.solicitacao.cod_solicitacao = compras.mapa_item.cod_solicitacao )
                                      -- 	) as vl_saldo
                                  FROM orcamento.entidade
                                       , sw_cgm
                                       , compras.solicitacao


                                  join compras.solicitacao_item
                                    on ( solicitacao.exercicio       = solicitacao_item.exercicio
                                   and   solicitacao.cod_entidade    = solicitacao_item.cod_entidade
                                   and   solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao )
                               left join compras.mapa_item
                                      on ( mapa_item.exercicio = solicitacao_item.exercicio
                                     and   mapa_item.cod_entidade = solicitacao_item.cod_entidade
                                     and   mapa_item.cod_solicitacao = solicitacao_item.cod_solicitacao
                                     and   mapa_item.cod_centro = solicitacao_item.cod_centro
                                     and   mapa_item.cod_item = solicitacao_item.cod_item )
                               left join compras.solicitacao_item_anulacao
                                      on (solicitacao_item.exercicio        = solicitacao_item_anulacao.exercicio
                                     and  solicitacao_item.cod_entidade     = solicitacao_item_anulacao.cod_entidade
                                     and  solicitacao_item.cod_solicitacao  = solicitacao_item_anulacao.cod_solicitacao
                                     and  solicitacao_item.cod_centro       = solicitacao_item_anulacao.cod_centro
                                     and  solicitacao_item.cod_item         = solicitacao_item_anulacao.cod_item )
                                WHERE
                                    entidade.cod_entidade = solicitacao.cod_entidade
                                    AND entidade.exercicio = '".$this->getDado('exercicio')."'
                                    AND sw_cgm.numcgm = entidade.numcgm

                                    -- TESTE PARA VALIDAR SE NÃO ESTÁ HOMOLOGADA OU SE FOI HOMOLOGADA E ANULADA.

                                    AND
                                    (
                                        NOT EXISTS
                                        (
                                            SELECT 1
                                              FROM compras.mapa_solicitacao
                                                 , compras.solicitacao_homologada
                                             WHERE mapa_solicitacao.exercicio_solicitacao = solicitacao_homologada.exercicio
                                               AND mapa_solicitacao.cod_entidade          = solicitacao_homologada.cod_entidade
                                               AND mapa_solicitacao.cod_solicitacao       = solicitacao_homologada.cod_solicitacao
                                               AND solicitacao_homologada.exercicio       = solicitacao.exercicio
                                               AND solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
                                               AND solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
                                        )
                                    )
                                    AND
                                    (
                                        NOT EXISTS
                                        (
                                            SELECT  1
                                              FROM  compras.solicitacao_homologada
                                             WHERE  solicitacao_homologada.exercicio       = solicitacao.exercicio
                                               AND  solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
                                               AND  solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao
                                        )
                                        OR
                                        (
                                            EXISTS
                                            (
                                                SELECT  1
                                                  FROM  compras.solicitacao_homologada_anulacao
                                                 WHERE  solicitacao_homologada_anulacao.exercicio       = solicitacao.exercicio
                                                   AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao.cod_entidade
                                                   AND  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                                            )
                                        )
                                    )

                                    group by  solicitacao.exercicio
                                        ,solicitacao.cod_entidade
                                        ,solicitacao.cod_solicitacao
                                        ,data
                                        ,solicitacao.cod_objeto
                                        ,sw_cgm.nom_cgm
                                        ,sw_cgm.numcgm
                                        ,solicitacao.timestamp
                               ) as solicitacao
               Where qtd_saldo > 0
--                 and vl_saldo  > 0
                ";

        return $stSql;
  }

    public function recuperaSolicitacoesNaoAtendidasAnular(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSolicitacoesNaoAtendidasAnular().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaSolicitacoesNaoAtendidasAnular()
    {
        $stSql = "
                SELECT * FROM (
                                SELECT  solicitacao.exercicio
                                      ,solicitacao.cod_solicitacao
                                      ,TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data
                                      ,solicitacao.cod_objeto
                                      ,solicitacao.cod_entidade
                                      ,sw_cgm.nom_cgm
                                      ,sw_cgm.numcgm
                                      ,solicitacao.timestamp
                                      ,(
                                          (
                                              SELECT
                                                  sum(quantidade)
                                              FROM
                                                  compras.solicitacao_item
                                              WHERE
                                                  compras.solicitacao.cod_solicitacao = compras.solicitacao_item.cod_solicitacao
                                                AND solicitacao_item.exercicio = '".$this->getDado('exercicio')."'
                                          )
                                          -
                                           (
                                               SELECT
                                                   coalesce(sum(quantidade), 0.0)
                                               FROM
                                                   compras.solicitacao_item_anulacao
                                               WHERE
                                                   compras.solicitacao.cod_solicitacao = compras.solicitacao_item_anulacao.cod_solicitacao
                                                AND solicitacao_item_anulacao.exercicio = '".$this->getDado('exercicio')."'
                                           )
                                             -
                                           (
                                               SELECT
                                                   coalesce(sum(quantidade), 0.0)
                                               FROM
                                                   compras.mapa_item
                                               WHERE
                                                   compras.solicitacao.cod_solicitacao = compras.mapa_item.cod_solicitacao
                                                AND mapa_item.exercicio_solicitacao = '".$this->getDado('exercicio')."'
                                        )
                                         +
                                        (
                                               SELECT
                                                   coalesce(sum(quantidade), 0.0)
                                               FROM
                                                   compras.mapa_item_anulacao
                                               WHERE
                                                   compras.solicitacao.cod_solicitacao = compras.mapa_item_anulacao.cod_solicitacao
                                                   AND mapa_item_anulacao.exercicio_solicitacao = '".$this->getDado('exercicio')."'
                                        )
                                       ) as qtd_saldo
                                       ,mapa_solicitacao.cod_mapa
                                       ,mapa_item.cod_centro
                                       ,mapa_item.cod_item
                                       ,mapa_item.lote
                                FROM orcamento.entidade
                                    , sw_cgm
                                    , compras.solicitacao
                                
                                JOIN compras.solicitacao_item
                                    ON( solicitacao.exercicio       = solicitacao_item.exercicio
                                    and solicitacao.cod_entidade    = solicitacao_item.cod_entidade
                                    and solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao )
                                
                                LEFT JOIN compras.solicitacao_item_anulacao
                                    on( solicitacao_item_anulacao.exercicio        = solicitacao_item.exercicio
                                    and solicitacao_item_anulacao.cod_entidade     = solicitacao_item.cod_entidade
                                    and solicitacao_item_anulacao.cod_solicitacao  = solicitacao_item.cod_solicitacao
                                    and solicitacao_item_anulacao.cod_centro       = solicitacao_item.cod_centro
                                    and solicitacao_item_anulacao.cod_item         = solicitacao_item.cod_item )
                                
                                LEFT JOIN compras.mapa_item
                                    on( mapa_item.exercicio       = solicitacao_item_anulacao.exercicio
                                    and mapa_item.cod_entidade    = solicitacao_item_anulacao.cod_entidade
                                    and mapa_item.cod_solicitacao = solicitacao_item_anulacao.cod_solicitacao
                                    and mapa_item.cod_centro      = solicitacao_item_anulacao.cod_centro
                                    and mapa_item.cod_item        = solicitacao_item_anulacao.cod_item )
                                
                                LEFT JOIN compras.mapa_solicitacao
                                    ON( mapa_solicitacao.exercicio             = mapa_item.exercicio
                                    AND mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                                    AND mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                                    AND mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                                    AND mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao )
                                
                                WHERE
                                    entidade.cod_entidade = solicitacao.cod_entidade
                                    AND entidade.exercicio = '".$this->getDado('exercicio')."'
                                    AND sw_cgm.numcgm = entidade.numcgm
                                GROUP BY solicitacao.exercicio
                                        ,solicitacao.cod_entidade
                                        ,solicitacao.cod_solicitacao
                                        ,data
                                        ,solicitacao.cod_objeto
                                        ,sw_cgm.nom_cgm
                                        ,sw_cgm.numcgm
                                        ,solicitacao.timestamp
                                        ,mapa_solicitacao.cod_mapa
                                        ,mapa_item.cod_centro
                                        ,mapa_item.cod_item
                                        ,mapa_item.lote
                            ) as solicitacao
                WHERE qtd_saldo > 0
                    AND solicitacao.exercicio = '".$this->getDado('exercicio')."'
                    AND (NOT EXISTS( SELECT * FROM compras.solicitacao_homologada
                                        WHERE solicitacao_homologada.exercicio     = solicitacao.exercicio
                                        AND solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
                                        AND solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao )
                        OR EXISTS( SELECT * FROM compras.solicitacao_homologada_anulacao
                                            WHERE solicitacao_homologada_anulacao.exercicio     = solicitacao.exercicio
                                            AND solicitacao_homologada_anulacao.cod_entidade    = solicitacao.cod_entidade
                                            AND solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao )
                        OR EXISTS( SELECT * FROM compras.mapa_solicitacao_anulacao
                                            WHERE mapa_solicitacao_anulacao.exercicio           = solicitacao.exercicio
                                            AND mapa_solicitacao_anulacao.cod_mapa              = solicitacao.cod_mapa
                                            AND mapa_solicitacao_anulacao.exercicio_solicitacao = solicitacao.exercicio
                                            AND mapa_solicitacao_anulacao.cod_entidade          = solicitacao.cod_entidade
                                            AND mapa_solicitacao_anulacao.cod_solicitacao       = solicitacao.cod_solicitacao ) 
                        )
                    AND NOT EXISTS( SELECT * FROM compras.mapa_item_anulacao
                                            WHERE mapa_item_anulacao.exercicio        = solicitacao.exercicio
                                            AND mapa_item_anulacao.cod_entidade     = solicitacao.cod_entidade
                                            AND mapa_item_anulacao.cod_solicitacao  = solicitacao.cod_solicitacao
                                            AND mapa_item_anulacao.cod_mapa         = solicitacao.cod_mapa
                                            AND mapa_item_anulacao.cod_centro       = solicitacao.cod_centro
                                            AND mapa_item_anulacao.cod_item         = solicitacao.cod_item
                                            AND mapa_item_anulacao.exercicio_solicitacao = solicitacao.exercicio
                                            AND mapa_item_anulacao.lote             = solicitacao.lote )
                ";

        return $stSql;
    }

    public function montaRecuperaSolicitacoesSaldos()
    {
        $stSql = "
          SELECT solicitacao.exercicio
               , solicitacao.cod_entidade
               , solicitacao.cod_solicitacao
               , TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data
               , solicitacao.timestamp
               , solicitacao.cod_objeto
               , sw_cgm.nom_cgm
               , total_itens.quantidade
               , total_itens.vl_total
               , total_anulacoes.quantidade as quantidade_anulada
               , total_anulacoes.vl_total  as vl_anulado
             FROM compras.solicitacao
             join orcamento.entidade
               on ( solicitacao.cod_entidade = entidade.cod_entidade
              and   solicitacao.exercicio    = entidade.exercicio )
             join sw_cgm
               on (entidade.numcgm = sw_cgm.numcgm )
             ---- consulta para totalizar os itens
             join (
                   select solicitacao_item.exercicio
                        , solicitacao_item.cod_entidade
                        , solicitacao_item.cod_solicitacao
                        , sum (solicitacao_item.quantidade ) as quantidade
                        , sum (solicitacao_item.vl_total   ) as vl_total
                     from compras.solicitacao_item
                   group by solicitacao_item.exercicio
                        , solicitacao_item.cod_entidade
                        , solicitacao_item.cod_solicitacao) as total_itens
               on ( solicitacao.exercicio       = total_itens.exercicio
              and   solicitacao.cod_entidade    = total_itens.cod_entidade
              and   solicitacao.cod_solicitacao = total_itens.cod_solicitacao  )
             ---- consulta para totalizar as anulações
          left join (
                     select solicitacao_item_anulacao.exercicio
                         , solicitacao_item_anulacao.cod_entidade
                         , solicitacao_item_anulacao.cod_solicitacao
                         , sum (solicitacao_item_anulacao.quantidade ) as quantidade
                         , sum (solicitacao_item_anulacao.vl_total   ) as vl_total
                      from compras.solicitacao_item_anulacao
                    group by solicitacao_item_anulacao.exercicio
                         , solicitacao_item_anulacao.cod_entidade
                         , solicitacao_item_anulacao.cod_solicitacao ) as total_anulacoes
               on (  solicitacao.exercicio       = total_anulacoes.exercicio
              and    solicitacao.cod_entidade    = total_anulacoes.cod_entidade
              and    solicitacao.cod_solicitacao = total_anulacoes.cod_solicitacao  )
          where 1 = 1
                   ";

        return $stSql;
    }

  public function montaRecuperaSolicitacoesNaoAtendidas($stFiltro = '')
  {

    $stSql = "
        SELECT  solicitacao.exercicio
             ,  solicitacao.cod_solicitacao
             ,  TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') AS data
             ,  solicitacao.cod_objeto
             ,  solicitacao.cod_entidade
             ,  sw_cgm.nom_cgm

          FROM  compras.solicitacao

    INNER JOIN  compras.solicitacao_homologada
            ON  solicitacao_homologada.exercicio        = solicitacao.exercicio
           AND  solicitacao_homologada.cod_entidade     = solicitacao.cod_entidade
           AND  solicitacao_homologada.cod_solicitacao  = solicitacao.cod_solicitacao

    INNER JOIN  sw_cgm
            ON  solicitacao.cgm_solicitante = sw_cgm.numcgm

     LEFT JOIN (

		  SELECT solicitacao.exercicio                                                                                             
		        , solicitacao.cod_entidade                                                                                          
		        , solicitacao.cod_solicitacao                                                                                       
		        , COALESCE(total_solicitacao_item.vl_total_solicitacao,0.00) AS vl_total_solicitacao
		        , COALESCE(total_mapa_item.vl_total_mapa,0.00) AS vl_total_mapa                                                          
		        , COALESCE(total_anulado_mapa.vl_total_mapa_anulado,0.00) AS vl_total_mapa_anulado
						
		     FROM compras.solicitacao                                                                                               
		   			   
		 LEFT JOIN (
                                SELECT COALESCE(SUM(solicitacao_item.vl_total),0.00) - COALESCE(SUM(solicitacao_item_anulacao.vl_total),0.00) AS vl_total_solicitacao
                                     , solicitacao_item.exercicio      
                                     , solicitacao_item.cod_entidade   
                                     , solicitacao_item.cod_solicitacao
				      
				  FROM compras.solicitacao_item                                                                                 
				
			      LEFT JOIN compras.solicitacao_item_anulacao                                                          
				     ON solicitacao_item_anulacao.exercicio       = solicitacao_item.exercicio                          
                                    AND solicitacao_item_anulacao.cod_entidade    = solicitacao_item.cod_entidade                       
                                    AND solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                                    AND solicitacao_item_anulacao.cod_centro      = solicitacao_item.cod_centro
                                    AND solicitacao_item_anulacao.cod_item        = solicitacao_item.cod_item

			       GROUP BY solicitacao_item.exercicio      
                                      , solicitacao_item.cod_entidade   
                                      , solicitacao_item.cod_solicitacao

			) AS total_solicitacao_item
			 on  total_solicitacao_item.exercicio       = solicitacao.exercicio
			AND  total_solicitacao_item.cod_entidade    = solicitacao.cod_entidade
			AND  total_solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao

	          LEFT JOIN (
                                SELECT COALESCE(SUM(mapa_item.vl_total),0.00) - COALESCE(SUM(mapa_item_anulacao.vl_total), 0.00) as vl_total_mapa                                                                     
                                     , mapa_item.exercicio       
                                     , mapa_item.cod_entidade    
                                     , mapa_item.cod_solicitacao

				  FROM compras.mapa_item                                                                                               
				 
			     LEFT JOIN compras.mapa_item_anulacao                                                                                      
                                    ON mapa_item_anulacao.exercicio       = mapa_item.exercicio                                                     
                                   AND mapa_item_anulacao.cod_entidade    = mapa_item.cod_entidade                                                  
                                   AND mapa_item_anulacao.cod_solicitacao = mapa_item.cod_solicitacao
                                   AND mapa_item_anulacao.cod_mapa        = mapa_item.cod_mapa
                                   AND mapa_item_anulacao.cod_centro      = mapa_item.cod_centro
                                   AND mapa_item_anulacao.cod_item        = mapa_item.cod_item
                                   AND mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                                   AND mapa_item_anulacao.lote            = mapa_item.lote
				   
			      GROUP BY mapa_item.exercicio       
				     , mapa_item.cod_entidade    
				     , mapa_item.cod_solicitacao
                                     
			) AS total_mapa_item
			  on total_mapa_item.exercicio       = solicitacao.exercicio
			 AND total_mapa_item.cod_entidade    = solicitacao.cod_entidade
			 AND total_mapa_item.cod_solicitacao = solicitacao.cod_solicitacao

		   LEFT JOIN ( 
				SELECT COALESCE(SUM(mapa_item_anulacao.vl_total),0.00) as vl_total_mapa_anulado  
				     , mapa_item_anulacao.exercicio_solicitacao 
				     , mapa_item_anulacao.cod_solicitacao
				     , mapa_item_anulacao.cod_entidade

				  FROM compras.solicitacao                                                            

			      LEFT JOIN compras.mapa_item_anulacao                                                                                    
				     ON mapa_item_anulacao.exercicio_solicitacao = solicitacao.exercicio                                                  
				    AND mapa_item_anulacao.cod_solicitacao       = solicitacao.cod_solicitacao                                            
				    AND mapa_item_anulacao.cod_entidade          = solicitacao.cod_entidade

			       GROUP BY mapa_item_anulacao.exercicio_solicitacao       
				      , mapa_item_anulacao.cod_entidade    
				      , mapa_item_anulacao.cod_solicitacao

			) AS total_anulado_mapa
			  ON total_anulado_mapa.exercicio_solicitacao  = solicitacao.exercicio
			 AND total_anulado_mapa.cod_entidade           = solicitacao.cod_entidade
			 AND total_anulado_mapa.cod_solicitacao        = solicitacao.cod_solicitacao
          ) AS totais
            ON totais.exercicio       = solicitacao.exercicio
           AND totais.cod_entidade    = solicitacao.cod_entidade
           AND totais.cod_solicitacao = solicitacao.cod_solicitacao

         WHERE  1=1
           -- se os valores forem iguias é pq já foram utilizados totalemnte e nao parcialmente, entao nao deve trazer.
           AND totais.vl_total_solicitacao <> totais.vl_total_mapa                                                                  
           AND (
                (totais.vl_total_mapa > 0.00 AND totais.vl_total_mapa <> totais.vl_total_mapa_anulado)
                OR
                (totais.vl_total_mapa = 0.00)
               )
         
           -- A SOLICITAÇÃO NÃO PODE ESTAR ANULADA.
           AND  NOT EXISTS
                (
                    SELECT  1
                      FROM  compras.solicitacao_homologada_anulacao
                     WHERE  solicitacao_homologada_anulacao.exercicio       = solicitacao.exercicio
                       AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao.cod_entidade
                       AND  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                ) \n ";

        return $stSql;
    }

    public function recuperaValoresTotaisSolicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaValoresTotaisSolicitacao",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
    }

    public function montaRecuperaValoresTotaisSolicitacao()
    {
        $stSql = " SELECT sum(vl_total) as total                                                \n";
        $stSql.= "   FROM compras.solicitacao_item                                              \n";
        if ($this->getDado("cod_solicitacao")) {
            $stSql.= " WHERE solicitacao_item.cod_solicitacao = ".$this->getDado("cod_solicitacao")."  \n";
        }
        if ($this->getDado("exercicio")) {
            $stSql.= " AND solicitacao_item.exercicio = '".$this->getDado("exercicio")."'         \n";
        }
        if ($this->getDado("cod_entidade")) {
            $stSql.= " AND solicitacao_item.cod_entidade = ".$this->getDado("cod_entidade")."         \n";
        }
        $stSql.="and not exists (select * from compras.solicitacao_item_anulacao";
        $stSql.="                 where solicitacao_item_anulacao.cod_solicitacao = solicitacao_item.cod_solicitacao";
        $stSql.="                   and solicitacao_item_anulacao.exercicio = solicitacao_item.exercicio";
        $stSql.="                   and solicitacao_item_anulacao.cod_item = solicitacao_item.cod_item";
        $stSql.="                   and solicitacao_item_anulacao.cod_entidade = solicitacao_item.cod_entidade";
        $stSql.="                   and solicitacao_item_anulacao.cod_centro = solicitacao_item.cod_centro)	";

        return $stSql;
    }

    public function recuperaSolicitacaoAgrupadaNaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaSolicitacaoAgrupadaNaoAnulada",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaSolicitacaoAgrupadaNaoAnulada()
    {

        $stSql="select solicitacao.cod_solicitacao
                     , solicitacao.observacao
                     , solicitacao.exercicio
                     , solicitacao.cod_almoxarifado
                     , solicitacao.cod_entidade
                     , solicitacao.cgm_solicitante
                     , solicitacao.cgm_requisitante
                     , solicitacao.cod_objeto
                     , solicitacao.prazo_entrega
                     , solicitacao.timestamp
                  from compras.compra_direta
            inner join compras.mapa_cotacao
                    on mapa_cotacao.cod_mapa = compra_direta.cod_mapa
                   and mapa_cotacao.exercicio_mapa = compra_direta.exercicio_mapa
            inner join compras.cotacao
                    on cotacao.cod_cotacao    = mapa_cotacao.cod_cotacao
                   and cotacao.exercicio      = mapa_cotacao.exercicio_cotacao
            inner join compras.cotacao_item
                    on cotacao_item.cod_cotacao   = cotacao.cod_cotacao
                   and cotacao_item.exercicio     = cotacao.exercicio
            inner join compras.cotacao_fornecedor_item
                    on cotacao_item.cod_cotacao          = cotacao_fornecedor_item.cod_cotacao
                   and cotacao_item.exercicio            = cotacao_fornecedor_item.exercicio
                   and cotacao_item.cod_item             = cotacao_fornecedor_item.cod_item
                   and cotacao_item.lote                 = cotacao_fornecedor_item.lote
            inner join compras.julgamento_item
                    on julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                   and julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                   and julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                   and julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                   and julgamento_item.lote = cotacao_fornecedor_item.lote
            inner join compras.mapa_item
                    on mapa_cotacao.cod_mapa      = mapa_item.cod_mapa
                   and mapa_cotacao.exercicio_mapa= mapa_item.exercicio
                   and mapa_item.cod_item      = cotacao_fornecedor_item.cod_item
                   and mapa_item.lote          = cotacao_fornecedor_item.lote
            inner join compras.mapa_item_dotacao
                    on mapa_item_dotacao.exercicio             = mapa_item.exercicio
                   and mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                   and mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                   and mapa_item_dotacao.cod_item              = mapa_item.cod_item
                   and mapa_item_dotacao.lote                  = mapa_item.lote
            inner join compras.mapa_solicitacao
                    on mapa_solicitacao.exercicio             = mapa_item.exercicio
                   and mapa_solicitacao.cod_entidade          = mapa_item.cod_entidade
                   and mapa_solicitacao.cod_solicitacao       = mapa_item.cod_solicitacao
                   and mapa_solicitacao.cod_mapa              = mapa_item.cod_mapa
                   and mapa_solicitacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
            inner join compras.solicitacao_homologada
                    on solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                   and solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                   and solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
            inner join compras.solicitacao
                    on solicitacao.exercicio       = solicitacao_homologada.exercicio
                   and solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                   and solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
            inner join compras.solicitacao_item
                    on solicitacao_item.exercicio          = mapa_item.exercicio
                   and solicitacao_item.cod_entidade       = mapa_item.cod_entidade
                   and solicitacao_item.cod_solicitacao    = mapa_item.cod_solicitacao
                   and solicitacao_item.cod_centro         = mapa_item.cod_centro
                   and solicitacao_item.cod_item           = mapa_item.cod_item
                   and solicitacao_item.exercicio          = solicitacao.exercicio
                   and solicitacao_item.cod_entidade       = solicitacao.cod_entidade
                   and solicitacao_item.cod_solicitacao    = solicitacao.cod_solicitacao
            inner join compras.solicitacao_item_dotacao
                    on solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio
                   and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade
                   and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao
                   and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro
                   and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item
                   and mapa_item_dotacao.cod_despesa     = solicitacao_item_dotacao.cod_despesa
                 where compra_direta.cod_compra_direta is not null";

        return $stSql;
    }

    # Retorna o menor timestamp da tabela compras.solicitacao
    public function recuperaMinTimestamp(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMinTimestamp().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaMinTimestamp()
    {
        $stSql  = "   SELECT  MIN(timestamp) as timestamp                               \n";
        $stSql .= "        ,  TO_CHAR(MIN(timestamp), 'dd/mm/yyyy hh24:mi:ss') as data  \n";
        $stSql .= "     FROM  compras.solicitacao                                       \n";
        $stSql .= "    WHERE  1=1                                                       \n";

        return $stSql;
    }

    public function recuperaSolicitacaoItensAnulacao(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSolicitacaoItensAnulacao().$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaSolicitacaoItensAnulacao()
    {
        $stSql = "
        SELECT
               solicitacao_item_dotacao.cod_despesa
             , solicitacao_item_dotacao.cod_conta

             , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                   solicitacao_item_dotacao.cod_item
               ELSE
                   solicitacao_item.cod_item
               END as cod_item

             , catalogo_item.descricao_resumida
             , unidade_medida.nom_unidade
             , centro_custo.cod_centro
             , solicitacao_homologada_reserva.cod_reserva
             , centro_custo.descricao
             , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                  (coalesce(solicitacao_item_dotacao.vl_reserva, 0.00) - coalesce(solicitacao_item_dotacao_anulacao.vl_anulacao, 0.00))
               ELSE
                   (coalesce(solicitacao_item.vl_total, 0.00) - coalesce(solicitacao_item_anulacao.vl_total, 0.00))
               END AS vl_dotacao_solicitacao

             , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                   (coalesce(solicitacao_item_dotacao.quantidade, 0.0000) - coalesce(solicitacao_item_dotacao_anulacao.quantidade, 0.0000))
               ELSE
                  (coalesce(solicitacao_item.quantidade, 0.0000) - coalesce(solicitacao_item_anulacao.quantidade, 0.0000))
               END AS qnt_dotacao_solicitacao

             , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                  (coalesce(mapa_item_dotacao.vl_dotacao, 0.00) - coalesce(mapa_item_anulacao.vl_total,0.00)  )
               ELSE
                  (coalesce(mapa_item.vl_total, 0.00) - coalesce(mapa_item_anulacao.vl_total,0.00))
               END AS vl_dotacao_mapa

             , CASE WHEN solicitacao_item_dotacao.cod_conta is not null THEN
                  (coalesce(mapa_item_dotacao.quantidade, 0.0000) - coalesce(mapa_item_anulacao.quantidade, 0.0000))
               ELSE
                  (coalesce(mapa_item.quantidade, 0.0000) - coalesce(mapa_item_anulacao.quantidade, 0.0000))
               END as qnt_dotacao_mapa

          FROM compras.solicitacao
               JOIN compras.solicitacao_item
                 ON solicitacao_item.exercicio        = solicitacao.exercicio
                AND solicitacao_item.cod_entidade     = solicitacao.cod_entidade
                AND solicitacao_item.cod_solicitacao  = solicitacao.cod_solicitacao

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

               -- INICIO DE VALORES E QUANTIDADES
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

               LEFT JOIN( SELECT mapa_item_dotacao.exercicio_solicitacao
                               , mapa_item_dotacao.cod_entidade
                               , mapa_item_dotacao.cod_solicitacao
                               , mapa_item_dotacao.cod_centro
                               , mapa_item_dotacao.cod_item
                               , mapa_item_dotacao.cod_conta
                               , mapa_item_dotacao.cod_despesa
                               , sum(mapa_item_dotacao.quantidade)    as quantidade
                               , sum(mapa_item_dotacao.vl_dotacao)    as vl_dotacao
                            FROM compras.mapa_item_dotacao
                        GROUP BY mapa_item_dotacao.exercicio_solicitacao
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

               LEFT JOIN( SELECT mapa_item_anulacao.exercicio_solicitacao
                               , mapa_item_anulacao.cod_entidade
                               , mapa_item_anulacao.cod_solicitacao
                               , mapa_item_anulacao.cod_centro
                               , mapa_item_anulacao.cod_item
                               , mapa_item_anulacao.cod_conta
                               , mapa_item_anulacao.cod_despesa
                               , sum(mapa_item_anulacao.quantidade)  as quantidade
                               , sum(mapa_item_anulacao.vl_total)    as vl_total
                            FROM compras.mapa_item_anulacao
                        GROUP BY mapa_item_anulacao.exercicio_solicitacao
                               , mapa_item_anulacao.cod_entidade
                               , mapa_item_anulacao.cod_solicitacao
                               , mapa_item_anulacao.cod_centro
                               , mapa_item_anulacao.cod_item
                               , mapa_item_anulacao.cod_conta
                               , mapa_item_anulacao.cod_despesa ) as mapa_item_anulacao
                      ON(     mapa_item_anulacao.exercicio_solicitacao  = mapa_item_dotacao.exercicio_solicitacao
                          AND mapa_item_anulacao.cod_entidade           = mapa_item_dotacao.cod_entidade
                          AND mapa_item_anulacao.cod_solicitacao        = mapa_item_dotacao.cod_solicitacao
                          AND mapa_item_anulacao.cod_centro             = mapa_item_dotacao.cod_centro
                          AND mapa_item_anulacao.cod_item               = mapa_item_dotacao.cod_item
                          AND mapa_item_anulacao.cod_conta              = mapa_item_dotacao.cod_conta
                          AND mapa_item_anulacao.cod_despesa            = mapa_item_dotacao.cod_despesa )

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

               -- INICIO DA ENCHE??O DE LINGUI?A
              INNER JOIN  almoxarifado.catalogo_item
                      ON  catalogo_item.cod_item = solicitacao_item.cod_item
              INNER JOIN  administracao.unidade_medida
                      ON  catalogo_item.cod_unidade  = unidade_medida.cod_unidade
                     AND  catalogo_item.cod_grandeza = unidade_medida.cod_grandeza
              INNER JOIN  almoxarifado.centro_custo
                      ON  solicitacao_item.cod_centro = centro_custo.cod_centro

               LEFT JOIN compras.solicitacao_homologada
                      ON solicitacao_homologada.exercicio       = solicitacao.exercicio
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
         WHERE
               solicitacao.exercicio       = '".$this->getDado('exercicio')."'
           AND solicitacao.cod_entidade    =  ".$this->getDado('cod_entidade')."
           AND solicitacao.cod_solicitacao =  ".$this->getDado('cod_solicitacao')."
            ";

        return $stSql;
    }
}

?>
