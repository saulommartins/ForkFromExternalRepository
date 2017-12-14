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
-- /*
-- *
-- * Script de DDL e DML
-- *
-- * Versao 2.02.0
-- *
-- * Fabio Bertoldi - 20120924
-- *
-- */
-- 
-- ----------------
-- -- Ticket #16858
-- ----------------
-- 
-- BEGIN;
-- 
-- ALTER TABLE sw_andamento ADD COLUMN cod_situacao INTEGER;
-- ALTER TABLE sw_andamento ADD CONSTRAINT fk_andamento_4 FOREIGN KEY                     (cod_situacao)
--                                                        REFERENCES sw_situacao_processo (cod_situacao);
-- 
-- DROP TRIGGER tr_atualiza_ultimo_andamento ON sw_andamento;
-- 
-- CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
-- DECLARE
--     stSQL       VARCHAR;
--     reRecord    RECORD;
-- BEGIN
--     stSQL := '
--                SELECT *
--                  FROM sw_andamento
--                     ;
--              ';
--     FOR reRecord IN EXECUTE stSQL LOOP
--         PERFORM 1
--            FROM sw_recebimento
--           WHERE cod_andamento = reRecord.cod_andamento
--             AND cod_processo  = reRecord.cod_processo
--             AND ano_exercicio = reRecord.ano_exercicio
--               ;
--         IF FOUND THEN
--             UPDATE sw_andamento
--                SET cod_situacao  = 3
--              WHERE cod_andamento = reRecord.cod_andamento
--                AND cod_processo  = reRecord.cod_processo
--                AND ano_exercicio = reRecord.ano_exercicio
--                  ;
--         ELSE
--             UPDATE sw_andamento
--                SET cod_situacao  = 2
--              WHERE cod_andamento = reRecord.cod_andamento
--                AND cod_processo  = reRecord.cod_processo
--                AND ano_exercicio = reRecord.ano_exercicio
--                  ;
--         END IF;
--     END LOOP;
-- 
--     UPDATE sw_andamento
--        SET cod_situacao = sw_processo.cod_situacao
--       FROM sw_processo
--       JOIN sw_ultimo_andamento
--         ON sw_ultimo_andamento.cod_processo  = sw_processo.cod_processo
--        AND sw_ultimo_andamento.ano_exercicio = sw_processo.ano_exercicio
--      WHERE sw_ultimo_andamento.cod_andamento = sw_andamento.cod_andamento
--        AND sw_ultimo_andamento.ano_exercicio = sw_andamento.ano_exercicio
--        AND sw_ultimo_andamento.cod_processo = sw_andamento.cod_processo
--          ;
-- END;
-- $$ LANGUAGE 'plpgsql';
-- 
-- SELECT        manutencao();
-- DROP FUNCTION manutencao();
-- 
-- CREATE TRIGGER tr_atualiza_ultimo_andamento AFTER INSERT OR UPDATE ON sw_andamento FOR EACH ROW EXECUTE PROCEDURE fn_atualiza_ultimo_andamento();
-- 
-- ALTER TABLE sw_andamento ALTER COLUMN cod_situacao SET NOT NULL;
-- 
-- 
-- ----------------
-- -- Ticket #13736
-- ----------------
-- 
-- DROP   TRIGGER tr_monta_codigo_estrutural               ON almoxarifado.catalogo_classificacao;
-- CREATE TRIGGER tr_monta_codigo_estrutural BEFORE INSERT ON almoxarifado.catalogo_classificacao FOR EACH ROW EXECUTE PROCEDURE almoxarifado.fn_monta_codigo_estrutural();


----------------
-- Ticket #18789
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    inCodClassificacao  integer;
    inCodNivel          integer;
    i                   integer;
BEGIN

  PERFORM 1
     FROM administracao.configuracao
    WHERE cod_modulo = 2
      AND exercicio  = '2013'
      AND parametro  = 'cnpj'
      AND valor      = '94068418000184'
        ;

  IF FOUND THEN

    UPDATE almoxarifado.classificacao_nivel
       SET cod_nivel = LTRIM(catalogo_classificacao.cod_estrutural, '0')::INTEGER
      FROM almoxarifado.catalogo_classificacao
     WHERE classificacao_nivel.cod_catalogo      = catalogo_classificacao.cod_catalogo
       AND classificacao_nivel.cod_classificacao = catalogo_classificacao.cod_classificacao
       AND classificacao_nivel.cod_catalogo      = 2
         ;

      UPDATE almoxarifado.catalogo_classificacao
         SET cod_estrutural = TRIM(cod_estrutural)
       WHERE cod_catalogo = 2
           ;
  END IF;

END;
$$
LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


-- ----------------
-- -- Ticket #13736
-- ----------------
-- 
-- DROP   TRIGGER tr_monta_codigo_estrutural               ON almoxarifado.catalogo_classificacao;
-- CREATE TRIGGER tr_monta_codigo_estrutural BEFORE UPDATE ON almoxarifado.catalogo_classificacao FOR EACH ROW EXECUTE PROCEDURE almoxarifado.fn_monta_codigo_estrutural();
-- 
-- 
-- -----------------------------------------------------------------------------
-- -- ADICIONANDO FK DE compras.compraa_direta EM compras.compra_direta_processo
-- -----------------------------------------------------------------------------
-- 
-- ALTER TABLE compras.compra_direta_processo  ADD CONSTRAINT fk_compra_direta_processo FOREIGN KEY (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade)
--                                                                                      REFERENCES compras.compra_direta (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade);
-- 
-- 
-- ----------------------------------------------------------------
-- -- ADICIONANDO FK DE empenho.empenho EM frota.manutencao_empenho
-- ----------------------------------------------------------------
-- 
-- ALTER TABLE frota.manutencao_empenho ADD CONSTRAINT fk_manutencao_empenho_2 FOREIGN KEY (cod_entidade, cod_empenho, exercicio_empenho)
--                                                                             REFERENCES empenho.empenho (cod_entidade, cod_empenho, exercicio);
-- 
-- 
-- -----------------------------------------------------------------
-- -- ADICIONANDO FK DE compras.compra_direta EM compras.homologacao
-- -----------------------------------------------------------------
-- 
-- ALTER TABLE compras.homologacao ADD CONSTRAINT fk_homologacao_3 FOREIGN KEY (cod_compra_direta, cod_entidade, exercicio_compra_direta, cod_modalidade)
--                                                                 REFERENCES compras.compra_direta(cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade);

