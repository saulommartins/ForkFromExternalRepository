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

    * @author Tonismar R. Bernardo

    * @date: 17/03/2011

    * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCEAMReceita extends TOrcamentoContaReceita
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
    $stSql .= "  SELECT  cod_receita                                                               \n";
    $stSql .= "         , CASE WHEN sum(vl_original) > 0 THEN                               \n";
    $stSql .= "                   LPAD((abs(sum(vl_original))::text), 16, '0')                                       \n";
    $stSql .= "            ELSE                                                                                      \n";
    $stSql .= "                   '-'||LPAD((abs(sum(vl_original))::text), 15, '0')                                  \n";
    $stSql .= "            END as vl_original                                                                                      \n";
    $stSql .= "    FROM (                                                                          \n";
    $stSql .= "          SELECT  receita.vl_original                                               \n";
    $stSql .= "                 ,CASE WHEN SUBSTR(conta_receita.cod_estrutural,1,1)::integer = 9            \n";
    $stSql .= "                       THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)\n";
    $stSql .= "                       WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6) = '111305'\n";
    $stSql .= "                         OR  SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6) = '172133'\n";
    $stSql .= "                       THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6)::varchar,8,'')   \n";
    $stSql .= "                       ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,8)\n";
    $stSql .= "                  END AS cod_receita                                                \n";
    $stSql .= "            FROM orcamento.receita                                                  \n";
    $stSql .= "            JOIN orcamento.conta_receita                                            \n";
    $stSql .= "              ON receita.exercicio = conta_receita.exercicio                        \n";
    $stSql .= "             AND receita.cod_conta = conta_receita.cod_conta                        \n";
    $stSql .= "             AND receita.exercicio = '".$this->getDado('exercicio')."'              \n";
    $stSql .= "             AND receita.cod_entidade in (".$this->getDado('entidades').")          \n";
    $stSql .= "             AND receita.vl_original <> 0                                           \n";
    $stSql .= "       ) as receitas                                                                \n";
    $stSql .= "GROUP BY cod_receita                                                                \n";
    $stSql .= "ORDER BY cod_receita                                                                \n";

    return $stSql;
}

function recuperaReceitaOrcamentariaLancada(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaReceitaOrcamentariaLancada().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReceitaOrcamentariaLancada()
{
    $stSql = "
SELECT codigo_receita
         , tipo_atualizacao
         , ABS(SUM(vl_lancamento)) as vl_lancamento
 FROM (
    SELECT codigo_receita
         , tipo_atualizacao
         , ABS(SUM(vl_lancamento)) as vl_lancamento
    FROM ( SELECT
                CASE WHEN SUBSTR(conta_receita.cod_estrutural,1,1)::integer = 9
                     THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                     WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6) = '111305'
                       OR  SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6) = '172133'
                     THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6)::varchar,8,'')
                     ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,8)
                   END AS codigo_receita
                , CASE WHEN lancamento_receita.estorno = false THEN
                            1
                       ELSE
                            2
                   END as tipo_atualizacao
                 , SUM(valor_lancamento.vl_lancamento) as vl_lancamento
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
                   , lancamento_receita.estorno
                   , TO_CHAR(lote.dt_lote,'mm')
                   , lote.exercicio
             ORDER BY cod_estrutural
                    , lancamento_receita.estorno
    ) as orcamento_receita
     GROUP BY codigo_receita
            , tipo_atualizacao

    ";

    if ($this->getDado('boIncorporar')) {
      $stSql .= " UNION
          SELECT codigo_receita
             , tipo_atualizacao
             , ABS(SUM(vl_lancamento)) as vl_lancamento
        FROM ( SELECT
                    CASE WHEN SUBSTR(conta_receita.cod_estrutural,1,1)::integer = 9
                         THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                         WHEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6) = '111305'
                           OR  SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6) = '172133'
                         THEN RPAD(SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,6)::varchar,8,'')
                         ELSE SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,8)
                       END AS codigo_receita
                    , CASE WHEN lancamento_receita.estorno = false THEN
                                1
                           ELSE
                                2
                       END as tipo_atualizacao
                     , SUM(valor_lancamento.vl_lancamento) as vl_lancamento
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
           AND receita.cod_entidade IN (".$this->getDado('stCodEntidadesIncorporar').")
                GROUP BY cod_estrutural
                       , lancamento_receita.estorno
                       , lote.exercicio
                 ORDER BY cod_estrutural
                        , lancamento_receita.estorno
        ) as orcamento_receita
         GROUP BY codigo_receita
                , tipo_atualizacao ";
    }

    $stSql .= " ) as tabela
     GROUP BY codigo_receita
            , tipo_atualizacao
     ORDER BY codigo_receita ";

    return $stSql;
}

}
