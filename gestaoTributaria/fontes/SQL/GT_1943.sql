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
* $Id: GT_1943.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 1.94.2
*/


----------------
-- Ticket #13800
----------------

ALTER TABLE divida.parcela_reducao ADD COLUMN origem_reducao CHAR(1);
ALTER TABLE divida.parcela_reducao ADD CONSTRAINT ck_parcela_reducao_1 CHECK (origem_reducao IN ('A','C'));
UPDATE      divida.parcela_reducao SET origem_reducao = 'A';
ALTER TABLE divida.parcela_reducao ALTER COLUMN origem_reducao SET NOT NULL;
ALTER TABLE divida.parcela_reducao DROP CONSTRAINT pk_parcela_reducao;
ALTER TABLE divida.parcela_reducao ADD CONSTRAINT pk_parcela_reducao PRIMARY KEY (num_parcelamento, num_parcela, origem_reducao);;

COMMENT ON COLUMN divida.parcela_reducao.origem_reducao IS 'A = acrescimo, C = credito';


----------------
-- Ticket #3938
----------------

CREATE TABLE monetario.tipo_conta (
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_conta        PRIMARY KEY (cod_tipo)
);

GRANT ALL ON TABLE monetario.tipo_conta TO GROUP urbem;

INSERT INTO monetario.tipo_conta VALUES (1,'Movimento');
INSERT INTO monetario.tipo_conta VALUES (2,'Vinculada');
INSERT INTO monetario.tipo_conta VALUES (3,'Aplicação');


ALTER TABLE monetario.conta_corrente ADD   COLUMN cod_tipo INTEGER;
UPDATE      monetario.conta_corrente SET          cod_tipo = 1;
ALTER TABLE monetario.conta_corrente ALTER COLUMN cod_tipo SET NOT NULL;

ALTER TABLE monetario.conta_corrente ADD CONSTRAINT fk_conta_corrente_2 FOREIGN KEY (cod_tipo) REFERENCES monetario.tipo_conta (cod_tipo);


----------------
-- Ticket #14032
----------------

CREATE TABLE monetario.acrescimo_norma (
    cod_acrescimo       INTEGER         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_norma           INTEGER         NOT NULL,
    CONSTRAINT pk_acrescimo_norma       PRIMARY KEY                     (cod_acrescimo, cod_tipo, timestamp),
    CONSTRAINT fk_acrescimo_norma_1     FOREIGN KEY                     (cod_acrescimo, cod_tipo)
                                        REFERENCES monetario.acrescimo  (cod_acrescimo, cod_tipo),
    CONSTRAINT fk_acrescimo_norma_2     FOREIGN KEY                     (cod_norma)
                                        REFERENCES normas.norma         (cod_norma)
);

GRANT ALL ON TABLE monetario.acrescimo_norma TO GROUP urbem;
