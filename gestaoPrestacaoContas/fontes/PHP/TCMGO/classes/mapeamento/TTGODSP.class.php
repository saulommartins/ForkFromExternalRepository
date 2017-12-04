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

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.8  2007/10/10 23:35:33  hboaventura
correção dos arquivos

Revision 1.7  2007/06/12 20:44:11  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.6  2007/06/12 18:34:05  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.5  2007/06/11 16:06:41  hboaventura
Correção de bugs

Revision 1.4  2007/06/07 18:38:19  hboaventura
Correção de bugs

Revision 1.3  2007/06/07 15:36:31  hboaventura
Inclusão do filtro por periodicidade

Revision 1.2  2007/04/26 20:22:12  hboaventura
Arquivos para geração do TCMGO

Revision 1.1  2007/04/25 16:11:54  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGODSP extends Persistente
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
        $stSql = "
        SELECT
                '10'    AS     tipo_registro
             ,  despesa.cod_programa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             ,  SUBSTR(despesa.num_pao,1,1)     AS  cod_natureza
             ,  SUBSTR(despesa.num_pao,2,3)     AS  numero_pao
             ,  orcamento.orgao.nom_orgao   AS  descricao_unidade
             ,  pao.nom_pao
             ,  SUM(despesa.vl_original) AS vl_original
             ,  '0' as numero_sequencial
             ,  despesa.cod_programa||despesa.num_orgao||despesa.num_unidade||despesa.cod_funcao||despesa.cod_subfuncao||SUBSTR(despesa.num_pao,1,1)||SUBSTR(despesa.num_pao,2,3) as chave
          FROM  orcamento.despesa
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta
    INNER JOIN  orcamento.orgao
            ON  orcamento.orgao.exercicio = orcamento.despesa.exercicio
           AND  orcamento.orgao.num_orgao = orcamento.despesa.num_orgao
    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
         WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
           AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
           AND  despesa.cod_despesa <> 24
      GROUP BY  despesa.cod_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao, cod_natureza, numero_pao, descricao_unidade, nom_pao
      order BY  despesa.cod_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao, cod_natureza, numero_pao, descricao_unidade, nom_pao
    ";

        return $stSql;
    }

    public function recuperaDespesaPorElemento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesaPorElemento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDespesaPorElemento()
    {
        $stSql = "
        SELECT
                '11'    AS     tipo_registro
             ,  despesa.cod_programa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             ,  SUBSTR(despesa.num_pao,1,1)     AS  cod_natureza
             ,  SUBSTR(despesa.num_pao,2,3)     AS  numero_pao
             ,  orcamento.orgao.nom_orgao   AS  descricao_unidade
             ,  pao.nom_pao
             ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)    AS  elemento_despesa
            ,  SUM(despesa.vl_original) as vl_orcado
            ,  SUM(suplementacao.valor) as vl_suplementado
            ,  SUM(reducao.valor) as vl_reduzido
            ,  SUM(empenho.vl_total) as vl_empenho
            ,  SUM(empenho_anulado.vl_total) as vl_empenho_anulado
            ,  '' AS espacador
            ,  '0' as numero_sequencial
             ,  despesa.cod_programa||despesa.num_orgao||despesa.num_unidade||despesa.cod_funcao||despesa.cod_subfuncao||SUBSTR(despesa.num_pao,1,1)||SUBSTR(despesa.num_pao,2,3) as chave
             ";
             if ( $this->getDado('dtInicio') != '01/01/2007' ) {

             $stSql.= ",  (   SELECT  ( SUM(odespesa.vl_original) + SUM(osuplementacao.valor) - SUM(oreducao.valor) - SUM(oempenho.vl_empenho) + SUM(oempenho.vl_empenho_anulado) )
                      FROM  orcamento.despesa AS odespesa
                 LEFT JOIN  (   SELECT  suplementacao.dt_suplementacao
                                     ,  suplementacao_suplementada.cod_despesa
                                     ,  SUM(suplementacao_suplementada.valor) AS valor
                                     ,  suplementacao_suplementada.exercicio
                                  FROM  orcamento.suplementacao
                            INNER JOIN  orcamento.suplementacao_suplementada
                                    ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                   AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                 WHERE  NOT EXISTS (    SELECT  1
                                                          FROM  orcamento.suplementacao_anulada
                                                         WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                           AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                    )
                              GROUP BY  dt_suplementacao, cod_despesa, suplementacao_suplementada.exercicio
                            ) AS osuplementacao
                        ON  osuplementacao.cod_despesa = odespesa.cod_despesa
                       AND  osuplementacao.exercicio = odespesa.exercicio
                       AND  osuplementacao.dt_suplementacao >= '".$this->getDado('exercicio')."-01-01'::date
                       AND  osuplementacao.dt_suplementacao < to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                 LEFT JOIN  (   SELECT  suplementacao_reducao.cod_despesa
                                     ,  SUM(suplementacao_reducao.valor) AS valor
                                     ,  suplementacao_reducao.exercicio
                                     ,  suplementacao.dt_suplementacao
                                  FROM  orcamento.suplementacao
                            INNER JOIN  orcamento.suplementacao_reducao
                                    ON  suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                                   AND  suplementacao_reducao.exercicio = suplementacao.exercicio
                                 WHERE  NOT EXISTS (    SELECT  1
                                                          FROM  orcamento.suplementacao_anulada
                                                         WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                           AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                   )
                              GROUP BY  suplementacao.dt_suplementacao, cod_despesa, suplementacao_reducao.exercicio
                            ) AS oreducao
                        ON  oreducao.cod_despesa = odespesa.cod_despesa
                       AND  oreducao.exercicio = odespesa.exercicio
                       AND  oreducao.dt_suplementacao >= '".$this->getDado('exercicio')."-01-01'::date
                       AND  oreducao.dt_suplementacao < to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                 LEFT JOIN  (
                                SELECT  SUM(vl_total) AS vl_empenho
                                     ,  SUM(vl_anulado) AS vl_empenho_anulado
                                     ,  pre_empenho_despesa.cod_despesa
                                     ,  pre_empenho_despesa.exercicio
                                  FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 WHERE  empenho.dt_empenho >= '".$this->getDado('exercicio')."-01-01'::date
                                   AND  empenho.dt_empenho < to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                            ) as oempenho
                        ON  oempenho.cod_despesa = odespesa.cod_despesa
                       AND  oempenho.exercicio = odespesa.exercicio
                     WHERE  odespesa.exercicio = despesa.exercicio
                       AND  odespesa.cod_despesa = despesa.cod_despesa
                )   AS  vl_saldo_anterior
          ";
        } else {
            $stSql.= " , 0.00 as vl_saldo_anterior ";
        }

       $stSql.= "
          FROM  orcamento.despesa
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta
    INNER JOIN  orcamento.orgao
            ON  orcamento.orgao.exercicio = orcamento.despesa.exercicio
           AND  orcamento.orgao.num_orgao = orcamento.despesa.num_orgao
    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
     LEFT JOIN  (   SELECT
                            suplementacao_suplementada.cod_despesa
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
                       AND  suplementacao.dt_suplementacao BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  suplementacao_suplementada.cod_despesa, suplementacao_suplementada.exercicio
                )   AS  suplementacao
            ON  suplementacao.cod_despesa = despesa.cod_despesa
           AND  suplementacao.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT
                            suplementacao_reducao.cod_despesa
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
                       AND  suplementacao.dt_suplementacao BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  suplementacao.dt_suplementacao, suplementacao_reducao.cod_despesa, suplementacao_reducao.exercicio
                )   AS  reducao
            ON  reducao.cod_despesa = despesa.cod_despesa
           AND  reducao.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  SUM(item_pre_empenho.vl_total) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     WHERE  empenho.dt_empenho BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho
               ON  empenho.cod_despesa = despesa.cod_despesa
              AND  empenho.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  SUM(empenho_anulado_item.vl_anulado) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     WHERE  empenho.dt_empenho BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho_anulado
            ON  empenho_anulado.cod_despesa = despesa.cod_despesa
           AND  empenho_anulado.exercicio = despesa.exercicio
         WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
           AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
          -- Verificar a rotina para implementar esse filtro
          -- AND  (empenho.vl_total <> 0.00 OR empenho_anulado.vl_total <> 0.00 OR suplementacao.valor <> 0.00 OR reducao.valor <> 0.00)
           AND  despesa.cod_despesa <> 24
      GROUP BY  despesa.cod_despesa,despesa.exercicio,despesa.cod_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao, cod_natureza, numero_pao, descricao_unidade, pao.nom_pao, elemento_despesa
      order BY  despesa.cod_despesa,despesa.exercicio,despesa.cod_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao, cod_natureza, numero_pao, descricao_unidade, pao.nom_pao, elemento_despesa
    ";

        return $stSql;
    }

    public function recuperaDespesaPorRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDespesaPorRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDespesaPorRecurso()
    {
        $stSql = "
        SELECT
                '12'    AS     tipo_registro
             ,  despesa.cod_programa
             ,  despesa.cod_despesa
             ,  despesa.num_orgao
             ,  despesa.num_unidade
             ,  despesa.cod_funcao
             ,  despesa.cod_subfuncao
             ,  SUBSTR(despesa.num_pao,1,1)     AS  cod_natureza
             ,  SUBSTR(despesa.num_pao,2,3)     AS  numero_pao
             ,  orcamento.orgao.nom_orgao   AS  descricao_unidade
             ,  pao.nom_pao
             ,  SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6)    AS  elemento_despesa
             ,  recurso_direto.codigo_tc AS codigo_fonte_recurso
             ,  SUM(despesa.vl_original) as vl_orcado
             ,  SUM(suplementacao.valor) as vl_suplementado
             ,  SUM(reducao.valor) as vl_reduzido
             ,  SUM(empenho.vl_total) as vl_empenho
             ,  SUM(empenho_anulado.vl_total) as vl_empenho_anulado
             ,  '' AS espacador
             ,  '0' as numero_sequencial

";
             if ( $this->getDado('dtInicio') != '01/01/2007' ) {

             $stSql.= ",  (   SELECT  ( SUM(odespesa.vl_original) + SUM(osuplementacao.valor) - SUM(oreducao.valor) - SUM(oempenho.vl_empenho) + SUM(oempenho.vl_empenho_anulado) )
                      FROM  orcamento.despesa AS odespesa
                 LEFT JOIN  (   SELECT  suplementacao.dt_suplementacao
                                     ,  suplementacao_suplementada.cod_despesa
                                     ,  SUM(suplementacao_suplementada.valor) AS valor
                                     ,  suplementacao_suplementada.exercicio
                                  FROM  orcamento.suplementacao
                            INNER JOIN  orcamento.suplementacao_suplementada
                                    ON  suplementacao_suplementada.cod_suplementacao = suplementacao.cod_suplementacao
                                   AND  suplementacao_suplementada.exercicio = suplementacao.exercicio
                                 WHERE  NOT EXISTS (    SELECT  1
                                                          FROM  orcamento.suplementacao_anulada
                                                         WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                           AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                    )
                              GROUP BY  dt_suplementacao, cod_despesa, suplementacao_suplementada.exercicio
                            ) AS osuplementacao
                        ON  osuplementacao.cod_despesa = odespesa.cod_despesa
                       AND  osuplementacao.exercicio = odespesa.exercicio
                       AND  osuplementacao.dt_suplementacao >= '".$this->getDado('exercicio')."-01-01'::date
                       AND  osuplementacao.dt_suplementacao < to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                 LEFT JOIN  (   SELECT  suplementacao.dt_suplementacao
                                     ,  suplementacao_reducao.cod_despesa
                                     ,  SUM(suplementacao_reducao.valor) AS valor
                                     ,  suplementacao_reducao.exercicio
                                  FROM  orcamento.suplementacao
                            INNER JOIN  orcamento.suplementacao_reducao
                                    ON  suplementacao_reducao.cod_suplementacao = suplementacao.cod_suplementacao
                                   AND  suplementacao_reducao.exercicio = suplementacao.exercicio
                                 WHERE  NOT EXISTS (    SELECT  1
                                                          FROM  orcamento.suplementacao_anulada
                                                         WHERE  suplementacao_anulada.exercicio = suplementacao.exercicio
                                                           AND  suplementacao_anulada.cod_suplementacao = suplementacao.cod_suplementacao
                                                   )
                              GROUP BY  dt_suplementacao, cod_despesa, suplementacao_reducao.exercicio
                            ) AS oreducao
                        ON  oreducao.cod_despesa = odespesa.cod_despesa
                       AND  oreducao.exercicio = odespesa.exercicio
                       AND  oreducao.dt_suplementacao >= '".$this->getDado('exercicio')."-01-01'::date
                       AND  oreducao.dt_suplementacao < to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                 LEFT JOIN  (
                                SELECT  SUM(vl_total) AS vl_empenho
                                     ,  SUM(vl_anulado) AS vl_empenho_anulado
                                     ,  pre_empenho_despesa.cod_despesa
                                     ,  pre_empenho_despesa.exercicio
                                  FROM  empenho.pre_empenho_despesa
                            INNER JOIN  empenho.pre_empenho
                                    ON  pre_empenho.exercicio = pre_empenho_despesa.exercicio
                                   AND  pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                            INNER JOIN  empenho.item_pre_empenho
                                    ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                                   AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             LEFT JOIN  empenho.empenho_anulado_item
                                    ON  empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                                   AND  empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                            INNER JOIN  empenho.empenho
                                    ON  empenho.exercicio = pre_empenho.exercicio
                                   AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                 WHERE  empenho.dt_empenho >= '".$this->getDado('exercicio')."-01-01'::date
                                   AND  empenho.dt_empenho < to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                              GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                            ) as oempenho
                        ON  oempenho.cod_despesa = odespesa.cod_despesa
                       AND  oempenho.exercicio = odespesa.exercicio
                     WHERE  odespesa.exercicio = despesa.exercicio
                       AND  odespesa.cod_despesa = despesa.cod_despesa
                )   AS  vl_saldo_anterior
          ";
        } else {
            $stSql.= " , 0.00 as vl_saldo_anterior ";
        }

       $stSql.= "



          FROM  orcamento.despesa
    INNER JOIN  orcamento.conta_despesa
            ON  conta_despesa.exercicio = despesa.exercicio
           AND  conta_despesa.cod_conta = despesa.cod_conta
    INNER JOIN  orcamento.recurso
            ON  recurso.exercicio = despesa.exercicio
           AND  recurso.cod_recurso = despesa.cod_recurso
    INNER JOIN  orcamento.recurso_direto
            ON  recurso_direto.exercicio = despesa.exercicio
           AND  recurso_direto.cod_recurso = despesa.cod_recurso
    INNER JOIN  orcamento.orgao
            ON  orcamento.orgao.exercicio = orcamento.despesa.exercicio
           AND  orcamento.orgao.num_orgao = orcamento.despesa.num_orgao
    INNER JOIN  orcamento.pao
            ON  pao.exercicio = despesa.exercicio
           AND  pao.num_pao = despesa.num_pao
     LEFT JOIN  (   SELECT
                            suplementacao_suplementada.cod_despesa
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
                       AND  suplementacao.dt_suplementacao BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  suplementacao_suplementada.cod_despesa, suplementacao_suplementada.exercicio
                )   AS  suplementacao
            ON  suplementacao.cod_despesa = despesa.cod_despesa
           AND  suplementacao.exercicio = despesa.exercicio
     LEFT JOIN  (   SELECT  suplementacao.dt_suplementacao
                         ,  suplementacao_reducao.cod_despesa
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
                       AND  suplementacao.dt_suplementacao BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  suplementacao.dt_suplementacao, suplementacao_reducao.cod_despesa, suplementacao_reducao.exercicio
                )   AS  reducao
            ON  reducao.cod_despesa = despesa.cod_despesa
           AND  reducao.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  SUM(item_pre_empenho.vl_total) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.item_pre_empenho
                        ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                       AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     WHERE  empenho.dt_empenho  BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho
               ON  empenho.cod_despesa = despesa.cod_despesa
              AND  empenho.exercicio = despesa.exercicio
     LEFT JOIN  (
                    SELECT  SUM(empenho_anulado_item.vl_anulado) AS vl_total
                         ,  pre_empenho_despesa.cod_despesa
                         ,  pre_empenho_despesa.exercicio
                      FROM  empenho.pre_empenho_despesa
                INNER JOIN  empenho.pre_empenho
                        ON  empenho.pre_empenho.exercicio = pre_empenho_despesa.exercicio
                       AND  empenho.pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                INNER JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado_item.exercicio = pre_empenho.exercicio
                       AND  empenho_anulado_item.cod_pre_empenho = pre_empenho.cod_pre_empenho
                INNER JOIN  empenho.empenho
                        ON  empenho.exercicio = pre_empenho.exercicio
                       AND  empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     WHERE  empenho.dt_empenho  BETWEEN to_date('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  GROUP BY  pre_empenho_despesa.exercicio, pre_empenho_despesa.cod_despesa

                ) AS  empenho_anulado
            ON  empenho_anulado.cod_despesa = despesa.cod_despesa
           AND  empenho_anulado.exercicio = despesa.exercicio
         WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
           AND  despesa.cod_entidade IN (".$this->getDado('cod_entidade').")
          -- Verificar a rotina para implementar esse filtro
          -- AND  (empenho.vl_total <> 0.00 OR empenho_anulado.vl_total <> 0.00 OR suplementacao.valor <> 0.00 OR reducao.valor <> 0.00)
           AND  despesa.cod_despesa <> 24
      GROUP BY  despesa.cod_despesa,despesa.exercicio,despesa.cod_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao, cod_natureza, numero_pao, descricao_unidade, pao.nom_pao, elemento_despesa, codigo_fonte_recurso
      order BY  despesa.cod_despesa,despesa.exercicio,despesa.cod_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao, cod_natureza, numero_pao, descricao_unidade, pao.nom_pao, elemento_despesa, codigo_fonte_recurso
    ";

        return $stSql;
    }
}
