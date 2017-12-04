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
* Versao 2.02.4
*
* Gelson Gonçalves - 20140327
*
*/

----------------
-- Ticket #21612
----------------

CREATE TABLE licitacao.natureza_cargo (
codigo INTEGER NOT NULL,
descricao VARCHAR NULL,
CONSTRAINT pk_natureza_cargo PRIMARY KEY (codigo)
);

INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (0, 'Não Informado');
INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (1, 'Servidor Efetivo');
INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (2, 'Empregado Temporário');
INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (3, 'Cargo em Comissão');
INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (4, 'Empregado Público');
INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (5, 'Agente Político');
INSERT INTO licitacao.natureza_cargo (codigo, descricao) VALUES (6, 'Outra');

ALTER TABLE licitacao.comissao_membros ADD cargo VARCHAR(50) NOT NULL DEFAULT '';
ALTER TABLE licitacao.comissao_membros ADD natureza_cargo INTEGER NOT NULL DEFAULT 0;
ALTER TABLE licitacao.comissao_membros ADD CONSTRAINT fk_comissao_membros_5 FOREIGN KEY (natureza_cargo) REFERENCES licitacao.natureza_cargo(codigo);


----------------
-- Ticket #21604
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
     , ativo) 
  VALUES 
     (
       2948
     , 323
     , 'LSManterMembroAdicional.php'
     , 'manter'
     , 4
     ,''
     ,'Manter Membro Adicional'
     , true);

ALTER TABLE licitacao.membro_adicional ADD cargo VARCHAR(50) NOT NULL DEFAULT '';
ALTER TABLE licitacao.membro_adicional ADD natureza_cargo INTEGER NOT NULL DEFAULT 0;
ALTER TABLE licitacao.membro_adicional ADD CONSTRAINT fk_membro_adicional_3 FOREIGN KEY (natureza_cargo) REFERENCES licitacao.natureza_cargo(codigo);


----------------
-- Ticket #21637
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
     ( 2949
     , 326
     , 'FMManterRenunciaRecurso.php'
     , 'alterar'
     , 70
     , ''
     , 'Renúncia ao Prazo de Recurso'
     , TRUE
     );

ALTER TABLE licitacao.participante ADD COLUMN renuncia_recurso BOOLEAN NOT NULL DEFAULT TRUE;


----------------
-- Ticket #21646
----------------

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
     ( 483
     , 7
     , 'Controle Escolar'
     , 'instancias/transporte_escolar/'
     , 100
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
     ( 2950
     , 483
     , 'FMManterEscola.php'
     , 'manter'
     , 10
     , ''
     , 'Cadastro de Escola'
     , TRUE
     );

CREATE TABLE frota.escola(
    numcgm      INTEGER     NOT NULL,
    ativo       BOOLEAN     NOT NULL DEFAULT TRUE,
    CONSTRAINT pk_escola    PRIMARY KEY       (numcgm),
    CONSTRAINT fk_escola_1  FOREIGN KEY       (numcgm)
                            REFERENCES sw_cgm (numcgm)
);
GRANT ALL ON frota.escola TO GROUP urbem;


----------------
-- Ticket #21646
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
     ( 2951
     , 483
     , 'FLManterTransporteEscolar.php'
     , 'manter'
     , 20
     , ''
     , 'Transporte Escolar'
     , TRUE
     );

CREATE TABLE frota.turno(
    cod_turno       INTEGER         NOT NULL,
    descricao       VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_turno             PRIMARY KEY (cod_turno)
);
GRANT ALL ON frota.turno TO urbem;

INSERT INTO frota.turno VALUES (0, 'Não Informado'       );
INSERT INTO frota.turno VALUES (1, 'Manhã'               );
INSERT INTO frota.turno VALUES (2, 'Tarde'               );
INSERT INTO frota.turno VALUES (3, 'Noite'               );
INSERT INTO frota.turno VALUES (4, 'Manhã e Tarde'       );
INSERT INTO frota.turno VALUES (5, 'Manhã e Noite'       );
INSERT INTO frota.turno VALUES (6, 'Tarde e Noite'       );
INSERT INTO frota.turno VALUES (7, 'Manhã, Tarde e Noite');


CREATE TABLE frota.transporte_escolar(
    exercicio       CHAR(4)             NOT NULL,
    mes             INTEGER             NOT NULL,
    cod_veiculo     INTEGER             NOT NULL,
    cgm_escola      INTEGER             NOT NULL,
    passageiros     INTEGER             NOT NULL    DEFAULT 0,
    distancia       INTEGER             NOT NULL    DEFAULT 0,
    dias_rodados    INTEGER             NOT NULL    DEFAULT 0,
    cod_turno       INTEGER             NOT NULL    DEFAULT 0,
    CONSTRAINT pk_transporte_escolar    PRIMARY KEY                  (exercicio, mes, cod_veiculo, cgm_escola),
    CONSTRAINT fk_transporte_escolar_1  FOREIGN KEY                  (mes)
                                        REFERENCES administracao.mes (cod_mes),
    CONSTRAINT fk_transporte_escolar_2  FOREIGN KEY                  (cod_veiculo)
                                        REFERENCES frota.veiculo     (cod_veiculo),
    CONSTRAINT fk_transporte_escolar_3  FOREIGN KEY                  (cgm_escola)
                                        REFERENCES frota.escola      (numcgm),
    CONSTRAINT fk_transporte_escolar_4  FOREIGN KEY                  (cod_turno)
                                        REFERENCES frota.turno       (cod_turno)
);
GRANT ALL ON frota.transporte_escolar TO urbem;










