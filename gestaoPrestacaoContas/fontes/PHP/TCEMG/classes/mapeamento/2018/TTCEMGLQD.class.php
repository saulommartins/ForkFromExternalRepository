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
    * @author Desenvolvedor: Jean da Silva

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGLQD.class.php 64285 2016-01-07 11:05:25Z evandro $

    * Casos de uso: uc-06.04.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEMGLQD extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function __construct() {
        parent::Persistente();
    }

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "
                    SELECT *
                      FROM (
                          SELECT 10 AS tipo_registro
                               , LPAD((nota_liquidacao.cod_nota::VARCHAR || nota_liquidacao.exercicio), 15, '0') AS cod_reduzido
                               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                               , LPAD(LPAD(despesa.num_orgao::VARCHAR,2,'0') || LPAD(despesa.num_unidade::VARCHAR,2,'0'),5,'0') AS cod_unidade
                               , CASE WHEN empenho.exercicio < '".$this->getDado('exercicio')."'
                                        THEN 
                                                2 -- 2 Liquidação Despesas Restos a Pagar não Processados.
                                        ELSE 
                                                1 -- 1 Liquidação Despesa do Exercício;
                                END AS tipo_liquidacao
                               , empenho.cod_empenho AS num_empenho
                               , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dt_empenho
                               , TO_CHAR(nota_liquidacao.dt_liquidacao,'ddmmyyyy') AS dt_liquidacao
                               , TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."',
                                                              empenho.cod_entidade,
                                                              nota_liquidacao.cod_nota,
                                                              nota_liquidacao.exercicio_empenho,
                                                              empenho.cod_empenho
                                                             ) AS num_liquidacao
                               , LPAD(REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',','),13,'0') AS vl_liquidado
                               , ordenador.cpf AS cpf_liquidante
                            FROM empenho.pre_empenho

                      INNER JOIN empenho.empenho
                              ON empenho.exercicio = pre_empenho.exercicio
                             AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN empenho.nota_liquidacao
                              ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                             AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                             AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                      INNER JOIN empenho.nota_liquidacao_item
                              ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                             AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                             AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

                       LEFT JOIN (SELECT despesa.*
                                       , conta_despesa.cod_estrutural
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , recurso.cod_fonte
                                       , uniorcam.cgm_ordenador
                                   FROM empenho.pre_empenho_despesa
                             INNER JOIN orcamento.despesa
                                     ON despesa.exercicio = pre_empenho_despesa.exercicio
                                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                             INNER JOIN orcamento.conta_despesa
                                     ON conta_despesa.exercicio = despesa.exercicio
                                    AND conta_despesa.cod_conta = despesa.cod_conta
                             INNER JOIN orcamento.recurso
                                     ON despesa.cod_recurso = recurso.cod_recurso
                                    AND despesa.exercicio   = recurso.exercicio
                             INNER JOIN tcemg.uniorcam
                                     ON uniorcam.exercicio = despesa.exercicio
                                    AND uniorcam.num_orgao = despesa.num_orgao
                                    AND uniorcam.num_unidade = despesa.num_unidade
                                 ) AS despesa
                              ON despesa.exercicio = pre_empenho.exercicio
                             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade  = nota_liquidacao.cod_entidade
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.exercicio = nota_liquidacao.exercicio

                       LEFT JOIN sw_cgm_pessoa_fisica AS ordenador
                              ON ordenador.numcgm = despesa.cgm_ordenador

                           WHERE nota_liquidacao.exercicio = '".Sessao::getExercicio()."'
                             AND TO_CHAR(nota_liquidacao.dt_liquidacao,'mmyyyy') = '".$this->getDado('mes')."".Sessao::getExercicio()."'
                             AND nota_liquidacao.cod_entidade IN (".$this->getDado('entidades').")
                             AND pre_empenho.implantado = 'f'

                        GROUP BY empenho.cod_entidade
                               , nota_liquidacao.cod_nota
                               , nota_liquidacao.exercicio_empenho
                               , empenho.cod_empenho
                               , configuracao_entidade.valor
                               , despesa.num_unidade
                               , despesa.num_orgao
                               , empenho.dt_empenho
                               , nota_liquidacao.dt_liquidacao
                               , nota_liquidacao.exercicio
                               , empenho.exercicio
                               , ordenador.cpf

                           UNION

                          SELECT 10 AS tipo_registro
                               , LPAD((nota_liquidacao.cod_nota::VARCHAR || nota_liquidacao.exercicio), 15, '0') AS cod_reduzido
                               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
                               , CASE WHEN uniorcam_atual.num_orgao_atual IS NOT NULL
                                      THEN LPAD(LPAD(uniorcam_atual.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam_atual.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                      ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                   END AS cod_unidade
                               , CASE WHEN empenho.exercicio < '".$this->getDado('exercicio')."'
                                        THEN 
                                                2 -- 2 Liquidação Despesas Restos a Pagar não Processados.
                                        ELSE 
                                                1 -- 1 Liquidação Despesa do Exercício;
                                END AS tipo_liquidacao
                               , empenho.cod_empenho AS num_empenho
                               , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dt_empenho
                               , TO_CHAR(nota_liquidacao.dt_liquidacao,'ddmmyyyy') AS dt_liquidacao
                               , TCEMG.numero_nota_liquidacao('".$this->getDado('exercicio')."',
                                                              empenho.cod_entidade,
                                                              nota_liquidacao.cod_nota,
                                                              nota_liquidacao.exercicio_empenho,
                                                              empenho.cod_empenho
                                                             ) AS num_liquidacao
                               , LPAD(REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',','),13,'0') AS vl_liquidado
                               , ordenador.cpf AS cpf_liquidante

                            FROM empenho.pre_empenho

                      INNER JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio

                       LEFT JOIN (SELECT uniorcam.exercicio
                                       , uniorcam.num_unidade
                                       , uniorcam.num_orgao
                                       , uniorcam.exercicio_atual
                                       , uniorcam.num_unidade_atual
                                       , uniorcam.num_orgao_atual
                                       , CASE WHEN uniorcam.cgm_ordenador IS NOT NULL
                                              THEN uniorcam.cgm_ordenador
                                              ELSE atual.cgm_ordenador
                                          END AS cgm_ordenador
                                    FROM tcemg.uniorcam
                               LEFT JOIN tcemg.uniorcam AS atual
                                      ON atual.num_unidade = uniorcam.num_unidade_atual
                                     AND atual.num_orgao = uniorcam.num_orgao_atual
                                     AND atual.exercicio = uniorcam.exercicio_atual
                                 ) AS uniorcam_atual
                              ON uniorcam_atual.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam_atual.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam_atual.num_orgao_atual IS NOT NULL

                      INNER JOIN empenho.empenho
                              ON empenho.exercicio = pre_empenho.exercicio
                             AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN empenho.nota_liquidacao
                              ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                             AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                             AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                      INNER JOIN empenho.nota_liquidacao_item
                              ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                             AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                             AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

                       LEFT JOIN (SELECT despesa.*
                                       , conta_despesa.cod_estrutural
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , recurso.cod_fonte
                                       , uniorcam.cgm_ordenador
                                       , uniorcam.num_unidade AS num_unidade_uniorcam
                                       , uniorcam.num_orgao AS num_orgao_uniorcam
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                              INNER JOIN orcamento.recurso
                                      ON despesa.cod_recurso = recurso.cod_recurso
                                     AND despesa.exercicio   = recurso.exercicio
                              INNER JOIN tcemg.uniorcam
                                      ON uniorcam.exercicio = despesa.exercicio
                                     AND uniorcam.num_orgao = despesa.num_orgao
                                     AND uniorcam.num_unidade = despesa.num_unidade
                                 ) AS despesa
                              ON despesa.exercicio = pre_empenho.exercicio
                             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade  = nota_liquidacao.cod_entidade
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.exercicio = nota_liquidacao.exercicio

                       LEFT JOIN sw_cgm_pessoa_fisica AS ordenador
                              ON (   ( ordenador.numcgm = uniorcam_atual.cgm_ordenador
                                   AND uniorcam_atual.num_orgao_atual IS NOT NULL
                                   AND uniorcam_atual.cgm_ordenador IS NOT NULL
                                     )
                                  OR ( ordenador.numcgm = despesa.cgm_ordenador
                                   AND uniorcam_atual.num_orgao_atual IS NULL
                                   AND uniorcam_atual.cgm_ordenador IS NULL
                                     )
                                 )

                           WHERE nota_liquidacao.exercicio = '".Sessao::getExercicio()."'
                             AND TO_CHAR(nota_liquidacao.dt_liquidacao,'mmyyyy') = '".$this->getDado('mes')."".Sessao::getExercicio()."'
                             AND nota_liquidacao.cod_entidade IN (".$this->getDado('entidades').")
                             AND pre_empenho.implantado = 't'

                        GROUP BY tipo_registro
                               , cod_reduzido
                               , cod_orgao
                               , cod_unidade
                               , tipo_liquidacao
                               , num_empenho
                               , empenho.dt_empenho
                               , nota_liquidacao.dt_liquidacao
                               , num_liquidacao
                               , cpf_liquidante
                           ) AS tabela

                  ORDER BY num_empenho ";
        return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "
                    SELECT *
                      FROM (
                          SELECT 11 AS tipo_registro
                               , LPAD((nota_liquidacao.cod_nota::VARCHAR || nota_liquidacao.exercicio), 15, '0') AS cod_reduzido
                               , LPAD(LPAD(despesa.num_orgao::VARCHAR,2,'0') || LPAD(despesa.num_unidade::VARCHAR,2,'0'),5,'0') AS cod_unidade
                               , empenho.cod_empenho AS num_empenho
                               , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dt_empenho
                               , TCEMG.numero_nota_liquidacao('".Sessao::getExercicio()."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho) AS num_liquidacao
                               , despesa.cod_fonte::varchar AS cod_font_recursos
                               , REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',',') AS vl_fonte

                            FROM empenho.pre_empenho

                      INNER JOIN empenho.empenho
                              ON empenho.exercicio = pre_empenho.exercicio
                             AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN empenho.nota_liquidacao
                              ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                             AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                             AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                      INNER JOIN empenho.nota_liquidacao_item
                              ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                             AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                             AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

                       LEFT JOIN (SELECT despesa.*
                                       , conta_despesa.cod_estrutural
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , recurso.cod_fonte
                                       , uniorcam.cgm_ordenador
                                   FROM empenho.pre_empenho_despesa
                             INNER JOIN orcamento.despesa
                                     ON despesa.exercicio = pre_empenho_despesa.exercicio
                                    AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                             INNER JOIN orcamento.conta_despesa
                                     ON conta_despesa.exercicio = despesa.exercicio
                                    AND conta_despesa.cod_conta = despesa.cod_conta
                             INNER JOIN orcamento.recurso
                                     ON despesa.cod_recurso = recurso.cod_recurso
                                    AND despesa.exercicio   = recurso.exercicio
                             INNER JOIN tcemg.uniorcam
                                     ON uniorcam.exercicio = despesa.exercicio
                                    AND uniorcam.num_orgao = despesa.num_orgao
                                    AND uniorcam.num_unidade = despesa.num_unidade
                                 ) AS despesa
                              ON despesa.exercicio = pre_empenho.exercicio
                             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade  = nota_liquidacao.cod_entidade
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.exercicio = nota_liquidacao.exercicio

                           WHERE nota_liquidacao.exercicio = '".Sessao::getExercicio()."'
                             AND TO_CHAR(nota_liquidacao.dt_liquidacao,'mmyyyy') = '".$this->getDado('mes')."".Sessao::getExercicio()."'
                             AND nota_liquidacao.cod_entidade IN (".$this->getDado('entidades').")
                             AND pre_empenho.implantado = 'f'

                        GROUP BY empenho.cod_entidade
                               , nota_liquidacao.cod_nota
                               , nota_liquidacao.exercicio_empenho
                               , empenho.cod_empenho
                               , configuracao_entidade.valor
                               , despesa.num_unidade
                               , despesa.num_orgao
                               , empenho.dt_empenho
                               , nota_liquidacao.dt_liquidacao
                               , despesa.cod_fonte
                               , nota_liquidacao.exercicio

                        UNION

                          SELECT 11 AS tipo_registro
                               , LPAD((nota_liquidacao.cod_nota::VARCHAR || nota_liquidacao.exercicio), 15, '0') AS cod_reduzido
                               , CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                           THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                           ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                                 END AS cod_unidade
                               , empenho.cod_empenho AS num_empenho
                               , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dt_empenho
                               , TCEMG.numero_nota_liquidacao('".Sessao::getExercicio()."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho) AS num_liquidacao
                               , restos_pre_empenho.recurso::varchar AS cod_font_recursos
                               , REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',',') AS vl_fonte

                            FROM empenho.pre_empenho

                      INNER JOIN empenho.restos_pre_empenho
                              ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND restos_pre_empenho.exercicio = pre_empenho.exercicio

                       LEFT JOIN tcemg.uniorcam
                              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
                             AND uniorcam.exercicio = restos_pre_empenho.exercicio
                             AND uniorcam.num_orgao_atual IS NOT NULL

                      INNER JOIN empenho.empenho
                              ON empenho.exercicio = pre_empenho.exercicio
                             AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN empenho.nota_liquidacao
                              ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                             AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                             AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                      INNER JOIN empenho.nota_liquidacao_item
                              ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                             AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                             AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota

                       LEFT JOIN (SELECT despesa.*
                                       , conta_despesa.cod_estrutural
                                       , pre_empenho_despesa.cod_pre_empenho
                                       , recurso.cod_fonte
                                       , uniorcam.cgm_ordenador
                                       , uniorcam.num_unidade AS num_unidade_uniorcam
                                       , uniorcam.num_orgao AS num_orgao_uniorcam
                                    FROM empenho.pre_empenho_despesa
                              INNER JOIN orcamento.despesa
                                      ON despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              INNER JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = despesa.exercicio
                                     AND conta_despesa.cod_conta = despesa.cod_conta
                              INNER JOIN orcamento.recurso
                                      ON despesa.cod_recurso = recurso.cod_recurso
                                     AND despesa.exercicio   = recurso.exercicio
                              INNER JOIN tcemg.uniorcam
                                      ON uniorcam.exercicio = despesa.exercicio
                                     AND uniorcam.num_orgao = despesa.num_orgao
                                     AND uniorcam.num_unidade = despesa.num_unidade
                                 ) AS despesa
                              ON despesa.exercicio = pre_empenho.exercicio
                             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade  = nota_liquidacao.cod_entidade
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                             AND configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.exercicio = nota_liquidacao.exercicio

                           WHERE nota_liquidacao.exercicio = '".Sessao::getExercicio()."'
                             AND TO_CHAR(nota_liquidacao.dt_liquidacao,'mmyyyy') = '".$this->getDado('mes')."".Sessao::getExercicio()."'
                             AND nota_liquidacao.cod_entidade IN (".$this->getDado('entidades').")
                             AND pre_empenho.implantado = 't'

                        GROUP BY empenho.cod_entidade
                               , nota_liquidacao.cod_nota
                               , nota_liquidacao.exercicio_empenho
                               , empenho.cod_empenho
                               , configuracao_entidade.valor
                               , despesa.num_unidade
                               , despesa.num_orgao
                               , empenho.dt_empenho
                               , nota_liquidacao.dt_liquidacao
                               , restos_pre_empenho.recurso
                               , nota_liquidacao.exercicio
                               , despesa.num_unidade_uniorcam
                               , despesa.num_orgao_uniorcam
                               , cod_unidade
                               , restos_pre_empenho.num_unidade
                           ) AS tabela

                  ORDER BY num_empenho ";
        return $stSql;
    }

    public function recuperaExportacao12(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao12()
    {
        $stSql = "
                  SELECT 12 AS tipo_registro
                       , LPAD((nota_liquidacao.cod_nota::VARCHAR || nota_liquidacao.exercicio), 15, '0') as cod_reduzido
                       , SUBSTR(TO_CHAR(nota_liquidacao.dt_liquidacao,'ddmmyyyy'),3,2) AS mes_competencia
                       , nota_liquidacao.exercicio::integer AS exercicio_competencia
                       , empenho.cod_empenho as num_empenho
                       , TCEMG.numero_nota_liquidacao('2015', empenho.cod_entidade, nota_liquidacao.cod_nota, nota_liquidacao.exercicio_empenho, empenho.cod_empenho ) AS num_liquidacao
                       , REPLACE(SUM(nota_liquidacao_item.vl_total)::varchar,'.',',') AS vl_despesa_anterior
                    FROM empenho.empenho
              INNER JOIN empenho.nota_liquidacao
                      ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                     AND nota_liquidacao.cod_entidade      = empenho.cod_entidade
                     AND nota_liquidacao.cod_empenho       = empenho.cod_empenho
              INNER JOIN empenho.nota_liquidacao_item
                      ON nota_liquidacao_item.exercicio = nota_liquidacao.exercicio
                     AND nota_liquidacao_item.cod_entidade = nota_liquidacao.cod_entidade
                     AND nota_liquidacao_item.cod_nota = nota_liquidacao.cod_nota
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               LEFT JOIN (
                          SELECT restos_pre_empenho.exercicio
                               , restos_pre_empenho.cod_pre_empenho
                               , substring(restos_pre_empenho.cod_estrutural from 6 for 2) AS elemento
                            FROM empenho.restos_pre_empenho
                           WHERE substring(restos_pre_empenho.cod_estrutural from 6 for 2)::INTEGER IN (91,92)
                             AND substring(restos_pre_empenho.cod_estrutural from 2 for 1)::INTEGER IN (1)
                         ) AS restos_pre_empenho
                      ON restos_pre_empenho.exercicio = pre_empenho.exercicio
                     AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.exercicio = pre_empenho.exercicio
                     AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
               LEFT JOIN (
                          SELECT pre_empenho_despesa.cod_pre_empenho
                               , pre_empenho_despesa.exercicio
                               , despesa.cod_entidade
                               , substring(conta_despesa.cod_estrutural from 9 for 2) AS elemento
                            FROM empenho.pre_empenho_despesa
                      INNER JOIN orcamento.despesa
                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                      INNER JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = despesa.exercicio
                             AND conta_despesa.cod_conta = despesa.cod_conta
                           WHERE substring(conta_despesa.cod_estrutural from 9 for 2)::INTEGER IN (91,92)
                             AND substring(conta_despesa.cod_estrutural from 3 for 1)::INTEGER IN (1)
                        ) AS despesa
                      ON despesa.exercicio = pre_empenho.exercicio
                     AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND despesa.cod_entidade = empenho.cod_entidade
                   WHERE nota_liquidacao.dt_liquidacao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
				                                           AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                     AND nota_liquidacao.cod_entidade IN (".$this->getDado('entidades').")
                     AND ( despesa.elemento IS NOT NULL OR restos_pre_empenho.elemento IS NOT NULL )
                GROUP BY tipo_registro
                       , cod_reduzido
                       , mes_competencia
                       , exercicio_competencia
                       , num_empenho
                       , num_liquidacao
        ";

        return $stSql;
    }
    
    public function __destruct(){}

}

?>