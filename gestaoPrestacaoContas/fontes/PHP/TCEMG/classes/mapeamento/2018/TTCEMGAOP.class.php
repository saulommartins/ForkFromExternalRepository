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
    * Classe de mapeamento da tabela TTCEMG
    * Data de Criação: 26/02/2014

    * @author Analista: Valtair
    * @author Desenvolvedor: Carlos Adriano

    * @ignore
    * $Id: TTCEMGAOP.class.php 66183 2016-07-27 13:01:29Z franver $
    * $Date: 2016-07-27 10:01:29 -0300 (Wed, 27 Jul 2016) $
    * $Author: franver $
    * $Rev: 66183 $
    *

    * @package URBEM
    * @subpackage Mapeamento
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEMGAOP extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOP10.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOP10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOP10().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
                                            
    public function montaRecuperaDadosAOP10()
    {
        $stSql  = "
                  SELECT '10' AS tiporegistro
                       , LPAD(ordem_pagamento.cod_ordem::VARCHAR,7,'0')||ordem_pagamento.exercicio||TO_CHAR(nlpa.timestamp_anulada,'hh24mi') AS codreduzido
                       , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS codorgao
                       , CASE WHEN pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho AND pre_empenho.implantado = 't'
                              THEN CASE WHEN uniorcam_restos_atual.num_orgao IS NOT NULL
                                        THEN LPAD(LPAD(uniorcam_restos_atual.num_orgao::VARCHAR,2,'0')||LPAD(uniorcam_restos_atual.num_unidade::VARCHAR,2,'0'),5,'0')::VARCHAR
                                        ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')::VARCHAR
                                    END
                              ELSE LPAD((lpad(despesa.num_orgao::VARCHAR, 3, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')),5,'0')::VARCHAR
                          END AS codunidadesub
                       , TO_CHAR(nlp.timestamp,'yyyymmddHH24MI')||LPAD(ordem_pagamento.cod_ordem::VARCHAR,10,'0') AS nroop -- MI : minuto (00-59)
                       , TO_CHAR(nlp.timestamp,'ddmmyyyy') AS dtpagamento
                       , TO_CHAR(nlpa.timestamp_anulada,'yyyymmddhh24MISS')||LPAD(ordem_pagamento.cod_ordem::VARCHAR, 8, '0') AS nroanulacaoop
                       , TO_CHAR(nlpa.timestamp_anulada,'ddmmyyyy') AS dtanulacaoop
                       , CASE WHEN nlpa.observacao = ''
                              THEN 'Anulação de Pagamento'
                              ELSE nlpa.observacao
                          END AS justificativaanulacao
                       , SUM(COALESCE(nlpa.vl_anulado,0.00)) AS vlanulacaoop
                    FROM empenho.nota_liquidacao_paga AS nlp
              INNER JOIN empenho.nota_liquidacao_paga_anulada AS nlpa
                      ON nlpa.exercicio    = nlp.exercicio
                     AND nlpa.cod_nota     = nlp.cod_nota
                     AND nlpa.cod_entidade = nlp.cod_entidade
                     AND nlpa.timestamp    = nlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                      ON nlp.cod_entidade = plnlp.cod_entidade
                     AND nlp.cod_nota     = plnlp.cod_nota
                     AND nlp.exercicio    = plnlp.exercicio_liquidacao
                     AND nlp.timestamp    = plnlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao AS pl
                      ON pl.cod_entidade         = plnlp.cod_entidade
                     AND pl.cod_nota             = plnlp.cod_nota
                     AND pl.exercicio            = plnlp.exercicio
                     AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                     AND pl.cod_ordem            = plnlp.cod_ordem
               LEFT JOIN empenho.nota_liquidacao AS nl
                      ON nl.exercicio    = pl.exercicio_liquidacao
                     AND nl.cod_nota     = pl.cod_nota
                     AND nl.cod_entidade = pl.cod_entidade
               LEFT JOIN empenho.empenho
                      ON empenho.exercicio    = nl.exercicio_empenho
                     AND empenho.cod_entidade = nl.cod_entidade
                     AND empenho.cod_empenho  = nl.cod_empenho
               LEFT JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = nlp.cod_entidade
                     AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo   = 55
                     AND configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
               LEFT JOIN empenho.ordem_pagamento
                      ON pl.exercicio    = ordem_pagamento.exercicio
                     AND pl.cod_entidade = ordem_pagamento.cod_entidade
                     AND pl.cod_ordem    = ordem_pagamento.cod_ordem
               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
               LEFT JOIN empenho.restos_pre_empenho
                      ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = restos_pre_empenho.exercicio 
               LEFT JOIN tcemg.uniorcam AS uniorcam_restos
                      ON uniorcam_restos.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam_restos.num_orgao   = restos_pre_empenho.num_orgao
                     AND uniorcam_restos.exercicio   = restos_pre_empenho.exercicio    
                     AND uniorcam_restos.num_orgao_atual IS NOT NULL

               LEFT JOIN tcemg.uniorcam AS uniorcam_restos_atual
                      ON uniorcam_restos_atual.num_unidade = uniorcam_restos.num_unidade_atual
                     AND uniorcam_restos_atual.num_orgao   = uniorcam_restos.num_orgao_atual
                     AND uniorcam_restos_atual.exercicio   = '".$this->getDado('exercicio')."' 
        
               LEFT JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio   = pre_empenho_despesa.exercicio

                   WHERE nlpa.cod_entidade IN (".$this->getDado('entidade').")
                     AND TO_DATE(nlpa.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                    AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                GROUP BY tiporegistro
                       , codreduzido
                       , codorgao
                       , codunidadesub
                       , nroop
                       , dtpagamento
                       , nroanulacaoop
                       , dtanulacaoop
                       , justificativaanulacao
                ORDER BY nroop
        ";
        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosAOP11.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosAOP11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosAOP11().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosAOP11()
    {
     
        $stSql  = "    
                  SELECT '11' AS tiporegistro
                       , LPAD(ordem_pagamento.cod_ordem::VARCHAR,7,'0')||ordem_pagamento.exercicio||TO_CHAR(nlpa.timestamp_anulada,'hh24mi') AS codreduzido -- MI : minuto (00-59)
                       , CASE WHEN TO_CHAR(empenho.dt_empenho, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(plnlp.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')."
                              THEN '3'
                              WHEN TO_CHAR(empenho.dt_empenho, 'yyyy')::INTEGER < ".$this->getDado('exercicio')." AND TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(plnlp.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')."
                              THEN '4'
                              WHEN TO_CHAR(empenho.dt_empenho, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(nl.dt_liquidacao, 'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND TO_CHAR(plnlp.timestamp,'yyyy')::INTEGER = ".$this->getDado('exercicio')." AND conta_despesa.cod_estrutural ILIKE '4.6%'
                              THEN '2'
                              ELSE '1'
                          END AS tipopagamento
                       , empenho.cod_empenho AS nroempenho
                       , empenho.dt_empenho AS dtempenho
                       , TCEMG.numero_nota_liquidacao( '".$this->getDado('exercicio')."'
                                                     , empenho.cod_entidade
                                                     , nl.cod_nota
                                                     , nl.exercicio_empenho
                                                     , empenho.cod_empenho
                         ) AS nroliquidacao
                       , nl.dt_liquidacao AS dtliquidacao
                       , CASE WHEN restos_pre_empenho.recurso IS NOT NULL
                              THEN restos_pre_empenho.recurso
                              ELSE despesa.cod_recurso
                          END AS codfontrecurso
                       , SUM(COALESCE(nlpa.vl_anulado,0.00)) AS vlanulacaofonte

                    FROM empenho.nota_liquidacao_paga AS nlp
              INNER JOIN empenho.nota_liquidacao_paga_anulada AS nlpa
                      ON nlpa.exercicio    = nlp.exercicio
                     AND nlpa.cod_nota     = nlp.cod_nota
                     AND nlpa.cod_entidade = nlp.cod_entidade
                     AND nlpa.timestamp    = nlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                      ON nlp.cod_entidade = plnlp.cod_entidade
                     AND nlp.cod_nota     = plnlp.cod_nota
                     AND nlp.exercicio    = plnlp.exercicio_liquidacao
                     AND nlp.timestamp    = plnlp.timestamp
               LEFT JOIN empenho.pagamento_liquidacao AS pl
                      ON pl.cod_entidade         = plnlp.cod_entidade
                     AND pl.cod_nota             = plnlp.cod_nota
                     AND pl.exercicio            = plnlp.exercicio
                     AND pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
                     AND pl.cod_ordem            = plnlp.cod_ordem
               LEFT JOIN empenho.nota_liquidacao AS nl
                      ON nl.exercicio    = pl.exercicio_liquidacao
                     AND nl.cod_nota     = pl.cod_nota
                     AND nl.cod_entidade = pl.cod_entidade
               LEFT JOIN empenho.empenho
                      ON empenho.exercicio    = nl.exercicio_empenho
                     AND empenho.cod_entidade = nl.cod_entidade
                     AND empenho.cod_empenho  = nl.cod_empenho
               LEFT JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.cod_entidade = nlp.cod_entidade
                     AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
                     AND configuracao_entidade.cod_modulo   = 55
                     AND configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
               LEFT JOIN empenho.ordem_pagamento
                      ON pl.exercicio    = ordem_pagamento.exercicio
                     AND pl.cod_entidade = ordem_pagamento.cod_entidade
                     AND pl.cod_ordem    = ordem_pagamento.cod_ordem
               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
        
               LEFT JOIN empenho.restos_pre_empenho
                      ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
                     AND pre_empenho.exercicio = restos_pre_empenho.exercicio 
        
               LEFT JOIN tcemg.uniorcam AS uniorcam_restos
                      ON uniorcam_restos.num_unidade = restos_pre_empenho.num_unidade
                     AND uniorcam_restos.num_orgao   = restos_pre_empenho.num_orgao
                     AND uniorcam_restos.exercicio   = restos_pre_empenho.exercicio    
                     AND uniorcam_restos.num_orgao_atual IS NOT NULL

               LEFT JOIN tcemg.uniorcam AS uniorcam_restos_atual
                      ON uniorcam_restos_atual.num_unidade = uniorcam_restos.num_unidade_atual
                     AND uniorcam_restos_atual.num_orgao   = uniorcam_restos.num_orgao_atual
                     AND uniorcam_restos_atual.exercicio   = '".$this->getDado('exercicio')."' 
        
               LEFT JOIN orcamento.despesa
                      ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                     AND despesa.exercicio   = pre_empenho_despesa.exercicio
                
               LEFT JOIN orcamento.conta_despesa
                      ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                     AND conta_despesa.exercicio = pre_empenho_despesa.exercicio

                   WHERE nlpa.cod_entidade IN (".$this->getDado('entidade').")
                     AND TO_DATE(nlpa.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                    AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')

                GROUP BY tiporegistro
                       , codreduzido
                       , tipopagamento
                       , nroempenho
                       , dtempenho
                       , nroliquidacao
                       , dtliquidacao
                       , codfontrecurso
                ORDER BY codreduzido
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>