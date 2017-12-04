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

-------------------------------------------
-- CRIACAO DA BASE DE DADOS PARA MODULO LDO
-------------------------------------------

-- GF 1.95.5   CRIACAO DO SCHEMA ldo
-- GF 1.95.5  CREATE SCHEMA ldo;
-- GF 1.95.5  GRANT ALL ON SCHEMA ldo TO GROUP urbem;

-- GF 1.95.5   CRIACAO DAS TABELAS
-- GF 1.95.5  CREATE TABLE ldo.tipo_indicadores (
-- GF 1.95.5      cod_tipo_indicador  INTEGER         NOT NULL,
-- GF 1.95.5      descricao           VARCHAR(100)    NOT NULL,
-- GF 1.95.5      CONSTRAINT pk_tipo_indicadores       PRIMARY KEY (cod_tipo_indicador)
-- GF 1.95.5  );
-- GF 1.95.5  GRANT ALL ON ldo.tipo_indicadores TO GROUP urbem;

-- GF 1.95.5  INSERT INTO ldo.tipo_indicadores (cod_tipo_indicador, descricao) VALUES (1,'PIB'     );
-- GF 1.95.5  INSERT INTO ldo.tipo_indicadores (cod_tipo_indicador, descricao) VALUES (2,'Inflação');
-- GF 1.95.5  INSERT INTO ldo.tipo_indicadores (cod_tipo_indicador, descricao) VALUES (3,'Deflação');


-- GF 1.95.5  CREATE TABLE ldo.ldo (
-- GF 1.95.5      cod_ppa             INTEGER         NOT NULL,
-- GF 1.95.5      ano                 CHAR(1)         NOT NULL,
-- GF 1.95.5      CONSTRAINT pk_ldo                   PRIMARY KEY         (cod_ppa, ano),
-- GF 1.95.5      CONSTRAINT fk_ldo_1                 FOREIGN KEY         (cod_ppa)
-- GF 1.95.5                                          REFERENCES ppa.ppa  (cod_ppa)
-- GF 1.95.5  );
-- GF 1.95.5  GRANT ALL ON ldo.ldo TO GROUP urbem;

-- GF 1.95.5  CREATE TABLE ldo.indicadores (
-- GF 1.95.5      ano                 CHAR(4)         NOT NULL,
-- GF 1.95.5      indice              NUMERIC(4,1)    NOT NULL,
-- GF 1.95.5      cod_tipo_indicador  INTEGER         NOT NULL,
-- GF 1.95.5      CONSTRAINT pk_indicadores           PRIMARY KEY                     (ano),
-- GF 1.95.5      CONSTRAINT fk_indicadores_1         FOREIGN KEY                     (cod_tipo_indicador)
-- GF 1.95.5                                          REFERENCES ldo.tipo_indicadores (cod_tipo_indicador),
-- GF 1.95.5      CONSTRAINT fk_indicadores_2         FOREIGN KEY                     (ano)
-- GF 1.95.5                                          REFERENCES ldo.ldo              (ano)
-- GF 1.95.5  );
-- GF 1.95.5  GRANT ALL ON ldo.indicadores TO GROUP urbem;

-- GF 1.95.8 (ALTERADA P/ ldo.homologacao) CREATE TABLE ldo.homologacao (
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     cod_ppa             INTEGER         NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     ano                 CHAR(1)         NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     timestamp           TIMESTAMP       NOT NULL DEFAULT (now::text)::timestamp(3) WITH TIME ZONE,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     cod_norma           INTEGER         NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     numcgm_veiculo      INTEGER         NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     cod_periodicidade   INTEGER         NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     dt_encaminhamento   DATE            NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     dt_devolucao        DATE            NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     nro_protocolo       CHAR(9)         NOT NULL,
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     CONSTRAINT pk_homologacao           PRIMARY KEY                                 (cod_ppa, ano, timestamp)
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     CONSTRAINT fk_homologacao_1         FOREIGN KEY                                 (cod_ppa, ano)
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)                                         REFERENCES ldo.ldo                          (cod_ppa, ano),
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     CONSTRAINT fk_homologacao_2         FOREIGN KEY                                 (cod_norma)
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)                                         REFERENCES normas.norma                     (cod_norma),
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     CONSTRAINT fk_homologacao_3         FOREIGN KEY                                 (numcgm_veiculo)
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)                                         REFERENCES licitacao.veiculos_publicidade   (numcgm),
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)     CONSTRAINT fk_homologacao_4         FOREIGN KEY                                 (cod_periodicidade)
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao)                                         REFERENCES ppa.periodicidade                (cod_periodicidade)
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao) );
-- GF 1.95.8 (ALTERADA P/ ldo.homologacao) GRANT ALL ON ldo.homologacao_ldo TO GROUP urbem;






CREATE TABLE ldo.acao (
    cod_acao            INTEGER         NOT NULL,
    ano                 CHAR(4)         NOT NULL,
    cod_acao_ppa        INTEGER         NOT NULL,
    ativo               BOOLEAN         NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_acao                  PRIMARY KEY         (cod_acao),
    CONSTRAINT fk_acao_1                FOREIGN KEY         (ano)
                                        REFERENCES ldo.ldo  (ano),
    CONSTRAINT fk_acao_2                FOREIGN KEY         (cod_acao_ppa)
                                        REFERENCES ppa.acao (cod_acao)
);
GRANT ALL ON ldo.acao TO GROUP urbem;

CREATE TABLE ldo.acao_dados (
    cod_acao            INTEGER         NOT NULL,
    cod_acao_dados      INTEGER         NOT NULL,
    num_orgao           INTEGER         NOT NULL,
    num_unidade         INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    cod_norma           INTEGER                 ,
    CONSTRAINT pk_acao_dados            PRIMARY KEY                         (cod_acao, cod_acao_dados),
    CONSTRAINT fk_acao_dados_1          FOREIGN KEY                         (cod_acao)
                                        REFERENCES ldo.acao                 (cod_acao),
    CONSTRAINT fk_acao_dados_2          FOREIGN KEY                         (exercicio, num_unidade, num_orgao)
                                        REFERENCES orcamento.unidade        (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_acao_dados_3          FOREIGN KEY                         (cod_entidade, exercicio)
                                        REFERENCES orcamento.entidade       (cod_entidade, exercicio),
    CONSTRAINT fk_acao_dados_4          FOREIGN KEY                         (cod_norma)
                                        REFERENCES normas.norma             (cod_norma)
);
GRANT ALL ON ldo.acao_dados TO GROUP urbem;

CREATE TABLE ldo.acao_inativa_norma (
    cod_acao            INTEGER         NOT NULL,
    cod_norma           INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_acao_inativa_norma    PRIMARY KEY                 (cod_acao),
    CONSTRAINT fk_acao_inativa_norma_1  FOREIGN KEY                 (cod_acao)
                                        REFERENCES ldo.acao         (cod_acao),
    CONSTRAINT fk_acao_inativa_norma_2  FOREIGN KEY                 (cod_norma)
                                        REFERENCES normas.norma     (cod_norma)
);
GRANT ALL ON ldo.acao_inativa_norma TO GROUP urbem;

CREATE TABLE ldo.acao_recurso (
    cod_acao            INTEGER         NOT NULL,
    cod_acao_dados      INTEGER         NOT NULL,
    cod_recurso         INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_conta           INTEGER         NOT NULL,
    valor               NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_acao_recurso          PRIMARY KEY                         (cod_acao, cod_acao_dados, cod_recurso, exercicio),
    CONSTRAINT fk_acao_recurso_1        FOREIGN KEY                         (cod_acao_dados, cod_acao)
                                        REFERENCES ldo.acao_dados           (cod_acao_dados, cod_acao),
    CONSTRAINT fk_acao_recurso_2        FOREIGN KEY                         (exercicio, cod_recurso)
                                        REFERENCES orcamento.recurso        (exercicio, cod_recurso),
    CONSTRAINT fk_acao_recurso_3        FOREIGN KEY                         (exercicio, cod_conta)
                                        REFERENCES orcamento.conta_despesa  (exercicio, cod_conta)
);
GRANT ALL ON ldo.acao_recurso TO GROUP urbem;

CREATE TABLE ldo.receita (
    cod_receita         INTEGER         NOT NULL,
    ano                 CHAR(4)         NOT NULL,
    cod_receita_ppa     INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    cod_conta           INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_ppa             INTEGER         NOT NULL,
    valor_total         NUMERIC(14,2)   NOT NULL,
    ativo               BOOLEAN         NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_receita               PRIMARY KEY                 (cod_receita),
    CONSTRAINT fk_receita_1             FOREIGN KEY                 (ano)
                                        REFERENCES ldo.ldo          (ano),
    CONSTRAINT fk_receita_2             FOREIGN KEY                 (cod_receita_ppa, cod_ppa, exercicio, cod_conta, cod_entidade)
                                        REFERENCES ppa.ppa_receita  (cod_receita, cod_ppa, exercicio, cod_conta, cod_entidade)
);
GRANT ALL ON ldo.receita TO GROUP urbem;

CREATE TABLE ldo.receita_dados (
    cod_receita         INTEGER         NOT NULL,
    cod_receita_dados   INTEGER         NOT NULL,
    cod_norma           INTEGER                 ,
    CONSTRAINT pk_receita_dados         PRIMARY KEY                 (cod_receita, cod_receita_dados),
    CONSTRAINT fk_receita_dados_1       FOREIGN KEY                 (cod_receita)
                                        REFERENCES ldo.receita      (cod_receita),
    CONSTRAINT fk_receita_dados_2       FOREIGN KEY                 (cod_norma)
                                        REFERENCES normas.norma     (cod_norma)
);
GRANT ALL ON ldo.receita_dados TO GROUP urbem;

CREATE TABLE ldo.receita_inativa_norma (
    cod_receita         INTEGER         NOT NULL,
    cod_norma           INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_receita_inativa_norma     PRIMARY KEY                 (cod_receita),
    CONSTRAINT fk_receita_inativa_norma_1   FOREIGN KEY                 (cod_receita)
                                            REFERENCES ldo.receita      (cod_receita),
    CONSTRAINT fk_receita_inativa_norma_2   FOREIGN KEY                 (cod_norma)
                                            REFERENCES normas.norma     (cod_norma)
);
GRANT ALL ON ldo.receita_inativa_norma TO GROUP urbem;

CREATE TABLE ldo.receita_recurso (
    cod_receita         INTEGER         NOT NULL,
    cod_receita_dados   INTEGER         NOT NULL,
    cod_recurso         INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    valor               NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_receita_recurso       PRIMARY KEY                         (cod_receita, cod_receita_dados, cod_recurso, exercicio),
    CONSTRAINT fk_receita_recurso_1     FOREIGN KEY                         (cod_receita, cod_receita_dados)
                                        REFERENCES ldo.receita_dados        (cod_receita, cod_receita_dados),
    CONSTRAINT fk_receita_recurso_2     FOREIGN KEY                         (exercicio, cod_recurso)
                                        REFERENCES orcamento.recurso        (exercicio, cod_recurso)
);
GRANT ALL ON ldo.receita_recurso TO GROUP urbem;

--- 
--- ======================================
---

CREATE TABLE ldo.anexo (
  cod_anexo INTEGER   NOT NULL ,
  cod_acao INTEGER      ,
  CONSTRAINT pk_anexo    PRIMARY KEY                     (cod_anexo)
);

GRANT ALL ON ldo.anexo TO GROUP urbem;


CREATE TABLE ldo.nota_explicativa (
  cod_nota_explicativa  INTEGER     NOT NULL,
  cod_anexo             INTEGER     NOT NULL,
  ano                   CHAR(4)     NOT NULL,
  descricao             TEXT                ,
  numcgm                INTEGER             ,
  CONSTRAINT pk_nota_explicativa    PRIMARY KEY                     (cod_nota_explicativa),
  CONSTRAINT fk_risco_fiscal_1      FOREIGN KEY                     (cod_anexo)
                                    REFERENCES ldo.anexo            (cod_anexo),
  CONSTRAINT fk_risco_fiscal_2      FOREIGN KEY                     (numcgm)
                                    REFERENCES sw_cgm               (numcgm),
  CONSTRAINT fk_risco_fiscal_3      FOREIGN KEY                     (ano)
                                    REFERENCES ldo.ldo              (ano)
);

GRANT ALL ON ldo.nota_explicativa TO GROUP urbem;


CREATE TABLE ldo.risco_fiscal (
  cod_risco_fiscal  INTEGER         NOT NULL,
  ano               CHAR(4)         NOT NULL,
  descricao         VARCHAR(100)            ,
  valor             NUMERIC(14,2)           ,
  CONSTRAINT pk_risco_fiscal        PRIMARY KEY        (cod_risco_fiscal),
  CONSTRAINT fk_risco_fiscal_1      FOREIGN KEY        (ano)
                                    REFERENCES ldo.ldo (ano)
);

GRANT ALL ON ldo.risco_fiscal TO GROUP urbem;

CREATE INDEX idx_ldo_risco_fiscal_2 ON ldo.risco_fiscal (ano);


CREATE TABLE ldo.providencia_fiscal (
  cod_providencia_fiscal INTEGER   NOT NULL ,
  descricao VARCHAR(250)    ,
  valor NUMERIC(14,2)    ,
  cod_risco_fiscal INTEGER      ,

  CONSTRAINT pk_providencia_fiscal      PRIMARY KEY                     (cod_providencia_fiscal),

  CONSTRAINT fk_providencia_fiscal_1    FOREIGN KEY                     (cod_risco_fiscal)
                                        REFERENCES ldo.risco_fiscal     (cod_risco_fiscal)
);

GRANT ALL ON ldo.providencia_fiscal TO GROUP urbem;

CREATE INDEX idx_ldo_providencia_fiscal_1 ON ldo.providencia_fiscal (cod_risco_fiscal);


-- GF 1.95.5  CREATE TABLE ldo.compensacao_renuncia (
-- GF 1.95.5    cod_compensacao     INTEGER             NOT NULL,
-- GF 1.95.5    ano                 CHAR(4)             NOT NULL,  
-- GF 1.95.5    tributo             VARCHAR(250)        NOT NULL,
-- GF 1.95.5    modalidade          VARCHAR(250)        NOT NULL,
-- GF 1.95.5    setores_programas   VARCHAR(250)        NOT NULL,
-- GF 1.95.5    valor_ano_ldo       NUMERIC(14,2)               ,
-- GF 1.95.5    valor_ano_ldo_1     NUMERIC(14,2)               ,
-- GF 1.95.5    valor_ano_ldo_2     NUMERIC(14,2)               ,
-- GF 1.95.5    compensacao         VARCHAR(250)        NOT NULL,
-- GF 1.95.5    CONSTRAINT pk_compensacao_renuncia      PRIMARY KEY        (cod_compensacao, ano),
-- GF 1.95.5    CONSTRAINT fk_compensacao_renuncia_1    FOREIGN KEY        (ano)
-- GF 1.95.5                                            REFERENCES ldo.ldo (ano)
-- GF 1.95.5  );
-- GF 1.95.5  
-- GF 1.95.5  GRANT ALL ON ldo.compensacao_renuncia TO GROUP urbem;

CREATE INDEX idx_ldo_compensacao_renuncia_1 ON ldo.compensacao_renuncia (ano);

CREATE TABLE ldo.despesa_continua (
  cod_despesa                   INTEGER         NOT NULL,
  ano                           CHAR(4)         NOT NULL,
  aumento_permanente            NUMERIC(14,2)   NOT NULL,
  transferencia_constitucional  NUMERIC(14,2)           ,
  transferencia_fundeb          NUMERIC(14,2)   NOT NULL,
  reducao_permanente            NUMERIC(14,2)   NOT NULL,
  saldo_utilizado_margem_bruta  NUMERIC(14,2)   NOT NULL,
  docc                          NUMERIC(14,2)           ,
  docc_ppp                      NUMERIC(14,2)           ,
  CONSTRAINT pk_despesa_continua                PRIMARY KEY                     (cod_despesa, ano),
  CONSTRAINT fk_despesa_continua_1              FOREIGN KEY                     (ano)
                                                REFERENCES ldo.ldo              (ano)  
);

GRANT ALL ON ldo.despesa_continua TO GROUP urbem;

CREATE INDEX idx_ldo_despesa_continua_2 ON ldo.despesa_continua (ano);



-------
-- Menu
-------

DELETE
  FROM administracao.permissao
 WHERE cod_acao IN ( SELECT cod_acao
                       FROM administracao.acao
                      WHERE cod_funcionalidade = 374
                   );

DELETE
  FROM administracao.auditoria
 WHERE cod_acao IN ( SELECT cod_acao
                       FROM administracao.acao
                      WHERE cod_funcionalidade = 374
                   );

DELETE
  FROM administracao.acao
 WHERE cod_funcionalidade IN (  SELECT cod_funcionalidade
                                  FROM administracao.funcionalidade
                                 WHERE cod_modulo = 44
                             );

DELETE
  FROM administracao.funcionalidade
 WHERE cod_modulo = 44;

-- GF 1.95.1  INSERT
-- GF 1.95.1    INTO administracao.funcionalidade
-- GF 1.95.1  VALUES ('456'
-- GF 1.95.1       , '44'
-- GF 1.95.1       , 'Configuração'
-- GF 1.95.1       , 'instancias/configuracao/'
-- GF 1.95.1       , '1');

INSERT
  INTO administracao.funcionalidade
VALUES ('457'
     , '44'
     , 'Ação'
     , 'instancias/acao/'
     , '3');

INSERT
  INTO administracao.funcionalidade
VALUES ('458'
     , '44'
     , 'Receita'
     , 'instancias/receita/'
     , '2');

INSERT
  INTO administracao.funcionalidade
VALUES ('459'
     , '44'
     , 'Nota Explicativa'
     , 'instancias/notaExplicativa/'
     , '4');

INSERT
  INTO administracao.funcionalidade
VALUES ('460'
     , '44'
     , 'Riscos Fiscais'
     , 'instancias/riscoFiscal/'
     , '5');

-- GF 1.95.5  INSERT
-- GF 1.95.5    INTO administracao.funcionalidade
-- GF 1.95.5  VALUES ('461'
-- GF 1.95.5       , '44'
-- GF 1.95.5       , 'Relatórios'
-- GF 1.95.5       , 'instancias/relatorios/'
-- GF 1.95.5       , '20');

-- GF 1.95.5 (MOVIDO P/ Configuracao)  INSERT
-- GF 1.95.5 (MOVIDO P/ Configuracao)    INTO administracao.funcionalidade
-- GF 1.95.5 (MOVIDO P/ Configuracao)  VALUES ('462'
-- GF 1.95.5 (MOVIDO P/ Configuracao)       , '44'
-- GF 1.95.5 (MOVIDO P/ Configuracao)       , 'Renúncia de Receita'
-- GF 1.95.5 (MOVIDO P/ Configuracao)       , 'instancias/renunciaReceita/'
-- GF 1.95.5 (MOVIDO P/ Configuracao)       , '6');

INSERT
  INTO administracao.funcionalidade
VALUES ('463'
     , '44'
     , 'Despesa Contínua'
     , 'instancias/despesaContinua/'
     , '7');

-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')  INSERT
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')    INTO administracao.acao
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')  VALUES ('2486'
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')       , '456'
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')       , 'FMManterConfiguracao.php'
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')       , 'incluir'
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')       , '1'
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')       , ''
-- GF 1.95.5 (ALTERADO P/ 'Configurar Indicadores')       , 'Configurar LDO');

-- GF 1.95.8 INSERT
-- GF 1.95.8   INTO administracao.acao
-- GF 1.95.8  VALUES ('2487'
-- GF 1.95.8      , '456'
-- GF 1.95.8      , 'FMHomologarLDO.php'
-- GF 1.95.8      , 'homologar'
-- GF 1.95.8      , '2'
-- GF 1.95.8      , ''
-- GF 1.95.8      , 'Homologar LDO');

INSERT
  INTO administracao.acao
VALUES ('2488'
     , '457'
     , 'FMManterAcao.php'
     , 'incluir'
     , '1'
     , ''
     , 'Incluir Ação');

INSERT
  INTO administracao.acao
VALUES ('2489'
     , '457'
     , 'FLManterAcao.php'
     , 'alterar'
     , '2'
     , ''
     , 'Alterar Ação');

INSERT
  INTO administracao.acao
VALUES ('2490'
     , '457'
     , 'FLManterAcao.php'
     , 'excluir'
     , '3'
     , ''
     , 'Excluir Ação');

INSERT
  INTO administracao.acao
VALUES ('2491'
     , '458'
     , 'FMManterReceita.php'
     , 'incluir'
     , '1'
     , ''
     , 'Incluir Receita');

INSERT
  INTO administracao.acao
VALUES ('2492'
     , '458'
     , 'FLManterReceita.php'
     , 'alterar'
     , '2'
     , ''
     , 'Alterar Receita');

INSERT
  INTO administracao.acao
VALUES ('2493'
     , '458'
     , 'FLManterReceita.php'
     , 'excluir'
     , '3'
     , ''
     , 'Excluir Receita');

INSERT
  INTO administracao.acao
VALUES ('2494'
     , '459'
     , 'FMManterNotaExplicativa.php'
     , 'incluir'
     , '1'
     , ''
     , 'Incluir Nota Explicativa');

INSERT
  INTO administracao.acao
VALUES ('2495'
     , '459'
     , 'FLManterNotaExplicativa.php'
     , 'alterar'
     , '2'
     , ''
     , 'Alterar Nota Explicativa');

INSERT
  INTO administracao.acao
VALUES ('2496'
     , '459'
     , 'FLManterNotaExplicativa.php'
     , 'excluir'
     , '3'
     , ''
     , 'Excluir Nota Explicativa');

INSERT
  INTO administracao.acao
VALUES ('2497'
     , '460'
     , 'FMManterRiscoFiscal.php'
     , 'incluir'
     , '1'
     , ''
     , 'Incluir Risco Fiscal');

INSERT
  INTO administracao.acao
VALUES ('2498'
     , '460'
     , 'FLManterRiscoFiscal.php'
     , 'alterar'
     , '2'
     , ''
     , 'Alterar Risco Fiscal');

INSERT
  INTO administracao.acao
VALUES ('2499'
     , '460'
     , 'FLManterRiscoFiscal.php'
     , 'excluir'
     , '3'
     , ''
     , 'Excluir Risco Fiscal');

INSERT
  INTO administracao.acao
VALUES ('2500'
     , '461'
     , 'FLRelatorioMetasAnuais.php'
     , 'emitir'
     , '1'
     , ''
     , 'Metas Anuais');

INSERT
  INTO administracao.acao
VALUES ('2501'
     , '461'
     , 'FLRelatorioMetasComparadas.php'
     , 'emitir'
     , '2'
     , ''
     , 'Metas Comparadas');

INSERT
  INTO administracao.acao
VALUES ('2502'
     , '461'
     , 'FLRelatorioMetasAnterior.php'
     , 'emitir'
     , '3'
     , ''
     , 'Metas Anterior');

INSERT
  INTO administracao.acao
VALUES ('2503'
     , '461'
     , 'FLDemonstrativoRiscosFiscais.php'
     , 'emitir'
     , '4'
     , ''
     , 'Demonstrativo Riscos Fiscais');

INSERT
  INTO administracao.acao
VALUES ('2504'
     , '461'
     , 'FLRelatorioRecursosObtidos.php'
     , 'emitir'
     , '5'
     , ''
     , 'Recursos Obtidos por Alienação');

INSERT
  INTO administracao.acao
VALUES ('2505'
     , '461'
     , 'FLRelatorioRenunciaReceita.php'
     , 'emitir'
     , '6'
     , ''
     , 'Estimativa e compensação da renúncia');

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES ( 2714
     , 461
     , 'FLRelatorioDespesaContinua.php'
     , 'emitir'
     , 7
     , ''
     , 'Margem de Expansão das Despesas Contínuas'
     );


-- GF 1.95.5  INSERT
-- GF 1.95.5    INTO administracao.acao
-- GF 1.95.5  VALUES ('2506'
-- GF 1.95.5       , '456'
-- GF 1.95.5       , 'FMManterRenunciaReceita.php'
-- GF 1.95.5       , 'incluir'
-- GF 1.95.5       , '3'
-- GF 1.95.5       , ''
-- GF 1.95.5       , 'Incluir Renúncia de Receita');
-- GF 1.95.5  
-- GF 1.95.5  INSERT
-- GF 1.95.5    INTO administracao.acao
-- GF 1.95.5  VALUES ('2607'
-- GF 1.95.5       , '456'
-- GF 1.95.5       , 'FLManterRenunciaReceita.php'
-- GF 1.95.5       , 'alterar'
-- GF 1.95.5       , '4'
-- GF 1.95.5       , ''
-- GF 1.95.5       , 'Alterar Renúncia de Receita');
-- GF 1.95.5  
-- GF 1.95.5  INSERT
-- GF 1.95.5    INTO administracao.acao
-- GF 1.95.5  VALUES ('2708'
-- GF 1.95.5       , '456'
-- GF 1.95.5       , 'FLManterRenunciaReceita.php'
-- GF 1.95.5       , 'excluir'
-- GF 1.95.5       , '5'
-- GF 1.95.5       , ''
-- GF 1.95.5       , 'Excluir Renúncia de Receita');

INSERT
  INTO administracao.acao
VALUES ('2509'
     , '456'
     , 'FMManterDespesaContinua.php'
     , 'incluir'
     , '6'
     , ''
     , 'Incluir Despesa Contínua');

INSERT
  INTO administracao.acao
 VALUES ('2510'
     , '456'
     , 'FLManterDespesaContinua.php'
     , 'alterar'
     , '7'
     , ''
     , 'Alterar Despesa Contínua');

INSERT
  INTO administracao.acao
VALUES ('2511'
     , '456'
     , 'FLManterDespesaContinua.php'
     , 'excluir'
     , '8'
     , ''
     , 'Excluir Despesa Contínua');



----------------
-- Ticket #14985
----------------

INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (1, 2500);
INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (2, 2501);
INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (3, 2502);
INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (4, 2503);
INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (5, 2504);
INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (6, 2505);

----------------
-- Ticket #15235
----------------

INSERT INTO ldo.anexo (cod_anexo, cod_acao) VALUES (7, 2714);

