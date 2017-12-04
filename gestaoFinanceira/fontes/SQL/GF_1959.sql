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
* Script de DDL e DML
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: $
*
* Versão 1.95.9
*/

----------------
-- Ticket #15602
----------------

UPDATE administracao.acao
   SET ordem = 6
 WHERE cod_acao = 2244;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2745
          , 411
          , 'FLModelosAMF.php'
          , 'demons2'
          , 2
          , ''
          , 'Demonstrativo II'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 6
          , 36
          , 38
          , 'AMF - Demonstrativo II'
          , 'AMFDemonstrativo2.rptdesign'
          );


----------------------------------------------------------------------------------
-- ADICIONANDO TABELAS ldo.tipo_receita_despesa E ldo.configuracao_receita_despesa --
----------------------------------------------------------------------------------

CREATE TABLE ldo.tipo_receita_despesa (
    cod_tipo            INTEGER         NOT NULL,
    tipo                CHAR(1)         NOT NULL,
    cod_estrutural      CHAR(150)       NOT NULL,
    nivel               NUMERIC(1)      NOT NULL,
    descricao           CHAR(160)       NOT NULL,
    rpps                BOOLEAN         NOT NULL DEFAULT false,
    CONSTRAINT pk_tipo_receita_despesa  PRIMARY KEY (cod_tipo, tipo)
);

GRANT ALL ON ldo.tipo_receita_despesa TO GROUP urbem;

CREATE TABLE ldo.configuracao_receita_despesa (
    cod_ppa                 INTEGER         NOT NULL,
    ano                     CHAR(1)         NOT NULL,
    cod_tipo                INTEGER         NOT NULL,
    tipo                    CHAR(1)         NOT NULL,
    exercicio               CHAR(4)         NOT NULL,
    vl_arrecadado_liquidado NUMERIC(14,2)   NOT NULL,
    vl_previsto_fixado      NUMERIC(14,2)   NOT NULL,
    vl_projetado            NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_configuracao_receita_despesa      PRIMARY KEY                         (cod_ppa, ano, cod_tipo, tipo, exercicio),
    CONSTRAINT fk_configuracao_receita_despesa_1    FOREIGN KEY                         (cod_ppa, ano)
                                                    REFERENCES ldo.ldo                  (cod_ppa, ano),
    CONSTRAINT fk_configuracao_receita_despesa_2    FOREIGN KEY                         (cod_tipo, tipo)
                                                    REFERENCES ldo.tipo_receita_despesa (cod_tipo, tipo)
);

GRANT ALL ON ldo.configuracao_receita_despesa TO GROUP urbem;


----------------
-- Ticket #15534
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2743
          , 456
          , 'FLDespesaReceita.php'
          , 'incluir'
          , 8
          , ''
          , 'Despesa/Receita'
          );

INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (1 ,'1.0.0.0.00.00.00.00.00','RECEITAS CORRENTES'                       ,'R',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (2 ,'1.1.0.0.00.00.00.00.00','RECEITA TRIBUTARIA'                       ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (3 ,'1.2.0.0.00.00.00.00.00','RECEITA DE CONTRIBUIÇÕES'                 ,'R',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (4 ,'1.2.0.0.00.00.00.00.00','Receitas de Contribuições - PM'           ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (5 ,'1.2.0.0.00.00.00.00.00','Receitas de Contribuições - RPPS'         ,'R',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (6 ,'1.3.0.0.00.00.00.00.00','RECEITA PATRIMONIAL'                      ,'R',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (7 ,'1.3.2.0.00.00.00.00.00','Rendimentos de Aplicações Financeiras'    ,'R',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (8 ,'1.3.2.0.00.00.00.00.00','Rendimentos de Aplicações PM'             ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (9 ,'1.3.2.0.00.00.00.00.00','Rendimentos de Aplicações RPPS'           ,'R',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (10,'1.3.9.0.00.00.00.00.00','Outras Receitas Patrimoniais'             ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (11,'1.4.0.0.00.00.00.00.00','RECEITA AGROPECUÁRIA'                     ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (12,'1.5.0.0.00.00.00.00.00','RECEITA INDUSTRIAL'                       ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (13,'1.6.0.0.00.00.00.00.00','RECEITA DE SERVIÇOS'                      ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (14,'1.7.0.0.00.00.00.00.00','TRANSFERÊNCIAS CORRENTES'                 ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (15,'1.9.0.0.00.00.00.00.00','OUTRAS RECEITAS CORRENTES'                ,'R',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (16,'1.9.0.0.00.00.00.00.00','Outras Receitas Correntes - PM'           ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (17,'1.9.0.0.00.00.00.00.00','Outras Receitas Correntes - RPPS'         ,'R',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (18,'2.0.0.0.00.00.00.00.00','RECEITAS DE CAPITAL'                      ,'R',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (19,'2.1.0.0.00.00.00.00.00','OPERAÇÕES DE CRÉDITO'                     ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (20,'2.2.0.0.00.00.00.00.00','ALIENAÇÃO DE BENS'                        ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (21,'2.3.0.0.00.00.00.00.00','AMORTIZAÇÃO DE EMPRÉSTIMOS'               ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (22,'2.4.0.0.00.00.00.00.00','TRANSFERÊNCIAS DE CAPITAL'                ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (23,'2.5.0.0.00.00.00.00.00','OUTRAS RECEITAS DE CAPITAL'               ,'R',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (24,'7.2.1.0.00.00.00.00.00','Receitas Intra Orçamentárias - RPPS'      ,'R',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (25,'9.7.0.0.00.00.00.00.00','(-) DEDUÇÕES DA RECEITA'                  ,'R',1, false);

INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (26,'3.0.0.0.00.00.00.00.00','DESPESAS CORRENTES'                       ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (27,'3.1.0.0.00.00.00.00.00','PESSOAL E ENCARGOS SOCIAIS'               ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (28,'3.1.0.0.00.00.00.00.00','Pessoal Próprio'                          ,'D',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (29,'3.1.0.0.00.00.00.00.00','Pessoal do RPPS'                          ,'D',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (30,'3.2.0.0.00.00.00.00.00','JUROS E ENCARGOS DA DÍVIDA'               ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (31,'3.2.0.0.00.00.00.00.00','Juros e Encargos da Dívida'               ,'D',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (32,'3.2.0.0.00.00.00.00.00','Juros e Encargos da Dívida RPPS'          ,'D',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (33,'3.3.0.0.00.00.00.00.00','OUTRAS DESPESAS CORRENTES'                ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (34,'3.3.0.0.00.00.00.00.00','Outras Despesas Correntes'                ,'D',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (35,'3.3.0.0.00.00.00.00.00','Outras Despesas Correntes RPPS'           ,'D',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (36,'4.0.0.0.00.00.00.00.00','DESPESAS DE CAPITAL'                      ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (37,'4.4.0.0.00.00.00.00.00','INVESTIMENTOS'                            ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (38,'4.4.0.0.00.00.00.00.00','Investimentos'                            ,'D',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (39,'4.4.0.0.00.00.00.00.00','Investimentos RPPS'                       ,'D',1, true );
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (40,'4.5.0.0.00.00.00.00.00','INVERSÕES FINANCEIRAS'                    ,'D',0, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (41,'4.5.9.0.66.00.00.00.00','Concessão de Empréstimos e Financiamentos','D',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (42,'4.5.9.0.99.00.00.00.00','Outras Inversões Financeiras'             ,'D',1, false);
INSERT INTO ldo.tipo_receita_despesa (cod_tipo, cod_estrutural, descricao, tipo, nivel, rpps) VALUES (43,'4.6.0.0.00.00.00.00.00','AMORTIZAÇÃO DA DÍVIDA PÚBLICA'            ,'D',1, false);


----------------------------------------------------------------
-- ADICIONANDO TABELAS ldo.tipo_divida E ldo.configuracao_divida
----------------------------------------------------------------

CREATE TABLE ldo.tipo_divida (
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(70)     NOT NULL,
    CONSTRAINT pk_tipo_divida       PRIMARY KEY (cod_tipo)
);
GRANT ALL ON ldo.tipo_divida TO GROUP urbem;

CREATE TABLE ldo.configuracao_divida (
    cod_ppa         INTEGER             NOT NULL,
    ano             CHAR(1)             NOT NULL,
    cod_tipo        INTEGER             NOT NULL,
    exercicio       CHAR(4)             NOT NULL,
    valor           NUMERIC(14,2)       NOT NULL,
    CONSTRAINT pk_configuracao_divida   PRIMARY KEY (cod_ppa, ano, cod_tipo, exercicio),
    CONSTRAINT fk_configuracao_divida_1 FOREIGN KEY         (cod_ppa, ano)
                                        REFERENCES ldo.ldo  (cod_ppa, ano),
    CONSTRAINT fk_configuracao_divida_2 FOREIGN KEY (cod_tipo)
                                        REFERENCES ldo.tipo_divida (cod_tipo)
);
GRANT ALL ON ldo.configuracao_divida TO GROUP urbem;


----------------
-- Ticket #15631
----------------

CREATE TABLE ldo.tipo_evolucao_patrimonio_liquido (
    cod_tipo        INTEGER         NOT NULL,
    rpps            BOOLEAN         NOT NULL,
    cod_estrutural  VARCHAR(30)     NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_evolucao_patrimonio_liquido  PRIMARY KEY (cod_tipo, rpps)
);

GRANT ALL ON ldo.tipo_evolucao_patrimonio_liquido TO GROUP urbem;


CREATE TABLE ldo.configuracao_evolucao_patrimonio_liquido(
    cod_ppa         INTEGER         NOT NULL,
    ano             CHAR(1)         NOT NULL,
    cod_tipo        INTEGER         NOT NULL,
    rpps            BOOLEAN         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    valor           NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_configuracao_evolucao_patrimonio_liquido   PRIMARY KEY                                        (cod_ppa, ano, cod_tipo, rpps, exercicio),
    CONSTRAINT fk_configucarao_evolucao_patrimonio_liquido_1 FOREIGN KEY                                        (cod_ppa, ano)
                                                             REFERENCES ldo.ldo                                 (cod_ppa, ano),
    CONSTRAINT fk_configucarao_evolucao_patrimonio_liquido_2 FOREIGN KEY                                        (cod_tipo, rpps)
                                                             REFERENCES ldo.tipo_evolucao_patrimonio_liquido    (cod_tipo, rpps)

);

GRANT ALL ON ldo.configuracao_evolucao_patrimonio_liquido TO GROUP urbem;

INSERT INTO ldo.tipo_evolucao_patrimonio_liquido VALUES (1, FALSE, '2.4.1.0.0.0.00.00.00.00.00','Patrimônio/Capital'            );
INSERT INTO ldo.tipo_evolucao_patrimonio_liquido VALUES (2, FALSE, '2.4.2.0.0.0.00.00.00.00.00','Reservas'                      );
INSERT INTO ldo.tipo_evolucao_patrimonio_liquido VALUES (3, FALSE, '2.4.3.0.0.0.00.00.00.00.00','Resultado Acumulado'           );
INSERT INTO ldo.tipo_evolucao_patrimonio_liquido VALUES (1, TRUE , '2.4.1.0.0.0.00.00.00.00.00','Patrimônio/Capital'            );
INSERT INTO ldo.tipo_evolucao_patrimonio_liquido VALUES (2, TRUE , '2.4.2.0.0.0.00.00.00.00.00','Reservas'                      );
INSERT INTO ldo.tipo_evolucao_patrimonio_liquido VALUES (3, TRUE , '2.4.3.0.0.0.00.00.00.00.00','Lucros ou Prejuízos Acumulados');


INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2750
          , 456
          , 'FLEvolucaoPatrimonioLiquido.php'
          , 'incluir'
          , 13
          , ''
          , 'Evolução Patrimônio Líquido'
          );


----------------
-- Ticket #15628
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2749
          , 456
          , 'FLEvolucaoDivida.php'
          , 'incluir'
          , 12
          , ''
          , 'Evolução da Dívida'
          );


INSERT INTO ldo.tipo_divida ( cod_tipo, descricao ) VALUES ( 1, 'Dívida Consolidada'                     );
INSERT INTO ldo.tipo_divida ( cod_tipo, descricao ) VALUES ( 2, 'Disponibilidades Financeiras (Líquidas)');
INSERT INTO ldo.tipo_divida ( cod_tipo, descricao ) VALUES ( 3, 'Dívida Consolidada Líquida'             );
INSERT INTO ldo.tipo_divida ( cod_tipo, descricao ) VALUES ( 4, 'Passivos Reconhecidos'                  );
INSERT INTO ldo.tipo_divida ( cod_tipo, descricao ) VALUES ( 5, 'Dívida Fiscal Líquida'                  );
INSERT INTO ldo.tipo_divida ( cod_tipo, descricao ) VALUES ( 6, 'Resultado Nominal'                      );


-------------------------------------
-- ADICIONANDO TABELA ldo.homologacao
-------------------------------------

CREATE TABLE ldo.homologacao (
    cod_ppa             INTEGER         NOT NULL,
    ano                 CHAR(1)         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE,
    cod_norma           INTEGER         NOT NULL,
    numcgm_veiculo      INTEGER         NOT NULL,
    cod_periodicidade   INTEGER         NOT NULL,
    dt_encaminhamento   DATE            NOT NULL,
    dt_devolucao        DATE            NOT NULL,
    nro_protocolo       CHAR(9)         NOT NULL,
    CONSTRAINT pk_homologacao           PRIMARY KEY                                 (cod_ppa, ano, timestamp),
    CONSTRAINT fk_homologacao_1         FOREIGN KEY                                 (cod_ppa, ano)
                                        REFERENCES ldo.ldo                          (cod_ppa, ano),
    CONSTRAINT fk_homologacao_2         FOREIGN KEY                                 (cod_norma)
                                        REFERENCES normas.norma                     (cod_norma),
    CONSTRAINT fk_homologacao_3         FOREIGN KEY                                 (numcgm_veiculo)
                                        REFERENCES licitacao.veiculos_publicidade   (numcgm),
    CONSTRAINT fk_homologacao_4         FOREIGN KEY                                 (cod_periodicidade)
                                        REFERENCES ppa.periodicidade                (cod_periodicidade)
);
GRANT ALL ON ldo.homologacao TO GROUP urbem;


----------------
-- Ticket #15726
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2487
          , 456
          , 'FMHomologarLDO.php'
          , 'incluir'
          , 14
          , ''
          , 'Homologar LDO'
          );


-----------------------------------------------
-- ADICIONANDO TABELA ldo.acao_validada_despesa
-----------------------------------------------

CREATE TABLE orcamento.despesa_acao (
    cod_acao                INTEGER     NOT NULL,
    exercicio_despesa       CHAR(4)     NOT NULL,
    cod_despesa             INTEGER     NOT NULL,
    CONSTRAINT pk_acao_validada_despesa     PRIMARY KEY                     (cod_acao, exercicio_despesa, cod_despesa),
    CONSTRAINT fk_acao_validada_despesa_1   FOREIGN KEY                     (cod_acao)
                                            REFERENCES ppa.acao             (cod_acao),
    CONSTRAINT fk_acao_validada_despesa_2   FOREIGN KEY                     (exercicio_despesa, cod_despesa)
                                            REFERENCES orcamento.despesa    (exercicio, cod_despesa)
);

GRANT ALL ON orcamento.despesa_acao TO GROUP urbem;


----------------
-- Ticket #15813
----------------

ALTER TABLE ldo.ldo ADD  COLUMN timestamp TIMESTAMP NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE;


----------------------------------------------------
-- ADICIONANDO COLUNA timestamp EM ldo.acao_validada
----------------------------------------------------

ALTER TABLE ldo.acao_validada ADD COLUMN timestamp TIMESTAMP DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE;


----------------
-- Ticket #15835
----------------

UPDATE administracao.acao
   SET cod_funcionalidade = 436
 WHERE cod_funcionalidade = 160
     ;

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     )
VALUES
     ( 465
     , 43
     , 'Destinação de Recursos'
     , '../orcamento/instancias/destinacaoRecursos/'
     , 11
     );
UPDATE administracao.acao
   SET cod_funcionalidade = 465
 WHERE cod_funcionalidade = 395
     ;

UPDATE administracao.funcionalidade
   SET ordem = ordem + 1
 WHERE cod_modulo = 43
   AND ordem > 2
     ;
UPDATE administracao.funcionalidade
   SET nom_funcionalidade = 'Órgão Orçamentário'
 WHERE cod_funcionalidade = 454
     ;
INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio, ordem
     )
VALUES
     ( 466
     , 43
     , 'Unidade Orçamentária'
     , '../orcamento/instancias/classInstitucional/'
     , 3
     );

UPDATE administracao.acao
   SET cod_funcionalidade = 454
 WHERE cod_acao IN (562,563)
     ;
UPDATE administracao.acao
   SET cod_funcionalidade = 466
 WHERE cod_acao IN (564,565,566)
     ;

UPDATE administracao.acao
   SET cod_funcionalidade = 450
 WHERE cod_funcionalidade = 162
   AND cod_acao <> 2390
     ;

DELETE
  FROM administracao.auditoria
 where cod_acao in (573,574,575,576,577,578)
     ;
DELETE
  FROM administracao.permissao
 where cod_acao in (573,574,575,576,577,578)
     ;
DELETE
  FROM administracao.acao
 where cod_acao in (573,574,575,576,577,578)
     ;

UPDATE administracao.funcionalidade
   set nom_funcionalidade = 'Contas Contábeis'
 WHERE cod_funcionalidade = 162
     ;

----------------------------------------------------------------------
--ALTERAÇÃO PARA FUNCIONAMENTO COM QUANTIDADE VINCULADO POR RECURSO --
----------------------------------------------------------------------
CREATE OR REPLACE FUNCTION ppa.fn_verifica_exercicio_ppa(VARCHAR) RETURNS INTEGER AS $$
DECLARE
    stExercicio                ALIAS FOR $1;

    stProximoExercicio         INTEGER;
    stExercicioExiste          INTEGER := 0;
BEGIN

    SELECT to_number(stExercicio, '9999') INTO stProximoExercicio;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.funcao
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.subfuncao
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.recurso(CAST(stProximoExercicio AS VARCHAR))
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.orgao
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
         LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    IF (stExercicioExiste < stProximoExercicio) THEN
        SELECT exercicio
          INTO stExercicioExiste
          FROM orcamento.unidade
         WHERE exercicio = CAST(stProximoExercicio AS VARCHAR)
        LIMIT 1;
    END IF;
    
    IF (stExercicioExiste IS NULL) THEN
        stExercicioExiste := 0;
    END IF;
    
    RETURN stExercicioExiste;

END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE FUNCTION ppa.fn_gerar_dados_ppa(VARCHAR, VARCHAR) RETURNS VARCHAR AS $$
DECLARE
    stExercicioInicio           ALIAS FOR $1;
    stExercicioReplicar         ALIAS FOR $2;

    stProximoExercicio          VARCHAR;
    inExercicioExiste           INTEGER := 0;
    inCount                     INTEGER := 0;
    bolRetorno                  BOOLEAN := true;
    bolRecursoDestinacao        BOOLEAN := false;

    recRegistro                 RECORD;
BEGIN

    FOR inCount IN 0..3 LOOP
        stProximoExercicio := BTRIM(TO_CHAR(TO_NUMBER(stExercicioInicio,'9999') + inCount, '9999'));
        inExercicioExiste  := ppa.fn_verifica_exercicio_ppa(stProximoExercicio);

        IF (inExercicioExiste > 0) THEN
           bolRetorno := false;
        ELSE
            -- TABELA ADMINISTRACAO_CONFIGURACAO
            --
            FOR recRegistro IN SELECT * FROM administracao.configuracao WHERE cod_modulo IN (8,9,10) AND exercicio = stExercicioReplicar
            LOOP
               INSERT INTO administracao.configuracao (cod_modulo, parametro, valor, exercicio) VALUES ( recRegistro.cod_modulo, recRegistro.parametro, recRegistro.valor, stProximoExercicio);
            END LOOP;

            --
            -- TABELA ORCAMENTO.FUNCAO
            --
            FOR recRegistro IN SELECT * FROM orcamento.funcao WHERE exercicio = stExercicioReplicar
            LOOP
               INSERT INTO orcamento.funcao (exercicio, cod_funcao, descricao) VALUES (stProximoExercicio, recRegistro.cod_funcao ,recRegistro.descricao);
            END LOOP;

            --
            -- TABELA ORCAMENTO.SUBFUNCAO
            --
            FOR recRegistro IN SELECT * FROM orcamento.subfuncao WHERE  exercicio = stExercicioReplicar
            LOOP
               INSERT INTO orcamento.subfuncao (exercicio, cod_subfuncao, descricao) VALUES (stProximoExercicio, recRegistro.cod_subfuncao ,recRegistro.descricao);
            END LOOP;

            --
            -- TABELA ORCAMENTO.RECURSO
            --
            FOR recRegistro IN SELECT * FROM orcamento.recurso WHERE exercicio = stExercicioReplicar
            LOOP
               INSERT INTO orcamento.recurso (exercicio, cod_recurso, nom_recurso, cod_fonte) VALUES (stProximoExercicio, recRegistro.cod_recurso ,recRegistro.nom_recurso, recRegistro.cod_fonte);
            END LOOP;
            
            SELECT BTRIM(valor)::VARCHAR  INTO bolRecursoDestinacao
              FROM administracao.configuracao
             WHERE configuracao.exercicio  = stExercicioReplicar
               AND configuracao.cod_modulo = 8
               AND configuracao.parametro  = 'recurso_destinacao';

            IF bolRecursoDestinacao THEN
                --
                -- TABELA ORCAMENTO.DETALHAMENTO_DESTINACAO_RECURSO
                --
                FOR recRegistro IN SELECT * FROM orcamento.detalhamento_destinacao_recurso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.detalhamento_destinacao_recurso (exercicio, cod_detalhamento, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_detalhamento, recRegistro.descricao);
                END LOOP;
                
                --
                -- TABELA ORCAMENTO.DESTINACAO_RECURSO
                --
                FOR recRegistro IN SELECT * FROM orcamento.destinacao_recurso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.destinacao_recurso (exercicio, cod_destinacao, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_destinacao, recRegistro.descricao);
                END LOOP;
                  
                --
                -- TABELA ORCAMENTO.IDENTIFICADOR_USO
                --
                FOR recRegistro IN SELECT * FROM orcamento.identificador_uso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.identificador_uso (exercicio, cod_uso, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_uso, recRegistro.descricao);
                END LOOP;
                  
                --
                -- TABELA ORCAMENTO.ESPECIFICACAO_DESTINACAO_RECURSO
                --
                FOR recRegistro IN SELECT * FROM orcamento.especificacao_destinacao_recurso WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.especificacao_destinacao_recurso (exercicio, cod_especificacao, cod_fonte, descricao)
                   VALUES (stProximoExercicio, recRegistro.cod_especificacao, recRegistro.cod_fonte, recRegistro.descricao);
                END LOOP;
                
                --
                -- TABELA ORCAMENTO.RECURSO_DESTINACAO
                --
                FOR recRegistro IN SELECT * FROM orcamento.recurso_destinacao WHERE exercicio = stExercicioReplicar
                LOOP
                   INSERT INTO orcamento.recurso_destinacao (exercicio, cod_recurso, cod_uso, cod_destinacao, cod_especificacao, cod_detalhamento)
                   VALUES (stProximoExercicio, recRegistro.cod_recurso, recRegistro.cod_uso, recRegistro.cod_destinacao, recRegistro.cod_especificacao, recRegistro.cod_detalhamento);
                END LOOP;

            ELSE
                --
                -- TABELA ORCAMENTO.RECURSO_DIRETO
                --
                FOR recRegistro IN SELECT * FROM orcamento.recurso_direto WHERE exercicio = stExercicioReplicar
                LOOP
                    INSERT INTO orcamento.recurso_direto (exercicio, cod_recurso, cod_fonte, nom_recurso, finalidade, tipo, codigo_tc)
                    VALUES (stProximoExercicio, recRegistro.cod_recurso, recRegistro.cod_fonte, recRegistro.nom_recurso, recRegistro.finalidade, recRegistro.tipo, recRegistro.codigo_tc);
                END LOOP;
              
            END IF;
            
            --
            -- TABELA ORCAMENTO.ORGAO
            --
            FOR recRegistro IN SELECT * FROM orcamento.orgao WHERE exercicio = stExercicioReplicar
            LOOP
                 INSERT INTO orcamento.orgao (exercicio, num_orgao, nom_orgao, usuario_responsavel) VALUES (stProximoExercicio, recRegistro.num_orgao ,recRegistro.nom_orgao, recRegistro.usuario_responsavel);
            END LOOP;

            --
            -- TABELA ORCAMENTO.UNIDADE
            --
            FOR recRegistro IN SELECT * FROM orcamento.unidade WHERE exercicio = stExercicioReplicar
            LOOP
                INSERT INTO orcamento.unidade (exercicio, num_unidade, num_orgao, nom_unidade, usuario_responsavel) VALUES (stProximoExercicio, recRegistro.num_unidade, recRegistro.num_orgao ,recRegistro.nom_unidade, recRegistro.usuario_responsavel);
            END LOOP;

            bolRetorno := true;
        END IF;
    END LOOP;

RETURN bolRetorno;

END;
$$ LANGUAGE 'plpgsql';

CREATE OR REPLACE function ppa.fn_ajustar_acao_quantidade() RETURNS BOOLEAN AS $$
DECLARE
    stSql       varchar := '';
    reRegistro  RECORD;
    stExercicio varchar := '';
BEGIN
    stSql := 'select 
                 cod_acao
                ,timestamp_acao_dados
                ,cod_recurso
                ,exercicio_recurso
                ,ano
              from
                ppa.acao_recurso';
                
    FOR reRegistro IN EXECUTE stSql
    LOOP
        stExercicio := (reRegistro.exercicio_recurso::integer + reRegistro.ano::integer)::varchar;

        UPDATE ppa.acao_recurso SET exercicio_recurso = stExercicio WHERE cod_acao = reRegistro.cod_acao AND timestamp_acao_dados = reRegistro.timestamp_acao_dados AND ano = reRegistro.ano and cod_recurso = reRegistro.cod_recurso;

        UPDATE ppa.acao_quantidade SET cod_recurso = reRegistro.cod_recurso, exercicio_recurso = stExercicio  WHERE cod_acao = reRegistro.cod_acao AND timestamp_acao_dados = reRegistro.timestamp_acao_dados AND ano = reRegistro.ano;

        RAISE notice 'Ano -> % e stExercicio % e reRegistro %', reRegistro.ano, stExercicio, reRegistro.exercicio_recurso;
    END LOOP;
    
    RETURN true;
END;

$$ LANGUAGE 'plpgsql';

alter table ldo.acao_validada drop constraint pk_acao_validada;
alter table ldo.acao_validada drop constraint fk_acao_validada_1;

alter table ppa.acao_quantidade drop constraint pk_acao_quantidade;
alter table ppa.acao_quantidade drop constraint fk_acao_quantidade_1;

alter table ppa.acao_quantidade add column cod_recurso integer;
alter table ppa.acao_quantidade add column exercicio_recurso character(4);

select ppa.fn_gerar_dados_ppa('2010','2009');
select ppa.fn_ajustar_acao_quantidade();

alter table ppa.acao_quantidade alter column cod_recurso set not null;
alter table ppa.acao_quantidade alter column exercicio_recurso set not null;

alter table ppa.acao_quantidade add constraint pk_acao_quantidade PRIMARY KEY(cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano);
alter table ppa.acao_quantidade add constraint fk_acao_quantidade_1 FOREIGN KEY (cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano) REFERENCES ppa.acao_recurso(cod_acao, timestamp_acao_dados, cod_recurso, exercicio_recurso, ano);

alter table ldo.acao_validada add column cod_recurso integer not null;
alter table ldo.acao_validada add column exercicio_recurso character(4) not null;

alter table ldo.acao_validada add constraint pk_acao_validada PRIMARY KEY (cod_acao, ano, timestamp_acao_dados, cod_recurso, exercicio_recurso);
alter table ldo.acao_validada add constraint fk_acao_validada_1 FOREIGN KEY (cod_acao, ano, timestamp_acao_dados, cod_recurso, exercicio_recurso) REFERENCES ppa.acao_quantidade(cod_acao, ano, timestamp_acao_dados, cod_recurso, exercicio_recurso);

DROP FUNCTION ppa.fn_ajustar_acao_quantidade();


--------------------------------------------
-- SOLICITADO POR Eduardo Schitz EM 20090901
--------------------------------------------

UPDATE ppa.acao_quantidade SET valor = (valor * quantidade);

