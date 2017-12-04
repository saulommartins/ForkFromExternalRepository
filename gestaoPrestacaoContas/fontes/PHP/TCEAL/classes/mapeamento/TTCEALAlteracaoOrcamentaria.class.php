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
    * Extensão da Classe de Mapeamento TTCEALAlteracaoOrcamentaria
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: TTCEALAlteracaoOrcamentaria.class.php 65536 2016-05-31 18:07:26Z michel $
    *
    * @ignore
    *
*/
class TTCEALAlteracaoOrcamentaria extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALAlteracaoOrcamentaria()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCredor.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaAlteracaoOrcamentaria(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaAlteracaoOrcamentaria().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAlteracaoOrcamentaria()
    {       
        $stSql = "
                    SELECT
                            CASE WHEN codigo_ua <> ''
                                 THEN codigo_ua
                                 ELSE
                                      '0000'
                            END AS codigo_ua_real
                            , tabela.*
                    FROM ( SELECT
                                    (SELECT PJ.cnpj
                                       FROM orcamento.entidade
                                       JOIN sw_cgm
                                         ON sw_cgm.numcgm=entidade.numcgm
                                       JOIN sw_cgm_pessoa_juridica AS PJ
                                         ON sw_cgm.numcgm=PJ.numcgm
                                      WHERE entidade.exercicio='".$this->getDado('exercicio')."'
                                        AND entidade.cod_entidade=".$this->getDado('und_gestora')."
                                    ) AS Cod_Und_Gestora
                                  , (SELECT valor
                                       FROM administracao.configuracao_entidade
                                      WHERE exercicio = '".$this->getDado('exercicio')."'
                                        AND cod_entidade = ".$this->getDado('und_gestora')."
                                        AND cod_modulo = 62
                                        AND parametro = 'tceal_configuracao_unidade_autonoma'
                                    ) AS Codigo_UA
                                  , LPAD(despesa.num_orgao::varchar,2,'0') AS num_orgao
                                  , LPAD(despesa.num_unidade::varchar,4,'0') AS num_unidade
                                  , LPAD(despesa.cod_funcao::varchar,2,'0') AS cod_funcao
                                  , LPAD(despesa.cod_subfuncao::varchar,3,'0') AS cod_subfuncao
                                  , CASE WHEN programa.num_programa::varchar <> '' THEN LPAD(programa.num_programa::varchar,4,'0') ELSE LPAD(despesa.cod_programa::varchar,4,'0') END AS cod_programa
                                  , CASE WHEN acao.num_acao::varchar <> '' THEN LPAD(acao.num_acao::varchar,4,'0') ELSE LPAD(despesa.num_pao::varchar,4,'0') END AS num_pao
                                  , RPAD(REPLACE(conta_despesa.cod_estrutural::VARCHAR,'.',''),16,'0') AS cod_estrutural
                                  , LPAD(despesa.cod_recurso::varchar,9,'0') AS cod_recurso
                                  , tipo_norma.cod_tipo_norma AS documento_alteracao
                                  , CASE WHEN norma.cod_tipo_norma = 1
                                         THEN tceal.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Número da Lei')
                                         ELSE CAST(norma.num_norma as varchar)                                                    
                                    END AS num_doc_alteracao
                                  , CASE WHEN norma.cod_tipo_norma = 1
                                         THEN tceal.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Data da Lei')
                                         ELSE to_char(norma.dt_publicacao,'dd/mm/yyyy')                                              
                                    END AS dt_doc_alteracao
                                  , to_char(norma.dt_publicacao,'dd/mm/yyyy') AS dt_pub_alteracao
                                  , tceal.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Número da Lei') AS num_lei_autorizacao
                                  , tceal.fn_retorno_atributo_normas(norma.cod_tipo_norma,norma.cod_norma,'Data da Lei') AS dt_lei_autorizacao
                                  , to_char(norma.dt_publicacao,'dd/mm/yyyy') AS dt_pub_autorizacao
                                  , suplementacao.cod_suplementacao AS num_alteracao
                                  , CASE WHEN suplementacao.cod_tipo = 5 THEN 1
                                         WHEN suplementacao.cod_tipo = 4 THEN 2
                                         WHEN suplementacao.cod_tipo = 2 THEN 3
                                         WHEN suplementacao.cod_tipo = 1 THEN 4
                                         WHEN suplementacao.cod_tipo = 10 THEN 5
                                         WHEN suplementacao.cod_tipo = 9 THEN 6
                                         WHEN suplementacao.cod_tipo = 7 THEN 7
                                         WHEN suplementacao.cod_tipo = 6 THEN 8
                                         WHEN suplementacao.cod_tipo = 11 THEN 9
                                  END AS tipo_alteracao
                                  , suplementacao_suplementada.valor AS vl_alteracao

                            FROM orcamento.despesa

                            JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = despesa.exercicio
                             AND conta_despesa.cod_conta = despesa.cod_conta

                            JOIN empenho.pre_empenho_despesa
                              ON pre_empenho_despesa.exercicio = despesa.exercicio
                             AND pre_empenho_despesa.cod_despesa = despesa.cod_despesa

                            JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
                             AND pre_empenho.exercicio = pre_empenho_despesa.exercicio

                            JOIN empenho.item_pre_empenho
                              ON item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                             AND item_pre_empenho.exercicio = pre_empenho.exercicio

                            JOIN empenho.empenho
                              ON empenho.exercicio = pre_empenho.exercicio
                             AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                       LEFT JOIN empenho.empenho_anulado
                              ON empenho_anulado.exercicio = empenho.exercicio
                             AND empenho_anulado.cod_entidade = empenho.cod_entidade
                             AND empenho_anulado.cod_empenho = empenho.cod_empenho

                       LEFT JOIN empenho.empenho_anulado_item
                              ON empenho_anulado_item.exercicio = item_pre_empenho.exercicio
                             AND empenho_anulado_item.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                             AND empenho_anulado_item.num_item = item_pre_empenho.num_item

                            JOIN ( SELECT exercicio
                                        , cod_suplementacao
                                        , cod_despesa
                                        , valor
                                     FROM orcamento.suplementacao_suplementada
                                UNION ALL
                                   SELECT exercicio
                                        , cod_suplementacao
                                        , cod_despesa
                                        , valor
                                     FROM orcamento.suplementacao_reducao
                               ) AS suplementacao_suplementada
                              ON suplementacao_suplementada.exercicio = despesa.exercicio
                             AND suplementacao_suplementada.cod_despesa = despesa.cod_despesa

                       LEFT JOIN orcamento.suplementacao
                              ON suplementacao.exercicio = suplementacao_suplementada.exercicio
                             AND suplementacao.cod_suplementacao = suplementacao_suplementada.cod_suplementacao

                       LEFT JOIN orcamento.suplementacao_anulada
                              ON suplementacao_anulada.exercicio = suplementacao_suplementada.exercicio
                             AND suplementacao_anulada.cod_suplementacao = suplementacao_suplementada.cod_suplementacao

                            JOIN normas.norma
                              ON norma.cod_norma = suplementacao.cod_norma

                            JOIN normas.tipo_norma
                              ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma

                            JOIN sw_cgm
                              ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                       LEFT JOIN sw_cgm_pessoa_fisica                                                                             
                              ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                       LEFT JOIN sw_cgm_pessoa_juridica                                                                            
                            ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

                            JOIN orcamento.despesa_acao
                              ON despesa_acao.exercicio_despesa = despesa.exercicio
                             AND despesa_acao.cod_despesa = despesa.cod_despesa

                       LEFT JOIN ppa.acao
                              ON acao.cod_acao = despesa_acao.cod_acao

                       LEFT JOIN ppa.programa
                              ON programa.cod_programa = acao.cod_programa

                            WHERE empenho.exercicio = '".Sessao::getExercicio()."'
                              AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                              AND suplementacao.dt_suplementacao BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                              AND suplementacao.cod_tipo NOT IN (15, 16)
                              AND suplementacao_anulada.cod_suplementacao IS null
                        ) AS tabela

                GROUP BY codigo_ua,
                         Cod_Und_Gestora,
                         num_orgao,
                         num_unidade,
                         cod_funcao,
                         cod_subfuncao,
                         cod_programa,
                         num_pao,
                         cod_estrutural,
                         cod_recurso,
                         documento_alteracao,
                         num_doc_alteracao,
                         dt_doc_alteracao,
                         dt_pub_alteracao,
                         num_lei_autorizacao,
                         dt_lei_autorizacao,
                         dt_pub_autorizacao,
                         num_alteracao,
                         tipo_alteracao,
                         vl_alteracao
                ";
                
        return $stSql;
    }
}
?>
