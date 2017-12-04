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
* Versao 2.04.3
*
* Fabio Bertoldi - 20150901
*
*/

----------------
-- Ticket #23221
----------------

CREATE TABLE tcmba.tipo_documento_pagamento(
    cod_tipo        integer         NOT NULL,
    descricao       varchar(35)     NOT NULL,
    CONSTRAINT pk_tipo_documento_pagamento PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmba.tipo_documento_pagamento TO urbem;

INSERT INTO tcmba.tipo_documento_pagamento VALUES (1, 'Cheque');
INSERT INTO tcmba.tipo_documento_pagamento VALUES (2, 'Ordem' );
INSERT INTO tcmba.tipo_documento_pagamento VALUES (3, 'TED'   );
INSERT INTO tcmba.tipo_documento_pagamento VALUES (4, 'DOC'   );
INSERT INTO tcmba.tipo_documento_pagamento VALUES (5, 'Débito');


CREATE TABLE tcmba.pagamento_tipo_documento_pagamento(
    cod_tipo        integer     NOT NULL,
    cod_entidade    integer     NOT NULL,
    exercicio       varchar(4)  NOT NULL,
    timestamp       timestamp   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    cod_nota        integer     NOT NULL,
    num_documento   varchar(8),
    CONSTRAINT pk_pagamento_tipo_documento_pagamento    PRIMARY KEY                               (exercicio, cod_entidade, cod_nota, timestamp, cod_tipo),
    CONSTRAINT fk_pagamento_tipo_documento_pagamento_1  FOREIGN KEY                               (exercicio, cod_entidade, cod_nota, timestamp)
                                                        REFERENCES tesouraria.pagamento           (exercicio, cod_entidade, cod_nota, timestamp),
    CONSTRAINT fk_pagamento_tipo_documento_pagamento_2  FOREIGN KEY                               (cod_tipo)
                                                        REFERENCES tcmba.tipo_documento_pagamento (cod_tipo)
);
GRANT ALL ON tcmba.pagamento_tipo_documento_pagamento TO urbem;


----------------
-- Ticket #23250
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
     ( 3081
     , 390
     , 'FMManterConfiguracaoObrasServicos.php'
     , 'incluir'
     , 12
     , ''
     , 'Incluir Obras e Serviços de Engenharia'
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
     ( 3082
     , 390
     , 'FLManterConfiguracaoObrasServicos.php'
     , 'alterar'
     , 13
     , ''
     , 'Alterar Obras e Serviços de Engenharia'
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
     ( 3083
     , 390
     , 'FLManterConfiguracaoObrasServicos.php'
     , 'excluir'
     , 14
     , ''
     , 'Excluir Obras e Serviços de Engenharia'
     , TRUE
     );


----------------
-- Ticket #23250
----------------

CREATE TABLE tcmba.tipo_obra(
    cod_tipo    INTEGER     NOT NULL,
    descricao   VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tipo_obra PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmba.tipo_obra TO urbem;

INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 1, 'Projeto e consultoria'               );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 2, 'Avaliação/Perícia de engenharia'     );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 3, 'Ensaios e controle tecnológico'      );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 4, 'Serviços de manutenção'              );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 5, 'Fornecimento de material/equipamento');
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 6, 'Obra de construção/ampliação'        );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 7, 'Obra de reforma'                     );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 8, 'Outros serviços de engenharia'       );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES ( 9, 'Obra de Implantação'                 );
INSERT INTO tcmba.tipo_obra (cod_tipo, descricao) VALUES (10, 'Fornecimento de Mão de Obra'         );


CREATE TABLE tcmba.tipo_funcao_obra(
    cod_funcao  INTEGER             NOT NULL,
    nro_funcao  VARCHAR(5)          NOT NULL,
    descricao   VARCHAR(90)         NOT NULL,
    CONSTRAINT pk_tipo_funcao_obra  PRIMARY KEY (cod_funcao)
);
GRANT ALL ON tcmba.tipo_funcao_obra TO urbem;

INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 1, '11.10', 'Estação de Tratamento de Esgotos (ETE)'                     );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 2, '11.20', 'Emissário de esgotos'                                       );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 3, '11.30', 'Coletor-tronco de esgotos'                                  );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 4, '11.99', 'Outras obras ou serviços de esgotos sanitários'             );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 5, '12.10', 'Estação de Tratamento de Água (ETA)'                        );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 6, '12.20', 'Captação de água'                                           );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 7, '12.30', 'Reservatório de água'                                       );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 8, '12.40', 'Adutora de água'                                            );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES ( 9, '12.99', 'Outras obras ou serviços de abastecimento d''água'          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (10, '13.10', 'Aterros sanitários'                                         );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (11, '13.20', 'Usinas de lixo (reciclagem, compostagem, incineração, etc.)');
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (12, '13.30', 'Coleta e transporte de resíduos'                            );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (13, '13.40', 'Varrição e capina'                                          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (14, '13.99', 'Outras obras ou serviços de limpeza urbana'                 );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (15, '14.10', 'Galeria de águas pluviais'                                  );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (16, '14.20', 'Drenagem urbana'                                            );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (17, '14.99', 'Outras obras ou serviços de drenagem'                       );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (18, '19.99', 'Outras obras de saneamento básico'                          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (19, '21.10', 'Implantação de rodovias inter-municipais'                   );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (20, '21.20', 'Obras rodoviárias em vias urbanas'                          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (21, '21.30', 'Obras rodoviárias em área rural'                            );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (22, '29.99', 'Outras obras ou serviços viários'                           );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (23, '31.10', 'Requalificação urbana'                                      );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (24, '31.20', 'Paisagismo (Exclusivamente)'                                );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (25, '31.30', 'Loteamentos'                                                );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (26, '31.40', 'Parques temáticos ou empreendimentos turísticos'            );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (27, '31.99', 'Outras obras ou serviços de urbanização e paisagismo'       );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (28, '32.00', 'Infraestrutura urbana'                                      );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (29, '32.10', 'Iluminação pública'                                         );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (30, '32.20', 'Rede de telecomunicações'                                   );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (31, '32.30', 'Distribuição de gás canalizado'                             );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (32, '32.41', 'Distribuição de Energia Elétrica'                           );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (33, '32.99', 'Outras obras ou serviços de infraestrutura urbana'          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (34, '39.99', 'Outras obras de infraestrutura urbana e urbanização'        );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (35, '41.10', 'Habitação'                                                  );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (36, '41.20', 'Estabelecimento de ensino'                                  );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (37, '41.30', 'Estabelecimento de saúde'                                   );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (38, '41.40', 'Edificações para atividades de lazer e/ou esportes'         );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (39, '41.50', 'Terminais viários (rodoviário, hidroviário, etc.)'          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (40, '41.61', 'Edificações da administração direta (Poder Executivo)'      );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (41, '41.62', 'Edificações da administração direta (Poder Judiciário)'     );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (42, '41.63', 'Edificações da administração direta (Poder Legislativo)'    );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (43, '41.99', 'Edificações com outras funções'                             );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (44, '42.00', 'Portos, marinas e semelhantes'                              );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (45, '49.99', 'Outros tipos de edificações'                                );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (46, '51.10', 'Terraplenagem'                                              );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (47, '52.00', 'Construção de barragens, diques e assemelhados'             );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (48, '53.10', 'Pontes e viadutos'                                          );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (49, '53.20', 'Proteção de taludes e contenção de encostas'                );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (50, '53.30', 'Túneis'                                                     );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (51, '53.99', 'Outras obras-de-arte'                                       );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (52, '54.00', 'Prevenção e recuperação do meio ambiente'                   );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (53, '55.00', 'Geração e transmissão de energia elétrica'                  );
INSERT INTO tcmba.tipo_funcao_obra (cod_funcao, nro_funcao, descricao) VALUES (54, '56.00', 'Avaliações e Perícias'                                      );


CREATE TABLE tcmba.obra(
    cod_obra            INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    nro_obra            VARCHAR(10)     NOT NULL,
    local               VARCHAR(50)     NOT NULL,
    cep                 VARCHAR(8)      NOT NULL,
    cod_bairro          INTEGER         NOT NULL,
    cod_uf              INTEGER         NOT NULL,
    cod_municipio       INTEGER         NOT NULL,
    cod_funcao          INTEGER         NOT NULL,
    descricao           VARCHAR(255)    NOT NULL,
    vl_obra             NUMERIC(16,2)   NOT NULL,
    data_cadastro       DATE            NOT NULL,
    data_inicio         DATE            NOT NULL,
    data_aceite         DATE            NOT NULL,
    prazo               INTEGER         NOT NULL,
    data_recebimento    DATE            NOT NULL,
    cod_licitacao       INTEGER                 ,
    cod_modalidade      INTEGER                 ,
    exercicio_licitacao CHAR(4)                 ,
    CONSTRAINT pk_tcmba_obra            PRIMARY KEY                         (cod_obra, cod_entidade, exercicio, cod_tipo),
    CONSTRAINT fk_tcmba_obra_1          FOREIGN KEY                         (cod_tipo)
                                        REFERENCES tcmba.tipo_obra          (cod_tipo),
    CONSTRAINT fk_tcmba_obra_2          FOREIGN KEY                         (cod_licitacao, cod_modalidade, cod_entidade, exercicio_licitacao)
                                        REFERENCES licitacao.licitacao      (cod_licitacao, cod_modalidade, cod_entidade, exercicio),
    CONSTRAINT fk_tcmba_obra_3          FOREIGN KEY                         (cod_funcao)
                                        REFERENCES tcmba.tipo_funcao_obra   (cod_funcao),
    CONSTRAINT fk_tcmba_obra_4          FOREIGN KEY                         (cep)
                                        REFERENCES sw_cep                   (cep),
    CONSTRAINT fk_tcmba_obra_5          FOREIGN KEY                         (cod_bairro, cod_uf, cod_municipio)
                                        REFERENCES sw_bairro                (cod_bairro, cod_uf, cod_municipio),
    CONSTRAINT fk_tcmba_obra_6          FOREIGN KEY                         (cod_entidade, exercicio)
                                        REFERENCES orcamento.entidade       (cod_entidade, exercicio)
);
GRANT ALL ON tcmba.obra TO urbem;


CREATE TABLE tcmba.situacao_obra(
    cod_situacao    INTEGER     NOT NULL,
    descricao       VARCHAR(30) NOT NULL,
    CONSTRAINT pk_situacao_obra PRIMARY KEY (cod_situacao)
);
GRANT ALL ON tcmba.situacao_obra TO urbem;

INSERT INTO tcmba.situacao_obra (cod_situacao, descricao) VALUES (1, 'Em andamento'     );
INSERT INTO tcmba.situacao_obra (cod_situacao, descricao) VALUES (2, 'Não Iniciada'     );
INSERT INTO tcmba.situacao_obra (cod_situacao, descricao) VALUES (3, 'Paralisada'       );
INSERT INTO tcmba.situacao_obra (cod_situacao, descricao) VALUES (5, 'Concluída'        );
INSERT INTO tcmba.situacao_obra (cod_situacao, descricao) VALUES (6, 'Recbto Provisório');
INSERT INTO tcmba.situacao_obra (cod_situacao, descricao) VALUES (7, 'Recbto Definitivo');


CREATE TABLE tcmba.obra_andamento(
    cod_obra        INTEGER     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_tipo        INTEGER     NOT NULL,
    cod_situacao    INTEGER     NOT NULL,
    data_situacao   DATE        NOT NULL,
    justificativa   VARCHAR(255)        ,
    CONSTRAINT pk_tcmba_obra_andamento      PRIMARY KEY                     (cod_obra, cod_entidade, exercicio, cod_tipo, cod_situacao, data_situacao),
    CONSTRAINT fk_tcmba_obra_andamento_1    FOREIGN KEY                     (cod_obra, cod_entidade, exercicio, cod_tipo)
                                            REFERENCES tcmba.obra           (cod_obra, cod_entidade, exercicio, cod_tipo),
    CONSTRAINT fk_tcmba_obra_andamento_2    FOREIGN KEY                     (cod_situacao)
                                            REFERENCES tcmba.situacao_obra  (cod_situacao)
);
GRANT ALL ON tcmba.obra_andamento TO urbem;


CREATE TABLE tcmba.obra_fiscal(
    cod_obra                INTEGER     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    cod_tipo                INTEGER     NOT NULL,
    numcgm                  INTEGER     NOT NULL,
    matricula               VARCHAR(10)         ,
    registro_profissional   VARCHAR(16)         ,
    data_inicio             DATE        NOT NULL,
    data_final              DATE        NOT NULL,
    CONSTRAINT pk_tcmba_obra_fiscal     PRIMARY KEY             (cod_obra, cod_entidade, exercicio, cod_tipo, numcgm),
    CONSTRAINT fk_tcmba_obra_fiscal_1   FOREIGN KEY             (cod_obra, cod_entidade, exercicio, cod_tipo)
                                        REFERENCES tcmba.obra   (cod_obra, cod_entidade, exercicio, cod_tipo),
    CONSTRAINT fk_tcmba_obra_fiscal_2 FOREIGN KEY               (numcgm)
                                        REFERENCES sw_cgm       (numcgm)
);
GRANT ALL ON tcmba.obra_fiscal TO urbem;


CREATE TABLE tcmba.medidas_obra(
    cod_medida      INTEGER     NOT NULL,
    descricao       VARCHAR(20) NOT NULL,
    CONSTRAINT pk_medidas_obra  PRIMARY KEY (cod_medida)
);
GRANT ALL ON tcmba.medidas_obra TO urbem;

INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (1, 'km'     );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (2, 'kw'     );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (3, 'm²'     );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (4, 'm³'     );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (5, 'm³/s'   );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (6, 'hab'    );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (7, 'ton'    );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (8, 'ton/dia');
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (9, 'ton/km' );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (10, 'unid'  );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (11, 'm'     );
INSERT INTO tcmba.medidas_obra (cod_medida, descricao) VALUES (12, 'km²'   );


CREATE TABLE tcmba.obra_medicao(
    cod_obra            INTEGER         NOT NULL,
    cod_entidade        INTEGER         NOT NULL,
    exercicio           CHAR(4)         NOT NULL,
    cod_tipo            INTEGER         NOT NULL,
    cod_medicao         BIGINT          NOT NULL,
    cod_medida          INTEGER         NOT NULL,
    data_inicio         DATE            NOT NULL,
    data_final          DATE            NOT NULL,
    vl_medicao          NUMERIC(16,2)   NOT NULL,
    nro_nota_fiscal     VARCHAR(20)     NOT NULL,
    data_nota_fiscal    DATE            NOT NULL,
    numcgm              INTEGER         NOT NULL,
    data_medicao        DATE            NOT NULL,
    CONSTRAINT pk_tcmba_obra_medicao    PRIMARY KEY                     (cod_obra, cod_entidade, exercicio, cod_tipo, cod_medicao),
    CONSTRAINT fk_tcmba_obra_medicao_1  FOREIGN KEY                     (cod_obra, cod_entidade, exercicio, cod_tipo)
                                        REFERENCES tcmba.obra           (cod_obra, cod_entidade, exercicio, cod_tipo),
    CONSTRAINT fk_tcmba_obra_medicao_2  FOREIGN KEY                     (cod_medida)
                                        REFERENCES tcmba.medidas_obra   (cod_medida),
    CONSTRAINT fk_tcmba_obra_medicao_3  FOREIGN KEY                     (numcgm)
                                        REFERENCES sw_cgm_pessoa_fisica (numcgm)
);
GRANT ALL ON TABLE tcmba.obra_medicao TO urbem;


CREATE TABLE tcmba.tipo_contratacao_obra(
    cod_contratacao INTEGER             NOT NULL,
    descricao       VARCHAR(20)         NOT NULL,
    CONSTRAINT pk_tipo_contratacao_obra PRIMARY KEY (cod_contratacao)
);
GRANT ALL ON tcmba.tipo_contratacao_obra TO urbem;

INSERT INTO tcmba.tipo_contratacao_obra (cod_contratacao, descricao) VALUES (1, 'Contrato'         );
INSERT INTO tcmba.tipo_contratacao_obra (cod_contratacao, descricao) VALUES (2, 'Convênio'         );
INSERT INTO tcmba.tipo_contratacao_obra (cod_contratacao, descricao) VALUES (5, 'Outros'           );
INSERT INTO tcmba.tipo_contratacao_obra (cod_contratacao, descricao) VALUES (4, 'RPA'              );
INSERT INTO tcmba.tipo_contratacao_obra (cod_contratacao, descricao) VALUES (3, 'Termo de Parceria');


CREATE TABLE tcmba.obra_contratos(
    cod_obra integer NOT NULL,
    cod_entidade integer NOT NULL,
    exercicio character(4) NOT NULL,
    cod_tipo integer NOT NULL,
    cod_contratacao integer NOT NULL,
    nro_instrumento varchar(16),
    nro_contrato varchar(16),
    nro_convenio varchar(16),
    nro_parceria varchar(16),
    numcgm integer NOT NULL,
    funcao_cgm varchar(50) NOT NULL,
    data_inicio date NOT NULL,
    data_final date NOT NULL,
    lotacao varchar(50),
    CONSTRAINT pk_tcmba_obra_contratos      PRIMARY KEY                             (cod_obra, cod_entidade, exercicio, cod_tipo, cod_contratacao, numcgm),
    CONSTRAINT fk_tcmba_obra_contratos_1    FOREIGN KEY                             (cod_obra, cod_entidade, exercicio, cod_tipo)
                                            REFERENCES tcmba.obra                   (cod_obra, cod_entidade, exercicio, cod_tipo),
    CONSTRAINT fk_tcmba_obra_contratos_2    FOREIGN KEY                             (cod_contratacao)
                                            REFERENCES tcmba.tipo_contratacao_obra  (cod_contratacao),
    CONSTRAINT fk_tcmba_obra_contratos_3    FOREIGN KEY                             (numcgm)
                                            REFERENCES sw_cgm_pessoa_fisica         (numcgm)
);
GRANT ALL ON tcmba.obra_contratos TO urbem;


----------------
-- Ticket #23265
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
     ( 3085
     , 390
     , 'FLRelacionarAtosPessoal.php'
     , 'relacionar'
     , 16
     , ''
     , 'Relacionar Atos de Pessoal'
     , TRUE
     );

CREATE TABLE tcmba.tipo_ato_pessoal(
    cod_tipo    INTEGER             NOT NULL,
    descricao   VARCHAR(80)         NOT NULL,
    CONSTRAINT pk_tipo_ato_pessoal  PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcmba.tipo_ato_pessoal TO urbem;

INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES ( 9, 'Contratação por prazo determinado (Prorrogação)'                  );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (10, 'Contratação por prazo determinado (Renovação)'                    );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (11, 'Contratação p/ prazo determinado transf. em indet.(Antes CF/1988)');
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (16, 'Promoção de Nível'                                                );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (23, 'Exoneração de cargo em comissão'                                  );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (25, 'Disponibilidade'                                                  );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (29, 'Readmissão'                                                       );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (31, 'Readaptação'                                                      );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (32, 'Transferência do Município Mãe'                                   );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (34, 'Transferência para Município Emancipado'                          );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (40, 'Desistência de Aposentadoria / Reforma / Reserva'                 );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (42, 'Reavaliação de proventos de aposentadoria'                        );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (46, 'Reavaliação de proventos de pensão'                               );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (52, 'Licença sem vencimento'                                           );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (56, 'Afastamento por mandato eletivo'                                  );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (57, 'Auxílio Reclusão - Previdência Própria'                           );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (58, 'Licença para Atividade Política'                                  );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (59, 'Designação para Função Gratificada'                               );
INSERT INTO tcmba.tipo_ato_pessoal (cod_tipo, descricao) VALUES (60, 'Dispensa de Função Gratificada'                                   );


SELECT atualizarbanco('
CREATE TABLE pessoal.tcmba_assentamento_ato_pessoal(
    cod_assentamento    INTEGER     NOT NULL,
    cod_tipo_ato_pessoal    INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    CONSTRAINT pk_tcmba_assentamento_ato_pessoal    PRIMARY KEY               (cod_assentamento, cod_tipo_ato_pessoal, exercicio),
    CONSTRAINT fk_tcmba_assentamento_ato_pessoal_1  FOREIGN KEY                                 (cod_assentamento)
                                                    REFERENCES pessoal.assentamento_assentamento(cod_assentamento),
    CONSTRAINT fk_tcmba_assentamento_ato_pessoal_2  FOREIGN KEY                                 (cod_tipo_ato_pessoal)
                                                    REFERENCES tcmba.tipo_ato_pessoal           (cod_tipo)
);
');
SELECT atualizarbanco('
GRANT ALL ON pessoal.tcmba_assentamento_ato_pessoal TO urbem;
');


----------------
-- Ticket #22984
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
       FROM pg_proc
      WHERE proname     = 'fn_demonstrativo_consolidado_receita'
        AND pronargs    = 4
        AND proargtypes = '1043 1043 1043 1043'
          ;
    IF FOUND THEN
        DROP FUNCTION tcmba.fn_demonstrativo_consolidado_receita(varchar,varchar,varchar,varchar);
    END IF;
END;
$$ LANGUAGE 'plpgsql';
SELECT        manutencao();
DROP FUNCTION manutencao();

DROP TYPE fn_demonstrativo_consolidado_receita;

CREATE TYPE fn_demonstrativo_consolidado_receita
    AS (cod_estrutural              varchar,
        receita                     integer,
        recurso                     varchar,
        descricao                   varchar,
        valor_previsto              numeric,
        arrecadado_mes              numeric,
        arrecadado_ate_periodo      numeric,
        anulado_mes                 numeric,
        anulado_ate_periodo         numeric,
        valor_diferenca             numeric
    );


----------------
-- Ticket #23271
----------------

UPDATE administracao.acao SET ativo = FALSE WHERE cod_acao = 2007;
DROP TABLE tcmgo.balanco_apbaaaa_tipo_combustivel;


----------------
-- Ticket #20472
----------------

CREATE TYPE tcemg.siace_despesa_total_pessoal AS (
    cod_conta           VARCHAR,
    descricao           VARCHAR,
    cod_estrutural      VARCHAR,
    valor               NUMERIC
);


----------------
-- Ticket #23254
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
     ( 3089
     , 390
     , 'FLManterConfiguracaoParcSubvOSCIP.php'
     , 'configurar'
     , 17
     , 'Parceria/Subvenção/OSCIP.'
     , 'Configurar Termos de Parceria/Subvenção/OSCIP'
     , TRUE
     );


CREATE TABLE tcmba.termo_parceria (
    exercicio            VARCHAR(4)   NOT NULL,
    cod_entidade         INTEGER      NOT NULL,
    nro_processo         VARCHAR(16)  NOT NULL,
    dt_assinatura        DATE         NOT NULL,
    dt_publicacao        DATE         NOT NULL,
    imprensa_oficial     VARCHAR(50)  NOT NULL,
    dt_inicio            DATE         NOT NULL,
    dt_termino           DATE         NOT NULL,
    numcgm               INTEGER      NOT NULL,
    processo_licitatorio VARCHAR(36)          ,
    processo_dispensa    VARCHAR(16)          ,
    objeto               VARCHAR(400) NOT NULL,
    nro_processo_mj      VARCHAR(36)          ,
    dt_processo_mj       DATE                 ,
    dt_publicacao_mj     DATE                 ,
    vl_parceiro_publico  NUMERIC(14,2)        ,
    vl_termo_parceria    NUMERIC(14,2)        ,
    CONSTRAINT pk_tcmba_termo_parceria   PRIMARY KEY          (exercicio,cod_entidade,nro_processo),
    CONSTRAINT fk_tcmba_termo_parceria_1 FOREIGN KEY                      (exercicio, cod_entidade)
                                         REFERENCES orcamento.entidade    (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_termo_parceria_2 FOREIGN KEY                      (numcgm)
                                         REFERENCES sw_cgm_pessoa_juridica(numcgm)
    
);
GRANT ALL ON tcmba.termo_parceria TO urbem;

CREATE TABLE tcmba.termo_parceria_dotacao (
    exercicio              VARCHAR(4)    NOT NULL,
    cod_entidade           INTEGER       NOT NULL,
    nro_processo           VARCHAR(16)   NOT NULL,
    exercicio_despesa      VARCHAR(4)    NOT NULL,
    cod_despesa            INTEGER       NOT NULL,
    CONSTRAINT pk_tcmba_termo_parceria_dotacao   PRIMARY KEY                     (exercicio, cod_entidade, nro_processo, exercicio_despesa, cod_despesa),
    CONSTRAINT fk_tcmba_termo_parceria_dotacao_1 FOREIGN KEY                     (exercicio, cod_entidade, nro_processo)
                                                 REFERENCES tcmba.termo_parceria (exercicio, cod_entidade, nro_processo),
    CONSTRAINT fk_tcmba_termo_parceria_dotacao_2 FOREIGN KEY                     (exercicio_despesa, cod_despesa)
                                                 REFERENCES orcamento.despesa    (exercicio, cod_despesa)

);
GRANT ALL ON tcmba.termo_parceria_dotacao TO urbem;

CREATE TABLE tcmba.termo_parceria_prorrogacao (
    exercicio              VARCHAR(4)    NOT NULL,
    cod_entidade           INTEGER       NOT NULL,
    nro_processo           VARCHAR(16)   NOT NULL,
    nro_termo_aditivo      VARCHAR(36)   NOT NULL,
    exercicio_aditivo      VARCHAR(4)    NOT NULL,
    dt_prorrogacao         DATE          NOT NULL,
    dt_publicacao          DATE          NOT NULL,
    imprensa_oficial       VARCHAR(50)   NOT NULL,
    indicador_adimplemento BOOLEAN       NOT NULL DEFAULT FALSE,
    dt_inicio              DATE          NOT NULL,
    dt_termino             DATE          NOT NULL,
    vl_prorrogacao         NUMERIC(14,2) NOT NULL,
    CONSTRAINT pk_tcmba_termo_parceria_prorrogacao   PRIMARY KEY (exercicio, cod_entidade, nro_processo, nro_termo_aditivo, exercicio_aditivo),
    CONSTRAINT fk_tcmba_termo_parceria_prorrogacao_1 FOREIGN KEY                     (exercicio, cod_entidade, nro_processo)
                                                     REFERENCES tcmba.termo_parceria (exercicio, cod_entidade, nro_processo)
);
GRANT ALL ON tcmba.termo_parceria_prorrogacao TO urbem;
