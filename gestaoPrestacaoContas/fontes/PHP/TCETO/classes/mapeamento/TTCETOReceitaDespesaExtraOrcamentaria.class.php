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

 * Classe de mapeamento
 *
 * Data de Criação: 14/11/2014
 * 
 * @package Urbem
 * 
 * @subpackage Mapeamento
 * 
 * @author Evandro Melos
 * 
 * $Id: TTCETOReceitaDespesaExtraOrcamentaria.class.php 60860 2014-11-19 15:30:17Z carolina $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCETOReceitaDespesaExtraOrcamentaria extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCETOReceitaDespesaExtraOrcamentaria()
    {
        parent::Persistente();
        
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaRecDespExtraOrcamentarias(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)){
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        }
        $stSql = $this->montaRecuperaRecDespExtraOrcamentarias().$stCondicao.$stOrdem;        
        $this->setDebug( $stSql );     
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRecDespExtraOrcamentarias()
    {	

        $stSql = "  SELECT  (SELECT sw_cgm_pj.cnpj
                                FROM orcamento.entidade
                                JOIN sw_cgm
                                    ON sw_cgm.numcgm = entidade.numcgm
                                JOIN sw_cgm_pessoa_juridica AS sw_cgm_pj
                                    ON sw_cgm.numcgm = sw_cgm_pj.numcgm
                                WHERE entidade.exercicio  = '".$this->getDado('exercicio')."'
                                AND entidade.cod_entidade = ".$this->getDado('cod_entidade')."
                            ) AS cod_und_gestora
                            , numero_da_extra_orcamentario 
                            , cod_conta_balancete                 
                            , CASE WHEN cod_tipo = 1 THEN 'D'
                                   WHEN cod_tipo = 2 THEN 'C'
                              END AS identificador_dc                 
                            , SUM(valor) as valor
                            , CASE WHEN cod_tipo = 1 THEN 'D'
                                   WHEN cod_tipo = 2 THEN 'R'
                             END AS identificador_dr                 
                            , LPAD(tipo_movimentacao::varchar,2,'0') as tipo_movimentacao
                            , LPAD(classificacao::varchar,2,'0') as classificacao
                            , CASE WHEN cod_recurso IS NULL THEN
                                           '0'
                                   ELSE
                                           cod_recurso 
                            END as cod_recurso
                            , timestamp_transferencia
                    FROM(
                            SELECT  
                                    transferencia.cod_lote as numero_da_extra_orcamentario 
                                    , REPLACE(plano_conta.cod_estrutural,'.','') AS cod_conta_balancete
                                    , transferencia.cod_tipo                 
                                    , transferencia.valor                 
                                    , CASE WHEN transferencia.cod_tipo = 1 AND transferencia_estornada.cod_lote_estorno is null THEN 1
                                           WHEN transferencia.cod_tipo = 2 AND transferencia_estornada.cod_lote_estorno is null THEN 2
                                           WHEN transferencia_estornada.cod_lote_estorno is not null THEN 3
                                           ELSE 4
                                      END AS tipo_movimentacao
                                    , plano_banco.cod_banco AS cod_banco_cd
                                    , plano_banco.cod_agencia AS cod_agencia_banco_cd
                                    , plano_banco.cod_conta_corrente AS num_conta_corrente_cd 
                                    , plano_analitica_classificacao.cod_classificacao as classificacao
                                    , plano_recurso.cod_recurso
                                    , TO_CHAR(transferencia.timestamp_transferencia, 'yyyy-mm-dd') as timestamp_transferencia
                            
                            FROM tceto.plano_analitica_classificacao
              
                            JOIN contabilidade.plano_analitica
                                 ON plano_analitica.cod_plano = plano_analitica_classificacao.cod_plano
                                AND plano_analitica.exercicio = plano_analitica_classificacao.exercicio

                            JOIN contabilidade.plano_conta 
                                 ON plano_conta.exercicio = plano_analitica.exercicio 
                                AND plano_conta.cod_conta = plano_analitica.cod_conta 

                            JOIN tesouraria.transferencia
                                 ON transferencia.cod_plano_debito = plano_analitica.cod_plano
                                AND transferencia.exercicio = plano_analitica.exercicio

                            JOIN contabilidade.plano_banco
                                 ON plano_banco.cod_plano = transferencia.cod_plano_credito
                                AND plano_banco.exercicio = transferencia.exercicio

                            LEFT JOIN contabilidade.plano_recurso
                                 ON plano_recurso.exercicio = plano_analitica.exercicio
                                AND plano_recurso.cod_plano = plano_analitica.cod_plano

                            LEFT JOIN tesouraria.transferencia_estornada
                                 ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
                                AND transferencia_estornada.tipo         = transferencia.tipo
                                AND transferencia_estornada.exercicio    = transferencia.exercicio
                                AND transferencia_estornada.cod_lote     = transferencia.cod_lote
        
                            LEFT JOIN tceto.transferencia_tipo_pagamento
                                 ON transferencia_tipo_pagamento.cod_entidade = transferencia.cod_entidade
                                AND transferencia_tipo_pagamento.tipo         = transferencia.tipo
                                AND transferencia_tipo_pagamento.exercicio    = transferencia.exercicio
                                AND transferencia_tipo_pagamento.cod_lote     = transferencia.cod_lote

                            WHERE transferencia.exercicio ='".$this->getDado('exercicio')."'
                            AND transferencia.cod_entidade IN (".$this->getDado('cod_entidade').")
                            AND (transferencia.cod_tipo = 1 OR transferencia.cod_tipo = 2)
                            AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia::date, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."', 'dd/mm/yyyy')  

                        UNION

                            SELECT 
                                     transferencia.cod_lote as numero_da_extra_orcamentario 
                                    , REPLACE(plano_conta.cod_estrutural,'.','') AS cod_conta_balancete
                                    , transferencia.cod_tipo                 
                                    , transferencia.valor                 
                                    , CASE WHEN transferencia.cod_tipo = 1 AND transferencia_estornada.cod_lote_estorno is null THEN 1
                                           WHEN transferencia.cod_tipo = 2 AND transferencia_estornada.cod_lote_estorno is null THEN 2
                                           WHEN transferencia_estornada.cod_lote_estorno is not null THEN 3
                                           ELSE 4
                                      END AS tipo_movimentacao
                                    , plano_banco.cod_banco AS cod_banco_cd
                                    , plano_banco.cod_agencia AS cod_agencia_banco_cd
                                    , plano_banco.cod_conta_corrente AS num_conta_corrente_cd 
                                    , plano_analitica_classificacao.cod_classificacao as classificacao                 
                                    , plano_recurso.cod_recurso
                                    , TO_CHAR(transferencia.timestamp_transferencia, 'yyyy-mm-dd') as timestamp_transferencia
                 
                            FROM tceto.plano_analitica_classificacao
              
                            JOIN contabilidade.plano_analitica
                                 ON plano_analitica.cod_plano = plano_analitica_classificacao.cod_plano
                                AND plano_analitica.exercicio = plano_analitica_classificacao.exercicio

                            JOIN contabilidade.plano_conta 
                                 ON plano_conta.exercicio = plano_analitica.exercicio 
                                AND plano_conta.cod_conta = plano_analitica.cod_conta 

                            JOIN tesouraria.transferencia
                                 ON transferencia.cod_plano_credito = plano_analitica.cod_plano
                                AND transferencia.exercicio = plano_analitica.exercicio

                            JOIN contabilidade.plano_banco
                                 ON plano_banco.cod_plano = transferencia.cod_plano_debito
                                AND plano_banco.exercicio = transferencia.exercicio

                            LEFT JOIN contabilidade.plano_recurso
                                 ON plano_recurso.exercicio = plano_analitica.exercicio
                                AND plano_recurso.cod_plano = plano_analitica.cod_plano

                            LEFT JOIN tesouraria.transferencia_estornada
                                 ON transferencia_estornada.cod_entidade = transferencia.cod_entidade
                                AND transferencia_estornada.tipo         = transferencia.tipo
                                AND transferencia_estornada.exercicio    = transferencia.exercicio
                                AND transferencia_estornada.cod_lote     = transferencia.cod_lote
        
                            LEFT JOIN tceto.transferencia_tipo_pagamento
                                 ON transferencia_tipo_pagamento.cod_entidade = transferencia.cod_entidade
                                AND transferencia_tipo_pagamento.tipo         = transferencia.tipo
                                AND transferencia_tipo_pagamento.exercicio    = transferencia.exercicio
                                AND transferencia_tipo_pagamento.cod_lote     = transferencia.cod_lote

                            WHERE transferencia.exercicio ='".$this->getDado('exercicio')."'
                            AND transferencia.cod_entidade IN (".$this->getDado('cod_entidade').")
                            AND (transferencia.cod_tipo = 1 OR transferencia.cod_tipo = 2)
                            AND TO_DATE(TO_CHAR(transferencia.timestamp_transferencia::date, 'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN to_date('".$this->getDado('dtInicial')."', 'dd/mm/yyyy') AND to_date('".$this->getDado('dtFinal')."', 'dd/mm/yyyy') 
                ) as dados
                GROUP BY cod_und_gestora
                        , numero_da_extra_orcamentario 
                        , cod_conta_balancete
                        , identificador_dc                 
                        , identificador_dr
                        , tipo_movimentacao
                        , cod_banco_cd
                        , cod_agencia_banco_cd
                        , num_conta_corrente_cd
                        , classificacao
                        , cod_recurso
                        , timestamp_transferencia
                ORDER BY cod_conta_balancete
            ";

        return $stSql;

    }

}
