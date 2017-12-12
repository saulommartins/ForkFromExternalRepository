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
* URBEM SoluÃ§Ãµes de GestÃ£o PÃºblica Ltda
* www.urbem.cnm.org.br
*
* $Id: GPC_1913.sql 59612 2014-09-02 12:00:51Z gelson $
*
* VersÃ£o 1.91.3
*/

----------------
-- Ticket #14103
----------------

ALTER TABLE tcmgo.contrato ADD COLUMN nro_processo           NUMERIC(5);
ALTER TABLE tcmgo.contrato ADD COLUMN ano_processo           CHAR(4);
ALTER TABLE tcmgo.contrato ADD COLUMN cod_sub_assunto        INTEGER;
ALTER TABLE tcmgo.contrato ADD COLUMN detalhamentoSubAssunto VARCHAR(200);

ALTER TABLE tcmgo.obra     ADD COLUMN bairro                 VARCHAR(40);

CREATE TABLE tcmgo.contrato_sub_assunto(
    cod_sub_assunto     INTEGER         NOT NULL,
    descricao           VARCHAR(255)    NOT NULL,
    CONSTRAINT pk_contrato_sub_assunto  PRIMARY KEY (cod_sub_assunto)
    
);

INSERT INTO tcmgo.contrato_sub_assunto VALUES(1 , 'Aquisição de Materiais para Obras e Serviços Específicos');
INSERT INTO tcmgo.contrato_sub_assunto VALUES(2 , 'Aquisição de Materiais para Almoxarifado/Manutenção'     );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(3 , 'Aquisição de Materiais para Central de Produção'         );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(11, 'Projeto'                                                 );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(12, 'Fiscalização'                                            );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(13, 'Consultoria'                                             );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(20, 'Edificação'                                              );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(30, 'Pavimentação'                                            );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(40, 'Meio-fio'                                                );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(50, 'Galeria de Águas Pluviais'                               );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(60, 'Ponte/Viaduto/Trincheira'                                );
INSERT INTO tcmgo.contrato_sub_assunto VALUES(99, 'Outros'                                                  );


ALTER TABLE tcmgo.contrato ADD CONSTRAINT fk_contrato_5 FOREIGN KEY                             (cod_sub_assunto)
                                                        REFERENCES tcmgo.contrato_sub_assunto   (cod_sub_assunto);

ALTER TABLE tcmgo.contrato ADD COLUMN dt_firmatura      DATE;
ALTER TABLE tcmgo.contrato ADD COLUMN dt_lancamento     DATE;
ALTER TABLE tcmgo.contrato ADD COLUMN vl_acrescimo      NUMERIC(12,2);
ALTER TABLE tcmgo.contrato ADD COLUMN vl_decrescimo     NUMERIC(12,2);
ALTER TABLE tcmgo.contrato ADD COLUMN vl_contratual     NUMERIC(12,2);
ALTER TABLE tcmgo.contrato ADD COLUMN dt_rescisao       DATE;
ALTER TABLE tcmgo.contrato ADD COLUMN vl_final_contrato NUMERIC(12,2);

ALTER TABLE tcmgo.contrato ADD COLUMN prazo             INTEGER;


----------------
-- Ticket #14100
----------------

ALTER TABLE tcmgo.obra ADD COLUMN grau_latitude     INTEGER;
ALTER TABLE tcmgo.obra ADD COLUMN minuto_latitude   INTEGER;
ALTER TABLE tcmgo.obra ADD COLUMN segundo_latitude  NUMERIC(4,2);

ALTER TABLE tcmgo.obra ADD COLUMN grau_longitude    INTEGER;
ALTER TABLE tcmgo.obra ADD COLUMN minuto_longitude  INTEGER;
ALTER TABLE tcmgo.obra ADD COLUMN segundo_longitude NUMERIC(4,2);

ALTER TABLE tcmgo.obra ADD COLUMN cod_unidade       INTEGER;
ALTER TABLE tcmgo.obra ADD COLUMN cod_grandeza      INTEGER;

ALTER TABLE tcmgo.obra ADD CONSTRAINT fk_obra_1     FOREIGN KEY                             (cod_unidade, cod_grandeza)
                                                    REFERENCES administracao.unidade_medida (cod_unidade, cod_grandeza);

ALTER TABLE tcmgo.obra ADD COLUMN quantidade        INTEGER;
ALTER TABLE tcmgo.obra ADD COLUMN endereco          VARCHAR(100);


----------------
-- Ticket #14230
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2430
          , 364
          , 'FMManterVinculoTipoVeiculo.php'
          , 'manter'
          , 26
          , ''
          , 'Configurar Tipo Veículo'
          );

CREATE TABLE tcmgo.tipo_veiculo_tcm (
    cod_tipo_tcm        INTEGER         NOT NULL,
    nom_tipo_tcm        VARCHAR(200)    NOT NULL,
    CONSTRAINT pk_tipo_veiculo_tcm      PRIMARY KEY     (cod_tipo_tcm)
);

GRANT ALL ON tcmgo.tipo_veiculo_tcm TO GROUP urbem;

INSERT INTO tcmgo.tipo_veiculo_tcm(cod_tipo_tcm, nom_tipo_tcm) VALUES (01, 'Aeronaves');
INSERT INTO tcmgo.tipo_veiculo_tcm(cod_tipo_tcm, nom_tipo_tcm) VALUES (02, 'Embarcações');
INSERT INTO tcmgo.tipo_veiculo_tcm(cod_tipo_tcm, nom_tipo_tcm) VALUES (03, 'Veículos');
INSERT INTO tcmgo.tipo_veiculo_tcm(cod_tipo_tcm, nom_tipo_tcm) VALUES (04, 'Maquinário');
INSERT INTO tcmgo.tipo_veiculo_tcm(cod_tipo_tcm, nom_tipo_tcm) VALUES (99, 'Outros');


CREATE TABLE tcmgo.subtipo_veiculo_tcm (
    cod_tipo_tcm        INTEGER         NOT NULL,
    cod_subtipo_tcm     INTEGER ,
    nom_subtipo_tcm     VARCHAR(200),
    CONSTRAINT pk_subtipo_veiculo_tcm   PRIMARY KEY                         (cod_subtipo_tcm, cod_tipo_tcm),
    CONSTRAINT fk_subtipo_veiculo_tcm_1 FOREIGN KEY                         (cod_tipo_tcm)
                                        REFERENCES tcmgo.tipo_veiculo_tcm   (cod_tipo_tcm)
);

GRANT ALL ON tcmgo.subtipo_veiculo_tcm TO GROUP urbem;

INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (01, 01, 'Aeronaves');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (02, 01, 'Embarcações');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (03, 01, 'Veículo de Passeio');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (03, 02, 'Utilitário (Camionete..)');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (03, 03, 'Ônibus');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (03, 04, 'Caminhão');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (03, 05, 'Motocicleta');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (03, 06, 'Van');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 01, 'Trator de Esteira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 02, 'Trator de Pneu');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 03, 'Motoniveladora');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 04, 'Pá-Carregadeira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 05, 'Retro Escavadeira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 06, 'Mini-Carregadeira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 07, 'Escavadeira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 08, 'Empilhadeira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (04, 09, 'Compactador');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 01, 'Gerador');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 02, 'Motobomba');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 03, 'Roçadeira');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 04, 'Motosserra');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 05, 'Pulverizador');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 06, 'Compactador de Mão');
INSERT INTO tcmgo.subtipo_veiculo_tcm(cod_tipo_tcm, cod_subtipo_tcm, nom_subtipo_tcm) VALUES (99, 07, 'Oficina');


CREATE TABLE tcmgo.tipo_veiculo_vinculo (
    cod_tipo_tcm        INTEGER             NOT NULL,
    cod_subtipo_tcm     INTEGER             NOT NULL,
    cod_tipo            INTEGER             NOT NULL,
    CONSTRAINT pk_tipo_veiculo_vinculo      PRIMARY KEY                             (cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_1    FOREIGN KEY                             (cod_tipo)
                                            REFERENCES frota.tipo_veiculo           (cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_2    FOREIGN KEY                             (cod_tipo_tcm)
                                            REFERENCES tcmgo.tipo_veiculo_tcm       (cod_tipo_tcm),
    CONSTRAINT fk_tipo_veiculo_vinculo_3    FOREIGN KEY                             (cod_subtipo_tcm, cod_tipo_tcm)
                                            REFERENCES tcmgo.subtipo_veiculo_tcm    (cod_subtipo_tcm, cod_tipo_tcm)
);

GRANT ALL ON tcmgo.tipo_veiculo_vinculo TO GROUP urbem;


----------------
-- Ticket #14323
----------------

INSERT
  INTO administracao.configuracao
     ( exercicio
     , cod_modulo
     , parametro
     , valor )
VALUES ( '2009'
     , 30
     , 'seta_tipo_documento_tcmgo'
     , 'true'
     );

CREATE TABLE tcmgo.tipo_documento (
    cod_tipo            INTEGER             NOT NULL,
    descricao           VARCHAR(35)         NOT NULL,
    CONSTRAINT pk_tipo_documento            PRIMARY KEY(cod_tipo)
);
GRANT ALL ON tcmgo.tipo_documento TO GROUP urbem;

CREATE TABLE tesouraria.pagamento_tipo_documento (
    cod_tipo_documento  INTEGER             NOT NULL,
    cod_entidade        INTEGER             NOT NULL,
    exercicio           VARCHAR(4)          NOT NULL,
    timestamp           TIMESTAMP           NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_nota            INTEGER             NOT NULL,
    CONSTRAINT pk_pagamento_tipo_documento  PRIMARY KEY                         (exercicio, cod_entidade, cod_nota, timestamp, cod_tipo_documento),
    CONSTRAINT fk_pagamento_tipo_documento_1 FOREIGN KEY                        (cod_entidade, exercicio, timestamp, cod_nota)
                                            REFERENCES tesouraria.pagamento     (cod_entidade, exercicio, timestamp, cod_nota),
    CONSTRAINT fk_pagamento_tipo_documento_2 FOREIGN KEY                        (cod_tipo_documento)
                                            REFERENCES tcmgo.tipo_documento     (cod_tipo)
);
GRANT ALL ON tesouraria.pagamento_tipo_documento TO GROUP urbem;

INSERT INTO TCMGO.TIPO_DOCUMENTO VALUES (1 ,'Cheque'  );
INSERT INTO TCMGO.TIPO_DOCUMENTO VALUES (2 ,'DOC'     );
INSERT INTO TCMGO.TIPO_DOCUMENTO VALUES (3 ,'TED'     );
INSERT INTO TCMGO.TIPO_DOCUMENTO VALUES (4 ,'Borderô' );
INSERT INTO TCMGO.TIPO_DOCUMENTO VALUES (5 ,'Dinheiro');
INSERT INTO TCMGO.TIPO_DOCUMENTO VALUES (99,'Outros'  ); 


----------------
-- Ticket #14356
----------------

CREATE TABLE tcmgo.tipo_combustivel (
    cod_tipo            INTEGER             NOT NULL,
    descricao           VARCHAR(20)         NOT NULL,
    CONSTRAINT pk_tcm_go_tipo_combustivel   PRIMARY KEY     (cod_tipo)
);
GRANT ALL ON tcmgo.tipo_combustivel TO GROUP urbem;

INSERT INTO tcmgo.tipo_combustivel VALUES (1,'Combustível' );
INSERT INTO tcmgo.tipo_combustivel VALUES (2,'Lubrificante');

CREATE TABLE tcmgo.combustivel (
    cod_combustivel     INTEGER             NOT NULL,
    cod_tipo            INTEGER             NOT NULL,
    descricao           VARCHAR(30)         NOT NULL,
    CONSTRAINT pk_tcmgo_combustivel         PRIMARY KEY                         (cod_combustivel, cod_tipo),
    CONSTRAINT fk_tcmgo_combustivel_1       FOREIGN KEY                         (cod_tipo)
                                            REFERENCES tcmgo.tipo_combustivel   (cod_tipo)
);
GRANT ALL ON tcmgo.combustivel TO GROUP urbem;

INSERT INTO tcmgo.combustivel VALUES(1,1,'Álcool (Litro)'           );
INSERT INTO tcmgo.combustivel VALUES(2,1,'Gasolina (Litro)'         );
INSERT INTO tcmgo.combustivel VALUES(3,1,'Gás Natural (M³)'         );
INSERT INTO tcmgo.combustivel VALUES(4,1,'Diesel (Litro)'           );
INSERT INTO tcmgo.combustivel VALUES(5,1,'Querosene (Litro)'        );
INSERT INTO tcmgo.combustivel VALUES(1,2,'Óleo Lubrificante (Litro)');
INSERT INTO tcmgo.combustivel VALUES(2,2,'Graxa (Quilograma)'       );

CREATE TABLE tcmgo.combustivel_vinculo (
    cod_combustivel     INTEGER                 NOT NULL,
    cod_tipo            INTEGER                 NOT NULL,
    cod_item            INTEGER                 NOT NULL,
    CONSTRAINT pk_tcmgo_combustivel_vinculo     PRIMARY KEY                     (cod_combustivel, cod_tipo, cod_item),
    CONSTRAINT fk_tcmgo_combustivel_vinculo_1   FOREIGN KEY                     (cod_combustivel, cod_tipo)
                                                REFERENCES tcmgo.combustivel    (cod_combustivel, cod_tipo),
    CONSTRAINT fk_tcmgo_combustivel_vinculo_2   FOREIGN KEY                     (cod_item)
                                                REFERENCES frota.item           (cod_item),
    CONSTRAINT uk_tcmgo_combustivel_vinculo_1   UNIQUE                          (cod_item)
);
GRANT ALL ON tcmgo.combustivel_vinculo TO GROUP urbem;

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2462
          , 364
          , 'FMManterCombustivel.php'
          , 'vincCombustivel'
          , 27
          , ''
          , 'Manter Combustível'
          );
          
          
--------------------------
-- CRIACAO DO SCHEMA tcemg
--------------------------

CREATE SCHEMA tcemg;

GRANT ALL ON SCHEMA tcemg TO GROUP urbem;


----------------
-- Ticket #14383
-- Ticket #14442
----------------

INSERT INTO administracao.modulo
          ( cod_modulo
          , cod_responsavel
          , nom_modulo
          , nom_diretorio
          , ordem
          , cod_gestao
          )
     VALUES ( 55
          , 0
          , 'TCE - MG'
          , 'TCEMG/'
          , 91
          , 6
          );

INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 451
          , 55
          , 'Configuração'
          , 'instancias/configuracao/'
          , 1
          );

INSERT INTO administracao.funcionalidade
          ( cod_funcionalidade
          , cod_modulo
          , nom_funcionalidade
          , nom_diretorio
          , ordem )
     VALUES ( 452
          , 55
          , 'Exportação SIACE - LRF/MG'
          , 'instancias/exportacao/'
          , 2
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2463
          , 452
          , 'FLExportarArquivos.php'
          , 'exportar_lrf'
          , 1
          , ''
          , 'Arquivos'
          );
          

----------------
-- Ticket #14530
----------------

DELETE FROM administracao.permissao WHERE cod_acao in (2173,2182);
DELETE FROM administracao.acao      WHERE cod_acao in (2173,2182); 



