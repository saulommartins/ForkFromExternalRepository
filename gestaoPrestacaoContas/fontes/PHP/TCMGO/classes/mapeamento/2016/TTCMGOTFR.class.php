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
  * Classe de mapeamento do arquivo TFR do TCM-GO
  * Data de Criação: 17/03/2014
  * 
  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: TTCMGOTFR.class.php 65190 2016-04-29 19:36:51Z michel $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMGOTFR extends Persistente
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
            SELECT LPAD(c.tipo_registro::VARCHAR, 2,'0') AS tipo_registro
                 , LPAD(c.num_orgao::VARCHAR, 2, '0') AS cod_orgao
                 , '01' as cod_unidade
                 , LPAD(c.num_banco::VARCHAR, 3, '0') AS banco
                 , LPAD(c.num_agencia::VARCHAR, 4, '0') AS agencia
                 , LPAD(c.num_conta_corrente::VARCHAR, 12, '0') AS conta_corrente
                 , c.digito AS conta_corrente_dv
                 , c.tipo_conta
                 , '203000' AS codigo_fonte_origem
                 
                 , SUM(c.vl_saidas)     AS vl_saidas
                 , SUM(c.vl_entradas)   AS vl_entradas
                 , SUM(c.saldo_inicial) AS vl_decrescido
                 , SUM(c.saldo_final)   AS saldo_final
                 
              FROM (
                   SELECT '10'::int AS tipo_registro
                        , num_orgao
                        , CASE WHEN ltrim(replace(num_agencia,'-',''),'9') = '' AND num_banco = '999' THEN
                                   '999999999999'
                               ELSE
                                   ltrim(split_part(conta_corrente,'-',1),'0')
                               END AS num_conta_corrente
                        , num_banco
                        , ltrim(replace(num_agencia,'-',''),'0') as num_agencia
                        , ltrim(split_part(conta_corrente,'-',2),'0') AS digito
                        , plano_conta.nom_conta
                        , plano_analitica.cod_plano
                        , plano_analitica.exercicio
                        , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                               '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                               '02'
                               ELSE
                               '01'
                           END as tipo_conta
                        , '0' AS numero_sequencial
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                          FROM contabilidade.conta_debito
                                         INNER JOIN  contabilidade.valor_lancamento
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo != 'I'
                                         WHERE conta_debito.exercicio = pa.exercicio
                                           AND conta_debito.cod_plano = pa.cod_plano
                                    )) as vl_total
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                           ) AS vl_entradas
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo != 'I'
                                         WHERE conta_credito.exercicio = pa.exercicio
                                           AND conta_credito.cod_plano = pa.cod_plano
                                         )) as vl_total
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                           ) * -1  AS  vl_saidas
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo = 'I'
                                         WHERE conta_debito.exercicio = pa.exercicio
                                           AND conta_debito.cod_plano = pa.cod_plano
                                       )
                                      +
                                      ( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo = 'I'
                                         WHERE conta_credito.exercicio = pa.exercicio
                                           AND conta_credito.cod_plano = pa.cod_plano
                                       )) as vl_total
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                          ) AS  saldo_inicial
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                         WHERE conta_debito.exercicio = pa.exercicio
                                           AND conta_debito.cod_plano = pa.cod_plano
                                       )
                                       +
                                       ( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                            AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                          WHERE conta_credito.exercicio = pa.exercicio
                                            AND conta_credito.cod_plano = pa.cod_plano
                                        ))
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                          ) AS saldo_final
                     FROM tcmgo.orgao_plano_banco
                    INNER JOIN contabilidade.plano_banco
                       ON plano_banco.cod_plano = orgao_plano_banco.cod_plano
                      AND plano_banco.exercicio = orgao_plano_banco.exercicio
                    INNER JOIN contabilidade.plano_analitica
                       ON plano_analitica.cod_plano = plano_banco.cod_plano
                      AND plano_analitica.exercicio = plano_banco.exercicio
                    INNER JOIN contabilidade.plano_conta
                       ON plano_conta.cod_conta = plano_analitica.cod_conta
                      AND plano_conta.exercicio = plano_analitica.exercicio
                    INNER JOIN monetario.agencia
                       ON agencia.cod_banco = plano_banco.cod_banco
                      AND agencia.cod_agencia = plano_banco.cod_agencia
                    INNER JOIN monetario.banco
                       ON banco.cod_banco = plano_banco.cod_banco
                    WHERE plano_banco.exercicio = '".$this->getDado('exercicio')."'
                      AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').") ";
                    if($inMes[1] != '01'){
                        $stSql .= " AND plano_banco.cod_entidade IN (100) ";
                    }
                    $stSql .= "
                    GROUP BY num_orgao
                        , conta_corrente
                        , agencia.num_agencia
                        , banco.num_banco
                        , plano_conta.nom_conta
                        , plano_analitica.cod_plano
                        , plano_analitica.exercicio
                        , plano_conta.cod_estrutural
                 ) as c
             GROUP BY c.tipo_registro
                 , c.num_orgao
                 , c.num_conta_corrente
                 , c.num_banco
                 , c.num_agencia,c.digito
                 , c.tipo_conta         
        ";
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
            SELECT LPAD(c.tipo_registro::VARCHAR, 2,'0') AS tipo_registro
                 , LPAD(c.num_orgao::VARCHAR, 2, '0') AS cod_orgao
                 , '01' as cod_unidade
                 , LPAD(c.num_banco::VARCHAR, 3, '0') AS banco
                 , LPAD(c.num_agencia::VARCHAR, 4, '0') AS agencia
                 , LPAD(c.num_conta_corrente::VARCHAR, 12, '0') AS conta_corrente
                 , c.digito AS conta_corrente_dv
                 , c.tipo_conta
                 , '203000' AS codigo_fonte_origem
                 , '103000' AS codigo_fonte_destino
                 
                 , SUM(c.vl_saidas)     AS vl_saidas
                 , SUM(c.vl_entradas)   AS vl_entradas
                 , SUM(c.saldo_inicial) AS vl_acrescido
                 , SUM(c.saldo_final)   AS saldo_final
                 
              FROM (
                   SELECT '11'::int AS tipo_registro
                        , num_orgao
                        , CASE WHEN ltrim(replace(num_agencia,'-',''),'9') = '' AND num_banco = '999' THEN
                                   '999999999999'
                               ELSE
                                   ltrim(split_part(conta_corrente,'-',1),'0')
                               END AS num_conta_corrente
                        , num_banco
                        , ltrim(replace(num_agencia,'-',''),'0') as num_agencia
                        , ltrim(split_part(conta_corrente,'-',2),'0') AS digito
                        , plano_conta.nom_conta
                        , plano_analitica.cod_plano
                        , plano_analitica.exercicio
                        , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                               '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                               '02'
                               ELSE
                               '01'
                           END as tipo_conta
                        , '0' AS numero_sequencial
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
                                          FROM contabilidade.conta_debito
                                         INNER JOIN  contabilidade.valor_lancamento
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo != 'I'
                                         WHERE conta_debito.exercicio = pa.exercicio
                                           AND conta_debito.cod_plano = pa.cod_plano
                                    )) as vl_total
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                           ) AS vl_entradas
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo != 'I'
                                         WHERE conta_credito.exercicio = pa.exercicio
                                           AND conta_credito.cod_plano = pa.cod_plano
                                         )) as vl_total
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                           ) * -1  AS  vl_saidas
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo = 'I'
                                         WHERE conta_debito.exercicio = pa.exercicio
                                           AND conta_debito.cod_plano = pa.cod_plano
                                       )
                                      +
                                      ( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                           AND lote.exercicio = '".$this->getDado('exercicio')."'
                                           AND lote.tipo = 'I'
                                         WHERE conta_credito.exercicio = pa.exercicio
                                           AND conta_credito.cod_plano = pa.cod_plano
                                       )) as vl_total
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                          ) AS  saldo_inicial
                        , (SELECT SUM(( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                           AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                         WHERE conta_debito.exercicio = pa.exercicio
                                           AND conta_debito.cod_plano = pa.cod_plano
                                       )
                                       +
                                       ( SELECT COALESCE(SUM(valor_lancamento.vl_lancamento),0.00) as vl_total
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
                                            AND lote.dt_lote BETWEEN TO_DATE('01/01/".$this->getDado('exercicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                          WHERE conta_credito.exercicio = pa.exercicio
                                            AND conta_credito.cod_plano = pa.cod_plano
                                        ))
                             FROM contabilidade.plano_analitica AS pa
                            WHERE pa.cod_plano = plano_analitica.cod_plano
                              AND pa.exercicio = plano_analitica.exercicio
                          ) AS saldo_final
                     FROM tcmgo.orgao_plano_banco
                    INNER JOIN contabilidade.plano_banco
                       ON plano_banco.cod_plano = orgao_plano_banco.cod_plano
                      AND plano_banco.exercicio = orgao_plano_banco.exercicio
                    INNER JOIN contabilidade.plano_analitica
                       ON plano_analitica.cod_plano = plano_banco.cod_plano
                      AND plano_analitica.exercicio = plano_banco.exercicio
                    INNER JOIN contabilidade.plano_conta
                       ON plano_conta.cod_conta = plano_analitica.cod_conta
                      AND plano_conta.exercicio = plano_analitica.exercicio
                    INNER JOIN monetario.agencia
                       ON agencia.cod_banco = plano_banco.cod_banco
                      AND agencia.cod_agencia = plano_banco.cod_agencia
                    INNER JOIN monetario.banco
                       ON banco.cod_banco = plano_banco.cod_banco
                    WHERE plano_banco.exercicio = '".$this->getDado('exercicio')."'
                      AND plano_banco.cod_entidade IN (".$this->getDado('cod_entidade').") ";
                    if($inMes[1] != '01'){
                        $stSql .= " AND plano_banco.cod_entidade IN (100) ";
                    }
                    $stSql .= "
                    GROUP BY num_orgao
                        , conta_corrente
                        , agencia.num_agencia
                        , banco.num_banco
                        , plano_conta.nom_conta
                        , plano_analitica.cod_plano
                        , plano_analitica.exercicio
                        , plano_conta.cod_estrutural
                 ) as c
             GROUP BY c.tipo_registro
                 , c.num_orgao
                 , c.num_conta_corrente
                 , c.num_banco
                 , c.num_agencia,c.digito
                 , c.tipo_conta         
        ";
        return $stSql;
    }
}

?>