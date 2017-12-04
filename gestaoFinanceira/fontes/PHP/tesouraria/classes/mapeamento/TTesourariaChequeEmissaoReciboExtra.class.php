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
 * Mapeamento da tabela tesouraria.cheque_emissao_recibo_extra
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include_once CLA_PERSISTENTE;

class TTesourariaChequeEmissaoReciboExtra extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaChequeEmissaoDespesaExtra
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque_emissao_recibo_extra');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_recibo_extra, cod_entidade, exercicio, tipo_recibo, cod_agencia, cod_banco, cod_conta_corrente, num_cheque');

        $this->AddCampo('cod_recibo_extra'   ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_entidade'       ,'integer'   , true , ''     , true , true );
        $this->AddCampo('exercicio'          ,'varchar'   , true , '4'    , true , true );
        $this->AddCampo('tipo_recibo'        ,'varchar'   , true , '1'    , true , true );
        $this->AddCampo('cod_agencia'        ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_banco'          ,'integer'   , true , ''     , true , true );
        $this->AddCampo('cod_conta_corrente' ,'integer'   , true , ''     , true , true );
        $this->AddCampo('num_cheque'         ,'varchar'   , true , '15'   , true , true );
        $this->AddCampo('timestamp_emissao'  ,'timestamp' , true , ''     , true , false );
    }

    /**
     * Método que retronar os recibo extras que possam ser emitidos cheques
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
    
    function recuperaChequeReciboExtraSaldo(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->getChequeReciboExtraSaldo().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }
    
    public function getChequeReciboExtraSaldo()
    {
        $stSql = "
        SELECT recibo_extra.cod_recibo_extra
             , recibo_extra.exercicio
             , recibo_extra.cod_entidade
             , entidade_cgm.nom_cgm AS nom_entidade
             , (recibo_extra.valor - COALESCE(cheque_emissao_recibo_extra.valor,0.00)) AS valor
             , credor_cgm.nom_cgm AS nom_credor
             , TO_CHAR(recibo_extra.timestamp,'dd/mm/yyyy') AS data_cheque
          FROM tesouraria.recibo_extra
     LEFT JOIN tesouraria.recibo_extra_credor
            ON recibo_extra.exercicio        = recibo_extra_credor.exercicio
           AND recibo_extra.cod_entidade     = recibo_extra_credor.cod_entidade
           AND recibo_extra.cod_recibo_extra = recibo_extra_credor.cod_recibo_extra
           AND recibo_extra.tipo_recibo      = recibo_extra_credor.tipo_recibo
     LEFT JOIN sw_cgm AS credor_cgm
            ON recibo_extra_credor.numcgm = credor_cgm.numcgm
    INNER JOIN orcamento.entidade
            ON recibo_extra.cod_entidade = entidade.cod_entidade
           AND recibo_extra.exercicio    = entidade.exercicio
    INNER JOIN sw_cgm AS entidade_cgm
            ON entidade.numcgm = entidade_cgm.numcgm
     LEFT JOIN ( SELECT cheque_emissao_recibo_extra.exercicio
                      , cheque_emissao_recibo_extra.cod_entidade
                      , cheque_emissao_recibo_extra.cod_recibo_extra
                      , cheque_emissao_recibo_extra.tipo_recibo
                      , SUM(cheque_emissao.valor) AS valor
                   FROM tesouraria.cheque_emissao
             INNER JOIN tesouraria.cheque_emissao_recibo_extra
                     ON cheque_emissao.cod_banco          = cheque_emissao_recibo_extra.cod_banco
                    AND cheque_emissao.cod_agencia        = cheque_emissao_recibo_extra.cod_agencia
                    AND cheque_emissao.cod_conta_corrente = cheque_emissao_recibo_extra.cod_conta_corrente
                    AND cheque_emissao.num_cheque         = cheque_emissao_recibo_extra.num_cheque
                    AND cheque_emissao.timestamp_emissao  = cheque_emissao_recibo_extra.timestamp_emissao
                  WHERE NOT EXISTS ( SELECT 1
                                       FROM tesouraria.cheque_emissao_anulada
                                      WHERE cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
                                        AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
                                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
                                        AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
                                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
                                   )
               GROUP BY cheque_emissao_recibo_extra.exercicio
                      , cheque_emissao_recibo_extra.cod_entidade
                      , cheque_emissao_recibo_extra.cod_recibo_extra
                      , cheque_emissao_recibo_extra.tipo_recibo
               ) AS cheque_emissao_recibo_extra
            ON recibo_extra.exercicio        = cheque_emissao_recibo_extra.exercicio
           AND recibo_extra.cod_entidade     = cheque_emissao_recibo_extra.cod_entidade
           AND recibo_extra.cod_recibo_extra = cheque_emissao_recibo_extra.cod_recibo_extra
           AND recibo_extra.tipo_recibo      = cheque_emissao_recibo_extra.tipo_recibo
         WHERE NOT EXISTS ( SELECT 1
                              FROM tesouraria.recibo_extra_anulacao
                             WHERE recibo_extra.exercicio        = recibo_extra_anulacao.exercicio
                               AND recibo_extra.cod_entidade     = recibo_extra_anulacao.cod_entidade
                               AND recibo_extra.cod_recibo_extra = recibo_extra_anulacao.cod_recibo_extra
                               AND recibo_extra.tipo_recibo      = recibo_extra_anulacao.tipo_recibo
                          )
           AND NOT EXISTS ( SELECT 1
                              FROM tesouraria.recibo_extra_transferencia
                             WHERE recibo_extra.exercicio        = recibo_extra_transferencia.exercicio
                               AND recibo_extra.cod_entidade     = recibo_extra_transferencia.cod_entidade
                               AND recibo_extra.cod_recibo_extra = recibo_extra_transferencia.cod_recibo_extra
                               AND recibo_extra.tipo_recibo      = recibo_extra_transferencia.tipo_recibo
                          )
           AND (recibo_extra.valor - COALESCE(cheque_emissao_recibo_extra.valor,0.00)) > 0 ";

        return $stSql;
    }

    /**
     * Método que retorna os cheques vinculados a uma emissao por recibo extra
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
    function listChequesEmissaoReciboExtra(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->recuperaChequesEmissaoReciboExtra().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );
    
        return $obErro;
    }
    
    public function recuperaChequesEmissaoReciboExtra()
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
                 , cheque_emissao_recibo_extra.cod_recibo_extra
                 , cheque_emissao_recibo_extra.exercicio
                 , cheque_emissao_recibo_extra.cod_entidade
                 , cheque_emissao_recibo_extra.tipo_recibo
                 , plano_banco.cod_plano
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

        INNER JOIN tesouraria.cheque_emissao_recibo_extra
                ON cheque_emissao.cod_banco          = cheque_emissao_recibo_extra.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_recibo_extra.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_recibo_extra.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_recibo_extra.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_recibo_extra.timestamp_emissao

        INNER JOIN monetario.conta_corrente
                ON cheque.cod_conta_corrente  = conta_corrente.cod_conta_corrente
               AND cheque.cod_agencia         = conta_corrente.cod_agencia
               AND cheque.cod_banco           = conta_corrente.cod_banco

        INNER JOIN monetario.agencia
                ON conta_corrente.cod_agencia = agencia.cod_agencia
               AND conta_corrente.cod_banco   = agencia.cod_banco

        INNER JOIN monetario.banco
                ON agencia.cod_banco          = banco.cod_banco

        INNER JOIN contabilidade.plano_banco
                ON conta_corrente.cod_banco          = plano_banco.cod_banco
               AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
               AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
               AND plano_banco.exercicio          = '" . Sessao::getExercicio() . "'
        ";

        return $stSql;
    }
    
    public function __destruct(){}

}
