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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62219 $
    $Name$
    $Author: michel $
    $Date: 2015-04-09 09:47:41 -0300 (Thu, 09 Apr 2015) $
    
    $Id: TTGODES.class.php 62219 2015-04-09 12:47:41Z michel $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.5  2007/10/17 13:16:51  bruce
*** empty log message ***

Revision 1.4  2007/10/10 23:35:33  hboaventura
correção dos arquivos

Revision 1.3  2007/06/12 20:44:11  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.2  2007/06/12 18:34:05  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.1  2007/04/26 20:22:12  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGODES extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDespesa()
    {
        $stSql = "  SELECT
                              '10'    AS     tipo_registro
                            , ppa_progama.num_programa AS cod_programa
                            --, despesa.cod_despesa
                            , despesa.num_orgao
                            , despesa.num_unidade
                            , despesa.cod_funcao
                            , despesa.cod_subfuncao
                            , SUBSTR(acao.num_acao::VARCHAR,1,1) AS cod_natureza
                            , SUBSTR(acao.num_acao::VARCHAR,2,3) AS numero_pao
                            , pao.nom_pao
                            , COALESCE(SUM(despesa.vl_original),0.00) as vl_orcado
                            , CASE WHEN SUM(vl_transposicao_recurso.valor) IS NOT NULL 
                                    THEN 
                                        0.00
                                    ELSE
                                        COALESCE(SUM(suplementacao.valor),0.00) 
                             END as vl_suplementado
                            , COALESCE(SUM(reducao.valor),0.00) as vl_reduzido
                            , COALESCE(SUM(vl_credito_especial.valor),0.00) as vl_credito_especial
                            , COALESCE(SUM(vl_credito_extra.valor),0.00) as vl_credito_extra
                            , 0.00 AS vl_limitacao_empenho
                            , 0.00 AS vl_reversao_limitacao_empenho
                            , 0.00 AS vl_correcao_orcamento
                            , 0.00 AS vl_anulacao_realocacao
                            , COALESCE(SUM(vl_transposicao_recurso.valor),0.00) AS vl_transposicao_recurso
                            , COALESCE(SUM(empenho_autorizado.vl_total),0.00) as vl_autorizado
                            , '' AS espacador
                            , '0' as numero_sequencial

                    FROM  orcamento.despesa
                    INNER JOIN  orcamento.conta_despesa
                            ON  conta_despesa.exercicio = despesa.exercicio
                           AND  conta_despesa.cod_conta = despesa.cod_conta
                    INNER JOIN orcamento.programa AS o_programa
                            ON o_programa.exercicio = despesa.exercicio
                           AND o_programa.cod_programa = despesa.cod_programa
                    INNER JOIN orcamento.programa_ppa_programa
                            ON programa_ppa_programa.exercicio = o_programa.exercicio
                           AND programa_ppa_programa.cod_programa = o_programa.cod_programa
                    INNER JOIN ppa.programa AS ppa_progama
                            ON ppa_progama.cod_programa = programa_ppa_programa.cod_programa_ppa
                    INNER JOIN  orcamento.recurso
                            ON  recurso.exercicio = despesa.exercicio
                           AND  recurso.cod_recurso = despesa.cod_recurso

                    INNER JOIN  orcamento.pao
                            ON  pao.exercicio = despesa.exercicio
                           AND  pao.num_pao = despesa.num_pao
                    INNER JOIN orcamento.pao_ppa_acao
                            ON pao_ppa_acao.exercicio = pao.exercicio
                           AND pao_ppa_acao.num_pao   = pao.num_pao
                    INNER JOIN ppa.acao
                            ON acao.cod_acao = pao_ppa_acao.cod_acao

                    LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                                        ,  SUM(suplementacao_suplementada.valor) AS valor
                                        ,  suplementacao_suplementada.exercicio
                                     FROM  orcamento.suplementacao
                               INNER JOIN  orcamento.suplementacao_suplementada
                                       ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                      AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                    WHERE  NOT EXISTS (    SELECT  1
                                                             FROM  orcamento.suplementacao_anulada
                                                            WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                              AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                      )
                                 GROUP BY  suplementacao_suplementada.cod_despesa, suplementacao_suplementada.exercicio
                               )   AS  suplementacao
                           ON  suplementacao.cod_despesa = despesa.cod_despesa
                          AND  suplementacao.exercicio = despesa.exercicio
                    LEFT JOIN  (   SELECT  suplementacao_reducao.cod_despesa
                                        ,  SUM(suplementacao_reducao.valor) AS valor
                                        ,  suplementacao_reducao.exercicio
                                     FROM  orcamento.suplementacao
                               INNER JOIN  orcamento.suplementacao_reducao
                                       ON  suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                                      AND  suplementacao_reducao.exercicio = suplementacao.exercicio
                                    WHERE  NOT EXISTS (    SELECT  1
                                                             FROM  orcamento.suplementacao_anulada
                                                            WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                              AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                      )
                                 GROUP BY  suplementacao_reducao.cod_despesa, suplementacao_reducao.exercicio
                               )   AS  reducao
                           ON  reducao.cod_despesa = despesa.cod_despesa
                          AND  reducao.exercicio = despesa.exercicio
                    LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                                        ,  SUM(suplementacao_suplementada.valor) AS valor
                                        ,  suplementacao_suplementada.exercicio
                                     FROM  orcamento.suplementacao
                               INNER JOIN  orcamento.suplementacao_suplementada
                                       ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                      AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                    WHERE  NOT EXISTS  (    SELECT  1
                                                              FROM  orcamento.suplementacao_anulada
                                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                       )
                                      AND  suplementacao.cod_tipo BETWEEN 6 AND 10
                                 GROUP BY  cod_despesa, suplementacao_suplementada.exercicio
                            )   AS  vl_credito_especial
                           ON  vl_credito_especial.cod_despesa = despesa.cod_despesa
                          AND  vl_credito_especial.exercicio = despesa.exercicio

                    LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                                         ,  SUM(suplementacao_suplementada.valor) AS valor
                                         ,  suplementacao_suplementada.exercicio
                                      FROM  orcamento.suplementacao
                                INNER JOIN  orcamento.suplementacao_suplementada
                                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                     WHERE  NOT EXISTS  (    SELECT  1
                                                               FROM  orcamento.suplementacao_anulada
                                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                        )
                                       AND  suplementacao.cod_tipo = 11
                                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio
                            )   AS  vl_credito_extra
                         ON  vl_credito_extra.cod_despesa = despesa.cod_despesa
                       AND  vl_credito_extra.exercicio = despesa.exercicio
     
                    LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                                        ,  SUM(suplementacao_suplementada.valor) AS valor
                                        ,  suplementacao_suplementada.exercicio
                                     FROM  orcamento.suplementacao
                               INNER JOIN  orcamento.suplementacao_suplementada
                                       ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                      AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                    WHERE  NOT EXISTS  (    SELECT  1
                                                              FROM  orcamento.suplementacao_anulada
                                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                       )
                                      AND  suplementacao.cod_tipo = 13
                                 GROUP BY  cod_despesa, suplementacao_suplementada.exercicio
                               )   AS  vl_transposicao_recurso
                           ON  vl_transposicao_recurso.cod_despesa = despesa.cod_despesa
                          AND  vl_transposicao_recurso.exercicio = despesa.exercicio
           
           
                    LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                                        ,  SUM(suplementacao_suplementada.valor) AS valor
                                        ,  suplementacao_suplementada.exercicio
                                     FROM  orcamento.suplementacao
                               INNER JOIN  orcamento.suplementacao_suplementada
                                       ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                      AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                    WHERE  EXISTS  (    SELECT  1
                                                              FROM  orcamento.suplementacao_anulada
                                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                       )
                                      AND  suplementacao.cod_tipo IN (12,13,14)
                                 GROUP BY  cod_despesa, suplementacao_suplementada.exercicio
                               )   AS  vl_anulacao_realocacao
                           ON  vl_anulacao_realocacao.cod_despesa = despesa.cod_despesa
                          AND  vl_anulacao_realocacao.exercicio = despesa.exercicio

                    LEFT JOIN  (
                                SELECT  ( SUM(item_pre_empenho.vl_total) - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) ) AS vl_total
                                     ,  pre_empenho_despesa.cod_despesa
                                     ,  pre_empenho_despesa.exercicio
                                  FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                                   AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.autorizacao_empenho
                                    ON  autorizacao_empenho.exercicio = empenho.exercicio
                                   AND  autorizacao_empenho.cod_entidade = empenho.cod_entidade
                                 WHERE  NOT EXISTS  (   SELECT  1
                                                          FROM  empenho.autorizacao_anulada
                                                         WHERE  autorizacao_anulada.exercicio = autorizacao_empenho.exercicio
                                                           AND  autorizacao_anulada.cod_entidade = autorizacao_empenho.cod_entidade
                                                           AND  autorizacao_anulada.cod_autorizacao = autorizacao_empenho.cod_autorizacao
                                                    )
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa
                            ) AS  empenho_autorizado
                        ON  empenho_autorizado.cod_despesa = despesa.cod_despesa
                        AND  empenho_autorizado.exercicio = despesa.exercicio
                    WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
                    AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
                    GROUP BY  ppa_progama.num_programa
                           --, despesa.cod_despesa
                           , despesa.num_orgao
                           , despesa.num_unidade
                           , despesa.cod_funcao
                           , despesa.cod_subfuncao
                           , cod_natureza
                           , numero_pao
                           , pao.nom_pao
        ";

        return $stSql;
    }

    public function recuperaDespesaElemento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesaElemento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDespesaElemento()
    {
        $stSql = "
        SELECT
                '11'    AS     tipo_registro
             , ppa_progama.num_programa AS cod_programa
             --,  despesa.cod_despesa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             , SUBSTR(acao.num_acao::VARCHAR,1,1) AS cod_natureza
             , SUBSTR(acao.num_acao::VARCHAR,2,3) AS numero_pao
             ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)    AS  elemento_despesa
             ,  COALESCE(SUM(despesa.vl_original),0.00) as vl_orcado
             ,  COALESCE(SUM(suplementacao.valor),0.00) as vl_suplementado
             ,  COALESCE(SUM(reducao.valor),0.00) as vl_reduzido
             ,  COALESCE(SUM(vl_credito_especial.valor),0.00) as vl_credito_especial
             ,  COALESCE(SUM(vl_credito_extra.valor),0.00) as vl_credito_extra
             , 0.00 AS vl_limitacao_empenho
             , 0.00 AS vl_reversao_limitacao_empenho
             , 0.00 AS vl_correcao_orcamento
             , COALESCE(SUM(vl_anulacao_realocacao.valor),0.00) AS vl_anulacao_realocacao
             , COALESCE(SUM(vl_transposicao_recurso.valor),0.00) AS vl_transposicao_recurso
             ,  COALESCE(SUM(empenho_autorizado.vl_total),0.00) as vl_autorizado
             ,  COALESCE(SUM(empenho.vl_total),0.00) as vl_empenhado
             ,  COALESCE(SUM(liquidado.vl_total),0.00) as vl_liquidado
             ,  COALESCE(SUM(nota_paga.vl_total),0.00) as vl_pago
             ,  COALESCE(SUM(fonte.vl_total),0.00) as vl_fonte
             ,  '' AS espacador
             ,  '0' as numero_sequencial


          FROM  orcamento.despesa
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta
    INNER JOIN orcamento.programa AS o_programa
            ON o_programa.exercicio = despesa.exercicio
           AND o_programa.cod_programa = despesa.cod_programa
    INNER JOIN orcamento.programa_ppa_programa
            ON programa_ppa_programa.exercicio = o_programa.exercicio
           AND programa_ppa_programa.cod_programa = o_programa.cod_programa
    INNER JOIN ppa.programa AS ppa_progama
            ON ppa_progama.cod_programa = programa_ppa_programa.cod_programa_ppa
INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
    INNER JOIN  orcamento.orgao
            ON  orcamento.orgao.exercicio = orcamento.despesa.exercicio
           AND  orcamento.orgao.num_orgao = orcamento.despesa.num_orgao
    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
    INNER JOIN orcamento.pao_ppa_acao
            ON pao_ppa_acao.exercicio = pao.exercicio
           AND pao_ppa_acao.num_pao   = pao.num_pao
    INNER JOIN ppa.acao
            ON acao.cod_acao = pao_ppa_acao.cod_acao
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS (    SELECT  1
                                              FROM  orcamento.suplementacao_anulada
                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                       )
                  GROUP BY  suplementacao_suplementada.cod_despesa, suplementacao_suplementada.exercicio
                )   AS  suplementacao
            ON  suplementacao.cod_despesa = despesa.cod_despesa
           AND  suplementacao.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao_reducao.cod_despesa
                         ,  SUM(suplementacao_reducao.valor) AS valor
                         ,  suplementacao_reducao.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_reducao
                        ON  suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_reducao.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS (    SELECT  1
                                              FROM  orcamento.suplementacao_anulada
                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                       )
                  GROUP BY  suplementacao_reducao.cod_despesa, suplementacao_reducao.exercicio
                )   AS  reducao
            ON  reducao.cod_despesa = despesa.cod_despesa
           AND  reducao.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo BETWEEN 6 AND 10
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_credito_especial
            ON  vl_credito_especial.cod_despesa = despesa.cod_despesa
           AND  vl_credito_especial.exercicio = despesa.exercicio

     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo = 11
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_credito_extra
            ON  vl_credito_extra.cod_despesa = despesa.cod_despesa
           AND  vl_credito_extra.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo = 13
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_transposicao_recurso
            ON  vl_transposicao_recurso.cod_despesa = despesa.cod_despesa
           AND  vl_transposicao_recurso.exercicio = despesa.exercicio
           
           
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo IN (12,13,14)
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_anulacao_realocacao
            ON  vl_anulacao_realocacao.cod_despesa = despesa.cod_despesa
           AND  vl_anulacao_realocacao.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  ( SUM(item_pre_empenho.vl_total) - SUM(empenho_anulado_item.vl_anulado) ) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 LEFT JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN  empenho.autorizacao_empenho
                        ON  autorizacao_empenho.exercicio = empenho.exercicio
                       AND  autorizacao_empenho.cod_entidade = empenho.cod_entidade
                     WHERE  NOT EXISTS  (   SELECT  1
                                              FROM  empenho.autorizacao_anulada
                                             WHERE  autorizacao_anulada.exercicio = autorizacao_empenho.exercicio
                                               AND  autorizacao_anulada.cod_entidade = autorizacao_empenho.cod_entidade
                                               AND  autorizacao_anulada.cod_autorizacao = autorizacao_empenho.cod_autorizacao
                                        )
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho_autorizado
            ON  empenho_autorizado.cod_despesa = despesa.cod_despesa
           AND  empenho_autorizado.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  ( SUM(item_pre_empenho.vl_total) - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) ) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 LEFT JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho
            ON  empenho.cod_despesa = despesa.cod_despesa
           AND  empenho.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  (SUM(nota_liquidacao_paga.vl_pago) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0))) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                INNER JOIN  empenho.nota_liquidacao_paga
                        ON  nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
                 LEFT JOIN  empenho.nota_liquidacao_paga_anulada
                        ON  nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                       AND  nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                       AND  nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade

                  GROUP BY  cod_despesa, pre_empenho_despesa.exercicio
                ) AS  nota_paga
            ON  nota_paga.cod_despesa = despesa.cod_despesa
           AND  nota_paga.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  (SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0))) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                INNER JOIN  empenho.nota_liquidacao_item
                        ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                 LEFT JOIN  empenho.nota_liquidacao_item_anulado
                        ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                       AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                       AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                       AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                       AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                       AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                  GROUP BY  cod_despesa, pre_empenho_despesa.exercicio
                ) AS  liquidado
            ON  liquidado.cod_despesa = despesa.cod_despesa
           AND  liquidado.exercicio = despesa.exercicio
    INNER JOIN  (   SELECT  SUM(receita.vl_original) AS vl_total
                         ,  recurso.exercicio
                         ,  recurso.cod_recurso
                      FROM  orcamento.recurso
                INNER JOIN  orcamento.receita
                        ON  receita.exercicio = recurso.exercicio
                       AND  receita.cod_recurso = recurso.cod_recurso
                  GROUP BY  recurso.exercicio, recurso.cod_recurso
                ) AS  fonte
            ON  fonte.exercicio = despesa.exercicio
           AND  fonte.cod_recurso = despesa.cod_recurso
         WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
           AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
      GROUP BY  ppa_progama.num_programa
             --, despesa.cod_despesa
             , despesa.num_orgao
             , despesa.num_unidade
             , despesa.cod_funcao
             , despesa.cod_subfuncao
             , cod_natureza
             , numero_pao
             , elemento_despesa
        ";

        return $stSql;
    }

    public function recuperaDespesaRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesaRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDespesaRecurso()
    {
        $stSql = "
        SELECT
                '12'    AS     tipo_registro
             , ppa_progama.num_programa AS cod_programa
             --,  despesa.cod_despesa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             , SUBSTR(acao.num_acao::VARCHAR,1,1) AS cod_natureza
             , SUBSTR(acao.num_acao::VARCHAR,2,3) AS numero_pao
             , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)    AS  elemento_despesa
             , SUBSTR(despesa.cod_recurso::VARCHAR,1,3) AS cod_fonte
             ,  fonte_recurso.descricao AS nom_recurso
             ,  COALESCE(SUM(despesa.vl_original),0.00) as vl_orcado
             ,  COALESCE(SUM(suplementacao.valor),0.00) as vl_suplementado
             ,  COALESCE(SUM(reducao.valor),0.00) as vl_reduzido
             ,  COALESCE(SUM(vl_credito_especial.valor),0.00) as vl_credito_especial
             ,  COALESCE(SUM(vl_credito_extra.valor),0.00) as vl_credito_extra
             , 0.00 AS vl_limitacao_empenho
             , 0.00 AS vl_reversao_limitacao_empenho
             , 0.00 AS vl_correcao_orcamento
             , COALESCE(SUM(vl_anulacao_realocacao.valor),0.00) AS vl_anulacao_realocacao
             , COALESCE(SUM(vl_transposicao_recurso.valor),0.00) AS vl_transposicao_recurso
             ,  COALESCE(SUM(empenho_autorizado.vl_total),0.00) as vl_autorizado
             ,  COALESCE(SUM(empenho.vl_total),0.00) as vl_empenhado
             ,  COALESCE(SUM(liquidado.vl_total),0.00) as vl_liquidado
             ,  COALESCE(SUM(nota_paga.vl_total),0.00) as vl_pago
             ,  COALESCE(SUM(fonte.vl_total),0.00) as vl_fonte
             ,  '' AS espacador
             ,  '0' as numero_sequencial
          FROM  orcamento.despesa
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta
    INNER JOIN orcamento.programa AS o_programa
            ON o_programa.exercicio = despesa.exercicio
           AND o_programa.cod_programa = despesa.cod_programa
    INNER JOIN orcamento.programa_ppa_programa
            ON programa_ppa_programa.exercicio = o_programa.exercicio
           AND programa_ppa_programa.cod_programa = o_programa.cod_programa
    INNER JOIN ppa.programa AS ppa_progama
            ON ppa_progama.cod_programa = programa_ppa_programa.cod_programa_ppa
    INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
    INNER JOIN  orcamento.recurso_direto
            ON  recurso_direto.exercicio = recurso.exercicio
           AND  recurso_direto.cod_recurso = recurso.cod_recurso
    INNER JOIN  orcamento.fonte_recurso
            ON  fonte_recurso.cod_fonte = recurso_direto.cod_fonte
    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
    INNER JOIN orcamento.pao_ppa_acao
            ON pao_ppa_acao.exercicio = pao.exercicio
           AND pao_ppa_acao.num_pao   = pao.num_pao
    INNER JOIN ppa.acao
            ON acao.cod_acao = pao_ppa_acao.cod_acao
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS (    SELECT  1
                                              FROM  orcamento.suplementacao_anulada
                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                       )
                  GROUP BY  suplementacao_suplementada.cod_despesa, suplementacao_suplementada.exercicio
                )   AS  suplementacao
            ON  suplementacao.cod_despesa = despesa.cod_despesa
           AND  suplementacao.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao_reducao.cod_despesa
                         ,  SUM(suplementacao_reducao.valor) AS valor
                         ,  suplementacao_reducao.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_reducao
                        ON  suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_reducao.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS (    SELECT  1
                                              FROM  orcamento.suplementacao_anulada
                                             WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                               AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                       )
                  GROUP BY  suplementacao_reducao.cod_despesa, suplementacao_reducao.exercicio
                )   AS  reducao
            ON  reducao.cod_despesa = despesa.cod_despesa
           AND  reducao.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo BETWEEN 6 AND 10
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_credito_especial
            ON  vl_credito_especial.cod_despesa = despesa.cod_despesa
           AND  vl_credito_especial.exercicio = despesa.exercicio

     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo = 11
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_credito_extra
            ON  vl_credito_extra.cod_despesa = despesa.cod_despesa
           AND  vl_credito_extra.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  NOT EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo = 13
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_transposicao_recurso
            ON  vl_transposicao_recurso.cod_despesa = despesa.cod_despesa
           AND  vl_transposicao_recurso.exercicio = despesa.exercicio
           
           
     LEFT JOIN  (   SELECT  suplementacao_suplementada.cod_despesa
                         ,  SUM(suplementacao_suplementada.valor) AS valor
                         ,  suplementacao_suplementada.exercicio
                      FROM  orcamento.suplementacao
                INNER JOIN  orcamento.suplementacao_suplementada
                        ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                       AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                     WHERE  EXISTS  (    SELECT  1
                                               FROM  orcamento.suplementacao_anulada
                                              WHERE  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                AND  suplementacao_anulada.exercicio = suplementacao.exercicio
                                        )
                       AND  suplementacao.cod_tipo IN (12,13,14)
                  GROUP BY  cod_despesa, suplementacao_suplementada.exercicio

                )   AS  vl_anulacao_realocacao
            ON  vl_anulacao_realocacao.cod_despesa = despesa.cod_despesa
           AND  vl_anulacao_realocacao.exercicio = despesa.exercicio

     LEFT JOIN  (
                    SELECT  ( SUM(item_pre_empenho.vl_total) - SUM(empenho_anulado_item.vl_anulado) ) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 LEFT JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN  empenho.autorizacao_empenho
                        ON  autorizacao_empenho.exercicio = empenho.exercicio
                       AND  autorizacao_empenho.cod_entidade = empenho.cod_entidade
                     WHERE  NOT EXISTS  (   SELECT  1
                                              FROM  empenho.autorizacao_anulada
                                             WHERE  autorizacao_anulada.exercicio = autorizacao_empenho.exercicio
                                               AND  autorizacao_anulada.cod_entidade = autorizacao_empenho.cod_entidade
                                               AND  autorizacao_anulada.cod_autorizacao = autorizacao_empenho.cod_autorizacao
                                        )
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho_autorizado
            ON  empenho_autorizado.cod_despesa = despesa.cod_despesa
           AND  empenho_autorizado.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  ( SUM(item_pre_empenho.vl_total) - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) ) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 LEFT JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho
            ON  empenho.cod_despesa = despesa.cod_despesa
           AND  empenho.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  (SUM(nota_liquidacao_paga.vl_pago) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0))) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                INNER JOIN  empenho.nota_liquidacao_paga
                        ON  nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
                 LEFT JOIN  empenho.nota_liquidacao_paga_anulada
                        ON  nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                       AND  nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                       AND  nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade

                  GROUP BY  cod_despesa, pre_empenho_despesa.exercicio
                ) AS  nota_paga
            ON  nota_paga.cod_despesa = despesa.cod_despesa
           AND  nota_paga.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  (SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0))) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                INNER JOIN  empenho.nota_liquidacao_item
                        ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                 LEFT JOIN  empenho.nota_liquidacao_item_anulado
                        ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                       AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                       AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                       AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                       AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                       AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                  GROUP BY  cod_despesa, pre_empenho_despesa.exercicio
                ) AS  liquidado
            ON  liquidado.cod_despesa = despesa.cod_despesa
           AND  liquidado.exercicio = despesa.exercicio
    INNER JOIN  (   SELECT  SUM(receita.vl_original) AS vl_total
                         ,  recurso.exercicio
                         ,  recurso.cod_recurso
                      FROM  orcamento.recurso
                INNER JOIN  orcamento.receita
                        ON  receita.exercicio = recurso.exercicio
                       AND  receita.cod_recurso = recurso.cod_recurso
                  GROUP BY  recurso.exercicio, recurso.cod_recurso
                ) AS  fonte
            ON  fonte.exercicio = despesa.exercicio
           AND  fonte.cod_recurso = despesa.cod_recurso
         WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
           AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
            GROUP BY  ppa_progama.num_programa 
                    --, despesa.cod_despesa
                    , despesa.num_orgao
                    , despesa.num_unidade
                    , despesa.cod_funcao
                    , despesa.cod_subfuncao
                    , cod_natureza
                    , numero_pao
                    , elemento_despesa
                    , despesa.cod_recurso
                    , fonte_recurso.descricao
    ";

        return $stSql;
    }
    public function recuperaDespesaRecursoDetalhamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesaRecursoDetalhamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDespesaRecursoDetalhamento()
    {
        $stSql = "
        SELECT
                '13'    AS     tipo_registro
             , ppa_progama.num_programa AS cod_programa
             --,  despesa.cod_despesa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             , SUBSTR(acao.num_acao::VARCHAR,1,1) AS cod_natureza
             , SUBSTR(acao.num_acao::VARCHAR,2,3) AS numero_pao
             , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)    AS  elemento_despesa
             , SUBSTR(despesa.cod_recurso::VARCHAR,1,3) AS cod_fonte
             , substring(despesa.cod_recurso::VARCHAR from '...$') AS det_fonte_recurso
             ,  fonte_recurso.descricao AS nom_recurso
             ,  COALESCE(SUM(empenho.vl_total),0.00) as vl_empenhado
             ,  COALESCE(SUM(liquidado.vl_total),0.00) as vl_liquidado
             ,  COALESCE(SUM(nota_paga.vl_total),0.00) as vl_pago
             ,  '' AS espacador
             ,  '0' as numero_sequencial
          FROM  orcamento.despesa
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta
    INNER JOIN orcamento.programa AS o_programa
            ON o_programa.exercicio = despesa.exercicio
           AND o_programa.cod_programa = despesa.cod_programa
    INNER JOIN orcamento.programa_ppa_programa
            ON programa_ppa_programa.exercicio = o_programa.exercicio
           AND programa_ppa_programa.cod_programa = o_programa.cod_programa
    INNER JOIN ppa.programa AS ppa_progama
            ON ppa_progama.cod_programa = programa_ppa_programa.cod_programa_ppa
    INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
    INNER JOIN  orcamento.recurso_direto
            ON  recurso_direto.exercicio = recurso.exercicio
           AND  recurso_direto.cod_recurso = recurso.cod_recurso
    INNER JOIN  orcamento.fonte_recurso
            ON  fonte_recurso.cod_fonte = recurso_direto.cod_fonte
    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
    INNER JOIN orcamento.pao_ppa_acao
            ON pao_ppa_acao.exercicio = pao.exercicio
           AND pao_ppa_acao.num_pao   = pao.num_pao
    INNER JOIN ppa.acao
            ON acao.cod_acao = pao_ppa_acao.cod_acao

     LEFT JOIN  (
                    SELECT  ( SUM(item_pre_empenho.vl_total) - SUM(COALESCE(empenho_anulado_item.vl_anulado,0)) ) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                 LEFT JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                       AND  empenho_anulado_item.num_item = item_pre_empenho.num_item
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho
            ON  empenho.cod_despesa = despesa.cod_despesa
           AND  empenho.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  (SUM(nota_liquidacao_paga.vl_pago) - SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,0))) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                INNER JOIN  empenho.nota_liquidacao_paga
                        ON  nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
                 LEFT JOIN  empenho.nota_liquidacao_paga_anulada
                        ON  nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio
                       AND  nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota
                       AND  nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade

                  GROUP BY  cod_despesa, pre_empenho_despesa.exercicio
                ) AS  nota_paga
            ON  nota_paga.cod_despesa = despesa.cod_despesa
           AND  nota_paga.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  (SUM(nota_liquidacao_item.vl_total) - SUM(COALESCE(nota_liquidacao_item_anulado.vl_anulado,0))) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.nota_liquidacao
                        ON  nota_liquidacao.exercicio_empenho = empenho.exercicio
                       AND  nota_liquidacao.cod_entidade = empenho.cod_entidade
                       AND  nota_liquidacao.cod_empenho = empenho.cod_empenho
                INNER JOIN  empenho.nota_liquidacao_item
                        ON  nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                       AND  nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                       AND  nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
                 LEFT JOIN  empenho.nota_liquidacao_item_anulado
                        ON  nota_liquidacao_item_anulado.exercicio_item = nota_liquidacao_item.exercicio_item
                       AND  nota_liquidacao_item_anulado.cod_pre_empenho = nota_liquidacao_item.cod_pre_empenho
                       AND  nota_liquidacao_item_anulado.num_item = nota_liquidacao_item.num_item
                       AND  nota_liquidacao_item_anulado.exercicio = nota_liquidacao_item.exercicio
                       AND  nota_liquidacao_item_anulado.cod_entidade = nota_liquidacao_item.cod_entidade
                       AND  nota_liquidacao_item_anulado.cod_nota = nota_liquidacao_item.cod_nota
                  GROUP BY  cod_despesa, pre_empenho_despesa.exercicio
                ) AS  liquidado
            ON  liquidado.cod_despesa = despesa.cod_despesa
           AND  liquidado.exercicio = despesa.exercicio
         WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
           AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
            GROUP BY  ppa_progama.num_programa
                   --,  despesa.cod_despesa
                   ,  despesa.num_orgao
                   ,  despesa.num_unidade
                   ,  despesa.cod_funcao
                   ,  despesa.cod_subfuncao
                   ,  cod_natureza
                   ,  numero_pao
                   ,  elemento_despesa
                   ,  despesa.cod_recurso
                   ,  fonte_recurso.descricao
    ";

        return $stSql;
    }
}
