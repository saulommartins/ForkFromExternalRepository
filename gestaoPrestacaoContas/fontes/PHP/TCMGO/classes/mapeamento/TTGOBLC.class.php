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
    * Classe de mapeamento para o arquivo de exportação BLC
    * Data de Criação: 25/02/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOBLC extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function TTGOBLC()
    {
        parent::Persistente();
    }

    public function recuperaRegistro11(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRegistro11($stFiltro);
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRegistro11($stFiltro)
    {
        $stSql = "
      SELECT  c.tipo_registro
            , c.num_orgao AS cod_orgao
            , c.cod_tipo AS tipo_unidade_orcamentaria
            , c.indicador_superavit
            , c.cod_estrutural
            , EXTRACT(month from date '".SistemaLegado::dataToSql($this->getDado('data_final'))."') AS mes_referencia
            , SUM(c.vl_saidas) as vl_creditos
            , SUM(c.vl_entradas) as vl_debitos
            , SUM(c.saldo_inicial) as saldo_anterior
            , SUM(c.saldo_final) as saldo_final
          FROM  (
                     SELECT  '11'::int  AS  tipo_registro
                          ,  orgao.num_orgao
                          ,  orgao.cod_tipo
                          ,  plano_conta.cod_estrutural
                          ,  plano_conta.indicador_superavit
                          ,  (   SELECT  SUM(
                                                 (   SELECT  COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                                       FROM  contabilidade.conta_debito
                                                 INNER JOIN  contabilidade.valor_lancamento
                                                         ON  valor_lancamento.cod_lote = conta_debito.cod_lote
                                                        AND  valor_lancamento.tipo = conta_debito.tipo
                                                        AND  valor_lancamento.sequencia = conta_debito.sequencia
                                                        AND  valor_lancamento.exercicio = conta_debito.exercicio
                                                        AND  valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                                        AND  valor_lancamento.cod_entidade = conta_debito.cod_entidade
                                                 INNER JOIN  contabilidade.lancamento
                                                         ON  lancamento.sequencia = valor_lancamento.sequencia
                                                        AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                                        AND  lancamento.tipo = valor_lancamento.tipo
                                                        AND  lancamento.exercicio = valor_lancamento.exercicio
                                                        AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                                 INNER JOIN  contabilidade.lote
                                                         ON  lote.cod_lote = lancamento.cod_lote
                                                        AND  lote.exercicio = lancamento.exercicio
                                                        AND  lote.tipo = lancamento.tipo
                                                        AND  lote.cod_entidade = lancamento.cod_entidade
                                                        AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                                                        AND  lote.exercicio = '2012'
                                                        AND  lote.tipo != 'I'
                                                      WHERE  conta_debito.exercicio = pa.exercicio
                                                        AND  conta_debito.cod_plano = pa.cod_plano
                                                 )
                                         )  as vl_total
                                   FROM  contabilidade.plano_analitica AS pa
                                  WHERE  pa.cod_plano = plano_analitica.cod_plano
                                    AND  pa.exercicio = plano_analitica.exercicio
                             )   AS  vl_entradas

                          ,  (   SELECT  SUM(
                                                 (   SELECT  COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                                       FROM  contabilidade.conta_credito
                                                 INNER JOIN  contabilidade.valor_lancamento
                                                         ON  valor_lancamento.cod_lote = conta_credito.cod_lote
                                                        AND  valor_lancamento.tipo = conta_credito.tipo
                                                        AND  valor_lancamento.sequencia = conta_credito.sequencia
                                                        AND  valor_lancamento.exercicio = conta_credito.exercicio
                                                        AND  valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                                        AND  valor_lancamento.cod_entidade = conta_credito.cod_entidade
                                                 INNER JOIN  contabilidade.lancamento
                                                         ON  lancamento.sequencia = valor_lancamento.sequencia
                                                        AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                                        AND  lancamento.tipo = valor_lancamento.tipo
                                                        AND  lancamento.exercicio = valor_lancamento.exercicio
                                                        AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                                 INNER JOIN  contabilidade.lote
                                                         ON  lote.cod_lote = lancamento.cod_lote
                                                        AND  lote.exercicio = lancamento.exercicio
                                                        AND  lote.tipo = lancamento.tipo
                                                        AND  lote.cod_entidade = lancamento.cod_entidade
                                                        AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                                                        AND  lote.exercicio = '2012'
                                                        AND  lote.tipo != 'I'
                                                      WHERE  conta_credito.exercicio = pa.exercicio
                                                        AND  conta_credito.cod_plano = pa.cod_plano
                                                 )
                                         )  as vl_total
                                   FROM  contabilidade.plano_analitica AS pa
                                  WHERE  pa.cod_plano = plano_analitica.cod_plano
                                    AND  pa.exercicio = plano_analitica.exercicio
                             ) * -1  AS  vl_saidas




                          ,  (   SELECT  SUM(
                                                 (   SELECT  COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                                       FROM  contabilidade.conta_debito
                                                 INNER JOIN  contabilidade.valor_lancamento
                                                         ON  valor_lancamento.cod_lote = conta_debito.cod_lote
                                                        AND  valor_lancamento.tipo = conta_debito.tipo
                                                        AND  valor_lancamento.sequencia = conta_debito.sequencia
                                                        AND  valor_lancamento.exercicio = conta_debito.exercicio
                                                        AND  valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                                        AND  valor_lancamento.cod_entidade = conta_debito.cod_entidade
                                                 INNER JOIN  contabilidade.lancamento
                                                         ON  lancamento.sequencia = valor_lancamento.sequencia
                                                        AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                                        AND  lancamento.tipo = valor_lancamento.tipo
                                                        AND  lancamento.exercicio = valor_lancamento.exercicio
                                                        AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                                 INNER JOIN  contabilidade.lote
                                                         ON  lote.cod_lote = lancamento.cod_lote
                                                        AND  lote.exercicio = lancamento.exercicio
                                                        AND  lote.tipo = lancamento.tipo
                                                        AND  lote.cod_entidade = lancamento.cod_entidade
                                                         AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                                                         AND  lote.exercicio = '2012'
                                                         AND  lote.tipo = 'I'
                                                 WHERE  conta_debito.exercicio = pa.exercicio
                                                        AND  conta_debito.cod_plano = pa.cod_plano
                                                 )
                                                 +
                                                 (   SELECT  COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                                       FROM  contabilidade.conta_credito
                                                 INNER JOIN  contabilidade.valor_lancamento
                                                         ON  valor_lancamento.cod_lote = conta_credito.cod_lote
                                                        AND  valor_lancamento.tipo = conta_credito.tipo
                                                        AND  valor_lancamento.sequencia = conta_credito.sequencia
                                                        AND  valor_lancamento.exercicio = conta_credito.exercicio
                                                        AND  valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                                        AND  valor_lancamento.cod_entidade = conta_credito.cod_entidade
                                                 INNER JOIN  contabilidade.lancamento
                                                         ON  lancamento.sequencia = valor_lancamento.sequencia
                                                        AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                                        AND  lancamento.tipo = valor_lancamento.tipo
                                                        AND  lancamento.exercicio = valor_lancamento.exercicio
                                                        AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                                 INNER JOIN  contabilidade.lote
                                                         ON  lote.cod_lote = lancamento.cod_lote
                                                        AND  lote.exercicio = lancamento.exercicio
                                                        AND  lote.tipo = lancamento.tipo
                                                        AND  lote.cod_entidade = lancamento.cod_entidade
                                                         AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                                                         AND  lote.exercicio = '2012'
                                                         AND  lote.tipo = 'I'
                                                 WHERE  conta_credito.exercicio = pa.exercicio
                                                        AND  conta_credito.cod_plano = pa.cod_plano
                                                 )
                                         )  as vl_total
                                   FROM  contabilidade.plano_analitica AS pa
                                  WHERE  pa.cod_plano = plano_analitica.cod_plano
                                    AND  pa.exercicio = plano_analitica.exercicio
                             )   AS  saldo_inicial


                          ,  (   SELECT  SUM(
                                                 (   SELECT  COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                                       FROM  contabilidade.conta_debito
                                                 INNER JOIN  contabilidade.valor_lancamento
                                                         ON  valor_lancamento.cod_lote = conta_debito.cod_lote
                                                        AND  valor_lancamento.tipo = conta_debito.tipo
                                                        AND  valor_lancamento.sequencia = conta_debito.sequencia
                                                        AND  valor_lancamento.exercicio = conta_debito.exercicio
                                                        AND  valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                                        AND  valor_lancamento.cod_entidade = conta_debito.cod_entidade
                                                 INNER JOIN  contabilidade.lancamento
                                                         ON  lancamento.sequencia = valor_lancamento.sequencia
                                                        AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                                        AND  lancamento.tipo = valor_lancamento.tipo
                                                        AND  lancamento.exercicio = valor_lancamento.exercicio
                                                        AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                                 INNER JOIN  contabilidade.lote
                                                         ON  lote.cod_lote = lancamento.cod_lote
                                                        AND  lote.exercicio = lancamento.exercicio
                                                        AND  lote.tipo = lancamento.tipo
                                                        AND  lote.cod_entidade = lancamento.cod_entidade
                                                        AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND    TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                                                      WHERE  conta_debito.exercicio = pa.exercicio
                                                        AND  conta_debito.cod_plano = pa.cod_plano
                                                 )
                                                 +
                                                 (   SELECT  COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                                       FROM  contabilidade.conta_credito
                                                 INNER JOIN  contabilidade.valor_lancamento
                                                         ON  valor_lancamento.cod_lote = conta_credito.cod_lote
                                                        AND  valor_lancamento.tipo = conta_credito.tipo
                                                        AND  valor_lancamento.sequencia = conta_credito.sequencia
                                                        AND  valor_lancamento.exercicio = conta_credito.exercicio
                                                        AND  valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                                        AND  valor_lancamento.cod_entidade = conta_credito.cod_entidade
                                                 INNER JOIN  contabilidade.lancamento
                                                         ON  lancamento.sequencia = valor_lancamento.sequencia
                                                        AND  lancamento.cod_lote = valor_lancamento.cod_lote
                                                        AND  lancamento.tipo = valor_lancamento.tipo
                                                        AND  lancamento.exercicio = valor_lancamento.exercicio
                                                        AND  lancamento.cod_entidade = valor_lancamento.cod_entidade
                                                 INNER JOIN  contabilidade.lote
                                                         ON  lote.cod_lote = lancamento.cod_lote
                                                        AND  lote.exercicio = lancamento.exercicio
                                                        AND  lote.tipo = lancamento.tipo
                                                        AND  lote.cod_entidade = lancamento.cod_entidade
                                                        AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy') AND    TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                                                      WHERE  conta_credito.exercicio = pa.exercicio
                                                        AND  conta_credito.cod_plano = pa.cod_plano
                                                 )
                                           )
                                   FROM  contabilidade.plano_analitica AS pa
                                  WHERE  pa.cod_plano = plano_analitica.cod_plano
                                    AND  pa.exercicio = plano_analitica.exercicio
                             )   AS  saldo_final


                       FROM  tcmgo.orgao
                 INNER JOIN  tcmgo.orgao_plano_banco
                         ON  orgao_plano_banco.num_orgao = orgao.num_orgao
                        AND  orgao_plano_banco.exercicio = orgao.exercicio
                 INNER JOIN  contabilidade.plano_banco
                         ON  plano_banco.cod_plano = orgao_plano_banco.cod_plano
                        AND  plano_banco.exercicio = orgao_plano_banco.exercicio
                 INNER JOIN  contabilidade.plano_analitica
                         ON  plano_analitica.cod_plano = plano_banco.cod_plano
                        AND  plano_analitica.exercicio = plano_banco.exercicio
                 INNER JOIN  contabilidade.plano_conta
                         ON  plano_conta.cod_conta = plano_analitica.cod_conta
                        AND  plano_conta.exercicio = plano_analitica.exercicio
                 INNER JOIN  monetario.agencia
                         ON  agencia.cod_banco = plano_banco.cod_banco
                        AND  agencia.cod_agencia = plano_banco.cod_agencia
                 INNER JOIN  monetario.banco
                         ON  banco.cod_banco = plano_banco.cod_banco
                      $stFiltro
                   GROUP BY  orgao.num_orgao
                          ,  orgao.cod_tipo
                          ,  plano_analitica.cod_plano
                          ,  plano_analitica.exercicio
                          ,  plano_conta.cod_estrutural
                          ,  plano_conta.indicador_superavit
               ) as c
                  GROUP BY  c.tipo_registro
                         ,  c.num_orgao
                         ,  c.cod_tipo
                         ,  c.indicador_superavit
                         ,  c.cod_estrutural ";

        return $stSql;
    }
}
