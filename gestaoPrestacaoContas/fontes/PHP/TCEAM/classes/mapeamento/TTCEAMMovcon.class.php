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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 10/03/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEAMMovcon extends Persistente
{
    /*
    * Método Constructor
    * @access Private
    */
    public function TTCEAMMovcon()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaSaldoContabilContas(&$rsRecordSet, $stCondicao = '', $stOrdem = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaSaldoContabilContas().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaSaldoContabilContas()
    {
        $stSql = "
        SELECT reservado_tce
             , exercicio
             , conta_contabil
             , tipo_movimento
             , SUM(vl_credito) AS vl_credito
             , SUM(vl_debito) AS vl_debito
          FROM (
            SELECT '0'::text AS reservado_tce
                 , exercicio
                 , conta_contabil
                 , tipo_movimento
                 , lancamento_exercicio
                 , SUM(vl_credito) AS vl_credito
                 , SUM(vl_debito) AS vl_debito
              FROM (
                SELECT conta_credito.exercicio
                     , lancamento.cod_entidade
                     , lancamento.tipo
                     , substr(replace(conta_credito.cod_estrutural,'.',''),1,15) AS conta_contabil
                     , CASE WHEN lancamento.tipo = 'I' THEN
                                1
                            WHEN lancamento.cod_historico BETWEEN 800 AND 899 THEN
                                3
                         ELSE
                                2
                         END AS tipo_movimento
                     , ABS(valor_lancamento.vl_lancamento) AS vl_credito
                     , 0 AS vl_debito
                     , CASE WHEN lancamento.tipo = 'I' AND lancamento.cod_historico BETWEEN 800 AND 899 THEN
                            true
                       ELSE
                            false
                       END AS lancamento_exercicio
                  FROM contabilidade.lancamento
                  JOIN contabilidade.valor_lancamento
                    ON valor_lancamento.cod_lote     = lancamento.cod_lote
                   AND valor_lancamento.tipo         = lancamento.tipo
                   AND valor_lancamento.sequencia    = lancamento.sequencia
                   AND valor_lancamento.exercicio    = lancamento.exercicio
                   AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                  JOIN contabilidade.lote
                    ON lote.cod_lote      = lancamento.cod_lote
                   AND lote.exercicio     = lancamento.exercicio
                   AND lote.tipo          = lancamento.tipo
                   AND lote.cod_entidade  = lancamento.cod_entidade
                  JOIN orcamento.entidade
                    ON entidade.cod_entidade  = lancamento.cod_entidade
                   AND entidade.exercicio     = lancamento.exercicio
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm

                  JOIN ( SELECT conta_credito.cod_lote
                              , conta_credito.tipo
                              , conta_credito.sequencia
                              , conta_credito.exercicio
                              , conta_credito.tipo_valor
                              , conta_credito.cod_entidade
                              , conta_credito.cod_plano
                              , plano_conta.cod_estrutural
                           FROM contabilidade.plano_analitica
                           JOIN contabilidade.conta_credito
                             ON conta_credito.cod_plano    = plano_analitica.cod_plano
                            AND conta_credito.exercicio    = plano_analitica.exercicio
                           JOIN contabilidade.plano_conta
                             ON plano_conta.cod_conta    = plano_analitica.cod_conta
                            AND plano_conta.exercicio    = plano_analitica.exercicio
                          WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                     ) AS  conta_credito
                    ON conta_credito.cod_lote     = valor_lancamento.cod_lote
                   AND conta_credito.sequencia    = valor_lancamento.sequencia
                   AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                   AND conta_credito.tipo         = valor_lancamento.tipo
                   AND conta_credito.exercicio    = valor_lancamento.exercicio
                   AND conta_credito.cod_entidade = valor_lancamento.cod_entidade

                 WHERE lancamento.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(lote.dt_lote,'mm') = '".$this->getDado('mes')."' ";
                   if ($this->getDado('boIncorporar')) {
                     $stSql .= " AND lancamento.tipo <> 'I' ";
                   }
                   $stSql .= "

            UNION ALL

                SELECT conta_debito.exercicio
                     , lancamento.cod_entidade
                     , lancamento.tipo
                     , substr(replace(conta_debito.cod_estrutural,'.',''),1,15) AS conta_contabil
                     , CASE WHEN lancamento.tipo = 'I' THEN
                                1
                             WHEN lote.nom_lote ilike '%ENCERRAMENTO%'  THEN
                                3
                         ELSE
                         2
                         END AS tipo_movimento
                     , 0 AS vl_credito
                     , ABS(valor_lancamento.vl_lancamento) AS vl_debito
                     , false AS lancamento_exercicio
                  FROM contabilidade.lancamento
                  JOIN contabilidade.valor_lancamento
                    ON valor_lancamento.cod_lote     = lancamento.cod_lote
                   AND valor_lancamento.tipo         = lancamento.tipo
                   AND valor_lancamento.sequencia    = lancamento.sequencia
                   AND valor_lancamento.exercicio    = lancamento.exercicio
                   AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                  JOIN contabilidade.lote
                    ON lote.cod_lote      = lancamento.cod_lote
                   AND lote.exercicio     = lancamento.exercicio
                   AND lote.tipo          = lancamento.tipo
                   AND lote.cod_entidade  = lancamento.cod_entidade
                  JOIN orcamento.entidade
                    ON entidade.cod_entidade  = lancamento.cod_entidade
                   AND entidade.exercicio     = lancamento.exercicio
                  JOIN sw_cgm
                    ON sw_cgm.numcgm = entidade.numcgm

                  JOIN ( SELECT conta_debito.cod_lote
                              , conta_debito.tipo
                              , conta_debito.sequencia
                              , conta_debito.exercicio
                              , conta_debito.tipo_valor
                              , conta_debito.cod_entidade
                              , conta_debito.cod_plano
                              , plano_conta.cod_estrutural
                           FROM contabilidade.plano_analitica
                           JOIN contabilidade.conta_debito
                             ON conta_debito.cod_plano    = plano_analitica.cod_plano
                            AND conta_debito.exercicio    = plano_analitica.exercicio
                           JOIN contabilidade.plano_conta
                             ON plano_conta.cod_conta    = plano_analitica.cod_conta
                            AND plano_conta.exercicio    = plano_analitica.exercicio
                          WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                     ) AS  conta_debito
                    ON conta_debito.cod_lote     = valor_lancamento.cod_lote
                   AND conta_debito.sequencia    = valor_lancamento.sequencia
                   AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                   AND conta_debito.tipo         = valor_lancamento.tipo
                   AND conta_debito.exercicio    = valor_lancamento.exercicio
                   AND conta_debito.cod_entidade = valor_lancamento.cod_entidade

                 WHERE lancamento.exercicio = '".$this->getDado('exercicio')."'
                   AND to_char(lote.dt_lote,'mm') = '".$this->getDado('mes')."' ";
                   if ($this->getDado('boIncorporar')) {
                     $stSql .= " AND lancamento.tipo <> 'I' ";
                   }
            $stSql .= " ) AS tabela ";

        if ($this->getDado('mes') == '01' && strpos($this->getDado('cod_entidade'), ',')) {
            $stSql .= "
            WHERE cod_entidade = (SELECT valor FROM administracao.configuracao WHERE cod_modulo = 8 AND exercicio = '".$this->getDado('exercicio')."' AND parametro = 'cod_entidade_prefeitura')
               OR ( cod_entidade <> (SELECT valor FROM administracao.configuracao WHERE cod_modulo = 8 AND exercicio = '".$this->getDado('exercicio')."' AND parametro = 'cod_entidade_prefeitura')
                AND tipo = 'I'
                AND cod_entidade IN (".$this->getDado('cod_entidade')."))
            ";
        } else {
            $stSql .= "WHERE cod_entidade IN (".$this->getDado('cod_entidade').")";
        }

        $stSql .= "
            GROUP BY exercicio
                   , conta_contabil
                   , tipo_movimento
                   , lancamento_exercicio ";

        if ($this->getDado('boIncorporar')) {
          $stSql .= "
           UNION
                      SELECT '0' AS reservado_tce
                       , exercicio
                       , conta_contabil
                       , tipo_movimento
                       , lancamento_exercicio
                       , SUM(vl_credito) AS vl_credito
                       , SUM(vl_debito) AS vl_debito
                    FROM (
                      SELECT conta_credito.exercicio
                           , lancamento.cod_entidade
                           , lancamento.tipo
                           , substr(replace(conta_credito.cod_estrutural,'.',''),1,15) AS conta_contabil
                           , CASE WHEN lancamento.tipo = 'I' THEN
                                      1
                                  WHEN lancamento.cod_historico BETWEEN 800 AND 899 THEN
                                      3
                               ELSE
                                      2
                               END AS tipo_movimento
                           , ABS(valor_lancamento.vl_lancamento) AS vl_credito
                           , 0 AS vl_debito
                           , CASE WHEN lancamento.tipo = 'I' AND lancamento.cod_historico BETWEEN 800 AND 899 THEN
                                  true
                             ELSE
                                  false
                             END AS lancamento_exercicio
                        FROM contabilidade.lancamento
                        JOIN contabilidade.valor_lancamento
                          ON valor_lancamento.cod_lote     = lancamento.cod_lote
                         AND valor_lancamento.tipo         = lancamento.tipo
                         AND valor_lancamento.sequencia    = lancamento.sequencia
                         AND valor_lancamento.exercicio    = lancamento.exercicio
                         AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                        JOIN contabilidade.lote
                          ON lote.cod_lote      = lancamento.cod_lote
                         AND lote.exercicio     = lancamento.exercicio
                         AND lote.tipo          = lancamento.tipo
                         AND lote.cod_entidade  = lancamento.cod_entidade
                        JOIN orcamento.entidade
                          ON entidade.cod_entidade  = lancamento.cod_entidade
                         AND entidade.exercicio     = lancamento.exercicio
                        JOIN sw_cgm
                          ON sw_cgm.numcgm = entidade.numcgm

                        JOIN ( SELECT conta_credito.cod_lote
                                    , conta_credito.tipo
                                    , conta_credito.sequencia
                                    , conta_credito.exercicio
                                    , conta_credito.tipo_valor
                                    , conta_credito.cod_entidade
                                    , conta_credito.cod_plano
                                    , plano_conta.cod_estrutural
                                 FROM contabilidade.plano_analitica
                                 JOIN contabilidade.conta_credito
                                   ON conta_credito.cod_plano    = plano_analitica.cod_plano
                                  AND conta_credito.exercicio    = plano_analitica.exercicio
                                 JOIN contabilidade.plano_conta
                                   ON plano_conta.cod_conta    = plano_analitica.cod_conta
                                  AND plano_conta.exercicio    = plano_analitica.exercicio
                                WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'
                           ) AS  conta_credito
                          ON conta_credito.cod_lote     = valor_lancamento.cod_lote
                         AND conta_credito.sequencia    = valor_lancamento.sequencia
                         AND conta_credito.tipo_valor   = valor_lancamento.tipo_valor
                         AND conta_credito.tipo         = valor_lancamento.tipo
                         AND conta_credito.exercicio    = valor_lancamento.exercicio
                         AND conta_credito.cod_entidade = valor_lancamento.cod_entidade

                       WHERE lancamento.exercicio = '".$this->getDado('exercicio')."'
                         AND lancamento.tipo <> 'I'

                  UNION ALL

                      SELECT conta_debito.exercicio
                           , lancamento.cod_entidade
                           , lancamento.tipo
                           , substr(replace(conta_debito.cod_estrutural,'.',''),1,15) AS conta_contabil
                           , CASE WHEN lancamento.tipo = 'I' THEN
                                      1
                                   WHEN lote.nom_lote ilike '%ENCERRAMENTO%'  THEN
                                      3
                               ELSE
                               2
                               END AS tipo_movimento
                           , 0 AS vl_credito
                           , ABS(valor_lancamento.vl_lancamento) AS vl_debito
                           , false AS lancamento_exercicio
                        FROM contabilidade.lancamento
                        JOIN contabilidade.valor_lancamento
                          ON valor_lancamento.cod_lote     = lancamento.cod_lote
                         AND valor_lancamento.tipo         = lancamento.tipo
                         AND valor_lancamento.sequencia    = lancamento.sequencia
                         AND valor_lancamento.exercicio    = lancamento.exercicio
                         AND valor_lancamento.cod_entidade = lancamento.cod_entidade
                        JOIN contabilidade.lote
                          ON lote.cod_lote      = lancamento.cod_lote
                         AND lote.exercicio     = lancamento.exercicio
                         AND lote.tipo          = lancamento.tipo
                         AND lote.cod_entidade  = lancamento.cod_entidade
                        JOIN orcamento.entidade
                          ON entidade.cod_entidade  = lancamento.cod_entidade
                         AND entidade.exercicio     = lancamento.exercicio
                        JOIN sw_cgm
                          ON sw_cgm.numcgm = entidade.numcgm

                        JOIN ( SELECT conta_debito.cod_lote
                                    , conta_debito.tipo
                                    , conta_debito.sequencia
                                    , conta_debito.exercicio
                                    , conta_debito.tipo_valor
                                    , conta_debito.cod_entidade
                                    , conta_debito.cod_plano
                                    , plano_conta.cod_estrutural
                                 FROM contabilidade.plano_analitica
                                 JOIN contabilidade.conta_debito
                                   ON conta_debito.cod_plano    = plano_analitica.cod_plano
                                  AND conta_debito.exercicio    = plano_analitica.exercicio
                                 JOIN contabilidade.plano_conta
                                   ON plano_conta.cod_conta    = plano_analitica.cod_conta
                                  AND plano_conta.exercicio    = plano_analitica.exercicio
                                WHERE plano_analitica.exercicio = '2011'
                           ) AS  conta_debito
                          ON conta_debito.cod_lote     = valor_lancamento.cod_lote
                         AND conta_debito.sequencia    = valor_lancamento.sequencia
                         AND conta_debito.tipo_valor   = valor_lancamento.tipo_valor
                         AND conta_debito.tipo         = valor_lancamento.tipo
                         AND conta_debito.exercicio    = valor_lancamento.exercicio
                         AND conta_debito.cod_entidade = valor_lancamento.cod_entidade

                       WHERE lancamento.exercicio = '".$this->getDado('exercicio')."'
                         AND lancamento.tipo <> 'I'
                  ) AS tabela
                     WHERE cod_entidade IN (".$this->getDado('stCodEntidadesIncorporar').")
                  GROUP BY exercicio
                         , conta_contabil
                         , tipo_movimento
                         , lancamento_exercicio  ";
        }

        $stSql .= " ) as tabela
        GROUP BY reservado_tce
               , exercicio
               , conta_contabil
               , tipo_movimento
               , lancamento_exercicio
        ORDER BY conta_contabil ";

        return $stSql;
    }
}
?>
