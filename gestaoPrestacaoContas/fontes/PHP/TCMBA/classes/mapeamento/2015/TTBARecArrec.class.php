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
    * Extensão da Classe de mapeamento
    * Data de Criação: 15/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63028 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTBARecArrec extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT 1 AS tipo_registro
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , REPLACE(conta_receita.cod_estrutural,'.','') AS item_receita
                     , '".$this->getDado('exercicio')."'||'".$this->getDado('mes')."' AS competencia
                     , configuracao_lancamento_receita.cod_conta AS conta_contabil
                     , COALESCE(SUM(arrecadacao_receita.vl_arrecadacao),0.00) AS vl_receita
                     , TO_CHAR(arrecadacao_receita.timestamp_arrecadacao,'dd/mm/yyyy') AS dt_receita

                FROM orcamento.receita

          INNER JOIN orcamento.conta_receita
                  ON conta_receita.cod_conta = receita.cod_conta
                 AND conta_receita.exercicio = receita.exercicio

          INNER JOIN contabilidade.lancamento_receita
                  ON lancamento_receita.cod_receita = receita.cod_receita
                 AND lancamento_receita.exercicio = receita.exercicio

          INNER JOIN contabilidade.configuracao_lancamento_receita
                  ON configuracao_lancamento_receita.exercicio = conta_receita.exercicio
                 AND configuracao_lancamento_receita.cod_conta_receita = conta_receita.cod_conta

          INNER JOIN tesouraria.arrecadacao_receita
                  ON arrecadacao_receita.cod_receita = receita.cod_receita
                 AND arrecadacao_receita.exercicio = receita.exercicio

          INNER JOIN tesouraria.arrecadacao
                  ON arrecadacao.cod_arrecadacao = arrecadacao_receita.cod_arrecadacao
                 AND arrecadacao.exercicio = arrecadacao_receita.exercicio
                 AND arrecadacao.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

          INNER JOIN contabilidade.plano_analitica
                  ON plano_analitica.cod_plano = arrecadacao.cod_plano
                 AND plano_analitica.exercicio = arrecadacao.exercicio

          INNER JOIN contabilidade.plano_banco
                  ON plano_banco.exercicio = plano_analitica.exercicio
                 AND plano_banco.cod_plano = plano_analitica.cod_plano

               WHERE TO_DATE(TO_CHAR(arrecadacao_receita.timestamp_arrecadacao,'dd/mm/yyyy'),'dd/mm/yyyy')
                        BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                 AND receita.exercicio = '".$this->getDado('exercicio')."'
                 AND receita.cod_entidade IN (".$this->getDado('entidades').")
                 AND lancamento_receita.estorno <> 't'

              GROUP BY tipo_registro, unidade_gestora, item_receita, competencia, conta_contabil, dt_receita

              ORDER BY unidade_gestora, item_receita, conta_contabil, dt_receita

            ";
    return $stSql;
}

}
