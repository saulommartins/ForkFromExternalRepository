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
 * Data de Criação: 12/10/2007

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Diego Barbosa Victoria

 * @package URBEM
 * @subpackage Mapeamento

 $Id:$

 * Casos de uso: uc-06.06.00
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTRNEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTRNEmpenho()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function recuperaDadosEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosEmpenho()
    {
        $stSql .= "
    SELECT num_unidade
         , exercicio
         , num_orgao
         , cod_funcao
         , cod_subfuncao
         , cod_programa
         , num_pao
         , tipo_pao
         , cod_recurso
         , estrutural
         , nom_cgm
         , cod_empenho
         , data_empenho
         , cpf_cnpj
         , pf_pj
         , tipo_empenho
         , licitacao
         , valor_empenhado
         , fundef
         , royalties
         , cod_entidade
         , LPAD(cod_entidade::VARCHAR, 2, '0')||LPAD(num_unidade::VARCHAR, 3, '0') AS classificacao_institucional
         , LPAD(cod_funcao::VARCHAR, 2, '0')||' '||LPAD(cod_subfuncao::VARCHAR, 3, '0') as classificacao_funcional
      FROM (
          SELECT
                   CASE WHEN des.exercicio IS NOT NULL THEN des.exercicio ELSE res.exercicio end as exercicio
                  ,CASE WHEN des.num_orgao IS NOT NULL THEN des.num_orgao ELSE res.num_orgao end as num_orgao
                  ,CASE WHEN des.num_unidade IS NOT NULL THEN des.num_unidade ELSE res.num_unidade end as num_unidade
                  ,CASE WHEN des.cod_funcao IS NOT NULL THEN des.cod_funcao ELSE res.cod_funcao end as cod_funcao
                  ,CASE WHEN des.cod_subfuncao IS NOT NULL THEN des.cod_subfuncao ELSE res.cod_subfuncao end as cod_subfuncao
                  ,CASE WHEN des.cod_programa IS NOT NULL THEN des.cod_programa ELSE res.cod_programa end as cod_programa
                  ,CASE WHEN des.num_pao IS NOT NULL THEN des.num_pao ELSE res.num_pao end as num_pao
                  ,CASE WHEN des.tipo_pao IS NOT NULL THEN des.tipo_pao ELSE res.tipo_pao end as tipo_pao
                  ,CASE WHEN des.cod_recurso IS NOT NULL THEN des.cod_recurso ELSE res.cod_recurso end as cod_recurso
                  ,CASE WHEN des.estrutural IS NOT NULL THEN des.estrutural ELSE res.estrutural end as estrutural
                  ,cgm.nom_cgm
                  ,emp.cod_empenho
                  ,to_char(emp.dt_empenho,'dd/mm/yyyy') as data_empenho
                  ,case when  pf.cpf is not null  then pf.cpf
                        when pj.cnpj is not null  then pj.cnpj
                         else ''
                  end as cpf_cnpj
                  ,case when  pf.numcgm is not null then 1
                         else 0
                  end as pf_pj
                  ,case   when pre.cod_tipo = 1 then 'O'
                          when pre.cod_tipo = 3 then 'E'
                          when pre.cod_tipo = 2 then 'G'
                  end as tipo_empenho
                  ,'' as licitacao
                  ,valor_empenhado
                  ,fundeb.codigo AS fundef
                  ,royalties.codigo AS royalties
                  ,emp.cod_entidade

          FROM  empenho.empenho AS emp

        LEFT JOIN (
                    SELECT  fundeb.codigo AS codigo
                            , fundeb_empenho.cod_empenho AS cod_empenho
                            , fundeb_empenho.cod_entidade AS cod_entidade
                            , fundeb_empenho.exercicio AS exercicio
                        FROM tcern.fundeb_empenho
                        JOIN tcern.fundeb
                          ON fundeb.cod_fundeb = fundeb_empenho.cod_fundeb
                   ) AS fundeb
               ON (fundeb.cod_empenho  = emp.cod_empenho
              AND fundeb.cod_entidade = emp.cod_entidade
              AND fundeb.exercicio    = emp.exercicio
                  )

        LEFT JOIN (
                    SELECT  royalties.codigo AS codigo
                            , royalties_empenho.cod_empenho AS cod_empenho
                            , royalties_empenho.cod_entidade AS cod_entidade
                            , royalties_empenho.exercicio AS exercicio
                        FROM tcern.royalties_empenho
                        JOIN tcern.royalties
                          ON royalties.cod_royalties = royalties_empenho.cod_royalties
                   ) AS royalties
               ON royalties.cod_empenho  = emp.cod_empenho
              AND royalties.cod_entidade = emp.cod_entidade
              AND royalties.exercicio    = emp.exercicio

        , sw_cgm AS cgm
        LEFT JOIN sw_cgm_pessoa_fisica as pf
               ON ( cgm.numcgm = pf.numcgm )

        LEFT JOIN sw_cgm_pessoa_juridica as pj
               ON ( cgm.numcgm = pj.numcgm )

                ,empenho.pre_empenho AS pre
        LEFT JOIN (
                    SELECT res.exercicio
                          ,res.cod_pre_empenho
                          ,res.num_orgao
                          ,res.num_unidade
                          ,res.cod_funcao
                          ,res.cod_subfuncao
                          ,res.cod_programa
                          ,res.num_pao
                          ,orcamento.fn_consulta_tipo_pao(res.exercicio,res.num_pao) as tipo_pao
                          ,res.recurso as cod_recurso
                          ,res.cod_estrutural as estrutural
                        FROM empenho.restos_pre_empenho as res
                       WHERE res.exercicio = '".$this->getDado('exercicio')."'
                   ) as res
               ON ( pre.exercicio = res.exercicio
              AND pre.cod_pre_empenho = res.cod_pre_empenho
                  )
        LEFT JOIN (
                    SELECT
                            des.exercicio
                            ,ped.cod_pre_empenho
                            ,des.num_orgao
                            ,des.num_unidade
                            ,des.cod_funcao
                            ,des.cod_subfuncao
                            ,p_programa.num_programa AS cod_programa
                            ,acao.num_acao AS num_pao
                            ,orcamento.fn_consulta_tipo_pao(des.exercicio,des.num_pao) as tipo_pao
                            ,des.cod_recurso
                            ,substr(replace(cde.cod_estrutural,'.',''),1,6) as estrutural
                        FROM  empenho.pre_empenho_despesa AS ped
                             ,orcamento.despesa           AS des
                             INNER JOIN orcamento.programa
                                ON des.exercicio = programa.exercicio
                               AND des.cod_programa = programa.cod_programa
                             INNER JOIN orcamento.programa_ppa_programa
                                ON programa_ppa_programa.exercicio = programa.exercicio
                               AND programa_ppa_programa.cod_programa = programa.cod_programa
                             INNER JOIN ppa.programa AS p_programa
                                ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
                             INNER JOIN orcamento.pao                                              
                                ON des.exercicio = pao.exercicio                               
                               AND des.num_pao = pao.num_pao
                             INNER JOIN orcamento.pao_ppa_acao
                                ON pao_ppa_acao.exercicio = pao.exercicio
                               AND pao_ppa_acao.num_pao = pao.num_pao
                             INNER JOIN ppa.acao
                                ON pao_ppa_acao.cod_acao = acao.cod_acao
                             ,orcamento.conta_despesa     AS cde
                       WHERE ped.exercicio = '".$this->getDado('exercicio')."'
                         AND  ped.exercicio       = cde.exercicio
                         AND  ped.cod_conta       = cde.cod_conta
                         AND  ped.exercicio       = des.exercicio
                         AND  ped.cod_despesa     = des.cod_despesa
                   ) AS des
               ON ( pre.exercicio = des.exercicio
              AND pre.cod_pre_empenho = des.cod_pre_empenho
                    )
                  ,(
                      SELECT   exercicio
                              ,cod_pre_empenho
                              ,lpad(replace(coalesce(sum(vl_total),'0')::VARCHAR,'.',''),14,'0') as valor_empenhado
                      FROM    empenho.item_pre_empenho as ipe
                      WHERE   exercicio = '".$this->getDado('exercicio')."'
                      GROUP BY exercicio, cod_pre_empenho
                  ) as sume

          WHERE   emp.exercicio       = pre.exercicio
          AND     emp.cod_pre_empenho = pre.cod_pre_empenho
          AND     pre.cgm_beneficiario= cgm.numcgm
          AND     pre.exercicio       = sume.exercicio
          AND     pre.cod_pre_empenho = sume.cod_pre_empenho
          AND     emp.exercicio = '".$this->getDado('exercicio')."'
    ".($this->getDado('inCodEntidade')? " AND emp.cod_entidade IN (".$this->getDado('inCodEntidade').") ":"")."
          AND    emp.dt_empenho BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('stDataFinal')."','dd/mm/yyyy')
          ORDER BY emp.exercicio
                  ,emp.cod_empenho
                  ,emp.dt_empenho
          ) as tabela
        ";

        return $stSql;
    }

    public function recuperaDadosPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosPagamento()
    {
        $stSql = " SELECT   cod_entidade
                            , cod_empenho
                            , data_pagamento
                            , num_serie
                            , num_nota
                            , data_nota
                            , cod_validacao
                            , modelo
                            , lpad(replace(coalesce(vl_pago,'0')::VARCHAR,'.',''),14,'0') as vl_pago
                            , cpf_cnpj
                            , conta_corrente
                            , CASE WHEN ordem_bancaria <> ''
                                    THEN ordem_bancaria
                                    ELSE 'pagamento'
                            END AS ordem_bancaria

                        FROM tcern.fn_exportacao_pagamento(CAST('".$this->getDado('exercicio')."' AS varchar),
                                                '".$this->getDado('inCodEntidade')."',
                                                CAST('".$this->getDado('stDataInicial')."' AS varchar),
                                                CAST('".$this->getDado('stDataFinal')."' AS varchar))

                        ORDER BY data_pagamento
        ";

        return $stSql;
    }

    public function recuperaDadosAnulacoes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosAnulacoes().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAnulacoes()
    {
        $stSql .= "
          SELECT   ean.exercicio
                  ,ean.cod_entidade
                  ,ean.cod_empenho
                  ,lpad(replace((SELECT coalesce(sum(vl_anulado),'0')::VARCHAR
                      FROM    empenho.empenho_anulado_item eai
                      WHERE   ean.exercicio   = eai.exercicio
                      AND     ean.cod_entidade= eai.cod_entidade
                      AND     ean.cod_empenho = eai.cod_empenho
                      AND     ean.timestamp   = eai.timestamp
                  ),'.',''),14,'0') as valor_anulado
                  ,to_char(ean.timestamp,'dd/mm/yyyy') as data_anulacao
                  ,ean.oid as num_anulacao
                  ,ean.motivo
          FROM     empenho.empenho_anulado    as ean
          WHERE   ean.exercicio = '".$this->getDado('exercicio')."'
        ".($this->getDado('inCodEntidade')? " AND ean.cod_entidade IN (".$this->getDado('inCodEntidade').")  ":"")."
          AND    to_date(to_char(ean.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('stDataFinal')."','dd/mm/yyyy')
          ORDER BY ean.cod_entidade
                  ,ean.cod_empenho
                  , data_anulacao ";

        return $stSql;
    }

    public function recuperaDadosAnulacoesPagamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosAnulacoesPagamento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAnulacoesPagamento()
    {
        // Nao apagar o que esta comentado na query para caso seja necessario em alguma revisao do codigo
        $stSql .= " SELECT nota_liquidacao_paga_anulada.exercicio                                               AS exercicio
                           , TO_CHAR (nota_liquidacao_paga_anulada.timestamp_anulada,'dd/mm/yyyy')              AS data_anulacao
                           , LPAD (REPLACE(COALESCE(nota_liquidacao_paga_anulada.vl_anulado,'0')::VARCHAR,'.',''),14,'0')  AS valor_anulado
                           , nota_liquidacao.cod_empenho                                                        AS cod_empenho
                           , nota_liquidacao_paga_anulada.cod_entidade                                          AS cod_entidade
                           --, SUBSTRING(lancamento.complemento, (position('-' in lancamento.complemento)+2))   AS ordem_bancaria
                           , CASE WHEN nota_liquidacao_paga.observacao <> ''
                                    THEN nota_liquidacao_paga.observacao
                                    ELSE 'pagamento'
                            END AS ordem_bancaria
                           , pagamento_liquidacao_nota_liquidacao_paga.cod_ordem                                AS ordem_pagamento

                        FROM empenho.nota_liquidacao_paga_anulada
                        JOIN empenho.nota_liquidacao_paga
                          ON nota_liquidacao_paga.exercicio = nota_liquidacao_paga_anulada.exercicio
                         AND nota_liquidacao_paga.cod_nota = nota_liquidacao_paga_anulada.cod_nota
                         AND nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade
                         AND nota_liquidacao_paga.timestamp = nota_liquidacao_paga_anulada.timestamp
                        JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                          ON pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota = nota_liquidacao_paga.cod_nota
                         AND pagamento_liquidacao_nota_liquidacao_paga.exercicio = nota_liquidacao_paga.exercicio
                         AND pagamento_liquidacao_nota_liquidacao_paga.timestamp = nota_liquidacao_paga.timestamp
                        JOIN empenho.nota_liquidacao
                          ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                         AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                         AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota
                        /*JOIN contabilidade.pagamento
                          ON pagamento.exercicio = nota_liquidacao_paga.exercicio
                         AND pagamento.cod_entidade = nota_liquidacao_paga.cod_entidade
                         AND pagamento.timestamp = nota_liquidacao_paga.timestamp
                         AND pagamento.cod_nota = nota_liquidacao_paga.cod_nota
                        JOIN contabilidade.lancamento_empenho
                          ON lancamento_empenho.exercicio = pagamento.exercicio
                         AND lancamento_empenho.cod_entidade = pagamento.cod_entidade
                         AND lancamento_empenho.tipo = pagamento.tipo
                         AND lancamento_empenho.cod_lote = pagamento.cod_lote
                         AND lancamento_empenho.sequencia = pagamento.sequencia
                        JOIN contabilidade.lancamento
                          ON lancamento.exercicio = lancamento_empenho.exercicio
                         AND lancamento.cod_entidade = lancamento_empenho.cod_entidade
                         AND lancamento.tipo = lancamento_empenho.tipo
                         AND lancamento.cod_lote = lancamento_empenho.cod_lote
                         AND lancamento.sequencia = lancamento_empenho.sequencia*/

                        WHERE nota_liquidacao.exercicio_empenho = '".$this->getDado('exercicio')."'
                        ".($this->getDado('inCodEntidade')? " AND nota_liquidacao_paga_anulada.cod_entidade IN (".$this->getDado('inCodEntidade').")  ":"")."
                        AND TO_DATE(TO_CHAR(nota_liquidacao_paga_anulada.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') NOT BETWEEN
                        TO_DATE('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."','dd/mm/yyyy')
                        AND TO_DATE(TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN
                        TO_DATE('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('stDataFinal')."','dd/mm/yyyy')

                        /*AND lancamento.tipo = 'P'
                        AND lancamento_empenho.estorno = 'f'*/

                        ORDER BY nota_liquidacao_paga_anulada.cod_entidade
                                , data_anulacao

          /*SELECT   nlpa.exercicio
                  ,to_char(nlpa.timestamp_anulada,'dd/mm/yyyy') as data_anulacao
                  ,lpad(replace(coalesce(nlpa.vl_anulado,0),'.',''),14,'0') as valor_anulado
                  ,nlpa.oid as num_anulacao
                  --,nlpa1.oid as ordem_bancaria
                  ,nliq.cod_empenho
                  ,nlpa.cod_entidade as cod_entidade
                  ,lanc.complemento as ordem_bancaria
          FROM     empenho.nota_liquidacao_paga_anulada as nlpa
                  ,empenho.nota_liquidacao_paga   as nlpa1
                  ,empenho.nota_liquidacao        as nliq
                  ,contabilidade.lancamento       as lanc
          WHERE    nliq.exercicio_empenho     = '".$this->getDado('exercicio')."'
        ".($this->getDado('inCodEntidade')? " AND nlpa.cod_entidade IN (".$this->getDado('inCodEntidade').")  ":"")."
            AND    to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyyy') AND to_date('".$this->getDado('stDataFinal')."','dd/mm/yyyy')
            AND    nlpa.exercicio     = nlpa1.exercicio
            AND    nlpa.cod_entidade  = nlpa1.cod_entidade
            AND    nlpa.cod_nota      = nlpa1.cod_nota
            AND    nlpa.timestamp     = nlpa1.timestamp
            AND    nlpa1.exercicio    = nliq.exercicio
            AND    nlpa1.cod_entidade = nliq.cod_entidade
            AND    nlpa1.cod_nota     = nliq.cod_nota
            AND    nlpa.exercicio     = lanc.exercicio
            AND    nlpa.cod_entidade  = lanc.cod_entidade
          ORDER BY nlpa.cod_entidade
                  ,nlpa.oid*/
        ";

        return $stSql;
    }

}
