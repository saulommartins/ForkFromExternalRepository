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
* Versão 1.98.8
*/

--------------------------------
-- TABELAS P/ MODULO DEPRECIACAO
--------------------------------

ALTER TABLE patrimonio.bem ADD COLUMN vida_util                         INTEGER;
ALTER TABLE patrimonio.bem ADD COLUMN depreciavel                       BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE patrimonio.bem ADD COLUMN depreciacao_acelerada             BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE patrimonio.bem ADD COLUMN quota_depreciacao_anual           NUMERIC(5,2) NOT NULL DEFAULT 0.00;
ALTER TABLE patrimonio.bem ADD COLUMN quota_depreciacao_anual_acelerada NUMERIC(5,2) NOT NULL DEFAULT 0.00;

CREATE TABLE patrimonio.reavaliacao(
    cod_reavaliacao     INTEGER         NOT NULL,
    cod_bem             INTEGER         NOT NULL,
    dt_reavaliacao      DATE            NOT NULL,
    vida_util           INTEGER         NOT NULL,
    vl_reavaliacao      NUMERIC(14,2)   NOT NULL,
    motivo              VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_reavaliacao           PRIMARY KEY                 (cod_reavaliacao, cod_bem),
    CONSTRAINT fk_reavaliacao_1         FOREIGN KEY                 (cod_bem)
                                        REFERENCES patrimonio.bem   (cod_bem)
);

GRANT ALL ON patrimonio.reavaliacao TO GROUP urbem;


CREATE TABLE patrimonio.bem_plano_analitica(
    cod_bem             INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_plano           INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    CONSTRAINT pk_bem_plano_conta       PRIMARY KEY                              (cod_bem, timestamp),
    CONSTRAINT fk_bem_plano_conta_1     FOREIGN KEY                              (cod_bem)
                                        REFERENCES patrimonio.bem                (cod_bem),
    CONSTRAINT fk_bem_plano_conta_2     FOREIGN KEY                              (cod_plano, exercicio)
                                        REFERENCES contabilidade.plano_analitica (cod_plano, exercicio)
);

GRANT ALL ON patrimonio.bem_plano_analitica TO GROUP urbem;

CREATE TABLE patrimonio.depreciacao(
    cod_depreciacao     INTEGER         NOT NULL,
    cod_bem             INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    vl_depreciado       NUMERIC(14,2)   NOT NULL,
    dt_depreciacao      DATE            NOT NULL,
    competencia         VARCHAR(6)      NOT NULL,
    motivo              VARCHAR(100)    NOT NULL,
    acelerada           BOOLEAN         NOT NULL,
    quota_utilizada     NUMERIC(5,2)    NOT NULL,
    CONSTRAINT pk_depreciacao           PRIMARY KEY                               (cod_depreciacao, cod_bem, timestamp),
    CONSTRAINT fk_depreciacao_1         FOREIGN KEY                               (cod_bem, timestamp)
                                        REFERENCES patrimonio.bem_plano_analitica (cod_bem, timestamp)
);

GRANT ALL ON patrimonio.depreciacao TO GROUP urbem;

CREATE TABLE patrimonio.depreciacao_reavaliacao(
    cod_depreciacao     INTEGER         NOT NULL,
    cod_bem             INTEGER         NOT NULL,
    timestamp           TIMESTAMP       NOT NULL,
    cod_reavaliacao     INTEGER         NOT NULL,
    CONSTRAINT pk_depreciacao_reavaliacao   PRIMARY KEY                       (cod_depreciacao, cod_bem, timestamp),
    CONSTRAINT fk_depreciacao_reavaliacao_1 FOREIGN KEY                       (cod_depreciacao, cod_bem, timestamp)
                                            REFERENCES patrimonio.depreciacao (cod_depreciacao, cod_bem, timestamp),
    CONSTRAINT fk_depreciacao_reavaliacao_2 FOREIGN KEY                       (cod_reavaliacao, cod_bem)
                                            REFERENCES patrimonio.reavaliacao (cod_reavaliacao, cod_bem)
);

GRANT ALL ON patrimonio.depreciacao_reavaliacao TO GROUP urbem;


--------------------------------------------
-- ADICIONADA ACAO P/ Depreciacao Automatica
--------------------------------------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES
     ( 2782
     , 24
     , 'FLDepreciacaoAutomatica.php'
     , 'depreciar'
     , 25
     , ''
     , 'Depreciação Automática'
     );

