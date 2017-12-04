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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento
    
    $Id: TTGOAOP.class.php 61100 2014-12-08 18:56:59Z franver $

    $Revision: 61100 $
    $Name$
    $Author: franver $
    $Date: 2014-12-08 16:56:59 -0200 (Mon, 08 Dec 2014) $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOAOP extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

    public function recuperaAnulacaoOrdemPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaAnulacaoOrdemPagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaAnulacaoOrdemPagamento()
    {
        $stSql = "SELECT * FROM ( SELECT  '10' AS tiporegistro
                                        , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                                               THEN (CASE WHEN NOT pre_empenho.implantado THEN
                                                                LPAD(programa.num_programa::varchar, 4, '0')
                                                          ELSE
                                                                LPAD(restos_pre_empenho.cod_programa::varchar , 02, '0')
                                                    END)
                                               ELSE '0000'
                                        END AS codprograma
                                        , LPAD(despesa.num_orgao::varchar, 2, '0') AS codorgao
                                        , LPAD(despesa.num_unidade::varchar, 2, '0') AS codunidade
                                        , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                                               THEN (CASE WHEN NOT pre_empenho.implantado THEN
                                                                LPAD(despesa.cod_funcao::varchar, 2, '0')
                                                          ELSE
                                                                LPAD(restos_pre_empenho.cod_funcao::varchar, 02, '0')
                                                          END)
                                                     ELSE '00'
                                        END AS codfuncao
                                        , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                                               THEN (CASE WHEN NOT pre_empenho.implantado THEN
                                                                LPAD(despesa.cod_subfuncao::varchar,3, '0')
                                                          ELSE
                                                                '000'
                                                    END)
                                               ELSE '000'
                                        END AS codsubfuncao
                                        , LPAD((SUBSTR(acao.num_acao::varchar,1,1)), 6, '0')   AS naturezaacao
                                        , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS nroprojativ
                                        , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elementodespesa
                                        , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                                               THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                                               ELSE
                                                    '00'
                                        END AS subelemento
                                        , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                                                   LPAD(restos_pre_empenho.num_unidade::varchar  , 04, '0')
                                                || LPAD(restos_pre_empenho.cod_funcao::varchar   , 02, '0')
                                                || LPAD(restos_pre_empenho.cod_programa::varchar , 02, '0')
                                                || '000'
                                                || LPAD(restos_pre_empenho.num_pao::varchar      , 06, '0')
                                                || SUBSTR(REPLACE( restos_pre_empenho.cod_estrutural::varchar, '.', ''), 1, 6)
                                          ELSE LPAD('', 21, '0')
                                          END AS dotorigp2001
                                        , LPAD(nota_liquidacao.cod_empenho::varchar, 6, '0') AS nroempenho
                                        , ordem_pagamento.cod_ordem AS nroop
                                        , TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'ddmmyyyy')  AS dtAnulacao
                                        , ordem_pagamento.cod_ordem AS nranulacaoop -- por enquanto numero da OP
                                        /*, tc.numero_pagamento_empenho(ordem_pagamento_liquidacao_anulada.exercicio, ordem_pagamento_liquidacao_anulada.cod_entidade, ordem_pagamento_liquidacao_anulada.cod_ordem, ordem_pagamento_liquidacao_anulada.timestamp) AS  nranulacaoop*/
                                        , 2 as tipoop
                                        , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') AS dtinscricao
                                        , TO_CHAR(ordem_pagamento.dt_emissao,'ddmmyyyy') AS dtemissao
                                        , COALESCE(SUM(pagamento_liquidacao.vl_pagamento),0) AS vlop
                                        , nota_liquidacao_paga_anulada.vl_anulado AS vlanuladoop
                                        , sw_cgm.nom_cgm as nomecredor \n";

        if (Sessao::getExercicio() > 2012) {
          $stSql .= "                      , CASE WHEN sw_cgm.cod_pais <> 1 THEN
                                                 3
                                            WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN
                                                 1
                                            ELSE
                                                 2
                                            END AS tipocredor \n";
        } else {
          $stSql .= "                      , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN
                                                 1
                                            ELSE
                                                 2
                                            END AS tipocredor \n";
        }
        $stSql .= "                      , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                               THEN LPAD(sw_cgm_pessoa_fisica.cpf, 14, '0')
                                               ELSE
                                                    LPAD(sw_cgm_pessoa_juridica.cnpj, 14, '0')
                                        END AS cpfcnpj
                                        , sem_acentos(ordem_pagamento.observacao) as especificacaoop
                                        , 0 AS numero_sequencial
                                        , 0 AS nrextraorcamentaria

                                    FROM empenho.nota_liquidacao_paga

                                    JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                      ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                                     AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                                     AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                                     AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

                               LEFT JOIN (SELECT exercicio
                                                , cod_entidade
                                                , cod_nota
                                                , SUM(COALESCE(vl_anulado,0)) AS vl_anulado
                                                , MAX(timestamp) AS timestamp
                                                , MAX(timestamp_anulada) AS timestamp_anulada
                                                , observacao
                                            FROM empenho.nota_liquidacao_paga_anulada
                                            GROUP BY exercicio
                                                    , cod_entidade
                                                    , cod_nota
                                                    , observacao
                                        ) AS nota_liquidacao_paga_anulada
                                      ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                     AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                     AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                     AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp

                                    JOIN empenho.pagamento_liquidacao
                                      ON pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
                                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
                                     AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio

                                    JOIN empenho.ordem_pagamento
                                      ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                     AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                     AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

                               LEFT JOIN empenho.ordem_pagamento_liquidacao_anulada
                                      ON ordem_pagamento_liquidacao_anulada.exercicio = pagamento_liquidacao.exercicio
                                     AND ordem_pagamento_liquidacao_anulada.cod_entidade = pagamento_liquidacao.cod_entidade
                                     AND ordem_pagamento_liquidacao_anulada.cod_ordem = pagamento_liquidacao.cod_ordem
                                     AND ordem_pagamento_liquidacao_anulada.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                                     AND ordem_pagamento_liquidacao_anulada.cod_nota = pagamento_liquidacao.cod_nota

                                    JOIN empenho.nota_liquidacao
                                      ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                     AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                     AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

                                    JOIN empenho.empenho
                                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho

                                    JOIN empenho.pre_empenho
                                      ON pre_empenho.exercicio       = empenho.exercicio
                                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                               LEFT JOIN empenho.restos_pre_empenho
                                      ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                     AND restos_pre_empenho.exercicio       = pre_empenho.exercicio

                               LEFT JOIN sw_cgm
                                      ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                               LEFT JOIN sw_cgm_pessoa_fisica
                                     ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario

                               LEFT JOIN sw_cgm_pessoa_juridica
                                      ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario

                                    JOIN empenho.pre_empenho_despesa
                                      ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                                     AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

                                    JOIN orcamento.despesa
                                      ON despesa.exercicio   = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                              INNER JOIN orcamento.pao
                                      ON pao.num_pao = despesa.num_pao
                                     AND pao.exercicio = despesa.exercicio
                              
                              INNER JOIN orcamento.pao_ppa_acao
                                      ON pao_ppa_acao.exercicio = pao.exercicio
                                     AND pao_ppa_acao.num_pao = pao.num_pao
                            
                              INNER JOIN ppa.acao
                                      ON acao.cod_acao = pao_ppa_acao.cod_acao

                                    JOIN orcamento.programa AS o_programa
                                      ON o_programa.exercicio = despesa.exercicio
                                     AND o_programa.cod_programa = despesa.cod_programa
                                     
                                    JOIN orcamento.programa_ppa_programa
                                      ON programa_ppa_programa.exercicio = o_programa.exercicio
                                     AND programa_ppa_programa.cod_programa = o_programa.cod_programa

                                    JOIN ppa.programa
                                      ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

                                    JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

                               LEFT JOIN tcmgo.elemento_de_para
                                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                                     AND elemento_de_para.exercicio = conta_despesa.exercicio

                                   WHERE ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
                                     AND ordem_pagamento.cod_entidade IN (".$this->getDado('cod_entidade').")
                                     AND (TO_CHAR(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                                     AND TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                                GROUP BY nota_liquidacao.exercicio_empenho
                                       , pre_empenho.implantado
                                       , programa.num_programa
                                       , restos_pre_empenho.cod_programa
                                       , despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_funcao
                                       , restos_pre_empenho.cod_funcao
                                       , despesa.cod_subfuncao
                                       , acao.num_acao
                                       , conta_despesa.cod_estrutural
                                       , elemento_de_para.estrutural
                                       , restos_pre_empenho.num_unidade
                                       , restos_pre_empenho.num_pao
                                       , restos_pre_empenho.cod_estrutural
                                       , nota_liquidacao.cod_empenho
                                       , ordem_pagamento.cod_ordem
                                       , empenho.dt_empenho
                                       , ordem_pagamento.dt_emissao
                                       , sw_cgm.nom_cgm
                                       , sw_cgm_pessoa_fisica.cpf
                                       , sw_cgm_pessoa_juridica.cnpj
                                       , ordem_pagamento.observacao
                                       , nota_liquidacao_paga_anulada.vl_anulado
                                       , nota_liquidacao_paga_anulada.timestamp_anulada
                                       , sw_cgm.cod_pais
                                ) AS registros";

        return $stSql;
    }

    public function recuperaLiquidacaoOrdemPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaLiquidacaoOrdemPagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLiquidacaoOrdemPagamento()
    {
        $stSql = "SELECT * FROM ( SELECT  '11' AS tiporegistro
                                        , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                                               THEN (CASE WHEN NOT pre_empenho.implantado THEN
                                                                LPAD(programa.num_programa::varchar, 4, '0')
                                                          ELSE
                                                                LPAD(restos_pre_empenho.cod_programa::varchar , 02, '0')
                                                    END)
                                               ELSE '0000'
                                        END AS codprograma
                                        , LPAD(despesa.num_orgao::varchar, 2, '0') AS codorgao
                                        , LPAD(despesa.num_unidade::varchar, 2, '0') AS codunidade
                                        , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                                               THEN (CASE WHEN NOT pre_empenho.implantado THEN
                                                                LPAD(despesa.cod_funcao::varchar, 2, '0')
                                                          ELSE
                                                                LPAD(restos_pre_empenho.cod_funcao::varchar, 02, '0')
                                                          END)
                                                     ELSE '00'
                                        END AS codfuncao
                                        , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                                               THEN (CASE WHEN NOT pre_empenho.implantado THEN
                                                                LPAD(despesa.cod_subfuncao::varchar,3, '0')
                                                          ELSE
                                                                '000'
                                                    END)
                                               ELSE '000'
                                        END AS codsubfuncao
                                        , LPAD((SUBSTR(acao.num_acao::varchar,1,1)), 6, '0')   AS naturezaacao
                                        , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS nroprojativ
                                        , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elementodespesa
                                        , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                                               THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                                               ELSE
                                                    '00'
                                        END AS subelemento
                                        , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                                                   LPAD(restos_pre_empenho.num_unidade::varchar  , 04, '0')
                                                || LPAD(restos_pre_empenho.cod_funcao::varchar   , 02, '0')
                                                || LPAD(restos_pre_empenho.cod_programa::varchar , 02, '0')
                                                || '000'
                                                || LPAD(restos_pre_empenho.num_pao::varchar      , 06, '0')
                                                || SUBSTR(REPLACE( restos_pre_empenho.cod_estrutural::varchar, '.', ''), 1, 6)
                                          ELSE LPAD('', 21, '0')
                                          END AS dotorigp2001
                                        , LPAD(nota_liquidacao.cod_empenho::varchar, 6, '0') AS nroempenho
                                        , ordem_pagamento.cod_ordem AS nroop
                                        , TO_CHAR(nota_liquidacao_paga_anulada.timestamp_anulada,'ddmmyyyy')  AS dtAnulacao
                                        , ordem_pagamento.cod_ordem AS nranulacaoop
                                        --, tc.numero_pagamento_empenho(ordem_pagamento_anulada.exercicio, ordem_pagamento_anulada.cod_entidade, ordem_pagamento_anulada.cod_ordem, ordem_pagamento_anulada.timestamp) AS  nranulacaoop
                                        , TO_CHAR(nota_liquidacao.dt_liquidacao, 'ddmmyyyy') AS dtliquidacao
                                        , TCMGO.numero_nota_liquidacao('2014',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho) AS nrliquidacao
                                        , nota_liquidacao_paga_anulada.vl_anulado AS vlanulacao
                                        , 0 AS numero_sequencial

                                    FROM empenho.nota_liquidacao_paga

                                    JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                                      ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                                     AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                                     AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                                     AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

                               LEFT JOIN (SELECT exercicio
                                                , cod_entidade
                                                , cod_nota
                                                , SUM(COALESCE(vl_anulado,0)) AS vl_anulado
                                                , MAX(timestamp) AS timestamp
                                                , MAX(timestamp_anulada) AS timestamp_anulada
                                                , observacao
                                            FROM empenho.nota_liquidacao_paga_anulada
                                            GROUP BY exercicio
                                                    , cod_entidade
                                                    , cod_nota
                                                    , observacao
                                        ) AS nota_liquidacao_paga_anulada
                                      ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                                     AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                                     AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                                     AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp

                                    JOIN empenho.pagamento_liquidacao
                                      ON pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
                                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
                                     AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio

                                    JOIN empenho.ordem_pagamento
                                      ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                                     AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                                     AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

                                    JOIN empenho.nota_liquidacao
                                      ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                                     AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                                     AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

                                    JOIN empenho.empenho
                                      ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                                     AND empenho.cod_empenho = nota_liquidacao.cod_empenho

                                    JOIN empenho.pre_empenho
                                      ON pre_empenho.exercicio       = empenho.exercicio
                                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

                               LEFT JOIN empenho.restos_pre_empenho
                                      ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                     AND restos_pre_empenho.exercicio       = pre_empenho.exercicio

                               LEFT JOIN sw_cgm
                                      ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

                               LEFT JOIN sw_cgm_pessoa_fisica
                                     ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario

                               LEFT JOIN sw_cgm_pessoa_juridica
                                      ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario

                                    JOIN empenho.pre_empenho_despesa
                                      ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                                     AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

                                    JOIN orcamento.despesa
                                      ON despesa.exercicio   = pre_empenho_despesa.exercicio
                                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                              INNER JOIN orcamento.pao
                                      ON pao.num_pao = despesa.num_pao
                                     AND pao.exercicio = despesa.exercicio
                              
                              INNER JOIN orcamento.pao_ppa_acao
                                      ON pao_ppa_acao.exercicio = pao.exercicio
                                     AND pao_ppa_acao.num_pao = pao.num_pao
                            
                              INNER JOIN ppa.acao
                                      ON acao.cod_acao = pao_ppa_acao.cod_acao

                                    JOIN orcamento.programa AS o_programa
                                      ON o_programa.exercicio = despesa.exercicio
                                     AND o_programa.cod_programa = despesa.cod_programa
                                     
                                    JOIN orcamento.programa_ppa_programa
                                      ON programa_ppa_programa.exercicio = o_programa.exercicio
                                     AND programa_ppa_programa.cod_programa = o_programa.cod_programa

                                    JOIN ppa.programa
                                      ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

                                    JOIN orcamento.conta_despesa
                                      ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                                     AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

                               LEFT JOIN tcmgo.elemento_de_para
                                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                                     AND elemento_de_para.exercicio = conta_despesa.exercicio

                                   WHERE ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
                                     AND ordem_pagamento.cod_entidade IN (".$this->getDado('cod_entidade').")
                                     AND (TO_CHAR(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                                     AND TO_DATE(nota_liquidacao_paga_anulada.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                                GROUP BY nota_liquidacao.exercicio_empenho
                                       , pre_empenho.implantado
                                       , programa.num_programa
                                       , restos_pre_empenho.cod_programa
                                       , despesa.num_orgao
                                       , despesa.num_unidade
                                       , despesa.cod_funcao
                                       , restos_pre_empenho.cod_funcao
                                       , despesa.cod_subfuncao
                                       , acao.num_acao
                                       , conta_despesa.cod_estrutural
                                       , elemento_de_para.estrutural
                                       , restos_pre_empenho.num_unidade
                                       , restos_pre_empenho.num_pao
                                       , restos_pre_empenho.cod_estrutural
                                       , nota_liquidacao.cod_empenho
                                       , ordem_pagamento.cod_ordem
                                       , empenho.dt_empenho
                                       , ordem_pagamento.dt_emissao
                                       , sw_cgm.nom_cgm
                                       , sw_cgm_pessoa_fisica.cpf
                                       , sw_cgm_pessoa_juridica.cnpj
                                       , pre_empenho.descricao
                                       , nota_liquidacao_paga_anulada.timestamp_anulada
                                       , nota_liquidacao_paga_anulada.vl_anulado
                                       , nota_liquidacao.dt_liquidacao
                                       , nrliquidacao
                                ) AS registros";
        return $stSql;
    }

    public function recuperaFinanceiraOrdemPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaFinanceiraOrdemPagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaFinanceiraOrdemPagamento()
    {
        $stSql = "
            SELECT '12' AS tiporegistro
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE LPAD(rpe.cod_programa::varchar , 02, '0')
                               END)
                        ELSE '0000'
                    END AS codprograma
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD(despesa.num_orgao::varchar, 2, '0')
                        ELSE LPAD(rpe.num_orgao::varchar    , 2, '0')
                    END AS codorgao
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD(despesa.num_unidade::varchar, 2, '0')
                        ELSE LPAD(rpe.num_unidade::varchar    , 2, '0')
                    END AS codunidade
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE LPAD(rpe.cod_funcao::varchar    , 2, '0')
                               END)
                        ELSE '00'
                    END AS codfuncao
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                   ELSE LPAD(rpe.cod_subfuncao::varchar    , 3, '0')
                               END)
                        ELSE '000'
                    END AS codsubfuncao
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD((SUBSTR(acao.num_acao::varchar,1,1)), 6, '0')
                        ELSE LPAD((SUBSTR(rpe.num_pao::varchar,1,1)), 6, '0')
                    END AS naturezaacao
                 , CASE WHEN NOT pe.implantado
                        THEN SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3)
                        ELSE SUBSTR(LPAD(rpe.num_pao::varchar, 4, '0'), 2, 3)
                    END AS nroprojativ
                 , CASE WHEN NOT pe.implantado
                        THEN SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6)
                        ELSE SUBSTR(REPLACE(rpe.cod_estrutural::varchar,'.',''),1,6)
                    END AS elementodespesa
                 , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                    END AS subelemento
                 , CASE WHEN nl.exercicio_empenho <= '2001' AND pe.implantado
                        THEN LPAD(rpe.num_unidade::varchar  , 04, '0')
                          || LPAD(rpe.cod_funcao::varchar   , 02, '0')
                          || LPAD(rpe.cod_programa::varchar , 02, '0')
                          || '000'
                          || LPAD(rpe.num_pao::varchar      , 06, '0')
                          || SUBSTR(REPLACE( rpe.cod_estrutural::varchar, '.', ''), 1, 6)
                        ELSE LPAD('', 21, '0')
                    END AS dotorigp2001
                 , LPAD(e.cod_empenho::VARCHAR, 6, '0') AS nroempenho
                 , op.cod_ordem AS nroop
                 , TO_CHAR(nlpa.timestamp_anulada,'ddmmyyyy')  AS dtAnulacao
                 , op.cod_ordem AS nranulacaoop
                 , LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS codunidadefinanceira
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999'
                        ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                    END AS banco
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999999'
                        ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                    END AS agencia
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999999999999'
                        ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0')::varchar, '-', 1), '-', '')), 12, '0')
                    END AS conta_corrente
                 , LTRIM(SPLIT_PART(conta_corrente.num_conta_corrente,'-',2),'0') AS digiverif
                 , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                        THEN '03'
                        WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                        THEN '02'
                        ELSE '01'
                    END AS tipoconta
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999999999999999'
                        ELSE pagamento_tipo_documento.num_documento
                    END AS nrodocumento
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '99'
                        ELSE tipo_documento.cod_tipo 
                    END AS tipodocumento
                 , SUM(COALESCE(nlp.vl_pago,0)) AS vldocumento
                 , TO_CHAR(nlp.timestamp,'ddmmyyyy') AS dtemissao
                 , SUM(nlpa.vl_anulado) AS vlanulacao
                 , total.valor_total
                 , retencao.vl_retencao
                 , (SELECT SUM(vl_retencao)
                      FROM empenho.ordem_pagamento_retencao
                      JOIN empenho.pagamento_liquidacao
                        ON ordem_pagamento_retencao.cod_ordem = pagamento_liquidacao.cod_ordem
                       AND ordem_pagamento_retencao.cod_entidade = pagamento_liquidacao.cod_entidade
                       AND ordem_pagamento_retencao.exercicio = pagamento_liquidacao.exercicio
                      JOIN empenho.nota_liquidacao_paga
                        ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                       AND nota_liquidacao_paga.exercicio = pagamento_liquidacao.exercicio
                       AND nota_liquidacao_paga.cod_nota = pagamento_liquidacao.cod_nota
                       AND nota_liquidacao_paga.timestamp = (SELECT MAX(nlp.timestamp)
                                                               FROM empenho.nota_liquidacao_paga AS nlp
                                                              WHERE TO_DATE(nota_liquidacao_paga.timestamp::varchar,'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                                                                AND nlp.cod_entidade = nota_liquidacao_paga.cod_entidade
                                                                AND nlp.cod_nota = nota_liquidacao_paga.cod_nota
                                                                AND nlp.exercicio = nota_liquidacao_paga.exercicio)
                     WHERE ordem_pagamento_retencao.exercicio = '".$this->getDado('exercicio')."'
                   ) AS total_pago
              FROM empenho.nota_liquidacao_paga_anulada AS nlpa
        INNER JOIN empenho.nota_liquidacao_paga AS nlp
                ON nlp.exercicio    = nlpa.exercicio   
               AND nlp.cod_nota     = nlpa.cod_nota    
               AND nlp.cod_entidade = nlpa.cod_entidade
               AND nlp.timestamp    = nlpa.timestamp   
        INNER JOIN empenho.nota_liquidacao as nl
                ON nl.exercicio    = nlp.exercicio
               AND nl.cod_entidade = nlp.cod_entidade
               AND nl.cod_nota     = nlp.cod_nota
        INNER JOIN empenho.empenho AS e
                ON e.exercicio = nl.exercicio_empenho
               AND e.cod_entidade = nl.cod_entidade
               AND e.cod_empenho = nl.cod_empenho
        INNER JOIN empenho.pre_empenho AS pe
                ON pe.exercicio = e.exercicio
               AND pe.cod_pre_empenho = e.cod_pre_empenho
         LEFT JOIN empenho.restos_pre_empenho AS rpe
                ON rpe.cod_pre_empenho = pe.cod_pre_empenho
               AND rpe.exercicio = pe.exercicio
        INNER JOIN empenho.pre_empenho_despesa AS ped
                ON ped.cod_pre_empenho = pe.cod_pre_empenho
               AND ped.exercicio = pe.exercicio
        INNER JOIN orcamento.despesa
                ON despesa.exercicio   = ped.exercicio
               AND despesa.cod_despesa = ped.cod_despesa
        INNER JOIN orcamento.pao
                ON pao.num_pao = despesa.num_pao
               AND pao.exercicio = despesa.exercicio
        INNER JOIN orcamento.pao_ppa_acao
                ON pao_ppa_acao.exercicio = pao.exercicio
               AND pao_ppa_acao.num_pao = pao.num_pao
        INNER JOIN ppa.acao
                ON acao.cod_acao = pao_ppa_acao.cod_acao
        INNER JOIN orcamento.programa AS o_programa
                ON o_programa.exercicio = despesa.exercicio
               AND o_programa.cod_programa = despesa.cod_programa
        INNER JOIN orcamento.programa_ppa_programa
                ON programa_ppa_programa.exercicio = o_programa.exercicio
               AND programa_ppa_programa.cod_programa = o_programa.cod_programa
        INNER JOIN ppa.programa
                ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa
        INNER JOIN orcamento.conta_despesa
                ON conta_despesa.exercicio = ped.exercicio
               AND conta_despesa.cod_conta = ped.cod_conta
         LEFT JOIN tcmgo.elemento_de_para
                ON elemento_de_para.cod_conta = conta_despesa.cod_conta
               AND elemento_de_para.exercicio = conta_despesa.exercicio
        INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                ON nlp.cod_entidade = plnlp.cod_entidade
               AND nlp.cod_nota     = plnlp.cod_nota
               AND nlp.exercicio    = plnlp.exercicio
               AND nlp.timestamp    = plnlp.timestamp
        INNER JOIN empenho.pagamento_liquidacao AS pl
                ON pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
               AND pl.exercicio = plnlp.exercicio
               AND pl.cod_entidade = plnlp.cod_entidade
               AND pl.cod_nota = plnlp.cod_nota
               AND pl.cod_ordem = plnlp.cod_ordem
        INNER JOIN empenho.ordem_pagamento AS op
                ON op.exercicio = pl.exercicio
               AND op.cod_entidade = pl.cod_entidade
               AND op.cod_ordem = pl.cod_ordem
        INNER JOIN empenho.nota_liquidacao_conta_pagadora
                ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nlp.exercicio
               AND nota_liquidacao_conta_pagadora.cod_nota             = nlp.cod_nota
               AND nota_liquidacao_conta_pagadora.cod_entidade         = nlp.cod_entidade
               AND nota_liquidacao_conta_pagadora.timestamp            = nlp.timestamp
        
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
               AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
        INNER JOIN contabilidade.plano_banco
                ON plano_analitica.cod_plano = plano_banco.cod_plano
               AND plano_analitica.exercicio = plano_banco.exercicio
        INNER JOIN monetario.conta_corrente
                ON conta_corrente.cod_banco          = plano_banco.cod_banco
               AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
               AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
        INNER JOIN monetario.agencia
                ON agencia.cod_banco   = conta_corrente.cod_banco
               AND agencia.cod_agencia = conta_corrente.cod_agencia
        INNER JOIN monetario.banco
                ON agencia.cod_banco = banco.cod_banco
         LEFT JOIN tesouraria.pagamento_tipo_documento
                ON tesouraria.pagamento_tipo_documento.cod_nota  = nlp.cod_nota
               AND tesouraria.pagamento_tipo_documento.exercicio = nlp.exercicio
               AND pagamento_tipo_documento.timestamp            = nlp.timestamp
         LEFT JOIN tcmgo.tipo_documento
                ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento
         LEFT JOIN ( SELECT sum(vl_pago) as valor_total
                          , pagamento_tipo_documento.num_documento as nrDocumento
                          , TO_DATE(nlp.timestamp::varchar,'YYYY-MM') AS mesAno
                       FROM empenho.nota_liquidacao_paga as nlp
                  LEFT JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                         ON nlp.exercicio    = nlpa.exercicio
                        AND nlp.cod_nota     = nlpa.cod_nota
                        AND nlp.cod_entidade = nlpa.cod_entidade
                        AND nlp.timestamp    = nlpa.timestamp
                 
                  LEFT JOIN tesouraria.pagamento_tipo_documento
                         ON tesouraria.pagamento_tipo_documento.cod_nota   = nlp.cod_nota
                        AND tesouraria.pagamento_tipo_documento.exercicio  = nlp.exercicio
                        AND pagamento_tipo_documento.timestamp             = nlp.timestamp
        
                  GROUP BY pagamento_tipo_documento.num_documento
                         , mesAno
                   ) AS total
                ON total.nrDocumento = pagamento_tipo_documento.num_documento
               AND total.mesAno      = TO_DATE(nlp.timestamp::varchar,'YYYY-MM')

         LEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
                          , ordem_pagamento_retencao.cod_entidade
                          , ordem_pagamento_retencao.exercicio
                          , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                       FROM empenho.ordem_pagamento_retencao
                       JOIN contabilidade.plano_analitica
                         ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                        AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                       JOIN contabilidade.plano_conta
                         ON plano_conta.cod_conta = plano_analitica.cod_conta
                        AND plano_conta.exercicio = plano_analitica.exercicio
                      WHERE ordem_pagamento_retencao.cod_receita IS NULL
                   GROUP BY ordem_pagamento_retencao.cod_ordem
                          , ordem_pagamento_retencao.cod_entidade
                          , ordem_pagamento_retencao.exercicio
                   ) AS retencao
                ON op.cod_ordem = retencao.cod_ordem
               AND op.cod_entidade = retencao.cod_entidade
               AND op.exercicio = retencao.exercicio
        
             WHERE nlpa.exercicio = '".$this->getDado('exercicio')."'
               AND nlpa.cod_entidade IN (".$this->getDado('cod_entidade').")
               AND (TO_CHAR(op.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
               AND TO_DATE(nlpa.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
        
        
          GROUP BY tiporegistro
                 , codprograma
                 , codorgao
                 , codunidade
                 , codfuncao
                 , codsubfuncao
                 , naturezaacao
                 , nroprojativ
                 , elementodespesa
                 , subelemento
                 , dotorigp2001
                 , nroempenho
                 , nroop
                 , dtanulacao
                 , nranulacaoop
                 , codunidadefinanceira
                 , banco
                 , agencia
                 , conta_corrente.num_conta_corrente
                 , tipoconta
                 , nrodocumento
                 , tipodocumento
                 , dtemissao
                 , plano_conta.cod_estrutural
                 , total.valor_total
                 , retencao.vl_retencao
        ";
        return $stSql;
    }

    public function recuperaRecursoOrdemPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRecursoOrdemPagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRecursoOrdemPagamento()
    {
        $stSql = "
            SELECT '13' AS tiporegistro
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE LPAD(rpe.cod_programa::varchar , 02, '0')
                               END)
                        ELSE '0000'
                    END AS codprograma
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD(despesa.num_orgao::varchar, 2, '0')
                        ELSE LPAD(rpe.num_orgao::varchar    , 2, '0')
                    END AS codorgao
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD(despesa.num_unidade::varchar, 2, '0')
                        ELSE LPAD(rpe.num_unidade::varchar    , 2, '0')
                    END AS codunidade
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE LPAD(rpe.cod_funcao::varchar    , 2, '0')
                               END)
                        ELSE '00'
                    END AS codfuncao
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                   ELSE LPAD(rpe.cod_subfuncao::varchar    , 3, '0')
                               END)
                        ELSE '000'
                    END AS codsubfuncao
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD((SUBSTR(acao.num_acao::varchar,1,1)), 6, '0')
                        ELSE LPAD((SUBSTR(rpe.num_pao::varchar,1,1)), 6, '0')
                    END AS naturezaacao
                 , CASE WHEN NOT pe.implantado
                        THEN SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3)
                        ELSE SUBSTR(LPAD(rpe.num_pao::varchar, 4, '0'), 2, 3)
                    END AS nroprojativ
                 , CASE WHEN NOT pe.implantado
                        THEN SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6)
                        ELSE SUBSTR(REPLACE(rpe.cod_estrutural::varchar,'.',''),1,6)
                    END AS elementodespesa
                 , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                    END AS subelemento
                 , CASE WHEN nl.exercicio_empenho <= '2001' AND pe.implantado
                        THEN LPAD(rpe.num_unidade::varchar  , 04, '0')
                          || LPAD(rpe.cod_funcao::varchar   , 02, '0')
                          || LPAD(rpe.cod_programa::varchar , 02, '0')
                          || '000'
                          || LPAD(rpe.num_pao::varchar      , 06, '0')
                          || SUBSTR(REPLACE( rpe.cod_estrutural::varchar, '.', ''), 1, 6)
                        ELSE LPAD('', 21, '0')
                    END AS dotorigp2001
                 , LPAD(e.cod_empenho::VARCHAR, 6, '0') AS nroempenho
                 , op.cod_ordem AS nroop
                 , TO_CHAR(nlpa.timestamp_anulada,'ddmmyyyy')  AS dtAnulacao
                 , op.cod_ordem AS nranulacaoop
                 , LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS codunidadefinanceira
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999'
                        ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                    END AS banco
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999999'
                        ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                    END AS agencia
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999999999999'
                        ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0')::varchar, '-', 1), '-', '')), 12, '0')
                    END AS conta_corrente
                 , LTRIM(SPLIT_PART(conta_corrente.num_conta_corrente,'-',2),'0') AS digiverif
                 , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                        THEN '03'
                        WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                        THEN '02'
                        ELSE '01'
                    END AS tipoconta
                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                        THEN '999999999999999'
                        ELSE pagamento_tipo_documento.num_documento
                    END AS nrodocumento
                 , recurso.cod_fonte AS codFonteRecurso
                 , SUM(COALESCE(nlpa.vl_anulado,0)) AS vlanulacaofr
              FROM empenho.nota_liquidacao_paga_anulada AS nlpa
        INNER JOIN empenho.nota_liquidacao_paga AS nlp
                ON nlp.exercicio    = nlpa.exercicio   
               AND nlp.cod_nota     = nlpa.cod_nota    
               AND nlp.cod_entidade = nlpa.cod_entidade
               AND nlp.timestamp    = nlpa.timestamp   
        INNER JOIN empenho.nota_liquidacao as nl
                ON nl.exercicio    = nlp.exercicio
               AND nl.cod_entidade = nlp.cod_entidade
               AND nl.cod_nota     = nlp.cod_nota
        INNER JOIN empenho.empenho AS e
                ON e.exercicio = nl.exercicio_empenho
               AND e.cod_entidade = nl.cod_entidade
               AND e.cod_empenho = nl.cod_empenho
        INNER JOIN empenho.pre_empenho AS pe
                ON pe.exercicio = e.exercicio
               AND pe.cod_pre_empenho = e.cod_pre_empenho
         LEFT JOIN empenho.restos_pre_empenho AS rpe
                ON rpe.cod_pre_empenho = pe.cod_pre_empenho
               AND rpe.exercicio = pe.exercicio
        INNER JOIN empenho.pre_empenho_despesa AS ped
                ON ped.cod_pre_empenho = pe.cod_pre_empenho
               AND ped.exercicio = pe.exercicio
        INNER JOIN orcamento.despesa
                ON despesa.exercicio   = ped.exercicio
               AND despesa.cod_despesa = ped.cod_despesa
        INNER JOIN orcamento.pao
                ON pao.num_pao = despesa.num_pao
               AND pao.exercicio = despesa.exercicio
        INNER JOIN orcamento.pao_ppa_acao
                ON pao_ppa_acao.exercicio = pao.exercicio
               AND pao_ppa_acao.num_pao = pao.num_pao
        INNER JOIN ppa.acao
                ON acao.cod_acao = pao_ppa_acao.cod_acao
        INNER JOIN orcamento.programa AS o_programa
                ON o_programa.exercicio = despesa.exercicio
               AND o_programa.cod_programa = despesa.cod_programa
        INNER JOIN orcamento.programa_ppa_programa
                ON programa_ppa_programa.exercicio = o_programa.exercicio
               AND programa_ppa_programa.cod_programa = o_programa.cod_programa
        INNER JOIN ppa.programa
                ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa
        INNER JOIN orcamento.conta_despesa
                ON conta_despesa.exercicio = ped.exercicio
               AND conta_despesa.cod_conta = ped.cod_conta
         LEFT JOIN tcmgo.elemento_de_para
                ON elemento_de_para.cod_conta = conta_despesa.cod_conta
               AND elemento_de_para.exercicio = conta_despesa.exercicio
        INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                ON nlp.cod_entidade = plnlp.cod_entidade
               AND nlp.cod_nota     = plnlp.cod_nota
               AND nlp.exercicio    = plnlp.exercicio
               AND nlp.timestamp    = plnlp.timestamp
        INNER JOIN empenho.pagamento_liquidacao AS pl
                ON pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
               AND pl.exercicio = plnlp.exercicio
               AND pl.cod_entidade = plnlp.cod_entidade
               AND pl.cod_nota = plnlp.cod_nota
               AND pl.cod_ordem = plnlp.cod_ordem
        INNER JOIN empenho.ordem_pagamento AS op
                ON op.exercicio = pl.exercicio
               AND op.cod_entidade = pl.cod_entidade
               AND op.cod_ordem = pl.cod_ordem
        INNER JOIN empenho.nota_liquidacao_conta_pagadora
                ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nlp.exercicio
               AND nota_liquidacao_conta_pagadora.cod_nota             = nlp.cod_nota
               AND nota_liquidacao_conta_pagadora.cod_entidade         = nlp.cod_entidade
               AND nota_liquidacao_conta_pagadora.timestamp            = nlp.timestamp
        
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = nota_liquidacao_conta_pagadora.cod_plano
               AND plano_analitica.exercicio = nota_liquidacao_conta_pagadora.exercicio
        INNER JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
        INNER JOIN contabilidade.plano_banco
                ON plano_banco.cod_plano = plano_analitica.cod_plano
               AND plano_banco.exercicio = plano_analitica.exercicio
        INNER JOIN contabilidade.plano_recurso
                ON plano_recurso.exercicio = plano_analitica.exercicio
               AND plano_recurso.cod_plano = plano_analitica.cod_plano
        INNER JOIN monetario.conta_corrente
                ON conta_corrente.cod_banco          = plano_banco.cod_banco
               AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
               AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
        INNER JOIN monetario.agencia
                ON agencia.cod_banco   = conta_corrente.cod_banco
               AND agencia.cod_agencia = conta_corrente.cod_agencia
        INNER JOIN monetario.banco
                ON agencia.cod_banco = banco.cod_banco
        INNER JOIN orcamento.recurso
                ON recurso.exercicio   = plano_recurso.exercicio
               AND recurso.cod_recurso = plano_recurso.cod_recurso
        
         LEFT JOIN tesouraria.pagamento_tipo_documento
                ON tesouraria.pagamento_tipo_documento.cod_nota  = nlp.cod_nota
               AND tesouraria.pagamento_tipo_documento.exercicio = nlp.exercicio
               AND pagamento_tipo_documento.timestamp            = nlp.timestamp
         LEFT JOIN tcmgo.tipo_documento
                ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento
        
             WHERE nlpa.exercicio = '".$this->getDado('exercicio')."'
               AND nlpa.cod_entidade IN (".$this->getDado('cod_entidade').")
               AND (TO_CHAR(op.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
               AND TO_DATE(nlpa.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
        
        
          GROUP BY tiporegistro
                 , codprograma
                 , codorgao
                 , codunidade
                 , codfuncao
                 , codsubfuncao
                 , naturezaacao
                 , nroprojativ
                 , elementodespesa
                 , subelemento
                 , dotorigp2001
                 , nroempenho
                 , nroop
                 , dtanulacao
                 , nranulacaoop
                 , codunidadefinanceira
                 , banco
                 , agencia
                 , conta_corrente.num_conta_corrente
                 , tipoconta
                 , nrodocumento
                 , codFonteRecurso
                 , plano_conta.cod_estrutural                                
        ";
        return $stSql;
    }

    public function recuperaRetencaoOrdemPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRetencaoOrdemPagamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRetencaoOrdemPagamento()
    {
        $stSql = "
        SELECT tiporegistro
             , codprograma
             , codorgao
             , codunidade
             , codfuncao
             , codsubfuncao
             , naturezaacao
             , nroprojativ
             , elementodespesa
             , subelemento
             , dotorigp2001
             , nroempenho
             , nroop
             , dtAnulacao
             , nranulacaoop
             , tiporetencao
             , SUM(vlanulacaoretencao) AS vlanulacaoretencao
             , '0' AS nrextraorcamentaria
          FROM (
            SELECT '14' AS tiporegistro
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE LPAD(rpe.cod_programa::varchar , 02, '0')
                               END)
                        ELSE '0000'
                    END AS codprograma
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD(despesa.num_orgao::varchar, 2, '0')
                        ELSE LPAD(rpe.num_orgao::varchar    , 2, '0')
                    END AS codorgao
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD(despesa.num_unidade::varchar, 2, '0')
                        ELSE LPAD(rpe.num_unidade::varchar    , 2, '0')
                    END AS codunidade
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE LPAD(rpe.cod_funcao::varchar    , 2, '0')
                               END)
                        ELSE '00'
                    END AS codfuncao
                 , CASE WHEN nl.exercicio_empenho > '2001'
                        THEN (CASE WHEN NOT pe.implantado
                                   THEN LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                   ELSE LPAD(rpe.cod_subfuncao::varchar    , 3, '0')
                               END)
                        ELSE '000'
                    END AS codsubfuncao
                 , CASE WHEN NOT pe.implantado
                        THEN LPAD((SUBSTR(acao.num_acao::varchar,1,1)), 6, '0')
                        ELSE LPAD((SUBSTR(rpe.num_pao::varchar,1,1)), 6, '0')
                    END AS naturezaacao
                 , CASE WHEN NOT pe.implantado
                        THEN SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3)
                        ELSE SUBSTR(LPAD(rpe.num_pao::varchar, 4, '0'), 2, 3)
                    END AS nroprojativ
                 , CASE WHEN NOT pe.implantado
                        THEN SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6)
                        ELSE SUBSTR(REPLACE(rpe.cod_estrutural::varchar,'.',''),1,6)
                    END AS elementodespesa
                 , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                    END AS subelemento
                 , CASE WHEN nl.exercicio_empenho <= '2001' AND pe.implantado
                        THEN LPAD(rpe.num_unidade::varchar  , 04, '0')
                          || LPAD(rpe.cod_funcao::varchar   , 02, '0')
                          || LPAD(rpe.cod_programa::varchar , 02, '0')
                          || '000'
                          || LPAD(rpe.num_pao::varchar      , 06, '0')
                          || SUBSTR(REPLACE( rpe.cod_estrutural::varchar, '.', ''), 1, 6)
                        ELSE LPAD('', 21, '0')
                    END AS dotorigp2001
                 , LPAD(e.cod_empenho::VARCHAR, 6, '0') AS nroempenho
                 , op.cod_ordem AS nroop
                 , TO_CHAR(nlpa.timestamp_anulada,'ddmmyyyy')  AS dtAnulacao
                 , op.cod_ordem AS nranulacaoop
                 , SUBSTR((CASE WHEN de_para_tipo_retencao.cod_tipo IS NOT NULL
                                THEN de_para_tipo_retencao.cod_tipo::integer
                                WHEN balancete_extmmaa.sub_tipo_lancamento IS NOT NULL
                                THEN CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 3
                                              THEN 2::integer
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 2
                                              THEN 4::integer
                                          WHEN balancete_extmmaa.sub_tipo_lancamento = 4
                                              THEN 3::integer
                                          WHEN balancete_extmmaa.sub_tipo_lancamento NOT IN (1,2,3,4)
                                              THEN 99::integer
                                          ELSE balancete_extmmaa.sub_tipo_lancamento::integer
                                      END
                                ELSE NULL
                           END)::varchar, 1, 2) AS tiporetencao
                 , ordem_pagamento_retencao.vl_retencao AS vlanulacaoretencao
              FROM empenho.nota_liquidacao_paga_anulada AS nlpa
        INNER JOIN empenho.nota_liquidacao_paga AS nlp
                ON nlp.exercicio    = nlpa.exercicio   
               AND nlp.cod_nota     = nlpa.cod_nota    
               AND nlp.cod_entidade = nlpa.cod_entidade
               AND nlp.timestamp    = nlpa.timestamp   
        INNER JOIN empenho.nota_liquidacao as nl
                ON nl.exercicio    = nlp.exercicio
               AND nl.cod_entidade = nlp.cod_entidade
               AND nl.cod_nota     = nlp.cod_nota
        INNER JOIN empenho.empenho AS e
                ON e.exercicio = nl.exercicio_empenho
               AND e.cod_entidade = nl.cod_entidade
               AND e.cod_empenho = nl.cod_empenho
        INNER JOIN empenho.pre_empenho AS pe
                ON pe.exercicio = e.exercicio
               AND pe.cod_pre_empenho = e.cod_pre_empenho
         LEFT JOIN empenho.restos_pre_empenho AS rpe
                ON rpe.cod_pre_empenho = pe.cod_pre_empenho
               AND rpe.exercicio = pe.exercicio
        INNER JOIN empenho.pre_empenho_despesa AS ped
                ON ped.cod_pre_empenho = pe.cod_pre_empenho
               AND ped.exercicio = pe.exercicio
        INNER JOIN orcamento.despesa
                ON despesa.exercicio   = ped.exercicio
               AND despesa.cod_despesa = ped.cod_despesa
        INNER JOIN orcamento.pao
                ON pao.num_pao = despesa.num_pao
               AND pao.exercicio = despesa.exercicio
        INNER JOIN orcamento.pao_ppa_acao
                ON pao_ppa_acao.exercicio = pao.exercicio
               AND pao_ppa_acao.num_pao = pao.num_pao
        INNER JOIN ppa.acao
                ON acao.cod_acao = pao_ppa_acao.cod_acao
        INNER JOIN orcamento.programa AS o_programa
                ON o_programa.exercicio = despesa.exercicio
               AND o_programa.cod_programa = despesa.cod_programa
        INNER JOIN orcamento.programa_ppa_programa
                ON programa_ppa_programa.exercicio = o_programa.exercicio
               AND programa_ppa_programa.cod_programa = o_programa.cod_programa
        INNER JOIN ppa.programa
                ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa
        INNER JOIN orcamento.conta_despesa
                ON conta_despesa.exercicio = ped.exercicio
               AND conta_despesa.cod_conta = ped.cod_conta
         LEFT JOIN tcmgo.elemento_de_para
                ON elemento_de_para.cod_conta = conta_despesa.cod_conta
               AND elemento_de_para.exercicio = conta_despesa.exercicio
        INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga AS plnlp
                ON nlp.cod_entidade = plnlp.cod_entidade
               AND nlp.cod_nota     = plnlp.cod_nota
               AND nlp.exercicio    = plnlp.exercicio
               AND nlp.timestamp    = plnlp.timestamp
        INNER JOIN empenho.pagamento_liquidacao AS pl
                ON pl.exercicio_liquidacao = plnlp.exercicio_liquidacao
               AND pl.exercicio = plnlp.exercicio
               AND pl.cod_entidade = plnlp.cod_entidade
               AND pl.cod_nota = plnlp.cod_nota
               AND pl.cod_ordem = plnlp.cod_ordem
        INNER JOIN empenho.ordem_pagamento AS op
                ON op.exercicio = pl.exercicio
               AND op.cod_entidade = pl.cod_entidade
               AND op.cod_ordem = pl.cod_ordem
        INNER JOIN empenho.nota_liquidacao_conta_pagadora
                ON nota_liquidacao_conta_pagadora.exercicio_liquidacao = nlp.exercicio
               AND nota_liquidacao_conta_pagadora.cod_nota             = nlp.cod_nota
               AND nota_liquidacao_conta_pagadora.cod_entidade         = nlp.cod_entidade
               AND nota_liquidacao_conta_pagadora.timestamp            = nlp.timestamp
         
        INNER JOIN empenho.ordem_pagamento_retencao
                ON ordem_pagamento_retencao.exercicio    = op.exercicio
               AND ordem_pagamento_retencao.cod_entidade = op.cod_entidade
               AND ordem_pagamento_retencao.cod_ordem    = op.cod_ordem
               AND ordem_pagamento_retencao.cod_receita IS NULL
         
        INNER JOIN contabilidade.plano_analitica
                ON plano_analitica.cod_plano = ordem_pagamento_retencao.cod_plano
               AND plano_analitica.exercicio = ordem_pagamento_retencao.exercicio
        INNER JOIN contabilidade.plano_recurso
                ON plano_recurso.exercicio = plano_analitica.exercicio
               AND plano_recurso.cod_plano = plano_analitica.cod_plano
        
         LEFT JOIN tcmgo.de_para_tipo_retencao
                ON de_para_tipo_retencao.exercicio = plano_analitica.exercicio
               AND de_para_tipo_retencao.cod_plano = plano_analitica.cod_plano
         
         LEFT JOIN tcmgo.tipo_retencao
                ON tipo_retencao.exercicio = de_para_tipo_retencao.exercicio_tipo
               AND tipo_retencao.cod_tipo  = de_para_tipo_retencao.cod_tipo
         
         LEFT JOIN tcmgo.balancete_extmmaa
                ON balancete_extmmaa.exercicio = plano_analitica.exercicio
               AND balancete_extmmaa.cod_plano = plano_analitica.cod_plano
        
              JOIN contabilidade.plano_conta
                ON plano_conta.cod_conta = plano_analitica.cod_conta
               AND plano_conta.exercicio = plano_analitica.exercicio
         
             WHERE nlpa.exercicio = '".$this->getDado('exercicio')."'
               AND nlpa.cod_entidade IN (".$this->getDado('cod_entidade').")
               AND (TO_CHAR(op.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
               AND TO_DATE(nlpa.timestamp_anulada::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
        
        
          GROUP BY tiporegistro
                 , pe.implantado
                 , codprograma
                 , codorgao
                 , codunidade
                 , codfuncao
                 , codsubfuncao
                 , naturezaacao
                 , nroprojativ
                 , elementodespesa
                 , subelemento
                 , dotorigp2001
                 , nroempenho
                 , nroop
                 , dtAnulacao
                 , nranulacaoop
                 , tiporetencao
                 , vlanulacaoretencao
              ) AS retencoes
          GROUP BY tiporegistro
              , codprograma
              , codorgao
              , codunidade
              , codfuncao
              , codsubfuncao
              , naturezaacao
              , nroprojativ
              , elementodespesa
              , subelemento
              , dotorigp2001
              , nroempenho
              , nroop
              , dtAnulacao
              , nranulacaoop
              , tiporetencao
                                ";
        return $stSql;
    }

}
