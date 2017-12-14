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
* Versao 2.02.8
*
* Fabio Bertoldi - 20140710
*
*/

----------------
-- Ticket #21892
----------------

CREATE TABLE normas.lei(
    cod_lei     INTEGER     NOT NULL,
    descricao   VARCHAR(15) NOT NULL,
    CONSTRAINT pk_lei       PRIMARY KEY (cod_lei)
);
GRANT ALL ON normas.lei TO urbem;

INSERT INTO normas.lei VALUES (1, 'PPA');
INSERT INTO normas.lei VALUES (2, 'LDO');
INSERT INTO normas.lei VALUES (3, 'LOA');


CREATE TABLE normas.norma_detalhe_al(
    cod_norma               INTEGER         NOT NULL,
    cod_lei_alteracao       INTEGER         NOT NULL,
    cod_norma_alteracao     INTEGER         NOT NULL,
    descricao_alteracao     VARCHAR(250)    NOT NULL,
    CONSTRAINT pk_norma_detalhe_al          PRIMARY KEY             (cod_norma),
    CONSTRAINT fk_norma_detalhe_al_1        FOREIGN KEY             (cod_lei_alteracao)
                                            REFERENCES normas.lei   (cod_lei),
    CONSTRAINT fk_norma_detalhe_al_2        FOREIGN KEY             (cod_norma)
                                            REFERENCES normas.norma (cod_norma),
    CONSTRAINT fk_norma_detalhe_al_3        FOREIGN KEY             (cod_norma_alteracao)
                                            REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON normas.norma_detalhe_al TO urbem;


----------------
-- Ticket #21890
----------------

UPDATE administracao.acao           SET ativo = FALSE where cod_acao = 1439;
UPDATE administracao.funcionalidade SET ativo = FALSE where cod_funcionalidade = 298;
