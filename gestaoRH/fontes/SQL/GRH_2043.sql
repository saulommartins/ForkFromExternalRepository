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
* Versao 2.04.3
*
* Fabio Bertoldi - 20150911
*
*/

----------------
-- Ticket #23251
----------------

CREATE TABLE tcmba.tipo_alteracao_orcamentaria(
    cod_tipo        INTEGER     NOT NULL,
    descricao       VARCHAR(80) NOT NULL,
    CONSTRAINT pk_tipo_alteracao_orcamentaria   PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmba.tipo_alteracao_orcamentaria TO urbem;


INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (4 , 'Crédito Especial por anulação de dotação (crédito)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (5 , 'Crédito Extraordinário por anulação de dotação (crédito)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (6 , 'Suplementação por anulação de dotação (crédito)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (7 , 'Crédito Especial por excesso de arrecadação');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (8 , 'Crédito Extraordinário por excesso de arrecadação');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (9 , 'Suplementação por excesso de arrecadação');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (10, 'Crédito Especial por superávit (Financeiro do exercício anterior)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (11, 'Crédito Extraordinário por superávit(Financeiro do exercício anterior)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (12, 'Suplementação por superávit (Financeiro do exercício anterior)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (13, 'Crédito Especial por operação de crédito');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (14, 'Crédito Extraordinário por operação de crédito');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (15, 'Suplementação por operação de crédito');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (16, 'Crédito Especial decorrente da assinatura de convênio');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (17, 'Crédito Extraordinário decorrente da assinatura de convênio');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (18, 'Suplementação decorrente da assinatura de convênio');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (19, 'Anulação de Dotação');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (20, 'Alteração do QDD (Acréscimo)');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (21, 'Reabertura de Crédito Especial');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (22, 'Reabertura de Crédito Extraordinário');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (27, 'Transposição / Remanejamento / Transferência de Programa p/ Outra');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (28, 'Transposição / Remanejamento / Transferência de um Órgão p/ Outro');
INSERT INTO tcmba.tipo_alteracao_orcamentaria VALUES (29, 'Suplementação decorrente da reserva de contigência');

CREATE TABLE tcmba.limite_alteracao_credito(
    exercicio           CHAR(4)         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    cod_norma           INTEGER         NOT NULL,
    cod_tipo_alteracao  INTEGER         NOT NULL,
    valor_alteracao     NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_limite_alteracao_credito    PRIMARY KEY (exercicio, cod_entidade, cod_norma, cod_tipo_alteracao),
    CONSTRAINT fk_limite_alteracao_credito_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                              REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_limite_alteracao_credito_2  FOREIGN KEY                                 (cod_norma)
                                              REFERENCES normas.norma                     (cod_norma),
    CONSTRAINT fk_limite_alteracao_credito_3  FOREIGN KEY                                 (cod_tipo_alteracao)
                                              REFERENCES tcmba.tipo_alteracao_orcamentaria(cod_tipo)
);
GRANT ALL ON tcmba.limite_alteracao_credito TO urbem;

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
     ( 3084
     , 390
     , 'FLManterLimiteAlteracaoCreditoAdicional.php'
     , 'manter'
     , 15
     , ''
     , 'Limites para Alteração de Créditos Adicionais'
     , TRUE
     );

