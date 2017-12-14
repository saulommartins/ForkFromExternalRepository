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

    $Revision: 65015 $
    $Name$
    $Author: franver $
    $Date: 2016-04-19 09:32:42 -0300 (Tue, 19 Apr 2016) $

    * Casos de uso: uc-06.04.00
    $Id : $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOOPS extends Persistente
{
/**
* Método Construtor
* @access Private
*/

function recuperaOrdemPagamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera('montaRecuperaOrdemPagamento',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrdemPagamento()
{
    $stSql = "
        SELECT DISTINCT '10' AS TipoRegistro
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                    THEN (CASE WHEN NOT pre_empenho.implantado
                               THEN LPAD(programa.num_programa::varchar, 4, '0')
                               ELSE LPAD(programa.num_programa::varchar , 4, '0')
                           END)
                    ELSE '0000'
                END AS CodPrograma
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                    THEN (CASE WHEN NOT pre_empenho.implantado
                               THEN LPAD(despesa.cod_funcao::varchar, 2, '0')
                               ELSE LPAD(despesa.cod_funcao::varchar   , 02, '0')
                           END)
                    ELSE '00'
                END AS CodFuncao
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                    THEN (CASE WHEN NOT pre_empenho.implantado
                               THEN despesa.cod_subfuncao::varchar
                               ELSE LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                           END)
                    ELSE '000'
                END AS CodSubFuncao
             , LPAD(num_orgao::varchar, 2, '0') AS CodOrgao
             , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
             , LPAD(SUBSTR(acao.num_acao::varchar,1,1), 6, '0') AS NaturezaAcao
             , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS NroProjAtiv
             , LPAD(nota_liquidacao.cod_empenho::varchar, 6, '0') AS nroEmpenho
             , ordem_pagamento.cod_ordem AS nroOP
             , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado
                    THEN LPAD(num_unidade::varchar  , 04, '0')
                      || LPAD(cod_funcao::varchar   , 02, '0')
                      || LPAD(despesa.cod_programa::varchar , 02, '0')
                      || '000'
                      || LPAD(despesa.num_pao::varchar      , 06, '0')
                      || SUBSTR(REPLACE( conta_despesa.cod_estrutural::varchar, '.', ''), 1, 6)
                    ELSE LPAD('', 21, '0')
                END AS DotOrigp2001
             , sum(pagamento_liquidacao.vl_pagamento) AS VlOP
             , TO_CHAR(empenho.dt_empenho,'ddmmyyyy')      AS DtInscricao
             , TO_CHAR(ordem_pagamento.dt_emissao,'ddmmyyyy')      AS DtEmissao
             , 2 as TipoOp
             , sem_acentos(sw_cgm.nom_cgm) as NomeCredor
             , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                    THEN LPAD(sw_cgm_pessoa_fisica.cpf::varchar, 14, '0')
                    ELSE LPAD(sw_cgm_pessoa_juridica.cnpj, 14, '0')
                END AS CpfCnpj ";
            if (Sessao::getExercicio() > 2012) {
                $stSql .= "\n , CASE WHEN sw_cgm.cod_pais <> 1 THEN 3
                                   WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                                   ELSE 2
                               END AS TipoCredor ";
            } else {
                $stSql .= "\n , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN 1
                                   ELSE 2
                               END AS TipoCredor ";
            }
             $stSql .= "\n , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6) AS elementodespesa
             , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                    THEN SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)
                    ELSE '00'
                END AS subelemento
             , 0 AS numero_sequencial
             , sem_acentos(ordem_pagamento.observacao) as especificacaoop
             , 0::integer as nrextraorcamentaria 
             , '41880854104' AS cpf_resp_op
             , 'Fabio Oliveira de Lima' as nom_resp_op
             , nota_liquidacao_paga.num_documento AS nrdocumento
             , vl_retencao_orcamentaria.vl_retencao AS vlretencao
          FROM ( SELECT vl_total as valor
                      , nl.exercicio
                      , nl.cod_nota
                      , nl.cod_entidade
                      , nlp.timestamp
                      , pagamento_tipo_documento.num_documento
                   FROM empenho.nota_liquidacao as nl
                  INNER JOIN empenho.nota_liquidacao_paga as nlp
                     ON nlp.exercicio    = nl.exercicio
                    AND nlp.cod_entidade = nl.cod_entidade
                    AND nlp.cod_nota     = nl.cod_nota

             INNER JOIN tesouraria.pagamento_tipo_documento
                     ON pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                    AND pagamento_tipo_documento.exercicio    = nlp.exercicio
                    AND pagamento_tipo_documento.timestamp    = nlp.timestamp
                    AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade

             INNER JOIN empenho.nota_liquidacao_item as nli
                     ON nl.exercicio    = nli.exercicio
                    AND nl.cod_nota     = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade

              LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia
                ON nlia.exercicio       = nli.exercicio
               AND nlia.cod_nota        = nli.cod_nota
               AND nlia.num_item        = nli.num_item
               AND nlia.exercicio_item  =  nli.exercicio_item
               AND nlia.cod_pre_empenho =  nli.cod_pre_empenho
               AND nlia.cod_entidade    = nli.cod_entidade
             WHERE TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                            AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
             ) as nota_liquidacao_paga
    INNER JOIN empenho.nota_liquidacao
            ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
           AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
           AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

    INNER JOIN empenho.empenho
            ON empenho.exercicio = nota_liquidacao.exercicio_empenho
           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
           AND empenho.cod_empenho = nota_liquidacao.cod_empenho

    INNER JOIN empenho.pre_empenho
            ON pre_empenho.exercicio       = empenho.exercicio
           AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

     LEFT JOIN sw_cgm
            ON sw_cgm.numcgm = pre_empenho.cgm_beneficiario

     LEFT JOIN sw_cgm_pessoa_fisica
            ON sw_cgm_pessoa_fisica.numcgm = pre_empenho.cgm_beneficiario

     LEFT JOIN sw_cgm_pessoa_juridica
            ON sw_cgm_pessoa_juridica.numcgm = pre_empenho.cgm_beneficiario

    INNER JOIN empenho.pre_empenho_despesa
            ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
           AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

    INNER JOIN orcamento.despesa
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
    
    INNER JOIN orcamento.programa AS o_programa
            ON o_programa.cod_programa = despesa.cod_programa
           AND o_programa.exercicio = despesa.exercicio
  
    INNER JOIN orcamento.programa_ppa_programa
            ON programa_ppa_programa.cod_programa = o_programa.cod_programa
           AND programa_ppa_programa.exercicio = o_programa.exercicio
           
    INNER JOIN ppa.programa
            ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

    INNER JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN tcmgo.elemento_de_para
            ON elemento_de_para.cod_conta = conta_despesa.cod_conta
           AND elemento_de_para.exercicio = conta_despesa.exercicio

    INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
            ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
           AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
           AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
           AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

    INNER JOIN empenho.pagamento_liquidacao
            ON pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio_liquidacao
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
           AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio

    INNER JOIN empenho.ordem_pagamento
            ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
           AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
           AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem";

    if ($this->getDado('exercicio') > '2010') {
        $stSql .= "\nLEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
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
                        WHERE SUBSTR(plano_conta.cod_estrutural, 1, 1) <> '4'
                        GROUP BY ordem_pagamento_retencao.cod_ordem
                            , ordem_pagamento_retencao.cod_entidade
                            , ordem_pagamento_retencao.exercicio
                    ) AS vl_retencao_orcamentaria
                    ON vl_retencao_orcamentaria.cod_ordem     = ordem_pagamento.cod_ordem
                    AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                    AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio";
    }

    $stSql .= "\nWHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'

      GROUP BY TipoRegistro
             , CodPrograma
             , CodOrgao
             , CodUnidade
             , CodFuncao
             , nroEmpenho
             , nroOP
             , dtEmissao
             , despesa.num_pao
             , exercicio_empenho
             , implantado
             , dt_empenho
             , cpf
             , cnpj
             , nom_cgm
             , conta_despesa.cod_estrutural
             , cod_subfuncao
             , num_unidade
             , cod_funcao
             , despesa.cod_programa
             , ordem_pagamento.observacao
             , ordem_pagamento.cod_entidade
             , ordem_pagamento.exercicio
             , elemento_de_para.estrutural
             , nota_liquidacao_paga.num_documento";
    if (Sessao::getExercicio() > 2012) {
        $stSql .= "\n,sw_cgm.cod_pais";
    }

    if ($this->getDado('exercicio') > '2011') {
        $stSql .= "\n,vlretencao
                     ,acao.num_acao
                     

        ORDER BY  nroop
                , codunidade
                , codfuncao
                , codsubfuncao
                , naturezaacao
                , nroprojativ
                , elementodespesa
                , subelemento
                , dotorigp2001
                , nroempenho
                , codprograma
                , nrdocumento";
    } else {
        $stSql .= "\nORDER BY nroOP";
    }

    return $stSql;
}

function recuperaOrdemPagamentoLiquidacoes(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera('montaRecuperaOrdemPagamentoLiquidacoes',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrdemPagamentoLiquidacoes()
{
    $stSql = "
        SELECT '11' AS TipoRegistro
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN (
                    CASE WHEN NOT pre_empenho.implantado THEN
                            LPAD(programa.num_programa::varchar, 4, '0')
                         ELSE
                            LPAD(programa.num_programa::varchar , 4, '0')
                    END)
               ELSE '0000'
               END AS CodPrograma
             , LPAD(num_orgao::varchar, 2, '0') AS CodOrgao
             , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN (
                    CASE WHEN NOT pre_empenho.implantado THEN
                            LPAD(despesa.cod_funcao::varchar, 2, '0')
                         ELSE
                            LPAD(despesa.cod_funcao::varchar   , 02, '0')
                    END)
               ELSE '00'
               END AS CodFuncao
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                    (CASE WHEN NOT pre_empenho.implantado THEN
                             despesa.cod_subfuncao::varchar
                          ELSE
                             LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                    END)
               ELSE '000'
               END AS CodSubFuncao
             , LPAD(SUBSTR(acao.num_acao::varchar,1,1), 6, '0')   AS NaturezaAcao
             , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS NroProjAtiv
             , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elementodespesa
             , CASE WHEN( elemento_de_para.estrutural IS NOT NULL ) THEN
                        SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                    ELSE
                        '00'
               END AS subelemento
             , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                        LPAD(num_unidade::varchar  , 04, '0')
                     || LPAD(cod_funcao::varchar   , 02, '0')
                     || LPAD(despesa.cod_programa::varchar , 02, '0')
                     || '000'
                     || LPAD(despesa.num_pao::varchar      , 06, '0')
                     || SUBSTR(REPLACE( conta_despesa.cod_estrutural::varchar, '.', ''), 1, 6)
               ELSE LPAD('', 21, '0')
               END AS DotOrigp2001
             , LPAD(nota_liquidacao.cod_empenho::varchar, 6, '0') AS nroEmpenho
             , ordem_pagamento.cod_ordem AS nroOP
             , TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho) AS numeroliquidacao";
             if (Sessao::getExercicio() > '2011') {
                if ( Sessao::getExercicio() < '2014' ) {
                    $stSql .= "\n, LPAD(BTRIM(TO_CHAR((nota_liquidacao_paga.valor + COALESCE(vl_retencao_orcamentaria.vl_retencao, 0)), '9999999999D99')),13,'0') AS vlliquidacao";
                } else {
                    $stSql .= "\n, LPAD(BTRIM(TO_CHAR((nota_liquidacao_paga.valor), '9999999999D99')),13,'0') AS vlliquidacao";
                }
             } else {
                $stSql .= "\n, nota_liquidacao_paga.valor AS vlliquidacao";
            }
  $stSql .= "\n, pagamento_liquidacao.vl_pagamento AS vloP
             , to_char (empenho.nota_liquidacao.dt_liquidacao, 'dd/mm/yyyy') AS dt_liquidacao
             , 0 AS numero_sequencial
          FROM ( SELECT SUM(nli.vl_total) as valor
                      , nl.exercicio
                      , nl.cod_nota
                      , nl.cod_entidade
                      , nlp.timestamp
                      , pagamento_tipo_documento.num_documento
                   FROM empenho.nota_liquidacao as nl
                  INNER JOIN empenho.nota_liquidacao_paga as nlp
                     ON nlp.exercicio    = nl.exercicio
                    AND nlp.cod_entidade = nl.cod_entidade
                    AND nlp.cod_nota     = nl.cod_nota

             INNER JOIN tesouraria.pagamento_tipo_documento
                     ON pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                    AND pagamento_tipo_documento.exercicio    = nlp.exercicio
                    AND pagamento_tipo_documento.timestamp    = nlp.timestamp
                    AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade

             INNER JOIN empenho.nota_liquidacao_item as nli
                     ON nl.exercicio    = nli.exercicio
                    AND nl.cod_nota     = nli.cod_nota
                    AND nl.cod_entidade = nli.cod_entidade

              LEFT JOIN empenho.nota_liquidacao_item_anulado as nlia
                ON nlia.exercicio       = nli.exercicio
               AND nlia.cod_nota        = nli.cod_nota
               AND nlia.num_item        = nli.num_item
               AND nlia.exercicio_item  =  nli.exercicio_item
               AND nlia.cod_pre_empenho =  nli.cod_pre_empenho
               AND nlia.cod_entidade    = nli.cod_entidade
             WHERE TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                            AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

          GROUP BY nl.exercicio
                 , nl.cod_nota
                 , nl.cod_entidade
                 , nlp.timestamp
                 , pagamento_tipo_documento.num_documento
             ) as nota_liquidacao_paga
    INNER JOIN empenho.nota_liquidacao
            ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
           AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
           AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

    INNER JOIN empenho.empenho
            ON empenho.exercicio = nota_liquidacao.exercicio_empenho
           AND empenho.cod_entidade = nota_liquidacao.cod_entidade
           AND empenho.cod_empenho = nota_liquidacao.cod_empenho

    INNER JOIN empenho.pre_empenho
            ON pre_empenho.exercicio       = empenho.exercicio
           AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

    INNER JOIN empenho.pre_empenho_despesa
            ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
           AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

    INNER JOIN orcamento.despesa
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
    
    INNER JOIN orcamento.programa AS o_programa
            ON o_programa.cod_programa = despesa.cod_programa
           AND o_programa.exercicio = despesa.exercicio
  
    INNER JOIN orcamento.programa_ppa_programa
            ON programa_ppa_programa.cod_programa = o_programa.cod_programa
           AND programa_ppa_programa.exercicio = o_programa.exercicio
           
    INNER JOIN ppa.programa
            ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

    INNER JOIN orcamento.conta_despesa
            ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
           AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

     LEFT JOIN tcmgo.elemento_de_para
            ON elemento_de_para.cod_conta = conta_despesa.cod_conta
           AND elemento_de_para.exercicio = conta_despesa.exercicio

    INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
            ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
           AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
           AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
           AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

    INNER JOIN empenho.pagamento_liquidacao
            ON pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio_liquidacao
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
           AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio

    INNER JOIN empenho.ordem_pagamento
            ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
           AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
           AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem";

    if ($this->getDado('exercicio') > '2010') {
    $stSql .= "\nLEFT JOIN ( SELECT ordem_pagamento_retencao.cod_ordem
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
                    GROUP BY ordem_pagamento_retencao.cod_ordem
                           , ordem_pagamento_retencao.cod_entidade
                           , ordem_pagamento_retencao.exercicio
                  ) AS vl_retencao_orcamentaria
                 ON vl_retencao_orcamentaria.cod_ordem    = ordem_pagamento.cod_ordem
                AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio ";
            }

    $stSql .= "\n WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'

      GROUP BY TipoRegistro
             , CodPrograma
             , CodOrgao
             , CodUnidade
             , CodFuncao
             , nroEmpenho
             , nroOP
             , despesa.num_pao
             , exercicio_empenho
             , implantado
             , dt_empenho
             , vl_pagamento
             , conta_despesa.cod_estrutural
             , cod_subfuncao
             , nota_liquidacao.cod_nota
             , empenho.cod_entidade
             , empenho.cod_empenho
             , num_unidade
             , cod_funcao
             , despesa.cod_programa
             , nota_liquidacao_paga.valor
             , ordem_pagamento.observacao
             , elemento_de_para.estrutural
             , nota_liquidacao_paga.num_documento
             , nota_liquidacao.dt_liquidacao
             , programa.num_programa
             , acao.num_acao ";

        if (Sessao::getExercicio('exercicio') > '2011') {
            $stSql .= "\n, vl_retencao_orcamentaria.vl_retencao
            ORDER BY  codprograma
                    , codunidade
                    , codfuncao
                    , codsubfuncao
                    , naturezaacao
                    , nroprojativ
                    , elementodespesa
                    , subelemento
                    , dotorigp2001
                    , nroempenho
                    , nroop";
        }

    return $stSql;
}

function recuperaOrdemPagamentoFonteRecursos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera('montaRecuperaOrdemPagamentoFonteRecursos',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrdemPagamentoFonteRecursos()
{
    $stSql = "
  SELECT * FROM ( SELECT '13' AS TipoRegistro
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE
                                       LPAD(programa.num_programa::varchar , 4, '0')
                              END)
                         ELSE
                             '0000'
                    END AS CodPrograma
                  , LPAD(despesa.num_orgao::varchar, 2, '0') AS CodOrgao
                  , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE
                                       LPAD(despesa.cod_funcao::varchar   , 02, '0')
                              END)
                         ELSE '00'
                    END AS CodFuncao
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                            (CASE WHEN NOT pre_empenho.implantado THEN
                                      despesa.cod_subfuncao::varchar
                                  ELSE
                                      LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                             END)
                         ELSE
                             '000'
                    END AS CodSubFuncao
                  , LPAD(SUBSTR(acao.num_acao::varchar,1,1), 6, '0') AS NaturezaAcao
                  , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS NroProjAtiv
                  , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6) AS elementodespesa
                  , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)
                        ELSE '00'
                    END AS subelemento
                  , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                             LPAD(despesa.num_unidade::varchar  , 04, '0')
                          || LPAD(despesa.cod_funcao::varchar   , 02, '0')
                          || LPAD(despesa.cod_programa::varchar , 02, '0')
                          || LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                          || LPAD(despesa.num_pao::varchar     , 06, '0')
                          || SUBSTR(REPLACE( conta_despesa.cod_estrutural, '.', ''),1, 6)
                         ELSE
                             LPAD('', 21, '0')
                    END AS DotOrigp2001
                  , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroEmpenho
                  , ordem_pagamento.cod_ordem AS nroop ";
                  if ($this->getDado('exercicio') > '2012') {
                    $stSql .= "
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                             THEN '999'
                             ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                        END AS Banco
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                             THEN '999999'
                             ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                        END AS Agencia
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                             THEN '999999999999'
                             ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', 1), '-', '')), 12, '0')
                        END AS ContaCorrente
                      , LTRIM(split_part(conta_corrente.num_conta_corrente,'-',2),'0') AS contaCorrenteDigVerif
                      , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                  '03'
                             WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                  '02'
                             ELSE
                                  '01'
                        END as tipo_conta ";
                  } else {
                    $stSql .= "
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                             THEN '999'
                             ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                        END AS Banco
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                             THEN '999999'
                             ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                        END AS Agencia
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                             THEN '999999999999'
                             ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', 1), '-', '')), 12, '0')
                        END AS ContaCorrente
                      , LTRIM(split_part(conta_corrente.num_conta_corrente,'-',2),'0') AS contaCorrenteDigVerif
                      , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                    '03'
                               WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                  OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                  OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                    '02'
                               ELSE
                                    '01'
                        END AS tipo_conta ";
                  }
                  $stSql .= "
                  , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                       THEN '999999999999999'
                       ELSE nota_liquidacao_paga.nrdocumento
                  END AS nrdocumento
                  , recurso.cod_fonte AS codFonteRecurso
                  , sum(pagamento_liquidacao.vl_pagamento) AS VlFR
                  , '' AS Brancos
                  , 0 AS numero_sequencial
                  , LPAD(BTRIM(TO_CHAR(valor_total,'9999999999D99')),13,'0') AS valor_total
                  , (sum(pagamento_liquidacao.vl_pagamento) -  coalesce(sum(vl_retencao),0.00)) as vl_retencao
                  , FALSE AS tipo_retencao
               FROM ( SELECT nlp.exercicio
                           , nlp.cod_nota
                           , nlp.cod_entidade
                           , vl_pago as valor
                           , nlcp.cod_plano
                           , tipo_documento.cod_tipo as tipo_doc
                           , tipo_documento.descricao as descricao_doc
                           , pagamento_tipo_documento.num_documento as nrDocumento
                           , nlp.timestamp
                        FROM empenho.nota_liquidacao_paga as nlp

                   LEFT JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                          ON nlp.exercicio    = nlpa.exercicio
                         AND nlp.cod_nota     = nlpa.cod_nota
                         AND nlp.cod_entidade = nlpa.cod_entidade
                         AND nlp.timestamp    = nlpa.timestamp

                  INNER JOIN empenho.nota_liquidacao_conta_pagadora as nlcp
                          ON nlp.exercicio    = nlcp.exercicio_liquidacao
                         AND nlp.cod_nota     = nlcp.cod_nota
                         AND nlp.cod_entidade = nlcp.cod_entidade
                         AND nlp.timestamp    = nlcp.timestamp

                   LEFT JOIN contabilidade.pagamento
                          ON pagamento.exercicio_liquidacao = nlp.exercicio
                         AND pagamento.cod_entidade         = nlp.cod_entidade
                         AND pagamento.cod_nota             = nlp.cod_nota
                         AND pagamento.timestamp            = nlp.timestamp

                   LEFT JOIN contabilidade.lancamento
                          ON lancamento.exercicio    = pagamento.exercicio
                         AND lancamento.cod_entidade = pagamento.cod_entidade
                         AND lancamento.cod_lote     = pagamento.cod_lote
                         AND lancamento.sequencia    = pagamento.sequencia
                         AND lancamento.tipo         = pagamento.tipo

                   LEFT JOIN tesouraria.pagamento_tipo_documento
                          ON tesouraria.pagamento_tipo_documento.cod_nota  = nlp.cod_nota
                         AND tesouraria.pagamento_tipo_documento.exercicio = nlp.exercicio
                         AND pagamento_tipo_documento.timestamp            = nlp.timestamp

                   LEFT JOIN tcmgo.tipo_documento
                          ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento

             WHERE TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                            AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                    ORDER BY cod_nota
                  ) as nota_liquidacao_paga

         INNER JOIN contabilidade.plano_analitica
                 ON plano_analitica.cod_plano = nota_liquidacao_paga.cod_plano
                AND plano_analitica.exercicio = nota_liquidacao_paga.exercicio

         INNER JOIN contabilidade.plano_banco
                 ON plano_analitica.cod_plano = plano_banco.cod_plano
                AND plano_analitica.exercicio = plano_banco.exercicio

         INNER JOIN contabilidade.plano_recurso
                 ON plano_recurso.exercicio = plano_analitica.exercicio
                AND plano_recurso.cod_plano = plano_analitica.cod_plano

         INNER JOIN contabilidade.plano_conta
                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                 AND plano_conta.exercicio = plano_analitica.exercicio

         INNER JOIN orcamento.recurso
                 ON recurso.exercicio   = plano_recurso.exercicio
                AND recurso.cod_recurso = plano_recurso.cod_recurso

         INNER JOIN monetario.conta_corrente
                 ON conta_corrente.cod_banco          = plano_banco.cod_banco
                AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente

         INNER JOIN monetario.agencia
                 ON agencia.cod_banco   = conta_corrente.cod_banco
                AND agencia.cod_agencia = conta_corrente.cod_agencia

         INNER JOIN monetario.banco
                 ON agencia.cod_banco = banco.cod_banco

         INNER JOIN empenho.nota_liquidacao
                 ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota

         INNER JOIN empenho.empenho
                 ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                AND empenho.cod_empenho = nota_liquidacao.cod_empenho

         INNER JOIN empenho.pre_empenho
                 ON pre_empenho.exercicio       = empenho.exercicio
                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

         INNER JOIN orcamento.despesa
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
         
         INNER JOIN orcamento.programa AS o_programa
                 ON o_programa.cod_programa = despesa.cod_programa
                AND o_programa.exercicio = despesa.exercicio
       
         INNER JOIN orcamento.programa_ppa_programa
                 ON programa_ppa_programa.cod_programa = o_programa.cod_programa
                AND programa_ppa_programa.exercicio = o_programa.exercicio
                
         INNER JOIN ppa.programa
                 ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

         INNER JOIN orcamento.conta_despesa
                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

          LEFT JOIN tcmgo.elemento_de_para
                 ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                AND elemento_de_para.exercicio = conta_despesa.exercicio

         INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                 ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

         INNER JOIN empenho.pagamento_liquidacao
                 ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                AND pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio

         INNER JOIN empenho.ordem_pagamento
                 ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

          LEFT JOIN ( SELECT  ordem_pagamento_retencao.cod_ordem
                             ,ordem_pagamento_retencao.cod_entidade
                             ,ordem_pagamento_retencao.exercicio
                             ,SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                        FROM empenho.ordem_pagamento_retencao
                        JOIN contabilidade.plano_analitica
                          ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                         AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                        JOIN contabilidade.plano_conta
                          ON plano_conta.cod_conta = plano_analitica.cod_conta
                         AND plano_conta.exercicio = plano_analitica.exercicio
                    GROUP BY  ordem_pagamento_retencao.cod_ordem
                             ,ordem_pagamento_retencao.cod_entidade
                             ,ordem_pagamento_retencao.exercicio
          ) as retencao
                 ON ordem_pagamento.cod_ordem = retencao.cod_ordem
                AND ordem_pagamento.cod_entidade = retencao.cod_entidade
                AND ordem_pagamento.exercicio = retencao.exercicio

          LEFT JOIN ( SELECT sum(vl_pago) as valor_total
                           , pagamento_tipo_documento.num_documento as nrDocumento
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

                       WHERE TO_DATE(nlp.timestamp::varchar,'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                     AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                    GROUP BY pagamento_tipo_documento.num_documento
                  ) AS total
                 ON total.nrDocumento = nota_liquidacao_paga.nrDocumento
              WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
           GROUP BY nota_liquidacao.exercicio_empenho
                  , despesa.cod_programa
                  , despesa.num_orgao
                  , empenho.cod_empenho
                  , banco.num_banco
                  , agencia.num_agencia
                  , plano_banco.conta_corrente
                  , recurso.cod_fonte
--                  , nota_liquidacao_paga.valor
                  , despesa.num_unidade
--                  , nota_liquidacao.cod_nota
--                  , empenho.cod_entidade
                  , ordem_pagamento.cod_ordem
--                  , pagamento_liquidacao.vl_pagamento
                  , pre_empenho.implantado
                  , despesa.cod_funcao
                  , despesa.cod_subfuncao
                  , despesa.num_pao
                  , conta_corrente.num_conta_corrente
                  , plano_banco.conta_corrente
                  , tipo_conta
                  , tipo_doc
                  , descricao_doc
                  , conta_despesa.cod_estrutural
                  , nota_liquidacao_paga.nrdocumento
                  , nota_liquidacao_paga.timestamp
                  , valor_total
                  , elemento_de_para.estrutural
                  , vl_retencao
                  , plano_conta.cod_estrutural
                  , programa.num_programa
                  , acao.num_acao ";

    if ($this->getDado('exercicio') > 2011) {
        $stSql .= "\nUNION
                SELECT '13' AS TipoRegistro
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE
                                       LPAD(programa.num_programa::varchar , 4, '0')
                              END)
                         ELSE
                             '0000'
                    END AS CodPrograma
                  , LPAD(despesa.num_orgao::varchar, 2, '0') AS CodOrgao
                  , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE
                                       LPAD(despesa.cod_funcao::varchar   , 02, '0')
                              END)
                         ELSE '00'
                    END AS CodFuncao
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                            (CASE WHEN NOT pre_empenho.implantado THEN
                                      despesa.cod_subfuncao::varchar
                                  ELSE
                                      LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                             END)
                         ELSE
                             '000'
                    END AS CodSubFuncao
                  , LPAD(SUBSTR(acao.num_acao::varchar,1,1), 6, '0') AS NaturezaAcao
                  , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS NroProjAtiv
                  , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elementodespesa
                  , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                    END AS subelemento
                  , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                             LPAD(despesa.num_unidade::varchar  , 04, '0')
                          || LPAD(despesa.cod_funcao::varchar   , 02, '0')
                          || LPAD(despesa.cod_programa::varchar , 02, '0')
                          || LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                          || LPAD(despesa.num_pao::varchar     , 06, '0')
                          || SUBSTR(REPLACE( conta_despesa.cod_estrutural::varchar, '.', ''),1, 6)
                         ELSE
                             LPAD('', 21, '0')
                    END AS DotOrigp2001
                  , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroEmpenho
                  , ordem_pagamento.cod_ordem AS nroop ";
                  if ($this->getDado('exercicio') > '2012') {
                    $stSql .= "
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                             THEN '999'
                             ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                        END AS Banco
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                             THEN '999999'
                             ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                        END AS Agencia
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                             THEN '999999999999'
                             ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', 1), '-', '')), 12, '0')
                        END AS ContaCorrente
                      , LTRIM(split_part(conta_corrente.num_conta_corrente,'-',2),'0') AS contaCorrenteDigVerif
                      , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                  '03'
                             WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                  '02'
                             ELSE
                                  '01'
                        END as tipo_conta ";
                  } else {
                    $stSql .= "
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                             THEN '999'
                             ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                        END AS Banco
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                             THEN '999999'
                             ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                        END AS Agencia
                      , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                             THEN '999999999999'
                             ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', 1), '-', '')), 12, '0')
                        END AS ContaCorrente
                      , LTRIM(split_part(conta_corrente.num_conta_corrente,'-',2),'0') AS contaCorrenteDigVerif
                      , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                    '03'
                               WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                  OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                  OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                    '02'
                               ELSE
                                    '01'
                        END AS tipo_conta ";
                  }
                  $stSql .= "
                  , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                       THEN '999999999999999'
                       ELSE nota_liquidacao_paga.nrdocumento
                  END AS nrdocumento
                  , recurso.cod_fonte AS codFonteRecurso
                  , sum(pagamento_liquidacao.vl_pagamento) AS VlFR
                  , '' AS Brancos
                  , 0 AS numero_sequencial
                  , LPAD(BTRIM(TO_CHAR(valor_total,'9999999999D99')),13,'0') AS valor_total
                  , retencao.vl_retencao as vl_retencao
                  , TRUE AS tipo_retencao
               FROM ( SELECT nlp.exercicio
                           , nlp.cod_nota
                           , nlp.cod_entidade
                           , vl_pago as valor
                           , nlcp.cod_plano
                           , tipo_documento.cod_tipo as tipo_doc
                           , tipo_documento.descricao as descricao_doc
                           , pagamento_tipo_documento.num_documento as nrDocumento
                           , nlp.timestamp
                        FROM empenho.nota_liquidacao_paga as nlp

                   LEFT JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                          ON nlp.exercicio    = nlpa.exercicio
                         AND nlp.cod_nota     = nlpa.cod_nota
                         AND nlp.cod_entidade = nlpa.cod_entidade
                         AND nlp.timestamp    = nlpa.timestamp

                  INNER JOIN empenho.nota_liquidacao_conta_pagadora as nlcp
                          ON nlp.exercicio    = nlcp.exercicio_liquidacao
                         AND nlp.cod_nota     = nlcp.cod_nota
                         AND nlp.cod_entidade = nlcp.cod_entidade
                         AND nlp.timestamp    = nlcp.timestamp

                   LEFT JOIN contabilidade.pagamento
                          ON pagamento.exercicio_liquidacao = nlp.exercicio
                         AND pagamento.cod_entidade         = nlp.cod_entidade
                         AND pagamento.cod_nota             = nlp.cod_nota
                         AND pagamento.timestamp            = nlp.timestamp

                   LEFT JOIN contabilidade.lancamento
                          ON lancamento.exercicio    = pagamento.exercicio
                         AND lancamento.cod_entidade = pagamento.cod_entidade
                         AND lancamento.cod_lote     = pagamento.cod_lote
                         AND lancamento.sequencia    = pagamento.sequencia
                         AND lancamento.tipo         = pagamento.tipo

                   LEFT JOIN tesouraria.pagamento_tipo_documento
                          ON tesouraria.pagamento_tipo_documento.cod_nota  = nlp.cod_nota
                         AND tesouraria.pagamento_tipo_documento.exercicio = nlp.exercicio
                         AND pagamento_tipo_documento.timestamp            = nlp.timestamp

                   LEFT JOIN tcmgo.tipo_documento
                          ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento

             WHERE TO_DATE(nlp.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                            AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                    ORDER BY cod_nota
                  ) as nota_liquidacao_paga

         INNER JOIN contabilidade.plano_analitica
                 ON plano_analitica.cod_plano = nota_liquidacao_paga.cod_plano
                AND plano_analitica.exercicio = nota_liquidacao_paga.exercicio

         INNER JOIN contabilidade.plano_banco
                 ON plano_analitica.cod_plano = plano_banco.cod_plano
                AND plano_analitica.exercicio = plano_banco.exercicio

         INNER JOIN contabilidade.plano_recurso
                 ON plano_recurso.exercicio = plano_analitica.exercicio
                AND plano_recurso.cod_plano = plano_analitica.cod_plano

         INNER JOIN contabilidade.plano_conta
                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                 AND plano_conta.exercicio = plano_analitica.exercicio

         INNER JOIN orcamento.recurso
                 ON recurso.exercicio   = plano_recurso.exercicio
                AND recurso.cod_recurso = plano_recurso.cod_recurso

         INNER JOIN monetario.conta_corrente
                 ON conta_corrente.cod_banco          = plano_banco.cod_banco
                AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente

         INNER JOIN monetario.agencia
                 ON agencia.cod_banco   = conta_corrente.cod_banco
                AND agencia.cod_agencia = conta_corrente.cod_agencia

         INNER JOIN monetario.banco
                 ON agencia.cod_banco = banco.cod_banco

         INNER JOIN empenho.nota_liquidacao
                 ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota

         INNER JOIN empenho.empenho
                 ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                AND empenho.cod_empenho = nota_liquidacao.cod_empenho

         INNER JOIN empenho.pre_empenho
                 ON pre_empenho.exercicio       = empenho.exercicio
                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

         INNER JOIN orcamento.despesa
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
         
         INNER JOIN orcamento.programa AS o_programa
                 ON o_programa.cod_programa = despesa.cod_programa
                AND o_programa.exercicio = despesa.exercicio
       
         INNER JOIN orcamento.programa_ppa_programa
                 ON programa_ppa_programa.cod_programa = o_programa.cod_programa
                AND programa_ppa_programa.exercicio = o_programa.exercicio
                
         INNER JOIN ppa.programa
                 ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

         INNER JOIN orcamento.conta_despesa
                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

          LEFT JOIN tcmgo.elemento_de_para
                 ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                AND elemento_de_para.exercicio = conta_despesa.exercicio

         INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                 ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

         INNER JOIN empenho.pagamento_liquidacao
                 ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                AND pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio

         INNER JOIN empenho.ordem_pagamento
                 ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

          LEFT JOIN ( SELECT  ordem_pagamento_retencao.cod_ordem
                             ,ordem_pagamento_retencao.cod_entidade
                             ,ordem_pagamento_retencao.exercicio
                             ,SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                        FROM empenho.ordem_pagamento_retencao
                        JOIN contabilidade.plano_analitica
                          ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                         AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                        JOIN contabilidade.plano_conta
                          ON plano_conta.cod_conta = plano_analitica.cod_conta
                         AND plano_conta.exercicio = plano_analitica.exercicio
                    GROUP BY  ordem_pagamento_retencao.cod_ordem
                             ,ordem_pagamento_retencao.cod_entidade
                             ,ordem_pagamento_retencao.exercicio
          ) as retencao
                 ON ordem_pagamento.cod_ordem = retencao.cod_ordem
                AND ordem_pagamento.cod_entidade = retencao.cod_entidade
                AND ordem_pagamento.exercicio = retencao.exercicio

          LEFT JOIN ( SELECT sum(vl_pago) as valor_total
                           , pagamento_tipo_documento.num_documento as nrDocumento
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

                       WHERE TO_DATE(nlp.timestamp::varchar,'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                     AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                    GROUP BY pagamento_tipo_documento.num_documento
                  ) AS total
                 ON total.nrDocumento = nota_liquidacao_paga.nrDocumento
              WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
           GROUP BY nota_liquidacao.exercicio_empenho
                  , despesa.cod_programa
                  , despesa.num_orgao
                  , empenho.cod_empenho
                  , banco.num_banco
                  , agencia.num_agencia
                  , plano_banco.conta_corrente
                  , recurso.cod_fonte
                  , despesa.num_unidade
                  , ordem_pagamento.cod_ordem
                  , pre_empenho.implantado
                  , despesa.cod_funcao
                  , despesa.cod_subfuncao
                  , despesa.num_pao
                  , conta_corrente.num_conta_corrente
                  , plano_banco.conta_corrente
                  , tipo_conta
                  , tipo_doc
                  , descricao_doc
                  , conta_despesa.cod_estrutural
                  , nota_liquidacao_paga.nrdocumento
                  , nota_liquidacao_paga.timestamp
                  , valor_total
                  , elemento_de_para.estrutural
                  , vl_retencao
                  , plano_conta.cod_estrutural
                  , programa.num_programa
                  , acao.num_acao ";
    }

    $stSql .="\n) as tbl";

    if ($this->getDado('exercicio') > 2011) {
    $stSql .= "\nORDER BY codprograma
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
                        , nrdocumento
                        , banco
                        , agencia
                        , contacorrente
                        , tipo_retencao";
    }

    return $stSql;
}

function recuperaOrdemPagamentoMovimentacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera('montaRecuperaOrdemPagamentoMovimentacao',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrdemPagamentoMovimentacao()
{
    $stSql = "
        SELECT '11' AS TipoRegistro
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                        (CASE WHEN NOT pre_empenho.implantado THEN
                                 pre_empenho_despesa.cod_programa
                              ELSE
                                 LPAD(restos_pre_empenho.cod_programa::varchar, 02, '0')
                         END)
                    ELSE '0000'
               END AS CodPrograma
             , pre_empenho_despesa.num_orgao AS CodOrgao
             , pre_empenho_despesa.num_unidade AS CodUnidade
             , TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho) AS numeroliquidacao
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                        (CASE WHEN NOT pre_empenho.implantado THEN
                                 pre_empenho_despesa.cod_funcao
                              ELSE
                                 LPAD(restos_pre_empenho.cod_funcao::varchar, 02, '0')
                         END)
                    ELSE '00'
               END AS CodFuncao
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                        (CASE WHEN NOT pre_empenho.implantado THEN
                                 pre_empenho_despesa.cod_subfuncao
                              ELSE
                                 LPAD(restos_pre_empenho.cod_subfuncao::varchar, 03, '0')
                         END)
                    ELSE '000'
               END AS CodSubFuncao
             , SUBSTR(CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                               (CASE WHEN NOT pre_empenho.implantado THEN
                                         SUBSTR(pre_empenho_despesa.num_pao::varchar,1,1)
                                     ELSE
                                         LPAD(restos_pre_empenho.num_pao::varchar, 06, '0')
                                END) ELSE '0000'
                      END,1,1) AS NaturezaAcao
             , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                        (CASE WHEN NOT pre_empenho.implantado
                                  THEN SUBSTR(pre_empenho_despesa.num_pao::varchar,2,3)
                              ELSE
                                  LPAD(restos_pre_empenho.num_pao::varchar, 06, '0')
                         END)
                    ELSE '0000'
               END AS NroProjAtiv
             , SUBSTR((CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                               (CASE WHEN NOT pre_empenho.implantado THEN
                                         pre_empenho_despesa.elemento_e_sub_despesa
                                     ELSE restos_pre_empenho.cod_estrutural
                                END)
                           ELSE '00000000'
                      END)::varchar,1,6) AS elementoDespesa
             , SUBSTR((CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                               (CASE WHEN NOT pre_empenho.implantado THEN
                                         sub_elemento_despesa.sub_elemento_despesa
                                     ELSE
                                         restos_pre_empenho.cod_estrutural
                                END)
                           ELSE '00000000'
                      END)::varchar,7,2) AS subElemento
             , SUBSTR((CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                               (sub_elemento_despesa.sub_elemento_despesa)
                           ELSE '00000000'
                      END)::varchar,7,2) AS subElemento
             , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                        LPAD(restos_pre_empenho.num_unidade::varchar  , 04, '0')
                     || LPAD(restos_pre_empenho.cod_funcao::varchar   , 02, '0')
                     || LPAD(restos_pre_empenho.cod_programa::varchar , 02, '0')
                     || LPAD(restos_pre_empenho.cod_subfuncao::varchar, 03, '0')
                     || LPAD(restos_pre_empenho.num_pao::varchar      , 06, '0')
                     || SUBSTR(REPLACE( restos_pre_empenho.cod_estrutural::varchar, '.', ''), 1, 6)
                    ELSE
                        LPAD('', 21, '0')
               END AS DotOrigp2001
             , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroEmpenho
             , ordem_pagamento.cod_ordem AS nroOP
             , CASE WHEN SUBSTR(REPLACE( restos_pre_empenho.cod_estrutural::varchar, '.', ''),1,6) = 11111 THEN
                        LPAD('',3,'9')
                    WHEN (SUBSTR(plano_conta.cod_estrutural::varchar,1,9) = '1.1.1.1.1') THEN
                        '999'
                    ELSE
                        LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
               END  AS Banco
             , CASE WHEN SUBSTR(REPLACE( restos_pre_empenho.cod_estrutural::varchar, '.', ''),1,6) = 11111 THEN
                        LPAD('',6,'9')
                    WHEN (SUBSTR(plano_conta.cod_estrutural::varchar,1,9) = '1.1.1.1.1') THEN
                        '999999'
                    ELSE
                        LPAD(BTRIM(REPLACE(COALESCE(banco.num_agencia, '0')::varchar,'-','')), 6, '0')
               END AS Agencia
             , CASE WHEN SUBSTR(REPLACE( restos_pre_empenho.cod_estrutural, '.', ''),1,6) = 11111 THEN
                        LPAD('',12,'9')
                    WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1') THEN
                        '999999999999'
                    ELSE
                        LPAD(BTRIM(REPLACE(COALESCE(banco.conta_corrente,'0'), '-', '')), 12, '0')
                    END  AS ContaCorrente
             , LPAD('', 15, '9') AS nrcheque
             , LPAD(BTRIM(TO_CHAR( SUM(nota_liquidacao_paga.vl_pago), '9999999999D99')),13,'0') AS VlCheque
             , TO_CHAR(nota_liquidacao_paga.timestamp,'ddmmyyyy') AS DtEmissao
             , '' AS Brancos
             , 0 AS numero_sequencial
          FROM empenho.ordem_pagamento
             , empenho.pagamento_liquidacao
             , empenho.pagamento_liquidacao_nota_liquidacao_paga
             , empenho.nota_liquidacao_paga
     LEFT JOIN ( SELECT nota_liquidacao_conta_pagadora.exercicio_liquidacao
                      , nota_liquidacao_conta_pagadora.cod_entidade
                      , nota_liquidacao_conta_pagadora.cod_nota
                      , nota_liquidacao_conta_pagadora.timestamp
                      , banco.num_banco
                      , agencia.num_agencia
                      , plano_banco.conta_corrente
                   FROM empenho.nota_liquidacao_conta_pagadora
                      , contabilidade.plano_analitica
                      , contabilidade.plano_banco
                      , monetario.agencia
                      , monetario.banco
                  WHERE nota_liquidacao_conta_pagadora.exercicio = plano_analitica.exercicio
                    AND nota_liquidacao_conta_pagadora.cod_plano = plano_analitica.cod_plano
                    AND plano_analitica.exercicio                = plano_banco.exercicio
                    AND plano_analitica.cod_plano                = plano_banco.cod_plano
                    AND plano_banco.cod_banco                    = agencia.cod_banco
                    AND plano_banco.cod_agencia                  = agencia.cod_agencia
                    AND monetario.agencia.cod_banco              = banco.cod_banco
             ) AS banco
            ON nota_liquidacao_paga.exercicio    = banco.exercicio_liquidacao
           AND nota_liquidacao_paga.cod_entidade = banco.cod_entidade
           AND nota_liquidacao_paga.cod_nota     = banco.cod_nota
           AND nota_liquidacao_paga.timestamp    = banco.timestamp
             , empenho.nota_liquidacao
             , empenho.empenho
             , empenho.pre_empenho
     LEFT JOIN empenho.restos_pre_empenho
            ON restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND restos_pre_empenho.exercicio       = pre_empenho.exercicio

     LEFT JOIN ( SELECT pre_empenho_despesa.exercicio
                      , pre_empenho_despesa.cod_pre_empenho
                      , LPAD(despesa.cod_programa ,4,'0') AS cod_programa
                      , LPAD(despesa.num_orgao    ,2,'0') AS num_orgao
                      , LPAD(despesa.num_unidade  ,2,'0') AS num_unidade
                      , LPAD(despesa.cod_funcao   ,2,'0') AS cod_funcao
                      , LPAD(despesa.cod_subfuncao,3,'0') AS cod_subfuncao
                      , LPAD(despesa.num_pao      ,4,'0') AS num_pao
                      , SUBSTR(REPLACE( cod_estrutural, '.', ''), 1, 8) AS elemento_e_sub_despesa
                   FROM empenho.pre_empenho_despesa
                      , orcamento.despesa
                      , orcamento.conta_despesa
                  WHERE pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                    AND pre_empenho_despesa.exercicio   = despesa.exercicio
                    AND despesa.exercicio               = conta_despesa.exercicio
                    AND despesa.cod_conta               = conta_despesa.cod_conta
             ) AS pre_empenho_despesa
            ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
           AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
     LEFT JOIN ( SELECT pre_empenho_despesa.cod_pre_empenho
                      , pre_empenho_despesa.exercicio
                      , conta_despesa.cod_conta
                      , SUBSTR(REPLACE( elemento_de_para.estrutural, '.', ''), 1, 8)  AS sub_elemento_despesa
                   FROM empenho.pre_empenho_despesa
             INNER JOIN orcamento.conta_despesa
                     ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
                    AND conta_despesa.exercicio = pre_empenho_despesa.exercicio
              LEFT JOIN tcmgo.elemento_de_para
                     ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                    AND elemento_de_para.exercicio = conta_despesa.exercicio
             ) AS sub_elemento_despesa
            ON sub_elemento_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
           AND sub_elemento_despesa.exercicio       = pre_empenho.exercicio
         WHERE ordem_pagamento.cod_ordem                 = pagamento_liquidacao.cod_ordem
           AND ordem_pagamento.exercicio                 = pagamento_liquidacao.exercicio
           AND ordem_pagamento.cod_entidade              = pagamento_liquidacao.cod_entidade
           AND pagamento_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio
           AND pagamento_liquidacao.cod_entidade         = nota_liquidacao.cod_entidade
           AND pagamento_liquidacao.cod_nota             = nota_liquidacao.cod_nota
           AND nota_liquidacao.exercicio_empenho         = empenho.exercicio
           AND nota_liquidacao.cod_entidade              = empenho.cod_entidade
           AND nota_liquidacao.cod_empenho               = empenho.cod_empenho
           AND empenho.exercicio                         = pre_empenho.exercicio
           AND empenho.cod_pre_empenho                   = pre_empenho.cod_pre_empenho
           AND pagamento_liquidacao.exercicio            = pagamento_liquidacao_nota_liquidacao_paga.exercicio
           AND pagamento_liquidacao.cod_entidade         = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
           AND pagamento_liquidacao.cod_ordem            = pagamento_liquidacao_nota_liquidacao_paga.cod_ordem
           AND pagamento_liquidacao.exercicio_liquidacao = pagamento_liquidacao_nota_liquidacao_paga.exercicio
           AND pagamento_liquidacao.cod_nota             = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = nota_liquidacao_paga.cod_entidade
           AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = nota_liquidacao_paga.cod_nota
           AND pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = nota_liquidacao_paga.exercicio
           AND pagamento_liquidacao_nota_liquidacao_paga.timestamp            = nota_liquidacao_paga.timestamp
        ---------------------------------------------------------------------------------------------
        --Filtros
        ---------------------------------------------------------------------------------------------
           AND TO_DATE(pagamento_liquidacao_nota_liquidacao_paga.timestamp, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                                              AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
           AND ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
           AND ordem_pagamento.cod_entidade IN (".$this->getDado('cod_entidade').")
      GROUP BY nota_liquidacao.exercicio_empenho
             , nota_liquidacao.cod_nota
             , empenho.cod_entidade
             , pre_empenho.implantado
             , pre_empenho_despesa.cod_programa
             , restos_pre_empenho.cod_programa
             , pre_empenho_despesa.num_orgao
             , pre_empenho_despesa.num_unidade
             , pre_empenho_despesa.cod_funcao
             , restos_pre_empenho.cod_funcao
             , pre_empenho_despesa.cod_subfuncao
             , restos_pre_empenho.cod_subfuncao
             , pre_empenho_despesa.num_pao
             , restos_pre_empenho.num_pao
             , pre_empenho_despesa.elemento_e_sub_despesa
             , restos_pre_empenho.cod_estrutural
             , sub_elemento_despesa.sub_elemento_despesa
             , restos_pre_empenho.num_unidade
             , empenho.cod_empenho
             , ordem_pagamento.cod_ordem
             , banco.num_banco
             , banco.num_agencia
             , banco.conta_corrente
             , DtEmissao
             , plano_conta.cod_estrutural";

    if ($this->getDado('exercicio') > 2011) {
        $stSql .= "\nORDER BY codprograma
                            , codunidade
                            , codfuncao
                            , codsubfuncao
                            , naturezaacao
                            , nroprojativ
                            , elementodespesa
                            , subelemento
                            , dotorigp2001
                            , nroempenho
                            , nroop";
    }

    return $stSql;
}

function recuperaOrdemPagamentoMovimentacao2009(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera('montaRecuperaOrdemPagamentoMovimentacao2009',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaOrdemPagamentoMovimentacao2009()
{
    if ($this->getDado('exercicio') > '2010') {
        $stSql = " SELECT * FROM ( SELECT '12' AS tiporegistro, ltrim(split_part(num_conta_corrente,'-',2),'0') AS contacorrentedigverif ";
    } else {
        $stSql = " SELECT * FROM ( SELECT '11' AS tiporegistro ";
    }
    if ($this->getDado('exercicio') > '2012') {
        $stSql .= "
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                    '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                    '02'
                               ELSE
                                    '01'
                          END as tipo_conta ";;
    } else {
      $stSql .= "
                    , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.1') THEN
                                  '03'
                             WHEN ((substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.3')
                                OR (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.5')
                                OR (substr(plano_conta.cod_estrutural, 1, 9) = '1.1.1.1.4')) THEN
                                  '02'
                             ELSE
                                  '01'
                        END AS tipo_conta ";
    }
    $stSql .= "
                  , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                       THEN '99'
                       ELSE tipo_doc
                    END AS tipo_doc
                  , descricao_doc
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE
                                       LPAD(programa.num_programa::varchar , 4, '0')
                              END)
                         ELSE
                             '0000'
                    END AS codprograma
                  , LPAD(despesa.num_orgao::varchar, 2, '0') AS codorgao
                  , LPAD(despesa.num_unidade::varchar, 2, '0') AS codunidade
                  , ordem_pagamento.cod_ordem AS nroop
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE
                                       LPAD(despesa.cod_funcao::varchar   , 02, '0')
                              END)
                         ELSE '00'
                    END AS codfuncao
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                            (CASE WHEN NOT pre_empenho.implantado THEN
                                      despesa.cod_subfuncao::varchar
                                  ELSE
                                      LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                             END)
                         ELSE
                             '000'
                    END AS codsubfuncao
                  , LPAD(SUBSTR(acao.num_acao::varchar,1,1), 6, '0') AS naturezaacao
                  , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS nroprojativ
                  , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elementodespesa
                  , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                        THEN SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                        ELSE '00'
                    END AS subelemento
                  , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                             LPAD(despesa.num_unidade::varchar  , 04, '0')
                          || LPAD(despesa.cod_funcao::varchar   , 02, '0')
                          || LPAD(despesa.cod_programa::varchar , 02, '0')
                          || LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                          || LPAD(despesa.num_pao::varchar     , 06, '0')
                          || SUBSTR(REPLACE( conta_despesa.cod_estrutural::varchar, '.', ''),1, 6)
                         ELSE
                             LPAD('', 21, '0')
                    END AS dotorigp2001
                  , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroempenho";
                  if ($this->getDado('exercicio') < '2011') {
                    $stSql .= ", CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                                       THEN '999'
                                       ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                                  END AS banco
                                , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                                       THEN '999999'
                                       ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                                  END AS agencia
                                 , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                                      THEN '999999999999'
                                      ELSE LPAD(BTRIM(REPLACE(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', '')), 12, '0')
                                 END AS contacorrente";
                    $stSql .= ", LPAD(BTRIM(TO_CHAR((nota_liquidacao_paga.valor),'9999999999D99')),13,'0') AS vldocumento";
                  } else {
                    $stSql .= ", CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
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
                                 END AS contacorrente";
                    $stSql .= "--, LPAD(BTRIM(TO_CHAR((sum(nota_liquidacao_paga.valor)),'9999999999D99')),13,'0') AS vldocumento
                               , SUM(COALESCE(nota_liquidacao_paga.valor,0)) AS vldocumento";
                  }
                    $stSql .= "
                  , TO_CHAR(nota_liquidacao_paga.timestamp,'ddmmyyyy') AS DtEmissao
                  --, nota_liquidacao_paga.nrdocumento
                  , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                       THEN '999999999999999'
                       ELSE nota_liquidacao_paga.nrdocumento
                  END AS nrdocumento
                  , '' AS Brancos
                  , 0 AS numero_sequencial
                  , (SUM(COALESCE(nota_liquidacao_paga.valor,0)) - vl_retencao_orcamentaria.vl_retencao) AS vl_associado
                  /*,  LPAD(BTRIM(TO_CHAR(nota_liquidacao_paga.vlassociado,'9999999999D99')),13,'0') AS valor_total*/
                  , LPAD(BTRIM(TO_CHAR(valor_total,'9999999999D99')),13,'0') AS valor_total";

                  if ($this->getDado('exercicio') > '2011') {
                    $stSql .= "\n, FALSE AS vlretencao";
                  }
    $stSql .= "\nFROM ( SELECT nlp.exercicio
                           , nlp.cod_nota
                           , nlp.cod_entidade
                           , vl_pago as valor
                           , nlcp.cod_plano
                           , tipo_documento.cod_tipo as tipo_doc
                           , tipo_documento.descricao as descricao_doc
                           , pagamento_tipo_documento.num_documento as nrdocumento
                           , nlp.timestamp
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
                            ) AS vlassociado

                        FROM empenho.nota_liquidacao_paga as nlp

                   LEFT JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                          ON nlp.exercicio    = nlpa.exercicio
                         AND nlp.cod_nota     = nlpa.cod_nota
                         AND nlp.cod_entidade = nlpa.cod_entidade
                         AND nlp.timestamp    = nlpa.timestamp

                  INNER JOIN empenho.nota_liquidacao_conta_pagadora as nlcp
                          ON nlp.exercicio    = nlcp.exercicio_liquidacao
                         AND nlp.cod_nota     = nlcp.cod_nota
                         AND nlp.cod_entidade = nlcp.cod_entidade
                         AND nlp.timestamp    = nlcp.timestamp

                   LEFT JOIN contabilidade.pagamento
                          ON pagamento.exercicio_liquidacao = nlp.exercicio
                         AND pagamento.cod_entidade         = nlp.cod_entidade
                         AND pagamento.cod_nota             = nlp.cod_nota
                         AND pagamento.timestamp            = nlp.timestamp

                   LEFT JOIN contabilidade.lancamento
                          ON lancamento.exercicio    = pagamento.exercicio
                         AND lancamento.cod_entidade = pagamento.cod_entidade
                         AND lancamento.cod_lote     = pagamento.cod_lote
                         AND lancamento.sequencia    = pagamento.sequencia
                         AND lancamento.tipo         = pagamento.tipo

                   LEFT JOIN tesouraria.pagamento_tipo_documento
                          ON tesouraria.pagamento_tipo_documento.cod_nota  = nlp.cod_nota
                         AND tesouraria.pagamento_tipo_documento.exercicio = nlp.exercicio
                         AND pagamento_tipo_documento.timestamp            = nlp.timestamp

                   LEFT JOIN tcmgo.tipo_documento
                          ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento

                       WHERE TO_DATE(nlp.timestamp::varchar,'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                        AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
              GROUP BY nlp.exercicio
                     , nlp.cod_nota
                     , nlp.cod_entidade
                     , nlcp.cod_plano
                     , tipo_doc
                     , nrdocumento
                     , nlp.timestamp

                    ORDER BY cod_nota
                    ) as nota_liquidacao_paga

         INNER JOIN contabilidade.plano_analitica
                 ON plano_analitica.cod_plano = nota_liquidacao_paga.cod_plano
                AND plano_analitica.exercicio = nota_liquidacao_paga.exercicio

         INNER JOIN contabilidade.plano_banco
                 ON plano_analitica.cod_plano = plano_banco.cod_plano
                AND plano_analitica.exercicio = plano_banco.exercicio

         INNER JOIN monetario.conta_corrente
                 ON conta_corrente.cod_banco          = plano_banco.cod_banco
                AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                AND conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente

         INNER JOIN  contabilidade.plano_conta
                 ON  plano_conta.cod_conta = plano_analitica.cod_conta
                AND  plano_conta.exercicio = plano_analitica.exercicio

         INNER JOIN monetario.agencia
                 ON agencia.cod_banco   = conta_corrente.cod_banco
                AND agencia.cod_agencia = conta_corrente.cod_agencia

         INNER JOIN monetario.banco
                 ON agencia.cod_banco = banco.cod_banco

         INNER JOIN empenho.nota_liquidacao
                 ON nota_liquidacao.exercicio = nota_liquidacao_paga.exercicio
                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao.cod_nota = nota_liquidacao_paga.cod_nota

         INNER JOIN empenho.empenho
                 ON empenho.exercicio = nota_liquidacao.exercicio_empenho
                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                AND empenho.cod_empenho = nota_liquidacao.cod_empenho

         INNER JOIN empenho.pre_empenho
                 ON pre_empenho.exercicio       = empenho.exercicio
                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

         INNER JOIN orcamento.despesa
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
         
         INNER JOIN orcamento.programa AS o_programa
                 ON o_programa.cod_programa = despesa.cod_programa
                AND o_programa.exercicio = despesa.exercicio
       
         INNER JOIN orcamento.programa_ppa_programa
                 ON programa_ppa_programa.cod_programa = o_programa.cod_programa
                AND programa_ppa_programa.exercicio = o_programa.exercicio
                
         INNER JOIN ppa.programa
                 ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa

         INNER JOIN orcamento.conta_despesa
                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

          LEFT JOIN tcmgo.elemento_de_para
                 ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                AND elemento_de_para.exercicio = conta_despesa.exercicio

         INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                 ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp

         INNER JOIN empenho.pagamento_liquidacao
                 ON pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade = pagamento_liquidacao.cod_entidade
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota     = pagamento_liquidacao.cod_nota
                AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem    = pagamento_liquidacao.cod_ordem
                AND pagamento_liquidacao_nota_liquidacao_paga.exercicio    = pagamento_liquidacao.exercicio

         INNER JOIN empenho.ordem_pagamento
                 ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem

          LEFT JOIN ( SELECT sum(vl_pago) as valor_total
                           , pagamento_tipo_documento.num_documento as nrDocumento
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

                       WHERE TO_DATE(nlp.timestamp::varchar,'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                     AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')

                    GROUP BY pagamento_tipo_documento.num_documento
                  ) AS total
                 ON total.nrDocumento = nota_liquidacao_paga.nrDocumento ";

            if ($this->getDado('exercicio') > '2010') {
                $stSql .= "
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
                       --WHERE SUBSTR(plano_conta.cod_estrutural, 1, 1) = '4'
                    GROUP BY ordem_pagamento_retencao.cod_ordem
                           , ordem_pagamento_retencao.cod_entidade
                           , ordem_pagamento_retencao.exercicio
                  ) AS vl_retencao_orcamentaria
                 ON vl_retencao_orcamentaria.cod_ordem    = ordem_pagamento.cod_ordem
                AND vl_retencao_orcamentaria.cod_entidade = ordem_pagamento.cod_entidade
                AND vl_retencao_orcamentaria.exercicio    = ordem_pagamento.exercicio ";
            }

            $stSql .= "
              WHERE (to_char(ordem_pagamento.dt_emissao, 'yyyy'))::integer = '".$this->getDado('exercicio')."'
                AND nota_liquidacao_paga.valor > 0
                --AND total.valor_total IS NOT NULL
           GROUP BY nota_liquidacao.exercicio_empenho
                  , despesa.cod_programa
                  , despesa.num_orgao
                  , empenho.cod_empenho
                  , banco.num_banco
                  , agencia.num_agencia
                  , plano_banco.conta_corrente";
            if ($this->getDado('exercicio') > '2010') {
                $stSql .= ", vl_retencao_orcamentaria.vl_retencao ";
            }
            $stSql .= "
                  , despesa.num_unidade
                  , ordem_pagamento.cod_ordem
                  , pre_empenho.implantado
                  , despesa.cod_funcao
                  , despesa.cod_subfuncao
                  , despesa.num_pao
                  , conta_corrente.num_conta_corrente
                  , tipo_conta
                  , plano_banco.conta_corrente
                  , tipo_doc
                  , descricao_doc
                  , conta_despesa.cod_estrutural
                  , nota_liquidacao_paga.nrdocumento
                  , TO_CHAR(nota_liquidacao_paga.timestamp,'ddmmyyyy')
                  , valor_total
                  , elemento_de_para.estrutural
                  , plano_conta.cod_estrutural
                  , nota_liquidacao_paga.vlassociado
                  , programa.num_programa
                  , acao.num_acao ";

    $stSql .="\n) as tbl";

    if ($this->getDado('exercicio') > 2011) {
        $stSql .= "\nORDER BY codprograma
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
                            , nrdocumento
                            , banco
                            , agencia
                            , contacorrente
                            , vlretencao";
    }

    return $stSql;
}

//LAYOUT NOVO A PARTIR DE 2009
function montaRecuperaOrdemPagamentoRetencao()
{
    $stSql = "
    SELECT tiporegistro
         , cod_estrutural
         , tipo_retencao
         , cod_ordem
         , numeroliquidacao
         , codprograma
         , naturezaacao
         , nroprojativ
         , codorgao
         , codunidade
         , codfuncao
         , codsubfuncao
         , elementodespesa
         , subelemento
         , dotorigp2001
         , nroempenho
         , nroop
         , banco
         , agencia
         , contacorrente
         , sum(vlretencao) as vlretencao
         , dtemissao
         , brancos
         , numero_sequencial
         , nom_conta 
         , nrextraorcamentaria ";

    if ($this->getDado('exercicio') > '2010') {
        $stSql .= " FROM ( SELECT '14'::varchar AS TipoRegistro
                  , SUBSTR((CASE WHEN de_para_tipo_retencao.cod_tipo IS NOT NULL THEN
                                    de_para_tipo_retencao.cod_tipo::integer
                                WHEN balancete_extmmaa.sub_tipo_lancamento IS NOT NULL THEN
                                    CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN
                                             2::integer
                                         WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN
                                             4::integer
                                         WHEN balancete_extmmaa.sub_tipo_lancamento = 4 THEN
                                             3::integer
                                         WHEN balancete_extmmaa.sub_tipo_lancamento NOT IN (1,2,3,4) THEN
                                             99::integer
                                         ELSE
                                             balancete_extmmaa.sub_tipo_lancamento::integer
                                    END
                                ELSE
                                    NULL
                           END)::varchar, 1, 2) AS tipo_retencao ";
    } else {
        $stSql .= " FROM ( SELECT '12'::varchar AS TipoRegistro
                  , SUBSTR((CASE WHEN de_para_tipo_retencao.cod_tipo IS NOT NULL THEN
                                    de_para_tipo_retencao.cod_tipo::integer
                                WHEN balancete_extmmaa.sub_tipo_lancamento IS NOT NULL THEN
                                    CASE WHEN balancete_extmmaa.sub_tipo_lancamento = 3 THEN
                                             2::integer
                                         WHEN balancete_extmmaa.sub_tipo_lancamento = 2 THEN
                                             99::integer
                                         ELSE
                                             balancete_extmmaa.sub_tipo_lancamento::integer
                                    END
                                ELSE
                                    NULL
                           END)::varchar, 1, 2) AS tipo_retencao ";
    }
    $stSql .= "
                  , conta_despesa.cod_estrutural
                  , ordem_pagamento.cod_ordem
                  , TCMGO.numero_nota_liquidacao('".$this->getDado('exercicio')."',empenho.cod_entidade,nota_liquidacao.cod_nota,nota_liquidacao.exercicio_empenho,empenho.cod_empenho) AS numeroliquidacao
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(programa.num_programa::varchar, 4, '0')
                                   ELSE
                                       LPAD(programa.num_programa::varchar , 4, '0')
                              END)
                         ELSE
                             '0000'
                    END AS CodPrograma
                  , LPAD(SUBSTR(acao.num_acao::varchar,1,1), 6, '0') AS NaturezaAcao
                  , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3 ) AS NroProjAtiv
                  , despesa.num_orgao AS CodOrgao
                  , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       LPAD(despesa.cod_funcao::varchar, 2, '0')
                                   ELSE
                                       LPAD(despesa.cod_funcao::varchar   , 02, '0')
                              END)
                         ELSE
                             '00'
                    END AS CodFuncao
                  , CASE WHEN nota_liquidacao.exercicio_empenho > '2001' THEN
                             (CASE WHEN NOT pre_empenho.implantado THEN
                                       despesa.cod_subfuncao::varchar
                                   ELSE
                                       LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                              END)
                         ELSE '000'
                    END AS CodSubFuncao
                  , SUBSTR(REPLACE(conta_despesa.cod_estrutural::varchar,'.',''),1,6) AS elementodespesa
                  , CASE WHEN (elemento_de_para.estrutural IS NOT NULL) THEN
                             SUBSTR(REPLACE(elemento_de_para.estrutural::varchar,'.',''),7,2)
                         ELSE
                             '00'
                    END AS subelemento
                  , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado THEN
                             LPAD(despesa.num_unidade::varchar  , 04, '0')
                          || LPAD(despesa.cod_funcao::varchar   , 02, '0')
                          || LPAD(despesa.cod_programa::varchar , 02, '0')
                          || LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                          || LPAD(despesa.num_pao::varchar      , 06, '0')
                          || SUBSTR(REPLACE( conta_despesa.cod_estrutural::varchar, '.', ''), 1, 6)
                         ELSE
                             LPAD('', 21, '0')
                    END AS DotOrigp2001
                  , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroEmpenho
                  , ordem_pagamento.cod_ordem AS nroOP ";
                  if ($this->getDado('exercicio') > '2012') {
                     $stSql .= "
                                , LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0') AS Banco
                                , LPAD(BTRIM(REPLACE(COALESCE(banco.num_agencia, '0'),'-','')), 6, '0') AS Agencia
                                , LPAD(BTRIM(REPLACE(COALESCE(banco.conta_corrente,'0'), '-', '')), 12, '0') AS ContaCorrente ";
                  } else {
                    $stSql .= "
                    , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                              THEN '999'
                              ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                      END AS Banco
                    , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                              THEN '999999'
                              ELSE LPAD(BTRIM(REPLACE(COALESCE(banco.num_agencia, '0'),'-','')), 6, '0')
                      END AS Agencia
                    , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,9) = '1.1.1.1.1')
                              THEN '999999999999'
                              ELSE LPAD(BTRIM(REPLACE(COALESCE(banco.conta_corrente,'0'), '-', '')), 12, '0')
                      END AS ContaCorrente ";
                  }
                  $stSql .= "
                  , SUM( ordem_pagamento_retencao.vl_retencao) AS VlRetencao
                  , TO_CHAR(nota_liquidacao_paga.timestamp,'ddmmyyyy') AS DtEmissao
                  , ''::varchar AS Brancos
                  , 0::integer AS numero_sequencial
                  , 0 as nrextraorcamentaria
                  , CASE WHEN de_para_tipo_retencao.cod_tipo IS NOT NULL THEN
                            CASE WHEN de_para_tipo_retencao.cod_tipo = 99 THEN
                                'OUTROS'
                            ELSE ''
                            END
                    ELSE
                            CASE WHEN balancete_extmmaa.sub_tipo_lancamento NOT IN (1,2,3,4) THEN
                                'OUTROS'
                            ELSE ''
                            END
                    END AS nom_conta
               FROM empenho.nota_liquidacao_paga
         INNER JOIN ( SELECT exercicio
                           , cod_nota
                           , cod_entidade
                           , MAX(timestamp) as timestamp
                        FROM empenho.nota_liquidacao_paga
                    GROUP BY exercicio
                           , cod_nota
                           , cod_entidade
                  ) AS max_paga
                 ON max_paga.exercicio    = nota_liquidacao_paga.exercicio
                AND max_paga.cod_nota     = nota_liquidacao_paga.cod_nota
                AND max_paga.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND max_paga.timestamp    = nota_liquidacao_paga.timestamp

          LEFT JOIN ( SELECT nota_liquidacao_paga_anulada.*
                        FROM empenho.nota_liquidacao_paga_anulada
                  INNER JOIN ( SELECT exercicio
                                    , cod_nota
                                    , cod_entidade
                                    , MAX(timestamp_anulada) AS timestamp_anulada
                                 FROM empenho.nota_liquidacao_paga_anulada
                             GROUP BY exercicio
                                    , cod_nota
                                    , cod_entidade
                           ) AS max_anulada
                          ON max_anulada.exercicio    = nota_liquidacao_paga_anulada.exercicio
                         AND max_anulada.cod_nota     = nota_liquidacao_paga_anulada.cod_nota
                         AND max_anulada.cod_entidade = nota_liquidacao_paga_anulada.cod_entidade

                  ) AS nota_liquidacao_paga_anulada
                 ON nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao_paga.exercicio
                AND nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao_paga.cod_nota
                AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao_paga_anulada.timestamp    = nota_liquidacao_paga.timestamp
                AND nota_liquidacao_paga_anulada.vl_anulado   IS NULL

         INNER JOIN empenho.nota_liquidacao
                 ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota

          LEFT JOIN ( SELECT nota_liquidacao_conta_pagadora.exercicio_liquidacao
                           , nota_liquidacao_conta_pagadora.cod_entidade
                           , nota_liquidacao_conta_pagadora.cod_nota
                           , nota_liquidacao_conta_pagadora.timestamp
                           , banco.num_banco
                           , agencia.num_agencia
                           , plano_banco.conta_corrente
                           , nota_liquidacao_conta_pagadora.cod_plano
                           , nota_liquidacao_conta_pagadora.exercicio
                        FROM empenho.nota_liquidacao_conta_pagadora
                           , contabilidade.plano_analitica
                           , contabilidade.plano_banco
                           , monetario.agencia
                           , monetario.banco
                       WHERE nota_liquidacao_conta_pagadora.exercicio = plano_analitica.exercicio
                         AND nota_liquidacao_conta_pagadora.cod_plano = plano_analitica.cod_plano
                         AND plano_analitica.exercicio                = plano_banco.exercicio
                         AND plano_analitica.cod_plano                = plano_banco.cod_plano
                         AND plano_banco.cod_banco                    = agencia.cod_banco
                         AND plano_banco.cod_agencia                  = agencia.cod_agencia
                         AND monetario.agencia.cod_banco              = banco.cod_banco
                  ) AS banco
                 ON nota_liquidacao_paga.exercicio    = banco.exercicio_liquidacao
                AND nota_liquidacao_paga.cod_entidade = banco.cod_entidade
                AND nota_liquidacao_paga.cod_nota     = banco.cod_nota
                AND nota_liquidacao_paga.timestamp    = banco.timestamp

         INNER JOIN empenho.empenho
                 ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                AND empenho.cod_empenho  = nota_liquidacao.cod_empenho

         INNER JOIN empenho.pre_empenho
                 ON pre_empenho.exercicio       = empenho.exercicio
                AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho

         INNER JOIN empenho.pre_empenho_despesa
                 ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho

         INNER JOIN orcamento.despesa
                 ON despesa.exercicio   = pre_empenho_despesa.exercicio
                AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
 
          LEFT JOIN orcamento.despesa_acao
                 ON despesa_acao.exercicio_despesa  = despesa.exercicio
                AND despesa_acao.cod_despesa        = despesa.cod_despesa

          LEFT JOIN ppa.acao
                 ON acao.cod_acao = despesa_acao.cod_acao

          LEFT JOIN ppa.programa
                 ON programa.cod_programa = acao.cod_programa
     
         INNER JOIN orcamento.conta_despesa
                 ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta

          LEFT JOIN tcmgo.elemento_de_para
                 ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                AND elemento_de_para.exercicio = conta_despesa.exercicio

         INNER JOIN empenho.pagamento_liquidacao
                 ON nota_liquidacao.exercicio    = pagamento_liquidacao.exercicio_liquidacao
                AND nota_liquidacao.cod_entidade = pagamento_liquidacao.cod_entidade
                AND nota_liquidacao.cod_nota     = pagamento_liquidacao.cod_nota

         INNER JOIN empenho.ordem_pagamento
                 ON pagamento_liquidacao.exercicio    = ordem_pagamento.exercicio
                AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                AND pagamento_liquidacao.cod_ordem    = ordem_pagamento.cod_ordem

         INNER JOIN empenho.ordem_pagamento_retencao
                 ON ordem_pagamento_retencao.exercicio    = ordem_pagamento.exercicio
                AND ordem_pagamento_retencao.cod_entidade = ordem_pagamento.cod_entidade
                AND ordem_pagamento_retencao.cod_ordem    = ordem_pagamento.cod_ordem

         INNER JOIN contabilidade.plano_analitica
                 ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio

          LEFT JOIN tcmgo.arquivo_ext
                ON arquivo_ext.cod_plano = plano_analitica.cod_plano
                AND arquivo_ext.exercicio = plano_analitica.exercicio
                AND arquivo_ext.mes = ".$this->getDado('mes')."

          LEFT JOIN tcmgo.de_para_tipo_retencao
                 ON de_para_tipo_retencao.exercicio = plano_analitica.exercicio
                AND de_para_tipo_retencao.cod_plano = plano_analitica.cod_plano

          LEFT JOIN tcmgo.tipo_retencao
                 ON tipo_retencao.exercicio = de_para_tipo_retencao.exercicio_tipo
                AND tipo_retencao.cod_tipo  = de_para_tipo_retencao.cod_tipo

          LEFT JOIN tcmgo.balancete_extmmaa
                 ON balancete_extmmaa.exercicio = plano_analitica.exercicio
                AND balancete_extmmaa.cod_plano = plano_analitica.cod_plano

         INNER JOIN contabilidade.plano_conta
                 ON plano_conta.cod_conta = plano_analitica.cod_conta
                AND plano_conta.exercicio = plano_analitica.exercicio

              WHERE TO_DATE(nota_liquidacao_paga.timestamp::varchar, 'YYYY-MM-DD') BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                                              AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                AND ordem_pagamento_retencao.exercicio = '".$this->getDado('exercicio')."'
                AND SUBSTR(plano_conta.cod_estrutural::varchar, 1, 1) <> '4'
           GROUP BY nota_liquidacao.exercicio_empenho
                  , pre_empenho.implantado
                  , despesa.cod_programa
                  , despesa.cod_programa
                  , despesa.num_orgao
                  , despesa.num_unidade
                  , despesa.cod_funcao
                  , despesa.cod_subfuncao
                  , despesa.num_pao
                  , empenho.cod_empenho
                  , conta_despesa.cod_estrutural
                  , conta_despesa.descricao
                  , ordem_pagamento.cod_ordem
                  , nota_liquidacao.cod_nota
                  , empenho.cod_entidade
                  , banco.num_banco
                  , banco.num_agencia
                  , banco.conta_corrente
                  , nota_liquidacao_paga.timestamp
                  , plano_conta.cod_estrutural
                  , plano_conta.nom_conta
                  , de_para_tipo_retencao.cod_tipo
                  , balancete_extmmaa.sub_tipo_lancamento
                  , tipo_retencao.descricao
                  , elemento_de_para.estrutural
                  , plano_conta.cod_estrutural
                  , balancete_extmmaa.cod_plano
                  , arquivo_ext.sequencial
                  , programa.num_programa
                  , acao.num_acao
         ) as tbl
  GROUP BY tiporegistro
         , cod_estrutural
         , tipo_retencao
         , cod_ordem
         , numeroliquidacao
         , codprograma
         , naturezaacao
         , nroprojativ
         , codorgao
         , codunidade
         , codfuncao
         , codsubfuncao
         , elementodespesa
         , subelemento
         , dotorigp2001
         , nroempenho
         , nroop
         , banco
         , agencia
         , contacorrente
         , dtemissao
         , brancos
         , numero_sequencial
         , nom_conta
         , nrextraorcamentaria ";

  return $stSql;
}

function recuperaOrdemPagamentoRetencao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera('montaRecuperaOrdemPagamentoRetencao',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}
    
    public function recuperaOrdemPagamentoFonteRecursos2016(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera('montaRecuperaOrdemPagamentoFonteRecursos2016',$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaOrdemPagamentoFonteRecursos2016()
    {
        $stSql = "
          SELECT *
            FROM (
                  SELECT '13' AS TipoRegistro
                       , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                              THEN (
                                    CASE WHEN NOT pre_empenho.implantado
                                         THEN LPAD(programa.num_programa::varchar, 4, '0')
                                         ELSE LPAD(programa.num_programa::varchar, 4, '0')
                                     END)
                              ELSE '0000'
                          END AS CodPrograma
                       , LPAD(despesa.num_orgao::varchar, 2, '0') AS CodOrgao
                       , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
                       , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                              THEN (
                                    CASE WHEN NOT pre_empenho.implantado
                                         THEN LPAD(despesa.cod_funcao::varchar, 2, '0')
                                         ELSE LPAD(despesa.cod_funcao::varchar, 2, '0')
                                     END)
                              ELSE '00'
                          END AS CodFuncao
                       , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                              THEN (
                                    CASE WHEN NOT pre_empenho.implantado
                                         THEN despesa.cod_subfuncao::varchar
                                         ELSE LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                                     END)
                              ELSE '000'
                          END AS CodSubFuncao
                       , LPAD(SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 1, 1), 6, '0') AS NaturezaAcao
                       , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS NroProjAtiv
                       , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6) AS elementodespesa
                       , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                              THEN SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)
                              ELSE '00'
                          END AS subelemento
                       , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado
                              THEN LPAD(despesa.num_unidade::varchar  , 4, '0')
                                || LPAD(despesa.cod_funcao::varchar   , 2, '0')
                                || LPAD(despesa.cod_programa::varchar , 2, '0')
                                || LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                || LPAD(despesa.num_pao::varchar      , 6, '0')
                                || SUBSTR(REPLACE( conta_despesa.cod_estrutural, '.', ''),1, 6)
                              ELSE LPAD('', 21, '0')
                          END AS DotOrigp2001
                       , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroEmpenho
                       , ordem_pagamento.cod_ordem AS nroop 
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999'
                              ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                          END AS Banco
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999999'
                              ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                          END AS Agencia
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999999999999'
                              ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', 1), '-', '')), 12, '0')
                          END AS ContaCorrente
                       , LTRIM(split_part(conta_corrente.num_conta_corrente,'-',2),'0') AS contaCorrenteDigVerif
                       , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                              THEN '03'
                              WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                              THEN '02'
                              ELSE '01'
                          END as tipo_conta 
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999999999999999'
                              ELSE nota_liquidacao_paga.nrdocumento
                          END AS nrdocumento
                       , recurso.cod_fonte AS codFonteRecurso
                       , pagamento_liquidacao.vl_pagamento
                       , '' AS Brancos
                       , 0 AS numero_sequencial
                       , (sum(pagamento_liquidacao.vl_pagamento) -  coalesce(sum(vl_retencao),0.00)) as vl_retencao
                       , FALSE AS tipo_retencao
                    FROM ( SELECT nlp.exercicio
                                , nlp.cod_nota
                                , nlp.cod_entidade
                                , vl_pago as valor
                                , nlcp.exercicio AS exercicio_plano
                                , nlcp.cod_plano
                                , tipo_documento.cod_tipo as tipo_doc
                                , tipo_documento.descricao as descricao_doc
                                , pagamento_tipo_documento.num_documento as nrDocumento
                                , nlp.timestamp
                             FROM empenho.nota_liquidacao_paga as nlp
                        LEFT JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                               ON nlp.exercicio    = nlpa.exercicio
                              AND nlp.cod_nota     = nlpa.cod_nota
                              AND nlp.cod_entidade = nlpa.cod_entidade
                              AND nlp.timestamp    = nlpa.timestamp
                       INNER JOIN empenho.nota_liquidacao_conta_pagadora as nlcp
                               ON nlp.exercicio    = nlcp.exercicio_liquidacao
                              AND nlp.cod_nota     = nlcp.cod_nota
                              AND nlp.cod_entidade = nlcp.cod_entidade
                              AND nlp.timestamp    = nlcp.timestamp
                        LEFT JOIN contabilidade.pagamento
                               ON pagamento.exercicio_liquidacao = nlp.exercicio
                              AND pagamento.cod_entidade         = nlp.cod_entidade
                              AND pagamento.cod_nota             = nlp.cod_nota
                              AND pagamento.timestamp            = nlp.timestamp
                        LEFT JOIN contabilidade.lancamento
                               ON lancamento.exercicio    = pagamento.exercicio
                              AND lancamento.cod_entidade = pagamento.cod_entidade
                              AND lancamento.cod_lote     = pagamento.cod_lote
                              AND lancamento.sequencia    = pagamento.sequencia
                              AND lancamento.tipo         = pagamento.tipo
                        INNER JOIN tesouraria.pagamento_tipo_documento
                               ON pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                              AND pagamento_tipo_documento.exercicio    = nlp.exercicio
                              AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade
                              AND pagamento_tipo_documento.timestamp    = nlp.timestamp
                        LEFT JOIN tcmgo.tipo_documento
                               ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento
                            WHERE nlp.timestamp BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                    AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                         ORDER BY cod_nota
                         ) as nota_liquidacao_paga
              INNER JOIN empenho.nota_liquidacao
                      ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                     AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                     AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
              INNER JOIN empenho.empenho
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              INNER JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                     AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
              INNER JOIN orcamento.despesa
                      ON despesa.exercicio   = pre_empenho_despesa.exercicio
                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
              INNER JOIN orcamento.pao
                      ON pao.num_pao   = despesa.num_pao
                     AND pao.exercicio = despesa.exercicio
              INNER JOIN orcamento.pao_ppa_acao
                      ON pao_ppa_acao.exercicio = pao.exercicio
                     AND pao_ppa_acao.num_pao   = pao.num_pao
              INNER JOIN ppa.acao
                      ON acao.cod_acao = pao_ppa_acao.cod_acao
              INNER JOIN orcamento.programa AS o_programa
                      ON o_programa.cod_programa = despesa.cod_programa
                     AND o_programa.exercicio    = despesa.exercicio
              INNER JOIN orcamento.programa_ppa_programa
                      ON programa_ppa_programa.cod_programa = o_programa.cod_programa
                     AND programa_ppa_programa.exercicio    = o_programa.exercicio
              INNER JOIN ppa.programa
                      ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa
              INNER JOIN orcamento.conta_despesa
                      ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                     AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
              INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                      ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                     AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                     AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                     AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
              INNER JOIN empenho.pagamento_liquidacao
                      ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota
                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                     AND pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio
              INNER JOIN empenho.ordem_pagamento
                      ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                     AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                     AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
               LEFT JOIN (
                          SELECT ordem_pagamento_retencao.cod_ordem
                               , ordem_pagamento_retencao.cod_entidade
                               , ordem_pagamento_retencao.exercicio
                               , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                            FROM empenho.ordem_pagamento_retencao
                      INNER JOIN contabilidade.plano_analitica
                              ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                             AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio
                        GROUP BY ordem_pagamento_retencao.cod_ordem
                               , ordem_pagamento_retencao.cod_entidade
                               , ordem_pagamento_retencao.exercicio
                         ) as retencao
                      ON ordem_pagamento.cod_ordem    = retencao.cod_ordem
                     AND ordem_pagamento.cod_entidade = retencao.cod_entidade
                     AND ordem_pagamento.exercicio    = retencao.exercicio
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = nota_liquidacao_paga.cod_plano
                     AND plano_analitica.exercicio = nota_liquidacao_paga.exercicio_plano
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
              INNER JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = plano_analitica.exercicio
                     AND plano_recurso.cod_plano = plano_analitica.cod_plano
              INNER JOIN orcamento.recurso
                      ON recurso.exercicio   = plano_recurso.exercicio
                     AND recurso.cod_recurso = plano_recurso.cod_recurso
                   WHERE ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
                GROUP BY TipoRegistro
                       , CodPrograma
                       , CodOrgao
                       , CodUnidade
                       , CodFuncao
                       , CodSubFuncao
                       , NaturezaAcao
                       , NroProjAtiv
                       , elementodespesa
                       , subelemento
                       , DotOrigp2001
                       , nroEmpenho
                       , nroop 
                       , Banco
                       , Agencia
                       , ContaCorrente
                       , contaCorrenteDigVerif
                       , nota_liquidacao_paga.nrdocumento
                       , codFonteRecurso
                       , plano_conta.cod_estrutural
                       , pagamento_liquidacao.vl_pagamento
                   UNION 
                  SELECT '13' AS TipoRegistro
                       , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                              THEN (
                                    CASE WHEN NOT pre_empenho.implantado
                                         THEN LPAD(programa.num_programa::varchar, 4, '0')
                                         ELSE LPAD(programa.num_programa::varchar, 4, '0')
                                     END)
                              ELSE '0000'
                          END AS CodPrograma
                       , LPAD(despesa.num_orgao::varchar, 2, '0') AS CodOrgao
                       , LPAD(despesa.num_unidade::varchar, 2, '0') AS CodUnidade
                       , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                              THEN (
                                    CASE WHEN NOT pre_empenho.implantado
                                         THEN LPAD(despesa.cod_funcao::varchar, 2, '0')
                                         ELSE LPAD(despesa.cod_funcao::varchar, 2, '0')
                                     END)
                              ELSE '00'
                          END AS CodFuncao
                       , CASE WHEN nota_liquidacao.exercicio_empenho > '2001'
                              THEN (
                                    CASE WHEN NOT pre_empenho.implantado
                                         THEN despesa.cod_subfuncao::varchar
                                         ELSE LPAD(despesa.cod_subfuncao::varchar, 03, '0')
                                     END)
                              ELSE '000'
                          END AS CodSubFuncao
                       , LPAD(SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 1, 1), 6, '0') AS NaturezaAcao
                       , SUBSTR(LPAD(acao.num_acao::varchar, 4, '0'), 2, 3) AS NroProjAtiv
                       , SUBSTR(REPLACE(conta_despesa.cod_estrutural,'.',''),1,6) AS elementodespesa
                       , CASE WHEN( elemento_de_para.estrutural IS NOT NULL )
                              THEN SUBSTR(REPLACE(elemento_de_para.estrutural,'.',''),7,2)
                              ELSE '00'
                          END AS subelemento
                       , CASE WHEN nota_liquidacao.exercicio_empenho <= '2001' AND pre_empenho.implantado
                              THEN LPAD(despesa.num_unidade::varchar  , 4, '0')
                                || LPAD(despesa.cod_funcao::varchar   , 2, '0')
                                || LPAD(despesa.cod_programa::varchar , 2, '0')
                                || LPAD(despesa.cod_subfuncao::varchar, 3, '0')
                                || LPAD(despesa.num_pao::varchar      , 6, '0')
                                || SUBSTR(REPLACE( conta_despesa.cod_estrutural, '.', ''),1, 6)
                              ELSE LPAD('', 21, '0')
                          END AS DotOrigp2001
                       , LPAD(empenho.cod_empenho::varchar, 6, '0') AS nroEmpenho
                       , ordem_pagamento.cod_ordem AS nroop 
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999'
                              ELSE LPAD(BTRIM(COALESCE(banco.num_banco, '0')), 3, '0')
                          END AS Banco
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999999'
                              ELSE LPAD(BTRIM(REPLACE(COALESCE(agencia.num_agencia, '0'),'-','')), 6, '0')
                          END AS Agencia
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999999999999'
                              ELSE LPAD(BTRIM(REPLACE(SPLIT_PART(COALESCE(conta_corrente.num_conta_corrente,'0'), '-', 1), '-', '')), 12, '0')
                          END AS ContaCorrente
                       , LTRIM(split_part(conta_corrente.num_conta_corrente,'-',2),'0') AS contaCorrenteDigVerif
                       , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                              THEN '03'
                              WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                              THEN '02'
                              ELSE '01'
                          END as tipo_conta 
                       , CASE WHEN (SUBSTR(plano_conta.cod_estrutural,1,12) = '1.1.1.1.1.01')
                              THEN '999999999999999'
                              ELSE nota_liquidacao_paga.nrdocumento
                          END AS nrdocumento
                       , recurso.cod_fonte AS codFonteRecurso
                       , pagamento_liquidacao.vl_pagamento
                       , '' AS Brancos
                       , 0 AS numero_sequencial
                       , coalesce(vl_retencao,0.00) as vl_retencao
                       , TRUE AS tipo_retencao
                    FROM ( SELECT nlp.exercicio
                                , nlp.cod_nota
                                , nlp.cod_entidade
                                , vl_pago as valor
                                , nlcp.exercicio AS exercicio_plano
                                , nlcp.cod_plano
                                , tipo_documento.cod_tipo as tipo_doc
                                , tipo_documento.descricao as descricao_doc
                                , pagamento_tipo_documento.num_documento as nrDocumento
                                , nlp.timestamp
                             FROM empenho.nota_liquidacao_paga as nlp
                        LEFT JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                               ON nlp.exercicio    = nlpa.exercicio
                              AND nlp.cod_nota     = nlpa.cod_nota
                              AND nlp.cod_entidade = nlpa.cod_entidade
                              AND nlp.timestamp    = nlpa.timestamp
                       INNER JOIN empenho.nota_liquidacao_conta_pagadora as nlcp
                               ON nlp.exercicio    = nlcp.exercicio_liquidacao
                              AND nlp.cod_nota     = nlcp.cod_nota
                              AND nlp.cod_entidade = nlcp.cod_entidade
                              AND nlp.timestamp    = nlcp.timestamp
                        LEFT JOIN contabilidade.pagamento
                               ON pagamento.exercicio_liquidacao = nlp.exercicio
                              AND pagamento.cod_entidade         = nlp.cod_entidade
                              AND pagamento.cod_nota             = nlp.cod_nota
                              AND pagamento.timestamp            = nlp.timestamp
                        LEFT JOIN contabilidade.lancamento
                               ON lancamento.exercicio    = pagamento.exercicio
                              AND lancamento.cod_entidade = pagamento.cod_entidade
                              AND lancamento.cod_lote     = pagamento.cod_lote
                              AND lancamento.sequencia    = pagamento.sequencia
                              AND lancamento.tipo         = pagamento.tipo
                       INNER JOIN contabilidade.lancamento_retencao
                               ON lancamento_retencao.cod_lote     = lancamento.cod_lote
                              AND lancamento_retencao.tipo         = lancamento.tipo
                              AND lancamento_retencao.sequencia    = lancamento.sequencia
                              AND lancamento_retencao.exercicio    = lancamento.exercicio
                              AND lancamento_retencao.cod_entidade = lancamento.cod_entidade
                       INNER JOIN tesouraria.pagamento_tipo_documento  
                               ON pagamento_tipo_documento.cod_nota     = nlp.cod_nota
                              AND pagamento_tipo_documento.exercicio    = nlp.exercicio
                              AND pagamento_tipo_documento.cod_entidade = nlp.cod_entidade
                        LEFT JOIN tcmgo.tipo_documento
                               ON tcmgo.tipo_documento.cod_tipo  =  pagamento_tipo_documento.cod_tipo_documento
                            WHERE nlp.timestamp BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                    AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                         ORDER BY cod_nota
                         ) as nota_liquidacao_paga
              INNER JOIN empenho.nota_liquidacao
                      ON nota_liquidacao.exercicio    = nota_liquidacao_paga.exercicio
                     AND nota_liquidacao.cod_entidade = nota_liquidacao_paga.cod_entidade
                     AND nota_liquidacao.cod_nota     = nota_liquidacao_paga.cod_nota
              INNER JOIN empenho.empenho
                      ON empenho.exercicio    = nota_liquidacao.exercicio_empenho
                     AND empenho.cod_entidade = nota_liquidacao.cod_entidade
                     AND empenho.cod_empenho  = nota_liquidacao.cod_empenho
              INNER JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
              INNER JOIN empenho.pre_empenho_despesa
                      ON pre_empenho.exercicio       = pre_empenho_despesa.exercicio
                     AND pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
              INNER JOIN orcamento.despesa
                      ON despesa.exercicio   = pre_empenho_despesa.exercicio
                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
              INNER JOIN orcamento.pao
                      ON pao.num_pao   = despesa.num_pao
                     AND pao.exercicio = despesa.exercicio
              INNER JOIN orcamento.pao_ppa_acao
                      ON pao_ppa_acao.exercicio = pao.exercicio
                     AND pao_ppa_acao.num_pao   = pao.num_pao
              INNER JOIN ppa.acao
                      ON acao.cod_acao = pao_ppa_acao.cod_acao
              INNER JOIN orcamento.programa AS o_programa
                      ON o_programa.cod_programa = despesa.cod_programa
                     AND o_programa.exercicio    = despesa.exercicio
              INNER JOIN orcamento.programa_ppa_programa
                      ON programa_ppa_programa.cod_programa = o_programa.cod_programa
                     AND programa_ppa_programa.exercicio    = o_programa.exercicio
              INNER JOIN ppa.programa
                      ON programa.cod_programa = programa_ppa_programa.cod_programa_ppa
              INNER JOIN orcamento.conta_despesa
                      ON conta_despesa.exercicio = pre_empenho_despesa.exercicio
                     AND conta_despesa.cod_conta = pre_empenho_despesa.cod_conta
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
              INNER JOIN empenho.pagamento_liquidacao_nota_liquidacao_paga
                      ON nota_liquidacao_paga.cod_entidade = pagamento_liquidacao_nota_liquidacao_paga.cod_entidade
                     AND nota_liquidacao_paga.cod_nota     = pagamento_liquidacao_nota_liquidacao_paga.cod_nota
                     AND nota_liquidacao_paga.exercicio    = pagamento_liquidacao_nota_liquidacao_paga.exercicio
                     AND nota_liquidacao_paga.timestamp    = pagamento_liquidacao_nota_liquidacao_paga.timestamp
              INNER JOIN empenho.pagamento_liquidacao
                      ON pagamento_liquidacao_nota_liquidacao_paga.exercicio_liquidacao = pagamento_liquidacao.exercicio_liquidacao
                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_entidade         = pagamento_liquidacao.cod_entidade
                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_nota             = pagamento_liquidacao.cod_nota
                     AND pagamento_liquidacao_nota_liquidacao_paga.cod_ordem            = pagamento_liquidacao.cod_ordem
                     AND pagamento_liquidacao_nota_liquidacao_paga.exercicio            = pagamento_liquidacao.exercicio
              INNER JOIN empenho.ordem_pagamento
                      ON pagamento_liquidacao.exercicio = ordem_pagamento.exercicio
                     AND pagamento_liquidacao.cod_entidade = ordem_pagamento.cod_entidade
                     AND pagamento_liquidacao.cod_ordem = ordem_pagamento.cod_ordem
              INNER JOIN (
                          SELECT ordem_pagamento_retencao.cod_ordem
                               , ordem_pagamento_retencao.cod_entidade
                               , ordem_pagamento_retencao.exercicio
                               , SUM(ordem_pagamento_retencao.vl_retencao) AS vl_retencao
                            FROM empenho.ordem_pagamento_retencao
                      INNER JOIN contabilidade.plano_analitica
                              ON ordem_pagamento_retencao.cod_plano = plano_analitica.cod_plano
                             AND ordem_pagamento_retencao.exercicio = plano_analitica.exercicio
                      INNER JOIN contabilidade.plano_conta
                              ON plano_conta.cod_conta = plano_analitica.cod_conta
                             AND plano_conta.exercicio = plano_analitica.exercicio
                        GROUP BY ordem_pagamento_retencao.cod_ordem
                               , ordem_pagamento_retencao.cod_entidade
                               , ordem_pagamento_retencao.exercicio
                         ) as retencao
                      ON ordem_pagamento.cod_ordem    = retencao.cod_ordem
                     AND ordem_pagamento.cod_entidade = retencao.cod_entidade
                     AND ordem_pagamento.exercicio    = retencao.exercicio
              INNER JOIN contabilidade.plano_analitica
                      ON plano_analitica.cod_plano = nota_liquidacao_paga.cod_plano
                     AND plano_analitica.exercicio = nota_liquidacao_paga.exercicio_plano
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
              INNER JOIN contabilidade.plano_recurso
                      ON plano_recurso.exercicio = plano_analitica.exercicio
                     AND plano_recurso.cod_plano = plano_analitica.cod_plano
              INNER JOIN orcamento.recurso
                      ON recurso.exercicio   = plano_recurso.exercicio
                     AND recurso.cod_recurso = plano_recurso.cod_recurso
                   WHERE ordem_pagamento.exercicio = '".$this->getDado('exercicio')."'
                GROUP BY TipoRegistro
                       , CodPrograma
                       , CodOrgao
                       , CodUnidade
                       , CodFuncao
                       , CodSubFuncao
                       , NaturezaAcao
                       , NroProjAtiv
                       , elementodespesa
                       , subelemento
                       , DotOrigp2001
                       , nroEmpenho
                       , nroop 
                       , Banco
                       , Agencia
                       , ContaCorrente
                       , contaCorrenteDigVerif
                       , nota_liquidacao_paga.nrdocumento
                       , codFonteRecurso
                       , pagamento_liquidacao.vl_pagamento
                       , vl_retencao
                       , plano_conta.cod_estrutural
                 ) as tbl
        ORDER BY codprograma
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
               , nrdocumento
               , banco
               , agencia
               , contacorrente
               , tipo_retencao
        ";
        return $stSql;
    }
}