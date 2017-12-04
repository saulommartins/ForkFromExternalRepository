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
/*
*
* Script de DDL e DML
*
* Versao 2.04.0
*
* Fabio Bertoldi - 20150515
*
*/

----------------
-- Ticket #22844
----------------

UPDATE contabilidade.sistema_contabil SET grupos = '1,2,3,4' WHERE exercicio IN ('2014','2015') AND cod_sistema = 1;
UPDATE contabilidade.sistema_contabil SET grupos = '5,6'     WHERE exercicio IN ('2014','2015') AND cod_sistema = 2;
UPDATE contabilidade.sistema_contabil SET grupos = '7,8'     WHERE exercicio IN ('2014','2015') AND cod_sistema = 3;
UPDATE contabilidade.sistema_contabil SET grupos = '0'       WHERE exercicio IN ('2014','2015') AND cod_sistema = 4;


----------------
-- Ticket #20490
----------------

CREATE TYPE tcemg_fn_recurso_alienacao_ativo AS (
    cod_entidade    INTEGER,
    cod_vinculo     INTEGER,
    rec_realizada   NUMERIC,
    saldo_inicial   NUMERIC,
    empenhado_per   NUMERIC,
    pago_per        NUMERIC,
    liquidado_per   NUMERIC
);

DROP FUNCTION tcemg.fn_recurso_alienacao_ativo(varchar, integer, varchar);


----------------
-- Ticket ##20474
----------------

DROP FUNCTION stn.fn_comparativoPe( varchar, varchar, varchar, varchar);


----------------
-- Ticket #
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 3050
     , 364
     , 'FMManterConfiguracaoOrgaoUnidadeContas.php'
     , 'confgOrgUnCc'
     , 4
     , ''
     , 'Configurar Órgão/Unidade das Contas Contábeis'
     , TRUE
     );

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    stSQL := '
                 SELECT cod_entidade
                   FROM orcamento.entidade
               GROUP BY cod_entidade
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2015'
             , reRecord.cod_entidade
             , 42
             , 'orgao_unidade_camara'
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2015'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 42
                        AND parametro    = 'orgao_unidade_camara'
                   )
             ;
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2015'
             , reRecord.cod_entidade
             , 42
             , 'orgao_unidade_outros'
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2015'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 42
                        AND parametro    = 'orgao_unidade_outros'
                   )
             ;
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2015'
             , reRecord.cod_entidade
             , 42
             , 'orgao_unidade_prefeitura'
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2015'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 42
                        AND parametro    = 'orgao_unidade_prefeitura'
                   )
             ;
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2015'
             , reRecord.cod_entidade
             , 42
             , 'orgao_unidade_rpps'
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2015'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 42
                        AND parametro    = 'orgao_unidade_rpps'
                   )
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #20477
----------------

DROP FUNCTION tcemg.fn_despesa_capital(VARCHAR, VARCHAR, INTEGER);


----------------
-- Ticket #23014
----------------

CREATE TYPE relatorio_restos_pagar_dotacao_credor AS (
    entidade            INTEGER
    , empenho           INTEGER
    , dt_empenho        VARCHAR
    , dt_liquidacao     VARCHAR
    , exercicio         CHAR(4)
    , credor            VARCHAR
    , cod_estrutural    VARCHAR
    , cod_recurso       INTEGER
    , cod_recurso_banco INTEGER
    , dotacao           VARCHAR
    , cod_nota          INTEGER
    , dt_pagamento      VARCHAR
    , conta             INTEGER
    , banco             VARCHAR
    , valor_pago        NUMERIC
);

