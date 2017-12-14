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
    * Arquivo de geracao do arquivo RestoPagar.txt TCM/BA
    * Data de Criação   : 10/09/2015
    * @author Analista      Valtair Santos
    * @author Desenvolvedor Michel Teixeira
    * 
    * $Id: TTCMBARestoPagar.class.php 63563 2015-09-10 19:09:46Z michel $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBARestoPagar extends Persistente {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaRestoPagar(&$rsRecordSet)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRestoPagar().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaRestoPagar()
    {
        $stSql = "
                SELECT tipo_registro
                     , competencia
                     , unidade_gestora
                     , tipo_movimentacao
                     , cod_recurso
                     , reservado
                     , reservado2
                     , natureza_despesa
                     , exercicio_anterior
                     , SUM(saldo_dotacao) AS saldo_dotacao
                     , SUM(saldo_resto) AS saldo_resto
                     , SUM(saldo_dotacao) - SUM(saldo_resto) AS saldo_insuficiencia
                  FROM (
                    SELECT tipo_registro
                         , competencia
                         , unidade_gestora
                         , tipo_movimentacao
                         , cod_recurso
                         , reservado
                         , reservado2
                         , natureza_despesa
                         , exercicio_anterior
                         , cod_despesa
                         , cod_estrutural
                         , ( SELECT * FROM empenho.fn_saldo_dotacao( exercicio, cod_despesa ) ) AS saldo_dotacao
                         , SUM(saldo_resto) AS saldo_resto
                      FROM (
                          SELECT 1 AS tipo_registro
                               , '".$this->getDado('competencia')."' AS competencia
                               , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                               , 1 AS tipo_movimentacao
                               , orgao.cod_recurso
                               , '00'::VARCHAR AS reservado
                               , '0000'::VARCHAR AS reservado2
                               , orgao.natureza_despesa
                               , empenho.exercicio as exercicio_anterior
                               , despesa.cod_despesa
                               , despesa.cod_estrutural
                               , despesa.mascara_classificacao
                               , despesa.exercicio				
                               , despesa.cod_despesa||despesa.exercicio||orgao.natureza_despesa as dotacao
                               , ( ( SELECT * FROM empenho.fn_consultar_valor_empenhado(empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade) )
                                   -
                                   ( SELECT * FROM empenho.fn_consultar_valor_empenhado_anulado(empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade) )
                                 )
                                 -
                                 ( ( SELECT * FROM empenho.fn_consultar_valor_empenhado_pago(empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade) )
                                   -
                                   ( SELECT * FROM empenho.fn_consultar_valor_empenhado_pago_anulado(empenho.exercicio, empenho.cod_empenho, empenho.cod_entidade) )
                                 ) AS saldo_resto
            
                            FROM empenho.empenho
            
                      INNER JOIN empenho.pre_empenho
                              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                             AND pre_empenho.exercicio = empenho.exercicio
            
                      INNER JOIN ( SELECT *
                                     FROM (
                                        SELECT despesa.num_orgao
                                             , despesa.num_unidade
                                             , despesa.exercicio
                                             , pre_empenho_despesa.cod_pre_empenho
                                             , despesa.cod_recurso
                                             , SUBSTR(REPLACE(conta_despesa.cod_estrutural, '.', ''), 2, 1)::integer as natureza_despesa
                                          FROM empenho.pre_empenho_despesa
                                          JOIN orcamento.despesa
                                            ON despesa.exercicio = pre_empenho_despesa.exercicio
                                           AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                          JOIN orcamento.conta_despesa
                                            ON conta_despesa.exercicio = despesa.exercicio
                                           AND conta_despesa.cod_conta = despesa.cod_conta
                                         UNION
                                        SELECT restos_pre_empenho.num_orgao
                                             , restos_pre_empenho.num_unidade
                                             , restos_pre_empenho.exercicio
                                             , restos_pre_empenho.cod_pre_empenho
                                             , restos_pre_empenho.recurso as cod_recurso
                                             , SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural, '.', ''), 2, 1)::integer as natureza_despesa
                                          FROM empenho.restos_pre_empenho
                                          ) AS tbl                                          
                                 GROUP BY num_orgao
                                        , exercicio
                                        , num_unidade
                                        , cod_pre_empenho
                                        , cod_recurso
                                        , natureza_despesa
                                 ) AS orgao                               
                              ON orgao.exercicio = pre_empenho.exercicio
                             AND orgao.cod_pre_empenho = pre_empenho.cod_pre_empenho

                      INNER JOIN ( SELECT despesa.*
                                        , conta_despesa.cod_estrutural
                                        , vw_classificacao_despesa.mascara_classificacao
                                        , pre_empenho_despesa.cod_pre_empenho
                                     FROM empenho.pre_empenho_despesa
                                     JOIN orcamento.despesa
                                       ON despesa.exercicio = pre_empenho_despesa.exercicio
                                      AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                     JOIN orcamento.conta_despesa
                                       ON conta_despesa.exercicio = despesa.exercicio
                                      AND conta_despesa.cod_conta = despesa.cod_conta
                                     JOIN orcamento.vw_classificacao_despesa
                                       ON vw_classificacao_despesa.exercicio = pre_empenho_despesa.exercicio
                                      AND vw_classificacao_despesa.cod_conta = pre_empenho_despesa.cod_conta
                                    WHERE despesa.exercicio = '".$this->getDado('exercicio')."'
                                 ) AS despesa
                              ON despesa.exercicio = pre_empenho.exercicio
                             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
            
                           WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
            
                        GROUP BY tipo_registro
                               , competencia
                               , unidade_gestora
                               , tipo_movimentacao
                               , orgao.cod_recurso
                               , reservado
                               , reservado2
                               , orgao.natureza_despesa
                               , empenho.exercicio
                               , despesa.cod_despesa
                               , despesa.exercicio
                               , empenho.cod_empenho
                               , empenho.cod_entidade
                               , despesa.cod_estrutural
                               , despesa.mascara_classificacao
                           ) AS restos

                     WHERE restos.saldo_resto > 0
                       AND exercicio_anterior = '".$this->getDado('exercicio')."'

                  GROUP BY tipo_registro
                         , competencia
                         , unidade_gestora
                         , tipo_movimentacao
                         , cod_recurso
                         , reservado
                         , reservado2
                         , natureza_despesa
                         , exercicio_anterior
                         , dotacao
                         , saldo_dotacao
                         , cod_despesa
                         , cod_estrutural
                       ) AS resto_final

              GROUP BY tipo_registro
                     , competencia
                     , unidade_gestora
                     , tipo_movimentacao
                     , cod_recurso
                     , reservado
                     , reservado2
                     , natureza_despesa
                     , exercicio_anterior
            
              ORDER BY exercicio_anterior
                     , cod_recurso
                     , natureza_despesa
        ";
        return $stSql;
    }
    
}

?>