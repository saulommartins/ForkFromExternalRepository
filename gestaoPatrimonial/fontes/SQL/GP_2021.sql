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
* Versao 2.02.1
*
* Fabio Bertoldi - 20130624
*
*/

----------------
-- Ticket #20200
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
  ( 2890
  , 24
  , 'FLLancamentoContabilDepreciacao.php'
  , 'incluir'
  , 30
  , ''
  , 'Gerar Lançamento Contábil de Depreciação'
  , TRUE
  );
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
  ( 2891
  , 24
  , 'FLLancamentoContabilDepreciacao.php'
  , 'estornar'
  , 35
  , ''
  , 'Estorno de Lançamento Contábil de Depreciação'
  , TRUE
  );

CREATE TABLE contabilidade.lancamento_depreciacao(
    id                      INTEGER         NOT NULL,
    timestamp               TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    exercicio               CHAR(4)         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    tipo                    CHAR(1)         NOT NULL,
    cod_lote                INTEGER         NOT NULL,
    sequencia               INTEGER         NOT NULL,
    cod_depreciacao         INTEGER         NOT NULL,
    cod_bem                 INTEGER         NOT NULL,
    timestamp_depreciacao   TIMESTAMP       NOT NULL,
    estorno                 BOOLEAN         NOT NULL,
    CONSTRAINT pk_lancamento_depreciacao    PRIMARY KEY (id),
    CONSTRAINT fk_lancamento_depreciacao_1  FOREIGN KEY                         (exercicio, cod_entidade, tipo, cod_lote, sequencia)
                                            REFERENCES contabilidade.lancamento (exercicio, cod_entidade, tipo, cod_lote, sequencia),
    CONSTRAINT fk_lancamento_depreciacao_2  FOREIGN KEY                         (cod_depreciacao, cod_bem, timestamp_depreciacao)
                                            REFERENCES patrimonio.depreciacao   (cod_depreciacao, cod_bem, timestamp)
);
GRANT ALL ON contabilidade.lancamento_depreciacao TO urbem;


INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 962
     , '2013'
     , 'Vlr ref Depreciação Acumulada mês'
     , TRUE
     , TRUE
     );

CREATE TABLE patrimonio.grupo_plano_depreciacao(
    cod_natureza    INTEGER     NOT NULL,
    cod_grupo       INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_plano       INTEGER     NOT NULL,
    CONSTRAINT pk_grupo_plano_depreciacao       PRIMARY KEY                              (cod_natureza, cod_grupo, exercicio, cod_plano),
    CONSTRAINT fk_grupo_plano_depreciacao_1     FOREIGN KEY                              (cod_natureza, cod_grupo)
                                                REFERENCES patrimonio.grupo              (cod_natureza, cod_grupo),
    CONSTRAINT fk_grupo_plano_depreciacao_2     FOREIGN KEY                              (exercicio, cod_plano)
                                                REFERENCES contabilidade.plano_analitica (exercicio, cod_plano)
);
GRANT ALL ON patrimonio.grupo TO urbem;


CREATE TABLE patrimonio.bem_plano_depreciacao(
    cod_bem         INTEGER     NOT NULL,
    timestamp       TIMESTAMP   NOT NULL DEFAULT ('now'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,
    cod_plano       INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    CONSTRAINT pk_bem_plano_depreciacao     PRIMARY KEY                             (cod_bem, timestamp),
    CONSTRAINT fk_bem_plano_depreciacao_1   FOREIGN KEY                             (cod_bem)
                                            REFERENCES patrimonio.bem               (cod_bem),
    CONSTRAINT fk_bem_plano_depreciacao_2   FOREIGN KEY                             (cod_plano, exercicio)
                                            REFERENCES contabilidade.plano_analitica(cod_plano, exercicio)
);
GRANT ALL ON patrimonio.bem_plano_depreciacao TO urbem;

ALTER TABLE patrimonio.depreciacao DROP CONSTRAINT fk_depreciacao_1;
ALTER TABLE patrimonio.depreciacao ADD  CONSTRAINT fk_depreciacao_1 FOREIGN KEY (cod_bem, timestamp)
                                                                    REFERENCES patrimonio.bem_plano_depreciacao (cod_bem, timestamp);


----------------
-- Ticket #16858
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN

    PERFORM 1
       FROM pg_class
          , pg_attribute
          , pg_type
      WHERE pg_class.relname      = 'sw_andamento'
        AND pg_attribute.attname  = 'cod_situacao'
        AND pg_attribute.attnum   > 0
        AND pg_attribute.attrelid = pg_class.oid
        AND pg_attribute.atttypid = pg_type.oid
           ;
    IF NOT FOUND THEN
        ALTER TABLE sw_andamento ADD COLUMN cod_situacao INTEGER;
        ALTER TABLE sw_andamento ADD CONSTRAINT fk_andamento_4 FOREIGN KEY                     (cod_situacao)
                                                               REFERENCES sw_situacao_processo (cod_situacao);
    END IF;

    DROP TRIGGER tr_atualiza_ultimo_andamento ON sw_andamento;

    stSQL := '
               SELECT *
                 FROM sw_andamento
                    ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        PERFORM 1
           FROM sw_recebimento
          WHERE cod_andamento = reRecord.cod_andamento
            AND cod_processo  = reRecord.cod_processo
            AND ano_exercicio = reRecord.ano_exercicio
              ;
        IF FOUND THEN
            UPDATE sw_andamento
               SET cod_situacao  = 3
             WHERE cod_andamento = reRecord.cod_andamento
               AND cod_processo  = reRecord.cod_processo
               AND ano_exercicio = reRecord.ano_exercicio
                 ;
        ELSE
            UPDATE sw_andamento
               SET cod_situacao  = 2
             WHERE cod_andamento = reRecord.cod_andamento
               AND cod_processo  = reRecord.cod_processo
               AND ano_exercicio = reRecord.ano_exercicio
                 ;
        END IF;
    END LOOP;

    UPDATE sw_andamento
       SET cod_situacao = sw_processo.cod_situacao
      FROM sw_processo
      JOIN sw_ultimo_andamento
        ON sw_ultimo_andamento.cod_processo  = sw_processo.cod_processo
       AND sw_ultimo_andamento.ano_exercicio = sw_processo.ano_exercicio
     WHERE sw_ultimo_andamento.cod_andamento = sw_andamento.cod_andamento
       AND sw_ultimo_andamento.ano_exercicio = sw_andamento.ano_exercicio
       AND sw_ultimo_andamento.cod_processo = sw_andamento.cod_processo
         ;

    CREATE TRIGGER tr_atualiza_ultimo_andamento AFTER INSERT OR UPDATE ON sw_andamento FOR EACH ROW EXECUTE PROCEDURE fn_atualiza_ultimo_andamento();

    ALTER TABLE sw_andamento ALTER COLUMN cod_situacao SET NOT NULL;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #13736
----------------

DROP   TRIGGER tr_monta_codigo_estrutural               ON almoxarifado.catalogo_classificacao;
CREATE TRIGGER tr_monta_codigo_estrutural BEFORE UPDATE ON almoxarifado.catalogo_classificacao FOR EACH ROW EXECUTE PROCEDURE almoxarifado.fn_monta_codigo_estrutural();


CREATE OR REPLACE function manutencao_fk() RETURNS VOID AS $$
DECLARE

BEGIN
    -----------------------------------------------------------------------------
    -- ADICIONANDO FK DE compras.compraa_direta EM compras.compra_direta_processo
    -----------------------------------------------------------------------------
    PERFORM 1
       FROM pg_constraint
      WHERE conname = 'fk_compra_direta_processo'
          ;
    IF NOT FOUND THEN
        ALTER TABLE compras.compra_direta_processo  ADD CONSTRAINT fk_compra_direta_processo FOREIGN KEY (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade)
                                                                                             REFERENCES compras.compra_direta (cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade);
    END IF;


----------------------------------------------------------------
-- ADICIONANDO FK DE empenho.empenho EM frota.manutencao_empenho
----------------------------------------------------------------
    PERFORM 1
       FROM pg_constraint
      WHERE conname = 'fk_manutencao_empenho_2'
          ;
    IF NOT FOUND THEN
        ALTER TABLE frota.manutencao_empenho ADD CONSTRAINT fk_manutencao_empenho_2 FOREIGN KEY (cod_entidade, cod_empenho, exercicio_empenho)
                                                                                    REFERENCES empenho.empenho (cod_entidade, cod_empenho, exercicio);
    END IF;


-----------------------------------------------------------------
-- ADICIONANDO FK DE compras.compra_direta EM compras.homologacao
-----------------------------------------------------------------
    PERFORM 1
       FROM pg_constraint
      WHERE conname = 'fk_homologacao_3'
          ;
    IF NOT FOUND THEN
        ALTER TABLE compras.homologacao ADD CONSTRAINT fk_homologacao_3 FOREIGN KEY (cod_compra_direta, cod_entidade, exercicio_compra_direta, cod_modalidade)
                                                                        REFERENCES compras.compra_direta(cod_compra_direta, cod_entidade, exercicio_entidade, cod_modalidade);
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao_fk();
DROP FUNCTION manutencao_fk();

