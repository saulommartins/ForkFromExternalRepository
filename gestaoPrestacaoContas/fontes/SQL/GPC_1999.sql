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
* Versão 1.99.9
*/

----------------
-- Ticket #17393
----------------

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     )
VALUES
     ( 2795
     , 467
     , 'FMVincularEsferaConvenio.php'
     , 'incluir'
     , 4
     , 'Vincular Esfera Convênio'
     , 'Vincular Esfera do Convênio'
     );


CREATE TABLE tceam.esfera_convenio (
    num_convenio        INTEGER         NOT NULL,
    exercicio           VARCHAR(4)      NOT NULL,
    esfera              CHAR(1)         NOT NULL,
    CONSTRAINT pk_esfera_convenio    PRIMARY KEY (num_convenio, exercicio),
    CONSTRAINT fk_esfera_convenio_1  FOREIGN KEY (num_convenio, exercicio)
                                     REFERENCES licitacao.convenio (num_convenio, exercicio)
);
GRANT ALL ON tceam.esfera_convenio TO GROUP urbem;


----------------
-- Ticket #17433
----------------

ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada DROP CONSTRAINT pk_conciliacao_lancamento_arrecadacao_estornada;
ALTER TABLE tesouraria.conciliacao_lancamento_arrecadacao_estornada  ADD CONSTRAINT pk_conciliacao_lancamento_arrecadacao_estornada
            PRIMARY KEY (cod_plano, exercicio_conciliacao, mes, cod_arrecadacao, exercicio, timestamp_arrecadacao, timestamp_estornada, tipo);


----------------
-- Ticket #17511
----------------

DELETE FROM tceam.arquivo_contas;
ALTER TABLE tceam.arquivo_contas ADD column cod_entidade INTEGER NOT NULL;


-----------------------------------------------------------------------------
-- CRIACAO DO TIPO tp_contas_pagamento P/ CONSULTA DE RELATORIO DE PAGAMENTOS
-----------------------------------------------------------------------------

CREATE TYPE tp_contas_pagamento AS (
    cod_plano           INTEGER,
    cod_entidade_plano  INTEGER,
    cod_empenho         INTEGER,
    cod_entidade        INTEGER,
    exercicio           CHAR(4),
    banco               VARCHAR,
    agencia             VARCHAR,
    conta               VARCHAR,
    row_number          INTEGER
);


----------------
-- Ticket #17634
----------------

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
     ( 2796
     , 406
     , 'FMVincularContaFundeb.php'
     , 'incluir'
     , 19
     , ''
     , 'Vincular Conta Fundeb'
     );

CREATE TABLE stn.vinculo_fundeb(
    cod_plano       INTEGER         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    CONSTRAINT pk_vinculo_fundeb    PRIMARY KEY                              (cod_plano, cod_entidade, exercicio),
    CONSTRAINT fk_vinculo_fundeb_1  FOREIGN KEY                              (cod_plano, exercicio)
                                    REFERENCES contabilidade.plano_analitica (cod_plano, exercicio),
    CONSTRAINT fk_vinculo_fundeb_2  FOREIGN KEY                              (cod_entidade, exercicio)
                                    REFERENCES orcamento.entidade            (cod_entidade, exercicio)
);
GRANT ALL ON stn.vinculo_fundeb TO GROUP urbem;

