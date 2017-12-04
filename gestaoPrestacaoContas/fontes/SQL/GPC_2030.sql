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
* Versao 2.03.0
*
* Fabio Bertoldi - 20140821
*
*/

----------------
-- Ticket #19058
----------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 36
     , 58
     , 'RREO - Anexo XVIII'
     , 'RREOAnexoXVIII.rptdesign'
     );


----------------
-- Ticket #22080
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_class
       JOIN pg_attribute
         ON pg_attribute.attrelid = pg_class.oid
       JOIN pg_namespace
         ON pg_class.relnamespace = pg_namespace.oid
      WHERE pg_namespace.nspname = 'tcemg'
        AND pg_class.relname     = 'conta_bancaria'
        AND pg_attribute.attname = 'cod_ctb_anterior'
        AND pg_attribute.attnum  > 0;

    IF NOT FOUND THEN
        ALTER TABLE tcemg.conta_bancaria ADD COLUMN cod_ctb_anterior BIGINT;
    END IF;

END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #22099
----------------

CREATE TABLE tcepb.tipo_retencao(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(25)     NOT NULL,
    CONSTRAINT pk_tipo_retencao     PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcepb.tipo_retencao TO urbem;


INSERT INTO tcepb.tipo_retencao VALUES (1, 'ISS'                );
INSERT INTO tcepb.tipo_retencao VALUES (2, 'IRRF'               );
INSERT INTO tcepb.tipo_retencao VALUES (3, 'INSS'               );
INSERT INTO tcepb.tipo_retencao VALUES (4, 'Previdência Própria');
INSERT INTO tcepb.tipo_retencao VALUES (5, 'Outras Consignações');


CREATE TABLE tcepb.plano_analitica_tipo_retencao(
    cod_plano       INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    cod_tipo        INTEGER         NOT NULL,
    CONSTRAINT pk_plano_analitica_tipo_retencao     PRIMARY KEY                             (cod_plano, exercicio),
    CONSTRAINT fk_plano_analitica_tipo_retencao_1   FOREIGN KEY                             (cod_plano, exercicio)
                                                    REFERENCES contabilidade.plano_analitica(cod_plano, exercicio),
    CONSTRAINT fk_plano_analitica_tipo_retencao_2   FOREIGN KEY                             (cod_tipo)
                                                    REFERENCES tcepb.tipo_retencao          (cod_tipo)
);
GRANT ALL ON tcepb.plano_analitica_tipo_retencao TO urbem;

CREATE TABLE tcepb.plano_analitica_relacionamento(
    exercicio                       CHAR(4)         NOT NULL,
    cod_plano                       INTEGER         NOT NULL,
    tipo                            CHAR(1)         NOT NULL,
    cod_relacionamento              INTEGER         NOT NULL,
    CONSTRAINT pk_plano_analitica_relacionamento    PRIMARY KEY                             (exercicio, cod_plano, tipo),
    CONSTRAINT fk_plano_analitica_relacionamento    FOREIGN KEY                             (exercicio, cod_plano)
                                                    REFERENCES contabilidade.plano_analitica(exercicio, cod_plano),
    CONSTRAINT ck_plano_analitica_relacionamento    CHECK (tipo IN ('D', 'R'))
);
GRANT ALL ON tcepb.plano_analitica_relacionamento TO urbem;

DROP TABLE tcepb.relacionamento_despesa_extra;
DROP TABLE tcepb.relacionamento_receita_extra;

UPDATE administracao.acao SET nom_arquivo = 'FMManterRelacionamentoExtra.php', nom_acao = 'Relacionar Rec/Desp Extra' WHERE cod_acao = 2194;
UPDATE administracao.acao SET ativo = FALSE WHERE cod_acao = 2193;


----------------
-- Ticket #21815
----------------

UPDATE tcepb.tipo_origem_recurso SET descricao = 'Receita de Impostos e de Transferência de Impostos - Educação'      WHERE cod_tipo =  1 AND exercicio = '2014';
UPDATE tcepb.tipo_origem_recurso SET descricao = 'Receita de Impostos e de Transferência de Impostos - Saúde'         WHERE cod_tipo =  2 AND exercicio = '2014';
UPDATE tcepb.tipo_origem_recurso SET descricao = 'Contribuição para o RPPS (patronal, servidores e comp. financeira)' WHERE cod_tipo =  3 AND exercicio = '2014';
UPDATE tcepb.tipo_origem_recurso SET descricao = 'Contribuição ao Programa Ensino Fundamental'                        WHERE cod_tipo =  4 AND exercicio = '2014';
UPDATE tcepb.tipo_origem_recurso SET descricao = 'Serviços de Saúde'                                                  WHERE cod_tipo = 12 AND exercicio = '2014';

DELETE FROM tcepb.tipo_origem_recurso WHERE cod_tipo IN (5, 6, 7, 8, 9, 10, 11, 99) AND exercicio = '2014';

INSERT INTO tcepb.tipo_origem_recurso VALUES ( 0, 'Recursos Ordinários'                                                   , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (13, 'Serviços Educacionais'                                                 , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (14, 'Transferência de Recursos do SUS'                                      , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (15, 'Transferência de Recursos do FNDE'                                     , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (16, 'Recursos da CIDE'                                                      , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (17, 'Contribuição para o custeio dos serviços de iluminação pública - COSIP', '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (18, 'Transferência do FUNDEB (magistério)'                                  , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (19, 'Transferência do FUNDEB (outras)'                                      , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (29, 'Transferência de Recursos do FNAS'                                     , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (41, 'Serviços Hospitalares'                                                 , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (50, 'Transferência de Convênios – Educação – Federal'                       , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (51, 'Transferência de Convênios – Saúde – Federal'                          , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (52, 'Transferência de Convênios – Outros – Federal'                         , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (53, 'Transferência de Convênios – Educação – Estadual/Municipal/Outros'     , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (54, 'Transferência de Convênios – Saúde – Estadual/Municipal/Outros'        , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (55, 'Transferência de Convênios – Outros - Estadual/Municipal/Outros'       , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (60, 'Royalties Educação'                                                    , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (61, 'Royalties Saúde'                                                       , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (62, 'FE – Petrobras'                                                        , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (90, 'Operações de Crédito Interna'                                          , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (91, 'Operações de Crédito Externa'                                          , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (92, 'Alienação de Bens'                                                     , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (93, 'Outras Receitas não-primárias'                                         , '2014');
INSERT INTO tcepb.tipo_origem_recurso VALUES (94, 'Remuneração de depósitos bancários'                                    , '2014');


----------------
-- Ticket #21827
----------------

CREATE TABLE tcepb.pagamento_origem_recursos_interna(
    cod_entidade                INTEGER                 NOT NULL,
    exercicio                   CHAR(4)                 NOT NULL,
    timestamp                   TIMESTAMP               NOT NULL DEFAULT ('now'::TEXT)::TIMESTAMP(3) WITH TIME ZONE,
    cod_nota                    INTEGER                 NOT NULL,
    cod_origem_recursos         INTEGER                 NOT NULL,
    exercicio_origem_recurso    CHAR(4)                 NOT NULL,
    CONSTRAINT pk_pagamento_origem_recursos_interna     PRIMARY KEY                          (cod_entidade, exercicio, timestamp, cod_nota),
    CONSTRAINT fk_pagamento_origem_recursos_interna_1   FOREIGN KEY                          (cod_entidade, exercicio, timestamp, cod_nota)
                                                        REFERENCES tesouraria.pagamento      (cod_entidade, exercicio, timestamp, cod_nota),
    CONSTRAINT fk_pagamento_origem_recursos_interna_2   FOREIGN KEY                          (cod_origem_recursos, exercicio_origem_recurso)
                                                        REFERENCES tcepb.tipo_origem_recurso (cod_tipo, exercicio)
);
GRANT ALL ON tcepb.pagamento_origem_recursos_interna TO urbem;

INSERT
  INTO tcepb.pagamento_origem_recursos_interna
     ( cod_entidade
     , exercicio
     , timestamp
     , cod_nota
     , cod_origem_recursos
     , exercicio_origem_recurso
     )
SELECT pag_tesouraria.cod_entidade
     , pag_tesouraria.exercicio
     , pag_tesouraria.timestamp
     , pag_tesouraria.cod_nota
     , tipo_origem_recurso.cod_tipo
     , tipo_origem_recurso.exercicio
  FROM tesouraria.pagamento_origem_recursos_interna AS pag_tesouraria
  JOIN tcepb.tipo_origem_recurso
    ON tipo_origem_recurso.cod_tipo = pag_tesouraria.cod_origem_recursos
 WHERE tipo_origem_recurso.exercicio = '2014'
     ;

DROP TABLE tesouraria.pagamento_origem_recursos_interna;
DROP TABLE empenho.origem_recursos_tcepb_interna;

----------------
-- Ticket #22106
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_class
       JOIN pg_attribute
         ON pg_attribute.attrelid = pg_class.oid
       JOIN pg_namespace
         ON pg_class.relnamespace = pg_namespace.oid
      WHERE pg_namespace.nspname = 'tcemg'
        AND pg_class.relname = 'uniorcam'
        AND pg_attribute.attname = 'cod_unidade_anterior'
        AND pg_attribute.attnum > 0
          ;
    IF NOT FOUND THEN
        ALTER TABLE tcemg.uniorcam ADD COLUMN cod_unidade_anterior INTEGER;
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();

----------------
-- Ticket #21979
----------------

UPDATE administracao.acao SET ordem = ordem + 41 WHERE cod_funcionalidade = 484 AND ordem > 8;
UPDATE administracao.acao SET ordem = 50 WHERE cod_acao = 2970;
UPDATE administracao.acao SET ordem = 51 WHERE cod_acao = 2967;


----------------
-- Ticket #19053
----------------

UPDATE administracao.relatorio
   SET arquivo = 'RREOAnexoXI.rptdesign'
 WHERE cod_gestao    = 6
   AND cod_modulo    = 36
   AND cod_relatorio = 29
     ;


----------------
-- Ticket #21208
----------------

INSERT INTO licitacao.tipo_contrato (cod_tipo, descricao, sigla) VALUES (39, 'Termo de Contrato de Gestão', 'TCG');
INSERT INTO licitacao.tipo_contrato (cod_tipo, descricao, sigla) VALUES (40, 'Termo Aditivo de Contrato de Gestão', 'TACG');
INSERT INTO licitacao.tipo_contrato (cod_tipo, descricao, sigla) VALUES (41, 'Termo de Rescisão de Cessão', 'TRC');
INSERT INTO licitacao.tipo_contrato (cod_tipo, descricao, sigla) VALUES (42, 'Termo de Apostilamento de Contrato', 'ACSS');
INSERT INTO licitacao.tipo_contrato (cod_tipo, descricao, sigla) VALUES (43, 'Apólice de Contratação de Serviços de Seguro', 'TACG');
INSERT INTO licitacao.tipo_contrato (cod_tipo, descricao, sigla) VALUES (44, 'Termo Aditivo de Apólice de Contratação de Serviços de Seguro', 'TAACSS');



----------------
-- Ticket #22183
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
     ( 2989
     , 365
     , 'FMTCEPBConfiguracaoTransferenciasConcedidasRecebidas.php'
     , 'incluir'
     , 95
     , ''
     , 'Config para Transferências Concedidas/Recebidas'
     , TRUE
     );

CREATE TABLE tcepb.tipo_transferencia(
    cod_tipo        INTEGER             NOT NULL,
    descricao       VARCHAR(50)         NOT NULL,
    CONSTRAINT pk_tipo_transferencia    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcepb.tipo_transferencia TO urbem;

INSERT INTO tcepb.tipo_transferencia VALUES (1, 'Duodécimo'                                       );
INSERT INTO tcepb.tipo_transferencia VALUES (2, 'Transferência Patronal (FUNDEB Magistério)'      );
INSERT INTO tcepb.tipo_transferencia VALUES (3, 'Transferência Patronal (FUNDEB Outros)'          );
INSERT INTO tcepb.tipo_transferencia VALUES (4, 'Transferência Patronal (MDE)'                    );
INSERT INTO tcepb.tipo_transferencia VALUES (5, 'Transferência Patronal (Saúde Recursos Próprios)');
INSERT INTO tcepb.tipo_transferencia VALUES (6, 'Transferência Patronal (Saúde SUS)'              );
INSERT INTO tcepb.tipo_transferencia VALUES (7, 'Transferência Patronal (Outros)'                 );
INSERT INTO tcepb.tipo_transferencia VALUES (8, 'Devolução de Recursos'                           );
INSERT INTO tcepb.tipo_transferencia VALUES (9, 'Transferências Indireta'                         );

CREATE TABLE tcepb.plano_conta_tipo_transferencia(
    cod_conta       INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_tipo        INTEGER     NOT NULL,
    CONSTRAINT pk_plano_conta_tipo_transferencia    PRIMARY KEY                          (cod_conta, exercicio),
    CONSTRAINT fk_plano_conta_tipo_transferencia_1  FOREIGN KEY                          (cod_conta, exercicio)
                                                    REFERENCES contabilidade.plano_conta (cod_conta, exercicio),
    CONSTRAINT fk_plano_conta_tipo_transferencia_2  FOREIGN KEY                          (cod_tipo)
                                                    REFERENCES tcepb.tipo_transferencia  (cod_tipo)
);
GRANT ALL ON tcepb.plano_conta_tipo_transferencia TO urbem;


