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
* $Id: GF_1953.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.95.3
*/

----------------
-- Ticket #
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2726
          , 438
          , 'FLProgramasMacroobjetivo.php'
          , 'emitir'
          , 2
          , ''
          , 'Programas por Macro Objetivo'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 43
          , 2
          , 'Programas por Macroobjetivo'
          , 'programasMacroobjetivo.rptdesign'
          );


--------------------------------------------------------
-- ADICIONADO RELATÒRIO DE DESPESAS PREVISTAS POR FUNCAO
--------------------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2727
          , 438
          , 'FLDespesasPrevistasFuncao.php'
          , 'emitir'
          , 3
          , ''
          , 'Despesas Previstas por Função'
          );
INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 43
          , 3
          , 'Despesas Previstas por Função'
          , 'despesasPrevistasFuncao.rptdesign'
          );


-------------------------
-- Ticket #15384 e #15385
-------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2729
          , 438
          , 'FLPrograma.php'
          , 'relatorio'
          , 4
          , ''
          , 'Resumo das Despesas por Programas'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 43
          , 4
          , 'Resumo das Despesas por Programas'
          , 'resumoDespesasProgramas.rptdesign'
          );


--------------------------------------------------
-- ADICIONADO RELATORIO DE ACOES NAO ORCAMENTARIAS
--------------------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2728
          , 438
          , 'FLAcoesNaoOrcamentarias.php'
          , 'emitir'
          , 5
          , ''
          , 'Ações Não Orçamentárias'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 43
          , 5
          , 'Ações Não Orçamentárias'
          , 'acoesNaoOrcamentarias.rptdesign'
          );


----------------
-- Ticket #15388
----------------

ALTER TABLE ppa.acao_dados ALTER COLUMN cod_natureza  DROP NOT NULL;
ALTER TABLE ppa.acao_dados ALTER COLUMN cod_funcao    DROP NOT NULL;
ALTER TABLE ppa.acao_dados ALTER COLUMN cod_subfuncao DROP NOT NULL;


----------------
-- Ticket #15390
----------------

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 437
         , 43
         , 'Consultas'
         , 'instancias/consultas/'
         , 20
         );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2730
          , 437
          , 'FLConsultarPPA.php'
          , 'consultar'
          , 1
          , ''
          , 'Consultar PPA'
          );


------------------------------------------------
-- ALTERACOES P/ HOMOLOGACAO E PUBLICACAO DE PPA
------------------------------------------------

DROP  TABLE ppa.ppa_norma;

ALTER TABLE ppa.ppa_encaminhamento DROP CONSTRAINT fk_ppa_encaminhamento_1;
ALTER TABLE ppa.ppa_encaminhamento DROP CONSTRAINT pk_ppa_encaminhamento;
ALTER TABLE ppa.ppa_publicacao     DROP CONSTRAINT pk_ppa_publicacao;

ALTER TABLE ppa.ppa_publicacao     ADD  COLUMN timestamp TIMESTAMP NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE;
ALTER TABLE ppa.ppa_encaminhamento ADD  COLUMN timestamp TIMESTAMP NOT NULL;

ALTER TABLE ppa.ppa_publicacao     ADD  CONSTRAINT pk_ppa_publicacao       PRIMARY KEY (cod_ppa, timestamp);
ALTER TABLE ppa.ppa_encaminhamento ADD  CONSTRAINT pk_ppa_encaminhamento   PRIMARY KEY (cod_ppa, timestamp);
ALTER TABLE ppa.ppa_encaminhamento ADD  CONSTRAINT fk_ppa_encaminhamento_1 FOREIGN KEY (cod_ppa, timestamp) REFERENCES ppa.ppa_publicacao(cod_ppa, timestamp);

ALTER TABLE ppa.ppa_publicacao     ADD  COLUMN cod_norma INTEGER NOT NULL;
ALTER TABLE ppa.ppa_publicacao     ADD  CONSTRAINT fk_ppa_publicacao_3     FOREIGN KEY (cod_norma) REFERENCES normas.norma (cod_norma);


----------------------------------------------------------------------
-- ADICIONANDO TIMESTAMP EM ppa.macro_objetico E ppa.programa_setorial
----------------------------------------------------------------------

ALTER TABLE ppa.macro_objetivo    ADD COLUMN timestamp TIMESTAMP NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE;
ALTER TABLE ppa.programa_setorial ADD COLUMN timestamp TIMESTAMP NOT NULL DEFAULT ('now'::text)::timestamp(3) WITH TIME ZONE;


-----------------------------------------------
-- CORRECOES NA ESTRUTURA DO MENU DO MODULO PPA
-----------------------------------------------

INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 454
          , 43
          , 'Classificação Institucional'
          , '../orcamento/instancias/classInstitucional/'
          , 6
          );

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES (  436
         , 43
         , 'Classificação Funcional Programática'
         , '../orcamento/instancias/classFuncional/'
         , 7
         );

INSERT INTO administracao.funcionalidade
         ( cod_funcionalidade
         , cod_modulo
         , nom_funcionalidade
         , nom_diretorio
         , ordem )
    VALUES ( 450
         , 43
         , 'Recursos'
         , '../orcamento/instancias/recurso/'
         , 9
         );

UPDATE administracao.funcionalidade SET ordem = 2                                   WHERE cod_funcionalidade = 454;
UPDATE administracao.funcionalidade SET ordem = 3                                   WHERE cod_funcionalidade = 436;
UPDATE administracao.funcionalidade SET ordem = 4                                   WHERE cod_funcionalidade = 430;
UPDATE administracao.funcionalidade SET ordem = 5                                   WHERE cod_funcionalidade = 431;
UPDATE administracao.funcionalidade SET ordem = 6                                   WHERE cod_funcionalidade = 450;
UPDATE administracao.funcionalidade SET ordem = 7                                   WHERE cod_funcionalidade = 462;
UPDATE administracao.funcionalidade SET ordem = 8                                   WHERE cod_funcionalidade = 463;
UPDATE administracao.funcionalidade SET ordem = 9, nom_funcionalidade = 'Programas' WHERE cod_funcionalidade = 433;
UPDATE administracao.funcionalidade SET ordem = 10                                  WHERE cod_funcionalidade = 434;


---------------------------------
-- ADICIONANDO AÇÂO HOMOLOGAR PPA
---------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2360
          , 432
          , 'FMHomologarPPA.php'
          , 'homologar'
          , 6
          , ''
          , 'Homologar PPA'
          );


----------------
-- Ticket #15422
----------------

ALTER TABLE ppa.programa_indicadores ADD COLUMN dt_indice_recente DATE NOT NULL DEFAULT ('now'::text)::date;


----------------
-- Ticket #15425
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2732
          , 438
          , 'FLDespesaFonteRecurso.php'
          , 'emitir'
          , 6
          , ''
          , 'Despesa Por Fonte de Recurso'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 43
          , 6
          , 'Despesa Por Fonte de Recursos'
          , 'despesaFonteRecurso.rptdesign'
          );


----------------
-- Ticket #15449
----------------

ALTER TABLE ppa.acao_dados ALTER COLUMN cod_produto        DROP NOT NULL;
ALTER TABLE ppa.acao_dados ALTER COLUMN cod_unidade_medida DROP NOT NULL;

