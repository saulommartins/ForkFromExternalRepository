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
    * Classe de mapeamento da tabela compras.mapa
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TComprasMapa.class.php 63367 2015-08-20 21:27:34Z michel $

    * Casos de uso: uc-03.04.05
                    uc-03.04.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TComprasMapa extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela("compras.mapa");

        $this->setCampoCod('cod_mapa');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'exercicio'          ,'char'     , true  , true  , '4 ', true , false           );
        $this->AddCampo( 'cod_mapa'           ,'sequence' , true  , true  , ''  , true  ,false           );
        $this->AddCampo( 'cod_objeto'         ,'integer'  , true  , true  , ''  , false ,'TComprasObjeto');
        $this->AddCampo( 'cod_tipo_licitacao' ,'integer'  , true  , true  , ''  , false ,false           );
        $this->AddCampo( 'timestamp'          ,'timestamp', false , false , ''  , false ,false           );

    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
                  select mapa_solicitacao.exercicio
                       , mapa_solicitacao.cod_mapa
                       , mapa_solicitacao.cod_entidade
                       , mapa_solicitacao.cod_solicitacao
                       , mapa_solicitacao.exercicio_solicitacao
                       , sw_cgm.nom_cgm as entidade
                       , to_char( mapa_solicitacao.timestamp, 'dd/mm/yyyy' ) as data
                       , TO_CHAR(mapa.timestamp, 'dd/mm/yyyy hh24:mi:ss') as dt_mapa
                       , mapa.cod_tipo_licitacao
                       , tipo_licitacao.descricao as descricao_tipo_licitacao
                       , mapa.cod_objeto
                       , objeto.descricao as descricao_objeto
                  from compras.mapa
                  join compras.mapa_solicitacao
                    on ( mapa_solicitacao.exercicio = mapa.exercicio
                   and mapa_solicitacao.cod_mapa  = mapa.cod_mapa )
                  join orcamento.entidade
                    on ( entidade.cod_entidade = mapa_solicitacao.cod_entidade
                   and entidade.exercicio    = mapa_solicitacao.exercicio)
                  join compras.tipo_licitacao
                    on ( mapa.cod_tipo_licitacao = tipo_licitacao.cod_tipo_licitacao )
                  join sw_cgm
                    on (entidade.numcgm = sw_cgm.numcgm)
                  join compras.objeto
                    on ( mapa.cod_objeto = objeto.cod_objeto )

     ";

        return $stSql;

    }

    public function recuperaMapaSolicitacoes(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapaSolicitacoes().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaSolicitacoes()
    {
        $stSql = "SELECT  solicitacao.exercicio
                       ,  solicitacao.cod_entidade
                       ,  sw_cgm.nom_cgm as nom_entidade
                       ,  solicitacao.cod_solicitacao
                       ,  TO_CHAR(solicitacao_homologada.timestamp,'dd/mm/yyyy') as data
                       ,  TO_CHAR(solicitacao.timestamp,'dd/mm/yyyy') as data_solicitacao

                       -- TOTAL DA SOLICITAÇÃO (TOTAL - ANULAÇÃO)
                       ,  ( SELECT  COALESCE(SUM(vl_total), 0.00)
                              FROM  compras.solicitacao_item
                             WHERE  solicitacao_item.exercicio       = solicitacao.exercicio
                               AND  solicitacao_item.cod_entidade    = solicitacao.cod_entidade
                               AND  solicitacao_item.cod_solicitacao = solicitacao.cod_solicitacao
                          ) -
                          ( SELECT  COALESCE(SUM(vl_total), 0.00)
                              FROM  compras.solicitacao_item_anulacao
                             WHERE  solicitacao_item_anulacao.exercicio       = solicitacao.exercicio
                               AND  solicitacao_item_anulacao.cod_entidade    = solicitacao.cod_entidade
                               AND  solicitacao_item_anulacao.cod_solicitacao = solicitacao.cod_solicitacao
                          ) AS valor_total

                       -- TOTAL EM MAPA
                       ,  ( SELECT  COALESCE(SUM(vl_total), 0.00)
                              FROM  compras.mapa_item
                             WHERE  mapa_item.exercicio_solicitacao = solicitacao.exercicio
                               AND  mapa_item.cod_solicitacao       = solicitacao.cod_solicitacao
                               AND  mapa_item.cod_entidade          = solicitacao.cod_entidade
                          ) - COALESCE(anulacao.total_anulado, 0.00) AS total_mapas

                       -- TOTAL NESTE MAPA
                       ,  (total.total_mapa - total.total_mapa_anulado) as total_mapa
                       ,  COALESCE(anulacao.total_anulado, 0.00) as total_anulado
                       ,  COALESCE(total.total_mapa_anulado, 0.00) as total_mapa_anulado
                       ,  solicitacao.registro_precos

                    FROM  compras.solicitacao

              INNER JOIN  orcamento.entidade
                      ON  solicitacao.cod_entidade = entidade.cod_entidade
                     AND  solicitacao.exercicio    = entidade.exercicio

              INNER JOIN  sw_cgm
                      ON  entidade.numcgm = sw_cgm.numcgm

              INNER JOIN  compras.solicitacao_homologada
                      ON  solicitacao_homologada.exercicio       = solicitacao.exercicio
                     AND  solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade
                     AND  solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao

              INNER JOIN  compras.mapa_solicitacao
                      ON  mapa_solicitacao.exercicio_solicitacao = solicitacao_homologada.exercicio
                     AND  mapa_solicitacao.cod_entidade          = solicitacao_homologada.cod_entidade
                     AND  mapa_solicitacao.cod_solicitacao       = solicitacao_homologada.cod_solicitacao

              INNER JOIN  (
                            SELECT  mapa_solicitacao.*
                                 -- TOTAL NESTE MAPA
                                 ,  ( SELECT  COALESCE(SUM(vl_total), 0.00)
                                       FROM  compras.mapa_item
                                      WHERE  mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                                        AND  mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                                        AND  mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                                        AND  mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                                        AND  mapa_item.exercicio             = mapa_solicitacao.exercicio
                                    ) as total_mapa

                                 -- TOTAL ANULADO DESTE MAPA
                                 ,  ( SELECT  COALESCE(SUM(vl_total), 0.00)
                                        FROM  compras.mapa_item_anulacao
                                       WHERE  mapa_item_anulacao.exercicio             = mapa_solicitacao.exercicio_solicitacao
                                         AND  mapa_item_anulacao.cod_entidade          = mapa_solicitacao.cod_entidade
                                         AND  mapa_item_anulacao.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                                         AND  mapa_item_anulacao.cod_mapa              = mapa_solicitacao.cod_mapa
                                         AND  mapa_item_anulacao.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                                    ) AS total_mapa_anulado
                              FROM  compras.mapa_solicitacao
                          ) as total
                      ON  mapa_solicitacao.exercicio_solicitacao = total.exercicio
                     AND  mapa_solicitacao.cod_entidade          = total.cod_entidade
                     AND  mapa_solicitacao.cod_solicitacao       = total.cod_solicitacao
                     AND  mapa_solicitacao.cod_mapa              = total.cod_mapa

               LEFT JOIN  (
                            SELECT  mapa_item_anulacao.exercicio_solicitacao
                                 ,  mapa_item_anulacao.cod_entidade
                                 ,  mapa_item_anulacao.cod_solicitacao
                                 ,  SUM(vl_total) as total_anulado

                              FROM  compras.mapa_item_anulacao

                          GROUP BY  exercicio_solicitacao
                                 ,  cod_entidade
                                 ,  cod_solicitacao
                          ) as anulacao
                      ON  solicitacao.cod_solicitacao = anulacao.cod_solicitacao
                     AND  solicitacao.exercicio       = anulacao.exercicio_solicitacao
                     AND  solicitacao.cod_entidade    = anulacao.cod_entidade

                   WHERE  1=1
                  -- AND  (total.total_mapa - total.total_mapa_anulado) > 0 \n";

        return $stSql;
    }

    public function recuperaMapaCotacaoValida(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapaCotacaoValida().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaCotacaoValida()
    {
        $stSql  = "     SELECT   mapa_cotacao.cod_mapa                                             \n";
        $stSql .= "            , mapa_cotacao.exercicio_mapa                                       \n";
        $stSql .= "            , mapa_cotacao.cod_cotacao                                          \n";
        $stSql .= "            , mapa_cotacao.exercicio_cotacao                                    \n";
        $stSql .= "       FROM   compras.mapa_cotacao                                              \n";
        $stSql .= " INNER JOIN   compras.cotacao                                                   \n";
        $stSql .= "         ON   cotacao.cod_cotacao = mapa_cotacao.cod_cotacao                    \n";
        $stSql .= "        AND   cotacao.exercicio = mapa_cotacao.exercicio_cotacao                \n";
        if ( $this->getDado( 'cod_mapa' ) && $this->getDado( 'exercicio_mapa' ) ) {
            $stSql .= " WHERE mapa_cotacao.cod_mapa = " . $this->getDado( 'cod_mapa' ) . " AND mapa_cotacao.exercicio_mapa = '" . $this->getDado( 'exercicio_mapa' ) . "' \n";
        }

        // Verifica se a cotação não está anulada (tabela compras.cotacao_anulada)
        $stSql .= " AND NOT EXISTS ( SELECT 1
                                       FROM compras.cotacao_anulada
                                      WHERE cotacao_anulada.cod_cotacao = mapa_cotacao.cod_cotacao
                                        AND cotacao_anulada.exercicio = mapa_cotacao.exercicio_cotacao
                                   ) ";

        return $stSql;
    }

    public function recuperaMapaLicitacaoProposta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapaLicitacaoProposta().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaLicitacaoProposta()
    {
       $stSql  = "    select mapa.cod_mapa                                                                                              \n";
       $stSql .= "         , mapa.exercicio                                                                                             \n";
       $stSql .= "         , mapa.cod_tipo_licitacao                                                                                    \n";
       //Pegar Edital que não está Anulado
       $stSql .= "         , case                                                                                         		    \n";
       $stSql .= "             when edital.num_edital is not null then                                                    		    \n";
       $stSql .= "                (Select edital.num_edital                                                    		    	    \n";
       $stSql .= "                FROM licitacao.edital                                                    		   		    \n";
       $stSql .= "                WHERE not exists( Select 1                                                    		   	    \n";
       $stSql .= "                   FROM licitacao.edital_anulado                                                    		    \n";
       $stSql .= "                   WHERE edital_anulado.num_edital = edital.num_edital                                                \n";
       $stSql .= "                   )                                                    		   				    \n";
       $stSql .= "                AND  edital.cod_licitacao = licitacao.cod_licitacao                                                   \n";
       $stSql .= "                AND edital.cod_modalidade = licitacao.cod_modalidade                                                  \n";
       $stSql .= "                AND edital.cod_entidade = licitacao.cod_entidade                                                      \n";
       $stSql .= "                AND edital.exercicio_licitacao = licitacao.exercicio)                                                 \n";
       $stSql .= "         	 end as num_edital                                                    		    	   		    \n";
       //Fim Pegar Edital que não está Anulado
       $stSql .= "         , edital.exercicio as exercicio_edital                                                                       \n";
       $stSql .= "         , licitacao.cod_licitacao                                                                                    \n";
       $stSql .= "         , licitacao.cod_entidade                                                                                     \n";
       $stSql .= "         , sw_cgm.nom_cgm as entidade                                                                                 \n";
       $stSql .= "         , licitacao.exercicio as exercicio_licitacao                                                                 \n";
       $stSql .= "         , modalidade.cod_modalidade                                                                                  \n";
       $stSql .= "         , modalidade.descricao                                                                                       \n";
       $stSql .= "         , case                                                                                                       \n";
       $stSql .= "             when licitacao.cod_objeto is not null then                                                               \n";
       $stSql .= "                 ( select descricao || ' ref. a Licitação' from compras.objeto where cod_objeto = licitacao.cod_objeto )::varchar     \n";
       $stSql .= "             else                                                                                                                     \n";
       $stSql .= "                 ( select descricao || ' ref. ao Mapa de Compras' from compras.objeto where cod_objeto = mapa.cod_objeto )::varchar   \n";
       $stSql .= "           end as descricao_objeto                                                                                                    \n";
       $stSql .= "      from compras.mapa                                                                                               \n";
       $stSql .= "            left join licitacao.licitacao                                                                             \n";
       $stSql .= "                   on licitacao.cod_mapa = mapa.cod_mapa                                                              \n";
       $stSql .= "                  and licitacao.exercicio_mapa = mapa.exercicio                                                       \n";
       $stSql .= "            left join compras.modalidade                                                                              \n";
       $stSql .= "                   on modalidade.cod_modalidade = licitacao.cod_modalidade                                            \n";
       $stSql .= "            left join licitacao.edital                                                                                \n";
       $stSql .= "                   on edital.cod_licitacao = licitacao.cod_licitacao                                                  \n";
       $stSql .= "                  and edital.cod_modalidade = licitacao.cod_modalidade                                                \n";
       $stSql .= "                  and edital.cod_entidade = licitacao.cod_entidade                                                    \n";
       $stSql .= "                  and edital.exercicio_licitacao = licitacao.exercicio                                                \n";
       $stSql .= "           inner join orcamento.entidade                                                                              \n";
       $stSql .= "                   on entidade.cod_entidade = licitacao.cod_entidade                                                  \n";
       $stSql .= "                  and entidade.exercicio = licitacao.exercicio                                                        \n";
       $stSql .= "           inner join sw_cgm                                                                                          \n";
       $stSql .= "                   on entidade.numcgm = sw_cgm.numcgm                                                                 \n";
       $stSql .= "    where                                                                                                             \n";

        if ( $this->getDado( 'cod_mapa' ) ) {
            $stSql .= "    mapa.cod_mapa = " . $this->getDado( 'cod_mapa' )  . "  and ";
        }
        if ( $this->getDado( 'exercicio' ) ) {
            $stSql .= "    mapa.exercicio = '" . $this->getDado( 'exercicio' )  . "'   and ";
        }

        $stSql.= "not exists( Select 1
                          from licitacao.licitacao_anulada
                         where licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                           and licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                           and licitacao_anulada.cod_entidade = licitacao.cod_entidade
                           and licitacao_anulada.exercicio = licitacao.exercicio
                        )   and ";

        $stSql = substr( $stSql, 0, strlen($stSql) - 4 );

        return $stSql;
    }

    public function recuperaDotacaoEdital(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDotacaoEdital().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTodos()
    {
        $stSql = "select mapa.exercicio
                       , mapa.cod_mapa
                       , mapa.cod_objeto
                       , mapa.timestamp
                       , to_char( mapa.timestamp, 'dd/mm/yyyy' ) as data
                       , mapa.cod_tipo_licitacao
                       , substring( objeto.descricao, 1, 60 ) as descricao
                       , (  select sum( vl_total )
                              from compras.mapa_item
                             where mapa.exercicio = mapa_item.exercicio
                               and mapa.cod_mapa  = mapa_item.cod_mapa )
                            - coalesce( ( select sum( vl_total ) from compras.mapa_item_anulacao
                                       where mapa_item_anulacao.cod_mapa = mapa.cod_mapa
                                         and mapa_item_anulacao.exercicio = mapa.exercicio
                                       ), 0 )
                             as valor_total
                   from compras.mapa
                   join compras.objeto
                     on ( mapa.cod_objeto = objeto.cod_objeto )
                    ";

        return $stSql;
    }

    public function montaRecuperaDotacaoEdital()
    {
        $stSql .= "select																			\n";
        $stSql .= "	 conta_despesa.cod_estrutural     												\n";
        $stSql .= "	,conta_despesa.descricao			  											\n";
        $stSql .= "	,pao.nom_pao																	\n";
        $stSql .= "from																				\n";
        $stSql .= "	 compras.solicitacao_item_dotacao												\n";
        $stSql .= "	,compras.mapa_item																\n";
        $stSql .= "	,compras.mapa																	\n";
        $stSql .= "	,orcamento.despesa																\n";
        $stSql .= "	,orcamento.conta_despesa														\n";
        $stSql .= "	,orcamento.pao																	\n";
        $stSql .= "where																			\n";
        $stSql .= "		mapa.cod_mapa  = mapa_item.cod_mapa											\n";
        $stSql .= "	and	mapa.exercicio = mapa_item.exercicio										\n";

        $stSql .= "	and mapa_item.exercicio_solicitacao = solicitacao_item_dotacao.exercicio		\n";
        $stSql .= "	and mapa_item.cod_entidade          = solicitacao_item_dotacao.cod_entidade		\n";
        $stSql .= "	and mapa_item.cod_solicitacao       = solicitacao_item_dotacao.cod_solicitacao	\n";
        $stSql .= "	and mapa_item.cod_centro            = solicitacao_item_dotacao.cod_centro		\n";
        $stSql .= "	and mapa_item.cod_item              = solicitacao_item_dotacao.cod_item			\n";

        $stSql .= "	and	solicitacao_item_dotacao.cod_despesa = despesa.cod_despesa					\n";
        $stSql .= "	and solicitacao_item_dotacao.exercicio   = despesa.exercicio					\n";

        $stSql .= "	and despesa.exercicio                     = conta_despesa.exercicio				\n";

        $stSql .= "	and despesa.cod_conta                    = conta_despesa.cod_conta				\n";

        $stSql .= "	and despesa.num_pao                      = pao.num_pao							\n";
        $stSql .= "	and despesa.exercicio					 = pao.exercicio						\n";

        $stSql .= " and mapa.cod_mapa = ". $this->getDado( 'cod_mapa')."                            \n";
        $stSql .= " and mapa.exercicio = '". $this->getDado( 'exercicio')."'                        \n";

        $stSql .= " group by                                                                        \n";
        $stSql .= "      conta_despesa.cod_estrutural                                               \n";
        $stSql .= "     ,conta_despesa.descricao                                                    \n";
        $stSql .= "     ,pao.nom_pao                                                                \n";

        return $stSql;
    }

    public function recuperaMapasAnulacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapasAnulacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    /* consulta mapas que podem ser anulados   */
    public function montaRecuperaMapasAnulacao()
    {
        $stSql  = "               select mapa.exercicio                                                                                                                    \n";
        $stSql .= "                    , mapa.cod_mapa                                                                                                                     \n";
        $stSql .= "                    , mapa.cod_objeto                                                                                                                   \n";
        $stSql .= "                    , mapa.timestamp                                                                                                                    \n";
        $stSql .= "                    , to_char( mapa.timestamp, 'dd/mm/yyyy' ) as data                                                                                   \n";
        $stSql .= "                    , mapa.cod_tipo_licitacao                                                                                                           \n";
        $stSql .= "                    , total_mapa.total_quantidade_mapa                                                                                                  \n";
        $stSql .= "                    , total_mapa.vl_total_mapa  - coalesce( anulacao.vl_total_anulacao, 0 ) as valor_total                                              \n";
        $stSql .= "                    , coalesce( anulacao.quantidade_total_anulacao, 0 ) as quantidade_total_anulacao                                                    \n";
        $stSql .= "                    , coalesce( anulacao.vl_total_anulacao, 0 ) as vl_total_anulacao                                                                    \n";
        $stSql .= "                    ,substring( objeto.descricao, 1, 60 ) as descricao                                                                                  \n";
        $stSql .= "              from compras.mapa                                                                                                                         \n";
        $stSql .= "              join ( select mapa_item.exercicio                                                                                                         \n";
        $stSql .= "                          , mapa_item.cod_mapa                                                                                                          \n";
        $stSql .= "                          , sum ( mapa_item.quantidade ) as total_quantidade_mapa                                                                       \n";
        $stSql .= "                          , sum ( mapa_item.vl_total   ) as vl_total_mapa                                                                               \n";
        $stSql .= "                       from compras.mapa_item                                                                                                           \n";
        $stSql .= "                     group by mapa_item.exercicio                                                                                                       \n";
        $stSql .= "                          , mapa_item.cod_mapa )  as total_mapa                                                                                         \n";
        $stSql .= "                on ( total_mapa.exercicio = mapa.exercicio                                                                                              \n";
        $stSql .= "               and   total_mapa.cod_mapa  = mapa.cod_mapa )                                                                                             \n";
        $stSql .= "              join compras.objeto                                                                                                                       \n";
        $stSql .= "                on ( mapa.cod_objeto = objeto.cod_objeto )                                                                                              \n";
        $stSql .= "              left join ( select mapa_solicitacao_anulacao.exercicio                                                                                    \n";
        $stSql .= "                               , mapa_solicitacao_anulacao.cod_mapa                                                                                     \n";
        $stSql .= "                               , coalesce( sum (mapa_item_anulacao.quantidade), '0' ) as quantidade_total_anulacao                                      \n";
        $stSql .= "                               , coalesce( sum (mapa_item_anulacao.vl_total  ), '0' ) as vl_total_anulacao                                              \n";
        $stSql .= "                            from compras.mapa_solicitacao_anulacao                                                                                      \n";
        $stSql .= "                            join compras.mapa_item_anulacao                                                                                             \n";
        $stSql .= "                              on ( mapa_item_anulacao.exercicio             = mapa_solicitacao_anulacao.exercicio                                       \n";
        $stSql .= "                             and   mapa_item_anulacao.cod_mapa              = mapa_solicitacao_anulacao.cod_mapa                                        \n";
        $stSql .= "                             and   mapa_item_anulacao.exercicio_solicitacao = mapa_solicitacao_anulacao.exercicio_solicitacao                           \n";
        $stSql .= "                             and   mapa_item_anulacao.cod_entidade          = mapa_solicitacao_anulacao.cod_entidade                                    \n";
        $stSql .= "                             and   mapa_item_anulacao.cod_solicitacao       = mapa_solicitacao_anulacao.cod_solicitacao                                 \n";
        $stSql .= "                             and   mapa_item_anulacao.timestamp             = mapa_solicitacao_anulacao.timestamp )                                     \n";
        $stSql .= "                          group by  mapa_solicitacao_anulacao.exercicio                                                                                 \n";
        $stSql .= "                               , mapa_solicitacao_anulacao.cod_mapa ) as anulacao                                                                       \n";
        $stSql .= "                  on ( anulacao.exercicio = mapa.exercicio                                                                                              \n";
        $stSql .= "                 and   anulacao.cod_mapa  = mapa.cod_mapa )                                                                                             \n";
        $stSql .= "              where total_mapa.total_quantidade_mapa > coalesce(anulacao.quantidade_total_anulacao, 0)                                                  \n";
        $stSql .= "                and (total_mapa.vl_total_mapa  - coalesce( anulacao.vl_total_anulacao, 0 )) > '0.00'                                                    \n";
        $stSql .= "                and ( not exists ( SELECT *                                                                                                             \n";
        $stSql .= "                                     FROM compras.compra_direta                                                                                         \n";
        $stSql .= "                                    WHERE compra_direta.exercicio_mapa = mapa.exercicio                                                                 \n";
        $stSql .= "                                      AND compra_direta.cod_mapa = mapa.cod_mapa                                                                        \n";
        $stSql .= "                                      AND not exists ( SELECT *                                                                                         \n";
        $stSql .= "                                                         FROM compras.compra_direta_anulacao                                                            \n";
        $stSql .= "                                                        WHERE compra_direta.cod_compra_direta  = compra_direta_anulacao.cod_compra_direta               \n";
        $stSql .= "                                                          AND compra_direta.cod_modalidade     = compra_direta_anulacao.cod_modalidade                  \n";
        $stSql .= "                                                          AND compra_direta.cod_entidade       = compra_direta_anulacao.cod_entidade                    \n";
        $stSql .= "                                                          AND compra_direta.exercicio_entidade = compra_direta_anulacao.exercicio_entidade ) )          \n";
        $stSql .= "                             and                                                                                                                        \n";
        $stSql .= "                      not exists ( SELECT *                                                                                                             \n";
        $stSql .= "                                     FROM licitacao.licitacao                                                                                           \n";
        $stSql .= "                                    WHERE licitacao.exercicio_mapa = mapa.exercicio                                                                     \n";
        $stSql .= "                                      AND licitacao.cod_mapa       = mapa.cod_mapa                                                                      \n";
        $stSql .= "                                      AND not exists ( SELECT *                                                                                         \n";
        $stSql .= "                                                         FROM licitacao.licitacao_anulada                                                               \n";
        $stSql .= "                                                        WHERE licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao                                \n";
        $stSql .= "                                                          AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade                               \n";
        $stSql .= "                                                          AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade                                 \n";
        $stSql .= "                                                          AND licitacao.exercicio      = licitacao_anulada.exercicio      ) ) )                         \n";
        $stSql .= "                and (     exists ( SELECT 1                                                                                                             \n";
        $stSql .= "                                     FROM compras.compra_direta                                                                                         \n";
        $stSql .= "                                    WHERE compra_direta.exercicio_mapa = mapa.exercicio                                                                 \n";
        $stSql .= "                                      AND compra_direta.cod_mapa       = mapa.cod_mapa )                                                                \n";
        $stSql .= "                              or                                                                                                                        \n";
        $stSql .= "                          exists (                                                                                                                      \n";
        $stSql .= "                                   SELECT 1                                                                                                             \n";
        $stSql .= "                                     FROM licitacao.licitacao                                                                                           \n";
        $stSql .= "                                    WHERE licitacao.exercicio_mapa = mapa.exercicio                                                                     \n";
        $stSql .= "                                      AND licitacao.cod_mapa       = mapa.cod_mapa )   )                                                                \n";
        $stSql .= "                and (     exists (                                                                                                                      \n";
        $stSql .= "                                   SELECT 1                                                                                                             \n";
        $stSql .= "                                     FROM compras.compra_direta                                                                                         \n";
        $stSql .= "                                          JOIN compras.compra_direta_anulacao                                                                           \n";
        $stSql .= "                                            ON compra_direta.cod_compra_direta  = compra_direta_anulacao.cod_compra_direta                              \n";
        $stSql .= "                                           AND compra_direta.cod_modalidade     = compra_direta_anulacao.cod_modalidade                                 \n";
        $stSql .= "                                           AND compra_direta.cod_entidade       = compra_direta_anulacao.cod_entidade                                   \n";
        $stSql .= "                                           AND compra_direta.exercicio_entidade = compra_direta_anulacao.exercicio_entidade                             \n";
        $stSql .= "                                    WHERE compra_direta.exercicio_mapa = mapa.exercicio                                                                 \n";
        $stSql .= "                                      AND compra_direta.cod_mapa       = mapa.cod_mapa )                                                                \n";
        $stSql .= "                              or                                                                                                                        \n";
        $stSql .= "                          exists (                                                                                                                      \n";
        $stSql .= "                                   SELECT 1                                                                                                             \n";
        $stSql .= "                                   FROM licitacao.licitacao                                                                                             \n";
        $stSql .= "                                        JOIN licitacao.licitacao_anulada                                                                                \n";
        $stSql .= "                                          ON licitacao.cod_licitacao  = licitacao_anulada.cod_licitacao                                                 \n";
        $stSql .= "                                         AND licitacao.cod_modalidade = licitacao_anulada.cod_modalidade                                                \n";
        $stSql .= "                                         AND licitacao.cod_entidade   = licitacao_anulada.cod_entidade                                                  \n";
        $stSql .= "                                         AND licitacao.exercicio      = licitacao_anulada.exercicio                                                     \n";
        $stSql .= "                                  WHERE licitacao.exercicio_mapa = mapa.exercicio                                                                       \n";
        $stSql .= "                                    AND licitacao.cod_mapa       = mapa.cod_mapa ) )                                                                    \n";
        $stSql .= " and ( not exists (                                                                                                                                     \n";
        $stSql .= "                    SELECT 1                                                                                                                            \n";
        $stSql .= "                      FROM                                                                                                                              \n";
        $stSql .= "                           compras.compra_direta as ccd                                                                                                 \n";
        $stSql .= "                           JOIN compras.mapa as cm                                                                                                      \n";
        $stSql .= "                             ON ccd.exercicio_mapa    = cm.exercicio                                                                                    \n";
        $stSql .= "                            AND ccd.cod_mapa          = cm.cod_mapa                                                                                     \n";
        $stSql .= "                           JOIN compras.mapa_cotacao as cmc                                                                                             \n";
        $stSql .= "                             ON cmc.exercicio_cotacao = cm.exercicio                                                                                    \n";
        $stSql .= "                            AND cmc.cod_mapa          = cm.cod_mapa                                                                                     \n";
        $stSql .= "                           JOIN compras.cotacao as c                                                                                                    \n";
        $stSql .= "                             ON c.exercicio           = cmc.exercicio_cotacao                                                                           \n";
        $stSql .= "                            AND c.cod_cotacao         = cmc.cod_cotacao                                                                                 \n";
        $stSql .= "                           JOIN empenho.item_pre_empenho_julgamento as eipej                                                                            \n";
        $stSql .= "                             ON eipej.exercicio       = c.exercicio                                                                                     \n";
        $stSql .= "                            AND eipej.cod_cotacao     = c.cod_cotacao                                                                                   \n";
        $stSql .= "                           JOIN empenho.item_pre_empenho as eipe                                                                                        \n";
        $stSql .= "                             ON eipe.cod_pre_empenho  = eipej.cod_pre_empenho                                                                           \n";
        $stSql .= "                            AND eipe.exercicio        = eipej.exercicio                                                                                 \n";
        $stSql .= "                            AND eipe.num_item         = eipej.num_item                                                                                  \n";
        $stSql .= "                           JOIN empenho.pre_empenho as epe                                                                                              \n";
        $stSql .= "                             ON epe.cod_pre_empenho   = eipe.cod_pre_empenho                                                                            \n";
        $stSql .= "                            AND epe.exercicio         = eipe.exercicio                                                                                  \n";
        $stSql .= "                           JOIN empenho.autorizacao_empenho as eae                                                                                      \n";
        $stSql .= "                             ON eae.exercicio         = epe.exercicio                                                                                   \n";
        $stSql .= "                            AND eae.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
        $stSql .= "                     WHERE ccd.cod_mapa       = mapa.cod_mapa                                                                                           \n";
        $stSql .= "                       AND ccd.exercicio_mapa = mapa.exercicio      )                                                                                   \n";
        $stSql .= "               or                                                                                                                                       \n";
        $stSql .= "           exists (                                                                                                                                     \n";
        $stSql .= "                    SELECT 1                                                                                                                            \n";
        $stSql .= "                      FROM                                                                                                                              \n";
        $stSql .= "                           compras.compra_direta as ccd                                                                                                 \n";
        $stSql .= "                           JOIN compras.mapa as cm                                                                                                      \n";
        $stSql .= "                             ON ccd.exercicio_mapa    = cm.exercicio                                                                                    \n";
        $stSql .= "                            AND ccd.cod_mapa          = cm.cod_mapa                                                                                     \n";
        $stSql .= "                           JOIN compras.mapa_cotacao as cmc                                                                                             \n";
        $stSql .= "                             ON cmc.exercicio_cotacao = cm.exercicio                                                                                    \n";
        $stSql .= "                            AND cmc.cod_mapa          = cm.cod_mapa                                                                                     \n";
        $stSql .= "                           JOIN compras.cotacao as c                                                                                                    \n";
        $stSql .= "                             ON c.exercicio           = cmc.exercicio_cotacao                                                                           \n";
        $stSql .= "                            AND c.cod_cotacao         = cmc.cod_cotacao                                                                                 \n";
        $stSql .= "                           JOIN empenho.item_pre_empenho_julgamento as eipej                                                                            \n";
        $stSql .= "                             ON eipej.exercicio       = c.exercicio                                                                                     \n";
        $stSql .= "                            AND eipej.cod_cotacao     = c.cod_cotacao                                                                                   \n";
        $stSql .= "                           JOIN empenho.item_pre_empenho as eipe                                                                                        \n";
        $stSql .= "                             ON eipe.cod_pre_empenho  = eipej.cod_pre_empenho                                                                           \n";
        $stSql .= "                            AND eipe.exercicio        = eipej.exercicio                                                                                 \n";
        $stSql .= "                            AND eipe.num_item         = eipej.num_item                                                                                  \n";
        $stSql .= "                           JOIN empenho.pre_empenho as epe                                                                                              \n";
        $stSql .= "                             ON epe.cod_pre_empenho   = eipe.cod_pre_empenho                                                                            \n";
        $stSql .= "                            AND epe.exercicio         = eipe.exercicio                                                                                  \n";
        $stSql .= "                           JOIN empenho.autorizacao_empenho as eae                                                                                      \n";
        $stSql .= "                             ON eae.exercicio         = epe.exercicio                                                                                   \n";
        $stSql .= "                            AND eae.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
        $stSql .= "                           JOIN empenho.autorizacao_anulada as eaa                                                                                      \n";
        $stSql .= "                             ON eaa.exercicio         = eae.exercicio                                                                                   \n";
        $stSql .= "                            AND eaa.cod_entidade      = eae.cod_entidade                                                                                \n";
        $stSql .= "                            AND eaa.cod_autorizacao   = eae.cod_autorizacao                                                                             \n";
        $stSql .= "                     WHERE ccd.cod_mapa       = mapa.cod_mapa                                                                                           \n";
        $stSql .= "                       AND ccd.exercicio_mapa = mapa.exercicio       ) )                                                                                \n";

        return $stSql;
    }

    public function recuperaMapaLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapaLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaLicitacao()
    {
        $stSql = "
                  select licitacao.exercicio
                       , licitacao.cod_mapa
                    from licitacao.licitacao
                    join licitacao.cotacao_licitacao
                      on ( licitacao.cod_licitacao  = cotacao_licitacao.cod_licitacao
                     and   licitacao.cod_modalidade = cotacao_licitacao.cod_modalidade
                     and   licitacao.cod_entidade   = cotacao_licitacao.cod_entidade
                     and   licitacao.exercicio      = cotacao_licitacao.exercicio_licitacao)
                    join licitacao.adjudicacao
                      on ( cotacao_licitacao.cod_licitacao       = adjudicacao.cod_licitacao
                     and   cotacao_licitacao.cod_modalidade      = adjudicacao.cod_modalidade
                     and   cotacao_licitacao.cod_entidade        = adjudicacao.cod_entidade
                     and   cotacao_licitacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                     and   cotacao_licitacao.lote                = adjudicacao.lote
                     and   cotacao_licitacao.cod_cotacao         = adjudicacao.cod_cotacao
                     and   cotacao_licitacao.cod_item            = adjudicacao.cod_item
                     and   cotacao_licitacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                     and   cotacao_licitacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor)
                  where not exists ( select *
                                       from licitacao.adjudicacao_anulada
                                      where adjudicacao.num_adjudicacao       = adjudicacao_anulada.num_adjudicacao
                                        and adjudicacao.cod_entidade          = adjudicacao_anulada.cod_entidade
                                        and adjudicacao.cod_modalidade        = adjudicacao_anulada.cod_modalidade
                                        and adjudicacao.cod_licitacao         = adjudicacao_anulada.cod_licitacao
                                        and adjudicacao.exercicio_licitacao   = adjudicacao_anulada.exercicio_licitacao
                                        and adjudicacao.cod_item              = adjudicacao_anulada.cod_item
                                        and adjudicacao.cod_cotacao           = adjudicacao_anulada.cod_cotacao
                                        and adjudicacao.lote                  = adjudicacao_anulada.lote
                                        and adjudicacao.exercicio_cotacao     = adjudicacao_anulada.exercicio_cotacao
                                        and adjudicacao.cgm_fornecedor        = adjudicacao_anulada.cgm_fornecedor )
                    ";

        return $stSql;
    }

    /*
        recupera mapas não anulados e que ainda não entraram em processo licitatorio ou que entraram em processo licitario que foi anulado

    */

    public function recuperaMapaProcessoLicitatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if ($stOrdem) {
            $stOrdem = " ORDER by $stOrdem ";
        }
        $stGroupBy = "
                GROUP BY mapa.exercicio
                       , mapa.cod_mapa
                       , mapa.cod_objeto
                       , mapa.timestamp
                       , mapa.cod_tipo_licitacao \n";

        $stSql = $this->montaRecuperaMapaProcessoLicitatorio().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaProcessoLicitatorio()
    {
        $stSql = "  SELECT mapa.exercicio
                         , mapa.cod_mapa
                         , mapa.cod_objeto
                         , mapa.timestamp
                         , to_char( mapa.timestamp, 'dd/mm/yyyy' ) as data
                         , mapa.cod_tipo_licitacao
                      FROM compras.mapa

                 LEFT JOIN compras.mapa_cotacao
                        ON mapa_cotacao.cod_mapa  = mapa.cod_mapa
                       AND mapa_cotacao.exercicio_mapa = mapa.exercicio

                 LEFT JOIN empenho.item_pre_empenho_julgamento
                        ON item_pre_empenho_julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND item_pre_empenho_julgamento.exercicio   = mapa_cotacao.exercicio_cotacao

                     WHERE 1=1

                    -- Teste para não listar mapas que já tiveram autorização de empenho realizada,
                    -- mesmo que a autorização tenha sido cancelada.
                       AND item_pre_empenho_julgamento.cod_cotacao IS NULL
                    
                    ---- este sub select server pra verificar se existem itens não anulados para cada mapa
                       AND EXISTS(SELECT mapa_item.exercicio
                                       , mapa_item.cod_entidade
                                       , mapa_item.cod_solicitacao
                                       , mapa_item.cod_mapa
                                       , mapa_item.exercicio_solicitacao
                                       , mapa_item.cod_item
                                    FROM compras.mapa_item

                               LEFT JOIN (SELECT mapa_item_anulacao.exercicio
                                               , mapa_item_anulacao.cod_entidade
                                               , mapa_item_anulacao.cod_solicitacao
                                               , mapa_item_anulacao.cod_mapa
                                               , mapa_item_anulacao.cod_centro
                                               , mapa_item_anulacao.cod_item
                                               , mapa_item_anulacao.exercicio_solicitacao
                                               , mapa_item_anulacao.lote
                                               , SUM( mapa_item_anulacao.quantidade ) as quantidade
                                               , SUM( mapa_item_anulacao.vl_total )  as vl_total
                                            FROM compras.mapa_item_anulacao
                                        GROUP BY mapa_item_anulacao.exercicio
                                               , mapa_item_anulacao.cod_entidade
                                               , mapa_item_anulacao.cod_solicitacao
                                               , mapa_item_anulacao.cod_mapa
                                               , mapa_item_anulacao.cod_centro
                                               , mapa_item_anulacao.cod_item
                                               , mapa_item_anulacao.exercicio_solicitacao
                                               , mapa_item_anulacao.lote
                                         ) as anulacao
                                      ON mapa_item.exercicio             = anulacao.exercicio
                                     AND mapa_item.cod_entidade          = anulacao.cod_entidade
                                     AND mapa_item.cod_solicitacao       = anulacao.cod_solicitacao
                                     AND mapa_item.cod_mapa              = anulacao.cod_mapa
                                     AND mapa_item.cod_centro            = anulacao.cod_centro
                                     AND mapa_item.cod_item              = anulacao.cod_item
                                     AND mapa_item.exercicio_solicitacao = anulacao.exercicio_solicitacao
                                     AND mapa_item.lote                  = anulacao.lote

                                   WHERE mapa_item.quantidade > coalesce( anulacao.quantidade, 0 )
                                     AND mapa_item.vl_total   > coalesce( anulacao.vl_total,   0 )
                                     AND mapa_item.cod_mapa   = mapa.cod_mapa
                                     AND mapa_item.exercicio  = mapa.exercicio
                                 )

                    --- verificando se o mapa já foi usado em outro processo (licitacao)
                       AND NOT EXISTS (SELECT licitacao.exercicio_mapa
                                            , licitacao.cod_mapa
                                         FROM licitacao.licitacao
                                        WHERE NOT EXISTS (SELECT 1
                                                            FROM licitacao.licitacao_anulada
                                                           WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                                             AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                                             AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                                             AND licitacao_anulada.exercicio      = licitacao.exercicio
                                                         )
                                          AND licitacao.exercicio_mapa = mapa.exercicio
                                          AND licitacao.cod_mapa       = mapa.cod_mapa
                                      )

                       AND NOT EXISTS (SELECT 1
                                         FROM compras.compra_direta
                                        WHERE NOT EXISTS (SELECT 1
                                                            FROM compras.compra_direta_anulacao
                                                           WHERE compra_direta_anulacao.cod_modalidade     = compra_direta.cod_modalidade
                                                             AND compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                                             AND compra_direta_anulacao.cod_entidade       = compra_direta.cod_entidade
                                                             AND compra_direta_anulacao.cod_compra_direta  = compra_direta.cod_compra_direta
                                                         )
                                          AND compra_direta.cod_mapa       = mapa.cod_mapa
                                          AND compra_direta.exercicio_mapa = mapa.exercicio
                                      )
                    --- verificando se todos os itens do mapa tem reserva de saldos
                       AND NOT EXISTS (SELECT 1
                                         FROM compras.mapa_item
                                        WHERE NOT EXISTS (SELECT 1
                                                            FROM compras.mapa_item_reserva
                                                           WHERE mapa_item_reserva.exercicio_mapa        = mapa_item.exercicio
                                                             AND mapa_item_reserva.cod_mapa              = mapa_item.cod_mapa
                                                             AND mapa_item_reserva.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                                                             AND mapa_item_reserva.cod_entidade          = mapa_item.cod_entidade
                                                             AND mapa_item_reserva.cod_solicitacao       = mapa_item.cod_solicitacao
                                                             AND mapa_item_reserva.cod_centro            = mapa_item.cod_centro
                                                             AND mapa_item_reserva.cod_item              = mapa_item.cod_item
                                                             AND mapa_item_reserva.lote                  = mapa_item.lote
                                                         )
                                          --Se a Reserva for do tipo Rigida, é Obrigatório ter Reserva de Saldo
                                          AND (SELECT valor
                                                 FROM administracao.configuracao AS AC
                                                WHERE AC.cod_modulo = 35
                                                  AND AC.parametro = 'reserva_rigida'
                                                  AND AC.exercicio = mapa.exercicio
                                              ) = 'true'
                                          AND mapa_item.exercicio = mapa.exercicio
                                          AND mapa_item.cod_mapa  = mapa.cod_mapa
                                      )
        ";

        return $stSql;
    }

    public function recuperaMapaObjeto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if ( ($this->getDado( 'cod_mapa' )) and ( $this->getDado( 'exercicio')  ) ) {
            $stSql = $this->montaRecuperaMapaObjeto().$stFiltro.$stOrdem;
            $this->stDebug = $stSql;
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        }

        return $obErro;
    }

    public function montaRecuperaMapaObjeto()
    {
        $stSql = "
                    select mapa.exercicio
                         , mapa.cod_mapa
                         , mapa.cod_objeto
                         , mapa.timestamp
                         , mapa.cod_tipo_licitacao
                         , objeto.descricao
                         ,  tipo_licitacao.descricao as tipo_licitacao
                    from compras.mapa
                    join compras.objeto
                      on ( mapa.cod_objeto = objeto.cod_objeto )
                    join compras.tipo_licitacao
                      on ( mapa.cod_tipo_licitacao = tipo_licitacao.cod_tipo_licitacao )
                    where mapa.cod_mapa = " . $this->getDado( 'cod_mapa' ) ."
                      and mapa.exercicio = '" . $this->getDado( 'exercicio' ) . "'";

        return $stSql;
    }

    /**
      *
      *  @author Diogo Zarpelon
      *
      */

    public function recuperaMapaItemPermissaoExclusaoReserva(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaMapaItemPermissaoExclusaoReserva().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaMapaItemPermissaoExclusaoReserva()
    {
        $stSql = "
                SELECT  mapa_solicitacao.exercicio,
                        mapa_solicitacao.cod_mapa,
                        mapa_solicitacao.cod_solicitacao,
                        mapa_solicitacao.exercicio_solicitacao,
                        mapa_solicitacao.cod_entidade,
                        mapa_item.cod_centro,
                        mapa_item.cod_item,
                        mapa_item.lote,
                        mapa_item_dotacao.cod_despesa,
                        mapa_item_dotacao.cod_conta,
                        mapa_item.quantidade as qtde_item,
                        mapa_item_dotacao.quantidade as qtde_item_dotacao,
                        mapa_item_anulacao.quantidade as qtde_anulacao,
                        (COALESCE(mapa_item_dotacao.quantidade,0) - COALESCE(mapa_item_anulacao.quantidade,0))::int AS saldo

                  FROM  compras.mapa
                     ,  compras.mapa_solicitacao
                     ,  compras.mapa_item

             LEFT JOIN  compras.mapa_item_dotacao
                    ON  mapa_item_dotacao.exercicio              = mapa_item.exercicio
                   AND  mapa_item_dotacao.cod_mapa               = mapa_item.cod_mapa
                   AND  mapa_item_dotacao.exercicio_solicitacao  = mapa_item.exercicio_solicitacao
                   AND  mapa_item_dotacao.cod_entidade           = mapa_item.cod_entidade
                   AND  mapa_item_dotacao.cod_solicitacao        = mapa_item.cod_solicitacao
                   AND  mapa_item_dotacao.cod_centro             = mapa_item.cod_centro
                   AND  mapa_item_dotacao.cod_item               = mapa_item.cod_item
                   AND  mapa_item_dotacao.lote                   = mapa_item.lote

             LEFT JOIN  compras.mapa_item_anulacao
                    ON  mapa_item_anulacao.exercicio             = mapa_item_dotacao.exercicio
                   AND  mapa_item_anulacao.cod_entidade          = mapa_item_dotacao.cod_entidade
                   AND  mapa_item_anulacao.cod_solicitacao       = mapa_item_dotacao.cod_solicitacao
                   AND  mapa_item_anulacao.cod_mapa              = mapa_item_dotacao.cod_mapa
                   AND  mapa_item_anulacao.cod_centro            = mapa_item_dotacao.cod_centro
                   AND  mapa_item_anulacao.cod_item              = mapa_item_dotacao.cod_item
                   AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item_dotacao.exercicio_solicitacao
                   AND  mapa_item_anulacao.lote                  = mapa_item_dotacao.lote
                   AND  mapa_item_anulacao.cod_despesa           = mapa_item_dotacao.cod_despesa
                   AND  mapa_item_anulacao.cod_conta             = mapa_item_dotacao.cod_conta

                 WHERE  mapa.cod_mapa                            = mapa_solicitacao.cod_mapa
                   AND  mapa.exercicio                           = mapa_solicitacao.exercicio
                   AND  mapa_solicitacao.exercicio               = mapa_item.exercicio
                   AND  mapa_solicitacao.cod_entidade            = mapa_item.cod_entidade
                   AND  mapa_solicitacao.cod_solicitacao         = mapa_item.cod_solicitacao
                   AND  mapa_solicitacao.cod_mapa                = mapa_item.cod_mapa
                   AND  mapa_solicitacao.exercicio_solicitacao   = mapa_item.exercicio_solicitacao ";

        return $stSql;
    }

    public function verificaMapaAnulacoes(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaVerificaMapaAnulacoes().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaVerificaMapaAnulacoes()
    {
        $stSql  = "   select mapa.exercicio                                                                                                     \n";
        $stSql .= "        , mapa.cod_mapa                                                                                                      \n";
        $stSql .= "        , mapa.cod_objeto                                                                                                    \n";
        $stSql .= "        , mapa.timestamp                                                                                                     \n";
        $stSql .= "        , to_char( mapa.timestamp, 'dd/mm/yyyy' ) as data                                                                    \n";
        $stSql .= "        , mapa.cod_tipo_licitacao                                                                                            \n";
        $stSql .= "        , total_mapa.total_quantidade_mapa                                                                                   \n";
        $stSql .= "        , total_mapa.vl_total_mapa  - coalesce( anulacao.vl_total_anulacao, 0 ) as valor_total                               \n";
        $stSql .= "        , coalesce( anulacao.quantidade_total_anulacao, 0 ) as quantidade_total_anulacao                                     \n";
        $stSql .= "        , coalesce( anulacao.vl_total_anulacao, 0 ) as vl_total_anulacao                                                     \n";
        $stSql .= "        ,substring( objeto.descricao, 1, 60 ) as descricao                                                                   \n";
        $stSql .= "     from compras.mapa                                                                                                       \n";
        $stSql .= "          join ( select mapa_item.exercicio                                                                                  \n";
        $stSql .= "                      , mapa_item.cod_mapa                                                                                   \n";
        $stSql .= "                      , sum ( mapa_item.quantidade ) as total_quantidade_mapa                                                \n";
        $stSql .= "                      , sum ( mapa_item.vl_total   ) as vl_total_mapa                                                        \n";
        $stSql .= "                   from compras.mapa_item                                                                                    \n";
        $stSql .= "                 group by mapa_item.exercicio                                                                                \n";
        $stSql .= "                      , mapa_item.cod_mapa )  as total_mapa                                                                  \n";
        $stSql .= "            on ( total_mapa.exercicio = mapa.exercicio                                                                       \n";
        $stSql .= "           and   total_mapa.cod_mapa  = mapa.cod_mapa )                                                                      \n";
        $stSql .= "          join compras.objeto                                                                                                \n";
        $stSql .= "            on ( mapa.cod_objeto = objeto.cod_objeto )                                                                       \n";
        $stSql .= "          left join ( select mapa_solicitacao_anulacao.exercicio                                                             \n";
        $stSql .= "                           , mapa_solicitacao_anulacao.cod_mapa                                                              \n";
        $stSql .= "                           , coalesce( sum (mapa_item_anulacao.quantidade), '0' ) as quantidade_total_anulacao               \n";
        $stSql .= "                           , coalesce( sum (mapa_item_anulacao.vl_total  ), '0' ) as vl_total_anulacao                       \n";
        $stSql .= "                        from compras.mapa_solicitacao_anulacao                                                               \n";
        $stSql .= "                        join compras.mapa_item_anulacao                                                                      \n";
        $stSql .= "                          on ( mapa_item_anulacao.exercicio             = mapa_solicitacao_anulacao.exercicio                \n";
        $stSql .= "                         and   mapa_item_anulacao.cod_mapa              = mapa_solicitacao_anulacao.cod_mapa                 \n";
        $stSql .= "                         and   mapa_item_anulacao.exercicio_solicitacao = mapa_solicitacao_anulacao.exercicio_solicitacao    \n";
        $stSql .= "                         and   mapa_item_anulacao.cod_entidade          = mapa_solicitacao_anulacao.cod_entidade             \n";
        $stSql .= "                         and   mapa_item_anulacao.cod_solicitacao       = mapa_solicitacao_anulacao.cod_solicitacao          \n";
        $stSql .= "                         and   mapa_item_anulacao.timestamp             = mapa_solicitacao_anulacao.timestamp )              \n";
        $stSql .= "                      group by  mapa_solicitacao_anulacao.exercicio                                                          \n";
        $stSql .= "                           , mapa_solicitacao_anulacao.cod_mapa ) as anulacao                                                \n";
        $stSql .= "            on ( anulacao.exercicio = mapa.exercicio                                                                         \n";
        $stSql .= "           and   anulacao.cod_mapa  = mapa.cod_mapa )                                                                        \n";
        $stSql .= "     where                                                                                                                   \n";
        $stSql .= "           (total_mapa.vl_total_mapa  - coalesce( anulacao.vl_total_anulacao, 0 )) = '0.00'                                  \n";
        $stSql .= "       AND mapa.exercicio = '".$this->getDado('exercicio')."'                                                                \n";
        $stSql .= "       AND mapa.cod_mapa  =  ".$this->getDado('cod_mapa')."                                                                  \n";

        return $stSql;
    }

    /*
        recupera mapas não anulados e que ainda não entraram em processo licitatorio ou que entraram em processo licitario que foi anulado
        recupera mapas sem resarva de saldo
    */
    public function recuperaMapaSemReservaProcessoLicitatorio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if ($stOrdem) {
            $stOrdem = " ORDER by $stOrdem ";
        }
        $stGroupBy = "
                GROUP BY mapa.exercicio
                       , mapa.cod_mapa
                       , mapa.cod_objeto
                       , mapa.timestamp
                       , mapa.cod_tipo_licitacao \n";

        $stSql = $this->montaRecuperaMapaSemReservaProcessoLicitatorio().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperaMapaSemReservaProcessoLicitatorio()
    {
        $stSql = "  SELECT mapa.exercicio
                         , mapa.cod_mapa
                         , mapa.cod_objeto
                         , mapa.timestamp
                         , to_char( mapa.timestamp, 'dd/mm/yyyy' ) as data
                         , mapa.cod_tipo_licitacao
                      FROM compras.mapa
                 LEFT JOIN compras.mapa_cotacao
                        ON mapa_cotacao.cod_mapa  = mapa.cod_mapa
                       AND mapa_cotacao.exercicio_mapa = mapa.exercicio
                 LEFT JOIN empenho.item_pre_empenho_julgamento
                        ON item_pre_empenho_julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
                       AND item_pre_empenho_julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                     WHERE 1=1
                    -- Teste para não listar mapas que já tiveram autorização de empenho realizada,
                    -- mesmo que a autorização tenha sido cancelada.
                       AND item_pre_empenho_julgamento.cod_cotacao IS NULL
                    ---- este sub select server pra verificar se existem itens não anulados para cada mapa
                       AND EXISTS(SELECT mapa_item.exercicio
                                       , mapa_item.cod_entidade
                                       , mapa_item.cod_solicitacao
                                       , mapa_item.cod_mapa
                                       , mapa_item.exercicio_solicitacao
                                       , mapa_item.cod_item
                                    FROM compras.mapa_item
                               LEFT JOIN (SELECT mapa_item_anulacao.exercicio
                                               , mapa_item_anulacao.cod_entidade
                                               , mapa_item_anulacao.cod_solicitacao
                                               , mapa_item_anulacao.cod_mapa
                                               , mapa_item_anulacao.cod_centro
                                               , mapa_item_anulacao.cod_item
                                               , mapa_item_anulacao.exercicio_solicitacao
                                               , mapa_item_anulacao.lote
                                               , SUM( mapa_item_anulacao.quantidade ) as quantidade
                                               , SUM( mapa_item_anulacao.vl_total )  as vl_total
                                            FROM compras.mapa_item_anulacao
                                        GROUP BY mapa_item_anulacao.exercicio
                                               , mapa_item_anulacao.cod_entidade
                                               , mapa_item_anulacao.cod_solicitacao
                                               , mapa_item_anulacao.cod_mapa
                                               , mapa_item_anulacao.cod_centro
                                               , mapa_item_anulacao.cod_item
                                               , mapa_item_anulacao.exercicio_solicitacao
                                               , mapa_item_anulacao.lote
                                         ) as anulacao
                                      ON mapa_item.exercicio             = anulacao.exercicio
                                     AND mapa_item.cod_entidade          = anulacao.cod_entidade
                                     AND mapa_item.cod_solicitacao       = anulacao.cod_solicitacao
                                     AND mapa_item.cod_mapa              = anulacao.cod_mapa
                                     AND mapa_item.cod_centro            = anulacao.cod_centro
                                     AND mapa_item.cod_item              = anulacao.cod_item
                                     AND mapa_item.exercicio_solicitacao = anulacao.exercicio_solicitacao
                                     AND mapa_item.lote                  = anulacao.lote
                                   WHERE mapa_item.quantidade > coalesce( anulacao.quantidade, 0 )
                                     AND mapa_item.vl_total   > coalesce( anulacao.vl_total,   0 )
                                     AND mapa_item.cod_mapa   = mapa.cod_mapa
                                     AND mapa_item.exercicio  = mapa.exercicio)
                    --- verificando se o mapa já foi usado em outro processo (licitacao)
                       AND NOT EXISTS (SELECT licitacao.exercicio_mapa
                                            , licitacao.cod_mapa
                                         FROM licitacao.licitacao
                                        WHERE NOT EXISTS (SELECT 1
                                                            FROM licitacao.licitacao_anulada
                                                           WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                                             AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                                             AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                                             AND licitacao_anulada.exercicio      = licitacao.exercicio
                                                         )
                                          AND licitacao.exercicio_mapa = mapa.exercicio
                                          AND licitacao.cod_mapa       = mapa.cod_mapa)
                       AND NOT EXISTS (SELECT 1
                                         FROM compras.compra_direta
                                        WHERE NOT EXISTS (SELECT 1
                                                            FROM compras.compra_direta_anulacao
                                                           WHERE compra_direta_anulacao.cod_modalidade     = compra_direta.cod_modalidade
                                                             AND compra_direta_anulacao.exercicio_entidade = compra_direta.exercicio_entidade
                                                             AND compra_direta_anulacao.cod_entidade       = compra_direta.cod_entidade
                                                             AND compra_direta_anulacao.cod_compra_direta  = compra_direta.cod_compra_direta
                                                         )
                                          AND compra_direta.cod_mapa       = mapa.cod_mapa
                                          AND compra_direta.exercicio_mapa = mapa.exercicio)
        ";

        return $stSql;
    }

    public function recuperaTipoMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if ($stOrdem) {
            $stOrdem = " ORDER by $stOrdem ";
        }
        $stGroupBy = "
                GROUP BY mapa.exercicio
                       , mapa.cod_mapa
                       , mapa.cod_objeto
                       , mapa.timestamp
                       , mapa.cod_tipo_licitacao
                       , solicitacao.registro_precos
        ";

        $stSql = $this->montaRecuperaTipoMapa().$stFiltro.$stGroupBy.$stOrdem;
        $this->stDebug = $stSql;        
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    private function montaRecuperaTipoMapa()
    {
        $stSql = "
              SELECT mapa.exercicio
                   , mapa.cod_mapa
                   , mapa.cod_objeto
                   , mapa.timestamp
                   , mapa.cod_tipo_licitacao
                   , solicitacao.registro_precos
                FROM compras.mapa
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
          INNER JOIN compras.solicitacao_homologada
                  ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                 AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                 AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
          INNER JOIN compras.solicitacao
                  ON solicitacao.exercicio       = solicitacao_homologada.exercicio
                 AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                 AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
               WHERE mapa.cod_mapa = ".$this->getDado('cod_mapa')."
                 AND mapa.exercicio = '".$this->getDado('exercicio')."'
        ";
        return $stSql;
    }

    public function __destruct() {}   
}
