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
 * Mapeamento da tabela tesouraria.cheque_emissao_transferencia
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaChequeEmissaoTransferencia extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaChequeEmissaoTransferencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque_emissao_transferencia');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_lote, cod_entidade, exercicio, tipo, cod_agencia, cod_banco, cod_conta_corrente, num_cheque');

        $this->AddCampo('cod_lote'           ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_entidade'       ,'integer'   , true , ''     , true , true );
        $this->AddCampo('exercicio'          ,'varchar'   , true , '4'    , true , true );
        $this->AddCampo('tipo'               ,'varchar'   , true , '1'    , true , true );
        $this->AddCampo('cod_agencia'        ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_banco'          ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_conta_corrente' ,'integer'   , true , ''     , true , true );
        $this->AddCampo('num_cheque'         ,'varchar'   , true , '15'   , true , true );
        $this->AddCampo('timestamp_emissao'  ,'timestamp' , true , ''     , true , false );
    }
    
    
    function recuperaChequeTransferenciaSaldo(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->getChequeTransferenciaSaldo();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }
    
    public function getChequeTransferenciaSaldo()
    {
        $stSql = "
        SELECT
            T.cod_lote,
            T.exercicio,
            T.cod_entidade,
            ent.nom_cgm as nom_entidade,
            T.tipo,
            T.cod_boletim,
            TO_CHAR(boletim.dt_boletim,'dd/mm/yyyy') AS data_cheque,
            T.cod_historico,
            T.observacao,
            t.cod_plano_credito,
            credito.nom_conta as nom_conta_credito,
            t.cod_plano_debito,
            debito.nom_conta as nom_conta_debito,
            (coalesce(t.valor,0.00) - coalesce(te.valor,0.00) - COALESCE(cheque_emissao_transferencia.valor,0.00)) as valor,
            tc.cod_credor,
            tc.nom_credor,
            t.cod_tipo
        FROM
         tesouraria.transferencia as T
         LEFT JOIN
            ( SELECT
                 tc.numcgm as cod_credor,
                 tc.exercicio,
                 tc.tipo,
                 tc.cod_entidade,
                 tc.cod_lote,
                 cgm.nom_cgm as nom_credor
              FROM
                 tesouraria.transferencia_credor  as TC,
                 sw_cgm  as CGM
              WHERE
                     tc.numcgm    = cgm.numcgm
            ) as TC on (     tc.tipo         = t.tipo
                         AND tc.exercicio    = t.exercicio
                         AND tc.cod_entidade = t.cod_entidade
                         AND tc.cod_lote     = t.cod_lote
                       )
         LEFT JOIN
             ( SELECT
                  cgm.nom_cgm,
                  e.cod_entidade,
                  e.exercicio
               FROM
                  sw_cgm as CGM,
                  orcamento.entidade as E
               WHERE
                  cgm.numcgm = e.numcgm
             ) as ENT on (
                  ent.exercicio    = t.exercicio    AND
                  ent.cod_entidade = t.cod_entidade
             )
         LEFT JOIN
             ( SELECT
                 pc.nom_conta,
                 pa.cod_plano,
                 pa.exercicio
               FROM
                 contabilidade.plano_conta     as pc,
                 contabilidade.plano_analitica as pa
               WHERE
                 pa.exercicio = pc.exercicio AND
                 pa.cod_conta = pc.cod_conta
             ) as debito on (
                     debito.cod_plano = t.cod_plano_debito AND
                     debito.exercicio = t.exercicio
             )
         LEFT JOIN
             ( SELECT
                 pc.nom_conta,
                 pa.cod_plano,
                 pa.exercicio
               FROM
                 contabilidade.plano_conta     as pc,
                 contabilidade.plano_analitica as pa
               WHERE
                 pa.exercicio = pc.exercicio AND
                 pa.cod_conta = pc.cod_conta
             ) as credito on (
                     credito.cod_plano = t.cod_plano_credito AND
                     credito.exercicio = t.exercicio
             )
         LEFT JOIN
             ( SELECT
                     coalesce(sum(te.valor),0.00) as valor,
                     te.cod_lote,
                     te.cod_entidade,
                     te.exercicio,
                     te.tipo
               FROM tesouraria.transferencia_estornada as te
               GROUP BY
                     te.cod_lote,
                     te.cod_entidade,
                     te.exercicio,
                     te.tipo
             ) as te on (
                    t.cod_lote        = te.cod_lote          AND
                    t.cod_entidade    = te.cod_entidade      AND
                    t.exercicio       = te.exercicio         AND
                    t.tipo            = te.tipo
             )
         LEFT JOIN tesouraria.tipo_transferencia as TT on (
                    t.cod_tipo = tt.cod_tipo )
         LEFT JOIN tesouraria.boletim
                ON T.cod_boletim  = boletim.cod_boletim
               AND T.cod_entidade = boletim.cod_entidade
               AND T.exercicio    = boletim.exercicio
         LEFT JOIN ( SELECT cheque_emissao_transferencia.cod_lote
                          , cheque_emissao_transferencia.cod_entidade
                          , cheque_emissao_transferencia.exercicio
                          , cheque_emissao_transferencia.tipo
                          , SUM(COALESCE(cheque_emissao.valor,0.00)) AS valor
                       FROM tesouraria.cheque_emissao_transferencia
                 INNER JOIN tesouraria.cheque_emissao
                         ON cheque_emissao_transferencia.num_cheque         = cheque_emissao.num_cheque
                        AND cheque_emissao_transferencia.cod_banco          = cheque_emissao.cod_banco
                        AND cheque_emissao_transferencia.cod_agencia        = cheque_emissao.cod_agencia
                        AND cheque_emissao_transferencia.cod_conta_corrente = cheque_emissao.cod_conta_corrente
                        AND cheque_emissao_transferencia.timestamp_emissao  = cheque_emissao.timestamp_emissao
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM tesouraria.cheque_emissao_anulada
                                          WHERE cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
                                            AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
                                            AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
                                            AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
                                            AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
                                       )
                   GROUP BY cheque_emissao_transferencia.cod_lote
                          , cheque_emissao_transferencia.cod_entidade
                          , cheque_emissao_transferencia.exercicio
                          , cheque_emissao_transferencia.tipo
                   ) AS cheque_emissao_transferencia
                ON cheque_emissao_transferencia.cod_lote     = t.cod_lote
               AND cheque_emissao_transferencia.cod_entidade = t.cod_entidade
               AND cheque_emissao_transferencia.exercicio    = t.exercicio
               AND cheque_emissao_transferencia.tipo         = t.tipo

                    ";

        return $stSql;
    }

    /**
     * Método que retorna os cheques vinculados a uma emissao por transferencia
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    
    function listChequesEmissaoTransferencia(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->recuperaChequesEmissaoTransferencia().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }
    
    public function recuperaChequesEmissaoTransferencia()
    {
        $stSql = "
            SELECT cheque.num_cheque
                 , conta_corrente.cod_conta_corrente
                 , conta_corrente.num_conta_corrente
                 , agencia.cod_agencia
                 , agencia.num_agencia
                 , agencia.nom_agencia
                 , banco.cod_banco
                 , banco.num_banco
                 , banco.nom_banco
                 , cheque_emissao.valor
                 , TO_CHAR(cheque_emissao.data_emissao,'dd/mm/yyyy') AS data_emissao
                 , cheque_emissao.timestamp_emissao
                 , cheque_emissao_transferencia.cod_lote
                 , cheque_emissao_transferencia.exercicio
                 , cheque_emissao_transferencia.cod_entidade
                 , cheque_emissao_transferencia.tipo
              FROM tesouraria.cheque
        INNER JOIN ( SELECT cheque_emissao.cod_banco
                          , cheque_emissao.cod_agencia
                          , cheque_emissao.cod_conta_corrente
                          , cheque_emissao.num_cheque
                          , cheque_emissao.timestamp_emissao
                          , cheque_emissao.valor
                          , cheque_emissao.data_emissao
                       FROM tesouraria.cheque_emissao
                      WHERE NOT EXISTS ( SELECT 1
                                           FROM tesouraria.cheque_emissao_anulada
                                          WHERE cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
                                            AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
                                            AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
                                            AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
                                            AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
                                       )
                   ) AS cheque_emissao
                ON cheque.cod_banco          = cheque_emissao.cod_banco
               AND cheque.cod_agencia        = cheque_emissao.cod_agencia
               AND cheque.cod_conta_corrente = cheque_emissao.cod_conta_corrente
               AND cheque.num_cheque         = cheque_emissao.num_cheque

        INNER JOIN tesouraria.cheque_emissao_transferencia
                ON cheque_emissao.cod_banco          = cheque_emissao_transferencia.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_transferencia.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_transferencia.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_transferencia.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_transferencia.timestamp_emissao

        INNER JOIN monetario.conta_corrente
                ON cheque.cod_conta_corrente  = conta_corrente.cod_conta_corrente
               AND cheque.cod_agencia         = conta_corrente.cod_agencia
               AND cheque.cod_banco           = conta_corrente.cod_banco

        INNER JOIN monetario.agencia
                ON conta_corrente.cod_agencia = agencia.cod_agencia
               AND conta_corrente.cod_banco   = agencia.cod_banco

        INNER JOIN monetario.banco
                ON agencia.cod_banco          = banco.cod_banco
        ";

        return $stSql;
    }

    /**
     * Método que retorna os cheques vinculados a uma emissao por transferencia que possa ser baixado
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $rsRecordSet
     */
    
    function listChequesEmissaoTransferenciaBaixa(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->recuperaChequesEmissaoTransferenciaBaixa();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }
    
    public function recuperaChequesEmissaoTransferenciaBaixa()
    {
        $stSql = "
        SELECT
            T.cod_lote,
            T.exercicio,
            T.cod_entidade,
            ent.nom_cgm as nom_entidade,
            T.tipo,
            T.cod_boletim,
            T.cod_historico,
            T.observacao,
            t.cod_plano_credito,
            credito.nom_conta as nom_conta_credito,
            t.cod_plano_debito,
            debito.nom_conta as nom_conta_debito,
            (coalesce(t.valor,0.00) - coalesce(te.valor,0.00)) as valor,
            tc.cod_credor,
            tc.nom_credor,
            t.cod_tipo
        FROM
         tesouraria.transferencia as T
         LEFT JOIN
            ( SELECT
                 tc.numcgm as cod_credor,
                 tc.exercicio,
                 tc.tipo,
                 tc.cod_entidade,
                 tc.cod_lote,
                 cgm.nom_cgm as nom_credor
              FROM
                 tesouraria.transferencia_credor  as TC,
                 sw_cgm  as CGM
              WHERE
                     tc.numcgm    = cgm.numcgm
            ) as TC on (     tc.tipo         = t.tipo
                         AND tc.exercicio    = t.exercicio
                         AND tc.cod_entidade = t.cod_entidade
                         AND tc.cod_lote     = t.cod_lote
                       )
         LEFT JOIN
             ( SELECT
                  cgm.nom_cgm,
                  e.cod_entidade,
                  e.exercicio
               FROM
                  sw_cgm as CGM,
                  orcamento.entidade as E
               WHERE
                  cgm.numcgm = e.numcgm
             ) as ENT on (
                  ent.exercicio    = t.exercicio    AND
                  ent.cod_entidade = t.cod_entidade
             )
         LEFT JOIN
             ( SELECT
                 pc.nom_conta,
                 pa.cod_plano,
                 pa.exercicio
               FROM
                 contabilidade.plano_conta     as pc,
                 contabilidade.plano_analitica as pa
               WHERE
                 pa.exercicio = pc.exercicio AND
                 pa.cod_conta = pc.cod_conta
             ) as debito on (
                     debito.cod_plano = t.cod_plano_debito AND
                     debito.exercicio = t.exercicio
             )
         LEFT JOIN
             ( SELECT
                 pc.nom_conta,
                 pa.cod_plano,
                 pa.exercicio
               FROM
                 contabilidade.plano_conta     as pc,
                 contabilidade.plano_analitica as pa
               WHERE
                 pa.exercicio = pc.exercicio AND
                 pa.cod_conta = pc.cod_conta
             ) as credito on (
                     credito.cod_plano = t.cod_plano_credito AND
                     credito.exercicio = t.exercicio
             )
         LEFT JOIN
             ( SELECT
                     coalesce(sum(te.valor),0.00) as valor,
                     te.cod_lote,
                     te.cod_entidade,
                     te.exercicio,
                     te.tipo
               FROM tesouraria.transferencia_estornada as te
               GROUP BY
                     te.cod_lote,
                     te.cod_entidade,
                     te.exercicio,
                     te.tipo
             ) as te on (
                    t.cod_lote        = te.cod_lote          AND
                    t.cod_entidade    = te.cod_entidade      AND
                    t.exercicio       = te.exercicio         AND
                    t.tipo            = te.tipo
             )
         LEFT JOIN tesouraria.tipo_transferencia as TT on (
                    t.cod_tipo = tt.cod_tipo )
        INNER JOIN ( SELECT cheque_emissao_transferencia.cod_lote
                          , cheque_emissao_transferencia.cod_entidade
                          , cheque_emissao_transferencia.exercicio
                          , cheque_emissao_transferencia.tipo
                          , SUM(COALESCE(cheque_emissao.valor,0.00)) AS valor
                       FROM tesouraria.cheque_emissao_transferencia
                 INNER JOIN tesouraria.cheque_emissao
                         ON cheque_emissao_transferencia.num_cheque         = cheque_emissao.num_cheque
                        AND cheque_emissao_transferencia.cod_banco          = cheque_emissao.cod_banco
                        AND cheque_emissao_transferencia.cod_agencia        = cheque_emissao.cod_agencia
                        AND cheque_emissao_transferencia.cod_conta_corrente = cheque_emissao.cod_conta_corrente
                        AND cheque_emissao_transferencia.timestamp_emissao  = cheque_emissao.timestamp_emissao

                  LEFT JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                   , cheque_emissao_baixa.cod_agencia
                                   , cheque_emissao_baixa.cod_conta_corrente
                                   , cheque_emissao_baixa.num_cheque
                                   , cheque_emissao_baixa.timestamp_emissao
                                FROM tesouraria.cheque_emissao_baixa
                          INNER JOIN ( SELECT cheque_emissao_baixa.cod_banco
                                            , cheque_emissao_baixa.cod_agencia
                                            , cheque_emissao_baixa.cod_conta_corrente
                                            , cheque_emissao_baixa.num_cheque
                                            , cheque_emissao_baixa.timestamp_emissao
                                            , MAX(cheque_emissao_baixa.timestamp_baixa) AS timestamp_baixa
                                         FROM tesouraria.cheque_emissao_baixa
                                     GROUP BY cheque_emissao_baixa.cod_banco
                                            , cheque_emissao_baixa.cod_agencia
                                            , cheque_emissao_baixa.cod_conta_corrente
                                            , cheque_emissao_baixa.num_cheque
                                            , cheque_emissao_baixa.timestamp_emissao
                                     ) AS cheque_emissao_baixa_max
                                  ON cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_max.cod_banco
                                 AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_max.cod_agencia
                                 AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_max.cod_conta_corrente
                                 AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_max.num_cheque
                                 AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_max.timestamp_emissao
                                 AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_max.timestamp_baixa
                               WHERE NOT EXISTS ( SELECT 1
                                                    FROM tesouraria.cheque_emissao_baixa_anulada
                                                   WHERE cheque_emissao_baixa.cod_banco          = cheque_emissao_baixa_anulada.cod_banco
                                                     AND cheque_emissao_baixa.cod_agencia        = cheque_emissao_baixa_anulada.cod_agencia
                                                     AND cheque_emissao_baixa.cod_conta_corrente = cheque_emissao_baixa_anulada.cod_conta_corrente
                                                     AND cheque_emissao_baixa.num_cheque         = cheque_emissao_baixa_anulada.num_cheque
                                                     AND cheque_emissao_baixa.timestamp_emissao  = cheque_emissao_baixa_anulada.timestamp_emissao
                                                     AND cheque_emissao_baixa.timestamp_baixa    = cheque_emissao_baixa_anulada.timestamp_baixa
                                                )
                           ) AS cheque_emissao_baixa
                        ON cheque_emissao_transferencia.cod_banco          = cheque_emissao_baixa.cod_banco
                       AND cheque_emissao_transferencia.cod_agencia        = cheque_emissao_baixa.cod_agencia
                       AND cheque_emissao_transferencia.cod_conta_corrente = cheque_emissao_baixa.cod_conta_corrente
                       AND cheque_emissao_transferencia.num_cheque         = cheque_emissao_baixa.num_cheque
                       AND cheque_emissao_transferencia.timestamp_emissao  = cheque_emissao_baixa.timestamp_emissao

                INNER JOIN monetario.conta_corrente
                        ON cheque_emissao.cod_conta_corrente  = conta_corrente.cod_conta_corrente
                       AND cheque_emissao.cod_agencia         = conta_corrente.cod_agencia
                       AND cheque_emissao.cod_banco           = conta_corrente.cod_banco

                INNER JOIN monetario.agencia
                        ON conta_corrente.cod_agencia = agencia.cod_agencia
                       AND conta_corrente.cod_banco   = agencia.cod_banco

                INNER JOIN monetario.banco
                        ON agencia.cod_banco          = banco.cod_banco

                     WHERE NOT EXISTS ( SELECT 1
                                          FROM tesouraria.cheque_emissao_anulada
                                         WHERE cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
                                           AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
                                           AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
                                           AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
                                           AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
                                      ) ";
        if ($this->getDado('num_cheque') != '') {
            $stSql .= " AND cheque_emissao.num_cheque = '" . $this->getDado('num_cheque') . "' ";
        }
        if ($this->getDado('num_banco') != '') {
            $stSql .= " AND banco.num_banco = '" . $this->getDado('num_banco') . "' ";
        }
        if ($this->getDado('num_agencia') != '') {
            $stSql .= " AND agencia.num_agencia = '" . $this->getDado('num_agencia') . "' ";
        }
        if ($this->getDado('num_conta_corrente') != '') {
            $stSql .= " AND conta_corrente.num_conta_corrente = '" . $this->getDado('num_conta_corrente') . "' ";
        }
        if ($this->getDado('baixado') == 'sim') {
            $stSql .= " AND cheque_emissao_baixa.num_cheque IS NOT NULL";
        } else {
            $stSql .= " AND cheque_emissao_baixa.num_cheque IS NULL";
        }
        $stSql .= "
                   GROUP BY cheque_emissao_transferencia.cod_lote
                          , cheque_emissao_transferencia.cod_entidade
                          , cheque_emissao_transferencia.exercicio
                          , cheque_emissao_transferencia.tipo
                   ) AS cheque_emissao_transferencia
                ON cheque_emissao_transferencia.cod_lote     = t.cod_lote
               AND cheque_emissao_transferencia.cod_entidade = t.cod_entidade
               AND cheque_emissao_transferencia.exercicio    = t.exercicio
               AND cheque_emissao_transferencia.tipo         = t.tipo
        ";

        return $stSql;
    }

    public function __destruct(){}
}
