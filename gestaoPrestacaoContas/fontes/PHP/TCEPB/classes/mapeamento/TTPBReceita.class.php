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
    * Extensão da Classe de mapeamento
    * Data de Criação: 05/02/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TTPBReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

/**
  *
  * Data de Criação: 05/02/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTPBReceita extends TOrcamentoContaReceita
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBReceita()
{
    parent::TOrcamentoContaReceita();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

//Mapeamento do case pode ser encontrado no documento de tabelas auxiliares do tribunal
function montaRecuperaTodos()
{
    $stSql  = " SELECT  rpad(replace(codigo_receita,'.',''),9,'0') as codigo_receita                    \n";
    $stSql .= "         ,descricao                                                                      \n";
    $stSql .= "         ,replace('4.'||codigo_receita||'.00.00.00','.','') as codigo_conta_contabil     \n";
    $stSql .= " FROM    orcamento.conta_receita as cr                                                   \n";
    $stSql .= " ,(                                                                                      \n";
    $stSql .= "     SELECT   substr(cod_estrutural,1,13) as codigo_receita                              \n";
    $stSql .= "     FROM     orcamento.conta_receita as cr                                              \n";
    $stSql .= "             ,orcamento.receita       as re                                              \n";
    $stSql .= "     WHERE   cr.exercicio = re.exercicio                                                 \n";
    $stSql .= "     AND     cr.cod_conta = re.cod_conta                                                 \n";
    $stSql .= "     AND     cr.exercicio = '".$this->getDado('exercicio')."'                            \n";
    $stSql .= "     AND     re.cod_entidade  in(".$this->getDado("stEntidades").")                      \n";
    $stSql .= "     GROUP BY substr(cod_estrutural,1,13)                                                \n";
    $stSql .= "     ORDER BY substr(cod_estrutural,1,13)                                                \n";
    $stSql .= " ) tab1                                                                                  \n";
    $stSql .= " WHERE   cr.exercicio = '".$this->getDado('exercicio')."'                                \n";
    $stSql .= " AND     cr.cod_estrutural = codigo_receita||'.00.00.00'                                 \n";
    $stSql .= " ORDER BY cod_estrutural                                                                 \n";

    return $stSql;
}

function recuperaRelacionamentoReceitaOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRelacionamentoReceitaOrcamentaria().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoReceitaOrcamentaria()
{
      $stSql = "


      select
rrl.estrutural_receita,
rrl.estrutural_portaria,
rrl.exercicio,
    ( select substr(descricao,1,80) as descricao from orcamento.conta_receita as ocrt
        where
            REPLACE(ocrt.cod_estrutural,'.','') LIKE REPLACE(rrl.ligadura,'.','') || '%'
        order by ocrt.cod_estrutural
        limit 1
    ) as denominacao,

    ( select replace(cpc.cod_estrutural ,'.','')
        from contabilidade.plano_conta as cpc
       where
              CASE WHEN substr(replace(rrl.ligadura,'.',''),1,1) <> 9
                   THEN REPLACE(cpc.cod_estrutural,'.','') LIKE replace('4'||rrl.ligadura,'.','') || '%'
                   ELSE REPLACE(cpc.cod_estrutural,'.','') LIKE replace(rrl.ligadura,'.','') || '%'
                END
         and cpc.exercicio = rrl.exercicio
         and exists( select 1
                        from contabilidade.plano_analitica
                       where plano_analitica.cod_conta = cpc.cod_conta
                         and plano_analitica.exercicio = cpc.exercicio
                       limit 1
                    )
       limit 1
    ) as estrutural_contabil

from(


        SELECT
             case when substr(replace(cr.cod_estrutural,'.',''),1,1) <> 9
                then
                    '0'|| substr(replace(cr.cod_estrutural,'.',''),1,8)
                else
                    substr(replace(cr.cod_estrutural,'.',''),1,9)
             end as estrutural_receita
             ,case when substr(replace(cr.cod_estrutural,'.',''),1,1) <> 9
                then
                    '0'|| substr(replace(cr.cod_estrutural,'.',''),1,8)
                else
                    substr(replace(cr.cod_estrutural,'.',''),1,9)
             end as estrutural_portaria
             , CASE WHEN substr(cr.cod_estrutural,1,1) <> 9
                    THEN substr(replace(cr.cod_estrutural,'.',''),1,8)
                    ELSE substr(replace(cr.cod_estrutural,'.',''),1,9)
               END AS ligadura
           --,substr(replace(cr.cod_estrutural,'.',''),1,8) as ligadura
             , r.exercicio
          FROM
               orcamento.receita           as r
             , orcamento.conta_receita     as cr
         WHERE
                abs(r.vl_original) >  0
           AND r.exercicio   = cr.exercicio
           AND r.cod_conta   = cr.cod_conta
           AND r.exercicio   =   '".$this->getDado('exercicio')."'
           AND r.cod_entidade in ( ".$this->getDado('stEntidades')." )
      GROUP BY estrutural_receita,estrutural_portaria,ligadura,r.exercicio

UNION	ALL


        SELECT
             case when substr(replace(cr.cod_estrutural,'.',''),1,1) <> 9
                then
                    '0'|| substr(replace(cr.cod_estrutural,'.',''),1,8)
                else
                    substr(replace(cr.cod_estrutural,'.',''),1,9)
             end as estrutural_receita
             ,case when substr(replace(cr.cod_estrutural,'.',''),1,1) <> 9
                then
                    '0'|| substr(replace(cr.cod_estrutural,'.',''),1,8)
                else
                    substr(replace(cr.cod_estrutural,'.',''),1,9)
             end as estrutural_portaria
            , CASE WHEN substr(cr.cod_estrutural,1,1) <> 9
                    THEN substr(replace(cr.cod_estrutural,'.',''),1,8)
                    ELSE substr(replace(cr.cod_estrutural,'.',''),1,9)
               END AS ligadura
             --,substr(replace(cr.cod_estrutural,'.',''),1,8) as ligadura
             , r.exercicio
          FROM
               orcamento.receita           as r
               JOIN ( SELECT lr.cod_receita
                           , lr.exercicio
                           , lr.cod_entidade
                        FROM contabilidade.lancamento_receita as lr
                           , contabilidade.lote as l
                       WHERE lr.cod_lote             = l.cod_lote
                         AND lr.tipo                 = l.tipo
                         AND lr.exercicio            = l.exercicio
                         AND lr.cod_entidade         = l.cod_entidade
                         AND lr.cod_entidade in (".$this->getDado('stEntidades').")
                         AND to_char(l.dt_lote,'mm') =  '".$this->getDado('inMes')."'

                    GROUP BY lr.cod_receita
                           , lr.exercicio
                           , lr.cod_entidade ) as ctb
                 ON (     ctb.cod_receita = r.cod_receita
                      AND ctb.exercicio   = r.exercicio
                      AND ctb.cod_entidade = r.cod_entidade )
             , orcamento.conta_receita     as cr
         WHERE
                r.vl_original <=   0
           AND r.exercicio   = cr.exercicio
           AND r.cod_conta   = cr.cod_conta
           AND r.exercicio   =   '".$this->getDado('exercicio')."'
           AND r.cod_entidade in ( ".$this->getDado('stEntidades')." )
      GROUP BY estrutural_receita,estrutural_portaria,ligadura,r.exercicio
      ORDER BY estrutural_receita

      ) as rrl
      group by estrutural_receita,estrutural_portaria,exercicio,denominacao,estrutural_contabil
      ORDER BY estrutural_receita
";

    return $stSql;
}

function recuperaReceitaPrevista(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaReceitaPrevista().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaPrevista()
{
    $stSQL .= " SELECT   re.exercicio   \n";
    $stSQL .= "         ,case when substr(replace(cr.cod_estrutural,'.',''),1,1)::integer <> 9 \n";
    $stSQL .= "             then \n";
    $stSQL .= "                 '00'|| substr(replace(cr.cod_estrutural,'.',''),1,8) \n";
    $stSQL .= "             else \n";
    $stSQL .= "                 '0'|| substr(replace(cr.cod_estrutural,'.',''),1,9) \n";
    $stSQL .= "         end as codigo_receita      \n";
    $stSQL .= "         ,replace(abs(sum(vl_original))::varchar,'.',',') as vl_original    \n";
    $stSQL .= " FROM     orcamento.receita       as re   \n";
    $stSQL .= "         ,orcamento.conta_receita as cr   \n";
    $stSQL .= " WHERE   re.exercicio = cr.exercicio   \n";
    $stSQL .= " AND     re.cod_conta = cr.cod_conta   \n";
    $stSQL .= " AND     re.exercicio = '".$this->getDado('exercicio')."' \n";
    if (trim($this->getDado('stEntidades'))) {
        $stSQL .= " AND     re.cod_entidade IN (".$this->getDado('stEntidades').") \n";
    }
    $stSQL .= " AND ABS(vl_original) > 0 \n";
    //$stSQL .= " GROUP BY re.exercicio, substr(replace(cr.cod_estrutural,'.',''),1,9)   \n";
    $stSQL .= " GROUP BY re.exercicio, codigo_receita \n";
    $stSQL .= " ORDER BY re.exercicio, codigo_receita \n";

    return $stSQL;
}

function recuperaReceitaOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaReceitaOrcamentaria().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaOrcamentaria()
{
    $stSql = "
    SELECT
        codigo_receita,
        tipo_registro,
        ABS(SUM(valor)) as valor,
        competencia_receita
    FROM ( SELECT case when substr(replace(cod_estrutural,'.',''),1,1)::integer <> 9 then
                           '00'|| substr(replace(cod_estrutural,'.',''),1,8)
                       else
                           '0'||substr(replace(cod_estrutural,'.',''),1,9)
                   end as codigo_receita
                 , CASE WHEN substr(cod_estrutural,1,1)::integer = 9 THEN
                            3
                        WHEN lancamento_receita.estorno = false THEN
                            1
                        ELSE
                            2
                   END as tipo_registro
                 , SUM(valor_lancamento.vl_lancamento) as valor
                 , TO_CHAR(lote.dt_lote,'mm') || lote.exercicio AS competencia_receita
              FROM orcamento.conta_receita
        INNER JOIN orcamento.receita
                ON receita.exercicio = conta_receita.exercicio
               AND receita.cod_conta = conta_receita.cod_conta
        INNER JOIN contabilidade.lancamento_receita
                ON lancamento_receita.exercicio = receita.exercicio
               AND lancamento_receita.cod_receita = receita.cod_receita
        INNER JOIN contabilidade.lancamento
                ON lancamento.exercicio = lancamento_receita.exercicio
               AND lancamento.cod_entidade = lancamento_receita.cod_entidade
               AND lancamento.cod_lote = lancamento_receita.cod_lote
               AND lancamento.tipo = lancamento_receita.tipo
               AND lancamento.sequencia = lancamento_receita.sequencia
        INNER JOIN contabilidade.valor_lancamento
                ON valor_lancamento.exercicio = lancamento.exercicio
               AND valor_lancamento.cod_entidade = lancamento.cod_entidade
               AND valor_lancamento.cod_lote = lancamento.cod_lote
               AND valor_lancamento.tipo = lancamento.tipo
               AND valor_lancamento.sequencia = lancamento.sequencia
        INNER JOIN contabilidade.lote
                ON lote.exercicio = valor_lancamento.exercicio
               AND lote.cod_entidade = valor_lancamento.cod_entidade
               AND lote.tipo = valor_lancamento.tipo
               AND lote.cod_lote = valor_lancamento.cod_lote
             WHERE conta_receita.exercicio = '".$this->getDado('exercicio')."'
               AND valor_lancamento.tipo = 'A'
               AND (
                     ( lancamento_receita.estorno=false AND  valor_lancamento.tipo_valor='C' )
                     OR
                     ( lancamento_receita.estorno=true  AND valor_lancamento.tipo_valor='D' )
                   )
      ";
    if (trim($this->getDado('stEntidades'))) {
        $stSql .= " AND receita.cod_entidade IN (".$this->getDado('stEntidades').") ";
    }
    if ( $this->getDado('inMes') != '' ) {
        $stSql .= " AND TO_CHAR(lote.dt_lote,'mm') = '".$this->getDado('inMes')."' ";
    }
    $stSql .= "
            GROUP BY cod_estrutural
                   , tipo_registro
                   , lancamento_receita.estorno
                   , TO_CHAR(lote.dt_lote,'mm')
                   , lote.exercicio
             ORDER BY cod_estrutural
                    , tipo_registro
                    , lancamento_receita.estorno
    ) as orcamento_receita
     GROUP BY codigo_receita
            , tipo_registro
            , competencia_receita
     ORDER BY codigo_receita
    ";

    return $stSql;
}

}

?>