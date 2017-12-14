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
* Versao 2.02.9
*
* Fabio Bertoldi - 20140730
*
*/

----------------
-- Ticket #20569
----------------

CREATE TABLE contabilidade.lancamento_baixa_patrimonio(
    id              INTEGER         NOT NULL,
    timestamp       TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    exercicio       CHAR(4)         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    tipo            CHAR(1)         NOT NULL,
    cod_lote        INTEGER         NOT NULL,
    sequencia       INTEGER         NOT NULL,
    cod_bem         INTEGER         NOT NULL,
    estorno         BOOLEAN         NOT NULL DEFAULT FALSE,
    CONSTRAINT pk_lancamento_baixa_patrimonio   PRIMARY KEY                         (id),
    CONSTRAINT fk_lancamento_baixa_patrimonio_1 FOREIGN KEY                         (exercicio, cod_entidade, tipo, cod_lote, sequencia)
                                                REFERENCES contabilidade.lancamento (exercicio, cod_entidade, tipo, cod_lote, sequencia),
    CONSTRAINT fk_lancamento_baixa_patrimonio_2 FOREIGN KEY                         (cod_bem)
                                                REFERENCES patrimonio.bem           (cod_bem)
);
GRANT ALL ON contabilidade.lancamento_baixa_patrimonio TO urbem;


----------------
-- Ticket #21973
----------------

ALTER TABLE frota.veiculo ALTER COLUMN num_certificado TYPE VARCHAR(14);


----------------
-- Ticket #21880
----------------

ALTER TABLE licitacao.licitacao DROP CONSTRAINT fk_licitacao_9;
DROP TABLE tcemg.regime_execucao_obras;

CREATE TABLE compras.regime_execucao_obras (
    cod_regime      INTEGER         NOT NULL,
    descricao       VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_regime_execucao_obras PRIMARY KEY (cod_regime)                    
);
GRANT ALL ON compras.regime_execucao_obras TO urbem;

INSERT INTO compras.regime_execucao_obras VALUES (1, 'Empreitada por Preço Global');
INSERT INTO compras.regime_execucao_obras VALUES (2, 'Empreitada por Preço Unitário');
INSERT INTO compras.regime_execucao_obras VALUES (3, 'Empreitada Integral');
INSERT INTO compras.regime_execucao_obras VALUES (4, 'Tarefa');
INSERT INTO compras.regime_execucao_obras VALUES (5, 'Execução Direta');


ALTER TABLE licitacao.licitacao ADD CONSTRAINT fk_licitacao_9  FOREIGN KEY (cod_regime)
                                                               REFERENCES compras.regime_execucao_obras(cod_regime);


----------------
-- Ticket #22053
----------------

ALTER TABLE frota.tipo_veiculo       ADD COLUMN controlar_horas_trabalhadas BOOLEAN      NOT NULL DEFAULT FALSE;
ALTER TABLE frota.utilizacao_retorno ADD COLUMN qtde_horas_trabalhadas      NUMERIC(6,2) NOT NULL DEFAULT 0;


----------------
-- Ticket #22015
----------------

UPDATE administracao.acao SET nom_arquivo = 'FLManterMembroAdicional.php' WHERE cod_acao = 2948;

