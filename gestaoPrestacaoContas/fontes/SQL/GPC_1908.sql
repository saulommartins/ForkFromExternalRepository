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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Id: GPC_1908.sql 59612 2014-09-02 12:00:51Z gelson $
*
* VersÃ£o 1.90.8
*/

----------------
-- Ticket #13620
----------------

-- #12825

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2330
          , 364
          , 'FMManterContrato.php'
          , 'incluir'
          , 24
          , ''
          , 'Incluir Contrato'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2331
          , 364
          , 'FLManterContrato.php'
          , 'alterar'
          , 25
          , ''
          , 'Alterar Contrato'
          );


INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2332
          , 364
          , 'FLManterContrato.php'
          , 'excluir'
          , 26
          , ''
          , 'Excluir Contrato'
          );


CREATE TABLE tcmgo.contrato_assunto (
    cod_assunto         INTEGER                 NOT NULL,
    descricao           VARCHAR(255)            NOT NULL,
    CONSTRAINT pk_contrato_assunto              PRIMARY KEY (cod_assunto)
);

CREATE TABLE tcmgo.contrato_tipo (
    cod_tipo            INTEGER                 NOT NULL,
    descricao           VARCHAR(20)             NOT NULL,
    CONSTRAINT pk_contrato_tipo                 PRIMARY KEY (cod_tipo)
);

CREATE TABLE tcmgo.contrato_modalidade_licitacao (
    cod_modalidade      INTEGER                 NOT NULL,
    descricao           VARCHAR(255)            NOT NULL,
    CONSTRAINT pk_contrato_modalidade_licitacao PRIMARY KEY (cod_modalidade)

);

-- INSERTS PARA CONFIGURAÇÃO NA TABELA TCM-GO MODALIDADE LICITAÇÃO --
insert into tcmgo.contrato_modalidade_licitacao values (1,'Abaixo do limite de convite para compras e serviços');
insert into tcmgo.contrato_modalidade_licitacao values (2,'Abaixo do limite de convite para Obras e Serviços de Engenharia');
insert into tcmgo.contrato_modalidade_licitacao values (3,'Convite para Compras e Serviços');
insert into tcmgo.contrato_modalidade_licitacao values (4,'Convite para Obras e Serviços de Engenharia');
insert into tcmgo.contrato_modalidade_licitacao values (5,'Tomada de Preço para Compras e Serviços');
insert into tcmgo.contrato_modalidade_licitacao values (6,'Tomada de Preço para Obras e Serviços de Engenharia');
insert into tcmgo.contrato_modalidade_licitacao values (7,'Concorrência para Compras e Serviços');
insert into tcmgo.contrato_modalidade_licitacao values (8,'Concorrência para Obras e Serviços de Engenharia');
insert into tcmgo.contrato_modalidade_licitacao values (9,'Leilão');
insert into tcmgo.contrato_modalidade_licitacao values (10,'Dispensa de Licitação');
insert into tcmgo.contrato_modalidade_licitacao values (11,'Inexigibilidade de Licitação');
insert into tcmgo.contrato_modalidade_licitacao values (12,'Concurso');
insert into tcmgo.contrato_modalidade_licitacao values (13,'Pregão');
insert into tcmgo.contrato_modalidade_licitacao values (99,'Outros \(Convênios\, ajustes\, similares\, etc\)');

-- INSERTS PARA CONFIGURAÇÃO NA TABELA TCM-GO ASSUNTO --
insert into tcmgo.contrato_assunto              values (1,'Obras e Serviços de Engenharia');
insert into tcmgo.contrato_assunto              values (2,'Assessoria Jurídica');
insert into tcmgo.contrato_assunto              values (3,'Assessoria Contábil');
insert into tcmgo.contrato_assunto              values (4,'Credenciamento');
insert into tcmgo.contrato_assunto              values (5,'Publicidade');
insert into tcmgo.contrato_assunto              values (6,'Transporte Escolar');
insert into tcmgo.contrato_assunto              values (7,'Fornecimento Combustíveis');
insert into tcmgo.contrato_assunto              values (8,'Fornecimento Medicamentos');
insert into tcmgo.contrato_assunto              values (9,'Fornecimento Veículos');
insert into tcmgo.contrato_assunto              values (10,'Fornecimento Suprimentos de Informática');
insert into tcmgo.contrato_assunto              values (11,'Locação');
insert into tcmgo.contrato_assunto              values (12,'Shows Artísticos');
insert into tcmgo.contrato_assunto              values (99,'Outros');

-- INSERT PARA CONFIGURAÇÃO NA TABELA CONTRATO TIPO --
insert into tcmgo.contrato_tipo                 values (1,'Original');
insert into tcmgo.contrato_tipo                 values (2,'Aditivo'); 



CREATE SEQUENCE tcmgo.contrato_nro_sequencial_seq;

CREATE TABLE tcmgo.contrato (
    cod_contrato        INTEGER                 NOT NULL,
    exercicio           CHAR(4)                 NOT NULL,
    cod_entidade        INTEGER                 NOT NULL,
    nro_contrato        INTEGER                 NOT NULL,
    cod_assunto         INTEGER                 NOT NULL,
    cod_tipo            INTEGER                 NOT NULL,
    cod_modalidade      INTEGER                 NOT NULL,
    vl_contrato         NUMERIC(14,2)           NOT NULL,
    objeto_contrato     VARCHAR(200)            NOT NULL,
    data_inicio         DATE                    NOT NULL,
    data_final          DATE                    NOT NULL,
    data_publicacao     DATE                    NOT NULL,
    nro_sequencial      INTEGER                 NOT NULL DEFAULT nextval('tcmgo.contrato_nro_sequencial_seq'),
    CONSTRAINT pk_contrato                      PRIMARY KEY (cod_contrato, exercicio, cod_entidade),
    CONSTRAINT fk_contrato_1                    FOREIGN KEY (exercicio, cod_entidade)
                                                REFERENCES orcamento.entidade (exercicio, cod_entidade),
    CONSTRAINT fk_contrato_2                    FOREIGN KEY (cod_assunto)
                                                REFERENCES tcmgo.contrato_assunto (cod_assunto),
    CONSTRAINT fk_contrato_3                    FOREIGN KEY (cod_tipo)
                                                REFERENCES tcmgo.contrato_tipo (cod_tipo),
    CONSTRAINT fk_contrato_4                    FOREIGN KEY (cod_modalidade)
                                                REFERENCES tcmgo.contrato_modalidade_licitacao (cod_modalidade),
    CONSTRAINT uk_contrato                      UNIQUE (nro_contrato)
);

CREATE TABLE tcmgo.contrato_empenho (
    cod_contrato        INTEGER                 NOT NULL,
    exercicio           VARCHAR(4)              NOT NULL,
    exercicio_empenho   VARCHAR(4)              NOT NULL,
    cod_entidade        INTEGER                 NOT NULL,
    cod_empenho         INTEGER                 NOT NULL,
    CONSTRAINT pk_contrato_empenho              PRIMARY KEY (cod_contrato, exercicio, exercicio_empenho, cod_entidade, cod_empenho),
    CONSTRAINT fk_contrato_empenho_1            FOREIGN KEY (cod_contrato, exercicio, cod_entidade)
                                                REFERENCES tcmgo.contrato (cod_contrato, exercicio, cod_entidade),
    CONSTRAINT fk_contrato_empenho_2            FOREIGN KEY (exercicio_empenho, cod_entidade, cod_empenho)
                                                REFERENCES empenho.empenho (exercicio, cod_entidade, cod_empenho),
    CONSTRAINT uk_contrato_empenho              UNIQUE (exercicio_empenho, cod_entidade, cod_empenho)
);

GRANT ALL ON tcmgo.contrato                         TO GROUP urbem;
GRANT ALL ON tcmgo.contrato_assunto                 TO GROUP urbem;
GRANT ALL ON tcmgo.contrato_empenho                 TO GROUP urbem;
GRANT ALL ON tcmgo.contrato_modalidade_licitacao    TO GROUP urbem;
GRANT ALL ON tcmgo.contrato_tipo                    TO GROUP urbem;
GRANT ALL ON tcmgo.contrato_nro_sequencial_seq      TO GROUP urbem;


-- ticket #12832

CREATE SEQUENCE tcmgo.nota_fiscal_nro_sequencial_seq;

CREATE TABLE tcmgo.nota_fiscal (
    cod_nota            INTEGER                 NOT NULL,
    nro_nota            INTEGER                 NOT NULL,
    nro_serie           VARCHAR(8)              NOT NULL,
    aidf                VARCHAR(15)             NOT NULL,
    data_emissao        DATE                    NOT NULL,
    vl_nota             NUMERIC(14,2)           NOT NULL,
    inscricao_municipal BIGINT                      NULL,
    inscricao_estadual  BIGINT                      NULL,
    nro_sequencial      INTEGER                 NOT NULL DEFAULT nextval('tcmgo.nota_fiscal_nro_sequencial_seq'),
    CONSTRAINT pk_nota_fiscal                   PRIMARY KEY (cod_nota)

);

create table tcmgo.nota_fiscal_empenho (
    cod_nota            INTEGER                 NOT NULL,
    exercicio           VARCHAR(4)              NOT NULL,
    cod_entidade        INTEGER                 NOT NULL,
    cod_empenho         INTEGER                 NOT NULL,
    vl_associado        NUMERIC(14,2)           NOT NULL,
    CONSTRAINT pk_nota_fiscal_empenho           PRIMARY KEY (cod_nota, exercicio, cod_entidade, cod_empenho),
    CONSTRAINT fk_nota_fiscal_empenho_1         FOREIGN KEY (cod_nota)
                                                REFERENCES tcmgo.nota_fiscal (cod_nota),
    CONSTRAINT fk_nota_fiscal_empenho_2         FOREIGN KEY (exercicio, cod_entidade, cod_empenho)
                                                REFERENCES empenho.empenho (exercicio, cod_entidade, cod_empenho)
);

GRANT ALL ON tcmgo.nota_fiscal          TO GROUP urbem;
GRANT ALL ON tcmgo.nota_fiscal_empenho  TO GROUP urbem;
GRANT ALL on tcmgo.nota_fiscal_nro_sequencial_seq to group urbem;


--INSERT INTO administracao.acao
--          ( cod_acao
--          , cod_funcionalidade
--          , nom_arquivo
--          , parametro
--          , ordem
--          , nom_acao
--          )
--     VALUES ( 2260
--          , 364
--          , 'FMManterNotasFiscais.php'
--          , 'incluir'
--          , 21
--          ,'Incluir Notas Fiscais'
--          );
--
--INSERT INTO administracao.acao
--          ( cod_acao
--          , cod_funcionalidade
--          , nom_arquivo
--          , parametro
--          , ordem
--          , nom_acao
--          )
--     VALUES ( 2261
--          , 364
--          , 'FLManterNotasFiscais.php'
--          , 'alterar'
--          , 22
--          ,'Alterar Notas Fiscais'
--          );
--
--INSERT INTO administracao.acao
--          ( cod_acao
--          , cod_funcionalidade
--          , nom_arquivo
--          , parametro
--          , ordem
--          , nom_acao
--          )
--     VALUES ( 2262
--          , 364
--          , 'FLManterNotasFiscais.php'
--          , 'excluir'
--          , 23
--          ,'Excluir Notas Fiscais'
--          );




---------------------------------------------------------------
-- SQLs REFERENTES AO TCMBA - 20080929 - solicitado p/ Tonismar
---------------------------------------------------------------

--
-- Iserir a Gestão
--
   INSERT INTO administracao.gestao ( cod_gestao, nom_gestao           , ordem,   versao, nom_diretorio )
                               SELECT          6, 'Prestação de Contas',     6, '1.33.0', '../../../../../../gestaoPrestacaoContas/fontes/PHP/'
                                WHERE 0 = ( SELECT COUNT(1) FROM administracao.gestao WHERE cod_gestao = 6 );


   INSERT INTO administracao.modulo ( cod_modulo, cod_responsavel, nom_modulo, nom_diretorio, ordem, cod_gestao ) VALUES ( 45,0,'TCM - BA','TCMBA/',43,6);
   INSERT INTO administracao.funcionalidade ( cod_funcionalidade, cod_modulo, nom_funcionalidade, nom_diretorio, ordem ) VALUES ( 382,45,'Exportação'  ,'instancias/exportacao/'  ,1);
   INSERT INTO administracao.funcionalidade ( cod_funcionalidade, cod_modulo, nom_funcionalidade, nom_diretorio, ordem ) VALUES ( 390,45,'Configuração','instancias/configuracao/',0);

   INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao) VALUES ( 1984 , 390,'FMManterTipoBem.php'        ,'manter', 2, '','Manter Tipo Bem');

   INSERT INTO administracao.acao ( cod_acao, cod_funcionalidade, nom_arquivo, parametro, ordem, complemento_acao, nom_acao) VALUES ( 1851,382,'FLManterExportacao.php','exportar',3,'','Arquivos SIGA');

--
-- Criação schema e tabelas
--
   CREATE OR REPLACE FUNCTION manutencao2() RETURNS BOOLEAN AS $$
   DECLARE
      varSchema     VARCHAR;
   BEGIN

     SELECT nspname
       INTO varSchema
       FROM pg_namespace
      WHERE nspname = 'tcmba'
      ;

      If varSchema Is Null THEN
         Create Schema tcmba;
         Grant Usage On Schema tcmba to Group Urbem;
      End If;

      RETURN true;
   END;
   $$ LANGUAGE 'plpgsql';

   select manutencao2();
   Drop Function manutencao2();

   CREATE TABLE tcmba.tipo_bem (
     cod_natureza       integer NOT NULL,
     cod_grupo          integer NOT NULL,
     cod_tipo_tcm       integer NOT NULL,
     CONSTRAINT pk_tipo_bem   PRIMARY KEY (cod_natureza,cod_grupo),
     CONSTRAINT fk_tipo_bem_1 FOREIGN KEY (cod_natureza,cod_grupo) REFERENCES patrimonio.grupo (cod_natureza,cod_grupo)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.tipo_bem to group urbem;


   CREATE TABLE tcmba.fonte_recurso (
      exercicio   char(4) NOT NULL,
      cod_fonte   INTEGER NOT NULL,
      descricao   VARCHAR(200) NOT NULL,
      cod_recurso INTEGER,
      CONSTRAINT pk_fonte_recurso PRIMARY KEY (exercicio, cod_fonte)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.fonte_recurso to group urbem;

   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 00, null, 'Tesouro' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 01, null, 'Receita Diretamente Arrecadada' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 04, null, 'Royalties Petróleo' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 05, null, 'FIES - Fundo de Investimentos Econômicos e Sociais' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 07, null, 'COSIP - Contribuição para Custeio da Iluminação' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 08, null, 'CIDE - Contribuição de Intervenção no Domínio Econômico' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 12, null, 'Convênios Federal' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 13, null, 'Convênios Estadual' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 14, null, 'Convênios Externos' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 16, null, 'Operações de Crédito Internas' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 17, null, 'Operações de Crédito Externa' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 18, null, 'Contribuição do Inst. Previdência Social' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 20, null, 'Transferência do Tesouro - 15% Saúde' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 21, null, 'Convênio - Saúde' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 22, null, 'Transferência SUS' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 23, null, 'Consórcio Intermunicipal de Saúde' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 24, null, 'Programa de Saúde - PSF - Programa Saúde Familia' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 25, null, 'Programa de Saúde - PACS - Programa Agente Comunitário de Saúde' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 26, null, 'Programa de Saúde - PAB - Piso de Atenção Básica' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 27, null, 'Programa de Saúde - Vigilância Sanitária' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 28, null, 'Programa de Saúde - ' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 29, null, 'Outras Transferências Vinculadas a Programas de Saúde' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 30, null, 'Transferência do Tesouro - 25% Educação' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 31, null, 'FNDE - Salário Educação' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 32, null, 'Recursos FUNDEB' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 33, null, 'Programa Educação - PNAE' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 34, null, 'Programa Educação - PDDE' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 35, null, 'Programa Educação - EJA' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 36, null, 'Programa Educação - PNATE' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 39, null, 'Outras Transferências Ligadas a Projetos de Educação' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 40, null, 'Assistência Social - 5%' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 41, null, 'Programa de Assistência Social - PETI' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 42, null, 'Programa de Assistência Social - PAC' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 49, null, 'Outras Transferência do Fundo assistência Social - FNAS' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 98, null, 'Outras Fontes Internas' );
   INSERT INTO tcmba.fonte_recurso (exercicio, cod_fonte, cod_recurso, descricao ) VALUES (2006, 99, null, 'Outras Fontes Externas' );


   -- MARCA --
   CREATE TABLE tcmba.marca (
      cod_marca_tcm   INTEGER NOT NULL,
      descricao       VARCHAR(200) NOT NULL,
      cod_marca       INTEGER,
      CONSTRAINT pk_marca   PRIMARY KEY (cod_marca_tcm),
      CONSTRAINT uk_marca_1 UNIQUE(cod_marca)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.marca to group urbem;

   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 1   , 'AUDI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 2   , 'BMW', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 3   , 'BUGGRY', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 4   , 'CHRISLER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 5   , 'CITROEN', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 6   , 'DAIHATSU', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 7   , 'FIAT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 8   , 'FORD', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 9   , 'GM', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 10  , 'GURGEL', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 11  , 'HONDA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 12  , 'HYOSUNG', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 13  , 'HYUNDAI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 14  , 'JAGUAR', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 15  , 'JEEP', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 16  , 'JEEP/CHEROKEE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 17  , 'KIA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 18  , 'LADA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 19  , 'LEXUS', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 20  , 'MERCEDES BENZ', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 21  , 'MINICOOPER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 22  , 'MITSUBISHI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 23  , 'NISSAN', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 24  , 'PEUGEOT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 25  , 'PORSHE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 26  , 'PUMA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 27  , 'RENAULT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 28  , 'SANGYONG', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 29  , 'SEAT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 30  , 'SUBARU', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 31  , 'SUZUKI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 32  , 'TOYOTA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 33  , 'TROLLER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 34  , 'VOLVO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 35  , 'VW', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 36  , 'AGRALE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 37  , 'APRILIA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 38  , 'CALO/MOBILETE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 39  , 'COYOTE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 40  , 'DUCATI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 41  , 'HARLEY', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 42  , 'HONDA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 43  , 'KASINSKI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 44  , 'KAVASAKI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 45  , 'LAMBRETA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 46  , 'SUZUKI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 47  , 'TRAXX', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 48  , 'TRIUMPH', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 49  , 'VESPA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 50  , 'YAMARA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 51  , 'CUMMINS', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 52  , 'DODGE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 53  , 'FIAT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 54  , 'FNM', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 55  , 'FORD', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 56  , 'GM', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 57  , 'GMC', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 58  , 'INTERNACIONAL', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 59  , 'IVECO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 60  , 'MERCEDES BENZ', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 61  , 'SCANIA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 62  , 'VOLVO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 63  , 'VW', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 64  , 'DAIHATSU', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 65  , 'DODGE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 66  , 'ENGESA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 67  , 'ENVEMO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 68  , 'FIAT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 69  , 'FORD', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 70  , 'GM', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 71  , 'GURGEL', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 72  , 'HYUNDAI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 73  , 'ISUZU', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 74  , 'JEEP', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 75  , 'KIA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 76  , 'VW', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 77  , 'AGRALE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 78  , 'AKERMAN', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 79  , 'CASE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 80  , 'CATERPILLAR', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 81  , 'CBT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 82  , 'CLARK', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 83  , 'DAIHATSU', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 84  , 'EMPHILADEIRA CLARK', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 85  , 'GUINDASTER HYSTER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 86  , 'HUYSTER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 87  , 'IVECO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 88  , 'KOMATSU', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 89  , 'MASSEY FERGUSON', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 90  , 'PATROL HUBER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 91  , 'VALMET', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 92  , 'FORD', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 93  , 'MERCEDES BENZ', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 94  , 'SCANIA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 95  , 'VOLVO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 96  , 'VW', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 97  , 'BARCO', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 98  , 'BOTE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 99  , 'BRASBOAT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 100 , 'CATAMARA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 101 , 'CORAL VENTURA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 102 , 'D JOSE', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 103 , 'DUMAR', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 104 , 'FIBRAFORT', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 105 , 'FIBRASUL', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 106 , 'FISHING', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 107 , 'FLYPER', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 118 , 'CUMMINS', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 119 , 'HONDA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 120 , 'MERCURY', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 121 , 'MWM', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 122 , 'PERKIS', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 123 , 'SUZUKI', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 124 , 'YAMAHA', null);
   INSERT INTO tcmba.marca (cod_marca_tcm, descricao, cod_marca) VALUES ( 125 , 'YAMAR', null);

   -- TIPO_VEICULO --
   CREATE TABLE tcmba.tipo_veiculo (
      cod_tipo_tcm    INTEGER NOT NULL,
      descricao       VARCHAR(200) NOT NULL,
      cod_tipo        INTEGER,
      CONSTRAINT pk_tipo_veiculo   PRIMARY KEY (cod_tipo_tcm),
      CONSTRAINT uk_tipo_veiculo_1 UNIQUE(cod_tipo)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.tipo_veiculo to group urbem;

   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 1 , 'Automóvel', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 2 , 'Caminhão', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 3 , 'Embarcação/Barco', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 4 , 'Moto/Lambreta', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 5 , 'Ônibus', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 6 , 'Utilitários', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 7 , 'Trator', null);
   INSERT INTO tcmba.tipo_veiculo (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 8 , 'Motor Gerador', null);


   -- TIPO_COMBUSTIVEL --
   CREATE TABLE tcmba.tipo_combustivel (
      cod_tipo_tcm    INTEGER NOT NULL,
      descricao       VARCHAR(200) NOT NULL,
      cod_tipo        INTEGER,
      CONSTRAINT pk_tipo_combustivel   PRIMARY KEY (cod_tipo_tcm),
      CONSTRAINT uk_tipo_combustivel_1 UNIQUE(cod_tipo)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.tipo_combustivel to group urbem;

   INSERT INTO tcmba.tipo_combustivel (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 1 , 'Gasolina', null);
   INSERT INTO tcmba.tipo_combustivel (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 2 , 'Álcool', null);
   INSERT INTO tcmba.tipo_combustivel (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 3 , 'Diesel', null);
   INSERT INTO tcmba.tipo_combustivel (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 4 , 'Gás', null);
   INSERT INTO tcmba.tipo_combustivel (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 5 , 'Flex', null);


   -- TIPO_CERTIDAO --
   CREATE TABLE tcmba.tipo_certidao (
      cod_tipo_tcm    INTEGER NOT NULL,
      descricao       VARCHAR(200) NOT NULL,
      cod_tipo        INTEGER,
      CONSTRAINT pk_tipo_certidao      PRIMARY KEY (cod_tipo_tcm),
      CONSTRAINT uk_tipo_certidao_1 UNIQUE(cod_tipo)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.tipo_certidao to group urbem;

   INSERT INTO tcmba.tipo_certidao (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 1 , 'INSS', null);
   INSERT INTO tcmba.tipo_certidao (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 2 , 'Fazenda Federal', null);
   INSERT INTO tcmba.tipo_certidao (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 3 , 'Fazenda Estadual', null);
   INSERT INTO tcmba.tipo_certidao (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 4 , 'Fazenda Municipal', null);
   INSERT INTO tcmba.tipo_certidao (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 5 , 'FGTS', null);
   INSERT INTO tcmba.tipo_certidao (cod_tipo_tcm, descricao, cod_tipo) VALUES ( 9 , 'Outras', null);



-- TIPO_NORMA --
   CREATE TABLE tcmba.tipo_norma (
       cod_tipo_tcm  INTEGER        NOT NULL,
       descricao     VARCHAR(200)   NOT NULL,
       cod_tipo      INTEGER                ,
      CONSTRAINT pk_tipo_norma PRIMARY KEY (cod_tipo_tcm)
   );

   GRANT SELECT, INSERT, UPDATE, DELETE ON tcmba.tipo_norma to urbem;

   INSERT INTO tcmba.tipo_norma (cod_tipo_tcm, descricao, cod_tipo) VALUES (1 , 'Decreto', null);
   INSERT INTO tcmba.tipo_norma (cod_tipo_tcm, descricao, cod_tipo) VALUES (2 , 'Edital', null);
   INSERT INTO tcmba.tipo_norma (cod_tipo_tcm, descricao, cod_tipo) VALUES (3 , 'Lei', null);
   INSERT INTO tcmba.tipo_norma (cod_tipo_tcm, descricao, cod_tipo) VALUES (4 , 'Portaria', null);
   INSERT INTO tcmba.tipo_norma (cod_tipo_tcm, descricao, cod_tipo) VALUES (5 , 'Resolução', null);
   INSERT INTO tcmba.tipo_norma (cod_tipo_tcm, descricao, cod_tipo) VALUES (9 , 'Outros', null);


----------------     ----------------
-- Ticket #13101  E  -- Ticket #13105
----------------     ----------------

ALTER TABLE tcmba.marca ADD COLUMN cod_tipo_tcm INTEGER;

ALTER TABLE tcmba.marca DROP CONSTRAINT uk_marca_1;
ALTER TABLE tcmba.marca DROP CONSTRAINT pk_marca;

UPDATE tcmba.marca SET cod_tipo_tcm = 1 WHERE cod_marca_tcm BETWEEN 1 AND 35;
UPDATE tcmba.marca SET cod_tipo_tcm = 2 WHERE cod_marca_tcm BETWEEN 51 AND 63;
UPDATE tcmba.marca SET cod_tipo_tcm = 3 WHERE cod_marca_tcm BETWEEN 97 AND 107;
UPDATE tcmba.marca SET cod_tipo_tcm = 4 WHERE cod_marca_tcm BETWEEN 36 AND 50;
UPDATE tcmba.marca SET cod_tipo_tcm = 5 WHERE cod_marca_tcm BETWEEN 92 AND 96;
UPDATE tcmba.marca SET cod_tipo_tcm = 6 WHERE cod_marca_tcm BETWEEN 64 AND 76;
UPDATE tcmba.marca SET cod_tipo_tcm = 7 WHERE cod_marca_tcm BETWEEN 77 AND 91;
UPDATE tcmba.marca SET cod_tipo_tcm = 8 WHERE cod_marca_tcm BETWEEN 118 AND 125;

ALTER TABLE tcmba.marca ADD CONSTRAINT pk_marca PRIMARY KEY(cod_marca_tcm,cod_tipo_tcm);

ALTER TABLE tcmba.marca ADD CONSTRAINT fk_marca_1 FOREIGN KEY (cod_tipo_tcm) REFERENCES tcmba.tipo_veiculo(cod_tipo_tcm);

ALTER TABLE tcmba.marca ALTER COLUMN cod_tipo_tcm SET NOT NULL;


----------------
-- Ticket #13099
----------------

UPDATE administracao.acao SET ordem = 3 WHERE cod_acao = 1853;
UPDATE administracao.acao SET ordem = 4 WHERE cod_acao = 1852;


----------------
-- Ticket #13123
----------------

ALTER TABLE tcmba.tipo_veiculo DROP CONSTRAINT uk_tipo_veiculo_1;
ALTER TABLE tcmba.tipo_veiculo DROP column cod_tipo;

CREATE TABLE tcmba.tipo_veiculo_vinculo(
    cod_tipo_tcm        INTEGER             NOT NULL,
    cod_tipo            INTEGER             NOT NULL,
    CONSTRAINT pk_tipo_veiculo_vinculo      PRIMARY KEY                     (cod_tipo_tcm,cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_1    FOREIGN KEY                     (cod_tipo_tcm)
                                            REFERENCES tcmba.tipo_veiculo   (cod_tipo_tcm),
    CONSTRAINT fk_tipo_veiculo_vinculo_2    FOREIGN KEY                     (cod_tipo)
                                            REFERENCES frota.tipo_veiculo   (cod_tipo)
);


----------------
-- Ticket #13132
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2310
          , 390
          , 'FMManterUnidadeGestora.php'
          , 'manter'
          , 1
          , ''
          , 'Manter Unidade Gestora'
          );


----------------
-- Ticket #13113
----------------

ALTER TABLE tcmba.tipo_combustivel DROP CONSTRAINT uk_tipo_combustivel_1;
ALTER TABLE tcmba.tipo_combustivel DROP column cod_tipo;

CREATE TABLE tcmba.tipo_combustivel_vinculo(
    cod_tipo_tcm        INTEGER                 NOT NULL,
    cod_combustivel     INTEGER                 NOT NULL,
    CONSTRAINT pk_tipo_combustivel_vinculo      PRIMARY KEY                         (cod_tipo_tcm,cod_combustivel),
    CONSTRAINT fk_tipo_combustivel_vinculo_1    FOREIGN KEY                         (cod_tipo_tcm)
                                                REFERENCES tcmba.tipo_combustivel   (cod_tipo_tcm),
    CONSTRAINT fk_tipo_combustivel_vinculo_2    FOREIGN KEY                         (cod_combustivel)
                                                REFERENCES frota.combustivel        (cod_combustivel)
);



