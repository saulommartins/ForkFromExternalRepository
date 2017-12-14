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
    * Classe de consulta do arquivo TCE_4010.TXT
    * Data de Criação: 19/02/2009

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CLA_PERSISTENTE);

class TTCERSReceitaPublica extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCERSReceitaPublica()
    {
        parent::Persistente();
    }

    public function montaRecuperaTodos()
    {
        $stSql = "
    SELECT arrecadacao_carne.exercicio
         --, '0000000000000000000000000000000000000000000' AS codigo_barras
         , TO_CHAR(TO_DATE(arrecadacao_carne.timestamp_arrecadacao, 'yyyy-mm-dd'), 'ddmmyyyy') AS dt_operacao
         , TO_CHAR(TO_DATE(boletim.dt_boletim, 'yyyy-mm-dd'), 'ddmmyyyy') AS dt_registro_contabil
         , REPLACE(receita.vl_original, '.', '') AS vl_original
         --, 0.00 AS vl_multa_descontos
         --, 0.00 AS vl_operacao
         , arrecadacao_receita.cod_receita
         , TRIM(conta_receita.descricao) AS nome_receita
         , REPLACE(plano_conta.cod_estrutural, '.', '') AS cod_plano_conta
         , '0000' AS preenche_zeros
         , sw_cgm.numcgm AS cod_contribuinte
         , sw_cgm.nom_cgm AS nome_contribuinte
         , CASE WHEN (sw_cgm_pessoa_fisica.numcgm IS NOT NULL) THEN
                    sw_cgm_pessoa_fisica.cpf
                ELSE
                    sw_cgm_pessoa_juridica.cnpj
           END AS cpf_cnpj
         , CASE WHEN (sw_cgm_pessoa_juridica.numcgm IS NOT NULL) THEN
                    sw_cgm_pessoa_juridica.insc_estadual
                ELSE
                    ''
           END AS inscricao_estadual
         , TO_CHAR(TO_DATE(receita.dt_criacao, 'yyyy-mm-dd'), 'ddmmyyyy') AS dt_cadastro_inicial
         , TO_CHAR(TO_DATE(parcela.vencimento, 'yyyy-mm-dd'), 'ddmmyyyy') AS dt_vencimento_receita
         --, 01 AS tipo_operacao
         --, 00 AS modalidade_operacao
         --, 6 AS tipo_moeda
         , 2 AS numero_casas_decimais
         --, '00000000000000000' AS vl_moeda
         --, '000000000000000000000000000000' AS cod_identificador
         --, '' AS historico
         , conta_corrente.cod_banco
         , conta_corrente.cod_agencia
         , conta_corrente.num_conta_corrente AS conta_corrente
      FROM arrecadacao.carne
      JOIN tesouraria.arrecadacao_carne
        ON arrecadacao_carne.numeracao = carne.numeracao
       AND arrecadacao_carne.cod_convenio = carne.cod_convenio
      JOIN tesouraria.arrecadacao
        ON arrecadacao.cod_arrecadacao       = arrecadacao_carne.cod_arrecadacao
       AND arrecadacao.exercicio             = arrecadacao_carne.exercicio
       AND arrecadacao.timestamp_arrecadacao = arrecadacao_carne.timestamp_arrecadacao
      JOIN tesouraria.boletim
        ON boletim.cod_boletim  = arrecadacao.cod_boletim
       AND boletim.cod_entidade = arrecadacao.cod_entidade
       AND boletim.exercicio    = arrecadacao.exercicio
      JOIN tesouraria.arrecadacao_receita
        ON arrecadacao_receita.cod_arrecadacao = arrecadacao.cod_arrecadacao
       AND arrecadacao_receita.exercicio       = arrecadacao.exercicio
       AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
      JOIN orcamento.receita
        ON receita.cod_receita = arrecadacao_receita.cod_receita
       AND receita.exercicio   = arrecadacao_receita.exercicio
      JOIN orcamento.conta_receita
        ON conta_receita.cod_conta = receita.cod_conta
       AND conta_receita.exercicio = receita.exercicio
      JOIN contabilidade.plano_analitica
        ON plano_analitica.cod_plano = arrecadacao.cod_plano
       AND plano_analitica.exercicio = arrecadacao.exercicio
      JOIN contabilidade.plano_conta
        ON plano_conta.cod_conta = plano_analitica.cod_conta
       AND plano_conta.exercicio = plano_analitica.exercicio
      JOIN contabilidade.plano_banco
        ON plano_banco.cod_plano = plano_analitica.cod_plano
       AND plano_banco.exercicio = plano_analitica.exercicio
      JOIN monetario.conta_corrente
        ON conta_corrente.cod_banco = plano_banco.cod_banco
       AND conta_corrente.cod_agencia = plano_banco.cod_agencia
       AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
      JOIN arrecadacao.parcela
        ON parcela.cod_parcela = carne.cod_parcela
      JOIN arrecadacao.lancamento
        ON lancamento.cod_lancamento = parcela.cod_lancamento
      JOIN arrecadacao.lancamento_calculo
        ON lancamento_calculo.cod_lancamento = lancamento.cod_lancamento
      JOIN arrecadacao.calculo_cgm
        ON calculo_cgm.cod_calculo = lancamento_calculo.cod_calculo
      JOIN sw_cgm
        ON calculo_cgm.numcgm = sw_cgm.numcgm
 LEFT JOIN sw_cgm_pessoa_fisica
        ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
 LEFT JOIN sw_cgm_pessoa_juridica
        ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
     WHERE TO_DATE(arrecadacao_carne.timestamp_arrecadacao, 'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicial')."', 'dd/mm/yyyy')
                                                                              AND TO_DATE('".$this->getDado('dtFinal')."', 'dd/mm/yyyy')
       AND arrecadacao_carne.exercicio = '".$this->getDado('exercicio')."'
        ";

        return $stSql;
    }
}
