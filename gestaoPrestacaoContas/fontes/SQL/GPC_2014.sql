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
* Versao 2.01.4
*
* Fabio Bertoldi - 20121220
*
*/

----------------
-- Ticket #19885
----------------

INSERT
  INTO administracao.modulo
  ( cod_modulo
  , cod_responsavel
  , nom_modulo
  , nom_diretorio
  , ordem
  , cod_gestao
  , ativo
  )
  VALUES
  ( 58
  , 0
  , 'Transparência'
  , 'transparencia/'
  , 93
  , 6
  , TRUE
  );

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
  ( 473
  , 58
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
  ( 2849
  , 473
  , 'FMConfiguracaoTransparencia.php'
  , 'alterar'
  , 1
  , ''
  , 'Alterar Configuração '
  , TRUE
  );

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
  ( 474
  , 58
  , 'Exportação'
  , 'instancias/exportacao/'
  , 2
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
  ( 2850
  , 474
  , 'FMExportarTransparencia.php'
  , 'exportar'
  , 1
  , ''
  , 'Exportar Arquivos '
  , TRUE
  );


----------------
-- Ticket #19755
----------------

CREATE SCHEMA manad;



CREATE TABLE manad.modelo_lrf (
    exercicio               CHAR(4)         NOT NULL,
    cod_modelo              INTEGER         NOT NULL,
    nom_modelo              VARCHAR(80)     NOT NULL,
    nom_modelo_orcamento    VARCHAR(100)            ,
    CONSTRAINT pk_modelo_lrf  PRIMARY KEY (exercicio, cod_modelo)
);
GRANT ALL ON TABLE manad.modelo_lrf TO siamweb;

INSERT INTO manad.modelo_lrf VALUES ('2005',  1, 'Modelo 1 - Demonstrativo da Receita Corrente Líquida'               , 'Demonstrativo da Receita Corrente Líquida'         );
INSERT INTO manad.modelo_lrf VALUES ('2005',  2, 'Modelo 2 - Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal'              );
INSERT INTO manad.modelo_lrf VALUES ('2005',  3, 'Modelo 3 - Demonstrativo da Disponibilidade de Caixa'               , 'Demonstrativo da Disponibilidade de Caixa'         );
INSERT INTO manad.modelo_lrf VALUES ('2005',  4, 'Modelo 4 - Demonstrativo da Dívida Consolidada Líquida-DCL'         , 'Demonstrativo da Dívida Consolidada Líquida'       );
INSERT INTO manad.modelo_lrf VALUES ('2005',  5, 'Modelo 5 - Demonstrativo das Garantias e Contragarantias de Valores', 'Demonstrativo das Garantias e Contragarantias'     );
INSERT INTO manad.modelo_lrf VALUES ('2005',  6, 'Modelo 6 - Demonstrativo das Operações de Crédito'                  , 'Demonstrativo das Operações de Crédito'            );
INSERT INTO manad.modelo_lrf VALUES ('2005',  9, 'Modelo 9 - Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Executivo'             );
INSERT INTO manad.modelo_lrf VALUES ('2005', 10, 'Modelo 10- Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal - Legislativo');
INSERT INTO manad.modelo_lrf VALUES ('2005', 13, 'Modelo 13- Demonstrativo dos Gastos Totais'                         , NULL                                                );
INSERT INTO manad.modelo_lrf VALUES ('2005', 14, 'Modelo 14- Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Legislativo'           );
INSERT INTO manad.modelo_lrf VALUES ('2006',  1, 'Modelo 1 - Demonstrativo da Receita Corrente Líquida'               , 'Demonstrativo da Receita Corrente Líquida'         );
INSERT INTO manad.modelo_lrf VALUES ('2006',  2, 'Modelo 2 - Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal'              );
INSERT INTO manad.modelo_lrf VALUES ('2006',  3, 'Modelo 3 - Demonstrativo da Disponibilidade de Caixa'               , 'Demonstrativo da Disponibilidade de Caixa'         );
INSERT INTO manad.modelo_lrf VALUES ('2006',  4, 'Modelo 4 - Demonstrativo da Dívida Consolidada Líquida-DCL'         , 'Demonstrativo da Dívida Consolidada Líquida'       );
INSERT INTO manad.modelo_lrf VALUES ('2006',  5, 'Modelo 5 - Demonstrativo das Garantias e Contragarantias de Valores', 'Demonstrativo das Garantias e Contragarantias'     );
INSERT INTO manad.modelo_lrf VALUES ('2006',  6, 'Modelo 6 - Demonstrativo das Operações de Crédito'                  , 'Demonstrativo das Operações de Crédito'            );
INSERT INTO manad.modelo_lrf VALUES ('2006',  9, 'Modelo 9 - Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Executivo'             );
INSERT INTO manad.modelo_lrf VALUES ('2006', 10, 'Modelo 10- Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal - Legislativo');
INSERT INTO manad.modelo_lrf VALUES ('2006', 14, 'Modelo 14- Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Legislativo'           );
INSERT INTO manad.modelo_lrf VALUES ('2006', 13, 'Modelo 13- Demonstrativo dos Gastos Totais'                         , NULL                                                );
INSERT INTO manad.modelo_lrf VALUES ('2007',  1, 'Modelo 1 - Demonstrativo da Receita Corrente Líquida'               , 'Demonstrativo da Receita Corrente Líquida'         );
INSERT INTO manad.modelo_lrf VALUES ('2007',  2, 'Modelo 2 - Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal'              );
INSERT INTO manad.modelo_lrf VALUES ('2007',  3, 'Modelo 3 - Demonstrativo da Disponibilidade de Caixa'               , 'Demonstrativo da Disponibilidade de Caixa'         );
INSERT INTO manad.modelo_lrf VALUES ('2007',  4, 'Modelo 4 - Demonstrativo da Dívida Consolidada Líquida-DCL'         , 'Demonstrativo da Dívida Consolidada Líquida'       );
INSERT INTO manad.modelo_lrf VALUES ('2007',  5, 'Modelo 5 - Demonstrativo das Garantias e Contragarantias de Valores', 'Demonstrativo das Garantias e Contragarantias'     );
INSERT INTO manad.modelo_lrf VALUES ('2007',  6, 'Modelo 6 - Demonstrativo das Operações de Crédito'                  , 'Demonstrativo das Operações de Crédito'            );
INSERT INTO manad.modelo_lrf VALUES ('2007',  9, 'Modelo 9 - Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Executivo'             );
INSERT INTO manad.modelo_lrf VALUES ('2007', 10, 'Modelo 10- Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal - Legislativo');
INSERT INTO manad.modelo_lrf VALUES ('2007', 13, 'Modelo 13- Demonstrativo dos Gastos Totais'                         , NULL                                                );
INSERT INTO manad.modelo_lrf VALUES ('2007', 14, 'Modelo 14- Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Legislativo'           );
INSERT INTO manad.modelo_lrf VALUES ('2008',  1, 'Modelo 1 - Demonstrativo da Receita Corrente Líquida'               , 'Demonstrativo da Receita Corrente Líquida'         );
INSERT INTO manad.modelo_lrf VALUES ('2008',  2, 'Modelo 2 - Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal'              );
INSERT INTO manad.modelo_lrf VALUES ('2008',  3, 'Modelo 3 - Demonstrativo da Disponibilidade de Caixa'               , 'Demonstrativo da Disponibilidade de Caixa'         );
INSERT INTO manad.modelo_lrf VALUES ('2008',  4, 'Modelo 4 - Demonstrativo da Dívida Consolidada Líquida-DCL'         , 'Demonstrativo da Dívida Consolidada Líquida'       );
INSERT INTO manad.modelo_lrf VALUES ('2008',  5, 'Modelo 5 - Demonstrativo das Garantias e Contragarantias de Valores', 'Demonstrativo das Garantias e Contragarantias'     );
INSERT INTO manad.modelo_lrf VALUES ('2008',  6, 'Modelo 6 - Demonstrativo das Operações de Crédito'                  , 'Demonstrativo das Operações de Crédito'            );
INSERT INTO manad.modelo_lrf VALUES ('2008',  9, 'Modelo 9 - Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Executivo'             );
INSERT INTO manad.modelo_lrf VALUES ('2008', 10, 'Modelo 10- Demonstrativo da Despesa com Pessoal'                    , 'Demonstrativo da Despesa com Pessoal - Legislativo');
INSERT INTO manad.modelo_lrf VALUES ('2008', 13, 'Modelo 13- Demonstrativo dos Gastos Totais'                         , NULL                                                );
INSERT INTO manad.modelo_lrf VALUES ('2008', 14, 'Modelo 14- Demonstrativo dos Limites'                               , 'Demonstrativo dos Limites - Legislativo'           );



CREATE TABLE manad.quadro_modelo_lrf (
    exercicio       CHAR(4)     NOT NULL,
    cod_modelo      INTEGER     NOT NULL,
    cod_quadro      INTEGER     NOT NULL,
    nom_quadro      VARCHAR(80) NOT NULL,
    CONSTRAINT pk_quadro_modelo_lrf     PRIMARY KEY                 (exercicio, cod_quadro, cod_modelo),
    CONSTRAINT fk_quadro_modelo_lrf_1   FOREIGN KEY                 (exercicio, cod_modelo)
                                        REFERENCES manad.modelo_lrf (exercicio, cod_modelo)
);
GRANT ALL ON TABLE manad.quadro_modelo_lrf TO siamweb;

INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  1, 1, 'Receitas Correntes'                                              );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  1, 2, 'Deduções'                                                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  2, 1, 'Despesa com Pessoal Ativo/Inativo da Entidade'                   );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  3, 1, 'Executivo/Legislativo e Indiretas Municipais'                    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  3, 2, 'Regime Próprio de Previdência Social do Servidor - RPPS'         );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  4, 1, 'Dívida Consolidada ou Fundada'                                   );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  4, 2, 'Deduções'                                                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  4, 4, 'Outras Obrigações não Integrantes da Dívida Consolidada Líquida' );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  5, 1, 'Garantias'                                                       );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  5, 2, 'Contragarantias'                                                 );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  6, 1, 'Operações de Crédito Internas e Externas'                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005',  6, 2, 'Operações de Crédito por Antecipação da Receita Orçamentária'    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005', 10, 1, 'Despesa com Pessoal Ativo/Inativo do Poder Legislativo Municipal');
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005', 13, 1, 'Receita Efetivamente Realizada no Exercício Anterior'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005', 13, 2, 'Demonstrativo dos Gastos Totais do Poder Legislativo'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2005', 13, 3, 'Folha de Pagamento do Legislativo Municipal'                     );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  1, 1, 'Receitas Correntes'                                              );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  1, 2, 'Deduções'                                                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  2, 1, 'Despesa com Pessoal Ativo/Inativo da Entidade'                   );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  3, 1, 'Executivo/Legislativo e Indiretas Municipais'                    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  3, 2, 'Regime Próprio de Previdência Social do Servidor - RPPS'         );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  4, 1, 'Dívida Consolidada ou Fundada'                                   );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  4, 2, 'Deduções'                                                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  4, 4, 'Outras Obrigações não Integrantes da Dívida Consolidada Líquida' );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  5, 1, 'Garantias'                                                       );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  5, 2, 'Contragarantias'                                                 );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  6, 1, 'Operações de Crédito Internas e Externas'                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006',  6, 2, 'Operações de Crédito por Antecipação da Receita Orçamentária'    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006', 10, 1, 'Despesa com Pessoal Ativo/Inativo do Poder Legislativo Municipal');
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006', 13, 1, 'Receita Efetivamente Realizada no Exercício Anterior'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006', 13, 2, 'Demonstrativo dos Gastos Totais do Poder Legislativo'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2006', 13, 3, 'Folha de Pagamento do Legislativo Municipal'                     );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  3, 1, 'Executivo/Legislativo e Indiretas Municipais'                    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  3, 2, 'Regime Próprio de Previdência Social do Servidor - RPPS'         );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  4, 1, 'Dívida Consolidada ou Fundada'                                   );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  4, 2, 'Deduções'                                                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  5, 1, 'Garantias'                                                       );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  5, 2, 'Contragarantias'                                                 );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  6, 1, 'Operações de Crédito Internas e Externas'                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007',  6, 2, 'Operações de Crédito por Antecipação da Receita Orçamentária'    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007', 10, 1, 'Despesa com Pessoal Ativo/Inativo do Poder Legislativo Municipal');
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007', 13, 1, 'Receita Efetivamente Realizada no Exercício Anterior'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007', 13, 2, 'Demonstrativo dos Gastos Totais do Poder Legislativo'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2007', 13, 3, 'Folha de Pagamento do Legislativo Municipal'                     );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  3, 1, 'Executivo/Legislativo e Indiretas Municipais'                    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  3, 2, 'Regime Próprio de Previdência Social do Servidor - RPPS'         );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  4, 1, 'Dívida Consolidada ou Fundada'                                   );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  4, 2, 'Deduções'                                                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  5, 1, 'Garantias'                                                       );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  5, 2, 'Contragarantias'                                                 );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  6, 1, 'Operações de Crédito Internas e Externas'                        );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008',  6, 2, 'Operações de Crédito por Antecipação da Receita Orçamentária'    );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008', 10, 1, 'Despesa com Pessoal Ativo/Inativo do Poder Legislativo Municipal');
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008', 13, 1, 'Receita Efetivamente Realizada no Exercício Anterior'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008', 13, 2, 'Demonstrativo dos Gastos Totais do Poder Legislativo'            );
INSERT INTO manad.quadro_modelo_lrf VALUES ('2008', 13, 3, 'Folha de Pagamento do Legislativo Municipal'                     );



CREATE TABLE manad.plano_conta_modelo_lrf (
    exercicio       CHAR(4)     NOT NULL,
    cod_modelo      INTEGER     NOT NULL,
    cod_conta       INTEGER     NOT NULL,
    cod_quadro      INTEGER     NOT NULL,
    redutora        BOOLEAN     NOT NULL,
    ordem           INTEGER     NOT NULL,
    CONSTRAINT pk_plano_conta_modelo_lrf    PRIMARY KEY                         (exercicio, cod_modelo, cod_conta, cod_quadro),
    CONSTRAINT fk_plano_conta_modelo_lrf_1  FOREIGN KEY                         (exercicio, cod_conta)
                                            REFERENCES contabilidade.plano_conta(exercicio, cod_conta),
    CONSTRAINT fk_plano_conta_modelo_lrf_2  FOREIGN KEY                         (exercicio, cod_quadro, cod_modelo)
                                            REFERENCES manad.quadro_modelo_lrf  (exercicio, cod_quadro, cod_modelo)
);
GRANT ALL ON TABLE manad.plano_conta_modelo_lrf TO siamweb;



CREATE TABLE manad.ajuste_plano_conta_modelo_lrf (
    exercicio       CHAR(4)         NOT NULL,
    cod_modelo      INTEGER         NOT NULL,
    cod_conta       INTEGER         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    cod_quadro      INTEGER         NOT NULL,
    mes             INTEGER         NOT NULL,
    vl_ajuste       NUMERIC(14,2)   NOT NULL,
    CONSTRAINT pk_ajuste_plano_conta_modelo_lrf     PRIMARY KEY                             (exercicio, cod_modelo, cod_conta, cod_entidade, cod_quadro, mes),
    CONSTRAINT fk_ajuste_plano_conta_modelo_lrf_1   FOREIGN KEY                             (exercicio, cod_modelo, cod_conta, cod_quadro)
                                                    REFERENCES manad.plano_conta_modelo_lrf (exercicio, cod_modelo, cod_conta, cod_quadro),
    CONSTRAINT fk_ajuste_plano_conta_modelo_lrf_2   FOREIGN KEY                             (exercicio, cod_entidade)
                                                    REFERENCES orcamento.entidade           (exercicio, cod_entidade)
);
GRANT ALL ON TABLE manad.ajuste_plano_conta_modelo_lrf TO siamweb;



CREATE TABLE manad.rd_extra (
    cod_conta           INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    classificacao       INTEGER     NOT NULL,
    CONSTRAINT pk_rd_extra          PRIMARY KEY                             (cod_conta, exercicio),
    CONSTRAINT fk_rd_extra_1        FOREIGN KEY                             (cod_conta, exercicio)
                                    REFERENCES contabilidade.plano_conta    (cod_conta, exercicio)
);
GRANT ALL ON TABLE manad.rd_extra TO siamweb;



CREATE TABLE manad.recurso_modelo_lrf (
    exercicio       CHAR(4)     NOT NULL,
    cod_modelo      INTEGER     NOT NULL,
    cod_quadro      INTEGER     NOT NULL,
    cod_recurso     INTEGER     NOT NULL,
    ordem           INTEGER     NOT NULL,
    CONSTRAINT pk_recurso_modelo_lrf    PRIMARY KEY                         (exercicio, cod_modelo, cod_recurso, cod_quadro),
    CONSTRAINT fk_recurso_modelo_lrf_1  FOREIGN KEY                         (exercicio, cod_quadro, cod_modelo)
                                        REFERENCES manad.quadro_modelo_lrf  (exercicio, cod_quadro, cod_modelo),
    CONSTRAINT fk_recurso_modelo_lrf_2  FOREIGN KEY                         (exercicio, cod_recurso)
                                        REFERENCES orcamento.recurso        (exercicio, cod_recurso)
);
GRANT ALL ON TABLE manad.recurso_modelo_lrf TO siamweb;



CREATE TABLE manad.ajuste_recurso_modelo_lrf (
    exercicio           CHAR(4)     NOT NULL,
    cod_modelo          INTEGER     NOT NULL,
    cod_quadro          INTEGER     NOT NULL,
    cod_recurso         INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    mes                 INTEGER     NOT NULL,
    CONSTRAINT pk_ajuste_recurso_modelo_lrf     PRIMARY KEY                         (exercicio, cod_modelo, cod_recurso, cod_quadro, cod_entidade, mes),
    CONSTRAINT fk_ajuste_recurso_modelo_lrf_1   FOREIGN KEY                         (exercicio, cod_modelo, cod_recurso, cod_quadro)
                                                REFERENCES manad.recurso_modelo_lrf (exercicio, cod_modelo, cod_recurso, cod_quadro),
    CONSTRAINT fk_ajuste_recurso_modelo_lrf_2   FOREIGN KEY                         (exercicio, cod_entidade)
                                                REFERENCES orcamento.entidade       (exercicio, cod_entidade)
);
GRANT ALL ON TABLE manad.ajuste_recurso_modelo_lrf TO siamweb;



CREATE TABLE manad.plano_conta_entidade (
    exercicio           CHAR(4)     NOT NULL,
    cod_conta           INTEGER     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    CONSTRAINT pk_plano_conta_entidade      PRIMARY KEY                             (exercicio, cod_conta),
    CONSTRAINT fk_plano_conta_entidade_1    FOREIGN KEY                             (exercicio, cod_conta)
                                            REFERENCES contabilidade.plano_conta    (exercicio, cod_conta),
    CONSTRAINT fk_plano_conta_entidade_2    FOREIGN KEY                             (exercicio, cod_entidade)
                                            REFERENCES orcamento.entidade           (exercicio, cod_entidade)
);
GRANT ALL ON TABLE manad.plano_conta_entidade TO siamweb;



CREATE TABLE manad.carac_peculiar_receita (
    cod_caracteristica  INTEGER         NOT NULL,
    descricao           VARCHAR(140)    NOT NULL,
    CONSTRAINT pk_carac_peculiar_receita PRIMARY KEY (cod_caracteristica)
);
GRANT ALL ON TABLE manad.carac_peculiar_receita TO siamweb;

INSERT INTO manad.carac_peculiar_receita VALUES (  0, 'Não se Aplica'                             );
INSERT INTO manad.carac_peculiar_receita VALUES (101, 'Renúncia de Receita'                       );
INSERT INTO manad.carac_peculiar_receita VALUES (102, 'Restituição de Receita'                    );
INSERT INTO manad.carac_peculiar_receita VALUES (103, 'Desconto Concedido'                        );
INSERT INTO manad.carac_peculiar_receita VALUES (105, 'Dedução de Receita para formação do FUNDEB');
INSERT INTO manad.carac_peculiar_receita VALUES (106, 'Compensação'                               );
INSERT INTO manad.carac_peculiar_receita VALUES (108, 'Retificações'                              );
INSERT INTO manad.carac_peculiar_receita VALUES (109, 'Outras Deduções'                           );



CREATE TABLE manad.receita_carac_peculiar_receita (
    cod_receita         INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_caracteristica  INTEGER     NOT NULL,
    CONSTRAINT pk_receita_carac_peculiar_receita    PRIMARY KEY                             (cod_receita, exercicio),
    CONSTRAINT fk_receita_carac_peculiar_receita_1  FOREIGN KEY                             (exercicio, cod_receita)
                                                    REFERENCES orcamento.receita            (exercicio, cod_receita),
    CONSTRAINT fk_receita_carac_peculiar_receita_2  FOREIGN KEY                             (cod_caracteristica)
                                                    REFERENCES manad.carac_peculiar_receita (cod_caracteristica)
);
GRANT ALL ON TABLE manad.receita_carac_peculiar_receita TO siamweb;



CREATE TABLE manad.credor (
    exercicio       CHAR(4)     NOT NULL,
    numcgm          INTEGER     NOT NULL,
    tipo            INTEGER     NOT NULL,
    CONSTRAINT pk_credor        PRIMARY KEY         (exercicio, numcgm),
    CONSTRAINT fk_credor_1      FOREIGN KEY         (numcgm)
                                REFERENCES sw_cgm   (numcgm)
);
GRANT ALL ON TABLE manad.credor TO siamweb;



CREATE TABLE manad.uniorcam (
    numcgm          INTEGER     NOT NULL,
    exercicio       CHAR(4)     NOT NULL,
    num_unidade     INTEGER     NOT NULL,
    num_orgao       INTEGER     NOT NULL,
    identificador   INTEGER     NOT NULL,
    CONSTRAINT pk_uniorcam      PRIMARY KEY                         (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_uniorcam_1    FOREIGN KEY                         (numcgm)
                                REFERENCES sw_cgm_pessoa_juridica   (numcgm)
);
GRANT ALL ON TABLE manad.uniorcam TO siamweb;

