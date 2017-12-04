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
    * Extensão da Classe de Mapeamento TTCEALCredor
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: TTCEALBalanceteReceita.class.php 65563 2016-05-31 20:36:59Z michel $
    *
    * @ignore
    *
*/
class TTCEALBalanceteReceita extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALBalanceteReceita()
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
    public function recuperaBalanceteReceita(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaBalanceteReceita().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaBalanceteReceita()
    {       
        $stSql = "
                    SELECT
                  CASE WHEN codigo_ua <> '' THEN 
                    dados.codigo_ua
                       ELSE
                        '0000'
                  END AS codigo_ua
                  , cod_und_gestora
                  , cod_orgao
                  , cod_und_orcamentaria
                  , cod_conta_receita
                  , RPAD(cod_conta_contabil,17,'0') AS cod_conta_contabil
                  , ABS(prev_inicial_receita)       as prev_inicial_receita
                  , ABS(prev_atualizada_receita)    as prev_atualizada_receita
                  , ABS(receita_realizada)          as receita_realizada
                  , ABS(meta_arrecadacao_bimestral) as meta_arrecadacao_bimestral
                  , tipo_nivel_conta
                  , num_nivel_conta
                  , CASE WHEN cod_rec_vinculado IS NULL THEN
                              '000000000'
                         ELSE cod_rec_vinculado
                    END as cod_rec_vinculado
                  , descricao
                  , '".$this->getDado('exercicio')."'  AS exercicio
                  , '".$this->getDado('inBimestre')."' AS bimestre
                  , '000' AS carac_peculiar

                FROM (
                    SELECT
                        (SELECT PJ.cnpj
                           FROM orcamento.entidade
                           JOIN sw_cgm
                         ON sw_cgm.numcgm = entidade.numcgm
                           JOIN sw_cgm_pessoa_juridica AS PJ
                         ON sw_cgm.numcgm	  =PJ.numcgm
                          WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                        AND entidade.cod_entidade     = ".$this->getDado('cod_entidade')."
                        ) AS cod_und_gestora
            
                      , (SELECT valor
                           FROM administracao.configuracao_entidade
                          WHERE exercicio = '".$this->getDado('exercicio')."'
                        AND cod_entidade  = ".$this->getDado('cod_entidade')."
                        AND cod_modulo    = 62
                        AND parametro     = 'tceal_configuracao_unidade_autonoma'
                        )::VARCHAR AS codigo_ua

                      , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."', ".$this->getDado('cod_entidade').", 'orgao')::VARCHAR, 2, '0')   AS cod_orgao
                      , LPAD(tceal.recupera_codigo_orgao('".$this->getDado('exercicio')."', ".$this->getDado('cod_entidade').", 'unidade')::VARCHAR, 4, '0') AS cod_und_orcamentaria
                      
                      , (SELECT SUM(vl_periodo)
                           FROM orcamento.previsao_receita 
                     INNER JOIN orcamento.receita
                             ON receita.exercicio   = previsao_receita.exercicio
                            AND receita.cod_receita = previsao_receita.cod_receita                          
                     INNER JOIN orcamento.conta_receita
                             ON conta_receita.exercicio = receita.exercicio
                            AND conta_receita.cod_conta = receita.cod_conta
                          WHERE previsao_receita.exercicio = '".$this->getDado('exercicio')."'
                            AND REPLACE(conta_receita.cod_estrutural, '.', '') LIKE SUBSTR(RTRIM(REPLACE(cod_estrutural_1, '.', ''), '0'), 1, LENGTH(RTRIM(REPLACE(conta_receita.cod_estrutural, '.', ''), '0'))) || '%'	            
                            AND previsao_receita.periodo = ".$this->getDado('bimestre')."
                        ) AS meta_arrecadacao_bimestral
                                            
                      , RPAD(REPLACE(cod_estrutural_1, '.', ''),16,'0') AS cod_conta_receita
                      , CASE WHEN cod_estrutural_1 ilike '9.1.7%' THEN
                                  '621310100000000'::varchar
                             ELSE
                                  '621200000000000'::varchar
                        END AS cod_conta_contabil

                      , valor_previsto AS prev_inicial_receita
                      , valor_previsto AS prev_atualizada_receita
                      , ABS(arrecadado_periodo) AS receita_realizada
                      , orcamento.fn_tipo_conta_receita('".$this->getDado('exercicio')."', cod_estrutural_1) AS tipo_nivel_conta
                      , publico.fn_nivel(cod_estrutural_1) AS num_nivel_conta
                      , LPAD(recurso,9,'0') AS cod_rec_vinculado		  
                      , RTRIM(descricao) AS descricao
                      
                    FROM                                                                 
                      orcamento.fn_balancete_receita('".$this->getDado('exercicio')."', '','".$this->getDado('dt_inicial')."','".$this->getDado('dt_final')."','".$this->getDado('cod_entidade')."','','','','','','','') 
                    AS retorno(                      
                      cod_estrutural_1    varchar,                                           
                      receita             integer,                                           
                      recurso             varchar,                                           
                      descricao           varchar,                                           
                      valor_previsto      numeric,                                           
                      arrecadado_periodo  numeric,                                           
                      arrecadado_ano      numeric,                                           
                      diferenca           numeric  
                                                     
                    ) ORDER BY cod_estrutural_1
                ) AS dados
                ";

        return $stSql;
    }
}
?>