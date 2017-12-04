
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
* Versao 2.05.2
*
* Fabio Bertoldi - 20160525
*
*/

----------------
-- Ticket #23647
----------------

CREATE TABLE tcmba.tipo_conciliacao(
    cod_tipo_conciliacao    INTEGER     NOT NULL,
    descricao               CHAR(100)   NOT NULL,
    CONSTRAINT pk_tipo_conciliaca PRIMARY KEY (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.tipo_conciliacao TO urbem;

INSERT INTO tcmba.tipo_conciliacao VALUES (1, 'Cheque não compensado - Banco'        );
INSERT INTO tcmba.tipo_conciliacao VALUES (2, 'Débito não lançado - Contábil'        );
INSERT INTO tcmba.tipo_conciliacao VALUES (4, 'Débito indevido pelo banco'           );
INSERT INTO tcmba.tipo_conciliacao VALUES (5, 'Tarifa cobrada não lançada - Contábil');
INSERT INTO tcmba.tipo_conciliacao VALUES (6, 'Tarifa cobrada indevida banco'        );
INSERT INTO tcmba.tipo_conciliacao VALUES (7, 'Depósito não lançado pelo banco'      );
INSERT INTO tcmba.tipo_conciliacao VALUES (8, 'Crédito não lançado - Contábil'       );
INSERT INTO tcmba.tipo_conciliacao VALUES (9, 'Crédito indevido pelo banco'          );
 

CREATE TABLE tcmba.conciliacao_lancamento_contabil(
    cod_plano               INTEGER     NOT NULL,
    exercicio_conciliacao   CHAR(4)     NOT NULL,
    mes                     INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    cod_lote                INTEGER     NOT NULL,
    sequencia               INTEGER     NOT NULL,
    tipo_valor              CHAR(1)     NOT NULL,
    cod_tipo_conciliacao    INTEGER     NOT NULL,
    CONSTRAINT pk_conciliacao_lancamento_contabil   PRIMARY KEY                               (cod_plano, exercicio_conciliacao, mes, cod_lote, exercicio, tipo, sequencia, cod_entidade, tipo_valor, cod_tipo_conciliacao),
    CONSTRAINT fk_conciliacao_lancamento_contabil_1 FOREIGN KEY                               (cod_plano, exercicio_conciliacao, mes)
                                                    REFERENCES tesouraria.conciliacao         (cod_plano, exercicio            , mes),
    CONSTRAINT fk_conciliacao_lancamento_contabil_2 FOREIGN KEY                               (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor)
                                                    REFERENCES contabilidade.valor_lancamento (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor),
    CONSTRAINT fk_conciliacao_lancamento_contabil_3 FOREIGN KEY                               (cod_tipo_conciliacao)
                                                    REFERENCES tcmba.tipo_conciliacao         (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.conciliacao_lancamento_contabil TO urbem;


CREATE TABLE tcmba.conciliacao_lancamento_arrecadacao(
    cod_plano               INTEGER     NOT NULL,
    exercicio_conciliacao   CHAR(4)     NOT NULL,
    mes                     INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    cod_arrecadacao         INTEGER     NOT NULL,
    timestamp_arrecadacao   TIMESTAMP   NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    cod_tipo_conciliacao    INTEGER     NOT NULL,
    CONSTRAINT pk_conciliacao_lancamento_arrecadacao   PRIMARY KEY                       (cod_plano, exercicio_conciliacao, mes, cod_arrecadacao, exercicio, timestamp_arrecadacao, tipo, cod_tipo_conciliacao),
    CONSTRAINT fk_conciliacao_lancamento_arrecadacao_1 FOREIGN KEY                       (cod_plano, exercicio_conciliacao, mes)
                                                       REFERENCES tesouraria.conciliacao (cod_plano, exercicio            , mes),
    CONSTRAINT fk_conciliacao_lancamento_arrecadacao_2 FOREIGN KEY                       (cod_arrecadacao, exercicio, timestamp_arrecadacao)
                                                       REFERENCES tesouraria.arrecadacao (cod_arrecadacao, exercicio, timestamp_arrecadacao),
    CONSTRAINT fk_conciliacao_lancamento_arrecadacao_3 FOREIGN KEY                       (cod_tipo_conciliacao)
                                                       REFERENCES tcmba.tipo_conciliacao (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.conciliacao_lancamento_arrecadacao TO urbem;


CREATE TABLE tcmba.conciliacao_lancamento_manual(
    cod_plano             INTEGER        NOT NULL,
    exercicio             CHAR(4)        NOT NULL,
    mes                   INTEGER        NOT NULL,
    sequencia             INTEGER        NOT NULL,
    dt_lancamento         DATE           NOT NULL,
    tipo_valor            CHAR(1)        NOT NULL,
    vl_lancamento         NUMERIC(14,2)  NOT NULL,
    descricao             TEXT           NOT NULL,
    conciliado            BOOLEAN        NOT NULL,
    cod_tipo_conciliacao  INTEGER        NOT NULL,
    CONSTRAINT pk_conciliacao_lancamento_manual   PRIMARY KEY                       (cod_plano, exercicio, mes, sequencia, cod_tipo_conciliacao),
    CONSTRAINT fk_conciliacao_lancamento_manual_1 FOREIGN KEY                       (cod_plano, exercicio, mes)
                                                  REFERENCES tesouraria.conciliacao (cod_plano, exercicio, mes),
    CONSTRAINT fk_conciliacao_lancamento_manual_2 FOREIGN KEY                       (cod_tipo_conciliacao)
                                                  REFERENCES tcmba.tipo_conciliacao (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.conciliacao_lancamento_manual TO urbem;

CREATE TABLE tcmba.conciliacao_lancamento_arrecadacao_estornada (
    cod_plano               INTEGER     NOT NULL,
    exercicio_conciliacao   CHAR(4)     NOT NULL,
    mes                     INTEGER     NOT NULL,
    cod_arrecadacao         INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    timestamp_arrecadacao   TIMESTAMP   NOT NULL,
    timestamp_estornada     TIMESTAMP   NOT NULL,
    tipo                    CHAR(1)     NOT NULL,
    cod_tipo_conciliacao    INTEGER     NOT NULL,
    CONSTRAINT pk_conciliacao_lancamento_arrecadacao_estornada   PRIMARY KEY                                 (cod_plano, exercicio_conciliacao, mes, cod_arrecadacao, exercicio, timestamp_arrecadacao, timestamp_estornada, tipo),
    CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_1 FOREIGN KEY                                 (cod_plano, exercicio_conciliacao, mes)
                                                                 REFERENCES tesouraria.conciliacao           (cod_plano, exercicio            , mes),
    CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_2 FOREIGN KEY                                 (cod_arrecadacao, exercicio, timestamp_arrecadacao, timestamp_estornada)
                                                                 REFERENCES tesouraria.arrecadacao_estornada (cod_arrecadacao, exercicio, timestamp_arrecadacao, timestamp_estornada),
    CONSTRAINT fk_conciliacao_lancamento_arrecadacao_estornada_3 FOREIGN KEY                                 (cod_tipo_conciliacao)
                                                                 REFERENCES tcmba.tipo_conciliacao           (cod_tipo_conciliacao)
);
GRANT ALL ON tcmba.conciliacao_lancamento_arrecadacao_estornada TO urbem;

