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
* $Id: GPC_013.sql 59612 2014-09-02 12:00:51Z gelson $
*
* Versão 013.
*/



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
    cod_tipo_tcm        INTEGER             NOT NULL,
    cod_combustivel     INTEGER             NOT NULL,
    CONSTRAINT pk_tipo_veiculo_vinculo      PRIMARY KEY                         (cod_tipo_tcm,cod_combustivel),
    CONSTRAINT fk_tipo_veiculo_vinculo_1    FOREIGN KEY                         (cod_tipo_tcm)
                                            REFERENCES tcmba.tipo_combustivel   (cod_tipo_tcm),
    CONSTRAINT fk_tipo_veiculo_vinculo_2    FOREIGN KEY                         (cod_combustivel)
                                            REFERENCES frota.combustivel        (cod_combustivel)
);



