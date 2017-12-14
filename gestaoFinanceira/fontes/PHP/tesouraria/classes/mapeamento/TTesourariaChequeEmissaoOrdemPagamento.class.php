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
 * Mapeamento da tabela tesouraria.cheque_emissao_ordem_pagamento
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: TTesourariaChequeEmissaoOrdemPagamento.class.php 66369 2016-08-18 20:11:36Z carlos.silva $
 */

include_once CLA_PERSISTENTE;

class TTesourariaChequeEmissaoOrdemPagamento extends Persistente
{
    /**
     * Método Construtor da classe TTesourariaChequeEmissaoOrdemPagamento
     *
     * @author      Analista        Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor   Henrique Boaventura <henrique.boaventura@cnm.org.br>
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela          ('tesouraria.cheque_emissao_ordem_pagamento');
        $this->setCampoCod        ('');
        $this->setComplementoChave('cod_ordem, exercicio, cod_entidade, cod_agencia, cod_banco, cod_conta_corrente, num_cheque');

        $this->AddCampo('cod_ordem'          ,'integer'  , true, ''  ,true , true);
        $this->AddCampo('exercicio'          ,'char'     , true, '4' ,true , true);
        $this->AddCampo('cod_entidade'       ,'integer'  , true, ''  ,true , true);
        $this->AddCampo('cod_agencia'        ,'integer'  , true, ''  ,true , true);
        $this->AddCampo('cod_banco'          ,'integer'  , true, ''  ,true , true);
        $this->AddCampo('cod_conta_corrente' ,'integer'  , true, ''  ,true , true);
        $this->AddCampo('num_cheque'         ,'varchar'  , true, '15',true , true);
        $this->AddCampo('timestamp_emissao'  ,'timestamp', true, ''  ,true , false );
    }

    /**
     * Método que retorna op vinculados com o valor ja emitido de cheques
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
    
    function recuperaChequesOPSaldo(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->getChequesOPSaldo().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function getChequesOPSaldo()
    {
        $stSql = "
        SELECT ordem_pagamento.nom_entidade
             , ordem_pagamento.cod_entidade
             , ordem_pagamento.cod_ordem
             , ordem_pagamento.exercicio
             , ordem_pagamento.nom_credor
             , ((ordem_pagamento.vl_ordem) - COALESCE(cheque_emissao_ordem_pagamento.valor,0)) AS valor
             , COALESCE(ordem_pagamento_retencao.vl_retencao,0) AS vl_retencao
             , ( SELECT TO_CHAR(dt_emissao,'dd/mm/yyyy')
                   FROM empenho.ordem_pagamento AS op
                  WHERE ordem_pagamento.cod_ordem::VARCHAR    = op.cod_ordem::VARCHAR
                    AND ordem_pagamento.exercicio    = op.exercicio
                    AND ordem_pagamento.cod_entidade = op.cod_entidade
               ) AS data_cheque
         FROM  (
        SELECT retorno.entidade AS nom_entidade
             , retorno.cod_entidade
             , split_part(retorno.ordem,'/',1) AS cod_ordem
             , split_part(retorno.ordem,'/',2) AS exercicio
             , retorno.beneficiario AS nom_credor
             , retorno.vl_ordem
          FROM empenho.fn_lista_empenhos_pagar_tesouraria( '" . $this->getDado('stFiltro') . "'
                                         ,'" . $this->getDado('stFiltroOrdem') . "'  , ''
               ) AS retorno( empenho          varchar
                            ,nota             varchar
                            ,adiantamento     varchar
                            ,ordem            varchar
                            ,cod_entidade     integer
                            ,entidade         varchar
                            ,cgm_beneficiario integer
                            ,beneficiario     varchar
                            ,vl_nota          numeric
                            ,vl_ordem         numeric )
                ) AS ordem_pagamento
     LEFT JOIN ( SELECT cheque_emissao_ordem_pagamento.cod_ordem
                      , cheque_emissao_ordem_pagamento.exercicio
                      , cheque_emissao_ordem_pagamento.cod_entidade
                      , SUM(cheque_emissao.valor) AS valor
                   FROM tesouraria.cheque_emissao_ordem_pagamento

             INNER JOIN tesouraria.cheque_emissao
                     ON cheque_emissao_ordem_pagamento.cod_banco          = cheque_emissao.cod_banco
                    AND cheque_emissao_ordem_pagamento.cod_agencia        = cheque_emissao.cod_agencia
                    AND cheque_emissao_ordem_pagamento.cod_conta_corrente = cheque_emissao.cod_conta_corrente
                    AND cheque_emissao_ordem_pagamento.num_cheque         = cheque_emissao.num_cheque
                    AND cheque_emissao_ordem_pagamento.timestamp_emissao  = cheque_emissao.timestamp_emissao

                  WHERE NOT EXISTS ( SELECT 1
                                       FROM tesouraria.cheque_emissao_anulada
                                      WHERE cheque_emissao.cod_banco          = cheque_emissao_anulada.cod_banco
                                        AND cheque_emissao.cod_agencia        = cheque_emissao_anulada.cod_agencia
                                        AND cheque_emissao.cod_conta_corrente = cheque_emissao_anulada.cod_conta_corrente
                                        AND cheque_emissao.num_cheque         = cheque_emissao_anulada.num_cheque
                                        AND cheque_emissao.timestamp_emissao  = cheque_emissao_anulada.timestamp_emissao
                                   )
               GROUP BY cheque_emissao_ordem_pagamento.cod_ordem
                      , cheque_emissao_ordem_pagamento.exercicio
                      , cheque_emissao_ordem_pagamento.cod_entidade
               ) AS cheque_emissao_ordem_pagamento
            ON ordem_pagamento.cod_ordem::VARCHAR = cheque_emissao_ordem_pagamento.cod_ordem::VARCHAR
           AND ordem_pagamento.exercicio = cheque_emissao_ordem_pagamento.exercicio
           AND ordem_pagamento.cod_entidade = cheque_emissao_ordem_pagamento.cod_entidade
     LEFT JOIN ( SELECT SUM(COALESCE(ordem_pagamento_retencao.vl_retencao,0)) AS vl_retencao
                      , ordem_pagamento_retencao.exercicio
                      , ordem_pagamento_retencao.cod_entidade
                      , ordem_pagamento_retencao.cod_ordem
                   FROM empenho.ordem_pagamento_retencao
               GROUP BY ordem_pagamento_retencao.exercicio
                      , ordem_pagamento_retencao.cod_entidade
                      , ordem_pagamento_retencao.cod_ordem
               ) AS ordem_pagamento_retencao
            ON ordem_pagamento.cod_ordem::VARCHAR    = ordem_pagamento_retencao.cod_ordem::VARCHAR
           AND ordem_pagamento.exercicio    = ordem_pagamento_retencao.exercicio
           AND ordem_pagamento.cod_entidade = ordem_pagamento_retencao.cod_entidade
        ";
        
        return $stSql;
    }

    /**
     * Método que retorna os cheques vinculados a uma emissao por OP
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
    function listChequesEmissaoOP(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->recuperaListChequesEmissaoOP($stFiltro);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function recuperaListChequesEmissaoOP( $stFiltro = "" )
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
                 , cheque_emissao_ordem_pagamento.cod_ordem
                 , cheque_emissao_ordem_pagamento.exercicio
                 , cheque_emissao_ordem_pagamento.cod_entidade
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

        INNER JOIN tesouraria.cheque_emissao_ordem_pagamento
                ON cheque_emissao.cod_banco          = cheque_emissao_ordem_pagamento.cod_banco
               AND cheque_emissao.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia
               AND cheque_emissao.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente
               AND cheque_emissao.num_cheque         = cheque_emissao_ordem_pagamento.num_cheque
               AND cheque_emissao.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao

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
                ON cheque_emissao_ordem_pagamento.cod_banco          = plano_banco.cod_banco
               AND cheque_emissao_ordem_pagamento.cod_agencia        = plano_banco.cod_agencia
               AND cheque_emissao_ordem_pagamento.cod_conta_corrente = plano_banco.cod_conta_corrente
               AND cheque_emissao_ordem_pagamento.cod_entidade       = plano_banco.cod_entidade
               AND cheque_emissao_ordem_pagamento.exercicio          = plano_banco.exercicio

        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = plano_banco.cod_plano
               AND plano_analitica.exercicio = plano_banco.exercicio

        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
        ";

        if ($stFiltro != '') {
            $stSql .= " WHERE " . substr($stFiltro,0,-4);
        }

        return $stSql;
    }
    
    function recuperaPorChaveNaoAnulada(&$rsRecordSet, $stFiltro = "",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->getPorChaveNaoAnulada().$stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function getPorChaveNaoAnulada()
    {
        $stSql = " SELECT
                       cheque_emissao_ordem_pagamento.*
                     FROM
                       tesouraria.cheque_emissao_ordem_pagamento
                    WHERE
                       NOT EXISTS ( SELECT 1
                                      FROM tesouraria.cheque_emissao_anulada
                                     WHERE     cheque_emissao_anulada.cod_agencia        = cheque_emissao_ordem_pagamento.cod_agencia
                                           AND cheque_emissao_anulada.cod_banco          = cheque_emissao_ordem_pagamento.cod_banco
                                           AND cheque_emissao_anulada.cod_conta_corrente = cheque_emissao_ordem_pagamento.cod_conta_corrente
                                           AND cheque_emissao_anulada.num_cheque         = cheque_emissao_ordem_pagamento.num_cheque
                                           AND cheque_emissao_anulada.timestamp_emissao  = cheque_emissao_ordem_pagamento.timestamp_emissao
                                  )
                       AND cheque_emissao_ordem_pagamento.cod_ordem = ".$this->getDado('cod_ordem')."
                       AND cheque_emissao_ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
                       AND cheque_emissao_ordem_pagamento.cod_entidade = ".$this->getDado('cod_entidade');

        return $stSql;
    }

    public function __destruct(){}

}
