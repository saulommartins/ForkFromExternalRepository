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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CLA_PERSISTENTE);

class TTCEMGBalancoFinanceiro extends Persistente {
  public function TTCEMGBalancoFinanceiro() {
    parent::Persistente();
  }

  public function recuperaDadosBF10(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBF10();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBF10() {
  	$sql = "
        SELECT  10 as tipo_registro,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlRecOrcamenRecurOrdinarios'
                     AND campos.cod_recurso IN (100)
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                   END
                ) AS vl_rec_orc_recurso_ordinario,
                
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuEducacao'
                     AND campos.cod_recurso IN (101, 113, 118, 119, 122, 143, 144, 145, 146, 147)
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_rec_orc_recursos_vinculado_educacao,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuSaude'
                     AND campos.cod_recurso IN (102, 112, 123, 148, 149, 150, 151, 152, 153, 154, 155)
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_rec_orc_recursos_vinculado_saude,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuRPPS'
                     AND campos.cod_recurso IN (102, 103)
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_rec_orc_recursos_vinculado_rpps,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlRecOrcamenRecurVincuAssistSocial'
                     AND campos.cod_recurso IN (129, 142, 156)
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_rec_orc_recursos_vinculado_assist_social,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlRecOrcamenOutrasDestRecursos'
                     AND campos.cod_recurso IN (116, 117, 124, 157, 158, 190, 191, 192, 193)
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_rec_orc_outra_destinac_recurso,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlTransFinanExecuOrcamentaria'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_trans_finan_execucao_orcamentaria,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlTransFinanIndepenExecuOrcamentaria'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_trans_finan_indepen_execucao_orcamentaria,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlTransFinanReceAportesRPPS'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_trans_finan_recebida_aporte_rec_rpps,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlIncriRSPNaoProcessado'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_inscri_resto_pagar_nao_processado,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlIncriRSPProcessado'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_inscri_resto_pagar_processado,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlDepoRestituVinculados'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_depo_restituivel_vinculado,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlOutrosRecExtraorcamentario'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_outr_recebimento_extraorcamentario,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlSaldoExerAnteriorCaixaEquiCaixa'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_sal_exerc_anterior_caixa_equivalente_caixa,
                SUM (
                  CASE
                    WHEN campos.nome_tag = 'vlSaldoExerAnteriorDepoRestVinculados'
                    THEN COALESCE(campos.valor, 0)
                    ELSE 0
                  END
                ) AS vl_sal_exerc_anterior_deposito_restitui_valor_vinculado,
                0 AS vl_total_quadro_ingresso
          FROM  (
    ";
  	
    $sql.= $this->montaRecuperaValoresReceitasOrcamentarias();

  	$sql.= "
            ) AS campos

            WHERE  campos.nome_arquivo_pertencente = 'BF'
              AND  campos.tipo_registro = 10 
            GROUP  BY campos.nome_arquivo_pertencente";

  	return $sql;
  }

  public function montaRecuperaValoresReceitasOrcamentarias()
  {
    return "
          SELECT  configuracao_dcasp_arquivo.nome_tag,
                  configuracao_dcasp_arquivo.nome_arquivo_pertencente,
                  configuracao_dcasp_arquivo.tipo_registro,
                  totais_receitas.*,
                  CASE WHEN totais_receitas.valor > totais_receitas.vl_original
                       THEN 2
                       ELSE 1
                   END AS fase
            FROM  (
                    SELECT  conta_receita.cod_estrutural,
                            arrecadacao_receita.exercicio,
                            receita.vl_original,
                            receita.cod_recurso,
                            SUM(arrecadacao_receita.vl_arrecadacao) AS valor

                      FROM  tesouraria.arrecadacao_receita

                      JOIN  orcamento.receita
                        ON  arrecadacao_receita.cod_receita = receita.cod_receita
                       AND  arrecadacao_receita.exercicio = receita.exercicio

                      JOIN  orcamento.conta_receita
                        ON  conta_receita.cod_conta = receita.cod_conta
                       AND  conta_receita.exercicio = receita.exercicio

                     WHERE  arrecadacao_receita.exercicio = '".$this->getDado('exercicio')."'
                       AND  receita.cod_entidade IN (".$this->getDado('entidade').")

                     GROUP  BY conta_receita.cod_estrutural,
                               arrecadacao_receita.exercicio,
                               receita.vl_original,
                               receita.cod_recurso

                  )  AS totais_receitas

            JOIN  tcemg.configuracao_dcasp_registros
              ON  configuracao_dcasp_registros.exercicio = '".$this->getDado('exercicio')."'
             AND  replace(configuracao_dcasp_registros.conta_orc_receita, '.', '') = replace(totais_receitas.cod_estrutural, '.', '')

            JOIN  tcemg.configuracao_dcasp_arquivo using (seq_arquivo)
    ";
  }

  public function recuperaDadosBF20(&$rsRecordSet) {
    $obErro = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $boTransacao = new Transacao();

    $sql = $this->montaRecuperaDadosBF20();
    $this->setDebug($sql);
    $obErro = $obConexao->executaSQL($rsRecordSet, $sql, $boTransacao);

    return $obErro;
  }

  private function montaRecuperaDadosBF20() {
  	$sql = "SELECT 20 as tipo_registro,
    				       SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDespOrcamenRecurOrdinarios'
                            THEN CASE
                                   WHEN receita_orcamentaria.cod_recurso IN (100, 200)
                                     THEN COALESCE(receita_orcamentaria.valor, 0)
                                   ELSE 0
                                 END
	 	                      ELSE 0
                        END) AS vl_desp_orc_recurso_ordinario,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDespOrcamenRecurVincuEducacao'
                            THEN CASE
                                   WHEN receita_orcamentaria.cod_recurso IN (101, 201, 113, 213, 118, 218, 119, 219, 122, 222, 143, 243, 144, 244, 145, 245, 146, 246, 147, 247, 289)
                                     THEN COALESCE(receita_orcamentaria.valor, 0)
                                   ELSE 0
                                 END
 	                        ELSE 0
                        END) AS vl_desp_orc_recursos_vinculado_educacao,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDespOrcamenRecurVincuSaude'
                            THEN CASE
                                   WHEN receita_orcamentaria.cod_recurso IN (102, 202, 112, 212, 123, 223, 148, 248, 149, 249, 150, 250, 151, 251, 152, 252, 153, 253, 154, 254, 155, 255, 288)
                                     THEN COALESCE(receita_orcamentaria.valor, 0)
                                   ELSE 0
                                 END
 	                        ELSE 0
                        END) AS vl_desp_orc_recursos_vinculado_saude,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDespOrcamenRecurVincuRPPS'
                            THEN CASE
                                   WHEN receita_orcamentaria.cod_recurso IN (100, 103, 200, 203)
                                     THEN COALESCE(receita_orcamentaria.valor, 0)
                                   ELSE 0
                                 END
 	                        ELSE 0
                        END) AS vl_desp_orc_recursos_vinculado_rpps,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDespOrcamenRecurVincuAssistSocial'
                            THEN CASE
                                   WHEN receita_orcamentaria.cod_recurso IN (129, 229, 142, 242, 156, 256)
                                     THEN COALESCE(receita_orcamentaria.valor, 0)
                                   ELSE 0
                                 END
                          ELSE 0
                        END) AS vl_desp_orc_recursos_vinculado_assist_social,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrasDespOrcamenDestRecursos'
                            THEN CASE
                                   WHEN receita_orcamentaria.cod_recurso IN (116, 216, 117, 217, 124, 224, 157, 257, 158, 258, 190, 290, 191, 291, 192, 292, 193, 293)
                                     THEN COALESCE(receita_orcamentaria.valor, 0)
                                   ELSE 0
                                 END
                          ELSE 0
                        END) AS vl_desp_orc_outra_destinac_recurso,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransFinanConcExecOrcamentaria'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_trans_finan_execucao_orcamentaria,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransFinanConcIndepenExecOrcamentaria'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_trans_finan_indepen_execucao_orcamentaria,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTransFinanConcAportesRecuRPPS'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_trans_finan_concedida_aporte_rec_rpps,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPagRSPNaoProcessado'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_pag_restos_pagar_nao_processado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlPagRSPProcessado'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_pag_restos_pagar_processado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlDeposRestVinculados'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_depo_restituivel_vinculado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlOutrosPagExtraorcamentarios'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_outr_pagamento_extraorcamentario,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlSaldoExerAtualCaixaEquiCaixa'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_sal_exerc_atual_caixa_equivalente_caixa,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlSaldoExerAtualDepoRestVinculados'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_sal_exerc_atual_deposito_restitui_valor_vinculado,
                   SUM (CASE
                          WHEN configuracao_dcasp_arquivo.nome_tag = 'vlTotalDispendios'
                            THEN COALESCE(receita_orcamentaria.valor, 0)
                          ELSE 0
                        END) AS vl_total_dispendio ";
  	$sql.= $this->montaFromValoresOrcamentarios();
  	$sql.= "WHERE configuracao_dcasp_arquivo.nome_arquivo_pertencente = 'BF'
			            AND configuracao_dcasp_registros.exercicio = '" . $this->getDado('exercicio') . "'
                  AND configuracao_dcasp_registros.tipo_registro = 20
            GROUP BY configuracao_dcasp_arquivo.nome_arquivo_pertencente";
  	return $sql;
  }

  private function montaFromValoresOrcamentarios() {
    return "FROM tcemg.configuracao_dcasp_registros
                 JOIN tcemg.configuracao_dcasp_arquivo USING (seq_arquivo)
                 LEFT JOIN (SELECT DISTINCT replace(conta_receita.cod_estrutural, '.', '') as conta,
                                   arrecadacao_receita.cod_arrecadacao,
                                   arrecadacao_receita.exercicio,
                                   receita.cod_conta,
                                   receita.cod_entidade,
                                   receita.cod_recurso,
                                   arrecadacao_receita.vl_arrecadacao as valor
                            FROM tesouraria.arrecadacao_receita
                                 JOIN orcamento.receita ON arrecadacao_receita.cod_receita = receita.cod_receita
                                      AND arrecadacao_receita.exercicio = receita.exercicio
                                 JOIN orcamento.conta_receita ON conta_receita.cod_conta = receita.cod_conta
                                      AND conta_receita.exercicio = receita.exercicio
                           ) AS receita_orcamentaria ON configuracao_dcasp_registros.exercicio = receita_orcamentaria.exercicio
                      AND replace(configuracao_dcasp_registros.conta_orc_receita, '.', '') = receita_orcamentaria.conta
                      AND receita_orcamentaria.cod_entidade IN (" . $this->getDado('entidade') . ")
                 LEFT JOIN (SELECT DISTINCT empenho.exercicio,
                                   empenho.cod_empenho,
                                   empenho.cod_entidade,
                                   nota_liquidacao_item.vl_total as valor,
                                   replace(conta_despesa.cod_estrutural, '.', '') as conta,
                                   despesa.cod_recurso
                            FROM orcamento.despesa
                                 JOIN orcamento.conta_despesa ON despesa.cod_conta = conta_despesa.cod_conta
                                      AND despesa.exercicio = conta_despesa.exercicio
                                 JOIN empenho.pre_empenho_despesa ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                                      AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                 JOIN empenho.pre_empenho ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
                                 JOIN empenho.empenho ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                      AND pre_empenho.exercicio = empenho.exercicio
                                 JOIN empenho.nota_liquidacao ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                                      AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                      AND empenho.cod_empenho = nota_liquidacao.cod_empenho
                                 JOIN empenho.nota_liquidacao_item ON nota_liquidacao.exercicio = nota_liquidacao_item.exercicio
                                      AND nota_liquidacao.cod_entidade = nota_liquidacao_item.cod_entidade
                                      AND nota_liquidacao.cod_nota = nota_liquidacao_item.cod_nota
                           ) AS despesa_orcamentaria ON configuracao_dcasp_registros.exercicio = despesa_orcamentaria.exercicio
                      AND replace(configuracao_dcasp_registros.conta_orc_despesa, '.', '') = despesa_orcamentaria.conta
                      AND despesa_orcamentaria.cod_entidade IN (" . $this->getDado('entidade') . ") ";
  }

  public function __destruct() {}

}
