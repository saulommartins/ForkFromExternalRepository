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
    * Extensï¿½o da Classe de mapeamento
    * Data de Criaï¿½ï¿½o: 30/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTPBEmpenho.class.php 39086 2009-03-25 12:07:39Z andrem $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criaï¿½ï¿½o: 22/01/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTCEAMEmpenho extends Persistente
{
    /**
        * Mï¿½todo Construtor
        * @access Private
    */
    public function TTCEAMRetencoesEmpenho()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function RecuperaRetencoesEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRetencoesEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRetencoesEmpenho()
    {
        $stSql = "SELECT
             exercicio --ano do empenho para a despesa
           , unidade --codigo unidade orcamentaria
           , cod_subfuncao
           , cod_programa
           , num_pao --numero do projeto atividade
           , categoria_economica
           , natureza
           , cod_modalidade
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


     FROM(

             SELECT  despesa.exercicio
                  ,  LPAD(despesa.num_orgao,3,'0') || LPAD(despesa.num_unidade,2,'0') as unidade
                  ,  despesa.cod_subfuncao
                  ,  despesa.cod_programa
                  ,  LPAD(despesa.num_pao, 7, '0') as num_pao
                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),1,1) as categoria_economica
                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),2,1) as natureza
                  ,  CASE WHEN atributo_empenho_valor.valor = 1  THEN 4
                  WHEN atributo_empenho_valor.valor = 2  THEN 3
                  WHEN atributo_empenho_valor.valor = 3  THEN 2
                  WHEN atributo_empenho_valor.valor = 4  THEN 1
                  WHEN atributo_empenho_valor.valor = 5  THEN 6
                  WHEN atributo_empenho_valor.valor = 6  THEN 8
                  WHEN atributo_empenho_valor.valor = 7  THEN 6
                  WHEN atributo_empenho_valor.valor = 8  THEN 6
                  WHEN atributo_empenho_valor.valor = 9  THEN 6
                  WHEN atributo_empenho_valor.valor = 10 THEN 0
            END as cod_modalidade


                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),3,2) as modalidade
                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),5,2) as elemento --
                  ,  empenho.cod_empenho
                  ,  CASE WHEN pre_empenho.cod_tipo = 2 THEN 3
                          WHEN pre_empenho.cod_tipo = 3 THEN 2
                          WHEN pre_empenho.cod_tipo = 1 THEN 1
                     END as tipo_empenho
                  ,  TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
                  ,  LPAD(item_pre_empenho.valor_empenhado,16,'0') as valor_empenhado
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
                  , SUBSTR(despesa.num_pao, 1, 1) as tipo_projeto_atividade --o primeiro digito do num_pao eh o tipo projeto ou atividade
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
                           ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                   THEN '1'
                                   WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
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

              WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
                AND  TO_CHAR(empenho.dt_empenho,'mm') = '".$this->getDado('inMes')."'
        ";
        if ( $this->getDado('stEntidades') ) {
            $stSql .= " AND despesa.cod_entidade in (".$this->getDado('stEntidades').") ";
        }
        $stSql .= " ORDER BY conta_despesa.exercicio, empenho.cod_empenho

    ) as tbl
    GROUP BY

            exercicio
           , unidade
           , cod_subfuncao
           , cod_programa
           , num_pao
           , categoria_economica
           , natureza
           , cod_modalidade
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

    ORDER BY
            cod_empenho
\n";

        return $stSql;
    }

     function montaRecuperaEmpenhos()
     {
        $stSql = "SELECT
             exercicio --ano do empenho para a despesa
           , unidade --codigo unidade orcamentaria
           , cod_subfuncao
           , cod_programa
           , num_pao --numero do projeto atividade
           , categoria_economica
           , natureza
           , cod_modalidade
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


     FROM(

             SELECT  despesa.exercicio
                  ,  LPAD(despesa.num_orgao,3,'0') || LPAD(despesa.num_unidade,2,'0') as unidade
                  ,  despesa.cod_subfuncao
                  ,  despesa.cod_programa
                  ,  LPAD(despesa.num_pao, 7, '0') as num_pao
                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),1,1) as categoria_economica
                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),2,1) as natureza
                  ,  CASE WHEN atributo_empenho_valor.valor = 1  THEN 4
                  WHEN atributo_empenho_valor.valor = 2  THEN 3
                  WHEN atributo_empenho_valor.valor = 3  THEN 2
                  WHEN atributo_empenho_valor.valor = 4  THEN 1
                  WHEN atributo_empenho_valor.valor = 5  THEN 6
                  WHEN atributo_empenho_valor.valor = 6  THEN 8
                  WHEN atributo_empenho_valor.valor = 7  THEN 6
                  WHEN atributo_empenho_valor.valor = 8  THEN 6
                  WHEN atributo_empenho_valor.valor = 9  THEN 6
                  WHEN atributo_empenho_valor.valor = 10 THEN 0
            END as cod_modalidade


                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),3,2) as modalidade
                  ,  SUBSTR(replace(conta_despesa.cod_estrutural,'.',''),5,2) as elemento --
                  ,  empenho.cod_empenho
                  ,  CASE WHEN pre_empenho.cod_tipo = 2 THEN 3
                          WHEN pre_empenho.cod_tipo = 3 THEN 2
                          WHEN pre_empenho.cod_tipo = 1 THEN 1
                     END as tipo_empenho
                  ,  TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
                  ,  LPAD(item_pre_empenho.valor_empenhado,16,'0') as valor_empenhado
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
                  , SUBSTR(despesa.num_pao, 1, 1) as tipo_projeto_atividade --o primeiro digito do num_pao eh o tipo projeto ou atividade
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
                           ,  CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                   THEN '1'
                                   WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
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

              WHERE  despesa.exercicio = '".$this->getDado('exercicio')."'
                AND  TO_CHAR(empenho.dt_empenho,'mm') = '".$this->getDado('inMes')."'
        ";
        if ( $this->getDado('stEntidades') ) {
            $stSql .= " AND despesa.cod_entidade in (".$this->getDado('stEntidades').") ";
        }
        $stSql .= " ORDER BY conta_despesa.exercicio, empenho.cod_empenho

    ) as tbl
    GROUP BY

            exercicio
           , unidade
           , cod_subfuncao
           , cod_programa
           , num_pao
           , categoria_economica
           , natureza
           , cod_modalidade
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

    ORDER BY
            cod_empenho
\n";

        return $stSql;
    }

}
