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
* Versao 2.02.6
*
* Fabio Bertoldi - 20140408
*
*/

----------------
-- Ticket #21207
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
     , ativo
     )
VALUES
     ( 2945
     , 468
     , 'FLExportacaoEContas.php'
     , 'econtas'
     , 1
     , 'Exportação EContas'
     , 'EContas'
     , TRUE
     );


----------------
-- Ticket #20522
----------------

CREATE TABLE tceal.credor (
    exercicio       char(4)         NOT NULL,
    numcgm          integer         NOT NULL,
    tipo            integer         NOT NULL,
    CONSTRAINT pk_tceal_credor      PRIMARY KEY         (exercicio, numcgm),
    CONSTRAINT fk_tceal_credor_1    FOREIGN KEY         (numcgm)
                                    REFERENCES sw_cgm   (numcgm)
);
GRANT ALL ON tceal.credor TO urbem;

CREATE TABLE tceal.despesa_receita_extra (
    cod_plano       integer         NOT NULL,
    exercicio       char(4)         NOT NULL,
    classificacao   varchar(2)      NOT NULL,
    CONSTRAINT pk_despesa_receita_extra     PRIMARY KEY                              (cod_plano, exercicio),
    CONSTRAINT fk_despesa_receita_extra_1   FOREIGN KEY                              (exercicio, cod_plano)
                                            REFERENCES contabilidade.plano_analitica (exercicio, cod_plano)
);
GRANT ALL ON tceal.despesa_receita_extra TO urbem;

CREATE TABLE tceal.uniorcam (
    numcgm          integer         NOT NULL,
    exercicio       char(4)         NOT NULL,
    num_unidade     integer         NOT NULL,
    num_orgao       integer         NOT NULL,
    identificador   integer         NOT NULL,
    CONSTRAINT pk_tceal_uniorcam    PRIMARY KEY                       (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_tceal_uniorcam_1  FOREIGN KEY                       (numcgm)
                                    REFERENCES sw_cgm_pessoa_juridica (numcgm)
);
GRANT ALL ON tceal.uniorcam TO urbem;

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem
     , ativo
     )
VALUES
     ( 480
     , 62
     , 'Configuração'
     , 'instancias/configuracao/'
     , 1
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 2902
     , 480
     , 'FMManterConfiguracaoOrcamento.php'
     , 'manter'
     , 1
     , ''
     , 'Orçamento'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 2905
     , 480
     , 'FMManterConfiguracaoUnidadeAutonoma.php'
     , 'manter'
     , 2
     , ''
     , 'Unidade Autônoma'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 2906
     , 480
     , 'FMManterConfiguracaoUnidadeOrcamentaria.php'
     , 'manter'
     , 3
     , ''
     , 'Unidade Orçamentária'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 2907
     , 480
     , 'FLManterConfiguracaoCredor.php'
     , 'manter'
     , 4
     , ''
     , 'Configurar Credores'
     , TRUE
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao
     , ativo
     )
VALUES
     ( 2908
     , 480
     , 'FMManterConfiguracaoDespRecExtra.php'
     , 'manter'
     , 5
     , ''
     , 'Configurar Rec/Desp Extra'
     , TRUE
     );


----------------
-- Ticket #21277
----------------

CREATE SEQUENCE tcemg.seq_num_op_extra;


----------------
-- Ticket #21775
----------------

ALTER TABLE tcemg.nota_fiscal_empenho_liquidacao DROP COLUMN vl_total_liquido;

ALTER TABLE tcemg.nota_fiscal ADD COLUMN vl_total           NUMERIC(14,2);
ALTER TABLE tcemg.nota_fiscal ADD COLUMN vl_desconto        NUMERIC(14,2);
ALTER TABLE tcemg.nota_fiscal ADD COLUMN vl_total_liquido   NUMERIC(14,2);

UPDATE tcemg.nota_fiscal
   SET vl_total = ( SELECT SUM(vl_associado) 
                      FROM tcemg.nota_fiscal_empenho_liquidacao 
                     WHERE nota_fiscal_empenho_liquidacao.cod_nota = nota_fiscal.cod_nota 
                       AND nota_fiscal_empenho_liquidacao.exercicio = nota_fiscal.exercicio 
                       AND nota_fiscal_empenho_liquidacao.cod_entidade = nota_fiscal.cod_entidade
                   )
     , vl_desconto = 0.00
     , vl_total_liquido = ( SELECT SUM(vl_associado)
                              FROM tcemg.nota_fiscal_empenho_liquidacao
                             WHERE nota_fiscal_empenho_liquidacao.cod_nota = nota_fiscal.cod_nota
                               AND nota_fiscal_empenho_liquidacao.exercicio = nota_fiscal.exercicio
                               AND nota_fiscal_empenho_liquidacao.cod_entidade = nota_fiscal.cod_entidade
                          )
     ;


----------------
-- Ticket #20716
----------------

CREATE TABLE tceal.tipo_cargo (
    cod_tipo_cargo  INTEGER         NOT NULL,
    descricao       VARCHAR(450)    NOT NULL,
    CONSTRAINT pk_tceal_tipo_cargo  PRIMARY KEY (cod_tipo_cargo)
);
GRANT ALL ON tceal.tipo_cargo TO urbem;

INSERT INTO tceal.tipo_cargo VALUES (1, 'Efetivo'     );
INSERT INTO tceal.tipo_cargo VALUES (2, 'Comissionado');
INSERT INTO tceal.tipo_cargo VALUES (3, 'Contratado'  );
INSERT INTO tceal.tipo_cargo VALUES (6, 'Eletivo'     );

CREATE TABLE tceal.de_para_tipo_cargo (
    cod_entidade            INTEGER             NOT NULL,
    exercicio               VARCHAR(4)          NOT NULL,
    cod_sub_divisao         INTEGER             NOT NULL,
    cod_tipo_cargo_tce      INTEGER             NOT NULL,
    CONSTRAINT pk_tceal_de_para_tipo_cargo      PRIMARY KEY                     (exercicio, cod_entidade, cod_sub_divisao),
    CONSTRAINT fk_tceal_de_para_tipo_cargo_1    FOREIGN KEY                     (cod_sub_divisao)
                                                REFERENCES pessoal.sub_divisao  (cod_sub_divisao),
    CONSTRAINT fk_tceal_de_para_tipo_cargo_2    FOREIGN KEY                     (cod_tipo_cargo_tce)
                                                REFERENCES tceal.tipo_cargo     (cod_tipo_cargo),
    CONSTRAINT fk_tceal_de_para_tipo_cargo_3    FOREIGN KEY                     (cod_entidade, exercicio)
                                                REFERENCES orcamento.entidade   (cod_entidade, exercicio)
);
GRANT ALL ON tceal.de_para_tipo_cargo TO urbem;

INSERT
  INTO administracao.acao
  ( cod_acao
  , cod_funcionalidade
  , nom_arquivo
  , parametro
  , ordem
  , complemento_acao
  , nom_acao
  , ativo
  )
  VALUES
  ( 2921
  , 480
  , 'FLManterConfiguracaoVinculoEmpregaticio.php'
  , 'manter'
  , 6
  , ''
  , 'Relacionar Vínculo Empregatício'
  , TRUE
  );


----------------
-- Ticket #20557
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
     , ativo
     )
VALUES
     ( 2965
     , 480
     , 'FMManterParametrosGerais.php'
     , 'configuração'
     , 2
     , ''
     , 'Parâmetros Gerais'
     , TRUE
     );


----------------
-- Ticket #20588
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
  , ativo
  )
  VALUES
  ( 2922
  , 481
  , 'FLExportacaoOrcamento.php'
  , 'manter'
  , 3
  , ''
  , 'Arquivos Orçamento'
  , TRUE
  );


----------------
-- Ticket #21275
----------------

CREATE SEQUENCE tcemg.seq_cod_red_alq;


----------------
-- Ticket #20717
----------------

CREATE TABLE tceal.ocorrencia_funcional (
    cod_ocorrencia  INTEGER         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    exercicio       VARCHAR(4)      NOT NULL,
    CONSTRAINT pk_tceal_ocorrencia  PRIMARY KEY (cod_ocorrencia)
);
GRANT ALL ON tceal.ocorrencia_funcional TO urbem;


CREATE TABLE tceal.ocorrencia_funcional_assentamento (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_ocorrencia          INTEGER     NOT NULL,
    cod_assentamento        INTEGER     NOT NULL,
    CONSTRAINT pk_tceal_ocorrencia_funcional_assentamento   PRIMARY KEY                                 (exercicio, cod_entidade, cod_ocorrencia, cod_assentamento),
    CONSTRAINT fk_tceal_ocorrencia_funcional_assentamento_1 FOREIGN KEY                                 (cod_ocorrencia)
                                                            REFERENCES tceal.ocorrencia_funcional       (cod_ocorrencia),
    CONSTRAINT fk_tceal_ocorrencia_funcional_assentamento_2 FOREIGN KEY                                 (cod_assentamento)
                                                            REFERENCES pessoal.assentamento_assentamento(cod_assentamento),
    CONSTRAINT fk_tceal_ocorrencia_funcional_assentamento_3 FOREIGN KEY                                 (exercicio, cod_entidade)
                                                            REFERENCES orcamento.entidade               (exercicio, cod_entidade)
);
GRANT ALL ON tceal.ocorrencia_funcional_assentamento TO urbem;


INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 1, '01 – Licença por motivo de doença em pessoa da família'          , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 2, '02 – Licença por motivo de afastamento do cônjuge ou companheiro', '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 3, '03 – Licença para o serviço militar'                             , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 4, '04 – Licença para atividade política'                            , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 5, '05 – Licença para capacitação'                                   , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 6, '06 – Licença para tratar de interesses particulares'             , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 7, '07 – Licença para desempenho de mandato classista'               , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES ( 8, '08 – Licença por morte de parente'                               , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES (10, '10 – Licença Médica (até 15 dias)'                               , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES (12, '12 – Licença por convocação da Justiça'                          , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES (13, '13 – Licença por trabalho em eleições oficiais'                  , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES (14, '14 – Licença por Enlace Matrimonial'                             , '2013');
INSERT INTO tceal.ocorrencia_funcional (cod_ocorrencia, descricao, exercicio) VALUES (99, '99 – Outras Ausências'                                           , '2013');


