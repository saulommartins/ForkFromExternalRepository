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
* Versao 2.04.9
*
* Fabio Bertoldi - 20160414
*
*/

----------------
-- Ticket #23482
----------------

CREATE TABLE licitacao.contrato_apostila(
    cod_apostila        INTEGER         NOT NULL,
    num_contrato        INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    cod_alteracao       INTEGER         NOT NULL,
    descricao           TEXT            NOT NULL,
    data_apostila       DATE            NOT NULL,
    valor_apostila      NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_contrato_apostila     PRIMARY KEY     (cod_apostila, num_contrato, cod_entidade, exercicio),
    CONSTRAINT fk_contrato_apostila_1   FOREIGN KEY                   (num_contrato, cod_entidade, exercicio)
                                        REFERENCES licitacao.contrato (num_contrato, cod_entidade, exercicio)
);
GRANT ALL ON licitacao.contrato_apostila TO urbem;


UPDATE administracao.acao SET ordem = 11 WHERE cod_acao = 3087;


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
     ( 3105
     , 342
     , 'FLManterApostilaContrato.php' 
     , 'incluir'
     , 15
     , ''
     , 'Incluir Apostila de Contrato'
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
     ( 3106
     , 342
     , 'FLManterApostilaContrato.php'
     , 'alterar'
     , 16
     , ''
     , 'Alterar Apostila de Contrato'
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
     ( 3107
     , 342
     , 'FLManterApostilaContrato.php'
     , 'excluir'
     , 17
     , ''
     , 'Excluir Apostila de Contrato'
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
     ( 3108
     , 428
     , 'FLManterApostilaContrato.php' 
     , 'incluir'
     , 9
     , ''
     , 'Incluir Apostila de Contrato'
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
     ( 3109
     , 428
     , 'FLManterApostilaContrato.php'
     , 'alterar'
     , 10
     , ''
     , 'Alterar Apostila de Contrato'
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
     ( 3110
     , 428
     , 'FLManterApostilaContrato.php'
     , 'excluir'
     , 11
     , ''
     , 'Excluir Apostila de Contrato'
     , TRUE
     );


----------------
-- Ticket #23672
----------------

CREATE TABLE licitacao.tipo_instrumento(
    cod_tipo        INTEGER         NOT NULL,
    codigo_tc       INTEGER         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL DEFAULT '',
    ativo           BOOLEAN         NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_tipo_instrumento  PRIMARY KEY (cod_tipo)
);
GRANT ALL ON licitacao.tipo_instrumento TO urbem;

INSERT INTO licitacao.tipo_instrumento VALUES (1, 1, 'Contrato'                 , TRUE);
INSERT INTO licitacao.tipo_instrumento VALUES (2, 2, 'Termos de parceria/OSCIP' , TRUE);
INSERT INTO licitacao.tipo_instrumento VALUES (3, 3, 'Contratos de gestão'      , TRUE);
INSERT INTO licitacao.tipo_instrumento VALUES (4, 4, 'Outros termos de parceria', TRUE);

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
     ( 3114
     , 342
     , 'FMManterTipoInstrumento.php'
     , 'incluir'
     , 4
     , ''
     , 'Incluir Tipo de Instrumento'
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
     ( 3115
     , 342
     , 'FLManterTipoInstrumento.php'
     , 'alterar'
     , 5
     , ''
     , 'Alterar Tipo de Instrumento'
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
     ( 3116
     , 342
     , 'FLManterTipoInstrumento.php'
     , 'excluir'
     , 6
     , ''
     , 'Excluir Tipo de Instrumento'
     , TRUE
     );


----------------
-- Ticket #23670
----------------

ALTER TABLE licitacao.contrato ADD COLUMN   multa_inadimplemento VARCHAR(100);

ALTER TABLE licitacao.contrato ADD COLUMN   cod_tipo_instrumento INTEGER;
UPDATE      licitacao.contrato SET          cod_tipo_instrumento = 1;
ALTER TABLE licitacao.contrato ALTER COLUMN cod_tipo_instrumento SET NOT NULL;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_10 FOREIGN KEY               (cod_tipo_instrumento)
                                                             REFERENCES licitacao.tipo_instrumento (cod_tipo);

ALTER TABLE licitacao.contrato ADD COLUMN cgm_representante_legal INTEGER;
ALTER TABLE licitacao.contrato ADD CONSTRAINT fk_contrato_11 FOREIGN KEY   (cgm_representante_legal)
                                                             REFERENCES sw_cgm_pessoa_fisica(numcgm);


----------------
-- Ticket #23665
----------------

CREATE SEQUENCE licitacao.seq_nro_contrato_apostila;

