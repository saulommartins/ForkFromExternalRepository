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
---------------------
--- Ticket #17003 ---
---------------------

INSERT INTO tcmgo.tipo_retencao VALUES ('2011', 1, 'INSS');
INSERT INTO tcmgo.tipo_retencao VALUES ('2011', 2, 'IRRF');
INSERT INTO tcmgo.tipo_retencao VALUES ('2011', 3, 'ISS');
INSERT INTO tcmgo.tipo_retencao VALUES ('2011', 4, 'RPPS');
INSERT INTO tcmgo.tipo_retencao VALUES ('2011', 99, 'Outros');

---------------------
--- Ticket #17202 ---
---------------------

CREATE TABLE tcmgo.nota_fiscal_empenho_liquidacao (
    cod_nota                INTEGER                 NOT NULL,
    exercicio               VARCHAR(4)              NOT NULL,
    cod_entidade            INTEGER                 NOT NULL,
    cod_empenho             INTEGER                 NOT NULL,
    cod_nota_liquidacao     INTEGER                 NOT NULL,
    exercicio_liquidacao    VARCHAR(4)              NOT NULL,
    vl_associado        NUMERIC(14,2)               NOT NULL,
    CONSTRAINT pk_nota_fiscal_empenho_liquidacao           PRIMARY KEY (cod_nota, exercicio, cod_entidade, cod_empenho, cod_nota_liquidacao, exercicio_liquidacao),
    CONSTRAINT fk_nota_fiscal_empenho_liquidacao_1         FOREIGN KEY (cod_nota)
                                                REFERENCES tcmgo.nota_fiscal (cod_nota),
    CONSTRAINT fk_nota_fiscal_empenho_liquidacao_2         FOREIGN KEY (exercicio, cod_entidade, cod_empenho)
                                                REFERENCES empenho.empenho (exercicio, cod_entidade, cod_empenho),
    CONSTRAINT fk_nota_fiscal_empenho_liquidacao_3         FOREIGN KEY (exercicio_liquidacao, cod_entidade, cod_nota_liquidacao)
                                                REFERENCES empenho.nota_liquidacao (exercicio, cod_entidade, cod_nota)

);

GRANT ALL ON tcmgo.nota_fiscal_empenho_liquidacao  TO GROUP urbem;

ALTER TABLE tcmgo.nota_fiscal ALTER COLUMN nro_nota DROP NOT NULL;
ALTER TABLE tcmgo.nota_fiscal ALTER COLUMN nro_serie DROP NOT NULL;
