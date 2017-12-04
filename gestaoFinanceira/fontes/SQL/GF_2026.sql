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
* Versao 2.02.6
*
* Fabio Bertoldi - 20140526
*
*/

----------------
-- Ticket #20570
----------------

CREATE TABLE tceal.tipo_documento(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_documento    PRIMARY KEY (cod_tipo)
);

GRANT ALL ON tceal.tipo_documento TO GROUP urbem;

INSERT INTO tceal.tipo_documento VALUES(1, 'Nota Fiscal Impressa'  );
INSERT INTO tceal.tipo_documento VALUES(2, 'Recibo'                );
INSERT INTO tceal.tipo_documento VALUES(3, 'Diária'                );
INSERT INTO tceal.tipo_documento VALUES(4, 'Folha Pagamento'       );
INSERT INTO tceal.tipo_documento VALUES(5, 'Bilhete Passagem'      );
INSERT INTO tceal.tipo_documento VALUES(6, 'Nota Fiscal Eletrônica');
INSERT INTO tceal.tipo_documento VALUES(7, 'Cupom Fiscal'          );
INSERT INTO tceal.tipo_documento VALUES(9, 'Outros'                );

CREATE TABLE tceal.documento(
    cod_tipo       INTEGER          NOT NULL,
    exercicio      CHAR(4)          NOT NULL,
    cod_nota       INTEGER          NOT NULL,
    cod_entidade   INTEGER          NOT NULL,
    nro_documento  VARCHAR(15)      NOT NULL,
    dt_documento   DATE                    ,
    descricao      VARCHAR(255)            ,
    autorizacao    VARCHAR(15)             ,
    modelo         VARCHAR(15)             ,
    nro_xml_nfe    VARCHAR(44)             ,
    CONSTRAINT pk_documento         PRIMARY KEY                         (exercicio, cod_nota, cod_entidade ),
    CONSTRAINT fk_documento_1       FOREIGN KEY                         (exercicio, cod_entidade, cod_nota)
                                    REFERENCES empenho.nota_liquidacao  (exercicio, cod_entidade, cod_nota)
);
GRANT ALL ON tceal.documento TO urbem;


----------------
-- Ticket #20574
----------------

CREATE TABLE tceal.pagamento_codigo_tipo_documento (
    cod_tipo_documento      INTEGER         NOT NULL,
    nom_tipo_documento      VARCHAR(255)    NOT NULL,
    CONSTRAINT pk_pagamento_codigo_tipo_documento PRIMARY KEY (cod_tipo_documento)
);
GRANT ALL ON tceal.pagamento_codigo_tipo_documento TO urbem;

INSERT INTO tceal.pagamento_codigo_tipo_documento (cod_tipo_documento, nom_tipo_documento) VALUES (1,'Ordem Bancária');
INSERT INTO tceal.pagamento_codigo_tipo_documento (cod_tipo_documento, nom_tipo_documento) VALUES (2,'Cheque');


CREATE TABLE tceal.pagamento_tipo_documento (
    cod_tipo_documento      INTEGER             NOT NULL,
    cod_entidade            INTEGER             NOT NULL,
    exercicio               VARCHAR(4)          NOT NULL,
    timestamp               TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_nota                INTEGER             NOT NULL,
    num_documento           VARCHAR(15),
    CONSTRAINT pk_pagamento_tipo_documento      PRIMARY KEY                                      (exercicio, cod_entidade, cod_nota, timestamp, cod_tipo_documento),
    CONSTRAINT fk_pagamento_tipo_documento_1    FOREIGN KEY                                      (cod_entidade, exercicio, timestamp, cod_nota)
                                                REFERENCES tesouraria.pagamento                  (cod_entidade, exercicio, timestamp, cod_nota),
    CONSTRAINT fk_pagamento_tipo_documento_2    FOREIGN KEY                                      (cod_tipo_documento)
                                                REFERENCES tceal.pagamento_codigo_tipo_documento (cod_tipo_documento)
);
GRANT ALL ON tceal.pagamento_tipo_documento TO urbem;


----------------
-- Ticket #21792
----------------

CREATE TABLE administracao.esfera(
    cod_esfera  INTEGER     NOT NULL,
    descricao   VARCHAR(15) NOT NULL,
    CONSTRAINT pk_esfera    PRIMARY KEY (cod_esfera)
);
GRANT ALL ON administracao.esfera TO urbem;

INSERT INTO administracao.esfera VALUES (1, 'Federal');
INSERT INTO administracao.esfera VALUES (2, 'Estadual');
INSERT INTO administracao.esfera VALUES (3, 'Municipal');

ALTER TABLE orcamento.recurso_direto ADD COLUMN cod_tipo_esfera INTEGER;
ALTER TABLE orcamento.recurso_direto ADD CONSTRAINT fk_recurso_direto_3 FOREIGN KEY                     (cod_tipo_esfera)
                                                                        REFERENCES administracao.esfera (cod_esfera);
