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
    * Data de Criacão: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBDiverso.class.php 39086 2009-03-25 12:07:39Z andrem $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criacão: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCEAMRecibos extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMRecibos()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaRecibos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRecibos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRecibos()
    {
        $stSql = " SELECT exercicio                          --ano do empenho para a despesa
           , unidade                            --codigo unidade orcamentaria
           , cod_empenho                        --numero do empenho em questao
           , dt_empenho
           , tipo_juridico_credor
           , cpf_cnpj                           --CGC ou CPF do credor
           , nome_credor
           , numero_recibo
           , valor_total_documento
           , sum(valor_total_pago) as valor_recibo
           , data_documento
           , data_pagamento
           , descricao_objeto
           , tipo_recibo
        FROM (
                   SELECT despesa.exercicio
                        , LPAD(despesa.num_orgao::varchar,3,'0') || LPAD(despesa.num_unidade::varchar,2,'0') as unidade
                        , empenho.cod_empenho
                        , TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy')  AS dt_empenho
                        , sw_cgm_documento.tipo_juridico_credor     AS tipo_juridico_credor
                        , CASE
                             WHEN  sw_cgm_documento.documento IS NOT NULL THEN LPAD(sw_cgm_documento.documento,14,'0')
                             ELSE  LPAD(entidade_documento.documento::varchar,14,'0')
                          END                                       AS cpf_cnpj
                        , sw_cgm_documento.nom_cgm                  AS nome_credor
                        , tipo_documento_recibo.numero              AS numero_recibo
                        , documento.vl_total                        AS valor_total_documento
                        , nota_liquidacao_paga.vl_pago              AS valor_total_pago
                        , to_char(tipo_documento_recibo.data,'dd/mm/yyyy') AS data_documento
                        , to_char(nota_liquidacao_paga.timestamp::date,'dd/mm/yyyy') AS data_pagamento
                        , empenho.pre_empenho.descricao                       AS descricao_objeto
                        , tipo_documento_recibo.cod_tipo_recibo               AS tipo_recibo
                     FROM  empenho.empenho
               INNER JOIN empenho.nota_liquidacao
                       ON nota_liquidacao.cod_empenho        = empenho.cod_empenho
                      AND nota_liquidacao.cod_entidade       = empenho.cod_entidade
                      AND nota_liquidacao.exercicio          = empenho.exercicio
                      AND nota_liquidacao.exercicio_empenho  = empenho.exercicio
               INNER JOIN (
                                SELECT nota_liquidacao_paga.*
                                  FROM empenho.nota_liquidacao_paga
                            INNER JOIN (   SELECT cod_entidade
                                                , cod_nota
                                                , exercicio
                                              --  , MAX(timestamp) AS timestamp
                                                , timestamp

                                             FROM empenho.nota_liquidacao_paga
                                         GROUP BY cod_entidade
                                                , cod_nota
                                                , exercicio
                                                ,timestamp
                                       ) AS maxtime
                                    ON maxtime.cod_entidade = nota_liquidacao_paga.cod_entidade
                                   AND maxtime.cod_nota     = nota_liquidacao_paga.cod_nota
                                   AND maxtime.exercicio    = nota_liquidacao_paga.exercicio
                                   AND maxtime.timestamp    = nota_liquidacao_paga.timestamp
                          ) AS nota_liquidacao_paga
                       ON nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                      AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                      AND nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                LEFT JOIN empenho.nota_liquidacao_paga_anulada
                       ON nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                      AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                      AND nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                      AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
               INNER JOIN tceam.documento
                       ON documento.cod_nota     = nota_liquidacao.cod_nota
                      AND documento.cod_entidade = nota_liquidacao.cod_entidade
                      AND documento.exercicio    = nota_liquidacao.exercicio
               INNER JOIN tceam.tipo_documento_recibo
                       ON tipo_documento_recibo.cod_documento = documento.cod_documento
               INNER JOIN tceam.tipo_documento
                       ON tipo_documento.cod_tipo = documento.cod_tipo
               INNER JOIN empenho.pre_empenho
                       ON pre_empenho.exercicio = empenho.exercicio
                      AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                LEFT JOIN (
                             SELECT CASE
                                       WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL    THEN sw_cgm_pessoa_fisica.cpf
                                       WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj
                                       ELSE NULL
                                    END AS documento
                                  , CASE
                                       WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL    THEN '1'
                                       WHEN sw_cgm_pessoa_juridica.numcgm IS NOT NULL THEN '2'
                                    END as tipo_juridico_credor
                                  , sw_cgm.numcgm
                                  , sw_cgm.nom_cgm
                               FROM sw_cgm
                          LEFT JOIN sw_cgm_pessoa_fisica
                                 ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                          LEFT JOIN sw_cgm_pessoa_juridica
                                 ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                          ) AS sw_cgm_documento
                       ON sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario
                LEFT JOIN (
                               SELECT entidade.exercicio
                                    , entidade.cod_entidade
                                    , CASE
                                         WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL    THEN sw_cgm_pessoa_fisica.cpf
                                         WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj
                                         ELSE NULL
                                      END AS documento
                                 FROM orcamento.entidade
                            LEFT JOIN sw_cgm_pessoa_fisica
                                   ON sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                   ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                          ) AS entidade_documento
                       ON entidade_documento.exercicio = empenho.exercicio
                      AND entidade_documento.cod_entidade = empenho.cod_entidade
               INNER JOIN (
                              SELECT exercicio
                                   , cod_pre_empenho
                                   , SUM(vl_total) as valor_empenhado
                                FROM empenho.item_pre_empenho
                            GROUP BY exercicio, cod_pre_empenho
                          ) AS item_pre_empenho
                       ON item_pre_empenho.exercicio = pre_empenho.exercicio
                      AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                LEFT JOIN empenho.item_pre_empenho_julgamento as ipej
                       ON item_pre_empenho.exercicio       = ipej.exercicio
                      AND item_pre_empenho.cod_pre_empenho = ipej.cod_pre_empenho
               INNER JOIN empenho.pre_empenho_despesa
                       ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                      AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
               INNER JOIN orcamento.despesa
                       ON despesa.exercicio = pre_empenho_despesa.exercicio
                      AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
               INNER JOIN orcamento.conta_despesa
                       ON conta_despesa.exercicio = despesa.exercicio
                      AND conta_despesa.cod_conta = despesa.cod_conta
               INNER JOIN orcamento.recurso
                       ON recurso.cod_recurso = despesa.cod_recurso
                      AND recurso.exercicio = despesa.exercicio
                    WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                      AND TO_CHAR(nota_liquidacao_paga.timestamp::date,'mm') = '".$this->getDado('inMes')."'

                      /*AND (
                             (     nota_liquidacao_paga_anulada.cod_nota IS NOT NULL
                               AND nota_liquidacao_paga_anulada.timestamp  = nota_liquidacao_paga.timestamp
                               AND nota_liquidacao_paga_anulada.vl_anulado < nota_liquidacao_paga.vl_pago
                             )
                             OR
                             (     nota_liquidacao_paga_anulada.cod_nota IS NOT NULL
                               AND nota_liquidacao_paga_anulada.timestamp  < nota_liquidacao_paga.timestamp
                                )
                             OR (  nota_liquidacao_paga_anulada.cod_nota IS NULL
                                )
                          )*/
                    AND nota_liquidacao_paga_anulada.cod_nota IS NULL
                    AND despesa.cod_entidade in (".$this->getDado('stEntidades').")
                 ORDER BY conta_despesa.exercicio, empenho.cod_empenho
             ) as tbl
    GROUP BY exercicio
           , unidade
           , cod_empenho
           , dt_empenho
           , tipo_juridico_credor
           , cpf_cnpj
           , nome_credor
           , numero_recibo
           , valor_total_documento
           , data_documento
           , data_pagamento
           , descricao_objeto
           , tipo_recibo
    ORDER BY cod_empenho";

        return $stSql;
    }
}
