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
    * Classe de mapeamento
    * Data de Criação: 07/05/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGOPFR.class.php 65168 2016-04-29 16:36:09Z michel $

    * Casos de uso: uc-06.04.00
*/

class TTGOPFR extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSQL = "
              SELECT '10' AS tipo_registro
                    , despesa.num_orgao
                    , CASE WHEN despesa.exercicio <= '2001' THEN
                                lpad( despesa.num_unidade::text    , 4, '0' ) ||
                                lpad( despesa.cod_funcao::text     , 2, '0' ) ||
                                lpad( despesa.cod_programa::text   , 2, '0' ) ||
                                lpad( ''                           , 3, '0' ) ||
                                lpad( despesa.num_pao::text        , 4, '0' ) ||
                                substr ( replace ( RP_PG.cod_estrutural,'.','') , 0 , 7 )
                           ELSE
                                NULL
                      END AS dotacao2001
                    , CASE WHEN despesa.exercicio > '2002' THEN
                                lpad( despesa.cod_programa::text   , 4, '0' ) ||
                                lpad( despesa.num_unidade::text    , 2, '0' ) ||
                                lpad( despesa.cod_funcao::text     , 2, '0' ) ||
                                lpad( despesa.cod_subfuncao::text  , 3, '0' ) ||
                                lpad( despesa.num_pao::text        , 4, '0' ) ||
                                substr ( replace ( RP_PG.cod_estrutural,'.','') , 0 , 7 ) ||
                                lpad( ''                           , 2, '0' )
                           ELSE
                                NULL
                      END AS dotacao2002
                    , empenho.cod_empenho
                    , to_char(empenho.dt_empenho, 'dd/mm/yyyy') as dt_empenho
                    , RP_NL.entidade
                    , sw_cgm.nom_cgm AS credor
                    , 1 AS tipo_lancamento
                    , (SELECT * FROM empenho.fn_consultar_valor_empenhado(empenho.exercicio::varchar, empenho.cod_empenho, empenho.cod_entidade)) AS vl_empenhado
                    , restos.valor_processado_exercicios_anteriores + restos.valor_processado_exercicio_anterior AS vl_processado_anterior
                    , (restos.valor_nao_processado_exercicios_anteriores + restos.valor_nao_processado_exercicio_anterior) - restos.valor_nao_processado_cancelado AS vl_nao_processado_anterior
                    , RP_NL.valor AS vl_processado_inscricao
                    , ((restos.valor_nao_processado_exercicios_anteriores + restos.valor_nao_processado_exercicio_anterior) - restos.valor_nao_processado_cancelado) - RP_NL.valor AS vl_nao_processado_inscricao
                    , restos.valor_processado_pago
                    , restos.valor_nao_processado_pago
                    , restos.valor_processado_cancelado
                    , restos.valor_nao_processado_cancelado
                    , 0.00 valor_processado_atribuicao
                    , 0.00 valor_nao_processado_atribuicao
                    , 0.00 valor_processado_encampacao
                    , 0.00 valor_nao_processado_encampacao
                    , restos.valor_processado_exercicios_anteriores + restos.valor_processado_exercicio_anterior + RP_NL.valor AS vl_processado_atual
                    , ( ( SELECT * FROM empenho.fn_consultar_valor_empenhado(empenho.exercicio::varchar, empenho.cod_empenho, empenho.cod_entidade) )
                        -
                        ( restos.valor_processado_cancelado + restos.valor_nao_processado_cancelado ) )
                      -
                      ( restos.valor_processado_exercicios_anteriores + restos.valor_processado_exercicio_anterior + RP_NL.valor )
                      AS vl_nao_processado_atual
                    , 0 AS tipo_cancelamento
                    , 0 AS nro_sequencial
                    
                 FROM empenho.empenho
           INNER JOIN ( SELECT * FROM empenho.fn_empenho_restos_pagar_anulado_liquidado_estornoliquidacao
                            ( ''
                            , ''
                            , '01/01/".$this->getDado('exercicio')."'
                            , '31/12/".$this->getDado('exercicio')."'
                            , '".$this->getDado('stEntidades')."'
                            , ''
                            , ''
                            , ''
                            , ''
                            , ''
                            , ''
                            , '".$this->getDado('stEntidades')."'
                            , ''
                            , ''
                            ) AS retorno(                      
                                entidade            INTEGER,                                           
                                empenho             INTEGER,                                           
                                exercicio           CHARACTER(4),                                           
                                cgm                 INTEGER,                                           
                                razao_social        VARCHAR,                                           
                                cod_nota            INTEGER,                                           
                                valor               NUMERIC,                                           
                                data                TEXT                                           
                            )
                      ) AS RP_NL
                   ON RP_NL.empenho = empenho.cod_empenho
                  AND RP_NL.exercicio = empenho.exercicio
                  AND RP_NL.entidade = empenho.cod_entidade
            LEFT JOIN ( SELECT * FROM empenho.fn_empenho_restos_pagar_pagamento_estorno_credor                              
                            ( ''                      
                            , ''
                            , '01/01/".$this->getDado('exercicio')."'
                            , '31/12/".$this->getDado('exercicio')."'
                            , '".$this->getDado('stEntidades')."'
                            , ''
                            , ''
                            , ''
                            , ''
                            , ''
                            , ''
                            , ''
                            , '1'
                            , ''
                            , 'true'
                            , ''
                            , ''
                            ) AS retorno(      
                                entidade            INTEGER,                             
                                empenho             INTEGER,                             
                                exercicio           CHARACTER(4),                             
                                credor              VARCHAR,                             
                                cod_estrutural      VARCHAR,                             
                                cod_nota            INTEGER,                             
                                data                TEXT,                                
                                conta               INTEGER,                             
                                banco               VARCHAR,                             
                                valor               NUMERIC                              
                            )
                      ) AS RP_PG
                   ON RP_PG.empenho = empenho.cod_empenho
                  AND RP_PG.exercicio = empenho.exercicio
                  AND RP_NL.entidade = empenho.cod_entidade
            LEFT JOIN empenho.pre_empenho
                   ON empenho.exercicio = pre_empenho.exercicio
                  AND empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
            LEFT JOIN empenho.pre_empenho_despesa   
                   ON empenho.exercicio = pre_empenho_despesa.exercicio
                  AND empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
            LEFT JOIN orcamento.despesa
                   ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                  AND pre_empenho_despesa.exercicio = despesa.exercicio
                 JOIN sw_cgm
                   ON pre_empenho.cgm_beneficiario = sw_cgm.numcgm
            LEFT JOIN orcamento.conta_despesa
                   ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                  AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
           INNER JOIN ( SELECT * FROM tcmgo.fn_restos_pagar
                            ( '".$this->getDado('exercicio')."'
                            , '".$this->getDado('stEntidades')."'
                            , '31/12/".$this->getDado('exercicio')."'
                            ) as rp (
                                cod_empenho                                 INTEGER, 
                                cod_entidade                                INTEGER, 
                                exercicio                                   CHARACTER(4), 
                                valor_processado_exercicios_anteriores      NUMERIC,
                                valor_processado_exercicio_anterior         NUMERIC, 
                                valor_processado_cancelado                  NUMERIC, 
                                valor_processado_pago                       NUMERIC,
                                valor_nao_processado_exercicios_anteriores  NUMERIC, 
                                valor_nao_processado_exercicio_anterior     NUMERIC,
                                valor_nao_processado_cancelado              NUMERIC, 
                                valor_nao_processado_pago                   NUMERIC
                            )
                      ) AS restos
                   ON restos.cod_empenho = empenho.cod_empenho
                  AND restos.exercicio = empenho.exercicio

                WHERE restos.valor_nao_processado_pago > 0

             GROUP BY RP_NL.empenho
                    , RP_NL.exercicio
                    , RP_NL.entidade
                    , RP_NL.valor
                    , despesa.num_orgao
                    , dotacao2001
                    , dotacao2002
                    , empenho.cod_empenho
                    , empenho.dt_empenho
                    , empenho.exercicio
                    , empenho.cod_entidade
                    , sw_cgm.nom_cgm
                    , conta_despesa.descricao
                    , restos.valor_processado_exercicios_anteriores
                    , restos.valor_processado_exercicio_anterior
                    , restos.valor_processado_cancelado
                    , restos.valor_processado_pago
                    , restos.valor_nao_processado_exercicios_anteriores
                    , restos.valor_nao_processado_exercicio_anterior
                    , restos.valor_nao_processado_cancelado
                    , restos.valor_nao_processado_pago
              
             ORDER BY RP_NL.empenho, RP_NL.exercicio
        ";

        return $stSQL;
    }
}
