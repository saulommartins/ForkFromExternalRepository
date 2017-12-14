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
    * Classe de mapeamento da tabela TTCEMG
    * Data de Criação: 15/12/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    $Id: TTCEMSUPDEF.class.php 64279 2016-01-05 19:42:43Z lisiane $

    * @package URBEM
    * @subpackage Mapeamento
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMSUPDEF extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosSUPDEF10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosSUPDEF10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;
        $stSql = $this->montaRecuperaDadosSUPDEF10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosSUPDEF10()
    {
        $stSql  = "    SELECT 10 AS tiporegistro
                            , CASE WHEN valor_ativo > valor_passivo
                                            THEN 'S'
                                            WHEN valor_passivo > valor_ativo
                                            THEN 'D'
                                        END AS superavit_deficit
                            , CASE WHEN valor_ativo > valor_passivo
                                   THEN valor_ativo - valor_passivo
                                   WHEN valor_passivo > valor_ativo
                                   THEN valor_passivo - valor_ativo
                                   ELSE 0.00
                               END AS vl_apurado
                         FROM (
                                SELECT SUM(valor_ativo) AS valor_ativo
					                 , SUM(valor_passivo) AS valor_passivo
                                  FROM (
                                        SELECT recurso.cod_recurso
                                             , recurso.nom_recurso
                                             , COALESCE(valor_ativo,0.00) AS valor_ativo
                                             , COALESCE(valor_passivo,0.00) AS valor_passivo
                                          FROM orcamento.recurso('".($this->getDado('exercicio')-1)."')
                                     LEFT JOIN (
                                                SELECT cod_recurso
                                                     , COALESCE(SUM(valor),0.00) AS valor_ativo
                                                  FROM (
                                                        SELECT cod_recurso
                                                             , COALESCE(vl_saldo_atual,0.00) AS valor
                                                          FROM contabilidade.fn_rl_apuracao_superavit_defict_conta_banco
                                                             ( '".($this->getDado('exercicio')-1)."'
                                                             , ' cod_entidade IN ( ".$this->getDado('entidade')." ) AND cod_estrutural NOT ILIKE (''1.1.3.8%'')  AND indicador_superavit = ''financeiro'' '
                                                             , '01/01/".($this->getDado('exercicio')-1)."'
                                                             , '31/12/".($this->getDado('exercicio')-1)."'
                                                             , 'A'::CHAR
                                                             )
                                                            AS retorno
                                                             ( cod_recurso         integer
                                                             , indicador_superavit char(12)
                                                             , vl_saldo_anterior   numeric
                                                             , vl_saldo_debitos    numeric
                                                             , vl_saldo_creditos   numeric
                                                             , vl_saldo_atual      numeric
                                                             )
                                                         WHERE indicador_superavit = 'financeiro'
                                                         UNION
                                                        SELECT cod_recurso
                                                             , COALESCE(SUM(vl_saldo_atual),0.00) AS valor
                                                          FROM contabilidade.fn_rl_apuracao_superavit_defict_recurso
                                                             ( '".($this->getDado('exercicio')-1)."'
                                                             , ' cod_entidade IN ( ".$this->getDado('entidade')." ) AND cod_estrutural ILIKE (''1.1.3.8%'')  AND indicador_superavit = ''financeiro'' '
                                                             , '01/01/".($this->getDado('exercicio')-1)."'
                                                             , '31/12/".($this->getDado('exercicio')-1)."'
                                                             , 'A'::CHAR
                                                             )
                                                            AS retorno
                                                             ( cod_recurso         integer
                                                             , indicador_superavit char(12)
                                                             , vl_saldo_anterior   numeric
                                                             , vl_saldo_debitos    numeric
                                                             , vl_saldo_creditos   numeric
                                                             , vl_saldo_atual      numeric
                                                             )
                                                         WHERE indicador_superavit = 'financeiro'
                                                  GROUP BY cod_recurso
                                                  ORDER BY cod_recurso
                                                       ) AS execucao
                                              GROUP BY execucao.cod_recurso
                                              ORDER BY execucao.cod_recurso
                                               ) AS ativo
                                            ON ativo.cod_recurso = recurso.cod_recurso

                                     LEFT JOIN (
                                                SELECT cod_recurso
                                                     , COALESCE(SUM(valor),0.00) AS valor_passivo
                                                  FROM (
                                                        SELECT cod_recurso
                                                             , COALESCE(SUM(valor),0.00) AS valor
                                                          FROM contabilidade.apuracao_empenho_superavit_deficit
                                                             ( '".($this->getDado('exercicio')-1)."'
                                                             , '01/01/'
                                                             , '31/12/".($this->getDado('exercicio')-1)."'
                                                             , '".$this->getDado('entidade')."'
                                                             )
                                                             as retorno
                                                             ( exercicio    CHAR(4)
                                                             , cod_empenho     TEXT
                                                             , cod_entidade INTEGER
                                                             , cod_recurso  INTEGER
                                                             , valor        NUMERIC
                                                             )
                                                      GROUP BY cod_recurso
                                                         UNION 
                                                        SELECT cod_recurso
                                                             , ABS(COALESCE(SUM(vl_saldo_atual),0.00)) AS valor
                                                          FROM contabilidade.fn_rl_apuracao_superavit_defict_recurso
                                                               ( '".($this->getDado('exercicio')-1)."'
                                                               , ' cod_entidade IN ( ".$this->getDado('entidade')." ) AND cod_estrutural ILIKE ''2.1.8%'' AND indicador_superavit = ''financeiro'' '
                                                               , '01/01/".($this->getDado('exercicio')-1)."'
                                                               , '31/12/".($this->getDado('exercicio')-1)."'
                                                               , 'A'::CHAR
                                                               )
                                                               AS retorno
                                                               ( cod_recurso      integer
                                                               , indicador_superavit char(12)
                                                               , vl_saldo_anterior   numeric
                                                               , vl_saldo_debitos    numeric
                                                               , vl_saldo_creditos   numeric
                                                               , vl_saldo_atual      numeric
                                                               )
                                                         WHERE indicador_superavit = 'financeiro'
                                                      GROUP BY cod_recurso
                                                       ) AS execucao
                                                GROUP BY execucao.cod_recurso
                                                ORDER BY execucao.cod_recurso
                                               ) AS passivo
                                            ON passivo.cod_recurso = recurso.cod_recurso
                                      ORDER BY recurso.cod_recurso ASC
                                       ) AS execucao
                                 WHERE NOT (valor_ativo = 0.00 AND valor_passivo = 0.00)
                              ) AS registro10
                    ";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosSUPDEF11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosSUPDEF11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;
        $stSql = $this->montaRecuperaDadosSUPDEF11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosSUPDEF11()
    {
        $stSql  = "SELECT 11 AS tiporegistro
                        , LPAD(SUBSTR(cod_recurso::varchar,2,3),3,'2') AS cod_recurso
                        , CASE WHEN valor_ativo > valor_passivo
                               THEN 'S'
                               WHEN valor_passivo > valor_ativo
                               THEN 'D'
                           END AS superavit_deficit
                        , CASE WHEN valor_ativo > valor_passivo
                               THEN valor_ativo - valor_passivo
                               WHEN valor_passivo > valor_ativo
                               THEN valor_passivo - valor_ativo
                               ELSE 0.00
                           END AS vl_apurado
                     FROM (
                           SELECT recurso.cod_recurso
                                , COALESCE(SUM(valor_ativo),0.00) AS valor_ativo
                                , COALESCE(SUM(valor_passivo),0.00) AS valor_passivo
                             FROM orcamento.recurso('".($this->getDado('exercicio')-1)."')
                        LEFT JOIN (
                                   SELECT cod_recurso
                                        , COALESCE(SUM(valor),0.00) AS valor_ativo
                                     FROM (
                                           SELECT cod_recurso
                                                , COALESCE(vl_saldo_atual,0.00) AS valor
                                             FROM contabilidade.fn_rl_apuracao_superavit_defict_conta_banco
                                                ( '".($this->getDado('exercicio')-1)."'
                                                , ' cod_entidade IN ( ".$this->getDado('entidade').") AND cod_estrutural NOT ILIKE (''1.1.3.8%'')  AND indicador_superavit = ''financeiro'' '
                                                , '01/01/".($this->getDado('exercicio')-1)."'
                                                , '31/12/".($this->getDado('exercicio')-1)."'
                                                , 'A'::CHAR
                                                )
                                               AS retorno
                                                ( cod_recurso         integer
                                                , indicador_superavit char(12)
                                                , vl_saldo_anterior   numeric
                                                , vl_saldo_debitos    numeric
                                                , vl_saldo_creditos   numeric
                                                , vl_saldo_atual      numeric
                                                )
                                            WHERE indicador_superavit = 'financeiro'
                                            UNION
                                           SELECT cod_recurso
                                                , COALESCE(SUM(vl_saldo_atual),0.00) AS valor
                                             FROM contabilidade.fn_rl_apuracao_superavit_defict_recurso
                                                ( '".($this->getDado('exercicio')-1)."'
                                                , ' cod_entidade IN ( ".$this->getDado('entidade').") AND cod_estrutural ILIKE (''1.1.3.8%'')  AND indicador_superavit = ''financeiro'' '
                                                , '01/01/".($this->getDado('exercicio')-1)."'
                                                , '31/12/".($this->getDado('exercicio')-1)."'
                                                , 'A'::CHAR
                                                )
                                               AS retorno
                                                ( cod_recurso         integer
                                                , indicador_superavit char(12)
                                                , vl_saldo_anterior   numeric
                                                , vl_saldo_debitos    numeric
                                                , vl_saldo_creditos   numeric
                                                , vl_saldo_atual      numeric
                                                )
                                            WHERE indicador_superavit = 'financeiro'
                                     GROUP BY cod_recurso
                                     ORDER BY cod_recurso
                                          ) AS execucao
                                 GROUP BY execucao.cod_recurso
                                 ORDER BY execucao.cod_recurso
                                  ) AS ativo
                               ON ativo.cod_recurso = recurso.cod_recurso

                        LEFT JOIN (
                                   SELECT cod_recurso
                                        , COALESCE(SUM(valor),0.00) AS valor_passivo
                                     FROM (
                                           SELECT cod_recurso
                                                , COALESCE(SUM(valor),0.00) AS valor
                                             FROM contabilidade.apuracao_empenho_superavit_deficit
                                                ( '".($this->getDado('exercicio')-1)."'
                                                , '01/01/'
                                                , '31/12/".($this->getDado('exercicio')-1)."'
                                                , '".$this->getDado('entidade')."'
                                                )
                                                as retorno
                                                ( exercicio    CHAR(4)
                                                , cod_empenho     TEXT
                                                , cod_entidade INTEGER
                                                , cod_recurso  INTEGER
                                                , valor        NUMERIC
                                                )
                                         GROUP BY cod_recurso
                                            UNION 
                                           SELECT cod_recurso
                                                , ABS(COALESCE(SUM(vl_saldo_atual),0.00)) AS valor
                                             FROM contabilidade.fn_rl_apuracao_superavit_defict_recurso
                                                  ( '".($this->getDado('exercicio')-1)."'
                                                  , ' cod_entidade IN ( ".$this->getDado('entidade').") AND cod_estrutural ILIKE ''2.1.8%'' AND indicador_superavit = ''financeiro'' '
                                                  , '01/01/".($this->getDado('exercicio')-1)."'
                                                  , '31/12/".($this->getDado('exercicio')-1)."'
                                                  , 'A'::CHAR
                                                  )
                                                  AS retorno
                                                  ( cod_recurso      integer
                                                  , indicador_superavit char(12)
                                                  , vl_saldo_anterior   numeric
                                                  , vl_saldo_debitos    numeric
                                                  , vl_saldo_creditos   numeric
                                                  , vl_saldo_atual      numeric
                                                  )
                                            WHERE indicador_superavit = 'financeiro'
                                         GROUP BY cod_recurso
                                          ) AS execucao
                                   GROUP BY execucao.cod_recurso
                                   ORDER BY execucao.cod_recurso
                                  ) AS passivo
                               ON passivo.cod_recurso = recurso.cod_recurso
                               GROUP BY recurso.cod_recurso
                         ORDER BY recurso.cod_recurso ASC
                          ) AS execucao
                    WHERE NOT (valor_ativo = 0.00 AND valor_passivo = 0.00) ";

        return $stSql;
    }

    public function __destruct(){}

}
