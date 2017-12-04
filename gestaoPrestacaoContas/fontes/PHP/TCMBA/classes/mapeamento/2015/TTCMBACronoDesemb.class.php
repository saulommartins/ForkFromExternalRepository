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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBACronoDesemb extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMBACronoDesemb() {
      parent::Persistente();
      $this->setEstrutura( array() );
      $this->setEstruturaAuxiliar( array() );
      $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = " SELECT 1 AS tipo_registro
                        , ( SELECT valor
                              FROM administracao.configuracao_entidade
                             WHERE cod_modulo = 45
                               AND parametro = 'tceba_codigo_unidade_gestora'
                               AND cod_entidade = '".$this->getDado('entidade')."'
                          ) AS unidade_gestora
                        , ".$this->getDado('exercicio')." AS ano
                        , despesa.num_orgao
                        , 1 AS mes_despesa1
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 1 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa1
                        , 2 AS mes_despesa2
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 2 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa2
                        , 3 AS mes_despesa3
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 3 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa3
                        , 4 AS mes_despesa4
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 4 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa4
                        , 5 AS mes_despesa5
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 5 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa5
                        , 6 AS mes_despesa6
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 6 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa6
                        , 7 AS mes_despesa7
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 7 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa7
                        , 8 AS mes_despesa8
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 8 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa8
                        , 9 AS mes_despesa9
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 9 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa9
                        , 10 AS mes_despesa10
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 10 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa10
                        , 11 AS mes_despesa11
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 11 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa11
                        , 12 AS mes_despesa12
                        , COALESCE(SUM((SELECT SUM(vl_previsto) FROM orcamento.previsao_despesa AS pd WHERE pd.exercicio = '".$this->getDado('exercicio')."' AND periodo = 12 AND pd.cod_despesa = despesa.cod_despesa)),0.00) AS vl_despesa12

                   FROM orcamento.despesa

                  WHERE despesa.exercicio = '".$this->getDado('exercicio')."'

                  GROUP BY unidade_gestora,
                           num_orgao,
                           ano,
                           mes_despesa1,
                           mes_despesa2,
                           mes_despesa3,
                           mes_despesa4,
                           mes_despesa5,
                           mes_despesa6,
                           mes_despesa7,
                           mes_despesa8,
                           mes_despesa9,
                           mes_despesa10,
                           mes_despesa11,
                           mes_despesa12

                  ORDER BY num_orgao
        ";

        return $stSql;
    }

}
