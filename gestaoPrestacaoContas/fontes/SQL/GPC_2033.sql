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
* Versao 2.03.3
*
* Fabio Bertoldi - 20141008
*
*/

----------------
-- Ticket #22325
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
     ( 3012
     , 487
     , 'FLManterDividaFundadaOperacaoCredito.php'
     , 'manter'
     , 15
     , ''
     , 'Dívida Fundada/Operacão de Crédito'
     , TRUE
     );

CREATE TABLE tcepe.divida_fundada_operacao_credito(
    exercicio                       CHAR(4)         NOT NULL,
    cod_entidade                    INTEGER         NOT NULL,
    tipo_operacao_credito           INTEGER         NOT NULL,
    cod_norma                       INTEGER         NOT NULL,
    num_contrato                    INTEGER         NOT NULL,
    dt_assinatura                   DATE                    ,
    vl_saldo_anterior_titulo        NUMERIC(14,2)           ,
    vl_inscricao_exercicio_titulo   NUMERIC(14,2)           ,
    vl_baixa_exercicio_titulo       NUMERIC(14,2)           ,
    vl_saldo_anterior_contrato      NUMERIC(14,2)           ,
    vl_inscricao_exercicio_contrato NUMERIC(14,2)           ,
    vl_baixa_exercicio_contrato     NUMERIC(14,2)           ,
    CONSTRAINT pk_divida_fundada_operacao_credito   PRIMARY KEY (exercicio, cod_entidade, tipo_operacao_credito
    , cod_norma, num_contrato),
    CONSTRAINT fk_divida_fundada_operacao_credito_1 FOREIGN KEY             (cod_norma)
                                                    REFERENCES normas.norma (cod_norma),
    CONSTRAINT fk_divida_fundada_operacao_credito_2 FOREIGN KEY             (exercicio, cod_entidade)
                                                    REFERENCES orcamento.entidade (exercicio, cod_entidade)
);
GRANT ALL ON tcepe.divida_fundada_operacao_credito TO urbem;


----------------
-- Ticket #20846
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
     ( 64
     , 0
     , 'TCE - TO'
     , 'TCETO/'
     , 99
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
     ( 489
     , 64
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
     ( 3013
     , 489
     , 'FMManterOrcamento.php'
     , 'configurar'
     , 1
     , ''
     , 'Configurar Orçamento'
     , TRUE
     );


INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_cod_norma'          , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_complementacao_loa' , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_credito_adicional'  , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_credito_antecipacao', '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_credito_interno'    , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_credito_externo'    , '');


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
     ( 3014
     , 489
     , 'FMManterParametrosGerais.php'
     , 'configuração'
     , 2
     , ''
     , 'Parâmetros Gerais'
     , TRUE
     );

INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_orgao_prefeitura'  , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_unidade_prefeitura', '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_orgao_camara'      , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_unidade_camara'    , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_orgao_rpps'        , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_unidade_rpps'      , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_orgao_outros'      , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_unidade_outros'    , '');


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
     ( 3018
     , 489
     , 'FMManterMetasFiscaisAnexo1.php'
     , 'configurar'
     , 3
     , ''
     , 'Configurar Metas Fiscais'
     , TRUE
     );

INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_metas_receitas_anuais'     , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_receitas_primarias'        , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_metas_despesas_anuais'     , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_despesas_primarias'        , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_resultado_primario'        , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_resultado_nominal'         , '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_divida_publica_consolidada', '');
INSERT INTO administracao.configuracao (exercicio, cod_modulo, parametro, valor) VALUES ('2014', 64, 'tceto_config_divida_consolidada_liquida', '');


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
     ( 3019
     , 489
     , 'FMManterConfiguracaoUnidadeOrcamentaria.php'
     , 'manter'
     , 4
     , ''
     , 'Configurar Unidade Orçamentária'
     , TRUE
     );

CREATE TABLE tceto.uniorcam (
    numcgm          INTEGER         NOT NULL,
    exercicio       CHAR(4)         NOT NULL,
    num_unidade     INTEGER         NOT NULL,
    num_orgao       INTEGER         NOT NULL,
    identificador   INTEGER         NOT NULL,
    CONSTRAINT pk_uniorcam          PRIMARY KEY                       (exercicio, num_unidade, num_orgao),
    CONSTRAINT fk_uniorcam_1        FOREIGN KEY                       (numcgm)
                                    REFERENCES sw_cgm_pessoa_juridica (numcgm)
);
GRANT ALL ON tceto.uniorcam TO urbem;


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
     ( 3020
     , 489
     , 'FLManterCredor.php'
     , ''
     , 5
     , ''
     , 'Configurar Credores'
     , TRUE
     );

CREATE TABLE tceto.tipo_credor(
    cod_tipo    INTEGER         NOT NULL,
    descricao   VARCHAR(70)     NOT NULL,
    CONSTRAINT pk_tipo_credor   PRIMARY KEY (cod_tipo)
);
GRANT ALL ON tceto.tipo_credor TO urbem;

INSERT INTO tceto.tipo_credor VALUES (1, 'Credores da administração publica Municipal'                 );
INSERT INTO tceto.tipo_credor VALUES (2, 'Credores que não pertencem a administração pública municipal');

CREATE TABLE tceto.credor(
    exercicio   CHAR(4)     NOT NULL,
    numcgm      INTEGER     NOT NULL,
    tipo        INTEGER     NOT NULL,
    CONSTRAINT pk_tceto_credor      PRIMARY KEY                 (exercicio, numcgm),
    CONSTRAINT fk_tceto_credor_1    FOREIGN KEY                 (numcgm)
                                    REFERENCES sw_cgm           (numcgm),
    CONSTRAINT fk_tceto_credor_2    FOREIGN KEY                 (tipo)
                                    REFERENCES tceto.tipo_credor(cod_tipo)
);
GRANT ALL ON tceto.credor TO urbem;


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
     ( 3021
     , 489
     , 'FMManterRecDespExtra.php'
     , 'rec_desp'
     , 6
     , ''
     , 'Configurar Receita/Despesa Extra'
     , TRUE
     );

CREATE TABLE tceto.classificacao_receita_despesa(
    cod_classificacao   INTEGER     NOT NULL,
    descricao           VARCHAR(30) NOT NULL,
    CONSTRAINT pk_classificacao_receita_despesa PRIMARY KEY (cod_classificacao)
);
GRANT ALL ON tceto.classificacao_receita_despesa TO urbem;

INSERT INTO tceto.classificacao_receita_despesa VALUES (1, 'Restos a Pagar'               );
INSERT INTO tceto.classificacao_receita_despesa VALUES (2, 'Serviços da Dívida'           );
INSERT INTO tceto.classificacao_receita_despesa VALUES (3, 'Depósitos'                    );
INSERT INTO tceto.classificacao_receita_despesa VALUES (4, 'Convênios'                    );
INSERT INTO tceto.classificacao_receita_despesa VALUES (5, 'Débitos da Tesouraria'        );
INSERT INTO tceto.classificacao_receita_despesa VALUES (6, 'Outras Operações (Realizável)');
INSERT INTO tceto.classificacao_receita_despesa VALUES (7, 'Interferências Financeiras'   );

CREATE TABLE tceto.plano_analitica_classificacao(
    exercicio           CHAR(4)         NOT NULL,
    cod_plano           INTEGER         NOT NULL,
    cod_classificacao   INTEGER         NOT NULL,
    CONSTRAINT pk_plano_analitica_classificacao     PRIMARY KEY                                   (exercicio, cod_plano),
    CONSTRAINT fk_plano_analitica_classificacao_1   FOREIGN KEY                                   (exercicio, cod_plano)
                                                    REFERENCES contabilidade.plano_analitica      (exercicio, cod_plano),
    CONSTRAINT fk_plano_analitica_classificacao_2   FOREIGN KEY                                   (cod_classificacao)
                                                    REFERENCES tceto.classificacao_receita_despesa(cod_classificacao)
);
GRANT ALL ON tceto.plano_analitica_classificacao TO urbem;


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
     ( 3022
     , 489
     , 'FLManterConfiguracaoIdentificadorDeducao.php'
     , ''
     , 7
     , ''
     , 'Configurar Identificador de Dedução'
     , TRUE
     );

CREATE TABLE tceto.valores_identificadores(
    cod_identificador       INTEGER         NOT NULL,
    descricao               VARCHAR         NOT NULL,
    CONSTRAINT pk_valores_identificadores   PRIMARY KEY (cod_identificador)
);
GRANT ALL ON tceto.valores_identificadores TO urbem;

INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (  0, '000 - Não se aplica'                             );
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (101, '101 - Renuncia da Receita'                       );
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (102, '102 - Restituição da Receita'                    );
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (103, '103 - Desconto Concedido'                        );
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (105, '105 - Dedução de Receita para formação do Fundeb');
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (106, '106 - Compensação'                               );
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (108, '108 - Retificações'                              );
INSERT INTO tceto.valores_identificadores (cod_identificador,descricao) VALUES (109, '109 - Outras Deduções'                           );

CREATE TABLE tceto.receita_indentificadores_peculiar_receita (
    cod_receita         INTEGER     NOT NULL,
    exercicio           CHAR(4)     NOT NULL,
    cod_identificador   INTEGER     NOT NULL,
    CONSTRAINT pk_tceto_receita_indentificadores_peculiar_receita   PRIMARY KEY                             (cod_receita, exercicio),
    CONSTRAINT fk_tceto_receita_indentificadores_peculiar_receita_1 FOREIGN KEY                             (cod_receita, exercicio)
                                                                    REFERENCES orcamento.receita            (cod_receita, exercicio),
    CONSTRAINT fk_tceto_receita_indentificadores_peculiar_receita_2 FOREIGN KEY                             (cod_identificador)
                                                                    REFERENCES tceto.valores_identificadores(cod_identificador)
);
GRANT ALL ON tceto.receita_indentificadores_peculiar_receita TO urbem;


CREATE TABLE tceto.alteracao_lei_ppa(
    cod_norma       INTEGER     NOT NULL,
    data_alteracao  DATE        NOT NULL,
    timestamp       TIMESTAMP   NOT NULL DEFAULT ('now'::text)::timestamp(3) with time zone,
    CONSTRAINT pk_alteracao_lei_ppa     PRIMARY KEY            (cod_norma, data_alteracao, timestamp),
    CONSTRAINT fk_alteracao_lei_ppa_1   FOREIGN KEY            (cod_norma)
                                        REFERENCES normas.norma(cod_norma)
);
GRANT ALL ON tceto.alteracao_lei_ppa TO urbem;


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
     ( 3024
     , 489
     , 'FMManterConfiguracaoUnidadeGestora.php'
     , 'manter'
     , 8
     , ''
     , 'Configurar Unidade Gestora'
     , TRUE
     );

CREATE OR REPLACE FUNCTION manutencao() RETURNS VOID AS $$
DECLARE
    stSQL       VARCHAR;
    reRecord    RECORD;
BEGIN
    INSERT
      INTO administracao.configuracao
         ( exercicio
         , cod_modulo
         , parametro
         , valor
         )
    VALUES
         ( '2014'
         , 64
         , 'tceto_configuracao_unidade_gestora'
         , ''
         );

    stSQL := '
                 SELECT cod_entidade
                   FROM orcamento.entidade
               GROUP BY cod_entidade
                      ;
             ';
    FOR reRecord IN EXECUTE stSQL LOOP
        INSERT
          INTO administracao.configuracao_entidade
             ( exercicio
             , cod_entidade
             , cod_modulo
             , parametro
             , valor
             )
        SELECT '2014'
             , reRecord.cod_entidade
             , 64
             , 'tceto_configuracao_unidade_gestora_'|| reRecord.cod_entidade
             , ''
         WHERE 0 = (
                     SELECT COUNT(1)
                       FROM administracao.configuracao_entidade
                      WHERE exercicio    = '2014'
                        AND cod_entidade = reRecord.cod_entidade
                        AND cod_modulo   = 64
                        AND parametro    = 'tceto_configuracao_unidade_gestora_'|| reRecord.cod_entidade
                   )
             ;
    END LOOP;
END;
$$ LANGUAGE 'plpgsql';

SELECT        manutencao();
DROP FUNCTION manutencao();



----------------
-- Ticket #22376
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
     ( 490
     , 64
     , 'SICAP'
     , 'instancias/SICAP/'
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
     ( 3015
     , 490
     , 'FLExportarArquivosExecucao.php'
     , 'exportar'
     , 1
     , 'Arquivos Execução'
     , 'Arquivos Execução'
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
     ( 3016
     , 490
     , 'FLExportarArquivosRelacionais.php'
     , 'exportar'
     , 2
     , 'Arquivos Relacionais'
     , 'Arquivos Relacionais'
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
     ( 3017
     , 490
     , 'FLExportarArquivosOrcamento.php'
     , 'exportar'
     , 3
     , 'Arquivos Orçamento'
     , 'Arquivos Orçamento'
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
     ( 3023
     , 490
     , 'FLExportarArquivosPessoal.php'
     , 'exportar'
     , 4
     , 'Arquivos Pessoal'
     , 'Arquivos Pessoal'
     , TRUE
     );


----------------
-- Ticket #22326
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
     ( 3025
     , 487
     , 'FLManterDividaFundadaOutraOperacaoCredito.php'
     , 'manter'
     , 16
     , ''
     , 'Dívida Fundada/Outra Operacão de Crédito'
     , TRUE
     );

CREATE TABLE tcepe.divida_fundada_outra_operacao_credito(
    exercicio               CHAR(4)         NOT NULL,
    cod_entidade            INTEGER         NOT NULL,
    cod_norma               INTEGER         NOT NULL,
    num_contrato            INTEGER         NOT NULL,
    dt_assinatura           DATE                    ,
    cgm_credor              INTEGER         NOT NULL,
    vl_saldo_anterior       NUMERIC(14,2)           ,
    vl_inscricao_exercicio  NUMERIC(14,2)           ,
    vl_baixa_exercicio      NUMERIC(14,2)           ,
    CONSTRAINT pk_divida_fundada_outra_operacao_credito_1 PRIMARY KEY (exercicio, cod_entidade, cod_norma, num_contrato),
    CONSTRAINT fk_divida_fundada_outra_operacao_credito_1 FOREIGN KEY (cod_norma)
                                                          REFERENCES normas.norma (cod_norma),
    CONSTRAINT fk_divida_fundada_outra_operacao_credito_2 FOREIGN KEY (exercicio, cod_entidade)
                                                          REFERENCES orcamento.entidade (exercicio, cod_entidade),
    CONSTRAINT fk_divida_fundada_outra_operacao_credito_3 FOREIGN KEY (cgm_credor)
                                                          REFERENCES sw_cgm (numcgm)
);
GRANT ALL ON tcepe.divida_fundada_outra_operacao_credito TO urbem;


----------------
-- Ticket #22282
----------------

UPDATE tcepe.tipo_responsavel
   SET descricao = 'Técnico de Contabilidade'
 WHERE cod_tipo = 2
     ;

