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
* $Id: GF_1910.sql 64421 2016-02-19 12:14:17Z fabio $
*
* Versão 1.91.0.
*/

----------------
-- Ticket #11657
----------------

INSERT INTO administracao.relatorio (
            cod_gestao
          , cod_modulo
          , cod_relatorio
          , nom_relatorio
          , arquivo )
     VALUES (
            2
          , 10
          , 2
          , 'Ordens de Pagamento'
          , 'ordensPagamento.rptdesign'
);


----------------
-- Ticket #11667
----------------

CREATE TABLE orcamento.receita_credito_desconto(
    exercicio               CHARACTER(4)        NOT NULL,
    cod_especie             INTEGER             NOT NULL,
    cod_genero              INTEGER             NOT NULL,
    cod_natureza            INTEGER             NOT NULL,
    cod_credito             INTEGER             NOT NULL,
    cod_receita             INTEGER             NOT NULL,
    exercicio_dedutora      CHARACTER(4)        NOT NULL,
    cod_receita_dedutora    INTEGER             NOT NULL,
    CONSTRAINT pk_receita_credito_desconto      PRIMARY KEY(exercicio,cod_especie,cod_genero,cod_natureza,cod_credito,cod_receita),
    CONSTRAINT fk_receita_credito_desconto_1    FOREIGN KEY(exercicio,cod_especie,cod_genero,cod_natureza,cod_credito)
                                                REFERENCES orcamento.receita_credito(exercicio,cod_especie,cod_genero,cod_natureza,cod_credito),
    CONSTRAINT fk_receita_credito_desconto_2    FOREIGN KEY(exercicio_dedutora,cod_receita_dedutora)
                                                REFERENCES orcamento.receita(exercicio,cod_receita)

);

GRANT ALL ON orcamento.receita_credito_desconto TO GROUP urbem;

----------------
-- Ticket #13513
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2323
          , 267
          , 'FMManterArrecadacaoBanco.php'
          , 'incluir'
          , 9
          , ''
          , 'Arrecadação via Banco'
          ); 


----------------
-- Ticket #11658
----------------

UPDATE administracao.acao
   SET cod_funcionalidade = 61
 WHERE cod_acao           = 1376;


----------------
-- Ticket #13589
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2328
          , 267
          , 'FMManterArrecadacaoBanco.php'
          , 'estornar'
          , 10
          , ''
          , 'Estorno de Arrecadação via Banco'
          );


----------------
-- Ticket #13593
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2329
          , 209
          , 'FLRetencoesOrdemPagamento.php'
          , ''
          , 14
          , ''
          , 'Retenções'
          );

INSERT INTO administracao.relatorio
         ( cod_gestao
         , cod_modulo
         , cod_relatorio
         , nom_relatorio
         , arquivo )
    VALUES ( 2
         , 10
         , 5
         , 'Relatório de Retenções'
         , 'retencoesOrdemPagamento.rptdesign'
         );


----------------
-- Ticket #13594
----------------

ALTER TABLE orcamento.receita_credito_desconto  DROP CONSTRAINT fk_receita_credito_desconto_1;
ALTER TABLE orcamento.receita_credito_desconto  DROP CONSTRAINT pk_receita_credito_desconto;
ALTER TABLE orcamento.receita_credito           DROP CONSTRAINT pk_receita_credito;


ALTER TABLE orcamento.receita_credito           ADD COLUMN divida_ativa boolean not null default false;
ALTER TABLE orcamento.receita_credito           ADD CONSTRAINT pk_receita_credito PRIMARY KEY(exercicio, cod_especie, cod_genero, cod_natureza, cod_credito, divida_ativa);

ALTER TABLE orcamento.receita_credito_desconto  ADD COLUMN divida_ativa boolean not null default false;
ALTER TABLE orcamento.receita_credito_desconto  ADD CONSTRAINT pk_receita_credito_desconto PRIMARY KEY(exercicio, cod_especie, cod_genero, cod_natureza, cod_credito, cod_receita, divida_ativa);

ALTER TABLE orcamento.receita_credito_desconto  ADD CONSTRAINT fk_receita_credito_desconto_1 FOREIGN KEY (exercicio, cod_especie, cod_genero, cod_natureza, cod_credito, divida_ativa) REFERENCES orcamento.receita_credito(exercicio, cod_especie, cod_genero, cod_natureza, cod_credito, divida_ativa);


----------------
-- Ticket #13644
----------------

ALTER TABLE orcamento.receita_credito_acrescimo 
       DROP CONSTRAINT pk_receita_credito_acrescimo;
ALTER TABLE orcamento.receita_credito_acrescimo 
        ADD COLUMN divida_ativa BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE orcamento.receita_credito_acrescimo 
        ADD CONSTRAINT pk_receita_credito_acrescimo PRIMARY KEY(cod_tipo, cod_acrescimo, cod_credito, cod_natureza, cod_genero, cod_especie, exercicio, divida_ativa);

