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
* Versao 2.05.1
*
* Fabio Bertoldi - 20160520
*
*/

----------------
-- Ticket #20201
----------------

ALTER TABLE patrimonio.grupo_plano_analitica ADD COLUMN cod_plano_alienacao_ganho INTEGER;
ALTER TABLE patrimonio.grupo_plano_analitica ADD CONSTRAINT fk_grupo_plano_analitica_6 FOREIGN KEY                              (cod_plano_alienacao_ganho   , exercicio)
                                                                                       REFERENCES contabilidade.plano_analitica (cod_plano                   , exercicio);

ALTER TABLE patrimonio.grupo_plano_analitica ADD COLUMN cod_plano_alienacao_perda INTEGER;
ALTER TABLE patrimonio.grupo_plano_analitica ADD CONSTRAINT fk_grupo_plano_analitica_7 FOREIGN KEY                              (cod_plano_alienacao_perda   , exercicio)
                                                                                       REFERENCES contabilidade.plano_analitica (cod_plano                   , exercicio);

INSERT INTO patrimonio.tipo_baixa VALUES (7, 'Ganho de Baixa Patrimonial por Alienação' );
INSERT INTO patrimonio.tipo_baixa VALUES (8, 'Perda de Baixa Patrimonial por Alienação' );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 968
     , '2016'
     , 'Vlr. Ref. Lançamento Contábil de Baixa de Bem por Alienação'
     , TRUE
     , TRUE
     );

INSERT
  INTO contabilidade.historico_contabil
     ( cod_historico
     , exercicio
     , nom_historico
     , complemento
     , historico_interno
     )
VALUES
     ( 969
     , '2016'
     , 'Vlr. Ref. Estorno de Lançamento Contábil de Baixa de Bem por Alienação'
     , TRUE
     , TRUE
     );

CREATE TABLE contabilidade.lancamento_baixa_patrimonio_alienacao(
    id                      INTEGER     NOT NULL,
    timestamp               TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    cod_lote                INTEGER     NOT NULL,
    sequencia               INTEGER     NOT NULL,
    cod_bem                 INTEGER     NOT NULL,
    cod_arrecadacao         INTEGER     NOT NULL,
    exercicio_arrecadacao   CHAR(4)     NOT NULL,
    timestamp_arrecadacao   TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    estorno                 BOOLEAN     NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_lancamento_baixa_patrimonio_alienacao     PRIMARY KEY                         (id),
    CONSTRAINT fk_lancamento_baixa_patrimonio_alienacao_1   FOREIGN KEY                         (exercicio, cod_entidade, tipo, cod_lote, sequencia)
                                                            REFERENCES contabilidade.lancamento (exercicio, cod_entidade, tipo, cod_lote, sequencia),
    CONSTRAINT fk_lancamento_baixa_patrimonio_alienacao_2   FOREIGN KEY                         (cod_bem)
                                                            REFERENCES patrimonio.bem           (cod_bem),
    CONSTRAINT fk_lancamento_baixa_patrimonio_alienacao_3   FOREIGN KEY              (cod_arrecadacao, exercicio_arrecadacao, timestamp_arrecadacao)
                                                            REFERENCES tesouraria.arrecadacao    (cod_arrecadacao, exercicio, timestamp_arrecadacao)
);
GRANT ALL ON contabilidade.lancamento_baixa_patrimonio_alienacao TO urbem;


----------------
-- Ticket #23778
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM administracao.configuracao
      WHERE cod_modulo = 2
        AND exercicio  = '2016'
        AND parametro  = 'cnpj'
        AND valor      = '12198693000158'
          ;
    IF FOUND THEN
        -- Despesa 9000000000000000
        DELETE
          FROM orcamento.classificacao_despesa
         WHERE cod_conta = 901
           AND exercicio = '2016'
             ;
        DELETE
          FROM orcamento.conta_despesa
         WHERE cod_conta = 901
           AND exercicio = '2016'
             ;

        -- Receita 7000000000000000
        DELETE
          FROM orcamento.classificacao_receita
         WHERE cod_conta = 727
           AND exercicio = '2016'
             ;
        DELETE
          FROM orcamento.conta_receita
         WHERE cod_conta = 727
           AND exercicio = '2016'
             ;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

