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
* Versão 1.99.6
*/

----------------
-- Ticket #17224
----------------

INSERT INTO administracao.configuracao VALUES ('2011', 30, 'seta_tipo_documento_liq_tceam', 'false');

UPDATE administracao.configuracao
   SET valor = 'true'
 WHERE parametro =  'seta_tipo_documento_liq_tceam'
   AND EXISTS (SELECT cod_uf 
                 FROM sw_uf 
                WHERE cod_uf IN (SELECT cod_uf 
                                   FROM sw_cgm 
                                  WHERE nom_cgm ILIKE '%prefeitura municipal%') 
                  AND sigla_uf = 'AM');

CREATE SCHEMA tceam;
GRANT ALL ON SCHEMA tceam TO GROUP urbem;

CREATE TABLE tceam.tipo_documento (
    cod_tipo           INTEGER     NOT NULL,
    descricao          VARCHAR(15) NOT NULL,
    CONSTRAINT pk_tipo_documento PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tceam.tipo_documento TO GROUP urbem;

INSERT INTO tceam.tipo_documento(cod_tipo, descricao) VALUES (1, 'Bilhete');
INSERT INTO tceam.tipo_documento(cod_tipo, descricao) VALUES (2, 'Diárias');
INSERT INTO tceam.tipo_documento(cod_tipo, descricao) VALUES (3, 'Diversos');
INSERT INTO tceam.tipo_documento(cod_tipo, descricao) VALUES (4, 'Folha');
INSERT INTO tceam.tipo_documento(cod_tipo, descricao) VALUES (5, 'Nota');
INSERT INTO tceam.tipo_documento(cod_tipo, descricao) VALUES (6, 'Recibo');


CREATE TABLE tceam.documento (
    cod_documento               INTEGER         NOT NULL,
    cod_tipo                    INTEGER         NOT NULL,
    exercicio                   CHAR(4)         NOT NULL,
    cod_entidade                INTEGER         NOT NULL,
    cod_nota                    INTEGER         NOT NULL,
    vl_comprometido             NUMERIC(14,2)       NULL,
    vl_total                    NUMERIC(14,2)       NULL,
    CONSTRAINT pk_documento PRIMARY KEY (cod_documento),
    CONSTRAINT fk_tipo_documento_bilhete_1      FOREIGN KEY (cod_tipo)
                                                REFERENCES tceam.tipo_documento (cod_tipo),
    CONSTRAINT fk_tipo_documento_bilhete_2      FOREIGN KEY (exercicio, cod_entidade, cod_nota)
                                                REFERENCES empenho.nota_liquidacao (exercicio, cod_entidade, cod_nota)
);
GRANT ALL ON tceam.documento TO GROUP urbem;

CREATE TABLE tceam.tipo_recibo (
    cod_tipo_recibo    INTEGER     NOT NULL,
    descricao          VARCHAR     NOT NULL,
    CONSTRAINT pk_tipo_recibo PRIMARY KEY (cod_tipo_recibo)
);
GRANT ALL ON tceam.tipo_recibo TO GROUP urbem;

INSERT INTO tceam.tipo_recibo(cod_tipo_recibo, descricao) VALUES (1, 'Comprovante de recebimento de recursos antecipados a terceiros(comprovante do repasse)');
INSERT INTO tceam.tipo_recibo(cod_tipo_recibo, descricao) VALUES (2, 'Demais recibos de empenhos pagos');


CREATE TABLE tceam.tipo_documento_bilhete (
    cod_tipo_documento_bilhete  INTEGER         NOT NULL,
    cod_documento               INTEGER         NOT NULL,
    numero                      VARCHAR(15)         NULL,
    dt_emissao                  DATE                NULL,
    dt_saida                    DATE                NULL,
    hora_saida                  TIME                NULL,
    destino                     VARCHAR(25)         NULL,
    dt_chegada                  DATE                NULL,
    hora_chegada                TIME                NULL,
    motivo                      VARCHAR(120)        NULL,
    CONSTRAINT pk_tipo_documento_bilhete        PRIMARY KEY (cod_tipo_documento_bilhete),
    CONSTRAINT fk_tipo_documento_bilhete_1      FOREIGN KEY (cod_documento)
                                                REFERENCES tceam.documento (cod_documento)
);
GRANT ALL ON tceam.tipo_documento_bilhete TO GROUP urbem;

CREATE TABLE tceam.tipo_documento_diaria (
    cod_tipo_documento_diaria   INTEGER         NOT NULL,
    cod_documento               INTEGER         NOT NULL,
    funcionario                 VARCHAR(30)         NULL,
    matricula                   VARCHAR(10)         NULL,
    dt_saida                    DATE                NULL,
    hora_saida                  TIME                NULL,
    destino                     VARCHAR(25)         NULL,
    dt_retorno                  DATE                NULL,
    hora_retorno                TIME                NULL,
    motivo                      VARCHAR(120)        NULL,
    CONSTRAINT pk_tipo_documento_diaria        PRIMARY KEY (cod_tipo_documento_diaria),
    CONSTRAINT fk_tipo_documento_diaria_1      FOREIGN KEY (cod_documento)
                                               REFERENCES tceam.documento (cod_documento)
);
GRANT ALL ON tceam.tipo_documento_diaria TO GROUP urbem;

CREATE TABLE tceam.tipo_documento_diverso (
    cod_tipo_documento_diverso  INTEGER         NOT NULL,
    cod_documento               INTEGER         NOT NULL,
    numero                      VARCHAR(10)         NULL,
    data                        DATE                NULL,
    descricao                   VARCHAR(120)        NULL,
    nome_documento              VARCHAR(120)        NULL,
    CONSTRAINT pk_tipo_documento_diverso        PRIMARY KEY (cod_tipo_documento_diverso),
    CONSTRAINT fk_tipo_documento_diverso_1      FOREIGN KEY (cod_documento)
                                                REFERENCES tceam.documento (cod_documento)
);
GRANT ALL ON tceam.tipo_documento_diverso TO GROUP urbem;

CREATE TABLE tceam.tipo_documento_folha (
    cod_tipo_documento_folha    INTEGER         NOT NULL,
    cod_documento               INTEGER         NOT NULL,
    mes                         CHAR(2)             NULL,
    exercicio                   CHAR(4)             NULL,
    CONSTRAINT pk_tipo_documento_folha        PRIMARY KEY (cod_tipo_documento_folha),
    CONSTRAINT fk_tipo_documento_folha_1      FOREIGN KEY (cod_documento)
                                              REFERENCES tceam.documento (cod_documento)
);
GRANT ALL ON tceam.tipo_documento_folha TO GROUP urbem;

CREATE TABLE tceam.tipo_documento_nota (
    cod_tipo_documento_nota INTEGER         NOT NULL,
    cod_documento           INTEGER         NOT NULL,
    numero_nota_fiscal      VARCHAR(10)         NULL,
    numero_serie            VARCHAR(3)          NULL,
    numero_subserie         VARCHAR(3)          NULL,
    data                    DATE                NULL,
    CONSTRAINT pk_tipo_documento_nota        PRIMARY KEY (cod_tipo_documento_nota),
    CONSTRAINT fk_tipo_documento_nota_1      FOREIGN KEY (cod_documento)
                                             REFERENCES tceam.documento (cod_documento)
);
GRANT ALL ON tceam.tipo_documento_nota TO GROUP urbem;

CREATE TABLE tceam.tipo_documento_recibo (
    cod_tipo_documento_recibo   INTEGER         NOT NULL,
    cod_documento               INTEGER         NOT NULL,
    cod_tipo_recibo             INTEGER         NOT NULL,
    numero                      VARCHAR(10)         NULL,
    valor                       NUMERIC(14,2)       NULL,
    data                        DATE                NULL,
    CONSTRAINT pk_tipo_documento_recibo        PRIMARY KEY (cod_tipo_documento_recibo),
    CONSTRAINT fk_tipo_documento_recibo_1      FOREIGN KEY (cod_documento)
                                               REFERENCES tceam.documento (cod_documento),
    CONSTRAINT fk_tipo_documento_recibo_2      FOREIGN KEY (cod_tipo_recibo)
                                               REFERENCES tceam.tipo_recibo (cod_tipo_recibo)
);
GRANT ALL ON tceam.tipo_documento_recibo TO GROUP urbem;

