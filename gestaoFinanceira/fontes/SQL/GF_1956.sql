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
* Versão 1.95.6
*/

-----------------------------
-- ADICIONANDO TABELA ldo.ldo
-----------------------------

CREATE SCHEMA ldo;
GRANT ALL ON SCHEMA ldo TO GROUP urbem;

CREATE TABLE ldo.ldo (
    cod_ppa             INTEGER         NOT NULL,
    ano                 CHAR(1)         NOT NULL,
    CONSTRAINT pk_ldo                   PRIMARY KEY        (cod_ppa,  ano),
    CONSTRAINT fk_ldo_1                 FOREIGN KEY        (cod_ppa)
                                        REFERENCES ppa.ppa (cod_ppa)
);
GRANT ALL ON ldo.ldo TO GROUP urbem;

CREATE TABLE ldo.compensacao_renuncia (
    cod_compensacao     INTEGER             NOT NULL,
    cod_ppa             INTEGER             NOT NULL,
    ano                 CHAR(1)             NOT NULL,
    tributo             VARCHAR(250)        NOT NULL,
    modalidade          VARCHAR(250)        NOT NULL,
    setores_programas   VARCHAR(250)        NOT NULL,
    valor_ano_ldo       NUMERIC(14,2)               ,
    valor_ano_ldo_1     NUMERIC(14,2)               ,
    valor_ano_ldo_2     NUMERIC(14,2)               ,
    compensacao         VARCHAR(250)        NOT NULL,
    CONSTRAINT pk_compensacao_renuncia      PRIMARY KEY                     (cod_compensacao, cod_ppa, ano),
    CONSTRAINT fk_compensacao_renuncia_1    FOREIGN KEY                     (cod_ppa, ano)
                                            REFERENCES ldo.ldo              (cod_ppa, ano)
);
GRANT ALL ON ldo.compensacao_renuncia TO GROUP urbem;


----------------
-- Ticket #15455
----------------

CREATE TABLE ldo.acao_validada(
    cod_acao                INTEGER         NOT NULL,
    ano                     CHAR(1)         NOT NULL,
    timestamp_acao_dados    TIMESTAMP       NOT NULL,
    valor                   NUMERIC(14,2)   NOT NULL,
    quantidade              NUMERIC(14,2)   NOT NULL, 
    CONSTRAINT pk_acao_validada             PRIMARY KEY                     (cod_acao, ano, timestamp_acao_dados),
    CONSTRAINT fk_acao_validada_1           FOREIGN KEY                     (cod_acao, ano, timestamp_acao_dados)
                                            REFERENCES ppa.acao_quantidade  (cod_acao, ano, timestamp_acao_dados)
);

GRANT ALL ON ldo.acao_validada TO GROUP urbem;


----------------
-- Ticket #15459
----------------

INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 456
          , 44
          , 'Configuração'
          , 'instancias/configuracao/'
          , 1
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2737
          , 456
          , 'FLValidarAcao.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Validação de Ações'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2738
          , 456
          , 'FLValidarAcao.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Validação de Ações'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2739
          , 456
          , 'FLValidarAcao.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Validação de Ações'
          );


INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 461
          , 44
          , 'Relatórios'
          , 'instancias/relatorios/'
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
     VALUES ( 2740
          , 461
          , 'FLLDOAnexoI.php'
          , 'imprimir'
          , 1
          , ''
          , 'Anexo I - Metas e Prioridades'
          );

INSERT INTO administracao.relatorio
          ( cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES ( 2
          , 44
          , 1
          , 'Anexo I'
          , 'LDOAnexoI.rptdesign'
          );


-----------------------------------------------------
-- ADICIONANDO ACOES P/ MANUTENCAO DE Renuncia Fiscal
-----------------------------------------------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES ('2506'
     , '456'
     , 'FMManterRenunciaReceita.php'
     , 'incluir'
     , '4'
     , ''
     , 'Incluir Renúncia de Receita'
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
     )
VALUES ('2507'
     , '456'
     , 'FLManterRenunciaReceita.php'
     , 'alterar'
     , '5'
     , ''
     , 'Alterar Renúncia de Receita'
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
     )
VALUES ('2508'
     , '456'
     , 'FLManterRenunciaReceita.php'
     , 'excluir'
     , '6'
     , ''
     , 'Excluir Renúncia de Receita'
     );


----------------
-- Ticket #15514 --
----------------

UPDATE administracao.acao
   SET ordem = ordem + 1
 WHERE cod_funcionalidade = 456;

INSERT
  INTO administracao.acao
VALUES ('2486'
     , '456'
     , 'FMManterConfiguracao.php'
     , 'incluir'
     , '1'
     , ''
     , 'Configurar Indicadores');

CREATE TABLE ldo.tipo_indicadores (
    cod_tipo_indicador  INTEGER         NOT NULL,
    cod_unidade         INTEGER         NOT NULL,
    cod_grandeza        INTEGER         NOT NULL,
    descricao           VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_tipo_indicadores      PRIMARY KEY (cod_tipo_indicador),
    CONSTRAINT fk_tipo_indicadores_1    FOREIGN KEY                             (cod_unidade, cod_grandeza)
                                        REFERENCES administracao.unidade_medida (cod_unidade, cod_grandeza)
);
GRANT ALL ON ldo.tipo_indicadores TO GROUP urbem;

INSERT INTO administracao.grandeza       VALUES (9,'Contábil');
INSERT INTO administracao.unidade_medida VALUES (1, 9, 'Moeda', 'R$');

CREATE TABLE ldo.indicadores (
    exercicio           CHAR(4)         NOT NULL,
    cod_tipo_indicador  INTEGER         NOT NULL,
    indice              NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_indicadores           PRIMARY KEY                     (exercicio, cod_tipo_indicador),
    CONSTRAINT fk_indicadores_1         FOREIGN KEY                     (cod_tipo_indicador)
                                        REFERENCES ldo.tipo_indicadores (cod_tipo_indicador)
);
GRANT ALL ON ldo.indicadores TO GROUP urbem;


-------------------------------------------------------
-- ADICIONADAS ACOES P/ MANUTENÇÂO DE Tipo de Indicador
-------------------------------------------------------

UPDATE administracao.acao
   SET ordem = ordem + 3
 WHERE cod_funcionalidade = 456;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2746
          , 456
          , 'FMManterTipoIndicador.php'
          , 'incluir'
          , 1
          , ''
          , 'Incluir Tipo de Indicador'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2747
          , 456
          , 'FLManterTipoIndicador.php'
          , 'alterar'
          , 2
          , ''
          , 'Alterar Tipo de Indicador'
          );
INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2748
          , 456
          , 'FLManterTipoIndicador.php'
          , 'excluir'
          , 3
          , ''
          , 'Excluir Tipo de Indicador'
          );


----------------
-- Ticket #15647
----------------

UPDATE tesouraria.banco_cheque_layout
   SET col_valor_numerico = 81
 WHERE cod_banco = 1000;


-------------------------------------
-- ADICIONANDO ACAO Replicar Entidade
-------------------------------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2736
          , 158
          , 'FMReplicarEntidade.php'
          , 'replicar'
          , 5
          , ''
          , 'Replicar Entidade'
          );
