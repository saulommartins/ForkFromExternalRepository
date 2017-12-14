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
    * Data de Criação: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEAMEmpenho.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCEAMEmpenho extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMEmpenho()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaEmpenhos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEmpenhos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEmpenhos()
    {
        $stSql = " SELECT *
        FROM (
        SELECT
             exercicio --ano do empenho para a despesa
           , cod_entidade
           , unidade --codigo unidade orcamentaria
           , cod_subfuncao
           , num_programa
           , num_acao --numero do projeto atividade
           , categoria_economica
           , natureza
           -- , cod_modalidade
           , modalidade
           , elemento --cod_elemento
           , cod_empenho --numero do empenho em questao
           , tipo_empenho
           , dt_empenho
           , valor_empenhado
           , valor_empenhado_nao_formato
           , historico
           , nome_credor
           , cpf_cnpj --CGC ou CPF do credor
           , cod_licitacao --processo_licitatorio
           , num_contrato --numero do contrato a que se refere o empenho
           , num_convenio
           , cod_fonte --codigo fonte orcamentaria
           , tipo_juridico_credor
           , tipo_projeto_atividade
           , empenho_recursos_antecipados
           , num_empenho_superior --falta definir que valor deve buscar para essa coluna
           , ano_empenho_superior --falta definir que valor deve buscar para essa coluna
           , meta_empenho --falta definir que valor deve buscar para essa coluna
           , nota_liberacao_descentralizacao --falta definir que valor deve buscar para essa coluna
           , 0 as cod_empenho_incorporado


     FROM(

             SELECT  despesa.exercicio
                  ,  despesa.cod_entidade
                  ,  LPAD(despesa.num_orgao::varchar,3,'0') || LPAD(despesa.num_unidade::varchar,2,'0') as unidade
                  ,  despesa.cod_subfuncao
                  ,  programa.num_programa
                  ,  LPAD(acao.num_acao::varchar, 7, '0') as num_acao
                  ,  CASE WHEN atributo_empenho_valor.valor::integer  = 1  THEN 4
                          WHEN atributo_empenho_valor.valor::integer  = 2  THEN 3
                          WHEN atributo_empenho_valor.valor::integer  = 3  THEN 2
                          WHEN atributo_empenho_valor.valor::integer  = 4  THEN 1
                          WHEN atributo_empenho_valor.valor::integer  = 5  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 6  THEN 8
                          WHEN atributo_empenho_valor.valor::integer  = 7  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 8  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 9  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 10 THEN 0
                      END as cod_modalidade
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento

                  ,  empenho.cod_empenho::varchar as cod_empenho
                  ,  CASE WHEN pre_empenho.cod_tipo = 2 THEN 3
                          WHEN pre_empenho.cod_tipo = 3 THEN 2
                          WHEN pre_empenho.cod_tipo = 1 THEN 1
                     END as tipo_empenho
                  ,  TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
                  ,  LPAD(item_pre_empenho.valor_empenhado::varchar,16,'0') as valor_empenhado
                  ,  item_pre_empenho.valor_empenhado as valor_empenhado_nao_formato
                  ,  pre_empenho.descricao as historico
                  ,  sw_cgm_documento.nom_cgm as nome_credor
                  ,  CASE WHEN  sw_cgm_documento.documento IS NOT NULL THEN LPAD(sw_cgm_documento.documento,14,'0')
                          ELSE  LPAD(entidade_documento.documento,14,'0')
                     END as cpf_cnpj
                  , CASE WHEN lcl.cod_licitacao IS NULL THEN 'Sem Licitação' ELSE lcl.cod_licitacao||lcl.exercicio_licitacao END as cod_licitacao
                  , CASE WHEN empenho_contrato.num_contrato IS NULL THEN 'Sem Contrato' ELSE empenho_contrato.num_contrato::varchar END as num_contrato
                  , CASE WHEN empenho_convenio.num_convenio IS NULL THEN 'Sem Convênio' ELSE empenho_convenio.num_convenio::varchar END as num_convenio
                  , recurso.cod_fonte as cod_fonte
                  , sw_cgm_documento.tipo_juridico_credor as tipo_juridico_credor
                  , SUBSTR(despesa.num_pao::varchar, 1, 1) as tipo_projeto_atividade --o primeiro digito do num_pao eh o tipo projeto ou atividade
                  , CASE WHEN categoria_empenho.cod_categoria = 1 THEN 'N' ELSE 'S' END as empenho_recursos_antecipados
                  , ''::varchar as num_empenho_superior --falta definir que valor deve buscar para essa coluna
                  , ''::varchar as ano_empenho_superior --falta definir que valor deve buscar para essa coluna
                  , ''::varchar as meta_empenho --falta definir que valor deve buscar para essa coluna
                  , ''::varchar as nota_liberacao_descentralizacao --falta definir que valor deve buscar para essa coluna

               FROM  empenho.empenho
         INNER JOIN  empenho.pre_empenho
                 ON  pre_empenho.exercicio = empenho.exercicio
                AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN  empenho.categoria_empenho
                 ON  empenho.cod_categoria = categoria_empenho.cod_categoria

                    --num_contrato
          LEFT JOIN  empenho.empenho_contrato
                 ON  empenho.cod_empenho = empenho_contrato.cod_empenho
                AND  empenho.cod_entidade = empenho_contrato.cod_entidade
                AND  empenho.exercicio = empenho_contrato.exercicio

                    --num_convenio
          LEFT JOIN  empenho.empenho_convenio
                 ON  empenho.cod_empenho = empenho_convenio.cod_empenho
                AND  empenho.cod_entidade = empenho_convenio.cod_entidade
                AND  empenho.exercicio = empenho_convenio.exercicio

          INNER JOIN (SELECT atributo_empenho_valor.*
                FROM empenho.atributo_empenho_valor
               INNER JOIN (SELECT cod_pre_empenho, exercicio, max(timestamp) as timestamp, cod_atributo, cod_modulo, cod_cadastro
                     FROM empenho.atributo_empenho_valor
                    WHERE cod_atributo = 101
                      AND cod_modulo = 10
                      AND cod_cadastro = 1
                    GROUP BY cod_pre_empenho, exercicio, cod_atributo, cod_modulo, cod_cadastro) as max_empenho
                  ON max_empenho.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                 AND max_empenho.exercicio     = atributo_empenho_valor.exercicio
                 AND max_empenho.timestamp       = atributo_empenho_valor.timestamp
                 AND max_empenho.cod_atributo    = atributo_empenho_valor.cod_atributo
                 AND max_empenho.cod_modulo      = atributo_empenho_valor.cod_modulo
                 AND max_empenho.cod_cadastro    = atributo_empenho_valor.cod_cadastro
               ) as atributo_empenho_valor
        ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
           AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho



          LEFT JOIN ( SELECT  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                   THEN sw_cgm_pessoa_fisica.cpf
                                   WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                   THEN sw_cgm_pessoa_juridica.cnpj
                                   ELSE NULL
                              END AS documento
                           ,  CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                                   THEN '1'
                                   WHEN sw_cgm_pessoa_juridica.numcgm IS NOT NULL
                                   THEN '2'
                              END as tipo_juridico_credor
                           ,  sw_cgm.numcgm
                           ,  sw_cgm.nom_cgm
                        FROM  sw_cgm
                   LEFT JOIN  sw_cgm_pessoa_fisica
                          ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                   LEFT JOIN  sw_cgm_pessoa_juridica
                          ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                     ) AS sw_cgm_documento
                 ON  sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario
          LEFT JOIN  ( SELECT  entidade.exercicio
                            ,  entidade.cod_entidade
                            ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                    THEN sw_cgm_pessoa_fisica.cpf
                                    WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                    THEN sw_cgm_pessoa_juridica.cnpj
                                    ELSE NULL
                               END AS documento
                         FROM  orcamento.entidade
                    LEFT JOIN  sw_cgm_pessoa_fisica
                           ON  sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                    LEFT JOIN  sw_cgm_pessoa_juridica
                           ON  sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                    ) AS entidade_documento
                 ON  entidade_documento.exercicio = empenho.exercicio
                AND  entidade_documento.cod_entidade = empenho.cod_entidade

         INNER JOIN  ( SELECT  exercicio
                            ,  cod_pre_empenho
                            ,  SUM(vl_total) as valor_empenhado
                         FROM  empenho.item_pre_empenho
                     GROUP BY  exercicio, cod_pre_empenho
                     ) AS item_pre_empenho
                 ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

      LEFT JOIN  empenho.item_pre_empenho_julgamento as ipej
                 ON  item_pre_empenho.exercicio       = ipej.exercicio
                AND  item_pre_empenho.cod_pre_empenho = ipej.cod_pre_empenho

        LEFT JOIN COMPRAS.cotacao_fornecedor_item as cfi
          ON ipej.exercicio     = cfi.exercicio
         AND ipej.cod_cotacao   = cfi.cod_cotacao
         AND ipej.lote      = cfi.lote
         AND ipej.cgm_fornecedor = cfi.cgm_fornecedor

        LEFT JOIN licitacao.cotacao_licitacao as lcl
          ON cfi.cgm_fornecedor = lcl.cgm_fornecedor
         AND cfi.cod_cotacao = lcl.cgm_fornecedor
         AND cfi.exercicio = lcl.exercicio_licitacao
         AND cfi.lote = lcl.lote

        LEFT JOIN licitacao.licitacao as ll
          ON lcl.cod_licitacao = ll.cod_licitacao
         AND lcl.cod_modalidade = ll.cod_modalidade
         AND lcl.cod_entidade = ll.cod_entidade
         AND lcl.exercicio_licitacao = ll.exercicio

         INNER JOIN  empenho.pre_empenho_despesa
                 ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
         INNER JOIN  orcamento.despesa
                 ON  despesa.exercicio = pre_empenho_despesa.exercicio
                AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         INNER JOIN  orcamento.conta_despesa
                 ON  conta_despesa.exercicio = despesa.exercicio
                AND  conta_despesa.cod_conta = despesa.cod_conta
         INNER JOIN  orcamento.recurso
                 ON  recurso.cod_recurso = despesa.cod_recurso
                AND  recurso.exercicio = despesa.exercicio

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

              WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'";

                if ( $this->getDado('inMes') ) {
                    $stSql .= " AND  TO_CHAR(empenho.dt_empenho,'mm') = '".$this->getDado('inMes')."' ";
                }
        if ( $this->getDado('stEntidades') && $this->getDado('inMes') ) {
            $stSql .= " AND despesa.cod_entidade in (".$this->getDado('stEntidades').") ";
        }
        $stSql .= " ORDER BY conta_despesa.exercicio, empenho.cod_empenho

    ) as tbl
    GROUP BY

            exercicio
           , cod_entidade
           , unidade
           , cod_subfuncao
           , num_programa
           , num_acao
           , categoria_economica
           , natureza
--           , cod_modalidade
           , cod_empenho
           , tipo_empenho
           , dt_empenho
           , valor_empenhado
           , valor_empenhado_nao_formato
           , historico
           , nome_credor
           , cpf_cnpj
           , cod_licitacao
           , num_contrato
           , num_convenio
           , cod_fonte
           , tipo_juridico_credor
           , tipo_projeto_atividade
           , elemento
           , empenho_recursos_antecipados
           , num_empenho_superior
           , ano_empenho_superior
           , meta_empenho
           , nota_liberacao_descentralizacao
           , modalidade

\n";

        if ($this->getDado('boIncorporarEmpenhos')) {
          $stSql .= " UNION
                    SELECT
             exercicio --ano do empenho para a despesa
           , cod_entidade
           , unidade --codigo unidade orcamentaria
           , cod_subfuncao
           , num_programa
           , num_acao --numero do projeto atividade
           , categoria_economica
           , natureza
--           , cod_modalidade
           , modalidade
           , elemento --cod_elemento
           , cod_empenho --numero do empenho em questao
           , tipo_empenho
           , dt_empenho
           , LPAD(SUM(valor_empenhado)- SUM(vl_anulado),16,'0') as valor_empenhado
           , SUM(valor_empenhado) - SUM(vl_anulado) as valor_empenhado_nao_formato
--           , valor_empenhado
--           , valor_empenhado_nao_formato
           , historico
           , nom_cgm as nome_credor
           , documento as cpf_cnpj --CGC ou CPF do credor
           , cod_licitacao --processo_licitatorio
           , num_contrato --numero do contrato a que se refere o empenho
           , num_convenio
           , cod_fonte --codigo fonte orcamentaria
           , tipo_juridico_credor
           , tipo_projeto_atividade
           , empenho_recursos_antecipados
           , num_empenho_superior --falta definir que valor deve buscar para essa coluna
           , ano_empenho_superior --falta definir que valor deve buscar para essa coluna
           , meta_empenho --falta definir que valor deve buscar para essa coluna
           , nota_liberacao_descentralizacao --falta definir que valor deve buscar para essa coluna
           , cod_empenho_incorporado


     FROM(

             SELECT  despesa.exercicio
                  ,  despesa.cod_entidade
                  ,  LPAD(despesa.num_orgao::varchar,3,'0') || LPAD(despesa.num_unidade::varchar,2,'0') as unidade
                  ,  despesa.cod_subfuncao
                  ,  programa.num_programa
                  ,  LPAD(acao.num_acao::varchar, 7, '0') as num_acao
                  ,  CASE WHEN atributo_empenho_valor.valor::integer  = 1  THEN 4
                          WHEN atributo_empenho_valor.valor::integer  = 2  THEN 3
                          WHEN atributo_empenho_valor.valor::integer  = 3  THEN 2
                          WHEN atributo_empenho_valor.valor::integer  = 4  THEN 1
                          WHEN atributo_empenho_valor.valor::integer  = 5  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 6  THEN 8
                          WHEN atributo_empenho_valor.valor::integer  = 7  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 8  THEN 6
                          WHEN atributo_empenho_valor.valor = 9  THEN 6
                          WHEN atributo_empenho_valor.valor::integer  = 10 THEN 0
                      END as cod_modalidade
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                  ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento

                  ,  empenho_incorporacao.descricao as cod_empenho
                  ,  empenho_incorporacao.cod_empenho_incorporado
                  ,  1 as tipo_empenho
                  ,  '31/12/'||despesa.exercicio as dt_empenho
                  ,  LPAD(valor_empenhado,16,'0') as valor_empenhado_nao_formato
                  ,  item_pre_empenho.valor_empenhado as valor_empenhado
                  ,  COALESCE(empenho_anulado.vl_anulado, 0.00) as vl_anulado
                  , 'Incorporação de Entidades'::text  as historico
                  ,  entidade_documento.nom_cgm as nome_credor
                  ,  CASE WHEN  sw_cgm_documento.documento IS NOT NULL THEN LPAD(sw_cgm_documento.documento,14,'0')
                          ELSE  LPAD(entidade_documento.documento,14,'0')
                     END as cpf_cnpj
                  , CASE WHEN lcl.cod_licitacao IS NULL THEN 'Sem Licitação' ELSE lcl.cod_licitacao||lcl.exercicio_licitacao END as cod_licitacao
                  , CASE WHEN empenho_contrato.num_contrato IS NULL THEN 'Sem Contrato' ELSE empenho_contrato.num_contrato::varchar END as num_contrato
                  , CASE WHEN empenho_convenio.num_convenio IS NULL THEN 'Sem Convênio' ELSE empenho_convenio.num_convenio::varchar END as num_convenio
                  , recurso.cod_fonte as cod_fonte
                  , '1'::varchar as tipo_juridico_credor
                  , SUBSTR(despesa.num_pao::varchar, 1, 1) as tipo_projeto_atividade --o primeiro digito do num_pao eh o tipo projeto ou atividade
                  , CASE WHEN categoria_empenho.cod_categoria = 1 THEN 'N' ELSE 'S' END as empenho_recursos_antecipados
                  , ''::varchar as num_empenho_superior --falta definir que valor deve buscar para essa coluna
                  , ''::varchar as ano_empenho_superior --falta definir que valor deve buscar para essa coluna
                  , ''::varchar as meta_empenho --falta definir que valor deve buscar para essa coluna
                  , ''::varchar as nota_liberacao_descentralizacao --falta definir que valor deve buscar para essa coluna
                  , entidade_documento.documento
                  , entidade_documento.nom_cgm

               FROM  empenho.empenho
         INNER JOIN  empenho.pre_empenho
                 ON  pre_empenho.exercicio = empenho.exercicio
                AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN  empenho.categoria_empenho
                 ON  empenho.cod_categoria = categoria_empenho.cod_categoria

         INNER JOIN  tceam.empenho_incorporacao
                 ON  empenho_incorporacao.cod_empenho = empenho.cod_empenho
                AND  empenho_incorporacao.cod_entidade = empenho.cod_entidade
                AND  empenho_incorporacao.exercicio = empenho.exercicio

                    --num_contrato
          LEFT JOIN  empenho.empenho_contrato
                 ON  empenho.cod_empenho = empenho_contrato.cod_empenho
                AND  empenho.cod_entidade = empenho_contrato.cod_entidade
                AND  empenho.exercicio = empenho_contrato.exercicio

                    --num_convenio
          LEFT JOIN  empenho.empenho_convenio
                 ON  empenho.cod_empenho = empenho_convenio.cod_empenho
                AND  empenho.cod_entidade = empenho_convenio.cod_entidade
                AND  empenho.exercicio = empenho_convenio.exercicio

          INNER JOIN (SELECT atributo_empenho_valor.*
                FROM empenho.atributo_empenho_valor
               INNER JOIN (SELECT cod_pre_empenho, exercicio, max(timestamp) as timestamp, cod_atributo, cod_modulo, cod_cadastro
                     FROM empenho.atributo_empenho_valor
                    WHERE cod_atributo = 101
                      AND cod_modulo = 10
                      AND cod_cadastro = 1
                    GROUP BY cod_pre_empenho, exercicio, cod_atributo, cod_modulo, cod_cadastro) as max_empenho
                  ON max_empenho.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                 AND max_empenho.exercicio     = atributo_empenho_valor.exercicio
                 AND max_empenho.timestamp       = atributo_empenho_valor.timestamp
                 AND max_empenho.cod_atributo    = atributo_empenho_valor.cod_atributo
                 AND max_empenho.cod_modulo      = atributo_empenho_valor.cod_modulo
                 AND max_empenho.cod_cadastro    = atributo_empenho_valor.cod_cadastro
               ) as atributo_empenho_valor
        ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
           AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho



          LEFT JOIN ( SELECT  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                   THEN sw_cgm_pessoa_fisica.cpf
                                   WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                   THEN sw_cgm_pessoa_juridica.cnpj
                                   ELSE NULL
                              END AS documento
                           ,  CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                                   THEN '1'
                                   WHEN sw_cgm_pessoa_juridica.numcgm IS NOT NULL
                                   THEN '2'
                              END as tipo_juridico_credor
                           ,  sw_cgm.numcgm
                           ,  sw_cgm.nom_cgm
                        FROM  sw_cgm
                   LEFT JOIN  sw_cgm_pessoa_fisica
                          ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                   LEFT JOIN  sw_cgm_pessoa_juridica
                          ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                     ) AS sw_cgm_documento
                 ON  sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario
          LEFT JOIN  ( SELECT  entidade.exercicio
                            ,  entidade.cod_entidade
                            ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                    THEN sw_cgm_pessoa_fisica.cpf
                                    WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                    THEN sw_cgm_pessoa_juridica.cnpj
                                    ELSE NULL
                               END AS documento
                            , sw_cgm.nom_cgm
                         FROM  orcamento.entidade
                    LEFT JOIN  sw_cgm
                           ON  sw_cgm.numcgm = entidade.numcgm
                    LEFT JOIN  sw_cgm_pessoa_fisica
                           ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                    LEFT JOIN  sw_cgm_pessoa_juridica
                           ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                    ) AS entidade_documento
                 ON  entidade_documento.exercicio = empenho.exercicio
                AND  entidade_documento.cod_entidade = empenho.cod_entidade

         INNER JOIN  ( SELECT  exercicio
                            ,  cod_pre_empenho
                            ,  SUM(vl_total) as valor_empenhado
                         FROM  empenho.item_pre_empenho
                     GROUP BY  exercicio, cod_pre_empenho
                     ) AS item_pre_empenho
                 ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

      LEFT JOIN  empenho.item_pre_empenho_julgamento as ipej
                 ON  item_pre_empenho.exercicio       = ipej.exercicio
                AND  item_pre_empenho.cod_pre_empenho = ipej.cod_pre_empenho

        LEFT JOIN (
                    SELECT empenho_anulado.cod_empenho
                         , empenho_anulado.cod_entidade
                         , empenho_anulado.exercicio
                         , SUM(vl_anulado) as vl_anulado
                      FROM empenho.empenho_anulado
                INNER JOIN  empenho.empenho_anulado_item
                        ON  empenho_anulado.cod_empenho  = empenho_anulado_item.cod_empenho
                       AND  empenho_anulado.cod_entidade = empenho_anulado_item.cod_entidade
                       AND  empenho_anulado.exercicio    = empenho_anulado_item.exercicio
                       AND  empenho_anulado.timestamp    = empenho_anulado_item.timestamp
                  GROUP BY  empenho_anulado.cod_empenho
                         , empenho_anulado.cod_entidade
                         , empenho_anulado.exercicio
                  ) as empenho_anulado
                 ON  empenho.cod_empenho  = empenho_anulado.cod_empenho
                AND  empenho.cod_entidade = empenho_anulado.cod_entidade
                AND  empenho.exercicio    = empenho_anulado.exercicio

        LEFT JOIN COMPRAS.cotacao_fornecedor_item as cfi
          ON ipej.exercicio     = cfi.exercicio
         AND ipej.cod_cotacao   = cfi.cod_cotacao
         AND ipej.lote      = cfi.lote
         AND ipej.cgm_fornecedor = cfi.cgm_fornecedor

        LEFT JOIN licitacao.cotacao_licitacao as lcl
          ON cfi.cgm_fornecedor = lcl.cgm_fornecedor
         AND cfi.cod_cotacao = lcl.cgm_fornecedor
         AND cfi.exercicio = lcl.exercicio_licitacao
         AND cfi.lote = lcl.lote

        LEFT JOIN licitacao.licitacao as ll
          ON lcl.cod_licitacao = ll.cod_licitacao
         AND lcl.cod_modalidade = ll.cod_modalidade
         AND lcl.cod_entidade = ll.cod_entidade
         AND lcl.exercicio_licitacao = ll.exercicio

         INNER JOIN  empenho.pre_empenho_despesa
                 ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
         INNER JOIN  orcamento.despesa
                 ON  despesa.exercicio = pre_empenho_despesa.exercicio
                AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
         INNER JOIN  orcamento.conta_despesa
                 ON  conta_despesa.exercicio = despesa.exercicio
                AND  conta_despesa.cod_conta = despesa.cod_conta
         INNER JOIN  orcamento.recurso
                 ON  recurso.cod_recurso = despesa.cod_recurso
                AND  recurso.exercicio = despesa.exercicio
                
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

              WHERE  despesa.exercicio = '".$this->getDado('exercicio')."' AND  despesa.cod_entidade in (".$this->getDado('stCodEntidadesIncorporadas').")  ORDER BY conta_despesa.exercicio, empenho.cod_empenho

    ) as tbl
    GROUP BY

            exercicio
           , cod_entidade
           , unidade
           , cod_subfuncao
           , num_programa
           , num_acao
           , categoria_economica
           , natureza
--           , cod_modalidade
           , cod_empenho
           , tipo_empenho
           , dt_empenho
           , historico
           , nom_cgm
           , documento
           , cod_licitacao
           , num_contrato
           , num_convenio
           , cod_fonte
           , tipo_juridico_credor
           , tipo_projeto_atividade
           , elemento
           , empenho_recursos_antecipados
           , num_empenho_superior
           , ano_empenho_superior
           , meta_empenho
           , nota_liberacao_descentralizacao
           , modalidade
           , cod_empenho_incorporado

          ";
        }

        $stSql .= " ) as tabela
            ORDER BY cod_empenho_incorporado
                   , substr(cod_empenho, position('.' IN cod_empenho)+1)::integer ";

        return $stSql;
    }

    public function montaRecuperaTotalElemento()
    {
        $stSql = "
                   SELECT
                           exercicio --ano do empenho para a despesa
                         , cod_entidade
                         , unidade --codigo unidade orcamentaria
                         , cod_subfuncao
                         , cod_programa
                         , num_pao --numero do projeto atividade
                         , categoria_economica
                         , natureza
                         , modalidade
                         , elemento --cod_elemento
                         , 1 as tipo_empenho
                         , '31/12/' || exercicio as dt_empenho
                         , LPAD(sum(valor_empenhado_nao_formato),16,'0') as valor_empenhado_nao_formato
                         , 'Incorporação de Entidades'  as historico
                         , nom_cgm as nome_credor
                         , documento as cpf_cnpj --CGC ou CPF do credor
                         , cod_licitacao --processo_licitatorio
                         , num_contrato --numero do contrato a que se refere o empenho
                         , num_convenio
                         , cod_fonte --codigo fonte orcamentaria
                         , 1 as tipo_juridico_credor
                         , tipo_projeto_atividade
                         , empenho_recursos_antecipados
                         , num_empenho_superior --falta definir que valor deve buscar para essa coluna
                         , ano_empenho_superior --falta definir que valor deve buscar para essa coluna
                         , meta_empenho --falta definir que valor deve buscar para essa coluna
                         , nota_liberacao_descentralizacao --falta definir que valor deve buscar para essa coluna
                         , (select max(cod_empenho)+1 from empenho.empenho where exercicio = '".$this->getDado('exercicio')."') as cod_empenho

                   FROM(

                           SELECT  despesa.exercicio
                                ,  despesa.cod_entidade
                                ,  LPAD(despesa.num_orgao::varchar,3,'0') || LPAD(despesa.num_unidade::varchar,2,'0') as unidade
                                ,  despesa.cod_subfuncao
                                ,  despesa.cod_programa
                                ,  LPAD(despesa.num_pao::varchar, 7, '0') as num_pao
                                ,  CASE WHEN atributo_empenho_valor.valor::integer = 1  THEN 4
                                WHEN atributo_empenho_valor.valor::integer = 2  THEN 3
                                WHEN atributo_empenho_valor.valor::integer = 3  THEN 2
                                WHEN atributo_empenho_valor.valor::integer = 4  THEN 1
                                WHEN atributo_empenho_valor.valor::integer = 5  THEN 6
                                WHEN atributo_empenho_valor.valor::integer = 6  THEN 8
                                WHEN atributo_empenho_valor.valor::integer= 7  THEN 6
                                WHEN atributo_empenho_valor.valor::integer = 8  THEN 6
                                WHEN atributo_empenho_valor.valor::integer = 9  THEN 6
                                WHEN atributo_empenho_valor.valor::integer = 10 THEN 0
                                    END as cod_modalidade
                                ,  SUBSTR(REPLACE(cod_estrutural,'.',''),1,1) AS categoria_economica
                                ,  SUBSTR(REPLACE(cod_estrutural,'.',''),2,1) AS natureza
                                ,  SUBSTR(REPLACE(cod_estrutural,'.',''),3,2) AS modalidade
                                ,  SUBSTR(REPLACE(cod_estrutural,'.',''),5,2) AS elemento

                                ,  empenho.cod_empenho
                                ,  LPAD(item_pre_empenho.valor_empenhado::varchar,16,'0') as valor_empenhado
                                ,  item_pre_empenho.valor_empenhado as valor_empenhado_nao_formato
                                ,  sw_cgm_documento.nom_cgm as nome_credor
                                ,  CASE WHEN  sw_cgm_documento.documento IS NOT NULL THEN LPAD(sw_cgm_documento.documento,14,'0')
                                        ELSE  LPAD(entidade_documento.documento,14,'0')
                                   END as cpf_cnpj
                                , CASE WHEN lcl.cod_licitacao IS NULL THEN 'Sem Licitação' ELSE lcl.cod_licitacao||lcl.exercicio_licitacao END as cod_licitacao
                                , CASE WHEN empenho_contrato.num_contrato IS NULL THEN 'Sem Contrato' ELSE empenho_contrato.num_contrato::varchar END as num_contrato
                                , CASE WHEN empenho_convenio.num_convenio IS NULL THEN 'Sem Convênio' ELSE empenho_convenio.num_convenio::varchar END as num_convenio
                                , recurso.cod_fonte as cod_fonte
                                , SUBSTR(despesa.num_pao::varchar, 1, 1) as tipo_projeto_atividade --o primeiro digito do num_pao eh o tipo projeto ou atividade
                                , CASE WHEN categoria_empenho.cod_categoria = 1 THEN 'N' ELSE 'S' END as empenho_recursos_antecipados
                                , ''::varchar as num_empenho_superior --falta definir que valor deve buscar para essa coluna
                                , ''::varchar as ano_empenho_superior --falta definir que valor deve buscar para essa coluna
                                , ''::varchar as meta_empenho --falta definir que valor deve buscar para essa coluna
                                , ''::varchar as nota_liberacao_descentralizacao --falta definir que valor deve buscar para essa coluna
                        , entidade_documento.documento
                        , entidade_documento.nom_cgm

                             FROM  empenho.empenho
                       INNER JOIN  empenho.pre_empenho
                               ON  pre_empenho.exercicio = empenho.exercicio
                              AND  pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                       INNER JOIN  empenho.categoria_empenho
                               ON  empenho.cod_categoria = categoria_empenho.cod_categoria

                                  --num_contrato
                        LEFT JOIN  empenho.empenho_contrato
                               ON  empenho.cod_empenho = empenho_contrato.cod_empenho
                              AND  empenho.cod_entidade = empenho_contrato.cod_entidade
                              AND  empenho.exercicio = empenho_contrato.exercicio

                                  --num_convenio
                        LEFT JOIN  empenho.empenho_convenio
                               ON  empenho.cod_empenho = empenho_convenio.cod_empenho
                              AND  empenho.cod_entidade = empenho_convenio.cod_entidade
                              AND  empenho.exercicio = empenho_convenio.exercicio

                        INNER JOIN (SELECT atributo_empenho_valor.*
                              FROM empenho.atributo_empenho_valor
                             INNER JOIN (SELECT cod_pre_empenho, exercicio, max(timestamp) as timestamp, cod_atributo, cod_modulo, cod_cadastro
                                   FROM empenho.atributo_empenho_valor
                                  WHERE cod_atributo = 101
                                    AND cod_modulo = 10
                                    AND cod_cadastro = 1
                                  GROUP BY cod_pre_empenho, exercicio, cod_atributo, cod_modulo, cod_cadastro) as max_empenho
                                ON max_empenho.cod_pre_empenho = atributo_empenho_valor.cod_pre_empenho
                               AND max_empenho.exercicio     = atributo_empenho_valor.exercicio
                               AND max_empenho.timestamp       = atributo_empenho_valor.timestamp
                               AND max_empenho.cod_atributo    = atributo_empenho_valor.cod_atributo
                               AND max_empenho.cod_modulo      = atributo_empenho_valor.cod_modulo
                               AND max_empenho.cod_cadastro    = atributo_empenho_valor.cod_cadastro
                             ) as atributo_empenho_valor
                      ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                         AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho



                        LEFT JOIN ( SELECT  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                                 THEN sw_cgm_pessoa_fisica.cpf
                                                 WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                                 THEN sw_cgm_pessoa_juridica.cnpj
                                                 ELSE NULL
                                            END AS documento
                                         ,  CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                                                 THEN '1'
                                                 WHEN sw_cgm_pessoa_juridica.numcgm IS NOT NULL
                                                 THEN '2'
                                            END as tipo_juridico_credor
                                         ,  sw_cgm.numcgm
                                         ,  sw_cgm.nom_cgm
                                      FROM  sw_cgm
                                 LEFT JOIN  sw_cgm_pessoa_fisica
                                        ON  sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                                 LEFT JOIN  sw_cgm_pessoa_juridica
                                        ON  sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                                   ) AS sw_cgm_documento
                               ON  sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario
                        LEFT JOIN  ( SELECT  entidade.exercicio
                                          ,  entidade.cod_entidade
                                          ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                                  THEN sw_cgm_pessoa_fisica.cpf
                                                  WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                                  THEN sw_cgm_pessoa_juridica.cnpj
                                                  ELSE NULL
                                             END AS documento
                              ,  sw_cgm.nom_cgm
                                       FROM  orcamento.entidade
                                  LEFT JOIN  sw_cgm_pessoa_fisica
                                         ON  sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                                  LEFT JOIN  sw_cgm_pessoa_juridica
                                         ON  sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                          LEFT JOIN  sw_cgm
                             ON  sw_cgm.numcgm = entidade.numcgm
                                  ) AS entidade_documento
                               ON  entidade_documento.exercicio = empenho.exercicio
                              AND  entidade_documento.cod_entidade = empenho.cod_entidade

                       INNER JOIN  ( SELECT  exercicio
                                          ,  cod_pre_empenho
                                          ,  SUM(vl_total) as valor_empenhado
                                       FROM  empenho.item_pre_empenho
                                   GROUP BY  exercicio, cod_pre_empenho
                                   ) AS item_pre_empenho
                               ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                              AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    LEFT JOIN  empenho.item_pre_empenho_julgamento as ipej
                               ON  item_pre_empenho.exercicio       = ipej.exercicio
                              AND  item_pre_empenho.cod_pre_empenho = ipej.cod_pre_empenho

                      LEFT JOIN COMPRAS.cotacao_fornecedor_item as cfi
                        ON ipej.exercicio     = cfi.exercicio
                       AND ipej.cod_cotacao   = cfi.cod_cotacao
                       AND ipej.lote      = cfi.lote
                       AND ipej.cgm_fornecedor = cfi.cgm_fornecedor

                      LEFT JOIN licitacao.cotacao_licitacao as lcl
                        ON cfi.cgm_fornecedor = lcl.cgm_fornecedor
                       AND cfi.cod_cotacao = lcl.cgm_fornecedor
                       AND cfi.exercicio = lcl.exercicio_licitacao
                       AND cfi.lote = lcl.lote

                      LEFT JOIN licitacao.licitacao as ll
                        ON lcl.cod_licitacao = ll.cod_licitacao
                       AND lcl.cod_modalidade = ll.cod_modalidade
                       AND lcl.cod_entidade = ll.cod_entidade
                       AND lcl.exercicio_licitacao = ll.exercicio

                       INNER JOIN  empenho.pre_empenho_despesa
                               ON  pre_empenho_despesa.exercicio = pre_empenho.exercicio
                              AND  pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       INNER JOIN  orcamento.despesa
                               ON  despesa.exercicio = pre_empenho_despesa.exercicio
                              AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                       INNER JOIN  orcamento.conta_despesa
                               ON  conta_despesa.exercicio = despesa.exercicio
                              AND  conta_despesa.cod_conta = despesa.cod_conta
                       INNER JOIN  orcamento.recurso
                               ON  recurso.cod_recurso = despesa.cod_recurso
                              AND  recurso.exercicio = despesa.exercicio

                            WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
                      AND  despesa.cod_entidade <> ".$this->getDado('stEntidades')."
                     ORDER BY conta_despesa.exercicio, empenho.cod_empenho

                   ) as tbl
                   GROUP BY
                             exercicio
                           , cod_entidade
                           , unidade
                           , cod_subfuncao
                           , cod_programa
                           , num_pao
                           , categoria_economica
                           , natureza
                           , documento
                           , cod_licitacao
                           , num_contrato
                           , num_convenio
                           , cod_fonte
                           , tipo_juridico_credor
                           , tipo_projeto_atividade
                           , elemento
                           , nom_cgm
                           , empenho_recursos_antecipados
                           , num_empenho_superior
                           , ano_empenho_superior
                           , meta_empenho
                           , nota_liberacao_descentralizacao
                           , modalidade

                   ORDER BY
                            cod_entidade
                          , cod_fonte
                          , categoria_economica
                          , natureza
                          , modalidade
                          , elemento
        ";

        return $stSql;
    }

    public function recuperaTotalElemento(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaTotalElemento().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function recuperaEstornos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEstornos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEstornos()
    {
        $stSQL  = "
                            SELECT row_number as numero_estorno
                     , tabela.exercicio
                     , tabela.unidade
                     , tabela.cod_empenho
                     , tabela.data_anulacao
                     , tabela.valor_anulado
                     , tabela.foi_liquidada
                     , tabela.motivo
                     , tabela.timestamp
                  FROM (  SELECT
                                       ean.exercicio as exercicio
                                     , lpad(desp.num_orgao::varchar, 3, '0')||lpad(desp.num_unidade::varchar, 2, '0') as unidade
                                     , emp.cod_empenho
                                     , to_char(ean.timestamp,'dd/mm/yyyy') as data_anulacao
                                     , ean.timestamp
                                     , coalesce(sume.valor_anulado, 0.00) as valor_anulado
                                     , case when liq.cod_entidade is not null then 'S' else 'N' end as foi_liquidada
                                     , ean.motivo as motivo
                             FROM     empenho.empenho            as emp
                                     LEFT JOIN
                                                ( SELECT  exercicio_empenho
                                                         ,cod_entidade
                                                         ,cod_empenho
                                                 FROM    empenho.nota_liquidacao as liq
                                                 WHERE   exercicio = '".$this->getDado('exercicio')."'
                                             AND     cod_entidade in (".$this->getDado('stEntidades').")
                                                 GROUP BY exercicio_empenho, cod_entidade, cod_empenho
                                                 ) as liq
                                               ON (
                                                     emp.exercicio    = liq.exercicio_empenho
                                                 AND emp.cod_entidade = liq.cod_entidade
                                                 AND emp.cod_empenho  = liq.cod_empenho
                                                 )
                                     ,empenho.empenho_anulado    as ean
                                     ,empenho.pre_empenho        as pre
                                     ,(
                                         SELECT   exercicio
                                                 ,cod_entidade
                                                 ,cod_empenho
                                                 ,timestamp
                                                 ,sum(vl_anulado) as valor_anulado
                                         FROM    empenho.empenho_anulado_item as ipe
                                         WHERE   exercicio = '".$this->getDado('exercicio')."'
                                     AND   cod_entidade in (".$this->getDado('stEntidades').")
                                         GROUP BY exercicio, cod_entidade, cod_empenho, timestamp
                                     ) as sume
                                     ,empenho.pre_empenho_despesa as pred
                                     ,orcamento.despesa          as desp
                             WHERE   emp.exercicio       =  pre.exercicio
                             AND     emp.cod_pre_empenho = pre.cod_pre_empenho
                             AND     emp.exercicio       = ean.exercicio
                             AND     emp.cod_entidade    = ean.cod_entidade
                             AND     emp.cod_empenho     = ean.cod_empenho
                             AND     pre.exercicio       = pred.exercicio
                             AND     pre.cod_pre_empenho = pred.cod_pre_empenho
                             AND     pred.exercicio      = desp.exercicio
                             AND     pred.cod_despesa    = desp.cod_despesa
                             AND     ean.exercicio       = sume.exercicio
                             AND     ean.cod_entidade    = sume.cod_entidade
                             AND     ean.cod_empenho     = sume.cod_empenho
                             AND     ean.timestamp       = sume.timestamp
                             AND     to_char(ean.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                             AND     ean.cod_entidade in (".$this->getDado('stEntidades').")
                             AND     to_char(ean.timestamp,'mm') = '".$this->getDado('inMes')."'
                             GROUP BY emp.cod_empenho, ean.exercicio, desp.num_orgao, desp.num_unidade, ean.timestamp, sume.valor_anulado, liq.cod_entidade, ean.motivo
                             ORDER BY  emp.cod_empenho) As tabela
            CROSS JOIN (  SELECT ARRAY(  SELECT
                                                  ean.timestamp
                                         FROM     empenho.empenho            as emp
                                                 ,empenho.empenho_anulado    as ean
                                                 ,empenho.pre_empenho        as pre
                                                 ,(
                                                     SELECT   exercicio
                                                             ,cod_entidade
                                                             ,cod_empenho
                                                             ,timestamp
                                                             ,sum(vl_anulado) as valor_anulado
                                                     FROM    empenho.empenho_anulado_item as ipe
                                                     WHERE   exercicio = '".$this->getDado('exercicio')."'
                                                 AND   cod_entidade in (".$this->getDado('stEntidades').")
                                                     GROUP BY exercicio, cod_entidade, cod_empenho, timestamp
                                                 ) as sume
                                                 ,empenho.pre_empenho_despesa as pred
                                                 ,orcamento.despesa          as desp
                                         WHERE   emp.exercicio       =  pre.exercicio
                                         AND     emp.cod_pre_empenho = pre.cod_pre_empenho
                                         AND     emp.exercicio       = ean.exercicio
                                         AND     emp.cod_entidade    = ean.cod_entidade
                                         AND     emp.cod_empenho     = ean.cod_empenho
                                         AND     pre.exercicio       = pred.exercicio
                                         AND     pre.cod_pre_empenho = pred.cod_pre_empenho
                                         AND     pred.exercicio      = desp.exercicio
                                         AND     pred.cod_despesa    = desp.cod_despesa
                                         AND     ean.exercicio       = sume.exercicio
                                         AND     ean.cod_entidade    = sume.cod_entidade
                                         AND     ean.cod_empenho     = sume.cod_empenho
                                         AND     ean.timestamp       = sume.timestamp
                                         AND     to_char(ean.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                                         AND     ean.cod_entidade in (".$this->getDado('stEntidades').")
                                         GROUP BY  ean.timestamp
                                         ORDER BY  ean.timestamp) As num_empenho)  AS empenhos
            CROSS JOIN generate_series(1, (SELECT
                                                  count(emp.cod_empenho)
                                         FROM     empenho.empenho            as emp
                                                 ,empenho.empenho_anulado    as ean
                                                 ,empenho.pre_empenho        as pre
                                                 ,empenho.pre_empenho_despesa as pred
                                                 ,orcamento.despesa          as desp
                                                 ,(
                                                      SELECT   exercicio
                                                              ,cod_entidade
                                                              ,cod_empenho
                                                              ,timestamp
                                                              ,sum(vl_anulado) as valor_anulado
                                                      FROM    empenho.empenho_anulado_item as ipe
                                                      WHERE   exercicio = '".$this->getDado('exercicio')."'
                                                  AND   cod_entidade in (".$this->getDado('stEntidades').")
                                                      GROUP BY exercicio, cod_entidade, cod_empenho, timestamp
                                                  ) as sume
                                         WHERE   emp.exercicio       =  pre.exercicio
                                         AND     emp.cod_pre_empenho = pre.cod_pre_empenho
                                         AND     emp.exercicio       = ean.exercicio
                                         AND     emp.cod_entidade    = ean.cod_entidade
                                         AND     emp.cod_empenho     = ean.cod_empenho
                                         AND     pre.exercicio       = pred.exercicio
                                         AND     pre.cod_pre_empenho = pred.cod_pre_empenho
                                         AND     pred.exercicio      = desp.exercicio
                                         AND     pred.cod_despesa    = desp.cod_despesa
                                         AND     ean.exercicio       = sume.exercicio
                                         AND     ean.cod_entidade    = sume.cod_entidade
                                         AND     ean.cod_empenho     = sume.cod_empenho
                                         AND     ean.timestamp       = sume.timestamp
                                         AND     to_char(ean.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
                                         AND     ean.cod_entidade in (".$this->getDado('stEntidades').")
                                         )
                                      ) AS row_number
                 WHERE empenhos.num_empenho[row_number] =  tabela.timestamp
              ORDER BY tabela.timestamp, row_number";

        return $stSQL;

    }
}
