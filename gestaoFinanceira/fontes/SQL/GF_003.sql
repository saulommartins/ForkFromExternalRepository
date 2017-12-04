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
* $Id: GF_003.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 003.
*/
-- Criada para o ticket #12191
-- Essa tabela guarda os recursos, para posterior exportacao
-- A outra tabela vincula esta origem do recursos com o pagamento
--


CREATE TABLE empenho.origem_recursos_tcepb_interna(
  cod_origem_recursos INTEGER      NOT NULL,
  nome                varchar(200) NOT NULL,
  CONSTRAINT pk_origem_recursos_tcepb PRIMARY KEY(cod_origem_recursos)
);

GRANT INSERT, DELETE, SELECT, UPDATE ON empenho.origem_recursos_tcepb_interna TO GROUP urbem;

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 1, 'Recursos de Impostos Diretamente Arrecadados(MDE e Saúde)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 2, 'Cota-Parte do Fundo de Participação dos Municípios(MDE e Saúde)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 3, 'Cota-Parte de ICMS(MDE e Saúde)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 4, 'Cota-Parte do Fundo Especial');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 5, 'Recursos do Fundef - Magistério(60%)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 6, 'Recursos do Fundef - Outras Despesas(40%)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 7, 'Outras Transferências de Impostos(MDE e Saúde)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 8, 'Outros Recursos Diretamente Arrecadados(MDE)');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES ( 9, 'Recursos do SUS');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES (10, 'Recursos de Convênios');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES (11, 'Recursos Transferidos');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES (12, 'Recursos Previdenciários');

INSERT INTO empenho.origem_recursos_tcepb_interna(cod_origem_recursos,nome)
VALUES (99, 'Outros');

CREATE TABLE tesouraria.pagamento_origem_recursos_interna (
  cod_entidade integer NOT NULL,
  exercicio    character(4) NOT NULL,
  timestamp    timestamp DEFAULT ('now'::text)::timestamp(3) with time zone NOT NULL,
  cod_nota     integer NOT NULL,
  cod_origem_recursos integer,
  CONSTRAINT pk_pagamento_origem_recursos_interna PRIMARY KEY (exercicio, cod_entidade, cod_nota, timestamp,cod_origem_recursos),
  CONSTRAINT fk_pagamento_origem_recursos_interna_1 FOREIGN KEY (cod_entidade, exercicio, timestamp, cod_nota) REFERENCES tesouraria.pagamento(cod_entidade, exercicio, timestamp, cod_nota),
  CONSTRAINT fk_pagamento_origem_recursos_interna_2 FOREIGN KEY (cod_origem_recursos) REFERENCES empenho.origem_recursos_tcepb_interna(cod_origem_recursos)
);

GRANT INSERT, DELETE, SELECT, UPDATE ON tesouraria.pagamento_origem_recursos_interna TO GROUP urbem;

-------
-- Popula tabela com todas nota_liquidacao_paga
-------

INSERT INTO tesouraria.pagamento_origem_recursos_interna
            (cod_entidade
          , exercicio
          , timestamp
          , cod_nota
          , cod_origem_recursos)
     SELECT cod_entidade
          , exercicio
          , timestamp
          , cod_nota
          , 99
       FROM tesouraria.pagamento
      WHERE exercicio = '2008'
        AND '08924078000104' = (SELECT btrim(valor)
                                  FROM administracao.configuracao
                                 WHERE parametro = 'cnpj'
                                   AND exercicio = '2008');

INSERT INTO administracao.configuracao
            (exercicio
          , cod_modulo
          , parametro
          , valor)
     VALUES ('2008'
          , 30
          , 'seta_origem_recurso_tcepb'
          , 'false');

UPDATE administracao.configuracao
   SET valor = 'true'
 WHERE parametro =  'seta_origem_recurso_tcepb'
   AND '08924078000104' = (SELECT btrim(valor)
                             FROM administracao.configuracao
                            WHERE parametro = 'cnpj'
                              AND exercicio = '2008');

-------------------
-- Ticket #12129
-------------------

ALTER TABLE tesouraria.conciliacao_lancamento_manual ADD COLUMN conciliado BOOLEAN;
ALTER TABLE tesouraria.conciliacao_lancamento_manual ADD COLUMN dt_conciliacao date NULL;
UPDATE tesouraria.conciliacao_lancamento_manual SET conciliado = false;
ALTER TABLE tesouraria.conciliacao_lancamento_manual ALTER COLUMN conciliado SET NOT NULL;

-------------
-- Ticket 12122
-------------

CREATE TABLE empenho.nota_liquidacao_assinatura (
  cod_nota       INTEGER     NOT NULL,
  cod_entidade   INTEGER     NOT NULL,
  exercicio      CHAR(04)    NOT NULL,
  num_assinatura INTEGER     NOT NULL,
  numcgm         INTEGER     NOT NULL,
  cargo          VARCHAR(80) NOT NULL,
  CONSTRAINT pk_nota_liquidacao_assinatura PRIMARY KEY(cod_nota, cod_entidade, exercicio, num_assinatura),
  CONSTRAINT fk_nota_liquidacao_assinatura_1 FOREIGN KEY(exercicio, cod_entidade, cod_nota) REFERENCES empenho.nota_liquidacao(exercicio, cod_entidade, cod_nota),
  CONSTRAINT fk_nota_liquidacao_assinatura_2 FOREIGN KEY(numcgm) REFERENCES sw_cgm(numcgm)
);

GRANT INSERT, DELETE, SELECT, UPDATE ON empenho.nota_liquidacao_assinatura TO GROUP urbem;

----------
-- Ticket 12125
----------

INSERT INTO administracao.acao
            (cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao)
     VALUES (2216
          , 274
          , 'FLRelacaoDespesaExtra.php'
          , 'consultar'
          , 9
          , ''
          , 'Relação de Despesa Extra');
