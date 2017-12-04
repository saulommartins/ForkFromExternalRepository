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
* Versao 2.03.3
*
* Fabio Bertoldi - 20141007
*
*/

----------------
-- Ticket #22257
----------------

CREATE TABLE patrimonio.depreciacao_anulada(
    cod_depreciacao     INTEGER         NOT NULL,
    cod_bem             INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    timestamp_anulacao  TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    motivo              VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_depreciacao_anulada   PRIMARY KEY                      (cod_depreciacao, cod_bem, timestamp),
    CONSTRAINT fk_depreciacao_anulada_1 FOREIGN KEY                      (cod_depreciacao, cod_bem, timestamp)
                                        REFERENCES patrimonio.depreciacao(cod_depreciacao, cod_bem, timestamp)
);
GRANT ALL ON patrimonio.depreciacao_anulada TO urbem;


----------------
-- Ticket #22313
----------------

CREATE TABLE tceal.tipo_documento_fiscal(
    cod_tipo_documento_fiscal   INTEGER     NOT NULL,
    descricao                   VARCHAR(25) NOT NULL,
    CONSTRAINT pk_tipo_documento_fiscal     PRIMARY KEY (cod_tipo_documento_fiscal)
);
GRANT ALL ON tceal.tipo_documento_fiscal TO urbem;

INSERT INTO tceal.tipo_documento_fiscal VALUES (1, 'Nota Fiscal Impressa'  );
INSERT INTO tceal.tipo_documento_fiscal VALUES (2, 'Nota Fiscal Eletrônica');
INSERT INTO tceal.tipo_documento_fiscal VALUES (3, 'Cupom Fiscal'          );
INSERT INTO tceal.tipo_documento_fiscal VALUES (4, 'Inexistente'           );

CREATE TABLE tceal.bem_comprado_tipo_documento_fiscal(
    cod_bem                     INTEGER     NOT NULL,
    cod_tipo_documento_fiscal   INTEGER     NOT NULL,
    CONSTRAINT pk_bem_comprado_tipo_documento_fiscal    PRIMARY KEY (cod_bem),
    CONSTRAINT fk_bem_comprado_tipo_documento_fiscal_1  FOREIGN KEY (cod_bem)
                                                        REFERENCES patrimonio.bem_comprado(cod_bem),
    CONSTRAINT fk_bem_comprado_tipo_documento_fiscal_2  FOREIGN KEY (cod_tipo_documento_fiscal)
                                                        REFERENCES tceal.tipo_documento_fiscal(cod_tipo_documento_fiscal)
);
GRANT ALL ON tceal.bem_comprado_tipo_documento_fiscal TO urbem;

ALTER TABLE patrimonio.bem_comprado ALTER COLUMN nota_fiscal TYPE VARCHAR(30);
ALTER TABLE patrimonio.bem_comprado ADD   COLUMN data_nota_fiscal DATE;
