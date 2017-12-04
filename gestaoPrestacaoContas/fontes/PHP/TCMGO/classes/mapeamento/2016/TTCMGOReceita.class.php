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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGORecita.class.php 63598 2015-09-15 19:15:26Z franver $

    * Casos de uso: uc-06.04.00
*/

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php" );

class TTCMGOReceita extends TOrcamentoContaReceita
{

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::TOrcamentoContaReceita();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }


    public function recuperaRegistro10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro10()
    {

        $stSQL = "
                  SELECT 10 AS tipo_registro
                       , orgao_plano_banco.num_orgao AS cod_orgao
                       , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                              THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                              ELSE '0' || SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,8)
                         END AS rubrica
                       , conta_receita.descricao AS especificacao
                       , CASE WHEN receita.vl_original < 0.00
                                THEN (receita.vl_original * -1)
                              ELSE receita.vl_original
                         END AS vl_previsto
                       , CASE WHEN receita.vl_original < 0.00
                                 THEN (receita.vl_original * -1)
                              ELSE receita.vl_original
                         END AS vl_atualizado
                       , ABS(SUM(COALESCE(arrecadacao_receita.vl_arrecadacao,0.00))) AS vl_arrecadado
                      
                    FROM orcamento.receita
              
              INNER JOIN orcamento.conta_receita
                      ON receita.exercicio = conta_receita.exercicio
                     AND receita.cod_conta = conta_receita.cod_conta
                    
              INNER JOIN tesouraria.arrecadacao_receita
                      ON receita.cod_receita = arrecadacao_receita.cod_receita
                     AND receita.exercicio   = arrecadacao_receita.exercicio
              
              INNER JOIN tesouraria.arrecadacao
                      ON arrecadacao_receita.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                     AND arrecadacao_receita.exercicio             = arrecadacao.exercicio
                     AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
              
              INNER JOIN contabilidade.plano_analitica
                      ON arrecadacao.cod_plano = plano_analitica.cod_plano
                     AND arrecadacao.exercicio = plano_analitica.exercicio
              
              INNER JOIN tcmgo.orgao_plano_banco
                      ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                     AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                     
               LEFT JOIN tesouraria.arrecadacao_estornada_receita
		                  ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		                 AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		                 AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		                 AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                       
                   WHERE arrecadacao_receita.exercicio = '".Sessao::getExercicio() ."'
                     AND receita.cod_entidade IN (".$this->getDado ( 'stEntidades' ).") 

                GROUP BY tipo_registro
                       , cod_orgao
                       , rubrica
                       , conta_receita.descricao
                       , receita.vl_original
              ";

        return $stSQL;
    }

    public function recuperaRegistro11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro11()
    {
        $stSQL = "
                  SELECT tipo_registro
                       , cod_orgao
                       , rubrica
                       , cod_fonte_recurso
                       , det_fonte_recurso
                       , ABS(vl_previsto) AS vl_previsto
                       , ABS(vl_atualizado) AS vl_atualizado
                       , ABS(SUM(vl_arrecadacao_mes)) AS vl_arrecadado
                       , '' AS brancos

                    FROM (
                          SELECT 11 AS tipo_registro
                               , orgao_plano_banco.num_orgao AS cod_orgao
                               , CASE WHEN SUBSTR(conta_receita.cod_estrutural::VARCHAR, 1, 1)::INTEGER = 9
                                        THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                                        ELSE '0' || substr(REPLACE(conta_receita.cod_estrutural,'.',''),1,8)
                                 END AS rubrica
                               , receita.vl_original AS vl_previsto
                               , receita.vl_original AS vl_atualizado
                               , ABS(SUM(COALESCE(arrecadacao_receita.vl_arrecadacao,0.00))) AS vl_arrecadacao_mes
                               , '000' AS cod_fonte_recurso
                               , '000' AS det_fonte_recurso

                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                       
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio             = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao

                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
		                ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		               AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		               AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		               AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio

                INNER JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio = receita.exercicio
                
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                       
                     WHERE arrecadacao.devolucao = FALSE
                       AND receita.cod_entidade IN (".$this->getDado( 'stEntidades' ).")
                       AND arrecadacao.exercicio = '".Sessao::getExercicio() ."'
                       
                  GROUP BY tipo_registro
                         , cod_orgao
                         , rubrica
                         , receita.vl_original
                         , recurso.cod_fonte

                UNION

                    SELECT 11 AS tipo_registro
                         , orgao_plano_banco.num_orgao AS cod_orgao
                         , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                                  THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                                ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END AS rubrica
                         , receita.vl_original AS vl_previsto
                         , receita.vl_original AS vl_atualizado
                         , ABS(ROUND(SUM(arrecadacao_receita.vl_arrecadacao), 2)) * -1 AS vl_arrecadado
                         , '000' AS cod_fonte_recurso
                         , '000' AS det_fonte_recurso

                      
                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                      
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                      
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio             = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
                      
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                      
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                      
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                      
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                      
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                      
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                      
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio

                INNER JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio = receita.exercicio
                        
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                      
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                           ) AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                     
                     WHERE arrecadacao.devolucao = TRUE
                       AND receita.cod_entidade IN (".$this->getDado('stEntidades').")
                       AND arrecadacao.exercicio = '".Sessao::getExercicio() ."'
                       
                  GROUP BY tipo_registro
                         , cod_orgao
                         , rubrica
                         , receita.vl_original
                         , recurso.cod_fonte
            ) AS tabela
     GROUP BY tipo_registro
            , cod_orgao
            , rubrica
            , vl_atualizado
            , vl_previsto
            , cod_fonte_recurso
            , det_fonte_recurso
         ";
        return $stSQL;
    }

    public function recuperaRegistro12 (&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro12",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRegistro12()
    {
        $stSQL = "
                  SELECT tipo_registro
                       , cod_orgao
                       , rubrica
                       , cod_fonte_recurso
                       , det_fonte_recurso
                       , ABS(vl_previsto) AS vl_previsto
                       , ABS(vl_atualizado) AS vl_atualizado
                       , ABS(SUM(vl_arrecadacao_mes)) AS vl_arrecadado
                       ,'' AS brancos

                    FROM (
                          SELECT 12 AS tipo_registro
                               , orgao_plano_banco.num_orgao AS cod_orgao
                               , CASE WHEN SUBSTR(conta_receita.cod_estrutural::VARCHAR, 1, 1)::INTEGER = 9
                                        THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                                      ELSE '0' || substr(REPLACE(conta_receita.cod_estrutural,'.',''),1,8)
                                 END AS rubrica
                               , receita.vl_original AS vl_previsto
                               , receita.vl_original AS vl_atualizado
                               , ABS(SUM(COALESCE(arrecadacao_receita.vl_arrecadacao,0.00))) AS vl_arrecadacao_mes
                               , '000' AS cod_fonte_recurso
                               , '000' AS det_fonte_recurso

                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                       
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio             = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao

                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
		                ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		               AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		               AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		               AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio

                INNER JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio = receita.exercicio
                
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                       
                     WHERE arrecadacao.devolucao = FALSE
                       AND receita.cod_entidade IN (".$this->getDado( 'stEntidades' ).")
                       
                  GROUP BY tipo_registro
                         , cod_orgao
                         , rubrica
                         , receita.vl_original
                         , recurso.cod_fonte

                UNION

                    SELECT 12 AS tipo_registro
                         , orgao_plano_banco.num_orgao AS cod_orgao
                         , CASE WHEN SUBSTR(conta_receita.cod_estrutural, 1, 1) = '9'
                                  THEN SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9)
                                ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END AS rubrica
                         , receita.vl_original AS vl_previsto
                         , receita.vl_original AS vl_atualizado
                         , ABS(ROUND(SUM(arrecadacao_receita.vl_arrecadacao), 2)) * -1 AS vl_arrecadado
                         , '000' AS cod_fonte_recurso
                         , '000' AS det_fonte_recurso

                      
                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                      
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                      
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao       = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio             = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
                      
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                      
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                      
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                      
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                      
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                      
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                      
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio

                INNER JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio = receita.exercicio
                        
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                      
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                           ) AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                     
                     WHERE arrecadacao.devolucao = TRUE
                       AND receita.cod_entidade IN (".$this->getDado('stEntidades').")
                       
                  GROUP BY tipo_registro
                         , cod_orgao
                         , rubrica
                         , receita.vl_original
                         , recurso.cod_fonte
            ) AS tabela
     GROUP BY tipo_registro
            , cod_orgao
            , rubrica
            , vl_atualizado
            , vl_previsto
            , cod_fonte_recurso
            , det_fonte_recurso
         ";
         
        return $stSQL;
    }
}

?>