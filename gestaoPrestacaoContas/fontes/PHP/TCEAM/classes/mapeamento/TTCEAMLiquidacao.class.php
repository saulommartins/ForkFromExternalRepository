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
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class TTCEAMLiquidacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMLiquidacao()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "  SELECT * FROM (                                                                                                                                    \n";
        $stSql .= "   SELECT liquida_anula.cod_empenho::varchar                                                                                                         \n";
        $stSql .= "        , liquida_anula.exercicio                                            AS exercicio_empenho                                                    \n";
        $stSql .= "        , lpad(despesa.num_orgao::varchar, 3, '0')||lpad(despesa.num_unidade::varchar, 2, '0') AS unidade_orcamentaria                                                 \n";
        $stSql .= "        , to_char(liquida_anula.dt_liquidacao,'dd/mm/yyyy')                  AS dt_liquidacao                                                        \n";
        $stSql .= "        , SUM(liquida_anula.valor)                                           AS valor                                                                \n";
        $stSql .= "        , to_char(liquida_anula.dt_anulacao,'dd/mm/yyyy')                    AS dt_anulacao                                                          \n";
        $stSql .= "        , liquida_anula.numero_status_anulacao                                                                                                       \n";
        $stSql .= "        , liquida_anula.itens                                                                                                                        \n";
        $stSql .= "        , liquida_anula.anulados                                                                                                                     \n";
        $stSql .= "        , 0 as cod_empenho_incorporado                                                                                                               \n";
        $stSql .= "     FROM (                                                                                                                                          \n";
        $stSql .= "                SELECT empenho.cod_empenho                                                                                                           \n";
        $stSql .= "                     , empenho.exercicio                                                                                                             \n";
        $stSql .= "                     , empenho.cod_entidade                                                                                                          \n";
        $stSql .= "                     , nota_liquidacao.cod_nota                                                                                                      \n";
        $stSql .= "                     , nota_liquidacao.dt_liquidacao                                                                                                 \n";
        $stSql .= "                     , nota_liquidacao_item.num_item                                                                                                \n";
        $stSql .= "                     , CASE                                                                                                                          \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '".$this->getDado('inMes')."' THEN                                \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total)                                                                                      \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '".$this->getDado('inMes')."' THEN                 \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total)                                                                                      \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN                                          \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total - nota_liquidacao_item_anulado.vl_anulado)                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) > 0                                THEN                                          \n";
        $stSql .= "                             0.00                                                                                                                    \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total)                                                                                      \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total)                                                                                      \n";
        $stSql .= "                          ELSE                                                                                                                       \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total)                                                                                      \n";
        $stSql .= "                       END                                  AS valor                                                                                 \n";
        $stSql .= "                     , CASE                                                                                                                          \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '".$this->getDado('inMes')."' THEN                                \n";
        $stSql .= "                             NULL                                                                                                                    \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '".$this->getDado('inMes')."' THEN                 \n";
        $stSql .= "                             nota_liquidacao_item_anulado.timestamp::date                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN                                          \n";
        $stSql .= "                             nota_liquidacao_item_anulado.timestamp::date                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) > 0                                THEN                                          \n";
        $stSql .= "                             NULL                                                                                                                    \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             nota_liquidacao_item_anulado.timestamp::date                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             nota_liquidacao_item_anulado.timestamp::date                                                                            \n";
        $stSql .= "                          ELSE                                                                                                                       \n";
        $stSql .= "                             NULL                                                                                                                    \n";
        $stSql .= "                       END                                  AS dt_anulacao                                                                           \n";
        $stSql .= "                     , CASE                                                                                                                          \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '".$this->getDado('inMes')."' THEN                                \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '".$this->getDado('inMes')."' THEN                 \n";
        $stSql .= "                             1                                                                                                                       \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             1                                                                                                                       \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             1                                                                                                                       \n";
        $stSql .= "                          ELSE                                                                                                                       \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                       END                                  AS numero_status_anulacao                                                                \n";
        $stSql .= "                     , mandrake.itens                                                                                                                \n";
        $stSql .= "                     , mandrake.anulados                                                                                                             \n";
        $stSql .= "                  FROM empenho.empenho                                                                                                               \n";
        $stSql .= "            INNER JOIN empenho.nota_liquidacao                                                                                                       \n";
        $stSql .= "                    ON empenho.exercicio    = nota_liquidacao.exercicio_empenho                                                                      \n";
        $stSql .= "                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade                                                                           \n";
        $stSql .= "                   AND empenho.cod_empenho  = nota_liquidacao.cod_empenho                                                                            \n";
        $stSql .= "            INNER JOIN empenho.nota_liquidacao_item                                                                                                  \n";
        $stSql .= "                    ON nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio                                                                 \n";
        $stSql .= "                   AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota                                                                  \n";
        $stSql .= "                   AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade                                                              \n";
        $stSql .= "             LEFT JOIN empenho.nota_liquidacao_item_anulado                                                                                          \n";
        $stSql .= "                    ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio                                                 \n";
        $stSql .= "                   AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota                                                  \n";
        $stSql .= "                   AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item                                                  \n";
        $stSql .= "                   AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item                                            \n";
        $stSql .= "                   AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho                                           \n";
        $stSql .= "                   AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade                                              \n";
        $stSql .= "            INNER JOIN (                                                                                                                             \n";
        $stSql .= "                             SELECT nota_liquidacao.cod_empenho                                                                                      \n";
        $stSql .= "                                  , nota_liquidacao.dt_liquidacao                                                                                    \n";
        $stSql .= "                                  , COUNT(nota_liquidacao_item.cod_nota)             AS itens                                                        \n";
        $stSql .= "                                  , COUNT(nota_liquidacao_item_anulado.cod_nota)     AS anulados                                                     \n";
        $stSql .= "                               FROM empenho.nota_liquidacao                                                                                          \n";
        $stSql .= "                         INNER JOIN empenho.nota_liquidacao_item                                                                                     \n";
        $stSql .= "                                 ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio                                                    \n";
        $stSql .= "                                AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade                                                 \n";
        $stSql .= "                                AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota                                                     \n";
        $stSql .= "                          LEFT JOIN empenho.nota_liquidacao_item_anulado                                                                             \n";
        $stSql .= "                                 ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio                                    \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade                                 \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota                                     \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item                                     \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item                               \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho                              \n";
        $stSql .= "                           GROUP BY nota_liquidacao.cod_empenho                                                                                      \n";
        $stSql .= "                                  , nota_liquidacao.dt_liquidacao                                                                                    \n";
        $stSql .= "                       )                                     AS mandrake                                                                             \n";
        $stSql .= "                    ON mandrake.cod_empenho   = empenho.cod_empenho                                                                                  \n";
        $stSql .= "                   AND mandrake.dt_liquidacao = nota_liquidacao.dt_liquidacao                                                                        \n";
        $stSql .= "              -- WHERE EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp) = '".$this->getDado('inMes')."'                                    \n";
        $stSql .= "              --    OR nota_liquidacao_item_anulado.timestamp IS NULL                                                                                \n";
        $stSql .= "              GROUP BY empenho.cod_empenho                                                                                                           \n";
        $stSql .= "                     , empenho.exercicio                                                                                                             \n";
        $stSql .= "                     , empenho.cod_entidade                                                                                                          \n";
        $stSql .= "                     , nota_liquidacao.cod_nota                                                                                                      \n";
        $stSql .= "                     , nota_liquidacao.dt_liquidacao                                                                                                 \n";
        $stSql .= "                     , nota_liquidacao_item.num_item                                                                                                 \n";
        $stSql .= "                     , nota_liquidacao_item_anulado.timestamp                                                                                        \n";
        $stSql .= "                     , nota_liquidacao_item_anulado.vl_anulado                                                                                       \n";
        $stSql .= "                     , nota_liquidacao_item.vl_total                                                                                                 \n";
        $stSql .= "                     , mandrake.itens                                                                                                                \n";
        $stSql .= "                     , mandrake.anulados                                                                                                             \n";
        $stSql .= "             UNION                                                                                                                                   \n";
        $stSql .= "                SELECT empenho.cod_empenho                                                                                                           \n";
        $stSql .= "                     , empenho.exercicio                                                                                                             \n";
        $stSql .= "                     , empenho.cod_entidade                                                                                                          \n";
        $stSql .= "                     , nota_liquidacao.cod_nota                                                                                                      \n";
        $stSql .= "                     , nota_liquidacao.dt_liquidacao                                                                                                 \n";
                $stSql .= "                     , nota_liquidacao_item.num_item                                                                                         \n";
        $stSql .= "                     , CASE                                                                                                                          \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '".$this->getDado('inMes')."' THEN                                \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '".$this->getDado('inMes')."' THEN                 \n";
        $stSql .= "                             SUM(nota_liquidacao_item_anulado.vl_anulado)                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN                                          \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total - nota_liquidacao_item_anulado.vl_anulado)                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) > 0                                THEN                                          \n";
        $stSql .= "                             0.00                                                                                                                    \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             SUM(nota_liquidacao_item_anulado.vl_anulado)                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) = 0                                THEN                                          \n";
        $stSql .= "                             SUM(nota_liquidacao_item_anulado.vl_anulado)                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao_item_anulado.vl_anulado IS NULL THEN                                                                  \n";
        $stSql .= "                             SUM(nota_liquidacao_item.vl_total)                                                                                      \n";
        $stSql .= "                          ELSE                                                                                                                       \n";
        $stSql .= "                             SUM(nota_liquidacao_item_anulado.vl_anulado)                                                                            \n";
        $stSql .= "                       END                                           AS valor                                                                        \n";
        $stSql .= "                     , CASE                                                                                                                          \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '".$this->getDado('inMes')."' THEN                                \n";
        $stSql .= "                             NULL                                                                                                                    \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '".$this->getDado('inMes')."' THEN                 \n";
        $stSql .= "                             nota_liquidacao_item_anulado.timestamp::date                                                                            \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) > 0                                THEN                                          \n";
        $stSql .= "                             NULL                                                                                                                    \n";
        $stSql .= "                          ELSE                                                                                                                       \n";
        $stSql .= "                             nota_liquidacao_item_anulado.timestamp::date                                                                            \n";
        $stSql .= "                       END                                           AS dt_anulacao                                                                  \n";
        $stSql .= "                     , CASE                                                                                                                          \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '".$this->getDado('inMes')."' THEN                                \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                          WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)  \n";
        $stSql .= "                           AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '".$this->getDado('inMes')."' THEN                 \n";
        $stSql .= "                             1                                                                                                                       \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN                                          \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                          WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date                                          \n";
        $stSql .= "                           AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total                                               \n";
        $stSql .= "                           AND (mandrake.itens - mandrake.anulados) > 0                                THEN                                          \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                          WHEN nota_liquidacao_item_anulado.vl_anulado IS NULL THEN                                                                  \n";
        $stSql .= "                             0                                                                                                                       \n";
        $stSql .= "                          ELSE                                                                                                                       \n";
        $stSql .= "                             1                                                                                                                       \n";
        $stSql .= "                       END                                  AS numero_status_anulacao                                                                \n";
        $stSql .= "                     , mandrake.itens                                                                                                                \n";
        $stSql .= "                     , mandrake.anulados                                                                                                             \n";
        $stSql .= "                  FROM empenho.empenho                                                                                                               \n";
        $stSql .= "            INNER JOIN empenho.nota_liquidacao                                                                                                       \n";
        $stSql .= "                    ON empenho.exercicio    = nota_liquidacao.exercicio_empenho                                                                      \n";
        $stSql .= "                   AND empenho.cod_entidade = nota_liquidacao.cod_entidade                                                                           \n";
        $stSql .= "                   AND empenho.cod_empenho  = nota_liquidacao.cod_empenho                                                                            \n";
        $stSql .= "            INNER JOIN empenho.nota_liquidacao_item                                                                                                  \n";
        $stSql .= "                    ON nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio                                                                 \n";
        $stSql .= "                   AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota                                                                  \n";
        $stSql .= "                   AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade                                                              \n";
        $stSql .= "             LEFT JOIN empenho.nota_liquidacao_item_anulado                                                                                          \n";
        $stSql .= "                    ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio                                                 \n";
        $stSql .= "                   AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota                                                  \n";
        $stSql .= "                   AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item                                                  \n";
        $stSql .= "                   AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item                                            \n";
        $stSql .= "                   AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho                                           \n";
        $stSql .= "                   AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade                                              \n";
        $stSql .= "            INNER JOIN (                                                                                                                             \n";
        $stSql .= "                             SELECT nota_liquidacao.cod_empenho                                                                                      \n";
        $stSql .= "                                  , nota_liquidacao.dt_liquidacao                                                                                    \n";
        $stSql .= "                                  , COUNT(nota_liquidacao_item.cod_nota)             AS itens                                                        \n";
        $stSql .= "                                  , COUNT(nota_liquidacao_item_anulado.cod_nota)     AS anulados                                                     \n";
        $stSql .= "                               FROM empenho.nota_liquidacao                                                                                          \n";
        $stSql .= "                         INNER JOIN empenho.nota_liquidacao_item                                                                                     \n";
        $stSql .= "                                 ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio                                                    \n";
        $stSql .= "                                AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade                                                 \n";
        $stSql .= "                                AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota                                                     \n";
        $stSql .= "                          LEFT JOIN empenho.nota_liquidacao_item_anulado                                                                             \n";
        $stSql .= "                                 ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio                                    \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade                                 \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota                                     \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item                                     \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item                               \n";
        $stSql .= "                                AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho                              \n";
        $stSql .= "                           GROUP BY nota_liquidacao.cod_empenho                                                                                      \n";
        $stSql .= "                                  , nota_liquidacao.dt_liquidacao                                                                                    \n";
        $stSql .= "                       )                                     AS mandrake                                                                             \n";
        $stSql .= "                    ON mandrake.cod_empenho   = empenho.cod_empenho                                                                                  \n";
        $stSql .= "                   AND mandrake.dt_liquidacao = nota_liquidacao.dt_liquidacao                                                                        \n";
        $stSql .= "              -- WHERE EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp) = '".$this->getDado('inMes')."'                                    \n";
        $stSql .= "              --    OR nota_liquidacao_item_anulado.timestamp IS NULL                                                                                \n";
        $stSql .= "              GROUP BY empenho.cod_empenho                                                                                                           \n";
        $stSql .= "                     , empenho.exercicio                                                                                                             \n";
        $stSql .= "                     , empenho.cod_entidade                                                                                                          \n";
        $stSql .= "                     , nota_liquidacao.cod_nota                                                                                                      \n";
        $stSql .= "                     , nota_liquidacao.dt_liquidacao                                                                                                 \n";
        $stSql .= "                     , nota_liquidacao_item.num_item                                                                                                \n";
        $stSql .= "                     , nota_liquidacao_item_anulado.timestamp                                                                                        \n";
        $stSql .= "                     , nota_liquidacao_item_anulado.vl_anulado                                                                                       \n";
        $stSql .= "                     , nota_liquidacao_item.vl_total                                                                                                 \n";
        $stSql .= "                     , mandrake.itens                                                                                                                \n";
        $stSql .= "                     , mandrake.anulados                                                                                                             \n";
        $stSql .= "          )  AS liquida_anula                                                                                                                        \n";
        $stSql .= " INNER JOIN empenho.empenho                                                                                                                          \n";
        $stSql .= "         ON empenho.cod_empenho = liquida_anula.cod_empenho                                                                                          \n";
        $stSql .= "        AND empenho.exercicio   = liquida_anula.exercicio                                                                                            \n";
        $stSql .= "        AND empenho.cod_entidade= liquida_anula.cod_entidade                                                                                         \n";
        $stSql .= " INNER JOIN empenho.pre_empenho                                                                                                                      \n";
        $stSql .= "         ON pre_empenho.exercicio       = empenho.exercicio                                                                                          \n";
        $stSql .= "        AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho                                                                                    \n";
        $stSql .= " INNER JOIN empenho.pre_empenho_despesa                                                                                                              \n";
        $stSql .= "         ON pre_empenho_despesa.exercicio       = pre_empenho.exercicio                                                                              \n";
        $stSql .= "        AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                                                                        \n";
        $stSql .= " INNER JOIN orcamento.despesa                                                                                                                        \n";
        $stSql .= "         ON despesa.exercicio   = pre_empenho_despesa.exercicio                                                                                      \n";
        $stSql .= "        AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa                                                                                    \n";
        $stSql .= "      WHERE liquida_anula.exercicio                              =  '".$this->getDado('exercicio')."'                                                \n";
        $stSql .= "        AND (                                                                                                                                        \n";
        $stSql .= "       (                                                                                                                                             \n";
        $stSql .= "        EXTRACT(month FROM liquida_anula.dt_liquidacao) = '".$this->getDado('inMes')."'                                                              \n";
        $stSql .= "        AND EXTRACT(month FROM liquida_anula.dt_anulacao) = '".$this->getDado('inMes')."'                                                            \n";
        $stSql .= "        )                                                                                                                                            \n";
        $stSql .= "        OR                                                                                                                                           \n";
        $stSql .= "        (                                                                                                                                            \n";
        $stSql .= "        EXTRACT(month FROM liquida_anula.dt_liquidacao) =  '".$this->getDado('inMes')."'                                                             \n";
        $stSql .= "        AND liquida_anula.dt_anulacao IS NULL                                                                                                        \n";
        $stSql .= "        )                                                                                                                                            \n";
        $stSql .= "        )                                                                                                                                            \n";
        $stSql .= "        AND liquida_anula.cod_entidade                           IN (".$this->getDado('stEntidades').")                                              \n";
        $stSql .= "   GROUP BY liquida_anula.cod_empenho                                                                                                                \n";
        $stSql .= "          , liquida_anula.exercicio                                                                                                                  \n";
        $stSql .= "          , liquida_anula.dt_liquidacao                                                                                                              \n";
        $stSql .= "          , liquida_anula.dt_anulacao                                                                                                                \n";
        $stSql .= "          , liquida_anula.numero_status_anulacao                                                                                                     \n";
        $stSql .= "          , liquida_anula.itens                                                                                                                      \n";
        $stSql .= "          , liquida_anula.anulados                                                                                                                   \n";
        $stSql .= "          , despesa.num_orgao                                                                                                                        \n";
        $stSql .= "          , despesa.num_unidade                                                                                                                      \n";

        if ($this->getDado('boIncorporarEmpenhos')) {
                $stSql .= " UNION ";
                $stSql .= " SELECT empenho_incorporacao.descricao                                    AS cod_empenho_incorporado
                                , liquida_anula.exercicio                                            AS exercicio_empenho
                                , lpad(despesa.num_orgao::varchar, 3, '0')||lpad(despesa.num_unidade::varchar, 2, '0') AS unidade_orcamentaria
                                , '31/12/'||liquida_anula.exercicio as data_liquidacao
                                , SUM(liquida_anula.valor)                                           AS valor
                                , CASE WHEN liquida_anula.dt_anulacao IS NOT NULL THEN
                                      '31/12/'||liquida_anula.exercicio
                                  END AS data_anulacao
                                , liquida_anula.numero_status_anulacao
                                , SUM(liquida_anula.itens) as itens
                                , SUM(liquida_anula.anulados) as anulados
                                , empenho_incorporacao.cod_empenho_incorporado
                             FROM (
                                        SELECT empenho.cod_empenho
                                             , empenho.exercicio
                                             , empenho.cod_entidade
                                             , nota_liquidacao.cod_nota
                                             , nota_liquidacao.dt_liquidacao
                                             , nota_liquidacao_item.num_item
                                             -- , CASE
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '12' THEN
                                             --         SUM(nota_liquidacao_item.vl_total)
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '12' THEN
                                             --         SUM(nota_liquidacao_item.vl_total)
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN
                                             --         SUM(nota_liquidacao_item.vl_total - nota_liquidacao_item_anulado.vl_anulado)
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) > 0                                THEN
                                             --         0.00
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                             --         SUM(nota_liquidacao_item.vl_total)
                                             --      WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                             --         SUM(nota_liquidacao_item.vl_total)
                                             --      ELSE
                                             --         SUM(nota_liquidacao_item.vl_total - nota_liquidacao_item_anulado.vl_anulado)
                                             --   END                                  AS valor
                                             , SUM(nota_liquidacao_item.vl_total - COALESCE(nota_liquidacao_item_anulado.vl_anulado, 0.00)) AS valor
                                             -- , CASE
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '12' THEN
                                             --         NULL
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '12' THEN
                                             --         nota_liquidacao_item_anulado.timestamp::date
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN
                                             --         nota_liquidacao_item_anulado.timestamp::date
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) > 0                                THEN
                                             --         NULL
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                             --         nota_liquidacao_item_anulado.timestamp::date
                                             --      WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                             --         nota_liquidacao_item_anulado.timestamp::date
                                             --      ELSE
                                             --         NULL
                                             --   END                                  AS dt_anulacao
                                             , NULL AS dt_anulacao
                                             , CASE
                                                  WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                                   AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '12' THEN
                                                     0
                                                  WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                                   AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '12' THEN
                                                     1
                                                  WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                                   AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                                   AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                                     1
                                                  WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date
                                                   AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                                   AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                                     1
                                                  ELSE
                                                     0
                                               END                                  AS numero_status_anulacao
                                             , mandrake.itens
                                             , mandrake.anulados
                                          FROM empenho.empenho
                                    INNER JOIN empenho.nota_liquidacao
                                            ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                           AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                    INNER JOIN empenho.nota_liquidacao_item
                                            ON nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio
                                           AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota
                                           AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
                                     LEFT JOIN empenho.nota_liquidacao_item_anulado
                                            ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                                           AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                                           AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                                           AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                                           AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                                           AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                                    INNER JOIN (
                                                     SELECT nota_liquidacao.cod_empenho
                                                          , nota_liquidacao.dt_liquidacao
                                                          , COUNT(nota_liquidacao_item.cod_nota)             AS itens
                                                          , COUNT(nota_liquidacao_item_anulado.cod_nota)     AS anulados
                                                       FROM empenho.nota_liquidacao
                                                 INNER JOIN empenho.nota_liquidacao_item
                                                         ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                                                        AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                                                        AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                                                  LEFT JOIN empenho.nota_liquidacao_item_anulado
                                                         ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                                                        AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                                                        AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                                                        AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                                                        AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                                                        AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                                   GROUP BY nota_liquidacao.cod_empenho
                                                          , nota_liquidacao.dt_liquidacao
                                               )                                     AS mandrake
                                            ON mandrake.cod_empenho   = empenho.cod_empenho
                                           AND mandrake.dt_liquidacao = nota_liquidacao.dt_liquidacao
                                      GROUP BY empenho.cod_empenho
                                             , empenho.exercicio
                                             , empenho.cod_entidade
                                             , nota_liquidacao.cod_nota
                                             , nota_liquidacao.dt_liquidacao
                                             , nota_liquidacao_item.num_item
                                             , nota_liquidacao_item_anulado.timestamp
                                             , nota_liquidacao_item_anulado.vl_anulado
                                             , nota_liquidacao_item.vl_total
                                             , mandrake.itens
                                             , mandrake.anulados
                                     UNION
                                        SELECT empenho.cod_empenho
                                             , empenho.exercicio
                                             , empenho.cod_entidade
                                             , nota_liquidacao.cod_nota
                                             , nota_liquidacao.dt_liquidacao
                                             , nota_liquidacao_item.num_item
                                             -- , CASE
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '12' THEN
                                             --         0
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '12' THEN
                                             --         SUM(nota_liquidacao_item_anulado.vl_anulado)
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN
                                             --         SUM(nota_liquidacao_item.vl_total - nota_liquidacao_item_anulado.vl_anulado)
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) > 0                                THEN
                                             --         0.00
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                             --         SUM(nota_liquidacao_item_anulado.vl_anulado)
                                             --      WHEN nota_liquidacao.dt_liquidacao < nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) = 0                                THEN
                                             --         SUM(nota_liquidacao_item_anulado.vl_anulado)
                                             --      WHEN nota_liquidacao_item_anulado.vl_anulado IS NULL THEN
                                             --         SUM(nota_liquidacao_item.vl_total)
                                             --      ELSE
                                             --         SUM(nota_liquidacao_item_anulado.vl_anulado)
                                             --   END                                           AS valor
                                             , SUM(nota_liquidacao_item_anulado.vl_anulado) AS valor
                                             -- , CASE
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '12' THEN
                                             --         NULL
                                             --      WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                             --       AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '12' THEN
                                             --         nota_liquidacao_item_anulado.timestamp::date
                                             --      WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                             --       AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                             --       AND (mandrake.itens - mandrake.anulados) > 0                                THEN
                                             --         NULL
                                             --      ELSE
                                             --         nota_liquidacao_item_anulado.timestamp::date
                                             --   END                                           AS dt_anulacao
                                             , nota_liquidacao_item_anulado.timestamp::date AS dt_anulacao
                                             , CASE
                                                  WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                                   AND EXTRACT(month FROM nota_liquidacao.dt_liquidacao) = '12' THEN
                                                     0
                                                  WHEN EXTRACT(month FROM nota_liquidacao.dt_liquidacao) < EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date)
                                                   AND EXTRACT(month FROM nota_liquidacao_item_anulado.timestamp::date) = '12' THEN
                                                     1
                                                  WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                                   AND nota_liquidacao_item_anulado.vl_anulado < nota_liquidacao_item.vl_total THEN
                                                     0
                                                  WHEN nota_liquidacao.dt_liquidacao = nota_liquidacao_item_anulado.timestamp::date
                                                   AND nota_liquidacao_item_anulado.vl_anulado = nota_liquidacao_item.vl_total
                                                   AND (mandrake.itens - mandrake.anulados) > 0                                THEN
                                                     0
                                                  WHEN nota_liquidacao_item_anulado.vl_anulado IS NULL THEN
                                                     0
                                                  ELSE
                                                     1
                                               END                                  AS numero_status_anulacao
                                             , mandrake.itens
                                             , mandrake.anulados
                                          FROM empenho.empenho
                                    INNER JOIN empenho.nota_liquidacao
                                            ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                                           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                           AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                                    INNER JOIN empenho.nota_liquidacao_item
                                            ON nota_liquidacao.exercicio    = nota_liquidacao_item.exercicio
                                           AND nota_liquidacao.cod_nota     = nota_liquidacao_item.cod_nota
                                           AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
                                    INNER JOIN empenho.nota_liquidacao_item_anulado
                                            ON nota_liquidacao_item.exercicio       = nota_liquidacao_item_anulado.exercicio
                                           AND nota_liquidacao_item.cod_nota        = nota_liquidacao_item_anulado.cod_nota
                                           AND nota_liquidacao_item.num_item        = nota_liquidacao_item_anulado.num_item
                                           AND nota_liquidacao_item.exercicio_item  = nota_liquidacao_item_anulado.exercicio_item
                                           AND nota_liquidacao_item.cod_pre_empenho = nota_liquidacao_item_anulado.cod_pre_empenho
                                           AND nota_liquidacao_item.cod_entidade    = nota_liquidacao_item_anulado.cod_entidade
                                    INNER JOIN (
                                                     SELECT nota_liquidacao.cod_empenho
                                                          , nota_liquidacao.dt_liquidacao
                                                          , COUNT(nota_liquidacao_item.cod_nota)             AS itens
                                                          , COUNT(nota_liquidacao_item_anulado.cod_nota)     AS anulados
                                                       FROM empenho.nota_liquidacao
                                                 INNER JOIN empenho.nota_liquidacao_item
                                                         ON nota_liquidacao_item.exercicio    = nota_liquidacao.exercicio
                                                        AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                                                        AND nota_liquidacao_item.cod_nota     = nota_liquidacao.cod_nota
                                                  LEFT JOIN empenho.nota_liquidacao_item_anulado
                                                         ON nota_liquidacao_item_anulado.exercicio       = nota_liquidacao_item.exercicio
                                                        AND nota_liquidacao_item_anulado.cod_entidade    = nota_liquidacao_item.cod_entidade
                                                        AND nota_liquidacao_item_anulado.cod_nota        = nota_liquidacao_item.cod_nota
                                                        AND nota_liquidacao_item_anulado.num_item        = nota_liquidacao_item.num_item
                                                        AND nota_liquidacao_item_anulado.exercicio_item  = nota_liquidacao_item.exercicio_item
                                                        AND nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                                                   GROUP BY nota_liquidacao.cod_empenho
                                                          , nota_liquidacao.dt_liquidacao
                                               )                                     AS mandrake
                                            ON mandrake.cod_empenho   = empenho.cod_empenho
                                           AND mandrake.dt_liquidacao = nota_liquidacao.dt_liquidacao
                                      GROUP BY empenho.cod_empenho
                                             , empenho.exercicio
                                             , empenho.cod_entidade
                                             , nota_liquidacao.cod_nota
                                             , nota_liquidacao.dt_liquidacao
                                             , nota_liquidacao_item.num_item
                                             , nota_liquidacao_item_anulado.timestamp
                                             , nota_liquidacao_item_anulado.vl_anulado
                                             , nota_liquidacao_item.vl_total
                                             , mandrake.itens
                                             , mandrake.anulados
                                  )  AS liquida_anula
                         INNER JOIN empenho.empenho
                                 ON empenho.cod_empenho = liquida_anula.cod_empenho
                                AND empenho.exercicio   = liquida_anula.exercicio
                                AND empenho.cod_entidade= liquida_anula.cod_entidade
                         INNER JOIN tceam.empenho_incorporacao
                                 ON empenho.cod_empenho  = empenho_incorporacao.cod_empenho
                                AND empenho.exercicio    = empenho_incorporacao.exercicio
                                AND empenho.cod_entidade = empenho_incorporacao.cod_entidade
                         INNER JOIN empenho.pre_empenho
                                 ON pre_empenho.exercicio       = empenho.exercicio
                                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                         INNER JOIN empenho.pre_empenho_despesa
                                 ON pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                                AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                         INNER JOIN orcamento.despesa
                                 ON despesa.exercicio   = pre_empenho_despesa.exercicio
                                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              WHERE liquida_anula.valor > 0.00
                                AND liquida_anula.exercicio                              =  '".$this->getDado('exercicio')."'
                                AND liquida_anula.cod_entidade                           IN (".$this->getDado('stCodEntidadesIncorporadas').")
                           GROUP BY liquida_anula.exercicio
                                  , data_liquidacao
                                  , data_anulacao
                                  , liquida_anula.numero_status_anulacao
                                  , unidade_orcamentaria
                                  , empenho_incorporacao.descricao
                                  , empenho_incorporacao.cod_empenho_incorporado ";
        }
        $stSql .= " ) as tabela
             ORDER BY cod_empenho_incorporado
                    , substr(cod_empenho, position('.' IN cod_empenho)+1)::integer
              ";

        return $stSql;
    }
}
?>
