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
* $Id:$
*
* Versão 2.00.0
*/

------------------------------
-- Ticket #17301 #17302 #17303
------------------------------

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 57
     , 1
     , 'Anexo I'
     , 'RGFAnexo1.rptdesign'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 57
     , 2
     , 'Anexo V'
     , 'RGFAnexo5.rptdesign'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
VALUES
     ( 6
     , 57
     , 3
     , 'Anexo VI'
     , 'RGFAnexo6.rptdesign'
     );


----------------
-- Ticket #17831
----------------

INSERT
  INTO administracao.funcionalidade
  ( cod_funcionalidade
  , cod_modulo
  , nom_funcionalidade
  , nom_diretorio
  , ordem )
  VALUES
  ( 470
  , 57
  , 'Configuração'
  , 'instancias/configuracao/'
  , 1
  );

INSERT
  INTO administracao.acao
  ( cod_acao
  , cod_funcionalidade
  , nom_arquivo
  , parametro
  , ordem
  , complemento_acao
  , nom_acao )
  VALUES
  ( 2800
  , 470
  , 'FMManterRGFAnexo1.php'
  , 'configurar'
  , 1
  , ''
  , 'Configurar RGF Anexo 1'
  );

CREATE SCHEMA tcems;
GRANT ALL ON SCHEMA tcems TO urbem;

CREATE TABLE tcems.despesas_nao_computadas(
    id              INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    quadrimestre1   NUMERIC(14,2)   NOT NULL default 0.00,
    quadrimestre2   NUMERIC(14,2)   NOT NULL default 0.00,
    quadrimestre3   NUMERIC(14,2)   NOT NULL default 0.00,
    CONSTRAINT pk_despesas_nao_computadas   PRIMARY KEY (id),
    CONSTRAINT uk_despesas_nao_computadas_1 UNIQUE (exercicio, descricao)
);

GRANT ALL ON tcems.despesas_nao_computadas TO GROUP urbem;

CREATE TABLE tcems.receita_corrente_liquida(
    mes             INTEGER         NOT NULL,
    ano             CHARACTER(4)    NOT NULL,
    exercicio       CHARACTER(4)    NOT NULL,
    valor           NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_receita_corrente_liquida_tcems    PRIMARY KEY                  (mes, ano, exercicio)
);

GRANT ALL ON tcems.receita_corrente_liquida TO GROUP urbem;


---------------------------------------------------
-- CORRIGINDO NOMES DE DIRETORIOS DE ALGUNS MODULOS
---------------------------------------------------

UPDATE administracao.modulo SET nom_diretorio = 'TCESC/' WHERE cod_modulo = 52;
UPDATE administracao.modulo SET nom_diretorio = 'LRFMG/' WHERE cod_modulo = 53;
UPDATE administracao.modulo SET nom_diretorio = 'PCAMG/' WHERE cod_modulo = 54;
UPDATE administracao.modulo SET nom_diretorio = 'TCEMS/' WHERE cod_modulo = 57;


----------------
-- Ticket #17298
----------------

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem )
     VALUES
     ( 471
     , 57
     , 'Relatórios Mensais'
     , 'instancias/relatoriosMensais/'
     , 4
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
     VALUES
     ( 2801
     , 471
     , 'FLDemonstrativoRestosPagar.php'
     , 'restosPagar'
     , 1
     , ''
     , 'Demonstrativo de Restos a Pagar'
     );

INSERT
  INTO administracao.relatorio
     ( cod_gestao
     , cod_modulo
     , cod_relatorio
     , nom_relatorio
     , arquivo )
     VALUES
     ( 6
     , 57
     , 4
     , 'Demonstrativo de Restos a Pagar'
     , 'demonstrativoRestosPagar.rptdesign'
     );


