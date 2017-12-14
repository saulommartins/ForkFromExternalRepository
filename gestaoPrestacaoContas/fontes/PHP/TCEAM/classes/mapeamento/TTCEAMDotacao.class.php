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

    $Id: TTCEAMDotacao.class.php 59612 2014-09-02 12:00:51Z gelson $
    
    * @ignore
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEAMDotacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTPBDotacao()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSQL  = " SELECT  despesa.cod_despesa                                                            \n";
        $stSQL .= "        ,SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,1) AS categoria_economica\n";
        $stSQL .= "        ,SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),2,1) AS natureza           \n";
        $stSQL .= "        ,SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),3,2) AS modalidade         \n";
        $stSQL .= "        ,SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),5,2) AS elemento           \n";
        $stSQL .= "        ,despesa.num_orgao||LPAD(despesa.num_unidade::text,2,'0') AS unidade            \n";
        $stSQL .= "        ,despesa.cod_funcao                                                             \n";
        $stSQL .= "        ,despesa.cod_subfuncao                                                          \n";
        $stSQL .= "        ,programa.num_programa                                                          \n";
        $stSQL .= "        ,acao.num_acao                                                                  \n";
        $stSQL .= "        ,recurso.cod_fonte                                                              \n";
        $stSQL .= "        ,SUBSTR(despesa.num_pao::text,1,1) AS cod_tipo                                  \n";
        $stSQL .= "        ,despesa.vl_original                                                            \n";
        $stSQL .= "   FROM orcamento.despesa                                                               \n";
        $stSQL .= "   JOIN orcamento.conta_despesa                                                         \n";
        $stSQL .= "     ON despesa.exercicio = conta_despesa.exercicio                                     \n";
        $stSQL .= "    AND despesa.cod_conta = conta_despesa.cod_conta                                     \n";
        $stSQL .= "                                                                                        \n";
        $stSQL .= "   JOIN orcamento.recurso                                                               \n";
        $stSQL .= "     ON recurso.exercicio = despesa.exercicio                                           \n";
        $stSQL .= "    AND recurso.cod_recurso = despesa.cod_recurso                                       \n";
        $stSQL .= "                                                                                        \n";
        $stSQL .= "   JOIN orcamento.pao                                                                   \n";
        $stSQL .= "     ON pao.exercicio = despesa.exercicio                                               \n";
        $stSQL .= "    AND pao.num_pao = despesa.num_pao                                                   \n";
        $stSQL .= "    AND despesa.vl_original > 0                                                         \n";
        $stSQL .= "    AND despesa.exercicio = '".$this->getDado('exercicio')."'                           \n";
        $stSQL .= "    AND despesa.cod_entidade in (".$this->getDado('entidades').")                       \n";
        $stSQL .= "                                                                                         \n";
        $stSQL .= "   JOIN  orcamento.pao_ppa_acao                                                          \n";
        $stSQL .= "     ON  pao_ppa_acao.exercicio=despesa.exercicio                                        \n";
        $stSQL .= "    AND  pao_ppa_acao.num_pao=despesa.num_pao                                            \n";
        $stSQL .= "                                                                                         \n";
        $stSQL .= "   JOIN  ppa.acao                                                                        \n";
        $stSQL .= "     ON  acao.cod_acao=pao_ppa_acao.cod_acao                                             \n";
        $stSQL .= "                                                                                         \n";
        $stSQL .= "   JOIN  orcamento.programa_ppa_programa                                                 \n";
        $stSQL .= "     ON  programa_ppa_programa.exercicio=despesa.exercicio                               \n";
        $stSQL .= "    AND  programa_ppa_programa.cod_programa=despesa.cod_programa                         \n";
        $stSQL .= "                                                                                         \n";
        $stSQL .= "   JOIN  ppa.programa                                                                    \n";
        $stSQL .= "     ON  programa.cod_programa=programa_ppa_programa.cod_programa_ppa                    \n";

        return $stSQL;
    }

function recuperaAtualizacaoOrcamentaria(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtualizacaoOrcamentaria().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaAtualizacaoOrcamentaria()
{
    $stSql = "
    (
          /* suplementacoes */

              SELECT  despesa.exercicio
                   ,  LPAD(despesa.num_orgao::varchar, 3, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento
                   ,  CASE WHEN suplementacao.cod_tipo IN (1,2,3,4,5,12,13,14,15)
                            AND despesa.vl_original    = 0
                            AND count(suplementacao_suplementada.cod_despesa) = 1  THEN 7
                           WHEN suplementacao.cod_tipo IN (6,7,8,9,10,11) THEN 2
                           WHEN suplementacao.cod_tipo = 11 THEN 3
                           ELSE 1
                      END AS tipo_suplementacao
                   ,  SUM(suplementacao_suplementada.valor) as valor
                   ,  ( LPAD(norma.num_norma,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') ) AS num_norma
                   ,  recurso.cod_fonte
                   ,  CASE WHEN norma.cod_tipo_norma = 0
                           THEN 9
                           WHEN norma.cod_tipo_norma = 1
                           THEN 5
                           WHEN norma.cod_tipo_norma = 2
                           THEN 1
                           WHEN norma.cod_tipo_norma = 3
                           THEN 9
                           WHEN norma.cod_tipo_norma = 4
                           THEN 2
                           ELSE 9
                      END AS cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1) as cod_tipo
                   ,  to_char(norma.dt_publicacao,'yyyymmdd') as data_vigencia
                FROM  orcamento.despesa
          INNER JOIN  orcamento.conta_despesa
                  ON  conta_despesa.exercicio = despesa.exercicio
                 AND  conta_despesa.cod_conta = despesa.cod_conta
          INNER JOIN  orcamento.suplementacao_suplementada
                  ON  suplementacao_suplementada.exercicio = despesa.exercicio
                 AND  suplementacao_suplementada.cod_despesa = despesa.cod_despesa
          INNER JOIN  orcamento.suplementacao
                  ON  suplementacao.exercicio = suplementacao_suplementada.exercicio
                 AND  suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
          INNER JOIN  normas.norma
                 ON   suplementacao.cod_norma = norma.cod_norma
          INNER JOIN  orcamento.recurso
                  ON  recurso.exercicio = despesa.exercicio
                 AND  recurso.cod_recurso = despesa.cod_recurso
                 
          INNER JOIN  orcamento.pao_ppa_acao
                  ON  pao_ppa_acao.exercicio=despesa.exercicio
                 AND  pao_ppa_acao.num_pao=despesa.num_pao

          INNER JOIN  ppa.acao
                  ON  acao.cod_acao=pao_ppa_acao.cod_acao
                  
          INNER JOIN  orcamento.programa_ppa_programa
                  ON  programa_ppa_programa.exercicio=despesa.exercicio
                 AND  programa_ppa_programa.cod_programa=despesa.cod_programa

          INNER JOIN  ppa.programa
                  ON  programa.cod_programa=programa_ppa_programa.cod_programa_ppa
                 
               WHERE  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                 )
                 AND  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 )
                 AND  despesa.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa.cod_entidade in (".$this->getDado('stEntidades').")
                 AND  TO_CHAR(suplementacao.dt_suplementacao, 'mm') = '".$this->getDado('inMes')."'
                 AND  suplementacao_suplementada.valor > 0
            GROUP BY  despesa.exercicio
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  despesa.vl_original
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)
                   ,  suplementacao.cod_tipo
                   ,  suplementacao_suplementada.cod_despesa
                   ,  norma.num_norma
                   ,  TO_CHAR(norma.dt_assinatura,'yyyy')
                   ,  recurso.cod_fonte
                   ,  norma.cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1)
                   ,  norma.dt_publicacao
              UNION ALL
          /* reducoes */

              SELECT  despesa.exercicio
                   ,  LPAD(despesa.num_orgao::varchar, 3, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento
                   ,  4 AS tipo_suplementacao
                   ,  SUM(suplementacao_reducao.valor) AS valor
                   ,  ( LPAD(norma.num_norma,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') ) AS num_norma
                   ,  recurso.cod_fonte
                   ,  CASE WHEN norma.cod_tipo_norma = 0
                           THEN 9
                           WHEN norma.cod_tipo_norma = 1
                           THEN 5
                           WHEN norma.cod_tipo_norma = 2
                           THEN 1
                           WHEN norma.cod_tipo_norma = 3
                           THEN 9
                           WHEN norma.cod_tipo_norma = 4
                           THEN 2
                           ELSE 9
                      END AS cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1) as cod_tipo
                   ,  to_char(norma.dt_publicacao,'yyyymmdd') as data_vigencia
                FROM  orcamento.despesa
          INNER JOIN  orcamento.conta_despesa
                  ON  conta_despesa.exercicio = despesa.exercicio
                 AND  conta_despesa.cod_conta = despesa.cod_conta
          INNER JOIN  orcamento.suplementacao_reducao
                  ON  suplementacao_reducao.exercicio = despesa.exercicio
                 AND  suplementacao_reducao.cod_despesa = despesa.cod_despesa
          INNER JOIN  orcamento.suplementacao
                  ON  suplementacao.exercicio = suplementacao_reducao.exercicio
                 AND  suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
          INNER JOIN  normas.norma
                 ON   suplementacao.cod_norma = norma.cod_norma
          INNER JOIN  orcamento.recurso
                  ON  recurso.exercicio = despesa.exercicio
                 AND  recurso.cod_recurso = despesa.cod_recurso
                 
          INNER JOIN  orcamento.pao_ppa_acao
                  ON  pao_ppa_acao.exercicio=despesa.exercicio
                 AND  pao_ppa_acao.num_pao=despesa.num_pao

          INNER JOIN  ppa.acao
                  ON  acao.cod_acao=pao_ppa_acao.cod_acao
                  
          INNER JOIN  orcamento.programa_ppa_programa
                  ON  programa_ppa_programa.exercicio=despesa.exercicio
                 AND  programa_ppa_programa.cod_programa=despesa.cod_programa

          INNER JOIN  ppa.programa
                  ON  programa.cod_programa=programa_ppa_programa.cod_programa_ppa                  
                 
               WHERE  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                 )
                 AND  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 )
                 AND  despesa.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa.cod_entidade in (".$this->getDado('stEntidades').")
                 AND  TO_CHAR(suplementacao.dt_suplementacao, 'mm') = '".$this->getDado('inMes')."'
                 AND  suplementacao_reducao.valor > 0
            GROUP BY  despesa.exercicio
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)
                   ,  suplementacao.cod_tipo
                   ,  norma.num_norma
                   ,  TO_CHAR(norma.dt_assinatura,'yyyy')
                   ,  recurso.cod_fonte
                   ,  norma.cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1)
                   ,  norma.dt_publicacao
      )
    ";

    if ($this->getDado('boIncorporar')) {
        $stSql .= " UNION
            (
 /* suplementacoes */

              SELECT  despesa.exercicio
                   ,  LPAD(despesa.num_orgao::varchar, 3, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento
                   ,  CASE WHEN suplementacao.cod_tipo IN (1,2,3,4,5,12,13,14,15)
                            AND despesa.vl_original    = 0
                            AND count(suplementacao_suplementada.cod_despesa) = 1  THEN 7
                           WHEN suplementacao.cod_tipo IN (6,7,8,9,10,11) THEN 2
                           WHEN suplementacao.cod_tipo = 11 THEN 3
                           ELSE 1
                      END AS tipo_suplementacao
                   ,  SUM(suplementacao_suplementada.valor) as valor
                   ,  ( LPAD(norma.num_norma,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') ) AS num_norma
                   ,  recurso.cod_fonte
                   ,  CASE WHEN norma.cod_tipo_norma = 0
                           THEN 9
                           WHEN norma.cod_tipo_norma = 1
                           THEN 5
                           WHEN norma.cod_tipo_norma = 2
                           THEN 1
                           WHEN norma.cod_tipo_norma = 3
                           THEN 9
                           WHEN norma.cod_tipo_norma = 4
                           THEN 2
                           ELSE 9
                      END AS cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1) as cod_tipo
                   ,  EXTRACT(year from norma.dt_publicacao)||'1231' as data_vigencia
                FROM  orcamento.despesa
          INNER JOIN  orcamento.conta_despesa
                  ON  conta_despesa.exercicio = despesa.exercicio
                 AND  conta_despesa.cod_conta = despesa.cod_conta
          INNER JOIN  orcamento.suplementacao_suplementada
                  ON  suplementacao_suplementada.exercicio = despesa.exercicio
                 AND  suplementacao_suplementada.cod_despesa = despesa.cod_despesa
          INNER JOIN  orcamento.suplementacao
                  ON  suplementacao.exercicio = suplementacao_suplementada.exercicio
                 AND  suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao
          INNER JOIN  normas.norma
                  ON  suplementacao.cod_norma = norma.cod_norma
          INNER JOIN  orcamento.recurso
                  ON  recurso.exercicio = despesa.exercicio
                 AND  recurso.cod_recurso = despesa.cod_recurso
                 
          INNER JOIN  orcamento.pao_ppa_acao
                  ON  pao_ppa_acao.exercicio=despesa.exercicio
                 AND  pao_ppa_acao.num_pao=despesa.num_pao

          INNER JOIN  ppa.acao
                  ON  acao.cod_acao=pao_ppa_acao.cod_acao
                  
          INNER JOIN  orcamento.programa_ppa_programa
                  ON  programa_ppa_programa.exercicio=despesa.exercicio
                 AND  programa_ppa_programa.cod_programa=despesa.cod_programa

          INNER JOIN  ppa.programa
                  ON  programa.cod_programa=programa_ppa_programa.cod_programa_ppa                  
                 
               WHERE  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                 )
                 AND  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 )
                 AND  despesa.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa.cod_entidade in (".$this->getDado('stCodEntidadesIncorporar').")
                 AND  suplementacao_suplementada.valor > 0
            GROUP BY  despesa.exercicio
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  despesa.vl_original
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)
                   ,  suplementacao.cod_tipo
                   ,  suplementacao_suplementada.cod_despesa
                   ,  norma.num_norma
                   ,  TO_CHAR(norma.dt_assinatura,'yyyy')
                   ,  recurso.cod_fonte
                   ,  cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1)
                   ,  data_vigencia
              UNION ALL
          /* reducoes */

              SELECT  despesa.exercicio
                   ,  LPAD(despesa.num_orgao::varchar, 3, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento
                   ,  4 AS tipo_suplementacao
                   ,  SUM(suplementacao_reducao.valor) AS valor
                   ,  ( LPAD(norma.num_norma,4,'0') || TO_CHAR(norma.dt_assinatura,'yyyy') ) AS num_norma
                   ,  recurso.cod_fonte
                   ,  CASE WHEN norma.cod_tipo_norma = 0
                           THEN 9
                           WHEN norma.cod_tipo_norma = 1
                           THEN 5
                           WHEN norma.cod_tipo_norma = 2
                           THEN 1
                           WHEN norma.cod_tipo_norma = 3
                           THEN 9
                           WHEN norma.cod_tipo_norma = 4
                           THEN 2
                           ELSE 9
                      END AS cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1) as cod_tipo
                   ,  to_char(norma.dt_publicacao,'yyyy')||'1231' as data_vigencia
                FROM  orcamento.despesa
          INNER JOIN  orcamento.conta_despesa
                  ON  conta_despesa.exercicio = despesa.exercicio
                 AND  conta_despesa.cod_conta = despesa.cod_conta
          INNER JOIN  orcamento.suplementacao_reducao
                  ON  suplementacao_reducao.exercicio = despesa.exercicio
                 AND  suplementacao_reducao.cod_despesa = despesa.cod_despesa
          INNER JOIN  orcamento.suplementacao
                  ON  suplementacao.exercicio = suplementacao_reducao.exercicio
                 AND  suplementacao.cod_suplementacao = suplementacao_reducao.cod_suplementacao
          INNER JOIN  normas.norma
                 ON   suplementacao.cod_norma = norma.cod_norma
          INNER JOIN  orcamento.recurso
                  ON  recurso.exercicio = despesa.exercicio
                 AND  recurso.cod_recurso = despesa.cod_recurso
                 
          INNER JOIN  orcamento.pao_ppa_acao
                  ON  pao_ppa_acao.exercicio=despesa.exercicio
                 AND  pao_ppa_acao.num_pao=despesa.num_pao

          INNER JOIN  ppa.acao
                  ON  acao.cod_acao=pao_ppa_acao.cod_acao
                  
          INNER JOIN  orcamento.programa_ppa_programa
                  ON  programa_ppa_programa.exercicio=despesa.exercicio
                 AND  programa_ppa_programa.cod_programa=despesa.cod_programa

          INNER JOIN  ppa.programa
                  ON  programa.cod_programa=programa_ppa_programa.cod_programa_ppa                  
                 
               WHERE  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                 )
                 AND  NOT EXISTS ( SELECT  1
                                     FROM  orcamento.suplementacao_anulada
                                    WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                      AND  suplementacao_anulada.cod_suplementacao_anulacao = suplementacao.cod_suplementacao
                                 )
                 AND  despesa.exercicio = '".$this->getDado('exercicio')."'
                 AND  despesa.cod_entidade in (".$this->getDado('stCodEntidadesIncorporar').")
                 AND  suplementacao_reducao.valor > 0
            GROUP BY  despesa.exercicio
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  programa.num_programa
                   ,  acao.num_acao
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2)
                   ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2)
                   ,  suplementacao.cod_tipo
                   ,  norma.num_norma
                   ,  TO_CHAR(norma.dt_assinatura,'yyyy')
                   ,  recurso.cod_fonte
                   ,  cod_tipo_norma
                   ,  SUBSTR(despesa.num_pao::varchar,1,1)
                   ,  data_vigencia
                   )
            ORDER BY data_vigencia
        ";
    }

    return $stSql;

}
}
