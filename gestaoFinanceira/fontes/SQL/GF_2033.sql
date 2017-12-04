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
* Fabio Bertoldi - 20141024
*
*/

----------------
-- Ticket #22286
----------------

ALTER TABLE empenho.ordem_pagamento ALTER COLUMN observacaO TYPE VARCHAR(600);


------------------------------------------------------------
-- CORRECAO NA DESCRICAO DE TIPO DOCUMENTO - Silvia 20141104
------------------------------------------------------------

UPDATE tcemg.tipo_documento SET descricao = 'Borderô' WHERE cod_tipo = 4;


----------------
-- Ticket #22226
----------------

CREATE TABLE tceto.tipo_pagamento(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_pagamento    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tceto.tipo_pagamento TO urbem;

INSERT INTO tceto.tipo_pagamento VALUES (1, 'Contrapartida de Convênios');
INSERT INTO tceto.tipo_pagamento VALUES (2, 'Fopag'                     );
INSERT INTO tceto.tipo_pagamento VALUES (3, 'Demais Pagamentos'         );


CREATE TABLE tceto.transferencia_tipo_pagamento(
    cod_lote        INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    tipo            CHAR(1)     NOT NULL,
    cod_tipo        INTEGER     NOT NULL,
    CONSTRAINT pk_transferencia_tipo_pagamento      PRIMARY KEY (cod_lote, exercicio, cod_entidade, tipo),
    CONSTRAINT fk_transferencia_tipo_pagamento_1    FOREIGN KEY (cod_lote, exercicio, cod_entidade, tipo)
                             REFERENCES tesouraria.transferencia(cod_lote, exercicio, cod_entidade, tipo),
    CONSTRAINT fk_transferencia_tipo_pagamento_2    FOREIGN KEY (cod_tipo)
                                                    REFERENCES tceto.tipo_pagamento(cod_tipo)
);
GRANT ALL ON tceto.transferencia_tipo_pagamento TO urbem;


----------------
-- Ticket #20917
----------------

CREATE TABLE tceto.tipo_documento(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_documento    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tceto.tipo_documento TO urbem;

INSERT INTO tceto.tipo_documento VALUES (1, 'Nota Fiscal'     );
INSERT INTO tceto.tipo_documento VALUES (2, 'Recibo'          );
INSERT INTO tceto.tipo_documento VALUES (3, 'Diária'          );
INSERT INTO tceto.tipo_documento VALUES (4, 'Folha Pagamento' );
INSERT INTO tceto.tipo_documento VALUES (5, 'Bilhete Passagem');
INSERT INTO tceto.tipo_documento VALUES (9, 'Outros'          );

CREATE TABLE tceto.nota_liquidacao_documento(
    exercicio           CHAR(4)     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    cod_nota            INTEGER     NOT NULL,
    cod_tipo            INTEGER     NOT NULL,
    nro_documento       VARCHAR(15) NOT NULL,
    dt_documento        DATE                ,
    descricao           VARCHAR(255)        ,
    autorizacao         VARCHAR(15)         ,
    modelo              VARCHAR(15)         ,
    CONSTRAINT pk_nota_liquidacao_documento     PRIMARY KEY (exercicio, cod_entidade, cod_nota),
    CONSTRAINT fk_nota_liquidacao_documento_1   FOREIGN KEY (exercicio, cod_entidade, cod_nota)
                         REFERENCES empenho.nota_liquidacao (exercicio, cod_entidade, cod_nota),
    CONSTRAINT fk_nota_liquidacao_documento_2   FOREIGN KEY (cod_tipo)
                                                REFERENCES tceto.tipo_documento(cod_tipo)
);
GRANT ALL ON tceto.nota_liquidacao_documento TO urbem;


----------------
-- Ticket #22390
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    stSQL := '
                 SELECT cod_entidade
                   FROM empenho.ordem_pagamento_retencao
                  WHERE exercicio = ''2014''
               GROUP BY cod_entidade
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
          UPDATE empenho.ordem_pagamento_retencao
             SET cod_receita = NULL
           WHERE exercicio    = '2014'
             AND cod_entidade = reRecord.cod_entidade
             AND cod_plano IN (
                                SELECT cod_plano
                                  FROM contabilidade.plano_analitica
                                 WHERE exercicio = '2014'
                                   AND cod_conta IN (
                                                      SELECT cod_conta
                                                        FROM contabilidade.plano_conta
                                                       WHERE exercicio      = '2014'
                                                         AND cod_estrutural NOT LIKE '4%'
                                                    )
                              )
               ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #22403
----------------

CREATE TABLE  tceto.tipo_transferencia(
    cod_tipo        INTEGER         NOT NULL,
    descricao       VARCHAR(30)     NOT NULL,
    CONSTRAINT pk_tipo_transferencia    PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tceto.tipo_transferencia TO urbem;

INSERT INTO tceto.tipo_transferencia VALUES (1, 'Contrapartida de Convênios');
INSERT INTO tceto.tipo_transferencia VALUES (2, 'Fopag'                     );
INSERT INTO tceto.tipo_transferencia VALUES (3, 'Demais Pagamentos'         );


CREATE TABLE tceto.transferencia_tipo_transferencia(
    cod_lote                      INTEGER     NOT NULL,
    exercicio                     CHAR(4)     NOT NULL,
    cod_entidade                  INTEGER     NOT NULL,
    tipo                          CHAR(1)     NOT NULL,
    cod_tipo_transferencia        INTEGER     NOT NULL,
    exercicio_empenho             CHAR(4),
    cod_empenho                   INTEGER,  
    
    CONSTRAINT pk_transferencia_tipo_transferencia      PRIMARY KEY (cod_lote, exercicio, cod_entidade, tipo),
    CONSTRAINT fk_transferencia_tipo_transferencia_1    FOREIGN KEY (cod_lote, exercicio, cod_entidade, tipo)
                             REFERENCES tesouraria.transferencia(cod_lote, exercicio, cod_entidade, tipo),
    CONSTRAINT fk_transferencia_tipo_transferencia_2    FOREIGN KEY (cod_tipo_transferencia)
                                                        REFERENCES tceto.tipo_transferencia(cod_tipo),
    CONSTRAINT fk_transferencia_tipo_transferencia_3    FOREIGN KEY (exercicio_empenho, cod_entidade, cod_empenho) 
                                                        REFERENCES empenho.empenho(exercicio, cod_entidade, cod_empenho)
);
GRANT ALL ON tceto.transferencia_tipo_transferencia TO urbem;


----------------
-- Ticket #22409
----------------

CREATE TABLE tceal.identificador_acao(
    cod_identificador   INTEGER     NOT NULL,
    descricao           VARCHAR(60) NOT NULL,
    CONSTRAINT pk_identificador_acao    PRIMARY KEY (cod_identificador)
);
GRANT ALL ON tceal.identificador_acao TO urbem;

INSERT INTO tceal.identificador_acao VALUES (1, 'RPPS');
INSERT INTO tceal.identificador_acao VALUES (2, 'Demais Projetos / Atividades / Operações Especiais');

CREATE TABLE tceal.acao_identificador_acao(
    cod_acao            INTEGER     NOT NULL,
    cod_identificador   INTEGER     NOT NULL,
    CONSTRAINT pk_acao_identificador_acao   PRIMARY KEY (cod_acao),
    CONSTRAINT fk_acao_identificador_acao_1 FOREIGN KEY (cod_acao)
                                            REFERENCES ppa.acao(cod_acao),
    CONSTRAINT fk_acao_identificador_acao_2 FOREIGN KEY (cod_identificador)
                                            REFERENCES tceal.identificador_acao(cod_identificador)
);
GRANT ALL ON tceal.acao_identificador_acao TO urbem;


CREATE TABLE tceto.identificador_acao(
    cod_identificador   INTEGER     NOT NULL,
    descricao           VARCHAR(60) NOT NULL,
    CONSTRAINT pk_identificador_acao    PRIMARY KEY (cod_identificador)
);
GRANT ALL ON tceto.identificador_acao TO urbem;

INSERT INTO tceto.identificador_acao VALUES (1, 'RPPS');
INSERT INTO tceto.identificador_acao VALUES (2, 'Demais Projetos / Atividades / Operações Especiais');

CREATE TABLE tceto.acao_identificador_acao(
    cod_acao            INTEGER     NOT NULL,
    cod_identificador   INTEGER     NOT NULL,
    CONSTRAINT pk_acao_identificador_acao   PRIMARY KEY (cod_acao),
    CONSTRAINT fk_acao_identificador_acao_1 FOREIGN KEY (cod_acao)
                                            REFERENCES ppa.acao(cod_acao),
    CONSTRAINT fk_acao_identificador_acao_2 FOREIGN KEY (cod_identificador)
                                            REFERENCES tceto.identificador_acao(cod_identificador)
);
GRANT ALL ON tceto.acao_identificador_acao TO urbem;


----------------
-- Ticket #22414
----------------

CREATE TABLE tceto.pagamento_tipo_pagamento(
    cod_nota        INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    timestamp       TIMESTAMP       NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_tipo_pagamento INTEGER      NOT NULL,
    CONSTRAINT pk_pagamento_tipo_documento      PRIMARY KEY                     (cod_nota, exercicio, cod_entidade, timestamp, cod_tipo_pagamento),
    CONSTRAINT fk_pagamento_tipo_documento_1    FOREIGN KEY                     (cod_nota, exercicio, cod_entidade, timestamp)
                                                REFERENCES tesouraria.pagamento (cod_nota, exercicio, cod_entidade, timestamp),
    CONSTRAINT fk_pagamento_tipo_documento_2    FOREIGN KEY                     (cod_tipo_pagamento)
                                                REFERENCES tceto.tipo_pagamento (cod_tipo)
);
GRANT ALL ON tceto.pagamento_tipo_pagamento TO urbem;


