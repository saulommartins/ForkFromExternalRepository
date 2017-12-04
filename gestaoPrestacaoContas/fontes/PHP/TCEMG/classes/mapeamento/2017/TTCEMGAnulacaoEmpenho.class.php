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
  * Página de Include Oculta - Exportação Arquivos TCEMG - ANL.csv
  * Data de Criação: 04/09/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Eduardo Paculski Schitz
  *
  * @ignore
  * $Id: TTCEMGAnulacaoEmpenho.class.php 66613 2016-10-03 12:51:54Z franver $
  * $Date: 2016-10-03 09:51:54 -0300 (Mon, 03 Oct 2016) $
  * $Author: franver $
  * $Rev: 66613 $
  *
*/
require_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";

class TTCEMGAnulacaoEmpenho extends TEmpenhoEmpenho
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGAnulacaoEmpenho()
    {
        parent::TEmpenhoEmpenho();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera ("montaRecuperaExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao10()
    {
        $stSql = "
          SELECT 10 AS tipo_registro
               , LPAD(configuracao_entidade.valor::VARCHAR,2,'0') AS cod_orgao
               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       THEN CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                 THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                 ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                             END
                       ELSE LPAD((LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')), 5, '0')
                  END AS cod_unidade
               , empenho.cod_empenho AS num_empenho
               , TO_CHAR (empenho.dt_empenho, 'ddmmyyyy') AS dt_empenho
               , TO_CHAR(empenho_anulado.timestamp,'ddmmyyyy') AS dt_anulacao
               , empenho.exercicio||LPAD(tc.numero_anulacao_empenho(empenho_anulado.exercicio, empenho_anulado.cod_entidade, empenho_anulado.cod_empenho, empenho_anulado.timestamp)::VARCHAR, 18, '0') AS num_anulacao
               , 1 AS tipo_anulacao -- fazer a análise de quais campos ou ações criar
               , empenho_anulado.motivo AS espc_anl_emp
               , SUM(valor_anulado) AS vl_anulado
            FROM (
                  SELECT empenho_anulado.exercicio
                       , empenho_anulado.cod_entidade
                       , empenho_anulado.cod_empenho
                       , empenho_anulado.timestamp
                       , empenho_anulado.motivo
                       , COALESCE(SUM(empenho_anulado_item.vl_anulado),0.00) AS valor_anulado
                    FROM empenho.empenho_anulado
              INNER JOIN empenho.empenho_anulado_item
                      ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                     AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                     AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
                GROUP BY empenho_anulado.exercicio
                       , empenho_anulado.cod_entidade
                       , empenho_anulado.cod_empenho
                       , empenho_anulado.timestamp
                 ) AS empenho_anulado

      INNER JOIN empenho.empenho
              ON empenho_anulado.exercicio    = empenho.exercicio
             AND empenho_anulado.cod_entidade = empenho.cod_entidade
             AND empenho_anulado.cod_empenho  = empenho.cod_empenho

      INNER JOIN empenho.pre_empenho
              ON pre_empenho.exercicio = empenho.exercicio
             AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

       LEFT JOIN empenho.pre_empenho_despesa
              ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             AND pre_empenho_despesa.exercicio = pre_empenho.exercicio

       LEFT JOIN empenho.restos_pre_empenho
              ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
             AND pre_empenho.exercicio = restos_pre_empenho.exercicio 

       LEFT JOIN tcemg.uniorcam
              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
             AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao
             AND uniorcam.exercicio   = restos_pre_empenho.exercicio
             AND uniorcam.num_orgao_atual IS NOT NULL

       LEFT JOIN orcamento.despesa
              ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             AND despesa.exercicio   = pre_empenho_despesa.exercicio

     INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
             AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
             AND configuracao_entidade.cod_modulo   = 55
             AND configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
 
           WHERE empenho_anulado.cod_entidade IN (".$this->getDado('entidades').")
             AND empenho.exercicio = '".$this->getDado('exercicio')."'
             AND empenho_anulado.timestamp::date BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                                                     AND last_day(TO_DATE('".$this->getDado('exercicio')."' || '-' || ".$this->getDado('mes')." || '-' || '01','yyyy-mm-dd'))
        GROUP BY tipo_registro
               , cod_orgao
               , cod_unidade
               , num_empenho
               , dt_empenho
               , dt_anulacao
               , num_anulacao
               , tipo_anulacao
               , espc_anl_emp
        ORDER BY num_empenho
               , num_anulacao
        ";
        return $stSql;
    }

    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera ("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "
          SELECT 11 AS tipo_registro
               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                       THEN CASE WHEN uniorcam.num_orgao_atual IS NOT NULL
                                 THEN LPAD(LPAD(uniorcam.num_orgao_atual::VARCHAR,2,'0')||LPAD(uniorcam.num_unidade_atual::VARCHAR,2,'0'),5,'0')
                                 ELSE LPAD(restos_pre_empenho.num_unidade::VARCHAR,5,'0')
                             END
                       ELSE LPAD((LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0')), 5, '0')
                  END AS cod_unidade
               , empenho.cod_empenho AS num_empenho
               , empenho.exercicio||LPAD(tc.numero_anulacao_empenho(empenho_anulado.exercicio, empenho_anulado.cod_entidade, empenho_anulado.cod_empenho, empenho_anulado.timestamp)::VARCHAR, 18, '0') AS num_anulacao
               , CASE WHEN restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                      THEN restos_pre_empenho.recurso
                      ELSE despesa.cod_recurso
                  END AS cod_fonte_recurso
               , SUM(valor_anulado) AS vl_anulacao_fonte
            FROM (
                  SELECT empenho_anulado.exercicio
                       , empenho_anulado.cod_entidade
                       , empenho_anulado.cod_empenho
                       , empenho_anulado.timestamp
                       , empenho_anulado.motivo
                       , COALESCE(SUM(empenho_anulado_item.vl_anulado),0.00) AS valor_anulado
                    FROM empenho.empenho_anulado
              INNER JOIN empenho.empenho_anulado_item
                      ON empenho_anulado_item.exercicio = empenho_anulado.exercicio
                     AND empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                     AND empenho_anulado_item.cod_empenho = empenho_anulado.cod_empenho
                     AND empenho_anulado_item.timestamp = empenho_anulado.timestamp
                GROUP BY empenho_anulado.exercicio
                       , empenho_anulado.cod_entidade
                       , empenho_anulado.cod_empenho
                       , empenho_anulado.timestamp
                 ) AS empenho_anulado
      INNER JOIN empenho.empenho
              ON empenho_anulado.exercicio    = empenho.exercicio
             AND empenho_anulado.cod_entidade = empenho.cod_entidade
             AND empenho_anulado.cod_empenho  = empenho.cod_empenho
      INNER JOIN empenho.pre_empenho
              ON pre_empenho.exercicio = empenho.exercicio
             AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
       LEFT JOIN empenho.pre_empenho_despesa
              ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
       LEFT JOIN empenho.restos_pre_empenho
              ON pre_empenho.cod_pre_empenho = restos_pre_empenho.cod_pre_empenho
             AND pre_empenho.exercicio = restos_pre_empenho.exercicio 
       LEFT JOIN tcemg.uniorcam
              ON uniorcam.num_unidade = restos_pre_empenho.num_unidade
             AND uniorcam.num_orgao   = restos_pre_empenho.num_orgao
             AND uniorcam.exercicio   = restos_pre_empenho.exercicio
             AND uniorcam.num_orgao_atual IS NOT NULL
       LEFT JOIN orcamento.despesa
              ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             AND despesa.exercicio   = pre_empenho_despesa.exercicio
     INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.cod_entidade = empenho.cod_entidade
             AND configuracao_entidade.exercicio    = '".$this->getDado('exercicio')."'
             AND configuracao_entidade.cod_modulo   = 55
             AND configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
           WHERE empenho_anulado.cod_entidade IN (".$this->getDado('entidades').")
             AND empenho.exercicio = '".$this->getDado('exercicio')."'
             AND empenho_anulado.timestamp::date BETWEEN TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."', 'dd/mm/yyyy')
                                                     AND last_day(TO_DATE('".$this->getDado('exercicio')."'||'-'||'".$this->getDado('mes')."'||'-'||'01','yyyy-mm-dd'))
        GROUP BY tipo_registro
               , cod_unidade
               , num_empenho
               , num_anulacao
               , cod_fonte_recurso               
        ORDER BY num_empenho
               , num_anulacao
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
