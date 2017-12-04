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
 * Extensão da Classe de Mapeamento TTCEALLancamentosEmpenho
 *
 * Data de Criação: 03/06/2014
 *
 * @author: Diogo Zarpelon
 *
 $Id: TTCEALLancamentosEmpenho.class.php 65568 2016-05-31 21:35:43Z michel $
 *
 * @ignore
 *
*/
class TTCEALLancamentosEmpenho extends Persistente
{
    /**
     * Método Construtor
     * @access Private
    */
    public function TTCEALLancamentosEmpenho()
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
    public function recuperaLancamentosEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;
        else
            $stOrdem = "ORDER BY empenho.exercicio , empenho.dt_empenho, empenho.cod_empenho, item_pre_empenho.classificacao";

        $stSql = $this->montaRecuperaLancamentosEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLancamentosEmpenho()
    {       
        $stSql = "
            SELECT
                    (
                        SELECT sw_cgm_pj.cnpj
                          FROM orcamento.entidade
                    INNER JOIN sw_cgm
                            ON sw_cgm.numcgm = entidade.numcgm
                    INNER JOIN sw_cgm_pessoa_juridica AS sw_cgm_pj
                            ON sw_cgm.numcgm = sw_cgm_pj.numcgm
                         WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                           AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
                    ) AS cod_und_gestora
                 ,  (
                        SELECT CASE WHEN valor != '' THEN valor ELSE '0000' END AS valor
                          FROM administracao.configuracao_entidade
                         WHERE exercicio = '".$this->getDado('exercicio')."'
                           AND cod_entidade = ".$this->getDado('und_gestora')."
                           AND cod_modulo = 62
                           AND parametro = 'tceal_configuracao_unidade_autonoma'
                    ) AS codigo_ua
                  , '".$this->getDado('bimestre')."'::varchar AS bimestre
                  , '".$this->getDado('exercicio')."'::varchar AS exercicio
                  , (empenho.exercicio::varchar || TO_CHAR(empenho.dt_empenho,'mm') || LPAD(empenho.cod_empenho::varchar,7,'0')::varchar)::varchar AS num_empenho
                  , item_pre_empenho.vl_total AS vl_empenho
                  , item_pre_empenho.sinal
                  , RPAD(replace(conta_despesa.cod_estrutural::varchar,'.',''),16,'0') AS cod_estrutural

               FROM empenho.empenho

         INNER JOIN empenho.pre_empenho
                 ON pre_empenho.exercicio       = empenho.exercicio
                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN ( SELECT empenho.exercicio
                           , empenho.cod_pre_empenho
                           , empenho.cod_entidade
                           , SUM(vl_total) AS vl_total
                           , '+' AS sinal
                           , 1 AS classificacao
                        FROM empenho.item_pre_empenho
                        JOIN empenho.empenho
                          ON empenho.exercicio = item_pre_empenho.exercicio
                         AND empenho.cod_pre_empenho = item_pre_empenho.cod_pre_empenho
                    GROUP BY empenho.exercicio
                           , empenho.cod_pre_empenho
                           , empenho.cod_entidade
                       UNION
                      SELECT exercicio
                           , cod_pre_empenho
                           , cod_entidade
                           , SUM(vl_anulado) AS vl_total
                           , '-' AS sinal
                           , 2 AS classificacao
                        FROM empenho.empenho_anulado_item
                    GROUP BY exercicio
                           , cod_pre_empenho
                           , cod_entidade
                    ) AS item_pre_empenho
                 ON item_pre_empenho.exercicio       = empenho.exercicio
                AND item_pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                AND item_pre_empenho.cod_entidade    = empenho.cod_entidade

         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

         INNER JOIN orcamento.despesa
                 ON despesa.exercicio   = pre_empenho_despesa.exercicio
                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

         INNER JOIN orcamento.conta_despesa
                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

              WHERE empenho.exercicio = '".Sessao::getExercicio()."'
                AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                AND empenho.dt_empenho BETWEEN TO_DATE('".$this->getDado('dtInicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFinal')."','dd/mm/yyyy')
                ";
    
        return $stSql;
    }
}

?>
