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
* Fabio Bertoldi - 20151023
*
*/

----------------
-- Ticket #23324
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
     ( 3090
     , 390
     , 'FLRelacionarLotacoesOrgaos.php'
     , 'configurar'
     , 18
     , ''
     , 'Relacionar Lotações/Orgãos'
     , TRUE
     );

SELECT atualizarbanco('
CREATE TABLE pessoal.de_para_lotacao_orgao(
    cod_orgao       INTEGER         NOT NULL,
    num_orgao       INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    CONSTRAINT pk_de_para_lotacao_orgao     PRIMARY KEY                     (cod_orgao, num_orgao, exercicio),
    CONSTRAINT fk_de_para_lotacao_orgao_1   FOREIGN KEY                     (cod_orgao)
                                            REFERENCES organograma.orgao    (cod_orgao),
    CONSTRAINT fk_de_para_lotacao_orgao_2   FOREIGN KEY                     (num_orgao, exercicio)
                                            REFERENCES orcamento.orgao      (num_orgao, exercicio)
);
');

SELECT atualizarbanco('GRANT ALL ON pessoal.de_para_lotacao_orgao TO urbem;');


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
     ( 3091
     , 390
     , 'FLManterConfiguracaoProrrogacaoTermo.php'
     , 'configurar'
     , 19
     , 'Prorrogação de Termos de Parceria/Subvenção/OSCIP.'
     , 'Configurar Prorrogação de Termos'
     , TRUE
     );

----------------
-- Ticket #23322
----------------

CREATE TABLE tcmba.tipo_funcao_servidor (
    cod_tipo_funcao INTEGER         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_tipo_funcao_servidor  PRIMARY KEY (cod_tipo_funcao)
);
GRANT ALL ON tcmba.tipo_funcao_servidor TO urbem;

INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 1, 'Diretor'                              );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 2, 'Planejamento Escolar'                 );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 3, 'Inspeção Escolar'                     );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 4, 'Supervisão Escolar'                   );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 5, 'Orientação Educacional'               );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 6, 'Coordenação Pedagógica'               );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 7, 'Professor'                            );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 8, 'Outra função ligada à Educação'       );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 9, 'Vice - Diretor'                       );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 21, 'Profissional ligado a Saúde'         );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 92, 'Agente Político'                     );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 93, 'Outros Benefí­cios'                   );
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 97, 'Guarda Municipal/Assistência Militar');
INSERT INTO tcmba.tipo_funcao_servidor (cod_tipo_funcao, descricao) VALUES ( 98, 'Conselheiros'                        );


SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_cargo_servidor (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_tipo_funcao         INTEGER     NOT NULL,
    cod_cargo               INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_cargo_servidor    PRIMARY KEY                                 (exercicio, cod_entidade, cod_tipo_funcao, cod_cargo),
    CONSTRAINT fk_tcmba_cargo_servidor_1  FOREIGN KEY                                 (cod_tipo_funcao)
                                          REFERENCES tcmba.tipo_funcao_servidor       (cod_tipo_funcao),
    CONSTRAINT fk_tcmba_cargo_servidor_2  FOREIGN KEY                                 (cod_cargo)
                                          REFERENCES pessoal.cargo                    (cod_cargo),
    CONSTRAINT fk_tcmba_cargo_servidor_3  FOREIGN KEY                                 (exercicio, cod_entidade)
                                          REFERENCES orcamento.entidade               (exercicio, cod_entidade)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_cargo_servidor TO urbem;');

CREATE TABLE tcmba.tipo_funcao_servidor_temporario (
    cod_tipo_funcao INTEGER         NOT NULL,
    descricao       VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_tipo_funcao_servidor_temporario  PRIMARY KEY (cod_tipo_funcao)
);
GRANT ALL ON tcmba.tipo_funcao_servidor_temporario TO urbem;

INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   1, 'Técnico/Auxiliar da Área de Educação'        );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   2, 'Acupunturista'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   3, 'Administrador'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   4, 'Profissional da Área Jurí­dica'               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   5, 'Agente Administrativo'                       );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   6, 'Agente Comunitário de Saúde'                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   8, 'Analista de Sistemas'                        );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (   9, 'Arquiteto'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  10, 'Ascensorista'                                );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  15, 'Assistente Social'                           );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  16, 'Técnico/Auxiliar da Área de Saúde'           );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  17, 'Carpinteiro/Marceneiro'                      );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  19, 'Censor(a)'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  20, 'Chapista'                                    );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  28, 'Técnico/Auxiliar de Eletricista'             );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  29, 'Técnico/Auxiliar de Encanador'               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  30, 'Engenheiro Agrônomo'                         );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  31, 'Engenheiro Civil'                            );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  32, 'Entrevistador(a)'                            );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  34, 'Merendeira'                                  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  35, 'Motorista'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  36, 'Psicólogo(a)'                                );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  37, 'Psicopedagogo(a)'                            );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  38, 'Psiquiatra'                                  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  39, 'Químico(a)'                                  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  40, 'Recepcionista'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  41, 'Recreador'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  42, 'Regente de Fanfarra'                         );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  43, 'Salva-Vidas'                                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  44, 'Sanitarista'                                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  45, 'Servente'                                    );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  47, 'Técnico/Auxiliar da Área Financeira/Contábil');
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  48, 'Telefonista'                                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  49, 'Terapeuta Ocupacional'                       );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  50, 'Tesoureiro(a)'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  51, 'Topógrafo'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  52, 'Veterinário(a)'                              );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  59, 'Vigilante'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  60, 'Zelador(a)'                                  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  61, 'Zootecnista'                                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  62, 'Professor(a)'                                );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  63, 'Médico'                                      );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  64, 'Odontólogo(a)'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  65, 'Enfermeiro(a)'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  66, 'Profissional da Área de Contabilidade'       );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  67, 'Engenheiro Sanitarista'                      );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  68, 'Pedagogo(a)'                                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  70, 'Serviços Gerais'                             );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  71, 'Gari'                                        );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  72, 'Técnico/Auxiliar da Área Administrativa'     );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  73, 'Fisioterapeuta'                              );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  74, 'Bibliotecário(a)'                            );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  75, 'Biólogo(a)'                                  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  76, 'Farmacêutico(a)'                             );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  78, 'Abatedor de Animais'                         );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  79, 'Bombeiro'                                    );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  81, 'Biomédico(a)'                                );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  82, 'Bioquímico(a)'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  91, 'Pedreiro'                                    );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  92, 'Pintor'                                      );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  93, 'Soldador'                                    );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  94, 'Borracheiro'                                 );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  95, 'Brigadista'                                  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  96, 'Mecânico'                                    );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  98, 'Coveiro'                                     );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES (  99, 'Desenhista/Projetista'                       );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 101, 'Instrutor'                                   );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 102, 'Mestre/Técnico de Obras'                     );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 104, 'Nutricionista'                               );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 105, 'Profissional da Área de Comunicação'         );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 106, 'Operador de Máquinas e Equipamentos'         );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 108, 'Técnico/Auxiliar da Área Ambiental'          );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 109, 'Técnico/Auxiliar da Área Agrí­cola'           );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 110, 'Técnico/Auxiliar em Agrimensura'             );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 111, 'Técnico/Auxiliar da Área de Informática'     );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 112, 'Técnico/Auxiliar da Área Sanitária'          );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 113, 'Técnico/Auxiliar da Área Social e Desporto'  );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 114, 'Técnico/Auxiliar da Área Tributária'         );
INSERT INTO tcmba.tipo_funcao_servidor_temporario ( cod_tipo_funcao, descricao ) VALUES ( 115, 'Fonoaudiólogo(a)'                            );


SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_cargo_servidor_temporario (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_tipo_funcao         INTEGER     NOT NULL,
    cod_cargo               INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_cargo_servidor_temporario    PRIMARY KEY                                      (exercicio, cod_entidade, cod_tipo_funcao, cod_cargo),
    CONSTRAINT fk_tcmba_cargo_servidor_temporario_1  FOREIGN KEY                                      (cod_tipo_funcao)
                                                     REFERENCES tcmba.tipo_funcao_servidor_temporario (cod_tipo_funcao),
    CONSTRAINT fk_tcmba_cargo_servidor_temporario_2  FOREIGN KEY                                      (cod_cargo)
                                                     REFERENCES pessoal.cargo                         (cod_cargo),
    CONSTRAINT fk_tcmba_cargo_servidor_temporario_3  FOREIGN KEY                                      (exercicio, cod_entidade)
                                                     REFERENCES orcamento.entidade                    (exercicio, cod_entidade)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_cargo_servidor_temporario TO urbem;');


CREATE TABLE tcmba.tipo_fonte_recurso_servidor(
    cod_tipo_fonte   INTEGER         NOT NULL,
    descricao        VARCHAR(100)    NOT NULL,
    CONSTRAINT pk_tipo_fonte_recurso_servidor PRIMARY KEY (cod_tipo_fonte)
);
GRANT ALL ON tcmba.tipo_fonte_recurso_servidor TO urbem;

INSERT INTO tcmba.tipo_fonte_recurso_servidor VALUES(1, 'Educação - Aplicação direta 60% do Fundeb' );
INSERT INTO tcmba.tipo_fonte_recurso_servidor VALUES(2, 'Saúde - Ligado diretamente a saúde'        );
INSERT INTO tcmba.tipo_fonte_recurso_servidor VALUES(3, 'Educação - Aplicação direta 40% do Fundeb' );
INSERT INTO tcmba.tipo_fonte_recurso_servidor VALUES(5, 'Educação - Pago com recurso próprio'       );


CREATE TABLE tcmba.fonte_recurso_lotacao(
    cod_tipo_fonte  INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    cod_orgao       INTEGER     NOT NULL,
    CONSTRAINT pk_fonte_recurso_lotacao   PRIMARY KEY                                  (cod_tipo_fonte, exercicio, cod_entidade, cod_orgao),
    CONSTRAINT fk_fonte_recurso_lotacao_1 FOREIGN KEY                                  (cod_tipo_fonte)
                                          REFERENCES tcmba.tipo_fonte_recurso_servidor (cod_tipo_fonte),
    CONSTRAINT fk_fonte_recurso_lotacao_2 FOREIGN KEY                                  (exercicio, cod_entidade)
                                          REFERENCES orcamento.entidade                (exercicio, cod_entidade),
    CONSTRAINT fk_fonte_recurso_lotacao_3 FOREIGN KEY                                  (cod_orgao)
                                          REFERENCES organograma.orgao                 (cod_orgao)
);
GRANT ALL ON tcmba.fonte_recurso_lotacao TO urbem;

CREATE TABLE tcmba.fonte_recurso_local(
    cod_tipo_fonte  INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    cod_entidade    INTEGER     NOT NULL,
    cod_local       INTEGER     NOT NULL,
    CONSTRAINT pk_fonte_recurso_local   PRIMARY KEY                                  (cod_tipo_fonte, exercicio, cod_entidade, cod_local),
    CONSTRAINT fk_fonte_recurso_local_1 FOREIGN KEY                                  (cod_tipo_fonte)
                                        REFERENCES tcmba.tipo_fonte_recurso_servidor (cod_tipo_fonte),
    CONSTRAINT fk_fonte_recurso_local_2 FOREIGN KEY                                  (exercicio, cod_entidade)
                                        REFERENCES orcamento.entidade                (exercicio, cod_entidade),
    CONSTRAINT fk_fonte_recurso_local_3 FOREIGN KEY                                  (cod_local)
                                        REFERENCES organograma.local                 (cod_local)
);
GRANT ALL ON tcmba.fonte_recurso_local TO urbem;

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_salario_base (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_salario_base    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_salario_base_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                        REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_salario_base_2  FOREIGN KEY                                 (cod_evento)
                                        REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_salario_base TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_vantagens_salariais (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_vantagens_salariais    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_vantagens_salariais_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                               REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_vantagens_salariais_2  FOREIGN KEY                                 (cod_evento)
                                               REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_vantagens_salariais TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_gratificacao_funcao (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_gratificacao_funcao    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_gratificacao_funcao_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                               REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_gratificacao_funcao_2  FOREIGN KEY                                 (cod_evento)
                                               REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_gratificacao_funcao TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_salario_familia (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_salario_familia    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_salario_familia_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                           REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_salario_familia_2  FOREIGN KEY                                 (cod_evento)
                                           REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_salario_familia TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_salario_horas_extras (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_salario_horas_extras    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_salario_horas_extras_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                                REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_salario_horas_extras_2  FOREIGN KEY                                 (cod_evento)
                                                REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_salario_horas_extras TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_salario_descontos (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_salario_descontos    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_salario_descontos_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                             REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_salario_descontos_2  FOREIGN KEY                                 (cod_evento)
                                             REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_salario_descontos TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_plano_saude (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_plano_saude    PRIMARY KEY                                 (exercicio, cod_entidade, cod_evento),
    CONSTRAINT fk_tcmba_plano_saude_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                       REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_plano_saude_2  FOREIGN KEY                                 (cod_evento)
                                       REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_plano_saude TO urbem;');

SELECT atualizarbanco('
CREATE TABLE folhapagamento.tcmba_emprestimo_consignado (
    exercicio               CHAR(4)     NOT NULL,
    cod_entidade            INTEGER     NOT NULL,
    cod_banco               INTEGER     NOT NULL,
    cod_evento              INTEGER     NOT NULL,
    CONSTRAINT pk_tcmba_emprestimo_consignado    PRIMARY KEY                                 (exercicio, cod_entidade, cod_banco, cod_evento),
    CONSTRAINT fk_tcmba_emprestimo_consignado_1  FOREIGN KEY                                 (exercicio, cod_entidade)
                                                 REFERENCES orcamento.entidade               (exercicio, cod_entidade),
    CONSTRAINT fk_tcmba_emprestimo_consignado_2  FOREIGN KEY                                 (cod_banco)
                                                 REFERENCES monetario.banco                  (cod_banco),
    CONSTRAINT fk_tcmba_emprestimo_consignado_3  FOREIGN KEY                                 (cod_evento)
                                                 REFERENCES folhapagamento.evento            (cod_evento)
);');
SELECT atualizarbanco('GRANT ALL ON folhapagamento.tcmba_emprestimo_consignado TO urbem;');

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
     ( 391
     , 390
     , 'FMManterConfiguracaoTipoSalario.php'
     , 'configurar'
     , 20
     , ''
     , 'Configurar Tipos de Salários'
     , TRUE
     );


----------------
-- Ticket #22981
----------------

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE

BEGIN
    PERFORM 1
      FROM pg_type
     WHERE typname = 'tcmba.type_conscontrazao'
         ;
    IF NOT FOUND THEN
        CREATE TYPE tcmba.type_conscontrazao AS (
            cod_estrutural  VARCHAR,
            deb_ex_ant      NUMERIC,
            deb_mov_ant     NUMERIC,
            deb_mes         NUMERIC,
            deb_mov         NUMERIC,
            cred_ex_ant     NUMERIC,
            cred_mov_ant    NUMERIC,
            cred_mes        NUMERIC,
            cred_mov        NUMERIC,
            deb_ex          NUMERIC,
            cred_ex         NUMERIC
        );
    END IF;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();


----------------
-- Ticket #22979
----------------

CREATE TYPE tcmba_servidor_salario2 AS (
    cod_periodo_movimentacao        INTEGER
  , cod_servidor_pensionista        INTEGER
  , tipo                            INTEGER
  , cod_contrato                    INTEGER
  , num_orgao                       INTEGER
  , cod_tipo_cargo                  INTEGER
  , funcao_atual                    INTEGER
  , classe                          INTEGER
  , numcgm                          INTEGER
  , nom_cgm                         VARCHAR
  , cpf                             VARCHAR
  , matricula                       INTEGER
  , cod_cargo                       INTEGER
  , nro_dias                        INTEGER
  , horas_mensais                   INTEGER
  , cod_funcao_temporario           INTEGER
  , folha                           INTEGER
  , cod_previdencia                 INTEGER
  , salario_base                    NUMERIC
  , salario_vantagens               NUMERIC
  , salario_gratificacao            NUMERIC
  , salario_familia                 NUMERIC
  , salario_ferias                  NUMERIC
  , salario_horas_extra             NUMERIC
  , salario_decimo                  NUMERIC
  , salario_descontos               NUMERIC
  , desconto_irrf                   NUMERIC
  , desconto_irrf_decimo            NUMERIC
  , desconto_consignado_1           NUMERIC
  , cod_banco_1                     VARCHAR
  , desconto_consignado_2           NUMERIC
  , cod_banco_2                     VARCHAR
  , desconto_consignado_3           NUMERIC
  , cod_banco_3                     VARCHAR
  , desconto_previdencia            NUMERIC
  , desconto_irrf_ferias            NUMERIC
  , desconto_previdencia_decimo     NUMERIC
  , desconto_previdencia_ferias     NUMERIC
  , desconto_pensao                 NUMERIC
  , desconto_plano_saude            NUMERIC
  , salario_liquido                 NUMERIC
);

