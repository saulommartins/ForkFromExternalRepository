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
* Versao 2.02.1
*
* Fabio Bertoldi - 20130730
*
*/

----------------
-- Ticket #20463
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
     ( 2895
     , 451
     , 'FLGestaoFiscalMedidas.php'
     , 'medidas'
     , 1
     , 'Indicação das Medidas Adotadas ou a Adotar'
     , 'Gestão Fiscal - Medidas'
     , TRUE
     );

CREATE TABLE administracao.poder_publico (
    cod_poder       INTEGER         NOT NULL,
    nome            VARCHAR(25)     NOT NULL,
    CONSTRAINT pk_poder_publico     PRIMARY KEY (cod_poder)
);
GRANT ALL ON administracao.poder_publico TO urbem;

INSERT INTO administracao.poder_publico VALUES (1, 'Executivo'  );
INSERT INTO administracao.poder_publico VALUES (2, 'Judiciário' );
INSERT INTO administracao.poder_publico VALUES (3, 'Legislativo');

CREATE TABLE tcemg.medidas (
    cod_medida          INTEGER     NOT NULL,
    cod_poder           INTEGER     NOT NULL,
    cod_mes             INTEGER     NOT NULL,
    descricao           TEXT        NOT NULL,
    riscos_fiscais      BOOLEAN             ,
    metas_fiscais       BOOLEAN             ,
    contratacao_aro     BOOLEAN             ,
    CONSTRAINT pk_medidas   PRIMARY KEY                            (cod_medida),
    CONSTRAINT fk_medidas_1 FOREIGN KEY                            (cod_poder)
                            REFERENCES administracao.poder_publico (cod_poder),
    CONSTRAINT fk_medidas_2 FOREIGN KEY                            (cod_mes)
                            REFERENCES administracao.mes           (cod_mes)
);
GRANT ALL ON tcemg.medidas TO urbem;


----------------
-- Ticket #20506
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
     ( 2901
     , 451
     , 'FLConsideracaoExecucaoVariacao.php'
     , 'consideracao'
     , 2
     , 'Configuração para Consideração-Execução Variação'
     , 'Consideração-Execução Variação'
     , TRUE
     );

CREATE TABLE tcemg.execucao_variacao (
    cod_mes             INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cons_adm_dir        TEXT        NOT NULL,
    cons_aut            TEXT        NOT NULL,
    cons_fund           TEXT        NOT NULL,
    cons_empe_est_dep   TEXT        NOT NULL,
    cons_dem_ent        TEXT        NOT NULL,
    CONSTRAINT pk_execucao_variacao     PRIMARY KEY                        (cod_mes, exercicio),
    CONSTRAINT fk_execucao_variacao_1   FOREIGN KEY                        (cod_mes)
                                        REFERENCES administracao.mes       (cod_mes)
);    
GRANT ALL ON tcemg.execucao_variacao TO urbem;


----------------
-- Ticket #20502
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
     ( 2904
     , 451
     , 'FLObsMetaArrecadacao.php'
     , 'incluir'
     , 3
     , 'Observações sobre metas bimestrais de arrecadação do anexo 14'
     , 'Obs Meta de Arrecadação'
     , TRUE
     );


CREATE TABLE tcemg.obs_meta_arrecadacao (
    mes             INTEGER         NOT NULL,
    exercicio       VARCHAR(100)    NOT NULL,
    observacao      TEXT            NOT NULL,
    CONSTRAINT pk_obs_meta_arrecadacao   PRIMARY KEY (mes, exercicio)
);
GRANT ALL ON tcemg.obs_meta_arrecadacao TO urbem;


----------------
-- Ticket #20578
----------------

CREATE SCHEMA tceal;

CREATE TABLE tceal.tipo_pagamento(
    cod_lote        INTEGER         NOT NULL,
    cod_entidade    INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    tipo            CHAR(1)         NOT NULL,
    tipo_pagamento  CHAR(15)        NOT NULL,
    descricao       CHAR(10)        NOT NULL,
    CONSTRAINT pk_tipo_pagamento    PRIMARY KEY                         (cod_lote, cod_entidade, exercicio, tipo),
    CONSTRAINT fk_tipo_pagamento_1  FOREIGN KEY                         (cod_lote, cod_entidade, exercicio, tipo)
                                    REFERENCES tesouraria.transferencia (cod_lote, cod_entidade, exercicio, tipo)
);
GRANT ALL ON tceal.tipo_pagamento TO urbem;


----------------
-- Ticket #20577
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
     ( 62
     , 0 
     , 'TCE - AL'
     , 'TCEAL/'
     , 96
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
     ( 481
     , 62
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
     ( 2903
     , 481
     , 'FLExportacaoExecucao.php'
     , 'exportar'
     , 1
     , ''
     , 'Arquivos Execução'
     , TRUE
     );


----------------
-- Ticket #20549
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
     ( 2909
     , 481
     , 'FLExportacaoRelacionais.php'
     , 'exportar'
     , 2
     , ''
     , 'Arquivos Relacionais'
     , TRUE
     );


----------------
-- Ticket #20811
----------------

ALTER TABLE tcepb.tipo_origem_recurso DROP CONSTRAINT fk_recurso_1;
ALTER TABLE tcepb.recurso             ADD  CONSTRAINT fk_recurso_2 FOREIGN KEY (cod_tipo, exercicio)
                                                                   REFERENCES tcepb.tipo_origem_recurso(cod_tipo, exercicio);

   INSERT
     INTO tcepb.tipo_origem_recurso
        ( exercicio
        , cod_tipo
        , descricao
        )
   SELECT '2010'
        , cod_tipo
        , descricao
     FROM tcepb.tipo_origem_recurso AS proximo
    WHERE exercicio='2009'
      AND NOT EXISTS (
                       SELECT 1
                         FROM tcepb.tipo_origem_recurso
                        WHERE exercicio  = '2010'
                          and cod_tipo = proximo.cod_tipo
                     )
        ;

   INSERT
     INTO tcepb.tipo_origem_recurso
        ( exercicio
        , cod_tipo
        , descricao
        )
   SELECT '2011'
        , cod_tipo
        , descricao
     FROM tcepb.tipo_origem_recurso AS proximo
    WHERE exercicio='2010'
      AND NOT EXISTS (
                       SELECT 1
                         FROM tcepb.tipo_origem_recurso
                        WHERE exercicio  = '2011'
                          and cod_tipo = proximo.cod_tipo
                     )
        ;

   INSERT
     INTO tcepb.tipo_origem_recurso
        ( exercicio
        , cod_tipo
        , descricao
        )
   SELECT '2012'
        , cod_tipo
        , descricao
     FROM tcepb.tipo_origem_recurso AS proximo
    WHERE exercicio='2011'
      AND NOT EXISTS (
                       SELECT 1
                         FROM tcepb.tipo_origem_recurso
                        WHERE exercicio  = '2012'
                          and cod_tipo = proximo.cod_tipo
                     )
        ;

   INSERT
     INTO tcepb.tipo_origem_recurso
        ( exercicio
        , cod_tipo
        , descricao
        )
   SELECT '2013'
        , cod_tipo
        , descricao
     FROM tcepb.tipo_origem_recurso AS proximo
    WHERE exercicio='2012'
      AND NOT EXISTS (
                       SELECT 1
                         FROM tcepb.tipo_origem_recurso
                        WHERE exercicio  = '2013'
                          and cod_tipo = proximo.cod_tipo
                     )
        ;

   INSERT
     INTO tcepb.tipo_origem_recurso
        ( exercicio
        , cod_tipo
        , descricao
        )
   SELECT '2014'
        , cod_tipo
        , descricao
     FROM tcepb.tipo_origem_recurso AS proximo
    WHERE exercicio='2013'
      AND NOT EXISTS (
                       SELECT 1
                         FROM tcepb.tipo_origem_recurso
                        WHERE exercicio  = '2014'
                          and cod_tipo = proximo.cod_tipo
                     )
        ;


----------------
-- Ticket #20956
----------------

INSERT
  INTO administracao.funcionalidade
     ( cod_funcionalidade
     , cod_modulo
     , nom_funcionalidade
     , nom_diretorio
     , ordem )
VALUES ( 482
     , 55
     , 'Exportação SICOM'
     , 'instancias/exportacao/'
     , 3
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES ( 2911
     , 482
     , 'FLExportarArquivosPlanejamento.php'
     , 'exportar_planej'
     , 1
     , ''
     , 'Arquivos Planejamento'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES ( 2927
     , 482
     , 'FLExportarInclusaoProgramas.php'
     , 'exportar_prog'
     , 2
     , ''
     , 'Inclusão de Programas'
     );

INSERT
  INTO administracao.acao
     ( cod_acao
     , cod_funcionalidade
     , nom_arquivo
     , parametro
     , ordem
     , complemento_acao
     , nom_acao )
VALUES ( 2928
     , 482
     , 'FLExportarAcompanhamentoMensal.php'
     , 'exportar_mensal'
     , 3
     , ''
     , 'Acompanhamento Mensal'
     );

----------------
-- Ticket #21298
----------------

CREATE TABLE tcemg.tipo_veiculo_tce (
    cod_tipo_tce        INTEGER         NOT NULL,
    nom_tipo_tce        VARCHAR(200)    NOT NULL,
    CONSTRAINT pk_tipo_veiculo_tce      PRIMARY KEY     (cod_tipo_tce)
);

GRANT ALL ON tcemg.tipo_veiculo_tce TO GROUP urbem;

INSERT INTO tcemg.tipo_veiculo_tce(cod_tipo_tce, nom_tipo_tce) VALUES (01, 'Aeronaves'   );
INSERT INTO tcemg.tipo_veiculo_tce(cod_tipo_tce, nom_tipo_tce) VALUES (02, 'Embarcações' );
INSERT INTO tcemg.tipo_veiculo_tce(cod_tipo_tce, nom_tipo_tce) VALUES (03, 'Veículos'    );
INSERT INTO tcemg.tipo_veiculo_tce(cod_tipo_tce, nom_tipo_tce) VALUES (04, 'Maquinário'  );
INSERT INTO tcemg.tipo_veiculo_tce(cod_tipo_tce, nom_tipo_tce) VALUES (05, 'Equipamentos');
INSERT INTO tcemg.tipo_veiculo_tce(cod_tipo_tce, nom_tipo_tce) VALUES (99, 'Outros'      );


CREATE TABLE tcemg.subtipo_veiculo_tce (
    cod_tipo_tce        INTEGER         NOT NULL,
    cod_subtipo_tce     INTEGER ,
    nom_subtipo_tce     VARCHAR(200),
    CONSTRAINT pk_subtipo_veiculo_tce   PRIMARY KEY                         (cod_subtipo_tce, cod_tipo_tce),
    CONSTRAINT fk_subtipo_veiculo_tce_1 FOREIGN KEY                         (cod_tipo_tce)
                                        REFERENCES tcemg.tipo_veiculo_tce   (cod_tipo_tce)
);

GRANT ALL ON tcemg.subtipo_veiculo_tce TO GROUP urbem;

INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (01, 01, 'Aeronaves'               );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (02, 02, 'Embarcações'             );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (03, 03, 'Veículo de Passeio'      );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (03, 04, 'Utilitário (Camionete..)');
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (03, 05, 'Ônibus'                  );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (03, 06, 'Caminhão'                );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (03, 07, 'Motocicleta'             );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (03, 08, 'Van'                     );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 09, 'Trator de Esteira'       );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 10, 'Trator de Pneu'          );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 11, 'Moto niveladora'         );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 12, 'Pá-Carregadeira'         );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 13, 'Retro Escavadeira'       );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 14, 'Mini Carregadeira'       );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 15, 'Escavadeira'             );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 16, 'Empilhadeira'            );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (04, 17, 'Compactador'             );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 18, 'Gerador'                 );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 19, 'Motobomba'               );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 20, 'Roçadeira'               );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 21, 'Motosserra'              );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 22, 'Pulverizador'            );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 23, 'Compactador de Mão'      );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 24, 'Oficina'                 );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (05, 25, 'Motor de Popa'           );
INSERT INTO tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce, nom_subtipo_tce) VALUES (99, 07, 'Outros'                  );

CREATE TABLE tcemg.tipo_veiculo_vinculo(
    cod_tipo_tce            INTEGER         NOT NULL,
    cod_subtipo_tce         INTEGER         NOT NULL,
    cod_tipo                INTEGER         NOT NULL,
    CONSTRAINT pk_tipo_veiculo_vinculo      PRIMARY KEY                         (cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_1    FOREIGN KEY                         (cod_tipo)
                                            REFERENCES frota.tipo_veiculo       (cod_tipo),
    CONSTRAINT fk_tipo_veiculo_vinculo_2    FOREIGN KEY                         (cod_tipo_tce)
                                            REFERENCES tcemg.tipo_veiculo_tce   (cod_tipo_tce),
    CONSTRAINT fk_tipo_veiculo_vinculo_3    FOREIGN KEY                         (cod_tipo_tce, cod_subtipo_tce)
                                            REFERENCES tcemg.subtipo_veiculo_tce(cod_tipo_tce, cod_subtipo_tce)
);
GRANT ALL ON tcemg.subtipo_veiculo_tce TO GROUP urbem;

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
     ( 2929
     , 451
     , 'FMManterVinculoTipoVeiculo.php'
     , 'manter'
     , 4
     , ''
     , 'Configurar Tipo Veículo'
     , TRUE
     );


----------------
-- Ticket #21965
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
     ( 2913
     , 451
     , 'FMManterConfiguracaoIDE.php'
     , 'manter'
     , 1
     , ''
     , 'Configurar IDE'
     , TRUE
     );

CREATE TABLE tcemg.configurar_ide (
    cod_municipio           INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    opcao_semestralidade    INTEGER             ,
    CONSTRAINT pk_configurar_ide        PRIMARY KEY (exercicio)
);
GRANT ALL ON tcemg.configurar_ide TO urbem;


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
     ( 2914
     , 451
     , 'FMManterConfiguracaoOrgao.php'
     , 'manter'
     , 1
     , ''
     , 'Configurar Orgão'
     , TRUE
     );
     
CREATE TABLE tcemg.orgao (
    num_orgao   INTEGER         NOT NULL,
    exercicio   CHAR(4)         NOT NULL,
    nom_orgao   VARCHAR         NOT NULL,
    CONSTRAINT pk_tcemg_orgao   PRIMARY KEY (exercicio, num_orgao)
);
GRANT ALL ON tcemg.orgao TO urbem;

INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (1,'2014','01 – Câmara Municipal'                                   );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (2,'2014','02 – Prefeitura Municipal'                               );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (3,'2014','03 – Autarquia (exceto RPPS)'                            );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (4,'2014','04 – Fundação'                                           );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (5,'2014','05 – RPPS (Regime Próprio de Previdência Social)'        );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (6,'2014','06 – RPPS – Assistência à Saúde'                         );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (8,'2014','08 – Empresa Pública (apenas as dependentes)'            );
INSERT INTO tcemg.orgao (num_orgao, exercicio, nom_orgao) VALUES (9,'2014','09 – Sociedade de Economia Mista (apenas as dependentes)');


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
     ( 2915
     , 451
     , 'FMManterConfiguracaoLeisPPA.php'
     , ''
     , 1
     , ''
     , 'Configurar Leis do PPA'
     , TRUE
     );

CREATE TABLE tcemg.configuracao_leis_ppa (
    exercicio                   CHAR(4)         NOT NULL,
    cod_norma                   INTEGER         NOT NULL,
    tipo_configuracao           VARCHAR         NOT NULL,
    status                      BOOLEAN         NOT NULL,
    CONSTRAINT pk_tcemg_configuracao_leis_ppa   PRIMARY KEY             (exercicio, cod_norma, tipo_configuracao),
    CONSTRAINT fk_tcemg_configuracao_leis_ppa_1 FOREIGN KEY             (cod_norma)
                                                REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcemg.configuracao_leis_ppa TO urbem;


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
     ( 2916
     , 451
     , 'FMManterConfiguracaoLOA.php'
     , ''
     , 1
     , ''
     , 'Configurar LOA'
     , TRUE
     );

CREATE TABLE tcemg.configuracao_loa (
    exercicio                               CHAR(4)     NOT NULL,
    cod_norma                               INTEGER     NOT NULL,
    percentual_abertura_credito             NUMERIC(14,2)       ,
    percentual_contratacao_credito          NUMERIC(14,2)       ,
    percentual_contratacao_credito_receita  NUMERIC(14,2)       ,
    CONSTRAINT pk_tcemg_configuracao_loa                PRIMARY KEY             (exercicio),
    CONSTRAINT fk_tcemg_configuracao_loa_1              FOREIGN KEY             (cod_norma)
                                                        REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcemg.configuracao_loa TO urbem;


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
     ( 2917
     , 451
     , 'FMManterConfiguracaoLeisLDO.php'
     , ''
     , 1
     , ''
     , 'Configurar LDO'
     , TRUE
     );

CREATE TABLE tcemg.configuracao_leis_ldo (
    exercicio                   CHAR(4)         NOT NULL,
    cod_norma                   INTEGER         NOT NULL,
    tipo_configuracao           VARCHAR         NOT NULL, -- consulta | alteracao
    status                      BOOLEAN         NOT NULL, -- true | false
    CONSTRAINT pk_tcemg_configuracao_leis_ldo   PRIMARY KEY             (exercicio, cod_norma, tipo_configuracao),
    CONSTRAINT fk_tcemg_configuracao_leis_ldo_1 FOREIGN KEY             (cod_norma)
                                                REFERENCES normas.norma (cod_norma)
);
GRANT ALL ON tcemg.configuracao_leis_ldo TO urbem;


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
     ( 2918
     , 451
     , 'FMManterConfiguracaoUnidadeOrcamentaria.php'
     , ''
     , 1
     , ''
     , 'Configurar Unidade Orçamentária'
     , TRUE
     );

CREATE TABLE tcemg.uniorcam (
    exercicio       CHAR(4)         NOT NULL,
    num_unidade     INTEGER         NOT NULL,
    num_orgao       INTEGER         NOT NULL,
    identificador   INTEGER         NOT NULL,
    CONSTRAINT pk_tcemg_uniorcam    PRIMARY KEY (exercicio, num_unidade, num_orgao)
);
GRANT ALL ON tcemg.uniorcam TO urbem;


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
     ( 2919
     , 451
     , 'FLManterConfiguracaoIdentificadorDeducao.php'
     , ''
     , 1
     , ''
     , 'Configurar Identificador de Dedução'
     , TRUE
     );

CREATE TABLE tcemg.valores_identificadores (
    descricao               VARCHAR             NOT NULL,
    cod_identificador       INTEGER             NOT NULL,
    CONSTRAINT pk_tcemg_valores_identificadores PRIMARY KEY (cod_identificador)
);
GRANT ALL ON tcemg.valores_identificadores TO urbem;

INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (91, '91 – Renúncia'            );
INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (92, '92 – Restituições'        );
INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (93, '93 – Descontos concedidos');
INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (95, '95 – FUNDEB'              );
INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (96, '96 – Compensações'        );
INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (98, '98 – Retificações'        );
INSERT INTO tcemg.valores_identificadores (cod_identificador,descricao) VALUES (99, '99 – Outras Deduções'     );

CREATE TABLE tcemg.receita_indentificadores_peculiar_receita (
    cod_receita         INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_identificador   INTEGER     NOT NULL,
    CONSTRAINT pk_tcemg_receita_indentificadores_peculiar_receita   PRIMARY KEY                             (cod_receita, exercicio),
    CONSTRAINT fk_tcemg_receita_indentificadores_peculiar_receita_1 FOREIGN KEY                             (exercicio, cod_receita)
                                                                    REFERENCES orcamento.receita            (exercicio, cod_receita),
    CONSTRAINT fk_tcemg_receita_indentificadores_peculiar_receita_2 FOREIGN KEY                             (cod_identificador)
                                                                    REFERENCES tcemg.valores_identificadores(cod_identificador)
);
GRANT ALL ON tcemg.receita_indentificadores_peculiar_receita TO urbem;


----------------
-- Ticket #21277
----------------

CREATE SEQUENCE tcemg.seqLiquidacao;
CREATE SEQUENCE tcemg.seqLiquidacaoAnulacao;

CREATE TABLE tcemg.balancete_extmmaa(
    cod_plano               INTEGER     NOT NULL,
    exercicio               CHAR(4)     NOT NULL,
    categoria               INTEGER             ,
    tipo_lancamento         INTEGER             ,
    sub_tipo_lancamento     INTEGER             ,
    CONSTRAINT pk_balancete_extmmaa     PRIMARY KEY                             (cod_plano, exercicio),
    CONSTRAINT fk_balancete_extmmaa_1   FOREIGN KEY                             (exercicio, cod_plano)
                                        REFERENCES contabilidade.plano_analitica(exercicio, cod_plano)
);
GRANT ALL ON tcemg.balancete_extmmaa TO urbem;


----------------
-- Ticket #20965
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
     ( 2923
     , 451
     , 'FMManterConfiguracaoPERC.php'
     , 'incluir'
     , 1
     , ''
     , 'Configurar PERC'
     , TRUE
     );

CREATE TABLE tcemg.configuracao_perc (
    exercicio               char(4)         NOT NULL,
    planejamento_anual      INTEGER         NOT NULL,
    porcentual_anual        NUMERIC(14,2)           ,
    CONSTRAINT pk_tcemg_configuracao_perc   PRIMARY KEY (exercicio)
);
GRANT ALL ON tcemg.configuracao_perc TO urbem;

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
     ( 2930
     , 451
     , 'FLManterConfiguracaoContaBancaria.php'
     , 'manter'
     , 16
     , ''
     , 'Configurar Conta Bancária'
     , TRUE
     );

CREATE TABLE tcemg.tipo_aplicacao(
    cod_tipo_aplicacao  INTEGER     NOT NULL,
    descricao           VARCHAR(70) NOT NULL,
    CONSTRAINT pk_tipo_aplicacao    PRIMARY KEY (cod_tipo_aplicacao)
);
GRANT ALL ON tcemg.tipo_aplicacao TO urbem;

INSERT INTO tcemg.tipo_aplicacao VALUES ( 1, 'Títulos do Tesouro Nacional – SELIC – Art. 7°, I, “a”'   );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 2, 'FI 100% títulos TN – Art. 7°, I, “b”'                    );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 3, 'Operações Compromissadas - Art. 7°, II'                  );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 4, 'FI Renda Fixa / Referenciado RF – Art. 7°, III'          );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 5, 'FI de renda fixa - Art. 7°, IV'                          );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 6, 'Poupança – Art. 7°, V'                                   );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 7, 'FI em direitos creditórios – aberto – Art. 7°, VI'       );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 8, 'FI em direitos creditórios – fechado – Art. 7°, VII “a”' );
INSERT INTO tcemg.tipo_aplicacao VALUES ( 9, 'FI renda fixa “Crédito Privado” – Art. 7°, VII, “b”'     );
INSERT INTO tcemg.tipo_aplicacao VALUES (10, 'FI Previdenciário em Ações – Art. 8°, I'                 );
INSERT INTO tcemg.tipo_aplicacao VALUES (11, 'FI de índice referenciado em Ações – Art. 8°, II'        );
INSERT INTO tcemg.tipo_aplicacao VALUES (12, 'FI em Ações - – Art. 8°, III'                            );
INSERT INTO tcemg.tipo_aplicacao VALUES (13, 'FI Multimercado aberto – Art. 8°, IV'                    );
INSERT INTO tcemg.tipo_aplicacao VALUES (14, 'FI em participações fechado – Art. 8° V'                 );
INSERT INTO tcemg.tipo_aplicacao VALUES (15, 'FI Imobiliário – cotas negociadas em bolsa – Art. 8°, VI');


CREATE TABLE tcemg.conta_bancaria(
    cod_conta           INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_entidade        INTEGER     NOT NULL,
    sequencia           INTEGER     NOT NULL,
    cod_tipo_aplicacao  INTEGER     NOT NULL,
    CONSTRAINT pk_conta_bancaria    PRIMARY KEY                         (cod_conta, exercicio, cod_tipo_aplicacao),
    CONSTRAINT fk_conta_bancaria_1  FOREIGN KEY                         (cod_conta, exercicio)
                                    REFERENCES contabilidade.plano_conta(cod_conta, exercicio),
    CONSTRAINT fk_conta_bancaria_2  FOREIGN KEY                         (exercicio, cod_entidade)
                                    REFERENCES orcamento.entidade       (exercicio, cod_entidade),
    CONSTRAINT fk_conta_bancaria_3  FOREIGN KEY                         (cod_tipo_aplicacao)
                                    REFERENCES tcemg.tipo_aplicacao     (cod_tipo_aplicacao)

);
GRANT ALL ON tcemg.conta_bancaria TO urbem;

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
     ( 2934
     , 451
     , 'FLManterConfiguracaoConvenioConta.php'
     , 'manter'
     , 17
     , ''
     , 'Configurar Convênio Conta Bancária'
     , TRUE
     );

CREATE TABLE tcemg.convenio_plano_banco(
    cod_plano           INTEGER             NOT NULL,
    cod_entidade        INTEGER             NOT NULL,
    exercicio           CHAR(4)             NOT NULL,
    num_convenio        NUMERIC(30)         NOT NULL,
    dt_assinatura       DATE                NOT NULL,
    CONSTRAINT pk_convenio_plano_banco      PRIMARY KEY                             (cod_plano, exercicio),
    CONSTRAINT fk_convenio_plano_banco_1    FOREIGN KEY                             (cod_plano, exercicio)
                                            REFERENCES contabilidade.plano_analitica(cod_plano, exercicio),
    CONSTRAINT fk_convenio_plano_banco_2    FOREIGN KEY                             (cod_entidade, exercicio)
                                            REFERENCES orcamento.entidade           (cod_entidade, exercicio)
);
GRANT ALL ON tcemg.convenio_plano_banco TO urbem;


----------------
-- Ticket #21297
----------------

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2924
          , 451
          , 'FMManterNotasFiscais.php'
          , 'incluir'
          , 10
          , 'Unidade Gestora'
          , 'Incluir Notas Fiscais'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2925
          , 451
          , 'FLManterNotasFiscais.php'
          , 'alterar'
          , 11
          , 'Unidade Gestora'
          , 'Alterar Notas Fiscais'
          );

INSERT INTO administracao.acao
          ( cod_acao
          , cod_funcionalidade
          , nom_arquivo
          , parametro
          , ordem
          , complemento_acao
          , nom_acao )
     VALUES ( 2926
          , 451
          , 'FLManterNotasFiscais.php'
          , 'excluir'
          , 12
          , 'Unidade Gestora'
          , 'Excluir Notas Fiscais'
          );



----------------
-- Ticket #21297
----------------

CREATE TABLE tcemg.tipo_nota_fiscal (
    cod_tipo        INTEGER              NOT NULL,
    descricao       VARCHAR(60)          NOT NULL,
    CONSTRAINT pk_tcemg_tipo_nota_fiscal PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tcemg.tipo_nota_fiscal TO urbem;

INSERT INTO tcemg.tipo_nota_fiscal (cod_tipo, descricao) VALUES (1, 'Nota Fiscal Eletrônica - Padrão Estadual'    );
INSERT INTO tcemg.tipo_nota_fiscal (cod_tipo, descricao) VALUES (2, 'Nota Fiscal Eletrônica - Padrão Municipal'   );
INSERT INTO tcemg.tipo_nota_fiscal (cod_tipo, descricao) VALUES (3, 'Nota Fiscal'                                 );
INSERT INTO tcemg.tipo_nota_fiscal (cod_tipo, descricao) VALUES (4, 'Nota Fiscal Eletrônica - Padrão SINIEF 07/05');


CREATE TABLE tcemg.nota_fiscal (
    cod_nota                    INTEGER     NOT NULL,
    cod_tipo                    INTEGER     NOT NULL,
    exercicio                   CHAR(4)     NOT NULL,
    cod_entidade                INTEGER     NOT NULL,
    nro_nota                    VARCHAR(20)         ,
    nro_serie                   VARCHAR(8)          ,
    aidf                        VARCHAR(15)         ,
    data_emissao                date        NOT NULL,
    inscricao_municipal         INTEGER             ,
    inscricao_estadual          INTEGER             ,
    chave_acesso                numeric(44,0)       ,
    chave_acesso_municipal      VARCHAR(60)         ,
    CONSTRAINT pk_tcemg_nota_fiscal         PRIMARY KEY                         (cod_nota, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_tipo_nota_fiscal_1  FOREIGN KEY                         (cod_tipo)
                                            REFERENCES tcemg.tipo_nota_fiscal   (cod_tipo),
    CONSTRAINT fk_tcemg_tipo_nota_fiscal_2  FOREIGN KEY                         (cod_entidade, exercicio)
                                            REFERENCES orcamento.entidade       (cod_entidade, exercicio)
);
GRANT ALL ON tcemg.tipo_nota_fiscal TO urbem;


CREATE TABLE tcemg.nota_fiscal_empenho_liquidacao
(
    cod_nota                INTEGER                      NOT NULL,
    exercicio               VARCHAR(4)                   NOT NULL,
    cod_entidade            INTEGER                      NOT NULL,
    cod_empenho             INTEGER                      NOT NULL,
    exercicio_empenho       VARCHAR(4)                   NOT NULL,
    cod_nota_liquidacao     INTEGER                      NOT NULL,
    exercicio_liquidacao    VARCHAR(4)                    NOT NULL,
    vl_liquidacao           NUMERIC(14,2)                NOT NULL,
    vl_associado            NUMERIC(14,2)                NOT NULL,
    vl_total_liquido        NUMERIC(14,2)                NOT NULL,
    CONSTRAINT pk_tcemg_nota_fiscal_empenho_liquidacao   PRIMARY KEY                        (cod_nota , exercicio , cod_entidade , cod_empenho , cod_nota_liquidacao , exercicio_liquidacao, exercicio_empenho),
    CONSTRAINT fk_tcemg_nota_fiscal_empenho_liquidacao_1 FOREIGN KEY                        (cod_nota, exercicio, cod_entidade)
                                                         REFERENCES tcemg.nota_fiscal       (cod_nota, exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_nota_fiscal_empenho_liquidacao_2 FOREIGN KEY                        (cod_empenho, exercicio_empenho, cod_entidade)
                                                         REFERENCES empenho.empenho         (cod_empenho,exercicio, cod_entidade),
    CONSTRAINT fk_tcemg_nota_fiscal_empenho_liquidacao_3 FOREIGN KEY                        (exercicio_liquidacao, cod_entidade, cod_nota_liquidacao)
                                                         REFERENCES empenho.nota_liquidacao (exercicio , cod_entidade , cod_nota)
);
GRANT ALL ON tcemg.tipo_nota_fiscal TO urbem;


----------------
-- Ticket #21151
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
  ( 2912
  , 482
  , 'FLExportarAcompanhamentoMensal.php'
  , 'exportar_mensal'
  , 2
  , ''
  , 'Acompanhamento Mensal'
  , TRUE
  );


----------------
-- Ticket #21277
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
  ( 2932
  , 451
  , 'FMManterEXT.php'
  , 'configurar'
  , 1
  , 'Configuração das Despesas e Receitas Extra-orcamentárias'
  , 'Configurar Rec/Desp Extra'
  , TRUE
  );


----------------
-- Ticket #20983
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
     ( 2933
     , 451
     , 'FLManterConfiguracaoMetasFiscais.php'
     , 'manter'
     , 1
     , 'MTFIS - Detalhamento das Metas Fiscais'
     , 'Configurar Metas Fiscais'
     , TRUE
     );

CREATE TABLE tcemg.metas_fiscais (
    exercicio                                   char(4) NOT NULL,
    valor_corrente_receita_total                NUMERIC(14,2)   ,
    valor_corrente_receita_primaria             NUMERIC(14,2)   ,
    valor_corrente_despesa_total                NUMERIC(14,2)   ,
    valor_corrente_despesa_primaria             NUMERIC(14,2)   ,
    valor_corrente_resultado_primario           NUMERIC(14,2)   ,
    valor_corrente_resultado_nominal            NUMERIC(14,2)   ,
    valor_corrente_divida_publica_consolidada   NUMERIC(14,2)   ,
    valor_corrente_divida_consolidada_liquida   NUMERIC(14,2)   ,
    valor_constante_receita_total               NUMERIC(14,2)   ,
    valor_constante_receita_primaria            NUMERIC(14,2)   ,
    valor_constante_despesa_total               NUMERIC(14,2)   ,
    valor_constante_despesa_primaria            NUMERIC(14,2)   ,
    valor_constante_resultado_primario          NUMERIC(14,2)   ,
    valor_constante_resultado_nominal           NUMERIC(14,2)   ,
    valor_constante_divida_publica_consolidada  NUMERIC(14,2)   ,
    valor_constante_divida_consolidada_liquida  NUMERIC(14,2)   ,
    percentual_pib_receita_total                NUMERIC(7,3)    ,
    percentual_pib_receita_primaria             NUMERIC(7,3)    ,
    percentual_pib_despesa_total                NUMERIC(7,3)    ,
    percentual_pib_despesa_primaria             NUMERIC(7,3)    ,
    percentual_pib_resultado_primario           NUMERIC(7,3)    ,
    percentual_pib_resultado_nominal            NUMERIC(7,3)    ,
    percentual_pib_divida_publica_consolidada   NUMERIC(7,3)    ,
    percentual_pib_divida_consolidada_liquida   NUMERIC(7,3)    ,
    CONSTRAINT pk_tcemg_metas_fiscais PRIMARY KEY (exercicio)
);
GRANT ALL ON tcemg.metas_fiscais TO urbem;


----------------
-- Ticket #21456
----------------

CREATE TABLE stn.identificador_risco_fiscal (
    cod_identificador   INTEGER         NOT NULL,
    descricao           VARCHAR(40)     NOT NULL,
    CONSTRAINT pk_identificador_risco_fiscal    PRIMARY KEY (cod_identificador)
);
GRANT ALL ON stn.identificador_risco_fiscal TO urbem;

INSERT INTO stn.identificador_risco_fiscal VALUES (01, 'Demandas Judiciais'                   );
INSERT INTO stn.identificador_risco_fiscal VALUES (02, 'Dívidas em Processo de Reconhecimento');
INSERT INTO stn.identificador_risco_fiscal VALUES (03, 'Avais e Garantias Concedidas'         );
INSERT INTO stn.identificador_risco_fiscal VALUES (04, 'Assunção de Passivos'                 );
INSERT INTO stn.identificador_risco_fiscal VALUES (05, 'Assistências Diversas'                );
INSERT INTO stn.identificador_risco_fiscal VALUES (06, 'Outros Passivos Contingentes'         );
INSERT INTO stn.identificador_risco_fiscal VALUES (07, 'Frustração de Arrecadação'            );
INSERT INTO stn.identificador_risco_fiscal VALUES (08, 'Restituição de Tributos a Maior'      );
INSERT INTO stn.identificador_risco_fiscal VALUES (09, 'Discrepância de Projeções'            );
INSERT INTO stn.identificador_risco_fiscal VALUES (10, 'Outros Riscos Fiscais'                );

ALTER TABLE stn.riscos_fiscais ADD COLUMN     cod_identificador INTEGER;
ALTER TABLE stn.riscos_fiscais ADD CONSTRAINT fk_riscos_fiscais_2 FOREIGN KEY (cod_identificador)
                                                                  REFERENCES stn.identificador_risco_fiscal (cod_identificador);


----------------
-- Ticket #21453
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
     ( 2935
     , 451
     , 'FMManterConsideracao.php'
     , 'manter'
     , 13
     , ''
     , 'Configurar Considerações'
     , TRUE
     );

CREATE TABLE tcemg.consideracao_arquivo(
    cod_arquivo     INTEGER             NOT NULL,
    nom_arquivo     VARCHAR(15)         NOT NULL,
    descricao       VARCHAR(3000)               ,
    CONSTRAINT pk_consideracao_arquivo  PRIMARY KEY (cod_arquivo)
);
GRANT ALL ON tcemg.consideracao_arquivo TO urbem;

INSERT INTO tcemg.consideracao_arquivo VALUES (01, 'IDE'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (02, 'PESSOA'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (03, 'ORGAO'    );
INSERT INTO tcemg.consideracao_arquivo VALUES (04, 'CONSOR'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (05, 'PAREC'    );
INSERT INTO tcemg.consideracao_arquivo VALUES (06, 'REC'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (07, 'ARC'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (08, 'LAO'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (09, 'AOC'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (10, 'ITEM'     );
INSERT INTO tcemg.consideracao_arquivo VALUES (11, 'REGLIC'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (12, 'ABERLIC'  );
INSERT INTO tcemg.consideracao_arquivo VALUES (13, 'RESPLIC'  );
INSERT INTO tcemg.consideracao_arquivo VALUES (14, 'HABLIC'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (15, 'JULGLIC'  );
INSERT INTO tcemg.consideracao_arquivo VALUES (16, 'HOMOLIC'  );
INSERT INTO tcemg.consideracao_arquivo VALUES (17, 'PARELIC'  );
INSERT INTO tcemg.consideracao_arquivo VALUES (18, 'REGADESAO');
INSERT INTO tcemg.consideracao_arquivo VALUES (19, 'DISPENSA' );
INSERT INTO tcemg.consideracao_arquivo VALUES (20, 'CONTRATOS');
INSERT INTO tcemg.consideracao_arquivo VALUES (21, 'CONV'     );
INSERT INTO tcemg.consideracao_arquivo VALUES (22, 'CTB'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (23, 'CAIXA'    );
INSERT INTO tcemg.consideracao_arquivo VALUES (24, 'EMP'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (25, 'ANL'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (26, 'RSP'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (27, 'LQD'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (28, 'ALQ'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (29, 'EXT'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (30, 'AEX'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (31, 'OPS'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (32, 'AOP'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (33, 'OBELAC'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (34, 'AOB'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (35, 'NTF'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (36, 'CVC'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (37, 'DDC'      );
INSERT INTO tcemg.consideracao_arquivo VALUES (38, 'PARPPS'   );
INSERT INTO tcemg.consideracao_arquivo VALUES (39, 'DCLRF'    );
INSERT INTO tcemg.consideracao_arquivo VALUES (40, 'CONSID'   );

