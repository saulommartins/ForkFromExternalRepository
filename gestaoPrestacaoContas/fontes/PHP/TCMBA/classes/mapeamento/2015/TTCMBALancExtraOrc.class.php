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
    * Data de Criação: 28/07/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Arthur Cruz

    $Id $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBALancExtraOrc extends Persistente
    {

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() 
    {
        parent::Persistente(); 
    }

    public function recuperaLancamentoExtraOrcamentario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLancamentoExtraOrcamentario().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLancamentoExtraOrcamentario()
    {
      $stSql = "
                SELECT
                        1 AS tipo_registro
                      , ".$this->getDado('inCodGestora')." AS unidade_gestora
                      , cod_tipo AS tipo_lancamento
                      , documento_credor
                      , nome_credor
                      , SUM(COALESCE(valor_lancamento,0.00)) AS valor_lancamento
                      , data_lancamento
                      , codigo_contabil AS conta_contabil
                      , num_lancamento

                  FROM
                     (
-- DESPESA EXTRA
                SELECT
                        plano_conta.cod_estrutural AS codigo_contabil
                      , transferencia.valor AS valor_lancamento
                      , 2 AS cod_tipo
                      , TO_CHAR(lote.dt_lote ,'ddmmyyyy') AS data_lancamento
                      , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL 
                             THEN sw_cgm_pessoa_fisica.cpf        
                             WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL 
                             THEN sw_cgm_pessoa_juridica.cnpj       
                             ELSE ''       
                      END AS documento_credor 
                      , sw_cgm.nom_cgm AS nome_credor
                      , recibo_extra.cod_recibo_extra AS num_lancamento

                  FROM tesouraria.transferencia

            INNER JOIN contabilidade.plano_analitica
                    ON plano_analitica.cod_plano = transferencia.cod_plano_debito
                   AND plano_analitica.exercicio = transferencia.exercicio

            INNER JOIN contabilidade.plano_conta
                    ON plano_analitica.cod_conta = plano_conta.cod_conta
                   AND plano_analitica.exercicio = plano_conta.exercicio

             LEFT JOIN tesouraria.transferencia_estornada
                    ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
                   AND transferencia_estornada.tipo         = transferencia.tipo
                   AND transferencia_estornada.exercicio    = transferencia.exercicio
                   AND transferencia_estornada.cod_lote     = transferencia.cod_lote

            INNER JOIN contabilidade.lote
                    ON lote.exercicio    = transferencia.exercicio
                   AND lote.cod_entidade = transferencia.cod_entidade
                   AND lote.tipo         = transferencia.tipo
                   AND lote.cod_lote     = transferencia.cod_lote

            INNER JOIN tesouraria.recibo_extra
                    ON recibo_extra.cod_plano = plano_analitica.cod_plano
                   AND recibo_extra.exercicio = plano_analitica.exercicio

             LEFT JOIN tesouraria.recibo_extra_credor
                    ON recibo_extra_credor.cod_entidade     = recibo_extra.cod_entidade
                   AND recibo_extra_credor.exercicio        = recibo_extra.exercicio
                   AND recibo_extra_credor.cod_recibo_extra = recibo_extra.cod_recibo_extra
                   AND recibo_extra_credor.tipo_recibo      = recibo_extra.tipo_recibo
           
             LEFT JOIN sw_cgm
                    ON sw_cgm.numcgm = recibo_extra_credor.numcgm
             
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
             
             LEFT JOIN sw_cgm_pessoa_juridica       
                    ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

                 WHERE transferencia.cod_tipo = 1
                   AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN
                                TO_DATE('".$this->getDado('dtInicio')."'::VARCHAR,'dd/mm/yyyy') AND
                                TO_DATE('".$this->getDado('dtFim')."'::VARCHAR,'dd/mm/yyyy')
                   AND TO_CHAR(transferencia.timestamp_transferencia,'yyyy') = '".$this->getDado('stExercicio')."'
                   AND transferencia.cod_entidade IN (".$this->getDado('stEntidades').")

             UNION ALL
-- RECEITA EXTRA
                SELECT
                        plano_conta.cod_estrutural AS codigo_contabil
                      , transferencia.valor AS valor_lancamento
                      , 1 AS cod_tipo
                      , TO_CHAR(lote.dt_lote ,'ddmmyyyy') AS data_lancamento 
                      , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL 
                             THEN sw_cgm_pessoa_fisica.cpf        
                             WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL 
                             THEN sw_cgm_pessoa_juridica.cnpj       
                             ELSE ''       
                      END AS documento_credor 
                      , sw_cgm.nom_cgm AS nome_credor
                      , recibo_extra.cod_recibo_extra AS num_lancamento

                  FROM tesouraria.transferencia

             LEFT JOIN tesouraria.recibo_extra_transferencia
                    ON recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
                   AND recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                   AND recibo_extra_transferencia.exercicio    = transferencia.exercicio
                   AND recibo_extra_transferencia.tipo         = transferencia.tipo

            INNER JOIN contabilidade.plano_analitica
                    ON plano_analitica.cod_plano = transferencia.cod_plano_credito
                   AND plano_analitica.exercicio = transferencia.exercicio

             LEFT JOIN contabilidade.plano_recurso
                    ON plano_recurso.exercicio = plano_analitica.exercicio
                   AND plano_recurso.cod_plano = plano_analitica.cod_plano

             LEFT JOIN orcamento.recurso('".$this->getDado('stExercicio')."') AS recurso
                    ON recurso.cod_recurso = plano_recurso.cod_recurso
                   AND recurso.exercicio   = plano_recurso.exercicio

            INNER JOIN contabilidade.plano_conta
                    ON plano_conta.cod_conta = plano_analitica.cod_conta
                   AND plano_conta.exercicio = plano_analitica.exercicio

             LEFT JOIN contabilidade.plano_banco
                    ON plano_banco.cod_plano = transferencia.cod_plano_credito
                   AND plano_banco.exercicio = transferencia.exercicio

             LEFT JOIN monetario.conta_corrente
                    ON conta_corrente.cod_banco          = plano_banco.cod_banco
                   AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                   AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente

             LEFT JOIN monetario.banco
                    ON banco.cod_banco = conta_corrente.cod_banco

            INNER JOIN orcamento.entidade
                    ON entidade.cod_entidade = transferencia.cod_entidade
                   AND entidade.exercicio    = transferencia.exercicio

            INNER JOIN sw_cgm as entidade_cgm
                    ON entidade_cgm.numcgm = entidade.numcgm

             LEFT JOIN tesouraria.transferencia_estornada
                    ON transferencia_estornada.exercicio    = transferencia.exercicio
                   AND transferencia_estornada.cod_entidade = transferencia.cod_entidade
                   AND transferencia_estornada.cod_lote     = transferencia.cod_lote
                   AND transferencia_estornada.tipo         = transferencia.tipo

            INNER JOIN tesouraria.recibo_extra
                    ON recibo_extra.cod_plano = plano_analitica.cod_plano
                   AND recibo_extra.exercicio = plano_analitica.exercicio

             LEFT JOIN tesouraria.recibo_extra_credor
                    ON recibo_extra_credor.cod_entidade     = recibo_extra.cod_entidade
                   AND recibo_extra_credor.exercicio        = recibo_extra.exercicio
                   AND recibo_extra_credor.cod_recibo_extra = recibo_extra.cod_recibo_extra
                   AND recibo_extra_credor.tipo_recibo      = recibo_extra.tipo_recibo
           
             LEFT JOIN sw_cgm
                    ON sw_cgm.numcgm = recibo_extra_credor.numcgm
           
             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
           
             LEFT JOIN sw_cgm_pessoa_juridica       
                    ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

            INNER JOIN contabilidade.lote
                    ON lote.exercicio    = transferencia.exercicio
                   AND lote.cod_entidade = transferencia.cod_entidade
                   AND lote.tipo         = transferencia.tipo
                   AND lote.cod_lote     = transferencia.cod_lote

                 WHERE transferencia.cod_tipo = 2
                   AND transferencia.tipo = 'T'
                   AND TO_DATE(timestamp_transferencia::VARCHAR,'yyyy-mm-dd') BETWEEN
                               TO_DATE('".$this->getDado('dtInicio')."'::VARCHAR,'dd/mm/yyyy')AND
                               TO_DATE('".$this->getDado('dtFim')."'::VARCHAR,'dd/mm/yyyy')
                   AND transferencia.cod_entidade IN (".$this->getDado('stEntidades').")
              ) AS retorno

              GROUP BY tipo_registro
                      , unidade_gestora
                      , cod_tipo
                      , documento_credor
                      , nome_credor
                      , data_lancamento
                      , codigo_contabil
                      , num_lancamento

              ORDER BY data_lancamento
        ";
        return $stSql;
    }

}

?>