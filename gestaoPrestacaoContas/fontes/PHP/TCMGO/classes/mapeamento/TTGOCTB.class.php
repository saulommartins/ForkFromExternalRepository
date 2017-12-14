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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62499 $
    $Name$
    $Author: lisiane $
    $Date: 2015-05-14 17:56:42 -0300 (Thu, 14 May 2015) $

    * Casos de uso: uc-06.04.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOCTB extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */

    public function recuperaContasBancarias(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancarias",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasBancarias()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
    SELECT  c.tipo_registro
        , c.num_orgao
        , '01' as num_unidade
        , c.num_conta_corrente
        , c.num_banco
        , c.num_agencia";
    if ($this->getDado('exercicio') > 2010 ) {
        $stSql .= ",c.digito ";
    }
    if ( $this->getDado('exercicio') < 2014 ) {
        $stSql .= "
            , SUM(c.vl_saidas) as vl_saidas
            , SUM(c.vl_entradas) as vl_entradas
        ";
    } else if ( $this->getDado('exercicio') >= 2014 && $this->getDado('inMesGeracao') == 1 ) {
        $stSql .= "
            , SUM(c.vl_saidas) + SUM(c.saldo_inicial) as vl_saidas
            , SUM(c.vl_entradas) + SUM(c.saldo_inicial) as vl_entradas
        ";
    } else {
        $stSql .= "
            , SUM(c.vl_saidas) as vl_saidas
            , SUM(c.vl_entradas) as vl_entradas
        ";
    }
    
    $stSql .= "	
        , SUM(c.saldo_inicial) as saldo_inicial
        , SUM(c.saldo_final) as saldo_final
        , c.tipo_conta";
    if ($this->getDado('exercicio') < 2011 ) {
    $stSql .= ",  c.nom_conta";
    }
    $stSql .= "	 FROM  (
               SELECT  '10'::int  AS  tipo_registro
                    ,  num_orgao";
    if ($this->getDado('exercicio') < 2011 ) {
        $stSql .="            ,  CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                            THEN '999999999999'
                            ELSE REPLACE(conta_corrente,'-','')
                       END AS num_conta_corrente
                    ,  CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                            THEN '999999'
                            ELSE REPLACE(agencia.num_agencia,'-','')
                       END AS num_agencia
                    ,  CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                            THEN '999'
                            ELSE banco.num_banco
                       END AS num_banco,
               '0'::int as digito";
    } elseif ($this->getDado('exercicio') < '2013') {
        $stSql .= "     ,case when (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                then '999999999999'
               else ltrim(split_part(conta_corrente,'-',1),'0')
             end   as num_conta_corrente
           ,case when (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
              then '999'
              else num_banco
            end
           ,case when (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
              then '999999'
               else ltrim(replace(num_agencia,'-',''),'0')
             end  as num_agencia
            , ltrim(split_part(conta_corrente,'-',2),'0') AS digito";
    } else {
       $stSql .= " , CASE WHEN ltrim(replace(num_agencia,'-',''),'9') = '' AND num_banco = '999' THEN
                        '999999999999'
                      ELSE
                        ltrim(split_part(conta_corrente,'-',1),'0')
                      END as num_conta_corrente
                ,num_banco
                ,ltrim(replace(num_agencia,'-',''),'0') as num_agencia
                , ltrim(split_part(conta_corrente,'-',2),'0') AS digito
                ";
    }
     $stSql .= "    ,  plano_conta.nom_conta
                    ,  plano_analitica.cod_plano
                    ,  plano_analitica.exercicio
                    ";
                    if ($this->getDado('exercicio') > '2012') {
                      $stSql .= "
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta ";
                    } else {
                      $stSql .= "
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                                '03'
                                           WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                              OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                              OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                                '02'
                                           ELSE
                                                '01'
                                      END as tipo_conta
                        ";
                    }
                    $stSql .= "
                    ,  '0'  AS  numero_sequencial

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
                                                  AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                  AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                                  AND  lote.tipo != 'I'
                                                WHERE  conta_debito.exercicio = pa.exercicio
                                                  AND  conta_debito.cod_plano = pa.cod_plano
                                           )
                                   )  - COALESCE(SUM(ordem_pagamento_retencao.vl_retencao),0.00) as vl_total
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
                                                  AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                  AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                                  AND  lote.tipo != 'I'
                                                WHERE  conta_credito.exercicio = pa.exercicio
                                                  AND  conta_credito.cod_plano = pa.cod_plano
                                           )
                                   )  + COALESCE(SUM(ordem_pagamento_retencao.vl_retencao),0.00) as vl_total
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
                                                  ";
                                    if ($inMes == '01') {
                                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                   AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                                   AND  lote.tipo = 'I'
                                        ";
                                    } else {
                                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                                    }
                                    $stSql.="   WHERE  conta_debito.exercicio = pa.exercicio
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
                                                  ";
                                    if ($inMes == '01') {
                                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                   AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                                   AND  lote.tipo = 'I'
                                        ";
                                    } else {
                                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                                    }
                                    $stSql.="   WHERE  conta_credito.exercicio = pa.exercicio
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
                                                  AND  lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND	TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
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
                                                  AND  lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND	TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                WHERE  conta_credito.exercicio = pa.exercicio
                                                  AND  conta_credito.cod_plano = pa.cod_plano
                                           )
                                     )
                             FROM  contabilidade.plano_analitica AS pa
                            WHERE  pa.cod_plano = plano_analitica.cod_plano
                              AND  pa.exercicio = plano_analitica.exercicio
                       )   AS  saldo_final


                 FROM  tcmgo.orgao_plano_banco
           INNER JOIN  contabilidade.plano_banco
                   ON  plano_banco.cod_plano = orgao_plano_banco.cod_plano
                  AND  plano_banco.exercicio = orgao_plano_banco.exercicio
           INNER JOIN  contabilidade.plano_analitica
                   ON  plano_analitica.cod_plano = plano_banco.cod_plano
                  AND  plano_analitica.exercicio = plano_banco.exercicio
       
            LEFT JOIN tesouraria.transferencia
                   ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                  AND transferencia.exercicio        = plano_analitica.exercicio
                  AND transferencia.cod_tipo IN (2)
       
            LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao
                   ON transferencia_ordem_pagamento_retencao.cod_lote     = transferencia.cod_lote
                  AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
                  AND transferencia_ordem_pagamento_retencao.exercicio    = transferencia.exercicio
                  AND transferencia_ordem_pagamento_retencao.tipo         = transferencia.tipo
       
            LEFT JOIN empenho.ordem_pagamento_retencao
                   ON ordem_pagamento_retencao.exercicio    = transferencia_ordem_pagamento_retencao.exercicio
                  AND ordem_pagamento_retencao.cod_entidade = transferencia_ordem_pagamento_retencao.cod_entidade
                  AND ordem_pagamento_retencao.cod_plano    = transferencia_ordem_pagamento_retencao.cod_plano
                  AND ordem_pagamento_retencao.cod_ordem    = transferencia_ordem_pagamento_retencao.cod_ordem
                  AND ordem_pagamento_retencao.sequencial   = transferencia_ordem_pagamento_retencao.sequencial
                  AND ordem_pagamento_retencao.cod_receita IS NULL
       
            LEFT JOIN contabilidade.lancamento_retencao
                   ON lancamento_retencao.exercicio_retencao = ordem_pagamento_retencao.exercicio
                  AND lancamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                  AND lancamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                  AND lancamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
                  AND lancamento_retencao.sequencial   = ordem_pagamento_retencao.sequencial
                  AND lancamento_retencao.tipo         = 'T'

           INNER JOIN  contabilidade.plano_conta
                   ON  plano_conta.cod_conta = plano_analitica.cod_conta
                  AND  plano_conta.exercicio = plano_analitica.exercicio
           INNER JOIN  monetario.agencia
                   ON  agencia.cod_banco = plano_banco.cod_banco
                  AND  agencia.cod_agencia = plano_banco.cod_agencia
           INNER JOIN  monetario.banco
                   ON  banco.cod_banco = plano_banco.cod_banco
                WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
                  AND  plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
             GROUP BY  num_orgao, conta_corrente, agencia.num_agencia, banco.num_banco, plano_conta.nom_conta,plano_analitica.cod_plano, plano_analitica.exercicio, plano_conta.cod_estrutural
         ) as c
            GROUP BY  c.tipo_registro
                , c.num_orgao
                       , c.num_conta_corrente
                 , c.num_banco
            , c.num_agencia";
    if ($this->getDado('exercicio') > 2010 ) {
           $stSql .= ",c.digito ";
    }
    $stSql .= "     , c.tipo_conta";
    if ($this->getDado('exercicio') < 2011 ) {
    $stSql .= ",  c.nom_conta";
    }

        return $stSql;
    }

    public function recuperaContasBancariasFonteRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancariasFonteRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaContasBancariasFonteRecurso()
    {
        $inMes = explode('/',$this->getDado('dtInicio'));
        $inMes = $inMes[1];
        $stSql = "
               SELECT  c.tipo_registro
      , c.num_orgao
      , '01' as num_unidade
      , c.num_conta_corrente
      , c.num_banco
      , c.num_agencia
      , c.digito
      , SUM(c.vl_saidas) as vl_saidas
      , SUM(c.vl_entradas) as vl_entradas
      , SUM(c.saldo_inicial) as saldo_inicial
      , SUM(c.saldo_final) as saldo_final
      , c.fonte
      , c.tipo_conta
  FROM  (
      SELECT  '11'::int  AS  tipo_registro
                ,  num_orgao ";
          if ($this->getDado('exercicio') > '2012') {
            $stSql .= "
                ,CASE WHEN split_part(conta_corrente,'-',1) LIKE  '99999%' THEN
                    '999999999999'
                ELSE
                    ltrim(split_part(conta_corrente,'-',1),'0')
                END AS num_conta_corrente
                ,num_banco
                ,ltrim(replace(num_agencia,'-',''),'0') as num_agencia
            ";
          } else {
            $stSql .= "
                ,case when (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                  then '999999999999'
                     else ltrim(split_part(conta_corrente,'-',1),'0')
                   end   as num_conta_corrente
                 ,case when (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                    then '999'
                    else num_banco
                  end
                 ,case when (substr(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                    then '999999'
                     else ltrim(replace(num_agencia,'-',''),'0')
                   end  as num_agencia ";
          }
          $stSql .= "
            , ltrim(split_part(conta_corrente,'-',2),'0') AS digito
                ,  plano_analitica.cod_plano
                ,  plano_analitica.exercicio ";
          if ($this->getDado('exercicio') > '2012') {
            $stSql .= "
                , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                          '03'
                     WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                          '02'
                     ELSE
                          '01'
                END as tipo_conta ";
          } else {
            $stSql .= "
                , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                      '03'
                                 WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                    OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                    OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                      '02'
                                 ELSE
                                      '01'
                            END as tipo_conta
              ";
          }
          $stSql .= "
                , recurso.cod_fonte as fonte
                ,  '0'  AS  numero_sequencial

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
                                              AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                              AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                              AND  lote.tipo != 'I'
                                            WHERE  conta_debito.exercicio = pa.exercicio
                                              AND  conta_debito.cod_plano = pa.cod_plano
                                       )
                               ) - COALESCE(SUM(ordem_pagamento_retencao.vl_retencao),0.00) as vl_total
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
                                              AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                              AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                              AND  lote.tipo != 'I'
                                            WHERE  conta_credito.exercicio = pa.exercicio
                                              AND  conta_credito.cod_plano = pa.cod_plano
                                       )
                               )  + COALESCE(SUM(ordem_pagamento_retencao.vl_retencao),0.00) as vl_total
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
                                              ";
                        if ($inMes == '01') {
                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                               AND  lote.exercicio = '".$this->getDado('exercicio')."'
                               AND  lote.tipo = 'I'
                        ";
                        } else {
                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                        }
                        $stSql.="   WHERE  conta_debito.exercicio = pa.exercicio
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
                              ";
                              if ($inMes == '01') {
                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                               AND  lote.exercicio = '".$this->getDado('exercicio')."'
                               AND  lote.tipo = 'I'
                        ";
                        } else {
                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                        }
                        $stSql.="   WHERE  conta_credito.exercicio = pa.exercicio
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
                                              AND  lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND	TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
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
                                              AND  lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND	TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                            WHERE  conta_credito.exercicio = pa.exercicio
                                              AND  conta_credito.cod_plano = pa.cod_plano
                                       )
                                 )
                         FROM  contabilidade.plano_analitica AS pa
                        WHERE  pa.cod_plano = plano_analitica.cod_plano
                          AND  pa.exercicio = plano_analitica.exercicio
                   )   AS  saldo_final


             FROM  tcmgo.orgao_plano_banco
           INNER JOIN  contabilidade.plano_banco
               ON  plano_banco.cod_plano = orgao_plano_banco.cod_plano
              AND  plano_banco.exercicio = orgao_plano_banco.exercicio
           INNER JOIN  contabilidade.plano_analitica
               ON  plano_analitica.cod_plano = plano_banco.cod_plano
              AND  plano_analitica.exercicio = plano_banco.exercicio
       
            LEFT JOIN tesouraria.transferencia
                   ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                  AND transferencia.exercicio        = plano_analitica.exercicio
                  AND transferencia.cod_tipo IN (2)
       
            LEFT JOIN tesouraria.transferencia_ordem_pagamento_retencao
                   ON transferencia_ordem_pagamento_retencao.cod_lote     = transferencia.cod_lote
                  AND transferencia_ordem_pagamento_retencao.cod_entidade = transferencia.cod_entidade
                  AND transferencia_ordem_pagamento_retencao.exercicio    = transferencia.exercicio
                  AND transferencia_ordem_pagamento_retencao.tipo         = transferencia.tipo
       
            LEFT JOIN empenho.ordem_pagamento_retencao
                   ON ordem_pagamento_retencao.exercicio    = transferencia_ordem_pagamento_retencao.exercicio
                  AND ordem_pagamento_retencao.cod_entidade = transferencia_ordem_pagamento_retencao.cod_entidade
                  AND ordem_pagamento_retencao.cod_plano    = transferencia_ordem_pagamento_retencao.cod_plano
                  AND ordem_pagamento_retencao.cod_ordem    = transferencia_ordem_pagamento_retencao.cod_ordem
                  AND ordem_pagamento_retencao.sequencial   = transferencia_ordem_pagamento_retencao.sequencial
                  AND ordem_pagamento_retencao.cod_receita IS NULL
       
            LEFT JOIN contabilidade.lancamento_retencao
                   ON lancamento_retencao.exercicio_retencao = ordem_pagamento_retencao.exercicio
                  AND lancamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                  AND lancamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                  AND lancamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
                  AND lancamento_retencao.sequencial   = ordem_pagamento_retencao.sequencial
                  AND lancamento_retencao.tipo         = 'T'
       
           INNER JOIN  contabilidade.plano_recurso
               ON  plano_analitica.cod_plano = plano_recurso.cod_plano
              AND  plano_analitica.exercicio = plano_recurso.exercicio
           INNER JOIN  orcamento.recurso
               ON  recurso.cod_recurso = plano_recurso.cod_recurso
              AND  recurso.exercicio   = plano_recurso.exercicio
           INNER JOIN  contabilidade.plano_conta
               ON  plano_conta.cod_conta = plano_analitica.cod_conta
              AND  plano_conta.exercicio = plano_analitica.exercicio
           INNER JOIN  monetario.agencia
               ON  agencia.cod_banco = plano_banco.cod_banco
              AND  agencia.cod_agencia = plano_banco.cod_agencia
           INNER JOIN  monetario.banco
               ON  banco.cod_banco = plano_banco.cod_banco
            WHERE  plano_banco.exercicio = '".$this->getDado('exercicio')."'
              AND  plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
             GROUP BY  num_orgao
                ,  conta_corrente
                ,  agencia.num_agencia
                ,  banco.num_banco
                ,plano_analitica.cod_plano
                ,plano_conta.cod_estrutural
                ,plano_analitica.exercicio
                ,recurso.cod_fonte) as c
            GROUP BY c.tipo_registro
                ,c.num_orgao
                       , c.num_conta_corrente
                 , c.num_banco
            ,c.num_agencia
            , c.digito
                ,c.fonte
            ,c.tipo_conta
    ";

        return $stSql;
    }

    public function recuperaTotalConta($stFiltro = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotalConta($stFiltro) ;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $rsRecordSet->getCampo( 'total' );
    }

    public function montaRecuperaTotalConta($stFiltro)
    {
        $stSQL = "
                    select
                         --total de credito
                    ROUND(coalesce (
                       ( select sum ( ROUND(valor_lancamento.vl_lancamento,2) ) as vl_total
                           from contabilidade.plano_conta
                           join contabilidade.plano_analitica
                             on ( plano_conta.exercicio = plano_analitica.exercicio
                            and   plano_conta.cod_conta = plano_analitica.cod_conta )
                     INNER JOIN contabilidade.plano_banco
                             ON plano_banco.cod_plano = plano_analitica.cod_plano
                            AND plano_banco.exercicio = plano_analitica.exercicio
                           join contabilidade.conta_credito
                             on ( plano_analitica.exercicio = conta_credito.exercicio
                            and   plano_analitica.cod_plano = conta_credito.cod_plano )
                           join contabilidade.valor_lancamento
                             on ( conta_credito.exercicio    = valor_lancamento.exercicio
                            and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                            and   conta_credito.tipo         = valor_lancamento.tipo
                            and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                            and   conta_credito.sequencia    = valor_lancamento.sequencia
                            and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                           join contabilidade.lote
                             on ( valor_lancamento.exercicio   = lote.exercicio
                            and   valor_lancamento.cod_entidade= lote.cod_entidade
                            and   valor_lancamento.tipo        = lote.tipo
                            and   valor_lancamento.cod_lote    = lote.cod_lote )
                           $stFiltro
                        ), 0 )
                       +
                        --total de debitos
                    coalesce (
                      ( select sum ( ROUND(valor_lancamento.vl_lancamento,2) ) as vl_total
                          from contabilidade.plano_conta
                          join contabilidade.plano_analitica
                            on ( plano_conta.exercicio = plano_analitica.exercicio
                           and   plano_conta.cod_conta = plano_analitica.cod_conta )
                    INNER JOIN contabilidade.plano_banco
                            ON plano_banco.cod_plano = plano_analitica.cod_plano
                           AND plano_banco.exercicio = plano_analitica.exercicio
                          join contabilidade.conta_debito
                            on ( plano_analitica.exercicio = conta_debito.exercicio
                           and   plano_analitica.cod_plano = conta_debito.cod_plano )
                          join contabilidade.valor_lancamento
                            on ( conta_debito.exercicio    = valor_lancamento.exercicio
                           and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                           and   conta_debito.tipo         = valor_lancamento.tipo
                           and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                           and   conta_debito.sequencia    = valor_lancamento.sequencia
                           and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                          join contabilidade.lote
                            on ( valor_lancamento.exercicio   = lote.exercicio
                           and   valor_lancamento.cod_entidade= lote.cod_entidade
                           and   valor_lancamento.tipo        = lote.tipo
                           and   valor_lancamento.cod_lote    = lote.cod_lote )
                          $stFiltro
                        ) , 0 ),2)   as total
                 ";

        return $stSQL;
    }

    public function recuperaOrgao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaOrgao().$stFiltro;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaOrgao()
    {
        $stSql = " SELECT exercicio
                        , cod_plano
                        , num_orgao
                    FROM tcmgo.orgao_plano_banco
        ";

        return $stSql;
    }

    public function recuperaTotalContaPorRecurso(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotalContaPorRecurso($stFiltro);
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTotalContaPorRecurso($stFiltro)
    {
        $stSql = "SELECT (SUM(tabela.vl_creditos) + SUM(tabela.vl_debitos)) as vl_total
     , tabela.cod_recurso
  FROM
                         --total de credito
                       ( select sum ( ROUND(valor_lancamento.vl_lancamento,2) ) as vl_creditos
                              , 0.00 AS vl_debitos
                              , recurso.cod_recurso
                           from contabilidade.plano_conta
                     inner join contabilidade.plano_analitica
                             on ( plano_conta.exercicio = plano_analitica.exercicio
                            and   plano_conta.cod_conta = plano_analitica.cod_conta )
                     INNER JOIN contabilidade.plano_banco
                             ON plano_banco.cod_plano = plano_analitica.cod_plano
                            AND plano_banco.exercicio = plano_analitica.exercicio
                     inner join contabilidade.conta_credito
                             on ( plano_analitica.exercicio = conta_credito.exercicio
                            and   plano_analitica.cod_plano = conta_credito.cod_plano )
                     inner join contabilidade.valor_lancamento
                             on ( conta_credito.exercicio    = valor_lancamento.exercicio
                            and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                            and   conta_credito.tipo         = valor_lancamento.tipo
                            and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                            and   conta_credito.sequencia    = valor_lancamento.sequencia
                            and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                     inner join contabilidade.lote
                             on ( valor_lancamento.exercicio   = lote.exercicio
                            and   valor_lancamento.cod_entidade= lote.cod_entidade
                            and   valor_lancamento.tipo        = lote.tipo
                            and   valor_lancamento.cod_lote    = lote.cod_lote )
                     INNER JOIN  contabilidade.plano_recurso
                             ON  plano_analitica.cod_plano = plano_recurso.cod_plano
                            AND  plano_analitica.exercicio = plano_recurso.exercicio
                     INNER JOIN  orcamento.recurso
                             ON  recurso.cod_recurso = plano_recurso.cod_recurso
                            AND  recurso.exercicio   = plano_recurso.exercicio
                            $stFiltro
                       GROUP BY  recurso.cod_recurso

                UNION ALL
                        --total de debitos
                        select sum ( ROUND(valor_lancamento.vl_lancamento,2) ) as vl_debitos
                             , 0.00 AS vl_creditos
                             , recurso.cod_recurso
                          from contabilidade.plano_conta
                    inner join contabilidade.plano_analitica
                            on ( plano_conta.exercicio = plano_analitica.exercicio
                           and   plano_conta.cod_conta = plano_analitica.cod_conta )
                    INNER JOIN contabilidade.plano_banco
                            ON plano_banco.cod_plano = plano_analitica.cod_plano
                           AND plano_banco.exercicio = plano_analitica.exercicio
                    inner join contabilidade.conta_debito
                            on ( plano_analitica.exercicio = conta_debito.exercicio
                           and   plano_analitica.cod_plano = conta_debito.cod_plano )
                    inner join contabilidade.valor_lancamento
                            on ( conta_debito.exercicio    = valor_lancamento.exercicio
                           and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                           and   conta_debito.tipo         = valor_lancamento.tipo
                           and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                           and   conta_debito.sequencia    = valor_lancamento.sequencia
                           and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                    inner join contabilidade.lote
                            on ( valor_lancamento.exercicio   = lote.exercicio
                           and   valor_lancamento.cod_entidade= lote.cod_entidade
                           and   valor_lancamento.tipo        = lote.tipo
                           and   valor_lancamento.cod_lote    = lote.cod_lote )
                    INNER JOIN  contabilidade.plano_recurso
                            ON  plano_analitica.cod_plano = plano_recurso.cod_plano
                           AND  plano_analitica.exercicio = plano_recurso.exercicio
                    INNER JOIN  orcamento.recurso
                            ON  recurso.cod_recurso = plano_recurso.cod_recurso
                           AND  recurso.exercicio   = plano_recurso.exercicio
                           $stFiltro
                      GROUP BY  recurso.cod_recurso
                        ) as tabela
                       GROUP BY tabela.cod_recurso


        ";

        return $stSql;
    }

    public function recuperaRecurso(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if (empty($stFiltro)) {
          $stFiltro = "
            WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'
              AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
              AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
          ";
        }
        $stSql = $this->montaRecuperaRecurso($stFiltro);
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRecurso($stFiltro)
    {
      $stSql = "         SELECT recurso.cod_recurso
                              , orgao_plano_banco.num_orgao
                              , '01' as num_unidade
                           from contabilidade.plano_conta
                     inner join contabilidade.plano_analitica
                             on ( plano_conta.exercicio = plano_analitica.exercicio
                            and   plano_conta.cod_conta = plano_analitica.cod_conta )
                     INNER JOIN contabilidade.plano_banco
                             ON plano_banco.cod_plano = plano_analitica.cod_plano
                            AND plano_banco.exercicio = plano_analitica.exercicio
                     inner join contabilidade.conta_debito as conta_credito
                             on ( plano_analitica.exercicio = conta_credito.exercicio
                            and   plano_analitica.cod_plano = conta_credito.cod_plano )
                     inner join contabilidade.valor_lancamento
                             on ( conta_credito.exercicio    = valor_lancamento.exercicio
                            and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                            and   conta_credito.tipo         = valor_lancamento.tipo
                            and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                            and   conta_credito.sequencia    = valor_lancamento.sequencia
                            and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                     inner join contabilidade.lote
                             on ( valor_lancamento.exercicio   = lote.exercicio
                            and   valor_lancamento.cod_entidade= lote.cod_entidade
                            and   valor_lancamento.tipo        = lote.tipo
                            and   valor_lancamento.cod_lote    = lote.cod_lote )
                     INNER JOIN  contabilidade.plano_recurso
                             ON  plano_analitica.cod_plano = plano_recurso.cod_plano
                            AND  plano_analitica.exercicio = plano_recurso.exercicio
                     INNER JOIN  orcamento.recurso
                             ON  recurso.cod_recurso = plano_recurso.cod_recurso
                            AND  recurso.exercicio   = plano_recurso.exercicio
                     INNER JOIN  tcmgo.orgao_plano_banco
                             ON  plano_banco.cod_plano = orgao_plano_banco.cod_plano
                            AND  plano_banco.exercicio = orgao_plano_banco.exercicio
                            $stFiltro
                       GROUP BY  recurso.cod_recurso
                              ,  orgao_plano_banco.num_orgao
                       ORDER BY  recurso.cod_recurso";

      return $stSql;
    }
    
    public function recuperaContasBancarias2014(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancarias2014",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaContasBancarias2014()
    {
        $stSql = "
        SELECT 10 AS tipo_registro
             , num_orgao
             , num_unidade
             , num_banco
             , num_agencia
             , num_conta_corrente
             , digito
             , tipo_conta
             , SUM(saldo_inicial) AS saldo_inicial
             , (SUM(vl_entradas) - SUM(vl_retencao)) AS vl_entradas
             , (SUM(vl_saidas) - SUM(vl_retencao)) AS vl_saidas
             , SUM(saldo_final) AS saldo_final
          FROM (
        
               SELECT CASE WHEN orgao_plano_banco.num_orgao IS NOT NULL
                           THEN orgao_plano_banco.num_orgao
                           ELSE '00'
                       END AS num_orgao
                    , CASE WHEN orgao_plano_banco.num_orgao IS NOT NULL
                           THEN '01'
                           ELSE '00'
                       END AS num_unidade
                    , banco.num_banco
                    , LTRIM(REPLACE(agencia.num_agencia,'-',''),'0') AS num_agencia
                    , CASE WHEN LTRIM(REPLACE(agencia.num_agencia,'-',''),'9') = '' AND banco.num_banco = '999'
                           THEN '999999999999'
                           ELSE LTRIM(SPLIT_PART(conta_corrente.num_conta_corrente,'-',1),'0')
                       END AS num_conta_corrente
                    , LTRIM(SPLIT_PART(conta_corrente.num_conta_corrente,'-',2),'0') AS digito
                    , CASE WHEN (SUBSTR(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                           THEN '03'
                           WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                           THEN '02'
                           ELSE '01'
                       END AS tipo_conta 
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_debito.cod_lote
                                      AND valor_lancamento.tipo = conta_debito.tipo
                                      AND valor_lancamento.sequencia = conta_debito.sequencia
                                      AND valor_lancamento.exercicio = conta_debito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade   ";
                        if ($this->getDado('inMesGeracao') == '01') {
                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                               AND  lote.exercicio = '".$this->getDado('exercicio')."'
                               AND  lote.tipo = 'I'
                        ";
                        } else {
                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                        }
                        $stSql.=" WHERE conta_debito.exercicio = pa.exercicio
                                      AND conta_debito.cod_plano = pa.cod_plano
                                  )
                                  +
                                  (SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_credito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_credito.cod_lote
                                      AND valor_lancamento.tipo = conta_credito.tipo
                                      AND valor_lancamento.sequencia = conta_credito.sequencia
                                      AND valor_lancamento.exercicio = conta_credito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                               ";
                         if ($this->getDado('inMesGeracao') == '01') {
                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                               AND  lote.exercicio = '".$this->getDado('exercicio')."'
                               AND  lote.tipo = 'I'
                        ";
                        } else {
                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                        }
                        $stSql.=" WHERE conta_credito.exercicio = pa.exercicio
                                      AND conta_credito.cod_plano = pa.cod_plano
                                  )) AS vl_total
                         FROM contabilidade.plano_analitica AS pa
                        WHERE pa.cod_plano = plano_analitica.cod_plano
                          AND pa.exercicio = plano_analitica.exercicio
                      ) AS saldo_inicial
        
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_debito.cod_lote
                                      AND valor_lancamento.tipo = conta_debito.tipo
                                      AND valor_lancamento.sequencia = conta_debito.sequencia
                                      AND valor_lancamento.exercicio = conta_debito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                      AND lote.exercicio = '".$this->getDado('exercicio')."'
                                      AND lote.tipo != 'I'
                                    WHERE conta_debito.exercicio = pa.exercicio
                                      AND conta_debito.cod_plano = pa.cod_plano
                              )) AS vl_total
                         FROM contabilidade.plano_analitica AS pa
                        WHERE pa.cod_plano = plano_analitica.cod_plano
                          AND pa.exercicio = plano_analitica.exercicio
                      ) AS vl_entradas
                    
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_credito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_credito.cod_lote
                                      AND valor_lancamento.tipo = conta_credito.tipo
                                      AND valor_lancamento.sequencia = conta_credito.sequencia
                                      AND valor_lancamento.exercicio = conta_credito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                      AND lote.exercicio = '".$this->getDado('exercicio')."'
                                      AND lote.tipo != 'I'
                                    WHERE conta_credito.exercicio = pa.exercicio
                                      AND conta_credito.cod_plano = pa.cod_plano
                              )) AS vl_total
                         FROM contabilidade.plano_analitica AS pa
                        WHERE pa.cod_plano = plano_analitica.cod_plano
                          AND pa.exercicio = plano_analitica.exercicio
                      ) * -1 AS vl_saidas
        
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_debito.cod_lote
                                      AND valor_lancamento.tipo = conta_debito.tipo
                                      AND valor_lancamento.sequencia = conta_debito.sequencia
                                      AND valor_lancamento.exercicio = conta_debito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                    WHERE conta_debito.exercicio = pa.exercicio
                                      AND conta_debito.cod_plano = pa.cod_plano
                                  )
                                  +
                                  (SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_credito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_credito.cod_lote
                                      AND valor_lancamento.tipo = conta_credito.tipo
                                      AND valor_lancamento.sequencia = conta_credito.sequencia
                                      AND valor_lancamento.exercicio = conta_credito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                    WHERE conta_credito.exercicio = pa.exercicio
                                      AND conta_credito.cod_plano = pa.cod_plano
                                  ))
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                      ) AS saldo_final
                    , CASE WHEN banco.num_banco = '999' AND agencia.num_agencia = '999999' THEN
                      (SELECT SUM(ordem_pagamento_retencao.vl_retencao)
                         FROM empenho.ordem_pagamento_retencao
                   INNER JOIN (SELECT plano_analitica.cod_plano
                                    , plano_analitica.exercicio
                                    , plano_conta.nom_conta
                                 FROM contabilidade.plano_analitica 
                           INNER JOIN contabilidade.plano_conta 
                                   ON plano_analitica.cod_conta = plano_conta.cod_conta
                                  AND plano_analitica.exercicio = plano_conta.exercicio
                            LEFT JOIN tesouraria.transferencia
                                   ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                                  AND transferencia.exercicio        = plano_analitica.exercicio
                                  AND transferencia.cod_tipo IN (2)
                              ) AS receita_orcamentaria
                           ON receita_orcamentaria.exercicio = ordem_pagamento_retencao.exercicio
                          AND receita_orcamentaria.cod_plano = ordem_pagamento_retencao.cod_plano
                   INNER JOIN empenho.ordem_pagamento
                           ON ordem_pagamento.cod_ordem    = ordem_pagamento_retencao.cod_ordem    
                          AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio    
                          AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                   INNER JOIN empenho.pagamento_liquidacao
                           ON pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem    
                          AND pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio    
                          AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                   INNER JOIN empenho.nota_liquidacao
                           ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                          AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                          AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota
                   INNER JOIN empenho.empenho
                           ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                          AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                          AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   INNER JOIN empenho.pre_empenho
                           ON pre_empenho.exercicio       = empenho.exercicio
                          AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                   INNER JOIN contabilidade.lancamento_retencao
                           ON lancamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                          AND lancamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                          AND lancamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
                          AND lancamento_retencao.exercicio    = ordem_pagamento_retencao.exercicio
                          AND lancamento_retencao.sequencial   = ordem_pagamento_retencao.sequencial
                   INNER JOIN contabilidade.lancamento
                           ON lancamento.exercicio = lancamento_retencao.exercicio
                          AND lancamento.cod_entidade = lancamento_retencao.cod_entidade
                          AND lancamento.tipo = lancamento_retencao.tipo
                          AND lancamento.cod_lote = lancamento_retencao.cod_lote
                          AND lancamento.sequencia = lancamento_retencao.sequencia
                   INNER JOIN contabilidade.lote
                           ON lote.exercicio = lancamento.exercicio
                          AND lote.cod_entidade = lancamento.cod_entidade
                          AND lote.tipo = lancamento.tipo
                          AND lote.cod_lote = lancamento.cod_lote
                   INNER JOIN tesouraria.transferencia_ordem_pagamento_retencao
                           ON transferencia_ordem_pagamento_retencao.exercicio = ordem_pagamento_retencao.exercicio
                          AND transferencia_ordem_pagamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                          AND transferencia_ordem_pagamento_retencao.cod_ordem = ordem_pagamento_retencao.cod_ordem
                          AND transferencia_ordem_pagamento_retencao.cod_plano = ordem_pagamento_retencao.cod_plano
                          AND transferencia_ordem_pagamento_retencao.sequencial = ordem_pagamento_retencao.sequencial
                    LEFT JOIN (SELECT receita.cod_receita                                     
                                    , receita.cod_entidade                              
                                    , conta_receita.descricao                                
                                    , receita.exercicio                                      
                                 FROM orcamento.receita                                     
                            LEFT JOIN orcamento.conta_receita                          
                                   ON conta_receita.exercicio = receita.exercicio      
                                  AND conta_receita.cod_conta = receita.cod_conta      
                              ) as receita                                                   
                           ON receita.cod_receita = ordem_pagamento_retencao.cod_receita   
                          AND receita.exercicio = ordem_pagamento_retencao.exercicio       
                        WHERE lote.dt_lote BETWEEN to_date('".$this->getDado('dtInicio')."', 'dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."', 'dd/mm/yyyy')
                          AND ordem_pagamento_retencao.exercicio = '".$this->getDado('exercicio')."'
                          AND ordem_pagamento_retencao.cod_entidade IN (".$this->getDado('cod_entidade').")
                          AND lancamento_retencao.tipo = 'T'
                          AND receita.cod_receita IS NULL 
                          AND EXISTS (SELECT 1 
                                        FROM empenho.nota_liquidacao_paga
                                  INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                          ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota = nota_liquidacao_paga.cod_nota
                                         AND pagamento_liquidacao_nota_liquidacao_paga.timestamp = nota_liquidacao_paga.timestamp
                                       WHERE nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                                         AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                                         AND nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                                       LIMIT 1)
                      ) ELSE 0.00
                       END AS vl_retencao
                 FROM contabilidade.plano_banco
        
           INNER JOIN contabilidade.plano_analitica
                   ON plano_analitica.cod_plano = plano_banco.cod_plano
                  AND plano_analitica.exercicio = plano_banco.exercicio
        
            LEFT JOIN tcmgo.orgao_plano_banco
                   ON orgao_plano_banco.exercicio = plano_banco.exercicio
                  AND orgao_plano_banco.cod_plano = plano_banco.cod_plano
        
           INNER JOIN contabilidade.plano_conta
                   ON plano_conta.cod_conta = plano_analitica.cod_conta
                  AND plano_conta.exercicio = plano_analitica.exercicio
        
           INNER JOIN monetario.conta_corrente
                   ON conta_corrente.cod_banco          = plano_banco.cod_banco
                  AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                  AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                  
           INNER JOIN monetario.agencia
                   ON agencia.cod_banco   = conta_corrente.cod_banco
                  AND agencia.cod_agencia = conta_corrente.cod_agencia
           INNER JOIN monetario.banco
                   ON banco.cod_banco = plano_banco.cod_banco
         
                WHERE plano_banco.exercicio = '".$this->getDado('exercicio')."'
                  AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
        
             GROUP BY orgao_plano_banco.num_orgao
                    , num_unidade
                    , banco.num_banco
                    , agencia.num_agencia
                    , conta_corrente.num_conta_corrente
                    , digito
                    , tipo_conta
                    , plano_analitica.cod_plano
                    , plano_analitica.exercicio
        ) AS ctb10
        GROUP BY num_orgao
             , num_unidade
             , num_banco
             , num_agencia
             , num_conta_corrente
             , digito
             , tipo_conta
        HAVING SUM(saldo_inicial) + (SUM(vl_entradas) - SUM(vl_retencao)) + (SUM(vl_saidas) - SUM(vl_retencao)) + SUM(saldo_final) <> 0.00
        ";
        return $stSql;
    }

    public function recuperaContasBancariasFonteRecurso2014(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaContasBancariasFonteRecurso2014",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaContasBancariasFonteRecurso2014()
    {
        $stSql = "
        SELECT 11 AS tipo_registro
             , num_orgao
             , num_unidade
             , num_banco
             , num_agencia
             , num_conta_corrente
             , digito
             , tipo_conta
             , fonte
             , SUM(saldo_inicial) AS saldo_inicial
             , (SUM(vl_entradas) - SUM(vl_retencao)) AS vl_entradas
             , (SUM(vl_saidas) - SUM(vl_retencao)) AS vl_saidas
             , SUM(saldo_final) AS saldo_final
          FROM (
        
               SELECT CASE WHEN orgao_plano_banco.num_orgao IS NOT NULL
                           THEN orgao_plano_banco.num_orgao
                           ELSE '00'
                       END AS num_orgao
                    , CASE WHEN orgao_plano_banco.num_orgao IS NOT NULL
                           THEN '01'
                           ELSE '00'
                       END AS num_unidade
                    , banco.num_banco
                    , LTRIM(REPLACE(agencia.num_agencia,'-',''),'0') AS num_agencia
                    , CASE WHEN LTRIM(REPLACE(agencia.num_agencia,'-',''),'9') = '' AND banco.num_banco = '999'
                           THEN '999999999999'
                           ELSE LTRIM(SPLIT_PART(conta_corrente.num_conta_corrente,'-',1),'0')
                       END AS num_conta_corrente
                    , LTRIM(SPLIT_PART(conta_corrente.num_conta_corrente,'-',2),'0') AS digito
                    , CASE WHEN (SUBSTR(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                           THEN '03'
                           WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                           THEN '02'
                           ELSE '01'
                       END AS tipo_conta
                    , recurso.cod_recurso AS fonte
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_debito.cod_lote
                                      AND valor_lancamento.tipo = conta_debito.tipo
                                      AND valor_lancamento.sequencia = conta_debito.sequencia
                                      AND valor_lancamento.exercicio = conta_debito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade 
                                ";
                         if ($this->getDado('inMesGeracao') == '01') {
                           $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                      AND  lote.exercicio = '".$this->getDado('exercicio')."'
                                      AND  lote.tipo = 'I'
                        ";
                        } else {
                           $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                        }
                          $stSql.=" WHERE conta_debito.exercicio = pa.exercicio
                                      AND conta_debito.cod_plano = pa.cod_plano
                                  )
                                  +
                                  (SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_credito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_credito.cod_lote
                                      AND valor_lancamento.tipo = conta_credito.tipo
                                      AND valor_lancamento.sequencia = conta_credito.sequencia
                                      AND valor_lancamento.exercicio = conta_credito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                            ";
                         if ($this->getDado('inMesGeracao') == '01') {
                        $stSql.= " AND  lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                   AND  lote.tipo = 'I'
                                   AND  lote.exercicio = '".$this->getDado('exercicio')."'
                            ";
                        } else {
                        $stSql.= " AND  lote.dt_lote < TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') ";
                        }
                       $stSql.=" WHERE conta_credito.exercicio = pa.exercicio
                                   AND conta_credito.cod_plano = pa.cod_plano
                                  )) AS vl_total
                         FROM contabilidade.plano_analitica AS pa
                        WHERE pa.cod_plano = plano_analitica.cod_plano
                          AND pa.exercicio = plano_analitica.exercicio
                      ) AS saldo_inicial
        
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_debito.cod_lote
                                      AND valor_lancamento.tipo = conta_debito.tipo
                                      AND valor_lancamento.sequencia = conta_debito.sequencia
                                      AND valor_lancamento.exercicio = conta_debito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                      AND lote.exercicio = '".$this->getDado('exercicio')."'
                                      AND lote.tipo != 'I'
                                    WHERE conta_debito.exercicio = pa.exercicio
                                      AND conta_debito.cod_plano = pa.cod_plano
                              )) AS vl_total
                         FROM contabilidade.plano_analitica AS pa
                        WHERE pa.cod_plano = plano_analitica.cod_plano
                          AND pa.exercicio = plano_analitica.exercicio
                      ) AS vl_entradas
                    
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_credito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_credito.cod_lote
                                      AND valor_lancamento.tipo = conta_credito.tipo
                                      AND valor_lancamento.sequencia = conta_credito.sequencia
                                      AND valor_lancamento.exercicio = conta_credito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                      AND lote.exercicio = '".$this->getDado('exercicio')."'
                                      AND lote.tipo != 'I'
                                    WHERE conta_credito.exercicio = pa.exercicio
                                      AND conta_credito.cod_plano = pa.cod_plano
                              )) AS vl_total
                         FROM contabilidade.plano_analitica AS pa
                        WHERE pa.cod_plano = plano_analitica.cod_plano
                          AND pa.exercicio = plano_analitica.exercicio
                      ) * -1 AS vl_saidas
        
                    , (SELECT SUM((SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_debito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_debito.cod_lote
                                      AND valor_lancamento.tipo = conta_debito.tipo
                                      AND valor_lancamento.sequencia = conta_debito.sequencia
                                      AND valor_lancamento.exercicio = conta_debito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_debito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_debito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                    WHERE conta_debito.exercicio = pa.exercicio
                                      AND conta_debito.cod_plano = pa.cod_plano
                                  )
                                  +
                                  (SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                     FROM contabilidade.conta_credito
                               INNER JOIN contabilidade.valor_lancamento
                                       ON valor_lancamento.cod_lote = conta_credito.cod_lote
                                      AND valor_lancamento.tipo = conta_credito.tipo
                                      AND valor_lancamento.sequencia = conta_credito.sequencia
                                      AND valor_lancamento.exercicio = conta_credito.exercicio
                                      AND valor_lancamento.tipo_valor = conta_credito.tipo_valor
                                      AND valor_lancamento.cod_entidade = conta_credito.cod_entidade
                               INNER JOIN contabilidade.lancamento
                                       ON lancamento.sequencia = valor_lancamento.sequencia
                                      AND lancamento.cod_lote = valor_lancamento.cod_lote
                                      AND lancamento.tipo = valor_lancamento.tipo
                                      AND lancamento.exercicio = valor_lancamento.exercicio
                                      AND lancamento.cod_entidade = valor_lancamento.cod_entidade
                               INNER JOIN contabilidade.lote
                                       ON lote.cod_lote = lancamento.cod_lote
                                      AND lote.exercicio = lancamento.exercicio
                                      AND lote.tipo = lancamento.tipo
                                      AND lote.cod_entidade = lancamento.cod_entidade
                                      AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy')
                                                           AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                    WHERE conta_credito.exercicio = pa.exercicio
                                      AND conta_credito.cod_plano = pa.cod_plano
                                  ))
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                      ) AS saldo_final
                    , CASE WHEN banco.num_banco = '999' AND agencia.num_agencia = '999999' THEN
                      (SELECT SUM(ordem_pagamento_retencao.vl_retencao)
                         FROM empenho.ordem_pagamento_retencao
                   INNER JOIN (SELECT plano_analitica.cod_plano
                                    , plano_analitica.exercicio
                                    , plano_conta.nom_conta
                                 FROM contabilidade.plano_analitica 
                           INNER JOIN contabilidade.plano_conta 
                                   ON plano_analitica.cod_conta = plano_conta.cod_conta
                                  AND plano_analitica.exercicio = plano_conta.exercicio
                            LEFT JOIN tesouraria.transferencia
                                   ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                                  AND transferencia.exercicio        = plano_analitica.exercicio
                                  AND transferencia.cod_tipo IN (2)
                              ) AS receita_orcamentaria
                           ON receita_orcamentaria.exercicio = ordem_pagamento_retencao.exercicio
                          AND receita_orcamentaria.cod_plano = ordem_pagamento_retencao.cod_plano
                   INNER JOIN empenho.ordem_pagamento
                           ON ordem_pagamento.cod_ordem    = ordem_pagamento_retencao.cod_ordem    
                          AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio    
                          AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
                   INNER JOIN empenho.pagamento_liquidacao
                           ON pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem    
                          AND pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio    
                          AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                   INNER JOIN empenho.nota_liquidacao
                           ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                          AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                          AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota
                   INNER JOIN empenho.empenho
                           ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                          AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
                          AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                   INNER JOIN empenho.pre_empenho
                           ON pre_empenho.exercicio       = empenho.exercicio
                          AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                   INNER JOIN contabilidade.lancamento_retencao
                           ON lancamento_retencao.cod_ordem    = ordem_pagamento_retencao.cod_ordem
                          AND lancamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                          AND lancamento_retencao.cod_plano    = ordem_pagamento_retencao.cod_plano
                          AND lancamento_retencao.exercicio    = ordem_pagamento_retencao.exercicio
                          AND lancamento_retencao.sequencial   = ordem_pagamento_retencao.sequencial
                   INNER JOIN contabilidade.lancamento
                           ON lancamento.exercicio = lancamento_retencao.exercicio
                          AND lancamento.cod_entidade = lancamento_retencao.cod_entidade
                          AND lancamento.tipo = lancamento_retencao.tipo
                          AND lancamento.cod_lote = lancamento_retencao.cod_lote
                          AND lancamento.sequencia = lancamento_retencao.sequencia
                   INNER JOIN contabilidade.lote
                           ON lote.exercicio = lancamento.exercicio
                          AND lote.cod_entidade = lancamento.cod_entidade
                          AND lote.tipo = lancamento.tipo
                          AND lote.cod_lote = lancamento.cod_lote
                   INNER JOIN tesouraria.transferencia_ordem_pagamento_retencao
                           ON transferencia_ordem_pagamento_retencao.exercicio = ordem_pagamento_retencao.exercicio
                          AND transferencia_ordem_pagamento_retencao.cod_entidade = ordem_pagamento_retencao.cod_entidade
                          AND transferencia_ordem_pagamento_retencao.cod_ordem = ordem_pagamento_retencao.cod_ordem
                          AND transferencia_ordem_pagamento_retencao.cod_plano = ordem_pagamento_retencao.cod_plano
                          AND transferencia_ordem_pagamento_retencao.sequencial = ordem_pagamento_retencao.sequencial
                    LEFT JOIN (SELECT receita.cod_receita                                     
                                    , receita.cod_entidade                              
                                    , conta_receita.descricao                                
                                    , receita.exercicio                                      
                                 FROM orcamento.receita                                     
                            LEFT JOIN orcamento.conta_receita                          
                                   ON conta_receita.exercicio = receita.exercicio      
                                  AND conta_receita.cod_conta = receita.cod_conta      
                              ) as receita                                                   
                           ON receita.cod_receita = ordem_pagamento_retencao.cod_receita   
                          AND receita.exercicio = ordem_pagamento_retencao.exercicio       
                        WHERE lote.dt_lote BETWEEN to_date('".$this->getDado('dtInicio')."', 'dd/mm/yyyy') AND to_date('".$this->getDado('dtFim')."', 'dd/mm/yyyy')
                          AND ordem_pagamento_retencao.exercicio = '".$this->getDado('exercicio')."'
                          AND ordem_pagamento_retencao.cod_entidade IN (".$this->getDado('cod_entidade').")
                          AND lancamento_retencao.tipo = 'T'
                          AND receita.cod_receita IS NULL
                          AND EXISTS (SELECT 1 
                                        FROM empenho.nota_liquidacao_paga
                                  INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                          ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                                         AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota = nota_liquidacao_paga.cod_nota
                                         AND pagamento_liquidacao_nota_liquidacao_paga.timestamp = nota_liquidacao_paga.timestamp
                                       WHERE nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio
                                         AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                                         AND nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota
                                       LIMIT 1)
                      ) ELSE 0.00
                       END AS vl_retencao
                 FROM contabilidade.plano_banco
        
           INNER JOIN contabilidade.plano_analitica
                   ON plano_analitica.cod_plano = plano_banco.cod_plano
                  AND plano_analitica.exercicio = plano_banco.exercicio
        
            LEFT JOIN tcmgo.orgao_plano_banco
                   ON orgao_plano_banco.exercicio = plano_banco.exercicio
                  AND orgao_plano_banco.cod_plano = plano_banco.cod_plano
        
           INNER JOIN contabilidade.plano_conta
                   ON plano_conta.cod_conta = plano_analitica.cod_conta
                  AND plano_conta.exercicio = plano_analitica.exercicio
        
           INNER JOIN contabilidade.plano_recurso
                   ON plano_recurso.cod_plano = plano_analitica.cod_plano
                  AND plano_recurso.exercicio = plano_analitica.exercicio
        
           INNER JOIN orcamento.recurso
                   ON recurso.cod_recurso = plano_recurso.cod_recurso
                  AND recurso.exercicio   = plano_recurso.exercicio
          
           INNER JOIN monetario.conta_corrente
                   ON conta_corrente.cod_banco          = plano_banco.cod_banco
                  AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                  AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                  
           INNER JOIN monetario.agencia
                   ON agencia.cod_banco   = conta_corrente.cod_banco
                  AND agencia.cod_agencia = conta_corrente.cod_agencia
           INNER JOIN monetario.banco
                   ON banco.cod_banco = plano_banco.cod_banco
         
                WHERE plano_banco.exercicio = '".$this->getDado('exercicio')."'
                  AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').")
        
             GROUP BY orgao_plano_banco.num_orgao
                    , num_unidade
                    , banco.num_banco
                    , agencia.num_agencia
                    , conta_corrente.num_conta_corrente
                    , digito
                    , tipo_conta
                    , recurso.cod_recurso
                    , plano_analitica.cod_plano
                    , plano_analitica.exercicio
        ) AS ctb11
        GROUP BY num_orgao
             , num_unidade
             , num_banco
             , num_agencia
             , num_conta_corrente
             , digito
             , tipo_conta        
             , fonte
        HAVING SUM(saldo_inicial) + (SUM(vl_entradas) - SUM(vl_retencao)) + (SUM(vl_saidas) - SUM(vl_retencao)) + SUM(saldo_final) <> 0.00
        ";
        return $stSql;
    }
    
}
