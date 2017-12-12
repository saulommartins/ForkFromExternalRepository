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
    * Classe de mapeamento da tabela ORCAMENTO.SUPLEMENTACAO
    * Data de Criação: 13/07/2004

    * @author Analista: Dieine
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TOrcamentoSuplementacao.class.php 66090 2016-07-19 14:25:30Z michel $

    * Casos de uso: uc-02.01.24
                    uc-02.01.07
                    uc-02.01.25
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TOrcamentoSuplementacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('orcamento.suplementacao');

    $this->setCampoCod('cod_suplementacao');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('cod_suplementacao','integer' ,true ,''   ,true ,true );
    $this->AddCampo('exercicio'        ,'char'    ,true ,'04' ,true ,true );
    $this->AddCampo('cod_norma'        ,'integer' ,true ,''   ,false,true );
    $this->AddCampo('cod_tipo'         ,'integer' ,true ,''   ,false,true );
    $this->AddCampo('dt_suplementacao' ,'date'    ,true ,''   ,false,false);
    $this->AddCampo('motivo'           ,'text'    ,false,''   ,false,false);
}

/**
    * Método para montar SQL para recuperar relacionamento entre suplementacao/lancamento/tipo_transferencia
    * @access Private
    * @return String $stSql
*/
function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT OS.exercicio                                                         \n";
    $stSql .= "      ,OS.cod_suplementacao                                                 \n";
    $stSql .= "      ,OS.cod_tipo                                                          \n";
    $stSql .= "      ,CTT.nom_tipo                                                         \n";
    $stSql .= "      ,OS.cod_norma                                                         \n";
    $stSql .= "      ,OS.motivo                                                            \n";
    $stSql .= "      ,CTD.cod_entidade                                                     \n";
    $stSql .= "      ,coalesce( max(OSS.cod_despesa), max(OSR.cod_despesa) ) AS cod_despea \n";
    $stSql .= "      ,TO_CHAR( OS.dt_suplementacao, 'dd/mm/yyyy' ) AS dt_suplementacao     \n";
    $stSql .= "      ,coalesce( OSS.valor, 0.00 ) AS vl_suplementado                       \n";
    $stSql .= "      ,coalesce( OSR.valor, 0.00 ) AS vl_reduzido                           \n";
    $stSql .= "FROM orcamento.suplementacao          AS OS                             \n";
    $stSql .= "LEFT JOIN ( SELECT OSS.exercicio                                            \n";
    $stSql .= "                  ,OSS.cod_suplementacao                                    \n";
    $stSql .= "                  ,MAX( OSS.cod_despesa ) as cod_despesa                    \n";
    $stSql .= "                  ,sum( OSS.valor ) as valor                                \n";
    $stSql .= "            FROM orcamento.suplementacao_suplementada AS OSS            \n";
    $stSql .= "            GROUP BY OSS.exercicio                                          \n";
    $stSql .= "                    ,OSS.cod_suplementacao                                  \n";
    $stSql .= "            ORDER BY OSS.exercicio                                          \n";
    $stSql .= "                    ,OSS.cod_suplementacao                                  \n";
    $stSql .= ") AS OSS ON( OS.exercicio         = OSS.exercicio                           \n";
    $stSql .= "         AND OS.cod_suplementacao = OSS.cod_suplementacao )                 \n";
    $stSql .= "LEFT JOIN ( SELECT OSR.exercicio                                            \n";
    $stSql .= "                  ,OSR.cod_suplementacao                                    \n";
    $stSql .= "                  ,MAX( OSR.cod_despesa ) as cod_despesa                    \n";
    $stSql .= "                  ,sum( OSR.valor ) AS valor                                \n";
    $stSql .= "            FROM orcamento.suplementacao_reducao AS OSR                 \n";
    $stSql .= "            GROUP BY OSR.exercicio                                          \n";
    $stSql .= "                    ,OSR.cod_suplementacao                                  \n";
    $stSql .= "            ORDER BY OSR.exercicio                                          \n";
    $stSql .= "                    ,OSR.cod_suplementacao                                  \n";
    $stSql .= ") AS OSR ON( OS.exercicio         = OSR.exercicio                           \n";
    $stSql .= "         AND OS.cod_suplementacao = OSR.cod_suplementacao )                 \n";
    $stSql .= "LEFT JOIN orcamento.suplementacao_anulada AS OSA                        \n";
    $stSql .= "ON(  OS.cod_suplementacao = OSA.cod_suplementacao_anulacao                  \n";
    $stSql .= "AND  OS.exercicio         = OSA.exercicio                 )                 \n";
    $stSql .= "    ,contabilidade.tipo_transferencia     AS CTT                        \n";
    $stSql .= "    ,contabilidade.transferencia_despesa  AS CTD                        \n";
    $stSql .= "WHERE OS.cod_tipo          = CTT.cod_tipo                                   \n";
    $stSql .= "AND   OS.exercicio         = CTT.exercicio                                  \n";
    $stSql .= "AND   OS.cod_tipo          = CTD.cod_tipo                                   \n";
    $stSql .= "AND   OS.exercicio         = CTD.exercicio                                  \n";
    $stSql .= "AND   OS.cod_suplementacao = CTD.cod_suplementacao                          \n";
    $stSql .= "AND   CTD.tipo             = 'S'                                            \n";
    $stSql .= "AND   OSA.cod_suplementacao is null                                         \n";
    $stSql .= "AND   OS.cod_tipo         != 16                                             \n";
    $stSql .=  $this->getDado('stFiltro'). "                                               \n";
    $stSql .= "GROUP BY OS.exercicio                                                       \n";
    $stSql .= "        ,OS.cod_suplementacao                                               \n";
    $stSql .= "        ,OS.cod_tipo                                                        \n";
    $stSql .= "        ,CTT.nom_tipo                                                       \n";
    $stSql .= "        ,OS.cod_norma                                                       \n";
    $stSql .= "        ,OS.motivo                                                          \n";
    $stSql .= "        ,CTD.cod_entidade                                                   \n";
    $stSql .= "        ,OS.dt_suplementacao                                                \n";
    $stSql .= "        ,OSS.valor                                                          \n";
    $stSql .= "        ,OSR.valor                                                          \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaRelacionamentoRecurso(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $stOrdem )
        $stOrdem = ( strpos( 'ORDER BY', $stOrdem ) ) ? $stOrdem : ' ORDER BY '.$stOrdem;

    $stSql = $this->montaRecuperaRelacionamentoRecurso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Método para montar SQL para recuperar relacionamento entre suplementacao/lancamento/tipo_transferencia
    * @access Private
    * @return String $stSql
*/
function montaRecuperaRelacionamentoRecurso()
{
    if(Sessao::getExercicio()>=2013){
        $stSql  = "SELECT OS.exercicio
                         ,OS.cod_suplementacao
                         ,OS.cod_tipo
                         ,CTT.nom_tipo
                         ,OS.cod_norma
                         ,OS.motivo
                         ,CASE WHEN OSS.cod_entidade IS NOT NULL THEN
                               OSS.cod_entidade
                          ELSE
                               OSR.cod_entidade
                          END as cod_entidade
                         ,TO_CHAR( OS.dt_suplementacao, 'dd/mm/yyyy' ) AS dt_suplementacao
                         ,orcamento.fn_totaliza_suplementacao( OS.exercicio, OS.cod_suplementacao ) AS vl_suplementado  
                         ,coalesce( OSR.valor, 0.00 ) AS vl_reduzido
                   FROM orcamento.suplementacao          AS OS
                   LEFT JOIN ( SELECT OSS.exercicio
                                     ,OSS.cod_suplementacao
                                     ,MAX( OSS.cod_despesa ) as cod_despesa
                                     ,MAX( RECURSO.cod_recurso ) as cod_recurso
                                     ,sum( OSS.valor ) as valor
                                     ,OD.cod_entidade
                               FROM orcamento.suplementacao_suplementada AS OSS
                                   ,orcamento.despesa                    AS OD
                                   ,orcamento.recurso('".$this->getDado('stExercicio')."')  AS RECURSO
                               WHERE
                                       OSS.cod_despesa = OD.cod_despesa
                                   AND OSS.exercicio   = OD.exercicio
                                   AND OD.cod_recurso  = RECURSO.cod_recurso
                                   AND OD.exercicio    = RECURSO.exercicio \n";
        if($this->getDado('inCodDetalhamento'))
            $stSql .= "            AND RECURSO.cod_detalhamento = ".$this->getDado('inCodDetalhamento')."         \n";
        if($this->getDado('stDestinacaoRecurso'))
            $stSql .= "            AND RECURSO.masc_recurso_red like '".$this->getDado('stDestinacaoRecurso')."%' \n";
        if($this->getDado('inCodDespesa'))
            $stSql .= "            AND OSS.cod_despesa = ".$this->getDado('inCodDespesa')."                       \n";
        $stSql .= "            GROUP BY OSS.exercicio
                                       ,OSS.cod_suplementacao
                                       ,RECURSO.cod_recurso
                                       ,OD.cod_entidade
                               ORDER BY OSS.exercicio
                                       ,OSS.cod_suplementacao
                                       ,RECURSO.cod_recurso
                             ) AS OSS
                          ON OS.exercicio         = OSS.exercicio
                         AND OS.cod_suplementacao = OSS.cod_suplementacao
                   LEFT JOIN ( SELECT OSR.exercicio
                                     ,OSR.cod_suplementacao
                                     ,MAX( OSR.cod_despesa ) as cod_despesa
                                     ,( select sum( suplementacao_reducao.valor )
                                          from orcamento.suplementacao_reducao
                                         where suplementacao_reducao.exercicio = OSR.exercicio
                                           and suplementacao_reducao.cod_suplementacao = OSR.cod_suplementacao
                                      ) AS valor
                                     ,OD.cod_entidade
                               FROM orcamento.suplementacao_reducao AS OSR
                         INNER JOIN orcamento.despesa                    AS OD
                                 ON OSR.cod_despesa = OD.cod_despesa
                                AND OSR.exercicio   = OD.exercicio
                              WHERE OSR.exercicio = '".$this->getDado('stExercicio')."' \n";
        if($this->getDado('inCodDespesa'))
            $stSql .= "         AND OSR.cod_despesa = ".$this->getDado('inCodDespesa')." \n";
        $stSql .= "            GROUP BY OSR.exercicio
                                       ,OSR.cod_suplementacao
                                       ,OD.cod_entidade
                               ORDER BY OSR.exercicio
                                       ,OSR.cod_suplementacao
                             ) AS OSR
                          ON OS.exercicio         = OSR.exercicio
                         AND OS.cod_suplementacao = OSR.cod_suplementacao
                   LEFT JOIN orcamento.suplementacao_anulada AS OSA
                          ON (      OS.cod_suplementacao = OSA.cod_suplementacao_anulacao
                                OR OSR.cod_suplementacao = OSA.cod_suplementacao
                             )
                         AND OS.exercicio         = OSA.exercicio
                   LEFT JOIN contabilidade.tipo_transferencia     AS CTT
                          ON OS.cod_tipo          = CTT.cod_tipo
                         AND OS.exercicio         = CTT.exercicio
                   LEFT JOIN contabilidade.transferencia_despesa  AS CTD
                          ON OS.cod_tipo          = CTD.cod_tipo
                         AND OS.exercicio         = CTD.exercicio
                         AND OS.cod_suplementacao = CTD.cod_suplementacao
                       WHERE OS.exercicio = '".$this->getDado('stExercicio')."' \n";
        if($this->getDado('inCodDespesa'))
            $stSql .= "  AND ( OSS.cod_despesa = ".$this->getDado('inCodDespesa')." OR OSR.cod_despesa = ".$this->getDado('inCodDespesa')." ) \n";
        $stSql .= $this->getDado('stFiltro');
        $stSql .= "GROUP BY OS.exercicio
                           ,OS.cod_suplementacao
                           ,OS.cod_tipo
                           ,CTT.nom_tipo
                           ,OS.cod_norma
                           ,OS.motivo
                           ,OS.dt_suplementacao
                           ,OSR.cod_entidade
                           ,OSS.cod_entidade
                           ,OSR.valor \n";
    }else{
        $stSql  = "SELECT OS.exercicio
                         ,OS.cod_suplementacao
                         ,OS.cod_tipo
                         ,CTT.nom_tipo
                         ,OS.cod_norma
                         ,OS.motivo
                         ,CTD.cod_entidade
                         ,TO_CHAR( OS.dt_suplementacao, 'dd/mm/yyyy' ) AS dt_suplementacao
                         ,orcamento.fn_totaliza_suplementacao( OS.exercicio, OS.cod_suplementacao ) AS vl_suplementado  
                         ,coalesce( OSR.valor, 0.00 ) AS vl_reduzido
                     FROM orcamento.suplementacao          AS OS
                   LEFT JOIN ( SELECT OSS.exercicio
                                     ,OSS.cod_suplementacao
                                     ,MAX( OSS.cod_despesa ) as cod_despesa
                                     ,MAX( RECURSO.cod_recurso ) as cod_recurso
                                     ,( select sum( suplementacao_reducao.valor )
                                          from orcamento.suplementacao_reducao
                                         where suplementacao_reducao.exercicio = OSR.exercicio
                                           and suplementacao_reducao.cod_suplementacao = OSR.cod_suplementacao
                                      ) AS valor
                               FROM orcamento.suplementacao_suplementada AS OSS
                                   ,orcamento.despesa                    AS OD
                                   ,orcamento.recurso('".$this->getDado('stExercicio')."')  AS RECURSO
                               WHERE
                                       OSS.cod_despesa = OD.cod_despesa
                                   AND OSS.exercicio   = OD.exercicio
                                   AND OD.cod_recurso  = RECURSO.cod_recurso
                                   AND OD.exercicio    = RECURSO.exercicio \n";
        if($this->getDado('inCodDetalhamento'))
            $stSql .= "            AND RECURSO.cod_detalhamento = ".$this->getDado('inCodDetalhamento')."         \n";
        if($this->getDado('stDestinacaoRecurso'))
            $stSql .= "            AND RECURSO.masc_recurso_red like '".$this->getDado('stDestinacaoRecurso')."%' \n";
        if($this->getDado('inCodDespesa'))
            $stSql .= "            AND OSS.cod_despesa = ".$this->getDado('inCodDespesa')."                       \n";
        $stSql .= "            GROUP BY OSS.exercicio
                                       ,OSS.cod_suplementacao
                                       ,RECURSO.cod_recurso
                               ORDER BY OSS.exercicio
                                       ,OSS.cod_suplementacao
                                       ,RECURSO.cod_recurso
                             ) AS OSS
                          ON OS.exercicio         = OSS.exercicio
                         AND OS.cod_suplementacao = OSS.cod_suplementacao
                   LEFT JOIN ( SELECT OSR.exercicio
                                     ,OSR.cod_suplementacao
                                     ,MAX( OSR.cod_despesa ) as cod_despesa
                                     ,sum( OSR.valor ) AS valor
                               FROM orcamento.suplementacao_reducao AS OSR
                              WHERE OSR.exercicio = '".$this->getDado('stExercicio')."' \n";
        if($this->getDado('inCodDespesa'))
            $stSql .= "         AND OSR.cod_despesa = ".$this->getDado('inCodDespesa')." \n";
        $stSql .= "            GROUP BY OSR.exercicio
                                       ,OSR.cod_suplementacao
                               ORDER BY OSR.exercicio
                                       ,OSR.cod_suplementacao
                             ) AS OSR
                          ON OS.exercicio         = OSR.exercicio
                         AND OS.cod_suplementacao = OSR.cod_suplementacao
                   LEFT JOIN orcamento.suplementacao_anulada AS OSA
                          ON (      OS.cod_suplementacao = OSA.cod_suplementacao_anulacao
                                OR OSR.cod_suplementacao = OSA.cod_suplementacao
                             )
                         AND OS.exercicio = OSA.exercicio
                       ,contabilidade.tipo_transferencia     AS CTT
                       ,contabilidade.transferencia_despesa  AS CTD
                   WHERE OS.cod_tipo          = CTT.cod_tipo
                   AND   OS.exercicio         = CTT.exercicio
                   AND   OS.cod_tipo          = CTD.cod_tipo
                   AND   OS.exercicio         = CTD.exercicio
                   AND   OS.cod_suplementacao = CTD.cod_suplementacao
                   AND   CTD.tipo             = 'S'
                   AND   OS.exercicio = '".$this->getDado('stExercicio')."' \n";
        if($this->getDado('inCodDespesa'))
            $stSql .= "  AND ( OSS.cod_despesa = ".$this->getDado('inCodDespesa')." OR OSR.cod_despesa = ".$this->getDado('inCodDespesa')." ) \n";
        $stSql .= $this->getDado('stFiltro');
        $stSql .= "GROUP BY OS.exercicio
                           ,OS.cod_suplementacao
                           ,OS.cod_tipo
                           ,CTT.nom_tipo
                           ,OS.cod_norma
                           ,OS.motivo
                           ,OS.dt_suplementacao
                           ,CTD.cod_entidade
                           ,OSR.valor \n";
    }

    return $stSql;
}
/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaConsultaSuplementacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaConsultaSuplementacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Método para montar SQL para recuperar dados de consulta da Suplementacao
    * @access Private
    * @return String $stSQL
**/
function montaRecuperaConsultaSuplementacao()
{
    $stSQL = "SELECT
                    S.cod_suplementacao,
                    S.exercicio,
                    S.cod_norma,
                    S.cod_tipo,
                    TO_CHAR(S.dt_suplementacao,'dd/mm/yyyy') AS dt_suplementacao,
                    TO_CHAR(SE.dt_suplementacao,'dd/mm/yyyy') AS dt_anulacao,
                    S.motivo,
                    orcamento.fn_totaliza_suplementacao( S.exercicio, S.cod_suplementacao ) AS vl_suplementacao
                FROM
                    orcamento.suplementacao S
                    LEFT JOIN orcamento.suplementacao_anulada SA ON
                    ( S.exercicio = SA.exercicio AND S.cod_suplementacao = SA.cod_suplementacao  )
                    LEFT JOIN orcamento.suplementacao SE ON
                    ( SE.exercicio = SA.exercicio AND SA.cod_suplementacao_anulacao = SE.cod_suplementacao )
                WHERE
                    S.cod_suplementacao IS NOT NULL \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaVlSuplementacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaVlSuplementacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Método para montar SQL para recuperar saldo suplementado e reduzido de uma dotação
    * @access Private
    * @return String $stSQL
**/
function montaRecuperaVlSuplementacao()
{
    $stSQL  = "SELECT sum( SS.valor ) as vl_suplementado                    \n";
    $stSQL .= "      ,sum( SR.valor ) as vl_reduzido                        \n";
    $stSQL .= "FROM ".$this->getTabela()." AS S                             \n";
    $stSQL .= "LEFT JOIN                                                    \n";
    $stSQL .= "  orcamento.suplementacao_suplementada AS SS             \n";
    $stSQL .= "ON(     SS.exercicio         = S.exercicio                   \n";
    $stSQL .= "    AND SS.cod_suplementacao = S.cod_suplementacao           \n";
    $stSQL .= "    AND SS.cod_despesa = ".$this->getDado("cod_despesa")." ) \n";
    $stSQL .= "LEFT JOIN                                                    \n";
    $stSQL .= "  orcamento.suplementacao_reducao AS SR                  \n";
    $stSQL .= "ON(     SR.exercicio         = S.exercicio                   \n";
    $stSQL .= "    AND SR.cod_suplementacao = S.cod_suplementacao           \n";
    $stSQL .= "    AND SR.cod_despesa = ".$this->getDado("cod_despesa")." ) \n";

    return $stSQL;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaSuplementacaoDespesa(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $stCondicao )
        $this->setDado( 'stFiltro', $stCondicao );
    $stSql = $this->montaRecuperaSuplementacaoDespesa();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Método para montar o sql para recuperar as suplementações e suas despesas
    * @access Private
    * @return String $stSql
*/
function montaRecuperaSuplementacaoDespesa()
{
    $stSql .= "SELECT tbl.exercicio                                          \n";
    $stSql .= "      ,tbl.cod_suplementacao                                  \n";
    $stSql .= "      ,tbl.cod_despesa                                        \n";
    $stSql .= "      ,OCD.cod_estrutural                                     \n";
    $stSql .= "      ,sum( tbl.vl_suplementado ) AS vl_suplementado          \n";
    $stSql .= "      ,sum( tbl.vl_reducao ) AS vl_reducao                    \n";
    $stSql .= "      ,empenho.fn_saldo_dotacao( tbl.exercicio, tbl.cod_despesa ) as saldo_dotacao  \n";
    $stSql .= "      ,( empenho.fn_saldo_dotacao( tbl.exercicio, tbl.cod_despesa ) - sum(tbl.vl_suplementado) + sum(tbl.vl_reducao) ) as saldo_posterior \n";
    $stSql .= "FROM(                                                         \n";
    $stSql .= "      SELECT OS.exercicio                                     \n";
    $stSql .= "            ,OS.cod_suplementacao                             \n";
    $stSql .= "            ,OSS.cod_despesa                                  \n";
    $stSql .= "            ,OSS.valor as vl_suplementado                     \n";
    $stSql .= "            ,0.00 as vl_reducao                               \n";
    $stSql .= "      FROM orcamento.suplementacao AS OS                      \n";
    $stSql .= "      LEFT JOIN orcamento.suplementacao_suplementada AS OSS   \n";
    $stSql .= "      ON( OSS.exercicio = OS.exercicio                        \n";
    $stSql .= "      AND OSS.cod_suplementacao = OS.cod_suplementacao )      \n";
    $stSql .= $this->getDado('stFiltro')." \n";
    $stSql .= "                                                              \n";
    $stSql .= "      UNION                                                   \n";
    $stSql .= "                                                              \n";
    $stSql .= "      SELECT OS.exercicio                                     \n";
    $stSql .= "            ,OS.cod_suplementacao                             \n";
    $stSql .= "            ,OSR.cod_despesa                                  \n";
    $stSql .= "            ,0.00 as vl_suplementado                          \n";
    $stSql .= "            ,OSR.valor as vl_reducao                          \n";
    $stSql .= "      FROM orcamento.suplementacao AS OS                      \n";
    $stSql .= "      LEFT JOIN orcamento.suplementacao_reducao AS OSR        \n";
    $stSql .= "      ON( OSR.exercicio = OS.exercicio                        \n";
    $stSql .= "      AND OSR.cod_suplementacao = OS.cod_suplementacao )      \n";
    $stSql .= $this->getDado('stFiltro')." \n";
    $stSql .= ") as tbl                                                      \n";
    $stSql .= ",orcamento.despesa AS OD                                      \n";
    $stSql .= ",orcamento.conta_despesa AS OCD                               \n";
    $stSql .= "WHERE tbl.cod_despesa = OD.cod_despesa                        \n";
    $stSql .= "AND   tbl.exercicio   = OD.exercicio                          \n";
    $stSql .= "AND   OD.exercicio    = OCD.exercicio                         \n";
    $stSql .= "AND   OD.cod_conta    = OCD.cod_conta                         \n";
    $stSql .= "GROUP BY tbl.exercicio                                        \n";
    $stSql .= "        ,tbl.cod_suplementacao                                \n";
    $stSql .= "        ,tbl.cod_despesa                                      \n";
    $stSql .= "        ,OCD.cod_estrutural                                   \n";
    $stSql .= "ORDER BY tbl.exercicio                                        \n";
    $stSql .= "        ,tbl.cod_suplementacao                                \n";
    $stSql .= "        ,tbl.cod_despesa                                      \n";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de ordenação do SQL (ORDER)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaHistorico(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $stOrdem )
        $stOrdem ( strpos( 'ORDER BY', $stOrdem ) ) ? $stOrdem : ' ORDER BY '.$stOrdem;
    $stSql = $this->montaRecuperaHistorico().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Método para montar SQL para função recuperaRecurso
    * @access Private
    * @return String $stSql
*/
function montaRecuperaHistorico()
{
    $stSql  = "SELECT OS.exercicio                                      \n";
    $stSql .= "      ,OS.cod_suplementacao                              \n";
    $stSql .= "      ,OS.cod_tipo                                       \n";
    $stSql .= "      ,CTD.cod_entidade                                  \n";
    $stSql .= "      ,CL.cod_historico                                  \n";
    $stSql .= "FROM orcamento.suplementacao                AS OS    \n";
    $stSql .= "    ,contabilidade.transferencia_despesa    AS CTD   \n";
    $stSql .= "    ,contabilidade.lancamento_transferencia AS CLT   \n";
    $stSql .= "    ,contabilidade.lancamento               AS CL    \n";
    $stSql .= "WHERE OS.cod_suplementacao = CTD.cod_suplementacao       \n";
    $stSql .= "  AND OS.exercicio         = CTD.exercicio               \n";
    $stSql .= "  AND CTD.tipo             = 'S'                         \n";
    $stSql .= "  AND CTD.cod_lote         = CLT.cod_lote                \n";
    $stSql .= "  AND CTD.tipo             = CLT.tipo                    \n";
    $stSql .= "  AND CTD.sequencia        = CLT.sequencia               \n";
    $stSql .= "  AND CTD.exercicio        = CLT.exercicio               \n";
    $stSql .= "  AND CTD.cod_tipo         = CLT.cod_tipo                \n";
    $stSql .= "  AND CTD.cod_entidade     = CLT.cod_entidade            \n";
    $stSql .= "  AND CLT.sequencia        = CL.sequencia                \n";
    $stSql .= "  AND CLT.cod_lote         = CL.cod_lote                 \n";
    $stSql .= "  AND CLT.tipo             = CL.tipo                     \n";
    $stSql .= "  AND CLT.exercicio        = CL.exercicio                \n";
    $stSql .= "  ANd CLT.cod_entidade     = CL.cod_entidade             \n";

    return $stSql;
}

function recuperaRelatorioSuplementacoes(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $stOrdem )
        $stOrdem ( strpos( 'ORDER BY', $stOrdem ) ) ? $stOrdem : ' ORDER BY '.$stOrdem;
    $stSql = $this->montaRecuperaRelatorioSuplementacoes().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelatorioSuplementacoes()
{
  switch ($this->getDado("relatorio")) {
    CASE 'entidade':
    $stSql  = " SELECT * FROM (                                                                                           \n";
    $stSql .= "    SELECT                                                                                                 \n";
    $stSql .= "         ode.cod_entidade		            as entidade,                                              \n";
    $stSql .= "         TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') as data,                                               \n";
    $stSql .= "         nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   as fundamentacao,                         \n";
    $stSql .= "         ctt.nom_tipo                        as tipo_suplementacao,                                        \n";
    $stSql .= "         sum(coalesce(oss.valor,0.00)) as valor,           						      \n";
    $stSql .= "         CASE                                                                                              \n";
    $stSql .= "            WHEN osa.cod_suplementacao_anulacao  is not null THEN 'Anulada'                                \n";
    $stSql .= "            ELSE 'Válida'                                                                                  \n";
    $stSql .= "         END as situacao,                                                                                  \n";
    $stSql .= "         LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                            \n";
    $stSql .= "     FROM                                                                                                  \n";
    $stSql .= "         orcamento.suplementacao                     as osu                                                \n";
    $stSql .= "         LEFT JOIN orcamento.suplementacao_anulada   as osa on(                                            \n";
    $stSql .= "             osu.exercicio = osa.exercicio                                                                 \n";
    $stSql .= "         AND osu.cod_suplementacao = osa.cod_suplementacao                                                 \n";
    $stSql .= "         ),                                                                                                \n";
    $stSql .= "         orcamento.suplementacao_suplementada       as oss,                                                \n";
    $stSql .= "         orcamento.despesa   				        as ode,                               \n";
    $stSql .= "         contabilidade.tipo_transferencia           as ctt,                                                \n";
    $stSql .= "         normas.norma                                       as nor                                         \n";
    $stSql .= "     WHERE                                                                	                              \n";
    $stSql .= "         ctt.cod_tipo        	= osu.cod_tipo                                                        \n";
    $stSql .= "     AND ctt.exercicio       	= osu.exercicio                                                       \n";
    $stSql .= "     AND ctt.cod_tipo           <> 16                                                                      \n";
    $stSql .= "                                                                                                           \n";
    $stSql .= "     AND osu.cod_suplementacao	= oss.cod_suplementacao                                               \n";
    $stSql .= "     AND osu.exercicio       	= oss.exercicio                                                       \n";
    $stSql .= "                                                                                                           \n";
    $stSql .= "     AND oss.cod_despesa			= ode.cod_despesa			                      \n";
    $stSql .= "     AND oss.exercicio       	= ode.exercicio                                                       \n";
    $stSql .= "                                                                                                           \n";
    $stSql .= "     AND osu.cod_norma       	= nor.cod_norma                                                       \n";
    $stSql .= "     AND osu.exercicio           = '". $this->getDado('exercicio')."'                                      \n";
    $stSql .= "                                                                                                           \n";
    if ($this->getDado("cod_norma")) {
        $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	  \n";
    }
    if ($this->getDado("dt_inicial")) {	$stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                  \n";
    }
    if ($this->getDado("dt_final")) {
        $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                    \n";
    }
    if ($this->getDado("cod_despesa")) {
        $stSql .= " AND ( (osu.cod_suplementacao IN (                                                                               \n";
        $stSql .= " SELECT cod_suplementacao FROM orcamento.suplementacao_suplementada                                              \n";
        $stSql .= " WHERE cod_despesa = ".$this->getDado("cod_despesa")." and exercicio = '".$this->getDado("exercicio")."'))       \n";
        $stSql .= " OR (osu.cod_suplementacao IN (                                                                                  \n";
        $stSql .= " SELECT cod_suplementacao FROM orcamento.suplementacao_reducao                                                   \n";
        $stSql .= " WHERE cod_despesa = ".$this->getDado("cod_despesa")." and exercicio = '".$this->getDado("exercicio")."')))      \n";
    }
    if ($this->getDado("cod_tipo")) {
        $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                                              \n";
    }
    $stSql .= "     AND ode.cod_entidade   in(".$this->getDado("cod_entidade").")                                         \n";
    $stSql .= "     GROUP BY                                                                                              \n";
    $stSql .= "         ode.cod_entidade,                                                                          		  \n";
    $stSql .= "         osu.dt_suplementacao,                                                                             \n";
    $stSql .= "         nor.num_norma,                                                                                    \n";
    $stSql .= "         nor.exercicio,                                                                                    \n";
    $stSql .= "         ctt.nom_tipo,                                                                                     \n";
    $stSql .= "         osa.cod_suplementacao_anulacao,                                                                   \n";
    $stSql .= "         nor.nom_norma,                                                                                     \n";
    $stSql .= "         ode.cod_recurso                                                                                   \n";
    $stSql .= " ) AS tabela                                                                                               \n";
    if ($this->getDado("situacao")) {
        $stSql .= " WHERE                                                                                                     \n";
        $stSql .= "     tabela.situacao = '".$this->getDado("situacao")."'                                                    \n";
    }
    $stSql .= " ORDER BY                                                                                                  \n";
    $stSql .= "     tabela.entidade,tabela.situacao,to_date(tabela.data,'dd/mm/yyyy')                                     \n";
    $stSql .= " ; 																										  \n";
    break;

    CASE 'lei_decreto':
        $stSql  = " SELECT                                                                      \n";
        $stSql .= "          tabela.data,                                                       \n";
        $stSql .= "          tabela.fundamentacao,                                                \n";
        $stSql .= "          tabela.cod_norma,                                                  \n";
        $stSql .= "          tabela.tipo_suplementacao,                                         \n";
        $stSql .= "          tabela.cod_tipo,                                                   \n";
        $stSql .= "          sum(coalesce(tabela.valor,0.00)) as valor,                         \n";
        $stSql .= "          tabela.situacao,                                                   \n";
        $stSql .= "          tabela.cod_entidade                                                \n";
        $stSql .= " FROM (                                                                      \n";
        $stSql .= "     SELECT                                                                  \n";
        $stSql .= "          TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') as data,                \n";
        $stSql .= "          nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   as fundamentacao,   \n";
        $stSql .= "          nor.cod_norma,                                                     \n";
        $stSql .= "          ctt.nom_tipo                        as tipo_suplementacao,         \n";
        $stSql .= "          ctt.cod_tipo,                                                      \n";
        $stSql .= "          oss.valor,                                                         \n";
        $stSql .= "          ode.cod_entidade,                                                  \n";
        $stSql .= "          CASE                                                               \n";
        $stSql .= "             WHEN osa.cod_suplementacao  is not null THEN 'Anulada'          \n";
        $stSql .= "             ELSE 'Válida'                                                   \n";
        $stSql .= "          END as situacao                                                    \n";
        $stSql .= "      FROM                                                                   \n";
        $stSql .= "          orcamento.suplementacao                   as osu              \n";
        $stSql .= "          LEFT JOIN orcamento.suplementacao_anulada  as osa on(          \n";
        $stSql .= "              osu.exercicio          = osa.exercicio                         \n";
        $stSql .= "          AND osu.cod_suplementacao  = osa.cod_suplementacao                 \n";
        $stSql .= "          ),                                                                 \n";
        $stSql .= "          orcamento.suplementacao_suplementada      as oss,             \n";
        $stSql .= "          orcamento.despesa                          as ode,             \n";
        $stSql .= "          contabilidade.transferencia_despesa       AS ctd,              \n";
        $stSql .= "          contabilidade.tipo_transferencia           as ctt,             \n";
        $stSql .= "          normas.norma                                      as nor              \n";
        $stSql .= "      WHERE                                                                  \n";
        $stSql .= "          ctt.cod_tipo           = osu.cod_tipo                              \n";
        $stSql .= "      AND ctt.exercicio          = osu.exercicio                             \n";
        $stSql .= "      AND ctt.cod_tipo          <> 16                                        \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osu.cod_suplementacao  = oss.cod_suplementacao                     \n";
        $stSql .= "      AND osu.exercicio          = oss.exercicio                             \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND oss.cod_despesa        = ode.cod_despesa                           \n";
        $stSql .= "      AND oss.exercicio          = ode.exercicio                             \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osu.cod_tipo          = CTD.cod_tipo                               \n";
        $stSql .= "      AND osu.cod_suplementacao = CTD.cod_suplementacao                      \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osu.cod_norma          = nor.cod_norma                             \n";
        $stSql .= "      AND osu.exercicio          = '". $this->getDado('exercicio')."'        \n";
    if ($this->getDado("cod_norma")) {
        $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	  \n";
    }
    if ($this->getDado("dt_inicial")) {
        $stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                  \n";
    }
    if ($this->getDado("dt_final")) {
        $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                    \n";
    }
    if ($this->getDado("cod_despesa")) {
        $stSql .= " AND ( (osu.cod_suplementacao IN (                                                                               \n";
        $stSql .= " SELECT cod_suplementacao FROM orcamento.suplementacao_suplementada                                              \n";
        $stSql .= " WHERE cod_despesa = ".$this->getDado("cod_despesa")." and exercicio = '".$this->getDado("exercicio")."'))       \n";
        $stSql .= " OR (osu.cod_suplementacao IN (                                                                                  \n";
        $stSql .= " SELECT cod_suplementacao FROM orcamento.suplementacao_reducao                                                   \n";
        $stSql .= " WHERE cod_despesa = ".$this->getDado("cod_despesa")." and exercicio = '".$this->getDado("exercicio")."')))      \n";
    }
    if ($this->getDado("cod_tipo")) {
        $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                \n";
    }
        $stSql .= "     AND ctd.cod_entidade   in(".$this->getDado("cod_entidade").")           \n";
        $stSql .= "  ) AS tabela                                                                \n";
    if ($this->getDado("situacao")) {
        $stSql .= " WHERE                                                                       \n";
        $stSql .= "     tabela.situacao = '".$this->getDado("situacao")."'                      \n";
    }
        $stSql .= "  GROUP BY                                                                   \n";
        $stSql .= "      tabela.data,                                                           \n";
        $stSql .= "      tabela.fundamentacao,                                                    \n";
        $stSql .= "      tabela.cod_norma,                                                      \n";
        $stSql .= "      tabela.tipo_suplementacao,                                             \n";
        $stSql .= "      tabela.cod_tipo,                                                       \n";
        $stSql .= "      tabela.situacao,                                                       \n";
        $stSql .= "      tabela.cod_entidade                                                    \n";
        $stSql .= "  ORDER BY                                                                   \n";
        $stSql .= "      tabela.fundamentacao,                                                    \n";
        $stSql .= "      to_date(tabela.data,'dd/mm/yyyy')                                      \n";
        $stSql .= " ; 																			\n";

    break;

    CASE 'data':
        $stSql  = " SELECT                                                                                                                                 \n";
        $stSql .= "     *                                                                                                                                 \n";
        $stSql .= " FROM (                                                                                                                                 \n";
        $stSql .= "     SELECT                                                                                                                             \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                            \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa || ' - ' || publico.fn_mascara_dinamica( (                                                                  \n";
        $stSql .= "         SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '". $this->getDado('exercicio')."' ), tabela.dotacao ) AS dotacao,      \n";
        $stSql .= "         sum(coalesce(tabela.valor,0.00)) as valor,                                                                                     \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         'S' as tipo,                                                                                                                   \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= "     FROM(                                                                                                                              \n";
        $stSql .= "         SELECT                                                                                                                         \n";
        $stSql .= "             TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') AS data,                                                                        \n";
        $stSql .= "             nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   AS fundamentacao,                                                  \n";
        $stSql .= "             ctt.nom_tipo                        AS tipo_suplementacao,                                                                 \n";
        $stSql .= "             ode.cod_despesa,                                                                                                           \n";
        $stSql .= "                 ode.num_orgao                                                                                                          \n";
        $stSql .= "             ||'.'||ode.num_unidade                                                                                                     \n";
        $stSql .= "             ||'.'||ode.cod_funcao                                                                                                      \n";
        $stSql .= "             ||'.'||ode.cod_subfuncao                                                                                                   \n";
        $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                                    \n";
        $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                                         \n";
        $stSql .= "             ||'.'||replace(ocd.cod_estrutural,'.','')                                                                                  \n";
        $stSql .= "             AS dotacao,                                                                                                                \n";
        $stSql .= "             oss.valor,                                                                                                                 \n";
        $stSql .= "             CASE                                                                                                                       \n";
        $stSql .= "                 WHEN osa.cod_suplementacao_anulacao  is not null THEN 'Anulada'                                                        \n";
        $stSql .= "                 ELSE 'Válida'                                                                                                          \n";
        $stSql .= "             END as situacao,                                                                                                           \n";
        $stSql .= "             LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                                                                \n";
        $stSql .= "         FROM                                                                                                                           \n";
        $stSql .= "            orcamento.suplementacao                      AS osu                                                                     \n";
        $stSql .= "            LEFT JOIN orcamento.suplementacao_anulada    AS osa on(                                                                 \n";
        $stSql .= "                 osu.exercicio = osa.exercicio                                                                                          \n";
        $stSql .= "            AND  osu.cod_suplementacao = osa.cod_suplementacao                                                                          \n";
        $stSql .= "            ),                                                                                                                          \n";
        $stSql .= "            orcamento.suplementacao_suplementada AS oss,                                                                            \n";
        $stSql .= "            orcamento.despesa                    AS ode                                                                                  \n";
    $stSql .= "	      JOIN orcamento.programa_ppa_programa  												\n";
    $stSql .= "         ON programa_ppa_programa.cod_programa = ode.cod_programa									\n";
    $stSql .= "        AND programa_ppa_programa.exercicio   = ode.exercicio									\n";
    $stSql .= "       JOIN ppa.programa															\n";
    $stSql .= "         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa								\n";
    $stSql .= "       JOIN orcamento.pao_ppa_acao													\n";
    $stSql .= "         ON pao_ppa_acao.num_pao = ode.num_pao											\n";
    $stSql .= "        AND pao_ppa_acao.exercicio = ode.exercicio											\n";
    $stSql .= "       JOIN ppa.acao 															\n";
    $stSql .= "         ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao											\n";
    $stSql .= "          , orcamento.conta_despesa              AS ocd,                                                                            \n";
        $stSql .= "            contabilidade.tipo_transferencia     AS ctt,                                                                            \n";
        $stSql .= "            normas.norma                                AS nor                                                                             \n";
        $stSql .= "         WHERE                                                                                                                          \n";
        $stSql .= "             osu.cod_suplementacao   = oss.cod_suplementacao                                                                            \n";
        $stSql .= "         AND osu.exercicio           = oss.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ode.cod_despesa         = oss.cod_despesa                                                                                  \n";
        $stSql .= "         AND ode.exercicio           = oss.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ode.cod_conta           = ocd.cod_conta                                                                                    \n";
        $stSql .= "         AND ode.exercicio           = ocd.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.cod_norma           = nor.cod_norma                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ctt.cod_tipo           = osu.cod_tipo                                                                                      \n";
        $stSql .= "         AND ctt.exercicio          = osu.exercicio                                                                                     \n";
        $stSql .= "         AND ctt.cod_tipo          <> 16                                                                                                \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.exercicio          = '". $this->getDado('exercicio')."'                                                                \n";
        $stSql .= "                                                                                                                                        \n";
        if ($this->getDado("cod_norma")) {
            $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	                           \n";
        }
        if ($this->getDado("dt_inicial")) {
            $stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                                           \n";
        }
        if ($this->getDado("dt_final")) {
            $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                                             \n";
        }
        if ($this->getDado("cod_despesa")) {
            $stSql .= "     AND ode.cod_despesa         = ".$this->getDado("cod_despesa")."                                                                \n";
        }
        if ($this->getDado("cod_tipo")) {
            $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                                                                       \n";
        }
        $stSql .= "     AND ode.cod_entidade   in(".$this->getDado("cod_entidade").")                                                                      \n";
        $stSql .= "     ) AS tabela                                                                                                                        \n";
        if ($this->getDado("situacao")) {
            $stSql .= " WHERE                                                                                                                              \n";
            $stSql .= "     tabela.situacao = '".$this->getDado("situacao")."'                                                                             \n";
        }
        $stSql .= "     GROUP BY                                                                                                                           \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                            \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa,                                                                                                            \n";
        $stSql .= "         tabela.dotacao,                                                                                                                \n";
        $stSql .= "         tabela.valor,                                                                                                                  \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= " UNION                                                                                                                                  \n";
        $stSql .= "     SELECT                                                                                                                             \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                            \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa || ' - ' || publico.fn_mascara_dinamica( (                                                                  \n";
        $stSql .= "         SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '". $this->getDado('exercicio')."' ), tabela.dotacao ) AS dotacao,      \n";
        $stSql .= "         (sum(coalesce(tabela.valor,0.00)) * -1) AS valor,                                                                              \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         'R' as tipo,                                                                                                                   \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= "     FROM(                                                                                                                              \n";
        $stSql .= "         SELECT                                                                                                                         \n";
        $stSql .= "             TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') AS data,                                                                        \n";
        $stSql .= "             nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   AS fundamentacao,                                                                        \n";
        $stSql .= "             ctt.nom_tipo                        AS tipo_suplementacao,                                                                 \n";
        $stSql .= "             ode.cod_despesa,                                                                                                           \n";
        $stSql .= "                 ode.num_orgao                                                                                                          \n";
        $stSql .= "             ||'.'||ode.num_unidade                                                                                                     \n";
        $stSql .= "             ||'.'||ode.cod_funcao                                                                                                      \n";
        $stSql .= "             ||'.'||ode.cod_subfuncao                                                                                                   \n";
        $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                                    \n";
        $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                                         \n";
        $stSql .= "             ||'.'||replace(ocd.cod_estrutural,'.','')                                                                                  \n";
        $stSql .= "             AS dotacao,                                                                                                                \n";
        $stSql .= "             osr.valor,                                                                                                                 \n";
        $stSql .= "             CASE                                                                                                                       \n";
        $stSql .= "                 WHEN osa.cod_suplementacao_anulacao  IS not null THEN 'Anulada'                                                        \n";
        $stSql .= "                 ELSE 'Válida'                                                                                                          \n";
        $stSql .= "             END AS situacao,                                                                                                           \n";
        $stSql .= "             LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                                                                \n";
        $stSql .= "         FROM                                                                                                                           \n";
        $stSql .= "            orcamento.suplementacao                      AS osu                                                                     \n";
        $stSql .= "            LEFT JOIN orcamento.suplementacao_anulada    AS osa on(                                                                 \n";
        $stSql .= "                 osu.exercicio = osa.exercicio                                                                                          \n";
        $stSql .= "            AND  osu.cod_suplementacao = osa.cod_suplementacao                                                                          \n";
        $stSql .= "            ),                                                                                                                          \n";
        $stSql .= "            orcamento.suplementacao_reducao      AS osr,                                                                            \n";
        $stSql .= "            orcamento.despesa                    AS ode                                                                                  \n";
    $stSql .= "	      JOIN orcamento.programa_ppa_programa  												\n";
    $stSql .= "         ON programa_ppa_programa.cod_programa = ode.cod_programa									\n";
    $stSql .= "        AND programa_ppa_programa.exercicio   = ode.exercicio									\n";
    $stSql .= "       JOIN ppa.programa															\n";
    $stSql .= "         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa								\n";
    $stSql .= "       JOIN orcamento.pao_ppa_acao													\n";
    $stSql .= "         ON pao_ppa_acao.num_pao = ode.num_pao											\n";
    $stSql .= "        AND pao_ppa_acao.exercicio = ode.exercicio											\n";
    $stSql .= "       JOIN ppa.acao 															\n";
    $stSql .= "         ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao											\n";
    $stSql .= "          , orcamento.conta_despesa              AS ocd,                                                                            \n";
        $stSql .= "            contabilidade.tipo_transferencia     AS ctt,                                                                            \n";
        $stSql .= "            normas.norma                                AS nor                                                                             \n";
        $stSql .= "         WHERE                                                                                                                          \n";
        $stSql .= "             osu.cod_suplementacao   = osr.cod_suplementacao                                                                            \n";
        $stSql .= "         AND osu.exercicio           = osr.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osr.cod_despesa         = ode.cod_despesa                                                                                  \n";
        $stSql .= "         AND osr.exercicio           = ode.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ode.cod_conta           = ocd.cod_conta                                                                                    \n";
        $stSql .= "         AND ode.exercicio           = ocd.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.cod_norma           = nor.cod_norma                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ctt.cod_tipo           = osu.cod_tipo                                                                                      \n";
        $stSql .= "         AND ctt.exercicio          = osu.exercicio                                                                                     \n";
        $stSql .= "         AND ctt.cod_tipo          <> 16                                                                                                \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.exercicio          = '". $this->getDado('exercicio')."'                                                                \n";
        if ($this->getDado("cod_norma")) {
            $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	                           \n";
        }
        if ($this->getDado("dt_inicial")) {
            $stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                                           \n";
        }
        if ($this->getDado("dt_final")) {
            $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                                             \n";
        }
        if ($this->getDado("cod_despesa")) {
            $stSql .= "     AND ode.cod_despesa         = ".$this->getDado("cod_despesa")."                                                                \n";
        }
        if ($this->getDado("cod_tipo")) {
            $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                                                                       \n";
        }
        $stSql .= "     AND ode.cod_entidade   in(".$this->getDado("cod_entidade").")                                                                      \n";
        $stSql .= "     ) AS tabela                                                                                                                        \n";
        if ($this->getDado("situacao")) {
            $stSql .= " WHERE                                                                                                                              \n";
            $stSql .= "     tabela.situacao = '".$this->getDado("situacao")."'                                                                             \n";
        }
        $stSql .= "     GROUP BY                                                                                                                           \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                            \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa,                                                                                                            \n";
        $stSql .= "         tabela.dotacao,                                                                                                                \n";
        $stSql .= "         tabela.valor,                                                                                                                  \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= " ) AS tabela                                                                                                                            \n";
        $stSql .= " ORDER BY                                                                                                                               \n";
        $stSql .= "     to_date(tabela.data,'dd/mm/yyyy'),                                                                                                 \n";
        $stSql .= "     tabela.fundamentacao,                                                                                                                \n";
        $stSql .= "     tabela.tipo_suplementacao,                                                                                                         \n";
        $stSql .= "     tabela.dotacao,                                                                                                                    \n";
        $stSql .= "     abs(tabela.valor)                                                                                                                  \n";

    break;

    CASE 'dotacao':
        $stSql  = " SELECT                                                                                                                                 \n";
        $stSql .= "     *,                                                                                                                                 \n";
        $stSql .= "     CASE WHEN tabela.tipo='S' THEN tabela.valor END AS valor_suplementado,                                                             \n";
        $stSql .= "     CASE WHEN tabela.tipo='R' THEN tabela.valor END AS valor_reduzido                                                                  \n";
        $stSql .= " FROM (                                                                                                                                 \n";
        $stSql .= "     SELECT                                                                                                                             \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                            \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa,                                                                                                            \n";
        $stSql .= "         tabela.cod_despesa || ' - ' || publico.fn_mascara_dinamica( (                                                                  \n";
        $stSql .= "         SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '". $this->getDado('exercicio')."' ), tabela.dotacao ) AS dotacao,      \n";
        $stSql .= "         sum(coalesce(tabela.valor,0.00)) as valor,                                                                                     \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         'S' as tipo,                                                                                                                   \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= "     FROM(                                                                                                                              \n";
        $stSql .= "         SELECT                                                                                                                         \n";
        $stSql .= "             TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') AS data,                                                                        \n";
        $stSql .= "             nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   AS fundamentacao,                                                                        \n";
        $stSql .= "             ctt.nom_tipo                        AS tipo_suplementacao,                                                                 \n";
        $stSql .= "             ode.cod_despesa,                                                                                                           \n";
        $stSql .= "                 ode.num_orgao                                                                                                          \n";
        $stSql .= "             ||'.'||ode.num_unidade                                                                                                     \n";
        $stSql .= "             ||'.'||ode.cod_funcao                                                                                                      \n";
        $stSql .= "             ||'.'||ode.cod_subfuncao                                                                                                   \n";
        $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                                    \n";
        $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                                         \n";
        $stSql .= "             ||'.'||replace(ocd.cod_estrutural,'.','')                                                                                  \n";
        $stSql .= "             AS dotacao,                                                                                                                \n";
        $stSql .= "             oss.valor,                                                                                                                 \n";
        $stSql .= "             CASE                                                                                                                       \n";
        $stSql .= "                 WHEN osa.cod_suplementacao_anulacao  is not null THEN 'Anulada'                                                        \n";
        $stSql .= "                 ELSE 'Válida'                                                                                                          \n";
        $stSql .= "             END as situacao,                                                                                                           \n";
        $stSql .= "             LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                                                                \n";
        $stSql .= "         FROM                                                                                                                           \n";
        $stSql .= "            orcamento.suplementacao                      AS osu                                                                     \n";
        $stSql .= "            LEFT JOIN orcamento.suplementacao_anulada    AS osa on(                                                                 \n";
        $stSql .= "                 osu.exercicio = osa.exercicio                                                                                          \n";
        $stSql .= "            AND  osu.cod_suplementacao = osa.cod_suplementacao                                                                          \n";
        $stSql .= "            ),                                                                                                                          \n";
        $stSql .= "            orcamento.suplementacao_suplementada AS oss,                                                                            \n";
        $stSql .= "            orcamento.despesa                    AS ode                                                                                  \n";
    $stSql .= "	      JOIN orcamento.programa_ppa_programa  												\n";
    $stSql .= "         ON programa_ppa_programa.cod_programa = ode.cod_programa									\n";
    $stSql .= "        AND programa_ppa_programa.exercicio   = ode.exercicio									\n";
    $stSql .= "       JOIN ppa.programa															\n";
    $stSql .= "         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa								\n";
    $stSql .= "       JOIN orcamento.pao_ppa_acao													\n";
    $stSql .= "         ON pao_ppa_acao.num_pao = ode.num_pao											\n";
    $stSql .= "        AND pao_ppa_acao.exercicio = ode.exercicio											\n";
    $stSql .= "       JOIN ppa.acao 															\n";
    $stSql .= "         ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao											\n";
    $stSql .= "          , orcamento.conta_despesa              AS ocd,                                                                            \n";
        $stSql .= "            contabilidade.tipo_transferencia     AS ctt,                                                                            \n";
        $stSql .= "            normas.norma                                AS nor                                                                             \n";
        $stSql .= "         WHERE                                                                                                                          \n";
        $stSql .= "             osu.cod_suplementacao   = oss.cod_suplementacao                                                                            \n";
        $stSql .= "         AND osu.exercicio           = oss.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ode.cod_despesa         = oss.cod_despesa                                                                                  \n";
        $stSql .= "         AND ode.exercicio           = oss.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ode.cod_conta           = ocd.cod_conta                                                                                    \n";
        $stSql .= "         AND ode.exercicio           = ocd.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.cod_norma           = nor.cod_norma                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ctt.cod_tipo           = osu.cod_tipo                                                                                      \n";
        $stSql .= "         AND ctt.exercicio          = osu.exercicio                                                                                     \n";
        $stSql .= "         AND ctt.cod_tipo          <> 16                                                                                                \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.exercicio          = '". $this->getDado('exercicio')."'                                                                \n";
        $stSql .= "                                                                                                                                        \n";
        if ($this->getDado("cod_norma")) {
            $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	                           \n";
        }
        if ($this->getDado("dt_inicial")) {
            $stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                                           \n";
        }
        if ($this->getDado("dt_final")) {
            $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                                             \n";
        }
        if ($this->getDado("cod_despesa")) {
            $stSql .= "     AND ode.cod_despesa         = ".$this->getDado("cod_despesa")."                                                                \n";
        }
        if ($this->getDado("cod_tipo")) {
            $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                                                                       \n";
        }
        $stSql .= "     AND ode.cod_entidade   in(".$this->getDado("cod_entidade").")                                                                      \n";
        $stSql .= "     ) AS tabela                                                                                                                        \n";
        if ($this->getDado("situacao")) {
            $stSql .= " WHERE                                                                                                                              \n";
            $stSql .= "     tabela.situacao = '".$this->getDado("situacao")."'                                                                             \n";
        }
        $stSql .= "     GROUP BY                                                                                                                           \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                          \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa,                                                                                                            \n";
        $stSql .= "         tabela.dotacao,                                                                                                                \n";
        $stSql .= "         tabela.valor,                                                                                                                  \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= " UNION                                                                                                                                  \n";
        $stSql .= "     SELECT                                                                                                                             \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                          \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa,                                                                                                            \n";
        $stSql .= "         tabela.cod_despesa || ' - ' || publico.fn_mascara_dinamica( (                                                                  \n";
        $stSql .= "         SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '". $this->getDado('exercicio')."' ), tabela.dotacao ) AS dotacao,      \n";
        $stSql .= "         sum(coalesce(tabela.valor,0.00))  AS valor,                                                                                    \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         'R' as tipo,                                                                                                                   \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= "     FROM(                                                                                                                              \n";
        $stSql .= "         SELECT                                                                                                                         \n";
        $stSql .= "             TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') AS data,                                                                        \n";
        $stSql .= "             nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   AS fundamentacao,                                                                        \n";
        $stSql .= "             ctt.nom_tipo                        AS tipo_suplementacao,                                                                 \n";
        $stSql .= "             ode.cod_despesa,                                                                                                           \n";
        $stSql .= "                 ode.num_orgao                                                                                                          \n";
        $stSql .= "             ||'.'||ode.num_unidade                                                                                                     \n";
        $stSql .= "             ||'.'||ode.cod_funcao                                                                                                      \n";
        $stSql .= "             ||'.'||ode.cod_subfuncao                                                                                                   \n";
        $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                                    \n";
        $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                                         \n";
        $stSql .= "             ||'.'||replace(ocd.cod_estrutural,'.','')                                                                                  \n";
        $stSql .= "             AS dotacao,                                                                                                                \n";
        $stSql .= "             osr.valor,                                                                                                                 \n";
        $stSql .= "             CASE                                                                                                                       \n";
        $stSql .= "                 WHEN osa.cod_suplementacao_anulacao  IS not null THEN 'Anulada'                                                        \n";
        $stSql .= "                 ELSE 'Válida'                                                                                                          \n";
        $stSql .= "             END AS situacao,                                                                                                           \n";
        $stSql .= "             LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                                                                \n";
        $stSql .= "         FROM                                                                                                                           \n";
        $stSql .= "            orcamento.suplementacao                      AS osu                                                                     \n";
        $stSql .= "            LEFT JOIN orcamento.suplementacao_anulada    AS osa on(                                                                 \n";
        $stSql .= "                 osu.exercicio = osa.exercicio                                                                                          \n";
        $stSql .= "            AND  osu.cod_suplementacao = osa.cod_suplementacao                                                                          \n";
        $stSql .= "            ),                                                                                                                          \n";
        $stSql .= "            orcamento.suplementacao_reducao  AS osr,                                                                                \n";
        $stSql .= "            orcamento.despesa                AS ode                                                                                  \n";
    $stSql .= "	      JOIN orcamento.programa_ppa_programa  												\n";
    $stSql .= "         ON programa_ppa_programa.cod_programa = ode.cod_programa									\n";
    $stSql .= "        AND programa_ppa_programa.exercicio   = ode.exercicio									\n";
    $stSql .= "       JOIN ppa.programa															\n";
    $stSql .= "         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa								\n";
    $stSql .= "       JOIN orcamento.pao_ppa_acao													\n";
    $stSql .= "         ON pao_ppa_acao.num_pao = ode.num_pao											\n";
    $stSql .= "        AND pao_ppa_acao.exercicio = ode.exercicio											\n";
    $stSql .= "       JOIN ppa.acao 															\n";
    $stSql .= "         ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao											\n";
    $stSql .= "          , orcamento.conta_despesa          AS ocd,                                                                                \n";
        $stSql .= "            contabilidade.tipo_transferencia AS ctt,                                                                                \n";
        $stSql .= "            normas.norma                            AS nor                                                                                 \n";
        $stSql .= "         WHERE                                                                                                                          \n";
        $stSql .= "             osu.cod_suplementacao   = osr.cod_suplementacao                                                                            \n";
        $stSql .= "         AND osu.exercicio           = osr.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osr.cod_despesa         = ode.cod_despesa                                                                                  \n";
        $stSql .= "         AND osr.exercicio           = ode.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ode.cod_conta           = ocd.cod_conta                                                                                    \n";
        $stSql .= "         AND ode.exercicio           = ocd.exercicio                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND osu.cod_norma           = nor.cod_norma                                                                                    \n";
        $stSql .= "                                                                                                                                        \n";
        $stSql .= "         AND ctt.cod_tipo           = osu.cod_tipo                                                                                      \n";
        $stSql .= "         AND ctt.exercicio          = osu.exercicio                                                                                     \n";
        $stSql .= "         AND ctt.cod_tipo          <> 16                                                                                                \n";
        $stSql .= "         AND osu.exercicio          = '". $this->getDado('exercicio')."'                                                                \n";
        $stSql .= "                                                                                                                                        \n";
        if ($this->getDado("cod_norma")) {
            $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	                           \n";
        }
        if ($this->getDado("dt_inicial")) {
            $stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                                           \n";
        }
        if ($this->getDado("dt_final")) {
            $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                                             \n";
        }
        if ($this->getDado("cod_despesa")) {
            $stSql .= "     AND ode.cod_despesa         = ".$this->getDado("cod_despesa")."                                                                \n";
        }
        if ($this->getDado("cod_tipo")) {
            $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                                                                       \n";
        }
        $stSql .= "     AND ode.cod_entidade   in(".$this->getDado("cod_entidade").")                                                                      \n";
        $stSql .= "     ) AS tabela                                                                                                                        \n";
        if ($this->getDado("situacao")) {
            $stSql .= " WHERE                                                                                                                              \n";
            $stSql .= "     tabela.situacao = '".$this->getDado("situacao")."'                                                                             \n";
        }
        $stSql .= "     GROUP BY                                                                                                                           \n";
        $stSql .= "         tabela.data,                                                                                                                   \n";
        $stSql .= "         tabela.fundamentacao,                                                                                                          \n";
        $stSql .= "         tabela.tipo_suplementacao,                                                                                                     \n";
        $stSql .= "         tabela.cod_despesa,                                                                                                            \n";
        $stSql .= "         tabela.dotacao,                                                                                                                \n";
        $stSql .= "         tabela.valor,                                                                                                                  \n";
        $stSql .= "         tabela.situacao,                                                                                                               \n";
        $stSql .= "         tabela.fonte                                                                                                                   \n";
        $stSql .= " ) AS tabela                                                                                                                            \n";
        $stSql .= " ORDER BY                                                                                                                               \n";
        $stSql .= "     tabela.cod_despesa,                                                                                                                \n";
        $stSql .= "     to_date(tabela.data,'dd/mm/yyyy'),                                                                                                 \n";
        $stSql .= "     tabela.fundamentacao,                                                                                                                \n";
        $stSql .= "     tabela.tipo_suplementacao,                                                                                                         \n";
        $stSql .= "     abs(tabela.valor)                                                                                                                  \n";
    break;

    CASE 'anuladas':
        $stSql  = " SELECT                                                                      \n";
        $stSql .= "     *                                                                       \n";
        $stSql .= " FROM (                                                                      \n";
        $stSql .= "     SELECT                                                                  \n";
        $stSql .= "          TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') as data,                \n";
        $stSql .= "          TO_CHAR(osua.dt_suplementacao,'dd/mm/yyyy') as data_anulacao,      \n";
        $stSql .= "          nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   as fundamentacao,                \n";
        $stSql .= "          nor.cod_norma,                                                     \n";
        $stSql .= "          osu.motivo,                                                        \n";
        $stSql .= "          ctt.nom_tipo                        as tipo_suplementacao         \n";
        $stSql .= "      FROM                                                                   \n";
        $stSql .= "          orcamento.suplementacao                    as osu,                 \n";
        $stSql .= "          orcamento.suplementacao_anulada            as osa,             \n";
        $stSql .= "          orcamento.suplementacao                    as osua,            \n";
        $stSql .= "          orcamento.suplementacao_suplementada       as oss,             \n";
        $stSql .= "          orcamento.despesa                          as ode,             \n";
        $stSql .= "          contabilidade.tipo_transferencia           as ctt,             \n";
        $stSql .= "          normas.norma                                      as nor              \n";
        $stSql .= "      WHERE                                                                  \n";
        $stSql .= "          ctt.cod_tipo           = osu.cod_tipo                              \n";
        $stSql .= "      AND ctt.exercicio          = osu.exercicio                             \n";
        $stSql .= "      AND ctt.cod_tipo          <> 16                                        \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osu.exercicio          = osa.exercicio                             \n";
        $stSql .= "      AND osu.cod_suplementacao  = osa.cod_suplementacao                     \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osa.exercicio          = osua.exercicio                            \n";
        $stSql .= "      AND osa.cod_suplementacao_anulacao  = osua.cod_suplementacao           \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osu.cod_suplementacao  = oss.cod_suplementacao                     \n";
        $stSql .= "      AND osu.exercicio          = oss.exercicio                             \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND oss.cod_despesa        = ode.cod_despesa                           \n";
        $stSql .= "      AND oss.exercicio          = ode.exercicio                             \n";
        $stSql .= "                                                                             \n";
        $stSql .= "      AND osu.cod_norma          = nor.cod_norma                             \n";
        $stSql .= "      AND osu.exercicio          = '". $this->getDado('exercicio')."'        \n";
    if ($this->getDado("cod_norma")) {
        $stSql .= "     AND osu.cod_norma       	= ".$this->getDado("cod_norma")."                                    	  \n";
    }
    if ($this->getDado("dt_inicial")) {
        $stSql .= "     AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')                  \n";
    }
    if ($this->getDado("dt_final")) {
        $stSql .= "     AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')                    \n";
    }
    if ($this->getDado("cod_despesa")) {
        $stSql .= " AND ( (osu.cod_suplementacao IN (                                                                               \n";
        $stSql .= " SELECT cod_suplementacao FROM orcamento.suplementacao_suplementada                                              \n";
        $stSql .= " WHERE cod_despesa = ".$this->getDado("cod_despesa")." and exercicio = '".$this->getDado("exercicio")."'))       \n";
        $stSql .= " OR (osu.cod_suplementacao IN (                                                                                  \n";
        $stSql .= " SELECT cod_suplementacao FROM orcamento.suplementacao_reducao                                                   \n";
        $stSql .= " WHERE cod_despesa = ".$this->getDado("cod_despesa")." and exercicio = '".$this->getDado("exercicio")."')))      \n";
    }
    if ($this->getDado("cod_tipo")) {
        $stSql .= "     AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                                              \n";
    }
        $stSql .= "     AND ode.cod_entidade   in(".$this->getDado("cod_entidade").")                                         \n";
        $stSql .= "      GROUP BY                                                               \n";
        $stSql .= "          osu.dt_suplementacao,                                              \n";
        $stSql .= "          osua.dt_suplementacao,                                             \n";
        $stSql .= "          nor.num_norma,                                                     \n";
        $stSql .= "          nor.exercicio,                                                     \n";
        $stSql .= "          nor.cod_norma,                                                     \n";
        $stSql .= "          ctt.nom_tipo,                                                      \n";
        $stSql .= "          osu.motivo,                                                        \n";
        $stSql .= "          nor.nom_norma                                                      \n";
        $stSql .= "  ) AS tabela                                                                \n";
        $stSql .= "  ORDER BY                                                                   \n";
        $stSql .= "      tabela.fundamentacao,                                                    \n";
        $stSql .= "      to_date(tabela.data,'dd/mm/yyyy')                                      \n";
        $stSql .= " ; 																		    \n";

    break;

    CASE 'resumo':
            $stSql .= "             SELECT                                                                                  \n";
            $stSql .= "                 tabela.tipo_suplementacao,                                                          \n";
            $stSql .= "                 coalesce(sum(tabela.valor_su),0.00) as valor_su,                                    \n";
            $stSql .= "                 coalesce(sum(tabela.valor_re),0.00) as valor_re                                     \n";
            $stSql .= "             FROM (                                                                                  \n";
            $stSql .= "                 SELECT                                                                              \n";
            $stSql .= "                     osu.cod_suplementacao,                                                          \n";
            $stSql .= "                     ctt.nom_tipo                        AS tipo_suplementacao,                      \n";
            $stSql .= "                     oss.valor as valor_su,                                                          \n";
            $stSql .= "                     osr.valor as valor_re,                                                          \n";
            $stSql .= "                     CASE                                                                            \n";
            $stSql .= "                         WHEN osa.cod_suplementacao_anulacao  is not null THEN 'Anulada'             \n";
            $stSql .= "                         ELSE 'Válida'                                                               \n";
            $stSql .= "                     END as situacao                                                                 \n";
            $stSql .= "                 FROM                                                                                \n";
            $stSql .= "                    orcamento.suplementacao                      AS osu                              \n";
            $stSql .= "                    LEFT JOIN orcamento.suplementacao_anulada    AS osa on(                          \n";
            $stSql .= "                         osu.exercicio = osa.exercicio                                               \n";
            $stSql .= "                    AND  osu.cod_suplementacao = osa.cod_suplementacao                               \n";
            $stSql .= "                    )                                                                                \n";
            $stSql .= "                    LEFT JOIN  (                                                                     \n";
            $stSql .= "                        select                                                                       \n";
            $stSql .= "                            osr.cod_suplementacao,                                                   \n";
            $stSql .= "                            osr.exercicio,                                                           \n";
            $stSql .= "                            coalesce(sum(osr.valor),0.00) as valor                                   \n";
            $stSql .= "                        from                                                                         \n";
            $stSql .= "                            orcamento.suplementacao_reducao             as osr,                      \n";
            $stSql .= "                            orcamento.despesa                           aS ode,                      \n";
            $stSql .= "                            orcamento.conta_despesa                     aS ocd                       \n";
            $stSql .= "                         WHERE                                                                       \n";
            $stSql .= "                                                                                                     \n";
            $stSql .= "                             ode.cod_despesa         = osr.cod_despesa                               \n";
            $stSql .= "                         AND ode.exercicio           = osr.exercicio                                 \n";
            $stSql .= "                                                                                                     \n";
            $stSql .= "                         AND ode.cod_conta           = ocd.cod_conta                                 \n";
            $stSql .= "                         AND ode.exercicio           = ocd.exercicio                                 \n";
            $stSql .= "                                                                                                     \n";
         if ($this->getDado("cod_despesa")) {
            $stSql .= "                         AND ode.cod_despesa = ".$this->getDado("cod_despesa")."                     \n";
            $stSql .= "                         AND ode.exercicio = '".$this->getDado("exercicio")."'                       \n";
         }
            $stSql .= "                         AND ode.cod_entidade in(".$this->getDado("cod_entidade").")                 \n";
            $stSql .= "                         GROUP BY osr.cod_suplementacao, osr.exercicio                               \n";
            $stSql .= "                                                                                                     \n";
            $stSql .= "                    ) as osr                                                                         \n";
            $stSql .= "                           on (                                                                      \n";
            $stSql .= "                               osu.cod_suplementacao   = osr.cod_suplementacao                       \n";
            $stSql .= "                           AND osu.exercicio           = osr.exercicio                               \n";
            $stSql .= "                           AND osu.cod_suplementacao || '-' || osu.exercicio IN (                    \n";
            $stSql .= "                               SELECT                                                                \n";
            $stSql .= "                                cod_suplementacao || '-' || cl.exercicio                             \n";
            $stSql .= "                                    FROM                                                             \n";
            $stSql .= "                                        contabilidade.transferencia_despesa ctd,                     \n";
            $stSql .= "                                        contabilidade.lote cl                                        \n";
            $stSql .= "                                    WHERE                                                            \n";
            $stSql .= "                                        ctd.exercicio = cl.exercicio AND                             \n";
            $stSql .= "                                        ctd.cod_lote  = cl.cod_lote AND                              \n";
            $stSql .= "                                        ctd.tipo      = cl.tipo AND                                  \n";
            $stSql .= "                                        ctd.cod_entidade = cl.cod_entidade                           \n";
            if ($this->getDado("dt_inicial")) {
                $stSql .= "                                       AND cl.dt_lote >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy') \n";
            }
            if ($this->getDado("dt_final")) {
                $stSql .= "                                       AND cl.dt_lote <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy') \n";
            }
            $stSql .= "                                )                                                                        \n";

            $stSql .= "                                AND NOT EXISTS ( SELECT 1                                              \n";
            $stSql .= "                                                   FROM orcamento.suplementacao_anulada o_sa           \n";
            $stSql .= "                                                  WHERE o_sa.cod_suplementacao = osu.cod_suplementacao \n";
            $stSql .= "                                                    AND o_sa.exercicio         = osu.exercicio         \n";
            $stSql .= "                                                    AND o_sa.exercicio         = '".$this->getDado("exercicio")."' \n";
            $stSql .= "                                               )                                                       \n";
            $stSql .= "                                AND NOT EXISTS ( SELECT 1                                                           \n";
            $stSql .= "                                                   FROM orcamento.suplementacao_anulada o_sa2                       \n";
            $stSql .= "                                                  WHERE o_sa2.cod_suplementacao_anulacao = osu.cod_suplementacao    \n";
            $stSql .= "                                                    AND o_sa2.exercicio         = osu.exercicio                     \n";
            $stSql .= "                                                    AND o_sa2.exercicio         = '".$this->getDado("exercicio")."' \n";
            $stSql .= "                                               )                                                                    \n";
            $stSql .= "                   )                                                                                 \n";
            $stSql .= "                    LEFT JOIN  (                                                                     \n";
            $stSql .= "                        select                                                                       \n";
            $stSql .= "                            oss.cod_suplementacao,                                                   \n";
            $stSql .= "                            oss.exercicio,                                                           \n";
            $stSql .= "                            coalesce(sum(oss.valor),0.00) as valor                                   \n";
            $stSql .= "                        from                                                                         \n";
            $stSql .= "                            orcamento.suplementacao_suplementada        as oss,                      \n";
            $stSql .= "                            orcamento.despesa                           aS ode,                      \n";
            $stSql .= "                            orcamento.conta_despesa                     aS ocd                       \n";
            $stSql .= "                         WHERE                                                                       \n";
            $stSql .= "                                                                                                     \n";
            $stSql .= "                             ode.cod_despesa         = oss.cod_despesa                               \n";
            $stSql .= "                         AND ode.exercicio           = oss.exercicio                                 \n";
            $stSql .= "                                                                                                     \n";
            $stSql .= "                         AND ode.cod_conta           = ocd.cod_conta                                 \n";
            $stSql .= "                         AND ode.exercicio           = ocd.exercicio                                 \n";
        if ($this->getDado("cod_despesa")) {
            $stSql .= "                         AND ode.cod_despesa = ".$this->getDado("cod_despesa")."                     \n";
            $stSql .= "                         AND ode.exercicio   = '".$this->getDado("exercicio")."'                     \n";
        }
            $stSql .= "                         AND ode.cod_entidade  in(".$this->getDado("cod_entidade").")                \n";
            $stSql .= "                         GROUP BY oss.cod_suplementacao, oss.exercicio                               \n";
            $stSql .= "                     ) as oss                                                                        \n";
            $stSql .= "                              on (                                                                   \n";
            $stSql .= "                                  osu.cod_suplementacao   = oss.cod_suplementacao                    \n";
            $stSql .= "                              AND osu.exercicio           = oss.exercicio                            \n";
            $stSql .= "                           AND osu.cod_suplementacao || '-' || osu.exercicio IN (                    \n";
            $stSql .= "                               SELECT                                                                \n";
            $stSql .= "                                cod_suplementacao || '-' || cl.exercicio                             \n";
            $stSql .= "                                    FROM                                                             \n";
            $stSql .= "                                        contabilidade.transferencia_despesa ctd,                     \n";
            $stSql .= "                                        contabilidade.lote cl                                        \n";
            $stSql .= "                                    WHERE                                                            \n";
            $stSql .= "                                        ctd.exercicio = cl.exercicio AND                             \n";
            $stSql .= "                                        ctd.cod_lote  = cl.cod_lote AND                              \n";
            $stSql .= "                                        ctd.tipo      = cl.tipo AND                                  \n";
            $stSql .= "                                        ctd.cod_entidade = cl.cod_entidade                           \n";
            if ($this->getDado("dt_inicial")) {
                $stSql .= "                                       AND cl.dt_lote >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy') \n";
            }
            if ($this->getDado("dt_final")) {
                $stSql .= "                                       AND cl.dt_lote <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy') \n";
            }
            $stSql .= "                                )                                                                    \n";
            $stSql .= "                                AND NOT EXISTS ( SELECT 1                                              \n";
            $stSql .= "                                                   FROM orcamento.suplementacao_anulada o_sa3           \n";
            $stSql .= "                                                  WHERE o_sa3.cod_suplementacao = osu.cod_suplementacao \n";
            $stSql .= "                                                    AND o_sa3.exercicio         = osu.exercicio         \n";
            $stSql .= "                                                    AND o_sa3.exercicio         = '".$this->getDado("exercicio")."' \n";
            $stSql .= "                                               )                                                       \n";
            $stSql .= "                                AND NOT EXISTS ( SELECT 1                                                           \n";
            $stSql .= "                                                   FROM orcamento.suplementacao_anulada o_sa4                       \n";
            $stSql .= "                                                  WHERE o_sa4.cod_suplementacao_anulacao = osu.cod_suplementacao    \n";
            $stSql .= "                                                    AND o_sa4.exercicio         = osu.exercicio                     \n";
            $stSql .= "                                                    AND o_sa4.exercicio         = '".$this->getDado("exercicio")."' \n";
            $stSql .= "                                               )                                                                    \n";
            $stSql .= "                              ),                                                                     \n";
            $stSql .= "                 contabilidade.tipo_transferencia             AS ctt,                                \n";
            $stSql .= "                 normas.norma                                 AS nor                                 \n";
            $stSql .= "             WHERE                                                                                   \n";
            $stSql .= "                                                                                                     \n";
            $stSql .= "                 osu.cod_norma          = nor.cod_norma                                              \n";
            $stSql .= "                 AND ctt.cod_tipo           = osu.cod_tipo                                           \n";
            $stSql .= "                 AND ctt.exercicio          = osu.exercicio                                          \n";
         if ($this->getDado("cod_norma")) {
            $stSql .= "                 AND osu.cod_norma           = ".$this->getDado("cod_norma")."                       \n";
         }
            $stSql .= "                 AND ctt.cod_tipo          <> 16                                                     \n";
         if ($this->getDado("cod_tipo")) {
            $stSql .= "                 AND ctt.cod_tipo        = ".$this->getDado("cod_tipo")."                            \n";
         }
            $stSql .= "                 AND osu.exercicio          = '". $this->getDado('exercicio')."'                     \n";
         if ($this->getDado("dt_inicial")) {
            $stSql .= "                 AND osu.dt_suplementacao >= to_date('".$this->getDado("dt_inicial")."','dd/mm/yyyy')\n";
         }
         if ($this->getDado("dt_final")) {
            $stSql .= "                 AND osu.dt_suplementacao <= to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')  \n";
         }
            $stSql .= "                 AND (osr.valor <> 0 OR oss.valor <> 0)                                              \n";
            $stSql .="         ) AS tabela                                                                                  \n";
         if ($this->getDado("situacao")) {
            $stSql .= "          WHERE                                                                                      \n";
            $stSql .= "             tabela.situacao = '".$this->getDado("situacao")."'                                      \n";
         }
            $stSql .= "          GROUP by tabela.tipo_suplementacao                                                         \n";

    break;
  }

  return $stSql;
}

function montaRecuperaDotacoesPorDecreto()
{
    $stSql  = " SELECT                                                                                                                                       \n";
    $stSql .= "     *                                                                                                                                        \n";
    $stSql .= " FROM (                                                                                                                                       \n";
    $stSql .= "     SELECT                                                                                                                                   \n";
    $stSql .= "         tabela.cod_despesa || ' - ' || publico.fn_mascara_dinamica( (                                                                        \n";
    $stSql .= "         SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '".$this->getDado('exercicio')."'), tabela.dotacao ) AS dotacao_formatada,  \n";
    $stSql .= "         tabela.valor,                                                                                                                        \n";
    $stSql .= "         tabela.descricao,                                                                                                                    \n";
    $stSql .= "         'suplementacao' AS tipo,                                                                                                             \n";
    $stSql .= "         tabela.fonte                                                                                                                         \n";
    $stSql .= "     FROM(                                                                                                                                    \n";
    $stSql .= "         SELECT                                                                                                                               \n";
    $stSql .= "             ocd.descricao,                                                                                                                   \n";
    $stSql .= "             ode.cod_despesa,                                                                                                                 \n";
    $stSql .= "                 ode.num_orgao                                                                                                                \n";
    $stSql .= "             ||'.'||ode.num_unidade                                                                                                           \n";
    $stSql .= "             ||'.'||ode.cod_funcao                                                                                                            \n";
    $stSql .= "             ||'.'||ode.cod_subfuncao                                                                                                         \n";
    $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                                 \n";
    $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                                         \n";
    $stSql .= "             ||'.'||replace(ocd.cod_estrutural,'.','')                                                                                        \n";
    $stSql .= "             AS dotacao,                                                                                                                      \n";
    $stSql .= "             sum(coalesce(oss.valor,0.00)) as valor,                                                                                          \n";
    $stSql .= "             LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                                                                      \n";
    $stSql .= "         FROM                                                                                                                                 \n";
    $stSql .= "            orcamento.suplementacao              AS osu                                                                                   \n";
    $stSql .= "          LEFT JOIN orcamento.suplementacao_anulada  as osa on(                                                                           \n";
    $stSql .= "              osu.exercicio          = osa.exercicio                                                                                          \n";
    $stSql .= "          AND osu.cod_suplementacao  = osa.cod_suplementacao                                                                                  \n";
    $stSql .= "          ),                                                                                                                                  \n";
    $stSql .= "            orcamento.suplementacao_suplementada AS oss,                                                                                  \n";
    $stSql .= "            orcamento.despesa                    AS ode                                                                                  \n";
    $stSql .= "	      JOIN orcamento.programa_ppa_programa  												\n";
    $stSql .= "         ON programa_ppa_programa.cod_programa = ode.cod_programa									\n";
    $stSql .= "        AND programa_ppa_programa.exercicio   = ode.exercicio									\n";
    $stSql .= "       JOIN ppa.programa															\n";
    $stSql .= "         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa								\n";
    $stSql .= "       JOIN orcamento.pao_ppa_acao													\n";
    $stSql .= "         ON pao_ppa_acao.num_pao = ode.num_pao											\n";
    $stSql .= "        AND pao_ppa_acao.exercicio = ode.exercicio											\n";
    $stSql .= "       JOIN ppa.acao 															\n";
    $stSql .= "         ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao											\n";
    $stSql .= "          , orcamento.conta_despesa              AS ocd                                                                                       \n";
    $stSql .= "         WHERE                                                                                                                                \n";
    $stSql .= "             osu.cod_suplementacao   = oss.cod_suplementacao                                                                                  \n";
    $stSql .= "         AND osu.exercicio           = oss.exercicio                                                                                          \n";
    $stSql .= "         AND ode.cod_despesa         = oss.cod_despesa                                                                                        \n";
    $stSql .= "         AND ode.exercicio           = oss.exercicio                                                                                          \n";
    $stSql .= "         AND ode.cod_conta           = ocd.cod_conta                                                                                          \n";
    $stSql .= "         AND ode.exercicio           = ocd.exercicio                                                                                          \n";
    $stSql .= "         AND ode.cod_entidade        IN ( ".$this->getDado('cod_entidade')." )                                                                \n";
    $stSql .= "                                                                                                                                              \n";
    $stSql .= "         AND osu.cod_norma = ". $this->getDado('cod_norma')."                                                                                 \n";
    $stSql .= "         AND osu.exercicio = '". $this->getDado('exercicio')."'                                                                               \n";
    if ($this->getDado('cod_tipo')) {
        $stSql .= "         AND osu.cod_tipo  = ". $this->getDado('cod_tipo')."                                                                              \n";
    }
    $stSql .= "         AND osu.dt_suplementacao = to_date('".$this->getDado("data")."','dd/mm/yyyy')                                                        \n";
    if ($this->getDado("situacao")=='Válida') {
        $stSql .= "         AND osa.cod_suplementacao is null                                                                                                \n";
    } else {
        $stSql .= "         AND osa.cod_suplementacao is not null                                                                                            \n";
    }
    $stSql .= "                                                                                                                                              \n";
    $stSql .= "         GROUP by ode.cod_despesa, ode.num_orgao, ode.num_unidade, ode.cod_funcao, ode.cod_subfuncao, ppa.programa.num_programa, ppa.acao.num_acao, ocd.cod_estrutural, ocd.descricao, ode.cod_recurso \n";
    $stSql .= "     ) AS tabela                                                                                                                              \n";
    $stSql .= " UNION                                                                                                                                        \n";
    $stSql .= "     SELECT                                                                                                                                   \n";
    $stSql .= "         tabela.cod_despesa || ' - ' || publico.fn_mascara_dinamica( (                                                                        \n";
    $stSql .= "         SELECT valor FROM administracao.configuracao WHERE parametro = 'masc_despesa' AND exercicio = '". $this->getDado('exercicio')."' ), tabela.dotacao ) AS dotacao_formatada,  \n";
    $stSql .= "         tabela.valor,                                                                                                                        \n";
    $stSql .= "         tabela.descricao,                                                                                                                    \n";
    $stSql .= "         'reducao' AS tipo,                                                                                                                   \n";
    $stSql .= "         tabela.fonte                                                                                                                         \n";
    $stSql .= "     FROM(                                                                                                                                    \n";
    $stSql .= "         SELECT                                                                                                                               \n";
    $stSql .= "             ocd.descricao,                                                                                                                   \n";
    $stSql .= "             ode.cod_despesa,                                                                                                                 \n";
    $stSql .= "                 ode.num_orgao                                                                                                                \n";
    $stSql .= "             ||'.'||ode.num_unidade                                                                                                           \n";
    $stSql .= "             ||'.'||ode.cod_funcao                                                                                                            \n";
    $stSql .= "             ||'.'||ode.cod_subfuncao                                                                                                         \n";
    $stSql .= "             ||'.'||ppa.programa.num_programa                                                                                                          \n";
    $stSql .= "             ||'.'||ppa.acao.num_acao                                                                                                               \n";
    $stSql .= "             ||'.'||replace(ocd.cod_estrutural,'.','')                                                                                        \n";
    $stSql .= "             AS dotacao,                                                                                                                      \n";
    $stSql .= "             sum(coalesce(osr.valor,0.00)) as valor,                                                                                          \n";
    $stSql .= "             LPAD(ode.cod_recurso::varchar,2,'0')::varchar AS fonte                                                                                      \n";
    $stSql .= "         FROM                                                                                                                                 \n";
    $stSql .= "            orcamento.suplementacao          AS osu                                                                                       \n";
    $stSql .= "          LEFT JOIN orcamento.suplementacao_anulada  as osa on(                                                                           \n";
    $stSql .= "              osu.exercicio          = osa.exercicio                                                                                          \n";
    $stSql .= "          AND osu.cod_suplementacao  = osa.cod_suplementacao                                                                                  \n";
    $stSql .= "          ),                                                                                                                                  \n";
    $stSql .= "            orcamento.suplementacao_reducao  AS osr,                                                                                      \n";
    $stSql .= "            orcamento.despesa                AS ode                                                                                  \n";
    $stSql .= "	      JOIN orcamento.programa_ppa_programa  												\n";
    $stSql .= "         ON programa_ppa_programa.cod_programa = ode.cod_programa									\n";
    $stSql .= "        AND programa_ppa_programa.exercicio   = ode.exercicio									\n";
    $stSql .= "       JOIN ppa.programa															\n";
    $stSql .= "         ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa								\n";
    $stSql .= "       JOIN orcamento.pao_ppa_acao													\n";
    $stSql .= "         ON pao_ppa_acao.num_pao = ode.num_pao											\n";
    $stSql .= "        AND pao_ppa_acao.exercicio = ode.exercicio											\n";
    $stSql .= "       JOIN ppa.acao 															\n";
    $stSql .= "         ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao											\n";
    $stSql .= "          , orcamento.conta_despesa          AS ocd                                                                                       \n";
    $stSql .= "         WHERE                                                                                                                                \n";
    $stSql .= "             osu.cod_suplementacao   = osr.cod_suplementacao                                                                                  \n";
    $stSql .= "         AND osu.exercicio           = osr.exercicio                                                                                          \n";
    $stSql .= "         AND osr.cod_despesa         = ode.cod_despesa                                                                                        \n";
    $stSql .= "         AND osr.exercicio           = ode.exercicio                                                                                          \n";
    $stSql .= "         AND ode.cod_conta           = ocd.cod_conta                                                                                          \n";
    $stSql .= "         AND ode.exercicio           = ocd.exercicio                                                                                          \n";
    $stSql .= "         AND ode.cod_entidade        IN ( ".$this->getDado('cod_entidade')." )                                                                \n";
    $stSql .= "                                                                                                                                              \n";
    $stSql .= "         AND osu.cod_norma = ". $this->getDado('cod_norma')."                                                                                 \n";
    $stSql .= "         AND osu.exercicio = '". $this->getDado('exercicio')."'                                                                               \n";
    if ($this->getDado('cod_tipo')) {
        $stSql .= "         AND osu.cod_tipo  = ". $this->getDado('cod_tipo')."                                                                              \n";
    }
    $stSql .= "         AND osu.dt_suplementacao = to_date('".$this->getDado("data")."','dd/mm/yyyy')                                                        \n";
    if ($this->getDado("situacao")=='Válida') {
        $stSql .= "         AND osa.cod_suplementacao is null                                                                                                \n";
    } else {
        $stSql .= "         AND osa.cod_suplementacao is not null                                                                                            \n";
    }
    $stSql .= "                                                                                                                                              \n";
    $stSql .= "         GROUP by ode.cod_despesa, ode.num_orgao, ode.num_unidade, ode.cod_funcao, ode.cod_subfuncao, ppa.programa.num_programa, ppa.acao.num_acao, ocd.cod_estrutural, ocd.descricao, ode.cod_recurso \n";
    $stSql .= "     ) AS tabela                                                                                                                              \n";
    $stSql .= " ) AS tabela                                                                                                                                  \n";
    $stSql .= " ORDER BY                                                                                                                                     \n";
    $stSql .= "     tabela.tipo,                                                                                                                             \n";
    $stSql .= "     tabela.dotacao_formatada                                                                                                                 \n";

    return $stSql;
}

function recuperaDotacoesPorDecreto(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $stOrdem )
        $stOrdem ( strpos( 'ORDER BY', $stOrdem ) ) ? $stOrdem : ' ORDER BY '.$stOrdem;
    $stSql = $this->montaRecuperaDotacoesPorDecreto().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/* Utilizado no e-Sfinge (TCE-SC) */
function recuperaAlteracaoOrcamentaria(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAlteracaoOrcamentaria();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/* Utilizado no e-Sfinge (TCE-SC) */
function montaRecuperaAlteracaoOrcamentaria()
{
    $stSql = "
select suplementacao.exercicio
     , case when suplementacao.cod_tipo < 6 then '1'
            when ( suplementacao.cod_tipo > 5 and suplementacao.cod_tipo < 11 ) then '2'
            when suplementacao.cod_tipo = 11 then '3'
            when ( suplementacao.cod_tipo = 15 or suplementacao.cod_tipo = 16 ) then '4'
            when ( suplementacao.cod_tipo = 12 or suplementacao.cod_tipo = 14 ) then '5'
            when suplementacao.cod_tipo = 13 then '6'
            when ( suplementacao.cod_tipo = 18 or suplementacao.cod_tipo = 19 ) then '7'
            else ''
       end as tipo_alteracao
     , case when norma.cod_tipo_norma = 1 then '1'
            when norma.cod_tipo_norma = 2 then '5'
            else ''
       end as tipo_texto_juridico
     , suplementacao.cod_norma
     , despesa.num_unidade
     , substr(despesa.num_pao::VARCHAR, 1, 2) as tipo_acao
     , despesa.num_pao
     , substr(conta_despesa.cod_estrutural, 1, 1) as categoria_economica
     , substr(conta_despesa.cod_estrutural, 3, 1) as grupo_natureza_despesa
     , substr(conta_despesa.cod_estrutural, 5, 2) as modalidade_da_aplicacao
     , substr(conta_despesa.cod_estrutural, 8, 2) as elemento
     , recurso.cod_fonte
     , suplementacao_despesa.valor
  from orcamento.suplementacao
  join normas.norma
    on norma.cod_norma = suplementacao.cod_norma
  join (
         select suplementacao_suplementada.exercicio
              , suplementacao_suplementada.cod_suplementacao
              , suplementacao_suplementada.cod_despesa
              , suplementacao_suplementada.valor
           from orcamento.suplementacao_suplementada
          union
         select suplementacao_reducao.exercicio
              , suplementacao_reducao.cod_suplementacao
              , suplementacao_reducao.cod_despesa
              , suplementacao_reducao.valor
           from orcamento.suplementacao_reducao
       ) as suplementacao_despesa
    on suplementacao_despesa.exercicio = suplementacao.exercicio
   and suplementacao_despesa.cod_suplementacao = suplementacao.cod_suplementacao
  join orcamento.despesa
    on despesa.exercicio = suplementacao_despesa.exercicio
   and despesa.cod_despesa = suplementacao_despesa.cod_despesa
  join orcamento.conta_despesa
    on conta_despesa.exercicio = despesa.exercicio
   and conta_despesa.cod_conta = despesa.cod_conta
  join orcamento.recurso
    on recurso.exercicio = despesa.exercicio
    and recurso.cod_recurso = despesa.cod_recurso

where suplementacao.exercicio = '". $this->getDado('exercicio')."'
and despesa.cod_entidade IN ( ". $this->getDado('cod_entidade')." )
and suplementacao.dt_suplementacao >= to_date( '". $this->getDado('dt_inicial')."', 'dd/mm/yyyy' )
and suplementacao.dt_suplementacao <= to_date( '". $this->getDado('dt_final')."', 'dd/mm/yyyy' )
    ";

    return $stSql;
}

/* Utilizado no e-Sfinge (TCE-SC) */
function recuperaFonteRecursosCréditoAdicional( &$rsRecordSet, $boTransacao = "" ) {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFonteRecursosCreditoAdicional();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/* Utilizado no e-Sfinge (TCE-SC) */
function montaRecuperaFonteRecursosCreditoAdicional()
{
    $stSql = "
select case when norma.cod_tipo_norma = 1 then '1'
            when norma.cod_tipo_norma = 2 then '5'
            else ''
       end as tipo_texto_juridico
     , suplementacao.cod_norma
     , recurso.cod_fonte
     , suplementacao_suplementada.valor
  from orcamento.suplementacao
  join orcamento.suplementacao_suplementada
    on suplementacao_suplementada.exercicio = suplementacao.exercicio
   and suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
  join normas.norma
    on norma.cod_norma = suplementacao.cod_norma
  join orcamento.despesa
    on despesa.exercicio = suplementacao_suplementada.exercicio
   and despesa.cod_despesa = suplementacao_suplementada.cod_despesa
  join orcamento.recurso
    on recurso.exercicio = despesa.exercicio
    and recurso.cod_recurso = despesa.cod_recurso
 where suplementacao.exercicio = '". $this->getDado('exercicio')."'
   and despesa.cod_entidade in ( ". $this->getDado('cod_entidade')." )
   and suplementacao.dt_suplementacao >= to_date( '". $this->getDado('dt_inicial')."', 'dd/mm/yyyy' )
   and suplementacao.dt_suplementacao < to_date( '". $this->getDado('dt_final')."', 'dd/mm/yyyy' )
    ";

    return $stSql;
}

function recuperaDadosMANAD(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosMANAD();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/* Utilizado no e-Sfinge (TCE-SC) */
function montaRecuperaDadosMANAD()
{
    $stSql = "
        SELECT *,
                           'L300' as reg,
                            CASE
                                 WHEN tabela.tipo_suplementacao like '%suplementa%' OR tabela.tipo_suplementacao like '%Suplementa%'  THEN '1'
                                 WHEN tabela.tipo_suplementacao like '%especia%'    OR tabela.tipo_suplementacao like '%Especia'      THEN '2'
                                 WHEN tabela.tipo_suplementacao like '%extraordin%' OR tabela.tipo_suplementacao  like '%Extraordin%'  THEN '3'
                             END as tip_cred_adicional,

                            CASE
                                 WHEN tabela.tipo_suplementacao like '%superávit%' OR tabela.tipo_suplementacao like  '%Superávit%' THEN '1'
                                 WHEN tabela.tipo_suplementacao like '%arrecadaç%' OR tabela.tipo_suplementacao like  '%Arrecadaç%' THEN '2'
                                 WHEN tabela.tipo_suplementacao like '%crédito%'   OR tabela.tipo_suplementacao like '%crédito%'    THEN '3'
                                 WHEN tabela.tipo_suplementacao like '%auxílios%'  OR tabela.tipo_suplementacao like '%Auxílios%' OR tabela.tipo_suplementacao like '%convênios%' OR tabela.tipo_suplementacao like '%Convênios%' THEN '4'
                                 WHEN tabela.tipo_suplementacao like '%reduç%'     OR tabela.tipo_suplementacao like '%Reduç%'      THEN '5'
                             END as tip_orig_recurso
                      FROM (
                            SELECT

                                 ode.cod_entidade                    as entidade,
                                 nor.num_norma||'/'||nor.exercicio as nm_lei_decreto,
                                 TO_CHAR(osu.dt_suplementacao,'dd/mm/yyyy') as dt_lei_decreto,
                                 nor.nom_norma||' '||nor.num_norma||'/'||nor.exercicio   as fundamentacao,
                                 ctt.nom_tipo                        as tipo_suplementacao,
                                 sum(coalesce(oss.valor,0.00)) as vl_red_dotacoes,
                                 CASE
                                    WHEN osa.cod_suplementacao_anulacao  is not null THEN 'Anulada'
                                    ELSE 'Válida'
                                 END as situacao,
                                 LPAD(ode.cod_recurso,2,0)::varchar AS fonte
                             FROM
                                 orcamento.suplementacao                     as osu
                                 LEFT JOIN orcamento.suplementacao_anulada   as osa on(
                                     osu.exercicio = osa.exercicio
                                 AND osu.cod_suplementacao = osa.cod_suplementacao
                                 ),
                                 orcamento.suplementacao_suplementada       as oss,
                                 orcamento.despesa                           as ode,
                                 contabilidade.tipo_transferencia           as ctt,
                                 normas.norma                                       as nor
                             WHERE
                                 ctt.cod_tipo            = osu.cod_tipo
                             AND ctt.exercicio           = osu.exercicio
                             AND ctt.cod_tipo           <> 16

                             AND osu.cod_suplementacao    = oss.cod_suplementacao
                             AND osu.exercicio           = oss.exercicio

                             AND oss.cod_despesa            = ode.cod_despesa
                             AND oss.exercicio           = ode.exercicio

                             AND osu.cod_norma           = nor.cod_norma
                             AND osu.exercicio           = '". $this->getDado('stExercicio')."'

                            -- AND osu.dt_suplementacao >= to_date( '". $this->getDado('dtInicial')."','ddmmyyyy')
                             --AND osu.dt_suplementacao <= to_date('". $this->getDado('dtFinal')."','ddmmyyyy')
                             AND ode.cod_entidade   in(". $this->getDado('stCodEntidades').")
                             GROUP BY
                                 ode.cod_entidade,
                                 osu.dt_suplementacao,
                                 nor.num_norma,
                                 nor.exercicio,
                                 ctt.nom_tipo,
                                 osa.cod_suplementacao_anulacao,
                                 nor.nom_norma,
                                 ode.cod_recurso
                         ) AS tabela
                         ORDER BY
                             tabela.entidade,tabela.situacao,to_date(tabela.dt_lei_decreto,'dd/mm/yyyy')
 ;

    ";

    return $stSql;
}

function recuperaValorTotalSuplementado(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if( $stOrdem )
        $stOrdem ( strpos( 'ORDER BY', $stOrdem ) ) ? $stOrdem : ' ORDER BY '.$stOrdem;
    $stSql = $this->montaRecuperaValorTotalSuplementado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaValorTotalSuplementado()
{
    $stSql="
                    SELECT SUM(suplementacao_suplementada.valor) as valor_suplementado

                      FROM orcamento.suplementacao  

                INNER JOIN orcamento.suplementacao_suplementada
                        ON suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND suplementacao_suplementada.exercicio         = suplementacao.exercicio

                 LEFT JOIN orcamento.suplementacao_anulada
                        ON suplementacao_anulada.exercicio         = suplementacao.exercicio
                       AND suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao

                 LEFT JOIN orcamento.despesa
                        ON despesa.exercicio   = suplementacao_suplementada.exercicio
                       AND despesa.cod_despesa = suplementacao_suplementada.cod_despesa

                     WHERE suplementacao_anulada.cod_suplementacao IS NULL
                       AND suplementacao.cod_tipo = 1 
            ";

    return $stSql;
}

}//END OF CLASS
