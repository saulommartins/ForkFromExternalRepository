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
    * 
    * Data de Criação   : 07/10/2014

    * @author Analista:
    * @author Desenvolvedor:  Evandro Melos
    $Id: TTCEPERestosInscritos.class.php 60490 2014-10-23 19:33:53Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEPERestosInscritos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    function TTCEPERestosInscritos()
    {
        parent::Persistente();
    }

    function montaRecuperaTodos()
    {
        //CONSULTA SOMA TODOS OS RESTOS QUE FORAM IMPLANTADOS JUNTO COM OS RESTOS NORMAIS
        $stSql="SELECT *
                FROM(
                    --QUANDO NAO FOR IMPLANTADO
                    SELECT DISTINCT
                              empenho.exercicio as exercicio_empenho
                            , LPAD(despesa.num_orgao::VARCHAR,2,'0') || LPAD(despesa.num_unidade::VARCHAR,2,'0') AS unidade_orcamentaria 
                            , despesa.cod_funcao
                            , despesa.cod_subfuncao
                            , despesa.cod_programa
                            , acao.num_acao as cod_acao
                            , CASE 
                                            -- PROJETOS
                                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 1 )               
                                                THEN 1                                                                                                               
                                            --ATIVIDADE
                                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 2 )                            
                                                THEN 2
                                            --OPERACOES ESPECIAIS
                                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(despesa.exercicio,acao.num_acao)) = 3 )
                                                THEN 9                           
                             END AS tipo_acao
                            , substr(despesa.cod_estrutural, 1, 1) as categoria_economica
                            , substr(despesa.cod_estrutural, 3, 1) as grupo_natureza_despesa
                            , orcamento_modalidade_despesa.cod_modalidade
                            , LPAD(substr(despesa.cod_estrutural, 9, 2),3,'0') as elemento
                            --Usar 999 quando não possuir subelenmento
                            , CASE WHEN substr(despesa.cod_estrutural, 12, 2) = '00' THEN
                                    '999'
                                ELSE
                                    LPAD(substr(despesa.cod_estrutural, 12, 2),3,'0')
                            END as subelemento
                            , CASE TRIM(atributo_empenho_valor.valor)::INTEGER
                                WHEN 4  THEN 1
                                WHEN 3  THEN 2
                                WHEN 2  THEN 3
                                WHEN 1  THEN 4
                                WHEN 5  THEN 7  
                                WHEN 6  THEN 8
                                WHEN 7  THEN 9
                                WHEN 14 THEN 10
                                WHEN 10 THEN 0
                                WHEN 11 THEN 0
                                WHEN 12 THEN 0
                                WHEN 20 THEN 6
                            END AS modalidade_licitacao
                            , empenho.cod_empenho as num_empenho
                            , CASE pre_empenho.cod_tipo
                                WHEN 1 THEN 1
                                WHEN 2 THEN 3
                                WHEN 3 THEN 2
                            END AS tipo_empenho
                            , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy') AS data_empenho         
                            , (SELECT COALESCE(SUM(item_pre_empenho.vl_total),0.00) 
                                    FROM empenho.item_pre_empenho
                                    WHERE item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 
                                    AND item_pre_empenho.exercicio = pre_empenho.exercicio
                            ) AS vl_original
                            , remove_acentos(pre_empenho.descricao) AS historico_empenho
                            , CASE WHEN sw_cgm_documento.documento IS NOT NULL 
                                    THEN sw_cgm_documento.documento
                                ELSE entidade_documento.documento
                            END AS cpf_cnpj_credor          
                            , codigo_fonte_recurso.cod_fonte as cod_fonte_recurso
                            , ( COALESCE(restos_pagar.valor_processado_exercicio_anterior,0.00) + COALESCE(restos_pagar.valor_processado_exercicios_anteriores,0.00) ) AS vl_saldo_ant_proc
                            , ( COALESCE(restos_pagar.valor_nao_processado_exercicios_anteriores,0.00) + COALESCE(restos_pagar.valor_nao_processado_exercicio_anterior,0.00) ) AS vl_saldo_ant_nao
                            , configuracao_ordenador.cgm_ordenador as cpf_ordenador
                            , LPAD(interna.cod_licitacao::VARCHAR,4,'0')||'/'||interna.exercicio_licitacao AS numero_licitacao
                            , LPAD(substr(despesa.cod_estrutural, 9, 2),3,'0') as cod_elemento_despesa
                        
                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                         ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        AND pre_empenho.exercicio = empenho.exercicio
                        
                    JOIN empenho.nota_liquidacao
                         ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                        AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                        AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                    LEFT JOIN empenho.pagamento_liquidacao
                         ON pagamento_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio
                        AND pagamento_liquidacao.cod_entidade = nota_liquidacao.cod_entidade
                        AND pagamento_liquidacao.cod_nota = nota_liquidacao.cod_nota

                    LEFT JOIN empenho.nota_liquidacao_paga
                         ON nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                        AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                        AND nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota

                    JOIN (SELECT
                                    despesa.*
                                    , conta_despesa.cod_estrutural
                                    , pre_empenho_despesa.cod_pre_empenho
                            FROM empenho.pre_empenho_despesa
                            JOIN orcamento.despesa
                              ON despesa.exercicio = pre_empenho_despesa.exercicio
                             AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa              
                            JOIN orcamento.conta_despesa
                              ON conta_despesa.exercicio = despesa.exercicio
                             AND conta_despesa.cod_conta = despesa.cod_conta
                    ) AS despesa
                         ON despesa.exercicio = pre_empenho.exercicio
                        AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    JOIN orcamento.despesa_acao
                         ON despesa_acao.exercicio_despesa   = despesa.exercicio
                        AND despesa_acao.cod_despesa        = despesa.cod_despesa

                    JOIN ppa.acao
                        ON acao.cod_acao = despesa_acao.cod_acao

                    JOIN empenho.atributo_empenho_valor
                         ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                        AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND atributo_empenho_valor.cod_atributo = 101

                    JOIN empenho.tipo_empenho
                        ON tipo_empenho.cod_tipo =  pre_empenho.cod_tipo

                    JOIN (  SELECT   CASE 
                                        WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                            THEN sw_cgm_pessoa_fisica.cpf
                                        WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                            THEN sw_cgm_pessoa_juridica.cnpj
                                        ELSE NULL
                                    END AS documento
                                    , sw_cgm.numcgm
                            FROM sw_cgm
                            LEFT JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                    ) AS sw_cgm_documento
                        ON sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario

                    JOIN (  SELECT  entidade.exercicio
                                    , entidade.cod_entidade
                                    , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                            THEN sw_cgm_pessoa_fisica.cpf
                                        WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                            THEN sw_cgm_pessoa_juridica.cnpj
                                        ELSE NULL
                                    END AS documento
                            FROM orcamento.entidade
                            LEFT JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                    ) AS entidade_documento
                         ON entidade_documento.exercicio = empenho.exercicio
                        AND entidade_documento.cod_entidade = empenho.cod_entidade

                    JOIN (
                            SELECT  
                                    item_pre_empenho.cod_pre_empenho
                                    , item_pre_empenho.exercicio
                                    , licitacao.cod_licitacao
                                    , licitacao.exercicio AS exercicio_licitacao
                            FROM empenho.item_pre_empenho
                            LEFT JOIN empenho.item_pre_empenho_julgamento
                                 ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                                AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                                AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
                            LEFT JOIN compras.julgamento_item
                                 ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                                AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                                AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                                AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                                AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                            LEFT JOIN compras.julgamento
                                 ON julgamento.exercicio   = julgamento_item.exercicio
                                AND julgamento.cod_cotacao = julgamento_item.cod_cotacao
                            LEFT JOIN compras.cotacao
                                 ON cotacao.cod_cotacao = julgamento.cod_cotacao
                                AND cotacao.exercicio   = julgamento.exercicio
                            LEFT JOIN compras.mapa_cotacao
                                 ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                                AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                            LEFT JOIN compras.mapa
                                 ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                                AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                            LEFT JOIN licitacao.licitacao
                                 ON licitacao.exercicio_mapa = mapa.exercicio
                                AND licitacao.cod_mapa = mapa.cod_mapa
                            GROUP BY item_pre_empenho.cod_pre_empenho
                                    , item_pre_empenho.exercicio
                                    , licitacao.cod_licitacao
                                    , licitacao.exercicio
                    ) AS interna
                         ON interna.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND interna.exercicio       = pre_empenho.exercicio

                    JOIN orcamento.recurso
                         ON recurso.exercicio   = despesa.exercicio
                        AND recurso.cod_recurso = despesa.cod_recurso

                    LEFT JOIN tcepe.codigo_fonte_recurso
                         ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                        AND codigo_fonte_recurso.exercicio   = recurso.exercicio

                    LEFT JOIN tcepe.orcamento_modalidade_despesa
                         ON orcamento_modalidade_despesa.exercicio   = despesa.exercicio
                        AND orcamento_modalidade_despesa.cod_despesa = despesa.cod_despesa

                    LEFT JOIN tcepe.configuracao_ordenador
                         ON configuracao_ordenador.cgm_ordenador = sw_cgm_documento.numcgm

                    LEFT JOIN (SELECT * FROM tcepe.fn_restos_pagar(
                                                                '".$this->getDado('exercicio')."',
                                                                '".$this->getDado('cod_entidade')."',
                                                                '31/12/".$this->getDado('exercicio')."'
                                                            ) as rp (
                                                                cod_empenho INTEGER
                                                                , cod_entidade INTEGER
                                                                , exercicio CHARACTER(4)
                                                                , valor_processado_exercicios_anteriores NUMERIC
                                                                , valor_processado_exercicio_anterior NUMERIC
                                                                , valor_processado_cancelado NUMERIC
                                                                , valor_processado_pago NUMERIC
                                                                , valor_nao_processado_exercicios_anteriores NUMERIC
                                                                , valor_nao_processado_exercicio_anterior NUMERIC
                                                                , valor_nao_processado_cancelado NUMERIC
                                                                , valor_nao_processado_pago NUMERIC
                                                            )
                             ORDER BY exercicio, cod_empenho
                    ) AS restos_pagar
                         ON restos_pagar.cod_empenho = empenho.cod_empenho
                        AND restos_pagar.cod_entidade = empenho.cod_entidade
                        AND restos_pagar.exercicio = empenho.exercicio

                    WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                    AND empenho.exercicio < '".$this->getDado('exercicio')."'
                    AND ( nota_liquidacao.exercicio='".$this->getDado('exercicio')."'
                         OR
                          pagamento_liquidacao.exercicio='".$this->getDado('exercicio')."'
                         OR
                          to_char(nota_liquidacao_paga.timestamp, 'yyyy')='".$this->getDado('exercicio')."'
                        )
        
        UNION
        --QUANDO FOR IMPLANTADO

                    SELECT DISTINCT
                              empenho.exercicio as exercicio_empenho
                            ,LPAD(restos_pre_empenho.num_orgao::VARCHAR,2,'0') || LPAD(restos_pre_empenho.num_unidade::VARCHAR,2,'0') AS unidade_orcamentaria                                 
                            , restos_pre_empenho.cod_funcao as cod_funcao
                            , restos_pre_empenho.cod_subfuncao as cod_subfuncao
                            , restos_pre_empenho.cod_programa as cod_programa                            
                            , restos_pre_empenho.num_pao as cod_acao
                            , CASE 
                                            -- PROJETOS
                                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(restos_pre_empenho.exercicio,restos_pre_empenho.num_pao)) = 1 )               
                                                THEN 1                                                                                                               
                                            --ATIVIDADE
                                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(restos_pre_empenho.exercicio,restos_pre_empenho.num_pao)) = 2 )                            
                                                THEN 2
                                            --OPERACOES ESPECIAIS
                                            WHEN ( (SELECT orcamento.fn_consulta_tipo_pao(restos_pre_empenho.exercicio,restos_pre_empenho.num_pao)) = 3 )
                                                THEN 9                           
                             END AS tipo_acao
                            ,  substr(restos_pre_empenho.cod_estrutural, 1, 1) AS categoria_economica
                            , substr(restos_pre_empenho.cod_estrutural, 3, 1) AS grupo_natureza_despesa
                            , orcamento_modalidade_despesa.cod_modalidade
                            ,LPAD(substr(restos_pre_empenho.cod_estrutural, 5, 2),3,'0')  AS elemento
                            --Usar 999 quando não possuir subelenmento
                            , CASE 
                                WHEN substr(restos_pre_empenho.cod_estrutural, 7, 2) = '00' THEN
                                        '999'
                                ELSE
                                        LPAD(substr(restos_pre_empenho.cod_estrutural, 7, 2),3,'0')
                             END AS subelemento
                            , CASE TRIM(atributo_empenho_valor.valor)::INTEGER
                                WHEN 4  THEN 1
                                WHEN 3  THEN 2
                                WHEN 2  THEN 3
                                WHEN 1  THEN 4
                                WHEN 5  THEN 7  
                                WHEN 6  THEN 8
                                WHEN 7  THEN 9
                                WHEN 14 THEN 10
                                WHEN 10 THEN 0
                                WHEN 11 THEN 0
                                WHEN 12 THEN 0
                                WHEN 20 THEN 6
                             END AS modalidade_licitacao
                            , empenho.cod_empenho as num_empenho
                            , CASE pre_empenho.cod_tipo
                                WHEN 1 THEN 1
                                WHEN 2 THEN 3
                                WHEN 3 THEN 2
                             END AS tipo_empenho
                            , TO_CHAR(empenho.dt_empenho, 'dd/mm/yyyy') AS data_empenho         
                            , (SELECT COALESCE(SUM(item_pre_empenho.vl_total),0.00) 
                                    FROM empenho.item_pre_empenho
                                    WHERE item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho 
                                    AND item_pre_empenho.exercicio = pre_empenho.exercicio
                            ) AS vl_original
                            , remove_acentos(pre_empenho.descricao) AS historico_empenho
                            , CASE WHEN sw_cgm_documento.documento IS NOT NULL 
                                    THEN sw_cgm_documento.documento
                                ELSE entidade_documento.documento
                             END AS cpf_cnpj_credor          
                            , codigo_fonte_recurso.cod_fonte as cod_fonte_recurso
                            , ( COALESCE(restos_pagar.valor_processado_exercicio_anterior,0.00) + COALESCE(restos_pagar.valor_processado_exercicios_anteriores,0.00) ) AS vl_saldo_ant_proc
                            , ( COALESCE(restos_pagar.valor_nao_processado_exercicios_anteriores,0.00) + COALESCE(restos_pagar.valor_nao_processado_exercicio_anterior,0.00) ) AS vl_saldo_ant_nao
                            , configuracao_ordenador.cgm_ordenador as cpf_ordenador
                            , LPAD(interna.cod_licitacao::VARCHAR,4,'0')||'/'||interna.exercicio_licitacao AS numero_licitacao
                            , LPAD(substr(restos_pre_empenho.cod_estrutural, 5, 2),3,'0') as cod_elemento_despesa
 
                    FROM empenho.empenho

                    JOIN empenho.pre_empenho
                         ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                        AND pre_empenho.exercicio = empenho.exercicio

                    JOIN empenho.restos_pre_empenho
                         ON restos_pre_empenho.cod_pre_empenho   = pre_empenho.cod_pre_empenho
                        AND restos_pre_empenho.exercicio        = pre_empenho.exercicio
                        AND pre_empenho.implantado = 't'

                    LEFT JOIN empenho.pre_empenho_despesa
                         ON pre_empenho_despesa.exercicio        = pre_empenho.exercicio
                        AND pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                    
                    JOIN empenho.nota_liquidacao
                         ON nota_liquidacao.exercicio_empenho = empenho.exercicio
                        AND nota_liquidacao.cod_entidade = empenho.cod_entidade
                        AND nota_liquidacao.cod_empenho = empenho.cod_empenho

                    LEFT JOIN empenho.pagamento_liquidacao
                         ON pagamento_liquidacao.exercicio_liquidacao = nota_liquidacao.exercicio
                        AND pagamento_liquidacao.cod_entidade = nota_liquidacao.cod_entidade
                        AND pagamento_liquidacao.cod_nota = nota_liquidacao.cod_nota

                    LEFT JOIN empenho.nota_liquidacao_paga
                         ON nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio
                        AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade
                        AND nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota
  

                    JOIN empenho.atributo_empenho_valor
                         ON atributo_empenho_valor.exercicio = pre_empenho.exercicio
                        AND atributo_empenho_valor.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND atributo_empenho_valor.cod_atributo = 101

                    JOIN empenho.tipo_empenho
                        ON tipo_empenho.cod_tipo =  pre_empenho.cod_tipo

                    JOIN (  SELECT   CASE 
                                        WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                            THEN sw_cgm_pessoa_fisica.cpf
                                        WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                            THEN sw_cgm_pessoa_juridica.cnpj
                                        ELSE NULL
                                    END AS documento
                                    , sw_cgm.numcgm
                            FROM sw_cgm
                            LEFT JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                    ) AS sw_cgm_documento
                        ON sw_cgm_documento.numcgm = pre_empenho.cgm_beneficiario

                    JOIN (  SELECT  entidade.exercicio
                                    , entidade.cod_entidade
                                    , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL
                                            THEN sw_cgm_pessoa_fisica.cpf
                                        WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL
                                            THEN sw_cgm_pessoa_juridica.cnpj
                                        ELSE NULL
                                    END AS documento
                            FROM orcamento.entidade
                            LEFT JOIN sw_cgm_pessoa_fisica
                                ON sw_cgm_pessoa_fisica.numcgm = entidade.numcgm
                            LEFT JOIN sw_cgm_pessoa_juridica
                                ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm
                    ) AS entidade_documento
                         ON entidade_documento.exercicio = empenho.exercicio
                        AND entidade_documento.cod_entidade = empenho.cod_entidade

                    JOIN (
                            SELECT  
                                    item_pre_empenho.cod_pre_empenho
                                    , item_pre_empenho.exercicio
                                    , licitacao.cod_licitacao
                                    , licitacao.exercicio AS exercicio_licitacao
                            FROM empenho.item_pre_empenho
                            LEFT JOIN empenho.item_pre_empenho_julgamento
                                 ON item_pre_empenho_julgamento.cod_pre_empenho  = item_pre_empenho.cod_pre_empenho   
                                AND item_pre_empenho_julgamento.exercicio        = item_pre_empenho.exercicio
                                AND item_pre_empenho_julgamento.num_item         = item_pre_empenho.num_item
                            LEFT JOIN compras.julgamento_item
                                 ON julgamento_item.exercicio      = item_pre_empenho_julgamento.exercicio_julgamento
                                AND julgamento_item.cod_cotacao    = item_pre_empenho_julgamento.cod_cotacao 
                                AND julgamento_item.cod_item       = item_pre_empenho_julgamento.cod_item
                                AND julgamento_item.lote           = item_pre_empenho_julgamento.lote
                                AND julgamento_item.cgm_fornecedor = item_pre_empenho_julgamento.cgm_fornecedor
                            LEFT JOIN compras.julgamento
                                 ON julgamento.exercicio   = julgamento_item.exercicio
                                AND julgamento.cod_cotacao = julgamento_item.cod_cotacao
                            LEFT JOIN compras.cotacao
                                 ON cotacao.cod_cotacao = julgamento.cod_cotacao
                                AND cotacao.exercicio   = julgamento.exercicio
                            LEFT JOIN compras.mapa_cotacao
                                 ON mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                                AND mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                            LEFT JOIN compras.mapa
                                 ON mapa.cod_mapa  = mapa_cotacao.cod_mapa
                                AND mapa.exercicio = mapa_cotacao.exercicio_mapa
                            LEFT JOIN licitacao.licitacao
                                 ON licitacao.exercicio_mapa = mapa.exercicio
                                AND licitacao.cod_mapa = mapa.cod_mapa
                            GROUP BY item_pre_empenho.cod_pre_empenho
                                    , item_pre_empenho.exercicio
                                    , licitacao.cod_licitacao
                                    , licitacao.exercicio
                    ) AS interna
                         ON interna.cod_pre_empenho = pre_empenho.cod_pre_empenho
                        AND interna.exercicio       = pre_empenho.exercicio

                    JOIN orcamento.recurso
                         ON recurso.exercicio   = restos_pre_empenho.exercicio
                        AND recurso.cod_recurso = restos_pre_empenho.recurso

                    LEFT JOIN tcepe.codigo_fonte_recurso
                         ON codigo_fonte_recurso.cod_recurso = recurso.cod_recurso
                        AND codigo_fonte_recurso.exercicio   = recurso.exercicio

                    LEFT JOIN orcamento.despesa
                         ON despesa.exercicio    = pre_empenho_despesa.exercicio
                        AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa

                    LEFT JOIN tcepe.orcamento_modalidade_despesa
                         ON orcamento_modalidade_despesa.exercicio   = despesa.exercicio
                        AND orcamento_modalidade_despesa.cod_despesa = despesa.cod_despesa

                    LEFT JOIN tcepe.configuracao_ordenador
                         ON configuracao_ordenador.cgm_ordenador = sw_cgm_documento.numcgm

                    LEFT JOIN (SELECT * FROM tcepe.fn_restos_pagar(
                                                                '".$this->getDado('exercicio')."',
                                                                '".$this->getDado('cod_entidade')."',
                                                                '31/12/".$this->getDado('exercicio')."'
                                                            ) as rp (
                                                                cod_empenho INTEGER
                                                                , cod_entidade INTEGER
                                                                , exercicio CHARACTER(4)
                                                                , valor_processado_exercicios_anteriores NUMERIC
                                                                , valor_processado_exercicio_anterior NUMERIC
                                                                , valor_processado_cancelado NUMERIC
                                                                , valor_processado_pago NUMERIC
                                                                , valor_nao_processado_exercicios_anteriores NUMERIC
                                                                , valor_nao_processado_exercicio_anterior NUMERIC
                                                                , valor_nao_processado_cancelado NUMERIC
                                                                , valor_nao_processado_pago NUMERIC
                                                            )
                             ORDER BY exercicio, cod_empenho
                    ) AS restos_pagar
                         ON restos_pagar.cod_empenho = empenho.cod_empenho
                        AND restos_pagar.cod_entidade = empenho.cod_entidade
                        AND restos_pagar.exercicio = empenho.exercicio

                    WHERE empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
                    AND empenho.exercicio < '".$this->getDado('exercicio')."'
                    AND ( nota_liquidacao.exercicio='".$this->getDado('exercicio')."'
                         OR
                          pagamento_liquidacao.exercicio='".$this->getDado('exercicio')."'
                         OR
                          to_char(nota_liquidacao_paga.timestamp, 'yyyy')='".$this->getDado('exercicio')."'
                        )
        ) as resultado

        ORDER BY exercicio_empenho, unidade_orcamentaria, cod_funcao, cod_subfuncao

        ";
        return $stSql;
    }
}

?>