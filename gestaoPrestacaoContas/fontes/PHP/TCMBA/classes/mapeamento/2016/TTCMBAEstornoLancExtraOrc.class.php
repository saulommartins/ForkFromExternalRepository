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
    * Data de Criação: 01/09/2015

    * @author Analista: Valtair Santos
    * @author Desenvolvedor: Michel Teixeira

    $Id: TTCMBAEstornoLancExtraOrc.class.php 63488 2015-09-01 18:54:44Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAEstornoLancExtraOrc extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct() 
    {
        parent::Persistente(); 
    }

    public function recuperaEstornoLancamentoExtraOrcamentario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaEstornoLancamentoExtraOrcamentario().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEstornoLancamentoExtraOrcamentario()
    {
        $stSql = " SELECT 1 AS tipo_registro 
                        , ".$this->getDado('inCodGestora')." AS unidade_gestora
                        , transferencia.cod_lote AS num_lancamento
                        , TO_CHAR(lote.dt_lote ,'yyyymm') AS competencia
                        , TO_CHAR(lote.dt_lote ,'ddmmyyyy') AS data_lancamento 
                        , REPLACE(plano_conta.cod_estrutural::VARCHAR,'.','') AS conta_contabil
                        , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                    THEN sw_cgm_pessoa_fisica.cpf
                               WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                    THEN sw_cgm_pessoa_juridica.cnpj       
                               ELSE ''       
                          END AS documento_credor 
                        , CASE WHEN transferencia.cod_tipo = 1
                                    THEN 2
                               ELSE 1
                          END AS tipo_lancamento
                        , REPLACE(transferencia_estornada.valor::VARCHAR, '.', '') AS valor_estornado
                        , transferencia_estornada.cod_lote_estorno AS num_estorno
                        , TO_CHAR(transferencia_estornada.timestamp_estornada ,'ddmmyyyy') AS data_estorno 

                     FROM tesouraria.transferencia

               INNER JOIN tesouraria.transferencia_estornada
                       ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
                      AND transferencia_estornada.tipo = transferencia.tipo
                      AND transferencia_estornada.exercicio = transferencia.exercicio
                      AND transferencia_estornada.cod_lote = transferencia.cod_lote

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

                LEFT JOIN orcamento.recurso('2015') AS recurso
                       ON recurso.cod_recurso = plano_recurso.cod_recurso
                      AND recurso.exercicio  = plano_recurso.exercicio

               INNER JOIN contabilidade.plano_conta
                       ON plano_conta.cod_conta  = plano_analitica.cod_conta
                      AND plano_conta.exercicio = plano_analitica.exercicio

                LEFT JOIN contabilidade.plano_banco
                       ON plano_banco.cod_plano  = transferencia.cod_plano_credito
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

               INNER JOIN contabilidade.lote
                       ON lote.exercicio    = transferencia.exercicio
                      AND lote.cod_entidade = transferencia.cod_entidade 
                      AND lote.tipo         = transferencia.tipo
                      AND lote.cod_lote     = transferencia.cod_lote

                LEFT JOIN tesouraria.transferencia_credor
                       ON transferencia_credor.cod_lote     = transferencia.cod_lote
                      AND transferencia_credor.cod_entidade = transferencia.cod_entidade
                      AND transferencia_credor.exercicio    = transferencia.exercicio
                      AND transferencia_credor.tipo         = transferencia.tipo

                LEFT JOIN sw_cgm
                       ON sw_cgm.numcgm = transferencia_credor.numcgm

                LEFT JOIN sw_cgm_pessoa_fisica
                       ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm

                LEFT JOIN sw_cgm_pessoa_juridica       
                       ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm

               INNER JOIN (
                         SELECT plano_analitica.cod_plano || ' - ' || plano_conta.nom_conta as nome_conta_banco
                              , plano_analitica.cod_plano as cod_plano_banco
                              , plano_analitica.exercicio 

                           FROM contabilidade.plano_conta

                     INNER JOIN contabilidade.plano_analitica
                             ON plano_conta.cod_conta = plano_analitica.cod_conta
                            AND plano_conta.exercicio = plano_analitica.exercicio
                          ) AS conta_banco
                       ON conta_banco.cod_plano_banco = transferencia.cod_plano_debito
                      AND conta_banco.exercicio       = transferencia.exercicio

                    WHERE transferencia.cod_tipo IN(1,2)
                      AND transferencia.tipo      = 'T'
                      AND transferencia.exercicio = '".$this->getDado('stExercicio')."'
                      AND TO_DATE(timestamp_transferencia::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                                     AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                 ORDER BY transferencia.cod_tipo ";

        return $stSql;
    }
}

?>