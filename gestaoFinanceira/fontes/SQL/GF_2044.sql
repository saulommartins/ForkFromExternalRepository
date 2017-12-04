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
* Versao 2.04.4
*
* Fabio Bertoldi - 20151026
*
*/

----------------------------------------------------------
-- CORRIGINDO ESTRUTURA DE empenho.incorporacao_patrimonio
----------------------------------------------------------

ALTER TABLE empenho.incorporacao_patrimonio ADD CONSTRAINT fk_incorporacao_patrimonio_3 FOREIGN KEY                        (exercicio, cod_entidade, cod_nota)
                                                                                        REFERENCES empenho.nota_liquidacao (exercicio, cod_entidade, cod_nota);


----------------
-- Ticket #23179
----------------

CREATE TYPE colunasRelatorioPagamentoOrdemNotaEmpenho AS (
    exercicio_empenho               VARCHAR,
    cod_entidade                    INTEGER,
    cod_empenho                     INTEGER,
    cod_pre_empenho                 INTEGER,
    dt_empenho                      DATE,
    timestamp_pagamento             TIMESTAMP,
    exercicio                       VARCHAR,
    cod_nota                        INTEGER,
    exercicio_nota                  VARCHAR,
    dt_liquidacao                   DATE,
    vl_pago                         NUMERIC,
    bo_pagamento_estornado          BOOLEAN,
    timestamp_pagamento_anulada     TIMESTAMP,
    vl_pago_retencao                NUMERIC,
    tipo_retencao                   VARCHAR,
    cod_ordem                       INTEGER,
    exercicio_ordem                 VARCHAR,
    vl_ordem                        NUMERIC,
    vl_retencao                     NUMERIC,
    bo_ordem_estornada              BOOLEAN,
    timestamp_ordem_anulada         TIMESTAMP,
    cod_conta_dotacao               INTEGER,
    desdobramento                   VARCHAR,
    exercicio_plano_pagamento       VARCHAR,
    cod_plano_pagamento             INTEGER,
    cod_conta_plano_pagamento       INTEGER,
    nom_conta_plano_pagamento       VARCHAR,
    cod_estrutural_plano_pagamento  VARCHAR,
    exercicio_plano_retencao        VARCHAR,
    cod_plano_retencao              INTEGER,
    cod_receita_retencao            INTEGER,
    nom_conta_retencao              VARCHAR,
    cod_estrutural_retencao         VARCHAR
    );


----------------
-- Ticket #
----------------

CREATE TABLE contabilidade.valor_lancamento_recurso (
    cod_lote       INTEGER       NOT NULL,
    tipo           VARCHAR(1)    NOT NULL,
    sequencia      INTEGER       NOT NULL,
    exercicio      VARCHAR(4)    NOT NULL,
    tipo_valor     VARCHAR(1)    NOT NULL,
    cod_entidade   INTEGER       NOT NULL,
    cod_recurso    INTEGER       NOT NULL,
    vl_recurso     NUMERIC(14,2) NOT NULL,
    CONSTRAINT pk_valor_lancamento_recurso   PRIMARY KEY (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor, cod_recurso),
    CONSTRAINT fk_valor_lancamento_recurso_1 FOREIGN KEY (exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor)
                                             REFERENCES contabilidade.valor_lancamento(exercicio, cod_entidade, tipo, cod_lote, sequencia, tipo_valor),
    CONSTRAINT fk_valor_lancamento_recurso_2 FOREIGN KEY (exercicio, cod_recurso)
                                             REFERENCES orcamento.recurso(exercicio, cod_recurso)
);
GRANT ALL ON contabilidade.valor_lancamento_recurso TO urbem;


----------------
-- Ticket #23359
----------------

ALTER TABLE empenho.item_pre_empenho ADD COLUMN cod_centro INTEGER;
ALTER TABLE empenho.item_pre_empenho ADD CONSTRAINT fk_item_pre_empenho_4 FOREIGN KEY                         (cod_centro)
                                                                          REFERENCES almoxarifado.centro_custo(cod_centro);
                                                                          
