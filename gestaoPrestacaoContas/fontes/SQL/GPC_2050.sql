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
* Versao 2.05.0
*
* Fabio Bertoldi - 20160503
*
*/

----------------
-- Ticket #23708
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
     ( 3117
     , 364
     , 'FMManterIDE.php'
     , 'manter'
     , 61
     , ''
     , 'Configurar IDE'
     , TRUE
     );

CREATE TABLE tcmgo.configuracao_ide (
    cod_entidade            INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    cgm_chefe_governo       INTEGER     NOT NULL,
    cgm_contador            INTEGER     NOT NULL,
    cgm_controle_interno    INTEGER     NOT NULL,
    crc_contador            INTEGER     NOT NULL,
    uf_crc_contador         INTEGER     NOT NULL,
    CONSTRAINT pk_configuracao_ide      PRIMARY KEY                   (cod_entidade, exercicio),
    CONSTRAINT fk_configuracao_ide_1    FOREIGN KEY                   (cod_entidade, exercicio)
                                        REFERENCES orcamento.entidade (cod_entidade, exercicio),
    CONSTRAINT fk_configuracao_ide_2    FOREIGN KEY                   (cgm_chefe_governo)
                                        REFERENCES sw_cgm             (numcgm),
    CONSTRAINT fk_configuracao_ide_3    FOREIGN KEY                   (cgm_contador)
                                        REFERENCES sw_cgm             (numcgm),
    CONSTRAINT fk_configuracao_ide_4    FOREIGN KEY                   (cgm_controle_interno)
                                        REFERENCES sw_cgm             (numcgm),
    CONSTRAINT fk_configuracao_ide_5    FOREIGN KEY                   (uf_crc_contador)
                                        REFERENCES sw_uf              (cod_uf)
);
GRANT ALL ON tcmgo.configuracao_ide TO urbem;


----------------
-- Ticket #23722
----------------

INSERT INTO tcemg.orgao VALUES (11, '2016', '11 - Empresa Pública (não dependentes)'            );
INSERT INTO tcemg.orgao VALUES (12, '2016', '12 - Sociedade de Economia Mista (não dependentes)');


----------------
-- Ticket #23727
----------------

ALTER TABLE tcemg.arquivo_folha_pessoa DROP COLUMN cpf;
ALTER TABLE tcemg.arquivo_folha_pessoa DROP COLUMN sexo;
ALTER TABLE tcemg.arquivo_folha_pessoa DROP COLUMN dt_nascimento;


----------------
-- Ticket #23738
----------------

UPDATE tcemg.tipo_remuneracao SET descricao = 'Subsídio'                                            WHERE cod_tipo =  1;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Pensão por Morte'                                    WHERE cod_tipo =  2;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Vencimento Cargo / Função Pública / Emprego Público' WHERE cod_tipo =  3;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Proventos de Aposentadoria'                          WHERE cod_tipo =  4;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Adicional por tempo de serviço'                      WHERE cod_tipo =  5;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Vantagens Pessoais'                                  WHERE cod_tipo =  6;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Função Gratificada'                                  WHERE cod_tipo =  7;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Vantagens Eventuais'                                 WHERE cod_tipo =  8;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Pagamento Retroativo'                                WHERE cod_tipo =  9;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Adicional Noturno'                                   WHERE cod_tipo = 10;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Adicional de Insalubridade'                          WHERE cod_tipo = 11;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Adicional de Periculosidade'                         WHERE cod_tipo = 12;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Auxílios'                                            WHERE cod_tipo = 13;
UPDATE tcemg.tipo_remuneracao SET descricao = 'Indenizações'                                        WHERE cod_tipo = 14;

INSERT INTO tcemg.tipo_remuneracao (cod_tipo, descricao) VALUES (15, 'Adicional de Desempenho');
INSERT INTO tcemg.tipo_remuneracao (cod_tipo, descricao) VALUES (16, 'Abono de Permanência'   );
INSERT INTO tcemg.tipo_remuneracao (cod_tipo, descricao) VALUES (17, '13º Salário'            );
INSERT INTO tcemg.tipo_remuneracao (cod_tipo, descricao) VALUES (99, 'Outros '                );

INSERT INTO tcemg.tipo_cargo_servidor (cod_tipo, descricao) VALUES (8, 'OTC - Outros tipos de cargo');


----------------
-- Ticket #23715
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
     ( 3118
     , 480
     , 'FLManterConfiguracaoRgfRreo.php'
     , 'manter'
     , 13
     , ''
     , 'Configurar RGF e RREO'
     , TRUE
     );

CREATE TABLE tceal.publicacao_rgf(
    exercicio               VARCHAR(4)                   NOT NULL,
    cod_entidade            INTEGER                      NOT NULL,
    numcgm                  INTEGER                      NOT NULL,
    dt_publicacao           DATE                         NOT NULL,
    observacao              VARCHAR(80)                          ,
    num_publicacao          INTEGER                              ,
    CONSTRAINT pk_publicacao_rgf   PRIMARY KEY (exercicio, cod_entidade, numcgm, dt_publicacao),
    CONSTRAINT fk_publicacao_rgf_1 FOREIGN KEY                        (exercicio, cod_entidade)
                                   REFERENCES orcamento.entidade      (exercicio, cod_entidade),
    CONSTRAINT fk_publicacao_rgf_2 FOREIGN KEY                                         (numcgm)
                                   REFERENCES  licitacao.veiculos_publicidade          (numcgm)
);
GRANT ALL ON tceal.publicacao_rgf TO urbem;

CREATE TABLE tceal.publicacao_rreo(
    exercicio               VARCHAR(4)                   NOT NULL,
    cod_entidade            INTEGER                      NOT NULL,
    numcgm                  INTEGER                      NOT NULL,
    dt_publicacao           date                         NOT NULL,
    observacao              VARCHAR(80)                          ,
    num_publicacao          INTEGER                              ,
    CONSTRAINT pk_publicacao_rreo   PRIMARY KEY (exercicio, cod_entidade, numcgm, dt_publicacao),
    CONSTRAINT fk_publicacao_rreo_1 FOREIGN KEY                        (exercicio, cod_entidade)
                                    REFERENCES orcamento.entidade      (exercicio, cod_entidade),
    CONSTRAINT fk_publicacao_rreo_2 FOREIGN KEY                                         (numcgm)
                                    REFERENCES  licitacao.veiculos_publicidade          (numcgm)
);
GRANT ALL ON tceal.publicacao_rreo TO urbem;

